{{-- STAGING: "See what you get" — sample report (public/images/Example.pdf). --}}
<section class="py-14 bg-white border-t border-gray-100">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-8 items-center">
            <div>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">See exactly what you get</h2>
                <p class="text-gray-600 mb-5">Every report is a full vehicle history tied to the VIN. Here's what's inside:</p>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start gap-2"><span class="text-primary-600 mt-1">✓</span> Accident &amp; damage records</li>
                    <li class="flex items-start gap-2"><span class="text-primary-600 mt-1">✓</span> Title brands (salvage, flood, rebuilt)</li>
                    <li class="flex items-start gap-2"><span class="text-primary-600 mt-1">✓</span> Odometer readings &amp; rollback checks</li>
                    <li class="flex items-start gap-2"><span class="text-primary-600 mt-1">✓</span> Ownership history &amp; usage</li>
                    <li class="flex items-start gap-2"><span class="text-primary-600 mt-1">✓</span> Service &amp; maintenance events</li>
                </ul>
                <a href="{{ asset('images/Example.pdf') }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0l-4-4m4 4l4-4M4 20h16"/></svg>
                    View a sample report (PDF)
                </a>
            </div>
            <div class="relative">
                <a href="{{ asset('images/Example.pdf') }}" target="_blank" rel="noopener" class="block group">
                    <div class="bg-[#f8f9fa] rounded-xl border border-gray-200 shadow-sm p-6 group-hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-lg font-bold text-gray-900">Vehicle History Report</div>
                            <span class="text-xs bg-primary-100 text-primary-700 font-semibold px-2 py-1 rounded">SAMPLE</span>
                        </div>
                        <div class="text-sm text-gray-500 mb-4">2018 Lexus ES 350 · VIN 58ABK1GG6JU1****</div>
                        <div class="space-y-2">
                            <div class="h-2.5 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-2.5 bg-gray-200 rounded w-full"></div>
                            <div class="h-2.5 bg-gray-200 rounded w-5/6"></div>
                            <div class="h-2.5 bg-gray-200 rounded w-2/3"></div>
                            <div class="grid grid-cols-3 gap-2 pt-3">
                                <div class="h-14 bg-white border border-gray-200 rounded"></div>
                                <div class="h-14 bg-white border border-gray-200 rounded"></div>
                                <div class="h-14 bg-white border border-gray-200 rounded"></div>
                            </div>
                        </div>
                        <div class="mt-4 text-primary-600 font-semibold text-sm">Open full sample &rarr;</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>
