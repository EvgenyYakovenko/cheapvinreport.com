{{-- STAGING: /vin-check hub. Lists all VinCheckController::CHECKS. --}}
@include('header')
@php $homeUrl=\App\Support\LocaleRoute::route('index'); @endphp

<main class="bg-[#f8f9fa]">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a><span class="mx-2">/</span><span class="text-gray-700">VIN Check Services</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 text-gray-900">VIN Check Services</h1>
                <p class="text-lg text-gray-700 max-w-3xl">Check a used car by VIN — accident, title, odometer, owner, service, auction and theft records, all from the full Carfax &amp; AutoCheck report.</p>
            </div>
        </div>
    </section>
    <section class="pb-14">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($checks as $slug => $c)
                    <a href="{{ \App\Support\LocaleRoute::route('vincheck.show', ['check' => $slug]) }}"
                       class="block bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-primary-300 transition p-5">
                        <h2 class="text-lg font-bold text-gray-900 mb-1">{{ $c['label'] }} Check</h2>
                        <p class="text-gray-500 text-sm">{{ ucfirst($c['shows']) }}.</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</main>

@include('footer')
