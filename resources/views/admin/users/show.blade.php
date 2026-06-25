@extends('layouts.admin')

@section('title', 'Detail Pengguna')
@section('page-title', 'Detail Pengguna')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Pengguna</a></li>
<li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')

<div class="row g-4">
    <!-- Profile -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center p-4">
                <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="100" height="100" style="object-fit:cover" alt="">
                <h5 class="fw-700 mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }} mb-3">
                    {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                </span>

                <div class="text-start mt-3">
                    @foreach([
                        ['icon' => 'fas fa-phone', 'label' => 'Telepon', 'value' => $user->phone ?? '-'],
                        ['icon' => 'fas fa-venus-mars', 'label' => 'Gender', 'value' => $user->gender ?? '-'],
                        ['icon' => 'fas fa-briefcase', 'label' => 'Pekerjaan', 'value' => $user->occupation ?? '-'],
                        ['icon' => 'fas fa-id-card', 'label' => 'NIK', 'value' => $user->nik ?? '-'],
                        ['icon' => 'fas fa-birthday-cake', 'label' => 'Tanggal Lahir', 'value' => $user->dob?->format('d M Y') ?? '-'],
                        ['icon' => 'fas fa-map-marker-alt', 'label' => 'Alamat', 'value' => $user->address ?? '-'],
                        ['icon' => 'fas fa-calendar', 'label' => 'Bergabung', 'value' => $user->created_at->format('d M Y')],
                    ] as $item)
                    <div class="d-flex gap-3 mb-2">
                        <i class="{{ $item['icon'] }} text-primary" style="width:18px;margin-top:3px"></i>
                        <div>
                            <div class="text-muted" style="font-size:0.72rem">{{ $item['label'] }}</div>
                            <div class="small fw-600">{{ $item['value'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="d-flex gap-2 mt-3">
                    <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="flex-grow-1">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-warning w-100">
                            <i class="fas fa-{{ $user->status === 'active' ? 'lock' : 'unlock' }} me-1"></i>
                            {{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking History -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><h6 class="fw-700 mb-0">Riwayat Booking ({{ $user->bookings->count() }})</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th class="ps-3">Kode</th><th>Kost</th><th>Check-in</th><th>Total</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($user->bookings as $b)
                            <tr>
                                <td class="ps-3 fw-600 text-primary small">{{ $b->booking_code }}</td>
                                <td class="small">{{ $b->kost->name ?? '-' }}</td>
                                <td class="small">{{ $b->check_in_date->format('d M Y') }}</td>
                                <td class="fw-600 small">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                                <td>{!! $b->status_badge !!}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada booking</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        <div class="card">
            <div class="card-header"><h6 class="fw-700 mb-0">Ulasan ({{ $user->reviews->count() }})</h6></div>
            <div class="card-body">
                @forelse($user->reviews as $review)
                <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                    <div>
                        <div class="fw-600 small">{{ $review->kost->name ?? '-' }}</div>
                        <div class="stars" style="font-size:0.7rem">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star" style="color:{{ $review->rating >= $i ? '#f59e0b' : '#d1d5db' }}"></i>
                            @endfor
                        </div>
                        <p class="text-muted small mb-0 mt-1">{{ $review->comment }}</p>
                    </div>
                    <span class="badge bg-{{ $review->is_approved ? 'success' : 'warning' }}">
                        {{ $review->is_approved ? 'Disetujui' : 'Pending' }}
                    </span>
                </div>
                @empty
                <p class="text-muted text-center mb-0">Belum ada ulasan</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
