import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useQuery } from "@tanstack/react-query";
import { Calendar, Download, FileText, Filter, BarChart3 } from "lucide-react";
import { useToast } from "@/hooks/use-toast";

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

export default function Reports() {
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");
  const [statusFilter, setStatusFilter] = useState("all");
  const [isExporting, setIsExporting] = useState(false);
  const { toast } = useToast();

  const { data: bookings, isLoading } = useQuery<Booking[]>({
    queryKey: ['/api/bookings'],
  });

  const handleExport = async () => {
    setIsExporting(true);
    try {
      const params = new URLSearchParams();
      if (startDate) params.append('startDate', startDate);
      if (endDate) params.append('endDate', endDate);

      const response = await fetch(`/api/reports/export?${params.toString()}`, {
        method: 'GET',
        credentials: 'include',
      });

      if (!response.ok) {
        throw new Error('Failed to export report');
      }

      // Create download link
      const blob = await response.blob();
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'booking_report.xlsx';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      window.URL.revokeObjectURL(url);

      toast({ title: "Laporan berhasil di-export" });
    } catch (error: any) {
      toast({ 
        title: "Error", 
        description: error.message || "Gagal export laporan", 
        variant: "destructive" 
      });
    } finally {
      setIsExporting(false);
    }
  };

  const getFilteredBookings = () => {
    if (!bookings) return [];
    
    let filtered = bookings;
    
    if (startDate && endDate) {
      const start = new Date(startDate);
      const end = new Date(endDate);
      filtered = filtered.filter(booking => {
        const bookingDate = new Date(booking.departureDate);
        return bookingDate >= start && bookingDate <= end;
      });
    }
    
    if (statusFilter !== "all") {
      filtered = filtered.filter(booking => booking.status === statusFilter);
    }
    
    return filtered;
  };

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

  const filteredBookings = getFilteredBookings();

  // Calculate summary statistics
  const totalBookings = filteredBookings.length;
  const approvedBookings = filteredBookings.filter(b => b.status === 'approved').length;
  const pendingBookings = filteredBookings.filter(b => b.status === 'pending').length;
  const rejectedBookings = filteredBookings.filter(b => b.status === 'rejected').length;

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Laporan Pemesanan</h2>
          <p className="text-sm text-gray-500">Generate dan export laporan pemesanan kendaraan</p>
        </div>
        <Button 
          onClick={handleExport}
          disabled={isExporting}
          className="bg-vehicleflow-primary hover:bg-blue-700"
        >
          <Download className="w-4 h-4 mr-2" />
          {isExporting ? "Exporting..." : "Export Excel"}
        </Button>
      </div>

      {/* Filter Section */}
      <Card className="stats-card mb-6">
        <CardHeader>
          <CardTitle className="flex items-center space-x-2">
            <Filter className="w-5 h-5" />
            <span>Filter Laporan</span>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <Label htmlFor="startDate">Tanggal Mulai</Label>
              <Input
                id="startDate"
                type="date"
                value={startDate}
                onChange={(e) => setStartDate(e.target.value)}
              />
            </div>
            <div>
              <Label htmlFor="endDate">Tanggal Akhir</Label>
              <Input
                id="endDate"
                type="date"
                value={endDate}
                onChange={(e) => setEndDate(e.target.value)}
              />
            </div>
            <div>
              <Label htmlFor="status">Status</Label>
              <Select value={statusFilter} onValueChange={setStatusFilter}>
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">Semua Status</SelectItem>
                  <SelectItem value="pending">Menunggu Persetujuan</SelectItem>
                  <SelectItem value="approved">Disetujui</SelectItem>
                  <SelectItem value="rejected">Ditolak</SelectItem>
                  <SelectItem value="completed">Selesai</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="flex items-end">
              <Button 
                variant="outline" 
                onClick={() => {
                  setStartDate("");
                  setEndDate("");
                  setStatusFilter("all");
                }}
                className="w-full"
              >
                Reset Filter
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Summary Statistics */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <Card className="stats-card">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-500">Total Pemesanan</p>
                <p className="text-3xl font-bold text-gray-900">{totalBookings}</p>
              </div>
              <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <FileText className="w-6 h-6 text-vehicleflow-primary" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card className="stats-card">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-500">Disetujui</p>
                <p className="text-3xl font-bold text-green-600">{approvedBookings}</p>
              </div>
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <BarChart3 className="w-6 h-6 text-green-600" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card className="stats-card">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-500">Pending</p>
                <p className="text-3xl font-bold text-yellow-600">{pendingBookings}</p>
              </div>
              <div className="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <Calendar className="w-6 h-6 text-yellow-600" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card className="stats-card">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-500">Ditolak</p>
                <p className="text-3xl font-bold text-red-600">{rejectedBookings}</p>
              </div>
              <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <FileText className="w-6 h-6 text-red-600" />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Report Table */}
      <Card className="stats-card">
        <CardHeader>
          <CardTitle>Detail Laporan Pemesanan</CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="space-y-4">
              {[1, 2, 3, 4, 5].map(i => (
                <div key={i} className="animate-pulse">
                  <div className="h-16 bg-gray-200 rounded mb-2"></div>
                </div>
              ))}
            </div>
          ) : filteredBookings.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b border-gray-200">
                    <th className="text-left py-3 px-2 font-medium text-gray-700">Booking ID</th>
                    <th className="text-left py-3 px-2 font-medium text-gray-700">Pemohon</th>
                    <th className="text-left py-3 px-2 font-medium text-gray-700">Tujuan</th>
                    <th className="text-left py-3 px-2 font-medium text-gray-700">Tanggal</th>
                    <th className="text-left py-3 px-2 font-medium text-gray-700">Kendaraan</th>
                    <th className="text-left py-3 px-2 font-medium text-gray-700">Status</th>
                    <th className="text-left py-3 px-2 font-medium text-gray-700">Dibuat</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredBookings.map((booking) => (
                    <tr key={booking.id} className="border-b border-gray-100 hover:bg-gray-50">
                      <td className="py-3 px-2 text-sm font-medium text-vehicleflow-primary">
                        {booking.bookingNumber}
                      </td>
                      <td className="py-3 px-2 text-sm">
                        <div>
                          <p className="font-medium text-gray-900">{booking.requester.fullName}</p>
                          {booking.requester.department && (
                            <p className="text-gray-500">{booking.requester.department}</p>
                          )}
                        </div>
                      </td>
                      <td className="py-3 px-2 text-sm text-gray-900">
                        <div>
                          <p className="font-medium">{booking.purpose}</p>
                          <p className="text-gray-500">{booking.destination}</p>
                        </div>
                      </td>
                      <td className="py-3 px-2 text-sm text-gray-900">
                        <div>
                          <p>{formatDate(booking.departureDate)}</p>
                          <p className="text-gray-500">{booking.departureTime} - {booking.returnTime}</p>
                        </div>
                      </td>
                      <td className="py-3 px-2 text-sm text-gray-900">
                        {booking.vehicle ? (
                          <div>
                            <p className="font-medium">{booking.vehicle.plateNumber}</p>
                            <p className="text-gray-500">{booking.vehicle.brand} {booking.vehicle.model}</p>
                          </div>
                        ) : (
                          <span className="text-gray-400">Belum ditentukan</span>
                        )}
                      </td>
                      <td className="py-3 px-2 text-sm">
                        {getStatusBadge(booking.status)}
                      </td>
                      <td className="py-3 px-2 text-sm text-gray-500">
                        {formatDateTime(booking.createdAt)}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <div className="text-center py-12 text-gray-500">
              <FileText className="w-12 h-12 mx-auto mb-4 opacity-50" />
              <p className="text-lg font-medium mb-2">Tidak ada data</p>
              <p className="text-sm">
                {startDate || endDate ? 
                  "Tidak ada pemesanan dalam rentang tanggal yang dipilih" : 
                  "Belum ada pemesanan yang tersedia"
                }
              </p>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
