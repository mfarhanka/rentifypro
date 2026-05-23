<?php
/**
 * @var array<string, mixed> $tenant
 * @var array<string, int> $stats
 */
?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php $dashboardUrl = site_url('dashboard'); ?>
<?php $workspaceSettingsUrl = site_url('workspace/settings'); ?>
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
    <div>
        <span class="badge badge-soft px-3 py-2 mb-2">Admin workspace controls</span>
        <h1 class="h2 mb-1">Workspace settings</h1>
        <p class="text-muted mb-0">Manage the identity of the current workspace and review its tenant-scoped activity.</p>
    </div>
    <div class="mt-3 mt-lg-0">
        <a href="<?= esc($dashboardUrl) ?>" class="btn btn-outline-secondary mr-2">Back to dashboard</a>
        <a href="<?= esc($workspaceSettingsUrl) ?>" class="btn btn-primary">Refresh</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Staff seats</p>
                <h2 class="display-4 mb-0"><?= esc((string) $stats['staff']) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Customers</p>
                <h2 class="display-4 mb-0"><?= esc((string) $stats['customers']) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Catalog items</p>
                <h2 class="display-4 mb-0"><?= esc((string) $stats['gadgets']) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Rentals</p>
                <h2 class="display-4 mb-0"><?= esc((string) $stats['rentals']) ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card page-card h-100">
            <div class="card-body p-4 p-lg-5">
                <h2 class="h4 mb-4">Workspace identity</h2>
                <form action="<?= esc($workspaceSettingsUrl) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="name">Workspace name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= esc(old('name', (string) $tenant['name'])) ?>" required>
                        <small class="form-text text-muted">Shown to signed-in users throughout the app.</small>
                    </div>
                    <div class="form-group">
                        <label for="slug">Workspace slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= esc(old('slug', (string) $tenant['slug'])) ?>" required>
                        <small class="form-text text-muted">Lowercase letters, numbers, and hyphens only. This is the stable workspace identifier for future domain or invite routing.</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= esc($dashboardUrl) ?>" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save workspace</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card page-card h-100">
            <div class="card-body p-4 p-lg-5">
                <h2 class="h4 mb-3">SaaS readiness</h2>
                <div class="border rounded-lg p-3 mb-3 bg-white">
                    <h3 class="h6">Current workspace</h3>
                    <p class="text-muted mb-2">Name: <?= esc((string) $tenant['name']) ?></p>
                    <p class="text-muted mb-0">Slug: <?= esc((string) $tenant['slug']) ?></p>
                </div>
                <div class="border rounded-lg p-3 mb-3 bg-white">
                    <h3 class="h6">What this page controls</h3>
                    <p class="text-muted mb-0">This updates the tenant record used to scope staff, customers, gadgets, rentals, and the workspace badge.</p>
                </div>
                <div class="border rounded-lg p-3 bg-white">
                    <h3 class="h6">Recommended next step</h3>
                    <p class="text-muted mb-0">Use the workspace slug as the base for invite URLs, subdomains, or billing records once you add those SaaS layers.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>