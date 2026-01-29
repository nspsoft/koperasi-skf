# Simple Professional PDF Converter
# Usage: .\convert-professional.ps1

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Professional PDF Converter" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Refresh PATH
$env:Path = [System.Environment]::GetEnvironmentVariable("Path", "Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path", "User")

# Create output folder
$outputFolder = "docs-pdf-professional"
if (-Not (Test-Path $outputFolder)) {
    New-Item -ItemType Directory -Path $outputFolder | Out-Null
    Write-Host "Created: $outputFolder" -ForegroundColor Green
}

# Get markdown files
$files = Get-ChildItem *.md | Where-Object { $_.Name -notlike "*node_modules*" }
Write-Host "Found $($files.Count) files" -ForegroundColor Green
Write-Host ""

$success = 0
$failed = 0

foreach ($file in $files) {
    Write-Host "Converting: $($file.Name)..." -NoNewline
    
    $output = Join-Path $outputFolder "$($file.BaseName).pdf"
    
    try {
        # Simple professional command
        & pandoc $file.FullName -o $output `
            --pdf-engine=xelatex `
            -V geometry:margin=1in `
            -V fontsize=11pt `
            --toc `
            --toc-depth=3 `
            --number-sections `
            -V colorlinks=true `
            -V linkcolor=blue `
            2>$null
        
        if (Test-Path $output) {
            Write-Host " ✓" -ForegroundColor Green
            $success++
        }
        else {
            Write-Host " ✗" -ForegroundColor Red
            $failed++
        }
    }
    catch {
        Write-Host " ✗" -ForegroundColor Red
        $failed++
    }
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "Done! Success: $success | Failed: $failed" -ForegroundColor Yellow
Write-Host "Output: $outputFolder" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

if ($success -gt 0) {
    Start-Process explorer.exe $outputFolder
}
