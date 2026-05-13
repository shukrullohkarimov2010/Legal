<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.app_name') }} — {{ __('ui.features_title') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        /* Dark Theme (Default) */
        body {
            background: #0f172a;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        /* Light Theme */
        body.light-theme {
            background: #ffffff;
            color: #1a1a1a;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        }

        body.light-theme .gradient-bg {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 50%, #ffffff 100%);
        }

        .card-gradient {
            background: linear-gradient(145deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.9) 100%);
            backdrop-filter: blur(10px);
        }

        body.light-theme .card-gradient {
            background: linear-gradient(145deg, rgba(241, 245, 249, 0.95) 0%, rgba(255, 255, 255, 1) 100%);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(59, 130, 246, 0.4);
        }

        .feature-card {
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: #3b82f6;
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .glow-text {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }

        body.light-theme .glow-text {
            text-shadow: none;
        }

        .message-user {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .message-ai {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        body.light-theme .message-ai {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .typing-indicator span {
            animation: typing 1.4s infinite;
            animation-fill-mode: both;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }

        .risk-high {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .risk-medium {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .risk-low {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }

        .sidebar-item {
            transition: all 0.2s ease;
        }

        .sidebar-item:hover, .sidebar-item.active {
            background: rgba(59, 130, 246, 0.2);
            border-left: 3px solid #3b82f6;
        }

        body.light-theme .sidebar-item:hover,
        body.light-theme .sidebar-item.active {
            background: rgba(59, 130, 246, 0.1);
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        body.light-theme ::-webkit-scrollbar-track {
            background: #e2e8f0;
        }

        ::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 4px;
        }

        body.light-theme ::-webkit-scrollbar-thumb {
            background: #94a3b8;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        .modal-overlay {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .upload-zone {
            border: 2px dashed rgba(59, 130, 246, 0.5);
            transition: all 0.3s ease;
        }

        body.light-theme .upload-zone {
            border: 2px dashed rgba(59, 130, 246, 0.3);
        }

        .upload-zone:hover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }

        .upload-zone.dragover {
            border-color: #22c55e;
            background: rgba(34, 197, 94, 0.1);
        }

        .progress-ring {
            transform: rotate(-90deg);
        }

        .tab-active {
            background: rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
            color: #60a5fa;
        }

        .markdown-content h1,
        .markdown-content h2,
        .markdown-content h3 {
            margin-top: 1em;
            margin-bottom: 0.5em;
            font-weight: 600;
        }

        .markdown-content p {
            margin-bottom: 1em;
            line-height: 1.7;
        }

        .markdown-content ul,
        .markdown-content ol {
            margin-left: 1.5em;
            margin-bottom: 1em;
        }

        .markdown-content code {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.2em 0.4em;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }

        body.light-theme .markdown-content code {
            background: rgba(0, 0, 0, 0.08);
        }

        .markdown-content pre {
            background: rgba(0, 0, 0, 0.3);
            padding: 1em;
            border-radius: 8px;
            overflow-x: auto;
            margin-bottom: 1em;
        }

        body.light-theme .markdown-content pre {
            background: rgba(0, 0, 0, 0.05);
        }

        .comparison-diff {
            background: rgba(239, 68, 68, 0.2);
            border-left: 3px solid #ef4444;
        }

        .comparison-add {
            background: rgba(34, 197, 94, 0.2);
            border-left: 3px solid #22c55e;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            position: fixed;
            top: 100px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 40px rgba(59, 130, 246, 0.4);
            transition: all 0.3s ease;
            z-index: 99;
        }

        .theme-toggle:hover {
            transform: scale(1.1) rotate(15deg);
            box-shadow: 0 15px 50px rgba(59, 130, 246, 0.5);
        }

        .theme-toggle svg {
            width: 24px;
            height: 24px;
            transition: all 0.3s ease;
        }

        .theme-toggle .sun-icon {
            display: none;
        }

        .theme-toggle .moon-icon {
            display: block;
        }

        body.light-theme .theme-toggle .sun-icon {
            display: block;
        }

        body.light-theme .theme-toggle .moon-icon {
            display: none;
        }

        /* Light theme specific styles */
        body.light-theme .text-slate-300 {
            color: #475569;
        }

        body.light-theme .text-slate-400 {
            color: #64748b;
        }

        body.light-theme .border-slate-700 {
            border-color: #e2e8f0;
        }

        body.light-theme nav {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid #e2e8f0;
        }

        body.light-theme footer {
            border-top: 1px solid #e2e8f0;
        }

        body.light-theme .bg-slate-800\/50 {
            background: rgba(241, 245, 249, 0.8);
        }

        body.light-theme input,
        body.light-theme select,
        body.light-theme textarea {
            background: rgba(241, 245, 249, 0.9);
            color: #1a1a1a;
            border-color: #cbd5e1;
        }

        body.light-theme input::placeholder,
        body.light-theme textarea::placeholder {
            color: #94a3b8;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">
<!-- Theme Toggle Button -->
<div class="theme-toggle" onclick="toggleTheme()" title="{{ __('ui.theme_toggle') }}">
    <svg class="sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    <svg class="moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
    </svg>
</div>
@include('admin.partials.header')
<!-- Hero Section -->
<section class="pt-32 pb-20 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <div class="inline-flex items-center space-x-2 bg-blue-500/20 border border-blue-500/30 rounded-full px-4 py-2 mb-6">
                <span class="w-2 h-2 bg-green-500 rounded-full pulse-animation"></span>
                <span class="text-sm text-blue-300">{{ __('ui.features_badge') }}</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                {{ __('ui.features_hero_title_1') }}
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">{{ __('ui.features_hero_title_2') }}</span>
            </h1>
            <p class="text-lg text-slate-400 max-w-3xl mx-auto mb-8">
                {{ __('ui.features_hero_text') }}
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <button onclick="scrollToDemo()" class="btn-primary px-8 py-4 rounded-xl font-semibold text-lg">
                    {{ __('ui.try_demo') }}
                </button>
                <button onclick="openModal('featuresModal')" class="px-8 py-4 rounded-xl font-semibold text-lg border border-slate-600 hover:border-blue-500 transition">
                    {{ __('ui.all_features') }}
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section id="features" class="py-20 px-4 border-t border-slate-700">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ __('ui.platform_features_title') }}</h2>
            <p class="text-slate-400 max-w-2xl mx-auto">{{ __('ui.platform_features_text') }}</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Feature 1: AI Chat -->
            <div class="feature-card card-gradient rounded-2xl p-6 border border-slate-700 cursor-pointer" onclick="openFeature('chat')">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('ui.feature_ai_chat') }}</h3>
                <p class="text-slate-400 text-sm mb-4">{{ __('ui.feature_ai_chat_text') }}</p>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full">{{ __('ui.popular') }}</span>
                    <span class="text-slate-500">{{ __('ui.response_60sec') }}</span>
                </div>
            </div>
            <!-- Feature 2: Document Analysis -->
            <div class="feature-card card-gradient rounded-2xl p-6 border border-slate-700 cursor-pointer" onclick="openFeature('analysis')">
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('ui.feature_contract_analysis') }}</h3>
                <p class="text-slate-400 text-sm mb-4">{{ __('ui.feature_contract_analysis_text') }}</p>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded-full">{{ __('ui.accuracy_95') }}</span>
                    <span class="text-slate-500">{{ __('ui.analysis_1min') }}</span>
                </div>
            </div>
            <!-- Feature 3: Risk Assessment -->
            <div class="feature-card card-gradient rounded-2xl p-6 border border-slate-700 cursor-pointer" onclick="openFeature('risk')">
                <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('ui.feature_risk_assessment') }}</h3>
                <p class="text-slate-400 text-sm mb-4">{{ __('ui.feature_risk_assessment_text') }}</p>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-purple-500/20 text-purple-400 rounded-full">{{ __('ui.ai_forecast') }}</span>
                </div>
            </div>
            <!-- Feature 4: Template Generator -->
            <div class="feature-card card-gradient rounded-2xl p-6 border border-slate-700 cursor-pointer" onclick="openFeature('templates')">
                <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('ui.feature_template_generator') }}</h3>
                <p class="text-slate-400 text-sm mb-4">{{ __('ui.feature_template_generator_text') }}</p>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full">{{ __('ui.templates_50') }}</span>
                </div>
            </div>
            <!-- Feature 5: Document Comparison -->
            <div class="feature-card card-gradient rounded-2xl p-6 border border-slate-700 cursor-pointer" onclick="openFeature('comparison')">
                <div class="w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('ui.feature_version_compare') }}</h3>
                <p class="text-slate-400 text-sm mb-4">{{ __('ui.feature_version_compare_text') }}</p>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded-full">{{ __('ui.diff_analysis') }}</span>
                </div>
            </div>
            <!-- Feature 6: Compliance Check -->
            <div class="feature-card card-gradient rounded-2xl p-6 border border-slate-700 cursor-pointer" onclick="openFeature('compliance')">
                <div class="w-12 h-12 bg-cyan-500/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('ui.feature_compliance') }}</h3>
                <p class="text-slate-400 text-sm mb-4">{{ __('ui.feature_compliance_text') }}</p>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full">{{ __('ui.up_to_date_2026') }}</span>
                </div>
            </div>
            <!-- Feature 7: Batch Processing -->
            <div class="feature-card card-gradient rounded-2xl p-6 border border-slate-700 cursor-pointer" onclick="openFeature('batch')">
                <div class="w-12 h-12 bg-pink-500/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('ui.feature_batch') }}</h3>
                <p class="text-slate-400 text-sm mb-4">{{ __('ui.feature_batch_text') }}</p>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-purple-500/20 text-purple-400 rounded-full">{{ __('ui.up_to_50_files') }}</span>
                </div>
            </div>
            <!-- Feature 8: Legal Research -->
            <div class="feature-card card-gradient rounded-2xl p-6 border border-slate-700 cursor-pointer" onclick="openFeature('research')">
                <div class="w-12 h-12 bg-indigo-500/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">{{ __('ui.feature_research') }}</h3>
                <p class="text-slate-400 text-sm mb-4">{{ __('ui.feature_research_text') }}</p>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded-full">Консультант+</span>
                </div>
            </div>
            <!-- Feature 12: API Access -->
            <div class="feature-card card-gradient rounded-2xl p-6 border border-slate-700 cursor-pointer" onclick="openFeature('api')">
                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">REST API</h3>
                <p class="text-slate-400 text-sm mb-4">{{ __('ui.feature_api_text') }}</p>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded-full">SDK</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Demo Section -->
