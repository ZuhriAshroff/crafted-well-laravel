@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
    .step-indicator {
        counter-reset: step;
    }
    .step-indicator li {
        counter-increment: step;
    }
    .step-indicator li::before {
        content: counter(step);
        @apply w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold text-sm;
    }
    .step-indicator li.active::before {
        @apply bg-pink-500;
    }
    .step-indicator li.completed::before {
        content: "✓";
        @apply bg-green-500;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        @apply ring-2 ring-pink-500 border-pink-500;
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
            <a href="{{ route('cart.index') }}" class="hover:text-pink-600">Cart</a>
            <span class="mx-2">/</span>
            <span class="text-gray-400">Checkout</span>
        </nav>
    </div>

    <!-- Progress Indicator -->
    <div class="container mx-auto px-4 mb-8">
        <ol class="flex items-center justify-center space-x-8 step-indicator">
            <li class="completed flex items-center space-x-2">
                <span></span>
                <span class="text-sm font-medium text-green-600">Cart</span>
            </li>
            <li class="active flex items-center space-x-2">
                <span></span>
                <span class="text-sm font-medium text-pink-600">Checkout</span>
            </li>
            <li class="flex items-center space-x-2">
                <span></span>
                <span class="text-sm font-medium text-gray-400">Confirmation</span>
            </li>
        </ol>
    </div>

    <!-- Checkout Content -->
    <div class="container mx-auto px-4 pb-16">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h1 class="text-3xl font-bold mb-8">Secure Checkout</h1>

                    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                        @csrf

                        <!-- Customer Information -->
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold mb-4 flex items-center">
                                <i class="fas fa-user text-pink-500 mr-2"></i>
                                Customer Information
                            </h2>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" 
                                           value="{{ $user->first_name }}" 
                                           readonly
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                                </div>
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" 
                                           value="{{ $user->last_name }}" 
                                           readonly
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                                </div>
                                <div class="form-group md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" 
                                           value="{{ $user->email }}" 
                                           readonly
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Information -->
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold mb-4 flex items-center">
                                <i class="fas fa-truck text-pink-500 mr-2"></i>
                                Shipping Information
                            </h2>
                            <div class="space-y-4">
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                    <input type="tel" 
                                           name="phone" 
                                           value="{{ old('phone') }}"
                                           placeholder="+94 77 123 4567"
                                           required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none">
                                    @error('phone')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Address *</label>
                                    <textarea name="shipping_address" 
                                              rows="3" 
                                              placeholder="Enter your complete address..."
                                              required
                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none">{{ old('shipping_address') }}</textarea>
                                    @error('shipping_address')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                        <input type="text" 
                                               name="city" 
                                               value="{{ old('city') }}"
                                               placeholder="Colombo"
                                               required
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none">
                                        @error('city')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code *</label>
                                        <input type="text" 
                                               name="postal_code" 
                                               value="{{ old('postal_code') }}"
                                               placeholder="10400"
                                               required
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none">
                                        @error('postal_code')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold mb-4 flex items-center">
                                <i class="fas fa-credit-card text-pink-500 mr-2"></i>
                                Payment Method
                            </h2>
                            <div class="space-y-3">
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-pink-300 transition-colors">
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="radio" 
                                               name="payment_method" 
                                               value="cash_on_delivery" 
                                               {{ old('payment_method', 'cash_on_delivery') == 'cash_on_delivery' ? 'checked' : '' }}
                                               class="text-pink-500">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-money-bill-wave text-green-500"></i>
                                            <div>
                                                <span class="font-medium">Cash on Delivery</span>
                                                <p class="text-sm text-gray-500">Pay when your order arrives</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-pink-300 transition-colors">
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="radio" 
                                               name="payment_method" 
                                               value="bank_transfer" 
                                               {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}
                                               class="text-pink-500">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-university text-blue-500"></i>
                                            <div>
                                                <span class="font-medium">Bank Transfer</span>
                                                <p class="text-sm text-gray-500">Direct bank transfer</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-pink-300 transition-colors opacity-50">
                                    <label class="flex items-center space-x-3 cursor-not-allowed">
                                        <input type="radio" 
                                               name="payment_method" 
                                               value="card" 
                                               disabled
                                               class="text-pink-500">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-credit-card text-purple-500"></i>
                                            <div>
                                                <span class="font-medium">Credit/Debit Card</span>
                                                <p class="text-sm text-gray-500">Coming soon</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @error('payment_method')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Order Notes -->
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold mb-4 flex items-center">
                                <i class="fas fa-sticky-note text-pink-500 mr-2"></i>
                                Order Notes (Optional)
                            </h2>
                            <textarea name="order_notes" 
                                      rows="3" 
                                      placeholder="Any special instructions for your order..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">{{ old('order_notes') }}</textarea>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-6">
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" required class="mt-1 text-pink-500">
                                <span class="text-sm text-gray-600">
                                    I agree to the <a href="#" class="text-pink-600 hover:underline">Terms and Conditions</a> 
                                    and <a href="#" class="text-pink-600 hover:underline">Privacy Policy</a>
                                </span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-pink-500 text-white py-4 rounded-full text-lg font-medium hover:bg-pink-600 transition-colors">
                            <i class="fas fa-lock mr-2"></i>Place Order
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold mb-6">Order Summary</h2>
                    
                    <!-- Cart Items -->
                    <div class="space-y-4 mb-6">
                        @foreach($cartItems as $item)
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <img src="{{ $item['image'] }}" 
                                         alt="{{ $item['name'] }}" 
                                         class="w-16 h-16 rounded-lg object-cover"
                                         onerror="this.src='{{ asset('images/placeholders/placeholder1.png') }}'">
                                    <span class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                        {{ $item['quantity'] }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900 truncate">{{ $item['name'] }}</h4>
                                    <p class="text-sm text-gray-500">30ml Serum</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-pink-600">Rs. {{ number_format($item['subtotal']) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Promo Code -->
                    <div class="mb-6">
                        <div class="flex space-x-2">
                            <input type="text" 
                                   placeholder="Promo code" 
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <button class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                                Apply
                            </button>
                        </div>
                    </div>

                    <!-- Order Totals -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">Rs. {{ number_format($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax (10%):</span>
                            <span class="font-medium">Rs. {{ number_format($tax) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping:</span>
                            <span class="font-medium text-green-600">Rs. {{ number_format($shipping) }}</span>
                        </div>
                        <hr class="my-3">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total:</span>
                            <span class="text-pink-600">Rs. {{ number_format($total) }}</span>
                        </div>
                    </div>

                    <!-- Security Features -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="font-medium text-gray-900 mb-3">Secure Checkout</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                                <span>SSL Encrypted</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-lock text-blue-500 mr-2"></i>
                                <span>Secure Payment</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-undo text-purple-500 mr-2"></i>
                                <span>30-Day Return Policy</span>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Info -->
                    <div class="bg-pink-50 rounded-lg p-4">
                        <h3 class="font-medium text-pink-900 mb-2">Delivery Information</h3>
                        <div class="text-sm text-pink-700 space-y-1">
                            <p><i class="fas fa-truck mr-2"></i>Standard Delivery: 3-5 Business Days</p>
                            <p><i class="fas fa-map-marker-alt mr-2"></i>Delivery across Sri Lanka</p>
                            <p><i class="fas fa-phone mr-2"></i>SMS/Call notifications</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    // Form validation and submission
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
                field.classList.remove('border-gray-300');
            } else {
                field.classList.add('border-gray-300');
                field.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            e.preventDefault();
            showError('Please fill in all required fields.');
            return;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        submitBtn.disabled = true;

        // Re-enable button after 10 seconds as fallback
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 10000);
    });

    // Phone number formatting
    document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.startsWith('94')) {
            value = '+' + value;
        } else if (value.startsWith('0')) {
            value = '+94 ' + value.substring(1);
        } else if (value.length > 0 && !value.startsWith('+')) {
            value = '+94 ' + value;
        }
        e.target.value = value;
    });

    // Payment method selection visual feedback
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove active class from all payment options
            document.querySelectorAll('input[name="payment_method"]').forEach(r => {
                r.closest('.border').classList.remove('border-pink-500', 'bg-pink-50');
                r.closest('.border').classList.add('border-gray-200');
            });
            
            // Add active class to selected option
            if (this.checked) {
                this.closest('.border').classList.add('border-pink-500', 'bg-pink-50');
                this.closest('.border').classList.remove('border-gray-200');
            }
        });
    });

    // Initialize active payment method styling
    document.addEventListener('DOMContentLoaded', function() {
        const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
        if (checkedRadio) {
            checkedRadio.closest('.border').classList.add('border-pink-500', 'bg-pink-50');
            checkedRadio.closest('.border').classList.remove('border-gray-200');
        }
    });

    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
        errorDiv.innerHTML = `
            ${message}
            <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        document.body.appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 5000);
    }
</script>
@endpush