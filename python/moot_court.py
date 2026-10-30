# -*- coding: utf-8 -*-
"""Симулятор суда: учебные дела, промпты и подсчёт баллов.

Модуль не ходит ни в модель, ни в RAG: только данные дел и чистые функции,
которые собирают промпты, проверяют ответ модели и считают баллы. Благодаря
этому его можно проверить тестами без сервера и без ключей, а `app.py`
остаётся склейкой.

Баллы считаются так, чтобы их нельзя было накрутить красивыми словами:
оценка модели по четырём критериям корректируется детерминированной проверкой
статей, которые назвал пользователь (та же проверка, что и в чате): найденные
в базе статьи прибавляют баллы, выдуманные или приписанные не тому кодексу
отнимают.
"""

from __future__ import annotations

import re
from typing import Optional

SPEAKERS = ("user", "judge", "opponent", "witness")
CRITERIA = ("legal_basis", "logic", "evidence", "procedure")
ACTIONS = ("argue", "object", "witness")

MAX_HISTORY = 14
MAX_TEXT = 1200
MAX_LINES = 3
MAX_TURNS = 10

VERIFIED_BONUS = 5      # за каждую статью, найденную по делу (не больше двух)
BAD_CITE_PENALTY = 8    # за каждую выдуманную или не из того кодекса (не больше двух)

CASES = {
    "theft": {
        "kind": "criminal",
        "title": "Дело о краже телефона",
        "role": "защитник подсудимого",
        "opponent": "Прокурор",
        "witness": "Дилором Назарова, продавец",
        "goal": "Добиться оправдания или смягчения ответственности подсудимого.",
        "opponent_gender": "m", "witness_gender": "f",
        "facts": (
            "Фарход Рахимов, 24 года, студент, ранее не судим, обвиняется в краже мобильного телефона "
            "стоимостью 2 800 сомони из торгового павильона. По версии обвинения, 14 июня он, воспользовавшись "
            "тем, что продавец отвлеклась, взял телефон с прилавка и ушёл. Рахимов утверждает, что забрал телефон "
            "по ошибке, приняв его за свой, оставленный там же час назад, а на следующий день, узнав о пропаже "
            "чужого телефона, вернул его. Телефон возвращён потерпевшему, ущерба нет. На записи камеры видно, "
            "как подсудимый берёт телефон, но не видно, что было до этого. Свидетель: продавец Дилором Назарова."
        ),
        "query": "кража тайное хищение чужого имущества умысел добровольное возмещение ущерба смягчающие обстоятельства доказательства",
    },
    "debt": {
        "kind": "civil",
        "title": "Взыскание долга по расписке",
        "role": "представитель истца",
        "opponent": "Представитель ответчика",
        "witness": "Ориф Холов, знакомый ответчика",
        "goal": "Добиться взыскания основного долга и неустойки в разумном размере.",
        "opponent_gender": "f", "witness_gender": "m",
        "facts": (
            "Ваш доверитель Сухроб Каримов 1 марта 2025 года дал в долг Бахтиёру Саидову 40 000 сомони по расписке "
            "сроком до 1 сентября 2025 года. Деньги не возвращены. Саидов признаёт получение денег, но утверждает, "
            "что вернул 15 000 сомони наличными без расписки, а неустойка (0,5% в день, указана в расписке) чрезмерна. "
            "Иск подан 20 января 2026 года на 40 000 сомони основного долга и 36 000 сомони неустойки. "
            "Свидетель: Ориф Холов, знакомый ответчика, присутствовал при передаче 15 000 сомони, но не может "
            "подтвердить, что это был возврат долга."
        ),
        "query": "договор займа расписка возврат долга неустойка уменьшение неустойки свидетельские показания доказательства",
    },
    "dismissal": {
        "kind": "labor",
        "title": "Незаконное увольнение",
        "role": "представитель работника",
        "opponent": "Представитель работодателя",
        "witness": "Зарина Ахмедова, главный бухгалтер",
        "goal": "Добиться восстановления на работе и оплаты вынужденного прогула.",
        "opponent_gender": "m", "witness_gender": "f",
        "facts": (
            "Ваша доверительница Мадина Юсупова шесть лет работала бухгалтером в ООО «Нур». 10 октября её уволили "
            "за «систематическое неисполнение обязанностей» без предупреждения: приказ издан в тот же день, ранее "
            "замечаний и выговоров не было, с приказом ознакомили только после увольнения. Работодатель ссылается на "
            "две служебные записки о задержке отчётов. Юсупова требует восстановить её на работе, оплатить "
            "вынужденный прогул и компенсировать моральный вред. Свидетель: главный бухгалтер Зарина Ахмедова."
        ),
        "query": ("расторжение трудового договора по инициативе работодателя дисциплинарное взыскание порядок применения "
                  "восстановление на работе оплата вынужденного прогула компенсация морального вреда"),
    },
}

