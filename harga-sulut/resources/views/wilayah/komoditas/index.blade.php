@extends('layouts.app')
@section('title', 'Komoditas Wilayah')
@section('breadcrumb') Komoditas <span>/ {{ $wilayah->nama_wilayah }}</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Komoditas Wilayah — {{ $wilayah->nama_wilayah }}</h1>
        <p class="page-subtitle">Kelola komoditas yang dipantau di wilayah Anda</p>
    </div>
</div>

{{-- Komoditas Aktif --}}
<div class="card mb-4">
    <div class="card-header">
        <div class="card-title">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
            Komoditas Aktif
            <span class="badge badge-green" style="margin-left:4px">{{ $komoditasAktif->count() }}</span>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Kode</th><th>Nama</th><th>Satuan</th><th>Kelompok</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($komoditasAktif as $kw)
                <tr>
                    <td style="font-family:var(--font-mono);font-size:0.75rem;">{{ $kw->komoditas->kode_komoditas }}</td>
                    <td style="font-weight:600">{{ $kw->komoditas->nama_komoditas }}</td>
                    <td>{{ $kw->komoditas->satuan }}</td>
                    <td style="font-size:0.75rem;color:var(--color-on-surface-variant)">{{ $kw->komoditas->kelompok }}</td>
                    <td>
                        <button class="btn btn-sm btn-ghost" onclick="openHapusModal({{ $kw->id }}, '{{ addslashes($kw->komoditas->nama_komoditas) }}')" style="color:var(--color-error)">Ajukan Hapus</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:1.5rem;color:var(--color-on-surface-variant)">Belum ada komoditas aktif.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Ajukan Tambah --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Ajukan Tambah Komoditas
        </div>
    </div>
    <form method="POST" action="{{ route('wilayah.komoditas.ajukan-tambah') }}">
        @csrf
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Pilih Komoditas <span class="required">*</span></label>
                    <select name="komoditas_id" class="form-control" required>
                        <option value="">— Pilih —</option>
                        @foreach($komoditasTersedia as $k)
                            <option value="{{ $k->id }}">{{ $k->kode_komoditas }} — {{ $k->nama_komoditas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Alasan Pengajuan <span class="required">*</span></label>
                    <textarea name="alasan_pengajuan" class="form-control" rows="2" placeholder="Jelaskan alasan mengapa komoditas ini perlu ditambahkan..." required minlength="10"></textarea>
                    @error('alasan_pengajuan')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="card-footer" style="text-align:right;">
            <button type="submit" class="btn btn-primary">Ajukan Penambahan</button>
        </div>
    </form>
</div>

{{-- Modal Hapus --}}
<div class="modal-overlay hidden" id="hapus-modal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">Ajukan Penghapusan Komoditas</h2>
            <button class="modal-close" onclick="closeHapusModal()"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form method="POST" id="hapus-form">
            @csrf
            <div class="modal-body">
                <p style="margin-bottom:1rem;font-size:0.875rem;">Komoditas: <strong id="hapus-nama"></strong></p>
                <div class="form-group">
                    <label class="form-label">Alasan Penghapusan <span class="required">*</span></label>
                    <textarea name="alasan_pengajuan" class="form-control" rows="3" required minlength="10" placeholder="Jelaskan mengapa komoditas ini perlu dihapus dari wilayah Anda..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeHapusModal()">Batal</button>
                <button type="submit" class="btn btn-danger">Ajukan Penghapusan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openHapusModal(kwId, nama) {
    document.getElementById('hapus-form').action = `/wilayah/komoditas/${kwId}/ajukan-hapus`;
    document.getElementById('hapus-nama').textContent = nama;
    document.getElementById('hapus-modal').classList.remove('hidden');
}
function closeHapusModal() { document.getElementById('hapus-modal').classList.add('hidden'); }
document.getElementById('hapus-modal')?.addEventListener('click', e => { if(e.target.id === 'hapus-modal') closeHapusModal(); });
</script>
@endsection
