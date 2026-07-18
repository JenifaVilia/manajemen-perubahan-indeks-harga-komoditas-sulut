@extends('layouts.app')
@section('title', 'Permintaan Komoditas')
@section('breadcrumb') Manajemen <span>/ Permintaan Komoditas</span> @endsection

@section('content')
<div class="page-header"><div class="page-header-left"><h1 class="page-title">Review Permintaan Komoditas</h1><p class="page-subtitle">Permintaan tambah/hapus komoditas dari Kabupaten/Kota</p></div></div>

@if($permintaans->count() > 0)
<div class="card mb-4">
    <div class="card-header"><div class="card-title">Permintaan Pending <span class="badge badge-yellow" style="margin-left:4px">{{ $permintaans->total() }}</span></div></div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Wilayah</th><th>Komoditas</th><th>Tipe</th><th>Alasan</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($permintaans as $p)
                <tr>
                    <td style="font-weight:600">{{ $p->wilayah->nama_wilayah }}</td>
                    <td>{{ $p->komoditas->nama_komoditas }}</td>
                    <td><span class="badge {{ $p->status === 'pending_tambah' ? 'badge-green' : 'badge-red' }}">{{ $p->status === 'pending_tambah' ? '+ Tambah' : '- Hapus' }}</span></td>
                    <td style="font-size:0.75rem;max-width:250px;">{{ $p->alasan_pengajuan }}</td>
                    <td style="font-size:0.75rem">{{ $p->requested_at?->format('d M Y') }}</td>
                    <td>
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('provinsi.permintaan-komoditas.approve', $p) }}" onsubmit="return confirm('Setujui permintaan ini?')">@csrf<button class="btn btn-sm btn-primary">Setujui</button></form>
                            <button class="btn btn-sm btn-ghost" style="color:var(--color-error)" onclick="openReject({{ $p->id }})">Tolak</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $permintaans->links() }}</div>
</div>
@else
<div class="card mb-4"><div style="padding:2rem;text-align:center;color:var(--color-on-surface-variant)"><div style="font-size:2rem;margin-bottom:0.5rem;">✅</div>Tidak ada permintaan pending.</div></div>
@endif

@if($riwayat->count() > 0)
<div class="card">
    <div class="card-header"><div class="card-title">Riwayat Keputusan</div></div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Wilayah</th><th>Komoditas</th><th>Tipe</th><th>Status</th><th>Catatan</th><th>Diproses Oleh</th></tr></thead>
            <tbody>
                @foreach($riwayat as $r)
                @php $badge = match($r->status) { 'aktif','nonaktif' => ['badge-green','Disetujui'], 'ditolak' => ['badge-red','Ditolak'], default => ['badge-gray',$r->status] }; @endphp
                <tr>
                    <td>{{ $r->wilayah->nama_wilayah }}</td>
                    <td>{{ $r->komoditas->nama_komoditas }}</td>
                    <td style="font-size:0.75rem">{{ str_contains($r->status, 'tambah') || $r->status === 'aktif' ? 'Tambah' : 'Hapus' }}</td>
                    <td><span class="badge {{ $badge[0] }}">{{ $badge[1] }}</span></td>
                    <td style="font-size:0.75rem">{{ $r->catatan_approval ?? '-' }}</td>
                    <td style="font-size:0.75rem">{{ $r->approver?->name ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Modal Reject --}}
<div class="modal-overlay hidden" id="reject-modal">
    <div class="modal">
        <div class="modal-header"><h2 class="modal-title">Tolak Permintaan</h2><button class="modal-close" onclick="document.getElementById('reject-modal').classList.add('hidden')"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
        <form method="POST" id="reject-form">
            @csrf
            <div class="modal-body">
                <div class="form-group"><label class="form-label">Alasan Penolakan <span class="required">*</span></label>
                    <textarea name="catatan_approval" class="form-control" rows="3" required minlength="5" placeholder="Jelaskan mengapa permintaan ini ditolak..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('reject-modal').classList.add('hidden')">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak Permintaan</button>
            </div>
        </form>
    </div>
</div>
<script>
function openReject(id) {
    document.getElementById('reject-form').action = `/provinsi/permintaan-komoditas/${id}/reject`;
    document.getElementById('reject-modal').classList.remove('hidden');
}
</script>
@endsection
