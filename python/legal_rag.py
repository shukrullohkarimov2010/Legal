# -*- coding: utf-8 -*-
"""Поиск по кодексам Республики Таджикистан.

Модуль решает четыре задачи, которые нельзя решить обычной нарезкой текста
по числу символов:

1. Разбиение кодекса на фрагменты по границам статей, а не по смещению
   в символах. Каждый фрагмент знает свой кодекс, раздел, главу и номер
   статьи, поэтому ссылка на норму строится из метаданных, а не угадывается
   регулярным выражением по тексту ответа модели.
2. Морфологическую нормализацию русского текста (стеммер Snowball),
   без которой «отпуск» и «отпуска» остаются разными термами.
3. Расширение запроса юридическими синонимами: в кодексе написано «налог на
   добавленную стоимость», а пользователь спрашивает про «НДС».
4. Маршрутизацию по кодексам: вопрос про увольнение не должен конкурировать
   с Гражданским кодексом за место в выдаче.

Векторный поиск подключается опционально (Ollama-эмбеддинги). Если модель
эмбеддингов недоступна, движок работает в чисто лексическом режиме — это
осознанное требование: юридическая система должна работать офлайн.
"""

from __future__ import annotations

import json
import math
import os
import re
import unicodedata
from collections import Counter, OrderedDict

# Версия схемы индекса. Любое изменение нарезки, токенизации или набора
# полей фрагмента обязано увеличивать её: устаревшие индексы пересобираются
# автоматически, а не отдают молча испорченную выдачу.
INDEX_VERSION = "v2-article-hybrid"

# ============================================================================
# 1. МОРФОЛОГИЯ: стеммер Snowball для русского языка
# ============================================================================

