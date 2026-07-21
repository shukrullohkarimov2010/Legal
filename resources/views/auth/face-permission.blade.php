<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.face_permission_title') }} | {{ __('ui.app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #08111f;
            --card: rgba(255, 255, 255, 0.96);
            --text: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #16a34a;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(56, 189, 248, 0.28), transparent 28%),
                radial-gradient(circle at bottom left, rgba(37, 99, 235, 0.3), transparent 32%),
                linear-gradient(140deg, #020617, #0b1220 50%, #1e293b);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }
        .permission-shell {
            width: min(620px, 100%);
            background: var(--card);
            border-radius: 28px;
            padding: 54px 42px;
            box-shadow: 0 30px 80px rgba(2, 6, 23, 0.42);
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--primary);
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 28px;
        }
        .brand-mark {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: #fff;
            background: var(--primary);
        }
        .permission-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(20, 184, 166, 0.08));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin: 20px 0 28px;
        }
        h1 {
            margin: 0 0 12px;
            color: var(--text);
            font-size: 32px;
            line-height: 1.2;
            font-weight: 800;
        }
        .lead {
            color: var(--muted);
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 32px;
        }
        .benefits {
            background: #f8fafc;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 32px;
        }
        .benefits h3 {
            color: var(--text);
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 14px;
        }
        .benefits-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .benefits-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 8px 0;
            color: #475569;
            font-size: 14px;
        }
        .benefits-list li:before {
            content: "✓";
            color: var(--success);
            font-weight: 700;
            flex: 0 0 auto;
            margin-top: 1px;
        }
        .actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }
        .btn {
            border: 0;
            border-radius: 12px;
            padding: 14px 18px;
            color: #fff;
            background: var(--primary);
            font: inherit;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: transform 0.18s ease, background 0.18s ease, opacity 0.18s ease;
        }
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        .btn:active {
            transform: translateY(0);
        }
        .btn-secondary {
            background: #e2e8f0;
            color: var(--text);
        }
        .btn-secondary:hover {
            background: #cbd5e1;
        }
        .privacy {
            color: #94a3b8;
            font-size: 13px;
            line-height: 1.6;
            text-align: center;
        }
        .privacy a {
            color: var(--primary);
            text-decoration: none;
        }
        .privacy a:hover {
            text-decoration: underline;
        }
        @media (max-width: 540px) {
            .permission-shell {
                padding: 32px 24px;
            }
            .actions {
                grid-template-columns: 1fr;
            }
            h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
<main class="permission-shell">
    <div class="brand">
        <span class="brand-mark">AI</span>
        <span>{{ __('ui.app_name') }}</span>
    </div>

    <div class="permission-icon">
        👤
    </div>

    <div>
        <h1>{{ __('ui.face_permission_heading') }}</h1>
        <p class="lead">{{ __('ui.face_permission_text') }}</p>
    </div>

    <div class="benefits">
        <h3>{{ __('ui.face_permission_benefits') }}:</h3>
        <ul class="benefits-list">
            <li>{{ __('ui.face_permission_benefit_1') }}</li>
            <li>{{ __('ui.face_permission_benefit_2') }}</li>
            <li>{{ __('ui.face_permission_benefit_3') }}</li>
        </ul>
    </div>

    <div class="actions">
        <form action="{{ route('face.permission.yes') }}" method="POST" style="display: grid;">
            @csrf
            <button type="submit" class="btn">{{ __('ui.face_permission_yes') }}</button>
        </form>
        <form action="{{ route('face.permission.no') }}" method="POST" style="display: grid;">
            @csrf
            <button type="submit" class="btn btn-secondary">{{ __('ui.face_permission_no') }}</button>
        </form>
    </div>

    <p class="privacy">
        {{ __('ui.face_permission_privacy') }}
    </p>
</main>
</body>
</html>
