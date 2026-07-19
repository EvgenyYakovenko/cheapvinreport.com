{{-- STAGING: Free tool — Car Payment (auto-loan) Calculator. Pure client-side JS.
     EN (dev handoff): follows the SEO tool template (see tools/vin-validator.blade.php). --}}
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
                    <span class="text-gray-700">Car Payment Calculator</span>
                </nav>

                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">Free Car Payment Calculator</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">
                    Estimate your monthly auto-loan payment from the price, down payment, trade-in,
                    sales tax, APR and loan term. Updates as you type — no sign-up.
                </p>

                <div class="grid lg:grid-cols-5 gap-6 max-w-4xl">
                    {{-- Inputs --}}
                    <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 space-y-4">
                        <div>
                            <label for="price" class="block text-sm font-semibold text-gray-700 mb-1">Vehicle price ($)</label>
                            <input id="price" type="number" min="0" step="100" value="25000"
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="down" class="block text-sm font-semibold text-gray-700 mb-1">Down payment ($)</label>
                                <input id="down" type="number" min="0" step="100" value="3000"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                            <div>
                                <label for="trade" class="block text-sm font-semibold text-gray-700 mb-1">Trade-in value ($)</label>
                                <input id="trade" type="number" min="0" step="100" value="0"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                        </div>
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div>
                                <label for="tax" class="block text-sm font-semibold text-gray-700 mb-1">Sales tax (%)</label>
                                <input id="tax" type="number" min="0" step="0.1" value="7"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                            <div>
                                <label for="apr" class="block text-sm font-semibold text-gray-700 mb-1">APR (%)</label>
                                <input id="apr" type="number" min="0" step="0.1" value="8.5"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                            <div>
                                <label for="term" class="block text-sm font-semibold text-gray-700 mb-1">Term (months)</label>
                                <input id="term" type="number" min="1" step="1" value="60"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                        </div>
                    </div>

                    {{-- Result --}}
                    <div class="lg:col-span-2 bg-primary-600 text-white rounded-xl shadow-sm p-6 flex flex-col justify-center">
                        <div class="text-primary-100 text-sm mb-1">Estimated monthly payment</div>
                        <div id="monthly" class="text-4xl font-bold mb-4">$0</div>
                        <dl class="text-sm space-y-1 text-primary-50">
                            <div class="flex justify-between"><dt>Amount financed</dt><dd id="financed">$0</dd></div>
                            <div class="flex justify-between"><dt>Total interest</dt><dd id="interest">$0</dd></div>
                            <div class="flex justify-between"><dt>Total of payments</dt><dd id="totalPaid">$0</dd></div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works / methodology --}}
    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">How it's calculated</h2>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        The amount financed is the vehicle price plus sales tax, minus your down
                        payment and trade-in. Sales tax here is applied to the price after the
                        trade-in credit (the rule in most U.S. states).
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        The monthly payment uses the standard amortized-loan formula:
                        <span class="whitespace-nowrap font-mono text-sm">M = P·r·(1+r)ⁿ / ((1+r)ⁿ − 1)</span>,
                        where <em>P</em> is the amount financed, <em>r</em> is the monthly rate
                        (APR ÷ 12) and <em>n</em> is the number of months.
                    </p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Good to know</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5">
                        <li>This is an estimate — dealer fees, registration and gap insurance aren't included.</li>
                        <li>Your real APR depends on credit score, lender and term.</li>
                        <li>A larger down payment or shorter term lowers total interest.</li>
                    </ul>
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
                    Uses the standard amortized-loan payment formula. For guidance on auto-loan
                    terms, APR and total cost, see the U.S.
                    <a href="https://www.consumerfinance.gov/consumer-tools/auto-loans/" target="_blank" rel="noopener nofollow"
                       class="text-primary-600 underline">Consumer Financial Protection Bureau (CFPB)</a>.
                    All math runs in your browser; nothing you enter is stored.
                </p>
            </div>
        </div>
    </section>

    {{-- SEO copy --}}
    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-gray-600 leading-relaxed space-y-4">
                <h2 class="text-2xl font-bold text-gray-900">Know the payment before you shop</h2>
                <p>
                    Monthly payment is where most car budgets are won or lost. By adjusting the down
                    payment, term and APR you can see exactly how each lever changes what you pay —
                    and how much of it is interest. Stretching a loan to 72 or 84 months lowers the
                    monthly figure but can add thousands in interest over the life of the loan.
                </p>
                <p>
                    Once you've settled on a budget and found a specific used car, the next step is
                    confirming the vehicle is worth it. A history report tied to the VIN shows
                    accidents, title brands and odometer records — the things that quietly wreck
                    resale value and safety.
                </p>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-10">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Car payment FAQ</h2>
                <div class="space-y-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-1">Does a bigger down payment lower my payment?</h3>
                        <p class="text-gray-600 text-sm">Yes — it reduces the amount financed, so both the monthly payment and total interest drop.</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-1">Is a longer term cheaper?</h3>
                        <p class="text-gray-600 text-sm">The monthly payment is lower, but you usually pay more total interest over a longer term.</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-1">Are taxes and fees included?</h3>
                        <p class="text-gray-600 text-sm">Sales tax is included; dealer fees, registration and add-ons are not — add them to the price for a closer estimate.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA / funnel --}}
    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-primary-50 border border-primary-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Found the right price? Check the car's past</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">
                    Before you sign, pull a full vehicle history report from $3.00 — accidents, title
                    brands, odometer and ownership history tied to the VIN.
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
    const ids = ['price','down','trade','tax','apr','term'];
    const el = {};
    ids.forEach(function (id) { el[id] = document.getElementById(id); });
    const outMonthly = document.getElementById('monthly');
    const outFinanced = document.getElementById('financed');
    const outInterest = document.getElementById('interest');
    const outTotal = document.getElementById('totalPaid');

    function usd(n) {
        if (!isFinite(n)) n = 0;
        return '$' + n.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    function num(v) { const n = parseFloat(v); return isFinite(n) && n > 0 ? n : 0; }

    function calc() {
        const price = num(el.price.value);
        const down  = num(el.down.value);
        const trade = num(el.trade.value);
        const taxR  = num(el.tax.value) / 100;
        const apr   = num(el.apr.value) / 100;
        const term  = Math.max(1, Math.round(num(el.term.value)) || 1);

        const taxable = Math.max(price - trade, 0);
        const taxAmt = taxable * taxR;
        const financed = Math.max(price + taxAmt - down - trade, 0);

        const r = apr / 12;
        let monthly;
        if (r === 0) {
            monthly = financed / term;
        } else {
            monthly = financed * r / (1 - Math.pow(1 + r, -term));
        }
        const totalPaid = monthly * term;
        const interest = Math.max(totalPaid - financed, 0);

        outMonthly.textContent = usd(monthly);
        outFinanced.textContent = usd(financed);
        outInterest.textContent = usd(interest);
        outTotal.textContent = usd(totalPaid);
    }

    ids.forEach(function (id) { el[id].addEventListener('input', calc); });
    calc();
})();
</script>

@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "Car Payment Calculator",
  "applicationCategory": "FinanceApplication",
  "operatingSystem": "Any",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" }
}
</script>
@endverbatim

@include('footer')
