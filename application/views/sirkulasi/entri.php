<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h2 class="mb-3">Tambah Peminjaman</h2>

<div class="card">
	<div class="card-header">
		<a href="<?= base_url('/sirkulasi'); ?>" class="btn btn-sm">
			<i class="fa fa-arrow-left"></i>&ensp;Kembali
		</a>
	</div>
	<div class="card-body">

		<form id="Form" class="form-horizontal" data-toggle="validator" action="<?= site_url('/sirkulasi/gentr'); ?>" method="post" enctype="multipart/form-data">

			<div class="form-group">
				<label class="control-label" for="noakta">Nomor Akta</label>
				<input type="text" id="snoakta" name="noakta" class="form-control xhr" placeholder="Ketikan 3 huruf/angka pertama kode akta atau klasifikasi akta" data-xhr="<?= site_url('/sirkulasi/xhr_akta'); ?>" autocomplete="off" required />
			</div>

			<div class="form-group">
				<label class=" control-label" for="username_peminjam">Username Peminjam</label>
				<input type="text" id="username_peminjam" name="username_peminjam" class="form-control xhr" placeholder="Ketikan 3 huruf pertama username yang akan meminjam" data-xhr="<?= site_url('/sirkulasi/xhr_user'); ?>" autocomplete="off" required />
			</div>

			<div class="form-group">
				<label class="control-label" for="keperluan">Alasan keperluan peminjaman</label>
				<textarea id="keperluan" name="keperluan" class="form-control" row="3" required></textarea>
			</div>

			<div class="form-group">
				<label class="control-label" for="tgl_pinjam">Tanggal Peminjaman</label>
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text" style="height: 50px;"><i class="mdi mdi-calendar text-secondary"></i></span>
					</div>
					<input id="tgl_pinjam" name="tgl_pinjam" class="form-control" type="text" value="<?php print $now ?>" required>

				</div>
			</div>

			<div class="form-group">
				<label class="control-label" for="tgl_haruskembali">Tanggal Harus Kembali</label>
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text" style="height: 50px;"><i class="mdi mdi-calendar text-secondary"></i></span>
					</div>
					<input id="tgl_haruskembali" name="tgl_haruskembali" class="form-control" type="text" required>

				</div>
			</div>

			<div class="form-group">
				<button type="submit" class="btn btn-info btn-lg text-white"><i class="fa fa-save"></i> Simpan</button>
			</div>

		</form>
	</div>
</div>