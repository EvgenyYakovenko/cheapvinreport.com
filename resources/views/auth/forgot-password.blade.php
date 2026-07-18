@php
    $passwordEmailUrl = \App\Support\LocaleRoute::route('password.email');
@endphp
<x-guest-layout>
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">{{ __('auth.forgot_password_title') }}</h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('auth.forgot_password_message') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ $passwordEmailUrl }}" class="mt-8 space-y-6">
        @csrf

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

        <!-- hCaptcha -->
        @if(config('hcaptcha.site_key'))
        <div id="hcaptcha-container-forgot" style="display: none;"></div>
        @endif

        <div>
            <button type="submit"
                    id="forgot-submit-btn"
                    class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                {{ __('auth.email_password_reset_link') }}
            </button>
        </div>
    </form>

    @if(config('hcaptcha.site_key'))
    <script>
        const hcaptchaSiteKeyForgot = @json(config('hcaptcha.site_key', ''));
        let hcaptchaWidgetIdForgot = null;

        const registerCaptchaInitForgot = window.registerHCaptchaInit || function(initFn) {
            window.__hcaptchaInitQueue = window.__hcaptchaInitQueue || [];
            if (typeof initFn === 'function') {
                window.__hcaptchaInitQueue.push(initFn);
            }
        };

        registerCaptchaInitForgot(function() {
            if (hcaptchaWidgetIdForgot !== null) {
                return;
            }

            if (hcaptchaSiteKeyForgot && typeof hcaptcha !== 'undefined') {
                const container = document.getElementById('hcaptcha-container-forgot');
                if (container) {
                    hcaptchaWidgetIdForgot = hcaptcha.render('hcaptcha-container-forgot', {
                        sitekey: hcaptchaSiteKeyForgot,
                        size: 'invisible',
                        callback: function(token) {
                            const form = document.querySelector('form[action="{{ $passwordEmailUrl }}"]');
                            if (form && form.dataset.submitted !== '1') {
                                form.dataset.submitted = '1';
                                form.submit();
                            }
                        },
                        'error-callback': function(err) {
                            console.error('hCaptcha error:', err);
                            const form = document.querySelector('form[action="{{ $passwordEmailUrl }}"]');
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
            const forgotForm = document.querySelector('form[action="{{ $passwordEmailUrl }}"]');
            const submitBtn = document.getElementById('forgot-submit-btn');
            let submitInProgress = false;

            if (forgotForm && submitBtn) {
                forgotForm.addEventListener('submit', function(e) {
                    if (submitInProgress) {
                        return;
                    }

                    e.preventDefault();
                    submitInProgress = true;
                    submitBtn.setAttribute('disabled', 'disabled');

                    if (hcaptchaSiteKeyForgot && hcaptchaWidgetIdForgot !== null && typeof hcaptcha !== 'undefined') {
                        try {
                            hcaptcha.execute(hcaptchaWidgetIdForgot);
                        } catch (err) {
                            console.error('hCaptcha execute error:', err);
                            if (forgotForm.dataset.submitted !== '1') {
                                forgotForm.dataset.submitted = '1';
                                forgotForm.submit();
                            }
                        }
                    } else {
                        if (forgotForm.dataset.submitted !== '1') {
                            forgotForm.dataset.submitted = '1';
                            forgotForm.submit();
                        }
                    }
                });
            }
        });
    </script>
    @endif
</x-guest-layout>