class RussianStemmer:
    """Алгоритм Snowball (Porter) для русского языка.

    Реализован внутри проекта намеренно: юридический контур обязан
    запускаться без доступа в сеть и без установки пакетов.
    """

    VOWELS = frozenset("аеиоуыэюя")

    _PERFECTIVE_GERUND_1 = ("вшись", "вши", "в")
    _PERFECTIVE_GERUND_2 = ("ившись", "ывшись", "ивши", "ывши", "ив", "ыв")
    _ADJECTIVE = (
        "ими", "ыми", "его", "ого", "ему", "ому", "их", "ых",
        "ее", "ие", "ые", "ое", "ей", "ий", "ый", "ой",
        "ем", "им", "ым", "ом", "ую", "юю", "ая", "яя", "ою", "ею",
    )
    _PARTICIPLE_1 = ("ющ", "вш", "нн", "ем", "щ")
    _PARTICIPLE_2 = ("ующ", "ивш", "ывш")
    _REFLEXIVE = ("ся", "сь")
    _VERB_1 = (
        "ете", "йте", "ешь", "нно", "ла", "на", "ли", "ем", "ло",
        "но", "ет", "ют", "ны", "ть", "й", "л", "н",
    )
    _VERB_2 = (
        "ейте", "уйте", "ила", "ыла", "ена", "ите", "или", "ыли", "ило",
        "ыло", "ено", "ует", "уют", "ены", "ить", "ыть", "ишь", "ей",
        "уй", "ил", "ыл", "им", "ым", "ен", "ят", "ит", "ыт", "ую", "ю",
    )
    _NOUN = (
        "иями", "ями", "ами", "иях", "ях", "иям", "ям", "ием", "ем",
        "ией", "ей", "ев", "ов", "ие", "ье", "еи", "ии", "ой", "ий",
        "ию", "ью", "ия", "ья", "ам", "ом", "ах", "а", "е", "и", "й",
        "о", "у", "ы", "ь", "ю", "я",
    )
    _SUPERLATIVE = ("ейше", "ейш")
    _DERIVATIONAL = ("ость", "ост")

    def __init__(self):
        self._cache: dict[str, str] = {}
        # Длинные окончания проверяются первыми — иначе «иями» срежется как «и».
        self._groups = {
            name: tuple(sorted(value, key=len, reverse=True))
            for name, value in (
                ("pg1", self._PERFECTIVE_GERUND_1),
                ("pg2", self._PERFECTIVE_GERUND_2),
                ("adj", self._ADJECTIVE),
                ("part1", self._PARTICIPLE_1),
                ("part2", self._PARTICIPLE_2),
                ("refl", self._REFLEXIVE),
                ("verb1", self._VERB_1),
                ("verb2", self._VERB_2),
                ("noun", self._NOUN),
                ("sup", self._SUPERLATIVE),
                ("deriv", self._DERIVATIONAL),
            )
        }

    # -- разметка областей RV / R1 / R2 ------------------------------------
    def _regions(self, word: str) -> tuple[int, int]:
        rv = len(word)
        for i, ch in enumerate(word):
            if ch in self.VOWELS:
                rv = i + 1
                break

        def _after_vowel_consonant(start: int) -> int:
            for i in range(start, len(word) - 1):
                if word[i] in self.VOWELS and word[i + 1] not in self.VOWELS:
                    return i + 2
            return len(word)

        r1 = _after_vowel_consonant(0)
        r2 = _after_vowel_consonant(r1)
        return rv, r2

    def _cut(self, word: str, region: int, group: str, preceded_by=None):
        """Срезает самое длинное окончание группы, если оно лежит в области."""
        for ending in self._groups[group]:
            if not word.endswith(ending):
                continue
            base = len(word) - len(ending)
            if base < region:
                continue
            if preceded_by is not None:
                if base == 0 or word[base - 1] not in preceded_by:
                    continue
                # Буква а/я — часть окончания и удаляется вместе с ним.
                return word[: base - 1]
            return word[:base]
        return None

    # Разносклоняемые существительные на -мя: «время» даёт основу «врем»,
    # а «временем» — «времен», и запрос перестаёт находить статью «Рабочее
    # время». Приводим начальную форму к той же основе, что и косвенные
    # падежи; всё остальное алгоритм разбирает сам.
    IRREGULAR = {
        "время": "времен", "имя": "имен", "знамя": "знамен",
        "племя": "племен", "бремя": "бремен", "семя": "семен",
        "темя": "темен", "стремя": "стремен", "пламя": "пламен",
        "вымя": "вымен", "дитя": "дет", "дети": "дет", "ребенок": "ребенк",
        "человек": "человек", "люди": "человек", "год": "год", "лет": "год",
    }

    def stem(self, word: str) -> str:
        cached = self._cache.get(word)
        if cached is not None:
            return cached

        original = word
        work = word.replace("ё", "е")

        irregular = self.IRREGULAR.get(work)
        if irregular is not None:
            self._cache[original] = irregular
            return irregular
        if len(work) < 3 or not any(ch in self.VOWELS for ch in work):
            self._cache[original] = work
            return work

        rv, r2 = self._regions(work)

        # Шаг 1
        step1 = self._cut(work, rv, "pg2")
        if step1 is None:
            step1 = self._cut(work, rv, "pg1", preceded_by="ая")
        if step1 is not None:
            work = step1
        else:
            reflexive = self._cut(work, rv, "refl")
            if reflexive is not None:
                work = reflexive
            adjectival = self._cut(work, rv, "adj")
            if adjectival is not None:
                work = adjectival
                participle = self._cut(work, rv, "part2")
                if participle is None:
                    participle = self._cut(work, rv, "part1", preceded_by="ая")
                if participle is not None:
                    work = participle
            else:
                verb = self._cut(work, rv, "verb2")
                if verb is None:
                    verb = self._cut(work, rv, "verb1", preceded_by="ая")
                if verb is not None:
                    work = verb
                else:
                    noun = self._cut(work, rv, "noun")
                    if noun is not None:
                        work = noun

        # Шаг 2
        if work.endswith("и") and len(work) - 1 >= rv:
            work = work[:-1]

        # Шаг 3
        derivational = self._cut(work, r2, "deriv")
        if derivational is not None:
            work = derivational

        # Шаг 4
        if work.endswith("нн"):
            work = work[:-1]
        else:
            superlative = self._cut(work, rv, "sup")
            if superlative is not None:
                work = superlative
                if work.endswith("нн"):
                    work = work[:-1]
        if work.endswith("ь"):
            work = work[:-1]

        result = work or original
        self._cache[original] = result
        return result


_STEMMER = RussianStemmer()

# Таджикские буквы сохраняются: часть источников содержит таджикские вставки.
_WORD_RE = re.compile(r"[0-9A-Za-zА-Яа-яЁёІіҶҷҚқҒғӢӣӮӯҲҳ]+")

# Служебные слова выбрасываются: в кодексах они есть в каждом фрагменте
# и только размывают BM25.
STOPWORDS = frozenset("""
и в во не что он на я с со как а то все она так его но да ты к у же вы за бы
по только ее мне было вот от меня еще нет о из ему теперь когда даже ну вдруг
ли если или быть был него до вас нибудь опять уж вам ведь там потом себя ничего
ей может они тут где есть надо ней для мы тебя их чем была сам чтоб без будто
чего раз тоже себе под будет ж тогда кто этот того потому этого какой совсем
ним здесь этом один почти мой тем чтобы нее сейчас были куда зачем всех никогда
можно при наконец два об другой хоть после над больше тот через эти нас про
всего них какая много разве три эту моя впрочем хорошо свою этой перед иногда
лучше чуть том нельзя такой им более всегда конечно всю между также иные иным
которых которые который котором настоящего настоящей настоящим настоящему
""".split())

