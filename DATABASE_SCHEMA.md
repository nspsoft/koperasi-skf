# 🗄️ DATABASE SCHEMA DOCUMENTATION
**Aplikasi Koperasi Karyawan Digital v3.0**

Dokumen ini menjelaskan struktur database (ERD) untuk mendukung 60+ fitur aplikasi.

---

## 1. CORE & AUTHENTICATION (User Management)
Tabel dasar untuk manajemen pengguna dan hak akses.

### `users`
| Column | Type | Attributes | Description |
|---|---|---|---|
| id | bigint | PK | User ID |
| name | varchar | | Full Name |
| email | varchar | Unique | Login Email |
| password | varchar | | Bcrypt Hash |
| role_id | bigint | FK | Link to Spatie Roles |
| status | enum | | 'active', 'suspended' |

### `members` (Extended Process)
| Column | Type | Attributes | Description |
|---|---|---|---|
| id | bigint | PK | |
| user_id | bigint | FK, Unique | Link to `users` |
| no_anggota | varchar | Unique | Format: KOP-YYMM-XXXX |
| nik | varchar | Unique, Encrypted | KTP Number |
| employee_id | varchar | | NIK Karyawan Perusahaan |
| department_id | bigint | FK | Departemen Kerja |
| salary | decimal | | Gaji Pokok (Basis potong 40%) |
| join_date | date | | Tgl Bergabung |
| status | enum | | 'pending', 'active', 'resigned' |

---

## 2. FINANCE KEY TABLES (Simpan Pinjam)

### `savings_products` (Produk Simpanan)
Master data jenis simpanan (Pokok, Wajib, Sukarela, Hari Raya).

### `savings_transactions` (Mutasi Simpanan)
| Column | Type | Description |
|---|---|---|
| id | bigint | PK |
| member_id | bigint | FK -> `members` |
| product_id | bigint | FK -> `savings_products` |
| type | enum | 'deposit', 'withdraw', 'interest' |
| amount | decimal | Nominal Transaksi |
| method | enum | 'transfer', 'cash', 'payroll' |
| proof_file | varchar | Path gambar bukti transfer |
| status | enum | 'pending', 'approved', 'rejected' |

### `loans` (Pinjaman Induk)
| Column | Type | Description |
|---|---|---|
| id | bigint | PK |
| loan_number | varchar | Unique (LOAN-2026-001) |
| member_id | bigint | FK |
| amount_principal | decimal | Pokok Pinjaman |
| duration | int | Tenor (Bulan) |
| interest_method | enum | 'flat', 'effective', 'annuity' |
| interest_rate | decimal | % Bunga per tahun |
| status | enum | 'pending', 'approved_1', 'approved_2', 'active', 'paid', 'bad_debt' |
| signature_digital | text | Base64 / Path TTD Digital |

### `loan_schedules` (Kartu Angsuran)
Tabel detail jadwal pembayaran per bulan.
| Column | Type | Description |
|---|---|---|
| loan_id | bigint | FK |
| installment_no | int | Angsuran ke-X |
| due_date | date | Tgl Jatuh Tempo |
| principal | decimal | Porsi Pokok |
| interest | decimal | Porsi Bunga |
| penalty | decimal | Denda Keterlambatan |
| status | enum | 'unpaid', 'paid', 'partial' |

---

## 3. MART & INVENTORY (Point of Sales)

### `product_categories`
Hierarchy: `id`, `parent_id`, `name` (e.g. Makanan -> Snack).

### `products`
| Column | Type | Description |
|---|---|---|
| id | bigint | PK |
| sku | varchar | Unique (Internal Code) |
| barcode | varchar | Index (Scan Code) |
| name | varchar | Nama Produk |
| price_buy | decimal | HPP (Average Cost) |
| price_sell_public | decimal | Harga Umum |
| price_sell_member | decimal | Harga Member |
| stock | int | Stok Fisik Saat ini |
| min_stock | int | Low Stock Alert Threshold |
| supplier_id | bigint | FK |

