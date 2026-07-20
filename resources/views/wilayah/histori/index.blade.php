@extends('layouts.app')
@section('title', 'Histori Harga')
@section('breadcrumb') Histori <span>/ {{ $wilayah->nama_wilayah }}</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Histori Harga — {{ $wilayah->nama_wilayah }}</h1>
        <p class="page-subtitle">Riwayat data harga komoditas di wilayah Anda</p>
    </div>
    <a href="{{ route('wilayah.histori.ekspor') }}" class="btn btn-secondary">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Ekspor Excel
    </a>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" class="filter-bar">
        <select name="periode_id" class="form-control" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua Periode</option>
            @foreach($periodes as $p)
                <option value="{{ $p->id }}" {{ $p->id == $periodeId ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
        <select name="tipe_indeks" class="form-control" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua Tipe Indeks</option>
            <option value="IHK" {{ ($tipeIndeks ?? '') === 'IHK' ? 'selected' : '' }}>IHK (Indeks Harga Konsumen)</option>
            <option value="IHPB" {{ ($tipeIndeks ?? '') === 'IHPB' ? 'selected' : '' }}>IHPB (Indeks Harga Perdagangan Besar)</option>
            <option value="IPP" {{ ($tipeIndeks ?? '') === 'IPP' ? 'selected' : '' }}>IPP (Indeks Harga Produsen)</option>
            <option value="IPH" {{ ($tipeIndeks ?? '') === 'IPH' ? 'selected' : '' }}>IPH (Indeks Harga Petani)</option>
        </select>
        <select name="komoditas_id" class="form-control" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua Komoditas</option>
            @foreach($komoditas as $k)
                <option value="{{ $k->id }}" {{ $k->id == $komoditasId ? 'selected' : '' }}>{{ $k->nama_komoditas }}</option>
            @endforeach
        </select>
        <a href="{{ route('wilayah.histori.index') }}" class="btn btn-ghost btn-sm">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Komoditas</th>
                    <th>Tipe</th>
                    <th class="num">Harga Level</th>
                    <th class="num">MtM (%)</th>
                    <th class="num">YtD (%)</th>
                    <th class="num">YoY (%)</th>
                    <th class="num">Andil MtM</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataHargas as $dh)
                @php $mtm = (float)$dh->inflasi_mtm; @endphp
                <tr>
                    <td style="white-space:nowrap">{{ $dh->periode->nama }}</td>
                    <td style="font-weight:600">{{ $dh->komoditas->nama_komoditas }}</td>
                    <td><span class="badge badge-blue">{{ $dh->tipe_indeks }}</span></td>
                    <td class="num">{{ number_format($dh->harga_level ?? 0, 2) }}</td>
                    <td class="num {{ $mtm > 0 ? 'inflasi-naik' : ($mtm < 0 ? 'inflasi-turun' : 'inflasi-stabil') }}">
                        {{ $mtm > 0 ? '+' : '' }}{{ number_format($mtm, 4) }}
                    </td>
                    <td class="num">{{ number_format($dh->inflasi_ytd ?? 0, 4) }}</td>
                    <td class="num">{{ number_format($dh->inflasi_yoy ?? 0, 4) }}</td>
                    <td class="num">{{ number_format($dh->andil_mtm ?? 0, 4) }}</td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--color-on-surface-variant)">Belum ada data harga.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $dataHargas->links() }}</div>
</div>
@endsection
