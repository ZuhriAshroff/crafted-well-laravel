@extends('layouts.app')

@section('title', 'Order Confirmation')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
    .success-animation {
        animation: scaleIn 0.5s ease-out;
    }
    
    @keyframes scaleIn {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .floating {
        animation: floating 3s ease-in-out infinite;
    }
    
    @keyframes floating {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }
</style>
@endpush

@section('content')
<div class="bg-gradient-to-br from-white to-pink-50 min-h-screen">
    <!-- Success Message -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50">
            {{ session('success') }}
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
            <a href="{{ route('checkout.index') }}" class="hover:text-pink-600">Checkout</a>
            <span class="mx-2">/</span>
            <span class="text-gray-400">Confirmation</span>
        </nav>
    </div>

    <!-- Success Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto text-center">
            <!-- Success Icon -->
            <div class="success-animation mb-8">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 floating">
                    <i class="fas fa-check text-4xl text-green-500"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Order Confirmed!</h1>
                <p class="text-xl text-gray-600 mb-8">Thank you for your purchase. Your order has been successfully placed.</p>
            </div>

            <!-- Order Details Card -->
            <div class="bg-white rounded-lg shadow-md p-8 mb-8 text-left">
                <div class="border-b pb-6 mb-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Order Details</h2>
                            <p class="text-gray-600">Your order number is: 
                                <span class="font-mono font-bold text-pink-600">
                                    @if(session('success'))
                                        {{ preg_match('/([A-Z]{2}\d{5})/', session('success'), $matches) ? $matches[1] : 'CW' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT) }}
                                    @else
                                        CW{{ str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT) }}
                                    @endif
                                </span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Order Date</p>
                            <p class="font-medium">{{ now()->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- What's Next Section -->
                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-clock text-blue-500 mr-2"></i>
                            What happens next?
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-xs font-bold text-blue-600">1</span>
                                </div>
                                <div>
                                    <p class="font-medium">Order Processing</p>
                                    <p class="text-gray-600">We'll prepare your personalized serum (1-2 business days)</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-xs font-bold text-blue-600">2</span>
                                </div>
                                <div>
                                    <p class="font-medium">Quality Check & Packaging</p>
                                    <p class="text-gray-600">Final quality assurance and secure packaging</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-xs font-bold text-blue-600">3</span>
                                </div>
                                <div>
                                    <p class="font-medium">Dispatch & Delivery</p>
                                    <p class="text-gray-600">Shipped via courier (3-5 business days)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-green-500 mr-2"></i>
                            Important Information
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <p class="font-medium text-green-800 mb-1">Email Confirmation</p>
                                <p class="text-green-700">A detailed confirmation has been sent to your email address.</p>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="font-medium text-blue-800 mb-1">Tracking Information</p>
                                <p class="text-blue-700">You'll receive tracking details once your order ships.</p>
                            </div>
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                                <p class="font-medium text-amber-800 mb-1">Questions?</p>
                                <p class="text-amber-700">Contact our support team for any assistance.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <a href="{{ route('custom-products.index') }}" 
                   class="bg-pink-500 text-white px-8 py-3 rounded-full font-medium hover:bg-pink-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create Another Product
                </a>
                <a href="{{ route('dashboard') }}" 
                   class="bg-gray-100 text-gray-700 px-8 py-3 rounded-full font-medium hover:bg-gray-200 transition-colors">
                    <i class="fas fa-home mr-2"></i>Back to Dashboard
                </a>
            </div>

            <!-- Support Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4">Need Help?</h3>
                <div class="grid sm:grid-cols-3 gap-4 text-sm">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-envelope text-blue-500"></i>
                        </div>
                        <p class="font-medium mb-1">Email Support</p>
                        <p class="text-gray-600">support@craftedwell.com</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-phone text-green-500"></i>
                        </div>
                        <p class="font-medium mb-1">Phone Support</p>
                        <p class="text-gray-600">+94 11 234 5678</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-comments text-purple-500"></i>
                        </div>
                        <p class="font-medium mb-1">Live Chat</p>
                        <p class="text-gray-600">Available 9AM-6PM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Particles Animation (Optional) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-20 left-10 w-2 h-2 bg-pink-200 rounded-full opacity-60 floating" style="animation-delay: 0s;"></div>
        <div class="absolute top-32 right-20 w-3 h-3 bg-blue-200 rounded-full opacity-60 floating" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-40 left-20 w-2 h-2 bg-green-200 rounded-full opacity-60 floating" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 right-10 w-3 h-3 bg-purple-200 rounded-full opacity-60 floating" style="animation-delay: 0.5s;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-hide flash messages
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.querySelectorAll('[class*="border-green-400"]').forEach(el => {
                if (el.querySelector('button')) {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                }
            });
        }, 8000); // Show success message longer on this page

        // Add confetti effect (optional)
        if (typeof confetti !== 'undefined') {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        }
    });

    // Email copy functionality
    function copyEmail() {
        navigator.clipboard.writeText('support@craftedwell.com').then(function() {
            // Show temporary success message
            const email = document.querySelector('p:contains("support@craftedwell.com")');
            if (email) {
                const originalText = email.textContent;
                email.textContent = 'Copied!';
                email.classList.add('text-green-600');
                setTimeout(() => {
                    email.textContent = originalText;
                    email.classList.remove('text-green-600');
                }, 2000);
            }
        });
    }
</script>
@endpush