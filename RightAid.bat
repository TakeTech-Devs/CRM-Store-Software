@echo off
title Starting RightAid

:: Set project directory
cd /d C:\Users\saika_0sflwby\TTD\RightAid-store-admin\CRM-Store-Software

:: Start Laravel server
start cmd /k "php artisan serve"

:: Optional: Start MySQL (for standalone installations like XAMPP)
:: start "" "C:\xampp\mysql\bin\mysqld.exe"

:: Open project in browser
timeout /t 2 > nul
start http://127.0.0.1:8000

exit
