@echo off
title Menjalankan Server SIMOR BMS
color 0b
echo ========================================================
echo        MENJALANKAN SERVER SIMOR BMS (PORT 8080)
echo ========================================================
echo.
cd /d "C:\xampp\htdocs\Project Besmindo Summary"

echo Memastikan server berjalan pada http://localhost:8080 ...
echo Tekan Ctrl + C di jendela ini jika ingin mematikan server.
echo.
php spark serve --port 8080
pause
