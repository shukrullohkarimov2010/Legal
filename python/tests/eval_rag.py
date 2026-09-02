# -*- coding: utf-8 -*-
"""Измерение качества поиска по кодексам РТ.

    python tests/eval_rag.py                оценить текущий движок
    python tests/eval_rag.py --ablation     вклад каждого компонента
    python tests/eval_rag.py --failures     показать провалившиеся вопросы

Метрики:
  Recall@k   доля вопросов, где эталонная статья попала в первые k
  MRR@10     средний обратный ранг первого попадания
  Маршрут    доля вопросов, где топ-1 пришёл из нужного кодекса

Оценка полностью офлайновая: она не обращается к языковой модели и меряет
только поиск. Ошибку поиска модель исправить не может — она отвечает по тому,
что ей передали.
"""

from __future__ import annotations

import argparse
import json
import os
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, os.path.dirname(HERE))

import legal_rag  # noqa: E402

INDEX_PATH = os.path.join(
    os.path.dirname(os.path.dirname(HERE)), "storage", "app", "rag", "laws-rt-chat.index.json"
)
QUESTIONS_PATH = os.path.join(HERE, "eval_questions.json")
CUTOFFS = (1, 3, 5, 10)


def load_index() -> dict:
    with open(INDEX_PATH, encoding="utf-8") as handle:
        index = json.load(handle)
    stats = legal_rag.build_stats(index["chunks"])
    index.update(stats)
    index["ready"] = True
    return index


def load_questions() -> list[dict]:
    with open(QUESTIONS_PATH, encoding="utf-8") as handle:
        return json.load(handle)["questions"]


def verify_gold(index: dict, questions: list[dict]) -> list[str]:
    """Эталон обязан существовать в корпусе, иначе метрика лжёт."""
    known = {(chunk["source"], chunk["article"]) for chunk in index["chunks"]}
    problems = []
    for item in questions:
        for article in item["articles"]:
            if (item["source"], article) not in known:
                problems.append(f"{item['source']} ст.{article} — нет в индексе ({item['q']!r})")
    return problems


def evaluate(index: dict, questions: list[dict], *, router=True, synonyms=True,
             stemming=True, store=None, provider=None) -> dict:
    """Прогоняет набор с возможностью отключить отдельные компоненты."""
    original_route = legal_rag.route_codes
    original_expand = legal_rag.expand_query
    original_tokenize = legal_rag.tokenize

    if not router:
        legal_rag.route_codes = lambda query: {source: 1.0 for source in legal_rag.CODE_HINTS}
    if not synonyms:
        legal_rag.expand_query = lambda query: query or ""
    if not stemming:
        legal_rag.tokenize = lambda text: [
            token for token in legal_rag._WORD_RE.findall((text or "").lower())
        ]

    try:
        # Токенизация запроса обязана совпадать с токенизацией индекса,
        # поэтому при отключённом стемминге статистику считаем заново.
        working = index
        if not stemming:
            chunks = []
            for chunk in index["chunks"]:
                copy = dict(chunk)
                copy["tokens"] = legal_rag.tokenize(chunk["text"])
                copy["token_count"] = len(copy["tokens"])
                copy["title_tokens"] = legal_rag.tokenize(chunk["title"])
                chunks.append(copy)
            working = dict(index, chunks=chunks, **legal_rag.build_stats(chunks))

        hits = {k: 0 for k in CUTOFFS}
        reciprocal = 0.0
        routed = 0
        failures = []

        for item in questions:
            results = legal_rag.search(working, item["q"], top_k=max(CUTOFFS),
                                       store=store, provider=provider)
            gold = {(item["source"], article) for article in item["articles"]}
            rank = None
            for position, result in enumerate(results, 1):
                if (result.get("source"), result.get("article")) in gold:
                    rank = position
                    break
            if results and results[0].get("source") == item["source"]:
                routed += 1
            if rank:
                reciprocal += 1.0 / rank
                for k in CUTOFFS:
                    if rank <= k:
                        hits[k] += 1
            else:
                failures.append((item, results[:3]))

        total = max(len(questions), 1)
        return {
            "recall": {k: hits[k] / total for k in CUTOFFS},
            "mrr": reciprocal / total,
            "routing": routed / total,
            "failures": failures,
            "total": total,
        }
    finally:
        legal_rag.route_codes = original_route
        legal_rag.expand_query = original_expand
        legal_rag.tokenize = original_tokenize


def format_row(name: str, result: dict) -> str:
    recall = result["recall"]
    return (
        f"{name:<26} "
        + "  ".join(f"R@{k}={recall[k]:.0%}" for k in CUTOFFS)
        + f"  MRR={result['mrr']:.3f}  маршрут={result['routing']:.0%}"
    )


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--ablation", action="store_true", help="вклад компонентов")
    parser.add_argument("--failures", action="store_true", help="показать промахи")
    parser.add_argument("--no-embeddings", action="store_true", help="только лексический поиск")
    args = parser.parse_args()

    index = load_index()
    questions = load_questions()

    problems = verify_gold(index, questions)
    if problems:
        print("Эталоны, отсутствующие в корпусе:")
        for problem in problems:
            print("  ", problem)
        print()

    store = provider = None
    if not args.no_embeddings:
        store = legal_rag.load_embeddings(INDEX_PATH)
        if store:
            provider = legal_rag.EmbeddingProvider()
            if not provider.available():
                store = provider = None

    print(f"Вопросов: {len(questions)}  фрагментов: {len(index['chunks'])}  "
          f"векторы: {'да' if store else 'нет'}")
    print("-" * 78)

    full = evaluate(index, questions, store=store, provider=provider)
    print(format_row("полный движок", full))

    if args.ablation:
        print()
        print("Абляция — что даёт каждый компонент:")
        for name, kwargs in (
            ("без маршрутизации", {"router": False}),
            ("без синонимов", {"synonyms": False}),
            ("без стемминга", {"stemming": False}),
            ("только BM25 (всё выкл.)", {"router": False, "synonyms": False, "stemming": False}),
        ):
            result = evaluate(index, questions, store=store, provider=provider, **kwargs)
            delta = result["recall"][5] - full["recall"][5]
            print(format_row("  " + name, result) + f"  ΔR@5={delta:+.0%}")

    if args.failures and full["failures"]:
        print()
        print(f"Промахи ({len(full['failures'])}):")
        for item, top in full["failures"]:
            print(f"  ✗ {item['q']}")
            print(f"      ждали: {legal_rag.CODE_SHORT.get(item['source'], item['source'])} "
                  f"ст.{', '.join(item['articles'])}")
            for result in top:
                print(f"      топ:   {result.get('code_short')} ст.{result.get('article')} — "
                      f"{(result.get('article_title') or '')[:56]}")

    return 0 if full["recall"][5] >= 0.70 else 1


if __name__ == "__main__":
    raise SystemExit(main())