### `pos_transactions` (Header Penjualan)
| Column | Type | Description |
|---|---|---|
| invoice_no | varchar | INV/POS/YYMM/XXXX |
| cashier_id | bigint | FK -> `users` |
| member_id | bigint | FK (Nullable) |
| total_amount | decimal | Grand Total |
| payment_method | enum | 'cash', 'transfer', 'credit_member', 'qris' |
| status | enum | 'generated', 'paid', 'void' |

### `pos_items` (Detail Barang)
| Column | Type | Description |
|---|---|---|
| transaction_id | bigint | FK |
| product_id | bigint | FK |
| qty | int | Jumlah Beli |
| price_at_moment | decimal | Harga saat transaksi (Snapshot) |
| cogs_at_moment | decimal | HPP saat transaksi (utk hitung profit) |

### `suppliers` & `purchase_orders`
Manajemen pembelian barang (Restock).
- `po_header`: supplier_id, date, status (draft, sent, received).
- `po_items`: product_id, qty_ordered, qty_received, buy_price.

### `stock_adjustments` (Opname)
| Column | Type | Description |
|---|---|---|
| id | bigint | PK |
| date | date | Tgl Opname |
| product_id | bigint | FK |
| system_qty | int | Stok Komputer |
| real_qty | int | Stok Fisik |
| reason | text | Alasan selisih (Rusak/Hilang) |

---

## 4. ORGANIZATION & UTILITY (Fitur Tambahan)

### `assets` (Inventaris Kantor)
| Column | Type | Description |
|---|---|---|
| asset_code | varchar | AST-001 |
| name | varchar | Laptop Asus ROG |
| purchase_date | date | Tgl Beli |
| purchase_price | decimal | Harga Beli |
| useful_life | int | Masa Manfaat (Tahun) |
| current_value | decimal | Nilai Buku (setelah penyusutan) |
| condition | enum | 'good', 'broken', 'lost' |

### `letters` (Surat Menyurat)
| Column | Type | Description |
|---|---|---|
| letter_no | varchar | Unique (e.g. 001/SK/KOP/I/2026) |
| type | enum | 'sk', 'undangan', 'pernyataan' |
| recipient | varchar | Penerima |
| body_content | json | Isi variabel surat |
| file_path | varchar | Lokasi PDF generate |

### `votings` (E-Voting)
- `voting_events`: title, start_date, end_date, active.
- `voting_candidates`: event_id, name, vision_mission, photo.
- `voting_ballots`: event_id, user_id (encrypted/hashed) -> Untuk cegah double vote tapi tetap anonim.

### `feedbacks` (Aspirasi)
| Column | Type | Description |
|---|---|---|
| user_id | bigint | FK (Nullable jika anonim) |
| subject | varchar | Topik |
| message | text | Isi saran |
| reply | text | Balasan Pengurus |
| status | enum | 'open', 'replied' |

### `announcements`
Title, Content (HTML), Target Audience (All/Member/Staff), Published At.

---

## 5. ACCOUNTING (Jurnal Umum)

### `chart_of_accounts` (COA)
Code (1101), Name (Kas Besar), Type (Asset/Liability/Equity/Revenue/Expense).

### `journal_entries`
| Column | Type | Description |
|---|---|---|
| id | bigint | PK |
| transaction_ref | varchar | Ref ID (Poly) -> Link ke `pos_transactions` / `savings` / `loans` |
| date | date | Tgl Jurnal |
| description | varchar | Keterangan |
| is_manual | boolean | True jika input manual |

### `journal_details`
| Column | Type | Description |
|---|---|---|
| journal_id | bigint | FK |
| coa_id | bigint | FK |
| debit | decimal | |
| credit | decimal | |

---

## 6. RELATIONS & CONSTRAINTS

- **Foreign Keys:** Semua relasi menggunakan `ON DELETE RESTRICT` untuk menjaga integritas data historis.
- **Indexes:**
    - `users(email)`
    - `members(nik, no_anggota)`
    - `products(sku, barcode, name)`
    - `pos_transactions(invoice_no, date)`

## 7. BACKUP STRATEGY
Tabel `audit_logs` dan `pos_transactions` akan dipartisi per tahun untuk menjaga performa query.
