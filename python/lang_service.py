# -*- coding: utf-8 -*-
"""Определение языка вопроса и перевод ru<->tg для чата.

Слой существует потому, что локальная qwen3:8b таджикский не держит: на прямой
вопрос она срывается в повтор одного токена («шартномаи иштирокчон ба шартномаи
иштирокчони дигар...») и выдаёт текст без смысла. Проверено и на генерации, и на
переводе готового русского ответа — промптом это не лечится.

Поэтому ответ всегда рождается по-русски, на языке корпуса кодексов РТ, а
таджикский получается отдельной моделью NLLB-200. Она официально поддерживает
tgk_Cyrl, а через CTranslate2 в int8 занимает ~600 МБ вместо ~2.4 ГБ у torch —
на машине без дискретной GPU это разница между «работает» и «своп».

Деградация мягкая, как у EmbeddingProvider в legal_rag: если модель не скачана
или не грузится, переводчик отключается и чат продолжает отвечать по-русски.
Молча ронять запрос в юридической системе нельзя — пользователь должен видеть,
что ответ пришёл не на том языке, о котором он просил.
"""

import os
import re
import threading

PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))

DEFAULT_LANG = os.environ.get("CHAT_DEFAULT_LANG", "ru")
SUPPORTED_LANGS = ("ru", "tg")

TRANSLATE_MODEL_DIR = os.environ.get(
    "TG_TRANSLATE_MODEL_DIR",
    os.path.join(PROJECT_ROOT, "storage", "app", "models", "nllb-200-distilled-600M-ct2-int8"),
)
TRANSLATE_DEVICE = os.environ.get("TG_TRANSLATE_DEVICE", "cpu")
TRANSLATE_THREADS = int(os.environ.get("TG_TRANSLATE_THREADS", "4"))
TRANSLATE_BEAM = int(os.environ.get("TG_TRANSLATE_BEAM", "2"))
TRANSLATE_MAX_INPUT = int(os.environ.get("TG_TRANSLATE_MAX_INPUT", "400"))

NLLB_CODES = {"ru": "rus_Cyrl", "tg": "tgk_Cyrl"}

# ============================================================================
# Определение языка
# ============================================================================

# Букв ӣ ӯ ҳ қ ғ ҷ нет в русском алфавите, а ы щ ь ц — в таджикском. Одна такая
# буква говорит о языке больше, чем десяток служебных слов, поэтому вес выше.
# ъ намеренно не в списке: он есть в обоих алфавитах.
TAJIK_LETTERS = set("ӣӯҳқғҷ")
RUSSIAN_ONLY_LETTERS = set("ыщьц")

# Пользователи часто пишут по-таджикски без диакритики («чи тавр» вместо «чӣ
# тавр»), и тогда буквенный признак молчит. Служебные слова остаются.
TAJIK_STOPWORDS = {
    "ва", "ба", "аз", "бо", "ки", "дар", "барои", "аст", "буд", "мебошад",
    "чи", "чӣ", "чист", "чанд", "кадом", "куҷо", "кучо", "кай", "оё",
    "ин", "ҳам", "хам", "то", "бояд", "метавонад", "мешавад",
    "мекунад", "карда", "шуда", "шудан", "кардан", "гуфта", "тавр",
    "ҳуқуқ", "хукук", "қонун", "конун", "модда", "моддаи", "шартнома",
    "шартномаи", "меҳнат", "мехнат", "судӣ", "ман", "шумо", "мо",
    # Приветствия и вежливые формулы: часто это вообще всё сообщение, и других
    # признаков языка в нём нет. Без них «салом» уходило в русскую ветку.
    "салом", "ассалом", "ассалому", "алейкум", "ваалейкум", "рахмат", "раҳмат",
    "ташаккур", "хайр", "хуш", "лутфан", "бубахшед", "марҳамат", "хуб",
}

RUSSIAN_STOPWORDS = {
    "и", "в", "на", "с", "по", "для", "что", "как", "это", "не", "или",
    "при", "от", "до", "если", "чтобы", "можно", "нужно", "какие", "какой",
    "который", "мне", "меня", "вы", "мы", "есть", "был", "была", "быть",
    "договор", "договора", "статья", "статьи", "закон", "закона", "право",
    "права", "суд", "суда", "работник", "работодатель", "кодекс", "кодекса",
}

