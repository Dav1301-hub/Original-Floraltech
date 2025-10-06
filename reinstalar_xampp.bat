@echo off
echo ==========================================================
echo           REINSTALACION AUTOMATICA DE XAMPP
echo ==========================================================
echo.

REM 1. Verificar permisos de administrador
net session >nul 2>&1
if %errorLevel% == 0 (
    echo [OK] Ejecutandose con permisos de administrador
) else (
    echo [ERROR] Este script necesita permisos de administrador
    echo Por favor, ejecuta como administrador
    pause
    exit /b 1
)

echo.
echo 1. Deteniendo servicios de XAMPP...
taskkill /f /im httpd.exe 2>nul
taskkill /f /im mysqld.exe 2>nul
taskkill /f /im xampp-control.exe 2>nul

echo.
echo 2. Creando backup adicional...
if exist "C:\xampp\htdocs" (
    xcopy "C:\xampp\htdocs" "C:\backup_xampp_htdocs" /E /I /Y
    echo [OK] Backup de htdocs creado
)

echo.
echo 3. Desinstalando XAMPP actual...
if exist "C:\xampp\uninstall.exe" (
    echo Ejecutando desinstalador...
    "C:\xampp\uninstall.exe" /S
    timeout /t 10
) else (
    echo No se encontro desinstalador, eliminando carpeta...
    rmdir /s /q "C:\xampp" 2>nul
)

echo.
echo 4. Limpiando registros...
reg delete "HKEY_LOCAL_MACHINE\SOFTWARE\XAMPP" /f 2>nul
reg delete "HKEY_CURRENT_USER\SOFTWARE\XAMPP" /f 2>nul

echo.
echo 5. Esperando descarga de XAMPP...
echo INSTRUCCIONES:
echo 1. Descarga XAMPP desde: https://www.apachefriends.org/download.html
echo 2. Ejecuta el instalador como administrador
echo 3. Instala en C:\xampp (ruta predeterminada)
echo 4. Una vez instalado, presiona cualquier tecla para continuar...
echo.
pause

echo.
echo 6. Restaurando proyecto...
if exist "C:\backup_xampp_htdocs\Original-Floraltech" (
    xcopy "C:\backup_xampp_htdocs\Original-Floraltech" "C:\xampp\htdocs\Original-Floraltech" /E /I /Y
    echo [OK] Proyecto restaurado
)

echo.
echo 7. Restaurando base de datos...
if exist "C:\xampp\htdocs\Original-Floraltech\backup_flores" (
    xcopy "C:\xampp\htdocs\Original-Floraltech\backup_flores" "C:\xampp\mysql\data\flores" /E /I /Y
    echo [OK] Base de datos restaurada
)

echo.
echo ==========================================================
echo           REINSTALACION COMPLETADA
echo ==========================================================
echo.
echo Proximos pasos:
echo 1. Inicia XAMPP Control Panel
echo 2. Arranca Apache y MySQL
echo 3. Ve a: http://localhost/Original-Floraltech
echo.
pause