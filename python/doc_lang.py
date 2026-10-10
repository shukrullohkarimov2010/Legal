# -*- coding: utf-8 -*-
"""Язык документа: отчёт отвечает на том языке, на котором написан договор или описание.

Весь разбор (поиск по кодексам, промпты, проверка цитат) русский: корпус законов
русский, а модель на русском юридическом тексте точнее. Поэтому таджикский документ
переводится на русский, анализируется, а готовый отчёт переводится обратно. Русский
документ проходит как раньше, без единого дополнительного вызова.

Модуль не ходит в сеть сам: перевод делает переданная функция `llm(prompt, num_predict,
json_mode=False) -> str`. Благодаря этому его можно проверить тестами без модели.
"""

from __future__ import annotations

import json
import re
from concurrent.futures import ThreadPoolExecutor
from typing import Callable, List, Optional

_CYRILLIC = re.compile(r"[А-Яа-яЁёӢӣӮӯҚқҒғҲҳҶҷ]")
_TAJIK_ONLY = re.compile(r"[ӢӣӮӯҚқҒғҲҳҶҷ]")

LANG_NAMES = {"ru": "русского", "tg": "таджикского"}
LANG_TO = {"ru": "русский", "tg": "таджикский"}

# В таджикском тексте специфические буквы составляют несколько процентов букв, в русском их нет
# вовсе. Порог взят с запасом: одно-два вкрапления в русский договор (фамилия, топоним) языком
# документа его не делают.
TAJIK_LETTER_RATIO = 0.012
MIN_CYRILLIC_LETTERS = 200


def is_tajik_text(text: str, sample: int = 8000) -> bool:
    """Таджикский ли документ: по доле таджикских букв среди кириллических."""
    chunk = (text or "")[:sample]
    cyrillic = len(_CYRILLIC.findall(chunk))
    if cyrillic < MIN_CYRILLIC_LETTERS:
        return False
    return len(_TAJIK_ONLY.findall(chunk)) / cyrillic >= TAJIK_LETTER_RATIO


def split_paragraphs(text: str, limit: int = 3500) -> List[str]:
    """Режет текст по абзацам на куски не длиннее limit; склейка кусков через \\n\\n даёт исходный порядок."""
    parts: List[str] = []
    current = ""
    for paragraph in re.split(r"\n{2,}", text or ""):
        # очень длинный абзац без разрывов режем по предложениям
        pieces = [paragraph]
        if len(paragraph) > limit:
            pieces = re.split(r"(?<=[.!?;])\s+", paragraph)
        for piece in pieces:
            while len(piece) > limit:
                parts.append(piece[:limit])
                piece = piece[limit:]
            if current and len(current) + len(piece) + 2 > limit:
                parts.append(current)
                current = piece
            else:
                current = (current + "\n\n" + piece) if current else piece
    if current:
        parts.append(current)
    return parts or [""]


def _prompt(part: str, src: str, tgt: str, kind: str, rules: str = "") -> str:
    return (
        f"Переведи {kind} с {LANG_NAMES[src]} языка на {LANG_TO[tgt]} язык ({'кириллица' if tgt == 'tg' else 'литературный русский'}) точно и полно.\n"
        "Правила: сохрани нумерацию пунктов, абзацы и переносы строк; числа, суммы, даты, проценты, реквизиты, "
        "названия организаций, адреса и имена оставь без изменений; не сокращай, не пересказывай, ничего не добавляй "
        "и не объясняй. Юридические термины переводи принятыми в праве Республики Таджикистан эквивалентами. "
        + rules +
        "Ответ — только перевод.\n\n"
        f"ТЕКСТ:\n{part}\n\nПЕРЕВОД:"
    )


_LABEL_RE = re.compile(r"^\s*(?:ПЕРЕВОД|ТАРҶУМА|Перевод|Тарҷума|Translation|TRANSLATION)\s*:\s*", re.U)


def clean_translation(text) -> str:
    """Ответ модели без повторённой метки «ПЕРЕВОД:» и без лишних пустых строк."""
    out = _LABEL_RE.sub("", str(text or "").strip())
    out = re.sub(r"\n\s*(?:ПЕРЕВОД|ТАРҶУМА|Перевод|Тарҷума)\s*:?\s*$", "", out)   # метка в конце ответа
    return re.sub(r"\n{3,}", "\n\n", out).strip()


def translate_text(text: str, src: str, tgt: str, llm: Callable[..., str],
                   kind: str = "юридический текст", workers: int = 4, limit: int = 3500, rules: str = "") -> str:
    """Перевод длинного текста: куски идут параллельно, порядок сохраняется. Любой сбой куска — исключение."""
    parts = split_paragraphs(text, limit)

    def one(part: str) -> str:
        if not part.strip():
            return part
        out = clean_translation(llm(_prompt(part, src, tgt, kind, rules), max(900, int(len(part) * 1.2))))
        if not out:
            raise RuntimeError("пустой перевод")
        return out

    with ThreadPoolExecutor(max(1, min(workers, len(parts)))) as pool:
        return "\n\n".join(pool.map(one, parts))


