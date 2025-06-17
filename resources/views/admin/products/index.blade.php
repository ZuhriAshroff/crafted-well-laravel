@extends('layouts.app')@section('title', 'Admin Dashboard - Products')@push('styles')
    <style>
        /* Admin-specific styles */
        .admin-nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .admin-card {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .admin-table th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .admin-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .admin-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
    </style>
@endpush

@section('content')
    <div class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">


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
                    <h2 class="text-3xl font-bold text-gray-800">Products Management</h2>
                    <p class="text-gray-500 mt-1">Manage your product catalog</p>
                </div>
                <button onclick="showAddModal()"
                    class="admin-button text-white px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>Add Product
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="admin-card bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-box text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Products</p>
                            <p class="text-2xl font-bold text-gray-800" id="totalProducts">0</p>
                        </div>
                    </div>
                </div>

                <div class="admin-card bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Active Products</p>
                            <p class="text-2xl font-bold text-gray-800" id="activeProducts">0</p>
                        </div>
                    </div>
                </div>

                <div class="admin-card bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-star text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Bestsellers</p>
                            <p class="text-2xl font-bold text-gray-800" id="bestsellerProducts">0</p>
                        </div>
                    </div>
                </div>

                <div class="admin-card bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-dollar-sign text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Avg Price</p>
                            <p class="text-2xl font-bold text-gray-800" id="averagePrice">LKR 0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="hidden flex justify-center my-12">
                <div class="relative">
                    <div class="w-12 h-12 rounded-full border-2 border-purple-200"></div>
                    <div class="w-12 h-12 rounded-full border-t-2 border-purple-600 animate-spin absolute top-0"></div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="admin-card bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="admin-table">
                            <tr>
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
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
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
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="base_category">
                                    Category *
                                </label>
                                <input type="text" id="base_category" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="product_type">
                                    Product Type *
                                </label>
                                <input type="text" id="product_type" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="standard_price">
                                    Standard Price (LKR) *
                                </label>
                                <input type="number" id="standard_price" required min="0" step="0.01"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2"
                                    for="customization_price_modifier">
                                    Customization Price Modifier *
                                </label>
                                <input type="number" id="customization_price_modifier" required min="0" step="0.01"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2" for="base_formulation_id">
                                    Base Formulation *
                                </label>
                                <select id="base_formulation_id" required
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                                    <option value="">Select Base Formulation</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="image_url">
                                Product Image URL
                            </label>
                            <input type="url" id="image_url" placeholder="https://example.com/image.jpg"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                oninput="previewImage()">
                            <p class="text-xs text-gray-500 mt-1">Enter a URL for the product image (optional)</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="description">
                                Description
                            </label>
                            <textarea id="description" rows="3" placeholder="Enter product description..."
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 resize-none"></textarea>
                        </div>

                        <div class="flex justify-end space-x-4 mt-8">
                            <button type="button" onclick="hideModal()"
                                class="px-6 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="admin-button text-white px-6 py-2 rounded-lg transition-all duration-200">
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
    </div>
@endsection

@push('scripts')
    <script>
        // Global variables
        let products = [];
        let deleteProductId = null;

        // ========================================
        // NOTIFICATION FUNCTIONS
        // ========================================

        function showError(message) {
            const errorDiv = document.getElementById('errorAlert');
            const errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                errorMessage.textContent = message;
                errorDiv.classList.remove('hidden');
                setTimeout(() => {
                    errorDiv.classList.add('hidden');
                }, 5000);
            }
        }

        function showSuccess(message) {
            const successDiv = document.getElementById('successAlert');
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.textContent = message;
                successDiv.classList.remove('hidden');
                setTimeout(() => {
                    successDiv.classList.add('hidden');
                }, 5000);
            }
        }

        function hideError() {
            document.getElementById('errorAlert').classList.add('hidden');
        }

        function hideSuccess() {
            document.getElementById('successAlert').classList.add('hidden');
        }

        // ========================================
        // PRODUCTS MANAGEMENT
        // ========================================

        async function loadProducts() {
            showLoading(true);
            try {
                const response = await fetch('/admin/products/data');
                const data = await response.json();

                if (data.success) {
                    products = data.products;
                    updateStats(data.stats);
                    displayProducts();
                } else {
                    throw new Error('Failed to load products');
                }
            } catch (error) {
                console.error('Error loading products:', error);
                showError('Failed to load products. Please try again.');
            } finally {
                showLoading(false);
            }
        }

        function updateStats(stats) {
            document.getElementById('totalProducts').textContent = stats.total || 0;
            document.getElementById('activeProducts').textContent = stats.active || 0;
            document.getElementById('bestsellerProducts').textContent = stats.bestsellers || 0;
            document.getElementById('averagePrice').textContent = `LKR ${(stats.average_price || 0).toLocaleString()}`;
        }

        function displayProducts() {
            const tbody = document.getElementById('productsTableBody');

            if (products.length === 0) {
                tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-lg">No products found.</p>
                                            <button onclick="showAddModal()" 
                                                   class="mt-4 admin-button text-white px-4 py-2 rounded-lg transition-all">
                                                <i class="fas fa-plus mr-2"></i>Add Your First Product
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                return;
            }

            tbody.innerHTML = products.map(product => `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    ${product.image_url ?
                    `<img src="${product.image_url}" alt="${product.product_name}" 
                                             class="h-12 w-12 rounded-lg object-cover border border-gray-200"
                                             onerror="this.src='{{ asset('images/placeholders/placeholder1.png') }}'">` :
                    `<div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                         </div>`
                }
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">${product.product_name}</div>
                                    <div class="text-sm text-gray-500">ID: ${product.product_id}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        ${product.base_category}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${product.product_type}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    LKR ${parseFloat(product.standard_price).toLocaleString()}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-1">
                                        ${product.is_active ?
                    `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>` :
                    `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>`
                }
                                        ${product.is_bestseller ?
                    `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-star text-xs mr-1"></i>Bestseller
                                             </span>` : ''
                }
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <button onclick="viewProduct(${product.product_id})" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editProduct(${product.product_id})" 
                                               class="text-green-600 hover:text-green-900 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="showDeleteModal(${product.product_id})" 
                                               class="text-red-600 hover:text-red-900 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
        }

        function showLoading(show) {
            const spinner = document.getElementById('loadingSpinner');
            const table = document.querySelector('.admin-card');

            if (show) {
                spinner.classList.remove('hidden');
                table.style.opacity = '0.5';
            } else {
                spinner.classList.add('hidden');
                table.style.opacity = '1';
            }
        }

        // ========================================
        // MODAL FUNCTIONS
        // ========================================

        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Product';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('productModal').classList.remove('hidden');
            loadFormulationOptions();
        }

        function hideModal() {
            document.getElementById('productModal').classList.add('hidden');
        }

        function showDeleteModal(productId) {
            deleteProductId = productId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            deleteProductId = null;
            document.getElementById('deleteModal').classList.add('hidden');
        }

        async function confirmDelete() {
            if (!deleteProductId) return;

            try {
                const response = await fetch(`/admin/products/${deleteProductId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showSuccess('Product deleted successfully');
                    hideDeleteModal();
                    loadProducts();
                } else {
                    showError(data.message || 'Failed to delete product');
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                showError('Network error occurred');
            }
        }

        // ========================================
        // FORM FUNCTIONS
        // ========================================

        async function loadFormulationOptions() {
            try {
                const response = await fetch('/admin/products/options');
                const data = await response.json();

                const select = document.getElementById('base_formulation_id');
                select.innerHTML = '<option value="">Select Base Formulation</option>';

                if (data.formulations) {
                    data.formulations.forEach(formulation => {
                        select.innerHTML += `<option value="${formulation.id}">${formulation.name}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading formulation options:', error);
            }
        }

        function previewImage() {
            const url = document.getElementById('image_url').value;
            const preview = document.getElementById('imagePreview');
            const section = document.getElementById('imagePreviewSection');

            if (url) {
                preview.src = url;
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
            }
        }

        function removeImagePreview() {
            document.getElementById('image_url').value = '';
            document.getElementById('imagePreviewSection').classList.add('hidden');
        }

        // ========================================
        // EVENT HANDLERS
        // ========================================

        document.getElementById('productForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = {
                product_name: document.getElementById('product_name').value,
                base_category: document.getElementById('base_category').value,
                product_type: document.getElementById('product_type').value,
                standard_price: document.getElementById('standard_price').value,
                customization_price_modifier: document.getElementById('customization_price_modifier').value,
                base_formulation_id: document.getElementById('base_formulation_id').value,
                image_url: document.getElementById('image_url').value,
                description: document.getElementById('description').value
            };

            const productId = document.getElementById('productId').value;
            const url = productId ? `/admin/products/${productId}` : '/admin/products';
            const method = productId ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    showSuccess(productId ? 'Product updated successfully' : 'Product created successfully');
                    hideModal();
                    loadProducts();
                } else {
                    showError(data.message || 'Failed to save product');
                }
            } catch (error) {
                console.error('Error saving product:', error);
                showError('Network error occurred');
            }
        });

        function viewProduct(productId) {
            window.location.href = `/admin/products/${productId}`;
        }

        function editProduct(productId) {
            const product = products.find(p => p.product_id === productId);
            if (!product) return;

            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('productId').value = product.product_id;
            document.getElementById('product_name').value = product.product_name;
            document.getElementById('base_category').value = product.base_category;
            document.getElementById('product_type').value = product.product_type;
            document.getElementById('standard_price').value = product.standard_price;
            document.getElementById('customization_price_modifier').value = product.customization_price_modifier;
            document.getElementById('base_formulation_id').value = product.base_formulation_id;
            document.getElementById('image_url').value = product.image_url || '';
            document.getElementById('description').value = product.description || '';

            if (product.image_url) {
                previewImage();
            }

            document.getElementById('productModal').classList.remove('hidden');
            loadFormulationOptions();
        }

        // ========================================
        // ADMIN LOGOUT FUNCTION
        // ========================================

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                document.querySelector('form[action*="logout"]').submit();
            }
        }

        // ========================================
        // INITIALIZATION
        // ========================================

        document.addEventListener('DOMContentLoaded', function () {
            loadProducts();

            // Auto-hide alerts
            setTimeout(() => {
                hideError();
                hideSuccess();
            }, 5000);
        });
    </script>
@endpush