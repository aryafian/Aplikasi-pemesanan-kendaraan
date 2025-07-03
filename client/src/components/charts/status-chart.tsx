import { useQuery } from "@tanstack/react-query";
import { Doughnut } from "react-chartjs-2";
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
} from "chart.js";

ChartJS.register(ArcElement, Tooltip, Legend);

interface StatusData {
  status: string;
  count: number;
}

export function StatusChart() {
  const { data: statusData, isLoading } = useQuery<StatusData[]>({
    queryKey: ['/api/dashboard/vehicle-status'],
  });

  if (isLoading) {
    return (
      <div className="w-full h-48 flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-vehicleflow-primary"></div>
      </div>
    );
  }

  const getStatusLabel = (status: string) => {
    switch (status) {
      case 'available':
        return 'Tersedia';
      case 'in_use':
        return 'Digunakan';
      case 'maintenance':
        return 'Maintenance';
      default:
        return status;
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'available':
        return 'hsl(122, 41%, 49%)'; // Success green
      case 'in_use':
        return 'hsl(207, 90%, 54%)'; // Primary blue
      case 'maintenance':
        return 'hsl(0, 87%, 59%)'; // Error red
      default:
        return 'hsl(0, 0%, 50%)';
    }
  };

  const labels = statusData?.map(item => getStatusLabel(item.status)) || ['Tersedia', 'Digunakan', 'Maintenance'];
  const data = statusData?.map(item => item.count) || [8, 4, 3];
  const colors = statusData?.map(item => getStatusColor(item.status)) || [
    'hsl(122, 41%, 49%)',
    'hsl(207, 90%, 54%)',
    'hsl(0, 87%, 59%)'
  ];

  const chartData = {
    labels,
    datasets: [
      {
        data,
        backgroundColor: colors,
        borderWidth: 0,
        hoverBackgroundColor: colors.map(color => color.replace(')', ', 0.8)')),
      },
    ],
  };

  const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false,
      },
      tooltip: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        titleColor: 'white',
        bodyColor: 'white',
        borderWidth: 0,
        callbacks: {
          label: function(context: any) {
            const label = context.label || '';
            const value = context.parsed || 0;
            const total = context.dataset.data.reduce((a: number, b: number) => a + b, 0);
            const percentage = Math.round((value / total) * 100);
            return `${label}: ${value} (${percentage}%)`;
          },
        },
      },
    },
    cutout: '60%',
  };

  return (
    <div className="w-full h-48 flex items-center justify-center">
      <div className="w-40 h-40">
        <Doughnut data={chartData} options={options} />
      </div>
    </div>
  );
}
