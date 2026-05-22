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
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Setup</th>
                        <th>Actions</th>
                        <th>Last active</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($customers === []) : ?>
                        <tr><td colspan="8" class="text-center text-muted py-5">No customer accounts yet.</td></tr>
                    <?php endif ?>
                    <?php foreach ($customers as $customer) : ?>
                        <?php $setupUrl = $customer['setup_token'] ? site_url('customers/setup/' . (string) $customer['setup_token']) : null; ?>
                        <?php $regenerateUrl = site_url('customers/' . (string) $customer['id'] . '/setup-link'); ?>
                        <?php $whatsAppUrl = $customer['phone'] && $setupUrl ? 'https://wa.me/' . preg_replace('/\D+/', '', (string) $customer['phone']) . '?text=' . rawurlencode('Create your Rentify Pro password: ' . $setupUrl) : null; ?>
                        <tr>
                            <td><?= esc((string) $customer['username']) ?></td>
                            <td><?= esc((string) ($customer['email'] ?? '-')) ?></td>
                            <td><?= esc((string) ($customer['phone'] ?? '-')) ?></td>
                            <td>
                                <span class="badge badge-<?= (int) $customer['active'] === 1 ? 'success' : 'secondary' ?>">
                                    <?= (int) $customer['active'] === 1 ? 'Active' : 'Suspended' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($customer['password_set_at'] !== null) : ?>
                                    <span class="badge badge-success">Ready</span>
                                <?php elseif ($setupUrl) : ?>
                                    <a href="<?= esc($setupUrl) ?>" class="btn btn-sm btn-outline-primary">Setup link</a>
                                <?php else : ?>
                                    <span class="badge badge-secondary">Pending</span>
                                <?php endif ?>
                            </td>
                            <td>
                                <?php if ($customer['password_set_at'] === null && $setupUrl !== null) : ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mb-1 js-copy-link" data-setup-link="<?= esc($setupUrl, 'attr') ?>">Copy</button>
                                    <?php if ($whatsAppUrl !== null) : ?>
                                        <a href="<?= esc($whatsAppUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success mb-1">WhatsApp</a>
                                    <?php endif ?>
                                    <form action="<?= esc($regenerateUrl) ?>" method="post" class="d-inline-block mb-1">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-warning">Regenerate</button>
                                    </form>
                                <?php else : ?>
                                    <span class="text-muted small">No action needed</span>
                                <?php endif ?>
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

<?= $this->section('pageScripts') ?>
<script>
document.addEventListener('click', function (event) {
    var button = event.target.closest('.js-copy-link');

    if (!button) {
        return;
    }

    var setupLink = button.getAttribute('data-setup-link') || '';

    if (!setupLink) {
        return;
    }

    navigator.clipboard.writeText(setupLink).then(function () {
        var originalText = button.textContent;
        button.textContent = 'Copied';
        window.setTimeout(function () {
            button.textContent = originalText;
        }, 1500);
    });
});
</script>
<?= $this->endSection() ?>