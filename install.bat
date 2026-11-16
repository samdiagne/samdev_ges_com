@echo off
setlocal enabledelayedexpansion
title Deploiement Restaurant POS - Installation Initiale
color 0A

echo =====================================================================
echo           INSTALLATION & DEPLOIEMENT - RESTAURANT POS
echo =====================================================================
echo.

:: 1. Verification de PHP
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERREUR] PHP n'est pas installe ou pas accessible dans le PATH Windows.
    echo Veuillez installer XAMPP et ajouter C:\xampp\php a vos variables d'environnement.
    echo.
    pause
    exit /b 1
)

:: 2. Verification de Composer
where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERREUR] Composer n'est pas installe.
    echo Veuillez telecharger et installer Composer depuis https://getcomposer.org
    echo.
    pause
    exit /b 1
)

echo [1/7] Verification du fichier de configuration .env...
if not exist .env (
    if exist .env.example (
        copy .env.example .env >nul
        echo       .env cree a partir de .env.example.
    ) else (
        echo [ERREUR] Fichier .env.example introuvable.
        pause
        exit /b 1
    )
) else (
    echo       Fichier .env existant detecte.
)
echo.

echo [2/7] Installation des dependances PHP (Composer)...
call composer install --no-interaction --prefer-dist --optimize-autoloader
if %errorlevel% neq 0 (
    echo [ATTENTION] Verifiez la connexion internet pour Composer.
)
echo.

echo [3/7] Generation de la cle d'application...
call php artisan key:generate --force
echo.

echo [4/7] Creation du lien symbolique du stockage...
call php artisan storage:link
echo.

echo [5/7] Migration et initialisation de la base de donnees...
echo IMPORTANT : Assurez-vous que MySQL (XAMPP) est demarre et que la base de donnees
echo specifiee dans .env (ex: restaurant_db) a ete creee dans phpMyAdmin.
echo.
set /p RUN_MIGRATE="Voulez-vous executer les migrations et seeders maintenant ? (O/N) : "
if /i "%RUN_MIGRATE%"=="O" (
    call php artisan migrate --seed --force
) else (
    echo       Migrations ignorees pour le moment. Lancez manuellement 'php artisan migrate --seed'.
)
echo.

echo [6/7] Nettoyage et optimisation des caches...
call php artisan optimize:clear
echo.

echo [7/7] Compilation des assets frontend (si Node.js est installe)...
where npm >nul 2>nul
if %errorlevel% equ 0 (
    call npm install --no-audit --prefer-offline
    call npm run build
) else (
    echo       [INFO] Node/NPM non detecte. Si le dossier public/build existe deja, ce n'est pas requis.
)
echo.

echo =====================================================================
echo                  INSTALLATION TERMINEE AVEC SUCCES !
echo =====================================================================
echo.
echo Identifiants par defaut :
echo   - Admin    : admin@my-business.sn / Admin@2025!
echo   - Caissier : caisse@my-business.sn / Caisse@2025!
echo.
echo Pour lancer le serveur de developpement en local :
echo   php artisan serve
echo.
pause
