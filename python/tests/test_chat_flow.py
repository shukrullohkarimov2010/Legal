# -*- coding: utf-8 -*-
"""Сквозная проверка чата: роль, глубина и документы доходят до промпта.

    python tests/test_chat_flow.py

Модель и поиск подменены: проверяется то, что код обязан гарантировать сам —
какие настройки уходят провайдеру, что попадает в промпт и что дописывается
к ответу. Качество текстов самой модели тут не проверяется.
"""

from __future__ import annotations

import json
import os
import sys
import traceback
import types

HERE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, HERE)
os.environ.setdefault("OPENAI_API_KEY", "test-key-not-used")

import app  # noqa: E402
import chat_modes  # noqa: E402

FAILS = []
CHECKS = 0
CALLS = []
QUERIES = []

CONTRACT_DOC = "**ДОГОВОР АРЕНДЫ КВАРТИРЫ**\n" + "1. Арендодатель передаёт квартиру в аренду. " * 30


def check(name, condition, detail=""):
    global CHECKS
    CHECKS += 1
    if not condition:
        FAILS.append("%s %s" % (name, detail))


def _fake_stream(model, prompt, **kwargs):
    CALLS.append(("stream", prompt, kwargs))
    yield {"response": "Готовый ответ."}
    yield {"done": True}


def _fake_once(model, prompt, num_predict, **kwargs):
    CALLS.append(("once", prompt, kwargs))
    if '"queries"' in prompt:
        return json.dumps({"queries": ["основания увольнения работника",
                                       "расчёт при увольнении"]}, ensure_ascii=False)
    return json.dumps({"topic": "аренда", "gap": "none", "ru": "", "search": "",
                       "ask": "", "choices": []}, ensure_ascii=False)


def _fake_retrieve(query, top_k=None, index=None):
    QUERIES.append((query, top_k))
    return [{
        "source": "1918_ru_1.docx", "code": "Гражданский кодекс Республики Таджикистан",
        "code_short": "ГК РТ", "article": "671", "article_title": "Договор аренды",
        "title": "Статья 671. Договор аренды",
        "text": "Статья 671. Договор аренды. По договору аренды арендодатель обязуется предоставить имущество.",
    }]


_REAL_LLM_STREAM = app.llm_stream
app.llm_stream = _fake_stream
app.llm_once = _fake_once
app.retrieve_rag = _fake_retrieve
app.load_chat_rag_index = lambda *a, **k: {}


def run(payload):
    CALLS.clear()
    QUERIES.clear()
    events = [json.loads(line) for line in app.stream_chat_response(payload)]
    text = "".join(event.get("response") or "" for event in events)
    streams = [c for c in CALLS if c[0] == "stream"]
    onces = [c for c in CALLS if c[0] == "once"]
    prompt, kwargs = (streams[0][1], streams[0][2]) if streams else ("", {})
    return text, prompt, kwargs, onces


def test_defaults_keep_old_behaviour():
    text, prompt, kwargs, onces = run({"message": "Какой срок исковой давности по договору займа?"})
    check("fast tokens from env", kwargs.get("num_predict") == app.CHAT_MAX_TOKENS, repr(kwargs))
    check("fast no reasoning override", kwargs.get("reasoning_effort") is None)
    check("fast no model override", kwargs.get("openai_model") is None)
    check("lawyer fast format", "юрист-консультант, кратко" in prompt)
    check("no disclaimer on short answer", chat_modes.DISCLAIMER.strip() not in text)
    check("one understanding call, no subqueries",
          len(onces) == 1 and "Выдели до четырёх РАЗНЫХ" not in onces[0][1], repr([c[1][:40] for c in onces]))
    check("search width from env", all(k == app.CHAT_RAG_TOP_K for _, k in QUERIES), repr(QUERIES))

    text2, prompt2, _, _ = run({"message": "Какой срок исковой давности по договору займа?",
                                "role": "судья", "depth": "???"})
    check("junk modes fall back", "юрист-консультант, кратко" in prompt2)


