@echo off
title Sela Mobile App - Localhost Preview
color 0B
echo ====================================================
echo          SELA MOBILE APP LOCALHOST RUNNER           
echo ====================================================
echo Starting Sela Mobile App in Google Chrome...
echo Please wait, initializing local hot-reload server...
echo.

cd /d "c:\Users\porta\Desktop\Sela\mobile_app"
call flutter run -d chrome

echo.
echo ====================================================
echo Execution completed or halted.
echo ====================================================
echo.
pause
