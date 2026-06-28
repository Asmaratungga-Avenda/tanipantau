<?php
/**
 * index.php — Halaman Landing Publik TaniPantau
 * Menampilkan ringkasan statistik dan informasi umum
 */
require_once __DIR__ . '/config/api.php';

// Ambil data dashboard dari API
$response = apiRequest('dashboard', 'GET');
$stats = [
    'total_petani'    => 0,
    'total_lahan'     => 0,
    'total_kunjungan' => 0,
    'total_petugas'   => 0,
];

if ($response['http_code'] === 200 && $response['data']) {
    $stats = array_merge($stats, $response['data']['data'] ?? []);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaniPantau - Smart Farming Monitoring System</title>
    
    <!-- External CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- TaniPantau Global Design System -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <style>
        /* Specific Landing Page Styles */
        .feature-card {
            padding: 32px 24px;
            text-align: center;
            border-radius: var(--radius-card);
            background: var(--white);
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-light);
            height: 100%;
            transition: var(--transition);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .feature-icon {
            width: 72px;
            height: 72px;
            background: rgba(0, 111, 90, 0.05);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: var(--primary);
            font-size: 32px;
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon {
            background: var(--primary);
            color: var(--white);
            transform: scale(1.1);
        }

        .stat-box {
            text-align: center;
            padding: 30px;
            border-radius: var(--radius-card);
            background: var(--white);
            box-shadow: var(--shadow-soft);
            transition: var(--transition);
            border: 1px solid var(--border-light);
        }
        
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stat-value {
            font-size: 42px;
            font-weight: 700;
            color: var(--primary);
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 15px;
            font-weight: 500;
        }
        
        /* Contact Section */
        .contact-card {
            background: var(--white);
            padding: 32px;
            border-radius: var(--radius-card);
            text-align: center;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-light);
            height: 100%;
            transition: var(--transition);
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .contact-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 20px;
        }

        /* FAQ Section */
        .faq-item {
            background: var(--white);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            overflow: hidden;
            transition: var(--transition);
        }

        .faq-item.active {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(0, 111, 90, 0.1);
        }

        .faq-question {
            padding: 20px 24px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--text-dark);
            background: var(--white);
            transition: var(--transition);
        }

        .faq-item.active .faq-question {
            color: var(--primary);
            background: rgba(0, 111, 90, 0.02);
        }

        .faq-answer {
            padding: 0 24px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .faq-item.active .faq-answer {
            padding: 0 24px 24px;
            max-height: 300px;
        }
        
        .section-padding { padding: 100px 0; }
        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 111, 90, 0.1);
            color: var(--primary);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .section-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .section-title .highlight { color: var(--primary); }
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
                <a href="#beranda" class="active">Beranda</a>
                <a href="#fitur">Fitur</a>
                <a href="#statistik">Statistik</a>
                <a href="#faq">FAQ</a>
                <a href="#kontak">Kontak</a>
                <a href="daftar-lahan.php" class="ms-lg-3"><i class="bi bi-map me-1"></i>Peta Lahan</a>
                <a href="pencarian.php"><i class="bi bi-search me-1"></i>Pencarian</a>
                
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section id="beranda" class="page-hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center animate-fade-in-up">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-4" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                        <span class="badge bg-white text-primary rounded-pill">Baru</span>
                        <span style="font-size: 13px;">Sistem Pemantauan Pintar v2.0 Dirilis!</span>
                    </div>
                    <h1 class="display-4 fw-bold mb-4" style="line-height: 1.2;">
                        Pantau & Kelola Lahan Pertanian Lebih <span style="color: var(--accent);">Cerdas</span>
                    </h1>
                    <p class="lead mb-5 opacity-75">
                        TaniPantau mempermudah pencatatan, pemantauan, dan manajemen 
                        aktivitas pertanian secara digital dan terintegrasi dari mana saja.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="daftar-lahan.php" class="btn-primary-tp" style="padding: 14px 32px; font-size: 16px;">
                            Jelajahi Peta Lahan <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="#fitur" class="btn-outline-tp" style="border-color: rgba(255,255,255,0.5); color: white; padding: 14px 32px; font-size: 16px;">
                            Pelajari Fitur
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur -->
    <section id="fitur" class="section-padding" style="background: var(--bg-body);">
        <div class="container reveal">
            <div class="text-center mb-5">
                <div class="section-badge"><i class="bi bi-stars"></i> Fitur Utama</div>
                <h2 class="section-title">Kenapa Memilih <span class="highlight">TaniPantau?</span></h2>
                <p class="text-muted max-w-2xl mx-auto">Kami menyediakan solusi komprehensif untuk mendigitalisasi pemantauan lahan pertanian Anda dengan teknologi modern.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-map-fill"></i></div>
                        <h4>Pemetaan Lahan</h4>
                        <p class="text-muted mt-3">Integrasi koordinat lahan dengan Leaflet.js untuk memvisualisasikan persebaran area pertanian.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-clipboard2-data-fill"></i></div>
                        <h4>Pencatatan Kunjungan</h4>
                        <p class="text-muted mt-3">Petugas dapat mencatat kondisi lahan secara real-time dari lapangan, lengkap dengan foto dokumentasi.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        <h4>Analisis Data</h4>
                        <p class="text-muted mt-3">Dashboard analitik yang menyajikan grafik perkembangan fase lahan dan riwayat aktivitas kunjungan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik -->
    <section id="statistik" class="section-padding bg-white">
        <div class="container reveal">
            <div class="text-center mb-5">
                <div class="section-badge"><i class="bi bi-bar-chart-fill"></i> Statistik Sistem</div>
                <h2 class="section-title">Data <span class="highlight">TaniPantau</span> Saat Ini</h2>
            </div>
            
            <div class="row g-4 justify-content-center">
                <div class="col-6 col-lg-3">
                    <div class="stat-box">
                        <div class="stat-value counter-animate" data-target="<?= htmlspecialchars($stats['total_petani']) ?>">0</div>
                        <div class="stat-label">Petani Terdaftar</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-box">
                        <div class="stat-value counter-animate" data-target="<?= htmlspecialchars($stats['total_lahan']) ?>">0</div>
                        <div class="stat-label">Total Lahan</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-box">
                        <div class="stat-value counter-animate" data-target="<?= htmlspecialchars($stats['total_petugas']) ?>">0</div>
                        <div class="stat-label">Total Petugas</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-box">
                        <div class="stat-value counter-animate" data-target="<?= htmlspecialchars($stats['total_kunjungan']) ?>">0</div>
                        <div class="stat-label">Total Kunjungan</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="section-padding" style="background: var(--bg-body);">
        <div class="container reveal">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <div class="section-badge"><i class="bi bi-question-circle-fill"></i> FAQ</div>
                        <h2 class="section-title">Pertanyaan <span class="highlight">Umum</span></h2>
                    </div>

                    <div class="faq-container">
                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(this)">
                                <span>Apa itu TaniPantau?</span>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                TaniPantau adalah sistem informasi berbasis web yang dirancang untuk memonitoring lahan pertanian, mencatat data petani, dan mendokumentasikan kunjungan lapangan oleh petugas pertanian.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(this)">
                                <span>Siapa yang dapat menggunakan sistem ini?</span>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                Masyarakat umum dapat melihat data lahan secara publik. Namun untuk menambah atau mengubah data, Anda harus memiliki akun Admin atau Petugas Lapangan.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(this)">
                                <span>Bagaimana cara menambahkan lahan baru?</span>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                Fitur penambahan lahan hanya tersedia di Dashboard Admin. Jika Anda adalah petugas, silakan login ke sistem dan pilih menu "Data Lahan" > "Tambah Lahan Baru".
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kontak -->
    <section id="kontak" class="section-padding bg-white">
        <div class="container reveal">
            <div class="text-center mb-5">
                <div class="section-badge"><i class="bi bi-headset"></i> Hubungi Kami</div>
                <h2 class="section-title">Punya <span class="highlight">Pertanyaan?</span></h2>
                <p class="text-muted">Silakan hubungi kami melalui kanal berikut untuk bantuan atau kerjasama.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon" style="background: rgba(13, 100, 67, 0.1); color: var(--success);">
                            <i class="bi bi-envelope-paper-fill"></i>
                        </div>
                        <h5>Email</h5>
                        <p class="text-muted mt-2 mb-0">info@tanipantau.id</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--info);">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <h5>Telepon</h5>
                        <p class="text-muted mt-2 mb-0">+62 812-3456-7890</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h5>Alamat</h5>
                        <p class="text-muted mt-2 mb-0">Kota Malang, Jawa Timur</p>
                    </div>
                </div>
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
                    <a href="#beranda" class="footer-link">Beranda</a>
                    <a href="#fitur" class="footer-link">Fitur</a>
                    <a href="#statistik" class="footer-link">Statistik</a>
                    <a href="#faq" class="footer-link">FAQ</a>
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

    <!-- Scripts -->
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
        mobileBtn.addEventListener('click', () => {
            navLinks.classList.toggle('show');
            const icon = mobileBtn.querySelector('i');
            if(navLinks.classList.contains('show')) {
                icon.classList.replace('bi-list', 'bi-x-lg');
            } else {
                icon.classList.replace('bi-x-lg', 'bi-list');
            }
        });

        // Scroll Animations (Intersection Observer)
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.15
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.reveal').forEach(el => {
            observer.observe(el);
        });

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    if(navLinks.classList.contains('show')) {
                        navLinks.classList.remove('show');
                        mobileBtn.querySelector('i').classList.replace('bi-x-lg', 'bi-list');
                    }
                    
                    const headerOffset = 80;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: "smooth"
                    });
                }
            });
        });

        // FAQ Toggle
        function toggleFaq(btn) {
            const item = btn.parentElement;
            const allItems = document.querySelectorAll('.faq-item');
            
            allItems.forEach(el => {
                if (el !== item) el.classList.remove('active');
            });
            
            item.classList.toggle('active');
        }

        // Animated Counters
        const counters = document.querySelectorAll('.counter-animate');
        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;
                    const finalValue = parseInt(target.getAttribute('data-target'));
                    const duration = 2000; // 2 seconds
                    const frameRate = 30; // ms per frame
                    const totalFrames = Math.round(duration / frameRate);
                    let currentFrame = 0;

                    const counterInterval = setInterval(() => {
                        currentFrame++;
                        const progress = currentFrame / totalFrames;
                        // Use easeOutQuad for smoother animation
                        const easeProgress = progress * (2 - progress);
                        const currentValue = Math.round(finalValue * easeProgress);

                        // Format with thousands separator
                        target.innerText = new Intl.NumberFormat('id-ID').format(currentValue);

                        if (currentFrame === totalFrames) {
                            clearInterval(counterInterval);
                            target.innerText = new Intl.NumberFormat('id-ID').format(finalValue);
                        }
                    }, frameRate);

                    observer.unobserve(target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => {
            counterObserver.observe(counter);
        });
    </script>
</body>
</html>
