# -*- coding: utf-8 -*-
"""Каталог кодексов РТ из RAG-индекса для страницы /contract/codex.

Страница показывает ровно то, что лежит в индексе: список кодексов, оглавление
(раздел -> глава -> статья), текст статьи и поиск. Ничего не выдумывается: ни
«редакций», ни «версий», ни «источников» — только статьи из индекса.

Индекс режет статьи на фрагменты по ~2500 символов. У фрагментов 2+ первой
строкой повторён заголовок статьи, а текст продолжается с середины фразы, поэтому
статья собирается заново: заголовок снимается, строки склеиваются, жёсткие переносы
старых .doc-файлов убираются.
"""

from __future__ import annotations

import hashlib
import re
from collections import OrderedDict

# Порядок и группы кодексов на странице. Ключ — имя файла-источника в индексе.
# Кодекс, которого здесь нет, всё равно попадёт на страницу (группа «other»).
CATALOG = OrderedDict([
    ("1918_ru_1.docx", ("gk", "civil")),
    ("ГРАЖДАНСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx", ("gpk", "civil")),
    ("ЭКОНОМИЧЕСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx", ("epk", "civil")),
    ("574_ru.doc", ("uk", "criminal")),
    ("УГОЛОВНО - ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx", ("upk", "criminal")),
    ("трудовой кодекс.docx", ("tk", "labor")),
    ("налоговый кодекс.docx", ("nk", "tax")),
    ("семейный кодекс.docx", ("sk", "family")),
    ("КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРОЦЕДУРАХ.docx", ("koapr", "admin")),
    ("ПРОЦЕССУАЛЬНЫЙ КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРАВОНАРУШЕНИЯХ.docx", ("pkoap", "admin")),
    ("Жилишный Кодекс.docx", ("zhk", "housing")),
    ("Градостроительный кодекс.docx", ("grk", "housing")),
    ("Водный кодекс.docx", ("vdk", "nature")),
    ("Лесной кодекс.docx", ("lk", "nature")),
    ("автомабильный кодекс.docx", ("kat", "transport")),
    ("Воздушный кодекс.docx", ("vzdk", "transport")),
    ("Кодекс здравохранение.docx", ("kz", "health")),
])

GROUP_ORDER = ["civil", "criminal", "labor", "tax", "family", "admin", "housing", "nature", "transport", "health", "other"]

SEARCH_LIMIT_MAX = 60
WRAP_MIN_WIDTH = 60   # строка .doc короче — перенос не «упёрся» в ширину, это конец заголовка
SNIPPET_CHARS = 240
TEXT_SCORE_FLOOR = 0.35

_MARKER_RE = re.compile(r"^\s*(?:\d{1,3}[.)]\s|\d{1,3}\)\s*|[а-яё]\)\s|[-–—•]\s)", re.I)
_DASH_RE = re.compile(r"^\s*[-–—•]\s")
_NOTE_RE = re.compile(r"^\s*(?:примечани|ПРИМЕЧАНИ)", re.I)
_SENTENCE_END = (".", ";", ":", "!", "?", ")", "»", '"')
_NUMBER_QUERY_RE = re.compile(
    r"^\s*(?:(?P<code>[A-Za-zА-Яа-яЁё]{1,8})\s*(?:рт)?\s*[,.]?\s*)?(?:ст(?:атья|атьи|\.)?\s*)?(?P<n>\d{1,4}(?:[.\-]\d{1,2})?)\s*(?:рт)?\s*$",
    re.I,
)


def norm_space(text: str) -> str:
    return re.sub(r"\s+", " ", str(text or "")).strip()


def code_id_for(source: str) -> tuple[str, str]:
    """(id, группа) кодекса по имени файла; незнакомые получают стабильный id."""
    if source in CATALOG:
        return CATALOG[source]
    return "x" + hashlib.md5(source.encode("utf-8")).hexdigest()[:6], "other"


