@extends('layouts.app')
@section('title', 'Kelola Periode')
@section('breadcrumb') Manajemen <span>/ Kelola Periode</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Kelola Periode Rekonsiliasi</h1>
        <p class="page-subtitle">Atur periode pengumpulan data harga dan rekonsiliasi</p>
    </div>
    <a href="{{ route('provinsi.periode.create') }}" class="btn btn-primary">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Periode
    </a>
</div>

@if($periodeAktif)
<div class="alert alert-info mb-4">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
    <div>Periode aktif saat ini: <strong>{{ $periodeAktif->nama }}</strong>. Hanya satu periode yang bisa aktif dalam satu waktu.</div>
</div>
@endif

<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Tanggal Buka</th>
                    <th>Tanggal Tutup</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periodes as $p)
                <tr>
                    <td style="font-weight:600">{{ $p->nama }}</td>
                    <td>{{ $p->tanggal_buka?->format('d M Y') ?? '-' }}</td>
                    <td>{{ $p->tanggal_tutup?->format('d M Y') ?? '-' }}</td>
                    <td>
                        <span class="periode-badge {{ $p->status }}">
                            @if($p->status === 'aktif')<span class="dot"></span>@endif
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td style="font-size:0.75rem">{{ $p->creator?->name ?? '-' }}</td>
                    <td style="font-size:0.75rem;max-width:200px;">{{ Str::limit($p->keterangan, 60) }}</td>
                    <td>
                        <div class="flex gap-1">
                            @if($p->status === 'draft')
                                <a href="{{ route('provinsi.periode.edit', $p) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form method="POST" action="{{ route('provinsi.periode.buka', $p) }}" style="display:inline" onsubmit="return confirm('Buka periode ini? Semua Kab/Kota akan mendapat notifikasi.')">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">Buka</button>
                                </form>
                                <form method="POST" action="{{ route('provinsi.periode.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Hapus periode ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            @elseif($p->status === 'aktif')
                                <a href="{{ route('provinsi.periode.edit', $p) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form method="POST" action="{{ route('provinsi.periode.tutup', $p) }}" style="display:inline" onsubmit="return confirm('Tutup periode ini?')">
                                    @csrf
                                    <button class="btn btn-sm btn-accent">Tutup</button>
                                </form>
                            @else
                                <span style="font-size:0.75rem;color:var(--color-outline)">Selesai</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--color-on-surface-variant)">Belum ada periode.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $periodes->links() }}</div>
</div>
@endsection
