<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h2 class="mb-4"><?= esc($title) ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="<?= $book ? base_url('admin/books/update/' . $book['id']) : base_url('admin/books/store') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" name="title" class="form-control" value="<?= old('title', $book['title'] ?? '') ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= old('category_id', $book['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi / Sinopsis</label>
                <textarea name="description" class="form-control" rows="4" required><?= old('description', $book['description'] ?? '') ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Cover Buku <?= $book ? '<small class="text-muted">- kosongkan jika tidak diubah</small>' : '' ?></label>
                    <input type="file" name="cover_image" class="form-control" accept="image/*" <?= $book ? '' : 'required' ?>>
                    <small class="text-muted">Format: JPG, PNG, GIF, WebP, dll. Maks. 5 MB</small>
                    <?php if ($book && !empty($book['cover_image'])): ?>
                        <img src="<?= base_url($book['cover_image']) ?>" class="mt-2 rounded" width="80">
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">File Buku (PDF) <?= $book ? '<small class="text-muted">- kosongkan jika tidak diubah</small>' : '' ?></label>
                    <input type="file" name="file_pdf" class="form-control" accept=".pdf" <?= $book ? '' : 'required' ?>>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Halaman Gratis Dari</label>
                    <input type="number" name="free_page_start" id="free_page_start" class="form-control" min="1" value="<?= old('free_page_start', $book['free_page_start'] ?? 1) ?>" required>
                    <small class="text-muted">Nomor halaman pertama yang bisa dibaca gratis user normal.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Halaman Gratis Sampai</label>
                    <input type="number" name="free_page_end" id="free_page_end" class="form-control" min="1" value="<?= old('free_page_end', $book['free_page_end'] ?? 10) ?>" required>
                    <small class="text-muted">Harus ≥ nilai "Dari". Contoh: Dari <strong>1</strong>, Sampai <strong>10</strong> → hal. 1–10 gratis, hal. 11+ berbayar (1 poin/halaman).</small>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Simpan</button>
                <a href="<?= base_url('admin/books') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('free_page_start')?.addEventListener('input', function () {
    const end = document.getElementById('free_page_end');
    if (end) end.min = this.value || 1;
});
</script>
<?= $this->endSection() ?>
