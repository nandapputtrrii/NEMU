<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NEMU') · Lost &amp; Found UMM</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #171717;
            --paper: #f7f5ef;
            --red: #c8102e;
            --red-dark: #9f1027;
            --gold: #f4c542;
            --muted: #66645f;
            --line: #dedbd2;
            --green: #187044;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; background: var(--paper); color: var(--ink); font-family: Arial, Helvetica, sans-serif; line-height: 1.6; }
        a { color: inherit; }
        .site-header { position: sticky; top: 0; z-index: 5; border-bottom: 2px solid var(--ink); background: rgb(247 245 239 / 96%); backdrop-filter: blur(12px); }
        .header-inner { width: min(1120px, calc(100% - 40px)); min-height: 70px; margin: auto; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .brand { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
        .brand img { width: 40px; height: 40px; object-fit: contain; }
        .brand strong { font-size: 1.45rem; letter-spacing: -.06em; }
        .nav { display: flex; flex-wrap: wrap; align-items: center; gap: 20px; }
        .nav a { font-size: .9rem; font-weight: 700; text-decoration: none; }
        .nav a:hover, .nav a:focus-visible { color: var(--red); }
        .nav .auth-link { padding: 6px 12px; border: 2px solid var(--ink); border-radius: 999px; }
        .auth-card { width: min(100%, 480px); margin: 55px auto; padding: clamp(22px, 5vw, 38px); }
        .auth-card h1 { font-size: clamp(2rem, 7vw, 3rem); }
        .auth-note { margin: 22px 0 0; color: var(--muted); font-size: .82rem; }
        .main { width: min(1120px, calc(100% - 40px)); min-height: calc(100vh - 150px); margin: auto; }
        .hero { max-width: 860px; margin: auto; padding: 82px 0 64px; text-align: center; }
        .eyebrow { margin: 0 0 8px; color: var(--red); font: .85rem ui-monospace, SFMono-Regular, Menlo, monospace; }
        h1, h2, h3, p { margin-top: 0; }
        h1 { margin-bottom: 16px; font-size: clamp(2.6rem, 8vw, 5rem); line-height: .98; letter-spacing: -.06em; }
        h2 { margin-bottom: 8px; font-size: clamp(1.8rem, 5vw, 2.8rem); line-height: 1.08; letter-spacing: -.045em; }
        .hero-copy { max-width: 640px; margin: 18px auto 0; color: var(--muted); font-size: 1.1rem; }
        .actions { display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; margin-top: 26px; }
        .button { min-height: 42px; display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 9px 16px; border: 2px solid var(--ink); border-radius: 9px; background: var(--red); color: #fff; font: inherit; font-size: .9rem; font-weight: 750; text-decoration: none; cursor: pointer; }
        .button:hover { background: var(--red-dark); }
        .button.secondary { background: transparent; color: var(--ink); }
        .button.secondary:hover { background: var(--ink); color: white; }
        .button.gold { background: var(--gold); color: var(--ink); }
        .button.gold:hover { background: #e7b72b; }
        .button.danger { background: white; color: var(--red-dark); }
        .button.danger:hover { background: #fff1f2; }
        .summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 48px; }
        .summary-card, .panel, .item-card { border: 2px solid var(--ink); border-radius: 12px; background: white; }
        .summary-card { padding: 18px; }
        .summary-card strong { display: block; font-size: 2rem; line-height: 1.1; }
        .summary-card span { color: var(--muted); font-size: .9rem; }
        .section { padding: 34px 0; scroll-margin-top: 90px; }
        .section-heading { display: flex; align-items: end; justify-content: space-between; gap: 14px; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid var(--ink); }
        .section-heading .eyebrow { margin-bottom: 3px; }
        .section-heading h2 { margin-bottom: 0; }
        .item-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
        .item-card { min-width: 0; overflow: hidden; }
        .item-card img { display: block; width: 100%; aspect-ratio: 4 / 3; object-fit: cover; border-bottom: 2px solid var(--ink); }
        .item-body { padding: 18px; }
        .item-label { margin-bottom: 6px; color: var(--red); font: .78rem ui-monospace, SFMono-Regular, Menlo, monospace; text-transform: uppercase; }
        .item-status { font-weight: 800; }
        .item-status.found { color: var(--green); }
        .item-card h3 { margin-bottom: 8px; font-size: 1.35rem; line-height: 1.2; }
        .item-description { min-height: 2.8em; margin-bottom: 14px; color: var(--muted); }
        .item-meta { display: grid; gap: 3px; padding-top: 12px; border-top: 1px dashed #aaa; color: var(--muted); font: .78rem ui-monospace, SFMono-Regular, Menlo, monospace; }
        .panel { padding: clamp(18px, 4vw, 38px); }
        .search-panel { background: var(--ink); color: white; }
        .search-panel .eyebrow { color: var(--gold); }
        .search-panel .muted { color: #d0cfcc; }
        .search-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: center; }
        .search-form { display: grid; gap: 12px; }
        .field { margin-bottom: 18px; }
        .field label { display: block; margin-bottom: 6px; font-size: .9rem; font-weight: 750; }
        .field input, .field select, .field textarea, .search-form input, .search-form select { width: 100%; padding: 11px 12px; border: 2px solid #bbb; border-radius: 8px; background: white; color: var(--ink); font: inherit; }
        .field textarea { min-height: 125px; resize: vertical; }
        .search-panel .search-form input, .search-panel .search-form select { border-color: #ddd; }
        .error { margin: 6px 0 0; color: var(--red-dark); font-size: .84rem; }
        .search-panel .error { color: #ffc9cf; }
        .empty, .notice { grid-column: 1 / -1; padding: 18px; border: 1px dashed #999; border-radius: 10px; color: var(--muted); text-align: center; }
        .notice { margin-bottom: 20px; border-style: solid; border-color: #9bc9ac; background: #effaf2; color: #17613c; text-align: left; }
        .notice.error-notice { border-color: #e4a8ae; background: #fff1f2; color: #8d1728; }
        .manage-table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 14px 16px; border-bottom: 1px solid #e8e5dd; vertical-align: top; }
        th { background: #faf9f5; color: var(--muted); font-size: .74rem; letter-spacing: .07em; text-transform: uppercase; }
        td { font-size: .9rem; }
        .table-actions { display: flex; gap: 7px; align-items: center; }
        .form-panel { max-width: 760px; }
        .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 20px 0 0; padding: 0; list-style: none; }
        .steps li { padding-top: 12px; border-top: 2px solid var(--ink); }
        .steps li p:last-child { color: var(--muted); }
        footer { margin-top: 36px; padding: 24px 20px; background: var(--ink); color: white; text-align: center; }
        footer p { margin: 2px; }
        footer .muted { color: #bbb; font-size: .85rem; }
        @media (max-width: 760px) {
            .header-inner, .main { width: min(100% - 28px, 1120px); }
            .header-inner { align-items: flex-start; flex-direction: column; padding: 12px 0; }
            .nav { gap: 10px 16px; }
            .hero { padding: 54px 0 40px; }
            .summary, .item-grid, .search-layout, .steps { grid-template-columns: 1fr; }
            .section-heading { align-items: flex-start; flex-direction: column; }
        }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/Bruh-removebg-preview.png') }}" alt="">
                <strong>NEMU</strong>
            </a>
            <nav class="nav" aria-label="Menu utama">
                <a href="{{ route('home') }}#cari">Cari barang</a>
                <a href="{{ route('laporan.lost.create') }}">Lapor kehilangan</a>
                <a href="{{ route('laporan.found.create') }}">Lapor temuan</a>
                <a href="{{ route('laporan.index') }}">Kelola laporan</a>
                <a class="auth-link" id="auth-link" href="{{ route('login') }}">Masuk</a>
            </nav>
        </div>
    </header>
    <main class="main">
        @if (session('success'))
            <div class="notice" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="notice error-notice" role="alert">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
    <footer>
        <p><strong>NEMU</strong></p>
        <p class="muted">&copy; {{ date('Y') }} NEMU. Semoga barangmu cepat ketemu.</p>
    </footer>
    <script src="{{ asset('js/nemu-auth.js') }}"></script>
</body>
</html>
