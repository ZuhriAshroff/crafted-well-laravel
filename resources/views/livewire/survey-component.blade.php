<div class="bg-gradient-to-br from-white to-pink-50 min-h-screen relative">
    {{-- Notification --}}
    @if($notification)
        <div class="fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300
                    {{ $notification['type'] === 'error' ? 'bg-red-500 text-white' : 'bg-green-500 text-white' }}"
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)">
            <div class="flex items-center">
                <i class="fas fa-{{ $notification['type'] === 'error' ? 'exclamation-circle' : 'check-circle' }} mr-2"></i>
                {{ $notification['message'] }}
            </div>
        </div>
    @endif

    {{-- Progress Steps --}}
    <div class="container mx-auto px-4 mb-12">
        <div class="flex justify-center items-center mt-16">
            <div class="flex items-center space-x-4">
                @for($i = 1; $i <= $totalSteps; $i++)
                    <div class="w-4 h-4 rounded-full {{ $currentStep >= $i ? 'bg-pink-500' : 'bg-pink-200' }}"></div>
                    @if($i < $totalSteps)
                        <div class="w-24 h-0.5 {{ $currentStep > $i ? 'bg-pink-500' : 'bg-pink-100' }}"></div>
                    @endif
                @endfor
            </div>
        </div>
        
        <div class="flex justify-center items-center mt-4">
            <div class="flex items-center space-x-4 text-sm text-gray-600">
                <span class="w-20 text-center">Skin Profile</span>
                <span class="w-24"></span>
                <span class="w-20 text-center">Environment</span>
                <span class="w-24"></span>
                <span class="w-20 text-center">Preferences</span>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 max-w-4xl">
        <h1 class="text-4xl font-bold text-center mb-8">Discover Your Unique Skin Profile</h1>

        {{-- Step 1: Skin Type & Concerns --}}
        @if($currentStep === 1)
            <div class="step-content">
                <h2 class="text-2xl uppercase text-center mb-8">Tell us about your skin</h2>

                <div class="grid md:grid-cols-2 gap-8">
                    {{-- Skin Type Section --}}
                    <div class="bg-pink-50 p-6 rounded-lg relative">
                        <span class="text-xs font-small text-gray-500">first step</span>
                        <h3 class="text-xl mb-4 font-bold">Skin Type</h3>
                        <p class="text-sm text-gray-600 mb-4">Please select one skin type that best describes your skin</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($skinTypes as $key => $label)
                                <button 
                                    wire:click="selectSkinType('{{ $key }}')"
                                    class="p-4 rounded-lg bg-white shadow-md border-2 transition-all duration-200 flex flex-col
                                           {{ $skinType === $key ? 'border-pink-500' : 'border-white hover:border-pink-500' }}">
                                    <i class="fas fa-{{ $key === 'dry' ? 'tint' : ($key === 'oily' ? 'oil-can' : ($key === 'combination' ? 'layer-group' : 'heart')) }} text-5xl text-pink-600 mb-2"></i>
                                    <span class="text-pink-600 text-lg tracking-widest uppercase text-left">{{ $label }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Skin Concerns Section --}}
                    <div class="bg-pink-50 p-6 rounded-lg relative">
                        <span class="text-xs font-small text-gray-500">second step</span>
                        <h3 class="text-xl mb-4 font-bold">Skin Concerns</h3>
                        <p class="text-sm text-gray-600 mb-4">Select your primary skin concerns. You can choose multiple options that apply to you.</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($skinConcerns as $key => $label)
                                <button 
                                    wire:click="selectConcern('{{ $key }}')"
                                    class="p-4 rounded-lg bg-white shadow-md border-2 transition-all duration-200 flex flex-col
                                           {{ $this->isConcernSelected($key) ? 'border-pink-500' : 'border-white hover:border-pink-500' }}">
                                    <i class="fas fa-{{ $key === 'blemish' ? 'bug' : ($key === 'wrinkle' ? 'user-clock' : ($key === 'spots' ? 'dot-circle' : 'leaf')) }} text-5xl text-pink-600 mb-2"></i>
                                    <span class="text-pink-600 text-lg tracking-widest uppercase text-left">{{ $label }}</span>
                                    @if($this->getConcernType($key) === 'primary')
                                        <span class="text-xs text-pink-800 mt-1">Primary</span>
                                    @elseif($this->getConcernType($key) === 'secondary')
                                        <span class="text-xs text-pink-800 mt-1">Secondary</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Step 2: Environment --}}
        @if($currentStep === 2)
            <div class="step-content">
                <h2 class="text-2xl text-center mb-8">Your Environmental Context</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($environments as $key => $label)
                        <button 
                            wire:click="selectEnvironment('{{ $key }}')"
                            class="bg-white rounded-2xl h-80 shadow-md hover:shadow-lg overflow-hidden relative group transition-all duration-300
                                   {{ $environment === $key ? 'border-pink-500 border-2' : 'border-transparent' }}">
                            <div class="p-4 absolute top-0 left-0 w-full z-10 text-center bg-white/80 backdrop-blur-sm">
                                <h3 class="text-xl font-bold mb-2 transition-all duration-300 group-hover:translate-y-[-5px]">{{ $label }}</h3>
                                <p class="text-gray-600 transition-all duration-300 group-hover:translate-y-[-5px]">
                                    {{ $key === 'urban' ? 'Polluted, high-stress environment' : 
                                       ($key === 'tropical' ? 'Humid, warm climate' : 'Moderate, changing seasons') }}
                                </p>
                            </div>
                            <img src="{{ asset('images/' . $key . '.png') }}" 
                                 alt="{{ $label }} Environment" 
                                 class="absolute bottom-0 w-full h-48 object-cover transition-transform duration-300 rounded-b-2xl group-hover:scale-105">
                        </button>
                    @endforeach
                </div>
                
                <div class="text-center mt-6">
                    <p class="text-gray-600 text-sm">Your environment affects your skin's needs. Choose the option that best describes your daily surroundings.</p>
                </div>
            </div>
        @endif

        {{-- Step 3: Allergies --}}
        @if($currentStep === 3)
            <div class="step-content">
                <h2 class="text-2xl text-center mb-8">Allergies And Other Concerns</h2>
                
                <div class="bg-white rounded-2xl p-6 shadow-md max-w-xl mx-auto">
                    <div class="relative">
                        <label class="text-sm text-gray-600 mb-2 block">Select any allergies or sensitivities that apply to you:</label>
                        
                        <div class="relative">
                            <button 
                                type="button" 
                                wire:click="toggleAllergyDropdown"
                                class="w-full p-4 border border-pink-200 rounded-lg bg-white flex justify-between items-center text-left hover:border-pink-300 transition-colors">
                                <span class="{{ count($allergies) === 0 ? 'text-gray-500' : 'text-gray-800' }}">
                                    {{ $this->getAllergyDropdownText() }}
                                </span>
                                <i class="fas fa-chevron-{{ $allergyDropdownOpen ? 'up' : 'down' }} text-gray-400 transition-transform duration-200"></i>
                            </button>

                            @if($allergyDropdownOpen)
                                <div class="absolute w-full mt-2 bg-white border border-pink-200 rounded-lg shadow-lg z-10 max-h-96 overflow-y-auto">
                                    <div class="p-2">
                                        @foreach($allergyOptions as $key => $option)
                                            <label class="flex items-start p-3 hover:bg-pink-50 rounded cursor-pointer transition-colors">
                                                <input 
                                                    type="checkbox" 
                                                    wire:click="toggleAllergy('{{ $key }}')"
                                                    {{ in_array($key, $allergies) ? 'checked' : '' }}
                                                    class="mr-3 mt-1 form-checkbox text-pink-500 focus:ring-pink-500 focus:ring-opacity-25">
                                                <div>
                                                    <span class="font-medium block text-gray-800">{{ $option['label'] }}</span>
                                                    <span class="text-sm text-gray-500">{{ $option['description'] }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Selected Allergies Display --}}
                    @if(count($allergies) > 0)
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($allergies as $allergy)
                                <span class="bg-pink-100 text-pink-800 px-3 py-1 rounded-full text-sm flex items-center transition-all duration-200">
                                    {{ $allergyOptions[$allergy]['label'] }}
                                    <button wire:click="removeAllergy('{{ $allergy }}')" class="ml-2 text-pink-600 hover:text-pink-800 transition-colors">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="mt-4 p-3 bg-pink-50 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-info-circle text-pink-500 mr-2"></i>
                            Don't worry if you're not sure about specific ingredients. This helps us create a safer formulation for you.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Navigation Buttons --}}
        <div class="flex justify-between mt-8">
            @if($currentStep > 1)
                <button 
                    wire:click="previousStep"
                    class="px-8 py-3 bg-transparent border-2 border-pink-600 text-pink-600 rounded-full hover:opacity-90 transition-all">
                    Previous
                </button>
            @endif

            <button 
                wire:click="nextStep"
                wire:loading.attr="disabled"
                class="px-8 py-3 border-2 border-pink-600 bg-pink-600 text-white rounded-full hover:opacity-90 transition-all 
                       {{ $currentStep === 1 ? 'ml-auto' : '' }} disabled:opacity-50">
                <span wire:loading.remove wire:target="nextStep">
                    {{ $currentStep === $totalSteps ? 'Complete Survey' : 'Next →' }}
                </span>
                <span wire:loading wire:target="nextStep">
                    Processing...
                </span>
            </button>
        </div>
    </div>

    {{-- Loading Overlay --}}
    @if($loading)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 text-center">
                <div class="🤚 mx-auto mb-4">
                    <div class="👉"></div>
                    <div class="👍"></div>
                </div>
                <p class="text-gray-600">Creating your personalized skincare...</p>
            </div>
        </div>
    @endif

    {{-- Authentication Modal --}}
    @if($showAuthModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">Almost there!</h3>
                    <button wire:click="hideAuthModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Auth Mode Toggle --}}
                <div class="flex border-b border-gray-200 mb-6">
                    <button 
                        wire:click="switchAuthMode('login')"
                        class="flex-1 py-2 text-center border-b-2 transition-colors
                               {{ $authMode === 'login' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500' }}">
                        Sign In
                    </button>
                    <button 
                        wire:click="switchAuthMode('register')"
                        class="flex-1 py-2 text-center border-b-2 transition-colors
                               {{ $authMode === 'register' ? 'border-pink-500 text-pink-600' : 'border-transparent text-gray-500' }}">
                        Create Account
                    </button>
                </div>

                {{-- Login Form --}}
                @if($authMode === 'login')
                    <form wire:submit.prevent="login">
                        <div class="space-y-4">
                            <div>
                                <input 
                                    type="email" 
                                    wire:model="email"
                                    placeholder="Email"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <input 
                                    type="password" 
                                    wire:model="password"
                                    placeholder="Password"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                class="w-full bg-pink-600 text-white py-3 rounded-lg hover:bg-pink-700 transition-colors disabled:opacity-50">
                                <span wire:loading.remove wire:target="login">Sign In</span>
                                <span wire:loading wire:target="login">Signing in...</span>
                            </button>
                        </div>
                    </form>
                @endif

                {{-- Register Form --}}
                @if($authMode === 'register')
                    <form wire:submit.prevent="register">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <input 
                                        type="text" 
                                        wire:model="firstName"
                                        placeholder="First Name"
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                    @error('firstName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <input 
                                        type="text" 
                                        wire:model="lastName"
                                        placeholder="Last Name"
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                    @error('lastName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <input 
                                    type="email" 
                                    wire:model="email"
                                    placeholder="Email"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <input 
                                    type="tel" 
                                    wire:model="phoneNumber"
                                    placeholder="Phone Number (Optional)"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                @error('phoneNumber') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <input 
                                    type="password" 
                                    wire:model="password"
                                    placeholder="Password"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <input 
                                    type="password" 
                                    wire:model="passwordConfirmation"
                                    placeholder="Confirm Password"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                @error('passwordConfirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <button 
                                type="submit"
                                wire:loading.attr="disabled"
                                class="w-full bg-pink-600 text-white py-3 rounded-lg hover:bg-pink-700 transition-colors disabled:opacity-50">
                                <span wire:loading.remove wire:target="register">Create Account</span>
                                <span wire:loading wire:target="register">Creating Account...</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

    {{-- Embedded Styles --}}
    <style>
        .🤚 {
            --size: 40px;
            --speed: 800ms;
            --thumbs: 1;
            width: var(--size);
            height: var(--size);
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .👉, .👍 {
            position: absolute;
            width: var(--size);
            height: var(--size);
            border-radius: 50%;
            background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
            animation: pulse var(--speed) ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
        }
    </style>
</div>