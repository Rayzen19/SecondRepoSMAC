@echo off
echo ========================================
echo   STARTING REAL-TIME MESSAGING SYSTEM
echo ========================================
echo.
echo This will start the necessary services for real-time messaging:
echo  1. Queue Worker (processes broadcast events)
echo  2. Vite Dev Server (provides Laravel Echo)
echo.
echo Keep this window open while using the messaging system.
echo Press Ctrl+C to stop all services.
echo.
echo ========================================
echo.

cd /d "%~dp0"

start "Queue Worker" cmd /k "php artisan queue:work --tries=3"
start "Vite Dev Server" cmd /k "npm run dev"

echo.
echo Services started in separate windows!
echo Close those windows or press Ctrl+C in them to stop.
echo.
pause
