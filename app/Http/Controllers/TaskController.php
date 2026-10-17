<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function index()
    {
        return view('admin.pages.tasks.index');
    }

    public function create()
    {
        return view('admin.pages.tasks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|min:3',
            'description' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:3',
            'deadline' => 'nullable|date',
        ]);

        $data['status'] = Task::STATUS_PENDING;
        $data['user_id'] = $request->user()?->id;

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Задача успешно создана');
    }

    public function show(Task $task)
    {
        return view('admin.pages.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        return view('admin.pages.tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|min:3',
            'priority' => 'required|integer|min:1|max:3',
            'deadline' => 'nullable|date',
        ]);

        $status = $request->input('status', Task::STATUS_PENDING);

        if (!in_array($status, [Task::STATUS_PENDING, Task::STATUS_COMPLETED, Task::STATUS_FAILED], true)) {
            $status = Task::STATUS_PENDING;
        }

        $data['status'] = $status;
        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'Задача обновлена');
    }

    public function calc()
    {
        return view('admin.pages.tasks.calc');
    }

    public function report(Request $request)
    {
        $validated = $request->validate([
            'contract' => 'required|file|mimes:pdf,doc,docx,txt,md,html,json|max:15360',
        ]);

        /** @var UploadedFile $file */
        $file = $validated['contract'];
        $storedPath = $file->store('contracts', 'local');

        $extension = Str::lower($file->getClientOriginalExtension());
        $textExtensions = ['txt', 'md', 'html', 'json'];
        $hasTextPreview = in_array($extension, $textExtensions, true);
        $text = $hasTextPreview ? $this->extractText($file, $extension) : '';

        $report = $this->buildDocumentReport($file, $storedPath, $text, $hasTextPreview);

        return view('admin.pages.tasks.index', compact('report'));
    }

    public function markCompleted(Task $task)
    {
        $task->update(['status' => Task::STATUS_COMPLETED]);

        return redirect()->route('tasks.index')->with('success', 'Задача отмечена как выполненная');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Задача удалена');
    }

    private function extractText(UploadedFile $file, string $extension): string
    {
        $rawText = file_get_contents($file->getRealPath()) ?: '';

        if ($extension === 'html') {
            $rawText = strip_tags($rawText);
        }

        if ($extension === 'json') {
            $decoded = json_decode($rawText, true);
            if (is_array($decoded)) {
                $rawText = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: $rawText;
            }
        }

        $text = preg_replace('/\s+/u', ' ', $rawText);

        return trim((string) $text);
    }

    private function buildDocumentReport(UploadedFile $file, string $storedPath, string $text, bool $hasTextPreview): array
    {
        $normalizedText = Str::lower($text);
        $contractType = $this->detectContractType($normalizedText, $file->getClientOriginalName());
        $risks = $this->findRisks($normalizedText, $contractType);
        $riskScore = $this->calculateRiskScore($risks, $text, $hasTextPreview);
        $words = $text !== '' ? preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) : [];
        $wordCount = is_array($words) ? count($words) : 0;
        $charCount = $text !== '' ? mb_strlen($text) : max(500, (int) round($file->getSize() * 1.4));
        $criticalCount = count(array_filter($risks, fn (array $risk) => $risk['severity'] === 'high'));

        return $this->localizeReport([
            'file' => [
                'original_name' => $file->getClientOriginalName(),
                'extension' => Str::upper($file->getClientOriginalExtension()),
                'mime_type' => $file->getClientMimeType(),
                'size_human' => $this->formatBytes($file->getSize()),
                'stored_path' => $storedPath,
            ],
            'summary' => $hasTextPreview
                ? sprintf(
                    __('Документ классифицирован как "%s". Обнаружено %d зон для ручной проверки. Основной фокус: ответственность сторон, финансовые условия и порядок расторжения.'),
                    __($contractType),
                    count($risks)
                )
                : sprintf(
                    __('Документ классифицирован как "%s". Текст не был извлечен на сервере для этого формата, поэтому показан ориентировочный отчет по метаданным файла и типу договора.'),
                    __($contractType)
                ),
            'risk_score' => $riskScore,
            'risk_label' => $this->resolveRiskLabel($riskScore),
            'contract_type' => $contractType,
            'char_count' => $charCount,
            'word_count' => $wordCount > 0 ? $wordCount : max(80, (int) round($file->getSize() / 6)),
            'issues_count' => count($risks),
            'critical_count' => $criticalCount,
            'analysis_status' => $hasTextPreview ? 'Текст распознан и проанализирован' : 'Сформирован базовый отчет по файлу',
            'analysis_note' => $hasTextPreview
                ? 'Отчет сформирован на сервере по содержимому файла.'
                : 'Для PDF, DOC и DOCX нужен отдельный парсер или OCR, чтобы извлечь текст и построить точный отчет.',
            'risks' => $risks,
            'recommendations' => $this->buildRecommendations($contractType, $risks),
            'facts' => [
                ['label' => 'Название файла', 'value' => $file->getClientOriginalName()],
                ['label' => 'Тип договора', 'value' => $contractType],
                ['label' => 'Формат', 'value' => Str::upper($file->getClientOriginalExtension()) ?: 'FILE'],
                ['label' => 'Размер', 'value' => $this->formatBytes($file->getSize())],
                ['label' => 'Режим анализа', 'value' => $hasTextPreview ? 'По тексту документа' : 'По метаданным файла'],
                ['label' => 'Критичных пунктов', 'value' => (string) $criticalCount],
            ],
            'has_text_preview' => $hasTextPreview,
            'preview_text' => $hasTextPreview && $text !== ''
                ? Str::limit($text, 2200, '...')
                : 'Для этого формата сервер пока не извлекает содержимое. Следующий шаг - подключить backend-парсер PDF/DOCX или OCR и строить отчет уже по полному тексту.',
        ]);
    }

    /**
     * Отчёт строится по русским шаблонам (по ним же работают проверки типа договора и тяжести),
     * а на таджикском интерфейсе выводимые тексты переводятся по словарю lang/tg.json.
     */
    private function localizeReport(array $report): array
    {
        if (app()->getLocale() !== 'tg') {
            return $report;
        }

        $tr = fn (?string $text) => $text === null || $text === '' ? $text : __($text);
        $risk = fn (array $item) => array_merge($item, ['label' => $tr($item['label'] ?? '')]);

        // risk_label остаётся русским ключом: по нему вьюха выбирает цвет и сама переводит подпись
        foreach (['contract_type', 'analysis_status', 'analysis_note'] as $key) {
            $report[$key] = $tr($report[$key]);
        }
        $report['risks'] = array_map($risk, $report['risks']);
        $report['recommendations'] = array_map($tr, $report['recommendations']);
        $report['facts'] = array_map(
            fn (array $fact) => ['label' => $tr($fact['label']), 'value' => $tr($fact['value'])],
            $report['facts']
        );
        if (! $report['has_text_preview']) {
            $report['preview_text'] = $tr($report['preview_text']);
        }

        return $report;
    }

    private function detectContractType(string $text, string $fileName): string
    {
        $source = Str::lower($text.' '.$fileName);

        return match (true) {
            Str::contains($source, ['поставк', 'таъминот']) => 'Договор поставки',
            Str::contains($source, ['подряд', 'пудрат']) => 'Договор подряда',
            Str::contains($source, ['трудов', 'меҳнат']) => 'Трудовой договор',
            Str::contains($source, ['nda', 'конфиденциаль', 'махфият']) => 'NDA / Конфиденциальность',
            Str::contains($source, ['услуг', 'хизматрасон']) => 'Договор услуг',
            Str::contains($source, ['лиценз', 'иҷозатнома']) => 'Лицензионный договор',
            default => 'Общий гражданско-правовой договор',
        };
    }

    private function findRisks(string $text, string $contractType): array
    {
        $riskMap = [
            ['term' => 'штраф', 'alt' => ['ҷарима', 'штраф'], 'label' => 'Штрафные санкции требуют проверки на соразмерность и порядок применения.', 'severity' => 'high'],
            ['term' => 'неустойка', 'alt' => ['неустойка', 'ноустойка', 'пеня'], 'label' => 'Проверьте расчет неустойки и верхний предел ответственности.', 'severity' => 'medium'],
            ['term' => 'одностороннем порядке', 'alt' => ['яктарафа'], 'label' => 'Есть условие об одностороннем изменении или прекращении обязательств.', 'severity' => 'high'],
            ['term' => 'конфиденциаль', 'alt' => ['махфият', 'конфиденсиал'], 'label' => 'Нужна ручная проверка блока конфиденциальности и исключений.', 'severity' => 'medium'],
            ['term' => 'персональн', 'alt' => ['маълумоти шахсӣ', 'маълумоти шахсии'], 'label' => 'Обнаружена обработка персональных данных, стоит добавить комплаенс-положения.', 'severity' => 'medium'],
            ['term' => 'форс-мажор', 'alt' => ['форс-мажор', 'қувваи қоҳира'], 'label' => 'Уточните порядок уведомления и подтверждения форс-мажора.', 'severity' => 'low'],
            ['term' => 'подсуд', 'alt' => ['тобеияти суд', 'суди маҳалл'], 'label' => 'Проверьте подсудность и применимое право.', 'severity' => 'medium'],
            ['term' => 'ответственност', 'alt' => ['ҷавобгарӣ', 'масъулият'], 'label' => 'Нужно отдельно проверить ограничения ответственности сторон.', 'severity' => 'high'],
            ['term' => 'расторжен', 'alt' => ['бекор'], 'label' => 'Условия расторжения должны быть симметричными и определенными.', 'severity' => 'medium'],
            ['term' => 'оплат', 'alt' => ['пардохт'], 'label' => 'Финансовые условия требуют проверки сроков, актов и оснований оплаты.', 'severity' => 'low'],
        ];

        if ($text === '') {
            return $this->fallbackRisks($contractType);
        }

        $found = [];
        foreach ($riskMap as $risk) {
            if (Str::contains($text, array_merge([$risk['term']], $risk['alt'] ?? []))) {
                $found[] = $risk;
            }
        }

        if ($found === []) {
            $found[] = [
                'term' => 'общие условия',
                'label' => 'Явных триггеров мало, но предмет договора и обязательства сторон нужно проверить вручную.',
                'severity' => 'low',
            ];
        }

        return array_slice($found, 0, 5);
    }

    private function fallbackRisks(string $contractType): array
    {
        return match ($contractType) {
            'Договор услуг' => [
                ['term' => 'services-scope', 'label' => 'Проверьте объем услуг и критерии приемки результата.', 'severity' => 'medium'],
                ['term' => 'services-payment', 'label' => 'Уточните сроки оплаты и санкции за просрочку.', 'severity' => 'medium'],
                ['term' => 'services-liability', 'label' => 'Проверьте предел ответственности исполнителя.', 'severity' => 'high'],
            ],
            'Договор поставки' => [
                ['term' => 'supply-transfer', 'label' => 'Проверьте переход риска и порядок поставки.', 'severity' => 'high'],
                ['term' => 'supply-defects', 'label' => 'Уточните приемку и порядок фиксации дефектов.', 'severity' => 'medium'],
                ['term' => 'supply-termination', 'label' => 'Проверьте штрафы и односторонний отказ.', 'severity' => 'medium'],
            ],
            default => [
                ['term' => 'subject', 'label' => 'Нужна проверка предмета договора и состава обязательств.', 'severity' => 'medium'],
                ['term' => 'termination', 'label' => 'Проверьте порядок расторжения и урегулирования споров.', 'severity' => 'medium'],
                ['term' => 'liability', 'label' => 'Оцените блок ответственности сторон.', 'severity' => 'high'],
            ],
        };
    }

    private function calculateRiskScore(array $risks, string $text, bool $hasTextPreview): int
    {
        $score = $hasTextPreview ? 22 : 38;

        foreach ($risks as $risk) {
            $score += match ($risk['severity']) {
                'high' => 18,
                'medium' => 11,
                default => 6,
            };
        }

        if (mb_strlen($text) > 4000) {
            $score += 5;
        }

        return min($score, 96);
    }

    private function resolveRiskLabel(int $score): string
    {
        return match (true) {
            $score >= 65 => 'Высокий',
            $score >= 40 => 'Средний',
            default => 'Низкий',
        };
    }

    private function buildRecommendations(string $contractType, array $risks): array
    {
        $recommendations = [
            'Добавьте точное описание предмета договора и критериев исполнения.',
            'Проверьте раздел ответственности: размер санкций, лимиты убытков и порядок взыскания.',
            'Уточните процедуру расторжения, уведомления и порядок разрешения споров.',
        ];

        if ($contractType === 'NDA / Конфиденциальность') {
            array_unshift($recommendations, 'Пропишите срок действия режима конфиденциальности и перечень исключений.');
        }

        foreach ($risks as $risk) {
            if (Str::contains($risk['term'], 'персональн')) {
                array_unshift($recommendations, 'Добавьте блок по персональным данным: цели, основания обработки и обязанности сторон.');
                break;
            }
        }

        return array_values(array_slice(array_unique($recommendations), 0, 4));
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 Б';
        }

        $units = ['Б', 'КБ', 'МБ', 'ГБ'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $power);
        $precision = $value >= 10 || $power === 0 ? 0 : 1;

        return number_format($value, $precision, '.', ' ').' '.$units[$power];
    }
}
