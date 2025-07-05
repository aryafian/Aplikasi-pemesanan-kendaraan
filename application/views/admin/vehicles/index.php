<div class="list-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold mb-0">Kelola Kendaraan</h4>
            <p class="text-muted">Daftar semua kendaraan yang terdaftar dalam sistem.</p>
        </div>
        <div>
            <a href="<?php echo base_url('admin/vehicles/create'); ?>" class="btn btn-primary"><i class="fas fa-plus mr-2"></i>Tambah Kendaraan</a>
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
                    <th>Nama Kendaraan</th>
                    <th>Tipe</th>
                    <th>No. Polisi</th>
                    <th>Jadwal Servis</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($vehicles)): ?>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <tr>
                            <td><strong>#<?php echo $vehicle->id; ?></strong></td>
                            <td><?php echo html_escape($vehicle->name); ?></td>
                            <td><?php echo html_escape(ucfirst(str_replace('_', ' ', $vehicle->type))); ?></td>
                            <td><span class="badge badge-secondary"><?php echo html_escape($vehicle->license_plate); ?></span></td>
                            <td><?php echo $vehicle->service_schedule ? date('d M Y', strtotime($vehicle->service_schedule)) : 'N/A'; ?></td>
                            <td>
                                <?php if ($vehicle->is_available): ?>
                                    <span class="status-badge status-completed">Tersedia</span>
                                <?php else: ?>
                                    <span class="status-badge status-pending">Dipinjam</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo base_url('admin/vehicles/edit/'.$vehicle->id); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <a href="<?php echo base_url('admin/vehicles/delete/'.$vehicle->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Anda yakin ingin menghapus kendaraan ini?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-car-side fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada data kendaraan.</h5>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
