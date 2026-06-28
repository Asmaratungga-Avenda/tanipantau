<?php
/**
 * daftar-lahan.php — Daftar Lahan Pertanian Publik
 * Menampilkan semua lahan aktif dengan pagination
 * Data diambil dari API Laravel (GET /api/lahan)
 */
require_once __DIR__ . '/config/api.php';

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$params = ['page' => $page];
if ($search) {
    $params['search'] = $search;
}

$endpoint = 'lahan?' . http_build_query($params);
$response = apiRequest($endpoint, 'GET');

$lahanData = [];
$lastPage = 1;
$total = 0;

if ($response['http_code'] === 200 && $response['data']) {
    $result = $response['data'];
    $lahanData = $result['data'] ?? [];
    $lastPage = $result['meta']['last_page'] ?? 1;
    $total = $result['meta']['total'] ?? 0;
    $currentPage = $result['meta']['current_page'] ?? 1;
}

// Mapping fase
$growthPhases = [
    'Persiapan'     => ['label' => 'Persiapan',     'class' => 'badge-bg-secondary'],
    'Penanaman'     => ['label' => 'Penanaman',     'class' => 'badge-bg-info'],
    'Pertumbuhan'   => ['label' => 'Pertumbuhan',   'class' => 'badge-bg-success'],
    'Panen'         => ['label' => 'Panen',         'class' => 'badge-bg-warning'],
    'Panen Selesai' => ['label' => 'Panen Selesai', 'class' => 'badge-bg-secondary'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Lahan - TaniPantau</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        .search-box-container {
            max-width: 700px;
            margin: -40px auto 48px;
            position: relative;
            z-index: 10;
        }
        
        .search-input-group {
            background: var(--white);
            border-radius: 16px;
            padding: 12px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .search-input-group .form-control {
            border: 2px solid var(--border-light);
            background: var(--white);
            box-shadow: none;
            padding: 14px 20px;
            font-size: 15px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .search-input-group .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 111, 90, 0.1);
        }
        
        .search-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: white;
            border-radius: 12px;
            padding: 12px 20px;
        }
        
        .lahan-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid var(--border-light);
        }
        
        .lahan-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .lahan-card-header {
            padding: 20px;
            border-bottom: 1px dashed var(--border-light);
        }
        
        .lahan-card-body {
            padding: 20px;
        }
        
        .lahan-card-footer {
            padding: 16px 20px;
            background: linear-gradient(135deg, rgba(0, 111, 90, 0.03) 0%, rgba(0, 111, 90, 0.06) 100%);
            border-top: 1px solid var(--border-light);
        }
        
        .lahan-meta-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .lahan-meta-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 12px;
        }
        
        .lahan-meta-list li:last-child {
            margin-bottom: 0;
        }
        
        .lahan-meta-list i {
            color: var(--primary);
            width: 22px;
            text-align: center;
            font-size: 17px;
        }
        
        .pagination-modern .page-link {
            border: none;
            color: var(--text-dark);
            font-weight: 500;
            padding: 10px 16px;
            border-radius: 10px;
            margin: 0 4px;
            transition: var(--transition);
        }
        
        .pagination-modern .page-item.active .page-link {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(0, 111, 90, 0.25);
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

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .search-box-container {
                max-width: 600px;
                margin: -35px auto 36px;
            }
        }
        
        @media (max-width: 768px) {
            .search-box-container {
                margin: -25px auto 30px;
            }

            .search-input-group {
                padding: 10px;
            }

            .search-input-group .form-control {
                padding: 12px 16px;
                font-size: 14px;
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
        <div class="container animate-fade-in-up">
            <h1 class="mb-3"><i class="bi bi-map-fill me-2"></i>Daftar Lahan Pertanian</h1>
            <p>Menjelajahi <?= number_format($total) ?> lahan terdaftar di wilayah pemantauan</p>
        </div>
    </section>

    <!-- Content -->
    <section class="py-5">
        <div class="container">
            <!-- Search -->
            <div class="search-box-container animate-fade-in-up">
                <form method="GET" action="daftar-lahan.php" class="search-input-group">
                    <div class="search-icon-wrapper">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" class="form-control flex-grow-1" name="search"
                           value="<?= htmlspecialchars($search) ?>"
                           placeholder="Cari nama lahan, komoditas, atau petani...">
                    
                    <button type="submit" class="btn-primary-tp" style="padding: 14px 28px; border-radius: 12px; font-weight: 600;">
                        Cari
                    </button>
                    
                    <?php if ($search): ?>
                        <a href="daftar-lahan.php" class="btn-outline-tp" style="padding: 12px 20px; border-radius: 12px;">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    <?php endif; ?>
                </form>
                
               
            </div>

            <?php if (!empty($lahanData)): ?>
            <!-- Grid -->
            <div class="row g-4">
                <?php foreach ($lahanData as $index => $l): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 reveal" style="transition-delay: <?= min($index * 0.1, 0.3) ?>s">
                    <div class="lahan-card h-100 d-flex flex-column">
                        <div class="lahan-card-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="mb-0" style="color: var(--primary); font-weight: 600; font-size: 17px; line-height: 1.3;">
                                    <?= htmlspecialchars($l['nama_lahan'] ?? '') ?>
                                </h5>
                                <?php
                                    $fase = $l['fase_lahan'] ?? '';
                                    $phaseInfo = $growthPhases[$fase] ?? ['label' => $fase, 'class' => 'badge-bg-secondary'];
                                ?>
                                <span class="badge-fase <?= $phaseInfo['class'] ?> ms-2 flex-shrink-0"><?= $phaseInfo['label'] ?></span>
                            </div>
                        </div>
                        
                        <div class="lahan-card-body flex-grow-1">
                            <ul class="lahan-meta-list">
                                <li>
                                    <i class="bi bi-person-circle"></i>
                                    <span><?= htmlspecialchars($l['petani']['nama'] ?? '-') ?></span>
                                </li>
                                <li>
                                    <i class="bi bi-basket3-fill"></i>
                                    <span><?= htmlspecialchars($l['komoditas'] ?? '-') ?></span>
                                </li>
                                <li>
                                    <i class="bi bi-rulers"></i>
                                    <span><?= number_format($l['luas_lahan'] ?? 0, 2) ?> Hektar</span>
                                </li>
                                <?php if ($l['tanggal_tanam']): ?>
                                <li>
                                    <i class="bi bi-calendar-check-fill"></i>
                                    <span>Tanam: <?= date('d M Y', strtotime($l['tanggal_tanam'])) ?></span>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <div class="lahan-card-footer">
                            <a href="detail-lahan.php?id=<?= $l['id'] ?>" class="btn-outline-tp w-100 d-flex justify-content-between align-items-center" style="border-width: 2px; padding: 12px 20px; border-radius: 12px;">
                                <span>Lihat Detail</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($lastPage > 1): ?>
            <div class="mt-5 d-flex justify-content-center">
                <nav>
                    <ul class="pagination pagination-modern">
                        <?php
                            // Build query string for pagination
                            $queryString = $search ? '&search=' . urlencode($search) : '';
                        ?>
                        
                        <!-- Previous -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?><?= $queryString ?>">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php
                            $start = max(1, $page - 2);
                            $end = min($lastPage, $page + 2);
                            if ($start > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1<?= $queryString ?>">1</a>
                            </li>
                            <?php if ($start > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; endif; ?>

                        <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= $queryString ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($end < $lastPage): ?>
                            <?php if ($end < $lastPage - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $lastPage ?><?= $queryString ?>"><?= $lastPage ?></a>
                            </li>
                        <?php endif; ?>

                        <!-- Next -->
                        <li class="page-item <?= $page >= $lastPage ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?><?= $queryString ?>">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <div class="text-center py-5 reveal">
                <i class="bi bi-map text-muted" style="font-size: 64px; opacity: 0.5;"></i>
                <h4 class="mt-4" style="font-weight: 600;">Tidak Ada Data Lahan</h4>
                <p class="text-muted">Belum ada lahan pertanian yang sesuai dengan pencarian Anda atau terdaftar di sistem.</p>
                <?php if ($search): ?>
                    <a href="daftar-lahan.php" class="btn-outline-tp mt-3">Reset Pencarian</a>
                <?php endif; ?>
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
            if (window.scrollY > 30) nav.classList.add('scrolled');
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
