# VehicleFlow - Sistem Manajemen Pemesanan Kendaraan

VehicleFlow adalah aplikasi web komprehensif untuk mengelola pemesanan kendaraan perusahaan dengan sistem persetujuan bertingkat dan dashboard analitik yang lengkap.

## 🚀 Fitur Utama

### 🔐 Sistem Multi-Role
- **Admin**: Kelola kendaraan, driver, dan semua pemesanan
- **Approver**: Setujui/tolak pemesanan (Level 1 & Level 2)
- **Requester**: Buat dan pantau pemesanan

### 📋 Manajemen Pemesanan
- Pemesanan kendaraan dengan form lengkap
- Sistem persetujuan bertingkat (minimum 2 level)
- Tracking status real-time
- Assignment kendaraan dan driver otomatis

### 📊 Dashboard & Analytics
- Dashboard dengan grafik pemakaian kendaraan
- Statistik real-time pemesanan
- Status kendaraan dan driver
- Notifikasi sistem

### 🚗 Manajemen Kendaraan & Driver
- CRUD kendaraan lengkap
- Manajemen data driver
- Status tracking (tersedia, digunakan, maintenance)
- Assignment otomatis berdasarkan ketersediaan

### 📈 Laporan & Export
- Laporan periodik pemesanan
- Export ke Excel dengan filter tanggal
- Activity logging sistem
- Audit trail lengkap

## 🛠️ Tech Stack

### Frontend
- **React 18** dengan TypeScript
- **Tailwind CSS** untuk styling
- **Shadcn UI** untuk komponen
- **Chart.js** untuk grafik
- **React Hook Form** untuk form handling
- **TanStack Query** untuk state management
- **Wouter** untuk routing

### Backend
- **Express.js** dengan TypeScript
- **PostgreSQL** sebagai database
- **Drizzle ORM** untuk database operations
- **bcrypt** untuk password hashing
- **express-session** untuk session management
- **xlsx** untuk Excel export

### Database
- **Neon PostgreSQL** (production-ready)
- Database migration dengan Drizzle Kit
- Foreign key constraints dan relations

## 🏗️ Arsitektur Sistem

### Database Schema
```mermaid
erDiagram
    users ||--o{ bookings : creates
    users ||--o{ approvals : approves
    vehicles ||--o{ bookings : assigned_to
    drivers ||--o{ bookings : assigned_to
    bookings ||--o{ approvals : requires
    
    users {
        int id PK
        string username UK
        string password
        string fullName
        string email UK
        string role
        string department
        string approvalLevel
        boolean isActive
        timestamp createdAt
    }
    
    vehicles {
        int id PK
        string plateNumber UK
        string brand
        string model
        int year
        int capacity
        string fuelType
        string status
        timestamp lastMaintenance
        timestamp nextMaintenance
        timestamp createdAt
    }
    
    drivers {
        int id PK
        string employeeId UK
        string fullName
        string licenseNumber UK
        string phone
        boolean isAvailable
        timestamp createdAt
    }
    
    bookings {
        int id PK
        string bookingNumber UK
        int requesterId FK
        int vehicleId FK
        int driverId FK
        string purpose
        string destination
        timestamp departureDate
        timestamp returnDate
        string departureTime
        string returnTime
        int passengers
        string notes
        string status
        timestamp createdAt
        timestamp updatedAt
    }
    
    approvals {
        int id PK
        int bookingId FK
        int approverId FK
        string level
        string status
        string comments
        timestamp approvedAt
        timestamp createdAt
    }
