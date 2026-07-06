<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'M-Library') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; }
        .navbar-custom { background: rgba(30,41,59,0.95); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.08); }
        .navbar-custom .nav-link { color: #94a3b8; }
        .navbar-custom .nav-link:hover, .navbar-custom .nav-link.active { color: #fff; }
        .points-badge { background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 20px; padding: 6px 14px; font-weight: 600; font-size: 0.875rem; }
        .btn-topup { width: 32px; height: 32px; border-radius: 50%; background: #22c55e; border: none; color: #fff; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .btn-topup:hover { background: #16a34a; color: #fff; }
        .book-card { background: rgba(30,41,59,0.8); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; overflow: hidden; transition: transform .2s, box-shadow .2s; height: 100%; }
        .book-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.3); }
        .book-card img { width: 100%; height: 220px; object-fit: cover; }
        .quest-panel { background: rgba(30,41,59,0.8); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; }
        .notification-dropdown {
            width: 340px;
            max-height: 400px;
            overflow-y: auto;
            background: #1e293b !important;
            color: #e2e8f0 !important;
            --bs-dropdown-link-color: #e2e8f0;
            --bs-dropdown-link-hover-color: #fff;
            --bs-dropdown-link-hover-bg: rgba(99, 102, 241, 0.15);
        }
        .notification-dropdown strong,
        .notification-dropdown .notif-header {
            color: #f8fafc !important;
        }
        .notification-item {
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 12px 16px;
            color: #e2e8f0 !important;
        }
        .notification-item:hover {
            background: rgba(99, 102, 241, 0.12) !important;
        }
        .notification-item.unread { background: rgba(99, 102, 241, 0.18); }
        .notification-item .notif-title { color: #f8fafc; font-weight: 600; }
        .notification-item .notif-message { color: #cbd5e1; }
        .notification-item .notif-date { color: #94a3b8; }
        .premium-badge { background: linear-gradient(135deg, #f59e0b, #ef4444); font-size: 0.7rem; padding: 2px 8px; border-radius: 10px; }
        /* Teks sekunder di tema gelap — Bootstrap text-muted terlalu gelap */
        main .text-muted { color: #94a3b8 !important; }
        main h1, main h2, main h3, main h4, main h5, main h6 { color: #f8fafc; }
        .book-synopsis {
            color: #cbd5e1;
            font-size: 1rem;
            line-height: 1.75;
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }
        .table-dark-theme { color: #e2e8f0; }
        .table-dark-theme td { color: #e2e8f0; vertical-align: middle; }
        .table-dark-theme td.text-muted { color: #94a3b8 !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-white" href="<?= base_url('dashboard') ?>"><i class="fa-solid fa-book-open-reader me-2 text-primary"></i>M-Library</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto ms-lg-3">
                    <li class="nav-item"><a class="nav-link <?= uri_string() === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link <?= uri_string() === 'favorites' ? 'active' : '' ?>" href="<?= base_url('favorites') ?>">Favorit</a></li>
                    <li class="nav-item"><a class="nav-link <?= uri_string() === 'history' ? 'active' : '' ?>" href="<?= base_url('history') ?>">History</a></li>
                    <li class="nav-item"><a class="nav-link <?= uri_string() === 'profile' ? 'active' : '' ?>" href="<?= base_url('profile') ?>">Profil</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="points-badge"><i class="fa-solid fa-coins me-1"></i><?= $user['poin'] ?? 0 ?> Poin</span>
                        <a href="<?= base_url('topup') ?>" class="btn-topup" title="Top Up"><i class="fa-solid fa-plus"></i></a>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-white position-relative p-0" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bell fa-lg"></i>
                            <?php if (($unreadCount ?? 0) > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown border-secondary shadow-lg">
                            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom border-secondary notif-header">
                                <strong>Notifikasi</strong>
                                <?php if (($unreadCount ?? 0) > 0): ?>
                                    <a href="<?= base_url('notifications/read-all') ?>" class="small text-info text-decoration-none">Tandai semua dibaca</a>
                                <?php endif; ?>
                            </div>
                            <?php if (empty($notifications)): ?>
                                <div class="text-center py-4 small" style="color:#94a3b8;">Tidak ada notifikasi</div>
                            <?php else: ?>
                                <?php foreach ($notifications as $notif): ?>
                                    <a href="<?= base_url('notifications/read/' . $notif['id']) ?>" class="notification-item d-block text-decoration-none <?= !$notif['is_read'] ? 'unread' : '' ?>">
                                        <div class="notif-title small"><?= esc($notif['title']) ?></div>
                                        <div class="notif-message small"><?= esc($notif['message']) ?></div>
                                        <div class="notif-date small mt-1"><?= date('d M Y H:i', strtotime($notif['created_at'])) ?></div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="text-muted small d-none d-md-inline"><?= esc(session()->get('nama_lengkap')) ?></span>
                    <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-light">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?= $this->renderSection('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
