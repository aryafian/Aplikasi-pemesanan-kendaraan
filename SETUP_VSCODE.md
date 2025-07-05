# Setup VehicleFlow di VS Code - Full Stack

## Prerequisites
- VS Code installed
- PHP 7.4+ installed
- Node.js 18+ installed
- XAMPP (opsional, untuk MySQL)

## 1. Setup Project

### Download/Clone Project
```bash
# Download project files ke local directory
# Extract semua files ke folder 'vehicleflow'
cd vehicleflow
```

### Install Dependencies
```bash
# Install Node.js dependencies untuk frontend
npm install
```

## 2. Setup Database

### Opsi A: MySQL dengan XAMPP
1. Start XAMPP Control Panel
2. Start Apache dan MySQL
3. Buka phpMyAdmin (http://localhost/phpmyadmin)
4. Create database dan import schema:
```sql
CREATE DATABASE vehicleflow;
USE vehicleflow;
SOURCE api/database/schema.sql;
```

### Opsi B: SQLite (Otomatis)
Database SQLite akan dibuat otomatis saat menjalankan PHP server.

## 3. Konfigurasi VS Code

### Recommended Extensions
```
- PHP Intelephense
- PHP Debug
- Thunder Client (untuk test API)
- Live Server
- MySQL (jika pakai MySQL)
```

### VS Code Tasks (Buat file .vscode/tasks.json)
```json
{
    "version": "2.0.0",
    "tasks": [
        {
            "label": "Start PHP Backend",
            "type": "shell",
            "command": "php",
            "args": ["-S", "localhost:8080", "-t", "api/public"],
            "group": "build",
            "presentation": {
                "echo": true,
                "reveal": "always",
                "panel": "new"
            },
            "problemMatcher": []
        },
        {
            "label": "Start Frontend Dev",
            "type": "shell",
            "command": "npm",
            "args": ["run", "dev"],
            "group": "build",
            "presentation": {
                "echo": true,
                "reveal": "always",
                "panel": "new"
            },
            "problemMatcher": []
        },
        {
            "label": "Build Frontend",
            "type": "shell",
            "command": "npm",
            "args": ["run", "build:client"],
            "group": "build",
            "presentation": {
                "echo": true,
                "reveal": "always",
                "panel": "shared"
            },
            "problemMatcher": []
        }
    ]
}
```

### VS Code Launch Configuration (.vscode/launch.json)
```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Launch PHP Backend",
            "type": "php",
            "request": "launch",
            "program": "${workspaceFolder}/api/public/index.php",
            "cwd": "${workspaceFolder}",
            "port": 9003,
            "runtimeArgs": [
                "-S",
                "localhost:8080",
                "-t",
                "api/public"
            ]
        }
    ]
}
```

## 4. Menjalankan Aplikasi

### Method 1: Manual (2 Terminal)

**Terminal 1 - Backend PHP:**
```bash
cd vehicleflow
php -S localhost:8080 -t api/public
```

**Terminal 2 - Frontend React:**
```bash
cd vehicleflow
npm run dev
```

### Method 2: VS Code Tasks
1. `Ctrl+Shift+P` → "Tasks: Run Task"
2. Pilih "Start PHP Backend"
3. Buka terminal baru
4. `Ctrl+Shift+P` → "Tasks: Run Task"
5. Pilih "Start Frontend Dev"

### Method 3: Script Automation
Buat file `start.bat` (Windows) atau `start.sh` (Linux/Mac):

**start.bat:**
```batch
@echo off
echo Starting VehicleFlow Full Stack...
start cmd /k "php -S localhost:8080 -t api/public"
timeout /t 2
start cmd /k "npm run dev"
echo Both servers started!
```

**start.sh:**
```bash
#!/bin/bash
echo "Starting VehicleFlow Full Stack..."
php -S localhost:8080 -t api/public &
npm run dev &
echo "Both servers started!"
wait
```

## 5. Akses Aplikasi

- **Frontend React**: http://localhost:5173 (Vite default)
- **Backend PHP API**: http://localhost:8080
- **Full App**: Frontend akan otomatis connect ke backend

## 6. Development Workflow

### File Structure
```
vehicleflow/
├── api/                     # PHP Backend
│   ├── public/index.php     # Entry point
│   ├── application/
│   │   ├── controllers/     # API Controllers
│   │   ├── config/         # Database config
│   │   └── core/           # Base classes
│   └── database/schema.sql  # Database schema
├── client/                  # React Frontend
│   ├── src/
│   ├── public/
│   └── package.json
├── .vscode/                # VS Code config
└── package.json            # Root package.json
```

### Testing API
Gunakan Thunder Client di VS Code:
- `GET http://localhost:8080/api/auth/me`
- `POST http://localhost:8080/api/auth/login`

### Hot Reload
- Frontend: Otomatis reload via Vite
- Backend: Restart PHP server untuk perubahan

## 7. Login Credentials
- **Admin**: `admin` / `password`
- **User**: `user` / `password`
- **Approver1**: `approver1` / `password`
- **Approver2**: `approver2` / `password`

## 8. Troubleshooting

### Port Conflicts
Jika port 8080 sudah digunakan:
```bash
php -S localhost:8081 -t api/public
```
Update frontend untuk connect ke port 8081.

### Database Connection
Check database config di `api/application/config/database.php` atau `database_sqlite.php`.

### CORS Issues
Headers CORS sudah di-setup di base controller PHP.

## 9. Production Build
```bash
# Build frontend untuk production
npm run build:client

# Files akan ada di dist/public/
# Deploy api/ folder ke web server dengan PHP support
```

Dengan setup ini, Anda bisa development full stack VehicleFlow di VS Code dengan hot reload dan debugging support!