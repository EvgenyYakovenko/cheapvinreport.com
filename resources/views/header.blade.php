<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/favicon/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('images/favicon/favicon-48x48.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">

    {{-- STAGING: also emit noindex meta on the 'staging' env (in addition to the
         X-Robots-Tag header added globally by ForceNoIndex middleware). Prod is
         excluded, so this stays safe when merged into the main repo. --}}
    @if(app()->environment(['local', 'development', 'staging']))
        <meta name="robots" content="noindex, nofollow">
    @endif

    @php
        use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

        // Получаем поддерживаемые языки из конфига
        $supportedLocales = config('laravellocalization.supportedLocales', []);

        // Маппинг флагов и названий языков
        $languageFlags = [
            'en' => '🇬🇧',
            'ru' => '🇷🇺',
            'uk' => '🇺🇦',
            'pl' => '🇵🇱',
            'kk' => '🇰🇿',
        ];

        $languageNames = [
            'en' => 'English',
            'ru' => 'Русский',
            'uk' => 'Українська',
            'pl' => 'Polski',
            'kk' => 'Қазақша',
        ];

        $languageCodes = [
            'en' => 'EN',
            'ru' => 'RU',
            'uk' => 'UK',
            'pl' => 'PL',
            'kk' => 'KZ',
        ];

        // Фильтруем только активные языки
        $activeLocales = array_filter($supportedLocales, function($locale) {
            return !empty($locale);
        });

        $currentLocale = app()->getLocale();
        $defaultLocale = LaravelLocalization::getDefaultLocale();
        $homeUrl = \App\Support\LocaleRoute::route('index');
        $loginUrl = \App\Support\LocaleRoute::route('login');
        $languageLinks = !empty($hreflangUrls ?? null) ? $hreflangUrls : null;
    @endphp

    @php
        $isHomePage = request()->routeIs('index', 'index.locale');
        $isPaginatedBlog = request()->routeIs('blog', 'blog.locale') && ((int) request()->query('page', 1) > 1);
        $isPostPage = request()->routeIs('post', 'post.locale');
        $needsHCaptchaSdk = $isHomePage;
        $canonicalUrl = url()->current();

        if ($isHomePage && empty($hreflangUrls ?? [])) {
            $hreflangUrls = [];
            foreach (array_keys($activeLocales ?? []) as $localeCode) {
                if ($localeCode === $defaultLocale) {
                    $hreflangUrls[$localeCode] = url('/');
                    continue;
                }

                $hreflangUrls[$localeCode] = LaravelLocalization::getLocalizedURL($localeCode, '/');
            }

            $xDefaultUrl = url('/');
        }

        $fallbackTitle = config('app.name', 'CheapVINReport');
        $fallbackDescription = 'CheapVINReport is a platform for buying and selling VIN reports. We are a team of experts who are dedicated to providing the best possible service to our customers.';

        if ($isHomePage) {
            $seoTitle = __('index.title');
            $seoDescription = __('index.description');
        } elseif (isset($metaTitle)) {
            $seoTitle = $metaTitle . ' - ' . config('app.name', 'CheapVINReport');
            $seoDescription = $metaDescription ?? '';
        } else {
            $seoTitle = $fallbackTitle;
            $seoDescription = $fallbackDescription;
        }

        $seoImage = $seoImage ?? asset('images/hero-new.jpg');
        $ogType = $isPostPage ? 'article' : 'website';
    @endphp

    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if($isPaginatedBlog)
        <meta name="robots" content="noindex,follow">
    @endif

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    @if(isset($metaKeywords) && $metaKeywords)
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif

    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ config('app.name', 'CheapVINReport') }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    {{-- hreflang для доступных языковых версий текущего контента --}}
    @if(!empty($hreflangUrls ?? []))
        @foreach($hreflangUrls as $localeCode => $url)
            <link rel="alternate" hreflang="{{ $localeCode }}" href="{{ $url }}">
        @endforeach

        @if(!empty($xDefaultUrl ?? null))
            <link rel="alternate" hreflang="x-default" href="{{ $xDefaultUrl }}">
        @endif
    @endif

    {{-- Микроразметка BlogPosting для постов --}}
    @if(!empty($blogPostingSchema ?? null))
        <script type="application/ld+json">{!! json_encode($blogPostingSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endif

    @php
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => 'https://cheapvinreport.com/#organization',
            'name' => 'Cheap VIN Report',
            'url' => 'https://cheapvinreport.com',
            'legalName' => 'FOP Yakovenko Igor Eduardovich',
            'taxID' => '2167301354',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Heroiv Kharkiv Ave., 131 V',
                'addressLocality' => 'Kharkiv',
                'postalCode' => '61001',
                'addressCountry' => 'UA',
            ],
            'sameAs' => [
                'https://www.etsy.com/shop/Cheapvinreport',
                'https://youcontrol.com.ua/catalog/fop_details/74637544/',
                'https://www.trustpilot.com/review/cheapvinreport.com',
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- hCaptcha SDK (load only where captcha is used) -->
    @if(config('hcaptcha.site_key') && $needsHCaptchaSdk)
        <script>
            window.__hcaptchaLoaded = false;
            window.__hcaptchaInitQueue = window.__hcaptchaInitQueue || [];
            window.registerHCaptchaInit = function(initFn) {
                if (typeof initFn !== 'function') {
                    return;
                }

                window.__hcaptchaInitQueue.push(initFn);
                if (window.__hcaptchaLoaded) {
                    initFn();
                }
            };

            window.runHCaptchaInits = function() {
                if (!window.__hcaptchaInitQueue || !window.__hcaptchaInitQueue.length) {
                    return;
                }

                window.__hcaptchaInitQueue.forEach(function(initFn) {
                    if (typeof initFn === 'function') {
                        initFn();
                    }
                });
            };

            window.onloadHCaptcha = function() {
                window.__hcaptchaLoaded = true;
                if (typeof window.runHCaptchaInits === 'function') {
                    window.runHCaptchaInits();
                }
            };
        </script>
        <script
            src="https://js.hcaptcha.com/1/api.js?onload=onloadHCaptcha&render=explicit"
            async defer>
        </script>
    @endif

    <style>
        #checkout-form:not(.show) {
            display: none;
        }

        .dropdown-menu {
            display: none;
        }

        .dropdown-menu.show {
            display: block;
        }
    </style>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-TSC9JLWP');</script>
    <!-- End Google Tag Manager -->
    <!-- Taboola Pixel Code -->
    <script type='text/javascript'>
        window._tfa = window._tfa || [];
        window._tfa.push({notify: 'event', name: 'page_view', id: 1650105});
        !function (t, f, a, x) {
            if (!document.getElementById(x)) {
                t.async = 1;
                t.src = a;
                t.id = x;
                f.parentNode.insertBefore(t, f);
            }
        }(document.createElement('script'),
            document.getElementsByTagName('script')[0],
            '//cdn.taboola.com/libtrc/unip/1650105/tfa.js',
            'tb_tfa_script');
    </script>
    <!-- End of Taboola Pixel Code -->
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TSC9JLWP"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- Navigation -->
<header class="bg-white border-b border-gray-200 shadow-sm">
    <nav class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ $homeUrl }}" class="flex items-center space-x-2">
                    <img
                        src="{{ asset('images/logonew.png') }}"
                        alt="CheapVINReport"
                        class="h-8 w-auto"
                    >
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex lg:items-center lg:space-x-8">
                <!-- Menu Links -->
                <div class="flex items-center space-x-6">
                    <a href="#bundles" class="text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium transition">
                        {{ __('index.nav.bundles') }}
                    </a>
                    <a href="#faq" class="text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium transition">
                        {{ __('index.nav.faq') }}
                    </a>
                    <a href="{{ asset('images/Example.pdf') }}" target="_blank" rel="noopener" class="text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium transition">
                        {{ __('index.nav.example') }}
                    </a>
                </div>

                <!-- Language Switcher -->
                <div class="relative">
                    <button
                        onclick="toggleDropdown('languageDropdown')"
                        class="flex items-center space-x-1 text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium transition"
                    >
                        <x-icons.language class="w-4 h-4 text-gray-500" aria-hidden="true"/>
                            <span>
                                {{ $languageCodes[$currentLocale] ?? strtoupper($currentLocale) }}
                            </span>
                        <x-icons.chevron-down class="w-4 h-4 text-gray-500" aria-hidden="true"/>
                    </button>
                    <div
                        id="languageDropdown"
                        class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200"
                    >
                        @if($languageLinks)
                            @foreach($languageLinks as $localeCode => $localizedUrl)
                                <a
                                    href="{{ $localizedUrl }}"
                                    class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ $currentLocale == $localeCode ? 'bg-primary-50 text-primary-700 font-semibold' : '' }}"
                                >
                                    <span>{{ $languageNames[$localeCode] ?? strtoupper($localeCode) }}</span>
                                </a>
                            @endforeach
                        @else
                            @foreach($activeLocales as $localeCode => $localeData)
                                @php
                                    $supportedLocalesList = LaravelLocalization::getSupportedLocales();
                                    $isSupported = $supportedLocalesList && array_key_exists($localeCode, $supportedLocalesList);
                                @endphp
                                @if($isSupported)
                                    @php
                                        try {
                                            $localizedUrl = LaravelLocalization::getLocalizedURL($localeCode, null, [], false);
                                        } catch (\Exception $e) {
                                            $localizedUrl = null;
                                        }
                                    @endphp
                                    @if($localizedUrl)
                                        <a
                                            href="{{ $localizedUrl }}"
                                            class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ $currentLocale == $localeCode ? 'bg-primary-50 text-primary-700 font-semibold' : '' }}"
                                        >
                                            <span>{{ $languageNames[$localeCode] ?? $localeData['native'] ?? strtoupper($localeCode) }}</span>
                                        </a>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- User Menu -->
                @auth
                    @if(auth()->user()->role === 'admin')
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('panel') }}"
                               class="flex items-center space-x-2 text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium transition">
                                <span>Panel</span>
                            </a>
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center space-x-2 text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium transition">
                                <span>Dashboard</span>
                            </a>
                        </div>
                    @else
                        <a href="{{ route('panel') }}"
                           class="flex items-center space-x-2 text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium transition">
                            <span>{{ __('index.nav.account') }}</span>
                        </a>
                    @endif
                @else
                    <a href="{{ $loginUrl }}"
                       class="flex items-center space-x-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        <span>{{ __('index.nav.login') }}</span>
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center lg:hidden">
                <button
                    type="button"
                    onclick="toggleMobileMenu()"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-primary-600 hover:bg-gray-100 focus:outline-none transition"
                >
                    <span class="sr-only">Open menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>
