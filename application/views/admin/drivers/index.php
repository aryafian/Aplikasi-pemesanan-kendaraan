<div class="list-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold mb-0">Kelola Driver</h4>
            <p class="text-muted">Daftar semua driver yang terdaftar dalam sistem.</p>
        </div>
        <div>
            <a href="<?php echo base_url('admin/drivers/create'); ?>" class="btn btn-primary"><i class="fas fa-plus mr-2"></i>Tambah Driver</a>
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
                    <th>Nama</th>
                    <th>No. Lisensi</th>
                    <th>No. Telepon</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($drivers)): ?>
                    <?php foreach ($drivers as $driver): ?>
                        <tr>
                            <td><strong>#<?php echo $driver->id; ?></strong></td>
                            <td><?php echo html_escape($driver->name); ?></td>
                            <td><?php echo html_escape($driver->license_number); ?></td>
                            <td><?php echo html_escape($driver->phone_number); ?></td>
                            <td>
                                <?php if ($driver->is_available): ?>
                                    <span class="status-badge status-completed">Tersedia</span>
                                <?php else: ?>
                                    <span class="status-badge status-pending">Bertugas</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo base_url('admin/drivers/edit/'.$driver->id); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <a href="<?php echo base_url('admin/drivers/delete/'.$driver->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Anda yakin ingin menghapus driver ini?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-id-card fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada data driver.</h5>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
