<!DOCTYPE html>
<html lang="ru" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ __('generate.meta_description') }}">
    <title>{{ __('generate.page_title') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://unpkg.com/docx@7.4.1/build/index.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        :root {
            /* Default Dark Theme */
            --color-bg: #0a0a0f;
            --color-surface: #12121a;
            --color-surface-elevated: #1a1a25;
            --color-border: rgba(255, 255, 255, 0.08);
            --color-border-hover: rgba(139, 92, 246, 0.3);
            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --text-muted: #71717a;
            --brand-primary: #8b5cf6;
            --brand-secondary: #06b6d4;
            --brand-accent: #f59e0b;
            --brand-success: #10b981;
            --brand-error: #ef4444;
            --gradient-brand: linear-gradient(135deg, #8b5cf6 0%, #6366f1 50%, #06b6d4 100%);
            --gradient-glow: radial-gradient(ellipse at center, rgba(139, 92, 246, 0.25) 0%, transparent 70%);
            --gradient-surface: linear-gradient(145deg, rgba(26, 27, 46, 0.9), rgba(19, 20, 31, 0.95));
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 8px 30px rgba(0, 0, 0, 0.5);
            --shadow-glow: 0 0 40px rgba(139, 92, 246, 0.3);
            --shadow-inner: inset 0 1px 0 rgba(255, 255, 255, 0.05);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-full: 9999px;
            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 400ms cubic-bezier(0.4, 0, 0.2, 1);
            --bg-pattern: none;
        }

        /* ===== THEME: STARTUP (неон) ===== */
        [data-theme="startup"] {
            --color-bg: #0c0618;
            --color-surface: #150a28;
            --color-surface-elevated: #1e0f3a;
            --color-border: rgba(236, 72, 153, 0.15);
            --color-border-hover: rgba(236, 72, 153, 0.4);
            --text-primary: #ffffff;
            --text-secondary: #d8b4fe;
            --text-muted: #a78bfa;
            --brand-primary: #ec4899;
            --brand-secondary: #8b5cf6;
            --brand-accent: #06b6d4;
            --brand-success: #10b981;
            --brand-error: #f43f5e;
            --gradient-brand: linear-gradient(135deg, #ec4899 0%, #8b5cf6 50%, #06b6d4 100%);
            --gradient-glow: radial-gradient(ellipse at center, rgba(236, 72, 153, 0.3) 0%, transparent 70%);
            --gradient-surface: linear-gradient(145deg, rgba(30, 15, 58, 0.95), rgba(21, 10, 40, 0.98));
            --shadow-glow: 0 0 60px rgba(236, 72, 153, 0.35);
        }

        /* ===== THEME: AURORA (сияние) ===== */
        [data-theme="aurora"] {
            --color-bg: #030712;
            --color-surface: #0a0f1e;
            --color-surface-elevated: #111827;
            --color-border: rgba(34, 211, 238, 0.12);
            --color-border-hover: rgba(34, 211, 238, 0.3);
            --text-primary: #f0f9ff;
            --text-secondary: #bae6fd;
            --text-muted: #7dd3fc;
            --brand-primary: #22d3ee;
            --brand-secondary: #10b981;
            --brand-accent: #a78bfa;
            --brand-success: #34d399;
            --brand-error: #fb7185;
            --gradient-brand: linear-gradient(135deg, #22d3ee 0%, #10b981 50%, #a78bfa 100%);
            --gradient-glow: radial-gradient(ellipse at center, rgba(34, 211, 238, 0.25) 0%, transparent 70%);
            --gradient-surface: linear-gradient(145deg, rgba(17, 24, 39, 0.9), rgba(10, 15, 30, 0.95));
            --shadow-glow: 0 0 50px rgba(34, 211, 238, 0.3);
        }

        /* ===== THEME: GRID (футуризм) ===== */
        [data-theme="grid"] {
            --color-bg: #000000;
            --color-surface: #0a0a0a;
            --color-surface-elevated: #141414;
            --color-border: rgba(34, 197, 94, 0.15);
            --color-border-hover: rgba(34, 197, 94, 0.4);
            --text-primary: #ecfccb;
            --text-secondary: #bef264;
            --text-muted: #84cc16;
            --brand-primary: #22c55e;
            --brand-secondary: #eab308;
            --brand-accent: #06b6d4;
            --brand-success: #10b981;
            --brand-error: #f43f5e;
            --gradient-brand: linear-gradient(135deg, #22c55e 0%, #eab308 50%, #06b6d4 100%);
            --gradient-glow: radial-gradient(ellipse at center, rgba(34, 197, 94, 0.2) 0%, transparent 70%);
            --gradient-surface: linear-gradient(145deg, rgba(20, 20, 20, 0.95), rgba(10, 10, 10, 0.98));
            --shadow-glow: 0 0 40px rgba(34, 197, 94, 0.25);
            --bg-pattern:
                linear-gradient(rgba(34, 197, 94, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34, 197, 94, 0.06) 1px, transparent 1px);
        }

        /* ===== THEME: LIGHT (светлая) ===== */
        [data-theme="light"] {
            --color-bg: #f8fafc;
            --color-surface: #ffffff;
            --color-surface-elevated: #f1f5f9;
            --color-border: rgba(15, 23, 42, 0.08);
            --color-border-hover: rgba(99, 102, 241, 0.3);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --brand-primary: #6366f1;
            --brand-secondary: #0891b2;
            --brand-accent: #d97706;
            --brand-success: #059669;
            --brand-error: #dc2626;
            --gradient-brand: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #0891b2 100%);
            --gradient-glow: radial-gradient(ellipse at center, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            --gradient-surface: linear-gradient(145deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.95));
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 8px 30px rgba(0, 0, 0, 0.12);
            --shadow-glow: 0 0 40px rgba(99, 102, 241, 0.15);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--color-bg);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
            transition: background var(--transition-base), color var(--transition-base);
        }

        /* Animated Background */
        .bg-gradient {
            position: fixed;
            inset: 0;
            z-index: -1;
            background-image: var(--bg-pattern),
            radial-gradient(ellipse at 20% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 20%, rgba(6, 182, 212, 0.15) 0%, transparent 50%),
            var(--color-bg);
            background-size: 40px 40px, auto, auto, auto;
            transition: background-image var(--transition-base), background var(--transition-base);
        }

        [data-theme="startup"] .bg-gradient {
            background-image: var(--bg-pattern),
            radial-gradient(ellipse at 20% 80%, rgba(236, 72, 153, 0.18) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 50% 50%, rgba(6, 182, 212, 0.08) 0%, transparent 70%),
            var(--color-bg);
        }

        [data-theme="aurora"] .bg-gradient {
            background-image: var(--bg-pattern),
            radial-gradient(ellipse at 20% 30%, rgba(34, 211, 238, 0.18) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 70%, rgba(167, 139, 250, 0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 50% 50%, rgba(16, 185, 129, 0.1) 0%, transparent 70%),
            var(--color-bg);
            animation: auroraShift 15s ease-in-out infinite;
        }

        @keyframes auroraShift {
            0%, 100% { filter: hue-rotate(0deg); }
            50% { filter: hue-rotate(20deg); }
        }

        [data-theme="light"] .bg-gradient {
            background-image: var(--bg-pattern),
            radial-gradient(ellipse at 20% 80%, rgba(99, 102, 241, 0.12) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 20%, rgba(8, 145, 178, 0.12) 0%, transparent 50%),
            var(--color-bg);
        }

        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: float 20s ease-in-out infinite;
            pointer-events: none;
            z-index: -1;
        }

        .bg-orb:nth-child(2) {
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            top: -200px;
            right: -200px;
        }

        .bg-orb:nth-child(3) {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, var(--brand-secondary), var(--brand-accent));
            bottom: -100px;
            left: -100px;
            animation-delay: -7s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* Header */
        .header {
            background: rgba(18, 18, 26, 0.7);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--color-border);
            padding: 16px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        [data-theme="light"] .header {
            background: rgba(255, 255, 255, 0.8);
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-logo {
            width: 44px;
            height: 44px;
            background: var(--gradient-brand);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: var(--shadow-glow);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: var(--shadow-glow); }
            50% { box-shadow: 0 0 60px rgba(139, 92, 246, 0.5); }
        }

        .header-title {
            font-size: 1.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--text-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
        }

        .header-badge {
            background: var(--gradient-brand);
            color: white;
            font-size: 0.6875rem;
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-left: 8px;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: auto;
        }

        .header-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color var(--transition-fast);
            position: relative;
        }

        .header-link:hover { color: var(--text-primary); }

        .header-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-brand);
            transition: width var(--transition-base);
        }

        .header-link:hover::after { width: 100%; }

        /* ===== Theme Switcher ===== */
        .theme-switcher {
            position: relative;
        }

        .theme-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-full);
            color: var(--text-primary);
            cursor: pointer;
            transition: all var(--transition-fast);
            font-family: inherit;
            font-size: 0.8125rem;
            font-weight: 500;
        }

        .theme-btn:hover {
            border-color: var(--brand-primary);
            background: var(--color-surface-elevated);
            transform: translateY(-1px);
        }

        .theme-btn i {
            font-size: 1rem;
            background: var(--gradient-brand);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .theme-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 260px;
            background: var(--color-surface-elevated);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 8px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px) scale(0.95);
            transition: all var(--transition-base);
            z-index: 1000;
        }

        .theme-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .theme-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all var(--transition-fast);
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .theme-option:hover {
            background: rgba(139, 92, 246, 0.1);
        }

        .theme-option.active {
            background: var(--gradient-brand);
            color: white;
        }

        .theme-preview {
            width: 36px;
            height: 24px;
            border-radius: 6px;
            border: 2px solid var(--color-border);
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .theme-option.active .theme-preview {
            border-color: white;
        }

        .theme-preview.dark {
            background: linear-gradient(135deg, #8b5cf6, #06b6d4);
        }
        .theme-preview.startup {
            background: linear-gradient(135deg, #ec4899, #8b5cf6, #06b6d4);
        }
        .theme-preview.aurora {
            background: linear-gradient(135deg, #22d3ee, #10b981, #a78bfa);
        }
        .theme-preview.grid {
            background:
                linear-gradient(rgba(34, 197, 94, 0.3) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34, 197, 94, 0.3) 1px, transparent 1px),
                linear-gradient(135deg, #000, #1a1a1a);
            background-size: 6px 6px, 6px 6px, auto;
        }
        .theme-preview.light {
            background: linear-gradient(135deg, #f8fafc, #6366f1, #0891b2);
        }

        /* Profile */
        .profile-wrapper { position: relative; }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 16px 6px 6px;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-full);
            color: var(--text-primary);
            cursor: pointer;
            transition: all var(--transition-fast);
            font-family: inherit;
        }

        .profile-btn:hover {
            border-color: var(--brand-primary);
            background: var(--color-surface-elevated);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .profile-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--gradient-brand);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 1.3;
        }

        .profile-name {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .profile-email {
            font-size: 0.6875rem;
            color: var(--text-muted);
        }

        .profile-arrow {
            width: 16px;
            height: 16px;
            color: var(--text-muted);
            transition: transform var(--transition-fast);
        }

        .profile-btn[aria-expanded="true"] .profile-arrow {
            transform: rotate(180deg);
        }

        .profile-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: 320px;
            background: var(--color-surface-elevated);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            overflow: hidden;
            animation: dropdownIn 0.2s ease-out;
        }

        .profile-dropdown.show { display: block; }

        @keyframes dropdownIn {
            from { opacity: 0; transform: translateY(-8px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .profile-dropdown-header {
            padding: 20px;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            gap: 16px;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), transparent);
        }

        .profile-dropdown-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--gradient-brand);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.125rem;
            box-shadow: var(--shadow-glow);
        }

        .profile-dropdown-info { flex: 1; min-width: 0; }

        .profile-dropdown-name {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .profile-dropdown-email {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
        }

        .profile-dropdown-menu { padding: 8px 0; }

        .profile-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all var(--transition-fast);
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            font-family: inherit;
        }

        .profile-dropdown-item:hover {
            background: rgba(139, 92, 246, 0.1);
            color: var(--brand-primary);
        }

        .profile-dropdown-item i {
            width: 18px;
            height: 18px;
            color: var(--text-muted);
        }

        .profile-dropdown-item.logout {
            color: var(--brand-error);
            border-top: 1px solid var(--color-border);
            margin-top: 8px;
            padding-top: 12px;
        }

        .profile-dropdown-item.logout:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        /* Progress Bar */
        .progress {
            max-width: 900px;
            margin: 32px auto 0;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: transform var(--transition-fast);
        }

        .step:hover { transform: translateY(-2px); }

        .step-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--color-surface);
            border: 2px solid var(--color-border);
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text-muted);
            transition: all var(--transition-base);
            position: relative;
            overflow: hidden;
        }

        .step-dot::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--gradient-brand);
            opacity: 0;
            transition: opacity var(--transition-fast);
        }

        .step.active .step-dot {
            border-color: transparent;
            color: white;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.3), var(--shadow-glow);
        }

        .step.active .step-dot::before { opacity: 1; }

        .step.done .step-dot {
            border-color: var(--brand-success);
            background: var(--brand-success);
            color: white;
            animation: stepPop 0.3s ease-out;
        }

        @keyframes stepPop {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .step-label {
            font-size: 0.6875rem;
            color: var(--text-muted);
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .step.active .step-label { color: var(--brand-primary); }
        .step.done .step-label { color: var(--brand-success); }

        .step-line {
            width: 60px;
            height: 2px;
            background: var(--color-border);
            position: relative;
            margin-top: 18px;
        }

        .step-line::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--gradient-brand);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform var(--transition-base);
        }

        .step-line.done::before { transform: scaleX(1); }

        .main {
            max-width: 900px;
            margin: 32px auto;
            padding: 0 24px;
        }

        .page {
            display: none;
            background: var(--gradient-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            padding: 40px;
            animation: pageIn 0.4s ease-out;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--brand-primary), transparent);
            opacity: 0.3;
        }

        .page.active { display: block; }

        @keyframes pageIn {
            from { opacity: 0; transform: translateY(24px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 32px;
        }

        .page-icon {
            width: 56px;
            height: 56px;
            background: var(--gradient-brand);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            box-shadow: var(--shadow-glow);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 0.9375rem;
            line-height: 1.6;
            max-width: 500px;
        }

        .field { margin-bottom: 24px; }

        .field-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .field-required { color: var(--brand-error); margin-left: 2px; }

        .field-input,
        .field-textarea,
        .field-select {
            width: 100%;
            padding: 14px 16px;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 0.9375rem;
            font-family: inherit;
            transition: all var(--transition-fast);
            outline: none;
        }

        .field-input:focus,
        .field-textarea:focus,
        .field-select:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15), var(--shadow-inner);
            background: var(--color-surface-elevated);
        }

        .field-textarea {
            min-height: 120px;
            resize: vertical;
            line-height: 1.7;
        }

        .field-input::placeholder,
        .field-textarea::placeholder {
            color: var(--text-muted);
            opacity: 0.6;
        }

        .field-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23a1a1aa' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
            cursor: pointer;
        }

        .field-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 8px;
            font-style: italic;
            opacity: 0.8;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            gap: 16px;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: var(--radius-md);
            font-size: 0.9375rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-fast);
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }

        .btn:hover::before { opacity: 1; }
        .btn:active { transform: translateY(1px) scale(0.99); }

        .btn-back {
            background: var(--color-surface);
            color: var(--text-secondary);
            border: 1px solid var(--color-border);
        }

        .btn-back:hover {
            background: var(--color-surface-elevated);
            border-color: var(--brand-primary);
            color: var(--text-primary);
        }

        .btn-next {
            background: var(--gradient-brand);
            color: white;
            margin-left: auto;
            box-shadow: 0 4px 20px rgba(139, 92, 246, 0.4);
        }

        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(139, 92, 246, 0.6);
        }

        .btn-generate {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            width: 100%;
            justify-content: center;
            padding: 18px 32px;
            font-size: 1rem;
            font-weight: 700;
            margin-top: 16px;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.4);
            letter-spacing: 0.3px;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(16, 185, 129, 0.6);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn-sm {
            padding: 8px 16px;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            font-size: 0.8125rem;
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition-fast);
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-sm:hover {
            background: var(--color-surface-elevated);
            color: var(--text-primary);
            border-color: var(--brand-primary);
            transform: translateY(-1px);
        }

        .btn-sm-danger {
            background: var(--brand-error);
            border-color: #dc2626;
            color: white;
        }

        .btn-sm-danger:hover {
            background: #dc2626;
            border-color: #991b1b;
        }

        .loader {
            display: none;
            text-align: center;
            padding: 64px 24px;
        }

        .loader.active { display: block; }

        .spinner {
            width: 64px;
            height: 64px;
            border: 3px solid var(--color-border);
            border-top-color: var(--brand-primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 24px;
            position: relative;
        }

        .spinner::after {
            content: '';
            position: absolute;
            inset: -4px;
            border: 3px solid transparent;
            border-top-color: var(--brand-secondary);
            border-radius: 50%;
            animation: spin 1.2s linear infinite reverse;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .loader-text {
            color: var(--text-secondary);
            font-size: 1rem;
            font-weight: 500;
        }

        .loader-subtext {
            display: block;
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 8px;
        }

        .result {
            display: none;
            margin-top: 32px;
            animation: pageIn 0.4s ease-out;
        }

        .result.active { display: block; }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--color-border);
        }

        .result-title {
            font-size: 1.25rem;
            color: var(--text-primary);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .result-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ===== ВЫБОР СТИЛЯ ДОГОВОРА ===== */
        .style-picker-block {
            margin-bottom: 24px;
        }

        .style-picker-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .style-picker-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: 0.2px;
        }

        .style-picker-hint {
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        .style-picker {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .style-card {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 16px;
            text-align: left;
            background: var(--gradient-surface);
            border: 1.5px solid var(--color-border);
            border-radius: var(--radius-lg);
            color: var(--text-secondary);
            font-family: inherit;
            cursor: pointer;
            overflow: hidden;
            transition: transform var(--transition-base), border-color var(--transition-base), box-shadow var(--transition-base);
        }

        .style-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(115deg, transparent 35%, rgba(255, 255, 255, 0.12) 50%, transparent 65%);
            transform: translateX(-120%);
            pointer-events: none;
        }

        .style-card:hover {
            transform: translateY(-4px);
            border-color: var(--color-border-hover);
            box-shadow: var(--shadow-lg);
        }

        .style-card:hover::before {
            animation: styleShimmer 900ms ease;
        }

        .style-card.selected {
            border-color: var(--brand-primary);
            box-shadow: var(--shadow-glow);
            animation: stylePop 420ms cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .style-card.selected::before {
            animation: styleShimmer 2600ms ease infinite;
        }

        @keyframes styleShimmer {
            to { transform: translateX(120%); }
        }

        @keyframes stylePop {
            0% { transform: scale(0.96); }
            60% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }

        .style-card-check {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--gradient-brand);
            color: #fff;
            font-size: 0.65rem;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: scale(0.2) rotate(-90deg);
            transition: all var(--transition-base);
        }

        .style-card.selected .style-card-check {
            opacity: 1;
            transform: scale(1) rotate(0);
        }

        /* Мини-превью документа внутри карточки */
        .style-preview {
            height: 74px;
            border-radius: var(--radius-sm);
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 5px;
            overflow: hidden;
            position: relative;
        }

        .style-preview-classic {
            background: #fdfdf8;
            border: 1px solid #d8d3c4;
        }

        .style-preview-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 55%, #f093fb 100%);
        }

        .sp-line {
            height: 5px;
            border-radius: 2px;
            background: rgba(15, 23, 42, 0.18);
            width: 100%;
        }

        .style-preview-modern .sp-line {
            background: rgba(255, 255, 255, 0.35);
        }

        .sp-line.sp-title {
            height: 8px;
            width: 58%;
            margin: 0 auto 4px;
            background: rgba(15, 23, 42, 0.55);
        }

        .style-preview-modern .sp-line.sp-title {
            width: 62%;
            margin: 0 0 4px;
            background: rgba(255, 255, 255, 0.85);
        }

        .sp-line.sp-short { width: 62%; }
        .sp-line.sp-mid { width: 82%; }

        .style-card.selected .sp-line {
            animation: spType 1.8s ease-in-out infinite;
        }

        .style-card.selected .sp-line:nth-child(2) { animation-delay: 0.15s; }
        .style-card.selected .sp-line:nth-child(3) { animation-delay: 0.3s; }
        .style-card.selected .sp-line:nth-child(4) { animation-delay: 0.45s; }
        .style-card.selected .sp-line:nth-child(5) { animation-delay: 0.6s; }

        @keyframes spType {
            0%, 100% { opacity: 0.45; }
            50% { opacity: 1; }
        }

        .style-preview-modern::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 120%, rgba(255, 255, 255, 0.4), transparent 55%);
            opacity: 0.6;
        }

        .style-card-name {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .style-card-desc {
            font-size: 0.78125rem;
            line-height: 1.5;
            color: var(--text-muted);
        }

        .style-card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: auto;
        }

        .style-tag {
            font-size: 0.6875rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: var(--radius-full);
            background: rgba(139, 92, 246, 0.12);
            border: 1px solid var(--color-border);
            color: var(--text-secondary);
        }

        .ai-badge {
            display: none;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding: 8px 14px;
            border-radius: var(--radius-full);
            background: rgba(139, 92, 246, 0.12);
            border: 1px solid var(--color-border);
            color: var(--text-secondary);
            font-size: 0.8125rem;
        }

        .ai-badge.active { display: inline-flex; }

        .ai-badge.done {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.35);
            color: var(--brand-success);
        }

        /* ===== CONTRACT BOX - Стартап стиль ===== */
        .contract-box {
            background: #ffffff;
            color: #1e293b;
            border-radius: var(--radius-lg);
            padding: 0;
            font-family: 'Space Grotesk', 'Inter', system-ui, sans-serif;
            font-size: 0.9rem;
            line-height: 1.7;
            max-height: 800px;
            overflow-y: auto;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid #e2e8f0;
        }

        .contract-box::-webkit-scrollbar { width: 10px; }
        .contract-box::-webkit-scrollbar-track { background: #f1f5f9; }
        .contract-box::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #8b5cf6, #06b6d4);
            border-radius: 5px;
        }

        /* Contract Header */
        .contract-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            color: white;
            padding: 40px 48px;
            position: relative;
            overflow: hidden;
        }

        .contract-hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(100px, -100px);
        }

        .contract-hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #fbbf24, #ec4899, #8b5cf6, #06b6d4);
        }

        .contract-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            position: relative;
            z-index: 2;
        }

        .contract-brand-logo {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .contract-brand-name {
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.9;
        }

        .contract-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
            position: relative;
            z-index: 2;
        }

        .contract-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 400;
            position: relative;
            z-index: 2;
        }

        .contract-meta {
            display: flex;
            gap: 24px;
            margin-top: 24px;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        .contract-meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .contract-meta-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.7;
            font-weight: 500;
        }

        .contract-meta-value {
            font-size: 0.9375rem;
            font-weight: 600;
        }

        /* Contract Body */
        .contract-body {
            padding: 48px;
        }

        .contract-parties {
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 40px;
            border: 1px solid #e0e7ff;
            position: relative;
        }

        .contract-parties::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 24px;
            right: 24px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .contract-parties-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .contract-parties-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 20px;
            align-items: center;
        }

        .contract-party {
            padding: 16px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .contract-party-role {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #8b5cf6;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .contract-party-name {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.4;
        }

        .contract-party-vs {
            font-size: 0.875rem;
            color: #94a3b8;
            font-weight: 600;
            padding: 8px;
        }

        /* Deal Summary Card */
        .contract-summary {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .contract-summary::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.3) 0%, transparent 60%);
            border-radius: 50%;
        }

        .contract-summary-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .contract-summary-title::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #8b5cf6;
            border-radius: 50%;
            box-shadow: 0 0 10px #8b5cf6;
        }

        .contract-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            position: relative;
            z-index: 2;
        }

        .contract-summary-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 16px;
            backdrop-filter: blur(10px);
        }

        .contract-summary-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .contract-summary-value {
            font-size: 1rem;
            font-weight: 700;
            color: white;
        }

        .contract-summary-value.highlight {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.25rem;
        }

        /* Section */
        .contract-section {
            margin-bottom: 36px;
            position: relative;
        }

        .contract-section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f1f5f9;
            position: relative;
        }

        .contract-section-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, #8b5cf6, #06b6d4);
        }

        .contract-section-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #8b5cf6, #06b6d4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.875rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .contract-section-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.01em;
        }

        .contract-section-number {
            font-size: 0.6875rem;
            color: #8b5cf6;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-right: auto;
            margin-left: auto;
        }

        .contract-text {
            color: #334155;
            margin-bottom: 12px;
            line-height: 1.8;
        }

        .contract-clause {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            padding: 12px 16px;
            background: #f8fafc;
            border-left: 3px solid #8b5cf6;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .contract-clause:hover {
            background: #f1f5f9;
            transform: translateX(4px);
        }

        .contract-clause-number {
            font-weight: 700;
            color: #8b5cf6;
            flex-shrink: 0;
            font-size: 0.875rem;
            min-width: 32px;
        }

        .contract-clause-text {
            color: #334155;
            flex: 1;
        }

        /* Highlight Block */
        .contract-highlight {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: 20px 24px;
            margin: 24px 0;
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .contract-highlight-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .contract-highlight-content {
            flex: 1;
        }

        .contract-highlight-title {
            font-weight: 700;
            color: #92400e;
            margin-bottom: 4px;
            font-size: 0.9375rem;
        }

        .contract-highlight-text {
            color: #78350f;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        /* Info Block */
        .contract-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 4px solid #3b82f6;
            border-radius: 12px;
            padding: 20px 24px;
            margin: 24px 0;
            display: flex;
            gap: 16px;
        }

        .contract-info-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .contract-info-content { flex: 1; }
        .contract-info-title {
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 4px;
            font-size: 0.9375rem;
        }

        .contract-info-text {
            color: #1e3a8a;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        /* Signatures */
        .contract-signatures {
            margin-top: 48px;
            padding-top: 32px;
            border-top: 2px dashed #e2e8f0;
        }

        .contract-signatures-title {
            text-align: center;
            font-size: 0.875rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            margin-bottom: 32px;
        }

        .contract-signatures-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .contract-signature {
            text-align: center;
        }

        .contract-signature-party {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .contract-signature-name {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 24px;
        }

        .contract-signature-line {
            border-top: 2px solid #0f172a;
            margin-bottom: 8px;
            padding-top: 8px;
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Requisites Table */
        .contract-requisites {
            margin-top: 24px;
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
        }

        .contract-requisites-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .contract-requisites-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .contract-requisites-col h5 {
            font-size: 0.875rem;
            color: #0f172a;
            font-weight: 700;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .contract-requisites-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 0.8125rem;
            gap: 8px;
        }

        .contract-requisites-label {
            color: #64748b;
            font-weight: 500;
        }

        .contract-requisites-value {
            color: #0f172a;
            font-weight: 600;
            text-align: right;
        }

        /* Contract Footer */
        .contract-footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 24px 48px;
            text-align: center;
            font-size: 0.75rem;
            line-height: 1.6;
        }

        .contract-footer-brand {
            font-size: 0.875rem;
            font-weight: 700;
            background: linear-gradient(135deg, #8b5cf6, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .contract-footer-text {
            opacity: 0.8;
        }

        /* ===== КЛАССИЧЕСКИЙ БЛАНК ДОГОВОРА (официальный вид) ===== */
        .contract-box.classic {
            font-family: 'Times New Roman', 'Liberation Serif', Georgia, serif;
            font-size: 0.9375rem;
            line-height: 1.65;
            color: #111827;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }

        .contract-box.classic::-webkit-scrollbar-thumb {
            background: #94a3b8;
        }

        .cls-doc {
            padding: 48px 56px 40px;
            max-width: 900px;
            margin: 0 auto;
        }

        .cls-title {
            text-align: center;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .cls-number {
            text-align: center;
            font-size: 1rem;
            font-weight: 700;
            margin-top: 4px;
        }

        .cls-place {
            display: flex;
            justify-content: space-between;
            margin: 28px 0 24px;
            font-size: 0.9375rem;
        }

        .cls-rule {
            border: 0;
            border-top: 1px solid #111827;
            margin: 0 0 24px;
        }

        .cls-preamble {
            text-align: justify;
            text-indent: 2em;
            margin-bottom: 18px;
        }

        .cls-basis {
            border: 1px solid #cbd5e1;
            border-left: 4px solid #334155;
            background: #f8fafc;
            padding: 12px 16px;
            margin-bottom: 26px;
            font-size: 0.84rem;
            line-height: 1.6;
            color: #334155;
        }

        .cls-basis-title {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-size: 0.75rem;
            margin-bottom: 6px;
            color: #0f172a;
        }

        .cls-basis ul {
            margin: 0;
            padding-left: 18px;
        }

        .cls-section {
            margin-bottom: 22px;
            break-inside: avoid;
        }

        .cls-section-title {
            text-align: center;
            font-size: 0.9375rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 10px;
        }

        .cls-clause {
            margin: 0 0 8px;
            text-align: justify;
            text-indent: 2em;
        }

        .cls-clause-num {
            font-weight: 700;
            margin-right: 4px;
        }

        .cls-list {
            margin: 4px 0 8px 2em;
            padding: 0;
            list-style: none;
        }

        .cls-list li {
            margin-bottom: 4px;
            text-align: justify;
        }

        .cls-sign-title {
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 34px 0 16px;
        }

        .cls-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .cls-table td {
            vertical-align: top;
            width: 50%;
            padding: 10px 14px;
            border: 1px solid #94a3b8;
        }

        .cls-table td.no-border {
            border: 0;
            padding: 10px 14px 10px 0;
        }

        .cls-party-title {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8125rem;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .cls-req-row {
            margin-bottom: 3px;
        }

        .cls-sign-line {
            margin-top: 26px;
            border-top: 1px solid #111827;
            padding-top: 4px;
            font-size: 0.75rem;
            color: #475569;
        }

        .cls-stamp {
            margin-top: 10px;
            font-size: 0.75rem;
            color: #64748b;
        }

        .cls-footnote {
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid #cbd5e1;
            font-size: 0.75rem;
            color: #64748b;
            text-align: center;
        }

        @media (max-width: 768px) {
            .cls-doc { padding: 28px 20px; }
            .cls-place { flex-direction: column; gap: 6px; }
            .cls-table, .cls-table tbody, .cls-table tr, .cls-table td { display: block; width: 100%; }
            .cls-table td { margin-bottom: 10px; }
        }

        /* Legacy for markdown support */
        .contract-box h1,
        .contract-box h2,
        .contract-box h3,
        .contract-box p,
        .contract-box ul,
        .contract-box li,
        .contract-box table,
        .contract-box td,
        .contract-box th {
            /* Reset if markdown renders */
        }

        /* Summary */
        .summary {
            background: var(--color-surface-elevated);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 32px;
            font-size: 0.875rem;
            line-height: 2.2;
            color: var(--text-secondary);
            border: 1px solid var(--color-border);
            position: relative;
            overflow: hidden;
        }

        .summary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-brand);
        }

        .summary strong {
            color: var(--text-primary);
            font-weight: 700;
        }

        .ai-analysis {
            background: linear-gradient(145deg, rgba(139, 92, 246, 0.1), rgba(99, 102, 241, 0.05));
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-bottom: 32px;
            position: relative;
        }

        .ai-analysis::before {
            content: '🤖';
            position: absolute;
            top: -10px;
            right: 20px;
            font-size: 1.5rem;
            opacity: 0.5;
        }

        .ai-analysis h4 {
            color: var(--brand-primary);
            font-size: 0.875rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ai-analysis .detected {
            color: var(--text-secondary);
            font-size: 0.875rem;
            line-height: 1.8;
        }

        .ai-analysis .detected strong {
            color: var(--text-primary);
            font-weight: 600;
        }

        .setup-box {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-bottom: 24px;
            font-size: 0.8125rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .setup-box code {
            background: var(--color-surface-elevated);
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            font-family: 'JetBrains Mono', monospace;
            color: var(--brand-secondary);
            font-size: 0.75rem;
            border: 1px solid var(--color-border);
            display: block;
            margin: 12px 0;
        }

        .toggle-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .toggle-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
            accent-color: var(--brand-primary);
            cursor: pointer;
        }

        .toggle-row label {
            color: var(--text-secondary);
            font-weight: 500;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .legal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 32px;
        }

        .legal-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 20px;
            transition: all var(--transition-base);
            position: relative;
            overflow: hidden;
        }

        .legal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gradient-brand);
            opacity: 0;
            transition: opacity var(--transition-fast);
        }

        .legal-card:hover {
            border-color: var(--brand-primary);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.2);
            transform: translateY(-2px);
        }

        .legal-card:hover::before { opacity: 1; }

        .legal-card h4 {
            color: var(--text-muted);
            font-size: 0.75rem;
            margin-bottom: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legal-card .field { margin-bottom: 16px; }
        .legal-card .field:last-child { margin-bottom: 0; }

        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--color-surface-elevated);
            color: var(--text-primary);
            padding: 16px 24px;
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            font-weight: 500;
            transform: translateY(80px) scale(0.95);
            opacity: 0;
            transition: all var(--transition-base);
            z-index: 2000;
            max-width: 400px;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid var(--color-border);
            backdrop-filter: blur(10px);
        }

        .toast.show {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .toast.success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-color: rgba(16, 185, 129, 0.3);
        }

        .toast.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .toast.warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #0a0a0f;
            border-color: rgba(245, 158, 11, 0.3);
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1500;
            padding: 24px;
            backdrop-filter: blur(8px);
        }

        .modal.active { display: flex; }

        .modal-content {
            background: var(--gradient-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            max-width: 640px;
            width: 100%;
            max-height: 85vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-lg);
            animation: modalIn 0.3s ease-out;
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--color-surface);
        }

        .modal-title {
            color: var(--text-primary);
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            transition: all var(--transition-fast);
        }

        .modal-close:hover {
            color: var(--text-primary);
            background: var(--color-surface-elevated);
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--color-border);
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            background: var(--color-surface);
        }

        .history-item {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-bottom: 16px;
            font-size: 0.875rem;
            transition: all var(--transition-fast);
            cursor: pointer;
        }

        .history-item:hover {
            border-color: var(--brand-primary);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
        }

        .history-item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .history-item-title {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.9375rem;
        }

        .history-item-date {
            color: var(--text-muted);
            font-size: 0.75rem;
            background: var(--color-surface-elevated);
            padding: 4px 10px;
            border-radius: var(--radius-sm);
        }

        .history-item-amount {
            color: var(--brand-success);
            margin-bottom: 12px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .history-item-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--color-surface-elevated);
            padding: 6px 12px;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid var(--color-border);
            color: var(--brand-secondary);
        }

        .chip.success {
            background: rgba(16, 185, 129, 0.15);
            border-color: var(--brand-success);
            color: var(--brand-success);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 12px 16px;
                flex-wrap: wrap;
                gap: 12px;
            }

            .header-nav { gap: 8px; }
            .header-link { font-size: 0.75rem; }

            .grid-2,
            .legal-grid {
                grid-template-columns: 1fr;
            }

            .contract-hero { padding: 24px; }
            .contract-title { font-size: 1.5rem; }
            .contract-body { padding: 24px; }
            .contract-parties-grid { grid-template-columns: 1fr; gap: 12px; }
            .contract-signatures-grid { grid-template-columns: 1fr; gap: 32px; }
            .contract-requisites-grid { grid-template-columns: 1fr; }
            .contract-summary-grid { grid-template-columns: 1fr; }
            .contract-footer { padding: 20px 24px; }

            .page { padding: 24px; }

            .btn {
                padding: 12px 20px;
                font-size: 0.875rem;
            }

            .step-label { display: none; }
            .step-dot {
                width: 36px;
                height: 36px;
                font-size: 0.75rem;
            }
            .step-line { width: 40px; }

            .result-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .result-actions {
                width: 100%;
                justify-content: center;
            }

            .profile-info { display: none; }
            .profile-btn { padding: 6px; }

            .theme-btn span { display: none; }
        }

        @media (max-width: 480px) {
            .header-logo {
                width: 36px;
                height: 36px;
                font-size: 1.25rem;
            }

            .header-title { font-size: 1rem; }
            .page { padding: 20px; }
            .page-title { font-size: 1.25rem; }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .btn-next { margin-left: 0; }
            .nav { flex-direction: column; }
        }

        /* ===== ПЕЧАТЬ: на бумагу уходит только сам договор ===== */
        @media print {
            body {
                background: #fff !important;
                color: #000 !important;
            }

            .header,
            .progress,
            .main > .page,
            .loader,
            .result-actions,
            .result-header,
            .ai-badge,
            .modal,
            .toast,
            .theme-switcher,
            .profile-wrapper,
            .bg-gradient,
            .bg-orb {
                display: none !important;
            }

            .main { padding: 0 !important; max-width: none !important; }

            .result {
                display: block !important;
                opacity: 1 !important;
                transform: none !important;
            }

            .contract-box {
                max-height: none !important;
                overflow: visible !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .cls-doc { padding: 0 !important; }
            .cls-section { page-break-inside: avoid; }
            .cls-table { page-break-inside: avoid; }
        }

        .hidden { display: none !important; }

        :focus-visible {
            outline: 2px solid var(--brand-primary);
            outline-offset: 2px;
        }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--color-surface); }
        ::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 4px;
            border: 2px solid var(--color-surface);
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--brand-primary); }
    </style>
