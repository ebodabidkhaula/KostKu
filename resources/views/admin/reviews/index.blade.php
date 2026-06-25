@extends('layouts.admin')

@section('title', 'Moderasi Ulasan')
@section('page-title', 'Moderasi Ulasan')
@section('breadcrumb')
<li class="breadcrumb-item active">Ulasan</li>
@endsection

@section('content')

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="rating" class="form-select">
                    <option value="">Semua Rating</option>
                    @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Reviews -->
<div class="row g-3">
    @forelse($reviews as $review)
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div class="d-flex gap-3">
                        <img src="{{ $review->user->avatar_url }}" class="rounded-circle" width="44" height="44" style="object-fit:cover" alt="">
                        <div>
                            <div class="fw-700">{{ $review->user->name }}</div>
                            <div class="text-muted small mb-1">
                                <i class="fas fa-building me-1"></i>{{ $review->kost->name ?? '-' }}
                            </div>
                            <div class="stars mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star" style="color:{{ $review->rating >= $i ? '#f59e0b' : '#d1d5db' }}"></i>
                                @endfor
                            </div>
                            <p class="text-muted mb-0">{{ $review->comment }}</p>
                            <small class="text-muted">{{ $review->created_at->format('d M Y, H:i') }}</small>
                        </div>
                    </div>
                    <div class="text-end d-flex flex-column gap-2" style="min-width:120px">
                        <span class="badge bg-{{ $review->is_approved ? 'success' : 'warning' }}">
                            {{ $review->is_approved ? 'Disetujui' : 'Pending' }}
                        </span>
                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-{{ $review->is_approved ? 'outline-secondary' : 'success' }} w-100">
                                <i class="fas fa-{{ $review->is_approved ? 'times' : 'check' }} me-1"></i>
                                {{ $review->is_approved ? 'Batalkan' : 'Setujui' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger w-100" data-confirm-delete="Hapus ulasan ini?">
                                <i class="fas fa-trash me-1"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="fas fa-star fa-3x text-muted mb-3 d-block"></i>
        <p class="text-muted">Belum ada ulasan</p>
    </div>
    @endforelse
</div>

@if($reviews->hasPages())
<div class="d-flex justify-content-center mt-4">{{ $reviews->links() }}</div>
@endif

@endsection
