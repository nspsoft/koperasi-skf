<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntryLine extends Model
{
    protected $fillable = [
        'journal_entry_id', 'account_id', 'debit', 'credit', 'description'
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    /**
     * Get parent journal entry
     */
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Get account
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the amount (debit or credit)
     */
    public function getAmountAttribute()
    {
        return $this->debit > 0 ? $this->debit : $this->credit;
    }

    /**
     * Get whether this is a debit or credit
     */
    public function getTypeAttribute()
    {
        return $this->debit > 0 ? 'debit' : 'credit';
    }
}
