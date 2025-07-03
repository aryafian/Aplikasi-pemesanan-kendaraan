import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { useQuery, useMutation } from "@tanstack/react-query";
import { BookingForm } from "@/components/booking/booking-form";
import { useState } from "react";
import { Plus, Eye } from "lucide-react";
import { queryClient } from "@/lib/queryClient";

interface Booking {
  id: number;
  bookingNumber: string;
  purpose: string;
  destination: string;
  departureDate: string;
  departureTime: string;
  returnTime: string;
  status: string;
  requester: {
    fullName: string;
    department?: string;
  };
  vehicle?: {
    plateNumber: string;
    brand: string;
    model: string;
  };
  driver?: {
    fullName: string;
  };
  createdAt: string;
}

export default function Booking() {
  const [showForm, setShowForm] = useState(false);

  const { data: bookings, isLoading } = useQuery<Booking[]>({
    queryKey: ['/api/bookings'],
  });

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'pending':
        return (
          <span className="status-badge-pending">
            <div className="status-indicator bg-yellow-400"></div>
            Menunggu Persetujuan
          </span>
        );
      case 'approved_level1':
        return (
          <span className="inline-flex items-center text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
            <div className="status-indicator bg-blue-400"></div>
            Disetujui Level 1
          </span>
        );
      case 'approved':
        return (
          <span className="status-badge-approved">
            <div className="status-indicator bg-green-400"></div>
            Disetujui
          </span>
        );
      case 'rejected':
        return (
          <span className="status-badge-rejected">
            <div className="status-indicator bg-red-400"></div>
            Ditolak
          </span>
        );
      case 'completed':
        return (
          <span className="inline-flex items-center text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full">
            <div className="status-indicator bg-gray-400"></div>
            Selesai
          </span>
        );
      default:
        return (
          <span className="status-badge-pending">
            <div className="status-indicator bg-gray-400"></div>
            {status}
          </span>
        );
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    });
  };

  const formatDateTime = (dateString: string) => {
    return new Date(dateString).toLocaleString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  if (showForm) {
    return (
      <div>
        <div className="flex items-center justify-between mb-6">
          <div>
            <h2 className="text-2xl font-bold text-gray-900">Pemesanan Kendaraan Baru</h2>
            <p className="text-sm text-gray-500">Isi formulir untuk mengajukan pemesanan kendaraan</p>
          </div>
          <Button variant="outline" onClick={() => setShowForm(false)}>
            Kembali
          </Button>
        </div>
        
        <BookingForm onSuccess={() => {
          setShowForm(false);
          queryClient.invalidateQueries({ queryKey: ['/api/bookings'] });
        }} />
      </div>
    );
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Daftar Pemesanan</h2>
          <p className="text-sm text-gray-500">Kelola semua pemesanan kendaraan</p>
        </div>
        <Button onClick={() => setShowForm(true)} className="bg-vehicleflow-primary hover:bg-blue-700">
          <Plus className="w-4 h-4 mr-2" />
          Pemesanan Baru
        </Button>
      </div>

      {isLoading ? (
        <div className="grid gap-4">
          {[1, 2, 3].map(i => (
            <Card key={i} className="animate-pulse">
              <CardContent className="p-6">
                <div className="space-y-3">
                  <div className="h-4 bg-gray-200 rounded w-1/4"></div>
                  <div className="h-4 bg-gray-200 rounded w-1/2"></div>
                  <div className="h-4 bg-gray-200 rounded w-1/3"></div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      ) : (
        <div className="grid gap-4">
          {bookings?.map((booking) => (
            <Card key={booking.id} className="stats-card hover:shadow-md transition-shadow">
              <CardContent className="p-6">
                <div className="flex items-start justify-between">
                  <div className="flex-1">
                    <div className="flex items-center space-x-3 mb-2">
                      <h3 className="text-lg font-semibold text-gray-900">{booking.purpose}</h3>
                      {getStatusBadge(booking.status)}
                    </div>
                    
                    <p className="text-sm text-gray-600 mb-3">
                      <strong>ID:</strong> {booking.bookingNumber}
                    </p>
                    
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                      <div>
                        <p className="text-sm font-medium text-gray-700">Pemohon</p>
                        <p className="text-sm text-gray-900">
                          {booking.requester.fullName}
                          {booking.requester.department && ` - ${booking.requester.department}`}
                        </p>
                      </div>
                      <div>
                        <p className="text-sm font-medium text-gray-700">Tanggal & Waktu</p>
                        <p className="text-sm text-gray-900">
                          {formatDate(booking.departureDate)}, {booking.departureTime}-{booking.returnTime}
                        </p>
                      </div>
                      <div>
                        <p className="text-sm font-medium text-gray-700">Destinasi</p>
                        <p className="text-sm text-gray-900">{booking.destination}</p>
                      </div>
                    </div>
                    
                    {(booking.vehicle || booking.driver) && (
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        {booking.vehicle && (
                          <div>
                            <p className="text-sm font-medium text-gray-700">Kendaraan</p>
                            <p className="text-sm text-gray-900">
                              {booking.vehicle.plateNumber} - {booking.vehicle.brand} {booking.vehicle.model}
                            </p>
                          </div>
                        )}
                        {booking.driver && (
                          <div>
                            <p className="text-sm font-medium text-gray-700">Driver</p>
                            <p className="text-sm text-gray-900">{booking.driver.fullName}</p>
                          </div>
                        )}
                      </div>
                    )}
                    
                    <p className="text-xs text-gray-500">
                      Dibuat: {formatDateTime(booking.createdAt)}
                    </p>
                  </div>
                  
                  <div className="ml-4">
                    <Button variant="ghost" size="sm">
                      <Eye className="w-4 h-4" />
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
          
          {(!bookings || bookings.length === 0) && (
            <Card className="stats-card">
              <CardContent className="p-12 text-center">
                <div className="text-gray-500">
                  <Plus className="w-12 h-12 mx-auto mb-4 opacity-50" />
                  <p className="text-lg font-medium mb-2">Belum ada pemesanan</p>
                  <p className="text-sm mb-4">Mulai dengan membuat pemesanan kendaraan baru</p>
                  <Button onClick={() => setShowForm(true)} className="bg-vehicleflow-primary hover:bg-blue-700">
                    <Plus className="w-4 h-4 mr-2" />
                    Buat Pemesanan
                  </Button>
                </div>
              </CardContent>
            </Card>
          )}
        </div>
      )}
    </div>
  );
}
