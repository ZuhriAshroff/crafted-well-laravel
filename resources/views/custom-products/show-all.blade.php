@extends('layouts.app')

@section('title', 'My Custom Products')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="bg-gradient-to-br from-white to-pink-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Custom Products</h1>
                    <p class="text-gray-600 mt-1">Manage your personalized skincare formulations</p>
                </div>
                <a href="{{ route('survey.index') }}" 
                   class="bg-pink-600 text-white px-6 py-3 rounded-lg hover:bg-pink-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create New Product
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-pink-100 p-3 rounded-full">
                        <i class="fas fa-flask text-pink-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Total Products</h3>
                        <p class="text-2xl font-bold text-pink-600">{{ $userStats['total_products'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-clock text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Recent Activity</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ count($userStats['recent_products']) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-heart text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Skin Health</h3>
                        <p class="text-2xl font-bold text-green-600">Great</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        @if($customProducts->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($customProducts as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <!-- Product Image -->
                        <div class="h-48 bg-gradient-to-br from-pink-100 to-purple-100 flex items-center justify-center">
                            <img src="{{ asset('images/products/serum-main.png') }}" 
                                 alt="{{ $product->product_name }}" 
                                 class="h-32 w-32 object-cover rounded-lg"
                                 onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">{{ $product->product_name }}</h3>
                            
                            <!-- Price -->
                            <div class="mb-3">
                                <span class="text-2xl font-bold text-pink-600">Rs. {{ number_format($product->total_price * 0.9) }}</span>
                                <span class="text-gray-500 line-through ml-2">Rs. {{ number_format($product->total_price) }}</span>
                            </div>
                            
                            <!-- Product Details -->
                            <div class="space-y-2 mb-4">
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-user-circle mr-2"></i>
                                    Skin Type: {{ ucfirst($product->profile_data['skin_type'] ?? 'N/A') }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Formulated: {{ $product->formulation_date->format('M d, Y') }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-flask mr-2"></i>
                                    {{ count($product->selected_ingredients) }} Active Ingredients
                                </div>
                            </div>

                            <!-- Concerns Tags -->
                            @if(isset($product->profile_data['skin_concerns']))
                                <div class="flex flex-wrap gap-1 mb-4">
                                    @foreach(array_slice($product->profile_data['skin_concerns'], 0, 3) as $concern)
                                        <span class="bg-pink-100 text-pink-600 px-2 py-1 rounded-full text-xs">
                                            {{ ucfirst($concern) }}
                                        </span>
                                    @endforeach
                                    @if(count($product->profile_data['skin_concerns']) > 3)
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                            +{{ count($product->profile_data['skin_concerns']) - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <a href="{{ route('custom-products.show', $product->custom_product_id) }}" 
                                   class="flex-1 bg-pink-600 text-white py-2 px-4 rounded-lg text-center hover:bg-pink-700 transition-colors">
                                    View Details
                                </a>
                                <!-- <a href="{{ route('custom-products.reformulate', $product->custom_product_id) }}" 
                                   class="bg-blue-100 text-blue-600 py-2 px-3 rounded-lg hover:bg-blue-200 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a> -->
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($customProducts->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $customProducts->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="bg-white rounded-lg shadow-md p-12 max-w-md mx-auto">
                    <i class="fas fa-flask text-6xl text-gray-300 mb-6"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">No Custom Products Yet</h3>
                    <p class="text-gray-600 mb-6">
                        Create your first personalized skincare product by taking our skin analysis survey.
                    </p>
                    <a href="{{ route('survey.index') }}" 
                       class="bg-pink-600 text-white px-6 py-3 rounded-lg hover:bg-pink-700 transition-colors inline-block">
                        <i class="fas fa-plus mr-2"></i>Create Your First Product
                    </a>
                </div>
            </div>
        @endif

        <!-- Recent Activity -->
        @if($userStats['recent_products'] && count($userStats['recent_products']) > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-6">Recent Activity</h2>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="space-y-4">
                        @foreach($userStats['recent_products'] as $recent)
                            <div class="flex items-center justify-between py-3 border-b last:border-b-0">
                                <div class="flex items-center">
                                    <div class="bg-pink-100 p-2 rounded-full mr-4">
                                        <i class="fas fa-flask text-pink-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold">{{ $recent['name'] }}</h4>
                                        <p class="text-sm text-gray-600">
                                            Formulated {{ \Carbon\Carbon::parse($recent['formulation_date'])->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('custom-products.show', $recent['product_id']) }}" 
                                   class="text-pink-600 hover:text-pink-700">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-hide flash messages
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.querySelectorAll('[class*="border-green-400"], [class*="border-red-400"]').forEach(el => {
                if (el.querySelector('button')) {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                }
            });
        }, 5000);
    });
</script>
@endpush