def split_header(chunk: dict) -> tuple[str, list[str]]:
    """(полное название статьи, строки текста без заголовка).

    Заголовок — «Статья N. Название», у старых .doc-файлов он перенесён жёстко на несколько
    строк, а индекс хранит в article_title только первую. Поэтому для .doc длинную строку без
    знака конца фразы дочитываем со следующей строки, пока строки «упираются» в ширину переноса.
    """
    lines = str(chunk.get("text") or "").split("\n")
    want = norm_space(chunk.get("title") or "")
    rest = lines
    used = []
    if want:
        acc = ""
        for i, line in enumerate(lines):
            acc = norm_space(acc + " " + line)
            used.append(line)
            if acc == want or acc.startswith(want):
                tail = acc[len(want):].strip()
                rest = ([tail] if tail else []) + lines[i + 1:]
                break
            if not want.startswith(acc):
                used = []
                break
        else:
            used = []
    if not used:
        # Заголовок не совпал буквально: снимаем хотя бы первую строку «Статья ...».
        if lines and re.match(r"^\s*Статья\s+\S+", lines[0]):
            used, rest = [lines[0]], lines[1:]
        else:
            used, rest = [], lines

    wrapped = str(chunk.get("source") or "").lower().endswith(".doc")
    header = list(used)
    while (wrapped and header and rest and len(header) < 6
           and len(header[-1].rstrip()) >= WRAP_MIN_WIDTH
           and not header[-1].rstrip().endswith((".", ";", ":", "!", "?"))
           and rest[0].strip() and not _MARKER_RE.match(rest[0])):
        header.append(rest[0])
        rest = rest[1:]
    title = norm_space(" ".join(header))
    title = re.sub(r"^Статья\s+\S+?\.?\s+", "", title)
    return title, rest


def reflow(lines: list[str]) -> list[dict]:
    """Строки -> абзацы. Склеивает жёсткие переносы, выделяет пункты списка и примечания."""
    blocks: list[str] = []
    for raw in lines:
        line = norm_space(raw)
        if not line:
            continue
        starts_item = bool(_MARKER_RE.match(raw)) or bool(_NOTE_RE.match(raw))
        # «- наказывается ...» после запятой — продолжение фразы (санкция), а не новый пункт.
        if starts_item and blocks and _DASH_RE.match(raw) and not blocks[-1].endswith(_SENTENCE_END):
            starts_item = False
        if blocks and not starts_item:
            prev = blocks[-1]
            join = (not prev.endswith(_SENTENCE_END)) or line[0].islower()
            if join:
                blocks[-1] = prev + " " + line
                continue
        blocks.append(line)
    out = []
    for text in blocks:
        kind = "note" if _NOTE_RE.match(text) else "li" if _MARKER_RE.match(text) else "p"
        out.append({"kind": kind, "text": text})
    return out


def assemble_article(parts: list[dict]) -> list[dict]:
    """Фрагменты одной статьи (в порядке частей) -> абзацы статьи."""
    parts = sorted(parts, key=lambda c: _int(c.get("part"), 1))
    lines: list[str] = []
    for position, chunk in enumerate(parts):
        chunk_lines = str(chunk.get("text") or "").split("\n")
        if position == 0:
            lines.extend(split_header(chunk)[1])
        else:
            # У продолжения повторена первая строка заголовка, текст идёт с середины фразы.
            lines.extend(chunk_lines[1:] if chunk_lines else [])
    return reflow(lines)


def _int(value, default=0) -> int:
    try:
        return int(str(value))
    except (TypeError, ValueError):
        return default


def _article_sort_key(number: str):
    match = re.match(r"^(\d+)(?:[.\-](\d+))?", str(number))
    return (int(match.group(1)), int(match.group(2) or 0)) if match else (10 ** 9, 0)


