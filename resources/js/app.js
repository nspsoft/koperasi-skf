import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

window.Alpine = Alpine;

// PWA Installation Logic
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    console.log('beforeinstallprompt event fired');
    e.preventDefault();
    deferredPrompt = e;
    window.dispatchEvent(new CustomEvent('pwa-installable'));
});

window.addEventListener('appinstalled', (event) => {
    deferredPrompt = null;
    console.log('PWA was installed');
});

window.installPWA = async () => {
    if (!deferredPrompt) {
        alert('Gunakan menu browser (titik tiga) lalu pilih "Instal Aplikasi" atau "Tambahkan ke Layar Utama".');
        return;
    }
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    console.log(`User response to the install prompt: ${outcome}`);
    deferredPrompt = null;
};

Alpine.start();
