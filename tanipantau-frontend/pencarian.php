<?php
/**
 * pencarian.php — Pencarian Petani & Lahan
 * Search page untuk mencari petani dan lahan berdasarkan berbagai kriteria
 * Data dari API: GET /api/petani?search=... + GET /api/lahan?search=...
 */
require_once __DIR__ . '/config/api.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'semua';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$petaniData = [];
$lahanData = [];
$totalPetani = 0;
$totalLahan = 0;
$lastPagePetani = 1;
$lastPageLahan = 1;

if ($search) {
    // Search petani
    $resPetani = apiRequest('petani?' . http_build_query(['search' => $search, 'page' => $page]), 'GET');
    if ($resPetani['http_code'] === 200 && $resPetani['data']) {
        $petaniData = $resPetani['data']['data'] ?? [];
        $totalPetani = $resPetani['data']['meta']['total'] ?? 0;
        $lastPagePetani = $resPetani['data']['meta']['last_page'] ?? 1;
    }

    // Search lahan
    $resLahan = apiRequest('lahan?' . http_build_query(['search' => $search, 'page' => $page]), 'GET');
    if ($resLahan['http_code'] === 200 && $resLahan['data']) {
        $lahanData = $resLahan['data']['data'] ?? [];
        $totalLahan = $resLahan['data']['meta']['total'] ?? 0;
        $lastPageLahan = $resLahan['data']['meta']['last_page'] ?? 1;
    }
}

// Mapping fase
$growthPhases = [
    'Persiapan'     => ['label' => 'Persiapan',     'class' => 'badge-bg-secondary'],
    'Penanaman'     => ['label' => 'Penanaman',     'class' => 'badge-bg-info'],
    'Pertumbuhan'   => ['label' => 'Pertumbuhan',   'class' => 'badge-bg-success'],
    'Panen'         => ['label' => 'Panen',         'class' => 'badge-bg-warning'],
    'Panen Selesai' => ['label' => 'Panen Selesai', 'class' => 'badge-bg-secondary'],
];

