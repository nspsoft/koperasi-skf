<?php

namespace App\Imports;

use App\Models\Saving;
use App\Models\Member;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Carbon\Carbon;

class SavingsImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures, \App\Traits\DateParserTrait;

    public function onRow(Row $row)
    {
        $rowData = $row->toArray();

        // Find member by member_id
        $member = Member::where('member_id', $rowData['id_anggota'])->first();
        
        if (!$member) {
            return null; // Skip if member not found
        }

        $amount = (float) str_replace(['.', ','], ['', '.'], $rowData['jumlah']);
        $type = strtolower($rowData['jenis']);
        $transactionType = $this->parseTransactionType($rowData['transaksi'] ?? 'setoran');

        // Create the saving record
        $saving = Saving::create([
            'member_id' => $member->id,
            'type' => $type,
            'transaction_type' => $transactionType,
            'amount' => $amount,
            'transaction_date' => $this->parseDate($rowData['tanggal']) ?? now(),
            'reference_number' => $rowData['no_referensi'] ?? 'IMP-' . date('Ymd') . '-' . rand(1000, 9999),
            'description' => $rowData['keterangan'] ?? 'Import dari Excel (via payroll)',
        ]);

        // Auto-generate journal entry (using bank since import is via payroll)
        if ($saving) {
            $saving->load('member.user');
            try {
                if ($transactionType === 'deposit') {
                    \App\Services\JournalService::journalSavingDeposit($saving, 'bank');
                } else {
                    \App\Services\JournalService::journalSavingWithdrawal($saving, 'bank');
                }
            } catch (\Exception $e) {
                // Log error but don't fail the import
                \Log::warning("Failed to create journal for saving #{$saving->id}: " . $e->getMessage());
            }
        }

        return $saving;
    }

    protected function parseTransactionType(string $type): string
    {
        $type = strtolower(trim($type));
        
        if (in_array($type, ['penarikan', 'tarik', 'withdrawal', 'w'])) {
            return 'withdrawal';
        }
        
        return 'deposit';
    }

    public function rules(): array
    {
        return [
            'id_anggota' => 'required',
            'jenis' => 'required|in:pokok,wajib,sukarela,Pokok,Wajib,Sukarela',
            'jumlah' => 'required',
            'tanggal' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'id_anggota.required' => 'Kolom id_anggota wajib diisi',
            'jenis.required' => 'Kolom jenis wajib diisi',
            'jenis.in' => 'Jenis harus: pokok, wajib, atau sukarela',
            'jumlah.required' => 'Kolom jumlah wajib diisi',
            'tanggal.required' => 'Kolom tanggal wajib diisi',
        ];
    }
}

