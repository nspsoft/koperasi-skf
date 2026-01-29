<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Surat Keterangan Anggota',
                'type' => 'membership',
                'placeholders' => ['nama_anggota', 'no_anggota', 'nik', 'jabatan', 'tanggal'],
                'content' => '
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h2 style="text-decoration: underline; margin-bottom: 5px;">SURAT KETERANGAN ANGGOTA</h2>
                        <p style="margin-top: 0;">Nomor: .../SK-KOP/{{month}}/{{year}}</p>
                    </div>
                    <p>Yang bertanda tangan di bawah ini, Pengurus Koperasi Karyawan Spindo Karawang Factory, dengan ini menerangkan bahwa:</p>
                    <table style="width: 100%; margin-left: 20px; margin-bottom: 20px;">
                        <tr><td style="width: 150px;">Nama</td><td>: <strong>{{nama_anggota}}</strong></td></tr>
                        <tr><td>No. Anggota</td><td>: {{no_anggota}}</td></tr>
                        <tr><td>NIK</td><td>: {{nik}}</td></tr>
                        <tr><td>Jabatan</td><td>: {{jabatan}}</td></tr>
                    </table>
                    <p>Adalah benar terdaftar sebagai Anggota Koperasi Karyawan Spindo Karawang Factory terhitung sejak tanggal {{tanggal}} sampai dengan surat keterangan ini diterbitkan.</p>
                    <p>Demikian surat keterangan ini diberikan untuk dapat dipergunakan sebagaimana mestinya.</p>
                    <div style="margin-top: 50px; float: right; width: 250px; text-align: center;">
                        <p>Karawang, {{today}}</p>
                        <p>Pengurus Koperasi,</p>
                        <div style="height: 80px;"></div>
                        <p><strong>( ____________________ )</strong></p>
                    </div>
                '
            ],
            [
                'name' => 'Surat Permohonan Pinjaman',
                'type' => 'loan',
                'placeholders' => ['nama_anggota', 'no_anggota', 'nik', 'jumlah_pinjaman', 'keperluan', 'tenor'],
                'content' => '
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h2 style="text-decoration: underline; margin-bottom: 5px;">SURAT PERMOHONAN PINJAMAN</h2>
                    </div>
                    <p>Kepada Yth,<br>Pengurus Koperasi Karyawan Spindo Karawang Factory<br>Di Tempat</p>
                    <p>Dengan hormat,<br>Saya yang bertanda tangan di bawah ini:</p>
                    <table style="width: 100%; margin-left: 20px; margin-bottom: 20px;">
                        <tr><td style="width: 150px;">Nama</td><td>: <strong>{{nama_anggota}}</strong></td></tr>
                        <tr><td>No. Anggota</td><td>: {{no_anggota}}</td></tr>
                        <tr><td>NIK</td><td>: {{nik}}</td></tr>
                    </table>
                    <p>Melalui surat ini bermaksud mengajukan permohonan pinjaman kepada Koperasi sebesar <strong>Rp {{jumlah_pinjaman}}</strong> untuk keperluan <strong>{{keperluan}}</strong>.</p>
                    <p>Pinjaman tersebut rencananya akan saya bayar dengan cara potong gaji selama <strong>{{tenor}} bulan</strong>.</p>
                    <p>Demikian permohonan ini saya sampaikan, atas perhatian dan persetujuannya saya ucapkan terima kasih.</p>
                    <div style="margin-top: 50px; float: right; width: 200px; text-align: center;">
                        <p>Karawang, {{today}}</p>
                        <p>Hormat Saya,</p>
                        <div style="height: 80px;"></div>
                        <p><strong>( {{nama_anggota}} )</strong></p>
                    </div>
                '
            ],
            [
                'name' => 'Surat Perjanjian Pinjaman',
                'type' => 'loan',
                'placeholders' => ['nama_anggota', 'no_anggota', 'jumlah_pinjaman', 'tenor', 'angsuran'],
                'content' => '
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h2 style="text-decoration: underline; margin-bottom: 5px;">SURAT PERJANJIAN PINJAMAN</h2>
                        <p style="margin-top: 0;">No: .../SPP/{{month}}/{{year}}</p>
                    </div>
                    <p>Pada hari ini {{day_name}}, tanggal {{today_full}}, kami yang bertanda tangan di bawah ini menyatakan setuju melakukan perjanjian pinjaman dengan ketentuan sebagai berikut:</p>
                    <p><strong>PIHAK PERTAMA:</strong> Pengurus Koperasi Spindo Karawang Factory<br><strong>PIHAK KEDUA:</strong> {{nama_anggota}} (No. Anggota: {{no_anggota}})</p>
                    <p>1. PIHAK PERTAMA telah memberikan pinjaman sebesar Rp {{jumlah_pinjaman}} kepada PIHAK KEDUA.<br>2. PIHAK KEDUA akan mencicil pinjaman tersebut selama {{tenor}} bulan dengan angsuran per bulan sebesar Rp {{angsuran}}.<br>3. Pemotongan angsuran dilakukan langsung melalui pemotongan gaji setiap bulan.</p>
                    <p>Demikian perjanjian ini dibuat dengan sebenar-benarnya tanpa ada paksaan dari pihak manapun.</p>
                    <div style="display: flex; justify-content: space-between; margin-top: 50px;">
                        <div style="text-align: center; width: 200px;">
                            <p>PIHAK KEDUA,</p>
                            <div style="height: 80px;"></div>
                            <p><strong>( {{nama_anggota}} )</strong></p>
                        </div>
                        <div style="text-align: center; width: 200px;">
                            <p>PIHAK PERTAMA,</p>
                            <div style="height: 80px;"></div>
                            <p><strong>( ____________________ )</strong></p>
                        </div>
                    </div>
                '
            ],
            [
                'name' => 'Surat Undangan',
                'type' => 'official',
                'placeholders' => ['perihal', 'hari_tanggal', 'waktu', 'tempat', 'agenda'],
                'content' => '
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h2 style="text-decoration: underline; margin-bottom: 5px;">SURAT UNDANGAN</h2>
                        <p style="margin-top: 0;">Nomor: .../UND/{{month}}/{{year}}</p>
                    </div>
                    <p>Kepada Yth,<br>Bapak/Ibu Anggota Koperasi<br>Di Tempat</p>
                    <p>Perihal: {{perihal}}</p>
                    <p>Dengan hormat, mengharap kehadiran Bapak/Ibu pada acara yang akan diselenggarakan pada:</p>
                    <table style="width: 100%; margin-left: 20px; margin-bottom: 20px;">
                        <tr><td style="width: 150px;">Hari / Tanggal</td><td>: {{hari_tanggal}}</td></tr>
                        <tr><td>Waktu</td><td>: {{waktu}}</td></tr>
                        <tr><td>Tempat</td><td>: {{tempat}}</td></tr>
                        <tr><td>Agenda</td><td>: {{agenda}}</td></tr>
                    </table>
                    <p>Mengingat pentingnya acara tersebut, kami sangat mengharapkan kehadiran Bapak/Ibu tepat pada waktunya.</p>
                    <p>Demikian undangan ini kami sampaikan, atas perhatian dan kehadirannya kami ucapkan terima kasih.</p>
                    <div style="margin-top: 50px; float: right; width: 250px; text-align: center;">
                        <p>Karawang, {{today}}</p>
                        <p>Ketua Koperasi,</p>
                        <div style="height: 80px;"></div>
                        <p><strong>( ____________________ )</strong></p>
                    </div>
                '
            ],
            [
                'name' => 'Surat Penunjukan Pengurus',
                'type' => 'official',
                'placeholders' => ['nama_user', 'nik', 'posisi', 'jabatan_pengurus', 'masa_jabatan'],
                'content' => '
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h2 style="text-decoration: underline; margin-bottom: 5px;">SURAT PENUNJUKAN</h2>
                        <p style="margin-top: 0;">Nomor: .../SK-PGR/{{month}}/{{year}}</p>
                    </div>
                    <p>Berdasarkan hasil Keputusan Rapat Anggota, dengan ini memberikan penunjukan kepada:</p>
                    <table style="width: 100%; margin-left: 20px; margin-bottom: 20px;">
                        <tr><td style="width: 150px;">Nama</td><td>: <strong>{{nama_user}}</strong></td></tr>
                        <tr><td>NIK</td><td>: {{nik}}</td></tr>
                        <tr><td>Posisi / Jabatan</td><td>: {{posisi}}</td></tr>
                    </table>
                    <p>Untuk menjabat sebagai <strong>{{jabatan_pengurus}}</strong> Koperasi Karyawan Spindo Karawang Factory untuk masa jabatan {{masa_jabatan}}.</p>
                    <p>Demikian surat penunjukan ini dibuat agar dilaksanakan dengan penuh tanggung jawab.</p>
                    <div style="margin-top: 50px; float: right; width: 250px; text-align: center;">
                        <p>Karawang, {{today}}</p>
                        <p>Pengawas Koperasi,</p>
                        <div style="height: 80px;"></div>
                        <p><strong>( ____________________ )</strong></p>
                    </div>
                '
            ],
            [
                'name' => 'Surat Pernyataan',
                'type' => 'official',
                'placeholders' => ['nama_anggota', 'nik', 'isi_pernyataan'],
                'content' => '
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h2 style="text-decoration: underline; margin-bottom: 5px;">SURAT PERNYATAAN</h2>
                    </div>
                    <p>Saya yang bertanda tangan di bawah ini:</p>
                    <table style="width: 100%; margin-left: 20px; margin-bottom: 20px;">
                        <tr><td style="width: 150px;">Nama</td><td>: <strong>{{nama_anggota}}</strong></td></tr>
                        <tr><td>NIK</td><td>: {{nik}}</td></tr>
                    </table>
                    <p>Dengan ini menyatakan bahwa:</p>
                    <div style="padding: 10px; border: 1px solid #ddd; min-height: 100px;">
                        {{isi_pernyataan}}
                    </div>
                    <p>Demikian pernyataan ini saya buat dengan sebenarnya tanpa ada paksaan dari pihak manapun.</p>
                    <div style="margin-top: 50px; float: right; width: 200px; text-align: center;">
                        <p>Karawang, {{today}}</p>
                        <p>Yang Membuat Pernyataan,</p>
                        <div style="height: 40px;">(Materai 10.000)</div>
                        <div style="height: 40px;"></div>
                        <p><strong>( {{nama_anggota}} )</strong></p>
                    </div>
                '
            ],
            [
                'name' => 'Surat Pemberitahuan',
                'type' => 'official',
                'placeholders' => ['perihal', 'isi_pemberitahuan'],
                'content' => '
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h2 style="text-decoration: underline; margin-bottom: 5px;">SURAT PEMBERITAHUAN</h2>
                        <p style="margin-top: 0;">Nomor: .../PBT/{{month}}/{{year}}</p>
                    </div>
                    <p>Perihal: {{perihal}}</p>
                    <p>Kepada Yth,<br>Seluruh Anggota Koperasi<br>Di Tempat</p>
                    <p>Dengan hormat, bersama surat ini kami beritahukan hal sebagai berikut:</p>
                    <div style="padding: 10px; min-height: 150px;">
                        {{isi_pemberitahuan}}
                    </div>
                    <p>Demikian pemberitahuan ini kami sampaikan untuk menjadi perhatian bagi seluruh pihak terkait.</p>
                    <div style="margin-top: 50px; float: right; width: 250px; text-align: center;">
                        <p>Karawang, {{today}}</p>
                        <p>Ketua Koperasi,</p>
                        <div style="height: 80px;"></div>
                        <p><strong>( ____________________ )</strong></p>
                    </div>
                '
            ],
            [
                'name' => 'Surat Pengunduran Diri',
                'type' => 'membership',
                'placeholders' => ['nama_anggota', 'no_anggota', 'alasan'],
                'content' => '
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h2 style="text-decoration: underline; margin-bottom: 5px;">SURAT PENGUNDURAN DIRI</h2>
                    </div>
                    <p>Kepada Yth,<br>Pengurus Koperasi Karyawan Spindo Karawang Factory<br>Di Tempat</p>
                    <p>Saya yang bertanda tangan di bawah ini:</p>
                    <table style="width: 100%; margin-left: 20px; margin-bottom: 20px;">
                        <tr><td style="width: 150px;">Nama</td><td>: <strong>{{nama_anggota}}</strong></td></tr>
                        <tr><td>No. Anggota</td><td>: {{no_anggota}}</td></tr>
                    </table>
                    <p>Dengan ini mengajukan permohonan pengunduran diri sebagai Anggota Koperasi Karyawan Spindo Karawang Factory dikarenakan <strong>{{alasan}}</strong>.</p>
                    <p>Segala kewajiban dan hak saya pasca pengunduran diri ini saya serahkan sepenuhnya sesuai dengan aturan AD/ART Koperasi yang berlaku.</p>
                    <p>Demikian surat permohonan ini saya sampaikan, atas kerja samanya selama ini saya ucapkan terima kasih.</p>
                    <div style="margin-top: 50px; float: right; width: 200px; text-align: center;">
                        <p>Karawang, {{today}}</p>
                        <p>Hormat Saya,</p>
                        <div style="height: 80px;"></div>
                        <p><strong>( {{nama_anggota}} )</strong></p>
                    </div>
                '
            ]
        ];

        foreach ($templates as $template) {
            \App\Models\DocumentTemplate::create([
                'name' => $template['name'],
                'type' => $template['type'],
                'placeholders' => json_encode($template['placeholders']),
                'content' => trim($template['content'])
            ]);
        }
    }
}
