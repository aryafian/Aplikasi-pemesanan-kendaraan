import type { Express, Request, Response } from "express";
import { createServer, type Server } from "http";
import session from "express-session";
import { storage } from "./storage";
import { loginSchema, bookingApprovalSchema, insertBookingSchema, insertVehicleSchema, insertDriverSchema } from "@shared/schema";
import { z } from "zod";
import * as XLSX from 'xlsx';

declare module 'express-session' {
  interface SessionData {
    userId?: number;
    user?: {
      id: number;
      username: string;
      fullName: string;
      role: string;
      department?: string;
      approvalLevel?: string;
    };
  }
}

export async function registerRoutes(app: Express): Promise<Server> {
  // Session configuration
  app.use(session({
    secret: process.env.SESSION_SECRET || 'vehicle-booking-secret',
    resave: false,
    saveUninitialized: false,
    cookie: {
      secure: false,
      httpOnly: true,
      maxAge: 24 * 60 * 60 * 1000, // 24 hours
    },
  }));

  // Middleware for authentication
  const requireAuth = (req: Request, res: Response, next: Function) => {
    if (!req.session.userId) {
      return res.status(401).json({ message: "Authentication required" });
    }
    next();
  };

  const requireRole = (roles: string[]) => (req: Request, res: Response, next: Function) => {
    if (!req.session.user || !roles.includes(req.session.user.role)) {
      return res.status(403).json({ message: "Insufficient permissions" });
    }
    next();
  };

  // Activity logging middleware
  const logActivity = async (userId: number, action: string, entityType: string, entityId?: number, details?: string) => {
    try {
      await storage.createActivityLog({
        userId,
        action,
        entityType,
        entityId,
        details,
        ipAddress: '127.0.0.1',
        userAgent: 'VehicleFlow App',
      });
    } catch (error) {
      console.error('Failed to log activity:', error);
    }
  };

  // Initialize default data
  const initializeData = async () => {
    try {
      // Check if admin user exists
      const adminUser = await storage.getUserByUsername('admin');
      if (!adminUser) {
        // Create default admin user
        await storage.createUser({
          username: 'admin',
          password: 'admin123',
          fullName: 'System Administrator',
          email: 'admin@vehicleflow.com',
          role: 'admin',
          department: 'IT',
          isActive: true,
        });

        // Create level 1 approver
        await storage.createUser({
          username: 'manager1',
          password: 'manager123',
          fullName: 'Manager Level 1',
          email: 'manager1@vehicleflow.com',
          role: 'approver',
          department: 'Management',
          approvalLevel: 'level1',
          isActive: true,
        });

        // Create level 2 approver
        await storage.createUser({
          username: 'manager2',
          password: 'manager123',
          fullName: 'Manager Level 2',
          email: 'manager2@vehicleflow.com',
          role: 'approver',
          department: 'Management',
          approvalLevel: 'level2',
          isActive: true,
        });

        // Create sample requester
        await storage.createUser({
          username: 'user1',
          password: 'user123',
          fullName: 'John Doe',
          email: 'john@vehicleflow.com',
          role: 'requester',
          department: 'Marketing',
          isActive: true,
        });

        console.log('Default users created successfully');
      }

      // Check if sample vehicles exist
      const vehicles = await storage.getVehicles();
      if (vehicles.length === 0) {
        await storage.createVehicle({
          plateNumber: 'B1234CD',
          brand: 'Toyota',
          model: 'Avanza',
          year: 2022,
          capacity: 7,
          fuelType: 'Petrol',
          status: 'available',
        });

        await storage.createVehicle({
          plateNumber: 'B5678EF',
          brand: 'Honda',
          model: 'CR-V',
          year: 2023,
          capacity: 5,
          fuelType: 'Petrol',
          status: 'available',
        });

        console.log('Sample vehicles created successfully');
      }

      // Check if sample drivers exist
      const drivers = await storage.getDrivers();
      if (drivers.length === 0) {
        await storage.createDriver({
          employeeId: 'DRV001',
          fullName: 'Ahmad Supardi',
          licenseNumber: 'SIM001234',
          phone: '081234567890',
          isAvailable: true,
        });

        await storage.createDriver({
          employeeId: 'DRV002',
          fullName: 'Budi Santoso',
          licenseNumber: 'SIM005678',
          phone: '081234567891',
          isAvailable: true,
        });

        console.log('Sample drivers created successfully');
      }
    } catch (error) {
      console.error('Failed to initialize data:', error);
    }
  };

  // Initialize data on startup
  await initializeData();

  // Auth routes
  app.post('/api/auth/login', async (req: Request, res: Response) => {
    try {
      const { username, password } = loginSchema.parse(req.body);
      
      const user = await storage.validateUserCredentials(username, password);
      if (!user) {
        return res.status(401).json({ message: "Invalid credentials" });
      }

      req.session.userId = user.id;
      req.session.user = {
        id: user.id,
        username: user.username,
        fullName: user.fullName,
        role: user.role,
        department: user.department || undefined,
        approvalLevel: user.approvalLevel || undefined,
      };

      await logActivity(user.id, 'LOGIN', 'USER', user.id, 'User logged in');

      res.json({ 
        user: {
          id: user.id,
          username: user.username,
          fullName: user.fullName,
          role: user.role,
          department: user.department,
          approvalLevel: user.approvalLevel,
        }
      });
    } catch (error) {
      if (error instanceof z.ZodError) {
        return res.status(400).json({ message: "Invalid input", errors: error.errors });
      }
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post('/api/auth/logout', requireAuth, async (req: Request, res: Response) => {
    const userId = req.session.userId;
    if (userId) {
      await logActivity(userId, 'LOGOUT', 'USER', userId, 'User logged out');
    }
    
    req.session.destroy(() => {
      res.json({ message: "Logged out successfully" });
    });
  });

  app.get('/api/auth/me', requireAuth, async (req: Request, res: Response) => {
    res.json({ user: req.session.user });
  });

  // Dashboard routes
  app.get('/api/dashboard/stats', requireAuth, async (req: Request, res: Response) => {
    try {
      const stats = await storage.getDashboardStats();
      res.json(stats);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch dashboard stats" });
    }
  });

  app.get('/api/dashboard/usage-data', requireAuth, async (req: Request, res: Response) => {
    try {
      const days = parseInt(req.query.days as string) || 7;
      const data = await storage.getUsageData(days);
      res.json(data);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch usage data" });
    }
  });

  app.get('/api/dashboard/vehicle-status', requireAuth, async (req: Request, res: Response) => {
    try {
      const data = await storage.getVehicleStatusData();
      res.json(data);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch vehicle status data" });
    }
  });

  app.get('/api/dashboard/recent-bookings', requireAuth, async (req: Request, res: Response) => {
    try {
      const limit = parseInt(req.query.limit as string) || 5;
      const bookings = await storage.getRecentBookings(limit);
      res.json(bookings);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch recent bookings" });
    }
  });

  // Booking routes
  app.get('/api/bookings', requireAuth, async (req: Request, res: Response) => {
    try {
      const user = req.session.user!;
      let bookings;
      
      if (user.role === 'admin') {
        bookings = await storage.getBookings();
      } else if (user.role === 'approver') {
        bookings = await storage.getBookings();
      } else {
        bookings = await storage.getBookingsByUser(user.id);
      }
      
      res.json(bookings);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch bookings" });
    }
  });

  app.get('/api/bookings/:id', requireAuth, async (req: Request, res: Response) => {
    try {
      const id = parseInt(req.params.id);
      const booking = await storage.getBooking(id);
      
      if (!booking) {
        return res.status(404).json({ message: "Booking not found" });
      }
      
      res.json(booking);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch booking" });
    }
  });

  app.post('/api/bookings', requireAuth, async (req: Request, res: Response) => {
    try {
      const user = req.session.user!;
      const bookingData = insertBookingSchema.parse({
        ...req.body,
        requesterId: user.id,
      });
      
      const booking = await storage.createBooking(bookingData);
      
      // Create approval records for level 1 and level 2
      const level1Approvers = await storage.getUsersByRole('approver');
      const level1Approver = level1Approvers.find(u => u.approvalLevel === 'level1');
      const level2Approver = level1Approvers.find(u => u.approvalLevel === 'level2');
      
      if (level1Approver) {
        await storage.createApproval({
          bookingId: booking.id,
          approverId: level1Approver.id,
          level: 'level1',
          status: 'pending',
        });
      }
      
      if (level2Approver) {
        await storage.createApproval({
          bookingId: booking.id,
          approverId: level2Approver.id,
          level: 'level2',
          status: 'pending',
        });
      }
      
      await logActivity(user.id, 'CREATE', 'BOOKING', booking.id, `Created booking ${booking.bookingNumber}`);
      
      res.status(201).json(booking);
    } catch (error) {
      if (error instanceof z.ZodError) {
        return res.status(400).json({ message: "Invalid input", errors: error.errors });
      }
      res.status(500).json({ message: "Failed to create booking" });
    }
  });

  // Approval routes
  app.get('/api/approvals/pending', requireAuth, requireRole(['approver', 'admin']), async (req: Request, res: Response) => {
    try {
      const user = req.session.user!;
      const pendingApprovals = await storage.getPendingApprovals(user.id);
      res.json(pendingApprovals);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch pending approvals" });
    }
  });

  app.post('/api/approvals', requireAuth, requireRole(['approver', 'admin']), async (req: Request, res: Response) => {
    try {
      const user = req.session.user!;
      const { bookingId, status, comments } = bookingApprovalSchema.parse(req.body);
      
      // Find the approval record
      const approvalList = await storage.getApprovalsByBooking(bookingId);
      const userApproval = approvalList.find(a => a.approverId === user.id && a.status === 'pending');
      
      if (!userApproval) {
        return res.status(404).json({ message: "Approval not found or already processed" });
      }
      
      // Update the approval
      await storage.updateApproval(userApproval.id, {
        status,
        comments,
        approvedAt: new Date(),
      });
      
      // Update booking status based on approval level
      const booking = await storage.getBooking(bookingId);
      if (!booking) {
        return res.status(404).json({ message: "Booking not found" });
      }
      
      if (status === 'rejected') {
        await storage.updateBooking(bookingId, { status: 'rejected' });
      } else if (status === 'approved') {
        if (userApproval.level === 'level1') {
          await storage.updateBooking(bookingId, { status: 'approved_level1' });
        } else if (userApproval.level === 'level2') {
          // Check if level 1 is also approved
          const level1Approval = approvalList.find(a => a.level === 'level1');
          if (level1Approval?.status === 'approved') {
            await storage.updateBooking(bookingId, { status: 'approved' });
          } else {
            await storage.updateBooking(bookingId, { status: 'approved_level2' });
          }
        }
      }
      
      await logActivity(user.id, 'APPROVE', 'BOOKING', bookingId, `${status} booking approval`);
      
      res.json({ message: "Approval processed successfully" });
    } catch (error) {
      if (error instanceof z.ZodError) {
        return res.status(400).json({ message: "Invalid input", errors: error.errors });
      }
      res.status(500).json({ message: "Failed to process approval" });
    }
  });

  // Vehicle routes
  app.get('/api/vehicles', requireAuth, async (req: Request, res: Response) => {
    try {
      const vehicles = await storage.getVehicles();
      res.json(vehicles);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch vehicles" });
    }
  });

  app.post('/api/vehicles', requireAuth, requireRole(['admin']), async (req: Request, res: Response) => {
    try {
      const user = req.session.user!;
      const vehicleData = insertVehicleSchema.parse(req.body);
      const vehicle = await storage.createVehicle(vehicleData);
      
      await logActivity(user.id, 'CREATE', 'VEHICLE', vehicle.id, `Created vehicle ${vehicle.plateNumber}`);
      
      res.status(201).json(vehicle);
    } catch (error) {
      if (error instanceof z.ZodError) {
        return res.status(400).json({ message: "Invalid input", errors: error.errors });
      }
      res.status(500).json({ message: "Failed to create vehicle" });
    }
  });

  app.put('/api/vehicles/:id', requireAuth, requireRole(['admin']), async (req: Request, res: Response) => {
    try {
      const user = req.session.user!;
      const id = parseInt(req.params.id);
      const vehicleData = insertVehicleSchema.partial().parse(req.body);
      const vehicle = await storage.updateVehicle(id, vehicleData);
      
      await logActivity(user.id, 'UPDATE', 'VEHICLE', vehicle.id, `Updated vehicle ${vehicle.plateNumber}`);
      
      res.json(vehicle);
    } catch (error) {
      if (error instanceof z.ZodError) {
        return res.status(400).json({ message: "Invalid input", errors: error.errors });
      }
      res.status(500).json({ message: "Failed to update vehicle" });
    }
  });

  app.delete('/api/vehicles/:id', requireAuth, requireRole(['admin']), async (req: Request, res: Response) => {
    try {
      const user = req.session.user!;
      const id = parseInt(req.params.id);
      await storage.deleteVehicle(id);
      
      await logActivity(user.id, 'DELETE', 'VEHICLE', id, `Deleted vehicle`);
      
      res.json({ message: "Vehicle deleted successfully" });
    } catch (error) {
      res.status(500).json({ message: "Failed to delete vehicle" });
    }
  });

  // Driver routes
  app.get('/api/drivers', requireAuth, async (req: Request, res: Response) => {
    try {
      const drivers = await storage.getDrivers();
      res.json(drivers);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch drivers" });
    }
  });

  app.post('/api/drivers', requireAuth, requireRole(['admin']), async (req: Request, res: Response) => {
    try {
      const user = req.session.user!;
      const driverData = insertDriverSchema.parse(req.body);
      const driver = await storage.createDriver(driverData);
      
      await logActivity(user.id, 'CREATE', 'DRIVER', driver.id, `Created driver ${driver.fullName}`);
      
      res.status(201).json(driver);
    } catch (error) {
      if (error instanceof z.ZodError) {
        return res.status(400).json({ message: "Invalid input", errors: error.errors });
      }
      res.status(500).json({ message: "Failed to create driver" });
    }
  });

  // Reports route
  app.get('/api/reports/export', requireAuth, requireRole(['admin', 'approver']), async (req: Request, res: Response) => {
    try {
      const { startDate, endDate } = req.query;
      
      const bookings = await storage.getBookings();
      
      // Filter by date if provided
      let filteredBookings = bookings;
      if (startDate && endDate) {
        const start = new Date(startDate as string);
        const end = new Date(endDate as string);
        filteredBookings = bookings.filter(b => 
          b.departureDate >= start && b.departureDate <= end
        );
      }
      
      // Prepare data for Excel
      const excelData = filteredBookings.map(booking => ({
        'Booking Number': booking.bookingNumber,
        'Requester': booking.requester.fullName,
        'Department': booking.requester.department,
        'Purpose': booking.purpose,
        'Destination': booking.destination,
        'Departure Date': booking.departureDate.toISOString().split('T')[0],
        'Departure Time': booking.departureTime,
        'Return Date': booking.returnDate.toISOString().split('T')[0],
        'Return Time': booking.returnTime,
        'Vehicle': booking.vehicle?.plateNumber || 'Not assigned',
        'Driver': booking.driver?.fullName || 'Not assigned',
        'Status': booking.status,
        'Created Date': booking.createdAt.toISOString().split('T')[0],
      }));
      
      // Create Excel workbook
      const wb = XLSX.utils.book_new();
      const ws = XLSX.utils.json_to_sheet(excelData);
      XLSX.utils.book_append_sheet(wb, ws, 'Bookings Report');
      
      // Generate buffer
      const buffer = XLSX.write(wb, { type: 'buffer', bookType: 'xlsx' });
      
      // Set headers for file download
      res.setHeader('Content-Disposition', 'attachment; filename=booking_report.xlsx');
      res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      
      await logActivity(req.session.userId!, 'EXPORT', 'REPORT', undefined, 'Exported booking report');
      
      res.send(buffer);
    } catch (error) {
      res.status(500).json({ message: "Failed to generate report" });
    }
  });

  // Activity logs route
  app.get('/api/activity-logs', requireAuth, requireRole(['admin']), async (req: Request, res: Response) => {
    try {
      const limit = parseInt(req.query.limit as string) || 50;
      const logs = await storage.getActivityLogs(limit);
      res.json(logs);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch activity logs" });
    }
  });

  // Seed data endpoint (only for admin)
  app.post('/api/seed-data', requireAuth, requireRole(['admin']), async (req: Request, res: Response) => {
    try {
      const { seedData } = await import('./seed-data');
      await seedData();
      
      await logActivity(req.session.userId!, 'SEED', 'DATABASE', undefined, 'Seeded database with sample data');
      
      res.json({ message: 'Database berhasil di-seed dengan data sample' });
    } catch (error) {
      console.error('Error seeding data:', error);
      res.status(500).json({ message: 'Gagal melakukan seeding data' });
    }
  });

  const httpServer = createServer(app);
  return httpServer;
}
