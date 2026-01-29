<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create a test notification for the admin user
        $admin = DB::table('users')->where('role', 'admin')->first();
        
        if ($admin) {
            DB::table('notifications')->insert([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'type' => 'App\\Notifications\\LowStockNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'product_id' => 1,
                    'product_name' => 'Produk Uji Coba',
                    'stock' => 3,
                    'min_stock' => 10,
                    'message' => 'Stok Produk Uji Coba tinggal 3 unit.',
                    'type' => 'low_stock',
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('notifications')->where('data', 'like', '%Produk Uji Coba%')->delete();
    }
};
