# -*- coding: utf-8 -*-
"""Граф связей статей: какие статьи кодексов ссылаются друг на друга.

Модуль не ходит ни в модель, ни в сеть: принимает фрагменты RAG-индекса и
строит ориентированный граф «статья -> статья, на которую она ссылается».
Связь принимается, только если статья-адресат действительно есть в базе: так
«статья 5 Закона о…» (закона в базе нет) и опечатки шумом не становятся.

Что отбрасывается при разборе текста статьи:
  * её собственный заголовок («Статья 943. Счета банков») и ссылки на саму себя;
  * примечания об изменениях в скобках («в редакции Закона РТ от …»);
  * ссылки на законы и указы: их текстов в базе нет;
  * ссылки на несуществующие статьи.
"""

from __future__ import annotations

import re
from collections import defaultdict
from typing import Callable, Optional

import chat_modes

SNIPPET_BEFORE = 90
SNIPPET_AFTER = 70
MAX_LIST_REFS = 8   # «статьи 5, 7, 9 и 11»: больше — почти наверняка перечень, а не ссылки

_HEADING_RE = re.compile(r"^\s*Статья\s+\d+(?:[-.]\d+)?\.[^\n]*\n?")
_NOTE_RE = re.compile(r"\([^()]*(?:редакци|Закон|Указ|Постановлени|утратил|исключен|дополнен|изменен)[^()]*\)", re.I)
_REF_RE = re.compile(
    r"стать[яеиюёй]+\s+(\d+(?:[-.]\d+)?)"
    r"((?:\s*(?:,|и|или)\s*\d+(?:[-.]\d+)?(?![\d%]))*)",
    re.I,
)
_NUM_RE = re.compile(r"\d+(?:[-.]\d+)?")
_THIS_CODE_RE = re.compile(r"^[^.;\n]{0,40}?(?:настоящ|данн)\w*\s+кодекс", re.I)
_LAW_AFTER_RE = re.compile(r"^[^.;\n]{0,25}?\b(?:закона|закон|указа|постановлени)", re.I)
_LAW_BEFORE_RE = re.compile(r"(?:закона|закон|указа|постановлени\w*)[^.;\n]{0,50}$", re.I)


def _strip_notes(text: str) -> str:
    for _ in range(3):
        cleaned = _NOTE_RE.sub(" ", text)
        if cleaned == text:
            break
        text = cleaned
    return text


def parse_references(text: str, own_code: str, own_article: str) -> list[dict]:
    """Ссылки из текста одной статьи: [{code, article, snippet}], код — сокращение («ТК РТ»)."""
    body = _strip_notes(_HEADING_RE.sub("", str(text or ""), count=1))
    found = []
    for match in _REF_RE.finditer(body):
        tail = body[match.end():match.end() + 60]
        head = body[max(0, match.start() - 60):match.start()]
        if _THIS_CODE_RE.match(tail):
            code = own_code
        elif _LAW_AFTER_RE.match(tail):
            continue
        else:
            code = chat_modes._code_near(body, match.start(), match.end())  # noqa: SLF001
            if code is None:
                if _LAW_BEFORE_RE.search(head):
                    continue
                code = own_code
        numbers = [match.group(1)] + _NUM_RE.findall(match.group(2) or "")
        if len(numbers) > MAX_LIST_REFS:
            continue
        snippet = re.sub(r"\s+", " ", body[max(0, match.start() - SNIPPET_BEFORE):match.end() + SNIPPET_AFTER]).strip()
        for number in numbers:
            if code == own_code and number == str(own_article):
                continue
            found.append({"code": code, "article": number, "snippet": snippet})
    return found


