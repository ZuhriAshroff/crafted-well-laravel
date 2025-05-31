<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'order_items';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'order_item_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'custom_product_id',
        'quantity',
        'product_price',
        'customization_details',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'product_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Get line total (quantity × price)
     */
    public function getLineTotalAttribute(): float
    {
        return $this->quantity * $this->product_price;
    }

    /**
     * Relationships
     */

    /**
     * Order item belongs to an order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Order item belongs to a product (nullable)
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Order item belongs to a custom product (nullable)
     */
    public function customProduct()
    {
        return $this->belongsTo(CustomProduct::class, 'custom_product_id', 'custom_product_id');
    }

    /**
     * Get the product name (from either product or custom product)
     */
    public function getProductNameAttribute(): ?string
    {
        return $this->product->product_name ?? $this->customProduct->product_name ?? 'Unknown Product';
    }
}