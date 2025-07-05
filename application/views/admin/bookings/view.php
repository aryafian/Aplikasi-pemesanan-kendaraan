<h2><?php echo $title; ?> (ID: <?php echo $booking->id; ?>)</h2>
<hr>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Booking Information
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                    <tr>
                        <th width="30%">Requester</th>
                        <td><?php echo htmlspecialchars($booking->requester_name, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <th>Vehicle</th>
                        <td><?php echo htmlspecialchars($booking->vehicle_name, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <th>Driver</th>
                        <td><?php echo htmlspecialchars($booking->driver_name, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <th>Start Date</th>
                        <td><?php echo date('d M Y, H:i', strtotime($booking->start_date)); ?></td>
                    </tr>
                    <tr>
                        <th>End Date</th>
                        <td><?php echo date('d M Y, H:i', strtotime($booking->end_date)); ?></td>
                    </tr>
                    <tr>
                        <th>Destination</th>
                        <td><?php echo htmlspecialchars($booking->destination, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <tr>
                        <th>Purpose</th>
                        <td><?php echo nl2br(htmlspecialchars($booking->purpose, ENT_QUOTES, 'UTF-8')); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                             <?php 
                                $status_class = 'badge-secondary';
                                if ($booking->status == 'approved') {
                                    $status_class = 'badge-success';
                                } elseif ($booking->status == 'rejected') {
                                    $status_class = 'badge-danger';
                                } elseif ($booking->status == 'pending') {
                                    $status_class = 'badge-warning';
                                }
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($booking->status); ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Approval History
            </div>
            <div class="card-body">
                <?php if (empty($approvals)): ?>
                    <p>No approval history yet.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($approvals as $approval): ?>
                            <li class="list-group-item">
                                <strong>Level <?php echo $approval->approval_level; ?>:</strong> <?php echo htmlspecialchars($approval->approver_name, ENT_QUOTES, 'UTF-8'); ?><br>
                                <span class="text-muted"><?php echo date('d M Y H:i', strtotime($approval->approved_at)); ?></span><br>
                                Status: <span class="badge badge-<?php echo $approval->status == 'approved' ? 'success' : 'danger'; ?>"><?php echo ucfirst($approval->status); ?></span>
                                <?php if (!empty($approval->comments)): ?>
                                    <p class="mt-2 mb-0"><em>"<?php echo htmlspecialchars($approval->comments, ENT_QUOTES, 'UTF-8'); ?>"</em></p>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<hr>
<a href="<?php echo site_url('admin/bookings'); ?>" class="btn btn-secondary">Back to Bookings List</a>
