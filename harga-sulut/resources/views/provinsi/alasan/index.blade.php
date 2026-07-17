@extends('layouts.app')
@section('title', 'Monitor Alasan Perubahan Harga')
@section('breadcrumb') Monitor Alasan <span>/ Semua Wilayah</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Monitor Alasan Perubahan Harga</h1>
        <p class="page-subtitle">Semua alasan dari 15 Kabupaten/Kota Sulawesi Utara</p>
    </div>
    <a href="{{ route('provinsi.ekspor.alasan') }}" class="btn btn-secondary">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Ekspor Excel
    </a>
</div>

{{-- Summary Cards --}}
<div class="kpi-grid mb-6" style="grid-template-columns:repeat(4,1fr)">
    <div class="kpi-card accent-blue"><div class="kpi-label">Total</div><div class="kpi-value">{{ $summary['total'] }}</div></div>
    <div class="kpi-card accent-gold"><div class="kpi-label">Menunggu Review</div><div class="kpi-value">{{ $summary['submitted'] }}</div></div>
    <div class="kpi-card accent-green"><div class="kpi-label">Disetujui</div><div class="kpi-value">{{ $summary['disetujui'] }}</div></div>
    <div class="kpi-card accent-red"><div class="kpi-label">Perlu Revisi</div><div class="kpi-value">{{ $summary['revisi'] }}</div></div>
</div>

{{-- Filters --}}
<div class="card mb-4">
    <form method="GET" class="filter-bar" id="filter-form">
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
        <select name="status" class="form-control" style="width:auto" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>Menunggu Review</option>
            <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="revisi"    {{ $status === 'revisi'    ? 'selected' : '' }}>Perlu Revisi</option>
            <option value="draft"     {{ $status === 'draft'     ? 'selected' : '' }}>Draft</option>
        </select>
        <input type="text" name="search" class="form-control" style="width:200px" placeholder="Cari komoditas..." value="{{ $search }}">
        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
        <a href="{{ route('provinsi.alasan.index') }}" class="btn btn-ghost btn-sm">Reset</a>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Komoditas</th>
                    <th>Wilayah</th>
                    <th>Periode</th>
                    <th>Alasan (Ringkasan)</th>
                    <th>Faktor Pendorong</th>
                    <th>Diinput Oleh</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alasans as $a)
                @php $badge = $a->status_badge; @endphp
                <tr>
                    <td style="font-weight:600">{{ $a->komoditas->nama_komoditas }}</td>
                    <td>{{ $a->wilayah->nama_wilayah }}</td>
                    <td style="white-space:nowrap">{{ $a->periode->nama }}</td>
                    <td style="max-width:280px;font-size:0.75rem;">{{ Str::limit($a->alasan, 80) }}</td>
                    <td style="font-size:0.75rem;color:var(--color-on-surface-variant)">{{ $a->faktors_label }}</td>
                    <td style="font-size:0.75rem">{{ $a->submitter?->name ?? '-' }}</td>
                    <td><span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span></td>
                    <td>
                        <a href="{{ route('provinsi.alasan.show', $a) }}" class="btn btn-sm btn-secondary">Detail</a>
                        @if($a->status === 'submitted')
                        <form method="POST" action="{{ route('provinsi.alasan.setujui', $a) }}" style="display:inline" onsubmit="return confirm('Setujui alasan ini?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">Setujui</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:var(--color-on-surface-variant)">Tidak ada data alasan ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $alasans->links() }}
    </div>
</div>
@endsection
