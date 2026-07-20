{{-- STAGING: Free tool — VIN Decoder. i18n lang/toolpages.vin_decoder + toolpages.common. --}}
@include('header')

@php
    $homeUrl  = \App\Support\LocaleRoute::route('index');
    $toolsUrl = \App\Support\LocaleRoute::route('tools.index');
@endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">{{ __('nav.home') }}</a>
                    <span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">{{ __('tools.hub.crumb') }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700">{{ __('tools.items.vin-decoder.label') }}</span>
                </nav>

                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">{{ __('tools.items.vin-decoder.title') }}</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">{{ __('toolpages.vin_decoder.lead') }}</p>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 max-w-3xl">
                    <label for="vinInput" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('toolpages.common.enter_vin_label') }}</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input id="vinInput" type="text" maxlength="17" autocomplete="off" spellcheck="false"
                               placeholder="e.g. 1HGCM82633A004352"
                               class="flex-1 uppercase tracking-wider px-4 py-3 text-lg rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 shadow-sm outline-none">
                        <button id="vinDecodeBtn" type="button"
                                class="px-6 py-3 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-lg transition shadow-sm">
                            {{ __('toolpages.vin_decoder.btn') }}
                        </button>
                    </div>
                    <div id="vinStatus" class="mt-3 text-sm text-gray-500 hidden"></div>
                    <div id="vinResult" class="mt-5 hidden">
                        <div id="vinTitle" class="text-xl font-bold text-gray-900 mb-3"></div>
                        <dl id="vinGrid" class="grid sm:grid-cols-2 gap-x-6 gap-y-2 text-sm"></dl>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.vin_decoder.m1_h') }}</h2>
                    <p class="text-gray-600 leading-relaxed">{{ __('toolpages.vin_decoder.m1_d') }}</p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.vin_decoder.m2_h') }}</h2>
                    <p class="text-gray-600 leading-relaxed">{{ __('toolpages.vin_decoder.m2_d') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('toolpages.common.source_h') }}</h2>
                <p class="text-gray-600 text-sm leading-relaxed">
                    {{ __('toolpages.vin_decoder.source_d') }}
                    <a href="https://vpic.nhtsa.dot.gov/" target="_blank" rel="noopener nofollow" class="text-acc-600 underline">NHTSA vPIC</a>
                </p>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-gray-600 leading-relaxed space-y-4">
                <h2 class="text-2xl font-bold text-gray-900">{{ __('toolpages.vin_decoder.seo_h') }}</h2>
                <p>{{ __('toolpages.vin_decoder.seo_p1') }}</p>
                <p>{{ __('toolpages.vin_decoder.seo_p2') }}</p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-acc-50 border border-acc-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('toolpages.vin_decoder.cta_h') }}</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">{{ __('toolpages.vin_decoder.cta_d') }}</p>
                <a href="{{ $homeUrl }}#vin-check"
                   class="inline-block px-6 py-3.5 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">
                    {{ __('toolpages.common.cta_history') }} &rarr;
                </a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const input = document.getElementById('vinInput');
    const btn = document.getElementById('vinDecodeBtn');
    const statusEl = document.getElementById('vinStatus');
    const resultEl = document.getElementById('vinResult');
    const titleEl = document.getElementById('vinTitle');
    const grid = document.getElementById('vinGrid');
    const API = "{{ route('tools.decode-vin') }}";
    const F = @json(__('toolpages.common.fields'));
    const T = {
        must17: @json(__('toolpages.common.must_17')),
        decoding: @json(__('toolpages.common.decoding')),
        noData: @json(__('toolpages.common.no_data')),
        unreachable: @json(__('toolpages.common.unreachable')),
        note: @json(__('toolpages.common.note')),
    };
    const FIELDS = ['Make','Model','ModelYear','Trim','Series','BodyClass','VehicleType','Doors','DriveType','EngineCylinders','DisplacementL','EngineHP','FuelTypePrimary','TransmissionStyle','Manufacturer','PlantCountry','PlantState'];

    function setStatus(msg, show) {
        statusEl.textContent = msg || '';
        statusEl.classList.toggle('hidden', !show);
    }

    async function decode() {
        const vin = (input.value || '').toUpperCase().trim();
        resultEl.classList.add('hidden');
        if (vin.length !== 17) { setStatus(T.must17, true); return; }
        setStatus(T.decoding, true);
        try {
            const ctrl = new AbortController();
            const timer = setTimeout(function () { ctrl.abort(); }, 15000);
            const res = await fetch(API + '?vin=' + encodeURIComponent(vin), {signal: ctrl.signal});
            clearTimeout(timer);
            const data = await res.json();
            if (data.error) { setStatus(data.error, true); return; }
            const r = data.result || {};
            const err = r.ErrorText || '';
            const year = r.ModelYear || '';
            const make = r.Make || '';
            const model = r.Model || '';
            titleEl.textContent = [year, make, model].filter(Boolean).join(' ') || 'VIN';
            grid.innerHTML = '';
            let shown = 0;
            FIELDS.forEach(function (key) {
                const val = r[key];
                if (val && String(val).trim() && String(val).trim() !== '0') {
                    grid.insertAdjacentHTML('beforeend',
                        '<div class="flex justify-between border-b border-gray-100 py-1"><dt class="text-gray-500">' +
                        (F[key] || key) + '</dt><dd class="text-gray-900 font-medium text-right">' + String(val) + '</dd></div>');
                    shown++;
                }
            });
            if (shown === 0) {
                setStatus(T.noData, true);
            } else {
                const hasNote = !!err && err.indexOf('0 -') !== 0;
                setStatus(hasNote ? (T.note + ' ' + err) : '', hasNote);
                resultEl.classList.remove('hidden');
                if (!hasNote) setStatus('', false);
            }
        } catch (e) {
            setStatus(T.unreachable, true);
        }
    }

    btn.addEventListener('click', decode);
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') decode(); });
})();
</script>

@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "VIN Decoder",
  "applicationCategory": "UtilitiesApplication",
  "operatingSystem": "Any",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" }
}
</script>
@endverbatim

@include('footer')
