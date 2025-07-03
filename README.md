# VehicleFlow - Sistem Pemesanan Kendaraan

Aplikasi web komprehensif untuk manajemen pemesanan kendaraan perusahaan tambang nikel dengan sistem persetujuan multi-level dan dashboard analytics.

![VehicleFlow Logo](https://img.shields.io/badge/VehicleFlow-Mining%20Company-blue)
![PHP](https://img.shields.io/badge/PHP-CodeIgniter%203-777BB4?logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Database-336791?logo=postgresql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap)

## 🚀 Fitur Utama

### ✅ Sistem Manajemen Pemesanan
- **Pembuatan booking** dengan validasi form lengkap
- **Multi-level approval workflow** (Level 1 → Level 2 → Final Approval)
- **Auto-assignment** kendaraan dan driver setelah disetujui
- **Tracking status** real-time pemesanan
- **Notifikasi** untuk approver dan requester

### 👥 Role-Based Access Control
- **Admin**: Full access, manage users, vehicles, drivers, reports
- **Approver Level 1**: Review dan approve booking tahap pertama
- **Approver Level 2**: Final approval booking
- **Requester**: Create dan track booking pribadi

### 📊 Dashboard & Analytics
- **Real-time statistics** dengan charts interaktif
- **Usage trends** dengan Chart.js
- **Vehicle status** monitoring (Available, In Use, Maintenance)
- **Recent bookings** overview
- **Activity logging** untuk audit trail

### 🚗 Manajemen Fleet
- **CRUD kendaraan** dengan status tracking
- **Maintenance scheduling** dan reminder
- **Driver management** dengan availability status
- **Automated resource allocation**

### 📈 Reporting & Export
- **Excel export** dengan filter tanggal
- **Comprehensive reports** untuk management
- **Activity logs** untuk compliance
- **Usage analytics** untuk optimization

## 🛠️ Tech Stack

### Backend
- **PHP 7.4+** dengan CodeIgniter 3.1.13
- **PostgreSQL** untuk database utama
- **Sessions** untuk authentication management
- **Active Record** untuk query building
- **Form Validation** built-in CodeIgniter

### Frontend
- **Bootstrap 5.3** untuk responsive UI
- **Chart.js** untuk data visualization
- **Bootstrap Icons** untuk iconography
- **jQuery** untuk enhanced interactions
- **Custom CSS** untuk brand styling

### Security
- **CSRF Protection** enabled
- **Password hashing** dengan bcrypt
- **Input validation** dan sanitization
- **Role-based authorization**
- **Session security** dengan proper configuration

## 📋 Prerequisites

Pastikan server Anda memiliki:

- **PHP 7.4** atau lebih tinggi
- **PostgreSQL 12** atau lebih tinggi
- **Apache/Nginx** web server
- **Composer** (optional untuk dependencies)
- **mod_rewrite** enabled untuk clean URLs

### PHP Extensions Required
```bash
php-pgsql
php-mbstring
php-json
php-session
php-curl
php-gd
```

## 🚀 Installation

### 1. Clone Repository
```bash
git clone https://github.com/your-username/vehicleflow.git
cd vehicleflow
```

### 2. Configure Database
Buat database PostgreSQL dan update `application/config/database.php`:

```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'vehicleflow_db',
    'dbdriver' => 'postgre',
    // ... other config
);
```

### 3. Environment Variables
Set environment variables atau update config:

```bash
export PGHOST=localhost
export PGUSER=your_username
export PGPASSWORD=your_password
export PGDATABASE=vehicleflow_db
```

### 4. Configure Base URL
Update `application/config/config.php`:

```php
$config['base_url'] = 'http://localhost/vehicleflow/public/';
```

### 5. Web Server Configuration

#### Apache (.htaccess)
File `.htaccess` di public folder:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 6. Generate Sample Data
Akses admin dashboard dan klik "Generate Data Sample" atau hit endpoint:
```bash
POST /dashboard/seed_data
```

## 🔐 Login Credentials

Setelah seeding data berhasil:

| Role | Username | Password | Description |
|------|----------|----------|-------------|
| **Admin** | `admin` | `admin123` | Full system access |
| **Manager L1** | `manager1` | `manager123` | Level 1 Approver |
| **Manager L2** | `manager2` | `manager123` | Level 2 Approver |
| **User** | `user1` | `user123` | Regular Requester |
| **User** | `user2` | `user123` | Maintenance Dept |
| **User** | `user3` | `user123` | Safety & Security |

## 📁 Project Structure

```
vehicleflow/
├── application/
│   ├── config/          # Configuration files
│   ├── controllers/     # MVC Controllers
│   │   ├── Auth.php
│   │   ├── Dashboard.php
│   │   ├── Bookings.php
│   │   └── ...
│   ├── models/          # Data Models
│   │   ├── User_model.php
│   │   ├── Booking_model.php
│   │   ├── Vehicle_model.php
│   │   └── ...
│   ├── views/           # View Templates
│   │   ├── templates/   # Header/Footer
│   │   ├── dashboard/   # Dashboard views
│   │   ├── auth/        # Login views
│   │   └── ...
│   └── libraries/       # Custom Libraries
│       └── Seed_library.php
├── public/              # Web Root
│   ├── index.php        # Entry point
│   └── assets/          # Static assets
│       ├── css/
│       ├── js/
│       └── images/
├── docs/                # Documentation
└── README.md
```

## 🔄 Booking Workflow

1. **Request Creation**
   - User creates booking request
   - System validates form data
   - Status: `pending`

2. **Level 1 Approval**
   - Manager Level 1 reviews request
   - Can approve or reject
   - Status: `approved_level1` or `rejected`

3. **Level 2 Approval**
   - Manager Level 2 final review
   - Final approval decision
   - Status: `approved` or `rejected`

4. **Resource Assignment**
   - Auto-assign available vehicle
   - Auto-assign available driver
   - Update vehicle status to `in_use`

5. **Completion**
   - Mark booking as completed
   - Release vehicle and driver
   - Update vehicle status to `available`

## 🗄️ Database Schema

### Users Table
```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'requester',
    department VARCHAR(100),
    approval_level VARCHAR(10),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Bookings Table
```sql
CREATE TABLE bookings (
    id SERIAL PRIMARY KEY,
    booking_number VARCHAR(50) UNIQUE NOT NULL,
    requester_id INTEGER REFERENCES users(id),
    vehicle_id INTEGER REFERENCES vehicles(id),
    driver_id INTEGER REFERENCES drivers(id),
    purpose TEXT NOT NULL,
    destination TEXT NOT NULL,
    departure_date DATE NOT NULL,
    return_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    return_time TIME NOT NULL,
    passengers INTEGER NOT NULL,
    notes TEXT,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

[Lihat complete schema di `docs/ERD.md`]

## 🔧 Configuration

### CSRF Protection
```php
$config['csrf_protection'] = TRUE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
```

### Session Configuration
```php
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = NULL;
```

### URL Configuration
```php
$config['index_page'] = 'index.php';
$config['uri_protocol'] = 'REQUEST_URI';
$config['url_suffix'] = '';
```

## 🔍 API Endpoints

### Authentication
- `POST /auth/login` - User login
- `POST /auth/logout` - User logout
- `GET /auth/profile` - User profile

### Dashboard
- `GET /dashboard` - Main dashboard
- `GET /dashboard/ajax_stats` - Dashboard statistics
- `POST /dashboard/seed_data` - Generate sample data

### Bookings
- `GET /bookings` - List bookings
- `POST /bookings/create` - Create booking
- `GET /bookings/view/{id}` - View booking detail
- `PUT /bookings/edit/{id}` - Update booking
- `DELETE /bookings/delete/{id}` - Delete booking

### Approvals
- `GET /approvals` - Pending approvals
- `POST /approvals/process/{id}` - Process approval

## 🎨 Customization

### Custom CSS
Edit `public/assets/css/custom.css` untuk styling tambahan:

```css
.custom-card {
    border-radius: 1rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
```

### Brand Colors
Update color scheme di template header:

```css
.sidebar {
    background: linear-gradient(135deg, #your-color-1, #your-color-2);
}
```

## 🧪 Testing

### Manual Testing
1. Login dengan credentials yang berbeda
2. Test create booking flow
3. Test approval workflow
4. Verify resource assignment
5. Check dashboard analytics

### Test Data
Generate sample data menggunakan seeding system:
- 8 users dengan role berbeda
- 8 vehicles dengan status varied
- 6 drivers dengan availability
- 8 bookings dengan status different
- Complete approval workflow

## 📊 Monitoring & Maintenance

### Activity Logs
System mencatat semua aktivitas user:
- Login/Logout events
- CRUD operations
- Approval actions
- Resource assignments

### Database Maintenance
```sql
-- Cleanup old activity logs (90+ days)
DELETE FROM activity_logs WHERE created_at < NOW() - INTERVAL '90 days';

-- Update vehicle maintenance status
UPDATE vehicles SET status = 'maintenance' 
WHERE next_maintenance <= CURRENT_DATE AND status != 'maintenance';
```

## 🔐 Security Considerations

1. **Input Validation**: Semua input divalidasi menggunakan CodeIgniter form validation
2. **CSRF Protection**: Enabled untuk semua forms
3. **SQL Injection**: Protected dengan Active Record
4. **Password Security**: Hashed dengan bcrypt
5. **Session Security**: Proper session configuration
6. **File Upload**: Secure upload handling (jika ada)

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

### Code Standards
- Follow PSR-1 dan PSR-2 untuk PHP
- Use meaningful variable names
- Comment complex logic
- Maintain MVC separation

## 📞 Support

Untuk bantuan teknis:

- **Email**: support@your-company.com
- **Documentation**: [Link to docs]
- **Issues**: [GitHub Issues]

## 📄 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## 🙏 Acknowledgments

- [CodeIgniter Framework](https://codeigniter.com/)
- [Bootstrap](https://getbootstrap.com/)
- [Chart.js](https://www.chartjs.org/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)

---

**VehicleFlow** - Efficient Vehicle Management for Mining Operations
*Built with ❤️ using CodeIgniter 3*