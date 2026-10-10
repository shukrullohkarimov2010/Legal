<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.dashboard_title') }} — LegalAI Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tw-create.css') }}?v={{ @filemtime(public_path('css/tw-create.css')) }}">
    <script src="{{ asset('vendor/docx-8.2.2.umd.js') }}"></script>
    <script src="{{ asset('vendor/html2pdf.bundle.min.js') }}"></script>
    <script defer src="{{ asset('vendor/alpine-collapse.min.js') }}"></script>
    <script defer src="{{ asset('vendor/alpine.min.js') }}"></script>
    <style>
        :root {
            --canvas: #f4f4f5;
            --canvas-mesh-1: #e0e7ff;
            --canvas-mesh-2: #fae8ff;
            --canvas-mesh-3: #dbeafe;
            --surface: rgba(255, 255, 255, 0.72);
            --surface-solid: #ffffff;
            --surface-sunken: rgba(255, 255, 255, 0.55);
            --surface-hover: rgba(255, 255, 255, 0.9);

            --text: #0f172a;
            --text-muted: #475569;
            --text-faint: #94a3b8;

            --line: rgba(15, 23, 42, 0.08);
            --line-strong: rgba(15, 23, 42, 0.14);

            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --accent-tint: rgba(99, 102, 241, 0.12);
            --accent-line: rgba(99, 102, 241, 0.35);
            --on-accent: #ffffff;

            --gradient-primary: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
            --gradient-hero: linear-gradient(120deg, #6366f1 0%, #8b5cf6 40%, #ec4899 80%, #f59e0b 100%);
            --gradient-mesh: radial-gradient(at 20% 30%, var(--canvas-mesh-1) 0px, transparent 50%),
                             radial-gradient(at 80% 20%, var(--canvas-mesh-2) 0px, transparent 50%),
                             radial-gradient(at 60% 80%, var(--canvas-mesh-3) 0px, transparent 50%);

            --ok: #10b981;
            --ok-tint: rgba(16, 185, 129, 0.12);
            --warn: #f59e0b;
            --warn-tint: rgba(245, 158, 11, 0.12);
            --danger: #ef4444;

            --shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.08), 0 2px 4px -2px rgba(15, 23, 42, 0.05);
            --shadow-raised: 0 20px 40px -12px rgba(99, 102, 241, 0.25), 0 8px 16px -8px rgba(15, 23, 42, 0.12);
            --shadow-glow: 0 0 0 1px rgba(99, 102, 241, 0.1), 0 12px 32px -8px rgba(99, 102, 241, 0.35);

            --r-sm: 8px;
            --r-md: 14px;
            --r-lg: 20px;
        }

        [data-theme="dark"] {
            --canvas: #0a0a14;
            --canvas-mesh-1: #1e1b4b;
            --canvas-mesh-2: #4c1d95;
            --canvas-mesh-3: #831843;
            --surface: rgba(30, 27, 75, 0.4);
            --surface-solid: #1a1a2e;
            --surface-sunken: rgba(30, 27, 75, 0.25);
            --surface-hover: rgba(55, 48, 163, 0.3);

            --text: #f1f5f9;
            --text-muted: #cbd5e1;
            --text-faint: #64748b;

            --line: rgba(148, 163, 184, 0.12);
            --line-strong: rgba(148, 163, 184, 0.2);

            --accent: #818cf8;
            --accent-hover: #a78bfa;
            --accent-tint: rgba(129, 140, 248, 0.18);
            --accent-line: rgba(129, 140, 248, 0.5);
            --on-accent: #0a0a14;

            --gradient-primary: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #f472b6 100%);
            --gradient-hero: linear-gradient(120deg, #818cf8 0%, #c084fc 40%, #f472b6 80%, #fb923c 100%);
            --gradient-mesh: radial-gradient(at 20% 30%, var(--canvas-mesh-1) 0px, transparent 50%),
                             radial-gradient(at 80% 20%, var(--canvas-mesh-2) 0px, transparent 50%),
                             radial-gradient(at 60% 80%, var(--canvas-mesh-3) 0px, transparent 50%);

            --ok: #34d399;
            --ok-tint: rgba(52, 211, 153, 0.15);
            --warn: #fbbf24;
            --warn-tint: rgba(251, 191, 36, 0.15);
            --danger: #f87171;

            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -2px rgba(0, 0, 0, 0.3);
            --shadow-raised: 0 20px 40px -12px rgba(129, 140, 248, 0.25), 0 8px 16px -8px rgba(0, 0, 0, 0.4);
            --shadow-glow: 0 0 0 1px rgba(129, 140, 248, 0.2), 0 12px 32px -8px rgba(129, 140, 248, 0.3);
        }

        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }

        html, body { overflow-x: hidden; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: var(--canvas);
            color: var(--text);
            position: relative;
        }

        /* Живой mesh-gradient фон — три пятна плавно дрейфуют */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: var(--gradient-mesh);
            background-size: 100% 100%;
            z-index: -2;
            pointer-events: none;
            animation: meshMove 25s ease-in-out infinite;
        }
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.4'/%3E%3C/svg%3E");
            opacity: 0.25;
            z-index: -1;
            pointer-events: none;
            mix-blend-mode: overlay;
        }

        @keyframes meshMove {
            0%,100% { transform: translate(0,0) rotate(0deg); }
            33% { transform: translate(2%,-1%) rotate(1deg); }
            66% { transform: translate(-1%,2%) rotate(-1deg); }
        }

        main, nav { position: relative; }

        /* Glass с лёгким backdrop-blur и насыщенным бордером */
        .glass {
            background: var(--surface);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease, border-color 0.35s ease;
        }

        .glass-strong {
            background: var(--surface);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--line);
        }

        /* Hero-блок с анимированным градиентом */
        .hero-premium {
            position: relative;
            overflow: hidden;
            background: var(--surface);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--line);
            box-shadow: var(--shadow-raised);
            color: var(--text);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .hero-premium::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--gradient-hero);
            background-size: 300% 300%;
            opacity: 0.08;
            animation: gradientShift 10s ease infinite;
            pointer-events: none;
        }

        .hero-premium::after {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: var(--gradient-primary);
            background-size: 100% 200%;
            animation: gradientShift 4s ease infinite;
            border-radius: 2px;
        }

        .hero-premium:hover { transform: translateY(-2px); }

        .chip-premium {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.3rem 0.75rem;
            background: var(--surface-sunken);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--line);
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            white-space: nowrap;
            transition: all 0.25s ease;
        }

        .chip-premium[data-state="ok"] {
            color: var(--ok);
            background: var(--ok-tint);
            border-color: var(--ok);
            box-shadow: 0 0 12px -4px var(--ok);
        }
        .chip-premium[data-state="warn"] {
            color: var(--warn);
            background: var(--warn-tint);
            border-color: var(--warn);
            box-shadow: 0 0 12px -4px var(--warn);
        }

        .chip-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
            animation: glowPulse 2s ease-in-out infinite;
            box-shadow: 0 0 8px currentColor;
        }

        .metric-card {
            position: relative;
            padding: 0.875rem 1rem;
            background: var(--surface-sunken);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .metric-card:hover {
            transform: translateY(-3px);
            border-color: var(--accent-line);
            box-shadow: var(--shadow-raised);
        }

        .metric-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
            opacity: 0.3;
            pointer-events: none;
        }

        .metric-label {
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--text-faint);
            margin-bottom: 0.35rem;
        }

        .metric-value {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
            font-variant-numeric: tabular-nums;
        }

        /* Формы */
        .input-premium label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .input-premium input,
        .input-premium textarea {
            width: 100%;
            padding: 0.7rem 0.875rem 0.7rem 2.5rem;
            background: var(--surface-solid);
            border: 1.5px solid var(--line-strong);
            border-radius: var(--r-md);
            font-size: 0.9375rem;
            font-family: inherit;
            color: var(--text);
            transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
        }

        .input-premium input:hover,
        .input-premium textarea:hover {
            border-color: var(--accent-line);
            transform: translateY(-1px);
        }

        .input-premium input:focus,
        .input-premium textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-tint), 0 8px 20px -8px var(--accent);
            transform: translateY(-1px);
        }

        .input-premium .input-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-faint);
            pointer-events: none;
            transition: color 0.25s ease, transform 0.25s ease;
        }

        .input-premium { position: relative; }
        .input-premium:focus-within .input-icon {
            color: var(--accent);
            transform: translateY(-50%) scale(1.1);
        }
        .input-premium textarea ~ .input-icon { top: 1rem; transform: none; }
        .input-premium:focus-within textarea ~ .input-icon { transform: scale(1.1); }

        /* Карточки шаблонов — 3D lift + glow */
        .template-card-premium {
            position: relative;
            border-radius: var(--r-lg);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease, box-shadow 0.4s ease, background-color 0.3s ease;
            overflow: hidden;
        }

        .template-card-premium::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            padding: 1.5px;
            background: var(--gradient-primary);
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .template-card-premium:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-raised);
        }

        .template-card-premium:hover::before { opacity: 1; }

        .template-card-premium.active {
            background: var(--accent-tint);
            transform: translateY(-2px);
        }

        .template-card-premium.active::before { opacity: 1; }

        .template-card-premium.active::after {
            content: '';
            position: absolute;
            top: 0.875rem;
            right: 0.875rem;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--gradient-primary);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite, glowPulse 2s ease-in-out infinite;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E"), var(--gradient-primary);
            background-size: 12px, 200% 200%;
            background-repeat: no-repeat, no-repeat;
            background-position: center, 0% 50%;
        }

        .section-card {
            cursor: pointer;
            border-radius: var(--r-md);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .section-card:hover {
            border-color: var(--accent-line);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .section-card.active {
            background: var(--accent-tint);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-tint), 0 8px 20px -8px var(--accent);
        }

        .section-card input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            margin-top: 1px;
            border: 1.5px solid var(--line-strong);
            border-radius: 6px;
            cursor: pointer;
            flex-shrink: 0;
            transition: background-color 0.2s, border-color 0.2s, transform 0.2s;
        }

        .section-card input[type="checkbox"]:checked {
            background: var(--gradient-primary);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
            border-color: transparent;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E"), var(--gradient-primary);
            background-size: 12px, 200% 200%;
            background-repeat: no-repeat, no-repeat;
            background-position: center, 0% 50%;
        }

        /* Договор */
        .contract-paper {
            background: var(--surface-solid);
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            box-shadow: var(--shadow);
            transition: box-shadow 0.3s ease;
        }

        .contract-paper:hover { box-shadow: var(--shadow-raised); }

        .contract-preview {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 0.875rem;
            line-height: 1.75;
            white-space: pre-wrap;
            word-break: break-word;
            color: var(--text);
            max-width: 68ch;
        }

        .preview-toolbar {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--line);
        }

        /* Кнопки */
        .toolbar-btn {
            position: relative;
            padding: 0.5rem 0.875rem;
            border-radius: var(--r-sm);
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-muted);
            border: 1px solid transparent;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            white-space: nowrap;
            overflow: hidden;
        }

        .toolbar-btn:hover {
            background: var(--surface-hover);
            color: var(--text);
            transform: translateY(-1px);
        }

        .toolbar-btn:active { transform: scale(0.96); }

        /* Ripple эффект */
        .toolbar-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, rgba(255,255,255,0.5) 0%, transparent 70%);
            opacity: 0;
            transform: scale(0);
            transition: transform 0.5s, opacity 0.5s;
            pointer-events: none;
        }

        .toolbar-btn:active::after {
            opacity: 1;
            transform: scale(2);
            transition: 0s;
        }

        .btn-primary {
            background: var(--gradient-primary);
            background-size: 200% 200%;
            color: var(--on-accent);
            border-color: transparent;
            box-shadow: 0 4px 14px -4px var(--accent), 0 2px 6px -2px rgba(99, 102, 241, 0.4);
            animation: gradientShift 6s ease infinite;
        }

        .btn-primary:hover {
            background-position: 100% 50%;
            box-shadow: 0 8px 24px -4px var(--accent), 0 4px 10px -2px rgba(99, 102, 241, 0.5);
            color: var(--on-accent);
            transform: translateY(-2px);
        }

        .btn-outline {
            border-color: var(--line-strong);
            color: var(--text);
            background: var(--surface-sunken);
        }

        .btn-outline:hover {
            border-color: var(--accent-line);
            background: var(--surface-hover);
            color: var(--accent);
            transform: translateY(-1px);
        }

        .field-plain {
            padding: 0.55rem 0.875rem;
            font-size: 0.875rem;
            font-family: inherit;
            background: var(--surface-solid);
            border: 1.5px solid var(--line-strong);
            border-radius: var(--r-md);
            color: var(--text);
            transition: all 0.25s ease;
        }

        .field-plain:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-tint);
            transform: translateY(-1px);
        }

        .field-plain::placeholder { color: var(--text-faint); }

        .chat-bubble-user {
            background: var(--gradient-primary);
            background-size: 200% 200%;
            animation: gradientShift 6s ease infinite;
            color: var(--on-accent);
            box-shadow: 0 4px 14px -4px var(--accent);
        }

        .chat-bubble-ai {
            background: var(--surface-sunken);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--line);
            color: var(--text);
        }

        /* Stepper */
        .stepper-dot {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            background: var(--surface-sunken);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1.5px solid var(--line-strong);
            color: var(--text-faint);
            flex-shrink: 0;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .stepper-dot.is-done {
            background: var(--gradient-primary);
            background-size: 200% 200%;
            animation: gradientShift 4s ease infinite;
            border-color: transparent;
            color: var(--on-accent);
            box-shadow: 0 0 0 4px var(--accent-tint), 0 4px 12px -2px var(--accent);
        }

        .stepper-line {
            height: 2px;
            flex: 1;
            min-width: 1rem;
            background: var(--line-strong);
            border-radius: 2px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .stepper-line.active {
            background: var(--gradient-primary);
            background-size: 200% 200%;
            animation: gradientShift 4s ease infinite;
            box-shadow: 0 0 8px var(--accent);
        }

        .insight-success {
            background: var(--ok-tint);
            border: 1px solid transparent;
            border-left: 3px solid var(--ok);
            box-shadow: inset 0 0 0 0 var(--ok);
            transition: all 0.3s ease;
        }

        .insight-success:hover { box-shadow: 0 0 16px -4px var(--ok); transform: translateX(2px); }

        .insight-warning {
            background: var(--warn-tint);
            border: 1px solid transparent;
            border-left: 3px solid var(--warn);
            transition: all 0.3s ease;
        }

        .insight-warning:hover { box-shadow: 0 0 16px -4px var(--warn); transform: translateX(2px); }

        .theme-switch {
            position: relative;
            width: 48px;
            height: 26px;
            border-radius: 999px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border: 1px solid var(--line-strong);
            cursor: pointer;
            transition: background 0.3s ease;
        }

        [data-theme="dark"] .theme-switch {
            background: linear-gradient(135deg, #4338ca, #1e1b4b);
        }

        .theme-switch-thumb {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--surface-solid);
            border: 1px solid var(--line-strong);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        [data-theme="dark"] .theme-switch-thumb { transform: translateX(22px); }

        .toast-container {
            position: fixed;
            top: 4.5rem;
            right: 1.5rem;
            z-index: 100;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        }

        .toast {
            background: var(--surface-solid);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            padding: 0.875rem 1.125rem;
            box-shadow: var(--shadow-raised);
            display: flex;
            align-items: center;
            gap: 0.625rem;
            min-width: 280px;
            max-width: 380px;
            pointer-events: auto;
            animation: toastIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .toast.hiding { animation: toastOut 0.25s ease-in forwards; }
        .toast-success { border-left: 3px solid var(--ok); }
        .toast-error { border-left: 3px solid var(--danger); }
        .toast-info { border-left: 3px solid var(--accent); }

        .dropdown-premium {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            min-width: 220px;
            background: var(--surface-solid);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            box-shadow: var(--shadow-raised);
            overflow: hidden;
            z-index: 60;
            transform-origin: top right;
            animation: dropdownIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes dropdownIn {
            0% { opacity: 0; transform: scale(0.92) translateY(-8px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        .gradient-text {
            background: var(--gradient-primary);
            background-size: 200% 200%;
            animation: gradientShift 4s ease infinite;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
        }

        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid var(--line-strong);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: var(--line-strong);
            border-radius: 999px;
            border: 3px solid transparent;
            background-clip: padding-box;
            transition: background 0.2s;
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--accent-line); background-clip: padding-box; }

        button:focus-visible,
        a:focus-visible,
        input:focus-visible,
        textarea:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }

        /* Плавающие иконки у метрик и шагов */
        .float-icon { animation: float 4s ease-in-out infinite; display: inline-block; }

        .section-card input[type="checkbox"]:checked { animation: checkPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }

        @keyframes checkPop {
            0% { transform: scale(0.7) rotate(-15deg); }
            60% { transform: scale(1.15) rotate(5deg); }
            100% { transform: scale(1) rotate(0); }
        }

        .chat-collapse {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .chat-collapse.is-open { grid-template-rows: 1fr; }

        .chat-collapse-inner { overflow: hidden; min-height: 0; }

        #chatChevron { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }

        .chat-msg-in { animation: msgIn 0.35s cubic-bezier(0.16, 1, 0.3, 1); }

        @keyframes msgIn {
            0% { opacity: 0; transform: translateY(8px) scale(0.96); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .typing-dots {
            display: inline-flex;
            gap: 4px;
            align-items: center;
            height: 1em;
        }

        .typing-dots span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent);
            animation: typingDot 1.2s ease-in-out infinite;
            box-shadow: 0 0 8px var(--accent);
        }

        .typing-dots span:nth-child(2) { animation-delay: 0.15s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.3s; }

        @keyframes typingDot {
            0%, 60%, 100% { opacity: 0.3; transform: translateY(0) scale(0.8); }
            30% { opacity: 1; transform: translateY(-3px) scale(1.1); }
        }

        .contract-paper,
        #chatMessages { scroll-behavior: smooth; }

        /* Каскадное появление блоков */
        main > section, main > div, main > .grid > div { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; }
        main > .grid { animation-delay: 0.05s; }
        .xl\:col-span-7 > div:nth-child(1) { animation-delay: 0.1s; }
        .xl\:col-span-7 > div:nth-child(2) { animation-delay: 0.2s; }
        .xl\:col-span-7 > div:nth-child(3) { animation-delay: 0.3s; }
        .xl\:col-span-5 > div { animation-delay: 0.25s; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        @media (max-width: 1279px) {
            .preview-sticky { position: static !important; }
        }
    </style>
</head>
<body class="min-h-screen text-neutral-900 dark:text-neutral-100">
@php
    $currentUser = auth()->user();
    $userName = $currentUser?->name ?: __('builder.ui.party1');
    $userEmail = $currentUser?->email ?: 'email@example.com';
    $nameParts = preg_split('/\s+/u', trim($userName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $userInitials = collect($nameParts)->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    $userInitials = $userInitials !== '' ? $userInitials : mb_substr(__('builder.ui.party1'), 0, 1);

    $builderTranslations = __('builder');
@endphp

<div class="toast-container" id="toastContainer"></div>

<nav class="sticky top-0 z-40 glass-strong border-b border-[color:var(--line)]">
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 group">
                <div class="relative w-10 h-10 rounded-lg flex items-center justify-center transition-transform group-hover:scale-110 group-hover:rotate-6" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; box-shadow: 0 8px 20px -6px rgba(99,102,241,0.6);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-extrabold text-base leading-tight gradient-text">LegalAI Pro</div>
                    <div class="text-[10px] text-[color:var(--text-faint)] font-medium">Contract Builder</div>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('welcome') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-[color:var(--text-muted)] hover:text-neutral-900 dark:hover:text-white hover:bg-[color:var(--surface-hover)] transition">{{ __('ui.nav_home') }}</a>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-white relative overflow-hidden" style="background: var(--gradient-primary); background-size: 200% 200%; animation: gradientShift 4s ease infinite;">{{ __('ui.nav_features') }}</a>
                <x-language-switcher />

                <div class="w-px h-6 bg-black/10 dark:bg-white/10 mx-2"></div>

                <div class="theme-switch" id="themeToggle" role="button" tabindex="0" aria-label="{{ __('ui.theme_toggle') }}">
                    <div class="theme-switch-thumb">
                        <svg class="w-3.5 h-3.5 block dark:hidden" style="color: #f59e0b;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3V4m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg class="w-3.5 h-3.5 hidden dark:block" style="color: #818cf8;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </div>
                </div>

                <button id="notificationsButton" class="relative p-2 rounded-lg hover:bg-[color:var(--surface-hover)] transition">
                    <svg class="w-5 h-5 text-[color:var(--text-muted)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 rounded-full text-[9px] font-bold flex items-center justify-center text-white ring-2 ring-white dark:ring-neutral-900 animate-pulse">3</span>
                </button>

                <div class="relative" id="profileContainer">
                    <button onclick="toggleProfile()" class="flex items-center gap-2 p-1 pr-3 rounded-full hover:bg-[color:var(--surface-hover)] transition" id="profileButton">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-semibold text-xs transition-transform hover:scale-110" style="background: linear-gradient(135deg, #6366f1, #ec4899); color: white; box-shadow: 0 4px 12px -2px rgba(99,102,241,0.5);">
                            {{ $userInitials }}
                        </div>
                        <span class="hidden lg:block text-sm font-medium">{{ $userName }}</span>
                        <svg class="w-4 h-4 text-neutral-500 transition-transform" id="profileArrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="profileDropdown" class="hidden dropdown-premium">
                        <div class="p-4 border-b border-[color:var(--line)]">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full flex items-center justify-center font-semibold" style="background: linear-gradient(135deg, #6366f1, #ec4899); color: white;">
                                    {{ $userInitials }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold truncate">{{ $userName }}</div>
                                    <div class="text-xs text-[color:var(--text-faint)] truncate">{{ $userEmail }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-[color:var(--surface-hover)] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ __('ui.profile') }}
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-[color:var(--surface-hover)] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ __('ui.settings') }}
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-[color:var(--surface-hover)] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                {{ __('ui.my_documents') }}
                            </a>
                        </div>
                        <div class="border-t border-[color:var(--line)] py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    {{ __('ui.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <button id="mobileMenuButton" class="md:hidden p-2 rounded-lg hover:bg-[color:var(--surface-hover)] transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>
</nav>

<main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-10">
    <section class="hero-premium rounded-2xl px-5 py-5 lg:px-6 lg:py-6 mb-6 animate-slide-up">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-5 relative z-10">
            <div class="min-w-0">
                <h1 class="text-xl lg:text-2xl font-extrabold tracking-tight mb-1.5">
                    {{ __('ui.dashboard_builder_title') }}
                </h1>
                <p class="text-sm leading-relaxed" style="color: var(--text-muted); max-width: 62ch;">
                    {{ __('ui.dashboard_builder_text') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <span class="chip-premium" id="serviceStatus">
                    <span class="chip-dot"></span>
                    <span id="serviceStatusLabel">{{ __('builder.ai.statusChecking') }}</span>
                </span>
                <span class="chip-premium" id="ragStatus" hidden>
                    <span class="chip-dot"></span>
                    <span id="ragStatusLabel"></span>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 relative z-10">
            <div class="metric-card">
                <div class="metric-label">{{ __('ui.dashboard_active_template') }}</div>
                <div class="metric-value truncate" id="heroTemplateLabel">{{ __('ui.template_service') }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">{{ __('ui.dashboard_sections_enabled') }}</div>
                <div class="metric-value" id="heroSectionCount">4</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">{{ __('ui.dashboard_contract_sum') }}</div>
                <div class="metric-value" id="heroAmount">120 000</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">{{ __('ui.dashboard_start_date') }}</div>
                <div class="metric-value" id="heroStartDate">15.04.2026</div>
            </div>
        </div>
    </section>

    <div class="glass rounded-2xl px-5 py-4 mb-6 flex items-center gap-3 lg:gap-5">
        <div class="flex items-center gap-3">
            <div class="stepper-dot is-done" id="step1">1</div>
            <div class="hidden sm:block">
                <div class="text-sm font-semibold">{{ __('ui.dashboard_choose_template') }}</div>
                <div class="text-xs text-[color:var(--text-faint)]">{{ __('builder.ui.stepContractType') }}</div>
            </div>
        </div>
        <div class="stepper-line" id="line1"></div>
        <div class="flex items-center gap-3">
            <div class="stepper-dot bg-[color:var(--surface-sunken)] text-neutral-500" id="step2">2</div>
            <div class="hidden sm:block">
                <div class="text-sm font-semibold">{{ __('ui.dashboard_fill_params') }}</div>
                <div class="text-xs text-[color:var(--text-faint)]">{{ __('builder.ui.stepParams') }}</div>
            </div>
        </div>
        <div class="stepper-line" id="line2"></div>
        <div class="flex items-center gap-3">
            <div class="stepper-dot bg-[color:var(--surface-sunken)] text-neutral-500" id="step3">3</div>
            <div class="hidden sm:block">
                <div class="text-sm font-semibold">{{ __('ui.dashboard_enable_sections') }}</div>
                <div class="text-xs text-[color:var(--text-faint)]">{{ __('builder.ui.stepSections') }}</div>
            </div>
        </div>
        <div class="ml-auto flex items-center gap-2 text-xs" style="color: var(--text-faint);">
            <svg class="w-4 h-4 float-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            <span class="hidden sm:inline">{{ __('builder.ui.autosave') }}</span>
            <span class="w-2 h-2 rounded-full animate-pulse" style="background: var(--ok); box-shadow: 0 0 8px var(--ok);"></span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-7 space-y-6">
            <div class="glass rounded-2xl p-6 lg:p-7 animate-slide-up">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wide mb-2.5" style="background: var(--accent-tint); color: var(--accent); box-shadow: 0 0 0 1px var(--accent-line);">
                            <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background: var(--accent); box-shadow: 0 0 6px var(--accent);"></span>
                            {{ __('builder.ui.step1') }}
                        </div>
                        <h2 class="text-2xl lg:text-3xl font-extrabold mb-1.5 tracking-tight">{{ __('ui.dashboard_choose_template') }}</h2>
                        <p class="text-sm text-[color:var(--text-muted)]">{{ __('ui.dashboard_choose_template_text') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="templateCards">
                    @foreach ([
                        'service' => ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
                        'supply' => ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
                        'nda' => ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>']
                    ] as $template => $meta)
                        <button type="button" class="template-card-premium glass p-5 text-left {{ $template === 'service' ? 'active' : '' }}" data-template="{{ $template }}">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-3.5 transition-transform" style="background: var(--gradient-primary); background-size: 200% 200%; animation: gradientShift 4s ease infinite; color: white; box-shadow: 0 4px 14px -4px var(--accent);">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $meta['icon'] !!}</svg>
                            </div>
                            <div class="font-bold text-base mb-1.5">{{ $builderTranslations['templates'][$template]['label'] }}</div>
                            <div class="text-sm mb-3.5 leading-relaxed" style="color: var(--text-muted);">{{ $builderTranslations['templates'][$template]['description'] }}</div>
                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold" style="background: var(--accent-tint); color: var(--accent);">
                                {{ $builderTranslations['templates'][$template]['badge'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="glass rounded-2xl p-6 lg:p-7 animate-slide-up">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wide mb-2.5" style="background: var(--accent-tint); color: var(--accent); box-shadow: 0 0 0 1px var(--accent-line);">
                            <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background: var(--accent); box-shadow: 0 0 6px var(--accent);"></span>
                            {{ __('builder.ui.step2') }}
                        </div>
                        <h2 class="text-2xl lg:text-3xl font-extrabold mb-1.5 tracking-tight">{{ __('ui.dashboard_fill_params') }}</h2>
                        <p class="text-sm text-[color:var(--text-muted)]">{{ __('ui.dashboard_fill_params_text') }}</p>
                    </div>
                    <button type="button" id="resetFormBtn" class="text-xs text-neutral-500 hover:text-red-500 transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        {{ __('builder.ui.reset') }}
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="input-premium">
                        <label>{{ __('ui.contract_number') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                            <input id="contractNumber" type="text" value="LA-042/26" placeholder="{{ __('builder.ui.numberPlaceholder') }}">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.city_of_signing') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <input id="contractCity" type="text" value="{{ __('builder.ui.defaultCity') }}">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.start_date') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <input id="startDate" type="date" value="2026-04-15">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.end_date') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <input id="endDate" type="date" value="2026-12-31">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.customer_buyer') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <input id="customerName" type="text" value="TOO LegalAI Client">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.contractor_supplier') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <input id="contractorName" type="text" value="TOO LegalAI Pro">
                        </div>
                    </div>
                    <div class="input-premium md:col-span-2">
                        <label>{{ __('ui.contract_subject') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <input id="contractSubject" type="text" value="{{ __('ui.service_default_subject') }}">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.amount') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <input id="contractAmount" type="number" min="0" step="1000" value="120000">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.payment_days') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <input id="paymentDays" type="number" min="1" value="5">
                        </div>
                    </div>
                    <div class="input-premium md:col-span-2">
                        <label>{{ __('ui.governing_law') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            <input id="governingLaw" type="text" value="{{ __('builder.ui.defaultLaw') }}">
                        </div>
                    </div>
                    <div class="input-premium md:col-span-2">
                        <label>{{ __('ui.penalty') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <input id="penaltyRule" type="text" value="{{ __('ui.default_penalty_rule') }}">
                        </div>
                    </div>
                    <div class="input-premium md:col-span-2">
                        <label>{{ __('ui.special_terms') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <textarea id="specialTerms" rows="3" style="padding-top: 0.875rem;">{{ __('builder.ui.defaultSpecialTermsValue') }}</textarea>
                        </div>
                    </div>

                    <div class="md:col-span-2 pt-5 mt-1 border-t border-[color:var(--line)]">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5 float-icon" style="color: var(--accent);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span class="text-sm font-extrabold tracking-tight">{{ __('builder.ai.criteriaTitle') }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-lg" style="background: var(--gradient-primary); color: white; background-size: 200% 200%; animation: gradientShift 4s ease infinite;" id="criteriaCounter">0</span>
                        </div>
                        <p class="text-xs text-[color:var(--text-muted)] mb-3">{{ __('builder.ai.criteriaHint') }}</p>

                        <div class="flex gap-2 mb-3">
                            <input id="criterionInput" type="text"
                                   class="flex-1 field-plain"
                                   placeholder="{{ __('builder.ai.criteriaPlaceholder') }}">
                            <button type="button" id="criterionAddBtn"
                                    class="toolbar-btn btn-primary px-4 py-2">
                                {{ __('builder.ai.criteriaAdd') }}
                            </button>
                        </div>

                        <div id="criteriaList" class="space-y-2"></div>
                    </div>
                </div>
            </div>

            <div class="glass rounded-2xl p-6 lg:p-7 animate-slide-up">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wide mb-2.5" style="background: var(--accent-tint); color: var(--accent); box-shadow: 0 0 0 1px var(--accent-line);">
                            <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background: var(--accent); box-shadow: 0 0 6px var(--accent);"></span>
                            {{ __('builder.ui.step3') }}
                        </div>
                        <h2 class="text-2xl lg:text-3xl font-extrabold mb-1.5 tracking-tight">{{ __('ui.dashboard_enable_sections') }}</h2>
                        <p class="text-sm text-[color:var(--text-muted)]">{{ __('ui.dashboard_enable_sections_text') }}</p>
                    </div>
                    <div class="text-xs text-[color:var(--text-faint)] bg-[color:var(--surface-sunken)] backdrop-blur px-3.5 py-2 rounded-lg" style="border: 1px solid var(--line);">
                        {{ __('builder.ui.selectedLabel') }} <span id="sectionsCounter" class="font-extrabold gradient-text text-base">4</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ([
                        'definitions' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        'scope' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        'payment' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'acceptance' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'liability' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                        'ip_rights' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        'termination' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'confidentiality' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
                    ] as $section => $icon)
                        <label class="section-card glass rounded-xl p-4 border-2 border-transparent {{ in_array($section, ['definitions', 'scope', 'payment', 'acceptance', 'liability', 'termination'], true) ? 'active' : '' }}">
                            <div class="flex items-start gap-3">
                                <input class="builder-section" type="checkbox" value="{{ $section }}" {{ in_array($section, ['definitions', 'scope', 'payment', 'acceptance', 'liability', 'termination'], true) ? 'checked' : '' }}>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icon !!}</svg>
                                        <div class="font-bold text-sm">{{ $builderTranslations['sections'][$section]['label'] }}</div>
                                    </div>
                                    <div class="text-xs text-[color:var(--text-muted)] leading-relaxed">{{ $builderTranslations['sections'][$section]['description'] }}</div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="flex flex-wrap gap-2 mt-5 pt-5 border-t border-[color:var(--line)]" id="activeSectionBadges"></div>
            </div>
        </div>

        <div class="xl:col-span-5">
            <div class="glass rounded-2xl p-6 preview-sticky sticky top-20 animate-slide-up">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-extrabold mb-0.5 tracking-tight">{{ __('ui.draft_contract') }}</h2>
                        <p class="text-sm" style="color: var(--text-muted);">{{ __('ui.draft_contract_text') }}</p>
                    </div>
                </div>

                <div class="preview-toolbar mb-4 flex-wrap">
                    <button type="button" class="toolbar-btn btn-primary" id="aiGenerateButton">
                        <svg class="w-4 h-4 float-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span id="aiGenerateLabel">{{ __('builder.ai.generate') }}</span>
                    </button>
                    <button type="button" class="toolbar-btn btn-outline hidden" id="aiStopButton">
                        <span class="spinner"></span>
                        {{ __('builder.ai.stop') }}
                    </button>
                    <button type="button" class="toolbar-btn btn-outline hidden" id="aiRestoreButton">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        {{ __('builder.ai.restore') }}
                    </button>
                    <div class="w-px h-5 bg-black/10 dark:bg-white/10 mx-1"></div>
                    <button type="button" class="toolbar-btn" id="copyContractButton">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        {{ __('ui.copy') }}
                    </button>
                    <div class="w-px h-5 bg-black/10 dark:bg-white/10 mx-1"></div>
                    <div class="relative">
                        <button type="button" class="toolbar-btn btn-outline" id="downloadDropdownButton">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            {{ __('ui.download') }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="downloadDropdown" class="hidden dropdown-premium">
                            <button class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-[color:var(--surface-hover)] transition text-left" id="downloadTxtButton">
                                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ __('ui.txt_document') }}
                            </button>
                            <button class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-[color:var(--surface-hover)] transition text-left" id="downloadPdfButton">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                {{ __('ui.pdf_document') }}
                            </button>
                            <button class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-[color:var(--surface-hover)] transition text-left" id="downloadDocxButton">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ __('ui.word_document') }}
                            </button>
                        </div>
                    </div>
                    <div class="ml-auto flex items-center gap-1">
                        <button type="button" class="toolbar-btn" id="fontSmaller" title="{{ __('builder.ui.fontSmaller') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                        </button>
                        <span class="text-xs font-mono text-[color:var(--text-muted)] px-1" id="fontSizeLabel">13</span>
                        <button type="button" class="toolbar-btn" id="fontBigger" title="{{ __('builder.ui.fontBigger') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>

                <div id="aiNotice" class="hidden mb-3 flex items-start gap-2.5 px-3 py-2.5 rounded-xl" style="background: var(--accent-tint); border: 1px solid var(--accent-line); box-shadow: 0 0 20px -8px var(--accent);">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5 animate-pulse" style="color: var(--accent);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs leading-relaxed" style="color: var(--text-muted);">
                        <span class="font-extrabold gradient-text">{{ __('builder.ai.aiBadge') }}.</span>
                        {{ __('builder.ai.aiNotice') }}
                    </div>
                </div>

                <div class="contract-paper px-6 py-7 lg:px-8 lg:py-9 max-h-[540px] overflow-y-auto mb-4">
                    <pre id="contractPreview" class="contract-preview"></pre>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="insight-success rounded-xl px-3.5 py-3">
                        <div class="text-[11px] font-bold uppercase tracking-wide mb-0.5" style="color: var(--ok);">{{ __('ui.build_status') }}</div>
                        <div class="text-sm font-bold" id="previewStatus">{{ __('ui.draft_ready') }}</div>
                    </div>
                    <div class="insight-warning rounded-xl px-3.5 py-3">
                        <div class="text-[11px] font-bold uppercase tracking-wide mb-0.5" style="color: var(--warn);">{{ __('ui.reminder') }}</div>
                        <div class="text-sm font-bold">{{ __('ui.review_required') }}</div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 float-icon" style="color: var(--accent);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <div class="text-sm font-extrabold tracking-tight">{{ __('ui.build_plan') }}</div>
                    </div>
                    <div class="space-y-2" id="timeline"></div>
                </div>

                <div class="mt-6 pt-5 border-t border-[color:var(--line)]">
                    <button type="button" id="chatToggle" class="w-full flex items-center gap-2 text-left group">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: var(--accent);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-extrabold tracking-tight">{{ __('builder.ai.chatTitle') }}</div>
                            <div class="text-xs text-[color:var(--text-faint)]">{{ __('builder.ai.chatHint') }}</div>
                        </div>
                        <svg id="chatChevron" class="w-4 h-4 text-neutral-400 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div id="chatPanel" class="chat-collapse">
                        <div class="chat-collapse-inner pt-4">
                            <div id="chatMessages" class="space-y-3 max-h-[320px] overflow-y-auto mb-3 pr-1">
                                <div class="text-xs text-[color:var(--text-faint)] italic" id="chatEmpty">{{ __('builder.ai.chatEmpty') }}</div>
                            </div>
                            <div class="flex gap-2">
                                <textarea id="chatInput" rows="2"
                                          class="flex-1 field-plain resize-none"
                                          placeholder="{{ __('builder.ai.chatPlaceholder') }}"></textarea>
                                <button type="button" id="chatSendBtn"
                                        class="toolbar-btn btn-primary px-3.5 self-stretch">
                                    {{ __('builder.ai.chatSend') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const translations = @json($builderTranslations);
    const locale = @json(app()->getLocale());

    function showToast(message, type = 'info', duration = 3000) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        const icons = {
            success: '<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            error: '<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            info: '<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `${icons[type]}<div class="flex-1 text-sm font-medium">${message}</div>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    (function initTheme() {
        const saved = localStorage.getItem('legalai-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = saved || (prefersDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
        if (theme === 'dark') document.documentElement.classList.add('dark');
    })();

    document.getElementById('themeToggle')?.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        document.documentElement.classList.toggle('dark', next === 'dark');
        localStorage.setItem('legalai-theme', next);
    });

    let profileOpen = false;
    function toggleProfile() {
        profileOpen = !profileOpen;
        document.getElementById('profileDropdown').classList.toggle('hidden', !profileOpen);
        document.getElementById('profileArrow').style.transform = profileOpen ? 'rotate(180deg)' : 'rotate(0)';
    }

    document.getElementById('notificationsButton')?.addEventListener('click', (e) => {
        e.stopPropagation();
        showToast(translations.ui?.notificationsToast, 'info');
    });

    let languageSwitcherOpen = false;
    document.getElementById('languageSwitcherButton')?.addEventListener('click', (e) => {
        e.stopPropagation();
        languageSwitcherOpen = !languageSwitcherOpen;
        document.getElementById('languageSwitcherDropdown').classList.toggle('hidden', !languageSwitcherOpen);
        document.getElementById('languageSwitcherArrow').style.transform = languageSwitcherOpen ? 'rotate(180deg)' : 'rotate(0)';
    });

    document.addEventListener('click', (e) => {
        if (profileOpen && !document.getElementById('profileContainer').contains(e.target)) {
            profileOpen = false;
            document.getElementById('profileDropdown').classList.add('hidden');
            document.getElementById('profileArrow').style.transform = 'rotate(0)';
        }
        if (languageSwitcherOpen && !document.getElementById('languageSwitcher').contains(e.target)) {
            languageSwitcherOpen = false;
            document.getElementById('languageSwitcherDropdown').classList.add('hidden');
            document.getElementById('languageSwitcherArrow').style.transform = 'rotate(0)';
        }
        if (!e.target.closest('#downloadDropdown') && !e.target.closest('#downloadDropdownButton')) {
            document.getElementById('downloadDropdown')?.classList.add('hidden');
        }
    });

    document.getElementById('downloadDropdownButton')?.addEventListener('click', (e) => {
        e.stopPropagation();
        document.getElementById('downloadDropdown').classList.toggle('hidden');
    });

    const templateConfig = translations.templates || {};
    const sectionConfig = translations.sections || {};
    const AI = translations.ai || {};

    const PYTHON_API = "{{ env('PYTHON_API_URL', 'http://127.0.0.1:5000') }}";
    const PAGE_LOCALE = "{{ app()->getLocale() }}";

    const state = { template: 'service', criteria: [], aiSections: {}, aiBusy: false };

    const hasAiSections = () => Object.keys(state.aiSections).length > 0;

    const fieldIds = ['contractNumber','contractCity','startDate','endDate','customerName','contractorName','contractSubject','contractAmount','paymentDays','governingLaw','penaltyRule','specialTerms'];
    const fields = {};
    fieldIds.forEach(id => fields[id] = document.getElementById(id));

    const previewElement = document.getElementById('contractPreview');
    const sectionInputs = Array.from(document.querySelectorAll('.builder-section'));
    const templateCards = Array.from(document.querySelectorAll('[data-template]'));

    const STORAGE_KEY = 'legalai-contract-draft';

    function saveToLocalStorage() {
        const data = {
            template: state.template,
            fields: {},
            sections: getSelectedSections(),
            criteria: state.criteria,
            aiSections: state.aiSections,
            fontSize: currentFontSize
        };
        fieldIds.forEach(id => { if (fields[id]) data.fields[id] = fields[id].value; });
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(data)); } catch(e) {}
    }

    function loadFromLocalStorage() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return;
            const data = JSON.parse(raw);
            if (data.template && templateConfig[data.template]) state.template = data.template;
            if (data.fields) {
                fieldIds.forEach(id => {
                    if (fields[id] && data.fields[id] !== undefined) fields[id].value = data.fields[id];
                });
            }
            if (data.sections) {
                sectionInputs.forEach(input => {
                    input.checked = data.sections.includes(input.value);
                    input.closest('.section-card')?.classList.toggle('active', input.checked);
                });
            }
            if (Array.isArray(data.criteria)) state.criteria = data.criteria.filter(c => typeof c === 'string');
            if (data.aiSections && typeof data.aiSections === 'object') {
                Object.entries(data.aiSections).forEach(([key, text]) => {
                    if (typeof text === 'string' && text.trim()) state.aiSections[key] = text;
                });
            }
            if (data.fontSize) {
                currentFontSize = data.fontSize;
                previewElement.style.fontSize = currentFontSize + 'px';
                document.getElementById('fontSizeLabel').textContent = currentFontSize;
            }
        } catch(e) {}
    }

    function resetForm() {
        if (!confirm(translations.ui?.confirmReset)) return;
        localStorage.removeItem(STORAGE_KEY);
        location.reload();
    }

    document.getElementById('resetFormBtn')?.addEventListener('click', resetForm);

    function replaceTokens(template, replacements = {}) {
        return Object.entries(replacements).reduce((r, [k, v]) => r.replaceAll(`:${k}`, v), template);
    }

    function syncTemplateSelection() {
        templateCards.forEach(c => c.classList.toggle('active', c.dataset.template === state.template));
    }

    function getSelectedSections() {
        return sectionInputs.filter(i => i.checked).map(i => i.value);
    }

    function formatAmount(v) {
        return `${new Intl.NumberFormat(locale === 'en' ? 'en-US' : 'ru-RU').format(Number(v || 0))} ${translations.ui?.rubCurrencySymbol || '₽'}`;
    }

    function formatDate(v) {
        if (!v) return translations.ui?.notSpecified || '';
        try { return new Intl.DateTimeFormat(locale === 'en' ? 'en-US' : 'ru-RU').format(new Date(v)); }
        catch { return translations.ui?.notSpecified || ''; }
    }

    function buildSectionBlocks(template, sections, values) {
        const blocks = [];
        const d = translations.draft || {};
        const get = (key) => d[key] || '';
        const put = (key, text) => blocks.push({ key, text });
        const has = (key) => sections.includes(key);

        if (has('definitions')) put('definitions', get('definitions'));
        if (has('scope')) put('scope', replaceTokens(get('scope'), { roleB: template.roleB, subject: values.subject, roleA: template.roleA }));
        if (has('payment')) put('payment', replaceTokens(get('payment'), { amount: formatAmount(values.amount), roleA: template.roleA, days: values.paymentDays, roleB: template.roleB }));
        if (has('acceptance')) put('acceptance', replaceTokens(get('acceptance'), { roleB: template.roleB, roleA: template.roleA, days: values.paymentDays }));
        if (has('liability')) put('liability', replaceTokens(get('liability'), { penalty: values.penaltyRule, roleB: template.roleB, roleA: template.roleA }));
        if (has('ip_rights')) put('ip_rights', replaceTokens(get('ip_rights'), { roleB: template.roleB, roleA: template.roleA }));
        if (has('termination')) put('termination', replaceTokens(get('termination'), { startDate: formatDate(values.startDate), endDate: formatDate(values.endDate) }));
        if (has('confidentiality')) put('confidentiality', get('confidentiality'));
        if (has('disputes')) put('disputes', replaceTokens(get('disputes'), { law: values.governingLaw }));
        if (has('force_majeure')) put('force_majeure', get('force_majeure'));
        if (has('notices')) put('notices', get('notices'));
        put('specialTerms', replaceTokens(get('specialTerms'), { terms: values.specialTerms }));
        put('entire_agreement', replaceTokens(get('entire_agreement'), { law: values.governingLaw }));
        put('signatures', replaceTokens(get('signatures'), { roleA: template.roleA, customer: values.customerName, roleB: template.roleB, contractor: values.contractorName }));
        return blocks;
    }

    function buildSectionText(template, sections, values) {
        const blocks = buildSectionBlocks(template, sections, values);
        const used = new Set();

        const parts = blocks.map(block => {
            if (Object.prototype.hasOwnProperty.call(state.aiSections, block.key)) {
                used.add(block.key);
                return state.aiSections[block.key];
            }
            return block.text;
        });

        Object.keys(state.aiSections).forEach(key => {
            if (key !== 'signatures' && !used.has(key)) parts.push(state.aiSections[key]);
        });

        const signIndex = blocks.findIndex(b => b.key === 'signatures');
        if (signIndex !== -1 && signIndex !== parts.length - 1) {
            parts.push(parts.splice(signIndex, 1)[0]);
        }
        return parts.join('\n\n');
    }

    function buildContractText() {
        const template = templateConfig[state.template];
        if (!template) return '';
        const ui = translations.ui || {};
        const values = currentValues();

        const d = translations.draft || {};
        const headerText = d.header || '';
        const header = `${template.label}\n№ ${values.number}\n${ui.cityPrefix || ''} ${values.city}\n${formatDate(values.startDate)}\n\n${replaceTokens(headerText, { customer: values.customerName, roleA: template.roleA, contractor: values.contractorName, roleB: template.roleB })}`;
        return `${header}\n\n${buildSectionText(template, getSelectedSections(), values)}`;
    }

    function renderBadges(selected) {
        const c = document.getElementById('activeSectionBadges');
        c.innerHTML = '';
        selected.forEach(s => {
            const b = document.createElement('span');
            b.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold transition-all hover:scale-105';
            b.style.background = 'var(--gradient-primary)';
            b.style.backgroundSize = '200% 200%';
            b.style.animation = 'gradientShift 4s ease infinite';
            b.style.color = 'white';
            b.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>${sectionConfig[s]?.label || s}`;
            c.appendChild(b);
        });
    }

    function renderTimeline(selected) {
        const template = templateConfig[state.template];
        if (!template) return;
        const t = translations.timeline || {};
        const items = [
            replaceTokens(t.template || ':value', { value: template.label }),
            replaceTokens(t.parties || ':value', { value: `${fields.customerName?.value.trim() || '—'} / ${fields.contractorName?.value.trim() || '—'}` }),
            replaceTokens(t.term || ':value', { value: `${formatDate(fields.startDate?.value)} - ${formatDate(fields.endDate?.value)}` }),
            replaceTokens(t.sections || ':value', { value: selected.map(s => sectionConfig[s]?.label).join(', ') || '—' })
        ];
        const tl = document.getElementById('timeline');
        tl.innerHTML = '';
        items.forEach((item, i) => {
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 p-3 rounded-xl text-xs hover:bg-[color:var(--surface-hover)] transition-all hover:-translate-y-0.5';
            div.innerHTML = `
                <div class="w-6 h-6 rounded-lg text-[10px] font-bold flex items-center justify-center flex-shrink-0" style="background: var(--gradient-primary); background-size: 200% 200%; animation: gradientShift 4s ease infinite; color: white; box-shadow: 0 4px 10px -4px var(--accent);">${i + 1}</div>
                <div class="text-[color:var(--text-muted)]">${item}</div>
            `;
            tl.appendChild(div);
        });
    }

    function renderHero(selected) {
        const template = templateConfig[state.template];
        if (!template) return;
        document.getElementById('heroTemplateLabel').textContent = template.label;
        document.getElementById('heroSectionCount').textContent = String(selected.length);
        document.getElementById('heroAmount').textContent = formatAmount(fields.contractAmount?.value);
        document.getElementById('heroStartDate').textContent = formatDate(fields.startDate?.value);
    }

    function updateStepper(selected) {
        const hasTemplate = !!state.template;
        const hasFields = fields.contractNumber?.value && fields.customerName?.value && fields.contractorName?.value;
        const hasSections = selected.length > 0;

        const setStep = (id, active, lineId) => {
            const el = document.getElementById(id);
            if (active) {
                el.className = 'stepper-dot is-done';
            } else {
                el.className = 'stepper-dot';
            }
            if (lineId) {
                document.getElementById(lineId)?.classList.toggle('active', active);
            }
        };

        setStep('step1', hasTemplate, null);
        setStep('step2', hasFields, 'line1');
        setStep('step3', hasSections, 'line2');
    }

    const criteriaList = document.getElementById('criteriaList');
    const criterionInput = document.getElementById('criterionInput');
    const REMOVE_ICON = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';

    function renderCriteria() {
        criteriaList.innerHTML = '';
        document.getElementById('criteriaCounter').textContent = state.criteria.length;

        if (!state.criteria.length) {
            const empty = document.createElement('div');
            empty.className = 'text-xs text-[color:var(--text-faint)] italic';
            empty.textContent = AI.criteriaEmpty || '';
            criteriaList.appendChild(empty);
            return;
        }

        state.criteria.forEach((text, index) => {
            const row = document.createElement('div');
            row.className = 'flex items-start gap-2 p-3 rounded-xl bg-[color:var(--surface-sunken)] border border-[color:var(--line)] transition-all hover:-translate-y-0.5 hover:shadow-md';

            const body = document.createElement('div');
            body.className = 'flex-1 text-sm leading-relaxed break-words';
            body.textContent = text;

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'text-neutral-400 hover:text-red-500 transition flex-shrink-0 mt-0.5';
            remove.title = AI.criteriaRemove || '';
            remove.innerHTML = REMOVE_ICON;
            remove.addEventListener('click', () => {
                state.criteria.splice(index, 1);
                renderCriteria();
                saveToLocalStorage();
            });

            row.append(body, remove);
            criteriaList.appendChild(row);
        });
    }

    function addCriterion(text) {
        const value = (text || '').trim();
        if (!value || state.criteria.includes(value)) return false;
        state.criteria.push(value);
        renderCriteria();
        saveToLocalStorage();
        return true;
    }

    document.getElementById('criterionAddBtn').addEventListener('click', () => {
        if (addCriterion(criterionInput.value)) criterionInput.value = '';
    });

    criterionInput.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        if (addCriterion(criterionInput.value)) criterionInput.value = '';
    });

    async function streamNdjson(url, body, signal, onChunk) {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/x-ndjson' },
            body: JSON.stringify(body),
            signal
        });
        if (!response.ok || !response.body) throw new Error('HTTP ' + response.status);

        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';

        while (true) {
            const { done, value } = await reader.read();
            if (done) break;
            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split('\n');
            buffer = lines.pop() || '';
            for (const line of lines) {
                if (!line.trim()) continue;
                let obj;
                try { obj = JSON.parse(line); } catch (e) { continue; }
                onChunk(obj);
            }
        }
    }

    const AI_MODEL = 'legal-draft';

    const aiGenerateButton = document.getElementById('aiGenerateButton');
    const aiStopButton = document.getElementById('aiStopButton');
    const aiRestoreButton = document.getElementById('aiRestoreButton');
    const aiNotice = document.getElementById('aiNotice');
    const previewScroll = previewElement.parentElement;
    let aiController = null;

    function fieldLabel(id) {
        const wrap = fields[id]?.closest('.input-premium');
        return wrap?.querySelector('label')?.textContent.trim() || id;
    }

    function currentValues() {
        const template = templateConfig[state.template] || {};
        const ui = translations.ui || {};
        return {
            number: fields.contractNumber?.value.trim() || ui.withoutNumber || '',
            city: fields.contractCity?.value.trim() || ui.notSpecified || '',
            startDate: fields.startDate?.value,
            endDate: fields.endDate?.value,
            customerName: fields.customerName?.value.trim() || ui.party1 || '',
            contractorName: fields.contractorName?.value.trim() || ui.party2 || '',
            subject: fields.contractSubject?.value.trim() || template.defaultSubject,
            amount: fields.contractAmount?.value,
            paymentDays: fields.paymentDays?.value || '5',
            governingLaw: fields.governingLaw?.value.trim() || ui.applicableLaw || '',
            penaltyRule: fields.penaltyRule?.value.trim() || ui.defaultPenaltyRule,
            specialTerms: fields.specialTerms?.value.trim() || ui.defaultSpecialTerms
        };
    }

    function collectDraftPayload() {
        const template = templateConfig[state.template] || {};
        const selected = getSelectedSections();
        const blocks = buildSectionBlocks(template, selected, currentValues())
            .filter(b => b.key !== 'signatures')
            .map(b => ({
                key: b.key,
                label: sectionConfig[b.key]?.label || b.key,
                text: state.aiSections[b.key] || b.text
            }));

        return {
            template: template.label || state.template,
            roleA: template.roleA || '',
            roleB: template.roleB || '',
            subject: fields.contractSubject?.value.trim() || template.defaultSubject || '',
            form: fieldIds.map(id => ({ label: fieldLabel(id), value: fields[id]?.value || '' })),
            sections: selected.map(s => sectionConfig[s]?.label || s),
            blocks,
            criteria: state.criteria,
            model: AI_MODEL,
            lang: PAGE_LOCALE
        };
    }

    const SECTION_RE = /^[ \t>*_#]*#{2,4}[ \t]*([a-zA-Z_][a-zA-Z0-9_]*)[^\n]*$/gm;

    function parseAiSections(text) {
        const found = {};
        const marks = [];
        SECTION_RE.lastIndex = 0;
        let match;
        while ((match = SECTION_RE.exec(text)) !== null) {
            marks.push({ key: match[1], start: match.index, end: SECTION_RE.lastIndex });
        }
        marks.forEach((mark, i) => {
            const stop = i + 1 < marks.length ? marks[i + 1].start : text.length;
            const body = text.slice(mark.end, stop).trim();
            if (body) found[mark.key] = body;
        });
        return found;
    }

    const previewStatus = document.getElementById('previewStatus');
    const templateStatusText = previewStatus.textContent;

    function updateAiUi() {
        const edited = hasAiSections();
        aiRestoreButton.classList.toggle('hidden', !edited);
        aiNotice.classList.toggle('hidden', !edited);
        aiStopButton.classList.toggle('hidden', !state.aiBusy);
        aiGenerateButton.classList.toggle('hidden', state.aiBusy);
        previewStatus.textContent = state.aiBusy
            ? (AI.generating || '')
            : (edited
                ? (AI.aiBadge || '') + ': ' + Object.keys(state.aiSections).length
                : templateStatusText);
    }

    async function generateWithAi() {
        if (state.aiBusy) return;
        state.aiBusy = true;
        aiController = new AbortController();
        updateAiUi();

        const before = Object.assign({}, state.aiSections);
        let text = '';
        try {
            let unapplied = [];
            let lostValues = [];
            await streamNdjson(PYTHON_API + '/api/contract/draft', collectDraftPayload(),
                aiController.signal, (obj) => {
                    if (obj.stage) {
                        const count = (obj.sections || []).length;
                        if (obj.stage === 'selecting') previewStatus.textContent = AI.stageSelecting || '';
                        if (obj.stage === 'editing') previewStatus.textContent = (AI.stageEditing || '').replace(':count', count);
                        if (obj.stage === 'retrying') previewStatus.textContent = (AI.stageRetrying || '').replace(':count', count);
                        return;
                    }
                    if (Array.isArray(obj.unapplied_criteria)) unapplied = obj.unapplied_criteria;
                    if (Array.isArray(obj.lost_form_values)) lostValues = obj.lost_form_values;
                    if (!obj.response) return;
                    text += obj.response;
                    const parsed = parseAiSections(text);
                    if (Object.keys(parsed).length) {
                        state.aiSections = Object.assign({}, before, parsed);
                        previewElement.textContent = buildContractText();
                        previewScroll.scrollTop = previewScroll.scrollHeight;
                    }
                });

            if (unapplied.length) {
                showToast((AI.unapplied || '').replace(':list', unapplied.join(', ')), 'error', 7000);
            }
            if (lostValues.length) {
                showToast((AI.lostValues || '').replace(':list', lostValues.join(', ')), 'error', 9000);
            }

            const parsed = parseAiSections(text);
            if (Object.keys(parsed).length) {
                state.aiSections = Object.assign({}, before, parsed);
                showToast((AI.generated || '') + ': ' + Object.keys(parsed).length, 'success');
            } else if (text.trim()) {
                state.aiSections = { contract: text.trim() };
                showToast(AI.generated, 'success');
            }
            previewElement.textContent = buildContractText();
        } catch (error) {
            showToast(error.name === 'AbortError' ? AI.stopped : AI.serviceDown,
                      error.name === 'AbortError' ? 'info' : 'error', 6000);
            state.aiSections = Object.assign({}, before, parseAiSections(text));
            previewElement.textContent = buildContractText();
        } finally {
            state.aiBusy = false;
            aiController = null;
            updateAiUi();
            saveToLocalStorage();
        }
    }

    function restoreTemplateDraft() {
        state.aiSections = {};
        previewElement.textContent = buildContractText();
        updateAiUi();
        saveToLocalStorage();
        showToast(AI.restored, 'info');
    }

    aiGenerateButton.addEventListener('click', generateWithAi);
    aiStopButton.addEventListener('click', () => aiController?.abort());
    aiRestoreButton.addEventListener('click', restoreTemplateDraft);

    async function checkServiceStatus() {
        const chip = document.getElementById('serviceStatus');
        const label = document.getElementById('serviceStatusLabel');
        const ragChip = document.getElementById('ragStatus');
        const ragLabel = document.getElementById('ragStatusLabel');

        try {
            const response = await fetch(PYTHON_API + '/api/rag/status', {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const status = await response.json();

            chip.dataset.state = 'ok';
            label.textContent = AI.statusOnline || '';

            if (status.ready) {
                ragChip.hidden = false;
                ragChip.dataset.state = 'ok';
                ragLabel.textContent = (AI.statusRag || '').replace(':count', status.chunks ?? '?');
            } else {
                ragChip.hidden = false;
                ragChip.dataset.state = 'warn';
                ragLabel.textContent = AI.statusRagOff || '';
            }
        } catch (error) {
            chip.dataset.state = 'warn';
            label.textContent = AI.statusOffline || '';
            ragChip.hidden = true;
        }
    }

    const CRITERION_RE = /^[ \t>*_-]*КРИТ[А-ЯЁ]{0,8}[ \t*_]*:[ \t*_]*(.+?)[ \t*_]*$/mi;

    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const chatSendBtn = document.getElementById('chatSendBtn');
    const chatHistory = [];
    let chatBusy = false;

    document.getElementById('chatToggle').addEventListener('click', () => {
        const panel = document.getElementById('chatPanel');
        const open = panel.classList.toggle('is-open');
        document.getElementById('chatChevron').style.transform = open ? 'rotate(180deg)' : '';
        if (open) setTimeout(() => chatInput.focus(), 200);
    });

    function contractContext() {
        const payload = collectDraftPayload();
        const form = payload.form.filter(f => f.value).map(f => f.label + ': ' + f.value).join('\n');
        const criteria = payload.criteria.length ? payload.criteria.map(c => '- ' + c).join('\n') : '-';
        return [
            'Тип: ' + payload.template,
            'Стороны: ' + payload.roleA + ' / ' + payload.roleB,
            '',
            form,
            '',
            'Критерии пользователя:',
            criteria,
            '',
            'Текущий текст договора:',
            previewElement.textContent.slice(0, 3000)
        ].join('\n');
    }

    function stripCriterion(text) {
        return text.replace(CRITERION_RE, '').trimEnd();
    }

    function addChatBubble(role, text) {
        document.getElementById('chatEmpty')?.remove();
        const wrap = document.createElement('div');
        wrap.className = (role === 'user' ? 'flex justify-end' : 'flex justify-start') + ' chat-msg-in';
        const bubble = document.createElement('div');
        bubble.className = role === 'user'
            ? 'max-w-[85%] px-3 py-2 rounded-xl text-sm chat-bubble-user'
            : 'max-w-[85%] px-3 py-2 rounded-xl text-sm chat-bubble-ai';
        const body = document.createElement('div');
        body.className = 'whitespace-pre-wrap leading-relaxed break-words';
        body.textContent = text;
        bubble.appendChild(body);
        wrap.appendChild(bubble);
        chatMessages.appendChild(wrap);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return { bubble, body };
    }

    function addTypingBubble() {
        const { bubble, body } = addChatBubble('ai', '');
        body.innerHTML = '<span class="typing-dots"><span></span><span></span><span></span></span>';
        return { bubble, body };
    }

    function attachCriterionButton(bubble, suggestion) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'mt-2 w-full text-left text-xs font-semibold px-3 py-2 rounded-lg transition-all hover:scale-[1.02]';
        button.style.background = 'var(--gradient-primary)';
        button.style.backgroundSize = '200% 200%';
        button.style.animation = 'gradientShift 4s ease infinite';
        button.style.color = 'white';
        button.textContent = (AI.addCriterion || '') + ': ' + suggestion;
        button.addEventListener('click', () => {
            if (addCriterion(suggestion)) showToast(AI.criterionAdded, 'success');
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');
        });
        bubble.appendChild(button);
    }

    async function sendChatMessage() {
        const message = chatInput.value.trim();
        if (!message || chatBusy) return;
        chatBusy = true;
        chatSendBtn.disabled = true;
        chatSendBtn.classList.add('opacity-50');
        chatInput.value = '';

        addChatBubble('user', message);
        const { bubble, body } = addTypingBubble();

        let text = '';
        let sourceText = '';
        try {
            await streamNdjson(PYTHON_API + '/api/chat', {
                message,
                model: AI_MODEL,
                temperature: 0.15,
                history: chatHistory.slice(-6),
                context: contractContext(),
                lang: PAGE_LOCALE
            }, null, (obj) => {
                if (obj.response) {
                    text += obj.response;
                    body.textContent = stripCriterion(text) || text;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
                if (obj.source_response) sourceText = obj.source_response;
            });

            const match = text.match(CRITERION_RE);
            body.textContent = stripCriterion(text) || text;
            if (match) attachCriterionButton(bubble, match[1].trim());

            chatHistory.push({ role: 'user', content: message });
            chatHistory.push({ role: 'ai', content: sourceText || text });
        } catch (error) {
            body.textContent = AI.serviceDown || 'Error';
        } finally {
            chatBusy = false;
            chatSendBtn.disabled = false;
            chatSendBtn.classList.remove('opacity-50');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    chatSendBtn.addEventListener('click', sendChatMessage);
    chatInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendChatMessage();
        }
    });

    let debounceTimer;
    function renderBuilder() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const selected = getSelectedSections();
            if (!state.aiBusy) previewElement.textContent = buildContractText();
            renderCriteria();
            renderBadges(selected);
            renderTimeline(selected);
            renderHero(selected);
            updateStepper(selected);
            document.getElementById('sectionsCounter').textContent = selected.length;
            saveToLocalStorage();
        }, 150);
    }

    templateCards.forEach(c => c.addEventListener('click', () => {
        state.template = c.dataset.template;
        if (fields.contractSubject && templateConfig[state.template]) {
            fields.contractSubject.value = templateConfig[state.template].defaultSubject;
        }
        syncTemplateSelection();
        renderBuilder();
    }));

    Object.values(fields).forEach(f => {
        if (!f) return;
        f.addEventListener('input', renderBuilder);
        f.addEventListener('change', renderBuilder);
    });

    sectionInputs.forEach(input => {
        input.addEventListener('change', () => {
            input.closest('.section-card').classList.toggle('active', input.checked);
            renderBuilder();
        });
    });

    let currentFontSize = 13;
    document.getElementById('fontSmaller')?.addEventListener('click', () => {
        if (currentFontSize > 10) {
            currentFontSize -= 1;
            previewElement.style.fontSize = currentFontSize + 'px';
            document.getElementById('fontSizeLabel').textContent = currentFontSize;
            saveToLocalStorage();
        }
    });
    document.getElementById('fontBigger')?.addEventListener('click', () => {
        if (currentFontSize < 20) {
            currentFontSize += 1;
            previewElement.style.fontSize = currentFontSize + 'px';
            document.getElementById('fontSizeLabel').textContent = currentFontSize;
            saveToLocalStorage();
        }
    });

    document.getElementById('copyContractButton')?.addEventListener('click', async () => {
        if (!navigator.clipboard) return;
        await navigator.clipboard.writeText(previewElement.textContent);
        showToast(translations.ui?.copiedToast, 'success');
    });

    document.getElementById('downloadTxtButton')?.addEventListener('click', () => {
        const blob = new Blob([previewElement.textContent], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `contract-${fields.contractNumber?.value || 'draft'}.txt`;
        a.click();
        URL.revokeObjectURL(url);
        showToast(translations.ui?.txtDownloaded, 'success');
        document.getElementById('downloadDropdown').classList.add('hidden');
    });

    document.getElementById('downloadPdfButton')?.addEventListener('click', async () => {
        try {
            await html2pdf().set({
                margin: [10, 10, 10, 10],
                filename: `contract-${fields.contractNumber?.value || 'draft'}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(previewElement).save();
            showToast(translations.ui?.pdfDownloaded, 'success');
        } catch (err) {
            console.error(err);
            showToast(translations.ui?.pdfError, 'error');
        }
        document.getElementById('downloadDropdown').classList.add('hidden');
    });

    document.getElementById('downloadDocxButton')?.addEventListener('click', async () => {
        try {
            if (typeof docx === 'undefined' && typeof window.docx === 'undefined') throw new Error(translations.ui?.docxLibMissing);
            const DocxLib = window.docx || docx;
            const { Document, Packer, Paragraph } = DocxLib;
            const text = previewElement.textContent || '';
            if (!text.trim()) throw new Error(translations.ui?.noText);

            const lines = text.split('\n');
            const children = lines.map(line => {
                const trimmed = line.trim();
                if (!trimmed) return new Paragraph({ text: '' });
                const isHeader = /^[0-9]+\.?\s/.test(trimmed);
                return new Paragraph({ text: trimmed, bold: isHeader, size: isHeader ? 28 : 24, spacing: { after: isHeader ? 400 : 200, line: 360 } });
            });

            const doc = new Document({ sections: [{ properties: {}, children }] });
            const blob = await Packer.toBlob(doc);
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `contract-${(fields.contractNumber?.value || 'draft').replace(/[^а-яА-ЯёЁҷҶҳҲӣӢқҚғҒӯӮa-zA-Z0-9-_]/g, '')}.docx`;
            a.click();
            setTimeout(() => URL.revokeObjectURL(url), 100);
            showToast(translations.ui?.docxDownloaded, 'success');
        } catch (err) {
            console.error(err);
            showToast((translations.ui?.docxErrorPrefix || '') + err.message, 'error');
        }
        document.getElementById('downloadDropdown').classList.add('hidden');
    });

    loadFromLocalStorage();
    syncTemplateSelection();
    updateAiUi();
    renderBuilder();
    checkServiceStatus();
</script>
</body>
</html>