$totalResults = $totalPetani + $totalLahan;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian - TaniPantau</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .search-container {
            max-width: 800px;
            margin: -40px auto 40px;
            position: relative;
            z-index: 10;
        }
        
        .search-card {
            background: var(--white);
            border-radius: var(--radius-card);
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            padding: 24px;
            border: 1px solid var(--border-light);
        }
        
        .search-input-wrapper {
            background: #F9FAFB;
            border: 1px solid var(--border-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            padding: 4px 4px 4px 16px;
            transition: var(--transition);
        }
        
        .search-input-wrapper:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 111, 90, 0.1);
            background: var(--white);
        }
        
        .search-input-wrapper input {
            border: none;
            background: transparent;
            box-shadow: none;
            padding: 12px;
            font-size: 16px;
            color: var(--text-dark);
        }
        
        .search-input-wrapper input:focus {
            box-shadow: none;
            background: transparent;
        }

        /* Tabs */
        .result-tabs {
            border-bottom: 2px solid var(--border-light);
            margin-bottom: 30px;
        }
        
        .result-tabs .nav-link {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 15px;
            padding: 12px 24px;
            border: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: var(--transition);
        }
        
        .result-tabs .nav-link:hover {
            color: var(--primary);
        }
        
        .result-tabs .nav-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background: transparent;
        }
        
        .result-tabs .badge {
            background: #F3F4F6;
            color: var(--text-muted);
            font-size: 12px;
            margin-left: 8px;
            border-radius: 50px;
            padding: 4px 8px;
        }
        
        .result-tabs .nav-link.active .badge {
            background: rgba(0, 111, 90, 0.1);
            color: var(--primary);
        }

        /* Result Cards */
        .result-item {
            background: var(--white);
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 16px;
            border: 1px solid var(--border-light);
            transition: var(--transition);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .result-item:hover {
            border-color: rgba(0, 111, 90, 0.3);
            box-shadow: var(--shadow-soft);
            transform: translateY(-2px);
        }
        
        .result-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 20px;
            flex-shrink: 0;
        }
        
        .icon-petani { background: rgba(59, 130, 246, 0.1); color: #3B82F6; }
        .icon-lahan { background: rgba(0, 111, 90, 0.1); color: var(--primary); }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: 16px;
            border: 1px dashed var(--border-light);
        }
        
        .empty-icon {
            width: 80px;
            height: 80px;
            background: #F9FAFB;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: var(--text-muted);
            margin: 0 auto 24px;
        }

        .pagination-modern .page-link {
            border: none;
            color: var(--text-dark);
            font-weight: 500;
            padding: 10px 16px;
            border-radius: 8px;
            margin: 0 4px;
            transition: var(--transition);
        }
        
        .pagination-modern .page-item.active .page-link {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(0, 111, 90, 0.2);
        }
        
        .pagination-modern .page-link:hover:not(.active) {
            background: var(--bg-light);
            color: var(--primary);
        }

        .pagination-modern .page-item.disabled .page-link {
            background: transparent;
            color: var(--text-muted);
            opacity: 0.5;
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
                <a href="daftar-lahan.php" class="ms-lg-3"><i class="bi bi-map me-1"></i>Peta Lahan</a>
                <a href="pencarian.php" class="active"><i class="bi bi-search me-1"></i>Pencarian</a>
                
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="page-hero pb-5">
        <div class="container text-center animate-fade-in-up">
            <h1 class="mb-3"><i class="bi bi-search me-2"></i>Pusat Pencarian</h1>
            <p>Telusuri database petani dan lahan pertanian secara terpusat</p>
        </div>
    </section>

    <!-- Search Box -->
    <div class="container">
        <div class="search-container animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="search-card">
                <form method="GET" action="pencarian.php">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search text-muted fs-5"></i>
                        <input type="text" class="form-control" name="search"
                               value="<?= htmlspecialchars($search) ?>"
                               placeholder="Cari berdasarkan nama, NIK, komoditas, atau lokasi..."
                               autofocus>
                        <button type="submit" class="btn-primary-tp" style="padding: 12px 32px;">
                            Cari Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Results -->
    <section class="py-2" style="padding-bottom: 80px;">
        <div class="container">
            <?php if ($search): ?>
                <div class="mb-4 text-center reveal">
                    <p class="text-muted" style="font-size: 15px;">
                        Hasil pencarian untuk <strong class="text-dark">"<?= htmlspecialchars($search) ?>"</strong> 
                        &bull; Ditemukan <?= $totalResults ?> data
                    </p>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-pills result-tabs justify-content-center reveal">
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'semua' ? 'active' : '' ?>" href="?search=<?= urlencode($search) ?>&tab=semua">
                            Semua Data <span class="badge"><?= $totalResults ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'petani' ? 'active' : '' ?>" href="?search=<?= urlencode($search) ?>&tab=petani">
                            Data Petani <span class="badge"><?= $totalPetani ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $tab === 'lahan' ? 'active' : '' ?>" href="?search=<?= urlencode($search) ?>&tab=lahan">
                            Data Lahan <span class="badge"><?= $totalLahan ?></span>
                        </a>
                    </li>
                </ul>

                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <!-- Petani Results -->
                        <?php if (($tab === 'semua' || $tab === 'petani') && !empty($petaniData)): ?>
                        <div class="mb-5 reveal">
                            <?php if ($tab === 'semua'): ?>
                            <h5 class="mb-3" style="color: var(--text-dark); font-weight: 600;">
                                Profil Petani
                            </h5>
                            <?php endif; ?>
                            
                            <?php foreach ($petaniData as $p): ?>
                            <div class="result-item">
                                <div class="d-flex align-items-center w-100">
                                    <div class="result-icon icon-petani">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1" style="font-weight: 600; font-size: 16px;">
                                            <?= htmlspecialchars($p['nama'] ?? '') ?>
                                        </h6>
                                        <div class="d-flex flex-wrap gap-3 text-muted" style="font-size: 13px;">
                                            <span><i class="bi bi-telephone-fill me-1"></i><?= htmlspecialchars($p['nomor_hp'] ?? '-') ?></span>
                                            <span><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($p['desa'] ?? '-') ?>, <?= htmlspecialchars($p['kecamatan'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Lahan Results -->
                        <?php if (($tab === 'semua' || $tab === 'lahan') && !empty($lahanData)): ?>
                        <div class="mb-4 reveal">
                            <?php if ($tab === 'semua'): ?>
                            <h5 class="mb-3" style="color: var(--text-dark); font-weight: 600;">
                                Lokasi Lahan
                            </h5>
                            <?php endif; ?>
                            
                            <?php foreach ($lahanData as $l): ?>
                            <?php
                                $fase = $l['fase_lahan'] ?? '';
                                $phaseInfo = $growthPhases[$fase] ?? ['label' => $fase, 'class' => 'badge-bg-secondary'];
                            ?>
                            <div class="result-item flex-column flex-md-row gap-3">
                                <div class="d-flex align-items-center w-100">
                                    <div class="result-icon icon-lahan">
                                        <i class="bi bi-map-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h6 class="mb-0" style="font-weight: 600; font-size: 16px;">
                                                <?= htmlspecialchars($l['nama_lahan'] ?? '') ?>
                                            </h6>
                                            <span class="badge-fase <?= $phaseInfo['class'] ?>" style="font-size: 10px; padding: 4px 8px;"><?= $phaseInfo['label'] ?></span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-3 text-muted" style="font-size: 13px;">
                                            <span><i class="bi bi-person-fill me-1"></i>Pemilik: <?= htmlspecialchars($l['petani']['nama'] ?? '-') ?></span>
                                            <span><i class="bi bi-basket3-fill me-1"></i><?= htmlspecialchars($l['komoditas'] ?? '-') ?></span>
                                            <span><i class="bi bi-rulers me-1"></i><?= number_format($l['luas_lahan'] ?? 0, 2) ?> Ha</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-md-end w-100 w-md-auto mt-2 mt-md-0" style="min-width: 120px;">
                                    <a href="detail-lahan.php?id=<?= $l['id'] ?>" class="btn-outline-tp w-100 px-3 py-2" style="font-size: 13px;">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Pagination -->
                        <?php
                            $maxPage = 1;
                            if ($tab === 'petani') {
                                $maxPage = $lastPagePetani;
                            } elseif ($tab === 'lahan') {
                                $maxPage = $lastPageLahan;
                            } else {
                                $maxPage = max($lastPagePetani, $lastPageLahan);
                            }
                        ?>
                        <?php if ($maxPage > 1): ?>
                        <div class="mt-4 d-flex justify-content-center">
                            <nav>
                                <ul class="pagination pagination-modern justify-content-center">
                                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?search=<?= urlencode($search) ?>&tab=<?= $tab ?>&page=<?= $page - 1 ?>">
                                            <i class="bi bi-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                    <?php for ($i = max(1, $page - 2); $i <= min($maxPage, $page + 2); $i++): ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?search=<?= urlencode($search) ?>&tab=<?= $tab ?>&page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $page >= $maxPage ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?search=<?= urlencode($search) ?>&tab=<?= $tab ?>&page=<?= $page + 1 ?>">
                                            Next <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <?php endif; ?>

                        <!-- No Results -->
                        <?php if ($totalResults === 0): ?>
                        <div class="empty-state reveal">
                            <div class="empty-icon"><i class="bi bi-search"></i></div>
                            <h4 style="font-weight: 600; color: var(--text-dark);">Tidak Ditemukan</h4>
                            <p class="text-muted mt-2">Maaf, sistem tidak menemukan data yang cocok untuk "<strong><?= htmlspecialchars($search) ?></strong>".</p>
                            <a href="pencarian.php" class="btn-outline-tp mt-3">Bersihkan Pencarian</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <div class="row justify-content-center reveal">
                    <div class="col-lg-6">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-search-heart"></i></div>
                            <h4 style="font-weight: 600; color: var(--text-dark);">Mulai Telusuri Data</h4>
                            <p class="text-muted mb-4">Gunakan kolom pencarian di atas untuk menemukan data yang Anda butuhkan dengan cepat.</p>
                            
                            <div class="text-start bg-light p-4 rounded-3 border">
                                <p class="text-dark fw-bold mb-2" style="font-size: 14px;">💡 Tips Pencarian:</p>
                                <ul class="text-muted mb-0 ps-3" style="font-size: 14px; line-height: 1.8;">
                                    <li>Ketik nama spesifik petani (mis. <em>"Budi"</em>)</li>
                                    <li>Ketik jenis komoditas panen (mis. <em>"Padi"</em> atau <em>"Jagung"</em>)</li>
                                    <li>Ketik nama desa atau kecamatan lokasi lahan</li>
                                    <li>Gunakan NIK lengkap untuk pencarian akurat</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
</body>
</html>
