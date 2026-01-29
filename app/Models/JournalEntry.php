<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalEntry extends Model
{
    protected $fillable = [
        'journal_number', 'reference_type', 'reference_id', 'transaction_date',
        'description', 'total_debit', 'total_credit', 'status',
        'created_by', 'posted_by', 'posted_at'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'posted_at' => 'datetime',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    /**
     * Get the source transaction (polymorphic)
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get journal entry lines
     */
    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    /**
     * Creator of journal
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who posted the journal
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Check if journal is balanced (debit = credit)
     */
    public function isBalanced(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01; // tolerance for float precision
    }

    /**
     * Generate journal number
     */
    public static function generateJournalNumber(): string
    {
        $prefix = 'JRN-' . date('Ymd') . '-';
        $lastJournal = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastJournal && str_starts_with($lastJournal->journal_number, $prefix)) {
            $lastNumber = (int) substr($lastJournal->journal_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope: Only posted journals
     */
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }
}
