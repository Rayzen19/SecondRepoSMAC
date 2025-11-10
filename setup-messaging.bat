@echo off
echo ====================================
echo Real-Time Messaging System Setup
echo ====================================
echo.

echo Step 1: Checking if Pusher package is installed...
composer show pusher/pusher-php-server >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Pusher package not found!
    echo Installing Pusher...
    composer require pusher/pusher-php-server
) else (
    echo [OK] Pusher package is installed
)
echo.

echo Step 2: Clearing Laravel cache...
php artisan config:clear
php artisan cache:clear
echo [OK] Cache cleared
echo.

echo Step 3: Checking .env configuration...
findstr /C:"PUSHER_APP_KEY" .env >nul 2>&1
if %errorlevel% neq 0 (
    echo [WARNING] PUSHER_APP_KEY not found in .env
    echo Please add your Pusher credentials to .env file
) else (
    echo [OK] Pusher configuration found in .env
)
echo.

echo ====================================
echo Setup Summary
echo ====================================
echo 1. Pusher package: Installed
echo 2. Cache: Cleared
echo 3. Configuration: Please verify .env
echo.
echo ====================================
echo Next Steps:
echo ====================================
echo 1. Get Pusher credentials from https://pusher.com
echo 2. Update .env with your Pusher credentials
echo 3. Run: php artisan queue:work
echo 4. Test messaging in your browser!
echo.
echo For detailed instructions, see:
echo - QUICK_START_MESSAGING.md
echo - REALTIME_MESSAGING_SETUP.md
echo ====================================
echo.
pause