def test_advocate_deep():
    text, prompt, kwargs, onces = run({
        "message": "Меня уволили без расчёта и не выплатили зарплату за 3 месяца, что делать?",
        "role": "advocate", "depth": "deep",
    })
    check("advocate persona", prompt.startswith("Вы — адвокат"), prompt[:60])
    check("advocate deep format", "развёрнутая позиция защиты" in prompt)
    check("deep tokens", kwargs.get("num_predict", 0) >= 2600, repr(kwargs.get("num_predict")))
    check("deep reasoning", kwargs.get("reasoning_effort") == "low", repr(kwargs.get("reasoning_effort")))
    check("deep planned subqueries", any('"queries"' in c[1] for c in onces))
    queried = [q for q, _ in QUERIES]
    check("subqueries were searched", "расчёт при увольнении" in queried, repr(queried))
    check("deep searches wider", all(k >= 10 for _, k in QUERIES), repr(QUERIES))
    check("disclaimer appended", chat_modes.DISCLAIMER.strip() in text)
    check("keeps citation rules", "ПРОВЕРЕННАЯ ССЫЛКА RAG" in prompt and "ЗАПРЕЩЕНО: \"РФ\"" in prompt)


def test_document_request():
    text, prompt, kwargs, onces = run({"message": "Составь договор аренды квартиры на 11 месяцев с Ахмедовым"})
    check("document task", "ЗАДАЧА: составить документ — договор аренды" in prompt)
    check("placeholders rule", "квадратных скобках" in prompt)
    check("doc token room", kwargs.get("num_predict", 0) >= 2600, repr(kwargs.get("num_predict")))
    check("doc has no refusal block", "ЕСЛИ ОТВЕТА НА ВОПРОС В ФРАГМЕНТАХ" not in prompt)
    check("doc keeps verified-citation rule", "ПРОВЕРЕННАЯ ССЫЛКА RAG" in prompt)
    check("doc has no answer-in-first-line rule", "Первой строкой дай прямой ответ" not in prompt)
    check("document needs no model call before the answer: the dialog already collected the request",
          len(onces) == 0, repr(len(onces)))
    queried = [q for q, _ in QUERIES]
    check("doc searched form of contract", any("форма договора" in q for q in queried), repr(queried))
    check("doc searched wider", all(k >= 8 for _, k in QUERIES), repr(QUERIES))
    check("doc disclaimer", chat_modes.DISCLAIMER.strip() in text)

    _, adv_prompt, _, _ = run({"message": "Напиши исковое заявление против Ахмедова о взыскании долга", "role": "advocate"})
    check("claim as advocate", "составлять документ как адвокат" not in adv_prompt
          and "как адвокат" in adv_prompt and "исковое заявление" in adv_prompt)


def test_document_edit():
    history = [
        {"role": "user", "content": "составь договор аренды квартиры"},
        {"role": "ai", "content": CONTRACT_DOC},
        {"role": "user", "content": "добавь пункт о неустойке за просрочку платежа"},
    ]
    text, prompt, kwargs, onces = run({
        "message": "добавь пункт о неустойке за просрочку платежа",
        "history": history, "depth": "deep",
    })
    check("edit gets full document", "ТЕКУЩИЙ ТЕКСТ ДОКУМЕНТА" in prompt and CONTRACT_DOC[:200] in prompt)
    check("edit asks whole text", "ПОЛНЫЙ исправленный текст" in prompt)
    check("edit skips model calls before answer", len(onces) == 0, repr([c[1][:50] for c in onces]))
    check("edit disclaimer", chat_modes.DISCLAIMER.strip() in text)

    # Тот же диалог, но реплика — вопрос: это консультация, а не правка.
    _, prompt2, _, _ = run({"message": "а можно ли добавить пункт о неустойке?", "history": history})
    check("question stays consultation", "ТЕКУЩИЙ ТЕКСТ ДОКУМЕНТА" not in prompt2)


