{{-- STAGING: Free tool — True Cost of Ownership Calculator (pure client-side JS). --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $toolsUrl=\App\Support\LocaleRoute::route('tools.index'); @endphp

<main class="bg-[#f8f9fa]">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a><span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">Tools</a><span class="mx-2">/</span>
                    <span class="text-gray-700">Cost of Ownership Calculator</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">Free Cost of Ownership Calculator</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">The sticker price is only part of what a car costs. Add depreciation, fuel, insurance and maintenance to see the true cost of ownership. Updates as you type.</p>

                <div class="grid lg:grid-cols-5 gap-6 max-w-4xl">
                    <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 grid sm:grid-cols-2 gap-4">
                        <div><label for="price" class="block text-sm font-semibold text-gray-700 mb-1">Purchase price ($)</label>
                            <input id="price" type="number" min="0" step="500" value="30000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="years" class="block text-sm font-semibold text-gray-700 mb-1">Years of ownership</label>
                            <input id="years" type="number" min="1" max="15" step="1" value="5" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="miles" class="block text-sm font-semibold text-gray-700 mb-1">Miles per year</label>
                            <input id="miles" type="number" min="0" step="500" value="12000" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="mpg" class="block text-sm font-semibold text-gray-700 mb-1">Fuel economy (MPG)</label>
                            <input id="mpg" type="number" min="1" step="0.1" value="28" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="gas" class="block text-sm font-semibold text-gray-700 mb-1">Gas price ($/gal)</label>
                            <input id="gas" type="number" min="0" step="0.01" value="3.50" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="ins" class="block text-sm font-semibold text-gray-700 mb-1">Insurance ($/yr)</label>
                            <input id="ins" type="number" min="0" step="50" value="1400" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div><label for="maint" class="block text-sm font-semibold text-gray-700 mb-1">Maintenance ($/yr)</label>
                            <input id="maint" type="number" min="0" step="50" value="800" class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                    </div>
                    <div class="lg:col-span-2 bg-primary-600 text-white rounded-xl shadow-sm p-6 flex flex-col justify-center">
                        <div class="text-primary-100 text-sm mb-1">True cost over <span id="yLabel">5</span> yrs</div>
                        <div id="total" class="text-4xl font-bold mb-4">$0</div>
                        <dl class="text-sm space-y-1 text-primary-50">
                            <div class="flex justify-between"><dt>Depreciation</dt><dd id="dep">$0</dd></div>
                            <div class="flex justify-between"><dt>Fuel</dt><dd id="fuel">$0</dd></div>
                            <div class="flex justify-between"><dt>Insurance</dt><dd id="inst">$0</dd></div>
                            <div class="flex justify-between"><dt>Maintenance</dt><dd id="maintt">$0</dd></div>
                            <div class="flex justify-between border-t border-primary-400 mt-1 pt-1"><dt>Per year</dt><dd id="perYear">$0</dd></div>
                            <div class="flex justify-between"><dt>Per mile</dt><dd id="perMile">$0</dd></div>
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
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">How it's calculated</h2>
                    <p class="text-gray-600 leading-relaxed">We add the four biggest ownership costs: depreciation (value lost over the period, ~20% year one then ~15%/yr), fuel (miles ÷ MPG × price), insurance and maintenance. The total is divided by years and miles for per-year and per-mile figures.</p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Good to know</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5">
                        <li>Depreciation is usually the single biggest cost of owning a car.</li>
                        <li>Financing interest, taxes and registration are extra.</li>
                        <li>A well-documented history helps limit depreciation.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Source &amp; methodology</h2>
                <p class="text-gray-600 text-sm leading-relaxed">Combines standard depreciation, fuel, insurance and maintenance estimates. Fuel-economy ratings: <a href="https://www.fueleconomy.gov/" target="_blank" rel="noopener nofollow" class="text-primary-600 underline">fueleconomy.gov</a>. Estimates only; runs in your browser.</p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-primary-50 border border-primary-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Lower surprise costs — check the history</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">Hidden accident or title problems raise repair and insurance costs. A report from $3.00 reveals them before you buy.</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">Check vehicle history &rarr;</a>
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
        const dep=price-val;
        const fuel = mpg>0 ? (miles/mpg)*gas*years : 0;
        const insurance=num(el.ins.value)*years, maintenance=num(el.maint.value)*years;
        const total=dep+fuel+insurance+maintenance;
        out.total.textContent=usd(total); out.dep.textContent=usd(dep); out.fuel.textContent=usd(fuel);
        out.inst.textContent=usd(insurance); out.maintt.textContent=usd(maintenance);
        out.perYear.textContent=usd(total/years);
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
