@include('header')
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary-50 to-primary-100 py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ __('panel.title') }}</h1>
                        <h2 class="text-2xl text-gray-700"><span class="text-gray-600">{{ __('panel.welcome') }}</span> <strong class="text-primary-600">{{ $user->name }}</strong></h2>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg">
                            {{ __('panel.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="py-8 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Balance & Topup -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Balance Card -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white p-4">
                                <h5 class="text-center text-lg font-bold mb-0">{{ __('panel.balance.title') }}</h5>
                            </div>
                            <div class="p-6">
                                <div class="text-center mb-6">
                                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-primary-50 to-primary-100 border-4 border-primary-200 mb-3">
                                        <h3 class="text-4xl font-bold text-primary-600">{{ number_format($user->report_balance) }}</h3>
                                    </div>
                                    <p class="text-gray-500 text-sm">{{ __('panel.balance.reports_available') }}</p>
                                </div>
                                <button type="button" class="w-full px-4 py-3 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg" id="topupReportButton">
                                    {{ __('panel.balance.topup_button') }}
                                </button>
                            </div>
                        </div>

                        <!-- Report Balance Topup Bundles -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hidden" id="checkout-report-form">
                            <h5 class="text-xl font-bold text-gray-900 mb-4 text-center">{{ __('panel.balance.topup_title') }}</h5>

                            <div class="space-y-3">
                                <!-- Starter Pack (5 Reports) -->
                                <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-primary-500 hover:shadow-md transition">
                                    <div class="flex justify-between items-center mb-2">
                                        <div>
                                            <h6 class="font-bold text-gray-900">Starter Pack</h6>
                                            <p class="text-sm text-gray-600">5 Reports</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-gray-900">
                                                <span id="starterBundlePricePanel">-</span>
                                            </div>
                                            <p class="text-xs text-primary-600">
                                                <span id="starterBundlePricePerReportPanel">-</span> per report
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button" id="starterBundleButtonPanel"
                                            class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                                        Get 5 Reports Now
                                    </button>
                                </div>

                                <!-- Most Popular (25 Reports) -->
                                <div class="border-2 border-primary-500 rounded-lg p-4 hover:shadow-md transition relative">
                                    <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                        <span class="bg-primary-600 text-white px-3 py-0.5 rounded-full text-xs font-semibold">⭐️ Most Popular</span>
                                    </div>
                                    <div class="flex justify-between items-center mb-2 pt-2">
                                        <div>
                                            <h6 class="font-bold text-gray-900">Most Popular</h6>
                                            <p class="text-sm text-gray-600">25 Reports</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-gray-900">
                                                <span id="popularBundlePricePanel">-</span>
                                            </div>
                                            <p class="text-xs text-primary-600">
                                                <span id="popularBundlePricePerReportPanel">-</span> per report
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button" id="popularBundleButtonPanel"
                                            class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                                        Get 25 Reports Now
                                    </button>
                                </div>

                                <!-- Best Value (100 Reports) -->
                                <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-primary-500 hover:shadow-md transition relative">
                                    <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                        <span class="bg-green-600 text-white px-3 py-0.5 rounded-full text-xs font-semibold">💎 Best Value</span>
                                    </div>
                                    <div class="flex justify-between items-center mb-2 pt-2">
                                        <div>
                                            <h6 class="font-bold text-gray-900">Best Value</h6>
                                            <p class="text-sm text-gray-600">100 Reports</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-bold text-gray-900">
                                                <span id="valueBundlePricePanel">-</span>
                                            </div>
                                            <p class="text-xs text-primary-600">
                                                <span id="valueBundlePricePerReportPanel">-</span> per report
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button" id="valueBundleButtonPanel"
                                            class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm">
                                        Get 100 Reports Now
                                    </button>
                                </div>
                            </div>

                            <div id="topupReportMessageDiv" class="mt-3 hidden"></div>
                        </div>

                        <!-- Account Settings Card -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-gray-800 to-gray-900 text-white p-4">
                                <h5 class="text-center text-lg font-bold mb-0">{{ __('panel.settings.title') }}</h5>
                            </div>
                            <div class="p-6 space-y-4">
                                <!-- Update Name -->
                                <div>
                                    <label for="userName" class="block text-sm font-medium text-gray-700 mb-2">{{ __('panel.settings.name_label') }}</label>
                                    <div class="flex gap-2">
                                        <input type="text" id="userName" value="{{ $user->name }}"
                                               class="flex-1 px-3 py-2 border-2 border-gray-300 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900">
                                        <button type="button" id="updateNameButton"
                                                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition">
                                            {{ __('panel.settings.update') }}
                                        </button>
                                    </div>
                                    <div id="nameMessage" class="mt-2 hidden"></div>
                                </div>

                                <!-- Change Password -->
                                <div class="pt-4 border-t border-gray-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('panel.settings.change_password') }}</label>
                                    <div class="space-y-2">
                                        <input type="password" id="currentPassword" placeholder="{{ __('panel.settings.current_password') }}"
                                               class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900">
                                        <input type="password" id="newPassword" placeholder="{{ __('panel.settings.new_password') }}"
                                               class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900">
                                        <input type="password" id="confirmPassword" placeholder="{{ __('panel.settings.confirm_password') }}"
                                               class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-200 text-gray-900">
                                        <button type="button" id="updatePasswordButton"
                                                class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition">
                                            {{ __('panel.settings.update_password') }}
                                        </button>
                                    </div>
                                    <div id="passwordMessage" class="mt-2 hidden"></div>
                                </div>

                                <!-- Delete Account -->
                                <div class="pt-4 border-t border-red-200">
                                    <label class="block text-sm font-medium text-red-700 mb-2">{{ __('panel.settings.delete_account') }}</label>
                                    <div class="space-y-2">
                                        <input type="password" id="deleteAccountPassword" placeholder="{{ __('panel.settings.confirm_password_delete') }}"
                                               class="w-full px-3 py-2 border-2 border-red-300 rounded-lg focus:border-red-500 focus:ring-2 focus:ring-red-200 text-gray-900">
                                        <button type="button" id="deleteAccountButton"
                                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                                            {{ __('panel.settings.delete_button') }}
                                        </button>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">{{ __('panel.settings.delete_warning') }}</p>
                                    <div id="deleteMessage" class="mt-2 hidden"></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Orders -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200">
                            <div class="bg-gradient-to-r from-gray-800 to-gray-900 text-white p-4 rounded-t-xl">
                                <h2 class="text-xl font-bold mb-0">{{ __('panel.orders.title') }}</h2>
                            </div>
                            <div class="p-4">
                                <form method="GET" action="{{ route('panel') }}" class="mb-4 flex flex-col sm:flex-row gap-2 sm:items-center">
                                    <input
                                        type="text"
                                        name="vin"
                                        value="{{ $vinSearch ?? '' }}"
                                        placeholder="Search by VIN"
                                        class="w-full sm:w-80 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                    >
                                    <button
                                        type="submit"
                                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-md transition"
                                    >
                                        Search
                                    </button>
                                    @if(!empty($vinSearch))
                                        <a
                                            href="{{ route('panel') }}"
                                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition text-center"
                                        >
                                            Clear
                                        </a>
                                    @endif
                                </form>

                                @if($orders->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">ID</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">TYPE</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">VIN</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">{{ __('panel.orders.status') }}</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">{{ __('panel.orders.information') }}</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">{{ __('panel.orders.actions') }}</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">{{ __('panel.orders.date') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($orders as $order)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm"><code class="bg-gray-100 px-2 py-1 rounded text-gray-800 text-xs">{{ $order->id }}</code></td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-xs">{{ $order->report_type ?: '—' }}</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-xs"><code class="bg-gray-100 px-2 py-1 rounded text-gray-800 text-xs">{{ $order->vin ?: '—' }}</code></td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            @if($order->status == 'paid' || $order->status == 'completed')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $order->status }}</span>
                                            @elseif($order->status == 'pending payment')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $order->status }}</span>
                                            @elseif($order->status == 'failed' || $order->status == 'expired')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ $order->status }}</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $order->status }}</span>
                                            @endif
                                        </td>
                                                        <td class="px-4 py-3 text-sm">
                                                            @if($order->order_purpose == 'topup_report_balance')
                                                @if(!is_null($order->report_to_add) && $order->report_to_add > 0)
                                                    <div class="text-primary-600 font-semibold text-xs">
                                                        +{{ $order->report_to_add }}
                                                        @php
                                                            $reportsWord = $order->report_to_add == 1 ? __('panel.balance.report') : __('panel.balance.reports');
                                                        @endphp
                                                        {{ $reportsWord }}
                                                    </div>
                                                @else
                                                    <div class="text-primary-600 font-semibold text-xs">
                                                        {{ number_format($order->total_price ?? 0, 2) }} {{ strtoupper($order->currency ?? 'usd') }}
                                                    </div>
                                                @endif
                                            @elseif($order->order_purpose == 'report')
                                                @if($order->payment_method == 'report_balance' && $order->total_price == 0)
                                                    <div class="text-primary-600 font-semibold text-xs">
                                                        1 {{ __('panel.balance.report') }}
                                                    </div>
                                                @else
                                                    <div class="font-semibold text-gray-900 text-xs">
                                                        {{ number_format($order->total_price, 2) }} {{ strtoupper($order->currency ?? 'usd') }}
                                                    </div>
                                                @endif
                                            @else
                                                <div class="font-semibold text-gray-900 text-xs">
                                                    {{ number_format($order->total_price ?? 0, 2) }} {{ strtoupper($order->currency ?? 'usd') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            @if($order->report_key)
                                                <a href="{{ route('view-report', ['report_key' => $order->report_key]) }}"
                                                   class="inline-flex px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition"
                                                   target="_blank"
                                                   title="{{ __('panel.orders.view_report') }}">
                                                    {{ __('panel.orders.view_report') }}
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-xs">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-4">
                                        {{ $orders->links() }}
                                    </div>
                                @else
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                                        <p class="text-blue-800">{{ __('panel.orders.no_orders') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Переводы для JavaScript
            const translations = {
                please_enter_name: @json(__('panel.settings.js.please_enter_name')),
                updating: @json(__('panel.settings.js.updating')),
                name_updated_success: @json(__('panel.settings.js.name_updated_success')),
                error_updating_name: @json(__('panel.settings.js.error_updating_name')),
                error_request: @json(__('panel.settings.js.error_request')),
                please_fill_all_fields: @json(__('panel.settings.js.please_fill_all_fields')),
                passwords_not_match: @json(__('panel.settings.js.passwords_not_match')),
                password_min_length: @json(__('panel.settings.js.password_min_length')),
                password_updated_success: @json(__('panel.settings.js.password_updated_success')),
                error_updating_password: @json(__('panel.settings.js.error_updating_password')),
                please_enter_password: @json(__('panel.settings.js.please_enter_password')),
                confirm_delete_account: @json(__('panel.settings.js.confirm_delete_account')),
                deleting: @json(__('panel.settings.js.deleting')),
                error_deleting_account: @json(__('panel.settings.js.error_deleting_account')),
                error_loading_prices: @json(__('panel.settings.js.error_loading_prices')),
                error_creating_order: @json(__('panel.settings.js.error_creating_order')),
                please_wait: @json(__('panel.settings.js.please_wait')),
                account_deleted_success: @json(__('panel.settings.js.account_deleted_success')),
            };

            // Настройка цен для отчетов
            @php
                $packagePrices = null;
                $hasError = false;

                if ($topupReportBalancePrice) {
                    // SettingService::getSetting() уже возвращает декодированный массив
                    if (is_array($topupReportBalancePrice)) {
                        $packagePrices = $topupReportBalancePrice;
                    } elseif (is_string($topupReportBalancePrice)) {
                        // Если по какой-то причине это строка, декодируем
                        $decoded = json_decode($topupReportBalancePrice, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $packagePrices = $decoded;
                        } else {
                            $hasError = true;
                        }
                    } else {
                        $hasError = true;
                    }
                } else {
                    $hasError = true;
                }

                // Определяем валюту и символ
                $currency = $currency ?? 'usd';
                $currencySymbols = [
                    'usd' => 'USD ($)',
                    'uah' => 'UAH (₴)',
                    'pln' => 'PLN (zł)',
                    'kzt' => 'KZT (₸)',
                ];
                $currencySymbol = $currencySymbols[$currency] ?? 'USD ($)';
            @endphp
            const packagePrices = @json($packagePrices);
            const currentCurrency = @json($currency ?? 'usd');
            const currencySymbol = @json($currencySymbol ?? 'USD ($)');
            const hasPriceError = @json($hasError);

            function getReportPackagePrice(amount) {
                if (hasPriceError || !packagePrices) {
                    throw new Error(translations.error_loading_prices);
                }

                if (!amount || amount < 1) {
                    return 0;
                }

                const key = String(amount);
                const pricesByCurrency = packagePrices[key] ?? packagePrices[amount];
                if (!pricesByCurrency || typeof pricesByCurrency !== 'object') {
                    throw new Error('Не найден пакет для указанного количества отчетов.');
                }

                const packagePrice = pricesByCurrency[currentCurrency];
                if (packagePrice !== undefined && typeof packagePrice === 'number') {
                    return packagePrice;
                }

                throw new Error(`Цена для валюты ${currentCurrency} не найдена в настройках.`);
            }

            function getReportPackagePricePerReport(amount) {
                if (!amount || amount < 1) {
                    return 0;
                }

                return getReportPackagePrice(amount) / amount;
            }

            const topupReportButton = document.getElementById('topupReportButton');
            const checkoutReportForm = document.getElementById('checkout-report-form');
            const topupReportMessageDiv = document.getElementById('topupReportMessageDiv');

            function submitPaymentForm(paymentForm) {
                const form = document.createElement('form');
                form.method = paymentForm.method || 'POST';
                form.action = paymentForm.action;
                form.acceptCharset = 'UTF-8';
                form.style.display = 'none';

                Object.entries(paymentForm.fields || {}).forEach(([name, value]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value === null || value === undefined ? '' : String(value);
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }

            // Функция для покупки баланса отчетов
            async function purchaseReportBalance(amount) {
                try {
                    const response = await fetch('{{ route("topup.report-balance") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            amount: amount
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success && data.payment_form) {
                        submitPaymentForm(data.payment_form);
                    } else if (response.ok && data.success && data.url) {
                        // Редирект на платежную систему
                        window.location.href = data.url;
                    } else {
                        showTopupReportError(data.error || translations.error_creating_order);
                    }
                } catch (error) {
                    console.error('Purchase error:', error);
                    showTopupReportError(translations.error_request);
                }
            }

            // Инициализация цен для бандлов
            function initializeBundlePrices() {
                if (hasPriceError || !packagePrices) {
                    console.error('Price error or packages not available:', {hasPriceError, packagePrices});
                    return;
                }

                // Starter Pack (5 reports)
                try {
                    const starterPrice = getReportPackagePrice(5);
                    const starterPricePerReport = getReportPackagePricePerReport(5);
                    const starterPriceEl = document.getElementById('starterBundlePricePanel');
                    const starterPricePerReportEl = document.getElementById('starterBundlePricePerReportPanel');
                    if (starterPriceEl) starterPriceEl.textContent = currencySymbol + starterPrice.toFixed(2);
                    if (starterPricePerReportEl) starterPricePerReportEl.textContent = currencySymbol + starterPricePerReport.toFixed(2);
                } catch (e) {
                    console.error('Error calculating starter bundle price:', e);
                }

                // Most Popular (25 reports)
                try {
                    const popularPrice = getReportPackagePrice(25);
                    const popularPricePerReport = getReportPackagePricePerReport(25);
                    const popularPriceEl = document.getElementById('popularBundlePricePanel');
                    const popularPricePerReportEl = document.getElementById('popularBundlePricePerReportPanel');
                    if (popularPriceEl) popularPriceEl.textContent = currencySymbol + popularPrice.toFixed(2);
                    if (popularPricePerReportEl) popularPricePerReportEl.textContent = currencySymbol + popularPricePerReport.toFixed(2);
                } catch (e) {
                    console.error('Error calculating popular bundle price:', e);
                }

                // Best Value (100 reports)
                try {
                    const valuePrice = getReportPackagePrice(100);
                    const valuePricePerReport = getReportPackagePricePerReport(100);
                    const valuePriceEl = document.getElementById('valueBundlePricePanel');
                    const valuePricePerReportEl = document.getElementById('valueBundlePricePerReportPanel');
                    if (valuePriceEl) valuePriceEl.textContent = currencySymbol + valuePrice.toFixed(2);
                    if (valuePricePerReportEl) valuePricePerReportEl.textContent = currencySymbol + valuePricePerReport.toFixed(2);
                } catch (e) {
                    console.error('Error calculating value bundle price:', e);
                }
            }

            // Проверка наличия цен при загрузке
            if (hasPriceError || !packagePrices) {
                showTopupReportError(translations.error_loading_prices);
                topupReportButton.disabled = true;
                topupReportButton.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                // Инициализируем цены при загрузке
                initializeBundlePrices();
            }

            // Показать форму пополнения баланса отчетов
            topupReportButton.addEventListener('click', function() {
                if (hasPriceError || !packagePrices) {
                    showTopupReportError(translations.error_loading_prices);
                    return;
                }
                checkoutReportForm.classList.remove('hidden');

                // Прокрутка к форме
                setTimeout(() => {
                    checkoutReportForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 300);
            });

            // Starter Pack Button (5 reports)
            const starterBundleButtonPanel = document.getElementById('starterBundleButtonPanel');
            if (starterBundleButtonPanel) {
                starterBundleButtonPanel.addEventListener('click', async function() {
                    await purchaseReportBalance(5);
                });
            }

            // Most Popular Button (25 reports)
            const popularBundleButtonPanel = document.getElementById('popularBundleButtonPanel');
            if (popularBundleButtonPanel) {
                popularBundleButtonPanel.addEventListener('click', async function() {
                    await purchaseReportBalance(25);
                });
            }

            // Best Value Button (100 reports)
            const valueBundleButtonPanel = document.getElementById('valueBundleButtonPanel');
            if (valueBundleButtonPanel) {
                valueBundleButtonPanel.addEventListener('click', async function() {
                    await purchaseReportBalance(100);
                });
            }

            function showTopupReportError(message) {
                topupReportMessageDiv.innerHTML = `<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg" role="alert">${message}</div>`;
                topupReportMessageDiv.classList.remove('hidden');
            }

            // Update Name
            const updateNameButton = document.getElementById('updateNameButton');
            const userNameInput = document.getElementById('userName');
            const nameMessage = document.getElementById('nameMessage');

            if (updateNameButton) {
                updateNameButton.addEventListener('click', async function() {
                    const name = userNameInput.value.trim();

                    if (!name) {
                        showMessage(nameMessage, translations.please_enter_name, 'error');
                        return;
                    }

                    updateNameButton.disabled = true;
                    updateNameButton.textContent = translations.updating;

                    try {
                        const response = await fetch('{{ route("panel.update-name") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ name: name })
                        });

                        const data = await response.json();

                        if (data.success) {
                            showMessage(nameMessage, data.message || translations.name_updated_success, 'success');
                            // Обновляем имя в заголовке страницы
                            const welcomeName = document.querySelector('h2 strong.text-primary-600');
                            if (welcomeName) {
                                welcomeName.textContent = data.name || name;
                            }
                        } else {
                            showMessage(nameMessage, data.error || translations.error_updating_name, 'error');
                        }
                    } catch (error) {
                        showMessage(nameMessage, translations.error_request, 'error');
                    } finally {
                        updateNameButton.disabled = false;
                        updateNameButton.textContent = '{{ __('panel.settings.update') }}';
                        // Разрешаем повторный запрос через 24 часа (раз в день)
                        // На клиенте просто блокируем до следующего дня
                        nameUpdateInProgress = false;
                    }
                });
            }

            // Update Password
            const updatePasswordButton = document.getElementById('updatePasswordButton');
            const currentPasswordInput = document.getElementById('currentPassword');
            const newPasswordInput = document.getElementById('newPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const passwordMessage = document.getElementById('passwordMessage');

            if (updatePasswordButton) {
                let passwordUpdateInProgress = false;
                updatePasswordButton.addEventListener('click', async function() {
                    if (passwordUpdateInProgress) {
                        return;
                    }

                    const currentPassword = currentPasswordInput.value;
                    const newPassword = newPasswordInput.value;
                    const confirmPassword = confirmPasswordInput.value;

                    if (!currentPassword || !newPassword || !confirmPassword) {
                        showMessage(passwordMessage, translations.please_fill_all_fields, 'error');
                        return;
                    }

                    if (newPassword !== confirmPassword) {
                        showMessage(passwordMessage, translations.passwords_not_match, 'error');
                        return;
                    }

                    if (newPassword.length < 8) {
                        showMessage(passwordMessage, translations.password_min_length, 'error');
                        return;
                    }

                    passwordUpdateInProgress = true;
                    updatePasswordButton.disabled = true;
                    updatePasswordButton.textContent = translations.updating;

                    try {
                        const response = await fetch('{{ route("panel.update-password") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                current_password: currentPassword,
                                password: newPassword,
                                password_confirmation: confirmPassword
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            showMessage(passwordMessage, data.message || translations.password_updated_success, 'success');
                            currentPasswordInput.value = '';
                            newPasswordInput.value = '';
                            confirmPasswordInput.value = '';
                        } else {
                            showMessage(passwordMessage, data.error || translations.error_updating_password, 'error');
                        }
                    } catch (error) {
                        showMessage(passwordMessage, translations.error_request, 'error');
                    } finally {
                        updatePasswordButton.disabled = false;
                        updatePasswordButton.textContent = '{{ __('panel.settings.update_password') }}';
                        // Разрешаем повторный запрос через 24 часа (раз в день)
                        // На клиенте просто блокируем до следующего дня
                        passwordUpdateInProgress = false;
                    }
                });
            }

            // Delete Account
            const deleteAccountButton = document.getElementById('deleteAccountButton');
            const deleteAccountPasswordInput = document.getElementById('deleteAccountPassword');
            const deleteMessage = document.getElementById('deleteMessage');

            if (deleteAccountButton) {
                let deleteAccountInProgress = false;
                deleteAccountButton.addEventListener('click', async function() {
                    if (deleteAccountInProgress) {
                        return;
                    }

                    const password = deleteAccountPasswordInput.value;

                    if (!password) {
                        showMessage(deleteMessage, translations.please_enter_password, 'error');
                        return;
                    }

                    if (!confirm(translations.confirm_delete_account)) {
                        return;
                    }

                    deleteAccountInProgress = true;
                    deleteAccountButton.disabled = true;
                    deleteAccountButton.textContent = translations.deleting;

                    try {
                        const response = await fetch('{{ route("panel.delete-account") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ password: password })
                        });

                        const data = await response.json();

                        if (data.success) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.href = '{{ route("index") }}';
                            }
                        } else {
                            showMessage(deleteMessage, data.error || translations.error_deleting_account, 'error');
                            deleteAccountButton.disabled = false;
                            deleteAccountButton.textContent = '{{ __('panel.settings.delete_button') }}';
                            // Разрешаем повторный запрос через 24 часа (раз в день)
                            // На клиенте просто блокируем до следующего дня
                            deleteAccountInProgress = false;
                        }
                    } catch (error) {
                        showMessage(deleteMessage, translations.error_request, 'error');
                        deleteAccountButton.disabled = false;
                        deleteAccountButton.textContent = '{{ __('panel.settings.delete_button') }}';
                        // Разрешаем повторный запрос через 24 часа (раз в день)
                        // На клиенте просто блокируем до следующего дня
                        deleteAccountInProgress = false;
                    }
                });
            }

            function showMessage(element, message, type) {
                if (!element) return;

                const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
                element.innerHTML = `<div class="${bgColor} border px-4 py-3 rounded-lg" role="alert">${message}</div>`;
                element.classList.remove('hidden');

                if (type === 'success') {
                    setTimeout(() => {
                        element.classList.add('hidden');
                    }, 5000);
                }
            }
        });
    </script>
@include('footer')
