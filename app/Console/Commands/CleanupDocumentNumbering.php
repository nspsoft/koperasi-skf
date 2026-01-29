<?php

namespace App\Console\Commands;

use App\Models\DocumentTemplate;
use Illuminate\Console\Command;

class CleanupDocumentNumbering extends Command
{
    protected $signature = 'app:cleanup-document-numbering';
    protected $description = 'Replace hardcoded document numbering with placeholders';

    public function handle()
    {
        $this->info('Cleaning up document templates numbering...');

        $templates = DocumentTemplate::all();
        $updated = 0;

        foreach ($templates as $template) {
            $content = $template->content;
            
            if (str_contains($content, '.../')) {
                $this->line("  Found pattern in: {$template->name}");
                
                // Replace variations of numbering lines
                $newContent = preg_replace('/(No:|Nomor:)\s*\.\.\.\/.*?\{\{year\}\}/i', '$1 {{nomor_surat}}', $content);
                
                // If regex didn't change anything, try a simpler one
                if ($newContent === $content) {
                    $newContent = preg_replace('/\.\.\.\/.*?\{\{year\}\}/i', '{{nomor_surat}}', $content);
                }

                if ($newContent !== $content) {
                    $template->update(['content' => $newContent]);
                    $updated++;
                    $this->line("  ✓ Updated numbering in: {$template->name}");
                } else {
                    $this->warn("  ⚠ Failed to update pattern in: {$template->name}");
                }
            }
        }

        $this->info("Done! {$updated} templates cleaned.");
        return 0;
    }
}
