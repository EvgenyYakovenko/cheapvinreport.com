{{-- STAGING: recent purchased reports (Demo 1). i18n via lang/home. --}}
@php $rows = $recentReports ?? []; @endphp
@if(!empty($rows))
<section class="py-16 bg-white border-t border-gray-900/10">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <p class="text-2xl lg:text-3xl font-extrabold tracking-tight text-gray-900 mb-2">{{ __('home.recent.title') }}</p>
            <p class="text-gray-500 mb-8">{{ __('home.recent.sub') }}</p>
            <div class="border border-gray-900/10 rounded-lg overflow-hidden bg-white">
                <table class="w-full text-sm">
                    <thead class="bg-gray-900 text-gray-300 text-left">
                        <tr>
                            <th class="px-4 py-3 font-semibold">{{ __('home.recent.h_vin') }}</th>
                            <th class="px-4 py-3 font-semibold">{{ __('home.recent.h_vehicle') }}</th>
                            <th class="px-4 py-3 font-semibold text-center">{{ __('home.recent.h_records') }}</th>
                            <th class="px-4 py-3 font-semibold text-right">{{ __('home.recent.h_report') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-900/10">
                        @foreach($rows as $r)
                            <tr>
                                <td class="px-4 py-3 font-mono text-gray-700 whitespace-nowrap">{{ $r['vin'] }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $r['vehicle'] ?: '—' }}</td>
                                <td class="px-4 py-3 text-center"><span class="inline-block bg-gray-100 text-gray-900 font-semibold rounded px-2 py-0.5">{{ $r['records'] }}</span></td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $r['type'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-400 mt-3">{{ __('home.recent.note') }}</p>
        </div>
    </div>
</section>
@endif
