@extends('layouts.app')
@section('title', 'Tren Komoditas')
@section('breadcrumb') Visualisasi <span>/ Tren Komoditas</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Tren Komoditas</h1>
        <p class="page-subtitle">Pergerakan inflasi MtM komoditas di seluruh wilayah selama 12 bulan terakhir</p>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="filter-bar">
        <label class="form-label" style="margin:0;white-space:nowrap;">Pilih Komoditas:</label>
        <select class="form-control" style="width:auto;min-width:220px;" id="komoditas-select" onchange="refreshChart()">
            @foreach($komoditasList as $k)
                <option value="{{ $k->id }}" {{ $k->id == $komoditasId ? 'selected' : '' }}>{{ $k->nama_komoditas }}</option>
            @endforeach
        </select>
        <label class="form-label" style="margin:0;white-space:nowrap;margin-left:1rem;">Tipe Indeks:</label>
        <select class="form-control" style="width:auto;" id="tipe-indeks-select" onchange="refreshChart()">
            <option value="IHK" {{ ($tipeIndeks ?? 'IHK') === 'IHK' ? 'selected' : '' }}>IHK (Indeks Harga Konsumen)</option>
            <option value="IHPB" {{ ($tipeIndeks ?? 'IHK') === 'IHPB' ? 'selected' : '' }}>IHPB (Indeks Harga Perdagangan Besar)</option>
            <option value="IPP" {{ ($tipeIndeks ?? 'IHK') === 'IPP' ? 'selected' : '' }}>IPP (Indeks Harga Produsen)</option>
            <option value="IPH" {{ ($tipeIndeks ?? 'IHK') === 'IPH' ? 'selected' : '' }}>IPH (Indeks Harga Petani)</option>
        </select>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            Inflasi MtM — <span id="chart-komoditas-name">{{ $komoditas?->nama_komoditas ?? 'Semua' }}</span>
        </div>
    </div>
    <div class="card-body">
        <div id="tren-chart" style="height:400px;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let trenChart;
document.addEventListener('DOMContentLoaded', () => {
    trenChart = echarts.init(document.getElementById('tren-chart'), null, { renderer: 'svg' });
    window.addEventListener('resize', () => trenChart.resize());
    refreshChart();
});

async function refreshChart() {
    const komoditasId = document.getElementById('komoditas-select').value;
    const tipeIndeks  = document.getElementById('tipe-indeks-select').value;
    const selectedText = document.getElementById('komoditas-select').selectedOptions[0]?.text || '';
    document.getElementById('chart-komoditas-name').textContent = selectedText;

    const url = `{{ route('provinsi.visualisasi.data-tabel') }}?komoditas_id=${komoditasId}&tipe_indeks=${tipeIndeks}`;
    const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const data = await resp.json();

    const option = {
        tooltip: {
            trigger: 'axis',
            formatter: params => {
                let html = `<div style="font-weight:700;margin-bottom:4px">${params[0].axisValue}</div>`;
                params.forEach(p => {
                    const val = p.value !== null ? p.value.toFixed(4) + '%' : '—';
                    html += `<div style="display:flex;align-items:center;gap:6px"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${p.color}"></span> ${p.seriesName}: <strong>${val}</strong></div>`;
                });
                return html;
            }
        },
        legend: {
            type: 'scroll',
            bottom: 0,
            textStyle: { fontSize: 11, fontFamily: 'Inter' }
        },
        grid: { top: 20, left: 45, right: 15, bottom: 65 },
        xAxis: {
            type: 'category',
            data: data.labels,
            axisLabel: { fontSize: 11, fontFamily: 'Inter', rotate: 0, margin: 12 },
            axisLine: { lineStyle: { color: '#c0c7d0' } },
            axisTick: { show: false }
        },
        yAxis: {
            type: 'value',
            axisLabel: { formatter: '{value}%', fontSize: 10, fontFamily: 'Inter' },
            splitLine: { lineStyle: { color: '#e5eeff' } }
        },
        series: (data.datasets || []).map(ds => ({
            name: ds.label,
            type: 'line',
            data: ds.data,
            smooth: true,
            symbol: 'circle',
            symbolSize: 6,
            showSymbol: false,
            lineStyle: { width: 1.75, color: ds.color || null },
            itemStyle: { color: ds.color || null },
            emphasis: {
                lineStyle: { width: 3.5 }
            }
        }))
    };

    trenChart.setOption(option, true);
}
</script>
@endpush
