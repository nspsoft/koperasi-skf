# 📋 CHANGELOG

All notable changes to the **Koperasi Karyawan Digital** project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [2.0.0] - 2026-01-17

### Added
- **Module UAT:** Added comprehensive UAT documentation suite (Plan, Scenarios, Checklist).
- **Security:** Added `SECURITY.md` compliance document.
- **Maintenance:** Added `MAINTENANCE.md` operations guide.
- **Scripts:** Added PDF conversion scripts (`convert-to-pdf.ps1`).

### Changed
- Refactored `README.md` to index all new documentation.
- Updated database schema to support polymorphic Journal Entries.
- Standardized API response format.

### Fixed
- Fixed n+1 query issue on Dashboard widgets.
- Fixed rounding error in SHU calculation.

---

## [1.5.0] - 2025-12-20

### Added
- **Module Mart:** POS System with Barcode support.
- **Module Mart:** Inventory management (Stock In/Out).
- **Integration:** Midtrans Payment Gateway for Simpanan.

---

## [1.0.0] - 2025-10-01

### Added
- Initial Release.
- **Module Auth:** Login, Register, Forgot Password.
- **Module Finance:** Simpanan & Pinjaman basics.
- **Module Member:** Profile & Digital Card.

---

## [0.5.0-beta] - 2025-08-15

### Added
- MVP Development.
- Basic CRUD for Members.
- Database prototype.
