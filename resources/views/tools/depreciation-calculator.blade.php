{{-- STAGING: Free tool — Car Depreciation Calculator. i18n lang/toolpages.depreciation + calc. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $toolsUrl=\App\Support\LocaleRoute::route('tools.index'); @endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">{{ __('nav.home') }}</a><span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">{{ __('tools.hub.crumb') }}</a><span class="mx-2">/</span>
                    <span class="text-gray-700">{{ __('tools.items.depreciation-calculator.label') }}</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">{{ __('tools.items.depreciation-calculator.title') }}</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">{{ __('toolpages.depreciation.lead') }}</p>

                <div class="grid lg:grid-cols-5 gap-6 max-w-4xl">
                    <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 space-y-4">
                        <div><label for="price" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.calc.l_purchase') }}</label>
                            <input id="price" type="number" min="0" step="500" value="30000" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div><label for="years" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.depreciation.l_years') }}</label>
                                <input id="years" type="number" min="1" max="15" step="1" value="5" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                            <div><label for="first" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.depreciation.l_first') }}</label>
                                <input id="first" type="number" min="0" step="1" value="20" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                            <div><label for="rate" class="block text-sm font-semibold text-gray-700 mb-1">{{ __('toolpages.depreciation.l_rate') }}</label>
                                <input id="rate" type="number" min="0" step="1" value="15" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none"></div>
                        </div>
                        <div id="yearTable" class="text-sm text-gray-600 border-t border-gray-100 pt-3"></div>
                    </div>
                    <div class="lg:col-span-2 bg-gray-900 text-white rounded-xl shadow-sm p-6 flex flex-col justify-center">
                        <div class="text-gray-400 text-sm mb-1">{{ __('toolpages.depreciation.r_after') }} <span id="yLabel">5</span> {{ __('toolpages.depreciation.r_yrs') }}</div>
                        <div id="future" class="text-4xl font-bold mb-4">$0</div>
                        <dl class="text-sm space-y-1 text-gray-300">
                            <div class="flex justify-between"><dt>{{ __('toolpages.depreciation.r_lost') }}</dt><dd id="lost">$0</dd></div>
                            <div class="flex justify-between"><dt>{{ __('toolpages.depreciation.r_retained') }}</dt><dd id="retained">0%</dd></div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
                <div><h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.calc.how') }}</h2><p class="text-gray-600 leading-relaxed">{{ __('toolpages.depreciation.m1_d') }}</p></div>
                <div><h2 class="text-2xl font-bold text-gray-900 mb-3">{{ __('toolpages.calc.gtk') }}</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5"><li>{{ __('toolpages.depreciation.m2_li1') }}</li><li>{{ __('toolpages.depreciation.m2_li2') }}</li><li>{{ __('toolpages.depreciation.m2_li3') }}</li></ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('toolpages.common.source_h') }}</h2>
                <p class="text-gray-600 text-sm leading-relaxed">{{ __('toolpages.depreciation.source_d') }}</p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-acc-50 border border-acc-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('toolpages.depreciation.cta_h') }}</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">{{ __('toolpages.depreciation.cta_d') }}</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3.5 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">{{ __('toolpages.common.cta_history') }} &rarr;</a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const el={}; ['price','years','first','rate'].forEach(function(id){el[id]=document.getElementById(id);});
    const futureEl=document.getElementById('future'), lostEl=document.getElementById('lost'), retEl=document.getElementById('retained'), yLabel=document.getElementById('yLabel'), tbl=document.getElementById('yearTable');
    const YEARLBL = @json(__('toolpages.depreciation.js_year'));
    function num(v){const n=parseFloat(v);return isFinite(n)&&n>=0?n:0;}
    function usd(n){if(!isFinite(n))n=0;return '$'+Math.round(n).toLocaleString('en-US');}
    function calc(){
        const price=num(el.price.value); const years=Math.max(1,Math.round(num(el.years.value))||1);
        const first=num(el.first.value)/100, rate=num(el.rate.value)/100;
        let val=price; let rows='';
        for(let y=1;y<=years;y++){ val = y===1 ? val*(1-first) : val*(1-rate); rows += '<div class="flex justify-between py-0.5"><span>'+YEARLBL+' '+y+'</span><span>'+usd(val)+'</span></div>'; }
        futureEl.textContent=usd(val); lostEl.textContent=usd(price-val);
        retEl.textContent=(price>0?Math.round(val/price*100):0)+'%'; yLabel.textContent=years; tbl.innerHTML=rows;
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
