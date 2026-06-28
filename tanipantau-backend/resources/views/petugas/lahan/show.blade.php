@extends('layouts.petugas')

@section('title', 'Detail Lahan')
@section('page-title', 'Detail Lahan')

@section('content')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card animate-slide-up">
                <div class="card-body p-4">
                    <h5 class="mb-4" style="font-weight:600; color:var(--text-dark);">{{ $lahan->nama_lahan }}</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-row d-flex">
                                <span style="width:120px; color:var(--text-muted); font-weight:500;">Nama Petani</span>
                                <span style="color:var(--text-dark); font-weight:500;">{{ $lahan->petani->nama ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row d-flex">
                                <span style="width:120px; color:var(--text-muted); font-weight:500;">Komoditas</span>
                                <span style="color:var(--text-dark); font-weight:500;">{{ $lahan->komoditas }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row d-flex">
                                <span style="width:120px; color:var(--text-muted); font-weight:500;">Luas Lahan</span>
                                <span style="color:var(--text-dark); font-weight:500;">{{ number_format($lahan->luas_lahan, 2) }} Ha</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row d-flex">
                                <span style="width:120px; color:var(--text-muted); font-weight:500;">Fase</span>
                                <span style="color:var(--text-dark); font-weight:500;">
                                    <span class="badge" style="background: #E8F5E9; color: #2E7D32;">{{ $lahan->fase_lahan }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row d-flex">
                                <span style="width:120px; color:var(--text-muted); font-weight:500;">Tanggal Tanam</span>
                                <span style="color:var(--text-dark); font-weight:500;">{{ $lahan->tanggal_tanam ? \Carbon\Carbon::parse($lahan->tanggal_tanam)->translatedFormat('d F Y') : '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row d-flex">
                                <span style="width:120px; color:var(--text-muted); font-weight:500;">Status</span>
                                <span style="color:var(--text-dark); font-weight:500;">
                                    @if($lahan->status_aktif)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-row d-flex">
                                <span style="width:120px; color:var(--text-muted); font-weight:500;">Lokasi</span>
                                <span style="color:var(--text-dark); font-weight:500;">
                                    {{ $lahan->desa ?? '' }}, {{ $lahan->kecamatan ?? '' }}, {{ $lahan->kabupaten ?? '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('petugas.kunjungan.create', ['lahan' => $lahan->id]) }}" class="btn btn-primary-custom">
                            <i class="bi bi-plus-circle me-2"></i> Tambah Kunjungan
                        </a>
                        <a href="{{ route('petugas.lahan.index') }}" class="btn btn-outline-secondary ms-2">Kembali</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card animate-slide-up" style="animation-delay:0.1s;">
                <div class="card-header" style="background:var(--primary); color:white; padding:20px 24px; border-radius:16px 16px 0 0;">
                    <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Lokasi Peta</h5>
                </div>
                <div class="card-body p-0">
                    @if(!empty($lahan->garis_lintang) && !empty($lahan->garis_bujur))
                        <div id="map" style="height: 300px; border-radius:0 0 16px 16px;"></div>
                        <div class="p-3">
                            <small class="text-muted">
                                <i class="bi bi-geo me-1"></i>
                                {{ $lahan->garis_lintang }}, {{ $lahan->garis_bujur }}
                            </small>
                        </div>
                    @else
                        <div class="p-5 text-center">
                            <i class="bi bi-geo-alt" style="font-size:48px; color:var(--text-muted); opacity:0.4;"></i>
                            <p class="text-muted mb-0 mt-2">Koordinat belum tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card animate-slide-up" style="animation-delay:0.2s;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background:var(--primary); color:white; padding:20px 24px; border-radius:16px 16px 0 0;">
                    <h5 class="mb-0"><i class="bi bi-clipboard2-check me-2"></i>Riwayat Kunjungan</h5>
                </div>
                <div class="card-body p-0">
                    @if($lahan->kunjungan && $lahan->kunjungan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Tanggal</th>
                                        <th>Kondisi</th>
                                        <th>Status Tindak Lanjut</th>
                                        <th>Catatan</th>
                                        <th class="text-center">Foto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lahan->kunjungan as $kunjungan)
                                    <tr>
                                        <td class="ps-4">{{ \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->translatedFormat('d F Y H:i') }}</td>
                                        <td>
                                            @php
                                                $kondisiColors = [
                                                    'Sangat Baik' => ['bg' => '#E8F5E9', 'color' => '#2E7D32'],
                                                    'Baik' => ['bg' => '#E3F2FD', 'color' => '#1565C0'],
                                                    'Sedang' => ['bg' => '#FFF3E0', 'color' => '#E65100'],
                                                    'Kurang Baik' => ['bg' => '#FCE4EC', 'color' => '#C62828'],
                                                    'Sangat Kurang Baik' => ['bg' => '#FFEBEE', 'color' => '#B71C1C'],
                                                ];
                                                $kondisi = $kondisiColors[$kunjungan->kondisi_lahan] ?? ['bg' => '#F3F4F6', 'color' => '#6B7280'];
                                            @endphp
                                            <span class="badge" style="background: {{ $kondisi['bg'] }}; color: {{ $kondisi['color'] }};">{{ $kunjungan->kondisi_lahan }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'Aman' => ['bg' => '#E8F5E9', 'color' => '#2E7D32'],
                                                    'Perlu Pemantauan' => ['bg' => '#FFF3E0', 'color' => '#E65100'],
                                                    'Perlu Tindakan' => ['bg' => '#FFEBEE', 'color' => '#B71C1C'],
                                                ];
                                                $status = $statusColors[$kunjungan->status_tindak_lanjut] ?? ['bg' => '#F3F4F6', 'color' => '#6B7280'];
                                            @endphp
                                            <span class="badge" style="background: {{ $status['bg'] }}; color: {{ $status['color'] }};">{{ $kunjungan->status_tindak_lanjut }}</span>
                                        </td>
                                        <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $kunjungan->catatan ?? '-' }}</td>
                                        <td class="text-center">
                                            @if($kunjungan->foto_url)
                                                <a href="{{ $kunjungan->foto_url }}" target="_blank">
                                                    <img src="{{ $kunjungan->foto_url }}" alt="Foto" style="max-width:60px; max-height:60px; border-radius:8px; cursor:pointer;" loading="lazy" decoding="async">
                                                </a>
                                            @else
                                                <div style="width:60px; height:60px; border-radius:8px; background:#F3F4F6; display:flex; align-items:center; justify-content:center; margin:0 auto; border:1px solid var(--border-light);">
                                                    <i class="bi bi-image text-muted" style="font-size:24px; opacity:0.4;"></i>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-5 text-center">
                            <i class="bi bi-clipboard2-check" style="font-size:48px; color:var(--text-muted); opacity:0.4;"></i>
                            <h6 class="mt-3 mb-1">Belum Ada Kunjungan</h6>
                            <p class="text-muted small">Silakan tambahkan kunjungan pertama</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
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
    </style>
@endpush

@push('scripts')
    @if(!empty($lahan->garis_lintang) && !empty($lahan->garis_bujur))
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
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
            
            document.addEventListener('DOMContentLoaded', function() {
                var map = L.map('map').setView([{{ $lahan->garis_lintang }}, {{ $lahan->garis_bujur }}], 15);
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
                
                const customIcon = createCustomMarker('{{ addslashes($lahan->komoditas) }}');
                L.marker([{{ $lahan->garis_lintang }}, {{ $lahan->garis_bujur }}], { icon: customIcon }).addTo(map)
                    .bindPopup(`
                        <div class="popup-wrapper">
                            <div class="popup-header">
                                <i class="bi bi-geo-alt-fill text-primary"></i>
                                <h6>{{ addslashes($lahan->nama_lahan) }}</h6>
                            </div>
                            <div class="popup-content">
                                <p><strong>Komoditas:</strong> {{ addslashes($lahan->komoditas) }}</p>
                                <p><strong>Luas:</strong> {{ number_format($lahan->luas_lahan, 2) }} Ha</p>
                                <p><strong>Fase:</strong> {{ addslashes($lahan->fase_lahan) }}</p>
                            </div>
                        </div>
                    `);
            });
        </script>
    @endif
@endpush
