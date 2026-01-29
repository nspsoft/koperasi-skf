@extends('layouts.app')

@section('title', 'Pengaturan Payment Gateway')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="page-title">🔐 Payment Gateway</h1>
                <p class="page-subtitle">Konfigurasi integrasi pembayaran digital (Midtrans, QRIS, VA, dll)</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div x-data="paymentGatewayConfig()" class="space-y-6">
        <!-- Provider Selection -->
        <div class="glass-card-solid p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <span class="text-2xl">🏦</span>
                Provider Payment Gateway
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <label class="relative cursor-pointer">
                    <input type="radio" name="provider" value="midtrans" x-model="provider" class="sr-only peer">
                    <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 transition-all text-center">
                        <div class="text-2xl mb-1">💳</div>
                        <div class="font-bold">Midtrans</div>
                        <div class="text-xs text-gray-500">Recommended</div>
                    </div>
                </label>
                
                <label class="relative cursor-pointer opacity-50">
                    <input type="radio" name="provider" value="xendit" disabled class="sr-only">
                    <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-center">
                        <div class="text-2xl mb-1">🔷</div>
                        <div class="font-bold">Xendit</div>
                        <div class="text-xs text-gray-400">Coming Soon</div>
                    </div>
                </label>
                
                <label class="relative cursor-pointer opacity-50">
                    <input type="radio" name="provider" value="doku" disabled class="sr-only">
                    <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-center">
                        <div class="text-2xl mb-1">🔵</div>
                        <div class="font-bold">Doku</div>
                        <div class="text-xs text-gray-400">Coming Soon</div>
                    </div>
                </label>
                
                <label class="relative cursor-pointer">
                    <input type="radio" name="provider" value="tripay" x-model="provider" class="sr-only peer">
                    <div class="p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 transition-all text-center">
                        <div class="text-2xl mb-1">🟢</div>
                        <div class="font-bold">Tripay</div>
                        <div class="text-xs text-gray-500">Budget-Friendly</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Midtrans Configuration -->
        <form action="{{ route('settings.payment-gateway.update') }}" method="POST" x-show="provider === 'midtrans'" x-transition>
            @csrf
            <input type="hidden" name="provider" :value="provider">
            
            <div class="glass-card-solid p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <span class="text-2xl">⚙️</span>
                        Konfigurasi Midtrans
                    </h3>
                    
                    <!-- Environment Toggle -->
                    <label class="flex items-center gap-3 cursor-pointer">
                        <span class="text-sm font-medium" :class="!isProduction ? 'text-yellow-600' : 'text-gray-400'">Sandbox</span>
                        <div class="relative">
                            <input type="checkbox" name="midtrans_is_production" x-model="isProduction" class="sr-only peer">
                            <div class="w-14 h-7 bg-yellow-400 peer-checked:bg-green-500 rounded-full transition-colors"></div>
                            <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full transition-transform peer-checked:translate-x-7"></div>
                        </div>
                        <span class="text-sm font-medium" :class="isProduction ? 'text-green-600' : 'text-gray-400'">Production</span>
                    </label>
                </div>
                
                <!-- Warning for Production Mode -->
                <div x-show="isProduction" x-transition class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-sm text-red-700 dark:text-red-300">
                            <p class="font-bold">Mode Production Aktif!</p>
                            <p>Transaksi akan diproses secara nyata. Pastikan semua konfigurasi sudah benar.</p>
                        </div>
                    </div>
                </div>

                <!-- API Credentials -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="form-label">Merchant ID</label>
                        <input type="text" name="midtrans_merchant_id" 
                               value="{{ $settings['midtrans_merchant_id'] }}"
                               placeholder="G123456789"
                               class="form-input font-mono">
                        <p class="text-xs text-gray-500 mt-1">Ditemukan di Dashboard Midtrans > Settings</p>
                    </div>
                    
                    <div>
                        <label class="form-label">Client Key</label>
                        <input type="text" name="midtrans_client_key" 
                               value="{{ $settings['midtrans_client_key'] }}"
                               placeholder="SB-Mid-client-xxxx atau Mid-client-xxxx"
                               class="form-input font-mono">
                        <p class="text-xs text-gray-500 mt-1">Untuk frontend (Snap popup)</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="form-label">Server Key 🔒</label>
                        <div class="relative">
                            <input :type="showServerKey ? 'text' : 'password'" 
                                   name="midtrans_server_key" 
                                   value="{{ $settings['midtrans_server_key'] }}"
                                   placeholder="SB-Mid-server-xxxx atau Mid-server-xxxx"
                                   class="form-input font-mono pr-12">
                            <button type="button" @click="showServerKey = !showServerKey" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg x-show="!showServerKey" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showServerKey" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">⚠️ Jangan bagikan Server Key ke siapapun. Untuk backend API calls.</p>
                    </div>
                </div>

                <!-- Webhook URL -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-6">
                    <label class="form-label flex items-center gap-2">
                        <span>🔗</span> Webhook URL (Payment Notification)
                    </label>
                    <div class="flex gap-2">
                        <input type="text" value="{{ $webhookUrl }}" readonly 
                               class="form-input bg-white dark:bg-gray-700 font-mono text-sm flex-1" id="webhookUrl">
                        <button type="button" onclick="copyWebhook()" 
                                class="btn-secondary px-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Copy
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Masukkan URL ini di Midtrans Dashboard > Settings > Configuration > Payment Notification URL</p>
                </div>

                <!-- Payment Methods -->
                <div class="mb-6">
                    <label class="form-label mb-3">💳 Metode Pembayaran Aktif</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="qris" 
                                   {{ in_array('qris', $settings['payment_methods']) ? 'checked' : '' }}
                                   class="form-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-medium">QRIS</div>
                                <div class="text-xs text-gray-500">Semua e-wallet</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="bank_transfer" 
                                   {{ in_array('bank_transfer', $settings['payment_methods']) ? 'checked' : '' }}
                                   class="form-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-medium">Virtual Account</div>
                                <div class="text-xs text-gray-500">BCA, BNI, Mandiri, BRI</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="gopay" 
                                   {{ in_array('gopay', $settings['payment_methods']) ? 'checked' : '' }}
                                   class="form-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-medium">GoPay</div>
                                <div class="text-xs text-gray-500">& GoPay Later</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="shopeepay" 
                                   {{ in_array('shopeepay', $settings['payment_methods']) ? 'checked' : '' }}
                                   class="form-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-medium">ShopeePay</div>
                                <div class="text-xs text-gray-500">& SPayLater</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="credit_card" 
                                   {{ in_array('credit_card', $settings['payment_methods']) ? 'checked' : '' }}
                                   class="form-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-medium">Credit Card</div>
                                <div class="text-xs text-gray-500">Visa, Mastercard, JCB</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="cstore" 
                                   {{ in_array('cstore', $settings['payment_methods']) ? 'checked' : '' }}
                                   class="form-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <div>
                                <div class="font-medium">Convenience Store</div>
                                <div class="text-xs text-gray-500">Indomaret, Alfamart</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="btn-primary flex-1 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Konfigurasi
                    </button>
                    
                    <button type="button" @click="testConnection()" 
                            :disabled="testing"
                            class="btn-secondary flex items-center justify-center gap-2">
                        <svg x-show="!testing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <svg x-show="testing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="testing ? 'Testing...' : 'Test Connection'"></span>
                    </button>
                </div>

                <!-- Test Result -->
                <div x-show="testResult" x-transition class="mt-4 p-4 rounded-xl" 
                     :class="testResult?.success ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'">
                    <div class="flex items-center gap-3">
                        <div x-show="testResult?.success" class="text-2xl">✅</div>
                        <div x-show="!testResult?.success" class="text-2xl">❌</div>
                        <div>
                            <p class="font-medium" x-text="testResult?.message" :class="testResult?.success ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'"></p>
                            <p x-show="testResult?.environment" class="text-sm text-gray-500" x-text="'Environment: ' + testResult?.environment"></p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Tripay Configuration -->
        <form action="{{ route('settings.payment-gateway.update') }}" method="POST" x-show="provider === 'tripay'" x-transition>
            @csrf
            <input type="hidden" name="provider" :value="provider">
            
            <div class="glass-card-solid p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <span class="text-2xl">🟢</span>
                        Konfigurasi Tripay
                    </h3>
                    
                    <!-- Environment Toggle -->
                    <label class="flex items-center gap-3 cursor-pointer">
                        <span class="text-sm font-medium" :class="!tripayIsProduction ? 'text-yellow-600' : 'text-gray-400'">Sandbox</span>
                        <div class="relative">
                            <input type="checkbox" name="tripay_is_production" x-model="tripayIsProduction" class="sr-only peer">
                            <div class="w-14 h-7 bg-yellow-400 peer-checked:bg-green-500 rounded-full transition-colors"></div>
                            <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full transition-transform peer-checked:translate-x-7"></div>
                        </div>
                        <span class="text-sm font-medium" :class="tripayIsProduction ? 'text-green-600' : 'text-gray-400'">Production</span>
                    </label>
                </div>
                
                <!-- Warning for Production Mode -->
                <div x-show="tripayIsProduction" x-transition class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-sm text-red-700 dark:text-red-300">
                            <p class="font-bold">Mode Production Aktif!</p>
                            <p>Transaksi akan diproses secara nyata. Pastikan semua konfigurasi sudah benar.</p>
                        </div>
                    </div>
                </div>

                <!-- API Credentials -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="form-label">Merchant Code</label>
                        <input type="text" name="tripay_merchant_code" 
                               value="{{ $settings['tripay_merchant_code'] }}"
                               placeholder="T12345"
                               class="form-input font-mono">
                        <p class="text-xs text-gray-500 mt-1">Kode merchant dari dashboard Tripay</p>
                    </div>
                    
                    <div>
                        <label class="form-label">API Key 🔒</label>
                        <div class="relative">
                            <input :type="showTripayApiKey ? 'text' : 'password'" 
                                   name="tripay_api_key" 
                                   value="{{ $settings['tripay_api_key'] }}"
                                   placeholder="DEV-xxxxx atau xxxxx (production)"
                                   class="form-input font-mono pr-12">
                            <button type="button" @click="showTripayApiKey = !showTripayApiKey" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg x-show="!showTripayApiKey" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showTripayApiKey" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Untuk authentication API calls</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="form-label">Private Key 🔒</label>
                        <div class="relative">
                            <input :type="showTripayPrivateKey ? 'text' : 'password'" 
                                   name="tripay_private_key" 
                                   value="{{ $settings['tripay_private_key'] }}"
                                   placeholder="xxxxx-xxxxx-xxxxx"
                                   class="form-input font-mono pr-12">
                            <button type="button" @click="showTripayPrivateKey = !showTripayPrivateKey" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg x-show="!showTripayPrivateKey" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showTripayPrivateKey" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">⚠️ Untuk generate signature. Jangan bagikan ke siapapun.</p>
                    </div>
                </div>

                <!-- Webhook URL -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-6">
                    <label class="form-label flex items-center gap-2">
                        <span>🔗</span> Callback URL (Payment Notification)
                    </label>
                    <div class="flex gap-2">
                        <input type="text" value="{{ $webhookUrl }}" readonly 
                               class="form-input bg-white dark:bg-gray-700 font-mono text-sm flex-1" id="webhookUrlTripay">
                        <button type="button" onclick="copyWebhookTripay()" 
                                class="btn-secondary px-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Copy
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Masukkan URL ini di Tripay Dashboard > Pengaturan > Callback URL</p>
                </div>

                <!-- Payment Methods -->
                <div class="mb-6">
                    <label class="form-label mb-3">💳 Metode Pembayaran Aktif</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="qris" 
                                   {{ in_array('qris', $settings['payment_methods']) ? 'checked' : '' }}
                                   class="form-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <div>
                                <div class="font-medium">QRIS</div>
                                <div class="text-xs text-gray-500">Semua e-wallet</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="bank_transfer" 
                                   {{ in_array('bank_transfer', $settings['payment_methods']) ? 'checked' : '' }}
                                   class="form-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <div>
                                <div class="font-medium">Virtual Account</div>
                                <div class="text-xs text-gray-500">BCA, BNI, Mandiri, BRI</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="ewallet" 
                                   {{ in_array('ewallet', $settings['payment_methods']) ? 'checked' : '' }}
                                   class="form-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <div>
                                <div class="font-medium">E-Wallet</div>
                                <div class="text-xs text-gray-500">OVO, Dana, LinkAja</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="cstore" 
                                   {{ in_array('cstore', $settings['payment_methods']) ? 'checked' : '' }}
                                   class="form-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <div>
                                <div class="font-medium">Convenience Store</div>
                                <div class="text-xs text-gray-500">Indomaret, Alfamart</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="btn-primary flex-1 flex items-center justify-center gap-2 !bg-green-600 hover:!bg-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Konfigurasi
                    </button>
                    
                    <button type="button" @click="testConnection()" 
                            :disabled="testing"
                            class="btn-secondary flex items-center justify-center gap-2">
                        <svg x-show="!testing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <svg x-show="testing" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="testing ? 'Testing...' : 'Test Connection'"></span>
                    </button>
                </div>

                <!-- Test Result -->
                <div x-show="testResult" x-transition class="mt-4 p-4 rounded-xl" 
                     :class="testResult?.success ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'">
                    <div class="flex items-center gap-3">
                        <div x-show="testResult?.success" class="text-2xl">✅</div>
                        <div x-show="!testResult?.success" class="text-2xl">❌</div>
                        <div>
                            <p class="font-medium" x-text="testResult?.message" :class="testResult?.success ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'"></p>
                            <p x-show="testResult?.environment" class="text-sm text-gray-500" x-text="'Environment: ' + testResult?.environment"></p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Help Section -->
        <div class="glass-card-solid p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <span class="text-2xl">📚</span>
                Panduan Setup Midtrans
            </h3>
            
            <div class="prose dark:prose-invert max-w-none text-sm">
                <ol class="space-y-3">
                    <li>
                        <strong>Daftar Akun Midtrans</strong><br>
                        Kunjungi <a href="https://dashboard.midtrans.com/register" target="_blank" class="text-primary-600 hover:underline">dashboard.midtrans.com/register</a>
                    </li>
                    <li>
                        <strong>Dapatkan API Keys</strong><br>
                        Settings → Access Keys → Copy Server Key dan Client Key
                    </li>
                    <li>
                        <strong>Setup Webhook</strong><br>
                        Settings → Configuration → Payment Notification URL → Paste webhook URL di atas
                    </li>
                    <li>
                        <strong>Test di Sandbox</strong><br>
                        Gunakan mode Sandbox untuk testing sebelum go live
                    </li>
                    <li>
                        <strong>Go Production</strong><br>
                        Setelah testing selesai, aktifkan mode Production dengan API Key Production
                    </li>
                </ol>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function paymentGatewayConfig() {
    return {
        provider: '{{ $settings["provider"] }}',
        // Midtrans
        isProduction: {{ $settings['midtrans_is_production'] ? 'true' : 'false' }},
        showServerKey: false,
        // Tripay
        tripayIsProduction: {{ $settings['tripay_is_production'] ? 'true' : 'false' }},
        showTripayApiKey: false,
        showTripayPrivateKey: false,
        // Common
        testing: false,
        testResult: null,

        async testConnection() {
            this.testing = true;
            this.testResult = null;

            try {
                const response = await fetch('{{ route("settings.payment-gateway.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                this.testResult = await response.json();
            } catch (error) {
                this.testResult = {
                    success: false,
                    message: 'Gagal menghubungi server: ' + error.message
                };
            }

            this.testing = false;
        }
    }
}

function copyWebhook() {
    copyToClipboard('webhookUrl');
}

function copyWebhookTripay() {
    copyToClipboard('webhookUrlTripay');
}

function copyToClipboard(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    document.execCommand('copy');
    
    // Show toast notification
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Copied!';
    btn.classList.add('text-green-600');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('text-green-600');
    }, 2000);
}
</script>
@endpush
