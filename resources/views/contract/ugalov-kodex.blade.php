<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="description" content="LegalAI Pro — интеллектуальный анализ УК Таджикистана на базе локальной LLM llama3.1:8b">
<meta name="theme-color" content="#05050a" media="(prefers-color-scheme: dark)">
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<title>LegalAI Pro — Анализ УК РТ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --ease-out-expo: cubic-bezier(0.16, 1, 0.3, 1);
  --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
  --ease-smooth: cubic-bezier(0.4, 0, 0.2, 1);
  --duration-fast: 0.15s;
  --duration-normal: 0.3s;
  --duration-slow: 0.6s;
  --radius-sm: 6px;
  --radius-md: 12px;
  --radius-lg: 18px;
  --radius-xl: 24px;
  --radius-2xl: 32px;
  --radius-full: 9999px;
}

[data-theme="dark"] {
  --bg-app: #05050a;
  --bg-elevated: #0f0f17;
  --bg-sunken: #0a0a12;
  --bg-glass: rgba(15, 15, 23, 0.78);
  --ink-primary: #fafaf9;
  --ink-secondary: #d6d3d1;
  --ink-tertiary: #a8a29e;
  --ink-quaternary: #78716c;
  --line-subtle: rgba(250,250,249,0.06);
  --line-medium: rgba(250,250,249,0.12);
  --line-strong: rgba(250,250,249,0.18);
  --accent-red: #f87171;
  --accent-red-dark: #dc2626;
  --accent-red-glow: rgba(248, 113, 113, 0.5);
  --accent-blue: #60a5fa;
  --accent-purple: #a78bfa;
  --accent-green: #34d399;
  --accent-orange: #fb923c;
  --accent-pink: #f472b6;
  --accent-amber: #fbbf24;
  --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #7f1d1d 100%);
  --gradient-accent: linear-gradient(135deg, #f87171 0%, #dc2626 50%, #991b1b 100%);
  --gradient-subtle: linear-gradient(135deg, rgba(248,113,113,0.08), rgba(167,139,250,0.08));
  --shadow-sm: 0 2px 8px rgba(0,0,0,0.4), 0 1px 2px rgba(0,0,0,0.3);
  --shadow-md: 0 8px 24px -4px rgba(0,0,0,0.5), 0 4px 8px -2px rgba(0,0,0,0.3);
  --shadow-lg: 0 20px 48px -8px rgba(0,0,0,0.6), 0 8px 16px -4px rgba(0,0,0,0.4);
  --shadow-xl: 0 32px 72px -12px rgba(0,0,0,0.7), 0 16px 24px -8px rgba(0,0,0,0.5);
  --shadow-red-glow: 0 0 48px var(--accent-red-glow);
  --noise-opacity: 0.035;
  --code-bg: rgba(0,0,0,0.3);
  --warning-bg: rgba(251,191,36,0.1);
  --warning-border: rgba(251,191,36,0.3);
  --warning-text: #fbbf24;
  --info-bg: rgba(96,165,250,0.1);
  --info-border: rgba(96,165,250,0.3);
  --info-text: #60a5fa;
  --surface-hover: rgba(248,113,113,0.08);
  --surface-active: rgba(248,113,113,0.12);
  --kbd-bg: rgba(0,0,0,0.3);
  --kbd-border: rgba(255,255,255,0.12);
  --scrollbar-thumb: rgba(250,250,249,0.15);
  --scrollbar-thumb-hover: rgba(250,250,249,0.25);
  --hero-grid: rgba(255,255,255,0.04);
  --hero-mask: radial-gradient(ellipse at center, black 40%, transparent 85%);
  --blob-red: rgba(220,38,38,0.7);
  --blob-purple: rgba(167,139,250,0.5);
  --blob-blue: rgba(96,165,250,0.3);
  --crown-grad: linear-gradient(180deg, #fbbf24, #f59e0b);
  --crown-shadow: rgba(251,191,36,0.5);
  --article-border-left: var(--accent-red);
  --article-bg: var(--bg-elevated);
  --punishment-hover-border: rgba(248,113,113,0.3);
  --kb-hover-border: rgba(248,113,113,0.3);
  --success-bg: rgba(52,211,153,0.1);
  --success-border: rgba(52,211,153,0.3);
  --success-text: #34d399;
}

[data-theme="light"] {
  --bg-app: #f8f7f5;
  --bg-elevated: #ffffff;
  --bg-sunken: #f0eeeb;
  --bg-glass: rgba(255, 255, 255, 0.88);
  --ink-primary: #1c1917;
  --ink-secondary: #44403c;
  --ink-tertiary: #78716c;
  --ink-quaternary: #a8a29e;
  --line-subtle: rgba(28,25,23,0.06);
  --line-medium: rgba(28,25,23,0.12);
  --line-strong: rgba(28,25,23,0.2);
  --accent-red: #dc2626;
  --accent-red-dark: #991b1b;
  --accent-red-glow: rgba(220, 38, 38, 0.25);
  --accent-blue: #2563eb;
  --accent-purple: #7c3aed;
  --accent-green: #059669;
  --accent-orange: #d97706;
  --accent-pink: #db2777;
  --accent-amber: #d97706;
  --gradient-hero: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 50%, #fecaca 100%);
  --gradient-accent: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #7f1d1d 100%);
  --gradient-subtle: linear-gradient(135deg, rgba(220,38,38,0.06), rgba(124,58,237,0.06));
  --shadow-sm: 0 2px 8px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
  --shadow-md: 0 8px 24px -4px rgba(0,0,0,0.08), 0 4px 8px -2px rgba(0,0,0,0.04);
  --shadow-lg: 0 20px 48px -8px rgba(0,0,0,0.1), 0 8px 16px -4px rgba(0,0,0,0.06);
  --shadow-xl: 0 32px 72px -12px rgba(0,0,0,0.12), 0 16px 24px -8px rgba(0,0,0,0.08);
  --shadow-red-glow: 0 0 32px var(--accent-red-glow);
  --noise-opacity: 0.02;
  --code-bg: rgba(0,0,0,0.05);
  --warning-bg: rgba(251,191,36,0.12);
  --warning-border: rgba(217,119,6,0.3);
  --warning-text: #b45309;
  --info-bg: rgba(37,99,235,0.08);
  --info-border: rgba(37,99,235,0.25);
  --info-text: #1d4ed8;
  --surface-hover: rgba(220,38,38,0.06);
  --surface-active: rgba(220,38,38,0.1);
  --kbd-bg: rgba(0,0,0,0.05);
  --kbd-border: rgba(0,0,0,0.1);
  --scrollbar-thumb: rgba(28,25,23,0.15);
  --scrollbar-thumb-hover: rgba(28,25,23,0.25);
  --hero-grid: rgba(28,25,23,0.04);
  --hero-mask: radial-gradient(ellipse at center, black 40%, transparent 85%);
  --blob-red: rgba(220,38,38,0.3);
  --blob-purple: rgba(124,58,237,0.2);
  --blob-blue: rgba(37,99,235,0.15);
  --crown-grad: linear-gradient(180deg, #f59e0b, #d97706);
  --crown-shadow: rgba(245,158,11,0.4);
  --article-border-left: var(--accent-red);
  --article-bg: #f0eeeb;
  --punishment-hover-border: rgba(220,38,38,0.2);
  --kb-hover-border: rgba(220,38,38,0.2);
  --success-bg: rgba(5,150,105,0.08);
  --success-border: rgba(5,150,105,0.3);
  --success-text: #059669;
}

* {
  -webkit-tap-highlight-color: transparent;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html { scroll-behavior: smooth; }

body {
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  color: var(--ink-primary);
  background: var(--bg-app);
  min-height: 100vh;
  overflow-x: hidden;
  position: relative;
  letter-spacing: -0.011em;
  transition: background var(--duration-normal) var(--ease-smooth), color var(--duration-normal) var(--ease-smooth);
}

body::before {
  content: '';
  position: fixed;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.5'/%3E%3C/svg%3E");
  opacity: var(--noise-opacity);
  pointer-events: none;
  z-index: 1;
  mix-blend-mode: overlay;
  transition: opacity var(--duration-normal) var(--ease-smooth);
}

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
  filter: blur(120px);
  opacity: 0.5;
  animation: blob-float 35s ease-in-out infinite;
  transition: background var(--duration-slow) var(--ease-smooth);
}
.blob-red {
  width: 700px; height: 700px;
  background: radial-gradient(circle, var(--blob-red) 0%, transparent 70%);
  top: -15%; left: -10%;
  animation-delay: 0s;
}
.blob-purple {
  width: 600px; height: 600px;
  background: radial-gradient(circle, var(--blob-purple) 0%, transparent 70%);
  top: 25%; right: -15%;
  animation-delay: -10s;
}
.blob-blue {
  width: 650px; height: 650px;
  background: radial-gradient(circle, var(--blob-blue) 0%, transparent 70%);
  bottom: -20%; left: 15%;
  animation-delay: -18s;
}
@keyframes blob-float {
  0%, 100% { transform: translate(0, 0) scale(1); }
  33% { transform: translate(100px, -80px) scale(1.15); }
  66% { transform: translate(-60px, 120px) scale(0.92); }
}

.app-shell {
  position: relative;
  z-index: 2;
  padding: 2rem 0;
}

.theme-toggle {
  position: relative;
  width: 44px; height: 24px;
  border-radius: var(--radius-full);
  background: var(--line-medium);
  border: 1px solid var(--line-subtle);
  cursor: pointer;
  display: flex;
  align-items: center;
  padding: 2px;
  transition: background var(--duration-normal) var(--ease-smooth), border-color var(--duration-normal);
  outline: none;
}
.theme-toggle:focus-visible { box-shadow: 0 0 0 3px var(--accent-red-glow); }
.theme-toggle__knob {
  width: 18px; height: 18px;
  border-radius: 50%;
  background: var(--bg-elevated);
  box-shadow: var(--shadow-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  transition: transform var(--duration-normal) var(--ease-spring), background var(--duration-normal);
  transform: translateX(0);
}
[data-theme="dark"] .theme-toggle__knob { transform: translateX(20px); }
[data-theme="light"] .theme-toggle__knob { transform: translateX(0); }

.navbar-glass {
  background: var(--bg-glass);
  backdrop-filter: blur(28px) saturate(180%);
  -webkit-backdrop-filter: blur(28px) saturate(180%);
  border-bottom: 1px solid var(--line-subtle);
  border-radius: var(--radius-lg);
  margin: 1rem auto;
  max-width: 1280px;
  padding: 0.75rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  transition: background var(--duration-normal), border-color var(--duration-normal);
}
.brand-logo {
  font-family: 'Manrope', sans-serif;
  font-weight: 800;
  font-size: 1.3rem;
  letter-spacing: -0.035em;
  background: var(--gradient-accent);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}
.nav-pills {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}
.nav-pill {
  padding: 0.5rem 1rem;
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--ink-secondary);
  text-decoration: none;
  transition: all var(--duration-fast);
  border: 1px solid transparent;
  background: transparent;
  cursor: pointer;
}
.nav-pill:hover {
  color: var(--ink-primary);
  background: var(--gradient-subtle);
  border-color: var(--line-subtle);
}
.nav-pill.active {
  color: var(--accent-red);
  background: var(--surface-active);
  border-color: rgba(220,38,38,0.15);
}

.ollama-status {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.4rem 0.9rem;
  border-radius: var(--radius-full);
  font-size: 0.75rem;
  font-weight: 600;
  font-family: 'JetBrains Mono', monospace;
  letter-spacing: 0.03em;
  border: 1px solid var(--line-medium);
  background: var(--bg-elevated);
  transition: all var(--duration-normal);
  cursor: pointer;
}
.ollama-status:hover { border-color: var(--line-strong); }
.ollama-status.connected {
  color: var(--accent-green);
  border-color: rgba(52,211,153,0.3);
  background: rgba(52,211,153,0.08);
}
.ollama-status.disconnected {
  color: var(--accent-orange);
  border-color: rgba(251,146,60,0.3);
  background: rgba(251,146,60,0.08);
}
.ollama-status.checking {
  color: var(--accent-blue);
  border-color: rgba(96,165,250,0.3);
  background: rgba(96,165,250,0.08);
}
.status-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: currentColor;
  box-shadow: 0 0 8px currentColor;
  animation: pulse-dot 2s infinite;
}
@keyframes pulse-dot {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}

.hero-criminal {
  position: relative;
  overflow: hidden;
  border-radius: var(--radius-2xl);
  padding: 3rem 2.5rem;
  background: var(--gradient-hero);
  color: var(--ink-primary);
  isolation: isolate;
  box-shadow: var(--shadow-xl), inset 0 1px 0 rgba(255,255,255,0.12);
  min-height: 340px;
  display: flex;
  align-items: center;
  transition: background var(--duration-slow);
}
.hero-criminal::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(var(--hero-grid) 1px, transparent 1px),
    linear-gradient(90deg, var(--hero-grid) 1px, transparent 1px);
  background-size: 56px 56px;
  mask-image: var(--hero-mask);
  -webkit-mask-image: var(--hero-mask);
  pointer-events: none;
  z-index: -1;
}
.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.4rem 0.9rem;
  border-radius: var(--radius-full);
  background: rgba(248,113,113,0.15);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(248,113,113,0.25);
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  color: #fecaca;
  margin-bottom: 1.25rem;
}
.hero-title {
  font-family: 'Manrope', sans-serif;
  font-weight: 800;
  font-size: clamp(1.85rem, 4.5vw, 2.8rem);
  line-height: 1.05;
  letter-spacing: -0.04em;
  margin-bottom: 1rem;
  text-wrap: balance;
  color: var(--ink-primary);
}
.hero-title .gradient-word {
  background: linear-gradient(135deg, #fecaca 0%, #f87171 50%, #dc2626 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}
.hero-subtitle {
  font-size: 1rem;
  line-height: 1.6;
  color: var(--ink-secondary);
  max-width: 520px;
  margin-bottom: 1.5rem;
}
.model-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: var(--code-bg);
  backdrop-filter: blur(14px);
  border: 1px solid var(--line-medium);
  border-radius: var(--radius-full);
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--accent-amber);
}

