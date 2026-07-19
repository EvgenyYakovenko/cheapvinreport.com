{{-- STAGING: free tools teaser on the homepage (internal links + value). --}}
@php
    use App\Http\Controllers\ToolController;
    $homeTools = array_slice(ToolController::TOOLS, 0, 6, true);
    $toolsHub  = \App\Support\LocaleRoute::route('tools.index');
@endphp
<section class="py-14 bg-white border-t border-gray-100">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-8">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Free VIN &amp; car tools</h2>
                <p class="text-gray-600">Handy tools for car buyers — free, no sign-up.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($homeTools as $slug => $tool)
                    <a href="{{ \App\Support\LocaleRoute::route('tools.show', ['tool' => $slug]) }}"
                       class="block bg-[#f8f9fa] rounded-xl border border-gray-200 hover:border-primary-300 hover:shadow-sm transition p-4">
                        <div class="font-semibold text-gray-900">{{ $tool['label'] ?? $tool['title'] }}</div>
                    </a>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ $toolsHub }}" class="text-primary-600 font-semibold hover:text-primary-700">See all free tools &rarr;</a>
            </div>
        </div>
    </div>
</section>
