@extends('layouts.app')

@section('title', 'Our Products - Crafted Well')

@push('styles')
<style>
    .product-card {
        transition: all 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
    }
    .hero-gradient {
        background: linear-gradient(135deg, #FFBFE3 0%, #ffffff 50%, #FFE9BE 100%);
    }
</style>
@endpush

@section('content')
<div class="hero-gradient min-h-screen">
    <!-- Navigation -->

    <!-- Page Header -->
    <div class="container mx-auto px-4 py-12">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Our Products</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Discover our range of customizable skincare solutions, each designed to be personalized to your unique needs.
            </p>
        </div>

        <!-- Base Products Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- Custom Facial Serum -->
            <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="h-64 bg-gradient-to-br from-pink-100 to-pink-200 flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-white rounded-full shadow-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-10 h-10 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 9.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <span class="text-pink-600 font-medium">Base Product</span>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Custom Facial Serum</h3>
                    <p class="text-gray-600 mb-4">
                        A personalized serum targeting your specific skin concerns with concentrated active ingredients.
                    </p>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Custom formulation
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Allergy-safe ingredients
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            30ml bottle
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-bold text-gray-900">From $49</span>
                            <span class="text-sm text-gray-500">/bottle</span>
                        </div>
                        <a href="{{ route('survey.index') }}" 
                           class="bg-pink-500 text-white px-6 py-2 rounded-full hover:bg-pink-600 transition-colors">
                            Customize
                        </a>
                    </div>
                </div>
            </div>

            <!-- Custom Moisturizer -->
            <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="h-64 bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-white rounded-full shadow-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <span class="text-orange-600 font-medium">Base Product</span>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Custom Moisturizer</h3>
                    <p class="text-gray-600 mb-4">
                        A tailored moisturizer formulated to match your skin type and environmental factors.
                    </p>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Climate-adapted formula
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            SPF options available
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            50ml tube
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-bold text-gray-900">From $39</span>
                            <span class="text-sm text-gray-500">/tube</span>
                        </div>
                        <a href="{{ route('survey.index') }}" 
                           class="bg-orange-500 text-white px-6 py-2 rounded-full hover:bg-orange-600 transition-colors">
                            Customize
                        </a>
                    </div>
                </div>
            </div>

            <!-- Custom Cleanser -->
            <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="h-64 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-white rounded-full shadow-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-green-600 font-medium">Base Product</span>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Custom Cleanser</h3>
                    <p class="text-gray-600 mb-4">
                        A gentle yet effective cleanser designed for your skin type and specific cleansing needs.
                    </p>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            pH balanced formula
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Sulfate-free options
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            150ml bottle
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-bold text-gray-900">From $29</span>
                            <span class="text-sm text-gray-500">/bottle</span>
                        </div>
                        <a href="{{ route('survey.index') }}" 
                           class="bg-green-500 text-white px-6 py-2 rounded-full hover:bg-green-600 transition-colors">
                            Customize
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-16">
            <div class="bg-white rounded-2xl p-8 shadow-lg max-w-2xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Ready to Create Your Perfect Routine?</h2>
                <p class="text-gray-600 mb-6">
                    Take our comprehensive skin survey to get personalized product recommendations.
                </p>
                <a href="{{ route('survey.index') }}" 
                   class="inline-block bg-gradient-to-r from-pink-500 to-orange-400 text-white 
                          px-8 py-3 rounded-full text-lg font-semibold hover:opacity-90 
                          transition-all duration-300 transform hover:scale-105">
                    Start Your Survey
                </a>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mt-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Our Custom Products Work</h2>
                <p class="text-lg text-gray-600">The science behind personalized skincare</p>
            </div>
            
            <div class="grid md:grid-cols-4 gap-6 max-w-5xl mx-auto">
                <div class="text-center">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Fast Acting</h3>
                    <p class="text-sm text-gray-600">See results in 2-4 weeks with targeted formulations</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Clinically Safe</h3>
                    <p class="text-sm text-gray-600">Dermatologist-approved ingredients and formulations</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Gentle Formula</h3>
                    <p class="text-sm text-gray-600">Suitable for sensitive skin with natural ingredients</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Adaptable</h3>
                    <p class="text-sm text-gray-600">Formulations can be updated as your skin changes</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth animations for product cards
    const cards = document.querySelectorAll('.product-card');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    }, observerOptions);

    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});
</script>
@endpush