<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Manajemen Perubahan Harga Komoditas SULUT — Platform pemantauan dan rekonsiliasi data perubahan harga komoditas di Sulawesi Utara.">
    <title>@yield('title', 'Dashboard') — Sistem Manajemen Perubahan Harga Komoditas SULUT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay hidden" id="sidebar-overlay" onclick="closeSidebar()"></div>

<div class="app-shell">
    <!-- ============================================================
         SIDEBAR
    ============================================================ -->
    <aside class="sidebar" id="sidebar">
        <!-- Logo -->
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon" style="background: transparent; padding: 0; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px;">
                <svg viewBox="0 0 1381 1070" width="34" height="28" fill="none">
                    <g id="#0093ddff">
                        <path fill="#0093dd" opacity="1.00" d=" M 268.54 0.00 L 452.70 0.00 C 458.67 1.27 463.72 6.05 465.05 12.04 C 466.56 17.59 464.60 23.18 463.41 28.58 C 452.39 76.05 441.24 123.49 430.30 170.98 C 427.74 181.29 427.68 182.14 427.48 182.98 C 418.90 219.60 410.40 256.25 401.86 292.88 C 401.48 296.95 400.32 300.88 399.50 304.88 C 398.27 310.80 399.78 317.53 404.38 321.67 C 407.99 325.10 413.12 326.24 417.97 326.12 C 487.98 326.11 557.99 326.13 628.01 326.11 C 632.16 326.12 636.52 325.88 640.42 327.60 C 645.47 329.67 649.14 334.52 649.98 339.90 C 651.09 346.05 648.54 352.01 647.42 357.97 C 624.44 456.64 601.47 555.31 578.48 653.98 C 576.29 664.53 567.87 673.43 557.52 676.34 C 552.78 677.66 547.81 677.22 542.95 677.29 C 452.48 677.32 362.02 677.23 271.56 677.34 C 271.30 674.35 271.45 671.35 271.42 668.36 C 296.28 662.80 321.23 654.42 341.56 638.60 C 357.92 626.10 370.17 608.19 375.28 588.20 C 392.02 516.17 408.84 444.15 425.58 372.12 C 427.20 365.27 424.77 357.25 418.60 353.46 C 414.00 350.40 408.28 350.64 402.99 350.69 C 352.68 350.69 302.36 350.71 252.05 350.69 C 250.42 350.80 248.68 350.37 247.14 350.97 C 218.52 375.64 191.32 402.03 166.76 430.77 C 142.32 459.05 120.47 489.71 103.05 522.82 C 82.65 561.72 68.42 604.77 67.88 649.01 C 67.08 691.24 80.52 733.54 105.68 767.48 C 128.28 798.38 159.11 822.45 192.50 840.75 C 228.62 860.29 267.74 873.94 307.70 883.18 C 307.61 883.95 307.44 885.47 307.36 886.24 C 303.67 901.39 300.26 916.60 296.69 931.78 C 270.46 928.24 244.49 922.78 218.95 915.87 C 183.45 906.01 148.70 892.78 116.80 874.21 C 84.15 855.26 54.34 830.33 33.23 798.78 C 9.12 763.35 -2.13 719.69 0.97 677.01 C 3.73 633.84 19.00 592.37 39.77 554.74 C 59.03 519.99 83.07 488.06 109.61 458.55 C 145.49 418.59 186.12 383.14 228.85 350.71 C 216.32 350.64 203.78 350.77 191.25 350.65 C 185.13 350.64 178.95 347.30 176.28 341.68 C 173.78 336.84 174.15 331.14 175.59 326.05 C 199.21 224.73 222.77 123.38 246.41 22.06 C 248.88 11.29 257.92 2.67 268.54 0.00 Z"/>
                        <path fill="#0093dd" opacity="1.00" d=" M 1171.91 697.88 C 1177.26 694.71 1182.20 690.76 1187.85 688.12 C 1170.32 701.42 1151.85 713.48 1133.40 725.47 C 1034.34 788.59 926.47 837.33 814.86 873.60 C 717.32 905.10 616.48 926.94 514.32 936.07 C 514.45 935.27 514.71 933.68 514.83 932.89 C 517.40 922.46 519.72 911.98 522.20 901.54 C 580.36 899.19 638.32 892.45 695.54 881.75 C 783.15 865.40 869.07 840.32 952.08 807.92 C 1028.46 777.95 1102.40 741.54 1171.91 697.88 Z"/>
                    </g>
                    <g id="#eb891bff">
                        <path fill="#eb891b" opacity="1.00" d=" M 785.57 0.00 L 1068.06 0.00 C 1068.03 2.86 1068.18 5.72 1067.89 8.57 C 1052.11 12.04 1036.56 16.91 1022.04 24.06 C 1007.64 30.98 994.41 40.67 984.26 53.08 C 1042.25 54.51 1100.35 60.87 1156.55 75.57 C 1198.24 86.60 1239.07 102.22 1275.62 125.35 C 1307.51 145.50 1336.08 171.95 1355.17 204.74 C 1369.87 229.54 1378.43 257.73 1381.00 286.40 L 1381.00 317.74 C 1378.07 357.09 1365.07 395.10 1346.90 429.93 C 1328.04 466.12 1303.75 499.24 1276.88 529.86 C 1226.12 587.23 1166.19 635.89 1102.60 678.34 C 1101.20 679.28 1099.83 680.32 1098.15 680.63 C 1098.53 679.94 1099.05 679.36 1099.69 678.90 C 1145.37 644.44 1188.35 606.14 1225.97 562.95 C 1252.50 532.29 1276.41 499.14 1294.91 462.99 C 1313.46 426.69 1326.55 386.97 1328.32 346.02 C 1329.49 319.20 1325.54 292.06 1315.89 266.97 C 1302.85 232.10 1279.21 201.84 1250.56 178.36 C 1219.90 153.05 1184.00 134.80 1146.82 121.16 C 1089.15 100.30 1028.11 90.18 967.08 86.03 C 954.61 139.52 942.17 193.02 929.70 246.52 C 925.14 266.62 919.99 286.61 916.26 306.87 C 915.44 313.39 918.36 320.49 924.26 323.74 C 928.68 326.41 934.02 326.14 939.00 326.12 C 1008.99 326.11 1078.98 326.11 1148.97 326.12 C 1153.98 325.99 1159.27 327.48 1162.76 331.25 C 1167.30 335.90 1168.19 343.08 1166.52 349.16 C 1143.08 449.83 1119.63 550.50 1096.19 651.17 C 1094.33 665.35 1081.61 677.67 1067.01 677.29 C 1009.35 677.29 951.70 677.31 894.05 677.28 C 888.96 677.37 883.64 675.61 880.26 671.67 C 876.19 667.14 875.35 660.51 876.80 654.77 C 898.74 560.56 920.70 466.36 942.63 372.15 C 944.25 365.28 941.81 357.23 935.61 353.44 C 930.71 350.19 924.62 350.70 919.04 350.70 C 849.36 350.70 779.68 350.70 710.00 350.69 C 705.01 350.83 699.76 349.43 696.23 345.74 C 691.65 341.22 690.58 334.15 692.16 328.10 C 710.19 250.66 728.25 173.23 746.26 95.78 C 637.24 111.45 530.22 140.39 427.65 180.43 L 426.05 180.99 Z"/>
                    </g>
                    <g id="#68b92eff">
                        <path fill="#68b92e" opacity="1.00" d=" M 675.58 414.96 C 678.25 401.99 690.58 391.45 703.97 391.71 C 761.62 391.71 819.26 391.70 876.91 391.72 C 882.95 391.50 889.29 394.18 892.44 399.51 C 895.30 404.12 895.46 409.90 894.11 415.03 C 870.41 516.67 846.77 618.33 823.07 719.97 C 820.29 732.71 808.17 743.05 794.98 742.89 C 726.64 742.91 658.30 742.89 589.96 742.90 C 584.10 742.98 578.00 742.35 572.43 744.58 C 563.55 747.98 556.29 755.73 554.03 765.04 C 543.39 810.53 532.87 856.05 522.20 901.54 C 519.72 911.98 517.40 922.46 514.83 932.89 C 506.41 970.02 497.58 1007.08 489.02 1044.19 C 486.97 1057.70 474.89 1069.35 460.99 1069.47 C 403.02 1069.53 345.05 1069.49 287.08 1069.49 C 281.97 1069.58 276.63 1067.80 273.25 1063.83 C 269.19 1059.28 268.37 1052.63 269.85 1046.88 C 278.80 1008.51 287.67 970.13 296.69 931.78 C 300.26 916.60 303.67 901.39 307.36 886.24 C 318.34 838.07 329.74 790.00 340.88 741.87 C 343.45 728.93 355.54 718.27 368.89 718.32 C 439.92 718.28 510.96 718.32 581.99 718.30 C 592.35 718.50 602.13 712.15 607.19 703.29 C 609.50 699.38 610.39 694.87 611.40 690.50 C 632.80 598.66 654.18 506.81 675.58 414.96 Z"/>
                    </g>
                </svg>
            </div>
            <div class="sidebar-logo-text">
                <div class="app-name">BPS SULUT</div>
                <div class="app-sub">Sistem Manajemen Perubahan<br>Harga Komoditas</div>
            </div>
        </div>

        <!-- Navigation (injected by child layout) -->
        @include(auth()->user()->isProvinsi() ? 'layouts.partials.sidebar-provinsi' : 'layouts.partials.sidebar-wilayah')

        <!-- User Info -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="sidebar-user-info">
                    <div class="user-name" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</div>
                    <div class="user-role">
                        {{ auth()->user()->isProvinsi() ? 'Provinsi' : 'Kabupaten/Kota' }}
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-top:0.5rem">
                @csrf
                <button type="submit" class="sidebar-link w-full" style="border-radius:var(--radius-lg); color:rgba(255,255,255,0.5);">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- ============================================================
         MAIN CONTENT
    ============================================================ -->
    <div class="main-content">
        <!-- Header Bar -->
        <header class="header-bar">
            <!-- Mobile menu toggle -->
            <button class="btn-ghost btn-sm" id="menu-toggle" onclick="toggleSidebar()" style="display:none" title="Menu">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Page Title (breadcrumb) -->
            <div class="header-bar-title">
                @yield('breadcrumb', 'Dashboard')
            </div>

            <!-- Actions -->
            <div class="header-actions">
                <!-- Periode Status -->
                @php
                    $periodeAktif = \App\Models\Periode::where('status', 'aktif')->first();
                @endphp
                @if($periodeAktif)
                <div class="periode-badge aktif" title="Periode rekonsiliasi sedang berjalan">
                    <span class="dot"></span>
                    {{ $periodeAktif->nama }}
                </div>
                @endif

                <!-- Notification Bell -->
                <div style="position:relative;" id="notif-wrapper">
                    <button class="notif-bell" id="notif-toggle" onclick="toggleNotifPanel()" title="Notifikasi" aria-label="Notifikasi">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                        </svg>
                        @if(auth()->user()->unread_notif_count > 0)
                            <span class="badge" id="notif-badge">{{ auth()->user()->unread_notif_count > 9 ? '9+' : auth()->user()->unread_notif_count }}</span>
                        @endif
                    </button>

                    <!-- Notification Panel -->
                    <div class="notif-panel hidden" id="notif-panel">
                        <div class="notif-panel-header">
                            <h3>Notifikasi</h3>
                            <button class="btn-ghost btn-sm" onclick="markAllRead()" id="mark-all-btn">Tandai semua dibaca</button>
                        </div>
                        <div class="notif-list" id="notif-list">
                            <div style="padding:2rem;text-align:center;color:var(--color-on-surface-variant);font-size:0.8125rem;">
                                Memuat notifikasi...
                            </div>
                        </div>
                        <div class="notif-panel-footer">
                            <a href="{{ route('notifikasi.index') }}">Lihat semua notifikasi →</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages & Validation Errors -->
        @if(session('success'))
            <div id="flash-msg" style="margin:0; padding:0 var(--gutter);">
                <div class="alert alert-success" style="margin-top:1rem; border-radius:var(--radius-lg);">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <div>{{ session('success') }}</div>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div id="flash-msg" style="margin:0; padding:0 var(--gutter);">
                <div class="alert alert-error" style="margin-top:1rem; border-radius:var(--radius-lg);">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>{{ session('error') }}</div>
                </div>
            </div>
        @endif
        @if($errors->any())
            <div id="flash-errors" style="margin:0; padding:0 var(--gutter);">
                <div class="alert alert-error" style="margin-top:1rem; border-radius:var(--radius-lg); flex-direction:column; align-items:flex-start;">
                    <div style="display:flex; align-items:center; gap:0.5rem; font-weight:600;">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Terdapat kesalahan pada isian formulir:
                    </div>
                    <ul style="margin:0.5rem 0 0 1.75rem; padding:0; list-style-type:disc;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main class="page-container">
            @yield('content')
        </main>
    </div>
