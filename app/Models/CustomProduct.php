<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomProduct extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'custom_products';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'custom_product_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'base_product_id',
        'profile_data',
        'total_price',
        'selected_ingredients',
        'final_ingredient_concentrations',
        'product_name',
        'product_description',
        'formulation_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'profile_data' => 'array',
        'selected_ingredients' => 'array',
        'final_ingredient_concentrations' => 'array',
        'total_price' => 'decimal:2',
        'formulation_date' => 'datetime',
    ];

    /**
     * Base pricing and formulation constants
     */
    public const BASE_PRICE = 65.00;

    /**
     * Common allergies with examples and alternatives
     */
    public const ALLERGY_CATEGORIES = [
        'preservatives' => [
            'description' => 'Common preservatives (Parabens, Phenoxyethanol)',
            'avoid' => ['parabens', 'phenoxyethanol', 'methylisothiazolinone'],
            'alternatives' => ['sodium benzoate', 'potassium sorbate']
        ],
        'fragrances' => [
            'description' => 'Natural or synthetic fragrances',
            'avoid' => ['fragrance', 'parfum', 'essential_oils'],
            'alternatives' => ['fragrance_free_compounds']
        ],
        'sulfates' => [
            'description' => 'Cleansing agents (SLS, SLES)',
            'avoid' => ['sodium_lauryl_sulfate', 'sodium_laureth_sulfate'],
            'alternatives' => ['gentle_surfactants']
        ],
        'alcohol' => [
            'description' => 'Drying alcohols (Ethanol, SD Alcohol)',
            'avoid' => ['denatured_alcohol', 'ethanol'],
            'alternatives' => ['cetyl_alcohol', 'stearyl_alcohol']
        ],
        'silicones' => [
            'description' => 'Dimethicone and similar compounds',
            'avoid' => ['dimethicone', 'cyclopentasiloxane'],
            'alternatives' => ['natural_oils', 'squalane']
        ],
        'retinoids' => [
            'description' => 'Vitamin A derivatives (Retinol, Retinyl Palmitate)',
            'avoid' => ['retinol', 'retinyl_palmitate'],
            'alternatives' => ['bakuchiol', 'peptides']
        ],
        'vitamin_c' => [
            'description' => 'Ascorbic acid and derivatives',
            'avoid' => ['ascorbic_acid', 'vitamin_c'],
            'alternatives' => ['niacinamide', 'alpha_arbutin']
        ],
        'nuts' => [
            'description' => 'Nut-based ingredients (Almond oil, Shea)',
            'avoid' => ['almond_oil', 'shea_butter'],
            'alternatives' => ['seed_oils', 'squalane']
        ],
        'soy' => [
            'description' => 'Soy-derived ingredients',
            'avoid' => ['soy_extract', 'soy_oil'],
            'alternatives' => ['peptides', 'ceramides']
        ],
        'lanolin' => [
            'description' => 'Wool-derived ingredients',
            'avoid' => ['lanolin', 'wool_alcohol'],
            'alternatives' => ['plant_derived_emollients']
        ]
    ];

    /**
     * Product naming components
     */
    public const PRODUCT_NAME_PREFIXES = [
        'skin_type' => [
            'dry' => ['Hydra', 'Moisture'],
            'oily' => ['Balance', 'Clarify'],
            'combination' => ['Harmony', 'Equilibrium'],
            'sensitive' => ['Gentle', 'Soothe']
        ],
        'concern' => [
            'blemish' => ['Clear', 'Purify'],
            'wrinkle' => ['Youth', 'Renew'],
            'spots' => ['Bright', 'Radiance'],
            'soothe' => ['Calm', 'Relief']
        ]
    ];

    public const PRODUCT_TYPES = ['Serum', 'Essence', 'Elixir', 'Solution'];

    /**
     * Ingredient benefits and pricing
     */
    public const INGREDIENT_BENEFITS = [
        'niacinamide' => [
            'benefit' => 'Regulates Oil Production & Brightens Skin',
            'price' => 8.00,
            'primary_for' => ['oily', 'blemish', 'spots'],
            'concentration' => 0.10,
            'description_keyword' => 'Oil-Balancing'
        ],
        'salicylic_acid' => [
            'benefit' => 'Unclogs Pores & Controls Breakouts',
            'price' => 12.00,
            'primary_for' => ['blemish', 'oily'],
            'concentration' => 0.02,
            'description_keyword' => 'Pore-Clarifying'
        ],
        'peptides' => [
            'benefit' => 'Reduces Fine Lines & Wrinkles',
            'price' => 15.00,
            'primary_for' => ['wrinkle'],
            'concentration' => 0.05,
            'description_keyword' => 'Anti-Aging'
        ],
        'hyaluronic_acid' => [
            'benefit' => 'Deep Hydration & Plumping',
            'price' => 10.00,
            'primary_for' => ['dry', 'wrinkle'],
            'concentration' => 0.15,
            'description_keyword' => 'Hydrating'
        ],
        'vitamin_c' => [
            'benefit' => 'Brightens & Protects Against Environmental Damage',
            'price' => 13.00,
            'primary_for' => ['spots', 'urban'],
            'concentration' => 0.10,
            'description_keyword' => 'Brightening'
        ],
        'centella_asiatica' => [
            'benefit' => 'Soothes & Calms Irritated Skin',
            'price' => 9.00,
            'primary_for' => ['sensitive', 'soothe'],
            'concentration' => 0.05,
            'description_keyword' => 'Soothing'
        ],
        'alpha_arbutin' => [
            'benefit' => 'Fades Dark Spots & Evens Skin Tone',
            'price' => 11.00,
            'primary_for' => ['spots'],
            'concentration' => 0.02,
            'description_keyword' => 'Tone-Evening'
        ],
        'beta_glucan' => [
            'benefit' => 'Strengthens Skin Barrier & Soothes',
            'price' => 10.00,
            'primary_for' => ['sensitive', 'dry'],
            'concentration' => 0.05,
            'description_keyword' => 'Barrier-Strengthening'
        ]
    ];

    /**
     * Environmental protection mapping
     */
    public const ENVIRONMENTAL_PROTECTION = [
        'urban' => [
            'ingredients' => ['vitamin_c', 'niacinamide'],
            'benefit' => 'Urban Pollution Shield',
            'description_keyword' => 'City-Protecting'
        ],
        'tropical' => [
            'ingredients' => ['beta_glucan', 'centella_asiatica'],
            'benefit' => 'Tropical Climate Protection',
            'description_keyword' => 'Climate-Adapting'
        ],
        'moderate' => [
            'ingredients' => ['peptides', 'hyaluronic_acid'],
            'benefit' => 'Environmental Balance',
            'description_keyword' => 'Balance-Restoring'
        ]
    ];

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($customProduct) {
            if (empty($customProduct->formulation_date)) {
                $customProduct->formulation_date = now();
            }
        });
    }

    /**
     * Get the validation rules for custom product data
     */
    public static function validationRules($isUpdate = false): array
    {
        $rules = [
            'user_id' => [
                $isUpdate ? 'sometimes' : 'required',
                'integer',
                'exists:User,user_id'
            ],
            'base_product_id' => [
                $isUpdate ? 'sometimes' : 'required',
                'integer',
                'exists:Product,product_id'
            ],
            'profile_data' => [
                $isUpdate ? 'sometimes' : 'required',
                'array'
            ],
            'profile_data.skin_type' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                Rule::in(['dry', 'oily', 'combination', 'sensitive'])
            ],
            'profile_data.skin_concerns' => [
                $isUpdate ? 'sometimes' : 'required',
                'array',
                'min:1'
            ],
            'profile_data.skin_concerns.*' => [
                'string',
                Rule::in(['blemish', 'wrinkle', 'spots', 'soothe'])
            ],
            'profile_data.environmental_factors' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                Rule::in(['urban', 'tropical', 'moderate'])
            ],
            'profile_data.allergies' => [
                'sometimes',
                'array'
            ],
            'profile_data.allergies.*' => [
                'string',
                Rule::in(array_keys(self::ALLERGY_CATEGORIES))
            ],
        ];

        return $rules;
    }

    /**
     * Create custom product with intelligent formulation
     */
    public static function createWithFormulation(array $data): self
    {
        return DB::transaction(function () use ($data) {
            // Generate the product composition
            $productComposition = static::generateProductComposition($data['profile_data']);
            
            // Generate product name and description
            $productName = static::generateProductName($data['profile_data']);
            $productDescription = static::generateProductDescription($data['profile_data'], $productComposition);
            
            // Calculate price
            $totalPrice = static::calculateTotalPrice($productComposition['ingredients']);
            
            // Create the custom product
            $customProduct = static::create([
                'user_id' => $data['user_id'],
                'base_product_id' => $data['base_product_id'],
                'profile_data' => $data['profile_data'],
                'total_price' => $totalPrice,
                'selected_ingredients' => $productComposition['ingredients'],
                'final_ingredient_concentrations' => $productComposition['concentrations'],
                'product_name' => $productName,
                'product_description' => $productDescription,
            ]);

            return $customProduct;
        });
    }

    /**
     * Generate product composition based on profile
     */
    public static function generateProductComposition(array $profileData): array
    {
        $allergies = $profileData['allergies'] ?? [];
        
        $composition = [
            'ingredients' => [],
            'concentrations' => [],
            'benefits' => []
        ];

        // Add ingredients based on skin type
        static::addIngredientsForSkinType($composition, $profileData['skin_type'], $allergies);
        
        // Add ingredients for skin concerns
        foreach ($profileData['skin_concerns'] as $concern) {
            static::addIngredientsForConcern($composition, $concern, $allergies);
        }
        
        // Add environmental protection ingredients
        if (isset($profileData['environmental_factors'])) {
            static::addEnvironmentalIngredients($composition, $profileData['environmental_factors'], $allergies);
        }

        return $composition;
    }

    /**
     * Add ingredients for skin type
     */
    protected static function addIngredientsForSkinType(array &$composition, string $skinType, array $allergies): void
    {
        $skinTypeIngredients = [
            'dry' => ['hyaluronic_acid', 'beta_glucan'],
            'oily' => ['niacinamide', 'salicylic_acid'],
            'combination' => ['niacinamide', 'hyaluronic_acid'],
            'sensitive' => ['centella_asiatica', 'beta_glucan']
        ];

        foreach ($skinTypeIngredients[$skinType] ?? [] as $ingredient) {
            if (!static::isAllergicTo($ingredient, $allergies)) {
                static::addIngredient($composition, $ingredient);
            }
        }
    }

    /**
     * Add ingredients for skin concern
     */
    protected static function addIngredientsForConcern(array &$composition, string $concern, array $allergies): void
    {
        $concernIngredients = [
            'blemish' => ['salicylic_acid', 'niacinamide'],
            'wrinkle' => ['peptides', 'vitamin_c'],
            'spots' => ['alpha_arbutin', 'vitamin_c'],
            'soothe' => ['centella_asiatica', 'beta_glucan']
        ];

        foreach ($concernIngredients[$concern] ?? [] as $ingredient) {
            if (!static::isAllergicTo($ingredient, $allergies)) {
                static::addIngredient($composition, $ingredient);
            }
        }
    }

    /**
     * Add environmental protection ingredients
     */
    protected static function addEnvironmentalIngredients(array &$composition, string $factor, array $allergies): void
    {
        $envIngredients = self::ENVIRONMENTAL_PROTECTION[$factor]['ingredients'] ?? [];
        
        foreach ($envIngredients as $ingredient) {
            if (!static::isAllergicTo($ingredient, $allergies)) {
                static::addIngredient($composition, $ingredient);
            }
        }
    }

    /**
     * Add ingredient to composition
     */
    protected static function addIngredient(array &$composition, string $ingredient): void
    {
        if (!in_array($ingredient, $composition['ingredients'])) {
            $composition['ingredients'][] = $ingredient;
            $composition['concentrations'][$ingredient] = self::INGREDIENT_BENEFITS[$ingredient]['concentration'];
            $composition['benefits'][] = self::INGREDIENT_BENEFITS[$ingredient]['benefit'];
        }
    }

    /**
     * Generate product name
     */
    public static function generateProductName(array $profileData): string
    {
        $skinTypePrefix = self::PRODUCT_NAME_PREFIXES['skin_type'][$profileData['skin_type']][
            array_rand(self::PRODUCT_NAME_PREFIXES['skin_type'][$profileData['skin_type']])
        ];
        
        $mainConcern = $profileData['skin_concerns'][0];
        $concernPrefix = self::PRODUCT_NAME_PREFIXES['concern'][$mainConcern][
            array_rand(self::PRODUCT_NAME_PREFIXES['concern'][$mainConcern])
        ];
        
        $type = self::PRODUCT_TYPES[array_rand(self::PRODUCT_TYPES)];
        
        return "CraftedWell {$skinTypePrefix}-{$concernPrefix} {$type}";
    }

    /**
     * Generate product description
     */
    public static function generateProductDescription(array $profileData, array $composition): string
    {
        $skinType = ucfirst($profileData['skin_type']);
        
        // Get primary benefits based on selected ingredients
        $benefitKeywords = array_map(function($ingredient) {
            return self::INGREDIENT_BENEFITS[$ingredient]['description_keyword'];
        }, $composition['ingredients']);
        $benefitKeywords = array_unique($benefitKeywords);

        // Build main targeting phrase
        $targeting = "Precision-Engineered Serum Targeting {$skinType}";
        
        // Add skin concerns
        if (!empty($profileData['skin_concerns'])) {
            $concerns = array_map(function($concern) {
                return ucfirst(str_replace('_', ' ', $concern));
            }, $profileData['skin_concerns']);
            
            if (count($concerns) > 1) {
                $lastConcern = array_pop($concerns);
                $targeting .= ", " . implode(', ', $concerns) . " & {$lastConcern} Prone";
            } else {
                $targeting .= ", {$concerns[0]} Prone";
            }
        }
        $targeting .= " Skin";

        if (isset($profileData['environmental_factors'])) {
            $envFactor = self::ENVIRONMENTAL_PROTECTION[$profileData['environmental_factors']];
            $targeting .= " With {$envFactor['description_keyword']} Technology";
        }

        $benefits = array_slice($benefitKeywords, 0, 3); 
        $benefitsText = "";
        if (count($benefits) > 1) {
            $lastBenefit = array_pop($benefits);
            $benefitsText = implode(', ', $benefits) . " And " . $lastBenefit;
        } else {
            $benefitsText = $benefits[0] ?? 'Advanced';
        }

        return "{$targeting}. {$benefitsText} Formula Enhanced With Advanced Skin-Compatible Technology For Optimal Results.";
    }

    /**
     * Calculate total price
     */
    public static function calculateTotalPrice(array $ingredients): float
    {
        $total = self::BASE_PRICE;

        foreach ($ingredients as $ingredient) {
            if (isset(self::INGREDIENT_BENEFITS[$ingredient])) {
                $total += self::INGREDIENT_BENEFITS[$ingredient]['price'];
            }
        }

        $complexityMultiplier = 1 + (count($ingredients) * 0.05); 
        $total *= $complexityMultiplier;

        return ceil($total * 2) / 2; // Round to nearest 0.50
    }

    /**
     * Check if allergic to ingredient
     */
    protected static function isAllergicTo(string $ingredient, array $allergies): bool
    {
        if (empty($allergies)) return false;

        foreach ($allergies as $allergyCategory) {
            if (isset(self::ALLERGY_CATEGORIES[$allergyCategory])) {
                if (in_array($ingredient, self::ALLERGY_CATEGORIES[$allergyCategory]['avoid'])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get formatted product details
     */
    public function getFormattedDetails(): array
    {
        return [
            'product_id' => $this->custom_product_id,
            'name' => $this->product_name,
            'description' => $this->product_description,
            'total_price' => $this->total_price,
            'size' => '30ML',
            'personalized_for' => $this->formatPersonalizationInfo(),
            'allergy_consideration' => $this->formatAllergyInfo(),
            'solution_description' => $this->product_description,
            'selected_ingredients' => $this->selected_ingredients,
            'concentrations' => $this->final_ingredient_concentrations,
            'benefits' => $this->getBenefits(),
            'formulation_date' => $this->formulation_date,
        ];
    }

    /**
     * Get benefits from ingredients
     */
    public function getBenefits(): array
    {
        $benefits = [];
        foreach ($this->selected_ingredients as $ingredient) {
            if (isset(self::INGREDIENT_BENEFITS[$ingredient])) {
                $benefits[] = self::INGREDIENT_BENEFITS[$ingredient]['benefit'];
            }
        }
        return array_unique($benefits);
    }

    /**
     * Format personalization information
     */
    public function formatPersonalizationInfo(): string
    {
        $profile = $this->profile_data;
        
        $info = ucfirst($profile['skin_type']) . " Skin";
        
        // Add concerns
        if (!empty($profile['skin_concerns'])) {
            $concerns = array_map(function($concern) {
                return ucfirst(str_replace('_', ' ', $concern));
            }, $profile['skin_concerns']);
            
            $info .= " with " . implode(' & ', $concerns) . " Concerns";
        }

        return $info;
    }

    /**
     * Format allergy information
     */
    public function formatAllergyInfo(): string
    {
        $profile = $this->profile_data;
        if (empty($profile['allergies'])) {
            return "No Specific Allergies";
        }

        $allergies = array_map(function($allergy) {
            return self::ALLERGY_CATEGORIES[$allergy]['description'];
        }, $profile['allergies']);

        return "Formulated Without: " . implode(', ', $allergies);
    }

    /**
     * Update product with new profile data
     */
    public function updateWithNewProfile(array $profileData): bool
    {
        return DB::transaction(function () use ($profileData) {
            // Regenerate product composition
            $productComposition = static::generateProductComposition($profileData);
            $productName = static::generateProductName($profileData);
            $productDescription = static::generateProductDescription($profileData, $productComposition);
            $totalPrice = static::calculateTotalPrice($productComposition['ingredients']);
            
            return $this->update([
                'profile_data' => $profileData,
                'total_price' => $totalPrice,
                'selected_ingredients' => $productComposition['ingredients'],
                'final_ingredient_concentrations' => $productComposition['concentrations'],
                'product_name' => $productName,
                'product_description' => $productDescription,
            ]);
        });
    }

    /**
     * Get allergy alternatives
     */
    public static function getAllergyAlternatives(): array
    {
        return self::ALLERGY_CATEGORIES;
    }

    /**
     * Scope for products by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent products
     */
    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('formulation_date', 'desc')->limit($limit);
    }

    /**
     * Get user's custom products count
     */
    public static function getUserProductsCount($userId): int
    {
        return static::forUser($userId)->count();
    }

    /**
     * Get recent custom products for user
     */
    public static function getRecentForUser($userId, $limit = 5)
    {
        return static::forUser($userId)
            ->recent($limit)
            ->get()
            ->map(fn($product) => $product->getFormattedDetails());
    }

    /**
     * Relationships
     */

    /**
     * Custom product belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Custom product belongs to a base product
     */
    public function baseProduct()
    {
        return $this->belongsTo(Product::class, 'base_product_id', 'product_id');
    }

    /**
     * Custom product can be in many order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'custom_product_id', 'custom_product_id');
    }

    /**
     * Custom product can be in many orders through order items
     */
    public function orders()
    {
        return $this->hasManyThrough(
            Order::class,
            OrderItem::class,
            'custom_product_id',
            'order_id',
            'custom_product_id',
            'order_id'
        );
    }
}