<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="description" content="LegalAI Pro — квалификация по УК Таджикистана: ответ строится только по тексту кодекса из RAG-индекса">
<meta name="theme-color" content="#05050a" media="(prefers-color-scheme: dark)">
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<title>LegalAI Pro — Анализ УК РТ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════════
   DESIGN TOKENS — Senior-level CSS Architecture
   ═══════════════════════════════════════════════════════════ */
:root {
  --ease-out-expo: cubic-bezier(0.16, 1, 0.3, 1);
  --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
  --ease-smooth: cubic-bezier(0.4, 0, 0.2, 1);
  --ease-in-out: cubic-bezier(0.65, 0, 0.35, 1);
  --duration-instant: 0.1s;
  --duration-fast: 0.15s;
  --duration-normal: 0.3s;
  --duration-slow: 0.6s;
  --duration-slower: 1s;
  --radius-xs: 4px;
  --radius-sm: 6px;
  --radius-md: 12px;
  --radius-lg: 18px;
  --radius-xl: 24px;
  --radius-2xl: 32px;
  --radius-full: 9999px;
  --space-1: 0.25rem;
  --space-2: 0.5rem;
  --space-3: 0.75rem;
  --space-4: 1rem;
  --space-5: 1.25rem;
  --space-6: 1.5rem;
  --space-8: 2rem;
  --space-10: 2.5rem;
  --space-12: 3rem;
  --space-16: 4rem;
  --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
  --font-display: 'Manrope', sans-serif;
  --font-mono: 'JetBrains Mono', monospace;
  --text-xs: 0.6875rem;
  --text-sm: 0.8125rem;
  --text-base: 0.9375rem;
  --text-lg: 1.0625rem;
  --text-xl: 1.25rem;
  --text-2xl: 1.5rem;
  --text-3xl: 2rem;
  --text-4xl: 2.75rem;
  --leading-tight: 1.1;
  --leading-normal: 1.5;
  --leading-relaxed: 1.7;
  --tracking-tight: -0.03em;
  --tracking-normal: -0.011em;
  --tracking-wide: 0.06em;
  --z-base: 1;
  --z-dropdown: 100;
  --z-sticky: 200;
  --z-overlay: 500;
  --z-toast: 1000;
  --container-max: 1280px;
  --container-padding: clamp(1rem, 4vw, 2rem);
}

[data-theme="dark"] {
  --bg-app: #05050a;
  --bg-elevated: #0f0f17;
  --bg-sunken: #0a0a12;
  --bg-glass: rgba(15, 15, 23, 0.82);
  --bg-overlay: rgba(5, 5, 10, 0.9);
  --ink-primary: #fafaf9;
  --ink-secondary: #d6d3d1;
  --ink-tertiary: #a8a29e;
  --ink-quaternary: #78716c;
  --line-subtle: rgba(250,250,249,0.06);
  --line-medium: rgba(250,250,249,0.12);
  --line-strong: rgba(250,250,249,0.18);
  --accent-red: #f87171;
  --accent-red-dark: #dc2626;
  --accent-red-glow: rgba(248, 113, 113, 0.4);
  --accent-blue: #60a5fa;
  --accent-purple: #a78bfa;
  --accent-green: #34d399;
  --accent-orange: #fb923c;
  --accent-pink: #f472b6;
  --accent-amber: #fbbf24;
  --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #7f1d1d 100%);
  --gradient-accent: linear-gradient(135deg, #f87171 0%, #dc2626 50%, #991b1b 100%);
  --gradient-subtle: linear-gradient(135deg, rgba(248,113,113,0.08), rgba(167,139,250,0.08));
  --gradient-success: linear-gradient(135deg, #34d399, #059669);
  --shadow-sm: 0 2px 8px rgba(0,0,0,0.4), 0 1px 2px rgba(0,0,0,0.3);
  --shadow-md: 0 8px 24px -4px rgba(0,0,0,0.5), 0 4px 8px -2px rgba(0,0,0,0.3);
  --shadow-lg: 0 20px 48px -8px rgba(0,0,0,0.6), 0 8px 16px -4px rgba(0,0,0,0.4);
  --shadow-xl: 0 32px 72px -12px rgba(0,0,0,0.7), 0 16px 24px -8px rgba(0,0,0,0.5);
  --shadow-glow: 0 0 48px var(--accent-red-glow);
  --shadow-inset: inset 0 1px 0 rgba(255,255,255,0.06);
  --noise-opacity: 0.03;
  --code-bg: rgba(0,0,0,0.35);
  --warning-bg: rgba(251,191,36,0.08);
  --warning-border: rgba(251,191,36,0.25);
  --warning-text: #fbbf24;
  --info-bg: rgba(96,165,250,0.08);
  --info-border: rgba(96,165,250,0.25);
  --info-text: #60a5fa;
  --success-bg: rgba(52,211,153,0.08);
  --success-border: rgba(52,211,153,0.25);
  --success-text: #34d399;
  --surface-hover: rgba(248,113,113,0.06);
  --surface-active: rgba(248,113,113,0.1);
  --scrollbar-thumb: rgba(250,250,249,0.12);
  --scrollbar-thumb-hover: rgba(250,250,249,0.22);
  --skeleton-base: rgba(250,250,249,0.04);
  --skeleton-shine: rgba(250,250,249,0.08);
}

[data-theme="light"] {
  --bg-app: #f8f7f5;
  --bg-elevated: #ffffff;
  --bg-sunken: #f0eeeb;
  --bg-glass: rgba(255, 255, 255, 0.9);
  --bg-overlay: rgba(248, 247, 245, 0.92);
  --ink-primary: #1c1917;
  --ink-secondary: #44403c;
  --ink-tertiary: #78716c;
  --ink-quaternary: #a8a29e;
  --line-subtle: rgba(28,25,23,0.06);
  --line-medium: rgba(28,25,23,0.12);
  --line-strong: rgba(28,25,23,0.2);
  --accent-red: #dc2626;
  --accent-red-dark: #991b1b;
  --accent-red-glow: rgba(220, 38, 38, 0.2);
  --accent-blue: #2563eb;
  --accent-purple: #7c3aed;
  --accent-green: #059669;
  --accent-orange: #d97706;
  --accent-pink: #db2777;
  --accent-amber: #d97706;
  --gradient-hero: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 50%, #fecaca 100%);
  --gradient-accent: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #7f1d1d 100%);
  --gradient-subtle: linear-gradient(135deg, rgba(220,38,38,0.05), rgba(124,58,237,0.05));
  --gradient-success: linear-gradient(135deg, #059669, #047857);
  --shadow-sm: 0 2px 8px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
  --shadow-md: 0 8px 24px -4px rgba(0,0,0,0.07), 0 4px 8px -2px rgba(0,0,0,0.03);
  --shadow-lg: 0 20px 48px -8px rgba(0,0,0,0.09), 0 8px 16px -4px rgba(0,0,0,0.05);
  --shadow-xl: 0 32px 72px -12px rgba(0,0,0,0.11), 0 16px 24px -8px rgba(0,0,0,0.07);
  --shadow-glow: 0 0 32px var(--accent-red-glow);
  --shadow-inset: inset 0 1px 0 rgba(255,255,255,0.8);
  --noise-opacity: 0.015;
  --code-bg: rgba(0,0,0,0.04);
  --warning-bg: rgba(251,191,36,0.1);
  --warning-border: rgba(217,119,6,0.25);
  --warning-text: #b45309;
  --info-bg: rgba(37,99,235,0.06);
  --info-border: rgba(37,99,235,0.2);
  --info-text: #1d4ed8;
  --success-bg: rgba(5,150,105,0.06);
  --success-border: rgba(5,150,105,0.25);
  --success-text: #059669;
  --surface-hover: rgba(220,38,38,0.04);
  --surface-active: rgba(220,38,38,0.08);
  --scrollbar-thumb: rgba(28,25,23,0.12);
  --scrollbar-thumb-hover: rgba(28,25,23,0.22);
  --skeleton-base: rgba(28,25,23,0.04);
  --skeleton-shine: rgba(28,25,23,0.08);
}

/* ═══════════════════════════════════════════════════════════
   RESET & BASE
   ═══════════════════════════════════════════════════════════ */
*, *::before, *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  -webkit-tap-highlight-color: transparent;
}

html {
  scroll-behavior: smooth;
  text-size-adjust: 100%;
}

body {
  font-family: var(--font-sans);
  color: var(--ink-primary);
  background: var(--bg-app);
  min-height: 100vh;
  min-height: 100dvh;
  overflow-x: hidden;
  letter-spacing: var(--tracking-normal);
  line-height: var(--leading-normal);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  transition: background var(--duration-normal) var(--ease-smooth),
              color var(--duration-normal) var(--ease-smooth);
}

/* Noise texture overlay */
body::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.4'/%3E%3C/svg%3E");
  opacity: var(--noise-opacity);
  pointer-events: none;
  z-index: var(--z-base);
  mix-blend-mode: overlay;
}

/* ═══════════════════════════════════════════════════════════
   AMBIENT BACKGROUND
   ═══════════════════════════════════════════════════════════ */
.ambient-bg {
  position: fixed;
  inset: 0;
  z-index: 0;
  overflow: hidden;
  pointer-events: none;
}

.ambient-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(130px);
  opacity: 0.45;
  will-change: transform;
  animation: blob-drift 40s ease-in-out infinite;
}

.blob-1 {
  width: 650px; height: 650px;
  background: radial-gradient(circle, rgba(220,38,38,0.6) 0%, transparent 70%);
  top: -12%; left: -8%;
}

.blob-2 {
  width: 550px; height: 550px;
  background: radial-gradient(circle, rgba(167,139,250,0.4) 0%, transparent 70%);
  top: 30%; right: -12%;
  animation-delay: -13s;
}

.blob-3 {
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(96,165,250,0.25) 0%, transparent 70%);
  bottom: -15%; left: 20%;
  animation-delay: -22s;
}

