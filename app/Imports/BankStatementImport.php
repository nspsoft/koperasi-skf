<?php

namespace App\Imports;

use App\Models\BankTransaction;
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

class BankStatementImport implements OnEachRow, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures, \App\Traits\DateParserTrait;

    public function onRow(Row $row)
    {
        $rowData = $row->toArray();
        
        // Handle Empty Rows
        if (empty($rowData['tanggal']) && empty($rowData['date'])) {
            return null;
        }

        // Parse Date
        $dateString = $rowData['tanggal'] ?? $rowData['date'];
        $parsedDate = $this->parseDate($dateString) ?? now();

        // Parse Amount and Type
        // Option A: Single Amount Column (Positive/Negative)
        // Option B: Debit/Credit Columns
        
        $credit = isset($rowData['kredit']) ? $this->parseAmount($rowData['kredit']) : (isset($rowData['credit']) ? $this->parseAmount($rowData['credit']) : 0);
        $debit = isset($rowData['debit']) ? $this->parseAmount($rowData['debit']) : 0;
        $amount = isset($rowData['jumlah']) ? $this->parseAmount($rowData['jumlah']) : (isset($rowData['amount']) ? $this->parseAmount($rowData['amount']) : 0);

        // Determine Final Amount and Type based on Bank Statement logic:
        // Credit = Money In (Positive)
        // Debit = Money Out (Negative)
        
        if ($credit > 0) {
            $amount = $credit;
            $type = 'credit'; // Money In
        } elseif ($debit > 0) {
            $amount = $debit; // Absolute amount in DB
            $type = 'debit'; // Money Out
        } else {
            // Using single 'amount' column
            if ($amount >= 0) {
                $type = 'credit';
            } else {
                $type = 'debit';
                $amount = abs($amount);
            }
        }

        // Logic check: if "type" column is explicit
        if (isset($rowData['jenis']) || isset($rowData['type'])) {
            $typeStr = strtolower($rowData['jenis'] ?? $rowData['type']);
            if (in_array($typeStr, ['d', 'db', 'debit', 'dr', 'out', 'k keluar'])) {
                $type = 'debit';
            } elseif (in_array($typeStr, ['k', 'cr', 'credit', 'in', 'masuk'])) {
                $type = 'credit';
            }
        }

        // Create the Bank Transaction record
        $transaction = BankTransaction::create([
            'transaction_date' => $parsedDate,
            'description' => $rowData['keterangan'] ?? ($rowData['description'] ?? 'Imported Transaction'),
            'amount' => $amount,
            'type' => $type,
            'reference_number' => $rowData['no_referensi'] ?? ($rowData['ref'] ?? null),
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return $transaction;
    }

    private function parseAmount($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        // Remove currency symbols and thousand separators, keep decimal point check
        // Assuming format like "1.000.000,00" or "1,000,000.00"
        // Simple cleanup: remove everything except numbers, dash, dot, comma
        $clean = preg_replace('/[^0-9\.\,\-]/', '', $value);
        
        // If comma is used as decimal separator (Indonesian format)
        if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
             // Both exist. If dot is first, it's thousand sep.
             if (strpos($clean, '.') < strpos($clean, ',')) {
                 $clean = str_replace('.', '', $clean);
                 $clean = str_replace(',', '.', $clean);
             } else {
                 $clean = str_replace(',', '', $clean);
             }
        } elseif (strpos($clean, ',') !== false) {
            // Only comma, likely decimal in ID
             $clean = str_replace(',', '.', $clean);
        }
        
        return (float) $clean;
    }
}
