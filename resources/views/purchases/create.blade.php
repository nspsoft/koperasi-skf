@extends('layouts.app')

@section('title', __('messages.purchases.create_title'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('purchases.index') }}" class="btn-secondary-sm">
            {{ __('messages.purchases.show_back') }}
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.purchases.create_subtitle') }}</h1>
    </div>

    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm" class="space-y-4" enctype="multipart/form-data">
        @csrf
        
        <!-- Transaction Details Card -->
        <div class="glass-card p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Row 1: Basic Info -->
                <div>
                    <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.purchases.create_ref_no') }}</label>
                    <input type="text" name="reference_number" class="form-input bg-gray-100 cursor-not-allowed" value="{{ old('reference_number', $poNumber) }}" readonly>
                </div>
                <div>
                    <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.purchases.create_date') }}</label>
                    <input type="date" name="purchase_date" class="form-input" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.purchases.create_supplier') }} <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="supplier_id" class="form-input flex-1" required>
                            <option value="">{{ __('messages.purchases.create_select_supplier') }}</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                            @endforeach
                        </select>
                        <a href="{{ route('suppliers.create') }}" class="btn-secondary-sm shrink-0" title="Add Supplier" target="_blank">
                            +
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <!-- Row 2: Notes & Upload -->
                <div>
                    <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.purchases.show_notes') }}</label>
                    <textarea name="note" rows="1" class="form-input resize-none" placeholder="Catatan tambahan (Opsional)...">{{ old('note') }}</textarea>
                </div>
                <div>
                    <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">Upload Struk / Nota</label>
                    <input type="file" name="receipt_image" class="form-input file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" accept="image/*">
                    <p class="text-[10px] text-gray-500 mt-1">Format: JPG, PNG. Max: 2MB.</p>
                </div>
            </div>
        </div>

        <!-- Barcode Scanner -->
        <div class="glass-card p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-800 rounded-full text-blue-600 dark:text-blue-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
                <div class="flex-1">
                    <label class="form-label mb-1">{{ __('messages.purchases.create_scan_label') }}</label>
                    <input type="text" id="barcodeInput" class="form-input text-lg font-mono focus:ring-blue-500" placeholder="{{ __('messages.purchases.create_scan_placeholder') }}" autofocus>
                    <p class="text-xs text-gray-500 mt-1">{{ __('messages.purchases.create_scan_help') }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="glass-card overflow-hidden">
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 dark:text-white">{{ __('messages.purchases.show_items_title') }}</h3>
                <button type="button" onclick="addItem()" class="btn-primary-sm">
                    {{ __('messages.purchases.create_add_manual') }}
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="table-modern w-full" id="itemsTable">
                    <thead>
                        <tr>
                            <th style="width: 45%">{{ __('messages.purchases.create_table_product') }}</th>
                            <th style="width: 10%">{{ __('messages.purchases.create_table_qty') }}</th>
                            <th style="width: 10%">{{ __('messages.purchases.create_table_buy_unit') }}</th>
                            <th style="width: 15%">{{ __('messages.purchases.create_table_cost_unit') }}</th>
                            <th style="width: 15%" class="text-right">{{ __('messages.purchases.create_table_subtotal') }}</th>
                            <th style="width: 5%"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <!-- Items will be added here via JS -->
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <td colspan="4" class="text-right font-bold py-3 px-4">TOTAL</td>
                            <td class="text-right font-bold py-3 px-4 text-primary-600 text-lg" id="grandTotal">Rp 0</td>
                            <td colspan="1"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('purchases.index') }}" class="btn-secondary">{{ __('messages.purchases.create_btn_cancel') }}</a>
            <button type="submit" class="btn-primary">{{ __('messages.purchases.create_btn_save') }}</button>
        </div>
    </form>
</div>

