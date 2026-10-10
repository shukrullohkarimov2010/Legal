# -*- coding: utf-8 -*-
"""Модульные тесты языкового слоя чата.

    python tests/test_lang_service.py

Сеть и скачанная модель перевода не нужны: проверяются определение языка,
защита юридических реквизитов от переводчика и разбор markdown. Именно здесь
ломается качество ответа на таджикском, если тронуть эвристики.
"""

from __future__ import annotations

import os
import sys
import traceback

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

import lang_service as ls  # noqa: E402

FAILURES: list[str] = []


def check(condition, message):
    if not condition:
        FAILURES.append(message)


def test_detects_tajik_with_diacritics():
    """Буквы ӣ ӯ ҳ қ ғ ҷ есть только в таджикском алфавите."""
    samples = [
        "Шартномаи меҳнатӣ чӣ тавр бекор карда мешавад?",
        "Ҳуқуқи меросӣ чист?",
        "Оё ман метавонам шартномаро бекор кунам?",
    ]
    for text in samples:
        check(ls.detect_lang(text) == "tg", "не распознан таджикский: %s" % text)


def test_detects_tajik_without_diacritics():
    """Пользователи пишут без диакритики — служебные слова обязаны вытянуть."""
    samples = [
        "Шартномаи мехнати чи тавр бекор карда мешавад?",
        "Хукуки мерос чист?",
        "Модда 45 чи мегуяд?",
    ]
    for text in samples:
        check(ls.detect_lang(text) == "tg", "не распознан таджикский без диакритики: %s" % text)


def test_detects_tajik_word_forms_without_diacritics():
    """Разговорная речь без диакритики: словоформа, а не словарная форма.

    Баг замечен на живом чате: «хукуки» (родительный/определительный падеж от
    «хукук») не совпадал точь-в-точь со стоп-словом и уходил в default —
    которым фронт передаёт язык интерфейса страницы, а не язык вопроса.
    """
    samples = [
        "мехохам донам оиди хукуки корй",
        "корманд метавонад аз шартномаи мехнати даст кашад",
        "ин конунхои РТ дар бораи андоз чист",
    ]
    for text in samples:
        check(ls.detect_lang(text, default="ru") == "tg",
              "словоформа без диакритики ушла в default: %s" % text)


def test_detects_russian():
    """Буквы ы щ ь ц в таджикском алфавите отсутствуют."""
    samples = [
        "Как расторгнуть трудовой договор?",
        "Какие статьи ГК РТ регулируют аренду?",
        "Что говорит статья 45 о сроках?",
    ]
    for text in samples:
        check(ls.detect_lang(text) == "ru", "не распознан русский: %s" % text)


def test_detects_tajik_greetings():
    """«салом» — всё сообщение целиком, других признаков языка в нём нет."""
    for text in ("салом", "Салом!", "ассалому алейкум", "рахмат", "ташаккур"):
        check(ls.detect_lang(text) == "tg", "приветствие ушло в русскую ветку: %s" % text)


def test_smalltalk_skips_rag():
    """Приветствие не юридический вопрос: BM25 цеплял «салом» за «салам» в НК РТ."""
    for text in ("салом", "Салом!", "ассалому алейкум", "привет", "добрый день", "рахмат"):
        check(ls.is_smalltalk(text), "не распознано приветствие: %s" % text)


def test_short_legal_question_is_not_smalltalk():
    """Принять короткий вопрос за приветствие — остаться без опоры на закон."""
    for text in ("срок договора", "Как расторгнуть договор?", "модда 45", "статья 41"):
        check(not ls.is_smalltalk(text), "юридический вопрос принят за приветствие: %s" % text)


def test_falls_back_when_no_signal():
    """Без признаков языка берётся значение по умолчанию, а не случайный выбор."""
    check(ls.detect_lang("", default="tg") == "tg", "пустая строка проигнорировала default")
    check(ls.detect_lang("hello world", default="tg") == "tg", "латиница проигнорировала default")
    check(ls.detect_lang("2026", default="ru") == "ru", "цифры проигнорировали default")