.scales-wrapper {
  position: relative;
  width: 280px; height: 280px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.scales-base {
  position: absolute;
  bottom: 0;
  width: 120px; height: 8px;
  background: linear-gradient(180deg, var(--accent-red), var(--accent-red-dark));
  border-radius: 4px;
  box-shadow: 0 0 20px var(--accent-red-glow);
}
.scales-pillar {
  position: absolute;
  bottom: 8px;
  left: 50%;
  transform: translateX(-50%);
  width: 8px; height: 140px;
  background: linear-gradient(180deg, var(--accent-red), var(--accent-red-dark));
  border-radius: 4px;
  box-shadow: 0 0 15px var(--accent-red-glow);
}
.scales-beam {
  position: absolute;
  top: 60px;
  left: 50%;
  transform: translateX(-50%) rotate(-4deg);
  width: 240px; height: 6px;
  background: linear-gradient(90deg, var(--accent-red-dark), var(--accent-red), var(--accent-red-dark));
  border-radius: 3px;
  transform-origin: center;
  animation: scales-balance 5s ease-in-out infinite;
  box-shadow: 0 0 15px var(--accent-red-glow);
}
@keyframes scales-balance {
  0%, 100% { transform: translateX(-50%) rotate(-4deg); }
  50% { transform: translateX(-50%) rotate(4deg); }
}
.scales-chain {
  position: absolute;
  width: 2px;
  background: linear-gradient(180deg, var(--accent-red), rgba(248,113,113,0.3));
}
.chain-left {
  top: 60px; left: 10px; height: 70px;
  animation: chain-left 5s ease-in-out infinite;
}
.chain-right {
  top: 60px; right: 10px; height: 70px;
  animation: chain-right 5s ease-in-out infinite;
}
@keyframes chain-left {
  0%, 100% { transform: translateY(10px); }
  50% { transform: translateY(-10px); }
}
@keyframes chain-right {
  0%, 100% { transform: translateY(-10px); }
  50% { transform: translateY(10px); }
}
.scales-pan {
  position: absolute;
  top: 125px;
  width: 80px; height: 50px;
  background: linear-gradient(135deg, rgba(220,38,38,0.25), rgba(185,28,28,0.15));
  border: 2px solid var(--accent-red);
  border-radius: 0 0 40px 40px;
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Manrope', sans-serif;
  font-weight: 800;
  font-size: 0.875rem;
  color: var(--accent-red);
  box-shadow: var(--shadow-md);
}
.pan-left { left: -30px; animation: pan-left 5s ease-in-out infinite; }
.pan-right { right: -30px; animation: pan-right 5s ease-in-out infinite; }
@keyframes pan-left {
  0%, 100% { transform: translateY(15px); }
  50% { transform: translateY(-15px); }
}
@keyframes pan-right {
  0%, 100% { transform: translateY(-15px); }
  50% { transform: translateY(15px); }
}
.scales-crown {
  position: absolute;
  top: 25px;
  left: 50%;
  transform: translateX(-50%);
  width: 50px; height: 40px;
  background: var(--crown-grad);
  border-radius: 8px 8px 4px 4px;
  box-shadow: 0 0 20px var(--crown-shadow);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.analysis-panel {
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-xl);
  padding: 2rem;
  margin-top: 2rem;
  box-shadow: var(--shadow-lg);
  position: relative;
  overflow: hidden;
  transition: background var(--duration-normal), border-color var(--duration-normal), box-shadow var(--duration-normal);
}
.analysis-panel::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: var(--gradient-accent);
}
.panel-title {
  font-family: 'Manrope', sans-serif;
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.panel-title::before {
  content: '';
  width: 6px; height: 6px;
  background: var(--accent-red);
  border-radius: 50%;
  box-shadow: 0 0 10px var(--accent-red);
  animation: pulse-dot 2s infinite;
}
.panel-subtitle {
  color: var(--ink-tertiary);
  font-size: 0.875rem;
  margin-bottom: 1.5rem;
}

.scenario-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 0.625rem;
  margin-bottom: 1.25rem;
}
.scenario-chip {
  padding: 0.65rem 0.9rem;
  background: var(--bg-sunken);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  font-size: 0.8125rem;
  font-weight: 500;
  color: var(--ink-secondary);
  cursor: pointer;
  transition: all var(--duration-fast);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  user-select: none;
}
.scenario-chip:hover {
  background: var(--surface-hover);
  border-color: rgba(220,38,38,0.3);
  color: var(--accent-red);
  transform: translateY(-2px);
}
.scenario-chip:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px var(--accent-red-glow);
}
.scenario-chip .emoji { font-size: 1.1rem; }

.crime-textarea {
  width: 100%;
  min-height: 140px;
  padding: 1rem 1.25rem;
  background: var(--bg-sunken);
  border: 1px solid var(--line-medium);
  border-radius: var(--radius-lg);
  color: var(--ink-primary);
  font-family: inherit;
  font-size: 0.9375rem;
  line-height: 1.6;
  resize: vertical;
  transition: all var(--duration-fast);
  outline: none;
}
.crime-textarea:focus {
  border-color: var(--accent-red);
  box-shadow: 0 0 0 4px var(--accent-red-glow);
}
.crime-textarea::placeholder { color: var(--ink-quaternary); }

.textarea-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0.75rem;
  padding: 0 0.25rem;
  flex-wrap: wrap;
  gap: 0.75rem;
}
.char-counter {
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.75rem;
  color: var(--ink-quaternary);
  transition: color var(--duration-fast);
}
.char-counter.warning { color: var(--accent-orange); }
.char-counter.danger { color: var(--accent-red); }

