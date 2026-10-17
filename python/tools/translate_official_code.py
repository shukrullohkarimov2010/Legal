# -*- coding: utf-8 -*-
"""Машинный перевод официального таджикского текста кодекса на русский по статьям (для RAG-индекса).

Источник — страница документа в официальной базе законодательства (base.mmk.tj), скачанная в
storage/app/official/base_mmk_tj/sanad_<id>.html. Номера статей и числа остаются из официального текста,
а перевод проверяется: каждое число исходной статьи обязано встретиться в переводе, иначе статья переводится повторно.

    python python/tools/translate_official_code.py <sanadID> <slug> [--limit N] [--max-gap N]   # max-gap: допустимый пропуск номеров (по умолчанию 3, УИК — 12)
    python python/tools/build_translated_codes.py            # собрать .docx для индекса

Результат: storage/app/official/derived/translated/<slug>.json
"""
import html
import json
import os
import re
import sys
import time
import urllib.request
from concurrent.futures import ThreadPoolExecutor

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
OFF = os.path.join(ROOT, "storage", "app", "official", "base_mmk_tj")
OUT_DIR = os.path.join(ROOT, "storage", "app", "official", "derived", "translated")
BATCH_CHARS = 6500


def api_key():
    for line in open(os.path.join(ROOT, ".env"), encoding="utf-8"):
        if line.startswith("OPENAI_API_KEY="):
            return line.split("=", 1)[1].strip()
    raise SystemExit("OPENAI_API_KEY не найден в .env")


KEY = api_key()
MODEL = os.environ.get("TRANSLATE_MODEL", "gpt-6-luna")


def doc_text(path):
    raw = open(path, encoding="utf-8", errors="replace").read()
    raw = re.sub(r"<script.*?</script>|<style.*?</style>", "", raw, flags=re.S)
    raw = raw[raw.find("Матн"):]
    raw = re.sub(r"</(p|div|tr|li|h\d)>|<br\s*/?>", "\n", raw, flags=re.I)
    text = html.unescape(re.sub(r"<[^>]+>", " ", raw)).replace("\xa0", " ")
    text = re.sub(r"[ \t]+", " ", text)
    return re.sub(r"\n\s*\n+", "\n", text).strip()


# Строка оглавления: «Моддаи 5. Название … 14» — есть название после номера и номер страницы в конце. «Моддаи 1» без названия — не оглавление.
TOC_LINE_RE = re.compile(r"(?:Модда(?:и)?|БОБИ)[ 	]+\d+(?:[ 	]*-[ 	]*\d+)?[ 	]*\.[ 	]*\S.*[ 	]\d{1,3}[ 	]*$", re.I)
HEAD_RE = re.compile(r"(?m)^[ 	]*Модда(?:и)?[ 	]+(\d+)(?:-(\d+))?(\.?)[ 	]*(.*)$")   # [ 	], не \s: иначе название «съедает» следующую строку
CHAPTER_RE = re.compile(r"(?m)^[ 	]*БОБИ[ 	]+(\d+)(?:-(\d+))?\.?[ 	]*(.*)$")


def parse(text, max_gap=3):
    """[(номер, заголовок, текст)] и [(позиция, глава)]. Заголовок принимается, только если номер идёт следом за предыдущим."""
    heads, last = [], 0
    for m in HEAD_RE.finditer(text):
        # Оглавление в начале документа: те же «Моддаи N. Название», но в конце строки номер страницы. Его пропускаем.
        if TOC_LINE_RE.search(m.group(0)):
            continue
        base, sub = int(m.group(1)), m.group(2)
        if sub:
            ok = base == last
        else:
            # допускаем пропуски номеров (утратившие силу статьи: у УИК нет статей 62–69); повторы того же номера и «откаты» отбрасываем
            ok = last + 1 <= base <= last + max_gap or (last == 0 and base == 1)
        if not ok:
            continue
        if not sub:
            last = base
        heads.append(m)
    articles = []
    for i, m in enumerate(heads):
        end = heads[i + 1].start() if i + 1 < len(heads) else len(text)
        number = m.group(1) + ("-" + m.group(2) if m.group(2) else "")
        body = text[m.end():end]
        # хвост документа (подпись, колонтитул) в последнюю статью не берём
        body = re.split(r"\n\s*©\d{4}|\n\s*Кофтукоб", body)[0]
        articles.append({"n": number, "title_tj": m.group(4).strip(), "pos": m.start(),
                         "text_tj": "\n".join(line.strip() for line in body.splitlines() if line.strip())})
    chapters = [{"pos": m.start(), "n": m.group(1) + ("-" + m.group(2) if m.group(2) else ""), "title_tj": m.group(3).strip()}
                for m in CHAPTER_RE.finditer(text) if not TOC_LINE_RE.search(m.group(0))]
    return articles, chapters


