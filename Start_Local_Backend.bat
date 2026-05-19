@echo off
title Sela PHP Backend - Localhost Server
color 0E
echo ====================================================
echo          SELA PHP BACKEND LOCALHOST RUNNER           
echo ====================================================
echo Starting local hot-reload PHP development server...
echo Pointing to local database over cPanel remote channel...
echo.
echo Server URL: http://localhost:8080
echo.
echo [INFO] Keep this window open while testing the mobile app!
echo ====================================================
echo.

cd /d "c:\Users\porta\Desktop\Sela"
php spark serve --port 8080

echo.
echo ====================================================
echo Server stopped or halted.
echo ====================================================
echo.
pause
