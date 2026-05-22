<?php
/**
 * @var list<array<string, mixed>> $staffMembers
 */
?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php $staffCreateUrl = site_url('staff/create'); ?>
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
    <div>
        <h1 class="h2 mb-1">Staff accounts</h1>
        <p class="text-muted mb-0">Admins can create, edit, suspend, and remove staff access.</p>
    </div>
    <a href="<?= esc($staffCreateUrl) ?>" class="btn btn-primary mt-3 mt-lg-0">Add staff</a>
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
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($staffMembers === []) : ?>
                        <tr><td colspan="6" class="text-center text-muted py-5">No staff accounts yet.</td></tr>
                    <?php endif ?>
                    <?php foreach ($staffMembers as $staffMember) : ?>
                        <?php $staffId = (string) $staffMember['id']; ?>
                        <tr>
                            <td><?= esc((string) $staffMember['username']) ?></td>
                            <td><?= esc((string) ($staffMember['email'] ?? '-')) ?></td>
                            <td>
                                <span class="badge badge-<?= (int) $staffMember['active'] === 1 ? 'success' : 'secondary' ?>">
                                    <?= (int) $staffMember['active'] === 1 ? 'Active' : 'Suspended' ?>
                                </span>
                            </td>
                            <td><?= esc($staffMember['last_active'] ? (string) $staffMember['last_active'] : 'Never') ?></td>
                            <td><?= esc((string) $staffMember['created_at']) ?></td>
                            <td class="text-right">
                                <a href="<?= esc(site_url('staff/' . $staffId . '/edit')) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="<?= esc(site_url('staff/' . $staffId . '/suspend')) ?>" method="post" class="d-inline-block ml-2">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-warning">
                                        <?= (int) $staffMember['active'] === 1 ? 'Suspend' : 'Reactivate' ?>
                                    </button>
                                </form>
                                <form action="<?= esc(site_url('staff/' . $staffId . '/delete')) ?>" method="post" class="d-inline-block ml-2" onsubmit="return confirm('Remove this staff account?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>