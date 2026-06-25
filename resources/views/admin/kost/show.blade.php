@extends('layouts.admin')

@section('title', 'Detail Kost')
@section('page-title', 'Detail Kost')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.kost.index') }}">Data Kost</a></li>
<li class="breadcrumb-item active">{{ $kost->name }}</li>
@endsection

@push('styles')
<style>
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 10px;
}
.gallery-grid img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 10px;
}
</style>
@endpush

@section('content')

<div class="row g-4">
    <!-- Main Info -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="fw-800 mb-1">{{ $kost->name }}</h4>
                        <p class="text-muted mb-0">
                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                            {{ $kost->address }}, {{ $kost->city }}, {{ $kost->province }}
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge
                            {{ $kost->type === 'Putra' ? 'bg-primary' : ($kost->type === 'Putri' ? 'bg-danger' : 'bg-success') }} mb-1">
                            {{ $kost->type }}
                        </span><br>
                        @switch($kost->status)
                            @case('active') <span class="badge bg-success">Aktif</span> @break
                            @case('inactive') <span class="badge bg-secondary">Nonaktif</span> @break
                            @case('full') <span class="badge bg-danger">Penuh</span> @break
                        @endswitch
                        @if($kost->is_featured)<span class="badge bg-warning text-dark">⭐ Unggulan</span>@endif
                    </div>
                </div>

                <p class="text-muted">{{ $kost->description }}</p>

                <div class="row g-3 mt-2">
                    @foreach([
                        ['label' => 'Harga/Bulan', 'value' => $kost->price_formatted, 'icon' => 'fas fa-money-bill'],
                        ['label' => 'Harga/Tahun', 'value' => $kost->price_yearly ? 'Rp ' . number_format($kost->price_yearly,0,',','.') : '-', 'icon' => 'fas fa-calendar-alt'],
                        ['label' => 'Total Kamar', 'value' => $kost->total_rooms, 'icon' => 'fas fa-bed'],
                        ['label' => 'Kamar Tersedia', 'value' => $kost->available_rooms, 'icon' => 'fas fa-door-open'],
                        ['label' => 'Min. Sewa', 'value' => $kost->min_stay . ' Bulan', 'icon' => 'fas fa-clock'],
                        ['label' => 'Pemilik', 'value' => $kost->owner_name . ' (' . $kost->owner_phone . ')', 'icon' => 'fas fa-user'],
                    ] as $item)
                    <div class="col-md-4 col-6">
                        <div class="d-flex gap-2">
                            <i class="{{ $item['icon'] }} text-primary" style="width:18px;margin-top:3px"></i>
                            <div>
                                <div class="text-muted" style="font-size:0.72rem">{{ $item['label'] }}</div>
                                <div class="fw-600 small">{{ $item['value'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($kost->facilities)
                <hr>
                <h6 class="fw-700 mb-2">Fasilitas Kamar</h6>
                <div>
                    @foreach($kost->facilities as $f)<span class="badge bg-light text-dark me-1 mb-1">{{ $f }}</span>@endforeach
                </div>
                @endif

                @if($kost->shared_facilities)
                <h6 class="fw-700 mb-2 mt-3">Fasilitas Bersama</h6>
                <div>
                    @foreach($kost->shared_facilities as $f)<span class="badge bg-light text-dark me-1 mb-1">{{ $f }}</span>@endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Photos -->
        @if($kost->photos->count())
        <div class="card mb-4">
            <div class="card-header"><h6 class="fw-700 mb-0">Galeri Foto</h6></div>
            <div class="card-body">
                <div class="gallery-grid">
                    @foreach($kost->photos as $photo)
                    <img src="{{ $photo->url }}" alt="">
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Video -->
        @if($kost->video_tour)
        <div class="card mb-4">
            <div class="card-header"><h6 class="fw-700 mb-0">Video Tur</h6></div>
            <div class="card-body">
                <video src="{{ asset('storage/videos/' . $kost->video_tour) }}" controls class="w-100 rounded-3" style="max-height:300px"></video>
            </div>
        </div>
        @endif

        <!-- Rooms -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-700 mb-0">Daftar Kamar (<span id="roomCount">{{ $kost->rooms->count() }}</span>)</h6>
                <button type="button" class="btn btn-sm btn-primary" id="btnAddRoom">
                    <i class="fas fa-plus me-1"></i>Tambah Kamar
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">No. Kamar</th>
                                <th>Lantai</th>
                                <th>Ukuran</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th class="text-center" style="width:100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="roomTableBody">
                            @forelse($kost->rooms as $room)
                            <tr id="room-row-{{ $room->id }}">
                                <td class="ps-3 fw-600">{{ $room->room_number }}</td>
                                <td>{{ $room->floor }}</td>
                                <td>{{ $room->size }} m²</td>
                                <td>Rp {{ number_format($room->price,0,',','.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $room->status === 'available' ? 'success' : ($room->status === 'occupied' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($room->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-light btn-edit-room" title="Edit"
                                        data-room='@json($room)'>
                                        <i class="fas fa-edit text-warning"></i>
                                    </button>
                                    <form action="{{ route('admin.kost.rooms.destroy', [$kost, $room]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-light" data-confirm-delete="Hapus kamar {{ $room->room_number }}?">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr id="roomEmptyRow">
                                <td colspan="6" class="text-center py-3 text-muted">Belum ada data kamar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ $kost->thumbnail_url }}" class="rounded-3 mb-3 w-100" style="height:160px;object-fit:cover" alt="">
                <a href="{{ route('admin.kost.edit', $kost) }}" class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-edit me-2"></i>Edit Kost
                </a>
                <a href="{{ route('kost.show', $kost->slug) }}" target="_blank" class="btn btn-outline-primary w-100">
                    <i class="fas fa-external-link-alt me-2"></i>Lihat di Web
                </a>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card mb-4">
            <div class="card-header"><h6 class="fw-700 mb-0">Booking Terbaru</h6></div>
            <div class="card-body p-0">
                @forelse($kost->bookings->take(5) as $b)
                <div class="d-flex justify-content-between p-3 border-bottom">
                    <div>
                        <div class="fw-600 small">{{ $b->user->name ?? '-' }}</div>
                        <div class="text-muted" style="font-size:0.72rem">{{ $b->booking_code }}</div>
                    </div>
                    {!! $b->status_badge !!}
                </div>
                @empty
                <p class="text-muted text-center py-3 mb-0">Belum ada booking</p>
                @endforelse
            </div>
        </div>

        <!-- Reviews -->
        <div class="card">
            <div class="card-header"><h6 class="fw-700 mb-0">Ulasan ({{ $kost->reviews->count() }})</h6></div>
            <div class="card-body">
                @forelse($kost->reviews->take(3) as $review)
                <div class="mb-3 pb-3 border-bottom">
                    <div class="fw-600 small">{{ $review->user->name ?? '-' }}</div>
                    <div class="stars" style="font-size:0.7rem">
                        @for($i=1;$i<=5;$i++)<i class="fas fa-star" style="color:{{ $review->rating >= $i ? '#f59e0b':'#d1d5db' }}"></i>@endfor
                    </div>
                    <p class="text-muted small mb-0 mt-1">{{ Str::limit($review->comment, 80) }}</p>
                </div>
                @empty
                <p class="text-muted text-center mb-0">Belum ada ulasan</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal: Tambah/Edit Kamar -->
<div class="modal fade" id="roomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:16px">
            <form id="roomForm" method="POST" action="{{ route('admin.kost.rooms.store', $kost) }}">
                @csrf
                <input type="hidden" name="_method" id="roomFormMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title fw-700" id="roomModalTitle">Tambah Kamar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">No. Kamar <span class="text-danger">*</span></label>
                            <input type="text" name="room_number" id="room_number" class="form-control" required placeholder="A1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lantai <span class="text-danger">*</span></label>
                            <input type="number" name="floor" id="room_floor" class="form-control" required min="1" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ukuran (m&sup2;)</label>
                            <input type="number" name="size" id="room_size" class="form-control" step="0.5" min="0" placeholder="12">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga per Bulan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="room_price" class="form-control" required min="0" placeholder="1200000">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="room_status" class="form-select" required>
                                <option value="available">Tersedia</option>
                                <option value="occupied">Terisi</option>
                                <option value="maintenance">Perbaikan</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Fasilitas Kamar</label>
                            <div class="d-flex flex-wrap gap-2" id="roomFacilities">
                                @foreach(['AC','Kasur','Lemari','Meja Belajar','TV','WiFi','Kamar Mandi Dalam','Water Heater'] as $fac)
                                <div class="form-check">
                                    <input class="form-check-input room-facility" type="checkbox" name="facilities[]" value="{{ $fac }}" id="rf_{{ $loop->index }}">
                                    <label class="form-check-label small" for="rf_{{ $loop->index }}">{{ $fac }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" id="room_description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="roomSubmitBtn">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    var roomStoreUrl = "{{ route('admin.kost.rooms.store', $kost) }}";
    var roomModalEl = document.getElementById('roomModal');

    // PENTING: Pindahkan modal langsung ke <body>.
    // Ini mencegah modal "terjebak" di dalam parent yang punya CSS animation/transform
    // (yang membuat stacking context baru), sehingga klik mouse tidak tertahan
    // oleh boundary parent dan modal benar-benar full-screen sesuai perilaku Bootstrap.
    if (roomModalEl && roomModalEl.parentElement !== document.body) {
        document.body.appendChild(roomModalEl);
    }

    var roomModalInstance = null;

    function getModalInstance() {
        if (!roomModalInstance) {
            roomModalInstance = bootstrap.Modal.getOrCreateInstance(roomModalEl);
        }
        return roomModalInstance;
    }

    function resetRoomForm() {
        document.getElementById('roomModalTitle').textContent = 'Tambah Kamar';
        document.getElementById('roomForm').action = roomStoreUrl;
        document.getElementById('roomFormMethod').value = 'POST';
        document.getElementById('roomForm').reset();
        document.querySelectorAll('.room-facility').forEach(function(cb) { cb.checked = false; });
        document.getElementById('room_status').value = 'available';
        document.getElementById('room_floor').value = 1;
    }

    function fillRoomForm(room) {
        document.getElementById('roomModalTitle').textContent = 'Edit Kamar ' + room.room_number;
        document.getElementById('roomForm').action = roomStoreUrl + '/' + room.id;
        document.getElementById('roomFormMethod').value = 'PUT';

        document.getElementById('room_number').value = room.room_number || '';
        document.getElementById('room_floor').value = room.floor || 1;
        document.getElementById('room_size').value = room.size || '';
        document.getElementById('room_price').value = room.price || '';
        document.getElementById('room_status').value = room.status || 'available';
        document.getElementById('room_description').value = room.description || '';

        var facilities = room.facilities || [];
        document.querySelectorAll('.room-facility').forEach(function(cb) {
            cb.checked = facilities.indexOf(cb.value) !== -1;
        });
    }

    // Tombol "Tambah Kamar"
    var btnAddRoom = document.getElementById('btnAddRoom');
    if (btnAddRoom) {
        btnAddRoom.addEventListener('click', function() {
            resetRoomForm();
            getModalInstance().show();
        });
    }

    // Tombol "Edit" di setiap baris kamar (delegasi event, supaya tetap jalan walau baris ditambah dinamis nanti)
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-edit-room');
        if (!btn) return;

        var raw = btn.getAttribute('data-room');
        try {
            var room = JSON.parse(raw);
            fillRoomForm(room);
            getModalInstance().show();
        } catch (err) {
            console.error('Gagal membaca data kamar:', err);
            alert('Terjadi kesalahan saat membuka data kamar. Silakan refresh halaman.');
        }
    });

    // Pastikan tombol submit tidak ke-disable oleh state lama jika modal dibuka berkali-kali
    roomModalEl.addEventListener('hidden.bs.modal', function() {
        document.getElementById('roomSubmitBtn').disabled = false;
    });

    // Optional: kasih feedback visual saat submit (cegah double-submit)
    document.getElementById('roomForm').addEventListener('submit', function() {
        document.getElementById('roomSubmitBtn').disabled = true;
        document.getElementById('roomSubmitBtn').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    });
})();
</script>
@endpush