</div>

<!-- External Libraries -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

<style>
    .sidebar-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 1003;
    }
    @media (max-width: 768px) {
        #menu-toggle { display: flex !important; }
    }
</style>

<script>
    // ---- Sidebar Mobile ----
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').classList.toggle('hidden');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.add('hidden');
    }

    // ---- Notification Panel ----
    function toggleNotifPanel() {
        const panel = document.getElementById('notif-panel');
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) loadNotifs();
    }

    // Close on outside click
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('notif-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('notif-panel')?.classList.add('hidden');
        }
    });

    async function loadNotifs() {
        const list = document.getElementById('notif-list');
        try {
            const res = await fetch('{{ route('notifikasi.unread-count') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.notifs && data.notifs.length > 0) {
                list.innerHTML = data.notifs.map(n => `
                    <div class="notif-item ${n.is_read ? '' : 'unread'}" onclick="markRead(${n.id}, '${n.url || '#'}')">
                        <div class="notif-icon" style="color:var(--color-primary-container)">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="15" height="15">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                            </svg>
                        </div>
                        <div>
                            <div class="notif-title">${n.judul}</div>
                            <div class="notif-message">${n.pesan}</div>
                            <div class="notif-time">${n.time_ago}</div>
                        </div>
                    </div>
                `).join('');
            } else {
                list.innerHTML = '<div style="padding:2rem;text-align:center;color:var(--color-on-surface-variant);font-size:0.8125rem;">Tidak ada notifikasi baru.</div>';
            }
        } catch(e) {
            list.innerHTML = '<div style="padding:1.5rem;text-align:center;color:var(--color-on-surface-variant);font-size:0.8125rem;">Gagal memuat notifikasi.</div>';
        }
    }

    async function markRead(id, url) {
        await fetch(`/notifikasi/${id}/baca`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
        });
        if (url && url !== '#') window.location.href = url;
        else loadNotifs();
    }

    async function markAllRead() {
        await fetch('/notifikasi/baca-semua', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
        });
        document.getElementById('notif-badge')?.remove();
        loadNotifs();
    }

    // Universal CRUD Confirmation Handler
    document.addEventListener('submit', function(e) {
        var form = e.target;
        if (form.dataset.confirm) {
            if (!confirm(form.dataset.confirm)) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Auto-dismiss flash messages
    setTimeout(() => {
        document.getElementById('flash-msg')?.remove();
        document.getElementById('flash-errors')?.remove();
    }, 6000);
</script>

@stack('scripts')
</body>
</html>