def translate_items(items: List[str], src: str, tgt: str, llm: Callable[..., str],
                    kind: str = "фрагменты юридического отчёта") -> List[str]:
    """Перевод списка коротких строк одним вызовом. Что не вернулось — остаётся оригиналом."""
    clean = [str(item or "") for item in items]
    todo = [(i, s) for i, s in enumerate(clean) if s.strip()]
    if not todo:
        return clean
    payload = json.dumps([{"i": i, "text": s} for i, s in todo], ensure_ascii=False)
    prompt = (
        f"Переведи {kind} с {LANG_NAMES[src]} языка на {LANG_TO[tgt]} язык ({'кириллица' if tgt == 'tg' else 'русский'}). "
        "Сохрани числа, номера статей и пунктов, эмодзи, знаки препинания по краям и названия организаций; "
        "не добавляй пояснений. Юридические термины — принятые в праве Республики Таджикистан.\n\n"
        f"Строки: {payload}\n\n"
        'Ответ — только JSON: {"items":[{"i":0,"text":"..."}, ...]}, по элементу на каждую строку.'
    )
    try:
        raw = llm(prompt, max(1200, sum(len(s) for _, s in todo) * 2), json_mode=True)
        data = json.loads(raw)
        translated = {int(x["i"]): str(x["text"]).strip() for x in data["items"] if str(x.get("text", "")).strip()}
    except Exception:  # noqa: BLE001
        return clean
    return [translated.get(i, s) for i, s in enumerate(clean)]


# ---------------------------------------------------------------- подписи отчёта

REPORT_LABELS = {
    "ru": {
        "title": "## Проверенный RAG-анализ договора",
        "source": "Источник права: `{source}` (Республика Таджикистан).",
        "scope": "В отчёт включены пункты с цитатой из договора и нормой, сверенной с подключённым RAG.",
        "honest": "Если модель не смогла указать точную статью, это честно помечено как требующее ручной проверки.",
        "translated": "",
        "type": "Определённый тип договора: **{type}** (уверенность: {confidence}).",
        "alt": "Альтернативные варианты: {types}.",
        "risks": "### Выявленные риски",
        "none": "Подтверждённых рисков не найдено: для публикации требуется одновременно цитата из договора и применимая статья из RAG.",
        "manual": "⚠️ **Требуется ручная проверка.** Договор объёмный, но система не подтвердила ни одного риска. "
                  "Это может быть следствием нестандартных формулировок — заключение должен проверить практикующий юрист.",
        "risk_default": "Юридический риск",
        "level": "Уровень", "point": "Пункт договора", "point_none": "не указан в документе",
        "quote": "Цитата из договора", "quote_none": "условие отсутствует в договоре",
        "norm": "Норма РТ из RAG", "why": "Основание", "fix": "Рекомендуемая правка",
        "norm_text": "**Текст нормы из подключённого RAG:**",
        "checklist": "### Чек-лист обязательных условий",
        "checklist_note": "Ниже — механическая проверка наличия в тексте формулировок по стандартным пунктам договора "
                          "(регулярные выражения, а не анализ модели). Эти пункты **не учитываются** в балле риска и "
                          "вердикте ниже: пункт мог фактически присутствовать в договоре другими словами, которые проверка "
                          "не распознала, поэтому относитесь к списку как к подсказке для самостоятельной проверки, а не как "
                          "к подтверждённому риску.",
        "condition": "Условие", "significance": "уровень значимости",
        "summary": "### Резюме", "score": "Балл риска", "high": "Высоких рисков", "medium": "Средних рисков",
        "low": "Низких рисков", "verdict": "Вердикт",
        "footer": "Отчёт основан на подключённой базе RAG и не заменяет проверку практикующим юристом.",
        "severity": {"high": "Высокий", "medium": "Средний", "low": "Низкий"},
    },
    "tg": {
        "title": "## Таҳлили санҷидашудаи шартнома бо RAG",
        "source": "Сарчашмаи ҳуқуқ: `{source}` (Ҷумҳурии Тоҷикистон).",
        "scope": "Дар ҳисобот бандҳое дохил шудаанд, ки иқтибос аз шартнома ва меъёри бо RAG-и пайвастшуда санҷидашуда доранд.",
        "honest": "Агар модел моддаи дақиқро нишон дода натавонад, ин ҳамчун «санҷиши дастӣ лозим» қайд карда мешавад.",
        "translated": "Эзоҳ: шартнома барои таҳлил ба забони русӣ тарҷума шуд, бинобар ин иқтибосҳо аз шартнома бо тарҷумаи баръакс "
                      "оварда шудаанд ва аз матни аслӣ каме фарқ карда метавонанд. Матни меъёрҳо бо забони русӣ, чун дар кодекси расмӣ, оварда мешавад.",
        "type": "Навъи муайяншудаи шартнома: **{type}** (боварӣ: {confidence}).",
        "alt": "Вариантҳои алтернативӣ: {types}.",
        "risks": "### Хавфҳои ошкоршуда",
        "none": "Хавфҳои тасдиқшуда ёфт нашуданд: барои нашр ҳам иқтибос аз шартнома ва ҳам моддаи мувофиқ аз RAG лозим аст.",
        "manual": "⚠️ **Санҷиши дастӣ лозим аст.** Шартнома ҳаҷман калон аст, вале система ягон хавфро тасдиқ накард. "
                  "Ин метавонад оқибати ибораҳои ғайристандартӣ бошад — хулосаро ҳуқуқшиноси амалкунанда бояд санҷад.",
        "risk_default": "Хавфи ҳуқуқӣ",
        "level": "Дараҷа", "point": "Банди шартнома", "point_none": "дар ҳуҷҷат нишон дода нашудааст",
        "quote": "Иқтибос аз шартнома", "quote_none": "шарт дар шартнома мавҷуд нест",
        "norm": "Меъёри ҶТ аз RAG", "why": "Асос", "fix": "Таҳрири тавсияшаванда",
        "norm_text": "**Матни меъёр аз RAG-и пайвастшуда (бо забони русӣ):**",
        "checklist": "### Рӯйхати шартҳои ҳатмӣ",
        "checklist_note": "Дар зер — санҷиши механикии мавҷудияти ибораҳо оид ба бандҳои стандартии шартнома "
                          "(ифодаҳои мунтазам, на таҳлили модел). Ин бандҳо ба холи хавф ва ҳукми зерин **ба ҳисоб гирифта намешаванд**: "
                          "банд метавонист дар шартнома бо калимаҳои дигар бошад ва санҷиш онро нашинохта бошад, бинобар ин рӯйхатро "
                          "ҳамчун ёддошт барои санҷиши мустақилона қабул кунед, на ҳамчун хавфи тасдиқшуда.",
        "condition": "Шарт", "significance": "дараҷаи аҳамият",
        "summary": "### Хулоса", "score": "Холи хавф", "high": "Хавфҳои баланд", "medium": "Хавфҳои миёна",
        "low": "Хавфҳои паст", "verdict": "Ҳукм",
        "footer": "Ҳисобот ба пойгоҳи пайвастшудаи RAG асос ёфтааст ва санҷиши ҳуқуқшиноси амалкунандаро иваз намекунад.",
        "severity": {"high": "Баланд", "medium": "Миёна", "low": "Паст"},
    },
}

