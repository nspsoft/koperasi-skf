{{-- 
    Delete Confirmation Modal with Related Data Warning
    
    Usage:
    <x-delete-confirm-modal 
        action="{{ route('resource.destroy', $item) }}" 
        title="Hapus Data"
        :related-data="['Jurnal Keuangan terkait akan ikut terhapus', 'Data tidak dapat dikembalikan']"
    />
--}}

@props([
    'action' => '',
    'method' => 'DELETE',
    'title' => 'Konfirmasi Hapus',
    'message' => 'Apakah Anda yakin ingin menghapus data ini?',
    'relatedData' => [],
    'buttonText' => 'Hapus',
    'buttonClass' => 'bg-red-600 hover:bg-red-700',
    'modalId' => 'deleteModal-' . uniqid()
])

<div x-data="{ open: false }" x-cloak>
    <!-- Trigger Button -->
    <button type="button" 
            @click="open = true"
            {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition dark:text-red-400 dark:hover:bg-red-900/30']) }}>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        {{ $slot->isNotEmpty() ? $slot : 'Hapus' }}
    </button>

    <!-- Modal Backdrop -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        
        <!-- Modal Content -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="open = false"
             class="w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Header with Warning Icon -->
            <div class="bg-gradient-to-r from-red-500 to-orange-500 p-6 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white">{{ $title }}</h3>
            </div>

            <!-- Body -->
            <div class="p-6">
                <p class="text-gray-600 dark:text-gray-300 text-center mb-4">{{ $message }}</p>
                
                @if(count($relatedData) > 0)
                <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-xl p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">Data Terkait yang Akan Terpengaruh:</h4>
                            <ul class="text-sm text-amber-700 dark:text-amber-300 space-y-1">
                                @foreach($relatedData as $item)
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $item }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex justify-end gap-3">
                <button type="button" 
                        @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-550 transition">
                    Batal
                </button>
                <form action="{{ $action }}" method="POST" class="inline">
                    @csrf
                    @method($method)
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white {{ $buttonClass }} rounded-lg shadow-sm hover:shadow-md transition">
                        {{ $buttonText }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
