<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="row page-title-header mb-0">
    <div class="col-12">
        <div class="page-header">
            <h4 class="page-title">Profil</h4>
            <div class="quick-link-wrapper d-md-flex flex-md-wrap">
                <div class="quick-links">
                    <?php if (isset($_SESSION['akses_modul']['pengaturan']) && $_SESSION['akses_modul']['pengaturan'] == 'on') : ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">

    <div class="card-body">

        <form class="form-horizontal" role="form" method="post"
            action="<?php echo site_url("/pengaturan/save_profil"); ?>">
            <input type="hidden" name="id" id="ediduser" value="<?= $user->id ?>">
            <div class="form-group">
                <label class="control-label" for="username">Username</label>
                <div class="">
                    <input type="text" class="form-control" id="username" name="username" value="<?= $user->username ?>"
                        readonly />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="password">Password</label>
                <div class="">
                    <input type="password" class="form-control" id="password" name="password" />
                </div>
            </div>

            <div class="form-group">
                <label class="control-label" for="conf_password">Konfirmasi password</label>
                <div class="">
                    <input type="password" class="form-control" id="conf_password" name="conf_password" />
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>