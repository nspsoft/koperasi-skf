<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Apply 20% margin to all products and recalculate prices with Rp 1000 ceiling
     */
    public function up(): void
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            // Set margin to 20% if not already set or is 0
            $product->margin_percent = 20;
            
            // Calculate new price with 20% margin and Rp 1000 ceiling
            $costPerUnit = $product->conversion_factor > 0 
                ? $product->cost / $product->conversion_factor 
                : $product->cost;
            
            $rawPrice = $costPerUnit * 1.20; // 20% margin
            $newPrice = ceil($rawPrice / 1000) * 1000; // Ceiling to Rp 1000
            
            $product->price = $newPrice;
            $product->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse - would need original prices
    }
};
