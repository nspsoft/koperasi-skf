<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'consignor']);

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        // Filter by consignment
        if ($request->has('is_consignment') && $request->is_consignment !== '') {
            $query->where('is_consignment', $request->is_consignment);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = \App\Models\Category::all();

        return view('commerce.products.index', compact('products', 'categories'));
    }

    /**
     * Print product labels for store shelves (Tom & Jerry sticker size: 38mm x 25mm)
     */
    public function printLabels(Request $request)
    {
        $productsParam = $request->input('products', '');
        $quantity = $request->input('quantity', 1);

        // If specific products are selected, prioritize those
        if (!empty($productsParam)) {
            $productIds = is_array($productsParam) 
                ? $productsParam 
                : explode(',', $productsParam);
            $products = Product::whereIn('id', $productIds)->get();
        } else {
            // Apply filters if no specific products are selected
            $query = Product::query();

            // Search
            if ($request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by status (default to active for printing labels)
            if ($request->has('is_active') && $request->is_active !== '') {
                $query->where('is_active', $request->is_active);
            } else {
                $query->where('is_active', true);
            }

            $products = $query->get();
        }

        return view('commerce.products.print-labels', compact('products', 'quantity'));
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all(); // Pass suppliers for consignment
        return view('commerce.products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|unique:products',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'is_preorder' => 'nullable|boolean',
            'preorder_eta' => 'nullable|string|max:255',
            // Consignment Validations
            'is_consignment' => 'nullable|boolean',
            'consignor_type' => 'required_if:is_consignment,1|nullable|in:member,supplier',
            'consignor_id' => 'required_if:is_consignment,1|nullable|integer',
            'consignment_price' => 'required_if:is_consignment,1|nullable|numeric|min:0',
        ]);

        $data = $request->all();
        // Ensure boolean fields are set correctly if unchecked
        $data['is_consignment'] = $request->has('is_consignment');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        \App\Models\AuditLog::log(
            'create', 
            "Menambahkan produk baru: {$product->name} ({$product->code})",
            $product
        );

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    // ... show ...

    public function edit(Product $product)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('commerce.products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|unique:products,code,'.$product->id,
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'is_preorder' => 'nullable|boolean',
            'preorder_eta' => 'nullable|string|max:255',
            // Consignment Validations
            'is_consignment' => 'nullable|boolean',
            'consignor_type' => 'required_if:is_consignment,1|nullable|in:member,supplier',
            'consignor_id' => 'required_if:is_consignment,1|nullable|integer',
            'consignment_price' => 'required_if:is_consignment,1|nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['is_consignment'] = $request->has('is_consignment'); // Handle boolean checkbox
        $data['is_preorder'] = $request->has('is_preorder'); 
        $data['is_active'] = $request->has('is_active'); 

        if ($request->hasFile('image')) {
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        \App\Models\AuditLog::log(
            'update', 
            "Memperbarui data produk: {$product->name} ({$product->code})",
            $product
        );

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Update product image only (AJAX)
     */
    public function updateImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $path = $request->file('image')->store('products', 'public');
            $product->update(['image' => $path]);

            \App\Models\AuditLog::log(
                'update', 
                "Memperbarui gambar produk: {$product->name} ({$product->code})",
                $product
            );

            return response()->json([
                'success' => true,
                'image_url' => Storage::url($path),
                'message' => 'Gambar berhasil diperbarui'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada gambar yang diupload'], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('products.index')->with('error', 'Hanya Admin yang dapat menghapus produk');
        }

        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        
        \App\Models\AuditLog::log(
            'delete', 
            "Menghapus produk: {$product->name} ({$product->code})",
            $product
        );

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
    }

    /**
     * Export products to Excel
     */
    public function export(Request $request)
    {
        $query = Product::with(['category', 'consignor']);

        // Apply same filters as index
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $products = $query->latest()->get();

        // Create spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Produk');

        // Headers
        $headers = ['Kode', 'Nama Produk', 'Kategori', 'Harga Jual', 'Harga Modal', 'Margin %', 'Stok', 'Min Stok', 'Satuan', 'Status', 'Konsinyasi'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Data
        $row = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product->code);
            $sheet->setCellValue('B' . $row, $product->name);
            $sheet->setCellValue('C' . $row, $product->category ? $product->category->name : '-');
            $sheet->setCellValue('D' . $row, $product->price);
            $sheet->setCellValue('E' . $row, $product->cost);
            $sheet->setCellValue('F' . $row, $product->margin_percent);
            $sheet->setCellValue('G' . $row, $product->stock);
            $sheet->setCellValue('H' . $row, $product->min_stock);
            $sheet->setCellValue('I' . $row, $product->unit);
            $sheet->setCellValue('J' . $row, $product->is_active ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue('K' . $row, $product->is_consignment ? 'Ya' : 'Tidak');
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Log activity
        \App\Models\AuditLog::log(
            'export',
            'product',
            null,
            null,
            ['count' => $products->count(), 'format' => 'xlsx']
        );

        // Download
        $filename = 'produk_' . date('Y-m-d_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Show bulk upload form
     */
    public function bulkUpload()
    {
        return view('commerce.products.bulk-upload');
    }

    /**
     * Download Excel template for bulk upload
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Produk');

        // Headers with styling - updated with unit conversion columns
        $headers = ['Kode Produk*', 'Nama Produk*', 'Kategori', 'Satuan Jual', 'Satuan Beli', 'Konversi', 'Harga Modal', 'Margin %', 'Harga Jual', 'Stok', 'Nama File Gambar', 'Deskripsi'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
        
        // Highlight new unit columns with blue background
        $newColStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('D1:F1')->applyFromArray($newColStyle); // Satuan Jual, Satuan Beli, Konversi
        $sheet->getStyle('H1')->applyFromArray($newColStyle); // Margin %
        
        // Sample data with unit conversion examples
        $sampleData = [
            // Kode, Nama, Kategori, Satuan Jual, Satuan Beli, Konversi, Harga Modal, Margin%, Harga Jual, Stok, Gambar, Deskripsi
            ['PRD001', 'Indomie Goreng', 'Sembako', 'pcs', 'dus', 40, 120000, 15, 3500, 200, 'PRD001.jpg', 'Mie instan rasa goreng (1 dus = 40 pcs)'],
            ['PRD002', 'Aqua 600ml', 'Minuman', 'botol', 'pack', 24, 48000, 20, 2500, 120, 'PRD002.jpg', 'Air mineral (1 pack = 24 botol)'],
            ['PRD003', 'Beras Premium 5kg', 'Sembako', 'kg', 'karung', 25, 325000, 10, 14500, 50, '', 'Beras premium (1 karung = 25 kg)'],
            ['PRD004', 'Gula Pasir', 'Sembako', 'kg', 'kg', 1, 14000, 10, 15500, 80, '', 'Satuan beli = satuan jual'],
            ['PRD005', 'Kopi Sachet', 'Minuman', 'sachet', 'renceng', 10, 22000, 15, 2500, 200, '', 'Kopi sachet (1 renceng = 10 sachet)'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');
        
        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Add instruction sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');
        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE PRODUK'],
            [''],
            ['Kolom dengan tanda * adalah WAJIB diisi'],
            [''],
            ['KOLOM DASAR:'],
            ['Kode Produk* - Kode unik untuk setiap produk (contoh: PRD001, SKU-123)'],
            ['Nama Produk* - Nama lengkap produk'],
            ['Kategori - Nama kategori (akan dibuat otomatis jika belum ada)'],
            [''],
            ['KOLOM KONVERSI SATUAN (BARU):'],
            ['Satuan Jual - Satuan untuk penjualan (pcs, kg, botol, sachet, dll)'],
            ['Satuan Beli - Satuan untuk pembelian dari supplier (dus, pack, karton, dll)'],
            ['Konversi - Jumlah satuan jual per 1 satuan beli (contoh: 40 jika 1 dus = 40 pcs)'],
            [''],
            ['KOLOM HARGA:'],
            ['Harga Modal - Harga beli PER SATUAN BELI (contoh: Rp 120.000 per dus)'],
            ['Margin % - Persentase keuntungan (10, 15, 20, dll)'],
            ['Harga Jual - Harga jual PER SATUAN JUAL (akan dikalkulasi: modal/konversi × (1+margin%))'],
            [''],
            ['KOLOM LAINNYA:'],
            ['Stok - Jumlah stok awal (dalam SATUAN JUAL)'],
            ['Nama File Gambar - Nama file gambar (opsional, upload terpisah)'],
            ['Deskripsi - Deskripsi produk (opsional)'],
            [''],
            ['CONTOH KALKULASI:'],
            ['- Beli 1 dus Indomie = Rp 120.000'],
            ['- 1 dus = 40 pcs'],
            ['- Modal per pcs = 120.000 / 40 = Rp 3.000'],
            ['- Margin 15% = 3.000 × 1.15 = Rp 3.450'],
            ['- Harga jual (ceiling 500) = Rp 3.500'],
            [''],
            ['TIPS:'],
            ['- Hapus baris contoh sebelum mengisi data Anda'],
            ['- Untuk upload gambar, gunakan fitur Bulk Upload Gambar'],
            ['- Nama file gambar harus sama dengan Kode Produk'],
            ['- Jika satuan beli = satuan jual, isi konversi = 1'],
        ];
        $instructionSheet->fromArray($instructions, null, 'A1');
        $instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionSheet->getStyle('A5')->getFont()->setBold(true);
        $instructionSheet->getStyle('A10')->getFont()->setBold(true);
        $instructionSheet->getStyle('A15')->getFont()->setBold(true);
        $instructionSheet->getStyle('A20')->getFont()->setBold(true);
        $instructionSheet->getStyle('A25')->getFont()->setBold(true);
        $instructionSheet->getStyle('A32')->getFont()->setBold(true);
        $instructionSheet->getColumnDimension('A')->setWidth(70);
        
        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);
        
        // Generate file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_produk.xlsx';
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import products from Excel/CSV file
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:5120',
        ]);

        $updateExisting = $request->has('update_existing');
        $result = ['success' => 0, 'updated' => 0, 'failed' => 0, 'errors' => []];

        try {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            if ($extension === 'csv') {
                $data = $this->parseCsv($file);
            } else {
                // Parse Excel file using PhpSpreadsheet
                $data = $this->parseExcel($file);
            }

            foreach ($data as $index => $row) {
                try {
                    if (empty($row['code']) || empty($row['name'])) {
                        $result['errors'][] = "Baris " . ($index + 2) . ": Kode dan nama produk wajib diisi";
                        $result['failed']++;
                        continue;
                    }

                    // Find or create category
                    $category = Category::firstOrCreate(
                        ['name' => $row['category'] ?? 'Umum'],
                        ['name' => $row['category'] ?? 'Umum']
                    );

                    $existingProduct = Product::where('code', $row['code'])->first();

                    if ($existingProduct) {
                        if ($updateExisting) {
                            $existingProduct->update([
                                'name' => $row['name'],
                                'category_id' => $category->id,
                                'unit' => $row['unit'] ?? 'pcs',
                                'purchase_unit' => $row['purchase_unit'] ?? 'pcs',
                                'conversion_factor' => $row['conversion_factor'] ?? $row['conversion'] ?? 1,
                                'cost' => $row['cost'] ?? 0,
                                'margin_percent' => $row['margin_percent'] ?? $row['margin'] ?? 0,
                                'price' => $row['price'] ?? 0,
                                'stock' => $row['stock'] ?? 0,
                                'description' => $row['description'] ?? null,
                            ]);
                            $result['updated']++;
                        } else {
                            $result['errors'][] = "Baris " . ($index + 2) . ": Kode {$row['code']} sudah ada";
                            $result['failed']++;
                        }
                    } else {
                        Product::create([
                            'code' => $row['code'],
                            'name' => $row['name'],
                            'category_id' => $category->id,
                            'unit' => $row['unit'] ?? 'pcs',
                            'purchase_unit' => $row['purchase_unit'] ?? 'pcs',
                            'conversion_factor' => $row['conversion_factor'] ?? $row['conversion'] ?? 1,
                            'cost' => $row['cost'] ?? 0,
                            'margin_percent' => $row['margin_percent'] ?? $row['margin'] ?? 0,
                            'price' => $row['price'] ?? 0,
                            'stock' => $row['stock'] ?? 0,
                            'description' => $row['description'] ?? null,
                            'is_active' => true,
                        ]);
                        $result['success']++;
                    }
                } catch (\Exception $e) {
                    $result['errors'][] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                    $result['failed']++;
                }
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        if ($result['success'] > 0 || $result['updated'] > 0) {
            \App\Models\AuditLog::log(
                'import', 
                "Import data produk (Excel): {$result['success']} baru, {$result['updated']} diperbarui"
            );
        }

        return back()->with('import_result', $result)->with('success', 'Import selesai!');
    }

    /**
     * Parse CSV file
     */
    protected function parseCsv($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
            rewind($handle);
        }
        
        $headers = fgetcsv($handle);
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);
        // Normalize header names
        $headers = $this->normalizeHeaders($headers);
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }
        
        fclose($handle);
        return $data;
    }

    /**
     * Parse Excel file using PhpSpreadsheet
     */
    protected function parseExcel($file)
    {
        $data = [];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        if (count($rows) < 2) {
            return $data;
        }
        
        // First row is headers
        $headers = array_map('trim', $rows[0]);
        $headers = array_map('strtolower', $headers);
        // Normalize header names
        $headers = $this->normalizeHeaders($headers);
        
        // Process data rows (skip header)
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            if (count($row) >= count($headers)) {
                $rowData = [];
                foreach ($headers as $index => $header) {
                    $rowData[$header] = $row[$index] ?? '';
                }
                $data[] = $rowData;
            }
        }
        
        return $data;
    }

    /**
     * Normalize header names to match expected format
     */
    protected function normalizeHeaders($headers)
    {
        $mapping = [
            'kode produk*' => 'code',
            'kode produk' => 'code',
            'kode' => 'code',
            'code' => 'code',
            'nama produk*' => 'name',
            'nama produk' => 'name',
            'nama' => 'name',
            'name' => 'name',
            'kategori' => 'category',
            'category' => 'category',
            // New unit columns
            'satuan jual' => 'unit',
            'satuan' => 'unit',
            'unit' => 'unit',
            'satuan beli' => 'purchase_unit',
            'purchase_unit' => 'purchase_unit',
            'konversi' => 'conversion',
            'conversion' => 'conversion',
            'conversion_factor' => 'conversion_factor',
            'isi' => 'conversion',
            // Price columns
            'harga jual' => 'price',
            'harga' => 'price',
            'price' => 'price',
            'harga modal' => 'cost',
            'modal' => 'cost',
            'cost' => 'cost',
            'margin %' => 'margin',
            'margin' => 'margin',
            'margin_percent' => 'margin_percent',
            // Other columns
            'stok' => 'stock',
            'stock' => 'stock',
            'nama file gambar' => 'image_file',
            'gambar' => 'image_file',
            'image' => 'image_file',
            'image_file' => 'image_file',
            'deskripsi' => 'description',
            'description' => 'description',
        ];
        
        return array_map(function($header) use ($mapping) {
            $normalized = strtolower(trim($header));
            return $mapping[$normalized] ?? $normalized;
        }, $headers);
    }

    /**
     * Bulk upload images - match by product code
     */
    public function bulkImages(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|max:2048',
        ]);

        $matched = 0;
        $notFound = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Get filename without extension as product code
                $filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                
                // Find product by code
                $product = Product::where('code', $filename)->first();
                
                if ($product) {
                    // Delete old image if exists
                    if ($product->image) {
                        Storage::disk('public')->delete($product->image);
                    }
                    
                    // Store new image
                    $path = $image->store('products', 'public');
                    $product->update(['image' => $path]);
                    $matched++;
                } else {
                    $notFound[] = $image->getClientOriginalName();
                }
            }
        }

        $message = "Berhasil upload {$matched} gambar.";
        if (count($notFound) > 0) {
            $message .= " " . count($notFound) . " file tidak ditemukan produknya: " . implode(', ', array_slice($notFound, 0, 5));
            if (count($notFound) > 5) {
                $message .= "...";
            }
        }

        if ($matched > 0) {
            \App\Models\AuditLog::log(
                'update', 
                "Bulk upload gambar produk: {$matched} gambar berhasil diperbarui"
            );
        }

        return back()->with('success', $message);
    }
}
