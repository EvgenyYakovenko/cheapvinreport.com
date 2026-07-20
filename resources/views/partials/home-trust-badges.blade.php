{{-- STAGING: hero trust row — Demo 1 (green-dot inline) + 24h counter (mobile). i18n via lang/home. --}}
@php $p24 = $purchases24h ?? null; $mbUrl = \App\Support\LocaleRoute::route('money-back'); @endphp
<div class="mt-1">
    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[13px] font-medium text-gray-600">
        <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ __('home.badges.ssl') }}</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ __('home.badges.delivery') }}</span>
        <a href="{{ $mbUrl }}" class="inline-flex items-center gap-1.5 hover:text-gray-900 transition"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ __('home.badges.moneyback') }}</a>
        <span class="inline-flex items-center gap-1.5"><span class="text-acc-500">&#9733;</span><span class="text-gray-900 font-semibold">{{ __('home.badges.etsy') }}</span></span>
    </div>
    @if($p24)
        <div class="mt-4 lg:hidden inline-flex items-center gap-2 text-xs text-gray-600 bg-green-50 border border-green-100 rounded-full px-3 py-1">
            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span>
            <strong class="text-gray-900">{{ number_format($p24) }}</strong> {{ __('home.badges.counter') }}
        </div>
    @endif
</div>
