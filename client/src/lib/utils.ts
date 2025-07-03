import { type ClassValue, clsx } from "clsx"
import { twMerge } from "tailwind-merge"

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function formatDate(dateString: string | Date): string {
  const date = typeof dateString === 'string' ? new Date(dateString) : dateString;
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
}

export function formatDateTime(dateString: string | Date): string {
  const date = typeof dateString === 'string' ? new Date(dateString) : dateString;
  return date.toLocaleString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

export function formatTime(timeString: string): string {
  return timeString;
}

export function getStatusBadgeClass(status: string): string {
  switch (status) {
    case 'pending':
      return 'status-badge-pending';
    case 'approved_level1':
      return 'inline-flex items-center text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full';
    case 'approved_level2':
      return 'inline-flex items-center text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full';
    case 'approved':
      return 'status-badge-approved';
    case 'rejected':
      return 'status-badge-rejected';
    case 'completed':
      return 'inline-flex items-center text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full';
    default:
      return 'status-badge-pending';
  }
}

export function getStatusLabel(status: string): string {
  switch (status) {
    case 'pending':
      return 'Menunggu Persetujuan';
    case 'approved_level1':
      return 'Disetujui Level 1';
    case 'approved_level2':
      return 'Disetujui Level 2';
    case 'approved':
      return 'Disetujui';
    case 'rejected':
      return 'Ditolak';
    case 'completed':
      return 'Selesai';
    case 'in_progress':
      return 'Sedang Berlangsung';
    default:
      return status;
  }
}

export function getVehicleStatusLabel(status: string): string {
  switch (status) {
    case 'available':
      return 'Tersedia';
    case 'in_use':
      return 'Digunakan';
    case 'maintenance':
      return 'Maintenance';
    default:
      return status;
  }
}

export function generateBookingNumber(): string {
  const now = new Date();
  const year = now.getFullYear();
  const timestamp = Date.now().toString().slice(-6);
  return `VB-${year}-${timestamp}`;
}

export function validateEmail(email: string): boolean {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

export function validatePhone(phone: string): boolean {
  const phoneRegex = /^(\+62|62|0)[0-9]{9,13}$/;
  return phoneRegex.test(phone);
}

export function validatePlateNumber(plateNumber: string): boolean {
  // Indonesian plate number format: B1234CD
  const plateRegex = /^[A-Z]{1,2}[0-9]{1,4}[A-Z]{1,3}$/;
  return plateRegex.test(plateNumber);
}

export function formatCurrency(amount: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount);
}

export function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 Bytes';
  
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

export function debounce<T extends (...args: any[]) => any>(
  func: T,
  delay: number
): (...args: Parameters<T>) => void {
  let timeoutId: NodeJS.Timeout;
  return (...args: Parameters<T>) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => func.apply(null, args), delay);
  };
}

export function throttle<T extends (...args: any[]) => any>(
  func: T,
  limit: number
): (...args: Parameters<T>) => void {
  let inThrottle: boolean;
  return (...args: Parameters<T>) => {
    if (!inThrottle) {
      func.apply(null, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  };
}
