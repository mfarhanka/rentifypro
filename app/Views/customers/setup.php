<?php
/**
 * @var string $title
 * @var string $action
 * @var string|null $email
 * @var string|null $phone
 * @var string $identifier
 */
?>
<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= esc($title) ?> | Rentify Pro<?= $this->endSection() ?>

<?= $this->section('main') ?>
<div class="row no-gutters auth-shell align-items-stretch">
    <div class="col-lg-5 d-none d-lg-flex brand-panel align-items-center justify-content-center p-5">
        <div class="col-10">
            <p class="text-uppercase mb-3">First Access</p>
            <h1 class="display-4 font-weight-bold mb-4">Create your password to activate your customer account.</h1>
            <p class="lead mb-0">After setup, you can sign in using <?= esc($email ? 'your email' : 'your phone number') ?>.</p>
        </div>
    </div>
    <div class="col-lg-7 d-flex align-items-center justify-content-center p-4 p-lg-5">
        <div class="card shadow-lg w-100" style="max-width: 540px;">
            <div class="card-body p-4 p-lg-5">
                <h2 class="h3 mb-4"><?= esc($title) ?></h2>

                <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger"><?= esc(session('error')) ?></div>
                <?php elseif (session('errors') !== null) : ?>
                    <div class="alert alert-danger">
                        <?php foreach ((array) session('errors') as $error) : ?>
                            <div><?= esc($error) ?></div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <div class="alert alert-light border">
                    Sign in identifier after setup: <strong><?= esc($identifier) ?></strong>
                </div>

                <form action="<?= esc($action) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password_confirm">Confirm password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Create password</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>