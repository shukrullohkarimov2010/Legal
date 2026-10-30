/* Сцена зала заседания: иллюстрированные персонажи, подсветка говорящего, печать реплик,
 * голос с движением губ, взгляд в сторону говорящего, жест «возражаю», молоток, штампы
 * «Возражение / Принято / Отклонено», всплывающий балл и звуковые эффекты.
 *
 * Всё нарисовано кодом (SVG), внешних картинок нет. Текст реплик вставляется только через
 * textContent. CourtScene.create(контейнер, { voice }) возвращает объект сцены.
 */
(function () {
    'use strict';

    var REDUCED = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ------------------------------------------------------------------ цвет

    function shade(hex, amount) {
        var n = parseInt(hex.slice(1), 16), r = n >> 16, g = (n >> 8) & 255, b = n & 255;
        var f = function (c) { return Math.max(0, Math.min(255, Math.round(amount < 0 ? c * (1 + amount) : c + (255 - c) * amount))); };
        return '#' + ((1 << 24) + (f(r) << 16) + (f(g) << 8) + f(b)).toString(16).slice(1);
    }

    var SKIN = { a: '#efc39b', b: '#d9a273', c: '#b97f53' };

    // ------------------------------------------------------------------ персонажи

    // Бюст в рамке 120 x 150. role: judge | suit | casual; gender: m | f.
    function figure(o) {
        var id = o.id, skin = o.skin, hair = o.hair, cloth = o.cloth || '#1e293b', s = '';
        var skinD = shade(skin, -0.2), skinL = shade(skin, 0.18), lip = shade(skin, -0.38);
        s += '<defs>' +
            '<radialGradient id="' + id + 'sk" cx=".42" cy=".36" r=".78"><stop offset="0" stop-color="' + skinL + '"/><stop offset=".62" stop-color="' + skin + '"/><stop offset="1" stop-color="' + skinD + '"/></radialGradient>' +
            '<linearGradient id="' + id + 'hr" x1="0" y1="0" x2=".3" y2="1"><stop offset="0" stop-color="' + shade(hair, 0.22) + '"/><stop offset="1" stop-color="' + shade(hair, -0.25) + '"/></linearGradient>' +
            '<linearGradient id="' + id + 'cl" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="' + shade(cloth, 0.16) + '"/><stop offset="1" stop-color="' + shade(cloth, -0.32) + '"/></linearGradient>' +
            '</defs>';
        var SK = 'url(#' + id + 'sk)', HR = 'url(#' + id + 'hr)', CL = 'url(#' + id + 'cl)';

        s += '<g class="fig">';
        // волосы сзади
        if (o.gender === 'f') {
            s += '<path d="M31 60 C26 30 42 18 61 18 C82 18 95 32 90 62 L96 122 C90 130 82 126 79 118 L79 84 L41 84 L41 118 C38 126 30 130 24 122 Z" fill="' + HR + '"/>';
        }
        // тело и одежда
        if (o.role === 'judge') {
            s += '<path d="M2 150 C3 112 24 99 60 99 C96 99 117 112 118 150 Z" fill="' + CL + '"/>';
            s += '<path d="M22 150 C24 128 30 116 36 110 M98 150 C96 128 90 116 84 110 M48 150 L50 122 M72 150 L70 122" stroke="rgba(255,255,255,.07)" stroke-width="2.5" fill="none"/>';
            s += '<path d="M40 100 Q60 126 80 100 L86 108 Q60 140 34 108 Z" fill="#f6f3ec"/>';
            s += '<path d="M54 112 L57 150 L63 150 L66 112 Z" fill="#ebe5d6"/><path d="M60 112 V150" stroke="#d6cfbd" stroke-width="1"/>';
        } else if (o.role === 'suit') {
            s += '<path d="M3 150 C4 113 25 100 60 100 C95 100 116 113 117 150 Z" fill="' + CL + '"/>';
            s += '<path d="M46 100 L60 126 L74 100 Z" fill="#f4f5f7"/>';
            s += '<path d="M46 100 L52 112 L45 118 Z M74 100 L68 112 L75 118 Z" fill="#e3e6ec"/>';
            s += '<path d="M46 100 L58 132 L40 150 L10 150 C13 121 28 106 46 100 Z" fill="rgba(0,0,0,.2)"/>';
            s += '<path d="M74 100 L62 132 L80 150 L110 150 C107 121 92 106 74 100 Z" fill="rgba(0,0,0,.2)"/>';
            s += '<path d="M46 100 L58 132 M74 100 L62 132" stroke="rgba(255,255,255,.12)" stroke-width="1.2"/>';
            if (o.gender === 'm') {
                s += '<path d="M57 108 L63 108 L65 113 L60 116 L55 113 Z" fill="' + shade(o.accent, -0.2) + '"/>';
                s += '<path d="M56 115 L64 115 L67 140 L60 148 L53 140 Z" fill="' + o.accent + '"/>';
                s += '<path d="M58 118 L65 132" stroke="rgba(255,255,255,.18)" stroke-width="1.4"/>';
            } else {
                s += '<circle cx="60" cy="112" r="3" fill="' + o.accent + '"/><path d="M50 104 Q60 118 70 104" fill="none" stroke="' + o.accent + '" stroke-width="1.6"/>';
            }
            s += '<circle cx="60" cy="138" r="1.8" fill="rgba(0,0,0,.35)"/><circle cx="60" cy="146" r="1.8" fill="rgba(0,0,0,.35)"/>';
        } else {
            s += '<path d="M3 150 C4 115 25 103 60 103 C95 103 116 115 117 150 Z" fill="' + CL + '"/>';
            s += '<path d="M42 103 Q60 128 78 103 Q60 120 42 103 Z" fill="' + skinD + '"/>';
            s += '<path d="M42 103 Q60 128 78 103" fill="none" stroke="' + shade(cloth, 0.25) + '" stroke-width="3"/>';
            s += '<path d="M24 150 C26 132 30 122 34 116 M96 150 C94 132 90 122 86 116" stroke="rgba(0,0,0,.18)" stroke-width="2.5" fill="none"/>';
        }
        // поднятая рука (возражение)
        if (o.arm) {
            s += '<g class="arm"><path d="M90 128 C94 104 99 84 104 66 L116 70 C112 90 106 110 102 134 Z" fill="' + CL + '"/>' +
                 '<path d="M101 66 L118 71 L117 76 L100 71 Z" fill="#f4f5f7"/>' +
                 '<path d="M101 64 C99 52 102 44 106 42 C108 36 112 36 113 41 C115 37 119 38 119 43 C121 41 124 43 123 48 L121 66 C119 70 105 70 101 64 Z" fill="' + SK + '"/></g>';
        }
        // шея
        s += '<path d="M49 82 H71 V104 Q60 116 49 104 Z" fill="' + skinD + '"/>';
        s += '<path d="M49 92 Q60 100 71 92 V96 Q60 104 49 96 Z" fill="rgba(0,0,0,.12)"/>';

        // голова: всё внутри .head наклоняется при речи
        s += '<g class="head">';
        s += '<path d="M33 60 C30 56 27 64 30 70 C31 74 34 76 36 74 Z" fill="' + skinD + '"/><path d="M87 60 C90 56 93 64 90 70 C89 74 86 76 84 74 Z" fill="' + skinD + '"/>';
        s += '<path d="M34 56 C34 33 46 24 60 24 C74 24 86 33 86 56 C86 72 82 84 73 92 C68 96 64 98 60 98 C56 98 52 96 47 92 C38 84 34 72 34 56 Z" fill="' + SK + '"/>';
        s += '<path d="M40 84 Q60 104 80 84" stroke="rgba(0,0,0,.08)" stroke-width="4" fill="none"/>';
        if (o.stubble) s += '<path d="M42 80 Q48 96 60 97 Q72 96 78 80 Q72 90 60 91 Q48 90 42 80 Z" fill="rgba(40,30,25,.13)"/>';
        // волосы спереди
        if (o.gender === 'f') {
            s += '<path d="M33 60 C30 32 44 19 61 19 C81 19 91 34 87 58 C80 42 68 35 56 38 C46 41 39 48 33 60 Z" fill="' + HR + '"/>';
            s += '<path d="M46 26 Q60 20 76 27 M44 33 Q56 27 70 30" stroke="rgba(255,255,255,.16)" stroke-width="1.6" fill="none"/>';
        } else if (o.role === 'judge') {
            s += '<path d="M33 64 C30 48 33 38 39 33 C37 44 38 52 39 60 Z M87 64 C90 48 87 38 81 33 C83 44 82 52 81 60 Z" fill="' + HR + '"/>';
            s += '<path d="M40 33 C48 23 72 23 80 33 C72 29 48 29 40 33 Z" fill="' + HR + '"/>';
        } else {
            s += '<path d="M33 58 C30 32 43 20 60 20 C79 20 90 32 87 58 C85 47 81 41 76 38 C68 33 50 33 43 38 C38 42 35 48 33 58 Z" fill="' + HR + '"/>';
            s += '<path d="M44 28 Q58 22 74 28" stroke="rgba(255,255,255,.18)" stroke-width="1.8" fill="none"/>';
        }
        // брови
        var brow = o.role === 'judge' ? '#9a9aa3' : shade(hair, -0.1);
        var bl = o.stern ? 'M40 50 Q47 46 55 51 L55 53 Q47 49 40 52 Z' : 'M40 51 Q47 47 55 51 L55 53 Q47 50 40 53 Z';
        var br = o.stern ? 'M80 50 Q73 46 65 51 L65 53 Q73 49 80 52 Z' : 'M80 51 Q73 47 65 51 L65 53 Q73 50 80 53 Z';
        s += '<path d="' + bl + '" fill="' + brow + '"/><path d="' + br + '" fill="' + brow + '"/>';
        // глаза: белок, радужка (смотрит в сторону говорящего), веко
        var iris = o.iris || '#4a3020';
        s += '<g class="eyes">';
        [48, 72].forEach(function (x) {
            s += '<path d="M' + (x - 6.5) + ' 60 Q' + x + ' 55 ' + (x + 6.5) + ' 60 Q' + x + ' 64 ' + (x - 6.5) + ' 60 Z" fill="#fbfaf6"/>';
            s += '<g class="iris"><circle cx="' + x + '" cy="59.8" r="3.1" fill="' + iris + '"/><circle cx="' + x + '" cy="59.8" r="1.5" fill="#120d0a"/><circle cx="' + (x + 1) + '" cy="58.7" r=".8" fill="#fff"/></g>';
            s += '<path d="M' + (x - 7) + ' 60 Q' + x + ' 54 ' + (x + 7) + ' 60" fill="none" stroke="#2a1c14" stroke-width="1.5" stroke-linecap="round"/>';
        });
        s += '</g>';
        if (o.glasses) {
            s += '<g fill="rgba(170,200,255,.12)" stroke="#2d3142" stroke-width="1.8"><rect x="39" y="52.5" width="18" height="14" rx="5"/><rect x="63" y="52.5" width="18" height="14" rx="5"/></g><path d="M57 58 Q60 56 63 58" stroke="#2d3142" stroke-width="1.6" fill="none"/>';
        }
        // нос
        s += '<path d="M61 61 C60 67 57.5 71 57 73.5 C58.5 75.8 62 76 64 73.8" fill="none" stroke="' + skinD + '" stroke-width="1.7" stroke-linecap="round"/>';
        s += '<ellipse cx="58.5" cy="74.4" rx="1.3" ry=".8" fill="' + shade(skin, -0.42) + '"/><ellipse cx="62.6" cy="74.4" rx="1.3" ry=".8" fill="' + shade(skin, -0.42) + '"/>';
        // рот: внутренняя часть раскрывается по громкости голоса (--open)
        s += '<g class="mouth"><ellipse class="mouth-in" cx="60" cy="84" rx="6.2" ry="3" fill="#3a1216"/>' +
             '<path d="M52.5 82.6 Q56 80.6 60 81.8 Q64 80.6 67.5 82.6 Q60 84.2 52.5 82.6 Z" fill="' + lip + '"/>' +
             '<path d="M53.5 84.2 Q60 89 66.5 84.2 Q60 86.4 53.5 84.2 Z" fill="' + shade(lip, 0.12) + '"/></g>';
        s += '<ellipse cx="42" cy="75" rx="5.5" ry="3.2" fill="rgba(255,110,100,.14)"/><ellipse cx="78" cy="75" rx="5.5" ry="3.2" fill="rgba(255,110,100,.14)"/>';
        s += '</g>'; // .head
        s += '</g>'; // .fig
        return s;
    }

    function backFigure() {
        return '<g class="fig">' +
               '<path d="M-96 0 C-96 -52 -52 -78 0 -78 C52 -78 96 -52 96 0 Z" fill="#2c2a6b"/>' +
               '<path d="M-96 0 C-96 -52 -52 -78 0 -78" fill="none" stroke="rgba(167,139,250,.55)" stroke-width="3"/>' +
               '<path d="M-14 -78 L0 -58 L14 -78 Z" fill="#e8e6f2"/>' +
               '<rect x="-12" y="-84" width="24" height="14" rx="6" fill="#c08458"/>' +
               '<ellipse cx="0" cy="-106" rx="26" ry="29" fill="#2a2018"/>' +
               '<path d="M-22 -116 Q0 -142 22 -116" fill="none" stroke="rgba(255,255,255,.14)" stroke-width="3"/>' +
               '</g>';
    }

    // ------------------------------------------------------------------ декорации

    function plate(x, y, w, id, text) {
        return '<g><rect x="' + x + '" y="' + y + '" width="' + w + '" height="19" rx="4" fill="#caa24a"/>' +
               '<rect x="' + (x + 2) + '" y="' + (y + 2) + '" width="' + (w - 4) + '" height="15" rx="3" fill="#a9822c"/>' +
               '<text id="' + id + '" x="' + (x + w / 2) + '" y="' + (y + 14) + '" text-anchor="middle" fill="#fff6dc" ' +
               'font-family="Inter,system-ui,sans-serif" font-size="10.5" font-weight="800" letter-spacing="1.2">' + text + '</text></g>';
    }

    function panels(x, y, w, h, n) {
        var out = '', gap = 8, pw = (w - gap * (n + 1)) / n;
        for (var i = 0; i < n; i++) {
            out += '<rect x="' + (x + gap + i * (pw + gap)) + '" y="' + (y + 8) + '" width="' + pw + '" height="' + (h - 16) + '" rx="3" fill="rgba(0,0,0,.16)" stroke="rgba(255,255,255,.07)"/>';
        }
        return out;
    }

    var POS = { opponent: 146, judge: 480, witness: 823, me: 480 };

    function buildSvg(c) {
        var of = c.opponent_gender === 'f', wf = c.witness_gender === 'f';
        var opp = figure({ id: 'o', role: 'suit', gender: of ? 'f' : 'm', skin: of ? SKIN.a : SKIN.b, hair: of ? '#3b2418' : '#1d1814', cloth: of ? '#7f1d1d' : '#1e3a8a', accent: of ? '#fbbf24' : '#b91c1c', arm: true, stubble: !of, stern: true });
        var wit = figure({ id: 'w', role: 'casual', gender: wf ? 'f' : 'm', skin: wf ? SKIN.b : SKIN.c, hair: wf ? '#1a1410' : '#2a1d14', cloth: wf ? '#0f766e' : '#b45309', iris: '#3a2516' });
        var judge = figure({ id: 'j', role: 'judge', gender: 'm', skin: SKIN.a, hair: '#c9ccd6', cloth: '#1a1f33', glasses: true, stern: true, iris: '#5b4636' });

        return '' +
        '<svg viewBox="0 0 960 440" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Зал заседания" preserveAspectRatio="xMidYMid slice">' +
        '<defs>' +
          '<linearGradient id="cs-wall" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#232d57"/><stop offset=".6" stop-color="#151c3d"/><stop offset="1" stop-color="#0d1230"/></linearGradient>' +
          '<linearGradient id="cs-wood" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#8a5a32"/><stop offset="1" stop-color="#5e3a1d"/></linearGradient>' +
          '<linearGradient id="cs-wood2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#a8723f"/><stop offset="1" stop-color="#7a4d26"/></linearGradient>' +
          '<linearGradient id="cs-floor" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3b2514"/><stop offset="1" stop-color="#1c110a"/></linearGradient>' +
          '<radialGradient id="cs-light" cx=".5" cy="0" r=".9"><stop offset="0" stop-color="rgba(255,226,160,.38)"/><stop offset=".55" stop-color="rgba(255,226,160,.07)"/><stop offset="1" stop-color="rgba(255,226,160,0)"/></radialGradient>' +
          '<radialGradient id="cs-vig" cx=".5" cy=".5" r=".75"><stop offset=".6" stop-color="rgba(0,0,0,0)"/><stop offset="1" stop-color="rgba(0,0,0,.55)"/></radialGradient>' +
        '</defs>' +
        '<rect width="960" height="440" fill="url(#cs-wall)"/>' +
        '<g opacity=".9">' +
          '<rect x="20" y="0" width="46" height="350" fill="rgba(255,255,255,.04)"/><rect x="894" y="0" width="46" height="350" fill="rgba(255,255,255,.04)"/>' +
          '<rect x="14" y="0" width="58" height="10" fill="rgba(255,255,255,.07)"/><rect x="888" y="0" width="58" height="10" fill="rgba(255,255,255,.07)"/>' +
          '<path d="M118 30 H178 V180 H118 Z" fill="rgba(120,160,255,.10)" stroke="rgba(255,255,255,.12)"/><path d="M782 30 H842 V180 H782 Z" fill="rgba(120,160,255,.10)" stroke="rgba(255,255,255,.12)"/>' +
          '<path d="M148 30 V180 M118 105 H178 M812 30 V180 M782 105 H842" stroke="rgba(255,255,255,.12)"/>' +
        '</g>' +
        '<rect x="0" y="262" width="960" height="92" fill="url(#cs-wood)" opacity=".92"/>' + panels(0, 262, 960, 92, 12) +
        '<rect x="0" y="258" width="960" height="6" fill="#b07c46"/>' +
        '<rect x="0" y="352" width="960" height="88" fill="url(#cs-floor)"/>' +
        '<g stroke="rgba(255,255,255,.05)"><path d="M0 380 H960 M0 410 H960"/><path d="M200 352 L60 440 M420 352 L360 440 M540 352 L600 440 M760 352 L900 440"/></g>' +
        '<g><circle cx="480" cy="96" r="66" fill="rgba(255,255,255,.04)" stroke="rgba(202,162,74,.55)" stroke-width="2"/>' +
          '<circle cx="480" cy="96" r="56" fill="none" stroke="rgba(202,162,74,.25)"/>' +
          '<g stroke="rgba(202,162,74,.5)" stroke-width="3" fill="none" stroke-linecap="round" transform="translate(480 96)"><path d="M0 -44 V40 M-26 40 H26 M-34 -26 H34 M-34 -26 L-48 4 H-20 Z M34 -26 L20 4 H48 Z"/></g></g>' +
        '<rect width="960" height="440" fill="url(#cs-light)"/>' +

        '<g class="char" data-who="judge"><g transform="translate(420 40)">' + judge + '</g></g>' +
        '<ellipse cx="480" cy="256" rx="170" ry="8" fill="rgba(0,0,0,.35)"/>' +
        '<rect x="344" y="176" width="272" height="15" rx="3" fill="url(#cs-wood2)"/><rect x="352" y="191" width="256" height="64" fill="url(#cs-wood)"/>' + panels(352, 191, 256, 64, 3) +
        plate(430, 205, 100, 'cs-plate-judge', 'СУДЬЯ') +
        '<g id="cs-gavel" style="transform-origin: 568px 176px"><rect x="556" y="152" width="28" height="12" rx="3" fill="#6b4423"/><rect x="568" y="160" width="5" height="20" fill="#8a5a32"/></g>' +

        '<g class="char" data-who="opponent"><g transform="translate(78 132) scale(1.05)">' + opp + '</g></g>' +
        '<ellipse cx="146" cy="296" rx="130" ry="7" fill="rgba(0,0,0,.35)"/>' +
        '<rect x="22" y="262" width="248" height="14" rx="3" fill="url(#cs-wood2)"/><rect x="30" y="276" width="232" height="66" fill="url(#cs-wood)"/>' + panels(30, 276, 232, 66, 3) +
        plate(78, 292, 136, 'cs-plate-opp', 'ПРОКУРОР') +

        '<g class="char" data-who="witness"><g transform="translate(760 122) scale(1.05)">' + wit + '</g></g>' +
        '<ellipse cx="822" cy="296" rx="132" ry="7" fill="rgba(0,0,0,.35)"/>' +
        '<rect x="696" y="252" width="252" height="14" rx="3" fill="url(#cs-wood2)"/><rect x="704" y="266" width="236" height="78" fill="url(#cs-wood)"/>' + panels(704, 266, 236, 78, 3) +
        plate(752, 286, 140, 'cs-plate-wit', 'СВИДЕТЕЛЬ') +

        '<g class="char me" data-who="me"><g transform="translate(480 452)">' + backFigure() + '</g></g>' +
        '<rect width="960" height="440" fill="url(#cs-vig)"/>' +
        '</svg>';
    }

    // ------------------------------------------------------------------ звуковые эффекты

    var audio = null;
    function ctx() {
        if (!audio) {
            var AC = window.AudioContext || window.webkitAudioContext;
            if (AC) { try { audio = new AC(); } catch (e) { audio = null; } }
        }
        if (audio && audio.state === 'suspended') audio.resume();
        return audio;
    }
    function thump(freq0, freq1, dur, gain, noise) {
        var a = ctx();
        if (!a) return;
        var t = a.currentTime, osc = a.createOscillator(), g = a.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq0, t);
        osc.frequency.exponentialRampToValueAtTime(freq1, t + dur);
        g.gain.setValueAtTime(gain, t);
        g.gain.exponentialRampToValueAtTime(0.001, t + dur);
        osc.connect(g).connect(a.destination);
        osc.start(t);
        osc.stop(t + dur);
        if (noise) {
            var len = Math.floor(a.sampleRate * 0.07), buf = a.createBuffer(1, len, a.sampleRate), data = buf.getChannelData(0);
            for (var i = 0; i < len; i++) data[i] = (Math.random() * 2 - 1) * (1 - i / len);
            var src = a.createBufferSource(), ng = a.createGain();
            src.buffer = buf;
            ng.gain.value = noise;
            src.connect(ng).connect(a.destination);
            src.start(t);
        }
    }

    // ------------------------------------------------------------------ сцена

    function Scene(root, cfg) {
        this.root = root;
        this.cfg = cfg || {};
        this.voice = this.cfg.voice || null;
        this.names = {};
        this.genders = { judge: 'm', opponent: 'm', witness: 'f' };
        this.muted = false;
        try { this.muted = localStorage.getItem('court-sound') === 'off'; } catch (e) { /* нет хранилища */ }
        if (this.voice) this.voice.setMuted(this.muted);
        this.fast = false;
        this.pending = null;
        this.build();
    }

    Scene.prototype.build = function () {
        var self = this, root = this.root;
        root.classList.add('scene');
        root.replaceChildren();
        this.art = document.createElement('div');
        this.art.className = 'scene-art';
        root.appendChild(this.art);

        this.chip = document.createElement('div');
        this.chip.className = 'scene-chip';
        root.appendChild(this.chip);

        this.tools = document.createElement('div');
        this.tools.className = 'scene-tools';
        this.soundBtn = document.createElement('button');
        this.soundBtn.type = 'button';
        this.soundBtn.className = 'scene-btn';
        this.soundBtn.addEventListener('click', function () {
            self.muted = !self.muted;
            try { localStorage.setItem('court-sound', self.muted ? 'off' : 'on'); } catch (e) { /* ignore */ }
            if (self.voice) self.voice.setMuted(self.muted);
            self.paintSound();
            if (!self.muted) thump(140, 60, .12, .25);
        });
        this.tools.appendChild(this.soundBtn);
        root.appendChild(this.tools);
        this.paintSound();

        this.stampLayer = document.createElement('div');
        this.stampLayer.className = 'stamp-layer';
        root.appendChild(this.stampLayer);

        this.cap = document.createElement('div');
        this.cap.className = 'caption';
        this.cap.hidden = true;
        this.capName = document.createElement('div');
        this.capName.className = 'cap-name';
        this.capText = document.createElement('div');
        this.capText.className = 'cap-text';
        this.capNext = document.createElement('span');
        this.capNext.className = 'cap-next';
        this.capNext.textContent = '▶';
        this.cap.append(this.capName, this.capText, this.capNext);
        this.cap.addEventListener('click', function () { self.advance(); });
        root.appendChild(this.cap);
    };

    Scene.prototype.paintSound = function () {
        var labels = this.cfg.labels || {};
        var label = this.muted ? (labels.soundOn || 'Включить звук') : (labels.soundOff || 'Выключить звук');
        this.soundBtn.textContent = this.muted ? '🔇' : '🔊';
        this.soundBtn.setAttribute('aria-label', label);
        this.soundBtn.title = label;
    };

    Scene.prototype.setCase = function (c, labels) {
        var self = this;
        this.names = { judge: labels.judge, opponent: c.opponent, witness: c.witness.split(',')[0], me: labels.me };
        this.genders = { judge: 'm', opponent: c.opponent_gender === 'f' ? 'f' : 'm', witness: c.witness_gender === 'f' ? 'f' : 'm' };
        this.art.innerHTML = buildSvg(c);
        this.svg = this.art.querySelector('svg');
        this.gavelEl = this.svg.querySelector('#cs-gavel');
        [['#cs-plate-judge', labels.judge, 88], ['#cs-plate-opp', c.opponent, 122], ['#cs-plate-wit', labels.witness, 126]].forEach(function (row) {
            var node = self.svg.querySelector(row[0]), text = String(row[1] || '').toUpperCase();
            node.textContent = text;
            // Длинная надпись сжимается по ширине таблички, а не вылезает за рамку.
            if (text.length > 11) { node.setAttribute('textLength', String(row[2])); node.setAttribute('lengthAdjust', 'spacingAndGlyphs'); }
        });
        this.chip.textContent = labels.role;
        this.clear();
        this.root.classList.remove('enter');
        void this.root.offsetWidth;
        this.root.classList.add('enter');
    };

    // Подсветка говорящего; остальные поворачивают взгляд к нему.
    Scene.prototype.speaker = function (who) {
        this.root.classList.toggle('has-speaker', !!who);
        if (!this.svg) return;
        this.svg.querySelectorAll('.char').forEach(function (g) {
            var me = g.getAttribute('data-who');
            g.classList.toggle('speaking', me === who);
            var gx = 0;
            if (who && me !== who && POS[who] != null && POS[me] != null) {
                gx = Math.max(-1, Math.min(1, (POS[who] - POS[me]) / 320));
            } else if (me === 'opponent') gx = 0.45;
            else if (me === 'witness') gx = -0.45;
            g.style.setProperty('--gx', gx.toFixed(2));
        });
    };

    Scene.prototype.mouth = function (who, level) {
        if (!this.svg) return;
        var g = this.svg.querySelector('.char[data-who="' + who + '"]');
        if (g) g.style.setProperty('--open', (0.15 + level * 1.15).toFixed(2));
    };

    Scene.prototype.gesture = function (who, name, on) {
        if (!this.svg) return;
        var g = this.svg.querySelector('.char[data-who="' + who + '"]');
        if (g) g.classList.toggle(name, on !== false);
    };

    Scene.prototype.clear = function () {
        this.finish();
        if (this.voice) this.voice.stop();
        this.cap.hidden = true;
        this.speaker(null);
        if (this.svg) this.svg.querySelectorAll('.char.raise').forEach(function (g) { g.classList.remove('raise'); });
    };

    // Реплика: печатается по буквам, говорящий подсвечен; если есть голос — текст и губы идут за ним.
    Scene.prototype.say = function (who, text, o) {
        var self = this;
        o = o || {};
        this.finish();
        this.thinkingOn = false;
        this.speaker(who);
        this.cap.hidden = false;
        this.cap.className = 'caption ' + who;
        this.capName.textContent = this.names[who] || '';
        this.capText.textContent = '';
        this.capNext.style.visibility = 'hidden';

        var speech = (!o.silent && !this.fast && this.voice && who !== 'me')
            ? this.voice.speak(who, this.genders[who] || 'm', text, function (lv) { self.mouth(who, lv); })
            : null;
        this.root.classList.toggle('voiced', !!speech);
        // Пока голос загружается, в окне бегут точки, а не пустота.
        if (speech && !(REDUCED || this.fast || o.instant)) this.cap.classList.add('loading');

        return new Promise(function (resolve) {
            var n = text.length, timer = null, dwell = null, typing = true, finished = false;
            function done() {
                if (finished) return;
                finished = true;
                clearInterval(timer);
                clearTimeout(dwell);
                if (speech) speech.stop();
                self.root.classList.remove('voiced');
                self.mouth(who, 0);
                self.pending = null;
                resolve();
            }
            function typed() {
                if (!typing) return;
                typing = false;
                clearInterval(timer);
                self.cap.classList.remove('loading');
                self.capText.textContent = text;
                self.capNext.style.visibility = 'visible';
                if (speech && !self.fast) {
                    // После голоса — короткая пауза, затем следующая реплика.
                    speech.done.then(function () { dwell = setTimeout(done, 450); });
                } else {
                    var wait = self.fast ? 150 : (o.dwell != null ? o.dwell : Math.min(900 + n * 16, 4600));
                    dwell = setTimeout(done, wait);
                }
            }
            function type(seconds) {
                if (!typing) return;
                self.cap.classList.remove('loading');
                var ms = Math.max(600, (seconds || n / 70) * 1000 * 0.9);
                var step = Math.max(1, Math.ceil(n / (ms / 16)));
                var i = 0;
                timer = setInterval(function () {
                    i += step;
                    if (i >= n) { typed(); return; }
                    self.capText.textContent = text.slice(0, i);
                }, 16);
            }
            self.pending = {
                done: done,
                advance: function () { if (typing) typed(); else done(); },
            };
            if (REDUCED || self.fast || o.instant) { typed(); return; }
            if (speech) {
                // Длительность речи известна после загрузки звука; печать подстраивается под неё.
                var started = false;
                var go = function (sec) { if (!started) { started = true; type(sec); } };
                speech.ready.then(go);
                setTimeout(function () { go(null); }, 2500);
            } else {
                type(Math.min(2.2, n / 70));
            }
        });
    };

    Scene.prototype.advance = function () { if (this.pending) this.pending.advance(); };
    Scene.prototype.finish = function () { if (this.pending) this.pending.done(); };
    Scene.prototype.skipAll = function () { this.fast = true; if (this.voice) this.voice.stop(); this.finish(); };
    Scene.prototype.resetFast = function () { this.fast = false; };

    // «Суд совещается»: подсвечен судья, в окне диалога бегут точки.
    Scene.prototype.thinking = function (on, label) {
        if (!on) { if (this.thinkingOn) { this.cap.hidden = true; this.speaker(null); } this.thinkingOn = false; return; }
        this.finish();
        this.thinkingOn = true;
        this.speaker('judge');
        this.cap.hidden = false;
        this.cap.className = 'caption judge thinking';
        this.capName.textContent = this.names.judge || '';
        this.capText.textContent = label || '';
        this.capNext.style.visibility = 'hidden';
    };

    Scene.prototype.stamp = function (text, kind) {
        var self = this, node = document.createElement('div');
        node.className = 'stamp ' + (kind || 'bad');
        node.textContent = text;
        this.stampLayer.appendChild(node);
        this.play('stamp');
        return new Promise(function (resolve) {
            setTimeout(function () { node.classList.add('out'); }, self.fast ? 500 : 1150);
            setTimeout(function () { node.remove(); resolve(); }, self.fast ? 800 : 1500);
        });
    };

    Scene.prototype.gavel = function (times) {
        var self = this, n = times || 1;
        return new Promise(function (resolve) {
            var k = 0;
            (function hit() {
                if (self.gavelEl) {
                    self.gavelEl.classList.remove('hit');
                    void self.gavelEl.getBoundingClientRect();
                    self.gavelEl.classList.add('hit');
                }
                self.play('gavel');
                self.root.classList.remove('shake');
                void self.root.offsetWidth;
                if (!REDUCED) self.root.classList.add('shake');
                k += 1;
                if (k < n) setTimeout(hit, 380); else setTimeout(resolve, 420);
            })();
        });
    };

    Scene.prototype.float = function (score) {
        var node = document.createElement('div');
        node.className = 'float ' + (score >= 70 ? 'good' : score >= 50 ? 'mid' : 'low');
        node.textContent = (score >= 0 ? '+' : '') + score;
        this.root.appendChild(node);
        setTimeout(function () { node.remove(); }, 1700);
    };

    Scene.prototype.play = function (kind) {
        if (this.muted) return;
        if (kind === 'gavel') thump(190, 55, .16, .55, .5);
        else if (kind === 'stamp') thump(120, 45, .22, .6, .3);
    };

    window.CourtScene = {
        create: function (root, cfg) { return new Scene(root, cfg); },
        artwork: buildSvg,
    };
})();
