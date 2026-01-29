# 🏦 Aplikasi Koperasi Karyawan Digital

<p align="center">
  <strong>Sistem Manajemen Koperasi Modern & Terintegrasi</strong><br>
  Built with ❤️ using Laravel 11.x
</p>

<p align="center">
  <a href="#features"><strong>Features</strong></a> •
  <a href="#tech-stack"><strong>Tech Stack</strong></a> •
  <a href="#quick-start"><strong>Quick Start</strong></a> •
  <a href="#documentation"><strong>Documentation</strong></a> •
  <a href="#support"><strong>Support</strong></a>
</p>

---

## 📋 Overview

**Aplikasi Koperasi Karyawan** adalah platform digital komprehensif yang dirancang untuk mengelola semua aspek operasional koperasi karyawan modern, mulai dari manajemen keanggotaan, simpan-pinjam, hingga retail/mart dan akuntansi.

### 🎯 Key Highlights

- ✅ **60+ Fitur Terintegrasi** dalam 7 modul utama
- ✅ **100% Paperless** - Digital document generation & archiving
- ✅ **Real-time Dashboard** - Monitor kesehatan koperasi secara live
- ✅ **Multi-role Access** - Admin, Pengurus, Manager Toko, Anggota
- ✅ **Responsive Design** - Akses dari desktop, tablet, atau mobile
- ✅ **Compliance Ready** - Memenuhi UU No. 25/1992 tentang Koperasi

---

## 🚀 Features Overview

<details>
<summary><b>📊 Dashboard & Reporting</b></summary>
- Real-time overview kesehatan koperasi
- Grafik kinerja keuangan & operasional
- Quick access ke semua modul
- Notifikasi penting & alerts
</details>

<details>
<summary><b>💰 Keuangan (Simpan Pinjam)</b></summary>
- Manajemen Simpanan (Pokok, Wajib, Sukarela)
- Pengajuan Pinjaman Online & Approval Workflow
- Simulasi Pinjaman (Flat, Efektif, Anuitas)
- Pembagian SHU Otomatis
- Rekonsiliasi Bank
</details>

<details>
<summary><b>🛒 Koperasi Mart (Retail)</b></summary>
- POS (Point of Sales) dengan Barcode Scanner
- Manajemen Stok & Opname
- Supplier & Pembelian (PO)
- Support Pembayaran Tunai, Kredit Member, & QRIS
</details>

<details>
<summary><b>🛒 Belanja (E-Commerce Member)</b></summary>
- Katalog Produk Online untuk Anggota
- Keranjang Belanja & Checkout
- Riwayat Transaksi
- Potong Saldo/Plafon Kredit Otomatis
</details>

<details>
<summary><b>👥 Kepengurusan & Keanggotaan</b></summary>
- Database Anggota Lengkap
- Generate Kartu Anggota Digital (QR Code)
- Manajemen Aset Koperasi
- Digital Document Generator (Surat, Berita Acara)
</details>

---

## 🛠 Tech Stack

- **Backend:** Laravel 11.x (PHP 8.2+)
- **Frontend:** Blade, Alpine.js, Tailwind CSS
- **Database:** MySQL / MariaDB 10.x
- **Server:** Nginx, PHP-FPM
- **Services:** Supervisor (Queue), Cron (Schedule)
- **Integrations:** Midtrans (Payment), WhatsApp API (Notif)

---

## ⚡ Quick Start

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL

### Installation

```bash
# 1. Clone Repository
git clone https://github.com/your-repo/koperasi.git
cd koperasi

# 2. Install Dependencies
composer install
npm install

# 3. Setup Environment
cp .env.example .env
php artisan key:generate

# 4. Setup Database (Edit .env first!)
php artisan migrate:fresh --seed

# 5. Run Application
npm run build
php artisan serve
```

---

## 📚 Documentation

Dokumentasi lengkap tersedia dalam folder root project:

### **📖 Quick Access**

| **I'm a...** | **Start Here** | **Then Read** |
|---|---|---|
| **New Developer** | [QUICK_START.md](QUICK_START.md) (5 min) | [INSTALLATION.md](INSTALLATION.md) |
| **System Admin** | [MAINTENANCE.md](MAINTENANCE.md) | [DEPLOYMENT.md](DEPLOYMENT.md) |
| **QA Tester** | [UAT_PLAN.md](UAT_PLAN.md) | [UAT_TEST_SCENARIOS.md](UAT_TEST_SCENARIOS.md) |
| **End User** | [USER_MANUAL.md](USER_MANUAL.md) | [FEATURES.md](FEATURES.md) |
| **Anyone Lost?** | [DOCUMENTATION_INDEX.md](DOCUMENTATION_INDEX.md) | Master navigation |

---

### **📋 Complete Documentation (17 Files)**

#### **🏠 Getting Started**
- **[QUICK_START.md](QUICK_START.md)** - 5-minute setup guide
- **[DOCUMENTATION_INDEX.md](DOCUMENTATION_INDEX.md)** - Master index & navigation

#### **📘 Core Documentation**
- **[FEATURES.md](FEATURES.md)** - Complete 60+ feature catalog with benefits
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - System architecture & design decisions
- **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)** - ERD, table relationships, optimization

#### **🛠️ Development**
- **[INSTALLATION.md](INSTALLATION.md)** - Setup development environment
- **[DEPLOYMENT.md](DEPLOYMENT.md)** - Deploy to production server
- **[CHANGELOG.md](CHANGELOG.md)** - Version history & release notes

#### **🔧 Operations**
- **[MAINTENANCE.md](MAINTENANCE.md)** - Daily/weekly/monthly maintenance tasks
- **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** - Common issues & solutions
- **[SECURITY.md](SECURITY.md)** - Security policies & best practices

#### **🧪 Testing (UAT Suite)**
- **[UAT_PLAN.md](UAT_PLAN.md)** - UAT strategy, timeline, team structure
- **[UAT_TEST_SCENARIOS.md](UAT_TEST_SCENARIOS.md)** - 60 detailed test cases
- **[UAT_BUG_TEMPLATE.md](UAT_BUG_TEMPLATE.md)** - Bug report template
- **[UAT_CHECKLIST.md](UAT_CHECKLIST.md)** - 220-item tracking checklist

#### **👥 User Guides**
- **[USER_MANUAL.md](USER_MANUAL.md)** - Complete end-user guide

---

## 📞 Support

Jika mengalami kendala, hubungi Tim IT:

- **Email:** support@kopkarskf.com
- **WhatsApp:** IT Support Group
- **Issue Tracker:** Internal GitLab/Jira

---

<p align="center">
  © 2026 Koperasi Karyawan SKF. All rights reserved.
</p>
