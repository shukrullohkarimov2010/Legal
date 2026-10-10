# -*- coding: utf-8 -*-
"""Диалог перед составлением документа: какой договор и с кем.

Просьба «составь договор» без подробностей раньше сразу давала текст с
заглушками. Хороший юрист сначала выясняет, что за договор и с кем он
заключается. Здесь это сделано как цепочка коротких вопросов:

    договор:   вид договора  ->  вторая сторона
    иск, претензия, доверенность, расписка:  суть  ->  вторая сторона
    заявление, жалоба:  суть

Вопрос задаётся, только если ответа нет в самой просьбе: «составь договор
аренды с Ахмедовым» сразу превращается в документ. Каждый вопрос задаётся
не больше одного раза, а пропуск («стороны пока не определены») считается
ответом, так что диалог не зацикливается.

Состояние диалога сервер не хранит: оно уходит странице вместе с карточкой и
возвращается с ответом пользователя. Поэтому оно считается недоверенным и
проходит проверку (`sanitize_state`).

Модуль не ходит ни в модель, ни в RAG.
"""

from __future__ import annotations

import re
from dataclasses import dataclass
from typing import Callable, Optional

import chat_modes as cm

STATE_VERSION = 1
MAX_TEXT = 500
MAX_OPTIONS = 10

SLOT_TYPE, SLOT_TOPIC, SLOT_PARTY = "type", "topic", "party"
_SLOTS = (SLOT_TYPE, SLOT_TOPIC, SLOT_PARTY)

# (ключ для определителя вида, подпись на русском, подпись на таджикском)
CONTRACT_TYPES = (
    ("купли-продажи", "Купли-продажи", "Хариду фурӯш"),
    ("аренды", "Аренды", "Иҷора"),
    ("поставки", "Поставки", "Таъминот"),
    ("подряда", "Подряда", "Пудрат"),
    ("оказания услуг", "Оказания услуг", "Хизматрасонӣ"),
    ("займа", "Займа", "Қарз"),
    ("дарения", "Дарения", "Ҳадя"),
    ("трудовой", "Трудовой договор", "Шартномаи меҳнатӣ"),
)

# Вторая сторона. Для договора формулировки «С ...», для остальных документов
# просто вид лица. Ключ skip — «пока не определено».
_PARTY_OPTIONS_CONTRACT = (
    ("person", "С физическим лицом", "Бо шахси воқеӣ",
     "физическое лицо (имя и данные пока неизвестны, оставь заглушки)"),
    ("org", "С организацией (юридическим лицом)", "Бо ташкилот (шахси ҳуқуқӣ)",
     "организация, юридическое лицо (название и реквизиты пока неизвестны, оставь заглушки)"),
    ("ip", "С индивидуальным предпринимателем", "Бо соҳибкори инфиродӣ",
     "индивидуальный предприниматель (данные пока неизвестны, оставь заглушки)"),
    ("skip", "Стороны пока не определены", "Тарафҳо ҳоло муайян нашудаанд", ""),
)
_PARTY_OPTIONS_OTHER = (
    ("person", "Физическое лицо", "Шахси воқеӣ",
     "физическое лицо (имя и данные пока неизвестны, оставь заглушки)"),
    ("org", "Организация (юридическое лицо)", "Ташкилот (шахси ҳуқуқӣ)",
     "организация, юридическое лицо (название и реквизиты пока неизвестны, оставь заглушки)"),
    ("ip", "Индивидуальный предприниматель", "Соҳибкори инфиродӣ",
     "индивидуальный предприниматель (данные пока неизвестны, оставь заглушки)"),
    ("skip", "Пока не определено", "Ҳоло муайян нашудааст", ""),
)

