@extends('layouts.app')

@section('title', 'My Dashboard')

@push('styles')
<style>
    .dashboard-card {
        transition: all 0.3s ease;
    }
    .dashboard-card:hover {
        transform: translateY(-2px);
    }
    .hero-gradient {
        background: linear-gradient(135deg, #FFBFE3 0%, #ffffff 50%, #FFE9BE 100%);
    }
    .stat-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="hero-gradient min-h-screen">
    <!-- Page Header -->
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                Welcome back, {{ auth()->user()->first_name ?? auth()->user()->name }}
            </h1>
            <p class="text-lg text-gray-600">
                Your personalized skincare journey continues here
            </p>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 gap-6 mb-12 max-w-4xl">
            <a href="{{ route('survey.index') }}" 
               class="dashboard-card bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl group">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center mr-4 group-hover:bg-pink-200 transition-colors">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Create Custom Product</h3>
                        <p class="text-gray-600 text-sm">Take our skin survey to get started</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('custom-products.index') }}" 
               class="dashboard-card bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl group">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mr-4 group-hover:bg-orange-200 transition-colors">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">My Products</h3>
                        <p class="text-gray-600 text-sm">View and manage your formulations</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-12">
            <!-- Total Products -->
            <div class="stat-card p-4 md:p-6">
                <div class="text-center">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-pink-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <div class="text-xl md:text-2xl font-bold text-gray-900">{{ $stats['total_products'] ?? 0 }}</div>
                    <div class="text-xs md:text-sm text-gray-600">Products</div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="stat-card p-4 md:p-6">
                <div class="text-center">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-orange-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-xl md:text-2xl font-bold text-gray-900">{{ $stats['recent_count'] ?? 0 }}</div>
                    <div class="text-xs md:text-sm text-gray-600">This Month</div>
                </div>
            </div>

            <!-- Skin Type -->
            <div class="stat-card p-4 md:p-6">
                <div class="text-center">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div class="text-sm md:text-base font-bold text-gray-900 capitalize">{{ $stats['skin_type'] ?? 'Not Set' }}</div>
                    <div class="text-xs md:text-sm text-gray-600">Skin Type</div>
                </div>
            </div>

            <!-- Orders -->
            <div class="stat-card p-4 md:p-6">
                <div class="text-center">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-100 rounded-lg mx-auto mb-3 flex items-center justify-center">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="text-xl md:text-2xl font-bold text-gray-900">{{ $stats['total_orders'] ?? 0 }}</div>
                    <div class="text-xs md:text-sm text-gray-600">Orders</div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-3 gap-8">
            
            <!-- Recent Products -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Recent Custom Products
                        </h3>
                    </div>
                    <div class="p-6">
                        @if(isset($recentProducts) && count($recentProducts) > 0)
                            <div class="space-y-4">
                                @foreach($recentProducts as $product)
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition duration-200 group">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-pink-400 to-purple-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $product['name'] }}</h4>
                                        <p class="text-sm text-gray-600">{{ $product['personalized_for'] }}</p>
                                        <p class="text-sm font-medium text-pink-600">Rs. {{ number_format($product['total_price'], 2) }}</p>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('custom-products.show', $product['product_id']) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition duration-200 group-hover:bg-pink-600 group-hover:text-white">
                                            View
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-600 mb-2">No Custom Products Yet</h4>
                                <p class="text-gray-500 mb-6">Start your personalized skincare journey by creating your first custom product.</p>
                                <a href="{{ route('survey.index') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-orange-400 text-white font-semibold rounded-xl hover:opacity-90 transition duration-300">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                    </svg>
                                    Take Skin Survey
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Profile Card -->
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">My Profile</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Email:</span>
                            <span class="text-gray-900 font-medium">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Member since:</span>
                            <span class="text-gray-900 font-medium">{{ auth()->user()->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('profile.show') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition duration-200 w-full mt-4">
                        Edit Profile
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('products.index') }}" 
                           class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 group">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Browse Products</span>
                        </a>
                        
                        <a href="{{ route('custom-products.allergy-alternatives') }}" 
                           class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-200 group">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Allergy Info</span>
                        </a>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Need Help?</h3>
                    </div>
                    <p class="text-gray-600 mb-4 text-sm">Get support with your custom products or skincare questions.</p>
                    <a href="#" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition duration-200 w-full">
                        Contact Support
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('title', 'My Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 via-orange-50 to-purple-50">
    <!-- Hero Section with Creative Gradient -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-pink-500 via-purple-500 to-orange-500 opacity-90"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-pink-600/20 via-transparent to-orange-600/20"></div>
        
        <!-- Animated Background Elements -->
        <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute top-32 right-20 w-32 h-32 bg-white/5 rounded-full blur-2xl animate-bounce"></div>
        <div class="absolute bottom-20 left-1/4 w-16 h-16 bg-white/10 rounded-full blur-xl animate-pulse delay-1000"></div>
        
        <div class="relative px-6 py-12 sm:px-8 lg:px-12">
            <div class="max-w-7xl mx-auto">
                <div class="text-center">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-4">
                        Welcome back, 
                        <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">
                            {{ auth()->user()->first_name ?? auth()->user()->name }}
                        </span>
                    </h1>
                    <p class="text-xl text-pink-100 max-w-2xl mx-auto leading-relaxed">
                        Your personalized skincare journey continues here. Discover, create, and manage your custom beauty solutions.
                    </p>
                    
                    <!-- Quick Action Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('survey.index') }}" 
                           class="inline-flex items-center px-8 py-3 bg-white text-purple-600 font-semibold rounded-full hover:bg-gray-50 transform hover:scale-105 transition duration-300 shadow-lg">
                            <i class="fas fa-magic mr-2"></i>
                            Create New Product
                        </a>
                        <a href="{{ route('custom-products.index') }}" 
                           class="inline-flex items-center px-8 py-3 bg-transparent border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-purple-600 transform hover:scale-105 transition duration-300">
                            <i class="fas fa-flask mr-2"></i>
                            View My Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="max-w-7xl mx-auto px-6 py-12 sm:px-8 lg:px-12">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <!-- Total Products Card -->
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 hover:shadow-2xl transition duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Products</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_products'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-pink-500 to-purple-600 rounded-xl">
                        <i class="fas fa-flask text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600 font-medium">
                        <i class="fas fa-arrow-up mr-1"></i>
                        Active formulations
                    </span>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 hover:shadow-2xl transition duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Recent Activity</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['recent_count'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-blue-600 font-medium">
                        <i class="fas fa-calendar mr-1"></i>
                        This month
                    </span>
                </div>
            </div>

            <!-- Skin Profile Card -->
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 hover:shadow-2xl transition duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Skin Type</p>
                        <p class="text-2xl font-bold text-gray-900 capitalize">{{ $stats['skin_type'] ?? 'Not Set' }}</p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-green-500 to-teal-500 rounded-xl">
                        <i class="fas fa-leaf text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('profile.show') }}" class="text-sm text-purple-600 font-medium hover:text-purple-800">
                        <i class="fas fa-edit mr-1"></i>
                        Update profile
                    </a>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 hover:shadow-2xl transition duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Orders</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_orders'] ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl">
                        <i class="fas fa-shopping-bag text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-indigo-600 font-medium">
                        <i class="fas fa-truck mr-1"></i>
                        All time
                    </span>
                </div>
            </div>
        </div>

        <!-- Recent Products & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Recent Products -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-pink-500 to-purple-600">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-history mr-3"></i>
                            Recent Custom Products
                        </h3>
                    </div>
                    <div class="p-6">
                        @if(isset($recentProducts) && count($recentProducts) > 0)
                            <div class="space-y-4">
                                @foreach($recentProducts as $product)
                                <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition duration-200">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-pink-400 to-purple-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-flask text-white"></i>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $product['name'] }}</h4>
                                        <p class="text-sm text-gray-600">{{ $product['personalized_for'] }}</p>
                                        <p class="text-sm text-purple-600 font-medium">${{ number_format($product['total_price'], 2) }}</p>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('custom-products.show', $product['product_id']) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition duration-200">
                                            View
                                            <i class="fas fa-arrow-right ml-2"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-flask text-gray-300 text-6xl mb-4"></i>
                                <h4 class="text-xl font-semibold text-gray-600 mb-2">No Custom Products Yet</h4>
                                <p class="text-gray-500 mb-6">Start your personalized skincare journey by creating your first custom product.</p>
                                <a href="{{ route('survey.index') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-semibold rounded-xl hover:from-pink-600 hover:to-purple-700 transition duration-300">
                                    <i class="fas fa-magic mr-2"></i>
                                    Take Skin Survey
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions Sidebar -->
            <div class="space-y-6">
                
                <!-- Skin Survey Card -->
                <div class="bg-gradient-to-br from-pink-500 to-orange-500 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-magic text-2xl mr-3"></i>
                        <h3 class="text-xl font-bold">Skin Survey</h3>
                    </div>
                    <p class="text-pink-100 mb-6">Discover your perfect skincare formula with our personalized survey.</p>
                    <a href="{{ route('survey.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-white text-pink-600 font-semibold rounded-xl hover:bg-gray-50 transition duration-300 w-full justify-center">
                        Start Survey
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <!-- Profile Card -->
                <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-user-circle text-2xl mr-3 text-purple-600"></i>
                        <h3 class="text-xl font-bold text-gray-900">My Profile</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="text-gray-900 font-medium">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Member since:</span>
                            <span class="text-gray-900 font-medium">{{ auth()->user()->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('profile.show') }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition duration-200 w-full justify-center mt-4">
                        Edit Profile
                        <i class="fas fa-edit ml-2"></i>
                    </a>
                </div>

                <!-- Help & Support Card -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-question-circle text-2xl mr-3"></i>
                        <h3 class="text-xl font-bold">Need Help?</h3>
                    </div>
                    <p class="text-indigo-100 mb-6">Get support with your custom products or skincare questions.</p>
                    <a href="#" 
                       class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 font-semibold rounded-xl hover:bg-gray-50 transition duration-300 w-full justify-center">
                        Contact Support
                        <i class="fas fa-headset ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .animate-gradient {
        background-size: 200% 200%;
        animation: gradient-shift 6s ease infinite;
    }
</style>
@endpush
@endsection