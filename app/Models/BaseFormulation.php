<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BaseFormulation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'base_formulations';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'base_formulation_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'base_name',
        'universal_ingredients',
        'standard_concentration_ranges',
        'skin_type_compatibility',
        'formulation_category',
        'description',
        'created_by',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'universal_ingredients' => 'array',
        'standard_concentration_ranges' => 'array',
        'skin_type_compatibility' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Formulation categories
     */
    public const FORMULATION_CATEGORIES = [
        'anti_aging' => 'Anti-Aging',
        'hydrating' => 'Hydrating',
        'brightening' => 'Brightening',
        'acne_treatment' => 'Acne Treatment',
        'sensitive_care' => 'Sensitive Care',
        'barrier_repair' => 'Barrier Repair',
        'exfoliating' => 'Exfoliating',
        'antioxidant' => 'Antioxidant'
    ];

    /**
     * Skin types
     */
    public const SKIN_TYPES = ['dry', 'oily', 'combination', 'sensitive', 'normal'];

    /**
     * Universal ingredients with their properties
     */
    public const UNIVERSAL_INGREDIENTS = [
        'water' => [
            'name' => 'Purified Water',
            'function' => 'Solvent',
            'concentration_range' => ['min' => 60, 'max' => 85],
            'compatibility' => 'all'
        ],
        'glycerin' => [
            'name' => 'Glycerin',
            'function' => 'Humectant',
            'concentration_range' => ['min' => 1, 'max' => 10],
            'compatibility' => 'all'
        ],
        'sodium_hyaluronate' => [
            'name' => 'Sodium Hyaluronate',
            'function' => 'Hydrating Agent',
            'concentration_range' => ['min' => 0.1, 'max' => 2],
            'compatibility' => 'all'
        ],
        'phenoxyethanol' => [
            'name' => 'Phenoxyethanol',
            'function' => 'Preservative',
            'concentration_range' => ['min' => 0.5, 'max' => 1],
            'compatibility' => 'most'
        ],
        'carbomer' => [
            'name' => 'Carbomer',
            'function' => 'Thickening Agent',
            'concentration_range' => ['min' => 0.1, 'max' => 0.5],
            'compatibility' => 'all'
        ],
        'triethanolamine' => [
            'name' => 'Triethanolamine',
            'function' => 'pH Adjuster',
            'concentration_range' => ['min' => 0.1, 'max' => 0.3],
            'compatibility' => 'most'
        ],
        'disodium_edta' => [
            'name' => 'Disodium EDTA',
            'function' => 'Chelating Agent',
            'concentration_range' => ['min' => 0.01, 'max' => 0.1],
            'compatibility' => 'all'
        ]
    ];

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($baseFormulation) {
            if (empty($baseFormulation->is_active)) {
                $baseFormulation->is_active = true;
            }
            if (empty($baseFormulation->created_by)) {
                $baseFormulation->created_by = auth()->id();
            }
        });
    }

    /**
     * Get the validation rules for base formulation data
     */
    public static function validationRules($isUpdate = false): array
    {
        $rules = [
            'base_name' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:100',
                'unique:BaseFormulation,base_name' . ($isUpdate ? ',{id},base_formulation_id' : '')
            ],
            'universal_ingredients' => [
                $isUpdate ? 'sometimes' : 'required',
                'array',
                'min:3'
            ],
            'universal_ingredients.*' => [
                'string',
                Rule::in(array_keys(self::UNIVERSAL_INGREDIENTS))
            ],
            'standard_concentration_ranges' => [
                $isUpdate ? 'sometimes' : 'required',
                'array'
            ],
            'skin_type_compatibility' => [
                $isUpdate ? 'sometimes' : 'required',
                'array',
                'min:1'
            ],
            'skin_type_compatibility.*' => [
                'string',
                Rule::in(self::SKIN_TYPES)
            ],
            'formulation_category' => [
                'sometimes',
                'string',
                Rule::in(array_keys(self::FORMULATION_CATEGORIES))
            ],
            'description' => [
                'sometimes',
                'string',
                'max:500'
            ],
            'is_active' => [
                'sometimes',
                'boolean'
            ]
        ];

        return $rules;
    }

    /**
     * Create base formulation with validation and defaults
     */
    public static function createFormulation(array $data): self
    {
        return DB::transaction(function () use ($data) {
            // Set default universal ingredients if not provided
            if (empty($data['universal_ingredients'])) {
                $data['universal_ingredients'] = ['water', 'glycerin', 'sodium_hyaluronate', 'phenoxyethanol'];
            }

            // Set default concentration ranges based on universal ingredients
            if (empty($data['standard_concentration_ranges'])) {
                $data['standard_concentration_ranges'] = static::generateDefaultConcentrationRanges($data['universal_ingredients']);
            }

            // Validate concentration ranges against universal ingredients
            static::validateConcentrationRanges($data['universal_ingredients'], $data['standard_concentration_ranges']);

            return static::create($data);
        });
    }

    /**
     * Generate default concentration ranges for ingredients
     */
    public static function generateDefaultConcentrationRanges(array $ingredients): array
    {
        $ranges = [];
        
        foreach ($ingredients as $ingredient) {
            if (isset(self::UNIVERSAL_INGREDIENTS[$ingredient])) {
                $ranges[$ingredient] = self::UNIVERSAL_INGREDIENTS[$ingredient]['concentration_range'];
            }
        }
        
        return $ranges;
    }

    /**
     * Validate concentration ranges against ingredients
     */
    public static function validateConcentrationRanges(array $ingredients, array $concentrationRanges): void
    {
        foreach ($ingredients as $ingredient) {
            if (!isset($concentrationRanges[$ingredient])) {
                throw new \InvalidArgumentException("Missing concentration range for ingredient: {$ingredient}");
            }

            $range = $concentrationRanges[$ingredient];
            if (!isset($range['min']) || !isset($range['max'])) {
                throw new \InvalidArgumentException("Invalid concentration range format for ingredient: {$ingredient}");
            }

            if ($range['min'] >= $range['max']) {
                throw new \InvalidArgumentException("Invalid concentration range values for ingredient: {$ingredient}");
            }

            // Validate against universal ingredient limits if available
            if (isset(self::UNIVERSAL_INGREDIENTS[$ingredient])) {
                $universalRange = self::UNIVERSAL_INGREDIENTS[$ingredient]['concentration_range'];
                if ($range['min'] < $universalRange['min'] || $range['max'] > $universalRange['max']) {
                    throw new \InvalidArgumentException("Concentration range exceeds safe limits for ingredient: {$ingredient}");
                }
            }
        }
    }

    /**
     * Check ingredient compatibility with formulation
     */
    public function checkIngredientCompatibility(array $ingredientIds): array
    {
        $incompatible = [];
        
        foreach ($ingredientIds as $ingredientId) {
            if (!in_array($ingredientId, $this->universal_ingredients)) {
                $incompatible[] = $ingredientId;
            }
        }
        
        return $incompatible;
    }

    /**
     * Validate concentration for specific ingredient
     */
    public function validateConcentration(string $ingredientId, float $concentration): bool
    {
        if (!isset($this->standard_concentration_ranges[$ingredientId])) {
            return false;
        }

        $range = $this->standard_concentration_ranges[$ingredientId];
        
        return $concentration >= $range['min'] && $concentration <= $range['max'];
    }

    /**
     * Check if formulation is compatible with skin type
     */
    public function isCompatibleWithSkinType(string $skinType): bool
    {
        return in_array($skinType, $this->skin_type_compatibility);
    }

    /**
     * Get formulation summary with details
     */
    public function getFormulationSummary(): array
    {
        return [
            'base_formulation_id' => $this->base_formulation_id,
            'base_name' => $this->base_name,
            'category' => $this->formulation_category,
            'category_display' => self::FORMULATION_CATEGORIES[$this->formulation_category] ?? 'General',
            'description' => $this->description,
            'universal_ingredients' => $this->getIngredientDetails(),
            'concentration_ranges' => $this->standard_concentration_ranges,
            'skin_type_compatibility' => $this->skin_type_compatibility,
            'compatible_skin_types_display' => array_map('ucfirst', $this->skin_type_compatibility),
            'total_ingredients' => count($this->universal_ingredients),
            'is_active' => $this->is_active,
            'created_by' => $this->creator?->name ?? 'System',
        ];
    }

    /**
     * Get detailed ingredient information
     */
    public function getIngredientDetails(): array
    {
        $details = [];
        
        foreach ($this->universal_ingredients as $ingredient) {
            if (isset(self::UNIVERSAL_INGREDIENTS[$ingredient])) {
                $ingredientInfo = self::UNIVERSAL_INGREDIENTS[$ingredient];
                $details[$ingredient] = [
                    'name' => $ingredientInfo['name'],
                    'function' => $ingredientInfo['function'],
                    'concentration_range' => $this->standard_concentration_ranges[$ingredient] ?? $ingredientInfo['concentration_range'],
                ];
            }
        }
        
        return $details;
    }

    /**
     * Calculate total concentration percentage
     */
    public function calculateTotalConcentration(): array
    {
        $total = [
            'min_total' => 0,
            'max_total' => 0,
            'water_content' => 0
        ];

        foreach ($this->standard_concentration_ranges as $ingredient => $range) {
            if ($ingredient === 'water') {
                $total['water_content'] = $range['max'];
            } else {
                $total['min_total'] += $range['min'];
                $total['max_total'] += $range['max'];
            }
        }

        return $total;
    }

    /**
     * Get formulation recommendations for skin types
     */
    public static function getRecommendationsForSkinType(string $skinType): array
    {
        return static::where('is_active', true)
            ->whereJsonContains('skin_type_compatibility', $skinType)
            ->orderBy('base_name')
            ->get()
            ->map(fn($formulation) => $formulation->getFormulationSummary())
            ->toArray();
    }

    /**
     * Get formulations by category
     */
    public static function getByCategory(string $category): array
    {
        return static::where('formulation_category', $category)
            ->where('is_active', true)
            ->orderBy('base_name')
            ->get()
            ->map(fn($formulation) => $formulation->getFormulationSummary())
            ->toArray();
    }

    /**
     * Update formulation with validation
     */
    public function updateFormulation(array $data): bool
    {
        return DB::transaction(function () use ($data) {
            // If ingredients or ranges are being updated, validate them
            if (isset($data['universal_ingredients']) || isset($data['standard_concentration_ranges'])) {
                $ingredients = $data['universal_ingredients'] ?? $this->universal_ingredients;
                $ranges = $data['standard_concentration_ranges'] ?? $this->standard_concentration_ranges;
                
                static::validateConcentrationRanges($ingredients, $ranges);
            }

            return $this->update($data);
        });
    }

    /**
     * Deactivate formulation (soft delete alternative)
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Clone formulation with new name
     */
    public function cloneFormulation(string $newName, ?string $description = null): self
    {
        return static::createFormulation([
            'base_name' => $newName,
            'universal_ingredients' => $this->universal_ingredients,
            'standard_concentration_ranges' => $this->standard_concentration_ranges,
            'skin_type_compatibility' => $this->skin_type_compatibility,
            'formulation_category' => $this->formulation_category,
            'description' => $description ?? $this->description . ' (Clone)',
        ]);
    }

    /**
     * Scope for active formulations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for formulations by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('formulation_category', $category);
    }

    /**
     * Scope for skin type compatible formulations
     */
    public function scopeCompatibleWithSkinType($query, $skinType)
    {
        return $query->whereJsonContains('skin_type_compatibility', $skinType);
    }

    /**
     * Get formulation statistics
     */
    public static function getFormulationStats(): array
    {
        return [
            'total_formulations' => static::count(),
            'active_formulations' => static::active()->count(),
            'formulations_by_category' => static::selectRaw('formulation_category, COUNT(*) as count')
                ->whereNotNull('formulation_category')
                ->groupBy('formulation_category')
                ->pluck('count', 'formulation_category')
                ->toArray(),
            'skin_type_coverage' => static::getSkinTypeCoverage(),
            'most_used_ingredients' => static::getMostUsedIngredients(),
        ];
    }

    /**
     * Get skin type coverage statistics
     */
    protected static function getSkinTypeCoverage(): array
    {
        $coverage = [];
        $formulations = static::active()->get();
        
        foreach (self::SKIN_TYPES as $skinType) {
            $coverage[$skinType] = $formulations->filter(function ($formulation) use ($skinType) {
                return in_array($skinType, $formulation->skin_type_compatibility);
            })->count();
        }
        
        return $coverage;
    }

    /**
     * Get most used ingredients across formulations
     */
    protected static function getMostUsedIngredients(): array
    {
        $formulations = static::active()->get();
        $ingredients = [];
        
        foreach ($formulations as $formulation) {
            foreach ($formulation->universal_ingredients as $ingredient) {
                $ingredients[$ingredient] = ($ingredients[$ingredient] ?? 0) + 1;
            }
        }
        
        arsort($ingredients);
        return array_slice($ingredients, 0, 10, true);
    }

    /**
     * Get category options for forms
     */
    public static function getCategoryOptions(): array
    {
        return self::FORMULATION_CATEGORIES;
    }

    /**
     * Get skin type options for forms
     */
    public static function getSkinTypeOptions(): array
    {
        return array_combine(self::SKIN_TYPES, array_map('ucfirst', self::SKIN_TYPES));
    }

    /**
     * Get universal ingredient options for forms
     */
    public static function getUniversalIngredientOptions(): array
    {
        $options = [];
        foreach (self::UNIVERSAL_INGREDIENTS as $key => $ingredient) {
            $options[$key] = $ingredient['name'] . ' (' . $ingredient['function'] . ')';
        }
        return $options;
    }

    /**
     * Relationships
     */

    /**
     * Base formulation belongs to a creator (admin user)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Base formulation can be used by many custom products
     */
    public function customProducts()
    {
        return $this->hasMany(CustomProduct::class, 'base_product_id', 'base_formulation_id');
    }
}