.btn-group {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.analyze-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.625rem;
  padding: 0.95rem 2rem;
  background: var(--gradient-accent);
  color: white;
  border: none;
  border-radius: var(--radius-md);
  font-weight: 700;
  font-size: 0.9375rem;
  letter-spacing: -0.01em;
  cursor: pointer;
  transition: all var(--duration-normal) var(--ease-spring);
  box-shadow: 0 10px 28px rgba(220,38,38,0.4), inset 0 1px 0 rgba(255,255,255,0.2);
  position: relative;
  overflow: hidden;
  outline: none;
}
.analyze-btn::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,0), rgba(255,255,255,0.3), rgba(255,255,255,0));
  transform: translateX(-100%);
  transition: transform 0.7s;
}
.analyze-btn:hover::before { transform: translateX(100%); }
.analyze-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 40px rgba(220,38,38,0.5), inset 0 1px 0 rgba(255,255,255,0.2);
}
.analyze-btn:active { transform: translateY(-1px) scale(0.98); }
.analyze-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none !important;
}
.analyze-btn:focus-visible {
  box-shadow: 0 0 0 4px var(--accent-red-glow), 0 10px 28px rgba(220,38,38,0.4);
}
.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ✅ КРАСИВЫЙ STEP-INDICATOR вместо мусорного streamOutput */
.analysis-progress {
  display: none;
  margin-top: 1.5rem;
  padding: 1.5rem;
  background: var(--bg-sunken);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-lg);
}
.analysis-progress.active { display: block; }

.progress-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.25rem;
  font-family: 'Manrope', sans-serif;
  font-size: 1rem;
  font-weight: 700;
  color: var(--ink-primary);
}
.progress-title::before {
  content: '';
  width: 12px; height: 12px;
  border: 2px solid var(--accent-red);
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.progress-steps {
  display: flex;
  flex-direction: column;
  gap: 0.625rem;
}

.progress-step {
  display: flex;
  align-items: center;
  gap: 0.875rem;
  padding: 0.625rem 0.875rem;
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  color: var(--ink-tertiary);
  transition: all var(--duration-normal);
  opacity: 0.5;
}

.progress-step.active {
  opacity: 1;
  color: var(--ink-primary);
  border-color: rgba(248,113,113,0.3);
  background: var(--surface-active);
  box-shadow: 0 0 0 1px rgba(248,113,113,0.1);
}

.progress-step.done {
  opacity: 1;
  color: var(--accent-green);
  border-color: rgba(52,211,153,0.3);
  background: var(--success-bg);
}

.step-icon {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: var(--bg-sunken);
  border: 1px solid var(--line-medium);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  flex-shrink: 0;
  transition: all var(--duration-normal);
}

.progress-step.active .step-icon {
  background: var(--gradient-accent);
  border-color: var(--accent-red);
  color: white;
  box-shadow: 0 0 12px var(--accent-red-glow);
}

.progress-step.done .step-icon {
  background: var(--accent-green);
  border-color: var(--accent-green);
  color: white;
}

.step-label { flex: 1; font-weight: 500; }

.step-detail {
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.75rem;
  color: var(--ink-quaternary);
}

.progress-step.active .step-detail { color: var(--accent-red); }

.results-container {
  display: none;
  margin-top: 2rem;
  animation: fade-up 0.6s var(--ease-out-expo);
}
.results-container.active { display: block; }
@keyframes fade-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.result-section {
  background: var(--bg-sunken);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-lg);
  padding: 1.5rem;
  margin-bottom: 1.25rem;
  position: relative;
  overflow: hidden;
  transition: background var(--duration-normal), border-color var(--duration-normal);
}
.result-section.verdict {
  background: linear-gradient(135deg, rgba(220,38,38,0.08), rgba(185,28,28,0.04));
  border-color: rgba(248,113,113,0.3);
}
.result-section.verdict::before {
  content: '';
  position: absolute;
  left: 0; top: 0; bottom: 0;
  width: 4px;
  background: var(--gradient-accent);
}
.result-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}
.result-icon {
  width: 40px; height: 40px;
  border-radius: var(--radius-md);
  background: linear-gradient(135deg, rgba(248,113,113,0.2), rgba(220,38,38,0.1));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  box-shadow: 0 0 15px rgba(248,113,113,0.2);
  flex-shrink: 0;
}
.result-title {
  font-family: 'Manrope', sans-serif;
  font-weight: 700;
  font-size: 1rem;
  letter-spacing: -0.015em;
  color: var(--ink-primary);
}
.result-label {
  font-size: 0.6875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--accent-red);
  font-family: 'JetBrains Mono', monospace;
}
.article-code {
  display: inline-block;
  padding: 0.4rem 0.85rem;
  background: rgba(248,113,113,0.15);
  border: 1px solid rgba(248,113,113,0.3);
  border-radius: var(--radius-md);
  font-family: 'JetBrains Mono', monospace;
  font-weight: 700;
  font-size: 1.125rem;
  color: var(--accent-red);
  margin-right: 0.5rem;
  box-shadow: 0 0 12px rgba(248,113,113,0.2);
}
.article-name {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--ink-primary);
}
.article-description {
  color: var(--ink-tertiary);
  font-size: 0.9rem;
  line-height: 1.6;
  margin-top: 0.75rem;
  padding: 0.875rem;
  background: var(--article-bg);
  border-radius: var(--radius-md);
  border-left: 3px solid var(--article-border-left);
  transition: background var(--duration-normal);
  white-space: pre-wrap;
}
.punishment-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 0.75rem;
  margin-top: 1rem;
}
.punishment-card {
  padding: 1rem;
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  transition: all var(--duration-fast);
}
.punishment-card:hover {
  border-color: var(--punishment-hover-border);
  transform: translateY(-2px);
  box-shadow: var(--shadow-sm);
}
.punishment-type {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--ink-tertiary);
  margin-bottom: 0.375rem;
}
.punishment-value {
  font-family: 'Manrope', sans-serif;
  font-weight: 800;
  font-size: 1.15rem;
  color: var(--accent-red);
  line-height: 1.2;
}
.punishment-note {
  font-size: 0.75rem;
  color: var(--ink-quaternary);
  margin-top: 0.25rem;
}

.action-list {
  list-style: none;
  padding: 0;
  margin: 0;
  counter-reset: action-counter;
}
.action-list li {
  position: relative;
  padding: 0.875rem 0 0.875rem 3rem;
  border-bottom: 1px solid var(--line-subtle);
  counter-increment: action-counter;
  transition: border-color var(--duration-fast);
}
.action-list li:last-child { border-bottom: none; }
.action-list li::before {
  content: counter(action-counter);
  position: absolute;
  left: 0;
  top: 0.875rem;
  width: 32px; height: 32px;
  background: var(--gradient-accent);
  color: white;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'JetBrains Mono', monospace;
  font-weight: 700;
  font-size: 0.8125rem;
  box-shadow: var(--shadow-sm);
  flex-shrink: 0;
}
.action-list li strong {
  display: block;
  font-weight: 700;
  color: var(--ink-primary);
  margin-bottom: 0.25rem;
}
.action-list li span {
  color: var(--ink-tertiary);
  font-size: 0.875rem;
  line-height: 1.5;
}

.knowledge-section { margin-top: 3rem; }
.kb-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1rem;
  margin-top: 1.5rem;
}
.kb-card {
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-lg);
  padding: 1.5rem;
  cursor: pointer;
  transition: all var(--duration-normal) var(--ease-out-expo);
  position: relative;
  overflow: hidden;
  outline: none;
  text-align: left;
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
  border-color: var(--kb-hover-border);
  box-shadow: var(--shadow-lg);
}
.kb-card:hover::before { opacity: 1; }
.kb-card:focus-visible {
  box-shadow: 0 0 0 3px var(--accent-red-glow), var(--shadow-lg);
}
.kb-article-num {
  font-family: 'JetBrains Mono', monospace;
  font-size: 2rem;
  font-weight: 800;
  color: var(--accent-red);
  line-height: 1;
  margin-bottom: 0.5rem;
  text-shadow: 0 0 20px rgba(248,113,113,0.3);
}
.kb-article-name {
  font-family: 'Manrope', sans-serif;
  font-weight: 700;
  font-size: 1.05rem;
  color: var(--ink-primary);
  margin-bottom: 0.5rem;
}
.kb-article-desc {
  color: var(--ink-tertiary);
  font-size: 0.8125rem;
  line-height: 1.55;
  margin-bottom: 0.875rem;
}
.kb-sentence {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.35rem 0.7rem;
  background: rgba(248,113,113,0.1);
  border: 1px solid rgba(248,113,113,0.2);
  border-radius: var(--radius-full);
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--accent-red);
}

.alert-premium {
  padding: 1rem 1.25rem;
  border-radius: var(--radius-md);
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  font-size: 0.875rem;
  line-height: 1.5;
  margin-bottom: 1rem;
  transition: background var(--duration-normal), border-color var(--duration-normal);
}
.alert-warning {
  background: var(--warning-bg);
  border: 1px solid var(--warning-border);
  color: var(--warning-text);
}
.alert-icon { font-size: 1.25rem; flex-shrink: 0; }

.page-footer {
  margin-top: 4rem;
  padding: 2rem;
  text-align: center;
  color: var(--ink-quaternary);
  font-size: 0.8125rem;
  border-top: 1px solid var(--line-subtle);
  transition: border-color var(--duration-normal);
}
.page-footer a {
  color: var(--accent-red);
  text-decoration: none;
  font-weight: 600;
}
.page-footer a:hover { text-decoration: underline; }

::-webkit-scrollbar { width: 9px; height: 9px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--scrollbar-thumb); border-radius: var(--radius-full); }
::-webkit-scrollbar-thumb:hover { background: var(--scrollbar-thumb-hover); }

.copy-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 1rem;
  background: var(--bg-elevated);
  border: 1px solid var(--line-medium);
  border-radius: var(--radius-md);
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--ink-secondary);
  cursor: pointer;
  transition: all var(--duration-fast);
}
.copy-btn:hover {
  background: var(--surface-hover);
  border-color: var(--accent-red);
  color: var(--accent-red);
}
.copy-btn.copied {
  background: var(--success-bg);
  border-color: var(--success-border);
  color: var(--success-text);
}

