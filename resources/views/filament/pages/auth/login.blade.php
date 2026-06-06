<div>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap');

        body, html {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            overflow: hidden !important;
            background: #c8cfe8 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        .fi-simple-layout,
        .fi-simple-layout-content,
        .fi-simple-main {
            all: unset !important;
            display: block !important;
        }

        :root {
            --navy:         #1e2d5a;
            --navy-dark:    #152040;
            --navy-mid:     #2a3f7e;
            --accent:       #3d5fc4;
            --panel-bg:     #c8cfe8;
            --white:        #ffffff;
            --text:         #1a2340;
            --error:        #dc2626;
        }

        .lrs-login {
            display: flex;
            position: fixed;
            inset: 0;
            z-index: 9999;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Panel Kiri - Foto */
        .lrs-photo {
            flex: 1 1 58%;
            position: relative;
            overflow: hidden;
            background: var(--navy-dark);
        }

        .lrs-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 10s ease;
        }

        .lrs-photo:hover img { transform: scale(1.04); }

        .lrs-photo-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                130deg,
                rgba(21,32,64,0.55) 0%,
                rgba(30,45,90,0.25) 55%,
                transparent 100%
            );
        }

        .lrs-photo-content {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 52px 56px;
        }

        .lrs-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 99px;
            padding: 7px 16px;
            margin-bottom: 20px;
            width: fit-content;
        }

        .lrs-tag-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 6px #4ade80;
            flex-shrink: 0;
        }

        .lrs-tag span {
            font-size: 12px;
            color: rgba(255,255,255,0.88);
            font-weight: 500;
        }

        .lrs-photo-title {
            font-family: 'Sora', sans-serif;
            font-size: clamp(26px, 3vw, 40px);
            font-weight: 800;
            color: white;
            line-height: 1.18;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            text-shadow: 0 2px 20px rgba(0,0,0,0.3);
        }

        .lrs-photo-sub {
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            max-width: 360px;
            line-height: 1.65;
        }

        /* Panel Kanan - Form */
        .lrs-form-panel {
            flex: 0 0 420px;
            background: var(--panel-bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 52px 44px;
            position: relative;
            overflow: hidden;
        }

        .lrs-form-panel::before {
            content: '';
            position: absolute;
            width: 380px;
            height: 380px;
            background: rgba(255,255,255,0.22);
            border-radius: 50%;
            top: -150px;
            right: -100px;
            pointer-events: none;
        }

        .lrs-form-panel::after {
            content: '';
            position: absolute;
            width: 230px;
            height: 230px;
            background: rgba(30,45,90,0.07);
            border-radius: 50%;
            bottom: -80px;
            left: -55px;
            pointer-events: none;
        }

        .lrs-logo {
            width: 78px;
            height: 78px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 24px rgba(30,45,90,0.15);
            overflow: hidden;
        }

        .lrs-logo img { width: 60px; height: 60px; object-fit: contain; }

        .lrs-logo-text {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 20px;
            color: var(--navy);
        }

        .lrs-heading {
            text-align: center;
            margin-bottom: 36px;
            position: relative;
            z-index: 1;
        }

        .lrs-heading h1 {
            font-family: 'Sora', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: var(--navy-dark);
            margin-bottom: 6px;
        }

        .lrs-heading p {
            font-size: 13px;
            color: rgba(26,35,64,0.55);
            font-weight: 500;
        }

        .lrs-fields {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 14px;
            position: relative;
            z-index: 1;
        }

        .lrs-alert {
            background: rgba(220,38,38,0.1);
            border: 1px solid rgba(220,38,38,0.22);
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 12px;
            color: #b91c1c;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .lrs-input-wrap { position: relative; }

        .lrs-input-wrap input {
            width: 100%;
            background: rgba(255,255,255,0.8);
            border: 1.5px solid rgba(255,255,255,0.6);
            border-radius: 12px;
            padding: 14px 46px 14px 46px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            outline: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .lrs-input-wrap input::placeholder { color: #9aaac4; }

        .lrs-input-wrap input:focus {
            background: rgba(255,255,255,0.96);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(61,95,196,0.15);
        }

        .lrs-input-wrap input.lrs-err {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(220,38,38,0.12);
        }

        .lrs-icon-left {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9aaac4;
            pointer-events: none;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .lrs-input-wrap:focus-within .lrs-icon-left { color: var(--accent); }

        .lrs-eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9aaac4;
            padding: 2px;
            display: flex;
            align-items: center;
            transition: color 0.18s;
        }

        .lrs-eye:hover { color: var(--accent); }

        .lrs-field-err {
            font-size: 11px;
            color: var(--error);
            font-weight: 500;
            margin-top: 5px;
            padding-left: 2px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── TOMBOL LOGIN ── */
        .lrs-btn {
            width: 100%;
            background: var(--navy);
            color: white;
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.3px;
            padding: 15px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 20px rgba(30,45,90,0.3);
        }

        .lrs-btn:hover {
            background: var(--navy-mid);
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(30,45,90,0.38);
        }

        .lrs-btn:active { transform: translateY(0); }

        .lrs-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none !important;
        }

        .lrs-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.35);
            border-top-color: white;
            border-radius: 50%;
            animation: lrs-spin 0.7s linear infinite;
        }

        @keyframes lrs-spin { to { transform: rotate(360deg); } }

        @media (max-width: 768px) {
            .lrs-photo { display: none; }
            .lrs-form-panel { flex: 1; padding: 40px 32px; }
        }
    </style>

    <div class="lrs-login">

        {{-- ── PANEL KIRI: FOTO ── --}}
        <div class="lrs-photo">
            <img
                src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1400&q=85"
                alt="Laboratorium"
                onerror="this.src='https://images.unsplash.com/photo-1606761568499-6d2451b23c66?w=1400&q=85'"
            >
            <div class="lrs-photo-overlay"></div>
            <div class="lrs-photo-content">
                <div class="lrs-tag">
                    <div class="lrs-tag-dot"></div>
                    <span>Sistem Aktif</span>
                </div>
                <h2 class="lrs-photo-title">
                    Sistem Pelaporan<br>Keluhan Lab ICT
                </h2>
                <p class="lrs-photo-sub">
                    Platform pengelolaan laporan kerusakan dan keluhan fasilitas laboratorium komputer.
                </p>
            </div>
        </div>

        {{-- ── PANEL KANAN: FORM ── --}}
        <div class="lrs-form-panel">

            <div class="lrs-logo">
                <img src="{{  asset('images/logoict.jpeg') }}" alt="Logo">
            </div>

            <div class="lrs-heading">
                <h1>Hello Again!</h1>
                <p>Selamat datang di Lab ICT</p>
            </div>

            <div class="lrs-fields">

                {{-- Alert error --}}
                @if ($errors->has('email'))
                    <div class="lrs-alert">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="flex-shrink:0">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $errors->first('email') }}
                    </div>
                @endif

                {{-- ✅ PERBAIKAN: wire:submit.prevent="authenticate" di form --}}
                <form wire:submit.prevent="authenticate" style="width:100%;display:flex;flex-direction:column;gap:14px;">

                    {{-- Email — wire:model="data.email" ✅ --}}
                    <div>
                        <div class="lrs-input-wrap">
                            <span class="lrs-icon-left">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <input
                                type="email"
                                wire:model="data.email"
                                placeholder="Email"
                                autocomplete="email"
                                class="{{ $errors->has('email') ? 'lrs-err' : '' }}"
                                autofocus
                            >
                        </div>
                    </div>

                    {{-- Password — wire:model="data.password" ✅ --}}
                    <div x-data="{ show: false }">
                        <div class="lrs-input-wrap">
                            <span class="lrs-icon-left">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input
                                :type="show ? 'text' : 'password'"
                                wire:model="data.password"
                                placeholder="Password"
                                autocomplete="current-password"
                                class="{{ $errors->has('password') ? 'lrs-err' : '' }}"
                            >
                            <button type="button" class="lrs-eye" @click="show = !show" tabindex="-1">
                                <svg x-show="!show" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="lrs-field-err">
                                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ✅ type="submit" agar trigger wire:submit di form --}}
                    <button
                        type="submit"
                        class="lrs-btn"
                        wire:loading.attr="disabled"
                        wire:target="authenticate"
                    >
                        <span wire:loading.remove wire:target="authenticate">Log in</span>
                        <div class="lrs-spinner" wire:loading wire:target="authenticate" style="display:none" wire:loading.style="display:flex"></div>
                        <span wire:loading wire:target="authenticate" style="display:none;font-size:14px" wire:loading.style="display:inline">Masuk...</span>
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>