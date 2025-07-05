<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Simple SQLite database for testing purposes
// This provides a working database for demonstration

// SQLite database configuration for testing
$sqlite_db_path = __DIR__ . '/../../database/vehicleflow.sqlite';

// Simple database connection class for SQLite
class SQLiteDatabase {
    private $connection;
    
    public function __construct($db_path) {
        // Create directory if it doesn't exist
        $db_dir = dirname($db_path);
        if (!is_dir($db_dir)) {
            mkdir($db_dir, 0755, true);
        }
        
        $this->connection = new PDO('sqlite:' . $db_path);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Initialize database tables if they don't exist
        $this->initialize_tables();
    }
    
    private function initialize_tables() {
        // Create users table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                full_name TEXT NOT NULL,
                role TEXT NOT NULL CHECK (role IN ('admin', 'requester', 'approver')),
                department TEXT,
                approval_level TEXT CHECK (approval_level IN ('level1', 'level2')),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create vehicles table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS vehicles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                plate_number TEXT UNIQUE NOT NULL,
                brand TEXT NOT NULL,
                model TEXT NOT NULL,
                year INTEGER NOT NULL,
                color TEXT NOT NULL,
                fuel_type TEXT NOT NULL,
                status TEXT DEFAULT 'available' CHECK (status IN ('available', 'in_use', 'maintenance')),
                next_maintenance DATE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create drivers table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS drivers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                full_name TEXT NOT NULL,
                license_number TEXT UNIQUE NOT NULL,
                phone TEXT NOT NULL,
                email TEXT,
                status TEXT DEFAULT 'available' CHECK (status IN ('available', 'on_duty', 'off_duty')),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create bookings table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                booking_number TEXT UNIQUE NOT NULL,
                user_id INTEGER NOT NULL,
                purpose TEXT NOT NULL,
                destination TEXT NOT NULL,
                departure_date DATE NOT NULL,
                departure_time TIME NOT NULL,
                return_time TIME NOT NULL,
                status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved_level1', 'approved_level2', 'approved', 'rejected', 'completed')),
                vehicle_id INTEGER,
                driver_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
                FOREIGN KEY (driver_id) REFERENCES drivers(id)
            )
        ");
        
        // Create approvals table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS approvals (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                booking_id INTEGER NOT NULL,
                approver_id INTEGER NOT NULL,
                level TEXT NOT NULL CHECK (level IN ('level1', 'level2')),
                status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'approved', 'rejected')),
                comments TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (booking_id) REFERENCES bookings(id),
                FOREIGN KEY (approver_id) REFERENCES users(id)
            )
        ");
        
        // Create activity logs table
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS activity_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                action TEXT NOT NULL,
                details TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ");
        
        // Insert default admin user if not exists
        $admin_check = $this->connection->query("SELECT COUNT(*) FROM users WHERE username = 'admin'")->fetchColumn();
        if ($admin_check == 0) {
            $this->seed_initial_data();
        }
    }
    
    private function seed_initial_data() {
        // Default password: "password"
        $password_hash = password_hash('password', PASSWORD_DEFAULT);
        
        // Insert default users
        $this->connection->exec("
            INSERT INTO users (username, password_hash, full_name, role, department, approval_level) VALUES
            ('admin', '$password_hash', 'Administrator', 'admin', 'IT', NULL),
            ('approver1', '$password_hash', 'Level 1 Approver', 'approver', 'Management', 'level1'),
            ('approver2', '$password_hash', 'Level 2 Approver', 'approver', 'Management', 'level2'),
            ('user', '$password_hash', 'Regular User', 'requester', 'Operations', NULL)
        ");
        
        // Insert sample vehicles
        $this->connection->exec("
            INSERT INTO vehicles (plate_number, brand, model, year, color, fuel_type, status) VALUES
            ('B1234CD', 'Toyota', 'Avanza', 2020, 'Silver', 'Petrol', 'available'),
            ('B5678EF', 'Honda', 'Civic', 2019, 'White', 'Petrol', 'available'),
            ('B9012GH', 'Suzuki', 'Ertiga', 2021, 'Black', 'Petrol', 'available')
        ");
        
        // Insert sample drivers
        $this->connection->exec("
            INSERT INTO drivers (full_name, license_number, phone, email, status) VALUES
            ('John Doe', 'DL123456789', '081234567890', 'john@example.com', 'available'),
            ('Jane Smith', 'DL987654321', '081987654321', 'jane@example.com', 'available'),
            ('Bob Johnson', 'DL456789123', '081456789123', 'bob@example.com', 'available')
        ");
    }
    
    public function query($sql, $params = []) {
        if (!empty($params)) {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        }
        
        return $this->connection->query($sql);
    }
    
    public function fetch_array($stmt) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function fetch_all($stmt) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function num_rows($stmt) {
        return $stmt->rowCount();
    }
    
    public function insert_id() {
        return $this->connection->lastInsertId();
    }
    
    public function affected_rows() {
        return $this->connection->rowCount();
    }
    
    public function escape_string($string) {
        return $string; // PDO handles this automatically with prepared statements
    }
    
    public function close() {
        $this->connection = null;
    }
}

// Global database instance using SQLite for testing
$GLOBALS['db_instance'] = new SQLiteDatabase($sqlite_db_path);