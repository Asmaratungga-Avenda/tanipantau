@extends('layouts.admin')
@section('title', 'Kunjungan Lapangan')
@section('page-title', 'Kunjungan Lapangan')

@push('styles')
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
    
    .badge-status {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 animate-slide-up" style="animation-delay: 0.1s;">
    <div>
        <p class="mb-0 text-muted" style="font-size: 14px;">
            @if(Auth::user()->role === 'petugas')
                Catat dan kelola riwayat kunjungan ke lahan pertanian
            @else
                Lihat riwayat kunjungan ke lahan pertanian dari semua petugas
            @endif
        </p>
    </div>
    @if(Auth::user()->role === 'petugas')
        <button class="btn btn-primary-custom" onclick="resetForm(); bootstrap.Modal.getOrCreateInstance(document.getElementById('modalKunjungan')).show();">
            <i class="bi bi-plus-circle me-2"></i>Tambah Kunjungan
        </button>
    @endif
</div>

<!-- Search -->
<div class="search-card animate-slide-up" style="animation-delay: 0.2s;">
    <form method="GET" action="{{ route('admin.kunjungan.index') }}" class="d-flex flex-wrap align-items-center gap-3">
        <div class="input-group flex-grow-1" style="max-width: 500px;">
            <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-start-0 bg-light" name="search"
                   value="{{ request('search') }}" placeholder="Cari nama lahan, catatan..."
                   style="border-radius: 0 12px 12px 0;">
        </div>
        <button type="submit" class="btn btn-primary-custom" style="padding: 10px 24px;">Cari</button>
        @if(request('search'))
            <a href="{{ route('admin.kunjungan.index') }}" class="btn btn-outline-secondary" style="border-radius: 10px; padding: 10px 20px;">Reset</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="table-container animate-slide-up" style="animation-delay: 0.3s;">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Tanggal</th>
                    <th>Lahan & Petani</th>
                    <th>Kondisi</th>
                    <th>Tindak Lanjut</th>
                    <th>Petugas</th>
                    <th>Catatan</th>
                    <th class="text-center">Foto</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kunjungan as $index => $k)
                <tr>
                    <td class="ps-4" style="font-weight: 500; font-size: 13px;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-check text-primary"></i>
                            {{ \Carbon\Carbon::parse($k->tanggal_kunjungan)->format('d M Y') }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-dark);">{{ $k->lahan->nama_lahan ?? '-' }}</div>
                        <div class="text-muted" style="font-size: 12px;">{{ $k->lahan->petani->nama ?? '-' }}</div>
                    </td>
                    <td>
                        @php $kondisi = $kondisiMap[$k->kondisi_lahan] ?? ['label' => $k->kondisi_lahan, 'class' => 'bg-secondary']; @endphp
                        <span class="badge-status {{ $kondisi['class'] }}">{{ $kondisi['label'] }}</span>
                    </td>
                    <td>
                        @php $status = $statusMap[$k->status_tindak_lanjut] ?? ['label' => $k->status_tindak_lanjut, 'class' => 'bg-secondary']; @endphp
                        <span class="badge-status {{ $status['class'] }}">{{ $status['label'] }}</span>
                    </td>
                    <td style="font-size: 13px;">{{ $k->petugas->name ?? '-' }}</td>
                    <td style="font-size: 13px; max-width: 200px;">
                        @if($k->catatan)
                            <span title="{{ $k->catatan }}">{{ Str::limit($k->catatan, 30) }}</span>
                        @else
                            <span class="text-muted fst-italic">Tidak ada</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($k->foto_url)
                            <a href="{{ $k->foto_url }}" target="_blank">
                                <img src="{{ $k->foto_url }}" alt="Foto" style="max-width: 80px; max-height: 80px; border-radius: 8px; cursor: pointer;" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjOThhMmFiIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiB4PSI1IiB5PSI1IiByeD0iMCIvPjxwYXRoIGQ9Ik01IDI3bDE0LjUtMTQuNUwxMiAyMWwxOSAyOSIvPjwvc3ZnPg=='">
                            </a>
                        @else
                            <div style="width: 80px; height: 80px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 1px solid #E5E7EB;">
                                <i class="bi bi-image text-muted" style="font-size: 28px; opacity: 0.4;"></i>
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(Auth::user()->role === 'petugas' && $k->petugas_id === Auth::id())
                            <button class="btn btn-sm btn-outline-primary" style="border-radius: 8px;"
                                    onclick="editKunjungan({{ $k }}, true)" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                        @else
                            <button class="btn btn-sm btn-outline-primary" style="border-radius: 8px;"
                                    onclick="showDetail({{ $k }})" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-clipboard2-x" style="font-size: 48px; display: block; margin-bottom: 12px; color: var(--border-light);"></i>
                        <h6 class="mb-1">Belum ada kunjungan</h6>
                        <p class="small mb-3">
                            @if(Auth::user()->role === 'petugas')
                                Silakan tambahkan kunjungan pertama Anda.
                            @else
                                Riwayat kunjungan belum tersedia.
                            @endif
                        </p>
                        @if(Auth::user()->role === 'petugas')
                            <button class="btn btn-primary-custom" onclick="resetForm(); bootstrap.Modal.getOrCreateInstance(document.getElementById('modalKunjungan')).show();">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Kunjungan
                            </button>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($kunjungan->hasPages())
    <div class="bg-white p-3 border-top d-flex justify-content-end">
        {{ $kunjungan->links('vendor.pagination.custom') }}
    </div>
    @endif
