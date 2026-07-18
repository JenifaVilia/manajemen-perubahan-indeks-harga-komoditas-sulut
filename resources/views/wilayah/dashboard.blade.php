@extends('layouts.app')
@section('title', 'Dashboard Wilayah — ' . $wilayah->nama_wilayah)
@section('breadcrumb')
    Dashboard <span>/ {{ $wilayah->tipe === 'kota' ? 'Kota' : 'Kab.' }} {{ $wilayah->nama_wilayah }}</span>
@endsection

@push('styles')
<style>
    .needs-input-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.625rem 1.25rem;
        border-bottom: 1px solid var(--color-surface-low);
        transition: var(--transition-fast);
    }
    .needs-input-row:hover { background: var(--color-surface-low); }
    .inflasi-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.125rem 0.5rem;
        border-radius: var(--radius-full);
        font-family: var(--font-mono);
        font-size: 0.75rem;
        font-weight: 700;
        min-width: 64px;
        justify-content: center;
    }
    .chip-up   { background: rgba(186,26,26,0.1); color: var(--color-error); }
    .chip-down { background: rgba(26,107,58,0.1); color: var(--color-success); }
    .chip-flat { background: var(--color-surface-container); color: var(--color-on-surface-variant); }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Dashboard — {{ $wilayah->tipe === 'kota' ? 'Kota' : 'Kab.' }} {{ $wilayah->nama_wilayah }}</h1>
        <p class="page-subtitle">
            Periode:
            @if($periode)
                <strong>{{ $periode->nama }}</strong>
                <span class="periode-badge {{ $periode->status }}" style="margin-left:0.5rem">
                    @if($periode->status === 'aktif')<span class="dot"></span>@endif
                    {{ ucfirst($periode->status) }}
                </span>
                @if($periode->tanggal_tutup && $periode->status === 'aktif')
                    &bull; Deadline: <strong>{{ $periode->tanggal_tutup->format('d M Y') }}</strong>
                    @if($periode->isDeadlineNear())
                        <span style="color:var(--color-error);font-weight:600"> ⚠ Mendekati Deadline!</span>
                    @endif
                @endif
            @else
                <span style="color:var(--color-on-surface-variant)">Tidak ada periode aktif</span>
            @endif
        </p>
    </div>
    <a href="{{ route('wilayah.input-alasan.index') }}" class="btn btn-primary">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
        </svg>
        Input Alasan Harga
    </a>
</div>

{{-- Alert Revisi --}}
@if($periodeAlerts->count() > 0)
<div class="alert alert-warning mb-4">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <div>
        <strong>{{ $periodeAlerts->count() }} alasan perlu direvisi</strong> berdasarkan catatan dari Provinsi:
        <span>{{ $periodeAlerts->pluck('komoditas.nama_komoditas')->implode(', ') }}</span>.
        <a href="{{ route('wilayah.input-alasan.index', ['filter' => 'revisi']) }}" style="font-weight:700;text-decoration:underline;margin-left:4px;">Perbaiki sekarang →</a>
    </div>
</div>
@endif

{{-- KPI Cards --}}
<div class="kpi-grid">
    <div class="kpi-card accent-blue">
        <div class="kpi-label">Total Komoditas Aktif</div>
        <div class="kpi-value">{{ $totalKomoditas }}</div>
        <div class="kpi-desc">komoditas dipantau di wilayah ini</div>
    </div>

    <div class="kpi-card {{ $persenSelesai >= 100 ? 'accent-green' : ($persenSelesai > 0 ? 'accent-gold' : 'accent-red') }}">
        <div class="kpi-label">Progress Rekonsiliasi</div>
        <div class="kpi-value">{{ $persenSelesai }}<small style="font-size:1rem;font-weight:500">%</small></div>
        <div class="kpi-desc">{{ $sudahInput }} dari {{ $perluInput }} alasan terisi</div>
    </div>

    <div class="kpi-card {{ $perluInput - $sudahInput > 0 ? 'accent-red' : 'accent-green' }}">
        <div class="kpi-label">Perlu Diisi Alasan</div>
        <div class="kpi-value">{{ max(0, $perluInput - $sudahInput) }}</div>
        <div class="kpi-desc">komoditas belum ada alasan</div>
    </div>

    <div class="kpi-card {{ ($rataInflasi ?? 0) > 0 ? 'accent-red' : 'accent-green' }}">
        <div class="kpi-label">Inflasi MtM Wilayah</div>
        <div class="kpi-value">
            {{ $rataInflasi !== null ? number_format($rataInflasi, 2) : '—' }}<small style="font-size:1rem;font-weight:500">%</small>
        </div>
        <div class="kpi-desc">rata-rata periode {{ $periode?->nama ?? 'ini' }}</div>
    </div>
</div>