def test_strong_model_for_depth_and_documents():
    os.environ["GPT_MODEL_DEEP"] = "test-strong-model"
    try:
        _, _, fast, _ = run({"message": "Какой срок исковой давности по договору займа?"})
        check("fast consultation stays on the base model", fast.get("openai_model") is None, repr(fast))
        _, _, deep, _ = run({"message": "Какой срок исковой давности по договору займа?", "depth": "deep"})
        check("deep uses the strong model", deep.get("openai_model") == "test-strong-model", repr(deep))
        _, _, _, onces = run({"message": "Какой срок исковой давности по договору займа?", "depth": "deep"})
        check("deep service steps use the strong model",
              onces and all(c[2].get("openai_model") == "test-strong-model" for c in onces),
              repr([c[2] for c in onces]))
        _, _, _, onces = run({"message": "Какой срок исковой давности по договору займа?"})
        # Шаг «понять вопрос» формулирует поиск на языке кодексов, от него зависит, дойдёт
        # ли нужная статья: он идёт на сильной модели без размышлений даже в быстром режиме.
        check("fast understanding step uses the strong model without reasoning",
              onces and all(c[2].get("openai_model") == "test-strong-model"
                            and c[2].get("reasoning_effort") == "minimal" for c in onces),
              repr([c[2] for c in onces]))
        _, _, doc, _ = run({"message": "Составь договор аренды квартиры с Ахмедовым"})
        check("document uses the strong model even in fast mode",
              doc.get("openai_model") == "test-strong-model" and doc.get("reasoning_effort") == "low", repr(doc))
    finally:
        os.environ.pop("GPT_MODEL_DEEP", None)
    _, _, doc, _ = run({"message": "Составь договор аренды квартиры с Ахмедовым"})
    check("no strong model configured: base model", doc.get("openai_model") is None, repr(doc))


def test_openai_params_for_new_model_families():
    for model in ("gpt-5-nano", "gpt-5-mini", "gpt-6-luna", "gpt-10-x", "o3-mini"):
        check("reasoning model: " + model, app._openai_is_reasoning_model(model))
    for model in ("gpt-4o", "gpt-4.1-mini", "gpt-4", "legal-fast", ""):
        check("classic model: " + repr(model), not app._openai_is_reasoning_model(model))

    luna = app._openai_payload("gpt-6-luna", "hi", 0.2, 2600, stream=True, reasoning_effort="low")
    check("luna uses max_completion_tokens", "max_completion_tokens" in luna and "max_tokens" not in luna)
    check("luna gets no temperature", "temperature" not in luna)
    check("luna keeps effort low", luna.get("reasoning_effort") == "low", repr(luna))
    minimal = app._openai_payload("gpt-6-luna", "hi", 0.2, 400, stream=False, reasoning_effort="minimal")
    check("minimal becomes none on gpt-6", minimal.get("reasoning_effort") == "none", repr(minimal))
    nano = app._openai_payload("gpt-5-nano", "hi", 0.2, 400, stream=False, reasoning_effort="minimal")
    check("minimal stays on gpt-5", nano.get("reasoning_effort") == "minimal", repr(nano))
    classic = app._openai_payload("gpt-4o", "hi", 0.2, 400, stream=False)
    check("classic keeps temperature and max_tokens",
          classic.get("temperature") == 0.2 and classic.get("max_tokens") == 400
          and "reasoning_effort" not in classic, repr(classic))


def _stages(payload):
    events = [json.loads(line) for line in app.stream_chat_response(payload)]
    return [e["stage"] for e in events if "stage" in e], [e for e in events if "stage" in e]


