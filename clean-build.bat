@echo off
echo ===================================================
echo   NexRun Project Cleaner ^& Builder
echo ===================================================

echo.
echo [1/3] Clearing Backend Cache, Config, and Views...
cd backend
php artisan optimize:clear
if %errorlevel% neq 0 (
    echo Error clearing backend cache!
    exit /b %errorlevel%
)

echo.
echo [2/3] Returning to root directory...
cd ..

echo.
echo [3/3] Installing NPM dependencies and Building Frontend...
:: Checking if Bun is available, else fallback to NPM
where bun >nul 2>&1
if %errorlevel% equ 0 (
    echo Bun detected. Using Bun for faster build...
    bun install
    bun run build
) else (
    echo Using NPM for build...
    npm install
    npm run build
)

if %errorlevel% neq 0 (
    echo Error building frontend!
    exit /b %errorlevel%
)

echo.
echo ===================================================
echo   Clean and Build completed successfully!
echo ===================================================
pause
