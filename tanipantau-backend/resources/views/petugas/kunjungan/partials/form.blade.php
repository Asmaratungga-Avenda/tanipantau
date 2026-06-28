<div class="card animate-slide-up">
    <div class="card-body p-4">
        @if(isset($selectedLahan) && $selectedLahan)
            <div class="mb-4 p-3 border rounded-3 bg-light">
                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Detail Lahan</h6>
                <div class="row g-2 text-sm">
                    <div class="col-md-6">
                        <span class="text-muted">Nama Lahan:</span>
                        <span class="fw-semibold ms-2">{{ $selectedLahan->nama_lahan }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted">Petani:</span>
                        <span class="fw-semibold ms-2">{{ $selectedLahan->petani->nama ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted">Komoditas:</span>
                        <span class="fw-semibold ms-2">{{ $selectedLahan->komoditas }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted">Luas:</span>
                        <span class="fw-semibold ms-2">{{ number_format($selectedLahan->luas_lahan, 2) }} Ha</span>
                    </div>
                    @if($selectedLahan->desa || $selectedLahan->kecamatan || $selectedLahan->kabupaten)
                        <div class="col-12">
                            <span class="text-muted">Lokasi:</span>
                            <span class="fw-semibold ms-2">
                                {{ $selectedLahan->desa ? $selectedLahan->desa . ', ' : '' }}
                                {{ $selectedLahan->kecamatan ? $selectedLahan->kecamatan . ', ' : '' }}
                                {{ $selectedLahan->kabupaten ?? '' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
            @csrf
            @if(isset($methodField))
                @method($methodField)
            @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Lahan <span class="text-danger">*</span></label>
                    <select class="form-select" name="lahan_id" id="lahan_id" required {{ isset($selectedLahan) && $selectedLahan ? 'disabled' : '' }}>
                        @if(!isset($selectedLahan) || !$selectedLahan)
                            <option value="">Pilih Lahan</option>
                        @endif
                        @foreach($lahanList as $l)
                            <option value="{{ $l->id }}" {{ (old('lahan_id') ?? (isset($kunjungan) ? $kunjungan->lahan_id : $selectedLahanId)) == $l->id ? 'selected' : '' }}>
                                {{ $l->nama_lahan }} — {{ $l->petani->nama ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @if(isset($selectedLahan) && $selectedLahan)
                        <input type="hidden" name="lahan_id" value="{{ $selectedLahan->id }}">
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Kunjungan <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" name="tanggal_kunjungan" id="tanggal_kunjungan"
                           value="{{ old('tanggal_kunjungan', isset($kunjungan) ? \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kondisi Lahan <span class="text-danger">*</span></label>
                    <select class="form-select" name="kondisi_lahan" id="kondisi_lahan" required>
                        @php
                            $kondisiOptions = ['Sangat Baik', 'Baik', 'Sedang', 'Kurang Baik', 'Sangat Kurang Baik'];
                        @endphp
                        @foreach($kondisiOptions as $opt)
                            <option value="{{ $opt }}" {{ (old('kondisi_lahan', isset($kunjungan) ? $kunjungan->kondisi_lahan : 'Baik')) == $opt ? 'selected' : '' }}>
                                {{ $opt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status Tindak Lanjut <span class="text-danger">*</span></label>
                    <select class="form-select" name="status_tindak_lanjut" id="status_tindak_lanjut" required>
                        @php
                            $statusOptions = ['Aman', 'Perlu Pemantauan', 'Perlu Tindakan'];
                        @endphp
                        @foreach($statusOptions as $opt)
                            <option value="{{ $opt }}" {{ (old('status_tindak_lanjut', isset($kunjungan) ? $kunjungan->status_tindak_lanjut : 'Aman')) == $opt ? 'selected' : '' }}>
                                {{ $opt }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" name="catatan" id="catatan" rows="3" placeholder="Catatan hasil kunjungan...">{{ old('catatan', isset($kunjungan) ? $kunjungan->catatan : '') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Foto Dokumentasi</label>
                    <input type="file" class="form-control" name="foto" id="foto" accept="image/jpeg,image/png,image/webp">
                    <small class="text-muted">Format: JPG, PNG, WEBP. Max: 5MB. {{ isset($kunjungan) ? 'Biarkan kosong jika tidak ingin mengubah foto.' : '' }}</small>

                    @if(isset($kunjungan) && $kunjungan->foto_url)
                        <div class="mt-3">
                            <div class="mb-2 small text-muted">Foto saat ini:</div>
                            <a href="{{ $kunjungan->foto_url }}" target="_blank">
                                <img src="{{ $kunjungan->foto_url }}" alt="Current Foto" style="max-width:200px; max-height:200px; border-radius:8px;" loading="lazy">
                            </a>
                        </div>
                    @endif

                    <div class="mt-2" id="foto_preview_container" style="display:none;">
                        <div class="small text-muted mb-1">{{ isset($kunjungan) ? 'Preview baru:' : 'Preview foto:' }}</div>
                        <img id="foto_preview" src="#" alt="Preview" style="max-width:200px; max-height:200px; border-radius:8px;">
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('petugas.kunjungan.index') }}" class="btn btn-outline-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-check-circle me-2"></i> {{ $submitText }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('foto').addEventListener('change', function() {
        const file = this.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('foto_preview').src = e.target.result;
                document.getElementById('foto_preview_container').style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('foto_preview_container').style.display = 'none';
        }
    });
</script>