@keyframes blob-drift {
  0%, 100% { transform: translate(0, 0) scale(1) rotate(0deg); }
  25% { transform: translate(80px, -60px) scale(1.1) rotate(2deg); }
  50% { transform: translate(-40px, 100px) scale(0.95) rotate(-1deg); }
  75% { transform: translate(60px, 40px) scale(1.05) rotate(1deg); }
}

/* ═══════════════════════════════════════════════════════════
   LAYOUT
   ═══════════════════════════════════════════════════════════ */
.app-shell {
  position: relative;
  z-index: 2;
  padding: var(--space-4) 0 var(--space-16);
}

.container {
  max-width: var(--container-max);
  margin: 0 auto;
  padding: 0 var(--container-padding);
}

/* ═══════════════════════════════════════════════════════════
   NAVBAR
   ═══════════════════════════════════════════════════════════ */
.navbar {
  background: var(--bg-glass);
  backdrop-filter: blur(24px) saturate(180%);
  -webkit-backdrop-filter: blur(24px) saturate(180%);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-lg);
  margin: var(--space-4) auto;
  padding: var(--space-3) var(--space-6);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-4);
  flex-wrap: wrap;
  box-shadow: var(--shadow-sm);
  transition: background var(--duration-normal), border-color var(--duration-normal);
}

.brand {
  font-family: var(--font-display);
  font-weight: 800;
  font-size: var(--text-xl);
  letter-spacing: var(--tracking-tight);
  background: var(--gradient-accent);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  transition: opacity var(--duration-fast);
}

.brand:hover { opacity: 0.85; }

.nav-actions {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  flex-wrap: wrap;
}

.nav-link {
  padding: var(--space-2) var(--space-4);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  font-weight: 500;
  color: var(--ink-secondary);
  text-decoration: none;
  border: 1px solid transparent;
  transition: all var(--duration-fast) var(--ease-smooth);
}

.nav-link:hover {
  color: var(--ink-primary);
  background: var(--surface-hover);
  border-color: var(--line-subtle);
}

.nav-link[aria-current="page"] {
  color: var(--accent-red);
  background: var(--surface-active);
  border-color: rgba(220,38,38,0.12);
  font-weight: 600;
}

/* Status indicator */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-3);
  border-radius: var(--radius-full);
  font-size: var(--text-xs);
  font-weight: 600;
  font-family: var(--font-mono);
  letter-spacing: 0.02em;
  border: 1px solid var(--line-medium);
  background: var(--bg-elevated);
  cursor: pointer;
  transition: all var(--duration-normal) var(--ease-smooth);
  user-select: none;
}

.status-badge:hover { border-color: var(--line-strong); transform: translateY(-1px); }

.status-badge[data-state="connected"] {
  color: var(--success-text);
  border-color: var(--success-border);
  background: var(--success-bg);
}

.status-badge[data-state="disconnected"] {
  color: var(--accent-orange);
  border-color: rgba(251,146,60,0.3);
  background: rgba(251,146,60,0.06);
}

.status-badge[data-state="checking"] {
  color: var(--info-text);
  border-color: var(--info-border);
  background: var(--info-bg);
}

.status-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: currentColor;
  box-shadow: 0 0 8px currentColor;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(0.85); }
}

/* Theme toggle */
.theme-toggle {
  position: relative;
  width: 48px; height: 26px;
  border-radius: var(--radius-full);
  background: var(--line-medium);
  border: 1px solid var(--line-subtle);
  cursor: pointer;
  display: flex;
  align-items: center;
  padding: 3px;
  transition: all var(--duration-normal) var(--ease-smooth);
  outline: none;
}

.theme-toggle:focus-visible {
  box-shadow: 0 0 0 3px var(--accent-red-glow);
}

.theme-toggle:hover { border-color: var(--line-strong); }

.theme-toggle__thumb {
  width: 20px; height: 20px;
  border-radius: 50%;
  background: var(--bg-elevated);
  box-shadow: var(--shadow-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  transition: transform var(--duration-normal) var(--ease-spring);
}

[data-theme="dark"] .theme-toggle__thumb { transform: translateX(22px); }
[data-theme="light"] .theme-toggle__thumb { transform: translateX(0); }

/* ═══════════════════════════════════════════════════════════
   HERO SECTION
   ═══════════════════════════════════════════════════════════ */
.hero {
  position: relative;
  overflow: hidden;
  border-radius: var(--radius-2xl);
  padding: var(--space-12) var(--space-10);
  background: var(--gradient-hero);
  isolation: isolate;
  box-shadow: var(--shadow-xl), var(--shadow-inset);
  min-height: 320px;
  display: flex;
  align-items: center;
}

.hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
  background-size: 60px 60px;
  mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
  -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
  pointer-events: none;
  z-index: -1;
}

.hero-content {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--space-8);
  width: 100%;
  position: relative;
  z-index: 2;
}

.hero-text { flex: 1 1 55%; min-width: 300px; }

.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-4);
  border-radius: var(--radius-full);
  background: rgba(248,113,113,0.12);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(248,113,113,0.2);
  font-size: var(--text-xs);
  font-weight: 600;
  letter-spacing: var(--tracking-wide);
  text-transform: uppercase;
  color: #fecaca;
  margin-bottom: var(--space-5);
}

.hero-title {
  font-family: var(--font-display);
  font-weight: 800;
  font-size: clamp(1.75rem, 4vw, 2.75rem);
  line-height: var(--leading-tight);
  letter-spacing: var(--tracking-tight);
  margin-bottom: var(--space-4);
  text-wrap: balance;
}

.hero-title .highlight {
  background: linear-gradient(135deg, #fecaca, #f87171, #dc2626);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-subtitle {
  font-size: var(--text-base);
  line-height: var(--leading-relaxed);
  color: var(--ink-secondary);
  max-width: 540px;
  margin-bottom: var(--space-6);
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-4);
  background: var(--code-bg);
  backdrop-filter: blur(12px);
  border: 1px solid var(--line-medium);
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
  font-size: var(--text-sm);
  font-weight: 600;
  color: var(--accent-amber);
}

/* Scales animation */
.hero-visual {
  flex: 1 1 35%;
  min-width: 220px;
  display: flex;
  justify-content: center;
}

.scales {
  position: relative;
  width: 260px; height: 260px;
}

.scales__crown {
  position: absolute;
  top: 20px; left: 50%;
  transform: translateX(-50%);
  font-size: 2rem;
  filter: drop-shadow(0 0 12px rgba(251,191,36,0.5));
  animation: float 4s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateX(-50%) translateY(0); }
  50% { transform: translateX(-50%) translateY(-8px); }
}

.scales__pillar {
  position: absolute;
  bottom: 10px; left: 50%;
  transform: translateX(-50%);
  width: 6px; height: 130px;
  background: linear-gradient(180deg, var(--accent-red), var(--accent-red-dark));
  border-radius: 3px;
  box-shadow: 0 0 12px var(--accent-red-glow);
}

.scales__beam {
  position: absolute;
  top: 55px; left: 50%;
  width: 220px; height: 5px;
  background: linear-gradient(90deg, var(--accent-red-dark), var(--accent-red), var(--accent-red-dark));
  border-radius: 3px;
  transform-origin: center;
  transform: translateX(-50%);
  animation: beam-tilt 6s ease-in-out infinite;
  box-shadow: 0 0 10px var(--accent-red-glow);
}

@keyframes beam-tilt {
  0%, 100% { transform: translateX(-50%) rotate(-3deg); }
  50% { transform: translateX(-50%) rotate(3deg); }
}

.scales__pan {
  position: absolute;
  top: 115px;
  width: 70px; height: 44px;
  background: linear-gradient(135deg, rgba(220,38,38,0.2), rgba(185,28,28,0.1));
  border: 2px solid var(--accent-red);
  border-radius: 0 0 35px 35px;
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-display);
  font-weight: 700;
  font-size: var(--text-sm);
  color: var(--accent-red);
}

.scales__pan--left { left: -10px; animation: pan-bob-l 6s ease-in-out infinite; }
.scales__pan--right { right: -10px; animation: pan-bob-r 6s ease-in-out infinite; }

@keyframes pan-bob-l {
  0%, 100% { transform: translateY(12px); }
  50% { transform: translateY(-12px); }
}
@keyframes pan-bob-r {
  0%, 100% { transform: translateY(-12px); }
  50% { transform: translateY(12px); }
}

.scales__base {
  position: absolute;
  bottom: 0; left: 50%;
  transform: translateX(-50%);
  width: 100px; height: 7px;
  background: linear-gradient(180deg, var(--accent-red), var(--accent-red-dark));
  border-radius: 4px;
  box-shadow: 0 0 16px var(--accent-red-glow);
}

/* ═══════════════════════════════════════════════════════════
   ANALYSIS PANEL
   ═══════════════════════════════════════════════════════════ */
.panel {
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-xl);
  padding: var(--space-8);
  margin-top: var(--space-8);
  box-shadow: var(--shadow-lg);
  position: relative;
  overflow: hidden;
  transition: background var(--duration-normal), border-color var(--duration-normal);
}

.panel::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: var(--gradient-accent);
}

.panel__header {
  margin-bottom: var(--space-6);
}

.panel__title {
  font-family: var(--font-display);
  font-size: var(--text-xl);
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: var(--space-3);
  margin-bottom: var(--space-2);
}

.panel__title::before {
  content: '';
  width: 8px; height: 8px;
  background: var(--accent-red);
  border-radius: 50%;
  box-shadow: 0 0 10px var(--accent-red);
  animation: pulse 2s infinite;
}

.panel__subtitle {
  color: var(--ink-tertiary);
  font-size: var(--text-sm);
}

