<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<h2 class="mb-3">Detail Arsip</h2>

<div class="card">
    <div class="card-header py-2">
        <a href="<?= base_url(); ?>" class="btn btn-sm">
            <i class="fa fa-arrow-left"></i>&ensp;Kembali
        </a>
        <a class="btn btn-primary btn-sm float-end" href="<?= site_url('/admin/vedit/' . encrypt_url($id)); ?>"><i
                class="fa fa-pencil"></i> Edit</a>
    </div>
    <div class="card-body">
        <!-- Form Name -->
        <div class="row">
            <div class="col-md-8">
                <!-- 1st column -->
                <div class="mb-2 row">
                    <label class="col-md-3 control-label" for="namaakta">Nama Dokumen</label>
                    <label class="col-md-9">: <?= $nama_dokumen; ?></label>
                </div>

                <div class="mb-2 row">
                    <label class="col-md-3 control-label" for="noakta">Nomor Akta</label>
                    <label class="col-md-9">: <?= $noakta; ?></label>
                </div>

                <div class="mb-2 row">
                    <label class="col-md-3 control-label" for="tanggal">Tanggal</label>
                    <label class="col-md-9">: <?= date_indo(($tanggal), 'd F Y');
												//if($f=='sudah') {
												//echo "<br />&nbsp;<div class=\"badge badge-warning text-dark\">Retensi expired: ".date_format(date_create($b),'d-M-Y')."</div>";
												//}else {
												//echo "<br />&nbsp;<div class=\"badge badge-warning text-dark\">Retensi tgl: ".date_format(date_create($b),'d-M-Y')."</div>";
												//}
												?>
                    </label>
                </div>

                <div class="mb-2 row">
                    <label class="col-md-3 control-label" for="pencipta">Pencipta</label>
                    <label class="col-md-9">: <?= $nama_pencipta; ?></label>
                </div>


                <div class="mb-2 row">
                    <label class="col-md-3 control-label" for="kode">Jenis akta</label>
                    <label class="col-md-9">: <?= $nama_kode . " - " . $nama; ?></label>
                </div>

                <div class="mb-2 row">
                    <label class="col-md-3 control-label" for="uraian">Uraian</label>
                    <label class="col-md-9">: <?= $uraian; ?></label>
                </div>

                <div class="mb-2 row">
                    <label class="col-md-3 control-label" for="media">Jenis Media</label>
                    <label class="col-md-9">: <?= $nama_media; ?></label>
                </div>

                <div class="mb-2 row">
                    <label class="col-md-3 control-label" for="ket">Ket. Keaslian</label>
                    <label class="col-md-9">: <?= strtoupper($ket); ?></label>
                </div>

                <div class="mb-2 row">
                    <label class="col-md-3 control-label" for="user">Nama penginput</label>
                    <label class="col-md-9">: <span class="badge badge-primary"><i class="fa fa-user"></i>
                            <?= $username; ?></span></label>
                </div>

            </div><!-- /1st column -->

            <div class="col-md-4">
                <div>
                    <div>QR Code</div>
                    <img src="<?= base_url('files/qrcode/' . $idakta . '.png') ?>" width="200" alt="">
                </div>

            </div><!-- 2nd column -->

        </div><!-- /.row -->

        <hr />

        <div class="row">
            <div class="col-md-12">
                <h4>File Preview</h4>
                <div class="mb-3">
                    <label class="col-md-12">File:
                        <?= ($file == "" ? "" : "<a href='" . base_url('files/' . $file) . "' target='_blank'>" . $file . "</a>"); ?></label>
                </div>

                <iframe id="pdf-js-viewer"
                    src="<?= base_url() ?>vendor/pdfjs/web/viewer.html?file=<?= base_url('files/' . $file); ?>"
                    title="webviewer" width="100%" frameborder="0" scrolling="yes"
                    style="display:block; width:100%; height:100vh;"></iframe>

            </div>
        </div><!-- /.row -->

    </div><!-- card-body -->
</div>