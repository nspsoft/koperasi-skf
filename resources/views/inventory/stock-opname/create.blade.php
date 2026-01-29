@extends('layouts.app')

@section('title', __('messages.stock_opname.title'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="stockOpnameForm()">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('stock-opname.index') }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.stock_opname.title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.stock_opname.subtitle') }}</p>
        </div>
    </div>

    <form action="{{ route('stock-opname.store') }}" method="POST">
        @csrf
        
        <!-- Info Section -->
        <div class="glass-card-solid p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="form-label">{{ __('messages.stock_opname.opname_date') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="opname_date" value="{{ old('opname_date', date('Y-m-d')) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">{{ __('messages.stock_opname.notes') }}</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" class="form-input" placeholder="{{ __('messages.stock_opname.notes_placeholder') }}">
                </div>
            </div>
        </div>

        <!-- Product Selection -->
        <div class="glass-card-solid p-6 mb-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                {{ __('messages.stock_opname.select_products') }}
            </h3>

            <!-- Scan Mode Toggle -->
            <div class="flex items-center gap-4 mb-4 p-4 rounded-xl" :class="scanMode ? 'bg-green-50 dark:bg-green-900/20 border-2 border-green-500' : 'bg-gray-50 dark:bg-gray-800'">
                <button type="button" @click="toggleScanMode()" 
                        class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-all"
                        :class="scanMode ? 'bg-green-600 text-white shadow-lg shadow-green-500/30' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 hover:bg-gray-100'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span x-text="scanMode ? '{{ __('messages.stock_opname.scan_mode_active') }}' : '{{ __('messages.stock_opname.activate_scan') }}'"></span>
                </button>
                
                <div x-show="scanMode" x-transition class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               x-ref="scanInput"
                               @keydown.enter.prevent="handleScan($event.target.value); $event.target.value = ''"
                               class="form-input pl-10 pr-4 py-3 text-lg font-mono border-2 border-green-500 focus:ring-green-500"
                               placeholder="{{ __('messages.stock_opname.scan_placeholder') }}"
                               autofocus>
                        <div class="absolute inset-y-0 left-3 flex items-center">
                            <svg class="w-5 h-5 text-green-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            {{ __('messages.stock_opname.scan_help') }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Scan Feedback -->
            <div x-show="scanFeedback.show" x-transition
                 class="mb-4 p-3 rounded-lg flex items-center gap-3"
                 :class="scanFeedback.type === 'success' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'">
                <svg x-show="scanFeedback.type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <svg x-show="scanFeedback.type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span x-text="scanFeedback.message" class="font-medium"></span>
            </div>

            <!-- Quick Add (Manual Selection) -->
            <div class="flex gap-2 mb-4" x-show="!scanMode">
                <select x-model="selectedProduct" class="form-input flex-1">
                    <option value="">{{ __('messages.stock_opname.select_product_placeholder') }}</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-code="{{ $product->code }}" data-stock="{{ $product->stock }}">
                        {{ $product->code }} - {{ $product->name }} (Stok: {{ $product->stock }})
                    </option>
                    @endforeach
                </select>
                <button type="button" @click="addProduct()" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    {{ __('messages.stock_opname.add_btn') }}
                </button>
                <button type="button" @click="addAllProducts()" class="btn-secondary">
                    {{ __('messages.stock_opname.add_all_btn') }}
                </button>
            </div>

            <!-- Last Scanned Info -->
            <div x-show="lastScanned" x-transition class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    <span class="font-medium">{{ __('messages.stock_opname.last_scanned') }}</span> 
                    <span x-text="lastScanned?.code" class="font-mono"></span> - 
                    <span x-text="lastScanned?.name"></span>
                    <span class="text-blue-500">({{ __('messages.stock_opname.system_stock') }}: <span x-text="lastScanned?.stock"></span>)</span>
                </p>
            </div>

            <!-- Items Table -->
            <div class="table-scroll-container" x-show="items.length > 0">
                <table class="table-modern w-full">
                    <thead class="sticky top-0 z-10">
                        <tr>
                            <th>No</th>
                            <th>{{ __('messages.stock_opname.code') }}</th>
                            <th>{{ __('messages.stock_opname.product_name') }}</th>
                            <th class="text-center">{{ __('messages.stock_opname.system_stock') }}</th>
                            <th class="text-center">{{ __('messages.stock_opname.actual_stock') }}</th>
                            <th class="text-center">{{ __('messages.stock_opname.difference') }}</th>
                            <th>{{ __('messages.stock_opname.notes') }}</th>
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="item.product_id">
                            <tr :class="item.justAdded ? 'bg-green-50 dark:bg-green-900/20 animate-pulse' : ''">
                                <td x-text="index + 1"></td>
                                <td class="font-mono text-xs" x-text="item.code"></td>
                                <td class="font-medium" x-text="item.name"></td>
                                <td class="text-center">
                                    <span class="badge badge-info" x-text="item.system_stock"></span>
                                </td>
                                <td class="text-center">
                                    <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id">
                                    <input type="number" 
                                           :name="'items[' + index + '][actual_stock]'" 
                                           x-model.number="item.actual_stock" 
                                           @input="calculateDifference(index)"
                                           class="form-input w-24 text-center" 
                                           min="0" 
                                           required>
                                </td>
                                <td class="text-center">
                                    <span class="badge" 
                                          :class="{
                                              'badge-success': item.difference > 0,
                                              'badge-danger': item.difference < 0,
                                              'badge-gray': item.difference === 0
                                          }"
                                          x-text="(item.difference > 0 ? '+' : '') + item.difference">
                                    </span>
                                </td>
                                <td>
                                    <input type="text" 
                                           :name="'items[' + index + '][notes]'" 
                                           x-model="item.notes"
                                           class="form-input text-xs" 
                                           placeholder="{{ __('messages.stock_opname.item_notes_placeholder') }}">
                                </td>
                                <td class="text-center">
                                    <button type="button" @click="removeItem(index)" class="btn-icon text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div x-show="items.length === 0" class="text-center py-12 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <p x-show="!scanMode">{{ __('messages.stock_opname.no_products') }}</p>
                <p x-show="scanMode">{{ __('messages.stock_opname.no_products_scan') }}</p>
            </div>
        </div>

        <!-- Summary & Submit -->
        <div class="glass-card-solid p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex gap-6">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="items.length">0</p>
                        <p class="text-xs text-gray-500">{{ __('messages.stock_opname.total_items') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600" x-text="items.filter(i => i.difference > 0).length">0</p>
                        <p class="text-xs text-gray-500">{{ __('messages.stock_opname.surplus') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-red-600" x-text="items.filter(i => i.difference < 0).length">0</p>
                        <p class="text-xs text-gray-500">{{ __('messages.stock_opname.deficit') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-600" x-text="items.filter(i => i.difference === 0).length">0</p>
                        <p class="text-xs text-gray-500">{{ __('messages.stock_opname.match') }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('stock-opname.index') }}" class="btn-secondary">{{ __('messages.cancel') }}</a>
                    <button type="submit" class="btn-primary" :disabled="items.length === 0">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('messages.stock_opname.save_draft') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function stockOpnameForm() {
    return {
        selectedProduct: '',
        items: [],
        products: @json($products),
        scanMode: false,
        scanFeedback: { show: false, type: 'success', message: '' },
        lastScanned: null,

        toggleScanMode() {
            this.scanMode = !this.scanMode;
            if (this.scanMode) {
                this.$nextTick(() => {
                    this.$refs.scanInput?.focus();
                });
            }
        },

        handleScan(barcode) {
            if (!barcode || !barcode.trim()) return;
            
            barcode = barcode.trim().toUpperCase();
            
            // Search by code or barcode
            const product = this.products.find(p => 
                p.code?.toUpperCase() === barcode || 
                p.barcode?.toUpperCase() === barcode
            );
            
            if (!product) {
                this.showFeedback('error', '{{ __('messages.stock_opname.product_not_found', ['barcode' => '']) }}'.replace(':barcode', barcode));
                this.playSound('error');
                return;
            }
            
            // Check if already added
            if (this.items.find(i => i.product_id == product.id)) {
                this.showFeedback('error', '{{ __('messages.stock_opname.already_added', ['name' => '']) }}'.replace(':name', product.name));
                this.playSound('error');
                return;
            }
            
            // Add product
            this.items.push({
                product_id: product.id,
                code: product.code,
                name: product.name,
                system_stock: product.stock,
                actual_stock: product.stock,
                difference: 0,
                notes: '',
                justAdded: true
            });
            
            // Remove highlight after animation
            setTimeout(() => {
                const item = this.items.find(i => i.product_id == product.id);
                if (item) item.justAdded = false;
            }, 2000);
            
            this.lastScanned = product;
            this.showFeedback('success', '{{ __('messages.stock_opname.added_success', ['code' => 'CODE_PLACEHOLDER', 'name' => 'NAME_PLACEHOLDER']) }}'
                .replace('CODE_PLACEHOLDER', product.code)
                .replace('NAME_PLACEHOLDER', product.name));
            this.playSound('success');
            
            // Refocus scan input
            this.$nextTick(() => {
                this.$refs.scanInput?.focus();
            });
        },

        showFeedback(type, message) {
            this.scanFeedback = { show: true, type, message };
            setTimeout(() => {
                this.scanFeedback.show = false;
            }, 3000);
        },

        playSound(type) {
            // Simple beep sounds using Web Audio API
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                if (type === 'success') {
                    oscillator.frequency.value = 800;
                    oscillator.type = 'sine';
                } else {
                    oscillator.frequency.value = 300;
                    oscillator.type = 'square';
                }
                
                gainNode.gain.value = 0.1;
                oscillator.start();
                oscillator.stop(audioContext.currentTime + 0.15);
            } catch (e) {
                // Audio not available
            }
        },

        addProduct() {
            if (!this.selectedProduct) return;
            
            // Check if already added
            if (this.items.find(i => i.product_id == this.selectedProduct)) {
                alert('{{ __('messages.stock_opname.already_added', ['name' => '']) }}'.replace(':name', ''));
                return;
            }

            const product = this.products.find(p => p.id == this.selectedProduct);
            if (product) {
                this.items.push({
                    product_id: product.id,
                    code: product.code,
                    name: product.name,
                    system_stock: product.stock,
                    actual_stock: product.stock,
                    difference: 0,
                    notes: ''
                });
            }
            this.selectedProduct = '';
        },

        addAllProducts() {
            if (confirm('{{ __('messages.stock_opname.add_all_confirm') }}')) {
                this.products.forEach(product => {
                    if (!this.items.find(i => i.product_id == product.id)) {
                        this.items.push({
                            product_id: product.id,
                            code: product.code,
                            name: product.name,
                            system_stock: product.stock,
                            actual_stock: product.stock,
                            difference: 0,
                            notes: ''
                        });
                    }
                });
            }
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        calculateDifference(index) {
            const item = this.items[index];
            item.difference = item.actual_stock - item.system_stock;
        }
    }
}
</script>
@endsection