def test_stage_events_follow_the_pipeline():
    keys, events = _stages({"message": "Какой срок исковой давности по договору займа?"})
    check("fast stages", keys == ["understand", "search", "found", "write"], repr(keys))
    check("found carries a count", any(c.isdigit() for c in events[2]["label"]), repr(events[2]))
    check("stage events carry no answer text", all("response" not in e and "done" not in e for e in events))

    keys, _ = _stages({"message": "Какой срок исковой давности по договору займа?", "depth": "deep"})
    check("deep adds planning", keys == ["understand", "plan", "search", "found", "write"], repr(keys))

    keys, _ = _stages({"message": "Составь договор аренды квартиры с Ахмедовым"})
    check("document goes straight to search and writing", keys == ["search", "found", "write_doc"], repr(keys))

    history = [{"role": "ai", "content": CONTRACT_DOC}]
    keys, _ = _stages({"message": "добавь пункт о неустойке за просрочку платежа", "history": history, "depth": "deep"})
    check("edit skips understanding and planning", keys == ["search", "found", "write_doc"], repr(keys))

    keys, _ = _stages({"message": "Здравствуйте", "depth": "deep"})
    check("smalltalk shows no stages", keys == [], repr(keys))


def test_stages_come_before_the_answer():
    events = [json.loads(line) for line in app.stream_chat_response({"message": "Какой срок исковой давности по договору займа?"})]
    first_text = next(i for i, e in enumerate(events) if e.get("response"))
    check("all stages precede the first text", all("stage" in e for e in events[:first_text]) and first_text >= 3,
          repr(first_text))


def _answer_with(text, payload):
    real = app.llm_stream
    app.llm_stream = lambda model, prompt, **kw: iter([{"response": text}, {"done": True}])
    try:
        return run(payload)[0]
    finally:
        app.llm_stream = real


def test_wrong_code_and_invented_quote_are_flagged():
    q = "Какой срок исковой давности по договору займа?"
    reply = _answer_with("Ответ: ТК РТ, ст. 671 — аренда.", {"message": q})
    check("wrong code flagged in fast mode", "Проверьте кодекс" in reply, repr(reply[-200:]))
    check("the warning names codes in full, not by abbreviation",
          "Трудовой кодекс Республики Таджикистан" in reply and "Гражданский кодекс Республики Таджикистан" in reply
          and "«ТК РТ»" not in reply, repr(reply[-260:]))

    reply = _answer_with("Ответ: ГК РТ, ст. 671 — аренда.", {"message": q})
    check("right code is not flagged", "Проверьте кодекс" not in reply, repr(reply[-200:]))

    invented = "**3. Применимые нормы**\n- ГК РТ, ст. 671 — «арендатор обязуется вернуть квартиру ночью»"
    reply = _answer_with(invented, {"message": q, "depth": "deep"})
    check("invented quote flagged in deep mode", "Цитаты не найдены дословно" in reply, repr(reply[-260:]))

    real_quote = "- ГК РТ, ст. 671 — «По договору аренды арендодатель обязуется предоставить имущество»"
    reply = _answer_with(real_quote, {"message": q, "depth": "deep"})
    check("real quote is not flagged", "Цитаты не найдены" not in reply, repr(reply[-260:]))

    reply = _answer_with(invented, {"message": q})
    check("fast mode does not check quotes", "Цитаты не найдены" not in reply)

def test_abbreviations_are_expanded_in_the_stream_even_when_split_across_chunks():
    """Токены приходят кусками: «ГК» в одном, «РТ» в другом — раскрытие обязано сработать."""
    chunks = ["Ответ: ", "Г", "К", " Р", "Т, ст", ". 214", ". Согласно ст. 81 С", "К РТ алименты. Это ответ по праву Р", "Т."]
    real = app.llm_stream
    app.llm_stream = lambda model, prompt, **kw: iter([{"response": c} for c in chunks] + [{"done": True}])
    try:
        text = run({"message": "Какой срок исковой давности по договору займа?"})[0]
    finally:
        app.llm_stream = real
    check("code name is complete", "Гражданский кодекс Республики Таджикистан, статья 214" in text, repr(text[:200]))
    check("case follows the preceding word",
          "Согласно статье 81 Семейного кодекса Республики Таджикистан алименты" in text, repr(text[:260]))
    check("bare RT expanded at the very end", "по праву Республики Таджикистан" in text, repr(text[-120:]))
    check("no abbreviation survives",
          not any(mark in text for mark in ("ГК РТ", "СК РТ", " ст.", "права РТ", "праву РТ")), repr(text[:260]))


