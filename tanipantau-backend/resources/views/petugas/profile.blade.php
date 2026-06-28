@extends('layouts.petugas')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@push('styles')
<style>
    .profile-card {
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-light);
        overflow: hidden;
    }
    .profile-header {
        background: linear-gradient(135deg, var(--dark-bg) 0%, var(--primary) 100%);
        padding: 40px 30px;
        position: relative;
        text-align: center;
        border-bottom: 4px solid var(--accent);
    }
    .profile-avatar-large {
        width: 100px;
        height: 100px;
        background: var(--white);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 40px;
        font-weight: 700;
        color: var(--primary);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }
    .profile-name {
        color: var(--white);
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .profile-role {
        color: var(--accent);
        font-size: 14px;
        font-weight: 500;
        text-transform: capitalize;
        letter-spacing: 1px;
    }
    .profile-body {
        padding: 30px;
    }
</style>
@endpush

@section('content')
<div class="row g-4 animate-slide-up" style="animation-delay: 0.1s;">
    <!-- Profile Info -->
    <div class="col-lg-7">
        <div class="profile-card h-100">
            <div class="profile-header">
                <div class="profile-avatar-large">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h3 class="profile-name">{{ $user->name }}</h3>
                <div class="profile-role"><i class="bi bi-shield-check me-1"></i> {{ $user->role }}</div>
            </div>
            <div class="profile-body">
                <h5 class="mb-4" style="font-weight: 600; color: var(--text-dark);">
                    Informasi Akun
                </h5>
                <form method="POST" action="{{ $user->role == 'admin' ? route('admin.profile.update') : route('petugas.profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label text-muted">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}" required style="font-weight: 500;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control border-start-0" name="email" value="{{ $user->email }}" required style="font-weight: 500;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Hak Akses</label>
                            <input type="text" class="form-control bg-light" value="{{ ucfirst($user->role) }}" disabled style="font-weight: 500;">
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-top text-end">
                        <button type="submit" class="btn btn-primary-custom px-4">
                            <i class="bi bi-check2-circle me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="col-lg-5">
        <div class="profile-card h-100">
            <div class="profile-body h-100 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 20px;">
                        <i class="bi bi-lock-fill"></i>
                    </div>
                    <div>
                        <h5 class="mb-1" style="font-weight: 600; color: var(--text-dark);">Ubah Password</h5>
                        <p class="text-muted mb-0" style="font-size: 13px;">Pastikan akun Anda tetap aman</p>
                    </div>
                </div>
                
                <form method="POST" action="{{ $user->role == 'admin' ? route('admin.profile.password') : route('petugas.profile.password') }}" class="flex-grow-1 d-flex flex-column justify-content-between">
                    @csrf
                    @method('PUT')
                    <div>
                        <div class="mb-4">
                            <label class="form-label text-muted">Password Lama <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="current_password" required placeholder="Masukkan password saat ini">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required minlength="8" placeholder="Minimal 8 karakter">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation" required placeholder="Ulangi password baru">
                        </div>
                    </div>
                    
                    <div class="mt-2 text-end">
                        <button type="submit" class="btn btn-outline-primary" style="border-radius: 10px; font-weight: 500; padding: 10px 24px; width: 100%;">
                            <i class="bi bi-shield-lock me-2"></i>Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
