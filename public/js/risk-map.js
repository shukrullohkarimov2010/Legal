/* Карта рисков договора: текст с подсветкой, панель разбора риска и безопасная формулировка пункта.
 *
 * Страница анализа вызывает RiskMap.render(карта, { api, codexUrl }), когда сервер прислал итог,
 * и RiskMap.reset() при сбросе. Текст договора вставляется только через textContent: договор —
 * чужой документ, и разметка из него не должна попасть в страницу.
 */
(function () {
    'use strict';

    var TEXT = {
        ru: {
            title: 'Карта рисков договора',
            sub: function (c) { return 'Нажмите на подсвеченный пункт: объясним риск, укажем норму и предложим безопасную формулировку. Найдено: ' + c; },
            translated: 'Договор был на таджикском: подсветка показана по русскому переводу, который использовался для анализа.',
            all: 'Все', high: 'Критические', medium: 'Предупреждения', low: 'Информация',
            fixed: 'Исправлено', of: 'из',
            empty: 'Выберите подсвеченный пункт слева', emptyHint: 'Красный — серьёзный риск, жёлтый — предупреждение, синий — информация.',
            quote: 'Фрагмент договора', why: 'Почему это риск', norm: 'Норма закона', fixHint: 'Как исправить',
            rewrite: 'Безопасная формулировка', writing: 'Составляю формулировку…', apply: 'Применить в тексте', undo: 'Вернуть как было',
            copy: 'Копировать', copied: 'Скопировано', suggested: 'Предлагаемая формулировка', applied: 'Применено в тексте',
            normText: 'Текст нормы', hideNormText: 'Скрыть текст нормы', article: 'ст.',
            unplaced: 'Не удалось найти место в тексте:', truncated: 'Договор длинный: показана только его первая часть.',
            error: 'Не удалось составить формулировку. Повторите попытку.',
            disclaimer: 'Формулировка подготовлена ИИ и не заменяет проверку юристом.',
            sev: { high: 'Критический риск', medium: 'Предупреждение', low: 'Информация' },
        },
        tg: {
            title: 'Харитаи хавфҳои шартнома',
            sub: function (c) { return 'Банди ранга пахшро пахш кунед: хавфро шарҳ медиҳем, меъёри қонунро нишон медиҳем ва навиштаи бехатарро пешниҳод мекунем. Ёфт шуд: ' + c; },
            translated: 'Шартнома ба забони тоҷикӣ буд: ранг аз рӯи тарҷумаи русӣ, ки барои таҳлил истифода шуд, нишон дода мешавад.',
            all: 'Ҳама', high: 'Ҷиддӣ', medium: 'Огоҳӣ', low: 'Маълумот',
            fixed: 'Ислоҳ шуд', of: 'аз',
            empty: 'Бандеро аз чап интихоб кунед', emptyHint: 'Сурх — хавфи ҷиддӣ, зард — огоҳӣ, кабуд — маълумот.',
            quote: 'Порчаи шартнома', why: 'Чаро ин хавф аст', norm: 'Меъёри қонун', fixHint: 'Чӣ тавр ислоҳ кардан',
            rewrite: 'Навиштаи бехатар', writing: 'Навишта таҳия мешавад…', apply: 'Дар матн татбиқ кардан', undo: 'Баргардонидан',
            copy: 'Нусха гирифтан', copied: 'Нусха шуд', suggested: 'Навиштаи пешниҳодшуда', applied: 'Дар матн татбиқ шуд',
            normText: 'Матни меъёр', hideNormText: 'Пинҳон кардани матн', article: 'м.',
            unplaced: 'Ҷойи он дар матн ёфт нашуд:', truncated: 'Шартнома дароз аст: танҳо қисми аввали он нишон дода шудааст.',
            error: 'Навишта таҳия нашуд. Бори дигар кӯшиш кунед.',
            disclaimer: 'Навишта аз ҷониби ҳуш таҳия шудааст ва санҷиши ҳуқуқшиносро иваз намекунад.',
            sev: { high: 'Хавфи ҷиддӣ', medium: 'Огоҳӣ', low: 'Маълумот' },
        },
    };
    var SEVERITIES = ['high', 'medium', 'low'];
    var ICON = { high: '●', medium: '●', low: '●' };

    var st = null;      // состояние текущей карты
    var cfg = {};

    function el(tag, cls, text) {
        var node = document.createElement(tag);
        if (cls) node.className = cls;
        if (text != null) node.textContent = text;
        return node;
    }

    function root() { return document.getElementById('riskMapSection'); }

    function reset() {
        var host = root();
        if (host) { host.classList.add('hidden'); host.replaceChildren(); }
        st = null;
    }

    function render(map, options) {
        cfg = options || {};
        var host = root();
        if (!host) return;
        if (!map || !Array.isArray(map.paragraphs) || !map.paragraphs.length || !Array.isArray(map.findings) || !map.findings.length) {
            reset();
            return;
        }
        st = { map: map, t: TEXT[map.lang === 'tg' ? 'tg' : 'ru'], filter: 'all', active: null, fixes: {}, applied: {}, refs: {} };
        host.replaceChildren();
        host.classList.remove('hidden');
        host.appendChild(build());
        layoutTicks();
        updateProgress();
    }

    // ------------------------------------------------------------------ каркас

    function build() {
        var t = st.t, map = st.map;
        var wrap = el('div', 'rm');

        var head = el('div', 'rm-head');
        var titles = el('div');
        titles.appendChild(el('div', 'rm-title', '🗺 ' + t.title));
        titles.appendChild(el('div', 'rm-sub', t.sub(map.findings.length)));
        head.appendChild(titles);
        head.appendChild(buildFilters());
        wrap.appendChild(head);

        if (map.lang === 'tg') wrap.appendChild(el('div', 'rm-note', t.translated));
        if (map.truncated) wrap.appendChild(el('div', 'rm-note', t.truncated));

        var progress = el('div', 'rm-progress');
        progress.appendChild(el('span', null, ''));
        var bar = el('div', 'rm-bar');
        bar.appendChild(el('span'));
        progress.appendChild(bar);
        st.refs.progressText = progress.firstChild;
        st.refs.progressBar = bar.firstChild;
        wrap.appendChild(progress);

        var grid = el('div', 'rm-grid');
        grid.appendChild(buildDoc());
        st.refs.panel = el('aside', 'rm-panel');
        showEmpty();
        grid.appendChild(st.refs.panel);
        wrap.appendChild(grid);

        if (map.unplaced && map.unplaced.length) {
            var box = el('div', 'rm-unplaced', t.unplaced);
            map.unplaced.forEach(function (id) {
                var b = el('button', null, '• ' + map.findings[id].title);
                b.type = 'button';
                b.addEventListener('click', function () { select(id); });
                box.appendChild(b);
            });
            wrap.appendChild(box);
        }
        return wrap;
    }

    function buildFilters() {
        var t = st.t, counts = st.map.counts || {};
        var box = el('div', 'rm-filters');
        st.refs.filters = {};
        ['all'].concat(SEVERITIES).forEach(function (key) {
            if (key !== 'all' && !counts[key]) return;
            var btn = el('button', 'rm-filter ' + key);
            btn.type = 'button';
            btn.setAttribute('aria-pressed', key === 'all' ? 'true' : 'false');
            if (key !== 'all') btn.appendChild(el('span', 'rm-dot'));
            btn.appendChild(el('span', null, t[key] + (key === 'all' ? '' : ' ' + counts[key])));
            btn.addEventListener('click', function () { setFilter(key); });
            st.refs.filters[key] = btn;
            box.appendChild(btn);
        });
        return box;
    }

    function setFilter(key) {
        st.filter = key;
        Object.keys(st.refs.filters).forEach(function (k) { st.refs.filters[k].setAttribute('aria-pressed', String(k === key)); });
        st.map.paragraphs.forEach(function (row) {
            var node = st.refs['p' + row.i];
            node.classList.toggle('dim', key !== 'all' && row.severity !== key);
        });
        Object.keys(st.refs).forEach(function (k) {
            if (k.indexOf('t') === 0 && k.length > 1 && !isNaN(k.slice(1))) {
                var tick = st.refs[k];
                tick.hidden = key !== 'all' && tick.dataset.sev !== key;
            }
        });
    }

    // ------------------------------------------------------------------ текст договора

    function buildDoc() {
        var wrap = el('div', 'rm-doc-wrap');
        var doc = el('div', 'rm-doc');
        doc.style.position = 'relative';
        st.refs.doc = doc;
        st.map.paragraphs.forEach(function (row) {
            var p = el('div', 'rm-p');
            st.refs['p' + row.i] = p;
            paintParagraph(p, row);
            if (row.findings && row.findings.length) {
                p.tabIndex = 0;
                p.setAttribute('role', 'button');
                p.addEventListener('click', function () { select(row.findings[0]); });
                p.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); select(row.findings[0]); }
                });
            }
            doc.appendChild(p);
        });
        wrap.appendChild(doc);
        var mini = el('div', 'rm-mini');
        st.refs.mini = mini;
        wrap.appendChild(mini);
        return wrap;
    }

    function paintParagraph(node, row) {
        node.replaceChildren();
        node.className = 'rm-p';
        var fixed = Object.prototype.hasOwnProperty.call(st.applied, row.i);
        if (row.findings && row.findings.length) {
            node.classList.add('has', row.severity || 'medium');
            if (fixed) node.classList.add('fixed');
        }
        if (st.active != null && st.map.findings[st.active].para === row.i) node.classList.add('active');
        if (st.filter !== 'all' && row.severity !== st.filter) node.classList.add('dim');
        if (fixed) { node.textContent = st.applied[row.i]; return; }

        var spans = (row.spans || []).slice().sort(function (a, b) { return a.s - b.s; });
        var pos = 0;
        spans.forEach(function (span) {
            if (span.s < pos || span.e > row.text.length) return;
            if (span.s > pos) node.appendChild(document.createTextNode(row.text.slice(pos, span.s)));
            var mark = el('mark', 'rm-mark', row.text.slice(span.s, span.e));
            node.appendChild(mark);
            pos = span.e;
        });
        node.appendChild(document.createTextNode(row.text.slice(pos)));
    }

    function layoutTicks() {
        var mini = st.refs.mini, doc = st.refs.doc;
        if (!mini || !doc) return;
        requestAnimationFrame(function () {
            if (!st || st.refs.mini !== mini) return;
            mini.replaceChildren();
            var total = doc.scrollHeight || 1;
            st.map.paragraphs.forEach(function (row) {
                if (!row.findings || !row.findings.length) return;
                var node = st.refs['p' + row.i];
                var fixed = Object.prototype.hasOwnProperty.call(st.applied, row.i);
                var tick = el('button', 'rm-tick ' + (fixed ? 'fixed' : row.severity));
                tick.type = 'button';
                tick.dataset.sev = row.severity;
                tick.hidden = st.filter !== 'all' && row.severity !== st.filter;
                tick.style.top = Math.min(98, (node.offsetTop / total) * 100) + '%';
                tick.title = st.map.findings[row.findings[0]].title;
                tick.addEventListener('click', function () { select(row.findings[0]); });
                st.refs['t' + row.i] = tick;
                mini.appendChild(tick);
            });
        });
    }

    // ------------------------------------------------------------------ панель разбора

    function showEmpty() {
        var panel = st.refs.panel, t = st.t;
        panel.replaceChildren();
        var box = el('div', 'rm-empty');
        box.appendChild(el('div', null, '👈'));
        box.appendChild(el('b', null, t.empty));
        box.appendChild(el('div', null, t.emptyHint));
        panel.appendChild(box);
    }

    function select(id) {
        var finding = st.map.findings[id];
        if (!finding) return;
        st.active = id;
        st.map.paragraphs.forEach(function (row) {
            st.refs['p' + row.i].classList.toggle('active', row.i === finding.para);
        });
        if (finding.para != null) {
            var node = st.refs['p' + finding.para], doc = st.refs.doc;
            doc.scrollTo({ top: Math.max(0, node.offsetTop - doc.clientHeight / 2 + node.clientHeight / 2), behavior: 'smooth' });
        }
        renderPanel(finding);
        if (window.matchMedia && window.matchMedia('(max-width: 900px)').matches) {
            st.refs.panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function section(panel, label, content) {
        panel.appendChild(el('div', 'rm-label', label));
        panel.appendChild(content);
    }

    function renderPanel(finding) {
        var t = st.t, panel = st.refs.panel;
        panel.replaceChildren();
        var row = finding.para != null ? st.map.paragraphs[finding.para] : null;
        var fixed = row && Object.prototype.hasOwnProperty.call(st.applied, row.i);

        var tabs = row && row.findings && row.findings.length > 1 ? row.findings : null;
        if (tabs) {
            var bar = el('div', 'rm-tabs');
            tabs.forEach(function (id, index) {
                var tab = el('button', 'rm-tab', String(index + 1) + '. ' + st.map.findings[id].title.slice(0, 28));
                tab.type = 'button';
                tab.setAttribute('aria-pressed', String(id === finding.id));
                tab.addEventListener('click', function () { select(id); });
                bar.appendChild(tab);
            });
            panel.appendChild(bar);
        }

        var sev = el('span', 'rm-sev ' + (fixed ? 'fixed' : finding.severity), (fixed ? '✓ ' + t.applied : ICON[finding.severity] + ' ' + t.sev[finding.severity]));
        panel.appendChild(sev);
        panel.appendChild(el('div', 'rm-ptitle', finding.title));

        if (finding.fragment) {
            var quote = el('div', 'rm-quote', '«' + finding.fragment + '»');
            quote.style.setProperty('--c', 'var(--rm-' + finding.severity + ')');
            section(panel, t.quote, quote);
        }
        if (finding.why) section(panel, t.why, el('div', 'rm-text', finding.why));
        if (finding.norm) section(panel, t.norm, buildNorms(finding));
        if (finding.fix) section(panel, t.fixHint, el('div', 'rm-text', finding.fix));

        var actions = el('div', 'rm-actions');
        if (row) {
            if (fixed) {
                var undo = el('button', 'rm-btn ghost', '↶ ' + t.undo);
                undo.type = 'button';
                undo.addEventListener('click', function () { setApplied(row.i, null); renderPanel(finding); });
                actions.appendChild(undo);
            } else {
                var rewrite = el('button', 'rm-btn', '✨ ' + t.rewrite);
                rewrite.type = 'button';
                rewrite.addEventListener('click', function () { requestRewrite(finding, row, rewrite, panel); });
                actions.appendChild(rewrite);
            }
        }
        panel.appendChild(actions);
        if (row && !fixed && st.fixes[finding.id]) showFix(panel, finding, row, st.fixes[finding.id]);
    }

    function buildNorms(finding) {
        var t = st.t, box = el('div');
        var list = el('div', 'rm-norms');
        var cites = finding.citations || [];
        if (cites.length) {
            cites.forEach(function (c) {
                var linkable = c.code_id && c.status !== 'missing';
                var chip = el(linkable ? 'a' : 'span', 'rm-norm ' + (c.status === 'verified' ? '' : c.status),
                    (c.status === 'verified' ? '✓ ' : '⚠ ') + c.code + ', ' + t.article + ' ' + c.article);
                if (linkable && cfg.codexUrl) {
                    chip.href = cfg.codexUrl + '?' + new URLSearchParams({ code: c.code_id, art: c.article });
                    chip.target = '_blank';
                    chip.rel = 'noopener';
                }
                list.appendChild(chip);
            });
        } else {
            list.appendChild(el('span', 'rm-norm plain', finding.norm));
        }
        box.appendChild(list);
        if (finding.norm_text) {
            var body = el('div', 'rm-normtext', finding.norm_text);
            body.hidden = true;
            var toggle = el('button', 'rm-btn ghost', t.normText);
            toggle.type = 'button';
            toggle.style.marginTop = '8px';
            toggle.addEventListener('click', function () {
                body.hidden = !body.hidden;
                toggle.textContent = body.hidden ? t.normText : t.hideNormText;
            });
            box.appendChild(toggle);
            box.appendChild(body);
        }
        return box;
    }

    // ------------------------------------------------------------------ безопасная формулировка

    function requestRewrite(finding, row, button, panel) {
        var t = st.t;
        var old = panel.querySelector('.rm-err');
        if (old) old.remove();
        button.disabled = true;
        button.textContent = '⏳ ' + t.writing;
        fetch((cfg.api || '') + '/api/clause/rewrite', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                clause: row.text, title: finding.title, why: finding.why, fix: finding.fix,
                norm: finding.norm, lang: st.map.lang,
            }),
        }).then(function (res) {
            return res.json().then(function (data) { return { ok: res.ok, data: data }; });
        }).then(function (r) {
            if (!r.ok || !r.data.text) throw new Error(r.data.error || 'error');
            st.fixes[finding.id] = r.data.text;
            if (st.active === finding.id) renderPanel(finding);
        }).catch(function () {
            button.disabled = false;
            button.textContent = '✨ ' + t.rewrite;
            panel.appendChild(el('div', 'rm-err', t.error));
        });
    }

    function showFix(panel, finding, row, text) {
        var t = st.t;
        var card = el('div', 'rm-fix');
        card.appendChild(el('div', 'rm-fix-head', '✨ ' + t.suggested));
        card.appendChild(el('div', 'rm-fix-body', text));
        panel.appendChild(card);

        var actions = el('div', 'rm-actions');
        var apply = el('button', 'rm-btn ok', '✓ ' + t.apply);
        apply.type = 'button';
        apply.addEventListener('click', function () { setApplied(row.i, text); renderPanel(finding); });
        var copy = el('button', 'rm-btn ghost', '⧉ ' + t.copy);
        copy.type = 'button';
        copy.addEventListener('click', function () {
            var done = function () { copy.textContent = '✓ ' + t.copied; setTimeout(function () { copy.textContent = '⧉ ' + t.copy; }, 1500); };
            if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(text).then(done, function () {});
        });
        actions.appendChild(apply);
        actions.appendChild(copy);
        panel.appendChild(actions);
        panel.appendChild(el('div', 'rm-note', t.disclaimer));
    }

    function setApplied(index, text) {
        if (text == null) delete st.applied[index]; else st.applied[index] = text;
        var row = st.map.paragraphs[index];
        paintParagraph(st.refs['p' + index], row);
        var tick = st.refs['t' + index];
        if (tick) tick.className = 'rm-tick ' + (text == null ? row.severity : 'fixed');
        updateProgress();
    }

    function updateProgress() {
        if (!st) return;
        var total = st.map.findings.length, done = 0;
        st.map.findings.forEach(function (f) {
            if (f.para != null && Object.prototype.hasOwnProperty.call(st.applied, f.para)) done += 1;
        });
        st.refs.progressText.textContent = st.t.fixed + ': ' + done + ' ' + st.t.of + ' ' + total;
        st.refs.progressBar.style.width = (total ? Math.round((done / total) * 100) : 0) + '%';
    }

    window.RiskMap = { render: render, reset: reset };
})();
