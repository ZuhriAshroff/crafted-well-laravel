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
    .category-colors {
        --serum: linear-gradient(135deg, #FFBFE3 0%, #FFE9BE 100%);
        --moisturizer: linear-gradient(135deg, #FFE9BE 0%, #BFDBFE 100%);
        --cleanser: linear-gradient(135deg, #BFDBFE 0%, #C7F7C7 100%);
        --mask: linear-gradient(135deg, #DDD6FE 0%, #FFBFE3 100%);
        --toner: linear-gradient(135deg, #FED7E2 0%, #FECACA 100%);
        --exfoliant: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
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
</style>
@endpush

@section('content')
<div class="hero-gradient min-h-screen">
    <!-- Page Header -->
    <div class="container mx-auto px-4 py-12">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Our Products</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Discover our range of customizable skincare solutions, each designed to be personalized to your unique needs.
            </p>
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
                        <select id="categoryFilter" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button onclick="clearFilters()" class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-600">Try adjusting your search or filter criteria</p>
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
    let products = [];
    let filteredProducts = [];
    
    // Color schemes for different categories
    const categoryColors = {
        'serum': 'from-pink-100 to-pink-200',
        'moisturizer': 'from-orange-100 to-orange-200', 
        'cleanser': 'from-green-100 to-green-200',
        'mask': 'from-purple-100 to-purple-200',
        'toner': 'from-blue-100 to-blue-200',
        'exfoliant': 'from-yellow-100 to-yellow-200'
    };

    const categoryIcons = {
        'serum': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>`,
        'moisturizer': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>`,
        'cleanser': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>`,
        'default': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>`
    };

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
        
        grid.innerHTML = productsToShow.map(product => {
            const categoryKey = product.base_category.toLowerCase();
            const colorClass = categoryColors[categoryKey] || 'from-gray-100 to-gray-200';
            const iconPath = categoryIcons[categoryKey] || categoryIcons.default;
            const categoryColor = categoryKey === 'serum' ? 'pink' : 
                                categoryKey === 'moisturizer' ? 'orange' :
                                categoryKey === 'cleanser' ? 'green' :
                                categoryKey === 'mask' ? 'purple' :
                                categoryKey === 'toner' ? 'blue' : 'gray';
            
            return `
                <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden">
                    ${product.image_url ? 
                        `<img src="${escapeHtml(product.image_url)}" alt="${escapeHtml(product.product_name)}" class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                         <div class="h-64 bg-gradient-to-br ${colorClass} items-center justify-center image-placeholder" style="display: none;">
                            <div class="text-center">
                                <div class="w-20 h-20 bg-white rounded-full shadow-lg mx-auto mb-4 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-${categoryColor}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        ${iconPath}
                                    </svg>
                                </div>
                                <span class="text-${categoryColor}-600 font-medium">Base Product</span>
                            </div>
                         </div>` :
                        `<div class="h-64 bg-gradient-to-br ${colorClass} flex items-center justify-center image-placeholder">
                            <div class="text-center">
                                <div class="w-20 h-20 bg-white rounded-full shadow-lg mx-auto mb-4 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-${categoryColor}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        ${iconPath}
                                    </svg>
                                </div>
                                <span class="text-${categoryColor}-600 font-medium">Base Product</span>
                            </div>
                        </div>`
                    }
                    
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">${escapeHtml(product.product_name)}</h3>
                        <p class="text-gray-600 mb-4">
                            ${product.description ? escapeHtml(product.description) : 'A personalized ' + product.base_category.toLowerCase() + ' targeting your specific skin concerns.'}
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
                                ${ucfirst(product.product_type)} formula
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Allergy-safe ingredients
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-gray-900">From LKR ${Number(product.standard_price).toLocaleString()}</span>
                                <span class="text-sm text-gray-500">/bottle</span>
                            </div>
                            <a href="{{ route('survey.index') }}" 
                               class="bg-${categoryColor}-500 text-white px-6 py-2 rounded-full hover:bg-${categoryColor}-600 transition-colors">
                                Customize
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        // Add animations
        animateCards();
    }

    // Get image placeholder HTML
    function getImagePlaceholder(categoryKey, iconPath, colorClass, categoryColor) {
        return `
            <div class="h-64 bg-gradient-to-br ${colorClass} flex items-center justify-center image-placeholder">
                <div class="text-center">
                    <div class="w-20 h-20 bg-white rounded-full shadow-lg mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-10 h-10 text-${categoryColor}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${iconPath}
                        </svg>
                    </div>
                    <span class="text-${categoryColor}-600 font-medium">Base Product</span>
                </div>
            </div>
        `;
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
    window.clearFilters = function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoryFilter').value = '';
        filteredProducts = [...products];
        displayProducts(filteredProducts);
    };

    // Show/hide loading state
    function showLoading(show) {
        document.getElementById('loadingState').classList.toggle('hidden', !show);
        document.getElementById('productsGrid').classList.toggle('hidden', show);
    }

    // Show no results message
    function showNoResults() {
        document.getElementById('productsGrid').innerHTML = '';
        document.getElementById('noResults').classList.remove('hidden');
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
    document.getElementById('searchInput').addEventListener('input', filterProducts);
    document.getElementById('categoryFilter').addEventListener('change', filterProducts);

    // Initialize
    loadProducts();
});
</script>
@endpush