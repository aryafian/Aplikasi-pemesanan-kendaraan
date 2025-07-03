# VehicleFlow - Vehicle Booking Management System

## Overview

VehicleFlow is a comprehensive web application for managing corporate vehicle bookings with a multi-tiered approval system and analytics dashboard. The system supports role-based access control for Admin, Approver, and Requester users, with automated vehicle/driver assignment and real-time tracking capabilities.

## System Architecture

### Frontend Architecture
- **Framework**: PHP CodeIgniter 3 with Bootstrap 5 for responsive UI
- **Template Engine**: CodeIgniter's built-in views with PHP templating
- **UI Framework**: Bootstrap 5 with custom CSS for modern design
- **Form Handling**: CodeIgniter form validation and CSRF protection
- **Charts**: Chart.js for data visualization and analytics
- **JavaScript**: jQuery for enhanced interactivity

### Backend Architecture
- **Framework**: PHP CodeIgniter 3 MVC framework
- **Database**: PostgreSQL with CodeIgniter's Active Record
- **Authentication**: CodeIgniter sessions with bcrypt password hashing
- **Database Provider**: PostgreSQL with PDO driver
- **API Design**: RESTful controllers with proper error handling
- **Data Seeding**: Custom library for generating realistic sample data

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
- **PostgreSQL database** with relational schema
- **Drizzle ORM** for type-safe database operations
- **Database migrations** managed through Drizzle Kit
- **Connection pooling** via Neon serverless

## External Dependencies

### Core Dependencies
- **@neondatabase/serverless**: PostgreSQL database connectivity
- **drizzle-orm**: Type-safe database operations
- **@tanstack/react-query**: Server state management
- **@radix-ui/***: Accessible UI component primitives
- **chart.js**: Data visualization
- **react-chartjs-2**: Chart.js React integration
- **bcrypt**: Password hashing
- **express-session**: Session management
- **connect-pg-simple**: PostgreSQL session store

### Development Dependencies
- **TypeScript**: Type safety across the application
- **Vite**: Build tool and development server
- **ESBuild**: Production bundling
- **Tailwind CSS**: Utility-first styling
- **PostCSS**: CSS processing

## Deployment Strategy

### Build Process
- **Frontend**: Vite builds React application to `dist/public`
- **Backend**: ESBuild bundles server code to `dist/index.js`
- **Database**: Drizzle pushes schema changes to PostgreSQL

### Environment Configuration
- **Database URL**: Required for PostgreSQL connection
- **Session Secret**: For secure session management
- **Node Environment**: Development/production mode switching

### Production Deployment
- Express server serves static frontend files
- Database migrations applied via `npm run db:push`
- Environment variables configured for production database

## Changelog
- July 03, 2025. Initial setup with Node.js/React stack
- July 03, 2025. Migrated to PHP CodeIgniter 3 architecture
- July 03, 2025. Implemented complete MVC structure with seeding system

## User Preferences
Preferred communication style: Simple, everyday language.
Preferred tech stack: PHP CodeIgniter 3 with Bootstrap 5 frontend.