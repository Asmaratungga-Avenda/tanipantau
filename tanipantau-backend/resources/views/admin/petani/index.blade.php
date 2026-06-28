@extends('layouts.admin')
@section('title', 'Data Petani')
@section('page-title', 'Data Petani')

@push('styles')
<style>
    .avatar-profile {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 36px;
        font-weight: 700;
        border: 4px solid #fff;
        box-shadow: 0 4px 16px rgba(0, 111, 90, 0.2);
        flex-shrink: 0;
    }
    .avatar-table {
        width: 36px;
        height: 36px;
        font-size: 14px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 600;
        flex-shrink: 0;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .info-card {
        transition: var(--transition);
    }
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .action-group {
        position: relative;
        z-index: 2;
    }
    .icon-delete-wrapper {
        width: 64px;
        height: 64px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(239, 68, 68, 0.1);
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3 animate-slide-up" style="animation-delay: 0.1s;">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="bi bi-people-fill me-2 text-primary"></i>Data Petani
        </h4>
        <p class="text-muted mb-0 small">Kelola data petani terdaftar di sistem TaniPantau</p>
    </div>
    @if(auth()->user()->role === 'admin')
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalPetani" onclick="resetForm()">
        <i class="bi bi-person-plus-fill me-2"></i>Tambah Petani
    </button>
    @endif
</div>

<div class="row g-3 mb-4 animate-slide-up" style="animation-delay: 0.15s;">
    <div class="col-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-people-fill text-primary"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">{{ $petani->total() }}</h5>
                    <small class="text-muted">Total Petani</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon bg-info bg-opacity-10">
                    <i class="bi bi-map-fill text-info"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">{{ $petani->total() }}</h5>
                    <small class="text-muted">Total Data</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-check-circle-fill text-success"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">{{ $petani->total() }}</h5>
                    <small class="text-muted">Siap Digunakan</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4 animate-slide-up" style="animation-delay: 0.2s;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.petani.index') }}" class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-3">
            <div class="input-group flex-grow-1">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0 bg-light" name="search"
                       value="{{ request('search') }}" placeholder="Cari nama, NIK, desa, kecamatan...">
            </div>
            <button type="submit" class="btn btn-primary-custom">Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.petani.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm animate-slide-up" style="animation-delay: 0.3s;">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">No</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Desa</th>
                    <th>Kecamatan</th>
                    <th>Kabupaten</th>
                    <th>No. HP</th>
                    @if(auth()->user()->role === 'admin')
                    <th class="text-center">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($petani as $index => $p)
                <tr class="align-middle">
                    <td class="ps-4 text-muted fw-medium">{{ $petani->firstItem() + $index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-table">
                                {{ strtoupper(substr($p->nama, 0, 1)) }}
                            </div>
                            <div>
                                <a href="javascript:void(0)" class="text-decoration-none fw-semibold text-dark" onclick="showDetail({{ $p }})">
                                    {{ $p->nama }}
                                </a>
                                @if($p->status_aktif)
                                <span class="badge bg-success bg-opacity-10 text-success ms-1 small">Aktif</span>
                                @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1 small">Tidak Aktif</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark fw-normal px-3 py-2">{{ $p->nik }}</span></td>
                    <td>{{ $p->desa }}</td>
                    <td>{{ $p->kecamatan }}</td>
                    <td>{{ $p->kabupaten ?? '-' }}</td>
                    <td>
                        <span class="d-inline-flex align-items-center gap-1">
                            <i class="bi bi-telephone text-muted small"></i>
                            {{ $p->nomor_hp }}
                        </span>
                    </td>
                    @if(auth()->user()->role === 'admin')
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center action-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="showDetail({{ $p }})" title="Lihat Detail" data-bs-toggle="tooltip">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="editPetani({{ $p }})" title="Edit" data-bs-toggle="tooltip">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $p->id }}, '{{ $p->nama }}')" title="Hapus" data-bs-toggle="tooltip">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->role === 'admin' ? 8 : 7 }}" class="text-center text-muted py-5">
                        <i class="bi bi-people d-block mb-3 text-light fs-1"></i>
                        <h6 class="fw-semibold mb-1">Tidak ada data petani</h6>
                        <p class="small mb-3">Data petani yang didaftarkan akan muncul di sini.</p>
                        @if(auth()->user()->role === 'admin')
                        <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalPetani" onclick="resetForm()">
                            <i class="bi bi-person-plus-fill me-1"></i>Tambah Petani Pertama
                        </button>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($petani->hasPages())
    <div class="d-flex justify-content-end p-3 border-top">
        {{ $petani->links('vendor.pagination.custom') }}
    </div>
    @endif
</div>
@endsection

@push('modals')
<div class="modal fade" id="modalPetani" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="bi bi-person-plus-fill me-2"></i>Tambah Petani
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPetani" method="POST">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-person text-primary me-1"></i>Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="nama" id="nama" required placeholder="Masukkan nama petani">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-credit-card text-primary me-1"></i>NIK <span class="text-danger">*</span>
                            </label>
                            <input type="tel" class="form-control" name="nik" id="nik" required placeholder="16 digit NIK" maxlength="16" pattern="\d{16}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                <i class="bi bi-geo-alt text-primary me-1"></i>Alamat Lengkap <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" name="alamat" id="alamat" rows="2" required placeholder="Detail alamat rumah"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-globe2 text-primary me-1"></i>Provinsi <span class="text-danger">*</span>
                            </label>
                            <select name="provinsi" id="provinsi" class="form-select select2" required data-placeholder="Pilih Provinsi">
                                <option value="Jawa Timur" selected>Jawa Timur</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-building text-primary me-1"></i>Kabupaten <span class="text-danger">*</span>
                            </label>
                            <select name="kabupaten" id="kabupaten" class="form-select select2" required data-placeholder="Pilih Kabupaten">
                                <option value="">Pilih Kabupaten</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-map text-primary me-1"></i>Kecamatan <span class="text-danger">*</span>
                            </label>
                            <select name="kecamatan" id="kecamatan" class="form-select select2" required data-placeholder="Pilih Kecamatan">
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-house text-primary me-1"></i>Desa <span class="text-danger">*</span>
                            </label>
                            <select name="desa" id="desa" class="form-select select2" required data-placeholder="Pilih Desa">
                                <option value="">Pilih Desa</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-telephone text-primary me-1"></i>Nomor Handphone <span class="text-danger">*</span>
                            </label>
                            <input type="tel" class="form-control" name="nomor_hp" id="nomor_hp" required placeholder="08xxxxxxxxxx" maxlength="15" pattern="\d{10,15}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-check2-circle me-2"></i><span id="btnSubmitText">Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-2"></i>Profil Petani
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar-profile mx-auto mb-3" id="detailAvatar">
                        <span id="detailAvatarText">P</span>
                    </div>
                    <h4 class="fw-bold mb-1" id="detailNama">-</h4>
                    <p class="text-muted mb-2 small" id="detailNik">-</p>
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2" id="detailStatus">
                        <i class="bi bi-check-circle-fill me-1"></i>Aktif
                    </span>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-0 bg-light rounded-3 p-3 info-card h-100">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-person text-primary fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Nama Lengkap</small>
                                    <span class="fw-semibold" id="detailNama2">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light rounded-3 p-3 info-card h-100">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-credit-card text-primary fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">NIK</small>
                                    <span class="fw-semibold" id="detailNik2">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light rounded-3 p-3 info-card h-100">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-telephone text-primary fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Nomor HP</small>
                                    <span class="fw-semibold" id="detailHp">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light rounded-3 p-3 info-card h-100">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-check-circle text-primary fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Status</small>
                                    <span class="fw-semibold" id="detailStatus2">Aktif</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border-0 bg-light rounded-3 p-3 info-card">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-geo-alt text-primary fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Alamat</small>
                                    <span class="fw-semibold" id="detailAlamat">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border-0 bg-light rounded-3 p-3 info-card h-100">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-globe2 text-primary fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Provinsi</small>
                                    <span class="fw-semibold" id="detailProvinsi">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border-0 bg-light rounded-3 p-3 info-card h-100">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-building text-primary fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Kabupaten</small>
                                    <span class="fw-semibold" id="detailKabupaten">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border-0 bg-light rounded-3 p-3 info-card h-100">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-map text-primary fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Kecamatan</small>
                                    <span class="fw-semibold" id="detailKecamatan">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card border-0 bg-light rounded-3 p-3 info-card h-100">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-house text-primary fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Desa</small>
                                    <span class="fw-semibold" id="detailDesa">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                @if(auth()->user()->role === 'admin')
                <button type="button" class="btn btn-primary-custom" id="detailEditBtn">
                    <i class="bi bi-pencil-square me-1"></i>Edit Data
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <div class="icon-delete-wrapper">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-2"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-2">Konfirmasi Hapus</h5>
                <p class="text-muted mb-4 small">Apakah Anda yakin ingin menghapus data petani <strong id="deleteName" class="text-dark"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                <form id="formDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger w-50">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    let cachedKabupatens = null;
    let cachedKecamatans = {};

    function loadKabupatens(selected) {
        if (cachedKabupatens) {
            renderKabupatens(selected);
            return;
        }
        fetch('/api/kabupaten')
        .then(r => r.json())
        .then(data => {
            cachedKabupatens = data;
            renderKabupatens(selected);
        });
    }

    function renderKabupatens(selected) {
        let sel = $('#kabupaten');
        sel.html('<option value="">Pilih Kabupaten</option>');
        cachedKabupatens.forEach(item => {
            let s = (item.name === selected) ? 'selected' : '';
            sel.append(`<option value="${item.name}" data-id="${item.id}" ${s}>${item.name}</option>`);
        });
        sel.trigger('change');
    }

    function loadKecamatans(kabupatenName, selectedKec) {
        if (!kabupatenName) {
            $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').trigger('change');
            return;
        }
        if (!cachedKabupatens) {
            fetch('/api/kabupaten')
            .then(r => r.json())
            .then(data => {
                cachedKabupatens = data;
                loadKecamatans(kabupatenName, selectedKec);
            });
            return;
        }
        let kab = cachedKabupatens.find(k => k.name === kabupatenName);
        if (!kab) return;
        if (cachedKecamatans[kab.id]) {
            renderKecamatans(cachedKecamatans[kab.id], selectedKec);
            return;
        }
        fetch('/api/kecamatan/' + kab.id)
        .then(r => r.json())
        .then(data => {
            cachedKecamatans[kab.id] = data;
            renderKecamatans(data, selectedKec);
        });
    }

    function renderKecamatans(data, selected) {
        let sel = $('#kecamatan');
        sel.html('<option value="">Pilih Kecamatan</option>');
        data.forEach(item => {
            let s = (item.name === selected) ? 'selected' : '';
            sel.append(`<option value="${item.name}" data-id="${item.id}" ${s}>${item.name}</option>`);
        });
        sel.trigger('change');
    }

    function loadDesas(kecamatanName, selected) {
        if (!kecamatanName) {
            $('#desa').html('<option value="">Pilih Desa</option>').trigger('change');
            return;
        }
        if (!selected && window._selectedDesa) {
            selected = window._selectedDesa;
        }
        let kecId = null;
        if ($('#kecamatan').length) {
            let opt = $('#kecamatan').find('option:selected');
            if (opt.length && opt.val() === kecamatanName) {
                kecId = opt.attr('data-id');
            }
        }
        if (!kecId) {
            for (let kabId in cachedKecamatans) {
                let found = cachedKecamatans[kabId].find(k => k.name === kecamatanName);
                if (found) { kecId = found.id; break; }
            }
        }
        if (!kecId) return;
        fetch('/api/desa/' + kecId)
        .then(r => r.json())
        .then(data => {
            if (!data) return;
            let sel = $('#desa');
            sel.html('<option value="">Pilih Desa</option>');
            data.forEach(item => {
                let s = (item.name === selected) ? 'selected' : '';
                sel.append(`<option value="${item.name}" ${s}>${item.name}</option>`);
            });
            sel.trigger('change');
        });
    }

    window.showDetail = function(p) {
        const initial = p.nama ? p.nama.charAt(0).toUpperCase() : '?';
        document.getElementById('detailAvatarText').textContent = initial;
        document.getElementById('detailNama').textContent = p.nama || '-';
        document.getElementById('detailNik').textContent = 'NIK: ' + (p.nik || '-');
        document.getElementById('detailNama2').textContent = p.nama || '-';
        document.getElementById('detailNik2').textContent = p.nik || '-';
        document.getElementById('detailHp').textContent = p.nomor_hp || '-';
        document.getElementById('detailAlamat').textContent = p.alamat || '-';
        document.getElementById('detailKabupaten').textContent = p.kabupaten || '-';
        document.getElementById('detailKecamatan').textContent = p.kecamatan || '-';
        document.getElementById('detailDesa').textContent = p.desa || '-';
        document.getElementById('detailProvinsi').textContent = p.provinsi || 'Jawa Timur';

        const statusEl = document.getElementById('detailStatus');
        const statusEl2 = document.getElementById('detailStatus2');
        if (p.status_aktif) {
            statusEl.className = 'badge bg-success bg-opacity-10 text-success px-3 py-2';
            statusEl.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Aktif';
            statusEl2.textContent = 'Aktif';
        } else {
            statusEl.className = 'badge bg-danger bg-opacity-10 text-danger px-3 py-2';
            statusEl.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i>Tidak Aktif';
            statusEl2.textContent = 'Tidak Aktif';
        }

        const editBtn = document.getElementById('detailEditBtn');
        if (editBtn) {
            editBtn.onclick = null;
            editBtn.addEventListener('click', function handler() {
                const modalDetail = bootstrap.Modal.getInstance(document.getElementById('modalDetail'));
                if (modalDetail) modalDetail.hide();
                setTimeout(function() { editPetani(p); }, 300);
                editBtn.removeEventListener('click', handler);
            });
        }

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetail'));
        modal.show();
    };

    $(document).ready(function() {
        $('.select2').select2({
            placeholder: function(){
                return $(this).data('placeholder');
            },
            allowClear: true,
            dropdownParent: $('#modalPetani')
        });

        loadKabupatens();

        $('#kabupaten').on('change', function(){
            loadKecamatans($(this).val());
            $('#desa').html('<option value="">Pilih Desa</option>').trigger('change');
        });

        $('#kecamatan').on('change', function(){
            loadDesas($(this).val());
        });

        window.resetForm = function() {
            $('#formPetani')[0].reset();
            $('#methodField').val('POST');
            $('#formPetani').attr('action', "{{ route('admin.petani.store') }}");
            $('#modalTitle').html('<i class="bi bi-person-plus-fill me-2"></i>Tambah Petani');
            $('#btnSubmitText').text('Simpan Data');
            $('#kabupaten, #kecamatan, #desa').val(null).trigger('change');
        };

        window.editPetani = function(p) {
            $('#modalTitle').html('<i class="bi bi-pencil-square me-2"></i>Edit Data Petani');
            $('#btnSubmitText').text('Simpan Perubahan');
            $('#methodField').val('PUT');
            $('#formPetani').attr('action', `/admin/petani/${p.id}`);
            $('#nama').val(p.nama || '');
            $('#nik').val(p.nik || '');
            $('#alamat').val(p.alamat || '');
            $('#provinsi').val(p.provinsi || 'Jawa Timur');
            $('#nomor_hp').val(p.nomor_hp || '');

            window._selectedDesa = p.desa || '';
            loadKabupatens(p.kabupaten || '');
            loadKecamatans(p.kabupaten || '', p.kecamatan || '');

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPetani'));
            modal.show();
        };

        window.confirmDelete = function(id, nama) {
            document.getElementById('deleteName').textContent = nama;
            document.getElementById('formDelete').action = `/admin/petani/${id}`;
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDelete'));
            modal.show();
        };

        if (typeof bootstrap !== 'undefined') {
            const tooltipList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            [...tooltipList].forEach(el => new bootstrap.Tooltip(el));
        }
    });
</script>
@endpush
