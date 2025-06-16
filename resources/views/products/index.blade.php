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

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }

        .image-placeholder {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
        }

        .bestseller-badge {
            background: linear-gradient(45deg, #ff6b6b, #feca57);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .add-to-cart-btn {
            transition: all 0.3s ease;
        }

        .add-to-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="hero-gradient min-h-screen">
        <!-- Page Header -->
        <div class="container mx-auto px-4 py-12">
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Our Signature Collection</h1>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Experience our expertly crafted products, ready to use or customize to your unique needs.
                    Each formula is dermatologist-approved and made with premium ingredients.
                </p>
                <div class="mt-6 flex justify-center space-x-4">
                    <span
                        class="inline-flex items-center px-4 py-2 bg-white rounded-full text-sm font-medium text-gray-700 shadow-sm">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Ready to Ship
                    </span>
                    <span
                        class="inline-flex items-center px-4 py-2 bg-white rounded-full text-sm font-medium text-gray-700 shadow-sm">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Customizable
                    </span>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="max-w-4xl mx-auto mb-12">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <input type="text" id="searchInput" placeholder="Search products..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        </div>
                        <div>
                            <select id="categoryFilter"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button onclick="clearFilters()"
                                class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="hidden text-center py-12">
                <div class="inline-flex items-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-pink-500 mr-3"></div>
                    <span class="text-gray-600">Loading products...</span>
                </div>
            </div>

            <!-- Products Grid -->
            <div id="productsGrid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Products will be loaded here dynamically -->
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden text-center py-12">
                <div class="max-w-md mx-auto">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                    <p class="text-gray-600">Try adjusting your search or filter criteria</p>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center mt-16">
                <div class="bg-white rounded-2xl p-8 shadow-lg max-w-2xl mx-auto">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Want Something Completely Unique?</h2>
                    <p class="text-gray-600 mb-6">
                        Take our comprehensive skin survey to create a 100% custom formulation tailored specifically to your
                        skin.
                    </p>
                    <a href="{{ route('survey.index') }}"
                        class="inline-block bg-gradient-to-r from-purple-500 to-pink-500 text-white 
                                                                          px-8 py-3 rounded-full text-lg font-semibold hover:opacity-90 
                                                                          transition-all duration-300 transform hover:scale-105">
                        Create Custom Product
                    </a>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mt-20">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose Crafted Well</h2>
                    <p class="text-lg text-gray-600">The perfect blend of science and personalization</p>
                </div>

                <div class="grid md:grid-cols-4 gap-6 max-w-5xl mx-auto">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Instant Results</h3>
                        <p class="text-sm text-gray-600">Premium formulations that work from day one</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Clinically Proven</h3>
                        <p class="text-sm text-gray-600">Dermatologist-tested and approved formulations</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Skin-Loving</h3>
                        <p class="text-sm text-gray-600">Gentle yet effective for all skin types</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Customizable</h3>
                        <p class="text-sm text-gray-600">Modify any product to match your exact needs</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let products = [];
            let filteredProducts = [];

            // Color schemes for different categories
            const categoryColors = {
                'serum': { bg: 'from-pink-100 to-pink-200', primary: 'pink', secondary: 'rose' },
                'moisturizer': { bg: 'from-orange-100 to-orange-200', primary: 'orange', secondary: 'amber' },
                'cleanser': { bg: 'from-green-100 to-green-200', primary: 'green', secondary: 'emerald' },
                'mask': { bg: 'from-purple-100 to-purple-200', primary: 'purple', secondary: 'violet' },
                'toner': { bg: 'from-blue-100 to-blue-200', primary: 'blue', secondary: 'sky' },
                'exfoliant': { bg: 'from-yellow-100 to-yellow-200', primary: 'yellow', secondary: 'amber' }
            };

            const categoryIcons = {
                'serum': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>`,
                'moisturizer': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>`,
                'cleanser': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>`,
                'default': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>`
            };

            // Get CSRF token
            function getCSRFToken() {
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                return metaTag ? metaTag.content : null;
            }

            // Load products from API
            async function loadProducts() {
                showLoading(true);
                try {
                    const response = await fetch('/api/products');
                    const data = await response.json();

                    if (data.status === 'success') {
                        products = data.data;
                        filteredProducts = [...products];
                        displayProducts(filteredProducts);
                    } else {
                        throw new Error('Failed to load products');
                    }
                } catch (error) {
                    console.error('Error loading products:', error);
                    showNoResults();
                } finally {
                    showLoading(false);
                }
            }

            // Display products in grid
            function displayProducts(productsToShow) {
                const grid = document.getElementById('productsGrid');
                const noResults = document.getElementById('noResults');

                if (productsToShow.length === 0) {
                    grid.innerHTML = '';
                    noResults.classList.remove('hidden');
                    return;
                }

                noResults.classList.add('hidden');

                grid.innerHTML = productsToShow.map((product) => {
                    const categoryKey = product.base_category.toLowerCase();
                    const colors = categoryColors[categoryKey] || { bg: 'from-gray-100 to-gray-200', primary: 'gray', secondary: 'gray' };
                    const iconPath = categoryIcons[categoryKey] || categoryIcons.default;

                    // FIXED: Use actual bestseller status from database
                    const isBestseller = product.is_bestseller === true || product.is_bestseller === 1;

                    const originalPrice = Number(product.standard_price);
                    const discountPercentage = product.discount_percentage || 15;
                    const discountedPrice = Math.floor(originalPrice * (1 - (discountPercentage / 100)));

                    return `
                            <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden relative">
                                ${isBestseller ?
                            `<div class="absolute top-4 left-4 z-10">
                                        <span class="bestseller-badge text-white text-xs font-bold px-3 py-1 rounded-full">
                                            ⭐ BESTSELLER
                                        </span>
                                    </div>` : ''
                        }

                                ${product.image_url ?
                            `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.product_name)}" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                     <div class="h-48 bg-gradient-to-br ${colors.bg} items-center justify-center image-placeholder" style="display: none;">
                                        <div class="text-center">
                                            <div class="w-16 h-16 bg-white rounded-full shadow-lg mx-auto mb-3 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-${colors.primary}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    ${iconPath}
                                                </svg>
                                            </div>
                                            <span class="text-${colors.primary}-600 font-medium text-sm">${ucfirst(product.base_category)}</span>
                                        </div>
                                     </div>` :
                            `<div class="h-48 bg-gradient-to-br ${colors.bg} flex items-center justify-center image-placeholder">
                                        <div class="text-center">
                                            <div class="w-16 h-16 bg-white rounded-full shadow-lg mx-auto mb-3 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-${colors.primary}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    ${iconPath}
                                                </svg>
                                            </div>
                                            <span class="text-${colors.primary}-600 font-medium text-sm">${ucfirst(product.base_category)}</span>
                                        </div>
                                    </div>`
                        }

                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-xl font-semibold text-gray-900 flex-1 pr-2">${escapeHtml(product.product_name)}</h3>
                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-${colors.primary}-100 text-${colors.primary}-800 flex-shrink-0">
                                            Ready to ship
                                        </span>
                                    </div>

                                    <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                                        ${product.description ? escapeHtml(product.description) : `A premium ${product.base_category.toLowerCase()} with clinically-proven ingredients. Can be customized to your specific skin needs.`}
                                    </p>

                                    <div class="space-y-2 mb-5">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Dermatologist approved
                                        </div>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-2 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            30ml premium bottle
                                        </div>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-2 text-purple-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Free customization available
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 flex-wrap">
                                                <span class="text-2xl font-bold text-gray-900">LKR ${discountedPrice.toLocaleString()}</span>
                                                ${originalPrice > discountedPrice ?
                            `<span class="text-lg text-gray-500 line-through">LKR ${originalPrice.toLocaleString()}</span>` : ''
                        }
                                            </div>
                                            ${originalPrice > discountedPrice ?
                            `<span class="text-sm text-green-600 font-medium">Save LKR ${(originalPrice - discountedPrice).toLocaleString()}</span>` : ''
                        }
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <button onclick="addToCart('${product.product_id}', '${escapeHtml(product.product_name)}', ${discountedPrice})" 
                                                class="add-to-cart-btn w-full bg-${colors.primary}-500 text-white px-4 py-3 rounded-lg hover:bg-${colors.primary}-600 transition-all font-medium" 
                                                data-product-id="${product.product_id}">
                                            <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                                        </button>
                                        <a href="{{ route('survey.index') }}?customize=${product.product_id}" 
                                           class="w-full border-2 border-${colors.primary}-500 text-${colors.primary}-600 px-4 py-2 rounded-lg hover:bg-${colors.primary}-50 transition-colors text-center font-medium text-sm block">
                                            <i class="fas fa-cog mr-2"></i>Customize This Product
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                }).join('');

                // Add animations
                animateCards();
            }

            // FIXED: Add to cart function with proper API integration
            window.addToCart = async function (productId, productName, price) {
                console.log('Adding ready product to cart:', { productId, productName, price });

                // Get the button element
                const button = event.target.closest('button');
                if (!button) return;

                // Show loading state
                const originalContent = button.innerHTML;
                const originalClass = button.className;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';

                try {
                    const csrfToken = getCSRFToken();
                    if (!csrfToken) {
                        throw new Error('CSRF token not found');
                    }

                    const response = await fetch('{{ route("cart.add-ready") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: 1
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        showCartSuccess(productName);

                        // Update cart counter using unified system
                        if (window.CartManager) {
                            window.CartManager.updateAllCounters(data.cart_count);
                        }

                        // Add success animation to button
                        button.innerHTML = '<i class="fas fa-check mr-2"></i>Added!';
                        button.className = originalClass.replace(/bg-\w+-500/g, 'bg-green-500').replace(/hover:bg-\w+-600/g, 'hover:bg-green-600');

                        setTimeout(() => {
                            button.innerHTML = originalContent;
                            button.className = originalClass;
                            button.disabled = false;
                        }, 1500);

                    } else {
                        throw new Error(data.message || 'Failed to add product to cart');
                    }

                } catch (error) {
                    console.error('Error adding to cart:', error);
                    showError(error.message || 'Network error occurred');

                    // Reset button state
                    button.disabled = false;
                    button.innerHTML = originalContent;
                    button.className = originalClass;
                }
            };

            function showCartSuccess(productName) {
                const successDiv = document.createElement('div');
                successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg z-50 shadow-lg';
                successDiv.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium">Added to cart!</p>
                                <p class="text-sm truncate">${productName}</p>
                            </div>
                            <button class="ml-4 flex-shrink-0" onclick="this.parentElement.parentElement.remove()">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                document.body.appendChild(successDiv);
                setTimeout(() => successDiv.remove(), 4000);
            }

            function showError(message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg z-50 shadow-lg';
                errorDiv.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium">Error!</p>
                                <p class="text-sm">${message}</p>
                            </div>
                            <button class="ml-4 flex-shrink-0" onclick="this.parentElement.parentElement.remove()">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                document.body.appendChild(errorDiv);
                setTimeout(() => errorDiv.remove(), 5000);
            }

            // Filter products
            function filterProducts() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                const categoryFilter = document.getElementById('categoryFilter').value;

                filteredProducts = products.filter(product => {
                    const matchesSearch = product.product_name.toLowerCase().includes(searchTerm) ||
                        product.base_category.toLowerCase().includes(searchTerm) ||
                        product.product_type.toLowerCase().includes(searchTerm) ||
                        (product.description && product.description.toLowerCase().includes(searchTerm));

                    const matchesCategory = !categoryFilter || product.base_category === categoryFilter;

                    return matchesSearch && matchesCategory;
                });

                displayProducts(filteredProducts);
            }

            // Clear filters
            window.clearFilters = function () {
                document.getElementById('searchInput').value = '';
                document.getElementById('categoryFilter').value = '';
                filteredProducts = [...products];
                displayProducts(filteredProducts);
            };

            // Show/hide loading state
            function showLoading(show) {
                const loadingState = document.getElementById('loadingState');
                const productsGrid = document.getElementById('productsGrid');

                if (loadingState) loadingState.classList.toggle('hidden', !show);
                if (productsGrid) productsGrid.classList.toggle('hidden', show);
            }

            // Show no results message
            function showNoResults() {
                const productsGrid = document.getElementById('productsGrid');
                const noResults = document.getElementById('noResults');

                if (productsGrid) productsGrid.innerHTML = '';
                if (noResults) noResults.classList.remove('hidden');
            }

            // Animate cards on scroll
            function animateCards() {
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
            }

            // Utility functions
            function escapeHtml(unsafe) {
                return unsafe
                    .toString()
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            function ucfirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            // Event listeners
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');

            if (searchInput) searchInput.addEventListener('input', filterProducts);
            if (categoryFilter) categoryFilter.addEventListener('change', filterProducts);

            // Initialize cart manager if available
            if (window.CartManager) {
                window.CartManager.init();
            }

            // Initialize
            loadProducts();
        });
    </script>
@endpush