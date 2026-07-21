from flask import Flask, request, Response, jsonify, stream_with_context, send_from_directory
from flask_cors import CORS
import requests
import json
import os
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

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})

# ================= НАСТРОЙКИ =================
OLLAMA_URL = "http://localhost:11434/api/generate"
MODEL_NAME = "llama3.1:8b"
CHUNK_SIZE = 8000
CHUNK_OVERLAP = 500
MAX_WORKERS = 1
CACHE_FILE = ".ollama_cache.json"
ANALYSIS_VERSION = "analysis-v9-grounded-rag-final-only"
TEMP_DIR = "tmp_uploads"
RESULTS_DIR = "analysis_results"
PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
CONTRACT_RAG_SOURCE_NAME = "1918_ru_1.docx"
CONTRACT_RAG_MIN_CHUNKS = 600
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
RAG_CHUNK_SIZE = int(os.environ.get("RAG_CHUNK_SIZE", "1800"))
RAG_CHUNK_OVERLAP = int(os.environ.get("RAG_CHUNK_OVERLAP", "200"))

_current_rag_source = None
_current_rag_index = None
RAG_TOP_K = int(os.environ.get("RAG_TOP_K", "10"))

WRONG_JURISDICTION_PENALTY = 20
HALLUCINATION_PENALTY = 0  # Отключаем штраф за галлюцинации

os.makedirs(TEMP_DIR, exist_ok=True)
os.makedirs(RESULTS_DIR, exist_ok=True)
os.makedirs(os.path.dirname(RAG_INDEX_FILE), exist_ok=True)

active_sessions = {}
sessions_lock = threading.Lock()
rag_index = None
rag_lock = threading.Lock()
rag_articles_cache = None

# ================= КЭШИРОВАНИЕ =================
def load_cache():
    if os.path.exists(CACHE_FILE):
        try:
            with open(CACHE_FILE, "r", encoding="utf-8") as f:
                return json.load(f)
        except Exception as e:
            print(f"⚠️ Ошибка загрузки кэша: {e}")
    return {}

def save_cache(cache):
    try:
        with open(CACHE_FILE, "w", encoding="utf-8") as f:
            json.dump(cache, f, ensure_ascii=False, indent=2)
    except Exception as e:
        print(f"⚠️ Ошибка сохранения кэша: {e}")

def get_cache_key(text):
    return hashlib.md5((ANALYSIS_VERSION + MODEL_NAME + text).encode("utf-8")).hexdigest()

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

# ================= ФУНКЦИЯ ГАЛЛЮЦИНАЦИЙ (отключена) =================
def detect_hallucination(finding, contract_text, rag_context):
    """Минимальная проверка — только если вообще нет данных"""
    return False

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

        text = _extract_text_from_doc_binary(path)
        if text and len(text.strip()) > 40:
            print(f"  ✅ DOC (бинарный парсер): {len(text)} символов")
            return text

        raise Exception("Не удалось извлечь текст из DOC")
    except Exception as e:
        raise Exception(f"Ошибка чтения DOC: {str(e)}")

def _extract_text_from_doc_command(path):
    for cmd in ["antiword", "catdoc"]:
        if shutil.which(cmd):
            try:
                proc = subprocess.run([cmd, path], capture_output=True, text=True, timeout=60)
                if proc.returncode == 0 and proc.stdout.strip():
                    return proc.stdout.strip()
            except Exception as e:
                print(f"  ⚠️ Ошибка вызова {cmd}: {e}")
    return ""

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
    return re.findall(r"[0-9A-Za-zА-Яа-яЁёІіҶҷҚқҒғӢӣӮӯҲҳ]+", (text or "").lower())

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

def guess_chunk_title(chunk, idx):
    lines = [line.strip() for line in chunk.splitlines() if line.strip()]
    for line in lines[:8]:
        if len(line) <= 180:
            return line
    return f"Fragment {idx + 1}"

def build_rag_chunks(text):
    raw_chunks = split_text_smart(text, RAG_CHUNK_SIZE, RAG_CHUNK_OVERLAP)
    chunks = []
    for idx, chunk in enumerate(raw_chunks):
        tokens = tokenize_for_rag(chunk)
        if len(tokens) < 10:
            continue
        chunks.append({
            "id": idx + 1,
            "title": guess_chunk_title(chunk, idx),
            "text": chunk,
            "tokens": tokens,
            "token_count": len(tokens),
        })
    return chunks

