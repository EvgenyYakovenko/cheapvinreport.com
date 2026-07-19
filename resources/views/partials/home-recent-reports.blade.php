{{-- STAGING: recent purchased reports (social proof). $recentReports from HomeController. --}}
@php $rows = $recentReports ?? []; @endphp
@if(!empty($rows))
<section class="py-14 bg-[#f8f9fa] border-t border-gray-100">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-8">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Recently checked vehicles</h2>
                <p class="text-gray-600">A live look at the latest reports pulled by our customers.</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-left">
                        <tr>
                            <th class="px-4 py-3 font-semibold">VIN</th>
                            <th class="px-4 py-3 font-semibold">Vehicle</th>
                            <th class="px-4 py-3 font-semibold text-center">Records</th>
                            <th class="px-4 py-3 font-semibold text-right">Report</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($rows as $r)
                            <tr>
                                <td class="px-4 py-3 font-mono text-gray-700 whitespace-nowrap">{{ $r['vin'] }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $r['vehicle'] ?: '—' }}</td>
                                <td class="px-4 py-3 text-center"><span class="inline-block bg-primary-50 text-primary-700 font-semibold rounded px-2 py-0.5">{{ $r['records'] }}</span></td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $r['type'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-center text-xs text-gray-400 mt-3">VINs are partially masked to protect buyer privacy.</p>
        </div>
    </div>
</section>
@endif
