/* Симулятор суда: выбор дела, зал заседания, оценка по ходу и вердикт.
 *
 * Конфигурация приходит из шаблона в window.COURT: { api, lang, codexUrl }.
 * Всё, что написали пользователь и модель, вставляется через textContent.
 */
(function () {
    'use strict';

    var TEXT = {
        ru: {
            kinds: { criminal: 'Уголовное', civil: 'Гражданское', labor: 'Трудовое' },
            start: 'Начать заседание', goal: 'Цель', role: 'Ваша роль',
            speakers: { judge: 'Судья', opponent: null, witness: 'Свидетель', me: 'Вы' },
            actions: { argue: '💬 Выступить', object: '✋ Возражение', witness: '🧍 Вопрос свидетелю' },
            placeholders: {
                argue: 'Изложите позицию. Ссылайтесь на статьи кодексов: система проверит их по базе.',
                object: 'Заявите возражение и назовите основание, например: «Возражаю: вопрос наводящий».',
                witness: 'Задайте свидетелю вопрос по обстоятельствам дела.',
            },
            tags: { object: '✋ Возражение', witness: '🧍 Вопрос свидетелю' },
            addressee: 'Вы обращаетесь к', send: 'Отправить', sending: 'Суд слушает…', finish: 'Завершить заседание', hint: 'Ctrl + Enter — отправить',
            brief: 'Материалы дела', round: function (n, max) { return 'Раунд ' + n + ' из ' + max; }, roundIdle: 'Заседание началось',
            score: 'Текущий балл', scoreIdle: 'после первой реплики',
            criteria: { legal_basis: 'Юридическое обоснование', logic: 'Логика', evidence: 'Работа с доказательствами', procedure: 'Процессуальный порядок' },
            lastTurn: 'Оценка последней реплики', cited: 'Ваши ссылки на статьи', noCites: 'В этой реплике статей не названо.',
            showHint: '💡 Показать подсказку', cite: { verified: 'по делу', exists: 'не по делу', wrong_code: 'другой кодекс', missing: 'нет в базе' },
            opening: function (c) { return 'Заседание объявляется открытым. Рассматривается дело: «' + c.title + '». Слово предоставляется стороне: ' + c.role + '.'; },
            youAre: function (c) { return 'Вы — ' + c.role + '. ' + c.goal; },
            maxReached: 'Достигнут предел раундов. Завершите заседание, чтобы услышать решение суда.',
            errTurn: 'Не удалось получить ответ суда. Повторите реплику.', errCases: 'Не удалось загрузить дела. Проверьте, запущен ли сервер.',
            deliberating: 'Суд удаляется на совещание…', errVerdict: 'Не удалось вынести решение. Повторите.',
            verdictTitle: 'Решение суда', outcomes: { won: 'Дело выиграно', partial: 'Выиграно частично', lost: 'Дело проиграно' },
            grades: { excellent: 'Отличная работа', good: 'Хорошая работа', fair: 'Есть над чем поработать', weak: 'Нужна серьёзная подготовка' },
            breakdown: 'Оценка по критериям', strengths: 'Что получилось', mistakes: 'Над чем поработать', study: 'Что изучить', articles: 'Статьи по этому делу',
            again: 'Сыграть ещё раз', other: 'Другое дело', article: 'ст.', of100: 'из 100',
            noTurns: 'Сначала выступите хотя бы один раз.',
        },
        tg: {
            kinds: { criminal: 'Ҷиноятӣ', civil: 'Шаҳрвандӣ', labor: 'Меҳнатӣ' },
            start: 'Оғози мурофиа', goal: 'Мақсад', role: 'Нақши шумо',
            speakers: { judge: 'Судя', opponent: null, witness: 'Шоҳид', me: 'Шумо' },
            actions: { argue: '💬 Баромад кардан', object: '✋ Эътироз', witness: '🧍 Савол ба шоҳид' },
            placeholders: {
                argue: 'Мавқеи худро баён кунед. Ба моддаҳои кодексҳо истинод кунед: система онҳоро аз рӯи база месанҷад.',
                object: 'Эътироз баён карда, асосашро ном баред, масалан: «Эътироз дорам: савол роҳнамоӣ мекунад».',
                witness: 'Ба шоҳид оид ба ҳолатҳои парванда савол диҳед.',
            },
            tags: { object: '✋ Эътироз', witness: '🧍 Савол ба шоҳид' },
            addressee: 'Шумо муроҷиат мекунед ба', send: 'Фиристодан', sending: 'Суд гӯш мекунад…', finish: 'Анҷом додани мурофиа', hint: 'Ctrl + Enter — фиристодан',
            brief: 'Маводҳои парванда', round: function (n, max) { return 'Давр ' + n + ' аз ' + max; }, roundIdle: 'Мурофиа оғоз шуд',
            score: 'Холи ҷорӣ', scoreIdle: 'пас аз аввалин баромад',
            criteria: { legal_basis: 'Асосноккунии ҳуқуқӣ', logic: 'Мантиқ', evidence: 'Кор бо далелҳо', procedure: 'Тартиби мурофиавӣ' },
            lastTurn: 'Баҳои баромади охирин', cited: 'Истинодҳои шумо ба моддаҳо', noCites: 'Дар ин баромад моддае ном бурда нашуд.',
            showHint: '💡 Нишон додани маслиҳат', cite: { verified: 'ба парванда мувофиқ', exists: 'ба парванда не', wrong_code: 'кодекси дигар', missing: 'дар база нест' },
            opening: function (c) { return 'Маҷлиси суд кушода эълон мешавад. Парванда баррасӣ мешавад: «' + c.title + '». Суханро ба тараф медиҳам: ' + c.role + '.'; },
            youAre: function (c) { return 'Шумо — ' + c.role + '. ' + c.goal; },
            maxReached: 'Шумораи давраҳо ба охир расид. Мурофиаро анҷом диҳед, то қарори судро шунавед.',
            errTurn: 'Ҷавоби суд гирифта нашуд. Баромадро такрор кунед.', errCases: 'Парвандаҳо бор нашуданд. Санҷед, ки сервер кор мекунад.',
            deliberating: 'Суд ба утоқи машварат меравад…', errVerdict: 'Қарор баровардан муяссар нашуд. Такрор кунед.',
            verdictTitle: 'Қарори суд', outcomes: { won: 'Парванда ғалаба шуд', partial: 'Қисман ғалаба шуд', lost: 'Парванда бохта шуд' },
            grades: { excellent: 'Кори аъло', good: 'Кори хуб', fair: 'Ҷои кор кардан ҳаст', weak: 'Омодагии ҷиддӣ лозим аст' },
            breakdown: 'Баҳо аз рӯи меъёрҳо', strengths: 'Чӣ хуб баромад', mistakes: 'Болои чӣ кор кардан лозим', study: 'Чиро омӯхтан лозим', articles: 'Моддаҳо оид ба ин парванда',
            again: 'Бори дигар бозӣ кардан', other: 'Парвандаи дигар', article: 'м.', of100: 'аз 100',
            noTurns: 'Аввал ақаллан як маротиба баромад кунед.',
        },
    };

    var cfg = window.COURT || {};
    var T = TEXT[cfg.lang === 'tg' ? 'tg' : 'ru'];
    var ICON = { judge: '⚖️', opponent: '🏛️', witness: '🧍', me: '🎓' };
    var CRIT = ['legal_basis', 'logic', 'evidence', 'procedure'];
    var CIRC = 2 * Math.PI * 70;

    var st = { cases: [], c: null, history: [], scores: [], cited: [], action: 'argue', busy: false, turn: 0, max: 10, done: false };
    var $ = function (id) { return document.getElementById(id); };

    function el(tag, cls, text) {
        var node = document.createElement(tag);
        if (cls) node.className = cls;
        if (text != null) node.textContent = text;
        return node;
    }
    function show(name) {
        ['screenCases', 'screenRoom', 'screenVerdict'].forEach(function (id) { $(id).hidden = id !== name; });
        window.scrollTo({ top: 0 });
    }
    function colorFor(score) { return score >= 70 ? 'var(--ok)' : score >= 50 ? 'var(--warn)' : 'var(--bad)'; }
    function api(path, body) {
        var opts = body ? { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(body) } : {};
        return fetch(cfg.api + path, opts).then(function (res) {
            return res.json().then(function (data) { return { ok: res.ok, data: data }; });
        });
    }

    // ------------------------------------------------------------------ выбор дела

    function loadCases() {
        api('/api/court/cases?lang=' + encodeURIComponent(cfg.lang)).then(function (r) {
            if (!r.ok) throw new Error('cases');
            st.cases = r.data.cases;
            renderCases();
        }).catch(function () {
            var box = $('cases');
            box.replaceChildren(el('div', 'err', T.errCases));
        });
    }

    function renderCases() {
        var box = $('cases');
        box.replaceChildren();
        st.cases.forEach(function (c) {
            var card = el('article', 'case');
            card.appendChild(el('span', 'kind ' + c.kind, T.kinds[c.kind] || c.kind));
            card.appendChild(el('h3', null, c.title));
            card.appendChild(el('div', 'role', T.role + ': ' + c.role));
            card.appendChild(el('p', 'facts', c.facts));
            card.appendChild(el('p', 'goal', '🎯 ' + T.goal + ': ' + c.goal));
            var btn = el('button', 'btn', '⚖️ ' + T.start);
            btn.type = 'button';
            btn.addEventListener('click', function () { startCase(c); });
            card.appendChild(btn);
            box.appendChild(card);
        });
    }

    // ------------------------------------------------------------------ зал

    function speakerName(who) {
        if (who === 'opponent') return st.c.opponent;
        return T.speakers[who] || who;
    }

    function startCase(c) {
        st.c = c; st.history = []; st.scores = []; st.cited = []; st.action = 'argue'; st.busy = false; st.turn = 0; st.done = false;
        $('caseTitle').textContent = c.title;
        $('caseRole').textContent = T.role + ': ' + c.role;
        $('briefSummary').textContent = T.brief;
        $('briefText').textContent = c.facts + '\n\n🧍 ' + c.witness;
        $('log').replaceChildren();
        $('input').value = '';
        $('composeError').textContent = '';
        say('system', T.opening(c));
        say('system', T.youAre(c));
        resetSide();
        buildActions();
        updateControls();
        show('screenRoom');
        $('input').focus({ preventScroll: true });
    }

    function say(who, text, tag) {
        var row = el('div', 'msg ' + who);
        if (who === 'system') {
            row.appendChild(el('div', 'bubble', text));
        } else {
            var key = who === 'me' ? 'me' : who;
            var avatar = el('div', 'avatar', ICON[key] || '•');
            var body = el('div');
            body.appendChild(el('div', 'who', who === 'me' ? T.speakers.me : speakerName(who)));
            var bubble = el('div', 'bubble');
            if (tag) bubble.appendChild(el('div', 'tag', tag));
            bubble.appendChild(document.createTextNode(text));
            body.appendChild(bubble);
            row.append(avatar, body);
        }
        $('log').appendChild(row);
        $('log').scrollTop = $('log').scrollHeight;
        return row;
    }

    function typing() {
        var row = el('div', 'msg judge');
        row.appendChild(el('div', 'avatar', '⚖️'));
        var bubble = el('div', 'bubble');
        var dots = el('div', 'typing');
        dots.append(el('i'), el('i'), el('i'));
        bubble.appendChild(dots);
        row.appendChild(bubble);
        $('log').appendChild(row);
        $('log').scrollTop = $('log').scrollHeight;
        return row;
    }

    function buildActions() {
        var box = $('actions');
        box.replaceChildren();
        ['argue', 'object', 'witness'].forEach(function (key) {
            var b = el('button', 'act', T.actions[key]);
            b.type = 'button';
            b.dataset.action = key;
            b.setAttribute('aria-pressed', String(key === st.action));
            b.addEventListener('click', function () {
                st.action = key;
                box.querySelectorAll('.act').forEach(function (x) { x.setAttribute('aria-pressed', String(x.dataset.action === key)); });
                $('input').placeholder = placeholderFor(key);
                $('input').focus({ preventScroll: true });
            });
            box.appendChild(b);
        });
        $('input').placeholder = placeholderFor(st.action);
    }

    // Вопрос свидетелю: в подсказке названо, к кому обращаетесь, чтобы не писать «ты» наугад.
    function placeholderFor(key) {
        return key === 'witness' ? T.placeholders.witness + ' ' + T.addressee + ': ' + st.c.witness + '.' : T.placeholders[key];
    }

    function updateControls() {
        var locked = st.busy || st.done || st.turn >= st.max;
        $('send').disabled = locked;
        $('send').textContent = st.busy ? '⏳ ' + T.sending : '➤ ' + T.send;
        $('input').disabled = st.busy || st.done;
        $('finish').disabled = st.busy || st.turn < 1;
        $('finish').textContent = '🔨 ' + T.finish;
    }

    function send() {
        var text = $('input').value.trim();
        if (st.busy || st.done || text.length < 3 || st.turn >= st.max) return;
        var action = st.action;
        $('composeError').textContent = '';
        var mine = say('me', text, action === 'witness' ? T.tags.witness + ': ' + st.c.witness : T.tags[action]);
        var wait = typing();
        var before = st.history.slice();
        st.busy = true;
        $('input').value = '';
        updateControls();

        api('/api/court/turn', { case_id: st.c.id, history: before, message: text, action: action, lang: cfg.lang }).then(function (r) {
            if (!r.ok) throw new Error(r.data.error || 'turn');
            wait.remove();
            st.history = before.concat([{ speaker: 'user', text: text, action: action }], r.data.lines.map(function (l) { return { speaker: l.speaker, text: l.text }; }));
            st.turn = r.data.turn;
            st.max = r.data.max_turns || st.max;
            st.scores.push(r.data.score);
            (r.data.citations || []).forEach(function (c) {
                var label = c.code + ', ст. ' + c.article;
                if (c.status === 'verified' && st.cited.indexOf(label) < 0) st.cited.push(label);
            });
            return playLines(r.data.lines).then(function () { renderSide(r.data); });
        }).catch(function (e) {
            wait.remove();
            mine.remove();
            $('input').value = text;
            $('composeError').textContent = (e && e.message && e.message !== 'turn') ? e.message : T.errTurn;
        }).then(function () {
            st.busy = false;
            if (st.turn >= st.max) say('system', T.maxReached);
            updateControls();
            if (!$('input').disabled) $('input').focus({ preventScroll: true });
        });
    }

    function playLines(lines) {
        return lines.reduce(function (chain, line) {
            return chain.then(function () {
                say(line.speaker, line.text);
                return new Promise(function (resolve) { setTimeout(resolve, 450); });
            });
        }, Promise.resolve());
    }

    // ------------------------------------------------------------------ боковая панель

    function gauge(host, score) {
        var fill = host.querySelector('.fill');
        host.style.setProperty('--gc', score == null ? 'var(--accent)' : colorFor(score));
        fill.style.strokeDasharray = ((score || 0) / 100) * CIRC + ' 999';
    }

    function resetSide() {
        gauge($('gauge'), null);
        $('gaugeNum').textContent = '—';
        $('gaugeCap').textContent = T.scoreIdle;
        $('round').textContent = T.roundIdle;
        $('feedback').replaceChildren();
        $('bars').replaceChildren();
        $('cites').replaceChildren();
        $('citesCard').hidden = true;
    }

    function average() {
        return st.scores.length ? Math.round(st.scores.reduce(function (a, b) { return a + b; }, 0) / st.scores.length) : null;
    }

    function renderSide(d) {
        var avg = average();
        gauge($('gauge'), avg);
        $('gaugeNum').textContent = String(avg);
        $('gaugeCap').textContent = T.score;
        $('round').textContent = T.round(st.turn, st.max);

        var bars = $('bars');
        bars.replaceChildren();
        bars.appendChild(el('h4', null, T.lastTurn + ' · ' + d.score));
        CRIT.forEach(function (key) {
            var v = d.parts[key] || 0, row = el('div', 'bar');
            var top = el('div', 'bar-top');
            top.append(el('span', null, T.criteria[key]), el('b', null, v + '/10'));
            var track = el('div', 'bar-track'), fillEl = el('i');
            fillEl.style.setProperty('--bc', colorFor(v * 10));
            track.appendChild(fillEl);
            row.append(top, track);
            bars.appendChild(row);
            requestAnimationFrame(function () { requestAnimationFrame(function () { fillEl.style.width = v * 10 + '%'; }); });
        });

        var fb = $('feedback');
        fb.replaceChildren();
        if (d.comment) fb.appendChild(el('div', null, d.comment));
        if (d.hint) {
            var hintBox = el('div', 'hint', '💡 ' + d.hint);
            hintBox.hidden = true;
            var hintBtn = el('button', 'btn ghost hint-btn', T.showHint);
            hintBtn.type = 'button';
            hintBtn.addEventListener('click', function () { hintBox.hidden = false; hintBtn.remove(); });
            fb.append(hintBtn, hintBox);
        }

        var box = $('cites');
        box.replaceChildren();
        $('citesCard').hidden = false;
        if (!(d.citations || []).length) { box.appendChild(el('div', 'muted', T.noCites)); return; }
        d.citations.forEach(function (c) {
            var linkable = c.code_id && c.status !== 'missing';
            var chip = el(linkable ? 'a' : 'span', 'cite ' + c.status);
            chip.appendChild(document.createTextNode(c.code + ', ' + T.article + ' ' + c.article + ' '));
            chip.appendChild(el('small', null, T.cite[c.status] || ''));
            if (linkable) {
                chip.href = cfg.codexUrl + '?' + new URLSearchParams({ code: c.code_id, art: c.article });
                chip.target = '_blank';
                chip.rel = 'noopener';
            }
            box.appendChild(chip);
        });
    }

    // ------------------------------------------------------------------ вердикт

    function finish() {
        if (st.busy || st.turn < 1) { $('composeError').textContent = T.noTurns; return; }
        st.busy = true;
        updateControls();
        $('finish').textContent = '⏳ ' + T.deliberating;
        api('/api/court/verdict', { case_id: st.c.id, history: st.history, scores: st.scores, cited: st.cited, lang: cfg.lang }).then(function (r) {
            if (!r.ok) throw new Error(r.data.error || 'verdict');
            st.done = true;
            renderVerdict(r.data);
        }).catch(function (e) {
            $('composeError').textContent = (e && e.message && e.message !== 'verdict') ? e.message : T.errVerdict;
        }).then(function () {
            st.busy = false;
            updateControls();
        });
    }

    function list(title, items) {
        var card = el('div', 'card');
        card.appendChild(el('h4', null, title));
        var ul = el('ul');
        items.forEach(function (t) { ul.appendChild(el('li', null, t)); });
        card.appendChild(ul);
        return card;
    }

    function renderVerdict(d) {
        var box = $('verdictBody');
        box.replaceChildren();
        var head = el('div', 'v-head');
        head.appendChild(el('div', 'gavel', '🔨'));
        head.appendChild(el('h2', null, T.verdictTitle));
        head.appendChild(el('span', 'outcome ' + d.outcome, T.outcomes[d.outcome] || ''));
        box.appendChild(head);

        var grid = el('div', 'v-grid');
        var left = el('div');
        var g = el('div', 'gauge');
        g.innerHTML = '<svg viewBox="0 0 160 160"><circle class="track" cx="80" cy="80" r="70"></circle><circle class="fill" cx="80" cy="80" r="70"></circle></svg>';
        var num = el('div', 'num');
        num.append(el('b', null, String(d.score)), el('span', null, T.of100));
        g.appendChild(num);
        left.appendChild(g);
        var grade = el('div', 'grade', T.grades[d.grade] || '');
        grade.style.setProperty('--gc', colorFor(d.score));
        left.appendChild(grade);
        grid.append(left, el('div', 'v-text', d.verdict));
        box.appendChild(grid);
        requestAnimationFrame(function () { requestAnimationFrame(function () { gauge(g, d.score); }); });

        var bd = el('div', 'card');
        bd.appendChild(el('h4', null, T.breakdown));
        CRIT.forEach(function (key) {
            var v = (d.breakdown || {})[key] || 0, row = el('div', 'bar');
            var top = el('div', 'bar-top');
            top.append(el('span', null, T.criteria[key]), el('b', null, String(v)));
            var track = el('div', 'bar-track'), fillEl = el('i');
            fillEl.style.setProperty('--bc', colorFor(v));
            track.appendChild(fillEl);
            row.append(top, track);
            bd.appendChild(row);
            requestAnimationFrame(function () { requestAnimationFrame(function () { fillEl.style.width = v + '%'; }); });
        });
        box.appendChild(bd);

        var cols = el('div', 'v-cols');
        if (d.strengths && d.strengths.length) cols.appendChild(list('✅ ' + T.strengths, d.strengths));
        if (d.mistakes && d.mistakes.length) cols.appendChild(list('⚠️ ' + T.mistakes, d.mistakes));
        if (d.study && d.study.length) cols.appendChild(list('📚 ' + T.study, d.study));
        if (cols.children.length) box.appendChild(cols);

        if (d.articles && d.articles.length) {
            var arts = el('div', 'card');
            arts.style.marginTop = '14px';
            arts.appendChild(el('h4', null, T.articles));
            var chips = el('div', 'cites');
            d.articles.forEach(function (a) {
                var chip = el('a', 'cite verified');
                chip.appendChild(document.createTextNode(a.code + ', ' + T.article + ' ' + a.article + (a.title ? ' — ' + a.title : '')));
                chip.href = cfg.codexUrl + '?' + new URLSearchParams({ code: a.code_id, art: a.article });
                chip.target = '_blank';
                chip.rel = 'noopener';
                chips.appendChild(chip);
            });
            arts.appendChild(chips);
            box.appendChild(arts);
        }

        var actions = el('div', 'v-actions');
        var again = el('button', 'btn', '↻ ' + T.again);
        again.type = 'button';
        again.addEventListener('click', function () { startCase(st.c); });
        var other = el('button', 'btn ghost', '← ' + T.other);
        other.type = 'button';
        other.addEventListener('click', function () { show('screenCases'); });
        actions.append(again, other);
        box.appendChild(actions);
        show('screenVerdict');
    }

    // ------------------------------------------------------------------ тема и запуск

    function initTheme() {
        var saved = null;
        try { saved = localStorage.getItem('legalai-theme'); } catch (e) { /* нет хранилища */ }
        var theme = saved || (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
        document.documentElement.setAttribute('data-theme', theme);
        $('themeBtn').textContent = theme === 'dark' ? '☀️' : '🌙';
        $('themeBtn').addEventListener('click', function () {
            theme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', theme);
            $('themeBtn').textContent = theme === 'dark' ? '☀️' : '🌙';
            try { localStorage.setItem('legalai-theme', theme); } catch (e) { /* ignore */ }
        });
    }

    function init() {
        initTheme();
        $('send').addEventListener('click', send);
        $('finish').addEventListener('click', finish);
        $('input').addEventListener('keydown', function (e) { if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) { e.preventDefault(); send(); } });
        $('hintline').textContent = T.hint;
        $('backToCases').addEventListener('click', function () { show('screenCases'); });
        loadCases();
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
