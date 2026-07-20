@extends('layouts.app')
@section('title', 'Output Month-to-Month')
@section('breadcrumb') Visualisasi <span>/ Output MtM</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Output Month-to-Month (MtM)</h1>
        <p class="page-subtitle">Heat-map inflasi MtM seluruh komoditas di semua wilayah</p>
    </div>
</div>

{{-- Summary Stats --}}
<div class="kpi-grid mb-4">
    <div class="kpi-card accent-red"><div class="kpi-label">Inflasi Tertinggi</div><div class="kpi-value">{{ number_format($stats['max'], 2) }}%</div></div>
    <div class="kpi-card accent-green"><div class="kpi-label">Deflasi Terdalam</div><div class="kpi-value">{{ number_format($stats['min'], 2) }}%</div></div>
    <div class="kpi-card accent-blue"><div class="kpi-label">Rata-rata</div><div class="kpi-value">{{ number_format($stats['avg'], 4) }}%</div></div>
    <div class="kpi-card accent-gold">
        <div class="kpi-label">Distribusi</div>
        <div style="display:flex;gap:0.75rem;margin-top:0.5rem;">
            <span class="badge badge-red">↑ {{ $stats['naik'] }}</span>
            <span class="badge badge-green">↓ {{ $stats['turun'] }}</span>
            <span class="badge badge-gray">= {{ $stats['stabil'] }}</span>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" class="filter-bar">
        <select name="periode_id" class="form-control" style="width:auto" onchange="this.form.submit()">
            @foreach($periodes as $p)
                <option value="{{ $p->id }}" {{ $p->id == $periodeId ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
        <select name="tipe_indeks" class="form-control" style="width:auto" onchange="this.form.submit()">
            <option value="IHK" {{ ($tipeIndeks ?? 'IHK') === 'IHK' ? 'selected' : '' }}>IHK (Indeks Harga Konsumen)</option>
            <option value="IHPB" {{ ($tipeIndeks ?? 'IHK') === 'IHPB' ? 'selected' : '' }}>IHPB (Indeks Harga Perdagangan Besar)</option>
            <option value="IPP" {{ ($tipeIndeks ?? 'IHK') === 'IPP' ? 'selected' : '' }}>IPP (Indeks Harga Produsen)</option>
            <option value="IPH" {{ ($tipeIndeks ?? 'IHK') === 'IPH' ? 'selected' : '' }}>IPH (Indeks Harga Petani)</option>
        </select>
        <div class="map-legend ml-auto" style="gap:1rem;">
            <div class="legend-item"><div class="legend-dot" style="background:rgba(186,26,26,0.35);"></div> Inflasi Tinggi (&ge;3%)</div>
            <div class="legend-item"><div class="legend-dot" style="background:rgba(186,26,26,0.15);"></div> Inflasi Sedang (1-3%)</div>
            <div class="legend-item"><div class="legend-dot" style="background:rgba(64,72,79,0.07);"></div> Stabil (0%)</div>
            <div class="legend-item"><div class="legend-dot" style="background:rgba(26,107,58,0.15);"></div> Deflasi Sedang</div>
            <div class="legend-item"><div class="legend-dot" style="background:rgba(26,107,58,0.35);"></div> Deflasi Tinggi</div>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrapper" style="max-height:600px;overflow:auto;">
        <table class="data-table" style="min-width:{{ 200 + $wilayahs->count() * 80 }}px;">
            <thead>
                <tr>
                    <th style="position:sticky;left:0;background:var(--color-surface-high);z-index:2;min-width:180px;">Komoditas</th>
                    @foreach($wilayahs as $w)
                        <th class="num" style="font-size:0.6rem;min-width:72px;white-space:normal;line-height:1.2;">{{ Str::limit($w->nama_wilayah, 14) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($komoditas as $k)
                <tr>
                    <td style="position:sticky;left:0;background:var(--color-surface-white);z-index:1;font-weight:600;font-size:0.75rem;border-right:2px solid var(--color-outline-variant);">
                        {{ $k->nama_komoditas }}
                    </td>
                    @foreach($wilayahs as $w)
                        @php
                            $val = $pivot[$k->id][$w->id] ?? null;
                            $heatClass = 'stable';
                            if ($val !== null) {
                                $abs = abs($val);
                                if ($val > 0) $heatClass = $abs >= 3 ? 'high-up' : ($abs >= 1 ? 'med-up' : 'low-up');
                                elseif ($val < 0) $heatClass = $abs >= 3 ? 'high-down' : ($abs >= 1 ? 'med-down' : 'low-down');
                            }
                        @endphp
                        <td class="num">
                            @if($val !== null)
                                <span class="heat-cell {{ $heatClass }}">{{ number_format($val, 2) }}</span>
                            @else
                                <span style="color:var(--color-outline)">—</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
