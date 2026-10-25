# NEMU
NEMU adalah platform Lost & Found khusus mahasiswa UMM untuk membantu pengguna melaporkan barang hilang atau barang yang ditemukan, serta mencari data laporan dengan cepat.

## Fitur utama
- Pencarian laporan barang berdasarkan kata kunci
- Filter berdasarkan kategori dan status (`Hilang` / `Ditemukan`)
- Ringkasan laporan secara real-time
- Tampilan daftar barang berbasis MVC di PHP
- Halaman penunjang untuk login dan pelaporan barang

## Stack yang digunakan
- PHP 8.x
- Node.js dan npm
- JSON Server
- Tailwind CSS

## Struktur proyek
- `index.php` — entry point aplikasi PHP MVC
- `app/Models/BarangModel.php` — membaca data barang dari `db.json`
- `app/Controllers/HomeController.php` — menangani logika pencarian, filter, dan ringkasan
- `app/Views/home.php` — halaman utama aplikasi
- `app/Views/partials/item-card.php` — komponen kartu item laporan
- `pages/` — halaman statis seperti login, pencarian, dan formulir pelaporan
- `db.json` — database lokal berbasis JSON

## Persyaratan
- PHP 8.x terinstall di sistem
- Node.js dan npm terinstall
- Browser modern

## Cara menjalankan project
1. Buka terminal di folder proyek.
2. Install dependency Node.js:

```sh
npm install
```

3. Jalankan JSON Server untuk membaca data dari `db.json`:

```sh
npx json-server db.json --watch --port 3000
```

4. Jalankan server PHP untuk aplikasi utama:

```sh
php -S localhost:8000
```

5. Buka browser dan akses:

- `http://localhost:8000` — halaman utama aplikasi MVC (`index.php`)
- `http://localhost:8000/pages/login.html` — halaman login
- `http://localhost:8000/pages/nemu.html` — halaman pencarian dan pelaporan barang
- `http://localhost:8000/pages/lapor-hilang.html` — formulir laporan barang hilang
- `http://localhost:8000/pages/lapor-temuan.html` — formulir laporan barang ditemukan

## Catatan penting
- Data barang utama disimpan di `db.json` dengan struktur `barang`.
- API frontend yang digunakan biasanya tersedia di `http://localhost:3000/barang`.
- Pastikan JSON Server dan PHP server berjalan bersamaan agar fitur pencarian dan laporan dapat berfungsi dengan benar.

## Troubleshooting
- Jika data tidak muncul, pastikan perintah `npx json-server ...` sudah berjalan dengan benar.
- Jika halaman tidak bisa diakses, pastikan `php -S localhost:8000` dijalankan dari root proyek.
- Jika halaman static tidak dapat memuat data, cek apakah port `3000` tidak dipakai proses lain.
