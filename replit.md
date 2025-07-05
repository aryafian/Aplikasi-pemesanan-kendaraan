# VehicleFlow - Vehicle Booking Management System

## Overview

VehicleFlow is a comprehensive web application for managing corporate vehicle bookings with a multi-tiered approval system and analytics dashboard. The system supports role-based access control for Admin, Approver, and Requester users, with automated vehicle/driver assignment and real-time tracking capabilities.

## System Architecture

### Frontend Architecture
- **Framework**: React with TypeScript for modern UI
- **State Management**: TanStack Query for server state management
- **UI Framework**: Tailwind CSS with Radix UI components
- **Form Handling**: React Hook Form with Zod validation
- **Charts**: Chart.js with react-chartjs-2 for data visualization
- **Routing**: Wouter for client-side routing
- **Build Tool**: Vite for fast development and building

### Backend Architecture
- **Framework**: PHP CodeIgniter 3 MVC framework
- **Database**: MySQL with MySQLi driver (XAMPP compatible)
- **Authentication**: PHP sessions with password_hash/password_verify
- **Database Provider**: MySQL with prepared statements
- **API Design**: RESTful controllers with JSON responses
- **Data Seeding**: Custom seeding controller for sample data

## Key Components

### 1. User Management & Authentication
- **Multi-role system**: Admin, Approver (Level 1 & 2), Requester
- **Session-based authentication** with Express sessions
- **Role-based access control** for different features
- **Password hashing** with bcrypt for security

### 2. Vehicle & Driver Management
- **CRUD operations** for vehicles and drivers
- **Status tracking** (available, in_use, maintenance)
- **Automated availability checking** based on booking dates
- **Maintenance scheduling** with next maintenance tracking

### 3. Booking System
- **Form-driven booking creation** with validation
- **Multi-level approval workflow** (minimum 2 approval levels)
- **Real-time status tracking** throughout the approval process
- **Automated vehicle and driver assignment** upon final approval
- **Booking number generation** for tracking

### 4. Approval Workflow
- **Sequential approval process** (Level 1 → Level 2 → Final Approval)
- **Approval dashboard** for designated approvers
- **Comment system** for approval/rejection reasons
- **Email notifications** (infrastructure ready)

### 5. Analytics & Reporting
- **Dashboard with charts** showing usage statistics
- **Excel export functionality** with date filtering
- **Activity logging** for audit trails
- **Real-time statistics** on bookings and vehicle utilization

## Data Flow

### Booking Creation Flow
1. Requester fills booking form with destination, dates, and purpose
2. System validates form data and checks vehicle/driver availability
3. Booking is created with "pending" status
4. Level 1 approval record is generated
5. Notification sent to Level 1 approver

### Approval Process Flow
1. Level 1 approver reviews and decides (approve/reject)
2. If approved, Level 2 approval record is created
3. Level 2 approver makes final decision
4. Upon final approval, vehicle and driver are automatically assigned
5. Activity logs are maintained throughout the process

### Data Persistence
- **MySQL database** with relational schema (XAMPP compatible)
- **MySQLi driver** with prepared statements for security
- **Database schema** managed through SQL files
- **Connection pooling** via PHP MySQLi

## External Dependencies

### Backend Dependencies (PHP)
- **PHP 7.4+**: Server-side scripting language
- **MySQL 5.7+**: Database management system
- **MySQLi Extension**: Database connectivity
- **CodeIgniter 3**: MVC framework structure (custom implementation)

### Frontend Dependencies (React)
- **@tanstack/react-query**: Server state management
- **@radix-ui/***: Accessible UI component primitives
- **chart.js**: Data visualization
- **react-chartjs-2**: Chart.js React integration
- **wouter**: Client-side routing
- **react-hook-form**: Form handling
- **zod**: Runtime type validation

### Development Dependencies
- **TypeScript**: Type safety for frontend
- **Vite**: Build tool and development server
- **Tailwind CSS**: Utility-first styling
- **PostCSS**: CSS processing

## Deployment Strategy

### Build Process
- **Frontend**: Vite builds React application to `dist/public`
- **Backend**: PHP files served directly from `api/` directory
- **Database**: MySQL schema imported via SQL files

### Environment Configuration
- **Database Configuration**: MySQL connection settings in `api/application/config/database.php`
- **PHP Session**: Native PHP session management
- **Web Server**: PHP built-in server or Apache/Nginx for production

### Production Deployment
#### XAMPP Local Development
1. Copy `api` folder to XAMPP `htdocs` directory
2. Import database schema via phpMyAdmin
3. Access via `http://localhost/api/public`

#### Replit Environment
1. Run `./start_php_server.sh` to start PHP development server
2. Frontend served via Vite on same port
3. Database configuration for MySQL

## Changelog
- July 03, 2025. Initial setup with Node.js/React stack
- July 03, 2025. Migrated to PHP CodeIgniter 3 architecture
- July 03, 2025. Implemented complete MVC structure with seeding system
- July 05, 2025. **MAJOR MIGRATION**: Completely migrated backend from Node.js/Express to PHP CodeIgniter 3
- July 05, 2025. Replaced PostgreSQL with MySQL database for XAMPP compatibility
- July 05, 2025. Created complete PHP backend with all API endpoints ported
- July 05, 2025. Maintained React frontend unchanged - full compatibility with new PHP backend

## User Preferences
Preferred communication style: Simple, everyday language.
Preferred tech stack: PHP CodeIgniter 3 with Bootstrap 5 frontend.