import { useState } from "react";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useMutation } from "@tanstack/react-query";
import { apiRequest } from "@/lib/queryClient";
import { useToast } from "@/hooks/use-toast";

interface BookingFormProps {
  onSuccess: () => void;
}

export function BookingForm({ onSuccess }: BookingFormProps) {
  const { toast } = useToast();
  
  const [formData, setFormData] = useState({
    purpose: '',
    destination: '',
    departureDate: '',
    returnDate: '',
    departureTime: '',
    returnTime: '',
    passengers: 1,
    notes: '',
  });

  const createMutation = useMutation({
    mutationFn: (data: any) => apiRequest('POST', '/api/bookings', data),
    onSuccess: () => {
      toast({ title: "Pemesanan berhasil dibuat" });
      onSuccess();
    },
    onError: (error: any) => {
      toast({ 
        title: "Error", 
        description: error.message || "Gagal membuat pemesanan", 
        variant: "destructive" 
      });
    },
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    // Convert dates to proper format
    const bookingData = {
      ...formData,
      departureDate: new Date(formData.departureDate).toISOString(),
      returnDate: new Date(formData.returnDate).toISOString(),
    };
    
    createMutation.mutate(bookingData);
  };

  const handleInputChange = (field: string, value: any) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  };

  return (
    <Card className="stats-card">
      <CardContent className="p-6">
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <Label htmlFor="purpose">Tujuan Perjalanan *</Label>
              <Input
                id="purpose"
                value={formData.purpose}
                onChange={(e) => handleInputChange('purpose', e.target.value)}
                placeholder="Contoh: Kunjungan client, meeting, dll"
                required
              />
            </div>
            
            <div>
              <Label htmlFor="destination">Destinasi *</Label>
              <Input
                id="destination"
                value={formData.destination}
                onChange={(e) => handleInputChange('destination', e.target.value)}
                placeholder="Alamat lengkap tujuan"
                required
              />
            </div>
            
            <div>
              <Label htmlFor="departureDate">Tanggal Keberangkatan *</Label>
              <Input
                id="departureDate"
                type="date"
                value={formData.departureDate}
                onChange={(e) => handleInputChange('departureDate', e.target.value)}
                min={new Date().toISOString().split('T')[0]}
                required
              />
            </div>
            
            <div>
              <Label htmlFor="returnDate">Tanggal Kembali *</Label>
              <Input
                id="returnDate"
                type="date"
                value={formData.returnDate}
                onChange={(e) => handleInputChange('returnDate', e.target.value)}
                min={formData.departureDate || new Date().toISOString().split('T')[0]}
                required
              />
            </div>
            
            <div>
              <Label htmlFor="departureTime">Waktu Keberangkatan *</Label>
              <Input
                id="departureTime"
                type="time"
                value={formData.departureTime}
                onChange={(e) => handleInputChange('departureTime', e.target.value)}
                required
              />
            </div>
            
            <div>
              <Label htmlFor="returnTime">Estimasi Waktu Kembali *</Label>
              <Input
                id="returnTime"
                type="time"
                value={formData.returnTime}
                onChange={(e) => handleInputChange('returnTime', e.target.value)}
                required
              />
            </div>
            
            <div>
              <Label htmlFor="passengers">Jumlah Penumpang *</Label>
              <Select 
                value={formData.passengers.toString()} 
                onValueChange={(value) => handleInputChange('passengers', parseInt(value))}
              >
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="1">1 orang</SelectItem>
                  <SelectItem value="2">2 orang</SelectItem>
                  <SelectItem value="3">3 orang</SelectItem>
                  <SelectItem value="4">4 orang</SelectItem>
                  <SelectItem value="5">5 orang</SelectItem>
                  <SelectItem value="6">6 orang</SelectItem>
                  <SelectItem value="7">7 orang</SelectItem>
                  <SelectItem value="8">8+ orang</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
          
          <div>
            <Label htmlFor="notes">Catatan Tambahan</Label>
            <Textarea
              id="notes"
              rows={3}
              value={formData.notes}
              onChange={(e) => handleInputChange('notes', e.target.value)}
              placeholder="Informasi tambahan yang diperlukan"
            />
          </div>
          
          <div className="flex justify-end space-x-4">
            <Button type="button" variant="outline">
              Batalkan
            </Button>
            <Button 
              type="submit" 
              disabled={createMutation.isPending}
              className="bg-vehicleflow-primary hover:bg-blue-700"
            >
              {createMutation.isPending ? "Memproses..." : "Ajukan Pemesanan"}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  );
}
