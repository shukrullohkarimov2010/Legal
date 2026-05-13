<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AI Конструктор Договоров — создавайте юридические документы за минуты с помощью искусственного интеллекта">
    <title>AI Конструктор Договоров PRO | Современный генератор документов</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://unpkg.com/docx@7.4.1/build/index.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        :root {
            /* Colors */
            --color-bg: #0a0a0f;
            --color-surface: #12121a;
            --color-surface-elevated: #1a1a25;
            --color-border: rgba(255, 255, 255, 0.08);
            --color-border-hover: rgba(139, 92, 246, 0.3);

            /* Text */
            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --text-muted: #71717a;

            /* Brand */
            --brand-primary: #8b5cf6;
            --brand-secondary: #06b6d4;
            --brand-accent: #f59e0b;
            --brand-success: #10b981;
            --brand-error: #ef4444;

            /* Gradients */
            --gradient-brand: linear-gradient(135deg, #8b5cf6 0%, #6366f1 50%, #06b6d4 100%);
            --gradient-glow: radial-gradient(ellipse at center, rgba(139, 92, 246, 0.25) 0%, transparent 70%);
            --gradient-surface: linear-gradient(145deg, rgba(26, 27, 46, 0.9), rgba(19, 20, 31, 0.95));

            /* Shadows */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 8px 30px rgba(0, 0, 0, 0.5);
            --shadow-glow: 0 0 40px rgba(139, 92, 246, 0.3);
            --shadow-inner: inset 0 1px 0 rgba(255, 255, 255, 0.05);

            /* Radius */
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-full: 9999px;

            /* Spacing */
            --space-1: 4px;
            --space-2: 8px;
            --space-3: 12px;
            --space-4: 16px;
            --space-5: 24px;
            --space-6: 32px;
            --space-8: 48px;

            /* Transitions */
            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 400ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
        }

        /* Animated Background */
        .bg-gradient {
            position: fixed;
            inset: 0;
            z-index: -1;
            background:
                radial-gradient(ellipse at 20% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(6, 182, 212, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(99, 102, 241, 0.05) 0%, transparent 70%),
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

        .bg-orb:nth-child(1) {
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            top: -200px;
            right: -200px;
        }

        .bg-orb:nth-child(2) {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #06b6d4, #22c55e);
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
            background: rgba(18, 18, 26, 0.8);
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
            background: linear-gradient(135deg, #fff 0%, #a1a1aa 100%);
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
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 32px;
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

        .header-link:hover {
            color: var(--text-primary);
        }

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

        .header-link:hover::after {
            width: 100%;
        }

        /* Profile */
        .profile-wrapper {
            position: relative;
        }

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

        /* Profile Dropdown */
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

        .profile-dropdown.show {
            display: block;
        }

        @keyframes dropdownIn {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
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

        .profile-dropdown-info {
            flex: 1;
            min-width: 0;
        }

        .profile-dropdown-name {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-dropdown-email {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
        }

        .profile-dropdown-email i {
            width: 14px;
            height: 14px;
        }

        .profile-dropdown-menu {
            padding: 8px 0;
        }

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
            transition: color var(--transition-fast);
        }

        .profile-dropdown-item:hover i {
            color: var(--brand-primary);
        }

        .profile-dropdown-item.logout {
            color: var(--brand-error);
            border-top: 1px solid var(--color-border);
            margin-top: 8px;
            padding-top: 12px;
        }

        .profile-dropdown-item.logout:hover {
            background: rgba(239, 68, 68, 0.1);
            color: var(--brand-error);
        }

        .profile-dropdown-item.logout i {
            color: var(--brand-error);
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
            position: relative;
        }

        .step:hover {
            transform: translateY(-2px);
        }

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

        .step.active .step-dot::before {
            opacity: 1;
        }

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
            transition: color var(--transition-fast);
        }

        .step.active .step-label {
            color: var(--brand-primary);
        }

        .step.done .step-label {
            color: var(--brand-success);
        }

        .step-line {
            width: 60px;
            height: 2px;
            background: var(--color-border);
            transition: all var(--transition-base);
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

        .step-line.done::before {
            transform: scaleX(1);
        }

        /* Main Container */
        .main {
            max-width: 900px;
            margin: 32px auto;
            padding: 0 24px;
        }

        /* Page */
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
            background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.3), transparent);
        }

        .page.active {
            display: block;
        }

        @keyframes pageIn {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
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
            color: white;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 0.9375rem;
            line-height: 1.6;
            max-width: 500px;
        }

        /* Form Fields */
        .field {
            margin-bottom: 24px;
        }

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

        .field-required {
            color: var(--brand-error);
            margin-left: 2px;
        }

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

        .field-input:hover,
        .field-textarea:hover,
        .field-select:hover {
            border-color: var(--border-hover);
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

        /* Buttons */
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

        .btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            opacity: 0;
            transition: opacity var(--transition-fast);
        }

        .btn:hover::before {
            opacity: 1;
        }

        .btn:active {
            transform: translateY(1px) scale(0.99);
        }

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

        /* Loader */
        .loader {
            display: none;
            text-align: center;
            padding: 64px 24px;
            animation: fadeIn 0.3s ease-out;
        }

        .loader.active {
            display: block;
        }

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

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

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
            font-weight: 400;
        }

        /* Result */
        .result {
            display: none;
            margin-top: 32px;
            animation: pageIn 0.4s ease-out;
        }

        .result.active {
            display: block;
        }

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
            color: white;
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

        /* Contract Box */
        .contract-box {
            background: #ffffff;
            color: #1e293b;
            border-radius: var(--radius-lg);
            padding: 40px;
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 0.875rem;
            line-height: 1.8;
            max-height: 700px;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
            border: 1px solid #e2e8f0;
        }

        .contract-box::-webkit-scrollbar {
            width: 8px;
        }

        .contract-box::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .contract-box::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .contract-box::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .contract-box h1 {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
            color: #111;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .contract-box h2 {
            text-align: center;
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 32px;
            font-style: italic;
        }

        .contract-box h3 {
            font-size: 1.125rem;
            margin: 32px 0 12px;
            color: #1e293b;
            font-weight: 700;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        .contract-box p {
            text-indent: 30px;
            text-align: justify;
            margin-bottom: 12px;
            color: #334155;
        }

        .contract-box ul {
            margin: 12px 0 12px 48px;
        }

        .contract-box li {
            margin-bottom: 6px;
            color: #334155;
        }

        .contract-box table {
            width: 100%;
            margin-top: 32px;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .contract-box td,
        .contract-box th {
            border: 1px solid #cbd5e1;
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        .contract-box th {
            background: #f8fafc;
            font-weight: 600;
            color: #1e293b;
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

        /* AI Analysis */
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

        /* Setup Box */
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
            border-radius: 4px;
        }

        .toggle-row label {
            color: var(--text-secondary);
            font-weight: 500;
            cursor: pointer;
            font-size: 0.875rem;
        }

        /* Legal Grid */
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

        .legal-card:hover::before {
            opacity: 1;
        }

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

        .legal-card .field {
            margin-bottom: 16px;
        }

        .legal-card .field:last-child {
            margin-bottom: 0;
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--color-surface-elevated);
            color: white;
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
            border-color: rgba(16, 185, 129, 0.3);
        }

        .toast.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-color: rgba(239, 68, 68, 0.3);
        }

        .toast.warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #0a0a0f;
            border-color: rgba(245, 158, 11, 0.3);
        }

        /* Modal */
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

        .modal.active {
            display: flex;
        }

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
            from {
                opacity: 0;
                transform: scale(0.95) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
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
            color: white;
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
            color: white;
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

        /* Chip */
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

            .header-nav {
                display: none;
            }

            .grid-2,
            .legal-grid {
                grid-template-columns: 1fr;
            }

            .contract-box {
                padding: 24px;
                font-size: 0.8125rem;
            }

            .page {
                padding: 24px;
            }

            .btn {
                padding: 12px 20px;
                font-size: 0.875rem;
            }

            .step-label {
                display: none;
            }

            .step-dot {
                width: 36px;
                height: 36px;
                font-size: 0.75rem;
            }

            .step-line {
                width: 40px;
            }

            .result-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .result-actions {
                width: 100%;
                justify-content: center;
            }

            .profile-info {
                display: none;
            }

            .profile-btn {
                padding: 6px;
            }
        }

        @media (max-width: 480px) {
            .header-logo {
                width: 36px;
                height: 36px;
                font-size: 1.25rem;
            }

            .header-title {
                font-size: 1rem;
            }

            .page {
                padding: 20px;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .btn-next {
                margin-left: 0;
            }

            .nav {
                flex-direction: column;
            }
        }

        /* Utilities */
        .hidden {
            display: none !important;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        /* Focus visible */
        :focus-visible {
            outline: 2px solid var(--brand-primary);
            outline-offset: 2px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--color-surface);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 4px;
            border: 2px solid var(--color-surface);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--brand-primary);
        }
    </style>
</head>
<body>
@php
    $currentUser = auth()->user();
    $userName = $currentUser?->name ?: 'Пользователь';
    $userEmail = $currentUser?->email ?: 'email@example.com';
    $nameParts = preg_split('/\s+/u', trim($userName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $userInitials = collect($nameParts)->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    $userInitials = $userInitials ?: 'П';
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
            <h1 class="header-title">AI Конструктор Договоров</h1>
            <span class="header-badge">PRO v4.0</span>
        </div>
    </div>

    <nav class="header-nav">
        <a href="{{ route('welcome') }}" class="header-link">Главная</a>
        <a href="{{ route('dashboard') }}" class="header-link">Возможности</a>
    </nav>

    <!-- Profile -->
    <div class="profile-wrapper" id="profileContainer">
        <button class="profile-btn" id="profileButton" aria-label="Профиль" aria-expanded="false" onclick="toggleProfile()">
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
                    <span>Профиль</span>
                </a>
                <a href="#" class="profile-dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>Настройки</span>
                </a>
                <a href="#" class="profile-dropdown-item">
                    <i class="fas fa-file-alt"></i>
                    <span>Мои документы</span>
                </a>
            </div>

            <div class="profile-dropdown-menu">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="profile-dropdown-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Выйти</span>
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
                <h2 class="page-title">Описание сделки</h2>
                <p class="page-subtitle">Расскажите ИИ о вашей сделке — он автоматически извлечёт реквизиты и ключевые условия</p>
            </div>
        </div>

        <div class="field">
            <label class="field-label">
                Суть договора
                <span class="field-required">*</span>
            </label>
            <textarea
                id="f_desc"
                class="field-textarea"
                placeholder="Пример: ООО «СтройМастер» заказывает у ИП Смирнова ремонт офиса 50 м². Срок: 1 месяц, цена: 300 000₽. Оплата: 50% предоплата."
                maxlength="1000"
            ></textarea>
            <div class="field-hint">
                <i class="fas fa-lightbulb"></i>
                Чем подробнее описание — тем точнее результат. Макс. 1000 символов
            </div>
        </div>

        <div class="setup-box">
            <strong>⚙️ Настройки ИИ</strong>
            <code>OLLAMA_ORIGINS="*" ollama serve</code>
            <div class="toggle-row">
                <input type="checkbox" id="useOllama" checked>
                <label for="useOllama">Использовать локальную Ollama</label>
            </div>
            <div class="toggle-row">
                <select id="aiModel" class="field-select" style="width: auto; padding: 6px 12px; font-size: 0.8125rem;">
                    <option value="llama3.1:8b">⚡ llama3.1:8b (быстро)</option>
                    <option value="llama3">🧠 Llama 3 (качественно)</option>
                    <option value="mixtral">🚀 Mixtral (макс. точность)</option>
                </select>
                <span style="color: var(--text-muted); font-size: 0.75rem;">Модель анализа</span>
            </div>
        </div>

        <div class="nav">
            <div></div>
            <button class="btn btn-next" onclick="analyzeAndNext()">
                <i class="fas fa-bolt"></i>
                Анализировать и продолжить
            </button>
        </div>
    </section>

    <!-- Step 2 -->
    <section class="page" data-step="2">
        <div class="page-header">
            <div class="page-icon">🔍</div>
            <div>
                <h2 class="page-title">Проверка данных</h2>
                <p class="page-subtitle">ИИ распознал информацию — отредактируйте при необходимости</p>
            </div>
        </div>

        <div class="ai-analysis" id="aiAnalysis">
            <h4><i class="fas fa-robot"></i> Распознано ИИ:</h4>
            <div class="detected" id="detectedInfo"></div>
        </div>

        <div class="grid-2">
            <div class="field">
                <label class="field-label">Тип договора</label>
                <select id="f_type" class="field-select">
                    <option value="оказания услуг">Оказания услуг</option>
                    <option value="подряда">Подряда</option>
                    <option value="аренды">Аренды</option>
                    <option value="купли-продажи">Купли-продажи</option>
                    <option value="поставки">Поставки</option>
                    <option value="лицензионный">Лицензионный</option>
                </select>
            </div>
            <div class="field">
                <label class="field-label">Сумма (₽)</label>
                <input type="number" id="f_amount" class="field-input" placeholder="300000" min="0" inputmode="numeric">
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
                <label class="field-label">
                    Сторона 1
                    <span class="field-required">*</span>
                </label>
                <input type="text" id="f_p1" class="field-input" placeholder="ООО «Ромашка»" maxlength="100">
            </div>
            <div class="field">
                <label class="field-label">
                    Сторона 2
                    <span class="field-required">*</span>
                </label>
                <input type="text" id="f_p2" class="field-input" placeholder="ИП Петров" maxlength="100">
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
                <label class="field-label">Роль Стороны 1</label>
                <select id="f_role1" class="field-select">
                    <option value="Заказчик">Заказчик</option>
                    <option value="Покупатель">Покупатель</option>
                    <option value="Арендатор">Арендатор</option>
                    <option value="Лицензиат">Лицензиат</option>
                </select>
            </div>
            <div class="field">
                <label class="field-label">Роль Стороны 2</label>
                <select id="f_role2" class="field-select">
                    <option value="Исполнитель">Исполнитель</option>
                    <option value="Продавец">Продавец</option>
                    <option value="Арендодатель">Арендодатель</option>
                    <option value="Лицензиар">Лицензиар</option>
                </select>
            </div>
        </div>

        <div class="field">
            <label class="field-label">Предмет договора</label>
            <textarea id="f_subject" class="field-textarea" placeholder="Что именно предоставляется или выполняется?" maxlength="500" style="min-height: 80px;"></textarea>
        </div>

        <div class="nav">
            <button class="btn btn-back" onclick="goStep(1)">
                <i class="fas fa-arrow-left"></i>
                Назад
            </button>
            <button class="btn btn-next" onclick="goStep(3)">
                Продолжить
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </section>

    <!-- Step 3 -->
    <section class="page" data-step="3">
        <div class="page-header">
            <div class="page-icon">⚖️</div>
            <div>
                <h2 class="page-title">Юридические условия</h2>
                <p class="page-subtitle">Настройте важные параметры договора под вашу сделку</p>
            </div>
        </div>

        <div class="legal-grid">
            <div class="legal-card">
                <h4><i class="fas fa-coins"></i> Оплата</h4>
                <div class="field">
                    <input type="text" id="f_pay" class="field-input" placeholder="50% предоплата, 50% по акту" maxlength="100">
                </div>
                <div class="field">
                    <select id="f_vat" class="field-select">
                        <option value="не указано">НДС не указан</option>
                        <option value="в т.ч. НДС 20%">в т.ч. НДС 20%</option>
                        <option value="без НДС">без НДС</option>
                    </select>
                </div>
                <div class="field-hint">Условия и порядок расчётов</div>
            </div>

            <div class="legal-card">
                <h4><i class="fas fa-calendar"></i> Сроки</h4>
                <div class="field">
                    <input type="text" id="f_duration" class="field-input" placeholder="30 календарных дней" maxlength="50">
                </div>
                <div class="field">
                    <input type="date" id="f_date" class="field-input">
                </div>
                <div class="field-hint">Срок исполнения и дата договора</div>
            </div>

            <div class="legal-card">
                <h4><i class="fas fa-shield-alt"></i> Безопасность</h4>
                <div class="field">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--text-secondary);">
                        <input type="checkbox" id="f_conf" style="width: 16px; height: 16px; margin: 0; accent-color: var(--brand-primary);">
                        <span>Конфиденциальность</span>
                    </label>
                </div>
                <div class="field">
                    <input type="text" id="f_penalty" class="field-input" placeholder="0.1% в день" value="0.1% в день" maxlength="30">
                </div>
                <div class="field-hint">Неустойка и защита данных</div>
            </div>

            <div class="legal-card">
                <h4><i class="fas fa-gavel"></i> Споры</h4>
                <div class="field">
                    <select id="f_dispute" class="field-select">
                        <option value="арбитражный суд">Арбитражный суд</option>
                        <option value="третейский суд">Третейский суд</option>
                        <option value="переговоры">Только переговоры</option>
                    </select>
                </div>
                <div class="field">
                    <input type="text" id="f_city" class="field-input" placeholder="г. Москва" value="г. Москва" maxlength="50">
                </div>
                <div class="field-hint">Подсудность и место подписания</div>
            </div>
        </div>

        <div class="field">
            <label class="field-label">Особые условия</label>
            <textarea id="f_extra" class="field-textarea" placeholder="Гарантии, форс-мажор, дополнительные обязательства..." maxlength="300" style="min-height: 80px;"></textarea>
        </div>

        <div class="nav">
            <button class="btn btn-back" onclick="goStep(2)">
                <i class="fas fa-arrow-left"></i>
                Назад
            </button>
            <button class="btn btn-next" onclick="goStep(4)">
                Продолжить
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </section>

    <!-- Step 4 -->
    <section class="page" data-step="4">
        <div class="page-header">
            <div class="page-icon">✅</div>
            <div>
                <h2 class="page-title">Финальная проверка</h2>
                <p class="page-subtitle">Подтвердите данные перед генерацией документа</p>
            </div>
        </div>

        <div class="summary" id="summary"></div>

        <div class="setup-box">
            <strong>💡 Совет:</strong> После генерации вы сможете отредактировать текст вручную или создать новый черновик на основе этого договора.
        </div>

        <button class="btn btn-generate" id="genBtn" onclick="generate()">
            <i class="fas fa-magic"></i>
            Сгенерировать договор
        </button>

        <div class="nav" style="margin-top: 16px;">
            <button class="btn btn-back" onclick="goStep(3)">
                <i class="fas fa-arrow-left"></i>
                Изменить условия
            </button>
        </div>
    </section>

    <!-- Loader -->
    <div class="loader" id="loader">
        <div class="spinner"></div>
        <div class="loader-text">ИИ составляет юридический документ...</div>
        <span class="loader-subtext">Обычно 15-45 секунд</span>
    </div>

    <!-- Result -->
    <div class="result" id="result">
        <div class="result-header">
            <h2 class="result-title">
                <i class="fas fa-file-contract"></i>
                Договор готов
            </h2>
            <div class="result-actions">
                <button class="btn-sm" onclick="copyText()">
                    <i class="fas fa-copy"></i>
                    Копировать
                </button>
                <button class="btn-sm" onclick="window.print()">
                    <i class="fas fa-print"></i>
                    Печать
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
                    История
                </button>
                <button class="btn-sm" onclick="startOver()">
                    <i class="fas fa-redo"></i>
                    Заново
                </button>
            </div>
        </div>
        <div class="contract-box" id="contractOutput"></div>
    </div>
</main>

<!-- History Modal -->
<div class="modal" id="historyModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">📚 История черновиков</h3>
            <button class="modal-close" onclick="closeHistoryModal()" aria-label="Закрыть">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="historyList"></div>
        <div class="modal-footer">
            <button class="btn-sm btn-sm-danger" onclick="clearAllHistory()">
                <i class="fas fa-trash"></i>
                Очистить всё
            </button>
            <button class="btn-sm" onclick="closeHistoryModal()">Закрыть</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast" role="alert" aria-live="polite"></div>

<script>
    // ===== Configuration =====
    const CONFIG = {
        OLLAMA_URL: 'http://localhost:11434/api/chat',
        HISTORY_KEY: 'contract_builder_history_v5',
        MAX_HISTORY: 10,
        TOTAL_STEPS: 5
    };

    let currentStep = 1;
    let contractText = '';
    const requestCache = new Map();

    // ===== Profile Toggle =====
    function toggleProfile() {
        const dropdown = document.getElementById('profileDropdown');
        const button = document.getElementById('profileButton');
        if (!dropdown || !button) return;

        const isOpen = dropdown.classList.toggle('show');
        button.setAttribute('aria-expanded', isOpen);
    }

    document.addEventListener('click', (e) => {
        const container = document.getElementById('profileContainer');
        const dropdown = document.getElementById('profileDropdown');
        if (container && dropdown && !container.contains(e.target)) {
            dropdown.classList.remove('show');
            document.getElementById('profileButton')?.setAttribute('aria-expanded', 'false');
        }
    });

    // ===== Progress Bar =====
    function buildProgress() {
        const steps = [
            { n: 1, l: 'Описание' },
            { n: 2, l: 'Данные' },
            { n: 3, l: 'Условия' },
            { n: 4, l: 'Проверка' },
            { n: 5, l: 'Готово' }
        ];

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

    // Set today's date
    const dateInput = document.getElementById('f_date');
    if (dateInput) dateInput.valueAsDate = new Date();

    // ===== Navigation =====
    function goStep(n) {
        if (n < 1 || n > CONFIG.TOTAL_STEPS) return;

        if (n > currentStep) {
            if (currentStep === 1 && !document.getElementById('f_desc').value.trim()) {
                toast('⚠️ Введите описание сделки', 'warning');
                return;
            }
            if (currentStep === 2 && (!document.getElementById('f_p1').value.trim() || !document.getElementById('f_p2').value.trim())) {
                toast('⚠️ Укажите обе стороны договора', 'warning');
                return;
            }
        }

        currentStep = n;

        // Hide all pages
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));

        // Show target page
        const targetPage = document.querySelector(`.page[data-step="${n}"]`);
        if (targetPage) targetPage.classList.add('active');

        // Update progress
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

        // Update lines
        for (let i = 1; i < CONFIG.TOTAL_STEPS; i++) {
            const line = document.getElementById(`pline${i}`);
            if (line) line.classList.toggle('done', i < n);
        }

        // Build summary on step 4
        if (n === 4) buildSummary();

        // Hide result on steps < 4
        if (n < 4) document.getElementById('result')?.classList.remove('active');
    }

    // ===== Ollama API =====
    async function ollamaChat(system, user, model = 'llama3.1:8b') {
        // Создаём безопасный ключ кэша без btoa
        const cacheKey = `${model}:${system.slice(0, 50).replace(/\s/g, '')}${user.slice(0, 50).replace(/\s/g, '')}`;
        const cached = requestCache.get(cacheKey);

        if (cached && Date.now() - cached.ts < 5 * 60 * 1000) {
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

            if (!res.ok) throw new Error(`API ошибка: ${res.status}`);

            const json = await res.json();
            const content = json.message?.content?.trim() || '';

            requestCache.set(cacheKey, { data: content, ts: Date.now() });
            if (requestCache.size > 50) {
                const oldest = Array.from(requestCache.entries()).sort((a, b) => a[1].ts - b[1].ts)[0][0];
                requestCache.delete(oldest);
            }

            return content;
        } catch (e) {
            clearTimeout(timeout);
            if (e.name === 'AbortError') throw new Error('Таймаут запроса');
            throw e;
        }
    }
    // ===== Analysis =====
    async function analyzeAndNext() {
        const desc = document.getElementById('f_desc').value.trim();
        if (!desc) {
            toast('⚠️ Введите описание сделки', 'warning');
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
                const prompt = `Ты — эксперт по анализу сделок в РФ. Извлеки данные для договора.
Верни ТОЛЬКО валидный JSON без пояснений:
{
  "type": "оказания услуг|подряда|аренды|купли-продажи|поставки",
  "party1": {"name": "строка", "role": "Заказчик|Покупатель|Арендатор"},
  "party2": {"name": "строка", "role": "Исполнитель|Продавец|Арендодатель"},
  "subject": "строка",
  "amount": число_или_null,
  "city": "строка",
  "duration": "строка",
  "payment": "строка"
}

Текст: ${desc}`;

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
            toast('✅ Данные успешно извлечены', 'success');
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
            party1: { name: 'Заказчик', role: 'Заказчик' },
            party2: { name: 'Исполнитель', role: 'Исполнитель' },
            subject: '',
            amount: null,
            city: 'г. Москва',
            duration: '',
            payment: ''
        };

        const l = desc.toLowerCase();

        if (l.match(/аренд|найм|помещ/)) d.type = 'аренды';
        else if (l.match(/купл|продаж|товар|закуп/)) d.type = 'купли-продажи';
        else if (l.match(/подряд|ремонт|монтаж|строит/)) d.type = 'подряда';
        else if (l.match(/поставк|доставк/)) d.type = 'поставки';

        const sumMatch = desc.match(/(\d[\d\s,.]*)\s*(?:тыс\.?|млн\.?|₽|руб)/i);
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
                party1: 'Сторона 1',
                party2: 'Сторона 2',
                amount: 'Сумма',
                payment: 'Оплата',
                duration: 'Срок',
                type: 'Тип',
                subject: 'Предмет',
                city: 'Город'
            };

            info.innerHTML = items.length ?
                items.map(([k, v]) => {
                    const lbl = labels[k] || k;
                    const val = typeof v === 'object' ?
                        (v.name ? `${v.name} (${v.role})` : JSON.stringify(v).replace(/[{}"]/g, '')) : v;
                    return `<strong>${lbl}:</strong> ${val}<br>`;
                }).join('') :
                '<em style="opacity: 0.8">Данные не распознаны — заполните вручную</em>';
        }
    }

    // ===== Summary =====
    function buildSummary() {
        const g = id => document.getElementById(id)?.value?.trim() || '—';
        const gs = id => document.getElementById(id)?.value || '';

        const amountVal = g('f_amount');
        const amountDisplay = amountVal !== '—' ?
            `${parseInt(amountVal).toLocaleString('ru-RU')} ₽` : '—';

        const vatChip = gs('f_vat') !== 'не указано' ?
            `<span class="chip ${gs('f_vat').includes('НДС') ? 'success' : ''}">${gs('f_vat')}</span>` : '';

        document.getElementById('summary').innerHTML = `
        <div><strong>Тип:</strong> ${gs('f_type')}</div>
        <div><strong>${gs('f_role1')}:</strong> ${g('f_p1')}</div>
        <div><strong>${gs('f_role2')}:</strong> ${g('f_p2')}</div>
        <div><strong>Сумма:</strong> ${amountDisplay} ${vatChip}</div>
        <div><strong>Оплата:</strong> ${g('f_pay')}</div>
        <div><strong>Срок:</strong> ${g('f_duration')}</div>
        ${g('f_subject') !== '—' ? `<div><strong>Предмет:</strong> ${g('f_subject')}</div>` : ''}
        <div><strong>Неустойка:</strong> ${g('f_penalty')}</div>
        <div><strong>Конфиденциальность:</strong> ${document.getElementById('f_conf')?.checked ? '✅ Да' : '❌ Нет'}</div>
        <div><strong>Подсудность:</strong> ${gs('f_dispute')} | ${g('f_city')}</div>
        ${g('f_extra') !== '—' ? `<div><strong>Доп.:</strong> ${g('f_extra')}</div>` : ''}
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
            toast('⚠️ Укажите обе стороны договора', 'warning');
            return;
        }

        const btn = document.getElementById('genBtn');
        const loader = document.getElementById('loader');

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Генерация...';

        document.querySelector('.page[data-step="4"]')?.classList.remove('active');
        loader.classList.add('active');

        try {
            let text;

            if (document.getElementById('useOllama').checked) {
                const prompt = `Ты — корпоративный юрист РФ. Составь договор в формате Markdown.
ТРЕБОВАНИЯ:
1) Только текст договора
2) Официально-деловой стиль, ссылки на ГК РФ
3) Структура: # Заголовок, ## Раздел, 1.1. пункты, | таблицы

