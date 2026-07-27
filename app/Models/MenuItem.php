<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'name',
        'image',
        'image_alt',
        'rating',
        'time',
        'description',
        'price',
        'category',
        'category_id',
        'popular',
        'status',
    ];

    protected $casts = [
        'price'   => 'decimal:2',
        'rating'  => 'decimal:1',
        'popular' => 'boolean',
    ];

    /**
     * Get the category this menu item belongs to.
     */
    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the order items for this menu item.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if menu item is available.
     */
    public function getIsAvailableAttribute(): bool
    {
        return ($this->status ?? 'available') === 'available';
    }
}
