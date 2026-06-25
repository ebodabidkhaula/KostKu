@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')
@section('breadcrumb')
<li class="breadcrumb-item active">Pengguna</li>
@endsection

@section('content')

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Cari nama, email, telepon..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="gender" class="form-select">
                    <option value="">Semua Gender</option>
                    <option value="Laki-laki" {{ request('gender') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ request('gender') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header">
        <h6 class="fw-700 mb-0">Daftar Pengguna <span class="badge bg-primary ms-2">{{ $users->total() }}</span></h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Pengguna</th>
                        <th>Kontak</th>
                        <th>Gender</th>
                        <th>Pekerjaan</th>
                        <th>Booking</th>
                        <th>Status</th>
                        <th class="text-center" style="width:130px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $u->avatar_url }}" class="rounded-circle" width="36" height="36" style="object-fit:cover" alt="">
                                <div>
                                    <div class="fw-600 small">{{ $u->name }}</div>
                                    <div class="text-muted" style="font-size:0.72rem">Bergabung {{ $u->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small">{{ $u->email }}</div>
                            <div class="text-muted small">{{ $u->phone }}</div>
                        </td>
                        <td class="small">{{ $u->gender ?? '-' }}</td>
                        <td class="small">{{ $u->occupation ?? '-' }}</td>
                        <td><span class="badge bg-info">{{ $u->bookings_count }}</span></td>
                        <td>
                            <span class="badge bg-{{ $u->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $u->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.users.show', $u) }}" class="btn btn-sm btn-light" title="Detail">
                                <i class="fas fa-eye text-primary"></i>
                            </a>
                            <form action="{{ route('admin.users.toggle', $u) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-light" title="{{ $u->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas fa-{{ $u->status === 'active' ? 'lock' : 'unlock' }} text-warning"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light" data-confirm-delete="Hapus pengguna {{ $u->name }}?">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data pengguna</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
        <small class="text-muted">Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }}</small>
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection
