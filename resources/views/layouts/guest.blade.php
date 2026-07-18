<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ config('app.name', 'CheapVINReport') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-primary-50 via-white to-primary-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 py-12">
            <!-- Language Switcher -->
            <div class="absolute top-4 right-4">
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
                    $homeUrl = \App\Support\LocaleRoute::route('index');
                @endphp
                <div class="relative">
                    <button
                        onclick="toggleDropdown('languageDropdownAuth')"
                        class="flex items-center space-x-1 text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium transition bg-white rounded-lg border border-gray-300 shadow-sm"
                    >
                        <span>
                            {{ $languageCodes[$currentLocale] ?? strtoupper($currentLocale) }}
                        </span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div
                        id="languageDropdownAuth"
                        class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200 hidden"
                    >
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
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <a href="{{ $homeUrl }}" class="flex items-center">
                    <span class="text-3xl font-bold text-gray-900 hover:text-gray-800 transition">{{ config('app.name', 'CheapVINReport') }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                {{ $slot }}
            </div>

        </div>

        <script>
            function toggleDropdown(id) {
                const dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.classList.toggle('hidden');
                }
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdowns = document.querySelectorAll('.dropdown-menu');
                dropdowns.forEach(dropdown => {
                    if (!dropdown.contains(event.target) && !event.target.closest('button[onclick*="toggleDropdown"]')) {
                        dropdown.classList.add('hidden');
                    }
                });
            });

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
        @if(config('hcaptcha.site_key'))
        <script
            src="https://js.hcaptcha.com/1/api.js?onload=onloadHCaptcha&render=explicit"
            async defer>
        </script>
        @endif
    </body>
</html>