CARD_FIELDS = ("title", "role", "opponent", "witness", "goal", "facts")
KINDS = ("criminal", "civil", "labor")
RULINGS = ("admitted", "rejected", "none")
MAX_EVIDENCE = 5

# Улики на доске доказательств. Часть помогает пользователю, часть противной
# стороне: выигрывает тот, кто сумеет ими распорядиться.
_EVIDENCE = {
    "theft": [
        {"id": "video", "icon": "🎥", "title": "Видеозапись с камеры",
         "detail": "Видно, как Рахимов берёт телефон с прилавка и уходит. Что было до этого, на записи не видно."},
        {"id": "phone", "icon": "📱", "title": "Телефон потерпевшего",
         "detail": "Возвращён на следующий день без повреждений, ущерба нет."},
        {"id": "log", "icon": "📒", "title": "Журнал павильона",
         "detail": "Запись за 14 июня, 13:10: «забытый чёрный телефон у кассы», владелец не указан."},
        {"id": "ref", "icon": "🎓", "title": "Характеристика с учёбы",
         "detail": "Положительная, ранее не судим, к ответственности не привлекался."},
    ],
    "debt": [
        {"id": "receipt", "icon": "🧾", "title": "Расписка",
         "detail": "40 000 сомони, возврат до 1 сентября 2025 года, неустойка 0,5% в день, подписи обеих сторон."},
        {"id": "chat", "icon": "💬", "title": "Переписка в мессенджере",
         "detail": "10 сентября Саидов пишет: «Верну всё до конца года, извини за задержку»."},
        {"id": "bank", "icon": "🏦", "title": "Банковская выписка",
         "detail": "Переводов от Саидова Каримову за 2025 год нет."},
        {"id": "kholov", "icon": "🗣️", "title": "Показания Холова",
         "detail": "В мае видел, как Саидов передал Каримову 15 000 сомони наличными; за что, не знает."},
    ],
    "dismissal": [
        {"id": "order", "icon": "📄", "title": "Приказ об увольнении",
         "detail": "От 10 октября, основание: систематическое неисполнение обязанностей; ознакомлена 12 октября."},
        {"id": "memos", "icon": "📝", "title": "Служебные записки",
         "detail": "Две записки о задержке квартальных отчётов на 2 и 3 дня; объяснения у работника не запрашивались."},
        {"id": "file", "icon": "📁", "title": "Личное дело",
         "detail": "Шесть лет стажа, взысканий нет, благодарность за 2024 год."},
        {"id": "akhmedova", "icon": "🗣️", "title": "Показания Ахмедовой",
         "detail": "Отчёты задерживались, потому что отдел продаж поздно передавал первичные документы."},
    ],
}
for _cid, _items in _EVIDENCE.items():
    CASES[_cid]["evidence"] = _items


def case_ids() -> list:
    return list(CASES)


def get_case(case_id) -> Optional[dict]:
    return CASES.get(str(case_id or ""))


def card_of(case_id: str, case: dict, translated: Optional[dict] = None) -> dict:
    """Карточка дела для интерфейса; `translated` подменяет текстовые поля и тексты улик."""
    translated = translated or {}
    card = {"id": case_id, "kind": case["kind"],
            "opponent_gender": case["opponent_gender"], "witness_gender": case["witness_gender"]}
    for field in CARD_FIELDS:
        card[field] = translated.get(field) or case[field]
    shown = translated.get("evidence") or []
    card["evidence"] = []
    for i, item in enumerate(case.get("evidence") or []):
        view = shown[i] if i < len(shown) and isinstance(shown[i], dict) else {}
        card["evidence"].append({"id": item["id"], "icon": item["icon"],
                                 "title": view.get("title") or item["title"], "detail": view.get("detail") or item["detail"]})
    return card


def public_case(case_id: str, translated: Optional[dict] = None) -> Optional[dict]:
    case = get_case(case_id)
    return card_of(case_id, case, translated) if case else None


