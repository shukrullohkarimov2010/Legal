
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.login_title') }} | {{ __('ui.app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #090d16;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at 10% 20%, rgba(37, 99, 235, 0.15), transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(14, 165, 233, 0.1), transparent 40%),
                linear-gradient(135deg, #070a13, #0f172a 60%, #172554);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            -webkit-font-smoothing: antialiased;
        }

        .shell {
            width: min(1040px, 100%);
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6);
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .hero {
            padding: 64px;
            background: linear-gradient(145deg, rgba(29, 78, 216, 0.85), rgba(15, 23, 42, 0.95));
            display: flex;
            flex-direction: column;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .panel {
            padding: 64px;
            background: var(--card);
            color: var(--text);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .topbar {
            position: absolute;
            top: 32px;
            right: 32px;
            z-index: 50;
        }

        /* Стилизация переводчика */
        .lang-switcher {
            position: relative;
            display: inline-block;
        }
        .lang-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid var(--line);
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .lang-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        .lang-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            padding: 6px;
            min-width: 140px;
            display: none;
            flex-direction: column;
            gap: 2px;
        }
        .lang-switcher:hover .lang-dropdown,
        .lang-switcher:focus-within .lang-dropdown {
            display: flex;
        }
        .lang-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.15s ease;
        }
        .lang-item:hover {
            background: #f1f5f9;
        }
        .flag-icon { font-size: 15px; }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 20px;
            margin-bottom: 40px;
            letter-spacing: -0.01em;
        }
        .badge {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        h1 { font-size: 34px; font-weight: 700; margin-bottom: 16px; letter-spacing: -0.02em; line-height: 1.25; }
        h2 { font-size: 28px; font-weight: 700; margin-bottom: 8px; letter-spacing: -0.02em; }

        .hero p { color: rgba(255, 255, 255, 0.75); line-height: 1.6; font-size: 15px; }
        .sub { color: var(--muted); font-size: 15px; margin-bottom: 32px; }

        ul { list-style: none; margin-top: 40px; display: flex; flex-direction: column; gap: 16px; }
        li { display: flex; align-items: center; gap: 14px; color: rgba(255, 255, 255, 0.9); font-size: 14px; font-weight: 500; }

        .li-icon {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.12);
            flex-shrink: 0;
            color: #fff;
        }

        form { display: flex; flex-direction: column; gap: 18px; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        label { font-size: 13px; font-weight: 600; color: #334155; }

        input[type="email"], input[type="password"] {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 16px;
            font-family: inherit;
            font-size: 15px;
            color: var(--text);
            transition: all 0.2s ease;
            background-color: #f8fafc;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
        }
        input:focus {
            outline: none;
            border-color: var(--primary);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 500;
        }
        .row label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }
        .row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid var(--line);
            cursor: pointer;
        }
        .row a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .row a:hover { text-decoration: underline; }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 14px 18px;
            background: var(--primary);
            color: #fff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }
        .btn:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25); }
        .btn:active { transform: translateY(0); }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: var(--muted);
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 24px 0 16px;
        }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid var(--line); }
        .divider:not(:empty)::before { margin-right: 1em; }
        .divider:not(:empty)::after { margin-left: 1em; }

        /* Социальные кнопки */
        .socials { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .socials a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid var(--line);
            color: #1e293b;
            background: #ffffff;
            transition: all 0.15s ease;
        }
        .socials a:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }
        .socials a:active { transform: translateY(0); }

        .foot { margin-top: 32px; text-align: center; font-size: 14px; color: var(--muted); }
        .foot a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .foot a:hover { text-decoration: underline; }

        .errors {
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #fef2f2;
            border-left: 4px solid #f87171;
            color: #991b1b;
            font-size: 14px;
            font-weight: 500;
        }

        @media (max-width: 950px) {
            .shell { grid-template-columns: 1fr; }
            .hero { display: none; }
            .panel { padding: 48px 28px; }
            .topbar { top: 24px; right: 24px; }
        }
    </style>
</head>
<body>
<div class="shell">
    <section class="hero">
        <div class="brand">
            <span class="badge">AI</span>
            <span>LegalAI Pro</span>
        </div>

        <h1>{{ __('ui.login_hero_title') ?? 'Анализ договоров с помощью ИИ' }}</h1>
        <p>{{ __('ui.login_hero_text') ?? 'Проверяйте договоры, находите юридические риски и получайте рекомендации на понятном языке.' }}</p>

        <ul>
            <li>
                <span class="li-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </span>
                {{ __('ui.login_feature_1') ?? 'Мгновенный анализ документов' }}
            </li>
            <li>
                <span class="li-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </span>
                {{ __('ui.login_feature_2') ?? 'Оценка рисков по ключевым параметрам' }}
            </li>
            <li>
                <span class="li-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </span>
                {{ __('ui.login_feature_3') ?? 'Рекомендации по исправлению спорных пунктов' }}
            </li>
            <li>
                <span class="li-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </span>
                {{ __('ui.login_feature_4') ?? 'Безопасная работа с документами' }}
            </li>
        </ul>
    </section>

    <section class="panel">
        <div class="topbar">
            <div class="lang-switcher">
                <button class="lang-btn" type="button">
                    <span class="flag-icon">
                        @if(app()->getLocale() == 'ru') 🇷🇺 @elseif(app()->getLocale() == 'en') 🇬🇧 @else 🇹🇯 @endif
                    </span>
                    <span>{{ strtoupper(app()->getLocale()) }}</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="lang-dropdown">
                    <a href="/lang/ru" class="lang-item"><span class="flag-icon">🇷🇺</span> Русский</a>
                    <a href="/lang/en" class="lang-item"><span class="flag-icon">🇬🇧</span> English</a>
                    <a href="/lang/tg" class="lang-item"><span class="flag-icon">🇹🇯</span> Тоҷикӣ</a>
                </div>
            </div>
        </div>

        <h2>{{ __('ui.login_heading') ?? 'Вход в систему' }}</h2>
        <div class="sub{{ $errors->any() ? ' hidden' : '' }}">{{ __('ui.login_subtitle') ?? 'Введите свои данные для продолжения' }}</div>

        @if ($errors->any())
            <div class="errors">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="field">
                <label for="email">{{ __('ui.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
            </div>

            <div class="field">
                <label for="password">{{ __('ui.password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>

            <div class="row">
                <label>
                    <input type="checkbox" name="remember">
                    {{ __('ui.remember_me') }}
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">{{ __('ui.forgot_password') }}</a>
                @endif
            </div>

            <button class="btn" type="submit">{{ __('ui.sign_in') }}</button>
        </form>

        <div class="divider">{{ __('ui.or_continue_with') ?? 'Или войдите через' }}</div>

        <div class="socials">
            <a href="/auth/google">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                Google
            </a>

            <a href="/auth/github">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="#24292e">
                    <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                </svg>
                GitHub
            </a>
        </div>

        <div class="foot">
            {{ __('ui.no_account') }}
            <a href="{{ route('register') }}">{{ __('ui.sign_up') }}</a>
        </div>
    </section>
</div>
</body>
</html>


