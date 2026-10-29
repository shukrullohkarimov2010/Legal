/*
 * Слой перевода интерфейса на таджикский.
 *
 * Страницы проекта написаны по-русски, часть текста строится в JavaScript уже после
 * загрузки. Этот скрипт подключается сервером только при выбранном языке «tg»
 * (App\Http\Middleware\InjectTajikRuntime) и подменяет русский текст на таджикский по
 * словарю lang/tg/phrases.json: сразу при загрузке и затем при каждом изменении страницы.
 *
 * Правила словаря:
 *  - ключ — русская строка целиком (пробелы схлопнуты), значение — перевод;
 *  - {0}, {1} — подставляемые значения (числа, имена): «Найдено норм: {0}»;
 *  - строки, которых нет в словаре, остаются как есть (пользовательские данные, отчёты).
 *
 * Снаружи доступен window.I18N_TG.t(строка) — для кода, который собирает текст сам.
 */
(function () {
  'use strict';

  var DICT = window.__I18N_TG || {};
  var CYR = /[А-Яа-яЁё]/;
  var exact = Object.create(null);
  var patterns = [];
  var cache = new Map();

  function collapse(s) { return s.replace(/\s+/g, ' ').trim(); }
  function escapeRe(s) { return s.replace(/[.*+?^${}()|[\]\\\/]/g, '\\$&'); }

  Object.keys(DICT).forEach(function (key) {
    var value = DICT[key];
    if (!/\{\d+\}/.test(key)) { exact[key] = value; return; }
    var parts = key.split(/\{(\d+)\}/);          // текст, номер, текст, номер, ...
    var literal = 0, source = '^';
    for (var i = 0; i < parts.length; i++) {
      if (i % 2 === 0) { source += escapeRe(parts[i]); literal += parts[i].trim().length; }
      else { source += '(.+?)'; }
    }
    // шаблон из одних плейсхолдеров совпал бы с чем угодно
    if (literal < 3) return;
    patterns.push({ re: new RegExp(source + '$'), value: value, literal: literal });
  });
  patterns.sort(function (a, b) { return b.literal - a.literal; });

  function translate(raw) {
    var core = collapse(raw);
    if (!core || !CYR.test(core)) return null;
    if (cache.has(core)) return cache.get(core);
    var result = null;
    if (exact[core] !== undefined) {
      result = exact[core];
    } else {
      for (var i = 0; i < patterns.length; i++) {
        var m = patterns[i].re.exec(core);
        if (m) {
          result = patterns[i].value.replace(/\{(\d+)\}/g, function (all, n) {
            var v = m[parseInt(n, 10) + 1];
            return v === undefined ? all : v;
          });
          break;
        }
      }
    }
    if (cache.size > 4000) cache.clear();
    cache.set(core, result);
    return result;
  }

  // Пробелы по краям исходной строки сохраняются: «Всего: » + <b>5</b> не должно слипнуться.
  function keepEdges(raw, value) {
    return raw.match(/^\s*/)[0] + value + raw.match(/\s*$/)[0];
  }

  var SKIP_TAGS = { SCRIPT: 1, STYLE: 1, NOSCRIPT: 1, TEXTAREA: 1, TEMPLATE: 1, PRE: 1 };
  var ATTRS = ['placeholder', 'title', 'aria-label', 'alt'];
  // Отчёты, договоры и ответы модели уже написаны на языке документа (python/doc_lang.py).
  // Словарь интерфейса по ним не ходит: он ломает русский отчёт, подставляя «Миёна»,
  // «Банди договора» и «шартномаи меҳнатӣ» в отдельные слова и подписи.
  var SKIP_SELECTOR = '[translate="no"], .cab-md, .markdown-body, .markdown-content, .contract-box';

  // Текст внутри <textarea>, <pre> и скриптов не трогаем (там вводит или вставляет сам пользователь),
  // а подсказки и подписи самих полей (placeholder, aria-label) — переводим.
  function skipped(el) {
    return !el || SKIP_TAGS[el.nodeName] === 1 || (el.closest && el.closest(SKIP_SELECTOR));
  }
  function skippedAttrs(el) {
    return !el || (el.closest && el.closest(SKIP_SELECTOR));
  }

  function handleText(node) {
    var parent = node.parentNode;
    if (skipped(parent)) return;
    var value = translate(node.nodeValue);
    if (value !== null) {
      var next = keepEdges(node.nodeValue, value);
      if (next !== node.nodeValue) node.nodeValue = next;
    }
  }

  function handleAttrs(el) {
    if (el.nodeType !== 1 || skippedAttrs(el)) return;
    for (var i = 0; i < ATTRS.length; i++) {
      var name = ATTRS[i];
      if (!el.hasAttribute(name)) continue;
      var old = el.getAttribute(name);
      var value = translate(old);
      if (value !== null && value !== old) el.setAttribute(name, value);
    }
    if (el.nodeName === 'INPUT' && /^(submit|button|reset)$/i.test(el.type) && el.value) {
      var v = translate(el.value);
      if (v !== null && v !== el.value) el.value = v;
    }
  }

  function walk(root) {
    if (!root) return;
    if (root.nodeType === 3) { handleText(root); return; }
    if (root.nodeType !== 1) return;
    handleAttrs(root);
    var walker = document.createTreeWalker(root, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT);
    var n;
    while ((n = walker.nextNode())) {
      if (n.nodeType === 3) handleText(n); else handleAttrs(n);   // запреты проверяют сами обработчики
    }
  }

  function translateTitle() {
    var v = translate(document.title || '');
    if (v !== null && v !== document.title) document.title = v;
  }

  var queue = [], scheduled = false;
  function flush() {
    scheduled = false;
    var items = queue; queue = [];
    for (var i = 0; i < items.length; i++) {
      var rec = items[i];
      if (rec.type === 'childList') { for (var j = 0; j < rec.addedNodes.length; j++) walk(rec.addedNodes[j]); }
      else if (rec.type === 'characterData') handleText(rec.target);
      else if (rec.type === 'attributes') handleAttrs(rec.target);
    }
    translateTitle();
  }

  function start() {
    document.documentElement.lang = 'tg';
    walk(document.body);
    translateTitle();
    document.documentElement.classList.remove('i18n-wait');

    new MutationObserver(function (records) {
      for (var i = 0; i < records.length; i++) queue.push(records[i]);
      if (!scheduled) { scheduled = true; (window.requestAnimationFrame || setTimeout)(flush); }
    }).observe(document.documentElement, {
      childList: true, subtree: true, characterData: true, attributes: true, attributeFilter: ATTRS,
    });
  }

  window.I18N_TG = {
    t: function (s) { var v = translate(String(s)); return v === null ? s : keepEdges(String(s), v); },
  };

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
  else start();
})();
