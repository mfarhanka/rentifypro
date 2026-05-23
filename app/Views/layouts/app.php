<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= esc($title ?? 'Rentify Pro') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        :root {
            --sand: #f6f1e8;
            --ink: #13324b;
            --ocean: #2a5d84;
            --mint: #d8efe4;
            --amber: #f4b860;
        }

        body {
            background: radial-gradient(circle at top left, rgba(244, 184, 96, 0.18), transparent 35%), linear-gradient(180deg, var(--sand) 0%, #eef4f7 100%);
            min-height: 100vh;
            color: #23313f;
        }

        .navbar {
            background: linear-gradient(90deg, var(--ink), var(--ocean));
        }

        .navbar-brand {
            letter-spacing: 0.12em;
            font-weight: 700;
        }

        .page-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 20px 45px rgba(19, 50, 75, 0.08);
        }

        .stat-card {
            border: 0;
            border-radius: 1rem;
            overflow: hidden;
        }

        .stat-card .card-body {
            background: rgba(255, 255, 255, 0.9);
        }

        .badge-soft {
            background: var(--mint);
            color: var(--ink);
        }
    </style>
    <?= $this->renderSection('pageStyles') ?>
</head>
<body>
<?php $user = auth()->user(); ?>
<?php $role = 'Guest'; ?>
<?php $dashboardUrl = site_url('dashboard'); ?>
<?php $customersUrl = site_url('customers'); ?>
<?php $staffUrl = site_url('staff'); ?>
<?php $workspaceSettingsUrl = site_url('workspace/settings'); ?>
<?php $gadgetsUrl = site_url('gadgets'); ?>
<?php $rentalsUrl = site_url('rentals'); ?>
<?php $newRentalUrl = site_url('rentals/create'); ?>
<?php $logoutUrl = site_url('logout'); ?>
<?php $workspaceName = $currentTenant['name'] ?? 'No workspace'; ?>
<?php if ($user !== null) : ?>
    <?php if ($user->inGroup('admin')) : ?>
        <?php $role = 'Admin'; ?>
    <?php elseif ($user->inGroup('staff')) : ?>
        <?php $role = 'Staff'; ?>
    <?php else : ?>
        <?php $role = 'Customer'; ?>
    <?php endif ?>
<?php endif ?>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?= esc($dashboardUrl) ?>">RENTIFY PRO</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="<?= esc($dashboardUrl) ?>">Dashboard</a></li>
                <?php if ($user !== null && ( $user->inGroup('admin') || $user->inGroup('staff') )) : ?>
                    <li class="nav-item"><a class="nav-link" href="<?= esc($customersUrl) ?>">Customers</a></li>
                <?php endif ?>
                <?php if ($user !== null && $user->inGroup('admin')) : ?>
                    <li class="nav-item"><a class="nav-link" href="<?= esc($staffUrl) ?>">Staff</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= esc($workspaceSettingsUrl) ?>">Workspace</a></li>
                <?php endif ?>
                <li class="nav-item"><a class="nav-link" href="<?= esc($gadgetsUrl) ?>">Gadgets</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= esc($rentalsUrl) ?>">Rentals</a></li>
                <?php if ($user !== null && $user->inGroup('customer')) : ?>
                    <li class="nav-item"><a class="nav-link" href="<?= esc($newRentalUrl) ?>">New Rental</a></li>
                <?php endif ?>
            </ul>
            <span class="navbar-text text-light mr-3">
                <span class="badge badge-soft mr-2"><?= esc($workspaceName) ?></span>
                <?= esc($user?->username ?? 'Guest') ?> <span class="badge badge-light"><?= esc($role) ?></span>
            </span>
            <?php if ($user !== null) : ?>
                <a href="<?= esc($logoutUrl) ?>" class="btn btn-outline-light btn-sm">Logout</a>
            <?php endif ?>
        </div>
    </div>
</nav>

<main class="container py-4 py-lg-5">
    <?php if (session('message')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session('message')) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif ?>

    <?php if (session('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session('error')) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif ?>

    <?php if (session('errors')) : ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach ((array) session('errors') as $error) : ?>
                <div><?= esc($error) ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <?= $this->renderSection('content') ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdXdVQ8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8nGmVVx4Um1vskeMj0sFAP+J" crossorigin="anonymous"></script>
<?= $this->renderSection('pageScripts') ?>
</body>
</html>