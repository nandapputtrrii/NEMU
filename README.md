# NEMU

NEMU adalah platform Lost & Found untuk mahasiswa UMM. Aplikasi Laravel ini menyediakan halaman pencarian barang dan fitur CRUD laporan barang dengan database SQLite.

## Persyaratan

- PHP 8.3 atau lebih baru
- Composer
- Ekstensi PHP SQLite (`pdo_sqlite` dan `sqlite3`)

## Menjalankan aplikasi

Dari direktori root project, jalankan:

```sh
composer install
test -f .env || cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```

Pastikan koneksi database di `.env` menggunakan:

```dotenv
DB_CONNECTION=sqlite
```

Buka `http://127.0.0.1:8000`.

- `/` — halaman utama NEMU dengan ringkasan, pencarian, filter, dan laporan terbaru.
- `/lapor-hilang` — formulir laporan barang hilang.
- `/lapor-temuan` — formulir laporan barang ditemukan.
- `/laporan` — daftar laporan dengan aksi edit dan hapus.

Data laporan lama tidak disertakan di Git karena berisi informasi kontak pelapor. Untuk memindahkan salinan lokal `legacy/db.json` ke SQLite, jalankan `php artisan db:seed --class=LegacyBarangSeeder`. Importer mempertahankan ID sumber dan aman dijalankan ulang.

## Struktur data

Model `App\Models\Barang` menyimpan nama, kategori, lokasi, tanggal, jam, deskripsi, status, identitas kontak pelapor, foto, dan lokasi penyimpanan. Migration tabel `barangs` dijalankan dengan:

```sh
php artisan migrate
```

Foto baru disimpan pada disk publik Laravel. Setelah menjalankan `php artisan storage:link`, file dapat ditampilkan melalui halaman NEMU.

## Test

```sh
php artisan test
```

Test mencakup operasi Eloquent `create`, `all`, dan `find`, filter laporan, formulir laporan hilang/temuan, validasi, serta siklus tambah-edit-hapus.

## Kode NEMU versi sebelumnya

Kode PHP dan JSON Server sebelumnya tetap disimpan di `legacy/` sebagai arsip. Aplikasi aktif di root project menggunakan Laravel.
