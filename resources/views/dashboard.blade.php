<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LegalAI Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        manrope: ['Manrope', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>

        :root[data-theme="light"] {
            --bg-main: #f8f6f0;
            --bg-main-end: #f1efe7;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: rgba(31, 41, 55, 0.08);
            --card-bg: rgba(255, 253, 248, 0.96);
            --surface-bg: #fff;
            --chip-bg: #eef3ff;
            --chip-text: #0d2d8a;
            --hero-gradient-start: #11224f;
            --hero-gradient-mid: #173ca8;
            --hero-gradient-end: #2358ff;
            --hover-border: rgba(20, 71, 230, 0.22);
            --hover-shadow: rgba(20, 71, 230, 0.08);
            --form-control-border: rgba(31, 41, 55, 0.12);
            --form-control-focus-border: rgba(20, 71, 230, 0.45);
            --form-control-focus-shadow: rgba(20, 71, 230, 0.12);
            --success-bg: #e8f7ee;
            --warning-bg: #fff2dc;
            --shadow-main: rgba(15, 23, 42, 0.08);
            --badge-light-bg: #f3f4f6;
            --badge-light-text: #1f2937;
            --navbar-bg: rgba(255, 253, 248, 0.88);
            --navbar-border: rgba(31, 41, 55, 0.08);
            --theme-toggle-bg: #f3f4f6;
            --theme-toggle-hover: #e5e7eb;
            --feature-card-bg: rgba(255, 253, 248, 0.92);
            --feature-card-border: rgba(31, 41, 55, 0.08);
            --feature-card-hover-shadow: rgba(20, 71, 230, 0.12);
            --footer-bg: rgba(255, 253, 248, 0.96);
            --footer-border: rgba(31, 41, 55, 0.06);
            --stat-card-bg: #1a1b2e;
            --stat-card-border: rgba(148, 163, 184, 0.12);
            --stat-value-color: #a78bfa;
            --stat-label-color: #94a3b8;
            --gradient-accent-1: rgba(20, 71, 230, 0.06);
            --modal-backdrop: rgba(15, 23, 42, 0.4);
            --hero-badge-bg: rgba(255,255,255,0.1);
            --hero-badge-border: rgba(255,255,255,0.15);
            --hero-chip-bg: rgba(255,255,255,0.12);
            --hero-chip-text: #fff;
            --hero-metric-bg: rgba(255,255,255,0.08);
            --hero-metric-border: rgba(255,255,255,0.1);
            --lang-btn-bg: rgba(255,255,255,0.1);
            --lang-btn-active: rgba(255,255,255,0.22);
            --lang-btn-text: rgba(255,255,255,0.7);
            --lang-btn-active-text: #fff;
            --dropdown-bg: rgba(255, 253, 248, 0.97);
            --dropdown-border: rgba(31, 41, 55, 0.1);
            --dropdown-hover: rgba(20, 71, 230, 0.06);
            --chat-bg: #fff;
            --chat-msg-user: #3b82f6;
            --chat-msg-ai: #f3f4f6;
            --chat-msg-ai-text: #1f2937;
        }

        :root[data-theme="dark"] {
            --bg-main: #0f0f1a;
            --bg-main-end: #12121f;
            --ink: #e2e8f0;
            --muted: #94a3b8;
            --line: rgba(148, 163, 184, 0.12);
            --card-bg: rgba(30, 32, 52, 0.96);
            --surface-bg: #1e2035;
            --chip-bg: #1e293b;
            --chip-text: #60a5fa;
            --hero-gradient-start: #0a1628;
            --hero-gradient-mid: #1e3a8a;
            --hero-gradient-end: #2563eb;
            --hover-border: rgba(59, 130, 246, 0.25);
            --hover-shadow: rgba(59, 130, 246, 0.1);
            --form-control-border: rgba(148, 163, 184, 0.2);
            --form-control-focus-border: rgba(96, 165, 250, 0.5);
            --form-control-focus-shadow: rgba(59, 130, 246, 0.2);
            --success-bg: #064e3b;
            --warning-bg: #451a03;
            --shadow-main: rgba(0, 0, 0, 0.3);
            --badge-light-bg: rgba(51, 65, 85, 0.5);
            --badge-light-text: #cbd5e1;
            --navbar-bg: rgba(15, 15, 26, 0.92);
            --navbar-border: rgba(71, 85, 105, 0.3);
            --theme-toggle-bg: rgba(51, 65, 85, 0.5);
            --theme-toggle-hover: rgba(71, 85, 105, 0.5);
            --feature-card-bg: rgba(30, 32, 52, 0.9);
            --feature-card-border: rgba(148, 163, 184, 0.12);
            --feature-card-hover-shadow: rgba(59, 130, 246, 0.15);
            --footer-bg: rgba(15, 15, 26, 0.95);
            --footer-border: rgba(71, 85, 105, 0.15);
            --stat-card-bg: #1a1b2e;
            --stat-card-border: rgba(148, 163, 184, 0.12);
            --stat-value-color: #a78bfa;
            --stat-label-color: #94a3b8;
            --gradient-accent-1: rgba(59, 130, 246, 0.08);
            --modal-backdrop: rgba(0, 0, 0, 0.6);
            --hero-badge-bg: rgba(255,255,255,0.08);
            --hero-badge-border: rgba(255,255,255,0.12);
            --hero-chip-bg: rgba(255,255,255,0.08);
            --hero-chip-text: #fff;
            --hero-metric-bg: rgba(255,255,255,0.06);
            --hero-metric-border: rgba(255,255,255,0.08);
            --lang-btn-bg: rgba(255,255,255,0.06);
            --lang-btn-active: rgba(255,255,255,0.15);
            --lang-btn-text: rgba(255,255,255,0.5);
            --lang-btn-active-text: #fff;
            --dropdown-bg: rgba(15, 15, 26, 0.97);
            --dropdown-border: rgba(71, 85, 105, 0.3);
            --dropdown-hover: rgba(255,255,255,0.05);
            --chat-bg: #1e2035;
            --chat-msg-user: #3b82f6;
            --chat-msg-ai: #2a2d47;
            --chat-msg-ai-text: #e2e8f0;
        }

        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            min-height: 100vh;
            background: var(--bg-main);
            padding-top: 56px;
            overflow-x: hidden;
        }

        .card-gradient {
            background: var(--navbar-bg);
            backdrop-filter: blur(18px);
        }

        .glow-text {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            color: #fff;
        }

        .hero-card {
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 12px 36px var(--shadow-main);
            background: linear-gradient(135deg, var(--hero-gradient-start) 0%, var(--hero-gradient-mid) 52%, var(--hero-gradient-end) 100%);
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        .hero-card::before {
            content: "";
            position: absolute;
            inset: -40px auto auto -40px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
        }

        .hero-card::after {
            content: "";
            position: absolute;
            inset: auto -50px -70px auto;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.05) 1px, transparent 0);
            background-size: 36px 36px;
            pointer-events: none;
        }

        .hero-badge {
            background: var(--hero-badge-bg);
            border: 1px solid var(--hero-badge-border);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.3rem 0.8rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            backdrop-filter: blur(8px);
        }

        .hero-title {
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1.25;
            text-shadow: 0 2px 16px rgba(0,0,0,0.15);
        }

        @media (min-width: 768px) {
            .hero-title { font-size: 1.85rem; }
        }

        .hero-text {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.65);
            line-height: 1.55;
        }

        .hero-chips { display: flex; flex-wrap: wrap; gap: 0.4rem; }

        .hero-chip {
            background: var(--hero-chip-bg);
            color: var(--hero-chip-text);
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            backdrop-filter: blur(6px);
        }

        .hero-metric {
            background: var(--hero-metric-bg);
            border: 1px solid var(--hero-metric-border);
            border-radius: 14px;
            padding: 0.75rem 1rem;
            backdrop-filter: blur(10px);
        }

        .hero-metric-label { font-size: 0.65rem; color: rgba(255,255,255,0.55); font-weight: 500; }
        .hero-metric-value { font-size: 1.15rem; font-weight: 700; margin-top: 0.15rem; }

        .btn-hero-primary {
            background: #fff;
            color: #11224f;
            border: none;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.55rem 1.4rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .btn-hero-primary:hover {
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
            transform: translateY(-1px);
            color: #11224f;
        }

        .btn-hero-outline {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.25);
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.55rem 1.4rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .btn-hero-outline:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.4);
            color: #fff;
        }

        .feature-card {
            background: var(--feature-card-bg);
            border: 1px solid var(--feature-card-border);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 32px var(--shadow-main);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px var(--feature-card-hover-shadow);
            border-color: var(--hover-border);
        }
        .feature-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            border-radius: 20px 20px 0 0;
        }
        .feature-card.feature-builder::before { background: linear-gradient(90deg, #3b82f6, #6366f1); }
        .feature-card.feature-ai::before { background: linear-gradient(90deg, #8b5cf6, #a855f7); }
        .feature-card.feature-generator::before { background: linear-gradient(90deg, #10b981, #06b6d4); }
        .feature-card.feature-chat::before { background: linear-gradient(90deg, #f59e0b, #ef4444); }
        .feature-card.feature-calc::before { background: linear-gradient(90deg, #06b6d4, #3b82f6); }
        .feature-card.feature-kb::before { background: linear-gradient(90deg, #ec4899, #f43f5e); }

        .feature-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
        }
        .feature-icon-blue { background: linear-gradient(135deg, rgba(59,130,246,0.14), rgba(99,102,241,0.14)); color: #3b82f6; }
        .feature-icon-purple { background: linear-gradient(135deg, rgba(139,92,246,0.14), rgba(168,85,247,0.14)); color: #8b5cf6; }
        .feature-icon-green { background: linear-gradient(135deg, rgba(16,185,129,0.14), rgba(6,182,212,0.14)); color: #10b981; }
        .feature-icon-orange { background: linear-gradient(135deg, rgba(245,158,11,0.14), rgba(239,68,68,0.14)); color: #f59e0b; }
        .feature-icon-cyan { background: linear-gradient(135deg, rgba(6,182,212,0.14), rgba(59,130,246,0.14)); color: #06b6d4; }
        .feature-icon-pink { background: linear-gradient(135deg, rgba(236,72,153,0.14), rgba(244,63,94,0.14)); color: #ec4899; }

        /* ===== STAT CARDS (EXACT MATCH) ===== */
        .stat-card {
            background: var(--stat-card-bg);
            border: 1px solid var(--stat-card-border);
            border-radius: 16px;
            padding: 1.25rem 1rem;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--stat-value-color);
            margin-bottom: 0.25rem;
            letter-spacing: -0.02em;
        }
        .stat-label {
            font-size: 0.82rem;
            color: var(--stat-label-color);
            font-weight: 500;
        }

        .surface-card {
            background: var(--card-bg);
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 10px 32px var(--shadow-main);
        }

        .form-control {
            border-radius: 14px;
            border-color: var(--form-control-border);
            padding-top: 0.7rem;
            padding-bottom: 0.7rem;
            background: var(--surface-bg);
            color: var(--ink);
            font-size: 0.9rem;
        }
        .form-control:focus {
            border-color: var(--form-control-focus-border);
            box-shadow: 0 0 0 0.25rem var(--form-control-focus-shadow);
            background: var(--surface-bg);
            color: var(--ink);
        }

        .insight-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 0.9rem;
            background: var(--surface-bg);
        }
        .soft-success { background: var(--success-bg); }
        .soft-warning { background: var(--warning-bg); }
        .muted { color: var(--muted); }

        .theme-toggle-btn {
            display: flex; align-items: center; justify-content: center;
            width: 34px; height: 34px;
            border-radius: 10px; border: none;
            background: var(--theme-toggle-bg);
            color: var(--ink);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .theme-toggle-btn:hover { background: var(--theme-toggle-hover); transform: rotate(15deg); }
        .theme-toggle-btn .icon-sun,
        .theme-toggle-btn .icon-moon {
            position: absolute;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        [data-theme="light"] .theme-toggle-btn .icon-sun { opacity: 1; transform: rotate(0deg) scale(1); }
        [data-theme="light"] .theme-toggle-btn .icon-moon { opacity: 0; transform: rotate(90deg) scale(0); }
        [data-theme="dark"] .theme-toggle-btn .icon-sun { opacity: 0; transform: rotate(-90deg) scale(0); }
        [data-theme="dark"] .theme-toggle-btn .icon-moon { opacity: 1; transform: rotate(0deg) scale(1); }

        .theme-icon-wrapper { position: relative; width: 18px; height: 18px; }

        .btn-gradient-blue { background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; border: none; transition: all 0.3s ease; }
        .btn-gradient-blue:hover { box-shadow: 0 6px 20px rgba(59, 130, 246, 0.35); transform: translateY(-2px); color: #fff; }
        .btn-gradient-purple { background: linear-gradient(135deg, #8b5cf6, #a855f7); color: #fff; border: none; transition: all 0.3s ease; }
        .btn-gradient-purple:hover { box-shadow: 0 6px 20px rgba(139, 92, 246, 0.35); transform: translateY(-2px); color: #fff; }
        .btn-gradient-green { background: linear-gradient(135deg, #10b981, #06b6d4); color: #fff; border: none; transition: all 0.3s ease; }
        .btn-gradient-green:hover { box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35); transform: translateY(-2px); color: #fff; }
        .btn-gradient-orange { background: linear-gradient(135deg, #f59e0b, #ef4444); color: #fff; border: none; transition: all 0.3s ease; }
        .btn-gradient-orange:hover { box-shadow: 0 6px 20px rgba(245, 158, 11, 0.35); transform: translateY(-2px); color: #fff; }
        .btn-gradient-cyan { background: linear-gradient(135deg, #06b6d4, #3b82f6); color: #fff; border: none; transition: all 0.3s ease; }
        .btn-gradient-cyan:hover { box-shadow: 0 6px 20px rgba(6, 182, 212, 0.35); transform: translateY(-2px); color: #fff; }
        .btn-gradient-pink { background: linear-gradient(135deg, #ec4899, #f43f5e); color: #fff; border: none; transition: all 0.3s ease; }
        .btn-gradient-pink:hover { box-shadow: 0 6px 20px rgba(236, 72, 153, 0.35); transform: translateY(-2px); color: #fff; }

        .template-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--surface-bg);
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
        }
        .template-card:hover { border-color: var(--hover-border); transform: translateY(-1px); }
        .template-card.active { border-color: #3b82f6; background: rgba(59, 130, 246, 0.04); }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: var(--modal-backdrop);
            backdrop-filter: blur(8px);
            z-index: 200;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-overlay.active { display: flex; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .modal-content-custom {
            background: var(--card-bg);
            border: 1px solid var(--line);
            border-radius: 22px;
            box-shadow: 0 24px 64px var(--shadow-main);
            max-width: 640px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalSlide 0.3s ease;
        }
        @keyframes modalSlide { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }

        .footer-card {
            background: var(--footer-bg);
            border-top: 1px solid var(--footer-border);
            backdrop-filter: blur(12px);
        }

        .lang-switcher { display: flex; gap: 0.35rem; margin-bottom: 0.7rem; }
        .lang-btn {
            background: var(--lang-btn-bg);
            border: 1px solid var(--hero-badge-border);
            color: var(--lang-btn-text);
            font-size: 0.7rem; font-weight: 700;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.2s ease;
            backdrop-filter: blur(6px);
        }
        .lang-btn:hover { background: var(--lang-btn-active); color: var(--lang-btn-active-text); }
        .lang-btn.active { background: var(--lang-btn-active); color: var(--lang-btn-active-text); border-color: rgba(255,255,255,0.3); }

        .footer-lang { display: flex; gap: 0.35rem; }
        .footer-lang-btn {
            background: var(--badge-light-bg);
            border: 1px solid var(--line);
            color: var(--badge-light-text);
            font-size: 0.7rem; font-weight: 600;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .footer-lang-btn:hover { background: var(--gradient-accent-1); border-color: var(--hover-border); }
        .footer-lang-btn.active { background: var(--gradient-accent-1); border-color: #3b82f6; color: #3b82f6; }

        .gradient-text {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .gradient-text-green {
            background: linear-gradient(135deg, #10b981, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .progress-bar-custom { height: 4px; border-radius: 2px; background: var(--line); overflow: hidden; }
        .progress-bar-custom .fill { height: 100%; border-radius: 2px; transition: width 0.6s ease; }
        .fill-blue { background: linear-gradient(90deg, #3b82f6, #6366f1); }
        .fill-purple { background: linear-gradient(90deg, #8b5cf6, #a855f7); }
        .fill-green { background: linear-gradient(90deg, #10b981, #06b6d4); }
        .fill-orange { background: linear-gradient(90deg, #f59e0b, #ef4444); }
        .fill-cyan { background: linear-gradient(90deg, #06b6d4, #3b82f6); }
        .fill-pink { background: linear-gradient(90deg, #ec4899, #f43f5e); }

        .step-indicator {
            width: 30px; height: 30px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.75rem;
            flex-shrink: 0;
        }
        .step-indicator-blue { background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; }
        .step-indicator-purple { background: linear-gradient(135deg, #8b5cf6, #a855f7); color: #fff; }
        .step-indicator-green { background: linear-gradient(135deg, #10b981, #06b6d4); color: #fff; }

        .section-label {
            display: inline-flex; align-items: center; gap: 0.4rem;
            font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em;
            color: #3b82f6;
            margin-bottom: 0.6rem;
        }
        .section-label::after {
            content: ""; flex: 1; height: 1px;
            background: linear-gradient(90deg, var(--line), transparent);
            min-width: 30px;
        }

        .feature-title { font-size: 1rem; font-weight: 700; margin-bottom: 0.4rem; color: var(--ink); }
        .feature-text { font-size: 0.8rem; color: var(--muted); margin-bottom: 0.75rem; line-height: 1.5; }
        .feature-list-item { display: flex; align-items: start; gap: 0.4rem; margin-bottom: 0.3rem; font-size: 0.78rem; color: var(--muted); }

        @media (max-width: 768px) {
            .hero-title { font-size: 1.35rem; }
            .hero-text { font-size: 0.78rem; }
            .stat-value { font-size: 1.25rem; }
        }

        .profile-dropdown {
            animation: slideDown 0.2s ease;
            background: var(--dropdown-bg);
            border: 1px solid var(--dropdown-border);
            border-radius: 16px;
            box-shadow: 0 20px 48px var(--shadow-main);
            backdrop-filter: blur(20px);
            overflow: hidden;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .profile-avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 800; color: #fff;
            flex-shrink: 0;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35);
        }

        .profile-avatar-sm {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 800; color: #fff;
            flex-shrink: 0;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .profile-dropdown-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.6rem 1rem;
            color: var(--ink);
            text-decoration: none;
            font-size: 0.85rem; font-weight: 500;
            transition: all 0.2s ease;
            border: none; background: none;
            width: 100%; cursor: pointer;
        }
        .profile-dropdown-item:hover { background: var(--dropdown-hover); color: #3b82f6; }
        .profile-dropdown-item svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.6; }

        .profile-email-badge {
            display: inline-flex; align-items: center; gap: 0.25rem;
            font-size: 0.7rem; font-weight: 500; color: var(--muted);
            background: var(--gradient-accent-1);
            padding: 0.15rem 0.5rem; border-radius: 999px;
            margin-top: 0.2rem;
        }

        .notification-dropdown {
            animation: slideDown 0.2s ease;
            background: var(--dropdown-bg);
            border: 1px solid var(--dropdown-border);
            border-radius: 16px;
            box-shadow: 0 20px 48px var(--shadow-main);
            backdrop-filter: blur(20px);
            overflow: hidden;
        }

        .notification-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--line);
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .notification-item:hover { background: var(--dropdown-hover); }
        .notification-item:last-child { border-bottom: none; }

        @keyframes msgAppear {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .calc-row {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            border-bottom: 1px solid var(--line);
            font-size: 0.85rem;
        }
        .calc-row:last-child { border-bottom: none; }
        .calc-total {
            font-weight: 700;
            font-size: 1rem;
            color: #3b82f6;
        }

        /* Knowledge Base */
        .kb-search {
            border-radius: 14px;
            border: 1px solid var(--form-control-border);
            padding: 0.7rem 1rem 0.7rem 2.8rem;
            background: var(--surface-bg);
            color: var(--ink);
            font-size: 0.9rem;
            width: 100%;
            outline: none;
        }
        .kb-search:focus { border-color: var(--form-control-focus-border); }
        .kb-search-wrapper { position: relative; }
        .kb-search-wrapper svg {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
        }
        .try-link {
            display: inline-block;
            font-weight: 600;
            font-size: 14px;
            color: #3b82f6;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .try-link:hover {
            color: #2563eb;
            transform: translateX(4px); /* лёгкое движение вправо */
        }
        /* ФИОЛЕТОВЫЙ */
        .try-link-purple {
            color: #8b5cf6;
        }
        .try-link-purple:hover {
            color: #7c3aed;
        }

        /* БИРЮЗОВЫЙ */
        .try-link-teal {
            color: #14b8a6;
        }
        .try-link-teal:hover {
            color: #0f766e;
        }

        /* ПОЛОСКИ СПРАВА (как на скрине) */
        .try-link-purple::before {
            background: #8b5cf6;
        }

        .try-link-teal::before {
            background: #14b8a6;
        }
        .kb-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--line);
            cursor: pointer;
            transition: background 0.2s ease;
            border-radius: 10px;
            margin-bottom: 0.25rem;
        }

        /* БАЗА */
        .try-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.25s ease;
            position: relative;
        }

        /* стрелка */
        .try-link::after {
            content: "→";
            transition: transform 0.25s ease;
        }

        /* hover движение */
        .try-link:hover::after {
            transform: translateX(5px);
        }

        /* ОРАНЖЕВЫЙ (AI чат) */
        .try-link-orange {
            color: #f59e0b;
        }
        .try-link-orange:hover {
            color: #d97706;
        }

        /* СИНИЙ (калькулятор) */
        .try-link-blue {
            color: #06b6d4;
        }
        .try-link-blue:hover {
            color: #0891b2;
        }
        .hero::after {
            content: "";
            position: absolute;
            right: -100px;
            bottom: -100px;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: 40px;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 15px;
        }

        .hero p {
            color: rgba(255,255,255,0.8);
            max-width: 600px;
        }
        .hero-title {
            font-size: 44px;
        }

        .hero-text {
            font-size: 12px;
        }
        .hero-chips {
            margin-top: 20px;
        }

        .hero-chip {
            margin-right: 10px;
            margin-bottom: 10px;
            padding: 8px 14px;
        }
        .hero-card {
            padding-left: 40px;
            padding-right: 40px;
        }

        .stat small {
            display: block;
            opacity: 0.7;
        }
        .stat strong {
            font-size: 18px;
        }
        /* РОЗОВЫЙ (база знаний) */
        .try-link-pink {
            color: #ec4899;
        }
        .try-link-pink:hover {
            color: #db2777;
        }
        .kb-tag-blue { background: rgba(59,130,246,0.12); color: #3b82f6; }
        .kb-tag-purple { background: rgba(139,92,246,0.12); color: #8b5cf6; }
        .kb-tag-green { background: rgba(16,185,129,0.12); color: #10b981; }
    </style>
</head>
<body>
@php
    $currentUser = auth()->user();
    $userName = $currentUser?->name ?: 'Пользователь';
    $userEmail = $currentUser?->email ?: 'email@example.com';
    $nameParts = preg_split('/\s+/u', trim($userName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $userInitials = collect($nameParts)
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $userInitials = $userInitials !== '' ? $userInitials : 'П';
@endphp
<nav class="fixed top-0 left-0 right-0 z-50 card-gradient border-b border-slate-700" style="border-color: var(--navbar-border);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo -->
            <div class="flex items-center space-x-3 cursor-pointer" onclick="window.location.reload()">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-xl font-bold glow-text">LegalAI Pro</span>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('welcome') }}" class="text-slate-300 hover:text-white transition text-sm font-medium px-3 py-2 rounded-lg hover:bg-slate-800/30">
                    {{ __('ui.nav.home') }}
                </a>
                <a href="{{ route('dashboard') }}" class="text-slate-300 hover:text-white transition text-sm font-medium px-3 py-2 rounded-lg hover:bg-slate-800/30">
                    {{ __('ui.nav.dashboard') }}
                </a>

                <x-language-switcher />

                <!-- Notifications -->
                <div class="relative">
                    <button onclick="toggleNotifications()" class="text-slate-300 hover:text-white transition relative p-2 rounded-lg hover:bg-slate-800/30" title="{{ __('ui.nav.notifications.title') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full text-[10px] text-white flex items-center justify-center font-bold">3</span>
                    </button>
                    <div id="notificationPanel" class="hidden notification-dropdown absolute right-0 mt-2 w-80 z-50">
                        <div class="p-4 border-b" style="border-color: var(--line);">
                            <h4 class="font-semibold text-sm" style="color: var(--ink);">{{ __('ui.nav.notifications.header') }}</h4>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="notification-item">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm" style="color: var(--ink);">{{ __('ui.nav.notifications.item1.text') }}</p>
                                        <p class="text-xs mt-1" style="color: var(--muted);">{{ __('ui.nav.notifications.item1.time') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-orange-500 rounded-full mt-1.5 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm" style="color: var(--ink);">{{ __('ui.nav.notifications.item2.text') }}</p>
                                        <p class="text-xs mt-1" style="color: var(--muted);">{{ __('ui.nav.notifications.item2.time') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-item">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mt-1.5 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm" style="color: var(--ink);">{{ __('ui.nav.notifications.item3.text') }}</p>
                                        <p class="text-xs mt-1" style="color: var(--muted);">{{ __('ui.nav.notifications.item3.time') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Theme Toggle -->
                <button onclick="toggleTheme()" class="theme-toggle-btn relative" title="{{ __('ui.nav.toggle_theme') }}">
                    <span class="theme-icon-wrapper">
                        <svg class="icon-sun w-5 h-5 text-yellow-400 absolute inset-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3V1m0 22v-2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42M12 7a5 5 0 100 10 5 5 0 000-10z"/></svg>
                        <svg class="icon-moon w-5 h-5 text-blue-300 absolute inset-0" fill="currentColor" viewBox="0 0 24 24"><path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                    </span>
                </button>

                <!-- Profile -->
                <div class="relative" id="profileContainer">
                    <button onclick="toggleProfile()" class="flex items-center space-x-2 hover:bg-slate-800/50 rounded-lg px-2 py-1.5 transition" id="profileButton">
                        <div class="profile-avatar-sm">{{ $userInitials }}</div>
                        <div class="hidden lg:block text-left">
                            <div class="text-sm font-medium text-white leading-tight">{{ $userName }}</div>
                            <div class="text-[11px] text-slate-400">{{ $userEmail }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 ml-1 transition-transform duration-200" id="profileArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="profileDropdown" class="hidden profile-dropdown absolute right-0 mt-2 w-72 z-50">
                        <div class="p-4 border-b" style="border-color: var(--line);">
                            <div class="flex items-center space-x-3">
                                <div class="profile-avatar">{{ $userInitials }}</div>
                                <div>
                                    <div class="text-sm font-semibold" style="color: var(--ink);">{{ $userName }}</div>
                                    <div class="profile-email-badge">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        {{ $userEmail }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            <a href="{{ route('profile.edit') }}" class="profile-dropdown-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ __('ui.nav.profile.my_profile') }}
                            </a>
                            <a href="#" class="profile-dropdown-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ __('ui.nav.profile.settings') }}
                            </a>
                            <a href="#" class="profile-dropdown-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                {{ __('ui.nav.profile.documents') }}
                            </a>
                        </div>
                        <div class="border-t py-2" style="border-color: var(--line);">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-red-400 hover:text-red-300 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    {{ __('ui.nav.profile.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button onclick="toggleMobileMenu()" class="md:hidden text-slate-300 hover:text-white p-1" title="{{ __('ui.nav.menu') }}">
                <svg id="mobileMenuIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden pb-4 border-t mt-2 pt-4" style="border-color: var(--navbar-border);">
            <div class="flex flex-col space-y-2">
                <a href="#" class="text-slate-300 hover:text-white transition px-3 py-2 rounded-lg hover:bg-slate-800/50 text-sm">
                    {{ __('ui.nav.home') }}
                </a>
                <a href="#features" class="text-slate-300 hover:text-white transition px-3 py-2 rounded-lg hover:bg-slate-800/50 text-sm">
                    {{ __('ui.nav.features') }}
                </a>
                <div class="flex items-center justify-between px-3 py-2">
                    <div class="flex items-center space-x-3">
                        <div class="profile-avatar-sm">{{ $userInitials }}</div>
                        <div>
                            <div class="text-sm font-medium text-white">{{ $userName }}</div>
                            <div class="text-xs text-slate-400">{{ $userEmail }}</div>
                        </div>
                    </div>
                    <button onclick="toggleTheme()" class="theme-toggle-btn relative" title="{{ __('ui.nav.toggle_theme') }}">
                        <span class="theme-icon-wrapper">
                            <svg class="icon-sun w-5 h-5 text-yellow-400 absolute inset-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3V1m0 22v-2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42M12 7a5 5 0 100 10 5 5 0 000-10z"/></svg>
                            <svg class="icon-moon w-5 h-5 text-blue-300 absolute inset-0" fill="currentColor" viewBox="0 0 24 24"><path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>    <section class="hero-card my-5 mx-4 mx-lg-5">
    <div class="hero-pattern"></div>
    <div class="max-w-7xl mx-auto px-1 sm:px-5 lg:px-7 py-5 lg:py-8 position-relative z-10" style="z-index: 1;">
        <div class="row g-3 align-items-center">
            <div class="col-lg-7">
                <!-- Переключатель языков (Laravel-роуты) -->
                <div class="lang-switcher mb-3">
                    <a href="{{ route('lang.switch', ['locale' => 'ru']) }}" class="lang-btn {{ app()->getLocale() === 'ru' ? 'active' : '' }}">RU</a>
                    <a href="{{ route('lang.switch', ['locale' => 'en']) }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                    <a href="{{ route('lang.switch', ['locale' => 'tj']) }}" class="lang-btn {{ app()->getLocale() === 'tj' ? 'active' : '' }}">TJ</a>
                </div>

                <div class="hero-badge mb-2"><span>⚡</span> {{ __('ui.hero_badge') }}</div>
                <h1 class="hero-title mb-2">{{ __('ui.hero_title') }}</h1>
                <p class="hero-text mb-3">{{ __('ui.hero_text') }}</p>

                <div class="d-flex flex-wrap gap-2 mb-2">
                    <button onclick="openModal('builder')" class="btn-hero-primary">{{ __('ui.hero_cta_start') }}</button>
                    <a href="#features" class="btn-hero-outline">{{ __('ui.hero_cta_learn') }}</a>
                </div>

                <div class="hero-chips">
                    <span class="hero-chip">{{ __('ui.hero_chip_builder') }}</span>
                    <span class="hero-chip">{{ __('ui.hero_chip_ai') }}</span>
                    <span class="hero-chip">{{ __('ui.hero_chip_generator') }}</span>
                    <span class="hero-chip">{{ __('ui.hero_chip_chat') }}</span>
                    <span class="hero-chip">{{ __('ui.hero_chip_calc') }}</span>
                    <span class="hero-chip">{{ __('ui.hero_chip_kb') }}</span>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="hero-metric">
                            <div class="hero-metric-label">{{ __('ui.stat_docs_label') }}</div>
                            <div class="hero-metric-value" id="hero-docs">12,480+</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hero-metric">
                            <div class="hero-metric-label">{{ __('ui.stat_risks_label') }}</div>
                            <div class="hero-metric-value">8,320</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hero-metric">
                            <div class="hero-metric-label">{{ __('ui.stat_templates_label') }}</div>
                            <div class="hero-metric-value">45+</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hero-metric">
                            <div class="hero-metric-label">{{ __('ui.stat_accuracy_label') }}</div>
                            <div class="hero-metric-value">97.3%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>

    <!-- 6 инструментов -->
<section id="features" class="mb-4">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
        <div class="text-center mb-4">
            <span class="section-label mx-auto">{{ __('ui.features_label') }}</span>
            <h2 class="h3 fw-bold mb-2">{{ __('ui.features_title') }}</h2>
            <p class="muted small">{{ __('ui.features_text') }}</p>
        </div>
        <div class="row g-3">
            <!-- 1. Конструктор -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card feature-builder h-100" onclick="openModal('builder')">
                    <div class="feature-icon feature-icon-blue">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <span class="badge rounded-pill mb-2 px-2 py-1 fw-semibold" style="background: var(--chip-bg); color: var(--chip-text); font-size: 0.7rem;">{{ __('ui.builder_badge') }}</span>
                    <h3 class="feature-title">{{ __('ui.builder_title') }}</h3>
                    <p class="feature-text">{{ __('ui.builder_text') }}</p>
                    <ul class="list-unstyled mb-3">
                        <li class="feature-list-item"><svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.builder_feat1') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.builder_feat2') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.builder_feat3') }}</span></li>
                    </ul>
                    <a href="{{ route('contract.create') }}" class="try-link">{{ __('ui.try_now') }}</a>
                </div>
            </div>

            <!-- 2. AI Анализ -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card feature-ai h-100" onclick="openModal('ai-analysis')">
                    <div class="feature-icon feature-icon-purple">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <span class="badge rounded-pill mb-2 px-2 py-1 fw-semibold" style="background: rgba(139,92,246,0.12); color: #8b5cf6; font-size: 0.7rem;">{{ __('ui.ai_badge') }}</span>
                    <h3 class="feature-title">{{ __('ui.ai_title') }}</h3>
                    <p class="feature-text">{{ __('ui.ai_text') }}</p>
                    <ul class="list-unstyled mb-3">
                        <li class="feature-list-item"><svg class="w-4 h-4 text-purple-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.ai_feat1') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-purple-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.ai_feat2') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-purple-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.ai_feat3') }}</span></li>
                    </ul>
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route('contract.form') }}" class="try-link try-link-purple">{{ __('ui.try_now') }}</a>
                        <div class="progress-bar-custom" style="width: 60px;"><div class="fill fill-purple" style="width: 92%;"></div></div>
                    </div>
                </div>
            </div>

            <!-- 3. Генератор -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card feature-generator h-100" onclick="openModal('generator')">
                    <div class="feature-icon feature-icon-green">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="badge rounded-pill mb-2 px-2 py-1 fw-semibold" style="background: rgba(16,185,129,0.12); color: #10b981; font-size: 0.7rem;">{{ __('ui.gen_badge') }}</span>
                    <h3 class="feature-title">{{ __('ui.gen_title') }}</h3>
                    <p class="feature-text">{{ __('ui.gen_text') }}</p>
                    <ul class="list-unstyled mb-3">
                        <li class="feature-list-item"><svg class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.gen_feat1') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.gen_feat2') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.gen_feat3') }}</span></li>
                    </ul>
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route('contract.generate') }}" class="try-link try-link-teal">{{ __('ui.try_now') }}</a>
                        <div class="progress-bar-custom" style="width: 60px;"><div class="fill fill-green" style="width: 78%;"></div></div>
                    </div>
                </div>
            </div>

            <!-- 4. AI Чат -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card feature-chat h-100" onclick="openModal('ai-chat')">
                    <div class="feature-icon feature-icon-orange">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <span class="badge rounded-pill mb-2 px-2 py-1 fw-semibold" style="background: rgba(245,158,11,0.12); color: #f59e0b; font-size: 0.7rem;">{{ __('ui.chat_badge') }}</span>
                    <h3 class="feature-title">{{ __('ui.chat_title') }}</h3>
                    <p class="feature-text">{{ __('ui.chat_text') }}</p>
                    <ul class="list-unstyled mb-3">
                        <li class="feature-list-item"><svg class="w-4 h-4 text-orange-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.chat_feat1') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-orange-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.chat_feat2') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-orange-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.chat_feat3') }}</span></li>
                    </ul>
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route('tasks.chat') }}" class="try-link try-link-orange">{{ __('ui.try_now') }}</a>
                        <div class="progress-bar-custom" style="width: 60px;"><div class="fill fill-orange" style="width: 88%;"></div></div>
                    </div>
                </div>
            </div>

            <!-- 5. Калькулятор -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card feature-calc h-100" onclick="openModal('calculator')">
                    <div class="feature-icon feature-icon-cyan">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="badge rounded-pill mb-2 px-2 py-1 fw-semibold" style="background: rgba(6,182,212,0.12); color: #06b6d4; font-size: 0.7rem;">{{ __('ui.calc_badge') }}</span>
                    <h3 class="feature-title">{{ __('ui.calc_title') }}</h3>
                    <p class="feature-text">{{ __('ui.calc_text') }}</p>
                    <ul class="list-unstyled mb-3">
                        <li class="feature-list-item"><svg class="w-4 h-4 text-cyan-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.calc_feat1') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-cyan-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.calc_feat2') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-cyan-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.calc_feat3') }}</span></li>
                    </ul>
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route('tasks.calc') }}" class="try-link try-link-blue">{{ __('ui.try_now') }}</a>
                        <div class="progress-bar-custom" style="width: 60px;"><div class="fill fill-cyan" style="width: 85%;"></div></div>
                    </div>
                </div>
            </div>

            <!-- 6. База знаний -->
            <div class="col-lg-4 col-md-6">
                <div class="feature-card feature-kb h-100" onclick="openModal('knowledge-base')">
                    <div class="feature-icon feature-icon-pink">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="badge rounded-pill mb-2 px-2 py-1 fw-semibold" style="background: rgba(236,72,153,0.12); color: #ec4899; font-size: 0.7rem;">{{ __('ui.kb_badge') }}</span>
                    <h3 class="feature-title">{{ __('ui.kb_title') }}</h3>
                    <p class="feature-text">{{ __('ui.kb_text') }}</p>
                    <ul class="list-unstyled mb-3">
                        <li class="feature-list-item"><svg class="w-4 h-4 text-pink-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.kb_feat1') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-pink-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.kb_feat2') }}</span></li>
                        <li class="feature-list-item"><svg class="w-4 h-4 text-pink-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg><span>{{ __('ui.kb_feat3') }}</span></li>
                    </ul>
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route('contract.codex') }}" class="try-link try-link-pink">{{ __('ui.try_now') }}</a>
                        <div class="progress-bar-custom" style="width: 60px;"><div class="fill fill-pink" style="width: 72%;"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- Как работает -->
<section id="how-it-works" class="mb-4">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
        <div class="surface-card p-3 p-lg-4">
            <div class="text-center mb-3">
                <span class="section-label mx-auto">{{ __('ui.process_label') }}</span>
                <h2 class="h4 fw-bold mb-2">{{ __('ui.process_title') }}</h2>
                <p class="muted small">{{ __('ui.process_text') }}</p>
            </div>
            <div class="row g-3 align-items-start">
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-2">
                        <div class="step-indicator step-indicator-blue">1</div>
                        <div>
                            <h5 class="fw-bold mb-1 small" style="color: var(--ink);">{{ __('ui.step1_title') }}</h5>
                            <p class="muted mb-0" style="font-size: 0.78rem;">{{ __('ui.step1_text') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-2">
                        <div class="step-indicator step-indicator-purple">2</div>
                        <div>
                            <h5 class="fw-bold mb-1 small" style="color: var(--ink);">{{ __('ui.step2_title') }}</h5>
                            <p class="muted mb-0" style="font-size: 0.78rem;">{{ __('ui.step2_text') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-2">
                        <div class="step-indicator step-indicator-green">3</div>
                        <div>
                            <h5 class="fw-bold mb-1 small" style="color: var(--ink);">{{ __('ui.step3_title') }}</h5>
                            <p class="muted mb-0" style="font-size: 0.78rem;">{{ __('ui.step3_text') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- Статистика -->
<section id="stats" class="mb-4">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
        <div class="row g-3 justify-content-center">
            <div class="col-6 col-lg-3">
                <div class="stat-card h-100 position-relative overflow-hidden">
                    <div class="stat-value" id="stat-docs">0</div>
                    <div class="stat-label">{{ __('ui.stat_docs') }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card h-100 position-relative overflow-hidden">
                    <div class="stat-value" id="stat-users">0</div>
                    <div class="stat-label">{{ __('ui.stat_users') }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card h-100 position-relative overflow-hidden">
                    <div class="stat-value" id="stat-risks">0</div>
                    <div class="stat-label">{{ __('ui.stat_risks') }}</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card h-100 position-relative overflow-hidden">
                    <div class="stat-value" id="stat-time">0</div>
                    <div class="stat-label">{{ __('ui.stat_time') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- CTA -->
<section class="mb-4">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
        <div class="hero-card p-3 p-lg-4 text-center position-relative overflow-hidden" style="z-index: 1;">
            <div class="hero-pattern"></div>

            <h2 class="h4 fw-bold mb-2 text-white">{{ __('ui.cta_title') }}</h2>
            <p class="small mb-3" style="color: rgba(255,255,255,0.65); max-width: 460px; margin: 0 auto;">
                {{ __('ui.cta_text') }}
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <button onclick="openModal('builder')" class="btn-hero-primary">{{ __('ui.cta_btn1') }}</button>
                <button onclick="openModal('ai-chat')" class="btn-hero-outline">{{ __('ui.cta_btn2') }}</button>
            </div>
        </div>
    </div>
</section></main>

<footer class="footer-card mt-4">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="d-flex items-center space-x-2 mb-1">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="fw-bold small" style="color: var(--ink);">LegalAI Pro</span>
                </div>
                <p class="small mb-0" style="color: var(--muted);" data-i18n="footer_desc">AI-платформа для создания и анализа юридических документов</p>
            </div>
            <div class="col-md-6 text-md-end d-flex align-items-center justify-content-md-end gap-3">
                <div class="footer-lang">
                    <button class="footer-lang-btn active" onclick="switchLang('ru')" data-lang="ru">RU</button>
                    <button class="footer-lang-btn" onclick="switchLang('en')" data-lang="en">EN</button>
                    <button class="footer-lang-btn" onclick="switchLang('tj')" data-lang="tj">TJ</button>
                </div>
                <p class="small mb-0" style="color: var(--muted);">© 2025 LegalAI Pro</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<script>
    // ===== I18n =====
    const i18nData = {
        ru: {
            hero_badge: "AI-платформа нового поколения",
            hero_title: "Юридические документы с интеллектом AI",
            hero_text: "Конструктор договоров, AI-анализ рисков, генератор документов, AI-чат, калькулятор и база знаний — шесть мощных инструментов в одной платформе.",
            hero_cta_start: "Начать бесплатно",
            hero_cta_learn: "Узнать больше",
            hero_chip_builder: "Конструктор",
            hero_chip_ai: "AI-анализ",
            hero_chip_generator: "Генератор",
            hero_chip_chat: "AI Чат",
            hero_chip_calc: "Калькулятор",
            hero_chip_kb: "База знаний",
            stat_docs_label: "Документов создано",
            stat_risks_label: "Рисков выявлено",
            stat_templates_label: "Шаблонов",
            stat_accuracy_label: "Точность AI",
            features_label: "Шесть инструментов",
            features_title: "Всё для работы с документами",
            features_text: "Полный набор инструментов для создания, анализа и работы с юридическими документами",
            builder_badge: "Конструктор", builder_title: "Конструктор договоров", builder_text: "Интерактивный сборщик документов с выбором шаблонов.",
            builder_feat1: "3 типа договоров", builder_feat2: "Живой предпросмотр", builder_feat3: "Экспорт Word, PDF, TXT",
            ai_badge: "AI Анализ", ai_title: "AI анализ договоров", ai_text: "Загрузите договор и получите мгновенный анализ рисков.",
            ai_feat1: "Оценка уровня риска", ai_feat2: "Выявление опасных пунктов", ai_feat3: "Рекомендации по правкам",
            gen_badge: "Генератор", gen_title: "Генератор договоров", gen_text: "Опишите что вам нужно и получите готовый договор.",
            gen_feat1: "Генерация по описанию", gen_feat2: "45+ шаблонов", gen_feat3: "Юридическая проверка",
            chat_badge: "AI Чат", chat_title: "Юридический AI-чат", chat_text: "Задайте любой юридический вопрос и получите ответ.",
            chat_feat1: "Консультации 24/7", chat_feat2: "Ссылки на законы", chat_feat3: "История диалогов",
            calc_badge: "Калькулятор", calc_title: "Юридический калькулятор", calc_text: "Рассчитайте госпошлины, пени, неустойки.",
            calc_feat1: "Госпошлины", calc_feat2: "Пени и неустойки", calc_feat3: "Сроки давности",
            kb_badge: "База знаний", kb_title: "База знаний", kb_text: "Справочник законов и кодексов с быстрым поиском.",
            kb_feat1: "Кодексы и законы", kb_feat2: "Судебная практика", kb_feat3: "Быстрый поиск",
            try_now: "Попробовать →",
            process_label: "Процесс", process_title: "Как это работает", process_text: "Три простых шага",
            step1_title: "Выберите инструмент", step1_text: "6 инструментов на любой случай.",
            step2_title: "Заполните параметры", step2_text: "Введите данные и условия.",
            step3_title: "Получите результат", step3_text: "Документ, ответ или расчёт.",
            cta_title: "Готовы начать?", cta_text: "Присоединяйтесь к тысячам юристов",
            cta_btn1: "Создать договор", cta_btn2: "Задать вопрос AI",
            footer_desc: "AI-платформа для юридических документов",
            modal_builder_title: "Конструктор договоров", modal_builder_text: "Выберите шаблон, заполните параметры",
            modal_ai_title: "AI анализ договоров", modal_ai_text: "Загрузите документ для анализа рисков",
            modal_gen_title: "Генератор договоров", modal_gen_text: "Опишите и получите готовый договор"
        },
        en: {
            hero_badge: "Next-gen AI platform", hero_title: "Legal documents powered by AI",
            hero_text: "Contract builder, AI risk analysis, document generator, AI chat, calculator and knowledge base — six powerful tools.",
            hero_cta_start: "Start free", hero_cta_learn: "Learn more",
            hero_chip_builder: "Builder", hero_chip_ai: "AI Analysis", hero_chip_generator: "Generator",
            hero_chip_chat: "AI Chat", hero_chip_calc: "Calculator", hero_chip_kb: "Knowledge Base",
            stat_docs_label: "Documents created", stat_risks_label: "Risks detected", stat_templates_label: "Templates", stat_accuracy_label: "AI accuracy",
            features_label: "Six tools", features_title: "Everything for document work", features_text: "Complete toolkit for legal documents",
            builder_badge: "Builder", builder_title: "Contract Builder", builder_text: "Interactive document builder with templates.",
            builder_feat1: "3 contract types", builder_feat2: "Live preview", builder_feat3: "Export Word, PDF, TXT",
            ai_badge: "AI Analysis", ai_title: "AI Contract Analysis", ai_text: "Upload a contract for instant risk analysis.",
            ai_feat1: "Risk assessment", ai_feat2: "Dangerous clauses", ai_feat3: "Recommendations",
            gen_badge: "Generator", gen_title: "Contract Generator", gen_text: "Describe and get a ready contract.",
            gen_feat1: "Generation from text", gen_feat2: "45+ templates", gen_feat3: "Legal check",
            chat_badge: "AI Chat", chat_title: "Legal AI Chat", chat_text: "Ask any legal question and get an answer.",
            chat_feat1: "24/7 consultations", chat_feat2: "Law references", chat_feat3: "Chat history",
            calc_badge: "Calculator", calc_title: "Legal Calculator", calc_text: "Calculate fees, penalties, deadlines.",
            calc_feat1: "Court fees", calc_feat2: "Penalties", calc_feat3: "Limitation periods",
            kb_badge: "Knowledge Base", kb_title: "Knowledge Base", kb_text: "Laws and codes with quick search.",
            kb_feat1: "Codes and laws", kb_feat2: "Court practice", kb_feat3: "Quick search",
            try_now: "Try now →", process_label: "Process", process_title: "How it works", process_text: "Three simple steps",
            step1_title: "Choose a tool", step1_text: "6 tools for any situation.", step2_title: "Fill parameters", step2_text: "Enter data and conditions.",
            step3_title: "Get results", step3_text: "Document, answer or calculation.",
            cta_title: "Ready to start?", cta_text: "Join thousands of lawyers", cta_btn1: "Create contract", cta_btn2: "Ask AI",
            footer_desc: "AI platform for legal documents",
            modal_builder_title: "Contract Builder", modal_builder_text: "Choose template, fill parameters",
            modal_ai_title: "AI Contract Analysis", modal_ai_text: "Upload document for risk analysis",
            modal_gen_title: "Contract Generator", modal_gen_text: "Describe and get a ready contract"
        },
        tj: {
            hero_badge: "Платформаи насли нав бо AI", hero_title: "Ҳуҷҷатҳои ҳуқуқӣ бо зеҳни сунъӣ",
            hero_text: "Сохтмони шартномаҳо, таҳлили хавфҳои AI, генератор, чати AI, калькулятор ва базаи дониш.",
            hero_cta_start: "Ройгон оғоз кунед", hero_cta_learn: "Бештар бидонед",
            hero_chip_builder: "Сохтмон", hero_chip_ai: "Таҳлили AI", hero_chip_generator: "Генератор",
            hero_chip_chat: "Чати AI", hero_chip_calc: "Калькулятор", hero_chip_kb: "Базаи дониш",
            stat_docs_label: "Ҳуҷҷатҳо сохта шуданд", stat_risks_label: "Хавфҳо ошкор шуданд", stat_templates_label: "Шаблонҳо", stat_accuracy_label: "Дақиқии AI",
            features_label: "Шаш абзор", features_title: "Ҳама чиз барои кор бо ҳуҷҷатҳо", features_text: "Маҷмӯи пурраи абзорҳо",
            builder_badge: "Сохтмон", builder_title: "Сохтмони шартномаҳо", builder_text: "Сохтмони интерактивии ҳуҷҷатҳо.",
            builder_feat1: "3 намуди шартнома", builder_feat2: "Пешнамоиши зинда", builder_feat3: "Содирот Word, PDF, TXT",
            ai_badge: "Таҳлили AI", ai_title: "Таҳлили шартномаҳо бо AI", ai_text: "Шартномаро бор кунед.",
            ai_feat1: "Арзёбии хавф", ai_feat2: "Бандҳои хавфнок", ai_feat3: "Тавсияҳо",
            gen_badge: "Генератор", gen_title: "Генератори шартномаҳо", gen_text: "Тавсиф кунед ва шартнома гиред.",
            gen_feat1: "Генератсия", gen_feat2: "45+ шаблонҳо", gen_feat3: "Санҷиши ҳуқуқӣ",
            chat_badge: "Чати AI", chat_title: "Чати ҳуқуқии AI", chat_text: "Саволи ҳуқуқӣ пурсед.",
            chat_feat1: "Маслиҳат 24/7", chat_feat2: "Истинод ба қонунҳо", chat_feat3: "Таърихи гуфтугӯ",
            calc_badge: "Калькулятор", calc_title: "Калькулятори ҳуқуқӣ", calc_text: "Ҳисоби боҷҳо, пеняҳо.",
            calc_feat1: "Боҷҳои судӣ", calc_feat2: "Пеняҳо", calc_feat3: "Мӯҳлатҳо",
            kb_badge: "Базаи дониш", kb_title: "Базаи дониш", kb_text: "Қонунҳо ва кодексҳо.",
            kb_feat1: "Кодексҳо", kb_feat2: "Амалияи судӣ", kb_feat3: "Ҷустуҷӯи зуд",
            try_now: "Санҷед →", process_label: "Раванд", process_title: "Чӣ тавр кор мекунад", process_text: "Се қадами оддӣ",
            step1_title: "Абзорро интихоб кунед", step1_text: "6 абзор.", step2_title: "Параметрҳоро пур кунед", step2_text: "Маълумотро ворид кунед.",
            step3_title: "Натиҷаро гиред", step3_text: "Ҳуҷҷат ё ҷавоб.",
            cta_title: "Омодаед?", cta_text: "Ба ҳуқуқшиносон ҳамроҳ шавед", cta_btn1: "Шартнома эҷод кунед", cta_btn2: "Савол пурсед",
            footer_desc: "Платформаи AI барои ҳуҷҷатҳои ҳуқуқӣ",
            modal_builder_title: "Сохтмони шартномаҳо", modal_builder_text: "Шаблонро интихоб кунед",
            modal_ai_title: "Таҳлили шартномаҳо бо AI", modal_ai_text: "Ҳуҷҷатро бор кунед",
            modal_gen_title: "Генератори шартномаҳо", modal_gen_text: "Тавсиф кунед ва гиред"
        }
    };

    let currentLang = 'ru';

    function switchLang(lang) {
        currentLang = lang;
        document.documentElement.lang = lang;
        localStorage.setItem('legalai-lang', lang);
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (i18nData[lang] && i18nData[lang][key]) el.textContent = i18nData[lang][key];
        });
        document.querySelectorAll('.lang-btn, .footer-lang-btn').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
        });
    }

    function initLang() {
        const saved = localStorage.getItem('legalai-lang');
        if (saved && i18nData[saved]) switchLang(saved); else switchLang('ru');
    }

    function initTheme() {
        const saved = localStorage.getItem('legalai-theme');
        if (saved) document.documentElement.setAttribute('data-theme', saved);
        else document.documentElement.setAttribute('data-theme', window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    }

    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('legalai-theme', next);
    }

    initTheme();
    initLang();

    function toggleMobileMenu() {
        document.getElementById('mobileMenu').classList.toggle('hidden');
    }

    // ===== Profile =====
    function toggleProfile() {
        const dropdown = document.getElementById('profileDropdown');
        const arrow = document.getElementById('profileArrow');
        const isOpen = !dropdown.classList.contains('hidden');
        if (isOpen) { dropdown.classList.add('hidden'); if(arrow) arrow.style.transform = 'rotate(0deg)'; }
        else { dropdown.classList.remove('hidden'); if(arrow) arrow.style.transform = 'rotate(180deg)'; }
    }

    document.addEventListener('click', function(e) {
        const pc = document.getElementById('profileContainer');
        const pd = document.getElementById('profileDropdown');
        const pa = document.getElementById('profileArrow');
        if (pc && !pc.contains(e.target)) { pd.classList.add('hidden'); if(pa) pa.style.transform = 'rotate(0deg)'; }
    });

    // ===== Notifications =====
    function toggleNotifications() {
        document.getElementById('notificationPanel').classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        const np = document.getElementById('notificationPanel');
        const nb = e.target.closest('button[onclick="toggleNotifications()"]');
        if (np && !np.contains(e.target) && !nb) np.classList.add('hidden');
    });

    // ===== Modals =====
    function openModal(name) {
        const modal = document.getElementById('modal-' + name);
        if (modal) { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
    }
    function closeModal(name) {
        const modal = document.getElementById('modal-' + name);
        if (modal) { modal.classList.remove('active'); document.body.style.overflow = ''; }
    }
    function closeModalOutside(event, name) {
        if (event.target.classList.contains('modal-overlay')) closeModal(name);
    }

    // ===== Builder =====
    const builderState = { template: 'service' };
    const templateLabels = { service: 'Договор услуг', supply: 'Договор поставки', nda: 'NDA' };
    const templateRoles = {
        service: { roleA: 'Заказчик', roleB: 'Исполнитель' },
        supply: { roleA: 'Покупатель', roleB: 'Поставщик' },
        nda: { roleA: 'Раскрывающая сторона', roleB: 'Принимающая сторона' }
    };

    function selectTemplate(el) {
        document.querySelectorAll('#modal-builder .template-card').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        builderState.template = el.dataset.template;
    }

    function formatAmount(val) { return new Intl.NumberFormat('ru-RU').format(Number(val || 0)) + ' ₽'; }
    function formatDate(val) { if (!val) return 'не указана'; return new Intl.DateTimeFormat('ru-RU').format(new Date(val)); }

    function previewBuilder() {
        const t = builderState.template, roles = templateRoles[t], label = templateLabels[t];
        const num = document.getElementById('b-contractNumber').value || 'б/н';
        const city = document.getElementById('b-contractCity').value || 'не указан';
        const customer = document.getElementById('b-customer').value || 'Сторона 1';
        const contractor = document.getElementById('b-contractor').value || 'Сторона 2';
        const amount = document.getElementById('b-amount').value || '0';
        const startD = document.getElementById('b-startDate').value;
        const endD = document.getElementById('b-endDate').value;

        const text = `${label} № ${num}\nг. ${city}\n${formatDate(startD)}\n\n${customer}, «${roles.roleA}», и ${contractor}, «${roles.roleB}»:\n\n1. Предмет\n${roles.roleB} обязуется выполнить работы, ${roles.roleA} — принять и оплатить.\n\n2. Цена: ${formatAmount(amount)}\nОплата в течение 5 дней.\n\n3. Ответственность: неустойка 0,1%/день\n\n4. Срок: с ${formatDate(startD)} до ${formatDate(endD)}\n\n5. Реквизиты\n${roles.roleA}: ${customer}\n${roles.roleB}: ${contractor}`;

        document.getElementById('builder-preview-text').textContent = text;
        document.getElementById('builder-preview').style.display = 'block';
    }

    function downloadContract(format) {
        const text = document.getElementById('builder-preview-text').textContent;
        if (!text) return;
        if (format === 'txt') { const b = new Blob([text], {type:'text/plain;charset=utf-8'}); const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = 'contract.txt'; a.click(); }
        else if (format === 'word') { const html = `<html><head><meta charset="UTF-8"><style>body{font-family:serif;font-size:12pt;line-height:1.6;margin:24px}</style></head><body><pre style="white-space:pre-wrap">${text.replace(/&/g,'&amp;').replace(/</g,'&lt;')}</pre></body></html>`; const b = new Blob(['\ufeff', html], {type:'application/msword'}); const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = 'contract.doc'; a.click(); }
        else if (format === 'pdf' && window.html2pdf) { const el = document.createElement('div'); el.style.cssText = 'padding:28px;background:#fff;font-family:serif;font-size:16px;line-height:1.7;white-space:pre-wrap'; el.textContent = text; html2pdf().set({margin:[12,12,12,12],filename:'contract.pdf',html2canvas:{scale:2},jsPDF:{unit:'mm',format:'a4'}}).from(el).save(); }
    }

    // ===== AI Analysis =====
    function analyzeContract() {
        const text = document.getElementById('ai-contract-text').value.trim();
        if (!text) { alert('Введите текст договора'); return; }
        const btn = document.getElementById('analyze-btn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> ...'; btn.disabled = true;
        setTimeout(() => {
            const risks = [], recommendations = [];
            if (text.includes('50%')) { risks.push({level:'high',text:'Высокий штраф (50%) — суд может снизить.'}); recommendations.push('Уменьшите штраф до 0,1%-1%/день.'); }
            if (text.includes('30 дней') || text.includes('30 банковских')) { risks.push({level:'medium',text:'Длительный срок оплаты (30 дней).'}); recommendations.push('Сократите до 5-10 рабочих дней.'); }
            if (!text.includes('форс-мажор') && !text.includes('непреодолимая сила')) { risks.push({level:'medium',text:'Нет раздела о форс-мажоре.'}); recommendations.push('Добавьте раздел о форс-мажоре.'); }
            if (!text.includes('подсудность') && !text.includes('суд') && !text.includes('арбитраж')) { risks.push({level:'medium',text:'Не определён порядок разрешения споров.'}); recommendations.push('Добавьте раздел о подсудности.'); }
            if (!risks.length) { risks.push({level:'low',text:'Критических рисков не обнаружено.'}); recommendations.push('Документ выглядит корректно.'); }

            const riskCount = risks.length;
            const score = Math.max(0, Math.min(100, 100 - risks.filter(r=>r.level==='high').length*15 - risks.filter(r=>r.level==='medium').length*8));
            document.getElementById('ai-score').textContent = score;
            document.getElementById('ai-risks-count').textContent = riskCount;
            document.getElementById('ai-sections-count').textContent = Math.max(3, Math.floor(text.split('\n').filter(l=>l.trim().length>0).length/5));

            const rl = document.getElementById('ai-risk-list'); rl.innerHTML = '';
            risks.forEach(r => {
                const bg = r.level==='high'?'soft-warning':'', color = r.level==='high'?'text-danger':'text-warning';
                const icon = r.level==='high'?'🔴':r.level==='medium'?'🟡':'🔵';
                rl.innerHTML += `<div class="insight-card ${bg} mb-2"><div class="d-flex align-items-start gap-2"><span>${icon}</span><div><div class="fw-semibold small ${color}">${r.level==='high'?'Высокий':r.level==='medium'?'Средний':'Низкий'}</div><div class="small mt-1" style="color:var(--ink);">${r.text}</div></div></div></div>`;
            });
            const rd = document.getElementById('ai-recommendations'); rd.innerHTML = '';
            recommendations.forEach((rec,i) => { rd.innerHTML += `<div class="insight-card soft-success mb-2"><div class="d-flex align-items-start gap-2"><span class="fw-bold" style="color:#10b981;">${i+1}.</span><div class="small" style="color:var(--ink);">${rec}</div></div></div>`; });
            document.getElementById('ai-results').style.display = 'block';
            btn.innerHTML = 'Запустить AI-анализ'; btn.disabled = false;
        }, 1500);
    }

    // ===== Generator =====
    function generateContract() {
        const prompt = document.getElementById('gen-prompt').value.trim();
        if (!prompt) { alert('Опишите договор'); return; }
        const btn = document.getElementById('generate-btn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> ...'; btn.disabled = true;
        setTimeout(() => {
            const type = document.getElementById('gen-type').value;
            let contract = '';
            if (type === 'rent') contract = `ДОГОВОР АРЕНДЫ\n\n1. Предмет: Арендодатель передаёт помещение для использования.\n2. Срок: 12 месяцев.\n3. Арендная плата: по соглашению сторон.\n4. Обязанности: Арендатор оплачивает коммуналку.\n5. Ответственность: неустойка 0,1%/день.\n6. Реквизиты: согласовываются отдельно.`;
            else if (type === 'service') contract = `ДОГОВОР ОКАЗАНИЯ УСЛУГ\n\n1. Заказчик поручает, Исполнитель оказывает услуги.\n2. Сроки: по заявкам.\n3. Оплата: 5 рабочих дней после акта.\n4. Ответственность: неустойка 0,1%.\n5. Реквизиты: отдельно.`;
            else contract = `ДОГОВОР\n\n1. Предмет: сотрудничество.\n2. Обязанности: исполнение обязательств.\n3. Ответственность: возмещение убытков.\n4. Срок: до исполнения.\n5. Реквизиты: отдельно.`;
            document.getElementById('gen-result-text').textContent = contract;
            document.getElementById('gen-results').style.display = 'block';
            btn.innerHTML = 'Сгенерировать договор'; btn.disabled = false;
        }, 2000);
    }

    function downloadGenerated(format) {
        const text = document.getElementById('gen-result-text').textContent;
        if (!text) return;
        if (format === 'txt') { const b = new Blob([text], {type:'text/plain;charset=utf-8'}); const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = 'contract.txt'; a.click(); }
        else if (format === 'word') { const html = `<html><head><meta charset="UTF-8"><style>body{font-family:serif;font-size:12pt;line-height:1.6;margin:24px}</style></head><body><pre style="white-space:pre-wrap">${text.replace(/&/g,'&amp;').replace(/</g,'&lt;')}</pre></body></html>`; const b = new Blob(['\ufeff', html], {type:'application/msword'}); const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = 'contract.doc'; a.click(); }
    }

    function copyGenerated() {
        const text = document.getElementById('gen-result-text').textContent;
        navigator.clipboard.writeText(text).then(() => alert('Скопировано!'));
    }

    // ===== AI Chat =====
    const chatResponses = {
        'договор': 'Для заключения договора необходимо:\n\n1. Определить предмет договора\n2. Согласовать существенные условия\n3. Указать реквизиты сторон\n4. Подписать документ\n\nСогласно ст. 432 ГК РФ, договор считается заключённым при согласовании всех существенных условий.',
        'увольнение': 'Основания для увольнения по ТК РФ:\n\n• По собственному желанию (ст. 80)\n• По соглашению сторон (ст. 78)\n• По инициативе работодателя (ст. 81)\n• По истечении срока договора\n\nРаботник обязан предупредить за 14 дней.',
        'алименты': 'Алименты на детей (СК РФ ст. 81):\n\n• На одного ребёнка — 1/4 дохода\n• На двоих — 1/3 дохода\n• На троих и более — 1/2 дохода\n\nАлименты можно взыскать через суд или по нотариальному соглашению.',
        'неустойка': 'Неустойка (ст. 330 ГК РФ):\n\n• Штраф — фиксированная сумма\n• Пеня — % от суммы за каждый день\n\nПо ст. 333 ГК РФ суд может уменьшить неустойку, если она несоразмерна последствиям нарушения.',
        'срок давности': 'Сроки исковой давности (ст. 196 ГК РФ):\n\n• Общий срок — 3 года\n• По спорам о ничтожности сделки — 3 года\n• По требованиям о применении последствий — 3 года\n\nСрок начинается с момента, когда лицо узнало о нарушении.',
        'default': 'Спасибо за ваш вопрос! Вот что я могу рассказать:\n\n📋 По договорному праву: условия заключения, расторжения, ответственность\n👷 По трудовому праву: увольнение, отпуска, зарплата\n👨‍👩‍👧 По семейному праву: алименты, брак, опека\n💰 По финансовому праву: налоги, неустойки, сроки давности\n\nЗадайте более конкретный вопрос для детального ответа!'
    };

    function sendChatMessage() {
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if (!text) return;

        const messages = document.getElementById('chatMessages');
        const div = document.createElement('div');
        div.className = 'chat-message chat-message-user';
        div.textContent = text;
        messages.appendChild(div);
        input.value = '';

        setTimeout(() => {
            const lowerText = text.toLowerCase();
            let response = chatResponses.default;
            if (lowerText.includes('договор') || lowerText.includes('contract')) response = chatResponses['договор'];
            else if (lowerText.includes('увольн') || lowerText.includes('работ')) response = chatResponses['увольнение'];
            else if (lowerText.includes('алимент') || lowerText.includes('детей')) response = chatResponses['алименты'];
            else if (lowerText.includes('неустойк') || lowerText.includes('пен') || lowerText.includes('штраф')) response = chatResponses['неустойка'];
            else if (lowerText.includes('срок') || lowerText.includes('давн')) response = chatResponses['срок давности'];

            const aiDiv = document.createElement('div');
            aiDiv.className = 'chat-message chat-message-ai';
            aiDiv.innerHTML = response.replace(/\n/g, '<br>');
            messages.appendChild(aiDiv);
            messages.scrollTop = messages.scrollHeight;
        }, 800);

        messages.scrollTop = messages.scrollHeight;
    }

    // ===== Calculator =====
    function updateCalcFields() {
        const type = document.getElementById('calcType').value;
        document.getElementById('calc-penalty-fields').style.display = type === 'penalty' ? 'block' : 'none';
        document.getElementById('calc-court-fields').style.display = type === 'court_fee' ? 'block' : 'none';
        document.getElementById('calc-limitation-fields').style.display = type === 'limitation' ? 'block' : 'none';
        document.getElementById('calc-result').style.display = 'none';
    }

    function calculateResult() {
        const type = document.getElementById('calcType').value;
        const resultDiv = document.getElementById('calc-result');
        let html = '';

        if (type === 'penalty') {
            const debt = Number(document.getElementById('calc-debt').value) || 0;
            const rate = Number(document.getElementById('calc-rate').value) || 0;
            const startDate = document.getElementById('calc-dateStart').value;
            const endDate = document.getElementById('calc-dateEnd').value;
            let days = 0;
            if (startDate && endDate) {
                days = Math.max(0, Math.ceil((new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24)));
            }
            const penalty = debt * rate / 100 * days;
            const total = debt + penalty;
            html = `
                <div class="calc-row"><span>Сумма долга</span><span>${debt.toLocaleString('ru-RU')} ₽</span></div>
                <div class="calc-row"><span>Ставка</span><span>${rate}%/день</span></div>
                <div class="calc-row"><span>Дней просрочки</span><span>${days}</span></div>
                <div class="calc-row"><span>Неустойка</span><span>${penalty.toLocaleString('ru-RU')} ₽</span></div>
                <div class="calc-row calc-total"><span>Итого</span><span>${total.toLocaleString('ru-RU')} ₽</span></div>
            `;
        } else if (type === 'court_fee') {
            const claim = Number(document.getElementById('calc-claim').value) || 0;
            let fee = 0;
            if (claim <= 20000) fee = claim * 0.04;
            else if (claim <= 100000) fee = 800 + (claim - 20000) * 0.03;
            else if (claim <= 200000) fee = 3200 + (claim - 100000) * 0.02;
            else if (claim <= 1000000) fee = 5200 + (claim - 200000) * 0.01;
            else fee = 13200 + (claim - 1000000) * 0.005;
            fee = Math.max(fee, 400);
            html = `
                <div class="calc-row"><span>Цена иска</span><span>${claim.toLocaleString('ru-RU')} ₽</span></div>
                <div class="calc-row calc-total"><span>Госпошлина</span><span>${Math.round(fee).toLocaleString('ru-RU')} ₽</span></div>
                <div class="mt-2 small muted">Расчёт по ст. 333.19 НК РФ</div>
            `;
        } else if (type === 'limitation') {
            const violationDate = document.getElementById('calc-violationDate').value;
            if (violationDate) {
                const vd = new Date(violationDate);
                const expiry = new Date(vd);
                expiry.setFullYear(expiry.getFullYear() + 3);
                const now = new Date();
                const daysLeft = Math.max(0, Math.ceil((expiry - now) / (1000 * 60 * 60 * 24)));
                html = `
                    <div class="calc-row"><span>Дата нарушения</span><span>${new Intl.DateTimeFormat('ru-RU').format(vd)}</span></div>
                    <div class="calc-row"><span>Срок истекает</span><span>${new Intl.DateTimeFormat('ru-RU').format(expiry)}</span></div>
                    <div class="calc-row calc-total"><span>Осталось дней</span><span>${daysLeft > 0 ? daysLeft + ' дней' : 'Срок истёк!'}</span></div>
                    <div class="mt-2 small muted">Общий срок исковой давности — 3 года (ст. 196 ГК РФ)</div>
                `;
            } else { html = '<div class="text-center small muted">Укажите дату нарушения</div>'; }
        }

        resultDiv.innerHTML = html;
        resultDiv.style.display = 'block';
    }

    // ===== Knowledge Base =====
    function filterKB() {
        const query = document.getElementById('kbSearch').value.toLowerCase();
        document.querySelectorAll('.kb-item').forEach(item => {
            const keywords = (item.dataset.keywords || '') + ' ' + item.textContent;
            item.style.display = keywords.toLowerCase().includes(query) ? 'block' : 'none';
        });
    }

    // ===== Counters (EXACT VALUES) =====
    function animateCounter(el, target, duration) {
        const startTime = performance.now();
        function update(currentTime) {
            const progress = Math.min((currentTime - startTime) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target).toLocaleString('ru-RU');
            if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    }

    let countersAnimated = false;
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !countersAnimated) {
                countersAnimated = true;
                // Exact values from the image
                animateCounter(document.getElementById('stat-docs'), 12480, 2000);
                animateCounter(document.getElementById('stat-users'), 3200, 2000);
                animateCounter(document.getElementById('stat-risks'), 8320, 2000);
                animateCounter(document.getElementById('stat-time'), 45600, 2000);
            }
        });
    }, { threshold: 0.3 });

    const statsSection = document.getElementById('stats');
    if (statsSection) observer.observe(statsSection);

    // ===== Escape =====
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(m => { m.classList.remove('active'); document.body.style.overflow = ''; });
            document.getElementById('profileDropdown').classList.add('hidden');
            document.getElementById('notificationPanel').classList.add('hidden');
        }
    });

    // Set default dates for calculator
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().split('T')[0];
        const thirtyDaysAgo = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        const oneYearAgo = new Date(Date.now() - 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

        const bStart = document.getElementById('b-startDate');
        const bEnd = document.getElementById('b-endDate');
        if (bStart) bStart.value = today;
        if (bEnd) bEnd.value = new Date(Date.now() + 90 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

        const calcStart = document.getElementById('calc-dateStart');
        const calcEnd = document.getElementById('calc-dateEnd');
        if (calcStart) calcStart.value = thirtyDaysAgo;
        if (calcEnd) calcEnd.value = today;

        const violationDate = document.getElementById('calc-violationDate');
        if (violationDate) violationDate.value = oneYearAgo;
    });
</script>
</body>
</html>

