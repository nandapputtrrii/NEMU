@extends('layouts.app')

@section('title', 'NEMU UMM')

@section('content')
    <section class="hero" id="beranda">
        <p class="eyebrow">Lost &amp; found mahasiswa UMM</p>
        <h1>Barangmu hilang? Mungkin sudah ada yang nemu.</h1>
        <p class="hero-copy">NEMU membantu mahasiswa UMM melapor, mencari, dan mengambil lagi barang yang hilang di kampus.</p>
        <div class="actions">
            <a class="button" href="#cari">Cari barang</a>
            <a class="button secondary" href="{{ route('laporan.lost.create') }}">Lapor kehilangan</a>
            <a class="button gold" href="{{ route('laporan.found.create') }}">Lapor temuan</a>
        </div>
    </section>

    <section class="summary" aria-label="Ringkasan laporan">
        @foreach ($ringkasanLaporan as $label => $jumlah)
            <article class="summary-card">
                <strong>{{ $jumlah }}</strong>
                <span>{{ $label }}</span>
            </article>
        @endforeach
    </section>

    <section class="section" id="showcase">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Barang ditemukan terbaru</p>
                <h2>Ada yang kamu kenal?</h2>
            </div>
            <a href="#cari">Lihat semua laporan</a>
        </div>
        <div class="item-grid">
            @forelse ($barangDitemukan as $barang)
                @include('barangs._card', ['barang' => $barang])
            @empty
                <p class="empty">Belum ada barang yang ditemukan.</p>
            @endforelse
        </div>
    </section>

    <section class="section" id="cari">
        <div class="panel search-panel">
            <div class="search-layout">
                <div>
                    <p class="eyebrow">Cari barang</p>
                    <h2>Coba ketik apa yang hilang.</h2>
                    <p class="muted">Cari berdasarkan nama, kategori, lokasi, atau deskripsi.</p>
                </div>
                <form action="{{ route('home') }}#cari" method="GET" class="search-form">
                    <div class="field">
                        <label for="q">Kata kunci</label>
                        <input id="q" name="q" type="search" value="{{ $kataKunci }}" placeholder="Contoh: dompet, charger, KTM">
                    </div>
                    <div class="field">
                        <label for="kategori">Kategori</label>
                        <select id="kategori" name="kategori">
                            <option value="">Semua kategori</option>
                            @foreach ($kategoriTersedia as $kategori)
                                <option value="{{ $kategori }}" @selected($kategoriFilter === $kategori)>{{ $kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">Semua status</option>
                            <option value="Hilang" @selected($statusFilter === 'Hilang')>Hilang</option>
                            <option value="Ditemukan" @selected($statusFilter === 'Ditemukan')>Ditemukan</option>
                        </select>
                    </div>
                    <button class="button gold" type="submit">Cari laporan</button>
                </form>
            </div>
            <h3 style="margin: 28px 0 14px">Daftar laporan ({{ $hasilPencarian->count() }})</h3>
            <div class="item-grid">
                @forelse ($hasilPencarian as $barang)
                    @include('barangs._card', ['barang' => $barang])
                @empty
                    <p class="empty">Tidak ada laporan yang cocok dengan pencarianmu.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section" id="tentang">
        <p class="eyebrow">Cara kerja</p>
        <h2>Tiga langkah, selesai.</h2>
        <ol class="steps">
            <li><p class="eyebrow">01</p><h3>Laporkan</h3><p>Kehilangan sesuatu atau menemukan barang orang lain? Isi formulir singkat berisi barang, lokasi, dan tanggal.</p></li>
            <li><p class="eyebrow">02</p><h3>Cari</h3><p>Periksa daftar laporan barang dan saring berdasarkan nama, kategori, atau status.</p></li>
            <li><p class="eyebrow">03</p><h3>Hubungi</h3><p>Jika menemukan kecocokan, hubungi pelapor untuk mengatur pengambilan barang.</p></li>
        </ol>
    </section>
@endsection
