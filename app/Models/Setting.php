<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    /**
     * Get setting value by key.
     */
    public static function get($key, $default = null)
    {
        $setting = Cache::remember("setting_{$key}", 3600, function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return match($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'number' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set setting value.
     */
    public static function set($key, $value, $group = 'general', $type = 'text', $description = null)
    {
        if (is_array($value)) {
            $value = json_encode($value);
            $type = 'json';
        }

        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget("setting_{$key}");

        return $setting;
    }

    /**
     * Get settings by group.
     */
    public static function getByGroup($group)
    {
        return self::where('group', $group)->get();
    }

    /**
     * Default settings for Koperasi.
     */
    public static function getDefaults()
    {
        return [
            // General
            ['key' => 'coop_name', 'value' => 'KOPERASI KARYAWAN PT. SPINDO TBK', 'group' => 'general', 'type' => 'text', 'description' => 'Nama Koperasi'],
            ['key' => 'coop_address', 'value' => 'Karawang, Jawa Barat', 'group' => 'general', 'type' => 'text', 'description' => 'Alamat Koperasi'],
            ['key' => 'coop_phone', 'value' => '', 'group' => 'general', 'type' => 'text', 'description' => 'Nomor Telepon'],
            ['key' => 'coop_email', 'value' => '', 'group' => 'general', 'type' => 'text', 'description' => 'Email Koperasi'],
            
            // Savings
            ['key' => 'saving_principal', 'value' => '100000', 'group' => 'savings', 'type' => 'number', 'description' => 'Jumlah Simpanan Pokok'],
            ['key' => 'saving_mandatory', 'value' => '50000', 'group' => 'savings', 'type' => 'number', 'description' => 'Jumlah Simpanan Wajib Bulanan'],
            
            // Loans
            ['key' => 'loan_interest_rate', 'value' => '1', 'group' => 'loan', 'type' => 'number', 'description' => 'Bunga Pinjaman per Bulan (%)'],
            ['key' => 'max_loan_amount', 'value' => '50000000', 'group' => 'loan', 'type' => 'number', 'description' => 'Maksimal Jumlah Pinjaman'],
            ['key' => 'max_loan_duration', 'value' => '24', 'group' => 'loan', 'type' => 'number', 'description' => 'Maksimal Tenor Pinjaman (bulan)'],
            
            // SHU
            ['key' => 'shu_jasa_simpanan', 'value' => '30', 'group' => 'shu', 'type' => 'number', 'description' => 'Persentase SHU untuk Jasa Simpanan (%)'],
            ['key' => 'shu_jasa_pinjaman', 'value' => '20', 'group' => 'shu', 'type' => 'number', 'description' => 'Persentase SHU untuk Jasa Pinjaman (%)'],

            // Koperasi Mart (Points)
            ['key' => 'point_earn_rate', 'value' => '10000', 'group' => 'commerce', 'type' => 'number', 'description' => 'Kelipatan belanja untuk 1 poin (Rp)'],
            ['key' => 'point_conversion_rate', 'value' => '1', 'group' => 'commerce', 'type' => 'number', 'description' => 'Nilai tukar 1 poin ke Rupiah'],
            
            // Pricing Settings
            ['key' => 'price_ceiling', 'value' => '1000', 'group' => 'commerce', 'type' => 'number', 'description' => 'Pembulatan harga jual ke kelipatan (Rp)'],
            ['key' => 'default_margin', 'value' => '20', 'group' => 'commerce', 'type' => 'number', 'description' => 'Default margin persen untuk produk baru'],
            
            // Inventory Costing Method
            ['key' => 'inventory_costing_method', 'value' => 'last_price', 'group' => 'commerce', 'type' => 'text', 'description' => 'Metode costing: last_price atau wac'],

            // WhatsApp Integration
            ['key' => 'whatsapp_number', 'value' => '6281234567890', 'group' => 'communication', 'type' => 'text', 'description' => 'Nomor WhatsApp Admin (format: 628xxx)'],
            ['key' => 'whatsapp_message', 'value' => 'Halo Admin, saya butuh bantuan mengenai Koperasi.', 'group' => 'communication', 'type' => 'text', 'description' => 'Pesan default WhatsApp'],

            // AD/ART Versioning
            ['key' => 'ad_art_version', 'value' => '3.0', 'group' => 'general', 'type' => 'text', 'description' => 'Versi AD/ART Terbaru'],
            ['key' => 'ad_art_ratification_date', 'value' => '15 Januari 2026', 'group' => 'general', 'type' => 'text', 'description' => 'Tanggal Pengesahan AD/ART'],
            ['key' => 'coop_legal_number', 'value' => 'No. 123/BH/2020', 'group' => 'general', 'type' => 'text', 'description' => 'Nomor Badan Hukum'],
            ['key' => 'coop_legal_principles', 'value' => 'UU No. 17/2012', 'group' => 'general', 'type' => 'text', 'description' => 'Dasar Hukum (UU/Peraturan)'],
        ];
    }

    /**
     * Seed default settings.
     */
    public static function seedDefaults()
    {
        foreach (self::getDefaults() as $setting) {
            self::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
