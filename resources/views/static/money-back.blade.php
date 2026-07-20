{{-- STAGING: Money-Back Guarantee page. Meta from StaticPageController. i18n lang/pages.mb. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); @endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">{{ __('nav.home') }}</a>
                    <span class="mx-2">/</span><span class="text-gray-700">{{ __('pages.mb.crumb') }}</span>
                </nav>

                <div class="flex items-center gap-3 mb-4">
                    <span class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">{{ __('pages.mb.h1') }}</h1>
                </div>
                <p class="text-lg text-gray-700 mb-8">{{ __('pages.mb.lead') }}</p>

                <div class="space-y-4">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('pages.mb.c1_t') }}</h2>
                        <p class="text-gray-600">{{ __('pages.mb.c1_d') }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('pages.mb.c2_t') }}</h2>
                        <p class="text-gray-600">{{ __('pages.mb.c2_d') }}</p>
                    </div>
                </div>

                <div class="mt-8 bg-acc-50 border border-acc-100 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('pages.mb.how_t') }}</h2>
                    <p class="text-gray-600 mb-3">{{ __('pages.mb.how_d') }}</p>
                    <a href="mailto:support@cheapvinreport.email" class="inline-block font-semibold text-acc-600 hover:text-acc-700">support@cheapvinreport.email</a>
                </div>

                <div class="mt-8 text-center">
                    <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3.5 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">{{ __('pages.mb.cta') }} &rarr;</a>
                </div>
            </div>
        </div>
    </section>
</main>

@include('footer')
