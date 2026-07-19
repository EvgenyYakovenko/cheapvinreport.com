{{-- STAGING: Free tool — Car Affordability Calculator (pure client-side JS). --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $toolsUrl=\App\Support\LocaleRoute::route('tools.index'); @endphp

<main class="bg-[#f8f9fa]">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a><span class="mx-2">/</span>
                    <a href="{{ $toolsUrl }}" class="hover:text-primary-600">Tools</a><span class="mx-2">/</span>
                    <span class="text-gray-700">Car Affordability Calculator</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">Free Car Affordability Calculator</h1>
                <p class="text-lg text-gray-700 mb-8 max-w-3xl">See how much car you can afford from a monthly payment you're comfortable with, your down payment, APR and loan term. Updates as you type.</p>

                <div class="grid lg:grid-cols-5 gap-6 max-w-4xl">
                    <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:p-6 space-y-4">
                        <div>
                            <label for="budget" class="block text-sm font-semibold text-gray-700 mb-1">Monthly payment budget ($)</label>
                            <input id="budget" type="number" min="0" step="10" value="400" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="down" class="block text-sm font-semibold text-gray-700 mb-1">Down payment ($)</label>
                                <input id="down" type="number" min="0" step="100" value="3000" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                            <div>
                                <label for="trade" class="block text-sm font-semibold text-gray-700 mb-1">Trade-in value ($)</label>
                                <input id="trade" type="number" min="0" step="100" value="0" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="apr" class="block text-sm font-semibold text-gray-700 mb-1">APR (%)</label>
                                <input id="apr" type="number" min="0" step="0.1" value="8.5" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                            <div>
                                <label for="term" class="block text-sm font-semibold text-gray-700 mb-1">Term (months)</label>
                                <input id="term" type="number" min="1" step="1" value="60" class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900 outline-none">
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-2 bg-primary-600 text-white rounded-xl shadow-sm p-6 flex flex-col justify-center">
                        <div class="text-primary-100 text-sm mb-1">You can afford a car up to</div>
                        <div id="price" class="text-4xl font-bold mb-4">$0</div>
                        <dl class="text-sm space-y-1 text-primary-50">
                            <div class="flex justify-between"><dt>Max loan amount</dt><dd id="loan">$0</dd></div>
                            <div class="flex justify-between"><dt>Down + trade-in</dt><dd id="cash">$0</dd></div>
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
                    <p class="text-gray-600 leading-relaxed">We work backwards from your monthly budget. The most you can borrow is the present value of those payments at your APR and term; add your down payment and trade-in and you get the vehicle price you can afford.</p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Good to know</h2>
                    <ul class="text-gray-600 leading-relaxed space-y-2 list-disc pl-5">
                        <li>This is the vehicle price before taxes and dealer fees.</li>
                        <li>A common rule of thumb keeps car payments under 15% of take-home pay.</li>
                        <li>Insurance and running costs are on top of the payment.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Source &amp; methodology</h2>
                <p class="text-gray-600 text-sm leading-relaxed">Uses the present-value form of the standard amortized-loan formula. For budgeting guidance see the U.S. <a href="https://www.consumerfinance.gov/consumer-tools/auto-loans/" target="_blank" rel="noopener nofollow" class="text-primary-600 underline">CFPB</a>. Runs in your browser.</p>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-primary-50 border border-primary-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Found a car in budget? Check its history</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">A report from $3.00 shows accidents, title brands, odometer and ownership tied to the VIN.</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">Check vehicle history &rarr;</a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    const el = {}; ['budget','down','trade','apr','term'].forEach(function(id){el[id]=document.getElementById(id);});
    const priceEl=document.getElementById('price'), loanEl=document.getElementById('loan'), cashEl=document.getElementById('cash');
    function num(v){const n=parseFloat(v);return isFinite(n)&&n>0?n:0;}
    function usd(n){if(!isFinite(n))n=0;return '$'+n.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});}
    function calc(){
        const pay=num(el.budget.value), down=num(el.down.value), trade=num(el.trade.value);
        const r=num(el.apr.value)/100/12, n=Math.max(1,Math.round(num(el.term.value))||1);
        let loan = r===0 ? pay*n : pay*(1-Math.pow(1+r,-n))/r;
        const cash=down+trade;
        priceEl.textContent=usd(loan+cash); loanEl.textContent=usd(loan); cashEl.textContent=usd(cash);
    }
    ['budget','down','trade','apr','term'].forEach(function(id){el[id].addEventListener('input',calc);});
    calc();
})();
</script>

@verbatim
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"Car Affordability Calculator","applicationCategory":"FinanceApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"}}
</script>
@endverbatim
@include('footer')
