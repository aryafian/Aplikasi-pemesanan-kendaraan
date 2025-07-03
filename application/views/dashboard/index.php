<!-- Admin Tools Section -->
<?php if ($user['role'] === 'admin'): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">Admin Tools</h5>
                        <p class="text-muted mb-0">Generate sample data untuk testing sistem</p>
                    </div>
                    <div>
                        <a href="<?php echo base_url('dashboard/seed_data'); ?>" 
                           class="btn btn-primary"
                           onclick="return confirm('Generate sample data? Data existing akan di-reset.')">
                            <i class="bi bi-database-fill"></i>
                            Generate Data Sample
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Pemesanan</h6>
                        <h3 class="mb-0"><?php echo $stats['total_bookings']; ?></h3>
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i> 12% dari bulan lalu
                        </small>
                    </div>
                    <div class="card-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Menunggu Persetujuan</h6>
                        <h3 class="mb-0"><?php echo $stats['pending_approval']; ?></h3>
                        <small class="text-warning">
                            <i class="bi bi-clock"></i> Butuh perhatian
                        </small>
                    </div>
                    <div class="card-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Kendaraan Aktif</h6>
                        <h3 class="mb-0"><?php echo $stats['active_vehicles']; ?></h3>
                        <small class="text-info">
                            <i class="bi bi-truck"></i> Siap operasi
                        </small>
                    </div>
                    <div class="card-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-truck"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Efisiensi</h6>
                        <h3 class="mb-0"><?php echo $stats['efficiency']; ?>%</h3>
                        <small class="text-success">
                            <i class="bi bi-graph-up"></i> Performa baik
                        </small>
                    </div>
                    <div class="card-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-8 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Tren Penggunaan Kendaraan</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="period" id="period7" autocomplete="off" checked>
                    <label class="btn btn-outline-secondary" for="period7">7 Hari</label>
                    
                    <input type="radio" class="btn-check" name="period" id="period30" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="period30">30 Hari</label>
                </div>
            </div>
            <div class="card-body">
                <canvas id="usageChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Status Kendaraan</h5>
            </div>
            <div class="card-body">
                <canvas id="vehicleStatusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Pemesanan Terbaru</h5>
                <a href="<?php echo base_url('bookings'); ?>" class="btn btn-sm btn-outline-primary">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_bookings)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No. Booking</th>
                                <th>Pemohon</th>
                                <th>Tujuan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Kendaraan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_bookings as $booking): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo base_url('bookings/view/' . $booking['id']); ?>" class="text-decoration-none">
                                        <?php echo $booking['booking_number']; ?>
                                    </a>
                                </td>
                                <td><?php echo $booking['requester_name']; ?></td>
                                <td><?php echo $booking['purpose']; ?></td>
                                <td><?php echo date('d M Y', strtotime($booking['departure_date'])); ?></td>
                                <td>
                                    <?php 
                                    $status_class = '';
                                    $status_text = '';
                                    switch ($booking['status']) {
                                        case 'pending':
                                            $status_class = 'bg-warning';
                                            $status_text = 'Menunggu';
                                            break;
                                        case 'approved_level1':
                                            $status_class = 'bg-info';
                                            $status_text = 'Approval L1';
                                            break;
                                        case 'approved_level2':
                                            $status_class = 'bg-primary';
                                            $status_text = 'Approval L2';
                                            break;
                                        case 'approved':
                                            $status_class = 'bg-success';
                                            $status_text = 'Disetujui';
                                            break;
                                        case 'rejected':
                                            $status_class = 'bg-danger';
                                            $status_text = 'Ditolak';
                                            break;
                                        case 'completed':
                                            $status_class = 'bg-secondary';
                                            $status_text = 'Selesai';
                                            break;
                                        default:
                                            $status_class = 'bg-light text-dark';
                                            $status_text = ucfirst($booking['status']);
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($booking['plate_number'])): ?>
                                        <span class="text-muted"><?php echo $booking['plate_number']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Belum ada pemesanan terbaru</p>
                    <a href="<?php echo base_url('bookings/create'); ?>" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Buat Pemesanan
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Usage Chart
    const usageCtx = document.getElementById('usageChart').getContext('2d');
    const usageData = <?php echo json_encode($usage_data); ?>;
    
    const usageChart = new Chart(usageCtx, {
        type: 'line',
        data: {
            labels: usageData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Jumlah Pemesanan',
                data: usageData.map(item => item.count),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Vehicle Status Chart
    const statusCtx = document.getElementById('vehicleStatusChart').getContext('2d');
    const statusData = <?php echo json_encode($vehicle_status_data); ?>;
    
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(item => item.status),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: [
                    '#28a745', // Available - Green
                    '#ffc107', // In Use - Yellow  
                    '#dc3545'  // Maintenance - Red
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Period selector for usage chart
    document.querySelectorAll('input[name="period"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const days = this.id === 'period7' ? 7 : 30;
            // You can implement AJAX call here to fetch data for different periods
            console.log('Period changed to:', days, 'days');
        });
    });
});
</script>