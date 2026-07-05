<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Manajemen Buku</h2>
    <a href="<?= base_url('admin/books/create') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Tambah Buku</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Cover</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Halaman</th>
                    <th>Gratis</th>
                    <th>Views</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($books)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada buku.</td></tr>
                <?php else: ?>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><img src="<?= base_url($book['cover_image']) ?>" alt="" width="50" height="70" class="rounded object-fit-cover"></td>
                            <td><?= esc($book['title']) ?></td>
                            <td><span class="badge bg-secondary"><?= esc($book['nama_kategori']) ?></span></td>
                            <td><?= $book['total_pages'] ?></td>
                            <td><?= $book['free_page_start'] ?> - <?= $book['free_page_end'] ?></td>
                            <td><?= $book['views'] ?></td>
                            <td>
                                <a href="<?= base_url('admin/books/edit/' . $book['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                                <a href="<?= base_url('admin/books/delete/' . $book['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus buku ini?')"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
