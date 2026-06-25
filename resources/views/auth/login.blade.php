@extends('layouts.app')

@section('title', 'Masuk — KostKu')

@push('styles')
<style>
.auth-container {
    min-height: calc(100vh - 80px);
    display: flex;
    align-items: center;
    padding: 40px 0;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.auth-card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(37,99,235,0.15);
    overflow: hidden;
}

.auth-left {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    padding: 48px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    color: white;
}

.auth-right {
    padding: 48px;
}

.form-floating label { color: #64748b; }

.social-divider {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #94a3b8;
    margin: 20px 0;
}
.social-divider::before,
.social-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e2e8f0;
}

.input-group-text {
    border-radius: 10px 0 0 10px;
    border: 1.5px solid #e2e8f0;
    border-right: none;
    background: #f8fafc;
}

.input-with-icon input {
    border-radius: 0 10px 10px 0;
    border: 1.5px solid #e2e8f0;
    border-left: none;
}
</style>
@endpush

@section('content')
<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="auth-card">
                    <div class="row g-0">
                        <!-- Left Side -->
                        <div class="col-lg-5 d-none d-lg-block">
                            <div class="auth-left h-100">
                                <div class="mb-4">
                                    <div style="font-size:2.5rem;font-weight:800">Kost<span style="color:#fbbf24">Ku</span></div>
                                    <p class="mt-2" style="opacity:0.85">Platform kost terpercaya untuk hunian nyaman Anda</p>
                                </div>
                                <div class="mt-auto">
                                    @foreach([
                                        '✅ Ribuan pilihan kost terverifikasi',
                                        '✅ Booking mudah dan cepat',
                                        '✅ Harga transparan tanpa biaya tersembunyi',
                                        '✅ Customer support 24/7',
                                    ] as $feature)
                                    <div class="mb-2" style="font-size:0.9rem;opacity:0.9">{{ $feature }}</div>
                                    @endforeach
                                </div>
                                <div class="mt-4" style="opacity:0.5;font-size:0.8rem">
                                    © {{ date('Y') }} KostKu. All rights reserved.
                                </div>
                            </div>
                        </div>

                        <!-- Right Side (Form) -->
                        <div class="col-lg-7">
                            <div class="auth-right">
                                <h2 class="fw-800 mb-1">Selamat Datang! 👋</h2>
                                <p class="text-muted mb-4">Masuk ke akun Anda untuk melanjutkan</p>

                                @if($errors->any())
                                <div class="alert alert-danger rounded-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ $errors->first() }}
                                </div>
                                @endif

                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <div class="input-group input-with-icon">
                                            <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                                placeholder="email@contoh.com" value="{{ old('email') }}" required autocomplete="email">
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="mb-3">
                                        <label class="form-label d-flex justify-content-between">
                                            Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group input-with-icon">
                                            <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                            <input type="password" name="password" id="passwordField"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Masukkan password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-radius:0 10px 10px 0;border:1.5px solid #e2e8f0;border-left:none">
                                                <i class="fas fa-eye" id="toggleIcon"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Remember + Forgot -->
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                            <label class="form-check-label text-muted small" for="remember">Ingat saya</label>
                                        </div>
                                        <a href="#" class="text-primary text-decoration-none small fw-600">Lupa password?</a>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-3 fw-600" style="font-size:1rem">
                                        <i class="fas fa-sign-in-alt me-2"></i>Masuk
                                    </button>
                                </form>

                                <div class="social-divider small">atau</div>

                                <p class="text-center text-muted small">
                                    Belum punya akun?
                                    <a href="{{ route('register') }}" class="text-primary fw-700 text-decoration-none">Daftar sekarang</a>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const field = document.getElementById('passwordField');
    const icon = document.getElementById('toggleIcon');
    field.type = field.type === 'password' ? 'text' : 'password';
    icon.className = field.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
});
</script>
@endpush
