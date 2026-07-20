{{-- STAGING: "See what you get" — sample report (public/images/Example.pdf). i18n via lang/home. --}}
<section class="py-16 bg-white border-t border-gray-900/10">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="text-2xl lg:text-3xl font-extrabold tracking-tight text-gray-900 mb-4">{{ __('home.sample.title') }}</h2>
                <p class="text-gray-500 mb-5">{{ __('home.sample.lead') }}</p>
                <ul class="space-y-2.5 text-gray-700">
                    <li class="flex items-start gap-2"><span class="text-acc-500 mt-0.5 font-bold">&#10003;</span> {{ __('home.sample.b1') }}</li>
                    <li class="flex items-start gap-2"><span class="text-acc-500 mt-0.5 font-bold">&#10003;</span> {{ __('home.sample.b2') }}</li>
                    <li class="flex items-start gap-2"><span class="text-acc-500 mt-0.5 font-bold">&#10003;</span> {{ __('home.sample.b3') }}</li>
                    <li class="flex items-start gap-2"><span class="text-acc-500 mt-0.5 font-bold">&#10003;</span> {{ __('home.sample.b4') }}</li>
                    <li class="flex items-start gap-2"><span class="text-acc-500 mt-0.5 font-bold">&#10003;</span> {{ __('home.sample.b5') }}</li>
                </ul>
                <a href="{{ asset('images/Example.pdf') }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 mt-6 px-6 py-3.5 bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0l-4-4m4 4l4-4M4 20h16"/></svg>
                    {{ __('home.sample.cta') }}
                </a>
            </div>
            <div class="relative">
                <a href="{{ asset('images/Example.pdf') }}" target="_blank" rel="noopener" class="block group">
                    <div class="bg-white rounded-lg border border-gray-900/10 shadow-sm p-6 group-hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-lg font-bold text-gray-900">{{ __('home.sample.card_title') }}</div>
                            <span class="text-xs bg-acc-500 text-white font-bold px-2 py-1 rounded">SAMPLE</span>
                        </div>
                        <div class="text-sm text-gray-500 mb-4">2018 Lexus ES 350 · VIN 58ABK1GG6JU1****</div>
                        <div class="space-y-2">
                            <div class="h-2.5 bg-gray-100 rounded w-3/4"></div>
                            <div class="h-2.5 bg-gray-100 rounded w-full"></div>
                            <div class="h-2.5 bg-gray-100 rounded w-5/6"></div>
                            <div class="h-2.5 bg-gray-100 rounded w-2/3"></div>
                            <div class="grid grid-cols-3 gap-2 pt-3">
                                <div class="h-14 bg-gray-50 border border-gray-200 rounded"></div>
                                <div class="h-14 bg-gray-50 border border-gray-200 rounded"></div>
                                <div class="h-14 bg-gray-50 border border-gray-200 rounded"></div>
                            </div>
                        </div>
                        <div class="mt-4 text-acc-600 font-bold text-sm">{{ __('home.sample.open') }} &rarr;</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>