def test_rag_reference_line_survives_round_trip():
    """Структурная строка ссылки обязана дойти до пользователя дословно."""
    text = "ПРОВЕРЕННАЯ ССЫЛКА RAG: Трудовой кодекс РТ, Глава 4, Статья 42"
    masked, saved = ls.protect_citations("Вывод. " + text)
    check(text not in masked, "строка ссылки не была защищена от перевода")
    check(ls.restore_citations(masked, saved) == "Вывод. " + text, "строка ссылки не восстановилась")
    check(ls.has_lost_citations("Хулоса.", saved), "потеря строки ссылки не замечена")


def test_code_abbreviations_are_protected():
    """«ТК РТ» NLLB превращала то в «Кодекси давлатии меҳнат», то в «ТТ»."""
    masked, saved = ls.protect_citations("Согласно статье 40 ТК РТ и главе 62 ГК РТ.")
    check("ТК РТ" not in masked and "ГК РТ" not in masked, "аббревиатуры не защищены: %s" % masked)
    check(saved == ["ТК РТ", "ГК РТ"], "защищено не то: %s" % saved)
    check("статье 40" in masked, "номер статьи не должен маскироваться: %s" % masked)


def test_dropped_placeholder_is_repaired():
    """Маркер, выброшенный переводчиком, дописывается: наблюдалось на «СК РТ»."""
    saved = ["СК РТ"]
    check(ls.repair_citations("Талоқ бо моддаи 20 танзим карда мешавад.", saved)
          == "Талоқ бо моддаи 20 танзим карда мешавад. (СК РТ)",
          "потерянная аббревиатура не дописана")
    whole = "Талоқ бо моддаи 20 СК РТ танзим карда мешавад."
    check(ls.repair_citations(whole, saved) == whole, "целый перевод был изменён")


def test_article_numbers_are_not_masked():
    """Маска на «статья 45» снята намеренно: NLLB выбрасывала плейсхолдер целиком,
    и номер статьи исчезал из ответа. Теперь его переносит сам перевод."""
    masked, saved = ls.protect_citations("Согласно статье 45 срок 30 дней.")
    check(masked == "Согласно статье 45 срок 30 дней.", "номер статьи всё ещё маскируется")
    check(saved == [], "в защиту попало лишнее: %s" % saved)


def test_lost_number_is_detected():
    """Потерянное число — главный риск перевода юридического текста."""
    source = "Согласно статье 41 срок 14 дней."
    check(ls.lost_numbers(source, "Мувофиқи моддаи 41 мӯҳлати 14 рӯз.") == [],
          "ложная тревога на целом переводе")
    check(ls.lost_numbers(source, "Мувофиқи моддаи 41 мӯҳлат.") == ["14"],
          "потеря числа не замечена")
    check(ls.lost_numbers("не более 5000 сомони", "на бештар аз 5 ҳазор сомонӣ") == ["5000"],
          "число, переписанное словами, не замечено")


def test_lost_number_is_repaired():
    """Реквизит дописывается в скобках: молча пропавший номер хуже некрасивого."""
    source = "Согласно статье 41 срок 14 дней."
    repaired = ls.repair_numbers(source, "Мувофиқи моддаи 41 мӯҳлат.")
    check("14" in repaired, "потерянное число не дописано: %s" % repaired)

    repaired = ls.repair_numbers("Штраф 5000 сомони", "Ҷарима 5 ҳазор сомонӣ")
    check("5000" in repaired, "переписанное словами число не дописано: %s" % repaired)

    clean = "Мувофиқи моддаи 41 мӯҳлати 14 рӯз."
    check(ls.repair_numbers(source, clean) == clean, "целый перевод был изменён")


def test_search_query_gets_code_wording():
    """NLLB даёт «отменить договор», а в кодексе это «расторжение»/«прекращение».
    Без подсказки вопрос о трудовом договоре поднимал статью об отмене усыновления."""
    query = ls.normalize_search_query("Как отменить трудовой договор?")
    check("расторжение" in query, "формулировка кодекса не добавлена: %s" % query)
    check("отменить" in query, "перевод был подменён, а не дополнен: %s" % query)


