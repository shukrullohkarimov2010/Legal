<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="description" content="LegalAI Pro — квалификация по УК Таджикистана: ответ строится только по тексту кодекса из RAG-индекса">
<meta name="theme-color" content="#0a0e1c" media="(prefers-color-scheme: dark)">
<meta name="theme-color" content="#f5f6fb" media="(prefers-color-scheme: light)">
<title>LegalAI Pro — Анализ УК РТ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════════════
   LegalAI Pro · УК РТ — дизайн-система
   1. Токены   2. База   3. Шапка и hero   4. Рабочая область
   5. Ввод     6. Прогресс   7. Результаты   8. База знаний/История
   9. Служебное   10. Адаптив и печать
   ═══════════════════════════════════════════════════════════════ */

/* ── 1. Токены ─────────────────────────────────────────────── */
:root {
  --font-sans: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
  --font-display: var(--font-sans);
  --font-mono: 'JetBrains Mono', ui-monospace, 'SF Mono', Consolas, monospace;

  --space-1: .25rem; --space-2: .5rem; --space-3: .75rem; --space-4: 1rem; --space-5: 1.25rem;
  --space-6: 1.5rem; --space-8: 2rem; --space-10: 2.5rem; --space-12: 3rem;

  --text-xs: .75rem; --text-sm: .875rem; --text-base: 1rem; --text-lg: 1.125rem;
  --text-xl: 1.375rem; --text-2xl: 1.75rem;
  --leading-normal: 1.5; --leading-relaxed: 1.65; --tracking-wide: .06em;

  --radius-sm: 8px; --radius-md: 10px; --radius-lg: 14px; --radius-xl: 20px; --radius-full: 999px;
  --duration-fast: .15s; --duration: .25s; --ease: cubic-bezier(.2, .8, .2, 1);

  --accent-orange: #f97316; --accent-amber: #fbbf24; --accent-green: #10b981;
  --grad: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  --topbar-h: 64px;
}

[data-theme="light"] {
  color-scheme: light;
  --bg: #f4f5fb; --surface: #ffffff; --surface-2: #f6f7fc; --sunken: #eef0f8;
  --ink-primary: #0f172a; --ink-secondary: #475569; --ink-tertiary: #64748b;
  --line: #e4e7f1; --line-medium: #d3d9e8; --line-strong: #b6c0d6;
  --accent: #4f46e5; --accent-strong: #4338ca; --accent-soft: rgba(79, 70, 229, .08); --accent-ring: rgba(79, 70, 229, .28);
  --glass: rgba(255, 255, 255, .78);
  --shadow-sm: 0 1px 2px rgba(15, 23, 42, .05);
  --shadow-md: 0 8px 24px -8px rgba(30, 41, 90, .14), 0 2px 6px rgba(15, 23, 42, .04);
  --shadow-lg: 0 24px 60px -20px rgba(49, 46, 129, .28);
  --success-bg: #ecfdf5; --success-border: #a7f3d0; --success-text: #047857;
  --warning-bg: #fffbeb; --warning-border: #fcd98a; --warning-text: #92400e;
  --info-bg: #eef4ff; --info-border: #c7d7fe; --info-text: #3730a3;
  --blob-1: rgba(99, 102, 241, .16); --blob-2: rgba(56, 189, 248, .14);
}
[data-theme="dark"] {
  color-scheme: dark;
  --bg: #090d1b; --surface: #111730; --surface-2: #161e3d; --sunken: #0c1228;
  --ink-primary: #eef1ff; --ink-secondary: #b6bedd; --ink-tertiary: #8e98bf;
  --line: #212a4d; --line-medium: #2c3763; --line-strong: #3d4a7e;
  --accent: #818cf8; --accent-strong: #a5b4fc; --accent-soft: rgba(129, 140, 248, .13); --accent-ring: rgba(129, 140, 248, .38);
  --glass: rgba(11, 16, 36, .72);
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, .3);
  --shadow-md: 0 10px 30px -10px rgba(0, 0, 0, .55);
  --shadow-lg: 0 30px 70px -24px rgba(0, 0, 0, .7);
  --success-bg: rgba(16, 185, 129, .1); --success-border: rgba(52, 211, 153, .32); --success-text: #6ee7b7;
  --warning-bg: rgba(245, 158, 11, .1); --warning-border: rgba(251, 191, 36, .34); --warning-text: #fcd34d;
  --info-bg: rgba(99, 102, 241, .11); --info-border: rgba(129, 140, 248, .32); --info-text: #c7d2fe;
  --blob-1: rgba(99, 102, 241, .22); --blob-2: rgba(14, 165, 233, .14);
}

