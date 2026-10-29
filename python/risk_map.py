# -*- coding: utf-8 -*-
"""Карта рисков договора: привязка найденных рисков к абзацам текста.

Модуль не ходит ни в модель, ни в RAG: принимает текст договора и уже
проверенные находки (title / fragment / why / fix / norm / severity) и
возвращает структуру для интерфейса. Благодаря этому его можно проверить
тестами без сервера.

Цитата находки к этому моменту уже прошла проверку «дословно есть в договоре»,
поэтому поиск здесь устроен просто: сначала точное вхождение с учётом пробелов
и регистра, затем совпадение по основам слов. Находка, которой не нашлось
места в тексте, не теряется, а уходит в `unplaced`.
"""

from __future__ import annotations

import re
from typing import Optional

SEVERITIES = ("high", "medium", "low")
RANK = {"high": 3, "medium": 2, "low": 1}

MAX_PARAGRAPHS = 700        # длиннее — карта обрезается, интерфейс говорит об этом
MAX_PARAGRAPH_CHARS = 1800  # один пункт длиннее режется, чтобы не раздувать событие
MIN_SHARE = 0.6             # доля основ цитаты, которая должна найтись в абзаце
MIN_SHARED = 3
STEM_LEN = 5

_WORD_RE = re.compile(r"[A-Za-zА-Яа-яЁёӢӣҒғҚқӮӯҲҳҶҷ0-9]{4,}")
_QUOTE_EDGES = "«»“”„\"'‘’ \t\r\n…"


def _stems(text: str) -> set:
    return {word.lower()[:STEM_LEN] for word in _WORD_RE.findall(text or "")}


def _clean_quote(text) -> str:
    return str(text or "").strip(_QUOTE_EDGES)


def split_paragraphs(text: str) -> tuple[list[str], bool]:
    """Непустые строки договора; второй элемент — обрезана ли карта по длине."""
    paragraphs = []
    for line in str(text or "").replace("\r", "").split("\n"):
        line = re.sub(r"[ \t]+", " ", line).strip()
        if not line:
            continue
        if len(line) > MAX_PARAGRAPH_CHARS:
            line = line[:MAX_PARAGRAPH_CHARS].rsplit(" ", 1)[0] + "…"
        paragraphs.append(line)
    truncated = len(paragraphs) > MAX_PARAGRAPHS
    return paragraphs[:MAX_PARAGRAPHS], truncated


def _exact_span(paragraph: str, quote: str) -> Optional[tuple[int, int]]:
    """Где цитата стоит в абзаце дословно (пробелы и регистр не важны)."""
    words = quote.split()
    if len(words) < 2:
        return None
    pattern = r"\s+".join(re.escape(word) for word in words)
    match = re.search(pattern, paragraph, re.IGNORECASE)
    return (match.start(), match.end()) if match else None


def locate(paragraphs: list[str], quote: str) -> Optional[tuple[int, Optional[tuple[int, int]]]]:
    """(номер абзаца, диапазон цитаты в нём или None) либо None, если не нашлось."""
    quote = _clean_quote(quote)
    if not quote:
        return None
    for index, paragraph in enumerate(paragraphs):
        span = _exact_span(paragraph, quote)
        if span:
            return index, span
    quote_stems = _stems(quote)
    if len(quote_stems) < MIN_SHARED:
        return None
    best, best_score = None, 0.0
    for index, paragraph in enumerate(paragraphs):
        shared = len(quote_stems & _stems(paragraph))
        score = shared / len(quote_stems)
        if shared >= MIN_SHARED and score >= MIN_SHARE and score > best_score:
            best, best_score = index, score
    return (best, None) if best is not None else None


def build_risk_map(text: str, findings, display=None) -> dict:
    """Абзацы договора с подсветкой и список находок.

    `findings` — проверенные находки на языке анализа (по ним ищется место в
    тексте). `display` — те же находки с текстами для показа (например,
    переведёнными); берётся по тому же индексу, а при расхождении длин
    игнорируется.
    """
    findings = [f for f in (findings or []) if isinstance(f, dict)]
    shown = display if isinstance(display, list) and len(display) == len(findings) else findings
    paragraphs, truncated = split_paragraphs(text)

    items, marks = [], {}
    for number, (finding, view) in enumerate(zip(findings, shown)):
        severity = finding.get("severity") if finding.get("severity") in SEVERITIES else "medium"
        where = locate(paragraphs, finding.get("fragment"))
        item = {
            "id": number,
            "severity": severity,
            "title": str(view.get("title") or finding.get("title") or "").strip(),
            "why": str(view.get("why") or "").strip(),
            "fix": str(view.get("fix") or "").strip(),
            "norm": str(view.get("norm") or finding.get("norm") or "").strip(),
            "norm_text": str(finding.get("norm_text") or "").strip(),
            "fragment": _clean_quote(view.get("fragment") or finding.get("fragment")),
            "para": None,
        }
        if where:
            index, span = where
            item["para"] = index
            marks.setdefault(index, []).append((number, span))
        items.append(item)

    rows = []
    for index, paragraph in enumerate(paragraphs):
        row = {"i": index, "text": paragraph}
        here = marks.get(index)
        if here:
            ids = [number for number, _ in here]
            row["findings"] = ids
            row["severity"] = max((items[n]["severity"] for n in ids), key=RANK.get)
            spans = [{"s": s, "e": e, "f": number} for number, span in here if span for s, e in [span]]
            if spans:
                row["spans"] = spans
        rows.append(row)

    counts = {level: sum(1 for item in items if item["severity"] == level) for level in SEVERITIES}
    return {
        "paragraphs": rows,
        "findings": items,
        "counts": counts,
        "unplaced": [item["id"] for item in items if item["para"] is None],
        "truncated": truncated,
    }
