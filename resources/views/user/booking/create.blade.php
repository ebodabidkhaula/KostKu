@extends('layouts.app')

@section('title', 'Booking Kost — ' . $kost->name)

@push('styles')
<style>
.booking-form-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 4px 30px rgba(37,99,235,0.12);
}

.kost-summary {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    border-radius: 16px;
    color: white;
    padding: 20px;
}

.kost-summary img {
    border-radius: 12px;
    width: 80px;
    height: 70px;
    object-fit: cover;
}

.price-calc-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed #e2e8f0;
    font-size: 0.9rem;
}

.price-calc-row.total {
    border: none;
    font-weight: 800;
    font-size: 1rem;
    color: var(--primary);
    padding-top: 12px;
}

.payment-option {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 12px;
}

.payment-option:hover,
.payment-option.selected {
    border-color: var(--primary);
    background: #eff6ff;
}

.payment-option input { display: none; }

.payment-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- Header -->
            <div class="mb-4">
                <nav>
                    <ol class="breadcrumb small">
                        <li class="breadcrumb-item"><a href="{{ route('kost.index') }}" class="text-primary text-decoration-none">Cari Kost</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kost.show', $kost->slug) }}" class="text-primary text-decoration-none">{{ $kost->name }}</a></li>
                        <li class="breadcrumb-item active">Booking</li>
                    </ol>
                </nav>
                <h3 class="fw-800">Form Pemesanan</h3>
            </div>

            <form action="{{ route('user.booking.store') }}" method="POST" id="bookingForm">
                @csrf
                <input type="hidden" name="kost_id" value="{{ $kost->id }}">

                <div class="row g-4">
                    <!-- Left: Form -->
                    <div class="col-lg-8">
                        <div class="booking-form-card card p-4 mb-4">
                            <h5 class="fw-700 mb-4">📅 Detail Pemesanan</h5>

                            <!-- Tanggal Check-in (Date) -->
                            <div class="mb-3">
                                <label class="form-label fw-600">Tanggal Check-in <span class="text-danger">*</span></label>
                                <input type="date" name="check_in_date" class="form-control"
                                    min="{{ now()->addDay()->format('Y-m-d') }}"
                                    value="{{ old('check_in_date', now()->addDays(3)->format('Y-m-d')) }}" required
                                    id="checkinDate">
                            </div>

                            <!-- Durasi Sewa (Select) -->
                            <div class="mb-3">
                                <label class="form-label fw-600">Durasi Sewa <span class="text-danger">*</span></label>
                                <select name="duration_months" class="form-select" id="duration" required>
                                    @for($m = $kost->min_stay; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ old('duration_months', $kost->min_stay) == $m ? 'selected' : '' }}>
                                        {{ $m }} Bulan
                                        @if($m == 12 && $kost->price_yearly) (Harga Tahunan Tersedia) @endif
                                    </option>
                                    @endfor
                                </select>
                                <small class="text-muted">Minimum sewa {{ $kost->min_stay }} bulan</small>
                            </div>

                            <!-- Pilih Kamar (jika tersedia) -->
                            @if($rooms->count())
                            <div class="mb-3">
                                <label class="form-label fw-600">Pilih Kamar</label>
                                <select name="room_id" class="form-select">
                                    <option value="">-- Tanpa Preferensi Kamar --</option>
                                    @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">
                                        Kamar {{ $room->room_number }}
                                        @if($room->floor) (Lantai {{ $room->floor }}) @endif
                                        @if($room->size) — {{ $room->size }} m² @endif
                                        — Rp {{ number_format($room->price, 0, ',', '.') }}/bln
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Catatan (Textarea) -->
                            <div class="mb-3">
                                <label class="form-label fw-600">Catatan Tambahan</label>
                                <textarea name="notes" class="form-control" rows="2"
                                    placeholder="Catatan khusus untuk pemilik kost...">{{ old('notes') }}</textarea>
                            </div>

                            <!-- Permintaan Khusus (Textarea) -->
                            <div class="mb-3">
                                <label class="form-label fw-600">Permintaan Khusus</label>
                                <textarea name="special_requests" class="form-control" rows="2"
                                    placeholder="Permintaan spesifik (misal: kamar lantai 1, dekat dapur, dll)">{{ old('special_requests') }}</textarea>
                            </div>
                        </div>

                        <!-- Metode Pembayaran -->
                        <div class="booking-form-card card p-4">
                            <h5 class="fw-700 mb-4">💳 Metode Pembayaran</h5>
                            <div class="row g-3">
                                @foreach([
                                    ['value' => 'transfer_bca', 'icon' => '🏦', 'label' => 'Transfer BCA', 'desc' => '8767431290 - Fatria Ardika Pratama', 'color' => '#0066cc'],
                                    ['value' => 'transfer_mandiri', 'icon' => '🏦', 'label' => 'Transfer Mandiri', 'desc' => '8971234321 - Dilham Abid Khaula', 'color' => '#003580'],
                                    ['value' => 'gopay', 'icon' => '💚', 'label' => 'GoPay', 'desc' => '089652370462 - Alfian Putra Herdian', 'color' => '#00AED6'],
                                    ['value' => 'Dana', 'icon' => '💜', 'label' => 'Dana', 'desc' => '085893171649 - Ryo Mirzavali', 'color' => '#4c3494'],
                                    ['value' => 'tunai', 'icon' => '💵', 'label' => 'Tunai ke Pemilik', 'desc' => 'Bayar langsung ke pemilik', 'color' => '#10b981'],
                                ] as $pm)
                                <div class="col-md-6">
                                    <label class="payment-option {{ old('payment_method') === $pm['value'] ? 'selected' : '' }}"
                                        onclick="selectPayment(this, '{{ $pm['value'] }}')">
                                        <input type="radio" name="payment_method" value="{{ $pm['value'] }}"
                                            {{ old('payment_method', 'transfer_bca') === $pm['value'] ? 'checked' : '' }}>
                                        <div class="payment-icon" style="background:{{ $pm['color'] }}20;color:{{ $pm['color'] }}">
                                            {{ $pm['icon'] }}
                                        </div>
                                        <div>
                                            <div class="fw-700 small">{{ $pm['label'] }}</div>
                                            <div class="text-muted" style="font-size:0.75rem">{{ $pm['desc'] }}</div>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right: Summary -->
                    <div class="col-lg-4">
                        <div class="position-sticky" style="top:90px">
                            <!-- Kost Summary -->
                            <div class="kost-summary mb-4">
                                <div class="d-flex gap-3 align-items-center mb-3">
                                    <img src="{{ $kost->thumbnail_url }}" alt="{{ $kost->name }}">
                                    <div>
                                        <div class="fw-700">{{ $kost->name }}</div>
                                        <div style="font-size:0.8rem;opacity:0.8">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $kost->city }}
                                        </div>
                                        <span class="badge bg-white text-primary mt-1" style="font-size:0.7rem">{{ $kost->type }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between" style="font-size:0.85rem;border-top:1px solid rgba(255,255,255,0.2);padding-top:10px">
                                    <span style="opacity:0.8">Harga per Bulan</span>
                                    <span class="fw-700">{{ $kost->price_formatted }}</span>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="card booking-form-card p-4">
                                <h6 class="fw-700 mb-3">Rincian Harga</h6>

                                <div class="price-calc-row">
                                    <span class="text-muted">Harga per Bulan</span>
                                    <span id="pricePerMonth">{{ $kost->price_formatted }}</span>
                                </div>
                                <div class="price-calc-row">
                                    <span class="text-muted">Durasi</span>
                                    <span id="durationDisplay">{{ $kost->min_stay }} Bulan</span>
                                </div>
                                <div class="price-calc-row">
                                    <span class="text-muted">Uang Muka (Deposit)</span>
                                    <span id="depositDisplay">{{ $kost->price_formatted }}</span>
                                </div>
                                <div class="price-calc-row total">
                                    <span>Total Pembayaran</span>
                                    <span id="totalDisplay">Rp {{ number_format($kost->price_monthly * $kost->min_stay, 0, ',', '.') }}</span>
                                </div>

                                <div class="alert alert-info rounded-3 mt-3 mb-3" style="font-size:0.8rem">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Pembayaran dikonfirmasi setelah transfer dan diverifikasi admin
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 fw-700">
                                    <i class="fas fa-check-circle me-2"></i>Konfirmasi Booking
                                </button>
                                <a href="{{ route('kost.show', $kost->slug) }}" class="btn btn-light w-100 mt-2">
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const priceMonthly = {{ $kost->price_monthly }};

function formatRp(val) {
    return 'Rp ' + Math.round(val).toLocaleString('id-ID');
}

function updatePriceCalc() {
    const dur = parseInt(document.getElementById('duration').value) || 1;
    const total = priceMonthly * dur;
    document.getElementById('durationDisplay').textContent = dur + ' Bulan';
    document.getElementById('totalDisplay').textContent = formatRp(total);
    document.getElementById('depositDisplay').textContent = formatRp(priceMonthly);
}

document.getElementById('duration').addEventListener('change', updatePriceCalc);

function selectPayment(el, value) {
    document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
}

// Set initial payment selection
document.querySelectorAll('.payment-option').forEach(el => {
    if (el.querySelector('input').checked) el.classList.add('selected');
    el.addEventListener('click', function() { selectPayment(this); });
});

updatePriceCalc();
</script>
@endpush
