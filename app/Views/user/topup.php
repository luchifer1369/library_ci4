<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<h3 class="mb-4"><i class="fa-solid fa-wallet me-2"></i>Top Up Poin</h3>
<p class="text-muted mb-4">Saldo saat ini: <strong class="text-white"><?= $user['poin'] ?> Poin</strong></p>

<div class="row g-4">
    <?php
    $icons = ['pemula' => 'fa-seedling', 'reguler' => 'fa-book', 'kutubuku' => 'fa-book-open', 'sultan' => 'fa-crown'];
    $highlights = ['kutubuku' => 'Best Value'];
    foreach ($packages as $key => $pkg):
    ?>
        <div class="col-md-6 col-lg-3">
            <div class="book-card p-4 text-center position-relative">
                <?php if (isset($highlights[$key])): ?>
                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2"><?= $highlights[$key] ?></span>
                <?php endif; ?>
                <i class="fa-solid <?= $icons[$key] ?> fa-2x text-primary mb-3"></i>
                <h5><?= esc($pkg['label']) ?></h5>
                <div class="fs-3 fw-bold text-primary mb-1"><?= $pkg['points'] ?> Poin</div>
                <?php if ($pkg['bonus'] > 0): ?>
                    <small class="text-success d-block mb-2">Bonus +<?= $pkg['bonus'] ?> Poin</small>
                <?php endif; ?>
                <div class="text-muted mb-3">Rp <?= number_format($pkg['price'], 0, ',', '.') ?></div>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/e0/QRIS_logo.svg" height="24" alt="QRIS">
                    <span class="badge bg-success">GoPay</span>
                    <span class="badge bg-primary">DANA</span>
                </div>
                <form action="<?= base_url('topup/purchase') ?>" method="post" class="purchase-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="package" value="<?= $key ?>">
                    <button type="submit" class="btn btn-primary w-100 btn-purchase" data-label="<?= esc($pkg['label']) ?>" data-points="<?= $pkg['points'] ?>">Beli Sekarang</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.purchase-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('.btn-purchase');
        Swal.fire({
            title: 'Pembayaran Berhasil!',
            text: btn.dataset.label + ' - +' + btn.dataset.points + ' poin ditambahkan.',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#6366f1'
        }).then(() => this.submit());
    });
});
</script>
<?= $this->endSection() ?>
