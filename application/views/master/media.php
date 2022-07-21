<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<h2 class="mb-3">Master Media</h2>

<div class="card">
  <div class="card-header">
    <?php if (isset($_SESSION['akses_modul']['media']) && $_SESSION['akses_modul']['media'] == 'on') : ?>
      <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#addmed"><i class="fa fa-plus"></i> Tambah Baru</a>
    <?php endif; ?>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-4 ms-auto">
        <form>
          <div class="input-group">
            <input name="katakunci" class="form-control form-control-sm" type="search" placeholder="Kata kunci" aria-label="Search">
            <div class="input-group-append">
              <button class="btn bg-secondary text-white" type="submit">
                <i class="fa fa-search"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
      <div class="col-md-12" id="divtabelmed">
        <div class="table-responsive">
          <table class="table table-bordered" name="vmed" id="vmed">
            <thead>
              <th class="width-sm">No</th>
              <th>Nama Media</th>
              <th class="width-sm"></th>
              <th class="width-sm"></th>
            </thead>
            <?php
            if (isset($med)) {
              $no = 1;
              foreach ($med as $u) {
                echo "<tr>";
                echo "<td>" . $no . "</td>";
                echo "<td>" . $u['nama_media'] . "</td>";
                echo "<td align=\"center\"><a data-toggle=\"modal\" data-target=\"#editmed\" class='edmed text-primary' href='#' id='" . $u['id'] . "' title=\"Edit\"><i class=\"fa fa-edit fa-lg\"></i> </a></td>";
                echo "<td align=\"center\"><a data-toggle=\"modal\" data-target=\"#delmed\" class='delmed text-danger' href='#' id='" . $u['id'] . "' title=\"Delete\"><i class=\"fa fa-trash fa-lg\"></i> </a></td>";
                echo "</tr>";
                $no++;
              }
            }
            ?>
          </table>
        </div>
        <div class="mt-2">
          <?= $pages; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addmed">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header py-3">
        <h5 class="modal-title">Tambah Media Arsip</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="faddmed" class="form-horizontal" role="form" method="post" action="<?= site_url("/admin/addmed"); ?>">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="nama">Nama</label>
            <div class="col-sm-10">
              <input type="text" class="form-control form-control-lg" id="nama" name="nama" />
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" id="addmedgo">Simpan</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="editmed">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header py-3">
        <h5 class="modal-title">Edit Media Arsip</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="fedmed" class="form-horizontal" role="form" method="post" action="<?= site_url("/admin/edmed"); ?>">
          <input type="hidden" name="id" id="edidmed" value="">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="nama">Nama</label>
            <div class="col-sm-10">
              <input type="text" class="form-control form-control-lg" id="enama" name="nama" />
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" id="editmedgo">Simpan</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="delmed">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header py-3">
        <h5 class="modal-title">Delete Media Arsip</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="fdelmed" class="form-horizontal" role="form" method="post" action="<?= site_url("/admin/delmed"); ?>">
          <h4 class="modal-title">Yakin ingin Hapus data ini?</h4>
          <input type="hidden" name="id" id="delidmed" value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" id="delmedgo">Hapus</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->