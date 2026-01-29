{{-- AI Assistant Floating Button & Chat Panel --}}
@php
    $aiConfig = \App\Models\AiSetting::getConfig();
@endphp

@if($aiConfig['enabled'])
<div x-data="aiAssistant()" x-cloak class="fixed z-50">
    
    {{-- Floating Button (positioned above WhatsApp) --}}
    <button @click="toggleChat()" 
            class="fixed bottom-24 right-6 w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 text-white rounded-full shadow-2xl hover:shadow-purple-500/50 hover:scale-110 transition-all duration-300 flex items-center justify-center group"
            :class="{ 'rotate-0': !isOpen, 'rotate-90': isOpen }">
        {{-- AI Icon --}}
        <svg x-show="!isOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
        </svg>
        {{-- Close Icon --}}
        <svg x-show="isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        
        {{-- Pulse Effect --}}
        <span class="absolute inset-0 rounded-full bg-purple-400 animate-ping opacity-20" x-show="!isOpen"></span>
    </button>

    {{-- Chat Panel --}}
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         :style="isFullscreen 
             ? 'position: fixed; top: 16px; left: 16px; right: 16px; bottom: 16px; width: auto; max-width: none; z-index: 9999;' 
             : 'position: fixed; bottom: 96px; right: 24px; width: 384px; max-width: calc(100vw - 48px); z-index: 50;'"
         class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700 flex flex-col">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-red-500 to-pink-600 px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-white">AI Assistant (TEST RED)</h3>
                    <p class="text-xs text-purple-100" x-text="connectionStatus"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- Model Selector --}}
                <select x-model="selectedModel" 
                        class="text-xs bg-white/20 text-white border-0 rounded-lg py-1 px-2 focus:ring-2 focus:ring-white/50"
                        @change="checkConnection()">
                    <template x-for="model in availableModels" :key="model">
                        <option :value="model" x-text="model" class="text-gray-800"></option>
                    </template>
                </select>
                {{-- Fullscreen Toggle --}}
                <button type="button" 
                        @click="isFullscreen = !isFullscreen" 
                        class="p-2 hover:bg-white/20 rounded-lg transition-colors text-white" 
                        :title="isFullscreen ? 'Minimize' : 'Fullscreen'">
                    {{-- Maximize Icon --}}
                    <svg x-show="!isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    {{-- Minimize Icon --}}
                    <svg x-show="isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                {{-- Clear Chat --}}
                <button type="button" 
                        @click="clearChat()" 
                        class="p-2 hover:bg-white/20 rounded-lg transition-colors text-white" 
                        title="Clear Chat">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Messages Area --}}
        <div x-ref="messagesContainer" 
             :class="isFullscreen ? 'flex-1' : 'h-80'"
             class="overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900/50">
            {{-- Welcome Message with Quick Prompts --}}
            <template x-if="messages.length === 0">
                <div class="text-center py-4">
                    <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-800 dark:text-white mb-1 text-sm">Halo! Saya AI Assistant</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Pilih topik atau ketik pertanyaan Anda</p>
                    
                    {{-- Quick Prompt Buttons --}}
                    <div class="grid grid-cols-2 gap-2 text-left">
                        <button @click="sendQuickPrompt('Bagaimana cara mendaftar menjadi anggota koperasi?')" 
                                class="flex items-center gap-2 p-2.5 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all text-xs">
                            <span class="text-base">📋</span>
                            <span class="text-gray-700 dark:text-gray-300">Cara Daftar</span>
                        </button>
                        <button @click="sendQuickPrompt('Jelaskan jenis-jenis simpanan di koperasi')" 
                                class="flex items-center gap-2 p-2.5 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all text-xs">
                            <span class="text-base">💰</span>
                            <span class="text-gray-700 dark:text-gray-300">Info Simpanan</span>
                        </button>
                        <button @click="sendQuickPrompt('Bagaimana cara mengajukan pinjaman?')" 
                                class="flex items-center gap-2 p-2.5 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all text-xs">
                            <span class="text-base">🏦</span>
                            <span class="text-gray-700 dark:text-gray-300">Ajukan Pinjaman</span>
                        </button>
                        <button @click="sendQuickPrompt('Bagaimana cara belanja online di koperasi?')" 
                                class="flex items-center gap-2 p-2.5 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all text-xs">
                            <span class="text-base">🛒</span>
                            <span class="text-gray-700 dark:text-gray-300">Belanja Online</span>
                        </button>
                        <button @click="sendQuickPrompt('Apa itu SHU dan bagaimana cara pembagiannya?')" 
                                class="flex items-center gap-2 p-2.5 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all text-xs">
                            <span class="text-base">📊</span>
                            <span class="text-gray-700 dark:text-gray-300">Info SHU</span>
                        </button>
                        <button @click="sendQuickPrompt('Tampilkan FAQ atau pertanyaan yang sering ditanyakan')" 
                                class="flex items-center gap-2 p-2.5 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all text-xs">
                            <span class="text-base">❓</span>
                            <span class="text-gray-700 dark:text-gray-300">FAQ Umum</span>
                        </button>
                        <button @click="sendQuickPrompt('Jelaskan tentang AD/ART koperasi')" 
                                class="flex items-center gap-2 p-2.5 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all text-xs">
                            <span class="text-base">📜</span>
                            <span class="text-gray-700 dark:text-gray-300">AD/ART</span>
                        </button>
                        <button @click="sendQuickPrompt('Apa saja tugas dan wewenang pengurus koperasi?')" 
                                class="flex items-center gap-2 p-2.5 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all text-xs">
                            <span class="text-base">👥</span>
                            <span class="text-gray-700 dark:text-gray-300">Tugas Pengurus</span>
                        </button>
                    </div>
                </div>
            </template>

            {{-- Chat Messages --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.role === 'user' 
                            ? 'bg-purple-500 text-white rounded-2xl rounded-br-md' 
                            : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white rounded-2xl rounded-bl-md shadow'"
                         class="max-w-[80%] px-4 py-3">
                        <p class="text-sm whitespace-pre-wrap" x-text="msg.content"></p>
                        <p class="text-[10px] mt-1 opacity-60" x-text="msg.time"></p>
                    </div>
                </div>
            </template>

            {{-- Typing Indicator --}}
            <div x-show="isTyping" class="flex justify-start">
                <div class="bg-white dark:bg-gray-700 rounded-2xl rounded-bl-md shadow px-4 py-3 flex items-center gap-2">
                    <div class="flex gap-1">
                        <span class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                    <span class="text-xs text-gray-500">AI sedang mengetik...</span>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input x-model="userInput" 
                       type="text" 
                       placeholder="Ketik pesan..." 
                       class="flex-1 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 text-sm focus:border-purple-500 focus:ring focus:ring-purple-200"
                       :disabled="isTyping">
                <button type="submit" 
                        :disabled="!userInput.trim() || isTyping"
                        class="px-4 py-2.5 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-xl font-medium shadow-lg shadow-purple-500/30 hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-2 text-center">Powered by <span x-text="provider.charAt(0).toUpperCase() + provider.slice(1)"></span> • <span x-text="selectedModel"></span></p>
        </div>
    </div>
</div>

<script>
function aiAssistant() {
    return {
        isOpen: false,
        userInput: '',
        messages: [],
        isTyping: false,
        isFullscreen: false,
        selectedModel: '{{ $aiConfig['model'] }}',
        availableModels: ['{{ $aiConfig['model'] }}'],
        connectionStatus: 'Menghubungkan...',
        provider: '{{ $aiConfig['provider'] }}',
        ollamaUrl: '{{ $aiConfig['url'] }}',
        apiKey: '{{ $aiConfig['apiKey'] }}',
        systemPrompt: `{{ addslashes($aiConfig['systemPrompt']) }}`,
        
        init() {
            this.checkConnection();
            this.loadHistory();
        },
        
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            }
        },
        
        async checkConnection() {
            try {
                // Use Laravel backend to test connection
                const response = await fetch('{{ route("ai.test.public") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        provider: this.provider,
                        url: this.ollamaUrl,
                        api_key: this.apiKey
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    this.connectionStatus = '🟢 Terhubung';
                    if (data.models && data.models.length > 0) {
                        this.availableModels = data.models;
                    }
                } else {
                    this.connectionStatus = '🔴 ' + (data.message || 'Tidak terhubung');
                }
            } catch (error) {
                this.connectionStatus = '🔴 Tidak terhubung';
            }
        },
        
        async sendMessage() {
            if (!this.userInput.trim() || this.isTyping) return;
            
            const userMessage = this.userInput.trim();
            this.messages.push({
                role: 'user',
                content: userMessage,
                time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
            });
            this.userInput = '';
            this.isTyping = true;
            this.scrollToBottom();
            
            try {
                // Use Laravel proxy to avoid CORS issues
                const response = await fetch('{{ route("ai.chat.public") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: userMessage })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.messages.push({
                        role: 'assistant',
                        content: data.response,
                        time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                    });
                    this.connectionStatus = '🟢 Terhubung';
                } else {
                    throw new Error(data.error || 'Unknown error');
                }
                
                this.saveHistory();
                
            } catch (error) {
                this.messages.push({
                    role: 'assistant',
                    content: `⚠️ Error: ${error.message}`,
                    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                });
                this.connectionStatus = '🔴 Error';
            }
            
            this.isTyping = false;
            this.scrollToBottom();
        },
        
        buildPrompt(message) {
            return `${this.systemPrompt}\n\nUser: ${message}\nAssistant:`;
        },
        
        clearChat() {
            this.messages = [];
            localStorage.removeItem('aiChatHistory');
        },
        
        sendQuickPrompt(prompt) {
            this.userInput = prompt;
            this.sendMessage();
        },
        
        saveHistory() {
            localStorage.setItem('aiChatHistory', JSON.stringify(this.messages.slice(-20)));
        },
        
        loadHistory() {
            const saved = localStorage.getItem('aiChatHistory');
            if (saved) {
                this.messages = JSON.parse(saved);
            }
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.messagesContainer) {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                }
            });
        }
    }
}
</script>
@endif
