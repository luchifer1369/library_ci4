# M-Library 📚

M-Library adalah aplikasi perpustakaan digital inovatif berbasis web yang dibangun menggunakan **CodeIgniter 4**. Aplikasi ini tidak hanya menyediakan akses membaca buku, tetapi juga mengimplementasikan sistem gamifikasi serta teknologi optimasi _bandwidth_ untuk kenyamanan pengguna.

---

## 🌟 Fitur Utama

- **Mode Baca Hemat Kuota (Viewport):** Dokumen PDF dikonversi menjadi gambar (PNG) per halaman secara otomatis di sisi server. Pengguna tidak perlu mengunduh file PDF secara utuh.
- **Sistem Autentikasi & Role:** Pemisahan hak akses yang jelas antara Admin, User Normal, dan User Premium.
- **Gamifikasi & Poin:**
  - _Daily Login & Quest:_ Misi harian untuk mendapatkan poin gratis.
  - _Pay-per-page:_ User Normal memotong saldo poin (-1 Poin) untuk membuka halaman buku berbayar.
- **Sistem Transaksi (Monetisasi):** Simulasi Top Up Poin dan pembelian status berlangganan Premium untuk akses baca tanpa batas.
- **Admin Dashboard:** Manajemen buku (CRUD), pengaturan batas halaman gratis (_free page_), dan analitik data.

---

## 🗄️ Struktur Database & Tabel

Aplikasi ini menggunakan database **MySQL/MariaDB** dengan nama standar `m_library_db`. Berikut adalah tabel-tabel utama penyusun sistem:

- `users` : Menyimpan data autentikasi, status premium, dan saldo koin pengguna.
- `categories` : Menyimpan daftar kategori/genre buku.
- `books` : Menyimpan meta-data buku, lokasi file PDF asli, dan batas halaman gratis.
- `book_pages` : Menyimpan path gambar hasil potongan dari tiap halaman buku PDF.
- `favorites` : Menyimpan data buku favorit pengguna.
- `reading_history` : Mencatat riwayat bacaan terakhir per pengguna.
- `unlocked_pages` : Mencatat halaman berbayar yang sudah dibuka oleh User Normal.
- `daily_quests` : Melacak pencapaian dan klaim misi harian.
- `notifications` : Sistem kotak masuk/pemberitahuan untuk pengguna.
- `transactions` : Mencatat riwayat Top Up poin atau pembelian layanan.

---

## 🔐 Akun Dummy (Testing)

Gunakan kredensial berikut untuk menguji coba fitur aplikasi di berbagai tingkatan role _(Pastikan Anda telah menjalankan Seeder database terlebih dahulu)_:

| Role             | Email                  | Password      | Keterangan                                                  |
| :--------------- | :--------------------- | :------------ | :---------------------------------------------------------- |
| **Admin**        | `admin@mlibrary.com`   | `password123` | Akses penuh ke dashboard, kelola buku & analitik.           |
| **User Normal**  | `user@mlibrary.com`    | `password123` | User standar, butuh poin untuk membuka halaman berbayar.    |
| **User Premium** | `premium@mlibrary.com` | `password123` | User berlangganan, akses seluruh halaman tanpa potong poin. |

---

## 🚀 Instalasi & Persiapan

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di lingkungan lokal Anda:

### 1. Clone Repositori

Unduh kode sumber aplikasi ke direktori lokal Anda:

```bash
git clone <url-repositori-m-library>
cd m-library
```

### 2. Install Dependency (Composer)

Unduh semua dependensi pustaka PHP yang dibutuhkan oleh CodeIgniter 4:

```bash
composer update
```

### 3. Konfigurasi File Environment (.env)

Salin file bawaan `env` menjadi `.env`:

```bash
cp env .env
```

Buka file `.env` menggunakan teks editor Anda, lalu lakukan konfigurasi berikut:

- **Mengatur URL Aplikasi:** Cari baris `app.baseURL` dan ubah menjadi:
  ```env
  app.baseURL = 'http://localhost:8080/'
  ```
- **Mengatur Database:** Hapus tanda pagar (`#`) di depan konfigurasi database, lalu sesuaikan dengan database lokal Anda:
  ```env
  database.default.hostname = localhost
  database.default.database = m_library_db
  database.default.username = root
  database.default.password =
  database.default.DBDriver = MySQLi
  database.default.port = 3306
  ```

### 4. Jalankan Migrasi Database

Buat seluruh struktur tabel yang diperlukan di dalam database Anda:

```bash
php spark migrate
```

### 5. Pengisian Data Dummy (Database Seeding)

Aplikasi ini menyediakan 3 file _Seeder_ utama:

1. `CategorySeeder.php` (Data master kategori buku)
2. `UserSeeder.php` (Data akun testing untuk Admin, Normal, & Premium)
3. `DatabaseSeeder.php` (Master seeder yang otomatis menjalankan semua seeder di atas)

Jalankan perintah berikut untuk mengisi seluruh data dummy secara otomatis:

```bash
php spark db:seed DatabaseSeeder
```

### 6. Jalankan Server Lokal

Nyalakan server bawaan CodeIgniter 4:

```bash
php spark serve
```

Setelah aktif, buka browser Anda dan akses aplikasi melalui tautan **[http://localhost:8080](http://localhost:8080)**.
