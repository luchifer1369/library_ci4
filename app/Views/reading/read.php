<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0a0a0a; color: #fff; min-height: 100vh; display: flex; flex-direction: column; }
        .reader-header { background: rgba(15,23,42,0.95); padding: 12px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .reader-content { flex: 1; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .reader-content img { max-width: 100%; max-height: calc(100vh - 160px); object-fit: contain; border-radius: 4px; box-shadow: 0 8px 32px rgba(0,0,0,0.5); }
        .reader-nav { background: rgba(15,23,42,0.95); padding: 16px; border-top: 1px solid rgba(255,255,255,0.08); }
        .reader-nav .btn { min-width: 120px; }
        .blocked-overlay { filter: blur(8px); opacity: 0.3; pointer-events: none; }
    </style>
</head>
<body>
    <div class="reader-header d-flex justify-content-between align-items-center">
        <a href="<?= base_url('book/' . $book['id']) ?>" class="text-white text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Detail</a>
        <span class="small text-muted"><?= esc($book['title']) ?> — Halaman <?= $pageNum ?> / <?= $totalPages ?></span>
        <span class="small"><i class="fa-solid fa-coins text-warning"></i> <?= $user['poin'] ?> Poin</span>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger m-3 mb-0"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="reader-content <?= !empty($blocked) ? 'blocked-overlay' : '' ?>">
        <img src="<?= base_url($page['image_path']) ?>" alt="Halaman <?= $pageNum ?>">
    </div>

    <div class="reader-nav">
        <div class="container d-flex justify-content-between align-items-center">
            <?php if ($pageNum > 1): ?>
                <a href="<?= base_url('book/' . $book['id'] . '/read/' . $prevPage) ?>" class="btn btn-outline-light"><i class="fa-solid fa-chevron-left me-1"></i> Previous</a>
            <?php else: ?>
                <span></span>
            <?php endif; ?>

            <a href="<?= base_url('book/' . $book['id']) ?>" class="btn btn-secondary"><i class="fa-solid fa-compress me-1"></i> Minimize</a>

            <?php if ($pageNum < $totalPages): ?>
                <a href="<?= base_url('book/' . $book['id'] . '/read/' . $nextPage . '?from=next') ?>" class="btn btn-outline-light">Next <i class="fa-solid fa-chevron-right ms-1"></i></a>
            <?php else: ?>
                <span></span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($showModal)): ?>
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.7)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-secondary text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="fa-solid fa-lock text-warning me-2"></i>Halaman Berbayar</h5>
                </div>
                <div class="modal-body">
                    <p>Halaman selanjutnya berbayar (1 Poin per halaman). Apakah Anda ingin mengaktifkan potong poin otomatis?</p>
                    <p class="small text-muted mb-0">Saldo Anda: <strong><?= $user['poin'] ?> Poin</strong></p>
                </div>
                <div class="modal-footer border-secondary flex-wrap gap-2">
                    <a href="<?= base_url('book/' . $book['id'] . '/read/' . $prevPage) ?>" class="btn btn-outline-secondary">Batal</a>
                    <form action="<?= base_url('read/unlock-once') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                        <input type="hidden" name="page_number" value="<?= $pageNum ?>">
                        <button type="submit" class="btn btn-outline-primary" <?= $user['poin'] < 1 ? 'disabled' : '' ?>>Buka Sekali (1 Poin)</button>
                    </form>
                    <form action="<?= base_url('read/enable-auto-deduct') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                        <input type="hidden" name="page_number" value="<?= $pageNum ?>">
                        <button type="submit" class="btn btn-primary" <?= $user['poin'] < 1 ? 'disabled' : '' ?>>Aktifkan Auto-Deduct</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