_TYPE_QUESTION = ("Какой договор вам нужен?", "Кадом шартнома ба шумо лозим аст?")
_PARTY_QUESTION = {
    "contract": ("С кем вы заключаете договор?", "Шартномаро бо кӣ мебандед?"),
    "claim": ("Против кого подаётся иск (ответчик)?", None),
    "claim_letter": ("Кому адресована претензия?", None),
    "poa": ("На кого оформляется доверенность (представитель)?", None),
    "receipt": ("Кто вторая сторона расписки?", None),
}
_TOPIC_CARDS = {
    "claim": ("О чём иск?", (
        "Взыскание долга (займ, расписка)", "Взыскание алиментов", "Расторжение брака",
        "Возмещение вреда (ущерба)", "Трудовой спор (зарплата, увольнение)",
        "Исполнение или расторжение договора")),
    "claim_letter": ("О чём претензия?", (
        "Некачественный товар или услуга", "Нарушение сроков", "Невозврат денег или долга",
        "Нарушение условий договора")),
    "complaint": ("На что жалоба?", (
        "На решение суда", "На действия должностного лица или органа", "На штраф или постановление",
        "На отказ в выдаче документа или услуги")),
    "statement": ("Какое заявление нужно?", (
        "Об увольнении по собственному желанию", "О расторжении брака", "О предоставлении отпуска",
        "В суд (суть уточню сам)")),
    "poa": ("На что нужна доверенность?", (
        "Продажа или покупка имущества", "Управление и продажа автомобиля",
        "Представительство в суде или органах", "Получение документов или денег")),
    "receipt": ("О чём расписка?", (
        "Получение денег в долг", "Получение денег за товар или услугу", "Получение имущества")),
}
_TOPIC_KINDS = frozenset(_TOPIC_CARDS)              # у договора «суть» — это его вид
_PARTY_KINDS = frozenset(_PARTY_QUESTION)            # заявлению и жалобе вторая сторона не нужна


# --------------------------------------------------------------- признаки

_FILLER = frozenset("""
мне мой моя моё мои меня нам нас нужен нужна нужно нужны надо можно пожалуйста прошу помоги помогите
срочно быстро пожалуйста для себя чтобы или как что это этот эта эти который какой такой такая
составь составьте составить напиши напишите написать подготовь подготовьте подготовить сделай сделайте
создай создайте создать сформируй оформи оформите оформить разработай образец шаблон шаблоны черновик проект
договор договора договору договоры контракт соглашение соглашения иск иска исковое исковую заявление заявления
жалоба жалобу жалобы претензия претензию претензии доверенность доверенности расписка расписку расписки
ходатайство ходатайства вид типа тип
""".split())
# Служебные слова, которые не называют ни вид договора, ни суть документа
# (русские и таджикские: «лозим аст», «мехоҳам», «барои ман»).
_FILLER_EXTRA = frozenset(
    "нужен нужна срочно пожалуйста быстро образец шаблон лозим мехоҳам мехоҳед мехоҳанд бояд барои шумо ҳозир "
    "зуд илтимос лутфан диҳед диҳам нависед созед кунед тартиб ариза аризаи даъво даъвои даъвогӣ шартнома "
    "шартномаи шикоят шикояти ваколатнома дархост судӣ".split()
)
_WORD_RE = re.compile(r"[A-Za-zА-Яа-яЁёӢӣӮӯҚқҲҳҶҷҒғ]{4,}")

_PARTY_RES = tuple(re.compile(p, re.I) for p in (
    r"(?<![А-Яа-яЁё])с\s+(?-i:ООО|ОАО|ЗАО|АО|ИП|ГУП|ТОО|ЧП)\b",
    r"\bс\s+(?:компани\w+|организаци\w+|фирм\w+|предприяти\w+|физическ\w+|юридическ\w+|"
    r"гражданин\w*|гражданк\w+|индивидуальн\w+|предпринимател\w+|банк\w*|школ\w+|университет\w*)",
    r"\bмежду\s+\S+.*\s+и\s+\S+",
    r"\b(?-i:ООО|ОАО|ЗАО|АО|ИП|ТОО|ЧП)\s*[«\"“]?(?-i:[А-ЯЁA-Z])",
    r"\b(?:с|со|к|от|у|для|на|против|на\s+имя|в\s+адрес)\s+"
    r"(?:моим\s+|моей\s+|моего\s+|мою\s+|нашим\s+|нашей\s+|нашего\s+)?"
    r"(?:сосед\w*|друг\w*|брат\w*|сестр\w+|родственник\w*|работодател\w+|арендодател\w+|арендатор\w*|"
    r"покупател\w+|продавц\w+|клиент\w*|партн[её]р\w*|поставщик\w*|заказчик\w*|подрядчик\w*|"
    r"исполнител\w*|наниматель\w*|квартирант\w*|жильц\w+|ответчик\w*|должник\w*|заёмщик\w*|заемщик\w*|"
    r"займодавец\w*|муж\w*|жен\w+|отец\w*|мать|мам\w+|сын\w*|дочь|дочер\w+)",
    r"\b(?:поставщик\w*|продавц\w+|покупател\w+|работодател\w+|ответчик\w*|должник\w*)\b",
    r"(?<![А-Яа-яЁё])(?:с|со|против|от|для|на|к|у)\s+(?-i:[А-ЯЁ][а-яё]{2,})\b",
    r"\bбо\s+(?-i:[А-ЯЁӢӮҚҲҶҒ][а-яёӣӯқҳҷғ]{2,})",
    r"\bбайни\s+\S+",
    r"\bшахси\s+(?:воқеӣ|ҳуқуқӣ)",
))


