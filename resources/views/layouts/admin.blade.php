<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — KostKu Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --accent: #f59e0b;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-hover: rgba(37,99,235,0.15);
            --sidebar-active: #2563eb;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            color: #334155;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 1.3rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand .icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .sidebar-brand span { color: var(--accent); }

        .sidebar-menu {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .menu-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #475569;
            padding: 12px 12px 6px;
        }

        .nav-item-admin a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            margin-bottom: 2px;
        }

        .nav-item-admin a:hover {
            background: var(--sidebar-hover);
            color: white;
        }

        .nav-item-admin a.active {
            background: var(--primary);
            color: white;
        }

        .nav-item-admin a .icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(255,255,255,0.08);
            font-size: 0.85rem;
        }

        .nav-item-admin a.active .icon {
            background: rgba(255,255,255,0.2);
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .admin-profile-mini {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 12px;
            background: rgba(255,255,255,0.05);
            color: white;
        }

        .admin-profile-mini img {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            object-fit: cover;
        }

        .admin-profile-mini .name { font-size: 0.85rem; font-weight: 600; }
        .admin-profile-mini .role { font-size: 0.7rem; color: var(--accent); }

        /* MAIN */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin 0.3s;
        }

        /* TOP BAR */
        .topbar {
            background: white;
            padding: 16px 24px;
            box-shadow: 0 1px 10px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .topbar .page-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
        }

        /* CARDS */
        .stat-card {
            border: none;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            background: white;
            transition: transform 0.2s;
        }

        .stat-card:hover { transform: translateY(-3px); }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #f1f5f9;
            padding: 16px 20px;
            border-radius: 16px 16px 0 0 !important;
        }

        .table th {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            background: #f8fafc;
            border: none;
        }

        .table td { vertical-align: middle; font-size: 0.9rem; }

        /* BADGES */
        .badge { border-radius: 8px; font-weight: 600; }

        /* FORM */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            padding: 10px 14px;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #374151;
        }

        /* BREADCRUMB */
        .breadcrumb-item a { color: var(--primary); text-decoration: none; }
        .breadcrumb-item + .breadcrumb-item::before { color: #94a3b8; }

        /* BTN */
        .btn { border-radius: 10px; font-weight: 600; font-size: 0.9rem; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); }

        /* RESPONSIVE */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }

        /* OVERLAY */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
        }

        .sidebar-overlay.show { display: block; }

        /* ANIMATIONS */
        /* Catatan: sengaja TIDAK pakai CSS `animation` di sini, karena properti
           `animation` membuat elemen jadi "stacking context" baru selamanya
           (bukan cuma saat animasi berjalan). Ini bisa menjebak modal Bootstrap
           (position: fixed) di dalamnya sehingga modal terlihat tapi tidak bisa
           diklik. Sebagai gantinya kita pakai transition + class toggle. */
        .animate-in {
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .animate-in.is-visible { opacity: 1; }
    </style>

    <script>
    // Trigger fade-in setelah DOM siap (menggantikan animation-based fadeIn)
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.animate-in').forEach(function(el) {
            requestAnimationFrame(function() { el.classList.add('is-visible'); });
        });
    });
    </script>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a class="sidebar-brand" href="{{ route('admin.dashboard') }}">
            <div class="icon"><i class="fas fa-home text-white"></i></div>
            Kost<span>Ku</span>
        </a>

        <div class="sidebar-menu">
            <div class="menu-label">Utama</div>

            <div class="nav-item-admin">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-tachometer-alt"></i></span>
                    Dashboard
                </a>
            </div>

            <div class="menu-label mt-2">Manajemen</div>

            <div class="nav-item-admin">
                <a href="{{ route('admin.kost.index') }}" class="{{ request()->routeIs('admin.kost.*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-building"></i></span>
                    Data Kost
                </a>
            </div>

            <div class="nav-item-admin">
                <a href="{{ route('admin.booking.index') }}" class="{{ request()->routeIs('admin.booking.*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-calendar-check"></i></span>
                    Booking
                    @php $pendingBookings = \App\Models\Booking::where('booking_status','pending')->count(); @endphp
                    @if($pendingBookings > 0)
                        <span class="ms-auto badge bg-danger rounded-pill">{{ $pendingBookings }}</span>
                    @endif
                </a>
            </div>

            <div class="nav-item-admin">
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-users"></i></span>
                    Pengguna
                </a>
            </div>

            <div class="nav-item-admin">
                <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-star"></i></span>
                    Ulasan
                    @php $pendingReviews = \App\Models\Review::where('is_approved',false)->count(); @endphp
                    @if($pendingReviews > 0)
                        <span class="ms-auto badge bg-warning rounded-pill">{{ $pendingReviews }}</span>
                    @endif
                </a>
            </div>

            <div class="menu-label mt-2">Laporan</div>

            <div class="nav-item-admin">
                <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-chart-bar"></i></span>
                    Laporan
                </a>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="admin-profile-mini">
                <img src="{{ auth()->user()->avatar_url }}" alt="">
                <div>
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role">Administrator</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button class="btn btn-sm btn-outline-secondary w-100" style="border-radius:10px;color:#94a3b8;border-color:rgba(255,255,255,0.1)">
                    <i class="fas fa-sign-out-alt me-2"></i>Keluar
                </button>
            </form>
        </div>
    </nav>

    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="page-title">@yield('page-title', 'Dashboard')</div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="font-size:0.78rem">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('home') }}" class="btn btn-sm btn-light" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i>Lihat Website
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 animate-in">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('vendor/axios/axios.min.js') }}"></script>
    <script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>

    <script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });

    // Delete confirmation
    document.querySelectorAll('[data-confirm-delete]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Hapus Data?',
                text: this.dataset.confirmDelete || 'Data tidak dapat dipulihkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });

    // Auto dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(a => new bootstrap.Alert(a).close());
    }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
