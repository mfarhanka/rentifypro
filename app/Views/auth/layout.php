<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $this->renderSection('title') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f4f1ea 0%, #d6e4f0 100%);
        }

        .auth-shell {
            min-height: 100vh;
        }

        .brand-panel {
            background: linear-gradient(160deg, #1f3b57 0%, #2a5d84 100%);
            color: #fff;
        }

        .brand-panel h1 {
            letter-spacing: 0.08em;
        }

        .card {
            border: 0;
            border-radius: 1rem;
        }
    </style>
    <?= $this->renderSection('pageStyles') ?>
</head>
<body>
    <main class="container-fluid auth-shell">
        <?= $this->renderSection('main') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdXdVQ8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8nGmVVx4Um1vskeMj0sFAP+J" crossorigin="anonymous"></script>
    <?= $this->renderSection('pageScripts') ?>
</body>
</html>