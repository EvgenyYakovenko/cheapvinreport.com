<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                User Details
            </h2>
            <a href="{{ route('users.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                ← Back to list
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- User information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold mb-2">User information</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">ID</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">#{{ $user->id }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Name</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Email</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->email }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Role</p>
                            <div class="relative inline-block" id="role-container">
                                @if($user->role === 'admin')
                                    <span class="role-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openRoleDropdown(event)">
                                        Admin
                                    </span>
                                @else
                                    <span class="role-badge px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 cursor-pointer hover:opacity-80 transition-opacity" onclick="openRoleDropdown(event)">
                                        User
                                    </span>
                                @endif
                                
                                <!-- Dropdown menu -->
                                <div id="role-dropdown" class="hidden absolute z-50 mt-1 w-32 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700" style="top: calc(100% + 0.25rem); left: 0;">
                                    <div class="py-1">
                                        <button onclick="updateUserRole('user')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $user->role === 'user' ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">User</span>
                                        </button>
                                        <button onclick="updateUserRole('admin')" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 block {{ $user->role === 'admin' ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Admin</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Report balance</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                <span id="report-balance-value">{{ $user->report_balance }}</span> <span class="text-sm text-gray-500 dark:text-gray-400">reports</span>
                            </p>
                            <button onclick="openBalanceModal('report_balance', {{ $user->report_balance }})" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mt-1">
                                Edit
                            </button>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Registered at</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Last updated</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Total orders</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $user->orders->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User orders -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold mb-2">User orders</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">User order history</p>
                    </div>

                    @if($user->orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            VIN
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Report type
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Price
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Created at
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($user->orders as $order)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">#{{ $order->id }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $order->vin ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($order->report_type ?? 'N/A') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($order->status === 'completed')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        Completed
                                                    </span>
                                                @elseif($order->status === 'paid')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200">
                                                        Paid
                                                    </span>
                                                @elseif($order->status === 'processing')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        Processing
                                                    </span>
                                                @elseif($order->status === 'failed')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        Failed
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($order->total_price ?? 0, 2) }} <span class="text-gray-500 dark:text-gray-400 text-xs">UAH</span></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if($order->report_key)
                                                    <a href="{{ route('view-report', ['report_key' => $order->report_key]) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                                        View report
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">No report</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No orders found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This user has no orders yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Balance edit modal -->
    <div id="balanceModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 transition-opacity">
        <div class="relative top-20 mx-auto p-0 w-96 shadow-xl rounded-lg bg-white dark:bg-gray-800 mb-10">
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100" id="balanceModalTitle">Update balance</h3>
                <button onclick="closeBalanceModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <form id="balanceForm" onsubmit="updateBalance(event)">
                    <input type="hidden" id="balanceType" name="type">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Current value: <span id="currentBalanceValue" class="font-semibold"></span>
                        </label>
                        <input 
                            type="number" 
                            id="balanceInput" 
                            name="value" 
                            step="0.01" 
                            min="0"
                            required
                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100"
                            placeholder="Enter new value"
                        >
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeBalanceModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const userId = {{ $user->id }};

        // Open role dropdown
        function openRoleDropdown(event) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }
            
            const dropdown = document.getElementById('role-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }
        
        document.addEventListener('click', function(event) {
            const container = document.getElementById('role-container');
            const dropdown = document.getElementById('role-dropdown');
            if (container && dropdown && !container.contains(event.target) && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        });

        // Update user role
        function updateUserRole(newRole) {
            const container = document.getElementById('role-container');
            const badge = container.querySelector('.role-badge');
            
            document.getElementById('role-dropdown').classList.add('hidden');
            
            badge.style.opacity = '0.6';
            badge.style.cursor = 'wait';
            badge.style.pointerEvents = 'none';
            
            fetch(`/users/${userId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ role: newRole })
            })
            .then(response => response.json())
            .then(data => {
                badge.style.opacity = '1';
                badge.style.cursor = 'pointer';
                badge.style.pointerEvents = 'auto';
                
                if (data.success) {
                    showNotification('User role updated successfully', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    showNotification(data.error || 'Failed to update role', 'error');
                }
            })
            .catch(error => {
                badge.style.opacity = '1';
                badge.style.cursor = 'pointer';
                badge.style.pointerEvents = 'auto';
                console.error('Error:', error);
                showNotification('Failed to update role. Check your internet connection.', 'error');
            });
        }

        // Balance edit modal
        function openBalanceModal(type, currentValue) {
            const modal = document.getElementById('balanceModal');
            const title = document.getElementById('balanceModalTitle');
            const currentValueSpan = document.getElementById('currentBalanceValue');
            const input = document.getElementById('balanceInput');
            
            document.getElementById('balanceType').value = type;
            
            title.textContent = 'Update balance reports';
            currentValueSpan.textContent = currentValue + ' reports';
            input.step = '1';
            input.min = '0';
            
            input.value = currentValue;
            modal.classList.remove('hidden');
        }

        function closeBalanceModal() {
            document.getElementById('balanceModal').classList.add('hidden');
        }

        function updateBalance(event) {
            event.preventDefault();
            
            const type = document.getElementById('balanceType').value;
            const value = document.getElementById('balanceInput').value;
            
            const data = {};
            data[type] = parseInt(value);
            
            fetch(`/users/${userId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('report-balance-value').textContent = value;
                    showNotification('Balance updated successfully', 'success');
                    closeBalanceModal();
                } else {
                    showNotification(data.error || 'Failed to update balance', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to update balance. Check your internet connection.', 'error');
            });
        }

        // Close modal on outside click
        document.getElementById('balanceModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBalanceModal();
            }
        });

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all transform translate-x-0 ${
                type === 'success' 
                    ? 'bg-green-500 text-white' 
                    : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('translate-x-0');
            }, 10);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }
    </script>
</x-app-layout>