# Приветствие — не юридический вопрос. Без этой проверки «салом» уходило в
# BM25, цеплялось за «салам» (термин исламского финансирования) в Налоговом
# кодексе и разворачивалось в пересказ статьи 206 про доходы банков.
SMALLTALK_WORDS = {
    "салом", "ассалом", "ассалому", "алейкум", "ваалейкум", "рахмат", "раҳмат",
    "ташаккур", "хайр", "хуш", "бубахшед", "марҳамат", "чӣ", "хел", "аҳвол",
    "привет", "здравствуйте", "здравствуй", "добрый", "доброе", "день",
    "вечер", "утро", "спасибо", "благодарю", "пока", "ок", "окей",
    "hi", "hello", "hey", "thanks", "ok",
}

_WORD_RE = re.compile(r"[^\W\d_]+", re.UNICODE)
_CYRILLIC_RE = re.compile(r"[а-яёӣӯҳқғҷ]", re.IGNORECASE)

# Разговорная речь без диакритики раньше не находила себе стоп-слова: список
# выше хранит словарные формы («хукук»), а в сообщении приходит словоформа
# («хукуки», «хукуку»). Точное сравнение эту пару не видело, счёт оставался
# 0:0, и detect_lang откатывался на default — которым фронт передаёт язык
# интерфейса страницы, а не язык вопроса. Порог в 5 букв — граница, ниже
# которой у языка почти нет содержательных стоп-слов и совпадение начала
# скорее случайность (короткие служебные «ва», «ба», «аз» сравниваются
# только точно).
_TAJIK_STEMS = tuple(word for word in TAJIK_STOPWORDS if len(word) >= 5)
_RUSSIAN_STEMS = tuple(word for word in RUSSIAN_STOPWORDS if len(word) >= 5)


def _stopword_score(word, exact_set, stems):
    if word in exact_set:
        return 1.0
    if len(word) >= 5 and any(word.startswith(stem) for stem in stems):
        return 1.0
    return 0.0


def is_smalltalk(text):
    """True для коротких приветствий и благодарностей без юридического вопроса.

    Порог в три слова намеренно жёсткий: пропустить приветствие в RAG не
    страшно, а вот принять за приветствие короткий вопрос вроде «срок
    договора» и остаться без опоры на закон — уже ошибка по существу.
    """
    words = _WORD_RE.findall((text or "").lower())
    if not words or len(words) > 3:
        return False
    return all(word in SMALLTALK_WORDS for word in words)


def detect_lang(text, default=None):
    """Возвращает 'ru' или 'tg' по тексту вопроса.

    Латиница и пустая строка отдают язык по умолчанию: английский чат не
    поддерживается, а гадать по трём символам смысла нет.
    """
    fallback = default or DEFAULT_LANG
    if not text or not text.strip():
        return fallback

    lowered = text.lower()
    if not _CYRILLIC_RE.search(lowered):
        return fallback

    letters = set(lowered)
    tg_score = 4.0 * len(letters & TAJIK_LETTERS)
    ru_score = 4.0 * len(letters & RUSSIAN_ONLY_LETTERS)

    for word in _WORD_RE.findall(lowered):
        tg_score += _stopword_score(word, TAJIK_STOPWORDS, _TAJIK_STEMS)
        ru_score += _stopword_score(word, RUSSIAN_STOPWORDS, _RUSSIAN_STEMS)

    if tg_score == ru_score:
        return fallback
    return "tg" if tg_score > ru_score else "ru"


def detect_lang_details(text, default=None):
    """Та же логика, но с разбором признаков — нужна тестам и диагностике."""
    lowered = (text or "").lower()
    letters = set(lowered)
    words = _WORD_RE.findall(lowered)
    return {
        "lang": detect_lang(text, default),
        "tajik_letters": sorted(letters & TAJIK_LETTERS),
        "russian_letters": sorted(letters & RUSSIAN_ONLY_LETTERS),
        "tajik_words": sorted({w for w in words if _stopword_score(w, TAJIK_STOPWORDS, _TAJIK_STEMS)}),
        "russian_words": sorted({w for w in words if _stopword_score(w, RUSSIAN_STOPWORDS, _RUSSIAN_STEMS)}),
    }


# ============================================================================
# Защита юридических реквизитов от перевода
# ============================================================================

