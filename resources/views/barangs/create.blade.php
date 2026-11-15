@extends('layouts.app')

@section('title', 'Buat Laporan')

@section('content')
    <section class="section">
        <div class="page-heading">
            <p class="eyebrow">Lapor barang {{ strtolower($status) }}</p>
            <h1>{{ $status === 'Hilang' ? 'Apa yang hilang?' : 'Apa yang kamu temukan?' }}</h1>
            <p class="hero-copy" style="margin-left: 0">Isi informasi barang agar mahasiswa lain dapat melihat dan membantu.</p>
        </div>
        <div class="panel form-panel">
            <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="status" value="{{ $status }}">
                @include('barangs._form', ['submitLabel' => 'Kirim laporan'])
            </form>
        </div>
    </section>
@endsection