def test_search_query_untouched_without_trigger():
    """Подсказка добавляется только по совпадению пары слов, а не всем подряд."""
    for query in ("Что такое право на наследство?", "Отмена усыновления ребенка"):
        check(ls.normalize_search_query(query) == query, "запрос изменён напрасно: %s" % query)


def test_markdown_prefix_is_preserved():
    """Маркер списка — разметка, а не слово: переводчик не должен его видеть."""
    cases = [
        ("- по статье 45", "- ", "по статье 45"),
        ("1) уведомить за 30 дней", "1) ", "уведомить за 30 дней"),
        ("## Заголовок", "## ", "Заголовок"),
        ("обычная строка", "", "обычная строка"),
    ]
    for line, expected_prefix, expected_body in cases:
        prefix, body = ls.split_line_prefix(line)
        check(prefix == expected_prefix, "неверный префикс у %r: %r" % (line, prefix))
        check(body == expected_body, "неверное тело у %r: %r" % (line, body))


def test_emphasis_is_stripped():
    """Звёздочки выделения выбрасываются: NMT переставит слова и обнимет не то."""
    check(ls.strip_emphasis("**Расторжение** договора") == "Расторжение договора",
          "жирный текст не очищен")
    check(ls.strip_emphasis("`код` и *курсив*") == "код и курсив", "код/курсив не очищены")


def test_sentence_split_keeps_content():
    """Разбиение на предложения не должно терять текст."""
    text = "Первое предложение. Второе предложение! Третье?"
    parts = ls.split_sentences(text)
    check(len(parts) == 3, "ожидалось 3 предложения, получено %d" % len(parts))
    for word in ("Первое", "Второе", "Третье"):
        check(any(word in part for part in parts), "потеряно предложение с %s" % word)


def test_citation_abbreviations_are_expanded_for_tajik():
    """«ст. 67» переводчик читал как страницу, «ч. 1 ст. 327» терял часть."""
    out = ls.prepare_for_tajik("Срок подачи жалобы (ч. 1 ст. 327 ГПК РТ), ст. 81, п. 3, гл. 13, ст. ст. 5, 6.")
    check("часть 1 статьи 327" in out, "не раскрыто «ч. 1 ст. 327»: %s" % out)
    check("статья 81" in out and "пункт 3" in out and "глава 13" in out, "не раскрыты ст./п./гл.: %s" % out)
    check("статьи 5" in out, "не раскрыто «ст. ст.»: %s" % out)
    check("ст." not in out and "ч." not in out, "остались сокращения: %s" % out)
    check("ГПК РТ" in out, "аббревиатура кодекса пропала: %s" % out)
    check(ls.prepare_for_tajik("Стоимость 5 ст. рублей") == "Стоимость 5 ст. рублей",
          "сокращение без номера трогать нельзя")


def test_dash_and_penalty_term_are_prepared_for_tajik():
    out = ls.prepare_for_tajik("Срок \u2014 три года. Суд вправе уменьшить неустойку.")
    check(" - " in out and "\u2014" not in out, "тире не заменено: %s" % out)
    for form in ("неустойка", "неустойку", "неустойки", "неустойке", "неустойкой", "Неустойкой", "неустоек"):
        check("неустой" not in ls.prepare_for_tajik("Взыскана %s за просрочку." % form)
              and "неусто" not in ls.prepare_for_tajik("Взыскана %s за просрочку." % form),
              "не заменена форма: " + form)
    check("штраф" in ls.prepare_for_tajik("Взыскана неустойка"), "нет замены на «штраф»")
    check(ls.prepare_for_tajik("Обычный текст без терминов.") == "Обычный текст без терминов.",
          "обычный текст изменён")
    check(ls.prepare_for_tajik("Договор \u2014 письменный") == "Договор - письменный", "тире внутри фразы")