def resolve_case(case_id, custom=None) -> Optional[dict]:
    """Встроенное дело по id или своё дело, присланное страницей (проверяется каждый раз)."""
    if str(case_id or "") == "custom":
        return sanitize_custom(custom)
    return get_case(case_id)


# ------------------------------------------------------------------ вход

def sanitize_history(history) -> list:
    """Реплики из запроса: только известные говорящие, текст ограничен, хвост не длиннее MAX_HISTORY."""
    clean = []
    for item in history if isinstance(history, list) else []:
        if not isinstance(item, dict):
            continue
        speaker, text = str(item.get("speaker") or ""), str(item.get("text") or "").strip()
        if speaker in SPEAKERS and text:
            entry = {"speaker": speaker, "text": text[:MAX_TEXT]}
            if speaker == "user" and str(item.get("action") or "") in ACTIONS:
                entry["action"] = str(item["action"])
            if speaker == "user" and str(item.get("exhibit") or "").strip():
                entry["exhibit"] = _text(item.get("exhibit"), 120)
            clean.append(entry)
    return clean[-MAX_HISTORY:]


def normalize_action(value) -> str:
    value = str(value or "").strip().lower()
    return value if value in ACTIONS else "argue"


# ------------------------------------------------------------------ промпты

_SPEAKER_NAMES = {"user": "ЗАЩИТНИК/ПРЕДСТАВИТЕЛЬ (пользователь)", "judge": "СУДЬЯ", "opponent": "ПРОТИВНАЯ СТОРОНА", "witness": "СВИДЕТЕЛЬ"}

def action_note(action: str, case: dict) -> str:
    if action == "object":
        return ("Пользователь заявил ВОЗРАЖЕНИЕ против действий или слов противной стороны. Судья обязан принять "
                "решение: возражение принято или отклонено, с кратким основанием.")
    if action == "witness":
        return (
            "Пользователь задаёт вопрос СВИДЕТЕЛЮ: %(w)s. Вопрос адресован именно ему: «ты», «вы» и «вас» в вопросе "
            "относятся к свидетелю, а «он», «его» и «ему» без имени — к подсудимому или стороне, о которой идёт дело. "
            "Не требуй уточнить, кому адресован вопрос или о ком речь, если это понятно из дела.\n"
            "Первой репликой отвечает СВИДЕТЕЛЬ от первого лица, коротко и по существу: только то, что он мог видеть и знать "
            "по делу; чего не знает — так и говорит. Выдумывать новые факты, меняющие суть дела, свидетель не вправе. "
            "Если вопрос недопустим (наводящий, оскорбительный), его снимает СУДЬЯ. Если вопрос не относится к делу, "
            "судья снимает его и одной фразой напоминает, о чём дело; противная сторона в этом случае молчит."
        ) % {"w": case.get("witness", "свидетель")}
    return "Пользователь излагает свою позицию или аргумент."


def _user_label(entry: dict, case: dict) -> str:
    action, label = entry.get("action"), _SPEAKER_NAMES["user"]
    if action == "object":
        label = "%s [возражение]" % label
    elif action == "witness":
        label = "%s [вопрос свидетелю: %s]" % (label, case.get("witness", "свидетель"))
    if entry.get("exhibit"):
        label = "%s [предъявлено доказательство: %s]" % (label, entry["exhibit"])
    return label


def _materials(case: dict) -> str:
    items = case.get("evidence") or []
    if not items:
        return "Отдельных доказательств нет."
    return "\n".join("- %s: %s" % (e["title"], e["detail"]) for e in items)


def _dialogue(history, case: dict) -> str:
    if not history:
        return "Заседание только началось."
    lines = []
    for h in history:
        name = _user_label(h, case) if h["speaker"] == "user" else _SPEAKER_NAMES.get(h["speaker"], h["speaker"])
        lines.append("%s: %s" % (name, h["text"]))
    return "\n".join(lines)


