from flask import Flask, request, Response, jsonify, stream_with_context, send_from_directory
from flask_cors import CORS
import requests
import json
import os
import sys
import hashlib
import time
import threading
import traceback
import re
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

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})

# ================= НАСТРОЙКИ =================
OLLAMA_URL = os.environ.get("OLLAMA_URL", "http://localhost:11434/api/generate")
# The single local legal profile is used by both contract analysis and chat.
MODEL_NAME = os.environ.get("LEGAL_MODEL_NAME", "legal-strong")
CHUNK_SIZE = 8000
CHUNK_OVERLAP = 500
MAX_WORKERS = 1
CACHE_FILE = ".ollama_cache.json"
ANALYSIS_VERSION = "analysis-v16-improved-lawyer"
TEMP_DIR = "tmp_uploads"
RESULTS_DIR = "analysis_results"
PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
ANALYSIS_CACHE_DIR = os.path.join(PROJECT_ROOT, "analysis_cache")
ANALYSIS_CACHE_TTL_SECONDS = int(os.environ.get("ANALYSIS_CACHE_TTL_SECONDS", str(14 * 24 * 60 * 60)))
ANALYSIS_CACHE_MAX_ENTRIES = int(os.environ.get("ANALYSIS_CACHE_MAX_ENTRIES", "200"))
OLLAMA_CACHE_MAX_ENTRIES = int(os.environ.get("OLLAMA_CACHE_MAX_ENTRIES", "1200"))
CONTRACT_RAG_SOURCE_NAME = "1918_ru_1.docx"
CONTRACT_RAG_MIN_CHUNKS = 600
RAG_TOP_K = int(os.environ.get("RAG_TOP_K", "12"))
RAG_SNIPPET_MAX = 700
CHAT_RAG_TOP_K = int(os.environ.get("CHAT_RAG_TOP_K", "5"))
CHAT_RAG_MAX_CHARS = int(os.environ.get("CHAT_RAG_MAX_CHARS", "6500"))
CHAT_MAX_TOKENS = int(os.environ.get("CHAT_MAX_TOKENS", "850"))
CHAT_KEEP_ALIVE = os.environ.get("CHAT_KEEP_ALIVE", "30m")

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
]
RAG_CODE_LABELS = {
    CONTRACT_RAG_SOURCE_NAME: "Гражданский кодекс Республики Таджикистан",
    "574_ru.doc": "Уголовный кодекс Республики Таджикистан",
    "семейный кодекс.docx": "Семейный кодекс Республики Таджикистан",
    "налоговый кодекс.docx": "Налоговый кодекс Республики Таджикистан",
    "трудовой кодекс.docx": "Трудовой кодекс Республики Таджикистан",
    "автомабильный кодекс.docx": "Кодекс автомобильного транспорта Республики Таджикистан",
}
CHAT_RAG_INDEX_FILE = os.path.join(PROJECT_ROOT, "storage", "app", "rag", "laws-rt-chat.index.json")
RAG_CODE_NAME = os.environ.get("RAG_CODE_NAME", "Гражданский кодекс Республики Таджикистан")
LEARNING_DATA_DIR = os.path.join(PROJECT_ROOT, "python", "training_data")
LEARNING_DATA_FILE = os.path.join(LEARNING_DATA_DIR, "legal-approved.jsonl")

_current_rag_source = None
_current_rag_index = None

WRONG_JURISDICTION_PENALTY = 20
HALLUCINATION_PENALTY = 25

os.makedirs(TEMP_DIR, exist_ok=True)
os.makedirs(RESULTS_DIR, exist_ok=True)
os.makedirs(ANALYSIS_CACHE_DIR, exist_ok=True)
os.makedirs(os.path.dirname(RAG_INDEX_FILE), exist_ok=True)
os.makedirs(LEARNING_DATA_DIR, exist_ok=True)

active_sessions = {}
sessions_lock = threading.Lock()
rag_index = None
chat_rag_index = None
rag_lock = threading.Lock()
rag_articles_cache = None
rag_citations_cache = None
multi_rag_citations_cache = {}
rag_embeddings = None
chat_rag_embeddings = None
embedding_provider = legal_rag.EmbeddingProvider()
ollama_cache = None
ollama_cache_lock = threading.Lock()

# ================= КЭШИРОВАНИЕ =================
def load_cache():
    """Load model responses once per process instead of once per LLM request."""
    global ollama_cache
    with ollama_cache_lock:
        if ollama_cache is not None:
            return ollama_cache
        if os.path.exists(CACHE_FILE):
            try:
                with open(CACHE_FILE, "r", encoding="utf-8") as f:
                    ollama_cache = json.load(f)
                    if isinstance(ollama_cache, dict):
                        return ollama_cache
            except Exception as e:
                print(f"⚠️ Ошибка загрузки кэша: {e}")
        ollama_cache = {}
        return ollama_cache

