import { 
  users, vehicles, drivers, bookings, approvals, activityLogs,
  type User, type InsertUser, type Vehicle, type InsertVehicle,
  type Driver, type InsertDriver, type Booking, type InsertBooking,
  type Approval, type InsertApproval, type ActivityLog, type InsertActivityLog
} from "@shared/schema";
import { db } from "./db";
import { eq, and, desc, gte, lte, count, sql } from "drizzle-orm";
import bcrypt from "bcrypt";

export interface IStorage {
  // User methods
  getUser(id: number): Promise<User | undefined>;
  getUserByUsername(username: string): Promise<User | undefined>;
  createUser(user: InsertUser): Promise<User>;
  updateUser(id: number, user: Partial<InsertUser>): Promise<User>;
  getUsers(): Promise<User[]>;
  getUsersByRole(role: string): Promise<User[]>;
  validateUserCredentials(username: string, password: string): Promise<User | null>;

  // Vehicle methods
  getVehicles(): Promise<Vehicle[]>;
  getVehicle(id: number): Promise<Vehicle | undefined>;
  createVehicle(vehicle: InsertVehicle): Promise<Vehicle>;
  updateVehicle(id: number, vehicle: Partial<InsertVehicle>): Promise<Vehicle>;
  deleteVehicle(id: number): Promise<void>;
  getAvailableVehicles(startDate: Date, endDate: Date): Promise<Vehicle[]>;

  // Driver methods
  getDrivers(): Promise<Driver[]>;
  getDriver(id: number): Promise<Driver | undefined>;
  createDriver(driver: InsertDriver): Promise<Driver>;
  updateDriver(id: number, driver: Partial<InsertDriver>): Promise<Driver>;
  deleteDriver(id: number): Promise<void>;
  getAvailableDrivers(startDate: Date, endDate: Date): Promise<Driver[]>;

  // Booking methods
  getBookings(): Promise<(Booking & { requester: User; vehicle?: Vehicle; driver?: Driver })[]>;
  getBooking(id: number): Promise<(Booking & { requester: User; vehicle?: Vehicle; driver?: Driver; approvals: (Approval & { approver: User })[] }) | undefined>;
  createBooking(booking: InsertBooking): Promise<Booking>;
  updateBooking(id: number, booking: Partial<InsertBooking>): Promise<Booking>;
  getBookingsByUser(userId: number): Promise<Booking[]>;
  getPendingApprovals(approverId: number): Promise<(Booking & { requester: User; vehicle?: Vehicle; driver?: Driver })[]>;
  getBookingStats(): Promise<{
    totalBookings: number;
    pendingApproval: number;
    approvedBookings: number;
    rejectedBookings: number;
  }>;

  // Approval methods
  getApprovals(): Promise<Approval[]>;
  createApproval(approval: InsertApproval): Promise<Approval>;
  updateApproval(id: number, approval: Partial<InsertApproval>): Promise<Approval>;
  getApprovalsByBooking(bookingId: number): Promise<(Approval & { approver: User })[]>;

  // Activity log methods
  createActivityLog(log: InsertActivityLog): Promise<ActivityLog>;
  getActivityLogs(limit?: number): Promise<(ActivityLog & { user?: User })[]>;

  // Dashboard methods
  getDashboardStats(): Promise<{
    totalBookings: number;
    pendingApproval: number;
    activeVehicles: number;
    efficiency: number;
  }>;
  getUsageData(days: number): Promise<{ date: string; count: number }[]>;
  getVehicleStatusData(): Promise<{ status: string; count: number }[]>;
  getRecentBookings(limit: number): Promise<(Booking & { requester: User; vehicle?: Vehicle; driver?: Driver })[]>;
}

export class DatabaseStorage implements IStorage {
  async validateUserCredentials(username: string, password: string): Promise<User | null> {
    const user = await this.getUserByUsername(username);
    if (!user) return null;
    
    const isValid = await bcrypt.compare(password, user.password);
    return isValid ? user : null;
  }

  async getUser(id: number): Promise<User | undefined> {
    const [user] = await db.select().from(users).where(eq(users.id, id));
    return user || undefined;
  }

  async getUserByUsername(username: string): Promise<User | undefined> {
    const [user] = await db.select().from(users).where(eq(users.username, username));
    return user || undefined;
  }

