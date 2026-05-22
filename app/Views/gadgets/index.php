<?php
/**
 * @var string $role
 * @var list<array<string, mixed>> $gadgets
 */
?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php $gadgetsCreateUrl = site_url('gadgets/create'); ?>
<?php $rentalsCreateBaseUrl = site_url('rentals/create'); ?>
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
    <div>
        <h1 class="h2 mb-1">Gadget catalog</h1>
        <p class="text-muted mb-0">Current stock levels and daily rental rates.</p>
    </div>
    <?php if (in_array($role, ['admin', 'staff'], true)) : ?>
        <a href="<?= esc($gadgetsCreateUrl) ?>" class="btn btn-primary mt-3 mt-lg-0">Add gadget</a>
    <?php endif ?>
</div>

<div class="row">
    <?php foreach ($gadgets as $gadget) : ?>
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card page-card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge badge-soft mb-2"><?= esc($gadget['code']) ?></span>
                            <h2 class="h5 mb-1"><?= esc($gadget['name']) ?></h2>
                            <p class="text-muted mb-0"><?= esc($gadget['brand']) ?></p>
                        </div>
                        <span class="badge badge-<?= (int) $gadget['available_stock'] > 0 ? 'success' : 'danger' ?>"><?= esc((string) $gadget['available_stock']) ?>/<?= esc((string) $gadget['stock']) ?></span>
                    </div>
                    <p class="text-muted flex-grow-1"><?= esc($gadget['description']) ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <strong>$<?= number_format((float) $gadget['daily_rate'], 2) ?>/day</strong>
                        <div>
                            <?php if (in_array($role, ['admin', 'staff'], true)) : ?>
                                <a href="<?= esc(site_url('gadgets/' . (string) $gadget['id'] . '/edit')) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <?php endif ?>
                            <?php if ($role === 'customer' && (int) $gadget['available_stock'] > 0) : ?>
                                <a href="<?= esc($rentalsCreateBaseUrl . '?gadget=' . rawurlencode((string) $gadget['id'])) ?>" class="btn btn-sm btn-primary">Rent now</a>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>
<?= $this->endSection() ?>