def save_cache(cache):
    """Atomically persist the in-memory cache so parallel requests cannot corrupt it."""
    tmp_path = f"{CACHE_FILE}.{uuid.uuid4().hex}.tmp"
    try:
        with open(tmp_path, "w", encoding="utf-8") as f:
            json.dump(cache, f, ensure_ascii=False, separators=(",", ":"))
        os.replace(tmp_path, CACHE_FILE)
    except Exception as e:
        print(f"⚠️ Ошибка сохранения кэша: {e}")
        if os.path.exists(tmp_path):
            try:
                os.remove(tmp_path)
            except OSError:
                pass

def get_cache_key(text):
    return hashlib.sha256((ANALYSIS_VERSION + MODEL_NAME + text).encode("utf-8")).hexdigest()

def get_analysis_cache_key(contract_text, rag_index_data):
    """Tie a full-report cache entry to content, model and the exact RAG revision."""
    payload = {
        "version": ANALYSIS_VERSION,
        "model": MODEL_NAME,
        "contract": hashlib.sha256((contract_text or "").encode("utf-8")).hexdigest(),
        "rag_source": rag_index_data.get("source_name"),
        "rag_mtime": rag_index_data.get("source_mtime"),
        "rag_chunk_size": rag_index_data.get("chunk_size"),
        "rag_chunk_overlap": rag_index_data.get("chunk_overlap"),
        "top_k": RAG_TOP_K,
    }
    encoded = json.dumps(payload, sort_keys=True, ensure_ascii=False).encode("utf-8")
    return hashlib.sha256(encoded).hexdigest()

def load_analysis_cache(cache_key):
    path = os.path.join(ANALYSIS_CACHE_DIR, f"{cache_key}.json")
    try:
        if not os.path.isfile(path):
            return None
        if time.time() - os.path.getmtime(path) > ANALYSIS_CACHE_TTL_SECONDS:
            os.remove(path)
            return None
        with open(path, "r", encoding="utf-8") as f:
            cached = json.load(f)
        if not isinstance(cached, dict) or not cached.get("content") or not isinstance(cached.get("metrics"), dict):
            return None
        return cached
    except Exception as e:
        print(f"⚠️ Ошибка чтения кэша анализа: {e}")
        return None

def save_analysis_cache(cache_key, payload):
    path = os.path.join(ANALYSIS_CACHE_DIR, f"{cache_key}.json")
    tmp_path = f"{path}.{uuid.uuid4().hex}.tmp"
    try:
        data = {**payload, "cached_at": time.time(), "analysis_version": ANALYSIS_VERSION}
        with open(tmp_path, "w", encoding="utf-8") as f:
            json.dump(data, f, ensure_ascii=False, separators=(",", ":"))
        os.replace(tmp_path, path)
        prune_analysis_cache()
    except Exception as e:
        print(f"⚠️ Ошибка сохранения кэша анализа: {e}")
        if os.path.exists(tmp_path):
            try:
                os.remove(tmp_path)
            except OSError:
                pass

def prune_analysis_cache():
    """Keep report cache bounded and remove expired entries opportunistically."""
    try:
        now = time.time()
        entries = []
        for name in os.listdir(ANALYSIS_CACHE_DIR):
            if not name.endswith(".json"):
                continue
            path = os.path.join(ANALYSIS_CACHE_DIR, name)
            mtime = os.path.getmtime(path)
            if now - mtime > ANALYSIS_CACHE_TTL_SECONDS:
                os.remove(path)
            else:
                entries.append((mtime, path))
        overflow = max(0, len(entries) - max(ANALYSIS_CACHE_MAX_ENTRIES, 1))
        for _, path in sorted(entries)[:overflow]:
            os.remove(path)
    except Exception as e:
        print(f"⚠️ Ошибка очистки кэша анализа: {e}")

# ================= КЛАССИФИКАТОР ТИПА ДОГОВОРА =================
CONTRACT_TYPE_KEYWORDS = {
    "купля-продажа": [
        "купли-продажи", "продавец", "покупатель", "товар", "передаёт в собственность",
        "price", "товара", "количество", "ассортимент", "качество товара",
    ],
    "аренда": [
        "аренды", "арендатор", "арендодатель", "арендную плату", "арендная плата",
        "имущество во временное владение", "объект аренды", "субаренд",
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
        "кредита", "кредитный", "кредитор", "заёмщик", "кредитную", "банк",
        "процентную ставку", "график погашения",
    ],
    "хранение": [
        "хранения", "хранитель", "поклажедатель", "вещь", "сохранность",
    ],
    "поручение": [
        "поручения", "поверенный", "доверитель", "от имени", "юридические действия",
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
        "простого товарищества", "совместной деятельности", "товарищи",
    ],
}

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
        (r"\bМосква\b", "Москва"),
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

# ================= ПРОВЕРКА ОБОСНОВАННОСТИ ВЫВОДОВ =================
def _rag_article_numbers():
    """Return article numbers that are actually present in the active RAG index."""
    index = load_rag_index(force=False)
    numbers = set()
    for chunk in index.get("chunks", []):
        text = chunk.get("text", "")
        for match in re.finditer(r"(?:статья|ст\.)\s*(\d+(?:[-.]\d+)?(?:\.\d+)?)", text, re.IGNORECASE):
            numbers.add(match.group(1))
    return numbers

