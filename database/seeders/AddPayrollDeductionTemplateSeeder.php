<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTemplate;

class AddPayrollDeductionTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $name = 'Surat Pemotongan Payroll Simpanan Wajib';
        $type = 'official';
        $placeholders = ['periode'];
        $content = '
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

<div style="margin-top: 50px; float: right; width: 250px; text-align: center;">
    <p>Karawang, {{today}}</p>
    <p>Pengurus Koperasi,</p>
    <div style="height: 80px;"></div>
    <p><strong>( ____________________ )</strong></p>
</div>
';

        DocumentTemplate::updateOrCreate(
            ['name' => $name],
            [
                'type' => $type,
                'placeholders' => json_encode($placeholders),
                'content' => trim($content)
            ]
        );

        // Add Kredit Mart Template
        $nameKm = 'Surat Pemotongan Payroll Kredit Mart';
        $contentKm = '
<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="text-decoration: underline; margin-bottom: 5px;">SURAT PEMOTONGAN PAYROLL KREDIT MART</h3>
    <p style="margin-top: 0;">Nomor: .../PAY-KM/{{month}}/{{year}}</p>
</div>

<p>Kepada Yth,<br><strong>Bagian HRD / Payroll</strong><br>PT Steel Pipe Industry Of Indonesia Tbk<br>Di Tempat</p>

<p>Dengan hormat,</p>
<p>Sehubungan dengan transaksi belanja anggota di Koperasi Mart, bersama ini kami sampaikan daftar anggota untuk dilakukan pemotongan Kredit Mart melalui payroll periode <strong>{{periode}}</strong>.</p>

<div style="margin-top: 20px; margin-bottom: 20px;">
    {{lampiran_anggota}}
</div>

<p>Demikian surat ini kami sampaikan. Atas bantuan dan kerjasamanya, kami ucapkan terima kasih.</p>

<div style="margin-top: 50px; float: right; width: 250px; text-align: center;">
    <p>Karawang, {{today}}</p>
    <p>Pengurus Koperasi,</p>
    <div style="height: 80px;"></div>
    <p><strong>( ____________________ )</strong></p>
</div>
';
        DocumentTemplate::updateOrCreate(
            ['name' => $nameKm],
            [
                'type' => $type,
                'placeholders' => json_encode($placeholders),
                'content' => trim($contentKm)
            ]
        );
    }
}
