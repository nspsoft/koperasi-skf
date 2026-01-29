<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_id',
        'employee_id',
        'department',
        'position',
        'join_date',
        'status',
        'credit_limit',
        'address',
        'id_card_number',
        'birth_date',
        'gender',
        'photo',
        'points',
    ];

    protected $casts = [
        'join_date' => 'date',
        'birth_date' => 'date',
        'credit_limit' => 'decimal:2',
        'points' => 'integer',
    ];

    public function getPointsValueAttribute()
    {
        // Default: 1 Point = Rp 1
        // We can make this configurable in settings later
        $rate = Setting::get('point_conversion_rate', 1);
        return $this->points * $rate;
    }

    /**
     * Get member name from User.
     */
    public function getNameAttribute()
    {
        return $this->user->name ?? $this->member_id;
    }

    /**
     * Get the user that owns the member profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all savings for this member.
     */
    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    /**
     * Get all loans for this member.
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get total simpanan pokok.
     */
    public function getTotalSimpananPokokAttribute()
    {
        return $this->savings()
            ->where('type', 'pokok')
            ->where('transaction_type', 'deposit')
            ->sum('amount') -
            $this->savings()
            ->where('type', 'pokok')
            ->where('transaction_type', 'withdrawal')
            ->sum('amount');
    }

    /**
     * Get total simpanan wajib.
     */
    public function getTotalSimpananWajibAttribute()
    {
        return $this->savings()
            ->where('type', 'wajib')
            ->where('transaction_type', 'deposit')
            ->sum('amount') -
            $this->savings()
            ->where('type', 'wajib')
            ->where('transaction_type', 'withdrawal')
            ->sum('amount');
    }

    /**
     * Get total simpanan sukarela.
     */
    public function getTotalSimpananSukarelaAttribute()
    {
        return $this->savings()
            ->where('type', 'sukarela')
            ->where('transaction_type', 'deposit')
            ->sum('amount') -
            $this->savings()
            ->where('type', 'sukarela')
            ->where('transaction_type', 'withdrawal')
            ->sum('amount');
    }

    /**
     * Get total all savings.
     */
    public function getTotalSimpananAttribute()
    {
        return $this->total_simpanan_pokok + $this->total_simpanan_wajib + $this->total_simpanan_sukarela;
    }

    /**
     * Get total active loans.
     */
    public function getTotalPinjamanAktifAttribute()
    {
        return $this->loans()
            ->where('status', 'active')
            ->sum('remaining_amount');
    }

    /**
     * Get current credit used from shopping.
     */
    public function getCreditUsedAttribute()
    {
        return Transaction::where('user_id', $this->user_id)
            ->where('payment_method', 'kredit')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->sum('total_amount');
    }

    /**
     * Get available credit limit.
     */
    public function getCreditAvailableAttribute()
    {
        return max(0, $this->credit_limit - $this->credit_used);
    }

    /**
     * Get Simpanan Sukarela balance.
     */
    public function getBalanceAttribute()
    {
        // Robust calculation: sum of deposits - sum of withdrawals (treating all as positive absolute values)
        $deposits = $this->savings()->where('type', 'sukarela')->where('transaction_type', 'deposit')->sum(\DB::raw('ABS(amount)'));
        $withdrawals = $this->savings()->where('type', 'sukarela')->where('transaction_type', 'withdrawal')->sum(\DB::raw('ABS(amount)'));
        return $deposits - $withdrawals;
    }

    /**
     * Generate automatic member ID.
     */
    public static function generateMemberId()
    {
        $year = date('Y');
        $prefix = 'KOP' . $year;
        
        // Get the last member with this year's prefix
        $lastMember = self::where('member_id', 'like', $prefix . '%')
            ->orderBy('member_id', 'desc')
            ->first();

        if ($lastMember) {
            // Extract the last 4 digits and increment
            $lastNumber = (int) substr($lastMember->member_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
