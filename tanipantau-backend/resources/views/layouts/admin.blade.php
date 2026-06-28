<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - TaniPantau</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        /* Select2 Custom Styling untuk warna TaniPantau */
        .select2-container--default .select2-selection--single {
            border: 1px solid var(--border-light);
            border-radius: 10px;
            height: 46px;
            padding: 10px 16px;
            background-color: #F9FAFB;
            transition: var(--transition);
            display: flex;
            align-items: center;
        }
        
        .select2-container--default .select2-selection--single:focus {
            border-color: var(--primary);
            background-color: var(--white);
            box-shadow: 0 0 0 4px rgba(0, 111, 90, 0.1);
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-dark);
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            padding: 0;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px;
            right: 10px;
            top: 0;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: var(--text-muted) transparent transparent transparent;
        }
        
        .select2-dropdown {
            border: 1px solid var(--border-light);
            border-radius: 10px;
            box-shadow: var(--shadow-md);
            font-family: 'Poppins', sans-serif;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary);
            color: white;
        }
        
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: rgba(0, 111, 90, 0.1);
            color: var(--primary);
        }
        
        .select2-search--dropdown .select2-search__field {
            border: 1px solid var(--border-light);
            border-radius: 8px;
        }
        
        .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 111, 90, 0.1);
        }
        
        /* Select2 in Modal */
        .select2-container {
            width: 100% !important;
        }
    </style>
    @stack('styles')
    <style>
        :root {
            /* Design System Colors */
            --primary: #006F5A;
            --secondary: #3CBF99;
            --accent: #12D98A;
            --dark-bg: #002223;
            --surface-bg: #001B1B;
            --text-primary: #F7F7F6;
            --text-dark: #1F2937;
            --text-muted: #6B7280;
            --border-color: rgba(255, 255, 255, 0.08);
            --border-light: #E5E7EB;
            --success: #0D6443;
            --neutral: #A0C4BC;
            --bg-body: #F4F7F6;
            --white: #ffffff;
            
            /* Shadows & Transitions */
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 8px 24px rgba(0, 111, 90, 0.12);
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Layout */
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-body);
            color: var(--text-dark);
            overflow-x: hidden;
            font-weight: 400;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in { animation: fadeIn 0.4s ease forwards; }
        .animate-slide-up { animation: slideUp 0.5s ease forwards; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--dark-bg);
            color: var(--text-primary);
            z-index: 1040;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border-color);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-brand {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 1px solid var(--border-color);
            height: 80px;
            overflow: hidden;
            white-space: nowrap;
        }

        .sidebar-brand i {
            font-size: 28px;
            color: var(--accent);
            flex-shrink: 0;
            transition: var(--transition);
        }

        .sidebar-brand span {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--white);
            transition: var(--transition);
        }

        .sidebar.collapsed .sidebar-brand span {
            opacity: 0;
            width: 0;
            display: none;
        }

        .sidebar.collapsed .sidebar-brand {
            justify-content: center;
            padding: 24px 0;
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        .sidebar-nav .nav-section {
            padding: 12px 28px 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--neutral);
            font-weight: 600;
            transition: var(--transition);
            white-space: nowrap;
        }

        .sidebar.collapsed .nav-section {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        .sidebar-nav .nav-item {
            padding: 0 16px;
            margin-bottom: 4px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 16px;
            color: rgba(247, 247, 246, 0.7);
            text-decoration: none;
            border-radius: 12px;
            transition: var(--transition);
            font-size: 14px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }

        .sidebar-nav .nav-link:hover {
            background: var(--surface-bg);
            color: var(--white);
            transform: translateX(4px);
        }

        .sidebar-nav .nav-link.active {
            background: linear-gradient(90deg, rgba(0, 111, 90, 0.4) 0%, rgba(60, 191, 153, 0.1) 100%);
            color: var(--accent);
            font-weight: 600;
        }

        .sidebar-nav .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10%;
            height: 80%;
            width: 4px;
            background: var(--accent);
            border-radius: 0 4px 4px 0;
        }

        .sidebar-nav .nav-link i {
            font-size: 20px;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        .sidebar.collapsed .nav-item {
            padding: 0 12px;
        }
        
        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px 0;
        }
        .sidebar.collapsed .nav-link:hover {
            transform: none;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid var(--border-color);
            background: var(--surface-bg);
        }

        .sidebar-footer .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 12px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: rgba(247, 247, 246, 0.9);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            overflow: hidden;
            white-space: nowrap;
        }

        .sidebar-footer .logout-btn:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .sidebar-footer .logout-btn i {
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .sidebar.collapsed .logout-btn span {
            display: none;
        }
        .sidebar.collapsed .logout-btn {
            justify-content: center;
            padding: 12px 0;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* ===== TOPBAR ===== */
        .topbar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 0 32px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-light);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-dark);
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            background: var(--bg-body);
        }

        .sidebar-toggle:hover {
            background: var(--border-light);
            color: var(--primary);
        }

        .topbar-left h5 {
            margin: 0;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 20px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .today-date {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            background: var(--bg-body);
            padding: 8px 16px;
            border-radius: 20px;
        }

        .today-date i {
            color: var(--primary);
        }

        .profile-dropdown {
            position: relative;
            cursor: pointer;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 12px 6px 6px;
            border-radius: 50px;
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .user-info:hover {
            background: var(--bg-body);
            border-color: var(--border-light);
        }

        .user-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
            font-size: 16px;
            box-shadow: var(--shadow-sm);
        }

        .user-details {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .user-role {
            font-size: 11px;
            font-weight: 500;
            color: var(--primary);
            text-transform: capitalize;
        }
        
        .dropdown-menu-custom {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            width: 200px;
            padding: 8px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition);
            border: 1px solid var(--border-light);
        }
        
        .profile-dropdown.show .dropdown-menu-custom {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: var(--text-dark);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .dropdown-item-custom:hover {
            background: var(--bg-body);
            color: var(--primary);
        }
        
        .dropdown-item-custom i {
            font-size: 16px;
            color: var(--text-muted);
        }
        .dropdown-item-custom:hover i {
            color: var(--primary);
        }
        
        .dropdown-divider {
            margin: 8px 0;
            border-top: 1px solid var(--border-light);
        }

        /* ===== CONTENT AREA ===== */
        .content-area {
            padding: 32px;
            flex: 1;
        }

        /* ===== CARDS ===== */
        .card {
            border: 1px solid var(--border-light);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            background: var(--white);
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
        }

        /* ===== BUTTONS ===== */
        .btn-primary-custom {
            background: var(--primary);
            border: 1px solid var(--primary);
            color: var(--white);
            border-radius: 10px;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary-custom:hover {
            background: #005a49;
            border-color: #005a49;
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 111, 90, 0.2);
        }

        .btn-success-custom {
            background: var(--secondary);
            border: 1px solid var(--secondary);
            color: var(--dark-bg);
            border-radius: 10px;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-success-custom:hover {
            background: #2ea886;
            border-color: #2ea886;
            color: var(--dark-bg);
        }

        /* ===== BADGES ===== */
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* ===== TABLE ===== */
        .table-responsive {
            border-radius: 12px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #F9FAFB;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px;
            border-bottom: 2px solid var(--border-light);
            white-space: nowrap;
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            font-size: 14px;
            color: var(--text-dark);
            border-bottom: 1px solid var(--border-light);
        }

        .table tbody tr:hover {
            background: #F9FAFB;
        }

        /* ===== MODAL ===== */
        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        /* Fix Select2 dropdown in modal — dropdown turun + modal tetap scroll */
        .select2-container--open {
            z-index: 9999 !important;
        }
        .modal-body {
            overflow-y: auto;
            max-height: 60vh;
        }
        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
        }
        .select2-container--default .select2-dropdown {
            top: 100% !important;
            bottom: auto !important;
        }

        .modal-header {
            background: var(--primary);
            color: var(--white);
            border-radius: 20px 20px 0 0;
            padding: 20px 28px;
            border-bottom: none;
        }
        
        .modal-header .modal-title {
            font-weight: 600;
            font-size: 18px;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
            transition: var(--transition);
        }
        .modal-header .btn-close:hover {
            opacity: 1;
        }
        
        .modal-body {
            padding: 28px;
        }
        
        .modal-footer {
            padding: 20px 28px;
            border-top: 1px solid var(--border-light);
        }

        /* ===== FORM ===== */
        .form-label {
            font-weight: 500;
            font-size: 13px;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-light);
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 14px;
            transition: var(--transition);
            background-color: #F9FAFB;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            background-color: var(--white);
            box-shadow: 0 0 0 4px rgba(0, 111, 90, 0.1);
        }

        /* ===== ALERT ===== */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
        }
        
        .alert-success {
            background: #ECFDF5;
            color: #065F46;
            border-left: 4px solid var(--secondary);
        }

        .alert-danger {
            background: #FEF2F2;
            color: #991B1B;
            border-left: 4px solid #EF4444;
        }

        /* ===== RESPONSIVE ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(2px);
            z-index: 1035;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .sidebar-overlay.show {
                display: block;
                opacity: 1;
            }
            .main-content {
                margin-left: 0 !important;
            }
            .topbar {
                padding: 0 20px;
            }
            .content-area {
                padding: 20px;
            }
            .today-date {
                display: none;
            }
        }
        
        @media (max-width: 576px) {
            .user-name { display: none; }
            .topbar-right { gap: 12px; }
            .topbar-left h5 { font-size: 16px; }
            .content-area { padding: 16px; }
            .stat-value { font-size: 22px; }
            .stat-card { padding: 16px; min-height: 110px; }
            .table thead th { font-size: 11px; padding: 12px 8px; }
            .table tbody td { padding: 12px 8px; font-size: 13px; }
            .modal-body { padding: 20px; }
        }

        @media (max-width: 375px) {
            .topbar { padding: 0 12px; }
            .content-area { padding: 12px; }
            .stat-value { font-size: 20px; }
            .stat-label { font-size: 11px; }
            .stat-icon { width: 36px; height: 36px; font-size: 18px; }
        }
        
        /* Utility */
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-tree-fill"></i>
            <span>TaniPantau</span>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section">Menu Utama</div>

            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.petani.index') }}" class="nav-link {{ request()->routeIs('admin.petani.*') ? 'active' : '' }}" title="Data Petani">
                    <i class="bi bi-people-fill"></i>
                    <span>Data Petani</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.lahan.index') }}" class="nav-link {{ request()->routeIs('admin.lahan.*') ? 'active' : '' }}" title="Data Lahan">
                    <i class="bi bi-map-fill"></i>
                    <span>Data Lahan</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.kunjungan.index') }}" class="nav-link {{ request()->routeIs('admin.kunjungan.*') ? 'active' : '' }}" title="Kunjungan">
                    <i class="bi bi-clipboard2-check-fill"></i>
                    <span>Kunjungan</span>
                </a>
            </div>

            @if(in_array(Auth::user()->role, ['admin', 'manajer']))
            <div class="nav-item">
                <a href="{{ route('admin.laporan.index') }}" class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}" title="Laporan">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span>Laporan</span>
                </a>
            </div>
            @endif

            <div class="nav-section mt-3">Sistem & Akun</div>

            @if(Auth::user()->role === 'admin')
            <div class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" title="Kelola User">
                    <i class="bi bi-people"></i>
                    <span>Kelola User</span>
                </a>
            </div>
            @endif

            <div class="nav-item">
                <a href="{{ route('admin.profile.edit') }}" class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" title="Profil Saya">
                    <i class="bi bi-person-circle"></i>
                    <span>Profil Saya</span>
                </a>
            </div>
        </div>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn" onclick="return confirm('Yakin ingin keluar?')" title="Keluar">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h5>@yield('page-title', 'Dashboard')</h5>
            </div>
            
            <div class="topbar-right">
                <div class="today-date d-none d-md-flex">
                    <i class="bi bi-calendar-event"></i>
                    {{ now()->translatedFormat('d F Y') }}
                </div>
                
                <div class="profile-dropdown" id="profileDropdown">
                    <div class="user-info">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="user-details d-none d-sm-flex">
                            <span class="user-name">{{ Auth::user()->name }}</span>
                            <span class="user-role">{{ Auth::user()->role }}</span>
                        </div>
                        <i class="bi bi-chevron-down ms-1 text-muted" style="font-size: 12px;"></i>
                    </div>
                    
                    <div class="dropdown-menu-custom">
                        <a href="{{ route('admin.profile.edit') }}" class="dropdown-item-custom">
                            <i class="bi bi-person"></i> Profil Saya
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item-custom text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right text-danger"></i> Keluar
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area animate-fade-in">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show animate-slide-up" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show animate-slide-up" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show animate-slide-up" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                        <div>
                            <strong>Terdapat kesalahan:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Modals -->
    @stack('modals')

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle logic
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            // Check window size
            const isDesktop = window.innerWidth >= 992;
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', () => {
                    if (window.innerWidth >= 992) {
                        // Desktop: toggle collapsed state
                        sidebar.classList.toggle('collapsed');
                    } else {
                        // Mobile: toggle show state
                        sidebar.classList.toggle('show');
                        sidebarOverlay.classList.toggle('show');
                    }
                });
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', () => {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }
            
            // Profile dropdown toggle
            const profileDropdown = document.getElementById('profileDropdown');
            if (profileDropdown) {
                profileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    this.classList.toggle('show');
                });
                
                // Close when clicking outside
                document.addEventListener('click', function() {
                    profileDropdown.classList.remove('show');
                });
            }
            
            // Init tooltips if bootstrap is loaded
            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
