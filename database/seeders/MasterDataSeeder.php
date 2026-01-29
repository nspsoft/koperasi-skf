<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Position;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Departments
        $departments = [
            ['name' => 'Production', 'code' => 'PROD', 'description' => 'Departemen Produksi'],
            ['name' => 'Quality Control', 'code' => 'QC', 'description' => 'Departemen Quality Control'],
            ['name' => 'Warehouse', 'code' => 'WH', 'description' => 'Departemen Gudang'],
            ['name' => 'Maintenance', 'code' => 'MTC', 'description' => 'Departemen Maintenance'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Departemen SDM'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Departemen Keuangan'],
            ['name' => 'IT', 'code' => 'IT', 'description' => 'Departemen IT'],
            ['name' => 'Purchasing', 'code' => 'PUR', 'description' => 'Departemen Pembelian'],
            ['name' => 'Marketing', 'code' => 'MKT', 'description' => 'Departemen Marketing'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // Seed Positions
        $positions = [
            ['name' => 'Staff', 'code' => 'STF', 'description' => 'Karyawan Staff'],
            ['name' => 'Supervisor', 'code' => 'SPV', 'description' => 'Supervisor'],
            ['name' => 'Manager', 'code' => 'MGR', 'description' => 'Manager'],
            ['name' => 'Assistant Manager', 'code' => 'ASSMGR', 'description' => 'Assistant Manager'],
            ['name' => 'Team Leader', 'code' => 'TL', 'description' => 'Team Leader'],
            ['name' => 'Senior Staff', 'code' => 'SRSTF', 'description' => 'Senior Staff'],
            ['name' => 'Junior Staff', 'code' => 'JRSTF', 'description' => 'Junior Staff'],
            ['name' => 'Operator', 'code' => 'OPR', 'description' => 'Operator'],
            ['name' => 'Technician', 'code' => 'TECH', 'description' => 'Technician'],
        ];

        foreach ($positions as $pos) {
            Position::create($pos);
        }

        $this->command->info('✅ Master data seeded successfully!');
        $this->command->info('   - Departments: ' . count($departments));
        $this->command->info('   - Positions: ' . count($positions));
    }
}
