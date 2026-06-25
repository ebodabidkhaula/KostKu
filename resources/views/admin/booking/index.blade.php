@extends('layouts.admin')

@section('title', 'Manajemen Booking')
@section('page-title', 'Manajemen Booking')
@section('breadcrumb')
<li class="breadcrumb-item active">Booking</li>
@endsection

@section('content')

<!-- Filter Bar -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.booking.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Cari kode, nama, kost..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach(['pending'=>'Menunggu','confirmed'=>'Dikonfirmasi','active'=>'Aktif','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $k => $v)
                    <option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment" class="form-select">
                    <option value="">Semua Pembayaran</option>
                    @foreach(['pending'=>'Menunggu','paid'=>'Lunas','partial'=>'Sebagian','refunded'=>'Refund'] as $k => $v)
                    <option value="{{ $k }}" {{ request('payment') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                <a href="{{ route('admin.booking.index') }}" class="btn btn-light">Reset</a>
            </div>
            <div class="col-md-auto ms-auto">
                <a href="{{ route('admin.booking.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Tambah Booking
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="fw-700 mb-0">Daftar Booking <span class="badge bg-primary ms-2">{{ $bookings->total() }}</span></h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Kode</th>
                        <th>Penyewa</th>
                        <th>Kost</th>
                        <th>Check-in</th>
                        <th>Durasi</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th class="text-center" style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                    <tr>
                        <td class="ps-3 fw-600 text-primary small">{{ $b->booking_code }}</td>
                        <td>
                            <div class="fw-600 small">{{ $b->user->name ?? '-' }}</div>
                            <div class="text-muted" style="font-size:0.72rem">{{ $b->user->phone ?? '' }}</div>
                        </td>
                        <td class="small">{{ Str::limit($b->kost->name ?? '-', 22) }}</td>
                        <td class="small">{{ $b->check_in_date->format('d M Y') }}</td>
                        <td class="small">{{ $b->duration_months }} bulan</td>
                        <td class="fw-600 small">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                        <td>
                            @switch($b->payment_status)
                                @case('paid') <span class="badge bg-success">Lunas</span> @break
                                @case('partial') <span class="badge bg-info">Sebagian</span> @break
                                @case('refunded') <span class="badge bg-secondary">Refund</span> @break
                                @default <span class="badge bg-warning">Menunggu</span>
                            @endswitch
                        </td>
                        <td>{!! $b->status_badge !!}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.booking.show', $b) }}" class="btn btn-sm btn-light" title="Detail">
                                <i class="fas fa-eye text-primary"></i>
                            </a>
                            <form action="{{ route('admin.booking.destroy', $b) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light" data-confirm-delete="Hapus booking {{ $b->booking_code }}?">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">Belum ada data booking</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($bookings->hasPages())
    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
        <small class="text-muted">Menampilkan {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} dari {{ $bookings->total() }}</small>
        {{ $bookings->links() }}
    </div>
    @endif
</div>

@endsection
