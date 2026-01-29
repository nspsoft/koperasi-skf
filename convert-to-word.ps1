$sourceDir = $PSScriptRoot
$outputDir = Join-Path $sourceDir "docs-word-final"

# Create output directory if it doesn't exist
if (-not (Test-Path -Path $outputDir)) {
    New-Item -ItemType Directory -Path $outputDir -Force | Out-Null
}

$files = @(
    "FEATURES.md",
    "USER_MANUAL.md",
    "MAINTENANCE.md",
    "TROUBLESHOOTING.md",
    "DATABASE_SCHEMA.md",
    "ARCHITECTURE.md",
    "UAT_PLAN.md",
    "UAT_TEST_SCENARIOS.md",
    "UAT_CHECKLIST.md",
    "UAT_BUG_TEMPLATE.md",
    "README.md",
    "DOCUMENTATION_INDEX.md",
    "QUICK_START.md",
    "INSTALLATION.md",
    "SECURITY.md"
)

Write-Host "Starting conversion to Word (DOCX)..." -ForegroundColor Cyan

foreach ($file in $files) {
    if (Test-Path "$sourceDir\$file") {
        $filesName = [System.IO.Path]::GetFileNameWithoutExtension($file)
        $outputFile = Join-Path $outputDir "$filesName.docx"
        
        Write-Host "Converting $file -> $filesName.docx" -NoNewline
        
        # Pandoc command for docx
        # --toc adds a Table of Contents
        try {
            pandoc "$sourceDir\$file" -o "$outputFile" --toc --number-sections
            Write-Host " [OK]" -ForegroundColor Green
        }
        catch {
            Write-Host " [FAILED]" -ForegroundColor Red
            Write-Host $_
        }
    }
    else {
        Write-Host "File not found: $file" -ForegroundColor Yellow
    }
}

Write-Host "`nAll conversions completed. Files are in: $outputDir" -ForegroundColor Cyan
Invoke-Item $outputDir
