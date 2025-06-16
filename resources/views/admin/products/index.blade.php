<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Products</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚙️</text></svg>"
        type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <!-- Header -->
    <nav class="bg-white shadow-lg border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    @if(file_exists(public_path('app/views/assets/Crafted Well Logo (2).png')))
                        <img src="{{ asset('app/views/assets/Crafted Well Logo (2).png') }}" alt="Logo"
                            class="h-6 w-auto object-contain">
                    @else
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-flask text-white"></i>
                        </div>
                    @endif
                    <h1
                        class="text-xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                        Admin Dashboard</h1>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-user-circle text-gray-400 text-xl"></i>
                        <span id="userEmail" class="text-gray-600">{{ auth()->user()->email }}</span>
                    </div>
                    <button onclick="logout()"
                        class="flex items-center space-x-2 text-red-500 hover:text-red-600 transition-colors px-4 py-2 rounded-lg hover:bg-red-50">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Error Alert -->
    <div id="errorAlert" class="hidden fixed top-4 right-4 max-w-md z-50">
        <div class="bg-white border-l-4 border-red-500 shadow-lg rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm text-gray-800" id="errorMessage"></p>
                </div>
                <button class="ml-4" onclick="hideError()">
                    <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Success Alert -->
    <div id="successAlert" class="hidden fixed top-4 right-4 max-w-md z-50">
        <div class="bg-white border-l-4 border-green-500 shadow-lg rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-sm text-gray-800" id="successMessage"></p>
                </div>
                <button class="ml-4" onclick="hideSuccess()">
                    <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Products</h2>
                <p class="text-gray-500 mt-1">Manage your product catalog</p>
            </div>
            <button onclick="showAddModal()"
                class="flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg">
                <i class="fas fa-plus"></i>
                <span>Add Product</span>
            </button>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="hidden flex justify-center my-12">
            <div class="relative">
                <div class="w-12 h-12 rounded-full border-2 border-blue-200"></div>
                <div class="w-12 h-12 rounded-full border-t-2 border-blue-600 animate-spin absolute top-0"></div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Image</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="productsTableBody">
                        <!-- Products will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div id="productModal"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full backdrop-blur-sm z-50">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-xl rounded-2xl bg-white my-10">
            <div class="absolute top-4 right-4">
                <button onclick="hideModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-semibold text-gray-800 mb-6" id="modalTitle">Add Product</h3>
                <form id="productForm" class="space-y-6">
                    <input type="hidden" id="productId">

                    <!-- Image Preview Section -->
                    <div id="imagePreviewSection" class="hidden">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Image Preview</label>
                        <div class="flex items-center space-x-4">
                            <img id="imagePreview" src="" alt="Product preview"
                                class="h-20 w-20 rounded-lg object-cover shadow-sm border border-gray-200">
                            <button type="button" onclick="removeImagePreview()"
                                class="text-red-500 hover:text-red-700 text-sm">
                                Remove Image
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="product_name">
                                Product Name *
                            </label>
                            <input type="text" id="product_name" required
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="base_category">
                                Category *
                            </label>
                            <input type="text" id="base_category" required
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="product_type">
                                Product Type *
                            </label>
                            <input type="text" id="product_type" required
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="standard_price">
                                Standard Price (LKR) *
                            </label>
                            <input type="number" id="standard_price" required min="0" step="0.01"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2"
                                for="customization_price_modifier">
                                Customization Price Modifier *
                            </label>
                            <input type="number" id="customization_price_modifier" required min="0" step="0.01"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="base_formulation_id">
                                Base Formulation *
                            </label>
                            <select id="base_formulation_id" required
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Select Base Formulation</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="image_url">
                            Product Image URL
                        </label>
                        <input type="url" id="image_url" placeholder="https://example.com/image.jpg"
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            oninput="previewImage()">
                        <p class="text-xs text-gray-500 mt-1">Enter a URL for the product image (optional)</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="description">
                            Description
                        </label>
                        <textarea id="description" rows="3" placeholder="Enter product description..."
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"></textarea>
                    </div>

                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" onclick="hideModal()"
                            class="px-6 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                            Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full backdrop-blur-sm z-50">
        <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-xl rounded-2xl bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">Confirm Deletion</h3>
                <p class="text-gray-600 mb-8">Are you sure you want to delete this product? This action cannot be
                    undone.</p>
                <div class="flex justify-center space-x-4">
                    <button onclick="hideDeleteModal()"
                        class="px-6 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            // Product data from Laravel
            const productData = @json($productDetails);

            // ========================================
            // NOTIFICATION FUNCTIONS
            // ========================================

            function showError(message) {
                const errorDiv = document.getElementById('errorMessage');
                if (errorDiv) {
                    errorDiv.querySelector('span').textContent = message;
                    errorDiv.classList.remove('hidden');
                    setTimeout(() => {
                        errorDiv.classList.add('hidden');
                    }, 5000);
                } else {
                    alert('Error: ' + message);
                }
            }

            function showSuccess(message) {
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

            // ========================================
            // UNIFIED CART FUNCTIONS
            // ========================================

            function addToCart(customProductId, quantityOverride = null) {
                // Get quantity from input field if not provided
                const quantityInput = document.getElementById('quantityInput');
                const quantity = quantityOverride || (quantityInput ? parseInt(quantityInput.value) : 1);

                console.log('Adding to cart with ID:', customProductId, 'Quantity:', quantity);

                if (!customProductId || customProductId === 'undefined') {
                    console.error('Invalid product ID:', customProductId);
                    showError('Product ID is missing. Please refresh the page and try again.');
                    return Promise.reject('Invalid product ID');
                }

                const requestData = {
                    custom_product_id: customProductId,
                    quantity: quantity
                };

                console.log('Request data being sent:', requestData);

                return fetch('{{ route("cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            showSuccess(data.message);

                            // Update cart count using unified system
                            if (data.cart_count !== undefined && window.CartManager) {
                                window.CartManager.updateAllCounters(data.cart_count);
                            } else if (window.updateCartCounter) {
                                window.updateCartCounter(data.cart_count || 0);
                            }

                            return data;
                        } else {
                            showError(data.message || 'Error adding to cart');
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        showError('Network error occurred');
                        throw error;
                    });
            }

            async function buyNow(customProductId) {
                try {
                    console.log('Buy now for product:', customProductId);

                    // First add to cart
                    await addToCart(customProductId);

                    // Then redirect to checkout
                    window.location.href = '{{ route("checkout.index") }}';
                } catch (error) {
                    console.error('Error proceeding to checkout:', error);
                    showError('Failed to proceed to checkout. Please try again.');
                }
            }

            // ========================================
            // PAGE INITIALIZATION
            // ========================================

            document.addEventListener('DOMContentLoaded', function () {
                // Quantity controls
                const quantityInput = document.getElementById('quantityInput');
                const decrementBtn = document.getElementById('decrementBtn');
                const incrementBtn = document.getElementById('incrementBtn');

                if (decrementBtn) {
                    decrementBtn.addEventListener('click', () => {
                        const currentValue = parseInt(quantityInput.value);
                        if (currentValue > 1) {
                            quantityInput.value = currentValue - 1;
                        }
                    });
                }

                if (incrementBtn) {
                    incrementBtn.addEventListener('click', () => {
                        const currentValue = parseInt(quantityInput.value);
                        if (currentValue < 10) {
                            quantityInput.value = currentValue + 1;
                        }
                    });
                }

                // Initialize cart counter if CartManager is available
                if (window.CartManager) {
                    window.CartManager.init();
                }

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

                console.log('Product page initialized. Functions available:', {
                    addToCart: typeof addToCart,
                    buyNow: typeof buyNow,
                    CartManager: typeof window.CartManager
                });
            });

            // ========================================
            // IMAGE GALLERY AND ACCORDION FUNCTIONS
            // ========================================

            function toggleAccordion(sectionId) {
                const content = document.getElementById(`${sectionId}-content`);
                const button = content.previousElementSibling;
                const icon = button.querySelector('i');

                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            }

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
            document.getElementById('imageModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeImageModal();
                }
            });
        </script>
    @endpush
</body>

</html>