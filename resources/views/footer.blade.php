<!-- Footer -->
@php
    use App\Http\Controllers\ToolController;

    $termsUrl   = \App\Support\LocaleRoute::route('terms');
    $privacyUrl = \App\Support\LocaleRoute::route('privacy');
    $refundUrl  = \App\Support\LocaleRoute::route('refund');
    $cookiesUrl = \App\Support\LocaleRoute::route('cookies');
    $dataSourcesUrl = \App\Support\LocaleRoute::route('data-sources');
    $blogUrl    = \App\Support\LocaleRoute::route('blog');
    $loginUrl   = \App\Support\LocaleRoute::route('login');
    $toolsUrl   = \App\Support\LocaleRoute::route('tools.index');

    // STAGING: footer Free Services Tools column is driven by ToolController::TOOLS —
    // add a tool to that registry and it shows up here automatically.
    $footerTools = ToolController::TOOLS;
    $footerCompare = array_slice(\App\Http\Controllers\ComparisonController::ITEMS, 0, 6, true);
    $compareUrl = \App\Support\LocaleRoute::route('compare.index');
    $footerChecks = \App\Http\Controllers\VinCheckController::CHECKS;
    $checksUrl = \App\Support\LocaleRoute::route('vincheck.index');
@endphp
<footer class="bg-primary-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            {{-- Column 1: Information + contacts --}}
            <div>
                <h2 class="text-lg font-bold mb-4">{{ __('index.footer.information') }}</h2>
                <ul class="space-y-2">
                    <li><a href="{{ $termsUrl }}" class="text-primary-200 hover:text-white transition">{{ __('index.footer.terms') }}</a></li>
                    <li><a href="{{ $privacyUrl }}" class="text-primary-200 hover:text-white transition">{{ __('index.footer.privacy') }}</a></li>
                    <li><a href="{{ $refundUrl }}" class="text-primary-200 hover:text-white transition">{{ __('index.footer.refund_policy') }}</a></li>
                    <li><a href="{{ $cookiesUrl }}" class="text-primary-200 hover:text-white transition">{{ __('nav.footer.cookie') }}</a></li>
                    <li><a href="{{ $dataSourcesUrl }}" class="text-primary-200 hover:text-white transition">{{ __('nav.footer.data_sources') }}</a></li>
                    <li><a href="{{ \App\Support\LocaleRoute::route('reviews') }}" class="text-primary-200 hover:text-white transition">{{ __('nav.footer.reviews') }}</a></li>
                    <li><a href="{{ \App\Support\LocaleRoute::route('money-back') }}" class="text-primary-200 hover:text-white transition">{{ __('nav.footer.money_back') }}</a></li>
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <li><a href="{{ route('dashboard') }}" class="text-primary-200 hover:text-white transition">{{ __('index.nav.account') }}</a></li>
                        @else
                            <li><a href="{{ route('panel') }}" class="text-primary-200 hover:text-white transition">{{ __('index.nav.account') }}</a></li>
                        @endif
                    @else
                        <li><a href="{{ $loginUrl }}" class="text-primary-200 hover:text-white transition">{{ __('index.nav.login') }}</a></li>
                    @endauth
                    <li class="pt-2">
                        <a href="mailto:support@cheapvinreport.email" class="text-primary-200 hover:text-white transition">
                            support@cheapvinreport.email
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 2: Free Services Tools (auto from registry) --}}
            <div>
                <h2 class="text-lg font-bold mb-4">{{ __('nav.footer.tools_col') }}</h2>
                <ul class="space-y-2">
                    @foreach($footerTools as $slug => $tool)
                        <li>
                            <a href="{{ \App\Support\LocaleRoute::route('tools.show', ['tool' => $slug]) }}"
                               class="text-primary-200 hover:text-white transition">
                                {{ __('tools.items.'.$slug.'.label') }}
                            </a>
                        </li>
                    @endforeach
                    <li class="pt-1">
                        <a href="{{ $toolsUrl }}" class="text-primary-100 font-medium hover:text-white transition">{{ __('nav.footer.all_tools') }} &rarr;</a>
                    </li>
                </ul>
            </div>

            {{-- Column 3: VIN Check Services (auto from VinCheckController::CHECKS) --}}
            <div>
                <h2 class="text-lg font-bold mb-4">{{ __('nav.footer.checks_col') }}</h2>
                <ul class="space-y-2">
                    @foreach($footerChecks as $slug => $c)
                        <li><a href="{{ \App\Support\LocaleRoute::route('vincheck.show', ['check' => $slug]) }}" class="text-primary-200 hover:text-white transition">{{ __('vincheck.checks.'.$slug.'.label') }}</a></li>
                    @endforeach
                    <li class="pt-1"><a href="{{ $checksUrl }}" class="text-primary-100 font-medium hover:text-white transition">{{ __('nav.footer.all_checks') }} &rarr;</a></li>
                </ul>
            </div>

            {{-- Column 3: Compare (auto from ComparisonController::ITEMS) --}}
            <div>
                <h2 class="text-lg font-bold mb-4">{{ __('nav.footer.compare_col') }}</h2>
                <ul class="space-y-2">
                    @foreach($footerCompare as $slug => $cmp)
                        <li><a href="{{ \App\Support\LocaleRoute::route('compare.show', ['competitor' => $slug]) }}" class="text-primary-200 hover:text-white transition">{{ __('compare.show.vs') }} {{ $cmp['name'] }}</a></li>
                    @endforeach
                    <li class="pt-1"><a href="{{ $compareUrl }}" class="text-primary-100 font-medium hover:text-white transition">{{ __('nav.footer.all_comparisons') }} &rarr;</a></li>
                </ul>
            </div>

            {{-- Column 3: Vehicle Tools — STAGING: fill as VIN-check landing pages are built --}}
            {{-- Column 4: Compare — STAGING: fill as comparison pages are built --}}

        </div>

        <div class="mt-10 border-t border-primary-800 pt-6 pb-6 text-sm text-primary-200">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    {{ __('index.footer.disclaimer', ['year' => now()->year]) }}
                </div>
                <div class="flex flex-wrap items-center gap-3 md:justify-end">
                    <img src="{{ asset('images/payment/visa.png') }}" alt="Visa" class="h-10 w-auto rounded bg-white p-1.5" loading="lazy">
                    <img src="{{ asset('images/payment/mastercard.png') }}" alt="Mastercard" class="h-10 w-auto rounded bg-white p-1.5" loading="lazy">
                    <img src="{{ asset('images/payment/prostir.png') }}" alt="Prostir" class="h-10 w-auto rounded bg-white p-1.5" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
