{{-- STAGING: Free tool — Car Depreciation Calculator (pure client-side JS). --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $toolsUrl=\App\Support\LocaleRoute::route('tools.index'); @endphp

<main class="bg-[#f8f9fa]">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a><span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">Tools</a><span class="mx-2">/</span>
                    <span class="text-gray-700">Depreciation Calculator</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">Free Car Depreciation Calculator</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">Estimate what a car will be worth in the years ahead and how much value it loses to depreciation. Updates as you type.</p>

                <div class="grid lg:grid-cols-5 gap-6 max-w-4xl">
                    <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 space-y-4">
                        <div>
                            <label for="price" class="block text-sm font-semibold text-gray-700 mb-1">Purchase price ($)</label>
                            <input id="price" type="number" min="0" step="500" value="30000" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                        </div>
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div>
                                <label for="years" class="block text-sm font-semibold text-gray-700 mb-1">Years</label>
                                <input id="years" type="number" min="1" max="15" step="1" value="5" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                            <div>
                                <label for="first" class="block text-sm font-semibold text-gray-700 mb-1">1st-year drop (%)</label>
                                <input id="first" type="number" min="0" step="1" value="20" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                            <div>
                                <label for="rate" class="block text-sm font-semibold text-gray-700 mb-1">After (%/yr)</label>
                                <input id="rate" type="number" min="0" step="1" value="15" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                        </div>
                        <div id="yearTable" class="text-sm text-gray-600 border-t border-gray-100 pt-3"></div>
                    </div>
                    <div class="lg:col-span-2 bg-primary-600 text-white rounded-xl shadow-sm p-6 flex flex-col justify-center">
                        <div class="text-primary-100 text-sm mb-1">Estimated value after <span id="yLabel">5</span> yrs</div>
                        <div id="future" class="text-4xl font-bold mb-4">$0</div>
                        <dl class="text-sm space-y-1 text-primary-50">
                            <div class="flex justify-between"><dt>Total depreciation</dt><dd id="lost">$0</dd></div>
                            <div class="flex justify-between"><dt>Value retained</dt><dd id="retained">0%</dd></div>
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
                    <p class="text-gray-600 leading-relaxed">New cars lose the most value in year one — often around 20% — then a steadier ~15% each following year. We apply your first-year drop, then the yearly rate, compounding on the remaining value.</p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Good to know</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5">
                        <li>Actual depreciation varies by make, mileage and condition.</li>
                        <li>A clean history report helps a car hold value at resale.</li>
                        <li>High-demand models depreciate slower than average.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Source &amp; methodology</h2>
                <p class="text-gray-600 text-sm leading-relaxed">Compound declining-balance depreciation with an elevated first-year rate, the widely used industry model. Estimates only. Runs in your browser.</p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-primary-50 border border-primary-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Resale value starts with a clean history</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">Check accidents, title brands and odometer with a report from $3.00 before you buy.</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">Check vehicle history &rarr;</a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const el={}; ['price','years','first','rate'].forEach(function(id){el[id]=document.getElementById(id);});
    const futureEl=document.getElementById('future'), lostEl=document.getElementById('lost'), retEl=document.getElementById('retained'), yLabel=document.getElementById('yLabel'), tbl=document.getElementById('yearTable');
    function num(v){const n=parseFloat(v);return isFinite(n)&&n>=0?n:0;}
    function usd(n){if(!isFinite(n))n=0;return '$'+Math.round(n).toLocaleString('en-US');}
    function calc(){
        const price=num(el.price.value); const years=Math.max(1,Math.round(num(el.years.value))||1);
        const first=num(el.first.value)/100, rate=num(el.rate.value)/100;
        let val=price; let rows='';
        for(let y=1;y<=years;y++){ val = y===1 ? val*(1-first) : val*(1-rate); rows += '<div class="flex justify-between py-0.5"><span>Year '+y+'</span><span>'+usd(val)+'</span></div>'; }
        futureEl.textContent=usd(val); lostEl.textContent=usd(price-val);
        retEl.textContent=(price>0?Math.round(val/price*100):0)+'%'; yLabel.textContent=years;
        tbl.innerHTML=rows;
    }
    ['price','years','first','rate'].forEach(function(id){el[id].addEventListener('input',calc);});
    calc();
})();
</script>

@verbatim
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"Car Depreciation Calculator","applicationCategory":"FinanceApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"}}
</script>
@endverbatim
@include('footer')
