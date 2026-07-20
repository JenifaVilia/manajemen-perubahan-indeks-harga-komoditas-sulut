<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Manajemen Perubahan Harga Komoditas SULUT — Platform pemantauan dan rekonsiliasi data perubahan harga komoditas di Sulawesi Utara.">
    <title>Login — Sistem Manajemen Perubahan Harga Komoditas SULUT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #1e4670;
            --color-primary-hover: #123150;
            --color-bg: #f3f4f6;
            --color-text-main: #0f172a;
            --color-text-muted: #475569;
            --color-border: #cbd5e1;
            --color-brand-bg: #f8fafc;
            --radius-card: 16px;
            --radius-input: 8px;
            --font-base: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--color-bg);
            font-family: var(--font-base);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-container {
            background: #ffffff;
            border-radius: var(--radius-card);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 860px;
            display: grid;
            grid-template-columns: 1.15fr 1.35fr;
            overflow: hidden;
            min-height: 480px;
        }

        /* Left Side: Brand Panel */
        .login-brand {
            background-color: var(--color-brand-bg);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-right: 1px solid #e2e8f0;
            position: relative;
        }

        .brand-logo-container {
            width: 100px;
            height: 100px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo-container svg {
            width: 100%;
            height: 100%;
        }

        .brand-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .brand-subtitle {
            font-size: 0.8125rem;
            color: var(--color-text-muted);
            line-height: 1.5;
            max-width: 250px;
            margin-bottom: 1.5rem;
        }

        .brand-divider {
            width: 50px;
            height: 3px;
            background: #cbd5e1;
            border-radius: 999px;
        }

        /* Right Side: Form Panel */
        .login-form-container {
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 1.75rem;
        }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-text-main);
            letter-spacing: -0.02em;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.375rem;
        }

        .form-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--color-text-main);
        }

        .forgot-link {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #0284c7;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .forgot-link:hover {
            color: #0369a1;
            text-decoration: underline;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 0.875rem;
            color: #94a3b8;
            width: 18px;
            height: 18px;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-input {
            width: 100%;
            padding: 0.6875rem 1rem 0.6875rem 2.625rem;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-input);
            font-size: 0.875rem;
            color: var(--color-text-main);
            background: #ffffff;
            transition: all 0.15s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .form-input.is-invalid {
            border-color: #ef4444;
        }

        .form-input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .form-error {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 0.375rem;
            font-weight: 500;
        }

        .form-error svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        /* Password Specific */
        .password-toggle {
            position: absolute;
            right: 0.875rem;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            transition: color 0.15s ease;
        }

        .password-toggle:hover {
            color: var(--color-text-muted);
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
        }

        .remember-wrapper {
            margin: 0.75rem 0 1.25rem;
            display: flex;
            align-items: center;
        }

        .remember-wrapper label {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.8125rem;
            color: var(--color-text-muted);
            cursor: pointer;
        }

        .remember-wrapper input[type="checkbox"] {
            accent-color: var(--color-primary);
            width: 14px;
            height: 14px;
        }

        /* Button Styling */
        .btn-submit {
            width: 100%;
            padding: 0.8125rem;
            background-color: var(--color-primary);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-input);
            font-size: 0.9375rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background-color: var(--color-primary-hover);
            box-shadow: 0 4px 12px rgba(30, 70, 112, 0.15);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit svg {
            width: 18px;
            height: 18px;
        }

        .btn-submit:disabled {
            background-color: #94a3b8;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Alert Box */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: var(--radius-input);
            font-size: 0.8125rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            line-height: 1.4;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-error svg {
            width: 16px;
            height: 16px;
            color: #dc2626;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .alert-success svg {
            width: 16px;
            height: 16px;
            color: #16a34a;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 440px;
            }
            .login-brand {
                border-right: none;
                border-bottom: 1px solid #e2e8f0;
                padding: 2.5rem 1.5rem;
            }
            .login-form-container {
                padding: 2.5rem 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="login-container">
    <!-- Left brand info panel -->
    <div class="login-brand">
        <div class="brand-logo-container">
            <svg viewBox="0 0 1381 1070" fill="none">
                <g id="#0093ddff">
                    <path fill="#0093dd" opacity="1.00" d=" M 268.54 0.00 L 452.70 0.00 C 458.67 1.27 463.72 6.05 465.05 12.04 C 466.56 17.59 464.60 23.18 463.41 28.58 C 452.39 76.05 441.24 123.49 430.30 170.98 C 427.74 181.29 427.68 182.14 427.48 182.98 C 418.90 219.60 410.40 256.25 401.86 292.88 C 401.48 296.95 400.32 300.88 399.50 304.88 C 398.27 310.80 399.78 317.53 404.38 321.67 C 407.99 325.10 413.12 326.24 417.97 326.12 C 487.98 326.11 557.99 326.13 628.01 326.11 C 632.16 326.12 636.52 325.88 640.42 327.60 C 645.47 329.67 649.14 334.52 649.98 339.90 C 651.09 346.05 648.54 352.01 647.42 357.97 C 624.44 456.64 601.47 555.31 578.48 653.98 C 576.29 664.53 567.87 673.43 557.52 676.34 C 552.78 677.66 547.81 677.22 542.95 677.29 C 452.48 677.32 362.02 677.23 271.56 677.34 C 271.30 674.35 271.45 671.35 271.42 668.36 C 296.28 662.80 321.23 654.42 341.56 638.60 C 357.92 626.10 370.17 608.19 375.28 588.20 C 392.02 516.17 408.84 444.15 425.58 372.12 C 427.20 365.27 424.77 357.25 418.60 353.46 C 414.00 350.40 408.28 350.64 402.99 350.69 C 352.68 350.69 302.36 350.71 252.05 350.69 C 250.42 350.80 248.68 350.37 247.14 350.97 C 218.52 375.64 191.32 402.03 166.76 430.77 C 142.32 459.05 120.47 489.71 103.05 522.82 C 82.65 561.72 68.42 604.77 67.88 649.01 C 67.08 691.24 80.52 733.54 105.68 767.48 C 128.28 798.38 159.11 822.45 192.50 840.75 C 228.62 860.29 267.74 873.94 307.70 883.18 C 307.61 883.95 307.44 885.47 307.36 886.24 C 303.67 901.39 300.26 916.60 296.69 931.78 C 270.46 928.24 244.49 922.78 218.95 915.87 C 183.45 906.01 148.70 892.78 116.80 874.21 C 84.15 855.26 54.34 830.33 33.23 798.78 C 9.12 763.35 -2.13 719.69 0.97 677.01 C 3.73 633.84 19.00 592.37 39.77 554.74 C 59.03 519.99 83.07 488.06 109.61 458.55 C 145.49 418.59 186.12 383.14 228.85 350.71 C 216.32 350.64 203.78 350.77 191.25 350.65 C 185.13 350.64 178.95 347.30 176.28 341.68 C 173.78 336.84 174.15 331.14 175.59 326.05 C 199.21 224.73 222.77 123.38 246.41 22.06 C 248.88 11.29 257.92 2.67 268.54 0.00 Z"/>
                    <path fill="#0093dd" opacity="1.00" d=" M 1171.91 697.88 C 1177.26 694.71 1182.20 690.76 1187.85 688.12 C 1170.32 701.42 1151.85 713.48 1133.40 725.47 C 1034.34 788.59 926.47 837.33 814.86 873.60 C 717.32 905.10 616.48 926.94 514.32 936.07 C 514.45 935.27 514.71 933.68 514.83 932.89 C 517.40 922.46 519.72 911.98 522.20 901.54 C 580.36 899.19 638.32 892.45 695.54 881.75 C 783.15 865.40 869.07 840.32 952.08 807.92 C 1028.46 777.95 1102.40 741.54 1171.91 697.88 Z"/>
                </g>
                <g id="#eb891bff">
                    <path fill="#eb891b" opacity="1.00" d=" M 785.57 0.00 L 1068.06 0.00 C 1068.03 2.86 1068.18 5.72 1067.89 8.57 C 1052.11 12.04 1036.56 16.91 1022.04 24.06 C 1007.64 30.98 994.41 40.67 984.26 53.08 C 1042.25 54.51 1100.35 60.87 1156.55 75.57 C 1198.24 86.60 1239.07 102.22 1275.62 125.35 C 1307.51 145.50 1336.08 171.95 1355.17 204.74 C 1369.87 229.54 1378.43 257.73 1381.00 286.40 L 1381.00 317.74 C 1378.07 357.09 1365.07 395.10 1346.90 429.93 C 1328.04 466.12 1303.75 499.24 1276.88 529.86 C 1226.12 587.23 1166.19 635.89 1102.60 678.34 C 1101.20 679.28 1099.83 680.32 1098.15 680.63 C 1098.53 679.94 1099.05 679.36 1099.69 678.90 C 1145.37 644.44 1188.35 606.14 1225.97 562.95 C 1252.50 532.29 1276.41 499.14 1294.91 462.99 C 1313.46 426.69 1326.55 386.97 1328.32 346.02 C 1329.49 319.20 1325.54 292.06 1315.89 266.97 C 1302.85 232.10 1279.21 201.84 1250.56 178.36 C 1219.90 153.05 1184.00 134.80 1146.82 121.16 C 1089.15 100.30 1028.11 90.18 967.08 86.03 C 954.61 139.52 942.17 193.02 929.70 246.52 C 925.14 266.62 919.99 286.61 916.26 306.87 C 915.44 313.39 918.36 320.49 924.26 323.74 C 928.68 326.41 934.02 326.14 939.00 326.12 C 1008.99 326.11 1078.98 326.11 1148.97 326.12 C 1153.98 325.99 1159.27 327.48 1162.76 331.25 C 1167.30 335.90 1168.19 343.08 1166.52 349.16 C 1143.08 449.83 1119.63 550.50 1096.19 651.17 C 1094.33 665.35 1081.61 677.67 1067.01 677.29 C 1009.35 677.29 951.70 677.31 894.05 677.28 C 888.96 677.37 883.64 675.61 880.26 671.67 C 876.19 667.14 875.35 660.51 876.80 654.77 C 898.74 560.56 920.70 466.36 942.63 372.15 C 944.25 365.28 941.81 357.23 935.61 353.44 C 930.71 350.19 924.62 350.70 919.04 350.70 C 849.36 350.70 779.68 350.70 710.00 350.69 C 705.01 350.83 699.76 349.43 696.23 345.74 C 691.65 341.22 690.58 334.15 692.16 328.10 C 710.19 250.66 728.25 173.23 746.26 95.78 C 637.24 111.45 530.22 140.39 427.65 180.43 Z"/>
                </g>
                <g id="#68b92eff">
                    <path fill="#68b92e" opacity="1.00" d=" M 675.58 414.96 C 678.25 401.99 690.58 391.45 703.97 391.71 C 761.62 391.71 819.26 391.70 876.91 391.72 C 882.95 391.50 889.29 394.18 892.44 399.51 C 895.30 404.12 895.46 409.90 894.11 415.03 C 870.41 516.67 846.77 618.33 823.07 719.97 C 820.29 732.71 808.17 743.05 794.98 742.89 C 726.64 742.91 658.30 742.89 589.96 742.90 C 584.10 742.98 578.00 742.35 572.43 744.58 C 563.55 747.98 556.29 755.73 554.03 765.04 C 543.39 810.53 532.87 856.05 522.20 901.54 C 519.72 911.98 517.40 922.46 514.83 932.89 C 506.41 970.02 497.58 1007.08 489.02 1044.19 C 486.97 1057.70 474.89 1069.35 460.99 1069.47 C 403.02 1069.53 345.05 1069.49 287.08 1069.49 C 281.97 1069.58 276.63 1067.80 273.25 1063.83 C 269.19 1059.28 268.37 1052.63 269.85 1046.88 C 278.80 1008.51 287.67 970.13 296.69 931.78 C 300.26 916.60 303.67 901.39 307.36 886.24 C 318.34 838.07 329.74 790.00 340.88 741.87 C 343.45 728.93 355.54 718.27 368.89 718.32 C 439.92 718.28 510.96 718.32 581.99 718.30 C 592.35 718.50 602.13 712.15 607.19 703.29 C 609.50 699.38 610.39 694.87 611.40 690.50 C 632.80 598.66 654.18 506.81 675.58 414.96 Z"/>
                </g>
            </svg>
        </div>
        <div class="brand-title">BPS Provinsi Sulawesi Utara</div>
        <div class="brand-subtitle">Sistem Manajemen Perubahan Harga Komoditas SULUT</div>
        <div class="brand-divider"></div>
    </div>

    <!-- Right form panel -->
    <div class="login-form-container">
        <div class="form-header">
            <h2>Selamat Datang</h2>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
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
            <div class="alert alert-success">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf

            <div class="form-group">
                <div class="form-label-row">
                    <label class="form-label" for="email">Email</label>
                </div>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email Anda"
                        required
                        autofocus
                        autocomplete="email"
                    >
                </div>
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
                <div class="form-label-row">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <a href="mailto:distribusi@bps-sulut.go.id" class="forgot-link" title="Hubungi administrator BPS Provinsi Sulawesi Utara untuk reset password">Lupa kata sandi?</a>
                </div>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Masukkan kata sandi"
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword()" title="Tampilkan/sembunyikan kata sandi">
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

            <div class="remember-wrapper">
                <label for="remember">
                    <input type="checkbox" name="remember" id="remember">
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-submit" id="login-btn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Masuk
            </button>
        </form>
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