# Маскируется только структурная строка ссылки: она должна дойти дословно.
#
# Ссылки вида «статья 41» через маску не прогоняются, хотя так и было сначала.
# Замер на восьми фразах показал, что маска решает не ту задачу: NLLB иногда
# просто выбрасывает плейсхолдер, и «статье 41» исчезала из ответа целиком —
# для юридической системы это худший из возможных исходов. А там, где маска
# срабатывала, в таджикскую фразу вклинивались русские слова («Статья 1 ва
# статья 250»).
#
# Без маски модель даёт нормальные таджикские термины («моддаи 41», «боби 62»)
# и числа переносит верно; единственный наблюдавшийся сбой — переписывание
# числа словами («5000 сомони» -> «5 ҳазор сомонӣ»). Это ловится сверкой чисел
# после перевода, поэтому такой путь и выбран.
#
# Аббревиатуры кодексов маскируются: переводить их не нужно, а NLLB на них
# фантазирует — «ТК РТ» выходило то «Кодекси давлатии меҳнат» («государственный
# кодекс труда»), то просто «ТТ». Номер статьи при этом оставался цел, но
# ссылка на несуществующий кодекс не лучше искажённого номера. Русская
# аббревиатура внутри таджикской фразы однозначна и совпадает с тем, что
# написано в самом кодексе.
_PROTECT_PATTERNS = [
    re.compile(r"ПРОВЕРЕННАЯ ССЫЛКА RAG:[^\n]*"),
    # Все кодексы, по которым ищет чат (см. CODE_SHORT в legal_rag): раньше
    # маскировались только шесть, а «ЭПК РТ» или «КАТ РТ» NLLB превращала в «ТТ».
    re.compile(r"\b(?:ТК|ГК|НК|СК|УК|ГПК|АПК|КоАПр|КоАП|ПКоАП|ЭПК|КАТ|ВдК|ВздК|ГрК|ЖК|КЗ|ЛК|УПК)\s*РТ\b"),
]

# ---------------------------------------------------------------------------
# Подготовка русского текста к переводу на таджикский и правка результата
# ---------------------------------------------------------------------------
# Всё ниже проверено замером на живых фразах ответов чата (NLLB-200, CPU):
#
# - «ст. 67» без раскрытия перевод иногда читал как «стр.» и выдавал
#   «саҳифа 67» (страница), а «ч. 1 ст. 327» превращалось в «моддаи 1 327»,
#   то есть часть статьи терялась. «статья 67» / «часть 1 статьи 327» NLLB
#   переводит стабильно: «моддаи 67», «қисми 1 моддаи 327».
# - Длинное тире переводчик выбрасывал вместе с логикой фразы: «срок — три
#   года» выходило «мӯҳлат  се сол» с двойным пробелом. Дефис он сохраняет.
# - «неустойка» без контекста теряется («ноустуворӣ» — «неустойчивость»,
#   «бепарвоӣ» — «беспечность»). В самом ГК РТ она определена как «штраф, пеня»,
#   а «штраф» NLLB даёт стабильно: «ҷарима».
#
# Всё это касается только направления ru -> tg; русский текст в истории диалога
# и в промпте не меняется.

_ABBREV_EXPANSIONS = (
    (re.compile(r"\bч\.\s*(\d+)\s*ст\.\s*(\d+)"), r"часть \1 статьи \2"),
    (re.compile(r"\bст\.\s*ст\.\s*(?=\d)"), "статьи "),
    (re.compile(r"\bст\.\s*(?=\d)"), "статья "),
    (re.compile(r"\bч\.\s*(?=\d)"), "часть "),
    (re.compile(r"\bп\.\s*(?=\d)"), "пункт "),
    (re.compile(r"\bгл\.\s*(?=\d)"), "глава "),
)
_PENALTY_RE = re.compile(r"\b[Нн]еустойк(?:а|у|и|е|ой|ою)\b|\b[Нн]еустоек\b")


def prepare_for_tajik(text):
    """Раскрывает сокращения ссылок и подменяет слова, которые NLLB теряет."""
    for pattern, replacement in _ABBREV_EXPANSIONS:
        text = pattern.sub(replacement, text)
    text = text.replace(" \u2014 ", " - ")
    return _PENALTY_RE.sub("штраф", text)


