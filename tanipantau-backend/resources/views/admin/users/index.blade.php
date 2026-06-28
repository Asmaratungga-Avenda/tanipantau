@extends('layouts.admin')

@section('title', 'Kelola User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0;">
        <h5 class="m-0" style="font-weight: 600;">Daftar User</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg"></i> Tambah User
        </button>
    </div>
    
    <div class="card-body p-0">
        <!-- Search -->
        <div class="p-3 border-bottom bg-light">
            <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau role..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary"><i class="bi bi-search"></i> Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td class="ps-4 fw-medium">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @if($u->role == 'admin')
                                <span class="badge bg-danger">Admin</span>
                            @elseif($u->role == 'manajer')
                                <span class="badge bg-primary">Manajer</span>
                            @else
                                <span class="badge bg-success">Petugas</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if(auth()->id() !== $u->id)
                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    
                    @push('modals')
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.users.update', $u->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nama</label>
                                            <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Role</label>
                                            <select name="role" class="form-select" required>
                                                <option value="admin" {{ $u->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="manajer" {{ $u->role == 'manajer' ? 'selected' : '' }}>Manajer</option>
                                                <option value="petugas" {{ $u->role == 'petugas' ? 'selected' : '' }}>Petugas</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                                            <input type="password" name="password" class="form-control" minlength="8">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endpush
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data user</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white border-top p-3" style="border-radius: 0 0 16px 16px;">
        {{ $users->links('vendor.pagination.custom') }}
    </div>
    @endif
</div>

@push('modals')
<!-- Add Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="petugas">Petugas</option>
                            <option value="manajer">Manajer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
@endsection
