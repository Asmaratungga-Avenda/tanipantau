@extends('layouts.admin')
@section('title', 'Data Lahan')
@section('page-title', 'Data Lahan')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .search-card {
        background: var(--white);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-light);
    }

    .table-container {
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-light);
        overflow: hidden;
    }
    
    .badge-fase {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    /* Style untuk dropdown yang lebih rapi */
    .form-select, .form-control {
        border-radius: 10px;
        padding: 10px 15px;
        border: 1px solid var(--border-light);
        transition: var(--transition);
    }

    .form-select:focus, .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 111, 90, 0.1);
    }
    
    /* Map Legend Styles */
    .map-legend {
        background: white;
        padding: 12px 16px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid var(--border-light);
    }
    
    .map-legend h6 {
        margin: 0 0 10px 0;
        font-weight: 600;
        color: var(--text-dark);
        font-size: 13px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        font-size: 13px;
        color: var(--text-dark);
    }
    
    .legend-item:last-child {
        margin-bottom: 0;
    }
    
    .legend-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 8px rgba(0,0,0,0.2);
    }
    
    .legend-icon i {
        color: white;
        font-size: 16px;
    }
    
    /* Popup Styling */
    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        border: 1px solid var(--border-light);
    }
    
    .leaflet-popup-content {
        margin: 16px;
    }
    
    .popup-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-light);
    }
    
    .popup-header h6 {
        margin: 0;
        font-weight: 600;
        color: var(--primary);
        font-size: 14px;
    }
    
    .popup-content {
        font-size: 13px;
        line-height: 1.8;
    }
    
    .popup-content p {
        margin: 6px 0;
        color: var(--text-dark);
    }
    
    .popup-content strong {
        color: var(--text-muted);
        font-weight: 500;
    }
    
    .popup-footer {
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px solid var(--border-light);
    }
    
    .popup-footer .btn {
        font-size: 12px;
        padding: 6px 14px;
        border-radius: 8px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .search-card {
            padding: 16px;
        }
        
        .badge-fase {
            padding: 5px 10px;
            font-size: 10px;
        }
        
        .map-legend {
            padding: 10px 12px;
        }
        
        .legend-item {
            font-size: 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 animate-slide-up" style="animation-delay: 0.1s;">
    <div>
        <p class="mb-0 text-muted" style="font-size: 14px;">Kelola informasi area lahan pertanian dan fase penanaman</p>
    </div>
    @if(auth()->user()->role === 'admin')
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalLahan" onclick="resetForm()">
        <i class="bi bi-map-fill me-2"></i>Tambah Lahan
    </button>
    @endif
</div>

<!-- Search -->
<div class="search-card animate-slide-up" style="animation-delay: 0.2s;">
    <form method="GET" action="{{ route('admin.lahan.index') }}" class="d-flex flex-wrap align-items-center gap-3">
        <div class="input-group flex-grow-1" style="max-width: 500px;">
            <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-start-0 bg-light" name="search"
                   value="{{ request('search') }}" placeholder="Cari nama lahan, komoditas, petani..."
                   style="border-radius: 0 12px 12px 0;">
        </div>
        <button type="submit" class="btn btn-primary-custom" style="padding: 10px 24px;">Cari</button>
        @if(request('search'))
            <a href="{{ route('admin.lahan.index') }}" class="btn btn-outline-secondary" style="border-radius: 10px; padding: 10px 20px;">Reset</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="table-container animate-slide-up" style="animation-delay: 0.3s;">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">No</th>
                    <th>Nama Lahan</th>
                    <th>Petani</th>
                    <th>Komoditas</th>
                    <th>Luas</th>
                    <th>Petugas</th>
                    <th>Fase</th>
                    <th>Tgl Tanam</th>
                    <th>Status</th>
                    @if(auth()->user()->role === 'admin')
                    <th class="text-center" style="width: 120px;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($lahan as $index => $l)
                <tr>
                    <td class="ps-4 text-muted" style="width: 60px;">{{ $lahan->firstItem() + $index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-pin-map-fill text-primary"></i>
                            <span style="font-weight: 600; color: var(--text-dark);">{{ $l->nama_lahan }}</span>
                        </div>
                    </td>
                    <td style="font-size: 14px;">{{ $l->petani->nama ?? '-' }}</td>
                    <td style="font-size: 14px;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-basket3 text-muted" style="font-size: 16px;"></i>
                            {{ $l->komoditas }}
                        </div>
                    </td>
                    <td style="font-weight: 500; font-size: 14px;">{{ number_format($l->luas_lahan, 2) }} Ha</td>
                    <td>
                        @if($l->petugas)
                            <span class="badge" style="background: #E8F5E9; color: #2E7D32; padding: 5px 10px; border-radius: 6px; font-size: 12px;">
                                <i class="bi bi-person-check me-1"></i>{{ $l->petugas->name }}
                            </span>
                        @else
                            <span class="text-muted small"><i class="bi bi-dash-circle me-1"></i>Belum ditugaskan</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $faseKey = strtolower(str_replace(' ', '_', $l->fase_lahan));
                            $phase = $growthPhases[$faseKey] ?? ['label' => $l->fase_lahan, 'class' => 'bg-secondary text-white'];
                        @endphp
                        <span class="badge-fase {{ $phase['class'] }}">{{ $phase['label'] }}</span>
                    </td>
                    <td style="font-size: 13px;" class="text-muted">
                        {{ $l->tanggal_tanam ? $l->tanggal_tanam->format('d M Y') : '-' }}
                    </td>
                    <td>
                        @if($l->status_aktif)
                            <span class="badge-fase" style="background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;">Aktif</span>
                        @else
                            <span class="badge-fase" style="background: #F3F4F6; color: #4B5563; border: 1px solid #D1D5DB;">Tidak Aktif</span>
                        @endif
                    </td>
                    @if(auth()->user()->role === 'admin')
                    <td class="text-center">
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-sm btn-outline-primary" style="border-radius: 8px; padding: 6px 12px;"
                                    onclick="editLahan({{ $l }})" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" style="border-radius: 8px; padding: 6px 12px;"
                                    onclick="confirmDelete({{ $l->id }}, '{{ $l->nama_lahan }}')" title="Hapus">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->role === 'admin' ? '10' : '9' }}" class="text-center text-muted py-5">
                        <i class="bi bi-map" style="font-size: 48px; display: block; margin-bottom: 12px; color: var(--border-light);"></i>
                        <h6 class="mb-1">Tidak ada data lahan</h6>
                        <p class="small mb-0">Data lahan pertanian akan muncul di sini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($lahan->hasPages())
    <div class="bg-white p-3 border-top d-flex justify-content-end">
        {{ $lahan->links('vendor.pagination.custom') }}
    </div>
    @endif
</div>

@push('modals')
<!-- Modal Form -->
<div class="modal fade" id="modalLahan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="bi bi-map-fill me-2"></i>Tambah Lahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formLahan" method="POST">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">
                <input type="hidden" name="provinsi_text" id="provinsi_text" value="Jawa Timur">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Petani Pemilik <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="petani_id" id="petani_id" required data-placeholder="Pilih Petani">
                                <option value="">-- Pilih Petani --</option>
                                @foreach($petaniList as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->nik }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Petugas Penanggung Jawab</label>
                            <select class="form-select select2" name="petugas_id" id="petugas_id" data-placeholder="Pilih Petugas">
                                <option value="">-- Tidak Ditugaskan --</option>
                                @foreach($petugasList as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Pilih petugas yang bertanggung jawab atas lahan ini</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                            <select name="provinsi" id="provinsi" class="form-select select2" required data-placeholder="Pilih Provinsi">
                                <option value="Jawa Timur" selected>Jawa Timur</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kabupaten <span class="text-danger">*</span></label>
                            <select name="kabupaten" id="kabupaten" class="form-select select2" required data-placeholder="Pilih Kabupaten">
                                <option value="">Pilih Kabupaten</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                            <select name="kecamatan" id="kecamatan" class="form-select select2" required data-placeholder="Pilih Kecamatan">
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Desa</label>
                            <select name="desa" id="desa" class="form-select select2" data-placeholder="Pilih Desa">
                                <option value="">Pilih Desa</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Lahan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_lahan" id="nama_lahan" required placeholder="Contoh: Sawah Blok A">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Komoditas <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="komoditas" id="komoditas" required data-placeholder="Pilih Komoditas">
                                <option value="">-- Pilih Komoditas --</option>
                                <option value="Padi">Padi</option>
                                <option value="Jagung">Jagung</option>
                                <option value="Hortikultura">Hortikultura</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Luas Lahan (Ha) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control border-end-0" name="luas_lahan" id="luas_lahan" step="0.01" min="0.01" required placeholder="0.50">
                                <span class="input-group-text bg-white border-start-0 text-muted">Ha</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fase Lahan <span class="text-danger">*</span></label>
                            <select class="form-select select2" name="fase_lahan" id="fase_lahan" required data-placeholder="Pilih Fase">
                                <option value="">-- Pilih Fase --</option>
                                <option value="Persiapan">Persiapan</option>
                                <option value="Penanaman">Penanaman</option>
                                <option value="Pertumbuhan">Pertumbuhan</option>
                                <option value="Panen">Panen</option>
                                <option value="Panen Selesai">Panen Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Tanam</label>
                            <input type="date" class="form-control" name="tanggal_tanam" id="tanggal_tanam"
                                   value="{{ date('Y-m-d') }}">
                            <small class="text-muted d-block mt-1">Default: hari ini</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Status Aktif</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="status_aktif" id="status_aktif" value="1" checked style="width: 40px; height: 20px;">
                                <label class="form-check-label ms-2 mt-1" for="status_aktif">Lahan masih aktif digunakan</label>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1" style="font-weight: 600;">Koordinat Lokasi</h6>
                                    <p class="mb-0 text-muted" style="font-size: 13px;">Tentukan titik lokasi lahan di peta</p>
                                </div>
                                <button type="button" class="btn btn-outline-primary" onclick="showMap()" style="border-radius: 8px;">
                                    <i class="bi bi-geo-alt-fill me-1"></i>Buka Peta
                                </button>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Garis Lintang (Latitude)</label>
                                    <input type="number" class="form-control bg-light" name="garis_lintang" id="garis_lintang"
                                           step="0.000001" placeholder="-7.9xxxxx" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Garis Bujur (Longitude)</label>
                                    <input type="number" class="form-control bg-light" name="garis_bujur" id="garis_bujur"
                                           step="0.000001" placeholder="112.xxxxxx" readonly>
                                </div>
                            </div>
                            
                            <div id="mapContainer" class="mt-2" style="display: none; height: 300px; border-radius: 12px; overflow: hidden; border: 2px solid var(--border-light);"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px; padding: 10px 24px;">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-check2-circle me-2"></i><span id="btnSubmitText">Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 48px;"></i>
                </div>
                <h5 class="mb-2" style="font-weight: 700;">Konfirmasi Hapus</h5>
                <p class="text-muted mb-4" style="font-size: 14px;">Apakah Anda yakin ingin menghapus lahan <strong id="deleteName" class="text-dark"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                <form id="formDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary flex-grow-1" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                        <button type="submit" class="btn btn-danger flex-grow-1" style="border-radius: 10px; font-weight: 500;">
                            Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endpush

@endsection
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let cachedKabupatens = null;
    let cachedKecamatans = {};
    const allLahan = @json($allLahan);

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

    // Helper to create custom location marker
        function createCustomMarker(komoditas) {
            let colorBg, colorBorder;
            if (komoditas === 'Padi') {
                colorBg = '#F59E0B'; // Kuning
                colorBorder = '#D97706';
            } else if (komoditas === 'Jagung') {
                colorBg = '#10B981'; // Hijau
                colorBorder = '#059669';
            } else if (komoditas === 'Hortikultura') {
                colorBg = '#3B82F6'; // Biru
                colorBorder = '#2563EB';
            } else {
                colorBg = '#6B7280';
                colorBorder = '#4B5563';
            }
            
            return L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color:${colorBg}; width:32px; height:32px; border-radius:50%; border:4px solid ${colorBorder}; box-shadow: 0 0 12px rgba(0,0,0,0.35); display:flex; align-items:center; justify-content:center;"><i class="bi bi-geo-alt-fill" style="color:white; font-size:18px;"></i></div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                popupAnchor: [0, -18]
            });
        }
        
        // Helper to create new marker (for available location)
        function createNewMarker() {
            return L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color:#10B981; width:32px; height:32px; border-radius:50%; border:4px solid #059669; box-shadow: 0 0 12px rgba(0,0,0,0.35); display:flex; align-items:center; justify-content:center;"><i class="bi bi-geo-alt-fill" style="color:white; font-size:18px;"></i></div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });
        }

    $(document).ready(function() {
        let map = null;
        let marker = null;
        let existingMarkers = [];

        // Initialize Select2
        $('.select2').select2({
            placeholder: function() { return $(this).data('placeholder'); },
            allowClear: true,
            dropdownParent: $('#modalLahan')
        });

        // Load kabupatens on page load
        loadKabupatens();

        // ===== AJAX DEPENDENT DROPDOWN (Kabupaten → Kecamatan → Desa) =====
        $('#kabupaten').on('change', function(){
            loadKecamatans($(this).val());
            $('#desa').html('<option value="">Pilih Desa</option>').trigger('change');
        });

        $('#kecamatan').on('change', function(){
            loadDesas($(this).val());
        });

        window.showMap = function() {
            const container = document.getElementById('mapContainer');
            container.style.display = 'block';

            if (!map) {
                map = L.map('mapContainer').setView([-7.9667, 112.6334], 10);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                // Add Legend
                const legend = L.control({ position: 'bottomright' });
                legend.onAdd = function(map) {
                    const div = L.DomUtil.create('div', 'map-legend');
                    div.innerHTML = `
                        <h6><i class="bi bi-palette2 me-1"></i>Legenda Komoditas</h6>
                        <div class="legend-item">
                            <div class="legend-icon" style="background-color: #F59E0B; border: 3px solid #D97706;">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <span>Padi</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-icon" style="background-color: #10B981; border: 3px solid #059669;">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <span>Jagung</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-icon" style="background-color: #3B82F6; border: 3px solid #2563EB;">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <span>Hortikultura</span>
                        </div>
                    `;
                    return div;
                };
                legend.addTo(map);

                // Add all existing lahan markers
                allLahan.forEach(l => {
                    if (l.garis_lintang && l.garis_bujur) {
                        const existingMarker = L.marker([l.garis_lintang, l.garis_bujur], {
                            icon: createCustomMarker(l.komoditas)
                        }).addTo(map);

                        const popupContent = `
                            <div class="popup-wrapper">
                                <div class="popup-header">
                                    <i class="bi bi-geo-alt-fill text-primary"></i>
                                    <h6>${l.nama_lahan}</h6>
                                </div>
                                <div class="popup-content">
                                    <p><strong>Petani:</strong> ${l.petani?.nama || '-'}</p>
                                    <p><strong>Komoditas:</strong> ${l.komoditas}</p>
                                    <p><strong>Luas:</strong> ${l.luas_lahan} Ha</p>
                                    <p><strong>Fase:</strong> ${l.fase_lahan}</p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge" style="background: ${l.status_aktif ? '#ECFDF5' : '#F3F4F6'}; color: ${l.status_aktif ? '#065F46' : '#4B5563'}; padding: 2px 8px; border-radius: 4px; font-size: 11px;">
                                            ${l.status_aktif ? 'Aktif' : 'Tidak Aktif'}
                                        </span>
                                    </p>
                                </div>
                                <div class="popup-footer">
                                    <a href="/admin/lahan" class="btn btn-primary-custom btn-sm">
                                        <i class="bi bi-eye me-1"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        `;

                        existingMarker.bindPopup(popupContent);

                        // Hover effects
                        existingMarker.on('mouseover', function(e) {
                            this.setZIndexOffset(1000);
                        });
                        existingMarker.on('mouseout', function(e) {
                            this.setZIndexOffset(0);
                        });

                        existingMarkers.push(existingMarker);
                    }
                });

                map.on('click', function(e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;

                    // Check if coordinate is already taken
                    const existingLahan = allLahan.find(l => 
                        l.garis_lintang && l.garis_bujur && 
                        Math.abs(l.garis_lintang - lat) < 0.0001 && 
                        Math.abs(l.garis_bujur - lng) < 0.0001
                    );

                    if (existingLahan) {
                        // Show popup for existing lahan
                        const existingMarker = existingMarkers.find(m => 
                            Math.abs(m.getLatLng().lat - lat) < 0.0001 && 
                            Math.abs(m.getLatLng().lng - lng) < 0.0001
                        );
                        if (existingMarker) {
                            existingMarker.openPopup();
                        }
                        return;
                    }

                    // Set new marker for available location
                    setMarker(lat, lng);
                });
            }

            const lat = parseFloat(document.getElementById('garis_lintang').value) || -7.9667;
            const lng = parseFloat(document.getElementById('garis_bujur').value) || 112.6334;
            
            const editingExisting = allLahan.find(l => 
                l.garis_lintang === lat && l.garis_bujur === lng
            );
            
            if (!editingExisting) {
                setMarker(lat, lng);
            }
            map.setView([lat, lng], 13);

            setTimeout(() => map.invalidateSize(), 100);
        };

        function setMarker(lat, lng) {
            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng], { 
                draggable: true,
                icon: createNewMarker()
            }).addTo(map);
            document.getElementById('garis_lintang').value = lat.toFixed(6);
            document.getElementById('garis_bujur').value = lng.toFixed(6);

            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                
                // Check if coordinate is already taken
                const existingLahan = allLahan.find(l => 
                    l.garis_lintang && l.garis_bujur && 
                    Math.abs(l.garis_lintang - pos.lat) < 0.0001 && 
                    Math.abs(l.garis_bujur - pos.lng) < 0.0001
                );

                if (existingLahan) {
                    alert('Koordinat ini sudah digunakan oleh lahan lain!');
                    return;
                }

                document.getElementById('garis_lintang').value = pos.lat.toFixed(6);
                document.getElementById('garis_bujur').value = pos.lng.toFixed(6);
            });
        }

        window.resetForm = function() {
            $('#formLahan')[0].reset();
            $('#methodField').val('POST');
            $('#formLahan').attr('action', "{{ route('admin.lahan.store') }}");
            $('#modalTitle').html('<i class="bi bi-map-fill me-2"></i>Tambah Lahan');
            $('#btnSubmitText').text('Simpan Data');
            $('#tanggal_tanam').val("{{ date('Y-m-d') }}");
            $('#status_aktif').prop('checked', true);
            $('#mapContainer').hide();
            $('#garis_lintang').val('');
            $('#garis_bujur').val('');
            
            // Reinitialize selects
            $('#petani_id, #petugas_id, #provinsi, #kabupaten, #kecamatan, #desa, #fase_lahan, #komoditas').val(null).trigger('change');
        };

        window.editLahan = function(l) {
            $('#modalTitle').html('<i class="bi bi-pencil-square me-2"></i>Edit Data Lahan');
            $('#btnSubmitText').text('Simpan Perubahan');
            $('#methodField').val('PUT');
            $('#formLahan').attr('action', `/admin/lahan/${l.id}`);
            $('#petani_id').val(l.petani_id || '').trigger('change');
            $('#petugas_id').val(l.petugas_id || '').trigger('change');
            $('#provinsi').val(l.provinsi || 'Jawa Timur').trigger('change');
            $('#nama_lahan').val(l.nama_lahan || '');
            $('#komoditas').val(l.komoditas || '').trigger('change');
            $('#luas_lahan').val(l.luas_lahan || '');
            $('#fase_lahan').val(l.fase_lahan || '').trigger('change');
            $('#garis_lintang').val(l.garis_lintang || '');
            $('#garis_bujur').val(l.garis_bujur || '');
            $('#status_aktif').prop('checked', l.status_aktif);
            $('#mapContainer').hide();

            const tgl = l.tanggal_tanam ? new Date(l.tanggal_tanam).toISOString().split('T')[0] : "{{ date('Y-m-d') }}";
            $('#tanggal_tanam').val(tgl);

            window._selectedDesa = l.desa || '';
            loadKabupatens(l.kabupaten || '');
            loadKecamatans(l.kabupaten || '', l.kecamatan || '');

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalLahan'));
            modal.show();
        };

        window.confirmDelete = function(id, nama) {
            $('#deleteName').text(nama);
            $('#formDelete').attr('action', `/admin/lahan/${id}`);
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDelete'));
            modal.show();
        };
    });
</script>
@endpush

