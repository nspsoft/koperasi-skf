<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class GeneratedDocument extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'document_number',
        'document_type',
        'user_id',
        'generated_by',
        'reference_type',
        'reference_id',
        'data',
        'verified_at'
    ];

    protected $casts = [
        'data' => 'array',
        'verified_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