_TG_LETTERS = "А-Яа-яЁёӢӣӮӯҚқҲҳҶҷҒғ"
_TG_FIXES = (
    # «Манбаъ: ТК РТ, саҳифа 67» — в ответе о законе страниц не бывает.
    (re.compile(r"саҳифа(?:и)?\s*(?=\d)"), "моддаи "),
    # Так называется кодекс в самом интерфейсе («Кодекси граждании ҶТ»).
    (re.compile(r"Кодекси\s+шаҳрванд\w*"), "Кодекси граждании"),
    # «РТ» без кодекса перед ним NLLB иногда пишет как «ТР» или «ТТ».
    (re.compile(r"(?<![%s])(?:ТР|ТТ)(?![%s])" % (_TG_LETTERS, _TG_LETTERS)), "ҶТ"),
    (re.compile(r"[ \t]{2,}"), " "),
    (re.compile(r"[ \t]+([,.;:!?])"), r"\1"),
)


def polish_tajik(text):
    """Исправляет повторяющиеся сбои перевода, которые видны без словаря."""
    for pattern, replacement in _TG_FIXES:
        text = pattern.sub(replacement, text)
    return text.strip()


# Название части документа, которое дописывает repair_numbers, должно быть
# таджикским: «Глава 13, Статья 81» в таджикском тексте выглядит как сбой.
_CONTEXT_WORDS = (
    (re.compile(r"стат[а-яё]*|ст\.", re.I), "моддаи"),
    (re.compile(r"глав[а-яё]*|гл\.", re.I), "боби"),
    (re.compile(r"част[а-яё]*|ч\.", re.I), "қисми"),
    (re.compile(r"пункт[а-яё]*|п\.", re.I), "банди"),
)


def localize_citation(text, lang):
    if lang != "tg":
        return text
    for pattern, word in _CONTEXT_WORDS:
        text = pattern.sub(word, text)
    return text


_NUMBER_RE = re.compile(r"\d+(?:[.,]\d+)*")
_CITATION_CONTEXT_RE = re.compile(
    r"(?:стат[а-яё]*|ст\.|глав[а-яё]*|гл\.|част[а-яё]*|ч\.|пункт[а-яё]*|п\.)\s*\d+(?:[-.]\d+)*",
    re.IGNORECASE,
)


# Машинный перевод плавает в юридическом регистре: «бекор карда мешавад» NLLB
# отдаёт как «отменить», хотя в кодексах РТ это «расторжение» и «прекращение».
# Из-за одного слова вопрос о трудовом договоре поднимал в топ статью 141
# Семейного кодекса — об отмене усыновления.
#
# Формулировка кодекса дописывается к запросу, а не заменяет перевод: лишний
# термин BM25 переживёт, а подмена смысла — нет. Править словарь синонимов в
# legal_rag было бы хуже: «отмена усыновления» и «отмена доверенности» —
# законные термины, и связывать «отмену» с «расторжением» глобально нельзя.
_QUERY_HINTS = (
    (("отмен", "договор"), "расторжение прекращение договора"),
    (("аннулир", "договор"), "расторжение прекращение договора"),
    (("отмен", "брак"), "расторжение брака"),
)


def normalize_search_query(query):
    """Дополняет машинный перевод формулировками, принятыми в кодексах."""
    lowered = (query or "").lower()
    additions = []
    for triggers, hint in _QUERY_HINTS:
        if all(trigger in lowered for trigger in triggers) and hint not in additions:
            additions.append(hint)
    if not additions:
        return query
    return "%s %s" % (query, " ".join(additions))


def lost_numbers(source, translated):
    """Числа исходника, которых нет в переводе, с учётом повторов."""
    available = _NUMBER_RE.findall(translated or "")
    lost = []
    for number in _NUMBER_RE.findall(source or ""):
        if number in available:
            available.remove(number)
        else:
            lost.append(number)
    return lost


def repair_numbers(source, translated, lang="ru"):
    """Дописывает реквизиты, которые переводчик потерял или переписал словами.

    Приписка в скобках выглядит грубее гладкой фразы, но ответ, где номер
    статьи молча исчез, юридически хуже: пользователь не может проверить норму.
    """
    lost = lost_numbers(source, translated)
    if not lost:
        return translated

    contexts = []
    for citation in _CITATION_CONTEXT_RE.findall(source or ""):
        if any(number in citation for number in lost) and citation not in contexts:
            contexts.append(citation)

    covered = " ".join(contexts)
    tail = [number for number in lost if number not in covered]
    parts = [localize_citation(part, lang) for part in contexts + tail]
    if not parts:
        return translated
    return "%s (%s)" % (translated.rstrip(), ", ".join(parts))

# Номер замены кодируется буквами, а не цифрами. С цифрами последний шаблон
# («любое число») находил бы индекс внутри уже поставленного маркера и
# заворачивал его во второй: «статье 45» превращалось в @@2@@ и назад не
# разворачивалось.
_PLACEHOLDER_ALPHABET = "ABCDEFGHIJ"
_PLACEHOLDER_RE = re.compile(r"@\s*([A-J]+)\s*@")


