<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Product extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'products';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'product_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_name',
        'base_category',
        'product_type',
        'standard_price',
        'customization_price_modifier',
        'base_formulation_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'standard_price' => 'decimal:2',
        'customization_price_modifier' => 'decimal:2',
        'base_formulation_id' => 'integer',
    ];

    /**
     * Valid categories and types
     */
    public const VALID_BASE_CATEGORIES = [
        'skincare', 'cleanser', 'moisturizer', 'serum', 'treatment', 'sunscreen'
    ];

    public const VALID_PRODUCT_TYPES = [
        'standard', 'premium', 'custom', 'limited_edition', 'subscription'
    ];

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            // Set default values if not provided
            if (empty($product->customization_price_modifier)) {
                $product->customization_price_modifier = 0.00;
            }
        });
    
        static::deleting(function ($product) {
            // Fix: Check for existing custom products before deletion with correct foreign key
            if ($product->customProducts()->count() > 0) {
                throw new \Exception("Cannot delete product with existing custom products");
            }
        });
    }

    /**
     * Get the validation rules for product data
     */
    public static function validationRules($isUpdate = false): array
    {
        $rules = [
            'product_name' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:255'
            ],
            'base_category' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:100',
                Rule::in(self::VALID_BASE_CATEGORIES)
            ],
            'product_type' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:100',
                Rule::in(self::VALID_PRODUCT_TYPES)
            ],
            'standard_price' => [
                $isUpdate ? 'sometimes' : 'required',
                'numeric',
                'min:0',
                'max:99999.99'
            ],
            'customization_price_modifier' => [
                $isUpdate ? 'sometimes' : 'required',
                'numeric',
                'min:0',
                'max:99999.99'
            ],
            'base_formulation_id' => [
                $isUpdate ? 'sometimes' : 'required',
                'integer',
                'exists:base_formulations,base_formulation_id'
            ],
        ];

        return $rules;
    }

    /**
     * Scope for products by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('base_category', $category);
    }

    /**
     * Scope for products by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('product_type', $type);
    }

    /**
     * Scope for products by price range
     */
    public function scopeByPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('standard_price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('standard_price', '<=', $maxPrice);
        }
        return $query;
    }

    /**
     * Scope for recent products
     */
    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('product_id', 'desc')->limit($limit);
    }

    /**
     * Scope for products suitable for skin type
     */
    public function scopeForSkinType($query, $skinType)
    {
        return $query->where('base_category', $skinType)
                    ->orWhereHas('baseFormulation', function ($q) use ($skinType) {
                        $q->where('suitable_skin_types', 'like', "%{$skinType}%");
                    });
    }

    /**
     * Get total products count
     */
    public static function getTotalCount(): int
    {
        return static::count();
    }

    /**
     * Get recent products
     */
    public static function getRecentProducts($limit = 5)
    {
        return static::with('baseFormulation')
            ->recent($limit)
            ->get();
    }

    /**
     * Get products by category with counts
     */
    public static function getProductsByCategory(): array
    {
        return static::selectRaw('base_category, COUNT(*) as count')
            ->groupBy('base_category')
            ->pluck('count', 'base_category')
            ->toArray();
    }

    /**
     * Get price statistics
     */
    public static function getPriceStatistics(): array
    {
        return [
            'min_price' => static::min('standard_price'),
            'max_price' => static::max('standard_price'),
            'avg_price' => static::avg('standard_price'),
            'total_products' => static::count(),
        ];
    }

    /**
     * Calculate final price with customization
     */
    public function getFinalPrice($isCustom = false): float
    {
        $basePrice = (float) $this->standard_price;
        return $isCustom ? $basePrice + (float) $this->customization_price_modifier : $basePrice;
    }

    /**
     * Check if product can be customized
     */
    public function isCustomizable(): bool
    {
        return $this->customization_price_modifier > 0 && 
               $this->baseFormulation && 
               $this->baseFormulation->allows_customization;
    }

    /**
     * Get formatted product data for API responses
     */
    public function getFormattedData(): array
    {
        return [
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'base_category' => $this->base_category,
            'product_type' => $this->product_type,
            'standard_price' => $this->standard_price,
            'customization_price_modifier' => $this->customization_price_modifier,
            'base_formulation_id' => $this->base_formulation_id,
            'is_customizable' => $this->isCustomizable(),
        ];
    }

    /**
     * Search products by name or category
     */
    public static function search($query, $category = null, $priceRange = null)
    {
        $search = static::query();

        if ($query) {
            $search->where('product_name', 'like', "%{$query}%");
        }

        if ($category) {
            $search->byCategory($category);
        }

        if ($priceRange) {
            $search->byPriceRange($priceRange['min'] ?? null, $priceRange['max'] ?? null);
        }

        return $search->with('baseFormulation')->get();
    }

    /**
     * Get recommended products for a user profile
     */
    public static function getRecommendationsForProfile($userProfile)
    {
        return static::forSkinType($userProfile->skin_type)
            ->whereHas('baseFormulation', function ($query) use ($userProfile) {
                $query->where('suitable_skin_types', 'like', "%{$userProfile->skin_type}%")
                      ->where('target_concerns', 'like', "%{$userProfile->primary_skin_concerns}%");
            })
            ->with('baseFormulation')
            ->get();
    }

    /**
     * Get category options for forms
     */
    public static function getCategoryOptions(): array
    {
        return array_combine(
            self::VALID_BASE_CATEGORIES,
            array_map('ucfirst', self::VALID_BASE_CATEGORIES)
        );
    }

    /**
     * Get type options for forms
     */
    public static function getTypeOptions(): array
    {
        return array_combine(
            self::VALID_PRODUCT_TYPES,
            array_map(function ($type) {
                return str_replace('_', ' ', ucfirst($type));
            }, self::VALID_PRODUCT_TYPES)
        );
    }

    /**
     * Relationships
     */

    /**
     * Product belongs to a base formulation
     */
    public function baseFormulation()
    {
        return $this->belongsTo(BaseFormulation::class, 'base_formulation_id', 'base_formulation_id');
    }

    /**
     * Product has many custom products
     */
    public function customProducts()
    {
        return $this->hasMany(CustomProduct::class, 'base_product_id', 'product_id');
    }

    /**
     * Product has many order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    /**
     * Product can be in many orders through order items
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'OrderItem', 'product_id', 'order_id')
            ->withPivot(['quantity', 'unit_price', 'customization_details'])
            ->withTimestamps();
    }

    /**
     * Get products that are frequently bought together
     */
    public function getFrequentlyBoughtTogether($limit = 3)
    {
        return static::whereHas('orders', function ($query) {
            $query->whereIn('order_id', function ($subQuery) {
                $subQuery->select('order_id')
                    ->from('OrderItem')
                    ->where('product_id', $this->product_id);
            });
        })
        ->where('product_id', '!=', $this->product_id)
        ->withCount('orders')
        ->orderBy('orders_count', 'desc')
        ->limit($limit)
        ->get();
    }

    /**
     * Get sales statistics for this product
     */
    public function getSalesStats()
    {
        return [
            'total_sales' => $this->orderItems()->sum('quantity'),
            'total_revenue' => $this->orderItems()->sum(\DB::raw('quantity * unit_price')),
            'average_order_quantity' => $this->orderItems()->avg('quantity'),
            'custom_product_count' => $this->customProducts()->count(),
        ];
    }
}