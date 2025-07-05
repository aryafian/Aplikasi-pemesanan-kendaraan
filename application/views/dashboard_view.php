<h2>Welcome to the Dashboard, <?php echo $this->session->userdata('full_name'); ?>!<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="font-weight-bold mb-0">Dashboard</h4>
        <p class="text-muted">Selamat datang kembali, kelola pemesanan kendaraan Anda.</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card d-flex align-items-center">
            <div class="stat-icon bg-blue mr-4"><i class="fas fa-calendar-check"></i></div>
            <div>
                <p>Total Pesanan</p>
                <h3><?php echo $total_bookings; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card d-flex align-items-center">
            <div class="stat-icon bg-orange mr-4"><i class="fas fa-clock"></i></div>
            <div>
                <p>Menunggu</p>
                <h3><?php echo $pending_bookings; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card d-flex align-items-center">
            <div class="stat-icon bg-green mr-4"><i class="fas fa-car-side"></i></div>
            <div>
                <p>Kendaraan Aktif</p>
                <h3><?php echo $active_vehicles; ?> <small class="text-muted">/ <?php echo $total_vehicles; ?></small></h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card d-flex align-items-center">
            <div class="stat-icon bg-purple mr-4"><i class="fas fa-chart-pie"></i></div>
            <div>
                <p>Efisiensi</p>
                <h3>13%</h3>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <div class="col-lg-7">
        <div class="chart-card">
            <h5 class="font-weight-bold mb-3">Pemakaian Kendaraan (7 Hari)</h5>
            <canvas id="vehicleUsageChart"></canvas>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="chart-card">
            <h5 class="font-weight-bold mb-3">Status Kendaraan</h5>
            <canvas id="vehicleStatusChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Bookings and Quick Actions -->
<div class="row">
    <div class="col-lg-8">
        <div class="list-card">
             <h5 class="font-weight-bold mb-3">Pemesanan Terbaru</h5>
             <?php if (!empty($recent_bookings)): ?>
                <?php foreach($recent_bookings as $booking): ?>
                    <div class="list-item">
                        <div class="stat-icon bg-blue mr-3"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 font-weight-bold text-dark"><?php echo html_escape($booking->purpose); ?></h6>
                            <small class="text-muted"><?php echo html_escape($booking->requester_name); ?> &bull; <?php echo date('d M Y', strtotime($booking->start_date)); ?></small>
                        </div>
                        <div class="text-right">
                            <?php 
                                $status_map = [
                                    'pending' => 'status-pending',
                                    'approved' => 'status-approved',
                                    'rejected' => 'status-rejected',
                                    'completed' => 'status-completed'
                                ];
                                $status_class = $status_map[$booking->status] ?? 'status-pending';
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>"><?php echo html_escape(ucfirst($booking->status)); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
             <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada pemesanan terbaru.</h5>
                </div>
             <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="list-card action-card">
            <h5 class="font-weight-bold mb-3">Aksi Cepat</h5>
            <a href="<?php echo base_url('admin/bookings/create'); ?>" class="btn btn-primary text-white"><i class="fas fa-plus"></i> Buat Pemesanan Baru</a>
            <a href="<?php echo base_url('admin/bookings'); ?>" class="btn btn-info text-white"><i class="fas fa-tasks"></i> Kelola Semua Pesanan</a>
            <a href="<?php echo base_url('admin/vehicles'); ?>" class="btn btn-secondary"><i class="fas fa-car"></i> Lihat Daftar Kendaraan</a>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Vehicle Usage Chart
    var ctxLine = document.getElementById('vehicleUsageChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: <?php echo $vehicle_usage_labels; ?>,
            datasets: [{
                label: 'Jumlah Pemakaian',
                data: <?php echo $vehicle_usage_data; ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#007bff',
                pointRadius: 5
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Vehicle Status Chart
    var ctxDoughnut = document.getElementById('vehicleStatusChart').getContext('2d');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: <?php echo $vehicle_status_labels; ?>,
            datasets: [{
                data: <?php echo $vehicle_status_data; ?>,
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderColor: '#fff',
                borderWidth: 4
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
