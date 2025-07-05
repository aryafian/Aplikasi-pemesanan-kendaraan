# VehicleFlow - PHP CodeIgniter 3 Version

## Overview
This is the PHP CodeIgniter 3 version of VehicleFlow, designed to run with XAMPP for local development.

## Architecture
- **Backend**: PHP CodeIgniter 3 with MySQL
- **Frontend**: React with Vite (unchanged)
- **Database**: MySQL (XAMPP compatible)

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP (recommended for local development)

### Database Setup
1. Start XAMPP Control Panel
2. Start Apache and MySQL services
3. Open phpMyAdmin (http://localhost/phpmyadmin)
4. Import the database schema:
   ```sql
   mysql -u root -p vehicleflow < api/database/schema.sql
   ```

### Running the Application

#### Option 1: Using the startup script (Recommended)
```bash
./start_php_server.sh
```

#### Option 2: Manual startup
```bash
php -S 0.0.0.0:5000 -t api/public
```

#### Option 3: Using XAMPP (For traditional deployment)
1. Copy the `api` folder to your XAMPP htdocs directory
2. Access via http://localhost/api/public

## API Endpoints

All endpoints are prefixed with `/api/`:

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/me` - Get current user info

### Dashboard
- `GET /api/dashboard/stats` - Get dashboard statistics
- `GET /api/dashboard/usage-data` - Get usage analytics
- `GET /api/dashboard/vehicle-status` - Get vehicle status data
- `GET /api/dashboard/recent-bookings` - Get recent bookings

### Bookings
- `GET /api/bookings` - Get all bookings
- `POST /api/bookings` - Create new booking

### Approvals
- `GET /api/approvals/pending` - Get pending approvals
- `POST /api/approvals/process` - Process approval

### Vehicles
- `GET /api/vehicles` - Get all vehicles
- `POST /api/vehicles` - Create new vehicle (Admin only)

### Drivers
- `GET /api/drivers` - Get all drivers
- `POST /api/drivers` - Create new driver (Admin only)

### Reports
- `GET /api/reports/export` - Export booking reports as CSV

### Activity Logs
- `GET /api/activity-logs` - Get activity logs (Admin only)

### Data Seeding
- `POST /api/seed-data` - Create sample data (Admin only)

## Default Users

After running the database setup, you can login with:

- **Admin**: `admin` / `password`
- **Level 1 Approver**: `approver1` / `password`
- **Level 2 Approver**: `approver2` / `password`
- **Regular User**: `user` / `password`

## Database Configuration

Database settings are configured in `api/application/config/database.php`:

```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'vehicleflow',
    'dbdriver' => 'mysqli',
    // ... other settings
);
```

## Frontend
The React frontend remains unchanged and will continue to work with the PHP backend. The API calls are made to the same endpoints, but now handled by PHP controllers instead of Node.js.

## Migration from Node.js
This version replaces the Node.js/Express backend with PHP CodeIgniter 3 while maintaining the same API interface. The React frontend requires no changes.

## Development Notes
- Session management is handled by PHP sessions
- Password hashing uses PHP's `password_hash()` and `password_verify()` functions
- All database operations use prepared statements for security
- CORS headers are automatically added for frontend compatibility