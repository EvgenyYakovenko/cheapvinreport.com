{{-- STAGING: Data Sources page (E-E-A-T / authority). Meta from StaticPageController. i18n lang/pages.ds. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); @endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-gray-900">{{ __('nav.home') }}</a>
                    <span class="mx-2">/</span><span class="text-gray-700">{{ __('pages.ds.crumb') }}</span>
                </nav>

                <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-gray-900 mb-4">{{ __('pages.ds.h1') }}</h1>
                <p class="text-lg text-gray-600 mb-8">{{ __('pages.ds.lead') }}</p>

                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('pages.ds.free_h2') }}</h2>
                <p class="text-gray-600 mb-5">{{ __('pages.ds.free_lead') }}</p>
                <div class="space-y-4 mb-10">
                    <div class="bg-white rounded-lg border border-gray-900/10 p-6">
                        <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                            <h3 class="text-lg font-bold text-gray-900">{{ __('pages.ds.vpic_t') }}</h3>
                            <span class="text-xs font-semibold bg-acc-50 text-acc-600 px-2 py-1 rounded">{{ __('pages.ds.badge') }}</span>
                        </div>
                        <p class="text-gray-600 mb-2">{{ __('pages.ds.vpic_d') }}</p>
                        <a href="https://vpic.nhtsa.dot.gov/" target="_blank" rel="noopener nofollow" class="text-sm font-semibold text-acc-600 hover:text-acc-700">vpic.nhtsa.dot.gov &rarr;</a>
                    </div>
                    <div class="bg-white rounded-lg border border-gray-900/10 p-6">
                        <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                            <h3 class="text-lg font-bold text-gray-900">{{ __('pages.ds.recalls_t') }}</h3>
                            <span class="text-xs font-semibold bg-acc-50 text-acc-600 px-2 py-1 rounded">{{ __('pages.ds.badge') }}</span>
                        </div>
                        <p class="text-gray-600 mb-2">{{ __('pages.ds.recalls_d') }}</p>
                        <a href="https://www.nhtsa.gov/recalls" target="_blank" rel="noopener nofollow" class="text-sm font-semibold text-acc-600 hover:text-acc-700">nhtsa.gov/recalls &rarr;</a>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('pages.ds.reports_h2') }}</h2>
                <p class="text-gray-600 mb-5">{{ __('pages.ds.reports_lead') }}</p>
                <div class="bg-white rounded-lg border border-gray-900/10 divide-y divide-gray-900/10 mb-6">
                    <div class="p-6"><h3 class="text-lg font-bold text-gray-900 mb-1">{{ __('pages.ds.nmvtis_t') }}</h3><p class="text-gray-600">{{ __('pages.ds.nmvtis_d') }}</p><a href="https://vehiclehistory.bja.ojp.gov/" target="_blank" rel="noopener nofollow" class="text-sm font-semibold text-acc-600 hover:text-acc-700 mt-1 inline-block">vehiclehistory.bja.ojp.gov &rarr;</a></div>
                    <div class="p-6"><h3 class="text-lg font-bold text-gray-900 mb-1">{{ __('pages.ds.state_t') }}</h3><p class="text-gray-600">{{ __('pages.ds.state_d') }}</p></div>
                    <div class="p-6"><h3 class="text-lg font-bold text-gray-900 mb-1">{{ __('pages.ds.ins_t') }}</h3><p class="text-gray-600">{{ __('pages.ds.ins_d') }}</p></div>
                    <div class="p-6"><h3 class="text-lg font-bold text-gray-900 mb-1">{{ __('pages.ds.nicb_t') }}</h3><p class="text-gray-600">{{ __('pages.ds.nicb_d') }}</p></div>
                    <div class="p-6"><h3 class="text-lg font-bold text-gray-900 mb-1">{{ __('pages.ds.mfr_t') }}</h3><p class="text-gray-600">{{ __('pages.ds.mfr_d') }}</p></div>
                    <div class="p-6"><h3 class="text-lg font-bold text-gray-900 mb-1">{{ __('pages.ds.insp_t') }}</h3><p class="text-gray-600">{{ __('pages.ds.insp_d') }}</p></div>
                </div>

                <div class="bg-white rounded-lg border border-gray-900/10 p-6 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ __('pages.ds.compiled_h2') }}</h2>
                    <p class="text-gray-600">{{ __('pages.ds.compiled_d') }}</p>
                </div>

                <div class="bg-acc-50 border border-acc-100 rounded-lg p-6 mb-10">
                    <h2 class="text-lg font-bold text-gray-900 mb-2">{{ __('pages.ds.accuracy_h2') }}</h2>
                    <p class="text-gray-600">{{ __('pages.ds.accuracy_d') }}</p>
                </div>

                <div class="text-center">
                    <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3.5 bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">{{ __('pages.ds.cta') }} &rarr;</a>
                </div>
            </div>
        </div>
    </section>
</main>

@include('footer')
