@php
    $passwordStoreUrl = \App\Support\LocaleRoute::route('password.store');
@endphp
<x-guest-layout>
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900">{{ __('auth.reset_password') }}</h2>
    </div>

    <form method="POST" action="{{ $passwordStoreUrl }}" class="mt-8 space-y-6">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="space-y-5">
            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('auth.email')" />
                <div class="mt-2">
                    <x-text-input id="email"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                                  type="email"
                                  name="email"
                                  :value="old('email', $request->email)"
                                  required
                                  autofocus
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
        <div id="hcaptcha-container-reset" style="display: none;"></div>
        @endif

        <div>
            <button type="submit"
                    id="reset-submit-btn"
                    class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                {{ __('auth.reset_password') }}
            </button>
        </div>
    </form>

    @if(config('hcaptcha.site_key'))
    <script>
        const hcaptchaSiteKeyReset = @json(config('hcaptcha.site_key', ''));
        let hcaptchaWidgetIdReset = null;

        const registerCaptchaInitReset = window.registerHCaptchaInit || function(initFn) {
            window.__hcaptchaInitQueue = window.__hcaptchaInitQueue || [];
            if (typeof initFn === 'function') {
                window.__hcaptchaInitQueue.push(initFn);
            }
        };

        registerCaptchaInitReset(function() {
            if (hcaptchaWidgetIdReset !== null) {
                return;
            }

            if (hcaptchaSiteKeyReset && typeof hcaptcha !== 'undefined') {
                const container = document.getElementById('hcaptcha-container-reset');
                if (container) {
                    hcaptchaWidgetIdReset = hcaptcha.render('hcaptcha-container-reset', {
                        sitekey: hcaptchaSiteKeyReset,
                        size: 'invisible',
                        callback: function(token) {
                            const form = document.querySelector('form[action="{{ $passwordStoreUrl }}"]');
                            if (form && form.dataset.submitted !== '1') {
                                form.dataset.submitted = '1';
                                form.submit();
                            }
                        },
                        'error-callback': function(err) {
                            console.error('hCaptcha error:', err);
                            const form = document.querySelector('form[action="{{ $passwordStoreUrl }}"]');
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
            const resetForm = document.querySelector('form[action="{{ $passwordStoreUrl }}"]');
            const submitBtn = document.getElementById('reset-submit-btn');
            let submitInProgress = false;

            if (resetForm && submitBtn) {
                resetForm.addEventListener('submit', function(e) {
                    if (submitInProgress) {
                        return;
                    }

                    e.preventDefault();
                    submitInProgress = true;
                    submitBtn.setAttribute('disabled', 'disabled');

                    if (hcaptchaSiteKeyReset && hcaptchaWidgetIdReset !== null && typeof hcaptcha !== 'undefined') {
                        try {
                            hcaptcha.execute(hcaptchaWidgetIdReset);
                        } catch (err) {
                            console.error('hCaptcha execute error:', err);
                            if (resetForm.dataset.submitted !== '1') {
                                resetForm.dataset.submitted = '1';
                                resetForm.submit();
                            }
                        }
                    } else {
                        if (resetForm.dataset.submitted !== '1') {
                            resetForm.dataset.submitted = '1';
                            resetForm.submit();
                        }
                    }
                });
            }
        });
    </script>
    @endif
</x-guest-layout>
