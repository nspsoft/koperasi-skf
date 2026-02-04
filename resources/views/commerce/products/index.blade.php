@extends('layouts.app')

@section('title', __('messages.titles.products'))

@section('content')
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="page-title">{{ __('messages.products_page.title') }}</h1>
                <p class="page-subtitle">{{ __('messages.products_page.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" id="headerLabelBtn" class="btn-secondary" title="{{ __('messages.products_page.print_label_tooltip') }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    {{ __('messages.products_page.label') }}
                </button>
                <a href="{{ route('products.export', request()->query()) }}" class="btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('messages.products_page.export') }}
                </a>
                <a href="{{ route('products.bulk') }}" class="btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    {{ __('messages.products_page.bulk_upload') }}
                </a>
                <a href="{{ route('products.create') }}" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('messages.products_page.add_product') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Search & Filter Tools -->
    <div class="mb-6">
        <form action="{{ route('products.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" class="form-input pl-10 w-64" placeholder="{{ __('messages.products_page.search_placeholder') }}" value="{{ request('search') }}">
            </div>
            <select name="category_id" class="form-input w-48" onchange="this.form.submit()">
                <option value="">{{ __('messages.products_page.all_categories') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @if(request('search') || request('category_id'))
                <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-red-500">
                    {{ __('messages.products_page.reset_filter') }}
                </a>
            @endif
        </form>
    </div>

    <div class="glass-card-solid overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-modern w-full">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" id="selectAll" class="form-checkbox rounded" title="{{ __('messages.products_page.select_all') }}">
                        </th>
                        <th class="w-16">{{ __('messages.products_page.image') }}</th>
                        <th>{{ __('messages.products_page.code_name') }}</th>
                        <th>{{ __('messages.products_page.category') }}</th>
                        <th>{{ __('messages.products_page.stock') }}</th>
                        <th>{{ __('messages.products_page.unit') }}</th>
                        <th>{{ __('messages.products_page.selling_price') }}</th>
                        <th>{{ __('messages.products_page.margin') }}</th>
                        <th class="text-right">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="p-2">
                            <input type="checkbox" name="products[]" value="{{ $product->id }}" class="product-checkbox form-checkbox rounded">
                        </td>
                        <td class="p-2">
                            <div class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden ring-1 ring-gray-200 dark:ring-gray-600">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                     class="h-full w-full object-cover"
                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'h-full w-full flex items-center justify-center text-gray-400 text-xs\'>No Img</div>'">
                            </div>
                        </td>
                        <td>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500">{{ $product->code }}</div>
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $product->category->name }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $product->stock < 5 ? 'badge-danger' : 'badge-success' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td>
                            <span class="text-gray-600 dark:text-gray-400 text-sm">{{ strtoupper($product->unit ?? 'pcs') }}</span>
                            @if(($product->conversion_factor ?? 1) > 1)
                                <div class="text-[10px] text-primary-500">1 {{ $product->purchase_unit ?? 'dus' }} = {{ $product->conversion_factor }} {{ $product->unit ?? 'pcs' }}</div>
                            @endif
                        </td>
                        <td class="font-semibold text-gray-900 dark:text-white">
                            <div class="flex items-center gap-2">
                                <span>Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                @if($product->hasPriceChange())
                                    @php
                                        $diff = $product->price_change_diff;
                                        $isIncrease = $diff > 0;
                                        $diffPerUnit = abs($diff) / ($product->conversion_factor ?? 1);
                                    @endphp
                                    <div class="relative group">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold cursor-help
                                            {{ $isIncrease ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                                            {{ $isIncrease ? '↑' : '↓' }}{{ $isIncrease ? '+' : '-' }}Rp {{ number_format(abs($diffPerUnit), 0, ',', '.') }}
                                        </span>
                                        {{-- Tooltip --}}
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 pointer-events-none">
                                            <div class="font-bold {{ $isIncrease ? 'text-red-300' : 'text-green-300' }}">
                                                {{ $isIncrease ? '⚠️ Harga Beli Naik' : '✅ Harga Beli Turun' }}
                                            </div>
                                            <div class="mt-1">
                                                Stok lama: {{ $product->stock_at_old_cost }} {{ $product->unit ?? 'pcs' }} @ Rp {{ number_format($product->previous_cost / ($product->conversion_factor ?? 1), 0, ',', '.') }}/{{ $product->unit ?? 'pcs' }}
                                            </div>
                                            <div class="text-gray-400 text-[10px] mt-1">
                                                {{ $product->cost_changed_at ? $product->cost_changed_at->diffForHumans() : '' }}
                                            </div>
                                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php
                                $costPerUnit = $product->cost / ($product->conversion_factor ?: 1);
                                $marginRp = $product->price - $costPerUnit;
                                $actualMarginPercent = $costPerUnit > 0 ? (($product->price - $costPerUnit) / $costPerUnit) * 100 : 0;
                            @endphp
                            <div class="text-center">
                                <span class="badge badge-success">{{ number_format($actualMarginPercent, 1) }}%</span>
                                <div class="text-xs text-gray-500 mt-1">Rp {{ number_format($marginRp, 0, ',', '.') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                @can('delete-data')
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-gray-500">Tidak ada produk ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $products->links() }}
        </div>
    </div>

    <div id="printFloatingBtn" class="fixed bottom-6 right-6 hidden z-50 flex flex-col gap-3 items-end">
        @can('delete-data')
        <button type="button" id="bulkDeleteBtn" class="flex items-center gap-2 px-6 py-3 bg-red-600 text-white font-bold rounded-full shadow-xl hover:bg-red-700 transform hover:scale-105 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            🗑️ Hapus <span class="selectedCount">0</span> Produk
        </button>
        @endcan

        <button type="button" id="printLabelsBtn" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-full shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            🏷️ Print <span class="selectedCount">0</span> Label
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.product-checkbox');
            const floatingBtn = document.getElementById('printFloatingBtn');
            const printBtn = document.getElementById('printLabelsBtn');

            if (!selectAll || !floatingBtn || !printBtn) {
                console.log('Missing elements for print labels');
                return;
            }

            let selectedIds = [];

            function updateSelection() {
                const checked = document.querySelectorAll('.product-checkbox:checked');
                const count = checked.length;
                document.querySelectorAll('.selectedCount').forEach(el => el.textContent = count);
                selectedIds = Array.from(checked).map(cb => cb.value);
                
                if (count > 0) {
                    floatingBtn.classList.remove('hidden');
                    floatingBtn.style.display = 'flex';
                } else {
                    floatingBtn.classList.add('hidden');
                    floatingBtn.style.display = 'none';
                }

                selectAll.checked = (count === checkboxes.length && count > 0);
                selectAll.indeterminate = (count > 0 && count < checkboxes.length);
            }

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSelection();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateSelection);
            });

            // Bulk Delete
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    const count = selectedIds.length;
                    if (count > 0 && confirm(`Yakin ingin menghapus ${count} produk terpilih?`)) {
                        fetch('{{ route("products.bulk-delete") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ products: selectedIds })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.message || 'Gagal menghapus produk');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan sistem');
                        });
                    }
                });
            }

            // Print button click - redirect with product IDs (floating button)
            printBtn.addEventListener('click', function() {
                if (selectedIds.length > 0) {
                    window.location.href = '{{ route("products.print-labels") }}?products=' + selectedIds.join(',');
                }
            });

            // Header Label button - use selected products if any, otherwise go with current filters
            const headerLabelBtn = document.getElementById('headerLabelBtn');
            if (headerLabelBtn) {
                headerLabelBtn.addEventListener('click', function() {
                    if (selectedIds.length > 0) {
                        window.location.href = '{{ route("products.print-labels") }}?products=' + selectedIds.join(',');
                    } else {
                        // Current query params (filters)
                        const searchParams = window.location.search;
                        const msg = searchParams ? 'Print semua produk sesuai filter saat ini?' : 'Tidak ada produk yang dipilih. Print semua produk aktif?';
                        
                        if (confirm(msg)) {
                            window.location.href = '{{ route("products.print-labels") }}' + searchParams;
                        }
                    }
                });
            }

            // Initialize
            updateSelection();
        });
    </script>
@endsection
