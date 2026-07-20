{{-- STAGING: hero quote — a real Etsy review, styled after the reference (serif,
     accent left rule, uppercase attribution). Inline styles so no rebuild is needed. --}}
@php
    $items = config('reviews.items', []);
    $q = $items[0] ?? ['name' => 'Etsy buyer', 'text' => 'Great value, highly recommended.'];
    $etsy = config('reviews.etsy.url', '#');
@endphp
<figure style="border-left: 3px solid #a0522d; padding-left: 2rem; max-width: 27rem;">
    <div style="font-family: Georgia, 'Times New Roman', serif; font-size: 4.5rem; line-height: 1; color: #d1d5db; margin-bottom: 0.25rem;">&ldquo;</div>
    <blockquote style="font-family: Georgia, 'Times New Roman', serif; font-size: 1.75rem; line-height: 1.4; color: #374151; margin: 0;">{{ $q['text'] }}</blockquote>
    <figcaption style="margin-top: 1.6rem; font-size: 0.72rem; letter-spacing: 0.18em; text-transform: uppercase; color: #9ca3af; font-weight: 600;">
        {{ $q['name'] }} &nbsp;&middot;&nbsp; <a href="{{ $etsy }}" target="_blank" rel="noopener" style="color:#9ca3af; text-decoration: none;">Verified buyer on Etsy</a>
    </figcaption>
</figure>
