<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id', 'reference_number', 'purchase_date', 
        'total_amount', 'status', 'note', 'created_by', 'completed_at', 'receipt_image'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'completed_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => __('messages.purchases.status_pending'),
            'completed' => __('messages.purchases.status_completed'),
            'cancelled' => __('messages.purchases.status_cancelled'),
            default => $this->status
        };
    }

    public function journalEntry()
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }
}