.confidence-meter {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.75rem;
  padding: 0.5rem 0.75rem;
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  font-size: 0.8125rem;
}
.confidence-bar {
  flex: 1;
  height: 6px;
  background: var(--line-subtle);
  border-radius: 3px;
  overflow: hidden;
}
.confidence-fill {
  height: 100%;
  background: var(--gradient-accent);
  border-radius: 3px;
  transition: width var(--duration-slow) var(--ease-out-expo);
}
.confidence-label {
  font-weight: 600;
  color: var(--accent-red);
  font-family: 'JetBrains Mono', monospace;
}

.toast {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  padding: 1rem 1.5rem;
  background: var(--bg-elevated);
  border: 1px solid var(--line-medium);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-lg);
  z-index: 1000;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  animation: toast-slide-in 0.3s var(--ease-out-expo);
  max-width: 400px;
}
.toast.success { border-color: var(--success-border); }
.toast.error { border-color: var(--warning-border); }
@keyframes toast-slide-in {
  from { transform: translateY(100px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.history-panel {
  margin-top: 1.5rem;
  padding: 1rem;
  background: var(--bg-sunken);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-lg);
}
.history-item {
  padding: 0.75rem;
  margin-bottom: 0.5rem;
  background: var(--bg-elevated);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--duration-fast);
  font-size: 0.875rem;
}
.history-item:hover {
  border-color: var(--accent-red);
  background: var(--surface-hover);
}
.history-item:last-child { margin-bottom: 0; }
.history-time {
  font-size: 0.75rem;
  color: var(--ink-quaternary);
  font-family: 'JetBrains Mono', monospace;
}

details.raw-response {
  margin-top: 1rem;
  background: var(--bg-sunken);
  border: 1px solid var(--line-subtle);
  border-radius: var(--radius-md);
  padding: 1rem;
  transition: background var(--duration-normal), border-color var(--duration-normal);
}
details.raw-response > summary {
  cursor: pointer;
  font-weight: 600;
  color: var(--ink-secondary);
  font-size: 0.875rem;
  list-style: none;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
details.raw-response > summary::-webkit-details-marker { display: none; }
details.raw-response > summary::before {
  content: '▶';
  font-size: 0.7rem;
  transition: transform var(--duration-fast);
  display: inline-block;
}
details.raw-response[open] > summary::before { transform: rotate(90deg); }

pre.raw-pre {
  margin-top: 1rem;
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.8125rem;
  line-height: 1.6;
  color: var(--ink-tertiary);
  white-space: pre-wrap;
  word-break: break-word;
}

.rag-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.3rem 0.7rem;
  background: var(--surface-active);
  border: 1px solid rgba(220,38,38,0.15);
  border-radius: var(--radius-full);
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.7rem;
  font-weight: 600;
  color: var(--accent-red);
  margin-bottom: 1rem;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-6px); }
  75% { transform: translateX(6px); }
}

@media (max-width: 768px) {
  .hero-criminal { padding: 2rem 1.5rem; }
  .scales-wrapper { width: 200px; height: 200px; transform: scale(0.8); }
  .analysis-panel { padding: 1.5rem; }
  .scenario-grid { grid-template-columns: 1fr 1fr; }
  .punishment-grid { grid-template-columns: 1fr; }
  .kb-grid { grid-template-columns: 1fr; }
  .navbar-glass { padding: 0.75rem 1rem; }
  .nav-pills { width: 100%; justify-content: flex-start; }
  .btn-group { width: 100%; }
  .analyze-btn { flex: 1; justify-content: center; }
}

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>
</head>
<body>

<div class="ambient-bg" aria-hidden="true">
  <div class="ambient-blob blob-red"></div>
  <div class="ambient-blob blob-purple"></div>
  <div class="ambient-blob blob-blue"></div>
</div>

<div class="app-shell">
  <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 1rem;">

    <nav class="navbar-glass" role="navigation" aria-label="Главное меню">
      <a href="/" class="brand-logo" aria-label="LegalAI Pro — на главную">
        <span aria-hidden="true">⚖️</span> LegalAI Pro
      </a>
      <div class="nav-pills">
        <a href="/" class="nav-pill">← На главную</a>
        <span class="nav-pill active" aria-current="page">УК РТ</span>
        <div id="ollamaStatus" class="ollama-status checking" aria-live="polite" title="Нажмите для перепроверки">
          <span class="status-dot" aria-hidden="true"></span>
          <span>Проверка Ollama...</span>
        </div>
        <button
          id="themeToggle"
          class="theme-toggle"
          aria-label="Переключить тему"
          title="Переключить тему (Ctrl/Cmd + Shift + L)"
          type="button"
        >
          <span class="theme-toggle__knob" id="themeKnob" aria-hidden="true">🌙</span>
        </button>
      </div>
    </nav>

    <section class="hero-criminal" aria-label="Главный баннер">
      <div class="row" style="display: flex; flex-wrap: wrap; align-items: center; gap: 2rem; width: 100%; position: relative; z-index: 2;">
        <div style="flex: 1 1 55%; min-width: 280px;">
          <div class="hero-eyebrow">Уголовный кодекс Республики Таджикистан</div>
          <h1 class="hero-title">
            Квалификация преступления<br>
            <span class="gradient-word">с ИИ-анализом УК РТ</span>
          </h1>
          <p class="hero-subtitle">
            Опишите ситуацию — локальная модель <strong style="color: var(--accent-amber);">llama3.1:8b</strong> определит статью УК Таджикистана, реальные сроки лишения свободы и пошаговый алгоритм действий для потерпевшего.
          </p>
          <div class="model-badge">
            <span aria-hidden="true">🦙</span> llama3.1:8b • Локальная LLM • Без отправки данных
          </div>
        </div>
        <div style="flex: 1 1 35%; min-width: 200px; display: flex; justify-content: center;">
          <div class="scales-wrapper" aria-hidden="true">
            <div class="scales-crown">⚖</div>
            <div class="scales-pillar"></div>
            <div class="scales-beam"></div>
            <div class="scales-chain chain-left"></div>
            <div class="scales-chain chain-right"></div>
            <div class="scales-pan pan-left">Вина</div>
            <div class="scales-pan pan-right">Закон</div>
            <div class="scales-base"></div>
          </div>
        </div>
      </div>
    </section>

    <section class="analysis-panel" aria-labelledby="analysis-heading">
      <div class="rag-badge" aria-label="Источник данных">
        <span>🔒</span> Только статьи из RAG-базы УК РТ (ст. 104, 111, 114, 246–250)
      </div>
      <div class="panel-title" id="analysis-heading">🔍 Опишите преступление</div>
      <div class="panel-subtitle">Чем подробнее описание — тем точнее квалификация по статьям УК РТ</div>

      <div id="ollamaWarning" class="alert-premium alert-warning" style="display: none;" role="alert">
        <span class="alert-icon" aria-hidden="true">⚠️</span>
        <div>
          <strong>Python RAG API недоступен.</strong>
          Запустите <code style="background: var(--code-bg); padding: 0.15rem 0.5rem; border-radius: 4px; margin: 0 0.25rem; font-family: 'JetBrains Mono', monospace;">python app.py</code>
          для работы с llama3.1:8b.
          <br><small>Пока используется локальная база знаний УК РТ.</small>
        </div>
      </div>

      <div style="margin-bottom: 0.75rem;">
        <div style="font-size: 0.8125rem; font-weight: 600; color: var(--ink-tertiary); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.5rem;">⚡ Быстрые сценарии</div>
        <div class="scenario-grid" id="scenarioGrid" role="list"></div>
      </div>

      <label for="crimeDescription" class="visually-hidden" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;">Описание преступления</label>
      <textarea
        id="crimeDescription"
        class="crime-textarea"
        placeholder="Например: Вчера вечером на улице Рудаки неизвестный мужчина вырвал у меня сумку из рук и убежал. В сумке был телефон, документы и 2000 сомони..."
        maxlength="2000"
        aria-describedby="charCounter"
      ></textarea>

      <div class="textarea-footer">
        <span class="char-counter" id="charCounter" aria-live="polite">0 / 2000 символов</span>
        <div class="btn-group">
          <button onclick="App.analyze()" id="analyzeBtn" class="analyze-btn" type="button" title="Быстрый анализ через RAG (Ctrl+Enter)">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Анализ</span>
            <kbd style="background: var(--kbd-bg); border: 1px solid var(--kbd-border); border-radius: 4px; padding: 0.1rem 0.35rem; font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; opacity: 0.8;">Ctrl+Enter</kbd>
          </button>
          <button onclick="App.qualifyCrime()" id="qualifyBtn" class="analyze-btn" type="button" style="background: linear-gradient(135deg, #a78bfa, #9333ea, #7c3aed);" title="Полный анализ с llama3.1:8b">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Квалификация ИИ</span>
          </button>
        </div>
      </div>

      <!-- ✅ КРАСИВЫЙ ПРОГРЕСС-ИНДИКАТОР -->
      <div id="analysisProgress" class="analysis-progress" aria-live="polite">
        <div class="progress-title">
          <span>LegalAI Pro анализирует преступление...</span>
        </div>
        <div class="progress-steps">
          <div class="progress-step" data-step="rag">
            <div class="step-icon">1</div>
            <div class="step-label">Поиск по базе УК РТ</div>
            <div class="step-detail" data-detail="rag">—</div>
          </div>
          <div class="progress-step" data-step="llm">
            <div class="step-icon">2</div>
            <div class="step-label">Анализ llama3.1:8b</div>
            <div class="step-detail" data-detail="llm">—</div>
          </div>
          <div class="progress-step" data-step="render">
            <div class="step-icon">3</div>
            <div class="step-label">Формирование ответа</div>
            <div class="step-detail" data-detail="render">—</div>
          </div>
        </div>
      </div>

      <div id="resultsContainer" class="results-container" aria-live="polite"></div>

      <div id="historyPanel" class="history-panel" style="display: none;">
        <div style="font-size: 0.8125rem; font-weight: 600; color: var(--ink-tertiary); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.75rem;">📜 История анализов</div>
        <div id="historyList"></div>
      </div>
    </section>

    <section class="knowledge-section" aria-labelledby="kb-heading">
      <div class="panel-title" id="kb-heading">📚 База знаний УК РТ</div>
      <div class="panel-subtitle">Основные статьи по преступлениям против собственности и личности. Кликните по карточке — текст подставится в форму.</div>
      <div class="kb-grid" id="kbGrid" role="list"></div>
    </section>

    <footer class="page-footer">
      <div>⚖️ LegalAI Pro • Анализ УК РТ на базе <a href="https://ollama.com" target="_blank" rel="noopener noreferrer">Ollama + llama3.1:8b</a></div>
      <div style="margin-top: 0.5rem;">Информация носит ознакомительный характер. Для точной консультации обратитесь к адвокату.</div>
    </footer>

  </div>
