<h2><?php echo $title; ?></h2>
<hr>

<?php if($this->session->flashdata('message')):
    echo '<div class="alert alert-success">'.$this->session->flashdata('message').'</div>';
endif; ?>
<?php if($this->session->flashdata('error')):
    echo '<div class="alert alert-danger">'.$this->session->flashdata('error').'</div>';
endif; ?>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Booking ID</th>
                <th>Requester</th>
                <th>Vehicle</th>
                <th>Driver</th>
                <th>Start Date</th>
                <th>Destination</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pending_bookings)): ?>
                <tr>
                    <td colspan="7" class="text-center">No pending approvals for you at the moment.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($pending_bookings as $booking): ?>
                    <tr>
                        <td><?php echo $booking->id; ?></td>
                        <td><?php echo htmlspecialchars($booking->requester_name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($booking->vehicle_name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($booking->driver_name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo date('d M Y H:i', strtotime($booking->start_date)); ?></td>
                        <td><?php echo htmlspecialchars($booking->destination, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="<?php echo site_url('admin/bookings/view/'.$booking->id); ?>" class="btn btn-sm btn-info" title="View Details"><i class="fas fa-eye"></i></a>
                            <button type="button" class="btn btn-sm btn-success action-btn" data-action="approved" data-booking-id="<?php echo $booking->id; ?>" data-toggle="modal" data-target="#approvalModal">Approve</button>
                            <button type="button" class="btn btn-sm btn-danger action-btn" data-action="rejected" data-booking-id="<?php echo $booking->id; ?>" data-toggle="modal" data-target="#approvalModal">Reject</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?php echo form_open('', ['id' => 'approvalForm']); ?>
      <div class="modal-header">
        <h5 class="modal-title" id="approvalModalLabel">Confirm Action</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to <strong id="action-text"></strong> this booking?</p>
        <input type="hidden" name="action" id="action-input">
        <div class="form-group">
            <label for="comments">Comments (optional)</label>
            <textarea name="comments" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" id="confirm-btn">Confirm</button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
