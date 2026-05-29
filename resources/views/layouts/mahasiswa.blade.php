<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Pelaporan Keluhan Lab')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy:      #1e2d5a;
            --navy-dark: #152040;
            --navy-mid:  #2a3f7e;
            --accent:    #3d5fc4;
            --accent-lt: #e8edf8;
            --slate:     #b0bec5;
            --bg:        #f4f6fb;
            --white:     #ffffff;
            --text:      #1a2340;
            --muted:     #7a8aaa;
            --border:    #dde3f0;
            --success:   #1a7f4b;
            --success-bg:#e6f4ee;
            --warning:   #a16207;
            --warning-bg:#fef9c3;
            --danger:    #b91c1c;
            --danger-bg: #fee2e2;
            --info:      #1d4ed8;
            --info-bg:   #dbeafe;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        img, svg {
            max-width: 100%;
            display: block;
        }

        /* ─── NAVBAR ─── */
        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 12px rgba(30,45,90,0.07);
        }

        .navbar-inner {
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
            padding: 0 24px;
            min-height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: relative;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            min-width: 0;
        }

        .navbar-logo {
            width: 40px;
            height: 40px;
            background: var(--navy);
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .navbar-name {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 13px;
            color: var(--navy);
            line-height: 1.3;
            white-space: nowrap;
        }

        .navbar-name span {
            display: block;
            font-weight: 400;
            font-size: 11px;
            color: var(--muted);
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
        }

        .navbar-nav a {
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            color: var(--muted);
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.18s ease;
            position: relative;
            display: block;
            white-space: nowrap;
        }

        .navbar-nav a:hover {
            color: var(--navy);
            background: var(--accent-lt);
        }

        .navbar-nav a.active {
            color: var(--navy);
            font-weight: 700;
        }

        .navbar-nav a.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 16px;
            right: 16px;
            height: 2px;
            background: var(--accent);
            border-radius: 2px 2px 0 0;
        }

        /* ─── HAMBURGER MENU ─── */
        .nav-toggle {
            display: none;
        }

        .nav-toggle-label {
            display: none;
            width: 40px;
            height: 40px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--white);
            cursor: pointer;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .nav-toggle-label span,
        .nav-toggle-label span::before,
        .nav-toggle-label span::after {
            content: '';
            display: block;
            width: 20px;
            height: 2px;
            background: var(--navy);
            border-radius: 999px;
            transition: 0.2s ease;
            position: relative;
        }

        .nav-toggle-label span::before {
            position: absolute;
            top: -6px;
        }

        .nav-toggle-label span::after {
            position: absolute;
            top: 6px;
        }

        .nav-toggle:checked + .nav-toggle-label span {
            background: transparent;
        }

        .nav-toggle:checked + .nav-toggle-label span::before {
            top: 0;
            transform: rotate(45deg);
        }

        .nav-toggle:checked + .nav-toggle-label span::after {
            top: 0;
            transform: rotate(-45deg);
        }

        /* ─── MAIN ─── */
        main {
            flex: 1;
            width: 100%;
        }

        /* ─── FOOTER ─── */
        .footer {
            background: var(--navy-dark);
            color: rgba(255,255,255,0.45);
            text-align: center;
            padding: 18px 24px;
            font-size: 12px;
            margin-top: auto;
            line-height: 1.6;
        }

        /* ─── FLASH MESSAGE ─── */
        .flash {
            max-width: 1100px;
            width: 100%;
            margin: 16px auto 0;
            padding: 0 24px;
        }

        .flash-inner {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            animation: fadeDown 0.3s ease;
            line-height: 1.5;
            word-break: break-word;
        }

        .flash-inner svg {
            flex-shrink: 0;
            margin-top: 2px;
        }

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .flash-success {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid #a7f3d0;
        }

        .flash-error {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid #fca5a5;
        }

        .flash-info {
            background: var(--info-bg);
            color: var(--info);
            border: 1px solid #93c5fd;
        }

        /* ─── TABLET ─── */
        @media (max-width: 768px) {
            .navbar-inner {
                padding: 0 18px;
            }

            .nav-toggle-label {
                display: flex;
            }

            .navbar-nav {
                position: absolute;
                top: calc(100% + 10px);
                left: 18px;
                right: 18px;
                display: none;
                flex-direction: column;
                align-items: stretch;
                gap: 6px;
                padding: 12px;
                background: var(--white);
                border: 1px solid var(--border);
                border-radius: 14px;
                box-shadow: 0 12px 30px rgba(30,45,90,0.12);
            }

            .nav-toggle:checked ~ .navbar-nav {
                display: flex;
            }

            .navbar-nav a {
                width: 100%;
                padding: 12px 14px;
                border-radius: 10px;
            }

            .navbar-nav a.active::after {
                display: none;
            }

            .navbar-nav a.active {
                background: var(--accent-lt);
            }

            .flash {
                padding: 0 18px;
            }
        }

        /* ─── MOBILE KECIL ─── */
        @media (max-width: 480px) {
            .navbar-inner {
                padding: 0 14px;
                min-height: 60px;
            }

            .navbar-logo {
                width: 36px;
                height: 36px;
                border-radius: 9px;
            }

            .navbar-name {
                font-size: 12px;
            }

            .navbar-name span {
                font-size: 10px;
            }

            .navbar-nav {
                left: 14px;
                right: 14px;
            }

            .flash {
                margin-top: 12px;
                padding: 0 14px;
            }

            .flash-inner {
                font-size: 12px;
                padding: 11px 13px;
            }

            .footer {
                font-size: 11px;
                padding: 16px 14px;
            }
        }

        /* ─── MOBILE SANGAT KECIL ─── */
        @media (max-width: 360px) {
            .navbar-name {
                max-width: 180px;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .navbar-name span {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="{{ route('mahasiswa.form') }}" class="navbar-brand">
            <img
                src="{{ asset('images/logoict.jpg') }}"
                alt="Logo"
                width="40"
                height="40"
                class="navbar-logo"
            >

            <div class="navbar-name">
                Sistem Pelaporan
                <span>Keluhan Laboratorium</span>
            </div>
        </a>

        <input type="checkbox" id="nav-toggle" class="nav-toggle">

        <label for="nav-toggle" class="nav-toggle-label" aria-label="Buka menu navigasi">
            <span></span>
        </label>

        <ul class="navbar-nav">
            <li>
                <a href="{{ route('mahasiswa.home') }}"
                   class="{{ request()->routeIs('mahasiswa.home') ? 'active' : '' }}">
                    Beranda
                </a>
            </li>
            <li>
                <a href="{{ route('mahasiswa.status') }}"
                   class="{{ request()->routeIs('mahasiswa.status*') ? 'active' : '' }}">
                    Status Laporan
                </a>
            </li>
        </ul>
    </div>
</nav>

@if(session('success'))
    <div class="flash">
        <div class="flash-inner flash-success">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if(session('error') || $errors->any())
    <div class="flash">
        <div class="flash-inner flash-error">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>{{ session('error') ?? $errors->first() }}</span>
        </div>
    </div>
@endif

<main>
    @yield('content')
</main>

<footer class="footer">
    © {{ date('Y') }} Sistem Pelaporan Keluhan Lab &mdash; Laboratorium Komputer
</footer>

@stack('scripts')
</body>
</html>