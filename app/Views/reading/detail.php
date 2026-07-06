<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-md-4">
        <img src="<?= base_url($book['cover_image']) ?>" class="img-fluid rounded shadow" alt="<?= esc($book['title']) ?>">
    </div>
    <div class="col-md-8">
        <span class="badge bg-primary mb-2"><?= esc($book['nama_kategori']) ?></span>
        <h2 class="text-white"><?= esc($book['title']) ?></h2>
        <div class="book-synopsis mb-3"><?= nl2br(esc($book['description'])) ?></div>
        <div class="d-flex gap-2 mb-3">
            <span class="badge bg-secondary"><i class="fa-solid fa-file me-1"></i><?= $book['total_pages'] ?> Halaman</span>
            <span class="badge bg-success"><i class="fa-solid fa-gift me-1"></i>Gratis hal. <?= $book['free_page_start'] ?>-<?= $book['free_page_end'] ?></span>
            <span class="badge bg-info"><i class="fa-solid fa-eye me-1"></i><?= $book['views'] ?> views</span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('book/' . $book['id'] . '/read/1') ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-book-open me-1"></i> Mulai Baca</a>
            <form action="<?= base_url('book/' . $book['id'] . '/favorite') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-lg <?= $isFavorite ? 'btn-danger' : 'btn-outline-danger' ?>">
                    <i class="fa-solid fa-heart"></i> <?= $isFavorite ? 'Favorit' : 'Favorite' ?>
                </button>
            </form>
        </div>
    </div>
</div>

<hr class="border-secondary my-4">

<h5 class="mb-3">Daftar Halaman</h5>
<div class="row g-2" id="pagesGrid">
    <?php foreach ($pages as $page): ?>
        <div class="col-4 col-md-2">
            <a href="<?= base_url('book/' . $book['id'] . '/read/' . $page['page_number']) ?>" class="btn btn-outline-light w-100">Hal. <?= $page['page_number'] ?></a>
        </div>
    <?php endforeach; ?>
</div>
<?php if ($totalPages > count($pages)): ?>
    <button id="loadMoreBtn" class="btn btn-secondary mt-3" data-offset="<?= count($pages) ?>" data-book="<?= $book['id'] ?>" data-total="<?= $totalPages ?>">
        <i class="fa-solid fa-plus me-1"></i> Load More
    </button>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const loadMoreBtn = document.getElementById('loadMoreBtn');
if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', async function() {
        const offset = parseInt(this.dataset.offset);
        const bookId = this.dataset.book;
        const res = await fetch(`<?= base_url('book') ?>/${bookId}/pages?offset=${offset}`);
        const data = await res.json();
        const grid = document.getElementById('pagesGrid');
        data.pages.forEach(p => {
            const col = document.createElement('div');
            col.className = 'col-4 col-md-2';
            col.innerHTML = `<a href="<?= base_url('book') ?>/${bookId}/read/${p.page_number}" class="btn btn-outline-light w-100">Hal. ${p.page_number}</a>`;
            grid.appendChild(col);
        });
        const newOffset = offset + data.pages.length;
        this.dataset.offset = newOffset;
        if (newOffset >= parseInt(this.dataset.total)) this.remove();
    });
}
</script>
<?= $this->endSection() ?>
