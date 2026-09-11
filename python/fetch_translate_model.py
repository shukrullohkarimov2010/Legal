# -*- coding: utf-8 -*-
"""Загрузка модели перевода ru<->tg для чата.

    python fetch_translate_model.py

Скачивает NLLB-200-distilled-600M, уже сконвертированную в формат CTranslate2
и квантованную в int8: ~620 МБ против ~2.4 ГБ у оригинала под torch. На машине
без дискретной GPU это разница между рабочим переводом и свопом.

Загрузка идёт по частям и умеет докачку: канал до HuggingFace бывает медленным,
и обрыв на 500-м мегабайте не должен означать «начать сначала». Скрипт можно
перезапускать сколько угодно раз — уже скачанные файлы он пропускает.
"""

from __future__ import annotations

import os
import sys
import time

import requests

REPO = os.environ.get("TG_TRANSLATE_REPO", "JustFrederik/nllb-200-distilled-600M-ct2-int8")
PROJECT_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
TARGET_DIR = os.environ.get(
    "TG_TRANSLATE_MODEL_DIR",
    os.path.join(PROJECT_ROOT, "storage", "app", "models", "nllb-200-distilled-600M-ct2-int8"),
)

# Веса плюс словарь и токенизатор. Без sentencepiece.bpe.model токенизатор не
# соберётся, поэтому список фиксированный, а не «всё, что лежит в репозитории».
FILES = [
    "config.json",
    "model.bin",
    "shared_vocabulary.txt",
    "sentencepiece.bpe.model",
    "special_tokens_map.json",
    "tokenizer.json",
    "tokenizer_config.json",
]

CHUNK = 1 << 20


def download(name: str) -> None:
    url = "https://huggingface.co/%s/resolve/main/%s" % (REPO, name)
    target = os.path.join(TARGET_DIR, name)
    partial = target + ".part"

    head = requests.head(url, allow_redirects=True, timeout=60)
    head.raise_for_status()
    total = int(head.headers.get("Content-Length") or 0)

    if os.path.isfile(target) and (not total or os.path.getsize(target) == total):
        print("  %-28s уже на месте" % name)
        return

    done = os.path.getsize(partial) if os.path.isfile(partial) else 0
    headers = {"Range": "bytes=%d-" % done} if done else {}

    with requests.get(url, headers=headers, stream=True, timeout=120) as response:
        if done and response.status_code != 206:
            # Сервер не принял докачку — начинаем файл заново, иначе склеим мусор.
            done = 0
            headers = {}
            response.close()
            response = requests.get(url, stream=True, timeout=120)
        response.raise_for_status()

        mode = "ab" if done else "wb"
        started = time.time()
        last_report = 0.0
        with open(partial, mode) as handle:
            for chunk in response.iter_content(CHUNK):
                if not chunk:
                    continue
                handle.write(chunk)
                done += len(chunk)
                now = time.time()
                if now - last_report >= 10:
                    speed = done / max(now - started, 1e-6) / 1048576
                    share = " (%.0f%%)" % (100.0 * done / total) if total else ""
                    print("  %-28s %6.1f МБ%s  %.2f МБ/с" % (name, done / 1048576, share, speed),
                          flush=True)
                    last_report = now

    os.replace(partial, target)
    print("  %-28s готов, %.1f МБ" % (name, os.path.getsize(target) / 1048576), flush=True)


def main() -> int:
    os.makedirs(TARGET_DIR, exist_ok=True)
    print("Модель перевода: %s" % REPO)
    print("Каталог:         %s" % TARGET_DIR)
    for name in FILES:
        try:
            download(name)
        except Exception as error:  # noqa: BLE001
            print("  %-28s ОШИБКА: %s" % (name, error), flush=True)
            print("Загрузка не завершена. Запустите скрипт ещё раз — он продолжит с места обрыва.")
            return 1
    print("Готово. Чат подхватит перевод после перезапуска python/app.py.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
