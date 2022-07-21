<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title><?php if (isset($title)) {
            echo $title . " - ";
          } ?><?= $set->site_title ?></title>
  <!-- base:css -->
  <link rel="stylesheet" href="<?= base_url('/assets/template/vendors/fontawesome-free/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/template/vendors/mdi/css/materialdesignicons.min.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('/assets/template/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/template/css/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/template/vendors/datatables/css/dataTables.bootstrap5.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/template/vendors/sweetalert2/sweetalert2.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/template/vendors/dropify/css/dropify.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/template/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('/assets/css/jquery.auto-complete.css') ?>" />

  <script src="<?= base_url('/assets/template/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('/assets/template/vendors/sweetalert2/sweetalert2.all.min.js') ?>"></script>
  <!-- endinject -->
  <link href="<?= base_url('/assets/logo.png') ?>" rel="icon" />

</head>

<body>
  <div class="container-scroller d-flex">

    <?php if ($status_aktif == "0") { ?>
      <div class="container-fluid page-body-wrapper full-page-wrapper d-flex">
        <div class="content-wrapper d-flex align-items-center text-center error-page bg-info">
          <div class="row flex-grow">
            <div class="col-lg-7 mx-auto text-white">
              <div class="row align-items-center d-flex flex-row">
                <div class="col-lg-6 text-lg-right pr-lg-4">
                  <h1 class="display-1 mb-0">ERR</h1>
                </div>
                <div class="col-lg-6 error-page-divider text-lg-left pl-lg-4">
                  <h2>MAAF!</h2>
                  <h3 class="font-weight-light">Status Dokumen Tidak-Aktif</h3>
                </div>
              </div>
              <div class="row mt-5">
                <div class="col-12 text-center mt-xl-2">
                  <a class="text-white font-weight-medium" href="<?= base_url() ?>">Back to home</a>
                </div>
              </div>
              <div class="row mt-5">
                <div class="col-12 mt-xl-2">
                  <p class="text-white font-weight-medium text-center">Copyright &copy; <?= date('Y') ?> All rights reserved.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
    <?php } else { ?>

      <div class="container-fluid page-body-wrapper full-page-wrapper d-flex">
        <div class="card">
          <div class="card-body">
            <h3 class="text-center"><?= $set->site_title ?></h3>
            <h1 class="text-center">NOTARIS</h1>
            <h1 class="text-center"><?= $set->site_nama ?></h1>
            <hr />
            <!-- Form Name -->
            <div class="row">
              <div class="col-md-9">
                <!-- 1st column -->

                <div class="view-group row">
                  <label class="col-md-3 control-label" for="namaakta">Nama Dokumen</label>
                  <label class="col-md-9">: <?= $nama_dokumen; ?></label>
                </div>

                <div class="view-group row">
                  <label class="col-md-3 control-label" for="noakta">Nomor Akta</label>
                  <label class="col-md-9">: <?= $noakta; ?></label>
                </div>

                <div class="view-group row">
                  <label class="col-md-3 control-label" for="uraian">Uraian</label>
                  <label class="col-md-9">: <?= $uraian; ?></label>
                </div>

                <div class="view-group row">
                  <label class="col-md-3 control-label" for="tanggal">Tanggal</label>
                  <label class="col-md-9">: <?= date_indo($tanggal, 'd F Y');
                                            //if($f=='sudah') {
                                            //echo "<br />&nbsp;<div class=\"badge badge-warning text-dark\">Retensi expired: ".date_format(date_create($b),'d-M-Y')."</div>";
                                            //}else {
                                            //echo "<br />&nbsp;<div class=\"badge badge-warning text-dark\">Retensi tgl: ".date_format(date_create($b),'d-M-Y')."</div>";
                                            //}
                                            ?>
                  </label>
                </div>

                <div class="view-group row">
                  <label class="col-md-3 control-label" for="pencipta">Pencipta</label>
                  <label class="col-md-9">: <?= $nama_pencipta; ?></label>
                </div>


                <div class="view-group row">
                  <label class="col-md-3 control-label" for="kode">Jenis Akta</label>
                  <label class="col-md-9">: <?= $nama_kode . " - " . $nama; ?></label>
                </div>

                <div class="view-group row">
                  <label class="col-md-3 control-label" for="media">Jenis Media</label>
                  <label class="col-md-9">: <?= $nama_media; ?></label>
                </div>

                <div class="view-group row">
                  <label class="col-md-3 control-label" for="ket">Ket. Keaslian</label>
                  <label class="col-md-9">: <?= strtoupper($ket); ?></label>
                </div>

                <!--<div class="view-group row">
	<label class="col-md-3 control-label" for="user">Nama penginput</label>
	<label class="col-md-9">: <span class="badge badge-primary"><i class="fa fa-user"></i> <?= $username; ?></span></label>
</div>-->

              </div><!-- /1st column -->

              <div class="col-md-3">

                <div>
                  <div>QR Code</div>
                  <img src="<?= base_url('files/qrcode/' . $idakta . '.png') ?>" width="150" alt="">
                </div>

              </div><!-- 2nd column -->

            </div><!-- /.row -->

            <hr />
            <p class="">Dokumen ini adalah Benar dan Tercatat dalam database, untuk memastikan bahwa dokumen tersebut benar, pastikan bahwa URL dalam browser anda adalah <?= base_url() ?> dan bentuk fisik dokumen sama seperti gambar di bawah ini</p>
            <hr />

            <div class="row">
              <div class="col-md-12">

                <h4>File Preview <span class="float-right"><i class="fa fa-download"></i> File: <?= ($file == "" ? "" : "<a href='" . base_url('files/' . $file) . "' target='_blank'>" . $file . "</a>"); ?></span>
                </h4>


                <iframe id="pdf-js-viewer" src="<?= base_url() ?>/vendor/pdfjs/web/viewer.html?file=<?= base_url('files/' . $file); ?>" title="webviewer" width="100%" frameborder="0" scrolling="yes" style="display:block; width:100%; height:100vh;">

              </div>
            </div><!-- /.row -->

          <?php } ?>

          </div><!-- card-body -->
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