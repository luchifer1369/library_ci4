<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin') ?> - M-Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
        .sidebar { min-height: 100vh; background: #0f172a; color: #e2e8f0; width: 260px; position: fixed; }
        .sidebar .brand { padding: 24px; font-weight: 800; font-size: 1.25rem; color: #818cf8; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar a { color: #94a3b8; text-decoration: none; display: block; padding: 12px 24px; transition: all .2s; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: rgba(99,102,241,0.15); }
        .main-content { margin-left: 260px; padding: 32px; }
        .stat-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .stat-card .icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><i class="fa-solid fa-book-open-reader me-2"></i>M-Library Admin</div>
        <nav class="mt-3">
            <a href="<?= base_url('admin') ?>" class="<?= uri_string() === 'admin' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
            <a href="<?= base_url('admin/books') ?>" class="<?= str_starts_with(uri_string(), 'admin/books') ? 'active' : '' ?>"><i class="fa-solid fa-book me-2"></i> Manajemen Buku</a>
            <a href="<?= base_url('logout') ?>"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
        </nav>
    </div>
    <div class="main-content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger"><ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <?= $this->renderSection('content') ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
