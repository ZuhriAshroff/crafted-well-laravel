@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
    <style>
        /* Admin-specific styles */
        .admin-nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .admin-card {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .admin-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .admin-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .admin-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
    </style>
@endpush

@section('content')
    <div class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
        <!-- Admin Navigation -->
        <div class="admin-nav shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                            <i class="fas fa-cog text-purple-600"></i>
                        </div>
                        <h1 class="text-xl font-bold text-white">Admin Dashboard</h1>
                    </div>

                    <!-- Admin Navigation Links -->
                    <div class="flex items-center space-x-6">
                        <nav class="flex space-x-4">
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-white/80 hover:text-white px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard*') ? 'text-white border-b-2 border-white' : '' }}">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <a href="{{ route('admin.products.index') }}"
                                class="text-white/80 hover:text-white px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.products*') ? 'text-white border-b-2 border-white' : '' }}">
                                <i class="fas fa-box mr-2"></i>Products
                            </a>
                            <a href="{{ route('admin.orders.index') }}"
                                class="text-white/80 hover:text-white px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.orders*') ? 'text-white border-b-2 border-white' : '' }}">
                                <i class="fas fa-shopping-cart mr-2"></i>Orders
                            </a>
                            <a href="{{ route('admin.custom-products.index') }}"
                                class="text-white/80 hover:text-white px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.custom-products*') ? 'text-white border-b-2 border-white' : '' }}">
                                <i class="fas fa-magic mr-2"></i>Custom Products
                            </a>
                        </nav>

                        <div class="flex items-center space-x-3 text-white">
                            <div class="text-sm">
                                <div class="font-medium">{{ auth()->user()->first_name ?? 'Admin' }}</div>
                                <div class="text-xs text-white/70">{{ auth()->user()->email }}</div>
                            </div>
                            <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-white/80 hover:text-white p-2 transition-colors">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('admin-content')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Admin-specific JavaScript
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                document.querySelector('form[action*="logout"]').submit();
            }
        }

        function showError(message) {
            // Create error notification
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg z-50 max-w-md';
            errorDiv.innerHTML = `
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <span>${message}</span>
                                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
            document.body.appendChild(errorDiv);
            setTimeout(() => errorDiv.remove(), 5000);
        }

        function showSuccess(message) {
            // Create success notification
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50 max-w-md';
            successDiv.innerHTML = `
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>${message}</span>
                                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-green-500 hover:text-green-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
            document.body.appendChild(successDiv);
            setTimeout(() => successDiv.remove(), 5000);
        }
    </script>
@endpush