/* RAG source badge */
.rag-source {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-3);
  background: var(--surface-active);
  border: 1px solid rgba(220,38,38,0.12);
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
  font-size: var(--text-xs);
  font-weight: 600;
  color: var(--accent-red);
  margin-bottom: var(--space-5);
}

/* Alert */
.alert {
  padding: var(--space-4) var(--space-5);
  border-radius: var(--radius-md);
  display: flex;
  align-items: flex-start;
  gap: var(--space-3);
  font-size: var(--text-sm);
  line-height: var(--leading-normal);
  margin-bottom: var(--space-5);
}

.alert--warning {
  background: var(--warning-bg);
  border: 1px solid var(--warning-border);
  color: var(--warning-text);
}

.alert__icon { font-size: 1.2rem; flex-shrink: 0; margin-top: 1px; }

.alert code {
  background: var(--code-bg);
  padding: 0.1em 0.4em;
  border-radius: var(--radius-xs);
  font-family: var(--font-mono);
  font-size: 0.9em;
}

/* Scenario chips */
.scenarios {
  margin-bottom: var(--space-5);
}

.scenarios__label {
  font-size: var(--text-xs);
  font-weight: 600;
  color: var(--ink-tertiary);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
  margin-bottom: var(--space-3);
}

.scenarios__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
  gap: var(--space-2);
}

.scenario-chip {
  padding: var(--space-3) var(--space-4);
  background: var(--bg-sunken);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  font-weight: 500;
  color: var(--ink-secondary);
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-smooth);
  display: flex;
  align-items: center;
  gap: var(--space-2);
  user-select: none;
  outline: none;
  text-align: left;
  font-family: inherit;
}

.scenario-chip:hover {
  background: var(--surface-hover);
  border-color: rgba(220,38,38,0.25);
  color: var(--accent-red);
  transform: translateY(-2px);
  box-shadow: var(--shadow-sm);
}

.scenario-chip:focus-visible {
  box-shadow: 0 0 0 3px var(--accent-red-glow);
}

.scenario-chip:active { transform: translateY(0) scale(0.98); }

/* Textarea */
.input-area {
  position: relative;
}

.crime-input {
  width: 100%;
  min-height: 150px;
  padding: var(--space-4) var(--space-5);
  background: var(--bg-sunken);
  border: 1.5px solid var(--line-medium);
  border-radius: var(--radius-lg);
  color: var(--ink-primary);
  font-family: inherit;
  font-size: var(--text-base);
  line-height: var(--leading-relaxed);
  resize: vertical;
  transition: all var(--duration-fast) var(--ease-smooth);
  outline: none;
}

.crime-input:focus {
  border-color: var(--accent-red);
  box-shadow: 0 0 0 4px var(--accent-red-glow);
  background: var(--bg-elevated);
}

.crime-input::placeholder { color: var(--ink-quaternary); }

.input-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: var(--space-3);
  flex-wrap: wrap;
  gap: var(--space-3);
}

.char-count {
  font-family: var(--font-mono);
  font-size: var(--text-xs);
  color: var(--ink-quaternary);
  transition: color var(--duration-fast);
}

.char-count[data-level="warning"] { color: var(--accent-orange); }
.char-count[data-level="danger"] { color: var(--accent-red); }

/* Buttons */
.btn-group {
  display: flex;
  gap: var(--space-3);
  flex-wrap: wrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  padding: var(--space-3) var(--space-6);
  border: none;
  border-radius: var(--radius-md);
  font-family: inherit;
  font-weight: 700;
  font-size: var(--text-base);
  letter-spacing: var(--tracking-normal);
  cursor: pointer;
  transition: all var(--duration-normal) var(--ease-spring);
  position: relative;
  overflow: hidden;
  outline: none;
  text-decoration: none;
}

.btn:focus-visible {
  box-shadow: 0 0 0 4px var(--accent-red-glow);
}

.btn--primary {
  background: var(--gradient-accent);
  color: white;
  box-shadow: 0 8px 24px rgba(220,38,38,0.35), var(--shadow-inset);
}

.btn--primary::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, transparent, rgba(255,255,255,0.25), transparent);
  transform: translateX(-100%);
  transition: transform 0.6s var(--ease-smooth);
}

.btn--primary:hover::after { transform: translateX(100%); }

.btn--primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 36px rgba(220,38,38,0.45), var(--shadow-inset);
}

.btn--primary:active { transform: translateY(0) scale(0.97); }

.btn--secondary {
  background: linear-gradient(135deg, #a78bfa, #7c3aed);
  color: white;
  box-shadow: 0 8px 24px rgba(124,58,237,0.3), var(--shadow-inset);
}

.btn--secondary:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 36px rgba(124,58,237,0.4), var(--shadow-inset);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

.btn kbd {
  background: rgba(0,0,0,0.2);
  border: 1px solid rgba(255,255,255,0.15);
  border-radius: var(--radius-xs);
  padding: 0.1em 0.4em;
  font-family: var(--font-mono);
  font-size: 0.7em;
  opacity: 0.8;
}

.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ═══════════════════════════════════════════════════════════
   PROGRESS TRACKER
   ═══════════════════════════════════════════════════════════ */
.progress-tracker {
  display: none;
  margin-top: var(--space-6);
  padding: var(--space-6);
  background: var(--bg-sunken);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-lg);
  animation: fade-in 0.3s var(--ease-out-expo);
}

.progress-tracker[data-active="true"] { display: block; }

@keyframes fade-in {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

.progress-tracker__title {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  margin-bottom: var(--space-5);
  font-family: var(--font-display);
  font-size: var(--text-base);
  font-weight: 700;
}

.progress-tracker__title::before {
  content: '';
  width: 14px; height: 14px;
  border: 2.5px solid var(--accent-red);
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

.progress-steps {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.progress-step {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-3) var(--space-4);
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  color: var(--ink-quaternary);
  transition: all var(--duration-normal) var(--ease-smooth);
  opacity: 0.4;
}

.progress-step[data-state="active"] {
  opacity: 1;
  color: var(--ink-primary);
  border-color: rgba(248,113,113,0.3);
  background: var(--surface-active);
}

.progress-step[data-state="done"] {
  opacity: 1;
  color: var(--success-text);
  border-color: var(--success-border);
  background: var(--success-bg);
}

.progress-step[data-state="error"] {
  opacity: 1;
  color: var(--accent-orange);
  border-color: rgba(251,146,60,0.3);
  background: rgba(251,146,60,0.06);
}

.step-indicator {
  width: 30px; height: 30px;
  border-radius: 50%;
  background: var(--bg-sunken);
  border: 1.5px solid var(--line-medium);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: var(--text-sm);
  font-weight: 700;
  flex-shrink: 0;
  transition: all var(--duration-normal);
}

.progress-step[data-state="active"] .step-indicator {
  background: var(--gradient-accent);
  border-color: var(--accent-red);
  color: white;
  box-shadow: 0 0 12px var(--accent-red-glow);
}

.progress-step[data-state="done"] .step-indicator {
  background: var(--gradient-success);
  border-color: var(--accent-green);
  color: white;
}

.step-content { flex: 1; }
.step-label { font-weight: 600; }

.step-detail {
  font-family: var(--font-mono);
  font-size: var(--text-xs);
  color: var(--ink-quaternary);
  margin-top: 2px;
}

.progress-step[data-state="active"] .step-detail { color: var(--accent-red); }
.progress-step[data-state="done"] .step-detail { color: var(--success-text); }

/* ═══════════════════════════════════════════════════════════
   RESULTS
   ═══════════════════════════════════════════════════════════ */
.results {
  display: none;
  margin-top: var(--space-8);
}

.results[data-active="true"] {
  display: block;
  animation: results-enter 0.5s var(--ease-out-expo);
}

@keyframes results-enter {
  from { opacity: 0; transform: translateY(24px); }
  to { opacity: 1; transform: translateY(0); }
}

.result-card {
  background: var(--bg-sunken);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-lg);
  padding: var(--space-6);
  margin-bottom: var(--space-5);
  position: relative;
  overflow: hidden;
  transition: all var(--duration-normal);
}

.result-card--verdict {
  background: linear-gradient(135deg, rgba(220,38,38,0.06), rgba(185,28,28,0.03));
  border-color: rgba(248,113,113,0.25);
}

.result-card--verdict::before {
  content: '';
  position: absolute;
  left: 0; top: 0; bottom: 0;
  width: 4px;
  background: var(--gradient-accent);
}

.result-card__header {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  margin-bottom: var(--space-4);
}

.result-card__icon {
  width: 42px; height: 42px;
  border-radius: var(--radius-md);
  background: linear-gradient(135deg, rgba(248,113,113,0.15), rgba(220,38,38,0.08));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  flex-shrink: 0;
}

.result-card__label {
  font-size: var(--text-xs);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
  color: var(--accent-red);
  font-family: var(--font-mono);
}

.result-card__title {
  font-family: var(--font-display);
  font-weight: 700;
  font-size: var(--text-base);
}

/* Article display */
.article-badge {
  display: inline-flex;
  align-items: center;
  padding: var(--space-2) var(--space-4);
  background: rgba(248,113,113,0.12);
  border: 1px solid rgba(248,113,113,0.25);
  border-radius: var(--radius-md);
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: var(--text-lg);
  color: var(--accent-red);
  box-shadow: 0 0 12px rgba(248,113,113,0.15);
}

.article-text {
  color: var(--ink-tertiary);
  font-size: var(--text-sm);
  line-height: var(--leading-relaxed);
  margin-top: var(--space-4);
  padding: var(--space-4);
  background: var(--bg-elevated);
  border-radius: var(--radius-md);
  border-left: 3px solid var(--accent-red);
  white-space: pre-wrap;
}

/* Confidence meter */
.confidence {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  margin-top: var(--space-4);
  padding: var(--space-3) var(--space-4);
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
}

.confidence__bar {
  flex: 1;
  height: 6px;
  background: var(--line-subtle);
  border-radius: 3px;
  overflow: hidden;
}

.confidence__fill {
  height: 100%;
  border-radius: 3px;
  transition: width var(--duration-slow) var(--ease-out-expo);
}

.confidence__fill--high { background: var(--gradient-success); }
.confidence__fill--medium { background: linear-gradient(90deg, var(--accent-amber), var(--accent-orange)); }
.confidence__fill--low { background: var(--gradient-accent); }

.confidence__value {
  font-weight: 700;
  font-family: var(--font-mono);
  color: var(--accent-red);
  min-width: 36px;
  text-align: right;
}

/* Punishment grid */
.punishment-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
  gap: var(--space-3);
  margin-top: var(--space-4);
}

