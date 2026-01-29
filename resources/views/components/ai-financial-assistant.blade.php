@php
    $config = \App\Models\AiSetting::getConfig();
@endphp

@if($config['enabled'] && auth()->user()->hasAdminAccess())
<div x-data="aiFinancialAssistant()" 
     class="fixed bottom-24 right-6 z-50 flex flex-col items-end"
     x-cloak>
    
    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-90 translate-y-12 rotate-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0 rotate-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0 rotate-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-8 rotate-1"
         :style="isFullscreen 
             ? 'position: fixed; top: 16px; left: 16px; right: 16px; bottom: 16px; width: auto; max-width: none; z-index: 9999; margin: 0;' 
             : ''"
         class="mb-4 w-80 md:w-96 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.2)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.4)] border border-white/20 dark:border-gray-700/50 flex flex-col overflow-hidden transition-all duration-300"
         :class="!isFullscreen ? 'max-h-[500px]' : ''">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-4 text-white flex items-center justify-between shadow-lg relative overflow-hidden">
            <!-- Header Glow -->
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-24 h-24 bg-white/20 rounded-full blur-2xl"></div>
            
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-9 h-9 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-xl shadow-inner animate-bounce" style="animation-duration: 3s;">🤖</div>
                <div>
                    <h3 class="font-bold text-sm tracking-tight text-white">Financial AI Assistant</h3>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                        <p class="text-[10px] text-purple-100 font-medium">Analisa Keuangan Real-time</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 relative z-10">
                <button type="button" @click="isFullscreen = !isFullscreen" class="text-white/80 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5" :title="isFullscreen ? 'Minimize' : 'Fullscreen'">
                    <svg x-show="!isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                    <svg x-show="isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <button type="button" @click="resetChat()" class="text-white/80 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5" title="Percakapan Baru">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
                <button type="button" @click="isOpen = false" class="text-white/80 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Chat History -->
        <!-- Chat History -->
        <div id="ai-chat-messages" 
             class="flex-1 p-4 overflow-y-auto space-y-4 bg-gray-50/30 dark:bg-gray-900/40 custom-scrollbar"
             :class="!isFullscreen ? 'min-h-[300px]' : ''">
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'"
                     x-show="true"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                    <div :class="msg.role === 'user' ? 'bg-gradient-to-br from-purple-600 to-indigo-600 text-white rounded-2xl rounded-tr-none shadow-lg shadow-purple-500/20' : (msg.isError ? 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 shadow-sm' : 'bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-gray-200 rounded-2xl rounded-tl-none border border-white/50 dark:border-gray-700/50 shadow-sm backdrop-blur-sm')"
                         class="max-w-[85%] p-3 text-sm leading-relaxed rounded-2xl">
                        <div class="flex items-start gap-2">
                            <span x-html="msg.content.replace(/\n/g, '<br>')"></span>
                        </div>
                        <template x-if="msg.isError">
                            <button @click="retryMessage(msg.retryPayload)" class="mt-2 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider bg-red-100 dark:bg-red-800/40 px-2 py-1 rounded-lg hover:bg-red-200 dark:hover:bg-red-800/60 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Coba Lagi
                            </button>
                        </template>
                    </div>
                </div>
            </template>
            <div x-show="isLoading" class="flex justify-start animate-pulse">
                <div class="bg-white/90 dark:bg-gray-800/90 p-3 rounded-2xl rounded-tl-none border border-white/50 dark:border-gray-700/50 shadow-sm backdrop-blur-sm">
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-1.5 items-center">
                            <div class="h-1.5 w-1.5 bg-purple-500 rounded-full animate-bounce"></div>
                            <div class="h-1.5 w-1.5 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            <div class="h-1.5 w-1.5 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                        </div>
                        <span class="text-[10px] text-gray-400 font-medium">Berpikir...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 border-t border-white/20 dark:border-gray-700/50 bg-white/50 dark:bg-gray-800/50 backdrop-blur-md">
            <div class="grid grid-cols-2 gap-2 mb-3 px-1" x-show="messages.length === 1">
                <button @click="sendMessage('Analisa kesehatan keuangan koperasi bulan ini')" 
                        class="text-[10px] p-2 bg-purple-500/10 dark:bg-purple-400/10 text-purple-700 dark:text-purple-300 rounded-xl hover:bg-purple-500/20 dark:hover:bg-purple-400/20 transition-all text-left font-semibold border border-purple-500/20">
                    📊 Analisa Keuangan
                </button>
                <button @click="sendMessage('Bagaimana performa Koperasi Mart?')" 
                        class="text-[10px] p-2 bg-indigo-500/10 dark:bg-indigo-400/10 text-indigo-700 dark:text-indigo-300 rounded-xl hover:bg-indigo-500/20 dark:hover:bg-indigo-400/20 transition-all text-left font-semibold border border-indigo-500/20">
                    🛒 Performa Mart
                </button>
            </div>
            <form @submit.prevent="sendMessage()" class="flex gap-2 relative">
                <input type="text" 
                       x-model="userInput" 
                       placeholder="Tanya asisten keuangan..."
                       class="flex-1 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-purple-500 transition-all dark:text-white"
                       :disabled="isLoading">
                <button type="submit" 
                        :disabled="isLoading || !userInput.trim()"
                        class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-3 rounded-xl hover:shadow-lg hover:shadow-purple-500/40 disabled:opacity-50 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Floating Button -->
    <button @click="toggleChat()" 
            class="ai-pulse-button flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-600 to-indigo-700 text-white rounded-full shadow-[0_10px_30px_rgba(147,51,234,0.4)] transition-all duration-500 transform hover:scale-110 active:scale-95 focus:outline-none ring-offset-2 focus:ring-4 focus:ring-purple-300 group">
        <svg class="w-8 h-8 transition-all duration-300 group-hover:rotate-12" :class="isOpen ? 'hidden' : 'block'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        <svg class="w-7 h-7" :class="isOpen ? 'block' : 'hidden'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        
        <!-- Badge -->
        <span class="absolute -top-1 -right-1 flex h-5 w-5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-5 w-5 bg-gradient-to-r from-purple-500 to-indigo-500 text-[10px] items-center justify-center font-bold border-2 border-white dark:border-gray-800 shadow-sm leading-none">AI</span>
        </span>
    </button>
