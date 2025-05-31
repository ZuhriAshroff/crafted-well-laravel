@extends('layouts.app')

@section('title', 'Custom Skincare - Powered by Your Answers')

@push('styles')
<style>
    .skincare {
        font-family: 'Dancing Script', cursive;
        background: linear-gradient(135deg, #ec4899, #f97316);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .hero-gradient {
        background: linear-gradient(135deg, #FFBFE3 0%, #ffffff 50%, #FFE9BE 100%);
    }
    
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    .float-animation {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(-12deg);
        }
        50% {
            transform: translateY(-10px) rotate(-12deg);
        }
    }
</style>
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')
<div class="hero-gradient min-h-screen">
    <!-- Hero Section -->
    <main class="container mx-auto px-4 py-12">
        <div class="text-center max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-8 mt-8">
                <span class="skincare">Custom Skincare ✧˖°.</span><br /> 
                Powered by Your Answers
            </h1>
            <p class="text-lg md:text-xl text-gray-700 mb-12 leading-relaxed max-w-3xl mx-auto">
                We listen to your unique skin story through a comprehensive survey, crafting precise wellness products
                that match your exact skin type, concerns, and lifestyle. Personalized skincare, tailored to you—because
                your skin deserves a solution as individual as you are.
            </p>
            
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                <a href="{{ route('survey.index') }}" 
                   class="inline-block bg-gradient-to-r from-pink-300 to-orange-200 text-gray-800 
                          px-8 py-4 rounded-full text-lg font-medium hover:opacity-90 
                          transition-all duration-300 transform hover:scale-105 shadow-lg">
                    Design My Unique Blend
                </a>
                
                @auth
                <a href="{{ route('dashboard') }}" 
                   class="inline-block bg-white text-gray-800 border-2 border-pink-300
                          px-8 py-4 rounded-full text-lg font-medium hover:bg-pink-50 
                          transition-all duration-300 shadow-lg">
                    My Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" 
                   class="inline-block bg-white text-gray-800 border-2 border-pink-300
                          px-8 py-4 rounded-full text-lg font-medium hover:bg-pink-50 
                          transition-all duration-300 shadow-lg">
                    Sign In
                </a>
                @endauth
            </div>
        </div>

        <!-- Circular Image Section -->
        <div class="relative max-w-xl mx-auto mt-16">
            <!-- Outer Circle -->
            <div class="aspect-square relative">
                <!-- Pink circle background with pulse animation -->
                <div class="absolute inset-0 rounded-full border-2 border-pink-300 pulse-animation"></div>
                
                <!-- Product image container -->
                <div class="absolute inset-12 rounded-full border-2 border-pink-300 bg-pink-50 
                           flex items-center justify-center overflow-hidden shadow-lg">
                    <img src="{{ asset('images/hero.png') }}" 
                         alt="Custom Skincare Product"
                         class="w-50 h-auto float-animation">
                </div>
                
                <!-- Decorative elements -->
                <div class="absolute top-4 right-4 w-4 h-4 bg-pink-400 rounded-full opacity-70"></div>
                <div class="absolute bottom-8 left-8 w-3 h-3 bg-orange-300 rounded-full opacity-70"></div>
                <div class="absolute top-1/3 left-2 w-2 h-2 bg-pink-300 rounded-full opacity-60"></div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mt-24 max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose Custom Skincare?</h2>
                <p class="text-lg text-gray-600">Discover the difference personalized formulation makes</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Personalized Formula</h3>
                    <p class="text-gray-600">
                        Each product is uniquely crafted based on your skin type, concerns, and lifestyle factors.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 9.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Science-Based</h3>
                    <p class="text-gray-600">
                        Our formulations are backed by dermatological research and proven ingredients.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Allergy-Safe</h3>
                    <p class="text-gray-600">
                        We consider your allergies and sensitivities to create safe, effective formulations.
                    </p>
                </div>
            </div>
        </div>

        <!-- How It Works Section -->
        <div class="mt-24 max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-lg text-gray-600">Simple steps to your perfect skincare routine</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-pink-500 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        1
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Take the Survey</h3>
                    <p class="text-gray-600">Answer questions about your skin type, concerns, and lifestyle.</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-pink-500 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        2
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Get Your Formula</h3>
                    <p class="text-gray-600">Our experts create a custom formulation just for you.</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-pink-500 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        3
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Enjoy Results</h3>
                    <p class="text-gray-600">Experience skincare that's perfectly tailored to your needs.</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="mt-24 text-center bg-white rounded-2xl p-12 shadow-xl max-w-2xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Ready to Transform Your Skin?</h2>
            <p class="text-lg text-gray-600 mb-8">
                Join thousands of customers who have discovered their perfect skincare routine.
            </p>
            <a href="{{ route('survey.index') }}" 
               class="inline-block bg-gradient-to-r from-pink-500 to-orange-400 text-white 
                      px-10 py-4 rounded-full text-lg font-semibold hover:opacity-90 
                      transition-all duration-300 transform hover:scale-105 shadow-lg">
                Start Your Skin Journey
            </a>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add intersection observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, observerOptions);

    // Observe feature cards
    document.querySelectorAll('.grid > div').forEach(card => {
        observer.observe(card);
    });
});
</script>
@endpush