def hydrate_rag_index(index):
    chunks = index.get("chunks", [])
    doc_count = max(len(chunks), 1)
    df = Counter()
    for chunk in chunks:
        df.update(set(chunk.get("tokens", [])))
    index["idf"] = {
        term: math.log(1 + (doc_count - freq + 0.5) / (freq + 0.5))
        for term, freq in df.items()
    }
    index["avgdl"] = (
        sum(chunk.get("token_count", 0) for chunk in chunks) / doc_count
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
                    and cached.get("chunk_size") == RAG_CHUNK_SIZE
                    and cached.get("chunk_overlap") == RAG_CHUNK_OVERLAP
                ):
                    rag_index = hydrate_rag_index(cached)
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
            "chunk_size": RAG_CHUNK_SIZE,
            "chunk_overlap": RAG_CHUNK_OVERLAP,
            "chunks": chunks,
        }
        rag_index = hydrate_rag_index(index)

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

def rag_status_payload():
    index = load_rag_index(force=False)
    return {
        "ready": bool(index.get("ready")),
        "source": index.get("source_name"),
        "source_path": index.get("source_path") or index.get("expected_path"),
        "chunks": len(index.get("chunks", [])),
        "built_at": index.get("built_at"),
        "error": index.get("error"),
    }

def _shorten_text(text, limit=260):
    text = re.sub(r"\s+", " ", text or "").strip()
    if len(text) <= limit:
        return text
    shortened = text[:limit].rsplit(" ", 1)[0].strip()
    return shortened + "..."

def extract_rag_articles(article_numbers):
    source_path = discover_rag_source()
    if not source_path:
        return []
    ext = os.path.splitext(source_path)[1].lower()
    if ext not in [".docx", ".doc"]:
        return []
    paragraphs = []
    source_name = os.path.basename(source_path)
    if ext == ".docx":
        doc = Document(source_path)
        paragraphs = [p.text.strip() for p in doc.paragraphs if p.text and p.text.strip()]
    elif ext == ".doc":
        text = extract_text_from_doc(source_path)
        if text:
            paragraphs = [p.strip() for p in text.split('\n') if p.strip()]
    if not paragraphs:
        return []
    articles = []
    for number in article_numbers:
        number = str(number).strip()
        if not number:
            continue
        starts = [
            idx for idx, paragraph in enumerate(paragraphs)
            if re.match(rf"^Статья\s+{re.escape(number)}\.", paragraph, re.IGNORECASE)
        ]
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
        })
    return articles

def retrieve_rag(query, top_k=None):
    index = load_rag_index(force=False)
    if not index.get("ready"):
        return []
    query_tokens = tokenize_for_rag(query)
    if not query_tokens:
        return []
    q_terms = Counter(query_tokens)
    idf = index.get("idf", {})
    avgdl = max(float(index.get("avgdl", 1)), 1.0)
    k1 = 1.5
    b = 0.75
    results = []
    for chunk in index.get("chunks", []):
        tokens = chunk.get("tokens", [])
        if not tokens:
            continue
        tf = Counter(tokens)
        dl = max(chunk.get("token_count", len(tokens)), 1)
        score = 0.0
        for term, q_count in q_terms.items():
            if term not in tf:
                continue
            term_idf = idf.get(term, 0.1)
            freq = tf[term]
            denom = freq + k1 * (1 - b + b * dl / avgdl)
            score += term_idf * ((freq * (k1 + 1)) / denom) * min(q_count, 3)
        query_lower = (query or "").lower()
        text_lower = (chunk.get("text") or "").lower()
        for phrase in re.findall(r"[А-Яа-яЁёA-Za-z0-9][^.!?\n]{8,80}", query_lower):
            phrase = phrase.strip()
            if phrase and phrase in text_lower:
                score += 2.0
        if score > 0:
            results.append({
                "id": chunk.get("id"),
                "title": chunk.get("title"),
                "text": chunk.get("text"),
                "score": round(score, 4),
                "source": index.get("source_name"),
            })
    results.sort(key=lambda item: item["score"], reverse=True)
    return results[:top_k or RAG_TOP_K]

