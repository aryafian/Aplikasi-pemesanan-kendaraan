<h2><?php echo $title; ?></h2>
<hr>

<?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

<?php echo form_open('admin/bookings/create'); ?>

<div class="form-group">
    <label for="vehicle_id">Vehicle</label>
    <select class="form-control" name="vehicle_id" required>
        <option value="">Select Vehicle</option>
        <?php foreach ($vehicles as $vehicle): ?>
            <option value="<?php echo $vehicle->id; ?>" <?php echo set_select('vehicle_id', $vehicle->id); ?>
                <?php echo $vehicle->is_available ? '' : 'disabled'; ?>>
                <?php echo htmlspecialchars($vehicle->name, ENT_QUOTES, 'UTF-8'); ?> (<?php echo $vehicle->is_available ? 'Available' : 'In Use'; ?>)
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="form-group">
    <label for="driver_id">Driver</label>
    <select class="form-control" name="driver_id" required>
        <option value="">Select Driver</option>
        <?php foreach ($drivers as $driver): ?>
            <option value="<?php echo $driver->id; ?>" <?php echo set_select('driver_id', $driver->id); ?>>
                <?php echo htmlspecialchars($driver->name, ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="form-group">
    <label for="start_date">Start Date and Time</label>
    <input type="datetime-local" class="form-control" name="start_date" value="<?php echo set_value('start_date'); ?>" required>
</div>

<div class="form-group">
    <label for="end_date">End Date and Time</label>
    <input type="datetime-local" class="form-control" name="end_date" value="<?php echo set_value('end_date'); ?>" required>
</div>

<div class="form-group">
    <label for="destination">Destination</label>
    <input type="text" class="form-control" name="destination" value="<?php echo set_value('destination'); ?>" required>
</div>

<div class="form-group">
    <label for="purpose">Purpose</label>
    <textarea class="form-control" name="purpose" rows="3" required><?php echo set_value('purpose'); ?></textarea>
</div>

<button type="submit" class="btn btn-primary">Create Booking</button>
<a href="<?php echo site_url('admin/bookings'); ?>" class="btn btn-secondary">Cancel</a>

<?php echo form_close(); ?>
