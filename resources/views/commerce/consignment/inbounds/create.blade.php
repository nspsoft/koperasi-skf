@extends('layouts.app')

@section('title', __('messages.consignment.inbound.create_title'))

@section('content')
    <div class="page-header">
        <a href="{{ route('consignment.inbounds.index') }}" class="flex items-center text-gray-500 hover:text-primary-600 mb-4 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('messages.consignment.inbound.back') }}
        </a>
        <h1 class="page-title">{{ __('messages.consignment.inbound.create_title') }}</h1>
    </div>

    <form action="{{ route('consignment.inbounds.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Header Info -->
            <div class="lg:col-span-1 space-y-6">
                <div class="glass-card p-6 space-y-4">
                    <h3 class="font-bold border-b pb-2 mb-4">{{ __('messages.consignment.inbound.section_partner') }}</h3>
                    
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.consignment.inbound.label_date') }}</label>
                        <input type="date" name="inbound_date" class="form-input" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('messages.consignment.inbound.label_type') }}</label>
                        <select name="consignor_type" id="consignor_type" class="form-input" onchange="toggleConsignorType()">
                            <option value="supplier">{{ __('messages.consignment.inbound.type_supplier') }}</option>
                            <option value="member">{{ __('messages.consignment.inbound.type_member') }}</option>
                        </select>
                    </div>

                    <div class="form-group" id="group_supplier">
                        <label class="form-label">{{ __('messages.consignment.inbound.label_supplier') }}</label>
                        <select name="consignor_id" id="consignor_id_supplier" class="form-input select2">
                            <option value="">{{ __('messages.consignment.inbound.select_supplier') }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group hidden" id="group_member">
                        <label class="form-label">{{ __('messages.consignment.inbound.label_member_id') }}</label>
                        <input type="number" name="consignor_id" id="consignor_id_member" class="form-input" placeholder="{{ __('messages.consignment.inbound.placeholder_member_id') }}" disabled>
                        <p class="text-xs text-gray-500 mt-1">{{ __('messages.consignment.inbound.help_member_id') }}</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __('messages.consignment.inbound.label_note') }}</label>
                        <textarea name="note" class="form-input" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <!-- Right: Items -->
            <div class="lg:col-span-2">
                <div class="glass-card p-6">
                    <h3 class="font-bold border-b pb-2 mb-4 flex justify-between items-center">
                        <span>{{ __('messages.consignment.inbound.section_items') }}</span>
                        <button type="button" onclick="addItem()" class="btn-secondary btn-sm">
                            {{ __('messages.consignment.inbound.btn_add_row') }}
                        </button>
                    </h3>

                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="p-2 text-left">{{ __('messages.consignment.inbound.table_item_product') }}</th>
                                <th class="p-2 text-left w-24">{{ __('messages.consignment.inbound.table_item_qty') }}</th>
                                <th class="p-2 text-left">{{ __('messages.consignment.inbound.table_item_cost') }}</th>
                                <th class="p-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="items_container">
                            <!-- Rows will be added here -->
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 text-right">
                    <button type="submit" class="btn-primary px-8">
                        {{ __('messages.consignment.inbound.btn_save') }}
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Template for JS -->
    <template id="item_row_template">
        <tr>
            <td class="p-2">
                <select name="items[INDEX][product_id]" class="form-input text-sm product-select" onchange="updateProductInfo(this)" required>
                    <option value="">{{ __('messages.consignment.inbound.select_product') }}</option>
                    @foreach($consignmentProducts as $product)
                        <option value="{{ $product->id }}" 
                                data-cost="{{ $product->consignment_price }}"
                                data-consignor-id="{{ $product->consignor_id }}"
                                data-consignor-type="{{ $product->consignor_type }}">
                            {{ $product->code }} - {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                <div class="text-xs text-red-500 hidden mismatch-warning">{{ __('messages.consignment.inbound.error_mismatch') }}</div>
            </td>
            <td class="p-2">
                <input type="number" name="items[INDEX][quantity]" class="form-input text-sm text-center" value="1" min="1" required>
            </td>
            <td class="p-2">
                <input type="text" class="form-input text-sm bg-gray-100 cursor-not-allowed cost-display" readonly>
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
        let rowCount = 0;

        function toggleConsignorType() {
            const type = document.getElementById('consignor_type').value;
            const supplierGroup = document.getElementById('group_supplier');
            const memberGroup = document.getElementById('group_member');
            const supplierInput = document.getElementById('consignor_id_supplier');
            const memberInput = document.getElementById('consignor_id_member');

            if (type === 'supplier') {
                supplierGroup.classList.remove('hidden');
                memberGroup.classList.add('hidden');
                supplierInput.removeAttribute('disabled');
                supplierInput.setAttribute('name', 'consignor_id');
                memberInput.setAttribute('disabled', 'disabled');
                memberInput.removeAttribute('name');
            } else {
                memberGroup.classList.remove('hidden');
                supplierGroup.classList.add('hidden');
                memberInput.removeAttribute('disabled');
                memberInput.setAttribute('name', 'consignor_id');
                supplierInput.setAttribute('disabled', 'disabled');
                supplierInput.removeAttribute('name');
            }
        }

        function addItem() {
            const container = document.getElementById('items_container');
            const template = document.getElementById('item_row_template');
            const clone = template.content.cloneNode(true);
            
            // Replaces INDEX placeholder
            const html = clone.firstElementChild.outerHTML.replace(/INDEX/g, rowCount++);
            // Create a temp div to convert string back to node
            const temp = document.createElement('tbody');
            temp.innerHTML = html;
            
            container.appendChild(temp.firstElementChild);
        }

        function removeItem(btn) {
            btn.closest('tr').remove();
        }

        function updateProductInfo(select) {
            const option = select.options[select.selectedIndex];
            const cost = parseFloat(option.getAttribute('data-cost')) || 0;
            const consignorId = option.getAttribute('data-consignor-id');
            const consignorType = option.getAttribute('data-consignor-type');
            
            const row = select.closest('tr');
            
            // Update Cost Display
            const formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
            row.querySelector('.cost-display').value = formatter.format(cost);

            // Warning Check
            const currentType = document.getElementById('consignor_type').value;
            let currentId = '';
            if (currentType === 'supplier') {
                currentId = document.getElementById('consignor_id_supplier').value;
            } else {
                currentId = document.getElementById('consignor_id_member').value;
            }

            const warning = row.querySelector('.mismatch-warning');
            // Logic: if product has specific owner, warn if mismatch.
            if (consignorId && (consignorId != currentId || consignorType != currentType)) {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }

        // Add first item on load
        document.addEventListener('DOMContentLoaded', () => {
            addItem();
        });
    </script>
    @endpush
@endsection
