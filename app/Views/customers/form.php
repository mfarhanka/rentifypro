<?php
/**
 * @var string $title
 * @var string $action
 */
?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php $customersIndexUrl = site_url('customers'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4 p-lg-5">
                <h1 class="h3 mb-4"><?= esc($title) ?></h1>
                <form action="<?= esc($action) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email')) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= esc(old('phone')) ?>" placeholder="e.g. 08123456789">
                        </div>
                    </div>
                    <p class="text-muted small">Provide either email or phone. The customer will create their own password from a setup link after the account is created.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= esc($customersIndexUrl) ?>" class="btn btn-outline-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Save customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>