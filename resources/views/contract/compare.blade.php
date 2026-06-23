<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LegalAI Compare — Premium Edition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0a0f;
            --bg-secondary: #13131a;
            --bg-tertiary: #1a1a24;
            --bg-elevated: #1f1f2e;
            --border: rgba(255, 255, 255, 0.08);
            --border-strong: rgba(255, 255, 255, 0.12);
            --border-hover: rgba(139, 92, 246, 0.3);
            --text-primary: #fafafa;
            --text-secondary: #a1a1aa;
            --text-tertiary: #71717a;
            --text-muted: #52525b;
            --accent-primary: #8b5cf6;
            --accent-secondary: #ec4899;
            --accent-tertiary: #06b6d4;
            --accent-success: #10b981;
            --accent-warning: #f59e0b;
            --accent-error: #ef4444;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.3);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.4);
            --shadow-lg: 0 12px 40px rgba(0,0,0,0.5);
            --shadow-xl: 0 24px 80px rgba(0,0,0,0.6);
            --shadow-glow: 0 0 40px rgba(139, 92, 246, 0.3);
        }

        /* ============================================ */
        /* LIGHT THEME VARIABLES                        */
        /* ============================================ */
        .light-theme {
            --bg-primary: #f4f6f8;
            --bg-secondary: #ffffff;
            --bg-tertiary: #eef2f6;
            --bg-elevated: #e2e8f0;
            --border: rgba(15, 23, 42, 0.08);
            --border-strong: rgba(15, 23, 42, 0.15);
            --border-hover: rgba(124, 58, 237, 0.4);
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-tertiary: #64748b;
            --text-muted: #94a3b8;
            --accent-primary: #7c3aed;
            --accent-secondary: #db2777;
            --accent-tertiary: #0891b2;
            --accent-success: #059669;
            --accent-warning: #d97706;
            --accent-error: #dc2626;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.025);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.02);
            --shadow-glow: 0 0 30px rgba(124, 58, 237, 0.15);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-feature-settings: "cv02", "cv03", "cv04", "cv11", "ss01";
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: var(--bg-primary);
            color: var(--text-primary);
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* ============================================ */
        /* LIGHT THEME OVERRIDES                        */
        /* ============================================ */
        .light-theme .glass {
            background: rgba(255, 255, 255, 0.75);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }
        .light-theme .glass-strong {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        }
        .light-theme .orb { opacity: 0.2; }
        .light-theme .mesh-bg::before { opacity: 0.6; }

        .light-theme .drop-zone {
            background: rgba(255, 255, 255, 0.6);
        }
        .light-theme .drop-zone:hover {
            background: rgba(255, 255, 255, 0.9);
        }
        .light-theme .drop-zone.has-file {
            background: rgba(16, 185, 129, 0.08);
        }

        .light-theme .diff-insert {
            background: rgba(16, 185, 129, 0.15);
            color: #065f46;
        }
        .light-theme .diff-delete {
            background: rgba(239, 68, 68, 0.15);
            color: #991b1b;
        }
        .light-theme .diff-modify {
            background: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }
        .light-theme .diff-pane-header {
            background: rgba(255, 255, 255, 0.9);
        }
        .light-theme .streaming-preview {
            background: #f8fafc;
            color: #334155;
        }
        .light-theme .heatmap-cell.empty {
            background: #e2e8f0;
            opacity: 0.6;
        }
        .light-theme .modal-overlay {
            background: rgba(15, 23, 42, 0.4);
        }

        /* ============================================ */
        /* ANIMATED MESH GRADIENT BACKGROUND            */
        /* ============================================ */
        .mesh-bg {
            position: fixed;
            inset: 0;
            z-index: -2;
            overflow: hidden;
            background: var(--bg-primary);
            transition: background-color 0.3s ease;
        }
        .mesh-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(at 20% 30%, rgba(139, 92, 246, 0.18) 0px, transparent 50%),
                radial-gradient(at 80% 20%, rgba(236, 72, 153, 0.12) 0px, transparent 50%),
                radial-gradient(at 70% 80%, rgba(6, 182, 212, 0.10) 0px, transparent 50%),
                radial-gradient(at 30% 70%, rgba(139, 92, 246, 0.08) 0px, transparent 50%);
            animation: meshMove 25s ease-in-out infinite;
        }
        @keyframes meshMove {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(40px, -30px) scale(1.08); }
            50% { transform: translate(-30px, 40px) scale(0.95); }
            75% { transform: translate(20px, 20px) scale(1.03); }
        }
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.5;
            pointer-events: none;
            z-index: -1;
            animation: float 30s ease-in-out infinite;
        }
        .orb-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.4), transparent);
            top: -150px;
            left: -150px;
        }
        .orb-2 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.3), transparent);
            bottom: -200px;
            right: -200px;
            animation-delay: -10s;
        }
        .orb-3 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.35), transparent);
            top: 40%;
            right: 20%;
            animation-delay: -20s;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(60px, -40px) scale(1.15); }
            66% { transform: translate(-40px, 60px) scale(0.9); }
        }
        /* ============================================ */
        /* GLASSMORPHISM CARDS                          */
        /* ============================================ */
        .glass {
            background: rgba(19, 19, 26, 0.6);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }
        .glass-strong {
            background: rgba(19, 19, 26, 0.85);
            backdrop-filter: blur(32px) saturate(200%);
            -webkit-backdrop-filter: blur(32px) saturate(200%);
            border: 1px solid var(--border-strong);
            box-shadow: var(--shadow-lg);
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }
        /* ============================================ */
        /* DROP ZONE                                    */
        /* ============================================ */
        .drop-zone {
            position: relative;
            border: 2px dashed var(--border-strong);
            border-radius: 24px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: rgba(19, 19, 26, 0.4);
            backdrop-filter: blur(16px);
            cursor: pointer;
            overflow: hidden;
        }
        .drop-zone::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
            rgba(139, 92, 246, 0.05),
            rgba(236, 72, 153, 0.03));
            opacity: 0;
            transition: opacity 0.3s;
        }
        .drop-zone:hover {
            border-color: var(--accent-primary);
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(139, 92, 246, 0.2);
        }
        .drop-zone:hover::before { opacity: 1; }
        .drop-zone.dragover {
            border-color: var(--accent-primary);
            border-style: solid;
            transform: scale(1.02);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.2), 0 20px 60px rgba(139, 92, 246, 0.3);
        }
        .drop-zone.has-file {
            border-style: solid;
            border-color: var(--accent-success);
            background: rgba(16, 185, 129, 0.05);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }
        /* ============================================ */
        /* SCORE RING WITH GLOW                         */
        /* ============================================ */
        .score-ring {
            position: relative;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: conic-gradient(
                var(--ring-color, var(--accent-primary)) calc(var(--score, 0) * 1%),
                var(--bg-tertiary) 0
            );
            transition: all 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow:
                0 0 0 12px var(--bg-secondary),
                0 0 60px color-mix(in srgb, var(--ring-color, var(--accent-primary)) 40%, transparent);
        }
        .score-ring::before {
            content: '';
            position: absolute;
            inset: 16px;
            border-radius: 50%;
            background: var(--bg-secondary);
            transition: background 0.3s ease;
        }
        .score-ring-inner {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }
        /* ============================================ */
        /* BENTO GRID                                   */
        /* ============================================ */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
        }
        .bento-item {
            border-radius: 24px;
            padding: 28px;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), background 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .bento-item::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
            rgba(139, 92, 246, 0.03),
            rgba(236, 72, 153, 0.02));
            opacity: 0;
            transition: opacity 0.3s;
        }
        .bento-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            border-color: var(--border-hover);
        }
        .bento-item:hover::before { opacity: 1; }
        /* ============================================ */
        /* STAT NUMBERS                                 */
        /* ============================================ */
        .stat-number {
            font-size: 3rem;
            font-weight: 900;
            letter-spacing: -0.05em;
            line-height: 1;
            background: linear-gradient(135deg,
            var(--text-primary),
            color-mix(in srgb, var(--accent-primary) 70%, var(--text-primary)));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        /* ============================================ */
        /* FINDING CARDS                                */
        /* ============================================ */
        .finding-card {
            position: relative;
            padding: 24px 28px;
            border-radius: 20px;
            margin-bottom: 16px;
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), background 0.3s ease;
            overflow: hidden;
        }
        .finding-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--severity-color, var(--accent-primary));
            border-radius: 4px 0 0 4px;
        }
        .finding-card:hover {
            transform: translateX(6px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
            border-color: var(--border-hover);
        }
        .finding-card.critical { --severity-color: var(--accent-error); }
        .finding-card.warning  { --severity-color: var(--accent-warning); }
        .finding-card.info     { --severity-color: var(--accent-primary); }
        /* ============================================ */
        /* DIFF CONTAINER                               */
        /* ============================================ */
        .diff-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            line-height: 1.8;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            transition: background 0.3s ease;
        }
        @media (max-width: 900px) {
            .diff-container { grid-template-columns: 1fr; }
        }
        .diff-pane {
            background: var(--bg-secondary);
            padding: 28px;
            min-height: 400px;
            max-height: 700px;
            overflow-y: auto;
            border-right: 1px solid var(--border);
            transition: background 0.3s ease;
        }
        .diff-pane:last-child { border-right: none; }
        .diff-pane-header {
            position: sticky;
            top: 0;
            padding: 16px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-secondary);
            z-index: 5;
            font-family: 'Inter', sans-serif;
            backdrop-filter: blur(12px);
            transition: background 0.3s ease;
        }
        .diff-equal { color: var(--text-secondary); }
        .diff-insert {
            background: rgba(16, 185, 129, 0.15);
            padding: 2px 8px;
            border-radius: 6px;
            border-bottom: 2px solid var(--accent-success);
            color: var(--text-primary);
        }
        .diff-delete {
            background: rgba(239, 68, 68, 0.15);
            padding: 2px 8px;
            border-radius: 6px;
            text-decoration: line-through;
            border-bottom: 2px solid var(--accent-error);
            color: var(--text-secondary);
        }
        .diff-modify {
            background: rgba(245, 158, 11, 0.15);
            padding: 2px 8px;
            border-radius: 6px;
            border-bottom: 2px solid var(--accent-warning);
            color: var(--text-primary);
        }
        /* ============================================ */
        /* PROGRESS BAR                                 */
        /* ============================================ */
        .progress-bar {
            height: 8px;
            background: var(--bg-tertiary);
            border-radius: 999px;
            overflow: hidden;
            position: relative;
            transition: background 0.3s ease;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg,
            var(--accent-primary),
            var(--accent-secondary),
            var(--accent-tertiary));
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
            border-radius: 999px;
            transition: width 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
        }
        @keyframes shimmer {
            0% { background-position: 0% 0; }
            100% { background-position: 200% 0; }
        }
        /* ============================================ */
        /* BUTTONS                                      */
        /* ============================================ */
        .btn-primary {
            background: linear-gradient(135deg,
            var(--accent-primary),
            var(--accent-secondary));
            color: white;
            font-weight: 700;
            border-radius: 16px;
            padding: 16px 32px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow:
                0 8px 24px rgba(139, 92, 246, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
            rgba(255, 255, 255, 0.2),
            transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .btn-primary:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow:
                0 12px 40px rgba(139, 92, 246, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        .btn-primary:hover::before { opacity: 1; }
        .btn-primary:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-cancel {
            background: linear-gradient(135deg,
            var(--accent-error),
            #dc2626);
            color: white;
            font-weight: 700;
            border-radius: 16px;
            padding: 16px 32px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 8px 24px rgba(239, 68, 68, 0.4);
        }
        .btn-cancel:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(239, 68, 68, 0.6);
        }
        /* ============================================ */
        /* TOAST                                        */
        /* ============================================ */
        .toast {
            transform: translateX(120%);
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            border-radius: 16px;
            backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-xl);
        }
        .toast.show { transform: translateX(0); }
        /* ============================================ */
        /* ANIMATIONS                                   */
        /* ============================================ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-in {
            animation: fadeInUp 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) backwards;
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        @keyframes pulse-dot {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.5;
                transform: scale(1.3);
            }
        }
        .pulse-dot {
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        .slide-in-right {
            animation: slideInRight 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) backwards;
        }
        .ai-thinking {
            display: inline-flex;
            gap: 8px;
            align-items: center;
        }
        .ai-thinking span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: linear-gradient(135deg,
            var(--accent-primary),
            var(--accent-secondary));
            animation: aiPulse 1.4s ease-in-out infinite;
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
        }
        .ai-thinking span:nth-child(2) { animation-delay: 0.2s; }
        .ai-thinking span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes aiPulse {
            0%, 100% {
                transform: scale(0.6);
                opacity: 0.4;
            }
            50% {
                transform: scale(1);
                opacity: 1;
            }
        }
        /* ============================================ */
        /* RECOMMENDATION CARDS                         */
        /* ============================================ */
        .rec-card {
            padding: 24px 28px;
            border-radius: 20px;
            margin-bottom: 16px;
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), background 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .rec-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--priority-color, var(--accent-primary));
            border-radius: 4px 0 0 4px;
        }
        .rec-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
            border-color: var(--border-hover);
        }
        .rec-card.high   {
            --priority-color: var(--accent-error);
            background: rgba(239, 68, 68, 0.03);
        }
        .rec-card.medium {
            --priority-color: var(--accent-warning);
            background: rgba(245, 158, 11, 0.03);
        }
        .rec-card.low    {
            --priority-color: var(--accent-primary);
            background: rgba(139, 92, 246, 0.03);
        }
        /* ============================================ */
        /* STATUS BADGES                                */
        /* ============================================ */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            backdrop-filter: blur(12px);
            transition: background 0.3s ease;
        }
        .status-badge .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 12px currentColor;
        }
        .status-badge.connected {
            color: var(--accent-success);
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
        }
        .status-badge.disconnected {
            color: var(--accent-error);
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.3);
        }
        .status-badge.checking {
            color: var(--accent-warning);
        }
        /* ============================================ */
        /* FORMAT BADGES                                */
        /* ============================================ */
        .format-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .format-badge.pdf  {
            background: rgba(239, 68, 68, 0.15);
            color: var(--accent-error);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .format-badge.docx {
            background: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        .format-badge.txt  {
            background: rgba(139, 92, 246, 0.15);
            color: var(--accent-primary);
            border: 1px solid rgba(139, 92, 246, 0.3);
        }
        /* ============================================ */
        /* SCROLLBAR                                    */
        /* ============================================ */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg-tertiary);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--border-strong);
            border-radius: 8px;
            border: 2px solid var(--bg-tertiary);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-primary);
        }
        /* ============================================ */
        /* SECTION TITLE                                */
        /* ============================================ */
        .section-title {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }
        .section-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg,
            var(--accent-primary),
            var(--accent-secondary));
            color: white;
            font-size: 20px;
            box-shadow: 0 8px 24px rgba(139, 92, 246, 0.4);
        }
        /* ============================================ */
        /* LEGEND                                       */
        /* ============================================ */
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            transition: background 0.3s ease;
        }
        .legend-dot {
            width: 10px;
            height: 4px;
            border-radius: 3px;
        }
        /* ============================================ */
        /* HISTORY ITEMS                                */
        /* ============================================ */
        .history-item {
            padding: 16px 20px;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: var(--bg-secondary);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), background 0.3s ease;
        }
        .history-item:hover {
            border-color: var(--border-hover);
            transform: translateX(6px);
            box-shadow: 0 8px 24px rgba(139, 92, 246, 0.2);
        }
        /* ============================================ */
        /* REPORT HEADER                                */
        /* ============================================ */
        .report-header {
            position: relative;
            padding: 48px;
            border-radius: 28px;
            overflow: hidden;
            color: white;
            background: linear-gradient(135deg,
            var(--accent-primary),
            var(--accent-secondary),
            var(--accent-tertiary));
            box-shadow:
                0 24px 80px rgba(139, 92, 246, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        .report-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(255,255,255,0.2), transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255,255,255,0.15), transparent 50%);
        }
        .report-header-pattern {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.08) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        /* ============================================ */
        /* BADGES                                       */
        /* ============================================ */
        .priority-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .priority-badge.high   {
            background: rgba(239, 68, 68, 0.2);
            color: var(--accent-error);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .priority-badge.medium {
            background: rgba(245, 158, 11, 0.2);
            color: var(--accent-warning);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .priority-badge.low    {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        .severity-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .severity-badge.critical {
            background: rgba(239, 68, 68, 0.2);
            color: var(--accent-error);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .severity-badge.warning  {
            background: rgba(245, 158, 11, 0.2);
            color: var(--accent-warning);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .severity-badge.info     {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        /* ============================================ */
        /* COMPARE BOXES                                */
        /* ============================================ */
        .compare-box {
            padding: 16px 20px;
            border-radius: 14px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            transition: background 0.3s ease;
        }
        .compare-box.a {
            border-left: 4px solid #3b82f6;
        }
        .compare-box.b {
            border-left: 4px solid var(--accent-secondary);
        }
        /* ============================================ */
        /* RISK & RECOMMENDATION BOXES                  */
        /* ============================================ */
        .risk-box {
            padding: 16px 20px;
            border-radius: 14px;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .recommendation-box {
            padding: 16px 20px;
            border-radius: 14px;
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        /* ============================================ */
        /* PRINT STYLES                                 */
        /* ============================================ */
        @media print {
            .no-print { display: none !important; }
            body {
                background: white !important;
                color: black !important;
            }
            .mesh-bg, .orb { display: none; }
            .glass, .glass-strong {
                background: white !important;
                backdrop-filter: none !important;
                border: 1px solid #ddd !important;
            }
        }
        /* ============================================ */
        /* MODAL                                        */
        /* ============================================ */
        .modal-overlay {
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(12px);
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .modal-content {
            transform: scale(0.9) translateY(30px);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-overlay.active .modal-content {
            transform: scale(1) translateY(0);
        }
        /* ============================================ */
        /* HEATMAP                                      */
        /* ============================================ */
        .heatmap-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 6px;
        }
        .heatmap-cell {
            aspect-ratio: 1;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .heatmap-cell:hover {
            transform: scale(1.3);
            z-index: 10;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }
        .heatmap-cell.critical {
            background: var(--accent-error);
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
        }
        .heatmap-cell.warning  {
            background: var(--accent-warning);
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.5);
        }
        .heatmap-cell.info     {
            background: var(--accent-primary);
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
        }
        .heatmap-cell.empty    {
            background: var(--bg-tertiary);
            opacity: 0.3;
        }
        /* ============================================ */
        /* STREAMING STATS                              */
        /* ============================================ */
        .progress-timer {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            color: var(--text-secondary);
        }
        .streaming-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 20px;
        }
        .stat-chip {
            padding: 16px 20px;
            border-radius: 16px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            text-align: center;
            backdrop-filter: blur(12px);
            transition: background 0.3s ease;
        }
        .stat-chip-value {
            font-size: 28px;
            font-weight: 900;
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg,
            var(--accent-primary),
            var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-chip-label {
            font-size: 11px;
            color: var(--text-tertiary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 6px;
            font-weight: 600;
        }
        .streaming-preview {
            margin-top: 20px;
            padding: 20px;
            border-radius: 16px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            max-height: 220px;
            overflow-y: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text-secondary);
            white-space: pre-wrap;
            word-break: break-word;
            backdrop-filter: blur(12px);
            transition: background 0.3s ease, color 0.3s ease;
        }
        .streaming-preview::after {
            content: '▊';
            animation: blink 1s infinite;
            color: var(--accent-primary);
        }
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
        /* ============================================ */
        /* CACHE BADGE                                  */
        /* ============================================ */
        .cache-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            background: rgba(16, 185, 129, 0.15);
            color: var(--accent-success);
            border: 1px solid rgba(16, 185, 129, 0.3);
            font-family: 'JetBrains Mono', monospace;
        }
        .cache-badge .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 12px currentColor;
        }
    </style>
</head>
<body>
<div class="mesh-bg"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>
<div class="min-h-screen">
    <!-- HEADER -->
    <header class="sticky top-0 z-40 glass-strong border-b" style="border-color: var(--border);">
        <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); box-shadow: 0 8px 24px rgba(139, 92, 246, 0.5);">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight">LegalAI Compare</h1>
                    <p class="text-xs font-medium" style="color: var(--text-tertiary);">Premium · Streaming · llama3.1:8b</p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div id="ollamaStatusBadge" class="status-badge checking">
                    <span class="dot pulse-dot"></span>
                    <span>Проверка Ollama...</span>
                </div>
                <div id="cacheStatsBadge" class="status-badge" style="color: var(--accent-success); background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3);">
                    <span class="dot"></span>
                    <span id="cacheStatsText">Кэш: 0</span>
                </div>
                <button onclick="toggleTheme()" id="themeToggle" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg transition" style="background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-primary);" title="Сменить тему">
                    🌙
                </button>
                <button onclick="showHistory()" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition" style="background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-primary);">
                    📚 История
                </button>
                <button onclick="showCacheManager()" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition" style="background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-primary);">
                    💾 Кэш
                </button>
            </div>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-6 py-12">
        <!-- UPLOAD SECTION -->
        <section id="uploadSection" class="mb-12 animate-in">
            <div class="mb-10">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full text-xs font-bold mb-5" style="background: rgba(139, 92, 246, 0.15); color: var(--accent-primary); border: 1px solid rgba(139, 92, 246, 0.3);">
                    <span class="w-2 h-2 rounded-full pulse-dot" style="background: var(--accent-primary);"></span>
                    AI-POWERED · STREAMING · CACHED
                </div>
                <h2 class="text-5xl font-black tracking-tight mb-4" style="letter-spacing: -0.04em; line-height: 1.1;">
                    Сравнение договоров<br>
                    <span style="background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary), var(--accent-tertiary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">с точностью юриста</span>
                </h2>
                <p class="text-xl font-medium" style="color: var(--text-secondary); max-width: 700px; line-height: 1.6;">
                    Загрузите два документа — получите профессиональный отчёт. <strong style="color: var(--accent-primary);">Повторные анализы мгновенные</strong> благодаря умному кэшированию.
                </p>
            </div>
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="flex items-center gap-3 mb-4">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black text-white" style="background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);">A</span>
                        <span class="font-bold text-lg">Базовый договор (эталон)</span>
                    </label>
                    <div id="dropZoneA" class="drop-zone p-10 min-h-[260px] flex flex-col items-center justify-center">
                        <input type="file" id="fileInputA" class="sr-only" accept=".pdf,.docx,.doc,.txt">
                        <div id="dropContentA" class="text-center space-y-5 relative z-10">
                            <div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(236, 72, 153, 0.15)); box-shadow: 0 8px 24px rgba(139, 92, 246, 0.3);">
                                <svg class="w-10 h-10" style="color: var(--accent-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-lg mb-2">Перетащите файл</p>
                                <p class="text-sm" style="color: var(--text-tertiary);">или нажмите для выбора</p>
                            </div>
                            <p class="text-xs" style="color: var(--text-tertiary);">
                                <span class="format-badge pdf">PDF</span>
                                <span class="format-badge docx">DOCX</span>
                                <span class="format-badge txt">TXT</span>
                            </p>
                        </div>
                        <div id="fileSelectedA" class="hidden w-full relative z-10">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgba(16, 185, 129, 0.2); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);">
                                    <svg class="w-7 h-7" style="color: var(--accent-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p id="fileNameA" class="font-bold text-lg truncate"></p>
                                    <p id="fileSizeA" class="text-sm" style="color: var(--text-tertiary);"></p>
                                    <p id="fileWordsA" class="text-sm font-semibold" style="color: var(--accent-primary);"></p>
                                </div>
                                <button onclick="event.stopPropagation();clearFile('A')" class="p-3 rounded-xl transition" style="color: var(--text-tertiary);">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-3 mb-4">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black text-white" style="background: linear-gradient(135deg, var(--accent-secondary), #be185d); box-shadow: 0 8px 20px rgba(236, 72, 153, 0.4);">B</span>
                        <span class="font-bold text-lg">Сравниваемый договор</span>
                    </label>
                    <div id="dropZoneB" class="drop-zone p-10 min-h-[260px] flex flex-col items-center justify-center">
                        <input type="file" id="fileInputB" class="sr-only" accept=".pdf,.docx,.doc,.txt">
                        <div id="dropContentB" class="text-center space-y-5 relative z-10">
                            <div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(236, 72, 153, 0.2), rgba(139, 92, 246, 0.15)); box-shadow: 0 8px 24px rgba(236, 72, 153, 0.3);">
                                <svg class="w-10 h-10" style="color: var(--accent-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-lg mb-2">Перетащите файл</p>
                                <p class="text-sm" style="color: var(--text-tertiary);">или нажмите для выбора</p>
                            </div>
                            <p class="text-xs" style="color: var(--text-tertiary);">
                                <span class="format-badge pdf">PDF</span>
                                <span class="format-badge docx">DOCX</span>
                                <span class="format-badge txt">TXT</span>
                            </p>
                        </div>
                        <div id="fileSelectedB" class="hidden w-full relative z-10">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgba(16, 185, 129, 0.2); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);">
                                    <svg class="w-7 h-7" style="color: var(--accent-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p id="fileNameB" class="font-bold text-lg truncate"></p>
                                    <p id="fileSizeB" class="text-sm" style="color: var(--text-tertiary);"></p>
                                    <p id="fileWordsB" class="text-sm font-semibold" style="color: var(--accent-secondary);"></p>
                                </div>
                                <button onclick="event.stopPropagation();clearFile('B')" class="p-3 rounded-xl transition" style="color: var(--text-tertiary);">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="glass-strong rounded-2xl p-7 mb-8">
                <div class="flex items-center justify-between flex-wrap gap-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); box-shadow: 0 8px 24px rgba(139, 92, 246, 0.4);">
                            <span class="text-2xl">🤖</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">AI-анализ через Ollama</h3>
                            <p class="text-sm" style="color: var(--text-tertiary);">Streaming · Полный промпт · Кэш</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <input type="text" id="ollamaUrl" value="http://localhost:11434" class="px-4 py-2.5 rounded-xl text-sm w-52" style="background: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary);" placeholder="URL Ollama">
                        <select id="ollamaModel" class="px-4 py-2.5 rounded-xl text-sm" style="background: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary);">
                            <option value="llama3.1:8b" selected>🦙 llama3.1:8b</option>
                            <option value="llama3.2:3b">⚡ llama3.2:3b (быстрее)</option>
                            <option value="phi3:mini">🚀 phi3:mini (самая быстрая)</option>
                        </select>
                        <button onclick="checkOllamaConnection()" class="px-4 py-2.5 rounded-xl text-sm font-semibold transition" style="background: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary);">
                            🔄 Проверить
                        </button>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="useAI" checked class="w-5 h-5" style="accent-color: var(--accent-primary);">
                            <span class="text-sm font-semibold">AI-анализ</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="useCache" checked class="w-5 h-5" style="accent-color: var(--accent-primary);">
                            <span class="text-sm font-semibold">Кэш</span>
                        </label>
                    </div>
                </div>
                <div id="ollamaInfoBox" class="hidden mt-5 p-4 rounded-xl text-sm" style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.3); color: var(--text-secondary);"></div>
            </div>
            <div class="flex justify-center">
                <button id="compareBtn" onclick="startComparison()" disabled class="btn-primary flex items-center gap-3 text-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Сравнить договоры</span>
                    <span class="text-sm opacity-70">⌘ + Enter</span>
                </button>
            </div>
        </section>
        <!-- PROGRESS SECTION -->
        <section id="progressSection" class="hidden mb-12 animate-in">
            <div class="glass-strong rounded-2xl p-10">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="ai-thinking"><span></span><span></span><span></span></div>
                        <span class="font-bold text-xl" id="progressLabel">Анализ документов...</span>
                    </div>
                    <div class="flex items-center gap-5">
                        <span class="progress-timer font-bold" id="progressTimer">00:00</span>
                        <span class="text-3xl font-black tabular-nums" style="background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;" id="progressPercent">0%</span>
                    </div>
                </div>
                <div class="progress-bar mb-6">
                    <div id="progressBar" class="progress-fill" style="width: 0%;"></div>
                </div>
                <div class="grid grid-cols-4 gap-4 text-sm mb-5" style="color: var(--text-tertiary);">
                    <div class="flex items-center gap-2"><span id="stage-read" class="w-3 h-3 rounded-full" style="background: var(--accent-primary);"></span>Чтение</div>
                    <div class="flex items-center gap-2"><span id="stage-diff" class="w-3 h-3 rounded-full" style="background: var(--bg-tertiary);"></span>Diff</div>
                    <div class="flex items-center gap-2"><span id="stage-ai" class="w-3 h-3 rounded-full" style="background: var(--bg-tertiary);"></span>AI-анализ</div>
                    <div class="flex items-center gap-2"><span id="stage-report" class="w-3 h-3 rounded-full" style="background: var(--bg-tertiary);"></span>Отчёт</div>
                </div>
                <div id="streamingStats" class="streaming-stats hidden">
                    <div class="stat-chip">
                        <div class="stat-chip-value" id="tokenCount">0</div>
                        <div class="stat-chip-label">Токенов</div>
                    </div>
                    <div class="stat-chip">
                        <div class="stat-chip-value" id="tokenSpeed">0</div>
                        <div class="stat-chip-label">Ток/сек</div>
                    </div>
                    <div class="stat-chip">
                        <div class="stat-chip-value" id="etaTime">--:--</div>
                        <div class="stat-chip-label">Осталось</div>
                    </div>
                </div>
                <div id="streamingPreview" class="streaming-preview hidden"></div>
                <div id="aiProgressDetails" class="hidden text-sm p-4 rounded-xl mt-5" style="background: var(--bg-tertiary); color: var(--text-secondary); font-family: 'JetBrains Mono', monospace;"></div>
                <div class="mt-5 flex justify-center">
                    <button id="cancelBtn" onclick="cancelAnalysis()" class="btn-cancel flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Отменить анализ</span>
                    </button>
                </div>
            </div>
        </section>
        <!-- RESULTS SECTION -->
        <section id="resultsSection" class="hidden space-y-10 animate-in">
            <div id="reportPrintArea" class="space-y-10">
                <div class="report-header animate-in">
                    <div class="report-header-pattern"></div>
                    <div class="relative z-10 flex items-start justify-between flex-wrap gap-8">
                        <div>
                            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full text-xs font-bold mb-5" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3);">
                                <span class="w-2 h-2 rounded-full pulse-dot" style="background: white;"></span>
                                ОТЧЁТ О СРАВНЕНИИ
                                <span id="cacheIndicator" class="hidden" style="margin-left: 10px;">
