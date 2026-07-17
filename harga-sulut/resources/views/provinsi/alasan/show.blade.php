@extends('layouts.app')
@section('title', 'Detail Alasan')
@section('breadcrumb') Monitor Alasan <span>/ Detail</span> @endsection

@section('content')
@php
    $alasan = $alasan ?? null;
    $dh = \App\Models\DataHarga::where('periode_id', $alasan?->periode_id)
        ->where('wilayah_id', $alasan?->wilayah_id)
        ->where('komoditas_id', $alasan?->komoditas_id)
        ->first();
@endphp

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Detail Alasan — {{ $alasan?->komoditas?->nama_komoditas }}</h1>
        <p class="page-subtitle">{{ $alasan?->wilayah?->nama_wilayah }} &bull; {{ $alasan?->periode?->nama }}</p>
    </div>
    <a href="{{ route('provinsi.alasan.index') }}" class="btn btn-ghost">← Kembali</a>
</div>

<div class="grid-12">
    <div class="col-span-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-title">Alasan Perubahan Harga</div>
                <span class="badge badge-{{ $alasan?->status_badge['color'] ?? 'gray' }}">{{ $alasan?->status_badge['label'] ?? '-' }}</span>
            </div>
            <div class="card-body">
                <div style="font-size:0.875rem;line-height:1.7;color:var(--color-on-surface);">
                    {{ $alasan?->alasan }}
                </div>

                @if($alasan?->faktor_pendorong)
                <div style="margin-top:1rem;">
                    <label class="form-label">Faktor Pendorong</label>
                    <div class="flex gap-1" style="flex-wrap:wrap;">
                        @foreach($alasan->faktor_pendorong as $f)
                            <span class="badge badge-blue">{{ \App\Models\AlasanPerubahan::$faktors[$f] ?? $f }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($alasan?->rekomendasi)
                <div style="margin-top:1rem;">
                    <label class="form-label">Rekomendasi</label>
                    <p style="font-size:0.8125rem;color:var(--color-on-surface-variant)">{{ $alasan->rekomendasi }}</p>
                </div>
                @endif

                @if($alasan?->catatan_provinsi)
                <div style="margin-top:1rem;padding:0.75rem;background:rgba(186,26,26,0.06);border-radius:var(--radius-lg);border:1px solid rgba(186,26,26,0.15);">
                    <label class="form-label" style="color:var(--color-error)">Catatan dari Provinsi</label>
                    <p style="font-size:0.8125rem;">{{ $alasan->catatan_provinsi }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Aksi Provinsi --}}
        @if($alasan?->status === 'submitted')
        <div class="card">
            <div class="card-header"><div class="card-title">Aksi Review</div></div>
            <div class="card-body">
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('provinsi.alasan.setujui', $alasan) }}" onsubmit="return confirm('Setujui alasan ini?')">
                        @csrf
                        <button class="btn btn-primary">✓ Setujui</button>
                    </form>
                    <form method="POST" action="{{ route('provinsi.alasan.minta-revisi', $alasan) }}" id="revisi-form">
                        @csrf
                        <div class="form-group" style="margin-bottom:0.5rem;">
                            <textarea name="catatan_provinsi" class="form-control" rows="2" placeholder="Catatan revisi untuk wilayah..." required minlength="5"></textarea>
                        </div>
                        <button class="btn btn-accent">↩ Minta Revisi</button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar info --}}
    <div class="col-span-4">
        <div class="card mb-4">
            <div class="card-header"><div class="card-title">Data Harga</div></div>
            <div class="card-body" style="font-size:0.8125rem;">
                @if($dh)
                <table style="width:100%;">
                    <tr><td style="color:var(--color-on-surface-variant)">Harga Level</td><td style="text-align:right;font-weight:600">{{ number_format($dh->harga_level ?? 0, 2) }}</td></tr>
                    <tr><td style="color:var(--color-on-surface-variant)">Inflasi MtM</td><td style="text-align:right;font-weight:700;color:{{ (float)$dh->inflasi_mtm > 0 ? 'var(--color-error)' : 'var(--color-success)' }}">{{ number_format($dh->inflasi_mtm ?? 0, 4) }}%</td></tr>
                    <tr><td style="color:var(--color-on-surface-variant)">Inflasi YtD</td><td style="text-align:right">{{ number_format($dh->inflasi_ytd ?? 0, 4) }}%</td></tr>
                    <tr><td style="color:var(--color-on-surface-variant)">Inflasi YoY</td><td style="text-align:right">{{ number_format($dh->inflasi_yoy ?? 0, 4) }}%</td></tr>
                    <tr><td style="color:var(--color-on-surface-variant)">Andil MtM</td><td style="text-align:right">{{ number_format($dh->andil_mtm ?? 0, 4) }}</td></tr>
                </table>
                @else
                <p style="color:var(--color-outline)">Data harga tidak ditemukan.</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><div class="card-title">Informasi</div></div>
            <div class="card-body" style="font-size:0.8125rem;">
                <table style="width:100%;">
                    <tr><td style="color:var(--color-on-surface-variant)">Diinput oleh</td><td style="text-align:right">{{ $alasan?->submitter?->name ?? '-' }}</td></tr>
                    <tr><td style="color:var(--color-on-surface-variant)">Tanggal input</td><td style="text-align:right">{{ $alasan?->submitted_at?->format('d M Y H:i') ?? '-' }}</td></tr>
                    <tr><td style="color:var(--color-on-surface-variant)">Direview oleh</td><td style="text-align:right">{{ $alasan?->reviewer?->name ?? '-' }}</td></tr>
                    <tr><td style="color:var(--color-on-surface-variant)">Tanggal review</td><td style="text-align:right">{{ $alasan?->reviewed_at?->format('d M Y H:i') ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
