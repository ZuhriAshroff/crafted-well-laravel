<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/Crafted Well Logo (2).png') }}"
                            class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        {{ __('Products') }}
                    </x-nav-link>
                    @auth
                        @if(auth()->user()->role !== 'admin')
                            <x-nav-link :href="route('survey.index')" :active="request()->routeIs('survey.*')">
                                {{ __('Skin Survey') }}
                            </x-nav-link>
                        @endif
                    @else
                        <x-nav-link :href="route('survey.index')" :active="request()->routeIs('survey.*')">
                            {{ __('Skin Survey') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                @auth
                    <!-- Cart Icon (only for non-admin users) - FIXED -->
                    @if(auth()->user()->role !== 'admin')
                        <a href="{{ route('cart.index') }}"
                            class="relative p-2 text-gray-600 hover:text-pink-600 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 5.5M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-8m-10 0V9a2 2 0 012-2h6a2 2 0 012 2v4.01">
                                </path>
                            </svg>
                            <!-- Cart Counter - IMPROVED -->
                            <span id="cartCounter"
                                class="cart-count absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full min-w-[20px] h-5 flex items-center justify-center font-medium px-1"
                                style="display: {{ ((session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0) + (session('ready_products_cart') ? array_sum(array_column(session('ready_products_cart'), 'quantity')) : 0)) > 0 ? 'flex' : 'none' }}">
                                {{ (session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0) + (session('ready_products_cart') ? array_sum(array_column(session('ready_products_cart'), 'quantity')) : 0) }}
                            </span>
                        </a>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button
                                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                                        alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}
                                        @if(auth()->user()->role === 'admin')
                                            <span
                                                class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                Admin
                                            </span>
                                        @endif

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Role-based Dashboard Link -->
                            @if(auth()->user()->role === 'admin')
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    <div class="flex items-center">
                                        <!-- <i class="fas fa-crown w-4 h-4 mr-2 text-purple-500"></i> -->
                                        {{ __('Admin Dashboard') }}
                                    </div>
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('admin.custom-products.index')">
                                    <div class="flex items-center">
                                        <!-- <i class="fas fa-flask w-4 h-4 mr-2 text-pink-500"></i> -->
                                        {{ __('Manage Custom Products') }}
                                    </div>
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('admin.custom-products.analytics')">
                                    <div class="flex items-center">
                                        <!-- <i class="fas fa-chart-bar w-4 h-4 mr-2 text-blue-500"></i> -->
                                        {{ __('Analytics') }}
                                    </div>
                                </x-dropdown-link>
                            @else
                                <x-dropdown-link :href="route('user.dashboard')">
                                    <div class="flex items-center">
                                        <!-- <i class="fas fa-tachometer-alt w-4 h-4 mr-2 text-gray-400"></i> -->
                                        {{ __('Dashboard') }}
                                    </div>
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('custom-products.index')">
                                    <div class="flex items-center">
                                        <!-- <i class="fas fa-flask w-4 h-4 mr-2 text-pink-500"></i> -->
                                        {{ __('My Custom Products') }}
                                    </div>
                                </x-dropdown-link>

                                <!-- Cart Link in Dropdown for Easy Access - FIXED -->
                                <x-dropdown-link :href="route('cart.index')">
                                    <div class="flex items-center">
                                        <i class="fas fa-shopping-cart w-4 h-4 mr-2 text-pink-500"></i>
                                        {{ __('Shopping Cart') }}
                                        @php
                                            $totalCartCount = (session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0) + (session('ready_products_cart') ? array_sum(array_column(session('ready_products_cart'), 'quantity')) : 0);
                                        @endphp
                                        @if($totalCartCount > 0)
                                            <span
                                                class="cart-count ml-auto bg-pink-100 text-pink-600 text-xs px-2 py-0.5 rounded-full min-w-[20px] flex items-center justify-center">
                                                {{ $totalCartCount }}
                                            </span>
                                        @endif
                                    </div>
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link :href="route('profile.show')">
                                <div class="flex items-center">
                                    <!-- <i class="fas fa-user w-4 h-4 mr-2 text-gray-400"></i> -->
                                    {{ __('Profile') }}
                                </div>
                            </x-dropdown-link>

                            <div class="border-t border-gray-200"></div>

                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link :href="route('logout')" @click.prevent="$root.submit();">
                                    <div class="flex items-center">
                                        <i class="fas fa-sign-out-alt w-4 h-4 mr-2 text-gray-400"></i>
                                        {{ __('Log Out') }}
                                    </div>
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900 transition">Log
                            in</a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-pink-500 to-orange-500 hover:from-pink-600 hover:to-orange-600 transition duration-150 ease-in-out shadow-sm">Register</a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                {{ __('Products') }}
            </x-responsive-nav-link>
            @auth
                @if(auth()->user()->role !== 'admin')
                    <x-responsive-nav-link :href="route('survey.index')" :active="request()->routeIs('survey.*')">
                        {{ __('Skin Survey') }}
                    </x-responsive-nav-link>
                    <!-- Mobile Cart Link - FIXED -->
                    <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center">
                                <i class="fas fa-shopping-cart w-4 h-4 mr-2 text-pink-500"></i>
                                {{ __('Shopping Cart') }}
                            </span>
                            @php
                                $mobileCartCount = (session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0) + (session('ready_products_cart') ? array_sum(array_column(session('ready_products_cart'), 'quantity')) : 0);
                            @endphp
                            @if($mobileCartCount > 0)
                                <span
                                    class="cart-count bg-pink-100 text-pink-600 text-xs px-2 py-0.5 rounded-full min-w-[20px] flex items-center justify-center">
                                    {{ $mobileCartCount }}
                                </span>
                            @endif
                        </div>
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('survey.index')" :active="request()->routeIs('survey.*')">
                    {{ __('Skin Survey') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="shrink-0 mr-3">
                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                                alt="{{ Auth::user()->name }}" />
                        </div>
                    @endif

                    <div>
                        <div class="font-medium text-base text-gray-800 flex items-center">
                            {{ Auth::user()->name }}
                            @if(auth()->user()->role === 'admin')
                                <span
                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    Admin
                                </span>
                            @endif
                        </div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <!-- Role-based Dashboard Link -->
                    @if(auth()->user()->role === 'admin')
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            <div class="flex items-center">
                                <i class="fas fa-crown w-4 h-4 mr-2 text-purple-500"></i>
                                {{ __('Admin Dashboard') }}
                            </div>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('admin.custom-products.index')"
                            :active="request()->routeIs('admin.custom-products.*')">
                            <div class="flex items-center">
                                <i class="fas fa-flask w-4 h-4 mr-2 text-pink-500"></i>
                                {{ __('Manage Custom Products') }}
                            </div>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('admin.custom-products.analytics')"
                            :active="request()->routeIs('admin.custom-products.analytics')">
                            <div class="flex items-center">
                                <i class="fas fa-chart-bar w-4 h-4 mr-2 text-blue-500"></i>
                                {{ __('Analytics') }}
                            </div>
                        </x-responsive-nav-link>
                    @else
                        <x-responsive-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')">
                            <div class="flex items-center">
                                <i class="fas fa-tachometer-alt w-4 h-4 mr-2 text-gray-400"></i>
                                {{ __('Dashboard') }}
                            </div>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('custom-products.index')"
                            :active="request()->routeIs('custom-products.*')">
                            <div class="flex items-center">
                                <i class="fas fa-flask w-4 h-4 mr-2 text-pink-500"></i>
                                {{ __('My Custom Products') }}
                            </div>
                        </x-responsive-nav-link>
                    @endif

                    <x-responsive-nav-link :href="route('profile.show')" :active="request()->routeIs('profile.show')">
                        <div class="flex items-center">
                            <i class="fas fa-user w-4 h-4 mr-2 text-gray-400"></i>
                            {{ __('Profile') }}
                        </div>
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" @click.prevent="$root.submit();">
                            <div class="flex items-center">
                                <i class="fas fa-sign-out-alt w-4 h-4 mr-2 text-gray-400"></i>
                                {{ __('Log Out') }}
                            </div>
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Log in') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endauth
    </div>
</nav>

<!-- Cart Counter CSS - ADD THIS STYLE SECTION -->
<style>
    /* Cart counter improvements */
    .cart-count {
        /* Ensure proper centering */
        display: flex !important;
        align-items: center;
        justify-content: center;

        /* Prevent text from wrapping */
        white-space: nowrap;

        /* Smooth transitions */
        transition: all 0.2s ease;

        /* Ensure minimum height */
        min-height: 20px;

        /* Better font sizing */
        font-size: 11px;
        font-weight: 600;
        line-height: 1;
    }

    /* Specific styling for main cart counter */
    #cartCounter {
        /* Positioning */
        position: absolute;
        top: -8px;
        right: -8px;

        /* Sizing constraints */
        min-width: 20px;
        height: 20px;
        max-width: 32px;

        /* Border radius for proper circle/pill shape */
        border-radius: 10px;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .cart-count {
            font-size: 10px;
            min-width: 18px;
            height: 18px;
            min-height: 18px;
        }

        #cartCounter {
            top: -6px;
            right: -6px;
            min-width: 18px;
            height: 18px;
            max-width: 28px;
        }
    }

    /* Animation for count changes */
    .cart-count.updating {
        animation: cartBounce 0.3s ease;
    }

    @keyframes cartBounce {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
        }
    }