def test_known_translation_glitches_are_polished():
    check(ls.polish_tajik("Манбаъ: ТК РТ, саҳифа 67.") == "Манбаъ: ТК РТ, моддаи 67.",
          "«страница» вместо статьи не исправлена")
    check("Кодекси граждании Ҷумҳурии" in ls.polish_tajik("Кодекси шаҳрвандии Ҷумҳурии Тоҷикистон"),
          "название ГК не исправлено")
    check(ls.polish_tajik("Мурофиаи шаҳрвандӣ") == "Мурофиаи шаҳрвандӣ", "чужое «шаҳрвандӣ» не должно меняться")
    check(ls.polish_tajik("меъёрҳои ТТ ва ТР.") == "меъёрҳои ҶТ ва ҶТ.", "«ТТ»/«ТР» не заменены")
    check(ls.polish_tajik("ТК РТ, ТРАНС") == "ТК РТ, ТРАНС", "слова с «ТР» внутри трогать нельзя")
    check(ls.polish_tajik("Мӯҳлат  се сол  .") == "Мӯҳлат се сол.", "пробелы не приведены в порядок")
    check(ls.polish_tajik("Ҷавоб: 40 соат") == "Ҷавоб: 40 соат", "нормальный текст изменён")


def test_appended_citation_context_is_tajik():
    check(ls.localize_citation("Статья 81, Глава 13, ч. 2, п. 3", "tg") == "моддаи 81, боби 13, қисми 2, банди 3",
          "части ссылки не переведены")
    check(ls.localize_citation("ст. 214", "ru") == "ст. 214", "русский текст менять нельзя")
    repaired = ls.repair_numbers("Согласно статье 41 срок 14 дней", "Мувофиқи меъёр мӯҳлат.", "tg")
    check("моддаи 41" in repaired and "Статья" not in repaired and "статье" not in repaired,
          "приписка потерянного номера осталась по-русски: %s" % repaired)
    check(ls.repair_numbers("Согласно статье 41", "Мувофиқи меъёр.", "ru").endswith("(статье 41)"),
          "для русского приписка не должна меняться")


def test_all_chat_codes_are_masked_from_the_translator():
    masked, saved = ls.protect_citations(
        "ЭПК РТ, КАТ РТ, ПКоАП РТ, КоАПр РТ, УПК РТ, ГК РТ, ЖК РТ, ЛК РТ, ВдК РТ, ВздК РТ, ГрК РТ, КЗ РТ.")
    check(len(saved) == 12, "замаскированы не все кодексы: %d из 12 (%s)" % (len(saved), saved))
    check("РТ" not in masked, "аббревиатуры остались в тексте для перевода: %s" % masked)
    masked, saved = ls.protect_citations("Закон РТ и нормы РТ остаются обычным текстом.")
    check(saved == [], "«Закон РТ» не аббревиатура кодекса: %s" % saved)


def test_translator_degrades_gracefully():
    """Отсутствующая модель отключает перевод, а не роняет чат."""
    translator = ls.Translator(model_dir=os.path.join("не", "существует"))
    check(translator.available() is False, "переводчик не распознал отсутствие модели")
    check(bool(translator.reason), "причина недоступности не заполнена")
    check(translator.translate(["текст"], "ru", "tg") is None, "перевод не вернул None")
    check(translator.translate_text("текст", "ru", "tg") is None, "translate_text не вернул None")
    check(translator.translate(["текст"], "ru", "ru") == ["текст"], "перевод в тот же язык сломан")


def main() -> int:
    tests = [value for name, value in sorted(globals().items()) if name.startswith("test_")]
    for test in tests:
        try:
            test()
        except Exception:  # noqa: BLE001
            FAILURES.append(f"{test.__name__}: исключение\n{traceback.format_exc()}")

    if FAILURES:
        print(f"ПРОВАЛЕНО {len(FAILURES)} проверок из {len(tests)} тестов:")
        for failure in FAILURES:
            print("  ✗", failure)
        return 1
    print(f"OK: {len(tests)} тестов пройдено")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
