@extends('layouts.app')

@section('title', 'Kelola Laporan')

@section('content')
    <section class="section">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Database · Laporan barang</p>
                <h1>Kelola Laporan</h1>
            </div>
            <div class="actions" style="margin-top: 0">
                <a class="button secondary" href="{{ route('home') }}">Kembali ke NEMU</a>
                <a class="button" href="{{ route('laporan.lost.create') }}">＋ Laporan hilang</a>
                <a class="button gold" href="{{ route('laporan.found.create') }}">＋ Laporan temuan</a>
            </div>
        </div>
        <section class="panel">
            @if ($barangs->isEmpty())
                <p class="empty">Belum ada laporan barang.</p>
            @else
                <div class="manage-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Barang</th>
                                <th scope="col">Status</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barangs as $barang)
                                <tr>
                                    <td><strong>{{ $barang->nama }}</strong><br>{{ $barang->nama_pelapor }}</td>
                                    <td>{{ $barang->status }}</td>
                                    <td>{{ $barang->kategori }}</td>
                                    <td>{{ $barang->lokasi }}</td>
                                    <td>{{ $barang->tanggal->format('d M Y') }}</td>
                                    <td>
                                        <div class="table-actions">
                                            <a class="button secondary" href="{{ route('laporan.edit', $barang) }}">Edit</a>
                                            <form action="{{ route('laporan.destroy', $barang) }}" method="POST" onsubmit="return confirm('Hapus laporan {{ addslashes($barang->nama) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="button danger" type="submit">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </section>
@endsection
