<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.register_heading') }} | {{ __('ui.app_name') }}</title>
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
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
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
        .card {
            width: min(640px, 100%);
            background: var(--card);
            border-radius: 28px;
            padding: 32px;
            box-shadow: 0 30px 80px rgba(2, 6, 23, 0.42);
            position: relative;
        }
        .topbar {
            position: absolute;
            top: 24px;
            right: 24px;
        }
        h1 {
            margin: 0 0 10px;
            color: var(--text);
            font-size: 32px;
        }
        .sub {
            color: var(--muted);
            margin-bottom: 28px;
        }
        .grid {
            display: grid;
            gap: 18px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-size: 14px;
            font-weight: 600;
        }
        input {
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
        .btn {
            width: 100%;
            margin-top: 6px;
            border: 0;
            border-radius: 14px;
            padding: 15px 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }
        .errors {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #fef2f2;
            color: #991b1b;
            font-size: 14px;
        }
        .foot {
            margin-top: 18px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }
        .foot a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }
        @media (max-width: 640px) {
            .card { padding: 24px; }
            .topbar { position: static; margin-bottom: 18px; }
            h1 { font-size: 28px; }
        }
    </style>
</head>
<body>
<div class="card">
    <div class="topbar">
    </div>

    <h1>{{ __('ui.register_heading') }}</h1>
    <div class="sub">{{ __('ui.register_subtitle') }}</div>

    @if ($errors->any())
        <div class="errors">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" class="grid">
        @csrf

        <div>
            <label for="name">{{ __('ui.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
        </div>

        <div>
            <label for="email">{{ __('ui.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
        </div>

        <div>
            <label for="password">{{ __('ui.password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
        </div>

        <div>
            <label for="password_confirmation">{{ __('ui.confirm_password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <button class="btn" type="submit">{{ __('ui.create_account') }}</button>
    </form>

    <div class="foot">
        {{ __('ui.already_registered') }}
        <a href="{{ route('login') }}">{{ __('ui.sign_in') }}</a>
    </div>
</div>
</body>
</html>
