@echo off
setlocal enabledelayedexpansion
color 0A
title OptiSpace - Automated Setup & Launch

:: ============================================================================
:: OPTISPACE ONE-CLICK LAUNCHER
:: Automated setup script for hackathon judges
:: ============================================================================

echo.
echo ╔════════════════════════════════════════════════════════════════╗
echo ║                  OPTISPACE SMART PARKING SYSTEM                ║
echo ║                    Automated Setup Wizard                      ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.

:: ============================================================================
:: STEP 1: CHECK FOR XAMPP/PHP
:: ============================================================================
echo [1/3] Checking for XAMPP/PHP...

php --version >nul 2>&1
if errorlevel 1 (
    color 0C
    echo.
    echo  ═══════════════════════════════════════════════════════════════
    echo   ERROR: XAMPP is missing.
    echo  ═══════════════════════════════════════════════════════════════
    echo.
    echo  PHP is not installed or not in your system PATH.
    echo  Opening download page...
    echo.
    
    start https://www.apachefriends.org/download.html
    
    echo  Please install XAMPP and restart this script.
    echo.
    pause
    exit /b 1
)

echo  [OK] PHP Found
php --version | findstr /R "PHP"
echo.

:: ============================================================================
:: STEP 2: DATABASE SETUP
:: ============================================================================
echo [2/3] Setting up Database...

:: Check if MySQL is accessible
mysql --version >nul 2>&1
if errorlevel 1 (
    color 0E
    echo  [WARNING] MySQL not found in PATH.
    echo  Please ensure XAMPP MySQL service is running.
    echo  Starting server without database import...
    echo.
    goto :skip_db
)

:: Create database
echo  Creating database 'optispace'...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS optispace;" 2>nul
if errorlevel 1 (
    echo  [WARNING] Could not create database.
) else (
    echo  [OK] Database created/verified
)

:: Import SQL file
if exist "setup_files\optispace_db.sql" (
    echo  Importing schema from setup_files\optispace_db.sql...
    mysql -u root optispace < "setup_files\optispace_db.sql" 2>nul
    if errorlevel 1 (
        color 0E
        echo  [ERROR] Database import failed.
        echo  Please check if MySQL service is running.
        pause
        goto :skip_db
    ) else (
        echo  [OK] Database Imported
    )
) else (
    color 0E
    echo  [ERROR] SQL file not found at: setup_files\optispace_db.sql
    echo  Please ensure the file exists and try again.
    pause
    goto :skip_db
)

echo.

:skip_db

:: ============================================================================
:: STEP 3: LAUNCH SERVER
:: ============================================================================
echo [3/3] Starting Web Server...
echo.
echo  ═══════════════════════════════════════════════════════════════
echo   Server Address: http://localhost:8000
echo   Document Root:  %CD%
echo  ═══════════════════════════════════════════════════════════════
echo.

:: Open browser
echo  Opening dashboard in browser...
timeout /t 2 /nobreak >nul
start http://localhost:8000

echo.
echo ╔════════════════════════════════════════════════════════════════╗
echo ║               OPTISPACE SYSTEM: ONLINE ✓                       ║
echo ╚════════════════════════════════════════════════════════════════╝
echo.
echo  Press CTRL+C to stop the server
echo.

:: Start PHP built-in server
php -S localhost:8000

:: If server stops
echo.
echo  Server stopped.
pause
