@extends('layouts.app')

@section('title', 'Dashboard Provinsi')

@section('breadcrumb')
    Dashboard <span>/ Provinsi Sulawesi Utara</span>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Dashboard-specific overrides */
    .map-legend {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
        margin-top: 0.75rem;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        color: var(--color-on-surface-variant);
    }
    .legend-dot {
        width: 12px; height: 12px;
        border-radius: 3px;
    }

    .wilayah-row-bar {
        height: 6px;
        background: var(--color-surface-container);
        border-radius: var(--radius-full);
        overflow: hidden;
        flex: 1;
        min-width: 80px;
    }
    .wilayah-row-bar-fill {
        height: 100%;
        border-radius: var(--radius-full);
        transition: width 0.8s ease;
    }
    .fill-selesai  { background: var(--color-success); }
    .fill-sebagian { background: var(--color-secondary-container); }
    .fill-belum    { background: var(--color-error); }
    .fill-no-data  { background: var(--color-outline-variant); }

    .topinflasi-bar {
        height: 8px;
        background: var(--color-surface-container);
        border-radius: var(--radius-full);
        overflow: hidden;
        flex: 1;
    }
    .topinflasi-bar-fill {
        height: 100%;
        background: var(--color-primary-container);
        border-radius: var(--radius-full);
    }
</style>
@endpush

@section('content')

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Dashboard Provinsi</h1>
        <p class="page-subtitle">
            Rekonsiliasi Periode:
            @if($periodeAktif)
                <strong>{{ $periodeAktif->nama }}</strong>
                <span class="periode-badge aktif" style="margin-left:0.5rem;">
                    <span class="dot"></span>
                    Aktif
                </span>
                @if($periodeAktif->tanggal_tutup)
                    &bull; Deadline: <strong>{{ $periodeAktif->tanggal_tutup->format('d M Y') }}</strong>
                    @if($periodeAktif->isDeadlineNear())
                        <span style="color:var(--color-error);font-weight:600;"> ⚠ Deadline Dekat!</span>
                    @endif
                @endif
            @else
                <span style="color:var(--color-on-surface-variant);">Tidak ada periode aktif</span>
            @endif
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('provinsi.data-harga.upload') }}" class="btn btn-accent">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Upload Data Harga
        </a>
        <div class="dropdown">
            <button class="btn btn-secondary" onclick="this.nextElementSibling.classList.toggle('hidden')">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Ekspor
            </button>
            <div class="dropdown-menu hidden">
                <a href="{{ route('provinsi.ekspor.rekap-periode') }}" class="dropdown-item">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    Rekap Periode (PDF)
                </a>
                <a href="{{ route('provinsi.ekspor.alasan') }}" class="dropdown-item">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Tabel Alasan (Excel)
                </a>
                <a href="{{ route('provinsi.ekspor.tabel-relatif') }}" class="dropdown-item">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Tabel Relatif Harga (Excel)
                </a>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card accent-blue">
        <div class="kpi-label">Total Komoditas Dipantau</div>
        <div class="kpi-value">{{ $totalKomoditas }}</div>
        <div class="kpi-desc">komoditas aktif di sistem</div>
    </div>

    <div class="kpi-card accent-green">
        <div class="kpi-label">Progress Rekonsiliasi</div>
        <div class="kpi-value">{{ $persenSelesai }}<small style="font-size:1rem;font-weight:500">%</small></div>
        <div class="kpi-desc">{{ $totalSudahInput }} dari {{ $totalPerluInput }} alasan terisi</div>
    </div>

    <div class="kpi-card {{ $pendingReview > 0 ? 'accent-gold' : 'accent-blue' }}">
        <div class="kpi-label">Menunggu Review</div>
        <div class="kpi-value">{{ $pendingReview }}</div>
        <div class="kpi-desc">alasan belum dikonfirmasi</div>
    </div>

    <div class="kpi-card {{ ($rataInflasi ?? 0) > 0 ? 'accent-red' : 'accent-green' }}">
        <div class="kpi-label">Rata-rata Inflasi MtM</div>
        <div class="kpi-value">
            {{ $rataInflasi !== null ? number_format($rataInflasi, 2) : '—' }}<small style="font-size:1rem;font-weight:500">%</small>
        </div>
        <div class="kpi-trend {{ ($rataInflasi ?? 0) > 0 ? 'up' : 'down' }}">
            @if($rataInflasi !== null)
                @if($rataInflasi > 0)
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                @else
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181"/></svg>
                @endif
                Provinsi Sulawesi Utara
            @endif
        </div>
    </div>
</div>

