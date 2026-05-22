<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?>Login | Rentify Pro<?= $this->endSection() ?>

<?= $this->section('main') ?>
<div class="row no-gutters auth-shell align-items-stretch">
    <div class="col-lg-6 d-none d-lg-flex brand-panel align-items-center justify-content-center p-5">
        <div class="col-10">
            <p class="text-uppercase mb-3">Rentify Pro</p>
            <h1 class="display-4 font-weight-bold mb-4">Rental tracking for gadgets, staff, and invoices.</h1>
            <p class="lead mb-0">Sign in to manage device availability, customer rentals, and printable invoices from one dashboard.</p>
        </div>
    </div>
    <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-lg-5">
        <div class="card shadow-lg w-100" style="max-width: 460px;">
            <div class="card-body p-4 p-lg-5">
                <h2 class="h3 mb-4">Sign in</h2>

                <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger"><?= esc(session('error')) ?></div>
                <?php elseif (session('errors') !== null) : ?>
                    <div class="alert alert-danger">
                        <?php if (is_array(session('errors'))) : ?>
                            <?php foreach (session('errors') as $error) : ?>
                                <div><?= esc($error) ?></div>
                            <?php endforeach ?>
                        <?php else : ?>
                            <?= esc(session('errors')) ?>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <?php if (session('message') !== null) : ?>
                    <div class="alert alert-success"><?= esc(session('message')) ?></div>
                <?php endif ?>

                <form action="<?= url_to('login') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="remember" name="remember" <?= old('remember') ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="remember">Remember me</label>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>

                <?php if (setting('Auth.allowRegistration')) : ?>
                    <p class="text-muted text-center mt-4 mb-0">Need an account? <a href="<?= url_to('register') ?>">Create one</a></p>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>