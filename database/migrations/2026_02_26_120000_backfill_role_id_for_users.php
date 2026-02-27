<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('roles')) {
            return;
        }

        $roleMap = DB::table('roles')->pluck('id', 'name')->toArray();
        if (! $roleMap) {
            return;
        }

        foreach ($roleMap as $name => $id) {
            DB::table('users')
                ->whereNull('role_id')
                ->where('role', $name)
                ->update(['role_id' => $id]);
        }
    }

    public function down(): void
    {
    }
};
