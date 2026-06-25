@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan & Statistik')
@section('breadcrumb')
<li class="breadcrumb-item active">Laporan</li>
@endsection

@section('content')

<!-- Year Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="d-flex align-items-center gap-2">
            <label class="form-label mb-0 fw-600">Tahun:</label>
            <select name="year" class="form-select" style="width:150px" onchange="this.form.submit()">
                @foreach($years as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    @foreach([
        ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($summary['total_revenue'], 0, ',', '.'), 'icon' => 'fas fa-money-bill-wave', 'color' => '#10b981'],
        ['label' => 'Total Booking', 'value' => number_format($summary['total_bookings']), 'icon' => 'fas fa-receipt', 'color' => '#2563eb'],
        ['label' => 'Rata-rata Booking', 'value' => 'Rp ' . number_format($summary['avg_booking_value'], 0, ',', '.'), 'icon' => 'fas fa-chart-line', 'color' => '#f59e0b'],
        ['label' => 'Tingkat Hunian', 'value' => $summary['occupancy_rate'] . '%', 'icon' => 'fas fa-bed', 'color' => '#8b5cf6'],
    ] as $stat)
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label mb-1">{{ $stat['label'] }}</div>
                    <div class="stat-value" style="font-size:1.5rem">{{ $stat['value'] }}</div>
                </div>
                <div class="stat-icon" style="background:{{ $stat['color'] }}20;color:{{ $stat['color'] }}">
                    <i class="{{ $stat['icon'] }}"></i>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <!-- Revenue Trend -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="fw-700 mb-0">Tren Pendapatan {{ $year }}</h6></div>
            <div class="card-body"><canvas id="revenueChart" height="220"></canvas></div>
        </div>
    </div>

    <!-- User Registration Trend -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="fw-700 mb-0">Pendaftaran Pengguna {{ $year }}</h6></div>
            <div class="card-body"><canvas id="userChart" height="220"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Top Kost -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h6 class="fw-700 mb-0">Top 10 Kost Berdasarkan Pendapatan</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Nama Kost</th>
                                <th>Kota</th>
                                <th>Booking</th>
                                <th>Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topKosts as $i => $kost)
                            <tr>
                                <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                                <td class="fw-600 small">{{ $kost->name }}</td>
                                <td class="small">{{ $kost->city }}</td>
                                <td><span class="badge bg-info">{{ $kost->total_bookings }}</span></td>
                                <td class="fw-700 small text-primary">Rp {{ number_format($kost->revenue, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Status -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h6 class="fw-700 mb-0">Distribusi Status Booking</h6></div>
            <div class="card-body"><canvas id="statusChart" height="240"></canvas></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

// Revenue chart
const revenueData = @json($revenueByMonth);
const revenueArr = Array(12).fill(0);
revenueData.forEach(item => revenueArr[item.month - 1] = parseFloat(item.total) || 0);

new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: monthNames,
        datasets: [{
            label: 'Pendapatan',
            data: revenueArr,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.1)',
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(0) + 'jt' } }
        }
    }
});

// User registration chart
const userData = @json($userRegistrations);
const userArr = Array(12).fill(0);
userData.forEach(item => userArr[item.month - 1] = item.count);

new Chart(document.getElementById('userChart'), {
    type: 'bar',
    data: {
        labels: monthNames,
        datasets: [{
            label: 'Pendaftar Baru',
            data: userArr,
            backgroundColor: '#10b981',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Status pie
const statusData = @json($bookingByStatus);
const statusLabels = { pending: 'Menunggu', confirmed: 'Dikonfirmasi', active: 'Aktif', completed: 'Selesai', cancelled: 'Dibatalkan' };
const statusColors = { pending: '#f59e0b', confirmed: '#3b82f6', active: '#10b981', completed: '#6366f1', cancelled: '#ef4444' };

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusData.map(s => statusLabels[s.booking_status] || s.booking_status),
        datasets: [{
            data: statusData.map(s => s.count),
            backgroundColor: statusData.map(s => statusColors[s.booking_status] || '#94a3b8'),
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endpush
