<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShuSetting extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'period_year',
        'total_shu_pool',
        'persen_cadangan',
        'persen_jasa_modal',
        'persen_jasa_usaha',
        'persen_pengurus',
        'persen_karyawan',
        'persen_pendidikan',
        'persen_sosial',
        'persen_pembangunan',
        'pool_cadangan',
        'pool_jasa_modal',
        'pool_jasa_usaha',
        'pool_pengurus',
        'pool_karyawan',
        'pool_pendidikan',
        'pool_sosial',
        'pool_pembangunan',
        'status',
        'created_by'
    ];

    protected $casts = [
        'total_shu_pool' => 'decimal:2',
        'persen_cadangan' => 'decimal:2',
        'persen_jasa_modal' => 'decimal:2',
        'persen_jasa_usaha' => 'decimal:2',
        'persen_pengurus' => 'decimal:2',
        'persen_karyawan' => 'decimal:2',
        'persen_pendidikan' => 'decimal:2',
        'persen_sosial' => 'decimal:2',
        'persen_pembangunan' => 'decimal:2',
        'pool_cadangan' => 'decimal:2',
        'pool_jasa_modal' => 'decimal:2',
        'pool_jasa_usaha' => 'decimal:2',
        'pool_pengurus' => 'decimal:2',
        'pool_karyawan' => 'decimal:2',
        'pool_pendidikan' => 'decimal:2',
        'pool_sosial' => 'decimal:2',
        'pool_pembangunan' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function distributions()
    {
        return ShuDistribution::where('period_year', $this->period_year)->get();
    }

    /**
     * Calculate pools from percentages
     */
    public function calculatePools()
    {
        $total = $this->total_shu_pool;
        
        $this->pool_cadangan = $total * ($this->persen_cadangan / 100);
        $this->pool_jasa_modal = $total * ($this->persen_jasa_modal / 100);
        $this->pool_jasa_usaha = $total * ($this->persen_jasa_usaha / 100);
        $this->pool_pengurus = $total * ($this->persen_pengurus / 100);
        $this->pool_karyawan = $total * ($this->persen_karyawan / 100);
        $this->pool_pendidikan = $total * ($this->persen_pendidikan / 100);
        $this->pool_sosial = $total * ($this->persen_sosial / 100);
        $this->pool_pembangunan = $total * ($this->persen_pembangunan / 100);
        
        return $this;
    }

    /**
     * Get total percentage (should be 100%)
     */
    public function getTotalPersenAttribute()
    {
        return $this->persen_cadangan + $this->persen_jasa_modal + $this->persen_jasa_usaha +
               $this->persen_pengurus + $this->persen_karyawan + $this->persen_pendidikan +
               $this->persen_sosial + $this->persen_pembangunan;
    }

    /**
     * Get pool that goes to members (jasa modal + jasa usaha)
     */
    public function getPoolAnggotaAttribute()
    {
        return $this->pool_jasa_modal + $this->pool_jasa_usaha;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'calculated' => 'warning',
            'distributed' => 'success',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'calculated' => 'Terhitung',
            'distributed' => 'Dibagikan',
            default => $this->status,
        };
    }
}
