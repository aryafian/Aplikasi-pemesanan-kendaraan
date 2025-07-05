<h2><?php echo $title; ?></h2>
<hr>

<?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

<?php echo form_open('admin/vehicles/edit/' . $vehicle->id); ?>

<div class="form-group">
    <label for="name">Vehicle Name</label>
    <input type="text" class="form-control" name="name" value="<?php echo set_value('name', $vehicle->name); ?>" required>
</div>

<div class="form-group">
    <label for="type">Type</label>
    <select class="form-control" name="type" required>
        <option value="angkutan orang" <?php echo ($vehicle->type == 'angkutan orang') ? 'selected' : ''; ?>>Angkutan Orang</option>
        <option value="angkutan barang" <?php echo ($vehicle->type == 'angkutan barang') ? 'selected' : ''; ?>>Angkutan Barang</option>
    </select>
</div>

<div class="form-group">
    <label for="ownership">Ownership</label>
    <select class="form-control" name="ownership" required>
        <option value="perusahaan" <?php echo ($vehicle->ownership == 'perusahaan') ? 'selected' : ''; ?>>Perusahaan</option>
        <option value="sewaan" <?php echo ($vehicle->ownership == 'sewaan') ? 'selected' : ''; ?>>Sewaan</option>
    </select>
</div>

<div class="form-group">
    <label for="license_plate">License Plate</label>
    <input type="text" class="form-control" name="license_plate" value="<?php echo set_value('license_plate', $vehicle->license_plate); ?>" required>
</div>

<div class="form-group">
    <label for="service_schedule">Next Service Schedule</label>
    <input type="date" class="form-control" name="service_schedule" value="<?php echo set_value('service_schedule', $vehicle->service_schedule); ?>">
</div>

<div class="form-group">
    <label for="is_available">Status</label>
    <select class="form-control" name="is_available" required>
        <option value="1" <?php echo ($vehicle->is_available == 1) ? 'selected' : ''; ?>>Available</option>
        <option value="0" <?php echo ($vehicle->is_available == 0) ? 'selected' : ''; ?>>In Use</option>
    </select>
</div>

<button type="submit" class="btn btn-primary">Update Vehicle</button>
<a href="<?php echo site_url('admin/vehicles'); ?>" class="btn btn-secondary">Cancel</a>

<?php echo form_close(); ?>
