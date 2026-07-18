{{-- Sidebar Provinsi Navigation --}}
@php $route = request()->route()->getName(); @endphp

<nav class="sidebar-section">
    <div class="sidebar-section-label">Utama</div>

    <a href="{{ route('provinsi.dashboard') }}"
       class="sidebar-link {{ str_starts_with($route, 'provinsi.dashboard') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
        Dashboard
    </a>
</nav>

<nav class="sidebar-section">
    <div class="sidebar-section-label">Data Harga</div>

    <a href="{{ route('provinsi.data-harga.index') }}"
       class="sidebar-link {{ str_starts_with($route, 'provinsi.data-harga') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        Upload & Input Harga
    </a>

    <a href="{{ route('provinsi.visualisasi.tabel-relatif') }}"
       class="sidebar-link {{ $route === 'provinsi.visualisasi.tabel-relatif' ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        Tabel Relatif Harga
    </a>

    <a href="{{ route('provinsi.visualisasi.output-mtm') }}"
       class="sidebar-link {{ $route === 'provinsi.visualisasi.output-mtm' ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Output M-to-M
    </a>

    <a href="{{ route('provinsi.visualisasi.tren-komoditas') }}"
       class="sidebar-link {{ $route === 'provinsi.visualisasi.tren-komoditas' ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
        </svg>
        Tren Komoditas
    </a>
</nav>

<nav class="sidebar-section">
    <div class="sidebar-section-label">Rekonsiliasi</div>

    <a href="{{ route('provinsi.alasan.index') }}"
       class="sidebar-link {{ str_starts_with($route, 'provinsi.alasan') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Monitor Alasan
        @php
            $pendingAlasan = \App\Models\AlasanPerubahan::submitted()->count();
        @endphp
        @if($pendingAlasan > 0)
            <span class="badge-count">{{ $pendingAlasan }}</span>
        @endif
    </a>
</nav>

<nav class="sidebar-section">
    <div class="sidebar-section-label">Manajemen</div>

    <a href="{{ route('provinsi.periode.index') }}"
       class="sidebar-link {{ str_starts_with($route, 'provinsi.periode') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
        </svg>
        Kelola Periode
    </a>

    <a href="{{ route('provinsi.komoditas.index') }}"
       class="sidebar-link {{ str_starts_with($route, 'provinsi.komoditas') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
        </svg>
        Kelola Komoditas
    </a>

    <a href="{{ route('provinsi.users.index') }}"
       class="sidebar-link {{ str_starts_with($route, 'provinsi.users') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        </svg>
        Kelola User
    </a>

    <a href="{{ route('provinsi.permintaan-komoditas.index') }}"
       class="sidebar-link {{ str_starts_with($route, 'provinsi.permintaan-komoditas') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Permintaan Komoditas
        @php
            $pendingKom = \App\Models\KomoditasWilayah::pending()->count();
        @endphp
        @if($pendingKom > 0)
            <span class="badge-count">{{ $pendingKom }}</span>
        @endif
    </a>
</nav>

<nav class="sidebar-section">
    <div class="sidebar-section-label">Lainnya</div>

    <a href="{{ route('notifikasi.index') }}"
       class="sidebar-link {{ str_starts_with($route, 'notifikasi') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
        Notifikasi
    </a>
</nav>
