<?php

namespace App\Console\Commands;

use App\Models\DocumentTemplate;
use Illuminate\Console\Command;

class SetupDocumentCodes extends Command
{
    protected $signature = 'app:setup-document-codes';
    protected $description = 'Setup default codes for document templates';

    public function handle()
    {
        $codes = [
            'Surat Undangan' => 'UND',
            'Surat Keterangan Anggota' => 'SK-KOP',
            'Surat Penunjukan Pengurus' => 'SK',
            'Surat Pemberitahuan' => 'PEMBERITAHUAN',
            'Surat Peringatan' => 'SP',
            'Surat Pengunduran Diri' => 'RESIGN',
            'Surat Permohonan Pinjaman' => 'LOAN',
            'Surat Perjanjian Pinjaman' => 'AGREEMENT',
            'Surat Pemotongan Payroll Kredit Mart' => 'PAY-KM',
            'Surat Pemotongan Payroll Simpanan Wajib' => 'PAY-SW',
        ];

        $this->info('Populating document template codes...');

        foreach ($codes as $name => $code) {
            $template = DocumentTemplate::where('name', $name)->first();
            if ($template) {
                $template->update(['code' => $code]);
                $this->line("  ✓ Set {$code} for {$name}");
            }
        }

        // Add 'kode_surat' to placeholders if not exists
        foreach (DocumentTemplate::all() as $template) {
            $placeholders = json_decode($template->placeholders, true) ?: [];
            if (!in_array('kode_surat', $placeholders)) {
                array_unshift($placeholders, 'kode_surat');
                $template->update(['placeholders' => json_encode($placeholders)]);
                $this->line("  ✓ Added 'kode_surat' placeholder to {$template->name}");
            }
        }

        $this->info('Done!');
        return 0;
    }
}
