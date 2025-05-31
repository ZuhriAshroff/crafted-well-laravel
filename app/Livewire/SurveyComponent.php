<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\CustomProduct;
use Illuminate\Support\Facades\Auth;

class SurveyComponent extends Component
{
    // Step management
    public $currentStep = 1;
    public $totalSteps = 3;
    
    // Survey data
    public $skinType = '';
    public $primaryConcern = '';
    public $secondaryConcerns = [];
    public $environment = '';
    public $allergies = [];
    
    // Auth data
    public $showAuthModal = false;
    public $authMode = 'login';
    public $firstName = '';
    public $lastName = '';
    public $email = '';
    public $phoneNumber = '';
    public $password = '';
    public $passwordConfirmation = '';
    
    // UI state
    public $loading = false;
    public $allergyDropdownOpen = false;
    public $notification = null;
    
    // Available options
    public $skinTypes = [
        'dry' => 'DRY',
        'oily' => 'OILY', 
        'combination' => 'COMBINATION',
        'sensitive' => 'SENSITIVE'
    ];
    
    public $skinConcerns = [
        'blemish' => 'BLEMISH',
        'wrinkle' => 'WRINKLE',
        'spots' => 'SPOTS',
        'soothe' => 'SOOTHE'
    ];
    
    public $environments = [
        'urban' => 'URBAN',
        'tropical' => 'TROPICAL',
        'moderate' => 'MODERATE'
    ];
    
    public $allergyOptions = [
        'preservatives' => ['label' => 'Preservatives', 'description' => 'Common preservatives (Parabens, Phenoxyethanol)'],
        'fragrances' => ['label' => 'Fragrances', 'description' => 'Natural or synthetic fragrances'],
        'sulfates' => ['label' => 'Sulfates', 'description' => 'Cleansing agents (SLS, SLES)'],
        'alcohol' => ['label' => 'Alcohol', 'description' => 'Drying alcohols (Ethanol, SD Alcohol)'],
        'silicones' => ['label' => 'Silicones', 'description' => 'Dimethicone and similar compounds'],
        'retinoids' => ['label' => 'Retinoids', 'description' => 'Vitamin A derivatives (Retinol, Retinyl Palmitate)'],
        'vitamin_c' => ['label' => 'Vitamin C', 'description' => 'Ascorbic acid and derivatives'],
        'nuts' => ['label' => 'Nuts', 'description' => 'Nut-based ingredients (Almond oil, Shea)'],
        'soy' => ['label' => 'Soy', 'description' => 'Soy-derived ingredients'],
        'lanolin' => ['label' => 'Lanolin', 'description' => 'Wool-derived ingredients']
    ];

    protected $rules = [
        'firstName' => 'required|string|max:255',
        'lastName' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phoneNumber' => 'nullable|string|max:20',
        'password' => 'required|min:8',
        'passwordConfirmation' => 'required|same:password'
    ];

    public function mount()
    {
        // Clear any notifications on mount
        $this->notification = null;
    }
    
