@extends('layouts.app')

@section('title', 'Cari Kost — KostKu')

@push('styles')
<style>
.filter-sidebar {
    position: sticky;
    top: 90px;
}

.filter-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.kost-card { overflow: hidden; }

.kost-card .card-img-top {
    height: 200px;
    object-fit: cover;
    transition: transform 0.4s;
}

.kost-card:hover .card-img-top { transform: scale(1.05); }

.kost-img-wrap { overflow: hidden; position: relative; }

.kost-card .type-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    border-radius: 8px;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 10px;
}

.facility-chip {
    background: #f1f5f9;
    color: #475569;
    border-radius: 6px;
    padding: 2px 8px;
    font-size: 0.73rem;
    font-weight: 600;
}

.sort-btn {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 6px 14px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.sort-btn.active, .sort-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: #eff6ff;
}

/* SPA Loading state */
#kostGrid { transition: opacity 0.3s; }
#kostGrid.loading { opacity: 0.4; pointer-events: none; }

.skeleton {
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 200% 100%;
    animation: shimmer 1.2s infinite;
    border-radius: 8px;
}

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.range-labels { display: flex; justify-content: space-between; font-size: 0.72rem; color: #94a3b8; }

/* Map hover */
.kost-map-preview {
    height: 120px;
    background: #f1f5f9;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 0.8rem;
}

@media (max-width: 991px) {
    .filter-sidebar { position: static; }
    #filterCollapse { display: none; }
    #filterCollapse.show { display: block; }
}
</style>
@endpush

@section('content')
<div class="container py-4">

    <!-- SPA Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-800 mb-1">Cari Kost</h4>
            <p class="text-muted small mb-0">
                @if(request('search'))
                    Hasil pencarian untuk "<strong>{{ request('search') }}</strong>"
                @else
                    Menampilkan semua kost tersedia
                @endif
                &bull; <span id="resultCount">{{ $kosts->total() }}</span> kost ditemukan
            </p>
        </div>
        <button class="btn btn-outline-primary btn-sm d-lg-none" id="toggleFilter">
            <i class="fas fa-filter me-1"></i>Filter
        </button>
    </div>

    <div class="row g-4">
        <!-- SIDEBAR FILTER -->
        <div class="col-lg-3">
            <div class="filter-sidebar">
                <div class="filter-card card p-0" id="filterCollapse">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('kost.index') }}" id="filterForm">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-700 mb-0">Filter</h6>
                                <a href="{{ route('kost.index') }}" class="btn btn-sm btn-light small">Reset</a>
                            </div>

                            <!-- Search -->
                            <div class="mb-3">
                                <label class="form-label small fw-600">Kata Kunci</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Nama, kota, lokasi..." value="{{ request('search') }}">
                            </div>

                            <!-- Kota -->
                            <div class="mb-3">
                                <label class="form-label small fw-600">Kota</label>
                                <select name="city" class="form-select form-select-sm">
                                    <option value="">Semua Kota</option>
                                    @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tipe Kost -->
                            <div class="mb-3">
                                <label class="form-label small fw-600">Tipe Kost</label>
                                <div>
                                    @foreach(['Putra','Putri','Campur'] as $t)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" value="{{ $t }}"
                                            id="type_{{ $t }}" {{ request('type') === $t ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="type_{{ $t }}">Kost {{ $t }}</label>
                                    </div>
                                    @endforeach
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" value=""
                                            id="type_all" {{ !request('type') ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="type_all">Semua Tipe</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Harga Range -->
                            <div class="mb-3">
                                <label class="form-label small fw-600">Harga per Bulan</label>
                                <div class="mb-1">
                                    <span class="text-primary fw-700 small" id="minPriceLabel">
                                        Rp {{ number_format(request('min_price', 0)) }}
                                    </span>
                                    <span class="text-muted small"> — </span>
                                    <span class="text-primary fw-700 small" id="maxPriceLabel">
                                        Rp {{ number_format(request('max_price', 5000000)) }}
                                    </span>
                                </div>
                                <input type="range" name="min_price" id="minPrice" class="form-range"
                                    min="0" max="5000000" step="100000"
                                    value="{{ request('min_price', 0) }}">
                                <input type="range" name="max_price" id="maxPrice" class="form-range"
                                    min="0" max="5000000" step="100000"
                                    value="{{ request('max_price', 5000000) }}">
                                <div class="range-labels"><span>Rp 0</span><span>Rp 5jt</span></div>
                            </div>

                            <!-- Fasilitas (Checkboxes) -->
                            <div class="mb-3">
                                <label class="form-label small fw-600">Fasilitas</label>
                                <div class="row g-1">
                                    @foreach(['WiFi','AC','Kamar Mandi Dalam','Parkir','Laundry','Dapur Bersama','CCTV'] as $fac)
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="facilities[]"
                                                value="{{ $fac }}" id="fac_{{ $loop->index }}"
                                                {{ in_array($fac, request('facilities', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" style="font-size:0.78rem" for="fac_{{ $loop->index }}">
                                                {{ $fac }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-sm">
                                <i class="fas fa-search me-1"></i>Terapkan Filter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOST LISTING -->
        <div class="col-lg-9">
            <!-- Sort Bar -->
            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                <span class="text-muted small">Urutkan:</span>
                @foreach(['latest' => 'Terbaru', 'price_asc' => 'Harga ↑', 'price_desc' => 'Harga ↓', 'popular' => 'Terpopuler'] as $val => $label)
                <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}"
                    class="sort-btn {{ request('sort', 'latest') === $val ? 'active' : '' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>

            <!-- Featured Banner (SPA component) -->
            @if(!request()->except('page') && $featured->count())
            <div class="card mb-4 border-0" style="background:linear-gradient(135deg,#1e3a8a,#2563eb);border-radius:16px;overflow:hidden">
                <div class="card-body p-4 text-white">
                    <h6 class="fw-700 mb-3">⭐ Kost Unggulan</h6>
                    <div class="row g-2">
                        @foreach($featured as $f)
                        <div class="col-md-4">
                            <a href="{{ route('kost.show', $f->slug) }}" class="text-decoration-none">
                                <div class="d-flex gap-2 align-items-center bg-white bg-opacity-10 rounded-3 p-2">
                                    <img src="{{ $f->thumbnail_url }}" class="rounded-2" style="width:40px;height:40px;object-fit:cover" alt="">
                                    <div>
                                        <div class="fw-600 small text-white">{{ Str::limit($f->name, 20) }}</div>
                                        <div style="font-size:0.72rem;opacity:0.8">{{ $f->city }} &bull; {{ $f->price_formatted }}/bln</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Grid -->
            <div class="row g-4" id="kostGrid">
                @forelse($kosts as $kost)
                <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                    <div class="card kost-card h-100">
                        <div class="kost-img-wrap">
                            <img src="{{ $kost->thumbnail_url }}" class="card-img-top" alt="{{ $kost->name }}">
                            <span class="badge type-badge
                                {{ $kost->type === 'Putra' ? 'bg-primary' : ($kost->type === 'Putri' ? 'bg-danger' : 'bg-success') }}">
                                {{ $kost->type }}
                            </span>
                            @if($kost->is_featured)
                            <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark" style="font-size:0.7rem">⭐ Unggulan</span>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-700 mb-1">{{ $kost->name }}</h6>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $kost->city }}, {{ $kost->province }}
                            </p>

                            <!-- Rating -->
                            <div class="d-flex align-items-center gap-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star" style="font-size:0.7rem;color:{{ $kost->average_rating >= $i ? '#f59e0b' : '#d1d5db' }}"></i>
                                @endfor
                                <span class="text-muted small ms-1">({{ $kost->reviews->count() }} ulasan)</span>
                            </div>

                            <!-- Facilities -->
                            <div class="mb-3">
                                @if($kost->facilities)
                                    @foreach(array_slice($kost->facilities, 0, 4) as $fac)
                                    <span class="facility-chip">{{ $fac }}</span>
                                    @endforeach
                                    @if(count($kost->facilities) > 4)
                                    <span class="facility-chip">+{{ count($kost->facilities) - 4 }}</span>
                                    @endif
                                @endif
                            </div>

                            <!-- Available Rooms -->
                            <div class="d-flex justify-content-between align-items-center mb-3 small text-muted">
                                <span><i class="fas fa-door-open me-1 text-success"></i>{{ $kost->available_rooms }} kamar tersedia</span>
                                <span><i class="fas fa-ruler-combined me-1"></i>Min. {{ $kost->min_stay }} bln</span>
                            </div>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="price-tag">{{ $kost->price_formatted }}</div>
                                        <small class="text-muted">/bulan</small>
                                    </div>
                                    <a href="{{ route('kost.show', $kost->slug) }}" class="btn btn-primary btn-sm px-3">
                                        Lihat <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5" data-aos="fade-up">
                    <i class="fas fa-search fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Kost tidak ditemukan</h5>
                    <p class="text-muted small">Coba ubah filter pencarian Anda</p>
                    <a href="{{ route('kost.index') }}" class="btn btn-outline-primary">Tampilkan Semua</a>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($kosts->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $kosts->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Mobile filter toggle
document.getElementById('toggleFilter').addEventListener('click', function() {
    const f = document.getElementById('filterCollapse');
    f.classList.toggle('show');
});

// Price range labels
const minPrice = document.getElementById('minPrice');
const maxPrice = document.getElementById('maxPrice');
const minLabel = document.getElementById('minPriceLabel');
const maxLabel = document.getElementById('maxPriceLabel');

minPrice.addEventListener('input', function() {
    minLabel.textContent = 'Rp ' + parseInt(this.value).toLocaleString('id-ID');
    if (parseInt(this.value) > parseInt(maxPrice.value)) {
        maxPrice.value = this.value;
        maxLabel.textContent = minLabel.textContent;
    }
});

maxPrice.addEventListener('input', function() {
    maxLabel.textContent = 'Rp ' + parseInt(this.value).toLocaleString('id-ID');
});

// SPA: Auto-submit filter form on change (debounced)
let filterTimer;
document.querySelectorAll('#filterForm input, #filterForm select').forEach(el => {
    el.addEventListener('change', function() {
        if (this.type === 'text') return; // skip text, let user type
        clearTimeout(filterTimer);
        filterTimer = setTimeout(() => {
            document.getElementById('kostGrid').classList.add('loading');
            document.getElementById('filterForm').submit();
        }, 200);
    });
});
</script>
@endpush
