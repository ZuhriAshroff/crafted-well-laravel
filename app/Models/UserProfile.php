<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class UserProfile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'user_profiles';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'profile_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'skin_type',
        'primary_skin_concerns',
        'secondary_skin_concerns',
        'allergies',
        'environmental_factors',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'secondary_skin_concerns' => 'array',
        'allergies' => 'array',
    ];

    /**
     * Valid options for ENUM fields
     */
    public const VALID_SKIN_TYPES = ['dry', 'oily', 'combination', 'sensitive'];
    public const VALID_SKIN_CONCERNS = ['blemish', 'wrinkle', 'spots', 'soothe'];
    public const VALID_ENVIRONMENTAL_FACTORS = ['urban', 'tropical', 'moderate'];

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($profile) {
            if (empty($profile->secondary_skin_concerns)) {
                $profile->secondary_skin_concerns = [];
            }
            if (empty($profile->allergies)) {
                $profile->allergies = [];
            }
        });
    }

/**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function customProducts()
    {
        return $this->hasMany(CustomProduct::class, 'profile_id', 'profile_id');
    }

    /**
     * Validation Rules
     */
    public static function validationRules($isUpdate = false): array
    {
        $rules = [
            'id' => $isUpdate ? 'sometimes|exists:User,id' : 'required|exists:User,id',
            'skin_type' => [
                $isUpdate ? 'sometimes' : 'required',
                Rule::in(self::VALID_SKIN_TYPES)
            ],
            'primary_skin_concerns' => [
                $isUpdate ? 'sometimes' : 'required',
                Rule::in(self::VALID_SKIN_CONCERNS)
            ],
            'secondary_skin_concerns' => [
                $isUpdate ? 'sometimes' : 'required',
                'array'
            ],
            'secondary_skin_concerns.*' => [
                Rule::in(self::VALID_SKIN_CONCERNS)
            ],
            'environmental_factors' => [
                $isUpdate ? 'sometimes' : 'required',
                Rule::in(self::VALID_ENVIRONMENTAL_FACTORS)
            ],
            'allergies' => 'sometimes|array',
            'allergies.*' => 'string|max:255',
        ];

        return $rules;
    }

    /**
     * Query Scopes
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('id', $userId);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('profile_id', 'desc');
    }

    /**
     * Helper Methods
     */
    public function isComplete(): bool
    {
        return !empty($this->skin_type) &&
               !empty($this->primary_skin_concerns) &&
               !empty($this->secondary_skin_concerns) &&
               !empty($this->environmental_factors);
    }

    public function getCompletionPercentage(): int
    {
        $requiredFields = ['skin_type', 'primary_skin_concerns', 'secondary_skin_concerns', 'environmental_factors'];
        $completedFields = 0;

        foreach ($requiredFields as $field) {
            if (!empty($this->$field)) {
                $completedFields++;
            }
        }

        return (int) (($completedFields / count($requiredFields)) * 100);
    }

    public function getFormattedData(): array
    {
        return [
            'profile_id' => $this->profile_id,
            'id' => $this->id,
            'skin_type' => $this->skin_type,
            'primary_skin_concerns' => $this->primary_skin_concerns,
            'secondary_skin_concerns' => $this->secondary_skin_concerns,
            'allergies' => $this->allergies,
            'environmental_factors' => $this->environmental_factors,
        ];
    }

    /**
     * Static Helper Methods
     */
    public static function getLatestForUser($userId)
    {
        return static::forUser($userId)->latest()->first();
    }

    public static function createOrUpdateForUser($userId, array $data): self
    {
        $existingProfile = static::getLatestForUser($userId);
        
        if ($existingProfile) {
            $existingProfile->update($data);
            return $existingProfile;
        }

        $data['id'] = $userId;
        return static::create($data);
    }

    public static function getSkinTypeOptions(): array
    {
        return array_combine(self::VALID_SKIN_TYPES, array_map('ucfirst', self::VALID_SKIN_TYPES));
    }

    public static function getSkinConcernsOptions(): array
    {
        return array_combine(self::VALID_SKIN_CONCERNS, array_map('ucfirst', self::VALID_SKIN_CONCERNS));
    }

    public static function getEnvironmentalFactorsOptions(): array
    {
        return array_combine(self::VALID_ENVIRONMENTAL_FACTORS, array_map('ucfirst', self::VALID_ENVIRONMENTAL_FACTORS));
    }

    /**
     * Get the validation rules for user profile data
     */
    public static function skinTypeValidationRules(): array
    {
        return [
            'skin_type' => ['required', Rule::in(self::VALID_SKIN_TYPES)]
        ];
    }

    public static function skinConcernsValidationRules(): array
    {
        return [
            'primary_skin_concerns' => ['required', Rule::in(self::VALID_SKIN_CONCERNS)],
            'secondary_skin_concerns' => ['required', 'array'],
            'secondary_skin_concerns.*' => [Rule::in(self::VALID_SKIN_CONCERNS)],
        ];
    }

    public static function environmentalFactorsValidationRules(): array
    {
        return [
            'environmental_factors' => ['required', Rule::in(self::VALID_ENVIRONMENTAL_FACTORS)]
        ];
    }

    /**
     * Get recommended products based on profile
     */
    public function getRecommendedProducts()
    {
        return Product::where('base_category', $this->skin_type)
                     ->orWhereJsonContains('target_concerns', $this->primary_skin_concerns)
                     ->get();
    }
}