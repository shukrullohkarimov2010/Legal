<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="description" content="LegalAI Pro — кодексы Республики Таджикистан из базы знаний: оглавление, чтение статей и поиск">
<meta name="theme-color" content="#0a0e1c" media="(prefers-color-scheme: dark)">
<meta name="theme-color" content="#f5f6fb" media="(prefers-color-scheme: light)">
<title>LegalAI Pro — Кодексы РТ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════════════
   LegalAI Pro · Кодексы РТ
   1. Токены   2. База   3. Шапка и hero   4. Главная (кодексы)
   5. Кодекс: оглавление и читалка   6. Поиск   7. Служебное   8. Адаптив и печать
   ═══════════════════════════════════════════════════════════════ */

/* ── 1. Токены ─────────────────────────────────────────────── */
:root {
  --font-sans: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
  --font-mono: 'JetBrains Mono', ui-monospace, 'SF Mono', Consolas, monospace;
  --space-1: .25rem; --space-2: .5rem; --space-3: .75rem; --space-4: 1rem; --space-5: 1.25rem;
  --space-6: 1.5rem; --space-8: 2rem; --space-10: 2.5rem;
  --text-xs: .75rem; --text-sm: .875rem; --text-base: 1rem; --text-lg: 1.125rem; --text-xl: 1.375rem;
  --radius-sm: 8px; --radius-md: 10px; --radius-lg: 14px; --radius-xl: 20px; --radius-full: 999px;
  --duration-fast: .15s; --duration: .25s; --ease: cubic-bezier(.2, .8, .2, 1);
  --grad: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  --topbar-h: 64px;
}
[data-theme="light"] {
  color-scheme: light;
  --bg: #f4f5fb; --surface: #ffffff; --surface-2: #f6f7fc; --sunken: #eef0f8;
  --ink-primary: #0f172a; --ink-secondary: #475569; --ink-tertiary: #64748b;
  --line: #e4e7f1; --line-medium: #d3d9e8; --line-strong: #b6c0d6;
  --accent: #4f46e5; --accent-strong: #4338ca; --accent-soft: rgba(79, 70, 229, .08); --accent-ring: rgba(79, 70, 229, .28);
  --glass: rgba(255, 255, 255, .8);
  --shadow-sm: 0 1px 2px rgba(15, 23, 42, .05);
  --shadow-md: 0 8px 24px -8px rgba(30, 41, 90, .14), 0 2px 6px rgba(15, 23, 42, .04);
  --shadow-lg: 0 24px 60px -20px rgba(49, 46, 129, .28);
  --success-bg: #ecfdf5; --success-border: #a7f3d0; --success-text: #047857;
  --warning-bg: #fffbeb; --warning-border: #fcd98a; --warning-text: #92400e;
  --info-bg: #eef4ff; --info-border: #c7d7fe; --info-text: #3730a3;
  --mark: rgba(250, 204, 21, .45);
  --blob-1: rgba(99, 102, 241, .16); --blob-2: rgba(56, 189, 248, .14);
}
[data-theme="dark"] {
  color-scheme: dark;
  --bg: #090d1b; --surface: #111730; --surface-2: #161e3d; --sunken: #0c1228;
  --ink-primary: #eef1ff; --ink-secondary: #b6bedd; --ink-tertiary: #8e98bf;
  --line: #212a4d; --line-medium: #2c3763; --line-strong: #3d4a7e;
  --accent: #818cf8; --accent-strong: #a5b4fc; --accent-soft: rgba(129, 140, 248, .13); --accent-ring: rgba(129, 140, 248, .38);
  --glass: rgba(11, 16, 36, .76);
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, .3);
  --shadow-md: 0 10px 30px -10px rgba(0, 0, 0, .55);
  --shadow-lg: 0 30px 70px -24px rgba(0, 0, 0, .7);
  --success-bg: rgba(16, 185, 129, .1); --success-border: rgba(52, 211, 153, .32); --success-text: #6ee7b7;
  --warning-bg: rgba(245, 158, 11, .1); --warning-border: rgba(251, 191, 36, .34); --warning-text: #fcd34d;
  --info-bg: rgba(99, 102, 241, .11); --info-border: rgba(129, 140, 248, .32); --info-text: #c7d2fe;
  --mark: rgba(250, 204, 21, .32);
  --blob-1: rgba(99, 102, 241, .22); --blob-2: rgba(14, 165, 233, .14);
}

