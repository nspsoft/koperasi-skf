@extends('layouts.app')

@section('title', 'Scan Barcode / QR Code')

@section('content')
<div class="max-w-md mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 text-center">Scan Pesanan</h2>
            <p class="text-gray-500 text-center mb-6">Scan QR Code pada struk untuk update status</p>

            <!-- Status Messages -->
            <div id="status-message" class="mb-4 p-3 rounded-lg text-sm hidden"></div>

            <!-- Camera Selection -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Kamera:</label>
                <div class="flex gap-2">
                    <select id="camera-select" class="form-input flex-1" disabled>
                        <option>Memuat kamera...</option>
                    </select>
                    <button id="switch-camera" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-200" onclick="switchCamera()" title="Tukar Kamera">
                        🔄
                    </button>
                </div>
            </div>

            <!-- Start/Stop Button -->
            <div class="flex gap-2 mb-4">
                <button id="start-camera" class="btn-primary flex-1 justify-center py-3" onclick="startScanner()">
                    📷 Mulai Scan
                </button>
                <button id="stop-camera" class="btn-secondary flex-1 justify-center py-3 hidden" onclick="stopScanner()">
                    ⏹️ Stop
                </button>
            </div>

            <!-- Scanner Viewport -->
            <div id="reader" class="w-full bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden mb-4 border-2 border-dashed border-gray-200 dark:border-gray-600" style="min-height: 280px; display: flex; align-items: center; justify-content: center;">
                <p class="text-gray-400 text-center p-4">Klik "Mulai Scan" untuk mengaktifkan kamera</p>
            </div>

            <!-- Manual Input -->
            <form action="{{ route('pos.scan.process') }}" method="POST" enctype="multipart/form-data" id="scan-form">
                @csrf
                
                <!-- QR Image Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                        <span class="text-lg">📂</span> Upload Gambar QR
                    </label>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl">
                        <input type="file" name="qr_image" id="qr_image" accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                        <p class="text-[10px] text-indigo-400 mt-2 font-medium">Pilih gambar QR Code dari galeri jika kamera bermasalah</p>
                    </div>
                </div>

                <div class="relative flex items-center my-6">
                    <div class="flex-grow border-t border-gray-200 dark:border-gray-600"></div>
                    <span class="flex-shrink mx-4 text-gray-400 text-xs font-bold">ATAU</span>
                    <div class="flex-grow border-t border-gray-200 dark:border-gray-600"></div>
                </div>

                <div class="mb-6">
                    <label for="invoice" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                        <span class="text-lg">⌨️</span> Input Manual
                    </label>
                    <input type="text" name="invoice" id="invoice" 
                           class="form-input w-full py-3" 
                           placeholder="TRX-20260116-XXXX atau INV-...">
                </div>
                <button type="submit" class="btn-primary w-full justify-center py-3 text-lg">
                    🔍 Cari Pesanan
                </button>
            </form>
        </div>
    </div>

    <!-- Debug Info (for troubleshooting) -->
    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-[10px] text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-600">
        <p class="font-bold mb-1 uppercase tracking-wider">Debug Stats:</p>
        <div class="grid grid-cols-2 gap-2">
            <p>Protocol: <span id="debug-protocol" class="font-mono"></span></p>
            <p>Cameras: <span id="debug-cameras" class="font-mono">Checking...</span></p>
        </div>
        <p class="mt-1 opacity-50">UA: <span id="debug-ua"></span></p>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" 
        integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg==" 
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    // Debug info
    document.getElementById('debug-protocol').textContent = location.protocol;
    document.getElementById('debug-ua').textContent = navigator.userAgent;

    let html5QrCode = null;
    let isScanning = false;
    let cameraDevices = [];

    // Show status message
    function showStatus(message, type = 'info') {
        const el = document.getElementById('status-message');
        el.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'bg-green-50', 'text-green-700', 'bg-yellow-50', 'text-yellow-700', 'bg-blue-50', 'text-blue-700');
        
        if (type === 'error') {
            el.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
        } else if (type === 'success') {
            el.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
        } else if (type === 'warning') {
            el.classList.add('bg-yellow-50', 'text-yellow-700', 'border', 'border-yellow-200');
        } else {
            el.classList.add('bg-blue-50', 'text-blue-700', 'border', 'border-blue-200');
        }
        
        el.innerHTML = message;
    }

    // Get available cameras
    async function loadCameras() {
        const select = document.getElementById('camera-select');
        try {
            // Recommendation: First request permission to get full device labels
            // This is crucial for tablets where labels might be empty before permission
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            stream.getTracks().forEach(track => track.stop()); // Close immediately after getting permission

            cameraDevices = await Html5Qrcode.getCameras();
            select.innerHTML = '';
            
            if (cameraDevices && cameraDevices.length > 0) {
                cameraDevices.forEach((device, index) => {
                    const option = document.createElement('option');
                    option.value = device.id;
                    
                    // Priority naming for better UX
                    let label = device.label || `Kamera ${index + 1}`;
                    if (label.toLowerCase().includes('back') || label.toLowerCase().includes('rear') || label.toLowerCase().includes('environment')) {
                        label = "📷 Kamera Belakang (" + label + ")";
                    } else if (label.toLowerCase().includes('front') || label.toLowerCase().includes('user')) {
                        label = "👤 Kamera Depan (" + label + ")";
                    }
                    
                    option.text = label;
                    select.appendChild(option);
                });
                
                // Smart auto-select: prioritize back camera
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].text.includes('Belakang')) {
                        select.selectedIndex = i;
                        break;
                    }
                }

                select.disabled = false;
                document.getElementById('debug-cameras').textContent = cameraDevices.length + ' ditemukan';
                showStatus('✅ ' + cameraDevices.length + ' kamera ditemukan. Klik "Mulai Scan" untuk memulai.', 'success');
            } else {
                select.innerHTML = '<option>Tidak ada kamera</option>';
                showStatus('⚠️ Tidak ada kamera ditemukan. Gunakan upload gambar.', 'warning');
            }
        } catch (err) {
            console.error('Error getting cameras:', err);
            showStatus('❌ Gagal mengakses kamera: ' + err.message + '<br><small>Pastikan Anda mengizinkan akses kamera.</small>', 'error');
        }
    }

    // Switch camera cycles through available devices
    function switchCamera() {
        if (isScanning) {
            showStatus('Silakan Stop scan terlebih dahulu untuk ganti kamera.', 'warning');
            return;
        }
        const select = document.getElementById('camera-select');
        if (select.options.length > 1) {
            select.selectedIndex = (select.selectedIndex + 1) % select.options.length;
            showStatus('Kamera diganti ke: ' + select.options[select.selectedIndex].text, 'info');
        } else {
            showStatus('Hanya ada 1 kamera yang ditemukan.', 'info');
        }
    }

    // Start scanner
    async function startScanner() {
        const cameraId = document.getElementById('camera-select').value;
        if (!cameraId || cameraId.includes('Error') || cameraId.includes('Tidak ada')) {
            showStatus('⚠️ Pilih kamera yang valid.', 'warning');
            return;
        }

        try {
            html5QrCode = new Html5Qrcode("reader");
            document.getElementById('reader').innerHTML = ''; 
            
            await html5QrCode.start(
                cameraId,
                { 
                    fps: 15, 
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                (decodedText) => {
                    // Success
                    showStatus('✅ QR Terbaca: ' + decodedText, 'success');
                    document.getElementById('invoice').value = decodedText;
                    stopScanner();
                    
                    // Small delay for feedback before redirect
                    setTimeout(() => {
                        document.getElementById('scan-form').submit();
                    }, 500);
                },
                (errorMessage) => { }
            );
            
            isScanning = true;
            document.getElementById('start-camera').classList.add('hidden');
            document.getElementById('stop-camera').classList.remove('hidden');
            document.getElementById('camera-select').disabled = true;
            document.getElementById('switch-camera').disabled = true;
            showStatus('📷 Kamera aktif. Arahkan ke QR Code di struk...', 'info');
            
        } catch (err) {
            console.error('Start scanner error:', err);
            showStatus('❌ Gagal: ' + err.message, 'error');
        }
    }

    // Stop scanner
    async function stopScanner() {
        if (html5QrCode && isScanning) {
            try {
                await html5QrCode.stop();
            } catch (e) { }
            isScanning = false;
        }
        document.getElementById('start-camera').classList.remove('hidden');
        document.getElementById('stop-camera').classList.add('hidden');
        document.getElementById('camera-select').disabled = false;
        document.getElementById('switch-camera').disabled = false;
        document.getElementById('reader').innerHTML = '<p class="text-gray-400 text-center p-4">Klik "Mulai Scan" untuk mengaktifkan kamera</p>';
    }

    // Handle file upload for QR scanning
    document.getElementById('qr_image').addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (file) {
            showStatus('⏳ Membaca QR dari gambar...', 'info');
            try {
                const tempScanner = new Html5Qrcode("reader");
                const decodedText = await tempScanner.scanFile(file, true);
                showStatus('✅ QR Terbaca dari file: ' + decodedText, 'success');
                document.getElementById('invoice').value = decodedText;
                setTimeout(() => {
                    document.getElementById('scan-form').submit();
                }, 500);
            } catch (err) {
                showStatus('❌ Gambar tidak jelas atau QR tidak ditemukan.', 'error');
            }
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', loadCameras);
</script>
@endsection
