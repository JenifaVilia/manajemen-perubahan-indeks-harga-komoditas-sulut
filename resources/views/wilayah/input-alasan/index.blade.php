@extends('layouts.app')
@section('title', 'Input Alasan Perubahan Harga')
@section('breadcrumb')
    Input Alasan <span>/ {{ $wilayah->nama_wilayah }}</span>
@endsection

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Input Alasan Perubahan Harga</h1>
        <p class="page-subtitle">{{ $wilayah->tipe === 'kota' ? 'Kota' : 'Kab.' }} {{ $wilayah->nama_wilayah }}
            @if($periodeAktif) &bull; Periode: <strong>{{ $periodeAktif->nama }}</strong> @endif
        </p>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card mb-4">
    <div class="filter-bar">
        <select class="form-control" name="periode_id" onchange="applyFilter()" id="filter-periode" style="width:auto;">
            @foreach($periodes as $p)
                <option value="{{ $p->id }}" {{ $p->id == $periodeId ? 'selected' : '' }}>{{ $p->nama }}</option>
            @endforeach
        </select>
        <div class="tab-nav" style="border:none;gap:0.25rem;flex:1;">
            @foreach(['perlu' => 'Perlu Diisi', 'revisi' => 'Perlu Revisi', 'sudah' => 'Sudah Selesai', 'semua' => 'Semua'] as $key => $label)
            <button class="tab-btn {{ $filter === $key ? 'active' : '' }}" onclick="setFilter('{{ $key }}')">{{ $label }}</button>
            @endforeach
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Daftar Komoditas — {{ $dataHargas->count() }} komoditas
        </div>
    </div>

    @if($dataHargas->count() > 0)
    <div class="table-wrapper">
        <table class="data-table" id="alasan-table">
            <thead>
                <tr>
                    <th>Komoditas</th>
                    <th>Kelompok</th>
                    <th class="num">Harga Level</th>
                    <th class="num">MtM (%)</th>
                    <th class="num">Andil MtM</th>
                    <th>Status Alasan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataHargas as $dh)
                @php
                    $mtm = (float) $dh->inflasi_mtm;
                    $statusAlasan = $dh->status_alasan ?? 'belum';
                    $badge = match($statusAlasan) {
                        'disetujui' => ['class' => 'badge-green', 'label' => 'Disetujui'],
                        'submitted' => ['class' => 'badge-yellow', 'label' => 'Menunggu Review'],
                        'revisi'    => ['class' => 'badge-red', 'label' => 'Perlu Revisi'],
                        'draft'     => ['class' => 'badge-blue', 'label' => 'Draft'],
                        default     => ['class' => 'badge-gray', 'label' => 'Belum Diisi'],
                    };
                @endphp
                <tr>
                    <td style="font-weight:600">{{ $dh->komoditas->nama_komoditas }}</td>
                    <td style="color:var(--color-on-surface-variant);font-size:0.75rem;">{{ $dh->komoditas->kelompok }}</td>
                    <td class="num">{{ number_format($dh->harga_level ?? 0, 2) }}</td>
                    <td class="num {{ $mtm > 0 ? 'inflasi-naik' : ($mtm < 0 ? 'inflasi-turun' : 'inflasi-stabil') }}">
                        {{ $mtm > 0 ? '+' : '' }}{{ number_format($mtm, 4) }}%
                    </td>
                    <td class="num" style="color:var(--color-on-surface-variant)">{{ number_format($dh->andil_mtm ?? 0, 4) }}</td>
                    <td>
                        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        @if($statusAlasan === 'revisi' && $dh->alasan_record?->catatan_provinsi)
                            <div style="font-size:0.6875rem;color:var(--color-error);margin-top:2px;" title="{{ $dh->alasan_record->catatan_provinsi }}">
                                📝 Ada catatan revisi
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($dh->alasan_record)
                            <a href="{{ route('wilayah.input-alasan.show', $dh->alasan_record->id) }}" class="btn btn-sm btn-secondary">
                                {{ in_array($statusAlasan, ['revisi','draft']) ? 'Edit' : 'Lihat' }}
                            </a>
                        @elseif($dh->isSignifikan())
                            <button class="btn btn-sm btn-primary" onclick="openAlasanModal({{ $dh->komoditas_id }}, '{{ addslashes($dh->komoditas->nama_komoditas) }}', {{ $mtm }})">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Isi Alasan
                            </button>
                        @else
                            <span style="font-size:0.75rem;color:var(--color-outline)">Tidak perlu</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="padding:3rem;text-align:center;color:var(--color-on-surface-variant)">
        <div style="font-size:2.5rem;margin-bottom:0.75rem;">📋</div>
        <div style="font-size:0.9375rem;font-weight:600;color:var(--color-on-surface);margin-bottom:0.25rem;">Tidak ada data</div>
        <div style="font-size:0.8125rem;">Belum ada data harga yang diupload untuk periode ini, atau semua sudah selesai.</div>
    </div>
    @endif
