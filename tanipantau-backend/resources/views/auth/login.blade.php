<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TaniPantau</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #006F5A;
            --secondary: #3CBF99;
            --accent: #12D98A;
            --dark-bg: #002223;
            --text-dark: #1F2937;
            --text-muted: #6B7280;
            --bg-body: #F4F7F6;
            --white: #ffffff;
            --shadow-lg: 0 8px 24px rgba(0, 111, 90, 0.12);
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-body) 0%, #e2ece9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
        }

        .wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
            width: 100%;
        }

        .left-panel {
            background: #003d30;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            width: 280px; height: 280px;
            border-radius: 50%;
            border: 40px solid rgba(18,217,138,0.08);
            top: -80px; left: -80px;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            border: 30px solid rgba(18,217,138,0.06);
            bottom: 40px; right: -60px;
        }

        .brand { position: relative; z-index: 1; }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(18,217,138,0.15);
            border: 0.5px solid rgba(18,217,138,0.3);
            border-radius: 20px;
            padding: 5px 14px;
            margin-bottom: 20px;
        }
        .brand-badge i { color: #12D98A; font-size: 14px; }
        .brand-badge span { color: #12D98A; font-size: 12px; font-weight: 500; letter-spacing: 0.5px; }
        .brand-name { color: #fff; font-size: 28px; font-weight: 700; margin-bottom: 6px; }
        .brand-desc { color: rgba(255,255,255,0.55); font-size: 13px; line-height: 1.6; max-width: 240px; }

        .features { position: relative; z-index: 1; display: flex; flex-direction: column; gap: 14px; }
        .feat-item { display: flex; align-items: center; gap: 12px; }
        .feat-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: rgba(18,217,138,0.12);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .feat-icon i { color: #12D98A; font-size: 16px; }
        .feat-text { color: rgba(255,255,255,0.75); font-size: 13px; }
        .feat-text strong { color: #fff; display: block; font-size: 13px; font-weight: 500; }
        .left-footer { position: relative; z-index: 1; color: rgba(255,255,255,0.3); font-size: 11px; }

        .right-panel {
            background: var(--white);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .form-container {
            max-width: 420px;
            width: 100%;
        }
        .form-heading { margin-bottom: 28px; }
        .form-heading h2 { color: var(--text-dark); font-size: 20px; font-weight: 600; margin-bottom: 4px; }
        .form-heading p { color: var(--text-muted); font-size: 13px; }

        .field { margin-bottom: 18px; }
        .field label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .input-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 0 14px;
            height: 44px;
            background: #f9fafb;
            transition: border-color 0.2s;
        }
        .input-wrap:focus-within { border-color: var(--primary); background: var(--white); }
        .input-wrap i { color: var(--text-muted); font-size: 16px; flex-shrink: 0; }
        .input-wrap input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 14px;
            color: var(--text-dark);
            outline: none;
            font-family: inherit;
        }
        .input-wrap input::placeholder { color: #d1d5db; }

        .row-opts {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }
        .check-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: var(--text-muted);
            cursor: pointer;
        }
        .check-label input[type=checkbox] { accent-color: var(--primary); width: 15px; height: 15px; }

        .btn-submit {
            width: 100%;
            height: 44px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-submit:hover { background: #005a48; }
        .btn-submit:active { transform: scale(0.99); }
        .btn-submit i { font-size: 16px; }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0 18px;
        }
        .divider hr { flex: 1; border: none; border-top: 1px solid #e5e7eb; }
        .divider span { font-size: 12px; color: #9ca3af; }

        .version-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 11px;
            color: var(--text-muted);
        }
        .version-badge i { color: var(--accent); font-size: 12px; }

        .alert-box {
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }
        .alert-danger { background: #FEF2F2; color: #991B1B; border-left: 3px solid #DC2626; }
        .alert-success { background: #ECFDF5; color: #065F46; border-left: 3px solid #10B981; }

        .back-link {
            margin-top: 16px;
            text-align: center;
            font-size: 13px;
        }
        .back-link a {
            color: var(--text-muted);
            text-decoration: none;
        }
        .back-link a:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .wrap { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .right-panel { padding: 36px 28px; }
        }
    </style>
</head>
<body>

<div class="wrap">
    <!-- Left Panel — Branding -->
    <div class="left-panel">
        <div class="brand">
            <div class="brand-badge">
                <i class="bi bi-leaf-fill"></i>
                <span>TaniPantau v2.0</span>
            </div>
            <div class="brand-name">Selamat Datang</div>
            <p class="brand-desc">Platform manajemen pertanian modern untuk memantau, mengelola, dan meningkatkan produktivitas lahan Anda.</p>
        </div>

        <div class="features">
            <div class="feat-item">
                <div class="feat-icon"><i class="bi bi-bar-chart-fill"></i></div>
                <div class="feat-text">
                    <strong>Analitik Real-time</strong>
                    Pantau data lahan secara langsung
                </div>
            </div>
            <div class="feat-item">
                <div class="feat-icon"><i class="bi bi-shield-check"></i></div>
                <div class="feat-text">
                    <strong>Keamanan Terjamin</strong>
                    Data terenkripsi & terlindungi
                </div>
            </div>
            <div class="feat-item">
                <div class="feat-icon"><i class="bi bi-phone"></i></div>
                <div class="feat-text">
                    <strong>Multi-platform</strong>
                    Akses dari perangkat apa saja
                </div>
            </div>
        </div>

        <div class="left-footer">&copy; 2025 TaniPantau &middot; Sistem Pertanian Cerdas</div>
    </div>

    <!-- Right Panel — Login Form -->
    <div class="right-panel">
        <div class="form-container">
            <div class="form-heading">
                <h2>Masuk ke Akun Anda</h2>
                <p>Silakan masukkan kredensial Anda</p>
            </div>

            @if(session('error'))
                <div class="alert-box alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="alert-box alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label>Alamat Email</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required autofocus>
                    </div>
                </div>

                <div class="field">
                    <label>Password</label>
                    <div class="input-wrap" id="pw-wrap">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="pw-input" name="password" placeholder="Masukkan password" required>
                        <i class="bi bi-eye" id="pw-toggle" style="cursor:pointer; font-size:16px; color:var(--text-muted)" title="Tampilkan password" onclick="togglePw()"></i>
                    </div>
                </div>

                <div class="row-opts">
                    <label class="check-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk
                </button>
            </form>

            <div class="divider">
                <hr><span>versi sistem</span><hr>
            </div>

            <div style="text-align:center">
                <span class="version-badge">
                    <i class="bi bi-circle-fill"></i>
                    Sistem aktif &middot; TaniPantau
                </span>
            </div>

            <div class="back-link">
                <a href="{{ url('/') }}">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke halaman utama
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePw() {
    const inp = document.getElementById('pw-input');
    const ico = document.getElementById('pw-toggle');
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
    }
}
</script>

</body>
</html>