<?php
/**
 * @var list<array<string, mixed>> $customers
 */
?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php $customerCreateUrl = site_url('customers/create'); ?>
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
    <div>
        <h1 class="h2 mb-1">Customer accounts</h1>
        <p class="text-muted mb-0">Admins and staff can register customer accounts for walk-in or assisted rentals.</p>
    </div>
    <a href="<?= esc($customerCreateUrl) ?>" class="btn btn-primary mt-3 mt-lg-0">Add customer</a>
</div>

<div class="card page-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Last active</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($customers === []) : ?>
                        <tr><td colspan="5" class="text-center text-muted py-5">No customer accounts yet.</td></tr>
                    <?php endif ?>
                    <?php foreach ($customers as $customer) : ?>
                        <tr>
                            <td><?= esc((string) $customer['username']) ?></td>
                            <td><?= esc((string) ($customer['email'] ?? '-')) ?></td>
                            <td>
                                <span class="badge badge-<?= (int) $customer['active'] === 1 ? 'success' : 'secondary' ?>">
                                    <?= (int) $customer['active'] === 1 ? 'Active' : 'Suspended' ?>
                                </span>
                            </td>
                            <td><?= esc($customer['last_active'] ? (string) $customer['last_active'] : 'Never') ?></td>
                            <td><?= esc((string) $customer['created_at']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>