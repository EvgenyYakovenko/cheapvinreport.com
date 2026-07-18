@php
    $passwordConfirmUrl = \App\Support\LocaleRoute::route('password.confirm');
@endphp
<x-guest-layout>
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">{{ __('auth.confirm_password') }}</h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('auth.confirm_password_message') }}
        </p>
    </div>

    <form method="POST" action="{{ $passwordConfirmUrl }}" class="mt-8 space-y-6">
        @csrf

        <div>
            <x-input-label for="password" :value="__('auth.password_label')" />
            <div class="mt-2">
                <x-text-input id="password" 
                              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                              type="password"
                              name="password"
                              required 
                              autocomplete="current-password"
                              placeholder="{{ __('auth.password_label') }}" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <button type="submit" 
                    class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                {{ __('auth.confirm') }}
            </button>
        </div>
    </form>
</x-guest-layout>
