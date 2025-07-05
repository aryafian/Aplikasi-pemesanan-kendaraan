            </div>
</div>

</main>

    </div><!-- .main-content -->
</div><!-- .main-wrapper -->

<!-- jQuery, Popper.js, and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });

        // Handle Approval Modal
        $('#approvalModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var action = button.data('action'); // Extract info from data-* attributes
            var bookingId = button.data('booking-id');

            var modal = $(this);
            var form = modal.find('#approvalForm');
            var actionText = (action === 'approved') ? 'approve' : 'reject';
            var btnClass = (action === 'approved') ? 'btn-success' : 'btn-danger';

            // Update the form's action URL
            form.attr('action', '<?php echo site_url('approvals/process/'); ?>' + bookingId);

            // Update modal content
            modal.find('#action-text').text(actionText);
            modal.find('#action-input').val(action);
            modal.find('#confirm-btn').removeClass('btn-success btn-danger').addClass(btnClass).text('Confirm ' + actionText.charAt(0).toUpperCase() + actionText.slice(1));
        });
    });
</script>

</body>
</html>
