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
                        <img src="{{ asset('app/views/assets/Crafted Well Logo (2).png') }}" alt="Logo" class="h-6 w-auto object-contain">
                    @else
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-flask text-white"></i>
                        </div>
                    @endif
                    <h1 class="text-xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
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
                            <label class="block text-gray-700 text-sm font-medium mb-2" for="customization_price_modifier">
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

    <script>
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Notifications System
        const notifications = {
            showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-y-full z-50`;

                if (type === 'success') {
                    toast.classList.add('bg-green-500', 'text-white');
                } else if (type === 'error') {
                    toast.classList.add('bg-red-500', 'text-white');
                }

                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => toast.classList.remove('translate-y-full'), 100);

                setTimeout(() => {
                    toast.classList.add('translate-y-full');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            },

            showError(message) {
                const errorAlert = document.getElementById('errorAlert');
                const errorMessage = document.getElementById('errorMessage');
                errorMessage.textContent = message;
                errorAlert.classList.remove('hidden');
            },

            showSuccess(message) {
                const successAlert = document.getElementById('successAlert');
                const successMessage = document.getElementById('successMessage');
                successMessage.textContent = message;
                successAlert.classList.remove('hidden');
            }
        };

        // Hide alerts
        function hideError() {
            document.getElementById('errorAlert').classList.add('hidden');
        }

        function hideSuccess() {
            document.getElementById('successAlert').classList.add('hidden');
        }

        // Auth utilities
        const auth = {
            init() {
                // Check if user is authenticated
                const userEmail = '{{ auth()->user()->email }}';
                if (userEmail) {
                    document.getElementById('userEmail').textContent = userEmail;
                }
            },

            logout() {
                document.body.innerHTML = `
                <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-200 border-t-blue-500 mx-auto mb-4"></div>
                        <p class="text-gray-600">Logging out...</p>
                    </div>
                </div>
            `;

                fetch('{{ route("admin.logout") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                }).then(() => {
                    window.location.href = '{{ route("admin.login") }}';
                }).catch(() => {
                    window.location.href = '{{ route("admin.login") }}';
                });
            }
        };

        // Products management
        const products = {
            async load() {
                ui.showLoading(true);
                try {
                    const response = await fetch('{{ route("admin.products.data") }}', {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();

                    if (data.status === 'success') {
                        this.display(data.data);
                    } else {
                        throw new Error(data.message || 'Failed to load products');
                    }
                } catch (error) {
                    console.error('Error loading products:', error);
                    notifications.showError(error.message || 'Error loading products');
                } finally {
                    ui.showLoading(false);
                }
            },

            display(products) {
                const tbody = document.getElementById('productsTableBody');

                if (!products || products.length === 0) {
                    tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-box-open text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500 text-lg">No products found</p>
                                <p class="text-gray-400 text-sm mt-1">Add your first product to get started</p>
                            </div>
                        </td>
                    </tr>
                `;
                    return;
                }

                tbody.innerHTML = products.map(product => `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            ${product.image_url ? 
                                `<img src="${ui.escapeHtml(product.image_url)}" alt="${ui.escapeHtml(product.product_name)}" 
                                     class="h-12 w-12 rounded-lg object-cover shadow-sm border border-gray-200"
                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDgiIGhlaWdodD0iNDgiIHZpZXdCb3g9IjAgMCA0OCA0OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiByeD0iOCIgZmlsbD0iI0Y5RkFGQiIvPgo8cGF0aCBkPSJNMjQgMzJMMTYgMjBIMzJMMjQgMzJaIiBmaWxsPSIjRTVFN0VCIi8+CjxjaXJjbGUgY3g9IjIwIiBjeT0iMTYiIHI9IjIiIGZpbGw9IiNFNUU3RUIiLz4KPC9zdmc+'; this.classList.add('opacity-50');">` :
                                `<div class="h-12 w-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>`
                            }
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${ui.escapeHtml(product.product_name)}</div>
                        ${product.description ? 
                            `<div class="text-xs text-gray-500 mt-1 max-w-xs truncate">${ui.escapeHtml(product.description)}</div>` : 
                            ''
                        }
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 text-sm text-blue-600 bg-blue-100 rounded-full">
                            ${ui.escapeHtml(product.base_category)}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 text-sm text-purple-600 bg-purple-100 rounded-full">
                            ${ui.escapeHtml(product.product_type)}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">LKR ${parseFloat(product.standard_price).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                        <button onclick='products.edit(${JSON.stringify(product)})' 
                            class="text-blue-600 hover:text-blue-900 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="products.showDeleteModal('${ui.escapeHtml(product.product_id)}')" 
                            class="text-red-600 hover:text-red-900 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            },

            async loadFormulations() {
                try {
                    const response = await fetch('{{ route("admin.products.options") }}', {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    console.log('Formulations response:', data); // Debug log

                    if (data.status === 'success') {
                        const select = document.getElementById('base_formulation_id');
                        select.innerHTML = '<option value="">Select Base Formulation</option>';
                        
                        if (data.data.base_formulations && data.data.base_formulations.length > 0) {
                            data.data.base_formulations.forEach(formulation => {
                                const option = document.createElement('option');
                                option.value = formulation.base_formulation_id;
                                option.textContent = formulation.base_name;
                                if (formulation.description) {
                                    option.title = formulation.description; // Show description on hover
                                }
                                select.appendChild(option);
                            });
                            console.log(`Loaded ${data.data.base_formulations.length} formulations`);
                        } else {
                            console.warn('No base formulations found');
                            select.innerHTML = '<option value="">No base formulations available</option>';
                        }
                    } else {
                        throw new Error(data.message || 'Failed to load formulations');
                    }
                } catch (error) {
                    console.error('Error loading formulations:', error);
                    const select = document.getElementById('base_formulation_id');
                    select.innerHTML = '<option value="">Error loading formulations</option>';
                    notifications.showError('Failed to load base formulations: ' + error.message);
                }
            },

            showAddModal() {
                document.getElementById('modalTitle').textContent = 'Add Product';
                document.getElementById('productId').value = '';
                document.getElementById('productForm').reset();
                this.loadFormulations();
                document.getElementById('productModal').classList.remove('hidden');
                document.getElementById('product_name').focus();
            },

            edit(product) {
                try {
                    document.getElementById('modalTitle').textContent = 'Edit Product';
                    document.getElementById('productId').value = product.product_id;
                    document.getElementById('product_name').value = product.product_name;
                    document.getElementById('base_category').value = product.base_category;
                    document.getElementById('product_type').value = product.product_type;
                    document.getElementById('standard_price').value = product.standard_price;
                    document.getElementById('customization_price_modifier').value = product.customization_price_modifier;
                    document.getElementById('image_url').value = product.image_url || '';
                    document.getElementById('description').value = product.description || '';
                    
                    this.loadFormulations().then(() => {
                        document.getElementById('base_formulation_id').value = product.base_formulation_id;
                    });
                    
                    document.getElementById('productModal').classList.remove('hidden');
                    document.getElementById('product_name').focus();
                } catch (error) {
                    console.error('Error editing product:', error);
                    notifications.showError('Failed to load product details');
                }
            },

            hideModal() {
                document.getElementById('productModal').classList.add('hidden');
                document.getElementById('productForm').reset();
                document.getElementById('imagePreviewSection').classList.add('hidden');
            },

            showDeleteModal(productId) {
                window.deleteProductId = productId;
                document.getElementById('deleteModal').classList.remove('hidden');
            },

            hideDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
                window.deleteProductId = null;
            },

            async delete(id) {
                if (!id) return;

                ui.showLoading(true);
                try {
                    const response = await fetch(`{{ url('admin/products') }}/${id}/delete`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (data.status === 'success') {
                        this.hideDeleteModal();
                        notifications.showSuccess(data.message);
                        this.load();
                    } else {
                        throw new Error(data.message || 'Failed to delete product');
                    }
                } catch (error) {
                    console.error('Error deleting product:', error);
                    notifications.showError(error.message || 'Failed to delete product');
                } finally {
                    ui.showLoading(false);
                }
            }
        };

        // UI utilities
        const ui = {
            addLoadingState(button) {
                const originalContent = button.innerHTML;
                button.disabled = true;
                button.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            `;
                return originalContent;
            },

            removeLoadingState(button, originalContent) {
                button.disabled = false;
                button.innerHTML = originalContent;
            },

            showLoading(show) {
                document.getElementById('loadingSpinner').classList.toggle('hidden', !show);
            },

            escapeHtml(unsafe) {
                return unsafe
                    .toString()
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize authentication
            auth.init();

            // Form submission handler
            document.getElementById('productForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitButton = e.target.querySelector('button[type="submit"]');
                const originalContent = ui.addLoadingState(submitButton);

                try {
                    const productId = document.getElementById('productId').value;
                    const formData = {
                        product_name: document.getElementById('product_name').value.trim(),
                        base_category: document.getElementById('base_category').value.trim(),
                        product_type: document.getElementById('product_type').value.trim(),
                        standard_price: Number(document.getElementById('standard_price').value),
                        customization_price_modifier: Number(document.getElementById('customization_price_modifier').value),
                        base_formulation_id: document.getElementById('base_formulation_id').value,
                        image_url: document.getElementById('image_url').value.trim(),
                        description: document.getElementById('description').value.trim()
                    };

                    if (!formData.product_name || !formData.base_category || !formData.product_type || !formData.base_formulation_id) {
                        throw new Error('Please fill in all required fields');
                    }

                    const url = productId ? `{{ url('admin/products') }}/${productId}/update` : '{{ route("admin.products.store") }}';
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();
                    if (data.status === 'success') {
                        products.hideModal();
                        notifications.showSuccess(data.message);
                        products.load();
                    } else {
                        throw new Error(data.message || 'Operation failed');
                    }
                } catch (error) {
                    console.error('Error saving product:', error);
                    notifications.showError(error.message || 'Failed to save product');
                } finally {
                    ui.removeLoadingState(submitButton, originalContent);
                }
            });

            // Initial data load
            products.load();
        });

        // Make functions available globally
        window.showAddModal = products.showAddModal.bind(products);
        window.hideModal = products.hideModal.bind(products);
        window.editProduct = products.edit.bind(products);
        window.showDeleteModal = products.showDeleteModal.bind(products);
        window.hideDeleteModal = products.hideDeleteModal.bind(products);
        window.confirmDelete = () => products.delete(window.deleteProductId);
        window.logout = auth.logout.bind(auth);
        
        // Image preview functions
        window.previewImage = function() {
            const imageUrl = document.getElementById('image_url').value;
            const previewSection = document.getElementById('imagePreviewSection');
            const previewImg = document.getElementById('imagePreview');
            
            if (imageUrl && imageUrl.trim() !== '') {
                previewImg.src = imageUrl;
                previewSection.classList.remove('hidden');
                
                // Handle image load errors
                previewImg.onerror = function() {
                    previewSection.classList.add('hidden');
                };
            } else {
                previewSection.classList.add('hidden');
            }
        };
        
        window.removeImagePreview = function() {
            document.getElementById('image_url').value = '';
            document.getElementById('imagePreviewSection').classList.add('hidden');
        };
    </script>
</body>

</html>