def _encode_index(index):
    return "@%s@" % "".join(_PLACEHOLDER_ALPHABET[int(digit)] for digit in str(index))


def _decode_index(code):
    return int("".join(str(_PLACEHOLDER_ALPHABET.index(letter)) for letter in code))


def protect_citations(text):
    """Заменяет реквизиты плейсхолдерами. Возвращает (текст, список замен)."""
    saved = []

    def _stash(match):
        saved.append(match.group(0))
        return _encode_index(len(saved) - 1)

    for pattern in _PROTECT_PATTERNS:
        text = pattern.sub(_stash, text)
    return text, saved


def restore_citations(text, saved):
    """Возвращает реквизиты на место.

    Пробелы вокруг маркера модель может добавить сама, поэтому регулярка их
    допускает. Потерянный моделью плейсхолдер восстановить нельзя — такие
    случаи ловит has_lost_citations.
    """
    if not saved:
        return text

    def _pop(match):
        try:
            index = _decode_index(match.group(1))
        except ValueError:
            return match.group(0)
        return saved[index] if 0 <= index < len(saved) else match.group(0)

    return _PLACEHOLDER_RE.sub(_pop, text)


def has_lost_citations(translated, saved):
    """True, если перевод потерял хотя бы один защищённый реквизит."""
    return any(item not in translated for item in saved)


def repair_citations(translated, saved):
    """Дописывает защищённые фрагменты, которые переводчик выбросил вместе с маркером."""
    lost = [item for item in saved if item not in translated]
    if not lost:
        return translated
    return "%s (%s)" % (translated.rstrip(), ", ".join(lost))


# ============================================================================
# Переводчик
# ============================================================================

# NLLB обучалась на предложениях: на длинном абзаце качество падает, а на
# нескольких абзацах разом она начинает пропускать куски. Поэтому текст всегда
# режется по границам предложений — заодно это позволяет отдавать перевод в
# чат по мере готовности, не дожидаясь конца ответа.
_SENTENCE_END_RE = re.compile(r"(?<=[.!?:;])\s+|\n+")


def split_sentences(text):
    parts = [part.strip() for part in _SENTENCE_END_RE.split(text or "")]
    return [part for part in parts if part]


# Чат рендерит ответ через marked.parse, то есть в тексте живут маркеры списка,
# заголовки и выделение. Маркер строки NLLB перевела бы как слово, поэтому он
# отрезается и приклеивается обратно уже к переводу. Звёздочки выделения,
# наоборот, выбрасываются: переводчик переставляет слова, и восстановленная
# пара «**» с большой вероятностью обняла бы не тот фрагмент.
_LINE_PREFIX_RE = re.compile(r"^(\s*(?:[-*•+]\s+|\d+[.)]\s+|#{1,6}\s+|>\s+)?)")
_EMPHASIS_RE = re.compile(r"\*\*|__|(?<!\*)\*(?!\*)|(?<!`)`(?!`)")


def split_line_prefix(line):
    """Делит строку на markdown-маркер и содержательную часть."""
    match = _LINE_PREFIX_RE.match(line or "")
    prefix = match.group(1) if match else ""
    return prefix, (line or "")[len(prefix):]


def strip_emphasis(text):
    return _EMPHASIS_RE.sub("", text or "")


