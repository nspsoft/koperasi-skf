<?php

use App\Models\Product;

// Check product BARANG007
$product = Product::where('code', 'BARANG007')->first();

if ($product) {
    echo "Product: " . $product->name . "\n";
    echo "Code: " . $product->code . "\n";
    echo "DB Price: " . $product->price . "\n";
    echo "DB Cost: " . $product->cost . "\n";
    echo "DB Margin %: " . $product->margin_percent . "\n";
    echo "Conversion Factor: " . $product->conversion_factor . "\n";
    echo "Cost Per Unit: " . ($product->cost / ($product->conversion_factor ?: 1)) . "\n";
    
    // Calculate what price should be with 20% margin + ceiling Rp 1000
    $costPerUnit = $product->cost / ($product->conversion_factor ?: 1);
    $expectedPrice = ceil($costPerUnit * 1.20 / 1000) * 1000;
    echo "Expected Price (20% + ceiling 1000): " . $expectedPrice . "\n";
} else {
    echo "Product not found\n";
}