  async createUser(insertUser: InsertUser): Promise<User> {
    const hashedPassword = await bcrypt.hash(insertUser.password, 10);
    const [user] = await db
      .insert(users)
      .values({ ...insertUser, password: hashedPassword })
      .returning();
    return user;
  }

  async updateUser(id: number, updateUser: Partial<InsertUser>): Promise<User> {
    const updateData = { ...updateUser };
    if (updateData.password) {
      updateData.password = await bcrypt.hash(updateData.password, 10);
    }
    
    const [user] = await db
      .update(users)
      .set(updateData)
      .where(eq(users.id, id))
      .returning();
    return user;
  }

  async getUsers(): Promise<User[]> {
    return await db.select().from(users).orderBy(users.fullName);
  }

  async getUsersByRole(role: string): Promise<User[]> {
    return await db.select().from(users).where(eq(users.role, role as any)).orderBy(users.fullName);
  }

  async getVehicles(): Promise<Vehicle[]> {
    return await db.select().from(vehicles).orderBy(vehicles.plateNumber);
  }

  async getVehicle(id: number): Promise<Vehicle | undefined> {
    const [vehicle] = await db.select().from(vehicles).where(eq(vehicles.id, id));
    return vehicle || undefined;
  }

  async createVehicle(insertVehicle: InsertVehicle): Promise<Vehicle> {
    const [vehicle] = await db
      .insert(vehicles)
      .values(insertVehicle)
      .returning();
    return vehicle;
  }

  async updateVehicle(id: number, updateVehicle: Partial<InsertVehicle>): Promise<Vehicle> {
    const [vehicle] = await db
      .update(vehicles)
      .set(updateVehicle)
      .where(eq(vehicles.id, id))
      .returning();
    return vehicle;
  }

  async deleteVehicle(id: number): Promise<void> {
    await db.delete(vehicles).where(eq(vehicles.id, id));
  }

  async getAvailableVehicles(startDate: Date, endDate: Date): Promise<Vehicle[]> {
    const conflictingBookings = await db
      .select({ vehicleId: bookings.vehicleId })
      .from(bookings)
      .where(
        and(
          gte(bookings.departureDate, startDate),
          lte(bookings.returnDate, endDate),
          eq(bookings.status, 'approved')
        )
      );

    const conflictingVehicleIds = conflictingBookings.map(b => b.vehicleId).filter(Boolean);

    if (conflictingVehicleIds.length === 0) {
      return await this.getVehicles();
    }

    return await db
      .select()
      .from(vehicles)
      .where(
        and(
          eq(vehicles.status, 'available'),
          sql`${vehicles.id} NOT IN (${conflictingVehicleIds.join(',')})`
        )
      );
  }

  async getDrivers(): Promise<Driver[]> {
    return await db.select().from(drivers).orderBy(drivers.fullName);
  }

  async getDriver(id: number): Promise<Driver | undefined> {
    const [driver] = await db.select().from(drivers).where(eq(drivers.id, id));
    return driver || undefined;
  }

  async createDriver(insertDriver: InsertDriver): Promise<Driver> {
    const [driver] = await db
      .insert(drivers)
      .values(insertDriver)
      .returning();
    return driver;
  }

  async updateDriver(id: number, updateDriver: Partial<InsertDriver>): Promise<Driver> {
    const [driver] = await db
      .update(drivers)
      .set(updateDriver)
      .where(eq(drivers.id, id))
      .returning();
    return driver;
  }

  async deleteDriver(id: number): Promise<void> {
    await db.delete(drivers).where(eq(drivers.id, id));
  }

  async getAvailableDrivers(startDate: Date, endDate: Date): Promise<Driver[]> {
    const conflictingBookings = await db
      .select({ driverId: bookings.driverId })
      .from(bookings)
      .where(
        and(
          gte(bookings.departureDate, startDate),
          lte(bookings.returnDate, endDate),
          eq(bookings.status, 'approved')
        )
      );

    const conflictingDriverIds = conflictingBookings.map(b => b.driverId).filter(Boolean);

    if (conflictingDriverIds.length === 0) {
      return await this.getDrivers();
    }

    return await db
      .select()
      .from(drivers)
      .where(
        and(
          eq(drivers.isAvailable, true),
          sql`${drivers.id} NOT IN (${conflictingDriverIds.join(',')})`
        )
      );
  }