def test_abbreviations_are_expanded_before_translation_and_checks():
    """Полное название попадает и в проверку кода: путаница кодексов по-прежнему ловится."""
    real = app.llm_stream
    app.llm_stream = lambda model, prompt, **kw: iter([{"response": "Ответ: ТК РТ, ст. 671."}, {"done": True}])
    try:
        text = run({"message": "Какой срок исковой давности по договору займа?"})[0]
    finally:
        app.llm_stream = real
    check("expanded", "Трудовой кодекс Республики Таджикистан, статья 671" in text, repr(text[:200]))
    check("the code mix-up is still caught after expansion", "Проверьте кодекс" in text, repr(text[-260:]))

def _events(payload):
    CALLS.clear()
    QUERIES.clear()
    return [json.loads(line) for line in app.stream_chat_response(payload)]


def test_document_dialog_asks_before_drafting():
    ev = _events({"message": "Составь договор"})
    check("only a card comes back", len(ev) == 1 and ev[0].get("clarify") and ev[0].get("done") is True
          and ev[0].get("response") == "", repr(ev)[:300])
    check("no model call and no search before the answer", not CALLS and not QUERIES, repr(CALLS)[:120])
    check("the card asks which contract", "Какой договор" in ev[0]["clarify"]["question"]
          and "Аренды" in ev[0]["clarify"]["options"] and len(ev[0]["clarify"]["options"]) == 8)
    check("dialog state goes to the page", ev[0]["doc_state"]["awaiting"] == "type" and ev[0]["doc_state"]["kind"] == "contract")
    check("history gets the Russian text of the card", ev[0]["source_response"].startswith("Какой договор вам нужен?"))
    check("language is reported", ev[0]["lang"] == "ru")

    ev2 = _events({"message": "Аренды", "doc_state": ev[0]["doc_state"], "clarified": True})
    check("then asks about the other side", len(ev2) == 1 and "С кем" in ev2[0]["clarify"]["question"]
          and ev2[0]["doc_state"]["awaiting"] == "party" and ev2[0]["doc_state"]["subtype"] == "аренды", repr(ev2)[:300])
    check("still no model call", not CALLS and not QUERIES)

    text, prompt, kwargs, onces = run({"message": "С физическим лицом", "doc_state": ev2[0]["doc_state"], "clarified": True})
    check("now the document is drafted", "ЗАДАЧА: составить документ — договор аренды" in prompt, prompt[:200])
    check("the model knows the other side", "Вторая сторона (контрагент)" in prompt and "физическое лицо" in prompt)
    check("the whole request reaches the model",
          "Вид договора: аренды" in prompt and "Составь договор" in prompt, "")
    check("dialog answers are not searched as a question", all("С физическим лицом" != q for q, _ in QUERIES), repr(QUERIES))
    check("a document has its disclaimer", chat_modes.DISCLAIMER.strip() in text)
    check("no service model calls for a document", len(onces) == 0)


def test_document_dialog_stages_and_events():
    ev = _events({"message": "Составь договор аренды с Ахмедовым"})
    keys = [e["stage"] for e in ev if "stage" in e]
    check("complete request goes straight to the document", keys == ["search", "found", "write_doc"], repr(keys))
    check("no card for a complete request", not any(e.get("clarify") for e in ev))


def test_document_dialog_is_left_when_the_user_asks_something_else():
    state = _events({"message": "Аренды", "doc_state": _events({"message": "Составь договор"})[0]["doc_state"]})[0]["doc_state"]
    text, prompt, kwargs, onces = run({"message": "Какой срок исковой давности по договору займа?", "doc_state": state})
    check("a legal question is answered as a question", "ФОРМАТ ОТВЕТА" in prompt and "ЗАДАЧА: составить документ" not in prompt,
          prompt[:150])
    ev = _events({"message": "Составь заявление на увольнение", "doc_state": state})
    check("another document replaces the old dialog", not any(e.get("clarify") for e in ev) and any(e.get("stage") == "write_doc" for e in ev),
          repr([e.get("stage") or e.get("clarify") for e in ev]))


