{{-- Sidebar Kabupaten/Kota Navigation --}}
@php
    $route = request()->route()->getName();
    $wilayah = auth()->user()->wilayah;
    $periodeAktif = \App\Models\Periode::where('status', 'aktif')->first();

    // Hitung komoditas yang perlu diisi alasan (periode aktif)
    $perluInput = 0;
    if ($periodeAktif && $wilayah) {
        $perluInput = \App\Models\DataHarga::where('periode_id', $periodeAktif->id)
            ->where('wilayah_id', $wilayah->id)
            ->signifikan()
            ->whereDoesntHave('alasanPerubahan', fn($q) => $q->whereIn('status', ['submitted', 'disetujui']))
            ->count();
    }
@endphp

<nav class="sidebar-section">
    <div class="sidebar-section-label">Wilayah Saya</div>

    <a href="{{ route('wilayah.dashboard') }}"
       class="sidebar-link {{ str_starts_with($route, 'wilayah.dashboard') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
        Dashboard
    </a>
</nav>

<nav class="sidebar-section">
    <div class="sidebar-section-label">Rekonsiliasi</div>

    <a href="{{ route('wilayah.input-alasan.index') }}"
       class="sidebar-link {{ str_starts_with($route, 'wilayah.input-alasan') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
        </svg>
        Input Alasan Harga
        @if($perluInput > 0)
            <span class="badge-count">{{ $perluInput }}</span>
        @endif
    </a>

    <a href="{{ route('wilayah.histori.index') }}"
       class="sidebar-link {{ str_starts_with($route, 'wilayah.histori') ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Histori Harga
    </a>
</nav>

<nav class="sidebar-section">
    <div class="sidebar-section-label">Komoditas</div>

    <a href="{{ route('wilayah.komoditas.index') }}"
       class="sidebar-link {{ $route === 'wilayah.komoditas.index' ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
        </svg>
        Komoditas Wilayah
    </a>

    <a href="{{ route('wilayah.komoditas.permintaan') }}"
       class="sidebar-link {{ $route === 'wilayah.komoditas.permintaan' ? 'active' : '' }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Status Permintaan
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
