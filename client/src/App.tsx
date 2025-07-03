import { Switch, Route } from "wouter";
import { queryClient } from "./lib/queryClient";
import { QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "@/components/ui/toaster";
import { TooltipProvider } from "@/components/ui/tooltip";
import { useState, useEffect } from "react";
import { apiRequest } from "./lib/queryClient";

import Login from "@/pages/login";
import Dashboard from "@/pages/dashboard";
import Booking from "@/pages/booking";
import Approval from "@/pages/approval";
import Vehicles from "@/pages/vehicles";
import Drivers from "@/pages/drivers";
import Reports from "@/pages/reports";
import NotFound from "@/pages/not-found";

import { Sidebar } from "@/components/layout/sidebar";
import { TopBar } from "@/components/layout/topbar";

export interface User {
  id: number;
  username: string;
  fullName: string;
  role: string;
  department?: string;
  approvalLevel?: string;
}

function Router({ user }: { user: User | null }) {
  if (!user) {
    return (
      <Switch>
        <Route path="/" component={Login} />
        <Route path="/login" component={Login} />
        <Route component={Login} />
      </Switch>
    );
  }

  return (
    <div className="min-h-screen flex bg-background">
      <Sidebar user={user} />
      <main className="flex-1 ml-64">
        <TopBar user={user} />
        <div className="p-6">
          <Switch>
            <Route path="/" component={Dashboard} />
            <Route path="/dashboard" component={Dashboard} />
            <Route path="/booking" component={Booking} />
            <Route path="/approval" component={Approval} />
            <Route path="/vehicles" component={Vehicles} />
            <Route path="/drivers" component={Drivers} />
            <Route path="/reports" component={Reports} />
            <Route component={NotFound} />
          </Switch>
        </div>
      </main>
    </div>
  );
}

function App() {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const checkAuth = async () => {
      try {
        const response = await apiRequest('GET', '/api/auth/me');
        const data = await response.json();
        setUser(data.user);
      } catch (error) {
        setUser(null);
      } finally {
        setLoading(false);
      }
    };

    checkAuth();
  }, []);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-primary"></div>
      </div>
    );
  }

  return (
    <QueryClientProvider client={queryClient}>
      <TooltipProvider>
        <Toaster />
        <Router user={user} />
      </TooltipProvider>
    </QueryClientProvider>
  );
}

export default App;
