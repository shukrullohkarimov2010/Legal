# -*- coding: utf-8 -*-
"""Векторы статей для поиска по смыслу (storage/app/rag/laws-rt-chat.index.emb.npz).

Запуск из корня проекта:  python python/tools/build_embeddings.py
Нужен OPENAI_API_KEY в .env (модель text-embedding-3-small, около 5 млн токенов на весь индекс — порядка 10 центов).
Запускать после любой пересборки индекса (новый кодекс, правка текста): векторы привязаны к id фрагментов,
для новых фрагментов их нет, и они ищутся только лексически.
"""
import os
import sys
import time
from concurrent.futures import ThreadPoolExecutor

HERE = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, os.path.abspath(os.path.join(HERE, "..")))

import app  # noqa: E402  (загружает .env и индекс)
import legal_rag  # noqa: E402


def main():
    index = app.load_chat_rag_index(force=False)
    chunks = index.get("chunks") or []
    if not chunks:
        raise SystemExit("индекс пуст")
    provider = legal_rag.make_embedding_provider()
    if not provider.available():
        raise SystemExit("провайдер эмбеддингов недоступен: %s" % provider.reason)
    texts = ["%s %s\n%s" % (c.get("code_short") or "", c.get("title") or "", c.get("text") or "") for c in chunks]
    ids = [c["id"] for c in chunks]
    step = 64
    slices = [(i, texts[i:i + step]) for i in range(0, len(texts), step)]
    started = time.time()
    out = {}

    def work(item):
        start, part = item
        vectors = provider.embed(part)
        if vectors is None:
            raise RuntimeError("сбой эмбеддингов на фрагменте %d: %s" % (start, provider.reason))
        return start, vectors

    with ThreadPoolExecutor(2) as pool:
        for start, vectors in pool.map(work, slices):
            out[start] = vectors
            print("  %d/%d" % (min(start + step, len(texts)), len(texts)), flush=True)
    vectors = [v for start, _ in slices for v in out[start]]
    path = legal_rag.save_embeddings(app.CHAT_RAG_INDEX_FILE, ids, vectors, model=provider.model)
    print("готово: %d векторов, модель %s, %.0f с -> %s" % (len(vectors), provider.model, time.time() - started, path))


if __name__ == "__main__":
    sys.stdout.reconfigure(encoding="utf-8")
    main()
