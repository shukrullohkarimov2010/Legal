/* Граф связей статей: интерактивная карта «какая статья на какую ссылается».
 *
 * ArticleGraph.open({ nodes: [{code_id, article, status}], api, codexUrl, lang }) открывает окно.
 * Раскладка считается здесь же простой силовой моделью: узлы отталкиваются, связи стягивают, граф
 * плавно успокаивается. Весь текст из базы вставляется через textContent.
 */
(function () {
    'use strict';

    var TEXT = {
        ru: {
            title: 'Граф связей статей', loading: 'Строю граф…', close: 'Закрыть',
            sub: 'Кольцо — статьи вашего ответа. Стрелка идёт от статьи к той, на которую она ссылается.',
            empty: 'Нажмите на статью', emptyHint: 'Покажем, на что она ссылается и какие статьи ссылаются на неё. Узлы можно перетаскивать, колесо мыши — масштаб.',
            open: 'Открыть в кодексе →', expand: '➕ Раскрыть связи', out: 'Ссылается на', inn: 'Ссылаются на неё', none: 'Связей нет',
            article: 'ст.', failed: 'Не удалось построить граф. Повторите попытку.', notFound: 'Этих статей нет в базе связей.',
            missing: function (n) { return 'Не показано статей из ответа: ' + n + ' (их нет в базе).'; },
            legendFocus: 'статья из ответа', legendEdge: 'ссылается на', refs: function (n) { return n + ' ' + (n === 1 ? 'ссылка' : 'ссылок'); },
        },
        tg: {
            title: 'Графи пайвастагии моддаҳо', loading: 'Граф сохта мешавад…', close: 'Пӯшидан',
            sub: 'Ҳалқа — моддаҳои ҷавоби шумо. Тир аз модда ба моддае меравад, ки он ба он ишора мекунад.',
            empty: 'Моддаро пахш кунед', emptyHint: 'Нишон медиҳем, ки он ба кадом моддаҳо ишора мекунад ва кадом моддаҳо ба он ишора мекунанд. Гиреҳҳоро кашидан мумкин аст, чархак — миқёс.',
            open: 'Дар кодекс кушодан →', expand: '➕ Пайвастагиҳоро кушодан', out: 'Ишора мекунад ба', inn: 'Ба он ишора мекунанд', none: 'Пайвастагӣ нест',
            article: 'м.', failed: 'Граф сохта нашуд. Бори дигар кӯшиш кунед.', notFound: 'Ин моддаҳо дар базаи пайвастагиҳо нестанд.',
            missing: function (n) { return 'Моддаҳои ҷавоб, ки нишон дода нашуданд: ' + n + ' (дар база нестанд).'; },
            legendFocus: 'модда аз ҷавоб', legendEdge: 'ишора мекунад ба', refs: function (n) { return n + ' истинод'; },
        },
    };
    var SVGNS = 'http://www.w3.org/2000/svg';
    var STATUS_COLOR = { verified: 'var(--ag-ok)', exists: 'var(--ag-info)', wrong_code: 'var(--ag-warn)' };

    var inst = null;

    function el(tag, cls, text) {
        var node = document.createElement(tag);
        if (cls) node.className = cls;
        if (text != null) node.textContent = text;
        return node;
    }
    function sv(tag, attrs) {
        var node = document.createElementNS(SVGNS, tag);
        Object.keys(attrs || {}).forEach(function (k) { node.setAttribute(k, attrs[k]); });
        return node;
    }
    function hue(text) {
        var h = 0;
        for (var i = 0; i < text.length; i++) h = (h * 31 + text.charCodeAt(i)) % 360;
        return h;
    }
    function colorOf(code) { return 'hsl(' + ((hue(code) * 7) % 360) + ' 62% 52%)'; }

    // ------------------------------------------------------------------ окно

    function open(options) {
        close();
        var t = TEXT[options.lang === 'tg' ? 'tg' : 'ru'];
        var overlay = el('div', 'ag-overlay');
        if (document.documentElement.classList.contains('dark') || document.documentElement.getAttribute('data-theme') === 'dark') overlay.classList.add('dark');
        var dialog = el('div', 'ag-dialog');
        dialog.setAttribute('role', 'dialog');
        dialog.setAttribute('aria-modal', 'true');
        dialog.setAttribute('aria-label', t.title);
        dialog.tabIndex = -1;

        var head = el('div', 'ag-head');
        var titles = el('div');
        titles.appendChild(el('div', 'ag-title', '🕸 ' + t.title));
        titles.appendChild(el('div', 'ag-sub', t.sub));
        var closeBtn = el('button', 'ag-close', '✕');
        closeBtn.type = 'button';
        closeBtn.setAttribute('aria-label', t.close);
        head.append(titles, closeBtn);

        var body = el('div', 'ag-body');
        var stage = el('div', 'ag-stage');
        var svg = sv('svg', { 'class': 'ag-svg' });
        var defs = sv('defs');
        var marker = sv('marker', { id: 'ag-arrow', viewBox: '0 0 10 10', refX: '9', refY: '5', markerWidth: '7', markerHeight: '7', orient: 'auto-start-reverse' });
        var arrow = sv('path', { d: 'M0,1 L10,5 L0,9 z' });
        arrow.style.fill = 'var(--ag-edge)';
        marker.appendChild(arrow);
        defs.appendChild(marker);
        var world = sv('g');
        var edgeLayer = sv('g');
        var nodeLayer = sv('g');
        world.append(edgeLayer, nodeLayer);
        svg.append(defs, world);
        var loading = el('div', 'ag-loading', t.loading);
        var tip = el('div', 'ag-tip');
        tip.hidden = true;
        var legend = el('div', 'ag-legend');
        stage.append(svg, loading, tip, legend);
        var side = el('aside', 'ag-side');
        body.append(stage, side);
        dialog.append(head, body);
        overlay.appendChild(dialog);
        document.body.appendChild(overlay);

        inst = {
            opts: options, t: t, overlay: overlay, dialog: dialog, svg: svg, world: world, edgeLayer: edgeLayer, nodeLayer: nodeLayer,
            stage: stage, tip: tip, legend: legend, side: side, loading: loading, nodes: [], edges: [], byId: {},
            focus: (options.nodes || []).map(function (n) { return { code_id: n.code_id, article: String(n.article), status: n.status }; }),
            sel: null, alpha: 1, raf: 0, view: { x: 0, y: 0, k: 1 }, opener: document.activeElement,
        };

        closeBtn.addEventListener('click', close);
        overlay.addEventListener('mousedown', function (e) { if (e.target === overlay) close(); });
        document.addEventListener('keydown', onKey);
        bindViewport();
        showEmpty();
        closeBtn.focus();
        load(true);
    }

    function close() {
        if (!inst) return;
        cancelAnimationFrame(inst.raf);
        document.removeEventListener('keydown', onKey);
        inst.overlay.remove();
        if (inst.opener && inst.opener.focus) inst.opener.focus();
        inst = null;
    }
    function onKey(e) { if (e.key === 'Escape') close(); }

    // ------------------------------------------------------------------ данные

    function load(selectFirst) {
        var me = inst;
        fetch(me.opts.api + '/api/graph/subgraph', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ nodes: me.focus.map(function (n) { return { code_id: n.code_id, article: n.article }; }), limit: 32 }),
        }).then(function (res) {
            return res.json().then(function (data) { return { ok: res.ok, data: data }; });
        }).then(function (r) {
            if (inst !== me) return;
            me.loading.hidden = true;
            if (!r.ok || !r.data.nodes || !r.data.nodes.length) { showMessage(me.t.notFound); return; }
            apply(r.data);
            if (selectFirst) {
                var first = me.nodes.filter(function (n) { return n.focus; })[0] || me.nodes[0];
                if (first) select(first);
            }
        }).catch(function () {
            if (inst !== me) return;
            me.loading.hidden = false;
            me.loading.textContent = me.t.failed;
        });
    }

    function apply(data) {
        var me = inst, w = me.stage.clientWidth || 700, h = me.stage.clientHeight || 500;
        var statusOf = {};
        me.focus.forEach(function (f) { statusOf[f.code_id + ':' + f.article] = f.status; });
        var previous = me.byId;
        me.byId = {};
        me.nodes = data.nodes.map(function (n, i) {
            var old = previous[n.id];
            var angle = (i / Math.max(1, data.nodes.length)) * Math.PI * 2;
            var spread = n.focus ? 40 : 150;
            var node = {
                id: n.id, data: n, focus: n.focus, status: statusOf[n.id], code: n.code,
                x: old ? old.x : w / 2 + Math.cos(angle) * spread + (Math.random() - .5) * 20,
                y: old ? old.y : h / 2 + Math.sin(angle) * spread + (Math.random() - .5) * 20,
                vx: 0, vy: 0, fixed: false,
                r: (n.focus ? 15 : 9) + Math.min(9, Math.sqrt(n.degree || 0) * 1.8),
            };
            me.byId[n.id] = node;
            return node;
        });
        me.edges = data.edges.filter(function (e) { return me.byId[e.s] && me.byId[e.t]; }).map(function (e) {
            return { s: me.byId[e.s], t: me.byId[e.t], count: e.count, snippet: e.snippet };
        });
        var shown = me.nodes.filter(function (n) { return n.focus; }).length;
        var wanted = me.opts.nodes ? me.opts.nodes.length : shown;
        draw();
        me.alpha = 1;
        me.fitted = false;
        if (!me.raf) me.raf = requestAnimationFrame(tickLoop);
        buildLegend(wanted - shown);
    }

    // ------------------------------------------------------------------ рисование

    function draw() {
        var me = inst, t = me.t;
        me.edgeLayer.replaceChildren();
        me.nodeLayer.replaceChildren();
        me.edges.forEach(function (e) {
            var line = sv('line', { 'class': 'ag-edge', 'stroke-width': String(1.2 + Math.min(3, e.count - 1)), 'marker-end': 'url(#ag-arrow)' });
            var hit = sv('line', { 'class': 'ag-hit' });
            hit.addEventListener('mousemove', function (ev) {
                showTip(ev, e.s.data.code + ', ' + t.article + ' ' + e.s.data.article + ' → ' + e.t.data.code + ', ' + t.article + ' ' + e.t.data.article, '…' + e.snippet + '…');
            });
            hit.addEventListener('mouseleave', hideTip);
            e.line = line; e.hit = hit;
            me.edgeLayer.append(line, hit);
        });
        me.nodes.forEach(function (n) {
            var g = sv('g', { 'class': 'ag-node' + (me.sel === n.id ? ' sel' : '') });
            g.style.setProperty('--fill', colorOf(n.code));
            if (n.focus) {
                g.appendChild(sv('circle', { 'class': 'ring', r: String(n.r + 6), stroke: STATUS_COLOR[n.status] || 'var(--ag-accent)' }));
            }
            g.appendChild(sv('circle', { 'class': 'body', r: String(n.r), fill: colorOf(n.code) }));
            var label = sv('text', { 'class': 'ag-label', y: String(n.r + (n.focus ? 22 : 15)) });
            label.textContent = n.data.code + ' ' + n.data.article;
            g.appendChild(label);
            n.g = g;
            g.addEventListener('mouseenter', function (ev) { hover(n, true, ev); });
            g.addEventListener('mousemove', function (ev) { showTip(ev, n.data.code_name + ', ' + t.article + ' ' + n.data.article, n.data.title); });
            g.addEventListener('mouseleave', function () { hover(n, false); });
            g.addEventListener('pointerdown', function (ev) { startDrag(ev, n); });
            me.nodeLayer.appendChild(g);
        });
        place();
    }

    function place() {
        var me = inst;
        me.nodes.forEach(function (n) { n.g.setAttribute('transform', 'translate(' + n.x.toFixed(1) + ',' + n.y.toFixed(1) + ')'); });
        me.edges.forEach(function (e) {
            var dx = e.t.x - e.s.x, dy = e.t.y - e.s.y, d = Math.sqrt(dx * dx + dy * dy) || 1;
            var ux = dx / d, uy = dy / d;
            var x1 = e.s.x + ux * e.s.r, y1 = e.s.y + uy * e.s.r;
            var x2 = e.t.x - ux * (e.t.r + 4), y2 = e.t.y - uy * (e.t.r + 4);
            [e.line, e.hit].forEach(function (l) {
                l.setAttribute('x1', x1.toFixed(1)); l.setAttribute('y1', y1.toFixed(1));
                l.setAttribute('x2', x2.toFixed(1)); l.setAttribute('y2', y2.toFixed(1));
            });
        });
        me.world.setAttribute('transform', 'translate(' + me.view.x + ',' + me.view.y + ') scale(' + me.view.k + ')');
    }

    function buildLegend(hidden) {
        var me = inst, t = me.t;
        me.legend.replaceChildren();
        var codes = [];
        me.nodes.forEach(function (n) { if (codes.indexOf(n.code) < 0) codes.push(n.code); });
        codes.slice(0, 7).forEach(function (code) {
            var item = el('span');
            var dot = el('i', 'ag-dot');
            dot.style.setProperty('--c', colorOf(code));
            item.append(dot, document.createTextNode(code));
            me.legend.appendChild(item);
        });
        var ring = el('span');
        ring.append(el('i', 'ag-ringdot'), document.createTextNode(t.legendFocus));
        me.legend.appendChild(ring);
        if (hidden > 0) me.legend.appendChild(el('span', null, t.missing(hidden)));
    }

    // ------------------------------------------------------------------ физика

    function tickLoop() {
        var me = inst;
        if (!me) return;
        me.raf = 0;
        step(me);
        if (!me.fitted && me.alpha < 0.12 && !me.dragging) fit(me);
        place();
        if (me.alpha > 0.02 || me.dragging) me.raf = requestAnimationFrame(tickLoop);
    }

    // Масштаб и сдвиг так, чтобы весь граф с подписями помещался в окне.
    function fit(me) {
        me.fitted = true;
        if (!me.nodes.length) return;
        var w = me.stage.clientWidth || 700, h = me.stage.clientHeight || 500;
        var x0 = Infinity, y0 = Infinity, x1 = -Infinity, y1 = -Infinity;
        me.nodes.forEach(function (n) {
            x0 = Math.min(x0, n.x - n.r - 10); x1 = Math.max(x1, n.x + n.r + 10);
            y0 = Math.min(y0, n.y - n.r - 10); y1 = Math.max(y1, n.y + n.r + 34);
        });
        var k = Math.max(.5, Math.min((w - 70) / (x1 - x0), (h - 90) / (y1 - y0), 1.6));
        me.view.k = k;
        me.view.x = w / 2 - ((x0 + x1) / 2) * k;
        me.view.y = h / 2 - ((y0 + y1) / 2) * k - 8;
    }

    function step(me) {
        var nodes = me.nodes, w = me.stage.clientWidth || 700, h = me.stage.clientHeight || 500;
        var a = me.alpha, i, j, n, m, dx, dy, d2, d, f;
        for (i = 0; i < nodes.length; i++) {
            for (j = i + 1; j < nodes.length; j++) {
                n = nodes[i]; m = nodes[j];
                dx = m.x - n.x; dy = m.y - n.y; d2 = dx * dx + dy * dy;
                if (d2 < 1) { dx = Math.random() - .5; dy = Math.random() - .5; d2 = 1; }
                d = Math.sqrt(d2);
                f = (5200 / d2) * a;
                if (d < n.r + m.r + 14) f += (n.r + m.r + 14 - d) * .05;
                n.vx -= (dx / d) * f; n.vy -= (dy / d) * f;
                m.vx += (dx / d) * f; m.vy += (dy / d) * f;
            }
        }
        me.edges.forEach(function (e) {
            dx = e.t.x - e.s.x; dy = e.t.y - e.s.y; d = Math.sqrt(dx * dx + dy * dy) || 1;
            f = (d - 120) * .035 * a;
            e.s.vx += (dx / d) * f; e.s.vy += (dy / d) * f;
            e.t.vx -= (dx / d) * f; e.t.vy -= (dy / d) * f;
        });
        nodes.forEach(function (p) {
            p.vx += (w / 2 - p.x) * .012 * a; p.vy += (h / 2 - p.y) * .012 * a;
            if (p.fixed) { p.vx = 0; p.vy = 0; return; }
            p.vx *= .72; p.vy *= .72;
            p.x += p.vx; p.y += p.vy;
        });
        me.alpha *= .985;
    }

    // ------------------------------------------------------------------ взаимодействие

    function hover(n, on, ev) {
        var me = inst;
        if (!on) {
            hideTip();
            me.nodes.forEach(function (p) { p.g.classList.remove('off'); });
            me.edges.forEach(function (e) { e.line.classList.remove('on', 'off'); });
            return;
        }
        var near = {};
        near[n.id] = true;
        me.edges.forEach(function (e) {
            var linked = e.s === n || e.t === n;
            if (linked) { near[e.s.id] = true; near[e.t.id] = true; }
            e.line.classList.toggle('on', linked);
            e.line.classList.toggle('off', !linked);
        });
        me.nodes.forEach(function (p) { p.g.classList.toggle('off', !near[p.id]); });
    }

    function showTip(ev, head, text) {
        var me = inst, box = me.stage.getBoundingClientRect();
        me.tip.replaceChildren();
        me.tip.appendChild(el('b', null, head));
        if (text) me.tip.appendChild(el('i', null, text));
        me.tip.hidden = false;
        var x = ev.clientX - box.left + 14, y = ev.clientY - box.top + 14;
        me.tip.style.left = Math.min(x, box.width - 310) + 'px';
        me.tip.style.top = Math.min(y, box.height - 70) + 'px';
    }
    function hideTip() { if (inst) inst.tip.hidden = true; }

    function startDrag(ev, n) {
        var me = inst;
        ev.stopPropagation();
        ev.preventDefault();
        var start = { x: ev.clientX, y: ev.clientY }, moved = false;
        me.dragging = true;
        n.fixed = true;
        function move(e) {
            if (Math.abs(e.clientX - start.x) + Math.abs(e.clientY - start.y) > 4) moved = true;
            var box = me.stage.getBoundingClientRect();
            n.x = (e.clientX - box.left - me.view.x) / me.view.k;
            n.y = (e.clientY - box.top - me.view.y) / me.view.k;
            me.alpha = Math.max(me.alpha, .3);
            if (!me.raf) me.raf = requestAnimationFrame(tickLoop);
        }
        function up() {
            window.removeEventListener('pointermove', move);
            window.removeEventListener('pointerup', up);
            me.dragging = false;
            n.fixed = false;
            if (!moved && inst === me) select(n);
        }
        window.addEventListener('pointermove', move);
        window.addEventListener('pointerup', up);
    }

    function bindViewport() {
        var me = inst, svg = me.svg;
        svg.addEventListener('wheel', function (e) {
            e.preventDefault();
            var box = svg.getBoundingClientRect(), v = me.view;
            var k = Math.max(.4, Math.min(2.5, v.k * (e.deltaY < 0 ? 1.12 : 1 / 1.12)));
            var px = e.clientX - box.left, py = e.clientY - box.top;
            v.x = px - (px - v.x) * (k / v.k);
            v.y = py - (py - v.y) * (k / v.k);
            v.k = k;
            place();
        }, { passive: false });
        svg.addEventListener('pointerdown', function (e) {
            if (e.target !== svg) return;
            var sx = e.clientX, sy = e.clientY, ox = me.view.x, oy = me.view.y;
            function move(ev) { me.view.x = ox + ev.clientX - sx; me.view.y = oy + ev.clientY - sy; place(); }
            function up() { window.removeEventListener('pointermove', move); window.removeEventListener('pointerup', up); }
            window.addEventListener('pointermove', move);
            window.addEventListener('pointerup', up);
        });
    }

    // ------------------------------------------------------------------ панель статьи

    function showEmpty() {
        var box = el('div', 'ag-empty');
        box.append(el('div', null, '🕸'), el('b', null, inst.t.empty), el('div', null, inst.t.emptyHint));
        inst.side.replaceChildren(box);
    }
    function showMessage(text) {
        inst.side.replaceChildren(el('div', 'ag-empty', text));
    }

    function select(n) {
        var me = inst, t = me.t;
        me.sel = n.id;
        me.nodes.forEach(function (p) { p.g.classList.toggle('sel', p === n); });
        var d = n.data, side = me.side;
        side.replaceChildren();
        side.appendChild(el('div', 'ag-code', d.code_name));
        side.appendChild(el('div', 'ag-art', t.article + ' ' + d.article));
        if (d.title) side.appendChild(el('div', 'ag-art-title', d.title));
        if (me.opts.codexUrl) {
            var open = el('a', 'ag-open', t.open);
            open.href = me.opts.codexUrl + '?' + new URLSearchParams({ code: d.code_id, art: d.article });
            open.target = '_blank';
            open.rel = 'noopener';
            side.appendChild(open);
        }
        var holder = el('div');
        holder.appendChild(el('div', 'ag-none', t.loading));
        side.appendChild(holder);

        fetch(me.opts.api + '/api/graph/article?' + new URLSearchParams({ code: d.code_id, n: d.article }))
            .then(function (res) { if (!res.ok) throw new Error(res.status); return res.json(); })
            .then(function (info) {
                if (inst !== me || me.sel !== n.id) return;
                holder.replaceChildren();
                holder.appendChild(group(t.out, info.out));
                holder.appendChild(group(t.inn, info.in));
                var inFocus = me.focus.some(function (f) { return f.code_id === d.code_id && f.article === d.article; });
                if (!inFocus && (info.out.length || info.in.length)) {
                    var more = el('button', 'ag-link', t.expand);
                    more.type = 'button';
                    more.addEventListener('click', function () { addFocus(d.code_id, d.article); });
                    holder.appendChild(more);
                }
            })
            .catch(function () { holder.replaceChildren(el('div', 'ag-none', t.failed)); });
    }

    function group(title, rows) {
        var t = inst.t, box = el('div', 'ag-group');
        box.appendChild(el('div', 'ag-group-title', title + (rows.length ? ' · ' + rows.length : '')));
        if (!rows.length) { box.appendChild(el('div', 'ag-none', t.none)); return box; }
        rows.slice(0, 12).forEach(function (r) {
            var btn = el('button', 'ag-link');
            btn.type = 'button';
            btn.appendChild(el('b', null, r.code + ', ' + t.article + ' ' + r.article));
            if (r.title) btn.appendChild(document.createTextNode(' — ' + r.title));
            btn.appendChild(el('small', null, '…' + r.snippet + '…'));
            btn.addEventListener('click', function () { addFocus(r.code_id, r.article, true); });
            box.appendChild(btn);
        });
        return box;
    }

    function addFocus(codeId, article, selectIt) {
        var me = inst;
        if (!me.focus.some(function (f) { return f.code_id === codeId && f.article === article; })) {
            me.focus.push({ code_id: codeId, article: String(article) });
        }
        var target = codeId + ':' + article;
        fetch(me.opts.api + '/api/graph/subgraph', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ nodes: me.focus.map(function (n) { return { code_id: n.code_id, article: n.article }; }), limit: 40 }),
        }).then(function (res) { return res.ok ? res.json() : null; }).then(function (data) {
            if (!data || inst !== me) return;
            apply(data);
            var node = me.byId[target];
            if (node) select(node);
        });
    }

    window.ArticleGraph = { open: open, close: close };
})();
