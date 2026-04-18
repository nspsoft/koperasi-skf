<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DocumentTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $t = DocumentTemplate::where('name', 'Invoice Penagihan')->first();
        if ($t) {
            $p = json_decode($t->placeholders, true);
            if (!in_array('alamat_tujuan', $p)) {
                $idx = array_search('tujuan_penerima', $p);
                if ($idx !== false) {
                    array_splice($p, $idx + 1, 0, 'alamat_tujuan');
                } else {
                    $p[] = 'alamat_tujuan';
                }
                $t->placeholders = json_encode($p);
            }

            // Fix Structure: Update recipient name and address block to support better spacing
            // We attempt multiple versions in case of partial manual updates
            $targets = [
                '<div style="margin: 0; line-height: 1.1;">{{tujuan_penerima}}</div><div style="margin: 0; font-size: 9pt; color: #555; line-height: 1.1;">{{alamat_tujuan}}</div>',
                '<p style="margin: 0;">{{tujuan_penerima}}</p><div style="margin: 0; font-size: 9pt; color: #555; white-space: pre-line;">{{alamat_tujuan}}</div>',
                '<p style="margin: 0;">{{tujuan_penerima}}</p>'
            ];

            $new = '<div style="margin: 0; line-height: 1.4;">{{tujuan_penerima}}<div style="font-size: 9pt; color: #555; margin-top: 2px;">{{alamat_tujuan}}</div></div>';

            foreach ($targets as $target) {
                if (strpos($t->content, $target) !== false) {
                    $t->content = str_replace($target, $new, $t->content);
                    break;
                }
            }
            
            $t->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $t = DocumentTemplate::where('name', 'Invoice Penagihan')->first();
        if ($t) {
            $p = json_decode($t->placeholders, true);
            $t->placeholders = json_encode(array_values(array_filter($p, fn($i) => $i !== 'alamat_tujuan')));
            
            $target = '<div style="margin: 0; line-height: 1.4;">{{tujuan_penerima}}<div style="font-size: 9pt; color: #555; margin-top: 2px;">{{alamat_tujuan}}</div></div>';
            $old = '<p style="margin: 0;">{{tujuan_penerima}}</p>';
            
            $t->content = str_replace($target, $old, $t->content);
            $t->save();
        }
    }
};
