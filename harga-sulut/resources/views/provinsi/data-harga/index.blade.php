@extends('layouts.app')
@section('title', 'Data Harga')
@section('breadcrumb') Data Harga <span>/ Kelola</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Data Harga Komoditas</h1>
        <p class="page-subtitle">Upload atau input data harga komoditas dari seluruh wilayah</p>
    </div>
</div>

<div class="kpi-grid mb-6" style="grid-template-columns: repeat(3,1fr);">
    <a href="{{ route('provinsi.data-harga.upload') }}" class="kpi-card accent-blue" style="text-decoration:none;cursor:pointer;transition:var(--transition-normal);">
        <div style="font-size:2rem;margin-bottom:0.5rem;">📤</div>
        <div class="kpi-label">Upload Excel</div>
        <div style="font-size:0.75rem;color:var(--color-on-surface-variant);margin-top:0.25rem;">Upload file .xlsx data harga dari BPS</div>
    </a>
    <a href="{{ route('provinsi.data-harga.manual') }}" class="kpi-card accent-gold" style="text-decoration:none;cursor:pointer;transition:var(--transition-normal);">
        <div style="font-size:2rem;margin-bottom:0.5rem;">✍️</div>
        <div class="kpi-label">Input Manual</div>
        <div style="font-size:0.75rem;color:var(--color-on-surface-variant);margin-top:0.25rem;">Input data harga satu per satu secara manual</div>
    </a>
    <a href="{{ route('provinsi.data-harga.riwayat') }}" class="kpi-card accent-green" style="text-decoration:none;cursor:pointer;transition:var(--transition-normal);">
        <div style="font-size:2rem;margin-bottom:0.5rem;">📋</div>
        <div class="kpi-label">Riwayat Upload</div>
        <div style="font-size:0.75rem;color:var(--color-on-surface-variant);margin-top:0.25rem;">Lihat riwayat data harga yang sudah diupload</div>
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download Template
        </div>
    </div>
    <div class="card-body" style="display:flex;align-items:center;gap:1rem;">
        <div style="flex:1;">
            <p style="font-size:0.875rem;color:var(--color-on-surface);">Download template Excel yang berisi daftar komoditas untuk diisi data harga.</p>
            <p style="font-size:0.75rem;color:var(--color-on-surface-variant);margin-top:0.25rem;">Template sudah berisi kode dan nama komoditas aktif.</p>
        </div>
        <a href="{{ route('provinsi.data-harga.template') }}" class="btn btn-primary">Download Template .xlsx</a>
    </div>
</div>
@endsection
