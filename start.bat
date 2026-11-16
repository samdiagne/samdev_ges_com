@echo off
title Demarrage Serveur - Restaurant POS
color 0B

echo =====================================================================
echo                DEMARRAGE DE RESTAURANT POS
echo =====================================================================
echo.
echo Verification de MySQL...
timeout /t 1 >nul

echo Demarrage du serveur local sur http://localhost:8000 ...
echo Appuyez sur Ctrl + C pour arreter le serveur.
echo.

start http://localhost:8000
php artisan serve --host=127.0.0.1 --port=8000
pause
