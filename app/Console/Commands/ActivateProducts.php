<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ActivateProducts extends Command
{
    protected $signature = 'app:activate-products';
    protected $description = 'Activate all products to make them visible in POS';

    public function handle()
    {
        $count = Product::where('is_active', false)
            ->orWhereNull('is_active')
            ->update(['is_active' => true]);

        $this->info("✅ {$count} produk berhasil diaktifkan!");
        $this->info("Semua produk sekarang akan tampil di kasir POS.");
        
        return 0;
    }
}
