<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'loan_number',
        'loan_type',
        'amount',
        'interest_rate',
        'duration_months',
        'monthly_installment',
        'total_amount',
        'remaining_amount',
        'status',
        'application_date',
        'approval_date',
        'disbursement_date',
        'due_date',
        'purpose',
        'notes',
        'approved_by',
        'created_by',
        'disbursed_by',
        'signature',
        'signed_by',
        'signed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'application_date' => 'date',
        'approval_date' => 'date',
        'disbursement_date' => 'date',
        'due_date' => 'date',
        'signed_at' => 'datetime',
    ];

    /**
     * Get the member that owns the loan.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get all payments for this loan.
     */
    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }

    /**
     * Get the user who approved this loan.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who created this loan.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who disbursed this loan.
     */
    public function disburser()
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    /**
     * Get the user who signed this loan.
     */
    public function signer()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    /**
     * Generate loan number.
     */
    public static function generateLoanNumber()
    {
        $prefix = 'PNJ' . date('Ymd');
        $lastLoan = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastLoan && str_starts_with($lastLoan->loan_number, $prefix)) {
            $lastNumber = (int) substr($lastLoan->loan_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate loan details (monthly installment, total amount).
     */
    public function calculateLoanDetails()
    {
        // Simple interest calculation
        $totalInterest = ($this->amount * $this->interest_rate / 100) * $this->duration_months;
        $this->total_amount = $this->amount + $totalInterest;
        $this->monthly_installment = $this->total_amount / $this->duration_months;
        $this->remaining_amount = $this->total_amount;
    }

    /**
     * Get loan type label in Indonesian.
     */
    public function getLoanTypeLabelAttribute()
    {
        return match($this->loan_type) {
            'regular' => 'Pinjaman Reguler',
            'emergency' => 'Pinjaman Darurat',
            'education' => 'Pinjaman Pendidikan',
            'special' => 'Pinjaman Khusus',
            default => $this->loan_type,
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute()
    {
        if ($this->status === 'approved') {
            return $this->signature ? 'Siap Dicairkan (Sudah TTD)' : 'Menunggu Tanda Tangan';
        }

        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'rejected' => 'Ditolak',
            'active' => 'Aktif',
            'completed' => 'Lunas',
            'defaulted' => 'Macet',
            default => $this->status,
        };
    }

    /**
     * Get status color class.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'active' => 'success',
            'completed' => 'gray',
            'defaulted' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get payment progress percentage.
     */
    public function getPaymentProgressAttribute()
    {
        if ($this->total_amount == 0) return 0;
        $paid = $this->total_amount - $this->remaining_amount;
        return round(($paid / $this->total_amount) * 100, 1);
    }

    /**
     * Get the related journal entry.
     */
    public function journalEntry()
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }
}
