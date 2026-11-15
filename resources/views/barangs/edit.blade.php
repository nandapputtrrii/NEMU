@extends('layouts.app')

@section('title', 'Edit Laporan')

@section('content')
    <section class="section">
        <p class="eyebrow">Kelola laporan · Edit</p>
        <h1>Edit Laporan Barang</h1>
        <p class="hero-copy" style="margin-left: 0">Perbarui informasi laporan di bawah ini.</p>
        <div class="panel form-panel" style="margin-top: 24px">
            <form action="{{ route('laporan.update', $barang) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('barangs._form', ['submitLabel' => 'Simpan perubahan'])
            </form>
        </div>
    </section>
@endsection
