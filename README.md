
# Hijab Shop — Simple E‑Commerce (HTML, PHP, CSS)

Proyek ini adalah aplikasi e‑commerce sederhana untuk jualan hijab, menggunakan **PHP (tanpa framework)**, **MySQL**, **HTML**, dan **CSS**. Fitur utama:
- Katalog produk (list + detail)
- Keranjang belanja (session‑based)
- Checkout (simpan pesanan + item pesanan)
- Panel admin sederhana (CRUD produk + upload gambar)
- Proteksi dasar: prepared statement, simple CSRF token, validasi file upload

## Prasyarat
- PHP 8.x
- MySQL/MariaDB
- Web server (Apache/Nginx) — contoh termudah: XAMPP/Laragon
- Modul PHP: `pdo_mysql`, `fileinfo`

## Cara Instalasi
1. Buat database, lalu impor `db.sql`.
2. Edit kredensial DB di `config.php` (DB_HOST/DB_NAME/DB_USER/DB_PASS).
3. Salin semua file/folder ke direktori web server, misal `htdocs/hijab_shop`.
4. Pastikan folder `assets/` dapat ditulis web server (untuk upload gambar).
5. Buka `http://localhost/hijab_shop/` untuk halaman toko.
6. Panel Admin: `http://localhost/hijab_shop/admin/login.php`  
   - User: `admin`  
   - Password: `admin123` (ubah di file login.php atau implementasikan dari DB jika ingin)

## Struktur Proyek
- `index.php` — katalog produk
- `product.php` — detail produk
- `cart.php` — keranjang: tambah/hapus/lihat
- `checkout.php` — checkout & simpan pesanan
- `config.php` — koneksi DB + helper
- `styles.css` — gaya tampilan
- `db.sql` — skema database + contoh data
- `admin/login.php` — login admin
- `admin/products.php` — CRUD produk
- `admin/logout.php` — keluar admin

## Catatan Produksi
- Ganti kredensial admin & tambah sistem user management nyata.
- Tambahkan validasi lanjutan, rate‑limit, dan sanitasi input yang lebih ketat.
- Gunakan HTTPS.
- Tambahkan pembayaran (Midtrans/Xendit) bila diperlukan.
