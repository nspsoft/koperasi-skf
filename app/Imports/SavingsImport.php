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
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Carbon\Carbon;

class SavingsImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithChunkReading
{
    use SkipsErrors, SkipsFailures, \App\Traits\DateParserTrait;

    public function prepareForValidation($data, $index)
    {
        if (empty($data['id_transaksi'])) {
            $altKeys = ['id transaksi', 'id-transaksi', 'idtransaksi'];
            foreach ($altKeys as $altKey) {
                if (!empty($data[$altKey])) {
                    $data['id_transaksi'] = $data[$altKey];
                    break;
                }
            }
        }

        if (isset($data['id_anggota'])) {
            $data['id_anggota'] = trim((string) $data['id_anggota']);
        }

        if (isset($data['jenis'])) {
            $data['jenis'] = strtolower(trim((string) $data['jenis']));
        }

        if (isset($data['transaksi'])) {
            $data['transaksi'] = strtolower(trim((string) $data['transaksi']));
        }

        if (isset($data['keterangan'])) {
            $data['keterangan'] = trim((string) $data['keterangan']);
        }

        if (!empty($data['id_transaksi'])) {
            $data['id_anggota'] = null;
            $data['jenis'] = null;
            $data['jumlah'] = null;
            $data['tanggal'] = null;
            $data['transaksi'] = null;
        }

        return $data;
    }

    public function onRow(Row $row)
    {
        $rowData = $row->toArray();

        $savingId = $rowData['id_transaksi'] ?? null;
        if ($savingId) {
            $saving = Saving::find($savingId);
            if (! $saving) {
                return null;
            }
            $saving->update([
                'description' => array_key_exists('keterangan', $rowData) ? $rowData['keterangan'] : $saving->description,
            ]);
            return $saving;
        }

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
            'id_transaksi' => 'nullable|integer|exists:savings,id',
            'id_anggota' => 'required_without:id_transaksi',
            'jenis' => 'required_without:id_transaksi|nullable|in:pokok,wajib,sukarela,Pokok,Wajib,Sukarela',
            'transaksi' => 'required_without:id_transaksi',
            'jumlah' => 'required_without:id_transaksi',
            'tanggal' => 'required_without:id_transaksi',
        ];
    }

    public function chunkSize(): int
    {
        return 200;
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

