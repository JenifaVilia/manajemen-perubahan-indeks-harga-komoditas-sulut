@extends('layouts.app')
@section('title', 'Status Permintaan Komoditas')
@section('breadcrumb') Komoditas <span>/ Status Permintaan</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Status Permintaan Komoditas</h1>
        <p class="page-subtitle">Riwayat pengajuan tambah/hapus komoditas di {{ $wilayah->nama_wilayah }}</p>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Komoditas</th><th>Tipe</th><th>Alasan</th><th>Tanggal</th><th>Status</th><th>Catatan Provinsi</th></tr></thead>
            <tbody>
                @forelse($permintaans as $p)
                @php
                    $badge = match($p->status) {
                        'pending_tambah'  => ['class' => 'badge-yellow', 'label' => 'Pending Tambah'],
                        'pending_hapus'   => ['class' => 'badge-yellow', 'label' => 'Pending Hapus'],
                        'aktif'           => ['class' => 'badge-green',  'label' => 'Disetujui'],
                        'nonaktif'        => ['class' => 'badge-green',  'label' => 'Dihapus'],
                        'ditolak'         => ['class' => 'badge-red',    'label' => 'Ditolak'],
                        default           => ['class' => 'badge-gray',   'label' => $p->status],
                    };
                @endphp
                <tr>
                    <td style="font-weight:600">{{ $p->komoditas->nama_komoditas }}</td>
                    <td><span class="badge badge-blue">{{ str_contains($p->status, 'tambah') ? 'Tambah' : 'Hapus' }}</span></td>
                    <td style="font-size:0.75rem;max-width:250px;">{{ $p->alasan_pengajuan }}</td>
                    <td style="font-size:0.75rem;white-space:nowrap;">{{ $p->requested_at?->format('d M Y') }}</td>
                    <td><span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span></td>
                    <td style="font-size:0.75rem;color:var(--color-on-surface-variant)">{{ $p->catatan_approval ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--color-on-surface-variant)">Belum ada permintaan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $permintaans->links() }}</div>
</div>
@endsection
