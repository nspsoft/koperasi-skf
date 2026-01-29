<?php

namespace App\Traits;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

trait DateParserTrait
{
    /**
     * Parse date from various formats (Excel Serial, d/m/Y, Y-m-d).
     *
     * @param mixed $value
     * @return \Carbon\Carbon|null
     */
    public function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Case 1: Excel Serial Number (e.g. 45290)
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value));
            }

            // Case 2: String Date
            // Try standard formats first
            $formats = [
                'd/m/Y',
                'd-m-Y',
                'Y-m-d',
                'Y/m/d',
                'd-M-Y', // 01-Jan-2024
                'd M Y', // 01 Jan 2024
            ];

            foreach ($formats as $format) {
                try {
                    $date = Carbon::createFromFormat($format, $value);
                    if ($date && $date->format($format) == $value) {
                         // Double check strictly? createFromFormat is usually strict by default unless ! modifier used
                         // But we want to be lenient about time parts, so verify date part matches
                         return $date->startOfDay();
                    }
                    // If parsing succeeded but format check failed (rare with createFromFormat), just use it if valid
                    if ($date) return $date->startOfDay();
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Fallback: Carbon's smart parse (might guess m/d/y on ambiguity)
            return Carbon::parse($value)->startOfDay();

        } catch (\Exception $e) {
            // Log error? or just return null?
            // For imports, maybe better to return null and let validation fail if required
            return null;
        }
    }
}
