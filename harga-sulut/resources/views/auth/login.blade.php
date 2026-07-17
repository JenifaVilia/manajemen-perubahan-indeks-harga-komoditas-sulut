<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIPERHARGA Sulut — Sistem Manajemen Perubahan Harga Komoditas Provinsi Sulawesi Utara">
    <title>Login — SIPERHARGA Sulut</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { background: var(--color-surface); min-height: 100vh; display: flex; align-items: center; justify-content: center; }

        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 480px;
            min-height: 100vh;
            width: 100%;
        }

        /* Left Panel — Branding */
        .login-brand {
            background: var(--color-inverse-surface);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 4rem;
            position: relative;
            overflow: hidden;
        }

        .login-brand::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(0,102,153,0.35) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-brand::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(254,203,0,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .brand-logo-icon {
            width: 52px; height: 52px;
            background: var(--color-primary-container);
            border-radius: var(--radius-xl);
            display: flex; align-items: center; justify-content: center;
        }

        .brand-logo-text .brand-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.01em;
        }

        .brand-logo-text .brand-sub {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.45);
        }

        .brand-headline {
            font-size: 2.5rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 1.25rem;
        }

        .brand-headline span { color: var(--color-secondary-container); }

        .brand-desc {
            font-size: 1rem;
            color: rgba(255,255,255,0.55);
            line-height: 1.65;
            max-width: 420px;
            margin-bottom: 2.5rem;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            color: rgba(255,255,255,0.7);
        }

        .brand-feature-icon {
            width: 32px; height: 32px;
            background: rgba(255,255,255,0.08);
            border-radius: var(--radius-lg);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .brand-feature-icon svg { width: 16px; height: 16px; color: var(--color-inverse-primary); }

        .brand-footer {
            margin-top: auto;
            padding-top: 2rem;
            font-size: 0.6875rem;
            color: rgba(255,255,255,0.25);
        }

        /* Right Panel — Form */
        .login-panel {
            background: var(--color-surface-white);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2.5rem 3rem;
            border-left: 1px solid var(--color-outline-variant);
        }

        .login-header {
            margin-bottom: 2.25rem;
        }

        .login-header h1 {
            font-size: 1.625rem;
            font-weight: 800;
            color: var(--color-on-surface);
            letter-spacing: -0.02em;
            margin-bottom: 0.375rem;
        }

        .login-header p {
            font-size: 0.875rem;
            color: var(--color-on-surface-variant);
        }

        .form-group + .form-group { margin-top: 1.125rem; }

        .password-field { position: relative; }

        .password-toggle {
            position: absolute;
            right: 0.75rem; top: 50%;
            transform: translateY(-50%);
            width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
            color: var(--color-on-surface-variant);
            transition: var(--transition-fast);
            cursor: pointer;
        }
        .password-toggle:hover { color: var(--color-on-surface); }
        .password-toggle svg { width: 16px; height: 16px; }

        .login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 1rem 0 1.5rem;
            font-size: 0.8125rem;
        }

        .login-options label {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            cursor: pointer;
            color: var(--color-on-surface-variant);
        }

        .login-options input[type="checkbox"] {
            accent-color: var(--color-primary-container);
            width: 14px; height: 14px;
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            font-size: 0.9375rem;
            font-weight: 700;
            background: var(--color-primary-container);
            color: white;
            border: none;
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: var(--transition-base);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            background: var(--color-primary);
            box-shadow: 0 4px 14px 0 rgba(0,102,153,0.35);
            transform: translateY(-1px);
        }

        .btn-login:active { transform: translateY(0); }
        .btn-login svg { width: 18px; height: 18px; }

        .login-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
            color: var(--color-outline);
            font-size: 0.75rem;
        }

        .login-divider::before, .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--color-outline-variant);
        }

        .login-help {
            margin-top: 1.5rem;
            padding: 1rem;
            background: var(--color-surface-low);
            border-radius: var(--radius-lg);
            border: 1px solid var(--color-outline-variant);
        }

        .login-help p {
            font-size: 0.75rem;
            color: var(--color-on-surface-variant);
            line-height: 1.5;
        }

        .login-help a {
            color: var(--color-primary-container);
            font-weight: 600;
        }

        .login-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.6875rem;
            color: var(--color-on-surface-variant);
        }

        @media (max-width: 900px) {
            .login-wrapper { grid-template-columns: 1fr; }
            .login-brand { display: none; }
            .login-panel { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <!-- Left Brand Panel -->
    <div class="login-brand">
        <div class="brand-logo">
            <div class="brand-logo-icon">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#bfe0ff" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
            </div>
            <div class="brand-logo-text">
                <div class="brand-name">SIPERHARGA</div>
                <div class="brand-sub">Sistem Perubahan Harga Sulawesi Utara</div>
            </div>
        </div>

        <h2 class="brand-headline">
            Rekonsiliasi Harga<br>
            <span>Lebih Cepat,</span><br>
            Lebih Mudah.
        </h2>

        <p class="brand-desc">
            Platform terpusat untuk pengumpulan, penyimpanan, dan visualisasi data perubahan harga komoditas di 15 kabupaten/kota Sulawesi Utara — menggantikan proses rekonsiliasi manual yang memakan waktu.
        </p>

        <div class="brand-features">
            <div class="brand-feature">
                <div class="brand-feature-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                Dashboard visual & peta choropleth real-time
            </div>
            <div class="brand-feature">
                <div class="brand-feature-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                Input alasan & konfirmasi perubahan harga
            </div>
            <div class="brand-feature">
                <div class="brand-feature-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                Ekspor data ke Excel, PDF & CSV
            </div>
            <div class="brand-feature">
                <div class="brand-feature-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                Notifikasi otomatis per wilayah & periode
            </div>
        </div>

        <div class="brand-footer">
            © 2024 Badan Pusat Statistik Provinsi Sulawesi Utara<br>
            Divisi Distribusi Statistik
        </div>
    </div>

    <!-- Right Login Form Panel -->
    <div class="login-panel">
        <div class="login-header">
            <h1>Masuk ke SIPERHARGA</h1>
            <p>Gunakan akun yang diberikan oleh administrator.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error mb-4">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success mb-4">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">
                    Email
                    <span class="required">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    value="{{ old('email') }}"
                    placeholder="contoh@bps-sulut.go.id"
                    required
                    autofocus
                    autocomplete="email"
                >
                @error('email')
                    <div class="form-error">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">
                    Password
                    <span class="required">*</span>
                </label>
                <div class="password-field">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Masukkan password Anda"
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword()" title="Tampilkan/sembunyikan password">
                        <svg id="eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <div class="form-error">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="login-options">
                <label>
                    <input type="checkbox" name="remember" id="remember">
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-login" id="login-btn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Masuk
            </button>
        </form>

        <div class="login-help">
            <p>
                Lupa password atau belum punya akun? Hubungi administrator BPS Provinsi Sulawesi Utara di
                <a href="mailto:distribusi@bps-sulut.go.id">distribusi@bps-sulut.go.id</a>
            </p>
        </div>

        <div class="login-footer">
            SIPERHARGA Sulut v1.0 &bull; BPS Provinsi Sulawesi Utara
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
        } else {
            input.type = 'password';
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        }
    }

    // Loading state on submit
    document.getElementById('login-form').addEventListener('submit', function() {
        const btn = document.getElementById('login-btn');
        btn.innerHTML = `<svg style="animation:spin 1s linear infinite;width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Memproses...`;
        btn.disabled = true;
    });
    const style = document.createElement('style');
    style.textContent = '@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}';
    document.head.appendChild(style);
</script>
</body>
</html>
