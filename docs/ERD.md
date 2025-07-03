# Entity Relationship Diagram (ERD) - VehicleFlow

## Database Schema Overview

VehicleFlow menggunakan PostgreSQL sebagai database dengan relasi yang terstruktur untuk mendukung sistem pemesanan kendaraan dengan approval workflow bertingkat.

## Physical Data Model

```mermaid
erDiagram
    users ||--o{ bookings : "creates (1:N)"
    users ||--o{ approvals : "approves (1:N)"
    users ||--o{ activity_logs : "performs (1:N)"
    vehicles ||--o{ bookings : "assigned_to (1:N)"
    drivers ||--o{ bookings : "assigned_to (1:N)"
    bookings ||--o{ approvals : "requires (1:N)"
    
    users {
        SERIAL id PK "Primary Key"
        VARCHAR(50) username UK "Unique Username"
        VARCHAR(255) password "Hashed Password"
        VARCHAR(100) full_name "Full Name"
        VARCHAR(100) email UK "Unique Email"
        user_role_enum role "User Role"
        VARCHAR(100) department "Department"
        approval_level_enum approval_level "Approval Level"
        BOOLEAN is_active "Active Status"
        TIMESTAMP created_at "Creation Date"
    }
    
    vehicles {
        SERIAL id PK "Primary Key"
        VARCHAR(20) plate_number UK "Unique Plate Number"
        VARCHAR(50) brand "Vehicle Brand"
        VARCHAR(50) model "Vehicle Model"
        INTEGER year "Manufacturing Year"
        INTEGER capacity "Passenger Capacity"
        VARCHAR(20) fuel_type "Fuel Type"
        vehicle_status_enum status "Vehicle Status"
        TIMESTAMP last_maintenance "Last Maintenance Date"
        TIMESTAMP next_maintenance "Next Maintenance Date"
        TIMESTAMP created_at "Creation Date"
    }
    
    drivers {
        SERIAL id PK "Primary Key"
        VARCHAR(20) employee_id UK "Unique Employee ID"
        VARCHAR(100) full_name "Full Name"
        VARCHAR(30) license_number UK "Unique License Number"
        VARCHAR(20) phone "Phone Number"
        BOOLEAN is_available "Availability Status"
        TIMESTAMP created_at "Creation Date"
    }
    
    bookings {
        SERIAL id PK "Primary Key"
        VARCHAR(20) booking_number UK "Unique Booking Number"
        INTEGER requester_id FK "Foreign Key to users"
        INTEGER vehicle_id FK "Foreign Key to vehicles"
        INTEGER driver_id FK "Foreign Key to drivers"
        TEXT purpose "Purpose of Travel"
        TEXT destination "Travel Destination"
        TIMESTAMP departure_date "Departure Date"
        TIMESTAMP return_date "Return Date"
        VARCHAR(10) departure_time "Departure Time"
        VARCHAR(10) return_time "Return Time"
        INTEGER passengers "Number of Passengers"
        TEXT notes "Additional Notes"
        booking_status_enum status "Booking Status"
        TIMESTAMP created_at "Creation Date"
        TIMESTAMP updated_at "Last Update Date"
    }
    
    approvals {
        SERIAL id PK "Primary Key"
        INTEGER booking_id FK "Foreign Key to bookings"
        INTEGER approver_id FK "Foreign Key to users"
        approval_level_enum level "Approval Level"
        VARCHAR(20) status "Approval Status"
        TEXT comments "Approval Comments"
        TIMESTAMP approved_at "Approval Date"
        TIMESTAMP created_at "Creation Date"
    }
    
    activity_logs {
        SERIAL id PK "Primary Key"
        INTEGER user_id FK "Foreign Key to users"
        VARCHAR(50) action "Action Performed"
        VARCHAR(20) entity_type "Entity Type"
        INTEGER entity_id "Entity ID"
        TEXT details "Action Details"
        VARCHAR(45) ip_address "IP Address"
        TEXT user_agent "User Agent"
        TIMESTAMP created_at "Creation Date"
    }