    // Step Navigation
    public function nextStep()
    {
        $this->notification = null; // Clear previous notifications
        
        if ($this->validateCurrentStep()) {
            if ($this->currentStep < $this->totalSteps) {
                $this->currentStep++;
            } else {
                $this->completeSurvey();
            }
        }
    }
    
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
        $this->notification = null;
    }
    
    // Survey Data Methods
    public function selectSkinType($type)
    {
        $this->skinType = $type;
        $this->notification = null;
    }
    
    public function selectConcern($concern)
    {
        if (empty($this->primaryConcern)) {
            $this->primaryConcern = $concern;
        } elseif ($this->primaryConcern === $concern) {
            $this->primaryConcern = '';
        } else {
            if (in_array($concern, $this->secondaryConcerns)) {
                $this->secondaryConcerns = array_values(array_diff($this->secondaryConcerns, [$concern]));
            } else {
                $this->secondaryConcerns[] = $concern;
            }
        }
        $this->notification = null;
    }
    
    public function selectEnvironment($env)
    {
        $this->environment = $env;
        $this->notification = null;
    }
    
    public function toggleAllergy($allergy)
    {
        if (in_array($allergy, $this->allergies)) {
            $this->allergies = array_values(array_diff($this->allergies, [$allergy]));
        } else {
            $this->allergies[] = $allergy;
        }
    }
    
    public function removeAllergy($allergy)
    {
        $this->allergies = array_values(array_diff($this->allergies, [$allergy]));
    }
    
    public function toggleAllergyDropdown()
    {
        $this->allergyDropdownOpen = !$this->allergyDropdownOpen;
    }
    
    // Validation
    private function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                if (empty($this->skinType)) {
                    $this->notification = ['type' => 'error', 'message' => 'Please select your skin type'];
                    return false;
                }
                if (empty($this->primaryConcern)) {
                    $this->notification = ['type' => 'error', 'message' => 'Please select at least one skin concern'];
                    return false;
                }
                return true;
                
            case 2:
                if (empty($this->environment)) {
                    $this->notification = ['type' => 'error', 'message' => 'Please select your environmental context'];
                    return false;
                }
                return true;
                
            case 3:
                return true;
                
            default:
                return false;
        }
    }
    
    // Auth Methods
    public function showAuthModal()
    {
        $this->showAuthModal = true;
        $this->authMode = 'login';
    }
    
    public function hideAuthModal()
    {
        $this->showAuthModal = false;
        $this->resetAuthForm();
    }
    
    public function switchAuthMode($mode)
    {
        $this->authMode = $mode;
        $this->resetErrorBag();
    }
    
    private function resetAuthForm()
    {
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->phoneNumber = '';
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->resetErrorBag();
    }
    
    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->hideAuthModal();
            $this->submitSurvey(); // This will create the custom product
        } else {
            $this->addError('email', 'Invalid credentials');
        }
    }
    
    public function register()
    {
        $this->validate();
        
        try {
            $user = User::create([
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'email' => $this->email,
                'phone_number' => $this->phoneNumber,
                'password' => $this->password
            ]);
            
            Auth::login($user);
            $this->hideAuthModal();
            $this->submitSurvey(); // This will create the custom product
        } catch (\Exception $e) {
            $this->addError('email', 'Registration failed. Please try again.');
        }
    }
    
    // Survey Completion
    public function completeSurvey()
    {
        if (Auth::check()) {
            $this->submitSurvey();
        } else {
            $this->showAuthModal();
        }
    }
    
    private function submitSurvey()
    {
        $this->loading = true;
        $this->notification = null;
        
        try {
            // Create or update user profile
            $profile = UserProfile::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'skin_type' => $this->skinType,
                    'primary_skin_concerns' => $this->primaryConcern,
                    'secondary_skin_concerns' => $this->secondaryConcerns,
                    'environmental_factors' => $this->environment,
                    'allergies' => $this->allergies
                ]
            );
            
            // Create custom product with formulation
            $profileData = [
                'skin_type' => $this->skinType,
                'skin_concerns' => array_filter(array_merge(
                    [$this->primaryConcern],
                    $this->secondaryConcerns
                )),
                'environmental_factors' => $this->environment,
                'allergies' => $this->allergies
            ];
            
            $customProduct = CustomProduct::createWithFormulation([
                'user_id' => Auth::id(),
                'base_product_id' => 1,
                'profile_data' => $profileData
            ]);
            
            $this->notification = ['type' => 'success', 'message' => 'Custom product created successfully!'];
            
            // Redirect to custom product page
            return redirect()->route('custom-products.show', $customProduct->custom_product_id);
            
        } catch (\Exception $e) {
            $this->notification = ['type' => 'error', 'message' => 'Something went wrong: ' . $e->getMessage()];
            \Log::error('Survey submission failed: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }
    
    // Helper methods for UI
    public function isConcernSelected($concern)
    {
        return $this->primaryConcern === $concern || in_array($concern, $this->secondaryConcerns);
    }
    
    public function getConcernType($concern)
    {
        if ($this->primaryConcern === $concern) {
            return 'primary';
        }
        if (in_array($concern, $this->secondaryConcerns)) {
            return 'secondary';
        }
        return null;
    }
    
    public function getAllergyDropdownText()
    {
        $count = count($this->allergies);
        if ($count === 0) {
            return 'Click to select allergies';
        }
        return $count . ' ' . ($count === 1 ? 'allergy' : 'allergies') . ' selected';
    }
    
    public function render()
    {
        return view('livewire.survey-component')
            ->layout('layouts.app');
    }
}