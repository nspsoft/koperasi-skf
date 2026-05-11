@extends('layouts.app')

@section('title', 'Absensi Kasir')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Absensi Kasir</h1>
        <p class="text-gray-500 text-sm">Silakan lakukan absensi masuk dan pulang di sini.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Clock In Card -->
        <div class="glass-card-solid p-6 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-green-50 dark:bg-green-900/30 text-green-600 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            </div>
            <h2 class="font-bold text-lg text-gray-900 dark:text-white mb-1">Absen Masuk</h2>
            <p class="text-sm text-gray-500 mb-4">Pastikan Anda berada di area toko.</p>
            
            @if($todayAttendance && $todayAttendance->clock_in)
                <div class="text-green-600 font-semibold text-sm">
                    Sudah Masuk: {{ $todayAttendance->clock_in->format('H:i') }}
                </div>
            @else
                <form action="{{ route('attendance.clock-in') }}" method="POST" id="form-clock-in">
                    @csrf
                    <input type="hidden" name="latitude" id="lat-in">
                    <input type="hidden" name="longitude" id="long-in">
                    <div class="mb-3">
                        <input type="text" name="notes" placeholder="Catatan (opsional)" class="form-input text-sm">
                    </div>
                    <button type="button" onclick="getLocation('in')" class="btn-primary w-full">Absen Masuk</button>
                </form>
            @endif
        </div>

        <!-- Clock Out Card -->
        <div class="glass-card-solid p-6 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-red-50 dark:bg-red-900/30 text-red-600 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            </div>
            <h2 class="font-bold text-lg text-gray-900 dark:text-white mb-1">Absen Pulang</h2>
            <p class="text-sm text-gray-500 mb-4">Lakukan saat shift Anda selesai.</p>
            
            @if($todayAttendance && $todayAttendance->clock_out)
                <div class="text-red-600 font-semibold text-sm">
                    Sudah Pulang: {{ $todayAttendance->clock_out->format('H:i') }}
                </div>
            @elseif($todayAttendance && $todayAttendance->clock_in)
                <form action="{{ route('attendance.clock-out') }}" method="POST" id="form-clock-out">
                    @csrf
                    <input type="hidden" name="latitude" id="lat-out">
                    <input type="hidden" name="longitude" id="long-out">
                    <button type="button" onclick="getLocation('out')" class="btn-danger w-full">Absen Pulang</button>
                </form>
            @else
                <button class="btn-secondary w-full cursor-not-allowed" disabled>Belum Masuk</button>
            @endif
        </div>

        <!-- Info Card -->
        <div class="glass-card-solid p-6">
            <h2 class="font-bold text-lg text-gray-900 dark:text-white mb-3">Info Lokasi Anda</h2>
            <div id="location-info" class="text-sm text-gray-500 space-y-2">
                <p>Latitude: <span id="display-lat">-</span></p>
                <p>Longitude: <span id="display-long">-</span></p>
                <p id="geo-status" class="text-orange-500">Mencari lokasi...</p>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="glass-card-solid p-6">
        <h2 class="font-bold text-lg text-gray-900 dark:text-white mb-4">Riwayat Absensi</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Masuk</th>
                        <th class="px-4 py-3">Pulang</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $item->date->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $item->clock_in ? $item->clock_in->format('H:i') : '-' }}</td>
                            <td class="px-4 py-3">{{ $item->clock_out ? $item->clock_out->format('H:i') : '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $item->status === 'present' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $item->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center">Belum ada riwayat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $history->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        function getLocation(type) {
            const statusText = document.getElementById('geo-status');
            statusText.textContent = 'Mengambil lokasi...';
            statusText.className = 'text-orange-500';

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const long = position.coords.longitude;

                        document.getElementById('display-lat').textContent = lat;
                        document.getElementById('display-long').textContent = long;
                        statusText.textContent = 'Lokasi ditemukan!';
                        statusText.className = 'text-green-500';

                        if (type === 'in') {
                            document.getElementById('lat-in').value = lat;
                            document.getElementById('long-in').value = long;
                            document.getElementById('form-clock-in').submit();
                        } else if (type === 'out') {
                            document.getElementById('lat-out').value = lat;
                            document.getElementById('long-out').value = long;
                            document.getElementById('form-clock-out').submit();
                        }
                    },
                    (error) => {
                        statusText.textContent = 'Gagal mengambil lokasi: ' + error.message;
                        statusText.className = 'text-red-500';
                        alert('Gagal mengambil lokasi. Pastikan izin lokasi diaktifkan.');
                    }
                );
            } else {
                statusText.textContent = 'Geolocation tidak didukung oleh browser ini.';
                statusText.className = 'text-red-500';
                alert('Browser Anda tidak mendukung Geolocation.');
            }
        }

        // Auto get location on load to show info
        document.addEventListener('DOMContentLoaded', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    document.getElementById('display-lat').textContent = position.coords.latitude;
                    document.getElementById('display-long').textContent = position.coords.longitude;
                    const statusText = document.getElementById('geo-status');
                    statusText.textContent = 'Lokasi siap.';
                    statusText.className = 'text-green-500';
                });
            }
        });
    </script>
    @endpush
@endsection
