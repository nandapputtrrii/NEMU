// Task 01: Select & Change DOM pada Platform NEMU UMM

// 1. Mengubah teks judul utama dan sub-judul pada section beranda
const judulUtama = document.querySelector("#beranda h1");
if (judulUtama) {
    judulUtama.textContent = "NEMU UMM";
}

const subJudul = document.querySelector("#beranda h2");
if (subJudul) {
    subJudul.textContent = "Pusat Lost & Found Kampus UMM";
}

// 2. Mengubah teks deskripsi beranda
const deskripsiBeranda = document.querySelector("#beranda p");
if (deskripsiBeranda) {
    deskripsiBeranda.textContent = "Tempat resmi mahasiswa UMM melapor dan mencari barang hilang di area kampus.";
}

// 3. Mengubah teks tombol aksi di section beranda
const tombolLapor = document.querySelector('#beranda a[href="#lapor"]');
if (tombolLapor) {
    tombolLapor.textContent = "Buat Laporan Baru";
}

const tombolCari = document.querySelector('#beranda a[href="#cari"]');
if (tombolCari) {
    tombolCari.textContent = "Cari Barang Sekarang";
}

// 4. Mengubah teks placeholder pada form pencarian secara dinamis
const inputCari = document.querySelector("#search");
if (inputCari) {
    inputCari.placeholder = "Cari: KTM, kunci motor, helm, tumbler...";
}

const inputLokasi = document.querySelector("#lokasi");
if (inputLokasi) {
    inputLokasi.placeholder = "Contoh: Perpustakaan Pusat, GKB 1, Masjid AR Fachruddin";
}