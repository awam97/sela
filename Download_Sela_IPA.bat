@echo off
title Sela App Downloader
color 0B
echo ====================================================
echo             SELA CLOUD APP DOWNLOADER              
echo ====================================================
echo Downloading the latest crash-proof sela.ipa...
echo Please wait, fetching release assets from GitHub...
echo.

powershell -ExecutionPolicy Bypass -Command "[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://github.com/awam97/sela/releases/download/latest/sela.ipa' -OutFile 'c:\Users\porta\Desktop\Sela\sela.ipa' -UserAgent 'Mozilla/5.0'"

if %ERRORLEVEL% NEQ 0 (
    color 0C
    echo.
    echo [ERROR] Failed to download sela.ipa!
    echo Please make sure the GitHub build is finished and your internet is connected.
    echo.
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo ====================================================
echo SUCCESS! sela.ipa has been successfully downloaded!
echo Path: c:\Users\porta\Desktop\Sela\sela.ipa
echo ====================================================
echo.

explorer.exe /select,"c:\Users\porta\Desktop\Sela\sela.ipa"
timeout /t 5
