<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">Your Orders</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage and track your orders</p>
                        </div>
                        <button
                            onclick="openCreateOrderModal()"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Order
                        </button>
                    </div>

                    <!-- Filter tabs -->
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex space-x-8" aria-label="Tabs">
                            <button
                                onclick="toggleFilterTab('search')"
                                id="tab-search"
                                class="filter-tab border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600 dark:text-indigo-400"
                            >
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search
                            </button>
                            <button
                                onclick="toggleFilterTab('filters')"
                                id="tab-filters"
                                class="filter-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                Filters
                            </button>
                            <button
                                onclick="toggleFilterTab('sort')"
                                id="tab-sort"
                                class="filter-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                </svg>
                                Sorting
                            </button>
                        </nav>
                    </div>

                    <!-- Filter form -->
                    <form method="GET" action="{{ route('dashboard') }}" id="filterForm" class="mb-6">
                        <!-- Search tab -->
                        <div id="panel-search" class="filter-panel">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Quick search
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input
                                        type="text"
                                        name="search"
                                        value="{{ request('search') }}"
                                        placeholder="Search by email, ID, VIN, Order Reference..."
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Filters tab -->
                        <div id="panel-filters" class="filter-panel hidden">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Status -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Status
                                        </label>
                                        <select name="status" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                            <option value="">All statuses</option>
                                            <option value="pending payment" {{ request('status') === 'pending payment' ? 'selected' : '' }}>Pending Payment</option>
                                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                            <option value="refund" {{ request('status') === 'refund' ? 'selected' : '' }}>Refund</option>
                                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                                            <option value="fraud" {{ request('status') === 'fraud' ? 'selected' : '' }}>Fraud</option>
                                        </select>
                                    </div>

                                    <!-- Price range -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Price range
                                        </label>
                                        <div class="flex gap-2">
                                            <input
                                                type="number"
                                                name="price_from"
                                                value="{{ request('price_from') }}"
                                                placeholder="From"
                                                step="0.01"
                                                class="flex-1 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                                            >
                                            <span class="self-center text-gray-500">—</span>
                                            <input
                                                type="number"
                                                name="price_to"
                                                value="{{ request('price_to') }}"
                                                placeholder="To"
                                                step="0.01"
                                                class="flex-1 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                                            >
                                        </div>
                                    </div>

                                    <!-- Date range -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Date range
                                        </label>
                                        <div class="flex gap-2">
                                            <input
                                                type="date"
                                                name="date_from"
                                                value="{{ request('date_from') }}"
                                                placeholder="From"
                                                class="flex-1 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                                            >
                                            <span class="self-center text-gray-500">—</span>
                                            <input
                                                type="date"
                                                name="date_to"
                                                value="{{ request('date_to') }}"
                                                placeholder="To"
                                                class="flex-1 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sorting tab -->
                        <div id="panel-sort" class="filter-panel hidden">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Sort by
                                        </label>
                                        <select name="sort_by" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                            <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Created date</option>
                                            <option value="total_price" {{ request('sort_by') === 'total_price' ? 'selected' : '' }}>Price</option>
                                            <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Status</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Order
                                        </label>
                                        <select name="sort_order" class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                            <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                                            <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="mt-4 flex justify-end gap-3">
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                Reset
                            </a>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                Apply filters
                            </button>
                        </div>
                    </form>

                    <!-- Quick status filters -->
                    <div class="mb-6">
                        <div class="flex flex-wrap gap-2 items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Quick filters:</span>
                            <a 
                                href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => ''])) }}"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                            >
                                All
                            </a>
                            <a 
                                href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'pending payment'])) }}"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('status') === 'pending payment' ? 'bg-orange-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                            >
                                Awaiting payment
                            </a>
                            <a 
                                href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'paid'])) }}"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('status') === 'paid' ? 'bg-cyan-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                            >
                                Paid
                            </a>
                            <a 
                                href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'processing'])) }}"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('status') === 'processing' ? 'bg-yellow-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                            >
                                Processing
                            </a>
                            <a 
                                href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'completed'])) }}"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('status') === 'completed' ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                            >
                                Completed
                            </a>
                            <a 
                                href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'failed'])) }}"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('status') === 'failed' ? 'bg-red-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                            >
                                Failed
                            </a>
                            <a 
                                href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'refund'])) }}"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('status') === 'refund' ? 'bg-purple-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                            >
                                Refund
                            </a>
                            <a 
                                href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'expired'])) }}"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('status') === 'expired' ? 'bg-gray-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                            >
                                Expired
                            </a>
                            @if(($fraudOrders ?? 0) > 0)
                            <a 
                                href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'fraud'])) }}"
                                class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ request('status') === 'fraud' ? 'bg-red-800 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
                            >
                                Fraud
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Statistics (clickable cards) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8 gap-4 mb-6">
                        <a 
                            href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => ''])) }}"
                            class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white hover:from-blue-600 hover:to-blue-700 transition-all transform hover:scale-105 cursor-pointer {{ !request('status') ? 'ring-2 ring-blue-300 ring-offset-2' : '' }}"
                        >
                            <div class="text-sm opacity-90">Total</div>
                            <div class="text-2xl font-bold">{{ $totalOrders }}</div>
                        </a>
                        <a 
                            href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'pending payment'])) }}"
                            class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white hover:from-orange-600 hover:to-orange-700 transition-all transform hover:scale-105 cursor-pointer {{ request('status') === 'pending payment' ? 'ring-2 ring-orange-300 ring-offset-2' : '' }}"
                        >
                            <div class="text-sm opacity-90">Awaiting payment</div>
                            <div class="text-2xl font-bold">{{ $pendingPaymentOrders }}</div>
                        </a>
                        <a 
                            href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'paid'])) }}"
                            class="bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-lg p-4 text-white hover:from-cyan-600 hover:to-cyan-700 transition-all transform hover:scale-105 cursor-pointer {{ request('status') === 'paid' ? 'ring-2 ring-cyan-300 ring-offset-2' : '' }}"
                        >
                            <div class="text-sm opacity-90">Paid</div>
                            <div class="text-2xl font-bold">{{ $paidOrders }}</div>
                        </a>
                        <a 
                            href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'processing'])) }}"
                            class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-4 text-white hover:from-yellow-600 hover:to-yellow-700 transition-all transform hover:scale-105 cursor-pointer {{ request('status') === 'processing' ? 'ring-2 ring-yellow-300 ring-offset-2' : '' }}"
                        >
                            <div class="text-sm opacity-90">Processing</div>
                            <div class="text-2xl font-bold">{{ $processingOrders }}</div>
                        </a>
                        <a 
                            href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'completed'])) }}"
                            class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white hover:from-green-600 hover:to-green-700 transition-all transform hover:scale-105 cursor-pointer {{ request('status') === 'completed' ? 'ring-2 ring-green-300 ring-offset-2' : '' }}"
                        >
                            <div class="text-sm opacity-90">Completed</div>
                            <div class="text-2xl font-bold">{{ $completedOrders }}</div>
                        </a>
                        <a 
                            href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'failed'])) }}"
                            class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-4 text-white hover:from-red-600 hover:to-red-700 transition-all transform hover:scale-105 cursor-pointer {{ request('status') === 'failed' ? 'ring-2 ring-red-300 ring-offset-2' : '' }}"
                        >
                            <div class="text-sm opacity-90">Failed</div>
                            <div class="text-2xl font-bold">{{ $failedOrders }}</div>
                        </a>
                        <a 
                            href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'refund'])) }}"
                            class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white hover:from-purple-600 hover:to-purple-700 transition-all transform hover:scale-105 cursor-pointer {{ request('status') === 'refund' ? 'ring-2 ring-purple-300 ring-offset-2' : '' }}"
                        >
                            <div class="text-sm opacity-90">Refund</div>
                            <div class="text-2xl font-bold">{{ $refundOrders }}</div>
                        </a>
                        <a 
                            href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'expired'])) }}"
                            class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg p-4 text-white hover:from-gray-600 hover:to-gray-700 transition-all transform hover:scale-105 cursor-pointer {{ request('status') === 'expired' ? 'ring-2 ring-gray-300 ring-offset-2' : '' }}"
                        >
                            <div class="text-sm opacity-90">Expired</div>
                            <div class="text-2xl font-bold">{{ $expiredOrders }}</div>
                        </a>
                    </div>
                    @if(($fraudOrders ?? 0) > 0)
                    <div class="mb-6">
                        <a 
                            href="{{ route('dashboard', array_merge(request()->except('status', 'page'), ['status' => 'fraud'])) }}"
                            class="bg-gradient-to-r from-red-700 to-red-800 rounded-lg p-4 text-white hover:from-red-800 hover:to-red-900 transition-all transform hover:scale-105 cursor-pointer block {{ request('status') === 'fraud' ? 'ring-2 ring-red-300 ring-offset-2' : '' }}"
                        >
                            <div class="text-sm opacity-90">Fraud</div>
                            <div class="text-2xl font-bold">{{ $fraudOrders ?? 0 }}</div>
                        </a>
                    </div>
                    @endif

                    <!-- Orders table -->
                    @if($orders->total() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                ID
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Email
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                VIN
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Report Type
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Price
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Date
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($orders as $order)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 text-xs font-semibold">
                                                            #{{ $order->id }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $order->email ?? 'N/A' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $order->vin }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($order->report_type) }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="relative inline-block" id="status-container-{{ $order->id }}">
                                                        @if($order->status === 'completed')
                                                            <span class="status-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openStatusDropdown({{ $order->id }}, event)">
                                                                Completed
                                                            </span>
                                                        @elseif($order->status === 'paid')
                                                            <span class="status-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openStatusDropdown({{ $order->id }}, event)">
                                                                Paid
                                                            </span>
                                                        @elseif($order->status === 'processing')
                                                            <span class="status-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openStatusDropdown({{ $order->id }}, event)">
                                                                Processing
                                                            </span>
                                                        @elseif($order->status === 'failed')
                                                            <span class="status-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openStatusDropdown({{ $order->id }}, event)">
                                                                Failed
                                                            </span>
                                                        @elseif($order->status === 'refund')
                                                            <span class="status-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openStatusDropdown({{ $order->id }}, event)">
                                                                Refund
                                                            </span>
                                                        @elseif($order->status === 'expired')
                                                            <span class="status-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openStatusDropdown({{ $order->id }}, event)">
                                                                Expired
                                                            </span>
                                                        @elseif($order->status === 'fraud')
                                                            <span class="status-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-900 dark:bg-red-950 dark:text-red-100 cursor-pointer hover:opacity-80 transition-opacity" onclick="openStatusDropdown({{ $order->id }}, event)">
                                                                Fraud
                                                            </span>
                                                        @else
                                                            <span class="status-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openStatusDropdown({{ $order->id }}, event)">
                                                                Pending Payment
                                                            </span>
                                                        @endif

                                                        <!-- Dropdown menu -->
                                                        <div id="status-dropdown-{{ $order->id }}" class="hidden absolute z-50 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700" style="top: calc(100% + 0.25rem); left: 0;">
                                                            <div class="py-1">
                                                                <button onclick="updateOrderStatus({{ $order->id }}, 'pending payment')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $order->status === 'pending payment' ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                                                    <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Pending Payment</span>
                                                                </button>
                                                                <button onclick="updateOrderStatus({{ $order->id }}, 'paid')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $order->status === 'paid' ? 'bg-cyan-50 dark:bg-cyan-900/20' : '' }}">
                                                                    <span class="px-2 py-1 rounded-full text-xs bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200">Paid</span>
                                                                </button>
                                                                <button onclick="updateOrderStatus({{ $order->id }}, 'processing')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $order->status === 'processing' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                                                    <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Processing</span>
                                                                </button>
                                                                <button onclick="updateOrderStatus({{ $order->id }}, 'completed')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $order->status === 'completed' ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Completed</span>
                                                                </button>
                                                                <button onclick="updateOrderStatus({{ $order->id }}, 'failed')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $order->status === 'failed' ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                                                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Failed</span>
                                                                </button>
                                                                <button onclick="updateOrderStatus({{ $order->id }}, 'refund')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $order->status === 'refund' ? 'bg-purple-50 dark:bg-purple-900/20' : '' }}">
                                                                    <span class="px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Refund</span>
                                                                </button>
                                                                <button onclick="updateOrderStatus({{ $order->id }}, 'expired')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $order->status === 'expired' ? 'bg-gray-50 dark:bg-gray-900/20' : '' }}">
                                                                    <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">Expired</span>
                                                                </button>
                                                                <button onclick="updateOrderStatus({{ $order->id }}, 'fraud')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $order->status === 'fraud' ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                                                    <span class="px-2 py-1 rounded-full text-xs bg-red-200 text-red-900 dark:bg-red-950 dark:text-red-100">Fraud</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($order->total_price ?? 0, 2) }} <span class="text-gray-500 dark:text-gray-400 text-xs">{{ $order->currency ?? 'usd' }}</span></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <button
                                                        onclick="openOrderModal({{ $order->id }})"
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
                                                    >
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Details
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No orders found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filters</p>
                        </div>
                    @endif

                    <!-- Pagination -->
                    @if($orders->hasPages())
                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Order details modal -->
    <div id="orderModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity">
        <div class="relative top-10 mx-auto p-0 w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-xl rounded-lg bg-white dark:bg-gray-800 mb-10">
            <!-- Modal header -->
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Order details</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex px-6" aria-label="Tabs">
                    <button
                        onclick="switchModalTab('info')"
                        id="modal-tab-info"
                        class="modal-tab border-b-2 border-indigo-500 py-4 px-4 text-sm font-medium text-indigo-600 dark:text-indigo-400"
                    >
                        Main info
                    </button>
                    <button
                        onclick="switchModalTab('payment')"
                        id="modal-tab-payment"
                        class="modal-tab border-b-2 border-transparent py-4 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        Payment data
                    </button>
                </nav>
            </div>

            <!-- Modal content -->
            <div class="px-6 py-6">
                <div id="modal-tab-content-info" class="modal-tab-content">
                    <div id="orderModalContent" class="space-y-4 text-gray-900 dark:text-gray-100">
                        <!-- Content is loaded via JavaScript -->
                    </div>
                </div>
                <div id="modal-tab-content-payment" class="modal-tab-content hidden">
                    <div id="paymentModalContent" class="space-y-4 text-gray-900 dark:text-gray-100">
                        <!-- Content is loaded via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle filter tabs
        function toggleFilterTab(tabName) {
            // Hide all panels
            document.querySelectorAll('.filter-panel').forEach(panel => {
                panel.classList.add('hidden');
            });

            // Remove active state from tabs
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                tab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            // Show selected panel
            document.getElementById('panel-' + tabName).classList.remove('hidden');

            // Activate selected tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
        }

        // Toggle modal tabs
        function switchModalTab(tabName) {
            // Hide all contents
            document.querySelectorAll('.modal-tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from tabs
            document.querySelectorAll('.modal-tab').forEach(tab => {
                tab.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                tab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            // Show selected content
            document.getElementById('modal-tab-content-' + tabName).classList.remove('hidden');

            // Activate selected tab
            const activeTab = document.getElementById('modal-tab-' + tabName);
            activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            activeTab.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
        }

        const ordersData = {!! json_encode($orders->keyBy('id')->map(function($order) {
            return [
                'id' => $order->id,
                'email' => $order->email,
                'vin' => $order->vin,
                'report_type' => $order->report_type,
                'report_key' => $order->report_key,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'currency' => $order->currency ?? 'usd',
                'currency_symbol' => $order->currency_symbol ?? '$',
                'reason' => $order->reason,
                'reasonCode' => $order->reasonCode,
                'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : null,
                'payment_data' => $order->payment_data,
                'payment_method' => $order->payment_method,
            ];
        })) !!};

        function openOrderModal(orderId) {
            const order = ordersData[orderId];
            if (!order) return;

            const modal = document.getElementById('orderModal');
            const content = document.getElementById('orderModalContent');
            const paymentContent = document.getElementById('paymentModalContent');

            // Main info
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Order ID</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">#${order.id}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Email</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${order.email || 'N/A'}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">VIN</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 font-mono">${order.vin || 'N/A'}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Report type</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${order.report_type ? order.report_type.charAt(0).toUpperCase() + order.report_type.slice(1) : 'N/A'}</p>
                        ${order.report_key ? `
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 mt-3">Report Key</p>
                        <p class="text-xs font-mono text-gray-900 dark:text-gray-100 break-all">${order.report_key}</p>
                        ` : '<p class="text-xs text-gray-400 dark:text-gray-500 mt-2 italic">No key</p>'}
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Status</p>
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full ${
                            order.status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                            order.status === 'paid' ? 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200' :
                            order.status === 'processing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                            order.status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                            order.status === 'refund' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' :
                            order.status === 'expired' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' :
                            order.status === 'fraud' ? 'bg-red-200 text-red-900 dark:bg-red-950 dark:text-red-100' :
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                        }">${order.status || 'N/A'}</span>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Amount</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">${order.total_price ? parseFloat(order.total_price).toFixed(2) + ' ' + (order.currency || 'usd').toUpperCase() : 'N/A'}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Created at</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${order.created_at ? new Date(order.created_at).toLocaleString('ru-RU', {year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'}) : 'N/A'}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Payment Method</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${order.payment_method ? order.payment_method : 'N/A'}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Invoice id</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${order.invoice_id ? order.invoice_id : 'N/A'}</p>
                    </div>
                    ${order.reason ? `
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Reason</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${order.reason}</p>
                    </div>
                    ` : ''}
                    ${order.reasonCode ? `
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Reason code</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${order.reasonCode}</p>
                    </div>
                    ` : ''}
                </div>
                ${order.report_key ? `
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Resend report email</h4>
                        <div class="flex gap-3 items-end">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Email address
                                </label>
                                <input
                                    type="email"
                                    id="resendEmailInput"
                                    value="${order.email || ''}"
                                    placeholder="Enter email"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 text-sm"
                                >
                            </div>
                            <button
                                onclick="resendOrderEmail(${order.id}, event)"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap"
                            >
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Send
                            </button>
                        </div>
                    </div>
                </div>
                ` : ''}
            `;

            // Payment data
            let paymentHtml = '<p class="text-gray-500 dark:text-gray-400">No payment data</p>';
            if (order.payment_data) {
                const data = typeof order.payment_data === 'string' ? JSON.parse(order.payment_data) : order.payment_data;

                // Format keys
                function formatKey(key) {
                    return key
                        .replace(/([A-Z])/g, ' $1')
                        .replace(/_/g, ' ')
                        .replace(/^./, str => str.toUpperCase())
                        .trim();
                }

                // Render values recursively
                function formatValue(value, depth = 0) {
                    if (value === null || value === undefined) {
                        return '<span class="text-gray-400 italic">null</span>';
                    }

                    if (typeof value === 'object' && !Array.isArray(value) && value !== null) {
                        // This is an object - render nested fields
                        if (Object.keys(value).length === 0) {
                            return '<span class="text-gray-400 italic">Empty object</span>';
                        }
                        let html = '<div class="mt-2 space-y-2 pl-4 border-l-2 border-gray-300 dark:border-gray-600">';
                        for (const [k, v] of Object.entries(value)) {
                            html += `
                                <div class="py-1">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">${formatKey(k)}:</span>
                                    <div class="mt-1">${formatValue(v, depth + 1)}</div>
                                </div>
                            `;
                        }
                        html += '</div>';
                        return html;
                    }

                    if (Array.isArray(value)) {
                        // This is an array
                        if (value.length === 0) {
                            return '<span class="text-gray-400 italic">Empty array</span>';
                        }
                        let html = '<div class="mt-2 space-y-2 pl-4 border-l-2 border-blue-300 dark:border-blue-600">';
                        value.forEach((item, index) => {
                            html += `
                                <div class="py-1">
                                    <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">[${index}]:</span>
                                    <div class="mt-1">${formatValue(item, depth + 1)}</div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        return html;
                    }

                    // Primitive values
                    if (typeof value === 'boolean') {
                        return `<span class="px-2 py-1 rounded text-xs font-semibold ${value ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'}">${value}</span>`;
                    }

                    if (typeof value === 'number') {
                        // If this is a timestamp (beyond a reasonable date)
                        if (value > 1000000000 && value < 9999999999) {
                            const date = new Date(value * 1000);
                            return `<span class="font-mono">${value}</span> <span class="text-xs text-gray-500 dark:text-gray-400">(${date.toLocaleString()})</span>`;
                        }
                        return `<span class="font-mono">${value}</span>`;
                    }

                    if (typeof value === 'string') {
                        // Check if this is a long ID or URL
                        if (value.startsWith('http://') || value.startsWith('https://')) {
                            return `<a href="${value}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline break-all">${value}</a>`;
                        }
                        if (value.length > 50) {
                            return `<span class="break-all font-mono text-xs">${value}</span>`;
                        }
                        return `<span class="break-words">${value}</span>`;
                    }

                    return `<span>${String(value)}</span>`;
                }

                // Detect payment system from order payment_method
                const paymentMethod = order.payment_method;
                const isStripe = paymentMethod === 'stripe';

                // Determine payment system name
                let paymentSystem = 'Unknown';
                let paymentBadge = '<span class="px-3 py-1 bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 text-xs font-semibold rounded-full">Unknown</span>';

                if (isStripe) {
                    paymentSystem = 'Stripe';
                    paymentBadge = '<span class="px-3 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 text-xs font-semibold rounded-full">Stripe</span>';
                }

                paymentHtml = `
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Payment data (${paymentSystem})</h3>
                            ${paymentBadge}
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                `;

                // If this is Stripe, show base fields first, then stripe_data
                if (isStripe && data.stripe_data) {
                    // Base Stripe fields (not stripe_data)
                    for (const [key, value] of Object.entries(data)) {
                        if (key !== 'stripe_data') {
                            paymentHtml += `
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">${formatKey(key)}</p>
                                    <div class="text-sm text-gray-900 dark:text-gray-100">${formatValue(value)}</div>
                                </div>
                            `;
                        }
                    }

                    // stripe_data - parse fully
                    paymentHtml += `
                        <div class="md:col-span-2 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-lg p-4 border-2 border-indigo-200 dark:border-indigo-800">
                            <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Stripe Session Data (Full data)
                            </p>
                            <div class="space-y-3">
                    `;

                    // Parse stripe_data recursively
                    for (const [key, value] of Object.entries(data.stripe_data)) {
                        paymentHtml += `
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">${formatKey(key)}</p>
                                <div class="text-sm text-gray-900 dark:text-gray-100">${formatValue(value)}</div>
                            </div>
                        `;
                    }

                    paymentHtml += `
                            </div>
                        </div>
                    `;
                } else {
                    // Standard payment data - show all fields
                    for (const [key, value] of Object.entries(data)) {
                        paymentHtml += `
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">${formatKey(key)}</p>
                                <div class="text-sm text-gray-900 dark:text-gray-100">${formatValue(value)}</div>
                            </div>
                        `;
                    }
                }

                paymentHtml += '</div></div>';
            }
            paymentContent.innerHTML = paymentHtml;

            // Reset tabs to first
            switchModalTab('info');
            modal.classList.remove('hidden');
        }

        function closeOrderModal() {
            document.getElementById('orderModal').classList.add('hidden');
        }

        // Close on outside click
        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderModal();
            }
        });

        // Open status dropdown
        let openDropdownId = null;

        function openStatusDropdown(orderId, event) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }

            // Close previous open dropdown
            if (openDropdownId !== null && openDropdownId !== orderId) {
                const prevDropdown = document.getElementById('status-dropdown-' + openDropdownId);
                if (prevDropdown) {
                    prevDropdown.classList.add('hidden');
                }
            }

            const dropdown = document.getElementById('status-dropdown-' + orderId);
            if (dropdown) {
                const isHidden = dropdown.classList.contains('hidden');
                dropdown.classList.toggle('hidden');
                openDropdownId = isHidden ? orderId : null;
            }
        }

        // Close dropdown on outside click
        document.addEventListener('click', function(event) {
            if (openDropdownId !== null) {
                const container = document.getElementById('status-container-' + openDropdownId);
                if (container && !container.contains(event.target)) {
                    document.getElementById('status-dropdown-' + openDropdownId).classList.add('hidden');
                    openDropdownId = null;
                }
            }
        });

        // Update order status
        function updateOrderStatus(orderId, newStatus) {
            const container = document.getElementById('status-container-' + orderId);
            const badge = container.querySelector('.status-badge');
            const oldStatus = ordersData[orderId]?.status || badge.textContent.trim();

            // Close dropdown
            document.getElementById('status-dropdown-' + orderId).classList.add('hidden');
            openDropdownId = null;

            // Disable badge during request
            badge.style.opacity = '0.6';
            badge.style.cursor = 'wait';
            badge.style.pointerEvents = 'none';

            // Show loading indicator
            const loadingIndicator = document.createElement('span');
            loadingIndicator.className = 'ml-2 inline-block';
            loadingIndicator.innerHTML = '<svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            container.appendChild(loadingIndicator);

            fetch(`/orders/${orderId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                loadingIndicator.remove();
                badge.style.opacity = '1';
                badge.style.cursor = 'pointer';
                badge.style.pointerEvents = 'auto';

                if (data.success) {
                    // Update ordersData for modal
                    if (ordersData[orderId]) {
                        ordersData[orderId].status = newStatus;
                    }

                    // Show success notification
                    showNotification('Order status updated successfully', 'success');

                    // Reload the page to refresh stats and badges
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    showNotification(data.error || 'Failed to update status', 'error');
                }
            })
            .catch(error => {
                loadingIndicator.remove();
                badge.style.opacity = '1';
                badge.style.cursor = 'pointer';
                badge.style.pointerEvents = 'auto';
                console.error('Error:', error);
                showNotification('Failed to update status. Check your internet connection.', 'error');
            });
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all transform translate-x-0 ${
                type === 'success'
                    ? 'bg-green-500 text-white'
                    : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Appear animation
            setTimeout(() => {
                notification.classList.add('translate-x-0');
            }, 10);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Send report email
        function resendOrderEmail(orderId, event) {
            if (event) {
                event.preventDefault();
            }

            const emailInput = document.getElementById('resendEmailInput');
            const email = emailInput.value.trim();

            if (!email) {
                showNotification('Please enter email address', 'error');
                return;
            }

            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showNotification('Please enter a valid email address', 'error');
                return;
            }

            // Disable button and input
            const button = event && event.target ? event.target.closest('button') : document.querySelector(`button[onclick*="resendOrderEmail(${orderId}"]`);
            if (!button) {
                showNotification('Error: button not found', 'error');
                return;
            }
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin h-4 w-4 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sending...';
            emailInput.disabled = true;

            fetch(`/orders/${orderId}/resend-email`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                button.disabled = false;
                button.innerHTML = originalText;
                emailInput.disabled = false;

                if (data.success) {
                    showNotification(data.message || 'Email sent successfully', 'success');
                } else {
                    showNotification(data.error || 'Failed to send email', 'error');
                }
            })
            .catch(error => {
                button.disabled = false;
                button.innerHTML = originalText;
                emailInput.disabled = false;
                console.error('Error:', error);
                showNotification('Failed to send email. Check your internet connection.', 'error');
            });
        }

        // Create order form handler (event delegation)
        function handleCreateOrderSubmit(e) {
            const form = e.target.closest('form');
            if (!form || form.id !== 'createOrderForm') {
                return; // not our form - do not block submit (filterForm, etc.)
            }
            e.preventDefault();
            e.stopPropagation();

            const submitButton = form.querySelector('button[type="submit"]');
            if (!submitButton) {
                console.error('Submit button not found');
                return;
            }
            const originalButtonText = submitButton.innerHTML;

            // Collect form data
            const formData = {
                vin: form.querySelector('#vin')?.value.trim() || '',
                email: form.querySelector('#email')?.value.trim() || '',
                report_type: form.querySelector('#report_type')?.value || '',
            };

            // Client-side validation
            if (!formData.vin || formData.vin.length !== 17) {
                showNotification('VIN must be 17 characters', 'error');
                return false;
            }

            if (!formData.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
                showNotification('Enter a valid email address', 'error');
                return false;
            }

            if (!formData.report_type) {
                showNotification('Select report type', 'error');
                return false;
            }

            // Disable button
            submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="animate-spin h-5 w-5 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creating...';

            // Send request
            fetch('{{ route("dashboard.orders.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || `HTTP error! status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;

                if (data.success) {
                    showNotification(data.message || 'Order created successfully', 'success');
                    closeCreateOrderModal();
                    // Reload page after 1 second
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification(data.error || 'Failed to create order', 'error');
                }
            })
            .catch(error => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                console.error('Error:', error);
                showNotification(error.message || 'Failed to create order. Check your internet connection.', 'error');
            });

            return false;
        }

        // Bind handler via event delegation
        document.addEventListener('submit', handleCreateOrderSubmit);

        // Create order modal
        function openCreateOrderModal() {
            document.getElementById('createOrderModal').classList.remove('hidden');
        }

        function closeCreateOrderModal() {
            document.getElementById('createOrderModal').classList.add('hidden');
            // Clear form
            const form = document.getElementById('createOrderForm');
            if (form) {
                form.reset();
            }
            // Clear error messages
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(msg => msg.remove());
        }

        // Close on outside click
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('createOrderModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeCreateOrderModal();
                    }
                });
            }
        });
    </script>

    <!-- Create order modal -->
    <div id="createOrderModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity">
        <div class="relative top-10 mx-auto p-0 w-11/12 md:w-2/3 lg:w-1/2 xl:w-1/3 shadow-xl rounded-lg bg-white dark:bg-gray-800 mb-10">
            <!-- Modal header -->
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Create new order</h3>
                <button onclick="closeCreateOrderModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form id="createOrderForm" method="POST" action="#" onsubmit="return false;" class="px-6 py-6">
                <div class="space-y-6">
                    <!-- VIN -->
                    <div>
                        <label for="vin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            VIN <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="vin"
                            name="vin"
                            required
                            maxlength="17"
                            placeholder="Enter 17-character VIN"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100 uppercase"
                            oninput="this.value = this.value.toUpperCase().replace(/[^0-9A-Z]/g, '')"
                        >
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter vehicle VIN (17 characters)</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            placeholder="example@email.com"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                        >
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">User email (does not need to be registered)</p>
                    </div>

                    <!-- Report type -->
                    <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Report type <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="report_type"
                            name="report_type"
                            required
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                        >
                            <option value="">Select report type</option>
                            <option value="carfax">Carfax</option>
                            <option value="autocheck">AutoCheck</option>
                            <option value="auctions">Auctions</option>
                            <option value="sticker">Sticker</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select report type for generation</p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        onclick="closeCreateOrderModal()"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                    >
                        Create Order
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
