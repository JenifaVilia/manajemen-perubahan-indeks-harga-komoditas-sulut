@extends('layouts.app')
@section('title', 'Edit Periode')
@section('breadcrumb') Manajemen <span>/ Kelola Periode / Edit</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Edit Periode — {{ $periode->nama }}</h1>
    </div>
</div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('provinsi.periode.update', $periode) }}">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Bulan</label>
                    <input type="text" class="form-control" value="{{ $periode->nama }}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <span class="periode-badge {{ $periode->status }}">{{ ucfirst($periode->status) }}</span>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tanggal Buka</label>
                    <input type="date" name="tanggal_buka" class="form-control" value="{{ old('tanggal_buka', $periode->tanggal_buka?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Tutup (Deadline)</label>
                    <input type="date" name="tanggal_tutup" class="form-control" value="{{ old('tanggal_tutup', $periode->tanggal_tutup?->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $periode->keterangan) }}</textarea>
            </div>
        </div>
        <div class="card-footer flex justify-between">
            <a href="{{ route('provinsi.periode.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
