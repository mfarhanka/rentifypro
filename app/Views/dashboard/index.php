<?php
/**
 * @var string $role
 * @var array<string, mixed> $stats
 * @var list<array<string, mixed>> $recentRentals
 * @var list<array<string, mixed>> $gadgets
 */
?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php $rentalsUrl = site_url('rentals'); ?>
<?php $gadgetsUrl = site_url('gadgets'); ?>
<?php $rentalsCreateBaseUrl = site_url('rentals/create'); ?>
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
    <div>
        <span class="badge badge-soft px-3 py-2 mb-2"><?= ucfirst(esc($role)) ?> workspace</span>
        <h1 class="h2 mb-1">Rental operations at a glance</h1>
        <p class="text-muted mb-0">Track gadget availability, customer bookings, and invoice totals from one panel.</p>
    </div>
    <div class="mt-3 mt-lg-0">
        <a href="<?= esc($rentalsUrl) ?>" class="btn btn-outline-primary mr-2">View rentals</a>
        <a href="<?= esc($gadgetsUrl) ?>" class="btn btn-primary">Browse gadgets</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <p class="text-muted mb-1"><?= esc($stats['labelOne']) ?></p>
                <h2 class="display-4 mb-0"><?= esc((string) $stats['valueOne']) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <p class="text-muted mb-1"><?= esc($stats['labelTwo']) ?></p>
                <h2 class="display-4 mb-0"><?= esc((string) $stats['valueTwo']) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <p class="text-muted mb-1"><?= esc($stats['labelThree']) ?></p>
                <h2 class="display-4 mb-0">
                    <?php if (str_contains(strtolower($stats['labelThree']), 'invoice') || strtolower($stats['labelFour']) === 'revenue') : ?>
                        RM <?= esc(number_format((float) $stats['valueThree'], 2)) ?>
                    <?php else : ?>
                        <?= esc((string) $stats['valueThree']) ?>
                    <?php endif ?>
                </h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <p class="text-muted mb-1"><?= esc($stats['labelFour']) ?></p>
                <h2 class="display-4 mb-0">
                    <?php if (strtolower($stats['labelFour']) === 'revenue') : ?>
                        RM <?= esc(number_format((float) $stats['valueFour'], 2)) ?>
                    <?php else : ?>
                        <?= esc((string) $stats['valueFour']) ?>
                    <?php endif ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card page-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">Recent rentals</h2>
                    <a href="<?= esc($rentalsUrl) ?>" class="btn btn-sm btn-outline-secondary">All rentals</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Gadget</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentRentals === []) : ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">No rentals recorded yet.</td></tr>
                            <?php endif ?>
                            <?php foreach ($recentRentals as $rental) : ?>
                                <tr>
                                    <td><a href="<?= esc(site_url('rentals/' . (string) $rental['id'] . '/invoice')) ?>"><?= esc($rental['invoice_number']) ?></a></td>
                                    <td><?= esc($rental['customer_name']) ?></td>
                                    <td><?= esc($rental['gadget_name']) ?></td>
                                    <td><span class="badge badge-<?= $rental['status'] === 'approved' ? 'success' : ($rental['status'] === 'pending' ? 'warning' : 'secondary') ?>"><?= esc(ucfirst($rental['status'])) ?></span></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card page-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">Available gadgets</h2>
                    <a href="<?= esc($gadgetsUrl) ?>" class="btn btn-sm btn-outline-secondary">Catalog</a>
                </div>
                <?php foreach ($gadgets as $gadget) : ?>
                    <div class="border rounded-lg p-3 mb-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="h6 mb-1"><?= esc($gadget['name']) ?></h3>
                                <p class="text-muted mb-2"><?= esc($gadget['brand']) ?> · <?= esc($gadget['code']) ?></p>
                            </div>
                            <span class="badge badge-soft"><?= esc((string) $gadget['available_stock']) ?> available</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>RM <?= esc(number_format((float) $gadget['daily_rate'], 2)) ?>/day</strong>
                            <?php if ($role === 'customer') : ?>
                                <a href="<?= esc($rentalsCreateBaseUrl . '?gadget=' . rawurlencode((string) $gadget['id'])) ?>" class="btn btn-sm btn-primary">Rent</a>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>