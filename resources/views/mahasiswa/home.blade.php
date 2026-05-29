@extends('layouts.mahasiswa')

@section('title', 'Beranda')

@push('styles')
<style>
.hero {
    max-width: 1280px;
    width: 100%;
    min-height: calc(100dvh - 64px - 56px);
    margin: 0 auto;
    padding: 42px 24px;

    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-card {
    width: 100%;
    min-height: clamp(440px, 55vh, 560px);
    background: linear-gradient(135deg, #c8d3ee 0%, #b8c4e0 45%, #a8b6d4 100%);
    border-radius: 28px;
    padding: 72px 56px;
    text-align: center;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    box-shadow: 0 24px 55px rgba(30, 45, 90, 0.14);
}

.hero-title {
    font-family: 'Sora', sans-serif;
    font-size: clamp(42px, 5.2vw, 64px);
    font-weight: 800;
    color: var(--navy-dark);
    margin-bottom: 18px;
    line-height: 1.08;
    letter-spacing: -1.4px;
}

.hero-subtitle {
    font-size: clamp(14px, 1.5vw, 17px);
    color: rgba(21, 32, 64, 0.68);
    margin-bottom: 38px;
    line-height: 1.7;
    max-width: 600px;
}

.hero-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: var(--navy);
    color: white;
    padding: 15px 36px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.2s ease;
    min-width: 170px;
}

.hero-btn:hover {
    background: var(--navy-dark);
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(30, 45, 90, 0.2);
}

/* Laptop kecil / tablet */
@media (max-width: 1024px) {
    .hero {
        max-width: 100%;
        padding: 36px 22px;
    }

    .hero-card {
        min-height: 430px;
        padding: 64px 42px;
        border-radius: 24px;
    }

    .hero-title {
        font-size: clamp(38px, 6vw, 54px);
    }
}

/* Tablet */
@media (max-width: 768px) {
    .hero {
        min-height: calc(100dvh - 120px);
        padding: 32px 18px;
    }

    .hero-card {
        min-height: 380px;
        padding: 56px 32px;
        border-radius: 22px;
    }

    .hero-title {
        font-size: clamp(34px, 7vw, 48px);
        letter-spacing: -1px;
    }

    .hero-subtitle {
        margin-bottom: 32px;
    }
}

/* Mobile */
@media (max-width: 480px) {
    .hero {
        min-height: calc(100dvh - 110px);
        padding: 24px 14px;
    }

    .hero-card {
        min-height: 340px;
        padding: 44px 22px;
        border-radius: 18px;
    }

    .hero-title {
        font-size: clamp(32px, 10vw, 42px);
        margin-bottom: 14px;
        letter-spacing: -0.6px;
    }

    .hero-subtitle {
        font-size: 13px;
        margin-bottom: 26px;
        max-width: 280px;
    }

    .hero-btn {
        width: 100%;
        max-width: 240px;
        padding: 13px 24px;
        font-size: 13px;
    }
}

/* Mobile sangat kecil */
@media (max-width: 360px) {
    .hero {
        padding: 20px 12px;
    }

    .hero-card {
        min-height: 310px;
        padding: 38px 18px;
    }

    .hero-title {
        font-size: 30px;
    }

    .hero-btn {
        max-width: 100%;
    }
}
</style>
@endpush

@section('content')

<section class="hero">
    <div class="hero-card">

        <h1 class="hero-title">
            LAPORAN<br>
            DAN KELUHAN
        </h1>

        <p class="hero-subtitle">
            Sampaikan keluhan laboratorium kepada para asisten
        </p>

        <a href="{{ route('mahasiswa.form') }}" class="hero-btn">
            Buat Laporan
        </a>

    </div>
</section>

@endsection