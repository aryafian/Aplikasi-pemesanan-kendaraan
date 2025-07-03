import { useQuery } from "@tanstack/react-query";
import { Line } from "react-chartjs-2";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
} from "chart.js";

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

interface UsageData {
  date: string;
  count: number;
}

export function UsageChart() {
  const { data: usageData, isLoading } = useQuery<UsageData[]>({
    queryKey: ['/api/dashboard/usage-data?days=7'],
  });

  if (isLoading) {
    return (
      <div className="w-full h-64 flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-vehicleflow-primary"></div>
      </div>
    );
  }

  const labels = usageData?.map(item => {
    const date = new Date(item.date);
    return date.toLocaleDateString('id-ID', { weekday: 'short' });
  }) || ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

  const data = usageData?.map(item => item.count) || [12, 19, 8, 15, 25, 22, 18];

  const chartData = {
    labels,
    datasets: [
      {
        label: 'Pemesanan',
        data,
        borderColor: 'hsl(207, 90%, 54%)',
        backgroundColor: 'hsla(207, 90%, 54%, 0.1)',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: 'hsl(207, 90%, 54%)',
        pointBorderColor: 'hsl(207, 90%, 54%)',
        pointRadius: 4,
        pointHoverRadius: 6,
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
        borderColor: 'hsl(207, 90%, 54%)',
        borderWidth: 1,
      },
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: {
          color: 'rgba(0, 0, 0, 0.1)',
        },
        ticks: {
          color: 'hsl(25, 5.3%, 44.7%)',
        },
      },
      x: {
        grid: {
          display: false,
        },
        ticks: {
          color: 'hsl(25, 5.3%, 44.7%)',
        },
      },
    },
    elements: {
      line: {
        borderWidth: 2,
      },
    },
  };

  return (
    <div className="w-full h-48">
      <Line data={chartData} options={options} />
    </div>
  );
}
