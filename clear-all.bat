@echo off
cd /d "%~dp0"
php artisan cache:clear
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan view:clear
echo All caches cleared successfully!
pause
