<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_name',
        'table_number',
        'restaurant_table_id',
        'status',
        'order_type',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'total_price'    => 'decimal:2',
    ];

    // ─── Boot ────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->invoice_number)) {
                $order->invoice_number = static::generateInvoice();
            }
        });
    }

    /**
     * Generate a unique invoice number.
     * Uses lockForUpdate() inside a transaction to prevent duplicate numbers
     * under concurrent requests (race condition fix).
     */
    public static function generateInvoice(): string
    {
        $prefix = now()->format('ymd');
        $last   = static::where('invoice_number', 'like', "{$prefix}%")
                        ->lockForUpdate()
                        ->orderByDesc('id')
                        ->value('invoice_number');

        $seq = $last ? ((int) substr($last, 6)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function restaurantTable(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // ─── Query Scopes ────────────────────────────────────────────────────────

    public function scopeQrOrders(Builder $query): Builder
    {
        return $query->where('order_type', 'self');
    }

    public function scopeCashierOrders(Builder $query): Builder
    {
        return $query->where('order_type', 'cashier');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Tunggu Konfirmasi',
            'preparing' => 'Disiapkan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'schedule',
            'preparing' => 'skillet',
            'completed' => 'task_alt',
            'cancelled' => 'cancel',
            default     => 'info',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'yellow',
            'preparing' => 'blue',
            'completed' => 'primary',
            'cancelled' => 'red',
            default     => 'gray',
        };
    }

    public function getOrderTypeLabelAttribute(): string
    {
        return match ($this->order_type) {
            'self'    => 'Pesanan QR',
            'cashier' => 'Manual',
            default   => ucfirst($this->order_type ?? 'Pesanan QR'),
        };
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
