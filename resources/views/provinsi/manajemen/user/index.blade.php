@extends('layouts.app')
@section('title', 'Kelola User')
@section('breadcrumb') Manajemen <span>/ Kelola User</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Kelola User</h1>
        <p class="page-subtitle">Daftar akun pengguna SIPERHARGA Sulut</p>
    </div>
    <a href="{{ route('provinsi.users.create') }}" class="btn btn-primary">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah User
    </a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Wilayah</th>
                    <th>Status</th>
                    <th>Login Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td style="font-weight:600">{{ $u->name }}</td>
                    <td style="font-size:0.75rem;color:var(--color-on-surface-variant)">{{ $u->email }}</td>
                    <td><span class="badge {{ $u->hasRole('provinsi') ? 'badge-blue' : 'badge-yellow' }}">{{ $u->hasRole('provinsi') ? 'Provinsi' : 'Kab/Kota' }}</span></td>
                    <td style="font-size:0.75rem">{{ $u->wilayah?->nama_wilayah ?? '-' }}</td>
                    <td><span class="badge {{ $u->is_active ? 'badge-green' : 'badge-gray' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td style="font-size:0.75rem">{{ $u->last_login?->diffForHumans() ?? 'Belum pernah' }}</td>
                    <td>
                        <div class="flex gap-1">
                            <a href="{{ route('provinsi.users.edit', $u) }}" class="btn btn-sm btn-secondary">Edit</a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('provinsi.users.reset-password', $u) }}" style="display:inline" onsubmit="return confirm('Reset password user ini?')">
                                @csrf
                                <button class="btn btn-sm btn-accent" title="Reset Password">🔑</button>
                            </form>
                            <form method="POST" action="{{ route('provinsi.users.toggle-aktif', $u) }}" style="display:inline">
                                @csrf
                                <button class="btn btn-sm {{ $u->is_active ? 'btn-ghost' : 'btn-primary' }}" title="{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    {{ $u->is_active ? '🚫' : '✅' }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--color-on-surface-variant)">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $users->links() }}</div>
</div>
@endsection
