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


def case_ids() -> list:
    return list(CASES)


def get_case(case_id) -> Optional[dict]:
    return CASES.get(str(case_id or ""))


def public_case(case_id: str, translated: Optional[dict] = None) -> Optional[dict]:
    """Карточка дела для интерфейса; `translated` подменяет текстовые поля."""
    case = get_case(case_id)
    if not case:
        return None
    card = {"id": case_id, "kind": case["kind"],
            "opponent_gender": case["opponent_gender"], "witness_gender": case["witness_gender"]}
    for field in CARD_FIELDS:
        card[field] = (translated or {}).get(field) or case[field]
    return card


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
    action = entry.get("action")
    if action == "object":
        return "%s [возражение]" % _SPEAKER_NAMES["user"]
    if action == "witness":
        return "%s [вопрос свидетелю: %s]" % (_SPEAKER_NAMES["user"], case.get("witness", "свидетель"))
    return _SPEAKER_NAMES["user"]


def _dialogue(history, case: dict) -> str:
    if not history:
        return "Заседание только началось."
    lines = []
    for h in history:
        name = _user_label(h, case) if h["speaker"] == "user" else _SPEAKER_NAMES.get(h["speaker"], h["speaker"])
        lines.append("%s: %s" % (name, h["text"]))
    return "\n".join(lines)


def build_turn_prompt(case: dict, history: list, message: str, action: str, rag_context: str, turn_no: int) -> str:
    return f"""Ты — модератор учебного судебного заседания по законодательству Республики Таджикистан. Ты играешь СУДЬЮ,
ПРОТИВНУЮ СТОРОНУ ({case['opponent']}) и СВИДЕТЕЛЯ ({case['witness']}). Пользователь — {case['role']}.

ДЕЛО: {case['title']}
ФАКТЫ: {case['facts']}
ЦЕЛЬ ПОЛЬЗОВАТЕЛЯ: {case['goal']}

ФРАГМЕНТЫ ЗАКОНОВ РТ (единственный допустимый источник статей):
{rag_context or "Фрагменты не найдены."}

ХОД ЗАСЕДАНИЯ:
{_dialogue(history, case)}

НОВАЯ РЕПЛИКА ПОЛЬЗОВАТЕЛЯ (раунд {turn_no}): {message}
ТИП ДЕЙСТВИЯ: {action_note(action, case)}

ЗАДАЧА: продолжи заседание и оцени реплику пользователя.
Правила:
- Верни от 1 до {MAX_LINES} коротких реплик (каждая 1–3 предложения) от судьи, противной стороны или свидетеля. Говори живо, как в зале суда.
- Противная сторона возражает по существу и ищет слабые места, судья задаёт уточняющие вопросы и следит за порядком, свидетель отвечает только если ему задан вопрос.
- Ссылайся на статьи только из фрагментов выше и называй кодекс полностью; номера, которых нет во фрагментах, не придумывай.
- Не подсказывай пользователю решение в репликах персонажей: подсказка идёт только в поле hint.
- Оценка assessment: legal_basis (юридическое обоснование, ссылки на нормы), logic (логика и последовательность), evidence (работа с фактами и доказательствами), procedure (процессуальный порядок, уместность, тон): целые числа 0–10.
  Реплика без статей получает низкий legal_basis; пустая или не по делу — низкие оценки по всем критериям.
- comment: одно предложение о том, что было сильным или слабым. hint: одно предложение о том, что сделать дальше.

Ответь ТОЛЬКО JSON без пояснений:
{{"lines":[{{"speaker":"judge|opponent|witness","text":"..."}}],"assessment":{{"legal_basis":0,"logic":0,"evidence":0,"procedure":0,"comment":"...","hint":"..."}}}}"""


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
    return {"lines": lines, "parts": parts, "comment": _text(raw.get("comment"), 300), "hint": _text(raw.get("hint"), 300)}


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
