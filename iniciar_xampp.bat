@echo off
echo Iniciando Apache y MySQL para XAMPP...
echo.

REM Cambiar al directorio de XAMPP
cd /d C:\xampp

REM Iniciar Apache
echo Iniciando Apache...
start "" "C:\xampp\apache\bin\httpd.exe"

REM Esperar un momento
timeout /t 3 /nobreak > nul

REM Iniciar MySQL
echo Iniciando MySQL...
start "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file=C:\xampp\mysql\bin\my.ini --standalone --console

echo.
echo ===================================================
echo  XAMPP iniciado correctamente
echo ===================================================
echo  Apache: http://localhost
echo  phpMyAdmin: http://localhost/phpmyadmin
echo  Tu aplicación: http://localhost/Original-Floraltech
echo ===================================================
echo.
echo Presiona cualquier tecla para continuar...
pause > nul