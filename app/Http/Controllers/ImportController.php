<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MembersImport;
use App\Imports\SavingsImport;
use App\Imports\LoansImport;
use App\Imports\CreditPaymentsImport;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ImportController extends Controller
{
    /**
     * Show import page
     */
    public function index()
    {
        // Get counts for reset section
        $counts = [
            'members' => Member::count(),
            'users' => User::where('role', '!=', 'admin')->count(),
            'savings' => Saving::count(),
            'loans' => Loan::count(),
            'transactions' => \App\Models\Transaction::count(),
            'purchases' => \App\Models\Purchase::count(),
        ];

        return view('imports.index', compact('counts'));
    }

    /**
     * Import Members from Excel
     */
    public function importMembers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $import = new MembersImport;
            Excel::import($import, $request->file('file'));
            
            $errorMessages = [];

            // Check for validation failures
            $failures = $import->failures();
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "Baris {$row}: {$attribute} - {$errors}";
            }

            // Check for execution errors (SQL errors, etc)
            $errors = $import->errors();
            foreach ($errors as $error) {
                $errorMessages[] = "System Error: " . $error->getMessage();
            }

            if (!empty($errorMessages)) {
                return redirect()->back()
                    ->with('error', 'Terdapat error pada saat import:')
                    ->with('import_errors', $errorMessages);
            }
            
            return redirect()->back()->with('success', 'Data anggota berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "Baris {$row}: {$attribute} - {$errors}";
            }
            
            return redirect()->back()
                ->with('error', 'Validasi gagal:')
                ->with('import_errors', $errorMessages);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Import Savings from Excel
     */
    public function importSavings(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $import = new SavingsImport;
            Excel::import($import, $request->file('file'));
            
            $errorMessages = [];

            // Check for validation failures
            $failures = $import->failures();
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "Baris {$row}: {$attribute} - {$errors}";
            }

            // Check for execution errors
            $errors = $import->errors();
            foreach ($errors as $error) {
                $errorMessages[] = "System Error: " . $error->getMessage();
            }

            if (!empty($errorMessages)) {
                return redirect()->back()
                    ->with('error', 'Terdapat error pada saat import:')
                    ->with('import_errors', $errorMessages);
            }
            
            return redirect()->back()->with('success', 'Data simpanan berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "Baris {$row}: {$attribute} - {$errors}";
            }
            return redirect()->back()->with('error', 'Validasi gagal:')->with('import_errors', $errorMessages);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Import Loans from Excel
     */
    public function importLoans(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $import = new LoansImport;
            Excel::import($import, $request->file('file'));
            
            $errorMessages = [];

            // Check for validation failures
            $failures = $import->failures();
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "Baris {$row}: {$attribute} - {$errors}";
            }

            // Check for execution errors
            $errors = $import->errors();
            foreach ($errors as $error) {
                $errorMessages[] = "System Error: " . $error->getMessage();
            }

            if (!empty($errorMessages)) {
                return redirect()->back()
                    ->with('error', 'Terdapat error pada saat import:')
                    ->with('import_errors', $errorMessages);
            }
            
            return redirect()->back()->with('success', 'Data pinjaman berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "Baris {$row}: {$attribute} - {$errors}";
            }
            return redirect()->back()->with('error', 'Validasi gagal:')->with('import_errors', $errorMessages);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Download example Excel templates
     */
    public function downloadTemplate($type)
    {
        $filename = match($type) {
            'members' => 'Template_Anggota.xlsx',
            'savings' => 'Template_Simpanan.xlsx',
            'loans' => 'Template_Pinjaman.xlsx',
            'credit_payments' => 'Template_Pelunasan_Kredit.xlsx',
            default => abort(404),
        };

        $export = match($type) {
            'members' => new \App\Exports\MembersTemplateExport(),
            'savings' => new \App\Exports\SavingsTemplateExport(),
            'loans' => new \App\Exports\LoansTemplateExport(),
            'credit_payments' => new \App\Exports\CreditPaymentsTemplateExport(),
        };

        return Excel::download($export, $filename);
    }

    /**
     * Import Credit Payments from Excel
     */
    public function importCreditPayments(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $import = new CreditPaymentsImport();
            Excel::import($import, $request->file('file'));
            
            $errorMessages = [];

            // Check for validation failures
            $failures = $import->failures();
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "Baris {$row}: {$attribute} - {$errors}";
            }

            // Check for execution errors
            $errors = $import->errors();
            foreach ($errors as $error) {
                $errorMessages[] = "System Error: " . $error->getMessage();
            }

            if (!empty($errorMessages)) {
                return redirect()->back()
                    ->with('error', 'Terdapat error pada saat import:')
                    ->with('import_errors', $errorMessages);
            }
            
            return redirect()->back()->with('success', 'Data pelunasan kredit berhasil diimport! Jurnal masuk ke akun Bank.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "Baris {$row}: {$attribute} - {$errors}";
            }
            return redirect()->back()->with('error', 'Validasi gagal:')->with('import_errors', $errorMessages);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Reset/Delete all members (except admins)
     */
    public function resetMembers(Request $request)
    {
        $request->validate([
            'confirm' => 'required|in:HAPUS'
        ]);

        try {
            DB::beginTransaction();

            // Delete journals for members (though members usually don't have direct journals, they have savings/loans)
            // But we'll delete all journals if we're resetting data
            \App\Models\JournalEntry::query()->delete();
            \App\Models\JournalEntryLine::query()->delete();

            // Delete members first (this will cascade delete savings, loans due to foreign keys)
            Member::query()->delete();
            
            // Delete non-admin users
            User::where('role', '!=', 'admin')->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Semua data anggota dan jurnal berhasil dihapus! (Admin tetap aman)');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal reset: ' . $e->getMessage());
        }
    }

    /**
     * Reset/Delete all savings
     */
    public function resetSavings(Request $request)
    {
        $request->validate([
            'confirm' => 'required|in:HAPUS'
        ]);

        try {
            DB::beginTransaction();

            // Delete related journals
            \App\Models\JournalEntry::where('reference_type', Saving::class)
                ->each(function ($journal) {
                    $journal->lines()->delete();
                    $journal->delete();
                });

            Saving::query()->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Semua data simpanan dan jurnal terkait berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal reset: ' . $e->getMessage());
        }
    }

    /**
     * Reset/Delete all loans (and loan payments)
     */
    public function resetLoans(Request $request)
    {
        $request->validate([
            'confirm' => 'required|in:HAPUS'
        ]);

        try {
            DB::beginTransaction();

            // Delete related journals for loans
            \App\Models\JournalEntry::where('reference_type', Loan::class)
                ->each(function ($journal) {
                    $journal->lines()->delete();
                    $journal->delete();
                });

            // Delete related journals for loan payments
            \App\Models\JournalEntry::where('reference_type', LoanPayment::class)
                ->each(function ($journal) {
                    $journal->lines()->delete();
                    $journal->delete();
                });

            // Delete loan payments first
            LoanPayment::query()->delete();
            
            // Then delete loans
            Loan::query()->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Semua data pinjaman dan jurnal terkait berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal reset: ' . $e->getMessage());
        }
    }

    /**
     * Reset ALL data (members, savings, loans) - DANGER!
     */
    public function resetAll(Request $request)
    {
        $request->validate([
            'confirm' => 'required|in:HAPUS SEMUA'
        ]);

        try {
            DB::beginTransaction();

            // Delete journals
            \App\Models\JournalEntryLine::query()->delete();
            \App\Models\JournalEntry::query()->delete();

            // Delete in order (respecting foreign keys)
            \App\Models\PurchaseItem::query()->delete();
            \App\Models\Purchase::query()->delete();
            LoanPayment::query()->delete();
            Loan::query()->delete();
            Saving::query()->delete();
            \App\Models\TransactionItem::query()->delete();
            \App\Models\Transaction::query()->delete();
            Member::query()->delete();
            User::where('role', '!=', 'admin')->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Semua data berhasil dihapus! Akun Admin tetap aman.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal reset: ' . $e->getMessage());
        }
    }

    /**
     * Reset/Delete all purchases
     */
    public function resetPurchases(Request $request)
    {
        $request->validate([
            'confirm' => 'required|in:HAPUS'
        ]);

        try {
            DB::beginTransaction();

            // Delete journals
            \App\Models\JournalEntry::where('reference_type', \App\Models\Purchase::class)
                ->each(function ($journal) {
                    $journal->lines()->delete();
                    $journal->delete();
                });

            \App\Models\PurchaseItem::query()->delete();
            \App\Models\Purchase::query()->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Semua data pembelian dan jurnal terkait berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal reset: ' . $e->getMessage());
        }
    }

    /**
     * Reset/Delete all transactions
     */
    public function resetTransactions(Request $request)
    {
        $request->validate([
            'confirm' => 'required|in:HAPUS'
        ]);

        try {
            DB::beginTransaction();

            // Delete journals
            \App\Models\JournalEntry::where('reference_type', \App\Models\Transaction::class)
                ->each(function ($journal) {
                    $journal->lines()->delete();
                    $journal->delete();
                });

            \App\Models\TransactionItem::query()->delete();
            \App\Models\Transaction::query()->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Semua data transaksi dan jurnal terkait berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal reset: ' . $e->getMessage());
        }
    }

    /**
     * Generate Journal Entries for Savings that don't have journals yet
     */
    public function generateSavingsJournals(Request $request)
    {
        try {
            DB::beginTransaction();

            // Get savings without journal entries
            $savingsWithoutJournals = Saving::whereDoesntHave('journalEntry')
                ->with(['member.user'])
                ->get();

            $generated = 0;
            $errors = [];

            foreach ($savingsWithoutJournals as $saving) {
                try {
                    // Imported savings are via payroll/bank transfer
                    if ($saving->transaction_type === 'deposit') {
                        \App\Services\JournalService::journalSavingDeposit($saving, 'bank');
                    } else {
                        \App\Services\JournalService::journalSavingWithdrawal($saving, 'bank');
                    }
                    $generated++;
                } catch (\Exception $e) {
                    $errors[] = "Saving #{$saving->id}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Berhasil generate {$generated} jurnal dari " . $savingsWithoutJournals->count() . " simpanan tanpa jurnal.";
            
            if (!empty($errors)) {
                return redirect()->back()
                    ->with('warning', $message)
                    ->with('import_errors', $errors);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal generate jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Generate Journal Entries for Loans that don't have journals yet
     */
    public function generateLoansJournals(Request $request)
    {
        try {
            DB::beginTransaction();

            // Get loans without journal entries (only disbursed/active/completed)
            $loansWithoutJournals = Loan::whereDoesntHave('journalEntry')
                ->whereIn('status', ['active', 'completed'])
                ->with(['member.user'])
                ->get();

            $generated = 0;
            $errors = [];

            foreach ($loansWithoutJournals as $loan) {
                try {
                    \App\Services\JournalService::journalLoanDisbursement($loan);
                    $generated++;
                } catch (\Exception $e) {
                    $errors[] = "Loan #{$loan->loan_number}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Berhasil generate {$generated} jurnal dari " . $loansWithoutJournals->count() . " pinjaman tanpa jurnal.";
            
            if (!empty($errors)) {
                return redirect()->back()
                    ->with('warning', $message)
                    ->with('import_errors', $errors);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal generate jurnal: ' . $e->getMessage());
        }
    }
}
