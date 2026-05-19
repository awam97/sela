@echo off
title Sela Mobile App - Localhost Preview
color 0B
echo ====================================================
echo          SELA MOBILE APP LOCALHOST RUNNER           
echo ====================================================
echo Starting Sela Mobile App in Google Chrome...
echo Please wait, launching Chrome with disabled web security (CORS bypass)...
echo.

cd /d "c:\Users\porta\Desktop\Sela\mobile_app"
call flutter run -d chrome --web-browser-flag "--disable-web-security"

echo.
echo ====================================================
echo Execution completed or halted.
echo ====================================================
echo.
pause
