# Cara Mengganti ke Backend PHP CodeIgniter 3

## Status Saat Ini
- ✅ Backend Node.js masih berjalan (workflow otomatis)
- ✅ Backend PHP CodeIgniter 3 sudah siap
- ✅ Frontend React kompatibel dengan kedua backend

## Langkah Mengganti ke PHP Backend

### 1. Hentikan Workflow Node.js
Di Replit Console, tekan `Ctrl+C` untuk menghentikan workflow yang sedang berjalan.

### 2. Jalankan PHP Server
```bash
php -S 0.0.0.0:5000 -t api/public
```

### 3. Atau Gunakan Script Startup
```bash
./start_php_server.sh
```

## Test Backend PHP
Untuk memastikan PHP backend berjalan:
```bash
php test_php_backend.php
```

## Untuk Development XAMPP
1. Copy folder `api` ke `htdocs` XAMPP
2. Import database: `api/database/schema.sql`
3. Akses: `http://localhost/api/public`

## Login Credentials
- Admin: `admin` / `password`
- User: `user` / `password`
- Approver1: `approver1` / `password`
- Approver2: `approver2` / `password`

## Perbedaan Backend

### Node.js (Saat Ini)
- Database: PostgreSQL
- Session: Express Session
- Port: 5000

### PHP CodeIgniter 3 (Target)
- Database: MySQL/SQLite
- Session: PHP Session
- Port: 5000 (sama)

Frontend React akan tetap berfungsi normal dengan kedua backend karena API endpoint-nya identik.