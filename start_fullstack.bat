@echo off
echo ========================================
echo    VehicleFlow Full Stack Startup
echo ========================================
echo.
echo Starting PHP Backend (CodeIgniter 3)...
start "PHP Backend" cmd /k "php -S localhost:8080 -t api/public"
echo Backend will be available at: http://localhost:8080
echo.
echo Waiting 3 seconds...
timeout /t 3 /nobreak > nul
echo.
echo Starting React Frontend...
start "React Frontend" cmd /k "npm run dev"
echo Frontend will be available at: http://localhost:5173
echo.
echo ========================================
echo    Both servers are starting!
echo ========================================
echo.
echo Login Credentials:
echo - Admin: admin / password
echo - User: user / password
echo - Approver1: approver1 / password
echo - Approver2: approver2 / password
echo.
echo Press any key to close this window...
pause > nul