<?php
/**
 * @var string $title
 * @var string $action
 * @var \CodeIgniter\Shield\Entities\User|null $staffMember
 */
?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php $staffIndexUrl = site_url('staff'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4 p-lg-5">
                <h1 class="h3 mb-4"><?= esc($title) ?></h1>
                <form action="<?= esc($action) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= esc(old('username', $staffMember?->username ?? '')) ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email', $staffMember?->email ?? '')) ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password">Password<?= $staffMember !== null ? ' (leave blank to keep current)' : '' ?></label>
                            <input type="password" class="form-control" id="password" name="password" <?= $staffMember === null ? 'required' : '' ?>>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password_confirm">Confirm password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" <?= $staffMember === null ? 'required' : '' ?>>
                        </div>
                    </div>
                    <?php if ($staffMember !== null) : ?>
                        <div class="alert alert-light border">
                            Current status: <strong><?= $staffMember->active ? 'Active' : 'Suspended' ?></strong>
                        </div>
                    <?php endif ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= esc($staffIndexUrl) ?>" class="btn btn-outline-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Save staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>