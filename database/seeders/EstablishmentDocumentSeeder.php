<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTemplate;

class EstablishmentDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Berita Acara Rapat Pembentukan',
                'type' => 'official',
                'placeholders' => ['no_surat', 'hari_tanggal', 'waktu', 'tempat', 'jumlah_hadir', 'pimpinan_rapat', 'sekretaris_rapat', 'nama_koperasi', 'ketua_terpilih', 'susunan_pengurus_lainnya', 'pengawas_terpilih', 'susunan_pengawas_lainnya', 'simpanan_pokok', 'simpanan_wajib'],
                'content' => '
                    <style>
                        /* Set standar font dan line-height yang konsisten */
                        p, li, td { font-size: 11pt; line-height: 1.4; text-align: justify; margin-bottom: 0; padding-bottom: 0; }
                        /* Header Utama */
                        h3 { font-size: 14pt; margin-bottom: 5px; font-weight: bold; text-align: center; text-decoration: underline; }
                        /* List Numbering */
                        ol { margin-top: 5px; margin-bottom: 5px; padding-left: 25px; }
                        li { margin-bottom: 3px; }
                        /* Kotak Tanda Tangan */
                        .signature-box { height: 50px; }
                        /* Helper Class */
                        .center-text { text-align: center; }
                        .bold-text { font-weight: bold; }
                    </style>

                    <div style="text-align: center; margin-bottom: 15px;">
                        <h3>BERITA ACARA RAPAT PEMBENTUKAN KOPERASI</h3>
                        <p style="margin-top: 5px; font-size: 11pt; text-align: center;">Nomor: {{no_surat}}</p>
                    </div>
                    
                    <p style="margin-bottom: 5px;">Pada hari ini <strong>{{hari_tanggal}}</strong>, pukul <strong>{{waktu}}</strong>, bertempat di <strong>{{tempat}}</strong>, telah diselenggarakan Rapat Pembentukan Koperasi yang dihadiri oleh <strong>{{jumlah_hadir}}</strong> orang pendiri (Daftar Hadir terlampir).</p>
                    
                    <p style="margin-bottom: 5px;">Rapat dipimpin oleh saudara/i <strong>{{pimpinan_rapat}}</strong> dan dicatat oleh saudara/i <strong>{{sekretaris_rapat}}</strong>.</p>
                    
                    <p style="margin-bottom: 5px;">Setelah melalui musyawarah dan mufakat, Rapat memutuskan dan menetapkan hal-hal sebagai berikut:</p>
                    
                    <ol>
                        <li>Sepakat membentuk Koperasi dengan nama: <strong>"{{nama_koperasi}}"</strong>.</li>
                        <li>Menerima dan mengesahkan Anggaran Dasar (AD) dan Anggaran Rumah Tangga (ART) Koperasi.</li>
                        <li>Menetapkan Susunan Pengurus dan Pengawas Pertama periode saat ini sebagai berikut:
                            
                            <!-- Tabel Layout Pengurus & Pengawas (Side-by-Side) -->
                            <table style="width: 100%; border: none; margin-top: 5px; border-collapse: collapse;">
                                <tr>
                                    <!-- Kolom Pengurus -->
                                    <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                                        <div class="center-text bold-text" style="margin-bottom: 2px; text-decoration: underline;">A. PENGURUS</div>
                                        <table style="width: 100%; border: none; font-size: 11pt;">
                                            <tr>
                                                <td style="width: 80px; vertical-align: top; padding: 0;">Ketua</td>
                                                <td style="width: 10px; vertical-align: top; padding: 0;">:</td>
                                                <td style="vertical-align: top; padding: 0;">{{ketua_terpilih}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="vertical-align: top; padding-top: 2px;">{{susunan_pengurus_lainnya}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                    
                                    <!-- Kolom Pengawas -->
                                    <td style="width: 50%; vertical-align: top; padding-left: 15px;">
                                        <div class="center-text bold-text" style="margin-bottom: 2px; text-decoration: underline;">B. PENGAWAS</div>
                                        <table style="width: 100%; border: none; font-size: 11pt;">
                                            <tr>
                                                <td style="width: 90px; vertical-align: top; padding: 0;">Koordinator</td>
                                                <td style="width: 10px; vertical-align: top; padding: 0;">:</td>
                                                <td style="vertical-align: top; padding: 0;">{{pengawas_terpilih}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="vertical-align: top; padding-top: 2px;">{{susunan_pengawas_lainnya}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </li>
                        <li style="margin-top: 5px;">Menetapkan Simpanan Pokok sebesar Rp {{simpanan_pokok}} dan Simpanan Wajib sebesar Rp {{simpanan_wajib}} per bulan.</li>
                        <li>Memberikan kuasa kepada Pengurus Terpilih untuk menghadap Notaris guna memproses pengesahan Badan Hukum Koperasi.</li>
                    </ol>

                    <p style="margin-top: 10px; margin-bottom: 10px;">Demikian Berita Acara ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
                    
                    <!-- Wrapper Tanda Tangan -->
                    <div style="page-break-inside: avoid;">
                        <table style="width: 100%; border: none; border-collapse: collapse;">
                            <tr>
                                <td style="width: 33%; text-align: center; vertical-align: top;">
                                    <p style="margin-bottom: 0; text-align: center;">Sekretaris Rapat,</p>
                                    <div class="signature-box"></div>
                                    <p style="text-align: center;"><strong>( {{sekretaris_rapat}} )</strong></p>
                                </td>
                                <td style="width: 34%; text-align: center; vertical-align: top;">
                                    <p style="margin-bottom: 0; text-align: center;">Pengawas Terpilih,</p>
                                    <div class="signature-box"></div>
                                    <p style="text-align: center;"><strong>( {{pengawas_terpilih}} )</strong></p>
                                </td>
                                <td style="width: 33%; text-align: center; vertical-align: top;">
                                    <p style="margin-bottom: 0; text-align: center;">Pimpinan Rapat,</p>
                                    <div class="signature-box"></div>
                                    <p style="text-align: center;"><strong>( {{pimpinan_rapat}} )</strong></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                '
            ],
            [
                'name' => 'Daftar Hadir Rapat Pendirian',
                'type' => 'official',
                'placeholders' => ['hari_tanggal', 'waktu', 'tempat', 'pimpinan_rapat', 'pengawas_terpilih'],
                'content' => '
                    <style>
                        p, li, td { font-size: 10pt; line-height: 1.3; }
                        h3 { font-size: 12pt; margin-bottom: 15px; font-weight: bold; text-decoration: underline; }
                        .signature-box { height: 60px; }
                        th { padding: 8px; font-size: 10pt; background-color: #f0f0f0; }
                        /* Increased padding by 50% from 8px to 12px for taller rows */
                        td.cell { padding: 12px; border: 1px solid #000; }
                    </style>
                   <div style="text-align: center; margin-bottom: 15px;">
                        <h3>DAFTAR HADIR RAPAT PEMBENTUKAN KOPERASI</h3>
                    </div>
                    <table style="width: 100%; margin-bottom: 10px; border: none; font-size: 10pt;">
                        <tr><td style="width: 120px;">Hari / Tanggal</td><td>: {{hari_tanggal}}</td></tr>
                        <tr><td>Waktu</td><td>: {{waktu}}</td></tr>
                        <tr><td>Tempat</td><td>: {{tempat}}</td></tr>
                    </table>

                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                        <thead>
                            <tr>
                                <th style="border: 1px solid #000; width: 30px; text-align: center;">No</th>
                                <th style="border: 1px solid #000; text-align: center;">Nama Lengkap</th>
                                <th style="border: 1px solid #000; text-align: center;">Alamat / NIK</th>
                                <th style="border: 1px solid #000; text-align: center;">Jabatan / Pekerjaan</th>
                                <th style="border: 1px solid #000; width: 100px; text-align: center;">Tanda Tangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="cell" style="text-align: center;">1</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">2</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">3</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">4</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">5</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">6</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">7</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">8</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">9</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">10</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">11</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">12</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">13</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">14</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">15</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">16</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">17</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">18</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">19</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                            <tr><td class="cell" style="text-align: center;">20</td><td class="cell"></td><td class="cell"></td><td class="cell"></td><td class="cell"></td></tr>
                        </tbody>
                    </table>
                    
                    <div style="page-break-inside: avoid; margin-top: 20px;">
                        <table style="width: 100%; border: none;">
                            <tr>
                                <td style="width: 50%; text-align: center; vertical-align: top;">
                                    <p style="margin-bottom: 0px; text-align: center;">Pengawas Terpilih,</p>
                                    <div class="signature-box"></div>
                                    <p style="text-align: center;"><strong>( {{pengawas_terpilih}} )</strong></p>
                                </td>
                                <td style="width: 50%; text-align: center; vertical-align: top;">
                                    <p style="margin-bottom: 0px; text-align: center;">Pimpinan Rapat,</p>
                                    <div class="signature-box"></div>
                                    <p style="text-align: center;"><strong>( {{pimpinan_rapat}} )</strong></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                '
            ],
            [
                'name' => 'Surat Kuasa Pendirian Koperasi',
                'type' => 'official',
                'placeholders' => ['no_surat', 'nama_pemberi_kuasa', 'nama_penerima_kuasa', 'nama_koperasi'],
                'content' => '
                     <style>
                        p, li, td { font-size: 11pt; line-height: 1.5; text-align: justify; }
                        h3 { font-size: 14pt; margin-bottom: 5px; font-weight: bold; text-decoration: underline; }
                        .signature-box { height: 75px; }
                    </style>
                    <div style="text-align: center; margin-bottom: 25px;">
                        <h3>SURAT KUASA</h3>
                         <p style="margin-top: 5px; font-size: 11pt;">Nomor: {{no_surat}}</p>
                    </div>
                    <p style="margin-bottom: 8px;">Yang bertanda tangan di bawah ini:</p>
                    <table style="width: 100%; margin-left: 15px; margin-bottom: 15px; border: none;">
                        <tr><td style="width: 100px;">Nama</td><td>: (Para Pendiri Koperasi - Daftar Terlampir)</td></tr>
                        <tr><td>Alamat</td><td>: (Sesuai KTP)</td></tr>
                    </table>
                    <p style="margin-top: 0px; margin-bottom: 15px;">Selanjutnya disebut sebagai <strong>PEMBERI KUASA</strong>.</p>
                    
                    <p style="margin-bottom: 8px;">Dengan ini memberikan kuasa penuh kepada:</p>
                    <table style="width: 100%; margin-left: 15px; margin-bottom: 15px; border: none;">
                        <tr><td style="width: 100px;">Nama</td><td>: <strong>{{nama_penerima_kuasa}}</strong></td></tr>
                        <tr><td>Jabatan</td><td>: Ketua / Pengurus Terpilih</td></tr>
                    </table>
                    <p style="margin-top: 0px; margin-bottom: 15px;">Selanjutnya disebut sebagai <strong>PENERIMA KUASA</strong>.</p>
                    
                    <p style="margin-bottom: 8px;"><strong>KHUSUS</strong></p>
                    <p style="margin-bottom: 8px;">Untuk dan atas nama Pemberi Kuasa menghadap Notaris dan Pejabat Yang Berwenang guna mengurus, menandatangani akta pendirian, serta melakukan segala tindakan hukum yang diperlukan untuk mendapatkan pengesahan badan hukum <strong>{{nama_koperasi}}</strong>.</p>
                    
                    <p style="margin-bottom: 15px;">Surat Kuasa ini diberikan dengan hak substitusi baik sebagian maupun seluruhnya.</p>
                    
                    <div style="page-break-inside: avoid; margin-top: 40px;">
                         <table style="width: 100%; border: none;">
                            <tr>
                                <td style="width: 50%; text-align: center; vertical-align: top;">
                                    <p style="margin-bottom: 0; text-align: center;">Penerima Kuasa,</p>
                                    <div class="signature-box"></div>
                                    <p style="text-align: center;"><strong>( {{nama_penerima_kuasa}} )</strong></p>
                                </td>
                                <td style="width: 50%; text-align: center; vertical-align: top;">
                                    <p style="margin-bottom: 0; text-align: center;">Pemberi Kuasa (Mewakili),</p>
                                    <div class="signature-box"></div>
                                    <p style="text-align: center;"><strong>( {{nama_pemberi_kuasa}} )</strong></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                '
            ]
        ];

        foreach ($templates as $template) {
            DocumentTemplate::updateOrCreate(
                ['name' => $template['name']],
                [
                    'type' => $template['type'],
                    'placeholders' => json_encode($template['placeholders']),
                    'content' => trim($template['content'])
                ]
            );
        }
    }
}