</div>

<script>
const CONFIG = Object.freeze({
  PYTHON_API_URL: 'http://127.0.0.1:5000',
  MODEL: 'llama3.1:8b',
  MAX_CHARS: 2000,
  RETRY_ATTEMPTS: 3,
  RETRY_DELAY: 1000,
});

const LOCAL_FALLBACK_DB = {
  '247': { number: '247', name: 'Кража', description: 'Тайное хищение чужого имущества. Преступник действует незаметно для потерпевшего и окружающих.', sentences: [ { type: 'Основное', value: 'До 3 лет', note: 'ч. 1 — без отягчающих' }, { type: 'Группой лиц', value: 'До 5 лет', note: 'ч. 2 — предварительный сговор' }, { type: 'Крупный размер', value: '5–8 лет', note: 'ч. 3 — с проникновением' }, { type: 'Особо крупный', value: '7–12 лет', note: 'ч. 4 — организованной группой' } ] },
  '248': { number: '248', name: 'Грабёж', description: 'Открытое хищение чужого имущества. Совершается в присутствии потерпевшего.', sentences: [ { type: 'Штраф', value: '500–1000 ПР', note: 'показателей для расчётов' }, { type: 'Основное', value: 'До 5 лет', note: 'ч. 1 — открытое хищение' }, { type: 'Группой лиц', value: '5–10 лет', note: 'ч. 2 — с насилием не опасным' }, { type: 'С оружием', value: '8–15 лет', note: 'ч. 3 — организованная группа' } ] },
  '249': { number: '249', name: 'Разбой', description: 'Нападение в целях хищения чужого имущества, совершённое с применением насилия, опасного для жизни или здоровья.', sentences: [ { type: 'Основное', value: '3–7 лет', note: 'ч. 1 — нападение' }, { type: 'С оружием', value: '7–12 лет', note: 'ч. 2 — группой лиц' }, { type: 'Крупный размер', value: '10–15 лет', note: 'ч. 3 — причинение тяжкого вреда' }, { type: 'Особо опасный', value: '12–20 лет', note: 'ч. 4 — организованная группа' } ] },
  '250': { number: '250', name: 'Вымогательство', description: 'Требование передачи чужого имущества или права на имущество под угрозой применения насилия.', sentences: [ { type: 'Основное', value: 'До 4 лет', note: 'ч. 1 — требование' }, { type: 'Группой лиц', value: '3–7 лет', note: 'ч. 2 — неоднократно' }, { type: 'С насилием', value: '7–12 лет', note: 'ч. 3 — крупный размер' }, { type: 'Особо крупный', value: '10–15 лет', note: 'ч. 4 — организованная группа' } ] },
  '246': { number: '246', name: 'Мошенничество', description: 'Хищение чужого имущества или приобретение права на имущество путём обмана или злоупотребления доверием.', sentences: [ { type: 'Основное', value: 'До 3 лет', note: 'ч. 1 — обман' }, { type: 'Группой лиц', value: 'До 6 лет', note: 'ч. 2 — повторно' }, { type: 'Крупный размер', value: '5–10 лет', note: 'ч. 3 — служебное положение' }, { type: 'Особо крупный', value: '8–12 лет', note: 'ч. 4 — организованная группа' } ] },
  '104': { number: '104', name: 'Умышленное убийство', description: 'Умышленное причинение смерти другому человеку.', sentences: [ { type: 'Основное', value: '5–15 лет', note: 'ч. 1' }, { type: 'Двух и более', value: '10–20 лет', note: 'ч. 2 — отягчающие' }, { type: 'Особо тяжкое', value: '15–25 лет', note: 'или пожизненно' } ] },
  '111': { number: '111', name: 'Умышленное причинение тяжкого вреда здоровью', description: 'Умышленное причинение тяжкого вреда здоровью, опасного для жизни человека.', sentences: [ { type: 'Основное', value: 'До 5 лет', note: 'ч. 1' }, { type: 'Группой лиц', value: '3–8 лет', note: 'ч. 2 — отягчающие' }, { type: 'По неосторожности', value: '5–10 лет', note: 'ч. 3 — смерть потерпевшего' } ] },
  '114': { number: '114', name: 'Умышленное причинение средней тяжести вреда здоровью', description: 'Умышленное причинение вреда здоровью, не опасного для жизни, повлёкшего длительное расстройство здоровья.', sentences: [ { type: 'Основное', value: 'До 2 лет', note: 'ч. 1' }, { type: 'Группой лиц', value: 'До 4 лет', note: 'ч. 2 — отягчающие' } ] }
};

let RAG_DB = JSON.parse(JSON.stringify(LOCAL_FALLBACK_DB));

const SCENARIOS = Object.freeze({
  theft: 'Сегодня в метро в час пик кто-то вытащил у меня из кармана куртки телефон iPhone 15. Я заметил пропажу только когда вышел из вагона.',
  robbery: 'Вчера вечером на улице Рудаки в Душанбе неизвестный мужчина подбежал ко мне, вырвал сумку из рук и убежал. В сумке были документы, телефон и 3000 сомони.',
  assault: 'На меня напали двое неизвестных в тёмном переулке. Один держал нож и угрожал убить, второй забрал кошелёк и часы. Я получил ножевое ранение в руку.',
  extortion: 'Ко мне подошли трое мужчин спортивного телосложения и потребовали 10000 долларов, угрожая расправой над моей семьёй, если я не принесу деньги завтра.',
  fraud: 'Мне позвонили из "банка" и сказали, что мой счёт заблокирован. Под диктовку мошенника я перевёл все свои сбережения (50000 сомони) на "безопасный счёт".',
  battery: 'В баре возник конфликт. Мужчина ударил меня кулаком по лицу три раза. Свидетели есть, зафиксированы синяки и ссадины, есть справка из травмпункта.'
});

const ACTIONS_TEMPLATE = Object.freeze([
  { step: 'Заявление в РОВД', detail: 'Немедленно обратитесь в ближайший отдел милиции по месту совершения преступления. Подайте письменное заявление (арза).' },
  { step: 'Талон-уведомление', detail: 'Дежурный обязан выдать талон-уведомление с номером КУСП — это доказательство регистрации обращения.' },
  { step: 'Сбор доказательств', detail: 'Сохраните контакты свидетелей, запросите записи с камер видеонаблюдения (хранятся 7–30 дней), сделайте фото повреждений.' },
  { step: 'Медицинское освидетельствование', detail: 'При травмах — снимите побои в травмпункте. Справка будет ключевым доказательством.' },
  { step: 'Следственные действия', detail: 'Следователь проведёт проверку, возбудит уголовное дело, проведёт очные ставки и опознание.' },
  { step: 'Суд и приговор', detail: 'После завершения следствия дело передаётся в суд. Преступник будет этапирован в исправительную колонию.' }
]);

class ThemeManager {
  constructor() {
    this.root = document.documentElement;
    this.btn = document.getElementById('themeToggle');
    this.knob = document.getElementById('themeKnob');
    this.prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
    this.init();
  }
  init() {
    const saved = localStorage.getItem('legalai-theme');
    const initial = saved || (this.prefersDark.matches ? 'dark' : 'light');
    this.set(initial);
    this.btn.addEventListener('click', () => this.toggle());
    this.prefersDark.addEventListener('change', (e) => {
      if (!localStorage.getItem('legalai-theme')) this.set(e.matches ? 'dark' : 'light');
    });
    document.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key.toLowerCase() === 'l') {
        e.preventDefault();
        this.toggle();
      }
    });
  }
  set(theme) {
    this.root.setAttribute('data-theme', theme);
    this.knob.textContent = theme === 'dark' ? '🌙' : '☀️';
    this.btn.setAttribute('aria-pressed', theme === 'dark');
    localStorage.setItem('legalai-theme', theme);
  }
  toggle() {
    const current = this.root.getAttribute('data-theme');
    this.set(current === 'dark' ? 'light' : 'dark');
  }
}

class OllamaService {
  constructor() {
    this.connected = false;
    this.statusEl = document.getElementById('ollamaStatus');
    this.warningEl = document.getElementById('ollamaWarning');
    this.statusEl.addEventListener('click', () => this.check());
  }

  async check() {
    try {
      this._setStatus('checking', 'Проверка...');
      const resp = await fetch(`${CONFIG.PYTHON_API_URL}/api/rag/status`, { 
        method: 'GET', mode: 'cors', signal: AbortSignal.timeout(5000) 
      });
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      const data = await resp.json();
      if (!data.ready) throw new Error(data.error || 'RAG не готов');
      this.connected = true;
      this._setStatus('connected', `✓ RAG готов: ${data.chunks || 0} чанков`);
      this.warningEl.style.display = 'none';
      return true;
    } catch (err) {
      this.connected = false;
      this._setStatus('disconnected', '✗ RAG недоступен (локальный режим)');
      this.warningEl.style.display = 'flex';
      return false;
    }
  }

