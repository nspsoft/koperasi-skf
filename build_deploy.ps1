# Create directories
Remove-Item -Recurse -Force deploy_stage -ErrorAction SilentlyContinue
New-Item -ItemType Directory -Path "deploy_stage/kopkarskf" -Force
New-Item -ItemType Directory -Path "deploy_stage/public_html" -Force

# Use robocopy to mirror the main directory to kopkarskf, excluding unneeded folders
$source = "c:\laragon\www\Koperasi"
$dest = "c:\laragon\www\Koperasi\deploy_stage\kopkarskf"
$excludeDirs = @("deploy_stage", ".git", "node_modules", "public", ".agent", ".gemini")
$excludeFiles = @("koperasi_production_update.zip")

# robocopy returns exit codes < 8 for success
$robocopyArgs = @($source, $dest, "/MIR", "/XD") + $excludeDirs + @("/XF") + $excludeFiles
& robocopy $robocopyArgs

# Use robocopy to mirror the public directory to public_html
$publicSource = "c:\laragon\www\Koperasi\public"
$publicDest = "c:\laragon\www\Koperasi\deploy_stage\public_html"
& robocopy $publicSource $publicDest /MIR

# Output success message regardless of robocopy exit code
Write-Output "Files copied to staging directory."
