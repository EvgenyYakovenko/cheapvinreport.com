@php
    $loginUrl = \App\Support\LocaleRoute::route('login');
    $registerUrl = \App\Support\LocaleRoute::route('register');
    $forgotUrl = \App\Support\LocaleRoute::route('password.request');
@endphp
<x-guest-layout>
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">{{ __('auth.login') }}</h2>
        <p class="mt-2 text-sm text-gray-600">
            @if (Route::has('register'))
                {{ __('auth.dont_have_account') }}
                <a href="{{ $registerUrl }}" class="font-medium text-primary-600 hover:text-primary-500">
                    {{ __('auth.register') }}
                </a>
            @endif
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ $loginUrl }}" class="mt-8 space-y-6">
        @csrf

        <div class="space-y-5">
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
                                  autofocus
                                  autocomplete="username"
                                  placeholder="{{ __('auth.email') }}" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" :value="__('auth.password_label')" />
                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ $forgotUrl }}" class="font-semibold text-primary-600 hover:text-primary-500">
                                {{ __('auth.forgot_password') }}
                            </a>
                        </div>
                    @endif
                </div>
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
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me"
                   name="remember"
                   type="checkbox"
                   class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600">
            <label for="remember_me" class="ml-3 block text-sm leading-6 text-gray-900">
                {{ __('auth.remember_me') }}
            </label>
        </div>

        <!-- hCaptcha -->
        @if(config('hcaptcha.site_key'))
        <div id="hcaptcha-container-login" style="display: none;"></div>
        @endif

        <div>
            <button type="submit"
                    id="login-submit-btn"
                    class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                {{ __('auth.login') }}
            </button>
        </div>
    </form>

    @if(config('hcaptcha.site_key'))
    <script>
        const hcaptchaSiteKeyLogin = @json(config('hcaptcha.site_key', ''));
        let hcaptchaWidgetIdLogin = null;

        const registerCaptchaInitLogin = window.registerHCaptchaInit || function(initFn) {
            window.__hcaptchaInitQueue = window.__hcaptchaInitQueue || [];
            if (typeof initFn === 'function') {
                window.__hcaptchaInitQueue.push(initFn);
            }
        };

        registerCaptchaInitLogin(function() {
            if (hcaptchaWidgetIdLogin !== null) {
                return;
            }

            if (hcaptchaSiteKeyLogin && typeof hcaptcha !== 'undefined') {
                const container = document.getElementById('hcaptcha-container-login');
                if (container) {
                    hcaptchaWidgetIdLogin = hcaptcha.render('hcaptcha-container-login', {
                        sitekey: hcaptchaSiteKeyLogin,
                        size: 'invisible',
                        callback: function(token) {
                            const form = document.querySelector('form[action="{{ $loginUrl }}"]');
                            if (form && form.dataset.submitted !== '1') {
                                form.dataset.submitted = '1';
                                form.submit();
                            }
                        },
                        'error-callback': function(err) {
                            console.error('hCaptcha error:', err);
                            const form = document.querySelector('form[action="{{ $loginUrl }}"]');
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
            const loginForm = document.querySelector('form[action="{{ $loginUrl }}"]');
            const submitBtn = document.getElementById('login-submit-btn');
            let submitInProgress = false;

            if (loginForm && submitBtn) {
                loginForm.addEventListener('submit', function(e) {
                    if (submitInProgress) {
                        return;
                    }

                    e.preventDefault();
                    submitInProgress = true;
                    submitBtn.setAttribute('disabled', 'disabled');

                    if (hcaptchaSiteKeyLogin && hcaptchaWidgetIdLogin !== null && typeof hcaptcha !== 'undefined') {
                        try {
                            hcaptcha.execute(hcaptchaWidgetIdLogin);
                        } catch (err) {
                            console.error('hCaptcha execute error:', err);
                            if (loginForm.dataset.submitted !== '1') {
                                loginForm.dataset.submitted = '1';
                                loginForm.submit();
                            }
                        }
                    } else {
                        if (loginForm.dataset.submitted !== '1') {
                            loginForm.dataset.submitted = '1';
                            loginForm.submit();
                        }
                    }
                });
            }
        });
    </script>
    @endif
</x-guest-layout>
