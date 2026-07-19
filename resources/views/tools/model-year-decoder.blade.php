{{-- STAGING: Free tool — VIN Model Year Decoder (pure client-side, no API). --}}
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
                    <span class="text-gray-700">Model Year Decoder</span>
                </nav>

                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">Free VIN Model Year Decoder</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">
                    The 10th character of a VIN encodes the model year. Enter a VIN and we'll read it —
                    using the 7th character to tell the 1980–2009 and 2010–2039 cycles apart.
                </p>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 max-w-3xl">
                    <label for="vinInput" class="block text-sm font-semibold text-gray-700 mb-2">Enter VIN (17 characters)</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input id="vinInput" type="text" maxlength="17" autocomplete="off" spellcheck="false"
                               placeholder="e.g. 1HGCM82633A004352"
                               class="flex-1 uppercase tracking-wider px-4 py-3 text-lg rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 shadow-sm outline-none">
                        <button id="yearBtn" type="button"
                                class="px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                            Decode year
                        </button>
                    </div>
                    <div id="yearResult" class="mt-4 hidden rounded-lg p-4 border"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">How the year is encoded</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Model year uses a 30-year repeating code in the 10th VIN position — for
                        example "A" is 1980 or 2010, "R" is 1994 or 2024. To resolve which cycle a
                        car belongs to, the 7th character is a number for model years 1980–2009 and a
                        letter for 2010 onward.
                    </p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Good to know</h2>
                    <p class="text-gray-600 leading-relaxed">
                        The letters I, O, Q, U, Z and the number 0 are never used as year codes. Model
                        year is the manufacturer's designated year, which can differ from the calendar
                        date the car was actually built.
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
                    Follows the VIN model-year encoding defined in <strong>ISO&nbsp;3779</strong> and
                    U.S. <strong>49&nbsp;CFR&nbsp;§565</strong> (NHTSA). Runs entirely in your browser.
                </p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-primary-50 border border-primary-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Confirmed the year? Check the history</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">
                    A history report from $3.00 shows accidents, title brands, odometer and ownership
                    tied to this VIN.
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
    // 10th-char code -> [first cycle year, second cycle year]
    const YEAR = {A:[1980,2010],B:[1981,2011],C:[1982,2012],D:[1983,2013],E:[1984,2014],
        F:[1985,2015],G:[1986,2016],H:[1987,2017],J:[1988,2018],K:[1989,2019],L:[1990,2020],
        M:[1991,2021],N:[1992,2022],P:[1993,2023],R:[1994,2024],S:[1995,2025],T:[1996,2026],
        V:[1997,2027],W:[1998,2028],X:[1999,2029],Y:[2000,2030],
        '1':[2001,2031],'2':[2002,2032],'3':[2003,2033],'4':[2004,2034],'5':[2005,2035],
        '6':[2006,2036],'7':[2007,2037],'8':[2008,2038],'9':[2009,2039]};
    const input = document.getElementById('vinInput');
    const btn = document.getElementById('yearBtn');
    const box = document.getElementById('yearResult');

    function show(kind, html) {
        box.className = 'mt-4 rounded-lg p-4 border';
        if (kind === 'ok')  box.className += ' bg-green-50 border-green-200 text-green-800';
        if (kind === 'bad') box.className += ' bg-red-50 border-red-200 text-red-800';
        box.innerHTML = html;
        box.classList.remove('hidden');
    }

    function decode() {
        const vin = (input.value || '').toUpperCase().trim();
        if (vin.length !== 17) { show('bad', 'A VIN must be exactly 17 characters.'); return; }
        const c10 = vin[9], c7 = vin[6];
        const pair = YEAR[c10];
        if (!pair) { show('bad', 'The 10th character "' + c10 + '" is not a valid year code.'); return; }
        const isModern = /[A-Z]/.test(c7); // letter in position 7 => 2010+
        const year = isModern ? pair[1] : pair[0];
        show('ok', '<div class="text-sm text-green-700">Model year</div><div class="text-3xl font-bold">' + year +
            '</div><div class="text-sm mt-1">10th character: <strong>' + c10 + '</strong> · 7th character: <strong>' +
            c7 + '</strong> (' + (isModern ? '2010–2039 cycle' : '1980–2009 cycle') + ')</div>');
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
  "name": "VIN Model Year Decoder",
  "applicationCategory": "UtilitiesApplication",
  "operatingSystem": "Any",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" }
}
</script>
@endverbatim

@include('footer')
