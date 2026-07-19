{{-- STAGING: Free tool — Recall Check by VIN. Server proxy: decode VIN -> NHTSA recalls. --}}
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
                    <span class="text-gray-700">Recall Check by VIN</span>
                </nav>

                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">Free Recall Check by VIN</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">
                    Enter a VIN to see open safety recalls reported for that vehicle in the official
                    U.S. NHTSA database. Free, instant, no sign-up.
                </p>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 max-w-3xl">
                    <label for="vinInput" class="block text-sm font-semibold text-gray-700 mb-2">Enter VIN (17 characters)</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input id="vinInput" type="text" maxlength="17" autocomplete="off" spellcheck="false"
                               placeholder="e.g. 1HGCM82633A004352"
                               class="flex-1 uppercase tracking-wider px-4 py-3 text-lg rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 shadow-sm outline-none">
                        <button id="recallBtn" type="button"
                                class="px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                            Check recalls
                        </button>
                    </div>
                    <div id="recallStatus" class="mt-3 text-sm text-gray-500 hidden"></div>
                    <div id="recallSummary" class="mt-4 hidden rounded-lg p-4 border"></div>
                    <div id="recallList" class="mt-4 space-y-3"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">What a recall means</h2>
                    <p class="text-gray-600 leading-relaxed">
                        A safety recall is issued when a manufacturer or the NHTSA finds a defect that
                        creates an unreasonable safety risk. The fix is normally free at a franchised
                        dealer, no matter who owns the car now. Recalls don't expire — an older open
                        recall can still be repaired.
                    </p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">How this check works</h2>
                    <p class="text-gray-600 leading-relaxed">
                        We decode the VIN to its make, model and year, then look up recall campaigns
                        for that vehicle. This shows recalls issued for the model; whether a specific
                        repair was already completed on this exact car is confirmed by a dealer using
                        the VIN.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Source &amp; methodology</h2>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Recall data comes from the U.S. National Highway Traffic Safety Administration
                    <a href="https://www.nhtsa.gov/recalls" target="_blank" rel="noopener nofollow" class="text-primary-600 underline">NHTSA recalls database</a>,
                    queried live by make, model and year. We don't store your VIN.
                </p>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-gray-600 leading-relaxed space-y-4">
                <h2 class="text-2xl font-bold text-gray-900">Recalls are only part of the story</h2>
                <p>
                    Open recalls tell you about known manufacturing defects — but not what has
                    happened to this particular car. Accidents, salvage or flood titles, odometer
                    rollbacks and past ownership are recorded separately and can matter just as much
                    to safety and value.
                </p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-primary-50 border border-primary-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">See the full history, not just recalls</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">
                    A history report from $3.00 shows accidents, title brands, odometer records and
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
    const btn = document.getElementById('recallBtn');
    const statusEl = document.getElementById('recallStatus');
    const summary = document.getElementById('recallSummary');
    const list = document.getElementById('recallList');
    const API = "{{ route('tools.recalls') }}";

    function setStatus(msg, show) { statusEl.textContent = msg || ''; statusEl.classList.toggle('hidden', !show); }
    function esc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }

    async function check() {
        const vin = (input.value || '').toUpperCase().trim();
        summary.classList.add('hidden'); list.innerHTML = '';
        if (vin.length !== 17) { setStatus('A VIN must be exactly 17 characters.', true); return; }
        setStatus('Checking recalls…', true);
        try {
            const ctrl = new AbortController();
            const timer = setTimeout(function () { ctrl.abort(); }, 15000);
            const res = await fetch(API + '?vin=' + encodeURIComponent(vin), {signal: ctrl.signal});
            clearTimeout(timer);
            const data = await res.json();
            if (data.error) { setStatus(data.error, true); return; }
            setStatus('', false);

            const vehicle = data.vehicle || 'this vehicle';
            const count = data.count || 0;
            summary.className = 'mt-4 rounded-lg p-4 border ' +
                (count > 0 ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : 'bg-green-50 border-green-200 text-green-800');
            summary.innerHTML = count > 0
                ? '<strong>' + count + ' recall' + (count > 1 ? 's' : '') + '</strong> found for ' + esc(vehicle) + '.'
                : 'No open recalls found for ' + esc(vehicle) + '.';
            summary.classList.remove('hidden');

            (data.recalls || []).forEach(function (r) {
                list.insertAdjacentHTML('beforeend',
                    '<div class="bg-white rounded-xl border border-gray-200 p-5">' +
                    '<div class="flex justify-between items-start gap-3 mb-1">' +
                    '<h3 class="font-semibold text-gray-900">' + esc(r.component || 'Recall') + '</h3>' +
                    (r.campaign ? '<span class="text-xs text-gray-400 whitespace-nowrap">' + esc(r.campaign) + '</span>' : '') +
                    '</div>' +
                    (r.summary ? '<p class="text-gray-600 text-sm mb-2">' + esc(r.summary) + '</p>' : '') +
                    (r.remedy ? '<p class="text-gray-500 text-sm"><strong>Remedy:</strong> ' + esc(r.remedy) + '</p>' : '') +
                    '</div>');
            });
        } catch (e) {
            setStatus('Could not reach the recall service. Please try again in a moment.', true);
        }
    }

    btn.addEventListener('click', check);
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') check(); });
})();
</script>

@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "Recall Check by VIN",
  "applicationCategory": "UtilitiesApplication",
  "operatingSystem": "Any",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" }
}
</script>
@endverbatim

@include('footer')
