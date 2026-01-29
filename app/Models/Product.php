<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'code', 'name', 'description', 
        'price', 'cost', 'previous_cost', 'stock_at_old_cost', 'cost_changed_at',
        'stock', 'min_stock', 'unit', 
        'purchase_unit', 'conversion_factor', 'margin_percent',
        'image', 'is_active', 'is_preorder', 'preorder_eta',
        'is_consignment', 'consignor_type', 'consignor_id', 'consignment_price', 'consignment_profit_percent'
    ];

    protected $casts = [
        'is_preorder' => 'boolean',
        'is_active' => 'boolean',
        'is_consignment' => 'boolean',
        'margin_percent' => 'decimal:2',
        'consignment_price' => 'decimal:2',
        'consignment_profit_percent' => 'decimal:2',
        'cost_changed_at' => 'datetime',
    ];

    /**
     * Get the consignor (User or Supplier)
     */
    public function consignor()
    {
        return $this->morphTo();
    }

    /**
     * Get cost per sale unit (cost / conversion_factor)
     */
    public function getCostPerUnitAttribute()
    {
        if ($this->conversion_factor <= 0) return $this->cost;
        return round($this->cost / $this->conversion_factor, 2);
    }

    /**
     * Calculate selling price with margin and configurable ceiling
     */
    public function calculateSellingPrice($costPerUnit = null)
    {
        $cost = $costPerUnit ?? $this->cost_per_unit;
        $marginMultiplier = 1 + ($this->margin_percent / 100);
        $rawPrice = $cost * $marginMultiplier;
        
        // Get ceiling from settings (default Rp 1000)
        $ceiling = \App\Models\Setting::get('price_ceiling', 1000);
        
        return ceil($rawPrice / $ceiling) * $ceiling;
    }

    /**
     * Check if there's a recent price change to display
     */
    public function hasPriceChange()
    {
        return $this->previous_cost !== null 
            && $this->previous_cost != $this->cost 
            && $this->stock_at_old_cost > 0;
    }

    /**
     * Get price change difference (per purchase unit)
     */
    public function getPriceChangeDiffAttribute()
    {
        if (!$this->hasPriceChange()) return 0;
        return $this->cost - $this->previous_cost;
    }

    /**
     * Get price change type: 'increase', 'decrease', or null
     */
    public function getPriceChangeTypeAttribute()
    {
        if (!$this->hasPriceChange()) return null;
        return $this->price_change_diff > 0 ? 'increase' : 'decrease';
    }

    /**
     * Clear price change indicator (e.g., when all old stock is sold)
     */
    public function clearPriceChangeIndicator()
    {
        $this->update([
            'previous_cost' => null,
            'stock_at_old_cost' => 0,
            'cost_changed_at' => null,
        ]);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Check if product stock is below minimum threshold
     */
    public function isLowStock()
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Scope to get low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock')
                     ->where('is_active', true);
    }

    /**
     * Get low stock level (critical, warning, ok)
     */
    public function getStockLevelAttribute()
    {
        if ($this->stock <= 0) return 'out';
        if ($this->stock <= $this->min_stock * 0.5) return 'critical';
        if ($this->stock <= $this->min_stock) return 'warning';
        return 'ok';
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_visible', true);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?: 0, 1);
    }

    /**
     * Get product image URL - uploaded or auto-generated placeholder
     */
    public function getImageUrlAttribute()
    {
        // If product has uploaded image, use it
        if ($this->image) {
            return \Storage::url($this->image);
        }

        // Fallback: Use placehold.co for placeholder with product name and category color
        $colors = [
            'sembako' => '4f46e5/ffffff',     // indigo
            'makanan ringan' => 'f97316/ffffff', // orange
            'minuman' => '06b6d4/ffffff',      // cyan
            'atk' => '8b5cf6/ffffff',          // violet 
            'kebersihan' => '22c55e/ffffff',   // green
        ];

        $bgColor = '6366f1/ffffff'; // default: indigo
        if ($this->category) {
            $categoryName = strtolower($this->category->name);
            foreach ($colors as $key => $color) {
                if (str_contains($categoryName, $key)) {
                    $bgColor = $color;
                    break;
                }
            }
        }

        $text = urlencode($this->name);
        return "https://placehold.co/400x400/{$bgColor}?text={$text}&font=roboto";
    }

    /**
     * Get appropriate keyword for image search based on product name
     */
    protected function getImageKeyword()
    {
        $name = strtolower($this->name);
        
        // Mapping nama produk Indonesia ke keyword English untuk Unsplash
        $keywords = [
            // Sembako
            'beras' => 'rice,grain',
            'minyak' => 'cooking,oil',
            'gula' => 'sugar',
            'tepung' => 'flour',
            'telur' => 'eggs',
            'kecap' => 'soy,sauce',
            'garam' => 'salt',
            'mie' => 'noodles,instant',
            'indomie' => 'noodles,instant',
            
            // Minuman
            'kopi' => 'coffee',
            'teh' => 'tea',
            'susu' => 'milk',
            'air mineral' => 'water,bottle',
            'sirup' => 'syrup',
            'jus' => 'juice',
            
            // Makanan Ringan
            'biskuit' => 'biscuit,cookies',
            'coklat' => 'chocolate',
            'keripik' => 'chips,snack',
            'wafer' => 'wafer',
            'permen' => 'candy',
            'roti' => 'bread',
            'silverqueen' => 'chocolate,bar',
            
            // Kebersihan
            'sabun' => 'soap',
            'shampo' => 'shampoo',
            'pasta gigi' => 'toothpaste',
            'sikat gigi' => 'toothbrush',
            'deterjen' => 'detergent',
            'pewangi' => 'fabric,softener',
            'rinso' => 'detergent,powder',
            'sunlight' => 'dish,soap',
            'lifebuoy' => 'soap,bar',
            'pepsodent' => 'toothpaste',
            
            // ATK
            'buku' => 'notebook,book',
            'pulpen' => 'pen,ballpoint',
            'pensil' => 'pencil',
            'penghapus' => 'eraser',
            'kertas' => 'paper,a4',
            'staples' => 'stapler',
            'lem' => 'glue',
            'gunting' => 'scissors',
        ];

        // Find matching keyword
        foreach ($keywords as $key => $value) {
            if (str_contains($name, $key)) {
                return urlencode($value);
            }
        }

        // Default: use category name or generic product
        if ($this->category) {
            $categoryKeywords = [
                'sembako' => 'grocery,food',
                'makanan ringan' => 'snack,food',
                'minuman' => 'beverage,drink',
                'atk' => 'office,supplies',
                'kebersihan' => 'cleaning,products',
            ];

            $categoryName = strtolower($this->category->name);
            foreach ($categoryKeywords as $key => $value) {
                if (str_contains($categoryName, $key)) {
                    return urlencode($value);
                }
            }
        }

        return 'product,grocery';
    }
}