/* ── 2. База ───────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }
html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
body { margin: 0; min-height: 100vh; font-family: var(--font-sans); font-size: var(--text-base); line-height: 1.5; color: var(--ink-primary); background: var(--bg); -webkit-font-smoothing: antialiased; }
h1, h2, h3, p, ol, ul { margin: 0; }
button, input, select { font: inherit; color: inherit; }
button { cursor: pointer; }
a { color: inherit; text-decoration: none; }
[hidden] { display: none !important; }
:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
::selection { background: var(--accent-ring); }
.sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; }
mark { background: var(--mark); color: inherit; border-radius: 3px; padding: 0 1px; }

.ambient-bg { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
.ambient-bg::before, .ambient-bg::after { content: ''; position: absolute; border-radius: 50%; filter: blur(90px); }
.ambient-bg::before { width: 620px; height: 620px; top: -220px; left: -160px; background: var(--blob-1); }
.ambient-bg::after { width: 560px; height: 560px; right: -180px; top: 28%; background: var(--blob-2); }
.app-shell { position: relative; z-index: 1; }
.container { width: 100%; max-width: 1440px; margin-inline: auto; padding-inline: 24px; }
.surface { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius-xl); box-shadow: var(--shadow-md); }
@keyframes rise { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
@keyframes spin { to { transform: rotate(360deg); } }
@keyframes shimmer { to { background-position: -200% 0; } }

.btn { display: inline-flex; align-items: center; justify-content: center; gap: var(--space-2); min-height: 44px; padding: 0 var(--space-5); border-radius: var(--radius-lg); border: 1px solid transparent; font-weight: 600; font-size: var(--text-sm); white-space: nowrap; transition: transform var(--duration-fast), box-shadow var(--duration), background var(--duration), border-color var(--duration); }
.btn--primary { color: #fff; background: var(--grad); box-shadow: 0 10px 24px -10px rgba(99, 70, 229, .8), inset 0 1px 0 rgba(255, 255, 255, .22); }
.btn--primary:hover { transform: translateY(-1px); }
.btn--ghost { color: var(--ink-secondary); background: transparent; border-color: var(--line-medium); }
.btn--ghost:hover { color: var(--ink-primary); border-color: var(--line-strong); background: var(--surface-2); }
.chip-btn { display: inline-flex; align-items: center; gap: 7px; padding: 8px 13px; border-radius: var(--radius-md); border: 1px solid var(--line-medium); background: var(--surface); color: var(--ink-secondary); font-size: var(--text-sm); font-weight: 600; transition: all var(--duration-fast); }
.chip-btn:hover { color: var(--accent-strong); border-color: var(--accent-ring); background: var(--accent-soft); transform: translateY(-1px); }
.chip-btn[data-done="true"] { color: var(--success-text); border-color: var(--success-border); background: var(--success-bg); }
.is-translating { opacity: .5; animation: tr-pulse 1.1s ease-in-out infinite; }
@keyframes tr-pulse { 50% { opacity: .85; } }

/* ── 3. Шапка и hero ───────────────────────────────────────── */
.topbar { position: sticky; top: 0; z-index: 50; height: var(--topbar-h); background: var(--glass); backdrop-filter: blur(16px) saturate(1.4); -webkit-backdrop-filter: blur(16px) saturate(1.4); border-bottom: 1px solid var(--line); }
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
html[data-ui-lang="ru"] .lang-toggle [data-lang="ru"], html[data-ui-lang="tg"] .lang-toggle [data-lang="tg"] { background: var(--grad); color: #fff; }
.status-badge { display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: var(--radius-full); border: 1px solid var(--line-medium); background: var(--surface); font-size: var(--text-xs); font-weight: 600; color: var(--ink-secondary); }
.status-dot { width: 8px; height: 8px; border-radius: 50%; background: #fbbf24; animation: pulse 1.6s infinite; }
.status-badge[data-state="connected"] { color: var(--success-text); border-color: var(--success-border); }
.status-badge[data-state="connected"] .status-dot { background: #10b981; animation: none; box-shadow: 0 0 0 3px rgba(16, 185, 129, .2); }
.status-badge[data-state="disconnected"] { color: #ef4444; border-color: rgba(239, 68, 68, .4); }
.status-badge[data-state="disconnected"] .status-dot { background: #ef4444; animation: none; }
@keyframes pulse { 70% { box-shadow: 0 0 0 7px rgba(251, 191, 36, 0); } 0% { box-shadow: 0 0 0 0 rgba(251, 191, 36, .5); } }
.theme-toggle { position: relative; width: 52px; height: 30px; padding: 0; border-radius: var(--radius-full); border: 1px solid var(--line-medium); background: var(--surface); }
.theme-toggle__thumb { position: absolute; top: 3px; left: 3px; width: 22px; height: 22px; border-radius: 50%; display: grid; place-items: center; color: #fff; background: var(--grad); transition: transform var(--duration) var(--ease); }
[data-theme="dark"] .theme-toggle__thumb { transform: translateX(22px); }
[data-theme="dark"] #themeIconSun, [data-theme="light"] #themeIconMoon { display: none; }

.hero { padding-top: var(--space-6); }
.hero-card { position: relative; overflow: hidden; isolation: isolate; border-radius: 28px; color: #fff; background: linear-gradient(125deg, #1b1b5a 0%, #3028a8 50%, #2563eb 100%); box-shadow: var(--shadow-lg); padding: 36px 44px 32px; }
.hero-card::before { content: ''; position: absolute; inset: 0; z-index: -1; opacity: .5; background-image: linear-gradient(rgba(255, 255, 255, .06) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .06) 1px, transparent 1px); background-size: 38px 38px; -webkit-mask-image: radial-gradient(70% 90% at 80% 30%, #000 0%, transparent 75%); mask-image: radial-gradient(70% 90% at 80% 30%, #000 0%, transparent 75%); }
.hero-card::after { content: ''; position: absolute; z-index: -1; width: 520px; height: 520px; right: -120px; top: -220px; border-radius: 50%; background: radial-gradient(circle, rgba(167, 139, 250, .55), transparent 65%); }
.hero-eyebrow { display: inline-flex; align-items: center; gap: 8px; font-size: var(--text-xs); font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #c7d2fe; padding: 6px 12px; border-radius: var(--radius-full); background: rgba(255, 255, 255, .1); border: 1px solid rgba(255, 255, 255, .16); }
.hero-eyebrow::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #34d399; box-shadow: 0 0 10px #34d399; }
.hero-title { margin-top: var(--space-4); font-size: clamp(1.6rem, 3.2vw, 2.4rem); line-height: 1.12; font-weight: 800; letter-spacing: -.02em; text-wrap: balance; }
.hero-sub { margin-top: var(--space-3); max-width: 62ch; color: #dbe2ff; line-height: 1.6; }
.hero-stats { display: flex; flex-wrap: wrap; gap: var(--space-2); margin-top: var(--space-4); }
.stat-chip { display: inline-flex; align-items: center; gap: 8px; padding: 7px 14px; border-radius: var(--radius-full); font-size: var(--text-sm); font-weight: 600; color: #eef2ff; background: rgba(255, 255, 255, .1); border: 1px solid rgba(255, 255, 255, .16); }
.stat-chip b { font-weight: 800; color: #fff; }
.hero--compact .hero-sub, .hero--compact .hero-stats, .hero--compact .search-examples { display: none; }
.hero--compact .hero-card { padding: 22px 32px 22px; }
.hero--compact .hero-title { font-size: clamp(1.3rem, 2.4vw, 1.7rem); margin-top: var(--space-3); }

.search-box { position: relative; display: flex; align-items: center; margin-top: var(--space-5); max-width: 760px; }
.search-box > svg { position: absolute; left: 18px; color: #6b7399; pointer-events: none; }
.search-input { width: 100%; height: 56px; padding: 0 56px 0 52px; border-radius: var(--radius-lg); border: 1px solid rgba(255, 255, 255, .25); background: rgba(255, 255, 255, .96); color: #0f172a; font-size: var(--text-base); box-shadow: 0 14px 34px -14px rgba(10, 8, 60, .6); transition: box-shadow var(--duration-fast); }
.search-input::placeholder { color: #7a82a6; }
.search-input:focus { outline: none; box-shadow: 0 0 0 4px rgba(165, 180, 252, .55), 0 14px 34px -14px rgba(10, 8, 60, .6); }
.search-clear { position: absolute; right: 10px; width: 36px; height: 36px; display: grid; place-items: center; border: 0; border-radius: 10px; background: transparent; color: #6b7399; }
.search-clear:hover { background: #eef0f8; color: #0f172a; }
.search-examples { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin-top: var(--space-3); font-size: var(--text-xs); color: #c7d2fe; }
.search-examples button { padding: 5px 11px; border-radius: var(--radius-full); border: 1px solid rgba(255, 255, 255, .22); background: rgba(255, 255, 255, .08); color: #eef2ff; font-size: var(--text-xs); font-weight: 600; transition: background var(--duration-fast); }
.search-examples button:hover { background: rgba(255, 255, 255, .2); }

/* ── 4. Боковая панель и главная ─────────────────────────── */
.layout { display: grid; grid-template-columns: 276px minmax(0, 1fr); gap: var(--space-6); align-items: start; padding-block: var(--space-6) var(--space-10); }
.main { min-height: 50vh; min-width: 0; }
.sidebar { position: sticky; top: calc(var(--topbar-h) + 16px); max-height: calc(100vh - var(--topbar-h) - 32px); overflow-y: auto; padding: var(--space-3); scrollbar-width: thin; }
.side-title { padding: var(--space-3) var(--space-3) var(--space-2); font: 700 var(--text-xs)/1 var(--font-mono); letter-spacing: .08em; text-transform: uppercase; color: var(--ink-tertiary); }
.side-item { display: flex; align-items: center; gap: var(--space-3); width: 100%; padding: 9px var(--space-3); border: 0; border-radius: var(--radius-md); background: transparent; text-align: left; font-size: var(--text-sm); font-weight: 500; color: var(--ink-secondary); transition: background var(--duration-fast), color var(--duration-fast); }
.side-item:hover { background: var(--accent-soft); color: var(--ink-primary); }
.side-item.is-active { background: color-mix(in srgb, var(--g, var(--accent)) 13%, transparent); color: var(--ink-primary); font-weight: 700; box-shadow: inset 3px 0 0 var(--g, var(--accent)); }
.side-item .ico-tile { width: 30px; height: 30px; border-radius: 9px; }
.side-item__name { flex: 1; min-width: 0; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.side-item__count { flex: none; font: 600 11px/1 var(--font-mono); padding: 4px 7px; border-radius: var(--radius-full); color: var(--ink-tertiary); background: var(--sunken); }
.side-item.is-active .side-item__count { color: var(--g, var(--accent)); background: color-mix(in srgb, var(--g, var(--accent)) 14%, transparent); }
.side-group { display: flex; align-items: center; gap: 8px; padding: var(--space-4) var(--space-3) 6px; font: 700 11px/1.2 var(--font-mono); letter-spacing: .08em; text-transform: uppercase; color: var(--g, var(--ink-tertiary)); }
.side-group::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--g, var(--ink-tertiary)); }
.menu-btn { display: none; width: 40px; height: 40px; place-items: center; border-radius: 12px; border: 1px solid var(--line-medium); background: var(--surface); color: var(--ink-secondary); }
.sidebar-overlay { display: none; position: fixed; inset: 0; z-index: 59; background: rgba(5, 8, 20, .55); backdrop-filter: blur(2px); }
.group-block { margin-bottom: var(--space-8); animation: rise .45s var(--ease) both; animation-delay: calc(var(--gi, 0) * 60ms); }
.group-head { display: flex; align-items: center; gap: var(--space-3); margin-bottom: var(--space-3); }
.group-head h2 { font-size: var(--text-lg); font-weight: 800; letter-spacing: -.01em; }
.group-head span { font: 600 var(--text-xs)/1 var(--font-mono); color: var(--ink-tertiary); padding: 4px 8px; border-radius: var(--radius-full); background: var(--sunken); border: 1px solid var(--line); }
.ico-tile { display: grid; place-items: center; flex: none; width: 40px; height: 40px; border-radius: 12px; color: var(--g, var(--accent)); background: color-mix(in srgb, var(--g, var(--accent)) 14%, transparent); border: 1px solid color-mix(in srgb, var(--g, var(--accent)) 30%, transparent); }
.code-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: var(--space-4); }
.code-card { position: relative; display: flex; flex-direction: column; gap: var(--space-3); text-align: left; padding: var(--space-5); border-radius: var(--radius-xl); border: 1px solid var(--line); background: var(--surface); box-shadow: var(--shadow-sm); transition: transform var(--duration) var(--ease), box-shadow var(--duration), border-color var(--duration); overflow: hidden; }
.code-card::before { content: ''; position: absolute; inset: 0 0 auto 0; height: 3px; background: var(--g, var(--accent)); opacity: .85; }
.code-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: color-mix(in srgb, var(--g, var(--accent)) 45%, var(--line)); }
.code-card__top { display: flex; align-items: center; justify-content: space-between; gap: var(--space-3); }
.code-card__short { font: 700 var(--text-xs)/1 var(--font-mono); letter-spacing: .06em; padding: 6px 9px; border-radius: var(--radius-sm); color: var(--g, var(--accent)); background: color-mix(in srgb, var(--g, var(--accent)) 12%, transparent); }
.code-card__name { font-size: var(--text-base); font-weight: 700; line-height: 1.35; letter-spacing: -.005em; }
.code-card__stats { margin-top: auto; display: flex; align-items: center; justify-content: space-between; gap: var(--space-2); font-size: var(--text-xs); font-weight: 600; color: var(--ink-tertiary); }
.code-card__open { display: inline-flex; align-items: center; gap: 4px; color: var(--g, var(--accent)); transition: gap var(--duration-fast); }
.code-card:hover .code-card__open { gap: 8px; }
.recent { margin-bottom: var(--space-8); }
.recent__head { display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-3); }
.recent__head h2 { font-size: var(--text-base); font-weight: 800; }
.recent__clear { border: 0; background: none; font-size: var(--text-xs); font-weight: 600; color: var(--ink-tertiary); }
.recent__clear:hover { color: var(--ink-primary); }
.recent__list { display: flex; gap: var(--space-3); overflow-x: auto; padding-bottom: 6px; scrollbar-width: thin; }
.recent-item { flex: none; width: 240px; text-align: left; padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); border: 1px solid var(--line); background: var(--surface); transition: all var(--duration-fast); }
.recent-item:hover { border-color: var(--accent-ring); transform: translateY(-2px); box-shadow: var(--shadow-md); }
.recent-item small { display: block; font: 700 var(--text-xs)/1 var(--font-mono); color: var(--g, var(--accent)); margin-bottom: 6px; }
.recent-item span { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; font-size: var(--text-sm); font-weight: 600; line-height: 1.35; }
.skeleton { border-radius: var(--radius-xl); height: 150px; background: linear-gradient(90deg, var(--surface-2) 25%, var(--sunken) 50%, var(--surface-2) 75%); background-size: 200% 100%; animation: shimmer 1.4s linear infinite; border: 1px solid var(--line); }

/* ── 5. Кодекс: оглавление и читалка ──────────────────────── */
.code-head { display: flex; align-items: center; flex-wrap: wrap; gap: var(--space-4); margin-bottom: var(--space-4); animation: rise .35s var(--ease) both; }
.back-btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 14px; border-radius: var(--radius-full); border: 1px solid var(--line-medium); background: var(--surface); font-size: var(--text-sm); font-weight: 600; color: var(--ink-secondary); transition: all var(--duration-fast); }
.back-btn:hover { color: var(--ink-primary); border-color: var(--line-strong); }
.code-head__title { display: flex; align-items: center; gap: var(--space-3); min-width: 0; flex: 1; }
.code-head__title h2 { font-size: var(--text-xl); font-weight: 800; letter-spacing: -.015em; line-height: 1.2; }
.code-head__title p { font-size: var(--text-xs); font-weight: 600; color: var(--ink-tertiary); margin-top: 3px; }
.code-switch { height: 44px; max-width: 280px; padding: 0 36px 0 14px; border-radius: var(--radius-lg); border: 1px solid var(--line-medium); background: var(--surface) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238e98bf' stroke-width='3' stroke-linecap='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 14px center; appearance: none; -webkit-appearance: none; font-size: var(--text-sm); font-weight: 600; }
.code-layout { display: grid; grid-template-columns: minmax(280px, 360px) minmax(0, 1fr); gap: var(--space-5); align-items: start; }
.code-head .back-btn { display: none; }
.toc { position: sticky; top: calc(var(--topbar-h) + 16px); display: flex; flex-direction: column; max-height: calc(100vh - var(--topbar-h) - 32px); overflow: hidden; }
.toc__head { padding: var(--space-4) var(--space-4) var(--space-3); border-bottom: 1px solid var(--line); }
.toc__head h3 { font: 700 var(--text-xs)/1 var(--font-mono); letter-spacing: .08em; text-transform: uppercase; color: var(--ink-tertiary); margin-bottom: var(--space-3); }
.toc-filter { position: relative; }
.toc-filter svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--ink-tertiary); pointer-events: none; }
.toc-filter input { width: 100%; height: 42px; padding: 0 12px 0 38px; border-radius: var(--radius-md); border: 1px solid var(--line-medium); background: var(--sunken); font-size: var(--text-sm); }
.toc-filter input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-ring); background: var(--surface); }
.toc__body { overflow-y: auto; padding: var(--space-2) var(--space-2) var(--space-4); scrollbar-width: thin; }
.toc-section { padding: var(--space-4) var(--space-3) var(--space-2); font: 700 11px/1.3 var(--font-mono); letter-spacing: .08em; text-transform: uppercase; color: var(--g, var(--accent)); }
.toc-chapter { border-radius: var(--radius-md); }
.toc-chapter > summary { display: flex; align-items: flex-start; gap: var(--space-3); padding: 10px var(--space-3); border-radius: var(--radius-md); list-style: none; cursor: pointer; font-size: var(--text-sm); font-weight: 600; line-height: 1.35; user-select: none; transition: background var(--duration-fast); }
.toc-chapter > summary::-webkit-details-marker { display: none; }
.toc-chapter > summary::before { content: ''; flex: none; width: 7px; height: 7px; margin-top: 6px; border-right: 2px solid var(--ink-tertiary); border-bottom: 2px solid var(--ink-tertiary); transform: rotate(-45deg); transition: transform var(--duration) var(--ease); }
.toc-chapter[open] > summary::before { transform: rotate(45deg); }
.toc-chapter > summary:hover { background: var(--accent-soft); }
.toc-chapter__title { flex: 1; min-width: 0; }
.toc-count { flex: none; font: 600 11px/1 var(--font-mono); padding: 4px 7px; border-radius: var(--radius-full); color: var(--ink-tertiary); background: var(--sunken); }
.toc-list { display: flex; flex-direction: column; gap: 1px; padding: 2px 0 var(--space-2) 22px; }
.toc-item { display: flex; align-items: baseline; gap: var(--space-3); width: 100%; text-align: left; padding: 8px var(--space-3); border: 0; border-radius: var(--radius-sm); background: transparent; font-size: var(--text-sm); line-height: 1.35; color: var(--ink-secondary); transition: background var(--duration-fast), color var(--duration-fast); }
.toc-item:hover { background: var(--accent-soft); color: var(--ink-primary); }
.toc-item.is-active { background: var(--accent-soft); color: var(--accent-strong); font-weight: 600; box-shadow: inset 3px 0 0 var(--accent); }
.toc-n { flex: none; min-width: 34px; font: 700 var(--text-xs)/1.35 var(--font-mono); color: var(--g, var(--accent)); }
.toc-more { display: block; width: calc(100% - 12px); margin: var(--space-2) 6px; padding: 10px; border: 1px dashed var(--line-medium); border-radius: var(--radius-md); background: transparent; font-size: var(--text-sm); font-weight: 600; color: var(--ink-tertiary); }
.toc-more:hover { color: var(--ink-primary); border-color: var(--line-strong); }
.toc-empty { padding: var(--space-8) var(--space-4); text-align: center; font-size: var(--text-sm); color: var(--ink-tertiary); }

.reader { padding: var(--space-8) var(--space-8) var(--space-6); animation: rise .35s var(--ease) both; min-width: 0; }
.crumbs { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: var(--text-xs); font-weight: 600; color: var(--ink-tertiary); }
.crumbs span + span::before { content: '›'; margin-right: 6px; opacity: .6; }
.reader__badge { display: inline-flex; align-items: center; gap: 8px; margin-top: var(--space-4); padding: 6px 12px; border-radius: var(--radius-full); font: 700 var(--text-xs)/1 var(--font-mono); letter-spacing: .04em; color: var(--g, var(--accent)); background: color-mix(in srgb, var(--g, var(--accent)) 12%, transparent); border: 1px solid color-mix(in srgb, var(--g, var(--accent)) 30%, transparent); }
.reader__title { margin-top: var(--space-3); font-size: clamp(1.3rem, 2.4vw, 1.75rem); font-weight: 800; line-height: 1.25; letter-spacing: -.02em; text-wrap: balance; }
.reader__body { margin-top: var(--space-5); font-size: 1.0625rem; line-height: 1.8; color: var(--ink-primary); max-width: 74ch; }
.reader__body p, .reader__body .li { margin: 0 0 var(--space-4); overflow-wrap: anywhere; }
.reader__body .li { padding-left: var(--space-4); border-left: 2px solid var(--line-medium); color: var(--ink-secondary); }
.reader__body .li.sub { margin-left: var(--space-5); }
.reader__body .note { padding: var(--space-3) var(--space-4); border-radius: var(--radius-md); background: var(--warning-bg); border: 1px solid var(--warning-border); color: var(--warning-text); font-size: var(--text-sm); line-height: 1.6; }
.reader__lead { font-weight: 600; color: var(--ink-primary); }
.tr-note { display: flex; gap: 8px; align-items: flex-start; margin-top: var(--space-4); padding: var(--space-3) var(--space-4); border-radius: var(--radius-md); background: var(--info-bg); border: 1px solid var(--info-border); color: var(--info-text); font-size: var(--text-xs); line-height: 1.5; }
.reader__toolbar { display: flex; flex-wrap: wrap; gap: var(--space-2); margin-top: var(--space-5); padding-top: var(--space-5); border-top: 1px dashed var(--line-medium); }
.reader__nav { display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-3); margin-top: var(--space-5); }
.nav-card { display: flex; flex-direction: column; gap: 4px; padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); border: 1px solid var(--line); background: var(--surface-2); text-align: left; transition: all var(--duration-fast); min-width: 0; }
.nav-card:hover:not(:disabled) { border-color: var(--accent-ring); background: var(--accent-soft); transform: translateY(-2px); }
.nav-card:disabled { opacity: .45; cursor: default; }
.nav-card small { font: 700 11px/1 var(--font-mono); letter-spacing: .06em; text-transform: uppercase; color: var(--ink-tertiary); }
.nav-card b { font-size: var(--text-sm); line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.nav-card--next { text-align: right; align-items: flex-end; }
.reader-empty { display: grid; place-items: center; text-align: center; padding: var(--space-10) var(--space-6); min-height: 320px; border: 1.5px dashed var(--line-medium); border-radius: var(--radius-xl); background: color-mix(in srgb, var(--surface) 55%, transparent); }
.reader-empty .ico-tile { width: 64px; height: 64px; border-radius: 20px; margin: 0 auto var(--space-4); }
.reader-empty h3 { font-size: var(--text-lg); font-weight: 800; }
.reader-empty p { max-width: 42ch; margin: var(--space-2) auto 0; font-size: var(--text-sm); line-height: 1.6; color: var(--ink-tertiary); }
.reader-load { height: 320px; }
.to-toc { display: none; }

/* ── 6. Поиск ──────────────────────────────────────────────── */
.results-head { display: flex; align-items: center; flex-wrap: wrap; gap: var(--space-3); margin-bottom: var(--space-4); animation: rise .3s var(--ease) both; }
.results-head h2 { font-size: var(--text-lg); font-weight: 800; flex: 1; min-width: 0; overflow-wrap: anywhere; }
.results-note { margin-bottom: var(--space-4); padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); background: var(--info-bg); border: 1px solid var(--info-border); color: var(--info-text); font-size: var(--text-sm); }
.filter-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: var(--space-4); }
.filter-chip { display: inline-flex; align-items: center; gap: 7px; padding: 7px 12px; border-radius: var(--radius-full); border: 1px solid var(--line-medium); background: var(--surface); font-size: var(--text-xs); font-weight: 600; color: var(--ink-secondary); transition: all var(--duration-fast); }
.filter-chip b { font: 700 11px/1 var(--font-mono); padding: 3px 6px; border-radius: var(--radius-full); background: var(--sunken); color: var(--ink-tertiary); }
.filter-chip:hover { border-color: var(--line-strong); color: var(--ink-primary); }
.filter-chip[aria-pressed="true"] { color: #fff; border-color: transparent; background: var(--grad); }
.filter-chip[aria-pressed="true"] b { color: #fff; background: rgba(255, 255, 255, .2); }
.result-list { display: grid; gap: var(--space-3); }
.result-row { display: grid; grid-template-columns: auto 1fr; gap: var(--space-4); text-align: left; padding: var(--space-4) var(--space-5); border-radius: var(--radius-lg); border: 1px solid var(--line); background: var(--surface); box-shadow: var(--shadow-sm); animation: rise .35s var(--ease) both; animation-delay: calc(var(--i, 0) * 25ms); transition: transform var(--duration) var(--ease), border-color var(--duration), box-shadow var(--duration); }
.result-row:hover { transform: translateX(4px); border-color: var(--accent-ring); box-shadow: var(--shadow-md); }
.result-row__id { display: flex; flex-direction: column; align-items: center; gap: 6px; min-width: 64px; padding-top: 2px; }
.result-row__id .code-card__short { font-size: 10px; }
.result-row__n { font: 800 var(--text-lg)/1 var(--font-mono); color: var(--g, var(--accent)); }
.result-row__title { font-weight: 700; line-height: 1.35; }
.result-row__meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 4px; font-size: var(--text-xs); color: var(--ink-tertiary); font-weight: 600; }
.match-tag { padding: 2px 7px; border-radius: var(--radius-full); background: var(--sunken); border: 1px solid var(--line); }
.match-tag[data-k="number"] { color: var(--success-text); background: var(--success-bg); border-color: var(--success-border); }
.match-tag[data-k="title"] { color: var(--info-text); background: var(--info-bg); border-color: var(--info-border); }
.result-row__snippet { margin-top: var(--space-2); font-size: var(--text-sm); line-height: 1.6; color: var(--ink-secondary); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

/* ── 7. Служебное ──────────────────────────────────────────── */
.state-box { display: grid; place-items: center; text-align: center; gap: var(--space-3); padding: var(--space-10) var(--space-6); border-radius: var(--radius-xl); border: 1.5px dashed var(--line-medium); }
.state-box .ico-tile { width: 60px; height: 60px; border-radius: 18px; }
.state-box h3 { font-size: var(--text-lg); font-weight: 800; }
.state-box p { max-width: 46ch; font-size: var(--text-sm); line-height: 1.6; color: var(--ink-tertiary); }
.state-box--error { border-color: var(--warning-border); background: var(--warning-bg); }
.footer { padding-block: var(--space-8) var(--space-10); border-top: 1px solid var(--line); text-align: center; font-size: var(--text-xs); line-height: 1.7; color: var(--ink-tertiary); }
.footer p { max-width: 80ch; margin-inline: auto; }
.toast-container { position: fixed; z-index: 100; left: 50%; bottom: 24px; transform: translateX(-50%); display: grid; gap: var(--space-2); width: max-content; max-width: calc(100vw - 32px); pointer-events: none; }
.toast { display: flex; align-items: center; gap: 10px; padding: 12px 18px; border-radius: var(--radius-lg); font-size: var(--text-sm); font-weight: 600; color: #fff; background: #111827; border: 1px solid rgba(255, 255, 255, .12); box-shadow: var(--shadow-lg); animation: rise .3s var(--ease) both; }
.toast--success > span:first-child { color: #34d399; }
.toast--error > span:first-child { color: #fbbf24; }
.toast-exit { opacity: 0; transform: translateY(8px); transition: all .3s; }

/* ── 8. Адаптив и печать ───────────────────────────────────── */
@media (min-width: 1024px) and (max-width: 1279px) {
  .layout { grid-template-columns: 232px minmax(0, 1fr); gap: var(--space-4); }
  .code-layout { grid-template-columns: minmax(250px, 300px) minmax(0, 1fr); gap: var(--space-4); }
  .reader { padding: var(--space-6) var(--space-6) var(--space-5); }
}
@media (max-width: 1023px) {
  .container { padding-inline: 16px; }
  .layout { grid-template-columns: minmax(0, 1fr); padding-top: var(--space-4); }
  .menu-btn { display: grid; }
  .sidebar { position: fixed; z-index: 60; left: 0; top: 0; bottom: 0; width: min(86vw, 320px); max-height: none; border-radius: 0 var(--radius-xl) var(--radius-xl) 0; padding-top: var(--space-4); transform: translateX(-104%); transition: transform var(--duration) var(--ease); box-shadow: var(--shadow-lg); }
  .sidebar.open { transform: none; }
  .sidebar-overlay.open { display: block; }
  .code-head .back-btn { display: inline-flex; }
  .hero-card { padding: 26px 22px 24px; border-radius: 22px; }
  .code-layout { grid-template-columns: minmax(0, 1fr); }
  .toc { position: static; max-height: none; }
  .toc__body { max-height: none; overflow: visible; }
  .code-layout[data-pane="toc"] .reader-wrap { display: none; }
  .code-layout[data-pane="reader"] .toc { display: none; }
  .to-toc { display: inline-flex; margin-bottom: var(--space-4); }
  .nav-link:not([aria-current]) { display: none; }
  .reader { padding: var(--space-6) var(--space-5) var(--space-5); }
}
@media (max-width: 640px) {
  .brand__pro, .nav-link[aria-current] { display: none; }
  .nav-actions { gap: 6px; }
  .status-badge #statusText { display: none; }
  .status-badge { padding: 9px; }
  .code-grid { grid-template-columns: 1fr; }
  .code-head__title { flex: 1 1 100%; }
  .hero-sub { font-size: var(--text-sm); }
  .search-input { height: 52px; }
  .result-row { grid-template-columns: 1fr; gap: var(--space-2); padding: var(--space-4); }
  .result-row__id { flex-direction: row; align-items: center; }
  .reader__nav { grid-template-columns: 1fr; }
  .nav-card--next { text-align: left; align-items: flex-start; }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; scroll-behavior: auto !important; }
}
@media print {
  body { background: #fff !important; color: #000 !important; }
  .ambient-bg, .topbar, .hero, .sidebar, .sidebar-overlay, .toc, .code-head, .reader__toolbar, .reader__nav, .footer, .toast-container, .to-toc, .tr-note { display: none !important; }
  .layout, .code-layout { display: block; }
  .reader-wrap { display: block !important; }
  .surface, .reader { box-shadow: none !important; border: 0 !important; background: #fff !important; padding: 0 !important; }
  .reader__body, .reader__body * { color: #000 !important; }
  .reader__badge { color: #000 !important; border-color: #888 !important; background: none !important; }
}
</style>
</head>
<body>

<div class="ambient-bg" aria-hidden="true"></div>

<div class="app-shell">

  <header class="topbar">
    <div class="container">
      <button class="menu-btn" id="menuBtn" type="button" aria-label="Кодексы" aria-controls="sidebar" aria-expanded="false">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <a href="/" class="brand" aria-label="LegalAI Pro">
        <span class="brand__mark" aria-hidden="true">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M6 7l-3.5 7a3.5 3.5 0 0 0 7 0L6 7Zm12 0l-3.5 7a3.5 3.5 0 0 0 7 0L18 7ZM4 21h16M4.5 7h15"/></svg>
        </span>
        <span>LegalAI</span><span class="brand__pro">PRO</span>
      </a>
      <nav class="nav-actions" aria-label="Основная навигация">
        <a href="/" class="nav-link" data-i18n="nav.home">← Главная</a>
        <span class="nav-link" aria-current="page" data-i18n="nav.codex">Кодексы</span>
        <button id="langToggle" class="lang-toggle" type="button" aria-label="Язык интерфейса / Забони интерфейс" title="Русский / Тоҷикӣ">
          <span data-lang="ru">RU</span><span data-lang="tg">ТҶ</span>
        </button>
        <button id="statusBadge" class="status-badge" data-state="checking" aria-live="polite" type="button">
          <span class="status-dot" aria-hidden="true"></span>
          <span id="statusText">…</span>
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

    <section class="hero" id="hero">
      <div class="hero-card">
        <div class="hero-eyebrow" data-i18n="hero.eyebrow">Законодательство Республики Таджикистан</div>
        <h1 class="hero-title" data-i18n="hero.title">Кодексы Республики Таджикистан</h1>
        <p class="hero-sub" data-i18n="hero.sub">Тексты статей из базы знаний LegalAI Pro: оглавление, чтение и поиск по номеру, названию и тексту.</p>
        <div class="hero-stats" id="heroStats"></div>
        <form class="search-box" id="searchForm" role="search" autocomplete="off">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <label for="globalSearch" class="sr-only" data-i18n="search.label">Поиск по кодексам</label>
          <input id="globalSearch" class="search-input" type="search" data-i18n-placeholder="search.placeholder" placeholder="Найти: «кража», «УК 244», «расторжение договора аренды»…">
          <button type="button" class="search-clear" id="searchClear" hidden aria-label="Очистить">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
          </button>
        </form>
        <div class="search-examples" id="searchExamples"></div>
      </div>
    </section>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="layout">
      <aside class="sidebar surface" id="sidebar" aria-label="Кодексы"></aside>
      <main class="main" id="main" aria-live="polite"></main>
    </div>

    <footer class="footer">
      <p id="footerNote"></p>
    </footer>

  </div>
</div>

<div class="toast-container" id="toastContainer" aria-live="assertive"></div>

<script>
'use strict';

const CONFIG = Object.freeze({
  API_BASE: "{{ rtrim($pythonApiUrl ?? env('PYTHON_API_URL', 'http://127.0.0.1:5000'), '/') }}",
  TIMEOUT: 30000,
  SEARCH_DEBOUNCE: 380,
  SEARCH_MIN: 2,
  TOC_ROWS: 200,
  RECENT_MAX: 8,
});

let UI_LANG = "{{ app()->getLocale() === 'tg' ? 'tg' : 'ru' }}";
const LANG_URLS = {
  ru: "{{ route('locale.switch', ['locale' => 'ru']) }}",
  tg: "{{ route('locale.switch', ['locale' => 'tg']) }}",
};

// ── Тексты интерфейса (RU / ТҶ) ─────────────────────────────────────────────
// Таджикские строки написаны вручную — их стоит показать носителю языка. Названия и тексты статей
// лежат в индексе по-русски; в таджикском интерфейсе их переводит сервер (/api/codex/translate).
const I18N = {
  ru: {
    'nav.home': '← Главная', 'nav.codex': 'Кодексы',
    'hero.eyebrow': 'Законодательство Республики Таджикистан',
    'hero.title': 'Кодексы Республики Таджикистан',
    'hero.sub': 'Тексты статей из базы знаний LegalAI Pro: оглавление, чтение и поиск по номеру, названию и тексту.',
    'stat.codes': '{n} кодексов', 'stat.articles': '{n} статей',
    'search.label': 'Поиск по кодексам',
    'side.title': 'Категории', 'side.all': 'Все кодексы',
    'search.placeholder': 'Найти: «кража», «УК 244», «расторжение договора аренды»…',
    'search.examples': 'Например:',
    'ex.1': 'УК 244', 'ex.2': 'ТК 80', 'ex.3': 'расторжение договора аренды', 'ex.4': 'налог на прибыль',
    'search.for': 'По запросу «{q}»', 'search.count': 'Найдено: {n}', 'search.all': 'Все', 'search.close': 'Закрыть поиск',
    'search.none': 'Ничего не найдено. Попробуйте другое слово или номер статьи.',
    'search.number': 'Номер', 'search.title': 'В названии', 'search.text': 'В тексте',
    'search.understood': 'Искали по русскому переводу запроса: «{q}»',
    'search.loading': 'Ищем…',
    'grp.civil': 'Гражданское право', 'grp.criminal': 'Уголовное право', 'grp.labor': 'Трудовое право',
    'grp.tax': 'Налоговое право', 'grp.family': 'Семейное право', 'grp.admin': 'Административное право',
    'grp.housing': 'Жильё и градостроительство', 'grp.nature': 'Природные ресурсы',
    'grp.transport': 'Транспорт', 'grp.health': 'Здравоохранение', 'grp.other': 'Другие кодексы',
    'card.stats': '{a} статей · {c} глав', 'card.open': 'Открыть',
    'recent.title': 'Недавно открывали', 'recent.clear': 'Очистить',
    'code.back': 'Все кодексы', 'code.switch': 'Сменить кодекс',
    'toc.title': 'Оглавление', 'toc.filter': 'Номер или название статьи…',
    'toc.none': 'Ничего не найдено', 'toc.more': 'Показать ещё {n}', 'toc.toToc': 'К оглавлению',
    'reader.emptyTitle': 'Выберите статью',
    'reader.emptyText': 'Откройте главу в оглавлении и выберите статью — её текст появится здесь.',
    'reader.article': 'Статья {n}', 'reader.copy': 'Копировать текст', 'reader.link': 'Скопировать ссылку',
    'reader.print': 'Печать', 'reader.orig': 'Оригинал (рус.)', 'reader.tr': 'Перевод на таджикский',
    'reader.prev': 'Предыдущая', 'reader.next': 'Следующая',
    'reader.machine': 'Машинный перевод; официальный текст кодекса — русский.',
    'status.checking': '…', 'status.ok': '✓ Кодексов: {n}', 'status.bad': '✗ Индекс недоступен',
    'err.title': 'Не удалось загрузить кодексы',
    'err.text': 'Сервис кодексов временно недоступен. Повторите попытку через несколько секунд.',
    'err.retry': 'Повторить', 'err.article': 'Не удалось загрузить статью',
    'foot.note': 'Тексты взяты из файлов кодексов, загруженных в систему; индекс обновлён {date}. Это не официальная публикация: для юридически значимых действий сверяйте текст с официальным источником.',
    'toast.copied': 'Текст скопирован', 'toast.linkCopied': 'Ссылка скопирована', 'toast.copyFail': 'Не удалось скопировать',
    'tr.failed': 'Часть текста перевести не удалось — показан русский оригинал.',
  },
  tg: {
    'nav.home': '← Асосӣ', 'nav.codex': 'Кодексҳо',
    'hero.eyebrow': 'Қонунгузории Ҷумҳурии Тоҷикистон',
    'hero.title': 'Кодексҳои Ҷумҳурии Тоҷикистон',
    'hero.sub': 'Матни моддаҳо аз пойгоҳи дониши LegalAI Pro: мундариҷа, мутолиа ва ҷустуҷӯ аз рӯи рақам, ном ва матн.',
    'stat.codes': '{n} кодекс', 'stat.articles': '{n} модда',
    'search.label': 'Ҷустуҷӯ дар кодексҳо',
    'side.title': 'Категорияҳо', 'side.all': 'Ҳамаи кодексҳо',
    'search.placeholder': 'Ҷустуҷӯ: «дуздӣ», «УК 244», «бекор кардани шартномаи иҷора»…',
    'search.examples': 'Масалан:',
    'ex.1': 'УК 244', 'ex.2': 'ТК 80', 'ex.3': 'бекор кардани шартномаи иҷора', 'ex.4': 'андоз аз фоида',
    'search.for': 'Аз рӯи дархости «{q}»', 'search.count': 'Ёфт шуд: {n}', 'search.all': 'Ҳама', 'search.close': 'Пӯшидани ҷустуҷӯ',
    'search.none': 'Чизе ёфт нашуд. Калимаи дигар ё рақами моддаро санҷед.',
    'search.number': 'Рақам', 'search.title': 'Дар ном', 'search.text': 'Дар матн',
    'search.understood': 'Аз рӯи тарҷумаи русии дархост ҷустуҷӯ шуд: «{q}»',
    'search.loading': 'Ҷустуҷӯ…',
    'grp.civil': 'Ҳуқуқи гражданӣ', 'grp.criminal': 'Ҳуқуқи ҷиноятӣ', 'grp.labor': 'Ҳуқуқи меҳнатӣ',
    'grp.tax': 'Ҳуқуқи андоз', 'grp.family': 'Ҳуқуқи оилавӣ', 'grp.admin': 'Ҳуқуқи маъмурӣ',
    'grp.housing': 'Манзил ва шаҳрсозӣ', 'grp.nature': 'Захираҳои табиӣ',
    'grp.transport': 'Нақлиёт', 'grp.health': 'Тандурустӣ', 'grp.other': 'Кодексҳои дигар',
    'card.stats': '{a} модда · {c} боб', 'card.open': 'Кушодан',
    'recent.title': 'Ба наздикӣ кушодаед', 'recent.clear': 'Пок кардан',
    'code.back': 'Ҳамаи кодексҳо', 'code.switch': 'Иваз кардани кодекс',
    'toc.title': 'Мундариҷа', 'toc.filter': 'Рақам ё номи модда…',
    'toc.none': 'Чизе ёфт нашуд', 'toc.more': 'Боз {n} нишон диҳед', 'toc.toToc': 'Ба мундариҷа',
    'reader.emptyTitle': 'Моддаро интихоб кунед',
    'reader.emptyText': 'Бобро дар мундариҷа кушоед ва моддаро интихоб кунед — матни он дар ин ҷо пайдо мешавад.',
    'reader.article': 'Моддаи {n}', 'reader.copy': 'Нусха бардоштани матн', 'reader.link': 'Нусхаи истинод',
    'reader.print': 'Чоп', 'reader.orig': 'Нусхаи аслӣ (русӣ)', 'reader.tr': 'Тарҷума ба тоҷикӣ',
    'reader.prev': 'Пештара', 'reader.next': 'Минбаъда',
    'reader.machine': 'Тарҷумаи мошинӣ; матни расмии кодекс — русӣ.',
    'status.checking': '…', 'status.ok': '✓ Кодексҳо: {n}', 'status.bad': '✗ Индекс дастнорас аст',
    'err.title': 'Кодексҳо бор карда нашуданд',
    'err.text': 'Хидмати кодексҳо муваққатан дастрас нест. Чанд сонияи дигар бори дигар кӯшиш кунед.',
    'err.retry': 'Такрор', 'err.article': 'Модда бор карда нашуд',
    'foot.note': 'Матнҳо аз файлҳои кодексҳое гирифта шудаанд, ки ба система бор карда шудаанд; индекс {date} навсозӣ шудааст. Ин нашри расмӣ нест: барои амалҳои аҳамияти ҳуқуқидошта матнро бо сарчашмаи расмӣ муқоиса кунед.',
    'toast.copied': 'Матн нусха шуд', 'toast.linkCopied': 'Истинод нусха шуд', 'toast.copyFail': 'Нусха бардошта нашуд',
    'tr.failed': 'Тарҷумаи як қисми матн муваффақ нашуд — нусхаи русӣ нишон дода шуд.',
  },
};

// Полные названия кодексов на таджикском (по id кодекса). Аббревиатуры (ГК РТ, УК РТ…) остаются как
// в индексе: по ним ищут («УК 244»).
const CODE_NAMES_TG = {
  gk: 'Кодекси граждании Ҷумҳурии Тоҷикистон',
  gpk: 'Кодекси мурофиавии граждании Ҷумҳурии Тоҷикистон',
  epk: 'Кодекси мурофиавии иқтисодии Ҷумҳурии Тоҷикистон',
  uk: 'Кодекси ҷиноятии Ҷумҳурии Тоҷикистон',
  upk: 'Кодекси мурофиавии ҷиноятии Ҷумҳурии Тоҷикистон',
  tk: 'Кодекси меҳнати Ҷумҳурии Тоҷикистон',
  nk: 'Кодекси андози Ҷумҳурии Тоҷикистон',
  sk: 'Кодекси оилаи Ҷумҳурии Тоҷикистон',
  koapr: 'Кодекси мурофиаи маъмурии Ҷумҳурии Тоҷикистон',
  pkoap: 'Кодекси мурофиавии Ҷумҳурии Тоҷикистон оид ба ҳуқуқвайронкуниҳои маъмурӣ',
  zhk: 'Кодекси манзили Ҷумҳурии Тоҷикистон',
  grk: 'Кодекси шаҳрсозии Ҷумҳурии Тоҷикистон',
  vdk: 'Кодекси оби Ҷумҳурии Тоҷикистон',
  lk: 'Кодекси ҷангали Ҷумҳурии Тоҷикистон',
  kat: 'Кодекси нақлиёти автомобилии Ҷумҳурии Тоҷикистон',
  vzdk: 'Кодекси ҳавоии Ҷумҳурии Тоҷикистон',
  kz: 'Кодекси тандурустии Ҷумҳурии Тоҷикистон',
};

function t(key, vars) {
  let text = (I18N[UI_LANG] && I18N[UI_LANG][key]) ?? I18N.ru[key] ?? key;
  if (vars) for (const name of Object.keys(vars)) text = text.split('{' + name + '}').join(String(vars[name]));
  return text;
}

const esc = value => String(value ?? '').replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));

// Названия разделов и глав в кодексах набраны ЗАГЛАВНЫМИ. Для чтения их приводим к обычному регистру;
// «Республика Таджикистан» остаётся с заглавными, остальное — предложением.
function sentenceCase(text) {
  const s = String(text || '').trim();
  if (!s || s !== s.toUpperCase() || s === s.toLowerCase()) return s;
  const lower = s.toLowerCase();
  return (lower.charAt(0).toUpperCase() + lower.slice(1))
    .replace(/\b(республик[а-яё]*)\s+(таджикистан[а-яё]*)/gi, (m, a, b) => a.charAt(0).toUpperCase() + a.slice(1) + ' ' + b.charAt(0).toUpperCase() + b.slice(1))
    .replace(/(^|\s)(таджикистан[а-яё]*)/gi, (m, sp, w) => sp + w.charAt(0).toUpperCase() + w.slice(1));
}

// Прокручивает только внутренний контейнер, а не всю страницу (scrollIntoView двигает и окно).
function scrollInside(container, item) {
  if (!container || !item) return;
  const box = container.getBoundingClientRect(), row = item.getBoundingClientRect();
  if (row.top < box.top + 8 || row.bottom > box.bottom - 8) container.scrollTop += row.top - box.top - box.height / 3;
}

function codeName(code) {
  return UI_LANG === 'tg' && CODE_NAMES_TG[code.id] ? CODE_NAMES_TG[code.id] : code.label;
}

// Цвет и значок группы кодексов
const GROUPS = {
  civil: { color: '#4f46e5', icon: '<path d="M8 3h8l4 4v14H4V3h4Z"/><path d="M16 3v4h4M8 12h8M8 16h6"/>' },
  criminal: { color: '#dc2626', icon: '<path d="M12 3 4 6v6c0 4.5 3.4 8 8 9 4.6-1 8-4.5 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/>' },
  labor: { color: '#d97706', icon: '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/>' },
  tax: { color: '#059669', icon: '<circle cx="12" cy="12" r="9"/><path d="M9 15l6-6M9.5 9.5h.01M14.5 14.5h.01"/>' },
  family: { color: '#db2777', icon: '<path d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.5-7 10-7 10Z"/>' },
  admin: { color: '#0891b2', icon: '<path d="M3 21h18M5 21V10l7-5 7 5v11M9 21v-6h6v6"/>' },
  housing: { color: '#7c3aed', icon: '<path d="M3 11 12 3l9 8M5 10v11h14V10M10 21v-6h4v6"/>' },
  nature: { color: '#16a34a', icon: '<path d="M12 21V11M12 11c0-4 3-7 8-7 0 5-3 8-8 7ZM12 14c0-3-2-5-6-5 0 4 2 6 6 5Z"/>' },
  transport: { color: '#2563eb', icon: '<path d="M3 13h15l3-3-3-2H9L3 13ZM5 17h14M7 17v2M17 17v2"/>' },
  health: { color: '#e11d48', icon: '<path d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.5-7 10-7 10Z"/><path d="M8 12h2l1-2 2 4 1-2h2"/>' },
  other: { color: '#64748b', icon: '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>' },
};
const groupOf = id => GROUPS[id] || GROUPS.other;
function ico(path, size = 18, extra = '') {
  return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" ${extra}>${path}</svg>`;
}
const ICONS = {
  arrow: '<path d="M5 12h14M13 6l6 6-6 6"/>', back: '<path d="M19 12H5M11 6l-6 6 6 6"/>',
  copy: '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1"/>',
  link: '<path d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/>',
  printer: '<path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="9" rx="2"/><path d="M7 14h10v7H7z"/>',
  globe: '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
  search: '<circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
  book: '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>',
  alert: '<path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
};

// ── Перевод названий и текстов на таджикский ───────────────────────────────
const TgText = {
  KEY: 'legalai_tg_cache_v1',      // общий кэш со страницей квалификации
  MAX_ENTRIES: 600, SHORT: 400, CHUNK: 20,
  mem: new Map(), _loaded: false,

  _load() {
    if (this._loaded) return;
    this._loaded = true;
    try {
      const saved = JSON.parse(localStorage.getItem(this.KEY));
      if (Array.isArray(saved)) saved.forEach(([k, v]) => this.mem.set(k, v));
    } catch { /* кэш пуст или недоступен */ }
  },
  _save() {
    try { localStorage.setItem(this.KEY, JSON.stringify([...this.mem.entries()].slice(-this.MAX_ENTRIES))); } catch { /* только память */ }
  },
  cached(text) { this._load(); return this.mem.get(String(text || '').trim()); },

  async _request(items) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), 150000);
    try {
      const response = await fetch(`${CONFIG.API_BASE}/api/codex/translate`, {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ items }), signal: controller.signal,
      });
      if (!response.ok) throw new Error('HTTP ' + response.status);
      return await response.json();
    } finally { clearTimeout(timer); }
  },

  async translate(texts) {
    this._load();
    const source = texts.map(s => String(s || '').trim());
    const out = source.slice();
    const pending = new Map();
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
    long.forEach(text => batches.push([text]));
    let failed = 0;
    await Promise.all(batches.map(async batch => {
      try {
        const data = await this._request(batch);
        batch.forEach((text, k) => {
          const value = data.items && data.items[k];
          if (data.translated && data.translated[k] && value && value !== text) {
            this.mem.set(text, value);
            pending.get(text).forEach(i => { out[i] = value; });
          } else failed += 1;
        });
      } catch { failed += batch.length; }
    }));
    this._save();
    return { items: out, failed };
  },

  // Заменяет текст узлов [data-tr] переводом. Оригинал остаётся в data-tr-src, перевод — в data-tr-out:
  // читалка по этим атрибутам переключает «оригинал / перевод».
  async apply(nodes) {
    if (UI_LANG !== 'tg') return;
    const fresh = nodes.filter(n => n && !n.dataset.trDone);
    if (fresh.length) {
      fresh.forEach(n => { n.dataset.trDone = '1'; n.dataset.trSrc = n.textContent; n.classList.add('is-translating'); });
      const job = this._run(fresh);
      fresh.forEach(n => { n._trJob = job; });
    }
    await Promise.all([...new Set(nodes.map(n => n && n._trJob).filter(Boolean))]);
  },
  async _run(list) {
    const { items, failed } = await this.translate(list.map(n => n.dataset.trSrc));
    list.forEach((n, i) => {
      n.classList.remove('is-translating');
      if (items[i] && items[i] !== n.dataset.trSrc.trim()) {
        n.dataset.trOut = items[i];
        if (!n.closest('[data-show-original="true"]')) n.textContent = items[i];
      }
    });
    // Одно уведомление за несколько секунд: оглавление, читалка и соседние статьи переводятся пачками.
    if (failed > 0 && Date.now() - (this._lastFailToast || 0) > 8000) {
      this._lastFailToast = Date.now();
      App.toast(t('tr.failed'), 'error');
    }
  },
};

// ── Тема ────────────────────────────────────────────────────────────────────
const Theme = {
  init() {
    const root = document.documentElement;
    const media = window.matchMedia('(prefers-color-scheme: dark)');
    let saved = null;
    try { saved = localStorage.getItem('legalai-theme'); } catch { /* нет хранилища */ }
    const set = (theme, save = true) => {
      root.setAttribute('data-theme', theme);
      document.getElementById('themeToggle').setAttribute('aria-pressed', String(theme === 'dark'));
      if (save) { try { localStorage.setItem('legalai-theme', theme); } catch { /* ignore */ } }
    };
    set(saved || (media.matches ? 'dark' : 'light'), false);
    document.getElementById('themeToggle').addEventListener('click', () => set(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'));
  },
};

// ── Приложение ──────────────────────────────────────────────────────────────
const App = {
  state: { catalog: null, codes: new Map(), toc: new Map(), articles: new Map(), view: 'home', codeId: null, article: null, query: '', filter: '', pane: 'toc', showOriginal: false, searchData: null, searchCode: null },
  el: {},

  async api(path, params) {
    const url = new URL(CONFIG.API_BASE + path, window.location.href);
    Object.entries(params || {}).forEach(([k, v]) => { if (v !== undefined && v !== null && v !== '') url.searchParams.set(k, v); });
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), CONFIG.TIMEOUT);
    try {
      const response = await fetch(url, { signal: controller.signal });
      if (!response.ok) { const error = new Error('HTTP ' + response.status); error.status = response.status; throw error; }
      return await response.json();
    } finally { clearTimeout(timer); }
  },

  toast(message, type = 'success') {
    const box = document.getElementById('toastContainer');
    const node = document.createElement('div');
    node.className = `toast toast--${type}`;
    node.innerHTML = `<span aria-hidden="true">${type === 'success' ? '✓' : '⚠️'}</span><span>${esc(message)}</span>`;
    box.appendChild(node);
    setTimeout(() => { node.classList.add('toast-exit'); setTimeout(() => node.remove(), 300); }, 3200);
  },

  // ---------- запуск ----------
  async init() {
    Theme.init();
    this.el = {
      main: document.getElementById('main'), hero: document.getElementById('hero'), search: document.getElementById('globalSearch'),
      sidebar: document.getElementById('sidebar'),
      clear: document.getElementById('searchClear'), stats: document.getElementById('heroStats'),
      badge: document.getElementById('statusBadge'), statusText: document.getElementById('statusText'),
    };
    this.applyI18n();
    this.bind();
    await this.loadCatalog();
    window.addEventListener('popstate', () => this.route(false));
    await this.route(false);
  },

  applyI18n() {
    document.documentElement.setAttribute('data-ui-lang', UI_LANG);
    document.documentElement.lang = UI_LANG === 'tg' ? 'tg' : 'ru';
    document.querySelectorAll('[data-i18n]').forEach(n => { n.textContent = t(n.dataset.i18n); });
    document.querySelectorAll('[data-i18n-placeholder]').forEach(n => { n.placeholder = t(n.dataset.i18nPlaceholder); });
    const box = document.getElementById('searchExamples');
    box.innerHTML = `<span>${esc(t('search.examples'))}</span>` + [1, 2, 3, 4].map(i => `<button type="button" data-example="${esc(t('ex.' + i))}">${esc(t('ex.' + i))}</button>`).join('');
  },

  bind() {
    document.getElementById('langToggle').addEventListener('click', () => { window.location.href = LANG_URLS[UI_LANG === 'tg' ? 'ru' : 'tg']; });
    this.el.badge.addEventListener('click', () => this.loadCatalog(true).then(() => this.route(false)));
    document.getElementById('searchExamples').addEventListener('click', e => {
      const b = e.target.closest('[data-example]');
      if (b) { this.el.search.value = b.dataset.example; this.runSearch(b.dataset.example); }
    });

    let timer;
    this.el.search.addEventListener('input', () => {
      const value = this.el.search.value;
      this.el.clear.hidden = value.length === 0;
      clearTimeout(timer);
      if (value.trim().length === 0) { this.closeSearch(); return; }
      if (value.trim().length >= CONFIG.SEARCH_MIN) timer = setTimeout(() => this.runSearch(value), CONFIG.SEARCH_DEBOUNCE);
    });
    document.getElementById('searchForm').addEventListener('submit', e => { e.preventDefault(); clearTimeout(timer); this.runSearch(this.el.search.value); });
    this.el.clear.addEventListener('click', () => { this.el.search.value = ''; this.el.clear.hidden = true; this.closeSearch(); this.el.search.focus(); });

    document.addEventListener('keydown', e => {
      const typing = /^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement?.tagName || '');
      if (e.key === '/' && !typing) { e.preventDefault(); this.el.search.focus(); }
      if (e.key === 'Escape' && document.activeElement === this.el.search) { this.el.search.value = ''; this.el.clear.hidden = true; this.closeSearch(); }
      if (!typing && this.state.view === 'code' && this.state.article) {
        if (e.key === 'ArrowLeft' && this.state.article.prev) this.openArticle(this.state.codeId, this.state.article.prev.n);
        if (e.key === 'ArrowRight' && this.state.article.next) this.openArticle(this.state.codeId, this.state.article.next.n);
      }
    });

    // Делегирование кликов по основному содержимому
    this.el.main.addEventListener('click', e => this.onMainClick(e));
    this.el.main.addEventListener('input', e => { if (e.target.id === 'tocFilter') { this.state.filter = e.target.value; this.renderToc(); } });
    this.el.main.addEventListener('toggle', e => this.onToggle(e), true);

    // Боковая панель: переход по кодексам; на телефоне выезжает по кнопке ☰
    this.el.sidebar.addEventListener('click', e => {
      if (e.target.closest('[data-act="home"]')) { this.go({}); return; }
      const item = e.target.closest('[data-code]');
      if (item) this.go({ code: item.dataset.code });
    });
    document.getElementById('menuBtn').addEventListener('click', () => this.toggleSidebar());
    document.getElementById('sidebarOverlay').addEventListener('click', () => this.toggleSidebar(false));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') this.toggleSidebar(false); });
  },

  // ---------- каталог ----------
  async loadCatalog(force = false) {
    if (this.state.catalog && !force) return;
    this.setStatus('checking');
    try {
      const catalog = await this.api('/api/codex/catalog');
      this.state.catalog = catalog;
      this.state.codes = new Map(catalog.codes.map(c => [c.id, c]));
      this.setStatus('ok', catalog.codes.length);
      this.renderHeroStats();
      this.renderSidebar();
      const date = catalog.built_at ? new Date(catalog.built_at * 1000).toLocaleDateString(UI_LANG === 'tg' ? 'tg-TJ' : 'ru-RU') : '—';
      document.getElementById('footerNote').textContent = t('foot.note', { date });
    } catch {
      this.state.catalog = null;
      this.setStatus('bad');
    }
  },
  setStatus(kind, n) {
    this.el.badge.setAttribute('data-state', kind === 'ok' ? 'connected' : kind === 'bad' ? 'disconnected' : 'checking');
    this.el.statusText.textContent = kind === 'ok' ? t('status.ok', { n }) : kind === 'bad' ? t('status.bad') : t('status.checking');
  },
  // Название без хвоста «Республики Таджикистан»: в узкой панели оно только мешает.
  shortName(code) {
    return codeName(code).replace(/\s+(Республики Таджикистан|Ҷумҳурии Тоҷикистон)\s*/i, ' ').replace(/\s+/g, ' ').trim();
  },
  renderSidebar() {
    const c = this.state.catalog;
    if (!c) { this.el.sidebar.innerHTML = ''; return; }
    const number = n => n.toLocaleString(UI_LANG === 'tg' ? 'tg-TJ' : 'ru-RU');
    const groups = c.groups.map(g => {
      const meta = groupOf(g.id);
      return `<div class="side-group" style="--g:${meta.color}">${esc(t('grp.' + g.id))}</div>` + g.codes.map(id => {
        const code = this.state.codes.get(id);
        return `<button class="side-item" type="button" data-code="${esc(id)}" style="--g:${meta.color}" title="${esc(codeName(code))}">
          <span class="ico-tile">${ico(meta.icon, 16)}</span><span class="side-item__name">${esc(this.shortName(code))}</span><span class="side-item__count">${number(code.articles)}</span></button>`;
      }).join('');
    }).join('');
    this.el.sidebar.innerHTML = `
      <div class="side-title">${esc(t('side.title'))}</div>
      <button class="side-item" type="button" data-act="home" id="sideAll">
        <span class="ico-tile" style="--g:#4f46e5">${ico(ICONS.book, 16)}</span><span class="side-item__name">${esc(t('side.all'))}</span><span class="side-item__count">${number(c.total_articles)}</span></button>
      ${groups}`;
    this.markSidebar();
  },
  markSidebar() {
    const side = this.el.sidebar;
    if (!side) return;
    const code = this.state.view === 'code' ? this.state.codeId : null;
    side.querySelectorAll('.side-item').forEach(item => {
      const active = item.dataset.code ? item.dataset.code === code : (item.id === 'sideAll' && this.state.view === 'home');
      item.classList.toggle('is-active', active);
      item.setAttribute('aria-current', active ? 'page' : 'false');
    });
    const current = side.querySelector('.side-item.is-active');
    if (current) scrollInside(side, current);
  },
  toggleSidebar(force) {
    const open = typeof force === 'boolean' ? force : !this.el.sidebar.classList.contains('open');
    this.el.sidebar.classList.toggle('open', open);
    document.getElementById('sidebarOverlay').classList.toggle('open', open);
    document.getElementById('menuBtn').setAttribute('aria-expanded', String(open));
  },
  renderHeroStats() {
    const c = this.state.catalog;
    this.el.stats.innerHTML = `
      <span class="stat-chip">${ico(ICONS.book, 15)}<span><b>${c.codes.length}</b> ${esc(t('stat.codes', { n: '' }).trim())}</span></span>
      <span class="stat-chip">${ico('<path d="M8 3h8l4 4v14H4V3h4Z"/><path d="M16 3v4h4"/>', 15)}<span><b>${c.total_articles.toLocaleString(UI_LANG === 'tg' ? 'tg-TJ' : 'ru-RU')}</b> ${esc(t('stat.articles', { n: '' }).trim())}</span></span>`;
  },

  // ---------- маршрутизация (адрес страницы = состояние) ----------
  readUrl() {
    const p = new URLSearchParams(window.location.search);
    return { code: p.get('code') || '', art: p.get('art') || '', q: p.get('q') || '' };
  },
  go(next, push = true) {
    this.toggleSidebar(false);
    const p = new URLSearchParams();
    if (next.q) p.set('q', next.q);
    else { if (next.code) p.set('code', next.code); if (next.art) p.set('art', next.art); }
    const url = window.location.pathname + (p.toString() ? '?' + p.toString() : '');
    if (push) history.pushState({}, '', url);
    return this.route(false);
  },
  async route() {
    const { code, art, q } = this.readUrl();
    if (!this.state.catalog) { this.renderError(); return; }
    this.el.hero.classList.toggle('hero--compact', !!(code || q));
    if (q) { this.el.search.value = q; this.el.clear.hidden = false; await this.runSearch(q, { push: false }); return; }
    if (code && this.state.codes.has(code)) { await this.openCode(code, art); return; }
    this.state.view = 'home';
    this.renderHome();
  },
  renderError() {
    this.el.main.innerHTML = `
      <div class="state-box state-box--error">
        <div class="ico-tile" style="--g:#d97706">${ico(ICONS.alert, 26)}</div>
        <h3>${esc(t('err.title'))}</h3><p>${esc(t('err.text'))}</p>
        <button class="btn btn--primary" type="button" data-act="retry">${esc(t('err.retry'))}</button>
      </div>`;
  },

  // ---------- главная ----------
  renderHome() {
    const c = this.state.catalog;
    this.markSidebar();
    const recent = this.getRecent().filter(r => this.state.codes.has(r.code));
    const recentHtml = recent.length ? `
      <section class="recent">
        <div class="recent__head"><h2>${esc(t('recent.title'))}</h2><button class="recent__clear" type="button" data-act="clear-recent">${esc(t('recent.clear'))}</button></div>
        <div class="recent__list">${recent.map(r => {
          const code = this.state.codes.get(r.code);
          return `<button class="recent-item" type="button" data-code="${esc(r.code)}" data-art="${esc(r.n)}" style="--g:${groupOf(code.group).color}">
            <small>${esc(code.short)} · ${esc(t('reader.article', { n: r.n }))}</small><span data-tr="title">${esc(r.t)}</span></button>`;
        }).join('')}</div>
      </section>` : '';
    const groups = c.groups.map((g, gi) => {
      const meta = groupOf(g.id);
      const codes = g.codes.map(id => this.state.codes.get(id));
      const total = codes.reduce((sum, code) => sum + code.articles, 0);
      return `
        <section class="group-block" style="--g:${meta.color};--gi:${gi}">
          <div class="group-head"><span class="ico-tile">${ico(meta.icon, 20)}</span><h2>${esc(t('grp.' + g.id))}</h2><span>${esc(t('stat.articles', { n: total.toLocaleString(UI_LANG === 'tg' ? 'tg-TJ' : 'ru-RU') }))}</span></div>
          <div class="code-grid">${codes.map(code => `
            <button class="code-card" type="button" data-code="${esc(code.id)}">
              <div class="code-card__top"><span class="code-card__short">${esc(code.short)}</span><span class="ico-tile" style="width:34px;height:34px;border-radius:10px">${ico(meta.icon, 17)}</span></div>
              <div class="code-card__name">${esc(codeName(code))}</div>
              <div class="code-card__stats"><span>${esc(t('card.stats', { a: code.articles, c: code.chapters }))}</span><span class="code-card__open">${esc(t('card.open'))} ${ico(ICONS.arrow, 14)}</span></div>
            </button>`).join('')}</div>
        </section>`;
    }).join('');
    this.el.main.innerHTML = recentHtml + groups;
    this.localize(this.el.main);
    document.title = 'LegalAI Pro — ' + t('hero.title');
  },

  // ---------- recent ----------
  getRecent() { try { const v = JSON.parse(localStorage.getItem('legalai_codex_recent')); return Array.isArray(v) ? v : []; } catch { return []; } },
  addRecent(code, article) {
    try {
      const list = this.getRecent().filter(r => !(r.code === code && r.n === article.number));
      list.unshift({ code, n: article.number, t: article.title });
      localStorage.setItem('legalai_codex_recent', JSON.stringify(list.slice(0, CONFIG.RECENT_MAX)));
    } catch { /* без хранилища «недавние» просто не копятся */ }
  },

  // ---------- кодекс ----------
  async openCode(id, art) {
    const code = this.state.codes.get(id);
    this.state.view = 'code';
    this.state.codeId = id;
    this.state.filter = '';
    this.state.article = null;
    this.markSidebar();
    this.state.pane = art ? 'reader' : 'toc';
    this.el.main.innerHTML = `<div class="code-head"><div class="skeleton" style="height:56px;flex:1"></div></div><div class="code-layout" data-pane="toc"><div class="skeleton" style="height:520px"></div><div class="skeleton" style="height:520px"></div></div>`;
    try {
      if (!this.state.toc.has(id)) this.state.toc.set(id, await this.api('/api/codex/toc', { code: id }));
    } catch { this.renderError(); return; }
    this.renderCodeShell(code);
    this.renderToc();
    document.title = `${code.short} — LegalAI Pro`;
    if (art) await this.openArticle(id, art, { push: false, scroll: false });
    else this.renderReaderEmpty();
  },

  renderCodeShell(code) {
    const meta = groupOf(code.group);
    this.el.main.innerHTML = `
      <div class="code-head" style="--g:${meta.color}">
        <button class="back-btn" type="button" data-act="home">${ico(ICONS.back, 15)}<span>${esc(t('code.back'))}</span></button>
        <div class="code-head__title">
          <span class="ico-tile">${ico(meta.icon, 20)}</span>
          <div><h2>${esc(codeName(code))}</h2><p>${esc(code.short)} · ${esc(t('card.stats', { a: code.articles, c: code.chapters }))}</p></div>
        </div>
      </div>
      <div class="code-layout" id="codeLayout" data-pane="${this.state.pane}" style="--g:${meta.color}">
        <aside class="toc surface" aria-label="${esc(t('toc.title'))}">
          <div class="toc__head">
            <h3>${esc(t('toc.title'))}</h3>
            <div class="toc-filter">${ico(ICONS.search, 16)}<input id="tocFilter" type="search" autocomplete="off" placeholder="${esc(t('toc.filter'))}" aria-label="${esc(t('toc.filter'))}"></div>
          </div>
          <div class="toc__body" id="tocBody"></div>
        </aside>
        <div class="reader-wrap" id="readerWrap"></div>
      </div>`;
  },

  // Оглавление: главы свёрнуты, статьи главы отрисовываются при раскрытии. Фильтр раскрывает совпавшие главы.
  renderToc() {
    const data = this.state.toc.get(this.state.codeId);
    const body = document.getElementById('tocBody');
    if (!data || !body) return;
    const filter = this.state.filter.trim().toLowerCase();
    const words = filter.split(/\s+/).filter(Boolean);
    const matches = a => {
      if (!words.length) return true;
      const title = (a.t + ' ' + (UI_LANG === 'tg' ? (TgText.cached(a.t) || '') : '')).toLowerCase();
      return words.every(w => a.n === w || a.n.startsWith(w) || title.includes(w));
    };
    let html = '', shown = 0, chapterIndex = 0, anyChapter = false;
    data.sections.forEach((section, si) => {
      let sectionHtml = '';
      section.chapters.forEach((chapter, ci) => {
        const list = filter ? chapter.articles.filter(matches) : chapter.articles;
        if (!list.length) return;
        anyChapter = true;
        const key = `${si}:${ci}`;
        const open = filter || this.state.article && chapter.articles.some(a => a.n === this.state.article.number);
        const rows = open ? this.tocRows(list, shown) : '';
        shown += open ? Math.min(list.length, Math.max(0, CONFIG.TOC_ROWS)) : 0;
        sectionHtml += `
          <details class="toc-chapter" data-key="${key}" ${open ? 'open' : ''}>
            <summary><span class="toc-chapter__title" ${chapter.title ? 'data-tr="title"' : ''}>${esc(chapter.title ? sentenceCase(chapter.title) : t('reader.article', { n: list[0].n }) + '…')}</span><span class="toc-count">${list.length}</span></summary>
            <div class="toc-list" data-list="${key}">${rows}</div>
          </details>`;
        chapterIndex += 1;
      });
      if (sectionHtml) html += (section.title ? `<div class="toc-section" data-tr="title">${esc(sentenceCase(section.title))}</div>` : '') + sectionHtml;
    });
    body.innerHTML = anyChapter ? html : `<div class="toc-empty">${esc(t('toc.none'))}</div>`;
    this.localize(body, { onlyOpen: true });
    const active = body.querySelector('.toc-item.is-active');
    if (active && !filter) scrollInside(body, active);
  },
  tocRows(list, _offset = 0) {
    const cap = CONFIG.TOC_ROWS;
    const rows = list.slice(0, cap).map(a => this.tocItem(a)).join('');
    const rest = list.length - cap;
    return rows + (rest > 0 ? `<button class="toc-more" type="button" data-act="toc-more">${esc(t('toc.more', { n: rest }))}</button>` : '');
  },
  tocItem(a) {
    const active = this.state.article && this.state.article.number === a.n;
    return `<button class="toc-item ${active ? 'is-active' : ''}" type="button" data-n="${esc(a.n)}"><span class="toc-n">${esc(a.n)}</span><span class="toc-t" data-tr="title">${esc(a.t)}</span></button>`;
  },
  onToggle(e) {
    const d = e.target;
    if (!(d instanceof HTMLDetailsElement) || !d.classList.contains('toc-chapter') || !d.open) return;
    const list = d.querySelector('.toc-list');
    if (list && !list.children.length) {
      const [si, ci] = d.dataset.key.split(':').map(Number);
      const chapter = this.state.toc.get(this.state.codeId).sections[si].chapters[ci];
      list.innerHTML = this.tocRows(chapter.articles);
    }
    this.localize(d);
  },

  renderReaderEmpty() {
    const wrap = document.getElementById('readerWrap');
    if (!wrap) return;
    wrap.innerHTML = `<div class="reader-empty"><div><span class="ico-tile" style="--g:${groupOf(this.state.codes.get(this.state.codeId).group).color}">${ico(ICONS.book, 28)}</span><h3>${esc(t('reader.emptyTitle'))}</h3><p>${esc(t('reader.emptyText'))}</p></div></div>`;
  },

  // ---------- статья ----------
  async openArticle(codeId, number, { push = true, scroll = true } = {}) {
    const wrap = document.getElementById('readerWrap');
    if (!wrap) return;
    if (push) { const p = new URLSearchParams({ code: codeId, art: number }); history.pushState({}, '', window.location.pathname + '?' + p); }
    this.state.pane = 'reader';
    const layout = document.getElementById('codeLayout');
    if (layout) layout.dataset.pane = 'reader';
    wrap.innerHTML = '<div class="skeleton reader-load"></div>';
    const key = codeId + ':' + number;
    try {
      if (!this.state.articles.has(key)) this.state.articles.set(key, await this.api('/api/codex/article', { code: codeId, n: number }));
    } catch {
      wrap.innerHTML = `<div class="state-box state-box--error"><div class="ico-tile" style="--g:#d97706">${ico(ICONS.alert, 24)}</div><h3>${esc(t('err.article'))}</h3><button class="btn btn--ghost" type="button" data-act="back-toc">${esc(t('toc.toToc'))}</button></div>`;
      return;
    }
    const a = this.state.articles.get(key);
    this.state.article = a;
    this.state.showOriginal = false;
    this.addRecent(codeId, a);
    this.renderReader(a);
    this.markActive(number);
    document.title = `${a.citation} — ${a.title} — LegalAI Pro`;
    if (scroll) window.scrollTo({ top: Math.max(0, document.getElementById('codeLayout').getBoundingClientRect().top + window.scrollY - 90), behavior: 'smooth' });
  },

  markActive(number) {
    document.querySelectorAll('.toc-item.is-active').forEach(n => n.classList.remove('is-active'));
    const item = document.querySelector(`.toc-item[data-n="${CSS.escape(number)}"]`);
    if (item) { item.classList.add('is-active'); scrollInside(document.getElementById('tocBody'), item); }
    else this.renderToc();     // глава статьи ещё не открыта (например, переход «следующая»)
  },

  renderReader(a) {
    const wrap = document.getElementById('readerWrap');
    const code = this.state.codes.get(a.code.id);
    const tg = UI_LANG === 'tg';
    const crumbs = [codeName(code), sentenceCase(a.section), sentenceCase(a.chapter)].filter(Boolean)
      .map((c, i) => `<span ${i ? 'data-tr="title"' : ''}>${esc(c)}</span>`).join('');
    const blocks = a.blocks.map(b => {
      if (b.kind === 'note') return `<p class="note" data-tr="text">${esc(b.text)}</p>`;
      if (b.kind === 'li') return `<div class="li ${/^\s*[а-яё]\)|^\s*[-–—•]/i.test(b.text) ? 'sub' : ''}" data-tr="text">${esc(b.text)}</div>`;
      return `<p data-tr="text">${esc(b.text)}</p>`;
    }).join('');
    const prev = a.prev, next = a.next;
    wrap.innerHTML = `
      <button class="back-btn to-toc" type="button" data-act="back-toc">${ico(ICONS.back, 15)}<span>${esc(t('toc.toToc'))}</span></button>
      <article class="reader surface" id="reader" data-show-original="false">
        <nav class="crumbs" aria-label="breadcrumb">${crumbs}</nav>
        <div class="reader__badge">${esc(a.code.short)} · ${esc(t('reader.article', { n: a.number }))}</div>
        <h2 class="reader__title" data-tr="title">${esc(a.title)}</h2>
        <div class="reader__body" id="readerBody">${blocks || ''}</div>
        ${tg ? `<div class="tr-note" id="trNote" hidden>${ico(ICONS.globe, 14)}<span>${esc(t('reader.machine'))}</span></div>` : ''}
        <div class="reader__toolbar">
          <button class="chip-btn" type="button" data-act="copy-text">${ico(ICONS.copy, 15)}<span>${esc(t('reader.copy'))}</span></button>
          <button class="chip-btn" type="button" data-act="copy-link">${ico(ICONS.link, 15)}<span>${esc(t('reader.link'))}</span></button>
          <button class="chip-btn" type="button" data-act="print">${ico(ICONS.printer, 15)}<span>${esc(t('reader.print'))}</span></button>
          ${tg ? `<button class="chip-btn" type="button" data-act="toggle-orig" id="btnOrig">${ico(ICONS.globe, 15)}<span>${esc(t('reader.orig'))}</span></button>` : ''}
        </div>
        <div class="reader__nav">
          <button class="nav-card" type="button" data-nav="${prev ? esc(prev.n) : ''}" ${prev ? '' : 'disabled'}><small>← ${esc(t('reader.prev'))}</small><b>${prev ? esc(t('reader.article', { n: prev.n }) + '. ') + '<span data-tr="title">' + esc(prev.t) + '</span>' : '—'}</b></button>
          <button class="nav-card nav-card--next" type="button" data-nav="${next ? esc(next.n) : ''}" ${next ? '' : 'disabled'}><small>${esc(t('reader.next'))} →</small><b>${next ? esc(t('reader.article', { n: next.n }) + '. ') + '<span data-tr="title">' + esc(next.t) + '</span>' : '—'}</b></button>
        </div>
      </article>`;
    if (tg) {
      const nodes = [...wrap.querySelectorAll('[data-tr]')];
      TgText.apply(nodes).then(() => { const note = document.getElementById('trNote'); if (note && nodes.some(n => n.dataset.trOut)) note.hidden = false; });
    }
  },

  toggleOriginal() {
    const reader = document.getElementById('reader');
    if (!reader) return;
    this.state.showOriginal = !this.state.showOriginal;
    reader.dataset.showOriginal = String(this.state.showOriginal);
    reader.querySelectorAll('[data-tr]').forEach(n => {
      if (!n.dataset.trSrc) return;
      n.textContent = this.state.showOriginal ? n.dataset.trSrc : (n.dataset.trOut || n.dataset.trSrc);
    });
    document.querySelector('#btnOrig span').textContent = t(this.state.showOriginal ? 'reader.tr' : 'reader.orig');
  },

  readerText() {
    const a = this.state.article;
    const body = [...document.querySelectorAll('#readerBody > *')].map(n => n.textContent.trim()).filter(Boolean).join('\n\n');
    const title = document.querySelector('#reader .reader__title')?.textContent.trim() || a.title;
    return `${a.code.short}, ${t('reader.article', { n: a.number })}. ${title}\n\n${body}`;
  },

  async copy(text, okKey) {
    try { await navigator.clipboard.writeText(text); this.toast(t(okKey)); return true; }
    catch { this.toast(t('toast.copyFail'), 'error'); return false; }
  },

  // ---------- поиск ----------
  closeSearch() {
    if (this.state.view !== 'search') return;
    this.go({ code: this.state.prevCode || '' });
  },

  async runSearch(raw, { push = true } = {}) {
    const q = String(raw || '').trim();
    if (q.length < CONFIG.SEARCH_MIN) return;
    const wasSearch = this.state.view === 'search';
    this.state.prevCode = this.state.view === 'code' ? this.state.codeId : (this.state.prevCode || '');
    this.state.view = 'search';
    this.state.query = q;
    this.state.searchCode = null;
    this.markSidebar();
    this.el.hero.classList.add('hero--compact');
    // Пока человек печатает, запись истории одна: «назад» ведёт из поиска, а не по каждой букве.
    if (push) history[wasSearch ? 'replaceState' : 'pushState']({}, '', window.location.pathname + '?' + new URLSearchParams({ q }));
    this.el.main.innerHTML = `<div class="results-head"><h2>${esc(t('search.loading'))}</h2></div><div class="result-list">${'<div class="skeleton" style="height:96px"></div>'.repeat(4)}</div>`;
    try {
      const data = await this.api('/api/codex/search', { q, limit: 60 });
      if (this.state.query !== q) return;     // пока шёл запрос, человек набрал другое
      this.state.searchData = data;
      this.renderResults();
    } catch { this.renderError(); }
  },

  renderResults() {
    const data = this.state.searchData;
    const filterCode = this.state.searchCode;
    const all = data.results;
    const counts = new Map();
    all.forEach(r => counts.set(r.code, (counts.get(r.code) || 0) + 1));
    const list = filterCode ? all.filter(r => r.code === filterCode) : all;
    const words = [...new Set((data.query_ru || data.query).toLowerCase().split(/[^\wа-яёӣӯҳқғҷ]+/i).filter(w => w.length >= 3))];
    // В таджикском режиме текст заменяется переводом, и подсветка русских слов потеряла бы смысл.
    const hl = text => {
      if (UI_LANG === 'tg') return esc(text);
      let out = esc(text);
      words.forEach(w => { out = out.replace(new RegExp('(' + w.slice(0, Math.max(4, w.length - 2)).replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '[а-яёa-z]*)', 'ig'), '<mark>$1</mark>'); });
      return out;
    };
    const chips = counts.size > 1 ? `<div class="filter-chips">
        <button class="filter-chip" type="button" data-filter="" aria-pressed="${!filterCode}">${esc(t('search.all'))} <b>${all.length}</b></button>
        ${[...counts].map(([id, n]) => `<button class="filter-chip" type="button" data-filter="${esc(id)}" aria-pressed="${filterCode === id}">${esc(this.state.codes.get(id).short)} <b>${n}</b></button>`).join('')}
      </div>` : '';
    const rows = list.map((r, i) => {
      const code = this.state.codes.get(r.code);
      return `<button class="result-row" type="button" data-code="${esc(r.code)}" data-art="${esc(r.n)}" style="--g:${groupOf(code.group).color};--i:${i}">
        <div class="result-row__id"><span class="code-card__short">${esc(r.short)}</span><span class="result-row__n">${esc(r.n)}</span></div>
        <div>
          <div class="result-row__title" data-tr="title">${hl(r.t)}</div>
          <div class="result-row__meta"><span class="match-tag" data-k="${esc(r.match)}">${esc(t('search.' + r.match))}</span>${r.chapter ? `<span data-tr="title">${esc(r.chapter)}</span>` : ''}</div>
          ${r.snippet ? `<div class="result-row__snippet" data-tr="snippet">${hl(r.snippet)}</div>` : ''}
        </div></button>`;
    }).join('');
    this.el.main.innerHTML = `
      <div class="results-head">
        <h2>${esc(t('search.for', { q: data.query }))} <span style="color:var(--ink-tertiary);font-weight:600">· ${esc(t('search.count', { n: list.length }))}</span></h2>
        <button class="back-btn" type="button" data-act="close-search">${ico('<path d="M18 6 6 18M6 6l12 12"/>', 14)}<span>${esc(t('search.close'))}</span></button>
      </div>
      ${data.query_ru ? `<div class="results-note">${esc(t('search.understood', { q: data.query_ru }))}</div>` : ''}
      ${chips}
      ${list.length ? `<div class="result-list">${rows}</div>` : `<div class="state-box"><div class="ico-tile">${ico(ICONS.search, 26)}</div><h3>${esc(t('search.none'))}</h3></div>`}`;
    this.localize(this.el.main);
    document.title = `${data.query} — LegalAI Pro`;
  },

  // Переводит узлы [data-tr] внутри root. В оглавлении статьи закрытых глав ещё не отрисованы,
  // а заголовки самих глав (summary) переводятся всегда.
  localize(root, { onlyOpen = false } = {}) {
    if (UI_LANG !== 'tg') return;
    const nodes = [...root.querySelectorAll('[data-tr]')].filter(n => {
      if (onlyOpen && n.closest('.toc-list') && n.closest('details:not([open])')) return false;
      return true;
    });
    TgText.apply(nodes);
  },

  // ---------- клики ----------
  onMainClick(e) {
    const act = e.target.closest('[data-act]')?.dataset.act;
    if (act === 'retry') { this.loadCatalog(true).then(() => this.route(false)); return; }
    if (act === 'home') { this.go({}); return; }
    if (act === 'close-search') { this.el.search.value = ''; this.el.clear.hidden = true; this.go({ code: this.state.prevCode || '' }); return; }
    if (act === 'clear-recent') { try { localStorage.removeItem('legalai_codex_recent'); } catch { /* ignore */ } this.renderHome(); return; }
    if (act === 'back-toc') { this.state.pane = 'toc'; document.getElementById('codeLayout').dataset.pane = 'toc'; window.scrollTo({ top: 0, behavior: 'smooth' }); return; }
    if (act === 'toc-more') {
      const list = e.target.closest('.toc-list');
      const chapter = this.state.toc.get(this.state.codeId).sections.flatMap(s => s.chapters).find(c => c.articles.some(a => a.n === list.querySelector('.toc-item')?.dataset.n));
      if (chapter) { list.innerHTML = chapter.articles.map(a => this.tocItem(a)).join(''); this.localize(list); }
      return;
    }
    if (act === 'copy-text') { this.copy(this.readerText(), 'toast.copied').then(ok => { if (ok) this.flash(e.target.closest('button')); }); return; }
    if (act === 'copy-link') { const a = this.state.article; this.copy(`${window.location.origin}${window.location.pathname}?code=${encodeURIComponent(a.code.id)}&art=${encodeURIComponent(a.number)}`, 'toast.linkCopied'); return; }
    if (act === 'print') { window.print(); return; }
    if (act === 'toggle-orig') { this.toggleOriginal(); return; }

    const filter = e.target.closest('[data-filter]');
    if (filter) { this.state.searchCode = filter.dataset.filter || null; this.renderResults(); return; }
    const nav = e.target.closest('[data-nav]');
    if (nav && nav.dataset.nav) { this.openArticle(this.state.codeId, nav.dataset.nav); return; }
    const item = e.target.closest('.toc-item');
    if (item) { this.openArticle(this.state.codeId, item.dataset.n); return; }
    const card = e.target.closest('[data-code]');
    if (card) { this.go({ code: card.dataset.code, art: card.dataset.art || '' }); }
  },
  flash(btn) {
    if (!btn) return;
    btn.dataset.done = 'true';
    setTimeout(() => { delete btn.dataset.done; }, 1800);
  },
};

document.addEventListener('DOMContentLoaded', () => App.init());
</script>
</body>
</html>
