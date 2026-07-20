{{-- STAGING: Free tool — Car Payment Calculator. i18n lang/toolpages.car_payment + calc. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $toolsUrl=\App\Support\LocaleRoute::route('tools.index'); @endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">{{ __('nav.home') }}</a><span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">{{ __('tools.hub.crumb') }}</a><span class="mx-2">/</span>
                    <span class="text-gray-700">{{ __('tools.items.car-payment-calculator.label') }}</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">{{ __('tools.items.car-payment-calculator.title') }}</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">{{ __('toolpages.car_payment.lead') }}</p>

                <div class="grid lg:grid-cols-5 gap-6 max-w-4xl">
                    <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 space-y-4">
                        <div><label for="price" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_price') }}</label>
                            <input id="price" type="number" min="0" step="100" value="25000" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div><label for="down" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_down') }}</label>
                                <input id="down" type="number" min="0" step="100" value="3000" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                            <div><label for="trade" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_trade') }}</label>
                                <input id="trade" type="number" min="0" step="100" value="0" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        </div>
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div><label for="tax" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.car_payment.l_tax') }}</label>
                                <input id="tax" type="number" min="0" step="0.1" value="7" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                            <div><label for="apr" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_apr') }}</label>
                                <input id="apr" type="number" min="0" step="0.1" value="8.5" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                            <div><label for="term" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_term') }}</label>
                                <input id="term" type="number" min="1" step="1" value="60" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        </div>
                    </div>
                    <div class="lg:col-span-2 bg-gray-900 text-white rounded-xl shadow-sm p-6 flex flex-col justify-center">
                        <div class="text-gray-400 text-sm mb-1">{{ __('toolpages.car_payment.r_title') }}</div>
                        <div id="monthly" class="text-4xl font-bold mb-4">$0</div>
                        <dl class="text-sm space-y-1 text-gray-300">
                            <div class="flex justify-between"><dt>{{ __('toolpages.car_payment.r_financed') }}</dt><dd id="financed">$0</dd></div>
                            <div class="flex justify-between"><dt>{{ __('toolpages.car_payment.r_interest') }}</dt><dd id="interest">$0</dd></div>
                            <div class="flex justify-between"><dt>{{ __('toolpages.car_payment.r_total') }}</dt><dd id="totalPaid">$0</dd></div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.calc.how') }}</h2>
                    <p class="text-gray-600 leading-relaxed mb-3">{{ __('toolpages.car_payment.m1_d1') }}</p>
                    <p class="text-gray-600 leading-relaxed">{{ __('toolpages.car_payment.m1_d2') }} <span class="whitespace-nowrap font-mono text-sm">M = P·r·(1+r)ⁿ / ((1+r)ⁿ − 1)</span></p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.calc.gtk') }}</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5">
                        <li>{{ __('toolpages.car_payment.m2_li1') }}</li>
                        <li>{{ __('toolpages.car_payment.m2_li2') }}</li>
                        <li>{{ __('toolpages.car_payment.m2_li3') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('toolpages.common.source_h') }}</h2>
                <p class="text-gray-600 text-sm leading-relaxed">{{ __('toolpages.car_payment.source_d') }}
                    <a href="https://www.consumerfinance.gov/consumer-tools/auto-loans/" target="_blank" rel="noopener nofollow" class="text-acc-600 underline">CFPB</a></p>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-gray-600 leading-relaxed space-y-4">
                <h2 class="text-2xl font-bold text-gray-900">{{ __('toolpages.car_payment.seo_h') }}</h2>
                <p>{{ __('toolpages.car_payment.seo_p1') }}</p>
                <p>{{ __('toolpages.car_payment.seo_p2') }}</p>
            </div>
        </div>
    </section>

    <section class="py-10">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('toolpages.car_payment.faq_h') }}</h2>
                <div class="space-y-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-5"><h3 class="font-semibold text-gray-900 mb-1">{{ __('toolpages.car_payment.faq1_q') }}</h3><p class="text-gray-600 text-sm">{{ __('toolpages.car_payment.faq1_a') }}</p></div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5"><h3 class="font-semibold text-gray-900 mb-1">{{ __('toolpages.car_payment.faq2_q') }}</h3><p class="text-gray-600 text-sm">{{ __('toolpages.car_payment.faq2_a') }}</p></div>
                    <div class="bg-white rounded-xl border border-gray-200 p-5"><h3 class="font-semibold text-gray-900 mb-1">{{ __('toolpages.car_payment.faq3_q') }}</h3><p class="text-gray-600 text-sm">{{ __('toolpages.car_payment.faq3_a') }}</p></div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-acc-50 border border-acc-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('toolpages.car_payment.cta_h') }}</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">{{ __('toolpages.car_payment.cta_d') }}</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3.5 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">{{ __('toolpages.common.cta_history') }} &rarr;</a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const ids = ['price','down','trade','tax','apr','term'];
    const el = {}; ids.forEach(function (id) { el[id] = document.getElementById(id); });
    const outMonthly = document.getElementById('monthly'), outFinanced = document.getElementById('financed'), outInterest = document.getElementById('interest'), outTotal = document.getElementById('totalPaid');
    function usd(n){ if(!isFinite(n))n=0; return '$'+n.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    function num(v){ const n=parseFloat(v); return isFinite(n)&&n>0?n:0; }
    function calc(){
        const price=num(el.price.value), down=num(el.down.value), trade=num(el.trade.value), taxR=num(el.tax.value)/100, apr=num(el.apr.value)/100, term=Math.max(1,Math.round(num(el.term.value))||1);
        const taxable=Math.max(price-trade,0), taxAmt=taxable*taxR, financed=Math.max(price+taxAmt-down-trade,0), r=apr/12;
        let monthly = r===0 ? financed/term : financed*r/(1-Math.pow(1+r,-term));
        const totalPaid=monthly*term, interest=Math.max(totalPaid-financed,0);
        outMonthly.textContent=usd(monthly); outFinanced.textContent=usd(financed); outInterest.textContent=usd(interest); outTotal.textContent=usd(totalPaid);
    }
    ids.forEach(function(id){ el[id].addEventListener('input',calc); });
    calc();
})();
</script>

@verbatim
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"Car Payment Calculator","applicationCategory":"FinanceApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"}}
</script>
@endverbatim
@include('footer')