def _cited_article_numbers(text):
    return set(
        re.findall(
            r"(?:статья|ст\.|ч\.)\s*(\d+(?:[-.]\d+)?(?:\.\d+)?)",
            text or "",
            re.IGNORECASE,
        )
    )

def detect_hallucination(finding, contract_text, rag_context=""):
    """Return a reason when a finding cannot be verified by the contract and RAG."""
    quote = _normalize_text(finding.get("fragment", ""))
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
            cacheable = dict(index)
            with open(CHAT_RAG_INDEX_FILE, "w", encoding="utf-8") as file:
                json.dump(cacheable, file, ensure_ascii=False)
        except Exception as error:
            print(f"Chat RAG cache save error: {error}")
        return chat_rag_index

def rag_status_payload():
    index = load_rag_index(force=False)
    rag_dir = os.path.dirname(RAG_SOURCE_PATH)
    chat_sources = [name for name in CHAT_RAG_SOURCE_NAMES if os.path.isfile(os.path.join(rag_dir, name))]
    chunks = index.get("chunks", [])
    return {
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
            "model": legal_rag.EMBED_MODEL,
            "reason": embedding_provider.reason or None,
        },
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
def retrieve_rag(query, top_k=None, index=None):
    """Гибридный поиск: BM25 по статье и названию + опциональные эмбеддинги."""
    index = index or load_rag_index(force=False)
    if not index.get("ready"):
        return []
    store = index.get("embeddings")
    return legal_rag.search(
        index,
        query,
        top_k=top_k or RAG_TOP_K,
        store=store,
        provider=embedding_provider if store else None,
    )

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
    return format_rag_context(retrieve_rag(query, top_k=top_k))

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

# ================= ЗАПРОС К OLLAMA =================
def send_to_ollama(prompt, stream_callback=None, num_predict=2048, temperature=0.5):
    cache = load_cache()
    key = get_cache_key(prompt)

    with ollama_cache_lock:
        cached = cache.get(key)
    if cached:
        if stream_callback:
            # Cached data is already complete: forwarding it in one call avoids
            # an artificial per-character delay on repeat analyses.
            stream_callback(cached)
        return cached

    response_text = ""
    data = {
        "model": MODEL_NAME,
        "prompt": prompt,
        "stream": True,
        "options": {
            "temperature": temperature,
            "num_predict": num_predict,
            "top_p": 0.9
        }
    }

    try:
        with requests.post(OLLAMA_URL, json=data, stream=True, timeout=1500) as r:
            r.raise_for_status()
            # The default 512-byte buffer postpones the visible first token.
            # A one-byte chunk keeps browser streaming responsive on slow CPUs.
            for line in r.iter_lines(chunk_size=1):
                if line:
                    try:
                        obj = json.loads(line.decode("utf-8"))
                        chunk = obj.get("response", "")
                        if chunk:
                            response_text += chunk
                            if stream_callback:
                                stream_callback(chunk)
                    except json.JSONDecodeError:
                        continue

        if response_text:
            with ollama_cache_lock:
                cache[key] = response_text
                while len(cache) > max(OLLAMA_CACHE_MAX_ENTRIES, 1):
                    cache.pop(next(iter(cache)))
                save_cache(cache)

    except requests.exceptions.ConnectionError:
        error_msg = "⚠️ Не удалось подключиться к Ollama. Убедитесь, что сервер запущен: ollama serve"
        if stream_callback:
            stream_callback(error_msg)
        return error_msg
    except Exception as e:
        error_msg = f"⚠️ Ошибка Ollama: {str(e)}"
        if stream_callback:
            stream_callback(error_msg)
        return error_msg

    return response_text

