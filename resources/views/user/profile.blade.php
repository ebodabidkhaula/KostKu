@extends('layouts.app')

@section('title', 'Profil Saya — KostKu')

@push('styles')
<style>
.profile-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    padding: 24px;
    margin-bottom: 20px;
}

.avatar-edit {
    position: relative;
    width: 110px;
    height: 110px;
}

.avatar-edit img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #eff6ff;
}

.avatar-edit label {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 36px;
    height: 36px;
    background: var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    border: 3px solid white;
}

.nav-pills-custom .nav-link {
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    color: #64748b;
}

.nav-pills-custom .nav-link.active {
    background: var(--primary);
    color: white;
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <h3 class="fw-800 mb-4">Profil Saya</h3>

    <!-- Tabs -->
    <ul class="nav nav-pills nav-pills-custom mb-4 gap-2" id="profileTabs">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-profile">Informasi Profil</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-password">Ubah Password</a></li>
    </ul>

    <div class="tab-content">
        <!-- Profile Tab -->
        <div class="tab-pane fade show active" id="tab-profile">
            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="profile-card">
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="avatar-edit">
                            <img src="{{ $user->avatar_url }}" id="avatarPreview" alt="">
                            <label for="avatarInput">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/jpeg,image/png,image/jpg">
                        </div>
                        <div>
                            <h5 class="fw-700 mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                            <span class="badge bg-primary mt-1">{{ $user->occupation ?? 'Pengguna' }}</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Nama -->
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <!-- Email (readonly) -->
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            <small class="text-muted">Email tidak dapat diubah</small>
                        </div>

                        <!-- Telepon -->
                        <div class="col-md-6">
                            <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                        </div>

                        <!-- Jenis Kelamin (Radio) -->
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" value="Laki-laki" id="g1"
                                        {{ old('gender', $user->gender) === 'Laki-laki' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="g1">Laki-laki</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" value="Perempuan" id="g2"
                                        {{ old('gender', $user->gender) === 'Perempuan' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="g2">Perempuan</label>
                                </div>
                            </div>
                        </div>

                        <!-- NIK -->
                        <div class="col-md-6">
                            <label class="form-label">NIK (KTP)</label>
                            <input type="text" name="nik" class="form-control" maxlength="16" value="{{ old('nik', $user->nik) }}">
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob', $user->dob?->format('Y-m-d')) }}">
                        </div>

                        <!-- Pekerjaan -->
                        <div class="col-md-6">
                            <label class="form-label">Pekerjaan</label>
                            <select name="occupation" class="form-select">
                                <option value="">-- Pilih Pekerjaan --</option>
                                @foreach(['Mahasiswa','Karyawan Swasta','PNS','Wiraswasta','Freelancer','Lainnya'] as $occ)
                                <option value="{{ $occ }}" {{ old('occupation', $user->occupation) === $occ ? 'selected' : '' }}>{{ $occ }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Alamat -->
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" class="form-control" rows="3">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Password Tab -->
        <div class="tab-pane fade" id="tab-password">
            <form action="{{ route('user.password.update') }}" method="POST">
                @csrf @method('PUT')

                <div class="profile-card">
                    <h6 class="fw-700 mb-3">Ubah Password</h6>

                    @if($errors->any())
                    <div class="alert alert-danger rounded-3">{{ $errors->first() }}</div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