# Термины, которые нельзя выбрасывать даже при коротком написании.
_KEEP_SHORT = frozenset({"ндс", "ип", "чп", "ооо", "гк", "ук", "тк", "нк", "ск", "рт", "дтп"})


def tokenize(text: str) -> list[str]:
    """Токены с нормализацией: нижний регистр, стемминг, без стоп-слов."""
    tokens = []
    for raw in _WORD_RE.findall((text or "").lower()):
        if raw in _KEEP_SHORT:
            tokens.append(raw)
            continue
        if raw.isdigit():
            tokens.append(raw)
            continue
        if raw in STOPWORDS or len(raw) < 3:
            continue
        tokens.append(_STEMMER.stem(raw))
    return tokens


# ============================================================================
# 2. ЮРИДИЧЕСКИЕ СИНОНИМЫ
# ============================================================================

# Ключ — то, как спрашивает человек; значения — как это названо в кодексе.
# Раскрытие происходит до стемминга, поэтому записи даются в обычной форме.
LEGAL_SYNONYMS: dict[str, tuple[str, ...]] = {
    "ндс": ("налог на добавленную стоимость",),
    "ип": ("индивидуальный предприниматель",),
    "чп": ("индивидуальный предприниматель",),
    "ооо": ("общество с ограниченной ответственностью",),
    "зарплата": ("заработная плата", "оплата труда"),
    "зп": ("заработная плата", "оплата труда"),
    "увольнение": ("прекращение трудового договора", "расторжение трудового договора"),
    "уволить": ("прекращение трудового договора", "расторжение трудового договора"),
    "отпуск": ("трудовой отпуск", "ежегодный отпуск"),
    "развод": ("расторжение брака",),
    "развестись": ("расторжение брака",),
    "алименты": ("содержание детей", "взыскание алиментов"),
    "давность": ("исковая давность",),
    "штраф": ("неустойка", "пеня", "штраф"),
    "пеня": ("неустойка",),
    "аренда": ("аренда", "имущественный наём"),
    "снять": ("аренда",),
    "покупка": ("купля-продажа",),
    "продажа": ("купля-продажа",),
    "кража": ("тайное хищение чужого имущества",),
    "мошенничество": ("хищение путем обмана", "мошенничество"),
    "взятка": ("получение взятки", "дача взятки"),
    "дтп": ("дорожно-транспортное происшествие",),
    "перевозка": ("перевозка груза", "перевозка пассажиров"),
    "наследство": ("наследование", "наследственное имущество"),
    "завещание": ("наследование по завещанию",),
    "прописка": ("место жительства", "регистрация"),
    "декрет": ("отпуск по беременности и родам",),
    "больничный": ("временная нетрудоспособность",),
    "неустойка": ("неустойка", "пеня", "штраф"),
    "расписка": ("договор займа",),
    "долг": ("обязательство", "заём"),
    "подряд": ("договор подряда",),
    "услуги": ("возмездное оказание услуг",),
    "усыновить": ("усыновление",),
    "удочерить": ("усыновление",),
    "взыскать": ("взыскание",),
    "наследовать": ("наследование",),
    "арендовать": ("аренда", "имущественный наём"),
    "застраховать": ("страхование",),
    "уволиться": ("прекращение трудового договора",),
    "вычет": ("налоговый вычет",),
    "ставка": ("налоговая ставка",),
    "проверка": ("налоговая проверка",),
}


# Ключи синонимов сравниваются по основам: «зарплаты», «зарплате» и «зарплата»
# должны срабатывать одинаково.
_SYNONYM_STEMS: dict[str, tuple[str, ...]] = {}
for _trigger, _expansions in LEGAL_SYNONYMS.items():
    for _stem in (tokenize(_trigger) or [_trigger]):
        _SYNONYM_STEMS[_stem] = tuple(
            OrderedDict.fromkeys(_SYNONYM_STEMS.get(_stem, ()) + _expansions)
        )


def expand_query(query: str) -> str:
    """Дописывает к запросу формулировки, которыми оперирует сам кодекс."""
    additions: list[str] = []
    for stem in tokenize(query):
        expansions = _SYNONYM_STEMS.get(stem)
        if expansions:
            additions.extend(expansions)
    if not additions:
        return query or ""
    # dict.fromkeys — дедупликация с сохранением порядка.
    return (query or "") + " " + " ".join(OrderedDict.fromkeys(additions))


# ============================================================================
# 3. МАРШРУТИЗАЦИЯ ПО КОДЕКСАМ
# ============================================================================

CODE_SHORT = {
    "1918_ru_1.docx": "ГК РТ",
    "574_ru.doc": "УК РТ",
    "семейный кодекс.docx": "СК РТ",
    "налоговый кодекс.docx": "НК РТ",
    "трудовой кодекс.docx": "ТК РТ",
    "автомабильный кодекс.docx": "КАТ РТ",
}

