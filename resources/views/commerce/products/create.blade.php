@extends('layouts.app')

@section('title', __('messages.product_form.title_add'))

@section('content')
    <div class="page-header">
        <a href="{{ route('products.index') }}" class="flex items-center text-gray-500 hover:text-primary-600 mb-4 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('messages.product_form.back') }}
        </a>
        <h1 class="page-title">{{ __('messages.product_form.title_add') }}</h1>
    </div>

    <div class="max-w-2xl">
        <div class="glass-card-solid p-6">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div class="form-group">
                            <label for="code" class="form-label">{{ __('messages.product_form.code') }}</label>
                            <div class="flex gap-2">
                                <input type="text" name="code" id="code" class="form-input @error('code') border-red-500 @enderror" value="{{ old('code') }}" required>
                                <button type="button" onclick="openBarcodeScanner()" class="btn-secondary px-3" title="{{ __('messages.product_form.scan_barcode') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h2M4 12h2m10 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                </button>
                                <button type="button" onclick="generateCode()" class="btn-secondary px-3" title="{{ __('messages.product_form.generate_code') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                </button>
                            </div>
                            @error('code')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-group">
                            <label for="name" class="form-label">{{ __('messages.product_form.name') }}</label>
                            <input type="text" name="name" id="name" class="form-input @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
                            @error('name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-group">
                            <label for="category_id" class="form-label">{{ __('messages.product_form.category') }}</label>
                            <select name="category_id" id="category_id" class="form-input @error('category_id') border-red-500 @enderror" required>
                                <option value="">{{ __('messages.product_form.select_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">{{ __('messages.product_form.description') }}</label>
                            <textarea name="description" id="description" rows="3" class="form-input">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <!-- Pricing Section -->
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800 space-y-4">
                            <h3 class="font-bold text-blue-800 dark:text-blue-300 text-sm flex items-center gap-2">
                                {{ __('messages.product_form.pricing') }}
                            </h3>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="form-group">
                                    <label for="purchase_unit" class="form-label text-xs">{{ __('messages.product_form.purchase_unit') }}</label>
                                    <select name="purchase_unit" id="purchase_unit" class="form-input text-sm @error('purchase_unit') border-red-500 @enderror">
                                        @php
                                            $units = ['pcs' => 'Pcs', 'pack' => 'Pack', 'box' => 'Box', 'dus' => 'Dus', 'karton' => 'Karton', 'kg' => 'Kg', 'gram' => 'Gram', 'liter' => 'Liter', 'ml' => 'ML', 'lusin' => 'Lusin', 'bungkus' => 'Bungkus', 'botol' => 'Botol', 'kaleng' => 'Kaleng', 'sachet' => 'Sachet', 'roll' => 'Roll', 'lembar' => 'Lembar', 'pasang' => 'Pasang', 'set' => 'Set'];
                                        @endphp
                                        @foreach($units as $value => $label)
                                            <option value="{{ $value }}" {{ old('purchase_unit', 'dus') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="conversion_factor" class="form-label text-xs">{{ __('messages.product_form.conversion') }}</label>
                                    <input type="number" name="conversion_factor" id="conversion_factor" class="form-input text-sm @error('conversion_factor') border-red-500 @enderror" value="{{ old('conversion_factor', 1) }}" min="1" oninput="calculatePrice()">
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ __('messages.product_form.conversion_help') }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cost" class="form-label text-xs">{{ __('messages.product_form.cost_price') }}</label>
                                <input type="number" name="cost" id="cost" class="form-input @error('cost') border-red-500 @enderror" value="{{ old('cost', 0) }}" required min="0" oninput="calculatePrice()">
                                <p class="text-[10px] text-gray-400 mt-0.5" id="cost_per_unit_display">{{ __('messages.product_form.cost_per_sell_unit') }} Rp 0</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="form-group">
                                    <label for="margin_percent" class="form-label text-xs">{{ __('messages.product_form.margin_percent') }}</label>
                                    <input type="number" name="margin_percent" id="margin_percent" class="form-input text-sm @error('margin_percent') border-red-500 @enderror" value="{{ old('margin_percent', $globalSettings['default_margin'] ?? 10) }}" min="0" step="0.01" oninput="calculatePrice()">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-xs">&nbsp;</label>
                                    <button type="button" onclick="calculatePrice()" class="btn-secondary w-full !py-2 text-sm">
                                        {{ __('messages.product_form.calculate_btn') }}
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="price" class="form-label text-xs">{{ __('messages.product_form.sell_price') }} (Manual/Custom OK)</label>
                                <div class="relative">
                                    <input type="number" name="price" id="price" class="form-input font-bold text-lg @error('price') border-red-500 @enderror" value="{{ old('price', 0) }}" required min="0" oninput="reverseCalculateMargin()">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">/ <span id="unit_display">pcs</span></span>
                                </div>
                                <p class="text-[10px] text-green-600 mt-0.5" id="margin_display">{{ __('messages.product_form.margin_display', ['amount' => '0', 'percent' => '0']) }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="stock" class="form-label">{{ __('messages.product_form.stock') }}</label>
                            <input type="number" name="stock" id="stock" class="form-input @error('stock') border-red-500 @enderror" value="{{ old('stock', 0) }}" required min="0">
                            @error('stock')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-group">
                            <label for="unit" class="form-label">{{ __('messages.product_form.sell_unit') }}</label>
                            <select name="unit" id="unit" class="form-input @error('unit') border-red-500 @enderror" onchange="document.getElementById('unit_display').textContent = this.value">
                                @php
                                    $units = ['pcs' => 'Pcs (Satuan)', 'pack' => 'Pack', 'box' => 'Box', 'dus' => 'Dus', 'kg' => 'Kilogram', 'gram' => 'Gram', 'liter' => 'Liter', 'ml' => 'Mililiter', 'lusin' => 'Lusin', 'bungkus' => 'Bungkus', 'botol' => 'Botol', 'kaleng' => 'Kaleng', 'sachet' => 'Sachet', 'roll' => 'Roll', 'lembar' => 'Lembar', 'pasang' => 'Pasang', 'set' => 'Set'];
                                @endphp
                                @foreach($units as $value => $label)
                                    <option value="{{ $value }}" {{ old('unit', 'pcs') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('unit')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <!-- Pre-Order Settings -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 space-y-3" x-data="{ isPreorder: {{ old('is_preorder') ? 'true' : 'false' }} }">
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="is_preorder" value="0">
                                <input type="checkbox" name="is_preorder" id="is_preorder" value="1" class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 transition-all" x-model="isPreorder">
                                <label for="is_preorder" class="font-bold text-gray-700 dark:text-gray-300 cursor-pointer select-none">{{ __('messages.product_form.preorder') }}</label>
                            </div>
                            
                            <div x-show="isPreorder" x-transition.opacity.duration.300ms class="form-group pb-1 pl-8">
                                <label for="preorder_eta" class="form-label text-sm text-gray-500">{{ __('messages.product_form.eta') }}</label>
                                <input type="text" name="preorder_eta" id="preorder_eta" class="form-input text-sm @error('preorder_eta') border-red-500 @enderror" value="{{ old('preorder_eta') }}" placeholder="{{ __('messages.product_form.eta_placeholder') }}">
                                @error('preorder_eta')<p class="form-error">{{ $message }}</p>@enderror
                                <p class="text-[10px] text-gray-400 mt-1">{{ __('messages.product_form.po_help') }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image" class="form-label">{{ __('messages.product_form.image') }}</label>
                            <input type="file" name="image" id="image" class="form-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" accept="image/*">
                            @error('image')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-100 dark:border-gray-700 mt-6">
                    <button type="submit" class="btn-primary">{{ __('messages.product_form.save_btn') }}</button>
                </div>
            </form>

            <script>
                function calculatePrice() {
                    const cost = parseFloat(document.getElementById('cost').value) || 0;
                    const conversionFactor = parseInt(document.getElementById('conversion_factor').value) || 1;
                    const marginPercent = parseFloat(document.getElementById('margin_percent').value) || 0;

                    // Cost per sale unit
                    const costPerUnit = cost / conversionFactor;
                    document.getElementById('cost_per_unit_display').textContent = '{{ __('messages.product_form.cost_per_sell_unit') }} Rp ' + costPerUnit.toLocaleString('id-ID', {maximumFractionDigits: 0});

                    // Price with margin, ceiling based on settings
                    const ceiling = {{ $globalSettings['price_ceiling'] ?? 1000 }};
                    const rawPrice = costPerUnit * (1 + marginPercent / 100);
                    const finalPrice = Math.ceil(rawPrice / ceiling) * ceiling;
                    document.getElementById('price').value = finalPrice;

                    updateMarginInfo(finalPrice, costPerUnit);
                }

                function reverseCalculateMargin() {
                    const price = parseFloat(document.getElementById('price').value) || 0;
                    const cost = parseFloat(document.getElementById('cost').value) || 0;
                    const conversionFactor = parseInt(document.getElementById('conversion_factor').value) || 1;
                    
                    const costPerUnit = cost / conversionFactor;
                    
                    if (costPerUnit > 0) {
                        const actualMargin = price - costPerUnit;
                        const actualMarginPercent = (actualMargin / costPerUnit) * 100;
                        document.getElementById('margin_percent').value = actualMarginPercent.toFixed(2);
                        updateMarginInfo(price, costPerUnit);
                    }
                }

                function updateMarginInfo(price, costPerUnit) {
                    const actualMargin = price - costPerUnit;
                    const actualMarginPercent = costPerUnit > 0 ? ((actualMargin / costPerUnit) * 100).toFixed(1) : 0;
                    document.getElementById('margin_display').textContent = '{{ __('messages.product_form.margin_display', ['amount' => 'AMOUNT_PLACEHOLDER', 'percent' => 'PERCENT_PLACEHOLDER']) }}'
                        .replace('AMOUNT_PLACEHOLDER', 'Rp ' + actualMargin.toLocaleString('id-ID', {maximumFractionDigits: 0}))
                        .replace('PERCENT_PLACEHOLDER', actualMarginPercent);
                }

                // Initialize on page load
                document.addEventListener('DOMContentLoaded', calculatePrice);
            </script>
        </div>
    </div>

    <!-- Barcode Scanner Modal -->
    <div id="barcodeScannerModal" class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-bold text-gray-900 dark:text-white">{{ __('messages.product_form.scan_title') }}</h3>
                <button type="button" onclick="closeBarcodeScanner()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="barcode-reader" class="w-full aspect-square bg-gray-900"></div>
            <div class="p-4 text-center">
                <p class="text-sm text-gray-500">{{ __('messages.product_form.scan_help') }}</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode = null;

        function generateCode() {
            const timestamp = Date.now().toString().substr(-8);
            document.getElementById('code').value = 'PRD' + timestamp;
        }

        function openBarcodeScanner() {
            const modal = document.getElementById('barcodeScannerModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            html5QrCode = new Html5Qrcode("barcode-reader");
            
            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 150 },
                    aspectRatio: 1.0
                },
                (decodedText, decodedResult) => {
                    // Success callback
                    document.getElementById('code').value = decodedText;
                    closeBarcodeScanner();
                },
                (errorMessage) => {
                    // Error callback (ignore, keep scanning)
                }
            ).catch((err) => {
                console.error("Camera error:", err);
                alert("{{ __('messages.product_form.camera_error') }}");
                closeBarcodeScanner();
            });
        }

        function closeBarcodeScanner() {
            const modal = document.getElementById('barcodeScannerModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().catch(err => console.log("Stop error:", err));
            }
        }
    </script>
    @endpush
@endsection
