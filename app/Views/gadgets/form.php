<?php
/**
 * @var string $title
 * @var string $action
 * @var array<string, mixed>|null $gadget
 */
?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php $gadgetsIndexUrl = site_url('gadgets'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4 p-lg-5">
                <h1 class="h3 mb-4"><?= esc($title) ?></h1>
                <form action="<?= esc($action) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="code">Code</label>
                            <input type="text" class="form-control" id="code" name="code" value="<?= esc(old('code', $gadget['code'] ?? '')) ?>" required>
                        </div>
                        <div class="form-group col-md-8">
                            <label for="name">Gadget name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= esc(old('name', $gadget['name'] ?? '')) ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="brand">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" value="<?= esc(old('brand', $gadget['brand'] ?? '')) ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="daily_rate">Daily rate</label>
                            <input type="number" step="0.01" class="form-control" id="daily_rate" name="daily_rate" value="<?= esc(old('daily_rate', $gadget['daily_rate'] ?? '')) ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="stock">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="<?= esc(old('stock', $gadget['stock'] ?? 1)) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= esc(old('description', $gadget['description'] ?? '')) ?></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= esc($gadgetsIndexUrl) ?>" class="btn btn-outline-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Save gadget</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>