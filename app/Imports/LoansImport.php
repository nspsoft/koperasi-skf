<?php

namespace App\Imports;

use App\Models\Loan;
use App\Models\Member;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Carbon\Carbon;

class LoansImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures, \App\Traits\DateParserTrait;

    public function onRow(Row $row)
    {
        $rowData = $row->toArray();

        $member = Member::where('member_id', $rowData['id_anggota'])->first();
        
        if (!$member) {
            return null;
        }

        $amount = (float) str_replace(['.', ','], ['', '.'], $rowData['jumlah']);
        $rate = (float) ($rowData['bunga'] ?? 1.5);
        $duration = (int) $rowData['tenor'];
        
        // Calculate totals
        $totalInterest = $amount * ($rate / 100) * ($duration / 12);
        $totalAmount = $amount + $totalInterest;
        $monthlyInstallment = $totalAmount / $duration;

        $status = strtolower($rowData['status'] ?? 'pending');
        $applicationDate = $this->parseDate($rowData['tanggal_pengajuan']);
        $disbursementDate = isset($rowData['tanggal_pencairan']) ? $this->parseDate($rowData['tanggal_pencairan']) : null;

        // Create the loan record
        $loan = Loan::create([
            'member_id' => $member->id,
            'loan_number' => $rowData['no_pinjaman'],
            'loan_type' => strtolower($rowData['jenis']),
            'amount' => $amount,
            'interest_rate' => $rate,
            'duration_months' => $duration,
            'monthly_installment' => round($monthlyInstallment, 2),
            'total_amount' => round($totalAmount, 2),
            'remaining_amount' => $status === 'active' ? round($totalAmount, 2) : ($status === 'completed' ? 0 : round($totalAmount, 2)),
            'status' => $status,
            'purpose' => $rowData['tujuan'] ?? $rowData['keperluan'] ?? null,
            'application_date' => $applicationDate,
            'approval_date' => isset($rowData['tanggal_persetujuan']) ? $this->parseDate($rowData['tanggal_persetujuan']) : ($status !== 'pending' ? $applicationDate : null),
            'disbursement_date' => $disbursementDate,
            'approved_by' => !in_array($status, ['pending', 'rejected']) ? 1 : null,
            'notes' => $rowData['catatan'] ?? $rowData['keterangan'] ?? 'Import dari Excel',
        ]);

        // Auto-generate journal entry for active/completed loans (disbursed loans)
        if ($loan && in_array($status, ['active', 'completed'])) {
            $loan->load('member.user');
            try {
                \App\Services\JournalService::journalLoanDisbursement($loan);
            } catch (\Exception $e) {
                // Log error but don't fail the import
                \Log::warning("Failed to create journal for loan #{$loan->loan_number}: " . $e->getMessage());
            }
        }

        return $loan;
    }

    public function rules(): array
    {
        return [
            'id_anggota' => 'required',
            'no_pinjaman' => 'required|unique:loans,loan_number',
            'jenis' => 'required|in:regular,emergency,education,special,Regular,Emergency,Education,Special',
            'jumlah' => 'required',
            'tenor' => 'required|integer|min:1|max:60',
            'tanggal_pengajuan' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'id_anggota.required' => 'Kolom id_anggota wajib diisi',
            'no_pinjaman.required' => 'Kolom no_pinjaman wajib diisi',
            'no_pinjaman.unique' => 'Nomor pinjaman sudah terdaftar',
            'jenis.required' => 'Kolom jenis wajib diisi',
            'jenis.in' => 'Jenis harus: regular, emergency, education, atau special',
            'jumlah.required' => 'Kolom jumlah wajib diisi',
            'tenor.required' => 'Kolom tenor wajib diisi',
            'tenor.integer' => 'Tenor harus berupa angka',
            'tanggal_pengajuan.required' => 'Kolom tanggal_pengajuan wajib diisi',
        ];
    }
}

