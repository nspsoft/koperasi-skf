# 🖨️ DOCUMENTATION PRINTING GUIDE

## 📄 How to Print/Export Documentation

**Quick Summary:**
Use the provided PowerShell script `convert-professional.ps1` to batch convert all Markdown files to professional-grade PDFs using Pandoc.

---

## 🚀 AUTOMATED METHOD (Recommended)

We have included a script that automatically converts all documentation to PDF with:
- Table of Contents
- Section Numbering
- Professional Layout
- Consistent Formatting

**Prerequisites:**
1.  **Pandoc** installed (`choco install pandoc`).
2.  **MiKTeX** (or other LaTeX engine) installed.

**Steps:**
1.  Open PowerShell in project root.
2.  Run the script:
    ```powershell
    .\convert-professional.ps1
    ```
3.  Wait for completion.
4.  Open folder `docs-pdf-professional` to find your PDFs.

---

## 🛠️ MANUAL METHOD (VS Code)

If you don't want to install Pandoc/LaTeX:

1.  Install VS Code Extension: **Markdown PDF** (yzane).
2.  Open any `.md` file.
3.  Right-Click in editor -> **Markdown PDF: Export (pdf)**.
4.  Print the resulting PDF.

---

## 📦 WHAT TO PRINT?

**For Developer Onboarding:**
- [x] QUICK_START.pdf
- [x] ARCHITECTURE.pdf

**For Operations Team:**
- [x] MAINTENANCE.pdf
- [x] TROUBLESHOOTING.pdf
- [x] SECURITY.pdf

**For UAT Team:**
- [x] UAT_PLAN.pdf
- [x] UAT_CHECKLIST.pdf (Print multiple copies)
- [ ] UAT_TEST_SCENARIOS.pdf (Digital reference recommended - 80 pages)

**For Management:**
- [x] README.pdf
- [x] FEATURES.pdf

---

**Issues?**
See `PDF_CUSTOMIZATION_GUIDE.md` for advanced settings.
