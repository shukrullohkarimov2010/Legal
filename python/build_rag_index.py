# -*- coding: utf-8 -*-
"""Пересборка поисковых индексов по кодексам Республики Таджикистан.

    python build_rag_index.py                 пересобрать оба индекса
    python build_rag_index.py --embeddings    дополнительно построить векторы
    python build_rag_index.py --check         только проверить целостность

Скрипт намеренно отделён от Flask-приложения: индексация занимает минуты
и не должна происходить в момент первого запроса пользователя.
"""

from __future__ import annotations

import argparse
import json
import os
import sys
import time

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

import app as legal_app  # noqa: E402
import legal_rag  # noqa: E402

RAG_DIR = os.path.dirname(legal_app.RAG_SOURCE_PATH)

# Минимальное число статей на кодекс. Если извлечение текста сломается
# (например, конвертер .doc потеряет кириллицу), сборка обязана упасть,
# а не записать пустой индекс поверх рабочего.
MIN_ARTICLES = {
    "1918_ru_1.docx": 1000,
    "574_ru.doc": 300,
    "налоговый кодекс.docx": 250,
    "трудовой кодекс.docx": 250,
    "семейный кодекс.docx": 120,
    "автомабильный кодекс.docx": 80,
    "КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРОЦЕДУРАХ.docx": 100,
    "ЭКОНОМИЧЕСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx": 250,
    "ГРАЖДАНСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx": 350,
    "УГОЛОВНО - ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx": 450,
    "ПРОЦЕССУАЛЬНЫЙ КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРАВОНАРУШЕНИЯХ.docx": 200,
}


def fix_flattened_superscripts(name: str, chunks: list[dict]) -> None:
    """Статьи вида 397¹…397¹² в ГПК при чтении .docx теряют надстрочный знак и
    превращаются в «3971»…«39712» — ссылка указывала бы на несуществующую статью.
    Возвращаем привычную запись «397.1». Действует только там, где номер из
    четырёх-пяти цифр начинается с номера предыдущей статьи и в десять с лишним
    раз больше него (обычная нумерация так не прыгает)."""
    if name != "ГРАЖДАНСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx":
        return
    import re as _re
    base = None
    for chunk in chunks:
        article = str(chunk.get("article") or "")
        if not article.isdigit():
            continue
        if base and len(article) > len(base) and article.startswith(base) and int(article) >= int(base) * 10:
            fixed = f"{base}.{article[len(base):]}"
            for key in ("article", "title", "article_title", "citation"):
                if isinstance(chunk.get(key), str):
                    chunk[key] = chunk[key].replace(article, fixed, 1)
            text = chunk.get("text")
            if isinstance(text, str):
                chunk["text"] = _re.sub(r"^(Статья\s+)" + article, r"\g<1>" + fixed, text, count=1)
        else:
            base = article


def build_source_chunks(name: str) -> list[dict]:
    path = os.path.join(RAG_DIR, name)
    if not os.path.isfile(path):
        raise FileNotFoundError(path)
    text = legal_app.extract_text_for_rag(path)
    label = legal_app.RAG_CODE_LABELS.get(name, name)
    chunks = legal_rag.build_article_chunks(text, name, label)
    fix_flattened_superscripts(name, chunks)
    articles = len({chunk["article"] for chunk in chunks})
    required = MIN_ARTICLES.get(name, 1)
    if articles < required:
        raise RuntimeError(
            f"{name}: найдено {articles} статей, ожидалось не менее {required}. "
            "Проверьте извлечение текста — индекс не перезаписан."
        )
    print(f"  {name:<32} фрагментов {len(chunks):>5}  статей {articles:>5}")
    return chunks


def write_index(path: str, payload: dict) -> None:
    """Запись через временный файл: прерванная сборка не портит рабочий индекс."""
    temporary = path + ".tmp"
    with open(temporary, "w", encoding="utf-8") as handle:
        json.dump(payload, handle, ensure_ascii=False)
    os.replace(temporary, path)
    size_mb = os.path.getsize(path) / (1024 * 1024)
    print(f"  записан {os.path.basename(path)} ({size_mb:.1f} МБ)")