def format_rag_context(results, max_chars=12000):
    if not results:
        return ""
    blocks = []
    used = 0
    for i, item in enumerate(results, 1):
        text = re.sub(r"\s+", " ", item.get("text", "")).strip()
        if len(text) > 2000:
            text = text[:2000].rsplit(" ", 1)[0] + "..."
        block = f"[{i}] {item.get('source')} / {item.get('title')} (score {item.get('score')}):\n{text}"
        if used + len(block) > max_chars:
            break
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

    if key in cache:
        cached = cache[key]
        if stream_callback:
            for char in cached:
                stream_callback(char)
                time.sleep(0.001)
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
            for line in r.iter_lines():
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
            cache[key] = response_text
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

# ================= АНАЛИЗ ЧАНКА =================
def analyze_chunk(args):
    i, total, chunk, full_text = args
    result = []

    def collect(text):
        result.append(text)

    legal_context = rag_context_for_query(chunk, top_k=RAG_TOP_K)
    legal_context_block = (
        f"\n\nРелевантные фрагменты кодексов Республики Таджикистан (RAG):\n{legal_context}\n"
        if legal_context else
        "\n\nRAG-контекст по кодексам Республики Таджикистан не найден или база еще не подключена.\n"
    )

    # Берем начало и конец документа для контекста
    full_text_preview = ""
    if total > 1:
        full_text_preview = f"\n\nКОНТЕКСТ ВСЕГО ДОКУМЕНТА (начало):\n{full_text[:1500]}\n...\n(конец):\n{full_text[-1500:]}\n"

    prompt = f"""Ты — юрист-аналитик по законодательству Республики Таджикистан. 

ЮРИСДИКЦИЯ: ТОЛЬКО Республика Таджикистан (РТ). ЗАПРЕЩЕНО: РФ, Россия, ГК РФ.

Это ЧАСТЬ {i+1} из {total} договора.{full_text_preview}

Проанализируй этот фрагмент договора по законам РТ и НАЙДИ ВСЕ юридически значимые риски. 
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
    send_to_ollama(prompt, stream_callback=collect, num_predict=2000, temperature=0.15)
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

def _filter_findings_by_quality(findings, min_quality=0.45):
    """Фильтрует риски по минимальному качеству"""
    filtered = []
    for finding in findings:
        quality = _calculate_finding_quality(finding)
        if quality >= min_quality:
            finding['_quality'] = quality
            filtered.append(finding)
        else:
            print(f"  [FILTER] Удален низкокачественный риск: {finding.get('title', 'N/A')[:60]} (quality={quality:.2f})")
    return filtered

def _filter_findings_by_contract_evidence(findings, contract_text):
    """Keep only findings whose quoted clause can be located in the source."""
    source = _normalize_text(contract_text)
    if not source:
        return []

    verified = []
    for finding in findings:
        quote = _normalize_text(finding.get("fragment", ""))
        quote = quote.strip('«»"\'')
        # A short or fabricated quote is not enough to support a legal finding.
        if len(quote) < 20 or quote not in source:
            print(f"  [EVIDENCE] Removed unsupported finding: {finding.get('title', 'N/A')[:60]}")
            continue
        finding["_contract_evidence"] = True
        verified.append(finding)
    return verified

def _calculate_severity_score(severity, quality):
    """Вычисляет взвешенный балл за severity с учетом качества"""
    base_scores = {
        # A risk score is an exposure indicator, not a count of errors.
        # Keep one severity band from overwhelming the whole 0–100 scale.
        'high': 4,
        'medium': 2,
        'low': 1,
    }
    
    base = base_scores.get(severity, 2)
    
    # Умножаем на качество (минимум 0.6, максимум 1.0)
    quality_factor = max(0.6, min(1.0, 0.7 + 0.3 * quality))
    
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

def estimate_risk_metrics(analysis_text, contract_text="", rag_context=""):
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

    # Парсер рисков
    findings = []
    current = None
    
    for raw_line in (analysis_text or "").splitlines():
        line = raw_line.strip()
        
        if not line:
            if current:
                findings.append(current)
                current = None
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
                findings.append(current)
            title = re.sub(r'\[[^\]]*\]\s*', '', line)
            title = re.sub(r'^[\s•\-\*]+', '', title)
            title = title.strip(' .:')
            if not title or len(title) < 3:
                title = "Юридический риск"
            current = {
                "title": title,
                "severity": severity,
                "point": "",
                "fragment": "",
                "norm": "",
                "why": "",
                "fix": "",
            }
            continue
        
        if current:
            m = re.search(r'Пункт\s+договора\s*:\s*(.*)', line, re.IGNORECASE)
            if m: current["point"] = m.group(1).strip()
            m = re.search(r'Фрагмент\s+договора\s*:\s*(.*)', line, re.IGNORECASE)
            if m: current["fragment"] = m.group(1).strip()
            m = re.search(r'Норма\s+РТ\s*:\s*(.*)', line, re.IGNORECASE)
            if m: current["norm"] = m.group(1).strip()
            m = re.search(r'Почему\s+риск\s*:\s*(.*)', line, re.IGNORECASE)
            if m: current["why"] = m.group(1).strip()
            m = re.search(r'Что\s+исправить\s*:\s*(.*)', line, re.IGNORECASE)
            if m: current["fix"] = m.group(1).strip()
    
    if current:
        findings.append(current)

    print(f"  [PARSER] Найдено рисков до фильтрации: {len(findings)}")
    
    # Фильтруем низкокачественные риски
    findings = _filter_findings_by_quality(findings, min_quality=0.45)
    findings = _filter_findings_by_contract_evidence(findings, contract_text)
    
    # Удаляем дубликаты
    findings = _deduplicate_findings(findings)
    
    print(f"  [PARSER] После фильтрации и дедупликации: {len(findings)}")
    for f in findings:
        quality = f.get('_quality', 0)
        print(f"    [{f['severity']}] {f['title'][:50]} | quality={quality:.2f} | point={bool(f['point'])} frag={bool(f['fragment'])} norm={bool(f['norm'])}")

    # Подсчет по severity
    high = sum(1 for f in findings if f['severity'] == 'high')
    medium = sum(1 for f in findings if f['severity'] == 'medium')
    low = sum(1 for f in findings if f['severity'] == 'low')

    # Улучшенный расчёт балла с учетом качества
    score = 0
    
    for finding in findings:
        severity = finding.get('severity')
        quality = finding.get('_quality', 0.5)
        score += _calculate_severity_score(severity, quality)
    
    # Бонус за количество (сниженный)
    total_findings = high + medium + low
    if total_findings > 0:
        score += min(8, total_findings * 1.5)
    
    # Бонус за сложность и разнообразие
    complexity_bonus = _calculate_complexity_bonus(findings)
    score += complexity_bonus
    
    # Штраф за неправильную юрисдикцию
    if wrong_jurisdiction_count > 0:
        score = max(0, score - wrong_jurisdiction_count * WRONG_JURISDICTION_PENALTY)
    
    # 100 is reserved for a human-confirmed critical situation; an automated
    # RAG analysis must not assign it merely because it found many items.
    score = max(0, min(95, round(score)))
    level = "Высокий" if score >= 70 or high >= 2 else "Средний" if score >= 35 or high >= 1 or medium >= 3 else "Низкий"
    
    # Улучшенный вердикт с учетом качества
    high_quality = sum(1 for f in findings 
                       if f['severity'] == 'high' and f.get('_quality', 0) >= 0.60)
    medium_quality = sum(1 for f in findings 
                         if f['severity'] == 'medium' and f.get('_quality', 0) >= 0.60)
    
    if score >= 70 or high_quality >= 3:
        verdict = "❌ Не подписывать"
    elif score >= 30 or high_quality >= 1 or medium_quality >= 3:
        verdict = "⚠️ Требует правок"
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

        chunks = split_text_smart(full_text, CHUNK_SIZE, CHUNK_OVERLAP)
        print(f"📦 Разбито на {len(chunks)} чанков")

        yield send_event({"step": "chunked", "count": len(chunks), "progress_percent": 12}, "status")
        yield send_event({"step": "analyzing_start", "total": len(chunks), "progress_percent": 15}, "status")

        tasks = [(i, len(chunks), c, full_text) for i, c in enumerate(chunks)]
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
        
        risk_metrics = estimate_risk_metrics(full_analysis_text, contract_text=full_text, rag_context=rag_summary_context)
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
            final_summary = send_to_ollama(summary_prompt, num_predict=2500, temperature=0.3)

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
                final_summary = metrics_header + normalize_legal_terms(final_summary)
                yield send_event({
                    "step": "summary_complete",
                    "content": final_summary,
                    "length": len(final_summary),
                    "revised_contract": revised_contract,
                    "metrics": {
                        "score": risk_metrics["score"],
                        "level": risk_metrics["level"],
                        "high": risk_metrics["high"],
                        "medium": risk_metrics["medium"],
                        "low": risk_metrics["low"],
                        "verdict": risk_metrics["verdict"],
                        "hallucinations_detected": risk_metrics.get("hallucinations_detected", 0),
                        "wrong_jurisdiction_mentions": risk_metrics.get("wrong_jurisdiction_mentions", 0),
                    }
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
        payload = rag_status_payload()
        payload["rebuilt"] = bool(index.get("ready"))
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
        "results": retrieve_rag(query, top_k=top_k),
        "status": rag_status_payload(),
    })

@app.route("/api/rag/articles", methods=["POST"])
def rag_articles():
    data = request.get_json(silent=True) or {}
    numbers = data.get("numbers") or ["104", "111", "114", "246", "247", "248", "249", "250"]
    return jsonify({
        "articles": extract_rag_articles(numbers),
        "status": rag_status_payload(),
    })

@app.route("/api/crime/qualify", methods=["POST"])
def crime_qualify():
    data = request.get_json(silent=True) or {}
    crime_description = (data.get("description") or "").strip()
    if not crime_description:
        return jsonify({"error": "crime description is required"}), 400
    rag_results = retrieve_rag(crime_description, top_k=8)
    rag_context = format_rag_context(rag_results)
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
        "rag_sources": [
            {"number": i + 1, "source": item.get("source"), "title": item.get("title")}
            for i, item in enumerate(rag_results)
        ],
        "rag_status": rag_status_payload(),
    })

def stream_chat_response(payload):
    message = (payload.get("message") or "").strip()
    history = payload.get("history") or []
    model = payload.get("model") or MODEL_NAME
    temperature = float(payload.get("temperature", 0.5))
    personality = payload.get("personality") or "professional"

    system_prompts = {
        "professional": "Вы профессиональный юрист по законодательству Республики Таджикистан. Отвечайте точно, структурированно и на русском языке. Используйте ТОЛЬКО законы РТ, никогда не упоминайте РФ.",
        "friendly": "Вы дружелюбный юридический помощник по законодательству Республики Таджикистан. Объясняйте простым языком, но юридически аккуратно. Только законы РТ.",
        "concise": "Вы краткий юридический эксперт по законодательству Республики Таджикистан. Только суть, риски и практические шаги. Только законы РТ.",
    }

    rag_results = retrieve_rag(message, top_k=RAG_TOP_K)
    rag_context = format_rag_context(rag_results)
    sources = [
        f"[{i + 1}] {item.get('source')} / {item.get('title')}"
        for i, item in enumerate(rag_results)
    ]

    recent_history = []
    for item in history[-8:]:
        role = item.get("role")
        content = (item.get("content") or "").strip()
        if role in ["user", "ai"] and content:
            recent_history.append(f"{'Пользователь' if role == 'user' else 'Ассистент'}: {content[:1200]}")

    prompt = f"""{system_prompts.get(personality, system_prompts["professional"])}

КРИТИЧЕСКИ ВАЖНО - ЮРИСДИКЦИЯ:
- Используй ТОЛЬКО законы Республики Таджикистан
- ЗАПРЕЩЕНО: "РФ", "Россия", "ГК РФ", "ТК РФ", "НК РФ", "Российская Федерация"
- Разрешено ТОЛЬКО: "РТ", "ГК РТ", "кодекс РТ", "Республика Таджикистан"

Правила RAG:
- Используй фрагменты кодексов РТ ниже как основной справочный материал
- Если в контексте нет точной нормы, честно скажи, что в подключенной базе не найдено
- Не выдумывай номера статей
- В конце добавь раздел "Источники RAG"

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
        "options": {
            "temperature": temperature,
            "num_predict": 1536,
            "top_p": 0.9,
        },
    }

    try:
        with requests.post(OLLAMA_URL, json=data, stream=True, timeout=600) as r:
            r.raise_for_status()
            for line in r.iter_lines():
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
        "version": "3.0.0-senior",
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
    print("🚀 LegalAI Python API v3.0 (Senior Edition)")
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
