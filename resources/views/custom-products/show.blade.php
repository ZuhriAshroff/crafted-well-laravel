@extends('layouts.app')

@section('title', $productDetails['name'] ?? 'Custom Product')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
    .accordion-content.hidden {
        display: none;
    }
    .rotate-180 {
        transform: rotate(180deg);
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

    <!-- Error Message -->
    <div id="errorMessage" class="hidden fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50">
        <span class="block sm:inline"></span>
        <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.classList.add('hidden')">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 py-4">
        <nav class="text-sm text-gray-600">
            <a href="{{ route('dashboard') }}" class="hover:text-pink-600">Dashboard</a>
            <span class="mx-2">/</span>
            <a href="{{ route('custom-products.index') }}" class="hover:text-pink-600">My Custom Products</a>
            <span class="mx-2">/</span>
            <span class="text-gray-400">{{ $productDetails['name'] }}</span>
        </nav>
    </div>

    <!-- Product Section -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Left: Product Images -->
            <div class="relative">
                <!-- Main Image -->
                <div class="bg-white rounded-lg p-8 shadow-md mb-4">
                    <img src="{{ asset('images/serum-main.jpg') }}" alt="Personalized Serum" class="w-full" id="mainImage"
                         onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                    <button class="absolute right-12 bottom-12 bg-white rounded-full p-2 shadow-md" onclick="openImageModal()">
                        <i class="fas fa-search-plus text-gray-600"></i>
                    </button>
                </div>
                <!-- Thumbnail Images -->
                <div class="grid grid-cols-3 gap-4">
                    <img src="{{ asset('images/serum-1.jpg') }}" alt="Product view 1" 
                         class="w-full rounded-lg shadow-sm cursor-pointer thumbnail border-2 border-pink-500"
                         onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                    <img src="{{ asset('images/serum-2.jpg') }}" alt="Product view 2" 
                         class="w-full rounded-lg shadow-sm cursor-pointer thumbnail"
                         onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                    <img src="{{ asset('images/serum-3.jpg') }}" alt="Product view 3" 
                         class="w-full rounded-lg shadow-sm cursor-pointer thumbnail"
                         onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                </div>
                <!-- Navigation Arrows -->
                <button class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md" onclick="changeImage(-1)">
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </button>
                <button class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md" onclick="changeImage(1)">
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </button>
            </div>

            <!-- Right: Product Details -->
            <div>
                <div class="flex justify-between items-start mb-4">
                    <h1 class="text-3xl font-bold">{{ $productDetails['name'] }}</h1>
                    <div class="flex space-x-2">
                        <a href="{{ route('custom-products.reformulate', $customProduct->custom_product_id) }}" 
                           class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm hover:bg-blue-200 transition-colors">
                            <i class="fas fa-edit mr-1"></i> Reformulate
                        </a>
                        <a href="{{ route('custom-products.edit', $customProduct->custom_product_id) }}" 
                           class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm hover:bg-gray-200 transition-colors">
                            <i class="fas fa-cog mr-1"></i> Edit
                        </a>
                    </div>
                </div>
                
                <!-- Price -->
                <div class="mb-6">
                    @php
                        $originalPrice = $productDetails['total_price'];
                        $discountedPrice = floor($originalPrice * 0.9);
                    @endphp
                    <span class="text-gray-500 line-through">Rs. {{ number_format($originalPrice) }}</span>
                    <span class="text-2xl font-bold ml-2 text-pink-600">Rs. {{ number_format($discountedPrice) }}</span>
                    <span class="bg-pink-100 text-pink-600 px-2 py-1 rounded-full text-sm ml-2">10% OFF</span>
                </div>

                <!-- Product Details -->
                <div class="space-y-4 mb-6">
                    <div>
                        <span class="font-semibold">Personalized For:</span>
                        <span class="text-gray-600">{{ $productDetails['personalized_for'] }}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Allergy Consideration:</span>
                        <span class="text-gray-600">{{ $productDetails['allergy_consideration'] }}</span>
                    </div>
                    <div>
                        <span class="font-semibold">Formulated On:</span>
                        <span class="text-gray-600">{{ $customProduct->formulation_date->format('M d, Y') }}</span>
                    </div>
                </div>

                <!-- Solution Description -->
                <div class="mb-6">
                    <h2 class="font-semibold mb-2">Solution Description</h2>
                    <p class="text-gray-600">{{ $productDetails['solution_description'] }}</p>
                </div>

                <!-- Size and Stock -->
                <div class="flex items-center mb-6">
                    <span class="font-semibold mr-4">SIZE: {{ $productDetails['size'] }}</span>
                    <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>In Stock</span>
                </div>

                <!-- Quantity Selector -->
                <form id="addToCartForm" class="mb-6">
                    @csrf
                    <input type="hidden" name="custom_product_id" value="{{ $customProduct->custom_product_id }}">
                    <div class="flex items-center space-x-4 mb-4">
                        <label class="font-semibold">Quantity:</label>
                        <div class="flex items-center space-x-2">
                            <button type="button" class="w-8 h-8 flex items-center justify-center border rounded-full hover:bg-gray-100" id="decrementBtn">-</button>
                            <input type="number" name="quantity" value="1" min="1" max="10" class="w-16 text-center border rounded-lg" id="quantityInput">
                            <button type="button" class="w-8 h-8 flex items-center justify-center border rounded-full hover:bg-gray-100" id="incrementBtn">+</button>
                        </div>
                    </div>
                </form>

                <!-- Action Buttons -->
                <div class="space-y-4 mb-8">
                    <button class="w-full bg-pink-100 text-pink-600 py-3 rounded-full hover:bg-pink-200 transition-colors" onclick="addToCart()">
                        <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                    </button>
                    <button class="w-full bg-amber-500 text-white py-3 rounded-full hover:bg-amber-600 transition-colors" onclick="buyNow()">
                        <i class="fas fa-bolt mr-2"></i>Buy Now
                    </button>
                </div>

                <!-- Key Ingredients -->
                <div class="mt-8">
                    <h2 class="font-semibold mb-2">Key Ingredients & Benefits:</h2>
                    <ul class="list-disc list-inside text-gray-600 space-y-2">
                        @foreach($productDetails['selected_ingredients'] as $index => $ingredient)
                            <li class="mb-2">
                                <span class="font-medium">{{ ucwords(str_replace('_', ' ', $ingredient)) }}</span> 
                                ({{ number_format($productDetails['concentrations'][$ingredient] * 100, 1) }}%)
                                <span class="block text-sm ml-4 text-gray-500">{{ $productDetails['benefits'][$index] ?? '' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Additional Info -->
                <div class="mt-6 p-4 bg-pink-50 rounded-lg">
                    <div class="flex items-center text-sm text-gray-600 space-x-4">
                        <span><i class="fas fa-leaf text-green-500 mr-1"></i>Fragrance-Free</span>
                        <span><i class="fas fa-shipping-fast text-blue-500 mr-1"></i>3-5 Days Delivery</span>
                        <span><i class="fas fa-shield-alt text-purple-500 mr-1"></i>30-Day Guarantee</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accordion Sections -->
        <div class="mt-12 max-w-4xl">
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-md">
                    <button class="w-full p-4 flex justify-between items-center" onclick="toggleAccordion('overview')">
                        <span class="font-semibold">Product Overview</span>
                        <i class="fas fa-chevron-down transition-transform duration-200"></i>
                    </button>
                    <div class="accordion-content hidden" id="overview-content">
                        <div class="p-4 border-t">{{ $productDetails['solution_description'] }}</div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md">
                    <button class="w-full p-4 flex justify-between items-center" onclick="toggleAccordion('usage')">
                        <span class="font-semibold">How To Use</span>
                        <i class="fas fa-chevron-down transition-transform duration-200"></i>
                    </button>
                    <div class="accordion-content hidden" id="usage-content">
                        <div class="p-4 border-t">
                            <ol class="list-decimal list-inside space-y-2">
                                <li>Cleanse face thoroughly</li>
                                <li>Apply 2-3 drops to damp skin</li>
                                <li>Gently pat and massage until absorbed</li>
                                <li>Use morning and evening</li>
                                <li>Follow with sunscreen during day use</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md">
                    <button class="w-full p-4 flex justify-between items-center" onclick="toggleAccordion('ingredients')">
                        <span class="font-semibold">Full Ingredients</span>
                        <i class="fas fa-chevron-down transition-transform duration-200"></i>
                    </button>
                    <div class="accordion-content hidden" id="ingredients-content">
                        <div class="p-4 border-t">
                            <p class="mb-4 text-sm text-gray-600">Complete ingredients list:</p>
                            <ul class="text-sm space-y-1">
                                @foreach($productDetails['selected_ingredients'] as $ingredient)
                                    <li>{{ ucwords(str_replace('_', ' ', $ingredient)) }} ({{ number_format($productDetails['concentrations'][$ingredient] * 100, 1) }}%)</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
        <div class="relative max-w-4xl w-full mx-4">
            <img src="" alt="Zoomed product image" class="w-full" id="modalImage">
            <button class="absolute top-4 right-4 text-white text-2xl" onclick="closeImageModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Product data from Laravel - FIXED: Use $productDetails instead of undefined $product
    const productData = @json($productDetails);
    
    function showError(message) {
        const errorDiv = document.getElementById('errorMessage');
        errorDiv.querySelector('span').textContent = message;
        errorDiv.classList.remove('hidden');
        setTimeout(() => {
            errorDiv.classList.add('hidden');
        }, 5000);
    }

    function showSuccess(message) {
        // Create success notification
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
        successDiv.innerHTML = `
            ${message}
            <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        document.body.appendChild(successDiv);
        setTimeout(() => successDiv.remove(), 5000);
    }

    // Quantity controls
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantityInput');
        const decrementBtn = document.getElementById('decrementBtn');
        const incrementBtn = document.getElementById('incrementBtn');

        decrementBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });

        incrementBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue < 10) {
                quantityInput.value = currentValue + 1;
            }
        });

        // Auto-hide flash messages
        setTimeout(() => {
            document.querySelectorAll('[class*="border-green-400"], [class*="border-red-400"]').forEach(el => {
                if (el.querySelector('button')) {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                }
            });
        }, 5000);

        // Initialize thumbnail selection
        updateThumbnailSelection();
    });

    // Accordion functionality
    function toggleAccordion(sectionId) {
        const content = document.getElementById(`${sectionId}-content`);
        const button = content.previousElementSibling;
        const icon = button.querySelector('i');
        
        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    // Image gallery functionality
    let currentImageIndex = 0;
    const images = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('mainImage');

    function changeImage(direction) {
        currentImageIndex = (currentImageIndex + direction + images.length) % images.length;
        mainImage.src = images[currentImageIndex].src;
        updateThumbnailSelection();
    }

    function updateThumbnailSelection() {
        images.forEach((img, index) => {
            if (index === currentImageIndex) {
                img.classList.add('border-2', 'border-pink-500');
            } else {
                img.classList.remove('border-2', 'border-pink-500');
            }
        });
    }

    // Add click handlers to thumbnails
    images.forEach((img, index) => {
        img.addEventListener('click', () => {
            currentImageIndex = index;
            mainImage.src = img.src;
            updateThumbnailSelection();
        });
    });

    // Image modal functionality
    function openImageModal() {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = mainImage.src;
        modal.classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });

    // Cart functionality
    async function addToCart() {
        try {
            const formData = new FormData(document.getElementById('addToCartForm'));
            
            const response = await fetch('{{ route("cart.add") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();
            
            if (response.ok && result.success) {
                showSuccess('Product added to cart successfully!');
                // Update cart counter if you have one
                // updateCartCounter(result.cart_count);
            } else {
                throw new Error(result.message || 'Failed to add to cart');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            showError('Failed to add product to cart. Please try again.');
        }
    }

    async function buyNow() {
        try {
            // First add to cart
            await addToCart();
            
            // Then redirect to checkout
            window.location.href = '{{ route("checkout.index") }}';
        } catch (error) {
            console.error('Error proceeding to checkout:', error);
            showError('Failed to proceed to checkout. Please try again.');
        }
    }
</script>
@endpush