def build_contract_index() -> dict:
    print("Индекс для анализа договоров (Гражданский кодекс):")
    name = legal_app.CONTRACT_RAG_SOURCE_NAME
    path = os.path.join(RAG_DIR, name)
    chunks = build_source_chunks(name)
    payload = {
        "source_path": path,
        "source_name": name,
        "source_mtime": os.path.getmtime(path),
        "built_at": time.time(),
        "index_version": legal_rag.INDEX_VERSION,
        "chunks": chunks,
    }
    write_index(legal_app.RAG_INDEX_FILE, payload)
    return payload


def build_chat_index() -> dict:
    print("Индекс для чата (все кодексы):")
    sources = []
    chunks = []
    for name in legal_app.CHAT_RAG_SOURCE_NAMES:
        path = os.path.join(RAG_DIR, name)
        if not os.path.isfile(path):
            print(f"  {name}: файл отсутствует, пропущен")
            continue
        chunks.extend(build_source_chunks(name))
        sources.append({"path": path, "name": name, "mtime": os.path.getmtime(path)})
    if not chunks:
        raise RuntimeError("Не собрано ни одного фрагмента — индекс не перезаписан")
    payload = {
        "source_name": "Законы Республики Таджикистан",
        "sources": sources,
        "built_at": time.time(),
        "index_version": legal_rag.INDEX_VERSION,
        "chunks": chunks,
    }
    write_index(legal_app.CHAT_RAG_INDEX_FILE, payload)
    return payload


def build_embeddings(index_path: str, payload: dict) -> None:
    provider = legal_rag.EmbeddingProvider()
    if not provider.available():
        print(
            f"  векторы пропущены: модель {legal_rag.EMBED_MODEL} недоступна"
            f" ({provider.reason}).\n"
            f"  установить: ollama pull {legal_rag.EMBED_MODEL}"
        )
        return
    chunks = payload["chunks"]
    print(f"  эмбеддинги для {len(chunks)} фрагментов, модель {legal_rag.EMBED_MODEL}...")
    started = time.time()
    texts, ids = [], []
    for chunk in chunks:
        # В вектор идёт заголовок статьи вместе с телом: без него короткие
        # части длинной статьи теряют тему.
        texts.append(chunk["text"][:2000])
        ids.append(chunk["id"])
    vectors = provider.embed(texts)
    if not vectors:
        print(f"  векторы пропущены: {provider.reason}")
        return
    path = legal_rag.save_embeddings(index_path, ids, vectors)
    print(f"  записан {os.path.basename(path)} за {time.time() - started:.0f} с")


def check() -> int:
    print("Проверка индексов:")
    problems = 0
    for path in (legal_app.RAG_INDEX_FILE, legal_app.CHAT_RAG_INDEX_FILE):
        if not os.path.isfile(path):
            print(f"  {os.path.basename(path)}: отсутствует")
            problems += 1
            continue
        with open(path, encoding="utf-8") as handle:
            data = json.load(handle)
        chunks = data.get("chunks", [])
        version = data.get("index_version")
        articles = len({c.get("article") for c in chunks if c.get("article")})
        titled = sum(1 for c in chunks if str(c.get("title", "")).startswith("Статья"))
        status = "ок" if version == legal_rag.INDEX_VERSION else "УСТАРЕЛ"
        print(
            f"  {os.path.basename(path):<28} версия={version} [{status}] "
            f"фрагментов={len(chunks)} статей={articles} "
            f"корректных заголовков={titled * 100 // max(len(chunks), 1)}%"
        )
        if version != legal_rag.INDEX_VERSION:
            problems += 1
        store = legal_rag.load_embeddings(path)
        print(f"    векторы: {'есть, ' + str(len(store['ids'])) + ' шт.' if store else 'нет'}")
    return problems


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--embeddings", action="store_true", help="построить векторный слой")
    parser.add_argument("--check", action="store_true", help="только проверить индексы")
    parser.add_argument("--chat-only", action="store_true", help="только индекс чата")
    parser.add_argument("--contract-only", action="store_true", help="только индекс договоров")
    args = parser.parse_args()

    if args.check:
        return 1 if check() else 0

    started = time.time()
    built: list[tuple[str, dict]] = []
    if not args.chat_only:
        built.append((legal_app.RAG_INDEX_FILE, build_contract_index()))
    if not args.contract_only:
        built.append((legal_app.CHAT_RAG_INDEX_FILE, build_chat_index()))

    if args.embeddings:
        print("Векторный слой:")
        for path, payload in built:
            build_embeddings(path, payload)

    print(f"Готово за {time.time() - started:.0f} с")
    check()
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
