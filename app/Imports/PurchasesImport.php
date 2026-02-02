<?php

namespace App\Imports;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class PurchasesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public $importedCount = 0;
    public $errors = [];

    public function collection(Collection $rows)
    {
        // Group by PO Number
        $grouped = $rows->groupBy(function($row) {
            return $row['no_po'] ?? $row['purchase_order'] ?? null;
        });

        foreach ($grouped as $poNumber => $items) {
            if (empty($poNumber)) continue;

            try {
                DB::transaction(function () use ($poNumber, $items) {
                    // 1. Get or Validate Supplier
                    // Assuming first row has the supplier info
                    $firstRow = $items->first();
                    $supplierName = $firstRow['supplier'] ?? $firstRow['pemasok'];
                    
                    if (empty($supplierName)) {
                        throw new \Exception("Supplier wajib diisi untuk PO $poNumber");
                    }

                    $supplier = Supplier::where('name', 'like', '%' . $supplierName . '%')->first();
                    if (!$supplier) {
                        // Optional: Create supplier if not exists OR throw error
                         throw new \Exception("Supplier '$supplierName' tidak ditemukan untuk PO $poNumber");
                    }

                    // 2. Create Purchase Header
                    // Check if PO exists
                    if (Purchase::where('reference_number', $poNumber)->exists()) {
                        throw new \Exception("No PO '$poNumber' sudah ada di sistem");
                    }

                    $purchaseDate = $this->transformDate($firstRow['tanggal'] ?? $firstRow['date'] ?? now());

                    $purchase = Purchase::create([
                        'supplier_id' => $supplier->id,
                        'reference_number' => $poNumber,
                        'purchase_date' => $purchaseDate,
                        'status' => 'pending', // Default pending
                        'created_by' => auth()->id(),
                    ]);

                    $totalAmount = 0;

                    // 3. Create Items
                    foreach ($items as $row) {
                        $productCode = $row['kode_produk'] ?? $row['product_code'];
                        $quantity = $row['jumlah'] ?? $row['qty'] ?? $row['quantity'] ?? 0;
                        $cost = $row['harga'] ?? $row['price'] ?? $row['cost'] ?? 0;

                        if (empty($productCode)) continue;

                        $product = Product::where('code', $productCode)->first();
                        if (!$product) {
                            throw new \Exception("Produk dengan kode '$productCode' tidak ditemukan");
                        }

                        $subtotal = $quantity * $cost;
                        $totalAmount += $subtotal;

                        PurchaseItem::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'cost' => $cost,
                            'subtotal' => $subtotal,
                        ]);
                    }

                    $purchase->update(['total_amount' => $totalAmount]);
                    $this->importedCount++;
                });

            } catch (\Exception $e) {
                $this->errors[] = "Gagal import PO $poNumber: " . $e->getMessage();
            }
        }
    }

    private function transformDate($value, $format = 'Y-m-d')
    {
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            } 
            
            if (empty($value)) {
                return now();
            }

            return \Carbon\Carbon::parse($value);
        } catch (\Throwable $e) {
            return now();
        }
    }
}
