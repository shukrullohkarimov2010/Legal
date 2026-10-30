/* Сцена зала заседания: иллюстрированные персонажи, подсветка говорящего, печать реплик,
 * молоток, штампы «Возражение / Принято / Отклонено», всплывающий балл и звук.
 *
 * Всё нарисовано кодом (SVG), внешних картинок нет. Текст реплик вставляется только через
 * textContent. CourtScene.create(контейнер, настройки) возвращает объект сцены.
 */
(function () {
    'use strict';

    var REDUCED = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var SKIN = {
        a: { skin: '#f0c6a0', shade: '#d9a27a' },
        b: { skin: '#dba67a', shade: '#bf8559' },
        c: { skin: '#c48b5e', shade: '#a36d44' },
    };

    // ------------------------------------------------------------------ персонажи

    // Бюст в рамке 120 x 150. role: judge | suit | casual; gender: m | f.
    function figure(o) {
        var skin = o.skin.skin, shade = o.skin.shade, hair = o.hair, s = '';
        if (o.gender === 'f') {
            s += '<path d="M30 62 Q26 26 60 24 Q94 26 90 62 L96 120 Q88 126 80 117 L80 86 L40 86 L40 117 Q32 126 24 120 Z" fill="' + hair + '"/>';
        }
        if (o.role === 'judge') {
            s += '<path d="M4 150 C4 110 26 100 60 100 C94 100 116 110 116 150 Z" fill="#151a2c"/>';
            s += '<path d="M42 100 Q60 124 78 100 L84 108 Q60 138 36 108 Z" fill="#f5f2ea"/>';
            s += '<path d="M54 112 L60 150 L66 112 Z" fill="#e6e0d2"/>';
        } else if (o.role === 'suit') {
            s += '<path d="M4 150 C4 112 26 100 60 100 C94 100 116 112 116 150 Z" fill="' + o.cloth + '"/>';
            s += '<path d="M46 100 L60 128 L74 100 Z" fill="#f7f7f5"/>';
            s += '<path d="M46 100 L58 132 L38 150 L10 150 C12 122 28 106 46 100 Z" fill="rgba(0,0,0,.18)"/>';
            s += '<path d="M74 100 L62 132 L82 150 L110 150 C108 122 92 106 74 100 Z" fill="rgba(0,0,0,.18)"/>';
            if (o.gender === 'm') s += '<path d="M56 106 L64 106 L67 138 L60 146 L53 138 Z" fill="' + o.accent + '"/>';
            else s += '<path d="M50 104 Q60 118 70 104" fill="none" stroke="' + o.accent + '" stroke-width="3" stroke-linecap="round"/>';
        } else {
            s += '<path d="M4 150 C4 114 26 104 60 104 C94 104 116 114 116 150 Z" fill="' + o.cloth + '"/>';
            s += '<path d="M44 104 Q60 126 76 104" fill="' + shade + '"/>';
        }
        s += '<path d="M50 84 H70 V106 Q60 116 50 106 Z" fill="' + shade + '"/>';
        s += '<circle cx="34" cy="66" r="5.5" fill="' + skin + '"/><circle cx="86" cy="66" r="5.5" fill="' + skin + '"/>';
        s += '<ellipse cx="60" cy="62" rx="26" ry="30" fill="' + skin + '"/>';
        if (o.gender === 'f') {
            s += '<path d="M33 58 Q34 30 62 30 Q88 32 87 58 Q78 42 64 44 Q48 46 33 58 Z" fill="' + hair + '"/>';
        } else if (o.role === 'judge') {
            s += '<path d="M33 60 Q30 30 60 28 Q90 30 87 60 Q86 46 76 40 Q60 36 44 40 Q34 46 33 60 Z" fill="' + hair + '"/>';
        } else {
            s += '<path d="M33 58 Q31 28 60 28 Q89 28 87 58 Q82 42 62 42 Q42 42 33 58 Z" fill="' + hair + '"/>';
        }
        s += '<g class="brows" stroke="' + (o.role === 'judge' ? '#8b8b92' : '#2a2018') + '" stroke-width="2.4" fill="none" stroke-linecap="round">' +
             '<path d="M41 52 Q48 48 55 52"/><path d="M65 52 Q72 48 79 52"/></g>';
        s += '<g class="eyes" fill="#1c1712"><ellipse cx="48" cy="62" rx="2.8" ry="3.4"/><ellipse cx="72" cy="62" rx="2.8" ry="3.4"/></g>';
        if (o.glasses) {
            s += '<g fill="none" stroke="#2b2f3d" stroke-width="2"><circle cx="48" cy="62" r="8.5"/><circle cx="72" cy="62" r="8.5"/><path d="M56.5 62 H63.5"/></g>';
        }
        s += '<path d="M60 64 Q56 74 61 76" fill="none" stroke="' + shade + '" stroke-width="2" stroke-linecap="round"/>';
        s += '<ellipse class="mouth" cx="60" cy="84" rx="7" ry="1.9" fill="#7d2f33"/>';
        s += '<ellipse cx="42" cy="76" rx="5" ry="3" fill="rgba(255,120,110,.18)"/><ellipse cx="78" cy="76" rx="5" ry="3" fill="rgba(255,120,110,.18)"/>';
        return s;
    }

    function backFigure() {
        return '<path d="M-96 0 C-96 -52 -52 -78 0 -78 C52 -78 96 -52 96 0 Z" fill="#2c2a6b"/>' +
               '<path d="M-96 0 C-96 -52 -52 -78 0 -78" fill="none" stroke="rgba(167,139,250,.55)" stroke-width="3"/>' +
               '<path d="M-14 -78 L0 -58 L14 -78 Z" fill="#e8e6f2"/>' +
               '<circle cx="0" cy="-104" r="27" fill="#2a2018"/>' +
               '<path d="M-23 -112 Q0 -140 23 -112" fill="none" stroke="rgba(255,255,255,.14)" stroke-width="3"/>' +
               '<rect x="-12" y="-82" width="24" height="12" rx="5" fill="#d9a27a"/>';
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

    function buildSvg(c) {
        var opp = figure({ role: 'suit', gender: c.opponent_gender === 'f' ? 'f' : 'm', skin: c.opponent_gender === 'f' ? SKIN.a : SKIN.b, hair: c.opponent_gender === 'f' ? '#3b2418' : '#1d1814', cloth: c.opponent_gender === 'f' ? '#7f1d1d' : '#1e3a8a', accent: c.opponent_gender === 'f' ? '#fbbf24' : '#dc2626' });
        var wit = figure({ role: 'casual', gender: c.witness_gender === 'f' ? 'f' : 'm', skin: c.witness_gender === 'f' ? SKIN.b : SKIN.c, hair: c.witness_gender === 'f' ? '#1a1410' : '#2a1d14', cloth: c.witness_gender === 'f' ? '#0f766e' : '#b45309' });
        var judge = figure({ role: 'judge', gender: 'm', skin: SKIN.a, hair: '#c9ccd6', glasses: true });

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
        // колонны и окна
        '<g opacity=".9">' +
          '<rect x="20" y="0" width="46" height="350" fill="rgba(255,255,255,.04)"/><rect x="894" y="0" width="46" height="350" fill="rgba(255,255,255,.04)"/>' +
          '<rect x="14" y="0" width="58" height="10" fill="rgba(255,255,255,.07)"/><rect x="888" y="0" width="58" height="10" fill="rgba(255,255,255,.07)"/>' +
          '<path d="M118 30 H178 V180 H118 Z" fill="rgba(120,160,255,.10)" stroke="rgba(255,255,255,.12)"/><path d="M782 30 H842 V180 H782 Z" fill="rgba(120,160,255,.10)" stroke="rgba(255,255,255,.12)"/>' +
          '<path d="M148 30 V180 M118 105 H178 M812 30 V180 M782 105 H842" stroke="rgba(255,255,255,.12)"/>' +
        '</g>' +
        // дубовая панель стены
        '<rect x="0" y="262" width="960" height="92" fill="url(#cs-wood)" opacity=".92"/>' + panels(0, 262, 960, 92, 12) +
        '<rect x="0" y="258" width="960" height="6" fill="#b07c46"/>' +
        // пол
        '<rect x="0" y="352" width="960" height="88" fill="url(#cs-floor)"/>' +
        '<g stroke="rgba(255,255,255,.05)"><path d="M0 380 H960 M0 410 H960"/><path d="M200 352 L60 440 M420 352 L360 440 M540 352 L600 440 M760 352 L900 440"/></g>' +
        // эмблема
        '<g><circle cx="480" cy="96" r="66" fill="rgba(255,255,255,.04)" stroke="rgba(202,162,74,.55)" stroke-width="2"/>' +
          '<circle cx="480" cy="96" r="56" fill="none" stroke="rgba(202,162,74,.25)"/>' +
          '<g stroke="rgba(202,162,74,.5)" stroke-width="3" fill="none" stroke-linecap="round" transform="translate(480 96)"><path d="M0 -44 V40 M-26 40 H26 M-34 -26 H34 M-34 -26 L-48 4 H-20 Z M34 -26 L20 4 H48 Z"/></g></g>' +
        // луч света
        '<rect width="960" height="440" fill="url(#cs-light)"/>' +

        // судья
        '<g class="char" data-who="judge"><g transform="translate(420 40)">' + judge + '</g></g>' +
        '<ellipse cx="480" cy="256" rx="170" ry="8" fill="rgba(0,0,0,.35)"/>' +
        '<rect x="344" y="176" width="272" height="15" rx="3" fill="url(#cs-wood2)"/><rect x="352" y="191" width="256" height="64" fill="url(#cs-wood)"/>' + panels(352, 191, 256, 64, 3) +
        plate(430, 205, 100, 'cs-plate-judge', 'СУДЬЯ') +
        '<g id="cs-gavel" style="transform-origin: 568px 176px"><rect x="556" y="152" width="28" height="12" rx="3" fill="#6b4423"/><rect x="568" y="160" width="5" height="20" fill="#8a5a32"/></g>' +

        // противная сторона
        '<g class="char" data-who="opponent"><g transform="translate(78 132) scale(1.05)">' + opp + '</g></g>' +
        '<ellipse cx="146" cy="296" rx="130" ry="7" fill="rgba(0,0,0,.35)"/>' +
        '<rect x="22" y="262" width="248" height="14" rx="3" fill="url(#cs-wood2)"/><rect x="30" y="276" width="232" height="66" fill="url(#cs-wood)"/>' + panels(30, 276, 232, 66, 3) +
        plate(78, 292, 136, 'cs-plate-opp', 'ПРОКУРОР') +

        // свидетель
        '<g class="char" data-who="witness"><g transform="translate(760 122) scale(1.05)">' + wit + '</g></g>' +
        '<ellipse cx="822" cy="296" rx="132" ry="7" fill="rgba(0,0,0,.35)"/>' +
        '<rect x="696" y="252" width="252" height="14" rx="3" fill="url(#cs-wood2)"/><rect x="704" y="266" width="236" height="78" fill="url(#cs-wood)"/>' + panels(704, 266, 236, 78, 3) +
        plate(752, 286, 140, 'cs-plate-wit', 'СВИДЕТЕЛЬ') +

        // пользователь со спины
        '<g class="char me" data-who="me"><g transform="translate(480 452)">' + backFigure() + '</g></g>' +
        '<rect width="960" height="440" fill="url(#cs-vig)"/>' +
        '</svg>';
    }

    // ------------------------------------------------------------------ звук

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
        this.names = this.cfg.names || {};
        this.muted = false;
        try { this.muted = localStorage.getItem('court-sound') === 'off'; } catch (e) { /* нет хранилища */ }
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

        var tools = document.createElement('div');
        tools.className = 'scene-tools';
        this.soundBtn = document.createElement('button');
        this.soundBtn.type = 'button';
        this.soundBtn.className = 'scene-btn';
        this.soundBtn.addEventListener('click', function () {
            self.muted = !self.muted;
            try { localStorage.setItem('court-sound', self.muted ? 'off' : 'on'); } catch (e) { /* ignore */ }
            self.paintSound();
            if (!self.muted) thump(140, 60, .12, .25);
        });
        tools.appendChild(this.soundBtn);
        root.appendChild(tools);
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
        this.soundBtn.textContent = this.muted ? '🔇' : '🔊';
        this.soundBtn.setAttribute('aria-label', this.muted ? 'Включить звук' : 'Выключить звук');
        this.soundBtn.title = this.soundBtn.getAttribute('aria-label');
    };

    Scene.prototype.setCase = function (c, labels) {
        this.names = { judge: labels.judge, opponent: c.opponent, witness: c.witness.split(',')[0], me: labels.me };
        this.art.innerHTML = buildSvg(c);
        this.svg = this.art.querySelector('svg');
        this.gavelEl = this.svg.querySelector('#cs-gavel');
        var self = this;
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

    Scene.prototype.speaker = function (who) {
        this.root.classList.toggle('has-speaker', !!who);
        if (!this.svg) return;
        this.svg.querySelectorAll('.char').forEach(function (g) {
            g.classList.toggle('speaking', g.getAttribute('data-who') === who);
        });
    };

    Scene.prototype.clear = function () {
        this.finish(true);
        this.cap.hidden = true;
        this.speaker(null);
    };

    // Реплика в «окне диалога»: печатается по буквам, говорящий подсвечен.
    Scene.prototype.say = function (who, text, o) {
        var self = this;
        o = o || {};
        this.finish(true);
        this.thinkingOn = false;
        this.speaker(who);
        this.cap.hidden = false;
        this.cap.className = 'caption ' + who;
        this.capName.textContent = this.names[who] || '';
        this.capText.textContent = '';
        this.capNext.style.visibility = 'hidden';
        var instant = REDUCED || this.fast || o.instant;
        return new Promise(function (resolve) {
            var i = 0, n = text.length, timer = null, dwell = null, typing = true;
            var step = Math.max(1, Math.ceil(n / 130));
            function done() {
                clearInterval(timer);
                clearTimeout(dwell);
                self.pending = null;
                resolve();
            }
            function typed() {
                typing = false;
                clearInterval(timer);
                self.capText.textContent = text;
                self.capNext.style.visibility = 'visible';
                var wait = self.fast ? 150 : (o.dwell != null ? o.dwell : Math.min(900 + n * 16, 4600));
                dwell = setTimeout(done, wait);
            }
            self.pending = {
                done: done,
                advance: function () { if (typing) typed(); else done(); },
            };
            if (instant) { typed(); return; }
            timer = setInterval(function () {
                i += step;
                if (i >= n) { typed(); return; }
                self.capText.textContent = text.slice(0, i);
            }, 16);
        });
    };

    Scene.prototype.advance = function () { if (this.pending) this.pending.advance(); };
    Scene.prototype.finish = function () { if (this.pending) this.pending.done(); };
    Scene.prototype.skipAll = function () { this.fast = true; this.advance(); };
    Scene.prototype.resetFast = function () { this.fast = false; };

    // «Суд совещается»: подсвечен судья, в окне диалога бегут точки.
    Scene.prototype.thinking = function (on, label) {
        if (!on) { if (this.thinkingOn) { this.cap.hidden = true; this.speaker(null); } this.thinkingOn = false; return; }
        this.finish(true);
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
    };
})();