def build_turn_prompt(case: dict, history: list, message: str, action: str, rag_context: str, turn_no: int,
                      exhibit: Optional[dict] = None) -> str:
    shown = ""
    if exhibit:
        shown = (
            "\nПОЛЬЗОВАТЕЛЬ ПРЕДЪЯВЛЯЕТ СУДУ ДОКАЗАТЕЛЬСТВО: «%s» — %s\n"
            "Судья решает, приобщить ли его к делу (относимость и допустимость), и прямо говорит об этом в своей реплике; "
            "противная сторона может его оспорить. Поле ruling: admitted — приобщено, rejected — отказано.\n"
        ) % (exhibit["title"], exhibit["detail"])
    return f"""Ты — модератор учебного судебного заседания по законодательству Республики Таджикистан. Ты играешь СУДЬЮ,
ПРОТИВНУЮ СТОРОНУ ({case['opponent']}) и СВИДЕТЕЛЯ ({case['witness']}). Пользователь — {case['role']}.

ДЕЛО: {case['title']}
ФАКТЫ: {case['facts']}
ЦЕЛЬ ПОЛЬЗОВАТЕЛЯ: {case['goal']}
МАТЕРИАЛЫ ДЕЛА (доказательства, известные суду и сторонам):
{_materials(case)}

ФРАГМЕНТЫ ЗАКОНОВ РТ (единственный допустимый источник статей):
{rag_context or "Фрагменты не найдены."}

ХОД ЗАСЕДАНИЯ:
{_dialogue(history, case)}

НОВАЯ РЕПЛИКА ПОЛЬЗОВАТЕЛЯ (раунд {turn_no}): {message}
ТИП ДЕЙСТВИЯ: {action_note(action, case)}
{shown}
ЗАДАЧА: продолжи заседание и оцени реплику пользователя.
Правила:
- Верни от 1 до {MAX_LINES} коротких реплик (каждая 1–3 предложения) от судьи, противной стороны или свидетеля. Говори живо, как в зале суда.
- Противная сторона возражает по существу и ищет слабые места, судья задаёт уточняющие вопросы и следит за порядком, свидетель отвечает только если ему задан вопрос.
- Ссылайся на статьи только из фрагментов выше и называй кодекс полностью; номера, которых нет во фрагментах, не придумывай.
- Не подсказывай пользователю решение в репликах персонажей: подсказка идёт только в поле hint.
- Оценка assessment: legal_basis (юридическое обоснование, ссылки на нормы), logic (логика и последовательность), evidence (работа с фактами и доказательствами), procedure (процессуальный порядок, уместность, тон): целые числа 0–10.
  Реплика без статей получает низкий legal_basis; пустая или не по делу — низкие оценки по всем критериям.
- comment: одно предложение о том, что было сильным или слабым. hint: одно предложение о том, что сделать дальше.
- ruling: admitted или rejected, только если пользователь предъявил доказательство; иначе none.
- new_evidence: если в этом раунде установлен НОВЫЙ факт, которого нет в материалах (показания свидетеля, признание
  стороны), верни его одной записью {{"title": "2–5 слов", "detail": "1–2 предложения"}}; иначе пустой список.

Ответь ТОЛЬКО JSON без пояснений:
{{"lines":[{{"speaker":"judge|opponent|witness","text":"..."}}],"assessment":{{"legal_basis":0,"logic":0,"evidence":0,"procedure":0,"comment":"...","hint":"..."}},"ruling":"none","new_evidence":[]}}"""


def build_verdict_prompt(case: dict, history: list, mean_score: int, cited: list) -> str:
    cites = ", ".join(cited) if cited else "не названы"
    return f"""Ты — судья в учебном заседании по законодательству Республики Таджикистан. Вынеси решение по делу и оцени работу
пользователя ({case['role']}).

ДЕЛО: {case['title']}
ФАКТЫ: {case['facts']}
ЦЕЛЬ ПОЛЬЗОВАТЕЛЯ: {case['goal']}

ХОД ЗАСЕДАНИЯ:
{_dialogue(history, case)}

Статьи, названные пользователем и найденные в базе: {cites}.
Средний балл за реплики по системе: {mean_score} из 100.

Верни ТОЛЬКО JSON:
{{
 "verdict": "решение суда простым языком, 3–5 предложений, с мотивировкой",
 "outcome": "won|partial|lost",
 "overall": 0,
 "breakdown": {{"legal_basis": 0, "logic": 0, "evidence": 0, "procedure": 0}},
 "strengths": ["до 3 коротких пунктов"],
 "mistakes": ["до 3 коротких пунктов: что и почему было слабо"],
 "study": ["до 3 коротких поисковых запросов по темам и нормам, которые стоит изучить"]
}}
outcome считай по цели пользователя: won — решение полностью в его пользу (для защиты это оправдание или прекращение дела), partial — цель достигнута частично (смягчение, часть требований), lost — решение против пользователя. Исход должен совпадать с тем, что написано в verdict.
overall и значения breakdown — целые числа 0–100: они оценивают работу пользователя, а не исход дела. Не придумывай номера статей."""