def ask(prompt, max_tokens=9000):
    body = json.dumps({"model": MODEL, "messages": [{"role": "user", "content": prompt}],
                       "response_format": {"type": "json_object"}, "reasoning_effort": "none",
                       "max_completion_tokens": max_tokens}).encode()
    for attempt in range(1, 5):
        try:
            req = urllib.request.Request("https://api.openai.com/v1/chat/completions", data=body,
                                         headers={"Authorization": "Bearer " + KEY, "Content-Type": "application/json"})
            r = json.load(urllib.request.urlopen(req, timeout=300))
            return json.loads(r["choices"][0]["message"]["content"])
        except Exception as e:  # noqa: BLE001
            print("   повтор", attempt, type(e).__name__, str(e)[:90], flush=True)
            time.sleep(4 * attempt)
    return {}


RULES = (
    "Переведи фрагменты официального текста законодательства Республики Таджикистан с таджикского на русский язык точно и полно, "
    "как в официальных русских текстах законов РТ. Не сокращай, не пересказывай, ничего не добавляй и не объясняй. "
    "Сохрани нумерацию частей и пунктов, абзацы (как переносы строк \\n), все числа, суммы, проценты, даты, сроки, номера статей и законов. "
    "Термины: «нишондиҳанда барои ҳисобҳо» — «показатель для расчётов»; «ҷарима» — «штраф»; «муҷозот» — «санкция»; "
    "«Ҷумҳурии Тоҷикистон» — «Республика Таджикистан»; «Кодекси мазкур» — «настоящий Кодекс»; «қисми» — «часть»; «банд» — «пункт»; "
    "«сархат» — «абзац»; «моддаи» — «статья». Ссылки на законы оставляй с их названиями и номерами."
)


def digits(s):
    return set(re.findall(r"\d+", s or ""))


def translate_batch(batch, strict=False):
    items = [{"n": a["n"], "title": a["title_tj"], "text": a["text_tj"]} for a in batch]
    rule_title = ("\nЕсли у статьи исходное название ПУСТОЕ, в поле title верни пустую строку, а ВЕСЬ текст (включая первое предложение) переведи в text.")
    extra_title = rule_title
    extra = ("\nВНИМАНИЕ: в прошлый раз часть чисел пропала из перевода. Перенеси КАЖДОЕ число из исходного текста в перевод." if strict else "")
    out = ask(RULES + extra_title + extra + "\n\nФрагменты (статьи): " + json.dumps(items, ensure_ascii=False) +
              '\n\nОтвет — только JSON: {"items":[{"n":"…","title":"название статьи по-русски или пустая строка","text":"полный перевод текста статьи"}]}, '
              "по одному элементу на статью, в том же порядке, с теми же номерами n.")
    return {str(x.get("n")): x for x in out.get("items", [])}