  async getBookings(): Promise<(Booking & { requester: User; vehicle?: Vehicle; driver?: Driver })[]> {
    return await db
      .select({
        booking: bookings,
        requester: users,
        vehicle: vehicles,
        driver: drivers,
      })
      .from(bookings)
      .leftJoin(users, eq(bookings.requesterId, users.id))
      .leftJoin(vehicles, eq(bookings.vehicleId, vehicles.id))
      .leftJoin(drivers, eq(bookings.driverId, drivers.id))
      .orderBy(desc(bookings.createdAt))
      .then(results => 
        results.map(r => ({
          ...r.booking,
          requester: r.requester!,
          vehicle: r.vehicle || undefined,
          driver: r.driver || undefined,
        }))
      );
  }

  async getBooking(id: number): Promise<(Booking & { requester: User; vehicle?: Vehicle; driver?: Driver; approvals: (Approval & { approver: User })[] }) | undefined> {
    const result = await db
      .select({
        booking: bookings,
        requester: users,
        vehicle: vehicles,
        driver: drivers,
      })
      .from(bookings)
      .leftJoin(users, eq(bookings.requesterId, users.id))
      .leftJoin(vehicles, eq(bookings.vehicleId, vehicles.id))
      .leftJoin(drivers, eq(bookings.driverId, drivers.id))
      .where(eq(bookings.id, id));

    if (result.length === 0) return undefined;

    const bookingApprovals = await this.getApprovalsByBooking(id);

    return {
      ...result[0].booking,
      requester: result[0].requester!,
      vehicle: result[0].vehicle || undefined,
      driver: result[0].driver || undefined,
      approvals: bookingApprovals,
    };
  }

  async createBooking(insertBooking: InsertBooking): Promise<Booking> {
    const bookingNumber = `VB-${new Date().getFullYear()}-${String(Date.now()).slice(-6)}`;
    
    const [booking] = await db
      .insert(bookings)
      .values({ ...insertBooking, bookingNumber })
      .returning();
    return booking;
  }

  async updateBooking(id: number, updateBooking: Partial<InsertBooking>): Promise<Booking> {
    const [booking] = await db
      .update(bookings)
      .set({ ...updateBooking, updatedAt: new Date() })
      .where(eq(bookings.id, id))
      .returning();
    return booking;
  }

  async getBookingsByUser(userId: number): Promise<Booking[]> {
    return await db
      .select()
      .from(bookings)
      .where(eq(bookings.requesterId, userId))
      .orderBy(desc(bookings.createdAt));
  }

  async getPendingApprovals(approverId: number): Promise<(Booking & { requester: User; vehicle?: Vehicle; driver?: Driver })[]> {
    const pendingApprovalIds = await db
      .select({ bookingId: approvals.bookingId })
      .from(approvals)
      .where(
        and(
          eq(approvals.approverId, approverId),
          eq(approvals.status, 'pending')
        )
      );

    if (pendingApprovalIds.length === 0) return [];

    const bookingIds = pendingApprovalIds.map(a => a.bookingId);

    return await db
      .select({
        booking: bookings,
        requester: users,
        vehicle: vehicles,
        driver: drivers,
      })
      .from(bookings)
      .leftJoin(users, eq(bookings.requesterId, users.id))
      .leftJoin(vehicles, eq(bookings.vehicleId, vehicles.id))
      .leftJoin(drivers, eq(bookings.driverId, drivers.id))
      .where(sql`${bookings.id} IN (${bookingIds.join(',')})`)
      .then(results => 
        results.map(r => ({
          ...r.booking,
          requester: r.requester!,
          vehicle: r.vehicle || undefined,
          driver: r.driver || undefined,
        }))
      );
  }

  async getBookingStats(): Promise<{
    totalBookings: number;
    pendingApproval: number;
    approvedBookings: number;
    rejectedBookings: number;
  }> {
    const [totalBookings] = await db.select({ count: count() }).from(bookings);
    const [pendingApproval] = await db.select({ count: count() }).from(bookings).where(eq(bookings.status, 'pending'));
    const [approvedBookings] = await db.select({ count: count() }).from(bookings).where(eq(bookings.status, 'approved'));
    const [rejectedBookings] = await db.select({ count: count() }).from(bookings).where(eq(bookings.status, 'rejected'));

    return {
      totalBookings: totalBookings.count,
      pendingApproval: pendingApproval.count,
      approvedBookings: approvedBookings.count,
      rejectedBookings: rejectedBookings.count,
    };
  }

