{{-- STAGING: Free tool — VIN check-digit validator. i18n lang/toolpages.vin_validator. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $toolsUrl=\App\Support\LocaleRoute::route('tools.index'); @endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">{{ __('nav.home') }}</a><span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">{{ __('tools.hub.crumb') }}</a><span class="mx-2">/</span>
                    <span class="text-gray-700">{{ __('tools.items.vin-validator.label') }}</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">{{ __('tools.items.vin-validator.title') }}</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">{{ __('toolpages.vin_validator.lead') }}</p>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 max-w-3xl">
                    <label for="vinInput" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('toolpages.common.enter_vin_label') }}</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input id="vinInput" type="text" maxlength="17" autocomplete="off" spellcheck="false"
                               placeholder="e.g. 1HGCM82633A004352"
                               class="flex-1 uppercase tracking-wider px-4 py-3 text-lg rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 shadow-sm outline-none">
                        <button id="vinValidateBtn" type="button" class="px-6 py-3 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-lg transition shadow-sm">{{ __('toolpages.vin_validator.btn') }}</button>
                    </div>
                    <div id="vinResult" class="mt-4 hidden rounded-lg p-4 text-sm border"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.vin_validator.m1_h') }}</h2>
                    <p class="text-gray-600 leading-relaxed mb-3">{{ __('toolpages.vin_validator.m1_d1') }}</p>
                    <p class="text-gray-600 leading-relaxed">{{ __('toolpages.vin_validator.m1_d2') }}</p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.vin_validator.m2_h') }}</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5">
                        <li>{{ __('toolpages.vin_validator.m2_li1') }}</li>
                        <li>{{ __('toolpages.vin_validator.m2_li2') }}</li>
                        <li>{{ __('toolpages.vin_validator.m2_li3') }}</li>
                    </ul>
                    <p class="text-gray-400 text-sm mt-4">{{ __('toolpages.vin_validator.m2_note') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('toolpages.common.source_h') }}</h2>
                <p class="text-gray-600 text-sm leading-relaxed">{{ __('toolpages.vin_validator.source_d') }}
                    <a href="https://vpic.nhtsa.dot.gov/decoder/" target="_blank" rel="noopener nofollow" class="text-acc-600 underline">NHTSA vPIC</a>.</p>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-gray-600 leading-relaxed space-y-4">
                <h2 class="text-2xl font-bold text-gray-900">{{ __('toolpages.vin_validator.seo_h') }}</h2>
                <p>{{ __('toolpages.vin_validator.seo_p1') }}</p>
                <p>{{ __('toolpages.vin_validator.seo_p2') }}</p>
            </div>
        </div>
    </section>

    <section class="py-10">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('toolpages.vin_validator.faq_h') }}</h2>
                <div class="space-y-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-5"><h3 class="font-semibold text-gray-900 mb-1">{{ __('toolpages.vin_validator.faq1_q') }}</h3><p class="text-gray-600 text-sm">{{ __('toolpages.vin_validator.faq1_a') }}</p></div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5"><h3 class="font-semibold text-gray-900 mb-1">{{ __('toolpages.vin_validator.faq2_q') }}</h3><p class="text-gray-600 text-sm">{{ __('toolpages.vin_validator.faq2_a') }}</p></div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5"><h3 class="font-semibold text-gray-900 mb-1">{{ __('toolpages.vin_validator.faq3_q') }}</h3><p class="text-gray-600 text-sm">{{ __('toolpages.vin_validator.faq3_a') }}</p></div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-acc-50 border border-acc-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('toolpages.vin_validator.cta_h') }}</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">{{ __('toolpages.vin_validator.cta_d') }}</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3.5 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">{{ __('toolpages.common.cta_history') }} &rarr;</a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const MAP = {A:1,B:2,C:3,D:4,E:5,F:6,G:7,H:8,J:1,K:2,L:3,M:4,N:5,P:7,R:9,S:2,T:3,U:4,V:5,W:6,X:7,Y:8,Z:9,'0':0,'1':1,'2':2,'3':3,'4':4,'5':5,'6':6,'7':7,'8':8,'9':9};
    const WEIGHTS = [8,7,6,5,4,3,2,10,0,9,8,7,6,5,4,3,2];
    const input = document.getElementById('vinInput');
    const btn = document.getElementById('vinValidateBtn');
    const box = document.getElementById('vinResult');
    const T = {
        bad: @json(__('toolpages.vin_validator.js_bad')),
        len: @json(__('toolpages.vin_validator.js_len')),
        ioq: @json(__('toolpages.vin_validator.js_ioq')),
        chars: @json(__('toolpages.vin_validator.js_chars')),
        unexpected: @json(__('toolpages.vin_validator.js_unexpected')),
        valid_t: @json(__('toolpages.vin_validator.js_valid_t')),
        valid_d: @json(__('toolpages.vin_validator.js_valid_d')),
        mismatch_t: @json(__('toolpages.vin_validator.js_mismatch_t')),
        mismatch_d: @json(__('toolpages.vin_validator.js_mismatch_d')),
    };

    function show(kind, title, detail) {
        box.className = 'mt-4 rounded-lg p-4 text-sm border';
        if (kind === 'ok')   box.className += ' bg-green-50 border-green-200 text-green-800';
        if (kind === 'warn') box.className += ' bg-yellow-50 border-yellow-200 text-yellow-800';
        if (kind === 'bad')  box.className += ' bg-red-50 border-red-200 text-red-800';
        box.innerHTML = '<div class="font-semibold mb-1">' + title + '</div><div>' + detail + '</div>';
        box.classList.remove('hidden');
    }
    function validate() {
        const vin = (input.value || '').toUpperCase().trim();
        if (vin.length !== 17) { show('bad', T.bad, T.len.replace(':n', vin.length)); return; }
        if (/[IOQ]/.test(vin)) { show('bad', T.bad, T.ioq); return; }
        if (!/^[A-HJ-NPR-Z0-9]{17}$/.test(vin)) { show('bad', T.bad, T.chars); return; }
        let sum = 0;
        for (let i = 0; i < 17; i++) {
            const v = MAP[vin[i]];
            if (v === undefined) { show('bad', T.bad, T.unexpected.replace(':n', (i + 1))); return; }
            sum += v * WEIGHTS[i];
        }
        const rem = sum % 11;
        const expected = rem === 10 ? 'X' : String(rem);
        const actual = vin[8];
        if (expected === actual) { show('ok', T.valid_t, T.valid_d); }
        else { show('warn', T.mismatch_t, T.mismatch_d.replace(':actual', actual).replace(':expected', expected)); }
    }
    btn.addEventListener('click', validate);
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') validate(); });
})();
</script>

@verbatim
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"VIN Check-Digit Validator","applicationCategory":"UtilitiesApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"}}
</script>
@endverbatim
@include('footer')
