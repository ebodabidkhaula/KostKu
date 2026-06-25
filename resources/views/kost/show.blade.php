@extends('layouts.app')

@section('title', $kost->name . ' — KostKu')

@push('styles')
<style>
.gallery-main img {
    width: 100%;
    height: 420px;
    object-fit: cover;
    border-radius: 16px;
    cursor: pointer;
}

.gallery-thumbs {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-top: 8px;
}

.gallery-thumbs img {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s;
}

.gallery-thumbs img:hover,
.gallery-thumbs img.active { border-color: var(--primary); }

.info-box {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.booking-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 40px rgba(37,99,235,0.15);
    padding: 28px;
    position: sticky;
    top: 90px;
}

.facility-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 10px;
}

.facility-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    background: #f8fafc;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 500;
}

.facility-item i { color: var(--primary); width: 16px; text-align: center; }

.review-card {
    border: 1.5px solid #f1f5f9;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
}

.lightbox {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.92);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.lightbox.show { display: flex; }
.lightbox img { max-width: 90vw; max-height: 85vh; object-fit: contain; border-radius: 8px; }
.lightbox-close {
    position: absolute;
    top: 20px;
    right: 24px;
    color: white;
    font-size: 2rem;
    cursor: pointer;
    line-height: 1;
    z-index: 2;
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.12);
    border: none;
    color: white;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    font-size: 1.2rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    z-index: 2;
}

.lightbox-nav:hover { background: rgba(255,255,255,0.25); }
.lightbox-prev { left: 16px; }
.lightbox-next { right: 16px; }

.lightbox-counter {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    background: rgba(0,0,0,0.4);
    border-radius: 20px;
    padding: 4px 16px;
    font-size: 0.85rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .lightbox-nav { width: 40px; height: 40px; font-size: 1rem; }
    .lightbox-prev { left: 8px; }
    .lightbox-next { right: 8px; }
}

.rating-input {
    display: flex;
    gap: 6px;
    font-size: 1.8rem;
    cursor: pointer;
}

.rating-input i {
    color: #d1d5db;
    transition: color 0.2s;
}

.rating-input i.active { color: #f59e0b; }

.video-section {
    background: #0f172a;
    border-radius: 16px;
    overflow: hidden;
    position: relative;
}

.video-section video {
    width: 100%;
    max-height: 350px;
    object-fit: cover;
}

.video-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(0,0,0,0.6);
    color: white;
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 0.75rem;
    font-weight: 700;
}
</style>
@endpush

