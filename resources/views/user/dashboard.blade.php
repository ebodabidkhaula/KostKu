@extends('layouts.app')

@section('title', 'Dashboard Saya — KostKu')

@push('styles')
<style>
.user-dashboard {
    min-height: calc(100vh - 80px);
    background: #f8fafc;
}

.dashboard-sidebar {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    overflow: hidden;
    position: sticky;
    top: 90px;
}

.profile-header {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    padding: 28px 20px;
    text-align: center;
    color: white;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,0.4);
    object-fit: cover;
    margin-bottom: 10px;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    color: #64748b;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    border-left: 3px solid transparent;
    transition: all 0.2s;
}

.sidebar-nav a:hover {
    background: #f8fafc;
    color: var(--primary);
    border-left-color: var(--primary);
}

.sidebar-nav a.active {
    background: #eff6ff;
    color: var(--primary);
    border-left-color: var(--primary);
}

.stat-card-user {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    border-left: 4px solid;
}

.booking-status-pending { color: #f59e0b; }
.booking-status-active { color: #10b981; }
.booking-status-completed { color: #2563eb; }
.booking-status-cancelled { color: #ef4444; }
</style>
@endpush

@section('content')
<div class="user-dashboard py-4">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="dashboard-sidebar">
                    <div class="profile-header">
                        <img src="{{ auth()->user()->avatar_url }}" class="profile-avatar" alt="">
                        <div class="fw-700">{{ auth()->user()->name }}</div>
                        <div style="font-size:0.8rem;opacity:0.8">{{ auth()->user()->email }}</div>
                        <div class="mt-2">
                            <span class="badge bg-light text-primary" style="font-size:0.7rem">
                                {{ auth()->user()->occupation ?? 'Pengguna' }}
                            </span>
                        </div>
                    </div>
                    <div class="sidebar-nav py-2">
                        <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home" style="width:16px"></i> Dashboard
                        </a>
                        <a href="{{ route('user.booking.index') }}" class="{{ request()->routeIs('user.booking.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check" style="width:16px"></i> Booking Saya
                        </a>
                        <a href="{{ route('user.profile') }}" class="{{ request()->routeIs('user.profile') ? 'active' : '' }}">
                            <i class="fas fa-user" style="width:16px"></i> Profil Saya
                        </a>
                        <a href="{{ route('kost.index') }}">
                            <i class="fas fa-search" style="width:16px"></i> Cari Kost
                        </a>
                        <hr class="my-1">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" style="width:100%;background:none;border:none;text-align:left" class="text-danger">
                                <div class="d-flex align-items-center gap-2 px-4 py-2" style="font-size:0.9rem;font-weight:500">
                                    <i class="fas fa-sign-out-alt" style="width:16px"></i> Keluar
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <h4 class="fw-800 mb-4">Dashboard Saya</h4>

                <!-- Stat Cards -->
                <div class="row g-3 mb-4">
                    @php
                    $totalBookings = auth()->user()->bookings()->count();
                    $activeBookings = auth()->user()->bookings()->where('booking_status','active')->count();
                    $pendingBookings = auth()->user()->bookings()->where('booking_status','pending')->count();
                    @endphp
                    @foreach([
                        ['label' => 'Total Booking', 'value' => $totalBookings, 'icon' => 'fas fa-receipt', 'color' => '#2563eb'],
                        ['label' => 'Booking Aktif', 'value' => $activeBookings, 'icon' => 'fas fa-home', 'color' => '#10b981'],
                        ['label' => 'Menunggu', 'value' => $pendingBookings, 'icon' => 'fas fa-clock', 'color' => '#f59e0b'],
                        ['label' => 'Total Pengeluaran', 'value' => 'Rp ' . number_format($total_spent, 0, ',', '.'), 'icon' => 'fas fa-wallet', 'color' => '#8b5cf6'],
                    ] as $stat)
                    <div class="col-md-3 col-6">
                        <div class="stat-card-user" style="border-color:{{ $stat['color'] }}">
                            <i class="{{ $stat['icon'] }}" style="color:{{ $stat['color'] }};font-size:1.2rem"></i>
                            <div class="fw-800 mt-2" style="font-size:1.3rem">{{ $stat['value'] }}</div>
                            <div class="text-muted small">{{ $stat['label'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Active Booking -->
                @if($active_booking)
                <div class="card mb-4" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:none;border-radius:16px">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-700 mb-0">🏠 Kost Aktif Saat Ini</h6>
                            <span class="badge bg-success">Aktif</span>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $active_booking->kost->thumbnail_url }}" class="rounded-3"
                                style="width:80px;height:70px;object-fit:cover;flex-shrink:0" alt="">
                            <div class="flex-grow-1">
                                <div class="fw-700">{{ $active_booking->kost->name }}</div>
                                <div class="text-muted small mb-1">
                                    <i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $active_booking->kost->city }}
                                </div>
                                <div class="small">
                                    Check-in: <strong>{{ $active_booking->check_in_date->format('d M Y') }}</strong>
                                    &bull; Kode: <strong class="text-primary">{{ $active_booking->booking_code }}</strong>
                                </div>
                            </div>
                            <a href="{{ route('user.booking.show', $active_booking) }}" class="btn btn-primary btn-sm">
                                Detail <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Recent Bookings -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="fw-700 mb-0">Riwayat Booking</h6>
                        <a href="{{ route('user.booking.index') }}" class="btn btn-sm btn-light">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Kode</th>
                                        <th>Kost</th>
                                        <th>Check-in</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bookings as $b)
                                    <tr>
                                        <td class="ps-3 fw-600 text-primary small">{{ $b->booking_code }}</td>
                                        <td class="small">{{ Str::limit($b->kost->name ?? '-', 22) }}</td>
                                        <td class="small">{{ $b->check_in_date->format('d M Y') }}</td>
                                        <td class="fw-600 small">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                                        <td>{!! $b->status_badge !!}</td>
                                        <td>
                                            <a href="{{ route('user.booking.show', $b) }}" class="btn btn-sm btn-light">
                                                <i class="fas fa-eye text-primary"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-calendar-times fa-2x text-muted mb-2 d-block"></i>
                                            <p class="text-muted mb-2">Belum ada riwayat booking</p>
                                            <a href="{{ route('kost.index') }}" class="btn btn-primary btn-sm">Cari Kost Sekarang</a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