# Слова-маркеры, по которым определяется профильный кодекс. Список
# намеренно короткий: маршрутизатор должен подсказывать, а не решать.
CODE_HINTS: dict[str, tuple[str, ...]] = {
    "1918_ru_1.docx": (
        "договор", "сделка", "обязательство", "собственность", "аренда",
        "купля", "продажа", "поставка", "подряд", "заем", "займ", "залог",
        "наследство", "наследование", "завещание", "юридическое лицо",
        "исковая давность", "возмещение вреда", "неустойка", "дарение",
        "гражданский кодекс", "гк",
    ),
    "574_ru.doc": (
        "преступление", "наказание", "кража", "мошенничество", "убийство",
        "лишение свободы", "уголовн", "хищение", "взятка", "судимость",
        "соучастие", "покушение", "ук",
    ),
    "трудовой кодекс.docx": (
        "работник", "работодатель", "трудовой договор", "отпуск", "увольнение",
        "уволить", "заработная плата", "зарплата", "рабочее время", "прогул",
        "трудовой кодекс", "стаж", "больничный", "тк",
    ),
    "налоговый кодекс.docx": (
        "налог", "ндс", "акциз", "декларация", "налогоплательщик", "вычет",
        "налоговая", "пошлина", "патент", "упрощен", "налоговый кодекс", "нк",
    ),
    "семейный кодекс.docx": (
        "брак", "супруг", "развод", "алименты", "усыновление", "опека",
        "родител", "ребен", "детей", "семейный кодекс", "ск",
    ),
    "автомабильный кодекс.docx": (
        "перевозка", "перевозчик", "автомобильн", "транспорт", "водител",
        "груз", "пассажир", "маршрут", "дтп", "фрахт",
    ),
}

# Насколько сильно маршрутизатор влияет на итог. Профильный кодекс получает
# множитель ROUTE_BOOST, непрофильные — ROUTE_DAMPING. Оба значения мягкие:
# вопрос на стыке отраслей не должен терять релевантные нормы.
ROUTE_BOOST = 1.30
ROUTE_DAMPING = 0.88
# Ниже двух совпадений сигнал слишком слаб: «договор» есть в любом кодексе.
ROUTE_MIN_HITS = 2

# Подсказки сравниваются по основам слов: «усыновить» и «усыновление» должны
# указывать на один кодекс, хотя как подстроки они не совпадают.
_HINT_STEMS = {
    source: tuple(tuple(tokenize(hint)) for hint in hints)
    for source, hints in CODE_HINTS.items()
}

# Минимальная длина общего префикса основ. Стеммер не сводит отглагольные
# существительные к глаголу («усыновление» / «усыновить»), поэтому основы
# сопоставляются по префиксу, а не на точное равенство.
_STEM_PREFIX_MIN = 5


def _stem_matches(hint_stem: str, query_stems: frozenset) -> bool:
    if hint_stem in query_stems:
        return True
    if len(hint_stem) < _STEM_PREFIX_MIN:
        return False
    head = hint_stem[:_STEM_PREFIX_MIN]
    return any(
        len(stem) >= _STEM_PREFIX_MIN and stem.startswith(head)
        for stem in query_stems
    )


def route_codes(query: str) -> dict[str, float]:
    """Множитель релевантности для каждого кодекса.

    Маршрутизатор подсказывает, а не решает: разрыв между профильным и
    остальными кодексами намеренно небольшой, иначе вопросы на стыке
    отраслей (трудовой спор с гражданско-правовым требованием) теряют
    половину нужных норм.
    """
    query_stems = frozenset(tokenize(query))
    if not query_stems:
        return {source: 1.0 for source in CODE_HINTS}

    hits = {
        source: sum(
            1 for hint in hint_stems
            if hint and all(_stem_matches(stem, query_stems) for stem in hint)
        )
        for source, hint_stems in _HINT_STEMS.items()
    }
    best = max(hits.values()) if hits else 0
    if best < ROUTE_MIN_HITS:
        return {source: 1.0 for source in CODE_HINTS}
    return {
        source: ROUTE_BOOST if count == best else (1.0 if count else ROUTE_DAMPING)
        for source, count in hits.items()
    }


# ============================================================================
# 4. НАРЕЗКА ПО СТАТЬЯМ
# ============================================================================

ARTICLE_RE = re.compile(r"^\s*Статья\s+(\d+(?:[-–—]\d+)?(?:\.\d+)?)\s*\.?\s*(.*)$", re.IGNORECASE)
CHAPTER_RE = re.compile(r"^\s*ГЛАВА\s+(\d+(?:\.\d+)?)\s*\.?\s*(.*)$", re.IGNORECASE)
SECTION_RE = re.compile(r"^\s*(?:РАЗДЕЛ|ПОДРАЗДЕЛ)\s+([IVXLCDM]+|\d+(?:\.\d+)?)\s*\.?\s*(.*)$", re.IGNORECASE)
PARAGRAPH_RE = re.compile(r"^\s*§\s*(\d+(?:\.\d+)?)\s*\.?\s*(.*)$")

