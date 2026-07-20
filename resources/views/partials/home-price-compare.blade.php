{{-- STAGING: price comparison (Demo 1). i18n via lang/home. Prices are approximate single-report list prices. --}}
<section class="py-16 bg-gray-50 border-t border-gray-900/10">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            <p class="text-2xl lg:text-3xl font-extrabold tracking-tight text-gray-900 mb-2">{{ __('home.compare.title') }}</p>
            <p class="text-gray-500 mb-8">{{ __('home.compare.sub') }}</p>
            <div class="border border-gray-900/10 rounded-lg overflow-hidden bg-white">
                <table class="w-full text-[15px]">
                    <tbody class="divide-y divide-gray-900/10">
                        <tr class="bg-gray-900 text-white">
                            <td class="px-6 py-4 font-bold">cheapvinreport.com</td>
                            <td class="px-6 py-4 text-right font-extrabold text-acc-400 text-lg">$3.00</td>
                        </tr>
                        <tr><td class="px-6 py-4 text-gray-700">CarfaxDeals</td><td class="px-6 py-4 text-right font-semibold text-gray-500">$9.99</td></tr>
                        <tr><td class="px-6 py-4 text-gray-700">VINAudit</td><td class="px-6 py-4 text-right font-semibold text-gray-500">$9.99</td></tr>
                        <tr><td class="px-6 py-4 text-gray-700">EpicVIN</td><td class="px-6 py-4 text-right font-semibold text-gray-500">$19.99</td></tr>
                        <tr><td class="px-6 py-4 text-gray-700">AutoCheck</td><td class="px-6 py-4 text-right font-semibold text-gray-500">$24.99</td></tr>
                        <tr><td class="px-6 py-4 text-gray-700">Carfax</td><td class="px-6 py-4 text-right font-semibold text-gray-500">$44.99</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-400 mt-3">{{ __('home.compare.note') }}</p>
        </div>
    </div>
</section>
