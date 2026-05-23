<?php
/**
 * @var array<string, mixed> $rental
 */
?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php $rentalsIndexUrl = site_url('rentals'); ?>
<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Invoice <?= esc($rental['invoice_number']) ?></h1>
                <p class="text-muted mb-0">Generated for gadget rental tracking and payment confirmation.</p>
            </div>
            <button type="button" class="btn btn-primary" onclick="window.print()">Print invoice</button>
        </div>

        <div class="card page-card">
            <div class="card-body p-4 p-lg-5">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h2 class="h5">Rentify Pro</h2>
                        <p class="mb-0 text-muted">Gadget rental management<br>Admin, staff, and customer portal</p>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <div><strong>Status:</strong> <?= esc(ucfirst($rental['status'])) ?></div>
                        <div><strong>Invoice #:</strong> <?= esc($rental['invoice_number']) ?></div>
                        <div><strong>Generated:</strong> <?= esc(date('Y-m-d', strtotime((string) $rental['created_at']))) ?></div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h3 class="h6 text-uppercase text-muted">Customer</h3>
                        <p class="mb-0"><?= esc($rental['customer_name']) ?></p>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <h3 class="h6 text-uppercase text-muted">Rental item</h3>
                        <p class="mb-0"><?= esc($rental['gadget_name']) ?> (<?= esc($rental['gadget_brand']) ?>)</p>
                        <small class="text-muted">Code: <?= esc($rental['gadget_code']) ?></small>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Rental days</th>
                                <th>Daily rate</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= esc($rental['gadget_name']) ?> rental<br><small class="text-muted"><?= esc($rental['start_date']) ?> to <?= esc($rental['end_date']) ?></small></td>
                                <td><?= esc((string) $rental['quantity']) ?></td>
                                <td><?= esc((string) $rental['rental_days']) ?></td>
                                <td>RM <?= esc(number_format((float) $rental['daily_rate'], 2)) ?></td>
                                <td class="text-right">RM <?= esc(number_format((float) $rental['total_amount'], 2)) ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total</th>
                                <th class="text-right">RM <?= esc(number_format((float) $rental['total_amount'], 2)) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <h3 class="h6 text-uppercase text-muted">Notes</h3>
                        <p class="mb-0"><?= esc($rental['notes'] ?: 'No additional notes for this rental.') ?></p>
                    </div>
                    <div class="col-md-4 text-md-right mt-4 mt-md-0">
                        <a href="<?= esc($rentalsIndexUrl) ?>" class="btn btn-outline-secondary">Back to rentals</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>