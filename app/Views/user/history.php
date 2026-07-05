<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<h3 class="mb-4"><i class="fa-solid fa-clock-rotate-left me-2"></i>Riwayat Baca</h3>
<div class="row g-4">
    <?php if (empty($histories)): ?>
        <div class="col-12 text-center text-muted py-5">Belum ada riwayat baca.</div>
    <?php else: ?>
        <?php foreach ($histories as $hist): ?>
            <div class="col-md-6">
                <div class="book-card d-flex p-3 gap-3">
                    <img src="<?= base_url($hist['cover_image']) ?>" width="80" height="110" class="rounded object-fit-cover">
                    <div class="flex-grow-1">
                        <h6><?= esc($hist['title']) ?></h6>
                        <small class="text-muted d-block mb-2"><?= esc($hist['nama_kategori']) ?></small>
                        <small class="text-muted">Terakhir dibaca: Halaman <?= $hist['last_read_page'] ?></small>
                        <div class="mt-2">
                            <a href="<?= base_url('book/' . $hist['book_id'] . '/read/' . $hist['last_read_page']) ?>" class="btn btn-sm btn-primary">Lanjutkan</a>
                            <a href="<?= base_url('book/' . $hist['book_id']) ?>" class="btn btn-sm btn-outline-light">Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
