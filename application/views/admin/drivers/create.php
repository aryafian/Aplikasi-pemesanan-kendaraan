<h2><?php echo $title; ?></h2>
<hr>

<?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

<?php echo form_open('admin/drivers/create'); ?>

<div class="form-group">
    <label for="name">Driver Name</label>
    <input type="text" class="form-control" name="name" value="<?php echo set_value('name'); ?>" required>
</div>

<div class="form-group">
    <label for="license_number">License Number</label>
    <input type="text" class="form-control" name="license_number" value="<?php echo set_value('license_number'); ?>" required>
</div>

<div class="form-group">
    <label for="phone_number">Phone Number</label>
    <input type="text" class="form-control" name="phone_number" value="<?php echo set_value('phone_number'); ?>" required>
</div>

<button type="submit" class="btn btn-primary">Save Driver</button>
<a href="<?php echo site_url('admin/drivers'); ?>" class="btn btn-secondary">Cancel</a>

<?php echo form_close(); ?>
