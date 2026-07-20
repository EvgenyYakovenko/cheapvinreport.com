{{-- STAGING: /compare hub. i18n via lang/compare. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); @endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a><span class="mx-2">/</span><span class="text-gray-700">{{ __('compare.hub.crumb') }}</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">{{ __('compare.hub.h1') }}</h1>
                <p class="text-lg text-gray-700 max-w-3xl">{{ __('compare.hub.lead') }}</p>
            </div>
        </div>
    </section>
    <section class="pb-14">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($items as $slug => $item)
                    <a href="{{ \App\Support\LocaleRoute::route('compare.show', ['competitor' => $slug]) }}"
                       class="block bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-primary-300 transition p-5">
                        <h2 class="text-lg font-bold text-gray-900 mb-1">{{ __('compare.show.vs') }} {{ $item['name'] }}</h2>
                        <p class="text-gray-500 text-sm">{{ __('compare.hub.card', ['name' => $item['name'], 'price' => $item['price']]) }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</main>

@include('footer')
