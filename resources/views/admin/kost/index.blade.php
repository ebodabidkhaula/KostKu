@extends('layouts.admin')

@section('title', 'Manajemen Kost')
@section('page-title', 'Manajemen Kost')
@section('breadcrumb')
<li class="breadcrumb-item active">Data Kost</li>
@endsection

@section('content')

<!-- Filter & Actions Bar -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.kost.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama, kota..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">Semua Tipe</option>
                    <option value="Putra" {{ request('type') === 'Putra' ? 'selected' : '' }}>Putra</option>
                    <option value="Putri" {{ request('type') === 'Putri' ? 'selected' : '' }}>Putri</option>
                    <option value="Campur" {{ request('type') === 'Campur' ? 'selected' : '' }}>Campur</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="full" {{ request('status') === 'full' ? 'selected' : '' }}>Penuh</option>
                </select>
            </div>
            <div class="col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                <a href="{{ route('admin.kost.index') }}" class="btn btn-light">Reset</a>
            </div>
            <div class="col-md-auto ms-auto">
                <a href="{{ route('admin.kost.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Tambah Kost
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Kost Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="fw-700 mb-0">Daftar Kost <span class="badge bg-primary ms-2">{{ $kosts->total() }}</span></h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width:50px">#</th>
                        <th style="min-width:200px">Nama Kost</th>
                        <th>Tipe</th>
                        <th>Kota</th>
                        <th>Harga/Bulan</th>
                        <th>Kamar</th>
                        <th>Status</th>
                        <th>Unggulan</th>
                        <th class="text-center" style="width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kosts as $kost)
                    <tr>
                        <td class="ps-3 text-muted">{{ $kosts->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $kost->thumbnail_url }}" alt="" class="rounded-2"
                                    style="width:40px;height:40px;object-fit:cover;flex-shrink:0">
                                <div>
                                    <div class="fw-600 small">{{ $kost->name }}</div>
                                    <div class="text-muted" style="font-size:0.72rem">
                                        <i class="fas fa-bed me-1"></i>{{ $kost->rooms_count }} kamar
                                        <i class="fas fa-receipt ms-2 me-1"></i>{{ $kost->bookings_count }} booking
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill
                                {{ $kost->type === 'Putra' ? 'bg-primary' : ($kost->type === 'Putri' ? 'bg-danger' : 'bg-success') }}">
                                {{ $kost->type }}
                            </span>
                        </td>
                        <td class="small">{{ $kost->city }}</td>
                        <td class="fw-600 small text-primary">{{ $kost->price_formatted }}</td>
                        <td>
                            <span class="text-success fw-600">{{ $kost->available_rooms }}</span>
                            <span class="text-muted">/{{ $kost->total_rooms }}</span>
                        </td>
                        <td>
                            @switch($kost->status)
                                @case('active')
                                    <span class="badge bg-success">Aktif</span> @break
                                @case('inactive')
                                    <span class="badge bg-secondary">Nonaktif</span> @break
                                @case('full')
                                    <span class="badge bg-danger">Penuh</span> @break
                            @endswitch
                        </td>
                        <td>
                            @if($kost->is_featured)
                                <span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i>Ya</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.kost.show', $kost) }}" class="btn btn-sm btn-light" title="Detail">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                                <a href="{{ route('admin.kost.edit', $kost) }}" class="btn btn-sm btn-light" title="Edit">
                                    <i class="fas fa-edit text-warning"></i>
                                </a>
                                <form action="{{ route('admin.kost.destroy', $kost) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light" data-confirm-delete="Hapus kost '{{ $kost->name }}'?" title="Hapus">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-2">Belum ada data kost</p>
                            <a href="{{ route('admin.kost.create') }}" class="btn btn-primary btn-sm">Tambah Kost</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($kosts->hasPages())
    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
        <small class="text-muted">Menampilkan {{ $kosts->firstItem() }}–{{ $kosts->lastItem() }} dari {{ $kosts->total() }}</small>
        {{ $kosts->links() }}
    </div>
    @endif
</div>

@endsection
