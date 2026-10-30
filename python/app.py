# -*- coding: utf-8 -*-
from flask import Flask, request, Response, jsonify, stream_with_context, send_from_directory
from flask_cors import CORS
import requests
import json
import os
import sys
import hashlib
import time
import queue
import threading
import traceback
import re
import difflib
import math
import shutil
import subprocess
import uuid
from collections import Counter
from concurrent.futures import ThreadPoolExecutor, wait, FIRST_COMPLETED
from docx import Document
from docx.shared import Pt
from pypdf import PdfReader
from zipfile import ZipFile
import xml.etree.ElementTree as ET

# Консоль и перенаправленный вывод должны переживать эмодзи/кириллицу в cp1251.
for _stream in (sys.stdout, sys.stderr):
    try:
        _stream.reconfigure(encoding="utf-8", errors="replace")
    except Exception:
        pass

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import legal_rag  # noqa: E402  — движок поиска по кодексам РТ
import lang_service  # noqa: E402  — определение языка вопроса и перевод ru<->tg
import chat_modes  # noqa: E402  — роли (юрист/адвокат), глубина ответа, документы в чате
import contract_topics  # noqa: E402  — темы условий договора -> нужные статьи кодексов
import doc_lang  # noqa: E402  — язык документа: перевод договора и отчёта (таджикский <-> русский)
import moot_court  # noqa: E402  — симулятор суда: дела, баллы, вердикт
import article_graph  # noqa: E402  — граф связей статей кодексов
import risk_map  # noqa: E402  — карта рисков договора: привязка находок к абзацам
import codex_catalog  # noqa: E402  — кодексы из RAG-индекса для страницы /contract/codex
import doc_dialog  # noqa: E402  — вопросы перед составлением документа: какой договор и с кем

# Сервис раньше запускался только с переменными окружения, заданными вручную
# в шелле: .env в корне проекта Laravel никогда не читался, и его значения
# (OLLAMA_URL, LEGAL_MODEL_NAME и т.д.) совпадали со значениями по умолчанию
# в коде чисто случайно. Без явной загрузки .env новые переменные
# (LLM_PROVIDER, OPENAI_API_KEY, GPT_MODEL) остались бы невидимыми процессу.
try:
    from dotenv import load_dotenv
    load_dotenv(os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", ".env"))
except ImportError:
    pass

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})

# ================= НАСТРОЙКИ =================
OLLAMA_URL = os.environ.get("OLLAMA_URL", "http://localhost:11434/api/generate")
# The single local legal profile is used by both contract analysis and chat.
MODEL_NAME = os.environ.get("LEGAL_MODEL_NAME", "legal-strong")

# LLM_PROVIDER переключает бэкенд генерации для анализа, чата, генератора и
# конструктора договора, а также квалификации по УК РТ. "ollama" (по
# умолчанию) — локальные модели как раньше. "openai" — OpenAI Chat
# Completions API: тег модели Ollama (legal-strong/legal-fast/...) в этом
# режиме не используется, вместо него всегда идёт OPENAI_MODEL. Поиск по
# кодексам (BM25/эмбеддинги) провайдер не затрагивает — это отдельный слой.
LLM_PROVIDER = (os.environ.get("LLM_PROVIDER") or "ollama").strip().lower()
# Единственное название ИИ, которое видят пользователи и публичные ответы API.
AI_PUBLIC_NAME = "LegalAI Pro"
OPENAI_API_KEY = os.environ.get("OPENAI_API_KEY", "")
OPENAI_BASE_URL = (os.environ.get("OPENAI_BASE_URL") or "https://api.openai.com/v1").rstrip("/")
OPENAI_MODEL = os.environ.get("GPT_MODEL") or os.environ.get("OPENAI_MODEL") or "gpt-5-nano"
# Анализ договора, генератор договора и квалификация по УК РТ идут на
# gpt-6-luna независимо от общей GPT_MODEL (её используют чат и сравнение).
# Действует только при LLM_PROVIDER=openai; на Ollama остаются локальные модели.
ANALYSIS_GPT_MODEL = os.environ.get("ANALYSIS_GPT_MODEL") or "gpt-6-luna"
DRAFT_GPT_MODEL = os.environ.get("DRAFT_GPT_MODEL") or "gpt-6-luna"
CRIME_GPT_MODEL = os.environ.get("CRIME_GPT_MODEL") or "gpt-6-luna"
CHUNK_SIZE = 8000
CHUNK_OVERLAP = 500
MAX_WORKERS = 1
ANALYSIS_VERSION = "analysis-v16-improved-lawyer"
TEMP_DIR = "tmp_uploads"
RESULTS_DIR = "analysis_results"
PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
CONTRACT_RAG_SOURCE_NAME = "1918_ru_1.docx"
CONTRACT_RAG_MIN_CHUNKS = 600
RAG_TOP_K = int(os.environ.get("RAG_TOP_K", "12"))
RAG_SNIPPET_MAX = 700
CHAT_RAG_TOP_K = int(os.environ.get("CHAT_RAG_TOP_K", "6"))
CHAT_RAG_MAX_CHARS = int(os.environ.get("CHAT_RAG_MAX_CHARS", "9000"))
CHAT_MAX_TOKENS = int(os.environ.get("CHAT_MAX_TOKENS", "850"))
CHAT_KEEP_ALIVE = os.environ.get("CHAT_KEEP_ALIVE", "30m")
# Отдельно от MODEL_NAME: тот держит legal-strong (num_ctx 16384) ради анализа
# длинных договоров, а чат по умолчанию должен идти на более лёгком профиле
# (num_ctx 8192). Общий MODEL_NAME здесь не подходит — на машине без GPU две
# разные модели в памяти одновременно означают своп (см. LEGAL_MODEL.md).
CHAT_MODEL_NAME = os.environ.get("CHAT_MODEL_NAME", "legal-fast")

# Генератор договора на /contract/create. Лимиты выше чатовых: договор из
# восьми-десяти разделов в 850 токенов не помещается, а в контекст уходят
# форма, критерии пользователя и фрагменты кодексов.
CONTRACT_DRAFT_MODEL = os.environ.get("CONTRACT_DRAFT_MODEL", "legal-draft")
# Восемь фрагментов и 7000 символов давали промпт под 3500 токенов. Разбор
# промпта на этом железе идёт 11-100 токенов/с в зависимости от того, сколько
# свободно памяти, то есть только на чтение запроса уходило от 35 секунд до
# нескольких минут. Пять фрагментов покрывают договор не хуже: лишние статьи
# модель всё равно не цитировала.
# Четыре фрагмента вместо пяти и 3200 символов вместо 4500. Разбор запроса
# идёт 11-104 токена/с в зависимости от свободной памяти, и фрагменты кодексов
# составляли основную часть промпта. На контрольном договоре модель цитировала
# две-три статьи из пяти переданных — остальные оплачивались временем впустую.
CONTRACT_DRAFT_TOP_K = int(os.environ.get("CONTRACT_DRAFT_TOP_K", "4"))
CONTRACT_DRAFT_RAG_CHARS = int(os.environ.get("CONTRACT_DRAFT_RAG_CHARS", "3200"))
CONTRACT_DRAFT_MAX_TOKENS = int(os.environ.get("CONTRACT_DRAFT_MAX_TOKENS", "2600"))

_rag_source_env = os.environ.get("RAG_SOURCE_PATH")
_rag_index_env = os.environ.get("RAG_INDEX_FILE")
RAG_SOURCE_PATH = (
    _rag_source_env if _rag_source_env and os.path.isabs(_rag_source_env)
    else os.path.join(PROJECT_ROOT, _rag_source_env or os.path.join("storage", "app", "rag", CONTRACT_RAG_SOURCE_NAME))
)
RAG_INDEX_FILE = (
    _rag_index_env if _rag_index_env and os.path.isabs(_rag_index_env)
    else os.path.join(PROJECT_ROOT, _rag_index_env or os.path.join("storage", "app", "rag", "1918_ru_1.index.json"))
)
# Нарезка больше не идёт по числу символов: фрагмент — это статья кодекса.
# Порог деления длинной статьи задаётся в legal_rag.MAX_ARTICLE_CHARS.
CHAT_RAG_SOURCE_NAMES = [
    CONTRACT_RAG_SOURCE_NAME,
    "574_ru.doc",
    "семейный кодекс.docx",
    "налоговый кодекс.docx",
    "трудовой кодекс.docx",
    "автомабильный кодекс.docx",
    # Добавлены позже первоначального списка — файлы лежали в storage/app/rag
    # непроиндексированными: не значились здесь, значит не попадали ни в
    # индекс чата, ни (после переключения ниже) в контекст анализа договоров.
    "Водный кодекс.docx",
    "Воздушный кодекс.docx",
    "Градостроительный кодекс.docx",
    "Жилишный Кодекс.docx",
    "Кодекс здравохранение.docx",
    "Лесной кодекс.docx",
    # Лежали в storage/app/rag, но в список не были внесены: чат их не видел,
    # а вопросы про административные процедуры и споры в экономических судах
    # уходили в Гражданский и Налоговый кодексы.
    "КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРОЦЕДУРАХ.docx",
    "ЭКОНОМИЧЕСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx",
    "ГРАЖДАНСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx",
    "УГОЛОВНО - ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx",
    # Размеры штрафов КоАП РТ: справочник, собранный из официального текста (python/tools/build_koap_docx.py).
    "КоАП РТ — размеры штрафов.docx",
    # Официальные тексты с base.mmk.tj на таджикском, переведённые машинно (python/tools/translate_official_code.py).
    "Конституция РТ (машинный перевод).docx",
    "Земельный кодекс (машинный перевод).docx",
    "Уголовно-исполнительный кодекс (машинный перевод).docx",
    "Таможенный кодекс (машинный перевод).docx",
    # Процессуальный кодекс: порядок производства по делам, без размеров
    # штрафов. Материальной части (санкций КоАП) в базе по-прежнему нет.
    "ПРОЦЕССУАЛЬНЫЙ КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРАВОНАРУШЕНИЯХ.docx",
]
RAG_CODE_LABELS = {
    CONTRACT_RAG_SOURCE_NAME: "Гражданский кодекс Республики Таджикистан",
    "574_ru.doc": "Уголовный кодекс Республики Таджикистан",
    "семейный кодекс.docx": "Семейный кодекс Республики Таджикистан",
    "налоговый кодекс.docx": "Налоговый кодекс Республики Таджикистан",
    "трудовой кодекс.docx": "Трудовой кодекс Республики Таджикистан",
    "автомабильный кодекс.docx": "Кодекс автомобильного транспорта Республики Таджикистан",
    "Водный кодекс.docx": "Водный кодекс Республики Таджикистан",
    "Воздушный кодекс.docx": "Воздушный кодекс Республики Таджикистан",
    "Градостроительный кодекс.docx": "Градостроительный кодекс Республики Таджикистан",
    "Жилишный Кодекс.docx": "Жилищный кодекс Республики Таджикистан",
    "Кодекс здравохранение.docx": "Кодекс Республики Таджикистан о здравоохранении",
    "Лесной кодекс.docx": "Лесной кодекс Республики Таджикистан",
    "КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРОЦЕДУРАХ.docx": "Кодекс об административных процедурах Республики Таджикистан",
    "ЭКОНОМИЧЕСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx": "Экономический процессуальный кодекс Республики Таджикистан",
    "ГРАЖДАНСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx": "Гражданский процессуальный кодекс Республики Таджикистан",
    "УГОЛОВНО - ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx": "Уголовно-процессуальный кодекс Республики Таджикистан",
    "КоАП РТ — размеры штрафов.docx": "Кодекс Республики Таджикистан об административных правонарушениях (размеры штрафов)",
    "Конституция РТ (машинный перевод).docx": "Конституция Республики Таджикистан (машинный перевод официального текста)",
    "Земельный кодекс (машинный перевод).docx": "Земельный кодекс Республики Таджикистан (машинный перевод официального текста)",
    "Уголовно-исполнительный кодекс (машинный перевод).docx": "Уголовно-исполнительный кодекс Республики Таджикистан (машинный перевод официального текста)",
    "Таможенный кодекс (машинный перевод).docx": "Таможенный кодекс Республики Таджикистан (машинный перевод официального текста)",
    "ПРОЦЕССУАЛЬНЫЙ КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРАВОНАРУШЕНИЯХ.docx": "Процессуальный кодекс Республики Таджикистан об административных правонарушениях",
}

# Файлы-дубликаты: тот же кодекс лежит в папке под другим именем. Индексировать
# оба нельзя (результаты задвоятся), а в статусе такой файл не должен числиться
# «непроиндексированным».
RAG_IGNORED_FILES = {
    "Кодекс об административных правонарушениях.docx",  # = ПРОЦЕССУАЛЬНЫЙ КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРАВОНАРУШЕНИЯХ.docx
}

# Процессуальные кодексы описывают порядок производства, а не ответственность,
# поэтому в блок «смежные нормы» страницы УК (нормы об ответственности и
# возмещении вреда) они не попадают.
RELATED_EXCLUDED_SOURCES = {
    "574_ru.doc",
    "КоАП РТ — размеры штрафов.docx",   # страница УК остаётся строго уголовной
    "Уголовно-исполнительный кодекс (машинный перевод).docx",
    "КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРОЦЕДУРАХ.docx",
    "ЭКОНОМИЧЕСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx",
    "ГРАЖДАНСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx",
    "УГОЛОВНО - ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx",
    "ПРОЦЕССУАЛЬНЫЙ КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРАВОНАРУШЕНИЯХ.docx",
}
CHAT_RAG_INDEX_FILE = os.path.join(PROJECT_ROOT, "storage", "app", "rag", "laws-rt-chat.index.json")

# Страница /contract/ugalov-kodex отвечает только по Уголовному кодексу РТ.
# Поиск по общему индексу для неё не годится: на «вырвал сумку и убежал»
# он поднимал статьи Гражданского и Налогового кодексов, и уголовной нормы
# в выдаче не оказывалось вовсе.
CRIME_RAG_SOURCE_NAME = "574_ru.doc"
CRIME_RAG_TOP_K = int(os.environ.get("CRIME_RAG_TOP_K", "8"))
# Порог BM25, ниже которого совпадение считается шумом. Подобран по контрольным
# запросам: описание преступления даёт 20-130, посторонний вопрос («какая
# погода в Душанбе») — 5 и ниже. Без порога модель получала бы случайные
# статьи и отвечала бы по ним как по найденным.
CRIME_RAG_MIN_SCORE = float(os.environ.get("CRIME_RAG_MIN_SCORE", "8.0"))
# Бюджет промпта полного разбора. Замер на этом железе: 2023 токена промпта
# разбирались 116 секунд (17,5 ток/с), и почти весь промпт — фрагменты
# кодекса. Три статьи вместо восьми покрывают квалификацию не хуже: по
# контрольным описаниям профильная норма приходит первой с отрывом втрое,
# а ссылалась модель всё равно на одну-две. Поиску выдача не режется.
CRIME_RAG_USE_VECTORS = os.environ.get("CRIME_RAG_USE_VECTORS", "0").strip().lower() in {"1", "true", "yes", "on"}
CRIME_QUALIFY_TOP_K = int(os.environ.get("CRIME_QUALIFY_TOP_K", "3"))
CRIME_MAX_TOKENS = int(os.environ.get("CRIME_MAX_TOKENS", "700"))
# Краткая квалификация: статья, наказание и пара предложений — вчетверо короче полной.
CRIME_MAX_TOKENS_SHORT = int(os.environ.get("CRIME_MAX_TOKENS_SHORT", "650"))
# Полная квалификация: «что делать» плюс шесть разделов разбора. В 700 токенов
# такой ответ не помещается, поэтому у режима свой, больший бюджет.
# У облачной модели (gpt-6-luna) часть бюджета уходит на рассуждение, поэтому лимит выше.
CRIME_MAX_TOKENS_FULL = int(os.environ.get("CRIME_MAX_TOKENS_FULL", "4000" if LLM_PROVIDER == "openai" else "1500"))
# Контекст под промпт разбора, а не объявленные моделью 16k: на CPU лишний
# контекст оплачивается памятью и скоростью разбора запроса.
CRIME_NUM_CTX = int(os.environ.get("CRIME_NUM_CTX", "6144"))
CRIME_KEEP_ALIVE = os.environ.get("CRIME_KEEP_ALIVE", "30m")
# Как часто слать признак жизни, пока модель молчит: молчащий поток
# обрывается клиентом раньше, чем приходит первый токен.
CRIME_HEARTBEAT_SECONDS = float(os.environ.get("CRIME_HEARTBEAT_SECONDS", "5"))
CRIME_MODEL = os.environ.get("CRIME_MODEL", MODEL_NAME)
# 2600 символов на три статьи (по ~670 на статью) годятся для медленной локальной модели, но режут
# санкции и части статей: модель видела обрубок ст. 245 и выбирала часть 1 для хищения на 400 000 сомони.
# Облачной модели контекст не дорог, поэтому она получает статьи целиком.
CRIME_RAG_MAX_CHARS = int(os.environ.get("CRIME_RAG_MAX_CHARS", "15000" if LLM_PROVIDER == "openai" else "2600"))
# Действующий «показатель для расчётов» в сомони: значительный, крупный и особо крупный размер в УК РТ
# определяются кратно ему. Значение меняется законом о бюджете, поэтому задаётся в .env, а не зашито в код.
CRIME_CALC_INDICATOR = (os.environ.get("CRIME_CALC_INDICATOR") or "").strip()
CRIME_NOT_FOUND_MESSAGE = (
    "В Уголовном кодексе Республики Таджикистан не найдено норм, "
    "относящихся к этому описанию. Опишите событие подробнее: что именно "
    "произошло, каким способом, чем причинён вред. Ответ вне текста УК РТ "
    "система не даёт."
)
RAG_CODE_NAME = os.environ.get("RAG_CODE_NAME", "Гражданский кодекс Республики Таджикистан")
LEARNING_DATA_DIR = os.path.join(PROJECT_ROOT, "python", "training_data")
LEARNING_DATA_FILE = os.path.join(LEARNING_DATA_DIR, "legal-approved.jsonl")

_current_rag_source = None
_current_rag_index = None

WRONG_JURISDICTION_PENALTY = 20
HALLUCINATION_PENALTY = 25

os.makedirs(TEMP_DIR, exist_ok=True)
os.makedirs(RESULTS_DIR, exist_ok=True)
os.makedirs(os.path.dirname(RAG_INDEX_FILE), exist_ok=True)
os.makedirs(LEARNING_DATA_DIR, exist_ok=True)

active_sessions = {}
sessions_lock = threading.Lock()
rag_index = None
chat_rag_index = None
crime_rag_index = None
related_rag_indexes = {}
rag_lock = threading.Lock()
rag_articles_cache = None
rag_citations_cache = None
multi_rag_citations_cache = {}
rag_embeddings = None
chat_rag_embeddings = None
embedding_provider = legal_rag.make_embedding_provider()

def _active_model_tag(requested_model=None):
    """Effective model id for the active provider.

    Ollama tags (legal-strong, legal-fast, ...) don't exist on OpenAI, so a
    provider switch always overrides them with OPENAI_MODEL — otherwise a
    cache entry written under "openai" could be reused for "ollama" (or vice
    versa) despite the two producing different answers for the same prompt.
    """
    if LLM_PROVIDER == "openai":
        return "openai:" + OPENAI_MODEL
    return requested_model or MODEL_NAME

# ================= КЛАССИФИКАТОР ТИПА ДОГОВОРА =================
CONTRACT_TYPE_KEYWORDS = {
    "трудовой договор": [
        "трудовой договор", "работодатель", "работник", "трудовую функцию", "трудовая функция",
        "испытательный срок", "рабочее время", "ежегодный оплачиваемый отпуск", "заработная плата",
        "должность",
    ],
    "купля-продажа": [
        "купли-продажи", "продавец", "покупатель", "товар", "передаёт в собственность",
        "price", "товара", "количество", "ассортимент", "качество товара",
    ],
    "аренда": [
        "аренды", "арендатор", "арендодатель", "арендную плату", "арендная плата",
        "имущество во временное владение", "объект аренды", "субаренд",
        # Наём жилого помещения — тот же по сути договор (возмездное временное
        # пользование имуществом), но по-другому называет стороны. Без этих
        # ключей текст с «наймодатель»/«наниматель»/«наём» набирал 0 очков по
        # аренде, и договор найма квартиры классификатор относил к другому
        # типу по случайному совпадению постороннего слова.
        "наймодатель", "наниматель", "найма жилого", "плата за наём",
    ],
    "подряд": [
        "подряда", "подрядчик", "заказчик", "строительн", "проектную документацию",
        "смету", "работу", "результат работы", "сдать работу", "принять работу",
    ],
    "возмездное оказание услуг": [
        "оказания услуг", "услуг", "исполнитель", "заказчик", "оказать услуги",
        "качество услуг", "акт оказанных услуг",
    ],
    "поставка": [
        "поставки", "поставщик", "покупатель", "поставку товара", "период поставки",
        "график поставки", "парти",
    ],
    "займ": [
        "займа", "займ", "заёмщик", "займодавец", "сумму займа", "проценты",
        "возврат суммы займа",
    ],
    "кредитный": [
        # "банк" убран: он входит в "банковский перевод"/"банковские реквизиты",
        # обычные слова в оплате практически любого договора. Реальный
        # договор найма жилья с оплатой «банковским переводом» набирал по
        # этому одному совпадению 31% и подписывался в отчёте как «кредитный».
        "кредита", "кредитный", "кредитор", "заёмщик", "кредитную",
        "процентную ставку", "график погашения",
    ],
    "хранение": [
        "хранения", "хранитель", "поклажедатель", "вещь", "сохранность",
    ],
    "поручение": [
        "поручения", "поверенный", "доверитель", "от имени", "юридические действия",
    ],
    # Поручительство (гарантия исполнения чужого долга) — отдельный от
    # "поручения" (агентского по сути договора) институт. Без своей записи он
    # проваливался в "кредитный" по единственному слову "кредитор" и получал
    # чек-лист продавца — в том числе высокий риск «отсутствует цена», хотя
    # у поручительства нет цены в смысле купли-продажи вовсе.
    "поручительство": [
        "поручитель", "поручительства", "отвечать перед кредитором",
        "солидарн", "должник",
    ],
    "комиссия": [
        "комиссии", "комиссионер", "комитент", "сделк", "вознаграждение",
    ],
    "агентский": [
        "агентского", "агент", "принципал", "агентировани",
    ],
    "доверительное управление": [
        "доверительного управления", "доверительный управляющий", "учредитель управления",
    ],
    "франчайзинг": [
        "франчайзинг", "коммерческой концессии", "франчайзи", "франчайзер",
        "правообладатель", "пользователь",
    ],
    "страхование": [
        "страхования", "страховщик", "страхователь", "страховой случай", "страховую премию",
    ],
    "лизинг": [
        "лизинга", "лизингодатель", "лизингополучатель", "предмет лизинга",
    ],
    "залог": [
        "залога", "залогодатель", "залогодержатель", "предмет залога", "заложенное имущество",
    ],
    "дарение": [
        "дарения", "даритель", "одаряемый", "безвозмездно",
    ],
    "мены": [
        "мены", "обмениваем", "обмен",
    ],
    "авторский заказ": [
        "авторского заказа", "автор", "заказчик", "произведение",
    ],
    "отчуждение исключительного права": [
        "отчуждения исключительного права", "исключительное право", "правообладатель",
        "приобретатель",
    ],
    "лицензионный": [
        "лицензионного", "лицензиар", "лицензиат", "право использования", "лицензию",
    ],
    "простой товарищества": [
        "простого товарищества", "совместной деятельности", "совместной организационной деятельности",
        "товарищи", "инвестиц", "инвестор", "вклад", "прибыль",
    ],
}

# Тип, названный в самом заголовке договора, важнее подсчёта слов по тексту: в «Договоре субподряда и совместной
# деятельности» слова про подряд и услуги встречаются чаще, и без этого договор получал нормы о подряде, а не о
# совместной деятельности. Ищется только в начале текста (заголовок и преамбула).
TITLE_TYPE_PATTERNS = {
    "простой товарищества": re.compile(r"совместн\w+ (?:организационн\w+ )?деятельност|простого товарищества", re.IGNORECASE),
}
TITLE_TYPE_SCAN_CHARS = 400
TITLE_TYPE_SCORE = 0.9


def classify_contract_type(text):
    """
    Определяет тип договора по ключевым словам в тексте.
    Возвращает dict: {type, confidence, types_with_scores}
    """
    if not text:
        return {"type": "неопределённый", "confidence": 0.0, "types_with_scores": []}
    
    text_lower = text.lower()
    scores = []
    
    for contract_type, keywords in CONTRACT_TYPE_KEYWORDS.items():
        score = 0
        for kw in keywords:
            # Прямой поиск без учёта окончаний
            if kw.lower() in text_lower:
                score += 1
        if score > 0:
            # Нормализуем на количество ключевых слов, чтобы получить 0-1
            normalized = score / max(len(keywords) * 0.4, 1)  # 40% совпадений = 1.0
            normalized = min(1.0, normalized)
            scores.append({"type": contract_type, "score": round(normalized, 3), "matches": score})
    
    # Сортируем по убыванию
    head = text[:TITLE_TYPE_SCAN_CHARS]
    for contract_type, pattern in TITLE_TYPE_PATTERNS.items():
        if not pattern.search(head):
            continue
        entry = next((item for item in scores if item["type"] == contract_type), None)
        if entry:
            entry["score"] = max(entry["score"], TITLE_TYPE_SCORE)
        else:
            scores.append({"type": contract_type, "score": TITLE_TYPE_SCORE, "matches": 1})

    scores.sort(key=lambda x: x["score"], reverse=True)
    
    if not scores:
        return {"type": "неопределённый", "confidence": 0.0, "types_with_scores": []}
    
    best = scores[0]
    return {
        "type": best["type"],
        "confidence": best["score"],
        "types_with_scores": scores[:5],  # Топ-5
    }

# ================= ДЕТЕКТОР НЕПРАВИЛЬНОЙ ЮРИСДИКЦИИ =================
def detect_wrong_jurisdiction(text):
    russia_patterns = [
        (r"\bГК\s*РФ\b", "ГК РФ"),
        (r"\bТК\s*РФ\b", "ТК РФ"),
        (r"\bНК\s*РФ\b", "НК РФ"),
        (r"\bКоАП\s*РФ\b", "КоАП РФ"),
        (r"\bУК\s*РФ\b", "УК РФ"),
        (r"\bГПК\s*РФ\b", "ГПК РФ"),
        (r"\bАПК\s*РФ\b", "АПК РФ"),
        (r"\bРФ\b", "РФ"),
        (r"\bРоссии\b", "России"),
        (r"\bРоссия\b", "Россия"),
        (r"\bРоссийск(?:ая|ой|ую)\s+Федераци", "Российская Федерация"),
        (r"\bГражданск\w+\s+кодекс\s+РФ", "Гражданский кодекс РФ"),
        (r"\bТрудовой\s+кодекс\s+РФ", "Трудовой кодекс РФ"),
        (r"\bНалоговый\s+кодекс\s+РФ", "Налоговый кодекс РФ"),
        (r"\bФедеральн\w+\s+закон", "Федеральный закон"),
        (r"\bФЗ\s*№", "ФЗ"),
        (r"\bАрбитражн\w+\s+суд", "Арбитражный суд"),
        (r"\bМКАС\b", "МКАС"),
        (r"\bМоскв\w+", "Москва"),
        (r"\bСанкт-Петербург\b", "Санкт-Петербург"),
        (r"\bВерховн\w+\s+Суд\s+РФ", "Верховный Суд РФ"),
        (r"\bКонституционн\w+\s+Суд\s+РФ", "Конституционный Суд РФ"),
    ]
    
    violations = []
    for pattern, name in russia_patterns:
        matches = re.finditer(pattern, text, re.IGNORECASE)
        for match in matches:
            violations.append({
                "pattern": name,
                "match": match.group(0),
                "position": match.start()
            })

    return violations

def check_wrong_jurisdiction_in_contract(contract_text):
    """Риск-находка, если САМ ДОГОВОР ссылается на право/суд другой страны —
    независимо от того, заметила ли это модель.

    detect_wrong_jurisdiction проверяет только текст, который написала
    модель. Строгий протокол доказательности запрещает ей создавать риск без
    цитируемой статьи РАГ, а гражданский кодекс РТ не обязан содержать норму
    о выборе иностранного права для конкретного фрагмента — так что для
    некоторых фрагментов у модели просто нет допустимого основания заявить
    риск, и она честно молчит. На практике это значит: один и тот же
    тестовый договор со явной ссылкой на "Гражданский кодекс Российской
    Федерации" и "Арбитражный суд города Москвы" был пойман в одном прогоне
    (5 упоминаний) и полностью пропущен в другом — текст договора не
    менялся, менялся только выбор модели, что процитировать. Выбор чужого
    права — слишком серьёзный и слишком буквально проверяемый регексом риск,
    чтобы зависеть от везения с RAG-подборкой; проверяем его напрямую.
    """
    violations = detect_wrong_jurisdiction(contract_text)
    if not violations:
        return None
    terms = sorted({v["match"] for v in violations})
    first_pos = min(v["position"] for v in violations)
    snippet = re.sub(r"\s+", " ", contract_text[max(0, first_pos - 60):first_pos + 120]).strip()
    return {
        "title": "Договор ссылается на право/суд другой страны вместо Республики Таджикистан",
        "severity": "high",
        "point": "применимое право / разрешение споров",
        "fragment": snippet,
        "norm": "Юрисдикция РТ: договор должен регулироваться правом Республики Таджикистан",
        "why": (
            "В тексте договора обнаружены прямые ссылки на законодательство или суды другой "
            "страны (" + ", ".join(terms) + "). Это делает выбор применимого права и порядок "
            "разрешения споров практически неисполнимыми для сторон в Республике Таджикистан."
        ),
        "fix": "Заменить ссылки на право и суды другой страны на законодательство и суды Республики Таджикистан.",
        # Находка проверена регулярным выражением по тексту договора: она считается полноценным риском
        # (в балле и в списке рисков), а не пунктом информационного чек-листа.
        "_deterministic": True,
    }

# ================= ПРОВЕРКА ОБОСНОВАННОСТИ ВЫВОДОВ =================
def _rag_article_numbers():
    """Return article numbers present in ANY connected RAG codex, not only the
    Civil Code.

    Раньше сканировал только load_rag_index() (Гражданский кодекс). Находка
    анализа договора, честно процитировавшая реальную статью Налогового или
    Трудового кодекса, здесь отклонялась как "отсутствует в подключённом
    RAG" — потому что в подключённом-то она как раз была, просто не в том
    единственном источнике, который эта проверка знала. Номер статьи уже
    структурное поле чанка (chunk["article"]), а не то, что нужно заново
    выковыривать регэкспом из текста.
    """
    index = load_chat_rag_index(force=False)
    return {
        str(chunk["article"]) for chunk in index.get("chunks", []) if chunk.get("article")
    }

def _cited_article_numbers(text):
    return set(
        re.findall(
            r"(?:статья|ст\.|ч\.)\s*(\d+(?:[-.]\d+)?(?:\.\d+)?)",
            text or "",
            re.IGNORECASE,
        )
    )

_QUOTE_EDGES = '«»“”„"\'‘’ \t\r\n'


def _clean_quote(text):
    """Цитата без кавычек по краям: отчёт сам берёт её в «ёлочки», а проверка ищет голый текст."""
    return str(text or "").strip(_QUOTE_EDGES)


def _repair_quote_from_contract(finding, contract_text, min_share=0.6, min_shared=3):
    """Перефразированную моделью цитату заменяет дословным предложением договора.

    Модель часто пересказывает пункт своими словами, и тогда настоящий риск удалялся как «цитата не найдена».
    Если почти все основы слов цитаты и заголовка риска есть в одном предложении договора, в отчёт идёт это
    предложение дословно. Выдуманная цитата не похожа ни на одно предложение и по-прежнему отсеивается.
    """
    quote_stems = _stems(_clean_quote(finding.get("fragment")))
    if len(quote_stems) < min_shared:
        return False
    best, best_score = None, 0.0
    for segment in re.split(r"\n+|(?<=[.;!?])\s+", str(contract_text or "")):
        segment = segment.strip()
        if len(segment) < 20:
            continue
        shared = len(quote_stems & _stems(segment))
        score = shared / len(quote_stems)
        if shared >= min_shared and score >= min_share and score > best_score:
            best, best_score = segment, score
    if not best:
        return False
    finding["fragment"] = best if len(best) <= 400 else best[:400].rsplit(" ", 1)[0] + "…"
    return True


def detect_hallucination(finding, contract_text, rag_context=""):
    """Return a reason when a finding cannot be verified by the contract and RAG."""
    quote = _normalize_text(_clean_quote(finding.get("fragment", "")))
    contract = _normalize_text(contract_text)
    if len(quote) < 15 or not _quote_matches_contract(quote, contract):
        return "цитата не найдена в тексте договора"

    cited_articles = _cited_article_numbers(finding.get("norm", ""))
    if not cited_articles:
        return "не указана конкретная статья РТ"

    available_articles = _rag_article_numbers()
    if not cited_articles.issubset(available_articles):
        return "указанная статья отсутствует в подключённом RAG"

    if detect_wrong_jurisdiction(" ".join(str(value) for value in finding.values())):
        return "обнаружена ссылка на другую юрисдикцию"
    return None

# ================= ФУНКЦИЯ ИЗВЛЕЧЕНИЯ СТАТЕЙ ИЗ RAG =================
def extract_article_numbers_from_text(text):
    """Извлекает номера статей из текста"""
    numbers = set()
    for m in re.finditer(r'(?:стать[яиюе]|ст\.)\s*(\d+)', text, re.IGNORECASE):
        numbers.add(m.group(1))
    return list(numbers)

# ================= ИЗВЛЕЧЕНИЕ ТЕКСТА =================
def extract_text_from_pdf(path):
    try:
        print(f"📄 Чтение PDF: {os.path.basename(path)}")
        reader = PdfReader(path)
        texts = []

        for i, page in enumerate(reader.pages):
            try:
                text = page.extract_text()
                if text and text.strip():
                    clean_text = text.strip()
                    texts.append(clean_text)
                    print(f"  Страница {i+1}: {len(clean_text)} символов")
            except Exception as page_err:
                print(f"  ⚠️ Страница {i+1}: {page_err}")
                continue

        if not texts:
            raise Exception("PDF не содержит извлекаемого текста")

        full_text = "\n\n".join(texts)
        print(f"  ✅ PDF: {len(full_text)} символов, {len(texts)} страниц")
        return full_text

    except Exception as e:
        raise Exception(f"Ошибка чтения PDF: {str(e)}")

def extract_text_from_docx(path):
    try:
        print(f"📄 Чтение DOCX: {os.path.basename(path)}")
        doc = Document(path)
        all_text = []

        for i, para in enumerate(doc.paragraphs):
            text = para.text.strip()
            if text:
                all_text.append(text)

        for table in doc.tables:
            for row in table.rows:
                for cell in row.cells:
                    text = cell.text.strip()
                    if text:
                        all_text.append(text)

        if not all_text:
            print("  ⚠️ Стандартное извлечение не дало результата, пробуем XML...")
            try:
                with ZipFile(path) as z:
                    with z.open('word/document.xml') as f:
                        tree = ET.parse(f)
                        root = tree.getroot()
                        ns = {'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}

                        for elem in root.findall('.//w:t', ns):
                            if elem.text and elem.text.strip():
                                all_text.append(elem.text.strip())
            except Exception as xml_err:
                print(f"  ❌ XML-парсинг не удался: {xml_err}")

        if not all_text:
            raise Exception("Документ не содержит текста или повреждён")

        full_text = "\n".join(all_text)
        print(f"  ✅ DOCX: {len(full_text)} символов, {len(all_text)} блоков")
        return full_text

    except Exception as e:
        raise Exception(f"Ошибка чтения DOCX: {str(e)}")

def extract_text_from_doc(path):
    try:
        print(f"📄 Чтение DOC: {os.path.basename(path)}")
        text = _extract_text_from_doc_command(path)
        if text and text.strip():
            print(f"  ✅ DOC (внешняя утилита): {len(text)} символов")
            return text

        text = _extract_text_from_doc_unicode(path)
        if text and text.strip():
            print(f"  ✅ DOC (unicode binary): {len(text)} символов")
            return text

        text = _extract_text_from_doc_binary(path)
        if text and len(text.strip()) > 40:
            print(f"  ✅ DOC (бинарный парсер): {len(text)} символов")
            return text

        raise Exception("Не удалось извлечь текст из DOC")
    except Exception as e:
        raise Exception(f"Ошибка чтения DOC: {str(e)}")

def _cyrillic_ratio(text):
    """Доля кириллицы среди букв. Ноль означает потерю кодировки."""
    letters = [ch for ch in (text or "") if ch.isalpha()]
    if not letters:
        return 0.0
    cyrillic = sum(1 for ch in letters if "Ѐ" <= ch <= "ӿ")
    return cyrillic / len(letters)


def _doc_text_looks_valid(text, min_cyrillic=0.30):
    """Отбраковывает извлечение, потерявшее русский текст.

    antiword без карты кодировки отдаёт кириллицу как «?»: формально
    успешный вызов возвращает 500 КБ мусора. Молча проиндексировать такой
    результат хуже, чем не проиндексировать вовсе, — поэтому проверяем.
    """
    if not text or not text.strip():
        return False
    if text.count("?") > max(len(text) * 0.02, 200):
        return False
    return _cyrillic_ratio(text) >= min_cyrillic


def _extract_text_from_doc_command(path):
    """Пробует внешние конвертеры, отбраковывая испорченную кодировку.

    Вывод читается байтами и декодируется явно: на Windows text=True берёт
    кодировку из локали (cp1251) и падает на первом же неизвестном байте.
    """
    attempts = [
        (["antiword", "-m", "cp1251.txt", path], ("cp1251", "utf-8")),
        (["antiword", "-m", "UTF-8.txt", path], ("utf-8", "cp1251")),
        (["catdoc", "-d", "utf-8", path], ("utf-8", "cp1251")),
        (["antiword", path], ("cp1251", "utf-8")),
        (["catdoc", path], ("cp1251", "utf-8")),
    ]
    for argv, encodings in attempts:
        if not shutil.which(argv[0]):
            continue
        try:
            proc = subprocess.run(argv, capture_output=True, timeout=60)
            if proc.returncode != 0:
                continue
            raw = proc.stdout or b""
            for encoding in encodings:
                output = raw.decode(encoding, errors="replace")
                if _doc_text_looks_valid(output):
                    return output.strip()
            print(f"  ⚠️ {' '.join(argv[:3])}: кириллица потеряна, пробуем следующий способ")
        except Exception as e:
            print(f"  ⚠️ Ошибка вызова {argv[0]}: {e}")
    return ""

def _extract_text_from_doc_unicode(path):
    """High-fidelity extractor for legacy binary .doc (OLE2) documents.

    No ``antiword``/``catdoc``/``olefile``/``pywin32`` is installed in this
    environment, but Word 97-2003 files keep their document text inside the
    WordDocument stream as UTF-16 (little-endian) runs. The previous approach
    tried ``cp1251`` first and bailed out on the first encoding that produced
    >=5 garbage lines, which is why the *Уголовный кодекс* index came back as
    noise. We now score every candidate encoding by Cyrillic + legal-vocabulary
    density, keep the best one, then normalise the output so chapter/article
    headers land on their own lines (the citation-catalogue builder anchors on
    ``^Статья``).
    """
    with open(path, "rb") as f:
        data = f.read()

    legal_seed = re.compile(
        r"(статья|глава|раздел|часть|пункт|кодекс|наказывается|ответственность"
        r"|преступление|санкция|уголовн)", re.IGNORECASE,
    )

    chosen = None
    for encoding in ("utf-16-le", "utf-16-be", "utf-8", "cp1251", "cp1252"):
        try:
            decoded = data.decode(encoding, errors="ignore")
        except Exception:
            continue
        cyrillic = sum(1 for ch in decoded if "\u0400" <= ch <= "\u04FF")
        hits = len(legal_seed.findall(decoded))
        ratio = cyrillic / max(len(decoded), 1)
        # Require a real legal document: meaningful Cyrillic + vocabulary.
        if cyrillic > 300 and ratio > 0.05 and hits > 0:
            chosen = (encoding, decoded)
            break

    if chosen is None:
        chosen = ("utf-16-le", data.decode("utf-16-le", errors="ignore"))
    encoding, decoded = chosen

    # Strip VFP field markers (\x13...\x15), BOM and stray control bytes.
    cleaned = re.sub(r"\x13[^\x15]*\x15", " ", decoded)
    cleaned = cleaned.replace("\ufeff", " ").replace("\u00ad", "")
    cleaned = re.sub(r"[\x00-\x08\x0b\x0c\x0e-\x1f\x7f]", " ", cleaned)

    # Put legal-structure headers on their own lines (non-consuming lookahead).
    header_re = (
        r"Статья\s+\d[\d.]*|ГЛАВА\s+\d+|РАЗДЕЛ\s+\S+|ЧАСТЬ\s+\S+|"
        r"ПОДРАЗДЕЛ\s+\d+|ПУНКТ\s+\d+"
    )
    cleaned = re.sub(
        r"(?<![А-Яа-яЁё])(?=(" + header_re + r"))",
        "\n",
        cleaned,
    )

    # Collapse horizontal whitespace, keep newlines, drop noise lines.
    cleaned = re.sub(r"[ \t]+", " ", cleaned)
    cleaned = re.sub(r"\n{2,}", "\n", cleaned)
    lines = [ln.strip() for ln in cleaned.split("\n") if ln.strip()]
    keep = []
    for ln in lines:
        cyrillic = sum(1 for ch in ln if "\u0400" <= ch <= "\u04FF")
        is_header = bool(re.match(r"(Статья|ГЛАВА|РАЗДЕЛ|ЧАСТЬ|ПОДРАЗДЕЛ|ПУНКТ)\s+\d", ln, re.IGNORECASE))
        if cyrillic > 15 or is_header:
            keep.append(ln)

    text = "\n".join(keep)
    print(f"  ✅ DOC (unicode binary, {encoding}): {len(text)} символов")
    return text


def _extract_text_from_doc_binary(path):
    with open(path, "rb") as f:
        data = f.read()

    encodings = ["cp1251", "cp1252", "utf-8", "utf-16-le", "utf-16-be"]
    for encoding in encodings:
        try:
            decoded = data.decode(encoding, errors="ignore")
        except Exception:
            continue

        clean = re.sub(r"[^\r\n\t\x20-\x7E\u0400-\u04FF\u0500-\u052F«»–—.,:;!?()\[\]{}\"'\\/\\n\\r]+", " ", decoded)
        lines = [line.strip() for line in re.split(r"[\r\n]+", clean) if len(line.strip()) > 40]
        if len(lines) >= 5:
            return "\n".join(lines)

    decoded = data.decode("latin1", errors="ignore")
    clean = re.sub(r"[^\r\n\t\x20-\x7E\u0400-\u04FF\u0500-\u052F«»–—.,:;!?()\[\]{}\"'\\/\\n\\r]+", " ", decoded)
    lines = [line.strip() for line in re.split(r"[\r\n]+", clean) if len(line.strip()) > 40]
    return "\n".join(lines[:1000])

def extract_text_from_txt(path):
    try:
        print(f"📄 Чтение TXT: {os.path.basename(path)}")
        with open(path, "r", encoding="utf-8", errors="ignore") as f:
            text = f.read()
        if not text.strip():
            raise Exception("Файл пустой")
        print(f"  ✅ TXT: {len(text)} символов")
        return text
    except Exception as e:
        raise Exception(f"Ошибка чтения TXT: {str(e)}")

# ================= УМНАЯ РАЗБИВКА =================
def split_text_smart(text, size, overlap):
    chunks = []
    start = 0

    while start < len(text):
        end = min(start + size, len(text))

        if end < len(text):
            for sep in ["\n\n", ". ", "! ", "? ", "; ", ", "]:
                pos = text.rfind(sep, start, end + overlap)
                if pos > start + size // 2:
                    end = pos + len(sep)
                    break

        chunk = text[start:end].strip()
        if chunk:
            chunks.append(chunk)

        if end < len(text):
            start = max(start + 1, end - overlap)
        else:
            break

    return chunks if chunks else [text]

# ================= RAG =================
def tokenize_for_rag(text):
    """Нормализованные токены: стемминг + отсев служебных слов."""
    return legal_rag.tokenize(text)

def discover_rag_source(source_hint=None):
    global _current_rag_source
    if _current_rag_source and os.path.exists(_current_rag_source):
        return _current_rag_source
    if source_hint:
        rag_dir = os.path.dirname(RAG_SOURCE_PATH)
        candidate = os.path.join(rag_dir, source_hint)
        if os.path.exists(candidate):
            _current_rag_source = candidate
            return candidate
    if RAG_SOURCE_PATH and os.path.exists(RAG_SOURCE_PATH):
        return RAG_SOURCE_PATH
    rag_dir = os.path.dirname(RAG_SOURCE_PATH)
    if os.path.isdir(rag_dir):
        candidates = []
        for name in os.listdir(rag_dir):
            if name.lower().endswith((".docx", ".doc", ".txt", ".pdf")):
                full = os.path.join(rag_dir, name)
                candidates.append((os.path.getmtime(full), full))
        if candidates:
            candidates.sort(reverse=True)
            return candidates[0][1]
    return None

def activate_rag_source(filename):
    """Switch the shared RAG index to a source stored in storage/app/rag."""
    global _current_rag_source, _current_rag_index, rag_index

    if not filename or os.path.basename(filename) != filename:
        raise ValueError("Invalid RAG source filename")

    rag_dir = os.path.dirname(RAG_SOURCE_PATH)
    candidate = os.path.join(rag_dir, filename)
    if not os.path.isfile(candidate):
        raise FileNotFoundError(f"RAG source not found: {filename}")

    _current_rag_source = candidate
    _current_rag_index = os.path.splitext(candidate)[0] + ".index.json"
    rag_index = None
    return candidate

def ensure_contract_rag_ready():
    """Fail closed: contract analysis may run only with the Civil Code RAG source."""
    source_path = activate_rag_source(CONTRACT_RAG_SOURCE_NAME)
    index = load_rag_index(force=False)
    if not index.get("ready") or index.get("source_path") != source_path:
        raise RuntimeError(index.get("error") or "Contract RAG index is not ready")
    if len(index.get("chunks", [])) < CONTRACT_RAG_MIN_CHUNKS:
        raise RuntimeError(
            f"Contract RAG index has {len(index.get('chunks', []))} fragments; "
            f"at least {CONTRACT_RAG_MIN_CHUNKS} are required"
        )
    return index

def extract_text_for_rag(path):
    ext = os.path.splitext(path)[1].lower()
    if ext == ".pdf":
        return extract_text_from_pdf(path)
    if ext in [".docx", ".doc"]:
        return extract_text_from_doc(path) if ext == ".doc" else extract_text_from_docx(path)
    if ext == ".txt":
        return extract_text_from_txt(path)
    raise Exception(f"Unsupported RAG source format: {ext}")

def build_rag_chunks(text, source_name=None):
    """Разбивает кодекс по границам статей, а не по смещению в символах.

    Каждый фрагмент несёт кодекс, главу и номер статьи, поэтому ссылка на
    норму берётся из метаданных и не может «съехать» при цитировании.
    """
    source = source_name or os.path.basename(discover_rag_source() or CONTRACT_RAG_SOURCE_NAME)
    code_label = RAG_CODE_LABELS.get(source, RAG_CODE_NAME)
    chunks = legal_rag.build_article_chunks(text, source, code_label)
    if not chunks:
        raise RuntimeError(f"RAG: в источнике {source} не найдено ни одной статьи")
    return chunks

def hydrate_rag_index(index):
    chunks = index.get("chunks", [])
    doc_count = max(len(chunks), 1)
    stats = legal_rag.build_stats(chunks)
    index["idf"] = stats["idf"]
    index["avgdl"] = (
        stats["avgdl"]
        if chunks else 1
    )
    index["ready"] = bool(chunks)
    return index

def load_rag_index(force=False):
    global rag_index, _current_rag_index

    with rag_lock:
        source_path = discover_rag_source()
        if not source_path:
            rag_index = {
                "ready": False,
                "error": "RAG source file not found",
                "expected_path": RAG_SOURCE_PATH,
                "chunks": [],
            }
            return rag_index

        source_mtime = os.path.getmtime(source_path)
        if not force and rag_index and rag_index.get("source_path") == source_path and rag_index.get("source_mtime") == source_mtime:
            return rag_index

        if _current_rag_index:
            index_file = _current_rag_index
        else:
            index_file = os.path.splitext(source_path)[0] + ".index.json"
            _current_rag_index = index_file
        
        if not force and os.path.exists(index_file):
            try:
                with open(index_file, "r", encoding="utf-8") as f:
                    cached = json.load(f)
                if (
                    cached.get("source_path") == source_path
                    and cached.get("source_mtime") == source_mtime
                    and cached.get("index_version") == legal_rag.INDEX_VERSION
                ):
                    rag_index = hydrate_rag_index(cached)
                    rag_index["embeddings"] = legal_rag.load_embeddings(index_file)
                    return rag_index
            except Exception as e:
                print(f"RAG cache load error: {e}")

        text = extract_text_for_rag(source_path)
        chunks = build_rag_chunks(text)
        index = {
            "source_path": source_path,
            "source_name": os.path.basename(source_path),
            "source_mtime": source_mtime,
            "built_at": time.time(),
            "index_version": legal_rag.INDEX_VERSION,
            "chunks": chunks,
        }
        rag_index = hydrate_rag_index(index)
        rag_index["embeddings"] = legal_rag.load_embeddings(index_file)

        try:
            cacheable = dict(index)
            cacheable.pop("idf", None)
            cacheable.pop("avgdl", None)
            cacheable.pop("ready", None)
            with open(index_file, "w", encoding="utf-8") as f:
                json.dump(cacheable, f, ensure_ascii=False)
        except Exception as e:
            print(f"RAG cache save error: {e}")

        return rag_index

def load_chat_rag_index(force=False):
    """Load one searchable index containing every approved RT code for chat."""
    global chat_rag_index
    with rag_lock:
        rag_dir = os.path.dirname(RAG_SOURCE_PATH)
        source_paths = [
            os.path.join(rag_dir, name)
            for name in CHAT_RAG_SOURCE_NAMES
            if os.path.isfile(os.path.join(rag_dir, name))
        ]
        if not source_paths:
            return {"ready": False, "error": "Chat RAG sources not found", "chunks": []}

        sources = [
            {"path": path, "name": os.path.basename(path), "mtime": os.path.getmtime(path)}
            for path in source_paths
        ]
        if not force and chat_rag_index and chat_rag_index.get("sources") == sources:
            return chat_rag_index

        if not force and os.path.isfile(CHAT_RAG_INDEX_FILE):
            try:
                with open(CHAT_RAG_INDEX_FILE, "r", encoding="utf-8") as file:
                    cached = json.load(file)
                if (
                    cached.get("sources") == sources
                    and cached.get("index_version") == legal_rag.INDEX_VERSION
                ):
                    chat_rag_index = hydrate_rag_index(cached)
                    chat_rag_index["embeddings"] = legal_rag.load_embeddings(CHAT_RAG_INDEX_FILE)
                    return chat_rag_index
            except Exception as error:
                print(f"Chat RAG cache load error: {error}")

        chunks = []
        for source in sources:
            try:
                # id формируется нарезчиком как «источник:статья:часть» —
                # перенумеровывать его нельзя, иначе потеряется адресация.
                chunks.extend(build_rag_chunks(extract_text_for_rag(source["path"]), source["name"]))
            except Exception as error:
                print(f"Chat RAG source skipped ({source['name']}): {error}")

        index = {
            "source_name": "Законы Республики Таджикистан",
            "sources": sources,
            "built_at": time.time(),
            "index_version": legal_rag.INDEX_VERSION,
            "chunks": chunks,
        }
        chat_rag_index = hydrate_rag_index(index)
        chat_rag_index["embeddings"] = legal_rag.load_embeddings(CHAT_RAG_INDEX_FILE)
        try:
            # Векторы и служебная карта id лежат в том же словаре, что и индекс (hydrate_rag_index правит его на месте), но в JSON
            # они не нужны и не сериализуются: numpy-массив обрывал запись посередине файла, и кэш получался битым.
            cacheable = {key: value for key, value in index.items() if key not in ("embeddings", "_by_id")}
            # Атомарная запись: во временный файл и переименование. Иначе два процесса (сервер, тесты, инструменты),
            # собирающих индекс одновременно, перемешивали записи, и файл оказывался битым на середине.
            tmp_path = "%s.%d.tmp" % (CHAT_RAG_INDEX_FILE, os.getpid())
            try:
                with open(tmp_path, "w", encoding="utf-8") as file:
                    json.dump(cacheable, file, ensure_ascii=False)
                os.replace(tmp_path, CHAT_RAG_INDEX_FILE)
            finally:
                if os.path.exists(tmp_path):
                    os.remove(tmp_path)
        except Exception as error:
            print(f"Chat RAG cache save error: {error}")
        return chat_rag_index

def load_crime_rag_index(force=False):
    """Подындекс только по УК РТ — источник ответов страницы /contract/ugalov-kodex.

    Строится фильтрацией общего индекса кодексов РТ, но статистика BM25
    (idf, средняя длина) пересчитывается по одному Уголовному кодексу:
    в общем корпусе «хищение» и «наказание» — частые слова шести кодексов,
    и внутри УК они переставали различать статьи.
    """
    global crime_rag_index
    # Общий индекс берётся до захвата замка: rag_lock не реентрантный.
    chat_index = load_chat_rag_index(force=force)
    if not chat_index.get("ready"):
        return {
            "ready": False,
            "error": chat_index.get("error") or "Chat RAG index is not ready",
            "chunks": [],
        }

    with rag_lock:
        built_at = chat_index.get("built_at")
        if not force and crime_rag_index and crime_rag_index.get("built_from") == built_at:
            return crime_rag_index

        chunks = [
            chunk for chunk in chat_index.get("chunks", [])
            if chunk.get("source") == CRIME_RAG_SOURCE_NAME
        ]
        if not chunks:
            crime_rag_index = {
                "ready": False,
                "error": f"RAG source not indexed: {CRIME_RAG_SOURCE_NAME}",
                "chunks": [],
            }
            return crime_rag_index

        index = {
            "source_name": RAG_CODE_LABELS.get(CRIME_RAG_SOURCE_NAME, CRIME_RAG_SOURCE_NAME),
            "source": CRIME_RAG_SOURCE_NAME,
            "built_from": built_at,
            "index_version": chat_index.get("index_version"),
            "chunks": chunks,
        }
        crime_rag_index = hydrate_rag_index(index)
        # Векторы общие для всех кодексов: идентификаторы чужих источников
        # просто не разрешатся в этом подындексе и в выдачу не попадут.
        # Страница УК настроена на лексический поиск: с общими векторами топ-3 смещался (ст. 110 уходила на 4-е место при
        # избиении с тяжким вредом), и квалификация портилась. Включить: CRIME_RAG_USE_VECTORS=1.
        crime_rag_index["embeddings"] = chat_index.get("embeddings") if CRIME_RAG_USE_VECTORS else None
        return crime_rag_index


CRIME_QUERY_REWRITE = os.environ.get("CRIME_QUERY_REWRITE", "1").strip().lower() not in {"0", "false", "no", "off"}
CRIME_REWRITE_MIN_WORDS = 5
_crime_rewrite_cache = {}


def _crime_rewrite_prompt(description):
    return (
        "Ты юрист-криминалист Республики Таджикистан. Перед тобой описание события бытовым языком.\n"
        "Назови составы преступлений, которые оно может образовывать, ТАК, КАК ОНИ НАЗВАНЫ В УК РТ: "
        "названия статей и слова из диспозиций (например: «Кража», «тайное хищение чужого имущества», "
        "«незаконное проникновение в жилище», «нарушение правил безопасности движения и эксплуатации "
        "транспорта», «причинение смерти по неосторожности»). Включи основной состав и квалифицирующие признаки.\n"
        "Формат ответа: одна строка, 3-8 формулировок через запятую, без номеров статей, без пояснений. "
        "Если в событии нет ничего похожего на преступление — ответь одним словом: НЕТ.\n\n"
        "СОБЫТИЕ: " + description + "\nОТВЕТ:"
    )


def _crime_rewrite_query(description):
    """Переводит бытовое описание в язык диспозиций УК РТ.

    BM25 не знает, что «похитил ноутбук» и «проник через окно» — это «кража» и
    «проникновение в жилище»: рассказ с нейтральными словами («гражданин»,
    «сосед», «сомони») поднимал статьи о паспортах и тайне усыновления, а
    ст. 244 «Кража» не попадала в выдачу. Ручной список синонимов эту задачу не
    закрывает — событий больше, чем записей в нём. Сбой модели не блокирует
    поиск: возвращается пустая строка, и работает прежний запрос.
    """
    if not CRIME_QUERY_REWRITE or len((description or "").split()) < CRIME_REWRITE_MIN_WORDS:
        return ""
    key = description.strip().lower()
    if key in _crime_rewrite_cache:
        return _crime_rewrite_cache[key]
    try:
        raw = llm_once(CRIME_MODEL, _crime_rewrite_prompt(description[:1500]), 220,
                       temperature=0.0, keep_alive=CRIME_KEEP_ALIVE, timeout=45,
                       openai_model=CRIME_GPT_MODEL)
    except Exception as error:  # noqa: BLE001
        print("  [CRIME] переформулировка запроса не удалась (%s)" % type(error).__name__)
        return ""
    rewritten = re.sub(r"\s+", " ", re.sub(r"[«»\"\n]", " ", raw or "")).strip()[:400]
    if len(rewritten.split()) <= 6 and re.search(r"(?<!\w)(?:нет|отсутству\w*|не\s+усматрив\w*)(?!\w)", rewritten, flags=re.IGNORECASE):
        rewritten = ""
    # Номера статей из ответа модели в поиск не берём: они могут быть выдуманы.
    rewritten = re.sub(r"\b(?:ст\.?|статья|статьи)\s*\d+[\d.,\s-]*", " ", rewritten, flags=re.IGNORECASE)
    if len(_crime_rewrite_cache) > 256:
        _crime_rewrite_cache.clear()
    _crime_rewrite_cache[key] = rewritten
    return rewritten


def retrieve_crime_rag(query, top_k=None):
    """Статьи УК РТ по описанию события. Запрос расширяется синонимами:
    человек пишет «вырвал сумку», кодекс — «открытое хищение чужого
    имущества», и без расширения BM25 не находит ни одной статьи.

    Для развёрнутого описания сначала запрашивается формулировка на языке
    кодекса; поиск идёт по ней, а исходный запрос остаётся запасным."""
    index = load_crime_rag_index()
    if not index.get("ready"):
        return [], index
    store = index.get("embeddings")
    limit = top_k or CRIME_RAG_TOP_K

    def run(text):
        return legal_rag.search(
            index, legal_rag.expand_query(text), top_k=limit,
            store=store, provider=embedding_provider if store else None,
        )

    rewritten = _crime_rewrite_query(query)
    if rewritten:
        results = run(rewritten)
        if grounded_crime_results(results):
            return results, index
    return run(query), index


# Смежные нормы: справочный блок страницы УК. Квалификация остаётся строго по
# УК РТ (общий индекс когда-то вытеснял уголовную норму статьями Гражданского и
# Налогового кодексов), а рядом показываются нормы других кодексов, но только
# из тех областей, которые названы в описании.
RELATED_MAX_TOTAL = int(os.environ.get("CRIME_RELATED_MAX", "5"))
RELATED_PER_CODE = int(os.environ.get("CRIME_RELATED_PER_CODE", "2"))
RELATED_MIN_SCORE = float(os.environ.get("CRIME_RELATED_MIN_SCORE", "12.0"))
RELATED_QUERY_SUFFIX = " ответственность за нарушение возмещение вреда"


def load_related_rag_index(source, force=False):
    """Подындекс одного кодекса (не УК) со своей статистикой BM25.

    Общий индекс всех кодексов здесь не годится: слова «ответственность» и
    «возмещение вреда» есть в каждом из них и почти ничего не весят, а внутри
    одного кодекса именно они поднимают нужную статью (ст. 187 ТК о материальной
    ответственности работодателя, ст. 93 ВдК о возмещении вреда водным объектам).
    """
    chat_index = load_chat_rag_index(force=force)
    if not chat_index.get("ready"):
        return {"ready": False, "chunks": []}

    with rag_lock:
        built_at = chat_index.get("built_at")
        cached = related_rag_indexes.get(source)
        if not force and cached and cached.get("built_from") == built_at:
            return cached
        chunks = [c for c in chat_index.get("chunks", []) if c.get("source") == source]
        if not chunks:
            related_rag_indexes[source] = {"ready": False, "chunks": [], "built_from": built_at}
            return related_rag_indexes[source]
        related_rag_indexes[source] = hydrate_rag_index({
            "source_name": RAG_CODE_LABELS.get(source, source),
            "source": source,
            "built_from": built_at,
            "index_version": chat_index.get("index_version"),
            "chunks": chunks,
        })
        return related_rag_indexes[source]


def retrieve_related_norms(description):
    """Нормы других кодексов РТ по областям, названным в описании.

    Голый BM25 по чужим кодексам шумит (на «вырвал сумку» приходили статьи ГК
    о «чужом интересе», на «погоду» — Градостроительный кодекс), а порог по
    баллу шум от смысла не отделяет. Поэтому сначала отбираются кодексы, чьи
    маркерные слова встретились в описании, и только в каждом из них берутся
    лучшие статьи. Нет названной области — блока нет.
    """
    domains = {
        source: hits for source, hits in legal_rag.matched_codes(description, min_hits=1).items()
        if source not in RELATED_EXCLUDED_SOURCES
    }
    picked = []
    # Кодексы идут по числу совпадений маркеров: чем полнее названа область,
    # тем раньше её нормы.
    for source in domains:
        index = load_related_rag_index(source)
        if not index.get("ready"):
            continue
        # Что нужно рядом с квалификацией по УК: нормы об ответственности и
        # возмещении вреда в названной области. Без этих слов BM25 подбирал по
        # разговорному описанию случайное (льготы врачам-психиатрам на врачебную
        # ошибку, совместное управление лесами на незаконную рубку).
        results = legal_rag.search(index, description + RELATED_QUERY_SUFFIX, top_k=RELATED_PER_CODE * 4)
        seen, taken = set(), 0
        for item in results:
            article = item.get("article")
            if article in seen or float(item.get("lexical_score") or 0) < RELATED_MIN_SCORE:
                continue
            seen.add(article)
            picked.append(crime_article_payload(item))
            taken += 1
            if taken >= RELATED_PER_CODE or len(picked) >= RELATED_MAX_TOTAL:
                break
        if len(picked) >= RELATED_MAX_TOTAL:
            break
    return picked


def grounded_crime_results(results):
    """Оставляет только фрагменты, которые реально отвечают на запрос.

    Поиск всегда возвращает лучшее из имеющегося, поэтому на вопрос не по
    теме УК приходит формально непустая выдача со слабыми совпадениями.
    Такие фрагменты в ответ не идут: страница обязана либо ответить нормой
    кодекса, либо честно сказать, что нормы нет.
    """
    grounded = []
    for item in results:
        lexical = float(item.get("lexical_score") or 0)
        vector = float(item.get("vector_score") or 0)
        if lexical >= CRIME_RAG_MIN_SCORE or vector >= legal_rag.VECTOR_ONLY_MIN_SCORE:
            grounded.append(item)
    return grounded


def crime_article_payload(item):
    """Фрагмент УК РТ в виде, пригодном для показа на странице."""
    return {
        "article": item.get("article"),
        "article_title": item.get("article_title") or item.get("title"),
        "title": item.get("title"),
        "chapter": item.get("chapter"),
        "chapter_title": item.get("chapter_title"),
        "citation": item.get("citation") or legal_rag.citation_for(item),
        "text": item.get("text") or "",
        "snippet": (item.get("text") or "")[:RAG_SNIPPET_MAX],
        "score": item.get("score"),
        "lexical_score": item.get("lexical_score"),
        "vector_score": item.get("vector_score"),
        "source": item.get("source"),
        "code": RAG_CODE_LABELS.get(item.get("source"), item.get("source")),
    }


def crime_rag_status():
    """Готовность именно уголовного подындекса — её показывает шапка страницы."""
    index = load_crime_rag_index()
    chunks = index.get("chunks", [])
    return {
        "ready": bool(index.get("ready")),
        "source": index.get("source_name"),
        "source_file": CRIME_RAG_SOURCE_NAME,
        "chunks": len(chunks),
        "articles": len({chunk.get("article") for chunk in chunks if chunk.get("article")}),
        "error": index.get("error"),
    }


def rag_status_payload():
    index = load_rag_index(force=False)
    rag_dir = os.path.dirname(RAG_SOURCE_PATH)
    chat_sources = [name for name in CHAT_RAG_SOURCE_NAMES if os.path.isfile(os.path.join(rag_dir, name))]
    chunks = index.get("chunks", [])
    # «chunks» выше — только Гражданский кодекс (индекс анализа договоров), и
    # шапка чата показывала «RAG 1368», будто это вся база. Чат ищет по общему
    # индексу, поэтому считаем его отдельно, а файлы кодексов, лежащие в папке,
    # но не внесённые в список, выводим явно: именно так два кодекса месяц
    # оставались непроиндексированными.
    chat_index = load_chat_rag_index(force=False)
    indexed_sources = {chunk.get("source") for chunk in chat_index.get("chunks", [])}
    unindexed = sorted(
        name for name in os.listdir(rag_dir)
        if name.lower().endswith((".doc", ".docx")) and name not in indexed_sources
        and name not in RAG_IGNORED_FILES
    ) if os.path.isdir(rag_dir) else []
    return {
        "chat_index": {
            "ready": bool(chat_index.get("ready")),
            "codes": len(indexed_sources),
            "chunks": len(chat_index.get("chunks", [])),
            "unindexed_files": unindexed,
        },
        "ready": bool(index.get("ready")),
        "source": index.get("source_name"),
        "source_path": index.get("source_path") or index.get("expected_path"),
        "chunks": len(chunks),
        "articles": len({chunk.get("article") for chunk in chunks if chunk.get("article")}),
        "index_version": index.get("index_version"),
        "built_at": index.get("built_at"),
        "error": index.get("error"),
        "chat_sources": chat_sources,
        "embeddings": {
            "enabled": bool(index.get("embeddings")),
            "model": getattr(embedding_provider, "model", legal_rag.EMBED_MODEL),
            "reason": embedding_provider.reason or None,
        },
        # Статус переводчика виден в шапке чата: без него таджикские вопросы
        # получают русский ответ, и это должно быть заметно до первого запроса,
        # а не выясняться из текста ответа.
        "translation": {
            "enabled": lang_service.translator.is_installed(),
            "loaded": lang_service.translator.loaded,
            "languages": list(lang_service.SUPPORTED_LANGS),
            "reason": lang_service.translator.reason or None,
        },
        # Отдельная готовность УК РТ: страница квалификации работает только
        # по нему, и общий "ready" (Гражданский кодекс) о ней ничего не говорит.
        "crime": crime_rag_status(),
    }

def _shorten_text(text, limit=260):
    text = re.sub(r"\s+", " ", text or "").strip()
    if len(text) <= limit:
        return text
    shortened = text[:limit].rsplit(" ", 1)[0].strip()
    return shortened + "..."

def rag_article_catalog():
    """Build an exact chapter/article catalogue from the active RAG source."""
    global rag_citations_cache
    source_path = discover_rag_source()
    if not source_path or not os.path.isfile(source_path):
        return {}
    cache_key = (source_path, os.path.getmtime(source_path))
    if isinstance(rag_citations_cache, dict) and rag_citations_cache.get("key") == cache_key:
        return rag_citations_cache["articles"]

    ext = os.path.splitext(source_path)[1].lower()
    if ext == ".docx":
        paragraphs = [p.text.strip() for p in Document(source_path).paragraphs if p.text and p.text.strip()]
    elif ext == ".doc":
        paragraphs = [p.strip() for p in extract_text_from_doc(source_path).split("\n") if p.strip()]
    else:
        paragraphs = []

    articles = {}
    chapter = None
    for paragraph in paragraphs:
        chapter_match = re.match(r"^ГЛАВА\s+(\d+(?:\.\d+)?)\.?\s*(.*)$", paragraph, re.IGNORECASE)
        if chapter_match:
            chapter = chapter_match.group(1)
            continue
        article_match = re.match(r"^Статья\s+(\d+(?:[-.]\d+)?(?:\.\d+)?)\.\s*(.*)$", paragraph, re.IGNORECASE)
        if not article_match:
            continue
        number, title = article_match.groups()
        articles.setdefault(number, {
            "number": number,
            "chapter": chapter,
            "title": title.strip(),
            "code": RAG_CODE_NAME,
        })

    rag_citations_cache = {"key": cache_key, "articles": articles}
    return articles

def canonical_rag_norm(article_numbers):
    """Return only citations backed by the connected RAG source."""
    catalog = rag_article_catalog()
    citations = []
    for number in sorted({str(number) for number in article_numbers}):
        item = catalog.get(number)
        if not item:
            continue
        chapter_part = f", Глава {item['chapter']}" if item.get("chapter") else ""
        citations.append(f"{item.get('code', RAG_CODE_NAME)}{chapter_part}, Статья {number}")
    return "; ".join(citations)

# Отличительная основа названия каждого кодекса — используется, чтобы
# понять, на какой именно кодекс ссылается модель в свободном тексте нормы
# ("Гражданский кодекс...", "Налогового кодекса..."), когда голый номер
# статьи совпадает сразу в нескольких подключённых кодексах.
_CODE_NAME_STEMS = {
    CONTRACT_RAG_SOURCE_NAME: "гражданск",
    "574_ru.doc": "уголовн",
    "семейный кодекс.docx": "семейн",
    "налоговый кодекс.docx": "налогов",
    "трудовой кодекс.docx": "трудов",
    "автомабильный кодекс.docx": "автомобильн",
    "Водный кодекс.docx": "водн",
    "Воздушный кодекс.docx": "воздушн",
    "Градостроительный кодекс.docx": "градостроительн",
    "Жилишный Кодекс.docx": "жилищн",
    "Кодекс здравохранение.docx": "здравоохран",
    "Лесной кодекс.docx": "лесн",
}


def _guess_source_from_citation_text(text):
    """Best-effort: which registered codex does this citation text name?"""
    lowered = (text or "").lower()
    for source_name, stem in _CODE_NAME_STEMS.items():
        if stem in lowered:
            return source_name
    return None


def _resolve_rag_article_chunk(number, hint_text=""):
    """Pick ONE chunk for a cited article number across every connected
    codex — the single source of truth for both the citation label
    (``canonical_rag_norm_multi``) and the quoted statute text
    (``_attach_rag_norm_text``), so the two can never disagree.

    Each codex numbers its own articles starting from 1, so the same bare
    number can legitimately belong to two different codexes — caught live:
    a finding cited "Статья 269", which exists in both the Civil Code and
    the Tax Code (VAT invoices). The label picker (first-registered-codex-
    wins) and the text picker (longest-chunk-wins) used different tie-break
    rules and could each resolve to a different codex — the report showed
    "Гражданский кодекс ... Статья 269" as the label while quoting the Tax
    Code's VAT-invoice article as the text. Preferring the codex the model
    itself named in its citation, when it can be determined, fixes both at
    once; without that hint, both now fall back to the same "longest
    chunk" rule instead of two different ones.
    """
    index = load_chat_rag_index(force=False)
    candidates = [
        chunk for chunk in index.get("chunks", [])
        if str(chunk.get("article") or "") == str(number)
    ]
    if not candidates:
        return None
    if len(candidates) > 1:
        guessed_source = _guess_source_from_citation_text(hint_text)
        if guessed_source:
            named = [c for c in candidates if c.get("source") == guessed_source]
            if named:
                candidates = named
    return max(candidates, key=lambda c: len(c.get("text", "")))


def canonical_rag_norm_multi(article_numbers, hint_text=""):
    """Like ``canonical_rag_norm``, but resolves against every connected
    codex instead of only the Civil Code — used where a verified finding may
    legitimately cite the Tax, Labor, Family, etc. code, not just the Civil
    Code the single-source variant was scoped to.
    """
    citations = []
    for number in sorted({str(number) for number in article_numbers}):
        chunk = _resolve_rag_article_chunk(number, hint_text)
        if not chunk:
            continue
        chapter_part = f", Глава {chunk['chapter']}" if chunk.get("chapter") else ""
        code = chunk.get("code") or RAG_CODE_LABELS.get(chunk.get("source"), chunk.get("source")) or RAG_CODE_NAME
        citations.append(f"{code}{chapter_part}, Статья {number}")
    return "; ".join(citations)

def canonical_rag_norm_for_source(article_numbers, source_name):
    """Get exact citations for every document included in the chat RAG."""
    global multi_rag_citations_cache
    rag_dir = os.path.dirname(RAG_SOURCE_PATH)
    source_path = os.path.join(rag_dir, source_name)
    if not os.path.isfile(source_path):
        return ""
    cache_key = (source_path, os.path.getmtime(source_path))
    catalog = multi_rag_citations_cache.get(cache_key)
    if catalog is None:
        try:
            ext = os.path.splitext(source_path)[1].lower()
            if ext == ".docx":
                paragraphs = [p.text.strip() for p in Document(source_path).paragraphs if p.text and p.text.strip()]
            elif ext == ".doc":
                paragraphs = [p.strip() for p in extract_text_from_doc(source_path).split("\n") if p.strip()]
            else:
                paragraphs = []
            catalog, chapter = {}, None
            for paragraph in paragraphs:
                chapter_match = re.match(r"^ГЛАВА\s+(\d+(?:\.\d+)?)\.?\s*(.*)$", paragraph, re.IGNORECASE)
                if chapter_match:
                    chapter = chapter_match.group(1)
                    continue
                article_match = re.match(r"^Статья\s+(\d+(?:[-.]\d+)?(?:\.\d+)?)\.\s*(.*)$", paragraph, re.IGNORECASE)
                if article_match:
                    number = article_match.group(1)
                    catalog.setdefault(number, chapter)
            multi_rag_citations_cache[cache_key] = catalog
        except Exception as error:
            print(f"RAG citation catalogue skipped ({source_name}): {error}")
            return ""

    label = RAG_CODE_LABELS.get(source_name, source_name)
    citations = []
    for number in sorted({str(number) for number in article_numbers}):
        if number not in catalog:
            continue
        chapter_part = f", Глава {catalog[number]}" if catalog[number] else ""
        citations.append(f"{label}{chapter_part}, Статья {number}")
    return "; ".join(citations)

def _source_paragraphs(source_path):
    """Return clean paragraph lines for a docx/doc source file."""
    ext = os.path.splitext(source_path)[1].lower()
    if ext == ".docx":
        doc = Document(source_path)
        return [p.text.strip() for p in doc.paragraphs if p.text and p.text.strip()]
    if ext == ".doc":
        text = extract_text_from_doc(source_path)
        if text:
            return [p.strip() for p in text.split("\n") if p.strip()]
    return []


def extract_rag_articles(article_numbers, source_name=None):
    """Full article texts for the requested numbers. When ``source_name`` is
    given, only that RAG source (e.g. the Уголовный кодекс 574_ru.doc) is
    searched; otherwise every chat RAG source is searched."""
    rag_dir = os.path.dirname(RAG_SOURCE_PATH)
    sources = [
        (name, os.path.join(rag_dir, name))
        for name in CHAT_RAG_SOURCE_NAMES
        if os.path.isfile(os.path.join(rag_dir, name))
        and (source_name is None or name == source_name)
    ]

    articles = []
    for number in article_numbers:
        number = str(number).strip()
        if not number:
            continue
        for source_name, source_path in sources:
            paragraphs = _source_paragraphs(source_path)
            if not paragraphs:
                continue
            starts = [
                idx for idx, paragraph in enumerate(paragraphs)
                if re.match(rf"^Статья\s+{re.escape(number)}\.", paragraph, re.IGNORECASE)
            ]
            if not starts:
                continue
            candidates = []
            for start in starts:
                block = []
                for paragraph in paragraphs[start:]:
                    if block and re.match(r"^Статья\s+\d+\.", paragraph, re.IGNORECASE):
                        break
                    block.append(paragraph)
                if block:
                    candidates.append(block)
            if not candidates:
                continue
            block = max(candidates, key=lambda item: sum(len(part) for part in item))
            title = block[0]
            title = re.sub(rf"^Статья\s+{re.escape(number)}\.\s*", "", title, flags=re.IGNORECASE).strip()
            body = "\n".join(block[1:]).strip()
            full_text = "\n".join(block).strip()
            articles.append({
                "number": number,
                "title": title or f"Статья {number}",
                "description": _shorten_text(body or full_text, 320),
                "text": full_text,
                "source": source_name,
                "source_path": source_path,
                "code": RAG_CODE_LABELS.get(source_name, source_name),
            })
            break  # first source that contains the article wins
        else:
            # article not found in any source
            articles.append({
                "number": number,
                "title": f"Статья {number}",
                "description": "Не найдено в подключённых RAG-источниках",
                "text": "",
                "source": None,
                "source_path": None,
                "code": None,
            })
    return articles
FINES_SOURCE_NAME = "КоАП РТ — размеры штрафов.docx"
# Вопрос о штрафе: слово «штраф» в общем индексе тянет статьи УК/УПК с названием «Штраф» и неустойку ГК, а справочник
# КоАП тонет. Для таких вопросов справочник ищется отдельно (своя статистика BM25 и свои векторы), и его лучшие
# статьи ставятся в начало выдачи. Рядом со словами о договоре («штраф по договору») полоса не включается.
_FINE_INTENT_RE = re.compile(
    r"штраф|оштраф|сколько\s+(?:платить|заплатить|платят|взыскива\w+)|административн\w+\s+(?:ответственност|наказани|нарушени|правонарушени)",
    re.IGNORECASE)
_FINE_CIVIL_RE = re.compile(r"договор\w*|контракт\w*|обязательств\w*|неустойк\w*|пен[яиюей]\b|арендодател\w*|арендатор\w*|заказчик\w*|исполнител\w*", re.IGNORECASE)
_lane_indexes = {}

# Вопрос прямо называет кодекс: «что говорит Конституция…», «по Земельному кодексу…». Такой кодекс ищется отдельно, и его лучшие
# статьи идут в начало. Иначе статьи без названий (Конституция) или короткие кодексы тонут среди сильных по BM25 статей ГК и КЗ.
_NAMED_CODE_LANES = (
    (re.compile(r"конституци", re.IGNORECASE), "Конституция РТ (машинный перевод).docx"),
    (re.compile(r"земельн\w+\s+кодекс|по\s+земельному|земельного\s+кодекса", re.IGNORECASE), "Земельный кодекс (машинный перевод).docx"),
    (re.compile(r"таможенн\w+\s+кодекс|таможенного\s+кодекса|по\s+таможенному", re.IGNORECASE), "Таможенный кодекс (машинный перевод).docx"),
    (re.compile(r"уголовно-исполнительн", re.IGNORECASE), "Уголовно-исполнительный кодекс (машинный перевод).docx"),
)


def _lane_index(chat_index, source_name):
    """Подындекс одного источника (своя статистика BM25) + подмножество векторов; None, если источника нет."""
    built = chat_index.get("built_at")
    cached = _lane_indexes.get(source_name)
    if cached is not None and cached.get("built_from") == built:
        return cached if cached.get("chunks") else None
    chunks = [c for c in chat_index.get("chunks", []) if c.get("source") == source_name]
    if not chunks:
        _lane_indexes[source_name] = {"built_from": built, "chunks": []}
        return None
    index = hydrate_rag_index({"source": source_name, "built_from": built,
                               "index_version": chat_index.get("index_version"), "chunks": chunks})
    store = chat_index.get("embeddings")
    if store:
        ids = set(c["id"] for c in chunks)
        keep = [i for i, chunk_id in enumerate(store["ids"]) if chunk_id in ids]
        if keep:
            index["embeddings"] = {"ids": [store["ids"][i] for i in keep], "vectors": store["vectors"][keep],
                                   "model": store.get("model")}
    _lane_indexes[source_name] = index
    return index


def is_fine_question(query):
    text = query or ""
    return bool(_FINE_INTENT_RE.search(text)) and not _FINE_CIVIL_RE.search(text)


def retrieve_lane(query, source_name, top_k=3, index=None):
    """Лучшие статьи одного источника по запросу (пусто, если источника нет в индексе)."""
    chat_index = index or load_chat_rag_index(force=False)
    if not chat_index.get("ready"):
        return []
    lane = _lane_index(chat_index, source_name)
    if not lane:
        return []
    store = lane.get("embeddings")
    return legal_rag.search(lane, query, top_k=top_k, store=store, provider=embedding_provider if store else None)


def retrieve_fines(query, top_k=3, index=None):
    return retrieve_lane(query, FINES_SOURCE_NAME, top_k=top_k, index=index)


def _lane_sources(query):
    """Источники, которые нужно искать отдельно: названные в вопросе кодексы и справочник штрафов для вопроса о штрафе."""
    sources = [source for pattern, source in _NAMED_CODE_LANES if pattern.search(query or "")]
    if is_fine_question(query):
        sources.append(FINES_SOURCE_NAME)
    return sources


def retrieve_rag(query, top_k=None, index=None, fines_lane=False):
    """Гибридный поиск: BM25 по статье и названию + опциональные эмбеддинги.

    fines_lane=True (чат): названные в вопросе кодексы и справочник штрафов дополнительно ищутся отдельно и идут в начало выдачи."""
    index = index or load_rag_index(force=False)
    if not index.get("ready"):
        return []
    store = index.get("embeddings")
    limit = top_k or RAG_TOP_K
    results = legal_rag.search(
        index,
        query,
        top_k=limit,
        store=store,
        provider=embedding_provider if store else None,
    )
    if fines_lane:
        front = []
        for source in _lane_sources(query):
            front.extend(retrieve_lane(query, source, top_k=3, index=index))
        if front:
            seen = {item.get("id") for item in front}
            results = (front + [item for item in results if item.get("id") not in seen])[:limit]
    return results

def format_rag_context(results, max_chars=12000):
    if not results:
        return ""
    blocks = []
    used = 0
    # Бюджет делится между найденными нормами. Раньше каждый фрагмент резался
    # до 2000 символов, а не поместившийся блок обрывал цикл целиком — при
    # небольшом бюджете модель получала пустой контекст и отвечала без опоры
    # на закон. Заголовок и ссылка съедают около 200 символов на блок.
    per_item = max(400, (max_chars // max(len(results), 1)) - 200)
    for i, item in enumerate(results, 1):
        text = re.sub(r"\s+", " ", item.get("text", "")).strip()
        if len(text) > per_item:
            text = text[:per_item].rsplit(" ", 1)[0] + "..."
        # Ссылка берётся из метаданных фрагмента: он знает свой кодекс,
        # главу и номер статьи. Разбор текста регуляркой остаётся запасным
        # путём для индексов старого формата.
        citations = item.get("citation") or legal_rag.citation_for(item)
        if not citations:
            article_numbers = _cited_article_numbers(text)
            citations = (
                canonical_rag_norm(article_numbers)
                if item.get("source") == CONTRACT_RAG_SOURCE_NAME
                else canonical_rag_norm_for_source(article_numbers, item.get("source", ""))
            )
        citation_note = f"\nПРОВЕРЕННАЯ ССЫЛКА RAG: {citations}" if citations else ""
        label = item.get("code_short") or RAG_CODE_LABELS.get(item.get("source"), item.get("source"))
        block = f"[{i}] {label} / {item.get('title')}:\n{text}{citation_note}"
        remaining = max_chars - used
        if len(block) > remaining:
            # Последнюю норму лучше подрезать, чем потерять целиком.
            if remaining < 300:
                break
            block = block[:remaining].rsplit(" ", 1)[0] + "..."
        blocks.append(block)
        used += len(block)
    return "\n\n".join(blocks)

def rag_context_for_query(query, top_k=None):
    """RAG-контекст для анализа договора — по ВСЕМ подключённым кодексам РТ,
    не только по Гражданскому.

    Раньше здесь искал только Гражданский кодекс (retrieve_rag по умолчанию
    берёт load_rag_index() — единственный активный источник), и договор с
    налоговой или трудовой составляющей физически не мог получить норму
    другого кодекса: её просто не было в выдаче поиска. Юнифицированный
    chat-индекс уже покрывает все зарегистрированные кодексы одним BM25-
    поиском — ровно то же, чем пользуется чат.
    """
    return format_rag_context(retrieve_rag(query, top_k=top_k, index=load_chat_rag_index(force=False)))

def revise_contract_chunk(args):
    """Create a conservative, RAG-grounded revised version of one contract part."""
    index, total, chunk = args
    legal_context = rag_context_for_query(chunk, top_k=RAG_TOP_K)
    if not legal_context:
        raise RuntimeError("No relevant RAG context for contract revision")

    prompt = f"""Ты — юрист по гражданскому законодательству Республики Таджикистан.

Подготовь доработанную редакцию части {index + 1} из {total} договора. Возвращай ТОЛЬКО текст договора: без анализа, заголовков «риск», пояснений, markdown и ссылок на RAG.

Обязательные правила:
1. Сохрани фактические сведения, коммерческие условия, названия сторон и нумерацию, если они не противоречат приведённым нормам.
2. Устрани только юридически рискованные, двусмысленные или неполные положения: добавь ясные сроки, порядок исполнения, ответственность, порядок изменения и расторжения, если этого требует контекст.
3. Не выдумывай сведения, реквизиты, суммы, даты или права, которых нет в исходной части. Если данных недостаточно — используй нейтральную формулировку «в порядке, согласованном Сторонами в письменной форме».
4. Используй только нормы и термины Республики Таджикистан из RAG ниже. Не упоминай законодательство РФ.
5. Это проект редакции для проверки юристом; не утверждай, что он гарантированно не содержит рисков.

RAG-контекст:
{legal_context}

Исходная часть договора:
{chunk}

Доработанная редакция части договора:"""
    revised = send_to_ollama(prompt, num_predict=2400, temperature=0.1).strip()
    if not revised or revised.startswith("⚠️"):
        raise RuntimeError("Model did not return a revised contract part")
    return normalize_legal_terms(revised)

def write_revised_docx(text, output_path):
    doc = Document()
    style = doc.styles["Normal"]
    style.font.name = "Times New Roman"
    style.font.size = Pt(11)
    for paragraph in re.split(r"\n{2,}", text.strip()):
        clean = paragraph.strip()
        if clean:
            doc.add_paragraph(clean)
    doc.save(output_path)

def convert_docx_with_word(source_path, output_path, format_code):
    """Use installed Microsoft Word for the requested legacy DOC/PDF output."""
    if os.name != "nt":
        raise RuntimeError("DOC and PDF revision export requires Windows with Microsoft Word")
    source_path = os.path.abspath(source_path)
    output_path = os.path.abspath(output_path)
    script = r'''& {
param([string]$source, [string]$target, [int]$format)
$ErrorActionPreference = 'Stop'
$word = $null
$document = $null
try {
  $word = New-Object -ComObject Word.Application
  $word.Visible = $false
  $word.DisplayAlerts = 0
  $document = $word.Documents.Open($source, $false, $true)
  $document.SaveAs2($target, $format)
} finally {
  if ($document -ne $null) { $document.Close($false) }
  if ($word -ne $null) { $word.Quit() }
}
}'''
    result = subprocess.run(
        ["powershell.exe", "-NoProfile", "-NonInteractive", "-Command", script, source_path, output_path, str(format_code)],
        capture_output=True,
        text=True,
        encoding="utf-8",
        errors="replace",
        timeout=180,
        cwd=PROJECT_ROOT,
    )
    if result.returncode != 0 or not os.path.exists(output_path):
        details = (result.stderr or result.stdout or "Word conversion failed").strip()
        raise RuntimeError(details[:500])

def create_revised_contract_output(revised_text, original_filename):
    """Save the revised project in the same file format as the uploaded document."""
    extension = os.path.splitext(original_filename)[1].lower()
    if extension not in {".pdf", ".doc", ".docx", ".txt"}:
        raise RuntimeError(f"Unsupported revised contract format: {extension}")

    token = uuid.uuid4().hex
    stem = re.sub(r"[^A-Za-z0-9_-]+", "_", os.path.splitext(os.path.basename(original_filename))[0])[:80] or "contract"
    display_name = f"{stem}_revised{extension}"
    output_name = f"{token}_{display_name}"
    output_path = os.path.join(RESULTS_DIR, output_name)

    if extension == ".txt":
        with open(output_path, "w", encoding="utf-8") as f:
            f.write(revised_text.strip() + "\n")
    elif extension == ".docx":
        write_revised_docx(revised_text, output_path)
    else:
        intermediate = os.path.join(RESULTS_DIR, f"{token}_{stem}_revised.docx")
        write_revised_docx(revised_text, intermediate)
        # wdFormatDocument = 0; wdFormatPDF = 17
        convert_docx_with_word(intermediate, output_path, 17 if extension == ".pdf" else 0)

    return {
        "url": f"/api/result/{output_name}",
        "filename": display_name,
        "format": extension.lstrip("."),
    }

# ================= LLM: OLLAMA / OPENAI =================
class LLMBackendError(Exception):
    """Провайдер генерации недоступен или ответил ошибкой."""


# Сообщения для пользователя не называют провайдера и модель; подробности остаются в журнале сервера.
AI_UNAVAILABLE_TEXT = "⚠️ ИИ-ассистент временно недоступен. Повторите попытку через несколько секунд."


def _llm_unavailable_message():
    print(f"  [LLM] недоступен бэкенд генерации ({LLM_PROVIDER})")
    return AI_UNAVAILABLE_TEXT


def _openai_headers():
    if not OPENAI_API_KEY:
        print("  [LLM] OPENAI_API_KEY не задан в .env — запросы к ИИ невозможны")
        raise LLMBackendError(AI_UNAVAILABLE_TEXT)
    return {"Authorization": "Bearer " + OPENAI_API_KEY, "Content-Type": "application/json"}


_REASONING_MODEL_RE = re.compile(r"^(?:gpt-(?:[5-9]|\d{2,})|o[134])")


def _openai_is_reasoning_model(model):
    # GPT-5 и новее (gpt-6-luna и т. д.) и o-серия принимают только
    # max_completion_tokens и температуру по умолчанию (1) — свои значения
    # temperature/max_tokens они отклоняют с HTTP 400.
    return bool(_REASONING_MODEL_RE.match(model or ""))


def _openai_effort_for_model(model, effort):
    # gpt-6 и новее не знают уровня "minimal" (HTTP 400: допустимы none, low,
    # medium, high, xhigh). "none" — ближайший по смыслу: без размышлений.
    if effort == "minimal" and re.match(r"^gpt-(?:[6-9]|\d{2,})", model or ""):
        return "none"
    return effort


# По умолчанию модели GPT-5 тратят весь бюджет max_completion_tokens на
# скрытые "reasoning"-токены и возвращают пустой content с finish_reason
# "length" — проверено прямым запросом: 300 токенов ушли в reasoning, видимый
# ответ остался пустым. "minimal" сводит reasoning-токены к нулю, что здесь и
# нужно: наши промпты уже содержат всю инструкцию заранее (RAG-контекст,
# формат ответа), развёрнутые рассуждения модели не покупают точность.
OPENAI_REASONING_EFFORT = os.environ.get("OPENAI_REASONING_EFFORT", "minimal")


def _openai_reasoning_budget(num_predict):
    """max_completion_tokens for a reasoning model, padded for headroom.

    "minimal" reasoning_effort does not mean zero reasoning tokens on a long
    or complex prompt: measured directly against the real contract-analysis
    prompt (RAG context + full contract text, ~3300 prompt tokens), a
    max_completion_tokens of 2400 — this feature's actual budget — made the
    model spend the *entire* 2400 on invisible reasoning and return finish_
    reason "length" with zero visible characters. The same prompt at 4000
    used 0 reasoning tokens and returned ~2500 tokens of real content — i.e.
    this isn't a proportional cost, it's a threshold the tight budget fell
    under. Padding generously costs nothing when unneeded (billing is by
    tokens actually produced, not by this ceiling) and prevents a silently
    empty analysis — worse than a slightly larger request.
    """
    return max(num_predict + 1600, int(num_predict * 1.8))


def _openai_payload(model, prompt, temperature, num_predict, stream, json_mode=False,
                    reasoning_effort=None):
    payload = {"model": model, "messages": [{"role": "user", "content": prompt}], "stream": stream}
    if _openai_is_reasoning_model(model):
        payload["max_completion_tokens"] = _openai_reasoning_budget(num_predict)
        effort = _openai_effort_for_model(model, reasoning_effort or OPENAI_REASONING_EFFORT)
        if effort:
            payload["reasoning_effort"] = effort
    else:
        payload["temperature"] = temperature
        payload["max_tokens"] = num_predict
    if json_mode:
        # Forces a syntactically valid JSON response instead of relying on
        # the prompt asking nicely — the caller still has to prompt for the
        # right shape, but a truncated/prose/markdown-fenced reply that the
        # normal text parser has to guess at becomes structurally impossible.
        payload["response_format"] = {"type": "json_object"}
    return payload


def _openai_error_detail(error):
    try:
        return error.response.json().get("error", {}).get("message", "")
    except Exception:
        return ""


def llm_stream(model, prompt, *, temperature=0.5, num_predict=2048, top_p=0.9,
                think=None, keep_alive=None, num_ctx=None, timeout=600,
                openai_model=None, reasoning_effort=None):
    """Потоковая генерация независимо от бэкенда.

    Отдаёт объекты в форме ответа Ollama — {"response": кусок текста} на
    каждый токен — чтобы вызывающий код (уже написанный под Ollama) остался
    без изменений. Соединение и HTTP-ошибки заворачиваются в LLMBackendError
    с готовым текстом для пользователя.

    openai_model и reasoning_effort действуют только на бэкенде OpenAI: так
    режим «глубина» в чате может взять более сильную модель и подумать
    дольше, не трогая остальные вызовы.
    """
    if LLM_PROVIDER == "openai":
        model = openai_model or OPENAI_MODEL
        # Соединение с API иногда рвётся, не успев отдать ни слова («Удалённый
        # хост принудительно разорвал соединение»). Пока ничего не выдано,
        # запрос безопасно повторить один раз: пользователь получает ответ, а не
        # системное сообщение. После первого токена повтор дал бы дубль текста.
        produced = False
        for attempt in (1, 2):
            try:
                with requests.post(
                    OPENAI_BASE_URL + "/chat/completions",
                    headers=_openai_headers(),
                    json=_openai_payload(model, prompt, temperature, num_predict, stream=True,
                                         reasoning_effort=reasoning_effort),
                    stream=True,
                    timeout=timeout,
                ) as r:
                    r.raise_for_status()
                    for line in r.iter_lines(chunk_size=1):
                        if not line:
                            continue
                        raw = line.decode("utf-8", errors="replace")
                        if not raw.startswith("data:"):
                            continue
                        raw = raw[len("data:"):].strip()
                        if raw == "[DONE]":
                            break
                        try:
                            obj = json.loads(raw)
                        except json.JSONDecodeError:
                            continue
                        choices = obj.get("choices") or []
                        delta = (choices[0].get("delta") if choices else {}) or {}
                        content = delta.get("content")
                        if content:
                            produced = True
                            yield {"response": content}
                break
            except (requests.exceptions.ConnectionError, requests.exceptions.ChunkedEncodingError):
                if produced or attempt == 2:
                    raise LLMBackendError(_llm_unavailable_message())
                print("  [LLM] соединение оборвано до первого токена, повторяю запрос")
                time.sleep(1.0)
            except requests.exceptions.HTTPError as error:
                print("  [LLM] ошибка API: " + (_openai_error_detail(error) or str(error)))
                raise LLMBackendError(AI_UNAVAILABLE_TEXT)
        yield {"done": True}
        return

    data = {
        "model": model,
        "prompt": prompt,
        "stream": True,
        "options": {
            "temperature": temperature,
            "num_predict": num_predict,
            "top_p": top_p,
        },
    }
    if think is not None:
        data["think"] = think
    if keep_alive is not None:
        data["keep_alive"] = keep_alive
    if num_ctx is not None:
        data["options"]["num_ctx"] = num_ctx

    try:
        with requests.post(OLLAMA_URL, json=data, stream=True, timeout=timeout) as r:
            r.raise_for_status()
            # The default 512-byte buffer postpones the visible first token.
            # A one-byte chunk keeps browser streaming responsive on slow CPUs.
            for line in r.iter_lines(chunk_size=1):
                if not line:
                    continue
                try:
                    obj = json.loads(line.decode("utf-8"))
                except json.JSONDecodeError:
                    continue
                yield obj
    except requests.exceptions.ConnectionError:
        raise LLMBackendError(_llm_unavailable_message())


def llm_once(model, prompt, num_predict, temperature=0.1, keep_alive=None, timeout=600, json_mode=False,
             openai_model=None, reasoning_effort=None):
    """Один короткий неструктурированный запрос без стрима. Возвращает текст.

    openai_model и reasoning_effort действуют только на бэкенде OpenAI: режим
    «Размышление» берёт для служебных шагов ту же сильную модель, что и для
    ответа, потому что от них зависит, какие статьи вообще найдутся.
    """
    if LLM_PROVIDER == "openai":
        model = openai_model or OPENAI_MODEL
        try:
            r = requests.post(
                OPENAI_BASE_URL + "/chat/completions",
                headers=_openai_headers(),
                json=_openai_payload(model, prompt, temperature, num_predict, stream=False, json_mode=json_mode,
                                     reasoning_effort=reasoning_effort),
                timeout=timeout,
            )
            r.raise_for_status()
        except requests.exceptions.ConnectionError:
            raise LLMBackendError(_llm_unavailable_message())
        except requests.exceptions.HTTPError as error:
            print("  [LLM] ошибка API: " + (_openai_error_detail(error) or str(error)))
            raise LLMBackendError(AI_UNAVAILABLE_TEXT)
        choices = (r.json() or {}).get("choices") or []
        message = (choices[0].get("message") if choices else {}) or {}
        return message.get("content") or ""

    payload = {
        "model": model,
        "prompt": prompt,
        "stream": False,
        "think": False,
        "options": {
            "temperature": temperature,
            "num_predict": num_predict,
            "top_p": 0.85,
        },
    }
    if keep_alive is not None:
        payload["keep_alive"] = keep_alive
    if json_mode:
        payload["format"] = "json"
    try:
        response = requests.post(OLLAMA_URL, json=payload, timeout=timeout)
        response.raise_for_status()
    except requests.exceptions.ConnectionError:
        raise LLMBackendError(_llm_unavailable_message())
    return (response.json() or {}).get("response") or ""


def send_to_ollama(prompt, stream_callback=None, num_predict=2048, temperature=0.5):
    response_text = ""
    try:
        for obj in llm_stream(_active_model_tag(MODEL_NAME), prompt,
                               temperature=temperature, num_predict=num_predict, top_p=0.9,
                               timeout=1500, openai_model=ANALYSIS_GPT_MODEL):
            chunk = obj.get("response", "")
            if chunk:
                response_text += chunk
                if stream_callback:
                    stream_callback(chunk)

    except LLMBackendError as e:
        error_msg = str(e)
        if stream_callback:
            stream_callback(error_msg)
        return error_msg
    except Exception as e:
        error_msg = f"⚠️ Ошибка генерации: {str(e)}"
        if stream_callback:
            stream_callback(error_msg)
        return error_msg

    return response_text

def _build_contract_type_guidance(contract_type):
    """Return a string of key points to check for a specific contract type."""
    guidance = {
        "трудовой договор": (
            "Трудовой договор: сверяй каждое условие с ТК РТ. Условия, ухудшающие положение работника по "
            "сравнению с кодексом, недопустимы и считаются высоким риском. Проверь: трудовую функцию и место "
            "работы; срок договора; испытание (предельный срок, основания расторжения в период испытания); "
            "продолжительность рабочего времени и сверхурочные; время отдыха; размер оплаты труда (не ниже "
            "минимального) и сроки выплаты; ежегодный оплачиваемый отпуск (не короче минимального); удержания "
            "из заработной платы; материальную ответственность; основания и порядок расторжения "
            "(по инициативе работника и работодателя, срок предупреждения); порядок рассмотрения трудовых споров."
        ),
        "купля-продажа": (
            "Купля-продажа: проверь предмет (товар, количество, ассортимент), цену и порядок расчётов, "
            "момент перехода права собственности и риска случайной гибели, качество товара и гарантию, "
            "порядок приёмки, ответственность за просрочку поставки/оплаты, предоплату."
        ),
        "аренда": (
            "Аренда: проверь предмет аренды (индивидуализация имущества), арендную плату и порядок её пересмотра, "
            "срок аренды, обязанности по ремонту и содержанию, субаренду, порядок возврата имущества, "
            "ответственность за повреждение, преимущественное право арендатора."
        ),
        "подряд": (
            "Подряд: проверь предмет (конкретный результат работ), сроки начала/окончания и промежуточные, цену и смету, "
            "качество результата и гарантийные сроки, порядок сдачи-приёмки, право на привлечение субподрядчиков, "
            "ответственность за просрочку и скрытые недостатки."
        ),
        "возмездное оказание услуг": (
            "Услуги: проверь предмет (конкретный перечень услуг), сроки оказания, цену и порядок оплаты, "
            "качество услуг и порядок приёмки (акт), право отказа от договора, ответственность за некачественное оказание."
        ),
        "поставка": (
            "Поставка: проверь количество, ассортимент, качество и комплектность товара, график/период поставки, "
            "порядок приёмки по количеству и качеству, ответственность за недопоставку и просрочку, "
            "восполнение недопоставки, односторонний отказ."
        ),
        "займ": (
            "Займ: проверь сумму, валюту, проценты и порядок их начисления, срок и порядок возврата, "
            "досрочный возврат, последствия просрочки возврата, безвозмездность/возмездность."
        ),
        "кредитный": (
            "Кредит: проверь сумму, процентную ставку и способ её определения, график погашения, "
            "комиссии, досрочное погашение, обеспечение, право банка на одностороннее изменение условий, "
            "последствия просрочки."
        ),
        "хранение": (
            "Хранение: проверь предмет (вещи), возмездность, обязательства по сохранности, "
            "ответственность хранителя за утрату/повреждение, порядок выдачи вещи, срок хранения."
        ),
        "поручение": (
            "Поручение: проверь объём полномочий поверенного, вознаграждение, отчётность, "
            "право отмены поручения и отказа, ответственность поверенного, передоверие."
        ),
        "комиссия": (
            "Комиссия: проверь предмет сделки, вознаграждение, отчётность комиссионера, "
            "ответственность за утрату имущества, право комиссионера на удержание, исполнение третьим лицом."
        ),
        "агентский": (
            "Агентский: проверь объём действий агента, вознаграждение, отчётность, "
            "ограничения прав принципала/агента, суб-агентов, срок и порядок прекращения."
        ),
        "доверительное управление": (
            "Доверительное управление: проверь объект управления, полномочия управляющего, "
            "вознаграждение, отчётность, ответственность управляющего, выгоды и убытки."
        ),
        "франчайзинг": (
            "Франчайзинг (коммерческая концессия): проверь предмет (комплекс исключительных прав), "
            "обязанности по обучению и поддержке, роялти и платежи, ограничения конкуренции, "
            "ответственность за качество, срок и порядок прекращения."
        ),
        "страхование": (
            "Страхование: проверь страховой интерес, страховые случаи, страховую сумму и премию, "
            "обязанности страхователя, исключения из покрытия, порядок выплаты, франшизу."
        ),
        "лизинг": (
            "Лизинг: проверь предмет, продавца, график лизинговых платежей, переход рисков, "
            "обслуживание и страхование, выкупную стоимость, ответственность сторон."
        ),
        "залог": (
            "Залог: проверь предмет залога, существо обеспеченного обязательства, порядок обращения взыскания, "
            "обязанности залогодателя по содержанию, последующая ипотека/залог."
        ),
        "дарение": (
            "Дарение: проверь предмет дара, запрет/ограничение дарения, отмену дарения, "
            "обещание дарения, форму договора."
        ),
        "мены": (
            "Мена: проверь предмет обмена, неравноценность и компенсацию, момент перехода права собственности, "
            "качество и ответственность при обмене."
        ),
        "авторский заказ": (
            "Авторский заказ: проверь предмет (конкретное произведение), срок создания, вознаграждение, "
            "исключительное право и порядок его использования, ответственность за неисполнение."
        ),
        "отчуждение исключительного права": (
            "Отчуждение исключительного права: проверь объём отчуждаемого права, территорию и срок, "
            "вознаграждение, последствия, ограничения использования."
        ),
        "лицензионный": (
            "Лицензионный: проверь объём и способы использования, территорию, срок, вознаграждение, "
            "исключительную/неисключительную лицензию, сублицензирование."
        ),
        "простой товарищества": (
            "Простое товарищество: проверь цель совместной деятельности, вклады, порядок ведения общих дел, "
            "распределение прибыли и убытков, ответственность по общим обязательствам."
        ),
    }
    return guidance.get(contract_type, "")

# ================= АНАЛИЗ ЧАНКА =================
def _contract_code_filter(labor):
    """Какие кодексы допустимы как основание условий договора: уголовные и административные — нет;
    трудовой — только для трудового договора."""
    excluded = ("УК", "УПК", "КоАП", "ПКоАП", "УИК", "ТмК", "ВдК", "ВздК", "ЛК")

    def allowed(item):
        code = str(item.get("code_short") or "")
        if code.startswith(excluded):
            return False
        if code.startswith("ТК") and not labor:
            return False
        return True
    return allowed


CONTRACT_RAG_MAX_CHARS = int(os.environ.get("CONTRACT_RAG_MAX_CHARS", "16000"))


def contract_legal_context(chunk, contract_type_info, full_text=""):
    """Нормы для проверки фрагмента договора: сначала статьи по темам условий, затем поиск по тексту.

    Возвращает (текст контекста, названия найденных тем).
    """
    index = load_chat_rag_index(force=False)
    if not index.get("ready"):
        return rag_context_for_query(chunk, top_k=RAG_TOP_K), []
    search = lambda query, top_k: retrieve_rag(query, top_k=top_k, index=index)  # noqa: E731
    labor = contract_topics.is_labor_text(full_text or chunk)
    allowed = _contract_code_filter(labor)
    topical = contract_topics.topic_results(chunk, search, (contract_type_info or {}).get("type"), labor=labor)
    merged, seen = [], set()
    for item in topical + [r for r in search(chunk, RAG_TOP_K) if allowed(r)]:
        key = (item.get("code_short"), item.get("article"), item.get("title"))
        if key in seen:
            continue
        seen.add(key)
        merged.append(item)
    topics = list(dict.fromkeys(item["_topic"] for item in topical if item.get("_topic")))
    return format_rag_context(merged, max_chars=CONTRACT_RAG_MAX_CHARS), topics


def _analyze_chunk_variant(args, variant=0):
    """Один проход модели по фрагменту: variant=0 — общий разбор, variant=1 — попунктная проверка."""
    i, total, chunk, full_text, contract_type_info = args
    result = []

    def collect(text):
        result.append(text)

    legal_context, topic_names = contract_legal_context(chunk, contract_type_info, full_text)
    legal_context_block = (
        f"\n\nРелевантные фрагменты кодексов Республики Таджикистан (RAG):\n{legal_context}\n"
        if legal_context else
        "\n\nRAG-контекст по кодексам Республики Таджикистан не найден или база еще не подключена.\n"
    )
    if topic_names:
        legal_context_block = (
            "\n\nПервые нормы списка подобраны под условия этого фрагмента (" + ", ".join(topic_names) + "). "
            "Сверь каждое такое условие с этими нормами: условие, которое хуже нормы для стороны, противоречит "
            "императивной норме или лишает право без защиты, — риск; ссылайся именно на эту норму."
            + legal_context_block
        )

    # Тип договора и его специфические проверки
    contract_type = contract_type_info.get("type", "неопределённый")
    type_guidance = _build_contract_type_guidance(contract_type)
    type_block = ""
    if contract_type != "неопределённый":
        type_block = (
            f"\n\nОпределённый тип договора: «{contract_type}» (уверенность {contract_type_info.get('confidence', 0):.0%}).\n"
            f"Специфические аспекты для проверки: {type_guidance}\n"
        )

    # Берем начало и конец документа для контекста
    full_text_preview = ""
    if total > 1:
        full_text_preview = f"\n\nКОНТЕКСТ ВСЕГО ДОКУМЕНТА (начало):\n{full_text[:1500]}\n...\n(конец):\n{full_text[-1500:]}\n"

    prompt = f"""Ты — юрист-аналитик по законодательству Республики Таджикистан с 20-летним опытом договорной работы.

ЮРИСДИКЦИЯ: ТОЛЬКО Республика Таджикистан (РТ). ЗАПРЕЩЕНО: РФ, Россия, ГК РФ.

Это ЧАСТЬ {i+1} из {total} договора.{full_text_preview}{type_block}

ПРОФЕССИОНАЛЬНАЯ МЕТОДОЛОГИЯ (действуй как старший юрист при проверке договора):
Перед составлением ответа проведи мысленно следующие этапы проверки этого фрагмента:
1. Существенные/обязательные условия для данного типа договора — присутствуют ли они и однозначно ли определены.
2. Сбалансированность интересов сторон — нет ли явного смещения прав/обязанностей в пользу одной стороны.
3. Соответствие императивным нормам ГК РТ — не противоречит ли условие обязательным правилам.
4. Полнота процедурных условий — сроки, порядок исполнения, оплаты, уведомлений, ответственности, расторжения.
5. Двусмысленность и неопределённость формулировок, которые могут привести к спору.

Проанализируй этот фрагмент и найди риски, которые реально способны причинить стороне
ущерб, потерю денег или потерю права. НЕ составляй список из каждой мелкой формальной
недосказанности — большинство пробелов в детализации не являются риском, если основное
условие в тексте есть. Пример НЕ риска: товар описан как «цемент М-400, 200 тонн» — этого
достаточно для определения ассортимента и количества, отсутствие более подробной
спецификации риском не является.
Для КАЖДОГО риска ОБЯЗАТЕЛЬНО укажи КОНКРЕТНУЮ СТАТЬЮ из RAG-контекста.

ВАЖНО: Пиши КАЖДЫЙ риск отдельным пунктом со ВСЕМИ 6 полями.
Если рисков нет — напиши "Юридически значимые риски не найдены".

НОРМЫ ЗАКОНОВ РТ (только эти статьи используй):
{legal_context_block}

Формат каждого риска (строго!):
• [Высокий/Средний/Низкий] Название риска.
  Пункт договора: номер пункта.
  Фрагмент договора: точная цитата 5-25 слов из договора.
  Норма РТ: [Название кодекса РТ], Глава [номер], Статья [номер] (только из RAG-контекста выше)
  Почему риск: объяснение 1 предложение.
  Что исправить: конкретная правка 1 действие.

ПРИМЕР правильного оформления:
• [Высокий] Риск одностороннего расторжения договора
  Пункт договора: 7.2
  Фрагмент договора: "Заказчик вправе расторгнуть договор без объяснения причин"
  Норма РТ: Гражданский кодекс РТ, Глава 27, Статья 450
  Почему риск: Ущемляет права исполнителя, так как не предусматривает взаимного права расторжения
  Что исправить: Добавить взаимное право расторжения договора для обеих сторон

ВАЖНЕЙШИЕ ПРАВИЛА:
1. Для СТАТЬИ используй ТОЛЬКО данные из RAG-контекста выше
2. Если статьи нет в RAG — пиши "статья не найдена в подключенной базе"
3. НЕ выдумывай статьи и главы
4. Фрагмент договора — точная цитата 5-25 слов из текста
5. Каждый риск = отдельный пункт со всеми 6 полями
6. После каждого риска ОБЯЗАТЕЛЬНО сделай пустую строку

Текст (часть {i+1} из {total}):
{chunk}"""

    prompt += """

КАЛИБРОВКА УРОВНЯ РИСКА (обязательно):
- Высокий: риск недействительности, существенной потери денег/права, одностороннего лишения права или нарушения обязательной нормы.
- Средний: существенная неопределённость, несбалансированное условие, неполный порядок исполнения, оплаты, ответственности или прекращения.
- Низкий: устранимый пробел, двусмысленность, редакционная неточность или недостающий срок/реквизит без немедленного существенного ущерба.
- Проверь каждый из трёх уровней отдельно. Не помечай все замечания как «Высокий»; выбери уровень по этим критериям.
- Не повышай и не понижай уровень ради количества. Если для уровня нет подтверждённого фрагмента договора и нормы из RAG, не создавай риск.
- НЕ Высокий (даже если условие можно было бы прописать подробнее): ссылка на приложение/спецификацию за детальным перечнем при наличии основного предмета в тексте; общая формулировка «существенное нарушение» без развёрнутого списка случаев — это обычная и допустимая договорная практика; отсутствие исчерпывающего состава акта приёма-передачи при наличии самого условия о его составлении; неустойка с указанным процентом и разумным пределом (капом) — наличие предела снижает риск, а не создаёт его. Такие пункты — самое большее Низкий, обычно вообще не риск.
- Ещё НЕ Высокий: периодическое изменение платы/цены с предварительным письменным уведомлением заранее (например, за 30 дней, не чаще раза в год) — это стандартное условие, а не одностороннее лишение права; отсутствие депозита/дополнительного обеспечения там, где закон не требует его обязательно; право одной стороны на досрочный отказ, если это НЕ лишает другую сторону права на судебную защиту, взыскание убытков или собственный отказ по своим основаниям. «Высокий» — это когда сторона теряет право или деньги БЕЗ какой-либо защиты или компенсации, а не когда условие просто асимметрично или неидеально детализировано.
- Предоплата/оплата после подписания, срок поставки от момента оплаты, отсылка к моменту
  подписания как точке отсчёта срока — это стандартная договорная техника, а не риск.
  Отсутствие полного описания процедуры (напр. точного момента перехода права
  собственности, детального гарантийного порядка) при наличии основного условия — это
  НЕ Высокий, а Низкий или вообще не риск: закон восполняет такие пробелы диспозитивными
  нормами.
- Самопроверка перед выводом: если в этом фрагменте получилось больше одного риска уровня
  «Высокий» — перечитай каждый из них по критерию выше («теряет право или деньги БЕЗ
  защиты») и понизь до «Средний»/«Низкий» все, что не проходит этот критерий буквально.
  «Высокий» — это редкая, а не стандартная оценка.
"""
    prompt += """

СТРОГИЙ ПРОТОКОЛ ДОКАЗАТЕЛЬНОСТИ (имеет приоритет над всеми предыдущими инструкциями):
1. Единственные допустимые источники права — фрагменты RAG, приведённые в этом запросе. Твои знания, память, предположения, аналогии и «обычная практика» не являются источниками.
2. Создавай риск только когда одновременно имеются: (а) дословная цитата из этого фрагмента договора; (б) конкретная статья, буквально присутствующая в RAG; (в) прямая логическая связь между ними.
3. Номер статьи, название кодекса, глава и содержание нормы должны быть скопированы из RAG. Запрещено дополнять, исправлять, угадывать или объединять номера статей.
4. Если RAG не содержит подходящей нормы, НЕ создавай риск и НЕ пиши «статья не найдена». Отсутствие основания — это не риск.
5. Не ссылайся на законы, судебную практику или органы вне RAG. Юрисдикция исключительно Республика Таджикистан. Любое упоминание РФ, России или российского права запрещено.
6. Не интерпретируй отсутствие условия как нарушение закона без прямой нормы RAG. Не называй договор недействительным, незаконным или ничтожным, если это прямо не следует из цитируемой нормы.
7. До ответа проведи внутреннюю проверку каждого пункта. Если не можешь подтвердить любой из шести полей — исключи пункт целиком.
8. Выводи только подтверждённые риски в заданном формате; никаких вступлений, дисклеймеров, предположений и дополнительных рекомендаций.
"""
    if variant == 1:
        numbered = re.findall(r"(?m)^\s*(\d+(?:\.\d+)*)[.)]", chunk)
        prompt += (
            "\n\nРЕЖИМ ПОПУНКТНОЙ ПРОВЕРКИ (дополнительный проход, он ловит то, что пропускает общий разбор). "
            + (f"В этом фрагменте пронумерованные пункты: {', '.join(numbered)}. " if numbered else "")
            + "Пройди по пунктам по очереди. По каждому реши, содержит ли он условие, которое ущемляет сторону, "
            "лишает права или денег без защиты, противоречит норме из списка выше либо оставляет вопрос на "
            "усмотрение одной стороны. Один пункт может дать несколько рисков (например, неустойка без предела и "
            "освобождение от ответственности в одном пункте — два риска). По пунктам без риска ничего не выводи. "
            "Формат и правила доказательности прежние."
        )
    if variant == 2:
        prompt += (
            "\n\nРЕЖИМ ОБРАТНОЙ СВЕРКИ «НОРМА → ПУНКТ» (третий проход: он ловит то, что общий и попунктный разборы "
            "пропускают). Идёт в обратную сторону: возьми по очереди нормы из списка выше, особенно первые, подобранные "
            "под условия фрагмента, и для каждой найди в тексте пункт, который с ней расходится: хуже нормы для стороны, "
            "противоречит императивному правилу, оставляет вопрос на усмотрение одной стороны, лишает права, денег или "
            "судебной защиты, нарушает требуемую форму, валюту, срок, порядок расчётов либо подсудность. "
            "Если расхождение есть — это риск: цитата пункта и эта норма. Если пункта нет — ничего не выводи. "
            "Один пункт может расходиться с несколькими нормами; тогда выбери ту, что точнее. Формат и правила "
            "доказательности прежние."
        )
    send_to_ollama(prompt, stream_callback=collect, num_predict=2400, temperature=0.25 if variant else 0.15)
    return {"idx": i, "content": normalize_legal_terms("".join(result))}


CONTRACT_SECOND_PASS = os.environ.get("CONTRACT_SECOND_PASS", "1") != "0"
# Третий проход идёт от нормы к пункту. По контрольному набору из 10 договоров с заложенными рисками он поднимает полноту
# и сглаживает разброс между запусками; CONTRACT_THIRD_PASS=0 возвращает два прохода (быстрее и дешевле).
CONTRACT_THIRD_PASS = os.environ.get("CONTRACT_THIRD_PASS", "1") != "0"


def analyze_chunk(args):
    """Фрагмент договора разбирается двумя независимыми проходами; одинаковые риски склеит парсер.

    Один проход модели на тех же условиях раз за разом пропускает часть нарушений и каждый раз разные,
    поэтому второй проход (попунктный) поднимает полноту и убирает зависимость отчёта от удачи.
    """
    if not CONTRACT_SECOND_PASS:
        return _analyze_chunk_variant(args, 0)
    variants = (0, 1, 2) if CONTRACT_THIRD_PASS else (0, 1)
    with ThreadPoolExecutor(max_workers=len(variants)) as pool:
        futures = [pool.submit(_analyze_chunk_variant, args, v) for v in variants]
        parts = []
        for future in futures:
            try:
                parts.append(future.result()["content"])
            except Exception as error:  # noqa: BLE001
                print(f"  ⚠️ Проход по фрагменту {args[0] + 1} не удался: {error}")
        if not parts:
            raise RuntimeError("все проходы анализа фрагмента завершились ошибкой")
    return {"idx": args[0], "content": "\n\n".join(parts)}

def _calculate_finding_quality(finding):
    """Оценивает качество и достоверность риска (0-1)"""
    if not finding or not isinstance(finding, dict):
        return 0.0
    
    quality = 0.0
    max_quality = 0.0
    
    # Критически важные поля (вес 60%)
    critical_fields = {
        'title': 0.15,      # Название риска
        'point': 0.15,      # Пункт договора
        'fragment': 0.20,   # Фрагмент договора (цитата)
        'norm': 0.10,       # Норма РТ
    }
    
    for field, weight in critical_fields.items():
        max_quality += weight
        value = finding.get(field, '')
        if value and len(str(value).strip()) > 3:
            # Проверяем, что фрагмент - это реальная цитата (есть кавычки или достаточно длинная)
            if field == 'fragment':
                if '"' in value or "'" in value or len(value) > 15:
                    quality += weight
            # Проверяем, что норма содержит статью
            elif field == 'norm':
                if re.search(r'статья\s*\d+', value, re.IGNORECASE) or 'не найдена' in value.lower():
                    quality += weight
            else:
                quality += weight
    
    # Важные поля (вес 30%)
    important_fields = {
        'why': 0.15,        # Почему риск
        'fix': 0.15,        # Что исправить
    }
    
    for field, weight in important_fields.items():
        max_quality += weight
        value = finding.get(field, '')
        if value and len(str(value).strip()) > 5:
            quality += weight
    
    # Дополнительные проверки (вес 10%)
    max_quality += 0.10
    
    # Бонус за наличие severity
    if finding.get('severity') in ['high', 'medium', 'low']:
        quality += 0.05
    
    # Бонус за конкретность (если есть номера пунктов, статей)
    text = ' '.join(str(v) for v in finding.values() if v)
    if re.search(r'\d+\.\d+', text):  # Номера пунктов (например, 7.2)
        quality += 0.05
    
    if re.search(r'статья\s*\d+', text, re.IGNORECASE):  # Номера статей
        quality += 0.05
    
    # Нормализуем к 0-1
    if max_quality > 0:
        quality = min(1.0, quality / max_quality)
    
    return quality

def _normalize_text(text):
    """Нормализует текст для сравнения"""
    if not text:
        return ""
    text = text.lower()
    text = re.sub(r'\s+', ' ', text)
    text = text.strip(' .:;,"\'')
    return text

def _calculate_text_similarity(text1, text2):
    """Вычисляет схожесть двух текстов (0-1)"""
    if not text1 or not text2:
        return 0.0
    
    norm1 = _normalize_text(text1)
    norm2 = _normalize_text(text2)
    
    if norm1 == norm2:
        return 1.0
    
    # Сравнение по словам
    words1 = set(norm1.split())
    words2 = set(norm2.split())
    
    if not words1 or not words2:
        return 0.0
    
    intersection = len(words1 & words2)
    union = len(words1 | words2)
    
    if union == 0:
        return 0.0
    
    jaccard = intersection / union
    
    # Увеличиваем вес для значимых слов (более 3 букв)
    significant1 = {w for w in words1 if len(w) > 3}
    significant2 = {w for w in words2 if len(w) > 3}
    
    if significant1 and significant2:
        sig_intersection = len(significant1 & significant2)
        sig_union = len(significant1 | significant2)
        if sig_union > 0:
            sig_jaccard = sig_intersection / sig_union
            return max(jaccard, sig_jaccard * 1.3)
    
    return jaccard

def _stems(text):
    return {w[:5] for w in re.findall(r"[а-яёa-z]{4,}", (text or "").lower())}


def _fragment_overlap(a, b):
    """Доля общих основ слов относительно меньшего фрагмента."""
    sa, sb = _stems(a), _stems(b)
    if not sa or not sb:
        return 0.0
    return len(sa & sb) / min(len(sa), len(sb))


def _is_duplicate_risk(risk1, risk2, threshold=0.70):
    """Проверяет, являются ли два риска дубликатами"""
    # Сравниваем названия
    title_sim = _calculate_text_similarity(risk1.get('title', ''), risk2.get('title', ''))
    
    # Сравниваем фрагменты договора
    frag_sim = _calculate_text_similarity(risk1.get('fragment', ''), risk2.get('fragment', ''))
    
    # Сравниваем пункты договора
    point_sim = _calculate_text_similarity(risk1.get('point', ''), risk2.get('point', ''))
    
    # Критерии дубликата:
    # 1. Очень похожие названия (>= 0.75) И похожие фрагменты/пункты (>= 0.70)
    if title_sim >= 0.75 and (frag_sim >= 0.70 or point_sim >= 0.70):
        return True
    
    # 2. Почти идентичные фрагменты (>= 0.90) И moderately похожие названия (>= 0.50)
    if frag_sim >= 0.90 and title_sim >= 0.50:
        return True
    
    # 3. Одинаковые пункты договора (>= 0.85) И похожие названия (>= 0.60)
    if point_sim >= 0.85 and title_sim >= 0.60:
        return True

    # 4. Одно и то же условие договора, описанное по-разному (общий и попунктный проход модели):
    #    тот же пункт и почти те же слова цитаты, либо цитата совпадает практически целиком.
    overlap = _fragment_overlap(risk1.get('fragment', ''), risk2.get('fragment', ''))
    same_point = bool((risk1.get('point') or '').strip()) and (risk1.get('point') or '').strip() == (risk2.get('point') or '').strip()
    if (same_point and overlap >= 0.6) or overlap >= 0.85:
        return True

    # 5. Тот же пункт, та же проверенная норма и заметное пересечение цитат: условие описано дважды
    #    разными кусками одной фразы («реже срока, по усмотрению» и «по усмотрению, товарами»).
    norm1 = str(risk1.get('norm') or '').strip().lower()
    if same_point and norm1 and norm1 == str(risk2.get('norm') or '').strip().lower() and overlap >= 0.5:
        return True

    return False

def _deduplicate_findings(findings):
    """Удаляет дубликаты рисков, оставляя наиболее полные и качественные"""
    if len(findings) <= 1:
        return findings
    
    unique_findings = []
    
    for risk in findings:
        is_duplicate = False
        best_match_idx = -1
        best_match_score = 0
        
        for idx, existing in enumerate(unique_findings):
            if _is_duplicate_risk(risk, existing):
                # Из пары остаётся находка с подтверждённой нормой, затем более качественная
                def _rank(item):
                    return (1 if item.get('_norm_verified') else 0, _calculate_finding_quality(item))
                risk_quality = _rank(risk)
                existing_quality = _rank(existing)

                # Если новый риск лучше, заменяем
                if risk_quality > existing_quality:
                    best_match_idx = idx
                    best_match_score = risk_quality
                is_duplicate = True
                break
        
        if is_duplicate and best_match_idx >= 0:
            unique_findings[best_match_idx] = risk
        elif not is_duplicate:
            unique_findings.append(risk)
    
    return unique_findings

def _apply_severity_heuristics(findings):
    """Escalate severity of verified findings for high-risk clause patterns.

    The models tend to under-report classic red flags.  These rules raise the
    severity when a confirmed clause objectively threatens a party's rights or
    money, and never downgrade a higher severity assigned by the model.

    Every rule is a pair: (groups, target).  Each group is a tuple of
    substrings; at least one substring of every group must occur in the
    verified clause text (fragment + title + rationale).  Rules are therefore
    keyword-grounded on verified text only - a hallucinated pattern can not
    produce a finding by itself.
    """
    rules = [
        # Одностороннее расторжение/изменение в пользу одной стороны.
        ((("односторонн",), ("расторж", "изменен", "отказ", "прекращени")), "high"),
        ((("расторж", "отказ"), ("без объяснения", "по своему усмотрению", "в любое время")), "high"),
        # Невозвратные платежи / предоплата без встречного предоставления.
        ((("возврату", "возврат"), ("не подлеж", "не возвраща")), "high"),
        ((("предоплат", "аванс"), ("100", "сто процентов", "полностью")), "high"),
        ((("оплаченные суммы", "оплата"), ("не возвращ",)), "high"),
        # Одностороннее изменение цены / тарифов.
        ((("измен",), ("цен", "тариф", "стоимость", "ставку")), "medium"),
        # Ограничение ответственности одной стороны.
        ((("ответственност",), ("не несет", "не несёт", "освобожда")), "medium"),
        ((("неустойк", "штраф", "пен"), ("только", "исключительн", "не более", "огранич")), "medium"),
        # Отсутствие гарантий/ответственности за результат.
        ((("гарант",), ("не предостав", "не даёт", "не дает")), "high"),
        ((("ответственност",), ("не огранич", "не несет")), "high"),
        # Невозможность исполнения/приостановление по усмотрению.
        ((("исполнени",), ("приостанови", "отложи", "по своему усмотрению")), "high"),
        # Смещение рисков на одну сторону.
        ((("риск",), ("покупател", "заказчик", "исполнитель", "поставщик")), "medium"),
    ]
    priorities = {"low": 0, "medium": 1, "high": 2}

    for finding in findings:
        current = finding.get("severity", "medium")
        # «Что исправить» и норма — рекомендация и ссылка модели, а не условие договора: из-за слов «в одностороннем
        # порядке» в совете к вполне взаимному праву отказа находка ошибочно становилась «Высокой».
        text = " ".join(str(finding.get(k, "") or "") for k in ("title", "fragment", "why")).lower()
        if not text:
            continue
        for groups, target in rules:
            if not all(any(n in text for n in group) for group in groups):
                continue
            if priorities.get(target, 0) > priorities.get(current, 0):
                print(f"  [SEVERITY] Эскалация: {current} → {target} | {finding.get('title', '')[:60]}")
                current = target
                finding["severity"] = target
    return findings

# Формулировки, которыми модель сама описывает находку как пробел/неопределённость, а
# не как реальную потерю права или денег без защиты — то есть, по её же собственному
# определению "Средний"/"Низкий" в промпте (см. КАЛИБРОВКА УРОВНЯ РИСКА), а не "Высокий".
_TRIVIAL_HIGH_LANGUAGE = (
    "неясно", "неточн", "неопределенн", "неопределённ", "не указан точн", "не детализ",
    "не конкретизир", "нет конкретн", "нет чёткого", "нет четкого", "может привести к спору",
    "может возникнуть спор", "рекомендуется конкретизировать", "рекомендуется уточнить",
    "желательно уточнить", "нет явного", "не прописан", "не оговорен", "не оговорён",
    "недостаточно детализ", "отсутствует детализация", "отсутствует конкретиз",
    "может быть спор", "уточнить порядок", "конкретизировать",
)
# Формулировки настоящей высокой тяжести — если находка содержит хотя бы одну, эвристика
# понижения её не трогает, даже если рядом есть слово из списка выше.
_GENUINE_SEVERE_LANGUAGE = (
    "недействительн", "ничтожн", "лишени", "лишает", "без права", "отказ от права",
    "отказывается от права", "не подлежит возврат", "безвозвратн", "кабальн",
    "чрезмерн", "без ограничения", "неограниченн", "бессрочн", "в одностороннем порядке",
    "без согласия", "без объяснения причин", "запрещ", "нарушение прямой нормы",
    "нарушает статью", "противоречит статье", "не несет ответственности",
    "не несёт ответственности",
)

def _downgrade_overreaching_high_severity(findings):
    """Counterpart to ``_apply_severity_heuristics``: that function only escalates,
    so a model that mislabels a routine drafting gap as "Высокий" is never corrected
    downward anywhere in the pipeline. Observed on gpt-5-nano against a genuinely
    well-drafted contract: "не указан точный ассортимент" (goods were named
    explicitly) and "оплата зависит от даты подписания" (ordinary prepayment) were
    both marked "Высокий", inflating the score of a clean contract past "Высокий
    риск — Не подписывать". Both examples used exactly the hedging language the
    prompt's own calibration section defines as "Средний"/"Низкий", so that language
    is a reliable signal the model didn't actually apply its own criterion ("теряет
    право или деньги БЕЗ защиты"). Downgrade only fires on that combination, and
    never on a finding that also carries genuine severe-language — a real red flag
    described with a hedge word stays "Высокий".
    """
    for finding in findings:
        if finding.get("severity") != "high" or finding.get("_heuristic") or finding.get("_deterministic"):
            continue
        text = " ".join(str(finding.get(k, "") or "") for k in ("title", "why", "fragment")).lower()
        if not text or any(p in text for p in _GENUINE_SEVERE_LANGUAGE):
            continue
        if any(p in text for p in _TRIVIAL_HIGH_LANGUAGE):
            print(f"  [SEVERITY] Понижение необоснованного 'Высокий' → 'Средний' | {finding.get('title', '')[:60]}")
            finding["severity"] = "medium"
    return findings

def _filter_findings_by_quality(findings, min_quality=0.45):
    """Фильтрует риски по минимальному качеству, но сохраняет важные эвристические находки."""
    filtered = []
    for finding in findings:
        quality = _calculate_finding_quality(finding)
        # Сохраняем эвристические находки из чек-листа существенных условий, даже если качество ниже порога.
        if quality < min_quality and finding.get("_heuristic") and finding.get("severity") in ("high", "medium"):
            quality = max(quality, min_quality)
            finding["_quality"] = quality
            filtered.append(finding)
            print(f"  [FILTER] Сохранён эвристический риск: {finding.get('title', 'N/A')[:60]} (quality={quality:.2f})")
            continue
        if quality >= min_quality:
            finding['_quality'] = quality
            filtered.append(finding)
        else:
            print(f"  [FILTER] Удален низкокачественный риск: {finding.get('title', 'N/A')[:60]} (quality={quality:.2f})")
    return filtered

def _quote_matches_contract(quote, source, coverage_threshold=0.70, min_matches=4):
    """Check a quote is grounded in the source.

    Accept an exact substring, a long contiguous phrase, or a keyword match:
    when at least 70% of the quote's significant words appear in the contract
    (with stem-insensitive comparison), the quote is treated as a reformulation
    of the actual clause rather than as a hallucination.
    """
    if not quote or not source:
        return False
    if quote in source:
        return True
    q_words = quote.split()
    if len(q_words) < 8:
        return False
    for i in range(len(q_words) - 5):
        if " ".join(q_words[i:i + 6]) in source:
            return True
    # Keyword-coverage fallback: tolerance for the model's reformulations.
    significant = [w for w in q_words if len(w) > 3]
    unique_sig = sorted(set(significant))
    if len(unique_sig) < min_matches:
        return False
    source_words = set(source.split())
    def _covered(word):
        if word in source_words:
            return True
        if len(word) >= 6:
            stem = word[:4]
            return any(other.startswith(stem) or word.startswith(other[:4]) for other in source_words if len(other) >= 4)
        return False
    covered = sum(1 for w in unique_sig if _covered(w))
    return covered / len(unique_sig) >= coverage_threshold

def _filter_findings_by_contract_evidence(findings, contract_text):
    """Keep findings whose quoted clause can be located in the source (exact or fuzzy)."""
    source = _normalize_text(contract_text)
    if not source:
        return []

    verified = []
    for finding in findings:
        quote = _normalize_text(finding.get("fragment", ""))
        quote = quote.strip('«»"\'')
        # A short or fabricated quote is not enough to support a legal finding.
        if len(quote) < 15 or not _quote_matches_contract(quote, source):
            print(f"  [EVIDENCE] Removed unsupported finding: {finding.get('title', 'N/A')[:60]}")
            continue
        finding["_contract_evidence"] = True
        verified.append(finding)
    return verified

def _filter_findings_by_rag_evidence(findings, contract_text, rag_context=""):
    """Keep findings grounded in the contract; mark missing RAG norms honestly instead of deleting."""
    verified = []
    for finding in findings:
        finding["fragment"] = _clean_quote(finding.get("fragment"))
        reason = detect_hallucination(finding, contract_text, rag_context)
        if reason == "цитата не найдена в тексте договора" and _repair_quote_from_contract(finding, contract_text):
            reason = detect_hallucination(finding, contract_text, rag_context)
        if reason == "цитата не найдена в тексте договора":
            print(f"  [RAG EVIDENCE] Removed ungrounded finding: {finding.get('title', 'N/A')[:60]} ({reason})")
            continue
        finding["_rag_evidence"] = True
        if reason in ("не указана конкретная статья РТ", "указанная статья отсутствует в подключённом RAG"):
            finding["norm"] = "Статья не найдена в подключённой базе (требуется ручная проверка)"
            finding["_norm_verified"] = False
        elif reason == "обнаружена ссылка на другую юрисдикцию":
            # Этот случай раньше попадал в ветку «подтверждено» ниже: номер из
            # «ст. 450 ГК РФ» подставлялся под одноимённую статью ГК РТ, и вывод,
            # построенный на чужом праве, выходил к пользователю с отметкой о
            # проверке. Совпадение номеров в двух кодексах ничего не значит.
            finding["norm"] = "Ссылка на право другой страны — вывод не подтверждён"
            finding["_norm_verified"] = False
        else:
            # Replace model wording with the exact code/chapter/article,
            # resolved against every connected codex — not only the Civil
            # Code — since a verified finding may legitimately cite the Tax,
            # Labor or Family code.
            original_norm = finding.get("norm", "")
            exact_norm = canonical_rag_norm_multi(_cited_article_numbers(original_norm), hint_text=original_norm)
            if exact_norm:
                finding["norm"] = exact_norm
                finding["_norm_verified"] = True
        verified.append(finding)
    return verified

def summarize_contract_risk(raw_score, findings_count, contract_len, wrong_jurisdiction_count):
    """Сводит накопленный риск к оценке, уровню и вердикту.

    Вынесено из estimate_risk_metrics отдельной функцией: здесь легко ошибиться
    в знаке, а проверить это внутри общего конвейера почти невозможно — нужен
    собранный RAG-индекс, текст договора и живой вывод модели.

    Возвращает (score, level, verdict, manual_review_required).
    """
    # 100 зарезервировано за подтверждённой человеком критической ситуацией:
    # автоматический разбор не вправе поставить максимум только потому, что
    # нашёл много пунктов.
    score = max(0, min(95, round(raw_score)))
    # Уровень выводится из той же оценки, что видит пользователь, — иначе в
    # отчёте появлялось противоречие вида «Высокий риск — 35/100».
    level = "Высокий" if score >= 65 else "Средний" if score >= 25 else "Низкий"

    # Упоминание права другой страны в тексте анализа означает, что доверять
    # разбору нельзя. Раньше за каждое такое упоминание из оценки риска
    # ВЫЧИТАЛОСЬ 20 баллов: чем сильнее модель путалась в юрисдикции, тем
    # безопаснее выглядел договор, и трёх упоминаний РФ хватало, чтобы
    # «Не подписывать» превратилось в «Можно подписывать». Оценку это больше
    # не трогает — риск договора не зависит от ошибок разбора, — но вердикт
    # обязан стать осторожным.
    analysis_compromised = wrong_jurisdiction_count > 0
    # Длинный договор не может быть «чистым»: если фильтры убрали все риски,
    # отчёт честно требует ручной проверки, а не выдаёт ложное «подписывать».
    manual_review_required = analysis_compromised or (findings_count == 0 and contract_len >= 1000)

    if score >= 65:
        verdict = "❌ Не подписывать"
    elif manual_review_required:
        verdict = "⚠️ Требуется ручная проверка"
    elif score >= 25:
        verdict = "⚠️ Требует правок"
    else:
        verdict = "✅ Можно подписывать"

    return score, level, verdict, manual_review_required


def _calculate_complexity_bonus(findings):
    """Вычисляет бонус за сложность и разнообразие рисков"""
    if not findings:
        return 0
    
    bonus = 0
    
    # Бонус за разнообразие типов рисков
    titles = [f.get('title', '').lower() for f in findings]
    keywords = set()
    for title in titles:
        words = re.findall(r'\b[а-яёa-z]{4,}\b', title)
        keywords.update(words)
    
    bonus += min(5, len(keywords) * 0.5)
    
    # Бонус за наличие конкретных норм РТ
    norms_with_articles = sum(1 for f in findings 
                              if re.search(r'статья\s*\d+', f.get('norm', ''), re.IGNORECASE))
    bonus += min(6, norms_with_articles * 1.5)
    
    # Бонус за наличие конкретных правок
    fixes_detailed = sum(1 for f in findings if len(f.get('fix', '')) > 25)
    bonus += min(3, fixes_detailed * 0.5)
    
    # Бонус за наличие конкретных цитат
    fragments_with_quotes = sum(1 for f in findings 
                                if ('"' in f.get('fragment', '') or "'" in f.get('fragment', '')))
    bonus += min(2, fragments_with_quotes * 0.3)
    
    return min(10, round(bonus))

# ================= ЧЕК-ЛИСТ ОБЯЗАТЕЛЬНЫХ УСЛОВИЙ ДОГОВОРА =================
ESSENTIAL_CLAUSE_PATTERNS = {
    "предмет договора": {
        "patterns": [
            r"предмет\w*\s+договор",
            r"предмет\w*\s+соглашени",
            r"предмет\w*\s+о\s+оказани",
            r"предмет\w*\s+поставки",
            r"предмет\w*\s+выполнени",
            r"предмет",
            r"обязует\w*\s+(?:передать|оказ\w+|выполн\w+|поставить|продать|предоставить|принять|заказать)",
            r"(?:оказывает|передаёт|передает|сдаёт|сдает|предоставляет|выполняет)\s+\w+\s+\w+",
        ],
        "severity": "high",
    },
    "цена и порядок расчётов": {
        # "цена товара"/"цена на товар" (не только "цена договора") и
        # "предоплата" (не только "аванс") — реальный договор поставки писал
        # именно так, с явным разделом «ЦЕНА ТОВАРА» и условием о 100%
        # предоплате. "вознаграждение" — стандартный термин платы по услугам
        # и поручению (не "цена", не "оплата"), "плата за..." — термин платы
        # по перевозке и найму. Ни один из этих договоров прежде не совпадал
        # ни с одним паттерном, хотя цена в каждом была указана явно.
        "patterns": [
            r"цен\w*\s+договор",
            r"стоимост",
            r"оплат\w*\s+договор",
            r"порядок\s+расчёт",
            r"аванс",
            r"предоплат",
            r"цена\s+договор",
            r"цен\w*\s+(?:на\s+)?товар",
            r"общая\s+стоимост",
            r"вознаграждени",
            r"плата\s+(?:за|по)\b",
            r"\d[\d\s]*\s*(?:сомони|смн|руб|долл|usd|евро)",
            r"оплат",
            r"\bцен[аыу]\b",
            r"арендн\w+\s+плат",
        ],
        "severity": "high",
    },
    "срок исполнения/действия": {
        # Раньше требовалось конкретное слово "срок", вплотную к одному из
        # исполнени/выполнени/оказани/поставки/действи, либо жёсткая фраза
        # "срок действия договора"/"срок исполнения обязательства". Три из
        # десяти реальных тестовых договоров указывали срок явно — «в течение
        # 10 дней с момента подписания», «в течение 2 суток», «на срок с 15
        # мая 2026 года по 15 мая 2028 года» — и ни один паттерн не совпадал:
        # чек-лист показывал срок отсутствующим там, где он был прямо в тексте.
        "patterns": [
            r"срок\w*\s+(?:исполнени|выполнени|оказани|поставки|действи)",
            r"период\s+поставки",
            r"начало\s+работ",
            r"окончани\w*\s+работ",
            r"срок\s+действия",
            r"срок\s+исполнени",
            r"заключ\w*\s+на\s+срок",
            r"в\s+течение\s+\d+\s*\(?\s*[а-яё]*\s*\)?\s*(?:календарн\w*\s+)?(?:дн|суток|месяц|год|недел)",
            r"\bс\s+\d{1,2}\s+[а-яё]+\s+\d{4}\s*(?:года)?\s*(?:по|до)\s+\d{1,2}\s+[а-яё]+\s+\d{4}",
            r"\bсрок\w*",
            r"бессрочн",
            r"вступает\s+в\s+силу",
        ],
        "severity": "high",
    },
    "ответственность сторон": {
        "patterns": [
            r"ответственност",
            r"неустойк",
            r"пен\w*\s+за\s+просрочк",
            r"штраф",
            r"возмещени\w*\s+убытк",
        ],
        "severity": "medium",
    },
    "порядок расторжения": {
        "patterns": [
            r"расторжени",
            r"прекращени\s+договор",
            r"односторонн\w*\s+расторжени",
            r"расторжени\w*\s+договор",
            r"расторг",
            r"отказ\w*\s+от\s+(?:исполнения\s+)?договор",
        ],
        "severity": "medium",
    },
    "разрешение споров": {
        "patterns": [
            r"разрешени\w*\s+спор",
            r"подсудност",
            r"арбитраж",
            r"\bсуд\b",
            r"разрешение\s+споров",
            r"компетентный\s+суд",
            r"\bспор",
            r"судебн\w+\s+порядк",
        ],
        "severity": "medium",
    },
    "форс-мажор": {
        "patterns": [
            r"форс-мажор",
            r"непреодолимой",
            r"обстоятельства\s+непреодолимой",
            r"чрезвычайны\w*\s+обстоятельств",
        ],
        "severity": "low",
    },
    "уведомления сторон": {
        "patterns": [
            r"уведомлени",
            r"извещени",
            r"порядок\s+уведомлени",
            r"порядок\s+извещени",
        ],
        "severity": "low",
    },
    "регулирование отношений": {
        "patterns": [
            r"действующ\w*\s+законодательств\w*\s+республик\w*\s+таджикистан",
            r"законодательств\w*\s+республик\w*\s+таджикистан",
            r"гк\s+рт",
            r"гр\s+кодекс\s+республик\w*\s+таджикистан",
        ],
        "severity": "medium",
    },
    "порядок изменения/дополнения": {
        "patterns": [
            r"порядок\s+изменени\w*\s+договор",
            r"порядок\s+дополнени\w*\s+договор",
            r"изменени\w*\s+договор\s+только\s+письмен",
            r"дополнени\w*\s+к\s+договор",
        ],
        "severity": "low",
    },
    "неустойка/санкции": {
        # Раньше требовалась буквально смежная фраза («неустойка ЗА просрочку»,
        # «пени ЗА просрочку»). Реальный договор писал «пеню в размере 0,1% от
        # суммы задолженности за каждый день просрочки» — между «пеню» и «за
        # просрочку» стоит сумма, и ни один паттерн не совпадал: пункт о пене
        # в договоре БЫЛ, а чек-лист честно показывал его отсутствующим.
        # Голый корень достаточен: «пеня»/«неустойка»/«штраф» сами по себе уже
        # сильный сигнал условия об ответственности за просрочку, так же как
        # в категории «ответственность сторон» ниже.
        "patterns": [
            r"неустойк",
            r"пен[ьяию]",
            r"штраф",
            r"санкци",
        ],
        "severity": "medium",
    },
    "конфиденциальность": {
        "patterns": [
            r"конфиденциальн\w*",
            r"неразглашени",
            r"сохранени\w*\s+конфиденциальност",
        ],
        "severity": "low",
    },
    "порядок приёма-передачи": {
        "patterns": [
            r"порядок\s+приёмк\w*\s+работ",
            r"порядок\s+передач\w*\s+товар",
            r"акт\s+приёмк\w*\s+работ",
            r"акт\s+приёмк\w*\s+поставки",
            r"приёмк|приемк",
            r"\bакт\w*\b",
        ],
        "severity": "low",
    },
}

# Существенные и обычные условия трудового договора. Общий чек-лист ему не подходит: «предмет», «цена» и
# «приёмка» в трудовом договоре заменяют трудовая функция, оплата труда и режим работы.
LABOR_ESSENTIAL_PATTERNS = {
    "трудовая функция (должность)": {
        "patterns": [r"должност", r"професси", r"специальност", r"трудов\w+\s+функци", r"принимается\s+на"],
        "severity": "high",
    },
    "место работы": {
        "patterns": [r"место\s+работы", r"рабоч\w+\s+мест", r"по\s+адресу"],
        "severity": "medium",
    },
    "размер и сроки оплаты труда": {
        "patterns": [r"заработн\w+\s+плат", r"оклад", r"оплат\w+\s+труда", r"сомони"],
        "severity": "high",
    },
    "режим рабочего времени и отдыха": {
        "patterns": [r"рабоч\w+\s+(?:врем|недел|день)", r"\d+\s*час", r"график\s+работы", r"режим\s+работы"],
        "severity": "medium",
    },
    "ежегодный оплачиваемый отпуск": {
        "patterns": [r"отпуск"],
        "severity": "medium",
    },
    "срок трудового договора": {
        "patterns": [r"\bсрок\w*", r"бессрочн", r"неопределённ\w+\s+срок", r"неопределенн\w+\s+срок", r"вступает\s+в\s+силу"],
        "severity": "medium",
    },
    "порядок расторжения договора": {
        "patterns": [r"расторг", r"увольн", r"прекращени"],
        "severity": "medium",
    },
    "порядок разрешения трудовых споров": {
        "patterns": [r"\bспор", r"\bсуд"],
        "severity": "low",
    },
}

# Условия, неприменимые к типу договора по самой его природе. Список короткий
# намеренно: сюда попадает только то, где отсутствие условия определяет вид
# договора, а не является упущением. Дарение безвозмездно по определению, мена
# — обмен вещами без денежного расчёта; требовать в них цену значит требовать
# перестать быть дарением и меной.
#
# Раньше contract_type_info принимался и не использовался: тип договора
# вычислялся классификатором и выбрасывался, а чек-лист применялся ко всем
# договорам одинаково. Договор дарения получал риск severity=high «отсутствует
# цена и порядок расчётов», и одного этого хватало, чтобы оценка ушла в
# «Средний риск — требует правок».
CLAUSES_NOT_APPLICABLE = {
    "дарение": {"цена и порядок расчётов"},
    "мены": {"цена и порядок расчётов"},
    # Поручитель гарантирует чужой долг, а не покупает и не платит цену —
    # требовать здесь "цену и порядок расчётов" так же неверно, как требовать
    # её от договора дарения.
    "поручительство": {"цена и порядок расчётов"},
}


def check_essential_conditions(text, contract_type_info=None):
    """Find essential clauses missing from the contract and return them as findings."""
    findings = []
    if not text or len(text.strip()) < 100:
        return findings

    # Тип берётся только при уверенной классификации: при слабом совпадении
    # ключевых слов пропустить существенное условие опаснее, чем показать
    # лишний пункт чек-листа.
    contract_type = (contract_type_info or {}).get("type")
    confidence = float((contract_type_info or {}).get("confidence") or 0)
    skip = CLAUSES_NOT_APPLICABLE.get(contract_type, set()) if confidence >= 0.5 else set()
    labor = contract_type == "трудовой договор" and confidence >= 0.5
    clauses = LABOR_ESSENTIAL_PATTERNS if labor else ESSENTIAL_CLAUSE_PATTERNS
    code_note = "Проверьте существенные условия в Трудовом кодексе РТ" if labor else "Проверьте существенные условия в Гражданском кодексе РТ"

    text_lower = text.lower()
    for clause, cfg in clauses.items():
        if clause in skip:
            continue
        found = any(re.search(p, text_lower) for p in cfg["patterns"])
        if found:
            continue
        findings.append({
            "title": f"Отсутствует условие: {clause}",
            "severity": cfg["severity"],
            "point": "весь договор",
            "fragment": "",
            "norm": code_note,
            "why": (
                f"В договоре не обнаружено условие о {clause}. "
                "Отсутствие существенного условия создаёт риск признания договора незаключённым "
                "или неопределённости прав и обязанностей сторон."
            ),
            "fix": f"Добавить в договор раздел, чётко определяющий {clause}",
            "_heuristic": True,
        })
    return findings

_ARTICLE_MENTION_RE = re.compile(
    r"\b(статья|статьи|статье|статью|статьёй|статьей|ст\.)\s*(\d+(?:[.\-]\d+)*(?:\s*(?:,|и)\s*\d+(?:[.\-]\d+)*)*)",
    re.IGNORECASE,
)
# Падеж слова «статья» сохраняется: «под статью 718» -> «под указанную выше статью».
_ARTICLE_MENTION_FORMS = {
    "статья": "указанная выше статья", "статьи": "указанной выше статьи", "статье": "указанной выше статье",
    "статью": "указанную выше статью", "статьёй": "указанной выше статьёй", "статьей": "указанной выше статьёй",
    "ст.": "указанной выше статье",
}


def _align_article_mentions(finding):
    """Номера статей в «Основании» и «Правке» не должны расходиться с нормой, проверенной по RAG.

    Модель пишет эти поля свободным текстом и может назвать статью, которой нет в «Норме РТ из RAG»
    (в отчёте стояло «Статья 675», а в правке — «под статью 718»). Такая ссылка не проверена ни по
    цитате, ни по базе, поэтому остаётся только ссылка на проверенную норму.
    """
    allowed = _cited_article_numbers(finding.get("norm", ""))
    if not allowed:
        return

    def fix(match):
        numbers = re.findall(r"\d+(?:[.\-]\d+)*", match.group(2))
        if all(number in allowed for number in numbers):
            return match.group(0)
        return _ARTICLE_MENTION_FORMS.get(match.group(1).lower(), "указанная выше статья")

    for key in ("why", "fix"):
        if finding.get(key):
            finding[key] = _ARTICLE_MENTION_RE.sub(fix, finding[key])


def _polish_findings(findings):
    """Заголовок с заглавной буквы и ссылки на статьи только из проверенной нормы."""
    for finding in findings:
        title = str(finding.get("title") or "").strip()
        if title:
            finding["title"] = title[0].upper() + title[1:]
        _align_article_mentions(finding)
    return findings


def _attach_rag_norm_text(findings):
    """Attach real article text from the connected RAG codexes to verified norms.

    A verified finding keeps its model-provided norm, but the actual statutory
    text is pulled from the loaded RAG index and stored in ``norm_text`` so
    the final report quotes the real norm instead of an approximation.

    Resolves each cited number through ``_resolve_rag_article_chunk`` — the
    SAME resolver ``canonical_rag_norm_multi`` already used (earlier in the
    pipeline, in ``_filter_findings_by_rag_evidence``) to rewrite
    ``finding["norm"]`` into its canonical "{code}, Глава N, Статья M" form.
    Reusing it, with that already-canonical norm text as the disambiguation
    hint, guarantees the quoted text can never name a different codex than
    the label above it — a real mismatch caught live: label said "Гражданский
    кодекс ... Статья 269" while the quoted text was the Tax Code's VAT-
    invoice article, because the label and the text used two different
    tie-break rules when the same bare number existed in both codexes.
    """
    cache = {}
    for finding in findings:
        if finding.get("_norm_verified") is False:
            continue
        norm_text_field = finding.get("norm", "")
        numbers = _cited_article_numbers(norm_text_field)
        if not numbers:
            continue
        cache_key = (tuple(numbers), norm_text_field)
        snippet = cache.get(cache_key)
        if snippet is None:
            best = ""
            for num in numbers:
                chunk = _resolve_rag_article_chunk(num, hint_text=norm_text_field)
                text = chunk.get("text", "") if chunk else ""
                if len(text) > len(best):
                    best = text
            snippet = _shorten_text(best, RAG_SNIPPET_MAX)
            cache[cache_key] = snippet
        if snippet:
            finding["norm_text"] = snippet
    return findings

def _severity_series(base, decay, count):
    """base + base*decay + base*decay^2 + ... for `count` terms.

    Each further finding of the same severity adds less than the previous
    one, so ten trivial findings can't out-accumulate three severe ones —
    but nothing here can, by itself, force the total past any threshold.
    """
    return sum(base * (decay ** i) for i in range(count))


def _raw_risk_score(high, medium, low):
    """Score (before the 95 cap in ``summarize_contract_risk``) from verified
    finding counts by severity, spanning the full 1-100 scale in proportion
    to what was actually found — not clustered at the top.

    Two earlier versions both failed this on real test contracts:

    1. A cumulative-probability model (score = 100 * (1 - prod(1 -
       impact/100)) over every finding) saturated near the 95 ceiling for
       almost any contract with more than ~4-5 findings of any severity —
       five contracts with genuinely different profiles (3/8/1, 5/6/2,
       2/6/3, 5/4/3, 5/5/1 high/medium/low) all scored 95.
    2. The fix for that added a fixed base the instant a single high-severity
       finding existed (55 + escalation), which fixed the ceiling problem but
       created a floor problem: on ten real test contracts spanning very
       different actual risk levels, EVERY one still landed at 65+ ("Высокий
       риск — не подписывать"), because almost any real contract has at
       least one finding the model calls "Высокий" somewhere. A fixed 55-
       point jump the moment that happens overrides everything else.

    This version has no fixed base for any tier — the score is purely the
    accumulated series per severity (see _severity_series), so one weak or
    borderline "Высокий" finding on an otherwise solid contract lands in the
    20s-30s, not an automatic 65+, while several genuinely severe findings
    still climb well past that. Separated out for the same reason as
    ``summarize_contract_risk``: easy to get wrong, and the mistake is
    invisible inside the full pipeline.
    """
    return (
        _severity_series(20, 0.78, high)
        + _severity_series(8, 0.72, medium)
        + _severity_series(2.5, 0.6, low)
    )

VERIFY_MAX_CONTRACT_CHARS = int(os.environ.get("VERIFY_MAX_CONTRACT_CHARS", "14000"))
VERIFY_MAX_FINDINGS = 24
VERIFY_BATCH = 8


# Названия сторон в запросе к поиску только мешают: «Исполнитель» цепляет статьи «Исполнитель завещания» и
# «Обжалование действий исполнителя органа исполнения», хотя речь о стороне договора.
_PARTY_WORDS_RE = re.compile(r"\b(?:исполнител|заказчик|сторон)\w*", re.IGNORECASE)
NORM_CANDIDATES_MAX = 7
# Отраслевые кодексы берутся в кандидаты только когда сама находка о соответствующей отрасли: иначе поиск по общим
# словам («отчёт», «уведомление») тянет налоговую отчётность и земельные платежи к любому договору.
_SECTOR_CODES = {
    "НК": r"налог|ндс|пошлин|сбор",
    "ЗмК": r"земел|участк",
    "ГрК": r"строител|градостроит|проектн",
    "КАТ": r"перевоз|автомобил|транспорт",
}


def _norm_candidates(finding, contract_type, labor, index):
    """Статьи, которые могут относиться к условию из находки: по теме условия и по смыслу находки."""
    search = lambda query, top_k: retrieve_rag(query, top_k=top_k, index=index)  # noqa: E731
    allowed = _contract_code_filter(labor)
    finding_text = " ".join(str(finding.get(k) or "") for k in ("fragment", "title", "why")).lower()

    def in_scope(item):
        if not allowed(item):
            return False
        code = str(item.get("code_short") or "")
        return all(re.search(pattern, finding_text) for short, pattern in _SECTOR_CODES.items() if code.startswith(short))

    seed = " ".join(str(finding.get(k) or "") for k in ("fragment", "title"))
    pool = contract_topics.topic_results(seed, search, contract_type, per_topic=1, max_topics=4, labor=labor)
    if not pool:
        # Условие не подпало ни под одну тему — подбираем темы по описанию риска.
        pool = contract_topics.topic_results(str(finding.get("why") or ""), search, contract_type,
                                             per_topic=1, max_topics=3, labor=labor)
    # Название типа договора тоже ищет темы, но уже после тем самого условия: «простой товарищества» подтягивает
    # статьи о совместной деятельности, даже когда пункт про «результат работ» или «назначение средств» этих слов
    # не содержит, и при этом не вытесняет статью, подобранную под сам пункт.
    pool += contract_topics.topic_results(str(contract_type or ""), search, contract_type, per_topic=1, max_topics=1, labor=labor)
    for fields in (("title", "why"), ("fragment",)):
        query = _PARTY_WORDS_RE.sub(" ", " ".join(str(finding.get(k) or "") for k in fields)).strip()
        if query:
            pool += [item for item in search(query, 8) if in_scope(item)][:4]
    out, seen = [], set()
    for item in pool:
        key = (item.get("code_short"), item.get("article"))
        if key in seen or not in_scope(item):
            continue
        seen.add(key)
        out.append(item)
        if len(out) >= NORM_CANDIDATES_MAX:
            break
    return out


def _llm_verify_findings(findings, contract_text, contract_type_info=None):
    """Второй проход: модель-проверяющий сверяет каждый вывод с полным текстом договора.

    Убирает выводы, которые сам договор опровергает другим пунктом (например, «не предусмотрена оплата
    фактических расходов», когда такой пункт есть) и обычную допустимую практику, понижает преувеличенный
    уровень и подбирает статью, которая действительно регулирует это условие. При любой ошибке модели
    выводы возвращаются без изменений.
    """
    pool = [f for f in findings if not f.get('_deterministic')]
    if not pool or not (contract_text or '').strip():
        return findings
    index = load_chat_rag_index(force=False)
    if not index.get('ready'):
        return findings
    contract_type = (contract_type_info or {}).get('type')
    labor = contract_topics.is_labor_text(contract_text)
    batches = [pool[i:i + VERIFY_BATCH] for i in range(0, min(len(pool), VERIFY_MAX_FINDINGS), VERIFY_BATCH)]
    dropped = set()
    with ThreadPoolExecutor(max_workers=min(3, len(batches))) as executor:
        for result in executor.map(lambda b: _verify_batch(b, contract_text, contract_type, labor, index), batches):
            dropped |= result
    return [f for f in findings if id(f) not in dropped]


def _verify_batch(batch, contract_text, contract_type, labor, index):
    """Проверка одной пачки выводов; возвращает множество id выводов, которые нужно убрать."""
    blocks, candidate_map = [], {}
    for n, finding in enumerate(batch, 1):
        options = _norm_candidates(finding, contract_type, labor, index)
        candidate_map[n] = options
        lines = []
        current = _cited_article_numbers(finding.get("norm", ""))
        if current:
            # Без названия и текста статьи проверяющий выбирал [0] вслепую, и чужая статья (отказ от договора
            # транспортной экспедиции вместо отказа от договора услуг) оставалась в отчёте.
            own = next((c for c in (_resolve_rag_article_chunk(n, finding.get("norm", "")) for n in sorted(current)) if c), None)
            own_text = re.sub(r"\s+", " ", (own or {}).get("text", "")).strip()[:400]
            lines.append(f"   [0] текущая ссылка модели: {finding.get('norm', '')}" + (f" — {own_text}" if own_text else ""))
        for k, item in enumerate(options, 1):
            text = re.sub(r"\s+", " ", item.get("text", "")).strip()[:400]
            lines.append(f"   [{k}] {item.get('citation') or legal_rag.citation_for(item)} — {item.get('title')}: {text}")
        blocks.append(
            f"ВЫВОД {n}: [{finding.get('severity')}] {finding.get('title')}\n"
            f"   Цитата: {finding.get('fragment')}\n   Почему риск: {finding.get('why')}\n"
            "   Нормы-кандидаты:\n" + "\n".join(lines)
        )

    prompt = (
        "Ты — старший юрист по праву Республики Таджикистан. Младший юрист составил выводы о рисках договора. "
        "Проверь каждый вывод по ПОЛНОМУ тексту договора и нормам-кандидатам.\n\n"
        f"ТЕКСТ ДОГОВОРА:\n{contract_text[:VERIFY_MAX_CONTRACT_CHARS]}\n\n"
        "ВЫВОДЫ:\n" + "\n\n".join(blocks) + "\n\n"
        "Для каждого вывода верни:\n"
        "- verdict: \"keep\" или \"drop\".\n"
        "- drop_reason (только при drop): \"contradicted\" — договор сам закрывает вопрос другим пунктом "
        "(в evidence дословно процитируй этот пункт); \"standard_practice\" — это обычное допустимое условие, "
        "которое закон разрешает и которое не ущемляет сторону.\n"
        "- severity: \"high\" — сторона теряет право или деньги без защиты либо условие противоречит императивной "
        "норме; \"medium\" — несбалансированное или неопределённое условие; \"low\" — устранимый пробел.\n"
        "- norm: номер нормы-кандидата, которая НАПРЯМУЮ регулирует именно это условие; 0 — если подходит текущая "
        "ссылка модели; если подходят и общая, и специальная для этого вида договора норма — выбирай специальную; "
        "-1 — если ни одна из норм не относится к этому условию (не подбирай норму «по смыслу», "
        "если она про другое).\n"
        "Не повышай и не понижай уровень без причины, не добавляй новых выводов.\n"
        "Ответ — только JSON: {\"items\":[{\"id\":1,\"verdict\":\"keep\",\"drop_reason\":\"\",\"evidence\":\"\","
        "\"severity\":\"high\",\"norm\":1}]}"
    )
    try:
        raw = _analysis_llm(prompt, 2600, json_mode=True)
        if os.environ.get("VERIFY_DEBUG"):
            print("  [VERIFY-DEBUG] candidates: " + " || ".join(blocks)[:6000] + " [VERIFY-DEBUG] answer: " + str(raw)[:3000])
        data = json.loads(re.sub(r"^```(?:json)?|```$", "", (raw or "").strip(), flags=re.M).strip())
        items = {int(item["id"]): item for item in data.get("items", []) if str(item.get("id", "")).lstrip("-").isdigit()}
    except Exception as error:  # noqa: BLE001
        print(f"  [VERIFY] Проверочный проход не выполнен ({type(error).__name__}): {error}")
        return set()

    source = _normalize_text(contract_text)
    dropped = set()
    for n, finding in enumerate(batch, 1):
        item = items.get(n)
        if not item:
            continue
        if str(item.get("verdict")).lower() == "drop":
            reason = str(item.get("drop_reason") or "").lower()
            evidence = _normalize_text(str(item.get("evidence") or "")).strip('«»"\'')
            if reason == "standard_practice" or (reason == "contradicted" and len(evidence) >= 15 and _quote_matches_contract(evidence, source)):
                print(f"  [VERIFY] Убран вывод ({reason}): {finding.get('title', '')[:70]}")
                dropped.add(id(finding))
                continue
        severity = str(item.get("severity") or "").lower()
        if severity in ("high", "medium", "low") and severity != finding.get("severity"):
            print(f"  [VERIFY] Уровень {finding.get('severity')} → {severity}: {finding.get('title', '')[:60]}")
            finding["severity"] = severity
        try:
            choice = int(item.get("norm"))
        except (TypeError, ValueError):
            continue
        options = candidate_map.get(n) or []
        if 1 <= choice <= len(options):
            picked = options[choice - 1]
            finding["norm"] = picked.get("citation") or legal_rag.citation_for(picked)
            finding["_norm_verified"] = True
            finding.pop("norm_text", None)
        elif choice == -1:
            finding["norm"] = "Статья не найдена в подключённой базе (требуется ручная проверка)"
            finding["_norm_verified"] = False
            finding.pop("norm_text", None)
    return dropped


def estimate_risk_metrics(analysis_text, contract_text="", rag_context="", contract_type_info=None, verifier=None):
    text = (analysis_text or "").lower()
    
    if not text.strip():
        return {
            "score": 0,
            "level": "Низкий",
            "high": 0,
            "medium": 0,
            "low": 0,
            "verdict": "✅ Можно подписывать",
            "findings": [],
            "wrong_jurisdiction_mentions": 0,
            "hallucinations_detected": 0,
        }

    wrong_jurisdiction_violations = detect_wrong_jurisdiction(analysis_text)
    wrong_jurisdiction_count = len(wrong_jurisdiction_violations)
    
    if wrong_jurisdiction_count > 0:
        print(f"⚠️ ОБНАРУЖЕНО {wrong_jurisdiction_count} УПОМИНАНИЙ РОССИЙСКИХ ЗАКОНОВ!")

    # Парсер рисков — устойчив к опечаткам и отклонениям модели (когда она
    # опускает [severity] или меняет порядок строк), чтобы не терять реальные риски.
    findings = []
    current = None
    pending_title = None
    seen_titles = set()

    FIELD_PATTERNS = [
        ("point", re.compile(r'Пункт\s+договора\s*:\s*(.*)', re.IGNORECASE)),
        ("fragment", re.compile(r'Фрагмент\s+договора\s*:\s*(.*)', re.IGNORECASE)),
        ("norm", re.compile(r'Норма\s+РТ\s*:\s*(.*)', re.IGNORECASE)),
        ("why", re.compile(r'Почему\s+риск\s*:\s*(.*)', re.IGNORECASE)),
        ("fix", re.compile(r'Что\s+исправить\s*:\s*(.*)', re.IGNORECASE)),
    ]

    def _new_finding(title, severity):
        t = re.sub(r'\[[^\]]*\]\s*', '', title).strip()
        t = re.sub(r'^[\s\u2022\-\*\u2023]+', '', t).strip()
        t = re.sub(r'^Риск[: ]+', '', t, flags=re.IGNORECASE).strip()
        if not t or len(t) < 3:
            t = "Юридический риск"
        return {
            "title": t,
            "severity": severity,
            "point": "",
            "fragment": "",
            "norm": "",
            "why": "",
            "fix": "",
        }

    def _title_key(title):
        t = re.sub(r"\W+", " ", (title or "")).strip().lower()
        return " ".join(t.split())[:80]

    for raw_line in (analysis_text or "").splitlines():
        line = raw_line.strip()

        if not line:
            if current:
                title_key = _title_key(current.get("title"))
                if title_key and title_key not in seen_titles:
                    seen_titles.add(title_key)
                    findings.append(current)
                current = None
            pending_title = None
            continue

        severity = None
        if re.search(r'\[(?:Высокий|высок)\]', line):
            severity = "high"
        elif re.search(r'\[(?:Средний|средн)\]', line):
            severity = "medium"
        elif re.search(r'\[(?:Низкий|низк)\]', line):
            severity = "low"

        if severity:
            if current:
                title_key = _title_key(current.get("title"))
                if title_key and title_key not in seen_titles:
                    seen_titles.add(title_key)
                    findings.append(current)
            current = _new_finding(line, severity)
            pending_title = None
            continue

        # Поле текущего риска?
        field_matched = False
        if current:
            for fname, pat in FIELD_PATTERNS:
                m = pat.search(line)
                if m:
                    current[fname] = m.group(1).strip()
                    field_matched = True
                    break
        if field_matched:
            continue

        # Поле, но риск ещё не открыт (модель опустила [severity]) — восстанавливаем
        # заголовок из буфера и открываем риск со severity medium; он будет
        # эскализирован _apply_severity_heuristics при необходимости.
        if not current and pending_title:
            current = _new_finding(pending_title, "medium")
            pending_title = None
            for fname, pat in FIELD_PATTERNS:
                m = pat.search(line)
                if m:
                    current[fname] = m.group(1).strip()
                    break
            continue

        # Иначе: потенциальный заголовок следующего риска (буферизуем).
        pending_title = line

    if current:
        title_key = _title_key(current.get("title"))
        if title_key and title_key not in seen_titles:
            seen_titles.add(title_key)
            findings.append(current)

    print(f"  [PARSER] Найдено рисков до фильтрации: {len(findings)}")
    
    # Фильтруем низкокачественные риски
    findings = _filter_findings_by_quality(findings, min_quality=0.45)
    findings = _filter_findings_by_contract_evidence(findings, contract_text)
    findings = _filter_findings_by_rag_evidence(findings, contract_text, rag_context)

    # Дубли уходят до проверки: общий и попунктный проход модели описывают часть условий дважды
    findings = _deduplicate_findings(findings)

    if verifier and findings:
        findings = verifier(findings, contract_text)
    
    # Добавляем эвристический чек-лист отсутствующих существенных условий.
    # Для «отсутствующего» условия цитата из договора невозможна, поэтому добавляем после фильтров.
    heuristic_findings = check_essential_conditions(contract_text, contract_type_info)
    findings.extend(heuristic_findings)

    # Ссылка на право/суд другой страны в самом договоре — проверяем
    # регексом по тексту договора, а не полагаемся на то, заметит ли это
    # модель (см. docstring check_wrong_jurisdiction_in_contract).
    jurisdiction_finding = check_wrong_jurisdiction_in_contract(contract_text)
    if jurisdiction_finding:
        # Тот же вывод, который сделала модель, дублировал бы проверенную находку.
        findings = [f for f in findings if not detect_wrong_jurisdiction(str(f.get("fragment") or ""))]
        findings.append(jurisdiction_finding)

    # Удаляем дубликаты
    findings = _deduplicate_findings(findings)

    # Понижаем "Высокий", которым модель по факту описала обычный пробел в детализации
    # (см. docstring _downgrade_overreaching_high_severity), ДО эскалации — чтобы
    # находка с одновременно тревожным и формальным описанием всё равно могла
    # вернуться на "Высокий" по конкретному ключевому признаку ниже.
    findings = _downgrade_overreaching_high_severity(findings)

    # Усиливаем severity по эвристикам (одностороннее расторжение → высокий и т.п.)
    findings = _apply_severity_heuristics(findings)

    # Прикрепляем реальный текст статей из подключённого RAG к подтверждённым нормам.
    findings = _attach_rag_norm_text(findings)
    findings = _polish_findings(findings)

    print(f"  [PARSER] После фильтрации, эвристик и дедупликации: {len(findings)}")
    for f in findings:
        quality = f.get('_quality', 0)
        print(f"    [{f['severity']}] {f['title'][:50]} | quality={quality:.2f} | point={bool(f['point'])} frag={bool(f['fragment'])} norm={bool(f['norm'])}")

    # Чек-лист отсутствующих существенных условий (check_essential_conditions)
    # не является выводом модели — это механический regex-перебор, который
    # честно "находит" отсутствие условия и тогда, когда в договоре реально
    # нет текста для проверки (скан без текстового слоя и т.п.). Раньше эти
    # пункты считались наравне с подтверждёнными находками модели, и на
    # документах с почти пустым текстом чек-лист (7-8 однотипных пунктов)
    # полностью определял score и вердикт "Не подписывать", хотя по сути
    # анализа договора не произошло. Теперь эти пункты не входят в счёт
    # severity/score — они показываются отдельным информационным блоком.
    real_findings = [f for f in findings if not f.get('_heuristic')]
    checklist_findings = [f for f in findings if f.get('_heuristic')]

    # Подсчет по severity — только по подтверждённым находкам модели
    high = sum(1 for f in real_findings if f['severity'] == 'high')
    medium = sum(1 for f in real_findings if f['severity'] == 'medium')
    low = sum(1 for f in real_findings if f['severity'] == 'low')

    raw_score = _raw_risk_score(high, medium, low)

    score, level, verdict, manual_review_required = summarize_contract_risk(
        raw_score,
        findings_count=len(real_findings),
        contract_len=len((contract_text or "").strip()),
        wrong_jurisdiction_count=wrong_jurisdiction_count,
    )
    if manual_review_required:
        print("  [PARSER] ⚠️ Требуется ручная проверка")

    # Норма, которую не удалось подтвердить по подключённому RAG. Раньше это
    # число всегда уходило в интерфейс нулём, хотя фильтр такие выводы уже
    # помечал, — пользователь видел «галлюцинаций: 0» независимо от реальности.
    unverified_norms = sum(1 for f in real_findings if f.get("_norm_verified") is False)

    # Очищаем внутренние поля
    clean_findings = [{k: v for k, v in f.items() if not k.startswith('_')} for f in real_findings]
    clean_checklist_findings = [{k: v for k, v in f.items() if not k.startswith('_')} for f in checklist_findings]

    return {
        "score": score,
        "level": level,
        "high": high,
        "medium": medium,
        "low": low,
        "verdict": verdict,
        "findings": clean_findings,
        "checklist_findings": clean_checklist_findings,
        "hallucinations_detected": unverified_norms,
        "wrong_jurisdiction_mentions": wrong_jurisdiction_count,
        "wrong_jurisdiction_violations": wrong_jurisdiction_violations[:10],
        "manual_review_required": manual_review_required,
    }

def format_verified_findings_for_prompt(findings, max_items=20):
    if not findings:
        return "Подтвержденные риски не найдены."

    severity_labels = {"high": "Высокий", "medium": "Средний", "low": "Низкий"}
    priority = {"high": 0, "medium": 1, "low": 2}
    ordered = sorted(
        findings,
        key=lambda item: (priority.get(item.get("severity"), 9)),
    )

    blocks = []
    for idx, finding in enumerate(ordered[:max_items], 1):
        blocks.append(
            "\n".join([
                f"{idx}. [{severity_labels.get(finding.get('severity'), 'Низкий')}] {finding.get('title') or 'Риск без названия'}",
                f"   Пункт договора: {finding.get('point') or 'не указан'}",
                f"   Фрагмент договора: {finding.get('fragment') or 'не указан'}",
                f"   Норма РТ: {finding.get('norm') or 'статья не найдена в подключенной базе'}",
                f"   Почему риск: {finding.get('why') or 'не указано'}",
                f"   Что исправить: {finding.get('fix') or 'уточнить условие договора'}",
            ])
        )
    return "\n\n".join(blocks)

def build_grounded_contract_report(metrics, contract_type_info=None, lang="ru"):
    """Render the final report from verified records only; no second model pass.

    lang="tg": подписи отчёта на таджикском (doc_lang.REPORT_LABELS). Тексты рисков к этому
    моменту уже переведены (doc_lang.localize_report_inputs); русская версия не меняется.
    """
    L = doc_lang.REPORT_LABELS.get(lang) or doc_lang.REPORT_LABELS["ru"]
    findings = metrics.get("findings", [])
    severity_labels = L["severity"]
    lines = [
        L["title"],
        "",
        L["source"].format(source=CONTRACT_RAG_SOURCE_NAME),
        L["scope"],
        L["honest"],
    ]
    if L.get("translated"):
        lines.extend(["", L["translated"]])
    # Добавляем тип договора, если определён
    if contract_type_info and contract_type_info.get("type") != "неопределённый":
        lines.append(f"")
        lines.append(L["type"].format(type=contract_type_info['type'], confidence=f"{contract_type_info.get('confidence', 0):.0%}"))
        if contract_type_info.get('types_with_scores'):
            alt_types = [t['type'] for t in contract_type_info['types_with_scores'][1:3] if t['score'] > 0.2]
            if alt_types:
                lines.append(L["alt"].format(types=", ".join(alt_types)))
    lines.append("")
    lines.append(L["risks"])
    if not findings:
        lines.extend([
            "",
            L["none"],
        ])
        if metrics.get("manual_review_required"):
            lines.extend([
                "",
                L["manual"],
            ])
    else:
        for number, finding in enumerate(findings, 1):
            lines.extend([
                "",
                f"#### {number}. {finding.get('title') or L['risk_default']}",
                f"- {L['level']}: **{severity_labels.get(finding.get('severity'), severity_labels['low'])}**",
                f"- {L['point']}: {finding.get('point') or L['point_none']}",
                f"- {L['quote']}: «{finding.get('fragment') or L['quote_none']}»",
                f"- {L['norm']}: {finding.get('norm') or ''}",
                f"- {L['why']}: {finding.get('why') or ''}",
                f"- {L['fix']}: {finding.get('fix') or ''}",
            ])
            if finding.get("norm_text"):
                lines.extend([
                    "",
                    L["norm_text"],
                    finding["norm_text"],
                ])

    checklist_findings = metrics.get("checklist_findings") or []
    if checklist_findings:
        lines.extend([
            "",
            L["checklist"],
            "",
            L["checklist_note"],
        ])
        for finding in checklist_findings:
            lines.extend([
                "",
                f"- **{finding.get('title') or L['condition']}** "
                f"({L['significance']}: {severity_labels.get(finding.get('severity'), severity_labels['low'])}) — "
                f"{finding.get('fix') or ''}",
            ])

    lines.extend([
        "",
        L["summary"],
        f"- {L['score']}: **{metrics.get('score', 0)}/100**",
        f"- {L['level']}: **{metrics.get('level', severity_labels['low'])}**",
        f"- {L['high']}: **{metrics.get('high', 0)}**",
        f"- {L['medium']}: **{metrics.get('medium', 0)}**",
        f"- {L['low']}: **{metrics.get('low', 0)}**",
        f"- {L['verdict']}: **{metrics.get('verdict', '')}**",
        "",
        L["footer"],
    ])
    return "\n".join(lines)

_RF_TERM_RE = re.compile(
    r"\[УДАЛЕНО:[^\]]*\]"  # already redacted — pass through, don't re-wrap
    r"|\bГК\s*РФ\b"
    r"|\bТК\s*РФ\b"
    r"|\bНК\s*РФ\b"
    r"|\bРоссии\b"
    r"|\bРоссийск(?:ая|ой|ую)\s+Федераци\w*"
    r"|\bРФ\b",
    re.IGNORECASE,
)


def _redact_rf_match(match):
    matched = match.group(0)
    if matched.startswith("[УДАЛЕНО:"):
        return matched
    return f"[УДАЛЕНО: {matched}]"


def normalize_legal_terms(text):
    """Redacts mentions of Russian law from model output.

    This runs on chunk output right after generation, and again on the fully
    assembled report as a safety net — so it must be idempotent. The old
    version ran six separate re.sub passes: "ГК РФ" got wrapped into
    "[УДАЛЕНО: ГК РФ]" by the first pass, and the later bare "РФ" pass then
    matched the "РФ" still sitting inside those brackets, producing
    "[УДАЛЕНО: ГК [УДАЛЕНО: РФ]]" — and running the whole function twice
    compounded it further into doubly-nested brackets, observed verbatim in
    a real analysis report ("[УДАЛЕНО: [УДАЛЕНО: Российская Федерация]]").
    A single combined pattern fixes both: one term is matched and consumed
    at a time (no leftover substring for a later alternative to re-catch),
    and an already-bracketed span is recognized and left untouched.
    """
    text = _RF_TERM_RE.sub(_redact_rf_match, text)
    text = re.sub(r"(Норма\s+РТ\s*:\s*)кодекс\s*,", r"\1глава,", text, flags=re.IGNORECASE)
    text = re.sub(r"(Норма\s+РТ\s*:\s*)кодекс\s+и\s+статья", r"\1глава и статья", text, flags=re.IGNORECASE)
    return text

# ================= SSE ГЕНЕРАТОР =================
def _analysis_llm(prompt, num_predict, json_mode=False):
    """Вызов модели для перевода документа и отчёта: без размышлений, температура 0."""
    return llm_once(MODEL_NAME, prompt, num_predict, temperature=0.0, timeout=240, json_mode=json_mode,
                    openai_model=ANALYSIS_GPT_MODEL, reasoning_effort="minimal")


def _with_heartbeat(fn, send_event, step, message, start=9, cap=14, every=8):
    """Выполняет fn в потоке и пока она работает отдаёт признаки жизни (SSE не должен молчать минуту).

    Генератор: yield-ит события, а результат функции возвращает через StopIteration.value.
    """
    pool = ThreadPoolExecutor(max_workers=1)
    future = pool.submit(fn)
    waited = 0
    try:
        while True:
            done, _ = wait([future], timeout=every)
            if done:
                return future.result()
            waited += every
            yield send_event({"step": step, "message": message, "progress_percent": min(cap, start + waited // every)}, "status")
    finally:
        pool.shutdown(wait=False)


def generate_sse(file_path, request_id, original_filename=None, generate_revised=False):
    def send_event(data, event_type="message"):
        payload = {
            "type": event_type,
            "request_id": request_id,
            "timestamp": time.time()
        }
        if isinstance(data, dict):
            payload.update(data)
        else:
            payload["data"] = str(data)
        return f"data: {json.dumps(payload, ensure_ascii=False)}\n\n"

    try:
        print(f"🚀 Начало обработки: {request_id}")

        if not os.path.exists(file_path):
            print(f"❌ Файл не найден: {file_path}")
            yield send_event({"error": f"Файл не найден: {os.path.basename(file_path)}"}, "error")
            return

        # Contract analysis is fail-closed: do not fall back to a different
        # code or to a previously selected RAG source.
        try:
            contract_rag = ensure_contract_rag_ready()
        except Exception as rag_error:
            yield send_event({
                "error": f"RAG {CONTRACT_RAG_SOURCE_NAME} is unavailable: {rag_error}",
                "step": "rag_not_ready",
            }, "error")
            return

        yield send_event({
            "step": "rag_ready",
            "message": f"RAG ready: {CONTRACT_RAG_SOURCE_NAME} ({len(contract_rag.get('chunks', []))} fragments)",
            "source": CONTRACT_RAG_SOURCE_NAME,
            "progress_percent": 1,
        }, "status")
        yield send_event({"step": "reading", "file": os.path.basename(file_path), "progress_percent": 3}, "status")
        time.sleep(0.1)

        ext = os.path.splitext(file_path)[1].lower()
        full_text = ""

        print(f"📋 Расширение: {ext}, размер: {os.path.getsize(file_path)} байт")

        try:
            if ext == ".pdf":
                full_text = extract_text_from_pdf(file_path)
            elif ext == ".docx":
                full_text = extract_text_from_docx(file_path)
            elif ext == ".doc":
                full_text = extract_text_from_doc(file_path)
            elif ext == ".txt":
                full_text = extract_text_from_txt(file_path)
            else:
                yield send_event({
                    "error": f"Формат '{ext}' не поддерживается",
                    "supported": [".pdf", ".docx", ".doc", ".txt"]
                }, "error")
                return
        except Exception as extract_err:
            error_msg = f"Ошибка извлечения текста: {str(extract_err)}"
            print(f"❌ {error_msg}")
            yield send_event({"error": error_msg, "step": "extraction_failed"}, "error")
            return

        # Порог поднят с 50 до 300 символов: реальный договор всегда заметно
        # длиннее, а 50 символов пропускали случаи вроде отсканированного PDF
        # без текстового слоя, где pypdf вытаскивал только заголовок/пометку
        # сканера. С таким текстом полноценный анализ невозможен: RAG-модель
        # находит от силы 1 реальный пункт, а эвристический чек-лист
        # существенных условий не совпадает ни с одним паттерном по всем
        # категориям сразу — отчёт выглядит как анализ, но им не является.
        # Честная ошибка здесь лучше, чем уверенный вердикт на пустом месте.
        if not full_text or len(full_text.strip()) < 300:
            length = len(full_text.strip()) if full_text else 0
            hint = (
                " Похоже, это скан без текстового слоя — OCR в системе пока не реализован, "
                "загрузите документ с текстовым слоем (например, экспортированный из Word) "
                "или текстовую копию (DOCX/TXT)."
                if ext == ".pdf"
                else ""
            )
            yield send_event({
                "error": f"Текст не извлечён или слишком короткий для анализа ({length} симв.).{hint}",
                "length": length,
            }, "error")
            return

        # Таджикский договор разбирается по русскому переводу (корпус кодексов и промпты русские),
        # а готовый отчёт переводится обратно: язык ответа следует за языком документа.
        source_lang = "ru"
        if doc_lang.is_tajik_text(full_text):
            try:
                original_text = full_text
                translated_text = yield from _with_heartbeat(
                    lambda: doc_lang.translate_text(original_text, "tg", "ru", _analysis_llm, kind="текст договора"),
                    send_event, "translating", "Шартнома ба забони русӣ тарҷума мешавад...")
                if translated_text and len(translated_text) >= len(original_text) * 0.4:
                    full_text, source_lang = translated_text, "tg"
                    print(f"🌐 Договор на таджикском: переведён на русский ({len(full_text)} симв.)")
            except Exception as translate_error:  # noqa: BLE001
                print(f"⚠️ Перевод договора не удался, разбор по оригиналу: {translate_error}")

        print(f"✅ Текст извлечён: {len(full_text)} символов")
        yield send_event({
            "step": "extracted",
            "chars": len(full_text),
            "words": len(full_text.split()),
            "extension": ext,
            "progress_percent": 8
        }, "status")
        time.sleep(0.1)

        chunks = split_text_smart(full_text, CHUNK_SIZE, CHUNK_OVERLAP)
        print(f"📦 Разбито на {len(chunks)} чанков")

        yield send_event({"step": "chunked", "count": len(chunks), "progress_percent": 12}, "status")
        yield send_event({"step": "analyzing_start", "total": len(chunks), "progress_percent": 15}, "status")

        # Классифицируем тип договора
        contract_type_info = classify_contract_type(full_text)
        print(f"📋 Тип договора: {contract_type_info['type']} (уверенность: {contract_type_info['confidence']:.0%})")
        if contract_type_info['types_with_scores']:
            for t in contract_type_info['types_with_scores'][:3]:
                print(f"   - {t['type']}: {t['score']:.0%} ({t.get('matches', 0)} совпадений)")

        tasks = [(i, len(chunks), c, full_text, contract_type_info) for i, c in enumerate(chunks)]
        all_findings = []
        errors_count = 0

        with ThreadPoolExecutor(max_workers=MAX_WORKERS) as executor:
            futures = {executor.submit(analyze_chunk, t): i for i, t in enumerate(tasks)}
            pending = set(futures.keys())
            done = 0
            analysis_started_at = time.time()
            last_logged_done = None
            last_progress_sent = None

            while pending:
                completed, pending = wait(pending, timeout=10, return_when=FIRST_COMPLETED)

                if not completed:
                    elapsed = int(time.time() - analysis_started_at)
                    model_wait_progress = min(35, 15 + int(elapsed / 30))
                    chunk_progress = min(88, 15 + int((done / max(len(chunks), 1)) * 70))
                    heartbeat_progress = max(model_wait_progress, chunk_progress)
                    if done != last_logged_done:
                        yield send_event({
                            "step": "analyzing",
                            "processed": done,
                            "total": len(chunks),
                            "progress_percent": heartbeat_progress,
                            "message": f"Модель анализирует документ, готово {done}/{len(chunks)}"
                        }, "status")
                        last_logged_done = done
                        last_progress_sent = heartbeat_progress
                    elif heartbeat_progress != last_progress_sent:
                        yield send_event({
                            "progress_percent": heartbeat_progress,
                            "processed": done,
                            "total": len(chunks)
                        }, "progress")
                        last_progress_sent = heartbeat_progress
                    continue

                for future in completed:
                    i = futures[future]
                    try:
                        res = future.result()
                        all_findings.append((i, res["content"]))
                        done += 1
                        # Do not expose a draft model response.  It may be
                        # incomplete and is not the verified final report.
                        yield send_event({
                            "step": "chunk_done",
                            "idx": i + 1,
                            "total": len(chunks),
                            "message": f"Reviewed section {i + 1}/{len(chunks)}; preparing final report",
                        }, "status")
                        progress = min(15 + int(done / len(chunks) * 73), 88)
                        yield send_event({
                            "progress_percent": progress,
                            "processed": done,
                            "total": len(chunks)
                        }, "progress")
                        last_progress_sent = progress
                    except Exception as e:
                        errors_count += 1
                        done += 1
                        print(f"❌ Ошибка чанка {i+1}: {e}")
                        yield send_event({
                            "error": f"Ошибка части {i+1}: {str(e)[:100]}",
                            "idx": i + 1
                        }, "error")

        print(f"✅ Анализ завершён: {len(all_findings)} чанков, {errors_count} ошибок")

        all_findings.sort(key=lambda x: x[0])
        findings_text = [f"=== Часть {i+1} из {len(all_findings)} ===\n{t}\n" for i, t in all_findings]
        full_analysis_text = "\n".join(findings_text)

        yield send_event({"step": "summary_start", "progress_percent": 92}, "status")

        # Получаем RAG контекст для финального отчёта
        rag_summary_query = full_analysis_text[:5000] + "\n\n" + full_text[:3000]
        rag_summary_context = rag_context_for_query(rag_summary_query, top_k=RAG_TOP_K)
        rag_summary_block = (
            f"\n\nРелевантные фрагменты кодексов Республики Таджикистан (RAG):\n{rag_summary_context}\n"
            if rag_summary_context else
            "\n\nRAG-контекст по кодексам Республики Таджикистан не найден или база еще не подключена.\n"
        )
        
        risk_metrics = yield from _with_heartbeat(
            lambda: estimate_risk_metrics(
                full_analysis_text,
                contract_text=full_text,
                rag_context=rag_summary_context,
                contract_type_info=contract_type_info,
                verifier=lambda found, text: _llm_verify_findings(found, text, contract_type_info),
            ),
            send_event, "verifying",
            "Проверка выводов по тексту договора..." if source_lang == "ru" else "Санҷиши хулосаҳо аз рӯи матни шартнома...",
            start=92, cap=95)
        verified_findings_text = format_verified_findings_for_prompt(risk_metrics.get("findings", []))

        # Формируем промпт для summary с жёстким требованием указать статьи РТ
        summary_prompt = f"""Ты — старший юрист-эксперт по законодательству Республики Таджикистан с 20-летним опытом.

ЗАДАЧА: Составь структурированное юридическое заключение по результатам анализа договора.

РЕЗУЛЬТАТЫ АНАЛИЗА (найденные риски):
{verified_findings_text}

НОРМАТИВНАЯ БАЗА (фрагменты кодексов РТ):
{rag_summary_block}

СТРУКТУРА ЗАКЛЮЧЕНИЯ:

1. 🔴 ГЛАВНЫЕ РИСКИ — перечисли ВСЕ риски в формате:
   **Название риска** — уровень: Высокий/Средний/Низкий
   - Пункт договора: номер пункта
   - Фрагмент договора: точная цитата из договора
   - Норма РТ: укажи КОНКРЕТНУЮ статью, главу, кодекс РТ из RAG-контекста выше
   - Почему риск: объяснение
   - Что исправить: конкретная правка

2. 🟡 РЕКОМЕНДАЦИИ — конкретные исправления в договоре

3. ⚖️ ВЕРДИКТ: {risk_metrics['verdict']}

ВАЖНЕЙШИЕ ТРЕБОВАНИЯ:
- Для КАЖДОГО риска укажи КОНКРЕТНУЮ статью и главу из кодексов РТ (ГК РТ, и т.д.)
- Используй ТОЛЬКО статьи из RAG-контекста выше. Если статьи нет в RAG — пиши "статья не найдена в подключенной базе"
- НЕ ВЫДУМЫВАЙ статьи
- Фрагмент договора — ОБЯЗАТЕЛЬНО точная цитата 5-25 слов из договора
- Перечисли ВСЕ риски (высокие, средние, низкие)
- Юрисдикция: ТОЛЬКО Республика Таджикистан

Юридическое заключение:"""

        print("🧠 Генерация финального заключения...")
        try:
            # Do not let a second generative pass introduce unsupported claims.
            # The published report is rendered only from findings that passed
            # contract-quote and RAG-article verification.
            report_metrics = None
            if source_lang == "tg":
                try:
                    report_metrics, report_type_info = yield from _with_heartbeat(
                        lambda: doc_lang.localize_report_inputs(risk_metrics, contract_type_info, _analysis_llm),
                        send_event, "translating_report", "Ҳисобот ба забони тоҷикӣ тарҷума мешавад...", start=93, cap=96)
                    final_summary = build_grounded_contract_report(report_metrics, contract_type_info=report_type_info, lang="tg")
                except Exception as report_error:  # noqa: BLE001
                    print(f"⚠️ Перевод отчёта не удался, отчёт на русском: {report_error}")
                    final_summary = build_grounded_contract_report(risk_metrics, contract_type_info=contract_type_info)
            else:
                final_summary = build_grounded_contract_report(risk_metrics, contract_type_info=contract_type_info)

            if final_summary and not final_summary.startswith("⚠️"):
                revised_contract = None
                if generate_revised:
                    yield send_event({
                        "step": "revision_start",
                        "message": "Preparing the revised contract draft; final report will be shown when it is ready",
                        "progress_percent": 94,
                    }, "status")
                    revised_parts = []
                    for part_index, source_chunk in enumerate(chunks):
                        revised_parts.append(revise_contract_chunk((part_index, len(chunks), source_chunk)))
                        yield send_event({
                            "step": "revision_progress",
                            "message": f"Preparing revised contract: {part_index + 1}/{len(chunks)}",
                            "progress_percent": min(98, 94 + int((part_index + 1) / max(len(chunks), 1) * 4)),
                        }, "status")
                    revised_text = "\n\n".join(revised_parts)
                    if source_lang == "tg":
                        try:
                            revised_text = doc_lang.translate_text(revised_text, "ru", "tg", _analysis_llm, kind="текст договора")
                        except Exception as revise_error:  # noqa: BLE001
                            print(f"⚠️ Перевод исправленного договора не удался, остаётся русский: {revise_error}")
                    revised_contract = create_revised_contract_output(
                        revised_text,
                        original_filename or os.path.basename(file_path),
                    )
                metrics_header = f"""📊 МЕТРИКИ РИСКА (расчет системы)
- Балл риска: {risk_metrics['score']}/100
- Уровень риска: {risk_metrics['level']}
- Высоких рисков: {risk_metrics['high']}
- Средних рисков: {risk_metrics['medium']}
- Низких рисков: {risk_metrics['low']}
- Вердикт: {risk_metrics['verdict']}

"""
                final_summary = normalize_legal_terms(final_summary)
                response_metrics = {
                    "score": risk_metrics["score"],
                    "level": risk_metrics["level"],
                    "high": risk_metrics["high"],
                    "medium": risk_metrics["medium"],
                    "low": risk_metrics["low"],
                    "verdict": risk_metrics["verdict"],
                    "hallucinations_detected": risk_metrics.get("hallucinations_detected", 0),
                    "wrong_jurisdiction_mentions": risk_metrics.get("wrong_jurisdiction_mentions", 0),
                    # Флаг доходил только косвенно, текстом вердикта. Интерфейсу
                    # он нужен явно: на нём держится предупреждение о том, что
                    # разбору доверять нельзя.
                    "manual_review_required": risk_metrics.get("manual_review_required", False),
                }
                yield send_event({
                    "step": "summary_complete",
                    "content": final_summary,
                    "length": len(final_summary),
                    "revised_contract": revised_contract,
                    "metrics": response_metrics,
                    "risk_map": _contract_risk_map(full_text, risk_metrics, report_metrics, source_lang),
                }, "summary_complete")
                print("✅ Заключение сгенерировано")
            else:
                yield send_event({
                    "error": f"Не удалось сгенерировать заключение: {final_summary[:100]}",
                    "step": "summary_error"
                }, "error")
        except Exception as e:
            yield send_event({
                "error": f"Ошибка синтеза: {str(e)}",
                "step": "summary_error"
            }, "error")

        yield send_event({
            "step": "complete",
            "status": "success",
            "chunks_processed": len(all_findings),
            "errors_count": errors_count
        }, "complete")

        print(f"🎉 Обработка {request_id} завершена")

    except GeneratorExit:
        print(f"⚠️ Клиент отключился: {request_id}")
        raise
    except Exception as e:
        error_detail = f"{str(e)}\n\n{traceback.format_exc()}"
        print(f"❌ Критическая ошибка SSE: {error_detail}")
        yield send_event({
            "type": "error",
            "error": error_detail[:500],
            "step": "critical_error"
        }, "error")
    finally:
        try:
            if os.path.exists(file_path):
                os.remove(file_path)
                print(f"🗑️ Файл удалён: {os.path.basename(file_path)}")
        except Exception as e:
            print(f"⚠️ Не удалось удалить файл: {e}")

# ================= CHAT + RAG ENDPOINTS =================
@app.route("/api/result/<filename>")
def download_result(filename):
    if os.path.basename(filename) != filename or not re.match(r"^[a-f0-9]{32}_[A-Za-z0-9_-]+\.(pdf|doc|docx|txt)$", filename):
        return jsonify({"error": "Result not found"}), 404
    path = os.path.join(RESULTS_DIR, filename)
    if not os.path.isfile(path):
        return jsonify({"error": "Result not found"}), 404
    return send_from_directory(
        RESULTS_DIR,
        filename,
        as_attachment=True,
        download_name=filename.split("_", 1)[1],
    )

@app.route("/api/rag/set-source", methods=["POST"])
def rag_set_source():
    data = request.get_json(silent=True) or {}
    filename = data.get("filename", "").strip()
    if not filename:
        return jsonify({"error": "filename is required"}), 400
    try:
        candidate = activate_rag_source(filename)
    except FileNotFoundError:
        return jsonify({"error": f"RAG source not found: {filename}"}), 404
    except ValueError as e:
        return jsonify({"error": str(e)}), 400
    return jsonify({
        "success": True,
        "source": os.path.basename(candidate),
        "index": os.path.basename(_current_rag_index),
    })

@app.route("/api/rag/status")
def rag_status():
    try:
        return jsonify(rag_status_payload())
    except Exception as e:
        return jsonify({"ready": False, "error": str(e)}), 500

@app.route("/api/rag/reindex", methods=["POST"])
def rag_reindex():
    try:
        index = load_rag_index(force=True)
        chat_index = load_chat_rag_index(force=True)
        payload = rag_status_payload()
        payload["rebuilt"] = bool(index.get("ready"))
        payload["chat_rebuilt"] = bool(chat_index.get("ready"))
        payload["chat_chunks"] = len(chat_index.get("chunks", []))
        return jsonify(payload)
    except Exception as e:
        return jsonify({"ready": False, "error": str(e)}), 500

@app.route("/api/rag/search", methods=["POST"])
def rag_search():
    data = request.get_json(silent=True) or {}
    query = data.get("query", "")
    top_k = int(data.get("top_k", RAG_TOP_K))
    return jsonify({
        "query": query,
        "results": retrieve_rag(query, top_k=top_k, index=load_chat_rag_index()),
        "status": rag_status_payload(),
    })

@app.route("/api/rag/articles", methods=["POST"])
def rag_articles():
    data = request.get_json(silent=True) or {}
    # Список статей по умолчанию убран: он повторял ошибочную нумерацию со
    # страницы квалификации («247 — кража»), и запрос без номеров молча
    # возвращал не те статьи. Перечень статей УК РТ отдаёт /api/crime/catalog.
    numbers = data.get("numbers") or []
    if not numbers:
        return jsonify({"error": "numbers is required"}), 400
    source = data.get("source") or CRIME_RAG_SOURCE_NAME
    return jsonify({
        "articles": extract_rag_articles(numbers, source_name=source),
        "status": rag_status_payload(),
    })

def _crime_description(data):
    """Описание события. Ключ приходит разным: фронт исторически слал
    crime_description, а эндпоинт читал description — запрос падал в 400."""
    for key in ("description", "crime_description", "query", "text"):
        value = (data.get(key) or "").strip()
        if value:
            return _crime_text_to_russian(value)
    return ""


def _crime_llm_translate_to_russian(text):
    """Точный перевод описания с таджикского моделью; пусто — не вышло."""
    prompt = (
        "Переведи текст с таджикского на русский язык точно: не добавляй и не убирай факты, "
        "сохрани все числа, суммы, названия улиц и городов, способ действий и орудия. "
        "Ответ — только перевод, без пояснений и кавычек.\n\nТЕКСТ:\n" + text[:2000] + "\n\nПЕРЕВОД:"
    )
    try:
        raw = llm_once(CRIME_MODEL, prompt, 600, temperature=0.0, keep_alive=CRIME_KEEP_ALIVE,
                       timeout=45, openai_model=CRIME_GPT_MODEL)
    except Exception as error:  # noqa: BLE001
        print("  [CRIME] перевод моделью не удался (%s)" % type(error).__name__)
        return ""
    return (raw or "").strip().strip("«»\"")


def _crime_source_lang(data):
    """Язык исходного описания: таджикское описание получает ответ на таджикском."""
    for key in ("description", "crime_description", "query", "text"):
        value = (data.get(key) or "").strip()
        if value:
            try:
                return "tg" if lang_service.detect_lang(value, default="ru") == "tg" else "ru"
            except Exception:  # noqa: BLE001
                return "ru"
    return "ru"


CRIME_TG_RULE = (
    "Переведи разбор по уголовному праву Республики Таджикистан на таджикский язык (кириллица), точно и понятно "
    "человеку без юридического образования.\n"
    "Правила: строки-заголовки разделов (например «ЧТО ДЕЛАТЬ ПОСТРАДАВШЕМУ», «ЧТО ГРОЗИТ НАРУШИТЕЛЮ», «КАКИЕ ЗАКОНЫ "
    "НАРУШЕНЫ», «1. КВАЛИФИКАЦИЯ», «2. САНКЦИИ ПО ТЕКСТУ СТАТЬИ», «3. ФАКТЫ И ДОКАЗАТЕЛЬСТВА», «4. ОБСТОЯТЕЛЬСТВА», "
    "«5. ПРАВОВОЙ АНАЛИЗ», «6. ИТОГОВЫЙ ВЫВОД») оставь без изменений на русском вместе с номерами; номера статей, частей, "
    "пунктов, числа, суммы и сроки не меняй; цитаты из текста статей в «ёлочках» тоже переведи на таджикский точно, "
    "сохранив кавычки, числа, суммы и сроки; пометки «(общий "
    "совет)» и «(ст. N УПК РТ)» переведи, номер оставь; нумерацию и маркеры списков сохрани. "
    "Не оставляй русских слов: весь текст, кроме строк-заголовков разделов, должен быть на таджикском. "
    "Ничего не добавляй и не объясняй. Ответ — только перевод.\n\nТЕКСТ:\n"
)

CRIME_NOT_FOUND_MESSAGE_TG = (
    "Дар Кодекси ҷиноятии Ҷумҳурии Тоҷикистон меъёрҳои ба ин тавсиф дахлдор ёфт нашуданд. "
    "Ҳодисаро муфассалтар тавсиф кунед: аниқ чӣ рӯй дод, бо кадом усул, бо чӣ зарар расонида шуд. "
    "Берун аз матни КҶ ҶТ система ҷавоб намедиҳад."
)

# Итог для плашки считается по русскому разбору до перевода: метки «Квалификация:» и
# «Наказание по статье:» после перевода меняются, и страница уже не нашла бы их в тексте.
_CRIME_SUMMARY_NEGATIVE = re.compile(
    r"(невозможно|нельзя|не удалось|не указан|не определ|недостаточно|отсутствует|не найден)", re.I)


def _crime_summary_lines(text):
    """Квалификация и наказание одной строкой из русского разбора; none — квалификацию не установили."""
    def pick(pattern):
        found = re.findall(pattern, text or "", re.I)
        return found[-1].strip() if found else ""

    def clean(value):
        return re.sub(r"\*\*", "", re.sub(r"^[\s•\-–*]+", "", value)).strip()

    qual = clean(pick(r"Квалификация:\s*([^\n]+)") or pick(r"Основная статья[^:\n]*:\s*([^\n]+)"))
    punish = clean(pick(r"Наказание по статье:\s*([^\n]+)") or pick(r"Основное наказание:\s*([^\n]+)"))
    return {
        "qual": qual,
        "punish": "" if _CRIME_SUMMARY_NEGATIVE.search(punish) else punish,
        "none": (not qual) or bool(_CRIME_SUMMARY_NEGATIVE.search(qual)),
    }


def _crime_summary(text, lang="ru"):
    """Итог для плашки; на таджикском запросе обе строки переводятся."""
    summary = _crime_summary_lines(text)
    if lang == "tg" and not summary["none"]:
        try:
            out, _ok = _crime_to_tajik([summary["qual"], summary["punish"]])
            summary["qual"], summary["punish"] = out[0], out[1]
        except Exception as error:  # noqa: BLE001
            print("  [CRIME] перевод итога не удался (%s)" % type(error).__name__)
    summary["lang"] = lang
    return summary


# Тексты статей и названия для таджикского запроса: индекс кодекса русский, а страница
# обязана отвечать на языке вопроса. Перевод машинный, поэтому страница помечает его как таковой.
CRIME_TR_CACHE = {}
CRIME_TR_CACHE_MAX = 2000
CRIME_TR_MAX_ITEMS = 40
CRIME_TR_MAX_CHARS = 6000
CRIME_TR_SHORT = 400
CRIME_STATUTE_TG_RULES = (
    "Это текст статьи уголовного или процессуального закона Республики Таджикистан: сохрани структуру "
    "(части, пункты, нумерацию), номера статей и частей, числа, суммы и сроки. «Статья N» — «Моддаи N». "
)


def _crime_to_tajik(items):
    """Русские строки -> таджикские. Возвращает (список, список флагов «переведено»).

    Короткие строки идут одним вызовом, длинные (тексты статей) — каждая отдельно. Что перевести не
    удалось, остаётся русским оригиналом и в кэш не попадает.
    """
    items = [str(item or "") for item in items]
    out = list(items)
    done = [False] * len(items)
    pending_short, pending_long = [], []
    for i, text in enumerate(items):
        if not text.strip():
            done[i] = True
        elif text in CRIME_TR_CACHE:
            out[i], done[i] = CRIME_TR_CACHE[text], True
        elif len(text) <= CRIME_TR_SHORT:
            pending_short.append(i)
        else:
            pending_long.append(i)

    def remember(i, value):
        value = (value or "").strip()
        if not value or value == items[i]:
            return
        if len(CRIME_TR_CACHE) >= CRIME_TR_CACHE_MAX:
            CRIME_TR_CACHE.pop(next(iter(CRIME_TR_CACHE)))
        CRIME_TR_CACHE[items[i]] = value
        out[i], done[i] = value, True

    if pending_short:
        try:
            translated = _tajik_cards([items[i] for i in pending_short])
        except Exception as error:  # noqa: BLE001
            print("  [CRIME] перевод строк не удался (%s)" % type(error).__name__)
            translated = None
        for i, value in zip(pending_short, translated or []):
            remember(i, value)

    for i in pending_long:
        try:
            if _llm_tajik_available():
                value = doc_lang.translate_text(items[i], "ru", "tg", _analysis_llm, kind="текст статьи закона",
                                                rules=CRIME_STATUTE_TG_RULES, workers=2)
            elif lang_service.translator.available():
                value = lang_service.translator.translate_text(items[i], "ru", "tg")
            else:
                value = ""
        except Exception as error:  # noqa: BLE001
            print("  [CRIME] перевод статьи не удался (%s)" % type(error).__name__)
            value = ""
        remember(i, value)
    return out, done


@app.route("/api/crime/translate", methods=["POST"])
@app.route("/api/codex/translate", methods=["POST"])
def crime_translate():
    """Перевод названий и текстов статей на таджикский для страниц квалификации и кодексов."""
    data = request.get_json(silent=True) or {}
    items = data.get("items")
    if not isinstance(items, list) or not items:
        return jsonify({"error": "items is required"}), 400
    items = [str(item or "")[:CRIME_TR_MAX_CHARS] for item in items[:CRIME_TR_MAX_ITEMS]]
    translated, flags = _crime_to_tajik(items)
    return jsonify({"items": translated, "translated": flags})


# ---------------------------------------------------------------------------
# Страница /contract/codex: все кодексы РТ из общего RAG-индекса.
# ---------------------------------------------------------------------------
_codex_state = {"key": None, "catalog": None}


def _codex():
    """Каталог кодексов по текущему индексу; None — индекс не готов. Пересобирается при смене индекса."""
    index = load_chat_rag_index()
    if not index.get("ready") or not index.get("chunks"):
        return None
    key = (id(index), index.get("built_at"), len(index["chunks"]))
    if _codex_state["key"] != key:
        _codex_state["catalog"] = codex_catalog.Catalog(index)
        _codex_state["key"] = key
    return _codex_state["catalog"]


def _codex_unavailable():
    return jsonify({"error": "Индекс кодексов РТ не готов", "codes": []}), 503


@app.route("/api/codex/catalog")
def codex_catalog_route():
    """Список кодексов (группы, число статей и глав) — главный экран страницы."""
    catalog = _codex()
    if catalog is None:
        return _codex_unavailable()
    return jsonify(catalog.catalog())


@app.route("/api/codex/toc")
def codex_toc():
    """Оглавление кодекса: разделы -> главы -> статьи (номер и название)."""
    catalog = _codex()
    if catalog is None:
        return _codex_unavailable()
    toc = catalog.toc(request.args.get("code", ""))
    if toc is None:
        return jsonify({"error": "Кодекс не найден"}), 404
    return jsonify(toc)


# ---------------------------------------------------------------------------
# Граф связей статей: какие статьи кодексов ссылаются друг на друга.
# ---------------------------------------------------------------------------
_graph_state = {"key": None, "graph": None}


def _graph():
    """Граф по текущему индексу; None — индекс не готов. Строится за доли секунды."""
    index = load_chat_rag_index()
    if not index.get("ready") or not index.get("chunks"):
        return None
    key = (id(index), index.get("built_at"), len(index["chunks"]))
    if _graph_state["key"] != key:
        _graph_state["graph"] = article_graph.ArticleGraph(
            index["chunks"], lambda source: codex_catalog.code_id_for(source)[0])
        _graph_state["key"] = key
    return _graph_state["graph"]


# ---------------------------------------------------------------------------
# Симулятор суда: ИИ играет судью, противную сторону и свидетеля.
# ---------------------------------------------------------------------------
_court_cards = {}


def _court_lang(value):
    return "tg" if str(value or "").strip().lower() == "tg" else "ru"


def _court_json(prompt, num_predict):
    raw = llm_once(MODEL_NAME, prompt, num_predict, temperature=0.5, timeout=180, json_mode=True,
                   openai_model=chat_modes.strong_model() or ANALYSIS_GPT_MODEL, reasoning_effort="low")
    return _parse_json_object(raw)


def _court_to_ru(text, lang):
    """Реплика пользователя по-русски: промпты и корпус законов русские."""
    if lang != "tg" or not text:
        return text
    try:
        return doc_lang.translate_text(text, "tg", "ru", _analysis_llm, kind="реплика в судебном заседании")
    except Exception as e:  # noqa: BLE001
        print("court translate in: %s" % e)
        return text


def _court_from_ru(items, lang):
    if lang != "tg":
        return items
    try:
        return doc_lang.translate_items(items, "ru", "tg", _analysis_llm, kind="реплики судебного заседания")
    except Exception as e:  # noqa: BLE001
        print("court translate out: %s" % e)
        return items


# ---------------------------------------------------------------------------
# Голос персонажей симулятора. По умолчанию голос даёт браузер (бесплатно);
# COURT_TTS=openai включает живые голоса OpenAI (около $0,015 за минуту речи).
# Озвучка кешируется на диске: повтор той же реплики денег не стоит.
# ---------------------------------------------------------------------------
COURT_TTS = (os.environ.get("COURT_TTS") or "browser").strip().lower()
TTS_MODEL = os.environ.get("TTS_MODEL") or "gpt-4o-mini-tts"
_TTS_DIR = os.path.join(TEMP_DIR, "tts_cache")
_TTS_VOICES = {
    ("judge", "m"): "onyx", ("judge", "f"): "sage",
    ("opponent", "m"): "ash", ("opponent", "f"): "coral",
    ("witness", "m"): "echo", ("witness", "f"): "shimmer",
}
_TTS_STYLE = {
    "judge": "Ты судья в зале суда: говори спокойно, весомо и официально, с паузами.",
    "opponent": "Ты сторона в судебном споре: говори уверенно, напористо и чётко, как опытный юрист.",
    "witness": "Ты свидетель в суде: говори естественно, немного взволнованно, простыми словами.",
}


def _tts_enabled():
    return COURT_TTS == "openai" and bool(OPENAI_API_KEY)


@app.route("/api/tts", methods=["POST"])
def court_tts():
    """Озвучка реплики персонажа (mp3). 404 — облачный голос выключен, страница берёт голос браузера."""
    if not _tts_enabled():
        return jsonify({"error": "Облачный голос выключен"}), 404
    data = request.get_json(silent=True) or {}
    text = re.sub(r"\s+", " ", str(data.get("text") or "")).strip()[:1200]
    if not text:
        return jsonify({"error": "Нет текста"}), 400
    who = str(data.get("who") or "")
    who = who if who in _TTS_STYLE else "judge"
    gender = "f" if str(data.get("gender") or "") == "f" else "m"
    lang = _court_lang(data.get("lang"))
    voice = _TTS_VOICES[(who, gender)]
    style = _TTS_STYLE[who] + (" Говори по-таджикски, с естественным таджикским произношением." if lang == "tg"
                               else " Говори по-русски.")
    key = hashlib.sha1(json.dumps([TTS_MODEL, voice, style, text], ensure_ascii=False).encode("utf-8")).hexdigest()
    name = key + ".mp3"
    if os.path.isfile(os.path.join(_TTS_DIR, name)):
        return send_from_directory(os.path.abspath(_TTS_DIR), name, mimetype="audio/mpeg", max_age=86400)
    body = {"model": TTS_MODEL, "voice": voice, "input": text, "response_format": "mp3"}
    if TTS_MODEL.startswith("gpt-"):
        body["instructions"] = style
    try:
        r = requests.post(OPENAI_BASE_URL + "/audio/speech", headers=_openai_headers(), json=body, timeout=60)
    except Exception as e:  # noqa: BLE001
        print("tts: %s" % e)
        return jsonify({"error": "Сервис голоса недоступен"}), 503
    if r.status_code != 200 or not r.headers.get("content-type", "").startswith("audio"):
        print("tts: HTTP %s %s" % (r.status_code, r.text[:200]))
        return jsonify({"error": "Сервис голоса вернул ошибку"}), 502
    try:
        os.makedirs(_TTS_DIR, exist_ok=True)
        with open(os.path.join(_TTS_DIR, name), "wb") as fh:
            fh.write(r.content)
    except OSError as e:
        print("tts cache: %s" % e)
    return Response(r.content, mimetype="audio/mpeg", headers={"Cache-Control": "max-age=86400"})


@app.route("/api/court/cases")
def court_cases():
    """Учебные дела; для таджикского интерфейса карточки переводятся один раз и кешируются."""
    lang = _court_lang(request.args.get("lang"))
    cards = []
    for cid in moot_court.case_ids():
        translated = None
        if lang == "tg":
            if cid not in _court_cards:
                case = moot_court.get_case(cid)
                values = _court_from_ru([case[f] for f in moot_court.CARD_FIELDS], "tg")
                _court_cards[cid] = dict(zip(moot_court.CARD_FIELDS, values))
            translated = _court_cards[cid]
        cards.append(moot_court.public_case(cid, translated))
    return jsonify({"cases": cards, "voice": "openai" if _tts_enabled() else "browser"})


def _court_rag(case, message):
    """Статьи по делу и по последней реплике: единственный источник ссылок для судьи."""
    index = load_chat_rag_index(force=False)
    if not index.get("ready"):
        return [], ""
    results = retrieve_rag(case["query"] + " " + message, top_k=7, index=index)
    return results, format_rag_context(results, max_chars=6500)


@app.route("/api/court/turn", methods=["POST"])
def court_turn():
    """Один ход заседания: реплика пользователя -> ответ судьи, противной стороны или свидетеля и оценка."""
    data = request.get_json(silent=True) or {}
    case = moot_court.get_case(data.get("case_id"))
    message = str(data.get("message") or "").strip()[:moot_court.MAX_TEXT]
    if not case:
        return jsonify({"error": "Дело не найдено"}), 404
    if len(message) < 3:
        return jsonify({"error": "Введите реплику"}), 400
    lang = _court_lang(data.get("lang"))
    history = moot_court.sanitize_history(data.get("history"))
    action = moot_court.normalize_action(data.get("action"))
    turn_no = sum(1 for h in history if h["speaker"] == "user") + 1

    message_ru = _court_to_ru(message, lang)
    results, context = _court_rag(case, message_ru)
    try:
        parsed = moot_court.parse_turn(_court_json(
            moot_court.build_turn_prompt(case, history, message_ru, action, context, turn_no), 900))
    except Exception as e:  # noqa: BLE001
        print("court turn: %s" % e)
        return jsonify({"error": "Модель недоступна, повторите попытку"}), 503
    if not parsed:
        return jsonify({"error": "Суд не смог ответить, повторите реплику"}), 502

    citations = _chat_citations(message_ru, results)
    score = moot_court.turn_score(parsed["parts"], citations)
    texts = [line["text"] for line in parsed["lines"]] + [parsed["comment"], parsed["hint"]]
    shown = _court_from_ru(texts, lang)
    lines = [{"speaker": line["speaker"], "text": shown[i]} for i, line in enumerate(parsed["lines"])]
    count = len(parsed["lines"])
    return jsonify({
        "lines": lines,
        "score": score,
        "parts": parsed["parts"],
        "comment": shown[count],
        "hint": shown[count + 1],
        "citations": citations,
        "turn": turn_no,
        "max_turns": moot_court.MAX_TURNS,
    })


@app.route("/api/court/verdict", methods=["POST"])
def court_verdict():
    """Решение суда и итоговая оценка работы пользователя."""
    data = request.get_json(silent=True) or {}
    case = moot_court.get_case(data.get("case_id"))
    if not case:
        return jsonify({"error": "Дело не найдено"}), 404
    lang = _court_lang(data.get("lang"))
    history = moot_court.sanitize_history(data.get("history"))
    if not any(h["speaker"] == "user" for h in history):
        return jsonify({"error": "Заседание не началось"}), 400
    scores = [v for v in (data.get("scores") or []) if isinstance(v, (int, float))][-moot_court.MAX_TURNS:]
    cited = [str(c)[:60] for c in (data.get("cited") or [])[:8]]
    mean = round(sum(scores) / len(scores)) if scores else 0
    try:
        parsed = moot_court.parse_verdict(_court_json(moot_court.build_verdict_prompt(case, history, mean, cited), 1100))
    except Exception as e:  # noqa: BLE001
        print("court verdict: %s" % e)
        return jsonify({"error": "Модель недоступна, повторите попытку"}), 503
    if not parsed:
        return jsonify({"error": "Суд не смог вынести решение, повторите"}), 502

    score = moot_court.final_score(scores, parsed["overall"])
    # Статьи для изучения берутся из тех, что найдены по самому делу: свободный
    # поиск по темам от модели выдавал статьи из чужих кодексов.
    articles, seen = [], set()
    for item in _court_rag(case, "")[0]:
        key = (item.get("code_short"), item.get("article"))
        if not item.get("article") or key in seen:
            continue
        seen.add(key)
        articles.append({
            "code": item.get("code_short"), "article": item.get("article"), "title": item.get("article_title") or "",
            "code_id": codex_catalog.code_id_for(item.get("source") or "")[0],
        })
    articles = moot_court.relevant_titles(case["query"], articles, limit=4) or articles[:2]
    texts = [parsed["verdict"]] + parsed["strengths"] + parsed["mistakes"] + parsed["study"]
    shown = _court_from_ru(texts, lang)
    n_s, n_m = len(parsed["strengths"]), len(parsed["mistakes"])
    return jsonify({
        "verdict": shown[0],
        "outcome": parsed["outcome"],
        "score": score,
        "grade": moot_court.grade(score),
        "breakdown": parsed["breakdown"],
        "strengths": shown[1:1 + n_s],
        "mistakes": shown[1 + n_s:1 + n_s + n_m],
        "study": shown[1 + n_s + n_m:],
        "articles": articles,
    })


@app.route("/api/graph/article")
def graph_article():
    """Связи одной статьи: на что она ссылается и кто ссылается на неё."""
    graph = _graph()
    if graph is None:
        return _codex_unavailable()
    links = graph.article_links(request.args.get("code", ""), request.args.get("n", ""))
    if links is None:
        return jsonify({"error": "Статья не найдена"}), 404
    return jsonify(links)


@app.route("/api/graph/subgraph", methods=["POST"])
def graph_subgraph():
    """Граф вокруг выбранных статей (например, тех, на которые опирается ответ чата)."""
    graph = _graph()
    if graph is None:
        return _codex_unavailable()
    data = request.get_json(silent=True) or {}
    focus = []
    for item in (data.get("nodes") or [])[:12]:
        if isinstance(item, dict):
            focus.append((str(item.get("code_id") or ""), str(item.get("article") or "")))
    try:
        limit = max(4, min(60, int(data.get("limit") or 32)))
    except (TypeError, ValueError):
        limit = 32
    result = graph.subgraph(focus, limit=limit)
    if not result["nodes"]:
        return jsonify({"error": "Статьи не найдены в базе", "nodes": [], "edges": []}), 404
    return jsonify(result)


@app.route("/api/codex/article")
def codex_article():
    """Текст одной статьи, собранный из фрагментов индекса, и соседние статьи."""
    catalog = _codex()
    if catalog is None:
        return _codex_unavailable()
    article = catalog.article(request.args.get("code", ""), request.args.get("n", ""))
    if article is None:
        return jsonify({"error": "Статья не найдена"}), 404
    return jsonify(article)


@app.route("/api/codex/search")
def codex_search():
    """Поиск по всем кодексам или по одному (?code=): номер статьи, название, полный текст."""
    catalog = _codex()
    if catalog is None:
        return _codex_unavailable()
    index = catalog.index
    try:
        limit = int(request.args.get("limit") or 30)
    except ValueError:
        limit = 30
    query = request.args.get("q", "")
    # Индекс русский: таджикский запрос переводится до поиска, а страница показывает, по чему искали.
    query_ru = _crime_text_to_russian(query) if query.strip() else query
    def text_search(text, n):
        """Слова из запроса + смысловой поиск: «испытательный срок» не встречается в названии статьи об
        испытании при приёме на работу, и чистый BM25 находил только УИК."""
        base = list(legal_rag.lexical_search(index, text, limit=n))
        try:
            extra = retrieve_rag(text, top_k=min(n, 12), index=index)
        except Exception as error:  # noqa: BLE001
            print("  [CODEX] смысловой поиск недоступен (%s)" % type(error).__name__)
            extra = []
        best = max((score for score, _chunk in base), default=1.0)
        # Статью, которую смысловой поиск поставил в первую десятку, фильтр слабых совпадений отбрасывать не должен.
        picked = {chunk.get("id") for chunk in extra[:10]}
        seen = {chunk.get("id") for _score, chunk in base}
        return ([(best if chunk.get("id") in picked else score, chunk) for score, chunk in base]
                + [(best, chunk) for chunk in extra if chunk.get("id") not in seen])

    payload = catalog.search(
        query_ru,
        text_search,
        code_id=request.args.get("code") or None,
        limit=limit,
    )
    payload["query"] = query
    if query_ru != query:
        payload["query_ru"] = query_ru
    return jsonify(payload)


def _crime_translate_answer(text):
    """Ответ квалификации на таджикском; при сбое перевода возвращается русский оригинал."""
    try:
        out = _analysis_llm(CRIME_TG_RULE + text + "\n\nПЕРЕВОД:", max(1200, len(text) * 2))
        out = (out or "").strip()
        return out if len(out) >= len(text) * 0.4 else text
    except Exception as error:  # noqa: BLE001
        print("  [CRIME] перевод ответа не удался (%s)" % type(error).__name__)
        return text


def _crime_text_to_russian(text):
    """Описание на таджикском переводится на русский до поиска.

    Индекс УК и промпт русскоязычные: таджикский текст BM25 не находит ничего.
    Перевод машинный (NLLB, как в чате), поэтому при его недоступности или
    сбое остаётся исходный текст — поиск просто ответит «норма не найдена».
    """
    try:
        if lang_service.detect_lang(text, default="ru") != "tg":
            return text
        # NLLB на бытовых описаниях искажает смысл («бо мушт» — «ножом»,
        # «ҳамён» — «нос»), а от этого зависит статья. Облачная модель переводит
        # с таджикского заметно точнее, поэтому она первая, NLLB — запасной.
        if LLM_PROVIDER == "openai":
            translated = _crime_llm_translate_to_russian(text)
            if translated:
                return translated
        if not lang_service.translator.available():
            return text
        translated = lang_service.translator.translate_text(text, "tg", "ru")
        return (translated or "").strip() or text
    except Exception as error:  # noqa: BLE001
        print("  [CRIME] перевод описания не удался (%s)" % type(error).__name__)
        return text


CRIME_CLARIFY_ENABLED = os.environ.get("CRIME_CLARIFY", "1").strip().lower() not in {"0", "false", "no", "off"}
CRIME_CLARIFY_MAX_QUESTIONS = 3


def _crime_clarify_prompt(description):
    return (
        "Ты юрист-криминалист Республики Таджикистан. Перед квалификацией деяния по УК РТ проверь, "
        "хватает ли в описании фактов, от которых зависит выбор статьи, части и пункта.\n"
        "Такие факты: размер ущерба; применялось ли насилие или оружие; наступили ли смерть или вред "
        "здоровью и какой; проникновение в жилище; возраст потерпевшего или виновного; должность "
        "виновного; повторность и группа лиц; состояние опьянения; способ (обман, тайно, открыто).\n"
        "Правила:\n"
        "- Спрашивай ТОЛЬКО о том, чего нет в описании и без чего часть или пункт статьи не определить.\n"
        "- Не больше 3 вопросов, каждый короткий, на русском, один факт на вопрос.\n"
        "- Если описание уже достаточно подробное или это не описание деяния — вопросов не нужно.\n"
        'Ответ строго JSON: {"enough": true|false, "questions": ["...", "..."]}. '
        'Если enough=true, questions пустой.\n\n'
        "ОПИСАНИЕ: " + description + "\nJSON:"
    )


def _crime_clarify_questions(description):
    """Список уточняющих вопросов; пустой — фактов хватает или проверка не удалась.

    Сбой модели никогда не блокирует квалификацию: лучше ответить по тому, что
    есть, чем остановить человека на лишнем шаге.
    """
    if not CRIME_CLARIFY_ENABLED or len(description.split()) < 3:
        return []
    try:
        raw = llm_once(CRIME_MODEL, _crime_clarify_prompt(description[:1500]), 300,
                       temperature=0.0, keep_alive=CRIME_KEEP_ALIVE, timeout=40,
                       json_mode=True, openai_model=CRIME_GPT_MODEL)
        payload = json.loads(raw)
    except Exception as error:  # noqa: BLE001
        print("  [CRIME] уточняющие вопросы не получены (%s)" % type(error).__name__)
        return []
    if not isinstance(payload, dict) or payload.get("enough") is not False:
        return []
    questions = []
    for item in payload.get("questions") or []:
        text = re.sub(r"\s+", " ", str(item or "")).strip()
        if 5 <= len(text) <= 200 and text not in questions:
            questions.append(text)
    return questions[:CRIME_CLARIFY_MAX_QUESTIONS]


@app.route("/api/crime/clarify", methods=["POST"])
def crime_clarify():
    """Чего не хватает описанию для точной квалификации (до 3 вопросов)."""
    data = request.get_json(silent=True) or {}
    description = _crime_description(data)
    if not description:
        return jsonify({"error": "crime description is required"}), 400
    questions = _crime_clarify_questions(description)
    if questions and _crime_source_lang(data) == "tg":
        try:
            questions = _tajik_cards(questions) or questions
        except Exception as error:  # noqa: BLE001
            print("  [CRIME] перевод вопросов не удался (%s)" % type(error).__name__)
    return jsonify({"enough": not questions, "questions": questions, "lang": _crime_source_lang(data)})


def _crime_not_found_payload(query, results, status_key="qualification", lang="ru"):
    """Ответ, когда опоры в УК РТ нет. Модель в этом случае не вызывается:
    сгенерированный без нормы текст и есть та самая выдумка, ради запрета
    которой страница переведена на RAG."""
    return {
        status_key: CRIME_NOT_FOUND_MESSAGE_TG if lang == "tg" else CRIME_NOT_FOUND_MESSAGE,
        "lang": lang,
        "grounded": False,
        "query": query,
        "articles": [],
        # В УК нормы нет, но область права могла быть названа: смежные нормы
        # (например, Водного кодекса) полезнее пустого «не найдено».
        "related_norms": retrieve_related_norms(query),
        "rag_sources": [crime_article_payload(item) for item in results],
        "article_numbers": [],
        "sources": [],
        "primary_source": None,
        "crime_rag": crime_rag_status(),
    }


def _allowed_article_numbers(results):
    """Номера статей, на которые модели разрешено ссылаться — по фрагментам
    RAG, реально выданным ей для этого ответа. Общая для квалификации по УК
    РТ и чата: обе строят промпт из top-k найденных статей и обе должны
    проверить после генерации, что модель сослалась только на них.
    """
    numbers = []
    for item in results:
        number = str(item.get("article") or "").strip()
        if number and number not in numbers:
            numbers.append(number)
    return numbers


# Общий _cited_article_numbers ловит и «ч. 2» — для сверки ссылок это ложные
# срабатывания: часть статьи не статья, и ответ с «ч. 2 ст. 248» помечался бы
# как ссылка на несуществующую статью 2.
_ARTICLE_REF_RE = re.compile(
    r"(?:стать[а-яё]*|ст\.)\s*(\d+(?:[-.]\d+)?)", re.IGNORECASE
)


def _unverified_article_numbers(text, allowed):
    """Номера статей из ответа, которых не было в найденных фрагментах."""
    allowed_set = {str(number) for number in allowed}
    cited = {match.group(1) for match in _ARTICLE_REF_RE.finditer(text or "")}
    return sorted(cited - allowed_set)


def _chat_citations(answer_text, rag_results):
    """Статьи из ответа со статусом проверки и ссылкой на страницу кодексов.

    Сверка идёт и с найденными фрагментами, и с полным каталогом кодекса: так
    «статья есть, но к ответу её не подбирали» отличается от выдуманной.
    """
    catalog = _codex()
    ids = {}
    if catalog is not None:
        ids = {data["short"]: cid for cid, data in catalog.codes.items()}

    def lookup(code_short, article):
        cid = ids.get(code_short)
        info = catalog.article(cid, article) if cid else None
        return {"title": info["title"], "source": ""} if info else None

    report = chat_modes.citation_report(answer_text, rag_results, lookup if catalog else None)
    for entry in report:
        cid = ids.get(entry["code"]) or (codex_catalog.code_id_for(entry["source"])[0] if entry["source"] else "")
        entry["code_id"] = cid
        entry.pop("source", None)
    return report


# Ссылки вида «Закон РТ от 17.05.2004г. №35» — законная часть текста кодексов
# (каждая правка статьи сопровождается «в редакции Закона РТ от ...»), так что
# сам паттерн не подозрителен. Подозрительна конкретная дата+номер, которых не
# было в переданном модели RAG-контексте: наблюдался случай, когда модель по
# вопросу о налоговой декларации сослалась на «Закон РТ от 11.02.2025 №2143» —
# ни даты, ни номера не было ни в одном найденном фрагменте, то есть модель
# правдоподобно выдумала конкретную ссылку вместо того, чтобы честно сказать,
# что нормы под рукой нет (см. КАЛИБРОВКА в промпте чата).
_LAW_REF_RE = re.compile(
    r"закон[а-яё]*\s+(?:РТ|Республики\s+Таджикистан)\s+от\s+"
    r"(\d{1,2}[.\s]\d{1,2}[.\s]\d{2,4})\s*г?\.?\s*(?:№|N|No)\.?\s*(\d+)",
    re.IGNORECASE,
)


def _unverified_law_references(text, rag_context):
    """Ссылки на законы о внесении изменений, которых нет дословно в RAG-контексте."""
    context_digits = re.sub(r"\D", "", rag_context or "")
    unverified = []
    for match in _LAW_REF_RE.finditer(text or ""):
        date_digits = re.sub(r"\D", "", match.group(1))
        number = match.group(2)
        if not date_digits or not number:
            continue
        if date_digits in context_digits and number in context_digits:
            continue
        unverified.append(match.group(0).strip())
    return unverified


@app.route("/api/crime/catalog")
def crime_catalog():
    """Перечень статей УК РТ прямо из индекса — база знаний на странице.

    Раньше этот список был зашит в шаблон и разошёлся с кодексом: там
    значилось «247 — Кража», тогда как в УК РТ кража это статья 244,
    а 247 — мошенничество.
    """
    index = load_crime_rag_index()
    if not index.get("ready"):
        return jsonify({
            "error": index.get("error") or "Уголовный кодекс РТ не проиндексирован",
            "articles": [],
            "crime_rag": crime_rag_status(),
        }), 503

    articles = []
    seen = set()
    for chunk in index.get("chunks", []):
        number = str(chunk.get("article") or "").strip()
        if not number or number in seen:
            continue
        seen.add(number)
        articles.append({
            "article": number,
            "article_title": chunk.get("article_title") or chunk.get("title"),
            "chapter": chunk.get("chapter"),
            "chapter_title": chunk.get("chapter_title"),
            "citation": chunk.get("citation") or legal_rag.citation_for(chunk),
            "snippet": _shorten_text(chunk.get("text") or "", 240),
        })
    return jsonify({
        "articles": articles,
        "count": len(articles),
        "crime_rag": crime_rag_status(),
    })


@app.route("/api/crime/search", methods=["POST"])
def crime_search():
    """Чистый поиск по УК РТ без языковой модели: что нашлось — то и показано."""
    data = request.get_json(silent=True) or {}
    src_lang = _crime_source_lang(data)
    description = _crime_description(data)
    if not description:
        return jsonify({"error": "crime description is required"}), 400

    top_k = int(data.get("top_k") or CRIME_RAG_TOP_K)
    results, index = retrieve_crime_rag(description, top_k=top_k)
    if not index.get("ready"):
        return jsonify({
            "error": index.get("error") or "Уголовный кодекс РТ не проиндексирован",
            "grounded": False,
            "crime_rag": crime_rag_status(),
        }), 503

    grounded = grounded_crime_results(results)
    if not grounded:
        return jsonify(_crime_not_found_payload(description, results, status_key="message", lang=src_lang))

    return jsonify({
        "grounded": True,
        "lang": src_lang,
        "query": description,
        "articles": [crime_article_payload(item) for item in grounded],
        "article_numbers": _allowed_article_numbers(grounded),
        "related_norms": retrieve_related_norms(description),
        "crime_rag": crime_rag_status(),
    })


CRIME_PROCEDURE_SOURCE = "УГОЛОВНО - ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx"
CRIME_PROCEDURE_TOP = int(os.environ.get("CRIME_PROCEDURE_TOP", "3"))
CRIME_PROCEDURE_MIN_SCORE = float(os.environ.get("CRIME_PROCEDURE_MIN_SCORE", "12.0"))
CRIME_PROCEDURE_MAX_CHARS = int(os.environ.get("CRIME_PROCEDURE_MAX_CHARS", "5000" if LLM_PROVIDER == "openai" else "2400"))
# Слова, которые поднимают процессуальные статьи (заявление о преступлении,
# права потерпевшего, доказательства), а не статьи о самом составе: «что делать»
# человеку нужно из УПК, а не из УК. Без них поиск по описанию события
# возвращал бы те же нормы, что и квалификация.
CRIME_PROCEDURE_QUERY_SUFFIX = " заявление о преступлении потерпевший права доказательства возбуждение уголовного дела"


def retrieve_crime_procedure(description):
    """Статьи УПК РТ для блока «Что делать»: заявление, права потерпевшего, доказательства.

    Возвращает до CRIME_PROCEDURE_TOP разных статей. Пусто — индекса УПК нет
    или ничего достаточно близкого: тогда совет строится только из общих шагов
    с пометкой, а не из выдуманных ссылок.
    """
    try:
        index = load_related_rag_index(CRIME_PROCEDURE_SOURCE)
        if not index.get("ready"):
            return []
        results = legal_rag.search(index, (description or "")[:400] + CRIME_PROCEDURE_QUERY_SUFFIX, top_k=10)
    except Exception as error:  # noqa: BLE001
        print("  [CRIME] поиск по УПК не удался (%s)" % type(error).__name__)
        return []
    picked, seen = [], set()
    for item in results:
        article = item.get("article")
        if not article or article in seen:
            continue
        if float(item.get("lexical_score") or 0) < CRIME_PROCEDURE_MIN_SCORE:
            continue
        seen.add(article)
        picked.append(item)
        if len(picked) >= CRIME_PROCEDURE_TOP:
            break
    return picked


def _crime_todo_rules(procedure_numbers, detailed):
    """Правила блока «Что делать» — общие для краткого и полного режимов."""
    if procedure_numbers:
        source_rule = (
            "- Шаги, которые опираются на приведённые ниже статьи УПК РТ, сопровождай ссылкой "
            "«(ст. N УПК РТ)». Разрешены только эти статьи УПК: " + ", ".join(procedure_numbers) + "\n"
            "- Шаги вне этих статей (сохранить доказательства, обратиться за медицинской помощью, к адвокату) "
            "помечай «(общий совет)»"
        )
    else:
        source_rule = (
            "- Статей УПК под рукой нет: давай только общие шаги, каждый с пометкой «(общий совет)», "
            "без номеров статей"
        )
    size = ("6-8 пронумерованных шагов, у каждого одна-две фразы: что сделать, куда и зачем"
            if detailed else "3-5 коротких пронумерованных шагов, у каждого одна фраза")
    return (
        source_rule + "\n"
        "- Пиши для пострадавшего или свидетеля; если описание от лица самого нарушителя, дай нейтральные "
        "шаги: явка, помощь адвоката, последствия\n"
        "- Не выдумывай телефоны, сроки, названия органов и размеры выплат, которых нет в приведённых статьях\n"
        "- Объём: " + size
    )


_RU_THOUSANDS = {"одну": 1, "один": 1, "две": 2, "два": 2, "три": 3, "четыре": 4, "пять": 5, "шесть": 6, "семь": 7,
                 "восемь": 8, "девять": 9, "десять": 10}


def _crime_size_calculation(note_text, description):
    """Готовый расчёт порогов из примечания к главе и суммы из описания.

    Модель путалась в арифметике: 400 000 сомони при показателе 78 — это 5128 показателей, то есть особо крупный размер
    (порог 2000), а она называла крупный. Пороги берутся из текста примечания («в одну тысячу раз», «в две тысячи раз»),
    показатель — из CRIME_CALC_INDICATOR; считаем кодом, модели отдаём результат.
    """
    if not CRIME_CALC_INDICATOR:
        return ""
    try:
        indicator = float(CRIME_CALC_INDICATOR.replace(",", "."))
    except ValueError:
        return ""
    multiples = []
    for m in re.finditer(r"в\s+([а-яё]+)\s+тысяч\w*\s+раз", note_text.lower()):
        value = _RU_THOUSANDS.get(m.group(1))
        if value:
            multiples.append(value * 1000)
    amounts = []
    for m in re.finditer(r"(\d{1,3}(?:[ \u00a0]\d{3})+|\d{4,})\s*(?:сомони|сомон|сом\b)", description or ""):
        amounts.append(int(re.sub(r"\D", "", m.group(1))))
    if len(multiples) < 2 or not amounts:
        return ""
    big, huge = multiples[0], multiples[1]
    lines = ["РАСЧЁТ РАЗМЕРА (сделан кодом, используй его без пересчёта; показатель для расчётов %s сомони): "
             "крупный размер — от %d показателей (%s сомони), особо крупный — от %d показателей (%s сомони)." % (
                 CRIME_CALC_INDICATOR, big, f"{big * indicator:,.0f}".replace(",", " "), huge, f"{huge * indicator:,.0f}".replace(",", " "))]
    for amount in amounts[:3]:
        units = amount / indicator
        kind = "особо крупный размер" if units >= huge else ("крупный размер" if units >= big else "ниже порога крупного размера")
        lines.append("Сумма %s сомони = %.0f показателей — %s." % (f"{amount:,}".replace(",", " "), units, kind))
    return "\n".join(lines)


CRIME_CHAPTER_NOTE_MAX = 1200


def _crime_chapter_notes(rag_results, description=""):
    """Примечания к главе УК, которые задают размеры («крупный», «особо крупный», «значительный ущерб»).

    Такое примечание стоит под первой статьёй главы и действует на всю главу («в статьях настоящей главы»),
    но поиск не приносит его вместе с нужной статьёй: для присвоения на 400 000 сомони модель видела ст. 245
    без порога и выбирала часть 1. Возвращает (текст блока, номера статей, в которых лежат примечания).
    """
    index = load_crime_rag_index()
    chunks = index.get("chunks") or []
    seen, blocks, article_numbers = set(), [], []
    wanted = {(r.get("chapter"), r.get("source")) for r in rag_results if r.get("chapter")}
    for chunk in chunks:
        if (chunk.get("chapter"), chunk.get("source")) not in wanted:
            continue
        text = re.sub(r"\s+", " ", chunk.get("text") or "")   # жёсткие переносы строк старых .doc рвут фразы
        if "настоящей главы" not in text or "показател" not in text:
            continue
        start = text.find("Примечание")
        if start < 0:
            continue
        key = (chunk.get("chapter"), chunk.get("article"))
        if key in seen:
            continue
        seen.add(key)
        note = re.sub(r"\s+", " ", text[start:start + CRIME_CHAPTER_NOTE_MAX]).strip()
        blocks.append("[Примечание к главе %s, лежит под статьёй %s УК РТ] %s" % (
            chunk.get("chapter"), chunk.get("article"), note))
        article_numbers.append(str(chunk.get("article")))
    if not blocks:
        return "", []
    computed = _crime_size_calculation(" ".join(blocks), description)
    if computed:
        blocks.append(computed)
    header = ("\n\nПРИМЕЧАНИЯ К ГЛАВЕ (они определяют значительный, крупный и особо крупный размер для статей этой главы — "
              "применяй их к сумме из описания, ссылаться можно как «примечание к статье N»):\n")
    return header + "\n".join(blocks), article_numbers


def _crime_procedure_block(procedure):
    if not procedure:
        return ""
    return ("\n\nСТАТЬИ УПК РТ ДЛЯ СОВЕТА «ЧТО ДЕЛАТЬ» (допустимый источник для шагов):\n"
            + format_rag_context(procedure, max_chars=CRIME_PROCEDURE_MAX_CHARS))


def _crime_qualify_prompt(description, rag_results, allowed, procedure=None, procedure_numbers=None, notes=""):
    """Промпт полного разбора. Ответ обязан целиком помещаться в границы выдачи поиска."""
    rag_context = format_rag_context(rag_results, max_chars=CRIME_RAG_MAX_CHARS)
    allowed_line = ", ".join(allowed)
    todo_rules = _crime_todo_rules(procedure_numbers or [], detailed=True)
    calc_rule = (
        "Действующий показатель для расчётов: %s сомони. Посчитай пороги явно (кратность × показатель) и примени нужную часть."
        % CRIME_CALC_INDICATOR if CRIME_CALC_INDICATOR else
        "Значение показателя в справке не дано, не выдумывай его. В «Не хватает фактов» напиши, "
        "с каким порогом (в показателях для расчётов) надо сверить сумму, и укажи часть статьи, "
        "которая применяется, если порог превышен.")
    return f"""Ты — юрист по уголовному праву Республики Таджикистан.

ЗАДАЧА: подробно квалифицировать деяние по УК РТ, опираясь ТОЛЬКО на приведённые ниже статьи, и подсказать пострадавшему, что делать.

ОПИСАНИЕ ДЕЯНИЯ:
{description}

СТАТЬИ УК РТ, НАЙДЕННЫЕ ПОИСКОМ (единственный допустимый источник для квалификации):
{rag_context}{notes}{_crime_procedure_block(procedure)}

ЖЁСТКИЕ ПРАВИЛА (нарушение любого делает ответ негодным):
- Для квалификации разрешено ссылаться только на статьи УК РТ: {allowed_line}
- Ни номеров, ни санкций, ни формулировок вне приведённых статей приводить нельзя
- Сроки и виды наказания бери дословно из текста статьи выше; если санкция
  во фрагменте не указана — так и напиши, не восстанавливай её по памяти
- Только право Республики Таджикистан: упоминания РФ и её кодексов запрещены
- Пиши сразу ответ, без рассуждений о том, как ты его строишь
- Фразу «Для полной квалификации найденных норм УК РТ недостаточно» пиши
  ТОЛЬКО если ни одна из приведённых статей не подходит к деянию и основную
  статью назвать нельзя. Если основная статья названа, эту фразу не пиши:
  недостающие детали (часть, пункт, размер ущерба) укажи в «ФАКТЫ» одной
  строкой «Не хватает фактов: ...»
- Выбирай ЧАСТЬ и ПУНКТ статьи по всем названным в описании признакам: способ,
  размер, группа лиц, место, последствия, должностное положение. Берёшь часть 1,
  только если ни один квалифицирующий признак из частей 2–4 не подтверждён описанием.
- Размер ущерба: если в «Примечании» к статье значительный, крупный или особо крупный размер
  определён кратно показателю для расчётов, сопоставь с ним сумму из описания и назови порог.
  {calc_rule}
Правила раздела «ЧТО ДЕЛАТЬ»:
{todo_rules}

СТРУКТУРА ОТВЕТА (все семь разделов обязательны, названия не менять):

ЧТО ДЕЛАТЬ ПОСТРАДАВШЕМУ
   1. [шаг]
   2. [шаг]

1. КВАЛИФИКАЦИЯ
   • Основная статья УК РТ: [номер, часть, пункт и название из списка]
   • Дополнительные статьи (совокупность): [из списка или «нет»]

2. САНКЦИИ ПО ТЕКСТУ СТАТЬИ
   • Основное наказание: [дословно из приведённого фрагмента]
   • Дополнительные наказания: [если указаны во фрагменте]

3. ФАКТЫ И ДОКАЗАТЕЛЬСТВА
   • Элемент состава: [элемент] — цитата из описания: [фрагмент описания]

4. ОБСТОЯТЕЛЬСТВА
   • Отягчающие: [только названные в тексте приведённых статей]
   • Смягчающие: [то же правило]

5. ПРАВОВОЙ АНАЛИЗ
   [2-3 абзаца строго по тексту приведённых статей: почему именно эта статья, часть и пункт]

6. ИТОГОВЫЙ ВЫВОД
   • Квалификация: [статья и часть]
   • Наказание по статье: [диапазон из текста статьи]"""


def _crime_qualify_prompt_short(description, rag_results, allowed, procedure=None, procedure_numbers=None, notes=""):
    """Краткий режим: что делать, что грозит нарушителю и какие законы нарушены.

    Заголовки разделов разбирает страница (ЧТО ДЕЛАТЬ / ЧТО ГРОЗИТ / КАКИЕ ЗАКОНЫ),
    поэтому менять их нельзя.
    """
    rag_context = format_rag_context(rag_results, max_chars=CRIME_RAG_MAX_CHARS)
    allowed_line = ", ".join(allowed)
    todo_rules = _crime_todo_rules(procedure_numbers or [], detailed=False)
    return f"""Ты — юрист по уголовному праву Республики Таджикистан.

ЗАДАЧА: коротко и понятно для человека без юридического образования ответить на три вопроса: что делать в такой ситуации, что грозит нарушителю и какие статьи УК РТ нарушены. Опирайся ТОЛЬКО на приведённые ниже статьи.

ОПИСАНИЕ ДЕЯНИЯ:
{description}

СТАТЬИ УК РТ, НАЙДЕННЫЕ ПОИСКОМ (единственный допустимый источник для квалификации):
{rag_context}{notes}{_crime_procedure_block(procedure)}

ЖЁСТКИЕ ПРАВИЛА:
- Для квалификации разрешено ссылаться только на статьи УК РТ: {allowed_line}
- Санкцию бери дословно из текста статьи; если во фрагменте её нет — так и напиши
- Только право Республики Таджикистан: упоминания РФ и её кодексов запрещены
- Пиши сразу ответ, без рассуждений о том, как ты его строишь
- Выбери наиболее вероятную статью из приведённых, даже если часть фактов неизвестна:
  назови её с пометкой «предварительно» и укажи, чего не хватает
- «Установить нельзя» пиши ТОЛЬКО если ни одна из приведённых статей по смыслу
  не относится к деянию
Правила раздела «ЧТО ДЕЛАТЬ»:
{todo_rules}

СТРУКТУРА ОТВЕТА (строго три раздела, названия не менять, всего не больше 220 слов):

1. ЧТО ДЕЛАТЬ ПОСТРАДАВШЕМУ
   1. [шаг]
   2. [шаг]

2. ЧТО ГРОЗИТ НАРУШИТЕЛЮ
   • Наказание по статье: [дословно из приведённого фрагмента]
   • Отягчающие обстоятельства, которые могут усилить наказание: [если названы в статье, иначе «нет»]

3. КАКИЕ ЗАКОНЫ НАРУШЕНЫ
   • Основная статья УК РТ: [номер, часть, пункт и название из списка]
   • Дополнительные статьи: [из списка или «нет»]
   • Что именно нарушено: [одна фраза — какие признаки состава есть в описании]"""


def _crime_ollama_stream(prompt, max_tokens=None):
    """Токены модели по мере генерации.

    `think: false` здесь решает исход. Qwen3 по умолчанию сначала пишет
    рассуждения, и на CPU они уходили в минуты, за которые пользователь не
    видел ни одного символа: на замере разбор занимал 7,5 минут при видимом
    ответе в 350 токенов. Чат этот флаг уже ставит, `send_to_ollama` — нет.

    `keep_alive` держит модель загруженной: без него следующий запрос снова
    оплачивает чтение восьми гигабайт с диска. `num_ctx` урезан до нужного:
    модель объявляет 16k, а на CPU лишний контекст — это лишняя память и
    более медленный разбор промпта.
    """
    for obj in llm_stream(CRIME_MODEL, prompt, temperature=0.2, num_predict=max_tokens or CRIME_MAX_TOKENS,
                           top_p=0.9, think=False, keep_alive=CRIME_KEEP_ALIVE,
                           num_ctx=CRIME_NUM_CTX, timeout=1800,
                           openai_model=CRIME_GPT_MODEL):
        chunk = obj.get("response")
        if chunk:
            yield chunk


def _crime_stream_events(prompt, max_tokens=None):
    """Токены модели плюс сигналы жизни, пока она молчит.

    До первого токена проходят минуты: разбор промпта, а на холодном старте
    ещё и чтение восьми гигабайт модели с диска. Молчащее соединение за это
    время успевает оборваться на стороне клиента — проверка на живом API
    получила «terminated», не дождавшись ни символа. Поэтому генерация уходит
    в отдельный поток, а наружу каждые несколько секунд идёт признак жизни:
    он и держит соединение, и честно показывает, что модель ещё думает.
    """
    events = queue.Queue()

    def worker():
        try:
            for chunk in _crime_ollama_stream(prompt, max_tokens):
                events.put(("chunk", chunk))
        except LLMBackendError as error:
            events.put(("error", str(error)))
        except Exception as error:  # noqa: BLE001
            events.put(("error", f"Ошибка генерации ответа: {error}"))
        finally:
            events.put(("end", None))

    threading.Thread(target=worker, daemon=True).start()

    waited = 0.0
    while True:
        try:
            kind, value = events.get(timeout=CRIME_HEARTBEAT_SECONDS)
        except queue.Empty:
            waited += CRIME_HEARTBEAT_SECONDS
            yield ("wait", waited)
            continue
        if kind == "end":
            return
        yield (kind, value)


def _crime_unverified_note(unverified, lang="ru"):
    """Приписка о ссылках мимо выдачи поиска."""
    if not unverified:
        return ""
    if lang == "tg":
        return (
            "\n\n⚠️ RAG тасдиқ накардааст: моддаҳои " + ", ".join(unverified)
            + " дар порчаҳои ёфташудаи КҶ ҶТ вуҷуд надоранд ва санҷида намешаванд."
        )
    return (
        "\n\n⚠️ Не подтверждено RAG: статьи "
        + ", ".join(unverified)
        + " отсутствуют в найденных фрагментах УК РТ и проверке не подлежат."
    )


@app.route("/api/crime/qualify", methods=["POST"])
def crime_qualify():
    """Полная квалификация по УК РТ строго в границах найденных норм.

    По умолчанию отвечает потоком NDJSON: первая строка — опора ответа
    (статьи, ссылки, тексты), дальше токены, последняя — результат сверки
    ссылок. Ждать разбор целиком нельзя: на этом железе он идёт минуты, и
    страница пять минут показывала пустой спиннер, а затем обрывала запрос
    по таймауту. С `{"stream": false}` эндпоинт отвечает одним JSON.
    """
    data = request.get_json(silent=True) or {}
    src_lang = _crime_source_lang(data)
    description = _crime_description(data)
    if not description:
        return jsonify({"error": "crime description is required"}), 400

    results, index = retrieve_crime_rag(description)
    if not index.get("ready"):
        return jsonify({
            "error": index.get("error") or "Уголовный кодекс РТ не проиндексирован",
            "grounded": False,
            "crime_rag": crime_rag_status(),
        }), 503

    rag_results = grounded_crime_results(results)[:CRIME_QUALIFY_TOP_K]
    if not rag_results:
        return jsonify(_crime_not_found_payload(description, results, lang=src_lang))

    allowed = _allowed_article_numbers(rag_results)
    short = str(data.get("mode") or "").strip().lower() == "short"
    procedure = retrieve_crime_procedure(description)
    procedure_numbers = _allowed_article_numbers(procedure)
    notes, note_articles = _crime_chapter_notes(rag_results, description)
    prompt = (_crime_qualify_prompt_short if short else _crime_qualify_prompt)(
        description, rag_results, allowed, procedure, procedure_numbers, notes)
    # Ссылки в ответе проверяются по обоим кодексам: УК для квалификации, УПК для совета; примечание к главе можно цитировать.
    checkable = list(allowed) + [n for n in procedure_numbers if n not in allowed]
    checkable += [n for n in note_articles if n not in checkable]
    max_tokens = CRIME_MAX_TOKENS_SHORT if short else CRIME_MAX_TOKENS_FULL
    meta = {
        "grounded": True,
        "mode": "short" if short else "full",
        "query": description,
        "primary_source": RAG_CODE_LABELS.get(CRIME_RAG_SOURCE_NAME, CRIME_RAG_SOURCE_NAME),
        "sources": [{"name": CRIME_RAG_SOURCE_NAME,
                     "label": RAG_CODE_LABELS.get(CRIME_RAG_SOURCE_NAME, CRIME_RAG_SOURCE_NAME)}],
        "article_numbers": allowed,
        "articles": [crime_article_payload(item) for item in rag_results],
        "procedure": [crime_article_payload(item) for item in procedure],
        # Справочно, в квалификацию и в проверку ссылок не входят.
        "related_norms": retrieve_related_norms(description),
        "crime_rag": crime_rag_status(),
    }

    if data.get("stream") is False:
        try:
            text = normalize_legal_terms("".join(_crime_ollama_stream(prompt, max_tokens)))
        except LLMBackendError as error:
            text = str(error)
        except Exception as error:  # noqa: BLE001
            text = f"⚠️ Ошибка генерации ответа: {error}"
        unverified = _unverified_article_numbers(text, checkable)
        summary = _crime_summary(text, src_lang)
        if src_lang == "tg":
            text = _crime_translate_answer(text)
        return jsonify(dict(
            meta,
            qualification=text + _crime_unverified_note(unverified, src_lang),
            unverified_articles=unverified,
            summary=summary,
            lang=src_lang,
        ))

    def generate():
        yield json.dumps({"meta": dict(meta, lang=src_lang)}, ensure_ascii=False) + "\n"
        collected = []
        for kind, value in _crime_stream_events(prompt, max_tokens):
            if kind == "chunk":
                chunk = normalize_legal_terms(value)
                collected.append(chunk)
                # Таджикский ответ показывается целиком после перевода: русские токены на экране
                # сменились бы таджикским текстом, а это сбивает с толку.
                if src_lang != "tg":
                    yield json.dumps({"response": chunk}, ensure_ascii=False) + "\n"
            elif kind == "wait":
                # Признак жизни: держит соединение и показывает, что модель
                # ещё не выдала ни одного токена.
                yield json.dumps({"wait": round(value, 1)}, ensure_ascii=False) + "\n"
            else:
                yield json.dumps({"response": "\n\n⚠️ " + value},
                                 ensure_ascii=False) + "\n"

        answer_ru = "".join(collected)
        unverified = _unverified_article_numbers(answer_ru, checkable)
        summary = _crime_summary(answer_ru, src_lang)
        if src_lang == "tg":
            translated = _crime_translate_answer(answer_ru)
            yield json.dumps({"response": translated}, ensure_ascii=False) + "\n"
        yield json.dumps({
            "done": True,
            "unverified_articles": unverified,
            "note": _crime_unverified_note(unverified, src_lang),
            "summary": summary,
        }, ensure_ascii=False) + "\n"

    return Response(
        stream_with_context(generate()),
        mimetype="application/x-ndjson",
        headers={
            "Cache-Control": "no-cache, no-store, must-revalidate",
            "X-Accel-Buffering": "no",
            "Access-Control-Allow-Origin": "*",
        },
    )

# Перевод идёт не по токенам, а по завершённым предложениям: NLLB обучалась на
# предложениях и обрывок фразы переводит мусором. Ответ копится в буфере и
# уходит в чат кусками — иначе пользователь ждал бы конца генерации молча.
CHAT_TRANSLATE_FLUSH_CHARS = int(os.environ.get("CHAT_TRANSLATE_FLUSH_CHARS", "350"))
_SENTENCE_CUT_RE = re.compile(r"[.!?:;]\s")


def _translate_split_point(buffer):
    """Индекс, по которому буфер можно резать без потери смысла, либо None."""
    paragraph = buffer.find("\n\n")
    if paragraph != -1:
        return paragraph + 2
    if len(buffer) >= CHAT_TRANSLATE_FLUSH_CHARS:
        last = None
        for match in _SENTENCE_CUT_RE.finditer(buffer):
            last = match
        if last:
            return last.end()
    return None


CHAT_TG_RULES = (
    "Сохрани разметку Markdown (**жирный**, списки, нумерацию) и пустые строки. Строки вида «### ключ» (латиницей) "
    "и слова «КРИТЕРИЙ:» не меняй. Названия кодексов переводи принятыми формами: Гражданский кодекс Республики "
    "Таджикистан — Кодекси граждании Ҷумҳурии Тоҷикистон, Трудовой — Кодекси меҳнат, Семейный — Кодекси оила, "
    "Налоговый — Кодекси андоз, Уголовный — Кодекси ҷиноятӣ; «статья N» — «моддаи N», «часть» — «қисми», «пункт» — «банди». "
    "Цитаты в «ёлочках» из текста закона оставь дословно по-русски. "
)
# Пока модель пишет по-русски, а перевод идёт кусками, блоки укрупняются: на каждый вызов уходит
# секунда-две, и десяток коротких абзацев растянул бы ответ.
CHAT_TG_LLM_MIN_BLOCK = 900


def _llm_tajik_available():
    """Перевод на таджикский моделью: только при облачном провайдере (там модель сильная)."""
    return LLM_PROVIDER == "openai" and bool(OPENAI_API_KEY)


def _tg_block_via_llm(block):
    """Блок ответа на таджикском от модели; None — не вышло, тогда работает NLLB."""
    try:
        out = doc_lang.translate_text(block, "ru", "tg", _analysis_llm, kind="ответ юридического помощника",
                                      rules=CHAT_TG_RULES, workers=1)
        return out if len(out.strip()) >= len(block.strip()) * 0.35 else None
    except Exception as error:  # noqa: BLE001
        print("  [TG] перевод блока моделью не удался (%s)" % type(error).__name__)
        return None


def _tajik_translation_available():
    return _llm_tajik_available() or lang_service.translator.available()


def _questions_to_russian(texts):
    """Таджикские реплики пользователя на русский: моделью, а при её недоступности NLLB."""
    if _llm_tajik_available():
        try:
            out = doc_lang.translate_items(texts, "tg", "ru", _analysis_llm, kind="реплики пользователя юридического чата")
            if out and len(out) == len(texts):
                return out
        except Exception as error:  # noqa: BLE001
            print("  [TG] перевод вопроса моделью не удался (%s)" % type(error).__name__)
    return lang_service.translator.translate(texts, "tg", "ru")


# Таджикский ответ идёт как у ChatGPT: русский текст рождается потоком, и каждый готовый абзац сразу
# переводится моделью тоже потоком, токен за токеном. Раньше ждали весь русский ответ, переводили его целиком
# и отдавали одним куском — пользователь 15 секунд видел пустоту. Первый абзац уходит в перевод рано
# (чтобы текст появился быстро), следующие укрупняются: на каждый вызов перевода тратится около секунды.
CHAT_TG_FIRST_BLOCK = 120
CHAT_TG_NEXT_BLOCK = 260
CHAT_TG_MAX_BLOCK = 1400
_SENTENCE_END_RE = re.compile(r"[.!?;:][)»*]*\s+")


# Конец предложения, за которым видна заглавная буква: «ст. 214» посреди фразы так не режется.
_FIRST_CUT_RE = re.compile(r"(?:[.!?;:]\s+(?=[А-ЯЁA-Z*«\d])|[;,]\s+)")
CHAT_TG_FIRST_SENTENCE = 60


def _next_tg_block(buffer, first, final):
    """Следующий готовый к переводу блок русского текста: (блок, остаток, чем склеить со следующим) или None.

    Первый блок отдаётся уже по концу первого предложения: текст должен появиться как можно раньше.
    """
    need = CHAT_TG_FIRST_BLOCK if first else CHAT_TG_NEXT_BLOCK
    idx = buffer.find("\n\n", need)
    if first:
        cut = next((m for m in _FIRST_CUT_RE.finditer(buffer) if m.end() >= CHAT_TG_FIRST_SENTENCE), None)
        if cut and (idx == -1 or cut.end() < idx):
            return buffer[:cut.end()].rstrip(), buffer[cut.end():], " "
    if idx != -1:
        return buffer[:idx], buffer[idx + 2:], "\n\n"
    line = buffer.find("\n", need)   # пункты списка тоже границы: длинный список не ждёт конца целиком
    if line != -1:
        return buffer[:line], buffer[line + 1:], "\n"
    if len(buffer) > CHAT_TG_MAX_BLOCK:
        # длинный абзац без разрывов — режем по последнему концу предложения
        last = None
        for match in _SENTENCE_END_RE.finditer(buffer, 0, CHAT_TG_MAX_BLOCK):
            last = match
        if last:
            return buffer[:last.end()].rstrip(), buffer[last.end():], " "
        return buffer[:CHAT_TG_MAX_BLOCK], buffer[CHAT_TG_MAX_BLOCK:], " "
    if final and buffer.strip():
        return buffer, "", "\n\n"
    return None


# Метка «ПЕРЕВОД:» в конце блока: промпт заканчивается ею, и модель иногда повторяет её после текста.
_TG_TAIL_LABEL_RE = re.compile(r"\s*(?:ПЕРЕВОД|ТАРҶУМА|Перевод|Тарҷума)\s*:?\s*$")
CHAT_TG_HOLD = 16   # последние символы потока придерживаются, пока не станет ясно, что это не метка


def _stream_tg_block(block, separator):
    """Перевод одного блока потоком: отдаёт строки NDJSON с кусками таджикского текста по мере их появления."""
    raw, sent, started = "", 0, False
    prompt = doc_lang._prompt(block, "ru", "tg", "ответ юридического помощника", CHAT_TG_RULES)

    def _line(text):
        nonlocal sent
        return json.dumps({"response": (separator if sent == 0 else "") + text, "done": False},
                          ensure_ascii=False) + "\n"

    emitted = 0
    try:
        for obj in llm_stream(MODEL_NAME, prompt, temperature=0.0, num_predict=max(900, int(len(block) * 1.4)),
                              openai_model=ANALYSIS_GPT_MODEL, reasoning_effort="minimal"):
            piece = obj.get("response") or ""
            if not piece:
                continue
            raw += piece
            if not started:
                if len(raw) < CHAT_TG_HOLD:
                    continue
                raw, started = doc_lang._LABEL_RE.sub("", raw.lstrip()), True
            safe = len(raw) - CHAT_TG_HOLD
            if safe > sent:
                yield _line(raw[sent:safe])
                emitted += safe - sent
                sent = safe
    except Exception as error:  # noqa: BLE001
        print("  [TG] потоковый перевод блока не удался (%s)" % type(error).__name__)
    if not started:
        raw = doc_lang._LABEL_RE.sub("", raw.lstrip())
    raw = _TG_TAIL_LABEL_RE.sub("", raw).rstrip()
    rest = raw[sent:]
    if rest:
        yield _line(rest)
        emitted += len(rest)
        sent += len(rest)
    if emitted:
        return   # часть текста пользователь уже видит, повторять блок другим переводчиком нельзя
    fallback = lang_service.translator.translate_block(block, "ru", "tg")
    yield _line(block if fallback is None else fallback)


def _translate_chat_stream_llm(chunks, src, tgt):
    """Русский поток -> таджикский поток: абзац за абзацем, без ожидания конца ответа."""
    buffer, last, russian = "", None, []
    first, joiner = True, ""

    def _drain(final):
        nonlocal buffer, first, joiner
        while True:
            found = _next_tg_block(buffer, first, final)
            if not found:
                return
            block, buffer, next_joiner = found
            if block.strip():
                yield from _stream_tg_block(block, "" if first else joiner)
                first = False
                joiner = next_joiner

    for obj in chunks:
        if obj.get("done"):
            last = obj
        piece = obj.get("response") or ""
        russian.append(piece)
        buffer += piece
        yield from _drain(bool(obj.get("done")))
    yield from _drain(True)
    done = dict(last or {})
    done["done"] = True
    done["response"] = ""
    done["source_response"] = "".join(russian)
    done["lang"] = tgt
    yield json.dumps(done, ensure_ascii=False) + "\n"


def _translate_chat_stream(chunks, src, tgt):
    """Оборачивает поток Ollama переводом на язык пользователя."""
    buffer = ""
    final_obj = None
    use_llm = tgt == "tg" and src == "ru" and _llm_tajik_available()

    if use_llm:
        yield from _translate_chat_stream_llm(chunks, src, tgt)
        return
    # Русский оригинал уходит фронту вместе с последним куском и ложится в
    # историю диалога вместо перевода: на следующем ходу промпт должен остаться
    # русским, иначе модель получит в контексте язык, которого не знает.
    source_text = []

    def _emit(block):
        source_text.append(block)
        translated = _tg_block_via_llm(block) if use_llm else None
        if translated is None:
            translated = lang_service.translator.translate_block(block, src, tgt)
        # Слой мог отвалиться посреди ответа. Отдать русский фрагмент честнее,
        # чем оборвать диалог на полуслове.
        return json.dumps(
            {"response": block if translated is None else translated, "done": False},
            ensure_ascii=False,
        ) + "\n"

    for obj in chunks:
        if obj.get("done"):
            final_obj = obj
        buffer += obj.get("response") or ""
        while True:
            if use_llm and len(buffer) < CHAT_TG_LLM_MIN_BLOCK and not obj.get("done"):
                break
            cut = _translate_split_point(buffer)
            if cut is None:
                break
            block, buffer = buffer[:cut], buffer[cut:]
            if block.strip():
                yield _emit(block)

    if buffer.strip():
        yield _emit(buffer)

    done = dict(final_obj or {})
    done["done"] = True
    done["response"] = ""
    done["source_response"] = "".join(source_text)
    done["lang"] = tgt
    yield json.dumps(done, ensure_ascii=False) + "\n"


CHAT_CLARIFY_ENABLED = os.environ.get("CHAT_CLARIFY", "1") != "0"
# Шаг «понять вопрос» формулирует поиск на языке кодексов; от его качества зависит,
# дойдёт ли до модели нужная статья. Поэтому он идёт на сильной модели (GPT_MODEL_DEEP)
# и в быстром режиме тоже. CHAT_QUERY_STRONG=0 возвращает общую модель.
CHAT_QUERY_STRONG = os.environ.get("CHAT_QUERY_STRONG", "1") != "0"
CHAT_QUERIES_MAX = 3


def _parse_json_object(raw):
    """JSON-объект из ответа модели; терпит обёртку из ```json и лишний текст."""
    raw = (raw or "").strip()
    if not raw:
        return None
    try:
        data = json.loads(raw)
    except json.JSONDecodeError:
        match = re.search(r"\{.*\}", raw, re.S)
        if not match:
            return None
        try:
            data = json.loads(match.group(0))
        except json.JSONDecodeError:
            return None
    return data if isinstance(data, dict) else None


_TG_LETTERS_RE = re.compile(r"[ӣӯқҳҷғӢӮҚҲҶҒ]")
_CYRILLIC_RE = re.compile(r"[а-яёА-ЯЁ]")
# Слова, которые сами по себе не называют предмет вопроса: «сколько штраф»,
# «какой срок», «этот договор». Основы, а не формы, чтобы «штрафа» и «сроков»
# тоже считались пустыми, а «срок исковой давности» — нет.
_GENERIC_TOPIC_STEMS = (
    "штраф", "срок", "размер", "ставк", "наказан", "ответственн", "договор",
    "закон", "вопрос", "сумм", "прав", "какой", "сколько", "можно", "этот",
    "ҷарима", "мӯҳлат", "шартнома", "чанд", "чӣ", "қадар", "аст", "чист",
    "будет", "составля",
)
_GENERIC_TOPIC_STOP = {
    "за", "по", "ли", "и", "в", "о", "на", "не", "что", "это", "ин",
    # «Что делать?», «Оё мумкин аст?», «Чӣ кор кунам?» — вопрос без предмета.
    "делать", "быть", "как", "оё", "мумкин", "кор", "кунам", "кардан", "бояд",
}
_FIRST_PERSON_RE = re.compile(
    r"\b(я|меня|мне|мной|мною|мой|моя|моё|мои|мою|моих|моим|мы|нас|нам|"
    r"ман|маро|манам|мо|моро)\b", re.I,
)


_FALLBACK_ASK = "К какой области права относится ваш вопрос?"
_FALLBACK_OPTIONS = (
    "Трудовое право", "Семейное право", "Гражданское право (договоры)",
    "Уголовное право", "Налоговое право",
)
_SITUATION_ASK_RE = re.compile(r"что\s+(мне\s+)?делать|как\s+быть|куда\s+обращаться|чӣ\s+кор\s+кун", re.I)
_DEMONSTRATIVE_RE = re.compile(
    r"\b(этот|этого|этому|этим|данн\w+|ин)\s+(договор|документ|решени|шартнома|ҳуҷҷат|қарор)\w*", re.I,
)


def _topic_is_generic(topic):
    words = re.findall(r"\w+", (topic or "").lower())
    if not words:
        return True
    return all(w in _GENERIC_TOPIC_STOP or w.startswith(_GENERIC_TOPIC_STEMS) for w in words)


def _vague_reason(original, machine_ru, has_history):
    """Очевидно неясные формулировки, которые не стоит отдавать на усмотрение модели.

    Её решение о переспросе недетерминировано: «сколько штраф» в трети случаев
    проходил как готовый вопрос, и чат отвечал по случайной статье. Здесь только
    то, что видно по самому тексту: вопрос целиком из слов без предмета, ссылка
    «этот договор» без единой реплики истории, рассказ «меня уволили, что
    делать» без деталей. Возвращает "no_topic", "no_facts" или None.
    """
    texts = [t for t in (original, machine_ru) if t]
    # С историей «а сколько штраф?» продолжает разговор и понятно из контекста.
    if not has_history and any(_topic_is_generic(re.sub(r"[?!.,]", " ", t)) for t in texts):
        return "no_topic"
    if not has_history and any(_DEMONSTRATIVE_RE.search(t) for t in texts):
        return "no_topic"
    joined = " ".join(texts)
    if len(original.split()) <= 8 and _FIRST_PERSON_RE.search(joined) and _SITUATION_ASK_RE.search(joined):
        return "no_facts"
    return None


def _understand_question(model, original, machine_ru, lang, prior_turns, allow_clarify, llm_kwargs=None):
    """Один вызов модели до RAG: понять вопрос и решить, нужно ли переспросить.

    Возвращает None при сбое (тогда чат работает по-старому, без переписывания)
    или {"ru", "search", "clarify"}:
      ru      — вопрос по-русски, самодостаточный (с учётом истории и ответа на
                уточнение);
      search  — русские юридические ключевые слова для поиска по кодексам;
      clarify — None либо {"question", "options", "source"}, где source — тот же
                вопрос с вариантами по-русски для истории диалога.

    Зачем в одном вызове. Машинный перевод искажает таджикские вопросы («мӯҳлати
    даъво» превращался в «сколько лет для подачи исков»), а BM25 по вопросу в
    разговорной форме не находил нужную статью (ст. 93 об отпуске стояла ниже
    пятёрки). Модель читает оригинал, видит перевод как подсказку и отдаёт и
    чистую формулировку, и ключевые слова; решение о переспросе стоит рядом и
    не требует второго обращения к провайдеру.
    """
    history = "\n".join(
        f"{'Пользователь' if role == 'user' else 'Ассистент'}: {content}"
        for role, content in prior_turns
    ) or "нет"
    clarify_rules = """
УТОЧНЕНИЕ.
topic — о какой норме, институте или документе идёт речь, 2–5 слов. Бери слова
только из вопроса пользователя и истории, ничего не придумывай. Если предмет
неясен, оставь пустую строку. Слова «штраф», «срок», «размер», «ставка»,
«наказание», «договор», «закон», «можно ли» сами по себе предмет не называют.
gap — чего не хватает для ответа, одно из трёх значений:
  "no_topic" — предмета нет: вопрос обрывочный, не ясно, о чём речь (когда
               спрашивают «сколько штраф» или «какой срок», не сказав за что и
               чего; «можно ли расторгнуть этот договор» без указания договора);
  "no_facts" — предмет есть, но пользователь не сказал, что именно произошло,
               а от этого зависит всё (просто «меня уволили»);
  "offtopic" — вопрос вообще не о праве и законах (погода, рецепты, программи-
               рование, спорт): переспрашивать нечего, юридической нормы нет;
  "none"     — отвечать можно сразу. Это значение по умолчанию: если назван
               институт, норма, документ или действие, отвечай, даже когда часть
               деталей опущена. Короткая оговорка в ответе лучше лишнего вопроса.
ask и choices заполняй всегда, даже при gap="none": приложение может
решить переспросить само. ask — один короткий вопрос пользователю (не повтор
его вопроса), о самом важном. choices — 2–4 варианта ответа на ask: короткие
(до 8 слов), не пересекаются, каждый отвечает на ask. Это возможные ответы
самого пользователя (виды нарушений, типы договоров, основания, его статус), а
не советы, не просьбы что-то сделать и не «да/нет». Если у вопроса вообще нет
предмета (например, только «можно ли?» или «что делать?»), спроси, к какой области
права он относится, а в choices дай области: трудовое, семейное, гражданское
(договоры), уголовное, налоговое право. Не добавляй варианты
«Другое», «Не указано», «Не знаю», «Уточнить невозможно»: интерфейс добавит
своё «Другое» сам.
""" if allow_clarify else """
УТОЧНЕНИЕ. Пользователь уже ответил на твой уточняющий вопрос, переспрашивать
нельзя: gap="none", ask и choices пустые.
"""

    prompt = f"""Ты помощник юриста по законодательству Республики Таджикистан.
Задача: понять последний вопрос пользователя и подготовить его к поиску по
кодексам РТ. Вопрос может быть на русском или таджикском (кириллицей).

ru и search — ТОЛЬКО по-русски, даже если вопрос на таджикском: ни одного
таджикского слова и ни одной таджикской буквы (ӣ ӯ қ ҳ ҷ ғ), никакого английского.
ru — самодостаточная формулировка вопроса по-русски: с учётом истории диалога
(если последняя реплика — ответ на твой уточняющий вопрос, объедини её с
исходным вопросом). Сохраняй смысл точно, ничего не добавляй от себя и не
переноси в вопрос темы, о которых пользователь не спрашивал. Не путай
таджикское «ҷазо» (наказание) и «ҷарима» (штраф).
queries — 2–3 коротких названия статей или институтов ТАК, КАК ОНИ НАЗВАНЫ В КОДЕКСАХ РТ, которые нужны для
ответа (3–6 слов, именительный падеж), например «Размер алиментов на несовершеннолетних детей»,
«Раздел имущества супругов», «Сроки выплаты заработной платы». Это важнее всего: бытовое «муж не платит» и
кодексовое «взыскание алиментов» разные слова, и статью о размере поиск без такого названия не находит.
domain — область права одним словом: "трудовое", "семейное", "гражданское", "уголовное", "налоговое",
"процессуальное", "административное" или "" если неясно.
search — 4–8 русских слов для поиска по названиям и тексту статей кодексов:
юридические термины в именительном падеже, без вопросительных слов. Названия
статей кодексов начинаются с «Продолжительность», «Размер», «Срок», «Ставка»,
«Возраст», «Порядок», «Право», «Ответственность»: если спрашивают «сколько
дней», «какой размер», «какой срок», «какая ставка», «с какого возраста»,
добавь соответствующее слово.
{clarify_rules}
ask и choices пиши по-русски, даже если пользователь пишет на таджикском:
приложение переведёт их само.

История диалога:
{history}

Последний вопрос пользователя (оригинал):
{original}

Машинный перевод оригинала на русский (может быть неточным, это подсказка):
{machine_ru}

Ответь только JSON без пояснений:
{{"topic": "...", "gap": "no_topic" или "no_facts" или "none", "ru": "...", "search": "...", "queries": ["..."], "domain": "...", "ask": "...", "choices": ["..."]}}"""

    try:
        raw = llm_once(model, prompt, 700, temperature=0.0, keep_alive=CHAT_KEEP_ALIVE,
                       timeout=60, json_mode=True, **(llm_kwargs or {}))
    except Exception as error:  # noqa: BLE001
        # Шаг — надстройка над ответом: сбой не должен его блокировать, поэтому
        # чат просто работает как раньше, по машинному переводу.
        print("  [CHAT] понимание вопроса не удалось (%s)" % type(error).__name__)
        return None

    data = _parse_json_object(raw)
    if not data:
        print("  [CHAT] ответ шага «понять вопрос» не разобран: %r" % (raw or "")[:120])
        return None

    # «Да», «Нет», «Подробнее» не отвечают на уточняющий вопрос и в карточке
    # выглядят как заглушка («Бале.», «На, на.» на таджикском).
    filler = {"да", "нет", "подробнее", "другое", "иное", "не знаю", "не указано", "ещё", "еще"}

    def _clean(items):
        cleaned = []
        for item in items or []:
            item = str(item).strip().strip("-•* ").rstrip(".")
            if item.lower().strip(" ,.!") in filler:
                continue
            if item and item.lower() not in [c.lower() for c in cleaned]:
                cleaned.append(item[:90])
        return cleaned[:4]

    ru = str(data.get("ru") or "").strip()[:500]
    search = str(data.get("search") or "").strip()[:200]
    # Корпус русский: таджикские буквы или английский в этих полях делают поиск
    # бесполезным (модель иногда так отвечает), тогда лучше машинный перевод.
    # Иногда в ru просачивается пересказ инструкции: «С учётом истории диалога и
    # перевода, …». Вводную часть отбрасываем, а если ссылка на историю или
    # перевод осталась, поле бесполезно.
    ru = re.sub(r"^\s*С учет[оа]м[^,:]{0,80}[,:]\s*", "", ru, flags=re.I)
    if re.search(r"истори[июя] диалога|машинн\w* перевод|перевод\w* вопрос", ru, re.I):
        ru = ""
    if _TG_LETTERS_RE.search(ru) or not _CYRILLIC_RE.search(ru):
        ru = ""
    if _TG_LETTERS_RE.search(search) or not _CYRILLIC_RE.search(search):
        search = ""

    # Решение модели о переспросе недетерминировано (gpt-5-nano не принимает
    # temperature): одна и та же «какой срок исковой давности» то проходила, то
    # получала уточнение, а «сколько штраф» иногда отвечалась наугад. Поэтому
    # опираемся на topic, который модель заполняет стабильнее: пустая или
    # «пустая по смыслу» тема — переспрашиваем, названная — отвечаем.
    gap = str(data.get("gap") or "").strip().lower()
    topic = str(data.get("topic") or "").strip()
    # no_facts относится к рассказу о собственной ситуации («меня уволили»).
    # На безличных вопросах («какой срок…», «сколько дней…») модель ставила его
    # наугад и переспрашивала там, где ответ лежит прямо в кодексе.
    if gap == "no_facts" and not _FIRST_PERSON_RE.search(original + " " + machine_ru):
        gap = "none"
    # Названная конкретная тема отменяет «нет предмета». Обратное правило
    # («тема пустая по смыслу, значит переспросить») не нужно и вредно: модель
    # пишет темой одно слово «наказание» и на «что за кражу», а очевидно пустые
    # формулировки целиком ловит _vague_reason ниже.
    if gap == "no_topic" and topic and not _topic_is_generic(topic):
        gap = "none"
    # Область права названа («виды административных процедур» — маркеры Кодекса
    # об административных процедурах): «нет предмета» от модели не принимается,
    # она ставила его и на такие вопросы. Очевидно пустые формулировки («сколько
    # штраф») ниже всё равно ловит _vague_reason.
    if gap == "no_topic" and legal_rag.matched_codes(original + " " + machine_ru):
        gap = "none"
    vague = _vague_reason(original, machine_ru, bool(prior_turns))
    gap = vague or gap

    # «offtopic» принимается только когда в вопросе нет ни одного юридического
    # маркера и нет истории: иначе редкая, но законная тема (без слов из
    # CODE_HINTS) получила бы отказ вместо ответа.
    offtopic = (
        gap == "offtopic"
        and not prior_turns
        and not legal_rag.matched_codes(original + " " + machine_ru)
    )
    if gap == "offtopic":
        gap = "none"

    # Названия статей, как их пишет кодекс: каждое идёт отдельным запросом поиска.
    # Чистятся так же, как подвопросы глубокого режима: русские строки без повторов.
    queries = chat_modes.parse_subqueries(
        {"queries": data.get("queries")}, base=ru or original, limit=CHAT_QUERIES_MAX)
    result = {"ru": ru, "search": search, "queries": queries,
              "domain": str(data.get("domain") or "").strip().lower()[:30],
              "clarify": None, "offtopic": offtopic}
    if allow_clarify and gap in ("no_topic", "no_facts"):
        ask = str(data.get("ask") or "").strip()[:300]
        options = _clean(data.get("choices"))
        # Модель иногда подставляет в ask сам вопрос пользователя (или его
        # пересказ): такой «уточняющий» вопрос бесполезен, лучше ответить, чем
        # спросить то же самое.
        echo = any(
            difflib.SequenceMatcher(None, ask.lower(), (known or "").lower()).ratio() > 0.75
            for known in (original, machine_ru, ru)
        )
        # Латиница в ask значит мусор («işti»): лучше ответить, чем показать это.
        # Расширенная латиница (ş, ı, ü) тоже мусор, поэтому не только [A-Za-z].
        latin = re.compile(r"[A-Za-zÀ-ɏ]")
        if latin.search(ask) or any(latin.search(o) for o in options):
            ask = ""
        # Формулировка очевидно пустая («сколько штраф»), а модель на этот раз
        # прислала негодные вопрос или варианты: без запасной карточки такой
        # вопрос уходил в ответ по случайной статье. Запасной вопрос
        # фиксированный, он не зависит от настроения модели.
        if vague == "no_topic" and not (ask and len(options) >= 2 and not echo):
            ask, options, echo = _FALLBACK_ASK, list(_FALLBACK_OPTIONS), False
        if ask and len(options) >= 2 and not echo:
            # Модель пишет ask и choices только по-русски, а таджикоязычному
            # пользователю их переводит встроенный переводчик: таджикский
            # текст самой модели то приходил русским, то с мусором, а русская
            # карточка в таджикском диалоге выглядит как «система не поняла».
            shown_ask, shown_options = ask, options
            if lang == "tg":
                translated = _tajik_cards([ask] + options)
                if translated and len(translated) == len(options) + 1:
                    shown_ask, shown_options = translated[0], list(translated[1:])
            result["clarify"] = {
                "question": shown_ask,
                "options": shown_options,
                # В историю диалога уходит русская версия: промпт русский.
                "source": ask + "\n" + "\n".join(f"- {option}" for option in options),
            }
    return result


# Заголовки статей кодексов строятся по одному шаблону: «Продолжительность …»,
# «Размер …», «Ставки …», «Возраст, с которого …». Вопрос «сколько дней отпуска»
# этих слов не содержит, и BM25 поднимал статьи, где «отпуск» есть в заголовке
# чаще, а ст. 93 «Продолжительность ежегодного основного минимального отпуска»
# в выдачу не попадала, хотя ответ (24 дня) лежит именно в ней. Модель, которую
# просили добавить такое слово в ключевые слова, подсказку игнорировала.
_TITLE_WORD_HINTS = (
    (re.compile(r"сколько\s+(?:дней|месяцев|лет|времени)|как\s+долго|сколько\s+длится", re.I),
     "продолжительность"),
    (re.compile(r"какой\s+срок|на\s+какой\s+срок", re.I), "срок"),
    (re.compile(r"какой\s+размер|какая\s+сумма|сколько\s+(?:штраф|составля|платить|платят)", re.I),
     "размер"),
    (re.compile(r"какая\s+ставка|какой\s+процент|сколько\s+процентов|какие\s+ставки", re.I), "ставка ставки"),
    (re.compile(r"с\s+какого\s+возраста|в\s+каком\s+возрасте|какой\s+возраст", re.I), "возраст"),
)


# Название кодекса и страны в ключевых словах разбавляют долю слов заголовка,
# по которой движок начисляет бонус: «ежегодный отпуск Трудовой кодекс
# Таджикистан» ст. 93 не находил, а «ежегодный отпуск продолжительность» находил.
# Выбор кодекса делает основной запрос (route_codes), здесь эти слова лишние.
_SEARCH_NOISE_RE = re.compile(
    r"\b\w+(?:ый|ой|ая|ого|ом)\s+кодекс\w*|\b(?:кодекс\w*|таджикистан\w*|республик\w*|рт|тк|ук|гк|нк|ск)\b",
    re.I,
)


def _augment_search(search, question):
    """Ключевые слова поиска: без названия кодекса и со словами шаблонных заголовков."""
    words = re.sub(r"\s+", " ", _SEARCH_NOISE_RE.sub(" ", search or "")).strip()
    extra = [hint for pattern, hint in _TITLE_WORD_HINTS if pattern.search(question or "")]
    return (words + " " + " ".join(extra)).strip()


def _no_basis_note(question):
    """Пояснение к отказу «нормы в базе нет»: что именно отсутствует."""
    note = "\n\nВ подключённой базе кодексов РТ подходящей нормы для этого вопроса не нашлось."
    if re.search(r"штраф|размер\w*\s+наказан|административн", question or "", re.I):
        note += (
            " В нашем справочнике штрафов КоАП (редакция кодекса до 2019 года) такого случая не нашлось: "
            "норма могла появиться позже или сформулирована иначе. Точный размер штрафа назвать не могу: "
            "проверьте его в действующей редакции Кодекса об административных правонарушениях Республики Таджикистан."
        )
    else:
        note += " Уточните формулировку или проверьте вопрос по официальному тексту закона."
    return note


def _retrieve_chat_merged(queries, top_k):
    """Выдача нескольких запросов вперемешку: первый, второй, первый, второй…

    По одной формулировке нужная статья то поднималась, то нет: по вопросу
    «сколько дней отпуска положено работнику» ст. 93 о продолжительности отпуска
    стояла восьмой, а по ключевым словам «ежегодный основной минимальный отпуск»
    первой; ставка НДС (ст. 264) вовсе выпадала, и модель называла число из
    головы. Чередование берёт лучшее из обоих списков и стоит одного лишнего
    BM25-запроса, а не обращения к модели.
    """
    index = load_chat_rag_index()
    lists = [retrieve_rag(query, top_k=top_k, index=index, fines_lane=True) for query in queries if query]
    merged, seen = [], set()
    for rank in range(top_k):
        for results in lists:
            if rank >= len(results):
                continue
            item = results[rank]
            key = (item.get("source"), item.get("title"), (item.get("text") or "")[:60])
            if key not in seen:
                seen.add(key)
                merged.append(item)
    return merged[:top_k + 2]


def _prefer_domain_codes(results, texts, limit):
    """Статьи кодекса, о котором идёт речь, выше статей чужого кодекса.

    «При разводе как делится имущество» находило Гражданский кодекс (ст. 326,
    общая долевая собственность) рядом с Семейным (ст. 38, имущество супругов), и
    слабая модель отвечала по ГК: закон не тот, а уверенность прежняя. Область
    определяется по словам вопроса и названий статей (маркеры кодексов в
    legal_rag). Подавляются только кодексы, у которых совпадений меньше половины
    от лучшего, так что спор на стыке отраслей не теряет нужных норм. Порядок
    внутри групп сохраняется. Нет явной области — выдача не меняется.
    """
    hits = legal_rag.matched_codes(" ".join(t for t in texts if t))
    if not hits:
        return results[:limit]
    best = max(hits.values())
    primary = {source for source, count in hits.items() if count * 2 >= best}
    own = [item for item in results if item.get("source") in primary]
    other = [item for item in results if item.get("source") not in primary]
    return (own + other)[:limit]


def _tajik_cards(texts):
    """Русские тексты карточки -> таджикские; None, если перевести нечем.

    Сначала модель (при облачном провайдере), затем локальный NLLB: карточки и вопросы короткие,
    и модель переводит их заметно естественнее.
    """
    if _llm_tajik_available():
        try:
            out = doc_lang.translate_items(list(texts), "ru", "tg", _analysis_llm, kind="элементы интерфейса юридического чата")
            if out and len(out) == len(texts) and any(a != b for a, b in zip(out, texts)):
                return out
        except Exception as error:  # noqa: BLE001
            print("  [TG] перевод карточки моделью не удался (%s)" % type(error).__name__)
    if not lang_service.translator.available():
        return None
    return lang_service.translator.translate(texts, "ru", "tg")


def _plan_subqueries(model, question, llm_kwargs=None):
    """Режим «глубина»: разбить ситуацию на юридические подвопросы для поиска.

    Один запрос к кодексам находит статьи по самой яркой части рассказа
    («уволили»), а долг по зарплате, срок обращения в суд и порядок расчёта
    остаются без нормы. Каждый подвопрос идёт отдельным поисковым запросом.
    Сбой не блокирует ответ: без подвопросов чат ищет по основному запросу.
    """
    try:
        raw = llm_once(model, chat_modes.subquery_prompt(question), 400, temperature=0.0,
                       keep_alive=CHAT_KEEP_ALIVE, timeout=60, json_mode=True, **(llm_kwargs or {}))
    except Exception as error:  # noqa: BLE001
        print("  [CHAT] разбиение на подвопросы не удалось (%s)" % type(error).__name__)
        return []
    return chat_modes.parse_subqueries(_parse_json_object(raw), base=question)


# Служебная метка контекста. Правило в промпте «не пиши это в ответе» модель
# игнорирует (в ответах встречалась и латиницей, «PROVERENNAYA SSILKA RAG»),
# поэтому метка заменяется на нейтральное «Источник:» прямо в потоке.
_RAG_LABEL_RE = re.compile(
    r"ПРОВЕРЕННАЯ\s+ССЫЛКА(?:\s+RAG)?\s*:?|PROVERENNAYA\s+\w+\s+RAG\s*:?", re.I,
)
_RAG_LABEL_HOLD = 56


def stream_chat_response(payload):
    message = (payload.get("message") or "").strip()
    history = payload.get("history") or []
    model = payload.get("model") or CHAT_MODEL_NAME
    temperature = max(0.0, min(float(payload.get("temperature", 0.2)), 0.3))
    personality = payload.get("personality") or "professional"

    # Роль (юрист / адвокат) и глубина (скорость / глубина) выбираются в
    # интерфейсе. Клиенты, которые их не присылают, получают «юриста» и
    # «скорость»: значения из .env, то есть прежнее поведение.
    role = chat_modes.normalize_role(payload.get("role"))
    profile = chat_modes.depth_profile(
        payload.get("depth"), CHAT_RAG_TOP_K, CHAT_RAG_MAX_CHARS, CHAT_MAX_TOKENS
    )
    deep = profile.depth == "deep"

    # В глубоком режиме служебные шаги (понять вопрос, разбить на подвопросы)
    # тоже идут на сильной модели: слабая для одной и той же ситуации давала то
    # «затопление, ущерб», то «забота, обида» и ничего полезного для поиска.
    aux_kwargs = {}
    if deep and chat_modes.strong_model():
        aux_kwargs = {"openai_model": chat_modes.strong_model(), "reasoning_effort": chat_modes.strong_effort()}
    # Шаг «понять вопрос» нужен в любом режиме. В быстром он идёт на той же сильной
    # модели, но без размышлений: короткий JSON, а не рассуждение.
    understand_kwargs = aux_kwargs
    if not deep and CHAT_QUERY_STRONG and chat_modes.strong_model():
        understand_kwargs = {"openai_model": chat_modes.strong_model(), "reasoning_effort": "minimal"}

    # Язык берётся из самого вопроса, а не из переключателя локали: спросить
    # по-таджикски пользователь может и на русской версии интерфейса. Локаль
    # страницы остаётся значением по умолчанию для случаев, где сигнала нет.
    lang = lang_service.detect_lang(message, default=payload.get("lang"))

    # Составленный в этом диалоге документ нужен целиком, если его просят
    # поправить, а реплики истории ниже режутся: берём его до нарезки.
    prev_doc = chat_modes.previous_document(history)

    turns = []
    for item in history[-profile.history_turns:]:
        speaker = item.get("role")
        content = (item.get("content") or "").strip()
        if speaker in ["user", "ai"] and content:
            turns.append([speaker, content[:profile.history_chars]])
    current_in_history = (bool(turns) and turns[-1][0] == "user"
                          and turns[-1][1] == message[:profile.history_chars])

    # Ответ на уточнение — часто одно-два слова («16 сол»), по которым язык не
    # определить: детектор принимал их за русский, и продолжение таджикского
    # диалога приходило по-русски. Язык берём у исходного вопроса.
    clarified = bool(payload.get("clarified"))
    if clarified and len(message) <= 40:
        earlier = [content for role, content in (turns[:-1] if current_in_history else turns)
                   if role == "user"]
        if earlier:
            lang = lang_service.detect_lang(earlier[-1], default=lang)

    # Корпус кодексов РТ русский — таджикских текстов в storage/app/rag нет.
    # Поэтому таджикский вопрос переводится в русский поисковый запрос: без
    # этого BM25 не нашёл бы ни одной статьи и ответ остался бы без опоры.
    # Приветствие не должно поднимать RAG: «салом» цеплялось в BM25 за «салам»
    # (термин исламского финансирования) в Налоговом кодексе, и на «здравствуйте»
    # приходил пересказ статьи 206 о доходах банков.
    smalltalk = lang_service.is_smalltalk(message)

    search_query = message
    if lang == "tg" and not smalltalk:
        # Вопрос и таджикские реплики из истории переводятся одним батчем:
        # промпт целиком русский, иначе модель получила бы в контексте текст,
        # который не читает. Ответы ассистента фронт присылает уже по-русски
        # (source_response), поэтому второй раз их переводить не нужно.
        user_turns = [turn for turn in turns if turn[0] == "user"]
        translated = _questions_to_russian([message] + [turn[1] for turn in user_turns])
        # Длины совпадают по построению translate(), но обращение по индексу
        # надёжнее итератора: StopIteration внутри генератора стал бы
        # RuntimeError и уронил бы весь ответ (PEP 479).
        if translated and len(translated) == len(user_turns) + 1:
            search_query = lang_service.normalize_search_query(translated[0])
            for turn, text in zip(user_turns, translated[1:]):
                turn[1] = text

    # Фронт присылает историю вместе с текущей репликой (она последняя), а
    # другие клиенты могут её не включать. Предыдущие ходы нужны шагу
    # «понять вопрос»: без них ответ на уточнение («16 сол») не к чему приложить.
    prior_turns = list(turns[:-1]) if current_in_history else list(turns)

    # clarified=True приходит, когда текущая реплика — ответ на нашу карточку
    # с вариантами. Повторно переспрашивать после ответа нельзя: диалог
    # зациклился бы, поэтому шаг вызывается с allow_clarify=False.
    # Просьба составить договор, заявление, иск и т. п. (или поправить только
    # что составленный документ). Смотрим и оригинал, и перевод с таджикского.
    # Если в просьбе не хватает главного (какой договор, с кем), чат сначала
    # спрашивает: документ с угаданными сторонами хуже, чем один короткий вопрос.
    # Состояние диалога приходит со страницей (doc_state) и возвращается с карточкой.
    doc = None
    if not smalltalk and not (payload.get("context") or "").strip():
        doc_plan = doc_dialog.plan_document(
            message, search_query if search_query != message else "", prev_doc,
            payload.get("doc_state"), lang, translate=_tajik_cards,
        )
        if doc_plan and doc_plan.ask:
            card = doc_plan.ask
            yield json.dumps({
                "response": "",
                "clarify": {"question": card["question"], "options": card["options"]},
                "doc_state": doc_plan.state,
                "source_response": card["source"],
                "lang": lang,
                "done": True,
            }, ensure_ascii=False) + "\n"
            return
        if doc_plan and doc_plan.doc:
            doc = doc_plan.doc
            if doc_plan.request_text and not doc.edit:
                # Просьба, собранная из первого сообщения и ответов на вопросы:
                # одно слово «Аренды» само по себе поиску и модели ничего не даст.
                search_query = doc_plan.request_text

    # Этапы ответа уходят странице отдельными строками потока. Она показывает их,
    # только если ответа нет дольше 25 секунд: пользователь видит, на чём чат.
    def _stage(key, **fmt):
        return json.dumps(chat_modes.stage_event(key, lang, **fmt), ensure_ascii=False) + "\n"

    rag_queries = [search_query]
    understood = None
    if CHAT_CLARIFY_ENABLED and not smalltalk and not (payload.get("context") or "").strip():
        # Документ составляется сразу, с заглушками для неизвестных данных:
        # карточка «к какой области права относится вопрос» на «составь
        # договор аренды» только раздражала бы. Правке документа понимание
        # вопроса не нужно вовсе: запрос и так короткий и ясный.
        # Документу понимание вопроса тоже не нужно: запрос уже собран диалогом
        # (вид, суть, сторона), а нужные статьи задают document_queries.
        if not doc:
            yield _stage("understand")
            understood = _understand_question(
                model, message, search_query, lang, prior_turns,
                allow_clarify=(not doc and not clarified and payload.get("clarify") is not False),
                llm_kwargs=understand_kwargs,
            )

    if understood and understood.get("offtopic"):
        reply = ("Я юридический помощник по законодательству Республики Таджикистан, "
                 "и на этот вопрос ответить не могу. Задайте вопрос о законах РТ: договорах, "
                 "труде, семье, налогах, уголовной или административной ответственности.")
        if lang == "tg":
            translated = _tajik_cards([reply])
            if translated:
                reply = translated[0]
        yield json.dumps({"response": reply, "source_response": reply, "lang": lang, "done": True},
                         ensure_ascii=False) + "\n"
        return

    if understood and understood["clarify"]:
        card = understood["clarify"]
        yield json.dumps({
            "response": "",
            "clarify": {"question": card["question"], "options": card["options"]},
            "source_response": card["source"],
            "lang": lang,
            "done": True,
        }, ensure_ascii=False) + "\n"
        return

    if understood and understood["ru"]:
        # Чистая русская формулировка от модели точнее машинного перевода и
        # уже учитывает ответ на уточнение; ключевые слова помогают BM25
        # поднять статью, которую разговорная форма вопроса не находила.
        search_query = understood["ru"]
        rag_queries = [search_query, _augment_search(understood["search"], search_query)]
        # Названия статей «как в кодексе»: так находятся нормы, которых бытовая
        # формулировка не задевает (размер алиментов, раздел имущества супругов).
        rag_queries += understood.get("queries") or []
    elif clarified:
        # Понять вопрос не удалось: хотя бы склеим уточнение с исходным вопросом.
        prior_user = [content for role, content in prior_turns if role == "user"]
        if prior_user:
            search_query = f"{prior_user[-1]}. Уточнение пользователя: {search_query}"
            rag_queries = [search_query]

    # Документу нужны статьи, которых в просьбе не названо (форма сделки,
    # существенные условия, содержание иска), а сложной ситуации в режиме
    # «глубина» — по отдельной статье на каждый юридический подвопрос.
    rag_top_k, rag_max_chars = profile.top_k, profile.max_chars
    if doc:
        rag_top_k, rag_max_chars = max(rag_top_k, 8), max(rag_max_chars, 9000)
        rag_queries = rag_queries + chat_modes.document_queries(doc)
    if profile.subqueries and not smalltalk and not (doc and doc.edit):
        yield _stage("plan")
        rag_queries = rag_queries + _plan_subqueries(model, search_query, aux_kwargs)

    if not smalltalk:
        yield _stage("search")
    rag_results = [] if smalltalk else _retrieve_chat_merged(rag_queries, rag_top_k)
    if rag_results:
        rag_results = _prefer_domain_codes(rag_results, rag_queries, rag_top_k + 2)
    if not smalltalk:
        yield _stage("found", n=len(rag_results))
    rag_context = format_rag_context(rag_results, max_chars=rag_max_chars)
    sources = [
        f"[{i + 1}] {item.get('source')} / {item.get('title')}"
        for i, item in enumerate(rag_results)
    ]

    recent_history = [
        f"{'Пользователь' if role == 'user' else 'Ассистент'}: {content}"
        for role, content in turns
    ]

    # Конструктор договора на /contract/create присылает снимок формы и черновик.
    # Без них чат отвечает общими словами: «добавь предоплату» не к чему
    # приложить. Строка «КРИТЕРИЙ:» — контракт с фронтом: по ней он показывает
    # кнопку «добавить в критерии», поэтому формат жёсткий и одна строка.
    contract_context = ""
    context_text = (payload.get("context") or "").strip()
    if context_text:
        contract_context = f"""
ДОГОВОР, НАД КОТОРЫМ РАБОТАЕТ ПОЛЬЗОВАТЕЛЬ:
{context_text[:6000]}

Ты помогаешь довести этот договор до ума. Если из вопроса следует условие,
которое стоит внести в договор, закончи ответ ОТДЕЛЬНОЙ последней строкой
строго вида:
КРИТЕРИЙ: <одно предложение, формулировка условия>
Если вносить нечего, эту строку не добавляй.
"""

    role_line = chat_modes.system_line(role, personality)

    # Документ — самый ответственный вид ответа: его составляет сильная модель
    # и в быстром режиме, если она задана (GPT_MODEL_DEEP).
    gen_model, gen_effort = profile.openai_model, profile.reasoning_effort
    if doc:
        gen_model = gen_model or chat_modes.strong_model()
        gen_effort = gen_effort or chat_modes.strong_effort()

    # Ответ строится по формату роли и глубины, а документ — по указаниям для
    # его вида. Правила цитирования и юрисдикции ниже общие для обоих.
    if doc:
        length_rule = "- Ответ — это сам документ: полный текст, без сокращений и многоточий вместо пунктов"
        first_line_rule = ""
        answer_block = chat_modes.document_block(doc, role, prev_doc)
    else:
        length_rule = chat_modes.length_rule(profile.depth)
        first_line_rule = """- Первой строкой дай прямой ответ на вопрос (число, срок, ставка, размер,
  возраст, санкция), если он есть во фрагментах ниже, и сразу за ним ссылку на
  статью. Не начинай с «К сожалению» и не говори, что ответа нет, если точная
  цифра во фрагментах присутствует: прочитай фрагменты целиком
"""
        answer_block = chat_modes.format_block(role, profile.depth)

    if doc:
        no_ref_rule = "- Если такой строки нет, пиши пункт документа без ссылки на статью"
        no_basis_block = (
            "Документ составляй в любом случае: даже если подходящих фрагментов мало, дай рабочий "
            "текст по общим правилам и честно отметь в разделе «Что заполнить и проверить», "
            "какие нормы проверить по действующей редакции закона."
        )
    else:
        no_ref_rule = "- Если такой строки нет, напиши: «Точная статья в нашей базе законов не найдена»"
        no_basis_block = """ЕСЛИ ОТВЕТА НА ВОПРОС В ФРАГМЕНТАХ НИЖЕ НЕТ ИЛИ ОНИ ОТВЕЧАЮТ ЛИШЬ ЧАСТИЧНО (нужной статьи, размера,
срока или процедуры нет в подключённой базе) — не ограничивайся отказом и не угадывай норму.
1. Сначала скажи по-человечески, как юрист, что подтверждено нашей базой, а чего в ней нет
   (например: «этот вопрос регулирует …, но точной нормы в нашей базе нет, поэтому цифру не называю»).
2. Затем добавь отдельный раздел **Из общих знаний (в нашей базе этого нет):** — 2–5 коротких пунктов о том, что обычно
   действует по праву Республики Таджикистан по этому вопросу: какой вид ответственности или процедура, какой орган
   занимается, порядок обращения или обжалования, на что обратить внимание, что сделать прямо сейчас.
   Правила раздела: (а) пиши только то, в чём уверен; (б) номера статей, точные суммы и сроки называй, только если
   уверен, и помечай их «по общим сведениям, проверьте по действующей редакции закона»; если не уверен — опиши порядок
   без цифр; (в) никогда не выдавай эти сведения за подтверждённые нашей базой и не ссылайся на «фрагменты»;
   (г) право других государств не используй.
3. Раздел «Из общих знаний» нужен, только если база не дала полного ответа; если подтверждённого достаточно, его не пиши.
Отвечай только на тот вопрос, который задан: не подставляй темы, о которых пользователь не спрашивал."""

    if smalltalk:
        # Правила цитирования здесь только мешают: на «салом» модель начинала
        # добросовестно искать норму и приписывала в конец «Точная ссылка не
        # подтверждена RAG».
        prompt = f"""{role_line}

Пользователь поздоровался или поблагодарил, юридического вопроса он не задал.
Ответь ОДНОЙ короткой фразой: поздоровайся в ответ и предложи задать вопрос по
законодательству Республики Таджикистан. Не цитируй законы, не упоминай статьи,
главы и кодексы, не перечисляй темы и не добавляй оговорок.

Сообщение пользователя:
{message}
"""
    else:
        prompt = f"""{role_line}

КРИТИЧЕСКИ ВАЖНО - ЮРИСДИКЦИЯ:
- Используй ТОЛЬКО законы Республики Таджикистан
- ЗАПРЕЩЕНО: "РФ", "Россия", "ГК РФ", "ТК РФ", "НК РФ", "Российская Федерация"
- Разрешено ТОЛЬКО: «Республика Таджикистан», «Гражданский кодекс Республики Таджикистан» и т. д.

Правила RAG:
- Используй фрагменты кодексов РТ ниже как основной справочный материал
- Цитируй главу и статью только из строки «ПРОВЕРЕННАЯ ССЫЛКА RAG»
  (в самом ответе называй норму по образцу «Трудовой кодекс Республики Таджикистан,
  статья 158»: полное название кодекса, которому принадлежит фрагмент, и номер статьи,
  без угловых скобок; служебные слова «ПРОВЕРЕННАЯ ССЫЛКА RAG» не пиши и не переводи)
{no_ref_rule}
- Не выдумывай номера статей, главы или название кодекса
- Не выдумывай номера и даты законов о внесении изменений («Закон РТ от ... №...») —
  указывай их, только если они дословно есть в переданных фрагментах ниже
{length_rule}
{first_line_rule}- Если в вопросе упомянуто право другой страны, не сравнивай и не излагай его:
  ответь по законодательству РТ и одной фразой отметь, что отвечаешь по праву Республики Таджикистан

{no_basis_block}

{answer_block}

Релевантные фрагменты кодексов Республики Таджикистан:
{rag_context if rag_context else "RAG-контекст не найден."}
{contract_context}
История диалога:
{chr(10).join(recent_history) if recent_history else "Нет предыдущей истории."}

Вопрос пользователя:
{search_query}

Доступные источники RAG:
{chr(10).join(sources) if sources else "Нет найденных источников."}
"""

    num_predict = 120 if smalltalk else (profile.doc_tokens if doc else profile.max_tokens)
    allowed_articles = _allowed_article_numbers(rag_results)

    def _generation_stream():
        # Do not wait for Requests' default 512-byte buffer: chat should
        # render each generated fragment as soon as it arrives.
        #
        # The prompt tells the model to cite only from "ПРОВЕРЕННАЯ ССЫЛКА
        # RAG" and to say "не подтверждена RAG" otherwise — but unlike
        # contract analysis and УК РТ qualification, nothing checked
        # server-side whether it actually did. Accumulating the full answer
        # here and diffing its cited article numbers against the ones chat
        # actually retrieved (same check as crime qualification, generalized
        # in _allowed_article_numbers/_unverified_article_numbers) closes
        # that gap without breaking the live token-by-token stream: the
        # check only runs once, on the final "done" object, and its result
        # is appended as a trailing note rather than rewriting anything
        # already streamed.
        full_text = []
        # Хвост в _RAG_LABEL_HOLD символов придерживается до следующего куска:
        # метка может прийти разорванной между двумя токенами.
        pending = ""
        for obj in llm_stream(model, prompt, temperature=temperature, num_predict=num_predict,
                               top_p=0.85, think=False, keep_alive=CHAT_KEEP_ALIVE, timeout=600,
                               openai_model=gen_model, reasoning_effort=gen_effort):
            pending = _RAG_LABEL_RE.sub("Источник:", pending + (obj.get("response") or ""))
            # Сокращения («ГК РТ», «ст.») раскрываются здесь, до показа и до
            # перевода на таджикский: полные названия переводятся точнее.
            pending = chat_modes.expand_abbreviations(pending)
            pending = chat_modes.humanize(pending)
            if obj.get("done"):
                ready, pending = pending, ""
            else:
                cut = max(0, len(pending) - _RAG_LABEL_HOLD)
                ready, pending = pending[:cut], pending[cut:]
            ready = normalize_legal_terms(ready) if ready else ""
            obj["response"] = ready
            if ready:
                full_text.append(ready)
            elif not obj.get("done"):
                continue
            if obj.get("done"):
                answer_text = "".join(full_text)
                # Провайдер иногда закрывает поток без единого символа (сбой,
                # исчерпанный бюджет на «размышления»): пустой пузырь в чате
                # выглядит как зависание.
                if not answer_text.strip():
                    obj["response"] = ("⚠️ Модель не вернула ответ. Повторите запрос или "
                                       "переключитесь на режим «Скорость».")
                    yield obj
                    return
                # Модель нередко отказывает одной фразой «Точная ссылка не
                # подтверждена RAG» без объяснений: пользователь не понимает,
                # чего именно в базе нет.
                if len(answer_text.strip()) < 120 and "в нашей базе законов не найдена" in answer_text:
                    obj["response"] = (obj.get("response") or "") + _no_basis_note(search_query)
                unverified = _unverified_article_numbers(answer_text, allowed_articles)
                if unverified:
                    obj["response"] = (obj.get("response") or "") + (
                        "\n\n⚠️ Не подтверждено по базе законов: статьи " + ", ".join(unverified)
                        + " не найдены среди статей, по которым составлен этот ответ. Проверьте их по тексту закона."
                    )
                unverified_laws = _unverified_law_references(answer_text, rag_context)
                if unverified_laws:
                    obj["response"] = (obj.get("response") or "") + (
                        "\n\n⚠️ Не подтверждено по базе законов: ссылки на закон(ы) " + "; ".join(unverified_laws)
                        + " не найдены среди статей, по которым составлен этот ответ. Не полагайтесь на эту дату и номер без проверки."
                    )
                # Цифра статьи совпала с найденной, но статья с таким номером
                # найдена в другом кодексе: «ст. 39 ГК РТ» вместо «ст. 39 ТК РТ».
                wrong_code = chat_modes.misattributed_citations(answer_text, rag_results)
                if wrong_code:
                    obj["response"] = (obj.get("response") or "") + (
                        "\n\n⚠️ Проверьте кодекс: " + "; ".join(wrong_code[:3]) + "."
                    )
                # В глубоком режиме модель обязана цитировать статью дословно:
                # цитата, которой нет в тексте найденной статьи, — выдумка или
                # вольный пересказ, и пользователь должен об этом знать.
                if deep:
                    bad_quotes = chat_modes.unverified_quotes(
                        answer_text, [item.get("text") for item in rag_results],
                        message, *[content for _, content in turns],
                    )
                    if bad_quotes:
                        shown = "; ".join("«%s»" % (q if len(q) <= 90 else q[:87] + "…") for q in bad_quotes[:3])
                        obj["response"] = (obj.get("response") or "") + (
                            "\n\n⚠️ Цитаты не найдены дословно в текстах найденных статей: " + shown
                            + ". Сверьте их с текстом закона."
                        )
                # Развёрнутая консультация и готовый документ читаются как
                # заключение юриста — приписка об ограничениях ставится кодом.
                if not smalltalk and (deep or doc):
                    obj["response"] = (obj.get("response") or "") + chat_modes.DISCLAIMER
                if not smalltalk:
                    try:
                        obj["citations"] = _chat_citations(answer_text, rag_results)
                    except Exception as e:  # проверка цитат не должна ронять ответ
                        print("citations: %s" % e)
            yield obj

    if not smalltalk:
        yield _stage("write_doc" if doc else "write")

    try:
        if lang == "tg" and not _tajik_translation_available():
            # Молча ответить по-русски на таджикский вопрос нельзя: пользователь
            # решит, что система его не поняла. Причина уходит в лог сервера.
            print("TG перевод недоступен: %s" % lang_service.translator.reason)
            yield json.dumps({
                "response": "_Тарҷумон дастрас нест — ҷавоб бо забони русӣ._\n"
                            "_(Переводчик недоступен, ответ на русском.)_\n\n",
                "done": False,
            }, ensure_ascii=False) + "\n"
            lang = "ru"

        if lang == "ru":
            for obj in _generation_stream():
                yield json.dumps(obj, ensure_ascii=False) + "\n"
        else:
            for chunk in _translate_chat_stream(_generation_stream(), "ru", lang):
                yield chunk
    except LLMBackendError as e:
        yield json.dumps({
            "response": str(e),
            "done": True,
        }, ensure_ascii=False) + "\n"
    except Exception as e:
        yield json.dumps({
            "response": f"⚠️ Ошибка генерации ответа: {str(e)}",
            "done": True,
        }, ensure_ascii=False) + "\n"

@app.route("/api/chat", methods=["POST"])
def chat():
    payload = request.get_json(silent=True) or {}
    if not (payload.get("message") or "").strip():
        return jsonify({"error": "Message is required"}), 400
    return Response(
        stream_with_context(stream_chat_response(payload)),
        mimetype="application/x-ndjson",
        headers={
            "Cache-Control": "no-cache, no-store, must-revalidate",
            "X-Accel-Buffering": "no",
            "Access-Control-Allow-Origin": "*",
        }
    )

# ================= ГЕНЕРАТОР ДОГОВОРА (/contract/create) =================

def _draft_lines(items, empty):
    """Список в столбик для промпта. Пустой список — явная строка, а не пустота:
    молчание в этом месте модель трактует как «раздел на моё усмотрение»."""
    values = [str(item).strip() for item in (items or []) if str(item).strip()]
    return "\n".join("- %s" % value for value in values) if values else empty


SECTION_MARK_RE = re.compile(r"^[ \t>*_#]*#{2,4}[ \t]*([a-zA-Z_][a-zA-Z0-9_]*)", re.M)


def _ollama_once(model, prompt, num_predict, temperature=0.1):
    """Один короткий неструктурированный запрос без стрима.

    Нужен для прохода выбора разделов: там ответ в десяток токенов, и стрим
    только усложнил бы разбор.
    """
    return llm_once(model, prompt, num_predict, temperature=temperature, keep_alive=CHAT_KEEP_ALIVE,
                    openai_model=DRAFT_GPT_MODEL)


def _select_sections(model, prefix, known_keys):
    """Первый проход: какие разделы вообще нужно трогать.

    Смысл в арифметике. Генерация занимает 94% времени запроса и стоит линейно
    в токенах вывода, а модель, получив четыре раздела, переписывала все четыре
    даже когда критерия было два. Отдельный дешёвый вопрос «что менять» стоит
    десяток токенов вывода и вдвое сокращает дорогой второй проход.

    Второе, менее очевидное: этот проход разогревает KV-кэш общего префикса.
    Промпт правки начинается ровно теми же символами, поэтому Ollama не
    разбирает его заново — разбор запроса оплачивается один раз на оба прохода.
    """
    question = (
        prefix
        + "\nЗАДАЧА ЭТОГО ШАГА: только выбрать разделы, ничего не переписывая.\n"
        "Назови МИНИМАЛЬНЫЙ набор разделов, которые придётся изменить ради\n"
        "требований пользователя. Обычно это один-два раздела. Раздел, которого\n"
        "требования не касаются, называть нельзя: каждый лишний раздел стоит\n"
        "пользователю минуты ожидания." + chr(10) +
        "Ответ — одна строка: ключи латиницей через запятую, без пояснений.\n"
        "Допустимые ключи: %s\n"
        "Если требование не ложится ни в один раздел, добавь ключ new.\n"
        "ОТВЕТ:" % ", ".join(known_keys)
    )

    try:
        raw = _ollama_once(model, question, num_predict=48, temperature=0.0)
    except Exception as error:  # noqa: BLE001
        print("  [DRAFT] выбор разделов не удался (%s), правим все" % type(error).__name__)
        return list(known_keys)

    allowed = {key.lower(): key for key in known_keys}
    picked = []
    for token in re.findall(r"[a-zA-Z_][a-zA-Z0-9_]*", raw):
        key = allowed.get(token.lower())
        if key and key not in picked:
            picked.append(key)

    # Пустой или неразобранный ответ — не повод отказывать пользователю в
    # правке: возвращаемся к полному списку, это медленнее, но не хуже, чем
    # было до появления этого прохода.
    if not picked:
        print("  [DRAFT] выбор разделов пуст, правим все. Ответ модели: %r" % raw[:120])
        return list(known_keys)

    print("  [DRAFT] к правке выбрано %d из %d: %s" % (len(picked), len(known_keys), ", ".join(picked)))
    return picked


# Нумерация пунктов («2.1», «5.1») из проверки исключена намеренно. Первая
# версия сверяла все числа исходного раздела с правленым и считала утратой
# то, что раздел переехал с пятого места на третье и перенумеровался. Ложный
# повтор стоил 72 секунды на замере и ничего не исправлял.
_CLAUSE_NUMBER_RE = re.compile(r"^\s*\d+(?:\.\d+)*\.?\s", re.M)


def _significant_numbers(text):
    """Числа-факты: суммы, сроки, проценты. Нумерация пунктов отбрасывается."""
    body = _CLAUSE_NUMBER_RE.sub(" ", text or "")
    found = []
    for raw in re.findall(r"\d+(?:[.,]\d+)?", body):
        if raw not in found:
            found.append(raw)
    return found


def _verify_draft(form_pairs, criteria, originals, edited):
    """Возвращает (потерянные факты формы, неприменённые требования).

    Сверка идёт посекционно и только по числам, которые пользователь ввёл в
    форму. Две предыдущие редакции ошибались с обеих сторон, и обе ошибки
    стоили минут ожидания на ложных повторах:

    - первая сверяла все числа исходного раздела и считала утратой смену
      нумерации пунктов, когда раздел переезжал с пятого места на третье;
    - вторая искала факты во всём выводе разом и требовала сумму договора
      даже тогда, когда модель правила разделы об ответственности и
      расторжении, где этой суммы никогда и не было.

    Правило простое: если сумма или срок стояли в разделе до правки, они
    обязаны остаться в нём и после. Раздел, которого правка не касалась,
    ни о чём не спрашивается.
    """
    form_numbers = []
    for pair in form_pairs or []:
        value = pair.split(": ", 1)[1] if ": " in pair else pair
        for n in _significant_numbers(value):
            if n not in form_numbers:
                form_numbers.append(n)

    lost_facts = []
    for key, new_text in (edited or {}).items():
        original = (originals or {}).get(key, "")
        for n in _significant_numbers(original):
            if n in form_numbers and n not in lost_facts and not _number_present(n, new_text):
                lost_facts.append(n)

    whole = chr(10).join((edited or {}).values())
    unapplied = [n for n in _criteria_numbers(criteria) if not _number_present(n, whole)]
    return lost_facts, unapplied


def _number_present(number, text):
    """Есть ли число в тексте как самостоятельное значение.

    Поиск подстрокой не годится: «12» находится внутри «120000», и пропущенный
    критерий «гарантия 12 месяцев» считался бы выполненным только потому, что
    рядом стоит сумма договора. Проверяются границы числа и запись с
    разделителем разрядов, которую модель ставит по своему усмотрению.
    """
    variants = [number]
    if number.isdigit() and len(number) > 3:
        variants.append(_grouped(number))
    # Модель пишет десятичную дробь с точкой или запятой независимо от записи в требовании: «0,1» и «0.1» — одно число.
    if re.fullmatch(r"\d+[.,]\d+", number):
        variants.append(number.replace(",", "#").replace(".", ",").replace("#", "."))
    for variant in variants:
        pattern = r"(?<![\d,.])%s(?![\d])" % re.escape(variant)
        if re.search(pattern, text or ""):
            return True
    return False


def _grouped(number):
    """«120000» -> «120 000»: модель часто ставит разделитель разрядов."""
    digits = number[::-1]
    return " ".join(digits[i:i + 3] for i in range(0, len(digits), 3))[::-1]


def _criteria_numbers(criteria):
    numbers = []
    for item in criteria or []:
        for n in _significant_numbers(str(item)):
            if n not in numbers:
                numbers.append(n)
    return numbers


def stream_contract_draft(payload):
    """Правит разделы договора, собранного конструктором, с опорой на кодексы РТ.

    Работает в два прохода: сначала короткий вопрос «какие разделы менять»,
    затем правка только выбранных. Модель не пишет документ с нуля — номер,
    даты и стороны собираются из формы детерминированно, и выдумать там нечего.

    После правки результат проверяется: числа исходного раздела обязаны
    сохраниться, а числа из критериев — появиться. Не сошлось — один повтор по
    конкретному разделу с ужесточённым запросом. Раздел, не прошедший и повтор,
    отдаётся как есть, но пользователь получает об этом явную пометку: тихо
    подсунуть договор с потерянной суммой хуже, чем признать неудачу.

    Поля приходят с фронта уже подписанными и переведёнными: бэкенд не знает
    ни идентификаторов формы, ни локали интерфейса и просто печатает пары
    «подпись: значение». Так эндпоинт не приходится править каждый раз, когда
    в конструктор добавляют поле.
    """
    template = (payload.get("template") or "Договор").strip()
    role_a = (payload.get("roleA") or "Сторона 1").strip()
    role_b = (payload.get("roleB") or "Сторона 2").strip()
    model = payload.get("model") or CONTRACT_DRAFT_MODEL
    criteria = payload.get("criteria") or []

    form_pairs = []
    for item in payload.get("form") or []:
        label = str(item.get("label") or "").strip()
        value = str(item.get("value") or "").strip()
        if label and value:
            form_pairs.append("%s: %s" % (label, value))

    lang = lang_service.detect_lang(" ".join(str(c) for c in criteria), default=payload.get("lang"))

    query_parts = [template, str(payload.get("subject") or "")] + [str(c) for c in criteria]
    search_query = " ".join(part for part in query_parts if part.strip())
    if lang == "tg":
        translated = lang_service.translator.translate_text(search_query, "tg", "ru")
        if translated:
            search_query = lang_service.normalize_search_query(translated)

    rag_results = retrieve_rag(
        search_query, top_k=CONTRACT_DRAFT_TOP_K, index=load_chat_rag_index()
    )
    rag_context = format_rag_context(rag_results, max_chars=CONTRACT_DRAFT_RAG_CHARS)

    # Ключ раздела — латиница (payment, liability): его модель переносит в ответ
    # заметно надёжнее русского слова. На маркере «КРИТЕРИЙ» она уже спотыкалась
    # и писала «КРИТИРИЙ», а здесь по ключу выполняется подстановка в документ.
    blocks = []
    known_keys = []
    originals = {}
    for item in payload.get("blocks") or []:
        key = str(item.get("key") or "").strip()
        text = str(item.get("text") or "").strip()
        if not key or not text:
            continue
        known_keys.append(key)
        originals[key] = text
        blocks.append("### %s (%s)\n%s" % (key, item.get("label") or key, text))

    # Общий префикс обоих проходов. Порядок не случаен: сначала то, что не
    # меняется между запусками (тип договора, форма, фрагменты кодексов),
    # затем разделы и требования. Так KV-кэш Ollama переиспользуется по
    # максимальной длине совпадения, а не обнуляется на первом же отличии.
    prefix = f"""Ты дорабатываешь договор, собранный конструктором.

ТИП ДОГОВОРА: {template}
СТОРОНЫ: {role_a} и {role_b}
ПРЕДМЕТ: {payload.get("subject") or "определить по типу договора"}

ДАННЫЕ ФОРМЫ (использовать дословно, ничего не выдумывая):
{_draft_lines(form_pairs, "- данные не заполнены")}

ФРАГМЕНТЫ КОДЕКСОВ РЕСПУБЛИКИ ТАДЖИКИСТАН:
{rag_context if rag_context else "RAG-контекст не найден: формулируй условия без ссылок на статьи."}

ТЕКУЩИЕ РАЗДЕЛЫ ДОГОВОРА:
{chr(10).join(blocks) if blocks else "- разделов нет, договор пустой"}

ТРЕБОВАНИЯ ПОЛЬЗОВАТЕЛЯ:
{_draft_lines(criteria, "- дополнительных требований нет, усиль слабые места договора")}
"""

    def _edit_prompt(keys, strict_note=""):
        return prefix + f"""
ЗАДАЧА ЭТОГО ШАГА: переписать ТОЛЬКО эти разделы: {", ".join(keys)}.
Остальные разделы не трогай и не повторяй.

ОБЯЗАТЕЛЬНО отрази в тексте каждое требование:
{_draft_lines(criteria, "- особых требований нет, усиль слабые места")}
{strict_note}
Формат ответа. Каждый раздел начинай строкой из трёх решёток и ключа латиницей,
дальше с новой строки полный новый текст раздела:

### {keys[0]}
1. Название раздела
1.1. ...
1.2. ...

Пиши плотно: каждый пункт — одно предложение, без вводных слов и повторов.
Число пунктов диктуется требованиями: сначала внеси ВСЕ требования
пользователя, и только потом, если необходимо, добавь недостающее по закону.
Выбросить требование ради краткости нельзя — это главная ошибка.
Все суммы, сроки и реквизиты бери из данных формы дословно.
Заголовок договора, номер, даты, город и стороны не трогай.
Никаких вступлений, пояснений и перечня изменений.
"""

    def _stream_edit(keys, strict_note=""):
        """Поток правки выбранных разделов. Отдаёт куски ответа модели."""
        budget = min(CONTRACT_DRAFT_MAX_TOKENS, max(320, 360 * len(keys)))
        for obj in llm_stream(model, _edit_prompt(keys, strict_note), temperature=0.12,
                               num_predict=budget, top_p=0.85, think=False,
                               keep_alive=CHAT_KEEP_ALIVE, timeout=1800,
                               openai_model=DRAFT_GPT_MODEL):
            if obj.get("response"):
                obj["response"] = normalize_legal_terms(obj["response"])
            yield obj

    def _parse_sections(text):
        marks = list(SECTION_MARK_RE.finditer(text or ""))
        result = {}
        for i, mark in enumerate(marks):
            stop = marks[i + 1].start() if i + 1 < len(marks) else len(text)
            body = text[mark.end():stop].strip()
            if body:
                result[mark.group(1)] = body
        return result

    def _pipeline():
        """Оркестрация: выбор → правка → проверка → повтор. Отдаёт куски Ollama."""
        if not known_keys:
            yield {"response": "", "done": True}
            return

        yield {"stage": "selecting", "done": False}
        keys = _select_sections(model, prefix, known_keys)

        # Без критериев задача открытая («усиль слабые места»), и модель берётся
        # сразу за два раздела — на замере это самый медленный путь, 167 секунд.
        # Пользователь, не задавший ни одного требования, ждёт скорее показа
        # возможностей, чем полной переработки: одного раздела для этого хватает,
        # а повторный запуск ему ничего не стоит.
        if not criteria:
            keys = keys[:1]

        yield {"stage": "editing", "sections": keys, "done": False}
        collected = ""
        final_obj = None
        for obj in _stream_edit(keys):
            if obj.get("response"):
                collected += obj["response"]
            if obj.get("done"):
                final_obj = obj
            yield obj if not obj.get("done") else {"response": "", "done": False}

        # Проверка по фактам из формы и критериев. Повтор запускается только
        # если действительно потерялась сумма или срок: нумерация пунктов
        # закономерно меняется при переносе раздела и поводом не является.
        edited = _parse_sections(collected)
        lost_facts, missing_criteria = _verify_draft(form_pairs, criteria, originals, edited)

        if (lost_facts or missing_criteria) and edited:
            print("  [DRAFT] повтор: потеряно из формы %s, не применено %s"
                  % (lost_facts or "-", missing_criteria or "-"))
            retry_keys = [k for k in keys if k in edited] or list(edited.keys())
            yield {"stage": "retrying", "sections": retry_keys, "done": False}
            parts = []
            if lost_facts:
                parts.append("из текста пропали данные формы, они обязаны "
                             "присутствовать дословно: %s" % ", ".join(lost_facts[:12]))
            if missing_criteria:
                parts.append("не отражены требования пользователя, содержащие числа: "
                             "%s. Каждое требование обязано попасть в текст отдельным "
                             "пунктом" % ", ".join(missing_criteria[:12]))
            note = "ВАЖНО: в прошлый раз " + "; ".join(parts) + ".\n"
            for obj in _stream_edit(retry_keys, note):
                if not obj.get("done"):
                    collected += obj.get("response") or ""
                    yield obj
            edited = _parse_sections(collected)
            lost_facts, missing_criteria = _verify_draft(form_pairs, criteria, originals, edited)

        # Итог отдаётся в последнем куске: фронт покажет пометку, если часть
        # требований не удалось применить. Молчать об этом нельзя.
        done = dict(final_obj or {})
        done["done"] = True
        done["response"] = ""
        done["edited_sections"] = list(edited.keys())
        done["unapplied_criteria"] = missing_criteria
        done["lost_form_values"] = lost_facts
        yield done

    try:
        if lang == "tg" and not _tajik_translation_available():
            yield json.dumps({
                "response": "_Тарҷумон дастрас нест — матн бо забони русӣ._\n"
                            "_(Переводчик недоступен, текст на русском.)_\n\n",
                "done": False,
            }, ensure_ascii=False) + "\n"
            lang = "ru"

        if lang == "ru":
            for obj in _pipeline():
                yield json.dumps(obj, ensure_ascii=False) + "\n"
        else:
            for chunk in _translate_chat_stream(_pipeline(), "ru", lang):
                yield chunk
    except LLMBackendError as error:
        yield json.dumps({
            "response": str(error),
            "done": True,
        }, ensure_ascii=False) + "\n"
    except Exception as error:  # noqa: BLE001
        yield json.dumps({
            "response": "⚠️ Ошибка генерации договора: %s" % error,
            "done": True,
        }, ensure_ascii=False) + "\n"


@app.route("/api/contract/draft", methods=["POST"])
def contract_draft():
    payload = request.get_json(silent=True) or {}
    if not (payload.get("template") or "").strip():
        return jsonify({"error": "Template is required"}), 400
    return Response(
        stream_with_context(stream_contract_draft(payload)),
        mimetype="application/x-ndjson",
        headers={
            "Cache-Control": "no-cache, no-store, must-revalidate",
            "X-Accel-Buffering": "no",
            "Access-Control-Allow-Origin": "*",
        }
    )

# ================= ГЕНЕРАТОР ДОГОВОРОВ (/contract/generate) =================
GENERATE_MAX_CHARS = 12000


@app.route("/api/generate/chat", methods=["POST"])
def generate_chat():
    """Один запрос модели для страницы-генератора (раньше страница ходила в Ollama прямо из браузера).

    Через сервер модель одна на весь сайт и выбирается провайдером из .env: облачная gpt-6-luna
    пишет по-таджикски и по-русски заметно лучше локальной llama3.1:8b, а пользователю не нужно
    запускать Ollama с OLLAMA_ORIGINS. Язык ответа задаёт сама страница в промпте (по языку описания).
    """
    payload = request.get_json(silent=True) or {}
    system = str(payload.get("system") or "").strip()
    user = str(payload.get("user") or "").strip()
    if not (system or user):
        return jsonify({"error": "system or user is required"}), 400
    prompt = (system + "\n\n" + user).strip()[:GENERATE_MAX_CHARS]
    try:
        text = llm_once(
            CONTRACT_DRAFT_MODEL, prompt, 3000, temperature=0.15, timeout=600,
            json_mode=bool(payload.get("json")), openai_model=DRAFT_GPT_MODEL, reasoning_effort="minimal",
        )
    except LLMBackendError as error:
        return jsonify({"error": str(error)}), 503
    except Exception as error:  # noqa: BLE001
        return jsonify({"error": "Ошибка генерации: %s" % error}), 500
    return jsonify({"ok": True, "text": (text or "").strip()})


# ================= СРАВНЕНИЕ ДВУХ ДОГОВОРОВ (/contract/compare) =================
COMPARE_MAX_CHARS = 6000
COMPARE_MAX_TOKENS = 3000


def _compare_prompts(name_a, name_b, text_a, text_b):
    """Системный и пользовательский промпт для ИИ-сравнения двух договоров.

    Перенесено из JS (compare.blade.php звал Ollama напрямую из браузера,
    минуя этот сервис и настроенный LLM_PROVIDER) — структура и формулировки
    сохранены, чтобы не потерять качество уже отлаженного промпта.
    """
    def _trunc(text):
        text = text or ""
        if len(text) <= COMPARE_MAX_CHARS:
            return text
        return text[:COMPARE_MAX_CHARS] + "\n...(текст обрезан)"

    system_prompt = """# РОЛЬ
Ты — старший партнёр юридической фирмы Республики Таджикистан с 20-летним опытом в договорном и корпоративном праве. Ты специализируешься на сравнительном правовом анализе договоров и выявлении юридических рисков.
# ТВОЯ ЭКСПЕРТИЗА
- Гражданский кодекс Республики Таджикистан (части 1, 2, 3)
- Закон РТ «Об обществах с ограниченной ответственностью»
- Закон РТ «Об акционерных обществах»
- Закон РТ «О защите прав потребителей»
- Закон РТ «О контрактной системе закупок»
- Практика арбитражных судов РТ
# ПРИНЦИПЫ АНАЛИЗА
1. Точность: анализируй только реальные различия из текстов, не выдумывай
2. Конкретность: приводи точные цитаты из обоих документов в полях inDocA/inDocB
3. Практичность: рекомендации должны быть применимы в реальных переговорах
4. Сдержанность: если не уверен в ссылке на статью — не ссылайся, лучше пропусти
5. Приоритизация: critical > warning > info по юридической значимости
# ФОРМАТ ОТВЕТА
Верни ТОЛЬКО валидный JSON-объект. Никаких пояснений, markdown-обёрток, комментариев.
Массив "recommendations" ОБЯЗАТЕЛЬНО должен содержать минимум 3 элемента.
Язык ответа: русский."""

    user_prompt = f"""# ЗАДАЧА
Проведи сравнительный правовой анализ двух договоров. Выяви все юридически значимые различия и дай профессиональные рекомендации.
# ДОКУМЕНТЫ
## ДОГОВОР A (эталон): "{name_a}"
```
{_trunc(text_a)}
```
## ДОГОВОР B (сравниваемый): "{name_b}"
```
{_trunc(text_b)}
```
# КАТЕГОРИИ АНАЛИЗА (в порядке юридической значимости)
1. ФИНАНСОВЫЕ УСЛОВИЯ — сумма, валюта, НДС, порядок оплаты, предоплата, индексация
2. СРОКИ И ДАТЫ — даты начала/окончания, сроки исполнения, пролонгация, расторжение
3. ОТВЕТСТВЕННОСТЬ СТОРОН — штрафы, пени, неустойки, лимиты ответственности
4. ПРАВА И ОБЯЗАННОСТИ — объём и характер обязательств, порядок приёмки
5. КОНФИДЕНЦИАЛЬНОСТЬ И IP — режим NDA, права на интеллектуальную собственность
6. ПОДСУДНОСТЬ И ПРИМЕНИМОЕ ПРАВО — арбитраж, суд, применимое законодательство
7. ФОРС-МАЖОР — перечень обстоятельств, порядок уведомления, сроки
8. РАСТОРЖЕНИЕ — основания и порядок прекращения договора
9. ГАРАНТИИ И ЗАВЕРЕНИЯ — representations & warranties сторон
10. ПРОЧИЕ РАЗЛИЧИЯ — остальные значимые изменения
# УРОВНИ ВАЖНОСТИ
- "critical" — юридический конфликт, делающий договоры несовместимыми; требует немедленного устранения
- "warning" — потенциальный риск; требует внимания и обсуждения с контрагентом
- "info" — информационное различие, не создающее существенных рисков
# ПРИМЕР ВЫВОДА (СТРОГО ЭТОТ ФОРМАТ)
{{
"summary": "Документы имеют 5 существенных различий, из них 2 критических в финансовых условиях и сроках. Совместимость — 72%. Требуется согласование перед подписанием.",
"compatibilityScore": 72,
"compatibilityLabel": "Удовлетворительная",
"findings": [
{{
"id": 1,
"category": "ФИНАНСОВЫЕ УСЛОВИЯ",
"severity": "critical",
"title": "Различие в сумме договора",
"inDocA": "Общая сумма договора составляет 500 000 (пятьсот тысяч) сомони, включая НДС 20%",
"inDocB": "Цена договора — 450 000 сомони без НДС",
"legalRisk": "Несогласованность цены и режима НДС создаёт риск спора об оплате и налоговых последствий для обеих сторон",
"recommendation": "Согласовать единую сумму и режим НДС. Рекомендуется использовать формулировку 'включая НДС 20%' согласно ст. 184 НК РТ"
}}
],
"recommendations": [
{{"priority": "high", "action": "Согласовать финансовые условия (сумма, валюта, НДС)", "rationale": "Финансовые условия — существенное условие договора (ст. 424 ГК РТ). Без их согласования договор может быть признан незаключённым"}},
{{"priority": "medium", "action": "Сверить сроки исполнения и порядок приёмки", "rationale": "Несоответствие сроков может привести к просрочке и начислению неустойки"}},
{{"priority": "low", "action": "Составить протокол разногласий к договору", "rationale": "Протокол зафиксирует согласованные позиции и защитит интересы обеих сторон"}}
],
"overallConclusion": "Документы требуют дополнительного согласования по 2 критическим пунктам. После устранения разногласий договор может быть подписан."
}}
# ТРЕБОВАНИЯ К ВЫВОДУ
1. summary — 2-3 предложения на русском языке с ключевыми выводами
2. compatibilityScore — число 0-100 (100 = идентичны)
3. compatibilityLabel — одно из: "Отличная" (>=95), "Хорошая" (85-94), "Удовлетворительная" (70-84), "Низкая" (50-69), "Критическая" (<50)
4. findings — массив всех выявленных различий (каждое с полной структурой)
5. recommendations — массив минимум из 3 рекомендаций с приоритетами
6. overallConclusion — итоговое заключение: можно ли подписывать после устранения разногласий
# ВАЖНО
- Анализируй ТОЛЬКО реальные различия из текстов
- Давай точные цитаты из обоих документов
- Severity распределяй по юридической значимости, а не по количеству
- Если различий мало — честно об этом напиши
- Верни ТОЛЬКО JSON
JSON:"""
    return system_prompt, user_prompt


def _compare_parse_json(content):
    if not content:
        return None
    content = content.strip()
    try:
        return json.loads(content)
    except json.JSONDecodeError:
        pass
    fence = re.search(r"```(?:json)?\s*([\s\S]*?)```", content)
    if fence:
        try:
            return json.loads(fence.group(1).strip())
        except json.JSONDecodeError:
            pass
    start, end = content.find("{"), content.rfind("}")
    if start != -1 and end > start:
        try:
            return json.loads(content[start:end + 1])
        except json.JSONDecodeError:
            pass
    return None


TAJIK_COMPARE_RULE = """# ЯЗЫК ОТВЕТА: ТАДЖИКСКИЙ
Один из договоров написан по-таджикски, поэтому текстовые поля ответа пиши на таджикском языке (кириллица): summary, title, legalRisk, recommendation, action, rationale, overallConclusion.
Цитаты inDocA и inDocB приводи дословно, на языке самого договора. Ключи JSON, а также значения severity, priority, category и compatibilityLabel оставь ровно такими, как в образце выше (не переводи их).
"""


@app.route("/api/contract/compare", methods=["POST"])
def contract_compare():
    payload = request.get_json(silent=True) or {}
    name_a = (payload.get("nameA") or "Договор A").strip()
    name_b = (payload.get("nameB") or "Договор B").strip()
    text_a = (payload.get("textA") or "").strip()
    text_b = (payload.get("textB") or "").strip()
    if not text_a or not text_b:
        return jsonify({"error": "textA and textB are required"}), 400

    system_prompt, user_prompt = _compare_prompts(name_a, name_b, text_a, text_b)
    combined_prompt = system_prompt + "\n\n" + user_prompt

    # Язык отчёта следует за языком документов: если хотя бы один договор таджикский,
    # текстовые поля отвечаются по-таджикски. Служебные значения (severity, priority,
    # category, compatibilityLabel) остаются как в образце — по ним страница красит карточки.
    tajik = doc_lang.is_tajik_text(text_a) or doc_lang.is_tajik_text(text_b)
    extra = {}
    if tajik:
        combined_prompt = combined_prompt.replace(
            "JSON:", TAJIK_COMPARE_RULE + "JSON:") if combined_prompt.endswith("JSON:") else combined_prompt + "\n" + TAJIK_COMPARE_RULE
        extra = {"openai_model": DRAFT_GPT_MODEL, "reasoning_effort": "minimal"}

    try:
        raw = llm_once(
            CONTRACT_DRAFT_MODEL, combined_prompt,
            num_predict=COMPARE_MAX_TOKENS, temperature=0.1, timeout=240 if tajik else 180, json_mode=True,
            **extra,
        )
    except LLMBackendError as error:
        return jsonify({"error": str(error)}), 503

    result = _compare_parse_json(raw)
    if not isinstance(result, dict):
        return jsonify({"error": "Модель вернула невалидный JSON", "raw": raw[:500]}), 502

    return jsonify({"ok": True, "result": result})


# ================= ЭНДПОИНТЫ =================
def _contract_risk_map(contract_text, risk_metrics, report_metrics, source_lang):
    """Карта рисков для интерфейса; ошибка карты не должна ронять заключение."""
    try:
        shown = (report_metrics or {}).get("findings") if report_metrics else None
        result = risk_map.build_risk_map(contract_text, risk_metrics.get("findings"), display=shown)
        for item in result["findings"]:
            # Норма уже прошла сверку с RAG при анализе: «есть в кодексе» здесь
            # значит «подтверждена», поэтому показываем её зелёной.
            cites = _chat_citations(item["norm"], []) if item["norm"] else []
            for cite in cites:
                if cite["status"] == chat_modes.CITE_EXISTS:
                    cite["status"] = chat_modes.CITE_VERIFIED
            item["citations"] = cites
        result["lang"] = source_lang
        return result
    except Exception as e:  # noqa: BLE001
        print("risk_map: %s" % e)
        return None


@app.route("/api/clause/rewrite", methods=["POST"])
def clause_rewrite():
    """Безопасная формулировка пункта договора, из-за которого найден риск."""
    data = request.get_json(silent=True) or {}
    clause = str(data.get("clause") or "").strip()[:2500]
    if len(clause) < 15:
        return jsonify({"error": "Нужен текст пункта"}), 400
    title = str(data.get("title") or "").strip()[:300]
    why = str(data.get("why") or "").strip()[:900]
    fix = str(data.get("fix") or "").strip()[:900]
    norm = str(data.get("norm") or "").strip()[:400]
    lang = "tg" if str(data.get("lang") or "") == "tg" else "ru"
    prompt = f"""Ты — юрист по законодательству Республики Таджикистан. Перепиши пункт договора так,
чтобы устранить указанный риск.

ПУНКТ ДОГОВОРА:
{clause}

РИСК: {title}
ПОЧЕМУ ЭТО РИСК: {why or "не указано"}
НОРМА: {norm or "не указана"}
КАК ИСПРАВИТЬ: {fix or "на твоё усмотрение"}

Правила:
- Верни только текст исправленного пункта, без пояснений, заголовков и кавычек вокруг.
- Сохрани нумерацию, стороны и смысл, не относящийся к риску; меняй только то, что нужно.
- Используй только право Республики Таджикистан; не выдумывай номера статей и законов.
- Если в пункте не хватает данных (сумма, срок), оставь понятное место в скобках, например [срок].
"""
    try:
        text = llm_once(MODEL_NAME, prompt, 700, temperature=0.2, timeout=120,
                        openai_model=ANALYSIS_GPT_MODEL, reasoning_effort="minimal")
    except Exception as e:  # noqa: BLE001
        print("clause_rewrite: %s" % e)
        return jsonify({"error": "Модель недоступна, повторите попытку"}), 503
    text = (text or "").strip().strip("«»\"")
    if not text:
        return jsonify({"error": "Модель не вернула текст"}), 502
    if lang == "tg":
        try:
            text = doc_lang.translate_text(text, "ru", "tg", _analysis_llm, kind="пункт договора")
        except Exception as e:  # noqa: BLE001
            print("clause_rewrite translate: %s" % e)
    return jsonify({"text": text, "lang": lang})


@app.route("/api/analyze", methods=["POST"])
def analyze_upload():
    try:
        print("📥 Запрос на загрузку файла")
        if "file" not in request.files:
            return jsonify({"error": "Нет файла в запросе", "hint": "Используйте FormData с полем 'file'"}), 400
        file = request.files["file"]
        if not file.filename or file.filename == "":
            return jsonify({"error": "Пустое имя файла"}), 400
        generate_revised = str(request.form.get("generate_revised", "")).strip().lower() in {"1", "true", "yes", "on"}
        rid = hashlib.md5(f"{file.filename}{time.time()}{os.urandom(4).hex()}".encode()).hexdigest()[:12]
        safe_filename = "".join(c for c in file.filename if c.isalnum() or c in '._-')
        path = os.path.join(TEMP_DIR, f"{rid}_{safe_filename}")
        print(f"💾 Сохранение: {path}")
        file.save(path)
        if not os.path.exists(path):
            return jsonify({"error": "Не удалось сохранить файл"}), 500
        file_size = os.path.getsize(path)
        print(f"✅ Файл сохранён: {os.path.basename(path)} ({file_size} байт, ID: {rid})")
        with sessions_lock:
            active_sessions[rid] = {"file_path": path, "filename": file.filename, "size": file_size, "created_at": time.time(), "status": "ready", "generate_revised": generate_revised}
        return jsonify({"request_id": rid, "status": "ready", "filename": file.filename, "size": file_size})
    except Exception as e:
        error_msg = f"❌ Upload error: {str(e)}\n{traceback.format_exc()}"
        print(error_msg)
        return jsonify({"error": str(e)}), 500

@app.route("/api/stream/<rid>")
def stream_sse(rid):
    print(f"🔍 Запрос стрима для: {rid}")
    fpath = None
    original_filename = None
    generate_revised = False
    with sessions_lock:
        if rid in active_sessions:
            fpath = active_sessions[rid].get("file_path")
            original_filename = active_sessions[rid].get("filename")
            generate_revised = bool(active_sessions[rid].get("generate_revised"))
            active_sessions[rid]["status"] = "streaming"
    if not fpath:
        try:
            for f in os.listdir(TEMP_DIR):
                if f.startswith(rid):
                    fpath = os.path.join(TEMP_DIR, f)
                    print(f"✅ Найден файл: {f}")
                    break
        except Exception as e:
            print(f"❌ Ошибка поиска: {e}")
    if not fpath or not os.path.exists(fpath):
        print(f"❌ Файл не найден для: {rid}")
        def error_stream():
            yield f"data: {json.dumps({'type': 'error', 'error': 'Сессия не найдена или файл удалён', 'request_id': rid, 'timestamp': time.time()}, ensure_ascii=False)}\n\n"
        return Response(stream_with_context(error_stream()), mimetype="text/event-stream", headers={"Cache-Control": "no-cache", "X-Accel-Buffering": "no", "Connection": "keep-alive", "Access-Control-Allow-Origin": "*"})
    
    def event_stream():
        try:
            print(f"📡 Начало стриминга: {rid}")
            for event in generate_sse(fpath, rid, original_filename, generate_revised):
                yield event
                time.sleep(0.01)
        except (GeneratorExit, KeyboardInterrupt):
            print(f"🔌 Клиент отключился: {rid}")
        except Exception as e:
            print(f"❌ Stream error: {e}\n{traceback.format_exc()}")
        finally:
            with sessions_lock:
                if rid in active_sessions:
                    del active_sessions[rid]
    return Response(stream_with_context(event_stream()), mimetype="text/event-stream", headers={"Cache-Control": "no-cache, no-store, must-revalidate, no-transform", "X-Accel-Buffering": "no", "Connection": "keep-alive", "Transfer-Encoding": "chunked", "Access-Control-Allow-Origin": "*", "Access-Control-Allow-Headers": "Cache-Control"})

@app.route("/api/status/<rid>")
def session_status(rid):
    with sessions_lock:
        if rid in active_sessions:
            session = active_sessions[rid]
            return jsonify({"status": session.get("status", "unknown"), "filename": session.get("filename"), "size": session.get("size"), "created_at": session.get("created_at"), "file_exists": os.path.exists(session.get("file_path", ""))})
    for f in os.listdir(TEMP_DIR):
        if f.startswith(rid):
            fpath = os.path.join(TEMP_DIR, f)
            return jsonify({"status": "file_ready", "filename": f, "size": os.path.getsize(fpath), "file_exists": True})
    return jsonify({"status": "not_found", "request_id": rid}), 404

@app.route("/health")
def health():
    ollama_status = "offline"
    ollama_models = []
    if LLM_PROVIDER == "openai":
        ollama_status = "not used (LLM_PROVIDER=openai)"
    else:
        try:
            r = requests.get(OLLAMA_URL.replace("/generate", "/tags"), timeout=5)
            if r.status_code == 200:
                ollama_status = "ok"
                try:
                    data = r.json()
                    ollama_models = [m.get("name", "") for m in data.get("models", [])]
                except:
                    pass
        except Exception as e:
            ollama_status = f"error: {str(e)}"

    openai_status = "not used (LLM_PROVIDER=ollama)"
    if LLM_PROVIDER == "openai":
        openai_status = "configured" if OPENAI_API_KEY else "missing OPENAI_API_KEY"

    ai_ready = (ollama_status == "ok") if LLM_PROVIDER != "openai" else bool(OPENAI_API_KEY)
    temp_files = 0
    temp_size = 0
    if os.path.exists(TEMP_DIR):
        try:
            files = os.listdir(TEMP_DIR)
            temp_files = len(files)
            temp_size = sum(os.path.getsize(os.path.join(TEMP_DIR, f)) for f in files if os.path.isfile(os.path.join(TEMP_DIR, f)))
        except:
            pass
    return jsonify({
        "status": "ok",
        "service": "LegalAI API",
        "version": "3.1.0-complete-lawyer",
        "analysis_version": ANALYSIS_VERSION,
        # Публичный ответ не раскрывает названия моделей, провайдера и адреса сервисов: страницы показывают
        # только нейтральную марку, а детали настройки остаются в .env.
        "model": AI_PUBLIC_NAME,
        "draft_llm": {"model": AI_PUBLIC_NAME, "ready": ai_ready},
        "crime_llm": {"model": AI_PUBLIC_NAME, "ready": ai_ready},
        "temp_dir": {"files": temp_files, "size_bytes": temp_size},
        "rag": rag_status_payload(),
        "active_sessions": len(active_sessions),
        "timestamp": time.time()
    })

@app.route("/api/cleanup", methods=["POST"])
def cleanup():
    try:
        max_age = request.json.get("max_age_seconds", 3600) if request.is_json else 3600
        now = time.time()
        deleted = 0
        with sessions_lock:
            to_delete = [rid for rid, session in active_sessions.items() if now - session.get("created_at", 0) > max_age]
            for rid in to_delete:
                fpath = active_sessions[rid].get("file_path")
                if fpath and os.path.exists(fpath):
                    os.remove(fpath)
                    print(f"🗑️ Удалён старый файл: {os.path.basename(fpath)}")
                del active_sessions[rid]
                deleted += 1
        return jsonify({"status": "ok", "deleted_sessions": deleted, "remaining_sessions": len(active_sessions)})
    except Exception as e:
        return jsonify({"error": str(e)}), 500

# ================= ЗАПУСК =================
if __name__ == "__main__":
    print("=" * 60)
    print("🚀 LegalAI Python API v3.1 (Complete Lawyer Edition)")
    print("=" * 60)
    print(f"🧠 Провайдер: {LLM_PROVIDER}")
    print(f"📦 Модель: {_active_model_tag()}")
    print(f"📁 Temp: {os.path.abspath(TEMP_DIR)}")
    print(f"👷 Workers: {MAX_WORKERS}")
    print(f"📊 Chunk: {CHUNK_SIZE} (overlap: {CHUNK_OVERLAP})")
    print(f"⚖️ Юрисдикция: ТОЛЬКО Республика Таджикистан")
    print("=" * 60)
    print("Запуск на http://0.0.0.0:5000")
    print("Ctrl+C для остановки")
    print("=" * 60)
    if LLM_PROVIDER == "openai":
        print(f"🌐 OpenAI: {OPENAI_BASE_URL} ({OPENAI_MODEL})")
        if not OPENAI_API_KEY:
            print("❌ OPENAI_API_KEY не задан в .env — запросы к модели будут падать")
        else:
            print("✅ OPENAI_API_KEY задан")
    else:
        print(f"🌐 Ollama: {OLLAMA_URL}")
        try:
            print("🔍 Проверка Ollama...")
            r = requests.get(OLLAMA_URL.replace("/generate", "/tags"), timeout=5)
            if r.status_code == 200:
                print("✅ Ollama подключен")
            else:
                print(f"⚠️ Ollama: код {r.status_code}")
        except Exception as e:
            print(f"❌ Ollama недоступен: {e}")
            print("💡 Запустите: ollama serve")
    print()
    app.run(host="0.0.0.0", port=5000, debug=False, threaded=True)
