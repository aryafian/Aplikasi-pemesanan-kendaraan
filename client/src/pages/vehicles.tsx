import { useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useQuery, useMutation } from "@tanstack/react-query";
import { Plus, Car, Edit, Trash2 } from "lucide-react";
import { queryClient, apiRequest } from "@/lib/queryClient";
import { useToast } from "@/hooks/use-toast";

interface Vehicle {
  id: number;
  plateNumber: string;
  brand: string;
  model: string;
  year: number;
  capacity: number;
  fuelType: string;
  status: string;
  lastMaintenance?: string;
  nextMaintenance?: string;
  createdAt: string;
}

export default function Vehicles() {
  const [showForm, setShowForm] = useState(false);
  const [editingVehicle, setEditingVehicle] = useState<Vehicle | null>(null);
  const { toast } = useToast();

  const [formData, setFormData] = useState({
    plateNumber: '',
    brand: '',
    model: '',
    year: new Date().getFullYear(),
    capacity: 4,
    fuelType: 'Petrol',
    status: 'available',
  });

  const { data: vehicles, isLoading } = useQuery<Vehicle[]>({
    queryKey: ['/api/vehicles'],
  });

  const createMutation = useMutation({
    mutationFn: (data: any) => apiRequest('POST', '/api/vehicles', data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/vehicles'] });
      setShowForm(false);
      resetForm();
      toast({ title: "Kendaraan berhasil dibuat" });
    },
    onError: (error: any) => {
      toast({ title: "Error", description: error.message, variant: "destructive" });
    },
  });

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: any }) => 
      apiRequest('PUT', `/api/vehicles/${id}`, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/vehicles'] });
      setEditingVehicle(null);
      setShowForm(false);
      resetForm();
      toast({ title: "Kendaraan berhasil diupdate" });
    },
    onError: (error: any) => {
      toast({ title: "Error", description: error.message, variant: "destructive" });
    },
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => apiRequest('DELETE', `/api/vehicles/${id}`),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/vehicles'] });
      toast({ title: "Kendaraan berhasil dihapus" });
    },
    onError: (error: any) => {
      toast({ title: "Error", description: error.message, variant: "destructive" });
    },
  });

  const resetForm = () => {
    setFormData({
      plateNumber: '',
      brand: '',
      model: '',
      year: new Date().getFullYear(),
      capacity: 4,
      fuelType: 'Petrol',
      status: 'available',
    });
    setEditingVehicle(null);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (editingVehicle) {
      updateMutation.mutate({ id: editingVehicle.id, data: formData });
    } else {
      createMutation.mutate(formData);
    }
  };

  const handleEdit = (vehicle: Vehicle) => {
    setFormData({
      plateNumber: vehicle.plateNumber,
      brand: vehicle.brand,
      model: vehicle.model,
      year: vehicle.year,
      capacity: vehicle.capacity,
      fuelType: vehicle.fuelType,
      status: vehicle.status,
    });
    setEditingVehicle(vehicle);
    setShowForm(true);
  };

  const handleDelete = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus kendaraan ini?')) {
      deleteMutation.mutate(id);
    }
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'available':
        return (
          <span className="status-badge-approved">
            <div className="status-indicator bg-green-400"></div>
            Tersedia
          </span>
        );
      case 'in_use':
        return (
          <span className="inline-flex items-center text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
            <div className="status-indicator bg-blue-400"></div>
            Digunakan
          </span>
        );
      case 'maintenance':
        return (
          <span className="status-badge-rejected">
            <div className="status-indicator bg-red-400"></div>
            Maintenance
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

  if (showForm) {
    return (
      <div>
        <div className="flex items-center justify-between mb-6">
          <div>
            <h2 className="text-2xl font-bold text-gray-900">
              {editingVehicle ? 'Edit Kendaraan' : 'Tambah Kendaraan Baru'}
            </h2>
            <p className="text-sm text-gray-500">
              {editingVehicle ? 'Update informasi kendaraan' : 'Tambahkan kendaraan baru ke sistem'}
            </p>
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
                  <Label htmlFor="plateNumber">Nomor Polisi</Label>
                  <Input
                    id="plateNumber"
                    value={formData.plateNumber}
                    onChange={(e) => setFormData({ ...formData, plateNumber: e.target.value })}
                    placeholder="Contoh: B1234CD"
                    required
                  />
                </div>
                
                <div>
                  <Label htmlFor="brand">Merek</Label>
                  <Input
                    id="brand"
                    value={formData.brand}
                    onChange={(e) => setFormData({ ...formData, brand: e.target.value })}
                    placeholder="Contoh: Toyota"
                    required
                  />
                </div>
                
                <div>
                  <Label htmlFor="model">Model</Label>
                  <Input
                    id="model"
                    value={formData.model}
                    onChange={(e) => setFormData({ ...formData, model: e.target.value })}
                    placeholder="Contoh: Avanza"
                    required
                  />
                </div>
                
                <div>
                  <Label htmlFor="year">Tahun</Label>
                  <Input
                    id="year"
                    type="number"
                    value={formData.year}
                    onChange={(e) => setFormData({ ...formData, year: parseInt(e.target.value) })}
                    min="1990"
                    max={new Date().getFullYear() + 1}
                    required
                  />
                </div>
                
                <div>
                  <Label htmlFor="capacity">Kapasitas (orang)</Label>
                  <Input
                    id="capacity"
                    type="number"
                    value={formData.capacity}
                    onChange={(e) => setFormData({ ...formData, capacity: parseInt(e.target.value) })}
                    min="1"
                    max="50"
                    required
                  />
                </div>
                
                <div>
                  <Label htmlFor="fuelType">Jenis Bahan Bakar</Label>
                  <Select value={formData.fuelType} onValueChange={(value) => setFormData({ ...formData, fuelType: value })}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="Petrol">Bensin</SelectItem>
                      <SelectItem value="Diesel">Solar</SelectItem>
                      <SelectItem value="Electric">Listrik</SelectItem>
                      <SelectItem value="Hybrid">Hybrid</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                
                <div>
                  <Label htmlFor="status">Status</Label>
                  <Select value={formData.status} onValueChange={(value) => setFormData({ ...formData, status: value })}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="available">Tersedia</SelectItem>
                      <SelectItem value="in_use">Digunakan</SelectItem>
                      <SelectItem value="maintenance">Maintenance</SelectItem>
                    </SelectContent>
                  </Select>
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
                  disabled={createMutation.isPending || updateMutation.isPending}
                  className="bg-vehicleflow-primary hover:bg-blue-700"
                >
                  {editingVehicle ? 'Update Kendaraan' : 'Tambah Kendaraan'}
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
          <h2 className="text-2xl font-bold text-gray-900">Kelola Kendaraan</h2>
          <p className="text-sm text-gray-500">Kelola daftar kendaraan perusahaan</p>
        </div>
        <Button onClick={() => setShowForm(true)} className="bg-vehicleflow-primary hover:bg-blue-700">
          <Plus className="w-4 h-4 mr-2" />
          Tambah Kendaraan
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
          {vehicles?.map((vehicle) => (
            <Card key={vehicle.id} className="stats-card hover:shadow-md transition-shadow">
              <CardContent className="p-6">
                <div className="flex items-start justify-between mb-4">
                  <div className="flex items-center space-x-3">
                    <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                      <Car className="w-5 h-5 text-vehicleflow-primary" />
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">{vehicle.plateNumber}</h3>
                      <p className="text-sm text-gray-500">{vehicle.brand} {vehicle.model}</p>
                    </div>
                  </div>
                  {getStatusBadge(vehicle.status)}
                </div>
                
                <div className="space-y-2 mb-4">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-500">Tahun:</span>
                    <span className="text-gray-900">{vehicle.year}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-500">Kapasitas:</span>
                    <span className="text-gray-900">{vehicle.capacity} orang</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-500">Bahan Bakar:</span>
                    <span className="text-gray-900">{vehicle.fuelType}</span>
                  </div>
                </div>
                
                <div className="flex justify-end space-x-2">
                  <Button 
                    variant="ghost" 
                    size="sm"
                    onClick={() => handleEdit(vehicle)}
                  >
                    <Edit className="w-4 h-4" />
                  </Button>
                  <Button 
                    variant="ghost" 
                    size="sm"
                    onClick={() => handleDelete(vehicle.id)}
                    disabled={deleteMutation.isPending}
                  >
                    <Trash2 className="w-4 h-4" />
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
          
          {(!vehicles || vehicles.length === 0) && (
            <div className="col-span-full">
              <Card className="stats-card">
                <CardContent className="p-12 text-center">
                  <div className="text-gray-500">
                    <Car className="w-12 h-12 mx-auto mb-4 opacity-50" />
                    <p className="text-lg font-medium mb-2">Belum ada kendaraan</p>
                    <p className="text-sm mb-4">Mulai dengan menambahkan kendaraan baru</p>
                    <Button onClick={() => setShowForm(true)} className="bg-vehicleflow-primary hover:bg-blue-700">
                      <Plus className="w-4 h-4 mr-2" />
                      Tambah Kendaraan
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
