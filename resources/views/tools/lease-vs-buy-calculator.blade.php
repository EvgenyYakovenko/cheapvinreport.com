{{-- STAGING: Free tool — Lease vs Buy Calculator (pure client-side JS). --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $toolsUrl=\App\Support\LocaleRoute::route('tools.index'); @endphp

<main class="bg-[#f8f9fa]">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a><span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">Tools</a><span class="mx-2">/</span>
                    <span class="text-gray-700">Lease vs Buy Calculator</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">Free Lease vs Buy Calculator</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">Compare the total cost of leasing versus buying the same car over the same period, and see which comes out cheaper. Updates as you type.</p>

                <div class="grid md:grid-cols-2 gap-6 max-w-4xl">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-3">
                        <h2 class="font-bold text-gray-900">Buy</h2>
                        <div><label for="price" class="block text-sm font-semibold text-gray-700 mb-1">Vehicle price ($)</label>
                            <input id="price" type="number" min="0" step="500" value="30000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="down" class="block text-sm font-semibold text-gray-700 mb-1">Down payment ($)</label>
                            <input id="down" type="number" min="0" step="100" value="3000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label for="apr" class="block text-sm font-semibold text-gray-700 mb-1">APR (%)</label>
                                <input id="apr" type="number" min="0" step="0.1" value="8.5" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                            <div><label for="resale" class="block text-sm font-semibold text-gray-700 mb-1">Resale at end ($)</label>
                                <input id="resale" type="number" min="0" step="500" value="16000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-3">
                        <h2 class="font-bold text-gray-900">Lease</h2>
                        <div><label for="lmonthly" class="block text-sm font-semibold text-gray-700 mb-1">Monthly lease payment ($)</label>
                            <input id="lmonthly" type="number" min="0" step="10" value="350" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="lupfront" class="block text-sm font-semibold text-gray-700 mb-1">Due at signing ($)</label>
                            <input id="lupfront" type="number" min="0" step="100" value="2000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="term" class="block text-sm font-semibold text-gray-700 mb-1">Term for both (months)</label>
                            <input id="term" type="number" min="1" step="1" value="36" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                    </div>
                </div>

                <div id="verdict" class="mt-6 max-w-4xl bg-primary-600 text-white rounded-xl shadow-sm p-6">
                    <div id="verdictText" class="text-2xl font-bold mb-3">—</div>
                    <dl class="text-sm grid sm:grid-cols-2 gap-x-8 gap-y-1 text-primary-50">
                        <div class="flex justify-between"><dt>Net cost to buy</dt><dd id="buyCost">$0</dd></div>
                        <div class="flex justify-between"><dt>Net cost to lease</dt><dd id="leaseCost">$0</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">How it's calculated</h2>
                    <p class="text-gray-600 leading-relaxed">Buying: down payment plus all loan payments over the term, minus the car's resale value at the end (you keep that value). Leasing: money due at signing plus all lease payments — with nothing owned at the end.</p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Good to know</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5">
                        <li>Leases limit mileage and charge for wear; buying doesn't.</li>
                        <li>Resale value is the biggest swing factor in buying.</li>
                        <li>Taxes and fees vary and aren't included here.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Source &amp; methodology</h2>
                <p class="text-gray-600 text-sm leading-relaxed">Standard net-cost comparison using the amortized-loan formula for the purchase side. Estimates only; consult the <a href="https://www.consumerfinance.gov/consumer-tools/auto-loans/" target="_blank" rel="noopener nofollow" class="text-primary-600 underline">CFPB</a>. Runs in your browser.</p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-primary-50 border border-primary-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Buying used? Check the history first</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">A report from $3.00 shows accidents, title brands, odometer and ownership tied to the VIN.</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">Check vehicle history &rarr;</a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const el={}; ['price','down','apr','resale','lmonthly','lupfront','term'].forEach(function(id){el[id]=document.getElementById(id);});
    const buyEl=document.getElementById('buyCost'), leaseEl=document.getElementById('leaseCost'), vt=document.getElementById('verdictText');
    function num(v){const n=parseFloat(v);return isFinite(n)&&n>0?n:0;}
    function usd(n){if(!isFinite(n))n=0;return '$'+Math.round(n).toLocaleString('en-US');}
    function calc(){
        const price=num(el.price.value), down=num(el.down.value), r=num(el.apr.value)/100/12, resale=num(el.resale.value);
        const term=Math.max(1,Math.round(num(el.term.value))||1);
        const financed=Math.max(price-down,0);
        const monthly = r===0 ? financed/term : financed*r/(1-Math.pow(1+r,-term));
        const buyNet = down + monthly*term - resale;
        const leaseNet = num(el.lupfront.value) + num(el.lmonthly.value)*term;
        buyEl.textContent=usd(buyNet); leaseEl.textContent=usd(leaseNet);
        const diff=Math.abs(buyNet-leaseNet);
        vt.textContent = buyNet < leaseNet ? ('Buying is cheaper by ' + usd(diff)) : (leaseNet < buyNet ? ('Leasing is cheaper by ' + usd(diff)) : 'Buying and leasing cost about the same');
    }
    ['price','down','apr','resale','lmonthly','lupfront','term'].forEach(function(id){el[id].addEventListener('input',calc);});
    calc();
})();
</script>

@verbatim
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"Lease vs Buy Calculator","applicationCategory":"FinanceApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"}}
</script>
@endverbatim
@include('footer')
