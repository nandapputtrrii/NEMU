<article class="item-card">
    @if ($barang->foto)
        <img
            src="{{ str_starts_with($barang->foto, 'data:image/') ? $barang->foto : asset('storage/'.$barang->foto) }}"
            alt="Foto {{ $barang->nama }}"
            loading="lazy"
        >
    @endif
    <div class="item-body">
        <p class="item-label">
            {{ $barang->kategori }} ·
            <span class="item-status {{ $barang->status === 'Ditemukan' ? 'found' : '' }}">{{ $barang->status }}</span>
        </p>
        <h3>{{ $barang->nama }}</h3>
        <p class="item-description">{{ $barang->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
        <div class="item-meta">
            <span>Lokasi: {{ $barang->lokasi }}</span>
            <span>Tanggal: {{ $barang->tanggal->format('d M Y') }}{{ $barang->jam ? ' · '.$barang->jam : '' }}</span>
            @if ($barang->penyimpanan)
                <span>Disimpan di: {{ $barang->penyimpanan }}</span>
            @endif
        </div>
        @auth
            @if ($barang->telepon)
            @php
                $nomorWa = preg_replace('/\D+/', '', $barang->telepon);
                if (str_starts_with($nomorWa, '0')) {
                    $nomorWa = '62'.substr($nomorWa, 1);
                }
            @endphp
            @if (strlen($nomorWa) >= 10 && strlen($nomorWa) <= 15)
                <a class="button secondary" style="margin-top: 14px" href="https://wa.me/{{ $nomorWa }}" target="_blank" rel="noopener noreferrer">Hubungi pelapor</a>
            @endif
            @endif
        @else
            <a class="button secondary" style="margin-top: 14px" href="{{ route('login') }}">Masuk untuk menghubungi pelapor</a>
        @endif
    </div>
</article>
