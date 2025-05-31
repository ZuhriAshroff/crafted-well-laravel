<?php

namespace App\Livewire;

use App\Models\UserProfile;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;

class ProfileWizard extends Component
{
    // Component state
    public $currentStep = 1;
    public $totalSteps = 4;

    // Form data
    public $skin_type = '';
    public $primary_skin_concerns = '';
    public $secondary_skin_concerns = [];
    public $environmental_factors = '';
    public $allergies = [];
    public $allergyInput = '';

    // Options
    public $skinTypes = [];
    public $skinConcerns = [];
    public $environmentalFactors = [];

    // Validation state
    public $stepValidation = [];

    public function mount()
    {
        $this->skinTypes = UserProfile::getSkinTypeOptions();
        $this->skinConcerns = UserProfile::getSkinConcernsOptions();
        $this->environmentalFactors = UserProfile::getEnvironmentalFactorsOptions();

        // Load existing profile if available
        $existingProfile = UserProfile::getLatestForUser(auth()->user()->user_id);
        if ($existingProfile) {
            $this->loadExistingProfile($existingProfile);
        }
    }

    private function loadExistingProfile($profile)
    {
        $this->skin_type = $profile->skin_type;
        $this->primary_skin_concerns = $profile->primary_skin_concerns;
        $this->secondary_skin_concerns = $profile->secondary_skin_concerns ?? [];
        $this->environmental_factors = $profile->environmental_factors;
        $this->allergies = $profile->allergies ?? [];
    }

    public function nextStep()
    {
        if ($this->validateCurrentStep()) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                return $this->validateSkinType();
            case 2:
                return $this->validateSkinConcerns();
            case 3:
                return $this->validateEnvironmentalFactors();
            case 4:
                return $this->validateAllergies();
            default:
                return true;
        }
    }

    private function validateSkinType()
    {
        $validator = Validator::make(['skin_type' => $this->skin_type], UserProfile::skinTypeValidationRules());
        
        if ($validator->fails()) {
            $this->addError('skin_type', $validator->errors()->first('skin_type'));
            return false;
        }
        
        $this->resetErrorBag('skin_type');
        return true;
    }

    private function validateSkinConcerns()
    {
        $validator = Validator::make([
            'primary_skin_concerns' => $this->primary_skin_concerns,
            'secondary_skin_concerns' => $this->secondary_skin_concerns
        ], UserProfile::skinConcernsValidationRules());
        
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->addError('skin_concerns', $error);
            }
            return false;
        }
        
        $this->resetErrorBag('skin_concerns');
        return true;
    }

    private function validateEnvironmentalFactors()
    {
        $validator = Validator::make(['environmental_factors' => $this->environmental_factors], UserProfile::environmentalFactorsValidationRules());
        
        if ($validator->fails()) {
            $this->addError('environmental_factors', $validator->errors()->first('environmental_factors'));
            return false;
        }
        
        $this->resetErrorBag('environmental_factors');
        return true;
    }

    private function validateAllergies()
    {
        // Allergies are optional, so always valid
        return true;
    }

    public function addAllergy()
    {
        if (!empty($this->allergyInput) && !in_array($this->allergyInput, $this->allergies)) {
            $this->allergies[] = trim($this->allergyInput);
            $this->allergyInput = '';
        }
    }

    public function removeAllergy($index)
    {
        unset($this->allergies[$index]);
        $this->allergies = array_values($this->allergies);
    }

    public function addSecondaryConcern($concern)
    {
        if (!in_array($concern, $this->secondary_skin_concerns)) {
            $this->secondary_skin_concerns[] = $concern;
        }
    }

    public function removeSecondaryConcern($concern)
    {
        $this->secondary_skin_concerns = array_filter($this->secondary_skin_concerns, fn($c) => $c !== $concern);
        $this->secondary_skin_concerns = array_values($this->secondary_skin_concerns);
    }

    public function saveProfile()
    {
        // Validate all steps
        if (!$this->validateAllSteps()) {
            return;
        }

        try {
            $profileData = [
                'skin_type' => $this->skin_type,
                'primary_skin_concerns' => $this->primary_skin_concerns,
                'secondary_skin_concerns' => $this->secondary_skin_concerns,
                'environmental_factors' => $this->environmental_factors,
                'allergies' => $this->allergies,
            ];

            UserProfile::createOrUpdateForUser(auth()->user()->user_id, $profileData);

            session()->flash('success', 'Profile saved successfully!');
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            $this->addError('save', 'Failed to save profile. Please try again.');
        }
    }

    private function validateAllSteps()
    {
        $validator = Validator::make([
            'skin_type' => $this->skin_type,
            'primary_skin_concerns' => $this->primary_skin_concerns,
            'secondary_skin_concerns' => $this->secondary_skin_concerns,
            'environmental_factors' => $this->environmental_factors,
            'allergies' => $this->allergies,
        ], UserProfile::validationRules());

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->addError('validation', $error);
            }
            return false;
        }

        return true;
    }

    public function getProgressPercentage()
    {
        return ($this->currentStep / $this->totalSteps) * 100;
    }

    public function render()
    {
        return view('livewire.profile-wizard');
    }
}