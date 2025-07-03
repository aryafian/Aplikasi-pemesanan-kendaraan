import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { useQuery, useMutation } from "@tanstack/react-query";
import { ApprovalCard } from "@/components/approval/approval-card";
import { CheckCircle, Clock } from "lucide-react";
import { queryClient } from "@/lib/queryClient";

interface PendingApproval {
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

export default function Approval() {
  const { data: pendingApprovals, isLoading } = useQuery<PendingApproval[]>({
    queryKey: ['/api/approvals/pending'],
  });

  if (isLoading) {
    return (
      <div>
        <div className="mb-6">
          <h2 className="text-2xl font-bold text-gray-900">Persetujuan Pemesanan</h2>
          <p className="text-sm text-gray-500">Kelola persetujuan pemesanan kendaraan</p>
        </div>
        
        <div className="grid gap-4">
          {[1, 2, 3].map(i => (
            <Card key={i} className="animate-pulse">
              <CardContent className="p-6">
                <div className="space-y-3">
                  <div className="h-4 bg-gray-200 rounded w-1/4"></div>
                  <div className="h-4 bg-gray-200 rounded w-1/2"></div>
                  <div className="h-4 bg-gray-200 rounded w-1/3"></div>
                  <div className="flex space-x-2 mt-4">
                    <div className="h-8 bg-gray-200 rounded w-20"></div>
                    <div className="h-8 bg-gray-200 rounded w-20"></div>
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    );
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h2 className="text-2xl font-bold text-gray-900">Persetujuan Pemesanan</h2>
          <p className="text-sm text-gray-500">Kelola persetujuan pemesanan kendaraan</p>
        </div>
        <div className="flex items-center space-x-2 text-sm text-gray-600">
          <Clock className="w-4 h-4" />
          <span>{pendingApprovals?.length || 0} menunggu persetujuan</span>
        </div>
      </div>

      {pendingApprovals && pendingApprovals.length > 0 ? (
        <div className="space-y-4">
          {pendingApprovals.map((approval) => (
            <ApprovalCard 
              key={approval.id} 
              approval={approval}
              onApprovalChange={() => {
                queryClient.invalidateQueries({ queryKey: ['/api/approvals/pending'] });
                queryClient.invalidateQueries({ queryKey: ['/api/dashboard/stats'] });
              }}
            />
          ))}
        </div>
      ) : (
        <Card className="stats-card">
          <CardContent className="p-12 text-center">
            <div className="text-gray-500">
              <CheckCircle className="w-12 h-12 mx-auto mb-4 opacity-50" />
              <p className="text-lg font-medium mb-2">Tidak ada persetujuan pending</p>
              <p className="text-sm">Semua pemesanan sudah diproses</p>
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
