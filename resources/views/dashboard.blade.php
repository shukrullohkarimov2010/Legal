<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a0a0f" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#fafaf9" media="(prefers-color-scheme: light)">
    <title>LegalAI Pro — AI-платформа для юридических документов</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Manrope', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    }
                }
            }
        }
    </script>

    <style>
        /* ============================================
           PREMIUM DESIGN SYSTEM — LegalAI Pro
           ============================================ */

        :root {
            --ease-out-expo: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
            --duration-fast: 0.2s;
            --duration-normal: 0.4s;
            --duration-slow: 0.8s;
        }

        :root[data-theme="light"] {
            --bg-app: #fafaf9;
            --bg-elevated: #ffffff;
            --bg-sunken: #f5f5f4;
            --bg-glass: rgba(255, 255, 255, 0.72);
            --ink-primary: #0c0a09;
            --ink-secondary: #44403c;
            --ink-tertiary: #78716c;
            --ink-quaternary: #a8a29e;
            --line-subtle: rgba(12, 10, 9, 0.06);
            --line-medium: rgba(12, 10, 9, 0.1);
            --line-strong: rgba(12, 10, 9, 0.16);

            --accent-blue: #2563eb;
            --accent-purple: #7c3aed;
            --accent-green: #059669;
            --accent-orange: #ea580c;
            --accent-pink: #db2777;

            --gradient-hero: linear-gradient(135deg, #1e293b 0%, #312e81 50%, #1e40af 100%);
            --gradient-accent: linear-gradient(135deg, #2563eb, #7c3aed, #db2777);
            --gradient-subtle: linear-gradient(135deg, rgba(37,99,235,0.08), rgba(124,58,237,0.08));

            --shadow-xs: 0 1px 2px rgba(12,10,9,0.04);
            --shadow-sm: 0 2px 8px rgba(12,10,9,0.06), 0 1px 2px rgba(12,10,9,0.04);
            --shadow-md: 0 8px 24px -4px rgba(12,10,9,0.08), 0 4px 8px -2px rgba(12,10,9,0.04);
            --shadow-lg: 0 20px 48px -8px rgba(12,10,9,0.12), 0 8px 16px -4px rgba(12,10,9,0.06);
            --shadow-xl: 0 32px 72px -12px rgba(12,10,9,0.18), 0 16px 24px -8px rgba(12,10,9,0.08);
            --shadow-glow: 0 0 48px rgba(37,99,235,0.2);

            --noise-opacity: 0.025;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --radius-full: 9999px;
        }

        :root[data-theme="dark"] {
            --bg-app: #0a0a0f;
            --bg-elevated: #12121a;
            --bg-sunken: #0f0f16;
            --bg-glass: rgba(18, 18, 26, 0.72);
            --ink-primary: #fafaf9;
            --ink-secondary: #d6d3d1;
            --ink-tertiary: #a8a29e;
            --ink-quaternary: #78716c;
            --line-subtle: rgba(250,250,249,0.06);
            --line-medium: rgba(250,250,249,0.1);
            --line-strong: rgba(250,250,249,0.16);

            --accent-blue: #60a5fa;
            --accent-purple: #a78bfa;
            --accent-green: #34d399;
            --accent-orange: #fb923c;
            --accent-pink: #f472b6;

            --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #3730a3 100%);
            --gradient-accent: linear-gradient(135deg, #60a5fa, #a78bfa, #f472b6);
            --gradient-subtle: linear-gradient(135deg, rgba(96,165,250,0.12), rgba(167,139,250,0.12));

            --shadow-xs: 0 1px 2px rgba(0,0,0,0.2);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.3), 0 1px 2px rgba(0,0,0,0.2);
            --shadow-md: 0 8px 24px -4px rgba(0,0,0,0.4), 0 4px 8px -2px rgba(0,0,0,0.2);
            --shadow-lg: 0 20px 48px -8px rgba(0,0,0,0.5), 0 8px 16px -4px rgba(0,0,0,0.3);
            --shadow-xl: 0 32px 72px -12px rgba(0,0,0,0.6), 0 16px 24px -8px rgba(0,0,0,0.4);
            --shadow-glow: 0 0 48px rgba(96,165,250,0.25);

            --noise-opacity: 0.04;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --radius-full: 9999px;
        }

        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        html {
            scroll-behavior: smooth;
            font-feature-settings: 'cv11', 'ss01', 'ss03';
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            color: var(--ink-primary);
            background: var(--bg-app);
            min-height: 100vh;
            padding-top: 72px;
            overflow-x: hidden;
            position: relative;
            letter-spacing: -0.011em;
            transition: background-color var(--duration-normal) var(--ease-out-expo),
            color var(--duration-normal) var(--ease-out-expo);
        }

        /* ============================================
    DYNAMIC AMBIENT BACKGROUND
    Animated mesh gradient + shimmer particles
    ============================================ */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.5'/%3E%3C/svg%3E");
            opacity: var(--noise-opacity);
            pointer-events: none;
            z-index: 1;
            mix-blend-mode: overlay;
        }

        .ambient-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
            isolation: isolate;
        }

        /* Animated gradient blobs */
        .ambient-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0;
            mix-blend-mode: normal;
            will-change: transform, opacity;
            animation: blob-float 30s ease-in-out infinite;
        }

        /* Light theme blobs */
        [data-theme="light"] .ambient-blob { opacity: 0.35; }
        [data-theme="dark"] .ambient-blob { opacity: 0.45; }

        .ambient-blob-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(96,165,250,0.8) 0%, rgba(96,165,250,0) 70%);
            top: -10%;
            left: -5%;
            animation-delay: 0s;
            animation-duration: 32s;
        }

        .ambient-blob-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(167,139,250,0.7) 0%, rgba(167,139,250,0) 70%);
            top: 30%;
            right: -10%;
            animation-delay: -8s;
            animation-duration: 28s;
        }

        .ambient-blob-3 {
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(244,114,182,0.5) 0%, rgba(244,114,182,0) 70%);
            bottom: -15%;
            left: 20%;
            animation-delay: -16s;
            animation-duration: 36s;
        }

        .ambient-blob-4 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(52,211,153,0.4) 0%, rgba(52,211,153,0) 70%);
            top: 60%;
            left: 50%;
            animation-delay: -22s;
            animation-duration: 34s;
        }

        @keyframes blob-float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            25% {
                transform: translate(80px, -60px) scale(1.1);
            }
            50% {
                transform: translate(-40px, 80px) scale(0.95);
            }
            75% {
                transform: translate(60px, 40px) scale(1.05);
            }
        }

        /* Subtle animated grid overlay */
        .ambient-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(var(--line-subtle) 1px, transparent 1px),
                linear-gradient(90deg, var(--line-subtle) 1px, transparent 1px);
            background-size: 80px 80px;
            opacity: 0.4;
            mask-image: radial-gradient(ellipse at center, rgba(0,0,0,0.4) 0%, transparent 70%);
            -webkit-mask-image: radial-gradient(ellipse at center, rgba(0,0,0,0.4) 0%, transparent 70%);
            animation: grid-pulse 8s ease-in-out infinite;
        }

        @keyframes grid-pulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.5; }
        }

        /* Shimmer particles */
        .ambient-particles {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: var(--accent-blue);
            border-radius: 50%;
            opacity: 0;
            box-shadow: 0 0 8px var(--accent-blue);
            animation: particle-drift linear infinite;
        }

        .particle:nth-child(1) { left: 10%; top: 20%; animation-duration: 18s; animation-delay: 0s; background: var(--accent-blue); box-shadow: 0 0 8px var(--accent-blue); }
        .particle:nth-child(2) { left: 25%; top: 60%; animation-duration: 22s; animation-delay: -4s; background: var(--accent-purple); box-shadow: 0 0 8px var(--accent-purple); }
        .particle:nth-child(3) { left: 45%; top: 35%; animation-duration: 20s; animation-delay: -8s; background: var(--accent-pink); box-shadow: 0 0 8px var(--accent-pink); }
        .particle:nth-child(4) { left: 70%; top: 70%; animation-duration: 24s; animation-delay: -12s; background: var(--accent-green); box-shadow: 0 0 8px var(--accent-green); }
        .particle:nth-child(5) { left: 85%; top: 25%; animation-duration: 26s; animation-delay: -16s; background: var(--accent-orange); box-shadow: 0 0 8px var(--accent-orange); }
        .particle:nth-child(6) { left: 60%; top: 85%; animation-duration: 19s; animation-delay: -3s; background: var(--accent-blue); box-shadow: 0 0 8px var(--accent-blue); }
        .particle:nth-child(7) { left: 15%; top: 80%; animation-duration: 23s; animation-delay: -7s; background: var(--accent-purple); box-shadow: 0 0 8px var(--accent-purple); }
        .particle:nth-child(8) { left: 90%; top: 55%; animation-duration: 21s; animation-delay: -11s; background: var(--accent-pink); box-shadow: 0 0 8px var(--accent-pink); }

        @keyframes particle-drift {
            0% {
                transform: translate(0, 0) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 0.8;
                transform: translate(10px, -20px) scale(1);
            }
            50% {
                opacity: 0.5;
            }
            90% {
                opacity: 0.8;
                transform: translate(-30px, -200px) scale(1);
            }
            100% {
                transform: translate(-40px, -280px) scale(0);
                opacity: 0;
            }
        }

        /* Respect reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .ambient-blob,
            .ambient-grid,
            .particle {
                animation: none !important;
            }
            .ambient-blob {
                opacity: 0.2;
                transform: none;
            }
            .particle {
                display: none;
            }
        }

        .app-shell { position: relative; z-index: 2; }

        /* ============================================
           GLASSMORPHIC NAVBAR
           ============================================ */
        .navbar-glass {
            background: var(--bg-glass);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border-bottom: 1px solid var(--line-subtle);
            transition: all var(--duration-normal) var(--ease-out-expo);
        }

        .navbar-glass.scrolled {
            background: var(--bg-elevated);
            box-shadow: var(--shadow-sm);
        }

        .brand-logo {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: -0.03em;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: transform var(--duration-normal) var(--ease-spring);
        }
        .brand-logo:hover { transform: scale(1.04); }

        .nav-link-premium {
            position: relative;
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--ink-secondary);
            padding: 0.5rem 0.875rem;
            border-radius: var(--radius-md);
            transition: all var(--duration-fast) var(--ease-out-expo);
        }
        .nav-link-premium:hover {
            color: var(--ink-primary);
            background: var(--gradient-subtle);
        }
        .nav-link-premium.active {
            color: var(--ink-primary);
            background: var(--gradient-subtle);
        }
        .nav-link-premium.active::before {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            background: var(--accent-blue);
            border-radius: 50%;
        }

        .icon-btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-md);
            border: 1px solid var(--line-subtle);
            background: var(--bg-elevated);
            color: var(--ink-secondary);
            transition: all var(--duration-fast) var(--ease-out-expo);
            position: relative;
        }
        .icon-btn:hover {
            background: var(--gradient-subtle);
            border-color: var(--line-medium);
            color: var(--ink-primary);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }
        .icon-btn:active { transform: translateY(0); }

        .notification-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            border: 2px solid var(--bg-elevated);
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.5); }
            50% { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
        }

        .avatar-premium {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.875rem;
            color: white;
            position: relative;
            box-shadow: var(--shadow-sm), inset 0 1px 0 rgba(255,255,255,0.2);
        }
        .avatar-premium::after {
            content: '';
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 12px;
            height: 12px;
            background: #22c55e;
            border-radius: 50%;
            border: 2px solid var(--bg-elevated);
        }

        /* Theme toggle */
        .theme-toggle {
            position: relative;
            width: 38px;
            height: 38px;
            overflow: hidden;
        }
        .theme-toggle svg {
            position: absolute;
            inset: 0;
            margin: auto;
            width: 18px;
            height: 18px;
            transition: all var(--duration-normal) var(--ease-spring);
        }
        [data-theme="light"] .theme-toggle .icon-sun { opacity: 1; transform: rotate(0) scale(1); }
        [data-theme="light"] .theme-toggle .icon-moon { opacity: 0; transform: rotate(90deg) scale(0.5); }
        [data-theme="dark"] .theme-toggle .icon-sun { opacity: 0; transform: rotate(-90deg) scale(0.5); }
        [data-theme="dark"] .theme-toggle .icon-moon { opacity: 1; transform: rotate(0) scale(1); }

        /* ============================================
           HERO SECTION — CINEMATIC
           ============================================ */
        .hero-section {
            position: relative;
            overflow: hidden;
            border-radius: var(--radius-xl);
            padding: clamp(2rem, 5vw, 4rem);
            background: var(--gradient-hero);
            color: white;
            isolation: isolate;
            box-shadow:
                var(--shadow-xl),
                inset 0 1px 0 rgba(255,255,255,0.1),
                inset 0 -1px 0 rgba(0,0,0,0.2);
            min-height: 560px;
            display: flex;
            align-items: center;
        }

        /* Grid pattern */
        .hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 40%, transparent 80%);
            pointer-events: none;
            z-index: -1;
        }

        /* Aurora blobs */
        .hero-aurora {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            pointer-events: none;
            z-index: -1;
            animation: aurora 20s ease-in-out infinite;
        }
        .hero-aurora-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #60a5fa, transparent 70%);
            top: -200px; right: -100px;
            animation-delay: 0s;
        }
        .hero-aurora-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #a78bfa, transparent 70%);
            bottom: -150px; left: -100px;
            animation-delay: -7s;
        }
        .hero-aurora-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #f472b6, transparent 70%);
            top: 30%; left: 40%;
            animation-delay: -14s;
            opacity: 0.25;
        }
        @keyframes aurora {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.875rem;
            border-radius: var(--radius-full);
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.12);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            color: rgba(255,255,255,0.95);
            margin-bottom: 1.5rem;
            animation: fade-up 0.8s var(--ease-out-expo) both;
        }
        .hero-eyebrow::before {
            content: '';
            width: 6px; height: 6px;
            background: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 8px #22c55e;
            animation: pulse-dot 2s infinite;
        }

        .hero-title {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: clamp(2.25rem, 5.5vw, 4rem);
            line-height: 1.02;
            letter-spacing: -0.04em;
            margin-bottom: 1.25rem;
            text-wrap: balance;
            animation: fade-up 0.8s 0.1s var(--ease-out-expo) both;
        }
        .hero-title .gradient-word {
            background: linear-gradient(135deg, #fff 0%, #c7d2fe 50%, #a5b4fc 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 1.5vw, 1.125rem);
            line-height: 1.5;
            color: rgba(255,255,255,0.72);
            max-width: 540px;
            margin-bottom: 2rem;
            text-wrap: pretty;
            animation: fade-up 0.8s 0.2s var(--ease-out-expo) both;
        }

        @keyframes fade-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            animation: fade-up 0.8s 0.3s var(--ease-out-expo) both;
        }

        .btn-primary-premium {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            background: white;
            color: #0f172a;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.9375rem;
            letter-spacing: -0.01em;
            cursor: pointer;
            transition: all var(--duration-normal) var(--ease-spring);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.9);
            position: relative;
            overflow: hidden;
        }
        .btn-primary-premium::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0), rgba(255,255,255,0.3), rgba(255,255,255,0));
            transform: translateX(-100%);
            transition: transform 0.6s;
        }
        .btn-primary-premium:hover::before { transform: translateX(100%); }
        .btn-primary-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.9);
        }
        .btn-primary-premium:active { transform: translateY(0); }

        .btn-ghost-premium {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            color: white;
            border: 1px solid rgba(255,255,255,0.16);
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.9375rem;
            letter-spacing: -0.01em;
            cursor: pointer;
            transition: all var(--duration-normal) var(--ease-out-expo);
            text-decoration: none;
        }
        .btn-ghost-premium:hover {
            background: rgba(255,255,255,0.14);
            border-color: rgba(255,255,255,0.28);
            color: white;
            transform: translateY(-2px);
        }

        .hero-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 2rem;
            animation: fade-up 0.8s 0.4s var(--ease-out-expo) both;
        }
        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 500;
            color: rgba(255,255,255,0.85);
            transition: all var(--duration-fast);
        }
        .hero-chip:hover {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.2);
            transform: translateY(-1px);
        }
        .hero-chip::before {
            content: '✦';
            font-size: 0.625rem;
            opacity: 0.6;
        }

        /* Hero visual — floating dashboard mockup */
        .hero-visual {
            position: relative;
            perspective: 1200px;
            animation: fade-up 1s 0.5s var(--ease-out-expo) both;
        }
        .hero-dashboard {
            position: relative;
            transform: rotateY(-8deg) rotateX(4deg);
            transform-style: preserve-3d;
            transition: transform 0.8s var(--ease-out-expo);
        }
        .hero-dashboard:hover { transform: rotateY(-4deg) rotateX(2deg); }

        .dashboard-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            box-shadow: var(--shadow-lg);
        }
        .dashboard-metric {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.75rem 0;
        }
        .dashboard-metric:not(:last-child) {
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .metric-icon {
            width: 40px; height: 40px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.08);
            flex-shrink: 0;
        }
        .metric-icon.blue { background: rgba(96,165,250,0.2); color: #93c5fd; }
        .metric-icon.purple { background: rgba(167,139,250,0.2); color: #c4b5fd; }
        .metric-icon.green { background: rgba(52,211,153,0.2); color: #6ee7b7; }
        .metric-icon.orange { background: rgba(251,146,60,0.2); color: #fdba74; }

        .metric-label {
            font-size: 0.6875rem;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
        }
        .metric-value {
            font-family: 'Manrope', sans-serif;
            font-size: 1.375rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: white;
            line-height: 1.1;
            margin-top: 0.125rem;
        }
        .metric-trend {
            font-size: 0.6875rem;
            color: #86efac;
            font-weight: 600;
        }

        .hero-floating-card {
            position: absolute;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            box-shadow: var(--shadow-md);
            animation: float 6s ease-in-out infinite;
        }
        .hero-floating-card.card-1 {
            top: -30px; left: -40px;
            animation-delay: 0s;
        }
        .hero-floating-card.card-2 {
            bottom: 20px; right: -30px;
            animation-delay: -3s;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        /* ============================================
           TRUSTED BY / SOCIAL PROOF
           ============================================ */
        .trust-section {
            padding: 3rem 0 1rem;
        }
        .trust-label {
            text-align: center;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--ink-quaternary);
            margin-bottom: 1.5rem;
        }
        .trust-logos {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3rem;
            flex-wrap: wrap;
            opacity: 0.6;
            filter: grayscale(100%);
            transition: all var(--duration-normal);
        }
        .trust-logos:hover { opacity: 0.85; filter: grayscale(0%); }
        .trust-logo {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 1.125rem;
            color: var(--ink-tertiary);
            letter-spacing: -0.02em;
            transition: color var(--duration-fast);
        }
        .trust-logo:hover { color: var(--ink-primary); }

        /* ============================================
           SECTION HEADERS
           ============================================ */
        .section-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            background: var(--gradient-subtle);
            border: 1px solid var(--line-subtle);
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--accent-blue);
            letter-spacing: 0.02em;
        }

        .section-title {
            font-family: 'Manrope', sans-serif;
            font-size: clamp(1.75rem, 4vw, 2.75rem);
            font-weight: 800;
            letter-spacing: -0.035em;
            line-height: 1.1;
            color: var(--ink-primary);
            text-wrap: balance;
        }
        .section-title .gradient-word {
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-subtitle {
            font-size: 1.0625rem;
            color: var(--ink-tertiary);
            line-height: 1.55;
            max-width: 600px;
            text-wrap: pretty;
        }

        /* ============================================
           BENTO GRID — FEATURES
           ============================================ */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1rem;
            grid-auto-rows: minmax(200px, auto);
        }

        .bento-card {
            position: relative;
            background: var(--bg-elevated);
            border: 1px solid var(--line-subtle);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            overflow: hidden;
            transition: all var(--duration-normal) var(--ease-out-expo);
            cursor: pointer;
            isolation: isolate;
        }
        .bento-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--gradient-subtle);
            opacity: 0;
            transition: opacity var(--duration-normal);
            z-index: -1;
        }
        .bento-card:hover {
            border-color: var(--line-strong);
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        .bento-card:hover::before { opacity: 1; }

        /* Spotlight effect */
        .bento-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(600px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(96,165,250,0.08), transparent 40%);
            opacity: 0;
            transition: opacity var(--duration-normal);
            pointer-events: none;
            z-index: -1;
        }
        .bento-card:hover::after { opacity: 1; }

        .bento-card-lg { grid-column: span 8; }
        .bento-card-md { grid-column: span 4; }
        .bento-card-sm { grid-column: span 4; }
        .bento-card-wide { grid-column: span 6; }

        @media (max-width: 1024px) {
            .bento-card-lg, .bento-card-md, .bento-card-sm, .bento-card-wide {
                grid-column: span 12;
            }
        }

        .feature-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.625rem;
            background: var(--gradient-subtle);
            border: 1px solid var(--line-subtle);
            border-radius: var(--radius-full);
            font-size: 0.6875rem;
            font-weight: 600;
            color: var(--ink-secondary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .feature-badge .dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: currentColor;
        }
        .feature-badge.blue { color: var(--accent-blue); }
        .feature-badge.purple { color: var(--accent-purple); }
        .feature-badge.green { color: var(--accent-green); }
        .feature-badge.orange { color: var(--accent-orange); }
        .feature-badge.pink { color: var(--accent-pink); }
        .feature-badge.cyan { color: #0891b2; }

        .feature-icon-box {
            width: 52px; height: 52px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem;
            position: relative;
            box-shadow: var(--shadow-sm), inset 0 1px 0 rgba(255,255,255,0.4);
        }
        .feature-icon-box.blue { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
        .feature-icon-box.purple { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #6d28d9; }
        .feature-icon-box.green { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857; }
        .feature-icon-box.orange { background: linear-gradient(135deg, #fed7aa, #fdba74); color: #c2410c; }
        .feature-icon-box.pink { background: linear-gradient(135deg, #fce7f3, #fbcfe8); color: #be185d; }
        .feature-icon-box.cyan { background: linear-gradient(135deg, #cffafe, #a5f3fc); color: #0e7490; }
        [data-theme="dark"] .feature-icon-box.blue { background: linear-gradient(135deg, rgba(37,99,235,0.25), rgba(37,99,235,0.15)); color: #60a5fa; }
        [data-theme="dark"] .feature-icon-box.purple { background: linear-gradient(135deg, rgba(124,58,237,0.25), rgba(124,58,237,0.15)); color: #a78bfa; }
        [data-theme="dark"] .feature-icon-box.green { background: linear-gradient(135deg, rgba(5,150,105,0.25), rgba(5,150,105,0.15)); color: #34d399; }
        [data-theme="dark"] .feature-icon-box.orange { background: linear-gradient(135deg, rgba(234,88,12,0.25), rgba(234,88,12,0.15)); color: #fb923c; }
        [data-theme="dark"] .feature-icon-box.pink { background: linear-gradient(135deg, rgba(219,39,119,0.25), rgba(219,39,119,0.15)); color: #f472b6; }
        [data-theme="dark"] .feature-icon-box.cyan { background: linear-gradient(135deg, rgba(8,145,178,0.25), rgba(8,145,178,0.15)); color: #22d3ee; }

        .feature-title {
            font-family: 'Manrope', sans-serif;
            font-size: 1.375rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 0.625rem;
            color: var(--ink-primary);
        }
        .feature-desc {
            font-size: 0.9375rem;
            color: var(--ink-tertiary);
            line-height: 1.55;
            margin-bottom: 1.25rem;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1.5rem;
            display: grid;
            gap: 0.5rem;
        }
        .feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--ink-secondary);
            line-height: 1.45;
        }
        .feature-list li svg {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .feature-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.9375rem;
            color: var(--accent-blue);
            text-decoration: none;
            transition: gap var(--duration-normal) var(--ease-out-expo);
        }
        .feature-link:hover { gap: 0.875rem; color: var(--accent-blue); }
        .feature-link svg { transition: transform var(--duration-normal); }
        .feature-link:hover svg { transform: translateX(2px); }

        /* Bento card visual elements */
        .bento-visual {
            position: absolute;
            right: -20px;
            bottom: -20px;
            width: 200px;
            height: 200px;
            opacity: 0.5;
            pointer-events: none;
            z-index: -1;
        }

        /* Progress bar */
        .progress-premium {
            height: 4px;
            background: var(--bg-sunken);
            border-radius: var(--radius-full);
            overflow: hidden;
            width: 80px;
        }
        .progress-premium-fill {
            height: 100%;
            border-radius: var(--radius-full);
            background: var(--gradient-accent);
            position: relative;
            overflow: hidden;
        }
        .progress-premium-fill::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* ============================================
           PROCESS SECTION
           ============================================ */
        .process-card {
            background: var(--bg-elevated);
            border: 1px solid var(--line-subtle);
            border-radius: var(--radius-lg);
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .process-step {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            border-radius: var(--radius-md);
            transition: background var(--duration-fast);
        }
        .process-step:hover { background: var(--bg-sunken); }

        .step-number {
            width: 40px; height: 40px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 0.875rem;
            color: white;
            flex-shrink: 0;
            background: var(--gradient-accent);
            box-shadow: var(--shadow-sm);
        }

        .step-title {
            font-family: 'Manrope', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--ink-primary);
            margin-bottom: 0.25rem;
            letter-spacing: -0.01em;
        }
        .step-text {
            font-size: 0.875rem;
            color: var(--ink-tertiary);
            line-height: 1.5;
            margin: 0;
        }

        /* ============================================
           STATS SECTION
           ============================================ */
        .stat-card-premium {
            background: var(--bg-elevated);
            border: 1px solid var(--line-subtle);
            border-radius: var(--radius-lg);
            padding: 1.75rem 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all var(--duration-normal) var(--ease-out-expo);
        }
        .stat-card-premium:hover {
            transform: translateY(-4px);
            border-color: var(--line-medium);
            box-shadow: var(--shadow-md);
        }
        .stat-card-premium::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: var(--gradient-accent);
            opacity: 0;
            transition: opacity var(--duration-normal);
        }
        .stat-card-premium:hover::before { opacity: 1; }

        .stat-icon {
            width: 40px; height: 40px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            background: var(--gradient-subtle);
            margin-bottom: 1rem;
        }

        .stat-value-premium {
            font-family: 'Manrope', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1;
            color: var(--ink-primary);
            margin-bottom: 0.375rem;
        }
        .stat-value-premium .suffix {
            font-size: 1.5rem;
            color: var(--accent-blue);
        }
        .stat-label-premium {
            font-size: 0.875rem;
            color: var(--ink-tertiary);
            font-weight: 500;
        }
        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            background: rgba(34,197,94,0.1);
            border-radius: var(--radius-full);
            font-size: 0.6875rem;
            font-weight: 600;
            color: #16a34a;
            margin-top: 0.5rem;
        }
        [data-theme="dark"] .stat-trend { color: #4ade80; }

        /* ============================================
           CTA SECTION
           ============================================ */
        .cta-section {
            position: relative;
            overflow: hidden;
            border-radius: var(--radius-xl);
            padding: clamp(2.5rem, 6vw, 4rem);
            background: var(--gradient-hero);
            color: white;
            text-align: center;
            isolation: isolate;
            box-shadow: var(--shadow-xl);
        }

        .cta-title {
            font-family: 'Manrope', sans-serif;
            font-size: clamp(1.75rem, 4vw, 2.75rem);
            font-weight: 800;
            letter-spacing: -0.035em;
            line-height: 1.1;
            margin-bottom: 1rem;
            text-wrap: balance;
        }

        .cta-subtitle {
            font-size: 1.0625rem;
            color: rgba(255,255,255,0.72);
            max-width: 560px;
            margin: 0 auto 2rem;
            line-height: 1.55;
        }

        .cta-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ============================================
           FOOTER
           ============================================ */
        .footer-premium {
            background: var(--bg-elevated);
            border-top: 1px solid var(--line-subtle);
            margin-top: 4rem;
        }

        .footer-brand {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.03em;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-link {
            font-size: 0.875rem;
            color: var(--ink-tertiary);
            text-decoration: none;
            transition: color var(--duration-fast);
        }
        .footer-link:hover { color: var(--ink-primary); }

        .lang-pill {
            padding: 0.375rem 0.75rem;
            border-radius: var(--radius-full);
            border: 1px solid var(--line-subtle);
            background: transparent;
            color: var(--ink-tertiary);
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--duration-fast);
        }
        .lang-pill:hover { border-color: var(--line-strong); color: var(--ink-primary); }
        .lang-pill.active {
            background: var(--ink-primary);
            color: var(--bg-app);
            border-color: var(--ink-primary);
        }

        /* ============================================
           MODALS
           ============================================ */
        .modal-backdrop-premium {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            animation: fade-in 0.3s var(--ease-out-expo);
        }
        .modal-backdrop-premium.active { display: flex; }

        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-premium {
            background: var(--bg-elevated);
            border: 1px solid var(--line-subtle);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            max-width: 640px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: modal-pop 0.4s var(--ease-spring);
        }

        @keyframes modal-pop {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header-premium {
            padding: 1.5rem 1.75rem;
            border-bottom: 1px solid var(--line-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            background: var(--bg-elevated);
            z-index: 2;
            backdrop-filter: blur(12px);
        }
        .modal-body-premium { padding: 1.75rem; }

        .modal-close {
            width: 32px; height: 32px;
            border-radius: var(--radius-sm);
            border: none;
            background: transparent;
            color: var(--ink-tertiary);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all var(--duration-fast);
        }
        .modal-close:hover { background: var(--bg-sunken); color: var(--ink-primary); }

        /* Form controls */
        .form-premium {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--line-medium);
            border-radius: var(--radius-md);
            background: var(--bg-app);
            color: var(--ink-primary);
            font-family: inherit;
            font-size: 0.9375rem;
            transition: all var(--duration-fast);
        }
        .form-premium:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(96,165,250,0.15);
        }
        .form-label-premium {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--ink-secondary);
            margin-bottom: 0.375rem;
            display: block;
        }

        /* ============================================
           DROPDOWNS
           ============================================ */
        .dropdown-premium {
            position: absolute;
            background: var(--bg-elevated);
            border: 1px solid var(--line-subtle);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            min-width: 280px;
            z-index: 50;
            overflow: hidden;
            animation: dropdown-pop 0.2s var(--ease-spring);
            backdrop-filter: blur(20px);
        }
        @keyframes dropdown-pop {
            from { opacity: 0; transform: translateY(-8px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .dropdown-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--line-subtle);
            background: var(--gradient-subtle);
        }
        .dropdown-item-premium {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            color: var(--ink-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all var(--duration-fast);
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        .dropdown-item-premium:hover {
            background: var(--bg-sunken);
            color: var(--ink-primary);
        }
        .dropdown-item-premium svg { width: 16px; height: 16px; opacity: 0.6; }

        .notification-item {
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid var(--line-subtle);
            display: flex;
            gap: 0.75rem;
            cursor: pointer;
            transition: background var(--duration-fast);
        }
        .notification-item:hover { background: var(--bg-sunken); }
        .notification-item:last-child { border-bottom: none; }
        .notification-dot-indicator {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 6px;
        }
        .notification-text {
            font-size: 0.875rem;
            color: var(--ink-primary);
            margin-bottom: 0.25rem;
        }
        .notification-time {
            font-size: 0.75rem;
            color: var(--ink-quaternary);
        }

        /* ============================================
           UTILITIES & ANIMATIONS
           ============================================ */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity var(--duration-slow) var(--ease-out-expo),
            transform var(--duration-slow) var(--ease-out-expo);
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .stagger-1 { transition-delay: 0.1s; }
        .stagger-2 { transition-delay: 0.2s; }
        .stagger-3 { transition-delay: 0.3s; }
        .stagger-4 { transition-delay: 0.4s; }
        .stagger-5 { transition-delay: 0.5s; }
        .stagger-6 { transition-delay: 0.6s; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Chat UI */
        .chat-container {
            background: var(--bg-app);
            border: 1px solid var(--line-subtle);
            border-radius: var(--radius-md);
            height: 320px;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .chat-message {
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            max-width: 85%;
            font-size: 0.875rem;
            line-height: 1.5;
            animation: fade-up 0.3s var(--ease-out-expo);
        }
        .chat-message.user {
            background: var(--accent-blue);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }
        .chat-message.ai {
            background: var(--bg-sunken);
            color: var(--ink-primary);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: var(--line-medium);
            border-radius: var(--radius-full);
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--line-strong); }

        /* Back to top */
        .back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: var(--bg-elevated);
            border: 1px solid var(--line-subtle);
            color: var(--ink-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            pointer-events: none;
            transition: all var(--duration-normal) var(--ease-out-expo);
            box-shadow: var(--shadow-md);
            z-index: 40;
        }
        .back-to-top.visible {
            opacity: 1;
            pointer-events: auto;
        }
        .back-to-top:hover {
            background: var(--ink-primary);
            color: var(--bg-app);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            body { padding-top: 64px; }
            .hero-section { min-height: auto; }
            .stat-value-premium { font-size: 2rem; }
            .hero-floating-card { display: none; }
        }
        /* ============================================
   COMPACT PERSONALIZED GREETING
   ============================================ */
        .greeting-inline {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem 0.25rem 0.25rem;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: var(--radius-full);
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            animation: fade-up 0.6s var(--ease-out-expo) both;
            font-size: 0.8125rem;
            font-weight: 500;
            color: rgba(255,255,255,0.9);
            transition: all var(--duration-fast) var(--ease-out-expo);
        }

        .greeting-inline:hover {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.2);
            transform: translateY(-1px);
        }

        .greeting-avatar-sm {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #60a5fa, #a78bfa);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.6875rem;
            color: white;
            flex-shrink: 0;
            position: relative;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .greeting-avatar-sm::after {
            content: '';
            position: absolute;
            bottom: -1px;
            right: -1px;
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            border: 1.5px solid rgba(30, 27, 75, 0.9);
        }

        .greeting-text {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            letter-spacing: -0.005em;
        }

        .greeting-text .emoji { font-size: 0.875rem; }

        .greeting-text .user-name {
            font-weight: 700;
            background: linear-gradient(135deg, #fff 0%, #c7d2fe 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .greeting-text .divider {
            color: rgba(255,255,255,0.3);
            margin: 0 0.125rem;
        }

        /* Compact hero */
        .hero-section {
            min-height: 460px !important;
            padding: clamp(1.5rem, 4vw, 2.5rem) !important;
        }

        .hero-title {
            font-size: clamp(1.875rem, 4.5vw, 3rem) !important;
            margin-bottom: 0.875rem !important;
        }

        .hero-subtitle {
            font-size: clamp(0.9375rem, 1.3vw, 1rem) !important;
            margin-bottom: 1.5rem !important;
        }

        .hero-eyebrow { margin-bottom: 1rem !important; }

        .hero-chips { margin-top: 1.25rem !important; gap: 0.375rem !important; }
        .hero-chip { font-size: 0.6875rem !important; padding: 0.25rem 0.625rem !important; }

        /* Compact dashboard */
        .dashboard-card-compact {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: var(--radius-lg);
            padding: 1rem;
            box-shadow: var(--shadow-lg);
        }

        .dashboard-compact-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 0.75rem;
        }

        .dashboard-compact-title {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.6);
            font-weight: 600;
        }

        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.6875rem;
            font-weight: 600;
            color: #86efac;
        }

        .live-indicator::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 8px #22c55e;
            animation: pulse-dot 2s infinite;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.625rem;
        }

        .mini-stat {
            padding: 0.625rem;
            background: rgba(255,255,255,0.04);
            border-radius: var(--radius-md);
            border: 1px solid rgba(255,255,255,0.06);
            transition: all var(--duration-fast);
        }

        .mini-stat:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.12);
        }

        .mini-stat-label {
            font-size: 0.625rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .mini-stat-value {
            font-family: 'Manrope', sans-serif;
            font-size: 1.125rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .mini-stat-trend {
            font-size: 0.625rem;
            color: #86efac;
            font-weight: 600;
            margin-top: 0.125rem;
        }

        @media (max-width: 576px) {
            .greeting-text .divider,
            .greeting-text .time-context {
                display: none;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

@php
    // ===== ГЛОБАЛЬНЫЕ ПЕРЕМЕННЫЕ ПОЛЬЗОВАТЕЛЯ =====
    // Определяем их здесь, ДО navbar, чтобы они были доступны во всём шаблоне

    $currentUser = auth()->user();
    $userName = $currentUser?->name ?: 'Пользователь';
    $userEmail = $currentUser?->email ?: 'email@example.com';

    // Инициалы для аватара
    $nameParts = preg_split('/\s+/u', trim($userName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $userInitials = collect($nameParts)
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $userInitials = $userInitials !== '' ? $userInitials : 'П';

    // Первое имя для greeting
    $firstName = $nameParts[0] ?? $userName;

    // Время суток для приветствия
    $hour = (int) date('H');
    if ($hour >= 5 && $hour < 12) {
        $timeEmoji = '🌅';
        $timeRu = 'Доброе утро';
        $timeEn = 'Good morning';
        $timeTj = 'Субҳ ба хайр';
        $timeOfDay = 'morning';
    } elseif ($hour >= 12 && $hour < 18) {
        $timeEmoji = '☀️';
        $timeRu = 'Добрый день';
        $timeEn = 'Good afternoon';
        $timeTj = 'Рӯз ба хайр';
        $timeOfDay = 'afternoon';
    } elseif ($hour >= 18 && $hour < 23) {
        $timeEmoji = '🌆';
        $timeRu = 'Добрый вечер';
        $timeEn = 'Good evening';
        $timeTj = 'Бегоҳ ба хайр';
        $timeOfDay = 'evening';
    } else {
        $timeEmoji = '🌙';
        $timeRu = 'Доброй ночи';
        $timeEn = 'Good night';
        $timeTj = 'Шаб ба хайр';
        $timeOfDay = 'night';
    }
@endphp

    <!-- ============ DYNAMIC AMBIENT BACKGROUND ============ -->
<div class="ambient-bg" aria-hidden="true">
    <!-- Animated gradient blobs -->
    <div class="ambient-blob ambient-blob-1"></div>
    <div class="ambient-blob ambient-blob-2"></div>
    <div class="ambient-blob ambient-blob-3"></div>
    <div class="ambient-blob ambient-blob-4"></div>

    <!-- Subtle grid -->
    <div class="ambient-grid"></div>

    <!-- Shimmer particles -->
    <div class="ambient-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
</div>

<div class="app-shell">
    <!-- весь остальной контент без изменений -->
    <!-- ============ NAVBAR ============ -->
    <nav class="navbar-glass fixed-top" id="mainNav">
        <div class="container" style="max-width: 1280px;">
            <div class="d-flex align-items-center justify-content-between py-3">

                <!-- Brand -->
                <a href="{{ route('welcome') }}" class="d-flex align-items-center gap-2 text-decoration-none" onclick="event.preventDefault(); window.location.reload();">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="logoGrad" x1="0" y1="0" x2="32" y2="32">
                                <stop offset="0%" stop-color="#2563eb"/>
                                <stop offset="50%" stop-color="#7c3aed"/>
                                <stop offset="100%" stop-color="#db2777"/>
                            </linearGradient>
                        </defs>
                        <rect width="32" height="32" rx="8" fill="url(#logoGrad)"/>
                        <path d="M10 10 L14 14 L22 8 M10 16 L22 16 M10 22 L22 22" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="brand-logo">LegalAI Pro</span>
                </a>

                <!-- Desktop Nav -->
                <div class="d-none d-lg-flex align-items-center gap-1">
                    <a href="{{ route('welcome') }}" class="nav-link-premium active" data-i18n="nav_home">Главная</a>
                    <a href="{{ route('dashboard') }}" class="nav-link-premium" data-i18n="nav_dashboard">Рабочий стол</a>
                    <a href="#features" class="nav-link-premium" data-i18n="nav_features">Возможности</a>
                </div>

                <!-- Right actions -->
                <div class="d-flex align-items-center gap-2">

                    <x-language-switcher />

                    <!-- Notifications -->
                    <div class="position-relative d-none d-md-block">
                        <button onclick="toggleNotifications()" class="icon-btn" title="Уведомления">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="notification-dot"></span>
                        </button>
                        <div id="notificationPanel" class="dropdown-premium d-none" style="right:0; top: calc(100% + 8px); width: 340px;">
                            <div class="dropdown-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong style="font-size: 0.9375rem;" data-i18n="notif_title">Уведомления</strong>
                                    <span class="feature-badge blue"><span class="dot"></span>3 новых</span>
                                </div>
                            </div>
                            <div style="max-height: 320px; overflow-y: auto;">
                                <div class="notification-item">
                                    <div class="notification-dot-indicator" style="background: #2563eb;"></div>
                                    <div class="flex-grow-1">
                                        <div class="notification-text" data-i18n="notif_1">Новый шаблон договора доступен</div>
                                        <div class="notification-time">2 мин назад</div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-dot-indicator" style="background: #ea580c;"></div>
                                    <div class="flex-grow-1">
                                        <div class="notification-text" data-i18n="notif_2">Обновлена база кодексов</div>
                                        <div class="notification-time">1 час назад</div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-dot-indicator" style="background: #059669;"></div>
                                    <div class="flex-grow-1">
                                        <div class="notification-text" data-i18n="notif_3">AI улучшил точность анализа</div>
                                        <div class="notification-time">Вчера</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Theme toggle -->
                    <button onclick="toggleTheme()" class="icon-btn theme-toggle d-none d-md-flex" title="Тема">
                        <svg class="icon-sun" fill="currentColor" viewBox="0 0 24 24" style="color: #fbbf24;"><path d="M12 3V1m0 22v-2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42M12 7a5 5 0 100 10 5 5 0 000-10z"/></svg>
                        <svg class="icon-moon" fill="currentColor" viewBox="0 0 24 24" style="color: #60a5fa;"><path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                    </button>

                    <!-- Profile -->
                    <div class="position-relative" id="profileContainer">
                        <button onclick="toggleProfile()" class="d-flex align-items-center gap-2 p-1 rounded-3 border-0 bg-transparent" id="profileButton">
                            <div class="avatar-premium">{{ $userInitials }}</div>
                            <div class="d-none d-lg-block text-start">
                                <div style="font-size: 0.875rem; font-weight: 600; color: var(--ink-primary); line-height: 1.2;">{{ $userName }}</div>
                                <div style="font-size: 0.75rem; color: var(--ink-quaternary);">{{ $userEmail }}</div>
                            </div>
                            <svg id="profileArrow" class="d-none d-lg-block" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--ink-tertiary); transition: transform 0.2s;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="profileDropdown" class="dropdown-premium d-none" style="right:0; top: calc(100% + 8px); width: 280px;">
                            <div class="dropdown-header">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-premium" style="width: 44px; height: 44px;">{{ $userInitials }}</div>
                                    <div>
                                        <div style="font-size: 0.9375rem; font-weight: 700; color: var(--ink-primary);">{{ $userName }}</div>
                                        <div style="font-size: 0.75rem; color: var(--ink-tertiary);">{{ $userEmail }}</div>
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 0.5rem;">
                                <a href="{{ route('profile.edit') }}" class="dropdown-item-premium">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span data-i18n="menu_profile">Мой профиль</span>
                                </a>
                                <a href="{{ route('users.index') }}" class="dropdown-item-premium">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M16 12a4 4 0 100 8 4 4 0 000-8z"/></svg>
                                    <span data-i18n="menu_users">Пользователи</span>
                                </a>
                                <a href="#" class="dropdown-item-premium">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span data-i18n="menu_settings">Настройки</span>
                                </a>
                                <a href="#" class="dropdown-item-premium">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span data-i18n="menu_docs">Документы</span>
                                </a>
                            </div>
                            <div style="padding: 0.5rem; border-top: 1px solid var(--line-subtle); margin-top: 0.5rem;">
                                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                                    @csrf

                                    <button
                                        type="submit"
                                        class="dropdown-item-premium"
                                        style="
                color: #dc2626;
                width: 100%;
                background: none;
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 12px;
                text-align: left;
            "
                                    >
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                            />
                                        </svg>

                                        <span data-i18n="menu_logout">Выйти</span>
                                    </button>
                                </form>
                            </div>                        </div>
                    </div>

                    <!-- Mobile menu -->
                    <button onclick="toggleMobileMenu()" class="icon-btn d-lg-none" title="Меню">
                        <svg id="mobileMenuIcon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="d-none d-lg-none" style="padding: 1rem 0; border-top: 1px solid var(--line-subtle);">
                <div class="d-flex flex-column gap-1">
                    <a href="#" class="nav-link-premium" data-i18n="nav_home">Главная</a>
                    <a href="#features" class="nav-link-premium" data-i18n="nav_features">Возможности</a>
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2" style="border-top: 1px solid var(--line-subtle);">
                        <button onclick="toggleTheme()" class="icon-btn theme-toggle">
                            <svg class="icon-sun" fill="currentColor" viewBox="0 0 24 24" style="color: #fbbf24;"><path d="M12 3V1m0 22v-2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42M12 7a5 5 0 100 10 5 5 0 000-10z"/></svg>
                            <svg class="icon-moon" fill="currentColor" viewBox="0 0 24 24" style="color: #60a5fa;"><path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main><br><br><br>

        <!-- ============ HERO ============ -->
        <section class="container" style="max-width: 1280px;">
            <div class="hero-section reveal">
                <div class="hero-aurora hero-aurora-1"></div>
                <div class="hero-aurora hero-aurora-2"></div>
                <div class="hero-aurora hero-aurora-3"></div>

                <div class="row align-items-center g-4 w-100" style="position: relative; z-index: 2;">
                    <div class="col-lg-7">

                        <!-- Compact greeting -->
                        <div class="greeting-inline">
                            <div class="greeting-avatar-sm">{{ $userInitials }}</div>
                            <div class="greeting-text">
                                <span class="emoji">{{ $timeEmoji }}</span>
                                <span class="time-context" data-i18n="greeting_time">{{ $timeRu }}</span>
                                <span class="divider">·</span>
                                <span data-i18n="greeting_welcome_short">Привет,</span>
                                <span class="user-name">{{ $firstName }}</span>
                            </div>
                        </div>

                        <h1 class="hero-title">
                            <span data-i18n="hero_title_1">Юридические документы</span><br>
                            <span class="gradient-word" data-i18n="hero_title_2">с интеллектом AI</span>
                        </h1>
                        <!-- ... остальной код hero без изменений ... -->
                        <p class="hero-subtitle" data-i18n="hero_text">
                            Конструктор договоров, AI-анализ рисков, генератор, чат, Сравнение договора, калькулятор и база знаний — семь инструментов в одной платформе.
                        </p>
                        <div class="hero-actions">
                            <button onclick="openModal('builder')" class="btn-primary-premium">
                                <span data-i18n="hero_cta_start">Начать бесплатно</span>
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </button>
                            <a href="#features" class="btn-ghost-premium">
                                <span data-i18n="hero_cta_learn">Узнать больше</span>
                            </a>
                        </div>
                        <div class="hero-chips">
                            <span class="hero-chip" data-i18n="chip_builder">Конструктор</span>
                            <span class="hero-chip" data-i18n="chip_ai">AI-анализ</span>
                            <span class="hero-chip" data-i18n="chip_gen">Генератор</span>
                            <span class="hero-chip" data-i18n="chip_chat">AI Чат</span>
                            <span class="hero-chip" data-i18n="chip_calc">Калькулятор</span>
                            <span class="hero-chip" data-i18n="chip_kb">База знаний</span>
                            <span class="hero-chip" data-i18n="chip_compare">Сравнение Договора</span>

                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="hero-visual">
                            <!-- Compact dashboard -->
                            <div class="dashboard-card-compact">
                                <div class="dashboard-compact-header">
                                    <div class="dashboard-compact-title" data-i18n="dashboard_title">Активность сегодня</div>
                                    <div class="live-indicator" data-i18n="live">Live</div>
                                </div>
                                <div class="dashboard-grid">
                                    <div class="mini-stat">
                                        <div class="mini-stat-label">
                                            <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24" style="color: #60a5fa;"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span data-i18n="stat_docs">Документы</span>
                                        </div>
                                        <div class="mini-stat-value">12,480<span style="color: rgba(255,255,255,0.5); font-size: 0.75rem;">+</span></div>
                                        <div class="mini-stat-trend">↑ 24%</div>
                                    </div>
                                    <div class="mini-stat">
                                        <div class="mini-stat-label">
                                            <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24" style="color: #a78bfa;"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span data-i18n="stat_risks">Риски</span>
                                        </div>
                                        <div class="mini-stat-value">8,320</div>
                                        <div class="mini-stat-trend">↑ 18%</div>
                                    </div>
                                    <div class="mini-stat">
                                        <div class="mini-stat-label">
                                            <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24" style="color: #fb923c;"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            <span data-i18n="stat_templates">Шаблоны</span>
                                        </div>
                                        <div class="mini-stat-value">45<span style="color: rgba(255,255,255,0.5); font-size: 0.75rem;">+</span></div>
                                        <div class="mini-stat-trend">↑ 6</div>
                                    </div>
                                    <div class="mini-stat">
                                        <div class="mini-stat-label">
                                            <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24" style="color: #34d399;"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            <span data-i18n="stat_accuracy">Точность</span>
                                        </div>
                                        <div class="mini-stat-value">97.3<span style="color: rgba(255,255,255,0.5); font-size: 0.75rem;">%</span></div>
                                        <div class="mini-stat-trend">Premium</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ============ TRUSTED BY ============ -->
        <section class="trust-section container" style="max-width: 1280px;">
            <div class="trust-label" data-i18n="trust_label">Нам доверяют юридические команды по всему СНГ</div>
            <div class="trust-logos">
                <div class="trust-logo">LexCorps</div>
                <div class="trust-logo">•</div>
                <div class="trust-logo">Law&amp;Partners</div>
                <div class="trust-logo">•</div>
                <div class="trust-logo">ЮрКонсалт</div>
                <div class="trust-logo">•</div>
                <div class="trust-logo">LegalHub</div>
                <div class="trust-logo">•</div>
                <div class="trust-logo">PravoGroup</div>
            </div>
        </section>

        <!-- ============ FEATURES / BENTO GRID ============ -->
        <section id="features" class="container py-5" style="max-width: 1280px;">
            <div class="text-center mb-5 reveal">
        <span class="section-eyebrow" data-i18n="features_eyebrow">
            <span class="dot" style="width: 5px; height: 5px; border-radius: 50%; background: currentColor;"></span>
            Семь инструментов
        </span>
                <h2 class="section-title mt-3 mb-3" data-i18n="features_title">
                    Всё для работы с <span class="gradient-word">юридическими документами</span>
                </h2>
                <p class="section-subtitle mx-auto" data-i18n="features_text">
                    Полный набор инструментов для создания, анализа и работы с юридическими документами
                </p>
            </div>

            <div class="bento-grid">

                <!-- LARGE CARD: Builder -->
                <div class="bento-card bento-card-lg reveal stagger-1" onclick="openModal('builder')">
                    <div class="feature-badge blue mb-3"><span class="dot"></span><span data-i18n="builder_badge">Конструктор</span></div>
                    <div class="feature-icon-box blue">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="feature-title" data-i18n="builder_title">Конструктор договоров</h3>
                    <p class="feature-desc" data-i18n="builder_text">Интерактивный сборщик документов с выбором шаблонов, живым предпросмотром и экспортом в любой формат.</p>

                    <ul class="feature-list">
                        <li>
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="color: var(--accent-blue);"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span data-i18n="builder_feat1">3 типа договоров с гибкой настройкой</span>
                        </li>
                        <li>
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="color: var(--accent-blue);"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span data-i18n="builder_feat2">Live-предпросмотр в реальном времени</span>
                        </li>
                        <li>
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="color: var(--accent-blue);"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <span data-i18n="builder_feat3">Экспорт в Word, PDF, TXT</span>
                        </li>
                    </ul>

                    <a href="{{ route('contract.create') }}" class="feature-link" onclick="event.stopPropagation();" data-i18n="try_now">
                        Попробовать
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>

                    <svg class="bento-visual" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="40" y="40" width="120" height="140" rx="8" stroke="var(--accent-blue)" stroke-width="2" opacity="0.2"/>
                        <line x1="60" y1="70" x2="140" y2="70" stroke="var(--accent-blue)" stroke-width="2" opacity="0.3"/>
                        <line x1="60" y1="90" x2="120" y2="90" stroke="var(--accent-blue)" stroke-width="2" opacity="0.3"/>
                        <line x1="60" y1="110" x2="130" y2="110" stroke="var(--accent-blue)" stroke-width="2" opacity="0.3"/>
                        <line x1="60" y1="130" x2="100" y2="130" stroke="var(--accent-blue)" stroke-width="2" opacity="0.3"/>
                    </svg>
                </div>

                <!-- MD CARD: AI Analysis -->
                <div class="bento-card bento-card-md reveal stagger-2" onclick="openModal('ai-analysis')">
                    <div class="feature-badge purple mb-3"><span class="dot"></span><span data-i18n="ai_badge">AI Анализ</span></div>
                    <div class="feature-icon-box purple">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <h3 class="feature-title" data-i18n="ai_title">AI анализ договоров</h3>
                    <p class="feature-desc" data-i18n="ai_text">Мгновенный анализ рисков и рекомендации по правкам.</p>

                    <div class="d-flex align-items-center justify-content-between mt-auto">
                        <a href="{{ route('contract.form') }}" class="feature-link" style="color: var(--accent-purple);" onclick="event.stopPropagation();">
                            <span data-i18n="try_now">Попробовать</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-family: 'JetBrains Mono'; font-size: 0.75rem; color: var(--ink-tertiary);">92%</span>
                            <div class="progress-premium">
                                <div class="progress-premium-fill" style="width: 92%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WIDE CARD: Generator -->
                <div class="bento-card bento-card-wide reveal stagger-3" onclick="openModal('generator')">
                    <div class="feature-badge green mb-3"><span class="dot"></span><span data-i18n="gen_badge">Генератор</span></div>
                    <div class="feature-icon-box green">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="feature-title" data-i18n="gen_title">Генератор договоров</h3>
                    <p class="feature-desc" data-i18n="gen_text">Опишите что вам нужно и получите готовый договор за секунды.</p>

                    <div class="d-flex align-items-center justify-content-between mt-auto">
                        <a href="{{ route('contract.generate') }}" class="feature-link" style="color: var(--accent-green);" onclick="event.stopPropagation();">
                            <span data-i18n="try_now">Попробовать</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-family: 'JetBrains Mono'; font-size: 0.75rem; color: var(--ink-tertiary);">78%</span>
                            <div class="progress-premium">
                                <div class="progress-premium-fill" style="width: 78%; background: linear-gradient(135deg, #10b981, #06b6d4);"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WIDE CARD: AI Chat -->
                <div class="bento-card bento-card-wide reveal stagger-4" onclick="openModal('ai-chat')">
                    <div class="feature-badge orange mb-3"><span class="dot"></span><span data-i18n="chat_badge">AI Чат</span></div>
                    <div class="feature-icon-box orange">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h3 class="feature-title" data-i18n="chat_title">Юридический AI-чат</h3>
                    <p class="feature-desc" data-i18n="chat_text">Задайте любой юридический вопрос и получите экспертный ответ со ссылками на законы.</p>

                    <div class="d-flex align-items-center justify-content-between mt-auto">
                        <a href="{{ route('tasks.chat') }}" class="feature-link" style="color: var(--accent-orange);" onclick="event.stopPropagation();">
                            <span data-i18n="try_now">Попробовать</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-family: 'JetBrains Mono'; font-size: 0.75rem; color: var(--ink-tertiary);">88%</span>
                            <div class="progress-premium">
                                <div class="progress-premium-fill" style="width: 88%; background: linear-gradient(135deg, #f59e0b, #ef4444);"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SM CARD: Calculator -->
                <div class="bento-card bento-card-sm reveal stagger-5" onclick="openModal('calculator')">
                    <div class="feature-badge cyan mb-3"><span class="dot"></span><span data-i18n="calc_badge">Калькулятор</span></div>
                    <div class="feature-icon-box cyan">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="feature-title" data-i18n="calc_title">Юридический калькулятор</h3>
                    <p class="feature-desc" data-i18n="calc_text">Госпошлины, пени, неустойки, сроки давности.</p>

                    <div class="d-flex align-items-center justify-content-between mt-auto">
                        <a href="{{ route('tasks.calc') }}" class="feature-link" style="color: #0891b2;" onclick="event.stopPropagation();">
                            <span data-i18n="try_now">Попробовать</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" ВыviewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-family: 'JetBrains Mono'; font-size: 0.75rem; color: var(--ink-tertiary);">85%</span>
                            <div class="progress-premium">
                                <div class="progress-premium-fill" style="width: 85%; background: linear-gradient(135deg, #06b6d4, #3b82f6);"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SM CARD: Knowledge Base -->
                <div class="bento-card bento-card-sm reveal stagger-6" onclick="openModal('knowledge-base')">
                    <div class="feature-badge pink mb-3"><span class="dot"></span><span data-i18n="kb_badge">База знаний</span></div>
                    <div class="feature-icon-box pink">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="feature-title" data-i18n="kb_title">База знаний</h3>
                    <p class="feature-desc" data-i18n="kb_text">Справочник законов и кодексов с AI-поиском.</p>

                    <div class="d-flex align-items-center justify-content-between mt-auto">
                        <a href="{{ route('contract.codex') }}" class="feature-link" style="color: var(--accent-pink);" onclick="event.stopPropagation();">
                            <span data-i18n="try_now">Попробовать</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-family: 'JetBrains Mono'; font-size: 0.75rem; color: var(--ink-tertiary);">72%</span>
                            <div class="progress-premium">
                                <div class="progress-premium-fill" style="width: 72%; background: linear-gradient(135deg, #ec4899, #f43f5e);"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SM CARD: Contract Compare -->
                <div class="bento-card bento-card-sm reveal stagger-6" onclick="openModal('contractCompare')">
                    <div class="feature-badge purple mb-3"><span class="dot"></span><span>AI CONTRACT</span></div>
                    <div class="feature-icon-box purple">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                    </div>
                    <h3 class="feature-title">Сравнение договоров</h3>
                    <p class="feature-desc">Детальный анализ различий и юридических несоответствий.</p>

                    <div class="d-flex align-items-center justify-content-between mt-auto">
                        <a href="{{ route('contract.compare') }}" class="feature-link" style="color: var(--accent-purple);" onclick="event.stopPropagation();">
                            <span>Сравнить</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-family: 'JetBrains Mono'; font-size: 0.75rem; color: var(--ink-tertiary);">95%</span>
                            <div class="progress-premium">
                                <div class="progress-premium-fill" style="width: 95%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ HOW IT WORKS ============ -->
        <section class="container py-5" style="max-width: 1280px;">
            <div class="process-card reveal">
                <div class="text-center mb-5">
            <span class="section-eyebrow" data-i18n="process_eyebrow">
                <span class="dot" style="width: 5px; height: 5px; border-radius: 50%; background: currentColor;"></span>
                Процесс
            </span>
                    <h2 class="section-title mt-3 mb-3" data-i18n="process_title">
                        Как это <span class="gradient-word">работает</span>
                    </h2>
                    <p class="section-subtitle mx-auto" data-i18n="process_text">
                        Три простых шага от идеи до готового юридического документа
                    </p>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="process-step">
                            <div class="step-number">01</div>
                            <div>
                                <div class="step-title" data-i18n="step1_title">Выберите инструмент</div>
                                <p class="step-text" data-i18n="step1_text">6 профессиональных инструментов для любой юридической задачи</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="process-step">
                            <div class="step-number">02</div>
                            <div>
                                <div class="step-title" data-i18n="step2_title">Заполните параметры</div>
                                <p class="step-text" data-i18n="step2_text">Введите данные и условия в удобном интерфей</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="process-step">
                            <div class="step-number">03</div>
                            <div>
                                <div class="step-title" data-i18n="step3_title">Получите результат</div>
                                <p class="step-text" data-i18n="step3_text">Готовый документ, экспертный ответ или точный расчёт</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ STATS ============ -->
        <section id="stats" class="container py-5" style="max-width: 1280px;">
            <div class="row g-3">
                <div class="col-6 col-lg-3 reveal stagger-1">
                    <div class="stat-card-premium">
                        <div class="stat-icon" style="color: var(--accent-blue);">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="stat-value-premium"><span id="stat-docs">0</span></div>
                        <div class="stat-label-premium" data-i18n="stat_docs">Документов создано</div>
                        <div class="stat-trend">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            +24%
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 reveal stagger-2">
                    <div class="stat-card-premium">
                        <div class="stat-icon" style="color: var(--accent-purple);">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div class="stat-value-premium"><span id="stat-users">0</span><span class="suffix">K+</span></div>
                        <div class="stat-label-premium" data-i18n="stat_users">Активных пользователей</div>
                        <div class="stat-trend">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            +18%
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 reveal stagger-3">
                    <div class="stat-card-premium">
                        <div class="stat-icon" style="color: var(--accent-orange);">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="stat-value-premium"><span id="stat-risks">0</span><span class="suffix">K+</span></div>
                        <div class="stat-label-premium" data-i18n="stat_risks">Рисков выявлено</div>
                        <div class="stat-trend">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            +32%
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 reveal stagger-4">
                    <div class="stat-card-premium">
                        <div class="stat-icon" style="color: var(--accent-green);">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="stat-value-premium"><span id="stat-time">0</span></div>
                        <div class="stat-label-premium" data-i18n="stat_time">Часов сэкономлено</div>
                        <div class="stat-trend">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            +45%
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ CTA ============ -->
        <section class="container py-5" style="max-width: 1280px;">
            <div class="cta-section reveal">
                <div class="hero-aurora hero-aurora-1"></div>
                <div class="hero-aurora hero-aurora-2"></div>

                <div style="position: relative; z-index: 2;">
                    <h2 class="cta-title" data-i18n="cta_title">
                        Готовы <span class="gradient-word">начать?</span>
                    </h2>
                    <p class="cta-subtitle" data-i18n="cta_text">
                        Присоединяйтесь к тысячам юристов, которые уже используют LegalAI Pro для ежедневной работы
                    </p>
                    <div class="cta-actions">
                        <button onclick="openModal('builder')" class="btn-primary-premium">
                            <span data-i18n="cta_btn1">Создать договор</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </button>
                        <button onclick="openModal('ai-chat')" class="btn-ghost-premium">
                            <span data-i18n="cta_btn2">Задать вопрос AI</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- ============ FOOTER ============ -->
    <footer class="footer-premium">
        <div class="container py-5" style="max-width: 1280px;">
            <div class="row g-4 align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <svg width="24" height="24" viewBox="0 0 32 32" fill="none">
                            <defs>
                                <linearGradient id="logoGrad2" x1="0" y1="0" x2="32" y2="32">
                                    <stop offset="0%" stop-color="#2563eb"/>
                                    <stop offset="50%" stop-color="#7c3aed"/>
                                    <stop offset="100%" stop-color="#db2777"/>
                                </linearGradient>
                            </defs>
                            <rect width="32" height="32" rx="8" fill="url(#logoGrad2)"/>
                            <path d="M10 10 L14 14 L22 8 M10 16 L22 16 M10 22 L22 22" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="footer-brand">LegalAI Pro</span>
                    </div>
                    <p style="color: var(--ink-tertiary); font-size: 0.875rem; max-width: 400px;" data-i18n="footer_desc">
                        AI-платформа нового поколения для создания и анализа юридических документов
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex flex-wrap justify-content-md-end gap-2 mb-3">
                        <button class="lang-pill active" onclick="switchLang('ru')" data-lang="ru">RU</button>
                        <button class="lang-pill" onclick="switchLang('en')" data-lang="en">EN</button>
                        <button class="lang-pill" onclick="switchLang('tj')" data-lang="tj">TJ</button>
                    </div>
                    <div class="d-flex flex-wrap gap-3 justify-content-md-end mb-3">
                        <a href="#" class="footer-link" data-i18n="footer_privacy">Политика</a>
                        <a href="#" class="footer-link" data-i18n="footer_terms">Условия</a>
                        <a href="#" class="footer-link" data-i18n="footer_support">Поддержка</a>
                    </div>
                    <p style="color: var(--ink-quaternary); font-size: 0.8125rem;">
                        © 2025 LegalAI Pro. <span data-i18n="footer_rights">Все права защищены.</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to top -->
    <button class="back-to-top" id="backToTop" onclick="window.scrollTo({top:0, behavior:'smooth'})">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
    </button>

</div><!-- /.app-shell -->



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>

<script>
    // ===== I18N DATA =====
    const i18nData = {
        ru: {
            greeting_time_morning: "Доброе утро",
            greeting_time_afternoon: "Добрый день",
            greeting_time_evening: "Добрый вечер",
            greeting_time_night: "Доброй ночи",
            greeting_welcome: "Добро пожаловать,",
            greeting_streak: "Серия",
            nav_home: "Главная", nav_dashboard: "Рабочий стол", nav_features: "Возможности",
            hero_badge: "AI-платформа нового поколения",
            hero_title_1: "Юридические документы",
            hero_title_2: "с интеллектом AI",
            hero_text: "Конструктор договоров, AI-анализ рисков, генератор документов, Сравнение договора, AI-чат, калькулятор и база знаний — семь мощных инструментов в одной платформе.",
            hero_cta_start: "Начать бесплатно", hero_cta_learn: "Узнать больше",
            chip_builder: "Конструктор", chip_ai: "AI-анализ", chip_gen: "Генератор",
            chip_chat: "AI Чат", chip_calc: "Калькулятор", chip_kb: "База знаний",
            trust_label: "Нам доверяют юридические команды",
            features_eyebrow: "Семь инструментов",
            features_title: "Всё для работы с юридическими документами",
            features_text: "Полный набор инструментов для создания, анализа и работы с юридическими документами",
            builder_badge: "Конструктор", builder_title: "Конструктор договоров",
            builder_text: "Интерактивный сборщик документов с выбором шаблонов, живым предпросмотром и экспортом в любой формат.",
            builder_feat1: "3 типа договоров с гибкой настройкой",
            builder_feat2: "Live-предпросмотр в реальном времени",
            builder_feat3: "Экспорт в Word, PDF, TXT",
            ai_badge: "AI Анализ", ai_title: "AI анализ договоров",
            ai_text: "Мгновенный анализ рисков и рекомендации по правкам.",
            gen_badge: "Генератор", gen_title: "Генератор договоров",
            gen_text: "Опишите что вам нужно и получите готовый договор за секунды.",
            chat_badge: "AI Чат", chat_title: "Юридический AI-чат",
            chat_text: "Задайте любой юридический вопрос и получите экспертный ответ со ссылками на законы.",
            calc_badge: "Калькулятор", calc_title: "Юридический калькулятор",
            calc_text: "Госпошлины, пени, неустойки, сроки давности.",
            kb_badge: "База знаний", kb_title: "База знаний",
            kb_text: "Справочник законов и кодексов с AI-поиском.",
            try_now: "Попробовать",
            process_eyebrow: "Процесс", process_title: "Как это работает",
            process_text: "Три простых шага от идеи до готового юридического документа",
            step1_title: "Выберите инструмент", step1_text: "6 профессиональных инструментов для любой юридической задачи",
            step2_title: "Заполните параметры", step2_text: "Введите данные и условия в удобном интерфей",
            step3_title: "Получите результат", step3_text: "Готовый документ, экспертный ответ или точный расчёт",
            stat_docs: "Документов создано", stat_users: "Активных пользователей",
            stat_risks: "Рисков выявлено", stat_time: "Часов сэкономлено",
            cta_title: "Готовы начать?", cta_text: "Присоединяйтесь к тысячам юристов",
            cta_btn1: "Создать договор", cta_btn2: "Задать вопрос AI",
            footer_desc: "AI-платформа нового поколения для создания и анализа юридических документов",
            footer_privacy: "Политика", footer_terms: "Условия", footer_support: "Поддержка",
            footer_rights: "Все права защищены.",
            menu_profile: "Мой профиль", menu_users: "Пользователи",
            menu_settings: "Настройки", menu_docs: "Документы", menu_logout: "Выйти",
            notif_title: "Уведомления",
            notif_1: "Новый шаблон договора доступен",
            notif_2: "Обновлена база кодексов",
            notif_3: "AI улучшил точность анализа",
            modal_builder_title: "Конструктор договоров",
            modal_builder_text: "Выберите шаблон и заполните параметры",
            modal_ai_title: "AI анализ договоров",
            modal_ai_text: "Вставьте текст договора для анализа",
            modal_gen_title: "Генератор договоров"
        },
        en: {
            greeting_time_morning: "Good morning",
            greeting_time_afternoon: "Good afternoon",
            greeting_time_evening: "Good evening",
            greeting_time_night: "Good night",
            greeting_welcome: "Welcome back,",
            greeting_streak: "Streak",
            nav_home: "Home", nav_dashboard: "Dashboard", nav_features: "Features",
            hero_badge: "Next-gen AI platform",
            hero_title_1: "Legal documents",
            hero_title_2: "powered by AI",
            hero_text: "Contract builder, AI risk analysis, document generator, AI chat, calculator and knowledge base — six powerful tools in one platform.",
            hero_cta_start: "Start free", hero_cta_learn: "Learn more",
            chip_builder: "Builder", chip_ai: "AI Analysis", chip_gen: "Generator",
            chip_chat: "AI Chat", chip_calc: "Calculator", chip_kb: "Knowledge Base",
            trust_label: "Trusted by legal teams",
            features_eyebrow: "Six tools",
            features_title: "Everything for legal documents",
            features_text: "Complete toolkit for creating, analyzing and working with legal documents",
            builder_badge: "Builder", builder_title: "Contract Builder",
            builder_text: "Interactive document builder with templates, live preview and multi-format export.",
            builder_feat1: "3 contract types with flexible settings",
            builder_feat2: "Real-time live preview",
            builder_feat3: "Export to Word, PDF, TXT",
            ai_badge: "AI Analysis", ai_title: "AI Contract Analysis",
            ai_text: "Instant risk analysis and recommendations.",
            gen_badge: "Generator", gen_title: "Contract Generator",
            gen_text: "Describe what you need and get a ready contract in seconds.",
            chat_badge: "AI Chat", chat_title: "Legal AI Chat",
            chat_text: "Ask any legal question and get an expert answer with law references.",
            calc_badge: "Calculator", calc_title: "Legal Calculator",
            calc_text: "Court fees, penalties, deadlines.",
            kb_badge: "Knowledge Base", kb_title: "Knowledge Base",
            kb_text: "Laws and codes with AI search.",
            try_now: "Try now",
            process_eyebrow: "Process", process_title: "How it works",
            process_text: "Three simple steps from idea to finished legal document",
            step1_title: "Choose a tool", step1_text: "6 professional tools for any legal task",
            step2_title: "Fill parameters", step2_text: "Enter data and conditions in a convenient interface",
            step3_title: "Get results", step3_text: "Ready document, expert answer or accurate calculation",
            stat_docs: "Documents created", stat_users: "Active users",
            stat_risks: "Risks detected", stat_time: "Hours saved",
            cta_title: "Ready to start?", cta_text: "Join thousands of lawyers",
            cta_btn1: "Create contract", cta_btn2: "Ask AI",
            footer_desc: "Next-gen AI platform for legal documents",
            footer_privacy: "Privacy", footer_terms: "Terms", footer_support: "Support",
            footer_rights: "All rights reserved.",
            menu_profile: "My profile", menu_users: "Users",
            menu_settings: "Settings", menu_docs: "Documents", menu_logout: "Sign out",
            notif_title: "Notifications",
            notif_1: "New contract template available",
            notif_2: "Code database updated",
            notif_3: "AI improved analysis accuracy",
            modal_builder_title: "Contract Builder",
            modal_builder_text: "Choose template and fill parameters",
            modal_ai_title: "AI Contract Analysis",
            modal_ai_text: "Paste contract text for analysis",
            modal_gen_title: "Contract Generator"
        },
        tj: {
            greeting_time_morning: "Субҳ ба хайр",
            greeting_time_afternoon: "Рӯз ба хайр",
            greeting_time_evening: "Бегоҳ ба хайр",
            greeting_time_night: "Шаб ба хайр",
            greeting_welcome: "Хуш омадед,",
            greeting_streak: "Силсила",
            nav_home: "Асосӣ", nav_dashboard: "Мизи корӣ", nav_features: "Имкониятҳо",
            hero_badge: "Платформаи AI насли нав",
            hero_title_1: "Ҳуҷҷатҳои ҳуқуқӣ",
            hero_title_2: "бо зеҳни AI",
            hero_text: "Сохтмони шартномаҳо, таҳлили хавфҳо, генератор, чати AI, калкулятор ва базаи дониш.",
            hero_cta_start: "Ройгон оғоз", hero_cta_learn: "Бештар бидонед",
            chip_builder: "Сохтмон", chip_ai: "Таҳлили AI", chip_gen: "Генератор",
            chip_chat: "Чати AI", chip_calc: "Калкулятор", chip_kb: "Базаи дониш",
            trust_label: "Ба мо боварӣ мекунанд",
            features_eyebrow: "Шаш абзор",
            features_title: "Ҳама барои ҳуҷҷатҳои ҳуқуқӣ",
            features_text: "Маҷмӯи пурраи абзорҳо",
            builder_badge: "Сохтмон", builder_title: "Сохтмони шартномаҳо",
            builder_text: "Сохтмони интерактивии ҳуҷҷатҳо.",
            builder_feat1: "3 намуди шартнома",
            builder_feat2: "Пешнамоиши зинда",
            builder_feat3: "Содирот Word, PDF, TXT",
            ai_badge: "Таҳлили AI", ai_title: "Таҳлили AI",
            ai_text: "Таҳлили фаврии хавфҳо.",
            gen_badge: "Генератор", gen_title: "Генератори шартномаҳо",
            gen_text: "Тавсиф кунед ва гиред.",
            chat_badge: "Чати AI", chat_title: "Чати ҳуқуқӣ",
            chat_text: "Саволи ҳуқуқӣ пурсед.",
            calc_badge: "Калкулятор", calc_title: "Калкулятори ҳуқуқӣ",
            calc_text: "Ҳисобҳо.",
            kb_badge: "Базаи дониш", kb_title: "Базаи дониш",
            kb_text: "Қонунҳо бо ҷустуҷӯи AI.",
            try_now: "Санҷед",
            process_eyebrow: "Раванд", process_title: "Чӣ тавр кор мекунад",
            process_text: "Се қадами оддӣ",
            step1_title: "Интихоб кунед", step1_text: "6 абзор",
            step2_title: "Пур кунед", step2_text: "Маълумот",
            step3_title: "Натиҷа", step3_text: "Ҳуҷҷат ё ҷавоб",
            stat_docs: "Ҳуҷҷатҳо", stat_users: "Истифодабарандагон",
            stat_risks: "Хавфҳо", stat_time: "Соатҳо",
            cta_title: "Омодаед?", cta_text: "Ҳамроҳ шавед",
            cta_btn1: "Эҷод кунед", cta_btn2: "Савол пурсед",
            footer_desc: "Платформаи AI",
            footer_privacy: "Махфият", footer_terms: "Шартҳо", footer_support: "Дастгирӣ",
            footer_rights: "Ҳамаи ҳуқуқҳо.",
            menu_profile: "Профил", menu_users: "Корбарон",
            menu_settings: "Танзимот", menu_docs: "Ҳуҷҷатҳо", menu_logout: "Баромад",
            notif_title: "Огоҳиҳо",
            notif_1: "Шаблон нав", notif_2: "Навсозӣ", notif_3: "AI беҳтар шуд",
            modal_builder_title: "Сохтмони шартномаҳо",
            modal_builder_text: "Шаблонро интихоб кунед",
            modal_ai_title: "Таҳлили AI",
            modal_ai_text: "Матнро ворид кунед",
            modal_gen_title: "Генератори шартномаҳо"
        }
    };

    let currentLang = 'ru';

    function switchLang(lang) {
        currentLang = lang;
        document.documentElement.lang = lang;
        localStorage.setItem('legalai-lang', lang);

        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (i18nData[lang] && i18nData[lang][key]) {
                el.textContent = i18nData[lang][key];
            }
        });

        document.querySelectorAll('.lang-pill').forEach(btn => {
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

    // ===== NAVBAR SCROLL EFFECT =====
    const mainNav = document.getElementById('mainNav');
    const backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            mainNav?.classList.add('scrolled');
            backToTop?.classList.add('visible');
        } else {
            mainNav?.classList.remove('scrolled');
            backToTop?.classList.remove('visible');
        }
    });

    // ===== REVEAL ON SCROLL =====
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    // ===== SPOTLIGHT EFFECT ON BENTO CARDS =====
    document.querySelectorAll('.bento-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            card.style.setProperty('--mouse-x', x + '%');
            card.style.setProperty('--mouse-y', y + '%');
        });
    });

    // ===== MOBILE MENU =====
    function toggleMobileMenu() {
        document.getElementById('mobileMenu')?.classList.toggle('d-none');
    }

    // ===== PROFILE =====
    function toggleProfile() {
        const dropdown = document.getElementById('profileDropdown');
        const arrow = document.getElementById('profileArrow');
        dropdown?.classList.toggle('d-none');
        if (arrow) arrow.style.transform = dropdown?.classList.contains('d-none') ? 'rotate(0)' : 'rotate(180deg)';
    }

    // ===== NOTIFICATIONS =====
    function toggleNotifications() {
        document.getElementById('notificationPanel')?.classList.toggle('d-none');
    }

    document.addEventListener('click', function(e) {
        const pc = document.getElementById('profileContainer');
        const pd = document.getElementById('profileDropdown');
        if (pc && !pc.contains(e.target)) { pd?.classList.add('d-none'); }

        const np = document.getElementById('notificationPanel');
        const nb = e.target.closest('button[onclick="toggleNotifications()"]');
        if (np && !np.contains(e.target) && !nb) np?.classList.add('d-none');
    });

    // ===== MODALS =====
    function openModal(name) {
        const modal = document.getElementById('modal-' + name);
        if (modal) { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
    }
    function closeModal(name) {
        const modal = document.getElementById('modal-' + name);
        if (modal) { modal.classList.remove('active'); document.body.style.overflow = ''; }
    }
    function closeModalOutside(event, name) {
        if (event.target.classList.contains('modal-backdrop-premium')) closeModal(name);
    }

    // ===== BUILDER =====
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
        previewBuilder();
    }

    function formatAmount(val) { return new Intl.NumberFormat('ru-RU').format(Number(val || 0)) + ' ₽'; }
    function formatDate(val) { if (!val) return 'не указана'; return new Intl.DateTimeFormat('ru-RU').format(new Date(val)); }

    function previewBuilder() {
        const t = builderState.template, roles = templateRoles[t], label = templateLabels[t];
        const num = document.getElementById('b-contractNumber')?.value || 'б/н';
        const city = document.getElementById('b-contractCity')?.value || 'не указан';
        const customer = document.getElementById('b-customer')?.value || 'Сторона 1';
        const contractor = document.getElementById('b-contractor')?.value || 'Сторона 2';
        const amount = document.getElementById('b-amount')?.value || '0';
        const startD = document.getElementById('b-startDate')?.value;
        const endD = document.getElementById('b-endDate')?.value;

        const text = `${label} № ${num}\nг. ${city}\n${formatDate(startD)}\n\n${customer}, «${roles.roleA}», и ${contractor}, «${roles.roleB}»:\n\n1. Предмет\n${roles.roleB} обязуется выполнить работы, ${roles.roleA} — принять и оплатить.\n\n2. Цена: ${formatAmount(amount)}\nОплата в течение 5 дней.\n\n3. Ответственность: неустойка 0,1%/день\n\n4. Срок: с ${formatDate(startD)} до ${formatDate(endD)}\n\n5. Реквизиты\n${roles.roleA}: ${customer}\n${roles.roleB}: ${contractor}`;

        const previewText = document.getElementById('builder-preview-text');
        const preview = document.getElementById('builder-preview');
        if (previewText) previewText.textContent = text;
        if (preview) preview.style.display = 'block';
    }

    function downloadContract(format) {
        const text = document.getElementById('builder-preview-text')?.textContent;
        if (!text) return;
        if (format === 'txt') { const b = new Blob([text], {type:'text/plain;charset=utf-8'}); const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = 'contract.txt'; a.click(); }
        else if (format === 'word') { const html = `<html><head><meta charset="UTF-8"><style>body{font-family:serif;font-size:12pt;line-height:1.6;margin:24px}</style></head><body><pre style="white-space:pre-wrap">${text.replace(/&/g,'&amp;').replace(/</g,'&lt;')}</pre></body></html>`; const b = new Blob(['\ufeff', html], {type:'application/msword'}); const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = 'contract.doc'; a.click(); }
        else if (format === 'pdf' && window.html2pdf) { const el = document.createElement('div'); el.style.cssText = 'padding:28px;background:#fff;font-family:serif;font-size:16px;line-height:1.7;white-space:pre-wrap'; el.textContent = text; html2pdf().set({margin:[12,12,12,12],filename:'contract.pdf',html2canvas:{scale:2},jsPDF:{unit:'mm',format:'a4'}}).from(el).save(); }
    }

    // ===== AI ANALYSIS =====
    function analyzeContract() {
        const text = document.getElementById('ai-contract-text')?.value.trim();
        if (!text) { alert('Введите текст договора'); return; }
        const btn = document.getElementById('analyze-btn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Анализируем...';
        btn.disabled = true;

        setTimeout(() => {
            const risks = [], recommendations = [];
            if (text.includes('50%')) { risks.push({level:'high',text:'Высокий штраф (50%)'}); recommendations.push('Уменьшите штраф до 0,1%-1%/день.'); }
            if (text.includes('30 дней')) { risks.push({level:'medium',text:'Длительный срок оплаты'}); recommendations.push('Сократите до 5-10 дней.'); }
            if (!text.includes('форс-мажор')) { risks.push({level:'medium',text:'Нет раздела о форс-мажоре'}); recommendations.push('Добавьте раздел о форс-мажоре.'); }
            if (!risks.length) { risks.push({level:'low',text:'Критических рисков не найдено'}); recommendations.push('Документ корректен.'); }

            const score = Math.max(0, Math.min(100, 100 - risks.filter(r=>r.level==='high').length*15 - risks.filter(r=>r.level==='medium').length*8));
            document.getElementById('ai-score').textContent = score;
            document.getElementById('ai-risks-count').textContent = risks.length;
            document.getElementById('ai-sections-count').textContent = Math.max(3, Math.floor(text.split('\n').length/5));

            const rl = document.getElementById('ai-risk-list');
            rl.innerHTML = risks.map(r => `<div style="background: var(--bg-sunken); padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 0.5rem; border-left: 3px solid ${r.level==='high'?'#dc2626':r.level==='medium'?'#ea580c':'#16a34a'}"><strong style="color: var(--ink-primary);">${r.text}</strong></div>`).join('');

            const rd = document.getElementById('ai-recommendations');
            rd.innerHTML = '<div style="font-weight: 600; margin: 1rem 0 0.5rem; color: var(--ink-primary);">Рекомендации</div>' +
                recommendations.map((rec,i) => `<div style="background: rgba(34,197,94,0.08); padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 0.5rem; font-size: 0.875rem;"><strong style="color: #16a34a;">${i+1}.</strong> ${rec}</div>`).join('');

            document.getElementById('ai-results').style.display = 'block';
            btn.innerHTML = 'Запустить AI-анализ';
            btn.disabled = false;
        }, 1500);
    }

    // ===== GENERATOR =====
    function generateContract() {
        const prompt = document.getElementById('gen-prompt')?.value.trim();
        if (!prompt) { alert('Опишите договор'); return; }
        const btn = document.getElementById('generate-btn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Генерируем...';
        btn.disabled = true;

        setTimeout(() => {
            const type = document.getElementById('gen-type')?.value;
            let contract = type === 'rent' ? 'ДОГОВОР АРЕНДЫ\n\n1. Предмет...\n2. Срок: 12 мес.' : type === 'service' ? 'ДОГОВОР УСЛУГ\n\n1. Предмет...\n2. Оплата...' : 'ДОГОВОР\n\n1. Предмет...';
            document.getElementById('gen-result-text').textContent = contract;
            document.getElementById('gen-results').style.display = 'block';
            btn.innerHTML = 'Сгенерировать';
            btn.disabled = false;
        }, 2000);
    }

    function downloadGenerated(format) {
        const text = document.getElementById('gen-result-text')?.textContent;
        if (!text) return;
        if (format === 'txt') { const b = new Blob([text], {type:'text/plain;charset=utf-8'}); const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = 'contract.txt'; a.click(); }
    }

    function copyGenerated() {
        const text = document.getElementById('gen-result-text')?.textContent;
        navigator.clipboard.writeText(text).then(() => alert('Скопировано!'));
    }

    // ===== AI CHAT =====
    const chatResponses = {
        'договор': 'Для заключения договора необходимо:\n\n1. Определить предмет\n2. Согласовать условия\n3. Подписать документ',
        'default': 'Спасибо за вопрос! Задайте более конкретный юридический вопрос.'
    };

    function sendChatMessage() {
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if (!text) return;

        const messages = document.getElementById('chatMessages');
        const div = document.createElement('div');
        div.className = 'chat-message user';
        div.textContent = text;
        messages.appendChild(div);
        input.value = '';

        setTimeout(() => {
            const aiDiv = document.createElement('div');
            aiDiv.className = 'chat-message ai';
            aiDiv.innerHTML = (chatResponses[text.toLowerCase()] || chatResponses.default).replace(/\n/g, '<br>');
            messages.appendChild(aiDiv);
            messages.scrollTop = messages.scrollHeight;
        }, 600);
        messages.scrollTop = messages.scrollHeight;
    }

    // ===== CALCULATOR =====
    function updateCalcFields() {
        const type = document.getElementById('calcType')?.value;
        document.getElementById('calc-penalty-fields').style.display = type === 'penalty' ? 'block' : 'none';
        document.getElementById('calc-court-fields').style.display = type === 'court_fee' ? 'block' : 'none';
        document.getElementById('calc-limitation-fields').style.display = type === 'limitation' ? 'block' : 'none';
        document.getElementById('calc-result').style.display = 'none';
    }

    function calculateResult() {
        const type = document.getElementById('calcType')?.value;
        const resultDiv = document.getElementById('calc-result');
        let html = '';

        if (type === 'penalty') {
            const debt = Number(document.getElementById('calc-debt')?.value) || 0;
            const rate = Number(document.getElementById('calc-rate')?.value) || 0;
            const startDate = document.getElementById('calc-dateStart')?.value;
            const endDate = document.getElementById('calc-dateEnd')?.value;
            let days = 0;
            if (startDate && endDate) days = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24));
            const penalty = debt * rate / 100 * days;
            html = `<div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--line-subtle);"><span>Долг</span><strong>${debt.toLocaleString('ru-RU')} ₽</strong></div>
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--line-subtle);"><span>Дней</span><strong>${days}</strong></div>
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 1.125rem; font-weight: 700; color: var(--accent-blue);"><span>Итого</span><span>${(debt + penalty).toLocaleString('ru-RU')} ₽</span></div>`;
        } else if (type === 'court_fee') {
            const claim = Number(document.getElementById('calc-claim')?.value) || 0;
            let fee = claim * 0.04;
            html = `<div style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 1.125rem; font-weight: 700; color: var(--accent-blue);"><span>Госпошлина</span><span>${Math.round(fee).toLocaleString('ru-RU')} ₽</span></div>`;
        }
        resultDiv.innerHTML = html;
        resultDiv.style.display = 'block';
    }

    // ===== KB =====
    function filterKB() {
        const query = document.getElementById('kbSearch')?.value.toLowerCase();
        document.querySelectorAll('.kb-item').forEach(item => {
            const text = (item.dataset.keywords || '') + ' ' + item.textContent;
            item.style.display = text.toLowerCase().includes(query) ? 'block' : 'none';
        });
    }

    // ===== COUNTERS =====
    function animateCounter(el, target, duration = 2000) {
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
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !countersAnimated) {
                countersAnimated = true;
                animateCounter(document.getElementById('stat-docs'), 27, 2000);
                animateCounter(document.getElementById('stat-users'), 5, 2000);
                animateCounter(document.getElementById('stat-risks'), 14, 2000);
                animateCounter(document.getElementById('stat-time'), 3890, 2000);
            }
        });
    }, { threshold: 0.3 });

    const statsSection = document.getElementById('stats');
    if (statsSection) statsObserver.observe(statsSection);

    // ===== ESCAPE KEY =====
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop-premium.active').forEach(m => { m.classList.remove('active'); document.body.style.overflow = ''; });
            document.getElementById('profileDropdown')?.classList.add('d-none');
            document.getElementById('notificationPanel')?.classList.add('d-none');
        }
    });

    // ===== INIT DATES =====
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().split('T')[0];
        const bStart = document.getElementById('b-startDate');
        const bEnd = document.getElementById('b-endDate');
        if (bStart) bStart.value = today;
        if (bEnd) bEnd.value = new Date(Date.now() + 90 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    });
</script>

</body>
</html>
