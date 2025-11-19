@echo off
echo ============================================
echo   Starting Laravel Queue Worker
echo ============================================
echo.
cd /d "c:\xampp\htdocs\NEWSMAC"
echo Current directory: %CD%
echo.
echo Starting queue worker...
echo Press Ctrl+C to stop
echo.
php artisan queue:work --tries=3 --timeout=60
