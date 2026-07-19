{{-- STAGING: Money-Back Guarantee page. Meta from StaticPageController. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); @endphp

<main class="bg-[#f8f9fa]">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a>
                    <span class="mx-2">/</span><span class="text-gray-700">Money-Back Guarantee</span>
                </nav>

                <div class="flex items-center gap-3 mb-4">
                    <span class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Money-Back Guarantee</h1>
                </div>
                <p class="text-lg text-gray-700 mb-8">Your purchase is risk-free. If anything goes wrong with your report, we'll refund you — it's that simple.</p>

                <div class="space-y-4">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">Report didn't arrive within 24 hours?</h2>
                        <p class="text-gray-600">Reports are normally delivered within minutes. In the rare case yours hasn't arrived within <strong>24 hours</strong> of your order, we'll refund you in full.</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">Something wrong with the report?</h2>
                        <p class="text-gray-600">If the report won't open, is empty, or something else isn't right — just write to us. We'll fix it or refund you, no hassle.</p>
                    </div>
                </div>

                <div class="mt-8 bg-primary-50 border border-primary-100 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">How to request a refund</h2>
                    <p class="text-gray-600 mb-3">Email us with your order details (the email you used and the VIN) and a short note about the problem. We'll get back to you quickly.</p>
                    <a href="mailto:support@cheapvinreport.email" class="inline-block font-semibold text-primary-600 hover:text-primary-700">support@cheapvinreport.email</a>
                </div>

                <div class="mt-8 text-center">
                    <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">Check a VIN now &rarr;</a>
                </div>
            </div>
        </div>
    </section>
</main>

@include('footer')
