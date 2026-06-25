@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    @foreach([
        ['label' => 'Total Kost', 'value' => number_format($stats['total_kost']), 'icon' => 'fas fa-building', 'color' => '#2563eb', 'bg' => '#eff6ff'],
        ['label' => 'Total Pengguna', 'value' => number_format($stats['total_users']), 'icon' => 'fas fa-users', 'color' => '#10b981', 'bg' => '#f0fdf4'],
        ['label' => 'Booking Aktif', 'value' => number_format($stats['active_bookings']), 'icon' => 'fas fa-calendar-check', 'color' => '#f59e0b', 'bg' => '#fffbeb'],
        ['label' => 'Booking Pending', 'value' => number_format($stats['pending_bookings']), 'icon' => 'fas fa-clock', 'color' => '#ef4444', 'bg' => '#fef2f2'],
        ['label' => 'Total Booking', 'value' => number_format($stats['total_bookings']), 'icon' => 'fas fa-receipt', 'color' => '#8b5cf6', 'bg' => '#f5f3ff'],
        ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($stats['total_revenue'], 0, ',', '.'), 'icon' => 'fas fa-money-bill-wave', 'color' => '#0ea5e9', 'bg' => '#f0f9ff'],
        ['label' => 'Total Ulasan', 'value' => number_format($stats['total_reviews']), 'icon' => 'fas fa-star', 'color' => '#f97316', 'bg' => '#fff7ed'],
        ['label' => 'Ulasan Pending', 'value' => number_format($stats['pending_reviews']), 'icon' => 'fas fa-comment-dots', 'color' => '#6366f1', 'bg' => '#eef2ff'],
    ] as $stat)
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">{{ $stat['label'] }}</div>
                    <div class="stat-value">{{ $stat['value'] }}</div>
                </div>
                <div class="stat-icon" style="background:{{ $stat['bg'] }};color:{{ $stat['color'] }}">
                    <i class="{{ $stat['icon'] }}"></i>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Revenue Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-700 mb-0">Pendapatan Bulanan {{ date('Y') }}</h6>
                    <small class="text-muted">Total booking yang sudah dibayar</small>
                </div>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Booking Status Pie -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-700 mb-0">Status Booking</h6>
            </div>
            <div class="card-body">
                <canvas id="bookingChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row g-4">
    <!-- Recent Bookings -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-700 mb-0">Booking Terbaru</h6>
                <a href="{{ route('admin.booking.index') }}" class="btn btn-sm btn-light">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Kode</th>
                                <th>Penyewa</th>
                                <th>Kost</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_bookings as $booking)
                            <tr>
                                <td class="ps-3">
                                    <a href="{{ route('admin.booking.show', $booking) }}"
                                        class="text-primary fw-600 text-decoration-none">{{ $booking->booking_code }}</a>
                                </td>
                                <td>{{ $booking->user->name ?? '-' }}</td>
                                <td>{{ Str::limit($booking->kost->name ?? '-', 25) }}</td>
                                <td class="fw-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                <td>{!! $booking->status_badge !!}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada booking</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Kost -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-700 mb-0">Kost Terpopuler</h6>
            </div>
            <div class="card-body">
                @forelse($popular_kosts as $i => $kost)
                <div class="d-flex align-items-center gap-3 mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                    <div class="fw-800 text-muted" style="width:20px;font-size:1.1rem">{{ $i + 1 }}</div>
                    <div class="flex-grow-1">
                        <div class="fw-600 small">{{ Str::limit($kost->name, 30) }}</div>
                        <div class="text-muted" style="font-size:0.75rem">
                            <i class="fas fa-calendar me-1"></i>{{ $kost->bookings_count }} booking
                        </div>
                    </div>
                    <span class="badge" style="background:#eff6ff;color:#2563eb">
                        {{ $kost->type }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center small">Belum ada data</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="fw-700 mb-0">Aksi Cepat</h6>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('admin.kost.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>Tambah Kost Baru
                </a>
                <a href="{{ route('admin.booking.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-calendar-plus me-2"></i>Buat Booking Baru
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-star me-2"></i>Kelola Ulasan
                </a>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-info btn-sm text-white">
                    <i class="fas fa-chart-bar me-2"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Revenue Chart
const monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
const revenueData = @json($monthly_revenue);

const monthlyRevenue = Array(12).fill(0);
revenueData.forEach(item => {
    monthlyRevenue[item.month - 1] = parseFloat(item.total) || 0;
});

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: monthNames,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: monthlyRevenue,
            backgroundColor: 'rgba(37,99,235,0.15)',
            borderColor: '#2563eb',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f1f5f9' },
                ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(0) + 'jt' }
            },
            x: { grid: { display: false } }
        }
    }
});

// Booking Status Chart
new Chart(document.getElementById('bookingChart'), {
    type: 'doughnut',
    data: {
        labels: ['Aktif', 'Pending', 'Selesai', 'Dibatalkan'],
        datasets: [{
            data: [
                {{ $stats['active_bookings'] }},
                {{ $stats['pending_bookings'] }},
                {{ \App\Models\Booking::where('booking_status','completed')->count() }},
                {{ \App\Models\Booking::where('booking_status','cancelled')->count() }},
            ],
            backgroundColor: ['#10b981', '#f59e0b', '#2563eb', '#ef4444'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 12, font: { size: 12 } } }
        }
    }
});
</script>
@endpush