def test_hostile_document_state_does_not_break_the_chat():
    for bad in ("строка", 5, {"v": 1, "kind": "evil"}, {"v": 1, "kind": "contract", "awaiting": "x" * 999}, [1, 2]):
        text, prompt, kwargs, onces = run({"message": "Какой срок исковой давности по договору займа?", "doc_state": bad})
        check("plain question still answered with %r" % (bad,), "ФОРМАТ ОТВЕТА" in prompt, prompt[:120])
        ev = _events({"message": "Составь договор", "doc_state": bad})
        check("bare contract still asks with %r" % (bad,), len(ev) == 1 and ev[0].get("clarify"), repr(ev)[:150])


def test_tajik_document_dialog():
    real = app.lang_service.translator
    app.lang_service.translator = types.SimpleNamespace(available=lambda: False, translate=lambda *a, **k: None, reason="test")
    try:
        ev = _events({"message": "Шартнома тартиб диҳед", "lang": "tg"})
        check("card in Tajik", len(ev) == 1 and ev[0]["clarify"]["question"] == "Кадом шартнома ба шумо лозим аст?", repr(ev)[:300])
        check("Tajik labels", "Иҷора" in ev[0]["clarify"]["options"] and "Хизматрасонӣ" in ev[0]["clarify"]["options"])
        check("language reported", ev[0]["lang"] == "tg")
        check("history stays Russian", ev[0]["source_response"].startswith("Какой договор вам нужен?"))
        ev2 = _events({"message": "Иҷора", "doc_state": ev[0]["doc_state"], "lang": "tg", "clarified": True})
        check("second card in Tajik", ev2[0]["clarify"]["question"] == "Шартномаро бо кӣ мебандед?", repr(ev2)[:300])
        check("the click was understood", ev2[0]["doc_state"]["subtype"] == "аренды")
    finally:
        app.lang_service.translator = real


def test_contract_page_chat_is_not_touched():
    """Чат конструктора договора (передаёт context) документов не составляет и карточек не задаёт."""
    ev = _events({"message": "Составь договор", "context": "Текущий договор аренды..."})
    check("no document dialog on the contract page", not any(e.get("clarify") and e.get("doc_state") for e in ev),
          repr(ev)[:200])


class _FakeResponse:
    """Ответ OpenAI: строки потока, после которых (по желанию) соединение рвётся."""

    def __init__(self, lines, then_raise=None):
        self._lines, self._then_raise = lines, then_raise

    def __enter__(self):
        return self

    def __exit__(self, *exc):
        return False

    def raise_for_status(self):
        return None

    def iter_lines(self, chunk_size=1):
        for line in self._lines:
            yield line
        if self._then_raise:
            raise self._then_raise


def _sse(text):
    return ("data: " + json.dumps({"choices": [{"delta": {"content": text}}]})).encode("utf-8")


def _openai_stream_with(script):
    """Запускает настоящий llm_stream с подменённым requests.post: script — список поведений."""
    import requests
    calls = {"n": 0}

    def post(*args, **kwargs):
        step = script[min(calls["n"], len(script) - 1)]
        calls["n"] += 1
        if isinstance(step, Exception):
            raise step
        return step

    saved = (app.requests.post, app.LLM_PROVIDER, app.OPENAI_API_KEY, app.time.sleep)
    app.requests.post, app.LLM_PROVIDER, app.OPENAI_API_KEY, app.time.sleep = post, "openai", "test-key", lambda s: None
    try:
        out, error = [], None
        try:
            for obj in _REAL_LLM_STREAM("gpt-test", "prompt"):
                out.append(obj)
        except app.LLMBackendError as exc:
            error = str(exc)
        return "".join(o.get("response") or "" for o in out), calls["n"], error
    finally:
        app.requests.post, app.LLM_PROVIDER, app.OPENAI_API_KEY, app.time.sleep = saved


