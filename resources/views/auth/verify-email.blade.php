@php
    $verificationSendUrl = \App\Support\LocaleRoute::route('verification.send');
    $logoutUrl = \App\Support\LocaleRoute::route('logout');
@endphp
<x-guest-layout>
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">{{ __('auth.verify_email') }}</h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('auth.verify_email_message') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ __('auth.verification_link_sent') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-8 space-y-6">
        <form method="POST" action="{{ $verificationSendUrl }}">
            @csrf
            <button type="submit" 
                    class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                {{ __('auth.resend_verification_email') }}
            </button>
        </form>

        <form method="POST" action="{{ $logoutUrl }}">
            @csrf
            <button type="submit" 
                    class="flex w-full justify-center rounded-md bg-white px-3 py-1.5 text-sm font-semibold leading-6 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                {{ __('auth.log_out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
