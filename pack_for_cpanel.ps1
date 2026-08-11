$ErrorActionPreference = "Stop"

$projectRoot = "c:\laragon\www\Koperasi"
$deployDir = "$env:TEMP\deploy_stage"
$zipFile = "$projectRoot\koperasi_production_update.zip"

# Clean up previous staging
if (Test-Path $deployDir) { Remove-Item -Recurse -Force $deployDir }
if (Test-Path $zipFile) { Remove-Item -Force $zipFile }

# Create directories
New-Item -ItemType Directory -Path "$deployDir\kopkarskf" | Out-Null
New-Item -ItemType Directory -Path "$deployDir\public_html" | Out-Null

Write-Host "Copying core files to kopkarskf..."
$excludeItems = @('.git', '.agent', 'node_modules', 'deploy_stage', 'koperasi_production_update.zip', 'public', 'pack_for_cpanel.ps1', 'vendor')

Get-ChildItem -Path $projectRoot | Where-Object { $_.Name -notin $excludeItems } | Copy-Item -Destination "$deployDir\kopkarskf" -Recurse -Force

Write-Host "Copying vendor..."
if (Test-Path "$projectRoot\vendor") {
    Copy-Item -Path "$projectRoot\vendor" -Destination "$deployDir\kopkarskf" -Recurse -Force
}

Write-Host "Copying public files to public_html..."
if (Test-Path "$projectRoot\public") {
    Copy-Item -Path "$projectRoot\public\*" -Destination "$deployDir\public_html" -Recurse -Force
    Copy-Item -Path "$projectRoot\public\.htaccess" -Destination "$deployDir\public_html" -Force -ErrorAction SilentlyContinue
}

Write-Host "Modifying index.php..."
$indexContent = @"
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Arahkan ke folder core 'kopkarskf' yang berada satu level di atas public_html
if (file_exists(`$maintenance = __DIR__.'/../kopkarskf/storage/framework/maintenance.php')) {
    require `$maintenance;
}

require __DIR__.'/../kopkarskf/vendor/autoload.php';

`$app = require_once __DIR__.'/../kopkarskf/bootstrap/app.php';

`$app->handleRequest(Request::capture());
"@
Set-Content -Path "$deployDir\public_html\index.php" -Value $indexContent

Write-Host "Creating zip archive..."
Compress-Archive -Path "$deployDir\*" -DestinationPath $zipFile -Force

Write-Host "Cleaning up staging directory..."
Remove-Item -Recurse -Force $deployDir

Write-Host "Packing complete: $zipFile"