def main():
    sid, slug = sys.argv[1], sys.argv[2]
    limit = int(sys.argv[sys.argv.index("--limit") + 1]) if "--limit" in sys.argv else None
    text = doc_text(os.path.join(OFF, "sanad_%s.html" % sid))
    max_gap = int(sys.argv[sys.argv.index("--max-gap") + 1]) if "--max-gap" in sys.argv else 3
    articles, chapters = parse(text, max_gap)
    if limit:
        articles = articles[:limit]
    print("статей:", len(articles), "глав:", len(chapters), "символов:", len(text), flush=True)

    batches, cur, size = [], [], 0
    for a in articles:
        if cur and size + len(a["text_tj"]) > BATCH_CHARS:
            batches.append(cur)
            cur, size = [], 0
        cur.append(a)
        size += len(a["text_tj"])
    if cur:
        batches.append(cur)

    done = {}

    def work(batch):
        res = translate_batch(batch)
        bad = [a for a in batch if str(a["n"]) not in res or not digits(a["text_tj"]) <= digits(res[str(a["n"])].get("text", ""))]
        if bad:
            retry = translate_batch(bad, strict=True)
            for a in bad:
                new = retry.get(str(a["n"]))
                if new and digits(a["text_tj"]) <= digits(new.get("text", "")):
                    res[str(a["n"])] = new
        return res

    started = time.time()
    with ThreadPoolExecutor(3) as pool:
        for k, res in enumerate(pool.map(work, batches), 1):
            done.update(res)
            print("  пакет %d/%d, %.0f c" % (k, len(batches), time.time() - started), flush=True)

    def usable(a):
        t = done.get(str(a["n"]))
        return bool(t and (t.get("text") or "").strip() and digits(a["text_tj"]) <= digits(t.get("text", "")))

    for round_no in (1, 2, 3):
        todo = [a for a in articles if not usable(a)]
        if not todo:
            break
        print("  повтор %d: статей %d" % (round_no, len(todo)), flush=True)
        small = [todo[i:i + 3] for i in range(0, len(todo), 3)]
        with ThreadPoolExecutor(3) as pool:
            for res in pool.map(work, small):
                for key, value in res.items():
                    if (value.get("text") or "").strip():
                        done[key] = value

    result, flagged, missing = [], [], []
    for a in articles:
        t = done.get(str(a["n"]))
        if not t or not (t.get("text") or "").strip():
            missing.append(a["n"])
            continue
        ru = t["text"].strip()
        title_ru = (t.get("title") or "").strip()
        if not a["title_tj"] and title_ru:
            ru, title_ru = (title_ru + " " + ru).strip(), ""
        if not digits(a["text_tj"]) <= digits(ru):
            flagged.append(a["n"])
        result.append({"n": a["n"], "pos": a["pos"], "title_ru": title_ru, "text_ru": ru,
                       "title_tj": a["title_tj"], "numbers_ok": digits(a["text_tj"]) <= digits(ru)})

    # названия глав
    chap = []
    if chapters:
        items = [{"i": i, "n": c["n"], "tj": c["title_tj"]} for i, c in enumerate(chapters)]
        out = ask("Переведи названия глав кодекса Республики Таджикистан с таджикского на русский, кратко и точно. "
                  "Главы: " + json.dumps(items, ensure_ascii=False) + '\nОтвет — JSON: {"items":[{"i":0,"ru":"..."}]}.')
        names = {int(x["i"]): str(x["ru"]).strip() for x in out.get("items", [])}
        chap = [{"pos": c["pos"], "n": c["n"], "title_ru": names.get(i, c["title_tj"])} for i, c in enumerate(chapters)]

    os.makedirs(OUT_DIR, exist_ok=True)
    path = os.path.join(OUT_DIR, slug + ".json")
    json.dump({"sanad_id": sid, "slug": slug, "model": MODEL, "built": time.strftime("%Y-%m-%d %H:%M:%S"),
               "articles": result, "chapters": chap, "missing": missing, "numbers_flagged": flagged},
              open(path, "w", encoding="utf-8"), ensure_ascii=False, indent=1)
    print("готово: статей %d, не переведено %d, с расхождением чисел %d, %.0f c -> %s" % (
        len(result), len(missing), len(flagged), time.time() - started, path))


if __name__ == "__main__":
    sys.stdout.reconfigure(encoding="utf-8")
    main()