.punishment-card {
  padding: var(--space-4);
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  transition: all var(--duration-fast) var(--ease-smooth);
}

.punishment-card:hover {
  border-color: rgba(248,113,113,0.25);
  transform: translateY(-2px);
  box-shadow: var(--shadow-sm);
}

.punishment-card__type {
  font-size: var(--text-xs);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
  color: var(--ink-tertiary);
  margin-bottom: var(--space-1);
}

.punishment-card__value {
  font-family: var(--font-display);
  font-weight: 800;
  font-size: var(--text-lg);
  color: var(--accent-red);
}

.punishment-card__note {
  font-size: var(--text-xs);
  color: var(--ink-quaternary);
  margin-top: var(--space-1);
}

/* Action list */
.action-list {
  list-style: none;
  counter-reset: actions;
}

.action-list li {
  position: relative;
  padding: var(--space-4) 0 var(--space-4) 3.5rem;
  border-bottom: 1px solid var(--line-subtle);
  counter-increment: actions;
}

.action-list li:last-child { border-bottom: none; }

.action-list li::before {
  content: counter(actions);
  position: absolute;
  left: 0; top: var(--space-4);
  width: 32px; height: 32px;
  background: var(--gradient-accent);
  color: white;
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: var(--text-sm);
  box-shadow: var(--shadow-sm);
}

.action-list li strong {
  display: block;
  font-weight: 700;
  margin-bottom: var(--space-1);
}

.action-list li span {
  color: var(--ink-tertiary);
  font-size: var(--text-sm);
  line-height: var(--leading-normal);
}

/* Aggravating factors */
.aggravating-box {
  margin-top: var(--space-4);
  padding: var(--space-4);
  background: rgba(248,113,113,0.06);
  border-radius: var(--radius-md);
  border-left: 3px solid var(--accent-red);
  font-size: var(--text-sm);
  color: var(--ink-secondary);
  line-height: var(--leading-normal);
}

.aggravating-box strong { color: var(--accent-red); }

/* Copy button */
.copy-btn {
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-4);
  background: var(--bg-elevated);
  border: 1px solid var(--line-medium);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  font-weight: 600;
  font-family: inherit;
  color: var(--ink-secondary);
  cursor: pointer;
  transition: all var(--duration-fast);
  margin-left: auto;
}

.copy-btn:hover {
  border-color: var(--accent-red);
  color: var(--accent-red);
  background: var(--surface-hover);
}

.copy-btn[data-copied="true"] {
  border-color: var(--success-border);
  color: var(--success-text);
  background: var(--success-bg);
}

/* Raw response */
.raw-toggle {
  margin-top: var(--space-4);
  background: var(--bg-sunken);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  padding: var(--space-4);
}

.raw-toggle summary {
  cursor: pointer;
  font-weight: 600;
  color: var(--ink-secondary);
  font-size: var(--text-sm);
  list-style: none;
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.raw-toggle summary::-webkit-details-marker { display: none; }

.raw-toggle summary::before {
  content: '▶';
  font-size: 0.65rem;
  transition: transform var(--duration-fast);
}

.raw-toggle[open] summary::before { transform: rotate(90deg); }

.raw-toggle pre {
  margin-top: var(--space-4);
  font-family: var(--font-mono);
  font-size: var(--text-sm);
  line-height: var(--leading-relaxed);
  color: var(--ink-tertiary);
  white-space: pre-wrap;
  word-break: break-word;
  max-height: 400px;
  overflow-y: auto;
}

/* ═══════════════════════════════════════════════════════════
   KNOWLEDGE BASE
   ═══════════════════════════════════════════════════════════ */
.kb-section { margin-top: var(--space-12); }

.kb-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: var(--space-4);
  margin-top: var(--space-6);
}

.kb-card {
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-lg);
  padding: var(--space-6);
  cursor: pointer;
  transition: all var(--duration-normal) var(--ease-out-expo);
  position: relative;
  overflow: hidden;
  outline: none;
  text-align: left;
  font-family: inherit;
}

.kb-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  background: var(--gradient-accent);
  opacity: 0;
  transition: opacity var(--duration-normal);
}

.kb-card:hover {
  transform: translateY(-4px);
  border-color: rgba(248,113,113,0.2);
  box-shadow: var(--shadow-lg);
}

.kb-card:hover::before { opacity: 1; }

.kb-card:focus-visible {
  box-shadow: 0 0 0 3px var(--accent-red-glow), var(--shadow-lg);
}

.kb-card__number {
  font-family: var(--font-mono);
  font-size: var(--text-3xl);
  font-weight: 800;
  color: var(--accent-red);
  line-height: 1;
  margin-bottom: var(--space-2);
  text-shadow: 0 0 20px rgba(248,113,113,0.2);
}

.kb-card__name {
  font-family: var(--font-display);
  font-weight: 700;
  font-size: var(--text-base);
  margin-bottom: var(--space-2);
}

.kb-card__desc {
  color: var(--ink-tertiary);
  font-size: var(--text-sm);
  line-height: var(--leading-normal);
  margin-bottom: var(--space-3);
}

.kb-card__sentence {
  display: inline-flex;
  align-items: center;
  gap: var(--space-1);
  padding: var(--space-1) var(--space-3);
  background: rgba(248,113,113,0.08);
  border: 1px solid rgba(248,113,113,0.15);
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
  font-size: var(--text-xs);
  font-weight: 600;
  color: var(--accent-red);
}

/* ═══════════════════════════════════════════════════════════
   HISTORY
   ═══════════════════════════════════════════════════════════ */
.history {
  margin-top: var(--space-6);
  padding: var(--space-4);
  background: var(--bg-sunken);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-lg);
}

.history__title {
  font-size: var(--text-xs);
  font-weight: 600;
  color: var(--ink-tertiary);
  text-transform: uppercase;
  letter-spacing: var(--tracking-wide);
  margin-bottom: var(--space-3);
}

.history-item {
  padding: var(--space-3) var(--space-4);
  margin-bottom: var(--space-2);
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--duration-fast);
  font-size: var(--text-sm);
}

.history-item:hover {
  border-color: rgba(248,113,113,0.2);
  background: var(--surface-hover);
}

.history-item:last-child { margin-bottom: 0; }

.history-item__time {
  font-size: var(--text-xs);
  color: var(--ink-quaternary);
  font-family: var(--font-mono);
  margin-top: var(--space-1);
}

/* ═══════════════════════════════════════════════════════════
   FOOTER
   ═══════════════════════════════════════════════════════════ */
.footer {
  margin-top: var(--space-16);
  padding: var(--space-8);
  text-align: center;
  color: var(--ink-quaternary);
  font-size: var(--text-sm);
  border-top: 1px solid var(--line-subtle);
}

.footer a {
  color: var(--accent-red);
  text-decoration: none;
  font-weight: 600;
}

.footer a:hover { text-decoration: underline; }

/* ═══════════════════════════════════════════════════════════
   TOAST
   ═══════════════════════════════════════════════════════════ */
.toast-container {
  position: fixed;
  bottom: var(--space-8);
  right: var(--space-8);
  z-index: var(--z-toast);
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
}

.toast {
  padding: var(--space-4) var(--space-5);
  background: var(--bg-elevated);
  border: 1px solid var(--line-medium);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-lg);
  display: flex;
  align-items: center;
  gap: var(--space-3);
  font-size: var(--text-sm);
  font-weight: 500;
  animation: toast-in 0.35s var(--ease-out-expo);
  max-width: 380px;
}

.toast--success { border-color: var(--success-border); }
.toast--error { border-color: var(--warning-border); }

@keyframes toast-in {
  from { transform: translateY(20px) scale(0.95); opacity: 0; }
  to { transform: translateY(0) scale(1); opacity: 1; }
}

.toast-exit {
  animation: toast-out 0.25s var(--ease-smooth) forwards;
}

@keyframes toast-out {
  to { transform: translateY(10px); opacity: 0; }
}

/* ═══════════════════════════════════════════════════════════
   SKELETON LOADING
   ═══════════════════════════════════════════════════════════ */
.skeleton {
  background: var(--skeleton-base);
  border-radius: var(--radius-sm);
  position: relative;
  overflow: hidden;
}

.skeleton::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, var(--skeleton-shine), transparent);
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  from { transform: translateX(-100%); }
  to { transform: translateX(100%); }
}

/* ═══════════════════════════════════════════════════════════
   SCROLLBAR
   ═══════════════════════════════════════════════════════════ */
::-webkit-scrollbar { width: 8px; height: 8px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb {
  background: var(--scrollbar-thumb);
  border-radius: var(--radius-full);
}
::-webkit-scrollbar-thumb:hover { background: var(--scrollbar-thumb-hover); }

/* ═══════════════════════════════════════════════════════════
   ANIMATIONS
   ═══════════════════════════════════════════════════════════ */
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  20% { transform: translateX(-5px); }
  40% { transform: translateX(5px); }
  60% { transform: translateX(-3px); }
  80% { transform: translateX(3px); }
}

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 768px) {
  .hero { padding: var(--space-8) var(--space-6); }
  .hero-visual { display: none; }
  .panel { padding: var(--space-6); }
  .scenarios__grid { grid-template-columns: 1fr 1fr; }
  .punishment-grid { grid-template-columns: 1fr; }
  .kb-grid { grid-template-columns: 1fr; }
  .navbar { padding: var(--space-3) var(--space-4); }
  .nav-actions { width: 100%; justify-content: space-between; }
  .btn-group { width: 100%; }
  .btn { flex: 1; }
  .toast-container { left: var(--space-4); right: var(--space-4); bottom: var(--space-4); }
  .toast { max-width: 100%; }
}

