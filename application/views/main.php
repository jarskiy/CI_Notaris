<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row mb-2">
    <div class="col-md-8">
        <h1>Dashboard</h1>
    </div>
    <div class="col-md-4">
        <div class="float-end">
            <a href="#" role="button" data-bs-toggle="modal" data-bs-target="#advanced-search" aria-expanded="false"
                aria-controls="advanced-search" class="btn btn-outline-dark btn-sm me-2"><i class="fa fa-search"></i>
                Pencarian Lanjut</a>
            <a class="btn btn-success btn-sm text-white"
                href="<?= site_url('/home/dl') . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '') ?>"><i
                    class="fa fa-file-excel"></i> Ekspor ke Excel (XLS)</a>
        </div>
    </div>
</div>

<?php if ($_SESSION['tipe'] == 'admin') { ?>

<div class="row">

    <div class="col-lg-4 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card bg-gradient-primary d-flex align-items-center">
            <div class="card-body py-5">
                <div
                    class="d-flex flex-row align-items-center flex-wrap justify-content-md-center justify-content-xl-start py-1">
                    <i class="mdi mdi-folder-multiple icon-lg text-white"></i>
                    <div class="ms-3 ml-md-0 ml-xl-3">
                        <h3 class="font-weight-bold"><a class="text-white text-decoration-none"
                                href="<?= base_url('/home/search') ?>" alt="Jumlah Dokumen">Dokumen Akta</a></h3>
                        <h1 class="font-weight-medium mb-0 text-white"><?= $countakta; ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card bg-gradient-warning d-flex align-items-center">
            <div class="card-body py-5">
                <div
                    class="d-flex flex-row align-items-center flex-wrap justify-content-md-center justify-content-xl-start py-1">
                    <i class="mdi mdi-swap-horizontal-bold icon-lg text-white"></i>
                    <div class="ms-3 ml-md-0 ml-xl-3">
                        <h3 class="font-weight-bold"><a class="text-white text-decoration-none"
                                href="<?= base_url('/sirkulasi') ?>" alt="Sirkulasi" class="text-white">Sirkulasi
                                Peminjaman</a>
                        </h3>
                        <div class="fluid-container">
                            <h1 class="font-weight-medium mb-0 text-white"><?= $countSirkulasi; ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card bg-gradient-success d-flex align-items-center">
            <div class="card-body py-5">
                <div
                    class="d-flex flex-row align-items-center flex-wrap justify-content-md-center justify-content-xl-start py-1">
                    <i class="mdi mdi-account-multiple icon-lg text-white"></i>
                    <div class="ms-3 ml-md-0 ml-xl-3">
                        <h3 class="font-weight-bold"><a class="text-white text-decoration-none"
                                href="<?= base_url('/admin/vuser') ?>" alt="Pengguna" class="text-dark">Pengguna</a>
                            </h5>
                            <div class="fluid-container">
                                <h1 class="font-weight-medium mb-0 text-white"><?= $countUser; ?></h1>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php } ?>