_NAME_PAIR_RE = re.compile(r"(?-i:\b([А-ЯЁ][а-яё]{2,})\s+([А-ЯЁ][а-яё]{2,}))\b")


def _is_person_name_pair(first: str, second: str) -> bool:
    """«Ахмедов Карим» — имя; «Договор Аренды» — нет: это просто заглавные буквы."""
    for word in (first.lower(), second.lower()):
        if word in _FILLER or any(pattern.search(word) for _key, pattern in cm._SUBTYPES):
            return False
    return True


def has_counterparty(*texts: str) -> bool:
    """Названа ли в просьбе вторая сторона (имя, организация, роль)."""
    for text in texts:
        text = (text or "").strip()
        if not text:
            continue
        # Первое слово фразы пишется с заглавной буквы независимо от смысла:
        # «Составь Договор» не имя. Поэтому проверяем со второго слова.
        body = text.split(None, 1)[1] if " " in text else ""
        if any(pattern.search(body) for pattern in _PARTY_RES):
            return True
        if any(_is_person_name_pair(a, b) for a, b in _NAME_PAIR_RE.findall(body)):
            return True
    return False


def has_topic(*texts: str) -> bool:
    """Есть ли в просьбе суть документа: о чём иск, на что доверенность."""
    for text in texts:
        words = [w.lower() for w in _WORD_RE.findall(cm._CREATE_RE.sub(" ", text or ""))]
        meaningful = [w for w in words if w not in _FILLER and w not in _FILLER_EXTRA and not _looks_like_verb(w)]
        if meaningful:
            return True
    return False


def _looks_like_verb(word: str) -> bool:
    return bool(re.match(r"(?:состав|напиш|подготов|сдела|созда|сформир|оформ|разработ|сгенерир|набросай|заполни)", word))


_PARTY_CUT_RE = re.compile(r"\s(?:с|со|между|для|на\s+имя)\s", re.I)
_CONTRACT_NOUN_RE = re.compile(r"(?:договор\w*|контракт\w*|соглашени\w*|шартнома\w*)\s*(.*)$", re.I | re.S)


def contract_custom(text: str) -> str:
    """Вид договора своими словами: «договор совместной деятельности».

    Возвращает пусто, если после слова «договор» нет ничего, кроме служебных слов
    и сведений о стороне («договор с Ахмедовым»).
    """
    match = _CONTRACT_NOUN_RE.search((text or "").strip())
    if not match:
        return ""
    rest = cm._CREATE_RE.sub(" ", match.group(1))
    cut = _PARTY_CUT_RE.search(" " + rest)
    if cut:
        rest = (" " + rest)[:cut.start()]
    words = [w for w in re.findall(r"[A-Za-zА-Яа-яЁёӢӣӮӯҚқҲҳҶҷҒғ-]+", rest)]
    kept = [w for w in words if len(w) >= 4 and w.lower() not in _FILLER
            and w.lower() not in _FILLER_EXTRA]
    if not kept:
        return ""
    return " ".join(words)[:80].strip(" ,.;:-")


# ------------------------------------------------------------------ состояние

_KINDS = frozenset(kind for kind, _label, _pattern in cm._DOC_KINDS)


def _text(value, limit=MAX_TEXT) -> str:
    return str(value or "").strip()[:limit] if isinstance(value, (str, int, float)) else ""


def sanitize_state(state) -> Optional[dict]:
    """Состояние пришло от страницы, то есть от пользователя: берём только известное."""
    if not isinstance(state, dict) or state.get("v") != STATE_VERSION:
        return None
    kind = _text(state.get("kind"), 30)
    if kind not in _KINDS:
        return None
    awaiting = _text(state.get("awaiting"), 10)
    if awaiting and awaiting not in _SLOTS:
        return None
    options = []
    for item in (state.get("options") or [])[:MAX_OPTIONS]:
        if isinstance(item, dict) and _text(item.get("text"), 200):
            options.append({"text": _text(item.get("text"), 200), "key": _text(item.get("key"), 200)})
    asked = [s for s in (state.get("asked") or []) if s in _SLOTS][:len(_SLOTS)]
    label = next((lbl for k, lbl, _p in cm._DOC_KINDS if k == kind), kind)
    return {
        "v": STATE_VERSION, "kind": kind, "label": label,
        "subtype": _text(state.get("subtype"), 40), "custom": _text(state.get("custom"), 80),
        "topic": _text(state.get("topic")), "party": _text(state.get("party")),
        "party_known": bool(state.get("party_known")), "party_skipped": bool(state.get("party_skipped")),
        "request": _text(state.get("request")), "awaiting": awaiting, "options": options, "asked": asked,
        "lang": "tg" if state.get("lang") == "tg" else "ru",
    }


