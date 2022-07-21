<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login - <?= $set->site_title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= base_url('/assets/logo.png') ?>" rel="icon" type="image/x-icon" />
    <!-- base:css -->
    <link rel="stylesheet" href="<?= base_url('/assets/template/vendors/fontawesome-free/css/all.min.css') ?>">
    <link type="text/css" rel="stylesheet"
        href="<?= base_url('/assets/template/vendors/mdi/css/materialdesignicons.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/template/vendors/css/vendor.bundle.base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/template/css/style.css') ?>">
    <script src="<?= base_url('/assets/template/vendors/sweetalert2/sweetalert2.all.min.js') ?>"></script>
    <style>
    .form-control {
        font-size: 1em;
    }
    </style>
</head>

<body>
    <div class="container-scroller d-flex">
        <div class="container-fluid page-body-wrapper full-page-wrapper d-flex">

            <div class="content-wrapper d-flex align-items-center auth auth-img-bg px-0"
                style="background: url('<?= site_url('assets/images/') . $set->site_background ?>') no-repeat center center;background-size: cover; ">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <form class="pt-3 needs-validation" id="login" role="form" method="post"
                                action="<?= site_url('/home/gologin'); ?>" novalidate>
                                <h3 class="text-center"><?= $set->site_title ?></h3>
                                <h4 class="font-weight-bold mb-4 text-center"><?= $set->site_nama ?></h4>
                                <p>Masuk untuk memulai sesi Anda.</p>
                                <?php
                if ($this->session->flashdata('errorlogin')) {
                  echo "<div class=\"alert alert-danger\" role=\"alert\"><h5><i class=\"fa fa-exclamation-triangle\"></i> Perhatian!</h5>" . $this->session->flashdata('error') . "</div>";
                }
                ?>
                                <!--<input type="hidden" name="previous" value="<? //= (isset($previous) ? $previous : "") 
                                                                ?>">-->
                                <div class="form-group">
                                    <div class="input-group validate-input">
                                        <input type="text" class="form-control" name="username" id="loginEmail"
                                            placeholder="Username" required="" autofocus>
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="height: 50px;">
                                                <i class="mdi mdi-account-outline mdi-18px text-dark"></i>
                                            </span>
                                        </div>
                                        <div class="invalid-feedback">
                                            Username belum diisi
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group validate-input">
                                        <input type="password" class="form-control" name="password" id="loginPass"
                                            placeholder="Password" required="">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="height: 50px;">
                                                <i class="mdi mdi-lock-outline mdi-18px text-dark"></i>
                                            </span>
                                        </div>
                                        <div class="invalid-feedback">
                                            Password belum diisi
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button class="btn bg-primary font-weight-medium text-white"
                                        type="submit">Login</button>
                                </div>
                            </form>
                            <p class="text-center mt-3 text-secondary">Copyright © <?= date('Y') ?> Fajar Subarkah
                                All rights reserved. Template SPICA Free by Bootstrapdash.com.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="<?= base_url('/assets/template/vendors/js/vendor.bundle.base.js') ?>"></script>
    <script src="<?= base_url('/assets/template/js/jquery.cookie.js') ?>"></script>
    <script src="<?= base_url('/assets/template/js/off-canvas.js') ?>"></script>
    <script src="<?= base_url('/assets/template/js/hoverable-collapse.js') ?>"></script>
    <script src="<?= base_url('/assets/template/js/template.js') ?>"></script>
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
</body>

</html>