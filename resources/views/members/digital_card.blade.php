<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kartu Digital - {{ $member->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
            min-height: 100vh;
        }

        .card {
            width: 100%;
            max-width: 320px;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            animation: cardSlideIn 0.6s ease-out;
            transform-style: preserve-3d;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px) rotateX(2deg);
            box-shadow: 0 35px 60px rgba(0,0,0,0.4);
        }

        @keyframes cardSlideIn {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .header-wave {
            background: linear-gradient(160deg, #0a1628 0%, #1a3a5c 50%, #0f2744 100%);
            min-height: 90px;
            position: relative;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 16px;
        }
        .header-wave::after {
            content: '';
            position: absolute;
            bottom: -24px;
            left: 0;
            right: 0;
            height: 50px;
            background: #ffffff;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
            border-top: 3px solid #c9a227;
        }

        .footer-wave {
            background: linear-gradient(160deg, #0a1628 0%, #1a3a5c 50%, #0f2744 100%);
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top: 3px solid #c9a227;
        }

        .photo-frame {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #c9a227;
            overflow: hidden;
            background: #f1f5f9;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            animation: photoPopIn 0.5s ease-out 0.3s both;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .photo-frame:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 35px rgba(201, 162, 39, 0.4);
        }

        @keyframes photoPopIn {
            from {
                opacity: 0;
                transform: scale(0.5);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .info-row {
            animation: fadeInRight 0.4s ease-out both;
        }
        .info-row:nth-child(1) { animation-delay: 0.4s; }
        .info-row:nth-child(2) { animation-delay: 0.5s; }
        .info-row:nth-child(3) { animation-delay: 0.6s; }
        .info-row:nth-child(4) { animation-delay: 0.7s; }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .qr-container {
            animation: qrPulse 2s ease-in-out infinite;
        }

        @keyframes qrPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(15, 39, 68, 0.3); }
            50% { box-shadow: 0 0 0 10px rgba(15, 39, 68, 0); }
        }

        .shine-effect {
            position: relative;
            overflow: hidden;
        }
        .shine-effect::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            50%, 100% { left: 100%; }
        }

        .logo-box {
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .btn-download {
            transition: all 0.3s ease;
        }
        .btn-download:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(201, 162, 39, 0.4);
        }
        .btn-download:active {
            transform: translateY(0);
        }

        .status-badge {
            animation: statusPop 0.5s ease-out 0.8s both;
        }

        @keyframes statusPop {
            0% { transform: scale(0); }
            70% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .member-id {
            animation: idReveal 0.6s ease-out 0.9s both;
        }

        @keyframes idReveal {
            from {
                opacity: 0;
                letter-spacing: 0.5em;
            }
            to {
                opacity: 1;
                letter-spacing: 0.15em;
            }
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center p-4">

    @php
        $logo = \App\Models\Setting::get('coop_logo');
        $coopName = \App\Models\Setting::get('coop_name', 'KOPERASI KARYAWAN SKF');
    @endphp

    <!-- Card -->
    <div class="card">
        <!-- Header -->
        <div class="header-wave shine-effect">
            <div class="logo-box bg-white rounded-xl px-4 py-2 shadow-lg text-center relative z-10">
                <img src="{{ asset('images/spindo-logo.png') }}" alt="SPINDO" class="h-8 mx-auto">
                <p class="text-[6px] text-slate-500 mt-1">PT. STEEL PIPE INDUSTRY OF INDONESIA, Tbk.</p>
            </div>
        </div>

        <!-- Content -->
        <div class="px-6 pb-4">
            <!-- Photo -->
            <div class="flex justify-center mt-4 relative z-10">
                <div class="photo-frame">
                    @if($member->photo)
                        <img src="{{ Storage::url($member->photo) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-200 to-slate-300">
                            <svg class="w-12 h-12 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Name & Position -->
            <div class="text-center mt-4">
                <h2 class="text-lg font-bold text-[#0f2744]">{{ strtoupper($member->user->name) }}</h2>
                <p class="text-sm text-[#c9a227] font-semibold">{{ $member->position ?? 'Staff' }}</p>
            </div>

            <!-- Info -->
            <div class="mt-4 space-y-1.5 text-sm">
                <div class="info-row flex">
                    <span class="w-28 font-semibold text-[#0f2744]">NIK</span>
                    <span class="text-slate-600">: {{ $member->employee_id ?? $member->nik ?? '-' }}</span>
                </div>
                <div class="info-row flex">
                    <span class="w-28 font-semibold text-[#0f2744]">Departemen</span>
                    <span class="text-slate-600">: {{ $member->department ?? '-' }}</span>
                </div>
                <div class="info-row flex">
                    <span class="w-28 font-semibold text-[#0f2744]">Contact</span>
                    <span class="text-slate-600">: {{ $member->user->phone ?? '-' }}</span>
                </div>
                <div class="info-row flex items-center">
                    <span class="w-28 font-semibold text-[#0f2744]">Status</span>
                    <span class="status-badge {{ $member->status == 'active' ? 'bg-green-500' : 'bg-red-500' }} text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $member->status == 'active' ? 'AKTIF' : 'NON-AKTIF' }}</span>
                </div>
            </div>

            <!-- QR Code -->
            <div class="mt-5 text-center">
                <p class="text-[10px] text-slate-400 mb-2 uppercase tracking-wider">Scan untuk transaksi</p>
                <div class="qr-container inline-block bg-white p-2 rounded-xl border-2 border-[#0f2744]">
                    @if(isset($qrCode))
                        <img src="{{ $qrCode }}" alt="QR" class="w-28 h-28">
                    @else
                        <div id="qrcode"></div>
                    @endif
                </div>
                <p class="member-id text-lg font-mono font-bold text-[#0f2744] mt-2">{{ $member->member_id }}</p>
                @if(isset($generatedDocument))
                    <p class="text-xs text-slate-400">{{ $generatedDocument->document_number }}</p>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-wave">
            <p class="text-xs text-white font-medium tracking-widest">KARTU ANGGOTA KOPERASI</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex gap-4">
        <a href="{{ url()->previous() }}" class="px-5 py-2.5 text-white/70 hover:text-white transition">
            ← Kembali
        </a>
        <button onclick="downloadCard()" class="btn-download px-6 py-2.5 bg-[#c9a227] hover:bg-[#b8922a] text-white rounded-full font-semibold shadow-lg flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download
        </button>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        @if(!isset($qrCode))
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $member->member_id }}",
            width: 112,
            height: 112,
            colorDark: "#0f2744",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        @endif

        function downloadCard() {
            const card = document.querySelector('.card');
            const btn = document.querySelector('.btn-download');
            btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing...';
            
            html2canvas(card, {
                scale: 3,
                backgroundColor: null,
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'KartuAnggota-{{ $member->member_id }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Downloaded!';
                setTimeout(() => {
                    btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Download';
                }, 2000);
            });
        }
    </script>
</body>
</html>
