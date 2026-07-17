@extends('layouts.app')
@section('title', 'Tabel Relatif Harga')
@section('breadcrumb') Visualisasi <span>/ Tabel Relatif Harga</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Tabel Relatif Harga</h1>
        <p class="page-subtitle">Perbandingan nilai komoditas antar wilayah per periode</p>
    </div>
    <a href="{{ route('provinsi.ekspor.tabel-relatif', ['periode_id' => $periodeId]) }}" class="btn btn-secondary">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Ekspor Excel
    </a>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" class="filter-bar">
        <select name="periode_id" class="form-control" style="width:auto" onchange="this.form.submit()">
            @foreach($periodes as $p)
                <option value="{{ $p->id }}" {{ $p->id == $periodeId ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
        <select name="metrik" class="form-control" style="width:auto" onchange="this.form.submit()">
            <option value="inflasi_mtm" {{ $metrik === 'inflasi_mtm' ? 'selected' : '' }}>Inflasi MtM (%)</option>
            <option value="inflasi_ytd" {{ $metrik === 'inflasi_ytd' ? 'selected' : '' }}>Inflasi YtD (%)</option>
            <option value="inflasi_yoy" {{ $metrik === 'inflasi_yoy' ? 'selected' : '' }}>Inflasi YoY (%)</option>
            <option value="harga_level" {{ $metrik === 'harga_level' ? 'selected' : '' }}>Harga Level (Rp)</option>
        </select>
    </form>
</div>

<div class="card">
    <div class="table-wrapper" style="max-height:600px;overflow:auto;">
        <table class="data-table" style="min-width:{{ 200 + $wilayahs->count() * 90 }}px;">
            <thead>
                <tr>
                    <th style="position:sticky;left:0;background:var(--color-surface-high);z-index:2;min-width:180px;">Komoditas</th>
                    @foreach($wilayahs as $w)
                        <th class="num" style="font-size:0.625rem;min-width:80px;white-space:normal;line-height:1.2;">{{ $w->nama_wilayah }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($komoditas as $k)
                <tr>
                    <td style="position:sticky;left:0;background:var(--color-surface-white);z-index:1;font-weight:600;font-size:0.75rem;border-right:2px solid var(--color-outline-variant);">
                        {{ $k->nama_komoditas }}
                        <div style="font-size:0.625rem;font-weight:400;color:var(--color-on-surface-variant);">{{ $k->kelompok }}</div>
                    </td>
                    @foreach($wilayahs as $w)
                        @php
                            $val = $pivot[$k->id][$w->id] ?? null;
                            $isInflasi = str_starts_with($metrik, 'inflasi');
                            $heatClass = '';
                            if ($val !== null && $isInflasi) {
                                $abs = abs((float)$val);
                                if ((float)$val > 0) {
                                    $heatClass = $abs >= 3 ? 'high-up' : ($abs >= 1 ? 'med-up' : 'low-up');
                                } elseif ((float)$val < 0) {
                                    $heatClass = $abs >= 3 ? 'high-down' : ($abs >= 1 ? 'med-down' : 'low-down');
                                } else {
                                    $heatClass = 'stable';
                                }
                            }
                        @endphp
                        <td class="num">
                            @if($val !== null)
                                @if($isInflasi)
                                    <span class="heat-cell {{ $heatClass }}">{{ number_format((float)$val, 2) }}</span>
                                @else
                                    {{ number_format((float)$val, 2) }}
                                @endif
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
