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
            board: '📌 Доска доказательств', present: '📎 Предъявить суду', presented: 'предъявлено', admittedBadge: '✓ приобщено', rejectedBadge: '✕ отказано',
            admitted: 'Приобщено', refused: 'Отказано', newBadge: 'НОВОЕ', boardAdded: function (t) { return '📌 На доску добавлено: ' + t; },
            exhibitHint: 'Объясните суду, что доказывает эта улика и почему её нужно приобщить.', removeExhibit: 'Убрать улику',
            custom: {
                badge: 'Своё дело', title: 'Опишите свою ситуацию', lead: 'ИИ соберёт из неё дело для заседания: стороны, свидетеля и улики. Имена заменит вымышленными.',
                placeholder: 'Например: я снимал квартиру, съехал, а хозяин не возвращает залог 3000 сомони и говорит, что я испортил пол…',
                role: 'Ваша роль', roles: ['Определит ИИ', 'защитник подсудимого', 'представитель истца', 'представитель ответчика', 'представитель работника', 'представитель работодателя'],
                build: '✨ Собрать дело', building: 'Суд готовит материалы дела…', again: '✎ Изменить описание', evidence: 'Улики',
                err: 'Не удалось собрать дело. Уточните описание и попробуйте ещё раз.', short: 'Опишите подробнее: кто участники, что произошло, о чём спор.',
            },
            announce: 'Суд вернулся из совещательной комнаты. Оглашается решение.',
            outcomeBy: {
                criminal: { won: 'Оправдан', partial: 'Ответственность смягчена', lost: 'Признан виновным' },
                civil: { won: 'Иск удовлетворён', partial: 'Иск удовлетворён частично', lost: 'В иске отказано' },
                labor: { won: 'Требования удовлетворены', partial: 'Требования удовлетворены частично', lost: 'В требованиях отказано' },
            },
            doc: {
                state: 'Республика Таджикистан', court: 'Учебный суд LegalAI Pro', titleBy: { criminal: 'Приговор', civil: 'Решение', labor: 'Решение' },
                onCase: 'по делу', city: 'г. Душанбе', number: 'Дело №', resolvedBy: { criminal: 'Суд приговорил:', civil: 'Суд решил:', labor: 'Суд решил:' },
                judge: 'Председательствующий судья', judgeName: 'ИИ-судья LegalAI', seal: 'LEGALAI PRO • УЧЕБНЫЙ СУД • ',
                note: 'Учебный документ, создан симулятором. Юридической силы не имеет.',
            },
            listen: '🔊 Прослушать решение', stopListen: '⏹ Остановить', pdf: '🖨 Сохранить в PDF', yourResult: 'Ваш результат',
            medals: { gold: 'Золото · Адвокат года', silver: 'Серебро · Сильный юрист', bronze: 'Бронза · Уверенный старт', novice: 'Стажёр · Есть куда расти' },
            stats: { rounds: ['раунд', 'раунда', 'раундов'], cited: ['статья по делу', 'статьи по делу', 'статей по делу'], objections: ['возражение', 'возражения', 'возражений'], sustained: 'принято' },
            soundOn: 'Включить звук и голос', soundOff: 'Выключить звук и голос',
            voiceFree: 'lama:3.1 — бесплатный голос браузера (без таджикского)',
            voicePaid: 'legal — живой голос OpenAI, около $0,015 за минуту речи',
            voiceOff: 'legal недоступен: на сервере нет ключа OpenAI или платный голос выключен (COURT_TTS=off)',
            protocol: 'Протокол заседания', objection: 'Возражение!', accepted: 'Принято', rejected: 'Отклонено', skip: 'Пропустить реплики',
            opening: function (c) { return 'Заседание объявляется открытым. Рассматривается дело: «' + c.title + '». Слово предоставляется стороне: ' + c.role + '.'; },
            youAre: function (c) { return 'Вы — ' + c.role + '. ' + c.goal; },
            maxReached: 'Достигнут предел раундов. Завершите заседание, чтобы услышать решение суда.',
            errTurn: 'Не удалось получить ответ суда. Повторите реплику.', errCases: 'Не удалось загрузить дела. Проверьте, запущен ли сервер.',
            thinking: 'Суд совещается', deliberating: 'Суд удаляется на совещание…', errVerdict: 'Не удалось вынести решение. Повторите.',
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
            board: '📌 Тахтаи далелҳо', present: '📎 Ба суд пешниҳод кардан', presented: 'пешниҳод шуд', admittedBadge: '✓ ҳамроҳ шуд', rejectedBadge: '✕ рад шуд',
            admitted: 'Ҳамроҳ шуд', refused: 'Рад шуд', newBadge: 'НАВ', boardAdded: function (t) { return '📌 Ба тахта илова шуд: ' + t; },
            exhibitHint: 'Ба суд фаҳмонед, ки ин далел чиро исбот мекунад ва чаро бояд ба парванда ҳамроҳ шавад.', removeExhibit: 'Далелро бардоштан',
            custom: {
                badge: 'Парвандаи худ', title: 'Вазъияти худро тавсиф кунед', lead: 'Зеҳни сунъӣ аз он парванда месозад: тарафҳо, шоҳид ва далелҳо. Номҳоро бо номҳои тахайюлӣ иваз мекунад.',
                placeholder: 'Масалан: ман хонаро ба иҷора гирифта будам, баромадам, вале соҳибхона гарави 3000 сомониро барнамегардонад…',
                role: 'Нақши шумо', roles: ['Зеҳни сунъӣ муайян мекунад', 'ҳимоятгари судшаванда', 'намояндаи даъвогар', 'намояндаи ҷавобгар', 'намояндаи корманд', 'намояндаи корфармо'],
                build: '✨ Парванда сохтан', building: 'Суд маводҳои парвандаро омода мекунад…', again: '✎ Тағйир додани тавсиф', evidence: 'Далелҳо',
                err: 'Парванда сохта нашуд. Тавсифро дақиқ карда, бори дигар кӯшиш кунед.', short: 'Муфассалтар нависед: иштирокчиён кистанд, чӣ шуд, баҳс дар бораи чист.',
            },
            announce: 'Суд аз утоқи машварат баргашт. Қарор эълон карда мешавад.',
            outcomeBy: {
                criminal: { won: 'Беайб дониста шуд', partial: 'Ҷавобгарӣ сабук карда шуд', lost: 'Гунаҳгор дониста шуд' },
                civil: { won: 'Даъво қонеъ гардонида шуд', partial: 'Даъво қисман қонеъ гардонида шуд', lost: 'Даъво рад карда шуд' },
                labor: { won: 'Талабот қонеъ гардонида шуд', partial: 'Талабот қисман қонеъ гардонида шуд', lost: 'Талабот рад карда шуд' },
            },
            doc: {
                state: 'Ҷумҳурии Тоҷикистон', court: 'Суди таълимии LegalAI Pro', titleBy: { criminal: 'Ҳукм', civil: 'Ҳалнома', labor: 'Ҳалнома' },
                onCase: 'оид ба парванда', city: 'ш. Душанбе', number: 'Парвандаи №', resolvedBy: { criminal: 'Суд ҳукм кард:', civil: 'Суд ҳал кард:', labor: 'Суд ҳал кард:' },
                judge: 'Судяи раисикунанда', judgeName: 'Судяи зеҳни сунъии LegalAI', seal: 'LEGALAI PRO • СУДИ ТАЪЛИМӢ • ',
                note: 'Ҳуҷҷати таълимӣ, аз ҷониби симулятор сохта шудааст. Қувваи ҳуқуқӣ надорад.',
            },
            listen: '🔊 Гӯш кардани қарор', stopListen: '⏹ Қатъ кардан', pdf: '🖨 Ҳамчун PDF нигоҳ доштан', yourResult: 'Натиҷаи шумо',
            medals: { gold: 'Тилло · Ҳуқуқшиноси сол', silver: 'Нуқра · Ҳуқуқшиноси қавӣ', bronze: 'Биринҷӣ · Оғози боэътимод', novice: 'Таҷрибаомӯз · Ҷои рушд ҳаст' },
            stats: { rounds: ['давр'], cited: ['модда ба парванда'], objections: ['эътироз'], sustained: 'қабул шуд' },
            soundOn: 'Овоз ва садоро фаъол кардан', soundOff: 'Овоз ва садоро хомӯш кардан',
            voiceFree: 'lama:3.1 — овози ройгони браузер (бе забони тоҷикӣ)',
            voicePaid: 'legal — овози зиндаи OpenAI, тақрибан $0,015 барои як дақиқаи нутқ',
            voiceOff: 'legal дастрас нест: дар сервер калиди OpenAI нест ё овози пулакӣ хомӯш аст (COURT_TTS=off)',
            protocol: 'Протоколи мурофиа', objection: 'Эътироз!', accepted: 'Қабул шуд', rejected: 'Рад шуд', skip: 'Гузаштан аз гуфтугӯ',
            opening: function (c) { return 'Маҷлиси суд кушода эълон мешавад. Парванда баррасӣ мешавад: «' + c.title + '». Суханро ба тараф медиҳам: ' + c.role + '.'; },
            youAre: function (c) { return 'Шумо — ' + c.role + '. ' + c.goal; },
            maxReached: 'Шумораи давраҳо ба охир расид. Мурофиаро анҷом диҳед, то қарори судро шунавед.',
            errTurn: 'Ҷавоби суд гирифта нашуд. Баромадро такрор кунед.', errCases: 'Парвандаҳо бор нашуданд. Санҷед, ки сервер кор мекунад.',
            thinking: 'Суд машварат мекунад', deliberating: 'Суд ба утоқи машварат меравад…', errVerdict: 'Қарор баровардан муяссар нашуд. Такрор кунед.',
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

    var st = { cases: [], c: null, history: [], scores: [], cited: [], action: 'argue', busy: false, turn: 0, max: 10, done: false, objections: 0, sustained: 0,
               board: [], exhibit: null, custom: null, customText: '' };
    // Роль для своего дела уходит на сервер по-русски: промпты и корпус законов русские.
    var ROLE_RU = ['', 'защитник подсудимого', 'представитель истца', 'представитель ответчика', 'представитель работника', 'представитель работодателя'];
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
            initVoiceSwitch(!!r.data.voice_cloud, r.data.voice);
            renderCases();
        }).catch(function () {
            var box = $('cases');
            box.replaceChildren(el('div', 'err', T.errCases));
        });
    }

    // Переключатель голоса: «lama:3.1» — бесплатный голос браузера, «legal» — живой голос OpenAI.
    // Выбор запоминается в браузере; если платный голос на сервере недоступен, кнопка выключена.
    function initVoiceSwitch(cloud, fallback) {
        var saved = null;
        try { saved = localStorage.getItem('court-voice-mode'); } catch (e) { /* нет хранилища */ }
        var mode = saved === 'openai' || saved === 'browser' ? saved : fallback;
        if (mode === 'openai' && !cloud) mode = 'browser';
        var seg = $('voiceSeg');
        var apply = function (m) {
            mode = m;
            window.CourtVoice.configure({ mode: m, api: cfg.api, lang: cfg.lang });
            seg.querySelectorAll('button').forEach(function (b) { b.setAttribute('aria-pressed', String(b.dataset.mode === m)); });
        };
        seg.querySelectorAll('button').forEach(function (b) {
            var paid = b.dataset.mode === 'openai';
            b.title = paid ? (cloud ? T.voicePaid : T.voiceOff) : T.voiceFree;
            b.disabled = paid && !cloud;
            b.onclick = function () {
                window.CourtVoice.stop();
                apply(b.dataset.mode);
                try { localStorage.setItem('court-voice-mode', b.dataset.mode); } catch (e) { /* ignore */ }
            };
        });
        apply(mode);
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
        box.appendChild(customCard());
    }

    // ------------------------------------------------------------------ своё дело

    function customCard() {
        var C = T.custom, card = el('article', 'case custom');
        card.appendChild(el('span', 'kind custom', '✍️ ' + C.badge));
        card.appendChild(el('h3', null, C.title));
        card.appendChild(el('p', 'facts-lead', C.lead));
        var area = el('textarea', 'custom-input');
        area.rows = 5;
        area.maxLength = 2000;
        area.placeholder = C.placeholder;
        area.value = st.customText;
        var label = el('label', 'custom-role');
        label.appendChild(el('span', null, C.role));
        var select = el('select');
        C.roles.forEach(function (r, i) { var o = el('option', null, r); o.value = String(i); select.appendChild(o); });
        label.appendChild(select);
        var err = el('div', 'err');
        var btn = el('button', 'btn', C.build);
        btn.type = 'button';
        btn.addEventListener('click', function () {
            var text = area.value.trim();
            if (text.length < 30) { err.textContent = C.short; return; }
            st.customText = text;
            err.textContent = '';
            btn.disabled = true;
            area.disabled = true;
            btn.textContent = '⏳ ' + C.building;
            card.classList.add('building');
            api('/api/court/custom', { situation: text, role: ROLE_RU[Number(select.value)] || '', lang: cfg.lang }).then(function (r) {
                if (!r.ok) throw new Error(r.data.error || 'custom');
                st.custom = r.data.source;
                card.replaceWith(customPreview(r.data.case));
            }).catch(function (e) {
                card.classList.remove('building');
                err.textContent = (e && e.message && e.message !== 'custom') ? e.message : C.err;
                btn.disabled = false;
                area.disabled = false;
                btn.textContent = C.build;
            });
        });
        card.append(area, label, err, btn);
        return card;
    }

    // Собранное дело: та же карточка, что у готовых дел, плюс улики.
    function customPreview(c) {
        var C = T.custom, card = el('article', 'case custom ready');
        card.appendChild(el('span', 'kind ' + c.kind, '✍️ ' + (T.kinds[c.kind] || c.kind)));
        card.appendChild(el('h3', null, c.title));
        card.appendChild(el('div', 'role', T.role + ': ' + c.role));
        card.appendChild(el('p', 'facts', c.facts));
        if (c.evidence && c.evidence.length) {
            var ev = el('div', 'ev-mini');
            c.evidence.forEach(function (e) { ev.appendChild(el('span', null, e.icon + ' ' + e.title)); });
            card.appendChild(ev);
        }
        card.appendChild(el('p', 'goal', '🎯 ' + T.goal + ': ' + c.goal));
        var go = el('button', 'btn', '⚖️ ' + T.start);
        go.type = 'button';
        go.addEventListener('click', function () { startCase(c); });
        var edit = el('button', 'btn ghost', C.again);
        edit.type = 'button';
        edit.addEventListener('click', function () { card.replaceWith(customCard()); });
        var row = el('div', 'case-actions');
        row.append(go, edit);
        card.appendChild(row);
        return card;
    }

    function customPayload() { return st.c && st.c.id === 'custom' ? st.custom : undefined; }

    // ------------------------------------------------------------------ доска доказательств

    function renderBoard() {
        var box = $('board');
        box.replaceChildren();
        box.hidden = !st.board.length;
        var head = el('div', 'board-head');
        head.appendChild(el('span', null, T.board));
        box.appendChild(head);
        var grid = el('div', 'board-grid');
        st.board.forEach(function (item, i) {
            var card = el('div', 'ev' + (item.state ? ' ' + item.state : '') + (st.exhibit === item ? ' picked' : '') + (item.new && !item.shown ? ' fresh' : ''));
            item.shown = true;
            card.style.setProperty('--tilt', (((i * 7) % 5) - 2) * 0.8 + 'deg');
            card.appendChild(el('i', 'pin'));
            if (item.new) card.appendChild(el('span', 'ev-new', T.newBadge));
            var top = el('div', 'ev-top');
            top.append(el('span', 'ev-icon', item.icon || '📎'), el('b', null, item.title));
            card.append(top, el('p', 'ev-detail', item.detail));
            var foot = el('div', 'ev-foot');
            if (item.state === 'admitted') foot.appendChild(el('span', 'ev-state ok', T.admittedBadge));
            else if (item.state === 'rejected') foot.appendChild(el('span', 'ev-state bad', T.rejectedBadge));
            else if (item.state === 'presented') foot.appendChild(el('span', 'ev-state', T.presented));
            else {
                var b = el('button', 'ev-btn', st.exhibit === item ? '✓ ' + T.present.replace(/^\S+\s/, '') : T.present);
                b.type = 'button';
                b.addEventListener('click', function () { pickExhibit(item); });
                foot.appendChild(b);
            }
            card.appendChild(foot);
            item.node = card;
            grid.appendChild(card);
        });
        box.appendChild(grid);
    }

    function pickExhibit(item) {
        st.exhibit = st.exhibit === item ? null : item;
        renderChip();
        renderBoard();
        if (st.exhibit) $('input').focus({ preventScroll: true });
    }

    function renderChip() {
        var chip = $('exhibitChip');
        chip.replaceChildren();
        chip.hidden = !st.exhibit;
        $('input').placeholder = st.exhibit ? T.exhibitHint : placeholderFor(st.action);
        if (!st.exhibit) return;
        chip.appendChild(el('span', null, '📎 ' + st.exhibit.icon + ' ' + st.exhibit.title));
        var x = el('button', null, '✕');
        x.type = 'button';
        x.title = T.removeExhibit;
        x.setAttribute('aria-label', T.removeExhibit);
        x.addEventListener('click', function () { pickExhibit(st.exhibit); });
        chip.appendChild(x);
    }

    // Предъявленная улика «летит» с доски к судье.
    function flyToJudge(node) {
        if (!node || !node.isConnected) return;
        var from = node.getBoundingClientRect(), to = $('scene').getBoundingClientRect();
        var ghost = node.cloneNode(true);
        ghost.classList.add('ev-ghost');
        ghost.style.left = from.left + 'px';
        ghost.style.top = from.top + 'px';
        ghost.style.width = from.width + 'px';
        document.body.appendChild(ghost);
        var dx = to.left + to.width / 2 - (from.left + from.width / 2), dy = to.top + to.height * 0.4 - (from.top + from.height / 2);
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                ghost.style.transform = 'translate(' + dx + 'px,' + dy + 'px) scale(.3) rotate(-10deg)';
                ghost.style.opacity = '0';
            });
        });
        setTimeout(function () { ghost.remove(); }, 1000);
    }

    // ------------------------------------------------------------------ зал

    function speakerName(who) {
        if (who === 'opponent') return st.c.opponent;
        return T.speakers[who] || who;
    }

    function startCase(c) {
        st.c = c; st.history = []; st.scores = []; st.cited = []; st.action = 'argue'; st.busy = false; st.turn = 0; st.done = false;
        st.objections = 0; st.sustained = 0;
        st.exhibit = null;
        st.board = (c.evidence || []).map(function (e) { return { id: e.id, icon: e.icon, title: e.title, detail: e.detail, state: '' }; });
        window.CourtVoice.prefetch([{ who: 'judge', gender: 'm', text: T.opening(c) }]);
        $('caseTitle').textContent = c.title;
        $('caseRole').textContent = T.role + ': ' + c.role;
        $('briefSummary').textContent = T.brief;
        $('briefText').textContent = c.facts + '\n\n🧍 ' + c.witness;
        $('protocolSummary').textContent = T.protocol;
        $('log').replaceChildren();
        $('input').value = '';
        $('composeError').textContent = '';
        resetSide();
        buildActions();
        renderBoard();
        renderChip();
        show('screenRoom');
        scene.setCase(c, { judge: T.speakers.judge, witness: T.speakers.witness, me: T.speakers.me, role: T.role + ': ' + c.role });
        logLine('judge', T.opening(c));
        logLine('system', T.youAre(c));
        // Открытие заседания: удар молотка, затем слово судьи.
        st.busy = true;
        updateControls();
        scene.resetFast();
        scene.gavel(3).then(function () { return scene.say('judge', T.opening(c)); }).then(function () {
            scene.clear();
            st.busy = false;
            updateControls();
            $('input').focus({ preventScroll: true });
        });
    }

    function logLine(who, text, tag) {
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
        var action = st.action, exhibit = st.exhibit;
        $('composeError').textContent = '';
        var tag = action === 'witness' ? T.tags.witness + ': ' + st.c.witness : T.tags[action];
        if (exhibit) tag = (tag ? tag + ' · ' : '') + '📎 ' + exhibit.title;
        var mine = logLine('me', text, tag);
        var before = st.history.slice();
        st.busy = true;
        $('input').value = '';
        updateControls();
        if (exhibit) {
            flyToJudge(exhibit.node);
            exhibit.state = 'presented';
            st.exhibit = null;
            renderChip();
            renderBoard();
        }

        // Реплика звучит на сцене; возражение сопровождается штампом, пока суд думает.
        scene.resetFast();
        var waiting = true;
        var shown = scene.say('me', text, { instant: true, dwell: 700 });
        if (action === 'object') shown = shown.then(function () { return scene.stamp(T.objection, 'bad'); });
        shown.then(function () { if (waiting) scene.thinking(true, T.thinking); });

        api('/api/court/turn', {
            case_id: st.c.id, custom_case: customPayload(), history: before, message: text, action: action, lang: cfg.lang,
            exhibit: exhibit ? { id: exhibit.id, title: exhibit.title, detail: exhibit.detail } : undefined,
        }).then(function (r) {
            if (!r.ok) throw new Error(r.data.error || 'turn');
            waiting = false;
            var mineEntry = { speaker: 'user', text: text, action: action };
            if (exhibit) mineEntry.exhibit = exhibit.title;
            st.history = before.concat([mineEntry], r.data.lines.map(function (l) { return { speaker: l.speaker, text: l.text }; }));
            st.turn = r.data.turn;
            st.max = r.data.max_turns || st.max;
            st.scores.push(r.data.score);
            (r.data.citations || []).forEach(function (c) {
                var label = c.code + ', ст. ' + c.article;
                if (c.status === 'verified' && st.cited.indexOf(label) < 0) st.cited.push(label);
            });
            // Озвучка всех реплик заказывается сразу: пока идёт штамп и первая реплика, остальные уже готовы.
            window.CourtVoice.prefetch(r.data.lines.map(function (l) { return { who: l.speaker, gender: genderOf(l.speaker), text: l.text }; }));
            if (action === 'object') st.objections += 1;
            return shown.then(function () {
                scene.thinking(false);
                return playLines(r.data.lines, action, exhibit ? r.data.ruling : 'none', exhibit);
            }).then(function () {
                scene.float(r.data.score);
                renderSide(r.data);
                // Новые факты из показаний прикалываются к доске.
                (r.data.new_evidence || []).forEach(function (e) {
                    st.board.push({ id: e.id, icon: e.icon, title: e.title, detail: e.detail, state: '', new: true });
                    logLine('system', T.boardAdded(e.title));
                });
                if ((r.data.new_evidence || []).length) renderBoard();
            });
        }).catch(function (e) {
            waiting = false;
            scene.thinking(false);
            scene.clear();
            mine.remove();
            if (exhibit) {
                exhibit.state = '';
                st.exhibit = exhibit;
                renderChip();
                renderBoard();
            }
            $('input').value = text;
            $('composeError').textContent = (e && e.message && e.message !== 'turn') ? e.message : T.errTurn;
        }).then(function () {
            st.busy = false;
            scene.clear();
            if (st.turn >= st.max) logLine('system', T.maxReached);
            updateControls();
            if (!$('input').disabled) $('input').focus({ preventScroll: true });
        });
    }

    // Решение судьи по возражению: «принято» или «отклонено» (по словам в его реплике).
    function decisionOf(text) {
        var t = String(text || '').toLowerCase();
        if (/(не\s+принима|отклон|отказ|рад\s+(карда|мешавад|мекунад))/.test(t)) return 'rejected';
        if (/(принима|принят|удовлетвор|қабул)/.test(t)) return 'accepted';
        return null;
    }

    function genderOf(who) {
        if (who === 'opponent') return st.c.opponent_gender === 'f' ? 'f' : 'm';
        if (who === 'witness') return st.c.witness_gender === 'f' ? 'f' : 'm';
        return 'm';
    }

    var OBJECTS = /^\s*[«"]?\s*(возража|протестую|эътироз)/i;

    function playLines(lines, action, ruling, exhibit) {
        var stamped = false;
        // Решение о приобщении улики ставится штампом после первой реплики судьи (или в конце).
        var ruled = !(exhibit && (ruling === 'admitted' || ruling === 'rejected'));
        function rule() {
            ruled = true;
            exhibit.state = ruling;
            renderBoard();
            return scene.stamp(ruling === 'admitted' ? T.admitted : T.refused, ruling === 'admitted' ? 'good' : 'bad');
        }
        return lines.reduce(function (chain, line) {
            return chain.then(function () {
                logLine(line.speaker, line.text);
                // «Возражаю!» противной стороны сопровождается поднятой рукой.
                var raise = line.speaker === 'opponent' && OBJECTS.test(line.text);
                if (raise) scene.gesture('opponent', 'raise', true);
                var spoken = scene.say(line.speaker, line.text).then(function () {
                    if (raise) scene.gesture('opponent', 'raise', false);
                });
                if (action === 'object' && line.speaker === 'judge' && !stamped) {
                    var verdict = decisionOf(line.text);
                    if (verdict) {
                        stamped = true;
                        if (verdict === 'accepted') st.sustained += 1;
                        spoken = spoken.then(function () { return scene.stamp(T[verdict], verdict === 'accepted' ? 'good' : 'bad'); });
                    }
                }
                if (!ruled && line.speaker === 'judge') spoken = spoken.then(rule);
                return spoken;
            });
        }, Promise.resolve()).then(function () { if (!ruled) return rule(); });
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
        scene.resetFast();
        scene.thinking(true, T.deliberating);
        api('/api/court/verdict', { case_id: st.c.id, custom_case: customPayload(), history: st.history, scores: st.scores, cited: st.cited, lang: cfg.lang }).then(function (r) {
            if (!r.ok) throw new Error(r.data.error || 'verdict');
            st.done = true;
            var d = r.data;
            scene.thinking(false);
            window.CourtVoice.prefetch([{ who: 'judge', gender: 'm', text: T.announce }, { who: 'judge', gender: 'm', text: d.verdict }]);
            // Оглашение: молоток, судья зачитывает решение вслух, штамп с исходом, затем документ.
            var outcome = outcomeText(d.outcome);
            return scene.gavel(2)
                .then(function () { return scene.say('judge', T.announce); })
                .then(function () { return scene.say('judge', d.verdict); })
                .then(function () { return scene.stamp(outcome, (d.outcome === 'won' ? 'good' : d.outcome === 'lost' ? 'bad' : 'warn') + (outcome.length > 14 ? ' long' : '')); })
                .then(function () { scene.clear(); renderVerdict(d); });
        }).catch(function (e) {
            scene.thinking(false);
            scene.clear();
            $('composeError').textContent = (e && e.message && e.message !== 'verdict') ? e.message : T.errVerdict;
        }).then(function () {
            st.busy = false;
            updateControls();
        });
    }

    function list(title, items, kind) {
        var card = el('div', 'card v-list ' + (kind || ''));
        card.appendChild(el('h4', null, title));
        var ul = el('ul');
        items.forEach(function (t) { ul.appendChild(el('li', null, t)); });
        card.appendChild(ul);
        return card;
    }

    function outcomeText(outcome) {
        var byKind = T.outcomeBy[st.c && st.c.kind] || T.outcomeBy.civil;
        return byKind[outcome] || byKind.partial;
    }

    // Форма слова после числа: «1 раунд», «3 раунда», «5 раундов». В таджикском форма одна.
    function plural(n, forms) {
        if (forms.length < 3) return forms[0];
        var m10 = n % 10, m100 = n % 100;
        if (m10 === 1 && m100 !== 11) return forms[0];
        if (m10 >= 2 && m10 <= 4 && (m100 < 12 || m100 > 14)) return forms[1];
        return forms[2];
    }

    function medalOf(score) { return score >= 85 ? 'gold' : score >= 70 ? 'silver' : score >= 50 ? 'bronze' : 'novice'; }

    function countUp(node, to, ms) {
        var t0 = performance.now();
        (function frame(now) {
            var p = Math.min(1, (now - t0) / ms);
            node.textContent = String(Math.round(to * (1 - Math.pow(1 - p, 3))));
            if (p < 1) requestAnimationFrame(frame);
        })(t0);
    }

    // Радар по четырём критериям: сверху обоснование, справа логика, снизу доказательства, слева порядок.
    function radar(values) {
        var cx = 110, cy = 110, R = 80, ang = [-90, 0, 90, 180];
        var point = function (a, r) { var rad = a * Math.PI / 180; return (cx + r * Math.cos(rad)).toFixed(1) + ',' + (cy + r * Math.sin(rad)).toFixed(1); };
        var svg = '<svg viewBox="0 0 220 220" class="radar" aria-hidden="true">';
        [25, 50, 75, 100].forEach(function (p) { svg += '<polygon class="ring" points="' + ang.map(function (a) { return point(a, R * p / 100); }).join(' ') + '"/>'; });
        ang.forEach(function (a) { svg += '<line class="axis" x1="' + cx + '" y1="' + cy + '" x2="' + point(a, R).split(',')[0] + '" y2="' + point(a, R).split(',')[1] + '"/>'; });
        var pts = CRIT.map(function (k, i) { return point(ang[i], R * Math.max(4, Number(values[k]) || 0) / 100); });
        svg += '<g class="area-g"><polygon class="area" points="' + pts.join(' ') + '"/>';
        pts.forEach(function (p) { var xy = p.split(','); svg += '<circle class="dot" cx="' + xy[0] + '" cy="' + xy[1] + '" r="4"/>'; });
        svg += '</g></svg>';
        return svg;
    }

    function seal() {
        var box = el('div', 'seal');
        box.innerHTML = '<svg viewBox="0 0 140 140" aria-hidden="true"><defs><path id="sealPath" d="M70 70 m-50 0 a50 50 0 1 1 100 0 a50 50 0 1 1 -100 0"/></defs>' +
            '<circle cx="70" cy="70" r="64" class="s1"/><circle cx="70" cy="70" r="58" class="s2"/><circle cx="70" cy="70" r="38" class="s2"/>' +
            '<text class="st"><textPath href="#sealPath"></textPath></text>' +
            '<g class="sc" transform="translate(70 70)"><path d="M0 -22 V20 M-14 20 H14 M-20 -12 H20 M-20 -12 L-28 4 H-12 Z M20 -12 L12 4 H28 Z"/></g></svg>';
        box.querySelector('textPath').textContent = T.doc.seal + T.doc.seal;
        return box;
    }

    function confetti(host) {
        var layer = el('div', 'confetti');
        var colors = ['#fbbf24', '#34d399', '#818cf8', '#f472b6', '#60a5fa'];
        for (var i = 0; i < 46; i++) {
            var p = el('i');
            p.style.left = (Math.random() * 100).toFixed(1) + '%';
            p.style.background = colors[i % colors.length];
            p.style.animationDelay = (Math.random() * 0.9).toFixed(2) + 's';
            p.style.setProperty('--r', (Math.random() * 720 - 360).toFixed(0) + 'deg');
            p.style.setProperty('--x', (Math.random() * 160 - 80).toFixed(0) + 'px');
            layer.appendChild(p);
        }
        host.appendChild(layer);
        setTimeout(function () { layer.remove(); }, 4200);
    }

    var listening = null;

    function renderVerdict(d) {
        var box = $('verdictBody'), kind = st.c.kind, medal = medalOf(d.score);
        box.replaceChildren();

        // 1. Итог крупно: исход дела, балл, медаль, статистика заседания.
        var hero = el('section', 'vx-hero ' + d.outcome);
        var left = el('div', 'vx-left');
        left.appendChild(el('div', 'vx-kicker', '⚖️ ' + (T.doc.titleBy[kind] || T.verdictTitle)));
        left.appendChild(el('h2', 'vx-outcome', outcomeText(d.outcome)));
        left.appendChild(el('div', 'vx-case', st.c.title));
        var stats = el('div', 'vx-stats');
        [[st.turn, plural(st.turn, T.stats.rounds)], [st.cited.length, plural(st.cited.length, T.stats.cited)],
         [st.objections + (st.objections ? ' · ' + st.sustained + ' ' + T.stats.sustained : ''), plural(st.objections, T.stats.objections)]].forEach(function (s) {
            var chip = el('div', 'vx-stat');
            chip.append(el('b', null, String(s[0])), el('span', null, s[1]));
            stats.appendChild(chip);
        });
        left.appendChild(stats);
        var right = el('div', 'vx-right');
        var g = el('div', 'gauge big');
        g.innerHTML = '<svg viewBox="0 0 160 160"><circle class="track" cx="80" cy="80" r="70"></circle><circle class="fill" cx="80" cy="80" r="70"></circle></svg>';
        var num = el('div', 'num'), numB = el('b', null, '0');
        num.append(numB, el('span', null, T.of100));
        g.appendChild(num);
        right.append(el('div', 'vx-label', T.yourResult), g, el('div', 'medal ' + medal, T.medals[medal]));
        hero.append(left, right);
        box.appendChild(hero);
        requestAnimationFrame(function () { requestAnimationFrame(function () { gauge(g, d.score); countUp(numB, d.score, 1200); }); });
        if (d.outcome === 'won') confetti(hero);

        // 2. Документ: приговор или решение суда, с печатью.
        var tools = el('div', 'vx-tools');
        var listen = el('button', 'btn ghost', T.listen);
        listen.type = 'button';
        listen.addEventListener('click', function () {
            if (listening) { listening.stop(); listening = null; listen.textContent = T.listen; return; }
            listening = window.CourtVoice.speak('judge', 'm', d.verdict, function () {});
            if (!listening) return;
            listen.textContent = T.stopListen;
            listening.done.then(function () { listening = null; listen.textContent = T.listen; });
        });
        var pdf = el('button', 'btn ghost', T.pdf);
        pdf.type = 'button';
        pdf.addEventListener('click', function () { window.print(); });
        tools.append(listen, pdf);
        box.appendChild(tools);

        var doc = el('article', 'doc');
        var head = el('header', 'doc-head');
        var emblem = el('div', 'doc-emblem');
        emblem.innerHTML = '<svg viewBox="0 0 60 60" aria-hidden="true"><circle cx="30" cy="30" r="27"/><path d="M30 12 V46 M20 46 H40 M16 20 H44 M16 20 L10 32 H22 Z M44 20 L38 32 H50 Z"/></svg>';
        head.append(emblem, el('div', 'doc-state', T.doc.state), el('div', 'doc-court', T.doc.court));
        doc.appendChild(head);
        doc.appendChild(el('h3', 'doc-title', T.doc.titleBy[kind] || T.verdictTitle));
        doc.appendChild(el('div', 'doc-sub', T.doc.onCase + ' «' + st.c.title + '»'));
        var meta = el('div', 'doc-meta'), now = new Date();
        var date = ('0' + now.getDate()).slice(-2) + '.' + ('0' + (now.getMonth() + 1)).slice(-2) + '.' + now.getFullYear();
        meta.append(el('span', null, T.doc.city), el('span', null, date));
        doc.appendChild(meta);
        var seed = 0;
        (st.c.id + date + st.scores.join('')).split('').forEach(function (ch) { seed = (seed * 31 + ch.charCodeAt(0)) % 99991; });
        doc.appendChild(el('div', 'doc-number', T.doc.number + ' LA-' + now.getFullYear() + '-' + String(10000 + seed % 89999)));
        doc.appendChild(el('p', 'doc-body', d.verdict));
        var resolve = el('div', 'doc-resolve');
        resolve.append(el('b', null, T.doc.resolvedBy[kind] || T.doc.resolvedBy.civil), el('span', null, outcomeText(d.outcome) + '.'));
        doc.appendChild(resolve);
        var sign = el('div', 'doc-sign');
        var who = el('div', 'doc-signer');
        who.append(el('span', null, T.doc.judge), el('i', 'doc-line'), el('b', null, T.doc.judgeName));
        sign.append(who, seal());
        doc.appendChild(sign);
        doc.appendChild(el('div', 'doc-note', T.doc.note));
        box.appendChild(doc);

        // 3. Разбор: радар по критериям и статьи по делу.
        var grid = el('div', 'vx-grid');
        var radarCard = el('div', 'card');
        radarCard.appendChild(el('h4', null, T.breakdown));
        var wrap = el('div', 'radar-wrap');
        wrap.innerHTML = radar(d.breakdown || {});
        var labels = ['top', 'right', 'bottom', 'left'];
        CRIT.forEach(function (key, i) {
            var lab = el('span', 'rl ' + labels[i]);
            var v = (d.breakdown || {})[key] || 0;
            lab.append(el('b', null, String(v)), document.createTextNode(T.criteria[key]));
            lab.style.setProperty('--lc', colorFor(v));
            wrap.appendChild(lab);
        });
        radarCard.appendChild(wrap);
        grid.appendChild(radarCard);

        if (d.articles && d.articles.length) {
            var arts = el('div', 'card');
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
            grid.appendChild(arts);
        }
        box.appendChild(grid);

        // 4. Что получилось, над чем работать, что изучить.
        var cols = el('div', 'v-cols');
        if (d.strengths && d.strengths.length) cols.appendChild(list('✅ ' + T.strengths, d.strengths, 'good'));
        if (d.mistakes && d.mistakes.length) cols.appendChild(list('⚠️ ' + T.mistakes, d.mistakes, 'warn'));
        if (d.study && d.study.length) cols.appendChild(list('📚 ' + T.study, d.study, 'info'));
        if (cols.children.length) box.appendChild(cols);

        var actions = el('div', 'v-actions');
        var again = el('button', 'btn', '↻ ' + T.again);
        again.type = 'button';
        again.addEventListener('click', function () { if (listening) listening.stop(); startCase(st.c); });
        var other = el('button', 'btn ghost', '← ' + T.other);
        other.type = 'button';
        other.addEventListener('click', function () { if (listening) listening.stop(); show('screenCases'); });
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

    var scene = null;

    function init() {
        initTheme();
        window.CourtVoice.configure({ mode: 'browser', api: cfg.api, lang: cfg.lang });
        scene = window.CourtScene.create($('scene'), { voice: window.CourtVoice, labels: { soundOn: T.soundOn, soundOff: T.soundOff } });
        var skip = el('button', 'scene-btn', '⏭');
        skip.type = 'button';
        skip.title = T.skip;
        skip.setAttribute('aria-label', T.skip);
        skip.addEventListener('click', function () { scene.skipAll(); });
        $('scene').querySelector('.scene-tools').prepend(skip);
        $('send').addEventListener('click', send);
        $('finish').addEventListener('click', finish);
        $('input').addEventListener('keydown', function (e) { if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) { e.preventDefault(); send(); } });
        $('hintline').textContent = T.hint;
        $('backToCases').addEventListener('click', function () { show('screenCases'); });
        loadCases();
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
