<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<h3 class="mb-4"><i class="fa-solid fa-user me-2"></i>Profil Saya</h3>

<div class="row g-4">
    <div class="col-md-5">
        <div class="quest-panel p-4">
            <h5 class="mb-3">Informasi Akun</h5>
            <table class="table table-borderless table-dark-theme mb-0">
                <tr><td class="text-muted">Nama</td><td><?= esc($user['nama_lengkap']) ?></td></tr>
                <tr><td class="text-muted">Email</td><td><?= esc($user['email']) ?></td></tr>
                <tr><td class="text-muted">Saldo Poin</td><td><strong><?= $user['poin'] ?> Poin</strong></td></tr>
                <tr><td class="text-muted">Status</td><td>
                    <?php if ($user['is_premium']): ?>
                        <span class="premium-badge"><i class="fa-solid fa-crown"></i> Premium</span>
                        <?php if ($user['premium_expired_at']): ?>
                            <small class="text-muted d-block">Berlaku hingga <?= date('d M Y', strtotime($user['premium_expired_at'])) ?></small>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="badge bg-secondary">Normal</span>
                    <?php endif; ?>
                </td></tr>
                <tr><td class="text-muted">Auto-Deduct</td><td><?= $user['auto_deduct_enabled'] ? 'Aktif' : 'Nonaktif' ?></td></tr>
            </table>

            <hr class="border-secondary my-4">

            <h6 class="mb-3">Paket Premium</h6>
            <?php if (!$user['free_trial_used']): ?>
                <form action="<?= base_url('profile/premium-trial') ?>" method="post" class="mb-2">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-warning w-100"><i class="fa-solid fa-gift me-1"></i> Klaim Free Trial 3 Hari</button>
                </form>
            <?php endif; ?>
            <form action="<?= base_url('profile/premium') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary w-100" <?= $user['poin'] < 200 ? 'disabled' : '' ?>>
                    <i class="fa-solid fa-crown me-1"></i> Beli Premium 30 Hari (200 Poin)
                </button>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="quest-panel p-4">
            <h5 class="mb-3">Riwayat Transaksi</h5>
            <?php if (empty($transactions)): ?>
                <p class="text-muted mb-0">Belum ada transaksi.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead><tr><th>Tanggal</th><th>Tipe</th><th>Jumlah</th><th>Keterangan</th></tr></thead>
                        <tbody>
                            <?php foreach ($transactions as $tx): ?>
                                <tr>
                                    <td class="small"><?= date('d/m/Y H:i', strtotime($tx['created_at'])) ?></td>
                                    <td><span class="badge bg-<?= $tx['type'] === 'spend' ? 'danger' : ($tx['type'] === 'premium' ? 'warning' : 'success') ?>"><?= esc($tx['type']) ?></span></td>
                                    <td><?= $tx['type'] === 'spend' ? '-' : '+' ?><?= $tx['amount'] ?></td>
                                    <td class="small"><?= esc($tx['description']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
