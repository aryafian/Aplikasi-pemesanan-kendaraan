import { useState } from "react";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import { useMutation } from "@tanstack/react-query";
import { Check, X, Eye, Calendar, MapPin, User } from "lucide-react";
import { apiRequest } from "@/lib/queryClient";
import { useToast } from "@/hooks/use-toast";

interface Approval {
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

interface ApprovalCardProps {
  approval: Approval;
  onApprovalChange: () => void;
}

export function ApprovalCard({ approval, onApprovalChange }: ApprovalCardProps) {
  const [showCommentForm, setShowCommentForm] = useState(false);
  const [comments, setComments] = useState("");
  const [actionType, setActionType] = useState<'approved' | 'rejected' | null>(null);
  const { toast } = useToast();

  const approvalMutation = useMutation({
    mutationFn: (data: { bookingId: number; status: string; comments?: string }) => 
      apiRequest('POST', '/api/approvals', data),
    onSuccess: () => {
      toast({ 
        title: actionType === 'approved' ? "Pemesanan disetujui" : "Pemesanan ditolak" 
      });
      setShowCommentForm(false);
      setComments("");
      setActionType(null);
      onApprovalChange();
    },
    onError: (error: any) => {
      toast({ 
        title: "Error", 
        description: error.message || "Gagal memproses persetujuan", 
        variant: "destructive" 
      });
    },
  });

  const handleApproval = (status: 'approved' | 'rejected') => {
    setActionType(status);
    if (status === 'rejected') {
      setShowCommentForm(true);
    } else {
      approvalMutation.mutate({
        bookingId: approval.id,
        status,
        comments: comments || undefined,
      });
    }
  };

  const handleRejectWithComment = () => {
    if (!comments.trim()) {
      toast({ 
        title: "Error", 
        description: "Komentar diperlukan untuk penolakan", 
        variant: "destructive" 
      });
      return;
    }

    approvalMutation.mutate({
      bookingId: approval.id,
      status: 'rejected',
      comments,
    });
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
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

  return (
    <Card className="stats-card border-l-4 border-l-vehicleflow-accent">
      <CardContent className="p-6">
        <div className="flex items-start justify-between mb-4">
          <div>
            <h4 className="text-lg font-semibold text-gray-900">{approval.purpose}</h4>
            <p className="text-sm text-gray-500">ID: <span className="font-medium">{approval.bookingNumber}</span></p>
          </div>
          <span className="status-badge-pending">
            Level 1 Approval
          </span>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div className="flex items-start space-x-3">
            <User className="w-5 h-5 text-gray-400 mt-0.5" />
            <div>
              <p className="text-sm font-medium text-gray-700">Pemohon</p>
              <p className="text-sm text-gray-900">{approval.requester.fullName}</p>
              {approval.requester.department && (
                <p className="text-xs text-gray-500">{approval.requester.department}</p>
              )}
            </div>
          </div>
          
          <div className="flex items-start space-x-3">
            <Calendar className="w-5 h-5 text-gray-400 mt-0.5" />
            <div>
              <p className="text-sm font-medium text-gray-700">Tanggal & Waktu</p>
              <p className="text-sm text-gray-900">{formatDate(approval.departureDate)}</p>
              <p className="text-xs text-gray-500">{approval.departureTime} - {approval.returnTime}</p>
            </div>
          </div>
          
          <div className="flex items-start space-x-3">
            <MapPin className="w-5 h-5 text-gray-400 mt-0.5" />
            <div>
              <p className="text-sm font-medium text-gray-700">Destinasi</p>
              <p className="text-sm text-gray-900">{approval.destination}</p>
            </div>
          </div>
        </div>

        {(approval.vehicle || approval.driver) && (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            {approval.vehicle && (
              <div>
                <p className="text-sm font-medium text-gray-700">Kendaraan Ditugaskan</p>
                <p className="text-sm text-gray-900">
                  {approval.vehicle.plateNumber} - {approval.vehicle.brand} {approval.vehicle.model}
                </p>
              </div>
            )}
            {approval.driver && (
              <div>
                <p className="text-sm font-medium text-gray-700">Driver Ditugaskan</p>
                <p className="text-sm text-gray-900">{approval.driver.fullName}</p>
              </div>
            )}
          </div>
        )}

        {showCommentForm && actionType === 'rejected' && (
          <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <Label htmlFor="comments" className="text-sm font-medium text-red-800">
              Alasan Penolakan *
            </Label>
            <Textarea
              id="comments"
              rows={3}
              value={comments}
              onChange={(e) => setComments(e.target.value)}
              placeholder="Jelaskan alasan penolakan..."
              className="mt-2"
              required
            />
            <div className="flex justify-end space-x-2 mt-3">
              <Button 
                variant="outline" 
                size="sm"
                onClick={() => {
                  setShowCommentForm(false);
                  setComments("");
                  setActionType(null);
                }}
              >
                Batal
              </Button>
              <Button 
                size="sm"
                onClick={handleRejectWithComment}
                disabled={approvalMutation.isPending}
                className="bg-red-600 hover:bg-red-700"
              >
                {approvalMutation.isPending ? "Memproses..." : "Konfirmasi Tolak"}
              </Button>
            </div>
          </div>
        )}
        
        <div className="flex items-center justify-between pt-4 border-t border-gray-200">
          <div className="text-xs text-gray-500">
            Dibuat: {formatDateTime(approval.createdAt)}
          </div>
          <div className="flex items-center space-x-2">
            <Button 
              size="sm"
              onClick={() => handleApproval('approved')}
              disabled={approvalMutation.isPending}
              className="bg-vehicleflow-success hover:bg-green-600"
            >
              <Check className="w-4 h-4 mr-1" />
              Setujui
            </Button>
            <Button 
              variant="destructive"
              size="sm"
              onClick={() => handleApproval('rejected')}
              disabled={approvalMutation.isPending}
            >
              <X className="w-4 h-4 mr-1" />
              Tolak
            </Button>
            <Button variant="ghost" size="sm">
              <Eye className="w-4 h-4 mr-1" />
              Detail
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
