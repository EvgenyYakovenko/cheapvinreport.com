{{-- STAGING: comparison page template. Data from ComparisonController + i18n lang/compare. --}}
@include('header')
@php
    $homeUrl    = \App\Support\LocaleRoute::route('index');
    $compareUrl = \App\Support\LocaleRoute::route('compare.index');
    $rows = [
        [__('compare.rows.price'), '$3.00', $item['price']],
        [__('compare.rows.ato'), __('compare.val.included'), __('compare.val.included')],
        [__('compare.rows.delivery'), __('compare.val.yes'), __('compare.val.yes')],
        [__('compare.rows.coverage'), __('compare.val.yes'), __('compare.val.yes')],
        [__('compare.rows.model'), __('compare.val.pay_per_report'), __("compare.items.$slug.model")],
        [__('compare.rows.moneyback'), __('compare.val.yes'), '—'],
    ];
@endphp

<main class="bg-gray-50">
    <section class="py-12 lg:py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                    <a href="{{ $homeUrl }}" class="hover:text-primary-600">Home</a><span class="mx-2">/</span>
                    <a href="{{ $compareUrl }}" class="hover:text-primary-600">{{ __('compare.hub.crumb') }}</a><span class="mx-2">/</span>
                    <span class="text-gray-700">{{ $item['name'] }}</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">{{ $our }} <span class="text-gray-400">{{ __('compare.show.vs') }}</span> {{ $item['name'] }}</h1>
                <p class="text-lg text-gray-700 max-w-3xl">{{ __("compare.items.$slug.intro") }}</p>
            </div>
        </div>
    </section>

    <section class="pb-4">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto rounded-2xl border border-gray-200 overflow-hidden shadow-sm bg-white">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-sm">
                        <tr>
                            <th class="px-5 py-3 font-semibold">{{ __('compare.show.feature') }}</th>
                            <th class="px-5 py-3 font-semibold text-center bg-primary-50 text-primary-700">{{ $our }}</th>
                            <th class="px-5 py-3 font-semibold text-center">{{ $item['name'] }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($rows as $r)
                            <tr>
                                <td class="px-5 py-3 text-gray-700">{!! $r[0] !!}</td>
                                <td class="px-5 py-3 text-center font-semibold text-primary-700 bg-primary-50/60">{!! $r[1] !!}</td>
                                <td class="px-5 py-3 text-center text-gray-600">{!! $r[2] !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="max-w-4xl mx-auto text-center text-xs text-gray-400 mt-3">{{ __('compare.show.note') }}</p>
        </div>
    </section>

    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('compare.show.why_title', ['our' => $our]) }}</h2>
                    <ul class="space-y-2 text-gray-700 text-sm">
                        <li class="flex gap-2"><span class="text-acc-500">✓</span> {{ __('compare.show.why1') }}</li>
                        <li class="flex gap-2"><span class="text-acc-500">✓</span> {!! __('compare.show.why2') !!}</li>
                        <li class="flex gap-2"><span class="text-acc-500">✓</span> {{ __('compare.show.why3') }}</li>
                        <li class="flex gap-2"><span class="text-acc-500">✓</span> {{ __('compare.show.why4') }}</li>
                    </ul>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">{{ __('compare.show.when_title', ['name' => $item['name']]) }}</h2>
                    <p class="text-gray-600 text-sm">{{ __("compare.items.$slug.fair") }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-primary-50 border border-primary-100 rounded-xl p-6 lg:p-8 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('compare.show.cta_title') }}</h2>
                <p class="text-gray-600 mb-5 max-w-2xl mx-auto">{{ __('compare.show.cta_sub') }}</p>
                <a href="{{ $homeUrl }}#vin-check" class="inline-block px-6 py-3.5 text-lg bg-gray-900 hover:bg-acc-500 text-white font-bold rounded-md transition">{{ __('compare.show.cta_btn') }} &rarr;</a>
            </div>
        </div>
    </section>
</main>

@include('footer')
