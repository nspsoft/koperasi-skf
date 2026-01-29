# Advanced PDF Conversion Script with Customization
# Usage: .\convert-pdf-custom.ps1 -Style professional

param(
    [ValidateSet("professional", "technical", "manual", "print", "presentation")]
    [string]$Style = "professional",
    
    [string]$OutputFolder = "docs-pdf-custom",
    
    [switch]$NumberSections,
    
    [switch]$TOC = $true,
    
    [string]$Author = "Koperasi Team",
    
    [string]$FontSize = "11pt"
)

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Advanced PDF Converter with Styles" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Style: $Style" -ForegroundColor Yellow
Write-Host ""

# Refresh PATH
$env:Path = [System.Environment]::GetEnvironmentVariable("Path", "Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path", "User")

# Create output folder
if (-Not (Test-Path $OutputFolder)) {
    New-Item -ItemType Directory -Path $OutputFolder | Out-Null
    Write-Host "Created output folder: $OutputFolder" -ForegroundColor Green
}

# Get all markdown files
$mdFiles = Get-ChildItem -Path . -Filter *.md | Where-Object { 
    $_.Name -notlike "*node_modules*" -and 
    $_.Name -notlike "*vendor*" 
}

Write-Host "Found $($mdFiles.Count) Markdown files" -ForegroundColor Green
Write-Host ""

$successCount = 0
$failCount = 0

foreach ($file in $mdFiles) {
    Write-Host "Converting: $($file.Name)..." -NoNewline
    
    $outputPath = Join-Path $OutputFolder "$($file.BaseName).pdf"
    
    # Build pandoc command based on style
    $pandocArgs = @(
        $file.FullName,
        "-o", $outputPath,
        "--pdf-engine=xelatex"
    )
    
    # Common settings
    if ($TOC) {
        $pandocArgs += "--toc"
    }
    
    # Style-specific settings
    switch ($Style) {
        "professional" {
            $pandocArgs += @(
                "-V", "documentclass=report",
                "-V", "geometry:margin=1in",
                "-V", "fontsize=$FontSize",
                "-V", "linestretch=1.15",
                "--toc-depth=3",
                "-V", "colorlinks=true",
                "-V", "linkcolor=blue",
                "-V", "urlcolor=blue"
            )
            if ($NumberSections) {
                $pandocArgs += "--number-sections"
            }
        }
        
        "technical" {
            $pandocArgs += @(
                "-V", "documentclass=article",
                "-V", "geometry:margin=1.25in",
                "-V", "fontsize=10pt",
                "--toc-depth=4",
                "--number-sections",
                "-V", "colorlinks=true",
                "-V", "linkcolor=blue"
            )
        }
        
        "manual" {
            $pandocArgs += @(
                "-V", "documentclass=report",
                "-V", "geometry:margin=1in",
                "-V", "fontsize=11pt",
                "-V", "linestretch=1.3",
                "--toc-depth=2",
                "--number-sections"
            )
        }
        
        "print" {
            $pandocArgs += @(
                "-V", "geometry:margin=1in",
                "-V", "fontsize=11pt",
                "-V", "colorlinks=false",
                "-V", "linkbordercolor=white"
            )
        }
        
        "presentation" {
            $pandocArgs += @(
                "-V", "documentclass=article",
                "-V", "geometry:margin=0.75in",
                "-V", "fontsize=12pt",
                "--toc-depth=2",
                "-V", "colorlinks=true",
                "-V", "linkcolor=blue"
            )
        }
    }
    
    # Add metadata (simplified to avoid parsing issues)
    $pandocArgs += @(
        "-V", "date=17 January 2026"
    )
    
    try {
        # Run pandoc
        $process = Start-Process "pandoc" -ArgumentList $pandocArgs -NoNewWindow -Wait -PassThru -RedirectStandardError ".\pandoc_error.log"
        
        if ($process.ExitCode -eq 0 -and (Test-Path $outputPath)) {
            Write-Host " ✓ Success" -ForegroundColor Green
            $successCount++
        }
        else {
            Write-Host " ✗ Failed" -ForegroundColor Red
            if (Test-Path ".\pandoc_error.log") {
                $error = Get-Content ".\pandoc_error.log" -Raw
                if ($error) {
                    Write-Host "   Error: $($error.Substring(0, [Math]::Min(100, $error.Length)))" -ForegroundColor Red
                }
            }
            $failCount++
        }
    }
    catch {
        Write-Host " ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
        $failCount++
    }
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "Conversion Summary:" -ForegroundColor Cyan
Write-Host "  Style: $Style" -ForegroundColor Yellow
Write-Host "  Success: $successCount" -ForegroundColor Green
Write-Host "  Failed: $failCount" -ForegroundColor Red
Write-Host "  Output: $OutputFolder" -ForegroundColor Yellow
Write-Host "==========================================" -ForegroundColor Cyan

# Cleanup error log
if (Test-Path ".\pandoc_error.log") {
    Remove-Item ".\pandoc_error.log" -Force
}

# Open output folder if successful
if ($successCount -gt 0) {
    Write-Host ""
    Write-Host "Opening output folder..." -ForegroundColor Green
    Start-Process explorer.exe $OutputFolder
}

Write-Host ""
Write-Host "💡 TIP: Try different styles:" -ForegroundColor Cyan
Write-Host "   .\convert-pdf-custom.ps1 -Style technical" -ForegroundColor Gray
Write-Host "   .\convert-pdf-custom.ps1 -Style manual -NumberSections" -ForegroundColor Gray
Write-Host "   .\convert-pdf-custom.ps1 -Style print" -ForegroundColor Gray
Write-Host ""
