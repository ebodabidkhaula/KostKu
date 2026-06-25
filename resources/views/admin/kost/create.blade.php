@extends('layouts.admin')

@section('title', isset($kost) ? 'Edit Kost' : 'Tambah Kost')
@section('page-title', isset($kost) ? 'Edit Kost' : 'Tambah Kost')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.kost.index') }}">Data Kost</a></li>
<li class="breadcrumb-item active">{{ isset($kost) ? 'Edit' : 'Tambah' }}</li>
@endsection

@push('styles')
<style>
.nav-tabs .nav-link {
    border-radius: 10px 10px 0 0;
    font-weight: 600;
    font-size: 0.85rem;
    color: #64748b;
    border: none;
    padding: 10px 20px;
}
.nav-tabs .nav-link.active {
    color: var(--primary);
    background: white;
    border-bottom: 3px solid var(--primary);
}
.nav-tabs { border-bottom: 1px solid #e2e8f0; }

.photo-preview {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #e2e8f0;
}

.upload-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}
.upload-zone:hover, .upload-zone.dragover {
    border-color: var(--primary);
    background: #eff6ff;
}

.facility-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    color: var(--primary);
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 0.82rem;
    font-weight: 600;
    margin: 3px;
    cursor: pointer;
    border: 1.5px solid transparent;
    transition: all 0.2s;
}
.facility-tag:hover { border-color: var(--primary); }
.facility-tag input { display: none; }
.facility-tag.checked {
    background: var(--primary);
    color: white;
}
</style>
@endpush

@section('content')

