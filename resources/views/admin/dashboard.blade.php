<!-- resources/views/admin/dashboard.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900">System Overview</h3>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-indigo-100 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Users
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $usersCount }}
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-pink-100 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Orders
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $ordersCount }}
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-amber-100 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Products
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $productsCount }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <h4 class="text-md font-medium text-gray-700">Admin Actions</h4>
                    <div class="mt-4 space-y-4">
                        <a href="{{ route('admin.products') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Manage Products
                        </a>
                        <a href="{{ route('admin.orders') }}"
                            class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Manage Orders
                        </a>
                        <a href="{{ route('admin.users') }}"
                            class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Manage Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>