# ------------------------------------------------------------------ разбор ответа модели

def _clip(value, low, high, default=0):
    try:
        number = int(round(float(value)))
    except (TypeError, ValueError):
        return default
    return max(low, min(high, number))


def _text(value, limit=500) -> str:
    return re.sub(r"\s+", " ", str(value or "")).strip()[:limit]


def parse_turn(data) -> Optional[dict]:
    """Проверенные реплики и оценка из ответа модели; None, если пользоваться нечем."""
    if not isinstance(data, dict):
        return None
    lines = []
    for item in data.get("lines") if isinstance(data.get("lines"), list) else []:
        if not isinstance(item, dict):
            continue
        speaker, text = str(item.get("speaker") or "").strip().lower(), _text(item.get("text"), 700)
        if speaker in ("judge", "opponent", "witness") and text:
            lines.append({"speaker": speaker, "text": text})
        if len(lines) >= MAX_LINES:
            break
    if not lines:
        return None
    raw = data.get("assessment") if isinstance(data.get("assessment"), dict) else {}
    parts = {name: _clip(raw.get(name), 0, 10) for name in CRITERIA}
    ruling = str(data.get("ruling") or "").strip().lower()
    found = []
    for item in data.get("new_evidence") if isinstance(data.get("new_evidence"), list) else []:
        if isinstance(item, dict) and _text(item.get("title")) and _text(item.get("detail")):
            found.append({"title": _text(item.get("title"), 80), "detail": _text(item.get("detail"), 240)})
            break
    return {"lines": lines, "parts": parts, "comment": _text(raw.get("comment"), 300), "hint": _text(raw.get("hint"), 300),
            "ruling": ruling if ruling in RULINGS else "none", "new_evidence": found}


def sanitize_exhibit(value) -> Optional[dict]:
    """Улика, которую предъявляет пользователь: название и суть, ограниченные по длине."""
    if not isinstance(value, dict):
        return None
    title, detail = _text(value.get("title"), 80), _text(value.get("detail"), 240)
    if not title:
        return None
    return {"id": _text(value.get("id"), 40), "title": title, "detail": detail or title}


# ------------------------------------------------------------------ своё дело

_OPPONENTS = {"criminal": "Прокурор", "civil": "Представитель ответчика", "labor": "Представитель работодателя"}


def build_custom_prompt(situation: str, role_hint: str = "") -> str:
    role_line = ("Пользователь хочет выступать как: %s." % role_hint) if role_hint else \
        "Роль пользователя выбери сам: сторона, которой нужна юридическая защита в этой ситуации."
    return f"""Ты — методист юридической клиники. По описанию ситуации составь учебное дело для судебного заседания по
законодательству Республики Таджикистан. {role_line}

ОПИСАНИЕ СИТУАЦИИ:
{situation}

Правила:
- Имена реальных людей и названия организаций замени вымышленными таджикскими; город — Душанбе, если не указан другой город Таджикистана.
- Факты излагай нейтрально, 4–7 предложений, с датами и суммами из описания; спорные моменты оставь спорными, чтобы обеим сторонам было что доказывать.
- kind: criminal (уголовное), civil (гражданское) или labor (трудовое).
- Доказательства: 3–4 штуки, часть в пользу пользователя, часть против; у каждого один эмодзи, название в 2–5 слов и суть в 1–2 предложениях.
- query: 8–14 слов юридических терминов для поиска норм в кодексах РТ (как называются статьи), без названий кодексов.
- Если описание не похоже на правовой спор, всё равно составь ближайшее учебное дело по его теме.

Ответь ТОЛЬКО JSON:
{{"kind":"civil","title":"до 6 слов","role":"например, представитель истца","opponent":"например, Представитель ответчика","opponent_gender":"m|f",
"witness":"Имя Фамилия, кто это","witness_gender":"m|f","goal":"одно предложение","facts":"...","query":"...",
"evidence":[{{"icon":"🧾","title":"...","detail":"..."}}]}}"""


