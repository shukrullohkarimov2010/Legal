<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="description" content="LegalAI Pro — интеллектуальный анализ УК Таджикистана на базе локальной LLM llama3.1:8b с RAG-поиском">
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
            Опишите ситуацию — система выполнит векторный поиск по базе УК РТ, 
            а <strong style="color:var(--accent-amber)">llama3.1:8b</strong> определит статью, 
            реальные сроки и алгоритм действий для потерпевшего.
          </p>
          <div class="hero-badge">🦙 llama3.1:8b • RAG Pipeline • Локально</div>
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
      <div class="rag-source">🔒 RAG-база: ст. 104, 111, 114, 246–250 УК РТ</div>
      
      <div class="panel__header">
        <h2 class="panel__title" id="panel-title">Опишите преступление</h2>
        <p class="panel__subtitle">Чем подробнее описание — тем точнее квалификация по статьям УК РТ</p>
      </div>

      <div id="apiWarning" class="alert alert--warning" style="display:none" role="alert">
        <span class="alert__icon" aria-hidden="true">⚠️</span>
        <div>
          <strong>RAG API недоступен.</strong> Запустите <code>python app.py</code> для работы с llama3.1:8b.
          <br><small>Активирован локальный режим анализа.</small>
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
            <span>RAG-анализ</span>
            <kbd>Ctrl+↵</kbd>
          </button>
          <button id="btnQualify" class="btn btn--secondary" type="button">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Квалификация ИИ</span>
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
              <div class="step-label">Векторный поиск по базе УК РТ</div>
              <div class="step-detail" data-detail>Ожидание...</div>
            </div>
          </div>
          <div class="progress-step" data-step="llm">
            <div class="step-indicator">2</div>
            <div class="step-content">
              <div class="step-label">Генерация ответа llama3.1:8b</div>
              <div class="step-detail" data-detail>Ожидание...</div>
            </div>
          </div>
          <div class="progress-step" data-step="validation">
            <div class="step-indicator">3</div>
            <div class="step-content">
              <div class="step-label">Валидация и формирование отчёта</div>
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
      <p class="panel__subtitle">Основные статьи. Кликните — текст подставится в форму.</p>
      <div class="kb-grid" id="kbGrid" role="list"></div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
      <p>⚖️ LegalAI Pro • RAG-анализ УК РТ на базе <a href="https://ollama.com" target="_blank" rel="noopener">Ollama + llama3.1:8b</a></p>
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
  MODEL: 'llama3.1:8b',
  MAX_INPUT_LENGTH: 2000,
  RAG_TOP_K: 10,
  RETRY_MAX: 3,
  RETRY_BASE_DELAY: 800,
  TIMEOUT_SHORT: 5000,
  TIMEOUT_MEDIUM: 15000,
  TIMEOUT_LONG: 120000,
  DEBOUNCE_MS: 300,
  HISTORY_MAX: 8,
  CONFIDENCE_THRESHOLDS: Object.freeze({ HIGH: 0.8, MEDIUM: 0.5 }),
});

/* ═══════════════════════════════════════════════════════════
   LEGAL KNOWLEDGE BASE (RAG Fallback)
   ═══════════════════════════════════════════════════════════ */
