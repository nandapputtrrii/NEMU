@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <section class="panel auth-card">
        <p class="eyebrow">Akun NEMU</p>
        <h1>Buat akun</h1>
        <p>Daftarkan akun lokal NEMU untuk membuat dan mengelola laporan.</p>

        <form action="{{ route('register.store') }}" method="POST">
            @csrf
            <div class="field">
                <label for="name">Nama</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" maxlength="255" autocomplete="name" required autofocus placeholder="Nama kamu">
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label for="nim">NIM</label>
                <input id="nim" name="nim" type="text" value="{{ old('nim') }}" maxlength="30" autocomplete="username" required placeholder="NIM kamu">
                @error('nim') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required placeholder="Minimal 8 karakter">
                @error('password') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Ulangi password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required placeholder="Ulangi password">
            </div>
            <button class="button" type="submit">Daftar</button>
        </form>

        <p class="auth-note">Pendaftaran ini membuat akun lokal NEMU. NIM tidak diverifikasi ke sistem UMM. Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>.</p>
    </section>
@endsection
