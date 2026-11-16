@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <section class="panel auth-card">
        <p class="eyebrow">Akun NEMU</p>
        <h1>Masuk ke NEMU</h1>
        <p>Gunakan NIM dan password akun NEMU yang sudah kamu daftarkan.</p>

        @if (session('status'))
            <div class="notice" role="status">{{ session('status') }}</div>
        @endif

        <form action="{{ route('login.store') }}" method="POST">
            @csrf
            <div class="field">
                <label for="nim">NIM</label>
                <input id="nim" name="nim" type="text" value="{{ old('nim') }}" maxlength="30" autocomplete="username" required autofocus placeholder="Masukkan NIM">
                @error('nim') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Masukkan password">
                @error('password') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label><input name="remember" type="checkbox" value="1"> Ingat saya</label>
            </div>
            <button class="button" type="submit">Masuk</button>
        </form>

        <p class="auth-note">Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>. Akun ini dikelola lokal oleh NEMU dan tidak diverifikasi ke sistem UMM.</p>
    </section>
@endsection
