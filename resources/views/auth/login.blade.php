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
            --bg: #0f172a;
            --card: rgba(255, 255, 255, 0.96);
            --text: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.35), transparent 30%),
                radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.28), transparent 28%),
                linear-gradient(135deg, #020617, #0f172a 55%, #1e293b);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }
        .shell {
            width: min(1080px, 100%);
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(2, 6, 23, 0.45);
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(18px);
        }
        .hero {
            padding: 48px;
            background: linear-gradient(160deg, rgba(37, 99, 235, 0.92), rgba(30, 41, 59, 0.86));
        }
        .panel {
            padding: 48px;
            background: var(--card);
            color: var(--text);
            position: relative;
        }
        .topbar {
            position: absolute;
            top: 24px;
            right: 24px;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 22px;
            margin-bottom: 28px;
        }
        .badge {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.18);
        }
        h1, h2 { margin: 0 0 12px; }
        .hero p, .sub { color: rgba(255, 255, 255, 0.86); line-height: 1.7; }
        .sub { color: var(--muted); }
        ul {
            list-style: none;
            padding: 0;
            margin: 28px 0 0;
            display: grid;
            gap: 14px;
        }
        li {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.92);
        }
        li::before {
            content: "•";
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.16);
        }
        form { margin-top: 28px; }
        .field { margin-bottom: 18px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
        }
        input[type="email"], input[type="password"], input[type="text"] {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px 16px;
            font: inherit;
        }
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }
        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 22px;
            font-size: 14px;
        }
        .row a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        .btn {
            width: 100%;
            border: 0;
            border-radius: 14px;
            padding: 15px 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }
        .socials {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }
        .socials a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            border-radius: 14px;
            padding: 13px 16px;
            font-weight: 600;
            border: 1px solid var(--line);
            color: var(--text);
        }
        .socials a:last-child {
            background: #111827;
            color: #fff;
            border-color: #111827;
        }
        .divider {
            text-align: center;
            color: var(--muted);
            font-size: 14px;
            margin: 18px 0;
        }
        .foot {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
            color: var(--muted);
        }
        .foot a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }
        .errors {
            margin: 0 0 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #fef2f2;
            color: #991b1b;
            font-size: 14px;
        }
        .status {
            margin: 0 0 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 14px;
        }
        @media (max-width: 900px) {
            .shell { grid-template-columns: 1fr; }
            .hero, .panel { padding: 32px 24px; }
            .topbar { position: static; margin-bottom: 20px; }
        }
    </style>
</head>
<body>
<div class="shell">
    <section class="hero">
        <div class="brand">
            <span class="badge">AI</span>
            <span>{{ __('ui.app_name') }}</span>
        </div>

        <h1>{{ __('ui.login_hero_title') }}</h1>
        <p>{{ __('ui.login_hero_text') }}</p>

        <ul>
            <li>{{ __('ui.login_feature_1') }}</li>
            <li>{{ __('ui.login_feature_2') }}</li>
            <li>{{ __('ui.login_feature_3') }}</li>
            <li>{{ __('ui.login_feature_4') }}</li>
        </ul>
    </section>

    <section class="panel">
        <div class="topbar">
        </div>

        <h2>{{ __('ui.login_heading') }}</h2>
        <div class="sub">{{ __('ui.login_subtitle') }}</div>

        <x-auth-session-status class="status" :status="session('status')" />

        @if ($errors->any())
            <div class="errors">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="field">
                <label for="email">{{ __('ui.email') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>

            <div class="field">
                <label for="password">{{ __('ui.password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
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

        <div class="divider">{{ __('ui.or_continue_with') }}</div>

        <div class="socials">
            <a href="/auth/google">{{ __('ui.continue_with_google') }}</a>
            <a href="/auth/github">{{ __('ui.continue_with_github') }}</a>
        </div>

        <div class="foot">
            {{ __('ui.no_account') }}
            <a href="{{ route('register') }}">{{ __('ui.sign_up') }}</a>
        </div>
    </section>
</div>
</body>
</html>
