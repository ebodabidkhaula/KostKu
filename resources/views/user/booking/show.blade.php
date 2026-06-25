@extends('layouts.app')

@section('title', 'Detail Booking — ' . $booking->booking_code)

@push('styles')
<style>
.detail-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    padding: 24px;
    margin-bottom: 20px;
}

.timeline-step {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
}

.timeline-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.rating-input {
    display: flex;
    gap: 6px;
    font-size: 1.8rem;
    cursor: pointer;
}

.rating-input i { color: #d1d5db; transition: color 0.2s; }
.rating-input i.active { color: #f59e0b; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <nav class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}" class="text-primary text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.booking.index') }}" class="text-primary text-decoration-none">Booking Saya</a></li>
            <li class="breadcrumb-item active">{{ $booking->booking_code }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-800 mb-0">Detail Booking</h4>
        {!! $booking->status_badge !!}
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Kost Info -->
            <div class="detail-card">
                <div class="d-flex gap-3">
                    <img src="{{ $booking->kost->thumbnail_url }}" class="rounded-3" style="width:100px;height:90px;object-fit:cover" alt="">
                    <div>
                        <h5 class="fw-700 mb-1">{{ $booking->kost->name }}</h5>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $booking->kost->address }}
                        </p>
                        <a href="{{ route('kost.show', $booking->kost->slug) }}" class="btn btn-sm btn-outline-primary">
                            Lihat Kost <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Detail Info -->
            <div class="detail-card">
                <h6 class="fw-700 mb-3">Rincian Pemesanan</h6>
                <div class="row g-3">
                    @foreach([
                        ['label' => 'Kode Booking', 'value' => $booking->booking_code],
                        ['label' => 'Tanggal Check-in', 'value' => $booking->check_in_date->format('d F Y')],
                        ['label' => 'Durasi Sewa', 'value' => $booking->duration_months . ' Bulan'],
                        ['label' => 'Total Pembayaran', 'value' => 'Rp ' . number_format($booking->total_price, 0, ',', '.')],
                        ['label' => 'Deposit', 'value' => 'Rp ' . number_format($booking->deposit, 0, ',', '.')],
                        ['label' => 'Metode Pembayaran', 'value' => $booking->payment_method ? str_replace('_',' ',ucwords($booking->payment_method)) : '-'],
                    ] as $item)
                    <div class="col-md-6">
                        <div class="text-muted small">{{ $item['label'] }}</div>
                        <div class="fw-600">{{ $item['value'] }}</div>
                    </div>
                    @endforeach

                    @if($booking->room)
                    <div class="col-md-6">
                        <div class="text-muted small">Kamar</div>
                        <div class="fw-600">{{ $booking->room->room_number }}</div>
                    </div>
                    @endif
                </div>

                @if($booking->notes)
                <div class="mt-3">
                    <div class="text-muted small mb-1">Catatan</div>
                    <div class="p-2 bg-light rounded-2 small">{{ $booking->notes }}</div>
                </div>
                @endif

                @if($booking->special_requests)
                <div class="mt-3">
                    <div class="text-muted small mb-1">Permintaan Khusus</div>
                    <div class="p-2 bg-light rounded-2 small">{{ $booking->special_requests }}</div>
                </div>
                @endif
            </div>

            <!-- Upload Payment Proof -->
            @if($booking->payment_status === 'pending' && in_array($booking->booking_status, ['pending','confirmed']))
            <div class="detail-card">
                <h6 class="fw-700 mb-3">📤 Upload Bukti Pembayaran</h6>
                @if($booking->payment_proof)
                <div class="mb-3">
                    <img src="{{ asset('storage/payments/' . $booking->payment_proof) }}" class="img-fluid rounded-3" style="max-height:200px" alt="">
                    <p class="text-success small mt-2"><i class="fas fa-check-circle me-1"></i>Bukti telah diunggah, menunggu verifikasi</p>
                </div>
                @endif
                <form action="{{ route('user.booking.payment', $booking) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <input type="file" name="payment_proof" class="form-control" accept="image/*" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Upload
                        </button>
                    </div>
                    <small class="text-muted">Format JPG/PNG, maksimal 5MB</small>
                </form>
            </div>
            @endif

            <!-- Review Form -->
            @if($booking->booking_status === 'completed' && !$booking->review)
            <div class="detail-card">
                <h6 class="fw-700 mb-3">⭐ Berikan Ulasan</h6>
                <form action="{{ route('user.booking.review', $booking) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <div class="rating-input" id="ratingInput">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star" data-value="{{ $i }}"></i>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingValue" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Komentar</label>
                        <textarea name="comment" class="form-control" rows="3" required minlength="10"
                            placeholder="Bagaimana pengalaman Anda tinggal di kost ini?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Ulasan
                    </button>
                </form>
            </div>
            @elseif($booking->review)
            <div class="detail-card">
                <h6 class="fw-700 mb-3">Ulasan Anda</h6>
                <div class="stars mb-2">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star" style="color:{{ $booking->review->rating >= $i ? '#f59e0b' : '#d1d5db' }}"></i>
                    @endfor
                </div>
                <p class="text-muted mb-0">{{ $booking->review->comment }}</p>
                @if(!$booking->review->is_approved)
                <span class="badge bg-warning mt-2">Menunggu persetujuan admin</span>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar: Status Timeline -->
        <div class="col-lg-4">
            <div class="detail-card">
                <h6 class="fw-700 mb-3">Status Pesanan</h6>

                <div class="timeline-step">
                    <div class="timeline-icon bg-primary text-white"><i class="fas fa-check"></i></div>
                    <div>
                        <div class="fw-600 small">Booking Dibuat</div>
                        <div class="text-muted" style="font-size:0.75rem">{{ $booking->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="timeline-icon {{ $booking->confirmed_at ? 'bg-primary text-white' : 'bg-light text-muted' }}">
                        <i class="fas fa-{{ $booking->confirmed_at ? 'check' : 'clock' }}"></i>
                    </div>
                    <div>
                        <div class="fw-600 small">Konfirmasi Admin</div>
                        <div class="text-muted" style="font-size:0.75rem">
                            {{ $booking->confirmed_at ? $booking->confirmed_at->format('d M Y, H:i') : 'Menunggu konfirmasi' }}
                        </div>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="timeline-icon {{ $booking->paid_at ? 'bg-success text-white' : 'bg-light text-muted' }}">
                        <i class="fas fa-{{ $booking->paid_at ? 'check' : 'money-bill' }}"></i>
                    </div>
                    <div>
                        <div class="fw-600 small">Pembayaran Lunas</div>
                        <div class="text-muted" style="font-size:0.75rem">
                            {{ $booking->paid_at ? $booking->paid_at->format('d M Y, H:i') : 'Belum lunas' }}
                        </div>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="timeline-icon {{ $booking->booking_status === 'completed' ? 'bg-success text-white' : 'bg-light text-muted' }}">
                        <i class="fas fa-flag-checkered"></i>
                    </div>
                    <div>
                        <div class="fw-600 small">Selesai</div>
                        <div class="text-muted" style="font-size:0.75rem">
                            {{ $booking->booking_status === 'completed' ? 'Sewa telah selesai' : 'Belum selesai' }}
                        </div>
                    </div>
                </div>
            </div>

            @if($booking->kost->owner_phone)
            <div class="detail-card">
                <h6 class="fw-700 mb-2">Butuh Bantuan?</h6>
                <p class="text-muted small">Hubungi pemilik kost untuk informasi lebih lanjut</p>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->kost->owner_phone) }}" target="_blank" class="btn btn-success w-100">
                    <i class="fab fa-whatsapp me-2"></i>WhatsApp Pemilik
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const stars = document.querySelectorAll('#ratingInput i');
const ratingValue = document.getElementById('ratingValue');

stars?.forEach(star => {
    star.addEventListener('click', function() {
        const val = parseInt(this.dataset.value);
        ratingValue.value = val;
        stars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.value) <= val));
    });
    star.addEventListener('mouseenter', function() {
        const val = parseInt(this.dataset.value);
        stars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.value) <= val));
    });
});

document.getElementById('ratingInput')?.addEventListener('mouseleave', function() {
    const val = parseInt(ratingValue.value);
    stars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.value) <= val));
});
</script>
@endpush
