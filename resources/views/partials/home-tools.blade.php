{{-- STAGING: free tools teaser (Demo 1). i18n via lang/home. --}}
@php
    use App\Http\Controllers\ToolController;
    $homeTools = array_slice(ToolController::TOOLS, 0, 6, true);
    $toolsHub  = \App\Support\LocaleRoute::route('tools.index');
@endphp
<section class="py-16 bg-gray-50 border-t border-gray-900/10">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <p class="text-2xl lg:text-3xl font-extrabold tracking-tight text-gray-900 mb-2">{{ __('home.tools.title') }}</p>
            <p class="text-gray-500 mb-8">{{ __('home.tools.sub') }}</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-px bg-gray-900/10 border border-gray-900/10 rounded-lg overflow-hidden">
                @foreach($homeTools as $slug => $tool)
                    <a href="{{ \App\Support\LocaleRoute::route('tools.show', ['tool' => $slug]) }}"
                       class="block bg-white hover:bg-gray-50 transition p-5 group">
                        <div class="font-semibold text-gray-900 group-hover:text-acc-600 transition">{{ __('tools.items.'.$slug.'.label') }}</div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">
                <a href="{{ $toolsHub }}" class="inline-flex items-center gap-1 text-gray-900 font-bold hover:text-acc-600 transition">{{ __('home.tools.all') }} &rarr;</a>
            </div>
        </div>
    </div>
</section>
