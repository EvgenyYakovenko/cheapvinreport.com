<!-- Footer -->
@php
    $termsUrl = \App\Support\LocaleRoute::route('page', ['slug' => 'terms-and-conditions']);
    $privacyUrl = \App\Support\LocaleRoute::route('page', ['slug' => 'privacy-policy']);
    $refundUrl = \App\Support\LocaleRoute::route('page', ['slug' => 'refund-policy']);
    $blogUrl = \App\Support\LocaleRoute::route('blog');
    $loginUrl = \App\Support\LocaleRoute::route('login');
@endphp
<footer class="bg-primary-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <h2 class="text-lg font-bold mb-4">{{ __('index.footer.information') }}</h2>
                <ul class="space-y-2">
                    <li><a href="{{ $termsUrl }}" class="text-primary-200 hover:text-white transition">{{ __('index.footer.terms') }}</a></li>
                    <li><a href="{{ $privacyUrl }}" class="text-primary-200 hover:text-white transition">{{ __('index.footer.privacy') }}</a></li>
                    <li><a href="{{ $refundUrl }}" class="text-primary-200 hover:text-white transition">{{ __('index.footer.refund_policy') }}</a></li>
{{--                    <li><a href="{{ $blogUrl }}" class="text-primary-200 hover:text-white transition">{{ __('index.blog.title') }}</a></li>--}}
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <li><a href="{{ route('dashboard') }}" class="text-primary-200 hover:text-white transition">{{ __('index.nav.account') }}</a></li>
                        @else
                            <li><a href="{{ route('panel') }}" class="text-primary-200 hover:text-white transition">{{ __('index.nav.account') }}</a></li>
                        @endif
                    @else
                        <li><a href="{{ $loginUrl }}" class="text-primary-200 hover:text-white transition">{{ __('index.nav.login') }}</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h2 class="text-lg font-bold mb-4">{{ __('index.footer.contacts') }}</h2>
                <ul class="space-y-2">
                    <li>
                        <a href="mailto:support@cheapvinreport.email" class="text-primary-200 hover:text-white transition flex items-center">
                            <span>support@cheapvinreport.email</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mt-10 border-t border-primary-800 pt-6 pb-6 text-sm text-primary-200">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    {{ __('index.footer.disclaimer', ['year' => now()->year]) }}
                </div>
                <div class="flex flex-wrap items-center gap-3 md:justify-end">
                    <img src="{{ asset('images/payment/visa.png') }}" alt="Visa" class="h-10 w-auto rounded bg-white p-1.5" loading="lazy">
                    <img src="{{ asset('images/payment/mastercard.png') }}" alt="Mastercard" class="h-10 w-auto rounded bg-white p-1.5" loading="lazy">
                    <img src="{{ asset('images/payment/prostir.png') }}" alt="Prostir" class="h-10 w-auto rounded bg-white p-1.5" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