def sanitize_custom(data) -> Optional[dict]:
    """Своё дело из ответа модели или из запроса страницы: всё проверяется и обрезается, иначе None."""
    if not isinstance(data, dict):
        return None
    kind = str(data.get("kind") or "").strip().lower()
    kind = kind if kind in KINDS else "civil"
    case = {
        "kind": kind,
        "title": _text(data.get("title"), 80),
        "role": _text(data.get("role"), 80),
        "opponent": _text(data.get("opponent"), 60) or _OPPONENTS[kind],
        "witness": _text(data.get("witness"), 90),
        "goal": _text(data.get("goal"), 240),
        "facts": _text(data.get("facts"), 1600),
        "query": _text(data.get("query"), 240),
        "opponent_gender": "f" if str(data.get("opponent_gender") or "") == "f" else "m",
        "witness_gender": "f" if str(data.get("witness_gender") or "") == "f" else "m",
    }
    if not all(case[k] for k in ("title", "role", "witness", "goal", "facts")) or len(case["facts"]) < 60:
        return None
    if len(case["query"].split()) < 3:
        case["query"] = _text(case["title"] + " " + case["goal"], 240)
    evidence = []
    for i, item in enumerate(data.get("evidence") if isinstance(data.get("evidence"), list) else []):
        if not isinstance(item, dict):
            continue
        title, detail = _text(item.get("title"), 80), _text(item.get("detail"), 240)
        if title and detail:
            icon = _text(item.get("icon"), 4) or "📎"
            evidence.append({"id": _text(item.get("id"), 40) or "e%d" % (i + 1), "icon": icon, "title": title, "detail": detail})
        if len(evidence) >= MAX_EVIDENCE:
            break
    case["evidence"] = evidence
    return case


def turn_score(parts: dict, citations: list) -> int:
    """Балл реплики 0–100: оценка по критериям плюс проверка названных статей."""
    base = sum(parts.get(name, 0) for name in CRITERIA) / (10 * len(CRITERIA)) * 100
    # «exists» — статья есть в кодексе, но к делу её не подбирали: баллов не даёт и не отнимает.
    good = sum(1 for c in citations if c.get("status") == "verified")
    bad = sum(1 for c in citations if c.get("status") in ("missing", "wrong_code"))
    base += VERIFIED_BONUS * min(good, 2) - BAD_CITE_PENALTY * min(bad, 2)
    return _clip(base, 0, 100)


def parse_verdict(data) -> Optional[dict]:
    if not isinstance(data, dict) or not _text(data.get("verdict")):
        return None
    outcome = str(data.get("outcome") or "").strip().lower()
    raw = data.get("breakdown") if isinstance(data.get("breakdown"), dict) else {}

    def items(key, limit):
        values = data.get(key) if isinstance(data.get(key), list) else []
        return [_text(v, 220) for v in values if _text(v, 220)][:limit]

    return {
        "verdict": _text(data.get("verdict"), 900),
        "outcome": outcome if outcome in ("won", "partial", "lost") else "partial",
        "overall": _clip(data.get("overall"), 0, 100, 50),
        "breakdown": {name: _clip(raw.get(name), 0, 100, 50) for name in CRITERIA},
        "strengths": items("strengths", 3),
        "mistakes": items("mistakes", 3),
        "study": items("study", 3),
    }


def final_score(turn_scores: list, overall: int) -> int:
    """Итог: поровну средний балл за реплики (поздние весят больше) и общая оценка судьи."""
    scores = [s for s in turn_scores if isinstance(s, (int, float))][-MAX_TURNS:]
    if not scores:
        return _clip(overall, 0, 100)
    weights = [1 + index * 0.25 for index in range(len(scores))]
    mean = sum(s * w for s, w in zip(scores, weights)) / sum(weights)
    return _clip(0.5 * mean + 0.5 * overall, 0, 100)


_STEM_RE = re.compile(r"[А-Яа-яЁёӢӣҒғҚқӮӯҲҳҶҷ]{4,}")


def relevant_titles(query: str, items: list, limit: int = 4) -> list:
    """Статьи, чьё название пересекается с делом хотя бы по одной основе слова.

    Поиск по делу иногда приносит статью только из-за общих служебных слов
    («тайна» в «тайном хищении» и «тайне усыновления»); показывать её как
    «статью по делу» нельзя. Совпадение ищется по первым пяти буквам слова.
    """
    stems = {w.lower()[:5] for w in _STEM_RE.findall(query or "")}
    out = []
    for item in items:
        title = str(item.get("title") or "")
        if stems & {w.lower()[:5] for w in _STEM_RE.findall(title)}:
            out.append(item)
        if len(out) >= limit:
            break
    return out


def grade(score: int) -> str:
    if score >= 85:
        return "excellent"
    if score >= 70:
        return "good"
    if score >= 50:
        return "fair"
    return "weak"