class Translator:
    """CTranslate2 + NLLB-200 с ленивой загрузкой и мягкой деградацией."""

    def __init__(self, model_dir=TRANSLATE_MODEL_DIR):
        self.model_dir = model_dir
        self._translator = None
        self._tokenizer = None
        self._state = None
        self._reason = ""
        self._lock = threading.Lock()

    @property
    def reason(self):
        return self._reason

    @property
    def loaded(self):
        return self._state is True

    def is_installed(self):
        """Проверка наличия модели без её загрузки.

        Нужна /api/rag/status: он дёргается при каждом открытии чата, и
        поднимать там 600 МБ ради галочки в шапке нельзя.
        """
        return os.path.isfile(os.path.join(self.model_dir, "model.bin"))

    def available(self):
        if self._state is not None:
            return self._state
        # Загрузка занимает секунды и идёт на пути пользовательского запроса:
        # без блокировки два одновременных чата подняли бы модель дважды.
        with self._lock:
            if self._state is None:
                self._state = self._load()
        return self._state

    def _load(self):
        if not os.path.isdir(self.model_dir):
            self._reason = "модель не найдена: %s" % self.model_dir
            return False
        try:
            import ctranslate2
            from transformers import AutoTokenizer

            self._translator = ctranslate2.Translator(
                self.model_dir,
                device=TRANSLATE_DEVICE,
                compute_type="int8",
                inter_threads=1,
                intra_threads=TRANSLATE_THREADS,
            )
            self._tokenizer = AutoTokenizer.from_pretrained(self.model_dir)
            return True
        except Exception as error:  # noqa: BLE001 — причина уходит в статус
            self._reason = "%s: %s" % (type(error).__name__, error)
            self._translator = None
            self._tokenizer = None
            return False

    def translate(self, texts, src, tgt):
        """Переводит список строк. None — слой недоступен, вызывающий решает сам."""
        texts = list(texts)
        if not texts:
            return []
        if src == tgt:
            return texts
        if src not in NLLB_CODES or tgt not in NLLB_CODES:
            return None
        if not self.available():
            return None

        prepared = []
        stashes = []
        to_tajik = src == "ru" and tgt == "tg"
        for text in texts:
            clean, saved = protect_citations(text or "")
            prepared.append(prepare_for_tajik(clean) if to_tajik else clean)
            stashes.append(saved)

        try:
            self._tokenizer.src_lang = NLLB_CODES[src]
            batch = [
                self._tokenizer.convert_ids_to_tokens(self._tokenizer.encode(text))
                for text in prepared
            ]
            results = self._translator.translate_batch(
                batch,
                target_prefix=[[NLLB_CODES[tgt]]] * len(batch),
                beam_size=TRANSLATE_BEAM,
                max_input_length=TRANSLATE_MAX_INPUT,
                # Повтор одного токена — типичный срыв seq2seq на низкоресурсном
                # языке. Штраф и запрет 4-грамм гасят его до того, как он попадёт
                # пользователю; ровно так ломалась qwen3 на таджикском.
                repetition_penalty=1.15,
                no_repeat_ngram_size=4,
            )
        except Exception as error:  # noqa: BLE001
            self._reason = "%s: %s" % (type(error).__name__, error)
            return None

        output = []
        for source, result, saved in zip(texts, results, stashes):
            tokens = list(result.hypotheses[0])
            if tokens and tokens[0] == NLLB_CODES[tgt]:
                tokens = tokens[1:]
            decoded = self._tokenizer.decode(
                self._tokenizer.convert_tokens_to_ids(tokens),
                skip_special_tokens=True,
            )
            restored = repair_citations(restore_citations(decoded.strip(), saved), saved)
            if to_tajik:
                restored = polish_tajik(restored)
            output.append(repair_numbers(source or "", restored, tgt))
        return output

    def translate_text(self, text, src, tgt):
        """Перевод многопредложенного текста. None — слой недоступен."""
        if src == tgt:
            return text
        sentences = split_sentences(text)
        if not sentences:
            return text
        translated = self.translate(sentences, src, tgt)
        if translated is None:
            return None
        return " ".join(translated)

    def translate_block(self, block, src, tgt):
        """Перевод куска ответа с сохранением markdown-разметки строк.

        Пустые строки сохраняются как есть: на них держится разбиение на
        абзацы и списки, а переводить их нечего.
        """
        if src == tgt:
            return block
        lines = (block or "").split("\n")
        payload = []
        slots = []
        for index, line in enumerate(lines):
            prefix, body = split_line_prefix(line)
            body = strip_emphasis(body).strip()
            if not body:
                continue
            slots.append((index, prefix))
            payload.append(body)

        if not payload:
            return block

        translated = self.translate(payload, src, tgt)
        if translated is None:
            return None

        for (index, prefix), text in zip(slots, translated):
            lines[index] = prefix + text
        return "\n".join(lines)


translator = Translator()

__all__ = [
    "detect_lang",
    "detect_lang_details",
    "is_smalltalk",
    "split_sentences",
    "split_line_prefix",
    "strip_emphasis",
    "protect_citations",
    "restore_citations",
    "has_lost_citations",
    "lost_numbers",
    "repair_numbers",
    "prepare_for_tajik",
    "polish_tajik",
    "localize_citation",
    "normalize_search_query",
    "Translator",
    "translator",
    "SUPPORTED_LANGS",
    "NLLB_CODES",
    "DEFAULT_LANG",
]
