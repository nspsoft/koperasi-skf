@extends('layouts.print')

@section('title', 'Print Label Produk')

@section('content')
<style>
    @page {
        size: A4;
        margin: 5mm;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background: white;
        margin: 0;
        padding: 0;
    }

    .labels-container {
        display: grid;
        grid-template-columns: repeat({{ $cols }}, 38mm);
        gap: 2mm;
        padding: 5mm;
        justify-content: center;
    }

    .label {
        width: 38mm;
        height: 25mm;
        border: 0.3mm solid #333;
        border-radius: 1mm;
        padding: 1.5mm;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        page-break-inside: avoid;
        break-inside: avoid;
        position: relative;
        overflow: hidden;
    }

    .label::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1.5mm;
        background: linear-gradient(90deg, #059669, #0ea5e9);
    }

    .product-name {
        font-size: 6pt;
        font-weight: bold;
        color: #1f2937;
        text-align: center;
        line-height: 1.1;
        max-height: 7mm;
        overflow: hidden;
        margin-top: 1mm;
        text-transform: uppercase;
        letter-spacing: 0.2pt;
    }

    .price-section {
        text-align: center;
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .price {
        font-size: 12pt;
        font-weight: 900;
        color: #111827;
        line-height: 1;
    }

    .price-currency {
        font-size: 7pt;
        font-weight: 600;
    }

    .barcode-section {
        text-align: center;
        margin-top: auto;
    }

    .barcode {
        height: 5mm;
        width: 100%;
        background: repeating-linear-gradient(
            90deg,
            #000 0px,
            #000 1px,
            transparent 1px,
            transparent 2px
        );
    }

    .product-code {
        font-size: 5pt;
        color: #6b7280;
        margin-top: 0.5mm;
        letter-spacing: 0.5pt;
    }

    /* Print settings */
    @media print {
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .no-print {
            display: none !important;
        }

        .labels-container {
            padding: 0;
            gap: 1mm;
            grid-template-columns: repeat({{ $cols }}, 38mm);
        }

        .label {
            margin: 0;
            border: 0.2mm solid #666;
        }
    }

    /* Controls */
    .print-controls {
        position: fixed;
        top: 10px;
        right: 10px;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        z-index: 1000;
        display: flex;
        gap: 10px;
        flex-direction: column;
    }

    .print-btn {
        background: #059669;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
    }

    .print-btn:hover {
        background: #047857;
    }

    .back-btn {
        background: #6b7280;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
    }

    .quantity-input {
        width: 50px;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-align: center;
    }
</style>

<div class="print-controls no-print">
    @php
        $cols = request('cols', 3);
        $rows = request('rows', 8);
        $labelsPerSheet = $cols * $rows;
        $totalLabels = $products->count() * ($quantity ?? 1);
        $sheetsNeeded = ceil($totalLabels / $labelsPerSheet);
    @endphp
    
    <!-- Sheet Calculator -->
    <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 12px; margin-bottom: 10px;">
        <div style="font-size: 11px; color: #166534; font-weight: 600; margin-bottom: 8px;">📊 Kebutuhan Sticker</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px;">
            <div style="background: white; padding: 8px; border-radius: 6px; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; color: #059669;">{{ $totalLabels }}</div>
                <div style="color: #6b7280; font-size: 10px;">Total Label</div>
            </div>
            <div style="background: white; padding: 8px; border-radius: 6px; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; color: #059669;">{{ $sheetsNeeded }}</div>
                <div style="color: #6b7280; font-size: 10px;">Lembar Sticker</div>
            </div>
        </div>
        
        <!-- Configurable Sheet Layout -->
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #86efac;">
            <div style="font-size: 10px; color: #166534; margin-bottom: 5px;">⚙️ Susunan per Lembar:</div>
            <div style="display: flex; gap: 5px; align-items: center;">
                <input type="number" id="sheetCols" value="{{ $cols }}" min="1" max="10" style="width: 40px; padding: 4px; border: 1px solid #ddd; border-radius: 4px; text-align: center; font-size: 12px;">
                <span style="font-size: 12px;">×</span>
                <input type="number" id="sheetRows" value="{{ $rows }}" min="1" max="20" style="width: 40px; padding: 4px; border: 1px solid #ddd; border-radius: 4px; text-align: center; font-size: 12px;">
                <span style="font-size: 10px; color: #666;">= <strong id="labelsPerSheetDisplay">{{ $labelsPerSheet }}</strong>/lembar</span>
            </div>
        </div>
        
        <div style="font-size: 9px; color: #6b7280; margin-top: 6px; text-align: center;">
            *Default: 3×8 = 24 label
        </div>
    </div>

    <!-- Shopee Link -->
    <a href="https://shopee.co.id/search?keyword=label%20harga%20stiker%20tom%20jerry" target="_blank" rel="noopener" style="display: block; background: #ee4d2d; color: white; text-align: center; padding: 8px; border-radius: 6px; font-size: 11px; text-decoration: none; margin-bottom: 10px;">
        🛒 Beli Sticker di Shopee
    </a>

    <div class="quantity-control">
        <label>Jumlah per produk:</label>
        <input type="number" id="labelQuantity" value="{{ $quantity ?? 1 }}" min="1" max="10" class="quantity-input" onchange="updateLabels()">
    </div>
    <button onclick="window.print()" class="print-btn">🖨️ Print Label</button>
    <a href="{{ route('products.index') }}" class="back-btn">← Kembali</a>
</div>


<!-- Grid View (Default) -->
<div class="labels-container" id="gridView">
    @foreach($products as $product)
        @for($i = 0; $i < ($quantity ?? 1); $i++)
        <div class="label">
            <div class="product-name">{{ Str::limit($product->name, 30) }}</div>
            <div class="price-section">
                <div class="price">
                    <span class="price-currency">Rp</span> {{ number_format($product->price, 0, ',', '.') }}
                </div>
            </div>
            <div class="barcode-section">
                <svg class="barcode-svg" id="barcode-{{ $product->id }}-{{ $i }}"></svg>
                <div class="product-code">{{ $product->code }}</div>
            </div>
        </div>
        @endfor
    @endforeach
</div>



<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        generateBarcodes();
    });

    function generateBarcodes() {
        if (typeof JsBarcode === 'undefined') {
            console.error('JsBarcode library not loaded');
            alert('Gagal memuat library barcode. Pastikan koneksi internet tersedia.');
            return;
        }

        @foreach($products as $product)
            @for($i = 0; $i < ($quantity ?? 1); $i++)
            try {
                JsBarcode("#barcode-{{ $product->id }}-{{ $i }}", "{{ $product->code }}", {
                    format: "CODE128",
                    width: 1,
                    height: 15,
                    displayValue: false,
                    margin: 0
                });
            } catch (e) {
                console.error("Failed to generate barcode for {{ $product->code }}: ", e);
                // Fallback or visual indication of failure
                document.getElementById("barcode-{{ $product->id }}-{{ $i }}").parentElement.innerText = "Invalid Barcode";
            }
            @endfor
        @endforeach
    }

    function updateLabels() {
        const qty = document.getElementById('labelQuantity').value;
        const cols = document.getElementById('sheetCols').value;
        const rows = document.getElementById('sheetRows').value;
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('quantity', qty);
        currentUrl.searchParams.set('cols', cols);
        currentUrl.searchParams.set('rows', rows);
        window.location.href = currentUrl.toString();
    }
    
    // Update labels per sheet display on input change
    document.addEventListener('DOMContentLoaded', function() {
        const colsInput = document.getElementById('sheetCols');
        const rowsInput = document.getElementById('sheetRows');
        const display = document.getElementById('labelsPerSheetDisplay');
        
        function updateDisplay() {
            const cols = parseInt(colsInput.value) || 3;
            const rows = parseInt(rowsInput.value) || 8;
            display.textContent = cols * rows;
        }
        
        colsInput.addEventListener('input', updateDisplay);
        rowsInput.addEventListener('input', updateDisplay);
        colsInput.addEventListener('change', updateLabels);
        rowsInput.addEventListener('change', updateLabels);
    });
</script>
@endsection