def test_a_dropped_connection_before_the_first_token_is_retried_once():
    import requests
    ok = _FakeResponse([_sse("Готово."), b"data: [DONE]"])
    text, attempts, error = _openai_stream_with([requests.exceptions.ConnectionError("reset"), ok])
    check("second attempt answers", text == "Готово." and attempts == 2 and error is None, repr((text, attempts, error)))

    reset = requests.exceptions.ChunkedEncodingError("Connection broken: ConnectionResetError(10054)")
    text, attempts, error = _openai_stream_with([_FakeResponse([], then_raise=reset), ok])
    check("a reset in the middle of an empty stream is retried", text == "Готово." and attempts == 2, repr((text, attempts, error)))

    text, attempts, error = _openai_stream_with([requests.exceptions.ConnectionError("down")])
    check("two failures give a clear message, not a traceback",
          attempts == 2 and error and "OpenAI" in error and "ConnectionReset" not in error, repr((attempts, error)))


def test_a_reset_after_text_is_not_retried_so_the_answer_is_never_doubled():
    import requests
    reset = requests.exceptions.ChunkedEncodingError("Connection broken")
    text, attempts, error = _openai_stream_with([_FakeResponse([_sse("Начало ответа. ")], then_raise=reset),
                                                 _FakeResponse([_sse("ДУБЛЬ")])])
    check("one attempt only", attempts == 1, repr(attempts))
    check("the partial text is kept and the error is reported", text == "Начало ответа. " and error, repr((text, error)))
    check("no duplicated text", "ДУБЛЬ" not in text)


def test_empty_generation_gets_a_message():
    real = app.llm_stream
    app.llm_stream = lambda model, prompt, **kw: iter([{"done": True}])
    try:
        text, _, _, _ = run({"message": "Какой срок исковой давности по договору займа?", "depth": "deep"})
    finally:
        app.llm_stream = real
    check("empty answer is explained", "не вернула ответ" in text, repr(text))
    check("no disclaimer under an empty answer", chat_modes.DISCLAIMER.strip() not in text)


def test_smalltalk_is_untouched():
    text, prompt, kwargs, onces = run({"message": "Здравствуйте", "depth": "deep", "role": "advocate"})
    check("smalltalk short", kwargs.get("num_predict") == 120, repr(kwargs.get("num_predict")))
    check("smalltalk no disclaimer", chat_modes.DISCLAIMER.strip() not in text)
    check("smalltalk no calls before answer", len(onces) == 0)
    check("smalltalk no search", not QUERIES, repr(QUERIES))


def test_history_window_depends_on_depth():
    long_reply = "Ответ. " * 200
    history = [{"role": "user", "content": "вопрос %d" % i} if i % 2 == 0
               else {"role": "ai", "content": long_reply} for i in range(10)]
    _, fast_prompt, _, _ = run({"message": "Какой срок исковой давности?", "history": history})
    _, deep_prompt, _, _ = run({"message": "Какой срок исковой давности?", "history": history,
                                "depth": "deep"})
    check("deep remembers more", deep_prompt.count("Ассистент:") > fast_prompt.count("Ассистент:"),
          "%s vs %s" % (deep_prompt.count("Ассистент:"), fast_prompt.count("Ассистент:")))


def main():
    tests = [v for k, v in sorted(globals().items()) if k.startswith("test_") and callable(v)]
    for test in tests:
        try:
            test()
        except Exception:  # noqa: BLE001
            FAILS.append("%s: исключение\n%s" % (test.__name__, traceback.format_exc()))
    if FAILS:
        print("FAIL: %d из %d проверок" % (len(FAILS), CHECKS))
        for item in FAILS:
            print("  -", item)
        sys.exit(1)
    print("OK: %d проверок пройдено" % CHECKS)


if __name__ == "__main__":
    main()
