<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'system_stock',
        'actual_stock',
        'difference',
        'notes',
    ];

    /**
     * Relationship with stock opname
     */
    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    /**
     * Relationship with product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate and set difference
     */
    public function calculateDifference(): void
    {
        $this->difference = $this->actual_stock - $this->system_stock;
    }

    /**
     * Get difference status
     */
    public function getDifferenceStatusAttribute(): string
    {
        if ($this->difference > 0) {
            return 'surplus';
        } elseif ($this->difference < 0) {
            return 'deficit';
        }
        return 'match';
    }

    /**
     * Get difference badge color
     */
    public function getDifferenceColorAttribute(): string
    {
        return match($this->difference_status) {
            'surplus' => 'success',
            'deficit' => 'danger',
            'match' => 'gray',
            default => 'gray',
        };
    }
}
