<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<h3 class="mb-4"><i class="fa-solid fa-heart text-danger me-2"></i>Buku Favorit</h3>
<div class="row g-4">
    <?php if (empty($favorites)): ?>
        <div class="col-12 text-center text-muted py-5">Belum ada buku favorit.</div>
    <?php else: ?>
        <?php foreach ($favorites as $fav): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= base_url('book/' . $fav['book_id']) ?>" class="text-decoration-none text-white">
                    <div class="book-card">
                        <img src="<?= base_url($fav['cover_image']) ?>" alt="">
                        <div class="p-3">
                            <h6 class="mb-1"><?= esc($fav['title']) ?></h6>
                            <small class="text-muted"><?= esc($fav['nama_kategori']) ?></small>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
