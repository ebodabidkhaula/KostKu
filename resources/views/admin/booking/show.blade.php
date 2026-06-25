@extends('layouts.admin')

@section('title', 'Detail Booking')
@section('page-title', 'Detail Booking')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.booking.index') }}">Booking</a></li>
<li class="breadcrumb-item active">{{ $booking->booking_code }}</li>
@endsection

@section('content')

<div class="row g-4">
    <!-- LEFT: Info -->
    <div class="col-lg-8">
        <!-- Booking Info -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-700 mb-0">Informasi Booking</h6>
                <span class="badge bg-primary">{{ $booking->booking_code }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Penyewa</div>
                        <div class="fw-600">{{ $booking->user->name }}</div>
                        <div class="text-muted small">{{ $booking->user->email }} &bull; {{ $booking->user->phone }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Kost</div>
                        <div class="fw-600">{{ $booking->kost->name }}</div>
                        <div class="text-muted small">{{ $booking->kost->city }}, {{ $booking->kost->province }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small mb-1">Check-in</div>
                        <div class="fw-600">{{ $booking->check_in_date->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small mb-1">Durasi</div>
                        <div class="fw-600">{{ $booking->duration_months }} Bulan</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small mb-1">Total Harga</div>
                        <div class="fw-700 text-primary">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small mb-1">Deposit</div>
                        <div class="fw-600">Rp {{ number_format($booking->deposit, 0, ',', '.') }}</div>
                    </div>
                    @if($booking->room)
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Kamar</div>
                        <div class="fw-600">{{ $booking->room->room_number }} (Lantai {{ $booking->room->floor }})</div>
                    </div>
                    @endif
                    @if($booking->payment_method)
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Metode Pembayaran</div>
                        <div class="fw-600">{{ str_replace('_', ' ', ucwords($booking->payment_method)) }}</div>
                    </div>
                    @endif
                    @if($booking->notes)
                    <div class="col-12">
                        <div class="text-muted small mb-1">Catatan</div>
                        <div class="p-2 bg-light rounded-2 small">{{ $booking->notes }}</div>
                    </div>
                    @endif
                    @if($booking->special_requests)
                    <div class="col-12">
                        <div class="text-muted small mb-1">Permintaan Khusus</div>
                        <div class="p-2 bg-light rounded-2 small">{{ $booking->special_requests }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Proof -->
        @if($booking->payment_proof)
        <div class="card mb-4">
            <div class="card-header"><h6 class="fw-700 mb-0">Bukti Pembayaran</h6></div>
            <div class="card-body">
                <img src="{{ asset('storage/payments/' . $booking->payment_proof) }}" alt="Bukti Pembayaran"
                    class="img-fluid rounded-3" style="max-height:400px">
            </div>
        </div>
        @endif

        <!-- Review -->
        @if($booking->review)
        <div class="card mb-4">
            <div class="card-header"><h6 class="fw-700 mb-0">Ulasan Penyewa</h6></div>
            <div class="card-body">
                <div class="stars mb-2">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star" style="color:{{ $booking->review->rating >= $i ? '#f59e0b' : '#d1d5db' }}"></i>
                    @endfor
                </div>
                <p class="text-muted mb-0">{{ $booking->review->comment }}</p>
            </div>
        </div>
        @endif

        <!-- Kost Photos -->
        @if($booking->kost->photos->count())
        <div class="card">
            <div class="card-header"><h6 class="fw-700 mb-0">Foto Kost</h6></div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($booking->kost->photos->take(4) as $photo)
                    <div class="col-3">
                        <img src="{{ $photo->url }}" class="img-fluid rounded-2" style="height:80px;width:100%;object-fit:cover" alt="">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- RIGHT: Update Status -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h6 class="fw-700 mb-0">Update Status</h6></div>
            <div class="card-body">
                <form action="{{ route('admin.booking.update', $booking) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Status Booking</label>
                        <select name="booking_status" class="form-select">
                            @foreach(['pending'=>'Menunggu','confirmed'=>'Dikonfirmasi','active'=>'Aktif','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $k => $v)
                            <option value="{{ $k }}" {{ $booking->booking_status === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Pembayaran</label>
                        <select name="payment_status" class="form-select">
                            @foreach(['pending'=>'Menunggu','paid'=>'Lunas','partial'=>'Sebagian','refunded'=>'Refund'] as $k => $v)
                            <option value="{{ $k }}" {{ $booking->payment_status === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="payment_method" class="form-select">
                            <option value="">-- Pilih --</option>
                            @foreach(['transfer_bca'=>'Transfer BCA','transfer_mandiri'=>'Transfer Mandiri','gopay'=>'GoPay','ovo'=>'OVO','tunai'=>'Tunai','qris'=>'QRIS'] as $k => $v)
                            <option value="{{ $k }}" {{ $booking->payment_method === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Bukti Pembayaran</label>
                        <input type="file" name="payment_proof" class="form-control" accept="image/*,application/pdf">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Admin</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $booking->notes }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card mt-4">
            <div class="card-header"><h6 class="fw-700 mb-0">Riwayat</h6></div>
            <div class="card-body">
                <div class="d-flex gap-2 mb-3">
                    <div class="bg-primary rounded-circle" style="width:10px;height:10px;margin-top:4px;flex-shrink:0"></div>
                    <div>
                        <div class="fw-600 small">Booking Dibuat</div>
                        <div class="text-muted" style="font-size:0.75rem">{{ $booking->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                @if($booking->confirmed_at)
                <div class="d-flex gap-2 mb-3">
                    <div class="bg-info rounded-circle" style="width:10px;height:10px;margin-top:4px;flex-shrink:0"></div>
                    <div>
                        <div class="fw-600 small">Dikonfirmasi</div>
                        <div class="text-muted" style="font-size:0.75rem">{{ $booking->confirmed_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                @endif
                @if($booking->paid_at)
                <div class="d-flex gap-2">
                    <div class="bg-success rounded-circle" style="width:10px;height:10px;margin-top:4px;flex-shrink:0"></div>
                    <div>
                        <div class="fw-600 small">Pembayaran Lunas</div>
                        <div class="text-muted" style="font-size:0.75rem">{{ $booking->paid_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
