{{-- STAGING: VIN Check Service funnel page. Data from VinCheckController + i18n lang/vincheck. --}}
@include('header')
@php
    $homeUrl   = \App\Support\LocaleRoute::route('index');
    $hubUrl    = \App\Support\LocaleRoute::route('vincheck.index');
    $decodeApi = route('tools.decode-vin');
    $label     = __("vincheck.checks.$slug.label");
    $shows     = __("vincheck.checks.$slug.shows");
@endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a><span class="mx-2">/</span>
                    <a href="{{ $hubUrl }}" class="hover:text-primary-600">{{ __('vincheck.hub.crumb') }}</a><span class="mx-2">/</span>
                    <span class="text-gray-700">{{ $label }}</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ __("vincheck.checks.$slug.title") }}</h1>
                <p class="text-lg text-gray-700 mb-8">{{ __("vincheck.checks.$slug.intro") }}</p>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6">
                    <label for="vinInput" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('vincheck.show.enter_label') }}</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input id="vinInput" type="text" maxlength="17" autocomplete="off" spellcheck="false"
                               placeholder="e.g. 1HGCM82633A004352"
                               class="flex-1 uppercase tracking-wider px-4 py-3 text-lg rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 shadow-sm outline-none">
                        <button id="checkBtn" type="button"
                                class="px-6 py-3 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-lg transition shadow-sm whitespace-nowrap">{{ __('vincheck.show.check_btn') }}</button>
                    </div>
                    <div id="checkStatus" class="mt-3 text-sm text-gray-500 hidden"></div>
                    <div id="checkResult" class="mt-4 hidden"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('vincheck.show.what_title', ['label' => $label]) }}</h2>
                <p class="text-gray-600 leading-relaxed mb-4">{{ __('vincheck.show.full_includes', ['shows' => $shows, 'source' => $check['source']]) }}</p>
                <p class="text-gray-500 text-sm">{{ __('vincheck.show.source_note', ['source' => $check['source']]) }}</p>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const input = document.getElementById('vinInput');
    const btn = document.getElementById('checkBtn');
    const statusEl = document.getElementById('checkStatus');
    const result = document.getElementById('checkResult');
    const API = "{{ $decodeApi }}";
    const HOME = "{{ $homeUrl }}";
    const SHOWS = @json($shows);
    const SOURCE = @json($check['source']);
    const T = {
        invalid: @json(__('vincheck.show.invalid')),
        checking: @json(__('vincheck.show.checking')),
        availTitle: @json(__('vincheck.show.available_title')),
        found: @json(__('vincheck.show.found')),
        covered: @json(__('vincheck.show.covered')),
        cta: @json(__('vincheck.show.cta')),
    };

    function setStatus(m, show){ statusEl.textContent=m||''; statusEl.classList.toggle('hidden', !show); }
    function esc(s){ const d=document.createElement('div'); d.textContent=s==null?'':String(s); return d.innerHTML; }

    function funnel(vehicleLabel) {
        const veh = vehicleLabel ? T.found.replace(':veh', '<strong>' + esc(vehicleLabel) + '</strong>') : '';
        const covered = T.covered.replace(':source', esc(SOURCE)).replace(':shows', esc(SHOWS));
        result.className = 'mt-4 rounded-xl border border-green-200 bg-green-50 p-5';
        result.innerHTML =
            '<div class="flex items-start gap-2 mb-2"><span class="text-green-600 text-xl leading-none">✓</span>' +
            '<div class="font-semibold text-green-800">' + T.availTitle + '</div></div>' +
            '<p class="text-gray-700 text-sm mb-4">' + veh + covered + '</p>' +
            '<a href="' + HOME + '#vin-check" class="inline-block px-6 py-3 text-base bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-lg transition shadow-sm">' + T.cta + ' &rarr;</a>';
        result.classList.remove('hidden');
    }

    async function check() {
        const vin = (input.value || '').toUpperCase().trim();
        result.classList.add('hidden');
        if (vin.length !== 17) { setStatus(T.invalid, true); return; }
        setStatus(T.checking, true);
        let vehicle = '';
        try {
            const ctrl = new AbortController();
            const timer = setTimeout(function(){ ctrl.abort(); }, 15000);
            const res = await fetch(API + '?vin=' + encodeURIComponent(vin), {signal: ctrl.signal});
            clearTimeout(timer);
            const data = await res.json();
            const r = (data && data.result) || {};
            vehicle = [r.ModelYear, r.Make, r.Model].filter(Boolean).join(' ');
        } catch (e) { /* funnel anyway */ }
        setStatus('', false);
        funnel(vehicle);
    }

    btn.addEventListener('click', check);
    input.addEventListener('keydown', function(e){ if(e.key==='Enter') check(); });
})();
</script>

@include('footer')