const LEGAL_DB = Object.freeze({
  '104': Object.freeze({
    number: '104',
    name: 'Умышленное убийство',
    description: 'Умышленное причинение смерти другому человеку. Объект — жизнь человека. Субъективная сторона — прямой или косвенный умысел.',
    keywords: ['убийств', 'убил', 'смерть', 'зарезал', 'застрелил', 'задушил', 'отравил', 'лишил жизни'],
    sentences: Object.freeze([
      Object.freeze({ type: 'Основной состав', value: '5–15 лет', note: 'ч. 1 ст. 104' }),
      Object.freeze({ type: 'Отягчающие', value: '10–20 лет', note: 'ч. 2 — двух и более лиц' }),
      Object.freeze({ type: 'Особо тяжкое', value: '15–25 лет / пожизн.', note: 'ч. 3 — исключительная жестокость' }),
    ]),
  }),
  '111': Object.freeze({
    number: '111',
    name: 'Умышленное причинение тяжкого вреда здоровью',
    description: 'Умышленное причинение тяжкого вреда здоровью, опасного для жизни человека, или повлёкшего потерю зрения, слуха, органа.',
    keywords: ['ножев', 'ранен', 'тяжк', 'вред здоров', 'опасн для жизн', 'потерял сознани', 'кровотечен', 'перелом черепа', 'проникающ'],
    sentences: Object.freeze([
      Object.freeze({ type: 'Основной состав', value: 'До 5 лет', note: 'ч. 1 ст. 111' }),
      Object.freeze({ type: 'Отягчающие', value: '3–8 лет', note: 'ч. 2 — группой, неоднократно' }),
      Object.freeze({ type: 'Смерть потерпевшего', value: '5–10 лет', note: 'ч. 3 — по неосторожности' }),
    ]),
  }),
  '114': Object.freeze({
    number: '114',
    name: 'Умышленное причинение средней тяжести вреда здоровью',
    description: 'Умышленное причинение вреда здоровью, не опасного для жизни, повлёкшего длительное расстройство здоровья (свыше 21 дня).',
    keywords: ['ударил', 'побо', 'синяк', 'ссадин', 'кулаком', 'сломал нос', 'средн.*тяжест', 'ушиб', 'растяжен'],
    sentences: Object.freeze([
      Object.freeze({ type: 'Основной состав', value: 'До 2 лет', note: 'ч. 1 ст. 114' }),
      Object.freeze({ type: 'Отягчающие', value: 'До 4 лет', note: 'ч. 2 — группой, хулиганство' }),
    ]),
  }),
  '246': Object.freeze({
    number: '246',
    name: 'Мошенничество',
    description: 'Хищение чужого имущества или приобретение права на имущество путём обмана или злоупотребления доверием.',
    keywords: ['мошен', 'обман', 'перевёл', 'позвонили', 'банк', 'безопасный счёт', 'фишинг', 'подделк', 'подставн', 'афер'],
    sentences: Object.freeze([
      Object.freeze({ type: 'Основной состав', value: 'До 3 лет', note: 'ч. 1 ст. 246' }),
      Object.freeze({ type: 'Повторно / группой', value: 'До 6 лет', note: 'ч. 2 ст. 246' }),
      Object.freeze({ type: 'Крупный размер', value: '5–10 лет', note: 'ч. 3 — служебное положение' }),
      Object.freeze({ type: 'Особо крупный', value: '8–12 лет', note: 'ч. 4 — организованная группа' }),
    ]),
  }),
  '247': Object.freeze({
    number: '247',
    name: 'Кража',
    description: 'Тайное хищение чужого имущества. Преступник действует незаметно для потерпевшего и окружающих.',
    keywords: ['карман', 'тайно', 'вытащил', 'незаметно', 'украли', 'пропаж', 'взлом', 'двер', 'окн', 'похитил тайно', 'из кармана'],
    sentences: Object.freeze([
      Object.freeze({ type: 'Основной состав', value: 'До 3 лет', note: 'ч. 1 ст. 247' }),
      Object.freeze({ type: 'Группой / с проникновением', value: 'До 5 лет', note: 'ч. 2 ст. 247' }),
      Object.freeze({ type: 'Крупный размер', value: '5–8 лет', note: 'ч. 3 ст. 247' }),
      Object.freeze({ type: 'Особо крупный', value: '7–12 лет', note: 'ч. 4 — организованная группа' }),
    ]),
  }),
  '248': Object.freeze({
    number: '248',
    name: 'Грабёж',
    description: 'Открытое хищение чужого имущества. Совершается в присутствии потерпевшего, осознающего противоправность действий.',
    keywords: ['вырвал', 'открыто', 'подбежал', 'сумку из рук', 'грабёж', 'грабеж', 'сорвал', 'выхватил', 'отнял'],
    sentences: Object.freeze([
      Object.freeze({ type: 'Основной состав', value: 'До 5 лет', note: 'ч. 1 ст. 248' }),
      Object.freeze({ type: 'С насилием / группой', value: '5–10 лет', note: 'ч. 2 ст. 248' }),
      Object.freeze({ type: 'Организованная группа', value: '8–15 лет', note: 'ч. 3 ст. 248' }),
    ]),
  }),
  '249': Object.freeze({
    number: '249',
    name: 'Разбой',
    description: 'Нападение в целях хищения чужого имущества, совершённое с применением насилия, опасного для жизни или здоровья, либо с угрозой такого насилия.',
    keywords: ['нож', 'оруж', 'пистолет', 'напали', 'угрожал уб', 'ранен.*ограблен', 'разбой', 'нападен.*хищен'],
    sentences: Object.freeze([
      Object.freeze({ type: 'Основной состав', value: '3–7 лет', note: 'ч. 1 ст. 249' }),
      Object.freeze({ type: 'С оружием / группой', value: '7–12 лет', note: 'ч. 2 ст. 249' }),
      Object.freeze({ type: 'Тяжкий вред', value: '10–15 лет', note: 'ч. 3 ст. 249' }),
      Object.freeze({ type: 'Организованная группа', value: '12–20 лет', note: 'ч. 4 ст. 249' }),
    ]),
  }),
  '250': Object.freeze({
    number: '250',
    name: 'Вымогательство',
    description: 'Требование передачи чужого имущества или права на имущество под угрозой применения насилия, уничтожения имущества или распространения сведений.',
    keywords: ['вымог', 'потребовал.*деньг', 'угрожа.*расправ', 'шантаж', 'встречу.*деньг', 'заплати.*иначе', 'дань'],
    sentences: Object.freeze([
      Object.freeze({ type: 'Основной состав', value: 'До 4 лет', note: 'ч. 1 ст. 250' }),
      Object.freeze({ type: 'Повторно / группой', value: '3–7 лет', note: 'ч. 2 ст. 250' }),
      Object.freeze({ type: 'С насилием', value: '7–12 лет', note: 'ч. 3 ст. 250' }),
      Object.freeze({ type: 'Особо крупный', value: '10–15 лет', note: 'ч. 4 ст. 250' }),
    ]),
  }),
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
   ACTIONS TEMPLATE
   ═══════════════════════════════════════════════════════════ */
const ACTIONS = Object.freeze([
  { step: 'Заявление в РОВД', detail: 'Немедленно обратитесь в ближайший отдел милиции. Подайте письменное заявление (арза) с описанием обстоятельств.' },
  { step: 'Талон-уведомление', detail: 'Дежурный обязан выдать талон-уведомление с номером КУСП — доказательство регистрации обращения.' },
  { step: 'Сбор доказательств', detail: 'Сохраните контакты свидетелей, запросите записи с камер (хранятся 7–30 дней), сделайте фото повреждений.' },
  { step: 'Медосвидетельствование', detail: 'При травмах — снимите побои в травмпункте. Справка — ключевое доказательство в суде.' },
  { step: 'Следственные действия', detail: 'Следователь проведёт проверку, возбудит уголовное дело, проведёт очные ставки и опознание.' },
  { step: 'Суд и приговор', detail: 'После завершения следствия дело передаётся в суд. Потерпевший вправе заявить гражданский иск.' },
]);

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

  extractJson(text) {
    if (!text) return null;
    
    // Try markdown code block first
    const codeBlockMatch = text.match(/```(?:json)?\s*\n?([\s\S]*?)\n?```/);
    if (codeBlockMatch) {
      try { return JSON.parse(codeBlockMatch[1].trim()); } catch {}
    }
    
    // Try raw JSON object
    const jsonMatch = text.match(/\{[\s\S]*?\}/);
    if (jsonMatch) {
      try { return JSON.parse(jsonMatch[0]); } catch {}
    }
    
    // Try to find JSON with nested braces
    let depth = 0;
    let start = -1;
    for (let i = 0; i < text.length; i++) {
      if (text[i] === '{') {
        if (depth === 0) start = i;
        depth++;
      } else if (text[i] === '}') {
        depth--;
        if (depth === 0 && start !== -1) {
          try { return JSON.parse(text.slice(start, i + 1)); } catch {}
          start = -1;
        }
      }
    }
    
    return null;
  },

  sanitizeRagText(text) {
    return String(text || '')
      .replace(/[^\p{L}\p{N}\s.,;:!?\-()[\]{}"'«»§№]/gu, ' ')
      .replace(/\s{2,}/g, ' ')
      .trim()
      .slice(0, 1200);
  },
};

/* ═══════════════════════════════════════════════════════════
   RAG ENGINE — Multi-stage retrieval with scoring
   ═══════════════════════════════════════════════════════════ */
class RAGEngine {
  constructor() {
    this.db = new Map(Object.entries(LEGAL_DB));
    this.apiAvailable = false;
  }

  /**
   * Multi-stage article matching with weighted scoring
   * Stage 1: Exact keyword match (highest weight)
   * Stage 2: TF-IDF-like scoring on description
   * Stage 3: RAG API results (if available)
   */
  matchArticle(description, ragResults = []) {
    const lower = description.toLowerCase();
    const scores = new Map();

    // Stage 1: Keyword matching with position weighting
    for (const [num, article] of this.db) {
      let score = 0;
      for (const keyword of article.keywords) {
        try {
          const regex = new RegExp(keyword, 'gi');
          const matches = lower.match(regex);
          if (matches) {
            // More matches = higher score, but with diminishing returns
            score += Math.min(matches.length * 15, 45);
          }
        } catch {}
      }
      if (score > 0) {
        scores.set(num, (scores.get(num) || 0) + score);
      }
    }

    // Stage 2: Semantic similarity via token overlap
    const descTokens = new Set(lower.split(/\s+/).filter(t => t.length > 3));
    for (const [num, article] of this.db) {
      const articleTokens = new Set(
        `${article.name} ${article.description}`.toLowerCase().split(/\s+/).filter(t => t.length > 3)
      );
      let overlap = 0;
      for (const token of descTokens) {
        if (articleTokens.has(token)) overlap++;
      }
      const similarity = descTokens.size > 0 ? overlap / descTokens.size : 0;
      if (similarity > 0.05) {
        scores.set(num, (scores.get(num) || 0) + similarity * 30);
      }
    }

    // Stage 3: RAG API results boost
    if (ragResults.length > 0) {
      for (const result of ragResults) {
        const text = `${result.title || ''} ${result.text || ''}`.toLowerCase();
        for (const [num] of this.db) {
          const patterns = [
            new RegExp(`ст\\.?\\s*${num}\\b`, 'i'),
            new RegExp(`статья\\s*${num}\\b`, 'i'),
            new RegExp(`моддаи\\s*${num}\\b`, 'i'),
            new RegExp(`\\b${num}\\b`),
          ];
          for (const pattern of patterns) {
            if (pattern.test(text)) {
              scores.set(num, (scores.get(num) || 0) + 25);
              break;
            }
          }
        }
      }
    }

    // Stage 4: Aggravating factors boost
    const aggravatingScore = this._scoreAggravating(lower);
    for (const [num, boost] of Object.entries(aggravatingScore)) {
      scores.set(num, (scores.get(num) || 0) + boost);
    }

    // Find best match
    if (scores.size === 0) return { article: null, confidence: 'low', score: 0 };

    let bestNum = null;
    let bestScore = 0;
    for (const [num, score] of scores) {
      if (score > bestScore) {
        bestScore = score;
        bestNum = num;
      }
    }

    const article = this.db.get(bestNum) || null;
    const confidence = this._calculateConfidence(bestScore, scores);

    return { article, confidence, score: bestScore };
  }

  _scoreAggravating(text) {
    const boosts = {};
    const rules = [
      { pattern: /нож|оруж|пистолет|топор/, articles: ['249', '111'], boost: 20 },
      { pattern: /дво|тро|групп|несколько/, articles: ['248', '249', '250'], boost: 10 },
      { pattern: /крупн|особо крупн|дорог/, articles: ['247', '246', '250'], boost: 8 },
      { pattern: /угрожал|угроз|шантаж/, articles: ['250', '249'], boost: 15 },
      { pattern: /обман|позвонил|перевёл/, articles: ['246'], boost: 18 },
      { pattern: /тайно|незаметно|карман/, articles: ['247'], boost: 18 },
      { pattern: /открыто|вырвал|подбежал/, articles: ['248'], boost: 18 },
    ];

    for (const rule of rules) {
      if (rule.pattern.test(text)) {
        for (const num of rule.articles) {
          boosts[num] = (boosts[num] || 0) + rule.boost;
        }
      }
    }
    return boosts;
  }

  _calculateConfidence(bestScore, allScores) {
    if (bestScore >= 60) return 'high';
    if (bestScore >= 30) return 'medium';
    
    // Check if there's a clear winner
    const sorted = [...allScores.values()].sort((a, b) => b - a);
    if (sorted.length >= 2 && sorted[0] > sorted[1] * 2) return 'medium';
    
    return 'low';
  }

  getAggravatingFactors(description) {
    const lower = description.toLowerCase();
    const factors = [];
    
    const checks = [
      { pattern: /нож|оруж|пистолет|топор/, label: 'применение оружия или предметов' },
      { pattern: /дво|тро|групп|несколько человек/, label: 'совершение группой лиц' },
      { pattern: /крупн|особо крупн|дорог/, label: 'крупный размер ущерба' },
      { pattern: /ранен|травм|удар|побо|синяк|кровь/, label: 'причинение вреда здоровью' },
      { pattern: /повторн|неоднократн|ранее судим/, label: 'рецидив / неоднократность' },
      { pattern: /ноч|тёмн|переулк|безлюдн/, label: 'совершение в тёмное время суток' },
      { pattern: /ребён|несовершеннолетн|пожил|инвалид/, label: 'потерпевший — уязвимое лицо' },
      { pattern: /алкогол|пьян|наркот/, label: 'состояние опьянения' },
    ];

    for (const check of checks) {
      if (check.pattern.test(lower)) {
        factors.push(check.label);
      }
    }

    return factors;
  }

  async searchAPI(query) {
    if (!this.apiAvailable) return [];
    
    try {
      const response = await Utils.fetchWithRetry(
        `${CONFIG.API_BASE}/api/rag/search`,
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ query, top_k: CONFIG.RAG_TOP_K }),
          timeout: CONFIG.TIMEOUT_MEDIUM,
        }
      );
      const data = await response.json();
      return Array.isArray(data.results) ? data.results : [];
    } catch {
      return [];
    }
  }

  async checkStatus() {
    try {
      const response = await Utils.fetchWithRetry(
        `${CONFIG.API_BASE}/api/rag/status`,
        { timeout: CONFIG.TIMEOUT_SHORT }
      );
      const data = await response.json();
      this.apiAvailable = !!data.ready;
      return data;
    } catch {
      this.apiAvailable = false;
      return null;
    }
  }

  async loadArticlesFromAPI() {
    if (!this.apiAvailable) return false;
    
    try {
      const response = await Utils.fetchWithRetry(
        `${CONFIG.API_BASE}/api/rag/articles`,
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ source: '574_ru.doc' }),
          timeout: CONFIG.TIMEOUT_MEDIUM,
        }
      );
      const data = await response.json();
      
      if (!Array.isArray(data.articles) || data.articles.length === 0) return false;
      
      for (const article of data.articles) {
        if (!article.number) continue;
        const existing = LEGAL_DB[article.number];
        this.db.set(article.number, {
          number: article.number,
          name: article.title || existing?.name || `Статья ${article.number}`,
          description: article.description || article.text?.slice(0, 300) || existing?.description || '',
          keywords: existing?.keywords || [],
          sentences: Array.isArray(article.sentences) && article.sentences.length > 0
            ? article.sentences
            : existing?.sentences || [],
        });
      }
      return true;
    } catch {
      return false;
    }
  }
}

