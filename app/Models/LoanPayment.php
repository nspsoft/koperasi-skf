<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'payment_number',
        'installment_number',
        'amount',
        'principal_amount',
        'interest_amount',
        'due_date',
        'payment_date',
        'status',
        'payment_method',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    /**
     * Get the loan that owns this payment.
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get the user who received this payment.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Generate payment number.
     */
    public static function generatePaymentNumber()
    {
        $prefix = 'PAY' . date('Ymd');
        $lastPayment = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPayment && str_starts_with($lastPayment->payment_number, $prefix)) {
            $lastNumber = (int) substr($lastPayment->payment_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Belum Dibayar',
            'paid' => 'Lunas',
            'overdue' => 'Terlambat',
            'partial' => 'Sebagian',
            default => $this->status,
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'paid' => 'green',
            'overdue' => 'red',
            'partial' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get payment method label.
     */
    public function getPaymentMethodLabelAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'salary_deduction' => 'Potong Gaji',
            default => $this->payment_method,
        };
    }

    public function journalEntry()
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }
}
