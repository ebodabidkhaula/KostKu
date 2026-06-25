@extends('layouts.app')

@section('title', 'Daftar Akun — KostKu')

@push('styles')
<style>
.auth-container {
    min-height: calc(100vh - 80px);
    padding: 40px 0;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.auth-card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(37,99,235,0.15);
    padding: 40px;
}

.step-indicator {
    display: flex;
    gap: 0;
    margin-bottom: 32px;
}

.step {
    flex: 1;
    text-align: center;
    position: relative;
}

.step::after {
    content: '';
    position: absolute;
    top: 18px;
    left: 50%;
    right: -50%;
    height: 2px;
    background: #e2e8f0;
    z-index: 0;
}

.step:last-child::after { display: none; }

.step-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 6px;
    font-weight: 700;
    font-size: 0.85rem;
    position: relative;
    z-index: 1;
    transition: all 0.3s;
}

.step.active .step-circle {
    background: var(--primary);
    color: white;
}

.step.done .step-circle {
    background: #10b981;
    color: white;
}

.step.done::after, .step.active::after {
    background: var(--primary);
}

.step-label { font-size: 0.72rem; color: #64748b; font-weight: 500; }

.form-section {
    display: none;
}
.form-section.active {
    display: block;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}

.input-group-text {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-right: none;
    border-radius: 10px 0 0 10px;
}

.form-control, .form-select {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 10px 10px 0;
}

.avatar-preview {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e2e8f0;
}

.facility-check label {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.2s;
}

.facility-check input:checked + label {
    border-color: var(--primary);
    background: #eff6ff;
    color: var(--primary);
}

.password-strength { height: 4px; border-radius: 2px; margin-top: 6px; transition: all 0.3s; }
</style>
@endpush

@section('content')
<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h2 class="fw-800">Buat Akun Baru</h2>
                    <p class="text-muted">Bergabunglah dan temukan kost impianmu</p>
                </div>

                <div class="auth-card">
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step active" id="step-ind-1">
                            <div class="step-circle">1</div>
                            <div class="step-label">Informasi Dasar</div>
                        </div>
                        <div class="step" id="step-ind-2">
                            <div class="step-circle">2</div>
                            <div class="step-label">Data Pribadi</div>
                        </div>
                        <div class="step" id="step-ind-3">
                            <div class="step-circle">3</div>
                            <div class="step-label">Keamanan</div>
                        </div>
                    </div>

                    @if($errors->any())
                    <div class="alert alert-danger rounded-3 mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
                        @csrf

                        <!-- ── STEP 1: Informasi Dasar ── -->
                        <div class="form-section active" id="section-1">
                            <h5 class="fw-700 mb-4">📋 Informasi Dasar</h5>
                            <div class="row g-3">
                                <!-- Nama Lengkap (Text) -->
                                <div class="col-12">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                                        <input type="text" name="name" class="form-control" placeholder="Nama lengkap Anda"
                                            value="{{ old('name') }}" required>
                                    </div>
                                </div>

                                <!-- Email (Email) -->
                                <div class="col-md-6">
                                    <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control" placeholder="email@contoh.com"
                                            value="{{ old('email') }}" required>
                                    </div>
                                </div>

                                <!-- Telepon (Tel) -->
                                <div class="col-md-6">
                                    <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="tel" name="phone" class="form-control" placeholder="08xxxxxxxxxx"
                                            value="{{ old('phone') }}" required>
                                    </div>
                                </div>

                                <!-- Jenis Kelamin (Radio Button) -->
                                <div class="col-12">
                                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" value="Laki-laki"
                                                id="male" {{ old('gender') === 'Laki-laki' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="male">
                                                <i class="fas fa-mars text-primary me-1"></i>Laki-laki
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" value="Perempuan"
                                                id="female" {{ old('gender') === 'Perempuan' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="female">
                                                <i class="fas fa-venus text-danger me-1"></i>Perempuan
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Foto Profil (File) -->
                                <div class="col-12">
                                    <label class="form-label">Foto Profil</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset('images/default-avatar.svg') }}" class="avatar-preview" id="avatarPreview" alt="Preview">
                                        <div>
                                            <input type="file" name="avatar" class="form-control" id="avatarInput"
                                                accept="image/jpeg,image/png,image/jpg">
                                            <small class="text-muted">JPG, PNG. Maks. 2MB</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary px-4" onclick="nextStep(1)">
                                    Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ── STEP 2: Data Pribadi ── -->
                        <div class="form-section" id="section-2">
                            <h5 class="fw-700 mb-4">👤 Data Pribadi</h5>
                            <div class="row g-3">
                                <!-- NIK (Text/Number) -->
                                <div class="col-md-6">
                                    <label class="form-label">NIK (KTP)</label>
                                    <input type="text" name="nik" class="form-control" placeholder="16 digit NIK"
                                        maxlength="16" value="{{ old('nik') }}">
                                    <small class="text-muted">Opsional, untuk verifikasi identitas</small>
                                </div>

                                <!-- Tanggal Lahir (Date) -->
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                                </div>

                                <!-- Pekerjaan (Dropdown/Select) -->
                                <div class="col-md-6">
                                    <label class="form-label">Pekerjaan</label>
                                    <select name="occupation" class="form-select">
                                        <option value="">-- Pilih Pekerjaan --</option>
                                        <option value="Mahasiswa" {{ old('occupation') === 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                        <option value="Karyawan Swasta" {{ old('occupation') === 'Karyawan Swasta' ? 'selected' : '' }}>Karyawan Swasta</option>
                                        <option value="PNS" {{ old('occupation') === 'PNS' ? 'selected' : '' }}>PNS</option>
                                        <option value="Wiraswasta" {{ old('occupation') === 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                                        <option value="Freelancer" {{ old('occupation') === 'Freelancer' ? 'selected' : '' }}>Freelancer</option>
                                        <option value="Lainnya" {{ old('occupation') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>

                                <!-- Range (Rentang Harga Budget) -->
                                <div class="col-md-6">
                                    <label class="form-label">Budget Kost per Bulan: <span id="budgetLabel" class="text-primary fw-700">Rp 500.000</span></label>
                                    <input type="range" name="budget_range" class="form-range" min="300000" max="5000000"
                                        step="100000" value="500000" id="budgetRange">
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span>Rp 300rb</span><span>Rp 5jt</span>
                                    </div>
                                </div>

                                <!-- Alamat (Textarea) -->
                                <div class="col-12">
                                    <label class="form-label">Alamat Asal</label>
                                    <textarea name="address" class="form-control" rows="3"
                                        placeholder="Jalan, kota, provinsi">{{ old('address') }}</textarea>
                                </div>

                                <!-- Preferences (Checkbox) -->
                                <div class="col-12">
                                    <label class="form-label">Preferensi Kost (Pilih yang sesuai)</label>
                                    <div class="row g-2">
                                        @foreach(['WiFi', 'AC', 'Kamar Mandi Dalam', 'Dapur', 'Parkir', 'Laundry', 'CCTV', 'Gym'] as $pref)
                                        <div class="col-md-3 col-6 facility-check">
                                            <input type="checkbox" name="preferences[]" value="{{ $pref }}"
                                                id="pref_{{ $loop->index }}" class="d-none"
                                                {{ in_array($pref, old('preferences', [])) ? 'checked' : '' }}>
                                            <label for="pref_{{ $loop->index }}">
                                                <i class="fas fa-check-circle text-primary" style="display:none"></i>
                                                {{ $pref }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-light px-4" onclick="prevStep(2)">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </button>
                                <button type="button" class="btn btn-primary px-4" onclick="nextStep(2)">
                                    Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ── STEP 3: Keamanan ── -->
                        <div class="form-section" id="section-3">
                            <h5 class="fw-700 mb-4">🔒 Buat Password</h5>
                            <div class="row g-3">
                                <!-- Password -->
                                <div class="col-12">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" name="password" id="regPassword"
                                            class="form-control" placeholder="Minimal 8 karakter" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('regPassword', 'regPassIcon')" style="border:1.5px solid #e2e8f0;border-left:none;border-radius:0 10px 10px 0">
                                            <i class="fas fa-eye" id="regPassIcon"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength mt-2" id="strengthBar" style="background:#e2e8f0;width:0%"></div>
                                    <small class="text-muted" id="strengthText">Masukkan password</small>
                                </div>

                                <!-- Konfirmasi Password -->
                                <div class="col-12">
                                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" name="password_confirmation" id="regPassword2"
                                            class="form-control" placeholder="Ulangi password" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('regPassword2', 'regPass2Icon')" style="border:1.5px solid #e2e8f0;border-left:none;border-radius:0 10px 10px 0">
                                            <i class="fas fa-eye" id="regPass2Icon"></i>
                                        </button>
                                    </div>
                                    <small id="matchText" class="d-none text-danger">Password tidak cocok</small>
                                </div>

                                <!-- Terms (Checkbox) -->
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agree_terms" id="terms" required>
                                        <label class="form-check-label small" for="terms">
                                            Saya menyetujui
                                            <a href="#" class="text-primary fw-600">Syarat & Ketentuan</a> dan
                                            <a href="#" class="text-primary fw-600">Kebijakan Privasi</a> KostKu
                                        </label>
                                    </div>
                                </div>

                                <!-- Newsletter (Checkbox) -->
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="newsletter" id="newsletter">
                                        <label class="form-check-label small" for="newsletter">
                                            Kirim info promo dan penawaran kost terbaru ke email saya
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-light px-4" onclick="prevStep(3)">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </button>
                                <button type="submit" class="btn btn-success px-5 fw-700">
                                    <i class="fas fa-user-plus me-2"></i>Buat Akun Sekarang
                                </button>
                            </div>
                        </div>
                    </form>

                    <p class="text-center text-muted small mt-4">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-primary fw-700 text-decoration-none">Masuk di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStep = 1;

function nextStep(from) {
    document.getElementById('section-' + from).classList.remove('active');
    document.getElementById('section-' + (from + 1)).classList.add('active');
    document.getElementById('step-ind-' + from).classList.replace('active', 'done');
    document.getElementById('step-ind-' + (from + 1)).classList.add('active');
    currentStep = from + 1;
}

function prevStep(from) {
    document.getElementById('section-' + from).classList.remove('active');
    document.getElementById('section-' + (from - 1)).classList.add('active');
    document.getElementById('step-ind-' + from).classList.remove('active');
    document.getElementById('step-ind-' + (from - 1)).classList.replace('done', 'active');
    currentStep = from - 1;
}

function togglePwd(fieldId, iconId) {
    const f = document.getElementById(fieldId);
    const i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}

// Avatar preview
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(file);
    }
});

// Budget range
document.getElementById('budgetRange').addEventListener('input', function() {
    const v = parseInt(this.value);
    document.getElementById('budgetLabel').textContent = 'Rp ' + v.toLocaleString('id-ID');
});

// Password strength
document.getElementById('regPassword').addEventListener('input', function() {
    const v = this.value;
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    let strength = 0;
    let label = '', color = '';

    if (v.length >= 8) strength++;
    if (/[A-Z]/.test(v)) strength++;
    if (/[0-9]/.test(v)) strength++;
    if (/[^A-Za-z0-9]/.test(v)) strength++;

    const map = [
        ['', '', '#e2e8f0'],
        ['Lemah', '25%', '#ef4444'],
        ['Cukup', '50%', '#f59e0b'],
        ['Kuat', '75%', '#3b82f6'],
        ['Sangat Kuat', '100%', '#10b981'],
    ];

    [label, , color] = map[strength];
    bar.style.width = map[strength][1];
    bar.style.background = color;
    text.textContent = label ? `Kekuatan: ${label}` : 'Masukkan password';
    text.style.color = color;
});

// Password match
document.getElementById('regPassword2').addEventListener('input', function() {
    const match = document.getElementById('matchText');
    const p1 = document.getElementById('regPassword').value;
    if (this.value && this.value !== p1) {
        match.classList.remove('d-none');
    } else {
        match.classList.add('d-none');
    }
});

// Checkbox style update for facility checks
document.querySelectorAll('.facility-check input').forEach(cb => {
    cb.addEventListener('change', function() {
        const label = this.nextElementSibling;
        const icon = label.querySelector('i');
        if (this.checked) {
            label.style.borderColor = 'var(--primary)';
            label.style.background = '#eff6ff';
            label.style.color = 'var(--primary)';
            icon.style.display = 'inline';
        } else {
            label.style.borderColor = '#e2e8f0';
            label.style.background = '';
            label.style.color = '';
            icon.style.display = 'none';
        }
    });
});

// If errors, jump to step with errors
@if($errors->any())
    // Attempt to find which section has an error and go there
    const errorFields = {!! json_encode($errors->keys()) !!};
    const step1Fields = ['name', 'email', 'phone', 'gender', 'avatar'];
    const step2Fields = ['nik', 'dob', 'occupation', 'address'];
    let targetStep = 3;
    if (errorFields.some(f => step1Fields.includes(f))) targetStep = 1;
    else if (errorFields.some(f => step2Fields.includes(f))) targetStep = 2;

    for (let s = 1; s < targetStep; s++) nextStep(s);
@endif
</script>
@endpush