/* ═══════════════════════════════════════════════════════════
   LLM SERVICE — Prompt engineering + streaming
   ═══════════════════════════════════════════════════════════ */
class LLMService {
  constructor(ragEngine) {
    this.rag = ragEngine;
  }

  buildClassificationPrompt(description, ragResults) {
    const allowedArticles = [...this.rag.db.values()]
      .map(a => `  - Ст. ${a.number}: ${a.name}`)
      .join('\n');

    const ragContext = ragResults.length > 0
      ? ragResults.map((r, i) => {
          const cleanText = Utils.sanitizeRagText(r.text);
          return `[Фрагмент ${i + 1}] (${r.source || 'RAG'}): ${cleanText}`;
        }).join('\n\n')
      : 'Фрагменты не найдены. Используй знания из списка статей.';

    return `Ты — эксперт-криминалист по Уголовному кодексу Республики Таджикистан.

ЗАДАЧА: Квалифицируй описанное деяние, используя ТОЛЬКО предоставленные фрагменты RAG и список статей.

ДОПУСТИМЫЕ СТАТЬИ (выбери ОДНУ):
${allowedArticles}

ФРАГМЕНТЫ ИЗ БАЗЫ УК РТ:
${ragContext}

ОПИСАНИЕ ДЕЯНИЯ:
"${description}"

ИНСТРУКЦИЯ:
1. Проанализируй объективную сторону (способ, орудие, обстановка)
2. Определи объект посягательства
3. Установи субъективную сторону (умысел/неосторожность)
4. Сопоставь с диспозицией статьи
5. Верни СТРОГО JSON без пояснений:

{
  "article_number": "номер статьи из списка",
  "article_name": "название статьи",
  "confidence": "high|medium|low",
  "reasoning": "краткое обоснование (2-3 предложения)",
  "qualifying_signs": ["признак 1", "признак 2"],
  "aggravating": ["отягчающее обстоятельство или null"]
}`;
  }

