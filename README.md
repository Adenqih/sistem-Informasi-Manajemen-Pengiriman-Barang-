# ShipTrack - Sistem Informasi Manajemen Pengiriman Barang (Simple)

## Isi paket
- `init_db.sql` : SQL untuk membuat database dan tabel.
- `conec.php` : Koneksi database (sesuaikan user/password jika perlu).
- `index.php`  : Aplikasi utama (CRUD pengiriman).
- `style.css`  : Styling sederhana.

## Cara menjalankan (di lokal)
1. Install XAMPP / MAMP / LAMP.
2. Letakkan semua file dalam folder `htdocs/shiptrack` (untuk XAMPP).
3. Import `init_db.sql` ke MySQL (gunakan phpMyAdmin atau `mysql < init_db.sql`).
4. Pastikan `conec.php` sesuai dengan user/password MySQL Anda (default: root tanpa password pada XAMPP).
5. Buka `http://localhost/shiptrack/index.php`.

## Catatan
- Ini adalah contoh sederhana. Untuk produksi, tambahkan validasi, autentikasi, sanitasi yang lebih kuat, dan HTTPS.
- Jika ingin saya tambahkan fitur: login admin, export CSV, atau integrasi pelacakan otomatis, bilang saja.
