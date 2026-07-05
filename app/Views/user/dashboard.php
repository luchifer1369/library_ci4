<?= $this->extend('layouts/user') ?>

<?= $this->section('content') ?>
<?php if (!empty($user['is_premium'])): ?>
    <div class="alert alert-warning py-2 small mb-3"><i class="fa-solid fa-crown me-1"></i> Akun Premium aktif<?= !empty($user['premium_expired_at']) ? ' hingga ' . date('d M Y', strtotime($user['premium_expired_at'])) : '' ?></div>
<?php endif; ?>

<div class="quest-panel p-4 mb-4">
    <h5 class="mb-3"><i class="fa-solid fa-trophy text-warning me-2"></i>Daily Quest</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background:rgba(15,23,42,0.5)">
                <div>
                    <div class="small text-muted">Quest 1</div>
                    <div>Tambah 1 favorit (<?= min($favoritesToday, 1) ?>/1)</div>
                </div>
                <?php if ($quest['quest_1_claimed']): ?>
                    <button class="btn btn-secondary btn-sm" disabled>Claimed</button>
                <?php elseif ($favoritesToday >= 1): ?>
                    <a href="<?= base_url('quest/claim/1') ?>" class="btn btn-success btn-sm">Claim</a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm" disabled>Claim</button>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background:rgba(15,23,42,0.5)">
                <div>
                    <div class="small text-muted">Quest 2</div>
                    <div>Baca 5 halaman (<?= min($quest['pages_read_today'], 5) ?>/5)</div>
                </div>
                <?php if ($quest['quest_2_claimed']): ?>
                    <button class="btn btn-secondary btn-sm" disabled>Claimed</button>
                <?php elseif ($quest['pages_read_today'] >= 5): ?>
                    <a href="<?= base_url('quest/claim/2') ?>" class="btn btn-success btn-sm">Claim</a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm" disabled>Claim</button>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background:rgba(15,23,42,0.5)">
                <div>
                    <div class="small text-muted">Quest 3</div>
                    <div>Baca 15 halaman (<?= min($quest['pages_read_today'], 15) ?>/15)</div>
                </div>
                <?php if ($quest['quest_3_claimed']): ?>
                    <button class="btn btn-secondary btn-sm" disabled>Claimed</button>
                <?php elseif ($quest['pages_read_today'] >= 15): ?>
                    <a href="<?= base_url('quest/claim/3') ?>" class="btn btn-success btn-sm">Claim</a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm" disabled>Claim</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<form method="get" class="row g-3 mb-4">
    <div class="col-md-6">
        <input type="text" name="search" class="form-control bg-dark border-secondary text-white" placeholder="Cari judul buku..." value="<?= esc($search ?? '') ?>">
    </div>
    <div class="col-md-4">
        <select name="category" class="form-select bg-dark border-secondary text-white">
            <option value="">Semua Kategori</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? '') == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['nama_kategori']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-search me-1"></i> Cari</button>
    </div>
</form>

<h5 class="mb-3">Koleksi Buku</h5>
<div class="row g-4">
    <?php if (empty($books)): ?>
        <div class="col-12 text-center text-muted py-5">Tidak ada buku ditemukan.</div>
    <?php else: ?>
        <?php foreach ($books as $book): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= base_url('book/' . $book['id']) ?>" class="text-decoration-none text-white">
                    <div class="book-card">
                        <img src="<?= base_url($book['cover_image']) ?>" alt="<?= esc($book['title']) ?>">
                        <div class="p-3">
                            <h6 class="mb-1 text-truncate"><?= esc($book['title']) ?></h6>
                            <small class="text-muted"><?= esc($book['nama_kategori']) ?></small>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
