-- VehicleFlow Database Schema for MySQL
-- XAMPP Compatible

CREATE DATABASE IF NOT EXISTS vehicleflow;
USE vehicleflow;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'requester', 'approver') NOT NULL,
    department VARCHAR(100) DEFAULT NULL,
    approval_level ENUM('level1', 'level2') DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Vehicles table
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plate_number VARCHAR(20) UNIQUE NOT NULL,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    color VARCHAR(30) NOT NULL,
    fuel_type VARCHAR(20) NOT NULL,
    status ENUM('available', 'in_use', 'maintenance') DEFAULT 'available',
    next_maintenance DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Drivers table
CREATE TABLE IF NOT EXISTS drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    status ENUM('available', 'on_duty', 'off_duty') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_number VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    purpose TEXT NOT NULL,
    destination VARCHAR(255) NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    return_time TIME NOT NULL,
    status ENUM('pending', 'approved_level1', 'approved_level2', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    vehicle_id INT DEFAULT NULL,
    driver_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (driver_id) REFERENCES drivers(id)
);

-- Approvals table
CREATE TABLE IF NOT EXISTS approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    approver_id INT NOT NULL,
    level ENUM('level1', 'level2') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    comments TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (approver_id) REFERENCES users(id)
);

-- Activity logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default admin user
INSERT INTO users (username, password_hash, full_name, role, department) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 'IT')
ON DUPLICATE KEY UPDATE username = username;

-- Insert sample approvers
INSERT INTO users (username, password_hash, full_name, role, department, approval_level) 
VALUES 
('approver1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Level 1 Approver', 'approver', 'Management', 'level1'),
('approver2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Level 2 Approver', 'approver', 'Management', 'level2')
ON DUPLICATE KEY UPDATE username = username;

-- Insert sample requester
INSERT INTO users (username, password_hash, full_name, role, department) 
VALUES ('user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Regular User', 'requester', 'Operations')
ON DUPLICATE KEY UPDATE username = username;

-- Insert sample vehicles
INSERT INTO vehicles (plate_number, brand, model, year, color, fuel_type, status) 
VALUES 
('B1234CD', 'Toyota', 'Avanza', 2020, 'Silver', 'Petrol', 'available'),
('B5678EF', 'Honda', 'Civic', 2019, 'White', 'Petrol', 'available'),
('B9012GH', 'Suzuki', 'Ertiga', 2021, 'Black', 'Petrol', 'available')
ON DUPLICATE KEY UPDATE plate_number = plate_number;

-- Insert sample drivers
INSERT INTO drivers (full_name, license_number, phone, email, status) 
VALUES 
('John Doe', 'DL123456789', '081234567890', 'john@example.com', 'available'),
('Jane Smith', 'DL987654321', '081987654321', 'jane@example.com', 'available'),
('Bob Johnson', 'DL456789123', '081456789123', 'bob@example.com', 'available')
ON DUPLICATE KEY UPDATE license_number = license_number;