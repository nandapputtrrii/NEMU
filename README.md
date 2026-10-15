# NEMU
Platform Lost & Found khusus mahasiswa UMM.

## Persyaratan
- PHP 8.x
- Node.js dan npm
- Browser modern

## Langkah menjalankan project

1. Buka terminal di folder proyek.
2. Install dependency Node.js:

```sh
npm install
```

3. Jalankan JSON Server untuk membaca data dari `db.json`:

```sh
npx json-server db.json --watch --port 3000
```

4. Jalankan server PHP untuk halaman utama:

```sh
php -S localhost:8000
```

5. Buka browser dan akses:

- `http://localhost:8000` untuk halaman utama (`index.php`)
- `http://localhost:8000/pages/nemu.html` untuk halaman pencarian dan pelaporan barang

## Catatan
- Data barang disimpan di `db.json`.
- API yang digunakan oleh frontend berjalan di `http://localhost:3000/barang`.
- Pastikan kedua server berjalan bersamaan agar fitur laporan dan pencarian dapat berfungsi dengan benar.