# Тело статьи короче этого порога — почти наверняка строка оглавления.
MIN_ARTICLE_BODY_CHARS = 40
# Длинные статьи (например, о видах наказаний) режутся на части.
MAX_ARTICLE_CHARS = 2400
ARTICLE_PART_OVERLAP = 160


def normalize_document_text(text: str) -> str:
    """Приводит переводы строк к \\n и убирает неразрывные пробелы.

    Извлечение .doc отдаёт абзацы, разделённые \\r; без нормализации
    вся статья выглядит одной строкой и границы абзацев теряются.
    """
    if not text:
        return ""
    text = unicodedata.normalize("NFKC", text)
    text = text.replace("\r\n", "\n").replace("\r", "\n")
    text = text.replace(" ", " ").replace("", "\n")
    return text


def _split_long_body(body: str) -> list[str]:
    """Режет длинное тело статьи по границам абзацев, а не по символам."""
    if len(body) <= MAX_ARTICLE_CHARS:
        return [body]
    paragraphs = [p for p in body.split("\n") if p.strip()]
    parts: list[str] = []
    buffer: list[str] = []
    size = 0
    for paragraph in paragraphs:
        if size and size + len(paragraph) > MAX_ARTICLE_CHARS:
            parts.append("\n".join(buffer))
            tail = buffer[-1] if buffer else ""
            buffer = [tail[-ARTICLE_PART_OVERLAP:]] if len(tail) > ARTICLE_PART_OVERLAP else []
            size = sum(len(item) for item in buffer)
        buffer.append(paragraph)
        size += len(paragraph)
    if buffer:
        parts.append("\n".join(buffer))
    return parts or [body]


def build_article_chunks(text: str, source_name: str, code_label: str) -> list[dict]:
    """Разбивает кодекс на фрагменты по статьям с полными реквизитами.

    Оглавление отбрасывается: у его строк нет тела, между двумя заголовками
    статей нет ни одного содержательного абзаца.
    """
    lines = [line.strip() for line in normalize_document_text(text).split("\n")]
    lines = [line for line in lines if line]

    section = section_title = None
    chapter = chapter_title = None
    paragraph_title = None
    blocks: list[dict] = []
    current: dict | None = None

    def flush():
        if current and current["body"]:
            blocks.append(current)

    for position, line in enumerate(lines):
        section_match = SECTION_RE.match(line)
        if section_match and len(line) < 200:
            flush()
            current = None
            section, section_title = section_match.group(1), section_match.group(2).strip()
            continue

        chapter_match = CHAPTER_RE.match(line)
        if chapter_match and len(line) < 200:
            flush()
            current = None
            chapter, chapter_title = chapter_match.group(1), chapter_match.group(2).strip()
            paragraph_title = None
            continue

        paragraph_match = PARAGRAPH_RE.match(line)
        if paragraph_match and len(line) < 200:
            flush()
            current = None
            paragraph_title = paragraph_match.group(2).strip() or None
            continue

        article_match = ARTICLE_RE.match(line)
        if article_match and len(line) < 300:
            flush()
            number, title = article_match.group(1), article_match.group(2).strip()
            current = {
                "article": number,
                "title": title,
                "chapter": chapter,
                "chapter_title": chapter_title,
                "section": section,
                "section_title": section_title,
                "paragraph_title": paragraph_title,
                "position": position,
                "body": [],
            }
            continue

        if current is not None:
            current["body"].append(line)

    flush()

    # Одна и та же статья может встретиться в оглавлении и в теле кодекса —
    # оставляем вариант с самым содержательным телом.
    best: "OrderedDict[str, dict]" = OrderedDict()
    for block in blocks:
        body = "\n".join(block["body"]).strip()
        if len(body) < MIN_ARTICLE_BODY_CHARS:
            continue
        block["body_text"] = body
        key = block["article"]
        if key not in best or len(body) > len(best[key]["body_text"]):
            best[key] = block

    chunks: list[dict] = []
    for block in sorted(best.values(), key=lambda item: item.get("position", 0)):
        heading = f"Статья {block['article']}." + (f" {block['title']}" if block["title"] else "")
        parts = _split_long_body(block["body_text"])
        for index, part in enumerate(parts, 1):
            text_body = f"{heading}\n{part}"
            title_field = heading if block["title"] else f"{heading} ({code_label})"
            chunk = {
                "id": f"{source_name}:{block['article']}:{index}",
                "source": source_name,
                "code": code_label,
                "code_short": CODE_SHORT.get(source_name, code_label),
                "section": block["section"],
                "section_title": block["section_title"],
                "chapter": block["chapter"],
                "chapter_title": block["chapter_title"],
                "paragraph_title": block.get("paragraph_title"),
                "article": block["article"],
                "article_title": block["title"],
                "title": title_field,
                "part": index,
                "parts": len(parts),
                "text": text_body,
            }
            chunk["tokens"] = tokenize(text_body)
            chunk["token_count"] = len(chunk["tokens"])
            chunk["title_tokens"] = tokenize(
                " ".join(filter(None, [heading, block["chapter_title"], block.get("paragraph_title")]))
            )
            # Только собственное название статьи — основа для оценки покрытия.
            chunk["article_tokens"] = tokenize(block["title"])
            chunks.append(chunk)

    return [chunk for chunk in chunks if chunk["token_count"] >= 8]


