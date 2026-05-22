<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?>Register | Rentify Pro<?= $this->endSection() ?>

<?= $this->section('main') ?>
<div class="row no-gutters auth-shell align-items-stretch">
    <div class="col-lg-5 d-none d-lg-flex brand-panel align-items-center justify-content-center p-5">
        <div class="col-10">
            <p class="text-uppercase mb-3">Customer Portal</p>
            <h1 class="display-4 font-weight-bold mb-4">Start renting the gadgets you need.</h1>
            <p class="lead mb-0">Customers can register, place rental requests, and download invoices after approval.</p>
        </div>
    </div>
    <div class="col-lg-7 d-flex align-items-center justify-content-center p-4 p-lg-5">
        <div class="card shadow-lg w-100" style="max-width: 540px;">
            <div class="card-body p-4 p-lg-5">
                <h2 class="h3 mb-4">Create account</h2>

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

                <form action="<?= url_to('register') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password_confirm">Confirm password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </form>

                <p class="text-muted text-center mt-4 mb-0">Already registered? <a href="<?= url_to('login') ?>">Login here</a></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>