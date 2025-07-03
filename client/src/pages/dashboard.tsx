import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { UsageChart } from "@/components/charts/usage-chart";
import { StatusChart } from "@/components/charts/status-chart";
import { apiRequest } from "@/lib/queryClient";
import { useToast } from "@/hooks/use-toast";
import { auth } from "@/lib/auth";
import { useState } from "react";
import { 
  Calendar, 
  Clock, 
  Car, 
  TrendingUp, 
  Plus, 
  CheckCircle, 
  Download,
  ArrowUp,
  Database
} from "lucide-react";
import { Link } from "wouter";

interface DashboardStats {
  totalBookings: number;
  pendingApproval: number;
  activeVehicles: number;
  efficiency: number;
}

interface RecentBooking {
  id: number;
  bookingNumber: string;
  purpose: string;
  departureDate: string;
  departureTime: string;
  returnTime: string;
  status: string;
  requester: {
    fullName: string;
  };
  vehicle?: {
    plateNumber: string;
  };
}

export default function Dashboard() {
  const { toast } = useToast();
  const queryClient = useQueryClient();
  const [user, setUser] = useState<any>(null);

  // Get current user
  useQuery({
    queryKey: ['/api/auth/me'],
    queryFn: async () => {
      const userData = await auth.getCurrentUser();
      setUser(userData);
      return userData;
    }
  });

  const { data: stats, isLoading: statsLoading } = useQuery<DashboardStats>({
    queryKey: ['/api/dashboard/stats'],
  });

  const { data: recentBookings, isLoading: bookingsLoading } = useQuery<RecentBooking[]>({
    queryKey: ['/api/dashboard/recent-bookings'],
  });

  const seedDataMutation = useMutation({
    mutationFn: async () => {
      const response = await fetch('/api/seed-data', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        credentials: 'include',
      });
      if (!response.ok) {
        throw new Error('Failed to seed data');
      }
      return response.json();
    },
    onSuccess: () => {
      toast({
        title: "Berhasil!",
        description: "Data sample berhasil dibuat di database.",
      });
      // Refresh all dashboard data
      queryClient.invalidateQueries({ queryKey: ['/api/dashboard/stats'] });
      queryClient.invalidateQueries({ queryKey: ['/api/dashboard/recent-bookings'] });
      queryClient.invalidateQueries({ queryKey: ['/api/dashboard/usage-data'] });
      queryClient.invalidateQueries({ queryKey: ['/api/dashboard/vehicle-status'] });
    },
    onError: () => {
      toast({
        title: "Error",
        description: "Gagal membuat data sample.",
        variant: "destructive",
      });
    },
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

  return (
    <div>
      {/* Admin Controls */}
      {user && auth.isAdmin(user) && (
        <div className="mb-6">
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center justify-between">
                <div>
                  <h3 className="text-lg font-semibold text-gray-900">Admin Tools</h3>
                  <p className="text-sm text-gray-500">Generate sample data untuk testing sistem</p>
                </div>
                <Button
                  onClick={() => seedDataMutation.mutate()}
                  disabled={seedDataMutation.isPending}
                  className="flex items-center space-x-2"
                >
                  <Database className="w-4 h-4" />
                  <span>
                    {seedDataMutation.isPending ? 'Generating...' : 'Generate Data Sample'}
                  </span>
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>
      )}

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <Card className="stats-card">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-500">Total Pemesanan</p>
                <p className="text-3xl font-bold text-gray-900">
                  {statsLoading ? '...' : stats?.totalBookings || 0}
                </p>
                <p className="text-sm text-vehicleflow-success mt-1">
                  <ArrowUp className="w-4 h-4 inline mr-1" />
                  12% dari bulan lalu
                </p>
              </div>
              <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <Calendar className="w-6 h-6 text-vehicleflow-primary" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card className="stats-card">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-500">Menunggu Persetujuan</p>
                <p className="text-3xl font-bold text-gray-900">
                  {statsLoading ? '...' : stats?.pendingApproval || 0}
                </p>
                <p className="text-sm text-vehicleflow-warning mt-1">
                  <Clock className="w-4 h-4 inline mr-1" />
                  3 urgent
                </p>
              </div>
              <div className="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <Clock className="w-6 h-6 text-vehicleflow-accent" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card className="stats-card">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-500">Kendaraan Aktif</p>
                <p className="text-3xl font-bold text-gray-900">
                  {statsLoading ? '...' : stats?.activeVehicles || 0}
                </p>
                <p className="text-sm text-gray-500 mt-1">dari 15 total</p>
              </div>
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <Car className="w-6 h-6 text-vehicleflow-success" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card className="stats-card">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-500">Efisiensi Bulan Ini</p>
                <p className="text-3xl font-bold text-gray-900">
                  {statsLoading ? '...' : `${stats?.efficiency || 0}%`}
                </p>
                <p className="text-sm text-vehicleflow-success mt-1">
                  <TrendingUp className="w-4 h-4 inline mr-1" />
                  Target: 80%
                </p>
              </div>
              <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <TrendingUp className="w-6 h-6 text-purple-600" />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Charts Section */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <Card className="stats-card">
          <CardContent className="p-6">
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-lg font-semibold text-gray-900">Pemakaian Kendaraan</h3>
              <select className="text-sm border border-gray-300 rounded-lg px-3 py-1">
                <option value="7">7 Hari Terakhir</option>
                <option value="30">30 Hari Terakhir</option>
                <option value="90">3 Bulan Terakhir</option>
              </select>
            </div>
            <UsageChart />
          </CardContent>
        </Card>

        <Card className="stats-card">
          <CardContent className="p-6">
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-lg font-semibold text-gray-900">Status Kendaraan</h3>
              <div className="flex space-x-2">
                <span className="status-badge-approved">
                  <div className="status-indicator bg-green-400"></div>
                  Tersedia
                </span>
                <span className="inline-flex items-center text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                  <div className="status-indicator bg-blue-400"></div>
                  Digunakan
                </span>
                <span className="status-badge-rejected">
                  <div className="status-indicator bg-red-400"></div>
                  Maintenance
                </span>
              </div>
            </div>
            <StatusChart />
          </CardContent>
        </Card>
      </div>

      {/* Recent Activities */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Recent Bookings */}
        <div className="lg:col-span-2">
          <Card className="stats-card">
            <CardContent className="p-6">
              <div className="flex items-center justify-between mb-6">
                <h3 className="text-lg font-semibold text-gray-900">Pemesanan Terbaru</h3>
                <Link href="/booking">
                  <Button variant="ghost" size="sm">
                    Lihat Semua
                  </Button>
                </Link>
              </div>
              
              {bookingsLoading ? (
                <div className="space-y-4">
                  {[1, 2, 3].map(i => (
                    <div key={i} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg animate-pulse">
                      <div className="flex items-center space-x-4">
                        <div className="w-10 h-10 bg-gray-200 rounded-lg"></div>
                        <div>
                          <div className="w-32 h-4 bg-gray-200 rounded mb-2"></div>
                          <div className="w-24 h-3 bg-gray-200 rounded"></div>
                        </div>
                      </div>
                      <div className="w-20 h-6 bg-gray-200 rounded"></div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="space-y-4">
                  {recentBookings?.map((booking) => (
                    <div key={booking.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                      <div className="flex items-center space-x-4">
                        <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                          <Car className="w-5 h-5 text-vehicleflow-primary" />
                        </div>
                        <div>
                          <h4 className="font-medium text-gray-900">{booking.purpose}</h4>
                          <p className="text-sm text-gray-500">
                            {booking.requester.fullName} • {formatDate(booking.departureDate)} • {booking.departureTime}-{booking.returnTime}
                          </p>
                        </div>
                      </div>
                      <div className="flex items-center space-x-3">
                        {getStatusBadge(booking.status)}
                      </div>
                    </div>
                  ))}
                  
                  {(!recentBookings || recentBookings.length === 0) && (
                    <div className="text-center py-8 text-gray-500">
                      Belum ada pemesanan terbaru
                    </div>
                  )}
                </div>
              )}
            </CardContent>
          </Card>
        </div>

        {/* Quick Actions */}
        <div>
          <Card className="stats-card">
            <CardContent className="p-6">
              <h3 className="text-lg font-semibold text-gray-900 mb-6">Aksi Cepat</h3>
              <div className="space-y-4">
                <Link href="/booking">
                  <Button className="action-button-primary">
                    <Plus className="w-4 h-4" />
                    <span>Pemesanan Baru</span>
                  </Button>
                </Link>
                
                <Link href="/approval">
                  <Button className="action-button-accent">
                    <CheckCircle className="w-4 h-4" />
                    <span>Lihat Persetujuan</span>
                  </Button>
                </Link>
                
                <Link href="/reports">
                  <Button variant="outline" className="w-full">
                    <Download className="w-4 h-4 mr-2" />
                    <span>Export Laporan</span>
                  </Button>
                </Link>
              </div>

              <div className="mt-6 pt-6 border-t border-gray-200">
                <h4 className="text-sm font-semibold text-gray-900 mb-4">Notifikasi Sistem</h4>
                <div className="space-y-3">
                  <div className="flex items-start space-x-3">
                    <div className="w-2 h-2 bg-vehicleflow-accent rounded-full mt-2"></div>
                    <div>
                      <p className="text-sm text-gray-800">3 pemesanan menunggu persetujuan Level 2</p>
                      <p className="text-xs text-gray-500">5 menit lalu</p>
                    </div>
                  </div>
                  <div className="flex items-start space-x-3">
                    <div className="w-2 h-2 bg-vehicleflow-success rounded-full mt-2"></div>
                    <div>
                      <p className="text-sm text-gray-800">Kendaraan B1234CD tersedia kembali</p>
                      <p className="text-xs text-gray-500">1 jam lalu</p>
                    </div>
                  </div>
                  <div className="flex items-start space-x-3">
                    <div className="w-2 h-2 bg-vehicleflow-error rounded-full mt-2"></div>
                    <div>
                      <p className="text-sm text-gray-800">Service reminder: Kendaraan B5678EF</p>
                      <p className="text-xs text-gray-500">2 jam lalu</p>
                    </div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
