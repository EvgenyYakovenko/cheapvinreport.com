{{-- STAGING: Free tool — VIN Model Year Decoder. i18n lang/toolpages.model_year_decoder. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $toolsUrl=\App\Support\LocaleRoute::route('tools.index'); @endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">{{ __('nav.home') }}</a><span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">{{ __('tools.hub.crumb') }}</a><span class="mx-2">/</span>
                    <span class="text-gray-700">{{ __('tools.items.model-year-decoder.label') }}</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">{{ __('tools.items.model-year-decoder.title') }}</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">{{ __('toolpages.model_year_decoder.lead') }}</p>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 max-w-3xl">
                    <label for="vinInput" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('toolpages.common.enter_vin_label') }}</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input id="vinInput" type="text" maxlength="17" autocomplete="off" spellcheck="false"
                               placeholder="e.g. 1HGCM82633A004352"
                               class="flex-1 uppercase tracking-wider px-4 py-3 text-lg rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 shadow-sm outline-none">
                        <button id="yearBtn" type="button" class="px-6 py-3 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-lg transition shadow-sm">{{ __('toolpages.model_year_decoder.btn') }}</button>
                    </div>
                    <div id="yearResult" class="mt-4 hidden rounded-lg p-4 border"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div><h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.model_year_decoder.m1_h') }}</h2><p class="text-gray-600 leading-relaxed">{{ __('toolpages.model_year_decoder.m1_d') }}</p></div>
                <div><h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.model_year_decoder.m2_h') }}</h2><p class="text-gray-600 leading-relaxed">{{ __('toolpages.model_year_decoder.m2_d') }}</p></div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('toolpages.common.source_h') }}</h2>
                <p class="text-gray-600 text-sm leading-relaxed">{{ __('toolpages.model_year_decoder.source_d') }}</p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-acc-50 border border-acc-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('toolpages.model_year_decoder.cta_h') }}</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">{{ __('toolpages.model_year_decoder.cta_d') }}</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3.5 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">{{ __('toolpages.common.cta_history') }} &rarr;</a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const YEAR = {A:[1980,2010],B:[1981,2011],C:[1982,2012],D:[1983,2013],E:[1984,2014],F:[1985,2015],G:[1986,2016],H:[1987,2017],J:[1988,2018],K:[1989,2019],L:[1990,2020],M:[1991,2021],N:[1992,2022],P:[1993,2023],R:[1994,2024],S:[1995,2025],T:[1996,2026],V:[1997,2027],W:[1998,2028],X:[1999,2029],Y:[2000,2030],'1':[2001,2031],'2':[2002,2032],'3':[2003,2033],'4':[2004,2034],'5':[2005,2035],'6':[2006,2036],'7':[2007,2037],'8':[2008,2038],'9':[2009,2039]};
    const input = document.getElementById('vinInput');
    const btn = document.getElementById('yearBtn');
    const box = document.getElementById('yearResult');
    const MUST17 = @json(__('toolpages.common.must_17'));
    const T = {
        bad_code: @json(__('toolpages.model_year_decoder.js_bad_code')),
        my: @json(__('toolpages.model_year_decoder.js_my')),
        detail: @json(__('toolpages.model_year_decoder.js_detail')),
        cycle_old: @json(__('toolpages.model_year_decoder.js_cycle_old')),
        cycle_new: @json(__('toolpages.model_year_decoder.js_cycle_new')),
    };
    function show(kind, html) {
        box.className = 'mt-4 rounded-lg p-4 border';
        if (kind === 'ok')  box.className += ' bg-green-50 border-green-200 text-green-800';
        if (kind === 'bad') box.className += ' bg-red-50 border-red-200 text-red-800';
        box.innerHTML = html;
        box.classList.remove('hidden');
    }
    function decode() {
        const vin = (input.value || '').toUpperCase().trim();
        if (vin.length !== 17) { show('bad', MUST17); return; }
        const c10 = vin[9], c7 = vin[6];
        const pair = YEAR[c10];
        if (!pair) { show('bad', T.bad_code.replace(':c', c10)); return; }
        const isModern = /[A-Z]/.test(c7);
        const year = isModern ? pair[1] : pair[0];
        const cycle = isModern ? T.cycle_new : T.cycle_old;
        const detail = T.detail.replace(':c10', c10).replace(':c7', c7).replace(':cycle', cycle);
        show('ok', '<div class="text-sm text-green-700">' + T.my + '</div><div class="text-3xl font-bold">' + year + '</div><div class="text-sm mt-1">' + detail + '</div>');
    }
    btn.addEventListener('click', decode);
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') decode(); });
})();
</script>

@verbatim
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"VIN Model Year Decoder","applicationCategory":"UtilitiesApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"}}
</script>
@endverbatim
@include('footer')
