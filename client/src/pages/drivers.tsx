import { useState } from "react";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { useQuery, useMutation } from "@tanstack/react-query";
import { Plus, User, Edit, Trash2, Phone, IdCard } from "lucide-react";
import { queryClient, apiRequest } from "@/lib/queryClient";
import { useToast } from "@/hooks/use-toast";

interface Driver {
  id: number;
  employeeId: string;
  fullName: string;
  licenseNumber: string;
  phone: string;
  isAvailable: boolean;
  createdAt: string;
}

export default function Drivers() {
  const [showForm, setShowForm] = useState(false);
  const [editingDriver, setEditingDriver] = useState<Driver | null>(null);
  const { toast } = useToast();

  const [formData, setFormData] = useState({
    employeeId: '',
    fullName: '',
    licenseNumber: '',
    phone: '',
    isAvailable: true,
  });

  const { data: drivers, isLoading } = useQuery<Driver[]>({
    queryKey: ['/api/drivers'],
  });

  const createMutation = useMutation({
    mutationFn: (data: any) => apiRequest('POST', '/api/drivers', data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/drivers'] });
      setShowForm(false);
      resetForm();
      toast({ title: "Driver berhasil dibuat" });
    },
    onError: (error: any) => {
      toast({ title: "Error", description: error.message, variant: "destructive" });
    },
  });

  const resetForm = () => {
    setFormData({
      employeeId: '',
      fullName: '',
      licenseNumber: '',
      phone: '',
      isAvailable: true,
    });
    setEditingDriver(null);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    createMutation.mutate(formData);
  };

  if (showForm) {
    return (
      <div>
        <div className="flex items-center justify-between mb-6">
          <div>
            <h2 className="text-2xl font-bold text-gray-900">Tambah Driver Baru</h2>
            <p className="text-sm text-gray-500">Tambahkan driver baru ke sistem</p>
          </div>
          <Button variant="outline" onClick={() => {
            setShowForm(false);
            resetForm();
          }}>
            Batal
          </Button>
        </div>

        <Card className="stats-card">
          <CardContent className="p-6">
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <Label htmlFor="employeeId">ID Karyawan</Label>
                  <Input
                    id="employeeId"
                    value={formData.employeeId}
                    onChange={(e) => setFormData({ ...formData, employeeId: e.target.value })}
                    placeholder="Contoh: DRV001"
                    required
                  />
                </div>
                
                <div>
                  <Label htmlFor="fullName">Nama Lengkap</Label>
                  <Input
                    id="fullName"
                    value={formData.fullName}
                    onChange={(e) => setFormData({ ...formData, fullName: e.target.value })}
                    placeholder="Contoh: Ahmad Supardi"
                    required
                  />
                </div>
                
                <div>
                  <Label htmlFor="licenseNumber">Nomor SIM</Label>
                  <Input
                    id="licenseNumber"
                    value={formData.licenseNumber}
                    onChange={(e) => setFormData({ ...formData, licenseNumber: e.target.value })}
                    placeholder="Contoh: SIM001234"
                    required
                  />
                </div>
                
                <div>
                  <Label htmlFor="phone">Nomor Telepon</Label>
                  <Input
                    id="phone"
                    value={formData.phone}
                    onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                    placeholder="Contoh: 081234567890"
                    required
                  />
                </div>
              </div>
              
              <div className="flex justify-end space-x-4">
                <Button type="button" variant="outline" onClick={() => {
                  setShowForm(false);
                  resetForm();
                }}>
                  Batal
                </Button>
                <Button 
                  type="submit" 
                  disabled={createMutation.isPending}
                  className="bg-vehicleflow-primary hover:bg-blue-700"
                >
                  Tambah Driver
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Kelola Driver</h2>
          <p className="text-sm text-gray-500">Kelola daftar driver perusahaan</p>
        </div>
        <Button onClick={() => setShowForm(true)} className="bg-vehicleflow-primary hover:bg-blue-700">
          <Plus className="w-4 h-4 mr-2" />
          Tambah Driver
        </Button>
      </div>

      {isLoading ? (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {[1, 2, 3, 4, 5, 6].map(i => (
            <Card key={i} className="animate-pulse">
              <CardContent className="p-6">
                <div className="space-y-3">
                  <div className="h-4 bg-gray-200 rounded w-1/2"></div>
                  <div className="h-4 bg-gray-200 rounded w-1/3"></div>
                  <div className="h-4 bg-gray-200 rounded w-1/4"></div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {drivers?.map((driver) => (
            <Card key={driver.id} className="stats-card hover:shadow-md transition-shadow">
              <CardContent className="p-6">
                <div className="flex items-start justify-between mb-4">
                  <div className="flex items-center space-x-3">
                    <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                      <User className="w-5 h-5 text-vehicleflow-primary" />
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">{driver.fullName}</h3>
                      <p className="text-sm text-gray-500">{driver.employeeId}</p>
                    </div>
                  </div>
                  <span className={`inline-flex items-center text-xs px-2 py-1 rounded-full ${
                    driver.isAvailable 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  }`}>
                    <div className={`status-indicator ${
                      driver.isAvailable ? 'bg-green-400' : 'bg-red-400'
                    }`}></div>
                    {driver.isAvailable ? 'Tersedia' : 'Tidak Tersedia'}
                  </span>
                </div>
                
                <div className="space-y-3 mb-4">
                  <div className="flex items-center space-x-2 text-sm">
                    <IdCard className="w-4 h-4 text-gray-400" />
                    <span className="text-gray-500">SIM:</span>
                    <span className="text-gray-900">{driver.licenseNumber}</span>
                  </div>
                  <div className="flex items-center space-x-2 text-sm">
                    <Phone className="w-4 h-4 text-gray-400" />
                    <span className="text-gray-500">Phone:</span>
                    <span className="text-gray-900">{driver.phone}</span>
                  </div>
                </div>
                
                <div className="flex justify-end space-x-2">
                  <Button variant="ghost" size="sm">
                    <Edit className="w-4 h-4" />
                  </Button>
                  <Button variant="ghost" size="sm">
                    <Trash2 className="w-4 h-4" />
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
          
          {(!drivers || drivers.length === 0) && (
            <div className="col-span-full">
              <Card className="stats-card">
                <CardContent className="p-12 text-center">
                  <div className="text-gray-500">
                    <User className="w-12 h-12 mx-auto mb-4 opacity-50" />
                    <p className="text-lg font-medium mb-2">Belum ada driver</p>
                    <p className="text-sm mb-4">Mulai dengan menambahkan driver baru</p>
                    <Button onClick={() => setShowForm(true)} className="bg-vehicleflow-primary hover:bg-blue-700">
                      <Plus className="w-4 h-4 mr-2" />
                      Tambah Driver
                    </Button>
                  </div>
                </CardContent>
              </Card>
            </div>
          )}
        </div>
      )}
    </div>
  );
}
