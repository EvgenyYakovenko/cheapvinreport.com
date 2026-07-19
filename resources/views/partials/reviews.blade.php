{{-- STAGING: reusable reviews block. Params:
       $limit  (int|null)  — how many cards to show (null = all)
       $heading (string|null)
     Data from config/reviews.php. Always links to the Etsy source for verification. --}}
@php
    $rev     = config('reviews');
    $items   = $rev['items'] ?? [];
    $etsy    = $rev['etsy'] ?? [];
    $limit   = $limit ?? null;
    $shown   = $limit ? array_slice($items, 0, $limit) : $items;
    $heading = $heading ?? 'What our customers say';
    $reviewsUrl = \App\Support\LocaleRoute::route('reviews');
@endphp
<section class="py-14 bg-white border-t border-gray-100">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-8">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ $heading }}</h2>
                <a href="{{ $etsy['url'] ?? '#' }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 text-sm hover:opacity-80 transition">
                    <span class="text-yellow-400 text-lg leading-none">★★★★★</span>
                    <span class="font-bold text-gray-900">{{ $etsy['rating'] ?? '5.0' }}</span>
                    <span class="text-gray-600">· {{ $etsy['count'] ?? '' }} reviews verified on Etsy</span>
                </a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($shown as $r)
                    <figure class="bg-[#f8f9fa] rounded-xl border border-gray-200 p-5">
                        <div class="text-yellow-400 mb-2 leading-none">★★★★★</div>
                        <blockquote class="text-gray-700 mb-3">&ldquo;{{ $r['text'] }}&rdquo;</blockquote>
                        <figcaption class="text-sm text-gray-500 font-medium">{{ $r['name'] }} · {{ $r['date'] }}</figcaption>
                    </figure>
                @endforeach
            </div>

            @if($limit && count($items) > $limit)
                <div class="text-center mt-8">
                    <a href="{{ $reviewsUrl }}"
                       class="inline-block px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                        Read all reviews &rarr;
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
