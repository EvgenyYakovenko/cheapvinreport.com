@include('header')
@php
    $loginUrl = \App\Support\LocaleRoute::route('login');

    $currencyCode = strtoupper($currency ?? 'usd');
    $reportPrices = [
        $carfaxPrice,
        $autocheckPrice,
        $auctionsPrice,
        $stickerPrice,
    ];
    $reportPrices = array_filter(array_map('floatval', $reportPrices), function ($price) {
        return $price > 0;
    });

    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => 'Cheap Carfax Report',
        'description' => 'Instant vehicle history check and NMVTIS data. Get affordable reports with ownership, damage, and salvage history.',
        'provider' => [
            '@type' => 'Organization',
            'name' => 'CheapVINReport',
        ],
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => 4.8,
            'bestRating' => 5,
            'reviewCount' => 66,
        ],
        'offers' => array_filter([
            '@type' => 'AggregateOffer',
            'url' => url()->current(),
            'priceCurrency' => $currencyCode,
            'lowPrice' => 1.5,
            'highPrice' => 3,
            'offerCount' => 4,
        ], function ($value) {
            return $value !== null;
        }),
    ];
@endphp
<script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<main>
    <!-- Hero Section -->
    <style>@media (min-width:1024px){.hero-grid{grid-template-columns:3fr 2fr}}</style>
    <section class="bg-[#f8f9fa] py-16 lg:py-24">
        <div class="container mx-auto px-4">
            <div class="hero-grid grid gap-12 items-start max-w-7xl mx-auto">
                <!-- Left Column: Text and VIN Check Form -->
                <div>
                    {{-- STAGING: Etsy rating eyebrow (links to real Etsy shop) --}}
                    <a href="https://www.etsy.com/shop/Cheapvinreport/reviews" target="_blank" rel="noopener"
                       class="inline-block mb-2 text-sm text-primary-700 hover:opacity-80 transition">★★★★★ 5.0 · 32 reviews on Etsy</a>
                    <h1 class="text-3xl font-extrabold mb-2 text-gray-900">{{ __('index.hero.title') }}</h1>
                    <h2 class="text-lg text-gray-600 mb-5">{!! str_replace('$3.00', '<strong class="text-primary-600">$3.00</strong>', __('index.hero.subtitle')) !!}</h2>

                    <!-- VIN Check Form -->
                    <form id="vin-checker-form">
                        <div class="flex flex-col sm:flex-row gap-2 mb-5">
                            <input type="text"
                                   class="flex-1 px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900"
                                   placeholder="{{ __('index.vin_check.placeholder') }}" id="vin" name="vin" required>
                            <button type="submit"
                                    class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition"
                                    id="checkVin">
                                {{ __('index.vin_check.button') }}
                            </button>
                        </div>
                        <p class="text-gray-600 text-sm">{{ __('index.vin_check.hint') }}</p>

                        <!-- hCaptcha widget container -->
                        @if(config('hcaptcha.site_key'))
                            @php
                                // Test mode: show visible hCaptcha
                                // Works only in dev environment (APP_ENV=local) for safety
                                $isLocalEnv = app()->environment('local');
                                $testCaptchaMode = $isLocalEnv && (request()->get('test_captcha') == '1' || config('hcaptcha.test_mode', false));
                            @endphp
                            <div id="hcaptcha-container" style="{{ $testCaptchaMode ? '' : 'display: none;' }}"></div>
                            @if($testCaptchaMode)
                                <p class="text-yellow-600 text-sm mt-2 mb-2">
                                    ⚠️ <strong>hCaptcha test mode:</strong> The widget is shown for
                                    testing (dev environment only).
                                </p>
                            @endif
                        @endif

                        <div class="mt-4 hidden" id="vinSpinner">
                            <div class="flex items-center text-gray-700">
                                <div
                                    class="inline-block w-4 h-4 border-2 border-primary-600 border-t-transparent rounded-full animate-spin"></div>
                                <span class="ml-2">{{ __('index.vin_check.checking') }}</span>
                            </div>
                        </div>

                        <div id="data-report" class="pt-2"></div>
                    </form>

                    @include('partials.home-trust-badges')

                    <!-- Checkout Form (hidden by default) -->
                    <div class="bg-white rounded-lg p-6 mt-4 border-2 border-gray-300" id="checkout-form">
                        <h3 class="text-gray-900 text-xl font-bold mb-4">{{ __('index.checkout.title') }}</h3>
                        <form id="purchaseForm">
                            <input type="hidden" id="hiddenVin" name="vin" value="">

                            <div class="mb-4">
                                <label for="email"
                                       class="block text-gray-900 font-medium mb-2">{{ __('index.checkout.email_label') }}
                                    <span class="text-red-600">{{ __('index.checkout.email_required') }}</span></label>
                                <input type="email"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900"
                                       id="email" name="email" value="{{ auth()->user()->email ?? '' }}" required>
                                <p class="text-gray-600 text-sm mt-1">{{ __('index.checkout.email_hint') }}</p>
                            </div>

                            <div class="mb-4">
                                <label
                                    class="block text-gray-900 font-medium mb-2">{{ __('index.checkout.report_type_label') }}</label>
                                <input type="hidden" id="report_type" name="report_type" value="carfax">
                                <div class="grid grid-cols-2 gap-3">
                                    <label
                                        class="report-type-btn border-2 border-primary-500 bg-primary-50 rounded-lg p-4 cursor-pointer transition hover:border-primary-600 hover:shadow-md flex items-start gap-3"
                                        data-type="carfax" data-price="{{ $carfaxPrice }}">
                                        <input type="radio" name="report_type_radio" class="mt-1" checked>
                                        <div class="flex flex-col flex-1">
                                            <strong class="text-gray-900 mb-1">Carfax</strong>
                                            <span
                                                class="text-gray-600 text-sm">{{ $currencySymbol }}{{ $carfaxPrice }}</span>
                                        </div>
                                    </label>
                                    <label
                                        class="report-type-btn border-2 border-gray-300 rounded-lg p-4 cursor-pointer transition hover:border-primary-500 hover:shadow-md hover:bg-primary-50 flex items-start gap-3"
                                        data-type="autocheck" data-price="{{ $autocheckPrice }}">
                                        <input type="radio" name="report_type_radio" class="mt-1">
                                        <div class="flex flex-col flex-1">
                                            <strong class="text-gray-900 mb-1">AutoCheck</strong>
                                            <span
                                                class="text-gray-600 text-sm">{{ $currencySymbol }}{{ $autocheckPrice }}</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <span class="font-semibold text-gray-900">Price:</span> <span id="reportPrice"
                                                                                                  class="text-gray-900">{{ $currencySymbol }}{{ $carfaxPrice }}</span>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="mb-4">
                                <label class="block text-gray-900 font-bold mb-3">Payment Method</label>
                                <div class="flex flex-col gap-3">
                                    @auth
                                        <!-- Report Balance (for authenticated users only) - PRIORITY METHOD -->
                                        <label
                                            class="payment-method-card border-2 border-gray-300 rounded-lg p-4 cursor-pointer transition hover:border-primary-500 hover:shadow-md hover:bg-primary-50 flex items-start gap-3"
                                            for="payment_report_balance" id="payment_report_balance_card">
                                            <input class="mt-1" type="radio" name="payment_method"
                                                   id="payment_report_balance" value="report_balance"
                                                   @if(($userReportBalance ?? 0) >= 1) checked @endif>
                                            <div class="flex justify-between items-center flex-1">
                                                <div class="flex-grow">
                                                    <strong class="text-gray-900 block">Report Balance</strong>
                                                    <span class="text-gray-600 text-sm">
                                                    Available: <span
                                                            id="userReportBalanceDisplay">{{ number_format($userReportBalance ?? 0) }}</span> reports
                                                </span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                <span class="text-primary-600 payment-check hidden">
                                                    <x-icons.check-circle class="w-5 h-5" aria-hidden="true"/>
                                                </span>
                                                    <span class="bg-red-600 text-white text-xs px-2 py-1 rounded hidden"
                                                          id="reportBalanceInsufficient">Insufficient funds</span>
                                                </div>
                                            </div>
                                        </label>
                                    @else
                                        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                                            <p class="text-yellow-800 text-sm">
                                                <a href="{{ $loginUrl }}"
                                                   class="text-primary-600 hover:underline font-medium">Log in</a> to
                                                pay
                                                with balance
                                            </p>
                                        </div>
                                    @endauth

                                    <!-- Card Payments: Platon -->
                                    <label
                                        class="payment-method-card border-2 border-gray-300 rounded-lg p-4 cursor-pointer transition hover:border-primary-500 hover:shadow-md hover:bg-primary-50 flex items-center gap-3"
                                        for="payment_platon">
                                        <input class="mt-0" type="radio" name="payment_method" id="payment_platon"
                                               value="platon"
                                               @guest checked @endguest
                                               @auth @if(($userReportBalance ?? 0) < 1) checked @endif @endauth>
                                        <div class="flex justify-between items-center flex-1">
                                            <div>
                                                <strong class="text-gray-900 block">Card Payments (Platon)</strong>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center gap-2">
                                                    <img src="{{ asset('images/google-pay.png') }}" alt="Google Pay"
                                                         class="h-10 w-auto">
                                                    <img src="{{ asset('images/apple-pay.png') }}" alt="Apple Pay"
                                                         class="h-10 w-auto">
                                                </div>
                                                <span class="text-primary-600 payment-check hidden">
                                                    <x-icons.check-circle class="w-5 h-5" aria-hidden="true"/>
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <button type="submit"
                                        class="w-full px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold text-lg rounded-lg transition shadow-sm"
                                        id="purchaseButton">
                                    <span id="buttonText">{{ __('index.checkout.button_text') }}</span> (<span
                                        id="buttonPrice">{{ $currencySymbol }}{{ $carfaxPrice }}</span>)
                                </button>
                                <p class="mt-2 text-xs text-gray-500 text-center" id="purchasePaymentNote">
                                    {!! __('index.bundles.payment_note', ['amount' => '<span id="purchaseApprox">-</span>', 'rate' => '<strong>$1 ≈ ₴<span id="purchaseRate">-</span></strong>']) !!}
                                </p>
                            </div>

                            <div id="purchaseMessageDiv" class="mt-4 hidden"></div>
                        </form>
                    </div>

                    {{--                    <div class="flex justify-center mt-4">--}}
                    {{--                        <a class="text-gray-600 hover:text-gray-900 underline" href="#example" id="exampleLink">--}}
                    {{--                            {{ __('index.checkout.example_link') }}--}}
                    {{--                        </a>--}}
                    {{--                    </div>--}}
                </div>
                <!-- Right Column: customer quote -->
                <div class="hidden lg:block">
                    @include('partials.home-hero-quote')
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="max-w-3xl mx-auto text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">{{ __('index.report_details.title') }}</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                        <div class="rounded-2xl border border-gray-200 bg-[#f8f9fa] p-6 overflow-hidden">
                            <div class="w-14 h-14 shrink-0 overflow-hidden rounded-full bg-primary-100 flex items-center justify-center leading-none mb-5">
                                <x-icons.exclamation-triangle class="w-6 h-6 shrink-0 text-primary-600"/>
                            </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('index.report_details.accident_information.title') }}</h3>
                        <p class="text-gray-600 leading-7">
                            {{ __('index.report_details.accident_information.description') }}
                        </p>
                    </div>

                        <div class="rounded-2xl border border-gray-200 bg-[#f8f9fa] p-6 overflow-hidden">
                            <div class="w-14 h-14 shrink-0 overflow-hidden rounded-full bg-primary-100 flex items-center justify-center leading-none mb-5">
                                <x-icons.check-circle class="w-6 h-6 shrink-0 text-primary-600"/>
                            </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('index.report_details.service_history.title') }}</h3>
                        <p class="text-gray-600 leading-7">
                            {{ __('index.report_details.service_history.description') }}
                        </p>
                    </div>

                        <div class="rounded-2xl border border-gray-200 bg-[#f8f9fa] p-6 overflow-hidden">
                            <div class="w-14 h-14 shrink-0 overflow-hidden rounded-full bg-primary-100 flex items-center justify-center leading-none mb-5">
                                <x-icons.information-circle class="w-6 h-6 shrink-0 text-primary-600"/>
                            </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('index.report_details.purpose_of_use.title') }}</h3>
                        <p class="text-gray-600 leading-7">
                            {{ __('index.report_details.purpose_of_use.description') }}
                        </p>
                    </div>

                        <div class="rounded-2xl border border-gray-200 bg-[#f8f9fa] p-6 overflow-hidden">
                            <div class="w-14 h-14 shrink-0 overflow-hidden rounded-full bg-primary-100 flex items-center justify-center leading-none mb-5">
                                <x-icons.credit-card class="w-6 h-6 shrink-0 text-primary-600"/>
                            </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ __('index.report_details.ownership_history.title') }}</h3>
                        <p class="text-gray-600 leading-7">
                            {{ __('index.report_details.ownership_history.description') }}
                        </p>
                    </div>
                </div>

                <div class="mt-12 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ __('index.report_details.what_is.title') }}</h3>
                    <div class="space-y-4 text-gray-600 leading-7">
                        <p>
                            {{ __('index.report_details.what_is.paragraph_1') }}
                        </p>
                        <p>
                            {{ __('index.report_details.what_is.paragraph_2') }}
                        </p>
                        <p>
                            {{ __('index.report_details.what_is.paragraph_3') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Report Balance Purchase Plans Section -->
    {{-- STAGING: customer reviews (6) — full list on /reviews --}}
    @include('partials.home-how-it-works')
    @include('partials.home-sample-report')
    @include('partials.home-price-compare')

    @include('partials.reviews', ['limit' => 6])

    <section id="bundles" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-center text-3xl lg:text-4xl font-bold text-gray-900 mb-8">{{ __('index.bundles.title') }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                    <!-- Starter Pack (5 Reports) -->
                    <div
                        class="bg-white rounded-lg border-2 border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 text-center">{{ __('index.bundles.starter_pack') }}</h3>
                            <div class="mb-4 text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-1">
                                    <span id="starterBundlePrice">-</span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    {!! str_replace(':count', '<span id="starterBundleCount">5</span>', __('index.bundles.for_reports')) !!}
                                </p>
                                <p class="text-sm text-primary-600 font-semibold mt-1">
                                    <span id="starterBundlePricePerReport">-</span> {{ __('index.bundles.per_report') }}
                                    <span id="starterBundleSavePercent"
                                          class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs ml-2"></span>
                                </p>
                            </div>

                            <button type="button" id="starterBundleButton"
                                    class="w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                                {{ str_replace(':count', '5', __('index.bundles.get_reports_now')) }}
                            </button>
                            <p class="mt-2 text-xs text-gray-500 text-center" id="starterBundlePaymentNote">
                                {!! __('index.bundles.payment_note', ['amount' => '<span id="starterBundleApprox">-</span>', 'rate' => '<strong>$1 ≈ ₴<span id="starterBundleRate">-</span></strong>']) !!}
                            </p>
                        </div>
                    </div>

                    <!-- Most Popular (25 Reports) -->
                    <div class="bg-white rounded-lg border-2 border-primary-500 shadow-lg relative">
                        <div class="absolute top-0 left-0 w-full flex justify-center -translate-y-1/2 px-4">
                            <span
                                class="bg-primary-600 text-white px-4 py-1 rounded-full text-sm font-semibold text-center leading-tight whitespace-normal max-w-[90%]">⭐️ {{ __('index.bundles.most_popular') }}</span>
                        </div>
                        <div class="p-6 pt-10">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 text-center">{{ __('index.bundles.most_popular') }}</h3>
                            <div class="mb-4 text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-1">
                                    <span id="popularBundlePrice">-</span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    {!! str_replace(':count', '<span id="popularBundleCount">25</span>', __('index.bundles.for_reports')) !!}
                                </p>
                                <p class="text-sm text-primary-600 font-semibold mt-1">
                                    <span id="popularBundlePricePerReport">-</span> {{ __('index.bundles.per_report') }}
                                    <span id="popularBundleSavePercent"
                                          class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs ml-2"></span>
                                </p>
                            </div>

                            <button type="button" id="popularBundleButton"
                                    class="w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                                {{ str_replace(':count', '25', __('index.bundles.get_reports_now')) }}
                            </button>
                            <p class="mt-2 text-xs text-gray-500 text-center" id="popularBundlePaymentNote">
                                {!! __('index.bundles.payment_note', ['amount' => '<span id="popularBundleApprox">-</span>', 'rate' => '<strong>$1 ≈ ₴<span id="popularBundleRate">-</span></strong>']) !!}
                            </p>
                        </div>
                    </div>

                    <!-- Best Value (100 Reports) -->
                    <div
                        class="bg-white rounded-lg border-2 border-gray-200 shadow-sm hover:shadow-md transition-shadow relative">
                        <div class="absolute top-0 left-0 w-full flex justify-center -translate-y-1/2 px-4">
                            <span
                                class="bg-green-700 text-white px-4 py-1 rounded-full text-sm font-semibold text-center leading-tight whitespace-normal max-w-[90%]">💎 {{ __('index.bundles.best_value') }}</span>
                        </div>
                        <div class="p-6 pt-10">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 text-center">{{ __('index.bundles.best_value') }}</h3>
                            <div class="mb-4 text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-1">
                                    <span id="valueBundlePrice">-</span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    {!! str_replace(':count', '<span id="valueBundleCount">100</span>', __('index.bundles.for_reports')) !!}
                                </p>
                                <p class="text-sm text-primary-600 font-semibold mt-1">
                                    <span id="valueBundlePricePerReport">-</span> {{ __('index.bundles.per_report') }}
                                    <span id="valueBundleSavePercent"
                                          class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs ml-2"></span>
                                </p>
                            </div>

                            <button type="button" id="valueBundleButton"
                                    class="w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                                {{ str_replace(':count', '100', __('index.bundles.get_reports_now')) }}
                            </button>
                            <p class="mt-2 text-xs text-gray-500 text-center" id="valueBundlePaymentNote">
                                {!! __('index.bundles.payment_note', ['amount' => '<span id="valueBundleApprox">-</span>', 'rate' => '<strong>$1 ≈ ₴<span id="valueBundleRate">-</span></strong>']) !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="bg-gray-50 py-16" id="faq">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-center text-3xl font-bold mb-10 text-gray-900">{{ __('index.faq.title') }}</h2>
                @php
                    $faqItems = trans('index.faq.items');
                @endphp
                <div class="space-y-3">
                    @foreach($faqItems as $faqIndex => $faqItem)
                        @php
                            $collapseId = 'faq-collapse-' . ($faqIndex + 1);
                            $isOpen = $faqIndex === 0;
                        @endphp
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-5 cursor-pointer hover:bg-gray-50 transition"
                                 onclick="document.getElementById('{{ $collapseId }}').classList.toggle('hidden'); this.querySelector('[data-faq-chevron]').classList.toggle('rotate-180'); this.setAttribute('aria-expanded', this.getAttribute('aria-expanded') === 'true' ? 'false' : 'true');"
                                 role="button" aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <h3 class="min-w-0 flex-1 font-semibold text-gray-900">{{ $faqItem['question'] }}</h3>
                                    <x-icons.chevron-down class="w-5 h-5 shrink-0 text-gray-500 transition-transform duration-200 {{ $isOpen ? 'rotate-180' : '' }}"
                                                          data-faq-chevron aria-hidden="true"/>
                                </div>
                            </div>
                            <div id="{{ $collapseId }}" class="{{ $isOpen ? '' : 'hidden ' }}bg-gray-50 px-5 py-4 border-t border-gray-200">
                                <div class="text-gray-700 space-y-4 [&_ul]:list-disc [&_ul]:pl-6 [&_li]:mb-2 [&_p]:mb-4 last:[&_p]:mb-0">
                                    {!! $faqItem['answer'] !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    // hCaptcha variables
    let hcaptchaWidgetId = null;
    const hcaptchaSiteKey = @json(config('hcaptcha.site_key', ''));
    let pendingVinCheck = null;
    const hcaptchaTrustStorageKey = 'vin_check_hcaptcha_trust_until';
    const hcaptchaTrustDurationMs = 10 * 60 * 1000;

    function getHcaptchaTrustUntil() {
        const rawValue = sessionStorage.getItem(hcaptchaTrustStorageKey);
        const parsed = Number(rawValue);
        return Number.isFinite(parsed) ? parsed : 0;
    }

    function hasHcaptchaTrust() {
        return getHcaptchaTrustUntil() > Date.now();
    }

    function setHcaptchaTrust() {
        sessionStorage.setItem(hcaptchaTrustStorageKey, String(Date.now() + hcaptchaTrustDurationMs));
    }

    function clearHcaptchaTrust() {
        sessionStorage.removeItem(hcaptchaTrustStorageKey);
    }

    // hCaptcha test mode (visible version for testing)
    // Works only in dev environment for safety
    @php
        $isLocalEnv = app()->environment('local');
        $testCaptchaMode = $isLocalEnv && (request()->get('test_captcha') == '1' || config('hcaptcha.test_mode', false));
    @endphp
    const hcaptchaTestMode = @json($testCaptchaMode);

    // Report Balance Purchase Plans
    @php
        $packagePrices = null;
        $hasError = false;

        if ($topupReportBalancePrice) {
            if (is_array($topupReportBalancePrice)) {
                $packagePrices = $topupReportBalancePrice;
            } elseif (is_string($topupReportBalancePrice)) {
                $decoded = json_decode($topupReportBalancePrice, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $packagePrices = $decoded;
                } else {
                    $hasError = true;
                }
            } else {
                $hasError = true;
            }
        } else {
            $hasError = true;
        }

        $currency = $currency ?? 'usd';
        $currencySymbols = [
            'usd' => '$',
            'uah' => '₴',
            'pln' => 'zł',
            'kzt' => '₸',
        ];
        $currencyDisplay = $currencySymbols[$currency] ?? '$';
    @endphp

    const balancePackagePrices = @json($packagePrices);
    const balanceCurrentCurrency = @json($currency ?? 'usd');
    const balanceCurrencySymbol = @json($currencyDisplay ?? 'USD ($)');
    const balanceHasPriceError = @json($hasError);
    const isAuthenticated = @json(auth()->check());
    const conversionRateToUah = @json($conversionRateToUah ?? null);
    const carfaxSingleReportPrice = @json($carfaxPrice ?? null);
    const hasUahRate = typeof conversionRateToUah === 'number' && conversionRateToUah > 0 && conversionRateToUah !== 1;

    function formatApproxAmount(value) {
        const rounded = Math.round(value * 100) / 100;
        const hasDecimals = Math.abs(rounded % 1) > 0;
        return rounded.toLocaleString('en-US', {
            minimumFractionDigits: hasDecimals ? 2 : 0,
            maximumFractionDigits: 2
        });
    }

    // Register hCaptcha widget initialization.
    if (typeof window.registerHCaptchaInit === 'function') {
        window.registerHCaptchaInit(function () {
            if (hcaptchaSiteKey && typeof hcaptcha !== 'undefined') {
                const container = document.getElementById('hcaptcha-container');
                if (container) {
                    // Define callbacks inside DOMContentLoaded to access local variables
                    // For now, we'll use a workaround by storing references
                    hcaptchaWidgetId = hcaptcha.render('hcaptcha-container', {
                        sitekey: hcaptchaSiteKey,
                        size: hcaptchaTestMode ? 'normal' : 'invisible',
                        callback: function (token, key) {
                            // Call the global submitVinCheck function
                            if (window.submitVinCheck) {
                                window.submitVinCheck({ forceCaptcha: true });
                            }
                        },
                        'error-callback': function (err) {
                            console.error('hCaptcha error callback:', err);
                            const vinSpinner = document.getElementById('vinSpinner');
                            const checkVinBtn = document.getElementById('checkVin');
                            const dataReport = document.getElementById('data-report');
                            if (vinSpinner) vinSpinner.classList.add('hidden');
                            if (checkVinBtn) {
                                checkVinBtn.disabled = false;
                                checkVinBtn.textContent = '{{ __('index.vin_check.button') }}';
                            }
                            if (dataReport && window.showError) {
                                window.showError('{{ __('index.vin_check.captcha_error') }}');
                            }
                        },
                        'expired-callback': function () {
                            console.warn('hCaptcha token expired');
                            // Token expired, user needs to try again
                        }
                    });
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const currencySymbol = '{{ $currencySymbol }}';
        const vinForm = document.getElementById('vin-checker-form');
        const vinInput = document.getElementById('vin');
        const checkVinBtn = document.getElementById('checkVin');
        const vinSpinner = document.getElementById('vinSpinner');
        const dataReport = document.getElementById('data-report');
        const checkoutForm = document.getElementById('checkout-form');
        const purchaseForm = document.getElementById('purchaseForm');
        const hiddenVinInput = document.getElementById('hiddenVin');
        const purchaseButton = document.getElementById('purchaseButton');
        const purchaseMessageDiv = document.getElementById('purchaseMessageDiv');
        const reportTypeSelect = document.getElementById('report_type');
        const reportPriceSpan = document.getElementById('reportPrice');
        const buttonPriceSpan = document.getElementById('buttonPrice');
        const purchaseNoteEl = document.getElementById('purchasePaymentNote');
        const purchaseApproxEl = document.getElementById('purchaseApprox');
        const purchaseRateEl = document.getElementById('purchaseRate');
        const emailInput = document.getElementById('email');
        const vinFocusBtn = document.getElementById('vinFocusBtn');

        function submitPaymentForm(paymentForm) {
            const form = document.createElement('form');
            form.method = paymentForm.method || 'POST';
            form.action = paymentForm.action;
            form.acceptCharset = 'UTF-8';
            form.style.display = 'none';

            Object.entries(paymentForm.fields || {}).forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value === null || value === undefined ? '' : String(value);
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        function getSelectedReportPrice() {
            const selectedRadio = document.querySelector('input[name="report_type_radio"]:checked');
            if (!selectedRadio) return null;
            const label = selectedRadio.closest('.report-type-btn');
            if (!label) return null;
            const priceValue = parseFloat(label.getAttribute('data-price'));
            return Number.isFinite(priceValue) ? priceValue : null;
        }

        function updatePurchaseApprox(price) {
            if (purchaseApproxEl && purchaseNoteEl && hasUahRate) {
                purchaseApproxEl.textContent = '₴' + formatApproxAmount(price * conversionRateToUah);
                if (purchaseRateEl) purchaseRateEl.textContent = conversionRateToUah.toFixed(2);
                purchaseNoteEl.style.display = '';
            } else if (purchaseNoteEl) {
                purchaseNoteEl.style.display = 'none';
            }
        }

        // Make submitVinCheck available globally
        window.submitVinCheck = async function (options = {}) {
            const forceCaptcha = Boolean(options.forceCaptcha);
            const vin = pendingVinCheck;
            let lastErrorMessage = null;

            // Show spinner
            vinSpinner.classList.remove('hidden');
            checkVinBtn.disabled = true;
            checkVinBtn.textContent = '{{ __('index.vin_check.checking') }}';
            dataReport.innerHTML = '';
            checkoutForm.classList.add('hidden');
            checkoutForm.classList.remove('show');
            checkoutForm.style.display = '';

            // Get hCaptcha token if available
            let hcaptchaToken = null;
            const shouldAttachCaptchaToken = forceCaptcha || !hasHcaptchaTrust();
            if (shouldAttachCaptchaToken && hcaptchaWidgetId !== null && typeof hcaptcha !== 'undefined') {
                try {
                    hcaptchaToken = hcaptcha.getResponse(hcaptchaWidgetId);
                } catch (e) {
                    console.warn('Could not get hCaptcha token:', e);
                }
            }

            try {
                const requestBody = {vin: vin};
                if (hcaptchaToken) {
                    requestBody['h-captcha-response'] = hcaptchaToken;
                }

                const response = await fetch('{{ route("check-vin") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestBody)
                });

                // Check that response is successful before parsing JSON
                if (!response.ok) {
                    let errorText = '';
                    let errorData = null;
                    try {
                        errorData = await response.json();
                        lastErrorMessage = errorData.message || errorData.error || null;
                    } catch (parseError) {
                        try {
                            errorText = await response.text();
                        } catch (readError) {
                            errorText = '';
                        }
                    }
                    if (errorData && errorData.captcha_required === true) {
                        clearHcaptchaTrust();
                        if (hcaptchaSiteKey && hcaptchaWidgetId !== null && typeof hcaptcha !== 'undefined') {
                            try {
                                hcaptcha.execute(hcaptchaWidgetId);
                                return;
                            } catch (captchaExecuteError) {
                                console.error('hCaptcha execute retry error:', captchaExecuteError);
                            }
                        }
                    }

                    console.error('VIN Check HTTP Error:', {
                        status: response.status,
                        statusText: response.statusText,
                        message: lastErrorMessage,
                        body: errorText
                    });
                    throw new Error(lastErrorMessage || errorText || `HTTP ${response.status}: ${response.statusText}`);
                }

                let data;
                try {
                    data = await response.json();
                } catch (jsonError) {
                    console.error('VIN Check JSON Parse Error:', jsonError);
                    const text = await response.text();
                    console.error('Response text:', text);
                    lastErrorMessage = 'Invalid JSON response from server';
                    throw new Error('Invalid JSON response from server');
                }

                vinSpinner.classList.add('hidden');
                checkVinBtn.disabled = false;
                checkVinBtn.textContent = '{{ __('index.vin_check.button') }}';

                // Check response success - response.ok and data.success must be true
                // More flexible success check
                const isSuccess = response.ok && (
                    data.success === true ||
                    data.success === 'true' ||
                    data.success === 1 ||
                    (data.vin && data.vehicle) // If vin and vehicle exist, consider successful
                );

                if (isSuccess) {
                    if (hcaptchaToken) {
                        setHcaptchaTrust();
                        if (hcaptchaWidgetId !== null && typeof hcaptcha !== 'undefined') {
                            try {
                                hcaptcha.reset(hcaptchaWidgetId);
                            } catch (resetErr) {
                                console.warn('hCaptcha reset error:', resetErr);
                            }
                        }
                    }

                    // Show check result with full information
                    const vinDisplay = data.vin || vin;
                    const vehicleDisplay = data.vehicle || 'N/A';
                    const autocheckRecords = data.autocheck_records || 0;
                    const carfaxRecords = data.carfax_records || 0;
                    const auctionRecord = data.auction_record ?? false;
                    const stickerReport = data.sticker_report || false;
                    const carfaxAvailable = data.carfax_available || false;
                    const autocheckAvailable = data.autocheck_available || false;
                    const isAvailable = data.is_available || false;

                    dataReport.innerHTML = `
                            <div class="bg-white border-2 border-primary rounded-lg p-6 mt-4 shadow-lg">
                                <h3 class="text-primary text-xl font-bold mb-4">
                                    {{ __('index.vin_check.found_title') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="mb-2 text-gray-800"><strong>{{ __('index.vin_check.vehicle') }}:</strong> ${vehicleDisplay}</p>
                                        <p class="mb-0 text-gray-800"><strong>VIN:</strong> <code class="bg-gray-100 px-2 py-1 rounded">${vinDisplay}</code></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <div class="border-2 border-blue-500 rounded-lg p-3">
                                            <h4 class="font-semibold mb-1 text-gray-800">CARFAX</h4>
                                            <p class="mb-0 text-gray-600 text-sm">Records: <strong>${carfaxRecords}</strong></p>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="border-2 border-cyan-500 rounded-lg p-3">
                                            <h4 class="font-semibold mb-1 text-gray-800">AutoCheck</h4>
                                            <p class="mb-0 text-gray-600 text-sm">Records: <strong>${autocheckRecords}</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                    // Save VIN and show purchase form
                    hiddenVinInput.value = vinDisplay;
                    updateReportTypeAvailability('carfax', Number(carfaxRecords) > 0);
                    checkoutForm.classList.remove('hidden');
                    checkoutForm.classList.add('show');
                    checkoutForm.style.display = '';

                    // Check payment methods availability when showing form
                    const selectedButton = document.querySelector('.report-type-btn.border-primary-500');
                    const selectedReportPrice = getSelectedReportPrice();
                    if (selectedReportPrice !== null) {
                        if (typeof checkPaymentMethodAvailability === 'function') {
                            checkPaymentMethodAvailability(selectedReportPrice);
                        }
                        updatePurchaseApprox(selectedReportPrice);
                    }

                    // Scroll to purchase form
                    setTimeout(() => {
                        checkoutForm.scrollIntoView({behavior: 'smooth', block: 'nearest'});
                    }, 300);
                } else {
                    showError(data.message || data.error || '{{ __('index.vin_check.not_found') }}');
                    checkoutForm.classList.add('hidden');
                    checkoutForm.classList.remove('show');
                }
            } catch (error) {
                console.error('VIN Check Exception:', error);
                vinSpinner.classList.add('hidden');
                checkVinBtn.disabled = false;
                checkVinBtn.textContent = '{{ __('index.vin_check.button') }}';
                showError(lastErrorMessage || '{{ __('index.vin_check.error') }}');
                checkoutForm.style.display = '';
                checkoutForm.classList.remove('show');
                checkoutForm.classList.add('hidden');
            }
        };

        // VIN Check form submission
        vinForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const vin = vinInput.value.trim();

            // Validation
            if (!vin || vin.length !== 17) {
                showError('{{ __('index.vin_check.invalid_length_exact') }}');
                return;
            }

            if (!/^[a-zA-Z0-9]+$/.test(vin)) {
                showError('{{ __('index.vin_check.invalid_chars') }}');
                return;
            }

            // Store VIN for later submission
            pendingVinCheck = vin;

            // If hCaptcha is enabled and widget is initialized
            if (hcaptchaSiteKey && hcaptchaWidgetId !== null && typeof hcaptcha !== 'undefined') {
                if (!hasHcaptchaTrust()) {
                    if (hcaptchaTestMode) {
                        // In test mode (visible hCaptcha) wait for the user to complete the challenge
                        // Callback is triggered automatically after the challenge is completed
                        // Show a hint to the user
                        if (dataReport) {
                            dataReport.innerHTML = '<div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-3" role="alert">Please complete the hCaptcha check above to continue.</div>';
                        }
                    } else {
                        // In invisible mode, run the check automatically
                        try {
                            hcaptcha.execute(hcaptchaWidgetId);
                        } catch (err) {
                            console.error('hCaptcha execute error:', err);
                            // If hCaptcha fails, submit without it
                            submitVinCheck();
                        }
                    }
                } else {
                    submitVinCheck();
                }
            } else {
                // If hCaptcha is not available, submit directly
                submitVinCheck();
            }
        });

        // Report type button selection
        const reportTypeButtons = document.querySelectorAll('.report-type-btn');
        const reportTypeInput = document.getElementById('report_type');
        const reportTypeRadios = document.querySelectorAll('input[name="report_type_radio"]');

        function selectReportType(label) {
            if (!label || label.getAttribute('aria-disabled') === 'true') {
                return;
            }

            const radio = label.querySelector('input[name="report_type_radio"]');
            if (!radio || radio.disabled) {
                return;
            }

            // Remove selection from all buttons
            reportTypeButtons.forEach(btn => {
                btn.classList.remove('border-primary-500', 'bg-primary-50');
                btn.classList.add('border-gray-300');
            });

            // Add selection to checked button
            radio.checked = true;
            label.classList.add('border-primary-500', 'bg-primary-50');
            label.classList.remove('border-gray-300');

            // Update hidden input and price
            const reportType = label.getAttribute('data-type');
            const price = label.getAttribute('data-price');
            reportTypeInput.value = reportType;
            if (reportPriceSpan) reportPriceSpan.textContent = currencySymbol + price;
            if (buttonPriceSpan) buttonPriceSpan.textContent = currencySymbol + price;
            checkPaymentMethodAvailability(parseFloat(price));
            updatePurchaseApprox(parseFloat(price));
            initializeBundlePrices();
        }

        function updateReportTypeAvailability(type, isAvailable) {
            const label = document.querySelector(`.report-type-btn[data-type="${type}"]`);
            if (!label) {
                return;
            }

            const radio = label.querySelector('input[name="report_type_radio"]');
            if (radio) {
                radio.disabled = !isAvailable;
            }

            label.setAttribute('aria-disabled', String(!isAvailable));
            label.classList.toggle('opacity-60', !isAvailable);
            label.classList.toggle('cursor-not-allowed', !isAvailable);
            label.classList.toggle('cursor-pointer', isAvailable);
            label.title = isAvailable ? '' : 'No records available for this report';

            if (!isAvailable && radio?.checked) {
                const fallback = Array.from(reportTypeButtons).find(btn => {
                    const fallbackRadio = btn.querySelector('input[name="report_type_radio"]');
                    return fallbackRadio && !fallbackRadio.disabled;
                });

                if (fallback) {
                    selectReportType(fallback);
                }
            }
        }

        reportTypeRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                const label = this.closest('.report-type-btn');
                selectReportType(label);
            });
        });
        const initialReportPrice = getSelectedReportPrice();
        if (initialReportPrice !== null) {
            updatePurchaseApprox(initialReportPrice);
        }

        // Check payment methods availability when price changes
        function checkPaymentMethodAvailability(price) {
            @auth
            const userReportBalance = {{ $userReportBalance ?? 0 }};

            // Check report balance (1 report = 1 unit)
            const reportBalanceCard = document.getElementById('payment_report_balance_card');
            const reportBalanceInsufficient = document.getElementById('reportBalanceInsufficient');
            const reportBalanceRadio = document.getElementById('payment_report_balance');

            if (userReportBalance >= 1) {
                if (reportBalanceCard) reportBalanceCard.classList.remove('opacity-60');
                if (reportBalanceInsufficient) reportBalanceInsufficient.classList.add('hidden');
                if (reportBalanceRadio) reportBalanceRadio.disabled = false;
            } else {
                if (reportBalanceCard) reportBalanceCard.classList.add('opacity-60');
                if (reportBalanceInsufficient) reportBalanceInsufficient.classList.remove('hidden');
                if (reportBalanceRadio) {
                    reportBalanceRadio.disabled = true;
                    if (reportBalanceRadio.checked) {
                        // Switch to platon if report_balance is unavailable
                        const platonRadio = document.getElementById('payment_platon');
                        if (platonRadio) {
                            platonRadio.checked = true;
                        }
                    }
                }
            }
            @endauth
        }

        // Update visual display of selected payment method
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.payment-check').forEach(check => {
                    check.classList.add('hidden');
                });
                document.querySelectorAll('.payment-method-card').forEach(card => {
                    card.classList.remove('border-primary-500', 'bg-primary-50');
                });
                if (this.checked) {
                    const checkIcon = this.closest('.payment-method-card').querySelector('.payment-check');
                    const card = this.closest('.payment-method-card');
                    if (checkIcon) checkIcon.classList.remove('hidden');
                    if (card) {
                        card.classList.add('border-primary-500', 'bg-primary-50');
                    }
                }
            });
        });

        // Initialization on load
        const initialPrice = parseFloat('{{ $carfaxPrice }}');
        if (typeof checkPaymentMethodAvailability === 'function') {
            checkPaymentMethodAvailability(initialPrice);
        }

        // Auto-select report_balance on load if balance is sufficient
        @auth
        const userReportBalance = {{ $userReportBalance ?? 0 }};
        const reportBalanceRadio = document.getElementById('payment_report_balance');
        if (userReportBalance >= 1 && reportBalanceRadio && !reportBalanceRadio.disabled) {
            reportBalanceRadio.checked = true;
        }
        @endauth

        // Update visual display on load
        const checkedPayment = document.querySelector('input[name="payment_method"]:checked');
        if (checkedPayment) {
            checkedPayment.dispatchEvent(new Event('change'));
        }

        // Purchase report
        purchaseForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const vin = hiddenVinInput.value;
            const email = emailInput ? emailInput.value.trim() : document.getElementById('email').value.trim();
            const reportType = reportTypeInput ? reportTypeInput.value : document.getElementById('report_type').value;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'platon';
            const selectedReportRadio = document.querySelector('input[name="report_type_radio"]:checked');

            if (!email) {
                showPurchaseError('{{ __('index.checkout.error') }}');
                return;
            }

            if (!selectedReportRadio || selectedReportRadio.disabled) {
                showPurchaseError('Selected report is not available for this VIN');
                return;
            }

            purchaseButton.disabled = true;
            const buttonText = document.getElementById('buttonText');
            if (buttonText) buttonText.textContent = '{{ __('index.checkout.processing') }}';
            purchaseMessageDiv.classList.add('hidden');

            try {
                // Get current language from URL or use app()->getLocale()
                @php
                    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
                    $jsLocale = LaravelLocalization::getCurrentLocale() ?? app()->getLocale();
                    $jsLocale = explode('-', $jsLocale)[0];
                @endphp
                const currentLocale = '{{ $jsLocale }}';

                const response = await fetch(`/vin-report/purchase-report/${paymentMethod}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Accept-Language': currentLocale
                    },
                    body: JSON.stringify({
                        vin: vin,
                        email: email,
                        report_type: reportType,
                        payment_method: paymentMethod,
                        locale: currentLocale  // Explicitly pass locale in the request body
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (data.payment_form) {
                        submitPaymentForm(data.payment_form);
                    } else if (data.url) {
                        // For platon/stripe - redirect to payment system
                        // For report_balance - redirect to thank-you
                        window.location.href = data.url;
                    } else {
                        showPurchaseError('Error: redirect URL not received');
                        purchaseButton.disabled = false;
                        if (buttonText) buttonText.textContent = '{{ __('index.checkout.button_text') }}';
                    }
                } else {
                    showPurchaseError(data.error || '{{ __('index.checkout.purchase_error') }}');
                    purchaseButton.disabled = false;
                    if (buttonText) buttonText.textContent = '{{ __('index.checkout.button_text') }}';
                }
            } catch (error) {
                console.error('Purchase error:', error);
                showPurchaseError('{{ __('index.checkout.request_error') }}');
                purchaseButton.disabled = false;
                if (buttonText) buttonText.textContent = '{{ __('index.checkout.button_text') }}';
            }
        });

        function showError(message) {
            dataReport.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg" role="alert">${message}</div>`;
        }

        // Make showError available globally for hCaptcha callbacks
        window.showError = showError;

        function showPurchaseError(message) {
            purchaseMessageDiv.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg" role="alert">${message}</div>`;
            purchaseMessageDiv.classList.remove('hidden');
        }

        function getBalancePackagePrice(amount) {
            if (balanceHasPriceError || !balancePackagePrices) {
                throw new Error('Failed to load prices from the server.');
            }

            if (!amount || amount < 1) {
                return 0;
            }

            const key = String(amount);
            const pricesByCurrency = balancePackagePrices[key] ?? balancePackagePrices[amount];
            if (!pricesByCurrency || typeof pricesByCurrency !== 'object') {
                throw new Error('Package price not found for the selected amount.');
            }

            const packagePrice = pricesByCurrency[balanceCurrentCurrency];
            if (packagePrice !== undefined && typeof packagePrice === 'number') {
                return packagePrice;
            }

            throw new Error(`Price for currency ${balanceCurrentCurrency} was not found.`);
        }

        function getBalancePackagePricePerReport(amount) {
            if (!amount || amount < 1) {
                return 0;
            }

            return getBalancePackagePrice(amount) / amount;
        }

        // Function to purchase report balance
        async function purchaseReportBalance(amount) {
            if (!isAuthenticated) {
                window.location.href = '{{ route("login") }}';
                return;
            }

            try {
                const response = await fetch('{{ route("topup.report-balance") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: amount
                    })
                });

                const data = await response.json();

                if (response.ok && data.success && data.payment_form) {
                    submitPaymentForm(data.payment_form);
                } else if (response.ok && data.success && data.url) {
                    window.location.href = data.url;
                } else {
                    alert(data.error || 'Error creating a report balance top-up order');
                }
            } catch (error) {
                console.error('Purchase error:', error);
                alert('Error sending the request');
            }
        }

        // Initialize prices for bundles
        function initializeBundlePrices() {
            if (balanceHasPriceError || !balancePackagePrices) {
                console.error('Price error or packages not available:', {balanceHasPriceError, balancePackagePrices});
                return;
            }

            // Get single report price as base for bundle savings comparison
            let basePricePerReport = 0;
            try {
                const normalizedCarfaxPrice = typeof carfaxSingleReportPrice === 'string'
                    ? Number(carfaxSingleReportPrice.replace(',', '.'))
                    : Number(carfaxSingleReportPrice);
                if (Number.isFinite(normalizedCarfaxPrice) && normalizedCarfaxPrice > 0) {
                    basePricePerReport = normalizedCarfaxPrice;
                } else {
                    const packageAmounts = Object.keys(balancePackagePrices)
                        .map(amount => Number(amount))
                        .filter(Number.isFinite)
                        .sort((a, b) => a - b);
                    const smallestPackage = packageAmounts[0];
                    if (smallestPackage) {
                        basePricePerReport = getBalancePackagePricePerReport(smallestPackage);
                    }
                }
            } catch (e) {
                console.error('Error calculating base package price for comparison:', e);
            }

            const hasUahRate = typeof conversionRateToUah === 'number' && conversionRateToUah > 0 && conversionRateToUah !== 1;

            // Starter Pack (5 reports)
            try {
                const starterPrice = getBalancePackagePrice(5);
                const starterPricePerReport = getBalancePackagePricePerReport(5);
                const starterPriceEl = document.getElementById('starterBundlePrice');
                const starterPricePerReportEl = document.getElementById('starterBundlePricePerReport');
                const starterSavePercentEl = document.getElementById('starterBundleSavePercent');
                if (starterPriceEl) starterPriceEl.textContent = balanceCurrencySymbol + starterPrice.toFixed(2);
                if (starterPricePerReportEl) starterPricePerReportEl.textContent = balanceCurrencySymbol + starterPricePerReport.toFixed(2);

                const starterApproxEl = document.getElementById('starterBundleApprox');
                const starterRateEl = document.getElementById('starterBundleRate');
                const starterNoteEl = document.getElementById('starterBundlePaymentNote');
                if (starterApproxEl && starterNoteEl && hasUahRate) {
                    starterApproxEl.textContent = '₴' + formatApproxAmount(starterPrice * conversionRateToUah);
                    if (starterRateEl) starterRateEl.textContent = conversionRateToUah.toFixed(2);
                    starterNoteEl.style.display = '';
                } else if (starterNoteEl) {
                    starterNoteEl.style.display = 'none';
                }

                // Calculate savings percentage
                if (basePricePerReport > 0) {
                    const baseReportsTotalPrice = basePricePerReport * 5;
                    const starterSavePercent = Math.round(((baseReportsTotalPrice - starterPrice) / baseReportsTotalPrice) * 100);
                    if (starterSavePercentEl && starterSavePercent > 0) {
                        starterSavePercentEl.textContent = '−' + starterSavePercent + '%';
                    } else if (starterSavePercentEl) {
                        starterSavePercentEl.textContent = '';
                    }
                }
            } catch (e) {
                console.error('Error calculating starter bundle price:', e);
            }

            // Most Popular (25 reports)
            try {
                const popularPrice = getBalancePackagePrice(25);
                const popularPricePerReport = getBalancePackagePricePerReport(25);
                const popularPriceEl = document.getElementById('popularBundlePrice');
                const popularPricePerReportEl = document.getElementById('popularBundlePricePerReport');
                const popularSavePercentEl = document.getElementById('popularBundleSavePercent');
                if (popularPriceEl) popularPriceEl.textContent = balanceCurrencySymbol + popularPrice.toFixed(2);
                if (popularPricePerReportEl) popularPricePerReportEl.textContent = balanceCurrencySymbol + popularPricePerReport.toFixed(2);

                const popularApproxEl = document.getElementById('popularBundleApprox');
                const popularRateEl = document.getElementById('popularBundleRate');
                const popularNoteEl = document.getElementById('popularBundlePaymentNote');
                if (popularApproxEl && popularNoteEl && hasUahRate) {
                    popularApproxEl.textContent = '₴' + formatApproxAmount(popularPrice * conversionRateToUah);
                    if (popularRateEl) popularRateEl.textContent = conversionRateToUah.toFixed(2);
                    popularNoteEl.style.display = '';
                } else if (popularNoteEl) {
                    popularNoteEl.style.display = 'none';
                }

                // Calculate savings percentage
                if (basePricePerReport > 0) {
                    const baseReportsTotalPrice = basePricePerReport * 25;
                    const popularSavePercent = Math.round(((baseReportsTotalPrice - popularPrice) / baseReportsTotalPrice) * 100);
                    if (popularSavePercentEl && popularSavePercent > 0) {
                        popularSavePercentEl.textContent = '−' + popularSavePercent + '%';
                    } else if (popularSavePercentEl) {
                        popularSavePercentEl.textContent = '';
                    }
                }
            } catch (e) {
                console.error('Error calculating popular bundle price:', e);
            }

            // Best Value (100 reports)
            try {
                const valuePrice = getBalancePackagePrice(100);
                const valuePricePerReport = getBalancePackagePricePerReport(100);
                const valuePriceEl = document.getElementById('valueBundlePrice');
                const valuePricePerReportEl = document.getElementById('valueBundlePricePerReport');
                const valueSavePercentEl = document.getElementById('valueBundleSavePercent');
                if (valuePriceEl) valuePriceEl.textContent = balanceCurrencySymbol + valuePrice.toFixed(2);
                if (valuePricePerReportEl) valuePricePerReportEl.textContent = balanceCurrencySymbol + valuePricePerReport.toFixed(2);

                const valueApproxEl = document.getElementById('valueBundleApprox');
                const valueRateEl = document.getElementById('valueBundleRate');
                const valueNoteEl = document.getElementById('valueBundlePaymentNote');
                if (valueApproxEl && valueNoteEl && hasUahRate) {
                    valueApproxEl.textContent = '₴' + formatApproxAmount(valuePrice * conversionRateToUah);
                    if (valueRateEl) valueRateEl.textContent = conversionRateToUah.toFixed(2);
                    valueNoteEl.style.display = '';
                } else if (valueNoteEl) {
                    valueNoteEl.style.display = 'none';
                }

                // Calculate savings percentage
                if (basePricePerReport > 0) {
                    const baseReportsTotalPrice = basePricePerReport * 100;
                    const valueSavePercent = Math.round(((baseReportsTotalPrice - valuePrice) / baseReportsTotalPrice) * 100);
                    if (valueSavePercentEl && valueSavePercent > 0) {
                        valueSavePercentEl.textContent = '−' + valueSavePercent + '%';
                    } else if (valueSavePercentEl) {
                        valueSavePercentEl.textContent = '';
                    }
                }
            } catch (e) {
                console.error('Error calculating value bundle price:', e);
            }
        }

        // Starter Pack Button (5 reports)
        const starterBundleButton = document.getElementById('starterBundleButton');
        if (starterBundleButton) {
            starterBundleButton.addEventListener('click', async function () {
                await purchaseReportBalance(5);
            });
        }

        // Most Popular Button (25 reports)
        const popularBundleButton = document.getElementById('popularBundleButton');
        if (popularBundleButton) {
            popularBundleButton.addEventListener('click', async function () {
                await purchaseReportBalance(25);
            });
        }

        // Best Value Button (100 reports)
        const valueBundleButton = document.getElementById('valueBundleButton');
        if (valueBundleButton) {
            valueBundleButton.addEventListener('click', async function () {
                await purchaseReportBalance(100);
            });
        }

        // Initialize on load
        initializeBundlePrices();
    });
</script>
    @include('partials.home-trust')
    @include('partials.home-tools')
    @include('partials.home-recent-reports')
    @include('partials.home-disclaimer')

@include('footer')
