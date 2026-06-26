@echo off
title RightAid Store
color 0A

:: Resolve the project directory relative to this bat file's location
set "PROJECT_DIR=%~dp0"
cd /d "%PROJECT_DIR%"

:: XAMPP paths
set "PHP=C:\xampp\php\php.exe"
set "MYSQL=C:\xampp\mysql\bin\mysql.exe"
set "MYSQLD=C:\xampp\mysql\bin\mysqld.exe"
set "MYSQLADMIN=C:\xampp\mysql\bin\mysqladmin.exe"
set "XAMPP_DIR=C:\xampp"

:: Check PHP exists
if not exist "%PHP%" (
    echo [ERROR] PHP not found at %PHP%
    echo Please make sure XAMPP is installed.
    pause
    exit /b 1
)

:: Start Apache if not already running
tasklist /FI "IMAGENAME eq httpd.exe" 2>nul | find /I "httpd.exe" >nul
if errorlevel 1 (
    echo [INFO] Starting Apache...
    start "" "%XAMPP_DIR%\apache\bin\httpd.exe"
    timeout /t 2 >nul
)

:: Start MySQL if not already running
"%MYSQLADMIN%" -u root ping >nul 2>&1
if errorlevel 1 (
    echo [INFO] Starting MySQL...
    start "" "%MYSQLD%" --console
    timeout /t 4 >nul
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
    "%MYSQL%" -u root -e "CREATE DATABASE IF NOT EXISTS rightaid_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
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
