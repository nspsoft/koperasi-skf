<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saving extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'type',
        'transaction_type',
        'amount',
        'transaction_date',
        'reference_number',
        'description',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the member that owns the saving.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who created this transaction.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate reference number.
     */
    public static function generateReferenceNumber()
    {
        $prefix = 'SMP' . date('Ymd');
        $lastSaving = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSaving && str_starts_with($lastSaving->reference_number, $prefix)) {
            $lastNumber = (int) substr($lastSaving->reference_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get type label in Indonesian.
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'pokok' => 'Simpanan Pokok',
            'wajib' => 'Simpanan Wajib',
            'sukarela' => 'Simpanan Sukarela',
            default => $this->type,
        };
    }

    /**
     * Get transaction type label.
     */
    public function getTransactionTypeLabelAttribute()
    {
        return match($this->transaction_type) {
            'deposit' => 'Setoran',
            'withdrawal' => 'Penarikan',
            default => $this->transaction_type,
        };
    }

    /**
     * Get the related journal entry.
     */
    public function journalEntry()
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }
}