def citation_for(chunk: dict) -> str:
    """Точная ссылка на норму, собранная из метаданных фрагмента."""
    if not chunk or not chunk.get("article"):
        return ""
    code = chunk.get("code") or chunk.get("source") or ""
    chapter = f", Глава {chunk['chapter']}" if chunk.get("chapter") else ""
    return f"{code}{chapter}, Статья {chunk['article']}"


# ============================================================================
# 5. ЛЕКСИЧЕСКИЙ ПОИСК (BM25 + поля + номера статей)
# ============================================================================

BM25_K1 = 1.5
BM25_B = 0.75
TITLE_WEIGHT = 2.2          # совпадение в названии статьи весит больше тела
TITLE_COVERAGE_BONUS = 9.0  # заголовок статьи целиком «про то, что спросили»
EXACT_TITLE_BONUS = 12.0    # запрос покрыл название статьи полностью
PHRASE_BONUS = 2.5          # точная фраза из запроса найдена в тексте
ARTICLE_HIT_BONUS = 14.0    # в запросе прямо назван номер статьи

_ARTICLE_QUERY_RE = re.compile(r"(?:стать[а-я]*|ст\.)\s*(\d+(?:[-–—]\d+)?(?:\.\d+)?)", re.IGNORECASE)
_PHRASE_RE = re.compile(r"[А-Яа-яЁёA-Za-z0-9][^.!?\n]{8,80}")


def build_stats(chunks: list[dict]) -> dict:
    """Считает IDF и среднюю длину документа для BM25."""
    doc_count = max(len(chunks), 1)
    df: Counter = Counter()
    for chunk in chunks:
        df.update(set(chunk.get("tokens", ())))
        df.update(set(chunk.get("title_tokens", ())))
    idf = {
        term: math.log(1 + (doc_count - freq + 0.5) / (freq + 0.5))
        for term, freq in df.items()
    }
    avgdl = sum(chunk.get("token_count", 0) for chunk in chunks) / doc_count
    return {"idf": idf, "avgdl": max(avgdl, 1.0)}


def _bm25(term_freq: Counter, length: int, query_terms: Counter, idf: dict, avgdl: float) -> float:
    score = 0.0
    for term, q_count in query_terms.items():
        freq = term_freq.get(term)
        if not freq:
            continue
        denom = freq + BM25_K1 * (1 - BM25_B + BM25_B * length / avgdl)
        score += idf.get(term, 0.1) * ((freq * (BM25_K1 + 1)) / denom) * min(q_count, 3)
    return score


def lexical_search(index: dict, query: str, limit: int = 60) -> list[tuple[float, dict]]:
    """BM25 по телу и названию статьи с маршрутизацией и бонусами."""
    chunks = index.get("chunks") or []
    if not chunks:
        return []

    expanded = expand_query(query)
    query_terms = Counter(tokenize(expanded))
    asked_articles = set(_ARTICLE_QUERY_RE.findall(query or ""))
    if not query_terms and not asked_articles:
        return []

    idf = index.get("idf", {})
    avgdl = max(float(index.get("avgdl", 1.0)), 1.0)
    routing = route_codes(query)
    lowered = (query or "").lower()
    phrases = [p.strip() for p in _PHRASE_RE.findall(lowered) if len(p.strip()) >= 9]

    scored: list[tuple[float, dict]] = []
    for chunk in chunks:
        tokens = chunk.get("tokens") or ()
        if not tokens:
            continue
        body_tf = Counter(tokens)
        length = max(chunk.get("token_count", len(tokens)), 1)
        score = _bm25(body_tf, length, query_terms, idf, avgdl)

        title_tokens = chunk.get("title_tokens") or ()
        if title_tokens:
            title_tf = Counter(title_tokens)
            score += TITLE_WEIGHT * _bm25(title_tf, max(len(title_tokens), 1), query_terms, idf, avgdl)

        # Ключевой сигнал для юридического поиска: статья «Рабочее время»
        # должна выигрывать у статьи «Порядок ведения учёта рабочего времени»
        # на вопрос «что считается рабочим временем». Меряем, какую долю
        # собственного заголовка статьи покрыл запрос.
        article_terms = chunk.get("article_tokens") or ()
        if article_terms and query_terms:
            unique = frozenset(article_terms)
            covered = unique & query_terms.keys()
            if covered:
                coverage = len(covered) / len(unique)
                score += TITLE_COVERAGE_BONUS * (coverage ** 2)
                # Полное покрытие короткого названия — сигнал сильнее, чем
                # любой объём совпадений в теле длинной смежной статьи.
                if coverage == 1.0 and len(unique) <= 4:
                    score += EXACT_TITLE_BONUS

        if phrases:
            text_lower = chunk.get("text", "").lower()
            for phrase in phrases:
                if phrase in text_lower:
                    score += PHRASE_BONUS

        if asked_articles and chunk.get("article") in asked_articles:
            score += ARTICLE_HIT_BONUS

        if score <= 0:
            continue
        score *= routing.get(chunk.get("source"), 1.0)
        scored.append((score, chunk))

    scored.sort(key=lambda item: item[0], reverse=True)
    return scored[:limit]


