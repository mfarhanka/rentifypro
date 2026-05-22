<?php
/**
 * @var string $role
 * @var list<array<string, mixed>> $rentals
 */
?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
    <div>
        <h1 class="h2 mb-1">Rental records</h1>
        <p class="text-muted mb-0"><?= $role === 'customer' ? 'Your rental history and invoices.' : 'Track requests, approvals, returns, and invoice status.' ?></p>
    </div>
    <?php if ($role === 'customer') : ?>
        <a href="/rentals/create" class="btn btn-primary mt-3 mt-lg-0">New rental</a>
    <?php endif ?>
</div>

<div class="card page-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Gadget</th>
                        <th>Duration</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($rentals === []) : ?>
                        <tr><td colspan="7" class="text-center text-muted py-5">No rental records found.</td></tr>
                    <?php endif ?>
                    <?php foreach ($rentals as $rental) : ?>
                        <tr>
                            <td><?= esc($rental['invoice_number']) ?></td>
                            <td><?= esc($rental['customer_name']) ?></td>
                            <td><?= esc($rental['gadget_name']) ?></td>
                            <td><?= esc($rental['start_date']) ?> to <?= esc($rental['end_date']) ?></td>
                            <td>$<?= number_format((float) $rental['total_amount'], 2) ?></td>
                            <td><span class="badge badge-<?= $rental['status'] === 'approved' ? 'success' : ($rental['status'] === 'pending' ? 'warning' : 'secondary') ?>"><?= esc(ucfirst($rental['status'])) ?></span></td>
                            <td class="text-right">
                                <a href="/rentals/<?= esc((string) $rental['id']) ?>/invoice" class="btn btn-sm btn-outline-secondary">Invoice</a>
                                <?php if (in_array($role, ['admin', 'staff'], true)) : ?>
                                    <form action="/rentals/<?= esc((string) $rental['id']) ?>/status" method="post" class="d-inline-block ml-2">
                                        <?= csrf_field() ?>
                                        <div class="input-group input-group-sm">
                                            <select class="custom-select" name="status">
                                                <?php foreach (['pending', 'approved', 'returned', 'cancelled'] as $status) : ?>
                                                    <option value="<?= esc($status) ?>" <?= $rental['status'] === $status ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option>
                                                <?php endforeach ?>
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>