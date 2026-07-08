# M-Library 📚

M-Library adalah aplikasi perpustakaan digital inovatif berbasis web yang dibangun menggunakan **CodeIgniter 4**. Aplikasi ini tidak hanya menyediakan akses membaca buku, tetapi juga mengimplementasikan sistem gamifikasi (Poin & Misi Harian) serta teknologi konversi dokumen PDF ke Gambar (menggunakan Imagick) untuk menghemat *bandwidth* pengguna.

## 🌟 Fitur Utama

- **Mode Baca Hemat Kuota (Viewport):** Dokumen PDF dikonversi menjadi gambar (PNG) per halaman secara otomatis di sisi *server*, sehingga pengguna tidak perlu mengunduh file PDF secara utuh.
- **Sistem Autentikasi & Role:** Pemisahan hak akses antara `Admin` dan `User` (Normal & Premium).
- **Gamifikasi & Poin:** - *Daily Login & Quest:* Misi harian untuk mendapatkan poin gratis.
  - *Pay-per-page:* Pengguna memotong poin (-1 Poin) untuk membuka halaman buku berbayar.
- **Sistem Transaksi (Monetisasi):** Simulasi *Top Up* Poin dan pembelian status berlangganan Premium (akses baca tanpa batas).
- **Admin Dashboard:** Manajemen buku (CRUD), batas halaman gratis (*free page*), dan analitik.

## 🗄️ Struktur Database & Tabel

Aplikasi ini menggunakan database MySQL/MariaDB dengan nama (standar) `m_library_db`. Berikut adalah struktur tabel utama yang menyusun sistem:

1. `users` : Menyimpan data autentikasi, status premium, dan saldo poin pengguna.
2. `categories` : Menyimpan daftar kategori/genre buku.
3. `books` : Menyimpan meta-data buku, lokasi file PDF asli, dan batas halaman gratis.
4. `book_pages` : Menyimpan *path* gambar hasil potongan dari tiap halaman buku PDF.
5. `favorites` : Menyimpan data buku favorit pengguna.
6. `reading_history` : Mencatat riwayat bacaan terakhir per pengguna.
7. `unlocked_pages` : Mencatat halaman berbayar yang sudah dibuka oleh User Normal.
8. `daily_quests` : Melacak pencapaian dan klaim misi harian.
9. `notifications` : Sistem kotak masuk/pemberitahuan untuk pengguna.
10. `transactions` : Mencatat riwayat *Top Up* poin atau pembelian layanan.

## 🔐 Akun Dummy (Testing)

Anda dapat menggunakan kredensial berikut untuk menguji coba fitur aplikasi di berbagai *role* (Pastikan Anda telah menjalankan Seeder database):

| Role | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@mlibrary.com` | `password123` | Akses penuh ke dashboard admin, kelola buku & analitik. |
| **User Normal**| `user@mlibrary.com` | `password123` | User standar, butuh poin untuk membaca halaman berbayar. |
| **User Premium**| `premium@mlibrary.com` | `password123` | User berlangganan, akses seluruh halaman tanpa potong poin. |

## 🚀 Instalasi & Persiapan

1. *Clone* repositori ini.
2. Jalankan `composer update` atau `composer install`.
3. Salin file `env` menjadi `.env` dan atur konfigurasi database Anda (hapus tanda `#` pada bagian database).
4. Jalankan migrasi tabel: `php spark migrate`
5. Masukkan data dummy (*Seeder*): `php spark db:seed UserSeeder`
6. Jalankan server lokal: `php spark serve`