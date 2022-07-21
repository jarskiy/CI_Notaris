<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<h2 class="mb-3">Edit Data</h2>

<div class="card">
    <div class="card-header">
        <a href="<?= base_url('/home/search'); ?>" class="btn btn-sm">
            <i class="fa fa-arrow-left"></i>&ensp;Kembali
        </a>
    </div>
    <div class="card-body">
        <form id="Form" class="form-horizontal" data-toggle="validator" action="<?= site_url('/admin/edit'); ?>"
            method="post" enctype="multipart/form-data">
            <fieldset>
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="media" value="<?= $media ?>">
                <!-- Form Name -->
                <div class="row">
                    <div class="col-md-6">
                        <!-- 1st column -->
                        <div class="form-group">
                            <label class="col-md-6 control-label" for="noakta">Nomor Akta</label>
                            <div class="col-md-12">
                                <input id="noakta" name="noakta" class="form-control" type="text" value="<?= $noakta ?>"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-6 control-label" for="nama_dokumen">Nama Dokumen</label>
                            <div class="col-md-12">
                                <input id="nama_dokumen" name="nama_dokumen" class="form-control" type="text"
                                    value="<?= $nama_dokumen ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-6 control-label" for="kode">Jenis Klasifikasi Akta</label>
                            <div class="col-md-12">
                                <select id="kode" name="kode" class="form-select" required>
                                    <?php
									if (isset($kode2)) {
										foreach ($kode2 as $k) {
											echo "<option value='" . $k['id'] . "'" . ($kode == $k['id'] ? "selected=selected" : "") . " >" . $k['nama'] . " - " . $k['kode'] . "</option>";
										}
									}
									?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-6 control-label" for="tanggal">Tanggal Akta</label>
                            <div class="col-md-12">
                                <input id="tanggal" name="tanggal" class="form-control" type="text"
                                    value="<?= date('Y-m-d', strtotime($tanggal)); ?>" required>
                            </div>
                        </div>

                    </div><!-- /1st column -->

                    <div class="col-md-6">
                        <!-- 2nd column -->
                        <div class="form-group">
                            <label class="col-md-6 control-label" for="status_aktif">Status Aktif</label>
                            <div class="col-md-12">
                                <div class="form-radio form-radio-flat">
                                    <label for="yes" class="form-check-label">
                                        <input type="radio" id="yes" class="form-check-input" name="status_aktif"
                                            value="1" <?= ($status_aktif == "1" ? "checked=checked" : "") ?>>
                                        Ya</label>
                                </div>
                                <div class="form-radio form-radio-flat">
                                    <label for="no" class="form-check-label">
                                        <input type="radio" id="no" class="form-check-input" name="status_aktif"
                                            value="0" <?= ($status_aktif == "0" ? "checked=checked" : "") ?>>
                                        Tidak</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-6 control-label" for="ket">Keterangan Keaslian</label>
                            <div class="col-md-12">
                                <select class="form-select" name="ket" id="ket" required>
                                    <option value="asli" <?= ($ket == 'asli' ? "selected=selected" : "") ?>>Asli
                                    </option>
                                    <option value="copy" <?= ($ket == 'copy' ? "selected=selected" : "") ?>>Copy
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-6 control-label" for="file">File</label>
                            <div class="col-md-8">
                                <?php
								if ($file != "") {
									echo "<br/><span style='text-overflow:ellipsis;overflow:hidden;' id='linkfile'><a href='" . base_url('files/' . $file) . "'>$file</a></span>";
									echo "<span class='ms-3'><a href='#' data-bs-toggle=\"modal\" data-bs-target=\"#delfile\"><span class='fa fa-remove fa-lg' style='color:red' aria-hidden='true'></span></a></span>";
									echo "<div id='uplodfile' style='display:none;'>";
								} else {
									echo "<div id='uplodfile'>";
								}
								echo "<input type='file' id='file' name='file' class='dropify' data-height='100' required><p class='help-block'>Ukuran Maksimal " . number_format(ceil(max_file_upload_in_bytes() / 1000)) . "MB</p>";
								echo "</div>";
								?>
                            </div>
                        </div>

                    </div><!-- /2nd column -->
                </div><!-- /.row -->

                <div class="form-group">
                    <label class="col-md-6 control-label" for="uraian">Uraian</label>
                    <div class="col-md-12">
                        <textarea id="tinyMce" name="uraian" class="form-control" rows="3"
                            required><?= $uraian ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <button class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </div>

            </fieldset>
        </form>
    </div>
</div><!-- card -->

<div class="modal fade" id="delfile">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h5 class="modal-title">Hapus File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="fdelfile" class="form-horizontal" role="form" method="post"
                    action="<?= site_url("/admin/delfile"); ?>">
                    <h4 class="modal-title">Yakin ingin Hapus File ini?</h4>
                    <input type="hidden" name="id" id="delidfile" value="<?= $id ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="delfilego">Hapus</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
function return_bytes($val)
{
	$val = trim($val);
	$last = strtolower($val[strlen($val) - 1]);
	$val = (int)trim($val);
	switch ($last) {
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}
	return $val;
}

function max_file_upload_in_bytes()
{
	//select maximum upload size
	$max_upload = return_bytes(ini_get('upload_max_filesize'));
	//select post limit
	$max_post = return_bytes(ini_get('post_max_size'));
	//select memory limit
	$memory_limit = return_bytes(ini_get('memory_limit'));
	// return the smallest of them, this defines the real limit
	return min($max_upload, $max_post, $memory_limit);
}