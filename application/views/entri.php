<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h2 class="mb-3">Entri Dokumen Baru</h2>

<div class="card">
    <div class="card-header">
        <a href="<?= base_url('/home/search'); ?>" class="btn btn-sm">
            <i class="fa fa-arrow-left"></i>&ensp;Kembali
        </a>
    </div>

    <div class="card-body">
        <form id="entriForm" class="form-horizontal" action="<?= site_url('/admin/gentr'); ?>" method="post"
            enctype="multipart/form-data">
            <input type="hidden" name="media" value="1">
            <!-- Form Name -->
            <div class="row">
                <div class="col-md-6">
                    <!-- 1st column -->

                    <div class="form-group">
                        <label class="col-md-6 control-label" for="noakta">Nomor Akta</label>
                        <div class="col-md-12">
                            <input id="noakte" name="noakta" class="form-control" type="text" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-6 control-label" for="nama_dokumen">Nama Dokumen</label>
                        <div class="col-md-12">
                            <input id="nama_dokumen" name="nama_dokumen" class="form-control" type="text" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-6 control-label" for="kode">Jenis Klasifikasi Akte</label>
                        <div class="col-md-12">
                            <select id="kode" name="kode" class="form-select">
                                <option value="">Pilih</option>
                                <?php
								if (isset($kode)) {
									foreach ($kode as $k) {
										echo "<option value='" . $k['id'] . "' >" . $k['nama'] . " - " . $k['kode'] . "</option>";
									}
								}
								?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-6 control-label" for="tanggal">Tanggal Akta</label>
                        <div class="col-md-12">
                            <input id="tanggal" name="tanggal" class="form-control" type="text" placeholder="yyyy-mm-dd"
                                autocomplete="off" required>
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
                                    <input type="radio" id="yes" class="form-check-input" name="status_aktif" value="1">
                                    Ya</label>
                            </div>
                            <div class="form-radio form-radio-flat">
                                <label for="no" class="form-check-label">
                                    <input type="radio" id="no" class="form-check-input" name="status_aktif" value="0">
                                    Tidak</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-6 control-label" for="ket">Keterangan Keaslian</label>
                        <div class="col-md-12">
                            <select class="form-select" name="ket" id="ket">
                                <option value="asli" selected="selected">Asli</option>
                                <option value="copy">Copy</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-6 control-label" for="file">File</label>
                        <div class="col-md-12">
                            <input type="file" name="file" class="dropify" data-height="100" required>
                            <small class="form-text text-muted">Ukuran Maksimal
                                <?= number_format(ceil(max_file_upload_in_bytes() / 1000)); ?> MB</small>
                        </div>
                    </div>

                </div><!-- /2nd column -->
            </div><!-- /.row -->
            <div class="form-group">
                <label class="col-md-6 control-label" for="uraian">Uraian</label>
                <div class="col-md-12">
                    <textarea id="tinyMce" name="uraian" class="form-control" rows="5"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Simpan</button>
                </div>
            </div>
        </form>

    </div><!-- card body -->
</div> <!-- card -->

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