<section id="demo" class="py-20 px-4 border-t border-slate-700">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ __('ui.demo_capabilities_title') }}</h2>
            <p class="text-slate-400 max-w-2xl mx-auto">{{ __('ui.demo_capabilities_text') }}</p>
        </div>
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- AI Chat Demo -->
            <div class="card-gradient rounded-2xl border border-slate-700 overflow-hidden">
                <div class="p-4 border-b border-slate-700 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full pulse-animation"></div>
                        <span class="font-semibold">AI Ассистент</span>
                    </div>
                    <button onclick="clearChat()" class="text-slate-400 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                <div id="chatDemo" class="h-80 overflow-y-auto p-4 space-y-4">
                    <div class="flex justify-start">
                        <div class="message-ai rounded-2xl p-4 max-w-[85%]">
                            <p class="text-sm">Здравствуйте! Я ваш юридический AI-ассистент. Чем могу помочь?</p>
                            <p class="text-xs text-slate-400 mt-2">10:00</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-slate-700">
                    <div class="flex items-center space-x-3">
                        <input type="text" id="chatInput" placeholder="Задайте вопрос..." class="flex-1 bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 text-sm" onkeydown="if(event.key==='Enter')sendDemoMessage()">
                        <button onclick="sendDemoMessage()" class="btn-primary p-3 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <button onclick="quickQuestion('Какие риски в договоре поставки?')" class="px-3 py-1 bg-slate-800 rounded-full text-xs hover:bg-slate-700 transition">Договор поставки</button>
                        <button onclick="quickQuestion('Как защитить ИП?')" class="px-3 py-1 bg-slate-800 rounded-full text-xs hover:bg-slate-700 transition">Защита ИП</button>
                        <button onclick="quickQuestion('Штрафы по ГК РФ')" class="px-3 py-1 bg-slate-800 rounded-full text-xs hover:bg-slate-700 transition">Штрафы</button>
                    </div>
                </div>
            </div>
            <!-- Risk Analysis Demo -->
            <div class="card-gradient rounded-2xl border border-slate-700 overflow-hidden">
                <div class="p-4 border-b border-slate-700">
                    <span class="font-semibold">Анализ Рисков</span>
                </div>
                <div class="p-4">
                    <div class="upload-zone rounded-xl p-8 text-center cursor-pointer mb-4" onclick="simulateAnalysis()">
                        <svg class="w-12 h-12 text-blue-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-slate-400">Нажмите для демо-анализа</p>
                    </div>
                    <div id="analysisDemo" class="hidden">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-slate-400">Общий риск:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-medium risk-medium">Средний</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                    <span class="text-sm">Штрафные санкции</span>
                                </div>
                                <span class="text-xs text-red-400">Высокий</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                                    <span class="text-sm">Сроки оплаты</span>
                                </div>
                                <span class="text-xs text-orange-400">Средний</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-sm">Конфиденциальность</span>
                                </div>
                                <span class="text-xs text-green-400">Низкий</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="py-12 px-4 border-t border-slate-700">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center space-x-3 mb-4 md:mb-0">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-xl font-bold">LegalAI Pro</span>
            </div>
            <p class="text-slate-400 text-sm">© 2026 LegalAI Pro. Все права защищены.</p>
        </div>
    </div>
