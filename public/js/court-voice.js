/* Голос персонажей симулятора суда.
 *
 * Два режима:
 *  - openai: mp3 с сервера (/api/tts), губы двигаются по громкости звука (AnalyserNode);
 *  - browser: голос браузера (speechSynthesis), у каждого персонажа своя высота и темп,
 *    губы двигаются в такт речи синтетически. Таджикского голоса в браузерах обычно нет:
 *    тогда реплика идёт без звука, только субтитрами.
 *
 * CourtVoice.speak(...) возвращает { ready: Promise<секунды>, done: Promise, stop() } или null,
 * если сказать нечем.
 */
(function () {
    'use strict';

    var PROFILE = {
        judge: { pitch: 0.78, rate: 0.93 },
        opponent: { pitch: 1.0, rate: 1.06 },
        witness: { pitch: 1.12, rate: 0.98 },
    };
    var FEMALE = /(irina|svetlana|ekaterina|dariya|daria|milena|alena|elena|anna|female|жен|google русский)/i;
    var MALE = /(pavel|dmitry|dmitri|yuri|maxim|male|муж)/i;

    var st = { mode: 'browser', api: '', lang: 'ru', muted: false, voices: [], current: null, cache: {}, ctx: null };

    function loadVoices() {
        if (!('speechSynthesis' in window)) return;
        var fill = function () { st.voices = window.speechSynthesis.getVoices() || []; };
        fill();
        window.speechSynthesis.onvoiceschanged = fill;
    }

    function pickVoice(lang, gender) {
        var list = st.voices.filter(function (v) { return (v.lang || '').toLowerCase().indexOf(lang) === 0; });
        if (!list.length) return null;
        var want = gender === 'f' ? FEMALE : MALE, avoid = gender === 'f' ? MALE : FEMALE;
        return list.filter(function (v) { return want.test(v.name); })[0]
            || list.filter(function (v) { return !avoid.test(v.name); })[0]
            || list[0];
    }

    function audioCtx() {
        if (!st.ctx) {
            var AC = window.AudioContext || window.webkitAudioContext;
            if (AC) { try { st.ctx = new AC(); } catch (e) { st.ctx = null; } }
        }
        if (st.ctx && st.ctx.state === 'suspended') st.ctx.resume();
        return st.ctx;
    }

    function cacheKey(who, gender, text) { return who + '|' + gender + '|' + text; }

    function fetchAudio(who, gender, text) {
        var key = cacheKey(who, gender, text);
        if (!st.cache[key]) {
            st.cache[key] = fetch(st.api + '/api/tts', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ text: text, who: who, gender: gender, lang: st.lang }),
            }).then(function (res) {
                if (!res.ok) throw new Error('tts ' + res.status);
                return res.blob();
            }).then(function (blob) { return URL.createObjectURL(blob); });
            st.cache[key].catch(function () { delete st.cache[key]; });
        }
        return st.cache[key];
    }

    // Облачный голос: mp3 + анализатор громкости для губ.
    function speakCloud(who, gender, text, level) {
        var audio = new Audio(), stopped = false, raf = 0, resolveDone, resolveReady;
        var done = new Promise(function (r) { resolveDone = r; });
        var ready = new Promise(function (r) { resolveReady = r; });
        function finish() {
            if (stopped) return;
            stopped = true;
            cancelAnimationFrame(raf);
            level(0);
            resolveReady(null);
            resolveDone();
        }
        fetchAudio(who, gender, text).then(function (url) {
            if (stopped) return;
            audio.src = url;
            var ctx = audioCtx(), analyser = null, data = null;
            if (ctx) {
                try {
                    var src = ctx.createMediaElementSource(audio);
                    analyser = ctx.createAnalyser();
                    analyser.fftSize = 512;
                    data = new Uint8Array(analyser.fftSize);
                    src.connect(analyser);
                    analyser.connect(ctx.destination);
                } catch (e) { analyser = null; }
            }
            audio.addEventListener('loadedmetadata', function () { resolveReady(isFinite(audio.duration) ? audio.duration : null); });
            audio.addEventListener('ended', finish);
            audio.addEventListener('error', finish);
            (function tick() {
                if (stopped) return;
                if (analyser) {
                    analyser.getByteTimeDomainData(data);
                    var sum = 0;
                    for (var i = 0; i < data.length; i++) { var v = (data[i] - 128) / 128; sum += v * v; }
                    level(Math.min(1, Math.sqrt(sum / data.length) * 5.5));
                }
                raf = requestAnimationFrame(tick);
            })();
            var played = audio.play();
            if (played && played.catch) played.catch(finish);
        }).catch(finish);
        return { ready: ready, done: done, stop: function () { try { audio.pause(); } catch (e) { /* ignore */ } finish(); } };
    }

    // Голос браузера: губы шевелятся синтетически, пока идёт речь.
    function speakBrowser(who, gender, text, level) {
        if (!('speechSynthesis' in window)) return null;
        var voice = pickVoice(st.lang === 'tg' ? 'tg' : 'ru', gender);
        if (!voice) return null;
        var p = PROFILE[who] || PROFILE.judge;
        var u = new SpeechSynthesisUtterance(text);
        u.voice = voice;
        u.lang = voice.lang;
        u.pitch = gender === 'f' ? Math.min(2, p.pitch + 0.18) : p.pitch;
        u.rate = p.rate;
        var stopped = false, raf = 0, resolveDone, t0 = performance.now();
        var done = new Promise(function (r) { resolveDone = r; });
        function finish() {
            if (stopped) return;
            stopped = true;
            cancelAnimationFrame(raf);
            level(0);
            resolveDone();
        }
        u.onend = finish;
        u.onerror = finish;
        (function tick() {
            if (stopped) return;
            var t = (performance.now() - t0) / 1000;
            level(Math.max(0, 0.45 + 0.4 * Math.sin(t * 17) * Math.sin(t * 5.3 + 1)));
            raf = requestAnimationFrame(tick);
        })();
        window.speechSynthesis.cancel();
        window.speechSynthesis.speak(u);
        return {
            ready: Promise.resolve(text.length / (13.5 * p.rate)),
            done: done,
            stop: function () { window.speechSynthesis.cancel(); finish(); },
        };
    }

    window.CourtVoice = {
        configure: function (o) {
            st.mode = o.mode === 'openai' ? 'openai' : 'browser';
            st.api = o.api || '';
            st.lang = o.lang === 'tg' ? 'tg' : 'ru';
            loadVoices();
        },
        mode: function () { return st.mode; },
        available: function () {
            if (st.mode === 'openai') return true;
            return !!pickVoice(st.lang === 'tg' ? 'tg' : 'ru', 'm');
        },
        setMuted: function (on) { st.muted = !!on; if (on) this.stop(); },
        prefetch: function (lines) {
            if (st.mode !== 'openai' || st.muted) return;
            (lines || []).forEach(function (l) { fetchAudio(l.who, l.gender, l.text).catch(function () {}); });
        },
        speak: function (who, gender, text, level) {
            if (st.muted || !text) return null;
            this.stop();
            var handle = st.mode === 'openai' ? speakCloud(who, gender, text, level || function () {}) : speakBrowser(who, gender, text, level || function () {});
            st.current = handle;
            return handle;
        },
        stop: function () { if (st.current) { st.current.stop(); st.current = null; } },
    };
})();