</div>

<style>
@keyframes ai-pulse-glow {
    0% { box-shadow: 0 0 0 0 rgba(147, 51, 234, 0.5); }
    70% { box-shadow: 0 0 0 15px rgba(147, 51, 234, 0); }
    100% { box-shadow: 0 0 0 0 rgba(147, 51, 234, 0); }
}
.ai-pulse-button:not(:active) {
    animation: ai-pulse-glow 2s infinite;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(147, 51, 234, 0.2);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(147, 51, 234, 0.4);
}
</style>
</div>

<script>
function aiFinancialAssistant() {
    return {
        isOpen: false,
        userInput: '',
        isLoading: false,
        isFullscreen: false,
        messages: [
            { role: 'assistant', content: 'Halo Admin! Saya asisten finansial Anda. Saya sudah memuat data keuangan koperasi terbaru. Apa yang ingin Anda analisa hari ini?' }
        ],

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.scrollToBottom();
            }
        },

        resetChat() {
            if (confirm('Mulai percakapan baru? Semua riwayat chat saat ini akan dihapus.')) {
                this.messages = [
                    { role: 'assistant', content: 'Halo Admin! Saya asisten finansial Anda. Saya sudah memuat data keuangan koperasi terbaru. Apa yang ingin Anda analisa hari ini?' }
                ];
                this.userInput = '';
                this.isLoading = false;
            }
        },

        retryMessage(payload) {
            if (this.messages.length > 0 && this.messages[this.messages.length - 1].isError) {
                this.messages.pop();
            }
            this.sendMessage(payload);
        },

        async sendMessage(text = null) {
            const content = text || this.userInput.trim();
            if (!content || (this.isLoading && !text)) return;

            if (!text) this.userInput = '';
            
            this.messages.push({ role: 'user', content: content });
            this.isLoading = true;
            this.scrollToBottom();

            try {
                const response = await fetch('{{ route("ai.chat.public") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: content })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.messages.push({ role: 'assistant', content: data.response });
                } else {
                    this.messages.push({ 
                        role: 'assistant', 
                        content: 'Maaf, terjadi kesalahan: ' + (data.error || 'Server error'),
                        isError: true,
                        retryPayload: content
                    });
                }
            } catch (error) {
                this.messages.push({ 
                    role: 'assistant', 
                    content: 'Gagal terhubung ke server AI. Pastikan provider (seperti Ollama) sudah berjalan.',
                    isError: true,
                    retryPayload: content
                });
            } finally {
                this.isLoading = false;
                this.scrollToBottom();
            }
        },

        scrollToBottom() {
            setTimeout(() => {
                const chat = document.getElementById('ai-chat-messages');
                if (chat) chat.scrollTop = chat.scrollHeight;
            }, 50);
        }
    }
}
</script>
@endif
