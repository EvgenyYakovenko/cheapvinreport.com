@php
    $loginUrl = \App\Support\LocaleRoute::route('login');
    $registerUrl = \App\Support\LocaleRoute::route('register');
@endphp
<x-guest-layout>
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">{{ __('auth.register') }}</h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('auth.already_registered') }}
            <a href="{{ $loginUrl }}" class="font-medium text-primary-600 hover:text-primary-500">
                {{ __('auth.login') }}
            </a>
        </p>
    </div>

    <form method="POST" action="{{ $registerUrl }}" class="mt-8 space-y-6">
        @csrf

        <div class="space-y-5">
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('auth.name')" />
                <div class="mt-2">
                    <x-text-input id="name"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                                  type="text"
                                  name="name"
                                  :value="old('name')"
                                  required
                                  autofocus
                                  autocomplete="name"
                                  placeholder="{{ __('auth.name') }}" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('auth.email')" />
                <div class="mt-2">
                    <x-text-input id="email"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                                  type="email"
                                  name="email"
                                  :value="old('email')"
                                  required
                                  autocomplete="username"
                                  placeholder="{{ __('auth.email') }}" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('auth.password_label')" />
                <div class="mt-2">
                    <x-text-input id="password"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                                  type="password"
                                  name="password"
                                  required
                                  autocomplete="new-password"
                                  placeholder="{{ __('auth.password_label') }}" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('auth.confirm_password')" />
                <div class="mt-2">
                    <x-text-input id="password_confirmation"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                                  type="password"
                                  name="password_confirmation"
                                  required
                                  autocomplete="new-password"
                                  placeholder="{{ __('auth.confirm_password') }}" />
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <!-- hCaptcha -->
        @if(config('hcaptcha.site_key'))
        <div id="hcaptcha-container-register" style="display: none;"></div>
        @endif

        <div>
            <button type="submit"
                    id="register-submit-btn"
                    class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                {{ __('auth.register') }}
            </button>
        </div>
    </form>

    @if(config('hcaptcha.site_key'))
    <script>
        const hcaptchaSiteKeyRegister = @json(config('hcaptcha.site_key', ''));
        let hcaptchaWidgetIdRegister = null;

        const registerCaptchaInitRegister = window.registerHCaptchaInit || function(initFn) {
            window.__hcaptchaInitQueue = window.__hcaptchaInitQueue || [];
            if (typeof initFn === 'function') {
                window.__hcaptchaInitQueue.push(initFn);
            }
        };

        registerCaptchaInitRegister(function() {
            if (hcaptchaWidgetIdRegister !== null) {
                return;
            }

            if (hcaptchaSiteKeyRegister && typeof hcaptcha !== 'undefined') {
                const container = document.getElementById('hcaptcha-container-register');
                if (container) {
                    hcaptchaWidgetIdRegister = hcaptcha.render('hcaptcha-container-register', {
                        sitekey: hcaptchaSiteKeyRegister,
                        size: 'invisible',
                        callback: function(token) {
                            const form = document.querySelector('form[action="{{ $registerUrl }}"]');
                            if (form && form.dataset.submitted !== '1') {
                                form.dataset.submitted = '1';
                                form.submit();
                            }
                        },
                        'error-callback': function(err) {
                            console.error('hCaptcha error:', err);
                            const form = document.querySelector('form[action="{{ $registerUrl }}"]');
                            if (form && form.dataset.submitted !== '1') {
                                form.dataset.submitted = '1';
                                form.submit();
                            }
                        }
                    });
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const registerForm = document.querySelector('form[action="{{ $registerUrl }}"]');
            const submitBtn = document.getElementById('register-submit-btn');
            let submitInProgress = false;

            if (registerForm && submitBtn) {
                registerForm.addEventListener('submit', function(e) {
                    if (submitInProgress) {
                        return;
                    }

                    e.preventDefault();
                    submitInProgress = true;
                    submitBtn.setAttribute('disabled', 'disabled');

                    if (hcaptchaSiteKeyRegister && hcaptchaWidgetIdRegister !== null && typeof hcaptcha !== 'undefined') {
                        try {
                            hcaptcha.execute(hcaptchaWidgetIdRegister);
                        } catch (err) {
                            console.error('hCaptcha execute error:', err);
                            if (registerForm.dataset.submitted !== '1') {
                                registerForm.dataset.submitted = '1';
                                registerForm.submit();
                            }
                        }
                    } else {
                        if (registerForm.dataset.submitted !== '1') {
                            registerForm.dataset.submitted = '1';
                            registerForm.submit();
                        }
                    }
                });
            }
        });
    </script>
    @endif
</x-guest-layout>
