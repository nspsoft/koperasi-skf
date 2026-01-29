@extends('layouts.app')

@section('title', __('messages.titles.loan_detail'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center gap-4">
            <a href="{{ route('loans.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center gap-3">
                    <h1 class="page-title">Detail Pinjaman</h1>
                    <span class="badge badge-{{ $loan->status_color }}">
                        {{ $loan->status_label }}
                    </span>
                </div>
                <p class="page-subtitle">{{ $loan->loan_number }} - {{ $loan->member->user->name }}</p>
            </div>
            
            <!-- Approval Actions (Only for Admin & Pending Status) -->
            @if($loan->status === 'pending' && auth()->user()->hasAdminAccess())
            <div class="flex gap-2">
                <button onclick="document.getElementById('rejectModal').showModal()" class="btn-secondary text-red-600 hover:bg-red-50 hover:border-red-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Tolak
                </button>
                <form action="{{ route('loans.approve', $loan) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary bg-green-600 hover:bg-green-700 border-green-600 hover:border-green-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Setujui
                    </button>
                </form>
            </div>
            @endif

            <!-- Disbursement Action (Only for Admin & Approved Status) -->
            @if($loan->status === 'approved' && auth()->user()->hasAdminAccess())
                @if($loan->signature)
                <form action="{{ route('loans.disburse', $loan) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin mencairkan dana pinjaman ini? Status akan berubah menjadi AKTIF.')">
                   @csrf
                   <button type="submit" class="btn-primary">
                       <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                       </svg>
                       Cairkan Dana
                   </button>
                </form>
                @else
                <button class="btn-secondary opacity-50 cursor-not-allowed" title="Menunggu tanda tangan anggota" disabled>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Menunggu TTD
                </button>
                @endif
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Summary Card -->
            <div class="glass-card-solid p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Ringkasan Pinjaman</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                     <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Jumlah Pinjaman</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($loan->amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Harus Dibayar</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($loan->total_amount, 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="border-t border-gray-100 dark:border-gray-700 md:col-span-2 my-2"></div>

                     <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Angsuran Per Bulan</p>
                        <p class="text-lg font-semibold text-primary-600 dark:text-primary-400">Rp {{ number_format($loan->monthly_installment, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tenor / Jangka Waktu</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $loan->duration_months }} Bulan</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                @if($loan->status === 'active' || $loan->status === 'completed')
                <div class="mt-8">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600 dark:text-gray-300">Progress Pembayaran</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $loan->payment_progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-3 rounded-full transition-all duration-1000" style="width: {{ $loan->payment_progress }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span>Terbayar: Rp {{ number_format($loan->total_amount - $loan->remaining_amount, 0, ',', '.') }}</span>
                        <span>Sisa: Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Signature Section -->
            <div class="glass-card-solid p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Akad & Tanda Tangan</h3>
                    
                    @if($loan->status === 'approved' && !$loan->signature && (auth()->user()->id == $loan->member->user_id || auth()->user()->hasAdminAccess()))
                    <button onclick="document.getElementById('signatureModal').showModal(); initSignaturePad();" 
                            class="btn-primary animate-pulse">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        Tanda Tangan Akad Digital
                    </button>
                    @endif
                </div>

                @if($loan->signature)
                <div class="bg-white p-4 rounded-xl border border-gray-200 inline-block w-full text-center">
                    <img src="{{ Storage::url($loan->signature) }}" alt="Tanda Tangan" class="h-32 mx-auto mb-2">
                    <div class="text-xs text-gray-500 border-t pt-2 w-full mt-2">
                        <p class="font-semibold text-gray-900">{{ $loan->member->user->name }}</p>
                        <p>Ditandatangani secara digital pada:</p>
                        <p class="font-mono text-gray-700">{{ $loan->signed_at->format('d M Y H:i:s') }}</p>
                    </div>
                </div>
                @else
                    @if($loan->status === 'approved')
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl text-center">
                            <p class="text-yellow-800 dark:text-yellow-300">Menunggu tanda tangan anggota sebelum dana dapat dicairkan.</p>
                        </div>
                    @else
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-center">
                            <p class="text-gray-500">Tanda tangan tersedia setelah pinjaman disetujui.</p>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Notes / Purpose -->
             <div class="glass-card-solid p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 uppercase tracking-wider">Keperluan Pinjaman</h3>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $loan->purpose }}</p>
                
                @if($loan->notes)
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 uppercase tracking-wider">Catatan Admin</h3>
                    <p class="text-gray-600 dark:text-gray-300 italic">"{{ $loan->notes }}"</p>
                </div>
                @endif
            </div>

            <!-- Payment History -->
             <div class="glass-card-solid p-6">
                 <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Pembayaran</h3>
                    @if($loan->status === 'active' && auth()->user()->hasAdminAccess())
                        <a href="{{ route('loan-payments.create', ['loan_id' => $loan->id]) }}" class="btn-sm btn-primary">
                            Input Pembayaran
                        </a>
                    @endif
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">
                            <tr>
                                <th class="px-4 py-3 rounded-l-lg">No. Bayar</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Jumlah</th>
                                <th class="px-4 py-3 rounded-r-lg">Metode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($loan->payments as $payment)
                            <tr>
                                <td class="px-4 py-3">{{ $payment->payment_number }}</td>
                                <td class="px-4 py-3">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-semibold text-green-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $payment->payment_method_label }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada riwayat pembayaran</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-6">
             <!-- Member Info -->
             <div class="glass-card-solid p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informasi Peminjam
                </h3>
                <div class="flex items-center gap-4 mb-4">
                     @if($loan->member->photo)
                    <img src="{{ Storage::url($loan->member->photo) }}" class="w-12 h-12 rounded-full object-cover">
                    @else
                     <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">
                        {{ strtoupper(substr($loan->member->user->name, 0, 1)) }}
                    </div>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $loan->member->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $loan->member->member_id }}</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Departemen</span>
                        <span class="text-gray-900 dark:text-white">{{ $loan->member->department ?? '-' }}</span>
                    </div>
                     <div class="flex justify-between">
                        <span class="text-gray-500">Jabatan</span>
                        <span class="text-gray-900 dark:text-white">{{ $loan->member->position ?? '-' }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-center">
                    <a href="{{ route('members.show', $loan->member) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Lihat Profil Lengkap</a>
                </div>
            </div>

            <!-- Timeline -->
             <div class="glass-card-solid p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Timeline</h3>
                <div class="relative pl-4 border-l-2 border-gray-200 dark:border-gray-700 space-y-6">
                    <!-- Applied -->
                    <div class="relative">
                        <div class="absolute -left-[21px] top-0 w-3 h-3 rounded-full bg-green-500 border-2 border-white dark:border-gray-800"></div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Pengajuan Dibuat</p>
                        <p class="text-xs text-gray-500">{{ $loan->application_date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500 mt-1">Oleh: {{ $loan->creator->name ?? 'System' }}</p>
                    </div>
                    
                    <!-- Approved/Rejected -->
                    @if($loan->approval_date)
                    <div class="relative">
                        <div class="absolute -left-[21px] top-0 w-3 h-3 rounded-full {{ $loan->status === 'rejected' ? 'bg-red-500' : 'bg-blue-500' }} border-2 border-white dark:border-gray-800"></div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $loan->status === 'rejected' ? 'Ditolak' : 'Disetujui' }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $loan->approval_date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500 mt-1">Oleh: {{ $loan->approver->name ?? '-' }}</p>
                    </div>
                    @endif

                    <!-- Signed -->
                    @if($loan->signed_at)
                    <div class="relative">
                        <div class="absolute -left-[21px] top-0 w-3 h-3 rounded-full bg-indigo-500 border-2 border-white dark:border-gray-800"></div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Ditandatangani</p>
                        <p class="text-xs text-gray-500">{{ $loan->signed_at->format('d M Y') }}</p>
                    </div>
                    @endif

                    <!-- Disbursed -->
                    @if($loan->disbursement_date)
                    <div class="relative">
                        <div class="absolute -left-[21px] top-0 w-3 h-3 rounded-full bg-purple-500 border-2 border-white dark:border-gray-800"></div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Dana Dicairkan</p>
                        <p class="text-xs text-gray-500">{{ $loan->disbursement_date->format('d M Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <dialog id="rejectModal" class="modal bg-transparent">
        <div class="modal-box glass-card-solid p-6 max-w-md mx-auto mt-20 relative">
            <form method="POST" action="{{ route('loans.reject', $loan) }}">
                @csrf
                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-4">Tolak Pengajuan Pinjaman</h3>
                <div class="form-group mb-4">
                    <label class="form-label">Alasan Penolakan</label>
                    <textarea name="notes" class="form-input" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                </div>
                <div class="modal-action flex justify-end gap-2">
                    <button type="button" class="btn-secondary" onclick="document.getElementById('rejectModal').close()">Batal</button>
                    <button type="submit" class="btn-primary bg-red-600 hover:bg-red-700 border-red-600">Konfirmasi Tolak</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Signature Modal -->
    <dialog id="signatureModal" class="rounded-2xl shadow-2xl p-0 overflow-hidden backdrop:bg-gray-900/50 dark:backdrop:bg-gray-900/80">
        <div class="bg-white dark:bg-gray-800 p-6 w-[90vw] max-w-lg">
            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Tanda Tangan Akad</h3>
            <p class="text-sm text-gray-500 mb-4">Silakan tanda tangan di kotak bawah ini sebagai persetujuan akad pinjaman.</p>
            
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50 touch-none relative">
                <canvas id="signatureCanvas" class="w-full h-64 block" style="touch-action: none;"></canvas>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <button type="button" id="clearSignature" class="text-sm text-red-500 hover:text-red-700 font-medium px-2 py-1">
                    Hapus
                </button>
                
                <div class="flex gap-3">
                    <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700" onclick="document.getElementById('signatureModal').close()">
                        Batal
                    </button>
                    <button type="button" id="saveSignatureBtn" class="btn-primary">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </dialog>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        let signaturePad = null;

        function initSignaturePad() {
            const canvas = document.getElementById('signatureCanvas');
            
            // Tunggu sebentar agar modal benar-benar tampil dan ukuran CSS canvas sudah ter-render
            setTimeout(() => {
                // Set atribut width/height canvas sama dengan ukuran CSS-nya (render size)
                // Ini PENTING agar koordinat mouse/touch akurat
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;

                // Re-init atau Clear
                if(!signaturePad) {
                    signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(255, 255, 255, 0)', // Transparan
                        penColor: 'rgb(0, 0, 0)',
                        velocityFilterWeight: 0.7
                    });
                } else {
                    signaturePad.clear();
                }
            }, 300); // Delay sedikit lebih lama (300ms) untuk memastikan modal transisi selesai
        }

        document.getElementById('clearSignature').addEventListener('click', () => {
            if(signaturePad) signaturePad.clear();
        });

        document.getElementById('saveSignatureBtn').addEventListener('click', async () => {
            if (!signaturePad || signaturePad.isEmpty()) {
                alert("Harap tanda tangan terlebih dahulu.");
                return;
            }

            const confirmBtn = document.getElementById('saveSignatureBtn');
            const originalText = confirmBtn.innerText;
            confirmBtn.innerText = "Menyimpan...";
            confirmBtn.disabled = true;

            const base64 = signaturePad.toDataURL('image/png');

            try {
                const response = await fetch("{{ route('loans.sign', $loan) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ signature: base64 })
                });

                const result = await response.json();

                if (response.ok) {
                    // Tutup modal dulu biar smooth
                    document.getElementById('signatureModal').close();
                    
                    window.dispatchEvent(new CustomEvent('notify', { 
                        detail: { message: 'Tanda tangan berhasil disimpan! Halaman akan dimuat ulang...', type: 'success' }
                    }));
                    
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    alert(result.message || 'Gagal menyimpan tanda tangan.');
                    confirmBtn.innerText = originalText;
                    confirmBtn.disabled = false;
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan koneksi.');
                confirmBtn.innerText = originalText;
                confirmBtn.disabled = false;
            }
        });
        
        // Handle window resize untuk responsivitas
        window.addEventListener('resize', () => {
            if(signaturePad) {
                const canvas = document.getElementById('signatureCanvas');
                const data = signaturePad.toData(); // Simpan data coretan
                canvas.width = canvas.offsetWidth;
                canvas.height = canvas.offsetHeight;
                signaturePad.clear(); 
                signaturePad.fromData(data); // Restore coretan
            }
        });
    </script>
@endsection
