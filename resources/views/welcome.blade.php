@extends('layouts.app')

@section('title', 'KostKu - Temukan Kost Impianmu')

@section('content')

<!-- HERO SECTION -->
<section class="hero">
    <div class="container position-relative" style="z-index:2">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="mb-3">
                    <span class="badge px-3 py-2 rounded-pill" style="background:rgba(255,255,255,0.15);color:white;font-size:0.85rem">
                        🏠 Platform Kost #1 di Indonesia
                    </span>
                </div>
                <h1 class="mb-4">
                    Temukan Kost <span style="color:#fbbf24">Impianmu</span><br>dengan Mudah
                </h1>
                <p class="lead mb-4" style="opacity:0.9">
                    Lebih dari {{ number_format($stats['total_kost']) }} pilihan kost berkualitas di {{ $stats['total_cities'] }} kota besar. Proses booking mudah, cepat, dan terpercaya.
                </p>

                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('kost.index') }}" class="btn btn-warning btn-lg px-4 fw-700">
                        <i class="fas fa-search me-2"></i>Cari Kost Sekarang
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-user-plus me-2"></i>Daftar Gratis
                    </a>
                    @endguest
                </div>

                <!-- Stats -->
                <div class="row g-3 mt-4">
                    <div class="col-4">
                        <div class="text-center">
                            <div style="font-size:1.8rem;font-weight:800;color:white">{{ number_format($stats['total_kost']) }}+</div>
                            <div style="font-size:0.75rem;opacity:0.8">Kost Tersedia</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div style="font-size:1.8rem;font-weight:800;color:white">{{ number_format($stats['total_users']) }}+</div>
                            <div style="font-size:0.75rem;opacity:0.8">Pengguna Aktif</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div style="font-size:1.8rem;font-weight:800;color:white">{{ $stats['total_cities'] }}+</div>
                            <div style="font-size:0.75rem;opacity:0.8">Kota</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mt-4 mt-lg-0" data-aos="fade-left">
                <!-- Search Box -->
                <div class="search-box">
                    <h5 class="fw-700 mb-3 text-dark">🔍 Cari Kost</h5>
                    <form action="{{ route('kost.index') }}" method="GET">
                        <div class="mb-3">
                            <input type="text" name="search" class="form-control form-control-lg"
                                placeholder="Cari nama kost, kota, atau lokasi..." value="{{ request('search') }}">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <select name="city" class="form-select">
                                    <option value="">Semua Kota</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}">{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <select name="type" class="form-select">
                                    <option value="">Semua Tipe</option>
                                    <option value="Putra">Kost Putra</option>
                                    <option value="Putri">Kost Putri</option>
                                    <option value="Campur">Kost Campur</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="number" name="min_price" class="form-control" placeholder="Harga Min (Rp)">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" class="form-control" placeholder="Harga Max (Rp)">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-search me-2"></i>Cari Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED KOST -->
<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4" data-aos="fade-up">
            <div>
                <h2 class="fw-800 mb-1">Kost Unggulan</h2>
                <p class="text-muted mb-0">Dipilih khusus untuk Anda</p>
            </div>
            <a href="{{ route('kost.index') }}" class="btn btn-outline-primary">
                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($featured as $kost)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="card kost-card h-100">
                    <div class="position-relative">
                        <img src="{{ $kost->thumbnail_url }}" class="kost-img w-100" alt="{{ $kost->name }}">
                        <span class="badge-type badge
                            {{ $kost->type === 'Putra' ? 'bg-primary' : ($kost->type === 'Putri' ? 'bg-pink' : 'bg-success') }}">
                            {{ $kost->type }}
                        </span>
                        <span class="badge-featured">⭐ Unggulan</span>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-700 mb-1">{{ $kost->name }}</h6>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $kost->city }}, {{ $kost->province }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="price-tag">{{ $kost->price_formatted }}<span class="text-muted fw-400" style="font-size:0.8rem">/bulan</span></div>
                            <div class="stars small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $kost->average_rating >= $i ? '' : '-o' }}" style="color:{{ $kost->average_rating >= $i ? '#f59e0b' : '#d1d5db' }}"></i>
                                @endfor
                                <span class="text-muted ms-1">({{ $kost->reviews->count() }})</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            @if($kost->facilities)
                                @foreach(array_slice($kost->facilities, 0, 3) as $fac)
                                    <span class="facility-badge">{{ $fac }}</span>
                                @endforeach
                            @endif
                        </div>
                        <a href="{{ route('kost.show', $kost->slug) }}" class="btn btn-primary w-100">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada kost unggulan</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="py-5" style="background:white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-800">Cara Kerja KostKu</h2>
            <p class="text-muted">3 langkah mudah menemukan kost impian</p>
        </div>
        <div class="row g-4 text-center">
            @foreach([
                ['icon' => 'fas fa-search', 'color' => 'bg-primary', 'title' => 'Cari Kost', 'desc' => 'Cari kost berdasarkan lokasi, tipe, harga dan fasilitas yang sesuai kebutuhanmu'],
                ['icon' => 'fas fa-calendar-check', 'color' => 'bg-success', 'title' => 'Booking Online', 'desc' => 'Pilih kost favoritmu dan lakukan booking langsung secara online dengan mudah'],
                ['icon' => 'fas fa-key', 'color' => 'bg-warning', 'title' => 'Pindah ke Kost', 'desc' => 'Setelah pembayaran dikonfirmasi, kamu sudah bisa pindah ke kost barumu'],
            ] as $i => $step)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="p-4">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="{{ $step['color'] }} text-white rounded-circle d-flex align-items-center justify-content-center floating"
                             style="width:72px;height:72px;font-size:1.5rem;animation-delay:{{ $i * 0.5 }}s">
                            <i class="{{ $step['icon'] }}"></i>
                        </div>
                    </div>
                    <h5 class="fw-700">{{ $step['title'] }}</h5>
                    <p class="text-muted">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- WHY US -->