# ============================================================================
# 6. ВЕКТОРНЫЙ СЛОЙ (опциональный)
# ============================================================================

EMBED_MODEL = os.environ.get("RAG_EMBED_MODEL", "nomic-embed-text")
EMBED_URL = os.environ.get("RAG_EMBED_URL", "http://localhost:11434/api/embed")
EMBED_TIMEOUT = int(os.environ.get("RAG_EMBED_TIMEOUT", "120"))
EMBED_BATCH = int(os.environ.get("RAG_EMBED_BATCH", "32"))


class EmbeddingProvider:
    """Обёртка над эмбеддингами Ollama с мягкой деградацией.

    Если модель не установлена или демон не отвечает, провайдер сообщает
    об этом один раз и молча отключается: поиск продолжает работать
    в лексическом режиме.
    """

    # Проба короткая: она выполняется на пути пользовательского запроса,
    # и лишние секунды здесь — это задержка ответа в чате.
    PROBE_TIMEOUT = int(os.environ.get("RAG_EMBED_PROBE_TIMEOUT", "5"))
    # Отрицательный результат кэшируется, но не навсегда: Ollama могла быть
    # занята индексацией, и после перезапуска демона слой должен вернуться.
    RETRY_AFTER_SECONDS = 600

    def __init__(self, model: str = EMBED_MODEL, url: str = EMBED_URL):
        self.model = model
        self.url = url
        self._state: bool | None = None
        self._checked_at = 0.0
        self._reason = ""

    @property
    def reason(self) -> str:
        return self._reason

    def available(self) -> bool:
        import time

        if self._state is True:
            return True
        if self._state is False and time.time() - self._checked_at < self.RETRY_AFTER_SECONDS:
            return False

        self._checked_at = time.time()
        try:
            import requests  # локальный импорт: модуль обязан грузиться без сети
            response = requests.post(
                self.url,
                json={"model": self.model, "input": "проверка"},
                timeout=self.PROBE_TIMEOUT,
            )
            self._state = response.status_code == 200 and bool(
                (response.json() or {}).get("embeddings")
            )
            if not self._state:
                self._reason = f"HTTP {response.status_code}"
        except Exception as error:  # noqa: BLE001 — причина уходит в статус
            self._state = False
            self._reason = str(error)
        return self._state

    def embed(self, texts: list[str]) -> list[list[float]] | None:
        if not self.available():
            return None
        import requests
        vectors: list[list[float]] = []
        for start in range(0, len(texts), EMBED_BATCH):
            batch = texts[start:start + EMBED_BATCH]
            try:
                response = requests.post(
                    self.url,
                    json={"model": self.model, "input": batch},
                    timeout=EMBED_TIMEOUT,
                )
                response.raise_for_status()
                vectors.extend(response.json().get("embeddings") or [])
            except Exception as error:  # noqa: BLE001
                self._reason = str(error)
                return None
        return vectors if len(vectors) == len(texts) else None


def embeddings_path(index_path: str) -> str:
    return os.path.splitext(index_path)[0] + ".emb.npz"


def save_embeddings(index_path: str, ids: list[str], vectors) -> str:
    """Векторы хранятся отдельно во float16: JSON раздул бы индекс в разы."""
    import numpy as np
    matrix = np.asarray(vectors, dtype="float32")
    norms = np.linalg.norm(matrix, axis=1, keepdims=True)
    matrix = matrix / np.clip(norms, 1e-9, None)
    path = embeddings_path(index_path)
    np.savez_compressed(
        path,
        ids=np.array(ids, dtype=object),
        vectors=matrix.astype("float16"),
        model=np.array([EMBED_MODEL], dtype=object),
    )
    return path


