@extends('layouts.app')

@section('title', 'User Management')

@push('styles')
    <style>
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
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">User Management</h2>
                    <p class="text-gray-600 mt-2">Manage platform users and their permissions</p>
                </div>
                <div class="flex space-x-4">
                    <button onclick="exportUsers()"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>Export Users
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="admin-card bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_users'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="admin-card bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-user-check text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Regular Users</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stats['regular_users'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="admin-card bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-user-shield text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Admins</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stats['admin_users'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="admin-card bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-calendar-plus text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">New This Month</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stats['new_users_month'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="admin-card bg-white rounded-xl shadow-md p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select name="role"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500">
                            <option value="">All Roles</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="admin-button text-white px-4 py-2 rounded-lg transition-all w-full">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="admin-card bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="admin-table">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Joined
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Orders
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users ?? [] as $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold">
                                                {{ strtoupper(substr($user->first_name ?? $user->name ?? $user->email, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $user->first_name && $user->last_name ? $user->first_name . ' ' . $user->last_name : ($user->name ?? 'Unknown') }}
                                                </div>
                                                <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                                        @if($user->role === 'admin') bg-purple-100 text-purple-800
                                                                        @else bg-blue-100 text-blue-800 @endif">
                                            {{ ucfirst($user->role ?? 'user') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $user->created_at ? $user->created_at->format('M d, Y') : 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $user->orders_count ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button onclick="viewUser({{ $user->id }})"
                                                class="text-blue-600 hover:text-blue-900 transition-colors">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="editUser({{ $user->id }})"
                                                class="text-green-600 hover:text-green-900 transition-colors">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-lg">No users found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($users) && $users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function exportUsers() {
            window.location.href = '{{ route("admin.export") }}?type=users';
        }

        function viewUser(userId) {
            window.location.href = `/admin/users/${userId}`;
        }

        function editUser(userId) {
            // Add user edit functionality
            alert('Edit user: ' + userId);
        }

        // Auto-hide flash messages
        setTimeout(() => {
            document.querySelectorAll('[class*="border-green-400"], [class*="border-red-400"]').forEach(el => {
                if (el.querySelector('button')) {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                }
            });
        }, 5000);
    </script>
@endsection