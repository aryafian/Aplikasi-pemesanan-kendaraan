# Solusi Error Windows - VehicleFlow

## Error yang Terjadi
```
'NODE_ENV' is not recognized as an internal or external command
```

## Penyebab
Script `npm run dev` menggunakan syntax Linux/Mac (`NODE_ENV=development`), tidak kompatibel dengan Windows Command Prompt.

## Solusi

### 1. Gunakan Backend PHP (Rekomendasi)
Tidak perlu Node.js sama sekali, langsung gunakan PHP backend:

```bash
# Terminal 1: Start PHP Backend
php -S localhost:8080 -t api/public

# Terminal 2: Start Frontend
npx vite --port 5173
```

### 2. Gunakan PowerShell (Bukan CMD)
PowerShell mendukung syntax environment variable yang lebih modern:

```powershell
# Di PowerShell
$env:NODE_ENV="development"; tsx server/index.ts
```

### 3. Install cross-env (Universal)
```bash
npm install -g cross-env
```

Lalu edit package.json:
```json
"scripts": {
  "dev": "cross-env NODE_ENV=development tsx server/index.ts"
}
```

### 4. Script Windows (.bat)
Gunakan file batch yang sudah saya buat:
```bash
start_fullstack.bat
```

### 5. Manual Commands untuk Windows
```batch
# Start PHP Backend
php -S localhost:8080 -t api/public

# Start Frontend (terminal baru)
npx vite

# Atau gunakan Vite langsung
npm run build:client
```

## Rekomendasi untuk XAMPP
Karena Anda sudah menggunakan XAMPP, lebih baik fokus ke PHP backend:

### Setup XAMPP
1. Copy folder `api` ke `C:\xampp82\htdocs\vehicleflow\`
2. Start Apache dan MySQL di XAMPP Control Panel
3. Import database: `api/database/schema.sql` via phpMyAdmin
4. Akses: `http://localhost/vehicleflow/api/public`

### Development di VS Code
```bash
# Start PHP server (dari folder vehicleflow)
php -S localhost:8080 -t api/public

# Start frontend development
npx vite --port 5173
```

## Quick Fix Sekarang
Jalankan perintah ini di PowerShell (bukan CMD):

```powershell
# Terminal 1
php -S localhost:8080 -t api/public

# Terminal 2  
npx vite
```

Frontend akan tersedia di `http://localhost:5173` dan akan connect ke PHP backend di port 8080.