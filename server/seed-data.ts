import { db } from './db';
import { users, vehicles, drivers, bookings, approvals, activityLogs } from '@shared/schema';
import bcrypt from 'bcrypt';

export async function seedData() {
  console.log('🌱 Seeding database with sample data...');

  // Clear existing data
  await db.delete(activityLogs);
  await db.delete(approvals);
  await db.delete(bookings);
  await db.delete(drivers);
  await db.delete(vehicles);
  await db.delete(users);

  // Seed Users
  const hashedPassword = await bcrypt.hash('password123', 10);
  const adminPassword = await bcrypt.hash('admin123', 10);
  const managerPassword = await bcrypt.hash('manager123', 10);
  const userPassword = await bcrypt.hash('user123', 10);

  const seedUsers = await db.insert(users).values([
    {
      username: 'admin',
      password: adminPassword,
      fullName: 'Super Administrator',
      email: 'admin@nickel-mining.com',
      role: 'admin',
      department: 'IT Management'
    },
    {
      username: 'manager1',
      password: managerPassword,
      fullName: 'Budi Santoso',
      email: 'budi.santoso@nickel-mining.com',
      role: 'approver',
      department: 'Operations',
      approvalLevel: 'level1'
    },
    {
      username: 'manager2',
      password: managerPassword,
      fullName: 'Siti Rahayu',
      email: 'siti.rahayu@nickel-mining.com',
      role: 'approver',
      department: 'Operations',
      approvalLevel: 'level2'
    },
    {
      username: 'user1',
      password: userPassword,
      fullName: 'Ahmad Wijaya',
      email: 'ahmad.wijaya@nickel-mining.com',
      role: 'requester',
      department: 'Mining Operations'
    },
    {
      username: 'user2',
      password: userPassword,
      fullName: 'Dewi Lestari',
      email: 'dewi.lestari@nickel-mining.com',
      role: 'requester',
      department: 'Maintenance'
    },
    {
      username: 'user3',
      password: userPassword,
      fullName: 'Rudi Hermawan',
      email: 'rudi.hermawan@nickel-mining.com',
      role: 'requester',
      department: 'Safety & Security'
    },
    {
      username: 'user4',
      password: userPassword,
      fullName: 'Maya Sari',
      email: 'maya.sari@nickel-mining.com',
      role: 'requester',
      department: 'Human Resources'
    },
    {
      username: 'user5',
      password: userPassword,
      fullName: 'Andi Pratama',
      email: 'andi.pratama@nickel-mining.com',
      role: 'requester',
      department: 'Procurement'
    }
  ]).returning();

  // Seed Vehicles
  const seedVehicles = await db.insert(vehicles).values([
    {
      plateNumber: 'B1234CD',
      brand: 'Toyota',
      model: 'Hilux',
      year: 2023,
      capacity: 5,
      fuelType: 'diesel',
      status: 'available',
      nextMaintenance: new Date('2025-08-15')
    },
    {
      plateNumber: 'B5678EF',
      brand: 'Mitsubishi',
      model: 'Pajero Sport',
      year: 2022,
      capacity: 7,
      fuelType: 'diesel',
      status: 'available',
      nextMaintenance: new Date('2025-09-20')
    },
    {
      plateNumber: 'B9012GH',
      brand: 'Isuzu',
      model: 'D-Max',
      year: 2023,
      capacity: 5,
      fuelType: 'diesel',
      status: 'in_use',
      nextMaintenance: new Date('2025-07-30')
    },
    {
      plateNumber: 'B3456IJ',
      brand: 'Ford',
      model: 'Ranger',
      year: 2021,
      capacity: 5,
      fuelType: 'diesel',
      status: 'available',
      nextMaintenance: new Date('2025-10-10')
    },
    {
      plateNumber: 'B7890KL',
      brand: 'Hino',
      model: 'Dutro',
      year: 2022,
      capacity: 3,
      fuelType: 'diesel',
      status: 'maintenance',
      nextMaintenance: new Date('2025-12-01')
    },
    {
      plateNumber: 'B2345MN',
      brand: 'Suzuki',
      model: 'Carry',
      year: 2023,
      capacity: 3,
      fuelType: 'bensin',
      status: 'available',
      nextMaintenance: new Date('2025-08-25')
    },
    {
      plateNumber: 'B6789OP',
      brand: 'Daihatsu',
      model: 'Gran Max',
      year: 2022,
      capacity: 8,
      fuelType: 'bensin',
      status: 'available',
      nextMaintenance: new Date('2025-11-15')
    },
    {
      plateNumber: 'B1357QR',
      brand: 'Toyota',
      model: 'Avanza',
      year: 2021,
      capacity: 7,
      fuelType: 'bensin',
      status: 'in_use',
      nextMaintenance: new Date('2025-09-05')
    }
  ]).returning();

  // Seed Drivers
  const seedDrivers = await db.insert(drivers).values([
    {
      employeeId: 'DRV001',
      fullName: 'Joko Susilo',
      licenseNumber: '1234567890123456',
      phone: '081234567890',
      isAvailable: true
    },
    {
      employeeId: 'DRV002',
      fullName: 'Bambang Wijaya',
      licenseNumber: '2345678901234567',
      phone: '081234567891',
      isAvailable: true
    },
    {
      employeeId: 'DRV003',
      fullName: 'Suratno',
      licenseNumber: '3456789012345678',
      phone: '081234567892',
      isAvailable: false
    },
    {
      employeeId: 'DRV004',
      fullName: 'Agus Prasetyo',
      licenseNumber: '4567890123456789',
      phone: '081234567893',
      isAvailable: true
    },
    {
      employeeId: 'DRV005',
      fullName: 'Hendra Gunawan',
      licenseNumber: '5678901234567890',
      phone: '081234567894',
      isAvailable: true
    },
    {
      employeeId: 'DRV006',
      fullName: 'Wawan Setiawan',
      licenseNumber: '6789012345678901',
      phone: '081234567895',
      isAvailable: false
    }
  ]).returning();

  // Seed Bookings with different statuses and dates
  const now = new Date();
  const getRandomDate = (daysBack: number, daysForward: number) => {
    const randomDays = Math.floor(Math.random() * (daysBack + daysForward)) - daysBack;
    const date = new Date(now);
    date.setDate(date.getDate() + randomDays);
    return date;
  };

  const bookingData = [
    {
      bookingNumber: 'BK-2025-001',
      purpose: 'Inspeksi Lokasi Tambang Area A',
      destination: 'Site Tambang Blok A1',
      departureDate: getRandomDate(7, 3),
      returnDate: (() => {
        const depDate = getRandomDate(7, 3);
        const retDate = new Date(depDate);
        retDate.setDate(retDate.getDate() + 1);
        return retDate;
      })(),
      departureTime: '08:00',
      returnTime: '17:00',
      passengers: 3,
      status: 'completed' as const,
      requesterId: seedUsers[3].id, // Ahmad Wijaya
      vehicleId: seedVehicles[0].id,
      driverId: seedDrivers[0].id
    },
    {
      bookingNumber: 'BK-2025-002',
      purpose: 'Meeting dengan Vendor Equipment',
      destination: 'Kantor Vendor - Bekasi',
      departureDate: getRandomDate(5, 5),
      returnDate: (() => {
        const depDate = getRandomDate(5, 5);
        const retDate = new Date(depDate);
        retDate.setDate(retDate.getDate() + 1);
        return retDate;
      })(),
      departureTime: '09:00',
      returnTime: '16:00',
      passengers: 2,
      status: 'pending' as const,
      requesterId: seedUsers[4].id, // Dewi Lestari
      vehicleId: null,
      driverId: null
    },
    {
      bookingNumber: 'BK-2025-003',
      purpose: 'Training Safety Mining',
      destination: 'Training Center Jakarta',
      departureDate: getRandomDate(3, 7),
      returnDate: (() => {
        const depDate = getRandomDate(3, 7);
        const retDate = new Date(depDate);
        retDate.setDate(retDate.getDate() + 2);
        return retDate;
      })(),
      departureTime: '07:30',
      returnTime: '18:00',
      passengers: 4,
      status: 'approved_level1' as const,
      requesterId: seedUsers[5].id, // Rudi Hermawan
      vehicleId: null,
      driverId: null
    },
    {
      bookingNumber: 'BK-2025-004',
      purpose: 'Pengambilan Sample Nickel',
      destination: 'Laboratory Universitas Indonesia',
      departureDate: getRandomDate(2, 8),
      returnDate: (() => {
        const depDate = getRandomDate(2, 8);
        const retDate = new Date(depDate);
        retDate.setDate(retDate.getDate() + 1);
        return retDate;
      })(),
      departureTime: '10:00',
      returnTime: '15:00',
      passengers: 2,
      status: 'approved' as const,
      requesterId: seedUsers[3].id, // Ahmad Wijaya
      vehicleId: seedVehicles[1].id,
      driverId: seedDrivers[1].id
    },
    {
      bookingNumber: 'BK-2025-005',
      purpose: 'Rekrutmen Karyawan Baru',
      destination: 'Universitas Trisakti',
      departureDate: getRandomDate(1, 10),
      returnDate: (() => {
        const depDate = getRandomDate(1, 10);
        const retDate = new Date(depDate);
        retDate.setDate(retDate.getDate() + 1);
        return retDate;
      })(),
      departureTime: '08:30',
      returnTime: '16:30',
      passengers: 3,
      status: 'rejected' as const,
      requesterId: seedUsers[6].id, // Maya Sari
      vehicleId: null,
      driverId: null
    },
    {
      bookingNumber: 'BK-2025-006',
      purpose: 'Survey Lokasi Baru',
      destination: 'Sulawesi Tengah',
      departureDate: getRandomDate(0, 12),
      returnDate: (() => {
        const depDate = getRandomDate(0, 12);
        const retDate = new Date(depDate);
        retDate.setDate(retDate.getDate() + 3);
        return retDate;
      })(),
      departureTime: '06:00',
      returnTime: '20:00',
      passengers: 5,
      status: 'approved_level2' as const,
      requesterId: seedUsers[7].id, // Andi Pratama
      vehicleId: null,
      driverId: null
    },
    {
      bookingNumber: 'BK-2025-007',
      purpose: 'Maintenance Equipment Tambang',
      destination: 'Site Maintenance - Area B',
      departureDate: getRandomDate(10, 2),
      returnDate: (() => {
        const depDate = getRandomDate(10, 2);
        const retDate = new Date(depDate);
        retDate.setDate(retDate.getDate() + 1);
        return retDate;
      })(),
      departureTime: '07:00',
      returnTime: '17:00',
      passengers: 4,
      status: 'completed' as const,
      requesterId: seedUsers[4].id, // Dewi Lestari
      vehicleId: seedVehicles[2].id,
      driverId: seedDrivers[2].id
    },
    {
      bookingNumber: 'BK-2025-008',
      purpose: 'Audit Internal Mining Operations',
      destination: 'Kantor Pusat Jakarta',
      departureDate: getRandomDate(8, 4),
      returnDate: (() => {
        const depDate = getRandomDate(8, 4);
        const retDate = new Date(depDate);
        retDate.setDate(retDate.getDate() + 1);
        return retDate;
      })(),
      departureTime: '09:30',
      returnTime: '16:00',
      passengers: 3,
      status: 'completed' as const,
      requesterId: seedUsers[5].id, // Rudi Hermawan
      vehicleId: seedVehicles[3].id,
      driverId: seedDrivers[3].id
    }
  ];

  const seedBookings = await db.insert(bookings).values(bookingData).returning();

  // Seed Approvals for bookings that have been processed
  const approvalData = [];
  
  // For completed bookings - add both level approvals
  for (const booking of seedBookings.filter(b => b.status === 'completed' || b.status === 'approved')) {
    approvalData.push(
      {
        bookingId: booking.id,
        approverId: seedUsers[1].id, // Manager Level 1
        level: 'level1' as const,
        status: 'approved' as const,
        comments: 'Disetujui untuk kebutuhan operasional',
        approvedAt: new Date(booking.createdAt.getTime() + 3600000) // 1 hour after booking
      },
      {
        bookingId: booking.id,
        approverId: seedUsers[2].id, // Manager Level 2
        level: 'level2' as const,
        status: 'approved' as const,
        comments: 'Final approval granted',
        approvedAt: new Date(booking.createdAt.getTime() + 7200000) // 2 hours after booking
      }
    );
  }

  // For level1 approved bookings
  for (const booking of seedBookings.filter(b => b.status === 'approved_level1')) {
    approvalData.push({
      bookingId: booking.id,
      approverId: seedUsers[1].id, // Manager Level 1
      level: 'level1' as const,
      status: 'approved' as const,
      comments: 'Approved untuk tahap pertama',
      approvedAt: new Date(booking.createdAt.getTime() + 3600000)
    });
  }

  // For level2 approved bookings
  for (const booking of seedBookings.filter(b => b.status === 'approved_level2')) {
    approvalData.push(
      {
        bookingId: booking.id,
        approverId: seedUsers[1].id,
        level: 'level1' as const,
        status: 'approved' as const,
        comments: 'Approved tahap pertama',
        approvedAt: new Date(booking.createdAt.getTime() + 3600000)
      },
      {
        bookingId: booking.id,
        approverId: seedUsers[2].id,
        level: 'level2' as const,
        status: 'approved' as const,
        comments: 'Menunggu konfirmasi final',
        approvedAt: new Date(booking.createdAt.getTime() + 7200000)
      }
    );
  }

  // For rejected bookings
  for (const booking of seedBookings.filter(b => b.status === 'rejected')) {
    approvalData.push({
      bookingId: booking.id,
      approverId: seedUsers[1].id,
      level: 'level1' as const,
      status: 'rejected' as const,
      comments: 'Tidak dapat disetujui karena konflik dengan agenda lain',
      approvedAt: new Date(booking.createdAt.getTime() + 3600000)
    });
  }

  if (approvalData.length > 0) {
    await db.insert(approvals).values(approvalData);
  }

  // Seed Activity Logs
  const activityData = [
    {
      userId: seedUsers[0].id,
      action: 'LOGIN',
      entityType: 'USER',
      entityId: seedUsers[0].id,
      details: 'Admin login ke sistem',
      ipAddress: '192.168.1.100'
    },
    {
      userId: seedUsers[3].id,
      action: 'CREATE',
      entityType: 'BOOKING',
      entityId: seedBookings[0].id,
      details: 'Membuat booking baru BK-2025-001',
      ipAddress: '192.168.1.101'
    },
    {
      userId: seedUsers[1].id,
      action: 'APPROVE',
      entityType: 'BOOKING',
      entityId: seedBookings[0].id,
      details: 'Menyetujui booking BK-2025-001 level 1',
      ipAddress: '192.168.1.102'
    },
    {
      userId: seedUsers[0].id,
      action: 'CREATE',
      entityType: 'VEHICLE',
      entityId: seedVehicles[0].id,
      details: 'Menambahkan kendaraan baru B1234CD',
      ipAddress: '192.168.1.100'
    },
    {
      userId: seedUsers[0].id,
      action: 'CREATE',
      entityType: 'DRIVER',
      entityId: seedDrivers[0].id,
      details: 'Menambahkan driver baru Joko Susilo',
      ipAddress: '192.168.1.100'
    }
  ];

  await db.insert(activityLogs).values(activityData);

  console.log('✅ Database seeded successfully with sample data!');
  console.log('📊 Created:');
  console.log(`  - ${seedUsers.length} users`);
  console.log(`  - ${seedVehicles.length} vehicles`);
  console.log(`  - ${seedDrivers.length} drivers`);
  console.log(`  - ${seedBookings.length} bookings`);
  console.log(`  - ${approvalData.length} approvals`);
  console.log(`  - ${activityData.length} activity logs`);
}