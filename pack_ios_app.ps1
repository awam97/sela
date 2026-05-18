# Sela iOS App Packager for Sideloadly
$ErrorActionPreference = 'Stop'

Write-Host "==============================================" -ForegroundColor Yellow
Write-Host "    SELA IOS APP PACKAGER FOR SIDELOADLY      " -ForegroundColor Yellow
Write-Host "==============================================" -ForegroundColor Yellow

# 1. Locate the downloaded Sela iOS zip
$downloadsFolder = [Path]::Combine([Environment]::GetFolderPath("UserProfile"), "Downloads")
$zipPath = Join-Path $downloadsFolder "sela-ios-debug-app.zip"

if (!(Test-Path $zipPath)) {
    Write-Host "`nError: Could not find 'sela-ios-debug-app.zip' in your Downloads folder!" -ForegroundColor Red
    Write-Host "Please download it first from GitHub Actions:" -ForegroundColor White
    Write-Host "https://github.com/awam97/sela/actions/runs/26043876810" -ForegroundColor Cyan
    Write-Host "`nThen run this script again." -ForegroundColor Yellow
    Write-Host ""
    Read-Host "Press Enter to exit..."
    exit 1
}

# 2. Setup temporary paths
$targetDir = "c:\Users\porta\Desktop\Sela"
$tempExtract = Join-Path $targetDir ".temp_ios_extract"
$payloadDir = Join-Path $targetDir "Payload"
$outputPath = Join-Path $targetDir "sela.ipa"

if (Test-Path $tempExtract) { Remove-Item -Path $tempExtract -Recurse -Force | Out-Null }
if (Test-Path $payloadDir) { Remove-Item -Path $payloadDir -Recurse -Force | Out-Null }
if (Test-Path $outputPath) { Remove-Item -Path $outputPath -Force | Out-Null }

# 3. Extract the downloaded zip
Write-Host "`n1. Extracting iOS app artifacts from Downloads..." -ForegroundColor Cyan
Expand-Archive -Path $zipPath -DestinationPath $tempExtract

# 4. Create Payload directory and copy Runner.app
Write-Host "2. Creating Sideloadly Payload packaging structure..." -ForegroundColor Cyan
New-Item -ItemType Directory -Path $payloadDir | Out-Null
Copy-Item -Path (Join-Path $tempExtract "Runner.app") -Destination $payloadDir -Recurse

# 5. Compress Payload folder to sela.ipa
Write-Host "3. Generating sela.ipa package..." -ForegroundColor Cyan
$zipOutput = Join-Path $targetDir "sela.zip"
if (Test-Path $zipOutput) { Remove-Item -Path $zipOutput -Force | Out-Null }
Compress-Archive -Path $payloadDir -DestinationPath $zipOutput

# Rename .zip to .ipa
Rename-Item -Path $zipOutput -NewName "sela.ipa"

# 6. Cleanup
Remove-Item -Path $tempExtract -Recurse -Force | Out-Null
Remove-Item -Path $payloadDir -Recurse -Force | Out-Null

Write-Host "`n==============================================" -ForegroundColor Green
Write-Host "SUCCESS! Your iPhone application package has been generated!" -ForegroundColor Green
Write-Host "Location: $outputPath" -ForegroundColor Yellow
Write-Host "==============================================" -ForegroundColor Green
Write-Host "`nYou can now drag-and-drop this 'sela.ipa' file directly into Sideloadly!" -ForegroundColor White
Write-Host ""

# Open folder in Explorer and select the file
explorer.exe "/select,$outputPath"

Read-Host "Press Enter to exit..."
