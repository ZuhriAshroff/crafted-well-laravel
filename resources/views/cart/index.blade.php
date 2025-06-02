@extends('layouts.app')

@section('title', 'Shopping Cart')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .quantity-input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endpush

@section('content')
<div class="bg-gradient-to-br from-white to-pink-50 min-h-screen">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50">
            {{ session('success') }}
            <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50">
            {{ session('error') }}
            <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 py-4">
        <nav class="text-sm text-gray-600">
            <a href="{{ route('dashboard') }}" class="hover:text-pink-600">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-gray-400">Shopping Cart</span>
        </nav>
    </div>

    <!-- Cart Content -->
    <div class="container mx-auto px-4 py-8">
        @if(empty($cartItems))
            <!-- Empty Cart -->
            <div class="text-center py-16">
                <div class="bg-white rounded-lg shadow-md p-12 max-w-md mx-auto">
                    <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-6"></i>
                    <h2 class="text-2xl font-bold text-gray-700 mb-4">Your Cart is Empty</h2>
                    <p class="text-gray-500 mb-8">Looks like you haven't added any products to your cart yet.</p>
                    <a href="{{ route('custom-products.index') }}" 
                       class="bg-pink-500 text-white px-8 py-3 rounded-full hover:bg-pink-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Start Shopping
                    </a>
                </div>
            </div>
        @else
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold">Shopping Cart</h1>
                        <span class="text-gray-600">{{ count($cartItems) }} {{ Str::plural('item', count($cartItems)) }}</span>
                    </div>

                    @foreach($cartItems as $item)
                        <div class="bg-white rounded-lg shadow-md p-6 cart-item" data-id="{{ $item['id'] }}">
                            <div class="flex items-center space-x-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    <img src="{{ $item['image'] }}" 
                                         alt="{{ $item['name'] }}" 
                                         class="w-20 h-20 rounded-lg object-cover"
                                         onerror="this.src='{{ asset('images/placeholders/placeholder1.png') }}'">
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $item['name'] }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">Personalized Serum</p>
                                    <div class="flex items-center mt-2">
                                        <span class="text-sm text-gray-500 mr-2">Size:</span>
                                        <span class="text-sm font-medium">30ml</span>
                                    </div>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="flex items-center space-x-3">
                                    <button class="w-8 h-8 flex items-center justify-center border rounded-full hover:bg-gray-100 transition-colors quantity-btn" 
                                            data-action="decrease" data-id="{{ $item['id'] }}">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <input type="number" 
                                           value="{{ $item['quantity'] }}" 
                                           min="1" 
                                           max="10" 
                                           class="w-16 text-center border rounded-lg py-1 quantity-input" 
                                           data-id="{{ $item['id'] }}">
                                    <button class="w-8 h-8 flex items-center justify-center border rounded-full hover:bg-gray-100 transition-colors quantity-btn" 
                                            data-action="increase" data-id="{{ $item['id'] }}">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>

                                <!-- Price -->
                                <div class="text-right">
                                    <div class="text-lg font-bold text-pink-600">Rs. {{ number_format($item['subtotal']) }}</div>
                                    <div class="text-sm text-gray-500">Rs. {{ number_format($item['price']) }} each</div>
                                </div>

                                <!-- Remove Button -->
                                <button class="text-red-500 hover:text-red-700 p-2 remove-item" data-id="{{ $item['id'] }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <!-- Cart Actions -->
                    <div class="flex justify-between items-center pt-6">
                        <a href="{{ route('custom-products.index') }}" 
                           class="text-pink-600 hover:text-pink-700 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                        </a>
                        <button class="text-red-500 hover:text-red-700 font-medium" onclick="clearCart()">
                            <i class="fas fa-trash mr-2"></i>Clear Cart
                        </button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-xl font-bold mb-6">Order Summary</h2>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium">Rs. {{ number_format($subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax (10%):</span>
                                <span class="font-medium">Rs. {{ number_format($tax) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="font-medium text-green-600">Free</span>
                            </div>
                            <hr class="my-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span class="text-pink-600">Rs. {{ number_format($total) }}</span>
                            </div>
                        </div>

                        <!-- Promo Code -->
                        <div class="mt-6">
                            <div class="flex space-x-2">
                                <input type="text" 
                                       placeholder="Promo code" 
                                       class="flex-1 border rounded-lg px-3 py-2 text-sm">
                                <button class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                                    Apply
                                </button>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <a href="{{ route('checkout.index') }}" 
                           class="w-full bg-pink-500 text-white py-3 rounded-full text-center font-medium hover:bg-pink-600 transition-colors mt-6 block">
                            <i class="fas fa-lock mr-2"></i>Proceed to Checkout
                        </a>

                        <!-- Security Info -->
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center text-sm text-gray-600 space-x-4">
                                <span><i class="fas fa-shield-alt text-green-500 mr-1"></i>Secure Checkout</span>
                                <span><i class="fas fa-truck text-blue-500 mr-1"></i>Free Shipping</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

// Replace the entire @push('scripts') section with this:

@push('scripts')
<script>
    // Helper function to get CSRF token safely
    function getCSRFToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (!metaTag) {
            console.error('CSRF token meta tag not found. Make sure your layout includes: <meta name="csrf-token" content="{{ csrf_token() }}">');
            return null;
        }
        return metaTag.content;
    }

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

    // Quantity update functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.quantity-btn')) {
            const btn = e.target.closest('.quantity-btn');
            const action = btn.dataset.action;
            const itemId = btn.dataset.id;
            const input = document.querySelector(`input[data-id="${itemId}"]`);
            
            if (!input) {
                showError('Could not find quantity input');
                return;
            }
            
            let currentValue = parseInt(input.value);
            
            if (action === 'increase' && currentValue < 10) {
                input.value = currentValue + 1;
                updateCartItem(itemId, currentValue + 1);
            } else if (action === 'decrease' && currentValue > 1) {
                input.value = currentValue - 1;
                updateCartItem(itemId, currentValue - 1);
            }
        }
    });

    // Handle direct input changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            const itemId = e.target.dataset.id;
            const quantity = parseInt(e.target.value);
            
            if (quantity >= 1 && quantity <= 10) {
                updateCartItem(itemId, quantity);
            } else {
                e.target.value = Math.max(1, Math.min(10, quantity));
            }
        }
    });

    // Remove item functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const itemId = e.target.closest('.remove-item').dataset.id;
            removeCartItem(itemId);
        }
    });

    async function updateCartItem(itemId, quantity) {
        try {
            const csrfToken = getCSRFToken();
            if (!csrfToken) {
                showError('Security token not found. Please refresh the page.');
                return;
            }

            const response = await fetch(`{{ url('/cart/update') }}/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ quantity: quantity })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                // Reload page to update totals
                location.reload();
            } else {
                showError(result.message || 'Failed to update cart');
            }
        } catch (error) {
            console.error('Error updating cart:', error);
            showError('Failed to update cart. Please try again.');
        }
    }

    async function removeCartItem(itemId) {
        if (!confirm('Are you sure you want to remove this item from your cart?')) {
            return;
        }

        try {
            const csrfToken = getCSRFToken();
            if (!csrfToken) {
                showError('Security token not found. Please refresh the page.');
                return;
            }

            const response = await fetch(`{{ url('/cart/remove') }}/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                // Remove the item from DOM with animation
                const cartItem = document.querySelector(`[data-id="${itemId}"]`);
                if (cartItem) {
                    cartItem.style.transition = 'opacity 0.3s, transform 0.3s';
                    cartItem.style.opacity = '0';
                    cartItem.style.transform = 'translateX(-100%)';
                }
                
                setTimeout(() => {
                    location.reload();
                }, 300);
            } else {
                showError(result.message || 'Failed to remove item');
            }
        } catch (error) {
            console.error('Error removing item:', error);
            showError('Failed to remove item. Please try again.');
        }
    }

    async function clearCart() {
        if (!confirm('Are you sure you want to clear your entire cart?')) {
            return;
        }

        try {
            const csrfToken = getCSRFToken();
            if (!csrfToken) {
                showError('Security token not found. Please refresh the page.');
                return;
            }

            const response = await fetch('{{ route("cart.clear") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                location.reload();
            } else {
                showError(result.message || 'Failed to clear cart');
            }
        } catch (error) {
            console.error('Error clearing cart:', error);
            showError('Failed to clear cart. Please try again.');
        }
    }

    function showError(message) {
        // Check if document.body exists
        if (!document.body) {
            console.error('Error:', message);
            alert(message); // Fallback if body doesn't exist
            return;
        }

        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
        errorDiv.innerHTML = `
            ${message}
            <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(errorDiv);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (errorDiv && errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 5000);
    }
</script>
@endpush