class ArticleGraph:
    """Граф статей по фрагментам индекса. `code_id_of(source)` даёт id кодекса для ссылок."""

    def __init__(self, chunks, code_id_of: Callable[[str], str]):
        self.nodes: dict[tuple, dict] = {}
        texts: dict[tuple, list] = defaultdict(list)
        shorts: dict[str, set] = defaultdict(set)
        for chunk in chunks or []:
            article = str(chunk.get("article") or "").strip()
            short = chunk.get("code_short")
            source = chunk.get("source")
            if not (article and short and source):
                continue
            key = (code_id_of(source), article)
            if key not in self.nodes:
                self.nodes[key] = {
                    "code": short,
                    "code_name": chunk.get("code") or short,
                    "article": article,
                    "title": chunk.get("article_title") or "",
                }
            shorts[short].add(key[0])
            texts[key].append((chunk.get("part") or 1, chunk.get("text") or ""))

        self._ids = {short: sorted(ids)[0] for short, ids in shorts.items()}
        self.out: dict[tuple, dict] = defaultdict(dict)
        self.inn: dict[tuple, dict] = defaultdict(dict)
        for key, parts in texts.items():
            full = "\n".join(text for _, text in sorted(parts, key=lambda p: p[0]))
            own = self.nodes[key]
            for ref in parse_references(full, own["code"], key[1]):
                cid = self._ids.get(ref["code"])
                target = (cid, ref["article"]) if cid else None
                if target not in self.nodes or target == key:
                    continue
                edge = self.out[key].setdefault(target, {"count": 0, "snippet": ref["snippet"]})
                edge["count"] += 1
                self.inn[target].setdefault(key, edge)

    # ------------------------------------------------------------------ запросы

    def has(self, code_id: str, article: str) -> bool:
        return (code_id, str(article)) in self.nodes

    def stats(self) -> dict:
        return {"articles": len(self.nodes), "links": sum(len(v) for v in self.out.values())}

    def _degree(self, key) -> int:
        return len(self.out.get(key, {})) + len(self.inn.get(key, {}))

    def _node(self, key, focus: bool) -> dict:
        data = self.nodes[key]
        return {
            "id": "%s:%s" % key, "code_id": key[0], "code": data["code"], "code_name": data["code_name"],
            "article": key[1], "title": data["title"], "focus": focus, "degree": self._degree(key),
        }

    def subgraph(self, focus, limit: int = 32, per_node: int = 8) -> dict:
        """Выбранные статьи, их ближайшие соседи и связи между ними.

        У каждой выбранной статьи берутся соседи с самыми сильными связями (сколько раз
        сослались), общее число узлов не превышает `limit`: больше граф не читается.
        """
        keys, wanted = [], []
        for code_id, article in focus or []:
            key = (str(code_id), str(article))
            if key in self.nodes and key not in wanted:
                wanted.append(key)
        chosen = list(wanted)
        for key in wanted:
            neighbours = {}
            for other, edge in self.out.get(key, {}).items():
                neighbours[other] = neighbours.get(other, 0) + edge["count"] + 1
            for other, edge in self.inn.get(key, {}).items():
                neighbours[other] = neighbours.get(other, 0) + edge["count"]
            ranked = sorted(neighbours, key=lambda k: (-neighbours[k], -self._degree(k), k))
            for other in ranked[:per_node]:
                if other not in chosen and len(chosen) < limit:
                    chosen.append(other)
        keys = chosen[:limit]
        inside = set(keys)
        edges = []
        for src in keys:
            for dst, edge in self.out.get(src, {}).items():
                if dst in inside:
                    edges.append({"s": "%s:%s" % src, "t": "%s:%s" % dst, "count": edge["count"], "snippet": edge["snippet"]})
        return {"nodes": [self._node(k, k in wanted) for k in keys], "edges": edges, "stats": self.stats()}

    def article_links(self, code_id: str, article: str) -> Optional[dict]:
        """Исходящие и входящие ссылки одной статьи для боковой панели."""
        key = (str(code_id), str(article))
        if key not in self.nodes:
            return None

        def rows(table):
            items = sorted(table.get(key, {}).items(), key=lambda kv: (-kv[1]["count"], kv[0]))
            return [{**self._node(k, False), "count": e["count"], "snippet": e["snippet"]} for k, e in items]

        return {"node": self._node(key, True), "out": rows(self.out), "in": rows(self.inn)}
