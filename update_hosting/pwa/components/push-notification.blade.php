{{-- Push Notification Permission Component --}}
<div x-data="pushNotification()" x-init="init()">
    {{-- Settings Toggle in Profile/Settings --}}
    @if(isset($showToggle) && $showToggle)
    <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-800 dark:text-white">Push Notifications</p>
                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="statusText"></p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <template x-if="permission === 'granted' && isSubscribed">
                <button @click="testPush()" 
                        class="px-3 py-1.5 text-xs bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors"
                        :disabled="testing">
                    <span x-show="!testing">Test</span>
                    <span x-show="testing">Sending...</span>
                </button>
            </template>
            
            <button @click="toggleNotification()" 
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                    :class="isSubscribed ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'"
                    :disabled="loading || permission === 'denied'">
                <span class="sr-only">Toggle notifications</span>
                <span class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                      :class="isSubscribed ? 'translate-x-5' : 'translate-x-0'">
                    <span x-show="loading" class="absolute inset-0 flex items-center justify-center">
                        <svg class="animate-spin h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </span>
            </button>
        </div>
    </div>
    @endif
    
    {{-- First Time Permission Request Modal --}}
    <div x-show="showPermissionModal" 
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 text-center text-white">
                <div class="w-16 h-16 bg-white/20 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold">Aktifkan Notifikasi</h3>
                <p class="text-sm text-emerald-100 mt-2">Dapatkan update real-time untuk:</p>
            </div>
            
            <div class="p-6 space-y-3">
                <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Pinjaman disetujui/ditolak</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Pesanan siap diambil</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Pengumuman penting</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Reminder pembayaran</span>
                </div>
            </div>
            
            <div class="p-4 pt-0 flex gap-2">
                <button @click="requestPermission()" 
                        class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all"
                        :disabled="loading">
                    <span x-show="!loading">Ya, Aktifkan</span>
                    <span x-show="loading">Loading...</span>
                </button>
                <button @click="dismissModal()" 
                        class="py-3 px-6 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl font-medium text-sm transition-colors">
                    Nanti
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function pushNotification() {
    return {
        permission: 'default',
        isSubscribed: false,
        loading: false,
        testing: false,
        showPermissionModal: false,
        
        get statusText() {
            if (this.permission === 'denied') return 'Diblokir oleh browser';
            if (this.permission === 'granted' && this.isSubscribed) return 'Aktif';
            if (this.permission === 'granted' && !this.isSubscribed) return 'Tidak aktif';
            return 'Belum diaktifkan';
        },
        
        async init() {
            if (!('Notification' in window) || !('serviceWorker' in navigator)) {
                console.log('Push notifications not supported');
                return;
            }
            
            this.permission = Notification.permission;
            
            if (this.permission === 'granted') {
                await this.checkSubscription();
            }
            
            // Show permission modal for new users after 10 seconds
            if (this.permission === 'default' && !localStorage.getItem('push-modal-dismissed')) {
                setTimeout(() => {
                    this.showPermissionModal = true;
                }, 10000);
            }
        },
        
        async checkSubscription() {
            const reg = await navigator.serviceWorker.ready;
            const subscription = await reg.pushManager.getSubscription();
            this.isSubscribed = !!subscription;
        },
        
        async requestPermission() {
            this.loading = true;
            
            const permission = await Notification.requestPermission();
            this.permission = permission;
            
            if (permission === 'granted') {
                await this.subscribe();
            }
            
            this.showPermissionModal = false;
            this.loading = false;
        },
        
        async subscribe() {
            try {
                const response = await fetch('/api/push/vapid-public-key');
                const { publicKey } = await response.json();
                
                if (!publicKey) {
                    console.error('VAPID public key not configured');
                    return;
                }
                
                const reg = await navigator.serviceWorker.ready;
                const subscription = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(publicKey)
                });
                
                // Send subscription to server
                await fetch('/api/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(subscription.toJSON())
                });
                
                this.isSubscribed = true;
            } catch (err) {
                console.error('Failed to subscribe:', err);
            }
        },
        
        async unsubscribe() {
            try {
                const reg = await navigator.serviceWorker.ready;
                const subscription = await reg.pushManager.getSubscription();
                
                if (subscription) {
                    await fetch('/api/push/unsubscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ endpoint: subscription.endpoint })
                    });
                    
                    await subscription.unsubscribe();
                }
                
                this.isSubscribed = false;
            } catch (err) {
                console.error('Failed to unsubscribe:', err);
            }
        },
        
        async toggleNotification() {
            this.loading = true;
            
            if (this.permission !== 'granted') {
                await this.requestPermission();
            } else if (this.isSubscribed) {
                await this.unsubscribe();
            } else {
                await this.subscribe();
            }
            
            this.loading = false;
        },
        
        async testPush() {
            this.testing = true;
            
            try {
                const response = await fetch('/api/push/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                alert(result.message);
            } catch (err) {
                alert('Failed to send test notification');
            }
            
            this.testing = false;
        },
        
        dismissModal() {
            this.showPermissionModal = false;
            localStorage.setItem('push-modal-dismissed', 'true');
        },
        
        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    }
}
</script>
