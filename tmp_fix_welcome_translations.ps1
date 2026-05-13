$path = 'resources/views/welcome.blade.php'
$lines = [System.Collections.Generic.List[string]]::new()
(Get-Content -Encoding UTF8 $path).ForEach({ [void]$lines.Add($_) })

function Replace-Line {
    param(
        [System.Collections.Generic.List[string]]$Target,
        [int]$LineNumber,
        [string]$Value
    )
    $Target[$LineNumber - 1] = $Value
}

function Replace-Range {
    param(
        [System.Collections.Generic.List[string]]$Target,
        [int]$StartLine,
        [int]$EndLine,
        [string[]]$NewLines
    )
    $Target.RemoveRange($StartLine - 1, $EndLine - $StartLine + 1)
    $Target.InsertRange($StartLine - 1, $NewLines)
}

Replace-Line $lines 1526 "    {{ `$welcomeExtra['skip_to_content'] }}"
Replace-Line $lines 1530 '<div class="theme-toggle" onclick="toggleTheme()" title="{{ $welcomeExtra[''theme_toggle_title''] }}" role="switch" aria-label="{{ $welcomeExtra[''theme_toggle_aria''] }}">'
Replace-Line $lines 1540 '<nav role="navigation" aria-label="{{ $welcomeExtra[''nav_aria''] }}">'
Replace-Line $lines 1558 '                    <button onclick="toggleNotifications()" class="text-slate-400 hover:text-white transition relative p-2" aria-label="{{ __(''ui.notifications'') }}">'
Replace-Line $lines 1564 '                            <h4 class="font-semibold text-white">{{ __(''ui.notifications'') }}</h4>'
Replace-Line $lines 1571 '                                        <p class="text-sm text-white">{{ __(''ui.notification_analysis_done'') }}</p>'
Replace-Line $lines 1572 '                                        <p class="text-xs text-slate-400 mt-1">{{ __(''ui.minutes_ago_2'') }}</p>'
Replace-Line $lines 1580 '                                        <p class="text-sm text-white">{{ __(''ui.notification_high_risk'') }}</p>'
Replace-Line $lines 1581 '                                        <p class="text-xs text-slate-400 mt-1">{{ __(''ui.minutes_ago_15'') }}</p>'
Replace-Line $lines 1589 '                                        <p class="text-sm text-white">{{ __(''ui.notification_expiring'') }}</p>'
Replace-Line $lines 1590 '                                        <p class="text-xs text-slate-400 mt-1">{{ __(''ui.hour_ago_1'') }}</p>'
Replace-Line $lines 1598 '                    {{ __(''ui.nav_login'') }}'
Replace-Line $lines 1603 '            <button onclick="toggleMobileMenu()" class="md:hidden text-slate-400 hover:text-white p-2" aria-label="{{ $welcomeExtra[''mobile_menu_aria''] }}">'
Replace-Line $lines 1623 '            <button onclick="openCommandPalette()" class="w-full text-left py-2 text-slate-300 hover:text-white transition">⌕ {{ $welcomeExtra[''command_search''] }}</button>'
Replace-Line $lines 1624 '            <a href="{{route(''login'')}}" class="block btn-primary text-center px-6 py-2 rounded-lg font-medium">{{ __(''ui.nav_login'') }}</a>'
Replace-Line $lines 1697 '                                <span>{{ $welcomeExtra[''support_247''] }}</span>'
Replace-Line $lines 1706 '                                    <div class="text-lg font-bold text-green-400">60 sec</div>'
Replace-Line $lines 1707 '                                    <div class="text-xs text-slate-400">{{ __(''ui.welcome_analysis_time'') }}</div>'
Replace-Line $lines 1719 '                                    <div class="text-xs text-slate-400">{{ __(''ui.welcome_detection_accuracy'') }}</div>'
Replace-Line $lines 1924 "                            <h3 class=`"font-semibold text-white text-center`">{{ app()->getLocale() === 'tg' ? 'NDA ва махфият' : (app()->getLocale() === 'ru' ? 'NDA и конфиденциальность' : 'NDA and confidentiality') }}</h3>"
Replace-Line $lines 2014 '                    <p>{{ $welcomeExtra[''about_p3''] }}</p>'
Replace-Line $lines 2675 '            <h3 class="text-xl font-semibold">{{ $welcomeExtra[''demo_modal_title''] }}</h3>'
Replace-Line $lines 2676 "            <button onclick=`"closeModal('demoModal')`" class=`"text-slate-400 hover:text-white transition p-2`" aria-label=`"{{ `$welcomeExtra['close'] }}`">"
Replace-Line $lines 2729 '    <span id="toastMessage">{{ __(''ui.notifications'') }}</span>'
Replace-Line $lines 2733 "    <div class=`"fab`" onclick=`"showToast('info', @js(`$welcomeExtra['fab_message']))`" role=`"button`" aria-label=`"{{ `$welcomeExtra['help'] }}`">"
Replace-Line $lines 2745 "        showToast('success', isLight ? @js(`$welcomeExtra['theme_light']) : @js(`$welcomeExtra['theme_dark']));"

$problemBlock = @(
'        @php',
'            $problems = $welcomeExtra[''problems''];',
'            $solutions = $welcomeExtra[''solutions''];',
'        @endphp',
'',
'        <section class="relative py-24">',
'            <div class="mx-auto max-w-7xl px-4 sm:px-6">',
'                <div class="mx-auto max-w-3xl text-center">',
'                    <span class="inline-flex items-center gap-2 rounded-full border border-rose-500/20 bg-rose-500/10 px-3 py-1 text-xs font-medium text-rose-300">',
'                        <span class="h-1.5 w-1.5 rounded-full bg-rose-400 animate-pulse"></span>',
'                        {{ $welcomeExtra[''problems_badge''] }}',
'                    </span>',
'',
'                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-white sm:text-5xl">',
'                        {{ $welcomeExtra[''problems_title''] }}<br>',
'                        <span class="text-rose-400">{{ $welcomeExtra[''problems_title_accent''] }}</span>',
'                    </h2>',
'',
'                    <p class="mt-4 text-lg text-slate-400">',
'                        {{ $welcomeExtra[''problems_text''] }}',
'                    </p>',
'                </div>',
'',
'                <div class="mt-14 grid gap-6 lg:grid-cols-2">',
'                    <div class="relative h-full rounded-3xl border border-rose-500/20 bg-gradient-to-b from-rose-500/[0.07] to-transparent p-8">',
'                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-rose-500/40 to-transparent"></div>',
'',
'                        <div class="flex items-center gap-3">',
'                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/15 text-rose-400">!</div>',
'                            <div>',
'                                <div class="text-xs uppercase tracking-widest text-rose-400">{{ $welcomeExtra[''without_ai''] }}</div>',
'                                <div class="text-lg font-semibold text-white">{{ $welcomeExtra[''today_day''] }}</div>',
'                            </div>',
'                        </div>',
'',
'                        <div class="mt-6 space-y-4">',
'                            @foreach($problems as $p)',
'                                <div class="rounded-2xl border border-rose-500/10 bg-rose-500/[0.04] p-4 hover:border-rose-500/30 transition">',
'                                    <div class="flex gap-3">',
'                                        <span class="mt-1 flex h-6 w-6 items-center justify-center rounded-full bg-rose-500/20 text-rose-300">×</span>',
'                                        <div>',
'                                            <div class="font-semibold text-white">{{ $p[''pain''] }}</div>',
'                                            <p class="mt-1 text-sm text-slate-400">{{ $p[''detail''] }}</p>',
'                                            <div class="mt-2 inline-block rounded-md bg-rose-500/15 px-2 py-1 text-xs text-rose-300">{{ $p[''cost''] }}</div>',
'                                        </div>',
'                                    </div>',
'                                </div>',
'                            @endforeach',
'                        </div>',
'                    </div>',
'',
'                    <div class="relative h-full rounded-3xl border border-emerald-500/30 bg-gradient-to-b from-emerald-500/[0.08] to-transparent p-8 shadow-2xl">',
'                        <div class="absolute -right-4 -top-4 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 px-3 py-1 text-xs text-white">{{ $welcomeExtra[''with_ai''] }}</div>',
'',
'                        <div class="flex items-center gap-3">',
'                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-white">+</div>',
'                            <div>',
'                                <div class="text-xs uppercase text-emerald-400">{{ $welcomeExtra[''with_ai''] }}</div>',
'                                <div class="text-lg font-semibold text-white">{{ $welcomeExtra[''after_implementation''] }}</div>',
'                            </div>',
'                        </div>',
'',
'                        <div class="mt-6 space-y-4">',
'                            @foreach($solutions as $s)',
'                                <div class="rounded-2xl border border-emerald-500/15 bg-emerald-500/[0.05] p-4 hover:border-emerald-500/40 transition">',
'                                    <div class="flex gap-3">',
'                                        <span class="mt-1 flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-white">+</span>',
'                                        <div>',
'                                            <div class="font-semibold text-white">{{ $s[''win''] }}</div>',
'                                            <p class="mt-1 text-sm text-slate-400">{{ $s[''detail''] }}</p>',
'                                            <div class="mt-2 inline-block rounded-md bg-emerald-500/15 px-2 py-1 text-xs text-emerald-300">{{ $s[''metric''] }}</div>',
'                                        </div>',
'                                    </div>',
'                                </div>',
'                            @endforeach',
'                        </div>',
'',
'                        <a href="#demo" class="mt-6 flex justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:opacity-90 transition">',
'                            {{ $welcomeExtra[''try_same''] }}',
'                        </a>',
'                    </div>',
'                </div>',
'            </div>',
'        </section>'
)
Replace-Range $lines 1739 1880 $problemBlock

$stepsBlock = @(
'        @php',
'            $steps = $welcomeExtra[''steps''];',
'        @endphp',
'',
'        <section id="how-it-works" class="py-24 relative overflow-hidden">',
'',
'            <!-- Background -->',
'            <div class="absolute inset-0">',
'                <div class="w-[500px] h-[500px] bg-blue-500 opacity-10 blur-3xl rounded-full absolute -top-64 -right-64"></div>',
'                <div class="w-[400px] h-[400px] bg-purple-500 opacity-10 blur-3xl rounded-full absolute -bottom-32 -left-32"></div>',
'            </div>',
'',
'            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">',
'',
'                <!-- Header -->',
'                <div class="text-center mb-20">',
'                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6">',
'                        {{ $welcomeExtra[''how_title''] }} <span class="text-indigo-400">{{ $welcomeExtra[''how_title_accent''] }}</span>',
'                    </h2>',
'                    <p class="text-lg text-gray-400 max-w-2xl mx-auto">',
'                        {{ $welcomeExtra[''how_text''] }}',
'                    </p>',
'                </div>',
'',
'                <!-- Steps -->',
'                <div class="relative">',
'',
'                    <!-- Line desktop -->',
'                    <div class="hidden lg:block absolute top-24 left-[16%] right-[16%] h-0.5 bg-gradient-to-r from-blue-500 via-purple-500 to-emerald-500 opacity-40"></div>',
'',
'                    <div class="grid lg:grid-cols-3 gap-12 lg:gap-8">',
'',
'                        @foreach($steps as $index => $step)',
'                            <div class="relative group">',
'',
'                                <!-- Number -->',
'                                <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-12 h-12 rounded-2xl bg-slate-900 border-2 border-indigo-500 flex items-center justify-center text-white font-bold z-10">',
'                                    {{ $step[''number''] }}',
'                                </div>',
'',
'                                <!-- Card -->',
'                                <div class="rounded-3xl p-8 pt-14 text-center bg-white/5 backdrop-blur-lg border border-white/10 hover:border-indigo-500/30 transition">',
'                                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-gradient-to-br {{ $step[''color''] }} flex items-center justify-center text-white text-2xl shadow-xl group-hover:scale-110 transition">',
'                                        {{ $step[''icon''] }}',
'                                    </div>',
'',
'                                    <h3 class="text-xl font-semibold text-white mb-3">{{ $step[''title''] }}</h3>',
'                                    <p class="text-gray-400 mb-6">{{ $step[''description''] }}</p>',
'',
'                                    <div class="space-y-2">',
'                                        @foreach($step[''details''] as $detail)',
'                                            <div class="flex items-center justify-center gap-2 text-sm text-gray-400">',
'                                                <span>+</span><span>{{ $detail }}</span>',
'                                            </div>',
'                                        @endforeach',
'                                    </div>',
'',
'                                </div>',
'',
'                                @if($index < count($steps) - 1)',
'                                    <div class="lg:hidden flex justify-center py-6 text-gray-500">↓</div>',
'                                @endif',
'',
'                            </div>',
'                        @endforeach',
'',
'                    </div>',
'                </div>',
'',
'                <!-- CTA -->',
'                <div class="text-center mt-16">',
'                    <a href="#demo"',
'                       class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-2xl font-semibold shadow-lg hover:opacity-90 transition">',
'                        {{ $welcomeExtra[''try_now''] }}',
'                    </a>',
'                </div>',
'',
'            </div>',
'        </section>'
)
Replace-Range $lines 2021 2134 $stepsBlock

$faqBlock = @(
'        <section id="faq" class="faq-section reveal">',
'            <div class="max-w-3xl mx-auto">',
'                <div class="text-center mb-12">',
'                    <div class="badge badge-blue mb-4">{{ $welcomeExtra[''faq_badge''] }}</div>',
'                    <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $welcomeExtra[''faq_title''] }} <span class="text-gradient">{{ $welcomeExtra[''faq_title_accent''] }}</span></h2>',
'                    <p class="text-slate-400">{{ $welcomeExtra[''faq_text''] }}</p>',
'                </div>',
'                <div class="faq-list">',
'                    @foreach($welcomeExtra[''faq''] as $item)',
'                        <div class="faq-item">',
'                            <button class="faq-question" aria-expanded="false">',
'                                {{ $item[''question''] }}',
'                                <svg class="arrow w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>',
'                            </button>',
'                            <div class="faq-answer">{{ $item[''answer''] }}</div>',
'                        </div>',
'                    @endforeach',
'                </div>',
'            </div>',
'        </section>'
)
Replace-Range $lines 2136 2192 $faqBlock

$footerBlock = @(
'<!-- Footer -->',
'<footer class="py-16 px-4 mt-auto">',
'    <div class="max-w-7xl mx-auto">',
'        <div class="grid md:grid-cols-4 gap-8 mb-12">',
'            <div>',
'                <div class="flex items-center space-x-2 mb-4">',
'                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">',
'                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
'                    </svg>',
'                    <span class="text-xl font-bold glow-text">LegalAI Pro</span>',
'                </div>',
'                <p class="text-slate-400 text-sm leading-relaxed">{{ $welcomeExtra[''footer_about''] }}</p>',
'            </div>',
'            <div>',
'                <h4 class="font-semibold text-white mb-4">{{ $welcomeExtra[''footer_product''] }}</h4>',
'                <ul class="space-y-2 text-sm">',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ $welcomeExtra[''footer_features''] }}</a></li>',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ $welcomeExtra[''footer_pricing''] }}</a></li>',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">API</a></li>',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ $welcomeExtra[''footer_integrations''] }}</a></li>',
'                </ul>',
'            </div>',
'            <div>',
'                <h4 class="font-semibold text-white mb-4">{{ $welcomeExtra[''footer_resources''] }}</h4>',
'                <ul class="space-y-2 text-sm">',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ $welcomeExtra[''footer_docs''] }}</a></li>',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ $welcomeExtra[''footer_blog''] }}</a></li>',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ $welcomeExtra[''footer_webinars''] }}</a></li>',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ $welcomeExtra[''footer_status''] }}</a></li>',
'                </ul>',
'            </div>',
'            <div>',
'                <h4 class="font-semibold text-white mb-4">{{ $welcomeExtra[''footer_company''] }}</h4>',
'                <ul class="space-y-2 text-sm">',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ $welcomeExtra[''footer_about_link''] }}</a></li>',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ $welcomeExtra[''footer_careers''] }}</a></li>',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ $welcomeExtra[''footer_contacts''] }}</a></li>',
'                    <li><a href="#" class="text-slate-400 hover:text-white transition">{{ $welcomeExtra[''footer_partners''] }}</a></li>',
'                </ul>',
'            </div>',
'        </div>',
'        <div class="flex flex-col md:flex-row items-center justify-between pt-8 border-t border-slate-700">',
'            <p class="text-slate-400 text-sm">{{ __(''ui.copyright'') }}</p>',
'            <div class="flex space-x-6 mt-4 md:mt-0">',
'                <a href="#" class="text-slate-400 hover:text-white transition text-sm">{{ __(''ui.privacy_policy'') }}</a>',
'                <a href="#" class="text-slate-400 hover:text-white transition text-sm">{{ __(''ui.terms_of_use'') }}</a>',
'                <a href="#" class="text-slate-400 hover:text-white transition text-sm">{{ __(''ui.support'') }}</a>',
'            </div>',
'        </div>',
'    </div>',
'</footer>'
)
Replace-Range $lines 2619 2671 $footerBlock

Set-Content -Encoding UTF8 $path $lines
