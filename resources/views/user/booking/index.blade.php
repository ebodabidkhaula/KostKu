@extends('layouts.app')

@section('title', 'Booking Saya — KostKu')

@push('styles')
<style>
.booking-item {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    padding: 20px;
    margin-bottom: 16px;
    transition: transform 0.2s;
}
.booking-item:hover { transform: translateY(-2px); }
.booking-item img { width: 90px; height: 80px; object-fit: cover; border-radius: 12px; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <h3 class="fw-800 mb-4">Booking Saya</h3>

    @forelse($bookings as $b)
    <div class="booking-item" data-aos="fade-up">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <img src="{{ $b->kost->thumbnail_url }}" alt="">
            </div>
            <div class="col">
                <div class="d-flex justify-content-between flex-wrap">
                    <div>
                        <h6 class="fw-700 mb-1">{{ $b->kost->name }}</h6>
                        <p class="text-muted small mb-1">
                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $b->kost->city }}
                        </p>
                        <div class="small">
                            <span class="text-muted">Kode:</span> <strong class="text-primary">{{ $b->booking_code }}</strong>
                            &bull; Check-in: <strong>{{ $b->check_in_date->format('d M Y') }}</strong>
                            &bull; Durasi: <strong>{{ $b->duration_months }} bulan</strong>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-700 text-primary">Rp {{ number_format($b->total_price, 0, ',', '.') }}</div>
                        <div class="mt-1">{!! $b->status_badge !!}</div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('user.booking.show', $b) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>Detail
                    </a>
                    @if(in_array($b->booking_status, ['pending', 'confirmed']))
                    <form action="{{ route('user.booking.cancel', $b) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="button" class="btn btn-sm btn-outline-danger" data-confirm-delete="Batalkan booking ini?">
                            <i class="fas fa-times me-1"></i>Batalkan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
        <h6 class="text-muted">Belum ada booking</h6>
        <a href="{{ route('kost.index') }}" class="btn btn-primary mt-2">Cari Kost Sekarang</a>
    </div>
    @endforelse

    @if($bookings->hasPages())
    <div class="d-flex justify-content-center mt-4">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection
