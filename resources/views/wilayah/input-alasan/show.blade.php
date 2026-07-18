@extends('layouts.app')
@section('title', 'Detail Alasan')
@section('breadcrumb') Input Alasan <span>/ Detail</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Detail Alasan — {{ $alasan->komoditas?->nama_komoditas }}</h1>
        <p class="page-subtitle">{{ $alasan->periode?->nama }}</p>
    </div>
    <a href="{{ route('wilayah.input-alasan.index') }}" class="btn btn-ghost">← Kembali</a>
</div>

<div class="grid-12">
    <div class="col-span-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-title">Alasan Perubahan Harga</div>
                <span class="badge badge-{{ $alasan->status_badge['color'] }}">{{ $alasan->status_badge['label'] }}</span>
            </div>

            @if(in_array($alasan->status, ['draft', 'revisi']))
            {{-- Editable Form --}}
            <form method="POST" action="{{ route('wilayah.input-alasan.update', $alasan) }}">
                @csrf @method('PUT')
                <div class="card-body">
                    @if($alasan->catatan_provinsi)
                    <div style="margin-bottom:1rem;padding:0.75rem;background:rgba(186,26,26,0.06);border-radius:var(--radius-lg);border:1px solid rgba(186,26,26,0.15);">
                        <label class="form-label" style="color:var(--color-error)">📝 Catatan Revisi dari Provinsi:</label>
                        <p style="font-size:0.8125rem;">{{ $alasan->catatan_provinsi }}</p>
                    </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">Alasan Perubahan Harga <span class="required">*</span></label>
                        <textarea name="alasan" class="form-control" rows="4" required minlength="20">{{ old('alasan', $alasan->alasan) }}</textarea>
                        @error('alasan')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Faktor Pendorong</label>
                        <div class="checkbox-group">
                            @foreach($faktors as $key => $label)
                            <label class="checkbox-pill">
                                <input type="checkbox" name="faktor_pendorong[]" value="{{ $key }}" {{ in_array($key, $alasan->faktor_pendorong ?? []) ? 'checked' : '' }}>
                                {{ $label }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Rekomendasi</label>
                        <textarea name="rekomendasi" class="form-control" rows="2">{{ old('rekomendasi', $alasan->rekomendasi) }}</textarea>
                    </div>
                </div>
                <div class="card-footer flex justify-between">
                    <button type="submit" class="btn btn-secondary">Simpan Draft</button>
                    <div class="flex gap-2">
                        <a href="{{ route('wilayah.input-alasan.index') }}" class="btn btn-ghost">Batal</a>
                    </div>
                </div>
            </form>
            <div style="padding:0 1.25rem 1.25rem;">
                <form method="POST" action="{{ route('wilayah.input-alasan.submit', $alasan) }}" onsubmit="return confirm('Kirim alasan ini ke Provinsi? Pastikan sudah benar.')">
                    @csrf
                    <button class="btn btn-primary" style="width:100%;">Kirim ke Provinsi</button>
                </form>
            </div>
            @else
            {{-- Read-only --}}
            <div class="card-body">
                <div style="font-size:0.875rem;line-height:1.7;">{{ $alasan->alasan }}</div>
                @if($alasan->faktor_pendorong)
                <div style="margin-top:1rem;">
                    <label class="form-label">Faktor Pendorong</label>
                    <div class="flex gap-1" style="flex-wrap:wrap;">
                        @foreach($alasan->faktor_pendorong as $f)
                            <span class="badge badge-blue">{{ $faktors[$f] ?? $f }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($alasan->rekomendasi)
                <div style="margin-top:1rem;"><label class="form-label">Rekomendasi</label><p style="font-size:0.8125rem;color:var(--color-on-surface-variant)">{{ $alasan->rekomendasi }}</p></div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="col-span-4">
        <div class="card">
            <div class="card-header"><div class="card-title">Data Harga</div></div>
            <div class="card-body" style="font-size:0.8125rem;">
                @if($dataHarga)
                <table style="width:100%;">
                    <tr><td style="color:var(--color-on-surface-variant)">Harga</td><td style="text-align:right;font-weight:600">{{ number_format($dataHarga->harga_level ?? 0, 2) }}</td></tr>
                    <tr><td style="color:var(--color-on-surface-variant)">MtM</td><td style="text-align:right;font-weight:700;color:{{ (float)$dataHarga->inflasi_mtm > 0 ? 'var(--color-error)' : 'var(--color-success)' }}">{{ number_format($dataHarga->inflasi_mtm ?? 0, 4) }}%</td></tr>
                    <tr><td style="color:var(--color-on-surface-variant)">YtD</td><td style="text-align:right">{{ number_format($dataHarga->inflasi_ytd ?? 0, 4) }}%</td></tr>
                    <tr><td style="color:var(--color-on-surface-variant)">YoY</td><td style="text-align:right">{{ number_format($dataHarga->inflasi_yoy ?? 0, 4) }}%</td></tr>
                </table>
                @else
                <p style="color:var(--color-outline)">Tidak ada data.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
