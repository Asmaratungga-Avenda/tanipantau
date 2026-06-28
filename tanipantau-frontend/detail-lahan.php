<?php
/**
 * detail-lahan.php — Detail Lahan Pertanian
 * Menampilkan informasi lengkap satu lahan + peta Leaflet
 * Data dari API: GET /api/lahan/{id}
 */
require_once __DIR__ . '/config/api.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: daftar-lahan.php');
    exit;
}

$response = apiRequest("lahan/{$id}", 'GET');

if ($response['http_code'] !== 200 || empty($response['data']['data'])) {
    header('Location: daftar-lahan.php');
    exit;
}

$l = $response['data']['data'];

// Ambil data petani jika ada
$petani = $l['petani'] ?? [];

// Mapping fase
$growthPhases = [
    'Persiapan'     => ['label' => 'Persiapan',     'class' => 'badge-bg-secondary'],
    'Penanaman'     => ['label' => 'Penanaman',     'class' => 'badge-bg-info'],
    'Pertumbuhan'   => ['label' => 'Pertumbuhan',   'class' => 'badge-bg-success'],
    'Panen'         => ['label' => 'Panen',         'class' => 'badge-bg-warning'],
    'Panen Selesai' => ['label' => 'Panen Selesai', 'class' => 'badge-bg-secondary'],
];

$fase = $l['fase_lahan'] ?? '';
$phaseInfo = $growthPhases[$fase] ?? ['label' => $fase, 'class' => 'badge-bg-secondary'];

