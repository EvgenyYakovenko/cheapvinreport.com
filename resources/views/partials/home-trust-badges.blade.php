{{-- STAGING: hero trust block — Variant 3 (icon rows). Our font. + 24h counter. --}}
@php $p24 = $purchases24h ?? null; $mbUrl = \App\Support\LocaleRoute::route('money-back'); @endphp
<div class="mt-5">
    <div class="grid grid-cols-2 gap-x-6 gap-y-3 max-w-lg">
        <div class="flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </span>
            <div><div class="text-sm font-bold text-gray-900 leading-tight">SSL secured</div><div class="text-xs text-gray-400">Encrypted checkout</div></div>
        </div>
        <div class="flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </span>
            <div><div class="text-sm font-bold text-gray-900 leading-tight">~2 min delivery</div><div class="text-xs text-gray-400">Straight to you</div></div>
        </div>
        <a href="{{ $mbUrl }}" class="flex items-center gap-2.5 group">
            <span class="w-9 h-9 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <div><div class="text-sm font-bold text-gray-900 leading-tight group-hover:text-primary-600 transition">100% money-back</div><div class="text-xs text-gray-400">No risk</div></div>
        </a>
        <div class="flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.05 2.9c.3-.9 1.6-.9 1.9 0l1.3 4a1 1 0 00.95.7h4.2c.97 0 1.37 1.24.59 1.8l-3.37 2.45a1 1 0 00-.36 1.12l1.29 4c.3.9-.76 1.68-1.54 1.12l-3.37-2.45a1 1 0 00-1.17 0l-3.37 2.45c-.78.56-1.84-.22-1.54-1.12l1.29-4a1 1 0 00-.36-1.12L2.07 9.4c-.78-.56-.38-1.8.59-1.8h4.2a1 1 0 00.95-.7l1.3-4z"/></svg>
            </span>
            <div><div class="text-sm font-bold text-gray-900 leading-tight">5.0 on Etsy</div><div class="text-xs text-gray-400">32 reviews</div></div>
        </div>
    </div>
    @if($p24)
        <div class="mt-4 inline-flex items-center gap-2 text-xs text-gray-600 bg-green-50 border border-green-100 rounded-full px-3 py-1">
            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span>
            <strong class="text-gray-900">{{ number_format($p24) }}</strong> reports purchased in the last 24 hours
        </div>
    @endif
</div>
