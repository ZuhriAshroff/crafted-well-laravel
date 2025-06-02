<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/Crafted Well Logo (2).png') }}"  class="block h-9 w-auto fill-current text-gray-800" />
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
                    <!-- Cart Icon (only for non-admin users) -->
                    @if(auth()->user()->role !== 'admin')
                        <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-pink-600 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 5.5M7 13v8a2 2 0 002 2h10a2 2 0 002-2v-8m-10 0V9a2 2 0 012-2h6a2 2 0 012 2v4.01"></path>
                            </svg>
                            <!-- Cart Counter -->
                            <span id="cartCounter" class="absolute -top-1 -right-1 bg-pink-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                {{ session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0 }}
                            </span>
                        </a>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}
                                        @if(auth()->user()->role === 'admin')
                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                Admin
                                            </span>
                                        @endif

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
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

                                <!-- Cart Link in Dropdown for Easy Access -->
                                <x-dropdown-link :href="route('cart.index')">
                                    <div class="flex items-center">
                                        <i class="fas fa-shopping-cart w-4 h-4 mr-2 text-pink-500"></i>
                                        {{ __('Shopping Cart') }}
                                        @if(session('cart') && count(session('cart')) > 0)
                                            <span class="ml-auto bg-pink-100 text-pink-600 text-xs px-2 py-0.5 rounded-full">
                                                {{ array_sum(array_column(session('cart'), 'quantity')) }}
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
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900 transition">Log in</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-pink-500 to-orange-500 hover:from-pink-600 hover:to-orange-600 transition duration-150 ease-in-out shadow-sm">Register</a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                    <!-- Mobile Cart Link -->
                    <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center">
                                <i class="fas fa-shopping-cart w-4 h-4 mr-2 text-pink-500"></i>
                                {{ __('Shopping Cart') }}
                            </span>
                            @if(session('cart') && count(session('cart')) > 0)
                                <span class="bg-pink-100 text-pink-600 text-xs px-2 py-0.5 rounded-full">
                                    {{ array_sum(array_column(session('cart'), 'quantity')) }}
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
                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        </div>
                    @endif

                    <div>
                        <div class="font-medium text-base text-gray-800 flex items-center">
                            {{ Auth::user()->name }}
                            @if(auth()->user()->role === 'admin')
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
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

                        <x-responsive-nav-link :href="route('admin.custom-products.index')" :active="request()->routeIs('admin.custom-products.*')">
                            <div class="flex items-center">
                                <i class="fas fa-flask w-4 h-4 mr-2 text-pink-500"></i>
                                {{ __('Manage Custom Products') }}
                            </div>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('admin.custom-products.analytics')" :active="request()->routeIs('admin.custom-products.analytics')">
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

                        <x-responsive-nav-link :href="route('custom-products.index')" :active="request()->routeIs('custom-products.*')">
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

<!-- Optional: Add this script to update cart counter dynamically -->
<script>
    // Function to update cart counter
    function updateCartCounter(count) {
        const counter = document.getElementById('cartCounter');
        if (counter) {
            counter.textContent = count;
            if (count > 0) {
                counter.classList.remove('hidden');
            } else {
                counter.classList.add('hidden');
            }
        }
    }

    // Listen for cart updates (you can call this from your add to cart functions)
    window.updateCartCounter = updateCartCounter;
</script>