</style>

<script>
    // ========================================
    // ENHANCED GLOBAL CART MANAGEMENT SYSTEM
    // ========================================

    window.CartManager = {
        // Get current cart count from server (combines both cart types)
        getCurrentCount: function () {
            const customCartCount = {{ session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0 }};
            const readyCartCount = {{ session('ready_products_cart') ? array_sum(array_column(session('ready_products_cart'), 'quantity')) : 0 }};
            return customCartCount + readyCartCount;
        },

        // Fetch cart count from server via API
        fetchCartCount: async function () {
            try {
                const response = await fetch('{{ route("cart.summary") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        return data.cart_count || 0;
                    }
                }
            } catch (error) {
                console.warn('Failed to fetch cart count from server:', error);
            }

            // Fallback to current count
            return this.getCurrentCount();
        },

        // Update all cart counters on page - IMPROVED
        updateAllCounters: function (count) {
            console.log('Updating all cart counters to:', count);

            // Update main navigation counter with improved sizing
            const cartCounter = document.getElementById('cartCounter');
            if (cartCounter) {
                const displayCount = count > 99 ? '99+' : count.toString();
                cartCounter.textContent = displayCount;

                // Dynamic sizing based on count
                if (count > 0) {
                    cartCounter.style.display = 'flex';
                    cartCounter.classList.remove('hidden');

                    // Adjust size based on count
                    if (count < 10) {
                        // Single digit: small circle
                        cartCounter.style.minWidth = '20px';
                        cartCounter.style.paddingLeft = '0';
                        cartCounter.style.paddingRight = '0';
                        cartCounter.style.borderRadius = '50%';
                    } else if (count < 100) {
                        // Double digit: wider pill
                        cartCounter.style.minWidth = '24px';
                        cartCounter.style.paddingLeft = '4px';
                        cartCounter.style.paddingRight = '4px';
                        cartCounter.style.borderRadius = '10px';
                    } else {
                        // 100+: show "99+" and make it wider
                        cartCounter.style.minWidth = '28px';
                        cartCounter.style.paddingLeft = '4px';
                        cartCounter.style.paddingRight = '4px';
                        cartCounter.style.borderRadius = '10px';
                    }
                } else {
                    cartCounter.style.display = 'none';
                    cartCounter.classList.add('hidden');
                }
            }

            // Update all other elements with cart-count class
            const cartCounts = document.querySelectorAll('.cart-count:not(#cartCounter)');
            cartCounts.forEach(element => {
                const displayCount = count > 99 ? '99+' : count.toString();
                element.textContent = displayCount;

                if (count > 0) {
                    element.classList.remove('hidden');
                    element.style.display = 'flex';

                    // Apply responsive sizing to other counters too
                    if (count < 10) {
                        element.style.minWidth = '20px';
                        element.style.paddingLeft = '2px';
                        element.style.paddingRight = '2px';
                    } else {
                        element.style.minWidth = '24px';
                        element.style.paddingLeft = '6px';
                        element.style.paddingRight = '6px';
                    }
                } else {
                    element.classList.add('hidden');
                    element.style.display = 'none';
                }
            });

            // Store in localStorage for persistence across pages
            localStorage.setItem('cartCount', count);

            // Trigger custom event for other scripts to listen to
            window.dispatchEvent(new CustomEvent('cartUpdated', {
                detail: { count: count }
            }));

            // Update page title if needed
            this.updatePageTitle(count);
        },

        // Update browser tab title with cart count
        updatePageTitle: function (count) {
            const baseTitle = document.title.replace(/ \(\d+\)$/, ''); // Remove existing count
            if (count > 0) {
                document.title = `${baseTitle} (${count})`;
            } else {
                document.title = baseTitle;
            }
        },

        // Sync cart count with server periodically
        syncWithServer: async function () {
            const serverCount = await this.fetchCartCount();
            const localCount = parseInt(localStorage.getItem('cartCount') || '0');

            // If server count differs from local, update
            if (serverCount !== localCount) {
                this.updateAllCounters(serverCount);
            }
        },

        // Initialize cart counter on page load
        init: function () {
            // Get stored count or current server count
            const storedCount = localStorage.getItem('cartCount');
            const serverCount = this.getCurrentCount();

            // Use server count if available, otherwise use stored count
            const initialCount = serverCount !== null ? serverCount : (storedCount ? parseInt(storedCount) : 0);

            this.updateAllCounters(initialCount);

            console.log('CartManager initialized with count:', initialCount);

            // Sync with server every 30 seconds if user is authenticated
            if (document.querySelector('meta[name="user-authenticated"]')) {
                setInterval(() => {
                    this.syncWithServer();
                }, 30000);
            }
        },

        // Add item to cart and update counter
        addToCart: async function (productId, quantity = 1, type = 'ready_product') {
            try {
                const endpoint = type === 'custom_product' ?
                    '{{ route("cart.add-custom") }}' :
                    '{{ route("cart.add-ready") }}';

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        [type === 'custom_product' ? 'custom_product_id' : 'product_id']: productId,
                        quantity: quantity
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    this.updateAllCounters(data.cart_count);
                    return { success: true, data: data };
                } else {
                    throw new Error(data.message || 'Failed to add to cart');
                }

            } catch (error) {
                console.error('Error in CartManager.addToCart:', error);
                return { success: false, error: error.message };
            }
        }
    };

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function () {
        window.CartManager.init();

        // Listen for page visibility changes to sync cart
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden && window.CartManager) {
                window.CartManager.syncWithServer();
            }
        });
    });

    // Make updateCartCounter globally available for backward compatibility
    window.updateCartCounter = function (count) {
        window.CartManager.updateAllCounters(count);
    };

    // Enhanced cart operations
    window.removeFromCart = async function (itemId, type = 'custom_product') {
        try {
            const response = await fetch(`{{ url('/cart/remove') }}/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: type })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    window.CartManager.updateAllCounters(data.cart_count);
                    return { success: true, data: data };
                }
            }

            throw new Error('Failed to remove item from cart');

        } catch (error) {
            console.error('Error removing from cart:', error);
            return { success: false, error: error.message };
        }
    };

    window.updateCartQuantity = async function (itemId, quantity, type = 'custom_product') {
        try {
            const response = await fetch(`{{ url('/cart/update') }}/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    quantity: quantity,
                    type: type
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    window.CartManager.updateAllCounters(data.cart_count);
                    return { success: true, data: data };
                }
            }

            throw new Error('Failed to update cart quantity');

        } catch (error) {
            console.error('Error updating cart quantity:', error);
            return { success: false, error: error.message };
        }
    };

    // Show cart notifications
    window.showCartNotification = function (message, type = 'success') {
        const notificationDiv = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
        const icon = type === 'success' ?
            `<svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
        </svg>` :
            `<svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>`;

        notificationDiv.className = `fixed top-4 right-4 ${bgColor} px-6 py-4 rounded-lg z-50 shadow-lg border`;
        notificationDiv.innerHTML = `
        <div class="flex items-center">
            ${icon}
            <div>
                <p class="font-medium">${type === 'success' ? 'Success!' : 'Error!'}</p>
                <p class="text-sm">${message}</p>
            </div>
            <button class="ml-4" onclick="this.parentElement.parentElement.remove()">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    `;

        document.body.appendChild(notificationDiv);

        // Auto-remove after 4 seconds
        setTimeout(() => {
            if (notificationDiv && notificationDiv.parentNode) {
                notificationDiv.remove();
            }
        }, 4000);
    };
</script>