@media (max-width: 480px) {
  .scenarios__grid { grid-template-columns: 1fr; }
  .input-footer { flex-direction: column; align-items: stretch; }
  .btn-group { flex-direction: column; }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
  html { scroll-behavior: auto; }
}

/* Print */
@media print {
  .ambient-bg, .navbar, .hero, .scenarios, .btn-group, .progress-tracker { display: none !important; }
  .panel { box-shadow: none; border: 1px solid #ccc; }
}
</style>
</head>
<body>

<div class="ambient-bg" aria-hidden="true">
  <div class="ambient-blob blob-1"></div>
  <div class="ambient-blob blob-2"></div>
  <div class="ambient-blob blob-3"></div>
</div>

<div class="app-shell">
  <div class="container">

    <!-- NAVBAR -->
    <nav class="navbar" role="navigation" aria-label="Основная навигация">
      <a href="/" class="brand" aria-label="LegalAI Pro">⚖️ LegalAI Pro</a>
      <div class="nav-actions">
        <a href="/" class="nav-link">← Главная</a>
        <span class="nav-link" aria-current="page">УК РТ</span>
        <button id="statusBadge" class="status-badge" data-state="checking" aria-live="polite" title="Статус RAG-сервера">
          <span class="status-dot" aria-hidden="true"></span>
          <span id="statusText">Проверка...</span>
        </button>
        <button id="themeToggle" class="theme-toggle" aria-label="Переключить тему" type="button">
          <span class="theme-toggle__thumb" id="themeThumb" aria-hidden="true">🌙</span>
        </button>
      </div>
    </nav>

    <!-- HERO -->
    <section class="hero" aria-label="Описание сервиса">
      <div class="hero-content">
        <div class="hero-text">
          <div class="hero-eyebrow">Уголовный кодекс Республики Таджикистан</div>
          <h1 class="hero-title">
            Квалификация преступления<br>
            <span class="highlight">с RAG-анализом УК РТ</span>
          </h1>
          <p class="hero-subtitle">
            Опишите ситуацию — система найдёт нормы в тексте УК РТ, а
            <strong style="color:var(--accent-amber)">локальная модель</strong> разберёт состав
            только по найденному. Если подходящей нормы в кодексе нет, ответа не будет.
          </p>
          <div class="hero-badge">🔒 Только текст УК РТ • RAG • Локально</div>
        </div>
        <div class="hero-visual" aria-hidden="true">
          <div class="scales">
            <div class="scales__crown">⚖</div>
            <div class="scales__beam"></div>
            <div class="scales__pan scales__pan--left">Вина</div>
            <div class="scales__pan scales__pan--right">Закон</div>
            <div class="scales__pillar"></div>
            <div class="scales__base"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ANALYSIS PANEL -->
    <section class="panel" aria-labelledby="panel-title">
      <div class="rag-source">🔒 Источник ответа — только Уголовный кодекс Республики Таджикистан из RAG-индекса</div>
      
      <div class="panel__header">
        <h2 class="panel__title" id="panel-title">Опишите преступление</h2>
        <p class="panel__subtitle">Чем подробнее описание — тем точнее квалификация по статьям УК РТ</p>
      </div>

      <div id="apiWarning" class="alert alert--warning" style="display:none" role="alert">
        <span class="alert__icon" aria-hidden="true">⚠️</span>
        <div>
          <strong>Индекс УК РТ недоступен.</strong> Запустите <code>python app.py</code> и убедитесь, что собран индекс кодексов.
          <br><small>Страница отвечает только по тексту УК РТ, поэтому без индекса анализ не выполняется.</small>
        </div>
      </div>

      <!-- Scenarios -->
      <div class="scenarios">
        <div class="scenarios__label">⚡ Быстрые сценарии</div>
        <div class="scenarios__grid" id="scenarioGrid" role="list"></div>
      </div>

      <!-- Input -->
      <div class="input-area">
        <label for="crimeInput" class="sr-only" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);">Описание преступления</label>
        <textarea
          id="crimeInput"
          class="crime-input"
          placeholder="Например: Вчера вечером на улице Рудаки неизвестный мужчина вырвал у меня сумку из рук и убежал. В сумке был телефон, документы и 2000 сомони..."
          maxlength="2000"
          aria-describedby="charCount"
        ></textarea>
      </div>

      <div class="input-footer">
        <span class="char-count" id="charCount" aria-live="polite">0 / 2000</span>
        <div class="btn-group">
          <button id="btnAnalyze" class="btn btn--primary" type="button">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Найти норму</span>
            <kbd>Ctrl+↵</kbd>
          </button>
          <button id="btnQualify" class="btn btn--secondary" type="button">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Полная квалификация</span>
          </button>
        </div>
      </div>

      <!-- Progress -->
      <div id="progressTracker" class="progress-tracker" aria-live="polite">
        <div class="progress-tracker__title">Анализ преступления...</div>
        <div class="progress-steps">
          <div class="progress-step" data-step="retrieval">
            <div class="step-indicator">1</div>
            <div class="step-content">
              <div class="step-label">Поиск норм в тексте УК РТ</div>
              <div class="step-detail" data-detail>Ожидание...</div>
            </div>
          </div>
          <div class="progress-step" data-step="llm">
            <div class="step-indicator">2</div>
            <div class="step-content">
              <div class="step-label">Разбор моделью (только для полной квалификации)</div>
              <div class="step-detail" data-detail>Ожидание...</div>
            </div>
          </div>
          <div class="progress-step" data-step="validation">
            <div class="step-indicator">3</div>
            <div class="step-content">
              <div class="step-label">Сверка ссылок с выдачей поиска</div>
              <div class="step-detail" data-detail>Ожидание...</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Results -->
      <div id="results" class="results" aria-live="polite"></div>

      <!-- History -->
      <div id="historyPanel" class="history" style="display:none">
        <div class="history__title">📜 История анализов</div>
        <div id="historyList"></div>
      </div>
    </section>

    <!-- KNOWLEDGE BASE -->
    <section class="kb-section" aria-labelledby="kb-title">
      <h2 class="panel__title" id="kb-title">📚 База знаний УК РТ</h2>
      <p class="panel__subtitle">Статьи загружаются из RAG-индекса кодекса. Кликните — номер подставится в форму.</p>
      <div class="input-footer" style="margin-bottom:var(--space-4)">
        <label for="kbFilter" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);">Поиск по статьям УК РТ</label>
        <input id="kbFilter" class="crime-input" type="search" autocomplete="off"
               placeholder="Поиск по номеру, названию статьи или главе — например: кража, 248, против собственности"
               style="min-height:auto;height:44px;resize:none;flex:1 1 320px">
        <span class="char-count" id="kbCount" aria-live="polite"></span>
      </div>
      <div class="kb-grid" id="kbGrid" role="list"></div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
      <p>⚖️ LegalAI Pro • Ответ только по тексту УК РТ • локальная модель через <a href="https://ollama.com" target="_blank" rel="noopener">Ollama</a></p>
      <p style="margin-top:var(--space-2)">Информация носит ознакомительный характер. Для точной консультации обратитесь к адвокату.</p>
    </footer>

  </div>
</div>

<div class="toast-container" id="toastContainer" aria-live="assertive"></div>

<script>
'use strict';

/* ═══════════════════════════════════════════════════════════
   CONFIGURATION
   ═══════════════════════════════════════════════════════════ */
const CONFIG = Object.freeze({
  API_BASE: 'http://127.0.0.1:5000',
  MAX_INPUT_LENGTH: 2000,
  RAG_TOP_K: 8,
  RETRY_MAX: 3,
  RETRY_BASE_DELAY: 800,
  TIMEOUT_SHORT: 5000,
  TIMEOUT_MEDIUM: 15000,
  // Разбор приходит потоком, поэтому сторожим не весь ответ, а только
  // молчание до первого байта: прежний таймаут на весь запрос обрывал
  // генерацию посреди ответа с «signal is aborted without reason».
  TIMEOUT_FIRST_BYTE: 90000,
  DEBOUNCE_MS: 300,
  HISTORY_MAX: 8,
  // Сколько статей кодекса показывать в базе знаний за раз.
  KB_VISIBLE: 24,
  // Сколько найденных норм разворачивать полным текстом.
  SEARCH_VISIBLE: 5,
});

/* ═══════════════════════════════════════════════════════════
   SCENARIOS
   ═══════════════════════════════════════════════════════════ */
const SCENARIOS = Object.freeze({
  theft: { icon: '🕵️', label: 'Карманная кража', text: 'Сегодня в метро в час пик кто-то вытащил у меня из кармана куртки телефон iPhone 15. Я заметил пропажу только когда вышел из вагона.' },
  robbery: { icon: '🏃', label: 'Уличный грабёж', text: 'Вчера вечером на улице Рудаки в Душанбе неизвестный мужчина подбежал ко мне, вырвал сумку из рук и убежал. В сумке были документы, телефон и 3000 сомони.' },
  assault: { icon: '🔪', label: 'Нападение с ножом', text: 'На меня напали двое неизвестных в тёмном переулке. Один держал нож и угрожал убить, второй забрал кошелёк и часы. Я получил ножевое ранение в руку.' },
  extortion: { icon: '😰', label: 'Вымогательство', text: 'Ко мне подошли трое мужчин спортивного телосложения и потребовали 10000 долларов, угрожая расправой над моей семьёй, если я не принесу деньги завтра.' },
  fraud: { icon: '🎭', label: 'Мошенничество', text: 'Мне позвонили из "банка" и сказали, что мой счёт заблокирован. Под диктовку мошенника я перевёл все свои сбережения (50000 сомони) на "безопасный счёт".' },
  battery: { icon: '👊', label: 'Побои', text: 'В баре возник конфликт. Мужчина ударил меня кулаком по лицу три раза. Свидетели есть, зафиксированы синяки и ссадины, есть справка из травмпункта.' },
});

