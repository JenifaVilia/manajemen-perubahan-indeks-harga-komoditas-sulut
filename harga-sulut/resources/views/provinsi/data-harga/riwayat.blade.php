@extends('layouts.app')
@section('title', 'Riwayat Upload')
@section('breadcrumb') Data Harga <span>/ Riwayat</span> @endsection

@section('content')
<div class="page-header"><div class="page-header-left"><h1 class="page-title">Riwayat Data Harga</h1><p class="page-subtitle">Data harga yang sudah diupload/diinput</p></div></div>

<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Periode</th><th>Wilayah</th><th>Komoditas</th><th class="num">Harga</th><th class="num">MtM (%)</th><th>Tipe</th><th>Diupload Oleh</th><th>Tanggal</th></tr></thead>
            <tbody>
                @forelse($riwayat as $d)
                <tr>
                    <td style="white-space:nowrap">{{ $d->periode->nama }}</td>
                    <td>{{ $d->wilayah->nama_wilayah }}</td>
                    <td style="font-weight:600">{{ $d->komoditas->nama_komoditas }}</td>
                    <td class="num">{{ number_format($d->harga_level ?? 0, 2) }}</td>
                    <td class="num {{ (float)$d->inflasi_mtm > 0 ? 'inflasi-naik' : ((float)$d->inflasi_mtm < 0 ? 'inflasi-turun' : '') }}">{{ number_format($d->inflasi_mtm ?? 0, 4) }}</td>
                    <td><span class="badge badge-blue">{{ $d->tipe_indeks }}</span></td>
                    <td style="font-size:0.75rem">{{ $d->uploader?->name ?? '-' }}</td>
                    <td style="font-size:0.75rem">{{ $d->updated_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--color-on-surface-variant)">Belum ada riwayat data harga.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $riwayat->links() }}</div>
</div>
@endsection
