<h2><?php echo $title; ?></h2>
<hr>

<?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

<?php echo form_open('admin/vehicles/create'); ?>

<div class="form-group">
    <label for="name">Vehicle Name</label>
    <input type="text" class="form-control" name="name" value="<?php echo set_value('name'); ?>" required>
</div>

<div class="form-group">
    <label for="type">Type</label>
    <select class="form-control" name="type" required>
        <option value="angkutan orang">Angkutan Orang</option>
        <option value="angkutan barang">Angkutan Barang</option>
    </select>
</div>

<div class="form-group">
    <label for="ownership">Ownership</label>
    <select class="form-control" name="ownership" required>
        <option value="perusahaan">Perusahaan</option>
        <option value="sewaan">Sewaan</option>
    </select>
</div>

<div class="form-group">
    <label for="license_plate">License Plate</label>
    <input type="text" class="form-control" name="license_plate" value="<?php echo set_value('license_plate'); ?>" required>
</div>

<div class="form-group">
    <label for="service_schedule">Next Service Schedule</label>
    <input type="date" class="form-control" name="service_schedule" value="<?php echo set_value('service_schedule'); ?>">
</div>

<button type="submit" class="btn btn-primary">Save Vehicle</button>
<a href="<?php echo site_url('admin/vehicles'); ?>" class="btn btn-secondary">Cancel</a>

<?php echo form_close(); ?>
