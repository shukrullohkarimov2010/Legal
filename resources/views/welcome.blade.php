<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.welcome_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        /* ========== CSS VARIABLES ========== */
        :root {
            --bg-primary: #0B1120;
            --bg-secondary: #111827;
            --card-bg: rgba(17, 24, 39, 0.7);
            --card-border: rgba(55, 65, 81, 0.5);
            --card-hover-border: rgba(59, 130, 246, 0.4);
            --text-primary: #F9FAFB;
            --text-secondary: #9CA3AF;
            --text-muted: #6B7280;
            --accent-primary: #3B82F6;
            --accent-secondary: #1D4ED8;
            --accent-gradient: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 50%, #EC4899 100%);
            --accent-glow: rgba(59, 130, 246, 0.25);
            --success: #10B981;
            --warning: #F59E0B;
            --error: #EF4444;
            --glass-blur: blur(16px);
            --shadow-card: 0 8px 32px rgba(0, 0, 0, 0.3), 0 2px 8px rgba(0, 0, 0, 0.2);
            --shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.4), 0 4px 16px rgba(59, 130, 246, 0.15);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-full: 9999px;
            --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-normal: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ========== BASE STYLES ========== */
        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            overflow-x: hidden;
            transition: background var(--transition-normal), color var(--transition-normal);
            line-height: 1.6;
            position: relative;
        }

        /* ========== ANIMATED BACKGROUND ========== */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(600px circle at 20% 30%, rgba(59, 130, 246, 0.12) 0%, transparent 50%),
                radial-gradient(500px circle at 80% 70%, rgba(139, 92, 246, 0.08) 0%, transparent 50%),
                radial-gradient(400px circle at 40% 80%, rgba(236, 72, 153, 0.06) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
            animation: bgShift 20s ease-in-out infinite alternate;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(500px circle at 70% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                radial-gradient(400px circle at 20% 60%, rgba(139, 92, 246, 0.08) 0%, transparent 50%),
                radial-gradient(300px circle at 60% 40%, rgba(236, 72, 153, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
            animation: bgShift 25s ease-in-out infinite alternate-reverse;
        }

        @keyframes bgShift {
            0% {
                transform: translate(0, 0) scale(1);
            }
            25% {
                transform: translate(-20px, 30px) scale(1.02);
            }
            50% {
                transform: translate(30px, -20px) scale(0.98);
            }
            75% {
                transform: translate(-10px, -30px) scale(1.01);
            }
            100% {
                transform: translate(20px, 20px) scale(1);
            }
        }

        /* Grid pattern overlay */
        .bg-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(55, 65, 81, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(55, 65, 81, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: -1;
        }

        /* Floating particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            animation: floatParticle 15s infinite linear;
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 20s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 2s; animation-duration: 18s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 4s; animation-duration: 22s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 1s; animation-duration: 16s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 3s; animation-duration: 19s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 5s; animation-duration: 21s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 0.5s; animation-duration: 17s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 2.5s; animation-duration: 23s; }
        .particle:nth-child(9) { left: 90%; animation-delay: 4.5s; animation-duration: 18s; }

        @keyframes floatParticle {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(720deg);
                opacity: 0;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--text-muted);
            border-radius: var(--radius-full);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }

        /* Selection */
        ::selection {
            background: var(--accent-primary);
            color: white;
        }

        /* ========== LIGHT THEME ========== */
        body.light-theme {
            --bg-primary: #FFFFFF;
            --bg-secondary: #F9FAFB;
            --card-bg: rgba(255, 255, 255, 0.8);
            --card-border: rgba(209, 213, 219, 0.5);
            --card-hover-border: rgba(59, 130, 246, 0.3);
            --text-primary: #111827;
            --text-secondary: #4B5563;
            --text-muted: #9CA3AF;
            --shadow-card: 0 4px 16px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(0, 0, 0, 0.04);
            --shadow-hover: 0 12px 32px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        body.light-theme::before,
        body.light-theme::after {
            background:
                radial-gradient(600px circle at 20% 30%, rgba(59, 130, 246, 0.05) 0%, transparent 50%),
                radial-gradient(500px circle at 80% 70%, rgba(139, 92, 246, 0.03) 0%, transparent 50%);
        }

        body.light-theme h1, body.light-theme h2, body.light-theme h3,
        body.light-theme h4, body.light-theme h5, body.light-theme h6 {
            color: #111827;
        }

        body.light-theme p, body.light-theme span, body.light-theme div,
        body.light-theme label, body.light-theme li {
            color: var(--text-secondary);
        }

        /* ========== NAVIGATION ========== */
        nav {
            position: fixed;        /* ← БЫЛО ДОБАВЛЕНО */
            top: 0;                 /* ← БЫЛО ДОБАВЛЕНО */
            left: 0;                /* ← БЫЛО ДОБАВЛЕНО */
            right: 0;               /* ← БЫЛО ДОБАВЛЕНО */
            z-index: 9999;          /* ← БЫЛО ДОБАВЛЕНО */
            background: rgba(11, 17, 32, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--card-border);
        }

        body.light-theme nav {
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(209, 213, 219, 0.5);
        }

        .nav-link {
            color: var(--text-secondary);
            transition: color var(--transition-fast);
            position: relative;
            padding: 8px 0;
        }

        .nav-link:hover {
            color: var(--text-primary);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent-gradient);
            transition: width var(--transition-normal);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* ========== CARDS & CONTAINERS ========== */
        .card-gradient {
            background: var(--card-bg);
            backdrop-filter: var(--glass-blur);
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow-card);
            transition: all var(--transition-normal);
            border-radius: var(--radius-lg);
        }

        .card-gradient:hover {
            border-color: var(--card-hover-border);
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        /* Buttons */
        .btn-primary {
            background: var(--accent-gradient);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 14px var(--accent-glow);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px var(--accent-glow);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: transparent;
            border: 1px solid var(--card-border);
            color: var(--text-primary);
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
            backdrop-filter: blur(8px);
        }

        .btn-secondary:hover {
            border-color: var(--accent-primary);
            background: rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
        }

        /* Inputs */
        input, select, textarea {
            background: rgba(17, 24, 39, 0.6);
            border: 1px solid var(--card-border);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            outline: none;
            transition: all var(--transition-fast);
            width: 100%;
            backdrop-filter: blur(8px);
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            background: rgba(17, 24, 39, 0.8);
        }

        body.light-theme input, body.light-theme select, body.light-theme textarea {
            background: rgba(255, 255, 255, 0.8);
            color: #111827;
            border-color: rgba(209, 213, 219, 0.5);
        }

        /* ========== SECTIONS & INFO ========== */
        .info-section {
            background: var(--card-bg);
            backdrop-filter: var(--glass-blur);
            border: 1px solid var(--card-border);
            box-shadow: var(--shadow-card);
            transition: all var(--transition-normal);
            border-radius: var(--radius-xl);
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .info-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.5), transparent);
        }

        body.light-theme .info-section {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(209, 213, 219, 0.4);
        }

        /* Upload Zone */
        .upload-zone {
            border: 2px dashed rgba(59, 130, 246, 0.4);
            transition: all var(--transition-normal);
            cursor: pointer;
            border-radius: var(--radius-lg);
        }

        .upload-zone:hover {
            border-color: var(--accent-primary);
            background: rgba(59, 130, 246, 0.05);
        }

        .upload-zone.dragover {
            border-color: var(--success);
            background: rgba(16, 185, 129, 0.1);
        }

        /* Risk Badges */
        .risk-high {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        }

        .risk-medium {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        }

        .risk-low {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }

        /* Step Cards */
        .step-card {
            transition: all var(--transition-normal);
        }

        .step-card:hover {
            transform: translateY(-8px);
        }

        .step-number {
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
            transition: all var(--transition-normal);
        }

        .step-card:hover .step-number {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
            box-shadow: 0 8px 24px var(--accent-glow);
        }

        .step-card:hover .step-number span {
            color: white;
        }

        /* Feature Cards */
        .feature-card {
            transition: all var(--transition-slow);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.08), transparent);
            transition: left 0.6s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: var(--accent-primary);
        }

        .feature-card:hover::before {
            left: 100%;
        }

        /* ========== ANIMATIONS ========== */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.05); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        @keyframes float-slow {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            33% { transform: translateY(-8px) rotate(1deg); }
            66% { transform: translateY(4px) rotate(-1deg); }
        }

        @keyframes glow {
            0%, 100% { opacity: 0.5; filter: blur(8px); }
            50% { opacity: 0.8; filter: blur(12px); }
        }

        .slide-in { animation: slideUp 0.7s ease forwards; }
        .scale-in { animation: scaleIn 0.4s ease forwards; }

        /* Reveal on Scroll */
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* Hero background effects */
        .hero-bg {
            position: relative;
            overflow: hidden;
        }

        .hero-bg::before {
            content: '';
            position: absolute;
            top: 10%;
            left: 5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
            filter: blur(60px);
            animation: float-slow 12s ease-in-out infinite;
            pointer-events: none;
        }

        .hero-bg::after {
            content: '';
            position: absolute;
            bottom: 10%;
            right: 5%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.12) 0%, transparent 70%);
            filter: blur(60px);
            animation: float-slow 15s ease-in-out infinite reverse;
            pointer-events: none;
        }

        /* Gradient Text */
        .glow-text {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }

        body.light-theme .glow-text {
            text-shadow: none;
        }

        .text-gradient {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Skeleton */
        .skeleton {
            background: linear-gradient(90deg,
            rgba(255, 255, 255, 0.05) 25%,
            rgba(255, 255, 255, 0.1) 50%,
            rgba(255, 255, 255, 0.05) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: var(--radius-md);
        }

        body.light-theme .skeleton {
            background: linear-gradient(90deg,
            #E5E7EB 25%,
            #F3F4F6 50%,
            #E5E7EB 75%);
            background-size: 200% 100%;
        }

        .skeleton-text { height: 1rem; margin: 0.5rem 0; }
        .skeleton-title { height: 1.5rem; width: 60%; margin: 1rem 0; }
        .skeleton-card { height: 200px; }

        /* Sidebar */
        .sidebar-item {
            transition: all var(--transition-fast);
            border-radius: var(--radius-md);
        }

        .sidebar-item:hover, .sidebar-item.active {
            background: rgba(59, 130, 246, 0.15);
            border-left: 3px solid var(--accent-primary);
            color: var(--text-primary) !important;
        }

        body.light-theme .sidebar-item:hover,
        body.light-theme .sidebar-item.active {
            background: rgba(59, 130, 246, 0.1);
        }

        /* Chat */
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 500px;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .chat-message {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            animation: slideUp 0.3s ease;
        }

        .chat-message.user { flex-direction: row-reverse; }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .chat-avatar.ai { background: var(--accent-gradient); }
        .chat-avatar.user { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }

        .chat-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 16px;
            line-height: 1.5;
        }

        .chat-message.ai .chat-bubble {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-top-left-radius: 4px;
            color: var(--text-primary);
        }

        body.light-theme .chat-message.ai .chat-bubble {
            background: rgba(59, 130, 246, 0.08);
            border-color: rgba(59, 130, 246, 0.15);
        }

        .chat-message.user .chat-bubble {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-top-right-radius: 4px;
            color: var(--text-primary);
        }

        body.light-theme .chat-message.user .chat-bubble {
            background: rgba(16, 185, 129, 0.08);
            border-color: rgba(16, 185, 129, 0.15);
        }

        .chat-input-area {
            padding: 16px;
            border-top: 1px solid var(--card-border);
            display: flex;
            gap: 12px;
        }

        .chat-input {
            flex: 1;
            background: rgba(17, 24, 39, 0.6);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 12px 16px;
            color: var(--text-primary);
            font-size: 14px;
            outline: none;
            transition: all var(--transition-fast);
            backdrop-filter: blur(8px);
        }

        .chat-input:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: var(--text-muted);
            border-radius: 50%;
            display: inline-block;
            animation: typingBounce 1.4s infinite;
        }

        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        .quick-questions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 0 16px 16px;
        }

        .quick-question-btn {
            padding: 8px 14px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: var(--radius-full);
            color: #93C5FD;
            font-size: 13px;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        body.light-theme .quick-question-btn {
            color: #1D4ED8;
            background: rgba(59, 130, 246, 0.06);
            border-color: rgba(59, 130, 246, 0.2);
        }

        .quick-question-btn:hover {
            background: rgba(59, 130, 246, 0.2);
            border-color: var(--accent-primary);
            transform: translateY(-2px);
        }

        /* Comparison */
        .comparison-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .comparison-panel {
            background: var(--card-bg);
            border-radius: var(--radius-md);
            padding: 16px;
            border: 1px solid var(--card-border);
            backdrop-filter: blur(8px);
        }

        body.light-theme .comparison-panel {
            background: rgba(255, 255, 255, 0.8);
            border-color: rgba(209, 213, 219, 0.4);
        }

        .diff-added {
            background: rgba(16, 185, 129, 0.1);
            border-left: 3px solid var(--success);
            padding: 8px 12px;
            margin: 8px 0;
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
        }

        .diff-removed {
            background: rgba(239, 68, 68, 0.1);
            border-left: 3px solid var(--error);
            padding: 8px 12px;
            margin: 8px 0;
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
            text-decoration: line-through;
            opacity: 0.7;
        }

        .diff-modified {
            background: rgba(245, 158, 11, 0.1);
            border-left: 3px solid var(--warning);
            padding: 8px 12px;
            margin: 8px 0;
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, var(--accent-primary), var(--card-border));
        }

        .timeline-item {
            position: relative;
            padding-bottom: 24px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--accent-primary);
            border: 2px solid var(--bg-primary);
            transition: all var(--transition-fast);
        }

        body.light-theme .timeline-item::before {
            border-color: var(--bg-primary);
        }

        .timeline-item.completed::before { background: var(--success); }
        .timeline-item.warning::before { background: var(--warning); }

        /* Pricing */
        .pricing-card {
            transition: all var(--transition-slow);
            position: relative;
            overflow: hidden;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            border-color: var(--accent-primary);
        }

        .pricing-card.popular {
            border-color: var(--accent-primary);
            box-shadow: 0 0 40px var(--accent-glow);
        }

        .pricing-card.popular::before {
            content: attr(data-popular);
            position: absolute;
            top: 12px;
            right: -30px;
            background: var(--accent-gradient);
            padding: 4px 40px;
            font-size: 12px;
            font-weight: 600;
            transform: rotate(45deg);
            color: white;
        }

        /* Testimonials */
        .testimonial-card {
            background: var(--card-bg);
            backdrop-filter: var(--glass-blur);
            border: 1px solid var(--card-border);
            transition: all var(--transition-normal);
            border-radius: var(--radius-xl);
            padding: 24px;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        /* FAQ */
        .faq-section {
            padding: 80px 20px;
        }

        .faq-item {
            margin-bottom: 12px;
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--card-border);
            background: var(--card-bg);
            backdrop-filter: blur(8px);
            transition: all var(--transition-normal);
        }

        .faq-item:hover {
            border-color: var(--card-hover-border);
        }

        .faq-question {
            width: 100%;
            padding: 18px;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 16px;
            font-weight: 500;
            text-align: left;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background var(--transition-fast);
        }

        .faq-question:hover {
            background: rgba(59, 130, 246, 0.05);
        }

        .arrow {
            transition: transform var(--transition-normal);
            color: var(--accent-primary);
        }

        .faq-item.active .arrow {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            padding: 0 18px;
            font-size: 14px;
            color: var(--text-secondary);
            transition: all var(--transition-normal);
        }

        .faq-item.active .faq-answer {
            max-height: 300px;
            padding: 0 18px 18px;
        }

        /* Theme Toggle */
        .theme-toggle {
            position: fixed;
            top: 100px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 40px var(--accent-glow);
            transition: all var(--transition-normal);
            z-index: 99;
        }

        .theme-toggle:hover {
            transform: scale(1.1) rotate(15deg);
            box-shadow: 0 15px 50px var(--accent-glow);
        }

        .theme-toggle svg {
            width: 24px;
            height: 24px;
            color: white;
            transition: all var(--transition-normal);
        }

        .theme-toggle .sun-icon { display: none; }
        .theme-toggle .moon-icon { display: block; }

        body.light-theme .theme-toggle .sun-icon { display: block; }
        body.light-theme .theme-toggle .moon-icon { display: none; }

        /* FAB */
        .fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 40px var(--accent-glow);
            transition: all var(--transition-normal);
            z-index: 100;
            animation: float 4s ease-in-out infinite;
        }

        .fab:hover {
            transform: scale(1.1);
            box-shadow: 0 15px 50px var(--accent-glow);
        }

        .fab svg { color: white; }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 100px;
            right: 30px;
            padding: 16px 24px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: var(--glass-blur);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-hover);
            transform: translateX(120%);
            transition: transform var(--transition-normal);
            z-index: 1000;
            color: var(--text-primary);
        }

        .toast.show { transform: translateX(0); }
        .toast.success { border-color: var(--success); }
        .toast.error { border-color: var(--error); }
        .toast.warning { border-color: var(--warning); }
        .toast.info { border-color: var(--accent-primary); }

        /* Notification Panel */
        .notification-panel {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 8px;
            width: 320px;
            background: var(--bg-primary);
            border: 1px solid var(--card-border);
            backdrop-filter: var(--glass-blur);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-hover);
            overflow: hidden;
            animation: slideUp 0.3s ease;
        }

        body.light-theme .notification-panel {
            background: rgba(255, 255, 255, 0.95);
        }

        .notification-panel.hidden { display: none; }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.hidden { display: none; }

        .modal-content {
            background: var(--bg-primary);
            border: 1px solid var(--card-border);
            backdrop-filter: var(--glass-blur);
            border-radius: var(--radius-xl);
            max-width: 640px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: scaleIn 0.3s ease;
        }

        body.light-theme .modal-content {
            background: rgba(255, 255, 255, 0.95);
        }

        /* Stats Badge */
        .stat-badge {
            animation: float 4s ease-in-out infinite;
        }

        /* Notification Badge */
        .notification-badge {
            animation: pulse 2s infinite;
        }

        /* Risk Item */
        .risk-item {
            transition: all var(--transition-fast);
            border-radius: var(--radius-md);
        }

        .risk-item:hover {
            background: rgba(59, 130, 246, 0.08);
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--card-border);
            background: var(--bg-secondary);
            backdrop-filter: blur(8px);
        }

        body.light-theme footer {
            background: rgba(249, 250, 251, 0.9);
        }

        /* Section visibility */
        .hidden-section { display: none; }
        .active-section {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        /* Loader */
        .loader {
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid var(--accent-primary);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        body.light-theme .loader {
            border-color: rgba(0, 0, 0, 0.1);
            border-top-color: var(--accent-primary);
        }

        /* Progress bar */
        .progress-bar-fill {
            transition: width 0.3s ease;
            background: var(--accent-gradient);
            border-radius: var(--radius-full);
        }

        /* Accessibility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        *:focus-visible {
            outline: 2px solid var(--accent-primary);
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Hide scrollbar for slider */
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scrollbar::-webkit-scrollbar { display: none; }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: var(--radius-full);
            font-size: 13px;
            font-weight: 500;
        }

        .badge-blue {
            background: rgba(59, 130, 246, 0.15);
            color: #93C5FD;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        body.light-theme .badge-blue {
            background: rgba(59, 130, 246, 0.08);
            color: #1D4ED8;
            border-color: rgba(59, 130, 246, 0.2);
        }

        /* Document type cards */
        .doc-type-card {
            background: var(--card-bg);
            backdrop-filter: blur(8px);
            border: 1px solid var(--card-border);
            transition: all var(--transition-normal);
            border-radius: var(--radius-xl);
        }

        .doc-type-card:hover {
            border-color: var(--accent-primary);
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        /* Example cards */
        .example-card {
            background: var(--card-bg);
            backdrop-filter: blur(8px);
            border: 1px solid var(--card-border);
            transition: all var(--transition-normal);
            border-radius: var(--radius-xl);
        }

        .example-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: var(--accent-primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .comparison-container { grid-template-columns: 1fr; }
            .theme-toggle { top: 80px; right: 15px; width: 44px; height: 44px; }
            .fab { bottom: 20px; right: 20px; width: 52px; height: 52px; }
            .toast { bottom: 80px; right: 15px; left: 15px; }
            .info-section { padding: 2rem 1.5rem; }
        }

        /* Typing animation */
        @keyframes typingBounce {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
            30% { transform: translateY(-10px); opacity: 1; }
        }

        /* Gradient border animation */
        @keyframes borderGlow {
            0%, 100% {
                border-image: linear-gradient(90deg, rgba(59, 130, 246, 0.5), rgba(139, 92, 246, 0.5)) 1;
            }
            50% {
                border-image: linear-gradient(90deg, rgba(139, 92, 246, 0.5), rgba(236, 72, 153, 0.5)) 1;
            }
        }

        /* Smooth transitions for all elements */
        a, button {
            transition: all var(--transition-fast);
        }
    </style>
</head>
<body>
@php
    $welcomeExtra = match (app()->getLocale()) {
        'ru' => [
            'skip_to_content' => 'Перейти к основному контенту',
            'theme_toggle_title' => 'Переключить тему',
            'theme_toggle_aria' => 'Переключить цветовую тему',
            'nav_aria' => 'Основная навигация',
            'notifications_aria' => 'Уведомления',
            'mobile_menu_aria' => 'Меню',
            'command_search' => 'Поиск команд',
            'support_247' => '24/7 поддержка',
            'problems_badge' => 'Знакомо?',
            'problems_title' => 'Каждый плохой договор',
            'problems_title_accent' => 'стоит вам денег и нервов',
            'problems_text' => 'Мы посчитали, во что обходится подписание «не глядя».',
            'without_ai' => 'Без AI',
            'today_day' => 'Ваш сегодняшний день',
            'with_ai' => 'С AI',
            'after_implementation' => 'Ваш день после внедрения',
            'try_same' => 'Хочу так же — попробовать →',
            'about_p3' => 'Наш сервис подходит для проверки любых документов: договоров аренды, трудовых контрактов, договоров поставки, кредитных договоров и многих других. Мы анализируем документы по множеству параметров риска, включая юридические, финансовые и операционные аспекты.',
            'how_title' => 'Как это работает',
            'how_title_accent' => 'работает',
            'how_text' => 'Три простых шага до полного понимания рисков',
            'try_now' => 'Попробуйте сейчас →',
            'faq_badge' => 'Частые вопросы',
            'faq_title' => 'Есть',
            'faq_title_accent' => 'вопросы?',
            'faq_text' => 'Ответы на самые популярные вопросы о нашей платформе',
            'footer_about' => 'Интеллектуальный анализ юридических документов с помощью искусственного интеллекта',
            'footer_product' => 'Продукт',
            'footer_features' => 'Возможности',
            'footer_pricing' => 'Цены',
            'footer_integrations' => 'Интеграции',
            'footer_resources' => 'Ресурсы',
            'footer_docs' => 'Документация',
            'footer_blog' => 'Блог',
            'footer_webinars' => 'Вебинары',
            'footer_status' => 'Статус системы',
            'footer_company' => 'Ширкат',
            'footer_about_link' => 'О нас',
            'footer_careers' => 'Карьера',
            'footer_contacts' => 'Контакты',
            'footer_partners' => 'Партнёры',
            'demo_modal_title' => 'Демо-видео платформы',
            'close' => 'Закрыть',
            'help' => 'Помощь',
            'fab_message' => 'Нужна помощь? Напишите в поддержку!',
            'theme_light' => 'Светлая тема включена',
            'theme_dark' => 'Тёмная тема включена',
            'problems' => [
                ['pain' => 'Подписываете договор «вслепую»', 'detail' => 'Юрист в отпуске, дедлайн горит, контрагент торопит. Подписали — а в п. 5.3 неустойка 0.5%/день.', 'cost' => 'Средний скрытый риск: ₽2.4M'],
                ['pain' => 'Юрист тратит 6-8 часов на один договор', 'detail' => 'Ручная вычитка 30+ страниц, проверка контрагента в 5 базах, поиск судебной практики.', 'cost' => 'Стоимость работы: ₽18 000+ за документ'],
                ['pain' => 'Контрагент оказался однодневкой', 'detail' => 'Узнаёте об этом, когда деньги уже ушли, а в ЕГРЮЛ — отметка о ликвидации.', 'cost' => 'Невозвратные потери: 100% от суммы'],
            ],
            'solutions' => [
                ['win' => 'AI находит 14 из 14 рисков за 90 сек', 'detail' => 'Подсветка кабальных пунктов, штрафов и ловушек прямо в тексте.', 'metric' => 'Точность 98.7%'],
                ['win' => 'Анализ за стоимость чашки кофе', 'detail' => 'Безлимитная проверка договоров на тарифе Pro.', 'metric' => 'ROI до 5 000%'],
                ['win' => 'Контрагент проверен по 47 источникам', 'detail' => 'ФНС, ФССП, арбитраж, санкционные списки.', 'metric' => '0 однодневок'],
            ],
            'steps' => [
                ['number' => '01', 'title' => 'Загрузите договор', 'description' => 'Перетащите файл PDF или DOCX. Максимум 50 МБ.', 'color' => 'from-blue-500 to-indigo-500', 'icon' => '↑', 'details' => ['PDF, DOCX, TXT', 'OCR для сканов', 'До 50 МБ']],
                ['number' => '02', 'title' => 'AI-анализ', 'description' => 'Нейросеть анализирует договор и выявляет риски.', 'color' => 'from-purple-500 to-pink-500', 'icon' => 'AI', 'details' => ['~30 секунд', '99% точность', '500k+ документов']],
                ['number' => '03', 'title' => 'Получите отчёт', 'description' => 'Подробный отчёт с рекомендациями и объяснениями.', 'color' => 'from-emerald-500 to-teal-500', 'icon' => 'DOC', 'details' => ['PDF-отчёт', 'Word экспорт', 'История']],
            ],
            'faq' => [
                ['question' => 'Насколько точен AI-анализ?', 'answer' => 'Наша модель обучена на более чем 500 000 юридических документов и имеет точность выявления рисков 99.2%. Мы постоянно обновляем наши алгоритмы на основе актуального законодательства.'],
                ['question' => 'Какие типы документов поддерживаются?', 'answer' => 'LegalAI Pro работает с договорами купли-продажи, аренды, оказания услуг, NDA, трудовыми договорами, кредитными соглашениями и другими юридическими документами в форматах PDF, DOCX и TXT.'],
                ['question' => 'Безопасно ли загружать документы?', 'answer' => 'Все данные шифруются с использованием AES-256. Документы автоматически удаляются после анализа. Мы не передаём ваши данные третьим лицам и соответствуем требованиям GDPR.'],
                ['question' => 'Как быстро происходит анализ?', 'answer' => 'Среднее время анализа договора — до 60 секунд. Большие документы могут потребовать до 2 минут. Результаты доступны сразу после завершения анализа.'],
                ['question' => 'Можно ли получить помощь юриста?', 'answer' => 'Да. На тарифах Pro и Enterprise вы можете получить консультацию профессионального юриста. AI-анализ дополняется экспертной проверкой.'],
            ],
        ],
        'tg' => [
            'skip_to_content' => 'Ба мундариҷаи асосӣ гузаред',
            'theme_toggle_title' => 'Иваз кардани мавзӯъ',
            'theme_toggle_aria' => 'Иваз кардани мавзӯи ранг',
            'nav_aria' => 'Навигатсияи асосӣ',
            'notifications_aria' => 'Огоҳиномаҳо',
            'mobile_menu_aria' => 'Меню',
            'command_search' => 'Ҷустуҷӯи фармонҳо',
            'support_247' => 'Дастгирии 24/7',
            'problems_badge' => 'Шинос аст?',
            'problems_title' => 'Ҳар як шартномаи бад',
            'problems_title_accent' => 'ба шумо пул ва асаб меорад',
            'problems_text' => 'Мо ҳисоб кардем, ки имзои шартнома бидуни санҷиш чӣ қадар арзиш дорад.',
            'without_ai' => 'Бе AI',
            'today_day' => 'Рӯзи имрӯзаи шумо',
            'with_ai' => 'Бо AI',
            'after_implementation' => 'Рӯзи шумо баъд аз ҷорӣ кардан',
            'try_same' => 'Ман ҳам ҳаминро мехоҳам — санҷидан →',
            'about_p3' => 'Хидмати мо барои санҷиши ҳама гуна ҳуҷҷатҳо мувофиқ аст: шартномаҳои иҷора, шартномаҳои меҳнатӣ, шартномаҳои таҳвил, шартномаҳои қарзӣ ва бисёр дигар ҳуҷҷатҳо. Мо ҳуҷҷатҳоро аз рӯи параметрҳои зиёди хавф, аз ҷумла ҷанбаҳои ҳуқуқӣ, молиявӣ ва амалиётӣ таҳлил мекунем.',
            'how_title' => 'Ин чӣ гуна',
            'how_title_accent' => 'кор мекунад',
            'how_text' => 'Се қадами оддӣ то фаҳмиши пурраи хавфҳо',
            'try_now' => 'Ҳозир санҷед →',
            'faq_badge' => 'Саволҳои маъмул',
            'faq_title' => 'Оё',
            'faq_title_accent' => 'савол доред?',
            'faq_text' => 'Ҷавобҳо ба саволҳои машҳуртарини платформаи мо',
            'footer_about' => 'Таҳлили зеҳнии ҳуҷҷатҳои ҳуқуқӣ бо ёрии зеҳни сунъӣ',
            'footer_product' => 'Маҳсулот',
            'footer_features' => 'Имкониятҳо',
            'footer_pricing' => 'Нархҳо',
            'footer_integrations' => 'Ҳамгироиҳо',
            'footer_resources' => 'Захираҳо',
            'footer_docs' => 'Ҳуҷҷатҳо',
            'footer_blog' => 'Блог',
            'footer_webinars' => 'Вебинарҳо',
            'footer_status' => 'Вазъи система',
            'footer_company' => 'Ширкат',
            'footer_about_link' => 'Дар бораи мо',
            'footer_careers' => 'Карера',
            'footer_contacts' => 'Тамос',
            'footer_partners' => 'Шарикон',
            'demo_modal_title' => 'Видео-намоиши платформа',
            'close' => 'Пӯшидан',
            'help' => 'Кӯмак',
            'fab_message' => 'Кӯмак лозим аст? Ба дастгирӣ нависед!',
            'theme_light' => 'Мавзӯи равшан фаъол шуд',
            'theme_dark' => 'Мавзӯи торик фаъол шуд',
            'problems' => [
                ['pain' => 'Шартномаро «кӯр-кӯрона» имзо мекунед', 'detail' => 'Ҳуқуқшинос дар рухсатӣ аст, муҳлат кам аст, тарафи дигар шитоб медиҳад. Имзо кардед — ва дар банди 5.3 ҷаримаи 0.5% дар як рӯз ҳаст.', 'cost' => 'Хавфи миёнаи пинҳонӣ: ₽2.4M'],
                ['pain' => 'Ҳуқуқшинос барои як шартнома 6-8 соат сарф мекунад', 'detail' => 'Хониши дастии зиёда аз 30 саҳифа, санҷиши тарафи дигар дар 5 пойгоҳ, ҷустуҷӯи таҷрибаи судӣ.', 'cost' => 'Арзиши кор: ₽18 000+ барои ҳуҷҷат'],
                ['pain' => 'Тарафи дигар якрӯза баромад', 'detail' => 'Дар ин бора вақте мефаҳмед, ки пул аллакай рафтааст ва дар феҳрист қайди барҳамдиҳӣ ҳаст.', 'cost' => 'Зарари бебозгашт: 100% аз маблағ'],
            ],
            'solutions' => [
                ['win' => 'AI 14 аз 14 хавфро дар 90 сония меёбад', 'detail' => 'Нишон додани бандҳои вазнин, ҷаримаҳо ва домҳо рост дар матн.', 'metric' => 'Дақиқӣ 98.7%'],
                ['win' => 'Таҳлил бо арзиши як пиёла қаҳва', 'detail' => 'Санҷиши бемаҳдуди шартномаҳо дар тарофи Pro.', 'metric' => 'ROI то 5 000%'],
                ['win' => 'Тарафи дигар аз рӯи 47 манбаъ санҷида шуд', 'detail' => 'Андоз, иҷрочиён, арбитраж, рӯйхатҳои таҳримӣ.', 'metric' => '0 ширкатҳои якрӯза'],
            ],
            'steps' => [
                ['number' => '01', 'title' => 'Шартномаро бор кунед', 'description' => 'Файли PDF ё DOCX-ро кашида биёред. Ҳаҷми ҳадди аксар 50 МБ.', 'color' => 'from-blue-500 to-indigo-500', 'icon' => '↑', 'details' => ['PDF, DOCX, TXT', 'OCR барои сканҳо', 'То 50 МБ']],
                ['number' => '02', 'title' => 'AI-таҳлил', 'description' => 'Шабакаи нейронӣ шартномаро таҳлил карда, хавфҳоро меёбад.', 'color' => 'from-purple-500 to-pink-500', 'icon' => 'AI', 'details' => ['~30 сония', '99% дақиқӣ', '500k+ ҳуҷҷат']],
                ['number' => '03', 'title' => 'Гузориш гиред', 'description' => 'Гузориши муфассал бо тавсияҳо ва шарҳҳо.', 'color' => 'from-emerald-500 to-teal-500', 'icon' => 'DOC', 'details' => ['PDF-гузориш', 'Word содирот', 'Таърихча']],
            ],
            'faq' => [
                ['question' => 'AI-таҳлил то чӣ андоза дақиқ аст?', 'answer' => 'Модели мо дар зиёда аз 500 000 ҳуҷҷати ҳуқуқӣ омӯзонида шудааст ва дақиқии муайянкунии хавф 99.2% мебошад. Мо алгоритмҳоро мунтазам нав мекунем.'],
                ['question' => 'Кадом намудҳои ҳуҷҷат дастгирӣ мешаванд?', 'answer' => 'LegalAI Pro бо шартномаҳои харидуфурӯш, иҷора, хизматрасонӣ, NDA, меҳнатӣ, қарзӣ ва дигар ҳуҷҷатҳои ҳуқуқӣ дар форматҳои PDF, DOCX ва TXT кор мекунад.'],
                ['question' => 'Оё бор кардани ҳуҷҷатҳо бехатар аст?', 'answer' => 'Ҳама маълумот бо AES-256 рамзгузорӣ мешавад. Ҳуҷҷатҳо пас аз таҳлил худкор ҳазф мешаванд. Мо маълумоти шуморо ба шахсони сеюм намедиҳем.'],
                ['question' => 'Таҳлил чӣ қадар зуд анҷом меёбад?', 'answer' => 'Вақти миёнаи таҳлил то 60 сония аст. Ҳуҷҷатҳои калон метавонанд то 2 дақиқа вақт гиранд. Натиҷаҳо фавран дастрас мешаванд.'],
                ['question' => 'Оё машварати ҳуқуқшинос гирифтан мумкин аст?', 'answer' => 'Бале. Дар тарофаҳои Pro ва Enterprise шумо метавонед машварати ҳуқуқшиноси касбиро гиред.'],
            ],
        ],
        default => [
            'skip_to_content' => 'Skip to main content',
            'theme_toggle_title' => 'Toggle theme',
            'theme_toggle_aria' => 'Toggle color theme',
            'nav_aria' => 'Main navigation',
            'notifications_aria' => 'Notifications',
            'mobile_menu_aria' => 'Menu',
            'command_search' => 'Command search',
            'support_247' => '24/7 support',
            'problems_badge' => 'Recognizable?',
            'problems_title' => 'Every bad contract',
            'problems_title_accent' => 'costs you money and stress',
            'problems_text' => 'We calculated the cost of signing without reading carefully.',
            'without_ai' => 'Without AI',
            'today_day' => 'Your day today',
            'with_ai' => 'With AI',
            'after_implementation' => 'Your day after rollout',
            'try_same' => 'I want that too →',
            'about_p3' => 'Our service is suitable for reviewing all kinds of documents: rental agreements, employment contracts, supply agreements, credit agreements, and many others. We analyze documents across legal, financial, and operational risk dimensions.',
            'how_title' => 'How it',
            'how_title_accent' => 'works',
            'how_text' => 'Three simple steps to fully understand contract risk',
            'try_now' => 'Try it now →',
            'faq_badge' => 'Frequently asked questions',
            'faq_title' => 'Have',
            'faq_title_accent' => 'questions?',
            'faq_text' => 'Answers to the most common questions about our platform',
            'footer_about' => 'Intelligent analysis of legal documents powered by artificial intelligence',
            'footer_product' => 'Product',
            'footer_features' => 'Features',
            'footer_pricing' => 'Pricing',
            'footer_integrations' => 'Integrations',
            'footer_resources' => 'Resources',
            'footer_docs' => 'Documentation',
            'footer_blog' => 'Blog',
            'footer_webinars' => 'Webinars',
            'footer_status' => 'System status',
            'footer_company' => 'Company',
            'footer_about_link' => 'About',
            'footer_careers' => 'Careers',
            'footer_contacts' => 'Contacts',
            'footer_partners' => 'Partners',
            'demo_modal_title' => 'Platform demo video',
            'close' => 'Close',
            'help' => 'Help',
            'fab_message' => 'Need help? Contact support!',
            'theme_light' => 'Light theme enabled',
            'theme_dark' => 'Dark theme enabled',
            'problems' => [
                ['pain' => 'You sign contracts blindly', 'detail' => 'The lawyer is on vacation, the deadline is burning, the counterparty is rushing. You signed it, and clause 5.3 contains a 0.5% daily penalty.', 'cost' => 'Average hidden risk: ₽2.4M'],
                ['pain' => 'A lawyer spends 6-8 hours per contract', 'detail' => 'Manual review of 30+ pages, counterparty checks across 5 databases, and case-law research.', 'cost' => 'Work cost: ₽18,000+ per document'],
                ['pain' => 'The counterparty turned out to be a shell company', 'detail' => 'You find out after the money is gone and the registry already shows liquidation.', 'cost' => 'Irrecoverable losses: 100% of the amount'],
            ],
            'solutions' => [
                ['win' => 'AI finds 14 of 14 risks in 90 seconds', 'detail' => 'Unfair clauses, penalties, and traps are highlighted directly in the text.', 'metric' => 'Accuracy 98.7%'],
                ['win' => 'Analysis for the price of a cup of coffee', 'detail' => 'Unlimited contract checks on the Pro plan.', 'metric' => 'ROI up to 5,000%'],
                ['win' => 'Counterparty checked across 47 sources', 'detail' => 'Tax authority, enforcement, arbitration, and sanctions lists.', 'metric' => '0 shell companies'],
            ],
            'steps' => [
                ['number' => '01', 'title' => 'Upload a contract', 'description' => 'Drag in a PDF or DOCX file. Maximum 50 MB.', 'color' => 'from-blue-500 to-indigo-500', 'icon' => '↑', 'details' => ['PDF, DOCX, TXT', 'OCR for scans', 'Up to 50 MB']],
                ['number' => '02', 'title' => 'AI analysis', 'description' => 'The neural model analyzes the contract and identifies risks.', 'color' => 'from-purple-500 to-pink-500', 'icon' => 'AI', 'details' => ['~30 seconds', '99% accuracy', '500k+ documents']],
                ['number' => '03', 'title' => 'Get the report', 'description' => 'Receive a detailed report with recommendations and explanations.', 'color' => 'from-emerald-500 to-teal-500', 'icon' => 'DOC', 'details' => ['PDF report', 'Word export', 'History']],
            ],
            'faq' => [
                ['question' => 'How accurate is the AI analysis?', 'answer' => 'Our model is trained on more than 500,000 legal documents and reaches 99.2% risk-detection accuracy. We continuously update it to reflect current legislation.'],
                ['question' => 'What document types are supported?', 'answer' => 'LegalAI Pro supports sales, rental, services, NDA, employment, credit, and other legal documents in PDF, DOCX and TXT formats.'],
                ['question' => 'Is it safe to upload documents?', 'answer' => 'All data is encrypted with AES-256. Documents are automatically deleted after analysis. We do not share your data with third parties.'],
                ['question' => 'How fast is the analysis?', 'answer' => 'Average analysis time is up to 60 seconds. Large documents may take up to 2 minutes. Results are available immediately after processing.'],
                ['question' => 'Can I get help from a lawyer?', 'answer' => 'Yes. Pro and Enterprise plans include access to professional legal consultation.'],
            ],
        ],
    };
@endphp
    <!-- Background elements -->
<div class="bg-grid"></div>
<div class="particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>

<!-- Skip Link for Accessibility -->
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded-lg z-[10001]">
    {{ __('ui.skip_to_content') }}
</a>

<!-- Theme Toggle -->
<div class="theme-toggle" onclick="toggleTheme()" title="{{ __('ui.theme_toggle_title') }}" role="switch" aria-label="{{ __('ui.theme_toggle_aria') }}">
    <svg class="sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    <svg class="moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
    </svg>
</div>

<!-- Navigation -->
<nav role="navigation" aria-label="{{ __('ui.nav_aria') }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center space-x-3 cursor-pointer" onclick="showSection('landing')">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-xl font-bold glow-text">LegalAI Pro</span>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('welcome') }}" class="nav-link">{{ __('ui.nav_home') }}</a>
                <a href="{{route('tasks.create')}}" class="nav-link">{{ __('ui.nav_features') }}</a>
                <a href="{{route('tasks.calc')}}" class="nav-link">{{ __('ui.nav_calculator') }}</a>
                <a href="{{route('tasks.chat')}}" class="nav-link">{{ __('ui.nav_ai_chat') }}</a>
                <x-language-switcher />


                <!-- Notifications -->
                <div class="relative">
                    <button onclick="toggleNotifications()" class="text-slate-400 hover:text-white transition relative p-2" aria-label="{{ __('ui.notifications_aria') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="notification-badge absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full text-[10px] flex items-center justify-center text-white">3</span>
                    </button>
                    <div id="notificationPanel" class="notification-panel hidden">
                        <div class="p-4 border-b border-slate-700">
                            <h4 class="font-semibold text-white">{{ __('ui.notifications') }}</h4>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="p-4 border-b border-slate-700 hover:bg-slate-800/50 cursor-pointer transition">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm text-white">{{ __('ui.notification_sample_1') }}</p>
                                        <p class="text-xs text-slate-400 mt-1">{{ __('ui.notification_sample_time_1') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-b border-slate-700 hover:bg-slate-800/50 cursor-pointer transition">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm text-white">{{ __('ui.notification_sample_2') }}</p>
                                        <p class="text-xs text-slate-400 mt-1">{{ __('ui.notification_sample_time_2') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-slate-800/50 cursor-pointer transition">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm text-white">{{ __('ui.notification_sample_3') }}</p>
                                        <p class="text-xs text-slate-400 mt-1">{{ __('ui.notification_sample_time_3') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{route('login')}}" class="btn-primary px-6 py-2 rounded-lg font-medium">
                    {{ __('ui.nav_login') }}
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button onclick="toggleMobileMenu()" class="md:hidden text-slate-400 hover:text-white p-2" aria-label="{{ __('ui.mobile_menu_aria') }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden border-t border-slate-700 bg-slate-900/95 backdrop-blur-lg">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ route('welcome') }}" class="block py-2 text-slate-300 hover:text-white transition">{{ __('ui.nav_home') }}</a>
            <a href="{{route('tasks.create')}}" class="block py-2 text-slate-300 hover:text-white transition">{{ __('ui.nav_features') }}</a>
            <a href="{{route('tasks.calc')}}" class="block py-2 text-slate-300 hover:text-white transition">{{ __('ui.nav_calculator') }}</a>
            <a href="{{route('tasks.chat')}}" class="block py-2 text-slate-300 hover:text-white transition">{{ __('ui.nav_ai_chat') }}</a>
            <button onclick="openCommandPalette()" class="w-full text-left py-2 text-slate-300 hover:text-white transition">🔍 {{ __('ui.command_search') }}</button>
            <a href="{{route('login')}}" class="block btn-primary text-center px-6 py-2 rounded-lg font-medium">{{ __('ui.nav_login') }}</a>
        </div>
    </div>
</nav>
<!-- Main Content -->
<main id="main-content">
    <!-- LANDING SECTION -->
    <div id="landingSection" class="active-section">
        <!-- Hero -->
        <section class="hero-bg pt-32 pb-20 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="slide-in reveal">
                        <div class="badge badge-blue mb-6">
                            <span class="w-2 h-2 bg-green-500 rounded-full pulse-animation"></span>
                            {{ __('ui.welcome_badge_accuracy') }}
                        </div>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                            {{ __('ui.welcome_hero_title_1') }}
                            <span class="text-gradient">{{ __('ui.welcome_hero_title_2') }}</span>
                        </h1>
                        <p class="text-lg text-slate-400 mb-8 leading-relaxed">
                            {{ __('ui.welcome_hero_text') }}
                        </p>
                        <div class="flex flex-wrap gap-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-primary px-8 py-4 rounded-xl font-semibold text-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    {{ __('ui.welcome_upload_contract') }}
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="btn-primary px-8 py-4 rounded-xl font-semibold text-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    {{ __('ui.welcome_register_upload') }}
                                </a>
                            @endauth
                                <a href="https://www.contracts-ai-app.com/demo"
                                   target="_blank"
                                   class="btn-secondary px-8 py-4 rounded-xl font-semibold text-lg flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0110 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ __('ui.welcome_demo_video') }}
                                </a>                        </div>
                        <div class="mt-8 flex items-center space-x-6 text-sm text-slate-400">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>{{ __('ui.welcome_aes') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>{{ __('ui.welcome_law') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>24/7 поддержка</span>
                            </div>
                        </div>
                    </div>
                    <div class="relative reveal reveal-delay-2">
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600/30 to-purple-600/30 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-500 opacity-50"></div>
                            <img src="https://image.qwenlm.ai/public_source/48f99b9f-6493-4401-a338-927a8c873e1a/1269011f0-818d-4497-9ee0-8c8efb1a3788.png" alt="Legal AI Analysis" class="relative rounded-2xl shadow-2xl border border-slate-700 transform transition duration-500 group-hover:scale-[1.02]">
                        </div>
                        <div class="absolute -bottom-4 -left-4 card-gradient rounded-xl p-4 hidden md:block stat-badge">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-green-400">60 сек</div>
                                    <div class="text-xs text-slate-400">{{ __('ui.stat.avg_analysis_time') }}</div>                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-4 -right-4 card-gradient rounded-xl p-4 hidden md:block stat-badge" style="animation-delay: 1s;">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-blue-400">95%</div>
                                    <div class="text-xs text-slate-400">{{ __('ui.stat.accuracy_label') }}</div>                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why LegalAI -->
        <section class="px-4 mb-16 reveal">
            <div class="max-w-7xl mx-auto info-section text-center">
                <h3 class="text-2xl md:text-3xl font-bold text-white mb-6">{{ __('ui.welcome_why_title') }}</h3>
                <p class="text-lg text-slate-400 leading-relaxed max-w-4xl mx-auto">
                    {{ __('ui.welcome_why_text') }}
                </p>
            </div>
        </section>
        @php
            $problems = [
                [
                    "pain" => "Подписываете договор «вслепую»",
                    "detail" => "Юрист в отпуске, дедлайн горит, контрагент торопит. Подписали — а в п. 5.3 неустойка 0.5%/день.",
                    "cost" => "Средний скрытый риск: ₽2.4M",
                ],
                [
                    "pain" => "Юрист тратит 6–8 часов на один договор",
                    "detail" => "Ручная вычитка 30+ страниц, проверка контрагента в 5 базах, поиск судебной практики.",
                    "cost" => "Стоимость работы: ₽18 000+ за документ",
                ],
                [
                    "pain" => "Контрагент оказался однодневкой",
                    "detail" => "Узнаёте об этом, когда деньги уже ушли, а в ЕГРЮЛ — отметка о ликвидации.",
                    "cost" => "Невозвратные потери: 100% от суммы",
                ],
            ];

            $solutions = [
                [
                    "win" => "AI находит 14 из 14 рисков за 90 сек",
                    "detail" => "Подсветка кабальных пунктов, штрафов и ловушек прямо в тексте.",
                    "metric" => "Точность 98.7%",
                ],
                [
                    "win" => "Анализ за стоимость чашки кофе",
                    "detail" => "Безлимитная проверка договоров на тарифе Pro.",
                    "metric" => "ROI до 5 000%",
                ],
                [
                    "win" => "Контрагент проверен по 47 источникам",
                    "detail" => "ФНС, ФССП, арбитраж, санкционные списки.",
                    "metric" => "0 однодневок",
                ],
            ];
        @endphp

        <section class="relative py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">

                <!-- HEADER -->
                <div class="mx-auto max-w-3xl text-center">
    <span class="inline-flex items-center gap-2 rounded-full border border-rose-500/20 bg-rose-500/10 px-3 py-1 text-xs font-medium text-rose-300">
        <span class="h-1.5 w-1.5 rounded-full bg-rose-400 animate-pulse"></span>
        {{ __('ui.hero.badge') }}
    </span>

                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-white sm:text-5xl">
                        {{ __('ui.hero.heading.line1') }}<br>
                        {{ __('ui.hero.heading.line2') }}<span class="text-3xl md:text-5xl font-bold text-gradient mb-5">{{ __('ui.hero.heading.highlight') }}</span>
                    </h2>

                    <p class="mt-4 text-lg text-slate-400">
                        {{ __('ui.hero.subtitle') }}
                    </p>
                </div>
                <div class="mt-14 grid gap-6 lg:grid-cols-2">

                    <!-- PROBLEMS -->
                    <div class="relative h-full rounded-3xl border border-rose-500/20 bg-gradient-to-b from-rose-500/[0.07] to-transparent p-8">
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-rose-500/40 to-transparent"></div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/15 text-rose-400">
                                ⚠️
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-widest text-rose-400">{{ __('ui.card.without_ai.label') }}</div>
                                <div class="text-lg font-semibold text-white">{{ __('ui.card.without_ai.title') }}</div>
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            @php
                                $problems = [
                                    ['pain' => __('ui.problems.p1_pain'), 'detail' => __('ui.problems.p1_detail'), 'cost' => __('ui.problems.p1_cost')],
                                    ['pain' => __('ui.problems.p2_pain'), 'detail' => __('ui.problems.p2_detail'), 'cost' => __('ui.problems.p2_cost')],
                                    ['pain' => __('ui.problems.p3_pain'), 'detail' => __('ui.problems.p3_detail'), 'cost' => __('ui.problems.p3_cost')],
                                ];
                            @endphp

                            <div class="mt-6 space-y-4">
                                @foreach($problems as $p)
                                    <div class="rounded-2xl border border-rose-500/10 bg-rose-500/[0.04] p-4 hover:border-rose-500/30 transition">
                                        <div class="flex gap-3">
                                            <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-rose-500/20 text-rose-300 text-sm">✖</span>
                                            <div>
                                                <div class="font-semibold text-white">{{ $p['pain'] }}</div>
                                                <p class="mt-1 text-sm text-slate-400">{{ $p['detail'] }}</p>
                                                <div class="mt-2 inline-block rounded-md bg-rose-500/15 px-2 py-1 text-xs text-rose-300">{{ $p['cost'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>                        </div>
                    </div>
                    <!-- SOLUTIONS -->
                        <div class="relative h-full rounded-3xl border border-emerald-500/30 bg-gradient-to-b from-emerald-500/[0.08] to-transparent p-8 shadow-2xl">

                            <div class="absolute -right-4 -top-4 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 px-3 py-1 text-xs text-white">
                                ✦ {{ __('ui.with_ai_label') }}
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-white">
                                    ✔
                                </div>
                                <div>
                                    <div class="text-xs uppercase text-emerald-400">{{ __('ui.card.with_ai.label') }}</div>
                                    <div class="text-lg font-semibold text-white">{{ __('ui.card.with_ai.title') }}</div>
                                </div>
                            </div>

                            <div class="mt-6 space-y-4">
                                @php
                                    $solutions = [
                                        ['win' => __('ui.solutions.s1_win'), 'detail' => __('ui.solutions.s1_detail'), 'metric' => __('ui.solutions.s1_metric')],
                                        ['win' => __('ui.solutions.s2_win'), 'detail' => __('ui.solutions.s2_detail'), 'metric' => __('ui.solutions.s2_metric')],
                                        ['win' => __('ui.solutions.s3_win'), 'detail' => __('ui.solutions.s3_detail'), 'metric' => __('ui.solutions.s3_metric')],
                                    ];
                                @endphp

                                <div class="space-y-4">
                                    @foreach($solutions as $s)
                                        <div class="rounded-2xl border border-emerald-500/15 bg-emerald-500/[0.05] p-4 hover:border-emerald-500/40 transition">
                                            <div class="flex gap-3">
                    <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white text-sm">
                        ✔
                    </span>
                                                <div>
                                                    <div class="font-semibold text-white">{{ $s['win'] }}</div>
                                                    <p class="mt-1 text-sm text-slate-400">{{ $s['detail'] }}</p>
                                                    <div class="mt-2 inline-block rounded-md bg-emerald-500/15 px-2 py-1 text-xs text-emerald-300">
                                                        {{ $s['metric'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>                        </div>

                            <a href="#demo"
                               class="mt-6 flex justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:opacity-90 transition">
                                {{ __('ui.try_same') }}
                            </a>
                        </div>

                </div>
            </div>
        </section>
        <!-- Document Types Slider -->
        <section class="px-4 mb-20 reveal">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4 text-white">{{ __('ui.welcome_docs_title') }}</h2>
                    <p class="text-slate-400 text-lg">{{ __('ui.welcome_docs_footer') }}</p>
                </div>
                <div class="relative group">
                    <div id="sliderTrack" class="flex space-x-6 overflow-x-auto hide-scrollbar py-4 px-2 scroll-smooth">
                        <div class="flex-shrink-0 w-64 doc-type-card rounded-2xl p-6 flex flex-col items-center justify-center transition-all duration-300 cursor-pointer">
                            <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mb-4 text-blue-400">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <h3 class="font-semibold text-white text-center">{{ __('ui.welcome_doc_rent') }}</h3>
                        </div>
                        <div class="flex-shrink-0 w-64 doc-type-card rounded-2xl p-6 flex flex-col items-center justify-center transition-all duration-300 cursor-pointer">
                            <div class="w-16 h-16 bg-amber-500/20 rounded-full flex items-center justify-center mb-4 text-amber-400">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <h3 class="font-semibold text-white text-center">{{ __('ui.welcome_doc_employment') }}</h3>
                        </div>
                        <div class="flex-shrink-0 w-64 doc-type-card rounded-2xl p-6 flex flex-col items-center justify-center transition-all duration-300 cursor-pointer">
                            <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mb-4 text-green-400">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                            <h3 class="font-semibold text-white text-center">{{ __('ui.welcome_doc_sale') }}</h3>
                        </div>
                        <div class="flex-shrink-0 w-64 doc-type-card rounded-2xl p-6 flex flex-col items-center justify-center transition-all duration-300 cursor-pointer">
                            <div class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mb-4 text-purple-400">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>
                            </div>
                            <h3 class="font-semibold text-white text-center">{{ __('ui.welcome_doc_legal') }}</h3>
                        </div>
                        <div class="flex-shrink-0 w-64 doc-type-card rounded-2xl p-6 flex flex-col items-center justify-center transition-all duration-300 cursor-pointer">
                            <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mb-4 text-red-400">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="font-semibold text-white text-center">{{ __('ui.welcome_doc_insurance') }}</h3>
                        </div>
                        <div class="flex-shrink-0 w-64 doc-type-card rounded-2xl p-6 flex flex-col items-center justify-center transition-all duration-300 cursor-pointer">
                            <div class="w-16 h-16 bg-cyan-500/20 rounded-full flex items-center justify-center mb-4 text-cyan-400">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            </div>
                            <h3 class="font-semibold text-white text-center">{{ __('ui.nda_confidential') }}</h3>
                        </div>
                    </div>
                    <button onclick="scrollSlider(-1)" class="absolute left-2 top-1/2 -translate-y-1/2 card-gradient shadow-lg rounded-full p-3 text-blue-400 hover:bg-slate-700 transition-colors z-10 hidden md:block">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <button onclick="scrollSlider(1)" class="absolute right-2 top-1/2 -translate-y-1/2 card-gradient shadow-lg rounded-full p-3 text-blue-400 hover:bg-slate-700 transition-colors z-10 hidden md:block">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Usage Examples -->
        <section class="px-4 mb-20 reveal">
            <div class="max-w-7xl mx-auto">
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gradient mb-4">{{ __('ui.welcome_examples_title') }}</h2>
                    <p class="text-lg text-slate-400">{{ __('ui.welcome_examples_text') }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="example-card rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="group relative w-12 h-12 flex-shrink-0 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500
            flex items-center justify-center text-white shadow-lg shadow-purple-500/25
            transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/40 hover:scale-105
            focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white mb-3">{{ __('ui.welcome_example_rent_title') }}</h3>
                                <p class="text-slate-400 leading-relaxed text-sm">{{ __('ui.welcome_example_rent_text') }}</p>
                            </div>
                        </div>
                        <div class="mt-4 rounded-xl overflow-hidden">
                            <img src="https://image.qwenlm.ai/public_source/0cf6c008-6d37-4310-8806-4d9a4b8b3e72/1d6b56be1-cfd0-45bc-8946-814c31f279af.png" alt="Аренда квартиры" class="w-full h-48 object-cover">
                        </div>
                    </div>
                    <div class="example-card rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="group relative w-12 h-12 flex-shrink-0 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500
            flex items-center justify-center text-white shadow-lg shadow-purple-500/25
            transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/40 hover:scale-105
            focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white mb-3">{{ __('ui.welcome_example_employment_title') }}</h3>
                                <p class="text-slate-400 leading-relaxed text-sm">{{ __('ui.welcome_example_employment_text') }}</p>
                            </div>
                        </div>
                        <div class="mt-4 rounded-xl overflow-hidden">
                            <img src="https://image.qwenlm.ai/public_source/0cf6c008-6d37-4310-8806-4d9a4b8b3e72/1d9d738e9-e30c-4a3a-8915-2d4213afb29f.png" alt="Трудовой договор" class="w-full h-48 object-cover">
                        </div>
                    </div>
                    <div class="example-card rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="group relative w-12 h-12 flex-shrink-0 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500
            flex items-center justify-center text-white shadow-lg shadow-purple-500/25
            transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/40 hover:scale-105
            focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white mb-3">{{ __('ui.welcome_example_credit_title') }}</h3>
                                <p class="text-slate-400 leading-relaxed text-sm">{{ __('ui.welcome_example_credit_text') }}</p>
                            </div>
                        </div>
                        <div class="mt-4 rounded-xl overflow-hidden">
                            <img src="https://image.qwenlm.ai/public_source/2159c776-9dd0-47ff-bc1c-de8d46f17227/1250f1755-7a3f-4e53-8b86-a34f0fe393bd.png" alt="Кредитный договор" class="w-full h-48 object-cover">
                        </div>
                    </div>
                    <div class="example-card rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="group relative w-12 h-12 flex-shrink-0 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500
            flex items-center justify-center text-white shadow-lg shadow-purple-500/25
            transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/40 hover:scale-105
            focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white mb-3">{{ __('ui.welcome_example_supply_title') }}</h3>
                                <p class="text-slate-400 leading-relaxed text-sm">{{ __('ui.welcome_example_supply_text') }}</p>
                            </div>
                        </div>
                        <div class="mt-4 rounded-xl overflow-hidden">
                            <img src="https://image.qwenlm.ai/public_source/0cf6c008-6d37-4310-8806-4d9a4b8b3e72/19155704f-cef9-430f-bd4a-f463c6cbaa73.png" alt="Договор поставки" class="w-full h-48 object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="px-4 mb-20 reveal">
            <div class="max-w-4xl mx-auto info-section">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-gradient mb-8">
                    {{ __('ui.welcome_about_title') }}
                </h2>
                <div class="space-y-6 text-slate-300 text-lg leading-relaxed text-justify">
                    <p><strong class="text-white">LegalAI Pro</strong> — {{ __('ui.welcome_about_p1') }}</p>
                    <p>{{ __('ui.welcome_about_p2') }}</p>
                    <p>{{ __('ui.about_p3') }}</p>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        @php
            $steps = [
                [
                    "number" => "01",
                    "title" => "Загрузите договор",
                    "description" => "Перетащите файл PDF или DOCX. Максимум 50 МБ.",
                    "color" => "from-blue-500 to-indigo-500",
                    "details" => ["PDF, DOCX, TXT", "OCR для сканов", "До 50 МБ"]
                ],
                [
                    "number" => "02",
                    "title" => "AI-анализ",
                    "description" => "Нейросеть анализирует договор и выявляет риски.",
                    "color" => "from-purple-500 to-pink-500",
                    "details" => ["~30 секунд", "99% точность", "500k+ документов"]
                ],
                [
                    "number" => "03",
                    "title" => "Получите отчёт",
                    "description" => "Подробный отчёт с рекомендациями и объяснениями.",
                    "color" => "from-emerald-500 to-teal-500",
                    "details" => ["PDF-отчёт", "Word экспорт", "История"]
                ]
            ];
        @endphp

        <section id="how-it-works" class="py-24 relative overflow-hidden">

            <!-- Background -->
            <div class="absolute inset-0">
                <div class="w-[500px] h-[500px] bg-blue-500 opacity-10 blur-3xl rounded-full absolute -top-64 -right-64"></div>
                <div class="w-[400px] h-[400px] bg-purple-500 opacity-10 blur-3xl rounded-full absolute -bottom-32 -left-32"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="text-center mb-20">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6">
                        {{ __('ui.how_title') }} <span class="text-3xl md:text-5xl font-bold text-gradient mb-5">{{ __('ui.how_title_accent') }}</span>
                    </h2>
                    <p class="text-lg text-gray-400 max-w-2xl mx-auto">
                        {{ __('ui.how_text') }}
                    </p>
                </div>

                <!-- Steps -->
                @php
                    // ⚠️ Определяем массив ТОЛЬКО ОДИН раз, ДО цикла
                    $steps = [
                        [
                            'number'  => '01',
                            'icon'    => '📤',
                            'color'   => 'from-blue-500 to-indigo-600',
                            'title'   => __('ui.steps.1.title'),
                            'desc'    => __('ui.steps.1.desc'),
                            'details' => [__('ui.steps.1.d1'), __('ui.steps.1.d2')],
                        ],
                        [
                            'number'  => '02',
                            'icon'    => '🧠',
                            'color'   => 'from-purple-500 to-pink-600',
                            'title'   => __('ui.steps.2.title'),
                            'desc'    => __('ui.steps.2.desc'),
                            'details' => [__('ui.steps.2.d1'), __('ui.steps.2.d2')],
                        ],
                        [
                            'number'  => '03',
                            'icon'    => '📄',
                            'color'   => 'from-emerald-500 to-teal-600',
                            'title'   => __('ui.steps.3.title'),
                            'desc'    => __('ui.steps.3.desc'),
                            'details' => [__('ui.steps.3.d1'), __('ui.steps.3.d2')],
                        ],
                    ];
                @endphp

                <div class="relative">
                    <!-- Line desktop -->
                    <div class="hidden lg:block absolute top-24 left-[16%] right-[16%] h-0.5 bg-gradient-to-r from-blue-500 via-purple-500 to-emerald-500 opacity-40"></div>

                    <div class="grid lg:grid-cols-3 gap-12 lg:gap-8">
                        @foreach($steps as $index => $step)
                            <div class="relative group">

                                <!-- Number -->
                                <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-10">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-black font-bold text-sm shadow-lg shadow-indigo-500/30 ring-4 ring-indigo-500/10 transition-all duration-300 hover:scale-110 hover:shadow-indigo-500/50">
                                        {{ $step['number'] }}
                                    </div>
                                </div>

                                <!-- Card - ОДНА карточка на шаг -->
                                <div class="group relative h-full rounded-3xl border border-white/10 bg-white/5 p-8 pt-14 text-center backdrop-blur-lg transition-all duration-300 hover:border-indigo-500/30 hover:bg-white/[0.07] hover:shadow-xl hover:shadow-indigo-500/10 hover:-translate-y-1">

                                    <!-- Icon -->
                                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br {{ $step['color'] }} text-3xl text-white shadow-xl shadow-black/20 transition-transform duration-300 group-hover:scale-110">
                                        {{ $step['icon'] }}
                                    </div>

                                    <!-- Content -->
                                    <h3 class="mb-3 text-xl font-semibold text-white">{{ $step['title'] }}</h3>
                                    <p class="mb-6 text-sm leading-relaxed text-slate-400">{{ $step['desc'] }}</p>

                                    <!-- Details -->
                                    <div class="space-y-2.5">
                                        @foreach($step['details'] as $detail)
                                            <div class="flex items-center justify-center gap-2 text-xs font-medium text-slate-300">
                                                <svg class="h-4 w-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span>{{ $detail }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Arrow mobile -->
                                @if($index < count($steps) - 1)
                                    <div class="lg:hidden flex justify-center py-6 text-gray-500">
                                        ↓
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- CTA -->
                <div class="text-center mt-16">
                    <a href="#demo"
                       class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-2xl font-semibold shadow-lg hover:opacity-90 transition">
                        Попробуйте сейчас →
                    </a>
                </div>

            </div>
        </section>
        <!-- FAQ -->
        <section id="faq" class="faq-section reveal">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-12">
                    <div class="badge badge-blue mb-4">{{ __('ui.faq.badge') }}</div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">
                        {{ __('ui.faq.title.prefix') }}<span class="text-gradient">{{ __('ui.faq.title.highlight') }}</span>
                    </h2>
                    <p class="text-slate-400">{{ __('ui.faq.subtitle') }}</p>
                </div>

                <div class="faq-list">
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            {{ __('ui.faq.q1') }}
                            <svg class="arrow w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="faq-answer">{{ __('ui.faq.a1') }}</div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            {{ __('ui.faq.q2') }}
                            <svg class="arrow w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="faq-answer">{{ __('ui.faq.a2') }}</div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            {{ __('ui.faq.q3') }}
                            <svg class="arrow w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="faq-answer">{{ __('ui.faq.a3') }}</div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            {{ __('ui.faq.q4') }}
                            <svg class="arrow w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="faq-answer">{{ __('ui.faq.a4') }}</div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" aria-expanded="false">
                            {{ __('ui.faq.q5') }}
                            <svg class="arrow w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="faq-answer">{{ __('ui.faq.a5') }}</div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Notice -->
        <section class="px-4 py-16 reveal">
            <div class="max-w-4xl mx-auto text-center">
                <div class="card-gradient rounded-2xl p-8 border border-orange-500/30">
                    <div class="flex items-center justify-center space-x-3 mb-4">
                        <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-orange-400">{{ __('ui.welcome_notice_title') }}</h3>
                    </div>
                    <p class="text-slate-300 leading-relaxed">{{ __('ui.welcome_notice_text') }}</p>
                </div>
            </div>
        </section>
    </div>

    <!-- DASHBOARD SECTION -->
    <div id="dashboardSection" class="hidden-section pt-24 pb-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar -->
                <div class="w-full lg:w-64 flex-shrink-0">
                    <div class="card-gradient rounded-xl p-4 border border-slate-700 sticky top-24">
                        <div class="space-y-1">
                            <button onclick="switchDashboardTab('upload')" class="sidebar-item active w-full text-left px-4 py-3 rounded-lg text-slate-300 hover:text-white flex items-center space-x-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span>Загрузка</span>
                            </button>
                            <button onclick="switchDashboardTab('analysis')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg text-slate-300 hover:text-white flex items-center space-x-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Аналитика</span>
                            </button>
                            <button onclick="switchDashboardTab('chat')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg text-slate-300 hover:text-white flex items-center space-x-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                <span>AI-ассистент</span>
                            </button>
                            <button onclick="switchDashboardTab('compare')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg text-slate-300 hover:text-white flex items-center space-x-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span>Сравнение</span>
                            </button>
                            <button onclick="switchDashboardTab('history')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg text-slate-300 hover:text-white flex items-center space-x-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>История</span>
                            </button>
                            <button onclick="switchDashboardTab('settings')" class="sidebar-item w-full text-left px-4 py-3 rounded-lg text-slate-300 hover:text-white flex items-center space-x-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Настройки</span>
                            </button>
                        </div>
                        <div class="mt-6 pt-6 border-t border-slate-700">
                            <button onclick="openModal('createTemplateModal')" class="w-full btn-primary py-3 rounded-lg font-medium flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Новый шаблон</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1 min-w-0">
                    <!-- Upload Tab -->
                    <div id="uploadTab" class="dashboard-tab active-section">
                        <div class="card-gradient rounded-2xl p-8 border border-slate-700">
                            <h2 class="text-2xl font-bold mb-6">Загрузка документа</h2>
                            <div id="dropZone" class="upload-zone rounded-2xl p-12 text-center bg-slate-800/50">
                                <input type="file" id="fileInput" class="hidden" accept=".pdf,.docx,.txt">
                                <svg class="w-16 h-16 text-slate-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <h3 class="text-xl font-semibold mb-2">Перетащите файл сюда</h3>
                                <p class="text-slate-400 mb-6">или нажмите для выбора файла (PDF, DOCX, TXT)</p>
                                <button class="btn-primary px-6 py-3 rounded-lg font-medium">Выбрать файл</button>
                            </div>
                            <div id="fileInfo" class="hidden mt-6 card-gradient rounded-xl p-4 border border-slate-700 flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold" id="fileName">document.pdf</div>
                                        <div class="text-sm text-slate-400" id="fileSize">2.4 MB</div>
                                    </div>
                                </div>
                                <button onclick="removeFile()" class="text-slate-400 hover:text-red-400 transition p-2" aria-label="Удалить файл">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button id="analyzeBtn" onclick="startAnalysis()" disabled class="btn-primary px-8 py-3 rounded-lg font-medium opacity-50 cursor-not-allowed flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <span>Начать анализ</span>
                                </button>
                            </div>
                        </div>

                        <!-- Analysis Progress -->
                        <div id="analysisProgress" class="hidden mt-8 card-gradient rounded-2xl p-8 border border-slate-700">
                            <h3 class="text-xl font-bold mb-6">Процесс анализа</h3>
                            <div class="space-y-6">
                                <div class="flex items-center space-x-4">
                                    <div id="step1" class="w-10 h-10 rounded-full border-2 border-slate-600 flex items-center justify-center transition-all duration-300">
                                        <svg class="w-5 h-5 text-transparent transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-2 bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full progress-bar-fill transition-all duration-300" style="width: 0%" id="progressBar"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm text-slate-400 w-12 text-right" id="progressPercent">0%</span>
                                </div>
                                <div class="grid grid-cols-3 gap-4 text-center text-sm text-slate-400">
                                    <div>Распознавание текста</div>
                                    <div>Поиск рисков</div>
                                    <div>Генерация отчёта</div>
                                </div>
                            </div>
                        </div>

                        <!-- Analysis Skeleton -->
                        <div id="analysisSkeleton" class="hidden mt-8 space-y-6">
                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="card-gradient rounded-xl p-6 border border-slate-700">
                                    <div class="skeleton skeleton-text" style="width: 40%"></div>
                                    <div class="skeleton skeleton-title"></div>
                                </div>
                                <div class="card-gradient rounded-xl p-6 border border-slate-700">
                                    <div class="skeleton skeleton-text" style="width: 40%"></div>
                                    <div class="skeleton skeleton-title"></div>
                                </div>
                                <div class="card-gradient rounded-xl p-6 border border-slate-700">
                                    <div class="skeleton skeleton-text" style="width: 40%"></div>
                                    <div class="skeleton skeleton-title"></div>
                                </div>
                            </div>
                            <div class="card-gradient rounded-xl p-6 border border-slate-700">
                                <div class="skeleton skeleton-title" style="width: 30%"></div>
                                <div class="skeleton skeleton-card"></div>
                            </div>
                        </div>

                        <!-- Analysis Results -->
                        <div id="analysisResults" class="hidden mt-8 space-y-8">
                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="card-gradient rounded-xl p-6 border border-slate-700 text-center">
                                    <div class="text-sm text-slate-400 mb-2">Общий риск</div>
                                    <div id="overallRisk" class="px-4 py-2 rounded-full text-sm font-medium inline-block risk-medium">Средний риск</div>
                                </div>
                                <div class="card-gradient rounded-xl p-6 border border-slate-700 text-center">
                                    <div class="text-sm text-slate-400 mb-2">Найдено рисков</div>
                                    <div class="text-3xl font-bold text-red-400">4</div>
                                </div>
                                <div class="card-gradient rounded-xl p-6 border border-slate-700 text-center">
                                    <div class="text-sm text-slate-400 mb-2">Время анализа</div>
                                    <div class="text-3xl font-bold text-green-400">58 сек</div>
                                </div>
                            </div>
                            <div class="grid lg:grid-cols-2 gap-8">
                                <div class="card-gradient rounded-xl p-6 border border-slate-700">
                                    <h3 class="text-lg font-semibold mb-4">Распределение рисков</h3>
                                    <div class="h-64">
                                        <canvas id="riskChart"></canvas>
                                    </div>
                                </div>
                                <div class="card-gradient rounded-xl p-6 border border-slate-700">
                                    <h3 class="text-lg font-semibold mb-4">Выявленные проблемы</h3>
                                    <div id="riskList" class="space-y-3 max-h-64 overflow-y-auto pr-2"></div>
                                </div>
                            </div>
                            <div class="card-gradient rounded-xl p-6 border border-slate-700">
                                <h3 class="text-lg font-semibold mb-4">Рекомендации AI</h3>
                                <ul id="recommendations" class="space-y-3"></ul>
                                <div class="mt-6 flex flex-wrap justify-end gap-3">
                                    <button onclick="downloadReport()" class="btn-secondary px-6 py-2 rounded-lg font-medium flex items-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Скачать PDF</span>
                                    </button>
                                    <button onclick="showToast('success', 'Отчёт отправлен на email')" class="btn-primary px-6 py-2 rounded-lg font-medium flex items-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span>Отправить на email</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analytics Tab -->
                    <div id="analysisTab" class="dashboard-tab hidden-section">
                        <div class="grid md:grid-cols-2 gap-6 mb-8">
                            <div class="card-gradient rounded-xl p-6 border border-slate-700">
                                <h3 class="text-lg font-semibold mb-4">Активность проверок</h3>
                                <div class="h-64">
                                    <canvas id="analyticsChart1"></canvas>
                                </div>
                            </div>
                            <div class="card-gradient rounded-xl p-6 border border-slate-700">
                                <h3 class="text-lg font-semibold mb-4">Типы договоров</h3>
                                <div class="h-64">
                                    <canvas id="analyticsChart2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Chat Tab -->
                    <div id="chatTab" class="dashboard-tab hidden-section">
                        <div class="card-gradient rounded-2xl p-6 border border-slate-700">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-bold">AI-ассистент</h2>
                                <button onclick="clearChat()" class="text-slate-400 hover:text-white text-sm flex items-center space-x-2 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Очистить</span>
                                </button>
                            </div>
                            <div class="chat-container card-gradient rounded-xl border border-slate-700">
                                <div class="chat-messages" id="chatMessages">
                                    <div class="chat-message ai">
                                        <div class="chat-avatar ai">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div class="chat-bubble">
                                            Здравствуйте! Я ваш AI-юрист. Задайте мне любой вопрос о договоре, и я помогу вам разобраться. Например:
                                            <ul class="mt-2 space-y-1 text-sm">
                                                <li>• Какие риски в этом договоре?</li>
                                                <li>• Соответствует ли договор ГК РФ?</li>
                                                <li>• Какие пункты нужно изменить?</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="quick-questions" id="quickQuestions">
                                    <button class="quick-question-btn" onclick="askQuestion('Какие основные риски в договоре?')">Основные риски</button>
                                    <button class="quick-question-btn" onclick="askQuestion('Соответствует ли договор законодательству?')">Проверка по ГК РФ</button>
                                    <button class="quick-question-btn" onclick="askQuestion('Какие пункты нужно изменить?')">Рекомендации</button>
                                    <button class="quick-question-btn" onclick="askQuestion('Есть ли скрытые обязательства?')">Скрытые условия</button>
                                </div>
                                <div class="chat-input-area">
                                    <input type="text" id="chatInput" class="chat-input" placeholder="Задайте вопрос о договоре..." onkeypress="handleChatKeyPress(event)" aria-label="Сообщение">
                                    <button onclick="sendMessage()" class="btn-primary px-6 py-3 rounded-lg font-medium flex items-center space-x-2" aria-label="Отправить сообщение">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compare Tab -->
                    <div id="compareTab" class="dashboard-tab hidden-section">
                        <div class="card-gradient rounded-2xl p-6 border border-slate-700">
                            <h2 class="text-2xl font-bold mb-6">Сравнение документов</h2>
                            <div class="comparison-container mb-6">
                                <div class="comparison-panel">
                                    <h3 class="font-semibold mb-4 flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Документ A</span>
                                    </h3>
                                    <div class="upload-zone rounded-xl p-8 text-center bg-slate-800/50" onclick="document.getElementById('fileInputA').click()">
                                        <input type="file" id="fileInputA" class="hidden" accept=".pdf,.docx,.txt">
                                        <svg class="w-12 h-12 text-slate-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <p class="text-sm text-slate-400">Загрузите первый документ</p>
                                    </div>
                                </div>
                                <div class="comparison-panel">
                                    <h3 class="font-semibold mb-4 flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Документ B</span>
                                    </h3>
                                    <div class="upload-zone rounded-xl p-8 text-center bg-slate-800/50" onclick="document.getElementById('fileInputB').click()">
                                        <input type="file" id="fileInputB" class="hidden" accept=".pdf,.docx,.txt">
                                        <svg class="w-12 h-12 text-slate-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <p class="text-sm text-slate-400">Загрузите второй документ</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-center mb-6">
                                <button onclick="compareDocuments()" class="btn-primary px-8 py-3 rounded-lg font-medium flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    <span>Сравнить документы</span>
                                </button>
                            </div>
                            <div id="comparisonResults" class="hidden">
                                <div class="card-gradient rounded-xl p-6 border border-slate-700 mb-6">
                                    <h3 class="font-semibold mb-4">Результаты сравнения</h3>
                                    <div class="space-y-4">
                                        <div class="diff-added">
                                            <strong class="text-green-400">+ Добавлено:</strong> Пункт о конфиденциальности расширен до 5 лет
                                        </div>
                                        <div class="diff-removed">
                                            <strong class="text-red-400">− Удалено:</strong> Ограничение ответственности до 100% стоимости договора
                                        </div>
                                        <div class="diff-modified">
                                            <strong class="text-orange-400">∼ Изменено:</strong> Срок оплаты изменён с 30 до 14 дней
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <button onclick="downloadComparison()" class="btn-primary px-6 py-2 rounded-lg font-medium flex items-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Скачать отчёт</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History Tab -->
                    <div id="historyTab" class="dashboard-tab hidden-section">
                        <div class="card-gradient rounded-2xl p-6 border border-slate-700">
                            <h2 class="text-2xl font-bold mb-6">История анализов</h2>
                            <div class="timeline">
                                <div class="timeline-item completed">
                                    <div class="card-gradient rounded-xl p-4 border border-slate-700">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="font-semibold">Договор поставки №1234</div>
                                                    <div class="text-sm text-slate-400">Анализ завершён • Средний риск</div>
                                                </div>
                                            </div>
                                            <div class="text-sm text-slate-400">2 минуты назад</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item warning">
                                    <div class="card-gradient rounded-xl p-4 border border-slate-700">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="font-semibold">Договор аренды №1233</div>
                                                    <div class="text-sm text-slate-400">Анализ завершён • Высокий риск</div>
                                                </div>
                                            </div>
                                            <div class="text-sm text-slate-400">1 час назад</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item completed">
                                    <div class="card-gradient rounded-xl p-4 border border-slate-700">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="font-semibold">NDA №1232</div>
                                                    <div class="text-sm text-slate-400">Анализ завершён • Низкий риск</div>
                                                </div>
                                            </div>
                                            <div class="text-sm text-slate-400">Вчера, 15:30</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="card-gradient rounded-xl p-4 border border-slate-700">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="font-semibold">Договор услуг №1231</div>
                                                    <div class="text-sm text-slate-400">Анализ завершён • Средний риск</div>
                                                </div>
                                            </div>
                                            <div class="text-sm text-slate-400">2 дня назад</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Tab -->
                    <div id="settingsTab" class="dashboard-tab hidden-section">
                        <div class="card-gradient rounded-xl p-6 border border-slate-700">
                            <h3 class="text-lg font-semibold mb-6">Настройки профиля</h3>
                            <div class="space-y-4 max-w-lg">
                                <div>
                                    <label class="block text-sm text-slate-400 mb-2">Email</label>
                                    <input type="email" value="user@company.com" aria-label="Email">
                                </div>
                                <div>
                                    <label class="block text-sm text-slate-400 mb-2">Язык интерфейса</label>
                                    <select aria-label="Язык интерфейса">
                                        <option>Русский</option>
                                        <option>English</option>
                                        <option>Қазақша</option>
                                    </select>
                                </div>
                                <div class="flex items-center justify-between py-4 border-t border-slate-700">
                                    <div>
                                        <div class="font-medium">Уведомления на email</div>
                                        <div class="text-sm text-slate-400">Получать отчёты о завершении анализа</div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between py-4 border-t border-slate-700">
                                    <div>
                                        <div class="font-medium">Автоматический анализ</div>
                                        <div class="text-sm text-slate-400">Начинать анализ сразу после загрузки</div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <div class="pt-4">
                                    <button onclick="showToast('success', 'Настройки сохранены')" class="btn-primary px-6 py-3 rounded-lg font-medium">Сохранить изменения</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="py-16 px-4 mt-auto">
    <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-4 gap-8 mb-12">
            <!-- Brand -->
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="text-xl font-bold glow-text">LegalAI Pro</span>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed">{{ __('ui.footer.brand_description') }}</p>
            </div>

            <!-- Product -->
            <div>
                <h4 class="font-semibold text-white mb-4">{{ __('ui.footer.product') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.features') }}</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.pricing') }}</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.api') }}</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.integrations') }}</a></li>
                </ul>
            </div>

            <!-- Resources -->
            <div>
                <h4 class="font-semibold text-white mb-4">{{ __('ui.footer.resources') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.documentation') }}</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.blog') }}</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.webinars') }}</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.status') }}</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div>
                <h4 class="font-semibold text-white mb-4">{{ __('ui.footer.company') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.about') }}</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.careers') }}</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.contacts') }}</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ __('ui.footer.partners') }}</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="flex flex-col md:flex-row items-center justify-between pt-8 border-t border-slate-700">
            <p class="text-slate-400 text-sm">{{ __('ui.copyright', ['year' => date('Y')]) }}</p>
            <div class="flex space-x-6 mt-4 md:mt-0">
                <a href="#" class="text-slate-400 hover:text-white transition text-sm">{{ __('ui.privacy_policy') }}</a>
                <a href="#" class="text-slate-400 hover:text-white transition text-sm">{{ __('ui.terms_of_use') }}</a>
                <a href="#" class="text-slate-400 hover:text-white transition text-sm">{{ __('ui.support') }}</a>
            </div>
        </div>
    </div>
</footer>
<!-- Demo Modal -->
<div id="demoModal" class="modal-overlay hidden" onclick="closeModal('demoModal')">
    <div class="modal-content max-w-4xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between p-6 border-b border-slate-700">
            <h3 class="text-xl font-semibold">Демо-видео платформы</h3>
            <button onclick="closeModal('demoModal')" class="text-slate-400 hover:text-white transition p-2" aria-label="Закрыть">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6">
            <div class="aspect-video bg-slate-800 rounded-xl flex items-center justify-center">
                <svg class="w-20 h-20 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
</div>

<!-- Create Template Modal -->
<div id="createTemplateModal" class="modal-overlay hidden" onclick="closeModal('createTemplateModal')">
    <div class="modal-content max-w-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between p-6 border-b border-slate-700">
            <h3 class="text-xl font-semibold">Создать новый шаблон</h3>
            <button onclick="closeModal('createTemplateModal')" class="text-slate-400 hover:text-white transition p-2" aria-label="Закрыть">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm text-slate-400 mb-2">Название шаблона</label>
                <input type="text" placeholder="Введите название" aria-label="Название шаблона">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-2">Тип договора</label>
                <select aria-label="Тип договора">
                    <option>Поставка</option>
                    <option>Аренда</option>
                    <option>Услуги</option>
                    <option>Подряд</option>
                    <option>Другой</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-2">Описание</label>
                <textarea rows="4" placeholder="Опишите шаблон" aria-label="Описание шаблона"></textarea>
            </div>
        </div>
        <div class="p-6 border-t border-slate-700 flex justify-end space-x-3">
            <button onclick="closeModal('createTemplateModal')" class="btn-secondary px-6 py-3 rounded-lg font-medium">Отмена</button>
            <button onclick="closeModal('createTemplateModal'); showToast('success', 'Шаблон создан')" class="btn-primary px-6 py-3 rounded-lg font-medium">Создать</button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast">
    <svg id="toastIcon" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    <span id="toastMessage">Сообщение</span>
</div>

<!-- Floating Action Button -->
<div class="fab" onclick="showToast('info', 'Нужна помощь? Напишите в поддержку!')" role="button" aria-label="Помощь">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
    </svg>
</div>

<script>

    // ========== THEME MANAGEMENT ==========
    function toggleTheme() {
        document.body.classList.toggle('light-theme');
        const isLight = document.body.classList.contains('light-theme');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        showToast('success', isLight ? 'Светлая тема включена' : 'Тёмная тема включена');
        updateChartsTheme();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.body.classList.add('light-theme');
        }
        initScrollReveal();
    });

    function updateChartsTheme() {
        if (riskChart) {
            riskChart.options.scales.y.grid.color = 'rgba(148, 163, 184, 0.15)';
            riskChart.options.scales.y.ticks.color = '#94a3b8';
            riskChart.options.scales.x.ticks.color = '#94a3b8';
            riskChart.update();
        }
        if (analyticsChart1) {
            analyticsChart1.options.scales.y.grid.color = 'rgba(148, 163, 184, 0.15)';
            analyticsChart1.options.scales.y.ticks.color = '#94a3b8';
            analyticsChart1.options.scales.x.ticks.color = '#94a3b8';
            analyticsChart1.update();
        }
        if (analyticsChart2) {
            analyticsChart2.options.plugins.legend.labels.color = '#94a3b8';
            analyticsChart2.update();
        }
    }

    // ========== SCROLL REVEAL ==========
    function initScrollReveal() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    }

    // ========== TOAST NOTIFICATIONS ==========
    function showToast(type, message) {
        const toast = document.getElementById('toast');
        const toastIcon = document.getElementById('toastIcon');
        const toastMessage = document.getElementById('toastMessage');

        toast.className = `toast ${type}`;
        toastMessage.textContent = message;

        const icons = {
            success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
            error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
            warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
            info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        };

        toastIcon.innerHTML = icons[type] || icons.info;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // ========== NAVIGATION ==========
    function showSection(sectionId) {
        const landing = document.getElementById('landingSection');
        const dashboard = document.getElementById('dashboardSection');

        if (sectionId === 'landing') {
            landing.classList.remove('hidden-section');
            landing.classList.add('active-section');
            dashboard.classList.add('hidden-section');
            dashboard.classList.remove('active-section');
        } else {
            landing.classList.add('hidden-section');
            landing.classList.remove('active-section');
            dashboard.classList.remove('hidden-section');
            dashboard.classList.add('active-section');
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function switchDashboardTab(tabName) {
        document.querySelectorAll('.dashboard-tab').forEach(tab => {
            tab.classList.add('hidden-section');
            tab.classList.remove('active-section');
        });
        document.querySelectorAll('.sidebar-item').forEach(item => {
            item.classList.remove('active');
        });

        const targetTab = document.getElementById(tabName + 'Tab');
        if (targetTab) {
            targetTab.classList.remove('hidden-section');
            targetTab.classList.add('active-section');
        }

        document.querySelectorAll('.sidebar-item').forEach(btn => {
            if (btn.getAttribute('onclick')?.includes(tabName)) {
                btn.classList.add('active');
            }
        });

        if (tabName === 'analysis') {
            initAnalyticsCharts();
        }
    }

    // ========== MOBILE MENU ==========
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    }

    // ========== MODALS ==========
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Close modals on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.add('hidden');
            }
        });
    });

    // ========== NOTIFICATIONS ==========
    function toggleNotifications() {
        const panel = document.getElementById('notificationPanel');
        panel.classList.toggle('hidden');
    }

    document.addEventListener('click', (e) => {
        const panel = document.getElementById('notificationPanel');
        if (panel && !panel.contains(e.target) && !e.target.closest('[aria-label="Уведомления"]')) {
            panel.classList.add('hidden');
        }
    });

    // ========== SLIDER ==========
    function scrollSlider(direction) {
        const track = document.getElementById('sliderTrack');
        const scrollAmount = 280;
        track.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
    }

    // ========== FAQ ==========
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = btn.parentElement;
            const isActive = item.classList.contains('active');

            document.querySelectorAll('.faq-item').forEach(faqItem => {
                faqItem.classList.remove('active');
                faqItem.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            });

            if (!isActive) {
                item.classList.add('active');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // ========== FILE UPLOAD ==========
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const analyzeBtn = document.getElementById('analyzeBtn');
    let uploadedFile = null;

    if (dropZone) {
        dropZone.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) handleFile(e.target.files[0]);
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) handleFile(e.dataTransfer.files[0]);
        });
    }

    function handleFile(file) {
        const validExtensions = ['.pdf', '.docx', '.txt'];
        const fileExt = '.' + file.name.split('.').pop().toLowerCase();

        if (!validExtensions.includes(fileExt)) {
            showToast('error', 'Пожалуйста, загрузите файл в формате PDF, DOCX или TXT');
            return;
        }

        if (file.size > 50 * 1024 * 1024) {
            showToast('error', 'Размер файла не должен превышать 50 МБ');
            return;
        }

        uploadedFile = file;
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileInfo.classList.remove('hidden');
        analyzeBtn.disabled = false;
        analyzeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        showToast('success', 'Файл загружен успешно');
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function removeFile() {
        uploadedFile = null;
        fileInput.value = '';
        fileInfo.classList.add('hidden');
        analyzeBtn.disabled = true;
        analyzeBtn.classList.add('opacity-50', 'cursor-not-allowed');
        resetAnalysis();
    }

    // ========== ANALYSIS ==========
    let riskChart = null;
    let analyticsChart1 = null;
    let analyticsChart2 = null;

    function startAnalysis() {
        if (!uploadedFile) return;

        document.getElementById('analysisResults').classList.add('hidden');
        document.getElementById('analysisProgress').classList.remove('hidden');

        let progress = 0;
        const interval = setInterval(() => {
            progress += 2;
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressPercent').textContent = progress + '%';

            if (progress >= 25) completeStep('step1');
            if (progress >= 50) completeStep('step2');
            if (progress >= 75) completeStep('step3');
            if (progress >= 100) {
                completeStep('step3');
                clearInterval(interval);
                setTimeout(showResults, 500);
            }
        }, 60);
    }

    function completeStep(stepId) {
        const step = document.getElementById(stepId);
        if (step) {
            step.classList.remove('border-slate-600');
            step.classList.add('border-green-500', 'bg-green-500');
            step.querySelector('svg').classList.remove('text-transparent');
            step.querySelector('svg').classList.add('text-white');
        }
    }

    function showResults() {
        document.getElementById('analysisProgress').classList.add('hidden');
        document.getElementById('analysisResults').classList.remove('hidden');

        const riskData = {
            labels: ['Финансовые', 'Юридические', 'Операционные', 'Репутационные'],
            datasets: [{
                label: 'Уровень риска',
                data: [75, 45, 30, 20],
                backgroundColor: ['rgba(239,68,68,0.8)', 'rgba(245,158,11,0.8)', 'rgba(34,197,94,0.8)', 'rgba(59,130,246,0.8)'],
                borderColor: ['rgba(239,68,68,1)', 'rgba(245,158,11,1)', 'rgba(34,197,94,1)', 'rgba(59,130,246,1)'],
                borderWidth: 1
            }]
        };

        const ctx = document.getElementById('riskChart').getContext('2d');
        if (riskChart) riskChart.destroy();

        riskChart = new Chart(ctx, {
            type: 'bar',
            data: riskData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, max: 100, grid: { color: 'rgba(148,163,184,0.15)' }, ticks: { color: '#94a3b8' } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                },
                plugins: { legend: { display: false } }
            }
        });

        const riskItems = [
            { level: 'high', text: 'Штрафные санкции превышают 0.5% в день — рекомендуется снизить до 0.1%' },
            { level: 'medium', text: 'Отсутствует пункт о форс-мажорных обстоятельствах' },
            { level: 'medium', text: 'Не указаны сроки оплаты — рекомендуется добавить конкретные даты' },
            { level: 'low', text: 'Пункт о конфиденциальности требует уточнения сроков действия' }
        ];

        const riskList = document.getElementById('riskList');
        riskList.innerHTML = '';

        const levelConfig = {
            high: { class: 'bg-red-500/20', text: 'Высокий', color: 'text-red-400' },
            medium: { class: 'bg-orange-500/20', text: 'Средний', color: 'text-orange-400' },
            low: { class: 'bg-green-500/20', text: 'Низкий', color: 'text-green-400' }
        };

        riskItems.forEach(item => {
            const config = levelConfig[item.level];
            const div = document.createElement('div');
            div.className = 'risk-item p-3 rounded-lg flex items-start space-x-3';
            div.innerHTML = `
                    <span class="${config.class} ${config.color} px-2 py-1 rounded text-xs font-medium whitespace-nowrap">${config.text}</span>
                    <span class="text-slate-300 text-sm">${item.text}</span>
                `;
            riskList.appendChild(div);
        });

        const recommendations = [
            'Согласовать размер штрафных санкций с контрагентом',
            'Добавить раздел о форс-мажорных обстоятельствах',
            'Указать конкретные сроки оплаты в днях с момента подписания',
            'Проконсультироваться с юристом перед подписанием'
        ];

        const recList = document.getElementById('recommendations');
        recList.innerHTML = '';
        recommendations.forEach(rec => {
            const li = document.createElement('li');
            li.className = 'flex items-start space-x-2';
            li.innerHTML = `
                    <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span>${rec}</span>
                `;
            recList.appendChild(li);
        });

        const overallRisk = document.getElementById('overallRisk');
        overallRisk.className = 'px-4 py-2 rounded-full text-sm font-medium risk-medium';
        overallRisk.textContent = 'Средний риск';

        initAnalyticsCharts();
        showToast('success', 'Анализ завершён успешно');
    }

    function initAnalyticsCharts() {
        const ctx1 = document.getElementById('analyticsChart1');
        const ctx2 = document.getElementById('analyticsChart2');
        if (!ctx1 || !ctx2) return;

        if (analyticsChart1) analyticsChart1.destroy();
        if (analyticsChart2) analyticsChart2.destroy();

        analyticsChart1 = new Chart(ctx1.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн'],
                datasets: [{
                    label: 'Договоры',
                    data: [120, 150, 180, 220, 280, 340],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { grid: { color: 'rgba(148,163,184,0.15)' }, ticks: { color: '#94a3b8' } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                },
                plugins: { legend: { display: false } }
            }
        });

        analyticsChart2 = new Chart(ctx2.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Поставка', 'Аренда', 'Услуги', 'Подряд', 'Другие'],
                datasets: [{
                    data: [35, 25, 20, 15, 5],
                    backgroundColor: ['rgba(59,130,246,0.8)', 'rgba(168,85,247,0.8)', 'rgba(34,197,94,0.8)', 'rgba(245,158,11,0.8)', 'rgba(239,68,68,0.8)'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8' } } }
            }
        });
    }

    function resetAnalysis() {
        document.getElementById('analysisProgress').classList.add('hidden');
        document.getElementById('analysisResults').classList.add('hidden');

        ['step1', 'step2', 'step3'].forEach(stepId => {
            const step = document.getElementById(stepId);
            if (step) {
                step.classList.remove('border-green-500', 'bg-green-500');
                step.classList.add('border-slate-600');
                step.querySelector('svg').classList.add('text-transparent');
                step.querySelector('svg').classList.remove('text-white');
            }
        });

        document.getElementById('progressBar').style.width = '0%';
        document.getElementById('progressPercent').textContent = '0%';
    }

    function downloadReport() { showToast('success', 'Отчёт загружен'); }
    function downloadComparison() { showToast('success', 'Отчёт о сравнении загружен'); }

    // ========== CHAT ==========
    function sendMessage() {
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        if (!message) return;

        addChatMessage('user', message);
        input.value = '';
        showTypingIndicator();

        setTimeout(() => {
            removeTypingIndicator();
            const responses = [
                'На основе анализа договора, я рекомендую обратить внимание на пункты об ответственности сторон. Текущие формулировки могут создать дополнительные риски.',
                'Договор в целом соответствует требованиям ГК РФ, однако есть несколько пунктов, которые стоит уточнить для лучшей защиты ваших интересов.',
                'Я обнаружил 4 потенциальных риска в этом договоре. Наиболее критичный — неограниченная ответственность без верхнего лимита.',
                'Рекомендую добавить пункт о форс-мажорных обстоятельствах и уточнить сроки оплаты для избежания неоднозначного толкования.'
            ];
            const randomResponse = responses[Math.floor(Math.random() * responses.length)];
            addChatMessage('ai', randomResponse);
        }, 1500);
    }

    function askQuestion(question) {
        document.getElementById('chatInput').value = question;
        sendMessage();
    }

    function addChatMessage(type, message) {
        const messagesContainer = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${type}`;

        const avatarSvg = type === 'ai'
            ? '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'
            : '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>';

        messageDiv.innerHTML = `
                <div class="chat-avatar ${type}">${avatarSvg}</div>
                <div class="chat-bubble">${message}</div>
            `;
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function showTypingIndicator() {
        const messagesContainer = document.getElementById('chatMessages');
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message ai';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
                <div class="chat-avatar ai">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div class="chat-bubble">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
            `;
        messagesContainer.appendChild(typingDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function removeTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) typingIndicator.remove();
    }

    function clearChat() {
        const messagesContainer = document.getElementById('chatMessages');
        messagesContainer.innerHTML = `
                <div class="chat-message ai">
                    <div class="chat-avatar ai">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="chat-bubble">
                        Здравствуйте! Я ваш AI-юрист. Задайте мне любой вопрос о договоре, и я помогу вам разобраться.
                    </div>
                </div>
            `;
        showToast('success', 'Чат очищен');
    }

    function handleChatKeyPress(event) {
        if (event.key === 'Enter') sendMessage();
    }

    // ========== DOCUMENT COMPARISON ==========
    function compareDocuments() {
        const fileA = document.getElementById('fileInputA').files[0];
        const fileB = document.getElementById('fileInputB').files[0];

        if (!fileA || !fileB) {
            showToast('warning', 'Пожалуйста, загрузите оба документа');
            return;
        }

        showToast('info', 'Сравнение документов...');

        setTimeout(() => {
            document.getElementById('comparisonResults').classList.remove('hidden');
            showToast('success', 'Сравнение завершено');
        }, 2000);
    }

    // ========== COMMAND PALETTE ==========
    const commands = [
        { id: 'upload', title: '📤 Загрузить документ', shortcut: 'U', action: () => { showSection('dashboard'); switchDashboardTab('upload'); } },
        { id: 'chat', title: '💬 AI-ассистент', shortcut: 'C', action: () => { showSection('dashboard'); switchDashboardTab('chat'); } },
        { id: 'compare', title: '🔍 Сравнить документы', shortcut: 'D', action: () => { showSection('dashboard'); switchDashboardTab('compare'); } },
        { id: 'history', title: '📋 История анализов', shortcut: 'H', action: () => { showSection('dashboard'); switchDashboardTab('history'); } },
        { id: 'analytics', title: '📊 Аналитика', shortcut: 'A', action: () => { showSection('dashboard'); switchDashboardTab('analysis'); } },
        { id: 'settings', title: '⚙️ Настройки', shortcut: 'S', action: () => { showSection('dashboard'); switchDashboardTab('settings'); } },
        { id: 'theme-dark', title: '🌙 Тёмная тема', shortcut: 'T', action: () => { if (document.body.classList.contains('light-theme')) toggleTheme(); } },
        { id: 'theme-light', title: '☀️ Светлая тема', shortcut: 'T', action: () => { if (!document.body.classList.contains('light-theme')) toggleTheme(); } },
        { id: 'help', title: '❓ Справка и поддержка', shortcut: '?', action: () => showToast('info', 'Свяжитесь с поддержкой: support@legalai.pro') },
        { id: 'home', title: '🏠 Главная страница', shortcut: 'H', action: () => showSection('landing') },
    ];

    let focusedCommandIndex = 0;

    function openCommandPalette() {
        const palette = document.getElementById('commandPalette');
        const input = document.getElementById('commandInput');
        palette.classList.remove('hidden');
        input.focus();
        input.value = '';
        focusedCommandIndex = 0;
        renderCommandResults('');
    }

    function closeCommandPalette() {
        document.getElementById('commandPalette').classList.add('hidden');
        document.getElementById('commandInput').value = '';
        focusedCommandIndex = 0;
    }

    function renderCommandResults(query) {
        const container = document.getElementById('commandResults');
        const filtered = commands.filter(cmd =>
            cmd.title.toLowerCase().includes(query.toLowerCase())
        );

        focusedCommandIndex = Math.min(focusedCommandIndex, filtered.length - 1);

        container.innerHTML = filtered.map((cmd, index) => `
                <div class="command-item ${index === focusedCommandIndex ? 'focused' : ''}"
                     onclick="executeCommand(${commands.indexOf(cmd)})"
                     onmouseenter="focusedCommandIndex = ${index}; updateFocusedCommand()">
                    <span>${cmd.title}</span>
                    <kbd>${cmd.shortcut}</kbd>
                </div>
            `).join('');

        if (filtered.length === 0) {
            container.innerHTML = `
                    <div class="p-8 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Ничего не найдено</p>
                    </div>
                `;
        }
    }

    function updateFocusedCommand() {
        document.querySelectorAll('.command-item').forEach((item, index) => {
            item.classList.toggle('focused', index === focusedCommandIndex);
        });
    }

    function executeCommand(index) {
        commands[index].action();
        closeCommandPalette();
    }

    document.getElementById('commandInput')?.addEventListener('input', (e) => {
        focusedCommandIndex = 0;
        renderCommandResults(e.target.value);
    });

    document.getElementById('commandInput')?.addEventListener('keydown', (e) => {
        const filtered = commands.filter(cmd =>
            cmd.title.toLowerCase().includes(e.target.value.toLowerCase())
        );

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            focusedCommandIndex = Math.min(focusedCommandIndex + 1, filtered.length - 1);
            updateFocusedCommand();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            focusedCommandIndex = Math.max(focusedCommandIndex - 1, 0);
            updateFocusedCommand();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (filtered.length > 0) {
                executeCommand(commands.indexOf(filtered[focusedCommandIndex]));
            }
        }
    });

    // Global keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const palette = document.getElementById('commandPalette');
            if (palette.classList.contains('hidden')) {
                openCommandPalette();
            } else {
                closeCommandPalette();
            }
        }
        if (e.key === 'Escape') {
            closeCommandPalette();
            document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(modal => {
                modal.classList.add('hidden');
            });
        }
    });

    // ========== INITIALIZATION ==========
    document.addEventListener('DOMContentLoaded', () => {
        ['step1', 'step2', 'step3'].forEach(stepId => {
            const step = document.getElementById(stepId);
            if (step) {
                step.classList.remove('border-green-500', 'bg-green-500');
                step.classList.add('border-slate-600');
                step.querySelector('svg').classList.add('text-transparent');
                step.querySelector('svg').classList.remove('text-white');
            }
        });

        initScrollReveal();
    });
</script>
</body>
</html>
