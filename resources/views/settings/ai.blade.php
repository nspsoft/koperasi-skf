@extends('layouts.app')

@section('title', __('messages.titles.settings_ai'))

@section('content')
<div class="space-y-6" x-data="aiSettings()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pengaturan AI Assistant</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Konfigurasi AI Assistant untuk aplikasi</p>
        </div>
        <a href="{{ route('settings.index') }}" class="btn-secondary">
            ← Kembali
        </a>
    </div>

    <!-- Main Card -->
    <div class="glass-card p-6">
        <form method="POST" action="{{ route('settings.ai.update') }}" @submit="saving = true">
            @csrf
            
            <div class="space-y-6">
                <!-- Enable Toggle -->
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Aktifkan AI Assistant</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tampilkan tombol AI Assistant di semua halaman</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="ai_enabled" value="false">
                        <input type="checkbox" name="ai_enabled" value="true" class="sr-only peer" 
                               {{ ($settings['ai_enabled'] ?? 'true') === 'true' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                    </label>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <div>
                    <label class="form-label">Provider AI</label>
                    <select name="ai_provider" x-model="provider" @change="onProviderChange()" 
                            class="form-input">
                        <option value="ollama">Ollama (Lokal)</option>
                        <option value="openai">OpenAI</option>
                        <option value="gemini">Google Gemini</option>
                        <option value="custom">Custom API</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <span x-show="provider === 'ollama'">Ollama berjalan di komputer lokal</span>
                        <span x-show="provider === 'openai'">Memerlukan API Key dari OpenAI</span>
                        <span x-show="provider === 'gemini'">Memerlukan API Key dari Google AI Studio</span>
                        <span x-show="provider === 'custom'">Gunakan endpoint API kustom</span>
                    </p>
                </div>

                <!-- API URL -->
                <div>
                    <label class="form-label">API URL</label>
                    <input type="url" name="ai_url" x-model="url" class="form-input" 
                           placeholder="http://localhost:11434" required>
                    <p class="text-xs text-gray-500 mt-1">
                        Ollama default: http://localhost:11434
                    </p>
                </div>

                <!-- API Key (for OpenAI/Gemini) -->
                <div x-show="provider === 'openai' || provider === 'gemini'" x-transition>
                    <label class="form-label">API Key</label>
                    <input type="password" name="ai_api_key" x-model="apiKey" class="form-input" 
                           :placeholder="provider === 'gemini' ? 'AIza...' : 'sk-...'">
                    <p class="text-xs text-gray-500 mt-1">
                        <span x-show="provider === 'openai'">Dapatkan API Key dari <a href="https://platform.openai.com/api-keys" target="_blank" class="text-purple-600 hover:underline">OpenAI Platform</a></span>
                        <span x-show="provider === 'gemini'">Dapatkan API Key dari <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-blue-600 hover:underline">Google AI Studio</a></span>
                    </p>
                </div>

                <!-- Model Selection -->
                <div>
                    <label class="form-label">Model AI</label>
                    {{-- Hidden input to ensure model is always submitted --}}
                    <input type="hidden" name="ai_model" :value="model">
                    <div class="flex gap-2">
                        {{-- Dropdown for Gemini --}}
                        <template x-if="provider === 'gemini'">
                            <select x-model="model" class="form-input flex-1">
                                <optgroup label="🔥 Terbaru (Recommended)">
                                    <option value="gemini-2.5-flash">Gemini 2.5 Flash (Terbaru, Cepat)</option>
                                    <option value="gemini-2.5-pro">Gemini 2.5 Pro (Terbaru, Pintar)</option>
                                    <option value="gemini-2.0-flash">Gemini 2.0 Flash (Stabil)</option>
                                </optgroup>
                                <optgroup label="⚡ Version 1.5">
                                    <option value="gemini-1.5-flash">Gemini 1.5 Flash (Cepat & Hemat)</option>
                                    <option value="gemini-1.5-flash-8b">Gemini 1.5 Flash 8B (Ringan)</option>
                                    <option value="gemini-1.5-pro">Gemini 1.5 Pro (Pintar)</option>
                                </optgroup>
                                <optgroup label="🧪 Experimental">
                                    <option value="gemini-2.0-flash-exp">Gemini 2.0 Flash Exp</option>
                                    <option value="gemini-2.0-flash-exp-image-generation">Gemini 2.0 + Image Gen</option>
                                </optgroup>
                                <optgroup label="🔧 Legacy">
                                    <option value="gemini-1.0-pro">Gemini 1.0 Pro</option>
                                </optgroup>
                            </select>
                        </template>
                        {{-- Dropdown for OpenAI --}}
                        <template x-if="provider === 'openai'">
                            <select x-model="model" class="form-input flex-1">
                                <optgroup label="🔥 GPT-4o (Terbaru)">
                                    <option value="gpt-4o">GPT-4o (Multimodal, Pintar)</option>
                                    <option value="gpt-4o-mini">GPT-4o Mini (Hemat)</option>
                                </optgroup>
                                <optgroup label="⚡ GPT-4 Turbo">
                                    <option value="gpt-4-turbo">GPT-4 Turbo (128K Context)</option>
                                    <option value="gpt-4-turbo-preview">GPT-4 Turbo Preview</option>
                                </optgroup>
                                <optgroup label="💰 GPT-3.5 (Murah)">
                                    <option value="gpt-3.5-turbo">GPT-3.5 Turbo (Cepat)</option>
                                    <option value="gpt-3.5-turbo-16k">GPT-3.5 Turbo 16K</option>
                                </optgroup>
                                <optgroup label="🔧 Legacy">
                                    <option value="gpt-4">GPT-4 (Original)</option>
                                    <option value="gpt-4-32k">GPT-4 32K</option>
                                </optgroup>
                            </select>
                        </template>
                        {{-- Dropdown for Ollama --}}
                        <template x-if="provider === 'ollama'">
                            <select x-model="model" class="form-input flex-1">
                                <optgroup label="🔥 Llama 3 (Meta)">
                                    <option value="llama3.2">Llama 3.2 (Terbaru, 3B)</option>
                                    <option value="llama3.1">Llama 3.1 (8B)</option>
                                    <option value="llama3">Llama 3 (8B)</option>
                                    <option value="llama3:70b">Llama 3 70B (Besar)</option>
                                </optgroup>
                                <optgroup label="💎 Gemma (Google Open)">
                                    <option value="gemma3">Gemma 3 (4B)</option>
                                    <option value="gemma3:12b">Gemma 3 12B</option>
                                    <option value="gemma3:27b">Gemma 3 27B (Pintar)</option>
                                    <option value="gemma2">Gemma 2 (9B)</option>
                                </optgroup>
                                <optgroup label="🚀 Qwen (Alibaba)">
                                    <option value="qwen2.5">Qwen 2.5 (7B)</option>
                                    <option value="qwen2.5:14b">Qwen 2.5 14B</option>
                                    <option value="qwen2.5:32b">Qwen 2.5 32B</option>
                                </optgroup>
                                <optgroup label="💻 Coding Models">
                                    <option value="codellama">Code Llama (7B)</option>
                                    <option value="codegemma">Code Gemma (7B)</option>
                                    <option value="deepseek-coder">DeepSeek Coder (6.7B)</option>
                                    <option value="starcoder2">StarCoder 2 (3B)</option>
                                </optgroup>
                                <optgroup label="🔧 Others">
                                    <option value="mistral">Mistral (7B)</option>
                                    <option value="mixtral">Mixtral 8x7B</option>
                                    <option value="phi3">Phi-3 (Microsoft)</option>
                                    <option value="llama2">Llama 2 (Legacy)</option>
                                </optgroup>
                            </select>
                        </template>
                        {{-- Text input for Custom only --}}
                        <template x-if="provider === 'custom'">
                            <input type="text" x-model="model" class="form-input flex-1" 
                                   placeholder="model-name" required>
                        </template>
                        <button type="button" @click="testConnection()" 
                                :disabled="testing"
                                class="btn-secondary whitespace-nowrap">
                            <span x-show="!testing">🔗 Test Koneksi</span>
                            <span x-show="testing">⏳ Testing...</span>
                        </button>
                    </div>
                    
                    <!-- Connection Status -->
                    <div x-show="connectionResult" class="mt-2 p-3 rounded-lg text-sm"
                         :class="connectionSuccess ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300'">
                        <span x-text="connectionResult"></span>
                    </div>
                    
                    <!-- Available Models (for Ollama/Custom after test) -->
                    <div x-show="availableModels.length > 0 && (provider === 'ollama' || provider === 'custom')" class="mt-2">
                        <p class="text-xs text-gray-500 mb-1">Model tersedia:</p>
                        <div class="flex flex-wrap gap-1">
                            <template x-for="m in availableModels" :key="m">
                                <button type="button" @click="model = m" 
                                        class="px-2 py-1 text-xs rounded-lg transition-colors"
                                        :class="model === m ? 'bg-purple-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                        x-text="m"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- System Prompt -->
                <div>
                    <label class="form-label">System Prompt</label>
                    <textarea name="ai_system_prompt" x-model="systemPrompt" rows="4" class="form-input" 
                              placeholder="Kamu adalah AI Assistant..." required></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        Konteks yang diberikan ke AI untuk memahami perannya
                    </p>
                </div>

                <!-- Preset Prompts -->
                <div>
                    <label class="form-label">Preset Prompt</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="setPreset('koperasi')" class="px-3 py-1.5 text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-200 dark:hover:bg-purple-900/50">
                            🏦 Asisten Koperasi
                        </button>
                        <button type="button" @click="setPreset('general')" class="px-3 py-1.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50">
                            💬 Asisten Umum
                        </button>
                        <button type="button" @click="setPreset('coding')" class="px-3 py-1.5 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50">
                            💻 Asisten Coding
                        </button>
                    </div>
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <!-- WhatsApp Bot Integration -->
                <div class="space-y-4" x-data="{ 
                    waEnabled: {{ ($settings['wa_bot_enabled'] ?? 'false') === 'true' ? 'true' : 'false' }},
                    waProvider: '{{ $settings['wa_provider'] ?? 'fonnte' }}'
                }">
                    <!-- Header with Toggle -->
                    <div class="flex items-center justify-between p-4 rounded-xl border transition-all duration-300"
                         :class="waEnabled ? 'bg-green-50 dark:bg-green-900/30 border-green-300 dark:border-green-700 ring-2 ring-green-400/50' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700'">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-2xl shadow-lg transition-all duration-300"
                                 :class="waEnabled ? 'bg-green-500 shadow-green-500/30 animate-pulse' : 'bg-gray-400 shadow-gray-400/20'">
                                📱
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-gray-900 dark:text-white">WhatsApp AI Bot</h3>
                                    <span x-show="waEnabled" x-transition 
                                          class="px-2 py-0.5 text-[10px] font-bold bg-green-500 text-white rounded-full uppercase tracking-wider">
                                        Aktif
                                    </span>
                                    <span x-show="!waEnabled" x-transition 
                                          class="px-2 py-0.5 text-[10px] font-medium bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-full uppercase tracking-wider">
                                        Nonaktif
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Hubungkan AI dengan WhatsApp anggota</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="wa_bot_enabled" value="false">
                            <input type="checkbox" name="wa_bot_enabled" value="true" class="sr-only peer" 
                                   x-model="waEnabled"
                                   {{ ($settings['wa_bot_enabled'] ?? 'false') === 'true' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                        </label>
                    </div>

                    <!-- Provider Selection -->
                    <div class="grid md:grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="wa_provider" value="fonnte" x-model="waProvider" class="peer sr-only" {{ ($settings['wa_provider'] ?? 'fonnte') === 'fonnte' ? 'checked' : '' }}>
                            <div class="p-3 rounded-xl border-2 transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20 border-gray-200 dark:border-gray-700 hover:border-orange-300">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">FN</div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Fonnté</p>
                                        <p class="text-[10px] text-gray-500">Unofficial · Murah · Rp 25rb-355rb/bln</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="wa_provider" value="twilio" x-model="waProvider" class="peer sr-only" {{ ($settings['wa_provider'] ?? 'fonnte') === 'twilio' ? 'checked' : '' }}>
                            <div class="p-3 rounded-xl border-2 transition-all peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 border-gray-200 dark:border-gray-700 hover:border-red-300">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">TW</div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">Twilio <span class="text-[9px] bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 px-1.5 py-0.5 rounded-full ml-1">Official API</span></p>
                                        <p class="text-[10px] text-gray-500">Meta Partner · ~Rp 300-600/pesan</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Fonnte Settings -->
                    <div x-show="waProvider === 'fonnte'" x-transition class="space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Fonnté API Token</label>
                                <div class="flex gap-2">
                                    <input type="password" name="fonnte_token" x-model="fonnteToken" value="{{ $settings['fonnte_token'] ?? '' }}" class="form-input flex-1" placeholder="Token dari dashboard fonnte">
                                    <button type="button" @click="testFonnte()" :disabled="testingWa" 
                                            class="btn-secondary whitespace-nowrap text-sm">
                                        <span x-show="!testingWa">📤 Test</span>
                                        <span x-show="testingWa">⏳</span>
                                    </button>
                                </div>
                                <p class="text-[10px] text-gray-500 mt-1">Dapatkan di <a href="https://fonnte.com" target="_blank" class="text-orange-600 font-bold">fonnte.com</a></p>
                                <div x-show="waTestResult && waProvider === 'fonnte'" class="mt-2 p-2 rounded-lg text-xs"
                                     :class="waTestSuccess ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300'">
                                    <span x-text="waTestResult"></span>
                                </div>
                            </div>
                            <div>
                                <label class="form-label text-gray-400 flex items-center gap-2">
                                    Webhook URL <span class="text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-500">Auto</span>
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" readonly value="{{ url('/webhook/whatsapp') }}" class="form-input bg-gray-50 dark:bg-gray-800 text-gray-500 cursor-not-allowed text-xs">
                                    <button type="button" @click="navigator.clipboard.writeText('{{ url('/webhook/whatsapp') }}'); $dispatch('notify', {message: 'URL disalin!', type: 'success'})" 
                                            class="px-3 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 transition-colors" title="Salin">📋</button>
                                </div>
                                <p class="text-[10px] text-gray-500 mt-1">Paste di pengaturan Webhook Fonnte</p>
                            </div>
                        </div>
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-900/30">
                            <p class="text-xs text-amber-700 dark:text-amber-300 font-medium">⚠️ Fonnté adalah layanan <strong>unofficial</strong>. Ada risiko ban dari WhatsApp.</p>
                        </div>
                    </div>

                    <!-- Twilio Settings -->
                    <div x-show="waProvider === 'twilio'" x-transition class="space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Twilio Account SID</label>
                                <input type="text" name="twilio_sid" value="{{ $settings['twilio_sid'] ?? '' }}" class="form-input" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                <p class="text-[10px] text-gray-500 mt-1">Dari <a href="https://console.twilio.com" target="_blank" class="text-red-600 font-bold">Twilio Console</a></p>
                            </div>
                            <div>
                                <label class="form-label">Twilio Auth Token</label>
                                <input type="password" name="twilio_token" value="{{ $settings['twilio_token'] ?? '' }}" class="form-input" placeholder="Auth Token">
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">WhatsApp Number (Twilio)</label>
                                <input type="text" name="twilio_wa_number" value="{{ $settings['twilio_wa_number'] ?? '' }}" class="form-input" placeholder="+14155238886">
                                <p class="text-[10px] text-gray-500 mt-1">Format: +1xxxxx (nomor dari Twilio)</p>
                            </div>
                            <div>
                                <label class="form-label text-gray-400 flex items-center gap-2">
                                    Webhook URL <span class="text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-gray-500">Auto</span>
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" readonly value="{{ url('/webhook/whatsapp/twilio') }}" class="form-input bg-gray-50 dark:bg-gray-800 text-gray-500 cursor-not-allowed text-xs">
                                    <button type="button" @click="navigator.clipboard.writeText('{{ url('/webhook/whatsapp/twilio') }}'); $dispatch('notify', {message: 'URL disalin!', type: 'success'})" 
                                            class="px-3 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 transition-colors" title="Salin">📋</button>
                                </div>
                                <p class="text-[10px] text-gray-500 mt-1">Paste di Twilio WhatsApp Sandbox Settings</p>
                            </div>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-900/30">
                            <p class="text-xs text-blue-700 dark:text-blue-300 font-medium">✅ Twilio adalah <strong>Official Meta Partner</strong>. Tidak ada risiko ban, bisa dapat centang hijau.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-primary" :disabled="saving">
                    <span x-show="!saving">💾 Simpan Pengaturan</span>
                    <span x-show="saving">⏳ Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Info Card -->
    <div class="glass-card p-6">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">ℹ️ Panduan Setup</h3>
        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
            <div class="flex gap-3">
                <span class="text-purple-500">📥</span>
                <div>
                    <strong>Ollama:</strong> Download di <a href="https://ollama.ai" target="_blank" class="text-purple-600 hover:underline">ollama.ai</a>, lalu jalankan <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">ollama serve</code>
                </div>
            </div>
            <div class="flex gap-3">
                <span class="text-purple-500">🌐</span>
                <div>
                    <strong>CORS:</strong> Untuk Ollama, set environment <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">OLLAMA_ORIGINS=*</code>
                </div>
            </div>
            <div class="flex gap-3">
                <span class="text-purple-500">📦</span>
                <div>
                    <strong>Model:</strong> Download model dengan <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">ollama pull llama2</code>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function aiSettings() {
    return {
        provider: '{{ $settings['ai_provider'] ?? 'ollama' }}',
        url: '{{ $settings['ai_url'] ?? 'http://localhost:11434' }}',
        model: '{{ $settings['ai_model'] ?? 'llama2' }}',
        apiKey: '{{ $settings['ai_api_key'] ?? '' }}',
        systemPrompt: `{{ $settings['ai_system_prompt'] ?? 'Kamu adalah AI Assistant untuk aplikasi Koperasi Karyawan.' }}`,
        fonnteToken: '{{ $settings['fonnte_token'] ?? '' }}',
        testing: false,
        saving: false,
        testingWa: false,
        waTestResult: '',
        waTestSuccess: false,
        connectionResult: '',
        connectionSuccess: false,
        availableModels: [],
        
        onProviderChange() {
            if (this.provider === 'ollama') {
                this.url = 'http://localhost:11434';
                this.model = 'llama2';
            } else if (this.provider === 'openai') {
                this.url = 'https://api.openai.com';
                this.model = 'gpt-3.5-turbo';
            } else if (this.provider === 'gemini') {
                this.url = 'https://generativelanguage.googleapis.com';
                this.model = 'gemini-1.5-flash';
            }
            this.availableModels = [];
            this.connectionResult = '';
        },
        
        async testConnection() {
            this.testing = true;
            this.connectionResult = '';
            
            try {
                const response = await fetch('{{ route("settings.ai.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        provider: this.provider,
                        url: this.url,
                        api_key: this.apiKey
                    })
                });
                
                const data = await response.json();
                this.connectionSuccess = data.success;
                this.connectionResult = data.message;
                this.availableModels = data.models || [];
                
            } catch (error) {
                this.connectionSuccess = false;
                this.connectionResult = 'Error: ' + error.message;
            }
            
            this.testing = false;
        },

        async testFonnte() {
            if (!this.fonnteToken) {
                this.waTestResult = 'Masukkan token Fonnte terlebih dahulu';
                this.waTestSuccess = false;
                return;
            }

            this.testingWa= true;
            this.waTestResult = '';

            try {
                const response = await fetch('{{ route("settings.ai.test-fonnte") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        token: this.fonnteToken
                    })
                });
                
                const data = await response.json();
                this.waTestSuccess = data.success;
                this.waTestResult = data.message;
                
            } catch (error) {
                this.waTestSuccess = false;
                this.waTestResult = 'Error: ' + error.message;
            }
            
            this.testingWa = false;
        },
        
        setPreset(type) {
            const presets = {
                koperasi: 'Kamu adalah AI Assistant Finansial untuk Koperasi Karyawan PT. SPINDO TBK. Tugas utamamu adalah membantu Admin menganalisa kesehatan keuangan berdasarkan data real-time yang diberikan (Simpanan, Pinjaman, Outstanding, dan Omzet Mart). Berikan saran strategis untuk meningkatkan partisipasi anggota dan efisiensi operasional. Jawab dengan profesional, ramah, dan berbasis data.',
                general: 'Kamu adalah AI Assistant yang ramah dan membantu. Jawab pertanyaan dengan jelas dan informatif dalam bahasa Indonesia.',
                coding: 'Kamu adalah AI Assistant untuk programming dan coding. Bantu dengan pertanyaan tentang kode, debugging, dan best practices. Berikan contoh kode yang jelas dan penjelasan yang mudah dipahami.'
            };
            this.systemPrompt = presets[type] || '';
        }
    }
}
</script>
@endsection
