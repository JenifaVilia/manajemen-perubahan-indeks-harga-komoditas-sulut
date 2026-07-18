<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SIPERHARGA Sulut — Sistem Manajemen Perubahan Harga Komoditas Sulawesi Utara">
    <title>@yield('title', 'Dashboard') — SIPERHARGA Sulut</title>
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
            <div class="sidebar-logo-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#bfe0ff" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
            </div>
            <div class="sidebar-logo-text">
                <div class="app-name">SIPERHARGA</div>
                <div class="app-sub">Sulawesi Utara</div>
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

        <!-- Flash Messages -->
        @if(session('success'))
            <div id="flash-msg" style="margin:0; padding:0 var(--gutter);">
                <div class="alert alert-success" style="margin-top:1rem; border-radius:var(--radius-lg);">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div id="flash-msg" style="margin:0; padding:0 var(--gutter);">
                <div class="alert alert-error" style="margin-top:1rem; border-radius:var(--radius-lg);">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    {{ session('error') }}
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
        z-index: 99;
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

    // Auto-dismiss flash messages
    setTimeout(() => {
        document.getElementById('flash-msg')?.remove();
    }, 5000);
</script>

@stack('scripts')
</body>
</html>
