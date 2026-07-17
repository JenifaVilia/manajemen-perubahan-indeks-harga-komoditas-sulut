@extends('layouts.app')
@section('title', 'Kelola Komoditas')
@section('breadcrumb') Manajemen <span>/ Kelola Komoditas</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Kelola Master Komoditas</h1>
        <p class="page-subtitle">Daftar komoditas yang dipantau perubahan harganya</p>
    </div>
    <a href="{{ route('provinsi.komoditas.create') }}" class="btn btn-primary">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Komoditas
    </a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Komoditas</th>
                    <th>Satuan</th>
                    <th>Kelompok</th>
                    <th>Status</th>
                    <th class="num">Data Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($komoditas as $k)
                <tr>
                    <td style="font-family:var(--font-mono);font-weight:600;font-size:0.75rem;">{{ $k->kode_komoditas }}</td>
                    <td style="font-weight:600">{{ $k->nama_komoditas }}</td>
                    <td>{{ $k->satuan }}</td>
                    <td style="font-size:0.75rem;color:var(--color-on-surface-variant)">{{ $k->kelompok }}</td>
                    <td><span class="badge {{ $k->is_active ? 'badge-green' : 'badge-gray' }}">{{ $k->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="num">{{ $k->data_hargas_count }}</td>
                    <td>
                        <div class="flex gap-1">
                            <a href="{{ route('provinsi.komoditas.edit', $k) }}" class="btn btn-sm btn-secondary">Edit</a>
                            @if($k->data_hargas_count === 0)
                            <form method="POST" action="{{ route('provinsi.komoditas.destroy', $k) }}" style="display:inline" onsubmit="return confirm('Hapus komoditas ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--color-on-surface-variant)">Belum ada komoditas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $komoditas->links() }}</div>
</div>
@endsection
