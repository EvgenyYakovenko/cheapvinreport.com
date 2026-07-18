<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold mb-2">Application Settings</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Application settings management</p>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 bg-green-100 dark:bg-green-900/50 border border-green-400 text-green-700 dark:text-green-200 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.update') }}" id="settingsForm">
                        @csrf
                        
                        <div class="space-y-6">
                            <!-- Currency Conversion Rates -->
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">
                                            Cached Currency Rates (Monobank)
                                        </h3>
                                        <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                                            @php
                                                $currencyCodeMap = [
                                                    'usd' => 840,
                                                    'eur' => 978,
                                                    'pln' => 985,
                                                    'kzt' => 398,
                                                    'uah' => 980,
                                                ];
                                                $toCode = $currencyCodeMap['uah'];
                                                $ratePairs = ['usd', 'eur', 'pln', 'kzt'];
                                            @endphp
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                @foreach($ratePairs as $from)
                                                    @php
                                                        $fromCode = $currencyCodeMap[$from];
                                                        $rateKey = 'mono_currency_'.$fromCode.'_'.$toCode;
                                                        $rateValue = \App\Services\SettingService::getSetting($rateKey);
                                                    @endphp
                                                    <div class="bg-white dark:bg-gray-800 rounded-md p-3 border border-amber-200 dark:border-amber-800">
                                                        <div class="text-xs font-semibold text-amber-700 dark:text-amber-300 uppercase">
                                                            {{ strtoupper($from) }} → UAH
                                                        </div>
                                                        <div class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                                                            {{ $rateValue !== null ? $rateValue : 'Not cached' }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="mt-3 text-xs">
                                                Rates are cached in settings as <code class="bg-amber-100 dark:bg-amber-900 px-1 rounded">mono_currency_XXX_980</code>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Multi Currency Info -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                            Multi-currency
                                        </h3>
                                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                            <p>
                                                Status: <strong>{{ config('multicurrency.enabled', true) ? 'Enabled' : 'Disabled' }}</strong>
                                            </p>
                                            <p class="mt-1 text-xs">
                                                Manage multi-currency via the configuration file 
                                                <code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">config/multicurrency.php</code> 
                                                or the environment variable <code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">MULTI_CURRENCY_ENABLED</code>.
                                            </p>
                                            <p class="mt-1 text-xs">
                                                When enabled, the currency is determined by the user locale. 
                                                When disabled, the default currency is always used.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Default Currency -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <label for="default_currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Default Currency
                                </label>
                                <select 
                                    id="default_currency"
                                    name="settings[default_currency]" 
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                                    required
                                >
                                    <option value="usd" {{ ($settings['default_currency']->value ?? 'usd') === 'usd' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="uah" {{ ($settings['default_currency']->value ?? 'usd') === 'uah' ? 'selected' : '' }}>UAH (₴)</option>
                                    <option value="pln" {{ ($settings['default_currency']->value ?? 'usd') === 'pln' ? 'selected' : '' }}>PLN (zł)</option>
                                    <option value="kzt" {{ ($settings['default_currency']->value ?? 'usd') === 'kzt' ? 'selected' : '' }}>KZT (₸)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Default currency used when no mapping applies.</p>
                            </div>

                            <!-- Currency Mapping -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Currency Mapping
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Specify which currency is used for each language.</p>
                                
                                @php
                                    $currencyMapping = isset($settings['currency_mapping']) && $settings['currency_mapping']->value
                                        ? json_decode($settings['currency_mapping']->value, true)
                                        : ['en' => 'usd', 'uk' => 'uah', 'ru' => 'usd', 'es' => 'usd'];
                                    
                                    // Default values for each language
                                    $defaultCurrencyMapping = [
                                        'en' => 'usd',
                                        'uk' => 'uah',
                                        'ru' => 'usd',
                                        'es' => 'usd',
                                    ];
                                @endphp
                                
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($activeLocales ?? [] as $localeCode => $localeData)
                                        @php
                                            $localeName = $localeData['native'] ?? $localeData['name'] ?? $localeCode;
                                            $defaultCurrency = $defaultCurrencyMapping[$localeCode] ?? 'usd';
                                            $selectedCurrency = $currencyMapping[$localeCode] ?? $defaultCurrency;
                                        @endphp
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                                {{ $localeName }} ({{ $localeCode }})
                                            </label>
                                            <select name="currency_mapping[{{ $localeCode }}]" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                                <option value="usd" {{ $selectedCurrency === 'usd' ? 'selected' : '' }}>USD ($)</option>
                                                <option value="uah" {{ $selectedCurrency === 'uah' ? 'selected' : '' }}>UAH (₴)</option>
                                                <option value="pln" {{ $selectedCurrency === 'pln' ? 'selected' : '' }}>PLN (zł)</option>
                                                <option value="kzt" {{ $selectedCurrency === 'kzt' ? 'selected' : '' }}>KZT (₸)</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Prices by Currency -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Report Prices by Currency
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Set prices for each report type in each currency</p>
                                
                                @php
                                    $currencies = ['usd' => 'USD ($)', 'uah' => 'UAH (₴)', 'pln' => 'PLN (zł)', 'kzt' => 'KZT (₸)'];
                                    $reportTypes = [
                                        'carfax' => 'Carfax',
                                        'autocheck' => 'AutoCheck',
                                        'auctions' => 'Auctions',
                                        'sticker' => 'Sticker'
                                    ];
                                    
                                    $prices = [];
                                    foreach ($reportTypes as $type => $name) {
                                        $prices[$type] = isset($settings[$type.'_price']) && $settings[$type.'_price']->value 
                                            ? json_decode($settings[$type.'_price']->value, true) 
                                            : ['usd' => 10, 'uah' => 300, 'pln' => 40, 'kzt' => 400];
                                    }
                                @endphp
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-100 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Report type</th>
                                                @foreach($currencies as $code => $label)
                                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ $label }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($reportTypes as $type => $name)
                                                <tr>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $name }}
                                                    </td>
                                                    @foreach($currencies as $code => $label)
                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                            <input 
                                                                type="number" 
                                                                name="price[{{ $type }}][{{ $code }}]"
                                                                value="{{ $prices[$type][$code] ?? 0 }}"
                                                                step="0.01"
                                                                min="0"
                                                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 text-center"
                                                                required
                                                            >
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Topup Report Balance Price (JSON) -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Topup Report Balance Price
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Configure fixed packages and their prices</p>
                                
                                <div id="price-ranges-container" class="space-y-3">
                                    <!-- Dynamic fields are added via JavaScript -->
                                </div>
                                
                                <button 
                                    type="button" 
                                    onclick="addPackagePrice()"
                                    class="mt-3 px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                                >
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add package
                                </button>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                Save settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Parse the topup_report_balance_price JSON setting
        let packagePrices = {};
        @if(isset($settings['topup_report_balance_price']) && $settings['topup_report_balance_price']->value)
            try {
                @php
                    $jsonValue = $settings['topup_report_balance_price']->value;
                    $decoded = json_decode($jsonValue, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $packagePrices = $decoded;
                    } else {
                        $packagePrices = ["5" => ["usd" => 12.5, "uah" => 0, "pln" => 0, "kzt" => 0], "25" => ["usd" => 50, "uah" => 0, "pln" => 0, "kzt" => 0], "100" => ["usd" => 150, "uah" => 0, "pln" => 0, "kzt" => 0]];
                    }
                @endphp
                packagePrices = @json($packagePrices);
            } catch(e) {
                console.error('Error parsing package prices:', e);
                packagePrices = {"5": {"usd": 12.5, "uah": 0, "pln": 0, "kzt": 0}, "25": {"usd": 50, "uah": 0, "pln": 0, "kzt": 0}, "100": {"usd": 150, "uah": 0, "pln": 0, "kzt": 0}};
            }
        @else
            packagePrices = {"5": {"usd": 12.5, "uah": 0, "pln": 0, "kzt": 0}, "25": {"usd": 50, "uah": 0, "pln": 0, "kzt": 0}, "100": {"usd": 150, "uah": 0, "pln": 0, "kzt": 0}};
        @endif
        const hasNumericPackages = Object.keys(packagePrices || {}).every(key => /^\d+$/.test(String(key)));
        if (!hasNumericPackages) {
            packagePrices = {"5": {"usd": 12.5, "uah": 0, "pln": 0, "kzt": 0}, "25": {"usd": 50, "uah": 0, "pln": 0, "kzt": 0}, "100": {"usd": 150, "uah": 0, "pln": 0, "kzt": 0}};
        }

        const currencies = {
            'usd': 'USD ($)',
            'uah': 'UAH (₴)',
            'pln': 'PLN (zł)',
            'kzt': 'KZT (₸)'
        };

        let packageIndex = 0;

        // Add a new package
        function addPackagePrice(amount = null, prices = null) {
            const container = document.getElementById('price-ranges-container');
            const index = packageIndex++;
            
            const amountValue = amount || '';
            const pricesObj = prices || {usd: 0, uah: 0, pln: 0, kzt: 0};
            
            const rangeDiv = document.createElement('div');
            rangeDiv.className = 'bg-white dark:bg-gray-800 p-4 rounded border border-gray-200 dark:border-gray-600';
            rangeDiv.id = `range-${index}`;
            
            let pricesHtml = '';
            for (const [code, label] of Object.entries(currencies)) {
                pricesHtml += `
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">${label}</label>
                        <input 
                            type="number" 
                            name="topup_package_price[${index}][${code}]"
                            value="${pricesObj[code] || 0}"
                            step="0.01"
                            min="0"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                            required
                        >
                    </div>
                `;
            }
            
            rangeDiv.innerHTML = `
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Number of reports (e.g. 25)</label>
                        <input 
                            type="text" 
                            name="topup_package_amount_key[${index}]" 
                            value="${amountValue}"
                            placeholder="25"
                            pattern="^\\d+$"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                            required
                        >
                    </div>
                    <button 
                        type="button" 
                        onclick="removePackagePrice(${index})"
                        class="px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors mt-6"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-4 gap-3">
                    ${pricesHtml}
                </div>
            `;
            
            container.appendChild(rangeDiv);
        }

        // Remove a package
        function removePackagePrice(index) {
            const rangeDiv = document.getElementById(`range-${index}`);
            if (rangeDiv) {
                rangeDiv.remove();
            }
        }

        // Load existing packages on page load
        document.addEventListener('DOMContentLoaded', function() {
            for (const [amount, prices] of Object.entries(packagePrices)) {
                addPackagePrice(amount, prices);
            }
            
            // If no packages exist, add an empty one
            if (Object.keys(packagePrices).length === 0) {
                addPackagePrice();
            }
        });

        // Handle form submit: serialize packages to JSON
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            const amountKeys = document.querySelectorAll('input[name^="topup_package_amount_key"]');
            
            const packagePricesObj = {};
            amountKeys.forEach((keyInput, index) => {
                const key = keyInput.value.trim();
                if (key) {
                    const prices = {};
                    for (const code of Object.keys(currencies)) {
                        const priceInput = document.querySelector(`input[name="topup_package_price[${index}][${code}]"]`);
                        if (priceInput) {
                            const value = parseFloat(priceInput.value);
                            if (!isNaN(value)) {
                                prices[code] = value;
                            }
                        }
                    }
                    if (Object.keys(prices).length > 0) {
                        packagePricesObj[key] = prices;
                    }
                }
            });
            
            // Create a hidden JSON field
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'settings[topup_report_balance_price]';
            hiddenInput.value = JSON.stringify(packagePricesObj);
            this.appendChild(hiddenInput);
        });
    </script>
</x-app-layout>
