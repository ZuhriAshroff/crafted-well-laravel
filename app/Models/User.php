<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'User';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'password_hash',
        'role',
        'account_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'registration_date' => 'datetime',
        'last_login' => 'datetime',
        'account_status' => 'boolean',
    ];

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (empty($user->registration_date)) {
                $user->registration_date = now();
            }
            if (empty($user->account_status)) {
                $user->account_status = true;
            }
            if (empty($user->role)) {
                $user->role = 'user';
            }
        });
    }

    /**
     * Accessor for password field
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->password_hash,
            set: fn ($value) => ['password_hash' => bcrypt($value)],
        );
    }

    /**
     * Accessor for name field
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->first_name . ' ' . $this->last_name,
            set: function ($value) {
                $nameParts = explode(' ', $value, 2);
                return [
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1] ?? '',
                ];
            }
        );
    }

    /**
     * Relationships
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function customProducts()
    {
        return $this->hasMany(CustomProduct::class, 'user_id', 'user_id');
    }

    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class, 'user_id', 'user_id');
    }

    /**
     * Helper Methods
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->account_status == 1;
    }

    public function updateLastLogin(): bool
    {
        $this->last_login = now();
        return $this->save();
    }

    /**
     * Query Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('account_status', 1);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('registration_date', 'desc')->limit($limit);
    }

    /**
     * Authentication Methods
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get total users count
     */
    public static function getTotalCount(): int
    {
        return static::count();
    }

    /**
     * Get active users count
     */
    public static function getActiveUsersCount(): int
    {
        return static::active()->count();
    }

    /**
     * Get users count by role
     */
    public static function getUsersByRole(): array
    {
        return static::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();
    }

    /**
     * Get recent users
     */
    public static function getRecentUsers($limit = 5)
    {
        return static::select([
                'user_id',
                'first_name', 
                'last_name',
                'email',
                'registration_date',
                'last_login',
                'role',
                'account_status'
            ])
            ->recent($limit)
            ->get();
    }

    /**
     * Find user by email (active only)
     */
    public static function findByEmail(string $email)
    {
        return static::select([
                'user_id',
                'first_name',
                'last_name', 
                'email',
                'phone_number',
                'registration_date',
                'last_login',
                'account_status'
            ])
            ->where('email', $email)
            ->active()
            ->first();
    }

    /**
     * Find user by email with password hash (for authentication)
     */
    public static function findByEmailWithHash(string $email)
    {
        return static::where('email', $email)->first();
    }
}