@dataclass
class DocPlan:
    doc: Optional[cm.DocRequest] = None
    ask: Optional[dict] = None          # карточка: {"question", "options", "source"}
    state: Optional[dict] = None        # что вернуть странице вместе с карточкой
    request_text: str = ""              # запрос по-русски для промпта и поиска


def _norm(text: str) -> str:
    return re.sub(r"[^0-9a-zа-яёӣӯқҳҷғ]+", " ", (text or "").lower().replace("ё", "е")).strip()


def _option_key(message: str, options) -> Optional[str]:
    """Ключ варианта, если пользователь нажал кнопку (текст совпал с подписью)."""
    target = _norm(message)
    if not target:
        return None
    for item in options or []:
        if _norm(item.get("text")) == target:
            return item.get("key") or ""
    return None


def _fresh_state(doc: cm.DocRequest, message: str, translated: str, lang: str) -> dict:
    texts = [t for t in (message, translated) if t]
    joined = " ".join(texts)
    state = {
        "v": STATE_VERSION, "kind": doc.kind, "label": doc.label, "subtype": doc.subtype, "custom": "",
        "topic": "", "party": "", "party_known": has_counterparty(*texts), "party_skipped": False,
        "request": (translated or message).strip()[:MAX_TEXT], "awaiting": "", "options": [], "asked": [],
        "lang": "tg" if lang == "tg" else "ru",
    }
    if doc.kind == "contract" and not doc.subtype:
        state["custom"] = contract_custom(translated or message) or contract_custom(message)
    if doc.kind in _TOPIC_KINDS and has_topic(joined):
        state["topic"] = (translated or message).strip()[:MAX_TEXT]
    return state


def _missing_slot(state: dict) -> Optional[str]:
    if state["kind"] == "contract":
        if not (state["subtype"] or state["custom"]) and SLOT_TYPE not in state["asked"]:
            return SLOT_TYPE
    elif state["kind"] in _TOPIC_KINDS:
        if not state["topic"] and SLOT_TOPIC not in state["asked"]:
            return SLOT_TOPIC
    if state["kind"] in _PARTY_KINDS:
        if not (state["party"] or state["party_known"] or state["party_skipped"]) and SLOT_PARTY not in state["asked"]:
            return SLOT_PARTY
    return None


TranslateFn = Callable[[list], Optional[list]]


def _card(slot: str, state: dict, lang: str, translate: Optional[TranslateFn]):
    """Карточка вопроса: (вопрос, подписи вариантов, русский текст для истории, варианты состояния)."""
    kind = state["kind"]
    tg = lang == "tg"
    if slot == SLOT_TYPE:
        question = _TYPE_QUESTION[1 if tg else 0]
        shown = [c[2 if tg else 1] for c in CONTRACT_TYPES]
        ru_options = [c[1] for c in CONTRACT_TYPES]
        keys = [c[0] for c in CONTRACT_TYPES]
        ru_question = _TYPE_QUESTION[0]
    elif slot == SLOT_PARTY:
        table = _PARTY_OPTIONS_CONTRACT if kind == "contract" else _PARTY_OPTIONS_OTHER
        ru_question, tg_question = _PARTY_QUESTION[kind]
        ru_options = [o[1] for o in table]
        keys = [o[0] for o in table]
        shown = [o[2 if tg else 1] for o in table]
        question = ru_question
        if tg:
            question = tg_question or _translated([ru_question], translate, [ru_question])[0]
    else:
        ru_question, ru_options = _TOPIC_CARDS[kind]
        ru_options = list(ru_options)
        keys = list(ru_options)
        question, shown = ru_question, list(ru_options)
        if tg:
            done = _translated([ru_question] + ru_options, translate, [ru_question] + ru_options)
            question, shown = done[0], list(done[1:])
    source = ru_question + "\n" + "\n".join("- " + o for o in ru_options)
    return question, shown, source, [{"text": s, "key": k} for s, k in zip(shown, keys)]


def _translated(texts, translate, fallback):
    if translate is None:
        return fallback
    try:
        result = translate(texts)
    except Exception:  # noqa: BLE001 - карточка на русском лучше, чем отсутствие карточки
        return fallback
    return result if result and len(result) == len(texts) else fallback