<!-- Main Grid: Map + Status Wilayah -->
<div class="grid-12 mb-6">
    <!-- Peta Choropleth -->
    <div class="col-span-8">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/>
                    </svg>
                    Peta Status Rekonsiliasi — Sulawesi Utara
                </div>
                <div class="flex gap-2 items-center">
                    <select class="form-control" style="width:auto;font-size:0.75rem;padding:0.375rem 0.5rem;" id="peta-periode-select" onchange="refreshPeta()">
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}" {{ $periodeAktif && $p->id === $periodeAktif->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div id="choropleth-map" style="height:380px;border-radius:var(--radius-md);overflow:hidden;"></div>
                <div class="map-legend">
                    <div class="legend-item"><div class="legend-dot" style="background:#1a6b3a;"></div> Selesai (100%)</div>
                    <div class="legend-item"><div class="legend-dot" style="background:#fecb00;"></div> Sebagian (>0%)</div>
                    <div class="legend-item"><div class="legend-dot" style="background:#ba1a1a;"></div> Belum Ada Input</div>
                    <div class="legend-item"><div class="legend-dot" style="background:#c0c7d0;"></div> Tidak Ada Data</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status per Wilayah -->
    <div class="col-span-4">
        <div class="card" style="height:100%">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    Status Per Wilayah
                </div>
            </div>
            <div style="overflow-y:auto;max-height:380px;">
                @foreach($statusWilayah as $s)
                <div style="padding:0.625rem 1.25rem;border-bottom:1px solid var(--color-surface-low);display:flex;flex-direction:column;gap:0.375rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:0.5rem;">
                        <span style="font-size:0.75rem;font-weight:600;color:var(--color-on-surface);truncate;flex:1;" title="{{ $s['wilayah']->nama_wilayah }}">
                            {{ Str::limit($s['wilayah']->nama_wilayah, 22) }}
                        </span>
                        <span style="font-size:0.6875rem;font-weight:700;color:var(--color-on-surface-variant);font-family:var(--font-mono);">
                            {{ $s['sudah'] }}/{{ $s['perlu'] }}
                        </span>
                        @php
                            $bColor = match($s['status']) {
                                'selesai'  => 'badge-green',
                                'sebagian' => 'badge-yellow',
                                'belum'    => 'badge-red',
                                default    => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $bColor }}">{{ $s['persen'] }}%</span>
                    </div>
                    <div class="wilayah-row-bar">
                        <div class="wilayah-row-bar-fill fill-{{ $s['status'] }}"
                             style="width:{{ $s['persen'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Bottom Grid: Chart Inflasi + Top Komoditas -->
<div class="grid-12">
    <!-- Grafik Inflasi MtM -->
    <div class="col-span-7">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                    Inflasi MtM — 12 Bulan Terakhir
                </div>
                <select class="form-control" style="width:auto;font-size:0.75rem;padding:0.375rem 0.5rem;" id="chart-wilayah-select" onchange="refreshChart()">
                    <option value="">Semua Wilayah</option>
                    @foreach($wilayahs as $w)
                        <option value="{{ $w->id }}">{{ $w->nama_wilayah }}</option>
                    @endforeach
                </select>
            </div>
            <div class="card-body">
                <div id="inflasi-chart" style="height:280px;"></div>
            </div>
        </div>
    </div>

    <!-- Top Komoditas Pendorong Inflasi -->
    <div class="col-span-5">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                    </svg>
                    Top 10 Pendorong Inflasi MtM
                </div>
            </div>
            <div class="card-body" style="padding:0.75rem;">
                @forelse($topInflasi as $i => $dh)
                @php
                    $maxAndil = $topInflasi->max('andil_mtm') ?: 1;
                    $persen = round(($dh->andil_mtm ?? 0) / $maxAndil * 100);
                @endphp
                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
                    <span style="width:16px;font-size:0.6875rem;color:var(--color-on-surface-variant);font-weight:600;text-align:right;flex-shrink:0;">{{ $i+1 }}</span>
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:3px;">
                            <span style="font-size:0.75rem;font-weight:600;color:var(--color-on-surface);truncate;" title="{{ $dh->komoditas->nama_komoditas }}">
                                {{ Str::limit($dh->komoditas->nama_komoditas, 20) }}
                            </span>
                            <span style="font-size:0.6875rem;font-family:var(--font-mono);color:var(--color-error);font-weight:700;">
                                +{{ number_format($dh->andil_mtm ?? 0, 4) }}
                            </span>
                        </div>
                        <div class="topinflasi-bar">
                            <div class="topinflasi-bar-fill" style="width:{{ $persen }}%"></div>
                        </div>
                        <div style="font-size:0.625rem;color:var(--color-on-surface-variant);margin-top:2px;">{{ $dh->wilayah->nama_wilayah }}</div>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:2rem;color:var(--color-on-surface-variant);font-size:0.8125rem;">
                    Belum ada data harga untuk periode ini.
                </div>
                @endforelse
            </div>
            @if($topInflasi->count() > 0)
            <div class="card-footer">
                <a href="{{ route('provinsi.visualisasi.tren-komoditas') }}" style="font-size:0.75rem;font-weight:600;color:var(--color-primary-container);">
                    Lihat analisis tren komoditas →
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ============================================================
// PETA CHOROPLETH
// ============================================================
let map, geojsonLayer;

