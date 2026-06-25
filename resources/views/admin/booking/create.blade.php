@extends('layouts.admin')

@section('title', 'Tambah Booking')
@section('page-title', 'Tambah Booking')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.booking.index') }}">Booking</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')

<div class="card">
    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('admin.booking.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <!-- Penyewa -->
                <div class="col-md-6">
                    <label class="form-label">Penyewa <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Pilih Penyewa --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->email }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kost -->
                <div class="col-md-6">
                    <label class="form-label">Kost <span class="text-danger">*</span></label>
                    <select name="kost_id" id="kostSelect" class="form-select" required>
                        <option value="">-- Pilih Kost --</option>
                        @foreach($kosts as $k)
                        <option value="{{ $k->id }}" data-price="{{ $k->price_monthly }}" {{ old('kost_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->name }} — {{ $k->price_formatted }}/bln
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kamar -->
                <div class="col-md-6">
                    <label class="form-label">Kamar (Opsional)</label>
                    <select name="room_id" id="roomSelect" class="form-select">
                        <option value="">-- Tanpa Kamar Spesifik --</option>
                    </select>
                </div>

                <!-- Check-in -->
                <div class="col-md-6">
                    <label class="form-label">Tanggal Check-in <span class="text-danger">*</span></label>
                    <input type="date" name="check_in_date" class="form-control" required value="{{ old('check_in_date') }}">
                </div>

                <!-- Durasi -->
                <div class="col-md-4">
                    <label class="form-label">Durasi (Bulan) <span class="text-danger">*</span></label>
                    <input type="number" name="duration_months" id="durationInput" class="form-control" min="1" value="{{ old('duration_months', 1) }}" required>
                </div>

                <!-- Total Harga -->
                <div class="col-md-4">
                    <label class="form-label">Total Harga (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="total_price" id="totalPriceInput" class="form-control" min="0" required value="{{ old('total_price') }}">
                </div>

                <!-- Deposit -->
                <div class="col-md-4">
                    <label class="form-label">Deposit (Rp)</label>
                    <input type="number" name="deposit" class="form-control" min="0" value="{{ old('deposit', 0) }}">
                </div>

                <!-- Status Booking -->
                <div class="col-md-6">
                    <label class="form-label">Status Booking <span class="text-danger">*</span></label>
                    <select name="booking_status" class="form-select" required>
                        @foreach(['pending'=>'Menunggu','confirmed'=>'Dikonfirmasi','active'=>'Aktif','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $k => $v)
                        <option value="{{ $k }}" {{ old('booking_status', 'confirmed') === $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Pembayaran -->
                <div class="col-md-6">
                    <label class="form-label">Status Pembayaran <span class="text-danger">*</span></label>
                    <select name="payment_status" class="form-select" required>
                        @foreach(['pending'=>'Menunggu','paid'=>'Lunas','partial'=>'Sebagian','refunded'=>'Refund'] as $k => $v)
                        <option value="{{ $k }}" {{ old('payment_status', 'pending') === $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Metode Pembayaran -->
                <div class="col-md-6">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="payment_method" class="form-select">
                        <option value="">-- Pilih --</option>
                        @foreach(['transfer_bca'=>'Transfer BCA','transfer_mandiri'=>'Transfer Mandiri','gopay'=>'GoPay','ovo'=>'OVO','tunai'=>'Tunai','qris'=>'QRIS'] as $k => $v)
                        <option value="{{ $k }}" {{ old('payment_method') === $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Catatan -->
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <a href="{{ route('admin.booking.index') }}" class="btn btn-light px-4">
                    <i class="fas fa-arrow-left me-2"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary px-5">
                    <i class="fas fa-save me-2"></i>Simpan Booking
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const kostSelect = document.getElementById('kostSelect');
const durationInput = document.getElementById('durationInput');
const totalPriceInput = document.getElementById('totalPriceInput');
const roomSelect = document.getElementById('roomSelect');

function updateTotal() {
    const opt = kostSelect.options[kostSelect.selectedIndex];
    const price = parseFloat(opt?.dataset.price || 0);
    const dur = parseInt(durationInput.value || 1);
    if (price > 0) {
        totalPriceInput.value = price * dur;
    }
}

kostSelect.addEventListener('change', function() {
    updateTotal();
    const kostId = this.value;
    roomSelect.innerHTML = '<option value="">-- Tanpa Kamar Spesifik --</option>';
    if (kostId) {
        axios.get(`/admin/booking/${kostId}/rooms`).then(res => {
            res.data.forEach(room => {
                const opt = document.createElement('option');
                opt.value = room.id;
                opt.textContent = `Kamar ${room.room_number} (Lantai ${room.floor}) - Rp ${parseInt(room.price).toLocaleString('id-ID')}`;
                roomSelect.appendChild(opt);
            });
        }).catch(() => {});
    }
});

durationInput.addEventListener('input', updateTotal);
</script>
@endpush
