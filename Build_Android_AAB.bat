@echo off
title Sela Platform - Android AppBundle Builder
color 0A
echo ====================================================
2: echo          SELA PLATFORM - ANDROID APP BUNDLE BUILDER
3: echo ====================================================
4: echo This tool compiles your Flutter app into a signed
5: echo production App Bundle (.aab) ready for Google Play!
6: echo.
7: echo Prerequisites:
8: echo - Flutter SDK installed and in PATH
9: echo - Android SDK installed
10: echo.
11: echo [INFO] Building in RELEASE mode with your secure signature key...
12: echo ====================================================
13: echo.
14: 
15: cd /d "c:\Users\porta\Desktop\Sela\mobile_app"
16: 
17: echo [1/3] Cleaning temporary build files...
18: call flutter clean
19: if %ERRORLEVEL% NEQ 0 (
20:     echo.
21:     echo [ERROR] Flutter clean failed! Make sure Flutter is closed in VS Code/Android Studio.
22:     pause
23:     exit /b %ERRORLEVEL%
24: )
25: 
26: echo.
27: echo [2/3] Fetching dependencies...
28: call flutter pub get
29: 
30: echo.
31: echo [3/3] Compiling signed Production App Bundle (.aab)...
32: call flutter build appbundle --release
33: if %ERRORLEVEL% NEQ 0 (
34:     echo.
35:     echo [ERROR] Compilation failed! Review the errors above.
36:     pause
37:     exit /b %ERRORLEVEL%
38: )
39: 
40: echo.
41: echo ====================================================
42: echo SUCCESS! YOUR PRODUCTION APP BUNDLE IS READY!
43: echo ====================================================
44: echo Path: c:\Users\porta\Desktop\Sela\mobile_app\build\app\outputs\bundle\release\app-release.aab
45: echo.
46: echo You can upload this 'app-release.aab' file directly
47: echo to Google Play Console for publication!
48: echo ====================================================
49: echo.
50: 
51: explorer.exe "/select,c:\Users\porta\Desktop\Sela\mobile_app\build\app\outputs\bundle\release\app-release.aab"
52: 
53: pause
