# Rencana Spesifikasi Fitur Aplikasi M-Library

**AKTOR SISTEM:**
1. Admin
2. User (Terbagi menjadi 2: Normal dan Premium)

---

## 1. HALAMAN AUTENTIKASI (VIEW)

*   **Login:**
    *   Email
    *   Password
*   **Register:**
    *   Nama Lengkap
    *   Email
    *   Password

---

## 2. CORE LOGIC & GAMIFICATION

*   **Hak Akses Premium:** User dengan status Premium dapat mengakses dan membaca seluruh halaman buku (full access) tanpa dibatasi dan tanpa memotong poin.
*   **Premium Trial:** Pembelian/pendaftaran akun premium pertama kali mendapatkan Free Trial selama 3 hari.
*   **Daily Login Rewards:** Mendapatkan 10 poin setiap kali login pertama di hari tersebut.
*   **Daily Quest (Manual Claim System):** User harus menyelesaikan syarat quest, kemudian menekan tombol "Claim" di Dashboard untuk mendapatkan poin.
    *   **Quest 1:** Tambah 1 buku ke daftar favorit (Reward: 10 Poin).
    *   **Quest 2:** Baca 5 halaman, dihitung dari klik tombol "Next" (Reward: 10 Poin).
    *   **Quest 3:** Baca 15 halaman, dihitung dari klik tombol "Next" (Reward: 20 Poin).

---

## 3. DASHBOARD USER

*   **Tampilan Saldo Poin & Top Up:** Di bagian atas navigasi, tampilkan jumlah saldo poin user saat ini. Di sebelahnya terdapat tombol dengan ikon plus (+). Jika diklik, user akan diarahkan langsung ke halaman Top Up Poin.
*   **Inbox / Notification:** Fitur kotak masuk (Dropdown/Ikon Lonceng) untuk menerima pesan sistem, seperti:
    *   Notifikasi sukses beli/klaim Premium.
    *   Pengingat sisa masa aktif Premium.
    *   Notifikasi transaksi Top Up Poin berhasil.
*   **Panel Daily Quest:** Tampil di halaman utama Dashboard. Menampilkan daftar quest harian beserta progress-nya (Contoh: "Baca 5 halaman (2/5)"). Terdapat tombol "Claim" di sebelah setiap quest.
    *   *Logika Tombol:* Tombol berwarna abu-abu (Disabled) jika belum selesai. Jika sudah selesai, tombol menjadi aktif (Bisa diklik). Jika sudah diklik, tombol berubah menjadi "Claimed".
*   **Grid Buku:** Menampilkan daftar buku dalam bentuk grid (dapat diklik untuk masuk ke Detail Buku).
*   **Search:** Fitur pencarian berdasarkan nama/judul buku.
*   **Menu Navigasi Tambahan:**
    *   **Daily Login Rewards:** Menu khusus untuk mengklaim hadiah login (mendapatkan 10 poin setiap hari).
    *   **History:** List riwayat buku yang sudah dibaca.
    *   **Favorite:** List buku yang ditandai sebagai favorit.
    *   **Profile:** Halaman profil user yang berisi informasi akun dan menu transaksi (Klaim Pembelian Premium Free Trial dan Pembelian Premium 30 Hari).
*   **Kategori Buku:**
    1. Fiksi & Novel (Fiction)
    2. Pengembangan Diri (Self-Improvement)
    3. Bisnis & Keuangan (Business & Finance)
    4. Komik & Novel Grafis (Comics & Graphic Novels)
    5. Biografi & Autobiografi (Biography)
    6. Buku Anak & Edukasi (Children & Educational)

---

## 4. DETAIL BUKU & MODE BACA

### Halaman Detail Buku
*   **Informasi Buku:** Sinopsis dan detail buku.
*   **Button Action:** Tombol "Favorite" dan Tombol "Mulai Baca".
*   **Grid Halaman:** Menampilkan daftar page per halaman di bagian bawah dengan fitur/tombol [Load More].

### Mode Baca / Viewport
*   **Trigger:** Diakses dengan menekan tombol "Mulai Baca" atau klik salah satu page pada grid halaman.
*   **Tampilan:** Hanya memuat 1 gambar penuh per halaman (untuk menghemat token/bandwidth).
*   **Navigasi (Posisi di bawah gambar):**
    *   **Kiri:** Previous Page (Halaman Sebelumnya).
    *   **Tengah:** Minimize / Back to Detail (Keluar dari mode baca).
    *   **Kanan:** Next Page (Halaman Selanjutnya).

---

## 5. SISTEM MONETISASI, TOP UP, & IMAGICK (CORE ENGINE)

*   **Konversi File:** Menggunakan library Imagick untuk mengubah file PDF yang diupload menjadi format gambar PNG per halaman.
*   **Sistem Poin (Token):**
    *   **User Normal:** Harga akses adalah 1 Poin per 1 Halaman (berlaku setelah rentang halaman gratis habis).
    *   **User Premium:** Gratis akses ke semua halaman (tidak menggunakan poin).
    *   **Auto-Deduct (Potong Poin Otomatis):** Bagi User Normal, saat masuk ke halaman berbayar pertama, akan muncul pop-up peringatan: *"Halaman selanjutnya berbayar. Apakah Anda ingin mengaktifkan potong poin otomatis?"* Jika setuju, poin akan dipotong otomatis di latar belakang saat menekan "Next" tanpa peringatan lagi, sampai poin habis.

### Fitur Top Up Poin (Prototype Mode)
*   **Akses:** Melalui tombol "Plus (+)" di sebelah indikator saldo poin pada navigasi atas Dashboard.
*   **Tampilan UI:** Berupa halaman atau modal berisi pilihan paket poin dalam bentuk Card, lengkap dengan visual metode pembayaran (simulasi/dummy logo GoPay, QRIS, DANA, dll).
*   **Struktur Harga (Pricing Tier):**
    *   **Paket Pemula:** 50 Poin (Rp 5.000)
    *   **Paket Reguler:** 160 Poin (Rp 15.000) - Termasuk Bonus 10 Poin
    *   **Paket Kutu Buku:** 330 Poin (Rp 30.000) - Termasuk Bonus 30 Poin (Best Value)
    *   **Paket Sultan:** 575 Poin (Rp 50.000) - Termasuk Bonus 75 Poin
*   **Logika Pembayaran:** Saat user mengklik tombol "Beli Sekarang", sistem akan langsung memunculkan notifikasi sukses SweetAlert (simulasi), menambahkan jumlah poin ke saldo akun, dan mengirimkan riwayat pembelian ke Inbox/Notifikasi User.

---

## 6. DASHBOARD ADMIN

*   **Manajemen Buku (Tambah/Edit):**
    *   Judul
    *   Deskripsi
    *   Kategori
    *   Cover (Format PNG)
    *   File Buku (Format PDF)
*   **Pengaturan Halaman Gratis:** Admin menentukan rentang halaman yang bisa dibaca gratis untuk User Normal (Contoh: Input dari kolom 1 sampai kolom 10).
*   **Laporan & Statistik:**
    *   Grafik jumlah pengunjung.
    *   Daftar buku paling populer.