  _setStatus(cls, text) {
    this.statusEl.className = `ollama-status ${cls}`;
    this.statusEl.innerHTML = `<span class="status-dot" aria-hidden="true"></span><span>${text}</span>`;
  }

  async fetchWithRetry(url, options, attempts = CONFIG.RETRY_ATTEMPTS) {
    for (let i = 0; i < attempts; i++) {
      try {
        const resp = await fetch(url, options);
        if (resp.ok) return resp;
        throw new Error(`HTTP ${resp.status}`);
      } catch (err) {
        if (i === attempts - 1) throw err;
        await new Promise(r => setTimeout(r, CONFIG.RETRY_DELAY * (i + 1)));
      }
    }
  }

  async searchRag(query, topK = 8) {
    const resp = await this.fetchWithRetry(`${CONFIG.PYTHON_API_URL}/api/rag/search`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ query, top_k: topK })
    });
    const data = await resp.json();
    return Array.isArray(data.results) ? data.results : [];
  }

  async *streamChat(message) {
    const resp = await fetch(`${CONFIG.PYTHON_API_URL}/api/chat`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/x-ndjson' },
      body: JSON.stringify({
        message, model: CONFIG.MODEL, personality: 'professional',
        temperature: 0.1, history: []
      })
    });
    if (!resp.ok || !resp.body) throw new Error('Stream недоступен');
    const reader = resp.body.getReader();
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
          const obj = JSON.parse(line);
          if (obj.response) yield obj.response;
          if (obj.done) break;
        } catch {}
      }
    }
  }

  async loadArticles() {
    try {
      const resp = await this.fetchWithRetry(`${CONFIG.PYTHON_API_URL}/api/rag/articles`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({}),
        signal: AbortSignal.timeout(10000)
      });
      const data = await resp.json();
      if (!Array.isArray(data.articles) || data.articles.length === 0) return false;
      const newDB = {};
      for (const article of data.articles) {
        if (article.number) {
          const localArticle = LOCAL_FALLBACK_DB[article.number];
          newDB[article.number] = {
            number: article.number,
            name: article.title || `Статья ${article.number}`,
            description: article.description || article.text?.substring(0, 200) || (localArticle?.description || ''),
            sentences: Array.isArray(article.sentences) && article.sentences.length > 0
              ? article.sentences : (localArticle?.sentences || [])
          };
        }
      }
      RAG_DB = newDB;
      return true;
    } catch { return false; }
  }

  async setSource(filename) {
    try {
      const resp = await this.fetchWithRetry(`${CONFIG.PYTHON_API_URL}/api/rag/set-source`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ filename }),
        signal: AbortSignal.timeout(5000)
      });
      const data = await resp.json();
      return !!data.success;
    } catch { return false; }
  }
}

class RAGMatcher {
  static match(text) {
    const lower = text.toLowerCase();
    const keywords = [
      { keys: ['убийств', 'убил', 'смерть', 'зарезал', 'застрелил'], num: '104' },
      { keys: ['ножев', 'ранен', 'тяжк.*вред', 'опасн.*жизн', 'потерял сознани', 'кровотечен'], num: '111' },
      { keys: ['ударил', 'побо', 'синяк', 'ссадин', 'кулаком', 'сломал нос'], num: '114' },
      { keys: ['мошен', 'обман', 'перевёл', 'позвонили.*банк', 'безопасный счёт', 'фишинг', 'подделка'], num: '246' },
      { keys: ['карман', 'тайно', 'вытащил', 'незаметно', 'украли телефон', 'пропаж', 'взлом', 'двер', 'окн'], num: '247' },
      { keys: ['вырвал', 'открыто', 'подбежал', 'сумку из рук', 'грабёж', 'грабеж', 'сорвал', 'выхватил'], num: '248' },
      { keys: ['нож', 'оруж', 'пистолет', 'напали', 'угрожал уб', 'ранен.*при.*ограблен'], num: '249' },
      { keys: ['вымог', 'потребовал.*деньг', 'угрожа.*расправ', 'шантаж', 'встречу.*деньг'], num: '250' }
    ];
    for (const { keys, num } of keywords) {
      for (const pattern of keys) {
        if (new RegExp(pattern).test(lower)) return num;
      }
    }
    return null;
  }

  static matchFromRag(results) {
    if (!Array.isArray(results) || results.length === 0) return null;
    const knownNumbers = Object.keys(RAG_DB);
    if (knownNumbers.length === 0) return null;
    const articleRegexes = Object.fromEntries(knownNumbers.map(num => [
      num,
      new RegExp(`(?:ст\\.?|статья|моддаи|article)\\s*${num}\\b|\\b${num}\\b`, 'i')
    ]));
    for (const item of results) {
      const haystack = `${item.title || ''}\n${item.text || ''}`;
      for (const num of knownNumbers) {
        if (articleRegexes[num].test(haystack)) return num;
      }
    }
    return null;
  }

  static getAggravating(text) {
    const lower = text.toLowerCase();
    const facts = [];
    if (/нож|оруж|пистолет/.test(lower)) facts.push('применение оружия');
    if (/дво|тро|групп|несколько человек/.test(lower)) facts.push('группа лиц');
    if (/крупн|много денег|дорог|особо крупн/.test(lower)) facts.push('крупный размер');
    if (/ранен|травм|удар|побо|синяк/.test(lower)) facts.push('вред здоровью');
    if (/повторн|неоднократн|ранее судим/.test(lower)) facts.push('рецидив');
    return facts.length === 0 
      ? 'Стандартная квалификация без отягчающих обстоятельств.'
      : facts.join(', ') + ' — срок увеличивается.';
  }
}

class UIRenderer {
  static renderScenarios() {
    const grid = document.getElementById('scenarioGrid');
    const icons = { theft: '🕵️', robbery: '🏃', assault: '🔪', extortion: '😰', fraud: '🎭', battery: '👊' };
    const labels = { theft: 'Карманная кража', robbery: 'Уличное ограбление', assault: 'Нападение с ножом', extortion: 'Вымогательство', fraud: 'Мошенничество', battery: 'Побои' };
    grid.innerHTML = Object.entries(SCENARIOS).map(([key]) => `
      <button class="scenario-chip" onclick="App.fillScenario('${key}')" type="button" role="listitem">
        <span class="emoji" aria-hidden="true">${icons[key]}</span> ${labels[key]}
      </button>
    `).join('');
  }

