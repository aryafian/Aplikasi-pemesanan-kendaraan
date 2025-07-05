# Troubleshooting 404 Error - VehicleFlow Login

## Analisis Masalah
Debug menunjukkan backend PHP berfungsi normal. Error 404 saat login kemungkinan disebabkan oleh:

1. **Frontend masih connect ke Node.js backend** (port 5000)
2. **PHP server belum berjalan** di port yang benar
3. **CORS/routing issue** antara frontend-backend

## Solusi Step-by-Step

### 1. Pastikan PHP Server Berjalan
```bash
# Test PHP server
php -S localhost:8080 -t api/public

# Verify dengan curl
curl -X GET http://localhost:8080/
curl -X GET http://localhost:8080/api/auth/me
```

### 2. Update Frontend untuk Connect ke PHP Backend

Edit file `client/src/lib/queryClient.ts` atau konfigurasi API base URL untuk point ke port 8080:

```javascript
// Ganti dari port 5000 ke 8080
const API_BASE_URL = 'http://localhost:8080';
```

### 3. Test Login Manual
```bash
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password"}'
```

Expected response:
```json
{
  "id": 1,
  "username": "admin",
  "fullName": "Administrator",
  "role": "admin"
}
```

### 4. Konfigurasi XAMPP (Alternative)
Jika menggunakan XAMPP:

1. Copy folder `api` ke `C:\xampp82\htdocs\vehicleflow\`
2. Start Apache di XAMPP Control Panel
3. Akses: `http://localhost/vehicleflow/api/public`
4. Update frontend base URL ke `http://localhost/vehicleflow/api/public`

### 5. Quick Fix untuk VS Code

**Terminal 1:**
```bash
php -S localhost:8080 -t api/public
```

**Terminal 2:**
```bash
# Edit vite.config.ts untuk proxy ke PHP backend
npx vite --port 5173
```

**Edit vite.config.ts:**
```javascript
export default defineConfig({
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8080',
        changeOrigin: true
      }
    }
  }
})
```

### 6. Test Endpoints

**Endpoint yang tersedia:**
- `GET /` - API info
- `POST /api/auth/login` - Login
- `GET /api/auth/me` - User info  
- `GET /api/dashboard/stats` - Dashboard data

**Test dengan browser:**
- `http://localhost:8080/` - Should show API info
- `http://localhost:8080/api/auth/me` - Should return 401 (expected)

### 7. Frontend Configuration

Pastikan frontend menggunakan base URL yang benar. Cek file:
- `client/src/lib/queryClient.ts`
- `client/src/lib/auth.ts`

Update API calls untuk menggunakan port 8080 instead of 5000.

## Quick Test Commands

```bash
# 1. Test PHP backend
php -S localhost:8080 -t api/public &

# 2. Test endpoint
curl http://localhost:8080/

# 3. Test login
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password"}'

# 4. Start frontend
npx vite --port 5173
```

## Login Credentials
- **Admin**: `admin` / `password`
- **User**: `user` / `password`
- **Approver1**: `approver1` / `password`
- **Approver2**: `approver2` / `password`

Backend PHP sudah 100% siap, tinggal konfigurasi frontend untuk connect ke port yang benar.