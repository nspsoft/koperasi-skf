<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AccountCategory;
use App\Models\Account;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Account Categories
        $categories = [
            ['code' => '1', 'name' => 'Aktiva', 'type' => 'asset', 'description' => 'Harta atau aset koperasi'],
            ['code' => '2', 'name' => 'Kewajiban', 'type' => 'liability', 'description' => 'Hutang dan kewajiban koperasi'],
            ['code' => '3', 'name' => 'Modal', 'type' => 'equity', 'description' => 'Modal dan ekuitas koperasi'],
            ['code' => '4', 'name' => 'Pendapatan', 'type' => 'revenue', 'description' => 'Pendapatan dari berbagai sumber'],
            ['code' => '5', 'name' => 'Biaya', 'type' => 'expense', 'description' => 'Biaya operasional dan lainnya'],
        ];

        foreach ($categories as $catData) {
            AccountCategory::firstOrCreate(
                ['code' => $catData['code']],
                $catData
            );
        }

        // Get categories for relationships
        $assetCat = AccountCategory::where('code', '1')->first();
        $liabilityCat = AccountCategory::where('code', '2')->first();
        $equityCat = AccountCategory::where('code', '3')->first();
        $revenueCat = AccountCategory::where('code', '4')->first();
        $expenseCat = AccountCategory::where('code', '5')->first();

        // Create Accounts
        $accounts = [
            // AKTIVA (Assets)
            ['account_category_id' => $assetCat->id, 'code' => '1101', 'name' => 'Kas', 'type' => 'asset', 'sub_type' => 'current_asset', 'normal_balance' => 'debit', 'description' => 'Kas dan setara kas'],
            ['account_category_id' => $assetCat->id, 'code' => '1102', 'name' => 'Bank', 'type' => 'asset', 'sub_type' => 'current_asset', 'normal_balance' => 'debit', 'description' => 'Rekening bank koperasi'],
            ['account_category_id' => $assetCat->id, 'code' => '1201', 'name' => 'Piutang Anggota - Pinjaman', 'type' => 'asset', 'sub_type' => 'current_asset', 'normal_balance' => 'debit', 'description' => 'Piutang dari pinjaman anggota'],
            ['account_category_id' => $assetCat->id, 'code' => '1202', 'name' => 'Piutang Dagang', 'type' => 'asset', 'sub_type' => 'current_asset', 'normal_balance' => 'debit', 'description' => 'Piutang dari penjualan kredit'],
            ['account_category_id' => $assetCat->id, 'code' => '1301', 'name' => 'Persediaan Barang Dagangan', 'type' => 'asset', 'sub_type' => 'current_asset','normal_balance' => 'debit', 'description' => 'Stok barang untuk dijual'],
            ['account_category_id' => $assetCat->id, 'code' => '1401', 'name' => 'Peralatan Kantor', 'type' => 'asset', 'sub_type' => 'fixed_asset', 'normal_balance' => 'debit', 'description' => 'Peralatan kantor dan inventaris'],

            // KEWAJIBAN (Liabilities)
            ['account_category_id' => $liabilityCat->id, 'code' => '2101', 'name' => 'Simpanan Pokok', 'type' => 'liability', 'sub_type' => 'long_term_liability', 'normal_balance' => 'credit', 'description' => 'Simpanan pokok anggota'],
            ['account_category_id' => $liabilityCat->id, 'code' => '2102', 'name' => 'Simpanan Wajib', 'type' => 'liability', 'sub_type' => 'long_term_liability', 'normal_balance' => 'credit', 'description' => 'Simpanan wajib anggota'],
            ['account_category_id' => $liabilityCat->id, 'code' => '2103', 'name' => 'Simpanan Sukarela', 'type' => 'liability', 'sub_type' => 'current_liability', 'normal_balance' => 'credit', 'description' => 'Simpanan sukarela anggota'],
            ['account_category_id' => $liabilityCat->id, 'code' => '2201', 'name' => 'Hutang Usaha', 'type' => 'liability', 'sub_type' => 'current_liability', 'normal_balance' => 'credit', 'description' => 'Hutang kepada supplier'],
            ['account_category_id' => $liabilityCat->id, 'code' => '2301', 'name' => 'SHU Belum Dibagikan', 'type' => 'liability', 'sub_type' => 'current_liability', 'normal_balance' => 'credit', 'description' => 'SHU yang belum dibagikan ke anggota'],

            // MODAL (Equity)
            ['account_category_id' => $equityCat->id, 'code' => '3101', 'name' => 'Modal Koperasi', 'type' => 'equity', 'sub_type' => 'capital', 'normal_balance' => 'credit', 'description' => 'Modal dasar koperasi'],
            ['account_category_id' => $equityCat->id, 'code' => '3201', 'name' => 'Cadangan Umum', 'type' => 'equity', 'sub_type' => 'reserve', 'normal_balance' => 'credit', 'description' => 'Cadangan umum koperasi'],
            ['account_category_id' => $equityCat->id, 'code' => '3202', 'name' => 'Cadangan Khusus', 'type' => 'equity', 'sub_type' => 'reserve', 'normal_balance' => 'credit', 'description' => 'Cadangan khusus'],
            ['account_category_id' => $equityCat->id, 'code' => '3901', 'name' => 'Laba Ditahan', 'type' => 'equity', 'sub_type' => 'retained_earnings', 'normal_balance' => 'credit', 'description' => 'Laba ditahan dari periode sebelumnya'],

            // PENDAPATAN (Revenue)
            ['account_category_id' => $revenueCat->id, 'code' => '4101', 'name' => 'Pendapatan Bunga Pinjaman', 'type' => 'revenue', 'sub_type' => 'operating_revenue', 'normal_balance' => 'credit', 'description' => 'Pendapatan bunga dari pinjaman anggota'],
            ['account_category_id' => $revenueCat->id, 'code' => '4102', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue', 'sub_type' => 'operating_revenue', 'normal_balance' => 'credit', 'description' => 'Pendapatan dari penjualan barang'],
            ['account_category_id' => $revenueCat->id, 'code' => '4103', 'name' => 'Pendapatan Jasa Lainnya', 'type' => 'revenue', 'sub_type' => 'other_revenue', 'normal_balance' => 'credit', 'description' => 'Pendapatan jasa lainnya'],

            // BIAYA (Expenses)
            ['account_category_id' => $expenseCat->id, 'code' => '5101', 'name' => 'Biaya Gaji & Tunjangan', 'type' => 'expense', 'sub_type' => 'operating_expense', 'normal_balance' => 'debit', 'description' => 'Gaji karyawan dan tunjangan'],
            ['account_category_id' => $expenseCat->id, 'code' => '5102', 'name' => 'Biaya Operasional', 'type' => 'expense', 'sub_type' => 'operating_expense', 'normal_balance' => 'debit', 'description' => 'Biaya operasional umum'],
            ['account_category_id' => $expenseCat->id, 'code' => '5103', 'name' => 'Biaya Listrik & Air', 'type' => 'expense', 'sub_type' => 'operating_expense', 'normal_balance' => 'debit', 'description' => 'Biaya utilitas listrik dan air'],
            ['account_category_id' => $expenseCat->id, 'code' => '5104', 'name' => 'Biaya Pemeliharaan', 'type' => 'expense', 'sub_type' => 'operating_expense', 'normal_balance' => 'debit', 'description' => 'Biaya pemeliharaan dan perbaikan'],
            ['account_category_id' => $expenseCat->id, 'code' => '5201', 'name' => 'Harga Pokok Penjualan (HPP)', 'type' => 'expense', 'sub_type' => 'cost_of_goods_sold', 'normal_balance' => 'debit', 'description' => 'Harga pokok dari barang yang terjual'],
            ['account_category_id' => $expenseCat->id, 'code' => '5901', 'name' => 'Biaya Lain-lain', 'type' => 'expense', 'sub_type' => 'other_expense', 'normal_balance' => 'debit', 'description' => 'Biaya lain-lain'],
        ];

        foreach ($accounts as $accountData) {
            Account::firstOrCreate(
                ['code' => $accountData['code']],
                $accountData
            );
        }

        $this->command->info('Chart of Accounts seeded successfully!');
    }
}
