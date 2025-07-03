<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seed_library {

    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->model('User_model');
        $this->CI->load->model('Vehicle_model');
        $this->CI->load->model('Driver_model');
        $this->CI->load->model('Booking_model');
        $this->CI->load->model('Approval_model');
    }

    public function run_seed() {
        // Clear existing data
        $this->clear_data();
        
        // Create database tables if not exist
        $this->create_tables();
        
        // Seed users
        $user_ids = $this->seed_users();
        
        // Seed vehicles
        $vehicle_ids = $this->seed_vehicles();
        
        // Seed drivers
        $driver_ids = $this->seed_drivers();
        
        // Seed bookings
        $booking_ids = $this->seed_bookings($user_ids, $vehicle_ids, $driver_ids);
        
        // Seed approvals
        $this->seed_approvals($booking_ids, $user_ids);
        
        // Seed activity logs
        $this->seed_activity_logs($user_ids, $booking_ids, $vehicle_ids, $driver_ids);
        
        return true;
    }

    private function clear_data() {
        // Delete in correct order to avoid foreign key constraints
        $this->CI->db->query("TRUNCATE TABLE activity_logs RESTART IDENTITY CASCADE");
        $this->CI->db->query("TRUNCATE TABLE approvals RESTART IDENTITY CASCADE");
        $this->CI->db->query("TRUNCATE TABLE bookings RESTART IDENTITY CASCADE");
        $this->CI->db->query("TRUNCATE TABLE drivers RESTART IDENTITY CASCADE");
        $this->CI->db->query("TRUNCATE TABLE vehicles RESTART IDENTITY CASCADE");
        $this->CI->db->query("TRUNCATE TABLE users RESTART IDENTITY CASCADE");
    }

    private function create_tables() {
        // Users table
        $this->CI->db->query("
            CREATE TABLE IF NOT EXISTS users (
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
            )
        ");

        // Vehicles table
        $this->CI->db->query("
            CREATE TABLE IF NOT EXISTS vehicles (
                id SERIAL PRIMARY KEY,
                plate_number VARCHAR(20) UNIQUE NOT NULL,
                brand VARCHAR(50) NOT NULL,
                model VARCHAR(50) NOT NULL,
                year INTEGER NOT NULL,
                capacity INTEGER NOT NULL,
                fuel_type VARCHAR(20) NOT NULL,
                status VARCHAR(20) DEFAULT 'available',
                last_maintenance TIMESTAMP,
                next_maintenance TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Drivers table
        $this->CI->db->query("
            CREATE TABLE IF NOT EXISTS drivers (
                id SERIAL PRIMARY KEY,
                employee_id VARCHAR(20) UNIQUE NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                license_number VARCHAR(50) UNIQUE NOT NULL,
                phone VARCHAR(20) NOT NULL,
                is_available BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Bookings table
        $this->CI->db->query("
            CREATE TABLE IF NOT EXISTS bookings (
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
            )
        ");

        // Approvals table
        $this->CI->db->query("
            CREATE TABLE IF NOT EXISTS approvals (
                id SERIAL PRIMARY KEY,
                booking_id INTEGER REFERENCES bookings(id),
                approver_id INTEGER REFERENCES users(id),
                level VARCHAR(10) NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'pending',
                comments TEXT,
                approved_at TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Activity logs table
        $this->CI->db->query("
            CREATE TABLE IF NOT EXISTS activity_logs (
                id SERIAL PRIMARY KEY,
                user_id INTEGER REFERENCES users(id),
                action VARCHAR(50) NOT NULL,
                entity_type VARCHAR(50) NOT NULL,
                entity_id INTEGER,
                details TEXT,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    private function seed_users() {
        $users = array(
            array(
                'username' => 'admin',
                'password' => 'admin123',
                'full_name' => 'Super Administrator',
                'email' => 'admin@nickel-mining.com',
                'role' => 'admin',
                'department' => 'IT Management'
            ),
            array(
                'username' => 'manager1',
                'password' => 'manager123',
                'full_name' => 'Budi Santoso',
                'email' => 'budi.santoso@nickel-mining.com',
                'role' => 'approver',
                'department' => 'Operations',
                'approval_level' => 'level1'
            ),
            array(
                'username' => 'manager2',
                'password' => 'manager123',
                'full_name' => 'Siti Rahayu',
                'email' => 'siti.rahayu@nickel-mining.com',
                'role' => 'approver',
                'department' => 'Operations',
                'approval_level' => 'level2'
            ),
            array(
                'username' => 'user1',
                'password' => 'user123',
                'full_name' => 'Ahmad Wijaya',
                'email' => 'ahmad.wijaya@nickel-mining.com',
                'role' => 'requester',
                'department' => 'Mining Operations'
            ),
            array(
                'username' => 'user2',
                'password' => 'user123',
                'full_name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@nickel-mining.com',
                'role' => 'requester',
                'department' => 'Maintenance'
            ),
            array(
                'username' => 'user3',
                'password' => 'user123',
                'full_name' => 'Rudi Hermawan',
                'email' => 'rudi.hermawan@nickel-mining.com',
                'role' => 'requester',
                'department' => 'Safety & Security'
            ),
            array(
                'username' => 'user4',
                'password' => 'user123',
                'full_name' => 'Maya Sari',
                'email' => 'maya.sari@nickel-mining.com',
                'role' => 'requester',
                'department' => 'Human Resources'
            ),
            array(
                'username' => 'user5',
                'password' => 'user123',
                'full_name' => 'Andi Pratama',
                'email' => 'andi.pratama@nickel-mining.com',
                'role' => 'requester',
                'department' => 'Procurement'
            )
        );

        $user_ids = array();
        foreach ($users as $user) {
            $user_id = $this->CI->User_model->create_user($user);
            $user_ids[] = $user_id;
        }

        return $user_ids;
    }

    private function seed_vehicles() {
        $vehicles = array(
            array(
                'plate_number' => 'B1234CD',
                'brand' => 'Toyota',
                'model' => 'Hilux',
                'year' => 2023,
                'capacity' => 5,
                'fuel_type' => 'diesel',
                'status' => 'available',
                'next_maintenance' => date('Y-m-d', strtotime('+45 days'))
            ),
            array(
                'plate_number' => 'B5678EF',
                'brand' => 'Mitsubishi',
                'model' => 'Pajero Sport',
                'year' => 2022,
                'capacity' => 7,
                'fuel_type' => 'diesel',
                'status' => 'available',
                'next_maintenance' => date('Y-m-d', strtotime('+80 days'))
            ),
            array(
                'plate_number' => 'B9012GH',
                'brand' => 'Isuzu',
                'model' => 'D-Max',
                'year' => 2023,
                'capacity' => 5,
                'fuel_type' => 'diesel',
                'status' => 'in_use',
                'next_maintenance' => date('Y-m-d', strtotime('+25 days'))
            ),
            array(
                'plate_number' => 'B3456IJ',
                'brand' => 'Ford',
                'model' => 'Ranger',
                'year' => 2021,
                'capacity' => 5,
                'fuel_type' => 'diesel',
                'status' => 'available',
                'next_maintenance' => date('Y-m-d', strtotime('+100 days'))
            ),
            array(
                'plate_number' => 'B7890KL',
                'brand' => 'Hino',
                'model' => 'Dutro',
                'year' => 2022,
                'capacity' => 3,
                'fuel_type' => 'diesel',
                'status' => 'maintenance',
                'next_maintenance' => date('Y-m-d', strtotime('+150 days'))
            ),
            array(
                'plate_number' => 'B2345MN',
                'brand' => 'Suzuki',
                'model' => 'Carry',
                'year' => 2023,
                'capacity' => 3,
                'fuel_type' => 'bensin',
                'status' => 'available',
                'next_maintenance' => date('Y-m-d', strtotime('+55 days'))
            ),
            array(
                'plate_number' => 'B6789OP',
                'brand' => 'Daihatsu',
                'model' => 'Gran Max',
                'year' => 2022,
                'capacity' => 8,
                'fuel_type' => 'bensin',
                'status' => 'available',
                'next_maintenance' => date('Y-m-d', strtotime('+135 days'))
            ),
            array(
                'plate_number' => 'B1357QR',
                'brand' => 'Toyota',
                'model' => 'Avanza',
                'year' => 2021,
                'capacity' => 7,
                'fuel_type' => 'bensin',
                'status' => 'in_use',
                'next_maintenance' => date('Y-m-d', strtotime('+65 days'))
            )
        );

        $vehicle_ids = array();
        foreach ($vehicles as $vehicle) {
            $vehicle_id = $this->CI->Vehicle_model->create_vehicle($vehicle);
            $vehicle_ids[] = $vehicle_id;
        }

        return $vehicle_ids;
    }

    private function seed_drivers() {
        $drivers = array(
            array(
                'employee_id' => 'DRV001',
                'full_name' => 'Joko Susilo',
                'license_number' => '1234567890123456',
                'phone' => '081234567890',
                'is_available' => true
            ),
            array(
                'employee_id' => 'DRV002',
                'full_name' => 'Bambang Wijaya',
                'license_number' => '2345678901234567',
                'phone' => '081234567891',
                'is_available' => true
            ),
            array(
                'employee_id' => 'DRV003',
                'full_name' => 'Suratno',
                'license_number' => '3456789012345678',
                'phone' => '081234567892',
                'is_available' => false
            ),
            array(
                'employee_id' => 'DRV004',
                'full_name' => 'Agus Prasetyo',
                'license_number' => '4567890123456789',
                'phone' => '081234567893',
                'is_available' => true
            ),
            array(
                'employee_id' => 'DRV005',
                'full_name' => 'Hendra Gunawan',
                'license_number' => '5678901234567890',
                'phone' => '081234567894',
                'is_available' => true
            ),
            array(
                'employee_id' => 'DRV006',
                'full_name' => 'Wawan Setiawan',
                'license_number' => '6789012345678901',
                'phone' => '081234567895',
                'is_available' => false
            )
        );

        $driver_ids = array();
        foreach ($drivers as $driver) {
            $driver_id = $this->CI->Driver_model->create_driver($driver);
            $driver_ids[] = $driver_id;
        }

        return $driver_ids;
    }

    private function seed_bookings($user_ids, $vehicle_ids, $driver_ids) {
        $bookings = array(
            array(
                'purpose' => 'Inspeksi Lokasi Tambang Area A',
                'destination' => 'Site Tambang Blok A1',
                'departure_date' => date('Y-m-d', strtotime('-5 days')),
                'return_date' => date('Y-m-d', strtotime('-4 days')),
                'departure_time' => '08:00',
                'return_time' => '17:00',
                'passengers' => 3,
                'status' => 'completed',
                'requester_id' => $user_ids[3], // Ahmad Wijaya
                'vehicle_id' => $vehicle_ids[0],
                'driver_id' => $driver_ids[0]
            ),
            array(
                'purpose' => 'Meeting dengan Vendor Equipment',
                'destination' => 'Kantor Vendor - Bekasi',
                'departure_date' => date('Y-m-d', strtotime('+2 days')),
                'return_date' => date('Y-m-d', strtotime('+3 days')),
                'departure_time' => '09:00',
                'return_time' => '16:00',
                'passengers' => 2,
                'status' => 'pending',
                'requester_id' => $user_ids[4], // Dewi Lestari
                'vehicle_id' => null,
                'driver_id' => null
            ),
            array(
                'purpose' => 'Training Safety Mining',
                'destination' => 'Training Center Jakarta',
                'departure_date' => date('Y-m-d', strtotime('+5 days')),
                'return_date' => date('Y-m-d', strtotime('+7 days')),
                'departure_time' => '07:30',
                'return_time' => '18:00',
                'passengers' => 4,
                'status' => 'approved_level1',
                'requester_id' => $user_ids[5], // Rudi Hermawan
                'vehicle_id' => null,
                'driver_id' => null
            ),
            array(
                'purpose' => 'Pengambilan Sample Nickel',
                'destination' => 'Laboratory Universitas Indonesia',
                'departure_date' => date('Y-m-d', strtotime('+8 days')),
                'return_date' => date('Y-m-d', strtotime('+9 days')),
                'departure_time' => '10:00',
                'return_time' => '15:00',
                'passengers' => 2,
                'status' => 'approved',
                'requester_id' => $user_ids[3], // Ahmad Wijaya
                'vehicle_id' => $vehicle_ids[1],
                'driver_id' => $driver_ids[1]
            ),
            array(
                'purpose' => 'Rekrutmen Karyawan Baru',
                'destination' => 'Universitas Trisakti',
                'departure_date' => date('Y-m-d', strtotime('+10 days')),
                'return_date' => date('Y-m-d', strtotime('+11 days')),
                'departure_time' => '08:30',
                'return_time' => '16:30',
                'passengers' => 3,
                'status' => 'rejected',
                'requester_id' => $user_ids[6], // Maya Sari
                'vehicle_id' => null,
                'driver_id' => null
            ),
            array(
                'purpose' => 'Survey Lokasi Baru',
                'destination' => 'Sulawesi Tengah',
                'departure_date' => date('Y-m-d', strtotime('+12 days')),
                'return_date' => date('Y-m-d', strtotime('+15 days')),
                'departure_time' => '06:00',
                'return_time' => '20:00',
                'passengers' => 5,
                'status' => 'approved_level2',
                'requester_id' => $user_ids[7], // Andi Pratama
                'vehicle_id' => null,
                'driver_id' => null
            ),
            array(
                'purpose' => 'Maintenance Equipment Tambang',
                'destination' => 'Site Maintenance - Area B',
                'departure_date' => date('Y-m-d', strtotime('-10 days')),
                'return_date' => date('Y-m-d', strtotime('-9 days')),
                'departure_time' => '07:00',
                'return_time' => '17:00',
                'passengers' => 4,
                'status' => 'completed',
                'requester_id' => $user_ids[4], // Dewi Lestari
                'vehicle_id' => $vehicle_ids[2],
                'driver_id' => $driver_ids[2]
            ),
            array(
                'purpose' => 'Audit Internal Mining Operations',
                'destination' => 'Kantor Pusat Jakarta',
                'departure_date' => date('Y-m-d', strtotime('-8 days')),
                'return_date' => date('Y-m-d', strtotime('-7 days')),
                'departure_time' => '09:30',
                'return_time' => '16:00',
                'passengers' => 3,
                'status' => 'completed',
                'requester_id' => $user_ids[5], // Rudi Hermawan
                'vehicle_id' => $vehicle_ids[3],
                'driver_id' => $driver_ids[3]
            )
        );

        $booking_ids = array();
        foreach ($bookings as $booking) {
            $booking_id = $this->CI->Booking_model->create_booking($booking);
            $booking_ids[] = $booking_id;
        }

        return $booking_ids;
    }

    private function seed_approvals($booking_ids, $user_ids) {
        // Create approvals for bookings that have been processed
        $approvals = array(
            // Completed booking 1
            array(
                'booking_id' => $booking_ids[0],
                'approver_id' => $user_ids[1], // Manager Level 1
                'level' => 'level1',
                'status' => 'approved',
                'comments' => 'Disetujui untuk kebutuhan operasional',
                'approved_at' => date('Y-m-d H:i:s', strtotime('-4 days 10:00'))
            ),
            array(
                'booking_id' => $booking_ids[0],
                'approver_id' => $user_ids[2], // Manager Level 2
                'level' => 'level2',
                'status' => 'approved',
                'comments' => 'Final approval granted',
                'approved_at' => date('Y-m-d H:i:s', strtotime('-4 days 12:00'))
            ),
            // Level 1 approved booking
            array(
                'booking_id' => $booking_ids[2],
                'approver_id' => $user_ids[1], // Manager Level 1
                'level' => 'level1',
                'status' => 'approved',
                'comments' => 'Approved untuk tahap pertama',
                'approved_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ),
            // Pending Level 2 for level1 approved booking
            array(
                'booking_id' => $booking_ids[2],
                'approver_id' => $user_ids[2], // Manager Level 2
                'level' => 'level2',
                'status' => 'pending',
                'comments' => null,
                'approved_at' => null
            ),
            // Fully approved booking
            array(
                'booking_id' => $booking_ids[3],
                'approver_id' => $user_ids[1],
                'level' => 'level1',
                'status' => 'approved',
                'comments' => 'Approved tahap pertama',
                'approved_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ),
            array(
                'booking_id' => $booking_ids[3],
                'approver_id' => $user_ids[2],
                'level' => 'level2',
                'status' => 'approved',
                'comments' => 'Final approval granted',
                'approved_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ),
            // Rejected booking
            array(
                'booking_id' => $booking_ids[4],
                'approver_id' => $user_ids[1],
                'level' => 'level1',
                'status' => 'rejected',
                'comments' => 'Tidak dapat disetujui karena konflik dengan agenda lain',
                'approved_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))
            )
        );

        foreach ($approvals as $approval) {
            $this->CI->Approval_model->create_approval($approval);
        }
    }

    private function seed_activity_logs($user_ids, $booking_ids, $vehicle_ids, $driver_ids) {
        $this->load->model('Activity_model');
        
        $activities = array(
            array(
                'user_id' => $user_ids[0],
                'action' => 'LOGIN',
                'entity_type' => 'USER',
                'entity_id' => $user_ids[0],
                'details' => 'Admin login ke sistem',
                'ip_address' => '192.168.1.100'
            ),
            array(
                'user_id' => $user_ids[3],
                'action' => 'CREATE',
                'entity_type' => 'BOOKING',
                'entity_id' => $booking_ids[0],
                'details' => 'Membuat booking baru BK-2025-001',
                'ip_address' => '192.168.1.101'
            ),
            array(
                'user_id' => $user_ids[1],
                'action' => 'APPROVE',
                'entity_type' => 'BOOKING',
                'entity_id' => $booking_ids[0],
                'details' => 'Menyetujui booking BK-2025-001 level 1',
                'ip_address' => '192.168.1.102'
            ),
            array(
                'user_id' => $user_ids[0],
                'action' => 'CREATE',
                'entity_type' => 'VEHICLE',
                'entity_id' => $vehicle_ids[0],
                'details' => 'Menambahkan kendaraan baru B1234CD',
                'ip_address' => '192.168.1.100'
            ),
            array(
                'user_id' => $user_ids[0],
                'action' => 'CREATE',
                'entity_type' => 'DRIVER',
                'entity_id' => $driver_ids[0],
                'details' => 'Menambahkan driver baru Joko Susilo',
                'ip_address' => '192.168.1.100'
            )
        );

        foreach ($activities as $activity) {
            $this->CI->Activity_model->create_activity($activity);
        }
    }
}