/* ── 2. База ───────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }
html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
body {
  margin: 0; min-height: 100vh; font-family: var(--font-sans); font-size: var(--text-base);
  line-height: var(--leading-normal); color: var(--ink-primary); background: var(--bg);
  -webkit-font-smoothing: antialiased; text-rendering: optimizeLegibility;
}
h1, h2, h3, p, ol, ul { margin: 0; }
button, input, textarea { font: inherit; color: inherit; }
button { cursor: pointer; }
a { color: inherit; text-decoration: none; }
:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
::selection { background: var(--accent-ring); }
.sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; }

.ambient-bg { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
.ambient-bg::before, .ambient-bg::after { content: ''; position: absolute; border-radius: 50%; filter: blur(90px); }
.ambient-bg::before { width: 620px; height: 620px; top: -220px; left: -160px; background: var(--blob-1); }
.ambient-bg::after { width: 560px; height: 560px; right: -180px; top: 22%; background: var(--blob-2); }
.app-shell { position: relative; z-index: 1; }
.container { width: 100%; max-width: 1240px; margin-inline: auto; padding-inline: 24px; }

/* ── Кнопки ── */
.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: var(--space-2);
  min-height: 46px; padding: 0 var(--space-5); border-radius: var(--radius-lg); border: 1px solid transparent;
  font-weight: 600; font-size: var(--text-base); white-space: nowrap;
  transition: transform var(--duration-fast), box-shadow var(--duration), background var(--duration), border-color var(--duration), opacity var(--duration);
}
.btn:disabled { opacity: .55; cursor: not-allowed; }
.btn--primary { color: #fff; background: var(--grad); box-shadow: 0 10px 24px -10px rgba(99, 70, 229, .8), inset 0 1px 0 rgba(255, 255, 255, .22); }
.btn--primary:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 14px 30px -10px rgba(99, 70, 229, .9), inset 0 1px 0 rgba(255, 255, 255, .22); }
.btn--primary:active:not(:disabled) { transform: translateY(0); }
.btn--primary kbd { margin-left: var(--space-1); background: rgba(255, 255, 255, .18); color: #fff; border-color: rgba(255, 255, 255, .22); }
.btn--link { color: var(--ink-secondary); background: transparent; border-color: var(--line-medium); min-height: 46px; }
.btn--link:hover:not(:disabled) { color: var(--ink-primary); border-color: var(--line-strong); background: var(--surface-2); }
kbd {
  font: 600 var(--text-xs)/1 var(--font-mono); padding: 4px 6px; border-radius: 6px;
  border: 1px solid var(--line-medium); background: var(--sunken); color: var(--ink-tertiary);
}
.spinner { width: 18px; height: 18px; border-radius: 50%; border: 2px solid rgba(255, 255, 255, .35); border-top-color: #fff; animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── 3. Шапка и hero ───────────────────────────────────────── */
.topbar {
  position: sticky; top: 0; z-index: 50; height: var(--topbar-h);
  background: var(--glass); backdrop-filter: blur(16px) saturate(1.4); -webkit-backdrop-filter: blur(16px) saturate(1.4);
  border-bottom: 1px solid var(--line);
}
.topbar .container { height: 100%; display: flex; align-items: center; justify-content: space-between; gap: var(--space-4); }
.brand { display: inline-flex; align-items: center; gap: 10px; font-weight: 800; font-size: var(--text-lg); letter-spacing: -.01em; }
.brand__mark { display: grid; place-items: center; width: 34px; height: 34px; border-radius: 10px; color: #fff; background: var(--grad); box-shadow: 0 6px 16px -6px rgba(99, 70, 229, .8); }
.brand__pro { font-size: 10px; font-weight: 700; letter-spacing: .08em; padding: 3px 6px; border-radius: 6px; color: var(--accent); background: var(--accent-soft); margin-left: -2px; }
.nav-actions { display: flex; align-items: center; gap: var(--space-2); }
.nav-link { font-size: var(--text-sm); font-weight: 500; color: var(--ink-secondary); padding: 8px 12px; border-radius: var(--radius-full); transition: background var(--duration-fast), color var(--duration-fast); }
a.nav-link:hover { background: var(--accent-soft); color: var(--ink-primary); }
.nav-link[aria-current="page"] { color: var(--accent); background: var(--accent-soft); font-weight: 600; }

.lang-toggle { display: inline-flex; padding: 3px; border-radius: var(--radius-full); border: 1px solid var(--line-medium); background: var(--surface); font: 600 var(--text-xs)/1 var(--font-mono); color: var(--ink-tertiary); }
.lang-toggle span { padding: 7px 10px; border-radius: var(--radius-full); transition: background var(--duration-fast), color var(--duration-fast); }
html[data-ui-lang="ru"] .lang-toggle [data-lang="ru"],
html[data-ui-lang="tg"] .lang-toggle [data-lang="tg"] { background: var(--grad); color: #fff; }

.status-badge { display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: var(--radius-full); border: 1px solid var(--line-medium); background: var(--surface); font-size: var(--text-xs); font-weight: 600; color: var(--ink-secondary); transition: border-color var(--duration-fast); }
.status-badge:hover { border-color: var(--line-strong); }
.status-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent-amber); box-shadow: 0 0 0 0 rgba(251, 191, 36, .5); animation: pulse 1.6s infinite; }
.status-badge[data-state="connected"] .status-dot { background: #10b981; animation: none; box-shadow: 0 0 0 3px rgba(16, 185, 129, .2); }
.status-badge[data-state="connected"] { color: var(--success-text); border-color: var(--success-border); }
.status-badge[data-state="disconnected"] .status-dot { background: #ef4444; animation: none; box-shadow: 0 0 0 3px rgba(239, 68, 68, .2); }
.status-badge[data-state="disconnected"] { color: #ef4444; border-color: rgba(239, 68, 68, .4); }
@keyframes pulse { 70% { box-shadow: 0 0 0 7px rgba(251, 191, 36, 0); } 100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0); } }

.theme-toggle { position: relative; width: 52px; height: 30px; padding: 0; border-radius: var(--radius-full); border: 1px solid var(--line-medium); background: var(--surface); }
.theme-toggle__thumb { position: absolute; top: 3px; left: 3px; width: 22px; height: 22px; border-radius: 50%; display: grid; place-items: center; color: #fff; background: var(--grad); transition: transform var(--duration) var(--ease); }
[data-theme="dark"] .theme-toggle__thumb { transform: translateX(22px); }
[data-theme="dark"] #themeIconSun, [data-theme="light"] #themeIconMoon { display: none; }

.hero { padding-top: var(--space-6); }
.hero-card {
  position: relative; overflow: hidden; isolation: isolate; border-radius: 28px; color: #fff;
  background: linear-gradient(125deg, #1b1b5a 0%, #3028a8 50%, #2563eb 100%);
  box-shadow: var(--shadow-lg);
}
.hero-card::before { /* сетка */
  content: ''; position: absolute; inset: 0; z-index: -1; opacity: .5;
  background-image: linear-gradient(rgba(255, 255, 255, .06) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .06) 1px, transparent 1px);
  background-size: 38px 38px; -webkit-mask-image: radial-gradient(70% 90% at 75% 40%, #000 0%, transparent 75%); mask-image: radial-gradient(70% 90% at 75% 40%, #000 0%, transparent 75%);
}
.hero-card::after { /* свечение */
  content: ''; position: absolute; z-index: -1; width: 560px; height: 560px; right: -120px; top: -200px; border-radius: 50%;
  background: radial-gradient(circle, rgba(167, 139, 250, .55), transparent 65%);
}
.hero-content { display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(260px, .75fr); align-items: center; gap: var(--space-6); padding: 40px 48px; }
.hero-eyebrow { display: inline-flex; align-items: center; gap: 8px; font-size: var(--text-xs); font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #c7d2fe; padding: 6px 12px; border-radius: var(--radius-full); background: rgba(255, 255, 255, .1); border: 1px solid rgba(255, 255, 255, .16); }
.hero-eyebrow::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #34d399; box-shadow: 0 0 10px #34d399; }
.hero-title { margin-top: var(--space-4); font-size: clamp(1.65rem, 3.1vw, 2.5rem); line-height: 1.12; font-weight: 800; letter-spacing: -.02em; text-wrap: balance; }
.hero-subtitle { margin-top: var(--space-4); max-width: 60ch; font-size: var(--text-base); line-height: var(--leading-relaxed); color: #dbe2ff; }
.hero-subtitle strong { color: var(--accent-amber); font-weight: 700; }
.hero-trust { display: flex; flex-wrap: wrap; gap: var(--space-2); margin-top: var(--space-5); }
.trust-chip { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: var(--radius-full); font-size: var(--text-sm); font-weight: 600; color: #eef2ff; background: rgba(255, 255, 255, .1); border: 1px solid rgba(255, 255, 255, .16); backdrop-filter: blur(6px); }
.trust-chip svg { color: #a5b4fc; flex: none; }

/* 3D-весы */
.hero-visual { position: relative; display: grid; place-items: center; min-height: 250px; perspective: 900px; }
.scales { position: relative; width: min(100%, 300px); }
.scales__glow { position: absolute; inset: 8% 8% 0; border-radius: 50%; background: radial-gradient(circle, rgba(251, 191, 36, .32), transparent 68%); filter: blur(24px); animation: glow 5s ease-in-out infinite alternate; }
@keyframes glow { to { opacity: .55; transform: scale(1.08); } }
.scales__tilt { position: relative; transform-style: preserve-3d; transition: transform .25s ease-out; will-change: transform; }
.scales__svg { display: block; width: 100%; height: auto; overflow: visible; filter: drop-shadow(0 22px 22px rgba(10, 8, 60, .5)); animation: float 6s ease-in-out infinite; }
@keyframes float { 50% { transform: translateY(-7px); } }
.scales__beam { transform-box: view-box; transform-origin: 160px 56px; animation: sway 5.5s ease-in-out infinite alternate; }
.scales__pan { transform-box: view-box; animation: sway-back 5.5s ease-in-out infinite alternate; }
.scales__pan--l { transform-origin: 46px 66px; }
.scales__pan--r { transform-origin: 274px 66px; }
@keyframes sway { from { transform: rotate(-3.6deg); } to { transform: rotate(3.6deg); } }
@keyframes sway-back { from { transform: rotate(3.6deg); } to { transform: rotate(-3.6deg); } }
.scales__spark { position: absolute; border-radius: 50%; background: #fde68a; box-shadow: 0 0 10px 2px rgba(253, 230, 138, .8); animation: twinkle 3s ease-in-out infinite; }
.scales__spark:nth-of-type(1) { width: 5px; height: 5px; top: 12%; left: 10%; }
.scales__spark:nth-of-type(2) { width: 4px; height: 4px; top: 30%; right: 4%; animation-delay: 1.1s; }
.scales__spark:nth-of-type(3) { width: 6px; height: 6px; bottom: 22%; left: 2%; animation-delay: 2s; }
@keyframes twinkle { 0%, 100% { opacity: .15; transform: scale(.7); } 50% { opacity: 1; transform: scale(1.15); } }

/* ── 4. Рабочая область ────────────────────────────────────── */
.workspace {
  display: grid; gap: var(--space-4) var(--space-6); padding-block: var(--space-6) var(--space-10);
  grid-template-columns: minmax(360px, 440px) minmax(0, 1fr);
  grid-template-areas: "composer tabs" "composer panes"; grid-template-rows: auto 1fr; align-items: start;
}
.surface { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius-xl); box-shadow: var(--shadow-md); }
.pane { display: none; min-width: 0; }
.workspace[data-pane="input"] .pane--input,
.workspace[data-pane="result"] .pane--result,
.workspace[data-pane="kb"] .pane--kb,
.workspace[data-pane="history"] .pane--history { display: block; }
.pane--result, .pane--kb, .pane--history { grid-area: panes; }

.tabs { grid-area: tabs; display: flex; gap: 4px; padding: 4px; border-radius: var(--radius-lg); background: var(--sunken); border: 1px solid var(--line); align-self: start; overflow-x: auto; scrollbar-width: none; }
.tabs::-webkit-scrollbar { display: none; }
.tab { flex: 1 1 0; display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 40px; padding: 0 var(--space-4); border: 0; border-radius: var(--radius-md); background: transparent; font-size: var(--text-sm); font-weight: 600; color: var(--ink-tertiary); white-space: nowrap; transition: background var(--duration-fast), color var(--duration-fast), box-shadow var(--duration-fast); }
.tab:hover { color: var(--ink-primary); }
.tab[aria-selected="true"] { background: var(--surface); color: var(--ink-primary); box-shadow: var(--shadow-sm), 0 0 0 1px var(--line); }
.tab--input { display: none; }
.tab__count { min-width: 20px; padding: 2px 6px; border-radius: var(--radius-full); font: 700 11px/1.3 var(--font-mono); color: #fff; background: var(--grad); text-align: center; }
.tab__count[hidden] { display: none; }

/* ── 5. Ввод ───────────────────────────────────────────────── */
.composer { grid-area: composer; position: sticky; top: calc(var(--topbar-h) + 16px); padding: var(--space-6); display: none; flex-direction: column; gap: var(--space-4); }
.workspace[data-pane="input"] .composer { display: flex; }
.composer__head { display: flex; align-items: flex-start; justify-content: space-between; gap: var(--space-3); }
.composer__step { font: 700 var(--text-xs)/1 var(--font-mono); letter-spacing: .08em; text-transform: uppercase; color: var(--accent); }
.panel__title { margin-top: 6px; font-size: var(--text-xl); font-weight: 800; letter-spacing: -.015em; line-height: 1.2; }
.panel__subtitle { margin-top: 6px; font-size: var(--text-sm); color: var(--ink-tertiary); line-height: var(--leading-normal); }
.icon-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 10px; border-radius: var(--radius-md); border: 1px solid var(--line-medium); background: transparent; color: var(--ink-tertiary); font-size: var(--text-xs); font-weight: 600; transition: all var(--duration-fast); flex: none; }
.icon-btn:hover { color: var(--ink-primary); border-color: var(--line-strong); background: var(--surface-2); }

.alert { display: flex; gap: var(--space-3); padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); font-size: var(--text-sm); line-height: var(--leading-normal); }
.alert--warning { background: var(--warning-bg); border: 1px solid var(--warning-border); color: var(--warning-text); }
.alert__icon { flex: none; margin-top: 2px; }
.alert small { opacity: .85; }

.scenarios__label, .field-label { font: 700 var(--text-xs)/1 var(--font-mono); letter-spacing: .08em; text-transform: uppercase; color: var(--ink-tertiary); margin-bottom: var(--space-2); }
.scenarios__grid { display: flex; flex-wrap: wrap; gap: 6px; }
.scenario-chip { padding: 7px 12px; border-radius: var(--radius-full); border: 1px solid var(--line-medium); background: var(--surface-2); color: var(--ink-secondary); font-size: var(--text-sm); font-weight: 500; transition: all var(--duration-fast); }
.scenario-chip:hover { color: var(--accent-strong); border-color: var(--accent-ring); background: var(--accent-soft); transform: translateY(-1px); }

.input-area { position: relative; }
.crime-input {
  display: block; width: 100%; min-height: 190px; padding: var(--space-4) var(--space-4) 40px; resize: vertical;
  border-radius: var(--radius-lg); border: 1px solid var(--line-medium); background: var(--sunken); color: var(--ink-primary);
  font-size: var(--text-base); line-height: var(--leading-relaxed);
  transition: border-color var(--duration-fast), box-shadow var(--duration-fast), background var(--duration-fast);
}
.crime-input::placeholder { color: var(--ink-tertiary); opacity: .85; }
.crime-input:hover { border-color: var(--line-strong); }
.crime-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-ring); background: var(--surface); }
.char-count { font: 600 var(--text-xs)/1 var(--font-mono); color: var(--ink-tertiary); }
.input-area .char-count { position: absolute; right: 14px; bottom: 12px; pointer-events: none; }
.char-count[data-level="warning"] { color: var(--accent-orange); }
.char-count[data-level="danger"] { color: #ef4444; }
@keyframes shake { 20%, 60% { transform: translateX(-6px); } 40%, 80% { transform: translateX(6px); } }

.mode-switch { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-2); }
.mode-switch__opt { position: relative; text-align: left; display: flex; flex-direction: column; gap: 3px; padding: 12px 14px 12px 40px; border-radius: var(--radius-lg); border: 1px solid var(--line-medium); background: transparent; color: var(--ink-secondary); transition: all var(--duration-fast); }
.mode-switch__opt::before { content: ''; position: absolute; left: 14px; top: 15px; width: 16px; height: 16px; border-radius: 50%; border: 2px solid var(--line-strong); transition: all var(--duration-fast); }
.mode-switch__opt strong { color: var(--ink-primary); font-size: var(--text-sm); }
.mode-switch__opt span { font-size: var(--text-xs); color: var(--ink-tertiary); line-height: 1.4; }
.mode-switch__opt:hover { border-color: var(--line-strong); }
.mode-switch__opt[aria-checked="true"] { border-color: var(--accent); background: var(--accent-soft); box-shadow: 0 0 0 1px var(--accent) inset; }
.mode-switch__opt[aria-checked="true"]::before { border-color: var(--accent); background: radial-gradient(circle, var(--accent) 0 38%, transparent 42%); }

.input-footer { display: flex; flex-direction: column; gap: var(--space-2); }
.btn-group { display: flex; flex-direction: column; gap: var(--space-2); }
.btn-group .btn { width: 100%; }
.btn-group .btn--primary { min-height: 52px; font-size: var(--text-base); }
.composer__note { display: flex; gap: 8px; align-items: flex-start; padding-top: var(--space-4); border-top: 1px dashed var(--line-medium); font-size: var(--text-xs); line-height: 1.5; color: var(--ink-tertiary); }
.composer__note svg { flex: none; margin-top: 1px; color: var(--accent); }
.composer__note div { display: grid; gap: 4px; }

/* ── 6. Прогресс ───────────────────────────────────────────── */
.progress-tracker { display: none; padding: var(--space-5) var(--space-6); margin-bottom: var(--space-4); }
.progress-tracker[data-active] { display: block; animation: rise .4s var(--ease) both; }
.progress-tracker__title { font-weight: 700; font-size: var(--text-base); margin-bottom: var(--space-4); display: flex; align-items: center; gap: 10px; }
.progress-tracker__title::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: var(--accent); animation: pulse-accent 1.4s infinite; }
@keyframes pulse-accent { 70% { box-shadow: 0 0 0 8px transparent; } 0% { box-shadow: 0 0 0 0 var(--accent-ring); } }
.progress-steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-2); }
.progress-step { position: relative; display: flex; flex-direction: column; gap: var(--space-3); padding-right: var(--space-3); }
.progress-step:not(:last-child)::after { content: ''; position: absolute; top: 17px; left: 44px; right: 6px; height: 2px; border-radius: 2px; background: var(--line-medium); transition: background var(--duration); }
.progress-step[data-state="done"]:not(:last-child)::after { background: var(--accent); }
.step-indicator { position: relative; display: grid; place-items: center; width: 36px; height: 36px; border-radius: 50%; font: 700 var(--text-sm)/1 var(--font-mono); color: var(--ink-tertiary); background: var(--sunken); border: 2px solid var(--line-medium); transition: all var(--duration); }
.step-indicator.pop { animation: pop .35s var(--ease); }
@keyframes pop { 40% { transform: scale(1.18); } }
.progress-step[data-state="active"] .step-indicator { color: var(--accent); border-color: var(--accent); background: var(--accent-soft); }
.progress-step[data-state="active"] .step-indicator::after { content: ''; position: absolute; inset: -6px; border-radius: 50%; border: 2px solid transparent; border-top-color: var(--accent); animation: spin 1s linear infinite; }
.progress-step[data-state="done"] .step-indicator { font-size: 0; border-color: transparent; background: var(--grad); }
.progress-step[data-state="done"] .step-indicator::before { content: ''; width: 12px; height: 7px; margin-top: -3px; border-left: 2.5px solid #fff; border-bottom: 2.5px solid #fff; transform: rotate(-45deg); }
.progress-step[data-state="error"] .step-indicator { font-size: 0; border-color: transparent; background: var(--accent-orange); }
.progress-step[data-state="error"] .step-indicator::before { content: '!'; font: 800 16px/1 var(--font-sans); color: #fff; }
.step-label { font-size: var(--text-sm); font-weight: 700; line-height: 1.3; color: var(--ink-primary); }
.step-detail { margin-top: 4px; font-size: var(--text-xs); line-height: 1.45; color: var(--ink-tertiary); overflow-wrap: anywhere; }
.progress-step[data-state="done"] .step-detail { color: var(--success-text); }
.progress-step[data-state="error"] .step-detail { color: var(--warning-text); }
.progress-step:not([data-state]) .step-label { color: var(--ink-tertiary); }

/* ── 7. Результаты ─────────────────────────────────────────── */
.results { display: none; scroll-margin-top: calc(var(--topbar-h) + 16px); }
.results[data-active] { display: flex; flex-direction: column; gap: var(--space-4); }
.pane--result:has(.progress-tracker[data-active]) .empty-state { display: none; }
.results[data-active] ~ .empty-state { display: none; }
@keyframes rise { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }

.empty-state { text-align: center; padding: var(--space-10) var(--space-6); border: 1.5px dashed var(--line-medium); border-radius: var(--radius-xl); background: color-mix(in srgb, var(--surface) 55%, transparent); }
.empty-state__icon { display: grid; place-items: center; width: 68px; height: 68px; margin: 0 auto var(--space-4); border-radius: 22px; color: #fff; background: var(--grad); box-shadow: 0 16px 34px -14px rgba(99, 70, 229, .9); }
.empty-state h3 { font-size: var(--text-lg); font-weight: 800; }
.empty-state p { max-width: 46ch; margin: var(--space-2) auto 0; font-size: var(--text-sm); line-height: var(--leading-relaxed); color: var(--ink-tertiary); }
.empty-steps { display: flex; flex-wrap: wrap; justify-content: center; gap: var(--space-2); margin-top: var(--space-5); counter-reset: s; }
.empty-steps li { list-style: none; counter-increment: s; display: inline-flex; align-items: center; gap: 8px; padding: 7px 14px 7px 8px; border-radius: var(--radius-full); background: var(--surface); border: 1px solid var(--line); font-size: var(--text-xs); font-weight: 600; color: var(--ink-secondary); }
.empty-steps li::before { content: counter(s); display: grid; place-items: center; width: 20px; height: 20px; border-radius: 50%; font: 700 11px/1 var(--font-mono); color: #fff; background: var(--grad); }

.result-card, .summary-card, .clarify-card { animation: rise .45s var(--ease) both; animation-delay: calc(var(--i, 0) * 70ms); }
.result-card { padding: var(--space-5) var(--space-6); border-radius: var(--radius-xl); background: var(--surface); border: 1px solid var(--line); box-shadow: var(--shadow-md); }
.result-card--verdict { border-color: var(--accent-ring); background: linear-gradient(180deg, var(--accent-soft), transparent 140px), var(--surface); }
.result-card__header { display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-4); }
.result-card__icon { display: grid; place-items: center; flex: none; width: 42px; height: 42px; border-radius: 13px; color: var(--accent); background: var(--accent-soft); border: 1px solid var(--accent-ring); }
.result-card__label { font: 700 var(--text-xs)/1 var(--font-mono); letter-spacing: .08em; text-transform: uppercase; color: var(--accent); }
.result-card__title { margin-top: 5px; font-size: var(--text-lg); font-weight: 700; line-height: 1.3; letter-spacing: -.01em; }
.badge-row { display: flex; gap: var(--space-2); flex-wrap: wrap; margin-bottom: var(--space-3); }
.article-badge { display: inline-flex; align-items: center; padding: 6px 12px; border-radius: var(--radius-full); font-size: var(--text-sm); font-weight: 600; color: var(--accent-strong); background: var(--accent-soft); border: 1px solid var(--accent-ring); }
.article-text { padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); border-left: 3px solid var(--accent); background: var(--sunken); color: var(--ink-secondary); font-size: var(--text-sm); line-height: 1.75; overflow-wrap: anywhere; }
.muted-note { font-size: var(--text-sm); line-height: var(--leading-relaxed); color: var(--ink-tertiary); }

.summary-card { position: relative; overflow: hidden; padding: var(--space-6); border-radius: var(--radius-xl); border: 1px solid var(--success-border); background: linear-gradient(135deg, var(--success-bg), transparent 80%), var(--surface); box-shadow: var(--shadow-md); }
.summary-card::before { content: ''; position: absolute; inset: 0 auto 0 0; width: 5px; background: linear-gradient(180deg, #10b981, #34d399); }
.summary-card[data-state="none"] { border-color: var(--warning-border); background: linear-gradient(135deg, var(--warning-bg), transparent 80%), var(--surface); }
.summary-card[data-state="none"]::before { background: linear-gradient(180deg, #f59e0b, #fbbf24); }
.summary-card__label { display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: var(--radius-full); font: 700 var(--text-xs)/1 var(--font-mono); letter-spacing: .08em; text-transform: uppercase; color: var(--success-text); background: var(--success-bg); border: 1px solid var(--success-border); }
.summary-card[data-state="none"] .summary-card__label { color: var(--warning-text); background: var(--warning-bg); border-color: var(--warning-border); }
.summary-card__main { margin-top: var(--space-3); font-size: var(--text-xl); font-weight: 800; line-height: 1.3; letter-spacing: -.015em; text-wrap: balance; }
.summary-card__row { margin-top: var(--space-3); padding-top: var(--space-3); border-top: 1px dashed var(--line-medium); color: var(--ink-secondary); font-size: var(--text-base); line-height: var(--leading-relaxed); }
.summary-card__row strong { color: var(--ink-primary); }

.result-toolbar { display: flex; flex-wrap: wrap; gap: var(--space-2); margin-top: var(--space-4); }
.copy-btn { display: inline-flex; align-items: center; gap: 7px; padding: 8px 13px; border-radius: var(--radius-md); border: 1px solid var(--line-medium); background: var(--surface); color: var(--ink-secondary); font-size: var(--text-sm); font-weight: 600; transition: all var(--duration-fast); }
.copy-btn:hover { color: var(--accent-strong); border-color: var(--accent-ring); background: var(--accent-soft); transform: translateY(-1px); }
.copy-btn[data-copied="true"] { color: var(--success-text); border-color: var(--success-border); background: var(--success-bg); }

.disclaimer-bar { display: flex; gap: var(--space-3); align-items: flex-start; padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); background: var(--info-bg); border: 1px solid var(--info-border); color: var(--info-text); font-size: var(--text-sm); line-height: var(--leading-normal); }
.disclaimer-bar svg { flex: none; margin-top: 2px; }
.disclaimer-bar em { opacity: .85; }
.aggravating-box { margin-top: var(--space-3); padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); background: var(--warning-bg); border: 1px solid var(--warning-border); color: var(--warning-text); font-size: var(--text-sm); line-height: var(--leading-normal); }

.action-list { display: grid; gap: var(--space-3); padding: 0; list-style: none; counter-reset: a; }
.action-list li { counter-increment: a; position: relative; display: grid; gap: 3px; padding: var(--space-3) var(--space-4) var(--space-3) 52px; border-radius: var(--radius-lg); background: var(--sunken); font-size: var(--text-sm); line-height: var(--leading-normal); color: var(--ink-secondary); }
.action-list li::before { content: counter(a); position: absolute; left: 14px; top: 12px; display: grid; place-items: center; width: 26px; height: 26px; border-radius: 50%; font: 700 12px/1 var(--font-mono); color: var(--accent); background: var(--accent-soft); border: 1px solid var(--accent-ring); }
.action-list strong { color: var(--ink-primary); }

.more-details { display: flex; flex-direction: column; gap: var(--space-4); }
.more-details > summary, .raw-toggle > summary { display: flex; align-items: center; gap: var(--space-3); cursor: pointer; list-style: none; user-select: none; }
.more-details > summary::-webkit-details-marker, .raw-toggle > summary::-webkit-details-marker { display: none; }
.more-details > summary { padding: var(--space-4) var(--space-5); border-radius: var(--radius-xl); border: 1px solid var(--line-medium); background: var(--surface); box-shadow: var(--shadow-sm); font-weight: 700; transition: all var(--duration-fast); }
.more-details > summary:hover { border-color: var(--accent-ring); background: var(--accent-soft); }
.more-details > summary::after { content: ''; margin-left: auto; width: 9px; height: 9px; border-right: 2px solid var(--ink-tertiary); border-bottom: 2px solid var(--ink-tertiary); transform: rotate(45deg); transition: transform var(--duration) var(--ease); }
.more-details[open] > summary::after { transform: rotate(-135deg); }
.raw-toggle > summary { padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); border: 1px dashed var(--line-medium); font-size: var(--text-sm); font-weight: 600; color: var(--ink-tertiary); }
.raw-toggle > summary:hover { color: var(--ink-primary); }
.raw-toggle pre { margin-top: var(--space-3); padding: var(--space-4); max-height: 380px; overflow: auto; border-radius: var(--radius-lg); background: var(--sunken); border: 1px solid var(--line); font: 400 var(--text-xs)/1.7 var(--font-mono); white-space: pre-wrap; color: var(--ink-secondary); }
.results.is-exporting .result-toolbar, .results.is-exporting .raw-toggle { display: none; }

.clarify-card { padding: var(--space-6); border-radius: var(--radius-xl); border: 1px solid var(--info-border); background: linear-gradient(180deg, var(--info-bg), transparent 160px), var(--surface); box-shadow: var(--shadow-md); }
.clarify-card h3 { font-size: var(--text-lg); font-weight: 800; }
.clarify-card p { margin: 6px 0 var(--space-5); color: var(--ink-secondary); font-size: var(--text-sm); line-height: var(--leading-normal); }
.clarify-q { display: block; margin-bottom: var(--space-4); font-weight: 600; font-size: var(--text-sm); line-height: 1.45; }
.clarify-q input { display: block; width: 100%; margin-top: 8px; padding: 12px 14px; border-radius: var(--radius-md); border: 1px solid var(--line-medium); background: var(--sunken); font-weight: 400; transition: all var(--duration-fast); }
.clarify-q input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-ring); background: var(--surface); }
.clarify-actions { display: flex; gap: var(--space-3); flex-wrap: wrap; align-items: center; margin-top: var(--space-5); }
.clarify-actions .btn { min-height: 44px; }

/* ── 8. База знаний и история ─────────────────────────────── */
.kb-section, .history { padding: var(--space-6); }
.pane__head { margin-bottom: var(--space-4); }
.search-field { position: relative; display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-4); }
.search-field > svg { position: absolute; left: 14px; color: var(--ink-tertiary); pointer-events: none; }
.search-input { flex: 1; min-width: 0; height: 46px; padding: 0 14px 0 42px; border-radius: var(--radius-lg); border: 1px solid var(--line-medium); background: var(--sunken); transition: all var(--duration-fast); }
.search-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-ring); background: var(--surface); }
.kb-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: var(--space-3); }
.kb-card { display: flex; flex-direction: column; gap: var(--space-2); text-align: left; padding: var(--space-4); border-radius: var(--radius-lg); border: 1px solid var(--line); background: var(--surface-2); transition: all var(--duration) var(--ease); }
.kb-card:hover { transform: translateY(-3px); border-color: var(--accent-ring); background: var(--surface); box-shadow: var(--shadow-md); }
.kb-card__top { display: flex; gap: 10px; align-items: flex-start; }
.kb-card__number { flex: none; min-width: 42px; padding: 5px 8px; border-radius: var(--radius-sm); text-align: center; font: 700 var(--text-sm)/1 var(--font-mono); color: #fff; background: var(--grad); }
.kb-card__name { font-size: var(--text-sm); font-weight: 700; line-height: 1.35; padding-top: 2px; }
.kb-card__desc { font-size: var(--text-xs); line-height: 1.55; color: var(--ink-tertiary); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.kb-card__sentence { display: flex; align-items: center; gap: 6px; margin-top: auto; padding-top: var(--space-2); font-size: var(--text-xs); font-weight: 600; color: var(--accent); }
.empty-note { grid-column: 1 / -1; padding: var(--space-8) var(--space-4); text-align: center; font-size: var(--text-sm); color: var(--ink-tertiary); }

#historyList { display: grid; gap: var(--space-3); }
.history-item { position: relative; padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); border: 1px solid var(--line); background: var(--surface-2); cursor: pointer; transition: all var(--duration) var(--ease); }
.history-item:hover { border-color: var(--accent-ring); background: var(--surface); transform: translateX(3px); }
.history-item__title { font-weight: 700; font-size: var(--text-sm); }
.history-item__query { margin-top: 4px; font-size: var(--text-sm); line-height: 1.5; color: var(--ink-secondary); }
.history-item__time { margin-top: 8px; font: 500 var(--text-xs)/1 var(--font-mono); color: var(--ink-tertiary); }
.history-item--full::after { content: 'разбор сохранён'; position: absolute; top: 14px; right: 14px; padding: 3px 9px; border-radius: var(--radius-full); font-size: 11px; font-weight: 600; color: var(--success-text); background: var(--success-bg); border: 1px solid var(--success-border); }
.history-item--full[data-lang="tg"]::after { content: 'таҳлил захира шудааст'; }

/* Названия и тексты статей, которые переводятся на таджикский прямо сейчас */
.is-translating { opacity: .5; animation: tr-pulse 1.1s ease-in-out infinite; }
@keyframes tr-pulse { 50% { opacity: .85; } }
.tr-note { margin-top: var(--space-2); font-size: var(--text-xs); line-height: 1.5; color: var(--ink-tertiary); }
.tr-note::before { content: 'ⓘ '; }
.history-item--full .history-item__title { padding-right: 130px; }

/* ── 9. Служебное ──────────────────────────────────────────── */
.footer { padding-block: var(--space-8) var(--space-10); border-top: 1px solid var(--line); text-align: center; font-size: var(--text-sm); line-height: var(--leading-relaxed); color: var(--ink-tertiary); }
.footer p + p { margin-top: var(--space-2); font-size: var(--text-xs); }
.toast-container { position: fixed; z-index: 100; left: 50%; bottom: 24px; transform: translateX(-50%); display: grid; gap: var(--space-2); width: max-content; max-width: calc(100vw - 32px); pointer-events: none; }
.toast { display: flex; align-items: center; gap: 10px; padding: 12px 18px; border-radius: var(--radius-lg); font-size: var(--text-sm); font-weight: 600; color: #fff; background: #111827; border: 1px solid rgba(255, 255, 255, .12); box-shadow: var(--shadow-lg); animation: rise .3s var(--ease) both; }
.toast--success > span:first-child { color: #34d399; }
.toast--error > span:first-child { color: #fbbf24; }
.toast-exit { opacity: 0; transform: translateY(8px); transition: all .3s; }

/* ── 10. Адаптив ───────────────────────────────────────────── */
@media (min-width: 1024px) {
  .composer { display: flex; }
  .workspace[data-pane="input"] .pane--result { display: block; }
  .workspace[data-pane="input"] .tab[data-pane-target="result"] { background: var(--surface); color: var(--ink-primary); box-shadow: var(--shadow-sm), 0 0 0 1px var(--line); }
}
@media (max-width: 1023px) {
  .container { padding-inline: 16px; }
  .hero-content { grid-template-columns: 1fr; padding: 28px 24px; }
  .hero-visual { order: -1; min-height: 190px; }
  .scales { width: 210px; }
  .workspace { grid-template-columns: minmax(0, 1fr); grid-template-areas: none; grid-template-rows: none; padding-block: var(--space-4) var(--space-8); }
  .tabs, .composer, .pane--result, .pane--kb, .pane--history { grid-area: auto; }
  .tabs { position: sticky; top: calc(var(--topbar-h) + 8px); z-index: 20; box-shadow: var(--shadow-md); background: var(--glass); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); }
  .tab--input { display: inline-flex; }
  .results { scroll-margin-top: calc(var(--topbar-h) + 72px); }
  .composer { position: static; padding: var(--space-5); }
  .composer .input-footer { position: sticky; bottom: 0; z-index: 5; margin: 0 calc(var(--space-5) * -1) calc(var(--space-5) * -1); padding: var(--space-3) var(--space-5) var(--space-4); background: var(--glass); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border-top: 1px solid var(--line); border-radius: 0 0 var(--radius-xl) var(--radius-xl); }
  .nav-link:not([aria-current]) { display: none; }
}
@media (max-width: 720px) {
  .progress-steps { grid-template-columns: 1fr; gap: var(--space-4); }
  .progress-step { flex-direction: row; align-items: flex-start; padding-right: 0; }
  .progress-step:not(:last-child)::after { top: 40px; left: 17px; right: auto; bottom: -14px; width: 2px; height: auto; }
  .step-indicator { flex: none; }
  .result-card, .summary-card, .clarify-card, .kb-section, .history { padding: var(--space-5); }
  .hero-card { border-radius: 22px; }
}
@media (max-width: 560px) {
  .nav-link, .status-badge #statusText { display: none; }
  .status-badge { padding: 9px; }
  .mode-switch { grid-template-columns: 1fr; }
  .summary-card__main { font-size: var(--text-lg); }
  .tab { padding: 0 6px; gap: 5px; font-size: var(--text-xs); }
  .tab > svg { display: none; }
  .hero { padding-top: var(--space-4); }
  .hero-content { padding: 20px 18px 22px; gap: var(--space-3); }
  .hero-visual { min-height: 130px; }
  .scales { width: 150px; }
  .hero-subtitle, .hero-trust .trust-chip:not(.hero-badge) { display: none; }
  .hero-title { margin-top: var(--space-3); }
  .hero-trust { margin-top: var(--space-3); }
  .btn--primary kbd { display: none; }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; scroll-behavior: auto !important; }
}

/* ── Печать = PDF-справка: только результат ── */
@media print {
  body { background: #fff !important; color: #000 !important; }
  .ambient-bg, .topbar, .hero, .tabs, .composer, .pane--kb, .pane--history, .empty-state, .progress-tracker,
  .footer, .toast-container, .result-toolbar, .copy-btn, .raw-toggle { display: none !important; }
  .workspace { display: block; padding: 0; }
  .pane--result { display: block !important; }
  .result-card, .summary-card, .disclaimer-bar, .clarify-card { break-inside: avoid; box-shadow: none; background: #fff !important; color: #000 !important; border: 1px solid #999; }
  .result-card *, .summary-card *, .disclaimer-bar * { color: #000 !important; }
  .results, .results * { opacity: 1 !important; animation: none !important; transform: none !important; }
  .results::before { content: 'LegalAI Pro — справка по квалификации (УК РТ)'; display: block; font: 700 16pt/1.3 serif; margin-bottom: 8pt; }
}
</style>
</head>
<body>

<div class="ambient-bg" aria-hidden="true"></div>

<div class="app-shell">

  <!-- ═══ ШАПКА ═══ -->
  <header class="topbar">
    <div class="container">
      <a href="/" class="brand" aria-label="LegalAI Pro">
        <span class="brand__mark" aria-hidden="true">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M6 7l-3.5 7a3.5 3.5 0 0 0 7 0L6 7Zm12 0l-3.5 7a3.5 3.5 0 0 0 7 0L18 7ZM4 21h16M4.5 7h15"/></svg>
        </span>
        <span>LegalAI</span><span class="brand__pro">PRO</span>
      </a>
      <nav class="nav-actions" aria-label="Основная навигация">
        <a href="/" class="nav-link" data-i18n="nav.home">← Главная</a>
        <span class="nav-link" aria-current="page" data-i18n="nav.uk">УК РТ</span>
        <button id="langToggle" class="lang-toggle" type="button" aria-label="Язык интерфейса / Забони интерфейс" title="Русский / Тоҷикӣ">
          <span data-lang="ru">RU</span><span data-lang="tg">ТҶ</span>
        </button>
        <button id="statusBadge" class="status-badge" data-state="checking" aria-live="polite" title="Статус RAG-сервера">
          <span class="status-dot" aria-hidden="true"></span>
          <span id="statusText">Проверка...</span>
        </button>
        <button id="themeToggle" class="theme-toggle" aria-label="Переключить тему" type="button">
          <span class="theme-toggle__thumb" id="themeThumb" aria-hidden="true">
            <svg id="themeIconMoon" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M20.354 15.354A9 9 0 0 1 8.646 3.646 9.003 9.003 0 1 0 20.354 15.354Z"/></svg>
            <svg id="themeIconSun" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
          </span>
        </button>
      </nav>
    </div>
  </header>

  <div class="container">

    <!-- ═══ HERO ═══ -->
    <section class="hero" aria-label="Описание сервиса">
      <div class="hero-card">
        <div class="hero-content">
          <div class="hero-text">
            <div class="hero-eyebrow" data-i18n="hero.eyebrow">Уголовный кодекс Республики Таджикистан</div>
            <h1 class="hero-title" data-i18n="hero.title">Квалификация преступления с RAG-анализом УК РТ</h1>
            <p class="hero-subtitle">
              <span data-i18n="hero.sub1">Опишите ситуацию — система найдёт нормы в тексте УК РТ, а</span>
              <strong id="modelLabel" data-i18n="hero.model">модель</strong>
              <span data-i18n="hero.sub2">разберёт состав только по найденному. Если подходящей нормы в кодексе нет, ответа не будет.</span>
            </p>
            <div class="hero-trust">
              <span class="trust-chip hero-badge">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                <span id="heroBadgeText" data-i18n="hero.badge">Отвечает только по тексту кодекса</span>
              </span>
              <span class="trust-chip">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                <span data-i18n="trust.2">Сверка ссылок</span>
              </span>
              <span class="trust-chip">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg>
                <span data-i18n="trust.3">RU · ТҶ</span>
              </span>
            </div>
          </div>

          <div class="hero-visual" id="heroVisual" aria-hidden="true">
            <div class="scales">
              <div class="scales__glow"></div>
              <div class="scales__tilt" id="scalesTilt">
                <svg class="scales__svg" viewBox="0 0 320 290" role="presentation" focusable="false">
                  <defs>
                    <linearGradient id="sg-gold" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0" stop-color="#fff3c4"/><stop offset=".42" stop-color="#fbbf24"/><stop offset="1" stop-color="#b45309"/>
                    </linearGradient>
                    <linearGradient id="sg-gold-h" x1="0" y1="0" x2="1" y2="0">
                      <stop offset="0" stop-color="#b45309"/><stop offset=".3" stop-color="#fde68a"/><stop offset=".55" stop-color="#f59e0b"/><stop offset="1" stop-color="#92400e"/>
                    </linearGradient>
                    <linearGradient id="sg-steel" x1="0" y1="0" x2="1" y2="0">
                      <stop offset="0" stop-color="#1e1b4b"/><stop offset=".32" stop-color="#c7d2fe"/><stop offset=".55" stop-color="#6366f1"/><stop offset="1" stop-color="#1e1b4b"/>
                    </linearGradient>
                    <linearGradient id="sg-base" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0" stop-color="#818cf8"/><stop offset="1" stop-color="#312e81"/>
                    </linearGradient>
                    <radialGradient id="sg-pan-in" cx=".5" cy=".3" r=".8">
                      <stop offset="0" stop-color="#fde68a"/><stop offset="1" stop-color="#b45309"/>
                    </radialGradient>
                  </defs>

                  <!-- Основание и колонна -->
                  <ellipse cx="160" cy="276" rx="92" ry="8" fill="#0a0640" opacity=".45"/>
                  <rect x="86" y="258" width="148" height="14" rx="7" fill="url(#sg-base)"/>
                  <path d="M104 258c0-12 10-20 24-20h64c14 0 24 8 24 20z" fill="url(#sg-base)"/>
                  <path d="M112 252c4-8 12-10 20-10h56" fill="none" stroke="#e0e7ff" stroke-opacity=".5" stroke-width="2" stroke-linecap="round"/>
                  <rect x="151" y="48" width="18" height="196" rx="9" fill="url(#sg-steel)"/>
                  <rect x="155" y="56" width="3" height="180" rx="1.5" fill="#fff" opacity=".35"/>
                  <rect x="146" y="226" width="28" height="12" rx="6" fill="url(#sg-gold-h)"/>

                  <!-- Верхняя «шишка» -->
                  <circle cx="160" cy="38" r="12" fill="url(#sg-gold)"/>
                  <circle cx="156" cy="34" r="4" fill="#fff" opacity=".7"/>

                  <!-- Коромысло с чашами: качается, чаши остаются ровными -->
                  <g class="scales__beam">
                    <rect x="30" y="52" width="260" height="11" rx="5.5" fill="url(#sg-gold-h)"/>
                    <rect x="36" y="54" width="248" height="2.5" rx="1.25" fill="#fff" opacity=".5"/>
                    <circle cx="160" cy="57" r="10" fill="url(#sg-gold)"/>
                    <circle cx="160" cy="57" r="4" fill="#78350f"/>
                    <circle cx="30" cy="57" r="7" fill="url(#sg-gold)"/>
                    <circle cx="290" cy="57" r="7" fill="url(#sg-gold)"/>

                    <g class="scales__pan scales__pan--l">
                      <path d="M46 64 L14 160 M46 64 L78 160" stroke="#fde68a" stroke-opacity=".85" stroke-width="1.6" stroke-linecap="round" fill="none"/>
                      <ellipse cx="46" cy="160" rx="38" ry="7" fill="url(#sg-pan-in)"/>
                      <path d="M8 160c0 20 17 32 38 32s38-12 38-32z" fill="url(#sg-gold)"/>
                      <path d="M16 165c3 12 14 19 30 19" fill="none" stroke="#fff" stroke-opacity=".5" stroke-width="2" stroke-linecap="round"/>
                    </g>
                    <g class="scales__pan scales__pan--r">
                      <path d="M274 64 L242 160 M274 64 L306 160" stroke="#fde68a" stroke-opacity=".85" stroke-width="1.6" stroke-linecap="round" fill="none"/>
                      <ellipse cx="274" cy="160" rx="38" ry="7" fill="url(#sg-pan-in)"/>
                      <path d="M236 160c0 20 17 32 38 32s38-12 38-32z" fill="url(#sg-gold)"/>
                      <path d="M244 165c3 12 14 19 30 19" fill="none" stroke="#fff" stroke-opacity=".5" stroke-width="2" stroke-linecap="round"/>
                    </g>
                  </g>
                </svg>
              </div>
              <i class="scales__spark"></i><i class="scales__spark"></i><i class="scales__spark"></i>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ РАБОЧАЯ ОБЛАСТЬ ═══ -->
    <main class="workspace" id="workspace" data-pane="result">

      <!-- Вкладки правой колонки (на телефоне — вся навигация) -->
      <div class="tabs" id="paneTabs" role="tablist" aria-label="Разделы">
        <button class="tab tab--input" type="button" role="tab" data-pane-target="input" aria-selected="false">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
          <span data-i18n="tab.input">Описание</span>
        </button>
        <button class="tab" type="button" role="tab" data-pane-target="result" aria-selected="true">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v18M6 7l-3.5 7a3.5 3.5 0 0 0 7 0L6 7Zm12 0l-3.5 7a3.5 3.5 0 0 0 7 0L18 7ZM4 21h16M4.5 7h15"/></svg>
          <span data-i18n="tab.result">Результат</span>
        </button>
        <button class="tab" type="button" role="tab" data-pane-target="kb" aria-selected="false">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
          <span data-i18n="tab.kb">База знаний</span>
        </button>
        <button class="tab" type="button" role="tab" data-pane-target="history" aria-selected="false">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg>
          <span data-i18n="tab.history">История</span>
          <span class="tab__count" id="historyCount" hidden>0</span>
        </button>
      </div>

      <!-- ── Левая колонка: ввод ── -->
      <section class="composer surface" aria-labelledby="panel-title">
        <div class="composer__head">
          <div>
            <div class="composer__step" data-i18n="composer.step">Шаг 1</div>
            <h2 class="panel__title" id="panel-title" data-i18n="panel.title">Опишите преступление</h2>
            <p class="panel__subtitle" data-i18n="panel.subtitle">Чем подробнее описание — тем точнее квалификация по статьям УК РТ</p>
          </div>
          <button id="btnClear" class="icon-btn" type="button">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
            <span data-i18n="composer.clear">Очистить</span>
          </button>
        </div>

        <div id="apiWarning" class="alert alert--warning" style="display:none" role="alert">
          <svg class="alert__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          <div>
            <strong data-i18n="warn.title">Индекс УК РТ недоступен.</strong> <span data-i18n="warn.body">Запустите python app.py и убедитесь, что собран индекс кодексов.</span>
            <br><small data-i18n="warn.small">Страница отвечает только по тексту УК РТ, поэтому без индекса анализ не выполняется.</small>
          </div>
        </div>

        <div class="scenarios">
          <div class="scenarios__label" data-i18n="scen.label">Быстрые сценарии</div>
          <div class="scenarios__grid" id="scenarioGrid" role="list"></div>
        </div>

        <div class="input-area">
          <label for="crimeInput" class="sr-only">Описание преступления</label>
          <textarea
            id="crimeInput"
            class="crime-input"
            data-i18n-placeholder="input.placeholder"
            placeholder="Например: Вчера вечером на улице Рудаки неизвестный мужчина вырвал у меня сумку из рук и убежал. В сумке был телефон, документы и 2000 сомони..."
            maxlength="2000"
            aria-describedby="charCount"
          ></textarea>
          <span class="char-count" id="charCount" aria-live="polite">0 / 2000</span>
        </div>

        <div class="mode-switch" role="radiogroup" aria-label="Режим квалификации" id="modeSwitch">
          <button type="button" class="mode-switch__opt" role="radio" data-mode="short" aria-checked="false">
            <strong data-i18n="mode.short">Краткая</strong>
            <span data-i18n="mode.shortHint">Статья, наказание и 2–3 предложения. Быстрее.</span>
          </button>
          <button type="button" class="mode-switch__opt" role="radio" data-mode="full" aria-checked="true">
            <strong data-i18n="mode.full">Полная</strong>
            <span data-i18n="mode.fullHint">Состав, факты, обстоятельства, анализ и вывод.</span>
          </button>
        </div>

        <div class="input-footer">
          <div class="btn-group">
            <button id="btnQualify" class="btn btn--primary" type="button">
              <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              <span>Квалифицировать</span>
              <kbd>Ctrl+↵</kbd>
            </button>
            <button id="btnAnalyze" class="btn btn--link" type="button" data-i18n="btn.onlySearch" title="Только поиск статей, без разбора моделью">Только найти статьи</button>
          </div>
        </div>

        <div class="composer__note">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
          <div>
            <span data-i18n="panel.source">Источник ответа — только Уголовный кодекс Республики Таджикистан из RAG-индекса</span>
            <span data-i18n="composer.hint">Не указывайте персональные данные — достаточно фактов события.</span>
          </div>
        </div>
      </section>

      <!-- ── Правая колонка: результат ── -->
      <section class="pane pane--result" aria-label="Результат">
        <div id="progressTracker" class="progress-tracker surface" aria-live="polite">
          <div class="progress-tracker__title" data-i18n="prog.title">Анализ преступления...</div>
          <div class="progress-steps">
            <div class="progress-step" data-step="clarify">
              <div class="step-indicator">1</div>
              <div class="step-content">
                <div class="step-label" data-i18n="prog.clarify">Проверка полноты описания</div>
                <div class="step-detail" data-detail>Ожидание...</div>
              </div>
            </div>
            <div class="progress-step" data-step="retrieval">
              <div class="step-indicator">2</div>
              <div class="step-content">
                <div class="step-label" data-i18n="prog.retrieval">Поиск норм в тексте УК РТ</div>
                <div class="step-detail" data-detail>Ожидание...</div>
              </div>
            </div>
            <div class="progress-step" data-step="llm">
              <div class="step-indicator">3</div>
              <div class="step-content">
                <div class="step-label" data-i18n="prog.llm">Разбор моделью</div>
                <div class="step-detail" data-detail>Ожидание...</div>
              </div>
            </div>
            <div class="progress-step" data-step="validation">
              <div class="step-indicator">4</div>
              <div class="step-content">
                <div class="step-label" data-i18n="prog.validation">Сверка ссылок с выдачей поиска</div>
                <div class="step-detail" data-detail>Ожидание...</div>
              </div>
            </div>
          </div>
        </div>

        <div id="results" class="results" aria-live="polite"></div>

        <div class="empty-state">
          <div class="empty-state__icon">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v18M6 7l-3.5 7a3.5 3.5 0 0 0 7 0L6 7Zm12 0l-3.5 7a3.5 3.5 0 0 0 7 0L18 7ZM4 21h16M4.5 7h15"/></svg>
          </div>
          <h3 data-i18n="empty.title">Здесь появится квалификация</h3>
          <p data-i18n="empty.text">Опишите событие слева и нажмите «Квалифицировать». Система найдёт нормы в УК РТ и разберёт состав по их тексту.</p>
          <ol class="empty-steps">
            <li data-i18n="empty.s1">Опишите событие</li>
            <li data-i18n="empty.s2">Поиск норм в УК РТ</li>
            <li data-i18n="empty.s3">Разбор и проверка ссылок</li>
          </ol>
        </div>
      </section>

      <!-- ── База знаний ── -->
      <section class="pane pane--kb kb-section surface" aria-labelledby="kb-title">
        <div class="pane__head">
          <h2 class="panel__title" id="kb-title" style="margin-top:0" data-i18n="kb.title">База знаний УК РТ</h2>
          <p class="panel__subtitle" data-i18n="kb.sub">Статьи загружаются из RAG-индекса кодекса. Кликните — номер подставится в форму.</p>
        </div>
        <div class="search-field">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <label for="kbFilter" class="sr-only">Поиск по статьям УК РТ</label>
          <input id="kbFilter" class="search-input" type="search" autocomplete="off"
                 data-i18n-placeholder="kb.filter" placeholder="Поиск по номеру, названию статьи или главе — например: кража, 248, против собственности">
          <span class="char-count" id="kbCount" aria-live="polite"></span>
        </div>
        <div class="kb-grid" id="kbGrid" role="list"></div>
      </section>

      <!-- ── История ── -->
      <section class="pane pane--history history surface" id="historyPanel" aria-labelledby="hist-title">
        <div class="pane__head">
          <h2 class="panel__title" id="hist-title" style="margin-top:0" data-i18n="hist.title">История анализов</h2>
        </div>
        <div id="historyList"></div>
      </section>

    </main>

    <!-- ═══ ПОДВАЛ ═══ -->
    <footer class="footer">
      <p id="footerModel" data-i18n="foot.model">LegalAI Pro отвечает только по тексту УК РТ.</p>
      <p data-i18n="foot.disclaimer">Информация носит ознакомительный характер. Для точной консультации обратитесь к адвокату.</p>
    </footer>

  </div>
</div>

<div class="toast-container" id="toastContainer" aria-live="assertive"></div>

<script>
'use strict';

const CONFIG = Object.freeze({
  API_BASE: "{{ rtrim($pythonApiUrl ?? env('PYTHON_API_URL', 'http://127.0.0.1:5000'), '/') }}",
  MAX_INPUT_LENGTH: 2000,
  RAG_TOP_K: 8,
  RETRY_MAX: 3,
  RETRY_BASE_DELAY: 800,
  TIMEOUT_SHORT: 5000,
  TIMEOUT_MEDIUM: 15000,
  TIMEOUT_FIRST_BYTE: 90000,
  DEBOUNCE_MS: 300,
  HISTORY_MAX: 8,
  KB_VISIBLE: 24,
  SEARCH_VISIBLE: 5,
});

const ICON_PATHS = Object.freeze({
  shield: '<rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
  scale: '<path d="M12 3v18M6 7l-3.5 7a3.5 3.5 0 0 0 7 0L6 7Zm12 0l-3.5 7a3.5 3.5 0 0 0 7 0L18 7ZM4 21h16M4.5 7h15"/>',
  book: '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>',
  search: '<circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
  pen: '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
  clock: '<circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/>',
  flag: '<path d="M4 3v18"/><path d="M4 4h13l-2.5 4L17 12H4"/>',
  brain: '<path d="M9.5 3a3.5 3.5 0 0 0-3.5 3.5v.6A3 3 0 0 0 4 10v1a3 3 0 0 0 1.7 2.7A3.5 3.5 0 0 0 9 19a3.5 3.5 0 0 0 3.5-3.5v-9A3 3 0 0 0 9.5 3Z"/><path d="M14.5 3a3.5 3.5 0 0 1 3.5 3.5v.6a3 3 0 0 1 2 2.9v1a3 3 0 0 1-1.7 2.7A3.5 3.5 0 0 1 15 19a3.5 3.5 0 0 1-3.5-3.5v-9A3 3 0 0 1 14.5 3Z"/>',
  check: '<path d="M20 6 9 17l-5-5"/>',
  alert: '<path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
  terminal: '<polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/>',
  copy: '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1"/>',
  download: '<path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M5 21h14"/>',
  printer: '<path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="9" rx="2"/><path d="M7 14h10v7H7z"/>',
});

function icon(name, size = 18) {
  const paths = ICON_PATHS[name] || ICON_PATHS.book;
  return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${paths}</svg>`;
}


const I18N = {
  ru: {
    'nav.home': '← Главная', 'nav.uk': 'УК РТ',
    'hero.eyebrow': 'Уголовный кодекс Республики Таджикистан',
    'hero.title': 'Квалификация преступления с RAG-анализом УК РТ',
    'hero.sub1': 'Опишите ситуацию — система найдёт нормы в тексте УК РТ, а',
    'hero.sub2': 'разберёт состав только по найденному. Если подходящей нормы в кодексе нет, ответа не будет.',
    'hero.model': 'ИИ-ассистент', 'hero.modelCloud': 'ИИ-ассистент', 'hero.modelLocal': 'ИИ-ассистент',
    'hero.badge': 'Отвечает только по тексту кодекса',
    'hero.badgeLocal': 'Отвечает только по тексту кодекса',
    'hero.badgeCloud': 'Отвечает только по тексту кодекса',
    'panel.source': 'Источник ответа — только Уголовный кодекс Республики Таджикистан из RAG-индекса',
    'panel.title': 'Опишите преступление',
    'panel.subtitle': 'Чем подробнее описание — тем точнее квалификация по статьям УК РТ',
    'warn.title': 'Индекс УК РТ недоступен.',
    'warn.body': 'Запустите python app.py и убедитесь, что собран индекс кодексов.',
    'warn.small': 'Страница отвечает только по тексту УК РТ, поэтому без индекса анализ не выполняется.',
    'scen.label': 'Быстрые сценарии',
    'input.placeholder': 'Например: Вчера вечером на улице Рудаки неизвестный мужчина вырвал у меня сумку из рук и убежал. В сумке был телефон, документы и 2000 сомони...',
    'btn.qualify': 'Квалифицировать', 'btn.onlySearch': 'Только найти статьи', 'btn.busy': 'Обработка...',
    'mode.short': 'Краткая', 'mode.shortHint': 'Что делать, что грозит нарушителю и какие статьи нарушены. Быстрее.',
    'mode.full': 'Полная', 'mode.fullHint': 'То же подробно: шаги по УПК, состав, факты, обстоятельства, анализ и вывод.',
    'sec.todo': 'Что делать', 'sec.todo.t': 'Если вы пострадавший или свидетель',
    'sec.threat': 'Что грозит нарушителю', 'sec.threat.t': 'Наказание по тексту статьи',
    'sec.laws': 'Какие законы нарушены', 'sec.laws.t': 'Статьи УК РТ',
    'card.upk': 'Текст статьи УПК РТ',
    'sec.short': 'Обоснование', 'sec.short.t': 'Почему подходит эта статья',
    'understood': 'Система поняла ваше описание (машинный перевод) так:',
    'understoodHint': 'Если перевод исказил смысл, опишите событие проще и короче.',
    'prog.title': 'Анализ преступления...', 'prog.clarify': 'Проверка полноты описания',
    'prog.retrieval': 'Поиск норм в тексте УК РТ', 'prog.llm': 'Разбор моделью',
    'prog.validation': 'Сверка ссылок с выдачей поиска',
    'p.wait': 'Ожидание...', 'p.checking': 'Проверяю, хватает ли фактов...', 'p.needFacts': 'Нужно уточнить фактов: {n}',
    'p.enough': '✓ Фактов достаточно', 'p.skipped': '— пропущено',
    'p.searching': 'Поиск норм в тексте УК РТ...', 'p.waitFirst': 'Ожидание первых строк ответа...',
    'p.found': '✓ Найдено норм: {n}', 'p.inBasis': '✓ Статей в опоре: {n}',
    'p.notUsed': '— не используется: ответ из индекса', 'p.allFromCode': '✓ Все ссылки из текста кодекса',
    'p.notFound': '✗ Норма не найдена', 'p.noModel': '— модель не запускалась', 'p.noBasis': '✓ Ответ без опоры не выдан',
    'p.modelWork': 'модель готовит ответ, {s} с...', 'p.chunks': '{n} фрагментов...', 'p.got': '✓ Разбор получен',
    'p.badLinks': '✗ Ссылок мимо RAG: {n}', 'p.linksOk': '✓ Все ссылки подтверждены',
    'hist.title': 'История анализов',
    'kb.title': 'База знаний УК РТ',
    'kb.sub': 'Статьи загружаются из RAG-индекса кодекса. Кликните — номер подставится в форму.',
    'kb.filter': 'Поиск по номеру, названию статьи или главе — например: кража, 248, против собственности',
    'foot.model': 'LegalAI Pro отвечает только по тексту УК РТ.',
    'foot.local': 'LegalAI Pro отвечает только по тексту УК РТ.',
    'foot.cloud': 'LegalAI Pro отвечает только по тексту УК РТ.',
    'foot.disclaimer': 'Информация носит ознакомительный характер. Для точной консультации обратитесь к адвокату.',
    'err.noServer': 'Сервер не ответил — проверьте, запущен ли python app.py',
    'err.check': 'Что проверить:', 'err.check1': 'Запущен ли Python RAG API (python app.py) и доступна ли модель',
    'err.check2': 'Собран ли индекс УК РТ — статус виден в шапке страницы',
    'err.check3': 'Описано ли событие достаточно подробно',
    'sum.label': 'Итог', 'sum.punish': 'Наказание по статье', 'sum.punishNone': 'в приведённом фрагменте статьи не указано',
    'sum.none': 'Квалификацию по найденным статьям установить нельзя',
    'sum.noneHint': 'Опишите событие подробнее (что произошло, чем, с каким результатом) или проверьте найденные статьи ниже.',
    'sum.pdf': 'Скачать PDF', 'sum.copy': 'Копировать', 'sum.copied': '✓ Скопировано',
    'disc': 'Предварительная оценка по тексту УК РТ. Не заменяет консультацию адвоката: окончательную квалификацию дают следователь и суд.',
    'more.sum': 'Подробный разбор и тексты статей',
    'cl.title': 'Нужно уточнить несколько фактов',
    'cl.sub': 'От них зависит, какая часть и пункт статьи подойдут. На любой вопрос можно не отвечать.',
    'cl.ph': 'Ваш ответ', 'cl.go': 'Ответить и квалифицировать', 'cl.skip': 'Квалифицировать как есть',
    'card.support': 'Опора ответа', 'card.analysis': 'Разбор', 'card.writing': 'Модель пишет ответ по найденным нормам',
    'card.normText': 'Текст нормы из индекса',
    'sec.qual': 'Квалификация', 'sec.qual.t': 'Определение состава преступления',
    'sec.sanc': 'Санкции', 'sec.sanc.t': 'Наказание по тексту статьи УК РТ',
    'sec.facts': 'Факты', 'sec.facts.t': 'Доказательственная база',
    'sec.circ': 'Обстоятельства', 'sec.circ.t': 'Смягчающие и отягчающие',
    'sec.anal': 'Правовой анализ', 'sec.anal.t': 'Юридическое обоснование',
    'sec.verd': 'Вывод', 'sec.verd.t': 'Итоговое заключение',
    'raw': 'Полный ответ модели', 'unverified': 'Не подтверждено RAG:',
    'status.checking': 'Проверка...', 'status.ok': '✓ УК РТ: {n} статей', 'status.bad': '✗ Индекс УК РТ недоступен',
    'toast.copied': 'Результаты скопированы', 'toast.copyFail': 'Не удалось скопировать',
    'toast.hist': 'Открыт сохранённый разбор из истории',
    'sum.download': 'Скачать .txt', 'toast.downloaded': 'Файл сохранён',
    'tab.input': 'Описание', 'tab.result': 'Результат', 'tab.kb': 'База знаний', 'tab.history': 'История',
    'trust.1': 'Только текст кодекса', 'trust.2': 'Сверка ссылок', 'trust.3': 'RU · ТҶ',
    'composer.step': 'Шаг 1', 'composer.clear': 'Очистить', 'composer.hint': 'Не указывайте персональные данные — достаточно фактов события.',
    'empty.title': 'Здесь появится квалификация',
    'empty.text': 'Опишите событие слева и нажмите «Квалифицировать». Система найдёт нормы в УК РТ и разберёт состав по их тексту.',
    'empty.s1': 'Опишите событие', 'empty.s2': 'Поиск норм в УК РТ', 'empty.s3': 'Разбор и проверка ссылок',
    'hist.empty': 'Пока пусто. Ваши разборы сохраняются в этом браузере.',
    'kb.empty': 'Индекс УК РТ недоступен — база знаний не загружена.', 'kb.none': 'По запросу в УК РТ ничего не найдено.',
    'foot.brand': 'LegalAI Pro',
    'kb.shown': 'показано {a} из {b}', 'kb.count': '{n} ст.', 'kb.chapter': 'Глава {n}',
    'kb.prefill': 'Событие по признакам статьи {n} УК РТ ({title}): ',
    'src.label': 'Источник ответа', 'src.code': 'Уголовный кодекс Республики Таджикистан',
    'art.abbr': 'Ст.', 'art.uk': 'УК РТ', 'art.upk': 'УПК РТ', 'art.upkShort': 'УПК',
    'norm.default': 'Норма УК РТ', 'norm.closest': 'Наиболее близкая норма', 'norm.also': 'Также найдено',
    'search.note': 'Нормы найдены поиском по тексту кодекса и приведены дословно, без участия языковой модели. Порядок — по совпадению с описанием. Для разбора состава и санкций нажмите «Квалифицировать».',
    'rel.label': 'Смежные нормы · справочно', 'rel.title': 'Другие кодексы РТ по теме описания',
    'rel.note': 'Не входят в квалификацию по УК РТ. Показаны нормы об ответственности и возмещении вреда из областей права, названных в описании; применимость к вашему случаю проверьте по тексту кодекса.',
    'rag.label': 'Источники RAG', 'rag.title': 'Остальные нормы, найденные по описанию',
    'nf.label': 'Норма не найдена', 'nf.title': 'В УК РТ нет статьи под это описание', 'nf.query': 'Запрос:',
    'err.label': 'Ошибка анализа', 'err.search': 'Ошибка поиска по УК РТ', 'err.qualify': 'Ошибка квалификации',
    'err.noIndex': 'Индекс УК РТ недоступен',
    'err.noIndexBody': 'Страница отвечает только по тексту Уголовного кодекса РТ из RAG-индекса. Пока индекс не загружен, ответ дать неоткуда.',
    'err.noBasis': 'Сервер не прислал опору ответа',
    'unverified.tail': '— в найденных фрагментах УК РТ и УПК РТ отсутствуют, проверьте их по тексту кодекса.',
    'cl.added': 'Уточнения:',
    'tr.machine': 'Машинный перевод; официальный текст кодекса — русский.',
    'tr.failed': 'Часть текста перевести не удалось — показан русский оригинал.',
    'fb.title': 'Ответ модели без разбора на разделы',
  },
  tg: {
    'nav.home': '← Асосӣ', 'nav.uk': 'КҶ ҶТ',
    'hero.eyebrow': 'Кодекси ҷиноятии Ҷумҳурии Тоҷикистон',
    'hero.title': 'Тасниф кардани ҷиноят бо таҳлили RAG-и КҶ ҶТ',
    'hero.sub1': 'Вазъиятро тавсиф кунед — система меъёрҳоро дар матни КҶ ҶТ меёбад ва',
    'hero.sub2': 'таркиби ҷиноятро танҳо аз рӯи ёфташуда таҳлил мекунад. Агар меъёри мувофиқ дар кодекс набошад, ҷавоб дода намешавад.',
    'hero.model': 'ёрдамчии AI', 'hero.modelCloud': 'ёрдамчии AI', 'hero.modelLocal': 'ёрдамчии AI',
    'hero.badge': 'Танҳо аз рӯи матни кодекс ҷавоб медиҳад',
    'hero.badgeLocal': 'Танҳо аз рӯи матни кодекс ҷавоб медиҳад',
    'hero.badgeCloud': 'Танҳо аз рӯи матни кодекс ҷавоб медиҳад',
    'panel.source': 'Сарчашмаи ҷавоб — танҳо Кодекси ҷиноятии Ҷумҳурии Тоҷикистон аз индекси RAG',
    'panel.title': 'Ҷиноятро тавсиф кунед',
    'panel.subtitle': 'Ҳар қадар тавсиф муфассалтар бошад, таснифи аз рӯи моддаҳои КҶ ҶТ ҳамон қадар дақиқтар аст',
    'warn.title': 'Индекси КҶ ҶТ дастнорас аст.',
    'warn.body': 'python app.py-ро оғоз кунед ва боварӣ ҳосил намоед, ки индекси кодексҳо сохта шудааст.',
    'warn.small': 'Саҳифа танҳо аз рӯи матни КҶ ҶТ ҷавоб медиҳад, бинобар ин бе индекс таҳлил иҷро намешавад.',
    'scen.label': 'Сенарияҳои зуд',
    'input.placeholder': 'Масалан: Шаби гузашта дар кӯчаи Рӯдакӣ марди ношинос ҳамёнамро аз дастам кашида гирифт ва гурехт. Дар он телефон, ҳуҷҷатҳо ва 2000 сомонӣ буд...',
    'btn.qualify': 'Тасниф кардан', 'btn.onlySearch': 'Танҳо ёфтани моддаҳо', 'btn.busy': 'Коркард...',
    'mode.short': 'Мухтасар', 'mode.shortHint': 'Чӣ кор кардан лозим, ба ҷинояткор чӣ таҳдид мекунад ва кадом моддаҳо вайрон шудаанд. Зудтар.',
    'mode.full': 'Пурра', 'mode.fullHint': 'Ҳамин тавр муфассал: қадамҳо аз рӯи КМҶ, таркиб, далелҳо, ҳолатҳо, таҳлил ва хулоса.',
    'sec.todo': 'Чӣ кор кардан лозим', 'sec.todo.t': 'Агар шумо зарардида ё шоҳид бошед',
    'sec.threat': 'Ба ҷинояткор чӣ таҳдид мекунад', 'sec.threat.t': 'Ҷазо аз рӯи матни модда',
    'sec.laws': 'Кадом қонунҳо вайрон шудаанд', 'sec.laws.t': 'Моддаҳои КҶ ҶТ',
    'card.upk': 'Матни моддаи КМҶ ҶТ',
    'sec.short': 'Асосноккунӣ', 'sec.short.t': 'Чаро ин модда мувофиқ аст',
    'understood': 'Система тавсифи шуморо (тарҷумаи мошинӣ) чунин фаҳмид:',
    'understoodHint': 'Агар тарҷума маънои онро вайрон кардааст, ҳодисаро соддатар ва кӯтоҳтар тавсиф кунед.',
    'prog.title': 'Таҳлили ҷиноят...', 'prog.clarify': 'Санҷиши пуррагии тавсиф',
    'prog.retrieval': 'Ҷустуҷӯи меъёрҳо дар матни КҶ ҶТ', 'prog.llm': 'Таҳлили модел',
    'prog.validation': 'Муқоисаи истинодҳо бо натиҷаи ҷустуҷӯ',
    'p.wait': 'Интизорӣ...', 'p.checking': 'Месанҷам, ки далелҳо кифоя ҳастанд...', 'p.needFacts': 'Далелҳои возеҳкунӣ: {n}',
    'p.enough': '✓ Далелҳо кифоя аст', 'p.skipped': '— гузаронида шуд',
    'p.searching': 'Ҷустуҷӯи меъёрҳо дар матни КҶ ҶТ...', 'p.waitFirst': 'Интизори сатрҳои аввали ҷавоб...',
    'p.found': '✓ Меъёрҳои ёфташуда: {n}', 'p.inBasis': '✓ Моддаҳо дар такягоҳ: {n}',
    'p.notUsed': '— истифода намешавад: ҷавоб аз индекс', 'p.allFromCode': '✓ Ҳамаи истинодҳо аз матни кодекс',
    'p.notFound': '✗ Меъёр ёфт нашуд', 'p.noModel': '— модел оғоз нашуд', 'p.noBasis': '✓ Ҷавоб бе такягоҳ дода намешавад',
    'p.modelWork': 'модел ҷавобро тайёр мекунад, {s} сония...', 'p.chunks': '{n} порча...', 'p.got': '✓ Таҳлил гирифта шуд',
    'p.badLinks': '✗ Истинодҳои берун аз RAG: {n}', 'p.linksOk': '✓ Ҳамаи истинодҳо тасдиқ шуданд',
    'hist.title': 'Таърихи таҳлилҳо',
    'kb.title': 'Пойгоҳи дониши КҶ ҶТ',
    'kb.sub': 'Моддаҳо аз индекси RAG-и кодекс бор карда мешаванд. Клик кунед — рақами модда ба форма гузошта мешавад. Матни моддаҳо — таҳрири расмии русии кодекс.',
    'kb.filter': 'Ҷустуҷӯ аз рӯи рақам, номи модда ё боб — масалан: дуздӣ, 248, бар зидди моликият',
    'foot.model': 'LegalAI Pro танҳо аз рӯи матни КҶ ҶТ ҷавоб медиҳад.',
    'foot.local': 'LegalAI Pro танҳо аз рӯи матни КҶ ҶТ ҷавоб медиҳад.',
    'foot.cloud': 'LegalAI Pro танҳо аз рӯи матни КҶ ҶТ ҷавоб медиҳад.',
    'foot.disclaimer': 'Маълумот хусусияти иттилоотӣ дорад. Барои машварати дақиқ ба адвокат муроҷиат кунед.',
    'err.noServer': 'Сервер ҷавоб надод — санҷед, ки python app.py оғоз шудааст',
    'err.check': 'Чиро санҷидан лозим:', 'err.check1': 'Python RAG API (python app.py) оғоз шудааст ва модел дастрас аст',
    'err.check2': 'Индекси КҶ ҶТ сохта шудааст — ҳолат дар сарлавҳаи саҳифа намоён аст',
    'err.check3': 'Ҳодиса ба қадри кифоя муфассал тавсиф шудааст',
    'sum.label': 'Хулоса', 'sum.punish': 'Ҷазо аз рӯи модда', 'sum.punishNone': 'дар порчаи овардашудаи модда нишон дода нашудааст',
    'sum.none': 'Аз рӯи моддаҳои ёфташуда тасниф муқаррар карда намешавад',
    'sum.noneHint': 'Ҳодисаро муфассалтар тавсиф кунед (чӣ рӯй дод, бо чӣ, бо кадом натиҷа) ё моддаҳои ёфташударо дар поён бубинед.',
    'sum.pdf': 'Боргирии PDF', 'sum.copy': 'Нусха бардоштан', 'sum.copied': '✓ Нусха шуд',
    'disc': 'Баҳои пешакӣ аз рӯи матни КҶ ҶТ. Маслиҳати адвокатро иваз намекунад: таснифи ниҳоиро муфаттиш ва суд медиҳад.',
    'more.sum': 'Таҳлили муфассал ва матни моддаҳо',
    'cl.title': 'Чанд далелро бояд возеҳ кард',
    'cl.sub': 'Аз онҳо вобаста аст, ки кадом қисм ва банди модда мувофиқ меояд. Ба ҳар савол метавонед ҷавоб надиҳед.',
    'cl.ph': 'Ҷавоби шумо', 'cl.go': 'Ҷавоб додан ва тасниф кардан', 'cl.skip': 'Ҳамин тавр тасниф кардан',
    'card.support': 'Такягоҳи ҷавоб', 'card.analysis': 'Таҳлил', 'card.writing': 'Модел ҷавобро аз рӯи меъёрҳои ёфташуда менависад',
    'card.normText': 'Матни меъёр аз индекс',
    'sec.qual': 'Тасниф', 'sec.qual.t': 'Муайян кардани таркиби ҷиноят',
    'sec.sanc': 'Санксияҳо', 'sec.sanc.t': 'Ҷазо аз рӯи матни моддаи КҶ ҶТ',
    'sec.facts': 'Далелҳо', 'sec.facts.t': 'Пойгоҳи исбот',
    'sec.circ': 'Ҳолатҳо', 'sec.circ.t': 'Сабуккунанда ва вазнинкунанда',
    'sec.anal': 'Таҳлили ҳуқуқӣ', 'sec.anal.t': 'Асосноккунии ҳуқуқӣ',
    'sec.verd': 'Хулоса', 'sec.verd.t': 'Хулосаи ниҳоӣ',
    'raw': 'Ҷавоби пурраи модел', 'unverified': 'RAG тасдиқ накардааст:',
    'status.checking': 'Санҷиш...', 'status.ok': '✓ КҶ ҶТ: {n} модда', 'status.bad': '✗ Индекси КҶ ҶТ дастнорас аст',
    'toast.copied': 'Натиҷа нусха шуд', 'toast.copyFail': 'Нусха бардошта нашуд',
    'toast.hist': 'Таҳлили захирашуда аз таърих кушода шуд',
    'sum.download': 'Боргирии .txt', 'toast.downloaded': 'Файл захира шуд',
    'tab.input': 'Тавсиф', 'tab.result': 'Натиҷа', 'tab.kb': 'Пойгоҳи дониш', 'tab.history': 'Таърих',
    'trust.1': 'Танҳо матни кодекс', 'trust.2': 'Санҷиши истинодҳо', 'trust.3': 'RU · ТҶ',
    'composer.step': 'Қадами 1', 'composer.clear': 'Пок кардан', 'composer.hint': 'Маълумоти шахсиро ишора накунед — далелҳои ҳодиса кифоя аст.',
    'empty.title': 'Таснифот дар ин ҷо пайдо мешавад',
    'empty.text': 'Ҳодисаро дар тарафи чап тавсиф кунед ва «Тасниф кардан»-ро пахш намоед. Система меъёрҳоро дар КҶ ҶТ меёбад ва таркибро аз рӯи матни онҳо таҳлил мекунад.',
    'empty.s1': 'Ҳодисаро тавсиф кунед', 'empty.s2': 'Ҷустуҷӯи меъёрҳо дар КҶ ҶТ', 'empty.s3': 'Таҳлил ва санҷиши истинодҳо',
    'hist.empty': 'Ҳоло холӣ аст. Таҳлилҳои шумо дар ин браузер захира мешаванд.',
    'kb.empty': 'Индекси КҶ ҶТ дастнорас аст — пойгоҳи дониш бор нашудааст.', 'kb.none': 'Аз рӯи дархост дар КҶ ҶТ чизе ёфт нашуд.',
    'foot.brand': 'LegalAI Pro',
    'kb.shown': 'нишон дода шуд {a} аз {b}', 'kb.count': '{n} модда', 'kb.chapter': 'Боби {n}',
    'kb.prefill': 'Ҳодиса аз рӯи аломатҳои моддаи {n} КҶ ҶТ ({title}): ',
    'src.label': 'Сарчашмаи ҷавоб', 'src.code': 'Кодекси ҷиноятии Ҷумҳурии Тоҷикистон',
    'art.abbr': 'Моддаи', 'art.uk': 'КҶ ҶТ', 'art.upk': 'КМҶ ҶТ', 'art.upkShort': 'КМҶ',
    'norm.default': 'Меъёри КҶ ҶТ', 'norm.closest': 'Меъёри наздиктарин', 'norm.also': 'Инчунин ёфт шуд',
    'search.note': 'Меъёрҳо бо ҷустуҷӯ дар матни кодекс ёфта шудаанд ва айнан оварда мешаванд, бе иштироки модели забонӣ. Тартиб — аз рӯи мувофиқат бо тавсиф. Барои таҳлили таркиб ва санксияҳо «Тасниф кардан»-ро пахш кунед.',
    'rel.label': 'Меъёрҳои алоқаманд · барои маълумот', 'rel.title': 'Кодексҳои дигари ҶТ аз рӯи мавзӯи тавсиф',
    'rel.note': 'Ба таснифи КҶ ҶТ дохил намешаванд. Меъёрҳо дар бораи масъулият ва ҷуброни зарар аз соҳаҳои ҳуқуқие, ки дар тавсиф номбар шудаанд, оварда шудаанд; татбиқшавии онҳо ба ҳолати шуморо аз рӯи матни кодекс санҷед.',
    'rag.label': 'Сарчашмаҳои RAG', 'rag.title': 'Меъёрҳои дигаре, ки аз рӯи тавсиф ёфт шуданд',
    'nf.label': 'Меъёр ёфт нашуд', 'nf.title': 'Дар КҶ ҶТ моддаи мувофиқ ба ин тавсиф нест', 'nf.query': 'Дархост:',
    'err.label': 'Хатои таҳлил', 'err.search': 'Хатои ҷустуҷӯ дар КҶ ҶТ', 'err.qualify': 'Хатои тасниф',
    'err.noIndex': 'Индекси КҶ ҶТ дастнорас аст',
    'err.noIndexBody': 'Саҳифа танҳо аз рӯи матни Кодекси ҷиноятии ҶТ аз индекси RAG ҷавоб медиҳад. То бор нашудани индекс ҷавоб додан имконнопазир аст.',
    'err.noBasis': 'Сервер такягоҳи ҷавобро нафиристод',
    'unverified.tail': '— дар порчаҳои ёфташудаи КҶ ҶТ ва КМҶ ҶТ вуҷуд надоранд, онҳоро аз рӯи матни кодекс санҷед.',
    'cl.added': 'Возеҳкуниҳо:',
    'tr.machine': 'Тарҷумаи мошинӣ; матни расмии кодекс — русӣ.',
    'tr.failed': 'Тарҷумаи як қисми матн муваффақ нашуд — нусхаи русӣ нишон дода шуд.',
    'fb.title': 'Ҷавоби модел бе тақсим ба бахшҳо',
  },
};

// Режим квалификации хранится в браузере: человек выбирает его один раз.
let CRIME_MODE = (() => { try { return localStorage.getItem('legalai-crime-mode') === 'short' ? 'short' : 'full'; } catch { return 'full'; } })();

function applyMode() {
  document.querySelectorAll('#modeSwitch [data-mode]').forEach(btn => {
    btn.setAttribute('aria-checked', String(btn.dataset.mode === CRIME_MODE));
  });
}

// Язык интерфейса — общий для всего сайта: его выбирает сервер (сессия), а не эта страница.
let UI_LANG = "{{ app()->getLocale() === 'tg' ? 'tg' : 'ru' }}";
const LANG_URLS = {
  ru: "{{ route('locale.switch', ['locale' => 'ru']) }}",
  tg: "{{ route('locale.switch', ['locale' => 'tg']) }}",
};
// Состояние ИИ-сервиса с сервера (/health). Названия моделей на странице не показываются.
let CRIME_LLM = null;

// Язык результата — язык, на котором написано описание (его определяет сервер и
// присылает в ответе). Оболочка страницы живёт по UI_LANG, а карточки ответа — по OUT_LANG:
// вопрос на таджикском получает ответ строго на таджикском, на русском — на русском.
let OUT_LANG = UI_LANG;

function t(key, vars, lang = UI_LANG) {
  let text = (I18N[lang] && I18N[lang][key]) ?? I18N.ru[key] ?? key;
  if (vars) {
    for (const name of Object.keys(vars)) text = text.split('{' + name + '}').join(String(vars[name]));
  }
  return text;
}

// Строка из блока результата: на языке запроса.
function tr(key, vars) {
  return t(key, vars, OUT_LANG);
}

// Первая догадка о языке описания до ответа сервера (для ошибок и «уточнений»):
// таджикские буквы ӣ ӯ қ ғ ҳ ҷ в русском тексте не встречаются.
function guessLang(text) {
  return /[ӣӯқғҳҷӢӮҚҒҲҶ]/.test(String(text || '')) ? 'tg' : 'ru';
}

function setOutLang(lang) {
  OUT_LANG = lang === 'tg' ? 'tg' : lang === 'ru' ? 'ru' : UI_LANG;
}

// ── Перевод названий и текстов статей на таджикский ─────────────────────────
// Индекс УК РТ русский. Для таджикского запроса названия и тексты статей переводит
// сервер (/api/crime/translate); переводы кэшируются в браузере, чтобы повторно не ждать.
// Если перевод не вышел, остаётся русский оригинал, а человеку об этом сказано.
const TgText = {
  KEY: 'legalai_tg_cache_v1',
  MAX_ENTRIES: 400,
  SHORT: 400,
  CHUNK: 20,
  mem: new Map(),
  _loaded: false,

  _load() {
    if (this._loaded) return;
    this._loaded = true;
    try {
      const saved = JSON.parse(localStorage.getItem(this.KEY));
      if (Array.isArray(saved)) saved.forEach(([k, v]) => this.mem.set(k, v));
    } catch { /* пустой или недоступный кэш */ }
  },

  _save() {
    try {
      const entries = [...this.mem.entries()].slice(-this.MAX_ENTRIES);
      localStorage.setItem(this.KEY, JSON.stringify(entries));
    } catch { /* переполнение или приватный режим: кэш только в памяти */ }
  },

  cached(text) {
    this._load();
    return this.mem.get(String(text || '').trim());
  },

  async _request(items) {
    const response = await Utils.fetchWithRetry(`${CONFIG.API_BASE}/api/crime/translate`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ items }),
      timeout: 150000,
    }, 1);
    return response.json();
  },

  // Возвращает { items, failed }: items — переводы в том же порядке (оригинал, если не вышло).
  async translate(texts) {
    this._load();
    const source = texts.map(s => String(s || '').trim());
    const out = source.slice();
    const pending = new Map();                       // уникальный текст -> индексы
    source.forEach((text, i) => {
      if (!text) return;
      if (this.mem.has(text)) { out[i] = this.mem.get(text); return; }
      if (!pending.has(text)) pending.set(text, []);
      pending.get(text).push(i);
    });
    if (pending.size === 0) return { items: out, failed: 0 };

    const short = [], long = [];
    for (const text of pending.keys()) (text.length <= this.SHORT ? short : long).push(text);
    const batches = [];
    for (let i = 0; i < short.length; i += this.CHUNK) batches.push(short.slice(i, i + this.CHUNK));
    long.forEach(text => batches.push([text]));      // длинные тексты идут параллельно, по одному

    let failed = 0;
    await Promise.all(batches.map(async batch => {
      try {
        const data = await this._request(batch);
        batch.forEach((text, k) => {
          const value = data.items && data.items[k];
          if (data.translated && data.translated[k] && value && value !== text) {
            this.mem.set(text, value);
            pending.get(text).forEach(i => { out[i] = value; });
          } else {
            failed += 1;
          }
        });
      } catch {
        failed += batch.length;
      }
    }));
    this._save();
    return { items: out, failed };
  },

  // Заменяет текст узлов с data-tr переводом; узлы с длинными текстами получают пометку «машинный перевод».
  // Возвращает обещание, которое выполнится, когда все переданные узлы переведены (в том числе
  // те, перевод которых уже идёт): экспорт текста ждёт именно его.
  async apply(nodes) {
    const fresh = nodes.filter(n => n && !n.dataset.trDone);
    if (fresh.length) {
      fresh.forEach(n => {
        n.dataset.trDone = '1';
        n.dataset.trSrc = n.textContent;
        n.classList.add('is-translating');
      });
      const job = this._run(fresh);
      fresh.forEach(n => { n._trJob = job; });
    }
    await Promise.all([...new Set(nodes.map(n => n && n._trJob).filter(Boolean))]);
  },

  async _run(list) {
    const { items, failed } = await this.translate(list.map(n => n.dataset.trSrc));
    list.forEach((n, i) => {
      n.classList.remove('is-translating');
      const translated = items[i] && items[i] !== n.dataset.trSrc.trim();
      if (translated) {
        n.textContent = items[i];
        if (n.dataset.tr === 'text' && !n.nextElementSibling?.classList.contains('tr-note')) {
          const note = document.createElement('div');
          note.className = 'tr-note';
          note.textContent = t('tr.machine', null, 'tg');
          n.after(note);
        }
      }
    });
    if (failed > 0) UIRenderer.showToast(t('tr.failed', null, 'tg'), 'error');
  },
};

function applyProviderText() {
  const cloud = !!(CRIME_LLM && CRIME_LLM.cloud);
  const model = CRIME_LLM ? CRIME_LLM.model : '';
  const set = (id, value) => { const el = document.getElementById(id); if (el) el.textContent = value; };
  set('modelLabel', !CRIME_LLM ? t('hero.model') : cloud ? t('hero.modelCloud', { model }) : t('hero.modelLocal'));
  set('heroBadgeText', !CRIME_LLM ? t('hero.badge') : cloud ? t('hero.badgeCloud') : t('hero.badgeLocal'));
  set('footerModel', !CRIME_LLM ? t('foot.model') : cloud ? t('foot.cloud', { model }) : t('foot.local'));
}

function applyI18n() {
  const root = document.documentElement;
  root.setAttribute('data-ui-lang', UI_LANG);
  root.lang = UI_LANG === 'tg' ? 'tg' : 'ru';
  document.querySelectorAll('[data-i18n]').forEach(el => { el.textContent = t(el.dataset.i18n); });
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => { el.placeholder = t(el.dataset.i18nPlaceholder); });
  const label = document.querySelector('#btnQualify span');
  if (label && !label.closest('button').disabled) label.textContent = t('btn.qualify');
  applyProviderText();
}

const SCENARIOS = Object.freeze({
  theft: { label: 'Карманная кража', labelTg: 'Дуздии кисабурӣ', textTg: 'Имрӯз дар метро дар вақти серодамӣ касе аз ҷайби куртаам телефони iPhone 15-ро кашида гирифт. Ман танҳо баъди аз вагон баромадан гум шудани онро пай бурдам.', text: 'Сегодня в метро в час пик кто-то вытащил у меня из кармана куртки телефон iPhone 15. Я заметил пропажу только когда вышел из вагона.' },
  robbery: { label: 'Уличный грабёж', labelTg: 'Ғоратгарии кӯчагӣ', textTg: 'Шаби гузашта дар кӯчаи Рӯдакӣ дар Душанбе марди ношинос ба наздам давида омад, сумкаро аз дастам кашида гирифт ва гурехт. Дар сумка ҳуҷҷатҳо, телефон ва 3000 сомонӣ буд.', text: 'Вчера вечером на улице Рудаки в Душанбе неизвестный мужчина подбежал ко мне, вырвал сумку из рук и убежал. В сумке были документы, телефон и 3000 сомони.' },
  assault: { label: 'Нападение с ножом', labelTg: 'Ҳамла бо корд', textTg: 'Ду нафари ношинос дар кӯчаи торик ба ман ҳамла карданд. Яке корд дошта, таҳдид мекард, ки маро мекушад, дигаре ҳамён ва соати маро гирифт. Дастам аз корд захмдор шуд.', text: 'На меня напали двое неизвестных в тёмном переулке. Один держал нож и угрожал убить, второй забрал кошелёк и часы. Я получил ножевое ранение в руку.' },
  extortion: { label: 'Вымогательство', labelTg: 'Ғасб (тамагирӣ)', textTg: 'Се марди бадансоз назди ман омада, 10000 доллар талаб карданд ва таҳдид намуданд, ки агар пулро фардо наорам, бо оилаам ҳисобу китоб мекунанд.', text: 'Ко мне подошли трое мужчин спортивного телосложения и потребовали 10000 долларов, угрожая расправой над моей семьёй, если я не принесу деньги завтра.' },
  fraud: { label: 'Мошенничество', labelTg: 'Қаллобӣ', textTg: 'Аз «бонк» занг заданд ва гуфтанд, ки ҳисоби ман басташуда аст. Бо дастури қаллоб ман ҳамаи пасандозҳоямро (50000 сомонӣ) ба «ҳисоби бехатар» гузарондам.', text: 'Мне позвонили из "банка" и сказали, что мой счёт заблокирован. Под диктовку мошенника я перевёл все свои сбережения (50000 сомони) на "безопасный счёт".' },
  battery: { label: 'Побои', labelTg: 'Латукӯб', textTg: 'Дар бар задухӯрд шуд. Мард се бор бо мушт ба рӯи ман зад. Шоҳидон ҳастанд, кабудшавӣ ва харошҳо сабт шудаанд, аз пункти травматологӣ маълумотнома дорам.', text: 'В баре возник конфликт. Мужчина ударил меня кулаком по лицу три раза. Свидетели есть, зафиксированы синяки и ссадины, есть справка из травмпункта.' },
});

const Utils = {
  escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = String(str ?? '');
    return div.innerHTML;
  },

  debounce(fn, ms) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), ms);
    };
  },

  async sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  },

  async fetchWithRetry(url, options = {}, maxRetries = CONFIG.RETRY_MAX) {
    let lastError;
    for (let attempt = 0; attempt < maxRetries; attempt++) {
      try {
        const controller = new AbortController();
        const timeout = options.timeout || CONFIG.TIMEOUT_SHORT;
        const timeoutId = setTimeout(() => controller.abort(), timeout);
        
        const response = await fetch(url, {
          ...options,
          signal: controller.signal,
        });
        
        clearTimeout(timeoutId);
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response;
      } catch (err) {
        lastError = err;
        if (attempt < maxRetries - 1) {
          const delay = CONFIG.RETRY_BASE_DELAY * Math.pow(2, attempt);
          await Utils.sleep(delay);
        }
      }
    }
    throw lastError;
  },

};

class CrimeRAG {
  constructor() {
    this.ready = false;
    this.status = null;
    this.catalog = [];
  }

  async checkStatus() {
    try {
      const response = await Utils.fetchWithRetry(
        `${CONFIG.API_BASE}/api/rag/status`,
        { timeout: CONFIG.TIMEOUT_MEDIUM }
      );
      const data = await response.json();
      this.status = data.crime || null;
      this.ready = !!(this.status && this.status.ready);
    } catch {
      this.status = null;
      this.ready = false;
    }
    return this.status;
  }

  async loadCatalog() {
    if (!this.ready) return [];
    try {
      const response = await Utils.fetchWithRetry(
        `${CONFIG.API_BASE}/api/crime/catalog`,
        { timeout: CONFIG.TIMEOUT_MEDIUM }
      );
      const data = await response.json();
      this.catalog = Array.isArray(data.articles) ? data.articles : [];
    } catch {
      this.catalog = [];
    }
    return this.catalog;
  }

  async search(description) {
    const response = await Utils.fetchWithRetry(
      `${CONFIG.API_BASE}/api/crime/search`,
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ description, top_k: CONFIG.RAG_TOP_K }),
        timeout: CONFIG.TIMEOUT_MEDIUM,
      },
      1
    );
    return response.json();
  }
}

class CrimeService {
  // Чего не хватает описанию для точной квалификации. Любой сбой (таймаут,
  // 5xx, не-JSON) даёт пустой список: уточнение — надстройка, она не вправе
  // остановить человека, если модель не ответила.
  async clarify(description) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), 50000);
    try {
      const response = await fetch(`${CONFIG.API_BASE}/api/crime/clarify`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ description, lang: UI_LANG }),
        signal: controller.signal,
      });
      if (!response.ok) return { questions: [], lang: null };
      const data = await response.json();
      return {
        questions: data.enough === false && Array.isArray(data.questions) ? data.questions.slice(0, 3) : [],
        lang: data.lang || null,
      };
    } catch {
      return { questions: [], lang: null };
    } finally {
      clearTimeout(timer);
    }
  }

  async loadProvider() {
    try {
      const response = await Utils.fetchWithRetry(`${CONFIG.API_BASE}/health`, { timeout: CONFIG.TIMEOUT_MEDIUM }, 1);
      const data = await response.json();
      return data.crime_llm || null;
    } catch {
      return null;
    }
  }

  async qualifyStream(description, { onMeta, onChunk, onWait, onDone }) {
    const controller = new AbortController();
    let firstByte = false;
    const guard = setTimeout(() => {
      if (!firstByte) controller.abort();
    }, CONFIG.TIMEOUT_FIRST_BYTE);

    let response;
    try {
      response = await fetch(`${CONFIG.API_BASE}/api/crime/qualify`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/x-ndjson',
        },
        body: JSON.stringify({ description, mode: CRIME_MODE }),
        signal: controller.signal,
      });
    } catch (err) {
      clearTimeout(guard);
      throw new Error(firstByte ? err.message : t('err.noServer'));
    }

    const contentType = response.headers.get('content-type') || '';
    if (!response.ok || !response.body || contentType.includes('application/json')) {
      clearTimeout(guard);
      let payload = null;
      try { payload = await response.json(); } catch {}
      if (payload) return payload;
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let buffer = '';
    let meta = null;
    const collected = [];
    let tail = {};

    let plainPayload = null;
    const handle = line => {
      if (!line.trim()) return;
      let obj;
      try { obj = JSON.parse(line); } catch { return; }
      if (obj.meta) {
        meta = obj.meta;
        onMeta?.(meta);
        return;
      }
      if (obj.wait !== undefined) {
        onWait?.(obj.wait);
        return;
      }
      if (obj.response) {
        collected.push(obj.response);
        onChunk?.(obj.response);
      }
      if (obj.done) tail = obj;
      if (obj.meta === undefined && obj.response === undefined && !obj.done) {
        plainPayload = obj;
      }
    };

    while (true) {
      const { done, value } = await reader.read();
      if (done) break;
      firstByte = true;
      clearTimeout(guard);
      buffer += decoder.decode(value, { stream: true });
      const lines = buffer.split('\n');
      buffer = lines.pop() || '';
      lines.forEach(handle);
    }
    handle(buffer);
    clearTimeout(guard);

    if (!meta) {
      if (plainPayload) return plainPayload;
      throw new Error(tr('err.noBasis'));
    }

    const data = {
      ...meta,
      qualification: collected.join('') + (tail.note || ''),
      unverified_articles: tail.unverified_articles || [],
      summary: tail.summary || null,
    };
    onDone?.(data);
    return data;
  }
}

class UIRenderer {
  static renderScenarios() {
    const grid = document.getElementById('scenarioGrid');
    grid.innerHTML = Object.entries(SCENARIOS).map(([key, s]) => `
      <button class="scenario-chip" data-scenario="${key}" type="button" role="listitem">
        ${Utils.escapeHtml(UI_LANG === 'tg' ? (s.labelTg || s.label) : s.label)}
      </button>
    `).join('');
  }

  static renderKnowledgeBase(articles, filter = '') {
    const grid = document.getElementById('kbGrid');
    const counter = document.getElementById('kbCount');
    const query = filter.trim().toLowerCase();

    if (!Array.isArray(articles) || articles.length === 0) {
      grid.innerHTML = `<p class="empty-note">${Utils.escapeHtml(t('kb.empty'))}</p>`;
      if (counter) counter.textContent = '';
      return;
    }

    // В таджикском интерфейсе ищем и по уже полученным переводам: человек вводит «дуздӣ», а не «кража».
    const hay = value => {
      const own = String(value || '').toLowerCase();
      const translated = UI_LANG === 'tg' ? TgText.cached(value) : '';
      return translated ? own + ' ' + translated.toLowerCase() : own;
    };
    const matched = query
      ? articles.filter(a =>
          String(a.article).includes(query)
          || hay(a.article_title).includes(query)
          || hay(a.chapter_title).includes(query))
      : articles;

    const shown = matched.slice(0, CONFIG.KB_VISIBLE);
    if (counter) {
      counter.textContent = matched.length > shown.length
        ? t('kb.shown', { a: shown.length, b: matched.length })
        : t('kb.count', { n: matched.length });
    }

    if (shown.length === 0) {
      grid.innerHTML = `<p class="empty-note">${Utils.escapeHtml(t('kb.none'))}</p>`;
      return;
    }

    grid.innerHTML = shown.map(a => `
      <button class="kb-card" data-article="${Utils.escapeHtml(a.article)}" type="button" role="listitem">
        <div class="kb-card__top">
          <span class="kb-card__number">${Utils.escapeHtml(a.article)}</span>
          <span class="kb-card__name" data-tr="title">${Utils.escapeHtml(a.article_title || '')}</span>
        </div>
        <div class="kb-card__desc" data-tr="snippet">${Utils.escapeHtml(a.snippet || '')}</div>
        <div class="kb-card__sentence">${icon('book', 12)} <span ${a.chapter_title ? 'data-tr="title"' : ''}>${Utils.escapeHtml(a.chapter_title || t('kb.chapter', { n: a.chapter || '—' }))}</span></div>
      </button>
    `).join('');

    if (UI_LANG === 'tg') TgText.apply([...grid.querySelectorAll('[data-tr]')]);
  }

  // «Ст. 247 — Название» / «Моддаи 247 — Название»: подпись по языку ответа, название — узел для
  // перевода (индекс кодекса русский, на таджикский его переводит сервер).
  static _artLine(a, { upk = false } = {}) {
    // Неразрывный пробел в конце: в inline-flex бейдже обычный хвостовой пробел схлопывается.
    const prefix = (upk ? tr('art.upkShort') + ' · ' : '') + tr('art.abbr') + ' ' + a.article + ' — ';
    return `${Utils.escapeHtml(prefix)}<span data-tr="title">${Utils.escapeHtml(a.article_title || '')}</span>`;
  }

  static _artBadge(a, opts) {
    return `<span class="article-badge">${UIRenderer._artLine(a, opts)}</span>`;
  }

  // Название кодекса-источника: основное — готовой строкой, остальные — через перевод.
  static _sourceName(name) {
    if (!name || name === 'Уголовный кодекс Республики Таджикистан') return Utils.escapeHtml(tr('src.code'));
    return `<span data-tr="title">${Utils.escapeHtml(name)}</span>`;
  }

  static _articleCard(article, { label, iconName = 'book', verdict = false, index = 0, upk = false } = {}) {
    const code = tr(upk ? 'art.upk' : 'art.uk');
    return `
      <div class="result-card ${verdict ? 'result-card--verdict' : ''}" style="--i:${index}">
        <div class="result-card__header">
          <div class="result-card__icon">${icon(iconName)}</div>
          <div style="flex:1">
            <div class="result-card__label">${Utils.escapeHtml(label || tr('norm.default'))}</div>
            <div class="result-card__title" data-tr="title">${Utils.escapeHtml(article.article_title || '')}</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:var(--space-3);flex-wrap:wrap;margin-bottom:var(--space-4)">
          <span class="article-badge">${Utils.escapeHtml(tr('art.abbr') + ' ' + article.article + ' ' + code)}</span>
          <span style="color:var(--ink-tertiary);font-size:var(--text-sm)" data-tr="title">${Utils.escapeHtml(article.citation || '')}</span>
        </div>
        <div class="article-text" style="white-space:pre-wrap" data-tr="text">${Utils.escapeHtml(article.text || article.snippet || '')}</div>
      </div>
    `;
  }

  // Нормы других кодексов РТ: только справочно. Квалификация и проверка ссылок
  // идут строго по УК РТ, а здесь показаны нормы об ответственности и
  // возмещении вреда из областей права, названных в описании (врач — здравоохранение,
  // налоги — Налоговый кодекс и т.д.).
  static _relatedCard(norms, index = 0) {
    const list = Array.isArray(norms) ? norms : [];
    if (list.length === 0) return '';
    // Выдержка начинается с «Статья N. Название» — заголовок уже показан выше.
    const short = (text, title = '') => {
      let value = String(text || '').replace(/\s+/g, ' ').trim().replace(/^Статья\s+[\d.\-–—]+\.?\s*/i, '');
      if (title && value.startsWith(title)) value = value.slice(title.length).trim();
      return value.length > 260 ? value.slice(0, 260).replace(/\s\S*$/, '') + '…' : value;
    };
    return `
      <div class="result-card" style="--i:${index}">
        <div class="result-card__header">
          <div class="result-card__icon">${icon('book')}</div>
          <div>
            <div class="result-card__label">${Utils.escapeHtml(tr('rel.label'))}</div>
            <div class="result-card__title">${Utils.escapeHtml(tr('rel.title'))}</div>
          </div>
        </div>
        <div class="muted-note" style="margin-bottom:var(--space-3)">${Utils.escapeHtml(tr('rel.note'))}</div>
        <ol class="action-list">
          ${list.map(a => `
            <li>
              <strong>${UIRenderer._artLine(a)}</strong>
              <span style="display:block;color:var(--ink-tertiary);font-size:var(--text-sm)"><span data-tr="title">${Utils.escapeHtml(a.code || '')}</span>${a.citation ? ' · <span data-tr="title">' + Utils.escapeHtml(a.citation.replace(/^.*?,\s*/, '')) + '</span>' : ''}</span>
              <span data-tr="snippet">${Utils.escapeHtml(short(a.snippet || a.text, a.article_title || ''))}</span>
            </li>
          `).join('')}
        </ol>
      </div>
    `;
  }

  static _sourcesCard(articles, selectedNumber) {
    const rest = (articles || []).filter(a => String(a.article) !== String(selectedNumber));
    if (rest.length === 0) return '';
    return `
      <div class="result-card" style="--i:0">
        <div class="result-card__header">
          <div class="result-card__icon">${icon('search')}</div>
          <div>
            <div class="result-card__label">${Utils.escapeHtml(tr('rag.label'))}</div>
            <div class="result-card__title">${Utils.escapeHtml(tr('rag.title'))}</div>
          </div>
        </div>
        <ol class="action-list">
          ${rest.map(a => `
            <li>
              <strong>${UIRenderer._artLine(a)}</strong>
              <span data-tr="snippet">${Utils.escapeHtml(a.snippet || '')}</span>
            </li>
          `).join('')}
        </ol>
      </div>
    `;
  }

  // Предупреждение стоит прямо над ответом, а не в подвале страницы: для
  // уголовной темы это условие использования результата, а не мелкий шрифт.
  // Одна статья приходит несколькими фрагментами индекса — в бейджах она одна.
  static _uniqueArticles(articles) {
    const seen = new Set();
    return (articles || []).filter(a => !seen.has(String(a.article)) && seen.add(String(a.article)));
  }

  static _disclaimer() {
    return `
      <div class="disclaimer-bar" role="note">
        ${icon('alert', 16)}
        <span>${Utils.escapeHtml(tr('disc'))}</span>
      </div>`;
  }

  // Подписи разделов модели остаются русскими, чтобы страница могла разобрать ответ. В показе
  // (полный текст, «ответ без разбора») они заменяются таджикскими, когда ответ на таджикском.
  static _showHeadings(text) {
    if (OUT_LANG !== 'tg') return text;
    const map = [
      [/ЧТО\s+ДЕЛАТЬ\s+ПОСТРАДАВШЕМУ/g, 'ЗАРАРДИДА ЧӢ КОР КАРДАН ЛОЗИМ'],
      [/ЧТО\s+ГРОЗИТ\s+НАРУШИТЕЛЮ/g, 'БА НАРУШИТЕЛ ЧӢ ТАҲДИД МЕКУНАД'],
      [/КАКИЕ\s+ЗАКОНЫ\s+НАРУШЕНЫ/g, 'КАДОМ ҚОНУНҲО ВАЙРОН ШУДААНД'],
      [/САНКЦИИ\s+ПО\s+ТЕКСТУ\s+СТАТЬИ/g, 'САНКСИЯҲО АЗ РӮИ МАТНИ МОДДА'],
      [/ФАКТЫ\s+И\s+ДОКАЗАТЕЛЬСТВА/g, 'ДАЛЕЛҲО ВА ИСБОТҲО'],
      [/ПРАВОВОЙ\s+АНАЛИЗ/g, 'ТАҲЛИЛИ ҲУҚУҚӢ'],
      [/ИТОГОВЫЙ\s+ВЫВОД/g, 'ХУЛОСАИ НИҲОӢ'],
      [/ОБСТОЯТЕЛЬСТВА/g, 'ҲОЛАТҲО'],
      [/САНКЦИИ/g, 'САНКСИЯҲО'],
      [/КВАЛИФИКАЦИЯ/g, 'ТАСНИФ'],
    ];
    return map.reduce((acc, [re, to]) => acc.replace(re, to), String(text || ''));
  }

  // Короткий итог для человека без юридического образования. Берётся из
  // «ИТОГОВЫЙ ВЫВОД» (строки «Квалификация:» и «Наказание по статье:»), а при
  // их отсутствии — из разделов 1 и 2. Разбор модели не переписывается.
  static _summary(sections) {
    const pick = (src, re) => { const m = (src || '').match(re); return m ? m[1].trim() : ''; };
    const clean = value => value.replace(/^[\s•\-–*]+/, '').replace(/\*\*/g, '').trim();
    const negative = /(невозможно|нельзя|не удалось|не указан|не определ|недостаточно|отсутствует|не найден)/i;

    const qual = clean(
      pick(sections['вывод'], /Квалификация:\s*([^\n]+)/i)
      || pick(sections['квалификация'], /Основная статья[^:\n]*:\s*([^\n]+)/i)
    );
    const punish = clean(
      pick(sections['вывод'], /Наказание по статье:\s*([^\n]+)/i)
      || pick(sections['санкции'], /Основное наказание:\s*([^\n]+)/i)
    );
    return {
      qual,
      punish: negative.test(punish) ? '' : punish,
      none: !qual || negative.test(qual),
    };
  }

  // Описание на таджикском сервер переводит машинно, и перевод бывает неточным
  // («мушт» -> «нож»). Показываем, что именно ушло в разбор, чтобы человек
  // заметил искажение до того, как поверит итогу.
  static _understoodNote(data, description) {
    const used = String((data && data.query) || '').trim();
    const sent = String(description || '').replace(/\s+/g, ' ').trim();
    if (!used || used.replace(/\s+/g, ' ') === sent) return '';
    return `
      <div class="disclaimer-bar" role="note">
        ${icon('search', 16)}
        <span><strong>${Utils.escapeHtml(tr('understood'))}</strong>
          «${Utils.escapeHtml(used.slice(0, 500))}»<br>
          <em>${Utils.escapeHtml(tr('understoodHint'))}</em></span>
      </div>`;
  }

  // Копировать, скачать .txt, сохранить PDF (печать). Подписи — в span, чтобы
  // при «Скопировано» менялся только текст, а значок оставался.
  static _toolbar() {
    const btn = (action, iconName, key) => `
      <button class="copy-btn" data-action="${action}" type="button">
        ${icon(iconName, 15)}<span class="copy-btn__label">${Utils.escapeHtml(tr(key))}</span>
      </button>`;
    return `
      <div class="result-toolbar">
        ${btn('copy', 'copy', 'sum.copy')}
        ${btn('download', 'download', 'sum.download')}
        ${btn('print', 'printer', 'sum.pdf')}
      </div>`;
  }

  // Итог берётся с сервера: он посчитан по русскому разбору до перевода (метки «Квалификация:»
  // и «Наказание по статье:» после перевода меняются) и уже переведён на язык запроса.
  // Для сохранённых ранее разборов без summary — прежний разбор по тексту.
  static _summaryCard(sections, summary) {
    const sum = summary && typeof summary.qual === 'string'
      ? { qual: summary.qual, punish: summary.punish || '', none: !!summary.none }
      : UIRenderer._summary(sections);
    return `
      <div class="summary-card" data-state="${sum.none ? 'none' : 'ok'}" style="--i:0">
        <div class="summary-card__label">${icon(sum.none ? 'alert' : 'check', 14)}<span>${Utils.escapeHtml(tr('sum.label'))}</span></div>
        <div class="summary-card__main">${Utils.escapeHtml(sum.none ? tr('sum.none') : sum.qual)}</div>
        ${sum.none
          ? `<div class="summary-card__row">${Utils.escapeHtml(tr('sum.noneHint'))}</div>`
          : `<div class="summary-card__row"><strong>${Utils.escapeHtml(tr('sum.punish'))}:</strong>
               ${Utils.escapeHtml(sum.punish || tr('sum.punishNone'))}</div>`}
        ${UIRenderer._toolbar()}
      </div>`;
  }

  static renderClarify(questions, description, lang = null) {
    if (lang) setOutLang(lang);
    const container = document.getElementById('results');
    container.innerHTML = `
      <div class="clarify-card" style="--i:0">
        <h3>${Utils.escapeHtml(tr('cl.title'))}</h3>
        <p>${Utils.escapeHtml(tr('cl.sub'))}</p>
        <form id="clarifyForm" autocomplete="off">
          ${questions.map((q, i) => `
            <label class="clarify-q">${Utils.escapeHtml(q)}
              <input type="text" name="q${i}" data-question="${Utils.escapeHtml(q)}" maxlength="200"
                     placeholder="${Utils.escapeHtml(tr('cl.ph'))}">
            </label>`).join('')}
          <div class="clarify-actions">
            <button class="btn btn--primary" type="submit" id="clarifyGo"><span>${Utils.escapeHtml(tr('cl.go'))}</span></button>
            <button class="btn btn--link" type="button" id="clarifySkip">${Utils.escapeHtml(tr('cl.skip'))}</button>
          </div>
        </form>
      </div>`;
    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    container.querySelector('input')?.focus();
  }

  static renderSearch(data) {
    setOutLang(data.lang);
    const articles = data.articles || [];
    const container = document.getElementById('results');
    const top = articles[0] || {};
    const rest = articles.slice(1, CONFIG.SEARCH_VISIBLE);

    container.innerHTML = `
      ${UIRenderer._disclaimer()}
      <div class="result-card" style="--i:0">
        <div class="result-card__header">
          <div class="result-card__icon">${icon('shield')}</div>
          <div style="flex:1">
            <div class="result-card__label">${Utils.escapeHtml(tr('src.label'))}</div>
            <div class="result-card__title">${Utils.escapeHtml(tr('src.code'))}</div>
          </div>
        </div>
        <div class="badge-row">
          ${UIRenderer._uniqueArticles(articles).map(a => UIRenderer._artBadge(a)).join('')}
        </div>
        <div class="muted-note">${Utils.escapeHtml(tr('search.note'))}</div>
        ${UIRenderer._toolbar()}
      </div>

      ${UIRenderer._articleCard(top, {
        label: tr('norm.closest'),
        iconName: 'scale',
        verdict: true,
        index: 1,
      })}

      ${rest.map((a, i) => UIRenderer._articleCard(a, { label: tr('norm.also'), index: i + 2 })).join('')}

      ${UIRenderer._relatedCard(data.related_norms, rest.length + 2)}
    `;

    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    UIRenderer.localize(container);
  }

  static renderNotFound(message, query = '', related = [], lang = null) {
    if (lang) setOutLang(lang);
    const container = document.getElementById('results');
    container.innerHTML = `
      <div class="result-card result-card--verdict" style="--i:0;border-color:var(--warning-border);background:var(--warning-bg)">
        <div class="result-card__header">
          <div class="result-card__icon" style="background:var(--warning-bg);color:var(--warning-text)">${icon('search')}</div>
          <div>
            <div class="result-card__label" style="color:var(--warning-text)">${Utils.escapeHtml(tr('nf.label'))}</div>
            <div class="result-card__title">${Utils.escapeHtml(tr('nf.title'))}</div>
          </div>
        </div>
        <div class="article-text" style="border-left-color:var(--accent-orange)">${Utils.escapeHtml(message)}</div>
        ${query ? `<div style="margin-top:var(--space-3);color:var(--ink-tertiary);font-size:var(--text-sm)">${Utils.escapeHtml(tr('nf.query'))} ${Utils.escapeHtml(query)}</div>` : ''}
      </div>

      ${UIRenderer._relatedCard(related, 1)}
    `;
    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    UIRenderer.localize(container);
  }

  static renderError(message, details = '') {
    const container = document.getElementById('results');
    container.innerHTML = `
      <div class="result-card result-card--verdict" style="--i:0;border-color:var(--warning-border);background:var(--warning-bg)">
        <div class="result-card__header">
          <div class="result-card__icon" style="background:var(--warning-bg);color:var(--warning-text)">${icon('alert')}</div>
          <div>
            <div class="result-card__label" style="color:var(--warning-text)">${Utils.escapeHtml(tr('err.label'))}</div>
            <div class="result-card__title">${Utils.escapeHtml(message)}</div>
          </div>
        </div>
        ${details ? `<div class="article-text" style="border-left-color:var(--accent-orange)">${Utils.escapeHtml(details)}</div>` : ''}
        <div style="margin-top:var(--space-4);color:var(--ink-tertiary);font-size:var(--text-sm);line-height:var(--leading-relaxed)">
          <strong>${tr('err.check')}</strong>
          <ul style="margin-top:var(--space-2);padding-left:1.25rem">
            <li>${tr('err.check1')}</li>
            <li>${tr('err.check2')}</li>
            <li>${tr('err.check3')}</li>
          </ul>
        </div>
      </div>
    `;
    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  static renderQualificationStart(meta, description = '') {
    setOutLang(meta.lang);
    const container = document.getElementById('results');
    const articles = meta.articles || [];
    container.innerHTML = `
      ${UIRenderer._disclaimer()}
      ${UIRenderer._understoodNote(meta, description)}
      <div class="result-card" style="--i:0">
        <div class="result-card__header">
          <div class="result-card__icon">${icon('shield')}</div>
          <div style="flex:1">
            <div class="result-card__label">${Utils.escapeHtml(tr('card.support'))}</div>
            <div class="result-card__title">${UIRenderer._sourceName(meta.primary_source)}</div>
          </div>
        </div>
        <div style="display:flex;gap:var(--space-2);flex-wrap:wrap">
          ${UIRenderer._uniqueArticles(articles).map(a => UIRenderer._artBadge(a)).join('')}
        </div>
      </div>

      <div class="result-card result-card--verdict" style="--i:1">
        <div class="result-card__header">
          <div class="result-card__icon">${icon('pen')}</div>
          <div>
            <div class="result-card__label">${Utils.escapeHtml(tr('card.analysis'))}</div>
            <div class="result-card__title">${Utils.escapeHtml(tr('card.writing'))}</div>
          </div>
        </div>
        <div class="article-text" id="qualifyStream" style="white-space:pre-wrap"></div>
      </div>
    `;
    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    UIRenderer.localize(container);
  }

  static appendQualificationChunk(chunk) {
    const target = document.getElementById('qualifyStream');
    if (target) target.textContent += chunk;
  }

  // Блок ответа в виде карточки; список шагов и маркеры сохраняют переносы строк.
  static _textCard({ iconName, label, title, body, index, verdict = false }) {
    return `
      <div class="result-card ${verdict ? 'result-card--verdict' : ''}" style="--i:${index}">
        <div class="result-card__header">
          <div class="result-card__icon">${icon(iconName)}</div>
          <div>
            <div class="result-card__label">${Utils.escapeHtml(label)}</div>
            <div class="result-card__title">${Utils.escapeHtml(title)}</div>
          </div>
        </div>
        <div class="article-text" style="white-space:pre-wrap">${Utils.escapeHtml(UIRenderer._tidy(body))}</div>
      </div>`;
  }

  // Модель отступает строки по-разному (три пробела, два, ни одного) и иногда
  // оставляет markdown-звёздочки: выравниваем, чтобы список читался ровно.
  static _tidy(body) {
    return String(body || '').replace(/\*\*/g, '').split('\n').map(line => line.trim()).join('\n').trim();
  }

  static _actionsBar() {
    return UIRenderer._toolbar();
  }

  static renderQualification(data, description) {
    const text = data.qualification || '';
    const sections = UIRenderer._parseSections(text);
    const container = document.getElementById('results');
    const articles = data.articles || [];
    const procedure = data.procedure || [];
    const unverified = data.unverified_articles || [];
    const isShort = data.mode === 'short';

    // Язык карточек — язык запроса (его присылает сервер); для старых записей истории — язык интерфейса.
    setOutLang(data.lang);

    const sectionCards = [
      { key: 'квалификация', iconName: 'scale', label: tr('sec.qual'), title: tr('sec.qual.t') },
      { key: 'санкции', iconName: 'clock', label: tr('sec.sanc'), title: tr('sec.sanc.t') },
      { key: 'факты', iconName: 'search', label: tr('sec.facts'), title: tr('sec.facts.t') },
      { key: 'обстоятельства', iconName: 'flag', label: tr('sec.circ'), title: tr('sec.circ.t') },
      { key: 'анализ', iconName: 'brain', label: tr('sec.anal'), title: tr('sec.anal.t') },
      { key: 'вывод', iconName: 'check', label: tr('sec.verd'), title: tr('sec.verd.t') },
    ];
    // Краткий режим — три ответа подряд: что делать, что грозит нарушителю, какие
    // статьи нарушены. Полный — итоговая плашка, «что делать» и свёрнутый разбор.
    const shortCards = [
      { key: 'делать', iconName: 'flag', label: tr('sec.todo'), title: tr('sec.todo.t'), verdict: true },
      { key: 'грозит', iconName: 'clock', label: tr('sec.threat'), title: tr('sec.threat.t') },
      { key: 'законы', iconName: 'scale', label: tr('sec.laws'), title: tr('sec.laws.t') },
    ].filter(c => sections[c.key]);
    const todoCard = sections['делать']
      ? UIRenderer._textCard({ iconName: 'flag', label: tr('sec.todo'), title: tr('sec.todo.t'),
                                body: sections['делать'], index: 1, verdict: true }) : '';

    // В бейджах «опоры» — только статьи, которые ответ действительно назвал. Поиск
    // приносит и случайные соседи («Хищение оружия» к вырванной сумке): они
    // остаются в развороте с текстами, но не выдаются за нарушенные законы.
    const cited = new Set([...text.matchAll(/(?:ст\.|стать[яиеюй]|статьях|модда[а-яӣӯҳқғҷ]*|м\.)\s*(\d{1,3})/gi)].map(m => m[1]));
    const onlyCited = list => {
      const unique = UIRenderer._uniqueArticles(list);
      const picked = unique.filter(a => cited.has(String(a.article)));
      return picked.length ? picked : unique.slice(0, 1);
    };

    const shownSections = isShort ? [] : sectionCards.filter(s => sections[s.key]);
    const afterSections = shownSections.length || 1;
    // Если модель (особенно после перевода) не сохранила названия разделов, текст всё равно показываем
    // целиком одной карточкой, а не прячем его в «Полный ответ модели».
    const shortFallback = isShort && shortCards.length === 0;
    const fullFallback = !isShort && shownSections.length === 0 && !todoCard && text.trim().length > 0;
    const wholeText = (index) => UIRenderer._textCard({
      iconName: 'scale', label: tr('sec.qual'), title: tr('fb.title'),
      body: UIRenderer._showHeadings(text), index, verdict: true,
    });

    const head = isShort
      ? `${shortCards.map((c, i) => UIRenderer._textCard({
            iconName: c.iconName, label: c.label, title: c.title, body: sections[c.key], index: i + 1, verdict: c.verdict,
          })).join('')}
         ${shortFallback ? wholeText(1) : ''}
         ${UIRenderer._actionsBar()}`
      : `${UIRenderer._summaryCard(sections, data.summary)}
         ${UIRenderer._understoodNote(data, description)}
         ${todoCard}
         ${fullFallback ? wholeText(1) : ''}`;

    container.innerHTML = `
      ${UIRenderer._disclaimer()}
      ${isShort ? UIRenderer._understoodNote(data, description) : ''}
      ${head}

      <div class="result-card" style="--i:4">
        <div class="result-card__header">
          <div class="result-card__icon">${icon('shield')}</div>
          <div style="flex:1">
            <div class="result-card__label">${Utils.escapeHtml(tr('card.support'))}</div>
            <div class="result-card__title">${UIRenderer._sourceName(data.primary_source)}</div>
          </div>
        </div>
        <div style="display:flex;gap:var(--space-2);flex-wrap:wrap">
          ${onlyCited(articles).map(a => UIRenderer._artBadge(a)).join('')}
          ${(procedure.length ? onlyCited(procedure) : []).map(a => UIRenderer._artBadge(a, { upk: true })).join('')}
        </div>
        ${unverified.length > 0 ? `
          <div class="aggravating-box">
            <strong>${Utils.escapeHtml(tr('unverified'))}</strong> ${Utils.escapeHtml(unverified.join(', '))}
            ${Utils.escapeHtml(tr('unverified.tail'))}
          </div>` : ''}
      </div>

      <details class="more-details" id="moreDetails">
        <summary>${Utils.escapeHtml(tr('more.sum'))}</summary>

        ${shownSections.map((s, i) => UIRenderer._textCard({
          iconName: s.iconName, label: s.label, title: s.title, body: sections[s.key], index: i + 5,
          verdict: s.key === 'квалификация' || s.key === 'вывод',
        })).join('')}

        ${articles.map((a, i) => UIRenderer._articleCard(a, { label: tr('card.normText'), index: afterSections + 5 + i })).join('')}

        ${procedure.map((a, i) => UIRenderer._articleCard(a, { label: tr('card.upk'), upk: true, index: afterSections + articles.length + 5 + i })).join('')}

        ${UIRenderer._relatedCard(data.related_norms, afterSections + articles.length + procedure.length + 5)}

        <details class="raw-toggle">
          <summary>${icon('terminal', 14)} ${Utils.escapeHtml(tr('raw'))}</summary>
          <pre>${Utils.escapeHtml(UIRenderer._showHeadings(text))}</pre>
        </details>
      </details>
    `;

    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    UIRenderer.localize(container);
  }

  // Переводит на таджикский названия и тексты статей из русского индекса. Названия в видимой части
  // идут сразу; длинные тексты статей — в свёрнутом разборе, их переводят при раскрытии.
  static localize(root) {
    if (OUT_LANG !== 'tg' || !root) return;
    const nodes = [...root.querySelectorAll('[data-tr]')].filter(node => !node.closest('details:not([open])'));
    TgText.apply(nodes);
  }

  static _parseSections(text) {
    const sections = {};
    const patterns = [
      ['делать', /ЧТО\s+ДЕЛАТЬ[^\n]*\n([\s\S]*?)(?=\n\s*(?:\d\.?\s*)?(?:ЧТО\s+ГРОЗИТ|КАКИЕ\s+ЗАКОНЫ|КВАЛИФИКАЦИЯ|САНКЦИИ)|$)/i],
      ['грозит', /ЧТО\s+ГРОЗИТ[^\n]*\n([\s\S]*?)(?=\n\s*(?:\d\.?\s*)?(?:КАКИЕ\s+ЗАКОНЫ)|$)/i],
      ['законы', /КАКИЕ\s+ЗАКОНЫ[^\n]*\n([\s\S]*?)$/i],
      ['квалификация', /(?:1\.?\s*)?КВАЛИФИКАЦИЯ[^\n]*\n([\s\S]*?)(?=\n\s*(?:\d\.?\s*)?(?:САНКЦИИ|ФАКТЫ|ОБСТОЯТЕЛЬСТВА|ПРАВОВОЙ|ИТОГОВЫЙ|ВЫВОД)|$)/i],
      ['санкции', /(?:2\.?\s*)?САНКЦИИ[^\n]*\n([\s\S]*?)(?=\n\s*(?:\d\.?\s*)?(?:ФАКТЫ|ОБСТОЯТЕЛЬСТВА|ПРАВОВОЙ|ИТОГОВЫЙ|ВЫВОД)|$)/i],
      ['факты', /(?:3\.?\s*)?ФАКТЫ[^\n]*\n([\s\S]*?)(?=\n\s*(?:\d\.?\s*)?(?:ОБСТОЯТЕЛЬСТВА|ПРАВОВОЙ|ИТОГОВЫЙ|ВЫВОД)|$)/i],
      ['обстоятельства', /(?:4\.?\s*)?ОБСТОЯТЕЛЬСТВА[^\n]*\n([\s\S]*?)(?=\n\s*(?:\d\.?\s*)?(?:ПРАВОВОЙ|ИТОГОВЫЙ|ВЫВОД)|$)/i],
      ['анализ', /(?:5\.?\s*)?ПРАВОВОЙ[^\n]*\n([\s\S]*?)(?=\n\s*(?:\d\.?\s*)?(?:ИТОГОВЫЙ|ВЫВОД)|$)/i],
      ['вывод', /(?:6\.?\s*)?(?:ИТОГОВЫЙ|ВЫВОД)[^\n]*\n([\s\S]*?)$/i],
    ];

    for (const [key, pattern] of patterns) {
      const match = text.match(pattern);
      sections[key] = match ? match[1].trim() : '';
    }
    return sections;
  }

  static showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.innerHTML = `<span aria-hidden="true">${type === 'success' ? '✓' : '⚠️'}</span><span>${Utils.escapeHtml(message)}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
      toast.classList.add('toast-exit');
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  }
}

class ProgressTracker {
  constructor() {
    this.el = document.getElementById('progressTracker');
    this.steps = new Map();
    this.el.querySelectorAll('.progress-step').forEach(step => {
      this.steps.set(step.dataset.step, step);
    });
  }

  show() {
    this.el.setAttribute('data-active', 'true');
    this.reset();
  }

  hide() {
    this.el.removeAttribute('data-active');
  }

  reset() {
    for (const step of this.steps.values()) {
      step.removeAttribute('data-state');
      const detail = step.querySelector('[data-detail]');
      if (detail) detail.textContent = t('p.wait');
    }
  }

  update(name, state, detail = '') {
    const step = this.steps.get(name);
    if (!step) return;
    step.setAttribute('data-state', state);
    const detailEl = step.querySelector('[data-detail]');
    if (detailEl && detail) detailEl.textContent = detail;

    const indicator = step.querySelector('.step-indicator');
    if (indicator) {
      indicator.classList.remove('pop');
      void indicator.offsetWidth;
      indicator.classList.add('pop');
    }
  }
}

class HistoryManager {
  constructor() {
    this.key = 'legalai_history_v2';
  }

  // В хранилище могли остаться записи старого формата (без текста запроса):
  // одна такая запись роняла отрисовку и затирала готовый результат ошибкой.
  get() {
    try {
      const list = JSON.parse(localStorage.getItem(this.key));
      return Array.isArray(list)
        ? list.filter(item => item && typeof item.query === 'string' && item.query.trim())
        : [];
    } catch { return []; }
  }

  // data — полный разбор (опционально). Хранится урезанным: тексты статей
  // обрезаются, чтобы восемь записей не упёрлись в лимит localStorage.
  add(query, article, data = null) {
    const history = this.get();
    const entry = {
      query: query.slice(0, CONFIG.MAX_INPUT_LENGTH),
      articleNum: article.number,
      articleName: article.name,
      lang: (data && data.lang) || article.lang || null,
      ts: Date.now(),
    };
    if (data && data.qualification) {
      entry.data = {
        grounded: true,
        lang: data.lang || null,
        summary: data.summary || null,
        mode: data.mode === 'short' ? 'short' : 'full',
        primary_source: data.primary_source,
        qualification: data.qualification,
        unverified_articles: data.unverified_articles || [],
        articles: (data.articles || []).map(a => ({
          article: a.article, article_title: a.article_title, citation: a.citation,
          text: String(a.text || '').slice(0, 1800),
        })),
        procedure: (data.procedure || []).map(a => ({
          article: a.article, article_title: a.article_title, citation: a.citation,
          text: String(a.text || '').slice(0, 1200),
        })),
        related_norms: (data.related_norms || []).slice(0, 3).map(a => ({
          article: a.article, article_title: a.article_title, code: a.code, citation: a.citation,
          snippet: String(a.snippet || '').slice(0, 300),
        })),
      };
    }
    history.unshift(entry);
    if (history.length > CONFIG.HISTORY_MAX) history.length = CONFIG.HISTORY_MAX;
    this._save(history);
  }

  _save(history) {
    // Переполнение хранилища: сначала отбрасываем самые старые полные разборы,
    // чтобы запись не пропала целиком.
    for (let i = 0; i < 4; i++) {
      try {
        localStorage.setItem(this.key, JSON.stringify(history));
        return;
      } catch {
        const oldest = [...history].reverse().find(item => item.data);
        if (!oldest) return;
        delete oldest.data;
      }
    }
  }

  render() {
    const panel = document.getElementById('historyPanel');
    const list = document.getElementById('historyList');
    const history = this.get();

    const count = document.getElementById('historyCount');
    if (count) {
      count.textContent = String(history.length);
      count.hidden = history.length === 0;
    }

    if (history.length === 0) {
      list.innerHTML = `<p class="empty-note">${Utils.escapeHtml(t('hist.empty'))}</p>`;
      return;
    }

    // Подпись записи — на языке самого запроса: таджикский запрос остаётся таджикским и в истории.
    const itemLang = item => (item.lang || (item.data && item.data.lang) || UI_LANG) === 'tg' ? 'tg' : 'ru';
    list.innerHTML = history.map((item, i) => `
      <div class="history-item ${item.data ? 'history-item--full' : ''}" data-history-index="${i}" data-lang="${itemLang(item)}" role="button" tabindex="0">
        <div class="history-item__title">${item.articleNum ? Utils.escapeHtml(t('art.abbr', null, itemLang(item))) + ' ' + Utils.escapeHtml(item.articleNum) + ' — ' : ''}<span data-tr="title">${Utils.escapeHtml(item.articleName ?? '')}</span></div>
        <div class="history-item__query">${Utils.escapeHtml(item.query.slice(0, 140))}${item.query.length > 140 ? '…' : ''}</div>
        <div class="history-item__time">${new Date(item.ts).toLocaleString(itemLang(item) === 'tg' ? 'tg-TJ' : 'ru-RU')}</div>
      </div>
    `).join('');

    TgText.apply([...list.querySelectorAll('.history-item[data-lang="tg"] [data-tr]')]);
  }
}

class ThemeManager {
  constructor() {
    this.root = document.documentElement;
    this.btn = document.getElementById('themeToggle');
    this.thumb = document.getElementById('themeThumb');
    this.mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    this._init();
  }

  _init() {
    const saved = localStorage.getItem('legalai-theme');
    const initial = saved || (this.mediaQuery.matches ? 'dark' : 'light');
    this.set(initial, false);

    this.btn.addEventListener('click', () => this.toggle());
    this.mediaQuery.addEventListener('change', e => {
      if (!localStorage.getItem('legalai-theme')) this.set(e.matches ? 'dark' : 'light');
    });

    document.addEventListener('keydown', e => {
      if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key.toLowerCase() === 'l') {
        e.preventDefault();
        this.toggle();
      }
    });
  }

  set(theme, save = true) {
    this.root.setAttribute('data-theme', theme);
    this.btn.setAttribute('aria-pressed', String(theme === 'dark'));
    if (save) localStorage.setItem('legalai-theme', theme);
  }

  toggle() {
    const current = this.root.getAttribute('data-theme');
    this.set(current === 'dark' ? 'light' : 'dark');
  }
}

class LegalAIApp {
  constructor() {
    this.rag = new CrimeRAG();
    this.llm = new CrimeService();
    this.theme = new ThemeManager();
    this.history = new HistoryManager();
    this.progress = new ProgressTracker();
    this.isProcessing = false;

    this.input = document.getElementById('crimeInput');
    this.charCount = document.getElementById('charCount');
    this.btnAnalyze = document.getElementById('btnAnalyze');
    this.btnQualify = document.getElementById('btnQualify');
    this.statusBadge = document.getElementById('statusBadge');
    this.statusText = document.getElementById('statusText');
    this.apiWarning = document.getElementById('apiWarning');
    this.resultsEl = document.getElementById('results');
    this.kbFilter = document.getElementById('kbFilter');
    this.statusState = { state: 'checking', articles: 0 };

    this._bindEvents();
    this._init();
  }

  _bindEvents() {
    this.input.addEventListener('input', Utils.debounce(() => this._updateCounter(), 100));

    // Вкладки правой колонки (на телефоне — ещё и «Описание»).
    document.getElementById('paneTabs')?.addEventListener('click', e => {
      const tab = e.target.closest('[data-pane-target]');
      if (tab) this._showPane(tab.dataset.paneTarget);
    });

    document.getElementById('btnClear')?.addEventListener('click', () => {
      this.input.value = '';
      this._updateCounter();
      this.input.focus();
    });

    this.input.addEventListener('keydown', e => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        this.qualify();
      }
    });

    document.getElementById('modeSwitch')?.addEventListener('click', e => {
      const btn = e.target.closest('[data-mode]');
      if (!btn) return;
      CRIME_MODE = btn.dataset.mode === 'short' ? 'short' : 'full';
      try { localStorage.setItem('legalai-crime-mode', CRIME_MODE); } catch { /* приватный режим */ }
      applyMode();
    });

    this.btnAnalyze.addEventListener('click', () => this.analyze());
    this.btnQualify.addEventListener('click', () => this.qualify());

    this.statusBadge.addEventListener('click', () => this._checkAPI());

    // Переключатель меняет язык всего сайта и возвращает на эту же страницу.
    document.getElementById('langToggle')?.addEventListener('click', () => {
      window.location.href = LANG_URLS[UI_LANG === 'tg' ? 'ru' : 'tg'];
    });

    document.getElementById('scenarioGrid').addEventListener('click', e => {
      const chip = e.target.closest('[data-scenario]');
      if (chip) {
        const scenario = SCENARIOS[chip.dataset.scenario];
        if (scenario) {
          this.input.value = UI_LANG === 'tg' && scenario.textTg ? scenario.textTg : scenario.text;
          this._updateCounter();
          this.input.focus();
        }
      }
    });

    this.kbFilter?.addEventListener('input', Utils.debounce(
      () => UIRenderer.renderKnowledgeBase(this.rag.catalog, this.kbFilter.value),
      CONFIG.DEBOUNCE_MS
    ));

    document.getElementById('kbGrid').addEventListener('click', e => {
      const card = e.target.closest('[data-article]');
      if (!card) return;
      const article = this.rag.catalog.find(a => String(a.article) === card.dataset.article);
      if (!article) return;
      const title = (UI_LANG === 'tg' && TgText.cached(article.article_title)) || article.article_title || '';
      this.input.value = t('kb.prefill', { n: article.article, title });
      this._updateCounter();
      this._showPane('input');
      this.input.focus();
    });

    const openHistory = item => {
      const entry = this.history.get()[parseInt(item.dataset.historyIndex)];
      if (!entry) return;
      this.input.value = entry.query;
      this._updateCounter();
      if (entry.data) {
        // Сохранённый разбор открывается без нового обращения к модели.
        this._showPane('result');
        UIRenderer.renderQualification(entry.data, entry.query);
        UIRenderer.showToast(t('toast.hist'));
      } else {
        this.input.focus();
        this.analyze();
      }
    };
    const historyList = document.getElementById('historyList');
    historyList.addEventListener('click', e => {
      const item = e.target.closest('[data-history-index]');
      if (item) openHistory(item);
    });
    historyList.addEventListener('keydown', e => {
      if (e.key !== 'Enter' && e.key !== ' ') return;
      const item = e.target.closest('[data-history-index]');
      if (item) { e.preventDefault(); openHistory(item); }
    });

    this.resultsEl.addEventListener('click', e => {
      const copy = e.target.closest('[data-action="copy"]');
      if (copy) { this._copyResults(copy); return; }
      if (e.target.closest('[data-action="download"]')) { this._downloadResults(); return; }
      if (e.target.closest('[data-action="print"]')) { this._printResults(); return; }
      if (e.target.closest('#clarifySkip')) this._submitClarify(false);
    });

    // Тексты статей в свёрнутом разборе переводятся на таджикский при раскрытии, а не заранее.
    this.resultsEl.addEventListener('toggle', e => {
      if (e.target.open) UIRenderer.localize(e.target);
    }, true);

    this.resultsEl.addEventListener('submit', e => {
      if (e.target.id !== 'clarifyForm') return;
      e.preventDefault();
      this._submitClarify(true);
    });

    // Свёрнутый разбор не должен пропасть из PDF: при печати всё раскрывается.
    window.addEventListener('beforeprint', () => {
      document.querySelectorAll('details.more-details, details.raw-toggle').forEach(d => { d.open = true; });
    });
  }

  // Активная панель. На широком экране «Описание» всегда слева, поэтому выбор
  // «input» там лишь переводит фокус в поле; на телефоне панели идут по одной.
  _showPane(name) {
    const workspace = document.getElementById('workspace');
    if (!workspace) return;
    const wide = window.matchMedia('(min-width: 1024px)').matches;
    if (wide && name === 'input') { this.input.focus(); return; }
    workspace.setAttribute('data-pane', name);
    document.querySelectorAll('#paneTabs [data-pane-target]').forEach(tab => {
      tab.setAttribute('aria-selected', String(tab.dataset.paneTarget === name));
    });
  }

  async _init() {
    this._showPane(window.matchMedia('(min-width: 1024px)').matches ? 'result' : 'input');
    applyI18n();
    applyMode();
    UIRenderer.renderScenarios();
    this.history.render();
    this._updateCounter();
    // Провайдер модели нужен для честных текстов про облако; не ждём его.
    this.llm.loadProvider().then(info => { CRIME_LLM = info; applyProviderText(); });
    await this._checkAPI();
  }

  async _checkAPI() {
    this._setStatus('checking', 0);
    const status = await this.rag.checkStatus();

    if (this.rag.ready) {
      this._setStatus('connected', status.articles || 0);
      this.apiWarning.style.display = 'none';
      await this.rag.loadCatalog();
      UIRenderer.renderKnowledgeBase(this.rag.catalog, this.kbFilter?.value || '');
    } else {
      this._setStatus('disconnected', 0);
      this.apiWarning.style.display = 'flex';
      UIRenderer.renderKnowledgeBase([]);
    }
  }

  _setStatus(state, articles) {
    this.statusState = { state, articles };
    this._renderStatus();
  }

  _renderStatus() {
    const { state, articles } = this.statusState;
    this.statusBadge.setAttribute('data-state', state);
    this.statusText.textContent = state === 'connected' ? t('status.ok', { n: articles || '—' })
      : state === 'disconnected' ? t('status.bad') : t('status.checking');
  }

  _updateCounter() {
    const len = this.input.value.length;
    this.charCount.textContent = `${len} / ${CONFIG.MAX_INPUT_LENGTH}`;

    if (len > CONFIG.MAX_INPUT_LENGTH * 0.9) {
      this.charCount.setAttribute('data-level', 'danger');
    } else if (len > CONFIG.MAX_INPUT_LENGTH * 0.75) {
      this.charCount.setAttribute('data-level', 'warning');
    } else {
      this.charCount.removeAttribute('data-level');
    }
  }

  _setProcessing(active) {
    this.isProcessing = active;
    this.btnAnalyze.disabled = active;
    this.btnQualify.disabled = active;

    if (active) {
      this._showPane('result');
      this.btnQualify.innerHTML = `<div class="spinner"></div><span>${Utils.escapeHtml(t('btn.busy'))}</span>`;
      this.progress.show();
      this.resultsEl.removeAttribute('data-active');
    } else {
      this.btnQualify.innerHTML = `
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <span>${Utils.escapeHtml(t('btn.qualify'))}</span><kbd>Ctrl+↵</kbd>`;
      this.progress.hide();
    }
  }

  _canRun() {
    const description = this.input.value.trim();
    if (!description) {
      this.input.style.animation = 'shake 0.4s ease';
      setTimeout(() => this.input.style.animation = '', 400);
      this.input.focus();
      return null;
    }
    if (this.isProcessing) return null;
    // Язык ответа — язык описания; сервер подтвердит его в ответе, а пока это первая догадка.
    setOutLang(guessLang(description));
    if (!this.rag.ready) {
      UIRenderer.renderError(tr('err.noIndex'), tr('err.noIndexBody'));
      return null;
    }
    return description;
  }

  async analyze() {
    const description = this._canRun();
    if (!description) return;
    this._setProcessing(true);

    try {
      this.progress.update('clarify', 'done', t('p.skipped'));
      this.progress.update('retrieval', 'active', t('p.searching'));
      const data = await this.rag.search(description);
      this.progress.update('llm', 'done', t('p.notUsed'));

      if (!data.grounded) {
        this.progress.update('retrieval', 'error', t('p.notFound'));
        this.progress.update('validation', 'done', t('p.noBasis'));
        await Utils.sleep(200);
        this._setProcessing(false);
        UIRenderer.renderNotFound(data.message || '', description, data.related_norms, data.lang);
        return;
      }

      const found = (data.articles || []).length;
      this.progress.update('retrieval', 'done', t('p.found', { n: found }));
      this.progress.update('validation', 'done', t('p.allFromCode'));
      await Utils.sleep(200);

      this._setProcessing(false);
      UIRenderer.renderSearch(data);

      try {
        const top = data.articles[0];
        if (top) {
          this.history.add(description, { number: top.article, name: top.article_title || '', lang: data.lang });
          this.history.render();
        }
      } catch (historyError) {
        console.warn('History error:', historyError);
      }
    } catch (err) {
      console.error('Search error:', err);
      this._setProcessing(false);
      UIRenderer.renderError(tr('err.search'), err.message);
    }
  }

  // Ответы на уточняющие вопросы дописываются к описанию, и квалификация
  // запускается уже без повторной проверки полноты — иначе вопросы пошли бы по кругу.
  _submitClarify(withAnswers) {
    const form = document.getElementById('clarifyForm');
    if (withAnswers && form) {
      const lines = [...form.querySelectorAll('input[data-question]')]
        .map(el => ({ q: el.dataset.question, a: el.value.trim() }))
        .filter(item => item.a)
        .map(item => `- ${item.q} ${item.a}`);
      if (lines.length) {
        const base = this.input.value.trim();
        this.input.value = (base + '\n\n' + tr('cl.added') + '\n' + lines.join('\n')).slice(0, CONFIG.MAX_INPUT_LENGTH);
        this._updateCounter();
      }
    }
    this.qualify({ skipClarify: true });
  }

  async qualify({ skipClarify = false } = {}) {
    const description = this._canRun();
    if (!description) return;
    this._setProcessing(true);

    try {
      if (skipClarify || CRIME_MODE === 'short') {
        this.progress.update('clarify', 'done', t('p.skipped'));
      } else {
        this.progress.update('clarify', 'active', t('p.checking'));
        const { questions, lang } = await this.llm.clarify(description);
        if (questions.length) {
          this.progress.update('clarify', 'done', t('p.needFacts', { n: questions.length }));
          this._setProcessing(false);
          UIRenderer.renderClarify(questions, description, lang);
          return;
        }
        this.progress.update('clarify', 'done', t('p.enough'));
      }

      this.progress.update('retrieval', 'active', t('p.searching'));
      this.progress.update('llm', 'active', t('p.waitFirst'));

      let tokens = 0;
      const data = await this.llm.qualifyStream(description, {
        onMeta: meta => {
          this.progress.update('retrieval', 'done', t('p.inBasis', { n: (meta.article_numbers || []).length }));
          UIRenderer.renderQualificationStart(meta, description);
        },
        onWait: seconds => {
          this.progress.update('llm', 'active', t('p.modelWork', { s: Math.round(seconds) }));
        },
        onChunk: chunk => {
          tokens += 1;
          UIRenderer.appendQualificationChunk(chunk);
          if (tokens % 20 === 0) this.progress.update('llm', 'active', t('p.chunks', { n: tokens }));
        },
      });

      if (!data.grounded) {
        this.progress.update('retrieval', 'error', t('p.notFound'));
        this.progress.update('llm', 'done', t('p.noModel'));
        this.progress.update('validation', 'done', t('p.noBasis'));
        this._setProcessing(false);
        UIRenderer.renderNotFound(data.qualification || data.message || data.error || '', description, data.related_norms, data.lang);
        return;
      }

      this.progress.update('llm', 'done', t('p.got'));
      const unverified = (data.unverified_articles || []).length;
      this.progress.update('validation', unverified ? 'error' : 'done',
        unverified ? t('p.badLinks', { n: unverified }) : t('p.linksOk'));

      this._setProcessing(false);
      UIRenderer.renderQualification(data, description);

      // В историю идёт полный разбор, чтобы открыть его позже без новой оплаты модели.
      try {
        const top = (data.articles || [])[0];
        this.history.add(description, { number: top ? top.article : '', name: top ? (top.article_title || '') : '' }, data);
        this.history.render();
      } catch (historyError) {
        console.warn('History error:', historyError);
      }
    } catch (err) {
      console.error('Qualification error:', err);
      this._setProcessing(false);
      UIRenderer.renderError(tr('err.qualify'), err.message);
    }
  }

  _printResults() {
    document.querySelectorAll('details.more-details, details.raw-toggle').forEach(d => { d.open = true; });
    window.print();
  }

  // Текст результата для копирования и файла: свёрнутые блоки раскрываются на
  // время чтения, кнопки и дубль «Полный ответ модели» скрыты классом is-exporting.
  async _resultText() {
    // На таджикском запросе тексты статей из свёрнутого разбора ещё могут быть русскими: переводим до выгрузки.
    if (OUT_LANG === 'tg') await TgText.apply([...this.resultsEl.querySelectorAll('[data-tr]')]);
    const details = [...this.resultsEl.querySelectorAll('details')];
    const wasOpen = details.map(d => d.open);
    details.forEach(d => { d.open = true; });
    this.resultsEl.classList.add('is-exporting');
    const text = this.resultsEl.innerText.replace(/\n{3,}/g, '\n\n').trim();
    this.resultsEl.classList.remove('is-exporting');
    details.forEach((d, i) => { d.open = wasOpen[i]; });
    return text;
  }

  async _copyResults(btn) {
    const label = btn.querySelector('.copy-btn__label') || btn;
    const original = label.textContent;
    try {
      await navigator.clipboard.writeText(await this._resultText());
      btn.setAttribute('data-copied', 'true');
      label.textContent = tr('sum.copied');
      UIRenderer.showToast(t('toast.copied'));
      setTimeout(() => {
        btn.removeAttribute('data-copied');
        label.textContent = original;
      }, 2000);
    } catch {
      UIRenderer.showToast(t('toast.copyFail'), 'error');
    }
  }

  async _downloadResults() {
    const now = new Date();
    const stamp = now.toISOString().slice(0, 16).replace('T', '_').replace(':', '-');
    const tg = OUT_LANG === 'tg';
    const body = await this._resultText();
    const header = [
      'LegalAI Pro — ' + (tg ? 'Тасниф аз рӯи КҶ ҶТ' : 'Квалификация по УК РТ'),
      now.toLocaleString(tg ? 'tg-TJ' : 'ru-RU'),
      '',
      (tg ? 'Тавсиф' : 'Описание') + ':',
      this.input.value.trim(),
      '', '—'.repeat(24), '',
    ].join('\n');
    const blob = new Blob(['﻿' + header + body + '\n'], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `legalai-uk-rt_${stamp}.txt`;
    document.body.appendChild(link);
    link.click();
    link.remove();
    setTimeout(() => URL.revokeObjectURL(url), 1000);
    UIRenderer.showToast(t('toast.downloaded'));
  }
}

// Параллакс весов в шапке: наклон по положению курсора, плавно возвращается.
function initHeroTilt() {
  const zone = document.getElementById('heroVisual');
  const tilt = document.getElementById('scalesTilt');
  if (!zone || !tilt) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const MAX_DEG = 9;
  const hero = zone.closest('.hero') || zone;
  hero.addEventListener('pointermove', e => {
    if (e.pointerType === 'touch') return;
    const rect = zone.getBoundingClientRect();
    const px = Math.max(-1, Math.min(1, (e.clientX - rect.left) / rect.width - 0.5));
    const py = Math.max(-1, Math.min(1, (e.clientY - rect.top) / rect.height - 0.5));
    tilt.style.transform = `rotateY(${px * MAX_DEG * 2}deg) rotateX(${py * -MAX_DEG * 1.4}deg)`;
  });
  hero.addEventListener('pointerleave', () => {
    tilt.style.transform = '';
  });
}

document.addEventListener('DOMContentLoaded', () => {
  window.LegalAI = new LegalAIApp();
  initHeroTilt();
});
</script>
</body>
</html>
