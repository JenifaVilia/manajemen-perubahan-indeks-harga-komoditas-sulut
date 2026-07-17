@extends('layouts.app')
@section('title', 'Notifikasi')
@section('breadcrumb') Notifikasi @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Semua Notifikasi</h1>
    </div>
    <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
        @csrf
        <button class="btn btn-secondary btn-sm">Tandai Semua Sudah Dibaca</button>
    </form>
</div>

<div class="card">
    @php
        $notifikasis = auth()->user()->notifikasis()->latest()->paginate(20);
    @endphp
    @forelse($notifikasis as $n)
    <div class="needs-input-row" style="{{ !$n->is_read ? 'background:rgba(0,102,153,0.04);border-left:3px solid var(--color-primary)' : '' }}">
        <div style="flex:1;min-width:0;">
            <div style="font-size:0.8125rem;font-weight:{{ $n->is_read ? '500' : '700' }};color:var(--color-on-surface);">
                {{ $n->judul }}
            </div>
            <div style="font-size:0.75rem;color:var(--color-on-surface-variant);margin-top:2px;">{{ $n->pesan }}</div>
            <div style="font-size:0.6875rem;color:var(--color-outline);margin-top:4px;">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        <div style="display:flex;align-items:center;gap:0.5rem;">
            @if($n->url)
                <a href="{{ $n->url }}" class="btn btn-sm btn-primary"
                   onclick="event.preventDefault(); fetch('{{ route('notifikasi.baca', $n->id) }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>window.location='{{ $n->url }}')">
                    Lihat
                </a>
            @endif
            @if(!$n->is_read)
            <form method="POST" action="{{ route('notifikasi.baca', $n->id) }}" style="display:inline">
                @csrf
                <button class="btn btn-sm btn-ghost">✓</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div style="padding:3rem;text-align:center;color:var(--color-on-surface-variant)">
        <div style="font-size:2.5rem;margin-bottom:0.5rem;">🔔</div>
        <div style="font-weight:600">Belum ada notifikasi</div>
    </div>
    @endforelse
    <div class="card-footer">{{ $notifikasis->links() }}</div>
</div>
@endsection