</div>

{{-- Modal Input Alasan --}}
<div class="modal-overlay hidden" id="alasan-modal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h2 class="modal-title" id="modal-komoditas-title">Input Alasan Perubahan Harga</h2>
            <button class="modal-close" onclick="closeModal()">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('wilayah.input-alasan.store') }}" id="alasan-form">
            @csrf
            <input type="hidden" name="periode_id" value="{{ $periodeId }}">
            <input type="hidden" name="komoditas_id" id="modal-komoditas-id">

            <div class="modal-body">
                <div id="modal-inflasi-info" style="margin-bottom:1rem;padding:0.75rem;background:var(--color-surface-low);border-radius:var(--radius-lg);font-size:0.8125rem;"></div>

                <div class="form-group">
                    <label class="form-label">Alasan Perubahan Harga <span class="required">*</span></label>
                    <textarea name="alasan" class="form-control" rows="4" placeholder="Jelaskan secara rinci penyebab perubahan harga komoditas ini di wilayah Anda... (minimal 20 karakter)" required minlength="20"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Faktor Pendorong</label>
                    <div class="checkbox-group">
                        @foreach($faktors as $key => $label)
                        <label class="checkbox-pill">
                            <input type="checkbox" name="faktor_pendorong[]" value="{{ $key }}">
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Rekomendasi / Tindak Lanjut <span style="color:var(--color-outline);font-weight:400">(opsional)</span></label>
                    <textarea name="rekomendasi" class="form-control" rows="2" placeholder="Rekomendasi kebijakan atau tindak lanjut yang disarankan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
                <button type="submit" name="action" value="draft" class="btn btn-secondary">Simpan Draft</button>
                <button type="submit" name="action" value="submit" class="btn btn-primary">Kirim ke Provinsi</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function applyFilter() {
        const p = document.getElementById('filter-periode').value;
        window.location.href = `{{ url()->current() }}?periode_id=${p}&filter={{ $filter }}`;
    }

    function setFilter(f) {
        window.location.href = `{{ url()->current() }}?periode_id={{ $periodeId }}&filter=${f}`;
    }

    function openAlasanModal(komoditasId, nama, mtm) {
        document.getElementById('modal-komoditas-id').value = komoditasId;
        document.getElementById('modal-komoditas-title').textContent = 'Alasan: ' + nama;
        const color = mtm > 0 ? 'var(--color-error)' : 'var(--color-success)';
        const arrow = mtm > 0 ? '↑ Naik' : '↓ Turun';
        document.getElementById('modal-inflasi-info').innerHTML = `
            Komoditas: <strong>${nama}</strong> &bull;
            Perubahan MtM: <strong style="color:${color}">${mtm > 0 ? '+' : ''}${mtm.toFixed(4)}%</strong> <span style="color:${color}">${arrow}</span>
        `;
        document.getElementById('alasan-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('alasan-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close on overlay click
    document.getElementById('alasan-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endpush
