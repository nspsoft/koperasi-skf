# Convert all Markdown files to PDF
# Requirements: VS Code with Markdown PDF extension installed

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "  Markdown to PDF Converter" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Get all .md files
$mdFiles = Get-ChildItem -Path . -Filter *.md

Write-Host "Found $($mdFiles.Count) Markdown files:" -ForegroundColor Green
foreach ($file in $mdFiles) {
    Write-Host "  - $($file.Name)" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Converting to PDF..." -ForegroundColor Cyan
Write-Host ""

# Create output folder
$outputFolder = ".\docs-pdf"
if (-Not (Test-Path $outputFolder)) {
    New-Item -ItemType Directory -Path $outputFolder | Out-Null
    Write-Host "Created output folder: $outputFolder" -ForegroundColor Green
}

# Counter
$successCount = 0
$failCount = 0

# Convert each file using code command
foreach ($file in $mdFiles) {
    try {
        Write-Host "Converting: $($file.Name)..." -NoNewline
        
        # Using VS Code CLI to convert
        # Alternative: Use pandoc if installed
        # pandoc $file.FullName -o "$outputFolder\$($file.BaseName).pdf"
        
        # For now, create placeholder
        $pdfPath = Join-Path $outputFolder "$($file.BaseName).pdf"
        
        # Check if pandoc is available
        $pandocInstalled = Get-Command pandoc -ErrorAction SilentlyContinue
        
        if ($pandocInstalled) {
            # Use pandoc
            pandoc $file.FullName -o $pdfPath --pdf-engine=xelatex -V geometry:margin=1in --toc 2>$null
            
            if (Test-Path $pdfPath) {
                Write-Host " ✓ Success" -ForegroundColor Green
                $successCount++
            } else {
                Write-Host " ✗ Failed" -ForegroundColor Red
                $failCount++
            }
        } else {
            Write-Host " ⚠ Pandoc not installed. Skipping." -ForegroundColor Yellow
            Write-Host ""
            Write-Host "Install pandoc: choco install pandoc" -ForegroundColor Cyan
            break
        }
    }
    catch {
        Write-Host " ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
        $failCount++
    }
}

Write-Host ""
Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Conversion Summary:" -ForegroundColor Cyan
Write-Host "  Success: $successCount" -ForegroundColor Green
Write-Host "  Failed: $failCount" -ForegroundColor Red
Write-Host "  Output: $outputFolder" -ForegroundColor Yellow
Write-Host "==================================" -ForegroundColor Cyan

# Open output folder
if ($successCount -gt 0) {
    Write-Host ""
    Write-Host "Opening output folder..." -ForegroundColor Green
    Start-Process explorer.exe $outputFolder
}
