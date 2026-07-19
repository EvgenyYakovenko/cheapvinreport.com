{{-- STAGING: Free tool — VIN check-digit validator (pure client-side, no API).
     EN (dev handoff): SEO tool page template. Meta comes from ToolController
     ($metaTitle/$metaDescription read by header). Layout matches the homepage:
     light #f8f9fa background, `container mx-auto px-4`, homepage input/button styles.
     Sections: hero+tool, how-it-works/methodology, source, SEO copy, FAQ(+schema), CTA. --}}
@include('header')

@php
    $homeUrl  = \App\Support\LocaleRoute::route('index');
    $toolsUrl = \App\Support\LocaleRoute::route('tools.index');
@endphp

<main class="bg-[#f8f9fa]">
    {{-- Hero + tool --}}
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a>
                    <span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">Tools</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700">VIN Validator</span>
                </nav>

                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">Free VIN Check-Digit Validator</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">
                    Paste a 17-character VIN and we'll verify it against the official ISO&nbsp;3779
                    check-digit formula — instantly, in your browser, with no sign-up.
                </p>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 max-w-3xl">
                    <label for="vinInput" class="block text-sm font-semibold text-gray-700 mb-2">
                        Enter VIN (17 characters)
                    </label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input id="vinInput" type="text" maxlength="17" autocomplete="off" spellcheck="false"
                               placeholder="e.g. 1HGCM82633A004352"
                               class="flex-1 uppercase tracking-wider px-4 py-3 text-lg rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 shadow-sm outline-none">
                        <button id="vinValidateBtn" type="button"
                                class="px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                            Validate
                        </button>
                    </div>
                    <div id="vinResult" class="mt-4 hidden rounded-lg p-4 text-sm border"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works / methodology --}}
    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">How the check digit works</h2>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        Every VIN issued for the North-American market carries a "check digit" in the
                        9th position. It is derived from the other 16 characters: each letter is
                        translated to a number, multiplied by a fixed positional weight, the products
                        are summed, and the remainder of that sum divided by 11 becomes the check
                        digit (a remainder of 10 is written as "X").
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Change any single character and the check digit almost always stops matching,
                        which is exactly how typos — and many fabricated VINs — are caught.
                    </p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">What this tool checks</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5">
                        <li>Length is exactly 17 characters</li>
                        <li>No invalid letters (I, O and Q are never used in a VIN)</li>
                        <li>The 9th-position check digit matches the ISO&nbsp;3779 formula</li>
                    </ul>
                    <p class="text-gray-400 text-sm mt-4">
                        Note: some vehicles built outside North America don't follow the check-digit
                        rule, so a mismatch isn't always proof of a fake VIN.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Source / references --}}
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Source &amp; methodology</h2>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Calculation follows the VIN standard <strong>ISO&nbsp;3779</strong> and the U.S.
                    check-digit requirement in <strong>49&nbsp;CFR&nbsp;§565</strong> (NHTSA). VIN
                    structure reference:
                    <a href="https://vpic.nhtsa.dot.gov/decoder/" target="_blank" rel="noopener nofollow"
                       class="text-primary-600 underline">NHTSA vPIC VIN Decoder</a>.
                    This tool runs entirely in your browser and does not store the VIN you enter.
                </p>
            </div>
        </div>
    </section>

    {{-- SEO copy --}}
    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto prose-sm text-gray-600 leading-relaxed space-y-4">
                <h2 class="text-2xl font-bold text-gray-900">Why validate a VIN before you buy?</h2>
                <p>
                    A Vehicle Identification Number is the 17-character fingerprint of a car. Before
                    you pay for a used vehicle — or order a history report — it's worth confirming the
                    number itself is genuine. A VIN that fails the check-digit test is often a simple
                    listing typo, but it can also signal a cloned or altered identity, where a
                    stolen car wears the plates and paperwork of a legitimate one.
                </p>
                <p>
                    Validation is instant and free, but it only tells you the number is
                    well-formed — not what the car has been through. For accidents, title brands,
                    odometer readings, liens and ownership history you need a full vehicle history
                    report, which pulls records tied to that VIN.
                </p>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-10">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">VIN validator FAQ</h2>
                <div class="space-y-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-1">Where is the check digit in a VIN?</h3>
                        <p class="text-gray-600 text-sm">It's the 9th character, counting from the left.</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-1">My VIN failed — is the car fake?</h3>
                        <p class="text-gray-600 text-sm">
                            Not necessarily. Re-check for a typo first. Vehicles built outside North
                            America may not follow the check-digit rule at all.
                        </p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-1">Do you store my VIN?</h3>
                        <p class="text-gray-600 text-sm">No. The check runs locally in your browser.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA / funnel --}}
    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-primary-50 border border-primary-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">A valid VIN is only the first step</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">
                    Validation confirms the number is real — it won't reveal accidents, title
                    problems, odometer rollbacks or past owners. For the full picture, pull a
                    vehicle history report from $3.
                </p>
                <a href="{{ $homeUrl }}#vin-check"
                   class="inline-block px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                    Check full vehicle history &rarr;
                </a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    // ISO 3779 transliteration + positional weights (North-American VINs).
    const MAP = {A:1,B:2,C:3,D:4,E:5,F:6,G:7,H:8,J:1,K:2,L:3,M:4,N:5,P:7,R:9,
                 S:2,T:3,U:4,V:5,W:6,X:7,Y:8,Z:9,
                 '0':0,'1':1,'2':2,'3':3,'4':4,'5':5,'6':6,'7':7,'8':8,'9':9};
    const WEIGHTS = [8,7,6,5,4,3,2,10,0,9,8,7,6,5,4,3,2];
    const input = document.getElementById('vinInput');
    const btn = document.getElementById('vinValidateBtn');
    const box = document.getElementById('vinResult');

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
        if (vin.length !== 17) {
            show('bad', 'Not a valid VIN', 'A VIN must be exactly 17 characters. You entered ' + vin.length + '.');
            return;
        }
        if (/[IOQ]/.test(vin)) {
            show('bad', 'Not a valid VIN', 'The letters I, O and Q are never used in a VIN.');
            return;
        }
        if (!/^[A-HJ-NPR-Z0-9]{17}$/.test(vin)) {
            show('bad', 'Not a valid VIN', 'A VIN may only contain letters (except I, O, Q) and digits.');
            return;
        }
        let sum = 0;
        for (let i = 0; i < 17; i++) {
            const v = MAP[vin[i]];
            if (v === undefined) { show('bad', 'Not a valid VIN', 'Unexpected character at position ' + (i + 1) + '.'); return; }
            sum += v * WEIGHTS[i];
        }
        const rem = sum % 11;
        const expected = rem === 10 ? 'X' : String(rem);
        const actual = vin[8];
        if (expected === actual) {
            show('ok', 'Valid VIN', 'The format is correct and the check digit matches the ISO 3779 formula.');
        } else {
            show('warn', 'Check-digit mismatch', 'Format looks fine, but the 9th character is "' + actual +
                 '" while the formula expects "' + expected + '". Likely a typo — or a non-North-American VIN.');
        }
    }

    btn.addEventListener('click', validate);
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') validate(); });
})();
</script>

@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "VIN Check-Digit Validator",
  "applicationCategory": "UtilitiesApplication",
  "operatingSystem": "Any",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    { "@type": "Question", "name": "Where is the check digit in a VIN?",
      "acceptedAnswer": { "@type": "Answer", "text": "It is the 9th character, counting from the left." } },
    { "@type": "Question", "name": "My VIN failed the check — is the car fake?",
      "acceptedAnswer": { "@type": "Answer", "text": "Not necessarily. Check for a typo first; vehicles built outside North America may not follow the check-digit rule." } },
    { "@type": "Question", "name": "Do you store my VIN?",
      "acceptedAnswer": { "@type": "Answer", "text": "No. The check runs locally in your browser." } }
  ]
}
</script>
@endverbatim

@include('footer')
