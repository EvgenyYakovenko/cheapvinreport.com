@include('header', [
    'metaTitle' => $metaTitle ?? null,
    'metaDescription' => $metaDescription ?? null,
    'hreflangUrls' => $hreflangUrls ?? [],
    'xDefaultUrl' => $xDefaultUrl ?? null,
])

<!-- Blog Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('index.blog.title') }}</h1>
            <p class="text-xl text-gray-600">{{ __('index.blog.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($posts as $post)
                <a href="{{ \App\Support\LocaleRoute::route('post', ['slug' => $post->slug]) }}" class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col h-full border border-gray-100 hover:border-primary-300 group cursor-pointer">
                    <div class="p-6 flex flex-col flex-grow">
                        <h2 class="text-xl font-bold mb-3 text-gray-900 leading-tight group-hover:text-primary transition-colors duration-200">
                            {{ $post->title }}
                        </h2>
                        <p class="text-gray-600 text-sm leading-relaxed flex-grow mb-4 line-clamp-3">
                            {{ Str::limit(strip_tags($post->content), 120) }}
                        </p>
                        <div class="flex justify-between items-center mt-auto pt-4 border-t border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">
                                {{ $post->created_at->format('d.m.Y') }}
                            </span>
                            <span class="px-4 py-2 bg-primary group-hover:bg-green-600 text-white text-sm font-medium rounded-md transition-colors duration-200 shadow-sm group-hover:shadow">
                                {{ __('index.blog.read_more') }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full">
                    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-center">
                        {{ __('index.blog.no_posts') }}
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
            <div class="mt-12">
                <nav aria-label="{{ __('index.blog.pagination') }}">
                    <ul class="flex justify-center space-x-2">
                        @if($posts->onFirstPage())
                            <li>
                                <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">{{ __('index.blog.previous') }}</span>
                            </li>
                        @else
                            <li>
                                <a class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100 transition" href="{{ $posts->previousPageUrl() }}">{{ __('index.blog.previous') }}</a>
                            </li>
                        @endif

                        @if($posts->hasMorePages())
                            <li>
                                <a class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100 transition" href="{{ $posts->nextPageUrl() }}">{{ __('index.blog.next') }}</a>
                            </li>
                        @else
                            <li>
                                <span class="px-4 py-2 bg-gray-200 text-gray-500 rounded cursor-not-allowed">{{ __('index.blog.next') }}</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        @endif
    </div>
</section>

@include('footer')
