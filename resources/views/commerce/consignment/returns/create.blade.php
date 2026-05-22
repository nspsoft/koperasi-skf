@extends('layouts.app')

@section('title', 'Buat Retur Konsinyasi')

@section('content')
<div x-data="returnForm()" class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Retur Konsinyasi</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Kembalikan sisa barang konsinyasi ke Mitra/Supplier</p>
        </div>
        <a href="{{ route('consignment.returns.index') }}" class="btn-secondary">
            Batal & Kembali
        </a>
    </div>

    <form action="{{ route('consignment.returns.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Info Retur --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Informasi Mitra</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Retur *</label>
                    <input type="date" name="return_date" value="{{ date('Y-m-d') }}" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Mitra/Supplier *</label>
                    <select x-model="selectedConsignor" @change="updateProducts()" name="consignor_data" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 select2">
                        <option value="">-- Pilih Mitra --</option>
                        <optgroup label="Anggota (Member)">
                            @foreach($members as $member)
                                <option value="member_{{ $member->id }}">{{ $member->name }} ({{ $member->member_id }})</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Supplier Eksternal">
                            @foreach($suppliers as $supplier)
                                <option value="supplier_{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                    <input type="hidden" name="consignor_type" x-model="consignorType">
                    <input type="hidden" name="consignor_id" x-model="consignorId">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" placeholder="Alasan retur..." class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"></textarea>
                </div>
            </div>
        </div>

        {{-- Items --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Barang yang Diretur</h2>
                <button type="button" @click="addItem()" class="btn-primary text-sm py-1.5 px-3">
                    + Tambah Baris
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="item.id">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        
                        <div class="w-full" style="flex: 3;">
                            <label class="block text-xs font-medium text-gray-500 mb-1 md:hidden">Produk</label>
                            <select :name="'items['+index+'][product_id]'" x-model="item.product_id" @change="checkStock(index)" required class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <option value="">-- Pilih Produk --</option>
                                <template x-for="prod in availableProducts" :key="prod.id">
                                    <option :value="prod.id" x-text="prod.name + ' (Stok: ' + prod.stock + ')'" :disabled="prod.stock <= 0"></option>
                                </template>
                            </select>
                        </div>

                        <div class="w-full md:w-24">
                            <label class="block text-xs font-medium text-gray-500 mb-1 md:hidden">Qty Retur</label>
                            <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" @input="checkStock(index)" required min="1" placeholder="Qty"
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-center">
                        </div>
                        
                        <div class="w-full md:w-40">
                            <label class="block text-xs font-medium text-gray-500 mb-1 md:hidden">Keterangan</label>
                            <input type="text" :name="'items['+index+'][notes]'" x-model="item.notes" placeholder="Kondisi barang..."
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        </div>

                        <div class="w-full md:w-auto flex justify-end">
                            <button type="button" @click="removeItem(index)" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg" title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                        
                    </div>
                    {{-- Warning message --}}
                    <div x-show="item.error" class="text-red-500 text-xs mt-1" x-text="item.error"></div>
                </template>

                <div x-show="items.length === 0" class="text-center py-6 text-gray-500 dark:text-gray-400">
                    Belum ada barang yang ditambahkan.
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <div class="text-lg font-bold">Total Barang Diretur: <span x-text="totalQty" class="text-blue-600 dark:text-blue-400"></span> pcs</div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('consignment.returns.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary" :disabled="items.length === 0 || hasErrors">
                Simpan & Kurangi Stok
            </button>
        </div>
    </form>
</div>

<script>
    const allProducts = @json($consignmentProducts);
    
    function returnForm() {
        return {
            selectedConsignor: '',
            consignorType: '',
            consignorId: '',
            availableProducts: [],
            items: [],
            counter: 0,
            
            updateProducts() {
                if (!this.selectedConsignor) {
                    this.consignorType = '';
                    this.consignorId = '';
                    this.availableProducts = [];
                    this.items = [];
                    return;
                }
                
                const parts = this.selectedConsignor.split('_');
                this.consignorType = parts[0];
                this.consignorId = parts[1];
                
                // Filter products that belong to this consignor
                this.availableProducts = allProducts.filter(p => 
                    p.consignor_type === this.consignorType && 
                    p.consignor_id == this.consignorId
                );
                
                this.items = []; // reset items when consignor changes
                if(this.availableProducts.length > 0) {
                    this.addItem();
                }
            },
            
            addItem() {
                this.items.push({
                    id: this.counter++,
                    product_id: '',
                    quantity: 1,
                    notes: '',
                    error: ''
                });
            },
            
            removeItem(index) {
                this.items.splice(index, 1);
            },
            
            checkStock(index) {
                const item = this.items[index];
                if (!item.product_id) return;
                
                const product = this.availableProducts.find(p => p.id == item.product_id);
                if (product) {
                    if (item.quantity > product.stock) {
                        item.error = `Jumlah retur (${item.quantity}) melebihi stok yang ada (${product.stock})`;
                    } else {
                        item.error = '';
                    }
                }
            },
            
            get totalQty() {
                return this.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
            },
            
            get hasErrors() {
                return this.items.some(item => item.error !== '') || this.items.some(item => item.product_id === '' || item.quantity < 1);
            }
        }
    }
</script>
@endsection
