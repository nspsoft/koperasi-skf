<?php

namespace App\Console\Commands;

use App\Models\DocumentTemplate;
use Illuminate\Console\Command;

class UpdateDocumentRecipients extends Command
{
    protected $signature = 'app:update-document-recipients';
    protected $description = 'Update document templates to use dynamic recipient placeholder';

    public function handle()
    {
        $this->info('Updating document templates with dynamic recipient field...');

        $templates = DocumentTemplate::all();
        $updated = 0;

        foreach ($templates as $template) {
            $placeholders = json_decode($template->placeholders, true) ?: [];
            $content = $template->content;
            $changed = false;

            // Add tujuan_penerima to placeholders if not exists
            if (!in_array('tujuan_penerima', $placeholders)) {
                array_unshift($placeholders, 'tujuan_penerima');
                $changed = true;
            }

            // Replace hardcoded "Kepada Yth" patterns with dynamic placeholder
            $patterns = [
                // Pattern 1: Kepada Yth,<br>Pengurus Koperasi...<br>Di Tempat
                '/<p>Kepada Yth,<br>Pengurus Koperasi Karyawan Spindo Karawang Factory<br>Di Tempat<\/p>/i',
                // Pattern 2: Kepada Yth,<br>Bapak/Ibu Anggota Koperasi<br>Di Tempat
                '/<p>Kepada Yth,<br>Bapak\/Ibu Anggota Koperasi<br>Di Tempat<\/p>/i',
                // Pattern 3: Kepada Yth,<br>Seluruh Anggota Koperasi<br>Di Tempat
                '/<p>Kepada Yth,<br>Seluruh Anggota Koperasi<br>Di Tempat<\/p>/i',
                // Pattern 4: Kepada Yth,<br><strong>Bagian HRD / Payroll</strong><br>PT Steel...<br>Di Tempat
                '/<p>Kepada Yth,<br><strong>Bagian HRD \/ Payroll<\/strong><br>PT Steel Pipe Industry Of Indonesia Tbk<br>Di Tempat<\/p>/i',
            ];

            $replacement = '<p>Kepada Yth,<br>{{tujuan_penerima}}<br>Di Tempat</p>';

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    $content = preg_replace($pattern, $replacement, $content);
                    $changed = true;
                }
            }

            if ($changed) {
                $template->update([
                    'placeholders' => json_encode($placeholders),
                    'content' => $content,
                ]);
                $updated++;
                $this->line("  ✓ Updated: {$template->name}");
            }
        }

        $this->info("Done! {$updated} templates updated.");
        return 0;
    }
}