def _build_contract_type_guidance(contract_type):
    """Return a string of key points to check for a specific contract type."""
    guidance = {
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
def analyze_chunk(args):
    i, total, chunk, full_text, contract_type_info = args
    result = []

    def collect(text):
        result.append(text)

    legal_context = rag_context_for_query(chunk, top_k=RAG_TOP_K)
    legal_context_block = (
        f"\n\nРелевантные фрагменты кодексов Республики Таджикистан (RAG):\n{legal_context}\n"
        if legal_context else
        "\n\nRAG-контекст по кодексам Республики Таджикистан не найден или база еще не подключена.\n"
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

Проанализируй этот фрагмент и НАЙДИ ВСЕ юридически значимые риски. 
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
    send_to_ollama(prompt, stream_callback=collect, num_predict=2400, temperature=0.15)
    return {"idx": i, "content": normalize_legal_terms("".join(result))}

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
                # Вычисляем качество каждого риска
                risk_quality = _calculate_finding_quality(risk)
                existing_quality = _calculate_finding_quality(existing)
                
                # Если новый риск качественнее, заменяем
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
        text = " ".join(str(finding.get(k, "") or "") for k in ("title", "fragment", "why", "fix", "norm")).lower()
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
        reason = detect_hallucination(finding, contract_text, rag_context)
        if reason == "цитата не найдена в тексте договора":
            print(f"  [RAG EVIDENCE] Removed ungrounded finding: {finding.get('title', 'N/A')[:60]} ({reason})")
            continue
        finding["_rag_evidence"] = True
        if reason in ("не указана конкретная статья РТ", "указанная статья отсутствует в подключённом RAG"):
            finding["norm"] = "Статья не найдена в подключённой базе (требуется ручная проверка)"
            finding["_norm_verified"] = False
        else:
            # Replace model wording with the exact code/chapter/article parsed
            # from the primary RAG document.
            exact_norm = canonical_rag_norm(_cited_article_numbers(finding.get("norm", "")))
            if exact_norm:
                finding["norm"] = exact_norm
                finding["_norm_verified"] = True
        verified.append(finding)
    return verified

def _calculate_severity_score(severity, quality):
    """Return the exposure of one verified finding on a 0-100 scale."""
    base_scores = {
        # These values represent the maximum impact of one *verified* issue.
        # Multiple issues are combined below as probabilities, not added.
        'high': 50,
        'medium': 30,
        'low': 8,
    }

    base = base_scores.get(severity, 8)
    # Evidence quality affects confidence in the finding but cannot inflate a
    # weakly documented item above its severity band.
    quality_factor = max(0.75, min(1.0, 0.75 + 0.25 * float(quality or 0)))
    return base * quality_factor

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
        ],
        "severity": "high",
    },
    "цена и порядок расчётов": {
        "patterns": [
            r"цен\w*\s+договор",
            r"стоимост",
            r"оплат\w*\s+договор",
            r"порядок\s+расчёт",
            r"аванс",
            r"цена\s+договор",
            r"общая\s+стоимост",
        ],
        "severity": "high",
    },
    "срок исполнения/действия": {
        "patterns": [
            r"срок\w*\s+(?:исполнени|выполнени|оказани|поставки|действи)",
            r"период\s+поставки",
            r"начало\s+работ",
            r"окончани\w*\s+работ",
            r"срок\s+действия\s+договор",
            r"срок\s+исполнени\w*\s+обязан",
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
        "patterns": [
            r"неустойк\w*\s+за\s+просрочк",
            r"пени\s+за\s+просрочк",
            r"штраф\s+за\s+нарушен",
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
        ],
        "severity": "low",
    },
}

def check_essential_conditions(text, contract_type_info=None):
    """Find essential clauses missing from the contract and return them as findings."""
    findings = []
    if not text or len(text.strip()) < 100:
        return findings
    text_lower = text.lower()
    for clause, cfg in ESSENTIAL_CLAUSE_PATTERNS.items():
        found = any(re.search(p, text_lower) for p in cfg["patterns"])
        if found:
            continue
        findings.append({
            "title": f"Отсутствует условие: {clause}",
            "severity": cfg["severity"],
            "point": "весь договор",
            "fragment": "",
            "norm": "Проверьте существенные условия в Гражданском кодексе РТ",
            "why": (
                f"В договоре не обнаружено условие о {clause}. "
                "Отсутствие существенного условия создаёт риск признания договора незаключённым "
                "или неопределённости прав и обязанностей сторон."
            ),
            "fix": f"Добавить в договор раздел, чётко определяющий {clause}",
            "_heuristic": True,
        })
    return findings

def _attach_rag_norm_text(findings):
    """Attach real article text from the connected RAG source to verified norms.

    A verified finding keeps its model-provided norm, but the actual statutory
    text is pulled from the loaded RAG index (the same source of truth used by
    ``detect_hallucination``) and stored in ``norm_text`` so the final report
    quotes the real norm instead of an approximation.
    """
    index = load_rag_index(force=False)
    chunks = index.get("chunks", []) if isinstance(index, dict) else []

    def _best_chunk_for_numbers(numbers, norm_text):
        best = ""
        for num in numbers:
            if not num:
                continue
            pattern = re.compile(
                rf"(?:^|\b|\.\s+)(?:статья|ст\.|ч\.)\s*{re.escape(num)}\b", re.IGNORECASE
            )
            candidates = []
            for chunk in chunks:
                text = chunk.get("text", "")
                if pattern.search(text):
                    candidates.append(text)
            if candidates:
                candidate = max(candidates, key=len)
                if len(candidate) > len(best):
                    best = candidate
        if not best and norm_text:
            norm_lower = norm_text.lower()
            for chunk in chunks:
                text = chunk.get("text", "")
                text_lower = text.lower()
                if any(part in text_lower for part in norm_lower.split() if len(part) > 4):
                    if len(text) > len(best):
                        best = text
        return _shorten_text(best, RAG_SNIPPET_MAX)

    cache = {}
    for finding in findings:
        if finding.get("_norm_verified") is False:
            continue
        numbers = _cited_article_numbers(finding.get("norm", ""))
        norm_text = finding.get("norm", "")
        if not numbers and not norm_text:
            continue
        snippet = cache.get((tuple(numbers), norm_text))
        if snippet is None:
            snippet = _best_chunk_for_numbers(numbers, norm_text)
            cache[(tuple(numbers), norm_text)] = snippet
        if snippet:
            finding["norm_text"] = snippet
    return findings

