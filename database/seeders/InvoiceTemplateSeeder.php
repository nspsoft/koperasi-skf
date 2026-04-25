<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTemplate;

class InvoiceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $content = '
<div style="text-align: center; margin-bottom: 20px;">
    <h2 style="text-decoration: underline; margin-bottom: 5px;">INVOICE</h2>
    <p style="margin-top: 0;">Nomor: {{nomor_invoice}}</p>
</div>

<table style="width: 100%; margin-bottom: 20px;">
    <tr>
        <td style="width: 50%; vertical-align: top;">
            <p style="margin-bottom: 5px; font-weight: bold;">KEPADA:</p>
            <div style="margin: 0; line-height: 1.4;">{{tujuan_penerima}}<div style="font-size: 9pt; color: #555; margin-top: 2px;">{{alamat_tujuan}}</div></div>
        </td>
        <td style="width: 50%; vertical-align: top; text-align: right;">
            <table style="float: right;">
                <tr><td style="text-align: left;">Tanggal</td><td style="padding: 0 5px;">:</td><td style="text-align: left;">{{tanggal_invoice}}</td></tr>
                <tr><td style="text-align: left;">Jatuh Tempo</td><td style="padding: 0 5px;">:</td><td style="text-align: left;">{{jatuh_tempo}}</td></tr>
                <tr><td style="text-align: left;">Perihal</td><td style="padding: 0 5px;">:</td><td style="text-align: left;">{{perihal}}</td></tr>
            </table>
        </td>
    </tr>
</table>

<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="border: 1px solid #000; padding: 8px; text-align: left;">RINCIAN TAGIHAN</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #000; padding: 10px; min-height: 200px; vertical-align: top;">
                {{item_tagihan}}
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td style="border: 1px solid #000; padding: 8px; text-align: right; background-color: #f9f9f9;">
                <strong>TOTAL AKHIR: Rp {{total_tagihan}}</strong>
            </td>
        </tr>
    </tfoot>
</table>

<p><strong>Terbilang:</strong> # {{terbilang}} #</p>

<div style="margin-top: 30px;">
    <p style="font-weight: bold; margin-bottom: 5px;">CATATAN PEMBAYARAN:</p>
    <div style="padding: 10px; border: 1px dashed #ccc; background-color: #fffdec;">
        {{catatan_pembayaran}}
    </div>
</div>

<div style="margin-top: 10px;">
    <p>Demikian invoice ini kami sampaikan, agar dapat dipergunakan sebagaimana mestinya.</p>
</div>

<div style="margin-top: 50px; float: right; width: 250px; text-align: center;">
    <p>Karawang, {{today}}</p>
    <p>Bendahara Koperasi,</p>
    <div style="height: 80px;"></div>
    <p><strong>( ____________________ )</strong></p>
</div>';

        DocumentTemplate::updateOrCreate(
            ['name' => 'Invoice Penagihan'],
            [
                'type' => 'official',
                'placeholders' => json_encode(["nomor_invoice", "tujuan_penerima", "alamat_tujuan", "perihal", "tanggal_invoice", "jatuh_tempo", "item_tagihan", "total_tagihan", "terbilang", "catatan_pembayaran"]),
                'content' => trim($content)
            ]
        );
    }
}