<div class="card">
    <div class="card-header">
        <a class="btn btn-primary" href="<?= site_url('/admin/entr') ?>"><i
                class="menu-icon mdi mdi-file-document-box-plus"></i> Tambah</a>
    </div>
    <!-- Title -->

    <div class="card-body">
        <!--<?php
            //if ($this->session->flashdata('zz')) {
            //echo '<div class="alert alert-success" role="alert">' . $this->session->flashdata('zz') . '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
            //<span aria-hidden="true">&times;</span>
            //</button></div>';
            //}
            ?>-->
        <!-- /.row -->
        <!-- Page Features -->
        <div class="table-responsive">
            <table id="order-listing2" class="table table-bordered table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>No Akta</th>
                        <th width="400">Nama Dokumen</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Link</th>
                        <th class="text-center">File</th>
                        <th class="text-center">QR</th>
                        <th class="width-sm"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)) { ?>
                    <?php foreach ($data as $a) {
                            echo "<tr>";
                            echo "<td>" . $a['noakta'] . "</td>";
                            echo "<td>" . $a['nama_dokumen'] . "</td>";
                            echo "<td align=\"center\">" . $a['tanggal'] . "</td>";
                            if ($a['status_aktif'] == '1') {
                                echo "<td align=\"center\"><div class=\"badge badge-success\"><i class=\"ti-thumb-up\"></i> Aktif</div></td>";
                            } else {
                                echo "<td align=\"center\"><div class=\"badge badge-danger\"><i class=\"ti-thumb-down\"></i> Tdk Aktif</div></td>";
                            }

                            echo "<td align=\"center\"><a href=" . base_url('dokumen/detail/' . $a['idakta'] . '') . " target=\"_blank\">Link</a></td>";
                            if ($a['file'] == "") {
                                echo "<td></td>";
                            } else {
                                echo "<td align=\"center\"><a href='" . base_url('files/' . $a['file']) . "' target='_blank'><i class='fa fa-file fa-lg' aria-hidden='true'></i></a></td>";
                            }
                            echo "<td align=\"center\"><a href=\"#showQR-" . $a['id'] . "\" data-bs-toggle=\"modal\" data-bs-target=\"#showQR-" . $a['id'] . "\"><i class='fa fa-qrcode fa-lg' aria-hidden='true'></i></a>
							
							<div class=\"modal fade\" id=\"showQR-" . $a['id'] . "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"showQRLabel\" aria-hidden=\"true\">
                      <div class=\"modal-dialog\" role=\"document\">
                        <div class=\"modal-content\">
                          <div class=\"modal-header\">
                            <h5 class=\"modal-title\" id=\"showQRLabel\">QR Code Untuk Publik</h5>
                            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>
                          </div>
                          <div class=\"modal-body\">
                            <img src=" . base_url('files/qrcode/' . $a['idakta'] . '.png') . " alt=\"\" style=\"width: 40%;min-width: 40%;height:60%;border-radius: 0;\">
							<div>
							<p>" . $a['nama_dokumen'] . "<br/>
							No. " . $a['noakta'] . "</p>
							<p><a href=" . base_url('dokumen/detail/' . $a['idakta'] . '') . " target=\"_blank\">Buka Link</a></p>
							</div>
                          </div>
                          <div class=\"modal-footer\">
                            <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Tutup</button>
                          </div>
                        </div>
                      </div>
                    </div>
							</td>";
                            echo "<td align=\"center\"><a class=\"me-4\" href='" . site_url('home/view/' . encrypt_url($a['id'])) . "' ><i class=\"fa fa-eye fa-lg text-primary\"></i></a>";
                            if (isset($_SESSION['akses_modul']['entridata']) && $_SESSION['akses_modul']['entridata'] == 'on') {
                                echo "<a class=\"me-4\" href='" . site_url('/admin/vedit/' . encrypt_url($a['id'])) . "'><i class='fa fa-pencil fa-lg text-dark' aria-hidden='true'></i></a>";
                            }
                            if (isset($_SESSION['akses_modul']['entridata']) && $_SESSION['akses_modul']['entridata'] == 'on') {
                                echo "<a class='deldata' id='" . $a['id'] . "' href='#' data-bs-toggle=\"modal\" data-bs-target=\"#deldata\"><i class=\"fa fa-trash fa-lg text-danger\"></i></a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    <?php } else { ?>
                    <tr>
                        <td class="text-muted" align="center" colspan="8">Data Kosong</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div><!-- table responsive -->

        <div class="mt-2">
            <?php
            echo $pages;
            ?>
        </div>
        <!-- /.row -->
    </div>
</div>

<!-- modal -->
<div class="modal fade" id="advanced-search" tabindex="-1" role="dialog" aria-labelledby="advanced-search"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pencarian Lanjut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo site_url('/home/search'); ?>" method="get" id="srcmain">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">No Akta</label>
                                <div class="col-sm-9">
                                    <input id="noakta" name="noakta" class="form-control input-md" type="text"
                                        value="<?php echo $src['noakta'] ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nama Dokumen</label>
                                <div class="col-sm-9">
                                    <input id="nama_dokumen" name="nama_dokumen" class="form-control input-md"
                                        type="text" value="<?php echo $src['nama_dokumen'] ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Tanggal</label>
                                <div class="col-sm-9">
                                    <input id="tanggal" name="tanggal" class="form-control input-md" type="text"
                                        value="<?php echo $src['tanggal'] ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Uraian</label>
                                <div class="col-sm-9">
                                    <input id="uraian" name="uraian" class="form-control input-md" type="text"
                                        value="<?php echo $src['uraian'] ?>">
                                </div>
                            </div>

                        </div>
                        <!--col-->


                        <div class="col-md-6">

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Jenis Akta</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="kode" id="zkode">
                                        <option value="all">Semua</option>
                                        <?php
                                        if (isset($kode)) {
                                            foreach ($kode as $p) {
                                                echo "<option value=\"" . $p['kode'] . "\" " . ($src['kode'] == $p['kode'] ? "selected=selected" : "") . ">" . $p['kode'] . " - " . $p['nama'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Ket</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="ket" id="ket">
                                        <option value="all">Semua</option>
                                        <option value="asli"
                                            <?php echo ($src['ket'] == 'asli' ? 'selected=selected' : ''); ?>>Asli
                                        </option>
                                        <option value="copy"
                                            <?php echo ($src['ket'] == 'copy' ? 'selected=selected' : ''); ?>>Copy
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Pencipta</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="penc" id="penc">
                                        <option value="all">Semua</option>
                                        <?php
                                        if (isset($penc)) {
                                            foreach ($penc as $p) {
                                                echo "<option value=\"" . $p['id'] . "\" " . ($src['penc'] == $p['id'] ? "selected=selected" : "") . ">" . " - " . $p['nama_pencipta'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Media</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="med" id="med">
                                        <option value="all">Semua</option>
                                        <?php
                                        if (isset($med)) {
                                            foreach ($med as $p) {
                                                echo "<option value=\"" . $p['id'] . "\" " . ($src['med'] == $p['id'] ? "selected=selected" : "") . ">" . " - " . $p['nama_media'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <!--col-->

                    </div>
                    <!--row-->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit" id="singlebutton" name="singlebutton"><i
                                        class="fa fa-search"></i> Cari</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <!-- ./modal body -->
            <!--<div class="modal-footer">
                           <button type="button" class="btn btn-success trigger-submit"><i class="fa fa-search"></i> Cari</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
                          </div>-->
        </div>
    </div>
</div>
<!-- ./modal -->

<div class="modal fade" id="deldata">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <form id="fdeldata" class="form-horizontal" role="form" method="post"
                    action="<?php echo site_url("/admin/del1"); ?>">
                    <h4 class="fw-normal">Yakin ingin Hapus Data ini?</h4>
                    <input type="hidden" name="id" id="deliddata" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="deldatago">Hapus</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->