class Catalog:
    """Кодексы, оглавления и статьи, собранные из индекса один раз."""

    def __init__(self, index: dict):
        self.index = index
        self.built_at = index.get("built_at")
        self.codes: "OrderedDict[str, dict]" = OrderedDict()
        self._by_id: dict[str, dict] = {}
        self._build()

    # ------------------------------------------------------------ сборка
    def _build(self) -> None:
        grouped: "OrderedDict[str, OrderedDict[str, list]]" = OrderedDict()
        for chunk in self.index.get("chunks", []):
            source = chunk.get("source")
            number = str(chunk.get("article") or "").strip()
            if not source or not number:
                continue
            grouped.setdefault(source, OrderedDict()).setdefault(number, []).append(chunk)

        entries = []
        for source, articles in grouped.items():
            cid, group = code_id_for(source)
            first = next(iter(articles.values()))[0]
            entries.append({
                "id": cid,
                "source": source,
                "group": group,
                "label": first.get("code") or source,
                "short": first.get("code_short") or cid.upper(),
                "_articles": articles,
            })
        order = {name: i for i, name in enumerate(CATALOG)}
        entries.sort(key=lambda e: (GROUP_ORDER.index(e["group"]) if e["group"] in GROUP_ORDER else 99,
                                    order.get(e["source"], 999)))

        for entry in entries:
            articles = entry.pop("_articles")
            numbers = list(articles.keys())
            # Разделы и главы — в порядке появления в документе.
            sections: "OrderedDict[str, dict]" = OrderedDict()
            meta: dict[str, dict] = {}
            for position, number in enumerate(numbers):
                chunk = articles[number][0]
                sec_key = str(chunk.get("section") or "")
                chap_key = str(chunk.get("chapter") or "")
                section = sections.setdefault(sec_key, {
                    "id": sec_key, "title": _clean_title(chunk.get("section_title")), "chapters": OrderedDict()})
                chapter = section["chapters"].setdefault(chap_key, {
                    "id": chap_key, "title": _clean_title(chunk.get("chapter_title")), "articles": []})
                title = split_header(chunk)[0] or norm_space(chunk.get("article_title") or "")
                chapter["articles"].append({"n": number, "t": title})
                meta[number] = {
                    "position": position, "section": sec_key, "chapter": chap_key,
                    "section_title": section["title"], "chapter_title": chapter["title"],
                    "title": title, "parts": articles[number],
                }
            toc = [{
                "id": s["id"], "title": s["title"],
                "chapters": [{"id": c["id"], "title": c["title"], "articles": c["articles"]}
                             for c in s["chapters"].values()],
            } for s in sections.values()]
            entry.update({
                "articles": len(numbers),
                "chapters": sum(len(s["chapters"]) for s in sections.values()),
                "sections": len([s for s in sections if s not in ("", "None")]),
            })
            record = dict(entry)
            self.codes[entry["id"]] = record
            self._by_id[entry["id"]] = {"meta": meta, "numbers": numbers, "toc": toc, "cache": {}}

    # ------------------------------------------------------------ чтение
    def catalog(self) -> dict:
        codes = list(self.codes.values())
        groups = []
        for name in GROUP_ORDER:
            members = [c["id"] for c in codes if c["group"] == name]
            if members:
                groups.append({"id": name, "codes": members})
        return {
            "codes": codes,
            "groups": groups,
            "total_articles": sum(c["articles"] for c in codes),
            "built_at": self.built_at,
        }

    def toc(self, code_id: str) -> dict | None:
        data = self._by_id.get(code_id)
        if not data:
            return None
        return {"code": self.codes[code_id], "sections": data["toc"]}

    def article(self, code_id: str, number: str) -> dict | None:
        data = self._by_id.get(code_id)
        number = str(number or "").strip()
        if not data or number not in data["meta"]:
            return None
        if number not in data["cache"]:
            meta = data["meta"][number]
            numbers = data["numbers"]
            pos = meta["position"]
            neighbour = lambda i: {"n": numbers[i], "t": data["meta"][numbers[i]]["title"]} if 0 <= i < len(numbers) else None
            data["cache"][number] = {
                "code": {k: self.codes[code_id][k] for k in ("id", "short", "label")},
                "number": number,
                "title": meta["title"],
                "section": meta["section_title"],
                "chapter": meta["chapter_title"],
                "citation": f"{self.codes[code_id]['short']}, ст. {number}",
                "blocks": assemble_article(meta["parts"]),
                "prev": neighbour(pos - 1),
                "next": neighbour(pos + 1),
            }
        return data["cache"][number]

    # ------------------------------------------------------------ поиск
    def search(self, query: str, lexical, code_id: str | None = None, limit: int = 30) -> dict:
        """Номер статьи -> совпадение в названии -> полнотекстовый поиск (BM25 индекса).

        lexical(query, limit) -> [(score, chunk)] подаётся снаружи, чтобы модуль не знал
        про загрузку индекса.
        """
        query = norm_space(query)
        limit = max(1, min(int(limit or 30), SEARCH_LIMIT_MAX))
        if not query:
            return {"query": query, "results": [], "kind": "empty"}
        results: "OrderedDict[tuple[str, str], dict]" = OrderedDict()

        def add(cid: str, number: str, kind: str, snippet: str | None = None):
            if code_id and cid != code_id:
                return
            key = (cid, number)
            if key in results or len(results) >= limit:
                return
            data = self._by_id.get(cid)
            if not data or number not in data["meta"]:
                return
            meta = data["meta"][number]
            results[key] = {
                "code": cid, "short": self.codes[cid]["short"], "n": number, "t": meta["title"],
                "chapter": meta["chapter_title"], "match": kind,
                "snippet": snippet if snippet is not None else self._snippet(cid, number),
            }

        kind = "text"
        found = _NUMBER_QUERY_RE.match(query)
        if found:
            kind = "number"
            number = found.group("n")
            hint = (found.group("code") or "").lower()
            if hint in ("ст", "статья", "статьи"):    # «ст. 244» — это слово «статья», а не кодекс
                hint = ""
            for cid, record in self.codes.items():
                if hint and not (hint == cid or record["short"].lower().startswith(hint)):
                    continue
                add(cid, number, "number")

        if not results or not found:
            words = [w for w in re.findall(r"[\w-]+", query.lower()) if len(w) >= 3]
            if words:
                stems = [w[:max(4, len(w) - 2)] for w in words]
                hits = []
                for cid, data in self._by_id.items():
                    for number, meta in data["meta"].items():
                        title = meta["title"].lower()
                        if all(stem in title for stem in stems):
                            hits.append((len(title), cid, number))
                for _length, cid, number in sorted(hits):      # короткое название — точнее совпадение
                    add(cid, number, "title")
            ranked = list(lexical(query, limit * 3))
            best = max((score for score, _chunk in ranked), default=0)
            for score, chunk in ranked:
                if best and score < best * TEXT_SCORE_FLOOR:       # слабые совпадения — шум
                    continue
                cid, _group = code_id_for(chunk.get("source") or "")
                number = str(chunk.get("article") or "")
                if number:
                    add(cid, number, "text", snippet=_chunk_snippet(chunk))
            kind = "number" if (found and results) else "text"
        return {"query": query, "kind": kind, "results": list(results.values())[:limit]}

    def _snippet(self, code_id: str, number: str) -> str:
        article = self.article(code_id, number)
        if not article:
            return ""
        text = " ".join(block["text"] for block in article["blocks"])
        return text[:SNIPPET_CHARS].rsplit(" ", 1)[0] + "…" if len(text) > SNIPPET_CHARS else text


def _clean_title(value) -> str:
    text = norm_space(value)
    return "" if text in ("", "None") else text


def _chunk_snippet(chunk: dict) -> str:
    blocks = reflow(split_header(chunk)[1])
    text = " ".join(block["text"] for block in blocks)
    return text[:SNIPPET_CHARS].rsplit(" ", 1)[0] + "…" if len(text) > SNIPPET_CHARS else text