/* ═══════════════════════════════════════════════════════════
   UTILITY FUNCTIONS
   ═══════════════════════════════════════════════════════════ */
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

/* ═══════════════════════════════════════════════════════════
   RAG ENGINE — единственный источник ответов страницы

   Локального словаря статей здесь больше нет намеренно. Он подсказывал
   правдоподобные, но чужие номера: в нём «247 — Кража» и «246 —
   Мошенничество», тогда как в УК РТ 244 — кража, 247 — мошенничество,
   а 246 — хищение кредитных средств. Всё, что показывает страница,
   приходит из индекса Уголовного кодекса РТ.
   ═══════════════════════════════════════════════════════════ */
class CrimeRAG {
  constructor() {
    this.ready = false;
    this.status = null;
    this.catalog = [];
  }

  /** Готовность именно уголовного индекса, а не RAG вообще. */
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

  /** Перечень статей УК РТ для базы знаний — из индекса, не из шаблона. */
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

  /** Поиск норм по описанию события. Без языковой модели. */
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

/* ═══════════════════════════════════════════════════════════
   LLM SERVICE — модель работает в границах найденных норм

   Промпт собирается на сервере: там же лежит выдача поиска, которой ответ
   обязан ограничиться. Клиент отправляет только описание события.
   ═══════════════════════════════════════════════════════════ */
class CrimeService {
  /**
   * Полный разбор потоком NDJSON: опора ответа приходит первой строкой,
   * дальше текст по мере генерации, последней — результат сверки ссылок.
   *
   * Ответа целиком тут ждать нельзя: на этом железе разбор идёт минуты, и
   * обычный fetch обрывался по таймауту («signal is aborted without reason»),
   * так и не показав ни строчки. Таймаут сторожит только первый байт.
   */
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
        body: JSON.stringify({ description }),
        signal: controller.signal,
      });
    } catch (err) {
      clearTimeout(guard);
      throw new Error(firstByte ? err.message : 'Сервер не ответил — проверьте, запущены ли python app.py и Ollama');
    }

    // Отказ «нормы не найдено» и ошибки индекса приходят обычным JSON, а не
    // потоком: их надо вернуть как есть, иначе разбор NDJSON не найдёт в них
    // ни опоры, ни токенов и ответ выродится в «сервер не прислал опору».
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
        // Признак жизни: модель ещё молчит. Соединение держится, и до
        // первого токена видно, что запрос не завис.
        onWait?.(obj.wait);
        return;
      }
      if (obj.response) {
        collected.push(obj.response);
        onChunk?.(obj.response);
      }
      if (obj.done) tail = obj;
      // Строка без опоры, токена, ожидания и признака конца — это обычный
      // JSON-ответ, отданный без потока (отказ или ошибка).
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

    // Поток без первой строки с опорой — это отказ или ошибка, отданные JSON.
    if (!meta) {
      if (plainPayload) return plainPayload;
      throw new Error('Сервер не прислал опору ответа');
    }

    const data = {
      ...meta,
      qualification: collected.join('') + (tail.note || ''),
      unverified_articles: tail.unverified_articles || [],
    };
    onDone?.(data);
    return data;
  }
}

/* ═══════════════════════════════════════════════════════════
   UI RENDERER
   ═══════════════════════════════════════════════════════════ */
class UIRenderer {
  static renderScenarios() {
    const grid = document.getElementById('scenarioGrid');
    grid.innerHTML = Object.entries(SCENARIOS).map(([key, s]) => `
      <button class="scenario-chip" data-scenario="${key}" type="button" role="listitem">
        <span aria-hidden="true">${s.icon}</span> ${Utils.escapeHtml(s.label)}
      </button>
    `).join('');
  }

  /**
   * База знаний — статьи из индекса УК РТ. Кодекс целиком в сетку не влезает,
   * поэтому показывается срез по фильтру: без него 405 карточек хоронили бы
   * панель анализа под собой.
   */
  static renderKnowledgeBase(articles, filter = '') {
    const grid = document.getElementById('kbGrid');
    const counter = document.getElementById('kbCount');
    const query = filter.trim().toLowerCase();

    if (!Array.isArray(articles) || articles.length === 0) {
      grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:var(--ink-tertiary);padding:2rem;">'
        + 'Индекс УК РТ недоступен — база знаний не загружена.</p>';
      if (counter) counter.textContent = '';
      return;
    }

    const matched = query
      ? articles.filter(a =>
          String(a.article).includes(query)
          || String(a.article_title || '').toLowerCase().includes(query)
          || String(a.chapter_title || '').toLowerCase().includes(query))
      : articles;

    const shown = matched.slice(0, CONFIG.KB_VISIBLE);
    if (counter) {
      counter.textContent = matched.length > shown.length
        ? `показано ${shown.length} из ${matched.length}`
        : `${matched.length} ст.`;
    }

    if (shown.length === 0) {
      grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:var(--ink-tertiary);padding:2rem;">'
        + 'По запросу в УК РТ ничего не найдено.</p>';
      return;
    }

    grid.innerHTML = shown.map(a => `
      <button class="kb-card" data-article="${Utils.escapeHtml(a.article)}" type="button" role="listitem">
        <div class="kb-card__number">${Utils.escapeHtml(a.article)}</div>
        <div class="kb-card__name">${Utils.escapeHtml(a.article_title || '')}</div>
        <div class="kb-card__desc">${Utils.escapeHtml(a.snippet || '')}</div>
        <div class="kb-card__sentence">📖 ${Utils.escapeHtml(a.chapter_title || ('Глава ' + (a.chapter || '—')))}</div>
      </button>
    `).join('');
  }

  /** Карточка с нормой: заголовок, ссылка на кодекс и дословный текст статьи. */
  static _articleCard(article, { label, icon, verdict = false, withCopy = false } = {}) {
    return `
      <div class="result-card ${verdict ? 'result-card--verdict' : ''}">
        <div class="result-card__header">
          <div class="result-card__icon" aria-hidden="true">${icon || '📕'}</div>
          <div style="flex:1">
            <div class="result-card__label">${Utils.escapeHtml(label || 'Норма УК РТ')}</div>
            <div class="result-card__title">${Utils.escapeHtml(article.article_title || '')}</div>
          </div>
          ${withCopy ? `
          <button class="copy-btn" data-action="copy" type="button">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Копировать
          </button>` : ''}
        </div>
        <div style="display:flex;align-items:center;gap:var(--space-3);flex-wrap:wrap;margin-bottom:var(--space-4)">
          <span class="article-badge">Ст. ${Utils.escapeHtml(article.article)} УК РТ</span>
          <span style="color:var(--ink-tertiary);font-size:var(--text-sm)">${Utils.escapeHtml(article.citation || '')}</span>
        </div>
        <div class="article-text" style="white-space:pre-wrap">${Utils.escapeHtml(article.text || article.snippet || '')}</div>
      </div>
    `;
  }

  /** Список прочих норм, поднятых поиском: пользователь видит всю опору ответа. */
  static _sourcesCard(articles, selectedNumber) {
    const rest = (articles || []).filter(a => String(a.article) !== String(selectedNumber));
    if (rest.length === 0) return '';
    return `
      <div class="result-card">
        <div class="result-card__header">
          <div class="result-card__icon" aria-hidden="true">🔎</div>
          <div>
            <div class="result-card__label">ИСТОЧНИКИ RAG</div>
            <div class="result-card__title">Остальные нормы, найденные по описанию</div>
          </div>
        </div>
        <ol class="action-list">
          ${rest.map(a => `
            <li>
              <strong>Ст. ${Utils.escapeHtml(a.article)} — ${Utils.escapeHtml(a.article_title || '')}</strong>
              <span>${Utils.escapeHtml(a.snippet || '')}</span>
            </li>
          `).join('')}
        </ol>
      </div>
    `;
  }