</div>

@push('modals')
@if(Auth::user()->role === 'petugas')
<!-- Modal Form -->
<div class="modal fade" id="modalKunjungan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="bi bi-clipboard2-plus-fill me-2"></i>Catat Kunjungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formKunjungan" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Lahan Tujuan <span class="text-danger">*</span></label>
                            <select class="form-select" name="lahan_id" id="lahan_id" required>
                                <option value="">-- Pilih Lahan --</option>
                                @foreach($lahanList as $l)
                                    <option value="{{ $l->id }}">{{ $l->nama_lahan }} — {{ $l->petani->nama ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Kunjungan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_kunjungan" id="tanggal_kunjungan"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kondisi Lahan <span class="text-danger">*</span></label>
                            <select class="form-select" name="kondisi_lahan" id="kondisi_lahan" required>
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="Sangat Baik">Sangat Baik</option>
                                <option value="Baik">Baik</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Kurang Baik">Kurang Baik</option>
                                <option value="Sangat Kurang Baik">Sangat Kurang Baik</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tindak Lanjut <span class="text-danger">*</span></label>
                            <select class="form-select" name="status_tindak_lanjut" id="status_tindak_lanjut" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Aman">Aman</option>
                                <option value="Perlu Pemantauan">Perlu Pemantauan</option>
                                <option value="Perlu Tindakan">Perlu Tindakan</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan Hasil Kunjungan</label>
                            <textarea class="form-control" name="catatan" id="catatan" rows="3"
                                      placeholder="Tuliskan temuan atau instruksi selama kunjungan..." maxlength="1000"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Foto Dokumentasi</label>
                            <div class="upload-zone" id="uploadZone">
                                <i class="bi bi-cloud-arrow-up-fill upload-icon"></i>
                                <div class="upload-text">Tarik foto ke sini atau klik untuk memilih file</div>
                                <div class="upload-subtext">Maksimal 5MB (JPG, PNG, GIF, WEBP)</div>
                                <input type="file" class="file-input" name="foto" id="foto" accept="image/jpeg,image/png,image/gif,image/webp">
                            </div>
                            <div class="text-center">
                                <img src="" id="imagePreview" class="image-preview" alt="Preview Foto" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjOThhMmFiIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiB4PSI1IiB5PSI1IiByeD0iMCIvPjxwYXRoIGQ9Ik01IDI3bDE0LjUtMTQuNUwxMiAyMWwxOSAyOSIvPjwvc3ZnPg=='">
                            </div>
                            <div class="text-center mt-2" id="gantiFotoContainer" style="display: none;">
                                <button type="button" class="btn btn-sm btn-outline-primary me-2" id="btnGantiFoto" style="border-radius: 8px;">
                                    <i class="bi bi-image"></i> Ganti Foto
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btnHapusFoto" style="border-radius: 8px;">
                                    <i class="bi bi-trash"></i> Hapus Foto
                                </button>
                            </div>
                            <input type="hidden" name="hapus_foto" id="hapusFoto" value="0">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info mb-0 d-flex align-items-center gap-3">
                                <i class="bi bi-info-circle-fill" style="font-size: 20px;"></i>
                                <div style="font-size: 13px;">Sebagai petugas, Anda hanya dapat mengedit data kunjungan yang Anda buat sendiri.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px; padding: 10px 24px;">Batal</button>
                    <div id="deleteBtnContainer" style="display: none;">
                        <button type="button" class="btn btn-danger" id="btnDeleteInModal" style="border-radius: 10px;">
                            <i class="bi bi-trash3-fill me-2"></i>Hapus
                        </button>
                    </div>
                    <button type="submit" class="btn btn-primary-custom" id="btnUpdateSubmit">
                        <i class="bi bi-check2-circle me-2"></i><span id="btnSubmitText">Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailTitle"><i class="bi bi-info-circle me-2"></i>Detail Kunjungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-semibold">Lahan Tujuan</label>
                        <div class="fw-medium" id="detailNamaLahan">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-semibold">Tanggal Kunjungan</label>
                        <div class="fw-medium" id="detailTanggal">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-semibold">Kondisi Lahan</label>
                        <div id="detailKondisi">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-semibold">Tindak Lanjut</label>
                        <div id="detailStatus">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-semibold">Petugas</label>
                        <div class="fw-medium" id="detailPetugas">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-semibold">Petani</label>
                        <div class="fw-medium" id="detailPetani">-</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted fw-semibold">Catatan Hasil Kunjungan</label>
                        <div id="detailCatatan" class="text-muted">Tidak ada catatan</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted fw-semibold">Foto Dokumentasi</label>
                        <div id="detailFotoContainer" class="text-center">
                            <div style="width: 200px; height: 200px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 1px solid #E5E7EB;">
                                <i class="bi bi-image text-muted" style="font-size: 48px; opacity: 0.4;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px; padding: 10px 24px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation -->
@if(Auth::user()->role === 'petugas')
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 48px;"></i>
                </div>
                <h5 class="mb-2" style="font-weight: 700;">Konfirmasi Hapus</h5>
                <p class="text-muted mb-4" style="font-size: 14px;">Apakah Anda yakin ingin menghapus kunjungan <strong id="deleteName" class="text-dark"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
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
@endif

@endpush

@endsection
@push('scripts')
<script>
    @if(Auth::user()->role === 'petugas')
    // File Upload Preview & Drag/Drop
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('foto');
    const imagePreview = document.getElementById('imagePreview');

    fileInput.addEventListener('change', function(e) {
        if(this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'inline-block';
                uploadZone.style.display = 'none';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        uploadZone.classList.add('dragover');
    }

    function unhighlight(e) {
        uploadZone.classList.remove('dragover');
    }

    uploadZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        let dt = e.dataTransfer;
        let files = dt.files;
        fileInput.files = files;
        
        // Trigger change event for preview
        const event = new Event('change');
        fileInput.dispatchEvent(event);
    }

    function resetForm() {
        document.getElementById('formKunjungan').reset();
        document.getElementById('methodField').value = 'POST';
        document.getElementById('formKunjungan').action = "{{ route('admin.kunjungan.store') }}";
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-clipboard2-plus-fill me-2"></i>Catat Kunjungan';
        document.getElementById('btnSubmitText').textContent = 'Simpan Data';
        document.getElementById('tanggal_kunjungan').value = "{{ date('Y-m-d') }}";
        document.getElementById('deleteBtnContainer').style.display = 'none';
        document.getElementById('btnUpdateSubmit').style.display = 'inline-block';
        
        // Reset preview
        imagePreview.style.display = 'none';
        imagePreview.src = '';
        uploadZone.style.display = 'block';
        document.getElementById('gantiFotoContainer').style.display = 'none';
        document.getElementById('hapusFoto').value = '0';
    }

    let currentKunjunganId = null;

    function editKunjungan(k, canDelete = false) {
        currentKunjunganId = k.id;
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Detail & Edit Kunjungan';
        document.getElementById('btnSubmitText').textContent = 'Update Data';
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('formKunjungan').action = "{{ route('admin.kunjungan.index') }}/" + k.id;
        document.getElementById('lahan_id').value = k.lahan_id || '';
        document.getElementById('kondisi_lahan').value = k.kondisi_lahan || '';
        document.getElementById('status_tindak_lanjut').value = k.status_tindak_lanjut || '';
        document.getElementById('catatan').value = k.catatan || '';

        const tgl = k.tanggal_kunjungan ? k.tanggal_kunjungan.split(' ')[0] : "{{ date('Y-m-d') }}";
        document.getElementById('tanggal_kunjungan').value = tgl;
        
        document.getElementById('hapusFoto').value = '0';
        
        // Handle image preview for edit
        if (k.foto_url) {
            imagePreview.src = k.foto_url;
            imagePreview.style.display = 'inline-block';
            uploadZone.style.display = 'none';
            document.getElementById('gantiFotoContainer').style.display = 'block';
        } else {
            imagePreview.style.display = 'none';
            imagePreview.src = '';
            uploadZone.style.display = 'block';
            document.getElementById('gantiFotoContainer').style.display = 'none';
        }

        // Show/hide delete button in modal
        if (canDelete) {
            document.getElementById('deleteBtnContainer').style.display = 'inline-block';
        } else {
            document.getElementById('deleteBtnContainer').style.display = 'none';
        }

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalKunjungan'));
        modal.show();
    }
    
    // Tambahkan event listener untuk tombol ganti foto
    document.getElementById('btnGantiFoto').addEventListener('click', function() {
        document.getElementById('foto').click();
    });
    
    // Tambahkan event listener untuk tombol hapus foto
    document.getElementById('btnHapusFoto').addEventListener('click', function() {
        // Set hidden input hapus_foto to 1
        document.getElementById('hapusFoto').value = '1';
        // Hide image preview and ganti/hapus buttons, show upload zone
        imagePreview.style.display = 'none';
        document.getElementById('gantiFotoContainer').style.display = 'none';
        uploadZone.style.display = 'block';
    });

    // Event listener for delete button in modal
    document.getElementById('btnDeleteInModal').addEventListener('click', function() {
        if (currentKunjunganId) {
            confirmDelete(currentKunjunganId, 'Kunjungan');
        }
    });

    function confirmDelete(id, nama) {
        document.getElementById('deleteName').textContent = nama;
        document.getElementById('formDelete').action = "{{ route('admin.kunjungan.index') }}/" + id;
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDelete'));
        modal.show();
    }
    @endif

    function showDetail(k) {
        document.getElementById('modalDetailTitle').innerHTML = '<i class="bi bi-info-circle me-2"></i>Detail Kunjungan';
        
        // Isi detail
        document.getElementById('detailNamaLahan').textContent = k.lahan ? k.lahan.nama_lahan : '-';
        document.getElementById('detailTanggal').textContent = k.tanggal_kunjungan ? new Date(k.tanggal_kunjungan).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
        document.getElementById('detailPetugas').textContent = k.petugas ? k.petugas.name : '-';
        document.getElementById('detailPetani').textContent = k.lahan && k.lahan.petani ? k.lahan.petani.nama : '-';
        document.getElementById('detailCatatan').textContent = k.catatan || 'Tidak ada catatan';
        
        // Kondisi badge
        const kondisiDiv = document.getElementById('detailKondisi');
        let kondisiBadge = '<span class="badge bg-secondary text-white px-3 py-1 rounded-pill">' + (k.kondisi_lahan || '-') + '</span>';
        if (k.kondisi_lahan === 'Sangat Baik') {
            kondisiBadge = '<span class="badge bg-success text-white px-3 py-1 rounded-pill">Sangat Baik</span>';
        } else if (k.kondisi_lahan === 'Baik') {
            kondisiBadge = '<span class="badge bg-info text-white px-3 py-1 rounded-pill">Baik</span>';
        } else if (k.kondisi_lahan === 'Sedang') {
            kondisiBadge = '<span class="badge bg-warning text-dark px-3 py-1 rounded-pill">Sedang</span>';
        } else if (k.kondisi_lahan === 'Sangat Kurang Baik') {
            kondisiBadge = '<span class="badge bg-danger text-white px-3 py-1 rounded-pill">Sangat Kurang Baik</span>';
        }
        kondisiDiv.innerHTML = kondisiBadge;
        
        // Status badge
        const statusDiv = document.getElementById('detailStatus');
        let statusBadge = '<span class="badge bg-secondary text-white px-3 py-1 rounded-pill">' + (k.status_tindak_lanjut || '-') + '</span>';
        if (k.status_tindak_lanjut === 'Aman') {
            statusBadge = '<span class="badge bg-success text-white px-3 py-1 rounded-pill">Aman</span>';
        } else if (k.status_tindak_lanjut === 'Perlu Pemantauan') {
            statusBadge = '<span class="badge bg-warning text-dark px-3 py-1 rounded-pill">Perlu Pemantauan</span>';
        } else if (k.status_tindak_lanjut === 'Perlu Tindakan') {
            statusBadge = '<span class="badge bg-danger text-white px-3 py-1 rounded-pill">Perlu Tindakan</span>';
        }
        statusDiv.innerHTML = statusBadge;
        
        // Foto
        const fotoContainer = document.getElementById('detailFotoContainer');
        if (k.foto_url) {
            fotoContainer.innerHTML = '<a href="' + k.foto_url + '" target="_blank"><img src="' + k.foto_url + '" alt="Foto Kunjungan" style="max-width: 100%; max-height: 300px; border-radius: 8px; cursor: pointer;"></a>';
        } else {
            fotoContainer.innerHTML = '<div style="width: 200px; height: 200px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 1px solid #E5E7EB;"><i class="bi bi-image text-muted" style="font-size: 48px; opacity: 0.4;"></i></div>';
        }
        
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetail'));
        modal.show();
    }
</script>
@endpush