def load_embeddings(index_path: str):
    path = embeddings_path(index_path)
    if not os.path.isfile(path):
        return None
    try:
        import numpy as np
        data = np.load(path, allow_pickle=True)
        return {
            "ids": list(data["ids"]),
            "vectors": np.asarray(data["vectors"], dtype="float32"),
            "model": str(data["model"][0]) if "model" in data else EMBED_MODEL,
        }
    except Exception as error:  # noqa: BLE001
        print(f"RAG embeddings load skipped: {error}")
        return None


def vector_search(store: dict, provider: EmbeddingProvider, query: str, limit: int = 60):
    """Возвращает [(cosine, chunk_id)] или None, если слой недоступен."""
    if not store or not provider.available():
        return None
    vectors = provider.embed([query])
    if not vectors:
        return None
    import numpy as np
    q = np.asarray(vectors[0], dtype="float32")
    norm = float(np.linalg.norm(q))
    if norm < 1e-9:
        return None
    q /= norm
    scores = store["vectors"] @ q
    top = np.argsort(-scores)[:limit]
    return [(float(scores[i]), store["ids"][i]) for i in top]


# ============================================================================
# 7. ГИБРИДНОЕ СЛИЯНИЕ
# ============================================================================

RRF_K = 60
# Порог косинуса, ниже которого чисто семантическое совпадение в выдачу
# не пускается. Подобран так, чтобы отсекать «тематически похожие», но
# юридически посторонние статьи.
VECTOR_ONLY_MIN_SCORE = 0.62


def fuse(lexical: list[tuple[float, dict]], vector, top_k: int, resolve=None) -> list[dict]:
    """Reciprocal Rank Fusion.

    RRF выбран вместо взвешенной суммы намеренно: BM25 и косинус живут
    в разных шкалах, и любая нормализация делает вес зависимым от того,
    насколько «удачной» оказалась конкретная выдача.

    `resolve` отдаёт фрагмент по его id. Без него векторный слой мог бы
    только переранжировать лексических кандидатов — то есть ровно в том
    случае, ради которого он нужен (запрос перефразирован и BM25 ничего
    не нашёл), он не давал бы ничего.
    """
    ranks: dict[str, float] = {}
    payload: dict[str, dict] = {}

    for position, (score, chunk) in enumerate(lexical, 1):
        chunk_id = chunk.get("id")
        ranks[chunk_id] = ranks.get(chunk_id, 0.0) + 1.0 / (RRF_K + position)
        payload[chunk_id] = dict(chunk, lexical_score=round(score, 4))

    if vector:
        for position, (score, chunk_id) in enumerate(vector, 1):
            if chunk_id not in payload:
                # Семантическое попадание без лексического пересечения.
                # Пускаем его в выдачу только при уверенном косинусе:
                # для юридического поиска ложная норма дороже пропуска.
                if resolve is None or score < VECTOR_ONLY_MIN_SCORE:
                    continue
                chunk = resolve(chunk_id)
                if chunk is None:
                    continue
                payload[chunk_id] = dict(chunk)
                ranks[chunk_id] = 0.0
            ranks[chunk_id] += 1.0 / (RRF_K + position)
            payload[chunk_id]["vector_score"] = round(score, 4)

    ordered = sorted(ranks.items(), key=lambda item: item[1], reverse=True)
    results = []
    for chunk_id, rank_score in ordered[:top_k]:
        item = payload[chunk_id]
        item["score"] = round(rank_score * 1000, 4)
        item["citation"] = citation_for(item)
        results.append(item)
    return results


def search(index: dict, query: str, top_k: int = 8, store=None, provider=None) -> list[dict]:
    """Точка входа: гибридный поиск с деградацией до лексического."""
    lexical = lexical_search(index, query, limit=max(top_k * 6, 48))
    if not lexical:
        return []
    vector = None
    resolve = None
    if store is not None and provider is not None:
        try:
            vector = vector_search(store, provider, expand_query(query), limit=max(top_k * 6, 48))
            if vector:
                by_id = index.get("_by_id")
                if by_id is None:
                    by_id = {chunk.get("id"): chunk for chunk in index.get("chunks") or ()}
                    index["_by_id"] = by_id
                resolve = by_id.get
        except Exception as error:  # noqa: BLE001
            print(f"RAG vector search skipped: {error}")
            vector = None
    return fuse(lexical, vector, top_k, resolve=resolve)


__all__ = [
    "INDEX_VERSION",
    "RussianStemmer",
    "tokenize",
    "expand_query",
    "route_codes",
    "build_article_chunks",
    "build_stats",
    "citation_for",
    "lexical_search",
    "search",
    "fuse",
    "EmbeddingProvider",
    "load_embeddings",
    "save_embeddings",
    "embeddings_path",
    "normalize_document_text",
    "CODE_SHORT",
    "CODE_HINTS",
]
