@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <section class="panel auth-card">
        <p class="eyebrow">Khusus mahasiswa UMM</p>
        <h1>Masuk ke NEMU</h1>
        <p>Gunakan NIM dan PIC UMM untuk masuk ke prototipe NEMU.</p>

        <div class="notice" id="login-notice" role="status" hidden></div>

        <form id="login-form" data-login-path="{{ route('login') }}">
            <div class="field">
                <label for="nim">NIM UMM</label>
                <input id="nim" name="nim" type="text" autocomplete="username" required placeholder="Masukkan NIM">
            </div>
            <div class="field">
                <label for="pic">PIC UMM</label>
                <input id="pic" name="pic" type="password" autocomplete="current-password" required placeholder="Masukkan PIC">
            </div>
            <p class="error" id="login-error" role="alert" hidden></p>
            <button class="button" type="submit">Masuk</button>
        </form>

        <p class="auth-note">Login ini masih berupa mock untuk prototipe. NIM disimpan di browser, PIC tidak disimpan maupun diverifikasi ke layanan UMM.</p>
    </section>

    <script>
        const parameters = new URLSearchParams(window.location.search);
        const notice = document.querySelector("#login-notice");
        const reason = parameters.get("reason");

        if (reason === "claim") {
            notice.textContent = "Masuk dulu untuk menghubungi pelapor melalui WhatsApp.";
            notice.hidden = false;
        } else if (reason === "report") {
            notice.textContent = "Masuk dengan akun UMM dulu untuk melaporkan barang.";
            notice.hidden = false;
        }

        document.querySelector("#login-form").addEventListener("submit", function (event) {
            event.preventDefault();
            const nim = document.querySelector("#nim").value.trim();
            const pic = document.querySelector("#pic").value.trim();
            const error = document.querySelector("#login-error");

            if (!nim || !pic) {
                error.textContent = "NIM dan PIC UMM wajib diisi.";
                error.hidden = false;
                return;
            }

            try {
                localStorage.setItem("nemuUser", JSON.stringify({ isLoggedIn: true, nim }));
                const requestedPage = parameters.get("next") || "/";
                const destination = new URL(requestedPage, window.location.origin);

                const loginPath = new URL(document.querySelector("#login-form").dataset.loginPath, window.location.origin).pathname;
                if (destination.origin !== window.location.origin || destination.pathname === loginPath) {
                    window.location.assign("/");
                    return;
                }

                window.location.assign(destination.href);
            } catch (loginError) {
                console.error("Gagal menyimpan sesi login:", loginError);
                error.textContent = "Login tidak bisa disimpan. Periksa pengaturan penyimpanan browser.";
                error.hidden = false;
            }
        });
    </script>
@endsection
