<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Account;
use App\Models\AccountCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $equityCat = AccountCategory::where('code', '3')->first();
        $catId = $equityCat ? $equityCat->id : 3;

        Account::firstOrCreate(
            ['code' => '3902'],
            [
                'account_category_id' => $catId,
                'name' => 'SHU Tahun Berjalan',
                'type' => 'equity',
                'sub_type' => 'current_year_earnings',
                'normal_balance' => 'credit',
                'is_active' => true,
                'description' => 'Sisa Hasil Usaha (SHU) tahun buku berjalan',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $acc = Account::where('code', '3902')->first();
        if ($acc) {
            // Only delete if there are no journal entries referencing it
            $hasJournals = \App\Models\JournalEntryLine::where('account_id', $acc->id)->exists();
            if (!$hasJournals) {
                $acc->delete();
            }
        }
    }
};