  async getApprovals(): Promise<Approval[]> {
    return await db.select().from(approvals).orderBy(desc(approvals.createdAt));
  }

  async createApproval(insertApproval: InsertApproval): Promise<Approval> {
    const [approval] = await db
      .insert(approvals)
      .values(insertApproval)
      .returning();
    return approval;
  }

  async updateApproval(id: number, updateApproval: Partial<InsertApproval>): Promise<Approval> {
    const [approval] = await db
      .update(approvals)
      .set(updateApproval)
      .where(eq(approvals.id, id))
      .returning();
    return approval;
  }

  async getApprovalsByBooking(bookingId: number): Promise<(Approval & { approver: User })[]> {
    return await db
      .select({
        approval: approvals,
        approver: users,
      })
      .from(approvals)
      .leftJoin(users, eq(approvals.approverId, users.id))
      .where(eq(approvals.bookingId, bookingId))
      .orderBy(approvals.level)
      .then(results => 
        results.map(r => ({
          ...r.approval,
          approver: r.approver!,
        }))
      );
  }

  async createActivityLog(insertLog: InsertActivityLog): Promise<ActivityLog> {
    const [log] = await db
      .insert(activityLogs)
      .values(insertLog)
      .returning();
    return log;
  }

  async getActivityLogs(limit: number = 50): Promise<(ActivityLog & { user?: User })[]> {
    return await db
      .select({
        log: activityLogs,
        user: users,
      })
      .from(activityLogs)
      .leftJoin(users, eq(activityLogs.userId, users.id))
      .orderBy(desc(activityLogs.createdAt))
      .limit(limit)
      .then(results => 
        results.map(r => ({
          ...r.log,
          user: r.user || undefined,
        }))
      );
  }

  async getDashboardStats(): Promise<{
    totalBookings: number;
    pendingApproval: number;
    activeVehicles: number;
    efficiency: number;
  }> {
    const [totalBookings] = await db.select({ count: count() }).from(bookings);
    const [pendingApproval] = await db.select({ count: count() }).from(bookings).where(eq(bookings.status, 'pending'));
    const [activeVehicles] = await db.select({ count: count() }).from(vehicles).where(eq(vehicles.status, 'available'));
    const [approvedBookings] = await db.select({ count: count() }).from(bookings).where(eq(bookings.status, 'approved'));

    const efficiency = totalBookings.count > 0 ? Math.round((approvedBookings.count / totalBookings.count) * 100) : 0;

    return {
      totalBookings: totalBookings.count,
      pendingApproval: pendingApproval.count,
      activeVehicles: activeVehicles.count,
      efficiency,
    };
  }

  async getUsageData(days: number): Promise<{ date: string; count: number }[]> {
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - days);

    const results = await db
      .select({
        date: sql<string>`DATE(${bookings.createdAt})`,
        count: count(),
      })
      .from(bookings)
      .where(gte(bookings.createdAt, startDate))
      .groupBy(sql`DATE(${bookings.createdAt})`)
      .orderBy(sql`DATE(${bookings.createdAt})`);

    return results;
  }

  async getVehicleStatusData(): Promise<{ status: string; count: number }[]> {
    const results = await db
      .select({
        status: vehicles.status,
        count: count(),
      })
      .from(vehicles)
      .groupBy(vehicles.status);

    return results;
  }

  async getRecentBookings(limit: number): Promise<(Booking & { requester: User; vehicle?: Vehicle; driver?: Driver })[]> {
    return await db
      .select({
        booking: bookings,
        requester: users,
        vehicle: vehicles,
        driver: drivers,
      })
      .from(bookings)
      .leftJoin(users, eq(bookings.requesterId, users.id))
      .leftJoin(vehicles, eq(bookings.vehicleId, vehicles.id))
      .leftJoin(drivers, eq(bookings.driverId, drivers.id))
      .orderBy(desc(bookings.createdAt))
      .limit(limit)
      .then(results => 
        results.map(r => ({
          ...r.booking,
          requester: r.requester!,
          vehicle: r.vehicle || undefined,
          driver: r.driver || undefined,
        }))
      );
  }
}

export const storage = new DatabaseStorage();