</footer>

<!-- Feature Modal -->
<div id="featureModal" class="hidden fixed inset-0 z-50 modal-overlay flex items-center justify-center p-4">
    <div class="card-gradient rounded-2xl max-w-2xl w-full border border-slate-700 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-700 sticky top-0 bg-slate-900/90 backdrop-blur">
            <h3 class="text-xl font-semibold" id="featureTitle">Название возможности</h3>
            <button onclick="closeModal('featureModal')" class="text-slate-400 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6" id="featureContent">
            <!-- Content will be inserted here -->
        </div>
    </div>
</div>

<script>
    // Theme Toggle Function
    function toggleTheme() {
        document.body.classList.toggle('light-theme');
        const isLight = document.body.classList.contains('light-theme');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
    }

    // Load saved theme on page load
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.body.classList.add('light-theme');
        }
    });

    // Scroll to demo
    function scrollToDemo() {
        document.getElementById('demo').scrollIntoView({ behavior: 'smooth' });
    }

    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Open feature details
    function openFeature(featureId) {
        const features = {
            'chat': {
                title: 'AI Юридический Чат',
                content: `
<div class="space-y-4">
<p class="text-slate-300">Интеллектуальный юридический ассистент на базе GPT-4 и Google Gemini для консультаций 24/7.</p>
<h4 class="font-semibold mt-6">Возможности:</h4>
<ul class="space-y-2 text-slate-300">
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Консультации по российскому законодательству</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Анализ конкретных ситуаций и кейсов</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Ссылки на статьи ГК РФ и другие нормативные акты</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Поддержка загрузки документов для анализа</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>История диалогов с поиском</span>
</li>
</ul>
<div class="mt-6 p-4 bg-blue-500/10 border border-blue-500/30 rounded-xl">
<h5 class="font-semibold mb-2">Технические детали:</h5>
<div class="grid grid-cols-2 gap-4 text-sm">
<div>
<span class="text-slate-400">Модели:</span>
<span class="text-white ml-2">GPT-4, GPT-3.5, Gemini Pro</span>
</div>
<div>
<span class="text-slate-400">Время ответа:</span>
<span class="text-white ml-2">~30-60 секунд</span>
</div>
<div>
<span class="text-slate-400">Контекст:</span>
<span class="text-white ml-2">До 128K токенов</span>
</div>
<div>
<span class="text-slate-400">Языки:</span>
<span class="text-white ml-2">RU, EN, DE, FR</span>
</div>
</div>
</div>
</div>
`
            },
            'analysis': {
                title: 'Анализ Договоров',
                content: `
<div class="space-y-4">
<p class="text-slate-300">Автоматическое выявление юридических рисков, несоответствий законодательству и невыгодных условий в договорах.</p>
<h4 class="font-semibold mt-6">Что проверяется:</h4>
<ul class="space-y-2 text-slate-300">
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Штрафные санкции и неустойки</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Сроки исполнения обязательств</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Пункт о форс-мажоре</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Конфиденциальность и NDA</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Ответственность сторон</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Порядок разрешения споров</span>
</li>
</ul>
<div class="mt-6 grid grid-cols-3 gap-4 text-center">
<div class="p-4 bg-slate-800/50 rounded-xl">
<div class="text-2xl font-bold text-blue-400">95%</div>
<div class="text-xs text-slate-400">Точность</div>
</div>
<div class="p-4 bg-slate-800/50 rounded-xl">
<div class="text-2xl font-bold text-green-400">60 сек</div>
<div class="text-xs text-slate-400">Время анализа</div>
</div>
<div class="p-4 bg-slate-800/50 rounded-xl">
<div class="text-2xl font-bold text-purple-400">50+</div>
<div class="text-xs text-slate-400">Типов рисков</div>
</div>
</div>
</div>
`
            },
            'risk': {
                title: 'Оценка Рисков',
                content: `
<div class="space-y-4">
<p class="text-slate-300">Визуальная карта рисков с приоритетами, вероятностью наступления и рекомендациями по минимизации.</p>
<h4 class="font-semibold mt-6">Уровни рисков:</h4>
<div class="space-y-3">
<div class="flex items-center space-x-4 p-3 bg-red-500/10 border border-red-500/30 rounded-lg">
<div class="w-3 h-3 bg-red-500 rounded-full"></div>
<div>
<div class="font-medium text-red-400">Высокий риск</div>
<div class="text-sm text-slate-400">Требует немедленного внимания</div>
</div>
</div>
<div class="flex items-center space-x-4 p-3 bg-orange-500/10 border border-orange-500/30 rounded-lg">
<div class="w-3 h-3 bg-orange-500 rounded-full"></div>
<div>
<div class="font-medium text-orange-400">Средний риск</div>
<div class="text-sm text-slate-400">Рекомендуется доработка</div>
</div>
</div>
<div class="flex items-center space-x-4 p-3 bg-green-500/10 border border-green-500/30 rounded-lg">
<div class="w-3 h-3 bg-green-500 rounded-full"></div>
<div>
<div class="font-medium text-green-400">Низкий риск</div>
<div class="text-sm text-slate-400">Приемлемые условия</div>
</div>
</div>
</div>
<div class="mt-6 p-4 bg-blue-500/10 border border-blue-500/30 rounded-xl">
<h5 class="font-semibold mb-2">AI-прогноз:</h5>
<p class="text-sm text-slate-300">Система предсказывает вероятность наступления рискованных ситуаций на основе анализа похожих договоров и судебной практики.</p>
</div>
</div>
`
            },
            'templates': {
                title: 'Генератор Шаблонов',
                content: `
<div class="space-y-4">
<p class="text-slate-300">Создание юридических документов по параметрам с автоматическим заполнением и адаптацией под вашу ситуацию.</p>
<h4 class="font-semibold mt-6">Доступные шаблоны:</h4>
<div class="grid grid-cols-2 gap-3">
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">Договор поставки</div>
<div class="text-xs text-slate-400">ГК РФ ст. 506-523</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">Договор аренды</div>
<div class="text-xs text-slate-400">ГК РФ ст. 606-670</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">Договор услуг</div>
<div class="text-xs text-slate-400">ГК РФ ст. 779-783</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">Договор подряда</div>
<div class="text-xs text-slate-400">ГК РФ ст. 702-768</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">NDA (Конфиденциальность)</div>
<div class="text-xs text-slate-400">ГК РФ ст. 139</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">Трудовой договор</div>
<div class="text-xs text-slate-400">ТК РФ ст. 56-90</div>
</div>
</div>
<div class="mt-6 p-4 bg-green-500/10 border border-green-500/30 rounded-xl">
<h5 class="font-semibold mb-2">Преимущества:</h5>
<ul class="space-y-2 text-sm text-slate-300">
<li>• Автоматическая подстановка реквизитов</li>
<li>• Проверка на соответствие законодательству</li>
<li>• Сохранение собственных шаблонов</li>
<li>• Версионность документов</li>
</ul>
</div>
</div>
`
            },
            'comparison': {
                title: 'Сравнение Версий',
                content: `
<div class="space-y-4">
<p class="text-slate-300">Автоматическое сравнение двух версий документа с подсветкой добавленных, удалённых и изменённых фрагментов.</p>
<h4 class="font-semibold mt-6">Что показывает сравнение:</h4>
<ul class="space-y-2 text-slate-300">
<li class="flex items-start space-x-2">
<div class="w-3 h-3 bg-red-500/30 border-l-2 border-red-500 mt-1"></div>
<span>Удалённый текст (красная подсветка)</span>
</li>
<li class="flex items-start space-x-2">
<div class="w-3 h-3 bg-green-500/30 border-l-2 border-green-500 mt-1"></div>
<span>Добавленный текст (зелёная подсветка)</span>
</li>
<li class="flex items-start space-x-2">
<div class="w-3 h-3 bg-yellow-500/30 border-l-2 border-yellow-500 mt-1"></div>
<span>Изменённый текст (жёлтая подсветка)</span>
</li>
</ul>
<div class="mt-6 grid grid-cols-2 gap-4">
<div class="p-4 bg-slate-800/50 rounded-xl">
<div class="text-2xl font-bold text-blue-400">100%</div>
<div class="text-xs text-slate-400">Точность сравнения</div>
</div>
<div class="p-4 bg-slate-800/50 rounded-xl">
<div class="text-2xl font-bold text-green-400">PDF/DOCX</div>
<div class="text-xs text-slate-400">Поддерживаемые форматы</div>
</div>
</div>
</div>
`
            },
            'compliance': {
                title: 'Проверка Соответствия',
                content: `
<div class="space-y-4">
<p class="text-slate-300">Автоматическая проверка документов на соответствие российскому и международному законодательству.</p>
<h4 class="font-semibold mt-6">Проверяемые регуляторные требования:</h4>
<div class="grid grid-cols-2 gap-3">
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">152-ФЗ</div>
<div class="text-xs text-slate-400">Персональные данные</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">ГК РФ</div>
<div class="text-xs text-slate-400">Гражданский кодекс</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">44-ФЗ</div>
<div class="text-xs text-slate-400">Контрактная система</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">223-ФЗ</div>
<div class="text-xs text-slate-400">Госзакупки</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">GDPR</div>
<div class="text-xs text-slate-400">Защита данных ЕС</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg">
<div class="font-medium text-sm">Отраслевые</div>
<div class="text-xs text-slate-400">Стандарты отрасли</div>
</div>
</div>
<div class="mt-6 p-4 bg-blue-500/10 border border-blue-500/30 rounded-xl">
<h5 class="font-semibold mb-2">Результат проверки:</h5>
<p class="text-sm text-slate-300">Процент соответствия каждому требованию с подробным отчётом о нарушениях и рекомендациями по исправлению.</p>
</div>
</div>
`
            },
            'batch': {
                title: 'Пакетная Обработка',
                content: `
<div class="space-y-4">
<p class="text-slate-300">Одновременный анализ множества документов с формированием сводного отчёта и сравнительной аналитики.</p>
<h4 class="font-semibold mt-6">Возможности:</h4>
<ul class="space-y-2 text-slate-300">
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Загрузка до 50 файлов одновременно</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Сводный отчёт по всем документам</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Сравнительная таблица рисков</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Экспорт в Excel/PDF</span>
</li>
<li class="flex items-start space-x-2">
<svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
<span>Приоритетная очередь обработки</span>
</li>
</ul>
<div class="mt-6 grid grid-cols-3 gap-4 text-center">
<div class="p-4 bg-slate-800/50 rounded-xl">
<div class="text-2xl font-bold text-blue-400">50</div>
<div class="text-xs text-slate-400">Файлов за раз</div>
</div>
<div class="p-4 bg-slate-800/50 rounded-xl">
<div class="text-2xl font-bold text-green-400">45 мин</div>
<div class="text-xs text-slate-400">Среднее время</div>
</div>
<div class="p-4 bg-slate-800/50 rounded-xl">
<div class="text-2xl font-bold text-purple-400">Excel</div>
<div class="text-xs text-slate-400">Формат отчёта</div>
</div>
</div>
</div>
`
            },
            'api': {
                title: 'REST API',
                content: `
<div class="space-y-4">
<p class="text-slate-300">Программный доступ ко всем функциям платформы для интеграции с вашими системами.</p>
<h4 class="font-semibold mt-6">Доступные эндпоинты:</h4>
<div class="space-y-2">
<div class="p-3 bg-slate-800/50 rounded-lg font-mono text-sm">
<span class="text-green-400">POST</span> /api/v1/analyze
<div class="text-xs text-slate-400 mt-1">Анализ документа</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg font-mono text-sm">
<span class="text-blue-400">GET</span> /api/v1/contracts
<div class="text-xs text-slate-400 mt-1">Список договоров</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg font-mono text-sm">
<span class="text-purple-400">POST</span> /api/v1/chat
<div class="text-xs text-slate-400 mt-1">AI-чат</div>
</div>
<div class="p-3 bg-slate-800/50 rounded-lg font-mono text-sm">
<span class="text-orange-400">GET</span> /api/v1/templates
<div class="text-xs text-slate-400 mt-1">Шаблоны документов</div>
</div>
</div>
<div class="mt-6 p-4 bg-blue-500/10 border border-blue-500/30 rounded-xl">
<h5 class="font-semibold mb-2">SDK и библиотеки:</h5>
<div class="flex flex-wrap gap-2">
<span class="px-3 py-1 bg-slate-800 rounded text-xs">Python</span>
<span class="px-3 py-1 bg-slate-800 rounded text-xs">JavaScript</span>
<span class="px-3 py-1 bg-slate-800 rounded text-xs">PHP</span>
<span class="px-3 py-1 bg-slate-800 rounded text-xs">Java</span>
<span class="px-3 py-1 bg-slate-800 rounded text-xs">C#</span>
</div>
</div>
</div>
`
            }
        };
        const feature = features[featureId];
        if (feature) {
            document.getElementById('featureTitle').textContent = feature.title;
            document.getElementById('featureContent').innerHTML = feature.content;
            openModal('featureModal');
        }
    }

    // Demo chat functions
    let demoMessages = [
        { role: 'ai', content: 'Здравствуйте! Я ваш юридический AI-ассистент. Чем могу помочь?' }
    ];

    function renderDemoChat() {
        const container = document.getElementById('chatDemo');
        container.innerHTML = '';
        demoMessages.forEach(msg => {
            const div = document.createElement('div');
            div.className = `flex ${msg.role === 'user' ? 'justify-end' : 'justify-start'}`;
            div.innerHTML = `
<div class="${msg.role === 'user' ? 'message-user' : 'message-ai'} rounded-2xl p-4 max-w-[85%]">
<p class="text-sm">${msg.content}</p>
<p class="text-xs text-slate-400 mt-2">${new Date().toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' })}</p>
</div>
`;
            container.appendChild(div);
        });
        container.scrollTop = container.scrollHeight;
    }

    async function sendDemoMessage() {
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        if (!message) return;

        demoMessages.push({ role: 'user', content: message });
        renderDemoChat();
        input.value = '';

// Show typing
        const container = document.getElementById('chatDemo');
        const typing = document.createElement('div');
        typing.id = 'typingDemo';
        typing.className = 'flex justify-start';
        typing.innerHTML = `
<div class="message-ai rounded-2xl p-4">
<div class="typing-indicator flex space-x-1">
<span class="w-2 h-2 bg-blue-400 rounded-full"></span>
<span class="w-2 h-2 bg-blue-400 rounded-full"></span>
<span class="w-2 h-2 bg-blue-400 rounded-full"></span>
</div>
</div>
`;
        container.appendChild(typing);
        container.scrollTop = container.scrollHeight;

// Simulate response
        await new Promise(resolve => setTimeout(resolve, 1500));
        typing.remove();

        const responses = {
            'риск': '## Анализ рисков\n\nВ договоре поставки рекомендую обратить внимание на:\n1. **Штрафные санкции** — не более 0.5% в день (ст. 333 ГК РФ)\n2. **Сроки оплаты** — должны быть конкретные даты\n3. **Форс-мажор** — проверьте соответствие ст. 401 ГК РФ\n\nХотите, чтобы я проанализировал конкретный документ?',
            'ип': '## Защита интеллектуальной собственности\n\n### Рекомендации:\n1. **Регистрация прав** — в Роспатенте\n2. **NDA** — со всеми сотрудниками и партнёрами\n3. **Лицензионные договоры** — чёткие условия использования\n4. **Компенсации** — укажите в договорах\n\nЧасть IV ГК РФ регулирует права на результаты ИС.',
            'штраф': '## Штрафы по ГК РФ\n\n### Виды ответственности:\n| Вид | Статья | Описание |\n|-----|--------|----------|\n| Неустойка | ст. 330 | Штраф/пени |\n| Убытки | ст. 15 | Реальный ущерб + упущенная выгода |\n| Проценты | ст. 395 | За пользование средствами |\n\nСуд может уменьшить неустойку если она несоразмерна (ст. 333 ГК РФ).',
            'default': 'Спасибо за вопрос! Для более точного ответа рекомендую:\n1. Загрузить конкретный документ для анализа\n2. Указать больше деталей ситуации\n3. Обратиться к профильному юристу для консультации\n\nЧем ещё могу помочь?'
        };

        let response = responses['default'];
        const lowerMessage = message.toLowerCase();
        for (const [key, value] of Object.entries(responses)) {
            if (lowerMessage.includes(key)) {
                response = value;
                break;
            }
        }

        demoMessages.push({ role: 'ai', content: response });
        renderDemoChat();
    }

    function quickQuestion(question) {
        document.getElementById('chatInput').value = question;
        sendDemoMessage();
    }

    function clearChat() {
        demoMessages = [{ role: 'ai', content: 'Здравствуйте! Я ваш юридический AI-ассистент. Чем могу помочь?' }];
        renderDemoChat();
    }

    // Analysis demo
    function simulateAnalysis() {
        const demo = document.getElementById('analysisDemo');
        demo.classList.remove('hidden');

// Animate progress
        const items = demo.querySelectorAll('.bg-slate-800\\/50');
        items.forEach((item, index) => {
            item.style.opacity = '0';
            setTimeout(() => {
                item.style.transition = 'opacity 0.5s ease';
                item.style.opacity = '1';
            }, index * 300);
        });
    }

    // Close modal when clicking overlay
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.add('hidden');
            }
        });
    });
</script>
</body>
</html>