Данные:
• Тип: ${data.type}
• ${data.role1}: ${data.p1}
• ${data.role2}: ${data.p2}
• Сумма: ${data.amount ? `${parseInt(data.amount).toLocaleString('ru')} ₽` : '[не указана]'} ${data.vat || ''}
• Оплата: ${data.pay || 'По соглашению'}
• Срок: ${data.duration || '[не указан]'}
• Город: ${data.city || '__________'}
• Неустойка: ${data.penalty || '0.1%/день'}
• Конфиденциальность: ${data.conf ? 'Да' : 'Нет'}
• Подсудность: ${data.dispute || 'Арбитражный суд'}
• Предмет: ${data.subject || data.type}`;

                text = await ollamaChat(
                    'Ты — корпоративный юрист РФ. Составь договор в формате Markdown с ссылками на ГК РФ.',
                    prompt,
                    document.getElementById('aiModel').value
                );
            } else {
                await new Promise(r => setTimeout(r, 600));
                text = buildMockContract(data);
            }

            contractText = text;
            loader.classList.remove('active');

            const output = document.getElementById('contractOutput');
            if (output) {
                output.innerHTML = marked.parse(text);
                output.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            document.getElementById('result')?.classList.add('active');

            // Mark all steps as done
            for (let i = 1; i <= CONFIG.TOTAL_STEPS; i++) {
                document.getElementById(`pdot${i}`)?.classList.add('done');
                document.getElementById(`pstep${i}`)?.classList.remove('active');
            }

            saveHistory(data, text);
            toast('✅ Договор успешно создан!', 'success');
        } catch (e) {
            loader.classList.remove('active');
            document.querySelector('.page[data-step="4"]')?.classList.add('active');
            toast(`⚠️ ${e.message}`, 'error');
            console.error('Generation error:', e);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic"></i> Сгенерировать договор';
        }
    }

    function buildMockContract(d) {
        const dt = new Date(d.date || Date.now());
        const dateStr = dt.toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' });
        const amt = d.amount ? parseInt(d.amount) : null;
        const amtW = amt ? numWords(amt) : '[сумма прописью]';
        const amtF = amt ? amt.toLocaleString('ru-RU') : '[сумма]';

        return `# ДОГОВОР ${d.type.toUpperCase()}

