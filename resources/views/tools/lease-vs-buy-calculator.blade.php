{{-- STAGING: Free tool — Lease vs Buy Calculator. i18n lang/toolpages.lease_buy + calc. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $toolsUrl=\App\Support\LocaleRoute::route('tools.index'); @endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">{{ __('nav.home') }}</a><span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">{{ __('tools.hub.crumb') }}</a><span class="mx-2">/</span>
                    <span class="text-gray-700">{{ __('tools.items.lease-vs-buy-calculator.label') }}</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">{{ __('tools.items.lease-vs-buy-calculator.title') }}</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">{{ __('toolpages.lease_buy.lead') }}</p>

                <div class="grid md:grid-cols-2 gap-6 max-w-4xl">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-3">
                        <h2 class="font-bold text-gray-900">{{ __('toolpages.lease_buy.buy_h') }}</h2>
                        <div><label for="price" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_price') }}</label>
                            <input id="price" type="number" min="0" step="500" value="30000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="down" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_down') }}</label>
                            <input id="down" type="number" min="0" step="100" value="3000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label for="apr" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_apr') }}</label>
                                <input id="apr" type="number" min="0" step="0.1" value="8.5" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                            <div><label for="resale" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.lease_buy.l_resale') }}</label>
                                <input id="resale" type="number" min="0" step="500" value="16000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-3">
                        <h2 class="font-bold text-gray-900">{{ __('toolpages.lease_buy.lease_h') }}</h2>
                        <div><label for="lmonthly" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.lease_buy.l_lmonthly') }}</label>
                            <input id="lmonthly" type="number" min="0" step="10" value="350" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="lupfront" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.lease_buy.l_lupfront') }}</label>
                            <input id="lupfront" type="number" min="0" step="100" value="2000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="term" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.lease_buy.l_term') }}</label>
                            <input id="term" type="number" min="1" step="1" value="36" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                    </div>
                </div>

                <div id="verdict" class="mt-6 max-w-4xl bg-gray-900 text-white rounded-xl shadow-sm p-6">
                    <div id="verdictText" class="text-2xl font-bold mb-3">—</div>
                    <dl class="text-sm grid sm:grid-cols-2 gap-x-8 gap-y-1 text-gray-300">
                        <div class="flex justify-between"><dt>{{ __('toolpages.lease_buy.r_buy') }}</dt><dd id="buyCost">$0</dd></div>
                        <div class="flex justify-between"><dt>{{ __('toolpages.lease_buy.r_lease') }}</dt><dd id="leaseCost">$0</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div><h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.calc.how') }}</h2><p class="text-gray-600 leading-relaxed">{{ __('toolpages.lease_buy.m1_d') }}</p></div>
                <div><h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.calc.gtk') }}</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5"><li>{{ __('toolpages.lease_buy.m2_li1') }}</li><li>{{ __('toolpages.lease_buy.m2_li2') }}</li><li>{{ __('toolpages.lease_buy.m2_li3') }}</li></ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('toolpages.common.source_h') }}</h2>
                <p class="text-gray-600 text-sm leading-relaxed">{{ __('toolpages.lease_buy.source_d') }}
                    <a href="https://www.consumerfinance.gov/consumer-tools/auto-loans/" target="_blank" rel="noopener nofollow" class="text-acc-600 underline">CFPB</a></p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-acc-50 border border-acc-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('toolpages.lease_buy.cta_h') }}</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">{{ __('toolpages.lease_buy.cta_d') }}</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3.5 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">{{ __('toolpages.common.cta_history') }} &rarr;</a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const el={}; ['price','down','apr','resale','lmonthly','lupfront','term'].forEach(function(id){el[id]=document.getElementById(id);});
    const buyEl=document.getElementById('buyCost'), leaseEl=document.getElementById('leaseCost'), vt=document.getElementById('verdictText');
    const T = { buy: @json(__('toolpages.lease_buy.js_buy')), lease: @json(__('toolpages.lease_buy.js_lease')), same: @json(__('toolpages.lease_buy.js_same')) };
    function num(v){const n=parseFloat(v);return isFinite(n)&&n>0?n:0;}
    function usd(n){if(!isFinite(n))n=0;return '$'+Math.round(n).toLocaleString('en-US');}
    function calc(){
        const price=num(el.price.value), down=num(el.down.value), r=num(el.apr.value)/100/12, resale=num(el.resale.value), term=Math.max(1,Math.round(num(el.term.value))||1);
        const financed=Math.max(price-down,0);
        const monthly = r===0 ? financed/term : financed*r/(1-Math.pow(1+r,-term));
        const buyNet = down + monthly*term - resale;
        const leaseNet = num(el.lupfront.value) + num(el.lmonthly.value)*term;
        buyEl.textContent=usd(buyNet); leaseEl.textContent=usd(leaseNet);
        const diff=Math.abs(buyNet-leaseNet);
        vt.textContent = buyNet < leaseNet ? T.buy.replace(':d', usd(diff)) : (leaseNet < buyNet ? T.lease.replace(':d', usd(diff)) : T.same);
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