</header>

<!-- Mobile Offcanvas Menu -->
<div id="mobileMenuBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity"
     onclick="toggleMobileMenu()"></div>
<div id="mobileMenu"
     class="fixed top-0 right-0 h-full w-64 bg-white transform translate-x-full transition-transform duration-300 ease-in-out z-50 shadow-2xl">
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <span class="font-bold text-lg text-gray-900">Menu</span>
            <button onclick="toggleMobileMenu()" class="text-gray-600 hover:text-gray-900" aria-label="Close menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Menu Items -->
        <div class="flex-1 overflow-y-auto p-4">
            <nav class="flex flex-col space-y-4">
                <a class="text-gray-600 hover:text-gray-900 transition py-2" href="#bundles"
                   onclick="toggleMobileMenu()">{{ __('index.nav.bundles') }}</a>
                <a class="text-gray-600 hover:text-gray-900 transition py-2" href="#faq"
                   onclick="toggleMobileMenu()">{{ __('index.nav.faq') }}</a>
                <a class="text-gray-600 hover:text-gray-900 transition py-2"
                   href="{{ asset('images/Example.pdf') }}" target="_blank" rel="noopener">
                    {{ __('index.nav.example') }}
                </a>

                <hr class="border-gray-200">

                <!-- Language Switcher Mobile -->
                <div>
                    <button
                        class="text-gray-600 hover:text-gray-900 transition flex items-center justify-between py-2 w-full"
                        onclick="document.getElementById('languageDropdownMobile').classList.toggle('hidden')">
                        <span class="flex items-center space-x-2">
                            <x-icons.language class="w-4 h-4 text-gray-500" aria-hidden="true"/>
                            <span>
                                {{ $languageNames[$currentLocale] ?? $activeLocales[$currentLocale]['native'] ?? strtoupper($currentLocale) }}
                            </span>
                        </span>
                        <x-icons.chevron-down class="w-4 h-4 text-gray-500 ml-2" aria-hidden="true"/>
                    </button>
                    <div id="languageDropdownMobile" class="hidden pl-4 mt-2 space-y-2">
                        @if($languageLinks)
                            @foreach($languageLinks as $localeCode => $localizedUrl)
                                <a class="block py-2 text-gray-500 hover:text-gray-900 transition {{ $currentLocale == $localeCode ? 'text-primary-600 font-semibold' : '' }}"
                                   href="{{ $localizedUrl }}">
                                    {{ $languageNames[$localeCode] ?? strtoupper($localeCode) }}
                                </a>
                            @endforeach
                        @else
                            @foreach($activeLocales as $localeCode => $localeData)
                                @php
                                    $supportedLocalesList = LaravelLocalization::getSupportedLocales();
                                    $isSupported = $supportedLocalesList && array_key_exists($localeCode, $supportedLocalesList);
                                @endphp
                                @if($isSupported)
                                    @php
                                        try {
                                            $localizedUrl = LaravelLocalization::getLocalizedURL($localeCode, null, [], false);
                                        } catch (\Exception $e) {
                                            $localizedUrl = null;
                                        }
                                    @endphp
                                    @if($localizedUrl)
                                        <a class="block py-2 text-gray-500 hover:text-gray-900 transition {{ $currentLocale == $localeCode ? 'text-primary-600 font-semibold' : '' }}"
                                           href="{{ $localizedUrl }}">
                                            {{ $languageNames[$localeCode] ?? $localeData['native'] ?? strtoupper($localeCode) }}
                                        </a>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>

                <hr class="border-gray-200">

                @auth
                    @if(auth()->user()->role === 'admin')
                        <a class="text-gray-600 hover:text-gray-900 transition py-2 flex items-center"
                           href="{{ route('panel') }}" onclick="toggleMobileMenu()">
                            Panel
                        </a>
                        <a class="text-gray-600 hover:text-gray-900 transition py-2 flex items-center"
                           href="{{ route('dashboard') }}" onclick="toggleMobileMenu()">
                            Dashboard
                        </a>
                    @else
                        <a class="text-gray-600 hover:text-gray-900 transition py-2 flex items-center"
                           href="{{ route('panel') }}" onclick="toggleMobileMenu()">
                            {{ __('index.nav.account') }}
                        </a>
                    @endif
                @else
                    <a class="bg-primary-600 hover:bg-primary-700 text-white transition py-2 px-4 rounded-lg flex items-center justify-center"
                       href="{{ $loginUrl }}" onclick="toggleMobileMenu()">
                        {{ __('index.nav.login') }}
                    </a>
                @endauth
            </nav>
        </div>
    </div>
</div>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const backdrop = document.getElementById('mobileMenuBackdrop');

        if (menu.classList.contains('translate-x-full')) {
            // Open
            menu.classList.remove('translate-x-full');
            backdrop.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            // Close
            menu.classList.add('translate-x-full');
            backdrop.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (event) {
        const languageDropdown = document.getElementById('languageDropdown');
        const languageButton = event.target.closest('button[onclick*="languageDropdown"]');

        if (!languageButton && languageDropdown && !languageDropdown.contains(event.target)) {
            languageDropdown.classList.remove('show');
        }
    });
</script>
