<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen - Koperasi Spindo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        @if($isValid)
            <!-- Valid Header -->
            <div class="bg-green-600 p-6 text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">DOKUMEN VALID</h1>
                <p class="text-green-100 text-sm mt-1">Verifikasi Berhasil</p>
            </div>

            <!-- Document Details -->
            <div class="p-8 space-y-4">
                <div class="text-center mb-6">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Diterbitkan Oleh</p>
                    <p class="text-lg font-bold text-gray-800">Koperasi Karyawan Spindo</p>
                </div>

                <div class="border-t border-b border-gray-100 py-4 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Nomor Dokumen</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $document->document_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Jenis Dokumen</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $document->document_type }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Tanggal Terbit</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $document->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    @if(isset($meta['nama_anggota']))
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Nama Anggota</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $meta['nama_anggota'] }}</span>
                    </div>
                    @endif
                </div>

                <div class="text-center pt-2">
                    <p class="text-xs text-gray-400">Scan Terakhir: {{ $document->verified_at ? $document->verified_at->format('d M Y H:i:s') : 'Baru Saja' }}</p>
                </div>
            </div>
        @else
            <!-- Invalid Header -->
            <div class="bg-red-600 p-6 text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">DOKUMEN TIDAK VALID</h1>
                <p class="text-red-100 text-sm mt-1">Data Tidak Ditemukan</p>
            </div>

            <div class="p-8 text-center">
                <p class="text-gray-600">Maaf, dokumen yang Anda scan tidak terdaftar dalam sistem kami atau QR Code salah.</p>
                <div class="mt-6">
                    <p class="text-xs text-gray-400">Pastikan Anda memindai QR Code resmi dari dokumen Koperasi Spindo.</p>
                </div>
            </div>
        @endif

        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Koperasi Karyawan Spindo Karawang</p>
        </div>
    </div>

</body>
</html>
