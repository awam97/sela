# Sela iOS App Packager for Sideloadly
$ErrorActionPreference = 'Stop'

Write-Host "==============================================" -ForegroundColor Yellow
Write-Host "    SELA IOS APP PACKAGER FOR SIDELOADLY      " -ForegroundColor Yellow
Write-Host "==============================================" -ForegroundColor Yellow

# 1. Locate the downloaded Sela iOS zip
$downloadsFolder = "$env:USERPROFILE\Downloads"
$zipPath = Join-Path $downloadsFolder "sela-ios-release-app.zip"

if (!(Test-Path $zipPath)) {
    Write-Host "`nError: Could not find 'sela-ios-release-app.zip' in your Downloads folder!" -ForegroundColor Red
    Write-Host "Please download it first from GitHub Actions:" -ForegroundColor White
    Write-Host "https://github.com/awam97/sela/actions" -ForegroundColor Cyan
    Write-Host "`nThen run this script again." -ForegroundColor Yellow
    Write-Host ""
    Read-Host "Press Enter to exit..."
    exit 1
}

# 2. Setup paths
$targetDir = "c:\Users\porta\Desktop\Sela"
$payloadDir = Join-Path $targetDir "Payload"
$runnerAppDir = Join-Path $payloadDir "Runner.app"
$outputPath = Join-Path $targetDir "sela.ipa"

if (Test-Path $payloadDir) { Remove-Item -Path $payloadDir -Recurse -Force | Out-Null }
if (Test-Path $outputPath) { Remove-Item -Path $outputPath -Force | Out-Null }

# 3. Create Payload/Runner.app folder
Write-Host "`n1. Creating Payload structure..." -ForegroundColor Cyan
New-Item -ItemType Directory -Path $runnerAppDir | Out-Null

# 4. Extract downloaded zip directly into Payload/Runner.app
Write-Host "2. Extracting iOS application files..." -ForegroundColor Cyan
Expand-Archive -Path $zipPath -DestinationPath $runnerAppDir

# 5. Compress Payload folder to sela.ipa
Write-Host "3. Packaging Payload into sela.ipa..." -ForegroundColor Cyan
$zipOutput = Join-Path $targetDir "sela.zip"
if (Test-Path $zipOutput) { Remove-Item -Path $zipOutput -Force | Out-Null }
Compress-Archive -Path $payloadDir -DestinationPath $zipOutput

# Rename .zip to .ipa
Rename-Item -Path $zipOutput -NewName "sela.ipa"

# 6. Cleanup
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
