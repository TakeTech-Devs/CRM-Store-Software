@echo off
title RightAid Store
color 0A

:: Resolve the project directory relative to this bat file's location
set "PROJECT_DIR=%~dp0"
cd /d "%PROJECT_DIR%"

:: Use php from system PATH (same as running manually in cmd)
set "PHP=php"

:: Check PHP exists
where php >nul 2>&1
if errorlevel 1 (
    echo [ERROR] PHP not found in PATH.
    echo Please make sure XAMPP is installed and PHP is in your system PATH.
    pause
    exit /b 1
)

:: Find XAMPP on whichever drive it is installed: first next to the PHP on PATH
:: (...\xampp\php\php.exe), then C:\xampp ... G:\xampp.
set "XAMPP_DIR="
for /f "delims=" %%P in ('where php') do if not defined XAMPP_DIR if exist "%%~dpP..\mysql\bin\mysqld.exe" for %%I in ("%%~dpP..") do set "XAMPP_DIR=%%~fI"
for %%D in (C D E F G) do if not defined XAMPP_DIR if exist "%%D:\xampp\mysql\bin\mysqld.exe" set "XAMPP_DIR=%%D:\xampp"
if not defined XAMPP_DIR (
    echo [ERROR] Could not find XAMPP ^(MySQL^).
    echo Looked next to PHP and in C:\xampp to G:\xampp.
    pause
    exit /b 1
)
set "MYSQL=%XAMPP_DIR%\mysql\bin\mysql.exe"
set "MYSQLADMIN=%XAMPP_DIR%\mysql\bin\mysqladmin.exe"

:: Start MySQL if it is not already answering. (The old xampp_start.exe no longer
:: exists in current XAMPP, so MySQL is started directly. Apache is not needed:
:: the app is served by "php artisan serve" below.)
:: The port is read from XAMPP's own my.ini (some installs use 3308, not 3306).
set "MYSQL_PORT=3306"
for /f "tokens=2 delims==" %%A in ('findstr /b /c:"port=" "%XAMPP_DIR%\mysql\bin\my.ini"') do set "MYSQL_PORT=%%A"
"%MYSQLADMIN%" -u root -h 127.0.0.1 -P %MYSQL_PORT% ping >nul 2>&1
if errorlevel 1 (
    echo [INFO] Starting MySQL on port %MYSQL_PORT%...
    powershell -NoProfile -Command "Start-Process -WindowStyle Hidden -FilePath '%XAMPP_DIR%\mysql\bin\mysqld.exe' -ArgumentList '--defaults-file=%XAMPP_DIR%\mysql\bin\my.ini','--standalone'"
    set "MYSQL_UP="
    for /L %%i in (1,1,60) do if not defined MYSQL_UP ("%MYSQLADMIN%" -u root -h 127.0.0.1 -P %MYSQL_PORT% ping >nul 2>&1 && set "MYSQL_UP=1" || ping -n 2 127.0.0.1 >nul)
    if not defined MYSQL_UP (
        echo [ERROR] MySQL did not start within 60 seconds.
        echo Open the XAMPP Control Panel and start MySQL, then run this again.
        pause
        exit /b 1
    )
)

:: ─── FIRST RUN SETUP ───────────────────────────────────────────────
if not exist "%PROJECT_DIR%rightaid.installed" (
    echo [SETUP] First run detected. Setting up RightAid...
    echo.

    echo [1/5] Installing dependencies...
    composer install --no-interaction --prefer-dist --optimize-autoloader
    echo.

    echo [2/5] Setting up environment...
    if not exist "%PROJECT_DIR%.env" (
        copy "%PROJECT_DIR%.env.example" "%PROJECT_DIR%.env" >nul
    )
    "%PHP%" artisan key:generate --force
    echo.

    echo [3/5] Creating database...
    "%MYSQL%" -u root -h 127.0.0.1 -P %MYSQL_PORT% -e "CREATE DATABASE IF NOT EXISTS rightaid_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
    echo.

    echo [4/5] Running migrations...
    "%PHP%" artisan migrate --force
    echo.

    echo [5/5] Finalising...
    "%PHP%" artisan storage:link >nul 2>&1
    "%PHP%" artisan config:cache >nul 2>&1
    echo.

    :: Write installed flag
    type nul > "%PROJECT_DIR%rightaid.installed"

    echo [SETUP] Setup complete!
    echo.
)

:: ─── LAUNCH ────────────────────────────────────────────────────────
echo [INFO] Starting RightAid Store...
echo [INFO] Opening http://127.0.0.1:8000
echo [INFO] Close this window to stop the server.
echo.

:: Open browser after 2 seconds
start /b cmd /c "timeout /t 2 >nul && start http://127.0.0.1:8000"

:: Start Laravel server (keeps window open)
"%PHP%" artisan serve
