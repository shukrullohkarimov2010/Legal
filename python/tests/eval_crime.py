# -*- coding: utf-8 -*-
"""Живая проверка поиска по УК РТ на наборе описаний из crime_eval.json.

    python tests/eval_crime.py            # с переписыванием запроса моделью
    python tests/eval_crime.py --no-llm   # только словарь синонимов и BM25

Тратит вызовы модели (по одному на описание), поэтому в pytest не входит.
Метрика — попадание ожидаемой статьи в первые три найденных; выше 80%
считается нормой. Посторонние вопросы должны оставаться без норм.
"""

from __future__ import annotations

import json
import os
import sys

HERE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, HERE)

if "--no-llm" in sys.argv:
    os.environ["CRIME_QUERY_REWRITE"] = "0"

import app  # noqa: E402

TOP = 3


def main():
    with open(os.path.join(os.path.dirname(os.path.abspath(__file__)), "crime_eval.json"), encoding="utf-8") as file:
        data = json.load(file)

    hits, misses = 0, []
    for case in data["cases"]:
        results, _ = app.retrieve_crime_rag(case["q"])
        grounded = app.grounded_crime_results(results)
        found = []
        for item in grounded:
            number = str(item.get("article"))
            if number not in found:
                found.append(number)
        top = found[:TOP]
        if set(top) & set(case["expect"]):
            hits += 1
        else:
            misses.append((case["q"][:70], case["expect"], top))

    noise = []
    for query in data["off_topic"]:
        results, _ = app.retrieve_crime_rag(query)
        if app.grounded_crime_results(results):
            noise.append(query)

    total = len(data["cases"])
    rate = 100.0 * hits / total
    print("Попадание в топ-%d: %d из %d (%.0f%%)" % (TOP, hits, total, rate))
    for query, expect, top in misses:
        print("  промах: %s | ожидали %s | нашли %s" % (query, expect, top))
    print("Посторонние вопросы без норм: %d из %d" % (len(data["off_topic"]) - len(noise), len(data["off_topic"])))
    for query in noise:
        print("  ложная норма на: %s" % query)
    sys.exit(0 if rate >= 80 and not noise else 1)


if __name__ == "__main__":
    main()