  static renderKnowledgeBase() {
    const grid = document.getElementById('kbGrid');
    const articles = Object.values(RAG_DB);
    if (articles.length === 0) {
      grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--ink-tertiary); padding: 2rem;">База знаний пуста</div>';
      return;
    }
    grid.innerHTML = articles.map(article => {
      const minSentence = (article.sentences && article.sentences[0]?.value) || '—';
      return `
        <button class="kb-card" onclick="App.fillArticle('${article.number}')" type="button" role="listitem">
          <div class="kb-article-num">${article.number}</div>
          <div class="kb-article-name">${App.escapeHtml(article.name)}</div>
          <div class="kb-article-desc">${App.escapeHtml(article.description)}</div>
          <div class="kb-sentence">⏱ ${App.escapeHtml(minSentence)}</div>
        </button>
      `;
    }).join('');
  }

  static renderResults(article, description, rawText, confidence = 'high') {
    if (!article) {
      App.showError('Не удалось определить статью УК РТ');
      return;
    }
    const container = document.getElementById('resultsContainer');
    const confidencePercent = confidence === 'high' ? 95 : confidence === 'medium' ? 70 : 45;
    
    container.innerHTML = `
      <div class="result-section verdict">
        <div class="result-header">
          <div class="result-icon" aria-hidden="true">⚖️</div>
          <div style="flex: 1;">
            <div class="result-label">Квалификация деяния</div>
            <div class="result-title">${App.escapeHtml(article.name)}</div>
          </div>
          <button class="copy-btn" onclick="App.copyResults()" aria-label="Скопировать результаты">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Копировать
          </button>
        </div>
        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
          <span class="article-code">Ст. ${article.number}</span>
          <span class="article-name">${App.escapeHtml(article.name)}</span>
        </div>
        <div class="article-description">${App.escapeHtml(article.description)}</div>
        <div class="confidence-meter">
          <span style="color: var(--ink-tertiary); font-size: 0.8125rem;">Уверенность ИИ:</span>
          <div class="confidence-bar">
            <div class="confidence-fill" style="width: ${confidencePercent}%"></div>
          </div>
          <span class="confidence-label">${confidencePercent}%</span>
        </div>
      </div>

      <div class="result-section">
        <div class="result-header">
          <div class="result-icon" aria-hidden="true">⏱️</div>
          <div>
            <div class="result-label">Наказание для преступника</div>
            <div class="result-title">Реальные сроки по УК Таджикистана</div>
          </div>
        </div>
        <div class="punishment-grid">
          ${(Array.isArray(article.sentences) ? article.sentences : []).map(s => `
            <div class="punishment-card">
              <div class="punishment-type">${App.escapeHtml(s.type || 'Тип')}</div>
              <div class="punishment-value">${App.escapeHtml(s.value || '—')}</div>
              <div class="punishment-note">${App.escapeHtml(s.note || '')}</div>
            </div>
          `).join('') || '<div style="color: var(--ink-tertiary); padding: 1rem;">Данные о наказаниях недоступны.</div>'}
        </div>
        <div style="margin-top: 1rem; padding: 0.875rem; background: rgba(248,113,113,0.08); border-radius: var(--radius-md); border-left: 3px solid var(--accent-red); font-size: 0.875rem; color: var(--ink-secondary);">
          <strong style="color: var(--accent-red);">🚨 Отягчающие обстоятельства:</strong>
          <span>${App.escapeHtml(RAGMatcher.getAggravating(description))}</span>
        </div>
      </div>

      <div class="result-section">
        <div class="result-header">
          <div class="result-icon" aria-hidden="true">👮‍♂️</div>
          <div>
            <div class="result-label">Алгоритм действий</div>
            <div class="result-title">Что делать потерпевшему в Таджикистане</div>
          </div>
        </div>
        <ol class="action-list">
          ${ACTIONS_TEMPLATE.map(a => `
            <li><strong>${App.escapeHtml(a.step)}</strong><span>${App.escapeHtml(a.detail)}</span></li>
          `).join('')}
        </ol>
      </div>

      <details class="raw-response">
        <summary>🤖 Техническая информация (раскрыть)</summary>
        <pre class="raw-pre">${App.escapeHtml(rawText || JSON.stringify(article, null, 2))}</pre>
      </details>
    `;
    
    container.classList.add('active');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

class ProgressTracker {
  constructor() {
    this.el = document.getElementById('analysisProgress');
    this.steps = {
      rag: document.querySelector('[data-step="rag"]'),
      llm: document.querySelector('[data-step="llm"]'),
      render: document.querySelector('[data-step="render"]')
    };
  }

  show() {
    this.el.classList.add('active');
    this.reset();
  }

  hide() {
    this.el.classList.remove('active');
  }

  reset() {
    Object.values(this.steps).forEach(step => {
      step.classList.remove('active', 'done');
      const detail = step.querySelector('[data-detail]');
      if (detail) detail.textContent = '—';
    });
  }

  setStep(name, state, detailText = '') {
    const step = this.steps[name];
    if (!step) return;
    step.classList.remove('active', 'done');
    if (state) step.classList.add(state);
    const detail = step.querySelector('[data-detail]');
    if (detail && detailText) detail.textContent = detailText;
  }
}

class HistoryManager {
  constructor() {
    this.key = 'legalai_history';
    this.maxItems = 5;
  }
  get() {
    try { return JSON.parse(localStorage.getItem(this.key)) || []; } catch { return []; }
  }
  add(query, article) {
    const history = this.get();
    history.unshift({
      query: query.substring(0, 100),
      article: article.number,
      articleName: article.name,
      timestamp: Date.now()
    });
    if (history.length > this.maxItems) history.pop();
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
    list.innerHTML = history.map(item => `
      <div class="history-item" onclick="App.loadFromHistory('${App.escapeHtml(item.query).replace(/'/g, "\\'")}')">
        <div style="font-weight: 600; color: var(--ink-primary); margin-bottom: 0.25rem;">
          Ст. ${item.article} — ${App.escapeHtml(item.articleName)}
        </div>
        <div style="color: var(--ink-tertiary); font-size: 0.8125rem; margin-bottom: 0.25rem;">
          ${App.escapeHtml(item.query)}...
        </div>
        <div class="history-time">${new Date(item.timestamp).toLocaleString('ru-RU')}</div>
      </div>
    `).join('');
  }
}

class AppController {
  constructor() {
    this.ollama = new OllamaService();
    this.theme = new ThemeManager();
    this.history = new HistoryManager();
    this.progress = new ProgressTracker();
    this.textarea = document.getElementById('crimeDescription');
    this.counter = document.getElementById('charCounter');
    this.btn = document.getElementById('analyzeBtn');
    this.qualifyBtn = document.getElementById('qualifyBtn');
    this.resultsContainer = document.getElementById('resultsContainer');
    this.init();
  }

  init() {
    UIRenderer.renderScenarios();
    UIRenderer.renderKnowledgeBase();
    this.history.render();
    this.textarea.addEventListener('input', () => this.updateCounter());
    this.textarea.addEventListener('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        this.analyze();
      }
    });
    this.updateCounter();
    this.ollama.check().then(ok => {
      if (ok) {
        this.ollama.setSource('574_ru.doc').then(() => {
          return this.ollama.loadArticles();
        }).then(() => {
          UIRenderer.renderKnowledgeBase();
        }).catch(err => console.error('RAG init error:', err));
      }
    });
  }

  updateCounter() {
    const len = this.textarea.value.length;
    this.counter.textContent = `${len} / ${CONFIG.MAX_CHARS} символов`;
    this.counter.className = 'char-counter';
    if (len > CONFIG.MAX_CHARS * 0.9) this.counter.classList.add('danger');
    else if (len > CONFIG.MAX_CHARS * 0.75) this.counter.classList.add('warning');
  }

  fillScenario(key) {
    this.textarea.value = SCENARIOS[key];
    this.updateCounter();
    this.textarea.focus();
  }

  fillArticle(number) {
    const article = RAG_DB[number] || LOCAL_FALLBACK_DB[number];
    if (!article) return;
    this.textarea.value = `Преступление по статье ${article.number} УК РТ (${article.name}): `;
    this.updateCounter();
    this.textarea.focus();
    window.scrollTo({ top: 400, behavior: 'smooth' });
  }

  loadFromHistory(query) {
    this.textarea.value = query;
    this.updateCounter();
    this.textarea.focus();
    this.analyze();
  }

  setLoading(isLoading) {
    this.btn.disabled = isLoading;
    this.qualifyBtn.disabled = isLoading;
    
    if (isLoading) {
      this.btn.innerHTML = '<div class="spinner" aria-hidden="true"></div><span>Анализ...</span>';
      this.qualifyBtn.innerHTML = '<div class="spinner" aria-hidden="true"></div><span>Квалификация...</span>';
      this.progress.show();
      this.resultsContainer.classList.remove('active');
    } else {
      this.btn.innerHTML = `
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <span>Анализ</span>
        <kbd style="background: var(--kbd-bg); border: 1px solid var(--kbd-border); border-radius: 4px; padding: 0.1rem 0.35rem; font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; opacity: 0.8;">Ctrl+Enter</kbd>
      `;
      this.qualifyBtn.innerHTML = `
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <span>Квалификация ИИ</span>
      `;
      this.progress.hide();
    }
  }

  showError(message, details = '') {
    this.setLoading(false);
    const container = document.getElementById('resultsContainer');
    container.innerHTML = `
      <div class="result-section verdict" style="background: linear-gradient(135deg, rgba(251,191,36,0.12), rgba(217,119,6,0.06)); border-color: rgba(251,191,36,0.4);">
        <div class="result-header">
          <div class="result-icon" aria-hidden="true" style="background: linear-gradient(135deg, rgba(251,191,36,0.2), rgba(217,119,6,0.1));">⚠️</div>
          <div>
            <div class="result-label" style="color: var(--accent-amber);">Не удалось проанализировать</div>
            <div class="result-title">${this.escapeHtml(message)}</div>
          </div>
        </div>
        ${details ? `<div class="article-description" style="border-left-color: var(--accent-amber);">${this.escapeHtml(details)}</div>` : ''}
        <div style="margin-top: 1rem; color: var(--ink-tertiary); font-size: 0.875rem; line-height: 1.6;">
          💡 <strong>Советы:</strong>
          <ul style="margin-top: 0.5rem; padding-left: 1.25rem;">
            <li>Убедитесь, что Python RAG API запущен</li>
            <li>Или выберите готовый сценарий выше</li>
            <li>Проверьте консоль браузера (F12)</li>
          </ul>
        </div>
      </div>
    `;
    container.classList.add('active');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<span aria-hidden="true">${type === 'success' ? '✓' : '⚠️'}</span><span>${this.escapeHtml(message)}</span>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
  }

  async copyResults() {
    const container = document.getElementById('resultsContainer');
    const text = container.innerText;
    try {
      await navigator.clipboard.writeText(text);
      this.showToast('Результаты скопированы в буфер обмена', 'success');
      const btn = container.querySelector('.copy-btn');
      if (btn) {
        btn.classList.add('copied');
        btn.innerHTML = '✓ Скопировано';
        setTimeout(() => {
          btn.classList.remove('copied');
          btn.innerHTML = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> Копировать`;
        }, 2000);
      }
    } catch (err) {
      this.showToast('Не удалось скопировать', 'error');
    }
  }

  buildRagPrompt(description, ragResults) {
    const allowed = Object.values(RAG_DB).map(a => `- ${a.number}: ${a.name}`).join('\n');
    const sources = ragResults.map((item, index) => {
      // ✅ ФИЛЬТРАЦИЯ МУСОРА из RAG
      const rawText = String(item.text || '');
      const text = rawText
        .replace(/[^\p{L}\p{N}\s\.,;:!?\-()\[\]{}"'«»]/gu, ' ')  // только буквы, цифры, пунктуация
        .replace(/\s+/g, ' ')
        .trim()
        .slice(0, 900);
      return `[${index + 1}] ${item.source || 'RAG'}: ${text}`;
    }).join('\n\n');

    return `Use the RAG fragments to classify the crime under the Criminal Code of Tajikistan.
Return STRICT JSON only:
{
  "article_number": "one of the allowed article numbers",
  "confidence": "high|medium|low",
  "reasoning": "short reason grounded in RAG"
}

Allowed articles:
${allowed}

Crime description:
${description}

RAG fragments:
${sources || 'No RAG fragments found.'}`;
  }

  extractJson(rawText) {
    if (!rawText) return null;
    let match = rawText.match(/```(?:json)?\s*([\s\S]*?)```/);
    if (match) {
      try { return JSON.parse(match[1].trim()); } catch {}
    }
    match = rawText.match(/\{[\s\S]*\}/);
    if (match) {
      try { return JSON.parse(match[0].trim()); } catch {}
    }
    return null;
  }

  async analyze() {
    const description = this.textarea.value.trim();
    if (!description) {
      this.textarea.focus();
      this.textarea.style.animation = 'shake 0.4s ease';
      setTimeout(() => this.textarea.style.animation = '', 400);
      return;
    }

    this.setLoading(true);
    let rawResponse = '';
    let matchedArticle = null;
    let confidence = 'medium';

    if (this.ollama.connected) {
      try {
        // ШАГ 1: RAG поиск
        this.progress.setStep('rag', 'active', 'Поиск в базе УК РТ...');
        const ragResults = await this.ollama.searchRag(description);
        const ragArticleNumber = RAGMatcher.matchFromRag(ragResults);
        
        if (ragArticleNumber && RAG_DB[ragArticleNumber]) {
          matchedArticle = RAG_DB[ragArticleNumber];
          confidence = 'high';
          this.progress.setStep('rag', 'done', `✓ Найдено: Ст. ${ragArticleNumber}`);
        } else {
          const count = ragResults.length || 0;
          this.progress.setStep('rag', 'done', count > 0 ? `✓ Проверено ${count} фрагментов` : '✓ База проверена');
        }

        // ШАГ 2: LLM анализ
        this.progress.setStep('llm', 'active', 'llama3.1:8b генерирует ответ...');
        const message = this.buildRagPrompt(description, ragResults);
        
        let chunksCount = 0;
        for await (const chunk of this.ollama.streamChat(message)) {
          rawResponse += chunk;
          chunksCount++;
          if (chunksCount % 20 === 0) {
            this.progress.setStep('llm', 'active', `${chunksCount} токенов получено...`);
          }
        }
        
        const parsed = this.extractJson(rawResponse);
        if (parsed && parsed.article_number) {
          const num = String(parsed.article_number).replace(/\D/g, '');
          if (RAG_DB[num]) {
            matchedArticle = RAG_DB[num];
            confidence = parsed.confidence || 'medium';
          } else if (LOCAL_FALLBACK_DB[num]) {
            matchedArticle = LOCAL_FALLBACK_DB[num];
          }
        }
        this.progress.setStep('llm', 'done', `✓ Анализ завершён`);

      } catch (err) {
        console.error('RAG analysis error:', err);
        this.progress.setStep('rag', 'done', '✗ Ошибка, переключение на локальный режим');
        rawResponse = `Ошибка API: ${err.message}`;
      }
    }

    // Фоллбэк на локальный матчер
    if (!matchedArticle) {
      this.progress.setStep('rag', 'active', 'Локальный анализ...');
      const fallbackNum = RAGMatcher.match(description);
      if (fallbackNum) {
        matchedArticle = RAG_DB[fallbackNum] || LOCAL_FALLBACK_DB[fallbackNum] || null;
        confidence = 'medium';
      }
      if (!rawResponse) {
        rawResponse = 'Локальный анализ (API недоступен). Использован встроенный матчер.';
      }
      this.progress.setStep('rag', 'done', matchedArticle ? `✓ Ст. ${matchedArticle.number}` : '✗ Статья не найдена');
    }

    // ШАГ 3: Рендеринг
    this.progress.setStep('render', 'active', 'Подготовка отчёта...');
    await new Promise(r => setTimeout(r, 300));

    if (!matchedArticle) {
      this.showError(
        'Не удалось определить статью',
        'Опишите ситуацию подробнее: укажите способ совершения преступления, объект посягательства и наличие отягчающих обстоятельств.'
      );
      return;
    }

    this.progress.setStep('render', 'done', '✓ Отчёт готов');
    await new Promise(r => setTimeout(r, 400));

    this.setLoading(false);
    UIRenderer.renderResults(matchedArticle, description, rawResponse, confidence);
    this.history.add(description, matchedArticle);
    this.history.render();
  }

  async qualifyCrime() {
    const description = this.textarea.value.trim();
    if (!description) {
      this.textarea.focus();
      this.textarea.style.animation = 'shake 0.4s ease';
      setTimeout(() => this.textarea.style.animation = '', 400);
      return;
    }

    this.setLoading(true);

    if (!this.ollama.connected) {
      this.progress.setStep('rag', 'active', 'Локальный анализ...');
      await new Promise(r => setTimeout(r, 500));
      const fallbackNum = RAGMatcher.match(description);
      const matchedArticle = fallbackNum ? (RAG_DB[fallbackNum] || LOCAL_FALLBACK_DB[fallbackNum]) : null;
      if (!matchedArticle) {
        this.showError('Сервер недоступен и локальный анализ не дал результата.');
        return;
      }
      this.progress.setStep('rag', 'done', `✓ Ст. ${matchedArticle.number}`);
      this.progress.setStep('render', 'done', '✓ Готово');
      await new Promise(r => setTimeout(r, 300));
      this.setLoading(false);
      UIRenderer.renderResults(matchedArticle, description, 'Локальный анализ (API недоступен).');
      return;
    }

    try {
      this.progress.setStep('rag', 'active', 'Комплексный анализ...');
      const resp = await fetch(`${CONFIG.PYTHON_API_URL}/api/crime/qualify`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ crime_description: description }),
        signal: AbortSignal.timeout(120000)
      });

      if (!resp.ok) {
        const errorText = await resp.text().catch(() => '');
        throw new Error(`API вернул ${resp.status}: ${errorText.slice(0, 200)}`);
      }

      const data = await resp.json();
      if (!data.qualification) throw new Error('API не вернул данные');

      this.progress.setStep('rag', 'done', '✓ Анализ получен');
      this.progress.setStep('llm', 'done', '✓ LLM обработал');
      this.progress.setStep('render', 'active', 'Формирование отчёта...');
      await new Promise(r => setTimeout(r, 400));

      this.setLoading(false);
      this.renderQualificationResults(data, description);
    } catch (err) {
      console.error('Crime qualification error:', err);
      this.showError(
        'Ошибка квалификации',
        `${err.message}\n\nПроверьте эндпоинт /api/crime/qualify в Python API.`
      );
    }
  }

  renderQualificationResults(data, description) {
    const text = data.qualification || '';
    const sections = this.parseQualificationSections(text);
    const container = document.getElementById('resultsContainer');
    
    container.innerHTML = `
      <div class="result-section verdict">
        <div class="result-header">
          <div class="result-icon" aria-hidden="true">⚖️</div>
          <div>
            <div class="result-label">Профессиональная квалификация</div>
            <div class="result-title">Комплексный анализ по УК РТ</div>
          </div>
        </div>
        ${sections.квалификация ? `<div class="article-description">${this.escapeHtml(sections.квалификация)}</div>` : `<div class="article-description">${this.escapeHtml(text)}</div>`}
      </div>

      ${sections.санкции ? `
      <div class="result-section">
        <div class="result-header">
          <div class="result-icon" aria-hidden="true">⏱️</div>
          <div>
            <div class="result-label">САНКЦИИ И НАКАЗАНИЕ</div>
            <div class="result-title">Реальные сроки по УК РТ</div>
          </div>
        </div>
        <div class="article-description">${this.escapeHtml(sections.санкции)}</div>
      </div>` : ''}

      ${sections.факты ? `
      <div class="result-section">
        <div class="result-header">
          <div class="result-icon" aria-hidden="true">🔍</div>
          <div>
            <div class="result-label">ФАКТЫ И ДОКАЗАТЕЛЬСТВА</div>
            <div class="result-title">Подтверждение состава преступления</div>
          </div>
        </div>
        <div class="article-description">${this.escapeHtml(sections.факты)}</div>
      </div>` : ''}

      ${sections.обстоятельства ? `
      <div class="result-section">
        <div class="result-header">
          <div class="result-icon" aria-hidden="true">⚠️</div>
          <div>
            <div class="result-label">ОБСТОЯТЕЛЬСТВА</div>
            <div class="result-title">Смягчающие и отягчающие факторы</div>
          </div>
        </div>
        <div class="article-description">${this.escapeHtml(sections.обстоятельства)}</div>
      </div>` : ''}

      ${sections.анализ ? `
      <div class="result-section">
        <div class="result-header">
          <div class="result-icon" aria-hidden="true">🧠</div>
          <div>
            <div class="result-label">ПРАВОВОЙ АНАЛИЗ</div>
            <div class="result-title">Юридическое обоснование</div>
          </div>
        </div>
        <div class="article-description">${this.escapeHtml(sections.анализ)}</div>
      </div>` : ''}

      ${sections.вывод ? `
      <div class="result-section verdict">
        <div class="result-header">
          <div class="result-icon" aria-hidden="true">✅</div>
          <div>
            <div class="result-label">ИТОГОВЫЙ ВЫВОД</div>
            <div class="result-title">Заключение и рекомендации</div>
          </div>
        </div>
        <div class="article-description" style="border-color: rgba(248,113,113,0.5); font-weight: 600;">${this.escapeHtml(sections.вывод)}</div>
      </div>` : ''}

      <details class="raw-response">
        <summary>🤖 Полный ответ llama3.1:8b (раскрыть)</summary>
        <pre class="raw-pre">${this.escapeHtml(text)}</pre>
      </details>
    `;
    
    container.classList.add('active');
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  parseQualificationSections(text) {
    const sections = {
      квалификация: '', санкции: '', факты: '',
      обстоятельства: '', анализ: '', вывод: ''
    };
    if (!text) return sections;
    const patterns = {
      квалификация: /(?:1\.\s*)?КВАЛИФИКАЦИЯ[^\n]*\n([\s\S]*?)(?=\n\s*(?:2\.|САНКЦИИ|$))/i,
      санкции: /(?:2\.\s*)?САНКЦИИ[^\n]*\n([\s\S]*?)(?=\n\s*(?:3\.|ФАКТЫ|$))/i,
      факты: /(?:3\.\s*)?ФАКТЫ[^\n]*[Ии][^\n]*\n([\s\S]*?)(?=\n\s*(?:4\.|ОБСТОЯТЕЛЬСТВА|$))/i,
      обстоятельства: /(?:4\.\s*)?ОБСТОЯТЕЛЬСТВА[^\n]*\n([\s\S]*?)(?=\n\s*(?:5\.|ПРАВОВОЙ|$))/i,
      анализ: /(?:5\.\s*)?ПРАВОВОЙ[^\n]*\n([\s\S]*?)(?=\n\s*(?:6\.|ИТОГОВЫЙ|$))/i,
      вывод: /(?:6\.\s*)?ИТОГОВЫЙ[^\n]*\n([\s\S]*?)$/i
    };
    for (const [key, pattern] of Object.entries(patterns)) {
      const match = text.match(pattern);
      sections[key] = match ? match[1].trim() : '';
    }
    return sections;
  }

  escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text || '').replace(/[&<>"']/g, m => map[m]);
  }
}

const App = new AppController();
</script>

</body>
</html>