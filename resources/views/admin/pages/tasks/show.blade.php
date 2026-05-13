<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Конструктор Договоров PRO</title>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        /* Стили остались прежними, только добавили индикатор "PRO" */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #0b1120; color: #e2e8f0; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #1e1b4b, #312e81, #4338ca); padding: 32px 20px; text-align: center; }
        .header h1 { color: white; font-size: 26px; margin-bottom: 6px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .badge { background: #10b981; color: white; font-size: 12px; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .header p { color: #a5b4fc; font-size: 14px; }
        .progress { max-width: 650px; margin: 20px auto 0; padding: 0 20px; display: flex; align-items: center; justify-content: center; gap: 6px; }
        .p-dot { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #1e293b; border: 2px solid #334155; font-size: 13px; font-weight: 700; color: #475569; transition: 0.3s; }
        .p-dot.active { border-color: #6366f1; background: #6366f1; color: white; }
        .p-dot.done { border-color: #10b981; background: #10b981; color: white; }
        .p-line { width: 40px; height: 2px; background: #334155; transition: 0.3s; }
        .p-line.done { background: #10b981; }
        .main { max-width: 650px; margin: 24px auto; padding: 0 20px; }
        .page { display: none; background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; animation: fadeUp 0.35s ease; }
        .page.active { display: block; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .page h2 { font-size: 20px; color: white; margin-bottom: 4px; }
        .page .sub { color: #94a3b8; font-size: 13px; margin-bottom: 24px; line-height: 1.5; }
        .field { margin-bottom: 18px; }
        .field label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 6px; font-weight: 500; }
        .field input, .field textarea { width: 100%; padding: 14px 16px; background: #0f172a; border: 1px solid #334155; border-radius: 10px; color: white; font-size: 15px; font-family: inherit; transition: 0.2s; }
        .field input:focus, .field textarea:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
        .field textarea { min-height: 120px; resize: vertical; line-height: 1.6; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .nav { display: flex; justify-content: space-between; margin-top: 28px; gap: 12px; }
        .btn { padding: 14px 28px; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.2s; font-family: inherit; display: flex; align-items: center; gap: 8px; }
        .btn-back { background: #334155; color: #e2e8f0; }
        .btn-back:hover { background: #475569; }
        .btn-next { background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; margin-left: auto; }
        .btn-next:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); }
        .btn-gen { background: linear-gradient(135deg, #10b981, #059669); color: white; width: 100%; justify-content: center; padding: 18px; font-size: 16px; margin-top: 8px; }
        .btn-gen:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,185,129,0.4); }
        .btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none !important; box-shadow: none !important; }
        .loader { display: none; text-align: center; padding: 60px 20px; }
        .loader.active { display: block; animation: fadeUp 0.3s ease; }
        .spinner { width: 50px; height: 50px; border: 4px solid #334155; border-top-color: #6366f1; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 20px; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .result { display: none; margin-top: 24px; animation: fadeUp 0.4s ease; }
        .result.active { display: block; }
        .result-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px; }
        .result-top h2 { font-size: 18px; color: white; }
        .result-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-sm { padding: 8px 14px; background: #334155; border: 1px solid #475569; border-radius: 6px; color: #e2e8f0; font-size: 12px; cursor: pointer; transition: 0.2s; font-family: inherit; }
        .btn-sm:hover { background: #475569; }
        .contract-box { background: #ffffff; color: #1e293b; border-radius: 12px; padding: 40px; font-family: 'Times New Roman', Georgia, serif; font-size: 15px; line-height: 1.75; max-height: 700px; overflow-y: auto; }
        .contract-box h1 { text-align: center; font-size: 19px; margin-bottom: 6px; color: #111; }
        .contract-box h2 { text-align: center; font-size: 15px; font-weight: normal; color: #333; margin-bottom: 14px; }
        .contract-box h3 { font-size: 15px; margin: 18px 0 8px; color: #111; }
        .contract-box p { text-indent: 40px; text-align: justify; margin-bottom: 6px; color: #222; }
        .contract-box table { width: 100%; margin-top: 24px; border-collapse: collapse; }
        .contract-box td, .contract-box th { border: 1px solid #cbd5e1; padding: 10px; text-align: left; vertical-align: top; font-size: 14px; }
        .summary { background: #0f172a; border-radius: 10px; padding: 20px; margin-bottom: 20px; font-size: 14px; line-height: 2; color: #cbd5e1; }
        .ai-analysis { background: #0f172a; border: 1px solid #4338ca; border-radius: 10px; padding: 16px; margin-bottom: 20px; }
        .ai-analysis h4 { color: #818cf8; font-size: 14px; margin-bottom: 10px; }
        .ai-analysis .detected { color: #cbd5e1; font-size: 13px; line-height: 1.7; }
        .ai-analysis .detected strong { color: #e2e8f0; }
        .setup-box { background: #1e293b; border: 1px solid #475569; border-radius: 10px; padding: 14px; margin-bottom: 18px; font-size: 13px; color: #94a3b8; line-height: 1.5; }
        .setup-box code { background: #0f172a; padding: 2px 6px; border-radius: 4px; font-family: monospace; color: #a5b4fc; }
        .setup-box .toggle-row { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
        .setup-box input[type="checkbox"] { width: auto; margin: 0; }
        .toast { position: fixed; bottom: 20px; right: 20px; background: #10b981; color: white; padding: 12px 20px; border-radius: 8px; font-size: 14px; transform: translateY(80px); opacity: 0; transition: 0.3s; z-index: 999; }
        .toast.show { transform: translateY(0); opacity: 1; }
        @media print { body * { visibility: hidden; } .contract-box, .contract-box * { visibility: visible; } .contract-box { position: absolute; left: 0; top: 0; width: 100%; padding: 40px; border-radius: 0; max-height: none; } .header, .progress, .page, .loader, .result-top, .toast, .setup-box { display: none !important; } }
        @media (max-width: 600px) { .row { grid-template-columns: 1fr; } .contract-box { padding: 24px; } }
    </style>
</head>
<body>

<div class="header">
    <h1>📄 AI Конструктор Договоров <span class="badge">PRO</span></h1>
    <p>Улучшенный промпт → Юридически точный документ</p>
</div>

<div class="progress" id="progressBar"></div>

<div class="main">

    <!-- ═══════ ШАГ 1: ОПИСАНИЕ ═══════ -->
    <div class="page active" data-step="1">
        <h2>✍️ Вводные данные</h2>
        <p class="sub">Опишите суть сделки. AI сам вытащит реквизиты и условия.</p>

        <div class="field">
            <label>Суть договора <span style="color:#f87171">*</span></label>
            <textarea id="f_desc" placeholder="Например: ООО «СтройМастер» заказывает у ИП Смирнова ремонт офиса 50 кв.м. Срок 1 месяц, цена 300к. Оплата 50% до начала."></textarea>
        </div>

        <div class="setup-box">
            <strong>⚙️ Подключение Ollama</strong><br>
            Запустите: <code>OLLAMA_ORIGINS="*" ollama serve</code>
            <div class="toggle-row">
                <input type="checkbox" id="useOllama" checked>
                <label for="useOllama" style="color:white;font-weight:500;cursor:pointer">Использовать Ollama Mistral</label>
            </div>
        </div>

        <div class="nav">
            <div></div>
            <button class="btn btn-next" onclick="analyzeAndNext()">⚡ Далее →</button>
        </div>
    </div>

    <!-- ═══════ ШАГ 2: ПРОВЕРКА ДАННЫХ ═══════ -->
    <div class="page" data-step="2">
        <h2>🔍 Данные извлечены</h2>
        <p class="sub">Проверьте корректность. Вы можете что-то изменить.</p>

        <div class="ai-analysis" id="aiAnalysis">
            <h4>🤖 Что понял AI:</h4>
            <div class="detected" id="detectedInfo"></div>
        </div>

        <div class="row">
            <div class="field">
                <label>Тип договора</label>
                <input type="text" id="f_type" readonly style="opacity:0.7;">
            </div>
            <div class="field">
                <label>Сумма (₽)</label>
                <input type="number" id="f_amount" placeholder="300000">
            </div>
        </div>

        <div class="row">
            <div class="field">
                <label>Сторона 1 (Заказчик) <span style="color:#f87171">*</span></label>
                <input type="text" id="f_p1">
            </div>
            <div class="field">
                <label>Сторона 2 (Исполнитель) <span style="color:#f87171">*</span></label>
                <input type="text" id="f_p2">
            </div>
        </div>

        <div class="row">
            <div class="field">
                <label>Порядок оплаты</label>
                <input type="text" id="f_pay" placeholder="50% предоплата, 50% по акту">
            </div>
            <div class="field">
                <label>Срок исполнения</label>
                <input type="text" id="f_duration" placeholder="1 месяц">
            </div>
        </div>

        <div class="field">
            <label>Особые условия</label>
            <textarea id="f_extra" placeholder="Гарантия, штрафы, ответственность за просрочку..."></textarea>
        </div>

        <div class="field">
            <label>Дата договора</label>
            <input type="date" id="f_date">
        </div>

        <div class="nav">
            <button class="btn btn-back" onclick="goStep(1)">← Назад</button>
            <button class="btn btn-next" onclick="goStep(3)">⚡ Далее →</button>
        </div>
    </div>

    <!-- ═══════ ШАГ 3: ГЕНЕРАЦИЯ ═══════ -->
    <div class="page" data-step="3">
        <h2>✅ Финальная проверка</h2>
        <p class="sub">Подтвердите данные для генерации документа.</p>

        <div class="summary" id="summary"></div>

        <button class="btn btn-gen" id="genBtn" onclick="generate()">
            ✨ Сгенерировать договор
        </button>

        <div class="nav" style="margin-top:16px;">
            <button class="btn btn-back" onclick="goStep(2)">← Изменить данные</button>
        </div>
    </div>

    <!-- ЗАГРУЗКА -->
    <div class="loader" id="loader">
        <div class="spinner"></div>
        <p style="color:#94a3b8;">AI составляет юридический документ...</p>
    </div>

    <!-- РЕЗУЛЬТАТ -->
    <div class="result" id="result">
        <div class="result-top">
            <h2>📄 Договор готов</h2>
            <div class="result-actions">
                <button class="btn-sm" onclick="copyText()">📋 Копировать</button>
                <button class="btn-sm" onclick="window.print()">🖨️ Печать/PDF</button>
                <button class="btn-sm" onclick="downloadFile()">💾 Скачать .txt</button>
                <button class="btn-sm" onclick="startOver()">🔄 Заново</button>
            </div>
        </div>
        <div class="contract-box" id="contractOutput"></div>
    </div>

</div>

<div class="toast" id="toast"></div>

<script>
    let currentStep = 1;
    let contractText = '';
    const totalSteps = 3;
    const OLLAMA_URL = 'http://localhost:11434/api/chat';

    // ==========================================
    // 🚀 ПРОМПТЫ (УЛУЧШЕННЫЕ)
    // ==========================================

    // Промпт для анализа текста пользователя
    const ANALYSIS_PROMPT = `Ты — эксперт по анализу юридических текстов.
Твоя задача: извлечь из пользовательского описания данные для создания договора и вернуть СТРОГО JSON.

Схема JSON:
{
    "type": "тип договора (например: 'оказания услуг', 'подряда', 'аренды')",
    "party1": "Наименование Стороны 1 (Заказчика). Если не указано - 'Сторона 1'",
    "party2": "Наименование Стороны 2 (Исполнителя). Если не указано - 'Сторона 2'",
    "amount": "Сумма договора (только число). Если нет - null",
    "pay": "Порядок оплаты. Если нет - 'По соглашению сторон'",
    "duration": "Срок исполнения. Если нет - null",
    "extra": "Особые условия (штрафы, гарантии и т.д.). Если нет - null
}

Важно:
1. Не придумывай данные. Если чего-то нет в тексте — ставь null или дефолтное значение.
2. Не пиши никакой код вокруг JSON. Только чистый JSON.
3. Текст пользователя:`;

    // Промпт для генерации самого договора
    const CONTRACT_SYSTEM_PROMPT = `Ты — профессиональный корпоративный юрист в РФ со стажем 20 лет.
Ты составляешь юридически грамотные, четкие и лаконичные договоры, соответствующие Гражданскому Кодексу РФ (ГК РФ).

Твои правила:
1. Формат: Markdown.
2. Стиль: Строгий юридический ("Официально-деловой").
3. Структура:
   - Шапка (Город, Дата, Преамбула с полными названиями)
   - 1. Предмет договора (максимально детально)
   - 2. Стоимость и порядок расчетов
   - 3. Права и обязанности сторон (стандартный набор)
   - 4. Ответственность сторон (неустойка 0.1% в день за просрочку)
   - 5. Форс-мажор (ст. 401 ГК РФ)
   - 6. Порядок разрешения споров (Арбитраж)
   - 7. Реквизиты и подписи (Таблица)
4. Используй заполнители [__________] для пустых полей.
5. Не пиши вступлений типа "Вот ваш договор". Выдавай ТОЛЬКО текст договора.`;

    // Динамический промпт для генерации
    function getContractUserPrompt(data) {
        return `Составь Договор: ${data.type}.

ДАННЫЕ:
- Сторона 1: ${data.p1}
- Сторона 2: ${data.p2}
- Сумма: ${data.amount ? data.amount + ' руб.' : 'Не определена'}
- Оплата: ${data.pay}
- Срок: ${data.duration || 'До окончания работ'}
- Дата: ${data.date}
- Доп. условия: ${data.extra || 'Нет'}

Действуй строго по системным инструкциям.`;
    }


    // ==========================================
    // ЛОГИКА ИНТЕРФЕЙСА
    // ==========================================

    function buildProgress() {
        const bar = document.getElementById('progressBar');
        let html = '';
        for (let i = 1; i <= totalSteps; i++) {
            if (i > 1) html += `<div class="p-line" id="pline${i-1}"></div>`;
            html += `<div class="p-dot" id="pdot${i}">${i}</div>`;
        }
        bar.innerHTML = html;
    }
    buildProgress();
    document.getElementById('f_date').valueAsDate = new Date();

    function goStep(n) {
        if (n < 1 || n > totalSteps) return;
        currentStep = n;
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        document.querySelector(`.page[data-step="${n}"]`).classList.add('active');

        for (let i = 1; i <= totalSteps; i++) {
            const dot = document.getElementById('pdot' + i);
            dot.classList.remove('active', 'done');
            if (i === n) dot.classList.add('active');
            else if (i < n) dot.classList.add('done');
        }
        for (let i = 2; i <= totalSteps; i++) {
            const line = document.getElementById('pline' + (i-1));
            if (line) line.classList.toggle('done', i < n);
        }

        if (n === 3) buildSummary();
        document.getElementById('result').classList.remove('active');
    }

    async function ollamaChat(systemMsg, userMsg) {
        try {
            const res = await fetch(OLLAMA_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    model: 'mistral',
                    messages: [
                        { role: 'system', content: systemMsg },
                        { role: 'user', content: userMsg }
                    ],
                    stream: false,
                    options: { temperature: 0.1, num_predict: 2500 }
                })
            });

            if (!res.ok) {
                if (res.status === 0) throw new Error('CORS Error. Запустите: OLLAMA_ORIGINS="*" ollama serve');
                throw new Error(`Ошибка API: ${res.status}`);
            }

            const json = await res.json();
            return json.message?.content?.trim() || '';
        } catch (e) {
            throw e;
        }
    }

    async function analyzeAndNext() {
        const desc = document.getElementById('f_desc').value.trim();
        if (!desc) { toast('⚠️ Введите описание'); return; }

        const useOllama = document.getElementById('useOllama').checked;
        const loader = document.getElementById('loader');
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        loader.classList.add('active');

        try {
            let data;
            if (useOllama) {
                const response = await ollamaChat(ANALYSIS_PROMPT, desc);
                const clean = response.replace(/```(?:json)?\s*/g, '').replace(/```\s*/g, '').trim();
                data = JSON.parse(clean);
            } else {
                await new Promise(r => setTimeout(r, 500));
                data = mockAnalyze(desc);
            }

            fillFromAnalysis(data);

            const info = document.getElementById('detectedInfo');
            info.innerHTML = Object.entries(data)
                .filter(([k, v]) => v)
                .map(([k, v]) => `<strong>${getLabel(k)}:</strong> ${v}<br>`)
                .join('') || 'Нет данных';

            loader.classList.remove('active');
            goStep(2);
        } catch (e) {
            loader.classList.remove('active');
            document.querySelector('.page[data-step="1"]').classList.add('active');
            toast('⚠️ ' + e.message);
            console.error(e);
        }
    }

    function mockAnalyze(desc) {
        const lower = desc.toLowerCase();
        const d = { type: 'услуг', party1: 'Заказчик', party2: 'Исполнитель' };

        if (lower.includes('аренд')) d.type = 'аренды';
        else if (lower.includes('куп')) d.type = 'купли-продажи';
        else if (lower.includes('подряд|ремонт')) d.type = 'подряда';

        const ooo = desc.match(/ООО\s*"?([^"«»"]+)"?/i);
        if (ooo) d.party1 = `ООО "${ooo[1].trim()}"`;

        const sum = desc.match(/(\d[\d\s]*)\s*(?:тыс|₽|руб)/i);
        if (sum) {
            let n = sum[1].replace(/\s/g, '');
            d.amount = lower.includes('тыс') ? parseInt(n) * 1000 : n;
        }

        return d;
    }

    function fillFromAnalysis(a) {
        document.getElementById('f_type').value = a.type || 'оказания услуг';
        if (a.party1) document.getElementById('f_p1').value = a.party1;
        if (a.party2) document.getElementById('f_p2').value = a.party2;
        if (a.amount) document.getElementById('f_amount').value = a.amount;
        if (a.pay) document.getElementById('f_pay').value = a.pay;
        if (a.duration) document.getElementById('f_duration').value = a.duration;
        if (a.extra) document.getElementById('f_extra').value = a.extra;
    }

    function getLabel(key) {
        return { party1:'Заказчик', party2:'Исполнитель', amount:'Сумма',
            pay:'Оплата', duration:'Срок', extra:'Условия', type:'Тип' }[key] || key;
    }

    function buildSummary() {
        const g = id => document.getElementById(id).value.trim();
        document.getElementById('summary').innerHTML = `
            <div><strong>Тип:</strong> ${g('f_type')}</div>
            <div><strong>Заказчик:</strong> ${g('f_p1')}</div>
            <div><strong>Исполнитель:</strong> ${g('f_p2')}</div>
            <div><strong>Сумма:</strong> ${g('f_amount') || '—'} ₽</div>
            <div><strong>Оплата:</strong> ${g('f_pay')}</div>
            <div><strong>Срок:</strong> ${g('f_duration')}</div>
            ${g('f_extra') ? `<div><strong>Условия:</strong> ${g('f_extra')}</div>` : ''}
        `;
    }

    async function generate() {
        const btn = document.getElementById('genBtn');
        const loader = document.getElementById('loader');
        const result = document.getElementById('result');
        const useOllama = document.getElementById('useOllama').checked;

        const data = {
            type: document.getElementById('f_type').value,
            p1: document.getElementById('f_p1').value.trim(),
            p2: document.getElementById('f_p2').value.trim(),
            amount: document.getElementById('f_amount').value,
            pay: document.getElementById('f_pay').value.trim(),
            duration: document.getElementById('f_duration').value.trim(),
            date: document.getElementById('f_date').value,
            extra: document.getElementById('f_extra').value.trim()
        };

        if (!data.p1 || !data.p2) { toast('⚠️ Укажите стороны'); return; }

        btn.disabled = true;
        document.querySelector('.page[data-step="3"]').classList.remove('active');
        loader.classList.add('active');

        try {
            let text;
            if (useOllama) {
                text = await ollamaChat(CONTRACT_SYSTEM_PROMPT, getContractUserPrompt(data));
            } else {
                await new Promise(r => setTimeout(r, 800));
                text = buildContractMock(data);
            }

            contractText = text;
            loader.classList.remove('active');
            document.getElementById('contractOutput').innerHTML = marked.parse(text);
            result.classList.add('active');

            for (let i = 1; i <= totalSteps; i++) {
                document.getElementById('pdot' + i).classList.add('done');
                document.getElementById('pdot' + i).classList.remove('active');
            }
            toast('✅ Договор создан!');
        } catch (e) {
            loader.classList.remove('active');
            document.querySelector('.page[data-step="3"]').classList.add('active');
            toast('⚠️ ' + e.message);
        } finally {
            btn.disabled = false;
        }
    }

    function buildContractMock(d) {
        const dateStr = new Date(d.date || Date.now()).toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
        const amt = d.amount ? parseInt(d.amount).toLocaleString('ru-RU') : '[сумма]';

        return `# ДОГОВОР ${d.type.toUpperCase()}

**г. ____________** | **«${dateStr}»**

**${d.p1}**, именуемый «Заказчик», с одной стороны, и
**${d.p2}**, именуемый «Исполнитель», с другой стороны, заключили договор:

## 1. ПРЕДМЕТ
1.1. Исполнитель оказывает услуги по: ${d.type}.
1.2. Заказчик принимает и оплачивает результат.

## 2. СТОИМОСТЬ И ОПЛАТА
2.1. Цена договора: **${amt} ₽**.
2.2. Порядок: ${d.pay || 'По соглашению'}.

## 3. ОТВЕТСТВЕННОСТЬ
3.1. За просрочку начисляется пеня 0.1% от суммы.
3.2. Стороны освобождаются от ответственности при форс-мажоре.

## 4. РЕКВИЗИТЫ
| **Заказчик** | **Исполнитель** |
|--------------|-----------------|
| ${d.p1} | ${d.p2} |
| Подпись: ________ | Подпись: ________ |`;
    }

    function copyText() { navigator.clipboard.writeText(contractText); toast('📋 Скопировано'); }
    function downloadFile() {
        const blob = new Blob([contractText], { type: 'text/plain;charset=utf-8' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `Договор_${new Date().toISOString().slice(0,10)}.txt`;
        a.click();
    }
    function startOver() {
        contractText = '';
        document.getElementById('result').classList.remove('active');
        document.querySelectorAll('input, textarea').forEach(el => { if(el.type!=='checkbox') el.value=''; });
        document.getElementById('f_date').valueAsDate = new Date();
        document.getElementById('detectedInfo').innerHTML = '';
        goStep(1);
    }
    function toast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2500);
    }
</script>
</body>
</html>