$hasCoords = !empty($l['garis_lintang']) && !empty($l['garis_bujur']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lahan - TaniPantau</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .detail-header-card {
            background: var(--white);
            border-radius: var(--radius-card);
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            padding: 32px;
            margin-top: -60px;
            position: relative;
            z-index: 10;
            margin-bottom: 30px;
            border: 1px solid var(--border-light);
        }
        
        .info-group {
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-light);
        }
        
        .info-group:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .info-label {
            font-size: 13px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 16px;
            color: var(--text-dark);
            font-weight: 500;
        }
        
        .map-container {
            height: 100%;
            min-height: 400px;
            border-radius: var(--radius-card);
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-light);
        }
        
        .petani-card {
            background: rgba(0, 111, 90, 0.03);
            border-radius: 12px;
            padding: 20px;
            border: 1px dashed rgba(0, 111, 90, 0.2);
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
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .detail-header-card {
                padding: 24px;
                margin-top: -40px;
            }
        }
        
        @media (max-width: 768px) {
            .detail-header-card {
                padding: 20px;
                margin-top: -30px;
                margin-bottom: 20px;
            }
            
            .map-container {
                min-height: 300px;
            }
            
            .info-group {
                margin-bottom: 16px;
                padding-bottom: 16px;
            }
            
            .page-hero {
                padding: 100px 0 50px;
            }
            
            .page-hero h1 {
                font-size: 28px !important;
            }
            
            .map-legend {
                padding: 10px 12px;
            }
            
            .legend-item {
                font-size: 12px;
            }
        }
        
        @media (max-width: 576px) {
            .detail-header-card {
                padding: 16px;
                margin-top: -20px;
            }
            
            .page-hero h1 {
                font-size: 24px !important;
            }
            
            .map-container {
                min-height: 250px;
            }
            
            .petani-card {
                padding: 16px;
            }
            
            .info-value {
                font-size: 15px;
            }
            
            /* Table Mobile Styling */
            .table-responsive {
                padding: 10px;
                border-radius: 12px;
            }
            
            .table {
                font-size: 12px;
            }
            
            .table th, .table td {
                padding: 10px 8px;
            }
            
            .table img {
                max-width: 60px !important;
                max-height: 60px !important;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar-tp" id="mainNav">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="navbar-brand-tp">
                <i class="bi bi-tree-fill"></i>
                TaniPantau
            </a>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="bi bi-list"></i>
            </button>

            <div class="nav-links" id="navLinks">
                <a href="index.php">Beranda</a>
                <a href="index.php#fitur">Fitur</a>
                <a href="index.php#statistik">Statistik</a>
                <a href="index.php#faq">FAQ</a>
                <a href="index.php#kontak">Kontak</a>
                <a href="daftar-lahan.php" class="active ms-lg-3"><i class="bi bi-map me-1"></i>Peta Lahan</a>
                <a href="pencarian.php"><i class="bi bi-search me-1"></i>Pencarian</a>
                
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="page-hero pb-5">
        <div class="container">
            <div class="mb-4 text-start">
                <a href="daftar-lahan.php" class="btn-outline-tp text-white" style="border-color: rgba(255,255,255,0.3); padding: 8px 16px;">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
            <h1 class="mb-5 pb-4" style="font-size: 36px;">Detail Lahan</h1>
        </div>
    </section>

    <!-- Content -->
    <section style="padding-bottom: 80px;">
        <div class="container">
            <div class="detail-header-card animate-fade-in-up">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h2 class="mb-2" style="color: var(--primary); font-weight: 700;">
                            <?= htmlspecialchars($l['nama_lahan'] ?? 'Lahan Tanpa Nama') ?>
                        </h2>
                        <div class="d-flex align-items-center gap-3 text-muted">
                            <span><i class="bi bi-geo-alt-fill me-1 text-primary"></i> <?= $hasCoords ? "Titik Koordinat Tersedia" : "Tanpa Koordinat" ?></span>
                            <?php if ($l['status_aktif'] ?? false): ?>
                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1"><i class="bi bi-dash-circle-fill me-1"></i>Tidak Aktif</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-md-end">
                        <div class="info-label text-md-end mb-1">Fase Penanaman Saat Ini</div>
                        <span class="badge-fase <?= $phaseInfo['class'] ?>" style="font-size: 14px; padding: 8px 16px;">
                            <?= $phaseInfo['label'] ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <!-- Info Kolom Kiri -->
                <div class="col-lg-5 reveal">
                    <div class="card-modern">
                        <div class="card-body p-4">
                            <h5 class="mb-4 d-flex align-items-center gap-2" style="font-weight: 600;">
                                <i class="bi bi-info-square-fill text-primary"></i> Spesifikasi Lahan
                            </h5>
                            
                            <div class="info-group">
                                <div class="info-label">Komoditas Utama</div>
                                <div class="info-value d-flex align-items-center gap-2">
                                    <i class="bi bi-basket3 text-secondary"></i>
                                    <?= htmlspecialchars($l['komoditas'] ?? '-') ?>
                                </div>
                            </div>
                            
                            <div class="info-group">
                                <div class="info-label">Luas Area</div>
                                <div class="info-value">
                                    <?= number_format($l['luas_lahan'] ?? 0, 2) ?> Hektar
                                </div>
                            </div>
                            
                            <div class="info-group">
                                <div class="info-label">Tanggal Mulai Tanam</div>
                                <div class="info-value">
                                    <?= !empty($l['tanggal_tanam']) ? date('d F Y', strtotime($l['tanggal_tanam'])) : 'Belum Ditentukan' ?>
                                </div>
                            </div>
                            
                            <?php if ($hasCoords): ?>
                            <div class="info-group">
                                <div class="info-label">Garis Lintang & Bujur</div>
                                <div class="info-value text-muted" style="font-size: 14px; font-family: monospace;">
                                    <?= $l['garis_lintang'] ?>, <?= $l['garis_bujur'] ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($petani)): ?>
                            <h5 class="mt-5 mb-3 d-flex align-items-center gap-2" style="font-weight: 600;">
                                <i class="bi bi-person-badge-fill text-primary"></i> Pemilik Lahan
                            </h5>
                            <div class="petani-card">
                                <div class="fw-bold text-dark mb-1" style="font-size: 16px;">
                                    <?= htmlspecialchars($petani['nama'] ?? '-') ?>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2 text-dark" style="font-size: 14px;">
                                    <i class="bi bi-telephone-fill text-primary opacity-75"></i>
                                    <?= htmlspecialchars($petani['nomor_hp'] ?? '-') ?>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-dark" style="font-size: 14px;">
                                    <i class="bi bi-house-door-fill text-primary opacity-75"></i>
                                    <?= htmlspecialchars($petani['desa'] ?? '-') ?>, <?= htmlspecialchars($petani['kecamatan'] ?? '-') ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Map Kolom Kanan -->
                <div class="col-lg-7 reveal" style="transition-delay: 0.1s;">
                    <?php if ($hasCoords): ?>
                        <div id="map" class="map-container"></div>
                    <?php else: ?>
                        <div class="card-modern h-100 d-flex flex-column align-items-center justify-content-center py-5">
                            <i class="bi bi-geo-alt text-muted" style="font-size: 64px; opacity: 0.3;"></i>
                            <h5 class="mt-4 text-muted">Pemetaan Belum Tersedia</h5>
                            <p class="text-muted small text-center px-4">Titik koordinat untuk lahan ini belum ditambahkan ke dalam sistem pemantauan.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Riwayat Kunjungan -->
            <div class="mt-5 reveal">
                <h4 class="mb-4 d-flex align-items-center gap-2" style="font-weight: 700; color: var(--primary);">
                    <i class="bi bi-journal-text"></i> Riwayat Kunjungan
                </h4>
                
                <?php if (!empty($l['kunjungan'])): ?>
                <div class="table-responsive bg-white rounded-4 shadow-sm border border-light p-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tanggal</th>
                                <th>Kondisi</th>
                                <th>Petugas</th>
                                <th>Catatan</th>
                                <th>Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($l['kunjungan'] as $kunjungan): ?>
                            <tr>
                                <td class="ps-3"><?= date('d M Y H:i', strtotime($kunjungan['tanggal_kunjungan'])) ?></td>
                                <td>
                                    <?php
                                        $kondisi = $kunjungan['kondisi_lahan'] ?? '';
                                        $badgeClass = 'bg-secondary';
                                        if ($kondisi === 'Sangat Baik') $badgeClass = 'bg-success';
                                        elseif ($kondisi === 'Baik') $badgeClass = 'bg-info';
                                        elseif ($kondisi === 'Sedang') $badgeClass = 'bg-warning text-dark';
                                        elseif ($kondisi === 'Kurang Baik') $badgeClass = 'bg-secondary';
                                        elseif ($kondisi === 'Sangat Kurang Baik') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($kondisi) ?></span>
                                </td>
                                <td><?= htmlspecialchars($kunjungan['user']['name'] ?? 'Petugas') ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($kunjungan['catatan'] ?? '-') ?></small></td>
                                <td>
                                    <?php if (!empty($kunjungan['foto_url'])): ?>
                                        <a href="<?= htmlspecialchars($kunjungan['foto_url']) ?>" target="_blank">
                                            <img src="<?= htmlspecialchars($kunjungan['foto_url']) ?>" alt="Foto Kunjungan" style="max-width: 100px; max-height: 100px; border-radius: 8px; cursor: pointer;">
                                        </a>
                                    <?php else: ?>
                                        <div style="width: 100px; height: 100px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; border: 1px solid #E5E7EB;">
                                            <i class="bi bi-image text-muted" style="font-size: 36px; opacity: 0.4;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="card-modern d-flex flex-column align-items-center justify-content-center py-5">
                    <i class="bi bi-clipboard-x text-muted" style="font-size: 48px; opacity: 0.3;"></i>
                    <h5 class="mt-3 text-muted">Belum Ada Kunjungan</h5>
                    <p class="text-muted small">Lahan ini belum pernah dikunjungi atau dicatat oleh petugas lapang.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-tp">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand d-flex align-items-center mb-3">
                        <i class="bi bi-tree-fill"></i>
                        <h4 class="mb-0 text-white">TaniPantau</h4>
                    </div>
                    <p style="color: rgba(255,255,255,0.7); line-height: 1.8;">
                        Sistem monitoring lahan pertanian dan kunjungan petugas lapang
                        secara digital. Solusi cerdas untuk agrikultur masa depan.
                    </p>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <h5 class="footer-title">Navigasi</h5>
                    <a href="index.php#beranda" class="footer-link">Beranda</a>
                    <a href="index.php#fitur" class="footer-link">Fitur</a>
                    <a href="index.php#statistik" class="footer-link">Statistik</a>
                    <a href="index.php#faq" class="footer-link">FAQ</a>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <h5 class="footer-title">Layanan Publik</h5>
                    <a href="daftar-lahan.php" class="footer-link">Peta Lahan</a>
                    <a href="pencarian.php" class="footer-link">Pencarian Lahan</a>
                    <a href="pencarian.php?tab=petani" class="footer-link">Data Petani</a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-title">Teknologi</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-dark border border-secondary text-light px-3 py-2 rounded-pill">Laravel 12 API</span>
                        <span class="badge bg-dark border border-secondary text-light px-3 py-2 rounded-pill">PHP Native Frontend</span>
                        <span class="badge bg-dark border border-secondary text-light px-3 py-2 rounded-pill">Leaflet.js Mapping</span>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="mb-0">
                    &copy; <?= date('Y') ?> <strong style="color: var(--accent);">TaniPantau Smart Farming</strong>
                    &mdash; Proyek Pemrograman Web
                </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Navbar Scrolled Effect
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
        });

        // Mobile Menu Toggle
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.getElementById('navLinks');
        if(mobileBtn) {
            mobileBtn.addEventListener('click', () => {
                navLinks.classList.toggle('show');
                const icon = mobileBtn.querySelector('i');
                if(navLinks.classList.contains('show')) {
                    icon.classList.replace('bi-list', 'bi-x-lg');
                } else {
                    icon.classList.replace('bi-x-lg', 'bi-list');
                }
            });
        }

        // Scroll Animations
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>
    
    <?php if ($hasCoords): ?>
    <script>
        // Helper to create custom icon
        function createCustomIcon(komoditas) {
            let colorBg, colorBorder;
            if (komoditas === 'Padi') {
                colorBg = '#F59E0B'; // kuning
                colorBorder = '#D97706';
            } else if (komoditas === 'Jagung') {
                colorBg = '#10B981'; // hijau
                colorBorder = '#059669';
            } else if (komoditas === 'Hortikultura') {
                colorBg = '#3B82F6'; // biru
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

        // Leaflet Map Init
        document.addEventListener('DOMContentLoaded', function() {
            const lat = <?= $l['garis_lintang'] ?>;
            const lng = <?= $l['garis_bujur'] ?>;
            const komoditas = "<?= htmlspecialchars($l['komoditas'] ?? '') ?>";
            const map = L.map('map').setView([lat, lng], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
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
            
            const customIcon = createCustomIcon(komoditas);
            
            L.marker([lat, lng], {icon: customIcon}).addTo(map)
                .bindPopup(`
                    <div class="popup-wrapper">
                        <div class="popup-header">
                            <i class="bi bi-geo-alt-fill text-primary"></i>
                            <h6><?= htmlspecialchars($l['nama_lahan'] ?? '') ?></h6>
                        </div>
                        <div class="popup-content">
                            <p><strong>Komoditas:</strong> <?= htmlspecialchars($l['komoditas'] ?? '') ?></p>
                            <p><strong>Luas:</strong> <?= number_format($l['luas_lahan'] ?? 0, 2) ?> Ha</p>
                            <p><strong>Fase:</strong> <?= htmlspecialchars($l['fase_lahan'] ?? '') ?></p>
                        </div>
                    </div>
                `)
                .openPopup();
        });
    </script>
    <?php endif; ?>
</body>
</html>
