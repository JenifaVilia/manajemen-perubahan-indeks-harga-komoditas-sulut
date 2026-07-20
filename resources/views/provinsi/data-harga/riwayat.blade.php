@extends('layouts.app')
@section('title', 'Riwayat Upload')
@section('breadcrumb') Data Harga <span>/ Riwayat</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Riwayat Data Harga</h1>
        <p class="page-subtitle">Data harga yang sudah diupload/diinput dalam sistem</p>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card mb-4">
    <form method="GET" class="filter-bar" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
        <select name="periode_id" class="form-control" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua Periode</option>
            @foreach($periodes as $p)
                <option value="{{ $p->id }}" {{ $p->id == $periodeId ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>

        <select name="wilayah_id" class="form-control" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua Wilayah</option>
            @foreach($wilayahs as $w)
                <option value="{{ $w->id }}" {{ $w->id == $wilayahId ? 'selected' : '' }}>{{ $w->nama_wilayah }}</option>
            @endforeach
        </select>

        <select name="tipe_indeks" class="form-control" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua Tipe Indeks</option>
            <option value="IHK" {{ $tipeIndeks === 'IHK' ? 'selected' : '' }}>IHK (Indeks Harga Konsumen)</option>
            <option value="IHPB" {{ $tipeIndeks === 'IHPB' ? 'selected' : '' }}>IHPB (Indeks Harga Perdagangan Besar)</option>
            <option value="IPP" {{ $tipeIndeks === 'IPP' ? 'selected' : '' }}>IPP (Indeks Harga Produsen)</option>
            <option value="IPH" {{ $tipeIndeks === 'IPH' ? 'selected' : '' }}>IPH (Indeks Harga Petani)</option>
        </select>

        <input type="text" name="search" class="form-control" placeholder="Cari nama/kode komoditas..." value="{{ $search }}" style="width:auto;min-width:200px;">
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if($periodeId || $wilayahId || $tipeIndeks || $search)
            <a href="{{ route('provinsi.data-harga.riwayat') }}" class="btn btn-ghost btn-sm">Reset</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Wilayah</th>
                    <th>Komoditas</th>
                    <th class="num">Harga</th>
                    <th class="num">MtM (%)</th>
                    <th>Tipe</th>
                    <th>Diupload Oleh</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $d)
                <tr>
                    <td style="white-space:nowrap">{{ $d->periode->nama }}</td>
                    <td>{{ $d->wilayah->nama_wilayah }}</td>
                    <td style="font-weight:600">{{ $d->komoditas->nama_komoditas }}</td>
                    <td class="num">{{ number_format($d->harga_level ?? 0, 2) }}</td>
                    <td class="num {{ (float)$d->inflasi_mtm > 0 ? 'inflasi-naik' : ((float)$d->inflasi_mtm < 0 ? 'inflasi-turun' : '') }}">
                        {{ (float)$d->inflasi_mtm > 0 ? '+' : '' }}{{ number_format($d->inflasi_mtm ?? 0, 4) }}
                    </td>
                    <td><span class="badge badge-blue">{{ $d->tipe_indeks }}</span></td>
                    <td style="font-size:0.75rem">{{ $d->uploader?->name ?? '-' }}</td>
                    <td style="font-size:0.75rem">{{ $d->updated_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--color-on-surface-variant)">Tidak ada data riwayat harga yang sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $riwayat->links() }}</div>
</div>
@endsection
