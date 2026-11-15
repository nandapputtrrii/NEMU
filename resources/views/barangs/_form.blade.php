@php
    $item = $barang ?? null;
    $categories = ['Elektronik', 'Dokumen', 'Aksesoris', 'Tas', 'Pakaian', 'Lainnya'];
@endphp

<div class="field">
    <label for="nama">Nama barang</label>
    <input id="nama" name="nama" value="{{ old('nama', $item?->nama) }}" maxlength="255" required>
    @error('nama') <p class="error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="kategori">Kategori</label>
    <select id="kategori" name="kategori" required>
        <option value="">Pilih kategori</option>
        @foreach ($categories as $category)
            <option value="{{ $category }}" @selected(old('kategori', $item?->kategori) === $category)>{{ $category }}</option>
        @endforeach
    </select>
    @error('kategori') <p class="error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="lokasi">Lokasi</label>
    <input id="lokasi" name="lokasi" value="{{ old('lokasi', $item?->lokasi) }}" maxlength="255" required>
    @error('lokasi') <p class="error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="tanggal">Tanggal</label>
    <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $item?->tanggal?->format('Y-m-d')) }}" required>
    @error('tanggal') <p class="error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="jam">Jam <span style="font-weight: normal">(opsional)</span></label>
    <input id="jam" name="jam" type="time" value="{{ old('jam', $item?->jam ? substr($item->jam, 0, 5) : '') }}">
    @error('jam') <p class="error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="status">Status laporan</label>
    <select id="status" name="status" required>
        <option value="Hilang" @selected(old('status', $status ?? $item?->status) === 'Hilang')>Hilang</option>
        <option value="Ditemukan" @selected(old('status', $status ?? $item?->status) === 'Ditemukan')>Ditemukan</option>
    </select>
    @error('status') <p class="error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="nama_pelapor">Nama pelapor</label>
    <input id="nama_pelapor" name="nama_pelapor" value="{{ old('nama_pelapor', $item?->nama_pelapor) }}" maxlength="255" required>
    @error('nama_pelapor') <p class="error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="telepon">Nomor WhatsApp</label>
    <input id="telepon" name="telepon" type="tel" inputmode="tel" placeholder="081234567890" value="{{ old('telepon', $item?->telepon) }}" required>
    <p class="hint">Format nomor Indonesia, contoh 081234567890.</p>
    @error('telepon') <p class="error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="penyimpanan">Lokasi penyimpanan <span style="font-weight: normal">(opsional)</span></label>
    <input id="penyimpanan" name="penyimpanan" value="{{ old('penyimpanan', $item?->penyimpanan) }}" maxlength="255">
    @error('penyimpanan') <p class="error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="deskripsi">Deskripsi <span style="font-weight: normal">(opsional)</span></label>
    <textarea id="deskripsi" name="deskripsi" maxlength="5000">{{ old('deskripsi', $item?->deskripsi) }}</textarea>
    @error('deskripsi') <p class="error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="foto">Foto <span style="font-weight: normal">(opsional; JPEG, PNG, atau WebP, maks. 5 MB)</span></label>
    <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp">
    @error('foto') <p class="error">{{ $message }}</p> @enderror
    @if ($item?->foto)
        <p class="hint">Foto saat ini tersimpan. Pilih foto baru hanya jika ingin menggantinya.</p>
    @endif
</div>
<div class="actions" style="justify-content: flex-start">
    <button class="button" type="submit">{{ $submitLabel }}</button>
    <a class="button secondary" href="{{ route('home') }}">Kembali</a>
</div>
