{{-- STAGING: Free tool — VIN Decoder. Client-side fetch to NHTSA vPIC (free, no key, CORS-enabled).
     EN (dev handoff): follows the SEO tool template (see tools/vin-validator.blade.php). --}}
@include('header')

@php
    $homeUrl  = \App\Support\LocaleRoute::route('index');
    $toolsUrl = \App\Support\LocaleRoute::route('tools.index');
@endphp

<main class="bg-[#f8f9fa]">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a>
                    <span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">Tools</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700">VIN Decoder</span>
                </nav>

                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">Free VIN Decoder</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">
                    Enter a 17-character VIN to decode the make, model, year, body style, engine and
                    more — straight from the official U.S. NHTSA database. Free, no sign-up.
                </p>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 max-w-3xl">
                    <label for="vinInput" class="block text-sm font-semibold text-gray-700 mb-2">Enter VIN (17 characters)</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input id="vinInput" type="text" maxlength="17" autocomplete="off" spellcheck="false"
                               placeholder="e.g. 1HGCM82633A004352"
                               class="flex-1 uppercase tracking-wider px-4 py-3 text-lg rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 shadow-sm outline-none">
                        <button id="vinDecodeBtn" type="button"
                                class="px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                            Decode
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

    {{-- Methodology --}}
    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">What a VIN decode shows</h2>
                    <p class="text-gray-600 leading-relaxed">
                        The first 11 characters of a VIN encode the manufacturer, brand, vehicle type,
                        body style, engine, restraint system, model year and assembly plant. A decoder
                        translates those codes into plain specifications. The last six characters are
                        the unique serial number of the individual vehicle.
                    </p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">What it does not show</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Decoding returns factory specifications only — not the car's real-world
                        history. Accidents, title brands, odometer readings, recalls performed and
                        previous owners are recorded separately and require a vehicle history report.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Source --}}
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Source &amp; methodology</h2>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Data comes live from the U.S. National Highway Traffic Safety Administration
                    <a href="https://vpic.nhtsa.dot.gov/" target="_blank" rel="noopener nofollow" class="text-primary-600 underline">NHTSA vPIC</a>
                    Product Information Catalog — the official government VIN database. We query it
                    directly from your browser and don't store your VIN.
                </p>
            </div>
        </div>
    </section>

    {{-- SEO copy --}}
    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-gray-600 leading-relaxed space-y-4">
                <h2 class="text-2xl font-bold text-gray-900">Decode first, then check the history</h2>
                <p>
                    Decoding a VIN is the quickest way to confirm a listing is honest: the make,
                    model, year and engine returned by the government database should match what the
                    seller claims. A mismatch is a red flag worth questioning before you go further.
                </p>
                <p>
                    Specs confirm what the car <em>is</em>. To learn what it has been <em>through</em>
                    — accidents, salvage or flood titles, odometer history and past owners — pull a
                    full vehicle history report tied to the same VIN.
                </p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-primary-50 border border-primary-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Specs check out? See the full history</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">
                    A history report from $3.00 reveals accidents, title brands, odometer records and
                    ownership tied to this VIN.
                </p>
                <a href="{{ $homeUrl }}#vin-check"
                   class="inline-block px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                    Check vehicle history &rarr;
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

    // NHTSA field label -> response key
    const FIELDS = [
        ['Make', 'Make'], ['Model', 'Model'], ['Model year', 'ModelYear'],
        ['Trim', 'Trim'], ['Series', 'Series'], ['Body class', 'BodyClass'],
        ['Vehicle type', 'VehicleType'], ['Doors', 'Doors'], ['Drive type', 'DriveType'],
        ['Engine cylinders', 'EngineCylinders'], ['Displacement (L)', 'DisplacementL'],
        ['Engine HP', 'EngineHP'], ['Fuel type', 'FuelTypePrimary'],
        ['Transmission', 'TransmissionStyle'], ['Manufacturer', 'Manufacturer'],
        ['Plant country', 'PlantCountry'], ['Plant state', 'PlantState']
    ];

    function setStatus(msg, show) {
        statusEl.textContent = msg || '';
        statusEl.classList.toggle('hidden', !show);
    }

    async function decode() {
        const vin = (input.value || '').toUpperCase().trim();
        resultEl.classList.add('hidden');
        if (vin.length !== 17) { setStatus('A VIN must be exactly 17 characters.', true); return; }
        setStatus('Decoding…', true);
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
            titleEl.textContent = [year, make, model].filter(Boolean).join(' ') || 'Decoded VIN';
            grid.innerHTML = '';
            let shown = 0;
            FIELDS.forEach(function (f) {
                const val = r[f[1]];
                if (val && String(val).trim() && String(val).trim() !== '0') {
                    grid.insertAdjacentHTML('beforeend',
                        '<div class="flex justify-between border-b border-gray-100 py-1"><dt class="text-gray-500">' +
                        f[0] + '</dt><dd class="text-gray-900 font-medium text-right">' + String(val) + '</dd></div>');
                    shown++;
                }
            });
            if (shown === 0) {
                setStatus('No data found for this VIN. Check for typos.', true);
            } else {
                setStatus(err && err.indexOf('0 -') !== 0 ? ('Note: ' + err) : '', !!err && err.indexOf('0 -') !== 0);
                resultEl.classList.remove('hidden');
                if (!(err && err.indexOf('0 -') !== 0)) setStatus('', false);
            }
        } catch (e) {
            setStatus('Could not reach the NHTSA service. Please try again in a moment.', true);
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
