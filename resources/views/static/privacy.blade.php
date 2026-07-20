{{-- STAGING: legal page (privacy). Meta from StaticPageController. i18n lang/legal.privacy. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); $g='legal.privacy'; @endphp
<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">{{ __('nav.home') }}</a>
                    <span class="mx-2">/</span><span class="text-gray-700">{{ __($g.'.crumb') }}</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-gray-900 mb-2">{{ __($g.'.h1') }}</h1>
                <p class="text-sm text-gray-400 mb-8">{{ __('legal.updated') }} {{ now()->format('F Y') }}</p>
                <div class="bg-white rounded-lg border border-gray-900/10 p-6 lg:p-8 space-y-6 text-gray-600 leading-relaxed">
                    @foreach(__($g.'.sections') as $s)
                        <div><h2 class="text-lg font-bold text-gray-900 mb-1">{{ $s['t'] }}</h2><p>{{ $s['d'] }}</p></div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</main>
@include('footer')