  /**
   * Результат поиска по УК РТ: сами нормы, дословно и со ссылкой.
   * Ни одной строки здесь не пишет модель — это выдача индекса.
   */
  static renderSearch(data) {
    const articles = data.articles || [];
    const container = document.getElementById('results');
    const top = articles[0] || {};
    const rest = articles.slice(1, CONFIG.SEARCH_VISIBLE);

    container.innerHTML = `
      <div class="result-card">
        <div class="result-card__header">
          <div class="result-card__icon" aria-hidden="true">🔒</div>
          <div style="flex:1">
            <div class="result-card__label">ИСТОЧНИК ОТВЕТА</div>
            <div class="result-card__title">Уголовный кодекс Республики Таджикистан</div>
          </div>
          <button class="copy-btn" data-action="copy" type="button">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Копировать
          </button>
        </div>
        <div style="display:flex;gap:var(--space-2);flex-wrap:wrap;margin-bottom:var(--space-3)">
          ${articles.map(a => `<span class="article-badge">Ст. ${Utils.escapeHtml(a.article)} — ${Utils.escapeHtml(a.article_title || '')}</span>`).join('')}
        </div>
        <div style="color:var(--ink-tertiary);font-size:var(--text-sm);line-height:var(--leading-relaxed)">
          Нормы найдены поиском по тексту кодекса и приведены дословно, без участия
          языковой модели. Порядок — по совпадению с описанием. Для разбора состава
          и санкций нажмите «Полная квалификация».
        </div>
      </div>

      ${UIRenderer._articleCard(top, {
        label: 'Наиболее близкая норма',
        icon: '⚖️',
        verdict: true,
      })}

      ${rest.map(a => UIRenderer._articleCard(a, { label: 'Также найдено', icon: '📕' })).join('')}
    `;

    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  /** Ответ, когда в УК РТ опоры нет. Ничего «по общим знаниям» тут не будет. */
  static renderNotFound(message, query = '') {
    const container = document.getElementById('results');
    container.innerHTML = `
      <div class="result-card result-card--verdict" style="border-color:var(--warning-border);background:var(--warning-bg)">
        <div class="result-card__header">
          <div class="result-card__icon" style="background:var(--warning-bg)" aria-hidden="true">🔍</div>
          <div>
            <div class="result-card__label" style="color:var(--warning-text)">НОРМА НЕ НАЙДЕНА</div>
            <div class="result-card__title">В УК РТ нет статьи под это описание</div>
          </div>
        </div>
        <div class="article-text" style="border-left-color:var(--accent-amber)">${Utils.escapeHtml(message)}</div>
        ${query ? `<div style="margin-top:var(--space-3);color:var(--ink-tertiary);font-size:var(--text-sm)">Запрос: ${Utils.escapeHtml(query)}</div>` : ''}
      </div>
    `;
    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  static renderError(message, details = '') {
    const container = document.getElementById('results');
    container.innerHTML = `
      <div class="result-card result-card--verdict" style="border-color:var(--warning-border);background:var(--warning-bg)">
        <div class="result-card__header">
          <div class="result-card__icon" style="background:var(--warning-bg)" aria-hidden="true">⚠️</div>
          <div>
            <div class="result-card__label" style="color:var(--warning-text)">Ошибка анализа</div>
            <div class="result-card__title">${Utils.escapeHtml(message)}</div>
          </div>
        </div>
        ${details ? `<div class="article-text" style="border-left-color:var(--accent-amber)">${Utils.escapeHtml(details)}</div>` : ''}
        <div style="margin-top:var(--space-4);color:var(--ink-tertiary);font-size:var(--text-sm);line-height:var(--leading-relaxed)">
          <strong>Что проверить:</strong>
          <ul style="margin-top:var(--space-2);padding-left:1.25rem">
            <li>Запущен ли Python RAG API (<code>python app.py</code>) и Ollama</li>
            <li>Собран ли индекс УК РТ — статус виден в шапке страницы</li>
            <li>Описано ли событие достаточно подробно</li>
          </ul>
        </div>
      </div>
    `;
    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  /**
   * Живой вывод разбора: опора ответа рисуется сразу, текст дописывается
   * по мере генерации. Пустой спиннер на несколько минут — это то, ради
   * чего страница и переведена на поток.
   */
  static renderQualificationStart(meta) {
    const container = document.getElementById('results');
    const articles = meta.articles || [];
    container.innerHTML = `
      <div class="result-card">
        <div class="result-card__header">
          <div class="result-card__icon" aria-hidden="true">🔒</div>
          <div style="flex:1">
            <div class="result-card__label">ОПОРА ОТВЕТА</div>
            <div class="result-card__title">${Utils.escapeHtml(meta.primary_source || 'Уголовный кодекс Республики Таджикистан')}</div>
          </div>
        </div>
        <div style="display:flex;gap:var(--space-2);flex-wrap:wrap">
          ${articles.map(a => `<span class="article-badge">Ст. ${Utils.escapeHtml(a.article)} — ${Utils.escapeHtml(a.article_title || '')}</span>`).join('')}
        </div>
      </div>

      <div class="result-card result-card--verdict">
        <div class="result-card__header">
          <div class="result-card__icon" aria-hidden="true">✍️</div>
          <div>
            <div class="result-card__label">РАЗБОР</div>
            <div class="result-card__title">Модель пишет ответ по найденным нормам</div>
          </div>
        </div>
        <div class="article-text" id="qualifyStream" style="white-space:pre-wrap"></div>
      </div>
    `;
    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  static appendQualificationChunk(chunk) {
    const target = document.getElementById('qualifyStream');
    if (target) target.textContent += chunk;
  }  static renderQualification(data, description) {
    const text = data.qualification || '';
    const sections = UIRenderer._parseSections(text);
    const container = document.getElementById('results');
    const articles = data.articles || [];
    const unverified = data.unverified_articles || [];

    const sectionCards = [
      { key: 'квалификация', icon: '⚖️', label: 'КВАЛИФИКАЦИЯ', title: 'Определение состава преступления' },
      { key: 'санкции', icon: '⏱️', label: 'САНКЦИИ', title: 'Наказание по тексту статьи УК РТ' },
      { key: 'факты', icon: '🔍', label: 'ФАКТЫ', title: 'Доказательственная база' },
      { key: 'обстоятельства', icon: '⚠️', label: 'ОБСТОЯТЕЛЬСТВА', title: 'Смягчающие и отягчающие' },
      { key: 'анализ', icon: '🧠', label: 'ПРАВОВОЙ АНАЛИЗ', title: 'Юридическое обоснование' },
      { key: 'вывод', icon: '✅', label: 'ВЫВОД', title: 'Итоговое заключение' },
    ];

    container.innerHTML = `
      <div class="result-card">
        <div class="result-card__header">
          <div class="result-card__icon" aria-hidden="true">🔒</div>
          <div style="flex:1">
            <div class="result-card__label">ОПОРА ОТВЕТА</div>
            <div class="result-card__title">${Utils.escapeHtml(data.primary_source || 'Уголовный кодекс Республики Таджикистан')}</div>
          </div>
          <button class="copy-btn" data-action="copy" type="button">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Копировать
          </button>
        </div>
        <div style="display:flex;gap:var(--space-2);flex-wrap:wrap">
          ${articles.map(a => `<span class="article-badge">Ст. ${Utils.escapeHtml(a.article)} — ${Utils.escapeHtml(a.article_title || '')}</span>`).join('')}
        </div>
        ${unverified.length > 0 ? `
          <div class="aggravating-box">
            <strong>⚠️ Не подтверждено RAG:</strong> статьи ${Utils.escapeHtml(unverified.join(', '))}
            в найденных фрагментах УК РТ отсутствуют — проверьте их по тексту кодекса.
          </div>` : ''}
      </div>

      ${sectionCards.filter(s => sections[s.key]).map(s => `
        <div class="result-card ${s.key === 'квалификация' || s.key === 'вывод' ? 'result-card--verdict' : ''}">
          <div class="result-card__header">
            <div class="result-card__icon" aria-hidden="true">${s.icon}</div>
            <div>
              <div class="result-card__label">${s.label}</div>
              <div class="result-card__title">${s.title}</div>
            </div>
          </div>
          <div class="article-text">${Utils.escapeHtml(sections[s.key])}</div>
        </div>
      `).join('') || `
        <div class="result-card result-card--verdict">
          <div class="result-card__header">
            <div class="result-card__icon" aria-hidden="true">⚖️</div>
            <div>
              <div class="result-card__label">КВАЛИФИКАЦИЯ</div>
              <div class="result-card__title">Результат анализа</div>
            </div>
          </div>
          <div class="article-text">${Utils.escapeHtml(text)}</div>
        </div>
      `}

      ${articles.map(a => UIRenderer._articleCard(a, { label: 'Текст нормы из индекса', icon: '📕' })).join('')}

      <details class="raw-toggle">
        <summary>🤖 Полный ответ модели</summary>
        <pre>${Utils.escapeHtml(text)}</pre>
      </details>
    `;

    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  static _parseSections(text) {
    const sections = {};
    const patterns = [
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

/* ═══════════════════════════════════════════════════════════
   PROGRESS TRACKER
   ═══════════════════════════════════════════════════════════ */
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
      if (detail) detail.textContent = 'Ожидание...';
    }
  }

  update(name, state, detail = '') {
    const step = this.steps.get(name);
    if (!step) return;
    step.setAttribute('data-state', state);
    const detailEl = step.querySelector('[data-detail]');
    if (detailEl && detail) detailEl.textContent = detail;
  }
}

/* ═══════════════════════════════════════════════════════════
   HISTORY MANAGER
   ═══════════════════════════════════════════════════════════ */
class HistoryManager {
  constructor() {
    this.key = 'legalai_history_v2';
  }

  get() {
    try { return JSON.parse(localStorage.getItem(this.key)) || []; } catch { return []; }
  }

  add(query, article) {
    const history = this.get();
    history.unshift({
      query: query.slice(0, 120),
      articleNum: article.number,
      articleName: article.name,
      ts: Date.now(),
    });
    if (history.length > CONFIG.HISTORY_MAX) history.length = CONFIG.HISTORY_MAX;
    localStorage.setItem(this.key, JSON.stringify(history));
  }

  render() {
    const panel = document.getElementById('historyPanel');
    const list = document.getElementById('historyList');
    const history = this.get();

    if (history.length === 0) {
      panel.style.display = 'none';
      return;
    }

    panel.style.display = 'block';
    list.innerHTML = history.map((item, i) => `
      <div class="history-item" data-history-index="${i}" role="button" tabindex="0">
        <div style="font-weight:600;margin-bottom:2px">Ст. ${Utils.escapeHtml(item.articleNum)} — ${Utils.escapeHtml(item.articleName)}</div>
        <div style="color:var(--ink-tertiary);font-size:var(--text-sm)">${Utils.escapeHtml(item.query)}...</div>
        <div class="history-item__time">${new Date(item.ts).toLocaleString('ru-RU')}</div>
      </div>
    `).join('');
  }
}

/* ═══════════════════════════════════════════════════════════
   THEME MANAGER
   ═══════════════════════════════════════════════════════════ */
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
    this.thumb.textContent = theme === 'dark' ? '🌙' : '☀️';
    this.btn.setAttribute('aria-pressed', String(theme === 'dark'));
    if (save) localStorage.setItem('legalai-theme', theme);
  }

  toggle() {
    const current = this.root.getAttribute('data-theme');
    this.set(current === 'dark' ? 'light' : 'dark');
  }
}

/* ═══════════════════════════════════════════════════════════
   MAIN APPLICATION CONTROLLER
   ═══════════════════════════════════════════════════════════ */
class LegalAIApp {
  constructor() {
    this.rag = new CrimeRAG();
    this.llm = new CrimeService();
    this.theme = new ThemeManager();
    this.history = new HistoryManager();
    this.progress = new ProgressTracker();
    this.isProcessing = false;

    // DOM refs
    this.input = document.getElementById('crimeInput');
    this.charCount = document.getElementById('charCount');
    this.btnAnalyze = document.getElementById('btnAnalyze');
    this.btnQualify = document.getElementById('btnQualify');
    this.statusBadge = document.getElementById('statusBadge');
    this.statusText = document.getElementById('statusText');
    this.apiWarning = document.getElementById('apiWarning');
    this.resultsEl = document.getElementById('results');
    this.kbFilter = document.getElementById('kbFilter');

    this._bindEvents();
    this._init();
  }

  _bindEvents() {
    // Input counter with debounce
    this.input.addEventListener('input', Utils.debounce(() => this._updateCounter(), 100));

    // Ctrl+Enter to analyze
    this.input.addEventListener('keydown', e => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        this.analyze();
      }
    });

    // Buttons
    this.btnAnalyze.addEventListener('click', () => this.analyze());
    this.btnQualify.addEventListener('click', () => this.qualify());

    // Status badge click
    this.statusBadge.addEventListener('click', () => this._checkAPI());

    // Event delegation for scenarios
    document.getElementById('scenarioGrid').addEventListener('click', e => {
      const chip = e.target.closest('[data-scenario]');
      if (chip) {
        const scenario = SCENARIOS[chip.dataset.scenario];
        if (scenario) {
          this.input.value = scenario.text;
          this._updateCounter();
          this.input.focus();
        }
      }
    });

    // Фильтр базы знаний: кодекс целиком в сетку не помещается.
    this.kbFilter?.addEventListener('input', Utils.debounce(
      () => UIRenderer.renderKnowledgeBase(this.rag.catalog, this.kbFilter.value),
      CONFIG.DEBOUNCE_MS
    ));

    // Event delegation for KB cards
    document.getElementById('kbGrid').addEventListener('click', e => {
      const card = e.target.closest('[data-article]');
      if (!card) return;
      const article = this.rag.catalog.find(a => String(a.article) === card.dataset.article);
      if (!article) return;
      this.input.value = `Событие по признакам статьи ${article.article} УК РТ (${article.article_title}): `;
      this._updateCounter();
      this.input.focus();
      window.scrollTo({ top: 350, behavior: 'smooth' });
    });

    // Event delegation for history
    document.getElementById('historyList').addEventListener('click', e => {
      const item = e.target.closest('[data-history-index]');
      if (item) {
        const history = this.history.get();
        const entry = history[parseInt(item.dataset.historyIndex)];
        if (entry) {
          this.input.value = entry.query;
          this._updateCounter();
          this.input.focus();
          this.analyze();
        }
      }
    });

    // Event delegation for copy button in results
    this.resultsEl.addEventListener('click', e => {
      const btn = e.target.closest('[data-action="copy"]');
      if (btn) this._copyResults(btn);
    });
  }

  async _init() {
    UIRenderer.renderScenarios();
    this.history.render();
    this._updateCounter();
    await this._checkAPI();
  }

  async _checkAPI() {
    this._setStatus('checking', 'Проверка...');
    const status = await this.rag.checkStatus();

    if (this.rag.ready) {
      this._setStatus('connected', `✓ УК РТ: ${status.articles || '—'} статей`);
      this.apiWarning.style.display = 'none';
      await this.rag.loadCatalog();
      UIRenderer.renderKnowledgeBase(this.rag.catalog, this.kbFilter?.value || '');
    } else {
      // Локального режима нет: без индекса УК РТ отвечать нечем, и
      // догадка по ключевым словам здесь хуже честного отказа.
      this._setStatus('disconnected', '✗ Индекс УК РТ недоступен');
      this.apiWarning.style.display = 'flex';
      UIRenderer.renderKnowledgeBase([]);
    }
  }

  _setStatus(state, text) {
    this.statusBadge.setAttribute('data-state', state);
    this.statusText.textContent = text;
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
      this.btnAnalyze.innerHTML = '<div class="spinner"></div><span>Анализ...</span>';
      this.btnQualify.innerHTML = '<div class="spinner"></div><span>Обработка...</span>';
      this.progress.show();
      this.resultsEl.removeAttribute('data-active');
    } else {
      this.btnAnalyze.innerHTML = `
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <span>Найти норму</span><kbd>Ctrl+↵</kbd>`;
      this.btnQualify.innerHTML = `
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <span>Полная квалификация</span>`;
      this.progress.hide();
    }
  }

