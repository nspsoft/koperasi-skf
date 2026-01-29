{{-- PWA Install Prompt Banner Component --}}
<div x-data="pwaInstall()" 
     x-show="showInstallBanner" 
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="transform translate-y-full opacity-0"
     x-transition:enter-end="transform translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="transform translate-y-0 opacity-100"
     x-transition:leave-end="transform translate-y-full opacity-0"
     class="fixed bottom-0 left-0 right-0 z-50 p-4 sm:p-6"
     style="display: none;">
    
    <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Header with gradient --}}
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-3 flex items-center gap-3">
            <div class="w-12 h-12 bg-white rounded-xl p-1.5 shadow-lg flex-shrink-0">
                <img src="/icons/icon-192x192.png" alt="App Icon" class="w-full h-full object-contain">
            </div>
            <div class="flex-1 text-white">
                <h4 class="font-bold text-base">📱 Install Aplikasi</h4>
                <p class="text-xs text-emerald-100">Akses lebih cepat tanpa browser</p>
            </div>
            <button @click="dismissBanner()" class="p-1.5 hover:bg-white/20 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        {{-- Content --}}
        <div class="p-4">
            <div class="flex gap-3 mb-4">
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Offline Access</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Faster Load</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Push Notif</span>
                </div>
            </div>
            
            <div class="flex gap-2">
                <button @click="installApp()" 
                        class="flex-1 py-3 px-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Install Sekarang
                </button>
                <button @click="dismissBanner()" 
                        class="py-3 px-4 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl font-medium text-sm transition-colors">
                    Nanti
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Update Available Banner --}}
<div x-data="{ showUpdate: false }" 
     @sw-update.window="showUpdate = true"
     x-show="showUpdate"
     x-transition
     class="fixed top-4 left-4 right-4 z-50 sm:left-auto sm:right-4 sm:max-w-sm"
     style="display: none;">
    <div class="bg-blue-600 text-white rounded-xl shadow-2xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </div>
        <div class="flex-1">
            <p class="font-bold text-sm">Update Tersedia!</p>
            <p class="text-xs text-blue-100">Versi baru aplikasi sudah siap</p>
        </div>
        <button @click="window.location.reload()" class="px-4 py-2 bg-white text-blue-600 rounded-lg font-bold text-sm hover:bg-blue-50 transition-colors">
            Refresh
        </button>
    </div>
</div>

<script>
function pwaInstall() {
    return {
        deferredPrompt: null,
        showInstallBanner: false,
        
        init() {
            // Check if already installed or dismissed recently
            if (this.isInstalled() || this.isDismissedRecently()) {
                return;
            }
            
            // Listen for beforeinstallprompt
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                
                // Show banner after 3 seconds
                setTimeout(() => {
                    this.showInstallBanner = true;
                }, 3000);
            });
            
            // Listen for successful install
            window.addEventListener('appinstalled', () => {
                this.showInstallBanner = false;
                this.deferredPrompt = null;
                localStorage.setItem('pwa-installed', 'true');
            });
        },
        
        isInstalled() {
            return window.matchMedia('(display-mode: standalone)').matches || 
                   window.navigator.standalone === true ||
                   localStorage.getItem('pwa-installed') === 'true';
        },
        
        isDismissedRecently() {
            const dismissed = localStorage.getItem('pwa-dismissed');
            if (!dismissed) return false;
            
            const dismissedDate = new Date(dismissed);
            const now = new Date();
            const diffDays = (now - dismissedDate) / (1000 * 60 * 60 * 24);
            
            // Show again after 7 days
            return diffDays < 7;
        },
        
        async installApp() {
            if (!this.deferredPrompt) {
                // Fallback for iOS
                alert('Untuk install di iOS:\n1. Tap tombol Share (kotak dengan panah)\n2. Pilih "Add to Home Screen"');
                return;
            }
            
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                this.showInstallBanner = false;
            }
            
            this.deferredPrompt = null;
        },
        
        dismissBanner() {
            this.showInstallBanner = false;
            localStorage.setItem('pwa-dismissed', new Date().toISOString());
        }
    }
}

// Global install function for other buttons
window.installPWA = function() {
    const event = new CustomEvent('trigger-pwa-install');
    window.dispatchEvent(event);
}
</script>
