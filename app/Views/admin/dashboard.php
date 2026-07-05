<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h2 class="mb-4">Dashboard Admin</h2>
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon bg-primary bg-opacity-10 text-primary"><i class="fa-solid fa-book"></i></div>
            <div>
                <div class="text-muted small">Total Buku</div>
                <div class="fs-3 fw-bold"><?= $totalBooks ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon bg-success bg-opacity-10 text-success"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="text-muted small">Total User</div>
                <div class="fs-3 fw-bold"><?= $totalUsers ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon bg-warning bg-opacity-10 text-warning"><i class="fa-solid fa-eye"></i></div>
            <div>
                <div class="text-muted small">Total Kunjungan</div>
                <div class="fs-3 fw-bold"><?= $totalViews ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h5 class="mb-0">Buku Paling Populer</h5></div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Judul</th><th>Kategori</th><th>Views</th></tr></thead>
            <tbody>
                <?php if (empty($popularBooks)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data buku.</td></tr>
                <?php else: ?>
                    <?php foreach ($popularBooks as $book): ?>
                        <tr>
                            <td><?= esc($book['title']) ?></td>
                            <td><?= esc($book['nama_kategori']) ?></td>
                            <td><?= $book['views'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
