<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert the manage_email permission
        $permissionId = DB::table('permissions')->insertGetId([
            'name' => 'manage_email',
            'label' => 'Akses Email Admin',
            'group' => 'Sistem',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Automatically assign this permission to the admin role
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            DB::table('role_permission')->insert([
                'role_id' => $adminRole->id,
                'permission_id' => $permissionId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = DB::table('permissions')->where('name', 'manage_email')->first();
        if ($permission) {
            DB::table('role_permission')->where('permission_id', $permission->id)->delete();
            DB::table('permissions')->where('id', $permission->id)->delete();
        }
    }
};
