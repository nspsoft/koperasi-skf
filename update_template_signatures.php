<?php

use App\Models\DocumentTemplate;

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$nameKm = 'Surat Pemotongan Payroll Simpanan Wajib';
$template = DocumentTemplate::where('name', $nameKm)->first();

if ($template) {
    $newContent = '
<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="text-decoration: underline; margin-bottom: 5px;">SURAT PEMOTONGAN PAYROLL SIMPANAN WAJIB</h3>
    <p style="margin-top: 0;">Nomor: .../PAY-SW/{{month}}/{{year}}</p>
</div>

<p>Kepada Yth,<br><strong>Bagian HRD / Payroll</strong><br>PT Steel Pipe Industry Of Indonesia Tbk<br>Di Tempat</p>

<p>Dengan hormat,</p>
<p>Sehubungan dengan kewajiban anggota Koperasi Karyawan Spindo Karawang Factory, bersama ini kami sampaikan daftar anggota untuk dilakukan pemotongan Simpanan Wajib melalui payroll periode <strong>{{periode}}</strong>.</p>

<div style="margin-top: 20px; margin-bottom: 20px;">
    {{lampiran_anggota}}
</div>

<p>Demikian surat ini kami sampaikan. Atas bantuan dan kerjasamanya, kami ucapkan terima kasih.</p>

<table style="width: 100%; margin-top: 30px; text-align: center; border: none;">
    <tr>
        <td style="width: 33%; vertical-align: top; border: none;">
            <p style="margin-bottom: 5px;">Karawang, {{today}}</p>
            <p style="margin-bottom: 70px;">Di buat :</p>
            <p><strong>( Bendahara )</strong></p>
        </td>
        <td style="width: 33%; vertical-align: top; border: none;">
            <br><br>
            <p style="margin-bottom: 70px;">Di periksa :</p>
            <p><strong>( Ketua )</strong></p>
        </td>
        <td style="width: 33%; vertical-align: top; border: none;">
            <br><br>
            <p style="margin-bottom: 70px;">Di ketahui :</p>
            <p><strong>( HRD SPINDO Karawang )</strong></p>
        </td>
    </tr>
</table>
';

    $template->update(['content' => trim($newContent)]);
    echo "Template berhasil diupdate!\n";
} else {
    echo "Template tidak ditemukan!\n";
}
