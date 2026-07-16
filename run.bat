@echo off
title SMS Gateway MVC Server
echo ===================================================
echo Menjalankan Server PHP Lokal untuk SMS Gateway...
echo.
echo Dashboard Web dapat diakses di: http://localhost:8000
echo.
echo Jangan tutup jendela ini jika ingin aplikasi Android 
echo dapat menarik (sync) data dari server ini.
echo Tekan Ctrl+C untuk menghentikan server.
echo ===================================================
php -S localhost:8000
pause
