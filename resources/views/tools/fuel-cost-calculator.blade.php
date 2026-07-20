{{-- STAGING: Free tool — Fuel Cost Calculator. i18n lang/toolpages.fuel_cost + calc. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $toolsUrl=\App\Support\LocaleRoute::route('tools.index'); @endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">{{ __('nav.home') }}</a><span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">{{ __('tools.hub.crumb') }}</a><span class="mx-2">/</span>
                    <span class="text-gray-700">{{ __('tools.items.fuel-cost-calculator.label') }}</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">{{ __('tools.items.fuel-cost-calculator.title') }}</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">{{ __('toolpages.fuel_cost.lead') }}</p>

                <div class="grid lg:grid-cols-5 gap-6 max-w-4xl">
                    <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 space-y-4">
                        <div><label for="distance" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.fuel_cost.l_distance') }}</label>
                            <input id="distance" type="number" min="0" step="1" value="1000" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div><label for="mpg" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_mpg') }}</label>
                                <input id="mpg" type="number" min="1" step="0.1" value="28" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                            <div><label for="price" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.fuel_cost.l_gas') }}</label>
                                <input id="price" type="number" min="0" step="0.01" value="3.50" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input id="roundtrip" type="checkbox" class="rounded border-gray-300"> {{ __('toolpages.fuel_cost.l_rt') }}
                        </label>
                    </div>
                    <div class="lg:col-span-2 bg-gray-900 text-white rounded-xl shadow-sm p-6 flex flex-col justify-center">
                        <div class="text-gray-400 text-sm mb-1">{{ __('toolpages.fuel_cost.r_title') }}</div>
                        <div id="total" class="text-4xl font-bold mb-4">$0</div>
                        <dl class="text-sm space-y-1 text-gray-300">
                            <div class="flex justify-between"><dt>{{ __('toolpages.fuel_cost.r_gallons') }}</dt><dd id="gallons">0</dd></div>
                            <div class="flex justify-between"><dt>{{ __('toolpages.fuel_cost.r_permile') }}</dt><dd id="perMile">$0</dd></div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div><h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.calc.how') }}</h2><p class="text-gray-600 leading-relaxed">{{ __('toolpages.fuel_cost.m1_d') }}</p></div>
                <div><h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.fuel_cost.m2_h') }}</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5"><li>{{ __('toolpages.fuel_cost.m2_li1') }}</li><li>{{ __('toolpages.fuel_cost.m2_li2') }}</li><li>{{ __('toolpages.fuel_cost.m2_li3') }}</li></ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('toolpages.common.source_h') }}</h2>
                <p class="text-gray-600 text-sm leading-relaxed">{{ __('toolpages.fuel_cost.source_d') }}
                    <a href="https://www.fueleconomy.gov/" target="_blank" rel="noopener nofollow" class="text-acc-600 underline">fueleconomy.gov</a></p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-acc-50 border border-acc-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('toolpages.fuel_cost.cta_h') }}</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">{{ __('toolpages.fuel_cost.cta_d') }}</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3.5 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">{{ __('toolpages.common.cta_history') }} &rarr;</a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const d=document.getElementById('distance'), mpg=document.getElementById('mpg'), price=document.getElementById('price'), rt=document.getElementById('roundtrip');
    const total=document.getElementById('total'), gallonsEl=document.getElementById('gallons'), perMileEl=document.getElementById('perMile');
    function num(v){ const n=parseFloat(v); return isFinite(n)&&n>0?n:0; }
    function usd(n){ if(!isFinite(n))n=0; return '$'+n.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    function calc(){
        let dist=num(d.value); if(rt.checked) dist*=2;
        const economy=num(mpg.value), gas=num(price.value), gallons=economy>0?dist/economy:0, cost=gallons*gas;
        total.textContent=usd(cost); gallonsEl.textContent=gallons.toLocaleString('en-US',{maximumFractionDigits:1});
        perMileEl.textContent = dist>0 ? usd(cost/dist) : '$0.00';
    }
    [d,mpg,price].forEach(function(e){ e.addEventListener('input',calc); });
    rt.addEventListener('change',calc); calc();
})();
</script>

@verbatim
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"Fuel Cost Calculator","applicationCategory":"FinanceApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"}}
</script>
@endverbatim
@include('footer')
