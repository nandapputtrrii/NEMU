<?php
$nama = trim((string) ($barang['nama'] ?? 'Barang tanpa nama'));
$kategori = trim((string) ($barang['kategori'] ?? 'Lainnya'));
$deskripsi = trim((string) ($barang['deskripsi'] ?? 'Tidak ada deskripsi.'));
$lokasi = trim((string) ($barang['lokasi'] ?? 'Tidak diketahui'));
$tanggal = trim((string) ($barang['tanggal'] ?? 'Tidak diketahui'));
$status = trim((string) ($barang['status'] ?? 'Status tidak diketahui'));
$statusClass = strtolower($status) === 'ditemukan' ? 'found' : 'lost';
?>
<article class="item-card" data-status="<?= e($statusClass) ?>">
    <p class="item-category"><?= e($kategori) ?> · <span class="item-status <?= e($statusClass) ?>"><?= e($status) ?></span></p>
    <h3><?= e($nama) ?></h3>
    <p class="item-description"><?= e($deskripsi) ?></p>
    <div class="item-meta">
        <span>Lokasi: <?= e($lokasi) ?></span>
        <span>Tanggal: <?= e($tanggal) ?></span>
    </div>
</article>
