<?php
/**
 * @var list<array<string, mixed>> $gadgets
 * @var int $selectedId
 */
?>
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card page-card">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h1 class="h3 mb-1">Create rental request</h1>
                        <p class="text-muted mb-0">Choose a gadget, rental duration, and quantity. An invoice is generated automatically.</p>
                    </div>
                    <span class="badge badge-soft px-3 py-2">Customer access</span>
                </div>

                <form action="/rentals" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="gadget_id">Gadget</label>
                        <select class="form-control" id="gadget_id" name="gadget_id" required>
                            <option value="">Select a gadget</option>
                            <?php foreach ($gadgets as $gadget) : ?>
                                <option value="<?= esc((string) $gadget['id']) ?>" <?= (string) old('gadget_id', (string) $selectedId) === (string) $gadget['id'] ? 'selected' : '' ?>>
                                    <?= esc($gadget['name']) ?> - <?= esc($gadget['brand']) ?> ($<?= number_format((float) $gadget['daily_rate'], 2) ?>/day, <?= esc((string) $gadget['available_stock']) ?> available)
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="<?= esc(old('quantity', '1')) ?>" min="1" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="start_date">Start date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc(old('start_date', date('Y-m-d'))) ?>" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="end_date">End date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc(old('end_date', date('Y-m-d', strtotime('+1 day')))) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Optional delivery or usage notes"><?= esc(old('notes')) ?></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="/rentals" class="btn btn-outline-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Submit rental</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>