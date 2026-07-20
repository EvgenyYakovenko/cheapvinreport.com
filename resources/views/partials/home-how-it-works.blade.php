{{-- STAGING: How it works — 3 steps (Demo 1 style). i18n via lang/home. --}}
<section class="py-16 bg-white border-t border-gray-900/10">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-[13px] uppercase tracking-[.2em] text-acc-600 font-bold mb-2">{{ __('home.how.eyebrow') }}</h2>
            <p class="text-2xl lg:text-3xl font-extrabold tracking-tight text-gray-900 mb-10">{{ __('home.how.title') }}</p>
            <div class="grid md:grid-cols-3 gap-px bg-gray-900/10 border border-gray-900/10 rounded-lg overflow-hidden">
                <div class="bg-white p-7">
                    <div class="text-acc-500 font-black text-2xl mb-3">01</div>
                    <div class="font-bold text-gray-900 mb-1.5">{{ __('home.how.s1_t') }}</div>
                    <p class="text-[14px] text-gray-500">{{ __('home.how.s1_d') }}</p>
                </div>
                <div class="bg-white p-7">
                    <div class="text-acc-500 font-black text-2xl mb-3">02</div>
                    <div class="font-bold text-gray-900 mb-1.5">{{ __('home.how.s2_t') }}</div>
                    <p class="text-[14px] text-gray-500">{{ __('home.how.s2_d') }}</p>
                </div>
                <div class="bg-white p-7">
                    <div class="text-acc-500 font-black text-2xl mb-3">03</div>
                    <div class="font-bold text-gray-900 mb-1.5">{{ __('home.how.s3_t') }}</div>
                    <p class="text-[14px] text-gray-500">{{ __('home.how.s3_d') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
