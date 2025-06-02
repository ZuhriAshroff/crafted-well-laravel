<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
            <div class="flex items-center space-x-4">
                <button onclick="loadAnalytics()" 
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                        class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Loading State -->
            <div id="loading" class="hidden">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                        <span class="ml-2 text-gray-600">Loading analytics...</span>
                    </div>
                </div>
            </div>

            <!-- Overview Statistics -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">System Overview</h3>
                    <div class="flex space-x-2">
                        <select id="periodSelector" onchange="changePeriod()" 
                            class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="7days">Last 7 days</option>
                            <option value="30days" selected>Last 30 days</option>
                            <option value="90days">Last 90 days</option>
                            <option value="1year">Last year</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4" id="overviewStats">
                    <!-- Overview stats will be loaded here -->
                    <div class="bg-indigo-100 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $analytics['overview']['total_users'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-pink-100 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $analytics['overview']['total_orders'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-yellow-100 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $analytics['overview']['total_products'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-green-100 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Revenue Today</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    LKR {{ number_format($analytics['overview']['revenue_today'] ?? 0, 2) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Growth Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Growth Metrics</h4>
                    <div class="space-y-4" id="growthMetrics">
                        @if(isset($analytics['growth_metrics']))
                            @foreach($analytics['growth_metrics'] as $metric => $value)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $metric) }}</span>
                                    <span class="text-sm font-medium {{ $value >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $value >= 0 ? '+' : '' }}{{ number_format($value, 1) }}%
                                    </span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h4>
                    <div class="space-y-3" id="recentActivity">
                        @if(isset($analytics['recent_activity']['recent_orders']))
                            <div>
                                <h5 class="text-sm font-medium text-gray-700">Latest Orders</h5>
                                <div class="mt-2 space-y-1">
                                    @foreach(array_slice($analytics['recent_activity']['recent_orders']->toArray(), 0, 3) as $order)
                                        <div class="text-xs text-gray-600">
                                            Order #{{ $order['id'] }} - LKR {{ number_format($order['amount'], 2) }}
                                            <span class="ml-1 px-2 py-1 text-xs rounded-full 
                                                {{ $order['status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($order['status']) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Admin Actions</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('admin.products.index') }}"
                        class="inline-flex items-center justify-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Manage Products
                    </a>

                    <a href="{{ route('admin.orders.index') }}"
                        class="inline-flex items-center justify-center px-4 py-3 bg-pink-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-pink-700 active:bg-pink-900 focus:outline-none focus:border-pink-900 focus:ring focus:ring-pink-300 disabled:opacity-25 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Manage Orders
                    </a>

                    <a href="{{ route('admin.base-formulations.index') }}"
                        class="inline-flex items-center justify-center px-4 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        Base Formulations
                    </a>

                    <a href="{{ route('admin.user-management') }}"
                        class="inline-flex items-center justify-center px-4 py-3 bg-purple-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring focus:ring-purple-300 disabled:opacity-25 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Manage Users
                    </a>

                    <a href="{{ route('admin.settings') }}"
                        class="inline-flex items-center justify-center px-4 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>

                    <button onclick="showSystemHealth()"
                        class="inline-flex items-center justify-center px-4 py-3 bg-yellow-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring focus:ring-yellow-300 disabled:opacity-25 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        System Health
                    </button>
                </div>
            </div>

            <!-- System Health Modal -->
            <div id="systemHealthModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">System Health Check</h3>
                            <button onclick="hideSystemHealth()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="systemHealthContent">
                            <div class="flex items-center justify-center py-4">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
                                <span class="ml-2 text-gray-600">Checking system health...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
// Replace your admin login JavaScript with this:

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.textContent = 'Signing in...';
            
            // Clear previous errors
            clearErrors();
            
            try {
                // Use relative URL instead of absolute HTTP URL
                const response = await fetch('/admin/login', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (response.ok && (result.success || result.status === 'success')) {
                    // Show success message
                    showSuccess('Login successful! Redirecting...');
                    
                    // Redirect to admin dashboard
                    setTimeout(() => {
                        window.location.href = result.redirect || '/admin/dashboard';
                    }, 1000);
                } else if (response.status === 422) {
                    // Validation errors
                    if (result.errors) {
                        // Show validation errors
                        const errorMessages = Object.values(result.errors).flat();
                        showError(errorMessages.join('<br>'));
                    } else {
                        showError(result.message || 'Validation failed. Please check your input.');
                    }
                } else if (response.status === 419) {
                    // CSRF token expired
                    showError('Session expired. Please refresh the page and try again.');
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    // Other errors
                    showError(result.message || 'Login failed. Please try again.');
                }
                
            } catch (error) {
                console.error('Login error:', error);
                
                if (error.message.includes('Failed to fetch')) {
                    showError('Network error. Please check your connection and try again.');
                } else {
                    showError('Login failed. Please try again.');
                }
            } finally {
                // Reset button state
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
    }
});

// Helper function to get CSRF token
function getCSRFToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (!metaTag) {
        console.error('CSRF token meta tag not found. Make sure your layout includes the CSRF meta tag.');
        return '';
    }
    return metaTag.content;
}

// Show error message
function showError(message) {
    clearErrors();
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
    errorDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <div>${message}</div>
        </div>
    `;
    
    const form = document.getElementById('loginForm');
    if (form) {
        form.insertBefore(errorDiv, form.firstChild);
    }
}

// Show success message
function showSuccess(message) {
    clearErrors();
    
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4';
    successDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    const form = document.getElementById('loginForm');
    if (form) {
        form.insertBefore(successDiv, form.firstChild);
    }
}

// Clear existing error/success messages
function clearErrors() {
    const existingMessages = document.querySelectorAll('.error-message, .success-message');
    existingMessages.forEach(message => message.remove());
}

// Optional: Add some visual feedback for form fields
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.add('ring-2', 'ring-blue-300');
        });
        
        input.addEventListener('blur', function() {
            this.classList.remove('ring-2', 'ring-blue-300');
        });
        
        // Clear errors when user starts typing
        input.addEventListener('input', function() {
            clearErrors();
        });
    });
});
    </script>
</x-app-layout>