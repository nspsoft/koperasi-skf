<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class FixProductVisibility extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-product-visibility';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix product visibility by activating all products and setting default min_stock';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting product visibility fix...');

        // 1. Activate all products
        $inactiveCount = Product::where('is_active', false)->count();
        if ($inactiveCount > 0) {
            $this->comment("Activating {$inactiveCount} inactive products...");
            Product::where('is_active', false)->update(['is_active' => true]);
        } else {
            $this->info('All products are already active.');
        }

        // 2. Set default min_stock to 10 if it is 0 or NULL
        $noMinStockCount = Product::where(function($query) {
            $query->whereNull('min_stock')->orWhere('min_stock', 0);
        })->count();

        if ($noMinStockCount > 0) {
            $this->comment("Setting default min_stock = 10 for {$noMinStockCount} products...");
            Product::where(function($query) {
                $query->whereNull('min_stock')->orWhere('min_stock', 0);
            })->update(['min_stock' => 10]);
        } else {
            $this->info('All products already have a minimum stock level set.');
        }

        $this->info('Success! Please check the Low Stock and Online Shop views on your website.');
        
        return 0;
    }
}