<section class="py-5" id="tentang">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="fw-800 mb-4">Kenapa Memilih <span style="color:var(--primary)">KostKu?</span></h2>
                @foreach([
                    ['icon' => 'fas fa-shield-alt', 'color' => 'text-primary', 'title' => 'Terpercaya & Terverifikasi', 'desc' => 'Setiap kost telah diverifikasi oleh tim kami'],
                    ['icon' => 'fas fa-search-dollar', 'color' => 'text-success', 'title' => 'Harga Transparan', 'desc' => 'Tidak ada biaya tersembunyi, semua harga sudah jelas'],
                    ['icon' => 'fas fa-headset', 'color' => 'text-warning', 'title' => 'Dukungan 24/7', 'desc' => 'Tim customer service kami siap membantu kapan saja'],
                    ['icon' => 'fas fa-mobile-alt', 'color' => 'text-danger', 'title' => 'Booking Mudah', 'desc' => 'Proses booking hanya butuh beberapa menit'],
                ] as $i => $item)
                <div class="d-flex gap-3 mb-4" data-aos="fade-right" data-aos-delay="{{ $i * 80 }}">
                    <div class="flex-shrink-0">
                        <i class="{{ $item['icon'] }} {{ $item['color'] }}" style="font-size:1.5rem"></i>
                    </div>
                    <div>
                        <h6 class="fw-700 mb-1">{{ $item['title'] }}</h6>
                        <p class="text-muted small mb-0">{{ $item['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="card p-4 border-0" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:24px">
                    <div class="row g-3">
                        @foreach([
                            ['value' => $stats['total_kost'] . '+', 'label' => 'Kost Aktif', 'icon' => 'fas fa-building'],
                            ['value' => $stats['total_users'] . '+', 'label' => 'Pengguna', 'icon' => 'fas fa-users'],
                            ['value' => $stats['total_cities'] . '+', 'label' => 'Kota', 'icon' => 'fas fa-map-marked'],
                            ['value' => '4.8★', 'label' => 'Rating', 'icon' => 'fas fa-star'],
                        ] as $stat)
                        <div class="col-6">
                            <div class="card text-center p-3">
                                <i class="{{ $stat['icon'] }} text-primary mb-2" style="font-size:1.5rem"></i>
                                <div style="font-size:1.6rem;font-weight:800;color:#0f172a">{{ $stat['value'] }}</div>
                                <div class="text-muted small">{{ $stat['label'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
@guest
<section class="py-5" style="background:linear-gradient(135deg,#1e3a8a,#2563eb)">
    <div class="container text-center text-white" data-aos="fade-up">
        <h2 class="fw-800 mb-3">Siap Temukan Kost Impianmu?</h2>
        <p class="lead mb-4" style="opacity:0.9">Daftar gratis dan mulai cari kost terbaik sekarang</p>
        <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-5">
            <i class="fas fa-user-plus me-2"></i>Daftar Sekarang — Gratis!
        </a>
    </div>
</section>
@endguest

@endsection