_SKIP_RE = re.compile(r"^\s*(?:пока\s+)?(?:не\s+знаю|не\s+важно|неважно|потом|позже|пропустить|без\s+имён|без\s+имен)\b", re.I)


def _looks_like_new_request(message: str, translated: str, state: dict, prev_doc: str = "") -> bool:
    """Ответ на вопрос или уже что-то другое: новая просьба, правка документа, вопрос по закону."""
    if _option_key(message, state["options"]) is not None:
        return False
    doc = cm.detect_document(message, translated, prev_doc)
    if doc is not None and doc.edit:
        return True
    if doc is not None and (doc.kind != state["kind"] or len(message.split()) >= 5):
        return True
    if message.rstrip().endswith("?"):
        return True
    limit = 80 if state["awaiting"] == SLOT_TYPE else 300
    return len(message.strip()) > limit


def _apply_answer(state: dict, message: str, translated: str) -> None:
    slot = state["awaiting"]
    key = _option_key(message, state["options"])
    text = message.strip()[:MAX_TEXT]
    if slot == SLOT_TYPE:
        if key:
            state["subtype"] = key
        else:
            found = cm.detect_subtype(" ".join(t for t in (message, translated) if t))
            if found:
                state["subtype"] = found
            else:
                # «договор мены» -> «мены»: слово «договор» в названии дублировалось бы.
                own = contract_custom(translated or message) or contract_custom(message)
                if not own:
                    own = re.sub(r"^(?:договор\w*|шартнома\w*)\s*", "", (translated or message).strip(), flags=re.I)
                state["custom"] = own.strip(" ,.;:-")[:80]
        # «аренды с Ахмедовым»: вторую сторону в том же ответе тоже засчитываем
        if has_counterparty("х " + message, "х " + (translated or "")):
            state["party_known"] = True
    elif slot == SLOT_TOPIC:
        state["topic"] = key or (translated or text)[:MAX_TEXT]
    elif slot == SLOT_PARTY:
        if key == "skip" or (key is None and _SKIP_RE.search(message)):
            state["party_skipped"] = True
        elif key:
            table = _PARTY_OPTIONS_CONTRACT if state["kind"] == "contract" else _PARTY_OPTIONS_OTHER
            state["party"] = next((o[3] for o in table if o[0] == key), text)
        else:
            state["party"] = text
    state["awaiting"] = ""
    state["options"] = []


def _compose(state: dict) -> str:
    parts = [state["request"].rstrip(". ")]
    if state["kind"] == "contract" and (state["subtype"] or state["custom"]):
        parts.append("Вид договора: %s" % (state["subtype"] or state["custom"]))
    if state["topic"] and state["topic"] != state["request"]:
        parts.append("Суть: %s" % state["topic"])
    if state["party"]:
        parts.append("Вторая сторона: %s" % state["party"])
    elif state["party_skipped"]:
        parts.append("Стороны пока не определены: данные сторон оставь заглушками")
    return ". ".join(p for p in parts if p) + "."


def _finish(state: dict) -> DocPlan:
    doc = cm.DocRequest(
        state["kind"], state["label"], state["subtype"], False,
        custom=state["custom"] if not state["subtype"] else "",
        topic="" if state["topic"] == state["request"] else state["topic"],
        party=state["party"],
    )
    return DocPlan(doc=doc, request_text=_compose(state))


def plan_document(message: str, translated: str = "", prev_doc: str = "", state=None,
                  lang: str = "ru", translate: Optional[TranslateFn] = None) -> Optional[DocPlan]:
    """Что делать с сообщением: задать вопрос, составлять документ или это не про документы.

    None — обычный вопрос по закону, документ не заказывали. Иначе DocPlan с
    карточкой вопроса (ask) или с готовым к составлению документом (doc).
    """
    message = (message or "").strip()
    translated = (translated or "").strip()
    st = sanitize_state(state)
    if st and not st["awaiting"]:
        st = None  # диалог уже закончился: продолжать нечего

    if st:
        if _looks_like_new_request(message, translated, st, prev_doc):
            st = None
        else:
            _apply_answer(st, message, translated)

    if not st:
        doc = cm.detect_document(message, translated, prev_doc)
        if doc is None:
            return None
        if doc.edit:
            return DocPlan(doc=doc, request_text=message)
        st = _fresh_state(doc, message, translated, lang)

    slot = _missing_slot(st)
    if slot:
        question, options, source, option_state = _card(slot, st, lang, translate)
        st["awaiting"], st["options"] = slot, option_state
        st["asked"] = st["asked"] + [slot]
        return DocPlan(ask={"question": question, "options": options, "source": source}, state=st)
    return _finish(st)