**${d.city || 'г. ____________'}** | **«${dateStr}»**

**${d.p1}**, именуемый «${d.role1}», с одной стороны, и **${d.p2}**, именуемый «${d.role2}», с другой стороны, заключили настоящий договор:

## 1. ПРЕДМЕТ ДОГОВОРА
1.1. ${d.role2} обязуется оказать ${d.role1} услуги по: ${d.subject || d.type}, а ${d.role1} обязуется принять и оплатить их (ст. 432 ГК РФ).

## 2. ПРАВА И ОБЯЗАННОСТИ
2.1. ${d.role2} обязан: выполнить работы качественно и в срок; предоставлять отчётность; соблюдать конфиденциальность.
2.2. ${d.role1} обязан: предоставить информацию; принять результат по акту; оплатить услуги.

## 3. СТОИМОСТЬ И ОПЛАТА
3.1. Цена договора: **${amtF} (${amtW}) рублей** ${d.vat !== 'не указано' ? d.vat : ''}.
3.2. Оплата: ${d.pay || 'По соглашению сторон'} (ст. 314, 316 ГК РФ).

## 4. СРОКИ И СДАЧА-ПРИЕМКА
4.1. Срок исполнения: ${d.duration || '30 календарных дней'}.
4.2. Приёмка по акту; претензии по качеству — в течение 10 рабочих дней.

