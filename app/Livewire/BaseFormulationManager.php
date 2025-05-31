<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BaseFormulation;

class BaseFormulationManager extends Component
{
    // Form data
    public $baseName = '';
    public $description = '';
    public $formulationCategory = '';
    public $selectedIngredients = [];
    public $skinTypeCompatibility = [];
    public $concentrationRanges = [];

    // Component state
    public $mode = 'create'; // create, edit, view
    public $formulationId = null;
    public $showConcentrationHelper = false;

    // Validation and preview
    public $validationResults = [];
    public $formulationPreview = null;

    protected $rules = [
        'baseName' => 'required|string|max:100',
        'description' => 'sometimes|string|max:500',
        'formulationCategory' => 'required|string',
        'selectedIngredients' => 'required|array|min:3',
        'skinTypeCompatibility' => 'required|array|min:1',
        'concentrationRanges' => 'required|array',
    ];

    public function mount($formulationId = null, $mode = 'create')
    {
        $this->mode = $mode;
        $this->formulationId = $formulationId;

        if ($formulationId && in_array($mode, ['edit', 'view'])) {
            $this->loadFormulation($formulationId);
        } else {
            $this->initializeDefaults();
        }
    }

    public function loadFormulation($id)
    {
        $formulation = BaseFormulation::findOrFail($id);
        
        $this->baseName = $formulation->base_name;
        $this->description = $formulation->description;
        $this->formulationCategory = $formulation->formulation_category;
        $this->selectedIngredients = $formulation->universal_ingredients;
        $this->skinTypeCompatibility = $formulation->skin_type_compatibility;
        $this->concentrationRanges = $formulation->standard_concentration_ranges;
        
        $this->generatePreview();
    }

    public function initializeDefaults()
    {
        $this->selectedIngredients = ['water', 'glycerin', 'sodium_hyaluronate'];
        $this->updateConcentrationRanges();
    }

    public function updatedSelectedIngredients()
    {
        $this->updateConcentrationRanges();
        $this->generatePreview();
    }

    public function updatedSkinTypeCompatibility()
    {
        $this->generatePreview();
    }

    public function updatedFormulationCategory()
    {
        $this->generatePreview();
    }

    public function updateConcentrationRanges()
    {
        $newRanges = [];
        
        foreach ($this->selectedIngredients as $ingredient) {
            if (isset(BaseFormulation::UNIVERSAL_INGREDIENTS[$ingredient])) {
                $newRanges[$ingredient] = BaseFormulation::UNIVERSAL_INGREDIENTS[$ingredient]['concentration_range'];
            }
        }
        
        // Keep existing custom ranges if they exist
        foreach ($this->concentrationRanges as $ingredient => $range) {
            if (in_array($ingredient, $this->selectedIngredients)) {
                $newRanges[$ingredient] = $range;
            }
        }
        
        $this->concentrationRanges = $newRanges;
    }

    public function updateConcentrationRange($ingredient, $type, $value)
    {
        if (!isset($this->concentrationRanges[$ingredient])) {
            $this->concentrationRanges[$ingredient] = ['min' => 0, 'max' => 100];
        }
        
        $this->concentrationRanges[$ingredient][$type] = (float) $value;
        $this->validateConcentrations();
        $this->generatePreview();
    }

    public function validateConcentrations()
    {
        $this->validationResults = [];
        
        foreach ($this->concentrationRanges as $ingredient => $range) {
            $result = [
                'ingredient' => $ingredient,
                'is_valid' => true,
                'errors' => []
            ];
            
            // Check min/max relationship
            if ($range['min'] >= $range['max']) {
                $result['is_valid'] = false;
                $result['errors'][] = 'Minimum must be less than maximum';
            }
            
            // Check against universal ingredient limits
            if (isset(BaseFormulation::UNIVERSAL_INGREDIENTS[$ingredient])) {
                $universalRange = BaseFormulation::UNIVERSAL_INGREDIENTS[$ingredient]['concentration_range'];
                
                if ($range['min'] < $universalRange['min']) {
                    $result['is_valid'] = false;
                    $result['errors'][] = "Minimum below safe limit ({$universalRange['min']}%)";
                }
                
                if ($range['max'] > $universalRange['max']) {
                    $result['is_valid'] = false;
                    $result['errors'][] = "Maximum above safe limit ({$universalRange['max']}%)";
                }
            }
            
            $this->validationResults[$ingredient] = $result;
        }
    }

    public function generatePreview()
    {
        if (empty($this->selectedIngredients) || empty($this->skinTypeCompatibility)) {
            return;
        }

        $totalConcentration = [
            'min_total' => 0,
            'max_total' => 0,
            'water_content' => 0
        ];

        foreach ($this->concentrationRanges as $ingredient => $range) {
            if ($ingredient === 'water') {
                $totalConcentration['water_content'] = $range['max'];
            } else {
                $totalConcentration['min_total'] += $range['min'];
                $totalConcentration['max_total'] += $range['max'];
            }
        }

        $this->formulationPreview = [
            'total_ingredients' => count($this->selectedIngredients),
            'concentration_analysis' => $totalConcentration,
            'skin_type_coverage' => count($this->skinTypeCompatibility),
            'category_display' => BaseFormulation::FORMULATION_CATEGORIES[$this->formulationCategory] ?? 'General',
            'estimated_stability' => $this->calculateStabilityScore(),
        ];
    }

    private function calculateStabilityScore()
    {
        $score = 100;
        
        // Deduct points for too many ingredients
        if (count($this->selectedIngredients) > 7) {
            $score -= (count($this->selectedIngredients) - 7) * 5;
        }
        
        // Deduct points for concentration validation errors
        foreach ($this->validationResults as $result) {
            if (!$result['is_valid']) {
                $score -= 10;
            }
        }
        
        // Check for water content
        if (isset($this->concentrationRanges['water'])) {
            $waterContent = $this->concentrationRanges['water']['max'];
            if ($waterContent < 50) {
                $score -= 15; // Low water content affects stability
            }
        }
        
        return max(0, $score);
    }

    public function save()
    {
        $this->validate();
        $this->validateConcentrations();
        
        // Check if all concentrations are valid
        foreach ($this->validationResults as $result) {
            if (!$result['is_valid']) {
                session()->flash('error', 'Please fix concentration validation errors before saving.');
                return;
            }
        }

        try {
            $data = [
                'base_name' => $this->baseName,
                'description' => $this->description,
                'formulation_category' => $this->formulationCategory,
                'universal_ingredients' => $this->selectedIngredients,
                'standard_concentration_ranges' => $this->concentrationRanges,
                'skin_type_compatibility' => $this->skinTypeCompatibility,
            ];

            if ($this->mode === 'edit' && $this->formulationId) {
                $formulation = BaseFormulation::findOrFail($this->formulationId);
                $formulation->updateFormulation($data);
                session()->flash('success', 'Base formulation updated successfully.');
                
                return redirect()->route('admin.base-formulations.show', $formulation);
            } else {
                $formulation = BaseFormulation::createFormulation($data);
                session()->flash('success', 'Base formulation created successfully.');
                
                return redirect()->route('admin.base-formulations.show', $formulation);
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save base formulation: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.base-formulation-manager', [
            'categories' => BaseFormulation::getCategoryOptions(),
            'skinTypes' => BaseFormulation::getSkinTypeOptions(),
            'universalIngredients' => BaseFormulation::getUniversalIngredientOptions(),
        ]);
    }
}