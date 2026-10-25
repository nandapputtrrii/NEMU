<?php
$viewData = isset($viewData) && is_array($viewData) ? $viewData : [];
$kataKunci = (string) ($viewData['kataKunci'] ?? '');
$kategoriFilter = (string) ($viewData['kategoriFilter'] ?? '');
$statusFilter = (string) ($viewData['statusFilter'] ?? '');
$barangDitemukan = is_array($viewData['barangDitemukan'] ?? null) ? $viewData['barangDitemukan'] : [];
$hasilPencarian = is_array($viewData['hasilPencarian'] ?? null) ? $viewData['hasilPencarian'] : [];
$kategoriTersedia = is_array($viewData['kategoriTersedia'] ?? null) ? $viewData['kategoriTersedia'] : [];
$ringkasanLaporan = is_array($viewData['ringkasanLaporan'] ?? null) ? $viewData['ringkasanLaporan'] : [];
$dataError = $viewData['dataError'] ?? null;

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="NEMU membantu mahasiswa UMM melaporkan dan menemukan kembali barang yang hilang di kampus.">
    <title>NEMU UMM - Barangmu mungkin sudah ada yang nemu</title>
    <link rel="icon" type="image/png" href="assets/Bruh-removebg-preview.png">
    <style>
        :root { color-scheme: light; --ink: #171717; --paper: #f7f5ef; --red: #c8102e; --gold: #f4c542; --muted: #66645f; }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; background: var(--paper); color: var(--ink); font-family: Arial, Helvetica, sans-serif; line-height: 1.6; }
        a { color: inherit; }
        .site-header { position: sticky; top: 0; z-index: 2; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .8rem max(5vw, calc((100vw - 1100px) / 2)); border-bottom: 2px solid var(--ink); background: rgb(247 245 239 / 95%); }
        .brand { display: inline-flex; align-items: center; gap: .65rem; text-decoration: none; }
        .brand img { width: 2.5rem; height: 2.5rem; object-fit: contain; }
        .brand strong { font-size: 1.5rem; letter-spacing: -.06em; }
        nav { display: flex; flex-wrap: wrap; gap: 1.5rem; }
        nav a { font-size: .92rem; font-weight: 700; text-decoration: none; }
        nav a:hover, nav a:focus-visible { color: var(--red); }
        main { width: min(1100px, 90%); margin: auto; }
        section { padding: 4.5rem 0; scroll-margin-top: 5rem; }
        .hero { max-width: 850px; margin: auto; padding: 6.5rem 0; text-align: center; }
        .report-summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; padding: 0 0 2rem; }
        .summary-card { padding: 1.2rem; border: 2px solid var(--ink); border-radius: .8rem; background: #fff; }
        .summary-card strong { display: block; font-size: 2rem; line-height: 1.1; }
        .summary-card span { color: var(--muted); font-size: .9rem; }
        .eyebrow { color: var(--red); font-family: monospace; font-size: .9rem; }
        h1, h2, h3, p { margin-top: 0; }
        h1 { margin: .8rem 0 1rem; font-size: clamp(2.7rem, 8vw, 5.2rem); line-height: .98; letter-spacing: -.06em; }
        h2 { margin-bottom: .4rem; font-size: clamp(2rem, 5vw, 3rem); line-height: 1.1; letter-spacing: -.04em; }
        .hero-copy { max-width: 620px; margin: 1.3rem auto 0; color: var(--muted); font-size: 1.1rem; }
        .actions { display: flex; justify-content: center; flex-wrap: wrap; gap: .75rem; margin-top: 1.8rem; }
        .button { display: inline-block; padding: .75rem 1.1rem; border: 2px solid var(--ink); border-radius: .6rem; background: var(--red); color: #fff; font-weight: 700; text-decoration: none; }
        .button.secondary { background: transparent; color: var(--ink); }
        .button.gold { background: var(--gold); color: var(--ink); }
        .section-heading { display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--ink); }
        .section-heading p { margin-bottom: .3rem; }
        .item-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .item-card { min-width: 0; padding: 1.2rem; border: 2px solid var(--ink); border-radius: .8rem; background: #fff; }
        .item-category { margin-bottom: .25rem; color: var(--red); font: .8rem monospace; text-transform: uppercase; }
        .item-status { font-weight: 700; }
        .item-status.found { color: #187044; }
        .item-status.lost { color: #a20d27; }
        .item-card h3 { margin-bottom: .5rem; font-size: 1.45rem; }
        .item-description { min-height: 3rem; margin-bottom: 1rem; color: var(--muted); }
        .item-meta { display: grid; gap: .25rem; padding-top: .8rem; border-top: 1px dashed #aaa; color: var(--muted); font: .78rem monospace; }
        .empty-state, .error-state { grid-column: 1 / -1; padding: 1.2rem; border: 1px dashed #999; border-radius: .7rem; text-align: center; color: var(--muted); }
        .error-state { border-color: var(--red); color: #8a0e23; }
        .search-panel { padding: clamp(1.3rem, 5vw, 3rem); border-radius: 1rem; background: var(--ink); color: #fff; }
        .search-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center; }
        .search-panel .eyebrow { color: var(--gold); }
        .search-panel p { color: #d0cfcc; }
        .search-form { display: grid; gap: .8rem; }
        .search-form label { display: grid; gap: .35rem; font-weight: 700; }
        .search-form input, .search-form select { width: 100%; padding: .8rem; border: 2px solid #bbb; border-radius: .5rem; background: #fff; color: var(--ink); font: inherit; }
        .search-form button { padding: .8rem; border: 0; border-radius: .5rem; background: var(--red); color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
        .item-list-heading { margin: 1.5rem 0 .8rem; }
        .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin: 2rem 0 0; padding: 0; list-style: none; }
        .steps li { padding-top: .8rem; border-top: 2px solid var(--ink); }
        .steps p:last-child { margin-bottom: 0; color: var(--muted); }
        footer { padding: 2rem 5%; background: var(--ink); color: #fff; }
        footer > div { width: min(1100px, 100%); margin: auto; }
        footer p { margin: .25rem 0; }
        footer .muted { color: #bbb; font-size: .9rem; }
        @media (max-width: 700px) {
            .site-header { align-items: flex-start; flex-direction: column; padding: .7rem 5%; }
            nav { gap: .5rem 1rem; }
            section { padding: 3.5rem 0; }
            .hero { padding: 4rem 0; }
            .item-grid, .search-layout, .steps, .report-summary { grid-template-columns: 1fr; }
            .section-heading { align-items: flex-start; flex-direction: column; }
        }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } }
    </style>
</head>
<body>
    <header class="site-header">
        <a class="brand" href="#beranda">
            <img src="assets/Bruh-removebg-preview.png" alt="">
            <strong>NEMU</strong>
        </a>
        <nav aria-label="Menu utama">
            <a href="#showcase">Barang ditemukan</a>
            <a href="pages/lapor-hilang.html">Lapor kehilangan</a>
            <a href="pages/lapor-temuan.html">Lapor temuan</a>
            <a href="#tentang">Cara kerja</a>
        </nav>
    </header>

    <main>
        <section id="beranda" class="hero">
            <p class="eyebrow">Lost &amp; found mahasiswa UMM</p>
            <h1>Barangmu hilang? Mungkin sudah ada yang nemu.</h1>
            <p class="hero-copy">NEMU membantu mahasiswa UMM melapor, mencari, dan mengambil lagi barang yang hilang di kampus.</p>
            <div class="actions">
                <a class="button" href="#cari">Cari barang</a>
                <a class="button secondary" href="pages/lapor-hilang.html">Lapor kehilangan</a>
                <a class="button gold" href="pages/lapor-temuan.html">Lapor temuan</a>
            </div>
        </section>

        <section class="report-summary" aria-label="Ringkasan laporan">
            <?php foreach ($ringkasanLaporan as $label => $jumlah): ?>
                <article class="summary-card">
                    <strong><?= e($dataError === null ? $jumlah : '-') ?></strong>
                    <span><?= e($label) ?></span>
                </article>
            <?php endforeach; ?>
        </section>

        <section id="showcase">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Barang ditemukan</p>
                    <h2>Ada yang kamu kenal?</h2>
                </div>
                <a href="#cari">Lihat semua laporan</a>
            </div>
            <div class="item-grid">
                <?php if ($dataError !== null): ?>
                    <p class="error-state"><?= e($dataError) ?></p>
                <?php elseif ($barangDitemukan === []): ?>
                    <p class="empty-state">Belum ada barang yang ditemukan.</p>
                <?php else: ?>
                    <?php foreach (array_slice($barangDitemukan, 0, 3) as $barang): ?>
                        <?php require __DIR__ . '/partials/item-card.php'; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section id="cari">
            <div class="search-panel">
                <div class="search-layout">
                    <div>
                        <p class="eyebrow">Cari barang</p>
                        <h2>Coba ketik apa yang hilang.</h2>
                        <p>Gunakan pencarian untuk melihat laporan barang hilang maupun ditemukan.</p>
                    </div>
                    <form action="index.php#cari" method="get" class="search-form">
                        <label for="q">Nama, lokasi, atau deskripsi
                            <input id="q" name="q" type="search" value="<?= e($kataKunci) ?>" placeholder="Contoh: dompet, charger, KTM">
                        </label>
                        <label for="kategori">Kategori
                            <select id="kategori" name="kategori">
                                <option value="">Semua kategori</option>
                                <?php foreach ($kategoriTersedia as $kategori): ?>
                                    <option value="<?= e($kategori) ?>" <?= strcasecmp($kategoriFilter, $kategori) === 0 ? 'selected' : '' ?>>
                                        <?= e($kategori) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label for="status">Status laporan
                            <select id="status" name="status">
                                <option value="">Semua status</option>
                                <option value="Hilang" <?= strcasecmp($statusFilter, 'Hilang') === 0 ? 'selected' : '' ?>>Hilang</option>
                                <option value="Ditemukan" <?= strcasecmp($statusFilter, 'Ditemukan') === 0 ? 'selected' : '' ?>>Ditemukan</option>
                            </select>
                        </label>
                        <button type="submit">Cari barang</button>
                    </form>
                </div>
                <h3 class="item-list-heading">Daftar laporan (<?= count($hasilPencarian) ?>)</h3>
                <div class="item-grid" aria-live="polite">
                    <?php if ($dataError !== null): ?>
                        <p class="error-state"><?= e($dataError) ?></p>
                    <?php elseif ($hasilPencarian === []): ?>
                        <p class="empty-state">Tidak ada laporan yang cocok dengan pencarianmu.</p>
                    <?php else: ?>
                        <?php foreach ($hasilPencarian as $barang): ?>
                            <?php require __DIR__ . '/partials/item-card.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section id="tentang">
            <p class="eyebrow">Cara kerja</p>
            <h2>Tiga langkah, selesai.</h2>
            <ol class="steps">
                <li>
                    <p class="eyebrow">01</p>
                    <h3>Laporkan</h3>
                    <p>Kehilangan sesuatu atau menemukan barang orang lain? Isi formulir singkat berisi barang, lokasi, dan tanggal.</p>
                </li>
                <li>
                    <p class="eyebrow">02</p>
                    <h3>Cari</h3>
                    <p>Periksa daftar laporan barang yang ditemukan dan saring berdasarkan nama atau kategori.</p>
                </li>
                <li>
                    <p class="eyebrow">03</p>
                    <h3>Ambil kembali</h3>
                    <p>Hubungi pelapor melalui informasi kontak pada laporan untuk mengatur pengambilan barang.</p>
                </li>
            </ol>
        </section>
    </main>

    <footer>
        <div>
            <p><strong>NEMU</strong></p>
            <p class="muted">&copy; <?= date('Y') ?> NEMU. Semoga barangmu cepat ketemu.</p>
        </div>
    </footer>
</body>
</html>