<!-- Template for Row -->
<template id="itemRowTemplate">
    <tr class="item-row">
        <td class="p-2">
            <select name="items[INDEX][product_id]" class="form-input product-select" required onchange="updateProductPrice(this)">
                <option value="">{{ __('messages.purchases.create_select_product') }}</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" 
                        data-cost="{{ $product->cost }}" 
                        data-code="{{ $product->code }}"
                        data-purchase-unit="{{ $product->purchase_unit ?? 'pcs' }}"
                        data-conversion="{{ $product->conversion_factor ?? 1 }}"
                        data-sale-unit="{{ $product->unit ?? 'pcs' }}">
                    {{ $product->name }} ({{ $product->code }})
                </option>
                @endforeach
            </select>
        </td>
        <td class="p-2">
            <input type="number" name="items[INDEX][quantity]" class="form-input qty-input" min="1" value="1" required oninput="calculateRow(this)">
        </td>
        <td class="p-2 text-center">
            <span class="purchase-unit-display text-sm font-medium text-gray-600 dark:text-gray-400">-</span>
        </td>
        <td class="p-2">
            <input type="number" name="items[INDEX][cost]" class="form-input cost-input" min="0" value="0" required oninput="calculateRow(this)">
        </td>
        <td class="p-2 text-right font-medium subtotal-display">
            Rp 0
        </td>
        <td class="p-2 text-center">
            <span class="stock-add-display text-xs font-bold text-green-600">+0</span>
            <span class="sale-unit-display text-xs text-gray-500"></span>
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
    let itemIndex = 0;
    
    // Create a map of product codes to IDs for faster lookup
    const productsMap = {
        @foreach($products as $product)
        "{{ $product->code }}": "{{ $product->id }}",
        @endforeach
    };

    function addItem(productId = null) {
        const template = document.getElementById('itemRowTemplate');
        const clone = template.content.cloneNode(true);
        const tbody = document.getElementById('itemsBody');
        
        // Update names with unique index
        clone.querySelectorAll('[name*="INDEX"]').forEach(el => {
            el.name = el.name.replace('INDEX', itemIndex);
        });
        
        // Set product if provided via scan
        if (productId) {
            const select = clone.querySelector('.product-select');
            select.value = productId;
            // We need to trigger updateProductPrice significantly after append
            // but setting value here is safe before append
        }

        tbody.appendChild(clone);
        
        // Trigger calc and price update if product set
        if (productId) {
            const lastRow = tbody.lastElementChild;
            const select = lastRow.querySelector('.product-select');
            updateProductPrice(select);
        }

        itemIndex++;
        calculateTotal();
    }

    function removeItem(btn) {
        btn.closest('tr').remove();
        calculateTotal();
    }

    function updateProductPrice(select) {
        const option = select.selectedOptions[0];
        const cost = option.getAttribute('data-cost') || 0;
        const purchaseUnit = option.getAttribute('data-purchase-unit') || 'pcs';
        const saleUnit = option.getAttribute('data-sale-unit') || 'pcs';
        const conversion = parseInt(option.getAttribute('data-conversion')) || 1;
        const row = select.closest('tr');
        
        row.querySelector('.cost-input').value = cost;
        row.querySelector('.purchase-unit-display').textContent = purchaseUnit.toUpperCase();
        row.querySelector('.sale-unit-display').textContent = saleUnit;
        
        // Store conversion factor in row for later calculation
        row.dataset.conversion = conversion;
        row.dataset.saleUnit = saleUnit;
        
        calculateRow(select);
    }

    function calculateRow(input) {
        const row = input.closest('tr');
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
        const subtotal = qty * cost;
        const conversion = parseInt(row.dataset.conversion) || 1;
        const saleUnit = row.dataset.saleUnit || 'pcs';

        row.querySelector('.subtotal-display').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
        
        // Calculate and display stock impact
        const stockAdd = qty * conversion;
        row.querySelector('.stock-add-display').textContent = '+' + stockAdd;
        row.querySelector('.sale-unit-display').textContent = ' ' + saleUnit;
        
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
            total += (qty * cost);
        });

        document.getElementById('grandTotal').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }
    
    // Barcode Scanner Logic
    document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const barcode = this.value.trim();
            if (!barcode) return;
            
            const productId = productsMap[barcode];
            
            if (productId) {
                // Check if product needed to check if already exists
                let existingRow = null;
                document.querySelectorAll('.product-select').forEach(select => {
                    if (select.value === productId) {
                        existingRow = select.closest('tr');
                    }
                });
                
                if (existingRow) {
                    // Increment qty
                    const qtyInput = existingRow.querySelector('.qty-input');
                    qtyInput.value = parseInt(qtyInput.value) + 1;
                    calculateRow(qtyInput);
                    
                    // Flash row to indicate update
                    existingRow.classList.add('bg-green-50', 'dark:bg-green-900/20');
                    setTimeout(() => {
                        existingRow.classList.remove('bg-green-50', 'dark:bg-green-900/20');
                    }, 500);
                } else {
                    // Add new row
                    addItem(productId);
                }
                
                // Success feedback
                this.value = '';
                // Optional: Play sound or visual indicator
            } else {
                let msg = "{{ __('messages.purchases.barcode_not_found', ['barcode' => 'BARCODE']) }}";
                alert(msg.replace('BARCODE', barcode));
                this.select();
            }
        }
    });

    // Add initial row if empty
    document.addEventListener('DOMContentLoaded', () => {
        // addItem(); // Don't add empty row automatically if we want scan-first workflow, 
                     // but user might want manual input. Let's keep it clean for scanning, 
                     // or maybe add empty row only if no items.
                     // Let's NOT add initial row to keep it clean for scanner users.
    });
</script>
@endpush
@endsection
