@include('header')
@php
    $homeUrl = \App\Support\LocaleRoute::route('index');
    $loginUrl = \App\Support\LocaleRoute::route('login');
@endphp
    <!-- Taboola Pixel Code -->
<script>
    _tfa.push({notify: 'event', name: 'make_purchase', id: 1650105});
</script>
<!-- End of Taboola Pixel Code -->
<!-- Status Section -->
<section class="py-12 lg:py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8 lg:p-10">
                <!-- Spinner (показывается по умолчанию) -->
                <div class="spinner-container text-center py-8" id="spinnerContainer">
                    <div class="inline-block w-12 h-12 border-4 border-primary-600 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <p class="text-gray-700" id="spinnerText">
                        {{ __('thank_you.status.checking') }}
                    </p>
                </div>

                <!-- Status Message (скрыт по умолчанию) -->
                <div id="statusMessageContainer" class="hidden">
                    <div class="text-center">
                        <div class="status-icon inline-flex items-center justify-center mb-4" id="statusIcon"></div>
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3" id="statusTitle"></h2>
                        <p class="text-gray-600 mb-6" id="statusMessage"></p>

                        <!-- Order Info -->
                        <div class="order-info bg-gray-50 rounded-lg p-6 mb-6 hidden border border-gray-200" id="orderInfo">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                        {{ __('thank_you.order.order_number') }}
                                    </div>
                                    <div class="text-xl font-bold text-gray-900 break-all word-break" id="orderReference"></div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                        {{ __('thank_you.order.status') }}
                                    </div>
                                    <div class="inline-block">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold" id="orderStatusBadge">
                                            <span id="orderStatus"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons flex flex-col sm:flex-row gap-3 justify-center hidden" id="actionButtons">
                            <!-- View Report Button (показывается только для заказов на отчет с полученным report_url) -->
                            <a id="viewReportButton" href="#" target="_blank" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors text-center hidden">
                                {{ __('thank_you.actions.view_report') }}
                            </a>
                            <a href="{{ $homeUrl }}" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors text-center">
                                {{ __('thank_you.actions.home') }}
                            </a>
                            @auth
                            <a href="{{ route('panel') }}" class="px-6 py-3 border border-primary-600 text-primary-600 hover:bg-primary-50 font-semibold rounded-lg transition-colors text-center">
                                {{ __('thank_you.actions.my_orders') }}
                            </a>
                            @else
                            <a href="{{ $loginUrl }}" class="px-6 py-3 border border-primary-600 text-primary-600 hover:bg-primary-50 font-semibold rounded-lg transition-colors text-center">
                                {{ __('thank_you.actions.login') }}
                            </a>
                            @endauth
                            <!-- Retry Payment Button (показывается только для неудачных платежей) -->
                            <button id="retryPaymentButton" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors text-center hidden">
                                {{ __('thank_you.actions.retry_payment') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
@include('footer')

<style>
    .status-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .status-icon svg {
        width: 100%;
        height: 100%;
    }

    .word-break {
        word-break: break-word;
        overflow-wrap: break-word;
    }
</style>

<script>
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('id') || urlParams.get('orderReference') || urlParams.get('order_id') || urlParams.get('order');
        const orderKey = urlParams.get('key');
        // Получаем payment_method из данных заказа, переданных через Blade, а не из URL для безопасности
        const paymentMethod = '{{ $paymentMethod ?? 'other' }}';

        const spinnerContainer = document.getElementById('spinnerContainer');
        const statusMessageContainer = document.getElementById('statusMessageContainer');
        const statusIcon = document.getElementById('statusIcon');
        const statusTitle = document.getElementById('statusTitle');
        const statusMessage = document.getElementById('statusMessage');
        const spinnerText = document.getElementById('spinnerText');
        const orderInfo = document.getElementById('orderInfo');
        const orderReferenceEl = document.getElementById('orderReference');
        const orderStatusEl = document.getElementById('orderStatus');
        const actionButtons = document.getElementById('actionButtons');
        const viewReportButton = document.getElementById('viewReportButton');
        let orderPurpose = null;
        let reportUrl = null;

        function showStatus(type, title, message, showOrderInfo = false, showRetryButton = false, showReportButton = false) {
            // Скрываем спиннер
            spinnerContainer.classList.add('hidden');

            // Показываем сообщение
            statusMessageContainer.classList.remove('hidden');

            // Устанавливаем иконку и стили
            statusIcon.className = 'status-icon mb-4';

            // Подставляем SVG из заранее подготовленных шаблонов
            const map = {
                success: 'tpl-icon-success',
                error: 'tpl-icon-error',
                warning: 'tpl-icon-warning',
                processing: 'tpl-icon-processing'
            };
            const tpl = document.getElementById(map[type]);
            if (tpl) {
                statusIcon.innerHTML = tpl.innerHTML;
            }

            // Добавляем цвет в зависимости от типа
            if (type === 'success') {
                statusIcon.classList.add('text-green-600');
            } else if (type === 'error') {
                statusIcon.classList.add('text-red-600');
            } else if (type === 'warning') {
                statusIcon.classList.add('text-yellow-600');
            } else if (type === 'processing') {
                statusIcon.classList.add('text-blue-600');
            }

            statusTitle.textContent = title;
            statusMessage.textContent = message;

            // Показываем информацию о заказе если нужно
            if (showOrderInfo && orderId) {
                orderInfo.classList.remove('hidden');
                orderReferenceEl.textContent = orderId;

                let statusText = '';
                const orderStatusBadge = document.getElementById('orderStatusBadge');

                if (type === 'success') {
                    statusText = '{{ __('thank_you.status.paid') }}';
                    orderStatusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800';
                } else if (type === 'error') {
                    statusText = '{{ __('thank_you.status.error') }}';
                    orderStatusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800';
                } else if (type === 'warning') {
                    statusText = '{{ __('thank_you.status.under_review') }}';
                    orderStatusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800';
                } else if (type === 'processing') {
                    statusText = '{{ __('thank_you.status.processing') }}';
                    orderStatusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800';
                }
                orderStatusEl.textContent = statusText;
            }

            // Показываем кнопки действий
            actionButtons.classList.remove('hidden');

            // Показываем кнопку "Посмотреть отчет" если есть report_url
            if (viewReportButton) {
                if (showReportButton && reportUrl) {
                    viewReportButton.href = reportUrl;
                    viewReportButton.classList.remove('hidden');
                } else {
                    viewReportButton.classList.add('hidden');
                }
            }

            // Показываем кнопку повторной оплаты для неудачных платежей
            const retryButton = document.getElementById('retryPaymentButton');
            if (retryButton) {
                if (showRetryButton && (type === 'error')) {
                    retryButton.classList.remove('hidden');
                } else {
                    retryButton.classList.add('hidden');
                }
            }
        }

        function updateSpinnerText(text) {
            spinnerText.textContent = text;
        }

        if (!orderId) {
            showStatus('error', '{{ __('thank_you.errors.no_order') }}', '{{ __('thank_you.errors.no_order_message') }}');
            return;
        }

        // Polling для проверки статуса
        let pollInterval;
        let pollCount = 0;
        const maxPolls = 60;
        let checkCount = 0;

        const spinnerMessages = [
            '{{ __('thank_you.status.checking') }}',
            '{{ __('thank_you.status.waiting_payment') }}',
            '{{ __('thank_you.status.processing_data') }}',
            '{{ __('thank_you.status.checking_payment') }}'
        ];

        function updateSpinnerMessage() {
            checkCount++;
            const message = spinnerMessages[checkCount % spinnerMessages.length];
            updateSpinnerText(message);
        }

        // Обновляем текст спиннера каждые 2 секунды
        setInterval(updateSpinnerMessage, 2000);

        function checkOrderStatus() {
            pollCount++;

            if (pollCount > maxPolls) {
                clearInterval(pollInterval);
                showStatus('error', '{{ __('thank_you.errors.timeout_title') }}', '{{ __('thank_you.errors.timeout_message') }}', true);
                return;
            }

            let url = '{{ route("order.check-status") }}';
            let body = { id: orderId };

            if (paymentMethod === 'monobank') {
                url = '/api/v1/monobank/check-order-status';
                body = { id: orderId };
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Сохраняем order_purpose и report_url если они есть
                    if (data.order_purpose) {
                        orderPurpose = data.order_purpose;
                    }
                    if (data.report_url) {
                        reportUrl = data.report_url;
                        // Если report_url появился, сразу обновляем кнопку
                        if (viewReportButton && orderPurpose === 'report' && reportUrl) {
                            viewReportButton.href = reportUrl;
                            viewReportButton.classList.remove('hidden');
                        }
                    }

                    // Если заказ на отчет и есть report_url, показываем кнопку
                    const showReport = (orderPurpose === 'report' && reportUrl);

                    if (data.status === 'processing' || data.status === 'paid') {
                        // Если заказ на отчет, продолжаем polling для получения report_url
                        if (orderPurpose === 'report' && !reportUrl) {
                            // Продолжаем polling, показываем статус обработки
                            // Для report_balance показываем "Генерируется отчет", для других - "Оплата успешна"
                            if (paymentMethod === 'report_balance') {
                                showStatus('processing', '{{ __('thank_you.messages.payment_success_title') }}', '{{ __('thank_you.messages.payment_success') }} {{ __('thank_you.status.processing') }}...', true, false, false);
                            } else {
                                showStatus('success', '{{ __('thank_you.messages.payment_success_title') }}', '{{ __('thank_you.messages.payment_success') }}', true, false, false);
                            }
                        } else {
                            // Если report_url уже есть или заказ не на отчет, останавливаем polling
                            clearInterval(pollInterval);
                            showStatus('success', '{{ __('thank_you.messages.payment_success_title') }}', '{{ __('thank_you.messages.payment_success') }}', true, false, showReport);
                        }
                    } else if (data.status === 'completed') {
                        clearInterval(pollInterval);
                        showStatus('success', '{{ __('thank_you.messages.order_completed_title') }}', '{{ __('thank_you.messages.order_completed') }}', true, false, showReport);
                    } else if (data.status === 'failed') {
                        clearInterval(pollInterval);
                        showStatus('error', '{{ __('thank_you.messages.payment_failed_title') }}', '{{ __('thank_you.messages.payment_failed') }}', true, true);
                    } else if (data.status === 'fraud') {
                        clearInterval(pollInterval);
                        showStatus('warning', '{{ __('thank_you.messages.order_review_title') }}', '{{ __('thank_you.messages.order_review') }}', true);
                    } else if (data.status === 'refund' || data.status === 'expired') {
                        clearInterval(pollInterval);
                        showStatus('error', '{{ __('thank_you.messages.order_not_completed_title') }}', '{{ __('thank_you.messages.order_not_completed') }}', true, true);
                    }
                    // Если статус еще не определен, продолжаем проверку
                } else {
                    // При ошибке продолжаем проверку, но не более maxPolls раз
                    if (pollCount >= maxPolls) {
                        clearInterval(pollInterval);
                        showStatus('error', '{{ __('thank_you.errors.check_error_title') }}', data.error || '{{ __('thank_you.errors.check_error_message') }}', true);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // При ошибке продолжаем проверку
                if (pollCount >= maxPolls) {
                    clearInterval(pollInterval);
                    showStatus('error', '{{ __('thank_you.errors.connection_error_title') }}', '{{ __('thank_you.errors.connection_error_message') }}', true);
                }
            });
        }

        // Начинаем проверку сразу и затем каждые 5 секунд
        checkOrderStatus();
        pollInterval = setInterval(checkOrderStatus, 5000);
    })();
</script>

<!-- Шаблоны иконок для подстановки -->
<div id="statusIconTemplates" style="display:none">
    <template id="tpl-icon-success">
        @include('components.icons.check-circle', ['class' => 'w-16 h-16', 'aria-hidden' => 'true'])
    </template>
    <template id="tpl-icon-error">
        @include('components.icons.x-mark', ['class' => 'w-16 h-16', 'aria-hidden' => 'true'])
    </template>
    <template id="tpl-icon-warning">
        @include('components.icons.exclamation-triangle', ['class' => 'w-16 h-16', 'aria-hidden' => 'true'])
    </template>
    <template id="tpl-icon-processing">
        @include('components.icons.information-circle', ['class' => 'w-16 h-16', 'aria-hidden' => 'true'])
    </template>
</div>