{{-- Progress Bar --}}
@if($periode)
<div class="card mb-6">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.625rem;">
            <span style="font-size:0.8125rem;font-weight:600;color:var(--color-on-surface);">Progress Input Alasan — Periode {{ $periode->nama }}</span>
            <span style="font-size:0.875rem;font-weight:700;font-family:var(--font-mono);color:{{ $persenSelesai >= 100 ? 'var(--color-success)' : 'var(--color-on-surface)' }}">{{ $persenSelesai }}%</span>
        </div>
        <div class="progress-bar-wrap" style="height:10px;">
            <div class="progress-bar-fill {{ $persenSelesai >= 100 ? 'fill-green' : ($persenSelesai > 50 ? '' : 'fill-red') }}"
                 style="width:{{ $persenSelesai }}%"></div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:0.375rem;">
            <span style="font-size:0.6875rem;color:var(--color-on-surface-variant);">{{ $sudahInput }} alasan terisi</span>
            <span style="font-size:0.6875rem;color:var(--color-on-surface-variant);">{{ $perluInput }} komoditas perlu alasan</span>
        </div>
    </div>
</div>
@endif

{{-- Main Grid --}}
<div class="grid-12">
    {{-- Komoditas Perlu Diisi --}}
    <div class="col-span-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Perlu Input Alasan
                    @if($needsInput->count() > 0)
                        <span class="badge badge-red" style="margin-left:4px">{{ $needsInput->count() }}</span>
                    @endif
                </div>
                <a href="{{ route('wilayah.input-alasan.index', ['filter' => 'perlu']) }}" class="btn btn-sm btn-secondary">Lihat Semua</a>
            </div>
            @if($needsInput->count() > 0)
            <div style="overflow-y:auto;max-height:340px;">
                @foreach($needsInput as $dh)
                <div class="needs-input-row">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.8125rem;font-weight:600;color:var(--color-on-surface);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $dh->komoditas->nama_komoditas }}
                        </div>
                        <div style="font-size:0.6875rem;color:var(--color-on-surface-variant);">{{ $dh->komoditas->kelompok }}</div>
                    </div>
                    <div class="inflasi-chip {{ (float)$dh->inflasi_mtm > 0 ? 'chip-up' : ((float)$dh->inflasi_mtm < 0 ? 'chip-down' : 'chip-flat') }}">
                        {{ (float)$dh->inflasi_mtm > 0 ? '+' : '' }}{{ number_format($dh->inflasi_mtm, 2) }}%
                    </div>
                    <a href="{{ route('wilayah.input-alasan.index', ['filter' => 'semua', 'komoditas_id' => $dh->komoditas_id]) }}"
                       class="btn btn-sm btn-primary">Isi</a>
                </div>
                @endforeach
            </div>
            @else
            <div style="padding:3rem;text-align:center;">
                <div style="font-size:2rem;margin-bottom:0.5rem;">🎉</div>
                <div style="font-size:0.875rem;font-weight:600;color:var(--color-on-surface);">Semua alasan sudah terisi!</div>
                <div style="font-size:0.75rem;color:var(--color-on-surface-variant);margin-top:0.25rem;">Tidak ada komoditas yang perlu input alasan untuk periode ini.</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Chart Tren Inflasi --}}
    <div class="col-span-6">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                    Inflasi MtM — 12 Bulan Terakhir
                </div>
            </div>
            <div class="card-body">
                <div id="wilayah-chart" style="height:300px;"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const chart = echarts.init(document.getElementById('wilayah-chart'), null, { renderer: 'svg' });
    window.addEventListener('resize', () => chart.resize());

    try {
        const resp = await fetch('{{ route('wilayah.dashboard.chart-data') }}', { headers: {'Accept':'application/json'} });
        const data = await resp.json();

        chart.setOption({
            tooltip: { trigger: 'axis', formatter: params => {
                let h = `<div style="font-weight:700;margin-bottom:4px">${params[0].axisValue}</div>`;
                params.forEach(p => { h += `<div>${p.marker} ${p.seriesName}: <strong>${p.value !== null ? p.value.toFixed(4)+'%' : '-'}</strong></div>`; });
                return h;
            }},
            legend: { bottom: 0, textStyle: { fontSize: 11, fontFamily: 'Inter' } },
            grid: { top: 10, left: 50, right: 20, bottom: 55 },
            xAxis: { type: 'category', data: data.labels, axisLabel: { fontSize: 10, rotate: 30, fontFamily: 'Inter' } },
            yAxis: { type: 'value', axisLabel: { formatter: '{value}%', fontSize: 10, fontFamily: 'Inter' }, splitLine: { lineStyle: { color: '#e5eeff' } } },
            series: [
                { name: data.wilayah.label, type: 'line', data: data.wilayah.data, smooth: true, symbol: 'circle', symbolSize: 6, lineStyle: { width: 2.5, color: '#006699' }, itemStyle: { color: '#006699' }, areaStyle: { color: 'rgba(0,102,153,0.06)' } },
                { name: data.provinsi.label, type: 'line', data: data.provinsi.data, smooth: true, symbol: 'circle', symbolSize: 4, lineStyle: { width: 1.5, color: '#fecb00', type: 'dashed' }, itemStyle: { color: '#fecb00' } },
            ]
        });
    } catch(e) {
        document.getElementById('wilayah-chart').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--color-on-surface-variant)">Tidak ada data chart.</div>';
    }
});
</script>
@endpush
