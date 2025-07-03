import { Search, Bell } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { useLocation } from "wouter";
import { User as UserType } from "@/App";

interface TopBarProps {
  user: UserType;
}

export function TopBar({ user }: TopBarProps) {
  const [location] = useLocation();

  const getPageTitle = () => {
    switch (location) {
      case "/":
      case "/dashboard":
        return "Dashboard";
      case "/booking":
        return "Pemesanan Kendaraan";
      case "/approval":
        return "Persetujuan";
      case "/vehicles":
        return "Kelola Kendaraan";
      case "/drivers":
        return "Kelola Driver";
      case "/reports":
        return "Laporan";
      case "/settings":
        return "Pengaturan";
      default:
        return "Dashboard";
    }
  };

  const getPageDescription = () => {
    switch (location) {
      case "/":
      case "/dashboard":
        return "Selamat datang kembali, kelola pemesanan kendaraan Anda";
      case "/booking":
        return "Buat dan kelola pemesanan kendaraan";
      case "/approval":
        return "Kelola persetujuan pemesanan kendaraan";
      case "/vehicles":
        return "Kelola daftar kendaraan perusahaan";
      case "/drivers":
        return "Kelola daftar driver perusahaan";
      case "/reports":
        return "Generate dan export laporan pemesanan";
      case "/settings":
        return "Pengaturan sistem dan aplikasi";
      default:
        return "Selamat datang kembali";
    }
  };

  return (
    <header className="bg-vehicleflow-surface shadow-sm border-b border-gray-200 px-6 py-4">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">{getPageTitle()}</h2>
          <p className="text-sm text-gray-500">{getPageDescription()}</p>
        </div>
        <div className="flex items-center space-x-4">
          <div className="relative">
            <Input 
              type="search" 
              placeholder="Cari pemesanan..." 
              className="pl-10 pr-4 py-2 w-64"
            />
            <Search className="w-4 h-4 absolute left-3 top-3 text-gray-400" />
          </div>
          <Button variant="ghost" size="sm" className="relative">
            <Bell className="w-5 h-5 text-gray-400" />
            <span className="absolute -top-1 -right-1 bg-vehicleflow-error text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
              2
            </span>
          </Button>
        </div>
      </div>
    </header>
  );
}
