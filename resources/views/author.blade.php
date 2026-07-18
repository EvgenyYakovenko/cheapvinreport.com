@php
    $authorName = $author['name'] ?? 'Author';
    $authorRole = $author['role'] ?? '';
    $authorBio = $author['bio'] ?? '';
    $authorAvatar = $author['avatar'] ?? null;
    $authorUrl = $author['url'] ?? null;
    $authorSameAs = isset($author['same_as']) && is_array($author['same_as']) ? $author['same_as'] : [];
@endphp

@include('header', [
    'metaTitle' => $metaTitle ?? $authorName,
    'metaDescription' => $metaDescription ?? $authorBio,
    'hreflangUrls' => $hreflangUrls ?? [],
    'xDefaultUrl' => $xDefaultUrl ?? null,
    'seoImage' => $seoImage ?? null,
])

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <article class="bg-white rounded-lg shadow-sm p-8 md:p-12">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-8">Author</h1>

                <div class="flex items-start gap-6">
                    @if(!empty($authorAvatar))
                        <img src="{{ $authorAvatar }}" alt="{{ $authorName }}" class="h-20 w-20 rounded-full object-cover" loading="lazy">
                    @else
                        <div class="h-20 w-20 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-2xl">
                            {{ strtoupper(substr($authorName, 0, 1)) }}
                        </div>
                    @endif

                    <div class="min-w-0">
                        <h2 class="text-2xl font-semibold text-gray-900">{{ $authorName }}</h2>
                        @if(!empty($authorRole))
                            <p class="text-gray-600 mt-1">{{ $authorRole }}</p>
                        @endif
                        @if(!empty($authorBio))
                            <p class="text-gray-700 mt-4 leading-relaxed">{{ $authorBio }}</p>
                        @endif

                        @if(!empty($authorUrl))
                            <p class="mt-4">
                                <a href="{{ $authorUrl }}" target="_blank" rel="noopener nofollow" class="text-primary hover:underline">
                                    {{ $authorUrl }}
                                </a>
                            </p>
                        @endif

                        @if(!empty($authorSameAs))
                            <div class="mt-4">
                                <p class="text-sm font-semibold text-gray-900 mb-2">Profiles</p>
                                <ul class="space-y-1">
                                    @foreach($authorSameAs as $profileUrl)
                                        <li>
                                            <a href="{{ $profileUrl }}" target="_blank" rel="noopener nofollow" class="text-sm text-primary hover:underline break-all">
                                                {{ $profileUrl }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>

@include('footer')
