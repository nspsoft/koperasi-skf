<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\User;
use Carbon\Carbon;

// Bersihkan data polling lama
PollVote::truncate();
PollOption::truncate();
Poll::truncate();

// Buat Polling Baru
$poll = Poll::create([
    'title' => 'Pemilihan Ketua Koperasi Periode 2026-2029',
    'description' => 'Silakan gunakan hak suara Anda untuk memilih calon ketua koperasi kita. Satu anggota hanya dapat memilih satu kali dan suara bersifat rahasia (E-Voting).',
    'start_date' => Carbon::now()->subDays(2),
    'end_date' => Carbon::now()->addDays(5),
    'status' => 'active'
]);

// Buat 3 Kandidat
$candidates = [
    [
        'poll_id' => $poll->id,
        'candidate_name' => '1. Budi Santoso, S.E.',
        'vision_mission' => "Visi:\nMewujudkan koperasi yang modern, transparan, dan berdaya saing tinggi.\n\nMisi:\n1. Menerapkan digitalisasi penuh pada transaksi\n2. Meningkatkan SHU sebesar 20% setiap tahun\n3. Memperluas kerjasama supplier barang",
        'candidate_photo' => null
    ],
    [
        'poll_id' => $poll->id,
        'candidate_name' => '2. Dra. Siti Aminah',
        'vision_mission' => "Visi:\nMensejahterakan seluruh anggota melalui program simpan pinjam yang adil dan merata.\n\nMisi:\n1. Menurunkan margin pinjaman\n2. Program sembako murah bulanan\n3. Mempermudah proses pencairan dana",
        'candidate_photo' => null
    ],
    [
        'poll_id' => $poll->id,
        'candidate_name' => '3. Ahmad Wijaya, M.T.',
        'vision_mission' => "Visi:\nOptimalisasi aset koperasi untuk memberikan manfaat maksimal bagi anggota.\n\nMisi:\n1. Mengembangkan unit usaha minimarket koperasi\n2. Pelatihan kewirausahaan bagi anggota\n3. Transparansi laporan keuangan real-time",
        'candidate_photo' => null
    ]
];

foreach ($candidates as $c) {
    PollOption::create($c);
}

$options = PollOption::where('poll_id', $poll->id)->get();

// Beri simulasi voting dari para pengguna
$users = User::inRandomOrder()->limit(35)->get();

foreach ($users as $user) {
    // Pilih kandidat secara acak
    $randomOption = $options->random();
    
    PollVote::create([
        'poll_id' => $poll->id,
        'user_id' => $user->id,
        'poll_option_id' => $randomOption->id
    ]);
}

// Buat Polling yang sudah selesai (Sebagai riwayat)
$pollCompleted = Poll::create([
    'title' => 'Pemilihan Pengawas Koperasi 2024-2026',
    'description' => 'Pemungutan suara untuk jajaran pengawas internal koperasi.',
    'start_date' => Carbon::now()->subMonths(3),
    'end_date' => Carbon::now()->subMonths(3)->addDays(7),
    'status' => 'closed'
]);

$optionsCompleted = [
    ['poll_id' => $pollCompleted->id, 'candidate_name' => 'H. Rahman T., S.H.', 'vision_mission' => 'Pengawasan ketat dan independen.'],
    ['poll_id' => $pollCompleted->id, 'candidate_name' => 'Linda Kusuma, Ak.', 'vision_mission' => 'Audit berkala dan kepatuhan aturan koperasi.'],
];

foreach ($optionsCompleted as $oc) {
    PollOption::create($oc);
}

echo "\n\n>>> BERHASIL! DATA E-POLLING KANDIDAT & SUARA MASUK! <<<\n\n";
