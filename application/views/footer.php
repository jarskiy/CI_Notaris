<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<footer class="footer mt-3">
    <div class="d-sm-flex justify-content-center justify-content-sm-between py-2">
        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © <?= date('Y'); ?> <a
                href="https://www.github.com/jarskiy/" target="_blank">Fajar Subarkah</a>. All rights reserved.</span>
        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Template SPICA Free by
            Bootstrapdash.com
        </span>
    </div>
</footer>
</div>
<!-- content-wrapper ends -->
</div>
<!-- main-panel ends -->

</div>
<!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->

<!-- plugins:js -->
<script src="<?= base_url('/assets/template/js/off-canvas.js') ?>"></script>
<script src="<?= base_url('/assets/template/js/hoverable-collapse.js') ?>"></script>
<script src="<?= base_url('/assets/template/js/jquery.cookie.js') ?>"></script>
<script src="<?= base_url('/assets/template/js/template.js') ?>"></script>
<script src="<?= base_url('/assets/template/js/misc.js') ?>"></script>
<script src="<?= base_url('/assets/template/vendors/datatables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('/assets/template/vendors/datatables/js/dataTables.bootstrap5.min.js') ?>"></script>
<script src="<?= base_url('/assets/template/vendors/dropify/js/dropify.min.js') ?>"></script>
<script src="<?= base_url('/assets/template/js/dropify.js') ?>"></script>
<script src="<?= base_url('/assets/template/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') ?>"></script>
<script src="<?= base_url('/assets/template/vendors/jquery-validation/jquery.validate.min.js') ?>"></script>
<script src="<?= base_url('/assets/template/vendors/tinymce/tinymce.min.js') ?>"></script>
<script src="<?= base_url('/assets/js/jquery.form.min.js') ?>"></script>
<script src="<?= base_url('/assets/js/jquery.auto-complete.min.js') ?>"></script>
<script src="<?= base_url('/assets/js/custom.js') ?>"></script>
<script src="<?= base_url('/assets/js/validation.js') ?>"></script>
<script src="<?= base_url('/assets/template/js/editor.js') ?>"></script>
<!-- End custom js for this page -->
<script>
const Toast = Swal.mixin({
    toast: true,
    position: 'top',
    showConfirmButton: true,
    timer: 5000
});
<?php if ($message = $this->session->flashdata('success')) { ?>
Toast.fire({
    icon: 'success',
    title: '<?= $message ?>.'
})
<?php } ?>
<?php if ($message = $this->session->flashdata('error')) { ?>
Toast.fire({
    icon: 'error',
    title: '<?= $message ?>.'
})
<?php } ?>
</script>
<script>
function displayDate() {
    moment.locale('id-ID');
    var date = moment().format('dddd,Do MMMM YYYY');
    $('#tanggalan').html(date);
    setTimeout(displayDate, 1000);
}

function displayTime() {
    moment.locale('id-ID');
    var time = moment().format('HH:mm:ss');
    $('#jam').html(time);
    setTimeout(displayTime, 1000);
}

$(document).ready(function() {
    displayDate();
    displayTime();
});
</script>
</body>

</html>