## 5. ОТВЕТСТВЕННОСТЬ
5.1. За просрочку оплаты/исполнения: неустойка ${d.penalty || '0.1%'} в день (ст. 330, 395 ГК РФ).
5.2. Ограничение ответственности: в пределах суммы договора.

## 6. ФОРС-МАЖОР
6.1. Освобождение от ответственности при обстоятельствах непреодолимой силы (ст. 401 ГК РФ).
6.2. Уведомление: в течение 3 рабочих дней.

${d.conf ? `## 7. КОНФИДЕНЦИАЛЬНОСТЬ
7.1. Стороны обязуются не разглашать условия договора и коммерческую информацию.` : ''}

## ${d.conf ? '8' : '7'}. РАЗРЕШЕНИЕ СПОРОВ
${d.conf ? '8.1' : '7.1'}. Претензионный порядок: 30 календарных дней.
${d.conf ? '8.2' : '7.2'}. Подсудность: ${d.dispute || 'Арбитражный суд по месту ответчика'}.

## ${d.conf ? '9' : '8'}. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ
${d.conf ? '9.1' : '8.1'}. Договор вступает в силу с момента подписания.
${d.conf ? '9.2' : '8.2'}. Изменения — только в письменной форме.
${d.conf ? '9.3' : '8.3'}. Составлен в 2 экземплярах.

