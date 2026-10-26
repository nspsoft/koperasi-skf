<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollExternalDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'period',
        'source',
        'employee_id',
        'employee_name',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Helper to normalize employee number (e.g. "KA 008" -> "KA008")
     */
    public static function normalizeEmployeeId(?string $id): string
    {
        if (!$id) return '';
        return strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $id));
    }
}
