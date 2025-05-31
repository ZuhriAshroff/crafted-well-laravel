<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class Order extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'orders';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'order_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'order_date',
        'total_amount',
        'payment_status',
        'shipping_status',
        'delivery_method',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Valid status options
     */
    public const PAYMENT_STATUSES = ['pending', 'paid', 'failed', 'refunded'];
    public const SHIPPING_STATUSES = ['processing', 'shipped', 'delivered', 'cancelled'];
    public const DELIVERY_METHODS = ['standard', 'express', 'pickup'];

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_date)) {
                $order->order_date = now();
            }
            if (empty($order->payment_status)) {
                $order->payment_status = 'pending';
            }
            if (empty($order->shipping_status)) {
                $order->shipping_status = 'processing';
            }
        });

        static::deleting(function ($order) {
            // Delete order items when order is deleted
            $order->orderItems()->delete();
        });
    }

    /**
     * Get the validation rules for order data
     */
    public static function validationRules($isUpdate = false): array
    {
        $rules = [
            'user_id' => [
                $isUpdate ? 'sometimes' : 'required',
                'integer',
                'exists:User,user_id'
            ],
            'total_amount' => [
                $isUpdate ? 'sometimes' : 'required',
                'numeric',
                'min:0',
                'max:99999.99'
            ],
            'payment_status' => [
                'sometimes',
                Rule::in(self::PAYMENT_STATUSES)
            ],
            'shipping_status' => [
                'sometimes',
                Rule::in(self::SHIPPING_STATUSES)
            ],
            'delivery_method' => [
                $isUpdate ? 'sometimes' : 'required',
                Rule::in(self::DELIVERY_METHODS)
            ],
            'items' => [
                $isUpdate ? 'sometimes' : 'required',
                'array',
                'min:1'
            ],
            'items.*.product_id' => [
                'nullable',
                'integer',
                'exists:Product,product_id'
            ],
            'items.*.custom_product_id' => [
                'nullable',
                'integer',
                'exists:CustomProduct,custom_product_id'
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:100'
            ],
            'items.*.product_price' => [
                'required',
                'numeric',
                'min:0'
            ],
            'items.*.customization_details' => [
                'nullable',
                'string',
                'max:1000'
            ],
        ];

        return $rules;
    }

    /**
     * Create order with items in transaction
     */
    public static function createWithItems(array $orderData): self
    {
        return DB::transaction(function () use ($orderData) {
            // Extract items data
            $items = $orderData['items'];
            unset($orderData['items']);

            // Create the order
            $order = static::create($orderData);

            // Create order items
            foreach ($items as $itemData) {
                $order->orderItems()->create([
                    'product_id' => $itemData['product_id'] ?? null,
                    'custom_product_id' => $itemData['custom_product_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'product_price' => $itemData['product_price'],
                    'customization_details' => $itemData['customization_details'] ?? null,
                ]);
            }

            // Load relationships for return
            $order->load(['orderItems.product', 'orderItems.customProduct', 'user']);

            return $order;
        });
    }

    /**
     * Update order with allowed fields only
     */
    public function updateOrder(array $updateData): bool
    {
        $allowedFields = ['payment_status', 'shipping_status', 'delivery_method'];
        
        $filteredData = array_intersect_key($updateData, array_flip($allowedFields));
        
        if (empty($filteredData)) {
            throw new \InvalidArgumentException("No valid fields to update");
        }

        return $this->update($filteredData);
    }

    /**
     * Scope for orders by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for orders by status
     */
    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeByShippingStatus($query, $status)
    {
        return $query->where('shipping_status', $status);
    }

    /**
     * Scope for recent orders
     */
    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('order_date', 'desc')->limit($limit);
    }

    /**
     * Scope for orders within date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }

    /**
     * Get order with full details
     */
    public function getFullDetails(): array
    {
        $this->load([
            'user:user_id,first_name,last_name,email',
            'orderItems.product:product_id,product_name,base_category',
            'orderItems.customProduct:custom_product_id,product_name'
        ]);

        return [
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'user_email' => $this->user->email ?? null,
            'user_name' => $this->user ? $this->user->name : null,
            'order_date' => $this->order_date,
            'total_amount' => $this->total_amount,
            'payment_status' => $this->payment_status,
            'shipping_status' => $this->shipping_status,
            'delivery_method' => $this->delivery_method,
            'items' => $this->orderItems->map(function ($item) {
                return [
                    'order_item_id' => $item->order_item_id,
                    'product_id' => $item->product_id,
                    'custom_product_id' => $item->custom_product_id,
                    'product_name' => $item->product->product_name ?? $item->customProduct->product_name ?? null,
                    'quantity' => $item->quantity,
                    'product_price' => $item->product_price,
                    'customization_details' => $item->customization_details,
                    'line_total' => $item->quantity * $item->product_price,
                ];
            })->toArray(),
            'item_count' => $this->orderItems->count(),
            'total_items' => $this->orderItems->sum('quantity'),
        ];
    }

    /**
     * Calculate total amount from items
     */
    public function calculateTotalAmount(): float
    {
        return $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->product_price;
        });
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->payment_status, ['pending', 'failed']) && 
               in_array($this->shipping_status, ['processing']);
    }

    /**
     * Check if order can be refunded
     */
    public function canBeRefunded(): bool
    {
        return $this->payment_status === 'paid' && 
               in_array($this->shipping_status, ['processing', 'shipped']);
    }

    /**
     * Get order status for display
     */
    public function getDisplayStatus(): string
    {
        if ($this->payment_status === 'failed') {
            return 'Payment Failed';
        }
        
        if ($this->shipping_status === 'cancelled') {
            return 'Cancelled';
        }
        
        if ($this->payment_status === 'refunded') {
            return 'Refunded';
        }
        
        return match($this->shipping_status) {
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            default => ucfirst($this->shipping_status)
        };
    }

    /**
     * Static methods for analytics
     */
    public static function getTotalCount(): int
    {
        return static::count();
    }

    public static function getRecentOrders($limit = 5)
    {
        return static::with(['user:user_id,first_name,last_name,email'])
            ->recent($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'order_id' => $order->order_id,
                    'user_email' => $order->user->email ?? 'N/A',
                    'user_name' => $order->user ? $order->user->name : 'N/A',
                    'order_date' => $order->order_date,
                    'total_amount' => $order->total_amount,
                    'payment_status' => $order->payment_status,
                    'shipping_status' => $order->shipping_status,
                    'display_status' => $order->getDisplayStatus(),
                ];
            });
    }

    public static function getOrdersByStatus(): array
    {
        return [
            'payment_status' => static::selectRaw('payment_status, COUNT(*) as count')
                ->groupBy('payment_status')
                ->pluck('count', 'payment_status')
                ->toArray(),
            'shipping_status' => static::selectRaw('shipping_status, COUNT(*) as count')
                ->groupBy('shipping_status')
                ->pluck('count', 'shipping_status')
                ->toArray(),
        ];
    }

    public static function getRevenueStats(): array
    {
        return [
            'total_revenue' => static::where('payment_status', 'paid')->sum('total_amount'),
            'pending_revenue' => static::where('payment_status', 'pending')->sum('total_amount'),
            'average_order_value' => static::where('payment_status', 'paid')->avg('total_amount'),
            'orders_count' => static::count(),
            'paid_orders_count' => static::where('payment_status', 'paid')->count(),
        ];
    }

    /**
     * Get status options for forms
     */
    public static function getPaymentStatusOptions(): array
    {
        return array_combine(self::PAYMENT_STATUSES, array_map('ucfirst', self::PAYMENT_STATUSES));
    }

    public static function getShippingStatusOptions(): array
    {
        return array_combine(self::SHIPPING_STATUSES, array_map('ucfirst', self::SHIPPING_STATUSES));
    }

    public static function getDeliveryMethodOptions(): array
    {
        return array_combine(self::DELIVERY_METHODS, array_map('ucfirst', self::DELIVERY_METHODS));
    }

    /**
     * Relationships
     */

    /**
     * Order belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Order has many order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Get all products in this order (through order items)
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'OrderItem', 'order_id', 'product_id')
            ->withPivot(['quantity', 'product_price', 'customization_details'])
            ->withTimestamps();
    }

    /**
     * Get all custom products in this order
     */
    public function customProducts()
    {
        return $this->belongsToMany(CustomProduct::class, 'OrderItem', 'order_id', 'custom_product_id')
            ->withPivot(['quantity', 'product_price', 'customization_details'])
            ->withTimestamps();
    }
}