  async *streamClassification(description, ragResults) {
    const prompt = this.buildClassificationPrompt(description, ragResults);
    
    const response = await fetch(`${CONFIG.API_BASE}/api/chat`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/x-ndjson',
      },
      body: JSON.stringify({
        message: prompt,
        model: CONFIG.MODEL,
        temperature: 0.1,
        top_p: 0.9,
        history: [],
      }),
    });

    if (!response.ok || !response.body) {
      throw new Error(`LLM stream error: HTTP ${response.status}`);
    }

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
          const parsed = JSON.parse(line);
          if (parsed.response) yield parsed.response;
          if (parsed.done) return;
        } catch {}
      }
    }
  }

  async qualifyCrime(description) {
    const response = await Utils.fetchWithRetry(
      `${CONFIG.API_BASE}/api/crime/qualify`,
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ crime_description: description }),
        timeout: CONFIG.TIMEOUT_LONG,
      }
    );
    
    const data = await response.json();
    if (!data.qualification) throw new Error('API не вернул данные квалификации');
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

  static renderKnowledgeBase(db) {
    const grid = document.getElementById('kbGrid');
    const articles = [...db.values()];
    
    if (articles.length === 0) {
      grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:var(--ink-tertiary);padding:2rem;">База знаний пуста</p>';
      return;
    }

    grid.innerHTML = articles.map(a => {
      const minSentence = a.sentences?.[0]?.value || '—';
      return `
        <button class="kb-card" data-article="${a.number}" type="button" role="listitem">
          <div class="kb-card__number">${Utils.escapeHtml(a.number)}</div>
          <div class="kb-card__name">${Utils.escapeHtml(a.name)}</div>
          <div class="kb-card__desc">${Utils.escapeHtml(a.description)}</div>
          <div class="kb-card__sentence">⏱ ${Utils.escapeHtml(minSentence)}</div>
        </button>
      `;
    }).join('');
  }

  static renderResults({ article, description, rawText, confidence, aggravating }) {
    if (!article) return;

    const confidencePercent = confidence === 'high' ? 92 : confidence === 'medium' ? 68 : 40;
    const confidenceClass = confidence === 'high' ? 'high' : confidence === 'medium' ? 'medium' : 'low';
    const factors = aggravating || [];

    const container = document.getElementById('results');
    container.innerHTML = `
      <!-- Verdict -->
      <div class="result-card result-card--verdict">
        <div class="result-card__header">
          <div class="result-card__icon" aria-hidden="true">⚖️</div>
          <div style="flex:1">
            <div class="result-card__label">Квалификация деяния</div>
            <div class="result-card__title">${Utils.escapeHtml(article.name)}</div>
          </div>
          <button class="copy-btn" data-action="copy" type="button">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Копировать
          </button>
        </div>
        <div style="display:flex;align-items:center;gap:var(--space-3);flex-wrap:wrap;margin-bottom:var(--space-4)">
          <span class="article-badge">Ст. ${Utils.escapeHtml(article.number)} УК РТ</span>
        </div>
        <div class="article-text">${Utils.escapeHtml(article.description)}</div>
        <div class="confidence">
          <span style="color:var(--ink-tertiary);font-size:var(--text-sm)">Уверенность:</span>
          <div class="confidence__bar">
            <div class="confidence__fill confidence__fill--${confidenceClass}" style="width:${confidencePercent}%"></div>
          </div>
          <span class="confidence__value">${confidencePercent}%</span>
        </div>
      </div>

      <!-- Punishments -->
      <div class="result-card">
        <div class="result-card__header">
          <div class="result-card__icon" aria-hidden="true">⏱️</div>
          <div>
            <div class="result-card__label">Наказание</div>
            <div class="result-card__title">Реальные сроки по УК Таджикистана</div>
          </div>
        </div>
        <div class="punishment-grid">
          ${(article.sentences || []).map(s => `
            <div class="punishment-card">
              <div class="punishment-card__type">${Utils.escapeHtml(s.type)}</div>
              <div class="punishment-card__value">${Utils.escapeHtml(s.value)}</div>
              <div class="punishment-card__note">${Utils.escapeHtml(s.note || '')}</div>
            </div>
          `).join('') || '<p style="color:var(--ink-tertiary)">Данные недоступны</p>'}
        </div>
        ${factors.length > 0 ? `
          <div class="aggravating-box">
            <strong>🚨 Отягчающие обстоятельства:</strong> ${Utils.escapeHtml(factors.join('; '))} — срок может быть увеличен.
          </div>
        ` : `
          <div class="aggravating-box" style="border-left-color:var(--accent-green);background:var(--success-bg)">
            <strong style="color:var(--success-text)">✓ Отягчающие обстоятельства не выявлены.</strong> Применяется основная квалификация.
          </div>
        `}
      </div>

      <!-- Actions -->
      <div class="result-card">
        <div class="result-card__header">
          <div class="result-card__icon" aria-hidden="true">👮</div>
          <div>
            <div class="result-card__label">Алгоритм действий</div>
            <div class="result-card__title">Что делать потерпевшему</div>
          </div>
        </div>
        <ol class="action-list">
          ${ACTIONS.map(a => `
            <li><strong>${Utils.escapeHtml(a.step)}</strong><span>${Utils.escapeHtml(a.detail)}</span></li>
          `).join('')}
        </ol>
      </div>

      <!-- Raw -->
      <details class="raw-toggle">
        <summary>🤖 Технические данные (JSON / raw)</summary>
        <pre>${Utils.escapeHtml(rawText || JSON.stringify(article, null, 2))}</pre>
      </details>
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
          <strong>Рекомендации:</strong>
          <ul style="margin-top:var(--space-2);padding-left:1.25rem">
            <li>Опишите ситуацию подробнее (способ, объект, обстоятельства)</li>
            <li>Выберите готовый сценарий выше</li>
            <li>Проверьте, запущен ли Python RAG API</li>
          </ul>
        </div>
      </div>
    `;
    container.setAttribute('data-active', 'true');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  static renderQualification(data, description) {
    const text = data.qualification || '';
    const sections = UIRenderer._parseSections(text);
    const container = document.getElementById('results');

    const sectionCards = [
      { key: 'квалификация', icon: '⚖️', label: 'КВАЛИФИКАЦИЯ', title: 'Определение состава преступления' },
      { key: 'санкции', icon: '⏱️', label: 'САНКЦИИ', title: 'Наказание по УК РТ' },
      { key: 'факты', icon: '🔍', label: 'ФАКТЫ', title: 'Доказательственная база' },
      { key: 'обстоятельства', icon: '⚠️', label: 'ОБСТОЯТЕЛЬСТВА', title: 'Смягчающие и отягчающие' },
      { key: 'анализ', icon: '🧠', label: 'ПРАВОВОЙ АНАЛИЗ', title: 'Юридическое обоснование' },
      { key: 'вывод', icon: '✅', label: 'ВЫВОД', title: 'Итоговое заключение' },
    ];

    container.innerHTML = `
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
      <details class="raw-toggle">
        <summary>🤖 Полный ответ llama3.1:8b</summary>
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
    this.rag = new RAGEngine();
    this.llm = new LLMService(this.rag);
    this.theme = new ThemeManager();
    this.history = new HistoryManager();
    this.progress = new ProgressTracker();
    this.isProcessing = false;
    this.abortController = null;

    // DOM refs
    this.input = document.getElementById('crimeInput');
    this.charCount = document.getElementById('charCount');
    this.btnAnalyze = document.getElementById('btnAnalyze');
    this.btnQualify = document.getElementById('btnQualify');
    this.statusBadge = document.getElementById('statusBadge');
    this.statusText = document.getElementById('statusText');
    this.apiWarning = document.getElementById('apiWarning');
    this.resultsEl = document.getElementById('results');

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

    // Event delegation for KB cards
    document.getElementById('kbGrid').addEventListener('click', e => {
      const card = e.target.closest('[data-article]');
      if (card) {
        const article = this.rag.db.get(card.dataset.article);
        if (article) {
          this.input.value = `Преступление по статье ${article.number} УК РТ (${article.name}): `;
          this._updateCounter();
          this.input.focus();
          window.scrollTo({ top: 350, behavior: 'smooth' });
        }
      }
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
    UIRenderer.renderKnowledgeBase(this.rag.db);
    this.history.render();
    this._updateCounter();
    await this._checkAPI();
  }

  async _checkAPI() {
    this._setStatus('checking', 'Проверка...');
    const status = await this.rag.checkStatus();

    if (status && status.ready) {
      this._setStatus('connected', `✓ RAG: ${status.chunks || '—'} чанков`);
      this.apiWarning.style.display = 'none';
      
      // Load articles from API
      const loaded = await this.rag.loadArticlesFromAPI();
      if (loaded) UIRenderer.renderKnowledgeBase(this.rag.db);
    } else {
      this._setStatus('disconnected', '✗ Локальный режим');
      this.apiWarning.style.display = 'flex';
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
        <span>RAG-анализ</span><kbd>Ctrl+↵</kbd>`;
      this.btnQualify.innerHTML = `
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <span>Квалификация ИИ</span>`;
      this.progress.hide();
    }
  }

  async analyze() {
    const description = this.input.value.trim();
    if (!description) {
      this.input.style.animation = 'shake 0.4s ease';
      setTimeout(() => this.input.style.animation = '', 400);
      this.input.focus();
      return;
    }

    if (this.isProcessing) return;
    this._setProcessing(true);

    let ragResults = [];
    let rawText = '';
    let matchedArticle = null;
    let confidence = 'low';

    try {
      // STEP 1: RAG Retrieval
      this.progress.update('retrieval', 'active', 'Векторный поиск по базе УК РТ...');
      
      if (this.rag.apiAvailable) {
        ragResults = await this.rag.searchAPI(description);
        this.progress.update('retrieval', 'done',
          ragResults.length > 0 ? `✓ Найдено ${ragResults.length} фрагментов` : '✓ База проверена');
      } else {
        this.progress.update('retrieval', 'done', '✓ Локальный поиск (API оффлайн)');
      }

      // STEP 2: LLM Analysis (if available)
      this.progress.update('llm', 'active', 'llama3.1:8b генерирует ответ...');
      
      if (this.rag.apiAvailable) {
        try {
          let tokenCount = 0;
          for await (const chunk of this.llm.streamClassification(description, ragResults)) {
            rawText += chunk;
            tokenCount++;
            if (tokenCount % 15 === 0) {
              this.progress.update('llm', 'active', `${tokenCount} токенов...`);
            }
          }

          const parsed = Utils.extractJson(rawText);
          if (parsed?.article_number) {
            const num = String(parsed.article_number).replace(/\D/g, '');
            const article = this.rag.db.get(num);
            if (article) {
              matchedArticle = article;
              confidence = parsed.confidence || 'medium';
            }
          }
          this.progress.update('llm', 'done', '✓ LLM-анализ завершён');
        } catch (err) {
          console.warn('LLM stream failed, falling back:', err.message);
          this.progress.update('llm', 'error', '✗ LLM недоступен → локальный анализ');
        }
      } else {
        this.progress.update('llm', 'done', '✓ Пропущен (API оффлайн)');
      }

      // Fallback: Local RAG matching
      if (!matchedArticle) {
        const result = this.rag.matchArticle(description, ragResults);
        matchedArticle = result.article;
        confidence = result.confidence;
        if (!rawText) {
          rawText = `Локальный RAG-матчер.\nScore: ${result.score}\nConfidence: ${result.confidence}`;
        }
      }

      // STEP 3: Validation & Render
      this.progress.update('validation', 'active', 'Формирование отчёта...');
      await Utils.sleep(300);

      if (!matchedArticle) {
        this.progress.update('validation', 'error', '✗ Статья не определена');
        this._setProcessing(false);
        UIRenderer.renderError(
          'Не удалось квалифицировать деяние',
          'Опишите ситуацию подробнее: укажите способ совершения, объект посягательства, наличие оружия/угроз.'
        );
        return;
      }

      this.progress.update('validation', 'done', '✓ Отчёт сформирован');
      await Utils.sleep(200);

      const aggravating = this.rag.getAggravatingFactors(description);
      this._setProcessing(false);
      UIRenderer.renderResults({
        article: matchedArticle,
        description,
        rawText,
        confidence,
        aggravating,
      });

      this.history.add(description, matchedArticle);
      this.history.render();

    } catch (err) {
      console.error('Analysis error:', err);
      this._setProcessing(false);
      UIRenderer.renderError('Внутренняя ошибка анализа', err.message);
    }
  }

  async qualify() {
    const description = this.input.value.trim();
    if (!description) {
      this.input.style.animation = 'shake 0.4s ease';
      setTimeout(() => this.input.style.animation = '', 400);
      this.input.focus();
      return;
    }

    if (this.isProcessing) return;
    this._setProcessing(true);

    if (!this.rag.apiAvailable) {
      // Fallback to local analysis
      this.progress.update('retrieval', 'active', 'Локальный анализ...');
      await Utils.sleep(400);
      const result = this.rag.matchArticle(description);
      
      if (!result.article) {
        this._setProcessing(false);
        UIRenderer.renderError('API недоступен и локальный анализ не дал результата.');
        return;
      }

      this.progress.update('retrieval', 'done', `✓ Ст. ${result.article.number}`);
      this.progress.update('validation', 'done', '✓ Готово');
      await Utils.sleep(200);
      this._setProcessing(false);
      UIRenderer.renderResults({
        article: result.article,
        description,
        rawText: 'Локальный режим (API недоступен)',
        confidence: result.confidence,
        aggravating: this.rag.getAggravatingFactors(description),
      });
      return;
    }

    try {
      this.progress.update('retrieval', 'active', 'Комплексный RAG-поиск...');
      this.progress.update('llm', 'active', 'llama3.1:8b квалифицирует...');

      const data = await this.llm.qualifyCrime(description);

      this.progress.update('retrieval', 'done', '✓ RAG-поиск выполнен');
      this.progress.update('llm', 'done', '✓ Квалификация получена');
      this.progress.update('validation', 'active', 'Парсинг секций...');
      await Utils.sleep(300);
      this.progress.update('validation', 'done', '✓ Отчёт готов');
      await Utils.sleep(200);

      this._setProcessing(false);
      UIRenderer.renderQualification(data, description);
    } catch (err) {
      console.error('Qualification error:', err);
      this._setProcessing(false);
      UIRenderer.renderError('Ошибка квалификации', `${err.message}\n\nПроверьте эндпоинт /api/crime/qualify.`);
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