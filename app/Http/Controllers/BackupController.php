<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackupController extends Controller
{
    /**
     * Show backup management page
     */
    public function index()
    {
        $backups = $this->getExistingBackups();
        return view('settings.backup', compact('backups'));
    }

    /**
     * Download database backup
     */
    /**
     * Download database backup
     */
    public function download()
    {
        $database = config('database.connections.mysql.database');
        
        $filename = 'backup_' . $database . '_' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        // Ensure backup directory exists
        if (!file_exists(storage_path('app/backups'))) {
            try {
                mkdir(storage_path('app/backups'), 0755, true);
            } catch (\Exception $e) {
                // Ignore if it already exists
            }
        }

        try {
            // Use PHP-based dump to avoid exec() restrictions
            $this->dumpDatabase($path);
            
            // Return file download
            \App\Models\AuditLog::log(
                'backup', 
                "Mengunduh backup database: {$filename}"
            );

            return response()->download($path, $filename, [
                'Content-Type' => 'application/sql',
            ])->deleteFileAfterSend(false);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
    }

    /**
     * Pure PHP Database Dump
     */
    private function dumpDatabase($outputFile)
    {
        $handle = fopen($outputFile, 'w');
        if (!$handle) {
            throw new \Exception("Could not create backup file at $outputFile");
        }

        // Disable foreign key checks
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($handle, "SET time_zone = \"+00:00\";\n\n");

        $tables = \DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tablesColumn = "Tables_in_" . $dbName;

        foreach ($tables as $tableObj) {
            $table = $tableObj->$tablesColumn;
            
            // Drop table if exists
            fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");

            // Create table structure
            $createTableStmt = \DB::select("SHOW CREATE TABLE `$table`");
            if (isset($createTableStmt[0]->{'Create Table'})) {
                fwrite($handle, $createTableStmt[0]->{'Create Table'} . ";\n\n");
            }

            // Insert data
            // We use chunking to avoid memory issues
            \DB::table($table)->orderByRaw('1')->chunk(100, function ($rows) use ($handle, $table) {
                foreach ($rows as $row) {
                    $row = (array) $row;
                    $values = [];
                    foreach ($row as $value) {
                        if (is_null($value)) {
                            $values[] = "NULL";
                        } elseif (is_numeric($value)) {
                            $values[] = $value;
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }
                    fwrite($handle, "INSERT INTO `$table` VALUES (" . implode(',', $values) . ");\n");
                }
            });
            fwrite($handle, "\n\n");
        }

        // Re-enable foreign key checks
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    /**
     * Restore database from uploaded file
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:51200', // max 50MB
        ]);

        // Store uploaded file temporarily - use getRealPath directly from uploaded file
        $file = $request->file('backup_file');
        $fullPath = $file->getRealPath();

        try {
            // Verify file content is not empty
            if ($file->getSize() == 0) {
                throw new \Exception('File backup kosong.');
            }

            // Check if exec is enabled and mysql command is available
            $execEnabled = function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))));
            
            $restored = false;

            if ($execEnabled) {
                $database = config('database.connections.mysql.database');
                $username = config('database.connections.mysql.username');
                $password = config('database.connections.mysql.password');
                $host = config('database.connections.mysql.host');
                
                // Use native MySQL command for robust restoration
                $command = "mysql --user={$username} --password=\"{$password}\" --host={$host} {$database} < \"{$fullPath}\"";
                
                $output = [];
                $returnVar = 0;
                exec($command, $output, $returnVar);

                if ($returnVar === 0) {
                    $restored = true;
                } else {
                    \Log::warning("MySQL CLI restore failed with return code $returnVar. Falling back to PHP method.");
                }
            }

            // Fallback: PHP method
            if (!$restored) {
                $sql = file_get_contents($fullPath);
                
                // Increase time limit for large files
                set_time_limit(300);
                
                \DB::beginTransaction();
                try {
                    \DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    
                    // Simple splitter - works for mysqldump outputs but not complex procedures
                    // Using DB::unprepared allows multiple queries in some drivers, but safer to split
                    $statements = \DB::unprepared($sql);
                    
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1');
                    \DB::commit();
                    $restored = true;
                } catch (\Exception $e) {
                    \DB::rollBack();
                    // If unprepared failed, try splitting (slower/more fragile)
                    \DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    foreach ($statements as $statement) {
                        if (!empty($statement)) {
                            \DB::statement($statement);
                        }
                    }
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1');
                    $restored = true;
                }
            }
            
            \App\Models\AuditLog::log(
                'restore', 
                "Melakukan restore database dari file: " . $file->getClientOriginalName()
            );

            return redirect()->back()->with('success', 'Database berhasil direstore sepenuhnya!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal restore database: ' . $e->getMessage());
        }
    }

    /**
     * Delete a backup file
     */
    public function destroy($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        if (file_exists($path)) {
            unlink($path);

            \App\Models\AuditLog::log(
                'delete', 
                "Menghapus file backup database: {$filename}"
            );

            return redirect()->back()->with('success', 'Backup berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'File backup tidak ditemukan.');
    }

    /**
     * Get list of existing backups
     */
    private function getExistingBackups()
    {
        $backupPath = storage_path('app/backups');
        
        if (!file_exists($backupPath)) {
            return collect([]);
        }

        $files = scandir($backupPath);
        $backups = collect([]);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'sql') continue;

            $fullPath = $backupPath . '/' . $file;
            $backups->push([
                'name' => $file,
                'size' => filesize($fullPath),
                'date' => filemtime($fullPath),
            ]);
        }

        return $backups->sortByDesc('date')->values();
    }
    /**
     * Reset Database to Factory Settings
     */
    public function reset(Request $request)
    {
        // 1. Validation
        $request->validate([
            'password' => 'required|string',
            'confirm' => 'required|accepted'
        ]);

        if (!\Hash::check($request->password, auth()->user()->password)) {
            return back()->with('error', 'Password salah. Konfirmasi gagal.');
        }

        // 2. Auto Backup
        $database = config('database.connections.mysql.database');
        $filename = 'auto_backup_before_reset_' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);
        
        // Ensure dir exists
        if (!file_exists(storage_path('app/backups'))) {
             try {
                mkdir(storage_path('app/backups'), 0755, true);
             } catch (\Exception $e) {}
        }

        try {
            $this->dumpDatabase($path);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal melakukan backup otomatis. Reset dibatalkan demi keamanan.');
        }

        // 3. Reset Process
        try {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // List of tables to truncate (Empty completely)
            $tablesToTruncate = [
                // Financials & Accounting
                'journal_entries', 'journal_entry_lines', 'manual_journals', 
                'assets', 'bank_transactions', 'shu_distributions',
                'expenses', 'vouchers', // Keep 'expense_categories'

                // Commerce & Inventory
                'products', 'product_reviews', 
                'transactions', 'transaction_items', 
                'carts', 'cart_items',
                'purchases', 'purchase_items',
                'consignment_inbounds', 'consignment_settlements', 
                'stock_opnames', 'stock_opname_items',
                'suppliers', // Keep 'categories' (Product Categories)

                // Cooperative Core
                'members', 'savings', 'loans', 'loan_payments', 
                'withdrawal_requests', 'member_aspirations',
                
                // Organization & Activities
                'organization_profiles', 'meetings', 'work_programs',
                'announcements', 'polls', 'poll_options', 'poll_votes',
                
                // System & Logs
                'audit_logs', 'notifications', 'whatsapp_logs', 'failed_jobs', 'jobs'
            ];

            foreach ($tablesToTruncate as $table) {
                if (\Schema::hasTable($table)) {
                    \DB::table($table)->truncate();
                }
            }

            // Users: Delete all except ID 1 (Super Admin)
            \DB::table('users')->where('id', '!=', 1)->delete();

            // 4. File Cleanup
            $foldersToClear = [
                'members/photos',
                'products/images',
                'documents',
                'signatures',
                'meetings/attachments'
            ];
            
            foreach ($foldersToClear as $folder) {
                if (\Storage::disk('public')->exists($folder)) {
                    \Storage::disk('public')->deleteDirectory($folder);
                    \Storage::disk('public')->makeDirectory($folder); // Recreate empty
                }
            }
            
            // 5. Seed Initial Data (if missing)
            if (\DB::table('expense_categories')->count() == 0) {
                $now = now();
                $categories = [
                    ['name' => 'Konsumsi', 'description' => 'Biaya makan dan minum untuk rapat atau kegiaan operasional', 'created_at' => $now, 'updated_at' => $now],
                    ['name' => 'Transportasi', 'description' => 'Biaya bensin, tol, parkir, dan perjalanan dinas', 'created_at' => $now, 'updated_at' => $now],
                    ['name' => 'ATK & Perlengkapan', 'description' => 'Pembelian alat tulis kantor dan perlengkapan kerja', 'created_at' => $now, 'updated_at' => $now],
                    ['name' => 'Perawatan & Pemeliharaan', 'description' => 'Service kendaraan, AC, gedung, dan aset lainnya', 'created_at' => $now, 'updated_at' => $now],
                    ['name' => 'Listrik, Air & Internet', 'description' => 'Tagihan bulanan utilitas kantor', 'created_at' => $now, 'updated_at' => $now],
                ];
                \DB::table('expense_categories')->insert($categories);
            }

            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            \App\Models\AuditLog::log('system', 'Melakukan RESET DATABASE ke pengaturan awal (Factory Reset).');

            return back()->with('success', 'Database berhasil direset. Backup otomatis telah dibuat: ' . $filename);

        } catch (\Exception $e) {
             \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
             return back()->with('error', 'Terjadi kesalahan saat reset: ' . $e->getMessage());
        }
    }
}