const statusColors = {
    selesai:  '#1a6b3a',
    sebagian: '#fecb00',
    belum:    '#ba1a1a',
    'no-data':'#c0c7d0',
};

let geojsonData = null;
let petaStatusData = {};

async function loadGeoJSON() {
    if (geojsonData) return geojsonData;
    const resp = await fetch('{{ asset("geojson/sulut.geojson") }}');
    geojsonData = await resp.json();
    return geojsonData;
}

async function initMap() {
    map = L.map('choropleth-map', {
        center: [1.5, 124.8],
        zoom: 7,
        zoomControl: true,
        scrollWheelZoom: false,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
        attribution: '©OpenStreetMap ©CartoDB',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    await loadGeoJSON();
    await refreshPeta();
}

async function refreshPeta() {
    const periodeId = document.getElementById('peta-periode-select')?.value || '';
    const url = `{{ route('provinsi.dashboard.peta-data') }}?periode_id=${periodeId}`;
    const resp = await fetch(url, { headers: {'Accept':'application/json'} });
    const data = await resp.json();

    petaStatusData = {};
    (data.features || []).forEach(f => { petaStatusData[f.kode] = f; });

    if (geojsonLayer) geojsonLayer.remove();

    if (!geojsonData) return;

    geojsonLayer = L.geoJSON(geojsonData, {
        style: feature => {
            const kode = feature.properties.kode_wilayah;
            const s = petaStatusData[kode];
            return {
                fillColor:   s ? statusColors[s.status] : '#c0c7d0',
                fillOpacity: 0.75,
                color:       '#ffffff',
                weight:      1.5,
            };
        },
        onEachFeature: (feature, layer) => {
            const kode = feature.properties.kode_wilayah;
            const s = petaStatusData[kode];
            const nama = s?.nama || feature.properties.nama;
            const persen = s ? s.persen : '-';
            const sudah = s ? s.sudah : 0;
            const perlu = s ? s.perlu : 0;

            layer.bindTooltip(`
                <div style="font-family:Inter,sans-serif;padding:4px 2px;">
                    <div style="font-weight:700;margin-bottom:4px;font-size:13px;">${nama}</div>
                    <div style="font-size:12px;color:#40484f;">Alasan terisi: <strong>${sudah}/${perlu}</strong></div>
                    <div style="font-size:12px;color:#40484f;">Progress: <strong>${persen}%</strong></div>
                </div>
            `, { sticky: true, className: 'leaflet-tooltip-custom' });

            layer.on('mouseover', () => layer.setStyle({ fillOpacity: 0.95, weight: 2.5, color: '#004d75' }));
            layer.on('mouseout', () => geojsonLayer.resetStyle(layer));
        }
    }).addTo(map);
}

// ============================================================
// CHART INFLASI (ECharts)
// ============================================================
let inflasiChart;

async function initChart() {
    inflasiChart = echarts.init(document.getElementById('inflasi-chart'), null, { renderer: 'svg' });
    await refreshChart();
    window.addEventListener('resize', () => inflasiChart.resize());
}

async function refreshChart() {
    const wilayahId = document.getElementById('chart-wilayah-select')?.value || '';
    const url = `{{ route('provinsi.dashboard.chart-inflasi') }}?wilayah_id=${wilayahId}`;
    const resp = await fetch(url, { headers: {'Accept':'application/json'} });
    const data = await resp.json();

    const option = {
        tooltip: {
            trigger: 'axis',
            formatter: params => {
                let html = `<div style="font-weight:700;margin-bottom:4px">${params[0].axisValue}</div>`;
                params.forEach(p => {
                    const val = p.value !== null ? p.value.toFixed(4) + '%' : '-';
                    html += `<div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${p.color};"></span> ${p.seriesName}: <strong>${val}</strong></div>`;
                });
                return html;
            }
        },
        legend: {
            type: 'scroll',
            bottom: 0,
            textStyle: { fontSize: 11, fontFamily: 'Inter' }
        },
        grid: { top: 10, left: 40, right: 20, bottom: 60 },
        xAxis: {
            type: 'category',
            data: data.labels,
            axisLabel: { fontSize: 10, fontFamily: 'Inter', rotate: 30 },
        },
        yAxis: {
            type: 'value',
            axisLabel: { formatter: '{value}%', fontSize: 10, fontFamily: 'Inter' },
            splitLine: { lineStyle: { color: '#e5eeff' } }
        },
        series: data.datasets.map((ds, i) => ({
            name: ds.label,
            type: 'line',
            data: ds.data,
            smooth: true,
            symbol: 'circle',
            symbolSize: 5,
            lineStyle: { width: 2, color: ds.color || null },
            itemStyle: { color: ds.color || null },
        }))
    };

    inflasiChart.setOption(option, true);
}

// ============================================================
// INIT
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    initMap();
    initChart();
});

// Close dropdowns on outside click
document.addEventListener('click', e => {
    document.querySelectorAll('.dropdown-menu').forEach(m => {
        if (!m.parentElement.contains(e.target)) m.classList.add('hidden');
    });
});
</script>
@endpush
