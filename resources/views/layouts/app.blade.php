<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KostKu - Platform Kost Terpercaya')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Bootstrap -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="{{ asset('vendor/aos/aos.css') }}" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --accent: #f59e0b;
            --dark: #0f172a;
            --text: #334155;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 24px rgba(37,99,235,0.10);
            --radius: 16px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            background: var(--light-bg);
        }

        /* NAVBAR */
        .navbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            box-shadow: 0 1px 20px rgba(0,0,0,0.08);
            padding: 12px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
            letter-spacing: -0.5px;
        }

        .navbar-brand span { color: var(--accent); }

        .nav-link {
            font-weight: 500;
            color: var(--text) !important;
            transition: color 0.2s;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s;
        }

        .nav-link:hover::after, .nav-link.active::after { width: 80%; }
        .nav-link:hover { color: var(--primary) !important; }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(37,99,235,0.35);
        }

        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        /* CARD */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(37,99,235,0.18);
        }

        /* KOST CARD */
        .kost-card .kost-img {
            height: 220px;
            object-fit: cover;
            border-radius: var(--radius) var(--radius) 0 0;
        }

        .kost-card .badge-type {
            position: absolute;
            top: 12px;
            left: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 8px;
            padding: 4px 12px;
        }

        .kost-card .badge-featured {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--accent);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 8px;
            padding: 3px 10px;
        }

        .price-tag {
            color: var(--primary);
            font-weight: 800;
            font-size: 1.1rem;
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #0ea5e9 100%);
            color: white;
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            top: -200px;
            right: -100px;
        }

        .hero::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(245,158,11,0.15);
            border-radius: 50%;
            bottom: -100px;
            left: -50px;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            line-height: 1.2;
        }

        /* SEARCH BOX */
        .search-box {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        /* FOOTER */
        footer {
            background: var(--dark);
            color: #94a3b8;
        }

        footer a { color: #94a3b8; text-decoration: none; transition: color 0.2s; }
        footer a:hover { color: white; }

        /* ANIMATIONS */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .floating { animation: float 3s ease-in-out infinite; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in-up { animation: fadeInUp 0.6s ease forwards; }

        /* ALERTS */
        .alert { border-radius: 12px; border: none; font-weight: 500; }

        /* PAGINATION */
        .pagination .page-link {
            border-radius: 8px;
            margin: 0 3px;
            color: var(--primary);
            border: none;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .hero { padding: 60px 0 50px; }
            .search-box { padding: 16px; }
        }

        /* RATING STARS */
        .stars { color: #f59e0b; }

        /* FACILITY BADGE */
        .facility-badge {
            background: #eff6ff;
            color: var(--primary);
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin: 2px;
        }

        /* SPA LOADING */
        #page-loader {
            position: fixed;
            inset: 0;
            background: white;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.3s;
        }

        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e2e8f0;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* SPA Content transition */
        #app-content { transition: opacity 0.2s; }
        #app-content.loading { opacity: 0.5; }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Page Loader -->
    <div id="page-loader">
        <div class="loader-spinner"></div>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                Kost<span>Ku</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kost.*') ? 'active' : '' }}" href="{{ route('kost.index') }}">Cari Kost</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang</a>
                    </li>
                </ul>

                <div class="d-flex gap-2 align-items-center">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-3">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-3">Daftar</a>
                    @else
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-warning me-1">
                                <i class="fas fa-cog me-1"></i>Admin
                            </a>
                        @endif
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                                <img src="{{ auth()->user()->avatar_url }}" alt="" class="rounded-circle" width="28" height="28" style="object-fit:cover">
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}"><i class="fas fa-home me-2 text-primary"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.profile') }}"><i class="fas fa-user me-2 text-primary"></i>Profil</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.booking.index') }}"><i class="fas fa-bed me-2 text-primary"></i>Booking Saya</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Keluar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <!-- Main Content (SPA) -->
    <main id="app-content">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="text-white fw-800 mb-3">Kost<span style="color:#f59e0b">Ku</span></h5>
                    <p class="small">Platform pencarian kost terpercaya dengan ribuan pilihan di seluruh Indonesia. Temukan hunian nyaman impianmu.</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#"><i class="fab fa-youtube fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-white mb-3">Layanan</h6>
                    <ul class="list-unstyled small">
                        <li><a href="{{ route('kost.index') }}">Cari Kost</a></li>
                        <li><a href="#">Pasang Iklan</a></li>
                        <li><a href="#">Kost Eksklusif</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="text-white mb-3">Bantuan</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Hubungi Kami</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white mb-3">Kontak</h6>
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-envelope me-2"></i>info@kostku.id</li>
                        <li><i class="fas fa-phone me-2"></i>0800-1234-5678</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary mt-4">
            <p class="text-center small mb-0">&copy; {{ date('Y') }} KostKu. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('vendor/axios/axios.min.js') }}"></script>

    <script>
    // Initialize AOS
    AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic' });

    // Page Loader
    window.addEventListener('load', function() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => loader.style.display = 'none', 300);
        }
    });

    // SPA Navigation with AJAX (for same-origin links)
    const appContent = document.getElementById('app-content');
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

    // Delete confirmation
    document.querySelectorAll('[data-confirm-delete]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Data?',
                text: this.dataset.confirmDelete || 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(a => {
            new bootstrap.Alert(a).close();
        });
    }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
