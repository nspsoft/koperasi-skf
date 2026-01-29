<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    protected $fillable = [
        'opname_number',
        'opname_date',
        'status',
        'notes',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'opname_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Boot method to generate opname number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->opname_number)) {
                $model->opname_number = self::generateOpnameNumber();
            }
        });
    }

    /**
     * Generate unique opname number
     */
    public static function generateOpnameNumber(): string
    {
        $prefix = 'SO' . date('Ymd');
        $lastOpname = self::where('opname_number', 'like', $prefix . '%')
            ->orderBy('opname_number', 'desc')
            ->first();

        if ($lastOpname) {
            $lastNumber = (int) substr($lastOpname->opname_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Relationship with creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with items
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => __('messages.stock_opname.status_draft'),
            'completed' => __('messages.stock_opname.status_completed'),
            'cancelled' => __('messages.stock_opname.status_cancelled'),
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color for badge
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get total items count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Get items with difference count
     */
    public function getItemsWithDifferenceAttribute(): int
    {
        return $this->items()->where('difference', '!=', 0)->count();
    }
}