<span class="cache-badge" style="background: rgba(255,255,255,0.9); color: var(--accent-success); border-color: rgba(16, 185, 129, 0.5);">
<span class="dot"></span>
<span>Из кэша</span>
</span>
</span>
                            </div>
                            <h2 class="text-5xl font-black mb-4 tracking-tight" style="letter-spacing: -0.04em; line-height: 1.1;">
                                Сравнительный анализ<br>договоров
                            </h2>
                            <p class="text-lg font-medium text-white/90" id="reportSubtitle">Профессиональный юридический анализ</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-mono text-white/70 mb-2" id="reportNumber">CMP-000000</div>
                            <div class="text-base font-semibold text-white/95 mb-2" id="reportDate"></div>
                            <div class="text-sm font-medium text-white/80" id="reportModel"></div>
                        </div>
                    </div>
                </div>
                <div class="bento-grid">
                    <div class="bento-item animate-in delay-1" style="grid-column: span 5;">
                        <div class="flex items-center gap-8 flex-wrap">
                            <div class="score-ring" id="scoreRing" style="--score: 0; --ring-color: var(--accent-primary);">
                                <div class="score-ring-inner">
                                    <span id="scorePercent" class="text-6xl font-black tabular-nums" style="letter-spacing: -0.05em;">0%</span>
                                    <span class="text-sm font-bold uppercase tracking-wider mt-2" style="color: var(--text-tertiary);">Совместимость</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-[220px]">
                                <div id="scoreLabel" class="text-4xl font-black mb-3 tracking-tight">—</div>
                                <p class="text-base" style="color: var(--text-secondary);">Индекс сходства документов</p>
                            </div>
                        </div>
                    </div>
                    <div class="bento-item animate-in delay-2" style="grid-column: span 3;">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-bold uppercase tracking-wider" style="color: var(--text-tertiary);">Критические</span>
                            <span class="text-2xl">🚨</span>
                        </div>
                        <div class="stat-number" id="kpiCritical">0</div>
                        <p class="text-sm mt-3" style="color: var(--text-tertiary);">Юридических конфликтов</p>
                    </div>
                    <div class="bento-item animate-in delay-3" style="grid-column: span 2;">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-bold uppercase tracking-wider" style="color: var(--text-tertiary);">Внимание</span>
                            <span class="text-2xl">⚠️</span>
                        </div>
                        <div class="stat-number" id="kpiWarning">0</div>
                        <p class="text-sm mt-3" style="color: var(--text-tertiary);">Требуют проверки</p>
                    </div>
                    <div class="bento-item animate-in delay-4" style="grid-column: span 2;">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-bold uppercase tracking-wider" style="color: var(--text-tertiary);">Информация</span>
                            <span class="text-2xl">💡</span>
                        </div>
                        <div class="stat-number" id="kpiInfo">0</div>
                        <p class="text-sm mt-3" style="color: var(--text-tertiary);">Незначительных</p>
                    </div>
                    <div class="bento-item animate-in delay-1" style="grid-column: span 6;">
                        <div class="grid md:grid-cols-2 gap-5">
                            <div class="p-5 rounded-xl" style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black text-white" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">A</span>
                                    <span class="text-sm font-bold uppercase tracking-wider" style="color: var(--text-tertiary);">Базовый</span>
                                </div>
                                <p id="docAName" class="font-bold text-lg truncate">—</p>
                                <p id="docAInfo" class="text-sm mt-2" style="color: var(--text-tertiary);">—</p>
                            </div>
                            <div class="p-5 rounded-xl" style="background: var(--bg-tertiary); border: 1px solid var(--border);">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black text-white" style="background: linear-gradient(135deg, var(--accent-secondary), #be185d);">B</span>
                                    <span class="text-sm font-bold uppercase tracking-wider" style="color: var(--text-tertiary);">Сравниваемый</span>
                                </div>
                                <p id="docBName" class="font-bold text-lg truncate">—</p>
                                <p id="docBInfo" class="text-sm mt-2" style="color: var(--text-tertiary);">—</p>
                            </div>
                        </div>
                    </div>
                    <div class="bento-item animate-in delay-2" style="grid-column: span 6;">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h3 class="font-bold text-lg">Карта рисков</h3>
                                <p class="text-sm" style="color: var(--text-tertiary);">Распределение по категориям</p>
                            </div>
                            <div class="flex items-center gap-4 text-xs">
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded" style="background: var(--accent-error); box-shadow: 0 0 12px rgba(239, 68, 68, 0.5);"></span>Critical</div>
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded" style="background: var(--accent-warning); box-shadow: 0 0 12px rgba(245, 158, 11, 0.5);"></span>Warning</div>
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded" style="background: var(--accent-primary); box-shadow: 0 0 12px rgba(139, 92, 246, 0.5);"></span>Info</div>
                            </div>
                        </div>
                        <div id="heatmapGrid" class="heatmap-grid"></div>
                    </div>
                </div>
                <div class="glass-strong rounded-2xl p-10 animate-in">
                    <div class="section-title">
                        <div class="section-icon">
                            <span>📋</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black tracking-tight">Краткое резюме</h3>
                            <p class="text-base" style="color: var(--text-secondary);">Executive Summary</p>
                        </div>
                    </div>
                    <p id="executiveSummary" class="text-lg leading-relaxed" style="color: var(--text-primary);">Документы проанализированы.</p>
                </div>
                <div class="glass-strong rounded-2xl p-10 animate-in">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, var(--accent-error), var(--accent-warning));">
                            <span>🔍</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black tracking-tight">Выявленные различия</h3>
                            <p class="text-base" style="color: var(--text-secondary);">Юридический анализ по категориям</p>
                        </div>
                    </div>
                    <div id="findingsList"></div>
                </div>
                <div class="glass-strong rounded-2xl p-10 animate-in">
                    <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
                        <div class="section-title" style="margin-bottom: 0;">
                            <div class="section-icon" style="background: linear-gradient(135deg, #475569, #1e293b);">
                                <span>📄</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black tracking-tight">Построчное сравнение</h3>
                                <p class="text-base" style="color: var(--text-secondary);">Визуализация изменений</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <div class="legend-item"><div class="legend-dot" style="background: var(--text-secondary);"></div>Совпадение</div>
                            <div class="legend-item"><div class="legend-dot" style="background: var(--accent-warning);"></div>Изменено</div>
                            <div class="legend-item"><div class="legend-dot" style="background: var(--accent-error);"></div>Удалено</div>
                            <div class="legend-item"><div class="legend-dot" style="background: var(--accent-success);"></div>Добавлено</div>
                        </div>
                    </div>
                    <div class="diff-container">
                        <div class="diff-pane">
                            <div class="diff-pane-header">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black text-white" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">A</span>
                                    <span class="font-bold text-base" id="diffTitleA">Документ A</span>
                                </div>
                            </div>
                            <div id="diffContentA" class="whitespace-pre-wrap"></div>
                        </div>
                        <div class="diff-pane">
                            <div class="diff-pane-header">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black text-white" style="background: linear-gradient(135deg, var(--accent-secondary), #be185d);">B</span>
                                    <span class="font-bold text-base" id="diffTitleB">Документ B</span>
                                </div>
                            </div>
                            <div id="diffContentB" class="whitespace-pre-wrap"></div>
                        </div>
                    </div>
                </div>
                <div class="glass-strong rounded-2xl p-10 animate-in">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, var(--accent-success), #059669);">
                            <span>💡</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black tracking-tight">Рекомендации</h3>
                            <p class="text-base" style="color: var(--text-secondary);">Приоритетные действия</p>
                        </div>
                    </div>
                    <div id="recommendationsList"></div>
                </div>
                <div class="rounded-2xl p-6 flex gap-4 animate-in" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3);">
                    <svg class="w-6 h-6 flex-shrink-0 mt-0.5" style="color: var(--accent-warning);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        <strong>Дисклеймер:</strong> Отчёт сгенерирован ИИ (llama3.1:8b). Для юридически значимых решений проконсультируйтесь с юристом.
                    </p>
                </div>
            </div>
            <div class="glass-strong rounded-2xl p-8 no-print animate-in">
                <div class="flex items-center justify-between mb-5 flex-wrap gap-4">
                    <div>
                        <h3 class="font-bold text-lg">Экспорт отчёта</h3>
                        <p class="text-sm" style="color: var(--text-tertiary);">Сохраните результат</p>
                    </div>
                    <button onclick="resetComparison()" class="px-5 py-2.5 rounded-xl text-sm font-bold transition" style="background: var(--text-primary); color: var(--bg-primary);">
                        🔄 Новое сравнение
                    </button>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button onclick="exportPDF()" class="px-5 py-3 rounded-xl text-sm font-bold flex items-center gap-2 transition" style="background: var(--accent-error); color: white; box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        PDF
                    </button>
                    <button onclick="exportMarkdown()" class="px-5 py-3 rounded-xl text-sm font-bold flex items-center gap-2 transition" style="background: #3b82f6; color: white; box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Markdown
                    </button>
                    <button onclick="exportJSON()" class="px-5 py-3 rounded-xl text-sm font-bold flex items-center gap-2 transition" style="background: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        JSON
                    </button>
                    <button onclick="window.print()" class="px-5 py-3 rounded-xl text-sm font-bold flex items-center gap-2 transition" style="background: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Печать
                    </button>
                </div>
            </div>
        </section>
    </main>
