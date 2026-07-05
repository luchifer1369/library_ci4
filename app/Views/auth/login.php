<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - M-Library</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f8fafc;
            overflow-x: hidden;
            position: relative;
        }

        /* Decorative background blobs */
        .blob {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0) 70%);
            border-radius: 50%;
            z-index: 0;
            pointer-events: none;
        }
        .blob-1 { top: -50px; left: -50px; }
        .blob-2 { bottom: -50px; right: -50px; }

        .auth-container {
            z-index: 10;
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .auth-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .auth-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 30px -5px rgba(0, 0, 0, 0.4), 0 15px 15px -5px rgba(0, 0, 0, 0.4);
        }

        .brand-logo {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 0.875rem;
            color: #94a3b8;
            text-align: center;
            margin-bottom: 32px;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group-custom i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1rem;
            transition: color 0.3s ease;
            z-index: 5;
        }

        .form-control-custom {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 16px 12px 48px;
            color: #f8fafc;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control-custom:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #6366f1;
            outline: none;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.18);
        }

        .form-control-custom:focus + i {
            color: #818cf8;
        }

        .btn-submit {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            color: #ffffff;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        .btn-submit:active {
            transform: translateY(1px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.875rem;
            color: #94a3b8;
        }

        .auth-footer a {
            color: #818cf8;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover {
            color: #c084fc;
            text-decoration: underline;
        }

        /* Alert Styling */
        .alert {
            border-radius: 12px;
            font-size: 0.875rem;
            border: none;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }
        .alert ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>

    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="auth-container">
        <div class="auth-card">
            
            <div class="brand-logo">M-Library</div>
            <div class="brand-subtitle">Akses Ribuan Buku & Kembangkan Dirimu</div>

            <!-- Flash messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    <div><?= session()->getFlashdata('success') ?></div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    <div><?= session()->getFlashdata('error') ?></div>
                </div>
            <?php endif; ?>

            <!-- Validation errors -->
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i><strong>Ada beberapa kesalahan:</strong>
                    <ul class="mt-1">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group-custom">
                        <input type="email" name="email" id="email" class="form-control-custom" placeholder="contoh@email.com" value="<?= old('email') ?>" required>
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group-custom">
                        <input type="password" name="password" id="password" class="form-control-custom" placeholder="Masukkan password Anda" required>
                        <i class="fa-solid fa-lock"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Masuk <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
                </button>
            </form>

            <div class="auth-footer">
                Belum punya akun? <a href="<?= base_url('register') ?>">Daftar Sekarang</a>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
