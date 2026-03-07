<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>QR ID Amigo - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-900 flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <h1 class="text-lg font-bold text-slate-800">QR ID Amigo</h1>
        <p class="text-slate-500 text-sm mb-4">{{ $user->name }}</p>

        @if(!$member)
            <div class="p-6 bg-amber-50 border border-amber-200 rounded-xl text-amber-700">
                Akun Anda belum terhubung ke data anggota.
            </div>
        @elseif(!$member->id_amigo)
            <div class="p-6 bg-amber-50 border border-amber-200 rounded-xl text-amber-700">
                ID Amigo belum diisi.
            </div>
        @else
            <div class="flex items-center justify-center">
                @if($qrCode)
                    <img src="{{ $qrCode }}" alt="QR id_amigo" class="w-64 h-64">
                @else
                    <div id="qrcode" class="w-64 h-64" data-text="{{ $member->id_amigo }}" data-filename="QR-ID-Amigo-{{ $member->id_amigo }}.png"></div>
                @endif
            </div>
            <p class="mt-4 font-mono text-lg tracking-widest text-slate-700">{{ $member->id_amigo }}</p>
            <button onclick="downloadQR()" class="mt-4 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                Download PNG
            </button>
            <a href="{{ route('profile.card') }}" class="mt-3 inline-flex items-center justify-center px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg">
                Lihat Kartu Member
            </a>
        @endif

        <a href="{{ route('dashboard') }}" class="block mt-6 text-slate-500 hover:text-slate-700">← Kembali</a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        (function () {
            const el = document.getElementById('qrcode');
            if (!el) return;
            const text = el.dataset.text || '';
            if (!text) return;
            new QRCode(el, { text, width: 256, height: 256 });
        })();

        function downloadQR() {
            const node =
                document.querySelector('img[alt="QR id_amigo"]') ||
                document.querySelector('#qrcode img') ||
                document.querySelector('#qrcode canvas');
            if (!node) return;

            let dataUrl = '';
            if (node.tagName === 'IMG') {
                dataUrl = node.src;
            } else if (node.tagName === 'CANVAS') {
                dataUrl = node.toDataURL('image/png');
            } else {
                return;
            }
            const a = document.createElement('a');
            a.href = dataUrl;
            a.download = document.getElementById('qrcode')?.dataset.filename || 'QR-ID-Amigo.png';
            a.click();
        }
    </script>
</body>
</html>
