import { Car, Calendar, CheckCircle, Plus, List, Users, BarChart3, Settings, User, LogOut } from "lucide-react";
import { Link, useLocation } from "wouter";
import { Button } from "@/components/ui/button";
import { apiRequest } from "@/lib/queryClient";
import { User as UserType } from "@/App";

interface SidebarProps {
  user: UserType;
}

export function Sidebar({ user }: SidebarProps) {
  const [location] = useLocation();

  const handleLogout = async () => {
    try {
      await apiRequest('POST', '/api/auth/logout');
      window.location.reload();
    } catch (error) {
      console.error('Logout failed:', error);
    }
  };

  const navigationItems = [
    {
      name: "Dashboard",
      href: "/dashboard",
      icon: BarChart3,
      roles: ["admin", "requester", "approver"],
    },
    {
      name: "Pemesanan Baru",
      href: "/booking",
      icon: Plus,
      roles: ["admin", "requester"],
    },
    {
      name: "Daftar Pemesanan",
      href: "/booking",
      icon: List,
      roles: ["admin", "requester", "approver"],
    },
    {
      name: "Persetujuan",
      href: "/approval",
      icon: CheckCircle,
      roles: ["admin", "approver"],
      badge: user.role === "approver" ? "3" : undefined,
    },
    {
      name: "Kelola Kendaraan",
      href: "/vehicles",
      icon: Car,
      roles: ["admin"],
    },
    {
      name: "Kelola Driver",
      href: "/drivers",
      icon: Users,
      roles: ["admin"],
    },
    {
      name: "Laporan",
      href: "/reports",
      icon: BarChart3,
      roles: ["admin", "approver"],
    },
    {
      name: "Pengaturan",
      href: "/settings",
      icon: Settings,
      roles: ["admin"],
    },
  ];

  const visibleItems = navigationItems.filter(item => 
    item.roles.includes(user.role)
  );

  return (
    <aside className="w-64 bg-vehicleflow-surface shadow-lg fixed h-full z-10 border-r border-gray-200">
      <div className="p-6 border-b border-gray-200">
        <div className="flex items-center space-x-3">
          <div className="w-10 h-10 bg-vehicleflow-primary rounded-lg flex items-center justify-center">
            <Car className="w-6 h-6 text-white" />
          </div>
          <div>
            <h1 className="text-xl font-bold text-gray-900">VehicleFlow</h1>
            <p className="text-sm text-gray-500">Manajemen Kendaraan</p>
          </div>
        </div>
      </div>
      
      <nav className="mt-6">
        <div className="px-4 space-y-1">
          {visibleItems.map((item) => {
            const isActive = location === item.href || 
              (item.href === "/dashboard" && location === "/");
            
            return (
              <Link key={item.name} href={item.href}>
                <a 
                  className={`sidebar-link ${
                    isActive ? "sidebar-link-active" : "sidebar-link-inactive"
                  }`}
                >
                  <item.icon className="w-5 h-5 mr-3" />
                  {item.name}
                  {item.badge && (
                    <span className="ml-auto bg-vehicleflow-accent text-white text-xs px-2 py-1 rounded-full">
                      {item.badge}
                    </span>
                  )}
                </a>
              </Link>
            );
          })}
        </div>
      </nav>

      <div className="absolute bottom-0 w-full p-4 border-t border-gray-200">
        <div className="flex items-center space-x-3">
          <div className="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
            <User className="w-4 h-4 text-gray-600" />
          </div>
          <div className="flex-1">
            <p className="text-sm font-medium text-gray-900">{user.fullName}</p>
            <p className="text-xs text-gray-500 capitalize">{user.role}</p>
          </div>
          <Button variant="ghost" size="sm" onClick={handleLogout}>
            <LogOut className="w-4 h-4 text-gray-400 hover:text-gray-600" />
          </Button>
        </div>
      </div>
    </aside>
  );
}