<form action="{{ isset($kost) ? route('admin.kost.update', $kost) : route('admin.kost.store') }}"
      method="POST" enctype="multipart/form-data" id="kostForm">
    @csrf
    @if(isset($kost)) @method('PUT') @endif

    @if($errors->any())
    <div class="alert alert-danger rounded-3 mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Ada kesalahan:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-0" id="kostTabs">
        <li class="nav-item"><a class="nav-link active" data-tab="info" href="#">📋 Info Dasar</a></li>
        <li class="nav-item"><a class="nav-link" data-tab="detail" href="#">🏠 Detail</a></li>
        <li class="nav-item"><a class="nav-link" data-tab="media" href="#">📸 Media</a></li>
        <li class="nav-item"><a class="nav-link" data-tab="rules" href="#">📜 Peraturan</a></li>
    </ul>

    <div class="card" style="border-radius: 0 16px 16px 16px">
        <div class="card-body p-4">

            <!-- ─── TAB: INFO DASAR ─── -->
            <div class="tab-content-panel active" id="tab-info">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Kost <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                            value="{{ old('name', $kost->name ?? '') }}" placeholder="Contoh: Kost Melati Indah">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipe Kost <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="">-- Pilih Tipe --</option>
                            @foreach(['Putra','Putri','Campur'] as $t)
                            <option value="{{ $t }}" {{ old('type', $kost->type ?? '') === $t ? 'selected' : '' }}>Kost {{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4" required
                            placeholder="Deskripsikan kost secara detail...">{{ old('description', $kost->description ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="2" required>{{ old('address', $kost->address ?? '') }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kota <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control" required
                            value="{{ old('city', $kost->city ?? '') }}" placeholder="Jakarta">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <select name="province" class="form-select" required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach(['DKI Jakarta','Jawa Barat','Jawa Tengah','Jawa Timur','Banten','DI Yogyakarta','Bali','Sumatera Utara','Sulawesi Selatan','Kalimantan Timur'] as $p)
                            <option value="{{ $p }}" {{ old('province', $kost->province ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" name="postal_code" class="form-control" maxlength="10"
                            value="{{ old('postal_code', $kost->postal_code ?? '') }}" placeholder="12345">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga per Bulan (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price_monthly" class="form-control" required min="0"
                                value="{{ old('price_monthly', $kost->price_monthly ?? '') }}" placeholder="1500000">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga per Tahun (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price_yearly" class="form-control" min="0"
                                value="{{ old('price_yearly', $kost->price_yearly ?? '') }}" placeholder="15000000">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Minimal Sewa (Bulan) <span class="text-danger">*</span></label>
                        <select name="min_stay" class="form-select" required>
                            @foreach([1,2,3,6,12] as $m)
                            <option value="{{ $m }}" {{ old('min_stay', $kost->min_stay ?? 1) == $m ? 'selected' : '' }}>{{ $m }} Bulan</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Kamar <span class="text-danger">*</span></label>
                        <input type="number" name="total_rooms" class="form-control" required min="1"
                            value="{{ old('total_rooms', $kost->total_rooms ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kamar Tersedia <span class="text-danger">*</span></label>
                        <input type="number" name="available_rooms" class="form-control" required min="0"
                            value="{{ old('available_rooms', $kost->available_rooms ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $kost->status ?? 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $kost->status ?? '') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="full" {{ old('status', $kost->status ?? '') === 'full' ? 'selected' : '' }}>Penuh</option>
                        </select>
                    </div>
                    <!-- Nama & Telepon Pemilik -->
                    <div class="col-md-6">
                        <label class="form-label">Nama Pemilik <span class="text-danger">*</span></label>
                        <input type="text" name="owner_name" class="form-control" required
                            value="{{ old('owner_name', $kost->owner_name ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon Pemilik <span class="text-danger">*</span></label>
                        <input type="tel" name="owner_phone" class="form-control" required
                            value="{{ old('owner_phone', $kost->owner_phone ?? '') }}">
                    </div>
                    <!-- Checkboxes -->
                    <div class="col-12">
                        <label class="form-label">Opsi Tambahan</label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach([
                                ['name' => 'is_featured', 'label' => '⭐ Tampilkan sebagai Unggulan'],
                                ['name' => 'allow_cooking', 'label' => '🍳 Boleh Masak'],
                                ['name' => 'allow_pets', 'label' => '🐾 Boleh Bawa Hewan'],
                                ['name' => 'allow_guest', 'label' => '👥 Boleh Bawa Tamu'],
                            ] as $opt)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="{{ $opt['name'] }}"
                                    id="{{ $opt['name'] }}" value="1"
                                    {{ old($opt['name'], $kost->{$opt['name']} ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $opt['name'] }}">{{ $opt['label'] }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── TAB: DETAIL ─── -->
            <div class="tab-content-panel d-none" id="tab-detail">
                <div class="row g-4">
                    <!-- Fasilitas Kamar -->
                    <div class="col-12">
                        <label class="form-label fw-700">Fasilitas Kamar</label>
                        <div class="d-flex flex-wrap">
                            @foreach(['AC', 'Kasur', 'Lemari', 'Meja Belajar', 'Kursi', 'TV', 'WiFi', 'Kamar Mandi Dalam', 'Water Heater', 'Kulkas Mini', 'Balkon', 'Kipas Angin'] as $fac)
                            @php $checked = in_array($fac, old('facilities', $kost->facilities ?? [])); @endphp
                            <label class="facility-tag {{ $checked ? 'checked' : '' }}">
                                <input type="checkbox" name="facilities[]" value="{{ $fac }}" {{ $checked ? 'checked' : '' }}>
                                {{ $fac }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <!-- Fasilitas Bersama -->
                    <div class="col-12">
                        <label class="form-label fw-700">Fasilitas Bersama</label>
                        <div class="d-flex flex-wrap">
                            @foreach(['Dapur Bersama','Ruang Tamu','Parkir Motor','Parkir Mobil','Laundry','Mushola','CCTV','Satpam','Taman','Jemuran','Dispenser','Gym','Kolam Renang'] as $fac)
                            @php $checked = in_array($fac, old('shared_facilities', $kost->shared_facilities ?? [])); @endphp
                            <label class="facility-tag {{ $checked ? 'checked' : '' }}">
                                <input type="checkbox" name="shared_facilities[]" value="{{ $fac }}" {{ $checked ? 'checked' : '' }}>
                                {{ $fac }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <!-- Jam Masuk/Keluar -->
                    <div class="col-md-4">
                        <label class="form-label">Jam Masuk Tamu</label>
                        <input type="time" name="entry_time" class="form-control"
                            value="{{ old('entry_time', $kost->entry_time ?? '06:00') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Keluar Tamu</label>
                        <input type="time" name="exit_time" class="form-control"
                            value="{{ old('exit_time', $kost->exit_time ?? '22:00') }}">
                    </div>
                    <!-- Koordinat -->
                    <div class="col-md-6">
                        <label class="form-label">Latitude</label>
                        <input type="number" name="latitude" class="form-control" step="any"
                            value="{{ old('latitude', $kost->latitude ?? '') }}" placeholder="-6.2088">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude</label>
                        <input type="number" name="longitude" class="form-control" step="any"
                            value="{{ old('longitude', $kost->longitude ?? '') }}" placeholder="106.8456">
                    </div>
                </div>
            </div>

            <!-- ─── TAB: MEDIA ─── -->
            <div class="tab-content-panel d-none" id="tab-media">
                <div class="row g-4">
                    <!-- Thumbnail -->
                    <div class="col-md-6">
                        <label class="form-label fw-700">Foto Thumbnail Utama</label>
                        @if(isset($kost) && $kost->thumbnail)
                        <div class="mb-2">
                            <img src="{{ $kost->thumbnail_url }}" class="photo-preview mb-2" alt="Thumbnail">
                            <small class="text-muted d-block">Upload baru untuk mengganti</small>
                        </div>
                        @endif
                        <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/png,image/jpg"
                            id="thumbnailInput">
                        <small class="text-muted">JPG/PNG, max 5MB. Disarankan rasio 16:9</small>
                        <img id="thumbnailPreview" class="photo-preview mt-2 d-none" alt="">
                    </div>

                    <!-- Video Tour -->
                    <div class="col-md-6">
                        <label class="form-label fw-700">Video Tur Virtual <span class="badge bg-info text-white ms-1">Multimedia</span></label>
                        @if(isset($kost) && $kost->video_tour)
                        <div class="mb-2">
                            <video src="{{ asset('storage/videos/' . $kost->video_tour) }}" controls class="w-100 rounded-3" style="max-height:150px"></video>
                            <small class="text-muted">Upload baru untuk mengganti</small>
                        </div>
                        @endif
                        <input type="file" name="video_tour" class="form-control" accept="video/mp4,video/avi,video/mov"
                            id="videoInput">
                        <small class="text-muted">MP4/AVI/MOV, max 50MB</small>
                        <video id="videoPreview" controls class="w-100 rounded-3 mt-2 d-none" style="max-height:150px"></video>
                    </div>

                    <!-- Multiple Photos -->
                    <div class="col-12">
                        <label class="form-label fw-700">Galeri Foto Kost</label>
                        <div class="upload-zone" id="uploadZone" onclick="document.getElementById('photosInput').click()">
                            <i class="fas fa-images fa-2x text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-1">Klik atau drag & drop foto di sini</p>
                            <small class="text-muted">JPG/PNG, max 5MB per foto, bisa pilih banyak</small>
                        </div>
                        <input type="file" name="photos[]" id="photosInput" multiple accept="image/jpeg,image/png,image/jpg" class="d-none">
                        <div class="row g-2 mt-2" id="photoPreviews"></div>

                        @if(isset($kost) && $kost->photos->count())
                        <div class="mt-3">
                            <label class="form-label">Foto Existing</label>
                            <div class="row g-2">
                                @foreach($kost->photos as $photo)
                                <div class="col-md-2 col-3" id="photo-{{ $photo->id }}">
                                    <div class="position-relative">
                                        <img src="{{ $photo->url }}" class="photo-preview" alt="">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle"
                                            style="width:22px;height:22px;padding:0;font-size:0.6rem"
                                            onclick="deletePhoto({{ $photo->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted d-block text-center" style="font-size:0.7rem">{{ $photo->type }}</small>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ─── TAB: PERATURAN ─── -->
            <div class="tab-content-panel d-none" id="tab-rules">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-700">Peraturan Kost</label>
                        <p class="text-muted small">Tambahkan peraturan yang harus dipatuhi penghuni</p>
                        <div id="rulesContainer">
                            @php $rules = old('rules', $kost->rules ?? ['Dilarang membawa tamu menginap tanpa izin pemilik']); @endphp
                            @foreach((array)$rules as $i => $rule)
                            <div class="input-group mb-2 rule-row">
                                <span class="input-group-text text-muted" style="font-size:0.8rem">{{ $i + 1 }}</span>
                                <input type="text" name="rules[]" class="form-control" value="{{ $rule }}" placeholder="Tambahkan peraturan...">
                                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.rule-row').remove(); reindexRules()">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addRule()">
                            <i class="fas fa-plus me-1"></i>Tambah Peraturan
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Submit -->
        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="{{ route('admin.kost.index') }}" class="btn btn-light px-4">
                <i class="fas fa-arrow-left me-2"></i>Batal
            </a>
            <button type="submit" class="btn btn-primary px-5">
                <i class="fas fa-save me-2"></i>{{ isset($kost) ? 'Simpan Perubahan' : 'Tambah Kost' }}
            </button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
// Tab switching
document.querySelectorAll('[data-tab]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('[data-tab]').forEach(l => l.classList.remove('active'));
        document.querySelectorAll('.tab-content-panel').forEach(p => p.classList.add('d-none'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.remove('d-none');
    });
});

// Facility tag toggle
document.querySelectorAll('.facility-tag').forEach(tag => {
    tag.addEventListener('click', function() {
        const cb = this.querySelector('input');
        cb.checked = !cb.checked;
        this.classList.toggle('checked', cb.checked);
    });
});

// Thumbnail preview
document.getElementById('thumbnailInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const prev = document.getElementById('thumbnailPreview');
        prev.src = URL.createObjectURL(file);
        prev.classList.remove('d-none');
    }
});

// Video preview
document.getElementById('videoInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const prev = document.getElementById('videoPreview');
        prev.src = URL.createObjectURL(file);
        prev.classList.remove('d-none');
    }
});

// Multiple photo previews
document.getElementById('photosInput').addEventListener('change', function() {
    const container = document.getElementById('photoPreviews');
    container.innerHTML = '';
    Array.from(this.files).forEach((file, i) => {
        const div = document.createElement('div');
        div.className = 'col-md-2 col-3';
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.className = 'photo-preview';
        div.appendChild(img);
        container.appendChild(div);
    });
});

// Drag & drop
const zone = document.getElementById('uploadZone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('dragover');
    document.getElementById('photosInput').files = e.dataTransfer.files;
    document.getElementById('photosInput').dispatchEvent(new Event('change'));
});

// Delete existing photo
function deletePhoto(id) {
    if (!confirm('Hapus foto ini?')) return;
    axios.delete(`/admin/kost/photo/${id}`)
        .then(() => document.getElementById('photo-' + id).remove())
        .catch(() => alert('Gagal menghapus foto'));
}

// Rules
function addRule() {
    const container = document.getElementById('rulesContainer');
    const count = container.querySelectorAll('.rule-row').length + 1;
    const div = document.createElement('div');
    div.className = 'input-group mb-2 rule-row';
    div.innerHTML = `<span class="input-group-text text-muted" style="font-size:0.8rem">${count}</span>
        <input type="text" name="rules[]" class="form-control" placeholder="Tambahkan peraturan...">
        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.rule-row').remove(); reindexRules()"><i class="fas fa-minus"></i></button>`;
    container.appendChild(div);
}

function reindexRules() {
    document.querySelectorAll('.rule-row').forEach((row, i) => {
        row.querySelector('.input-group-text').textContent = i + 1;
    });
}
</script>
@endpush
