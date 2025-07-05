<h2><?php echo $title; ?></h2>
<hr>

<div class="list-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold mb-0">Kelola Pemesanan</h4>
            <p class="text-muted">Daftar semua pemesanan kendaraan dalam sistem.</p>
        </div>
        <div>
            <a href="<?php echo base_url('admin/bookings/export_csv'); ?>" class="btn btn-success"><i class="fas fa-file-excel mr-2"></i>Export CSV</a>
            <a href="<?php echo base_url('admin/bookings/create'); ?>" class="btn btn-primary"><i class="fas fa-plus mr-2"></i>Buat Pesanan</a>
        </div>
    </div>

    <?php if ($this->session->flashdata('message')): ?>
        <div class="alert alert-success">
            <?php echo $this->session->flashdata('message'); ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Pemohon</th>
                    <th>Kendaraan</th>
                    <th>Tujuan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><strong>#<?php echo $booking->id; ?></strong></td>
                            <td><?php echo html_escape($booking->requester_name); ?></td>
                            <td><?php echo html_escape($booking->vehicle_name); ?></td>
                            <td><?php echo html_escape($booking->purpose); ?></td>
                            <td><?php echo date('d M Y', strtotime($booking->start_date)); ?></td>
                            <td>
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
                            </td>
                            <td class="text-center">
                                <a href="<?php echo base_url('admin/bookings/edit/'.$booking->id); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <a href="<?php echo base_url('admin/bookings/delete/'.$booking->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Anda yakin ingin menghapus pesanan ini?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada data pemesanan.</h5>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