</div>
<div id="toastContainer" class="fixed bottom-6 right-6 z-50 space-y-3"></div>
<!-- HISTORY MODAL -->
<div id="historyModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-6" style="background: rgba(0,0,0,0.7);">
    <div class="modal-content glass-strong rounded-2xl max-w-2xl w-full max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border);">
            <h3 class="text-xl font-black">📚 История сравнений</h3>
            <button onclick="closeHistory()" class="p-3 rounded-xl transition" style="color: var(--text-tertiary);">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="historyList" class="p-6 overflow-y-auto flex-1 space-y-4"></div>
        <div class="p-5 border-t flex justify-between" style="border-color: var(--border);">
            <button onclick="clearHistory()" class="px-5 py-2.5 text-sm rounded-xl transition" style="color: var(--accent-error);">🗑️ Очистить всё</button>
            <button onclick="closeHistory()" class="px-5 py-2.5 text-sm rounded-xl" style="background: var(--bg-tertiary); color: var(--text-primary);">Закрыть</button>
        </div>
    </div>
</div>
<!-- CACHE MODAL -->
<div id="cacheModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-6" style="background: rgba(0,0,0,0.7);">
    <div class="modal-content glass-strong rounded-2xl max-w-2xl w-full max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between p-6 border-b" style="border-color: var(--border);">
            <h3 class="text-xl font-black">💾 Управление кэшем</h3>
            <button onclick="closeCacheManager()" class="p-3 rounded-xl transition" style="color: var(--text-tertiary);">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="cacheList" class="p-6 overflow-y-auto flex-1 space-y-4"></div>
        <div class="p-5 border-t flex justify-between items-center" style="border-color: var(--border);">
            <div class="text-sm" style="color: var(--text-secondary);">
                <strong id="cacheSizeText">0 записей</strong> · <span id="cacheBytesText">0 КБ</span>
            </div>
            <div class="flex gap-3">
                <button onclick="clearCache()" class="px-5 py-2.5 text-sm rounded-xl transition" style="color: var(--accent-error);">🗑️ Очистить кэш</button>
                <button onclick="closeCacheManager()" class="px-5 py-2.5 text-sm rounded-xl" style="background: var(--bg-tertiary); color: var(--text-primary);">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<script>
    // =================================================================
    // THEME TOGGLE
    // =================================================================
    function toggleTheme() {
        const html = document.documentElement;
        const isLight = html.classList.toggle('light-theme');
        localStorage.setItem('legalai_theme', isLight ? 'light' : 'dark');
        const btn = document.getElementById('themeToggle');
        if (btn) btn.textContent = isLight ? '☀️' : '🌙';
    }

    // =================================================================
    // STATE
    // =================================================================
    const state = {
        fileA: null, textA: '', nameA: '',
        fileB: null, textB: '', nameB: '',
        result: null,
        ollamaConnected: false,
        availableModels: [],
        currentAbortController: null,
        progressTimerInterval: null,
        progressStartTime: null
    };
    const HISTORY_KEY = 'legalai_compare_history_v3';
    const CACHE_KEY = 'legalai_compare_cache_v1';
    const CABINET_ACTIVITY_URL = '{{ route('cabinet.activity.store') }}';
    const MAX_CACHE_ENTRIES = 50;
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
    // =================================================================
    // CACHE MANAGER
    // =================================================================
    class CacheManager {
        static hash(text) {
            let hash = 0;
            for (let i = 0; i < text.length; i++) {
                const char = text.charCodeAt(i);
                hash = ((hash << 5) - hash) + char;
                hash = hash & hash;
            }
            return Math.abs(hash).toString(36);
        }
        static generateKey(textA, textB) {
            const hashA = this.hash(textA);
            const hashB = this.hash(textB);
            return `cmp_${hashA}_${hashB}`;
        }
        static get(textA, textB) {
            try {
                const cache = JSON.parse(localStorage.getItem(CACHE_KEY) || '{}');
                const key = this.generateKey(textA, textB);
                const entry = cache[key];
                if (!entry) return null;
                const age = Date.now() - entry.timestamp;
                if (age > 30 * 24 * 60 * 60 * 1000) {
                    delete cache[key];
                    localStorage.setItem(CACHE_KEY, JSON.stringify(cache));
                    return null;
                }
                entry.hits = (entry.hits || 0) + 1;
                entry.lastUsed = Date.now();
                cache[key] = entry;
                localStorage.setItem(CACHE_KEY, JSON.stringify(cache));
                return entry.result;
            } catch (e) {
                console.error('Cache get error:', e);
                return null;
            }
        }
        static set(textA, textB, result) {
            try {
                const cache = JSON.parse(localStorage.getItem(CACHE_KEY) || '{}');
                const key = this.generateKey(textA, textB);
                cache[key] = {
                    result: result,
                    timestamp: Date.now(),
                    lastUsed: Date.now(),
                    hits: 0,
                    nameA: state.nameA,
                    nameB: state.nameB
                };
                const keys = Object.keys(cache);
                if (keys.length > MAX_CACHE_ENTRIES) {
                    const sorted = keys.sort((a, b) => cache[a].lastUsed - cache[b].lastUsed);
                    const toRemove = sorted.slice(0, keys.length - MAX_CACHE_ENTRIES);
                    toRemove.forEach(k => delete cache[k]);
                }
                localStorage.setItem(CACHE_KEY, JSON.stringify(cache));
                updateCacheStats();
            } catch (e) {
                console.error('Cache set error:', e);
            }
        }
        static getAll() {
            try {
                const cache = JSON.parse(localStorage.getItem(CACHE_KEY) || '{}');
                return Object.entries(cache).map(([key, entry]) => ({
                    key,
                    ...entry
                })).sort((a, b) => b.lastUsed - a.lastUsed);
            } catch (e) {
                return [];
            }
        }
        static clear() {
            localStorage.removeItem(CACHE_KEY);
            updateCacheStats();
        }
        static remove(key) {
            try {
                const cache = JSON.parse(localStorage.getItem(CACHE_KEY) || '{}');
                delete cache[key];
                localStorage.setItem(CACHE_KEY, JSON.stringify(cache));
                updateCacheStats();
            } catch (e) {
                console.error('Cache remove error:', e);
            }
        }
        static getSize() {
            try {
                const cache = localStorage.getItem(CACHE_KEY) || '';
                return new Blob([cache]).size;
            } catch (e) {
                return 0;
            }
        }
    }
    function updateCacheStats() {
        const entries = CacheManager.getAll();
        const size = CacheManager.getSize();
        const sizeKB = (size / 1024).toFixed(1);
        document.getElementById('cacheStatsText').textContent = `Кэш: ${entries.length}`;
        document.getElementById('cacheSizeText').textContent = `${entries.length} записей`;
        document.getElementById('cacheBytesText').textContent = `${sizeKB} КБ`;
    }
    function showCacheManager() {
        const modal = document.getElementById('cacheModal');
        const list = document.getElementById('cacheList');
        const entries = CacheManager.getAll();
        if (entries.length === 0) {
            list.innerHTML = '<div class="text-center py-12" style="color: var(--text-tertiary);">💾 Кэш пуст</div>';
        } else {
            list.innerHTML = entries.map(entry => {
                const date = new Date(entry.timestamp).toLocaleString('ru-RU');
                const age = Math.floor((Date.now() - entry.timestamp) / (1000 * 60 * 60));
                const ageText = age < 24 ? `${age}ч назад` : `${Math.floor(age / 24)}д назад`;
                return `
<div class="history-item">
<div class="flex items-center justify-between mb-3">
<span class="text-sm" style="color: var(--text-tertiary);">${date}</span>
<div class="flex gap-3">
<span class="text-xs px-3 py-1 rounded-lg" style="background: rgba(16, 185, 129, 0.2); color: var(--accent-success); border: 1px solid rgba(16, 185, 129, 0.3);">${entry.hits || 0} использований</span>
<span class="text-xs" style="color: var(--text-tertiary);">${ageText}</span>
</div>
</div>
<div class="font-bold text-lg truncate">${escapeHtml(entry.nameA || 'Документ A')} ↔ ${escapeHtml(entry.nameB || 'Документ B')}</div>
<div class="text-sm mt-2" style="color: var(--text-tertiary);">
${entry.result?.findings?.length || 0} различий · ${entry.result?.recommendations?.length || 0} рекомендаций
</div>
<div class="mt-3 flex justify-end">
<button onclick="CacheManager.remove('${entry.key}');showCacheManager();" class="text-sm px-4 py-2 rounded-lg transition" style="color: var(--accent-error);">Удалить</button>
</div>
</div>
`;
            }).join('');
        }
        updateCacheStats();
        modal.classList.add('active');
    }
    function closeCacheManager() {
        document.getElementById('cacheModal').classList.remove('active');
    }
    function clearCache() {
        if (!confirm('Очистить весь кэш? Это действие нельзя отменить.')) return;
        CacheManager.clear();
        showCacheManager();
        showToast('Кэш очищен', 'success');
    }
    // =================================================================
    // TOAST
    // =================================================================
    function showToast(msg, type = 'info', duration = 4000) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        const styles = {
            info:    { bg: 'var(--bg-secondary)', text: 'var(--text-primary)', icon: 'ℹ️' },
            success: { bg: 'var(--accent-success)', text: 'white', icon: '✅' },
            error:   { bg: 'var(--accent-error)', text: 'white', icon: '❌' },
            warning: { bg: 'var(--accent-warning)', text: 'white', icon: '⚠️' }
        };
        const s = styles[type] || styles.info;
        toast.className = `toast flex items-center gap-4 px-6 py-5 min-w-[340px] max-w-md`;
        toast.style.background = s.bg;
        toast.style.color = s.text;
        toast.innerHTML = `
<span class="text-2xl">${s.icon}</span>
<span class="text-base font-semibold flex-1">${msg}</span>
<button onclick="this.parentElement.remove()" class="opacity-70 hover:opacity-100 text-xl leading-none">×</button>
`;
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('show'));
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, duration);
    }
    // =================================================================
    // OLLAMA CONNECTION
    // =================================================================
    async function checkOllamaConnection() {
        const url = document.getElementById('ollamaUrl').value.trim();
        const badge = document.getElementById('ollamaStatusBadge');
        const infoBox = document.getElementById('ollamaInfoBox');
        const modelSelect = document.getElementById('ollamaModel');
        badge.className = 'status-badge checking';
        badge.innerHTML = '<span class="dot pulse-dot"></span><span>Проверка Ollama...</span>';
        infoBox.classList.add('hidden');
        try {
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 8000);
            const response = await fetch(`${url}/api/tags`, { method: 'GET', signal: controller.signal });
            clearTimeout(timeout);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const data = await response.json();
            const models = data.models || [];
            if (models.length === 0) {
                state.ollamaConnected = false;
                badge.className = 'status-badge disconnected';
                badge.innerHTML = '<span class="dot"></span><span>Нет моделей</span>';
                infoBox.classList.remove('hidden');
                infoBox.innerHTML = `<strong>⚠️ Ollama запущена, но нет моделей.</strong><br><code class="px-3 py-1 rounded-lg text-xs" style="background: var(--bg-tertiary);">ollama pull llama3.1:8b</code>`;
                showToast('Нет моделей', 'warning');
                return false;
            }
            state.ollamaConnected = true;
            state.availableModels = models.map(m => m.name);
            const currentSelection = modelSelect.value;
            modelSelect.innerHTML = '';
            models.forEach(m => {
                const sizeGB = m.size ? ` (${(m.size / 1e9).toFixed(1)}GB)` : '';
                const option = document.createElement('option');
                option.value = m.name;
                option.textContent = `🦙 ${m.name}${sizeGB}`;
                if (m.name === 'llama3.1:8b' || m.name.startsWith('llama3.1')) option.selected = true;
                modelSelect.appendChild(option);
            });
            if (state.availableModels.includes(currentSelection)) modelSelect.value = currentSelection;
            badge.className = 'status-badge connected';
            badge.innerHTML = `<span class="dot pulse-dot"></span><span>Ollama · ${models.length}</span>`;
            infoBox.classList.remove('hidden');
            infoBox.innerHTML = `✅ <strong>Ollama подключена.</strong> ${models.length} моделей доступно.`;
            showToast(`Ollama подключена · ${models.length} моделей`, 'success');
            return true;
        } catch (err) {
            state.ollamaConnected = false;
            badge.className = 'status-badge disconnected';
            badge.innerHTML = '<span class="dot"></span><span>Ollama недоступна</span>';
            infoBox.classList.remove('hidden');
            infoBox.innerHTML = `<strong>❌ Не удалось подключиться.</strong><br>
<code class="px-3 py-1 rounded-lg text-xs" style="background: var(--bg-tertiary);">OLLAMA_ORIGINS="*" ollama serve</code>`;
            showToast('Ollama недоступна', 'error');
            return false;
        }
    }
    // =================================================================
    // PROGRESS TIMER
    // =================================================================
    function startProgressTimer() {
        state.progressStartTime = Date.now();
        const timerEl = document.getElementById('progressTimer');
        state.progressTimerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - state.progressStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            timerEl.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }, 1000);
    }
    function stopProgressTimer() {
        if (state.progressTimerInterval) {
            clearInterval(state.progressTimerInterval);
            state.progressTimerInterval = null;
        }
    }
    // =================================================================
    // STREAMING OLLAMA CALL
    // =================================================================
    async function callOllamaStreaming(systemPrompt, userPrompt) {
        const url = document.getElementById('ollamaUrl').value.trim();
        const model = document.getElementById('ollamaModel').value;
        const detailsEl = document.getElementById('aiProgressDetails');
        const streamingStats = document.getElementById('streamingStats');
        const streamingPreview = document.getElementById('streamingPreview');
        state.currentAbortController = new AbortController();
        const controller = state.currentAbortController;
        let fullContent = '';
        let tokenCount = 0;
        const startTime = Date.now();
        streamingStats.classList.remove('hidden');
        streamingPreview.classList.remove('hidden');
        detailsEl.classList.remove('hidden');
        detailsEl.textContent = `⏳ Подключение к ${model}...`;
        try {
            const response = await fetch(`${url}/api/chat`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    model: model,
                    messages: [
                        { role: 'system', content: systemPrompt },
                        { role: 'user', content: userPrompt }
                    ],
                    stream: true,
                    format: 'json',
                    options: {
                        temperature: 0.1,
                        top_p: 0.95,
                        num_predict: 4000,
                        num_ctx: 8192,
                        stop: ['\n']
                    }
                }),
                signal: controller.signal
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            if (!response.body) throw new Error('Streaming не поддерживается');
            detailsEl.textContent = `✅ Подключено. Получаю токены...`;
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
                    try {
                        const json = JSON.parse(line);
                        if (json.message?.content) {
                            fullContent += json.message.content;
                            tokenCount++;
                            if (tokenCount % 3 === 0) {
                                const elapsed = (Date.now() - startTime) / 1000;
                                const speed = tokenCount / elapsed;
                                document.getElementById('tokenCount').textContent = tokenCount;
                                document.getElementById('tokenSpeed').textContent = speed.toFixed(1);
                                const estimatedTotal = 4000;
                                const remainingTokens = estimatedTotal - tokenCount;
                                const etaSeconds = remainingTokens / speed;
                                if (etaSeconds > 0 && etaSeconds < 600) {
                                    const etaMin = Math.floor(etaSeconds / 60);
                                    const etaSec = Math.floor(etaSeconds % 60);
                                    document.getElementById('etaTime').textContent = `${etaMin}:${String(etaSec).padStart(2, '0')}`;
                                }
                                const progress = Math.min(90, Math.round((tokenCount / estimatedTotal) * 90));
                                document.getElementById('progressBar').style.width = progress + '%';
                                document.getElementById('progressPercent').textContent = progress + '%';
                                streamingPreview.textContent = fullContent.slice(-500);
                            }
                        }
                        if (json.done) {
                            const totalTime = (Date.now() - startTime) / 1000;
                            const finalSpeed = tokenCount / totalTime;
                            document.getElementById('tokenCount').textContent = tokenCount;
                            document.getElementById('tokenSpeed').textContent = finalSpeed.toFixed(1);
                            document.getElementById('etaTime').textContent = '0:00';
                            document.getElementById('progressBar').style.width = '95%';
                            document.getElementById('progressPercent').textContent = '95%';
                            detailsEl.textContent = `✅ Готово! ${tokenCount} токенов за ${Math.round(totalTime)}с (${finalSpeed.toFixed(1)} ток/с)`;
                            streamingPreview.textContent = fullContent;
                            break;
                        }
                    } catch (e) { continue; }
                }
            }
            if (!fullContent) throw new Error('Пустой ответ');
            return fullContent;
        } catch (err) {
            if (err.name === 'AbortError') {
                throw new Error('Анализ отменен пользователем');
            }
            if (err.message.includes('Failed to fetch')) {
                throw new Error('Ollama недоступна. Запустите: OLLAMA_ORIGINS="*" ollama serve');
            }
            throw err;
        } finally {
            state.currentAbortController = null;
        }
    }
    // =================================================================
    // CANCEL ANALYSIS
    // =================================================================
    function cancelAnalysis() {
        if (state.currentAbortController) {
            state.currentAbortController.abort();
            state.currentAbortController = null;
        }
        stopProgressTimer();
        document.getElementById('progressSection').classList.add('hidden');
        document.getElementById('uploadSection').classList.remove('hidden');
        document.getElementById('compareBtn').disabled = false;
        showToast('Анализ отменен', 'warning');
    }
    // =================================================================
    // PROFESSIONAL PROMPT
    // =================================================================
    function buildComparisonPrompt(textA, textB, nameA, nameB) {
        const maxLen = 5000;
        const tA = textA.length > maxLen ? textA.slice(0, maxLen) + '\n[...текст сокращён для экономии токенов...]' : textA;
        const tB = textB.length > maxLen ? textB.slice(0, maxLen) + '\n[...текст сокращён для экономии токенов...]' : textB;
        const systemPrompt = `# РОЛЬ
Ты — старший партнёр юридической фирмы Республики Таджикистан с 20-летним опытом в договорном и корпоративном праве. Ты специализируешься на сравнительном правовом анализе договоров и выявлении юридических рисков.
# ТВОЯ ЭКСПЕРТИЗА
- Гражданский кодекс Республики Таджикистан (части 1, 2, 3)
- Закон РТ «Об обществах с ограниченной ответственностью»
- Закон РТ «Об акционерных обществах»
- Закон РТ «О защите прав потребителей»
- Закон РТ «О контрактной системе закупок»
- Практика арбитражных судов РТ
# ПРИНЦИПЫ АНАЛИЗА
1. **Точность**: анализируй только реальные различия из текстов, не выдумывай
2. **Конкретность**: приводи точные цитаты из обоих документов в полях inDocA/inDocB
3. **Практичность**: рекомендации должны быть применимы в реальных переговорах
4. **Сдержанность**: если не уверен в ссылке на статью — не ссылайся, лучше пропусти
5. **Приоритизация**: critical > warning > info по юридической значимости
# ФОРМАТ ОТВЕТА
Верни ТОЛЬКО валидный JSON-объект. Никаких пояснений, markdown-обёрток, комментариев.
Массив "recommendations" ОБЯЗАТЕЛЬНО должен содержать минимум 3 элемента.
Язык ответа: русский.`;
        const userPrompt = `# ЗАДАЧА
Проведи сравнительный правовой анализ двух договоров. Выяви все юридически значимые различия и дай профессиональные рекомендации.
# ДОКУМЕНТЫ
## ДОГОВОР A (эталон): "${nameA}"
\`\`\`
${tA}
\`\`\`
## ДОГОВОР B (сравниваемый): "${nameB}"
\`\`\`
${tB}
\`\`\`
# КАТЕГОРИИ АНАЛИЗА (в порядке юридической значимости)
1. **ФИНАНСОВЫЕ УСЛОВИЯ** — сумма, валюта, НДС, порядок оплаты, предоплата, индексация
2. **СРОКИ И ДАТЫ** — даты начала/окончания, сроки исполнения, пролонгация, расторжение
3. **ОТВЕТСТВЕННОСТЬ СТОРОН** — штрафы, пени, неустойки, лимиты ответственности
4. **ПРАВА И ОБЯЗАННОСТИ** — объём и характер обязательств, порядок приёмки
5. **КОНФИДЕНЦИАЛЬНОСТЬ И IP** — режим NDA, права на интеллектуальную собственность
6. **ПОДСУДНОСТЬ И ПРИМЕНИМОЕ ПРАВО** — арбитраж, суд, применимое законодательство
7. **ФОРС-МАЖОР** — перечень обстоятельств, порядок уведомления, сроки
8. **РАСТОРЖЕНИЕ** — основания и порядок прекращения договора
9. **ГАРАНТИИ И ЗАВЕРЕНИЯ** — representations & warranties сторон
10. **ПРОЧИЕ РАЗЛИЧИЯ** — остальные значимые изменения
# УРОВНИ ВАЖНОСТИ
- **"critical"** — юридический конфликт, делающий договоры несовместимыми; требует немедленного устранения
- **"warning"** — потенциальный риск; требует внимания и обсуждения с контрагентом
- **"info"** — информационное различие, не создающее существенных рисков
# ПРИМЕР ВЫВОДА (СТРОГО ЭТОТ ФОРМАТ)
{
"summary": "Документы имеют 5 существенных различий, из них 2 критических в финансовых условиях и сроках. Совместимость — 72%. Требуется согласование перед подписанием.",
"compatibilityScore": 72,
"compatibilityLabel": "Удовлетворительная",
"findings": [
{
"id": 1,
"category": "ФИНАНСОВЫЕ УСЛОВИЯ",
"severity": "critical",
"title": "Различие в сумме договора",
"inDocA": "Общая сумма договора составляет 500 000 (пятьсот тысяч) сомони, включая НДС 20%",
"inDocB": "Цена договора — 450 000 сомони без НДС",
"legalRisk": "Несогласованность цены и режима НДС создаёт риск спора об оплате и налоговых последствий для обеих сторон",
"recommendation": "Согласовать единую сумму и режим НДС. Рекомендуется использовать формулировку 'включая НДС 20%' согласно ст. 184 НК РТ"
}
],
"recommendations": [
{
"priority": "high",
"action": "Согласовать финансовые условия (сумма, валюта, НДС)",
"rationale": "Финансовые условия — существенное условие договора (ст. 424 ГК РТ). Без их согласования договор может быть признан незаключённым"
},
{
"priority": "medium",
"action": "Сверить сроки исполнения и порядок приёмки",
"rationale": "Несоответствие сроков может привести к просрочке и начислению неустойки"
},
{
"priority": "low",
"action": "Составить протокол разногласий к договору",
"rationale": "Протокол зафиксирует согласованные позиции и защитит интересы обеих сторон"
}
],
"overallConclusion": "Документы требуют дополнительного согласования по 2 критическим пунктам. После устранения разногласий договор может быть подписан."
}
# ТРЕБОВАНИЯ К ВЫВОДУ
1. **summary** — 2-3 предложения на русском с ключевыми выводами
2. **compatibilityScore** — число 0-100 (100 = идентичны)
3. **compatibilityLabel** — одно из: "Отличная" (≥95), "Хорошая" (85-94), "Удовлетворительная" (70-84), "Низкая" (50-69), "Критическая" (<50)
4. **findings** — массив всех выявленных различий (каждое с полной структурой)
5. **recommendations** — массив минимум из 3 рекомендаций с приоритетами
6. **overallConclusion** — итоговое заключение: можно ли подписывать после устранения разногласий
# ВАЖНО
- Анализируй ТОЛЬКО реальные различия из текстов
- Давай точные цитаты из обоих документов
- Severity распределяй по юридической значимости, а не по количеству
- Если различий мало — честно об этом напиши
- Верни ТОЛЬКО JSON
JSON:`;
        return { systemPrompt, userPrompt };
    }
    // =================================================================
    // JSON PARSER
    // =================================================================
    function parseAIResponse(content) {
        if (!content) return null;
        try { return JSON.parse(content.trim()); } catch (e) {}
        const jsonMatch = content.match(/```(?:json)?\s*([\s\S]*?)```/);
        if (jsonMatch) { try { return JSON.parse(jsonMatch[1].trim()); } catch (e) {} }
        const braceMatch = content.match(/\{[\s\S]*\}/);
        if (bracematch) { try { return JSON.parse(bracematch[0]); } catch (e) {} }
        const start = content.indexOf('{');
        const end = content.lastIndexOf('}');
        if (start !== -1 && end > start) {
            try { return JSON.parse(content.slice(start, end + 1)); } catch (e) {}
        }
        return null;
    }
    // =================================================================
    // AUTO-GENERATE RECOMMENDATIONS
    // =================================================================
    function generateRecommendationsFromFindings(findings) {
        const recs = [];
        const criticals = findings.filter(f => f.severity === 'critical');
        const warnings = findings.filter(f => f.severity === 'warning');
        if (criticals.length > 0) {
            recs.push({
                priority: 'high',
                action: `Устранить ${criticals.length} критических различий`,
                rationale: `Обнаружены конфликты: ${[...new Set(criticals.map(f => f.category))].join(', ')}`
            });
        }
        if (findings.some(f => f.category === 'ФИНАНСОВЫЕ УСЛОВИЯ')) {
            recs.push({
                priority: 'high',
                action: 'Согласовать финансовые условия',
                rationale: 'Существенное условие договора (ст. 424 ГК РТ)'
            });
        }
        if (warnings.length > 0) {
            recs.push({
                priority: 'medium',
                action: `Пересмотреть ${warnings.length} пунктов с предупреждениями`,
                rationale: `Требуют внимания: ${[...new Set(warnings.map(f => f.category))].slice(0, 3).join(', ')}`
            });
        }
        if (findings.some(f => ['СРОКИ И ДАТЫ', 'ОТВЕТСТВЕННОСТЬ СТОРОН'].includes(f.category))) {
            recs.push({
                priority: 'medium',
                action: 'Сверить сроки и неустойки',
                rationale: 'Может привести к просрочке и спорам'
            });
        }
        if (findings.length > 3) {
            recs.push({
                priority: 'low',
                action: 'Составить протокол разногласий',
                rationale: `${findings.length} различий требуют фиксации`
            });
        }
        recs.push({
            priority: 'low',
            action: 'Направить юристу для проверки',
            rationale: 'Профессиональная экспертиза перед подписанием'
        });
        while (recs.length < 3) {
            recs.push({
                priority: 'low',
                action: 'Проверить соответствие ГК РТ',
                rationale: 'Дополнительная проверка'
            });
        }
        return recs;
    }
    // =================================================================
    // AI ANALYSIS
    // =================================================================
    async function analyzeWithAI(textA, textB, nameA, nameB) {
        const { systemPrompt, userPrompt } = buildComparisonPrompt(textA, textB, nameA, nameB);
        try {
            const content = await callOllamaStreaming(systemPrompt, userPrompt);
            const result = parseAIResponse(content);
            if (!result) {
                showToast('Не удалось распарсить JSON', 'warning');
                return null;
            }
            result.findings = Array.isArray(result.findings) ? result.findings : [];
            result.recommendations = Array.isArray(result.recommendations) ? result.recommendations : [];
            result.compatibilityScore = typeof result.compatibilityScore === 'number' ? result.compatibilityScore : 50;
            result.compatibilityLabel = result.compatibilityLabel || 'Не определена';
            result.summary = result.summary || 'Анализ завершён.';
            result.overallConclusion = result.overallConclusion || '';
            result.totalDifferences = result.findings.length;
            if (result.findings.length > 0 && result.recommendations.length === 0) {
                result.recommendations = generateRecommendationsFromFindings(result.findings);
            }
            if (result.recommendations.length === 0) {
                result.recommendations = [
                    { priority: 'medium', action: 'Проверить соответствие ГК РТ', rationale: 'Юридическая проверка' },
                    { priority: 'low', action: 'Сохранить отчёт', rationale: 'Для архива' },
                    { priority: 'low', action: 'Консультация юриста', rationale: 'Перед подписанием' }
                ];
            }
            return result;
        } catch (err) {
            console.error('AI error:', err);
            showToast('Ошибка: ' + err.message, 'error');
            return null;
        }
    }
    // =================================================================
    // FILE EXTRACTION
    // =================================================================
    function setupDropZone(zoneId, inputId, slot) {
        const zone = document.getElementById(zoneId);
        const input = document.getElementById(inputId);
        zone.addEventListener('click', () => input.click());
        zone.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); }
        });
        ['dragenter','dragover'].forEach(ev => zone.addEventListener(ev, e => {
            e.preventDefault(); e.stopPropagation(); zone.classList.add('dragover');
        }));
        ['dragleave','drop'].forEach(ev => zone.addEventListener(ev, e => {
            e.preventDefault(); e.stopPropagation(); zone.classList.remove('dragover');
        }));
        zone.addEventListener('drop', e => {
            const file = e.dataTransfer.files[0];
            if (file) handleFile(file, slot);
        });
        input.addEventListener('change', e => {
            if (e.target.files[0]) handleFile(e.target.files[0], slot);
        });
    }
    async function handleFile(file, slot) {
        const MAX = 50 * 1024 * 1024;
        if (file.size > MAX) { showToast('Файл > 50 МБ', 'error'); return; }
        const ext = file.name.split('.').pop().toLowerCase();
        if (!['pdf','docx','doc','txt'].includes(ext)) { showToast('Неподдерживаемый формат', 'error'); return; }
        if (ext === 'doc') { showToast('.doc не поддерживается', 'warning'); return; }
        const dropContent = document.getElementById(`dropContent${slot}`);
        const originalHTML = dropContent.innerHTML;
        dropContent.innerHTML = `<div class="ai-thinking"><span></span><span></span><span></span></div><p class="text-sm mt-3" style="color: var(--text-tertiary);">Извлечение...</p>`;
        try {
            const text = await extractText(file, ext);
            if (!text || text.trim().length < 20) {
                dropContent.innerHTML = originalHTML;
                showToast('Не удалось извлечь текст', 'error'); return;
            }
            state[`file${slot}`] = file;
            state[`text${slot}`] = text;
            state[`name${slot}`] = file.name;
            const wordCount = text.trim().split(/\s+/).length;
            document.getElementById(`fileName${slot}`).textContent = file.name;
            document.getElementById(`fileSize${slot}`).textContent = formatSize(file.size);
            document.getElementById(`fileWords${slot}`).textContent = `${wordCount.toLocaleString('ru-RU')} слов`;
            document.getElementById(`dropContent${slot}`).classList.add('hidden');
            document.getElementById(`fileSelected${slot}`).classList.remove('hidden');
            document.getElementById(`dropZone${slot}`).classList.add('has-file');
            updateCompareButton();
            showToast(`✅ ${file.name} (${wordCount} слов)`, 'success');
        } catch (err) {
            dropContent.innerHTML = originalHTML;
            showToast('Ошибка: ' + err.message, 'error');
        }
    }
    async function extractText(file, ext) {
        if (ext === 'txt') return await file.text();
        if (ext === 'docx') {
            const arrayBuffer = await file.arrayBuffer();
            const zip = await JSZip.loadAsync(arrayBuffer);
            const documentXmlFile = zip.file('word/document.xml');
            if (!documentXmlFile) throw new Error('word/document.xml не найден');
            const documentXml = await documentXmlFile.async('string');
            return extractTextFromDocxXml(documentXml);
        }
        if (ext === 'pdf') {
            const arrayBuffer = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            const textParts = [];
            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const content = await page.getTextContent();
                let currentY = null, pageText = '';
                for (const item of content.items) {
                    if (!item.str) continue;
                    const y = Math.round(item.transform[5]);
                    if (currentY === null) pageText += item.str;
                    else if (Math.abs(y - currentY) < 5) pageText += ' ' + item.str;
                    else pageText += '\n' + item.str;
                    currentY = y;
                }
                if (pageText.trim()) textParts.push(pageText.trim());
            }
            return textParts.join('\n');
        }
        throw new Error(`.${ext} не поддерживается`);
    }
    function extractTextFromDocxXml(xmlString) {
        const paragraphs = [];
        const pRegex = /<w:p\b[^>]*>([\s\S]*?)<\/w:p>/gi;
        let pMatch;
        while ((pMatch = pRegex.exec(xmlString)) !== null) {
            const paragraphXml = pMatch[1];
            const runs = [];
            const tRegex = /<w:t[^>]*>([^<]*)<\/w:t>/gi;
            let tMatch;
            while ((tMatch = tRegex.exec(paragraphXml)) !== null) {
                let text = tMatch[1]
                    .replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&')
                    .replace(/&quot;/g, '"').replace(/&apos;/g, "'")
                    .replace(/&#(\d+);/g, (_, code) => String.fromCharCode(code))
                    .replace(/&#x([0-9a-f]+);/gi, (_, hex) => String.fromCharCode(parseInt(hex, 16)));
                runs.push(text);
            }
            paragraphs.push(runs.length > 0 ? runs.join('') : '');
        }
        return paragraphs.join('\n').replace(/\n{3,}/g, '\n\n').trim();
    }
    function clearFile(slot) {
        state[`file${slot}`] = null;
        state[`text${slot}`] = '';
        state[`name${slot}`] = '';
        document.getElementById(`fileInput${slot}`).value = '';
        document.getElementById(`dropContent${slot}`).classList.remove('hidden');
        document.getElementById(`fileSelected${slot}`).classList.add('hidden');
        document.getElementById(`dropZone${slot}`).classList.remove('has-file');
        updateCompareButton();
    }
    function updateCompareButton() {
        document.getElementById('compareBtn').disabled = !(state.textA && state.textB);
    }
    function formatSize(bytes) {
        if (bytes === 0) return '0 Б';
        const k = 1024, sizes = ['Б','КБ','МБ','ГБ'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    // =================================================================
    // DIFF ALGORITHM
    // =================================================================
    function tokenize(text) {
        return text.match(/[\w\u0400-\u04FF]+|[^\w\s]|\s+/g) || [];
    }
    function lcs(a, b) {
        const m = a.length, n = b.length;
        if (m * n > 5000000) return simpleDiff(a, b);
        const dp = Array(m + 1).fill(null).map(() => Array(n + 1).fill(0));
        for (let i = 1; i <= m; i++) {
            for (let j = 1; j <= n; j++) {
                if (a[i-1].toLowerCase() === b[j-1].toLowerCase()) dp[i][j] = dp[i-1][j-1] + 1;
                else dp[i][j] = Math.max(dp[i-1][j], dp[i][j-1]);
            }
        }
        const result = [];
        let i = m, j = n;
        while (i > 0 && j > 0) {
            if (a[i-1].toLowerCase() === b[j-1].toLowerCase()) { result.unshift({ type: 'equal', a: a[i-1], b: b[j-1] }); i--; j--; }
            else if (dp[i-1][j] >= dp[i][j-1]) { result.unshift({ type: 'delete', a: a[i-1] }); i--; }
            else { result.unshift({ type: 'insert', b: b[j-1] }); j--; }
        }
        while (i > 0) { result.unshift({ type: 'delete', a: a[i-1] }); i--; }
        while (j > 0) { result.unshift({ type: 'insert', b: b[j-1] }); j--; }
        return result;
    }
    function simpleDiff(a, b) {
        const result = [];
        const max = Math.max(a.length, b.length);
        for (let i = 0; i < max; i++) {
            if (i < a.length && i < b.length) {
                if (a[i].toLowerCase() === b[i].toLowerCase()) result.push({ type: 'equal', a: a[i], b: b[i] });
                else result.push({ type: 'modify', a: a[i], b: b[i] });
            } else if (i < a.length) result.push({ type: 'delete', a: a[i] });
            else result.push({ type: 'insert', b: b[i] });
        }
        return result;
    }
    function computeDiff(textA, textB) { return lcs(tokenize(textA), tokenize(textB)); }
    function renderDiffA(diff) {
        return diff.map(op => {
            if (op.type === 'equal') return `<span class="diff-equal">${escapeHtml(op.a)}</span>`;
            if (op.type === 'delete') return `<span class="diff-delete">${escapeHtml(op.a)}</span>`;
            if (op.type === 'modify') return `<span class="diff-modify">${escapeHtml(op.a)}</span>`;
            return '';
        }).join('');
    }
    function renderDiffB(diff) {
        return diff.map(op => {
            if (op.type === 'equal') return `<span class="diff-equal">${escapeHtml(op.b || op.a)}</span>`;
            if (op.type === 'insert') return `<span class="diff-insert">${escapeHtml(op.b)}</span>`;
            if (op.type === 'modify') return `<span class="diff-modify">${escapeHtml(op.b)}</span>`;
            return '';
        }).join('');
    }
    function escapeHtml(s) {
        if (!s) return '';
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function computeDiffStats(diff) {
        let equal = 0, insert = 0, delete_ = 0, modify = 0;
        for (const op of diff) {
            if (op.type === 'equal') equal++;
            else if (op.type === 'insert') insert++;
            else if (op.type === 'delete') delete_++;
            else if (op.type === 'modify') modify++;
        }
        const total = equal + insert + delete_ + modify;
        const similarity = total > 0 ? Math.round((equal / total) * 100) : 100;
        return { equal, insert, delete: delete_, modify, total, similarity };
    }
    // =================================================================
    // FALLBACK ANALYSIS
    // =================================================================
    function analyzeWithoutAI(textA, textB, diff) {
        const stats = computeDiffStats(diff);
        const findings = [];
        let id = 1;
        const patterns = [
            { regex: /\b(\d+(?:[.,]\d+)?)\s*(?:руб|сом|USD|EUR|сомони)\b/gi, category: 'ФИНАНСОВЫЕ УСЛОВИЯ', severity: 'critical' },
            { regex: /\b(\d{1,2}[.\-/]\d{1,2}[.\-/]\d{2,4}|\d{1,2}\s+\w+\s+\d{4})\b/g, category: 'СРОКИ И ДАТЫ', severity: 'warning' },
            { regex: /\b(\d+(?:[.,]\d+)?)\s*%/g, category: 'ОТВЕТСТВЕННОСТЬ СТОРОН', severity: 'warning' },
        ];
        const extractValues = (text, regex) => {
            const matches = [];
            let m;
            const r = new RegExp(regex.source, regex.flags);
            while ((m = r.exec(text)) !== null) matches.push(m[0]);
            return matches;
        };
        patterns.forEach(p => {
            const valsA = extractValues(textA, p.regex);
            const valsB = extractValues(textB, p.regex);
            [...new Set(valsA)].forEach(vA => {
                if (![...new Set(valsB)].some(vB => vB.toLowerCase() === vA.toLowerCase())) {
                    findings.push({
                        id: id++, category: p.category, severity: p.severity,
                        title: `Различие: ${p.category.toLowerCase()}`,
                        inDocA: vA, inDocB: '— не найдено —',
                        legalRisk: 'Возможны разногласия',
                        recommendation: 'Согласовать значение'
                    });
                }
            });
        });
        if (stats.modify + stats.insert + stats.delete > 10) {
            findings.unshift({
                id: id++, category: 'ПРОЧЕЕ', severity: 'info',
                title: 'Множественные различия',
                inDocA: `${stats.delete} удалённых, ${stats.modify} изменённых`,
                inDocB: `${stats.insert} добавленных`,
                legalRisk: 'Требуется изучение',
                recommendation: 'Проверить вручную'
            });
        }
        const score = stats.similarity;
        let label = 'Отличная';
        if (score < 95) label = 'Хорошая';
        if (score < 85) label = 'Удовлетворительная';
        if (score < 70) label = 'Низкая';
        if (score < 50) label = 'Критическая';
        const result = {
            summary: `Совпадение: ${score}%. Различий: ${stats.insert + stats.delete + stats.modify}.`,
            compatibilityScore: score,
            compatibilityLabel: label,
            totalDifferences: stats.insert + stats.delete + stats.modify,
            findings: findings.slice(0, 20),
            recommendations: [],
            overallConclusion: score >= 85 ? 'Совместимы. Проверьте различия.' : 'Существенные различия. Требуется согласование.'
        };
        result.recommendations = findings.length > 0
            ? generateRecommendationsFromFindings(findings)
            : [
                { priority: 'medium', action: 'Проверить ГК РТ', rationale: 'Юридическая проверка' },
                { priority: 'low', action: 'Сохранить отчёт', rationale: 'Для архива' },
                { priority: 'low', action: 'Консультация юриста', rationale: 'Перед подписанием' }
            ];
        return result;
    }
    // =================================================================
    // MAIN COMPARISON
    // =================================================================
    async function startComparison() {
        if (!state.textA || !state.textB) { showToast('Загрузите оба документа', 'warning'); return; }
        document.getElementById('progressSection').classList.remove('hidden');
        document.getElementById('resultsSection').classList.add('hidden');
        document.getElementById('uploadSection').classList.add('hidden');
        document.getElementById('compareBtn').disabled = true;
        startProgressTimer();
        const useAI = document.getElementById('useAI').checked && state.ollamaConnected;
        const useCache = document.getElementById('useCache').checked;
        try {
            updateProgress(15, 'Чтение документов...', 'stage-read');
            await sleep(300);
            updateProgress(40, 'Построение различий...', 'stage-diff');
            const diff = computeDiff(state.textA, state.textB);
            const stats = computeDiffStats(diff);
            await sleep(300);
            let aiResult = null;
            let fromCache = false;
            if (useCache) {
                updateProgress(50, '💾 Проверка кэша...', 'stage-ai');
                const cached = CacheManager.get(state.textA, state.textB);
                if (cached) {
                    aiResult = cached;
                    fromCache = true;
                    showToast('📦 Результат из кэша — мгновенно!', 'success');
                    updateProgress(95, '📦 Из кэша...', 'stage-report');
                    await sleep(500);
                }
            }
            if (!aiResult && useAI) {
                updateProgress(50, '🤖 AI-анализ (streaming)...', 'stage-ai');
                try {
                    aiResult = await analyzeWithAI(state.textA, state.textB, state.nameA, state.nameB);
                    if (aiResult) {
                        if (useCache) {
                            CacheManager.set(state.textA, state.textB, aiResult);
                            showToast('💾 Результат сохранён в кэш', 'success');
                        }
                        showToast(`✅ AI: ${aiResult.findings.length} различий`, 'success');
                    } else {
                        showToast('AI не ответил, используем локальный анализ', 'warning');
                    }
                } catch (err) {
                    if (err.message.includes('отменен')) {
                        stopProgressTimer();
                        document.getElementById('progressSection').classList.add('hidden');
                        document.getElementById('uploadSection').classList.remove('hidden');
                        document.getElementById('compareBtn').disabled = false;
                        return;
                    }
                    showToast('Ошибка AI: ' + err.message, 'warning');
                }
            }
            await sleep(200);
            updateProgress(95, 'Формирование отчёта...', 'stage-report');
            const result = aiResult || analyzeWithoutAI(state.textA, state.textB, diff);
            result.diff = diff;
            result.stats = stats;
            result.usedAI = !!aiResult;
            result.fromCache = fromCache;
            state.result = result;
            await sleep(300);
            updateProgress(100, 'Готово!', 'stage-report');
            await sleep(400);
            renderResults(result);
            saveToHistory(result);
            stopProgressTimer();
            document.getElementById('progressSection').classList.add('hidden');
            document.getElementById('resultsSection').classList.remove('hidden');
            const source = fromCache ? '📦 из кэша' : (aiResult ? '🤖 AI' : '📊 локально');
            showToast(`✅ Готово: ${result.recommendations.length} рекомендаций (${source})`, 'success');
            setTimeout(() => {
                document.getElementById('resultsSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 300);
        } catch (err) {
            console.error(err);
            showToast('Ошибка: ' + err.message, 'error');
            stopProgressTimer();
            document.getElementById('progressSection').classList.add('hidden');
            document.getElementById('uploadSection').classList.remove('hidden');
        } finally {
            document.getElementById('compareBtn').disabled = false;
        }
    }
    function updateProgress(percent, label, activeStage) {
        document.getElementById('progressBar').style.width = percent + '%';
        document.getElementById('progressPercent').textContent = percent + '%';
        document.getElementById('progressLabel').textContent = label;
        const stages = ['stage-read','stage-diff','stage-ai','stage-report'];
        const activeIdx = stages.indexOf(activeStage);
        stages.forEach((id, idx) => {
            const el = document.getElementById(id);
            if (idx < activeIdx) el.style.background = 'var(--accent-success)';
            else if (idx === activeIdx) { el.style.background = 'var(--accent-primary)'; el.classList.add('pulse-dot'); }
            else { el.style.background = 'var(--bg-tertiary)'; el.classList.remove('pulse-dot'); }
        });
    }
    function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }
    // =================================================================
    // RENDER RESULTS
    // =================================================================
    function renderResults(result) {
        const diff = result.diff;
        const stats = result.stats;
        document.getElementById('reportNumber').textContent = 'CMP-' + Math.floor(Math.random() * 900000 + 100000);
        document.getElementById('reportDate').textContent = new Date().toLocaleString('ru-RU');
        document.getElementById('reportModel').textContent = result.fromCache
            ? '📦 Из кэша'
            : (result.usedAI ? '🤖 llama3.1:8b · Streaming' : '📊 Локальный анализ');
        document.getElementById('reportSubtitle').textContent = `${state.nameA} ↔ ${state.nameB}`;
        const cacheIndicator = document.getElementById('cacheIndicator');
        if (result.fromCache) {
            cacheIndicator.classList.remove('hidden');
        } else {
            cacheIndicator.classList.add('hidden');
        }
        const score = result.compatibilityScore || stats.similarity;
        const ringColor = score >= 85 ? 'var(--accent-success)' : score >= 70 ? 'var(--accent-warning)' : 'var(--accent-error)';
        document.getElementById('scoreRing').style.setProperty('--score', score);
        document.getElementById('scoreRing').style.setProperty('--ring-color', ringColor);
        animateCounter(document.getElementById('scorePercent'), score, '%');
        document.getElementById('scoreLabel').textContent = result.compatibilityLabel || '—';
        const critical = result.findings.filter(f => f.severity === 'critical').length;
        const warning = result.findings.filter(f => f.severity === 'warning').length;
        const info = result.findings.filter(f => f.severity === 'info').length;
        animateCounter(document.getElementById('kpiCritical'), critical);
        animateCounter(document.getElementById('kpiWarning'), warning);
        animateCounter(document.getElementById('kpiInfo'), info);
        document.getElementById('docAName').textContent = state.nameA;
        document.getElementById('docAInfo').textContent = `${state.textA.split(/\s+/).length.toLocaleString('ru-RU')} слов · ${formatSize(state.fileA?.size || 0)}`;
        document.getElementById('docBName').textContent = state.nameB;
        document.getElementById('docBInfo').textContent = `${state.textB.split(/\s+/).length.toLocaleString('ru-RU')} слов · ${formatSize(state.fileB?.size || 0)}`;
        document.getElementById('executiveSummary').innerHTML =
            `<strong>${escapeHtml(result.summary)}</strong><br><br>${escapeHtml(result.overallConclusion || '')}`;
        renderHeatmap(result.findings);
        renderFindings(result.findings);
        document.getElementById('diffTitleA').textContent = state.nameA;
        document.getElementById('diffTitleB').textContent = state.nameB;
        document.getElementById('diffContentA').innerHTML = renderDiffA(diff);
        document.getElementById('diffContentB').innerHTML = renderDiffB(diff);
        renderRecommendations(result.recommendations || []);
    }
    function animateCounter(el, target, suffix = '') {
        if (!el) return;
        const duration = 1200;
        const startTime = performance.now();
        function update(now) {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(target * eased) + suffix;
            if (progress < 1) requestAnimationFrame(update);
            else el.textContent = target + suffix;
        }
        requestAnimationFrame(update);
    }
    function renderHeatmap(findings) {
        const grid = document.getElementById('heatmapGrid');
        const cells = [];
        for (let i = 0; i < 30; i++) {
            const f = findings[i];
            const severity = f ? f.severity : 'empty';
            cells.push(`<div class="heatmap-cell ${severity}" title="${f ? escapeHtml(f.title) : ''}"></div>`);
        }
        grid.innerHTML = cells.join('');
    }
    function renderFindings(findings) {
        const container = document.getElementById('findingsList');
        if (!findings || findings.length === 0) {
            container.innerHTML = `
<div class="text-center py-20">
<div class="w-24 h-24 mx-auto mb-5 rounded-2xl flex items-center justify-center" style="background: rgba(16, 185, 129, 0.2); box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);">
<svg class="w-12 h-12" style="color: var(--accent-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
</div>
<h4 class="text-2xl font-black mb-3">Различий не обнаружено</h4>
<p class="text-base" style="color: var(--text-secondary);">Документы идентичны</p>
</div>`;
            return;
        }
        container.innerHTML = findings.map((f, idx) => `
<div class="finding-card ${f.severity} slide-in-right" style="animation-delay: ${idx * 0.06}s;">
<div class="flex items-start gap-4 mb-4 flex-wrap">
<span class="severity-badge ${f.severity}">${f.severity.toUpperCase()}</span>
<span class="text-sm font-bold uppercase tracking-wider" style="color: var(--text-tertiary);">${escapeHtml(f.category || '')}</span>
<span class="text-sm ml-auto" style="color: var(--text-tertiary);">#${idx + 1}</span>
</div>
<h4 class="font-black text-xl mb-4 tracking-tight">${escapeHtml(f.title || '')}</h4>
<div class="grid md:grid-cols-2 gap-4 mb-4">
<div class="compare-box a">
<div class="text-xs font-bold uppercase tracking-wider mb-3 flex items-center gap-2" style="color: #3b82f6;">
<span class="w-5 h-5 rounded text-[11px] font-black text-white flex items-center justify-center" style="background: #3b82f6;">A</span>
Базовый
</div>
<p class="text-base">${escapeHtml(f.inDocA || '—')}</p>
</div>
<div class="compare-box b">
<div class="text-xs font-bold uppercase tracking-wider mb-3 flex items-center gap-2" style="color: var(--accent-secondary);">
<span class="w-5 h-5 rounded text-[11px] font-black text-white flex items-center justify-center" style="background: var(--accent-secondary);">B</span>
Сравниваемый
</div>
<p class="text-base">${escapeHtml(f.inDocB || '—')}</p>
</div>
</div>
<div class="risk-box mb-3">
<div class="text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--accent-error);">⚠️ Риск</div>
<p class="text-base">${escapeHtml(f.legalRisk || '—')}</p>
</div>
<div class="recommendation-box">
<div class="text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--accent-success);">💡 Рекомендация</div>
<p class="text-base">${escapeHtml(f.recommendation || '—')}</p>
</div>
</div>
`).join('');
    }
    function renderRecommendations(recs) {
        const container = document.getElementById('recommendationsList');
        if (!recs || recs.length === 0) {
            container.innerHTML = '<p class="text-center py-8" style="color: var(--text-tertiary);">Нет рекомендаций</p>';
            return;
        }
        const priorityIcons = { high: '🚨', medium: '⚠️', low: '💡' };
        const priorityLabels = { high: 'HIGH', medium: 'MEDIUM', low: 'LOW' };
        container.innerHTML = recs.map((r, idx) => `
<div class="rec-card ${r.priority} slide-in-right" style="animation-delay: ${idx * 0.1}s;">
<div class="flex items-start gap-5">
<span class="text-4xl flex-shrink-0">${priorityIcons[r.priority] || '💡'}</span>
<span class="priority-badge ${r.priority} flex-shrink-0">${priorityLabels[r.priority] || 'LOW'}</span>
<div class="flex-1 min-w-0">
<h4 class="font-black text-xl mb-2 tracking-tight">${escapeHtml(r.action || '')}</h4>
<p class="text-base" style="color: var(--text-secondary);">${escapeHtml(r.rationale || '')}</p>
</div>
</div>
</div>
`).join('');
    }
    // =================================================================
    // EXPORT
    // =================================================================
    function exportPDF() {
        if (!state.result) { showToast('Нет данных', 'error'); return; }
        showToast('Генерация PDF...', 'info', 2000);
        const el = document.getElementById('reportPrintArea');
        html2pdf().set({
            margin: [10, 10, 10, 10],
            filename: `Сравнение_${Date.now()}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(el).save().then(() => showToast('PDF сохранён', 'success'));
    }
    function exportMarkdown() {
        if (!state.result) { showToast('Нет данных', 'error'); return; }
        const r = state.result;
        let md = `# Отчёт о сравнении\n`;
        md += `**Дата:** ${new Date().toLocaleString('ru-RU')}\n`;
        md += `**Источник:** ${r.fromCache ? '📦 Кэш' : (r.usedAI ? '🤖 llama3.1:8b' : '📊 Локальный')}\n`;
        md += `**A:** ${state.nameA}\n**B:** ${state.nameB}\n\n`;
        md += `## Оценка\n- **Совместимость:** ${r.compatibilityScore}% (${r.compatibilityLabel})\n- **Различий:** ${r.totalDifferences}\n\n`;
        md += `## Резюме\n${r.summary}\n\n`;
        md += `## Различия\n`;
        r.findings.forEach((f, i) => {
            md += `### ${i+1}. ${f.title} [${f.severity}]\n`;
            md += `**Категория:** ${f.category}\n`;
            md += `**A:** ${f.inDocA}\n**B:** ${f.inDocB}\n`;
            md += `**Риск:** ${f.legalRisk}\n**Рекомендация:** ${f.recommendation}\n\n`;
        });
        md += `## Рекомендации\n`;
        r.recommendations.forEach(rc => md += `- **[${rc.priority}]** ${rc.action} — ${rc.rationale}\n`);
        md += `\n## Итог\n${r.overallConclusion}\n`;
        const blob = new Blob([md], { type: 'text/markdown;charset=utf-8' });
        downloadBlob(blob, `Сравнение_${Date.now()}.md`);
        showToast('Markdown сохранён', 'success');
    }
    function exportJSON() {
        if (!state.result) { showToast('Нет данных', 'error'); return; }
        const data = {
            metadata: {
                date: new Date().toISOString(),
                documentA: state.nameA,
                documentB: state.nameB,
                source: state.result.fromCache ? 'cache' : (state.result.usedAI ? document.getElementById('ollamaModel').value : 'local')
            },
            result: state.result
        };
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        downloadBlob(blob, `Сравнение_${Date.now()}.json`);
        showToast('JSON сохранён', 'success');
    }
    function downloadBlob(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = filename; a.click();
        URL.revokeObjectURL(url);
    }
    // =================================================================
    // HISTORY
    // =================================================================
    function comparisonResultToText(result) {
        if (!result) return '';
        const lines = [
            'ОТЧЁТ О СРАВНЕНИИ ДОГОВОРОВ',
            `Документ A: ${state.nameA || 'Документ A'}`,
            `Документ B: ${state.nameB || 'Документ B'}`,
            `Совместимость: ${result.compatibilityScore || 0}% (${result.compatibilityLabel || 'нет оценки'})`,
            '',
            'КРАТКОЕ РЕЗЮМЕ',
            result.summary || '',
            result.overallConclusion || '',
            '',
            'ВЫЯВЛЕННЫЕ РАЗЛИЧИЯ'
        ];

        (result.findings || []).forEach((f, index) => {
            lines.push(
                '',
                `${index + 1}. ${f.title || 'Различие'} [${f.severity || 'info'}]`,
                `Категория: ${f.category || 'Не указана'}`,
                `Документ A: ${f.inDocA || '—'}`,
                `Документ B: ${f.inDocB || '—'}`,
                `Риск: ${f.legalRisk || '—'}`,
                `Рекомендация: ${f.recommendation || '—'}`
            );
        });

        lines.push('', 'РЕКОМЕНДАЦИИ');
        (result.recommendations || []).forEach((r, index) => {
            lines.push(`${index + 1}. [${r.priority || 'medium'}] ${r.action || 'Действие'} — ${r.rationale || ''}`);
        });

        return lines.join('\n');
    }

    function saveToHistory(result) {
        try {
            const history = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');
            history.unshift({
                id: Date.now().toString(36),
                date: new Date().toISOString(),
                nameA: state.nameA, nameB: state.nameB,
                score: result.compatibilityScore,
                label: result.compatibilityLabel,
                findings: result.findings.length,
                recommendations: result.recommendations.length,
                usedAI: result.usedAI,
                fromCache: result.fromCache,
                result
            });
            if (history.length > 20) history.splice(20);
            localStorage.setItem(HISTORY_KEY, JSON.stringify(history));
            recordCabinetActivity({
                type: 'comparison',
                title: 'Сравнение договоров',
                details: `${state.nameA || 'Документ A'} - ${state.nameB || 'Документ B'} · совместимость ${result.compatibilityScore || 0}% · различий ${result.findings?.length || 0}`,
                file_name: `${state.nameA || 'Документ A'} vs ${state.nameB || 'Документ B'}`,
                summary: result.summary || '',
                result: comparisonResultToText(result),
                status: 'completed',
                metadata: {
                    documentA: state.nameA,
                    documentB: state.nameB,
                    compatibilityScore: result.compatibilityScore,
                    compatibilityLabel: result.compatibilityLabel,
                    findings: result.findings?.length || 0,
                    recommendations: result.recommendations?.length || 0,
                    usedAI: result.usedAI,
                    fromCache: result.fromCache,
                    result
                }
            });
        } catch (e) { console.warn(e); }
    }
    function showHistory() {
        const modal = document.getElementById('historyModal');
        const list = document.getElementById('historyList');
        const history = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');
        if (!history.length) {
            list.innerHTML = '<div class="text-center py-12" style="color: var(--text-tertiary);">📭 Пусто</div>';
        } else {
            list.innerHTML = history.map(h => `
<div class="history-item" onclick="loadHistory('${h.id}')">
<div class="flex items-center justify-between mb-3">
<span class="text-sm" style="color: var(--text-tertiary);">${new Date(h.date).toLocaleString('ru-RU')}</span>
<div class="flex gap-3">
${h.fromCache ? '<span class="text-xs px-3 py-1 rounded-lg" style="background: rgba(16, 185, 129, 0.2); color: var(--accent-success); border: 1px solid rgba(16, 185, 129, 0.3);">📦 Кэш</span>' : ''}
${h.usedAI ? '<span class="text-xs px-3 py-1 rounded-lg" style="background: rgba(139, 92, 246, 0.2); color: var(--accent-primary); border: 1px solid rgba(139, 92, 246, 0.3);">🤖 AI</span>' : ''}
<span class="text-xs font-bold px-3 py-1 rounded-lg" style="background: ${h.score >= 85 ? 'rgba(16, 185, 129, 0.2)' : h.score >= 70 ? 'rgba(245, 158, 11, 0.2)' : 'rgba(239, 68, 68, 0.2)'}; color: ${h.score >= 85 ? 'var(--accent-success)' : h.score >= 70 ? 'var(--accent-warning)' : 'var(--accent-error)'}; border: 1px solid ${h.score >= 85 ? 'rgba(16, 185, 129, 0.3)' : h.score >= 70 ? 'rgba(245, 158, 11, 0.3)' : 'rgba(239, 68, 68, 0.3)'};">${h.score}%</span>
</div>
</div>
<div class="font-bold text-lg truncate">${escapeHtml(h.nameA)} ↔ ${escapeHtml(h.nameB)}</div>
<div class="text-sm mt-2" style="color: var(--text-tertiary);">${h.findings || 0} различий · ${h.recommendations || 0} рек.</div>
</div>
`).join('');
        }
        modal.classList.add('active');
    }
    function closeHistory() { document.getElementById('historyModal').classList.remove('active'); }
    function loadHistory(id) {
        const history = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');
        const item = history.find(h => h.id === id);
        if (!item) return;
        state.nameA = item.nameA;
        state.nameB = item.nameB;
        state.result = item.result;
        renderResults(item.result);
        closeHistory();
        document.getElementById('resultsSection').classList.remove('hidden');
        setTimeout(() => document.getElementById('resultsSection').scrollIntoView({ behavior: 'smooth' }), 100);
    }
    function clearHistory() {
        if (!confirm('Удалить историю?')) return;
        localStorage.removeItem(HISTORY_KEY);
        showHistory();
        showToast('Очищено', 'success');
    }
    function resetComparison() {
        clearFile('A'); clearFile('B');
        state.result = null;
        document.getElementById('resultsSection').classList.add('hidden');
        document.getElementById('progressSection').classList.add('hidden');
        document.getElementById('uploadSection').classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    // =================================================================
    // KEYBOARD
    // =================================================================
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            if (!document.getElementById('compareBtn').disabled) startComparison();
        }
        if (e.key === 'Escape') {
            closeHistory();
            closeCacheManager();
        }
    });
    // =================================================================
    // INIT
    // =================================================================
    setupDropZone('dropZoneA', 'fileInputA', 'A');
    setupDropZone('dropZoneB', 'fileInputB', 'B');

    // Theme Init
    const savedTheme = localStorage.getItem('legalai_theme');
    let isLight = false;
    if (savedTheme) {
        isLight = savedTheme === 'light';
    } else {
        isLight = !window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    if (isLight) {
        document.documentElement.classList.add('light-theme');
        const btn = document.getElementById('themeToggle');
        if (btn) btn.textContent = '☀️';
    }

    setTimeout(() => {
        checkOllamaConnection();
        updateCacheStats();
    }, 500);
</script>
</body>
</html>
