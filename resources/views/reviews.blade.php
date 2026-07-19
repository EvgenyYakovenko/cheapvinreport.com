{{-- STAGING: /reviews page. Meta from ReviewController. --}}
@include('header')

@php
    $homeUrl = \App\Support\LocaleRoute::route('index');
    $etsy    = config('reviews.etsy', []);
@endphp

<main class="bg-[#f8f9fa]">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700">Reviews</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">Customer Reviews</h1>
                <p class="text-lg text-gray-700 mb-6 max-w-2xl mx-auto">
                    We sold vehicle history reports on Etsy under the shop
                    <strong>Cheapvinreport</strong> — {{ $etsy['sales'] ?? '200+' }} sales with a
                    {{ $etsy['rating'] ?? '5.0' }}-star rating. Here's what buyers said.
                </p>
                <a href="{{ $etsy['url'] ?? '#' }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-5 py-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition">
                    <span class="text-yellow-400 text-lg leading-none">★★★★★</span>
                    <span class="font-bold text-gray-900">{{ $etsy['rating'] ?? '5.0' }}</span>
                    <span class="text-gray-600 text-sm">· {{ $etsy['count'] ?? '35' }} verified reviews on Etsy &rarr;</span>
                </a>
            </div>
        </div>
    </section>

    @include('partials.reviews', ['limit' => null, 'heading' => 'All reviews'])

    <section class="py-12 bg-[#f8f9fa]">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto bg-primary-50 border border-primary-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Ready to check a car?</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">
                    Get a full vehicle history report from $3.00 — accidents, title brands, odometer and
                    ownership tied to the VIN.
                </p>
                <a href="{{ $homeUrl }}#vin-check"
                   class="inline-block px-6 py-3 text-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                    Check a VIN now &rarr;
                </a>
            </div>
        </div>
    </section>
</main>

@include('footer')