# Решения системы: фиксированные формулировки, поэтому переводятся без модели.
LEVELS_TG = {"Высокий": "Баланд", "Средний": "Миёна", "Низкий": "Паст"}
VERDICTS_TG = {
    "❌ Не подписывать": "❌ Имзо накунед",
    "⚠️ Требуется ручная проверка": "⚠️ Санҷиши дастӣ лозим аст",
    "⚠️ Требует правок": "⚠️ Таҳрир лозим аст",
    "✅ Можно подписывать": "✅ Имзо кардан мумкин аст",
}


def localize_report_inputs(metrics: dict, type_info: Optional[dict], llm: Callable[..., str]):
    """Копии метрик и типа договора с текстами на таджикском; числа, номера и цитаты норм не трогаются.

    Переводится только то, что написала модель: названия рисков, цитаты из договора, основания, правки,
    чек-лист, тип договора и вердикт. Ссылка на норму (кодекс, глава, статья) идёт тем же вызовом.
    Текст нормы (`norm_text`) остаётся русским: это дословная выдержка из кодекса.
    """
    metrics = json.loads(json.dumps(metrics, ensure_ascii=False, default=str))
    type_info = json.loads(json.dumps(type_info, ensure_ascii=False, default=str)) if type_info else type_info

    slots = []  # (контейнер, ключ)
    for finding in metrics.get("findings") or []:
        for key in ("title", "fragment", "norm", "why", "fix"):
            if finding.get(key):
                slots.append((finding, key))
    for finding in metrics.get("checklist_findings") or []:
        for key in ("title", "fix"):
            if finding.get(key):
                slots.append((finding, key))
    if type_info and type_info.get("type"):
        slots.append((type_info, "type"))
        for entry in (type_info.get("types_with_scores") or [])[1:3]:
            if entry.get("type"):
                slots.append((entry, "type"))

    translated = translate_items([container[key] for container, key in slots], "ru", "tg", llm)
    for (container, key), value in zip(slots, translated):
        # отчёт сам берёт цитату в «ёлочки»: кавычки переводчика дали бы «« »»
        container[key] = value.strip(" «»\"") if key == "fragment" else value

    verdict = metrics.get("verdict")
    if verdict:
        metrics["verdict"] = VERDICTS_TG.get(verdict, verdict)
    if metrics.get("level"):
        metrics["level"] = LEVELS_TG.get(metrics["level"], metrics["level"])
    return metrics, type_info