</head>
<body>
@php
    $currentUser = auth()->user();
    $userName = $currentUser?->name ?: __('generate.default_user');
    $userEmail = $currentUser?->email ?: 'email@example.com';
    $nameParts = preg_split('/\s+/u', trim($userName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $userInitials = collect($nameParts)->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    $userInitials = $userInitials ?: __('generate.default_initial');
@endphp

    <!-- Background -->
<div class="bg-gradient"></div>
<div class="bg-orb"></div>
<div class="bg-orb"></div>

<!-- Header -->
<header class="header">
    <div class="header-brand">
        <div class="header-logo">⚖️</div>
        <div>
            <h1 class="header-title">{{ __('generate.header_title') }}<span class="header-badge">PRO v5.0</span></h1>
        </div>
    </div>

    <nav class="header-nav">
        <a href="{{ route('welcome') }}" class="header-link">{{ __('generate.nav_home') }}</a>
        <a href="{{ route('dashboard') }}" class="header-link">{{ __('generate.nav_dashboard') }}</a>

        <!-- Theme Switcher -->
        <div class="theme-switcher" id="themeSwitcher">
            <button class="theme-btn" id="themeBtn" onclick="toggleThemeDropdown(event)">
                <i class="fas fa-palette"></i>
                <span>{{ __('generate.theme') }}</span>
            </button>
            <div class="theme-dropdown" id="themeDropdown">
                <div class="theme-option active" data-theme="dark" onclick="setTheme('dark')">
                    <div class="theme-preview dark"></div>
                    <span>Dark Purple</span>
                </div>
                <div class="theme-option" data-theme="startup" onclick="setTheme('startup')">
                    <div class="theme-preview startup"></div>
                    <span>Startup Neon</span>
                </div>
                <div class="theme-option" data-theme="aurora" onclick="setTheme('aurora')">
                    <div class="theme-preview aurora"></div>
                    <span>Aurora Sky</span>
                </div>
                <div class="theme-option" data-theme="grid" onclick="setTheme('grid')">
                    <div class="theme-preview grid"></div>
                    <span>Cyber Grid</span>
                </div>
                <div class="theme-option" data-theme="light" onclick="setTheme('light')">
                    <div class="theme-preview light"></div>
                    <span>Light Mode</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Profile -->
    <div class="profile-wrapper" id="profileContainer">
        <button class="profile-btn" id="profileButton" aria-label="{{ __('generate.profile_aria') }}" aria-expanded="false" onclick="toggleProfile()">
            <div class="profile-avatar">{{ $userInitials }}</div>
            <div class="profile-info">
                <span class="profile-name">{{ $userName }}</span>
                <span class="profile-email">{{ $userEmail }}</span>
            </div>
            <svg class="profile-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div class="profile-dropdown" id="profileDropdown">
            <div class="profile-dropdown-header">
                <div class="profile-dropdown-avatar">{{ $userInitials }}</div>
                <div class="profile-dropdown-info">
                    <div class="profile-dropdown-name">{{ $userName }}</div>
                    <div class="profile-dropdown-email">
                        <i class="fas fa-envelope"></i>
                        {{ $userEmail }}
                    </div>
                </div>
            </div>

            <div class="profile-dropdown-menu">
                <a href="{{ route('profile.edit') }}" class="profile-dropdown-item">
                    <i class="fas fa-user"></i>
                    <span>{{ __('generate.profile') }}</span>
                </a>
                <a href="#" class="profile-dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>{{ __('generate.settings') }}</span>
                </a>
                <a href="#" class="profile-dropdown-item">
                    <i class="fas fa-file-alt"></i>
                    <span>{{ __('generate.my_documents') }}</span>
                </a>
            </div>

            <div class="profile-dropdown-menu">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="profile-dropdown-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>{{ __('generate.logout') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<!-- Progress -->
<div class="progress" id="progressBar"></div>

<!-- Main -->
<main class="main">
    <!-- Step 1 -->
    <section class="page active" data-step="1">
        <div class="page-header">
            <div class="page-icon">✍️</div>
            <div>
                <h2 class="page-title">{{ __('generate.step1_title') }}</h2>
                <p class="page-subtitle">{{ __('generate.step1_subtitle') }}</p>
            </div>
        </div>

        <div class="field">
            <label class="field-label">
                {{ __('generate.deal_essence') }}
                <span class="field-required">*</span>
            </label>
            <textarea
                id="f_desc"
                class="field-textarea"
                placeholder="{{ __('generate.deal_placeholder') }}"
                maxlength="1000"
            ></textarea>
            <div class="field-hint">
                <i class="fas fa-lightbulb"></i>
                {{ __('generate.deal_hint') }}
            </div>
        </div>

        <div class="setup-box">
            <strong>{{ __('generate.ai_settings') }}</strong>
            <code>OLLAMA_ORIGINS="*" ollama serve</code>
            <div class="toggle-row">
                <input type="checkbox" id="useOllama" checked>
                <label for="useOllama">{{ __('generate.use_local_ollama') }}</label>
            </div>
            <div class="toggle-row">
                <select id="aiModel" class="field-select" style="width: auto; padding: 6px 12px; font-size: 0.8125rem;">
                    <option value="llama3.1:8b">{{ __('generate.model_fast') }}</option>
                    <option value="llama3">{{ __('generate.model_quality') }}</option>
                    <option value="mixtral">{{ __('generate.model_max') }}</option>
                </select>
                <span style="color: var(--text-muted); font-size: 0.75rem;">{{ __('generate.analysis_model') }}</span>
            </div>
        </div>

        <div class="nav">
            <div></div>
            <button class="btn btn-next" onclick="analyzeAndNext()">
                <i class="fas fa-bolt"></i>
                {{ __('generate.analyze_and_continue') }}
            </button>
        </div>
    </section>

    <!-- Step 2 -->
    <section class="page" data-step="2">
        <div class="page-header">
            <div class="page-icon">🔍</div>
            <div>
                <h2 class="page-title">{{ __('generate.step2_title') }}</h2>
                <p class="page-subtitle">{{ __('generate.step2_subtitle') }}</p>
            </div>
        </div>

        <div class="ai-analysis" id="aiAnalysis">
            <h4><i class="fas fa-robot"></i> {{ __('generate.recognized_by_ai') }}</h4>
            <div class="detected" id="detectedInfo"></div>
        </div>

        <div class="grid-2">
            <div class="field">
                <label class="field-label">{{ __('generate.contract_type') }}</label>
                <select id="f_type" class="field-select">
                    <option value="оказания услуг">{{ __('generate.type_services') }}</option>
                    <option value="подряда">{{ __('generate.type_work') }}</option>
                    <option value="аренды">{{ __('generate.type_lease') }}</option>
                    <option value="купли-продажи">{{ __('generate.type_sale') }}</option>
                    <option value="поставки">{{ __('generate.type_supply') }}</option>
                    <option value="лицензионный">{{ __('generate.type_license') }}</option>
                </select>
            </div>
            <div class="field">
                <label class="field-label">{{ __('generate.amount_label') }}</label>
                <input type="number" id="f_amount" class="field-input" placeholder="300000" min="0" inputmode="numeric">
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
                <label class="field-label">
                    {{ __('generate.party1') }}
                    <span class="field-required">*</span>
                </label>
                <input type="text" id="f_p1" class="field-input" placeholder="{{ __('generate.p1_placeholder') }}" maxlength="100">
            </div>
            <div class="field">
                <label class="field-label">
                    {{ __('generate.party2') }}
                    <span class="field-required">*</span>
                </label>
                <input type="text" id="f_p2" class="field-input" placeholder="{{ __('generate.p2_placeholder') }}" maxlength="100">
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
                <label class="field-label">{{ __('generate.role_party1') }}</label>
                <select id="f_role1" class="field-select">
                    <option value="Заказчик">{{ __('generate.role_customer') }}</option>
                    <option value="Покупатель">{{ __('generate.role_buyer') }}</option>
                    <option value="Арендатор">{{ __('generate.role_tenant') }}</option>
                    <option value="Лицензиат">{{ __('generate.role_licensee') }}</option>
                </select>
            </div>
            <div class="field">
                <label class="field-label">{{ __('generate.role_party2') }}</label>
                <select id="f_role2" class="field-select">
                    <option value="Исполнитель">{{ __('generate.role_contractor') }}</option>
                    <option value="Продавец">{{ __('generate.role_seller') }}</option>
                    <option value="Арендодатель">{{ __('generate.role_landlord') }}</option>
                    <option value="Лицензиар">{{ __('generate.role_licensor') }}</option>
                </select>
            </div>
        </div>

        <div class="field">
            <label class="field-label">{{ __('generate.contract_subject') }}</label>
            <textarea id="f_subject" class="field-textarea" placeholder="{{ __('generate.subject_placeholder') }}" maxlength="500" style="min-height: 80px;"></textarea>
        </div>

        <div class="nav">
            <button class="btn btn-back" onclick="goStep(1)">
                <i class="fas fa-arrow-left"></i>
                {{ __('generate.back') }}
            </button>
            <button class="btn btn-next" onclick="goStep(3)">
                {{ __('generate.continue') }}
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </section>

    <!-- Step 3 -->
    <section class="page" data-step="3">
        <div class="page-header">
            <div class="page-icon">⚖️</div>
            <div>
                <h2 class="page-title">{{ __('generate.step3_title') }}</h2>
                <p class="page-subtitle">{{ __('generate.step3_subtitle') }}</p>
            </div>
        </div>

        <div class="legal-grid">
            <div class="legal-card">
                <h4><i class="fas fa-coins"></i> {{ __('generate.payment') }}</h4>
                <div class="field">
                    <input type="text" id="f_pay" class="field-input" placeholder="{{ __('generate.pay_placeholder') }}" maxlength="100">
                </div>
                <div class="field">
                    <select id="f_vat" class="field-select">
                        <option value="не указано">{{ __('generate.vat_none') }}</option>
                        <option value="в т.ч. НДС 20%">{{ __('generate.vat_incl') }}</option>
                        <option value="без НДС">{{ __('generate.vat_without') }}</option>
                    </select>
                </div>
                <div class="field-hint">{{ __('generate.payment_hint') }}</div>
            </div>

            <div class="legal-card">
                <h4><i class="fas fa-calendar"></i> {{ __('generate.terms') }}</h4>
                <div class="field">
                    <input type="text" id="f_duration" class="field-input" placeholder="{{ __('generate.duration_placeholder') }}" maxlength="50">
                </div>
                <div class="field">
                    <input type="date" id="f_date" class="field-input">
                </div>
                <div class="field-hint">{{ __('generate.terms_hint') }}</div>
            </div>

            <div class="legal-card">
                <h4><i class="fas fa-shield-alt"></i> {{ __('generate.security') }}</h4>
                <div class="field">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--text-secondary);">
                        <input type="checkbox" id="f_conf" style="width: 16px; height: 16px; margin: 0; accent-color: var(--brand-primary);">
                        <span>{{ __('generate.confidentiality') }}</span>
                    </label>
                </div>
                <div class="field">
                    <input type="text" id="f_penalty" class="field-input" placeholder="{{ __('generate.penalty_placeholder') }}" value="0.1%" maxlength="30">
                </div>
                <div class="field-hint">{{ __('generate.security_hint') }}</div>
            </div>

            <div class="legal-card">
                <h4><i class="fas fa-gavel"></i> {{ __('generate.disputes') }}</h4>
                <div class="field">
                    <select id="f_dispute" class="field-select">
                        <option value="экономический суд">{{ __('generate.dispute_economic') }}</option>
                        <option value="арбитражный суд">{{ __('generate.dispute_arbitration') }}</option>
                        <option value="третейский суд">{{ __('generate.dispute_tribunal') }}</option>
                        <option value="переговоры">{{ __('generate.dispute_negotiation') }}</option>
                    </select>
                </div>
                <div class="field">
                    <input type="text" id="f_city" class="field-input" placeholder="{{ __('generate.city_placeholder') }}" value="{{ __('generate.city_default') }}" maxlength="50">
                </div>
                <div class="field-hint">{{ __('generate.disputes_hint') }}</div>
            </div>
        </div>

        <div class="field">
            <label class="field-label">{{ __('generate.special_terms') }}</label>
            <textarea id="f_extra" class="field-textarea" placeholder="{{ __('generate.extra_placeholder') }}" maxlength="300" style="min-height: 80px;"></textarea>
        </div>

        <div class="nav">
            <button class="btn btn-back" onclick="goStep(2)">
                <i class="fas fa-arrow-left"></i>
                {{ __('generate.back') }}
            </button>
            <button class="btn btn-next" onclick="goStep(4)">
                {{ __('generate.continue') }}
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </section>

    <!-- Step 4 -->
    <section class="page" data-step="4">
        <div class="page-header">
            <div class="page-icon">✅</div>
            <div>
                <h2 class="page-title">{{ __('generate.step4_title') }}</h2>
                <p class="page-subtitle">{{ __('generate.step4_subtitle') }}</p>
            </div>
        </div>

        <div class="summary" id="summary"></div>

        <!-- Выбор оформления договора -->
        <div class="style-picker-block">
            <div class="style-picker-head">
                <span class="style-picker-title">{{ __('generate.style_title') }}</span>
                <span class="style-picker-hint">{{ __('generate.style_hint') }}</span>
            </div>
            <div class="style-picker" role="radiogroup" aria-label="{{ __('generate.style_title') }}">
                <button type="button" class="style-card selected" id="styleCard_classic" data-style="classic"
                        role="radio" aria-checked="true" onclick="selectStyle('classic')">
                    <span class="style-card-check"><i class="fas fa-check"></i></span>
                    <span class="style-preview style-preview-classic">
                        <span class="sp-line sp-title"></span>
                        <span class="sp-line"></span>
                        <span class="sp-line sp-mid"></span>
                        <span class="sp-line"></span>
                        <span class="sp-line sp-short"></span>
                    </span>
                    <span class="style-card-name"><i class="fas fa-scroll"></i> {{ __('generate.style_classic_name') }}</span>
                    <span class="style-card-desc">{{ __('generate.style_classic_desc') }}</span>
                    <span class="style-card-tags">
                        <span class="style-tag">{{ __('generate.style_tag_rt') }}</span>
                        <span class="style-tag">{{ __('generate.style_tag_print') }}</span>
                    </span>
                </button>

                <button type="button" class="style-card" id="styleCard_modern" data-style="modern"
                        role="radio" aria-checked="false" onclick="selectStyle('modern')">
                    <span class="style-card-check"><i class="fas fa-check"></i></span>
                    <span class="style-preview style-preview-modern">
                        <span class="sp-line sp-title"></span>
                        <span class="sp-line sp-mid"></span>
                        <span class="sp-line"></span>
                        <span class="sp-line sp-short"></span>
                        <span class="sp-line sp-mid"></span>
                    </span>
                    <span class="style-card-name"><i class="fas fa-wand-magic-sparkles"></i> {{ __('generate.style_modern_name') }}</span>
                    <span class="style-card-desc">{{ __('generate.style_modern_desc') }}</span>
                    <span class="style-card-tags">
                        <span class="style-tag">{{ __('generate.style_tag_animated') }}</span>
                        <span class="style-tag">{{ __('generate.style_tag_screen') }}</span>
                    </span>
                </button>
            </div>
        </div>

        <div class="setup-box">
            <strong>{{ __('generate.tip_label') }}</strong> {{ __('generate.tip_text') }}
        </div>

        <button class="btn btn-generate" id="genBtn" onclick="generate()">
            <i class="fas fa-magic"></i>
            {{ __('generate.generate_contract') }}
        </button>

        <div class="nav" style="margin-top: 16px;">
            <button class="btn btn-back" onclick="goStep(3)">
                <i class="fas fa-arrow-left"></i>
                {{ __('generate.change_terms') }}
            </button>
        </div>
    </section>

    <!-- Loader -->
    <div class="loader" id="loader">
        <div class="spinner"></div>
        <div class="loader-text">{{ __('generate.loader_text') }}</div>
        <span class="loader-subtext">{{ __('generate.loader_subtext') }}</span>
    </div>

    <!-- Result -->
    <div class="result" id="result">
        <div class="result-header">
            <h2 class="result-title">
                <i class="fas fa-file-contract"></i>
                {{ __('generate.contract_ready') }}
            </h2>
            <div class="result-actions">
                <button class="btn-sm" onclick="copyText()">
                    <i class="fas fa-copy"></i>
                    {{ __('generate.copy') }}
                </button>
                <button class="btn-sm" onclick="window.print()">
                    <i class="fas fa-print"></i>
                    {{ __('generate.print') }}
                </button>
                <button class="btn-sm" onclick="exportToPdf()">
                    <i class="fas fa-file-pdf"></i>
                    PDF
                </button>
                <button class="btn-sm" onclick="exportToDocx()">
                    <i class="fas fa-file-word"></i>
                    DOCX
                </button>
                <button class="btn-sm" onclick="downloadTxt()">
                    <i class="fas fa-file-alt"></i>
                    TXT
                </button>
                <button class="btn-sm" onclick="showHistoryModal()">
                    <i class="fas fa-history"></i>
                    {{ __('generate.history') }}
                </button>
                <button class="btn-sm" onclick="startOver()">
                    <i class="fas fa-redo"></i>
                    {{ __('generate.start_over') }}
                </button>
            </div>
        </div>
        <div class="ai-badge" id="aiBadge">
            <i class="fas fa-circle-notch fa-spin"></i>
            <span id="aiBadgeText">{{ __('generate.ai_polishing') }}</span>
        </div>
        <div class="contract-box" id="contractOutput"></div>
    </div>
</main>

<!-- History Modal -->
<div class="modal" id="historyModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">{{ __('generate.history_modal_title') }}</h3>
            <button class="modal-close" onclick="closeHistoryModal()" aria-label="{{ __('generate.close') }}">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="historyList"></div>
        <div class="modal-footer">
            <button class="btn-sm btn-sm-danger" onclick="clearAllHistory()">
                <i class="fas fa-trash"></i>
                {{ __('generate.clear_all') }}
            </button>
            <button class="btn-sm" onclick="closeHistoryModal()">{{ __('generate.close') }}</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast" role="alert" aria-live="polite"></div>

<script>
    // ===== I18N (server-driven, follows the app locale) =====
    const T = @json(__('generate.js'));
    const tr = (str, params = {}) => Object.entries(params).reduce((acc, [k, v]) => acc.split(':' + k).join(v), str || '');
    const typeName = t => (T.types && T.types[t]) || t || T.doc_type_default;
    const roleName = r => (T.roles && T.roles[r]) || r || '';
    const roleGen = r => (T.roles_gen && T.roles_gen[r]) || roleName(r);
    const roleDat = r => (T.roles_dat && T.roles_dat[r]) || roleName(r);
    const vatName = v => (T.vat && T.vat[v]) || v || '';
    const disputeName = v => (T.dispute && T.dispute[v]) || v || '';

    // ===== Configuration =====
    const CONFIG = {
        OLLAMA_URL: 'http://localhost:11434/api/chat',
        OLLAMA_TAGS_URL: 'http://localhost:11434/api/tags',
        HISTORY_KEY: 'contract_builder_history_v5',
        API_CACHE_KEY: 'contract_builder_api_cache_v2',
        FORM_CACHE_KEY: 'contract_builder_form_state_v1',
        THEME_KEY: 'contract_builder_theme_v1',
        STYLE_KEY: 'contract_builder_style_v1',
        MAX_HISTORY: 10,
        MAX_API_CACHE: 20,
        API_CACHE_TTL: 24 * 60 * 60 * 1000,
        TOTAL_STEPS: 5
    };
    const CABINET_ACTIVITY_URL = '{{ route('cabinet.activity.store') }}';

    async function recordCabinetActivity(payload) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) return;

        try {
            await fetch(CABINET_ACTIVITY_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(payload)
            });
        } catch (e) {
            console.warn('Cabinet history error:', e);
        }
    }

    let currentStep = 1;
    let contractText = '';
    let contractHtml = '';
    let requestCache = new Map();
    let ollamaStatus = 'unknown';
    let contractStyle = 'classic';

    // ===== Стиль оформления договора (классический / с анимацией) =====
    function selectStyle(style, silent = false) {
        if (style !== 'classic' && style !== 'modern') style = 'classic';
        contractStyle = style;

        document.querySelectorAll('.style-card').forEach(card => {
            const isActive = card.dataset.style === style;
            card.classList.toggle('selected', isActive);
            card.setAttribute('aria-checked', isActive ? 'true' : 'false');
        });

        try { localStorage.setItem(CONFIG.STYLE_KEY, style); } catch (e) { /* noop */ }

        const summaryStyle = document.getElementById('summaryStyle');
        if (summaryStyle) summaryStyle.textContent = styleName(style);

        if (!silent) toast(tr(T.ok_style_selected, { style: styleName(style) }), 'success');
    }

    const styleName = s => (T.styles && T.styles[s]) || s;

    // ===== Theme System =====
    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(CONFIG.THEME_KEY, theme);

        document.querySelectorAll('.theme-option').forEach(opt => {
            opt.classList.toggle('active', opt.dataset.theme === theme);
        });

        toast(tr(T.theme_activated, { name: getThemeName(theme) }), 'success');
    }

    function getThemeName(theme) {
        const names = {
            dark: 'Dark Purple',
            startup: 'Startup Neon',
            aurora: 'Aurora Sky',
            grid: 'Cyber Grid',
            light: 'Light Mode'
        };
        return names[theme] || theme;
    }

    function toggleThemeDropdown(e) {
        e?.stopPropagation();
        const dropdown = document.getElementById('themeDropdown');
        dropdown.classList.toggle('show');
    }

    function loadTheme() {
        const saved = localStorage.getItem(CONFIG.THEME_KEY) || 'dark';
        document.documentElement.setAttribute('data-theme', saved);
        document.querySelectorAll('.theme-option').forEach(opt => {
            opt.classList.toggle('active', opt.dataset.theme === saved);
        });
    }

    // Close dropdowns on outside click
    document.addEventListener('click', (e) => {
        const themeSwitcher = document.getElementById('themeSwitcher');
        const themeDropdown = document.getElementById('themeDropdown');
        if (themeSwitcher && themeDropdown && !themeSwitcher.contains(e.target)) {
            themeDropdown.classList.remove('show');
        }

        const profileContainer = document.getElementById('profileContainer');
        const profileDropdown = document.getElementById('profileDropdown');
        if (profileContainer && profileDropdown && !profileContainer.contains(e.target)) {
            profileDropdown.classList.remove('show');
            document.getElementById('profileButton')?.setAttribute('aria-expanded', 'false');
        }
    });

    // ===== Simple Hash =====
    function simpleHash(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return 'h_' + Math.abs(hash).toString(36);
    }

    // ===== API Cache =====
    function loadApiCache() {
        try {
            const data = JSON.parse(localStorage.getItem(CONFIG.API_CACHE_KEY) || '{}');
            const now = Date.now();
            Object.entries(data).forEach(([key, val]) => {
                if (now - val.ts < CONFIG.API_CACHE_TTL) {
                    requestCache.set(key, val);
                }
            });
        } catch (e) {
            console.warn('API cache load error:', e);
        }
    }

    function saveApiCache() {
        try {
            const entries = Array.from(requestCache.entries())
                .sort((a, b) => b[1].ts - a[1].ts)
                .slice(0, CONFIG.MAX_API_CACHE);
            const obj = Object.fromEntries(entries);
            localStorage.setItem(CONFIG.API_CACHE_KEY, JSON.stringify(obj));
        } catch (e) {
            localStorage.removeItem(CONFIG.API_CACHE_KEY);
        }
    }

    // ===== Form Cache =====
    const FORM_FIELDS = ['f_desc', 'f_type', 'f_p1', 'f_p2', 'f_role1', 'f_role2', 'f_amount', 'f_pay', 'f_vat', 'f_duration', 'f_date', 'f_city', 'f_extra', 'f_penalty', 'f_dispute', 'useOllama', 'aiModel', 'f_conf', 'f_subject'];

    let saveFormTimeout;
    function debounceSaveForm() {
        clearTimeout(saveFormTimeout);
        saveFormTimeout = setTimeout(saveFormState, 500);
    }

    function saveFormState() {
        try {
            const state = {};
            FORM_FIELDS.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                state[id] = el.type === 'checkbox' ? el.checked : el.value;
            });
            localStorage.setItem(CONFIG.FORM_CACHE_KEY, JSON.stringify(state));
        } catch (e) {
            console.warn('Form save error:', e);
        }
    }

    function restoreFormState() {
        try {
            const state = JSON.parse(localStorage.getItem(CONFIG.FORM_CACHE_KEY));
            if (!state) return false;
            Object.entries(state).forEach(([id, val]) => {
                const el = document.getElementById(id);
                if (!el) return;
                if (el.type === 'checkbox') el.checked = val;
                else if (val) el.value = val;
            });
            return true;
        } catch (e) {
            return false;
        }
    }

    // ===== Profile Toggle =====
    function toggleProfile() {
        const dropdown = document.getElementById('profileDropdown');
        const button = document.getElementById('profileButton');
        if (!dropdown || !button) return;
        const isOpen = dropdown.classList.toggle('show');
        button.setAttribute('aria-expanded', isOpen);
    }

    // ===== Progress Bar =====
    function buildProgress() {
        const steps = T.steps.map((label, i) => ({ n: i + 1, l: label }));

        const bar = document.getElementById('progressBar');
        let html = '';
        steps.forEach((s, i) => {
            if (i > 0) html += `<div class="step-line" id="pline${i}"></div>`;
            html += `
                <div class="step" id="pstep${s.n}" onclick="goStep(${s.n})">
                    <div class="step-dot" id="pdot${s.n}">${s.n}</div>
                    <div class="step-label">${s.l}</div>
                </div>
            `;
        });
        bar.innerHTML = html;
    }

    buildProgress();

    // ===== Navigation =====
    function goStep(n) {
        if (n < 1 || n > CONFIG.TOTAL_STEPS) return;

        if (n > currentStep) {
            if (currentStep === 1 && !document.getElementById('f_desc').value.trim()) {
                toast(T.warn_enter_description, 'warning');
                return;
            }
            if (currentStep === 2 && (!document.getElementById('f_p1').value.trim() || !document.getElementById('f_p2').value.trim())) {
                toast(T.warn_both_parties, 'warning');
                return;
            }
        }

        currentStep = n;

        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        const targetPage = document.querySelector(`.page[data-step="${n}"]`);
        if (targetPage) targetPage.classList.add('active');

        for (let i = 1; i <= CONFIG.TOTAL_STEPS; i++) {
            const dot = document.getElementById(`pdot${i}`);
            const step = document.getElementById(`pstep${i}`);
            if (dot) {
                dot.classList.remove('active', 'done');
                if (i === n) dot.classList.add('active');
                else if (i < n) dot.classList.add('done');
            }
            if (step) {
                step.classList.remove('active', 'done');
                if (i <= n) step.classList.add(i < n ? 'done' : 'active');
            }
        }

        for (let i = 1; i < CONFIG.TOTAL_STEPS; i++) {
            const line = document.getElementById(`pline${i}`);
            if (line) line.classList.toggle('done', i < n);
        }

        if (n === 4) buildSummary();
        if (n < 4) document.getElementById('result')?.classList.remove('active');
    }

    // ===== Ollama =====
    async function checkOllamaConnection() {
        const modelSelect = document.getElementById('aiModel');

        try {
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 8000);

            const res = await fetch(CONFIG.OLLAMA_TAGS_URL, {
                method: 'GET',
                signal: controller.signal
            });

            clearTimeout(timeout);

            if (!res.ok) throw new Error(`Status ${res.status}`);

            const data = await res.json();
            const models = data.models || [];

            if (models.length === 0) {
                ollamaStatus = 'no-model';
                return false;
            }

            ollamaStatus = 'connected';

            if (modelSelect) {
                const currentVal = modelSelect.value;
                modelSelect.innerHTML = '';

                models.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.name;
                    const sizeGB = m.size ? ` (${(m.size / 1e9).toFixed(1)} GB)` : '';
                    opt.textContent = `🟢 ${m.name}${sizeGB}`;
                    modelSelect.appendChild(opt);
                });

                const matchOption = Array.from(modelSelect.options).find(o => o.value === currentVal);
                if (matchOption) modelSelect.value = currentVal;
                else if (models.length > 0) modelSelect.value = models[0].name;

                debounceSaveForm();
            }

            return true;

        } catch (e) {
            ollamaStatus = 'disconnected';
            return false;
        }
    }

    async function ollamaChat(system, user, model = 'llama3.1:8b') {
        if (!model) throw new Error(T.err_model_not_selected);

        const cacheKey = simpleHash(`${model}:${system}:${user}`);
        const cached = requestCache.get(cacheKey);

        if (cached && Date.now() - cached.ts < CONFIG.API_CACHE_TTL) {
            return cached.data;
        }

        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 600000);

        try {
            const res = await fetch(CONFIG.OLLAMA_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    model,
                    messages: [
                        { role: 'system', content: system },
                        { role: 'user', content: user }
                    ],
                    stream: false,
                    options: { temperature: 0.15, num_predict: 3000 }
                }),
                signal: controller.signal
            });

            clearTimeout(timeout);

            if (!res.ok) {
                if (res.status === 404) {
                    throw new Error(tr(T.err_model_not_found, { model }));
                }
                throw new Error(tr(T.err_api, { status: res.status }));
            }

            const json = await res.json();
            const content = json.message?.content?.trim() || '';

            if (!content) throw new Error(T.err_empty_response);

            requestCache.set(cacheKey, { data: content, ts: Date.now() });
            saveApiCache();

            return content;
        } catch (e) {
            clearTimeout(timeout);

            if (e.name === 'AbortError') throw new Error(T.err_timeout);

            if (e.message.includes('Failed to fetch') || e.message.includes('NetworkError')) {
                throw new Error(T.err_connect);
            }

            throw e;
        }
    }

    // ===== Analysis =====
    async function analyzeAndNext() {
        const desc = document.getElementById('f_desc').value.trim();
        if (!desc) {
            toast(T.warn_enter_description, 'warning');
            return;
        }

        const useOllama = document.getElementById('useOllama').checked;
        const model = document.getElementById('aiModel').value;
        const loader = document.getElementById('loader');

        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        loader.classList.add('active');

        try {
            let data;

            if (useOllama) {
                if (ollamaStatus === 'disconnected') await checkOllamaConnection();

                if (ollamaStatus === 'disconnected') {
                    throw new Error(T.err_ollama_unavailable);
                }

                if (ollamaStatus === 'no-model') {
                    throw new Error(T.err_no_models);
                }

                const prompt = `${T.prompt_extract}\n\n${desc}`;

                const resp = await ollamaChat(prompt, '', model);
                const clean = resp.replace(/```(?:json)?\s*/gi, '').replace(/```/g, '').trim();
                data = JSON.parse(clean);
            } else {
                await new Promise(r => setTimeout(r, 400));
                data = mockAnalyze(desc);
            }

            fillForm(data);
            loader.classList.remove('active');
            goStep(2);
            toast(T.ok_extracted, 'success');
        } catch (e) {
            loader.classList.remove('active');
            document.querySelector('.page[data-step="1"]')?.classList.add('active');
            toast(`⚠️ ${e.message}`, 'error');
            console.error('Analysis error:', e);
        }
    }

    function mockAnalyze(desc) {
        const d = {
            type: 'оказания услуг',
            party1: { name: roleName('Заказчик'), role: 'Заказчик' },
            party2: { name: roleName('Исполнитель'), role: 'Исполнитель' },
            subject: '',
            amount: null,
            city: '',
            duration: '',
            payment: ''
        };

        const l = desc.toLowerCase();

        if (l.match(/аренд|найм|помещ/)) d.type = 'аренды';
        else if (l.match(/купл|продаж|товар|закуп/)) d.type = 'купли-продажи';
        else if (l.match(/подряд|ремонт|монтаж|строит/)) d.type = 'подряда';
        else if (l.match(/поставк|доставк/)) d.type = 'поставки';

        const sumMatch = desc.match(/(\d[\d\s,.]*)\s*(?:тыс\.?|млн\.?|₽|руб|сомон)/i);
        if (sumMatch) {
            let n = sumMatch[1].replace(/[\s,]/g, '').replace('.', '');
            let m = 1;
            if (desc.match(/тыс/i)) m = 1000;
            else if (desc.match(/млн/i)) m = 1000000;
            d.amount = Math.round(parseFloat(n) * m);
        }

        const dur = desc.match(/(\d+\s*(?:день|дней|месяц|месяцев|недел|год))/i);
        if (dur) d.duration = dur[0];

        const city = desc.match(/г\.\s*([А-ЯЁ][а-яё\-]+)/i);
        if (city) d.city = 'г. ' + city[1];

        return d;
    }

    function fillForm(a) {
        const set = (id, v) => {
            const el = document.getElementById(id);
            if (el && v) el.value = v;
        };

        set('f_type', a.type);
        set('f_p1', a.party1?.name);
        set('f_p2', a.party2?.name);
        set('f_role1', a.party1?.role);
        set('f_role2', a.party2?.role);
        set('f_amount', a.amount);
        set('f_pay', a.payment);
        set('f_duration', a.duration);
        set('f_subject', a.subject);
        set('f_city', a.city);

        const info = document.getElementById('detectedInfo');
        if (info) {
            const items = Object.entries(a).filter(([k, v]) => {
                if (typeof v === 'object' && v) return Object.values(v).some(x => x);
                return v && v !== null && v !== false;
            });

            const labels = {
                party1: T.lbl_party1,
                party2: T.lbl_party2,
                amount: T.lbl_amount,
                payment: T.lbl_payment,
                duration: T.lbl_duration,
                type: T.lbl_type,
                subject: T.lbl_subject,
                city: T.lbl_city
            };

            info.innerHTML = items.length ?
                items.map(([k, v]) => {
                    const lbl = labels[k] || k;
                    const val = typeof v === 'object' ?
                        (v.name ? `${v.name} (${v.role})` : JSON.stringify(v).replace(/[{}"]/g, '')) : v;
                    return `<strong>${lbl}:</strong> ${val}<br>`;
                }).join('') :
                `<em style="opacity: 0.8">${T.not_recognized}</em>`;
        }

        debounceSaveForm();
    }

    // ===== Summary =====
    function buildSummary() {
        const g = id => document.getElementById(id)?.value?.trim() || '—';
        const gs = id => document.getElementById(id)?.value || '';

        const amountVal = g('f_amount');
        const amountDisplay = amountVal !== '—' ?
            `${parseInt(amountVal).toLocaleString(T.intl_locale)} ${T.currency}` : '—';

        const vatChip = gs('f_vat') !== 'не указано' ?
            `<span class="chip success">${vatName(gs('f_vat'))}</span>` : '';

        document.getElementById('summary').innerHTML = `
            <div><strong>${T.lbl_type}:</strong> ${typeName(gs('f_type'))}</div>
            <div><strong>${roleName(gs('f_role1'))}:</strong> ${g('f_p1')}</div>
            <div><strong>${roleName(gs('f_role2'))}:</strong> ${g('f_p2')}</div>
            <div><strong>${T.lbl_amount}:</strong> ${amountDisplay} ${vatChip}</div>
            <div><strong>${T.lbl_payment}:</strong> ${g('f_pay')}</div>
            <div><strong>${T.lbl_duration}:</strong> ${g('f_duration')}</div>
            ${g('f_subject') !== '—' ? `<div><strong>${T.lbl_subject}:</strong> ${g('f_subject')}</div>` : ''}
            <div><strong>${T.lbl_penalty}:</strong> ${g('f_penalty')}</div>
            <div><strong>${T.lbl_confidentiality}:</strong> ${document.getElementById('f_conf')?.checked ? T.yes : T.no}</div>
            <div><strong>${T.lbl_jurisdiction}:</strong> ${disputeName(gs('f_dispute'))} | ${g('f_city')}</div>
            ${g('f_extra') !== '—' ? `<div><strong>${T.lbl_extra}:</strong> ${g('f_extra')}</div>` : ''}
            <div><strong>${T.lbl_style}:</strong> <span id="summaryStyle">${styleName(contractStyle)}</span></div>
        `;
    }

    // ===== Generate =====
    async function generate() {
        const data = {
            type: document.getElementById('f_type')?.value,
            p1: document.getElementById('f_p1')?.value.trim(),
            p2: document.getElementById('f_p2')?.value.trim(),
            role1: document.getElementById('f_role1')?.value,
            role2: document.getElementById('f_role2')?.value,
            amount: document.getElementById('f_amount')?.value,
            vat: document.getElementById('f_vat')?.value,
            pay: document.getElementById('f_pay')?.value.trim(),
            duration: document.getElementById('f_duration')?.value.trim(),
            subject: document.getElementById('f_subject')?.value.trim(),
            date: document.getElementById('f_date')?.value,
            city: document.getElementById('f_city')?.value.trim(),
            extra: document.getElementById('f_extra')?.value.trim(),
            conf: document.getElementById('f_conf')?.checked,
            penalty: document.getElementById('f_penalty')?.value.trim(),
            dispute: document.getElementById('f_dispute')?.value
        };

        if (!data.p1 || !data.p2) {
            toast(T.warn_both_parties, 'warning');
            return;
        }

        data.style = contractStyle;

        const btn = document.getElementById('genBtn');
        const loader = document.getElementById('loader');
        const badge = document.getElementById('aiBadge');
        const startedAt = performance.now();

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + T.generating;

        document.querySelector('.page[data-step="4"]')?.classList.remove('active');
        badge?.classList.remove('active', 'done');
        loader.classList.add('active');

        const loaderText = loader.querySelector('.loader-text');
        if (loaderText) loaderText.textContent = tr(T.loader_building, { style: styleName(contractStyle) });

        try {
            // 1) Документ собирается мгновенно из выверенного шаблона по законодательству РТ
            await new Promise(r => setTimeout(r, 420));

            const html = contractStyle === 'classic'
                ? buildClassicContract(data)
                : buildStartupContract(data);

            contractText = generateTextVersion(data);
            contractHtml = html;
            loader.classList.remove('active');

            const output = document.getElementById('contractOutput');
            if (output) {
                output.className = contractStyle === 'classic' ? 'contract-box classic' : 'contract-box';
                output.innerHTML = html;
                output.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            document.getElementById('result')?.classList.add('active');

            for (let i = 1; i <= CONFIG.TOTAL_STEPS; i++) {
                document.getElementById(`pdot${i}`)?.classList.add('done');
                document.getElementById(`pstep${i}`)?.classList.remove('active');
            }

            saveHistory(data, contractText);
            toast(tr(T.ok_contract_created_in, { secs: ((performance.now() - startedAt) / 1000).toFixed(1) }), 'success');

            // 2) ИИ дорабатывает формулировки в фоне — документ уже доступен пользователю
            if (document.getElementById('useOllama')?.checked) {
                polishWithAI(data);
            }
        } catch (e) {
            loader.classList.remove('active');
            document.querySelector('.page[data-step="4"]')?.classList.add('active');
            toast(`⚠️ ${e.message}`, 'error');
            console.error('Generation error:', e);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic"></i> ' + T.btn_generate_contract;
        }
    }

    // ===== Промпт для ИИ: строго по законодательству Республики Таджикистан =====
    function buildContractPrompt(d) {
        return `${T.prompt_build_intro}
${T.prompt_data}
• ${T.lbl_style}: ${styleName(d.style || contractStyle)}
• ${T.lbl_type}: ${typeName(d.type)}
• ${roleName(d.role1)}: ${d.p1}
• ${roleName(d.role2)}: ${d.p2}
• ${T.lbl_amount}: ${d.amount ? `${parseInt(d.amount).toLocaleString(T.intl_locale)} ${T.currency}` : T.prompt_not_specified_f} ${vatName(d.vat) || ''}
• ${T.lbl_payment}: ${d.pay || T.prompt_by_agreement}
• ${T.lbl_duration}: ${d.duration || T.prompt_not_specified_m}
• ${T.lbl_city}: ${d.city || '__________'}
• ${T.lbl_penalty}: ${d.penalty || T.txt_default_penalty}
• ${T.lbl_confidentiality}: ${d.conf ? T.yes : T.no}
• ${T.lbl_jurisdiction}: ${disputeName(d.dispute) || T.txt_default_dispute}
• ${T.lbl_subject}: ${d.subject || typeName(d.type)}
• ${T.lbl_legal_basis}: ${legalBasisFor(d.type)}

${T.prompt_rt_rules}

${T.prompt_outro}`;
    }

    // ===== Фоновая доработка текста договора ИИ =====
    async function polishWithAI(d) {
        const badge = document.getElementById('aiBadge');
        const badgeText = document.getElementById('aiBadgeText');
        if (!badge) return;

        badge.classList.remove('done');
        badge.classList.add('active');
        badge.innerHTML = `<i class="fas fa-circle-notch fa-spin"></i> <span id="aiBadgeText">${T.ai_polishing}</span>`;

        try {
            if (ollamaStatus === 'disconnected' || ollamaStatus === 'unknown') {
                await checkOllamaConnection();
            }
            if (ollamaStatus === 'disconnected') throw new Error(T.err_ollama_unavailable);

            const model = document.getElementById('aiModel')?.value;
            const aiText = await ollamaChat(T.prompt_system, buildContractPrompt(d), model);

            if (aiText) {
                contractText = aiText;
                updateLatestHistoryText(aiText);
                badge.classList.add('done');
                badge.innerHTML = `<i class="fas fa-check-circle"></i> <span id="aiBadgeText">${T.ai_polished}</span>`;
            } else {
                badge.classList.remove('active');
            }
        } catch (e) {
            badge.classList.remove('active');
            console.warn('AI polish skipped:', e.message);
            toast(tr(T.ai_polish_failed, { reason: e.message }), 'warning');
        }
    }

    // ===== Красивый Стартап-шаблон договора =====
    function buildStartupContract(d, aiContent = '') {
        const dt = new Date(d.date || Date.now());
        const dateStr = dt.toLocaleDateString(T.intl_locale, { day: 'numeric', month: 'long', year: 'numeric' });
        const contractNumber = `№ ${Math.floor(Math.random() * 9000) + 1000}/${dt.getFullYear()}`;
        const amt = d.amount ? parseInt(d.amount) : null;
        const amtW = amt ? numWords(amt) : T.doc_amount_in_words_placeholder;
        const amtF = amt ? amt.toLocaleString(T.intl_locale) : '—';
        const typeLocal = typeName(d.type);
        const typeUpper = typeLocal.toUpperCase();
        const typeIcon = getTypeIcon(d.type);
        const role1 = roleName(d.role1);
        const role2 = roleName(d.role2);
        const gen1 = roleGen(d.role1);
        const gen2 = roleGen(d.role2);
        const dat1 = roleDat(d.role1);
         return `
            <!-- HERO HEADER -->
            <div class="contract-hero">
                <div class="contract-brand">
                    <div class="contract-brand-logo">⚖️</div>
                    <div class="contract-brand-name">LEGAL PRO · CONTRACT</div>
                </div>
                <div class="contract-title">${tr(T.doc_title, { type: typeUpper })}</div>
                <div class="contract-subtitle">${T.doc_subtitle}</div>
                <div class="contract-meta">
                    <div class="contract-meta-item">
                        <span class="contract-meta-label">${T.doc_number}</span>
                        <span class="contract-meta-value">${contractNumber}</span>
                    </div>
                    <div class="contract-meta-item">
                        <span class="contract-meta-label">${T.doc_date}</span>
                        <span class="contract-meta-value">${dateStr}</span>
                    </div>
                    <div class="contract-meta-item">
                        <span class="contract-meta-label">${T.doc_city}</span>
                        <span class="contract-meta-value">${d.city || T.doc_city_blank}</span>
                    </div>
                    <div class="contract-meta-item">
                        <span class="contract-meta-label">${T.doc_type}</span>
                        <span class="contract-meta-value">${typeIcon} ${typeLocal}</span>
                    </div>
                </div>
            </div>

            <!-- CONTRACT BODY -->
            <div class="contract-body">
                <!-- PARTIES -->
                <div class="contract-parties">
                    <div class="contract-parties-title">${T.doc_parties_title}</div>
                    <div class="contract-parties-grid">
                        <div class="contract-party">
                            <div class="contract-party-role">${role1}</div>
                            <div class="contract-party-name">${d.p1}</div>
                        </div>
                        <div class="contract-party-vs">⇄</div>
                        <div class="contract-party">
                            <div class="contract-party-role">${role2}</div>
                            <div class="contract-party-name">${d.p2}</div>
                        </div>
                    </div>
                </div>

                <p class="contract-text" style="font-size: 1rem; margin-bottom: 32px; color: #475569;">
                    ${tr(T.doc_preamble, {
                        p1: `<strong style="color: #0f172a;">${d.p1}</strong>`,
                        p2: `<strong style="color: #0f172a;">${d.p2}</strong>`,
                        role1: `<strong>${role1}</strong>`,
                        role2: `<strong>${role2}</strong>`
                    })}
                </p>

                <!-- DEAL SUMMARY CARD -->
                <div class="contract-summary">
                    <div class="contract-summary-title">${T.doc_summary_title}</div>
                    <div class="contract-summary-grid">
                        <div class="contract-summary-item">
                            <div class="contract-summary-label">${T.doc_sum_amount}</div>
                            <div class="contract-summary-value highlight">${amtF}${amt ? ' ' + T.currency_short : ''}</div>
                        </div>
                        <div class="contract-summary-item">
                            <div class="contract-summary-label">${T.doc_sum_term}</div>
                            <div class="contract-summary-value">${d.duration || T.doc_default_duration}</div>
                        </div>
                        <div class="contract-summary-item">
                            <div class="contract-summary-label">${T.doc_sum_payment}</div>
                            <div class="contract-summary-value">${d.pay || T.doc_default_payment}</div>
                        </div>
                        <div class="contract-summary-item">
                            <div class="contract-summary-label">${T.doc_sum_vat}</div>
                            <div class="contract-summary-value">${vatName(d.vat) || T.doc_vat_not_specified}</div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 1: SUBJECT -->
                <div class="contract-section">
                    <div class="contract-section-header">
                        <div class="contract-section-icon"><i class="fas fa-bullseye"></i></div>
                        <div>
                            <div class="contract-section-title">${T.doc_s1_title}</div>
                            <div class="contract-section-number">§ 1</div>
                        </div>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">1.1.</span>
                        <span class="contract-clause-text">
                            ${tr(T.doc_s1_1, { gen1: gen1, role1: role1, role2: role2, subject: d.subject || typeLocal })}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">1.2.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s1_2}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">1.3.</span>
                        <span class="contract-clause-text">
                            ${tr(T.doc_s1_3, { dat1: dat1 })}
                        </span>
                    </div>
                </div>

                <!-- SECTION 2: RIGHTS & DUTIES -->
                <div class="contract-section">
                    <div class="contract-section-header">
                        <div class="contract-section-icon"><i class="fas fa-balance-scale"></i></div>
                        <div>
                            <div class="contract-section-title">${T.doc_s2_title}</div>
                            <div class="contract-section-number">§ 2</div>
                        </div>
                    </div>
                    <div class="contract-highlight">
                        <div class="contract-highlight-icon">👤</div>
                        <div class="contract-highlight-content">
                            <div class="contract-highlight-title">${tr(T.doc_s2_duties_b, { gen2: gen2 })}</div>
                            <div class="contract-highlight-text">
                                ${tr(T.doc_s2_b_list, { dat1: dat1 })}
                            </div>
                        </div>
                    </div>
                    <div class="contract-info">
                        <div class="contract-info-icon">👤</div>
                        <div class="contract-info-content">
                            <div class="contract-info-title">${tr(T.doc_s2_duties_a, { gen1: gen1 })}</div>
                            <div class="contract-info-text">
                                ${T.doc_s2_a_list}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: PRICE & PAYMENT -->
                <div class="contract-section">
                    <div class="contract-section-header">
                        <div class="contract-section-icon"><i class="fas fa-coins"></i></div>
                        <div>
                            <div class="contract-section-title">${T.doc_s3_title}</div>
                            <div class="contract-section-number">§ 3</div>
                        </div>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">3.1.</span>
                        <span class="contract-clause-text">
                            ${tr(T.doc_s3_1, { amount: amtF, words: amtW, vat: d.vat !== 'не указано' ? `, ${vatName(d.vat)}` : '' })}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">3.2.</span>
                        <span class="contract-clause-text">
                            ${tr(T.doc_s3_2, { payment: d.pay || T.doc_s3_by_agreement })}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">3.3.</span>
                        <span class="contract-clause-text">
                            ${tr(T.doc_s3_3, { gen2: gen2 })}
                        </span>
                    </div>
                </div>

                <!-- SECTION 4: TERMS -->
                <div class="contract-section">
                    <div class="contract-section-header">
                        <div class="contract-section-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <div class="contract-section-title">${T.doc_s4_title}</div>
                            <div class="contract-section-number">§ 4</div>
                        </div>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">4.1.</span>
                        <span class="contract-clause-text">
                            ${tr(T.doc_s4_1, { duration: d.duration || T.doc_s4_default_duration })}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">4.2.</span>
                        <span class="contract-clause-text">
                            ${tr(T.doc_s4_2, { role1: role1 })}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">4.3.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s4_3}
                        </span>
                    </div>
                </div>

                <!-- SECTION 5: LIABILITY -->
                <div class="contract-section">
                    <div class="contract-section-header">
                        <div class="contract-section-icon"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <div class="contract-section-title">${T.doc_s5_title}</div>
                            <div class="contract-section-number">§ 5</div>
                        </div>
                    </div>
                    <div class="contract-highlight">
                        <div class="contract-highlight-icon">⚠️</div>
                        <div class="contract-highlight-content">
                            <div class="contract-highlight-title">${T.doc_s5_penalty_title}</div>
                            <div class="contract-highlight-text">
                                ${tr(T.doc_s5_1, { penalty: d.penalty || '0,1%' })}
                            </div>
                        </div>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">5.1.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s5_2}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">5.2.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s5_3}
                        </span>
                    </div>
                </div>

                <!-- SECTION 6: FORCE MAJEURE -->
                <div class="contract-section">
                    <div class="contract-section-header">
                        <div class="contract-section-icon"><i class="fas fa-cloud-bolt"></i></div>
                        <div>
                            <div class="contract-section-title">${T.doc_s6_title}</div>
                            <div class="contract-section-number">§ 6</div>
                        </div>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">6.1.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s6_1}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">6.2.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s6_2}
                        </span>
                    </div>
                </div>

                ${d.conf ? `
                <!-- SECTION 7: CONFIDENTIALITY -->
                <div class="contract-section">
                    <div class="contract-section-header">
                        <div class="contract-section-icon"><i class="fas fa-lock"></i></div>
                        <div>
                            <div class="contract-section-title">${T.doc_s7_title}</div>
                            <div class="contract-section-number">§ 7</div>
                        </div>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">7.1.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s7_1}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">7.2.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s7_2}
                        </span>
                    </div>
                </div>
                ` : ''}

                <!-- SECTION ${d.conf ? 8 : 7}: DISPUTES -->
                <div class="contract-section">
                    <div class="contract-section-header">
                        <div class="contract-section-icon"><i class="fas fa-gavel"></i></div>
                        <div>
                            <div class="contract-section-title">${T.doc_s8_title}</div>
                            <div class="contract-section-number">§ ${d.conf ? 8 : 7}</div>
                        </div>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">${d.conf ? '8' : '7'}.1.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s8_1}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">${d.conf ? '8' : '7'}.2.</span>
                        <span class="contract-clause-text">
                            ${tr(T.doc_s8_2, { dispute: `<strong>${disputeName(d.dispute) || T.doc_s8_default}</strong>` })}
                        </span>
                    </div>
                </div>

                <!-- SECTION ${d.conf ? 9 : 8}: FINAL PROVISIONS -->
                <div class="contract-section">
                    <div class="contract-section-header">
                        <div class="contract-section-icon"><i class="fas fa-file-signature"></i></div>
                        <div>
                            <div class="contract-section-title">${T.doc_s9_title}</div>
                            <div class="contract-section-number">§ ${d.conf ? 9 : 8}</div>
                        </div>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">${d.conf ? '9' : '8'}.1.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s9_1}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">${d.conf ? '9' : '8'}.2.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s9_2}
                        </span>
                    </div>
                    <div class="contract-clause">
                        <span class="contract-clause-number">${d.conf ? '9' : '8'}.3.</span>
                        <span class="contract-clause-text">
                            ${T.doc_s9_3}
                        </span>
                    </div>
                    ${d.extra ? `
                    <div class="contract-clause">
                        <span class="contract-clause-number">${d.conf ? '9' : '8'}.4.</span>
                        <span class="contract-clause-text">${tr(T.doc_s9_extra, { extra: d.extra })}</span>
                    </div>
                    ` : ''}
                </div>

                <!-- SIGNATURES -->
                <div class="contract-signatures">
                    <div class="contract-signatures-title">${T.doc_signatures_title}</div>
                    <div class="contract-signatures-grid">
                        <div class="contract-signature">
                            <div class="contract-signature-party">${role1}</div>
                            <div class="contract-signature-name">${d.p1}</div>
                            <div class="contract-signature-line">
                                _________________________ / _________________ /
                            </div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">${T.doc_signature_hint}</div>
                        </div>
                        <div class="contract-signature">
                            <div class="contract-signature-party">${role2}</div>
                            <div class="contract-signature-name">${d.p2}</div>
                            <div class="contract-signature-line">
                                _________________________ / _________________ /
                            </div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">${T.doc_signature_hint}</div>
                        </div>
                    </div>

                    <!-- Requisites -->
                    <div class="contract-requisites">
                        <div class="contract-requisites-title">${T.doc_requisites_title}</div>
                        <div class="contract-requisites-grid">
                            <div class="contract-requisites-col">
                                <h5>${role1}: ${d.p1}</h5>
                                <div class="contract-requisites-row">
                                    <span class="contract-requisites-label">${T.doc_req_address}</span>
                                    <span class="contract-requisites-value">_________________</span>
                                </div>
                                <div class="contract-requisites-row">
                                    <span class="contract-requisites-label">${T.doc_req_inn}</span>
                                    <span class="contract-requisites-value">_________________</span>
                                </div>
                                <div class="contract-requisites-row">
                                    <span class="contract-requisites-label">${T.doc_req_account}</span>
                                    <span class="contract-requisites-value">_________________</span>
                                </div>
                                <div class="contract-requisites-row">
                                    <span class="contract-requisites-label">${T.doc_req_bank}</span>
                                    <span class="contract-requisites-value">_________________</span>
                                </div>
                            </div>
                            <div class="contract-requisites-col">
                                <h5>${role2}: ${d.p2}</h5>
                                <div class="contract-requisites-row">
                                    <span class="contract-requisites-label">${T.doc_req_address}</span>
                                    <span class="contract-requisites-value">_________________</span>
                                </div>
                                <div class="contract-requisites-row">
                                    <span class="contract-requisites-label">${T.doc_req_inn}</span>
                                    <span class="contract-requisites-value">_________________</span>
                                </div>
                                <div class="contract-requisites-row">
                                    <span class="contract-requisites-label">${T.doc_req_account}</span>
                                    <span class="contract-requisites-value">_________________</span>
                                </div>
                                <div class="contract-requisites-row">
                                    <span class="contract-requisites-label">${T.doc_req_bank}</span>
                                    <span class="contract-requisites-value">_________________</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="contract-footer">
                <div class="contract-footer-brand">⚖️ LEGAL PRO · CONTRACT</div>
                <div class="contract-footer-text">
                    ${T.doc_footer_1}<br>
                    ${dateStr} · ${T.doc_footer_2} · ${contractNumber}
                </div>
            </div>
        `;
    }

    // Институт ГК РТ, регулирующий выбранный тип договора
    function legalBasisFor(type) {
        return (T.legal_basis && (T.legal_basis[type] || T.legal_basis._default)) || '';
    }

    // Стабильный номер договора: одни и те же данные дают один и тот же номер
    function contractNo(d, year) {
        const src = `${d.p1 || ''}|${d.p2 || ''}|${d.type || ''}|${d.date || ''}`;
        let hash = 0;
        for (let i = 0; i < src.length; i++) hash = ((hash << 5) - hash + src.charCodeAt(i)) | 0;
        return `№ ${Math.abs(hash) % 9000 + 1000}/${year}`;
    }

    // ===== Классический бланк договора (официальное оформление, право РТ) =====
    function buildClassicContract(d, aiContent = '') {
        const dt = new Date(d.date || Date.now());
        const dateStr = dt.toLocaleDateString(T.intl_locale, { day: 'numeric', month: 'long', year: 'numeric' });
        const contractNumber = contractNo(d, dt.getFullYear());
        const amt = d.amount ? parseInt(d.amount) : null;
        const amtW = amt ? numWords(amt) : T.doc_amount_in_words_placeholder;
        const amtF = amt ? amt.toLocaleString(T.intl_locale) : '—';
        const typeLocal = typeName(d.type);
        const role1 = roleName(d.role1);
        const role2 = roleName(d.role2);
        const gen1 = roleGen(d.role1);
        const gen2 = roleGen(d.role2);
        const dat1 = roleDat(d.role1);
        const listItems = str => (str || '').split('<br>')
            .map(s => s.replace(/^\s*[•·-]\s*/, '').trim())
            .filter(Boolean);

        let n = 0;
        const section = (title, clauses) => {
            n++;
            const body = clauses.filter(Boolean).map((c, i) =>
                `<div class="cls-clause"><span class="cls-clause-num">${n}.${i + 1}.</span> ${c}</div>`
            ).join('');
            return `<div class="cls-section"><h3 class="cls-section-title">${n}. ${title}</h3>${body}</div>`;
        };

        const duties = (title, list) =>
            `${title}<ul class="cls-list">${listItems(list).map(li => `<li>— ${li}</li>`).join('')}</ul>`;

        const requisites = (role, name) => `
            <div class="cls-party-title">${role}</div>
            <div class="cls-req-row"><strong>${name}</strong></div>
            <div class="cls-req-row">${T.doc_req_address} _____________________</div>
            <div class="cls-req-row">${T.doc_req_inn} _____________________</div>
            <div class="cls-req-row">${T.doc_req_account} _____________________</div>
            <div class="cls-req-row">${T.doc_req_bank} _____________________</div>
            <div class="cls-sign-line">_________________ / _________________ /</div>
            <div class="cls-stamp">${T.doc_signature_hint} ${T.cls_stamp}</div>
        `;

        return `
            <div class="cls-doc">
                <div class="cls-title">${tr(T.doc_title, { type: typeLocal.toUpperCase() })}</div>
                <div class="cls-number">${contractNumber}</div>
                <div class="cls-place">
                    <span>${d.city || T.doc_city_blank}</span>
                    <span>${dateStr}</span>
                </div>
                <hr class="cls-rule">

                <p class="cls-preamble">
                    ${tr(T.doc_preamble, {
                        p1: `<strong>${d.p1}</strong>`,
                        p2: `<strong>${d.p2}</strong>`,
                        role1: `<strong>${role1}</strong>`,
                        role2: `<strong>${role2}</strong>`
                    })}
                </p>

                <div class="cls-basis">
                    <div class="cls-basis-title">${T.cls_basis_title}</div>
                    <div>${tr(T.cls_basis_lead, { basis: legalBasisFor(d.type) })}</div>
                    <ul>${(T.cls_basis_acts || []).map(a => `<li>${a}</li>`).join('')}</ul>
                </div>

                ${section(T.doc_s1_title.toUpperCase(), [
                    tr(T.doc_s1_1, { gen1: gen1, role1: role1, role2: role2, subject: d.subject || typeLocal }),
                    T.doc_s1_2,
                    tr(T.doc_s1_3, { dat1: dat1 })
                ])}

                ${section(T.doc_s2_title.toUpperCase(), [
                    duties(tr(T.doc_s2_duties_b, { gen2: gen2 }), tr(T.doc_s2_b_list, { dat1: dat1 })),
                    duties(tr(T.doc_s2_duties_a, { gen1: gen1 }), T.doc_s2_a_list)
                ])}

                ${section(T.doc_s3_title.toUpperCase(), [
                    tr(T.doc_s3_1, { amount: amtF, words: amtW, vat: d.vat !== 'не указано' ? `, ${vatName(d.vat)}` : '' }),
                    tr(T.doc_s3_2, { payment: d.pay || T.doc_s3_by_agreement }),
                    tr(T.doc_s3_3, { gen2: gen2 }),
                    T.cls_currency_clause,
                    T.cls_tax_clause
                ])}

                ${section(T.doc_s4_title.toUpperCase(), [
                    tr(T.doc_s4_1, { duration: d.duration || T.doc_s4_default_duration }),
                    tr(T.doc_s4_2, { role1: role1 }),
                    T.doc_s4_3
                ])}

                ${section(T.doc_s5_title.toUpperCase(), [
                    tr(T.doc_s5_1, { penalty: d.penalty || '0,1%' }),
                    T.doc_s5_2,
                    T.doc_s5_3
                ])}

                ${section(T.doc_s6_title.toUpperCase(), [T.doc_s6_1, T.doc_s6_2])}

                ${d.conf ? section(T.doc_s7_title.toUpperCase(), [T.doc_s7_1, T.doc_s7_2]) : ''}

                ${section(T.doc_s8_title.toUpperCase(), [
                    T.doc_s8_1,
                    tr(T.doc_s8_2, { dispute: `<strong>${disputeName(d.dispute) || T.doc_s8_default}</strong>` })
                ])}

                ${section(T.doc_s9_title.toUpperCase(), [
                    T.doc_s9_1,
                    T.doc_s9_2,
                    T.doc_s9_3,
                    T.cls_language_clause,
                    d.extra ? tr(T.doc_s9_extra, { extra: d.extra }) : ''
                ])}

                <div class="cls-sign-title">${T.cls_signatures_title}</div>
                <table class="cls-table">
                    <tbody>
                        <tr>
                            <td>${requisites(role1, d.p1)}</td>
                            <td>${requisites(role2, d.p2)}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="cls-footnote">
                    ${T.cls_footnote} · ${contractNumber} · ${dateStr}
                </div>
            </div>
        `;
    }

    function getTypeIcon(type) {
        const icons = {
            'оказания услуг': '🤝',
            'подряда': '🔨',
            'аренды': '🏢',
            'купли-продажи': '🛒',
            'поставки': '📦',
            'лицензионный': '📜'
        };
        return icons[type] || '📋';
    }

    function generateTextVersion(d) {
        const dt = new Date(d.date || Date.now());
        const dateStr = dt.toLocaleDateString(T.intl_locale, { day: 'numeric', month: 'long', year: 'numeric' });
        const amt = d.amount ? parseInt(d.amount) : null;
        const amtW = amt ? numWords(amt) : T.txt_words_placeholder;
        const amtF = amt ? amt.toLocaleString(T.intl_locale) : T.txt_amount_placeholder;
        const typeLocal = typeName(d.type);
        const role1 = roleName(d.role1);
        const role2 = roleName(d.role2);
        const dat1 = roleDat(d.role1);

        return `${tr(T.txt_header, { type: typeLocal.toUpperCase() })}

${d.city || T.txt_city_blank} | ${dateStr}

${tr(T.txt_preamble, { p1: d.p1, p2: d.p2, role1: role1, role2: role2 })}

${T.txt_s1}
${tr(T.txt_s1_1, { dat1: dat1, role1: role1, role2: role2, subject: d.subject || typeLocal })}

${T.txt_s2}
${tr(T.txt_s2_1, { amount: amtF, words: amtW, vat: d.vat !== 'не указано' ? vatName(d.vat) : '' })}
${tr(T.txt_s2_2, { payment: d.pay || T.doc_default_payment })}

${T.txt_s3}
${tr(T.txt_s3_1, { duration: d.duration || T.doc_s4_default_duration })}

${T.txt_s4}
${tr(T.txt_s4_1, { penalty: d.penalty || T.txt_default_penalty })}

${T.txt_s5}
${tr(T.txt_s5_1, { dispute: disputeName(d.dispute) || T.txt_default_dispute })}

${T.txt_signatures}
${role1}: ${d.p1} ___________    ${role2}: ${d.p2} ___________`;
    }

    function numWords(n) {
        if (!n) return T.txt_words_placeholder;
        n = Math.floor(n);
        if (n === 0) return T.num_zero + ' ' + T.currency;

        const ones = T.num_ones, teens = T.num_teens, tens = T.num_tens, hunds = T.num_hunds;
        const sep = T.num_sep || ' ';

        const formatGroup = (num) => {
            if (num === 0) return '';
            const h = Math.floor(num / 100) % 10,
                t = Math.floor(num / 10) % 10,
                o = num % 10;
            const parts = [];
            if (h) parts.push(hunds[h]);
            if (t === 1) parts.push(teens[o]);
            else {
                if (t) parts.push(tens[t]);
                if (o) parts.push(ones[o]);
            }
            return parts.filter(Boolean).join(sep);
        };

        const result = [];
        const x = n;
        const th = Math.floor(x / 1000) % 1000;
        if (th > 0) {
            const f = (th % 10 === 1 && th % 100 !== 11) ? T.num_thousand_one :
                ((th % 10 >= 2 && th % 10 <= 4 && (th % 100 < 10 || th % 100 > 20)) ? T.num_thousand_few : T.num_thousand_many);
            result.push(formatGroup(th) + ' ' + f);
        }
        const u = formatGroup(x % 1000);
        if (u || th === 0) result.push(u);

        return result.filter(Boolean).join(sep).trim() + ' ' + T.currency;
    }

    // ===== History =====
    function saveHistory(data, text) {
        try {
            const h = JSON.parse(localStorage.getItem(CONFIG.HISTORY_KEY) || '[]');
            h.unshift({
                id: Date.now(),
                ts: new Date().toISOString(),
                preview: `${data.type} | ${truncate(data.p1, 20)} ↔ ${truncate(data.p2, 20)}`,
                data,
                contract: text,
                amount: data.amount
            });
            if (h.length > CONFIG.MAX_HISTORY) h.splice(CONFIG.MAX_HISTORY);
            localStorage.setItem(CONFIG.HISTORY_KEY, JSON.stringify(h));
            recordCabinetActivity({
                type: 'generation',
                title: T.hist_title,
                details: `${typeName(data.type) || T.hist_contract} | ${data.p1 || T.lbl_party1} - ${data.p2 || T.lbl_party2}`,
                file_name: `${T.hist_file_name}_${new Date().toISOString().slice(0, 10)}.txt`,
                summary: `${typeName(data.type) || T.hist_contract}: ${data.p1 || T.lbl_party1} - ${data.p2 || T.lbl_party2}`,
                result: text || '',
                status: 'completed',
                contract_type: typeName(data.type) || T.hist_contract,
                metadata: {
                    model: document.getElementById('useOllama')?.checked ? document.getElementById('aiModel')?.value : 'template',
                    amount: data.amount,
                    city: data.city,
                    counterparties: [data.p1, data.p2].filter(Boolean)
                }
            });
        } catch (e) {
            console.warn('History error:', e);
        }
    }

    // Заменяет текст последнего черновика доработанной ИИ версией (без дубля в истории)
    function updateLatestHistoryText(text) {
        try {
            const h = JSON.parse(localStorage.getItem(CONFIG.HISTORY_KEY) || '[]');
            if (!h.length) return;
            h[0].contract = text;
            localStorage.setItem(CONFIG.HISTORY_KEY, JSON.stringify(h));
            updateHistoryUI();
        } catch (e) {
            console.warn('History update error:', e);
        }
    }

    function loadHistory(id) {
        const h = JSON.parse(localStorage.getItem(CONFIG.HISTORY_KEY) || '[]');
        const entry = h.find(e => e.id === id);
        if (!entry) {
            toast(T.hist_not_found, 'error');
            return;
        }

        const d = entry.data;
        const fields = ['f_type', 'f_p1', 'f_p2', 'f_role1', 'f_role2', 'f_amount', 'f_pay', 'f_duration', 'f_subject', 'f_extra', 'f_city', 'f_vat', 'f_penalty', 'f_dispute'];

        fields.forEach(k => {
            const el = document.getElementById(k);
            if (el && d[k] !== undefined) el.value = d[k];
        });

        if (d.date) {
            const el = document.getElementById('f_date');
            if (el) el.value = d.date;
        }

        if (d.conf) {
            const el = document.getElementById('f_conf');
            if (el) el.checked = d.conf;
        }

        if (entry.contract) {
            contractText = entry.contract;
            selectStyle(d.style || 'classic', true);
            const html = contractStyle === 'classic' ? buildClassicContract(d) : buildStartupContract(d);
            contractHtml = html;
            const output = document.getElementById('contractOutput');
            if (output) {
                output.className = contractStyle === 'classic' ? 'contract-box classic' : 'contract-box';
                output.innerHTML = html;
            }
            document.getElementById('result')?.classList.add('active');
            goStep(4);
        } else {
            goStep(2);
        }

        closeHistoryModal();
        toast(T.hist_loaded, 'success');
    }

    function deleteHistory(id) {
        let h = JSON.parse(localStorage.getItem(CONFIG.HISTORY_KEY) || '[]');
        const len = h.length;
        h = h.filter(e => e.id !== id);
        localStorage.setItem(CONFIG.HISTORY_KEY, JSON.stringify(h));
        updateHistoryUI();
        toast(tr(T.hist_deleted, { count: len - h.length }), 'success');
    }

    function clearAllHistory() {
        if (confirm(T.hist_confirm_clear)) {
            localStorage.removeItem(CONFIG.HISTORY_KEY);
            updateHistoryUI();
            toast(T.hist_cleared, 'success');
        }
    }

    function updateHistoryUI() {
        const c = document.getElementById('historyList');
        if (!c) return;

        const h = JSON.parse(localStorage.getItem(CONFIG.HISTORY_KEY) || '[]');

        if (!h.length) {
            c.innerHTML = `<p style="text-align: center; color: var(--text-muted); padding: 48px 24px; font-size: 0.9375rem;">${T.hist_empty}</p>`;
            return;
        }

        c.innerHTML = h.map(e => {
            const dt = new Date(e.ts);
            const ds = dt.toLocaleDateString(T.intl_locale, { day: '2-digit', month: '2-digit', year: 'numeric' });
            const tm = dt.toLocaleTimeString(T.intl_locale, { hour: '2-digit', minute: '2-digit' });

            return `
                <div class="history-item" onclick="loadHistory(${e.id})">
                    <div class="history-item-header">
                        <span class="history-item-title">${e.preview}</span>
                        <span class="history-item-date">${ds} ${tm}</span>
                    </div>
                    ${e.amount ? `<div class="history-item-amount">💰 ${parseInt(e.amount).toLocaleString(T.intl_locale)} ${T.currency_short}</div>` : ''}
                    <div class="history-item-actions" onclick="event.stopPropagation()">
                        <button class="btn-sm" onclick="loadHistory(${e.id})">
                            <i class="fas fa-download"></i>
                            ${T.hist_load}
                        </button>
                        <button class="btn-sm btn-sm-danger" onclick="deleteHistory(${e.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function showHistoryModal() {
        document.getElementById('historyModal')?.classList.add('active');
        updateHistoryUI();
    }

    function closeHistoryModal() {
        document.getElementById('historyModal')?.classList.remove('active');
    }

    document.addEventListener('click', e => {
        const m = document.getElementById('historyModal');
        if (m && e.target === m) closeHistoryModal();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeHistoryModal();
            const t = document.getElementById('toast');
            if (t?.classList.contains('show')) t.classList.remove('show');
            document.getElementById('themeDropdown')?.classList.remove('show');
        }
    });

    function truncate(s, n) {
        if (!s) return '';
        return s.length > n ? s.slice(0, n) + '…' : s;
    }

    // ===== Export =====
    function copyText() {
        if (!contractText) {
            toast(T.warn_nothing_to_copy, 'warning');
            return;
        }
        navigator.clipboard.writeText(contractText)
            .then(() => toast(T.ok_copied, 'success'))
            .catch(() => toast(T.err_copy, 'error'));
    }

    function downloadTxt() {
        if (!contractText) return;
        const blob = new Blob([contractText], { type: 'text/plain;charset=utf-8' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `${T.hist_file_name}_${new Date().toISOString().slice(0, 10)}.txt`;
        a.click();
        URL.revokeObjectURL(a.href);
        toast(T.ok_txt_downloaded, 'success');
    }

    async function exportToDocx() {
        if (!contractText) {
            toast(T.warn_generate_first, 'warning');
            return;
        }
        toast(T.creating_docx, 'warning');
        try {
            const { Document, Paragraph, TextRun, HeadingLevel, AlignmentType, Packer } = window.docx;
            const lines = contractText.split('\n');
            const children = [];

            for (let line of lines) {
                const t = line.trim();
                if (!t) continue;

                if (t.match(/^[A-ZА-ЯЁЎҚҒҲҶӢӮ0-9\s]{5,}$/)) {
                    children.push(new Paragraph({
                        text: t,
                        heading: HeadingLevel.HEADING_1,
                        alignment: AlignmentType.CENTER,
                        children: [new TextRun({ bold: true, size: 32, font: 'Times New Roman' })]
                    }));
                } else if (t.match(/^\d+\.\s[A-ZА-Я]/)) {
                    children.push(new Paragraph({
                        text: t,
                        heading: HeadingLevel.HEADING_2,
                        children: [new TextRun({ bold: true, size: 26, font: 'Times New Roman' })]
                    }));
                } else {
                    children.push(new Paragraph({
                        children: [new TextRun({
                            text: t,
                            size: 22,
                            font: 'Times New Roman'
                        })],
                        spacing: { after: 60 }
                    }));
                }
            }

            const doc = new Document({
                creator: 'AI Contract Builder PRO',
                title: T.hist_contract,
                sections: [{
                    properties: {
                        page: { margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 } }
                    },
                    children
                }]
            });

            const blob = await Packer.toBlob(doc);
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = `${T.hist_file_name}_${new Date().toISOString().slice(0, 10)}.docx`;
            a.click();
            URL.revokeObjectURL(a.href);
            toast(T.ok_docx_downloaded, 'success');
        } catch (e) {
            toast(`❌ DOCX: ${e.message}`, 'error');
            console.error('DOCX error:', e);
        }
    }

    async function exportToPdf() {
        if (!contractHtml && !contractText) {
            toast(T.warn_generate_first, 'warning');
            return;
        }
        toast(T.creating_pdf, 'warning');
        try {
            const { jsPDF } = window.jspdf;
            const el = document.getElementById('contractOutput');
            if (!el) throw new Error(T.err_element_not_found);

            const oldMax = el.style.maxHeight;
            el.style.maxHeight = 'none';

            const canvas = await html2canvas(el, {
                scale: 3,
                useCORS: true,
                backgroundColor: '#fff',
                logging: false,
                windowWidth: el.scrollWidth
            });

            el.style.maxHeight = oldMax;

            const img = canvas.toDataURL('image/png');
            const pdf = new jsPDF('p', 'mm', 'a4');
            const pw = 210, ph = 297;
            const iw = pw, ih = canvas.height * iw / canvas.width;
            let h = ih, pos = 0;

            pdf.addImage(img, 'PNG', 0, pos, iw, ih);
            h -= ph;

            while (h > 0) {
                pos = h - ih;
                pdf.addPage();
                pdf.addImage(img, 'PNG', 0, pos, iw, ih);
                h -= ph;
            }

            pdf.save(`${T.hist_file_name}_${new Date().toISOString().slice(0, 10)}.pdf`);
            toast(T.ok_pdf_downloaded, 'success');
        } catch (e) {
            toast(T.warn_pdf_print, 'warning');
            setTimeout(() => window.print(), 400);
            console.error('PDF error:', e);
        }
    }

    // ===== Utilities =====
    function startOver() {
        contractText = '';
        contractHtml = '';
        document.getElementById('result')?.classList.remove('active');

        document.querySelectorAll('input:not([type="checkbox"]), textarea, select').forEach(el => {
            if (el.id === 'f_date') el.valueAsDate = new Date();
            else if (el.id === 'f_city') el.value = @json(__('generate.city_default'));
            else if (el.id === 'f_type') el.value = 'оказания услуг';
            else if (el.id === 'f_role1') el.value = 'Заказчик';
            else if (el.id === 'f_role2') el.value = 'Исполнитель';
            else if (el.id === 'f_vat') el.value = 'не указано';
            else if (el.id === 'f_dispute') el.value = 'экономический суд';
            else if (el.id === 'f_penalty') el.value = '0.1%';
            else el.value = '';
        });

        const confEl = document.getElementById('f_conf');
        if (confEl) confEl.checked = false;

        const detectedInfo = document.getElementById('detectedInfo');
        if (detectedInfo) detectedInfo.innerHTML = '';

        const badge = document.getElementById('aiBadge');
        if (badge) badge.classList.remove('active', 'done');

        localStorage.removeItem(CONFIG.FORM_CACHE_KEY);
        goStep(1);
        toast(T.ok_restarted, 'success');
    }

    function toast(msg, type = 'success') {
        const t = document.getElementById('toast');
        if (!t) return;
        t.textContent = msg;
        t.className = `toast ${type} show`;
        setTimeout(() => t.classList.remove('show'), 4000);
    }

    // ===== Init =====
    document.addEventListener('DOMContentLoaded', () => {
        loadTheme();
        loadApiCache();

        let savedStyle = 'classic';
        try { savedStyle = localStorage.getItem(CONFIG.STYLE_KEY) || 'classic'; } catch (e) { /* noop */ }
        selectStyle(savedStyle, true);

        const isRestored = restoreFormState();
        if (isRestored && document.getElementById('f_desc')?.value) {
            toast(T.ok_cache_restored, 'success');
        }

        if (!document.getElementById('f_date')?.value) {
            const dateEl = document.getElementById('f_date');
            if (dateEl) dateEl.valueAsDate = new Date();
        }

        FORM_FIELDS.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', debounceSaveForm);
                el.addEventListener('change', debounceSaveForm);
            }
        });

        if (document.getElementById('useOllama')?.checked) {
            setTimeout(() => checkOllamaConnection(), 500);
        }

        const ollamaCheckbox = document.getElementById('useOllama');
        if (ollamaCheckbox) {
            ollamaCheckbox.addEventListener('change', () => {
                if (ollamaCheckbox.checked) checkOllamaConnection();
                debounceSaveForm();
            });
        }
    });

    document.querySelectorAll('input[type="text"], input[type="number"]').forEach(inp => {
        inp.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && currentStep < 4) {
                e.preventDefault();
                if (currentStep === 1) analyzeAndNext();
                else goStep(currentStep + 1);
            }
        });
    });
</script>
</body>
</html>