def estimate_risk_metrics(analysis_text, contract_text="", rag_context="", contract_type_info=None):
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
    
    # Добавляем эвристический чек-лист отсутствующих существенных условий.
    # Для «отсутствующего» условия цитата из договора невозможна, поэтому добавляем после фильтров.
    heuristic_findings = check_essential_conditions(contract_text, contract_type_info)
    findings.extend(heuristic_findings)
    
    # Удаляем дубликаты
    findings = _deduplicate_findings(findings)
    
    # Усиливаем severity по эвристикам (одностороннее расторжение → высокий и т.п.)
    findings = _apply_severity_heuristics(findings)

    # Прикрепляем реальный текст статей из подключённого RAG к подтверждённым нормам.
    findings = _attach_rag_norm_text(findings)

    print(f"  [PARSER] После фильтрации, эвристик и дедупликации: {len(findings)}")
    for f in findings:
        quality = f.get('_quality', 0)
        print(f"    [{f['severity']}] {f['title'][:50]} | quality={quality:.2f} | point={bool(f['point'])} frag={bool(f['fragment'])} norm={bool(f['norm'])}")

    # Подсчет по severity
    high = sum(1 for f in findings if f['severity'] == 'high')
    medium = sum(1 for f in findings if f['severity'] == 'medium')
    low = sum(1 for f in findings if f['severity'] == 'low')

    # Combine independently verified findings as cumulative exposure. This is
    # stable: it never assigns a magic fixed score, never exceeds 100 from a
    # long list, and every added verified finding increases the result.
    remaining_safe_probability = 1.0
    for finding in findings:
        impact = _calculate_severity_score(
            finding.get('severity'), finding.get('_quality', 0.5)
        )
        remaining_safe_probability *= 1.0 - (impact / 100.0)
    score = 100.0 * (1.0 - remaining_safe_probability)
    
    # Штраф за неправильную юрисдикцию
    if wrong_jurisdiction_count > 0:
        score = max(0, score - wrong_jurisdiction_count * WRONG_JURISDICTION_PENALTY)
    
    # 100 is reserved for a human-confirmed critical situation; an automated
    # RAG analysis must not assign it merely because it found many items.
    score = max(0, min(95, round(score)))
    # The level is derived from the same score shown to the user. This prevents
    # contradictory output such as "Высокий риск — 35/100".
    level = "Высокий" if score >= 65 else "Средний" if score >= 25 else "Низкий"
    
    # Длинный договор не может быть «чистым»: если фильтры убрали все риски,
    # отчёт честно предупреждает о необходимости ручной проверки, а не выдаёт
    # ложный вердикт «Можно подписывать».
    contract_len = len((contract_text or "").strip())
    manual_review_required = (len(findings) == 0 and contract_len >= 1000)
    if manual_review_required:
        print("  [PARSER] ⚠️ Длинный договор без подтверждённых рисков — требуется ручная проверка")

    if score >= 65:
        verdict = "❌ Не подписывать"
    elif score >= 25:
        verdict = "⚠️ Требует правок"
    elif manual_review_required:
        verdict = "⚠️ Требуется ручная проверка"
    else:
        verdict = "✅ Можно подписывать"

    # Очищаем внутренние поля
    clean_findings = []
    for f in findings:
        clean_finding = {k: v for k, v in f.items() if not k.startswith('_')}
        clean_findings.append(clean_finding)

    return {
        "score": score,
        "level": level,
        "high": high,
        "medium": medium,
        "low": low,
        "verdict": verdict,
        "findings": clean_findings,
        "hallucinations_detected": 0,
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

def build_grounded_contract_report(metrics, contract_type_info=None):
    """Render the final report from verified records only; no second model pass."""
    findings = metrics.get("findings", [])
    severity_labels = {"high": "Высокий", "medium": "Средний", "low": "Низкий"}
    lines = [
        "## Проверенный RAG-анализ договора",
        "",
        f"Источник права: `{CONTRACT_RAG_SOURCE_NAME}` (Республика Таджикистан).",
        "В отчёт включены пункты с цитатой из договора и нормой, сверенной с подключённым RAG.",
        "Если модель не смогла указать точную статью, это честно помечено как требующее ручной проверки.",
    ]
    # Добавляем тип договора, если определён
    if contract_type_info and contract_type_info.get("type") != "неопределённый":
        lines.append(f"")
        lines.append(f"Определённый тип договора: **{contract_type_info['type']}** (уверенность: {contract_type_info.get('confidence', 0):.0%}).")
        if contract_type_info.get('types_with_scores'):
            alt_types = [t['type'] for t in contract_type_info['types_with_scores'][1:3] if t['score'] > 0.2]
            if alt_types:
                lines.append(f"Альтернативные варианты: {', '.join(alt_types)}.")
    lines.append("")
    lines.append("### Выявленные риски")
    if not findings:
        lines.extend([
            "",
            "Подтверждённых рисков не найдено: для публикации требуется одновременно цитата из договора и применимая статья из RAG.",
        ])
        if metrics.get("manual_review_required"):
            lines.extend([
                "",
                "⚠️ **Требуется ручная проверка.** Договор объёмный, но система не подтвердила ни одного риска. "
                "Это может быть следствием нестандартных формулировок — заключение должен проверить практикующий юрист.",
            ])
    else:
        for number, finding in enumerate(findings, 1):
            lines.extend([
                "",
                f"#### {number}. {finding.get('title') or 'Юридический риск'}",
                f"- Уровень: **{severity_labels.get(finding.get('severity'), 'Низкий')}**",
                f"- Пункт договора: {finding.get('point') or 'не указан в документе'}",
                f"- Цитата из договора: «{finding.get('fragment') or 'условие отсутствует в договоре'}»",
                f"- Норма РТ из RAG: {finding.get('norm') or ''}",
                f"- Основание: {finding.get('why') or ''}",
                f"- Рекомендуемая правка: {finding.get('fix') or ''}",
            ])
            if finding.get("norm_text"):
                lines.extend([
                    "",
                    f"**Текст нормы из подключённого RAG:**",
                    finding["norm_text"],
                ])
    lines.extend([
        "",
        "### Резюме",
        f"- Балл риска: **{metrics.get('score', 0)}/100**",
        f"- Уровень: **{metrics.get('level', 'Низкий')}**",
        f"- Высоких рисков: **{metrics.get('high', 0)}**",
        f"- Средних рисков: **{metrics.get('medium', 0)}**",
        f"- Низких рисков: **{metrics.get('low', 0)}**",
        f"- Вердикт: **{metrics.get('verdict', '')}**",
        "",
        "Отчёт основан на подключённой базе RAG и не заменяет проверку практикующим юристом.",
    ])
    return "\n".join(lines)

def normalize_legal_terms(text):
    text = re.sub(r"\bГК\s*РФ\b", "[УДАЛЕНО: ГК РФ]", text, flags=re.IGNORECASE)
    text = re.sub(r"\bТК\s*РФ\b", "[УДАЛЕНО: ТК РФ]", text, flags=re.IGNORECASE)
    text = re.sub(r"\bНК\s*РФ\b", "[УДАЛЕНО: НК РФ]", text, flags=re.IGNORECASE)
    text = re.sub(r"\bРоссии\b", "[УДАЛЕНО: России]", text, flags=re.IGNORECASE)
    text = re.sub(r"\bРФ\b", "[УДАЛЕНО: РФ]", text, flags=re.IGNORECASE)
    text = re.sub(r"\bРоссийск(?:ая|ой|ую)\s+Федераци\w*", "[УДАЛЕНО: Российская Федерация]", text, flags=re.IGNORECASE)
    text = re.sub(r"(Норма\s+РТ\s*:\s*)кодекс\s*,", r"\1глава,", text, flags=re.IGNORECASE)
    text = re.sub(r"(Норма\s+РТ\s*:\s*)кодекс\s+и\s+статья", r"\1глава и статья", text, flags=re.IGNORECASE)
    return text

# ================= SSE ГЕНЕРАТОР =================
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

        if not full_text or len(full_text.strip()) < 50:
            yield send_event({
                "error": "Текст не извлечён или слишком короткий",
                "length": len(full_text) if full_text else 0
            }, "error")
            return

        print(f"✅ Текст извлечён: {len(full_text)} символов")
        yield send_event({
            "step": "extracted",
            "chars": len(full_text),
            "words": len(full_text.split()),
            "extension": ext,
            "progress_percent": 8
        }, "status")
        time.sleep(0.1)

        # Full-report cache: identical document text + identical RAG revision
        # can return immediately without invoking the model again. Revised
        # contract generation remains live because it creates a new file.
        analysis_cache_key = get_analysis_cache_key(full_text, contract_rag)
        if not generate_revised:
            cached_report = load_analysis_cache(analysis_cache_key)
            if cached_report:
                cached_metrics = cached_report["metrics"]
                yield send_event({
                    "step": "cache_hit",
                    "message": "Найден проверенный кэш: загружаем готовый RAG-отчёт",
                    "progress_percent": 96,
                    "cache_hit": True,
                    "cached_at": cached_report.get("cached_at"),
                }, "status")
                yield send_event({
                    "step": "summary_complete",
                    "content": cached_report["content"],
                    "length": len(cached_report["content"]),
                    "revised_contract": None,
                    "metrics": cached_metrics,
                    "cache_hit": True,
                }, "summary_complete")
                yield send_event({
                    "step": "complete",
                    "status": "success",
                    "chunks_processed": cached_report.get("chunks_processed", 0),
                    "errors_count": 0,
                    "cache_hit": True,
                }, "complete")
                return

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
        
        risk_metrics = estimate_risk_metrics(
            full_analysis_text,
            contract_text=full_text,
            rag_context=rag_summary_context,
            contract_type_info=contract_type_info,
        )
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
                    revised_contract = create_revised_contract_output(
                        "\n\n".join(revised_parts),
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
                }
                if not generate_revised:
                    save_analysis_cache(analysis_cache_key, {
                        "content": final_summary,
                        "metrics": response_metrics,
                        "chunks_processed": len(all_findings),
                        "rag_source": CONTRACT_RAG_SOURCE_NAME,
                    })
                yield send_event({
                    "step": "summary_complete",
                    "content": final_summary,
                    "length": len(final_summary),
                    "revised_contract": revised_contract,
                    "metrics": response_metrics
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
    numbers = data.get("numbers") or ["104", "111", "114", "246", "247", "248", "249", "250"]
    # ugalov-kodex (Уголовный кодекс РТ) page requests criminal articles.
    source = data.get("source") or "574_ru.doc"
    return jsonify({
        "articles": extract_rag_articles(numbers, source_name=source),
        "status": rag_status_payload(),
    })

@app.route("/api/crime/qualify", methods=["POST"])
def crime_qualify():
    data = request.get_json(silent=True) or {}
    crime_description = (data.get("description") or "").strip()
    if not crime_description:
        return jsonify({"error": "crime description is required"}), 400
        # Crime qualification MUST retrieve from the criminal code (Уголовный
# кодекс), which lives in the multi-source chat RAG index — NOT from the
# contract/Civil-Code index, which knows nothing about "кража"/"убийство".
    crime_index = load_chat_rag_index()
    if not crime_index.get("ready"):
        try:
            ensure_contract_rag_ready()
            crime_index = load_rag_index(force=False)
        except Exception:
            crime_index = None
    rag_results = retrieve_rag(crime_description, top_k=8, index=crime_index)
    # Provenance for the UI: unique source names + human labels + articles.
    rag_source_names = sorted({r.get("source") for r in rag_results if r.get("source")})
    rag_source_labels = [RAG_CODE_LABELS.get(s, s) for s in rag_source_names]
    article_numbers = sorted(
        {n for r in rag_results for n in _cited_article_numbers(r.get("text", ""))}
    )
    rag_context = format_rag_context(rag_results, max_chars=CHAT_RAG_MAX_CHARS)
    prompt = f"""Ты — опытный юрист по уголовному праву Республики Таджикистан с 15+ лет опыта.

ЗАДАЧА: Выполнить ПОЛНУЮ уголовно-правовую квалификацию преступления по действующему УК РТ.

ОПИСАНИЕ ПРЕСТУПЛЕНИЯ:
{crime_description}

ПРИМЕНИМЫЕ НОРМЫ УК РТ:
{rag_context}

СТРУКТУРА ОТВЕТА (обязательно):

1. КВАЛИФИКАЦИЯ
   • Основные статьи УК РТ: [номера через запятую]
   • Статьи со смягчающими обстоятельствами: [если есть]
   • Дополнительные статьи (совокупность): [если есть]

2. САНКЦИИ ПО ДЕЙСТВУЮЩЕМУ УК РТ
   • Основное наказание: [вид и размер]
   • Минимум: [в годах/месяцах]
   • Максимум: [в годах/месяцах]
   • Дополнительные наказания: [если предусмотрены]

3. ФАКТЫ И ДОКАЗАТЕЛЬСТВА
   • Элемент состава преступления: [элемент 1]
   • Доказательство из описания: [конкретная цитата]
   • (повторить для каждого элемента)

4. ОБСТОЯТЕЛЬСТВА, ВЛИЯЮЩИЕ НА КВАЛИФИКАЦИЮ
   • Смягчающие: [перечислить]
   • Отягчающие: [перечислить]

5. ПРАВОВОЙ АНАЛИЗ
   [2-3 абзаца объяснения применимых норм]

6. ИТОГОВЫЙ ВЫВОД
   • Квалификация преступления: [полная версия]
   • Ожидаемое наказание: [диапазон с учетом обстоятельств]

ПРАВИЛА:
- ТОЛЬКО законы Республики Таджикистан
- Используй актуальный УК РТ из RAG-контекста
- Если статья не найдена в RAG, напиши "Соответствующее положение УК РТ не найдено"
- Приводи точные номера статей и пункты
- Анализируй все элементы состава преступления"""

    response_text = send_to_ollama(prompt, num_predict=1500, temperature=0.3)
    return jsonify({
        "qualification": response_text,
        "sources": [
            {"name": name, "label": RAG_CODE_LABELS.get(name, name)}
            for name in rag_source_names
        ],
        "primary_source": rag_source_labels[0] if rag_source_labels else None,
        "article_numbers": article_numbers,
        "rag_sources": [
            {
                "number": i + 1,
                "source": item.get("source"),
                "source_label": RAG_CODE_LABELS.get(item.get("source"), item.get("source")),
                "title": item.get("title"),
                "score": item.get("score"),
                "snippet": (item.get("text") or "")[:RAG_SNIPPET_MAX],
            }
            for i, item in enumerate(rag_results)
        ],
        "rag_status": rag_status_payload(),
    })

def stream_chat_response(payload):
    message = (payload.get("message") or "").strip()
    history = payload.get("history") or []
    model = payload.get("model") or MODEL_NAME
    temperature = max(0.0, min(float(payload.get("temperature", 0.2)), 0.3))
    personality = payload.get("personality") or "professional"

    system_prompts = {
        "professional": "Вы профессиональный юрист по законодательству Республики Таджикистан. Отвечайте точно, структурированно и на русском языке. Используйте ТОЛЬКО законы РТ, никогда не упоминайте РФ.",
        "friendly": "Вы дружелюбный юридический помощник по законодательству Республики Таджикистан. Объясняйте простым языком, но юридически аккуратно. Только законы РТ.",
        "concise": "Вы краткий юридический эксперт по законодательству Республики Таджикистан. Только суть, риски и практические шаги. Только законы РТ.",
    }

    rag_results = retrieve_rag(message, top_k=CHAT_RAG_TOP_K, index=load_chat_rag_index())
    rag_context = format_rag_context(rag_results, max_chars=CHAT_RAG_MAX_CHARS)
    sources = [
        f"[{i + 1}] {item.get('source')} / {item.get('title')}"
        for i, item in enumerate(rag_results)
    ]

    recent_history = []
    for item in history[-4:]:
        role = item.get("role")
        content = (item.get("content") or "").strip()
        if role in ["user", "ai"] and content:
            recent_history.append(f"{'Пользователь' if role == 'user' else 'Ассистент'}: {content[:600]}")

    prompt = f"""{system_prompts.get(personality, system_prompts["professional"])}

КРИТИЧЕСКИ ВАЖНО - ЮРИСДИКЦИЯ:
- Используй ТОЛЬКО законы Республики Таджикистан
- ЗАПРЕЩЕНО: "РФ", "Россия", "ГК РФ", "ТК РФ", "НК РФ", "Российская Федерация"
- Разрешено ТОЛЬКО: "РТ", "ГК РТ", "кодекс РТ", "Республика Таджикистан"

Правила RAG:
- Используй фрагменты кодексов РТ ниже как основной справочный материал
- Цитируй главу и статью только из строки «ПРОВЕРЕННАЯ ССЫЛКА RAG»
- Если такой строки нет, напиши: «Точная ссылка не подтверждена RAG»
- Не выдумывай номера статей, главы или название кодекса
- Отвечай по существу: до 6 коротких абзацев или пунктов

Релевантные фрагменты кодексов Республики Таджикистан:
{rag_context if rag_context else "RAG-контекст не найден."}

История диалога:
{chr(10).join(recent_history) if recent_history else "Нет предыдущей истории."}

Вопрос пользователя:
{message}

Доступные источники RAG:
{chr(10).join(sources) if sources else "Нет найденных источников."}
"""

    data = {
        "model": model,
        "prompt": prompt,
        "stream": True,
        "think": False,
        "keep_alive": CHAT_KEEP_ALIVE,
        "options": {
            "temperature": temperature,
            "num_predict": CHAT_MAX_TOKENS,
            "top_p": 0.85,
        },
    }

    try:
        with requests.post(OLLAMA_URL, json=data, stream=True, timeout=600) as r:
            r.raise_for_status()
            # Do not wait for Requests' default 512-byte buffer: chat should
            # render each generated fragment as soon as Ollama sends it.
            for line in r.iter_lines(chunk_size=1):
                if line:
                    decoded = line.decode("utf-8")
                    try:
                        obj = json.loads(decoded)
                        if obj.get("response"):
                            obj["response"] = normalize_legal_terms(obj["response"])
                        yield json.dumps(obj, ensure_ascii=False) + "\n"
                    except json.JSONDecodeError:
                        yield decoded + "\n"
    except requests.exceptions.ConnectionError:
        yield json.dumps({
            "response": "⚠️ Не удалось подключиться к Ollama. Убедитесь, что сервер запущен: `ollama serve`.",
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

# ================= ЭНДПОИНТЫ =================
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
        "model": MODEL_NAME,
        "ollama": {"status": ollama_status, "models": ollama_models, "url": OLLAMA_URL},
        "temp_dir": {"path": os.path.abspath(TEMP_DIR), "files": temp_files, "size_bytes": temp_size},
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
    print(f"📦 Модель: {MODEL_NAME}")
    print(f"🌐 Ollama: {OLLAMA_URL}")
    print(f"📁 Temp: {os.path.abspath(TEMP_DIR)}")
    print(f"👷 Workers: {MAX_WORKERS}")
    print(f"📊 Chunk: {CHUNK_SIZE} (overlap: {CHUNK_OVERLAP})")
    print(f"⚖️ Юрисдикция: ТОЛЬКО Республика Таджикистан")
    print("=" * 60)
    print("Запуск на http://0.0.0.0:5000")
    print("Ctrl+C для остановки")
    print("=" * 60)
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
