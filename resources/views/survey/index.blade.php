@extends('layouts.app')

@section('title', 'Skincare Survey - Discover Your Unique Skin Profile')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
    .step-active {
        background-color: rgb(236, 72, 153);
    }
    .line-active {
        background-color: rgb(236, 72, 153);
    }
    
    /* Custom loader styles */
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
@endpush

@section('content')
<div class="bg-gradient-to-br from-white to-pink-50 min-h-screen">
    <!-- Progress Steps -->
    <div class="container mx-auto px-4 mb-12">
        <div class="flex justify-center items-center mt-16">
            <!-- Step dots and lines -->
            <div class="flex items-center space-x-4">
                <div class="w-4 h-4 rounded-full bg-pink-500 step-dot" data-step="1"></div>
                <div class="w-24 h-0.5 bg-pink-100 step-line" id="line1"></div>
                <div class="w-4 h-4 rounded-full bg-pink-200 step-dot" data-step="2"></div>
                <div class="w-24 h-0.5 bg-pink-100 step-line" id="line2"></div>
                <div class="w-4 h-4 rounded-full bg-pink-200 step-dot" data-step="3"></div>
            </div>
        </div>
        
        <!-- Step labels -->
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

        <!-- Step 1: Skin Type & Concerns -->
        <div class="step-content hidden" id="step1">
            <h2 class="text-2xl uppercase text-center mb-8">Tell us about your skin</h2>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Skin Type Section -->
                <div class="bg-pink-50 p-6 rounded-lg relative">
                    <span class="text-xs font-small text-gray-500 skincare">first step</span>
                    <h3 class="text-xl mb-4 font-bold">Skin Type</h3>
                    <p class="text-sm text-gray-600 mb-4">Please select one skin type that best describes your skin</p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <button class="skin-type-btn p-4 rounded-lg bg-white shadow-md border-2 border-white hover:border-2 hover:border-pink-500 flex flex-col transition-all duration-200" data-type="dry">
                            <i class="fas fa-tint text-5xl text-pink-600 mb-2"></i>
                            <span class="text-pink-600 text-lg tracking-widest uppercase text-left">DRY</span>
                        </button>
                        <button class="skin-type-btn p-4 rounded-lg bg-white shadow-md border-2 border-white hover:border-2 hover:border-pink-500 flex flex-col transition-all duration-200" data-type="oily">
                            <i class="fas fa-oil-can text-5xl text-pink-600 mb-2"></i>
                            <span class="text-pink-600 text-lg tracking-widest uppercase text-left">OILY</span>
                        </button>
                        <button class="skin-type-btn p-4 rounded-lg bg-white shadow-md border-2 border-white hover:border-2 hover:border-pink-500 flex flex-col transition-all duration-200" data-type="combination">
                            <i class="fas fa-layer-group text-5xl text-pink-600 mb-2"></i>
                            <span class="text-pink-600 text-lg tracking-widest uppercase text-left">COMBINATION</span>
                        </button>
                        <button class="skin-type-btn p-4 rounded-lg bg-white shadow-md border-2 border-white hover:border-2 hover:border-pink-500 flex flex-col transition-all duration-200" data-type="sensitive">
                            <i class="fas fa-heart text-5xl text-pink-600 mb-2"></i>
                            <span class="text-pink-600 text-lg tracking-widest uppercase text-left">SENSITIVE</span>
                        </button>
                    </div>
                </div>

                <!-- Skin Concerns Section -->
                <div class="bg-pink-50 p-6 rounded-lg relative">
                    <span class="text-xs font-small text-gray-500 skincare">second step</span>
                    <h3 class="text-xl mb-4 font-bold">Skin Concerns</h3>
                    <p class="text-sm text-gray-600 mb-4">Select your primary skin concerns. You can choose multiple options that apply to you.</p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <button class="concern-btn p-4 rounded-lg bg-white shadow-md border-2 border-white hover:border-2 hover:border-pink-500 flex flex-col transition-all duration-200" data-concern="blemish">
                            <i class="fas fa-bug text-5xl text-pink-600 mb-2"></i>
                            <span class="text-pink-600 text-lg tracking-widest uppercase text-left">BLEMISH</span>
                        </button>
                        <button class="concern-btn p-4 rounded-lg bg-white shadow-md border-2 border-white hover:border-2 hover:border-pink-500 flex flex-col transition-all duration-200" data-concern="wrinkle">
                            <i class="fas fa-user-clock text-5xl text-pink-600 mb-2"></i>
                            <span class="text-pink-600 text-lg tracking-widest uppercase text-left">WRINKLE</span>
                        </button>
                        <button class="concern-btn p-4 rounded-lg bg-white shadow-md border-2 border-white hover:border-2 hover:border-pink-500 flex flex-col transition-all duration-200" data-concern="spots">
                            <i class="fas fa-dot-circle text-5xl text-pink-600 mb-2"></i>
                            <span class="text-pink-600 text-lg tracking-widest uppercase text-left">SPOTS</span>
                        </button>
                        <button class="concern-btn p-4 rounded-lg bg-white shadow-md border-2 border-white hover:border-2 hover:border-pink-500 flex flex-col transition-all duration-200" data-concern="soothe">
                            <i class="fas fa-leaf text-5xl text-pink-600 mb-2"></i>
                            <span class="text-pink-600 text-lg tracking-widest uppercase text-left">SOOTHE</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Environment -->
        <div class="step-content hidden" id="step2">
            <h2 class="text-2xl text-center mb-8">Your Environmental Context</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <button class="env-btn bg-white rounded-2xl h-80 shadow-md hover:shadow-lg overflow-hidden relative group transition-all duration-300" data-env="urban">
                    <!-- Content positioned at the top -->
                    <div class="p-4 absolute top-0 left-0 w-full z-10 text-center bg-white/80 backdrop-blur-sm">
                        <h3 class="text-xl font-bold mb-2 transition-all duration-300 group-hover:translate-y-[-5px]">URBAN</h3>
                        <p class="text-gray-600 transition-all duration-300 group-hover:translate-y-[-5px]">Polluted, high-stress environment</p>
                    </div>
                    <!-- Image positioned at the bottom -->
                    <img src="{{ asset('images/environments/city.png') }}" alt="Urban Environment" class="absolute bottom-0 w-full h-48 object-cover transition-transform duration-300 rounded-b-2xl group-hover:scale-105">
                </button>
                
                <button class="env-btn bg-white rounded-2xl h-80 shadow-md hover:shadow-lg overflow-hidden relative group transition-all duration-300" data-env="tropical">
                    <div class="p-4 absolute top-0 left-0 w-full z-10 text-center bg-white/80 backdrop-blur-sm">
                        <h3 class="text-xl font-bold mb-2 transition-all duration-300 group-hover:translate-y-[-5px]">TROPICAL</h3>
                        <p class="text-gray-600 transition-all duration-300 group-hover:translate-y-[-5px]">Humid, warm climate</p>
                    </div>
                    <img src="{{ asset('images/environments/tropical.png') }}" alt="Tropical Environment" class="absolute bottom-0 w-full h-48 object-cover transition-transform duration-300 rounded-b-2xl group-hover:scale-105">
                </button>
                
                <button class="env-btn bg-white rounded-2xl h-80 shadow-md hover:shadow-lg overflow-hidden relative group transition-all duration-300" data-env="moderate">
                    <div class="p-4 absolute top-0 left-0 w-full z-10 text-center bg-white/80 backdrop-blur-sm">
                        <h3 class="text-xl font-bold mb-2 transition-all duration-300 group-hover:translate-y-[-5px]">MODERATE</h3>
                        <p class="text-gray-600 transition-all duration-300 group-hover:translate-y-[-5px]">Moderate, changing seasons</p>
                    </div>
                    <img src="{{ asset('images/environments/moderate.png') }}" alt="Moderate Environment" class="absolute bottom-0 w-full h-48 object-cover transition-transform duration-300 rounded-b-2xl group-hover:scale-105">
                </button>
            </div>
            
            <div class="text-center mt-6">
                <p class="text-gray-600 text-sm">Your environment affects your skin's needs. Choose the option that best describes your daily surroundings.</p>
            </div>
        </div>

        <!-- Step 3: Allergies -->
        <div id="step3" class="step-content hidden">
            <h2 class="text-2xl text-center mb-8">Allergies And Other Concerns</h2>
            
            <div class="bg-white rounded-2xl p-6 shadow-md max-w-xl mx-auto">
                <!-- Multiple Select Dropdown -->
                <div class="relative">
                    <label class="text-sm text-gray-600 mb-2 block">Select any allergies or sensitivities that apply to you:</label>
                    
                    <div class="relative" id="allergyDropdown">
                        <button type="button" class="w-full p-4 border border-pink-200 rounded-lg bg-white flex justify-between items-center text-left hover:border-pink-300 transition-colors" onclick="toggleDropdown()">
                            <span id="dropdownText" class="text-gray-500">Click to select allergies</span>
                            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="dropdownIcon"></i>
                        </button>

                        <!-- Dropdown Options -->
                        <div id="dropdownOptions" class="hidden absolute w-full mt-2 bg-white border border-pink-200 rounded-lg shadow-lg z-10 max-h-96 overflow-y-auto">
                            <div class="p-2">
                                <label class="flex items-start p-3 hover:bg-pink-50 rounded cursor-pointer transition-colors">
                                    <input type="checkbox" value="preservatives" class="mr-3 mt-1 form-checkbox text-pink-500 focus:ring-pink-500 focus:ring-opacity-25">
                                    <div>
                                        <span class="font-medium block text-gray-800">Preservatives</span>
                                        <span class="text-sm text-gray-500">Common preservatives (Parabens, Phenoxyethanol)</span>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 hover:bg-pink-50 rounded cursor-pointer transition-colors">
                                    <input type="checkbox" value="fragrances" class="mr-3 mt-1 form-checkbox text-pink-500 focus:ring-pink-500 focus:ring-opacity-25">
                                    <div>
                                        <span class="font-medium block text-gray-800">Fragrances</span>
                                        <span class="text-sm text-gray-500">Natural or synthetic fragrances</span>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 hover:bg-pink-50 rounded cursor-pointer transition-colors">
                                    <input type="checkbox" value="sulfates" class="mr-3 mt-1 form-checkbox text-pink-500 focus:ring-pink-500 focus:ring-opacity-25">
                                    <div>
                                        <span class="font-medium block text-gray-800">Sulfates</span>
                                        <span class="text-sm text-gray-500">Cleansing agents (SLS, SLES)</span>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 hover:bg-pink-50 rounded cursor-pointer transition-colors">
                                    <input type="checkbox" value="alcohol" class="mr-3 mt-1 form-checkbox text-pink-500 focus:ring-pink-500 focus:ring-opacity-25">
                                    <div>
                                        <span class="font-medium block text-gray-800">Alcohol</span>
                                        <span class="text-sm text-gray-500">Drying alcohols (Ethanol, SD Alcohol)</span>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 hover:bg-pink-50 rounded cursor-pointer transition-colors">
                                    <input type="checkbox" value="silicones" class="mr-3 mt-1 form-checkbox text-pink-500 focus:ring-pink-500 focus:ring-opacity-25">
                                    <div>
                                        <span class="font-medium block text-gray-800">Silicones</span>
                                        <span class="text-sm text-gray-500">Dimethicone and similar compounds</span>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 hover:bg-pink-50 rounded cursor-pointer transition-colors">
                                    <input type="checkbox" value="retinoids" class="mr-3 mt-1 form-checkbox text-pink-500 focus:ring-pink-500 focus:ring-opacity-25">
                                    <div>
                                        <span class="font-medium block text-gray-800">Retinoids</span>
                                        <span class="text-sm text-gray-500">Vitamin A derivatives (Retinol, Retinyl Palmitate)</span>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 hover:bg-pink-50 rounded cursor-pointer transition-colors">
                                    <input type="checkbox" value="vitamin_c" class="mr-3 mt-1 form-checkbox text-pink-500 focus:ring-pink-500 focus:ring-opacity-25">
                                    <div>
                                        <span class="font-medium block text-gray-800">Vitamin C</span>
                                        <span class="text-sm text-gray-500">Ascorbic acid and derivatives</span>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 hover:bg-pink-50 rounded cursor-pointer transition-colors">
                                    <input type="checkbox" value="nuts" class="mr-3 mt-1 form-checkbox text-pink-500 focus:ring-pink-500 focus:ring-opacity-25">
                                    <div>
                                        <span class="font-medium block text-gray-800">Nuts</span>
                                        <span class="text-sm text-gray-500">Nut-based ingredients (Almond oil, Shea)</span>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 hover:bg-pink-50 rounded cursor-pointer transition-colors">
                                    <input type="checkbox" value="soy" class="mr-3 mt-1 form-checkbox text-pink-500 focus:ring-pink-500 focus:ring-opacity-25">
                                    <div>
                                        <span class="font-medium block text-gray-800">Soy</span>
                                        <span class="text-sm text-gray-500">Soy-derived ingredients</span>
                                    </div>
                                </label>
                                <label class="flex items-start p-3 hover:bg-pink-50 rounded cursor-pointer transition-colors">
                                    <input type="checkbox" value="lanolin" class="mr-3 mt-1 form-checkbox text-pink-500 focus:ring-pink-500 focus:ring-opacity-25">
                                    <div>
                                        <span class="font-medium block text-gray-800">Lanolin</span>
                                        <span class="text-sm text-gray-500">Wool-derived ingredients</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Allergies Display -->
                <div id="selectedAllergies" class="mt-4 flex flex-wrap gap-2">
                    <!-- Selected items will be displayed here -->
                </div>
                
                <!-- Optional note -->
                <div class="mt-4 p-3 bg-pink-50 rounded-lg">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle text-pink-500 mr-2"></i>
                        Don't worry if you're not sure about specific ingredients. This helps us create a safer formulation for you.
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8">
            <button id="prevBtn" class="hidden px-8 py-3 bg-transparent border-2 border-pink-600 text-pink-600 rounded-full hover:opacity-90">
                Previous
            </button>
            <button id="nextBtn" class="px-8 py-3 border-2 border-pink-600 bg-pink-600 text-white rounded-full hover:opacity-90 ml-auto">
                Next →
            </button>
        </div>
    </div>

    <!-- Loader -->
    <x-loader />

    <!-- Authentication Modal -->
    <x-auth-modal />
</div>
@endsection

@push('scripts')
<script>
    // Initialize state variables
    let currentStep = 1;
    const totalSteps = 3;
    let surveyData = {
        base_product_id: 1,
        profile: {
            skin_type: '',
            primary_skin_concerns: '',
            secondary_skin_concerns: [],
            environmental_factors: '',
            allergies: []
        }
    };

    // Include all the JavaScript functionality
    @include('survey.partials.survey-scripts')
</script>
@endpush