  /** Общая проверка перед запросом: есть описание и есть чем отвечать. */
  _canRun() {
    const description = this.input.value.trim();
    if (!description) {
      this.input.style.animation = 'shake 0.4s ease';
      setTimeout(() => this.input.style.animation = '', 400);
      this.input.focus();
      return null;
    }
    if (this.isProcessing) return null;
    if (!this.rag.ready) {
      UIRenderer.renderError(
        'Индекс УК РТ недоступен',
        'Страница отвечает только по тексту Уголовного кодекса РТ из RAG-индекса. '
        + 'Пока индекс не загружен, ответ дать неоткуда.'
      );
      return null;
    }
    return description;
  }

  /**
   * Поиск норм по описанию события. Языковая модель не участвует: на этом
   * железе генерация занимает минуты, а сам текст статьи с санкцией — это
   * уже ответ, и он приходит из индекса дословно.
   */
  async analyze() {
    const description = this._canRun();
    if (!description) return;
    this._setProcessing(true);

    try {
      this.progress.update('retrieval', 'active', 'Поиск норм в тексте УК РТ...');
      const data = await this.rag.search(description);
      this.progress.update('llm', 'done', '— не используется: ответ из индекса');

      if (!data.grounded) {
        this.progress.update('retrieval', 'error', '✗ Норма не найдена');
        this.progress.update('validation', 'done', '✓ Ответ без опоры не выдан');
        await Utils.sleep(200);
        this._setProcessing(false);
        UIRenderer.renderNotFound(data.message || '', description);
        return;
      }

      const found = (data.articles || []).length;
      this.progress.update('retrieval', 'done', `✓ Найдено норм: ${found}`);
      this.progress.update('validation', 'done', '✓ Все ссылки из текста кодекса');
      await Utils.sleep(200);

      this._setProcessing(false);
      UIRenderer.renderSearch(data);

      const top = data.articles[0];
      if (top) {
        this.history.add(description, { number: top.article, name: top.article_title || '' });
        this.history.render();
      }
    } catch (err) {
      console.error('Search error:', err);
      this._setProcessing(false);
      UIRenderer.renderError('Ошибка поиска по УК РТ', err.message);
    }
  }

  /** Полный разбор: состав, санкции и обстоятельства в границах найденных норм. */
  async qualify() {
    const description = this._canRun();
    if (!description) return;
    this._setProcessing(true);

    try {
      this.progress.update('retrieval', 'active', 'Поиск норм в тексте УК РТ...');
      this.progress.update('llm', 'active', 'Ожидание первых строк ответа...');

      let tokens = 0;
      const data = await this.llm.qualifyStream(description, {
        onMeta: meta => {
          this.progress.update('retrieval', 'done', `✓ Статей в опоре: ${(meta.article_numbers || []).length}`);
          UIRenderer.renderQualificationStart(meta);
        },
        onWait: seconds => {
          // Первый токен приходит через минуты: разбор промпта, а на холодном
          // старте ещё и чтение модели с диска. Молча ждать нельзя.
          this.progress.update('llm', 'active', `модель готовит ответ, ${Math.round(seconds)} с...`);
        },
        onChunk: chunk => {
          tokens += 1;
          UIRenderer.appendQualificationChunk(chunk);
          if (tokens % 20 === 0) this.progress.update('llm', 'active', `${tokens} фрагментов...`);
        },
      });

      if (!data.grounded) {
        this.progress.update('retrieval', 'error', '✗ Норма не найдена');
        this.progress.update('llm', 'done', '— модель не запускалась');
        this.progress.update('validation', 'done', '✓ Ответ без опоры не выдан');
        this._setProcessing(false);
        UIRenderer.renderNotFound(data.qualification || data.message || data.error || '', description);
        return;
      }

      this.progress.update('llm', 'done', '✓ Разбор получен');
      const unverified = (data.unverified_articles || []).length;
      this.progress.update('validation', unverified ? 'error' : 'done',
        unverified ? `✗ Ссылок мимо RAG: ${unverified}` : '✓ Все ссылки подтверждены');

      this._setProcessing(false);
      // Финальная перерисовка: тот же текст, но разложенный по секциям и с
      // дословными статьями из индекса под ним.
      UIRenderer.renderQualification(data, description);
    } catch (err) {
      console.error('Qualification error:', err);
      this._setProcessing(false);
      UIRenderer.renderError('Ошибка квалификации', err.message);
    }
  }

  async _copyResults(btn) {
    try {
      const text = this.resultsEl.innerText;
      await navigator.clipboard.writeText(text);
      btn.setAttribute('data-copied', 'true');
      btn.textContent = '✓ Скопировано';
      UIRenderer.showToast('Результаты скопированы');
      setTimeout(() => {
        btn.removeAttribute('data-copied');
        btn.innerHTML = `<svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> Копировать`;
      }, 2000);
    } catch {
      UIRenderer.showToast('Не удалось скопировать', 'error');
    }
  }
}

/* ═══════════════════════════════════════════════════════════
   BOOTSTRAP
   ═══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  window.LegalAI = new LegalAIApp();
});
</script>
</body>
</html>