## ${d.conf ? '10' : '9'}. РЕКВИЗИТЫ И ПОДПИСИ
| **${d.role1}** | **${d.role2}** |
|----------------|----------------|
| ${d.p1} | ${d.p2} |
| Адрес: [__________] | Адрес: [__________] |
| ИНН/КПП: [__________] | ИНН/КПП: [__________] |
| Р/с: [__________] | Р/с: [__________] |
| Подпись: __________ | Подпись: __________ |`;
    }

    function numWords(n) {
        if (!n) return '[сумма прописью]';
        n = Math.floor(n);
        if (n === 0) return 'ноль рублей';

        const ones = ['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'];
        const teens = ['десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать'];
        const tens = ['', '', 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто'];
        const hunds = ['', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'];

        const formatGroup = (num) => {
            if (num === 0) return '';
            let h = Math.floor(num / 100) % 10,
                t = Math.floor(num / 10) % 10,
                o = num % 10,
                s = '';
            if (h) s += hunds[h] + ' ';
            if (t === 1) s += teens[o];
            else {
                if (t) s += tens[t] + ' ';
                if (o) s += ones[o];
            }
            return s.trim();
        };

        let result = [],
            x = n;
        let th = Math.floor(x / 1000) % 1000;
        if (th > 0) {
            let s = formatGroup(th);
            let f = (th % 10 === 1 && th % 100 !== 11) ? 'тысяча' :
                ((th % 10 >= 2 && th % 10 <= 4 && (th % 100 < 10 || th % 100 > 20)) ? 'тысячи' : 'тысяч');
            result.push(s + ' ' + f);
        }
        let u = formatGroup(x % 1000);
        if (u || th === 0) result.push(u);

        return result.filter(s => s).join(' ').trim() + ' рублей';
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
        } catch (e) {
            console.warn('History error:', e);
        }
    }

    function loadHistory(id) {
        const h = JSON.parse(localStorage.getItem(CONFIG.HISTORY_KEY) || '[]');
        const entry = h.find(e => e.id === id);
        if (!entry) {
            toast('❌ Не найдено', 'error');
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
            const output = document.getElementById('contractOutput');
            if (output) output.innerHTML = marked.parse(entry.contract);
            document.getElementById('result')?.classList.add('active');
            goStep(4);
        } else {
            goStep(2);
        }

        closeHistoryModal();
        toast('📂 Черновик загружен', 'success');
    }

    function deleteHistory(id) {
        let h = JSON.parse(localStorage.getItem(CONFIG.HISTORY_KEY) || '[]');
        const len = h.length;
        h = h.filter(e => e.id !== id);
        localStorage.setItem(CONFIG.HISTORY_KEY, JSON.stringify(h));
        updateHistoryUI();
        toast(`🗑️ Удалено (${len - h.length})`, 'success');
    }

    function clearAllHistory() {
        if (confirm('Удалить ВСЮ историю черновиков?')) {
            localStorage.removeItem(CONFIG.HISTORY_KEY);
            updateHistoryUI();
            toast('🗑️ История очищена', 'success');
        }
    }

    function updateHistoryUI() {
        const c = document.getElementById('historyList');
        if (!c) return;

        const h = JSON.parse(localStorage.getItem(CONFIG.HISTORY_KEY) || '[]');

        if (!h.length) {
            c.innerHTML = '<p style="text-align: center; color: var(--text-muted); padding: 48px 24px; font-size: 0.9375rem;">📭 История пуста</p>';
            return;
        }

        c.innerHTML = h.map(e => {
            const dt = new Date(e.ts);
            const ds = dt.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' });
            const tm = dt.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });

            return `
            <div class="history-item" onclick="loadHistory(${e.id})">
                <div class="history-item-header">
                    <span class="history-item-title">${e.preview}</span>
                    <span class="history-item-date">${ds} ${tm}</span>
                </div>
                ${e.amount ? `<div class="history-item-amount">💰 ${parseInt(e.amount).toLocaleString('ru-RU')} ₽</div>` : ''}
                <div class="history-item-actions" onclick="event.stopPropagation()">
                    <button class="btn-sm" onclick="loadHistory(${e.id})">
                        <i class="fas fa-download"></i>
                        Загрузить
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
            const toast = document.getElementById('toast');
            if (toast?.classList.contains('show')) toast.classList.remove('show');
        }
    });

    function truncate(s, n) {
        if (!s) return '';
        return s.length > n ? s.slice(0, n) + '…' : s;
    }

    // ===== Export =====
    function copyText() {
        if (!contractText) {
            toast('⚠️ Нечего копировать', 'warning');
            return;
        }

        navigator.clipboard.writeText(contractText)
            .then(() => toast('📋 Скопировано в буфер обмена', 'success'))
            .catch(() => toast('❌ Ошибка копирования', 'error'));
    }

    function downloadTxt() {
        if (!contractText) return;

        const blob = new Blob([contractText], { type: 'text/plain;charset=utf-8' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `Договор_${new Date().toISOString().slice(0, 10)}.txt`;
        a.click();
        URL.revokeObjectURL(a.href);
        toast('💾 TXT файл скачан', 'success');
    }

    async function exportToDocx() {
        if (!contractText) {
            toast('⚠️ Сначала сгенерируйте договор', 'warning');
            return;
        }

        toast('🔄 Создаю DOCX...', 'warning');

        try {
            const { Document, Paragraph, TextRun, HeadingLevel, AlignmentType, Packer } = window.docx;
            const lines = contractText.split('\n');
            const children = [];

            for (let line of lines) {
                const t = line.trim();
                if (!t) continue;

                if (t.startsWith('# ')) {
                    children.push(new Paragraph({
                        text: t.replace('# ', ''),
                        heading: HeadingLevel.HEADING_1,
                        alignment: AlignmentType.CENTER,
                        children: [new TextRun({ bold: true, size: 32, font: 'Times New Roman' })]
                    }));
                } else if (t.startsWith('## ')) {
                    children.push(new Paragraph({
                        text: t.replace('## ', ''),
                        heading: HeadingLevel.HEADING_2,
                        children: [new TextRun({ bold: true, size: 26, font: 'Times New Roman' })]
                    }));
                } else if (t.match(/^[-•*]\s/)) {
                    children.push(new Paragraph({
                        children: [new TextRun({
                            text: '• ' + t.replace(/^[-•*]\s?/, ''),
                            size: 22,
                            font: 'Times New Roman'
                        })],
                        indent: { left: 720 }
                    }));
                } else {
                    children.push(new Paragraph({
                        children: [new TextRun({
                            text: t.replace(/\*\*/g, ''),
                            size: 22,
                            font: 'Times New Roman'
                        })],
                        indent: t.match(/^\d+\./) ? { firstLine: 720 } : undefined,
                        spacing: { after: 60 }
                    }));
                }
            }

            const doc = new Document({
                creator: 'AI Contract Builder PRO',
                title: 'Договор',
                sections: [{
                    properties: {
                        page: { margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 } }
                    },
                    children: [
                        new Paragraph({
                            text: 'ДОГОВОР',
                            heading: HeadingLevel.TITLE,
                            alignment: AlignmentType.CENTER,
                            children: [new TextRun({ bold: true, size: 36, font: 'Times New Roman' })]
                        }),
                        ...children
                    ]
                }]
            });

            const blob = await Packer.toBlob(doc);
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = `Договор_${new Date().toISOString().slice(0, 10)}.docx`;
            a.click();
            URL.revokeObjectURL(a.href);
            toast('📄 DOCX файл скачан', 'success');
        } catch (e) {
            toast(`❌ DOCX: ${e.message}`, 'error');
            console.error('DOCX export error:', e);
        }
    }

    async function exportToPdf() {
        if (!contractText) {
            toast('⚠️ Сначала сгенерируйте договор', 'warning');
            return;
        }

        toast('🔄 Создаю PDF...', 'warning');

        try {
            const { jsPDF } = window.jspdf;
            const el = document.getElementById('contractOutput');
            if (!el) throw new Error('Элемент не найден');

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

            pdf.save(`Договор_${new Date().toISOString().slice(0, 10)}.pdf`);
            toast('📕 PDF файл скачан', 'success');
        } catch (e) {
            toast('⚠️ PDF: используем печать', 'warning');
            setTimeout(() => window.print(), 400);
            console.error('PDF export error:', e);
        }
    }

    // ===== Utilities =====
    function startOver() {
        contractText = '';
        document.getElementById('result')?.classList.remove('active');

        document.querySelectorAll('input:not([type="checkbox"]), textarea, select').forEach(el => {
            if (el.id === 'f_date') el.valueAsDate = new Date();
            else if (el.id === 'f_city') el.value = 'г. Москва';
            else if (el.id === 'f_type') el.value = 'оказания услуг';
            else if (el.id === 'f_role1') el.value = 'Заказчик';
            else if (el.id === 'f_role2') el.value = 'Исполнитель';
            else if (el.id === 'f_vat') el.value = 'не указано';
            else if (el.id === 'f_dispute') el.value = 'арбитражный суд';
            else if (el.id === 'f_penalty') el.value = '0.1% в день';
            else if (el.id === 'f_conf') el.checked = false;
            else el.value = '';
        });

        document.getElementById('detectedInfo') && (document.getElementById('detectedInfo').innerHTML = '');
        goStep(1);
        toast('🔄 Начато заново', 'success');
    }

    function toast(msg, type = 'success') {
        const t = document.getElementById('toast');
        if (!t) return;

        t.textContent = msg;
        t.className = `toast ${type} show`;

        setTimeout(() => t.classList.remove('show'), 3000);
    }

    // ===== Initialization =====
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('useOllama')?.checked) {
            fetch(CONFIG.OLLAMA_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ model: 'llama3.1:8b', messages: [], stream: false })
            }).catch(() => { });
        }

        setTimeout(() => {
            if (window.marked) marked.setOptions({ breaks: true, gfm: true });
        }, 100);
    });

    // Enter key navigation
    document.querySelectorAll('input[type="text"], input[type="number"]').forEach(inp => {
        inp.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && currentStep < 4) {
                e.preventDefault();
                if (currentStep === 1) analyzeAndNext();
                else goStep(currentStep + 1);
            }
        });
    });

    // Warn before unload
    window.addEventListener('beforeunload', e => {
        if (contractText && !localStorage.getItem(CONFIG.HISTORY_KEY)) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Keyboard navigation for steps
    document.querySelectorAll('.step').forEach(step => {
        step.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const stepNum = step.id.replace('pstep', '');
                goStep(parseInt(stepNum));
            }
        });
    });
</script>
</body>
</html>
