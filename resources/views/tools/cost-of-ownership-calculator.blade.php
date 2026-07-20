{{-- STAGING: Free tool — True Cost of Ownership Calculator. i18n lang/toolpages.cost_ownership + calc. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $toolsUrl=\App\Support\LocaleRoute::route('tools.index'); @endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">{{ __('nav.home') }}</a><span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">{{ __('tools.hub.crumb') }}</a><span class="mx-2">/</span>
                    <span class="text-gray-700">{{ __('tools.items.cost-of-ownership-calculator.label') }}</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">{{ __('tools.items.cost-of-ownership-calculator.title') }}</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">{{ __('toolpages.cost_ownership.lead') }}</p>

                <div class="grid lg:grid-cols-5 gap-6 max-w-4xl">
                    <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 grid sm:grid-cols-2 gap-4">
                        <div><label for="price" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_purchase') }}</label>
                            <input id="price" type="number" min="0" step="500" value="30000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="years" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.cost_ownership.l_years') }}</label>
                            <input id="years" type="number" min="1" max="15" step="1" value="5" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="miles" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.cost_ownership.l_miles') }}</label>
                            <input id="miles" type="number" min="0" step="500" value="12000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="mpg" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_mpg') }}</label>
                            <input id="mpg" type="number" min="1" step="0.1" value="28" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="gas" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.cost_ownership.l_gas') }}</label>
                            <input id="gas" type="number" min="0" step="0.01" value="3.50" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="ins" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.cost_ownership.l_ins') }}</label>
                            <input id="ins" type="number" min="0" step="50" value="1400" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="maint" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.cost_ownership.l_maint') }}</label>
                            <input id="maint" type="number" min="0" step="50" value="800" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                    </div>
                    <div class="lg:col-span-2 bg-gray-900 text-white rounded-xl shadow-sm p-6 flex flex-col justify-center">
                        <div class="text-gray-400 text-sm mb-1">{{ __('toolpages.cost_ownership.r_after') }} <span id="yLabel">5</span> {{ __('toolpages.cost_ownership.r_yrs') }}</div>
                        <div id="total" class="text-4xl font-bold mb-4">$0</div>
                        <dl class="text-sm space-y-1 text-gray-300">
                            <div class="flex justify-between"><dt>{{ __('toolpages.cost_ownership.r_dep') }}</dt><dd id="dep">$0</dd></div>
                            <div class="flex justify-between"><dt>{{ __('toolpages.cost_ownership.r_fuel') }}</dt><dd id="fuel">$0</dd></div>
                            <div class="flex justify-between"><dt>{{ __('toolpages.cost_ownership.r_ins') }}</dt><dd id="inst">$0</dd></div>
                            <div class="flex justify-between"><dt>{{ __('toolpages.cost_ownership.r_maint') }}</dt><dd id="maintt">$0</dd></div>
                            <div class="flex justify-between border-t border-gray-700 mt-1 pt-1"><dt>{{ __('toolpages.cost_ownership.r_peryear') }}</dt><dd id="perYear">$0</dd></div>
                            <div class="flex justify-between"><dt>{{ __('toolpages.cost_ownership.r_permile') }}</dt><dd id="perMile">$0</dd></div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div><h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.calc.how') }}</h2><p class="text-gray-600 leading-relaxed">{{ __('toolpages.cost_ownership.m1_d') }}</p></div>
                <div><h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.calc.gtk') }}</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5"><li>{{ __('toolpages.cost_ownership.m2_li1') }}</li><li>{{ __('toolpages.cost_ownership.m2_li2') }}</li><li>{{ __('toolpages.cost_ownership.m2_li3') }}</li></ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('toolpages.common.source_h') }}</h2>
                <p class="text-gray-600 text-sm leading-relaxed">{{ __('toolpages.cost_ownership.source_d') }}
                    <a href="https://www.fueleconomy.gov/" target="_blank" rel="noopener nofollow" class="text-acc-600 underline">fueleconomy.gov</a></p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-acc-50 border border-acc-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('toolpages.cost_ownership.cta_h') }}</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">{{ __('toolpages.cost_ownership.cta_d') }}</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3.5 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">{{ __('toolpages.common.cta_history') }} &rarr;</a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const el={}; ['price','years','miles','mpg','gas','ins','maint'].forEach(function(id){el[id]=document.getElementById(id);});
    const out={}; ['total','dep','fuel','inst','maintt','perYear','perMile','yLabel'].forEach(function(id){out[id]=document.getElementById(id);});
    function num(v){const n=parseFloat(v);return isFinite(n)&&n>=0?n:0;}
    function usd(n){if(!isFinite(n))n=0;return '$'+Math.round(n).toLocaleString('en-US');}
    function calc(){
        const price=num(el.price.value), years=Math.max(1,Math.round(num(el.years.value))||1);
        const miles=num(el.miles.value), mpg=num(el.mpg.value), gas=num(el.gas.value);
        let val=price; for(let y=1;y<=years;y++){ val = y===1 ? val*0.80 : val*0.85; }
        const dep=price-val, fuel = mpg>0 ? (miles/mpg)*gas*years : 0, insurance=num(el.ins.value)*years, maintenance=num(el.maint.value)*years;
        const total=dep+fuel+insurance+maintenance;
        out.total.textContent=usd(total); out.dep.textContent=usd(dep); out.fuel.textContent=usd(fuel);
        out.inst.textContent=usd(insurance); out.maintt.textContent=usd(maintenance); out.perYear.textContent=usd(total/years);
        const totalMiles=miles*years; out.perMile.textContent = totalMiles>0 ? ('$'+(total/totalMiles).toFixed(2)) : '$0.00';
        out.yLabel.textContent=years;
    }
    ['price','years','miles','mpg','gas','ins','maint'].forEach(function(id){el[id].addEventListener('input',calc);});
    calc();
})();
</script>

@verbatim
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"Cost of Ownership Calculator","applicationCategory":"FinanceApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"}}
</script>
@endverbatim
@include('footer')