@section('content')
<div class="container py-4">

    <!-- Breadcrumb -->
    <nav class="mb-3">
        <ol class="breadcrumb" style="font-size:0.82rem">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kost.index') }}" class="text-primary text-decoration-none">Cari Kost</a></li>
            <li class="breadcrumb-item active">{{ $kost->name }}</li>
        </ol>
    </nav>

    <!-- Title -->
    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
        <div>
            <h2 class="fw-800 mb-1">{{ $kost->name }}</h2>
            <p class="text-muted mb-0">
                <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                {{ $kost->address }}, {{ $kost->city }}, {{ $kost->province }}
            </p>
        </div>
        <div class="text-end">
            <div style="font-size:1.6rem;font-weight:800;color:var(--primary)">{{ $kost->price_formatted }}</div>
            <div class="text-muted small">/bulan</div>
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT: Gallery + Info -->
        <div class="col-lg-8">

            <!-- Photo Gallery -->
            <div class="info-box p-3 mb-4">
                @if($kost->photos->count())
                <div class="gallery-main">
                    <img src="{{ $kost->photos->first()->url }}" id="mainPhoto" alt="{{ $kost->name }}"
                        onclick="openLightbox()">
                </div>
                @if($kost->photos->count() > 1)
                <div class="gallery-thumbs mt-2">
                    @foreach($kost->photos->take(8) as $i => $photo)
                    <img src="{{ $photo->url }}" class="{{ $i === 0 ? 'active' : '' }}"
                        onclick="changePhoto('{{ $photo->url }}', this, {{ $i }})">
                    @endforeach
                </div>
                @endif
                @else
                <img src="{{ $kost->thumbnail_url }}" alt="{{ $kost->name }}"
                    style="width:100%;height:380px;object-fit:cover;border-radius:12px">
                @endif
            </div>

            <!-- Video Tour -->
            @if($kost->video_tour)
            <div class="info-box p-0 mb-4">
                <div class="video-section">
                    <video controls preload="metadata">
                        <source src="{{ asset('storage/videos/' . $kost->video_tour) }}" type="video/mp4">
                        Browser Anda tidak mendukung video.
                    </video>
                    <span class="video-badge"><i class="fas fa-play me-1"></i>Tur Virtual 360°</span>
                </div>
            </div>
            @endif

            <!-- Deskripsi -->
            <div class="info-box mb-4">
                <h5 class="fw-700 mb-3">Tentang Kost Ini</h5>
                <p class="text-muted" style="line-height:1.8">{{ $kost->description }}</p>

                <!-- Quick Info -->
                <div class="row g-3 mt-2">
                    @foreach([
                        ['icon' => 'fas fa-building', 'label' => 'Tipe', 'value' => 'Kost ' . $kost->type],
                        ['icon' => 'fas fa-door-open', 'label' => 'Kamar Tersedia', 'value' => $kost->available_rooms . '/' . $kost->total_rooms],
                        ['icon' => 'fas fa-calendar', 'label' => 'Min. Sewa', 'value' => $kost->min_stay . ' Bulan'],
                        ['icon' => 'fas fa-clock', 'label' => 'Jam Tamu', 'value' => ($kost->entry_time ? substr($kost->entry_time,0,5) : '06:00') . '–' . ($kost->exit_time ? substr($kost->exit_time,0,5) : '22:00')],
                        ['icon' => 'fas fa-utensils', 'label' => 'Masak', 'value' => $kost->allow_cooking ? 'Boleh' : 'Tidak Boleh'],
                        ['icon' => 'fas fa-paw', 'label' => 'Hewan Peliharaan', 'value' => $kost->allow_pets ? 'Boleh' : 'Tidak Boleh'],
                    ] as $info)
                    <div class="col-md-4 col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="{{ $info['icon'] }} text-primary" style="width:16px"></i>
                            <div>
                                <div class="text-muted" style="font-size:0.72rem">{{ $info['label'] }}</div>
                                <div class="fw-600 small">{{ $info['value'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Fasilitas Kamar -->
            @if($kost->facilities)
            <div class="info-box mb-4">
                <h5 class="fw-700 mb-3"><i class="fas fa-couch me-2 text-primary"></i>Fasilitas Kamar</h5>
                <div class="facility-grid">
                    @foreach($kost->facilities as $fac)
                    <div class="facility-item">
                        <i class="{{ match($fac) {
                            'AC' => 'fas fa-snowflake',
                            'WiFi' => 'fas fa-wifi',
                            'Kasur' => 'fas fa-bed',
                            'Lemari' => 'fas fa-archive',
                            'TV' => 'fas fa-tv',
                            'Kamar Mandi Dalam' => 'fas fa-shower',
                            'Water Heater' => 'fas fa-hot-tub',
                            'Kulkas Mini' => 'fas fa-thermometer-three-quarters',
                            'Balkon' => 'fas fa-door-open',
                            'Meja Belajar' => 'fas fa-desktop',
                            default => 'fas fa-check-circle',
                        } }}"></i>
                        {{ $fac }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Fasilitas Bersama -->
            @if($kost->shared_facilities)
            <div class="info-box mb-4">
                <h5 class="fw-700 mb-3"><i class="fas fa-users me-2 text-primary"></i>Fasilitas Bersama</h5>
                <div class="facility-grid">
                    @foreach($kost->shared_facilities as $fac)
                    <div class="facility-item">
                        <i class="fas fa-check-circle"></i>{{ $fac }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Peraturan -->
            @if($kost->rules)
            <div class="info-box mb-4">
                <h5 class="fw-700 mb-3"><i class="fas fa-list-ul me-2 text-primary"></i>Peraturan Kost</h5>
                <ul class="list-unstyled">
                    @foreach($kost->rules as $rule)
                    <li class="d-flex gap-2 mb-2 small">
                        <i class="fas fa-exclamation-circle text-warning mt-1 flex-shrink-0"></i>
                        {{ $rule }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Ulasan -->
            <div class="info-box mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-700 mb-0">
                        <i class="fas fa-star me-2 text-warning"></i>Ulasan
                        <span class="text-muted fw-400">({{ $kost->reviews->count() }})</span>
                    </h5>
                    @if($kost->reviews->count() > 0)
                    <div class="d-flex align-items-center gap-1">
                        <span class="fw-800 text-primary" style="font-size:1.5rem">{{ number_format($kost->average_rating, 1) }}</span>
                        <div>
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star" style="color:{{ $kost->average_rating >= $i ? '#f59e0b' : '#d1d5db' }};font-size:0.75rem"></i>
                                @endfor
                            </div>
                            <div style="font-size:0.7rem;color:#94a3b8">dari 5</div>
                        </div>
                    </div>
                    @endif
                </div>

                @forelse($kost->reviews->take(5) as $review)
                <div class="review-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex gap-2">
                            <img src="{{ $review->user->avatar_url }}" class="rounded-circle" width="36" height="36" style="object-fit:cover">
                            <div>
                                <div class="fw-600 small">{{ $review->user->name }}</div>
                                <div class="stars" style="font-size:0.65rem">
                                    @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star" style="color:{{ $review->rating >= $i ? '#f59e0b' : '#d1d5db' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="text-muted small mb-0">{{ $review->comment }}</p>
                </div>
                @empty
                <p class="text-muted text-center py-3">Belum ada ulasan untuk kost ini</p>
                @endforelse

                <!-- Form Tambah Ulasan -->
                <hr class="my-4">

                @auth
                    @if($userReview)
                    <div class="alert alert-info rounded-3 mb-0" style="font-size:0.85rem">
                        <i class="fas fa-info-circle me-2"></i>
                        Anda sudah memberikan ulasan untuk kost ini
                        @if(!$userReview->is_approved)
                            <span class="badge bg-warning text-dark ms-1">Menunggu persetujuan admin</span>
                        @endif
                    </div>
                    @else
                    <h6 class="fw-700 mb-3">✍️ Tulis Ulasan Anda</h6>

                    @error('comment')
                        <div class="alert alert-danger rounded-3 small">{{ $message }}</div>
                    @enderror
                    @error('rating')
                        <div class="alert alert-danger rounded-3 small">{{ $message }}</div>
                    @enderror

                    <form action="{{ route('kost.review.store', $kost->slug) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-600">Rating Anda</label>
                            <div class="rating-input" id="kostRatingInput">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star" data-value="{{ $i }}"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="kostRatingValue" value="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-600">Komentar</label>
                            <textarea name="comment" class="form-control" rows="3" required minlength="10"
                                placeholder="Bagaimana pengalaman Anda dengan kost ini?">{{ old('comment') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Ulasan
                        </button>
                    </form>
                    @endif
                @else
                    <div class="alert alert-light border rounded-3 mb-0 text-center" style="font-size:0.85rem">
                        <i class="fas fa-lock me-2 text-muted"></i>
                        <a href="{{ route('login') }}" class="text-primary fw-600 text-decoration-none">Masuk</a>
                        untuk memberikan ulasan kost ini
                    </div>
                @endauth
            </div>
        </div>

        <!-- RIGHT: Booking Card -->
        <div class="col-lg-4">
            <div class="booking-card">
                <div class="text-center mb-3">
                    <div style="font-size:1.8rem;font-weight:800;color:var(--primary)">{{ $kost->price_formatted }}</div>
                    <div class="text-muted small">/bulan</div>
                    @if($kost->price_yearly)
                    <div class="text-success small fw-600">💰 Hemat dengan sewa tahunan!</div>
                    @endif
                </div>

                <!-- Availability -->
                <div class="d-flex justify-content-between align-items-center p-3 rounded-3 mb-3
                    {{ $kost->available_rooms > 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10">
                    <span class="fw-600 small {{ $kost->available_rooms > 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-door-open me-1"></i>
                        {{ $kost->available_rooms > 0 ? $kost->available_rooms . ' kamar tersedia' : 'Kamar penuh' }}
                    </span>
                    <span class="badge {{ $kost->available_rooms > 0 ? 'bg-success' : 'bg-danger' }}">
                        {{ $kost->available_rooms > 0 ? 'Tersedia' : 'Penuh' }}
                    </span>
                </div>

                @auth
                @if($kost->available_rooms > 0)
                <a href="{{ route('user.booking.create', $kost) }}" class="btn btn-primary w-100 mb-2 py-3 fw-700">
                    <i class="fas fa-calendar-plus me-2"></i>Booking Sekarang
                </a>
                @else
                <button class="btn btn-secondary w-100 mb-2" disabled>Kamar Penuh</button>
                @endif
                @else
                <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2 py-3 fw-700">
                    <i class="fas fa-sign-in-alt me-2"></i>Masuk untuk Booking
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 mb-2">
                    Daftar Gratis
                </a>
                @endauth

                <hr>

                <!-- Kontak Pemilik -->
                <div class="mb-3">
                    <div class="fw-700 small mb-2">👤 Pemilik Kost</div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width:40px;height:40px;font-size:0.9rem;font-weight:700;flex-shrink:0">
                            {{ strtoupper(substr($kost->owner_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-600 small">{{ $kost->owner_name }}</div>
                            <div class="text-muted" style="font-size:0.8rem">
                                <i class="fas fa-phone me-1"></i>{{ $kost->owner_phone }}
                            </div>
                        </div>
                    </div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kost->owner_phone) }}"
                        target="_blank" class="btn btn-success btn-sm w-100 mt-2">
                        <i class="fab fa-whatsapp me-1"></i>WhatsApp Pemilik
                    </a>
                </div>

                <!-- Info Singkat -->
                <div class="rounded-3 p-3" style="background:#f8fafc;font-size:0.82rem">
                    @foreach([
                        ['icon' => 'fas fa-building', 'text' => 'Tipe: Kost ' . $kost->type],
                        ['icon' => 'fas fa-calendar', 'text' => 'Min. Sewa: ' . $kost->min_stay . ' Bulan'],
                        ['icon' => 'fas fa-city', 'text' => $kost->city . ', ' . $kost->province],
                    ] as $inf)
                    <div class="d-flex gap-2 mb-1">
                        <i class="{{ $inf['icon'] }} text-primary" style="width:14px;margin-top:2px"></i>
                        <span class="text-muted">{{ $inf['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Related Kost -->
    @if($related->count())
    <div class="mt-5">
        <h5 class="fw-700 mb-4">Kost Lainnya di {{ $kost->city }}</h5>
        <div class="row g-3">
            @foreach($related as $r)
            <div class="col-md-4" data-aos="fade-up">
                <div class="card kost-card">
                    <div class="kost-img-wrap">
                        <img src="{{ $r->thumbnail_url }}" class="card-img-top" style="height:160px;object-fit:cover" alt="">
                    </div>
                    <div class="card-body">
                        <h6 class="fw-700 small mb-1">{{ $r->name }}</h6>
                        <p class="text-muted mb-2" style="font-size:0.78rem">
                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $r->city }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="price-tag" style="font-size:0.95rem">{{ $r->price_formatted }}</span>
                            <a href="{{ route('kost.show', $r->slug) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Lightbox -->
<div class="lightbox" id="lightbox">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <button class="lightbox-nav lightbox-prev" onclick="prevPhoto(event)" aria-label="Sebelumnya">
        <i class="fas fa-chevron-left"></i>
    </button>
    <img id="lightboxImg" src="" alt="">
    <button class="lightbox-nav lightbox-next" onclick="nextPhoto(event)" aria-label="Selanjutnya">
        <i class="fas fa-chevron-right"></i>
    </button>
    <div class="lightbox-counter" id="lightboxCounter"></div>
</div>

@endsection

@push('scripts')
<script>
const photos = @json($kost->photos->pluck('url'));
let currentPhotoIndex = 0;

function changePhoto(url, el, idx) {
    document.getElementById('mainPhoto').src = url;
    document.querySelectorAll('.gallery-thumbs img').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
    currentPhotoIndex = idx;
}

function updateLightboxImage() {
    document.getElementById('lightboxImg').src = photos[currentPhotoIndex] || document.getElementById('mainPhoto').src;

    const counter = document.getElementById('lightboxCounter');
    if (photos.length > 1) {
        counter.textContent = (currentPhotoIndex + 1) + ' / ' + photos.length;
        counter.style.display = 'block';
    } else {
        counter.style.display = 'none';
    }

    // Sync main photo & thumbnail highlight
    if (photos[currentPhotoIndex]) {
        document.getElementById('mainPhoto').src = photos[currentPhotoIndex];
        document.querySelectorAll('.gallery-thumbs img').forEach((img, i) => {
            img.classList.toggle('active', i === currentPhotoIndex);
        });
    }

    // Hide nav buttons if only 1 photo
    const showNav = photos.length > 1;
    document.querySelectorAll('.lightbox-nav').forEach(btn => {
        btn.style.display = showNav ? 'flex' : 'none';
    });
}

function openLightbox() {
    const lb = document.getElementById('lightbox');
    updateLightboxImage();
    lb.classList.add('show');
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('show');
}

function nextPhoto(e) {
    if (e) e.stopPropagation();
    if (photos.length === 0) return;
    currentPhotoIndex = (currentPhotoIndex + 1) % photos.length;
    updateLightboxImage();
}

function prevPhoto(e) {
    if (e) e.stopPropagation();
    if (photos.length === 0) return;
    currentPhotoIndex = (currentPhotoIndex - 1 + photos.length) % photos.length;
    updateLightboxImage();
}

document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) closeLightbox();
});

document.addEventListener('keyup', e => {
    if (!document.getElementById('lightbox').classList.contains('show')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextPhoto();
    if (e.key === 'ArrowLeft') prevPhoto();
});

// Swipe support (touch devices)
let touchStartX = 0;
const lightboxEl = document.getElementById('lightbox');

lightboxEl.addEventListener('touchstart', e => {
    touchStartX = e.changedTouches[0].screenX;
}, { passive: true });

lightboxEl.addEventListener('touchend', e => {
    const touchEndX = e.changedTouches[0].screenX;
    const diff = touchEndX - touchStartX;
    if (Math.abs(diff) > 50) {
        if (diff < 0) nextPhoto();
        else prevPhoto();
    }
}, { passive: true });

// Review rating star input
const kostStars = document.querySelectorAll('#kostRatingInput i');
const kostRatingValue = document.getElementById('kostRatingValue');

if (kostStars.length) {
    kostStars.forEach(star => {
        star.addEventListener('click', function() {
            const val = parseInt(this.dataset.value);
            kostRatingValue.value = val;
            kostStars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.value) <= val));
        });
        star.addEventListener('mouseenter', function() {
            const val = parseInt(this.dataset.value);
            kostStars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.value) <= val));
        });
    });

    document.getElementById('kostRatingInput').addEventListener('mouseleave', function() {
        const val = parseInt(kostRatingValue.value);
        kostStars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.value) <= val));
    });
}
</script>
@endpush
