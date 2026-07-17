@extends('layouts.app')
@section('title', 'Tambah Periode')
@section('breadcrumb') Manajemen <span>/ Kelola Periode / Tambah</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Tambah Periode Baru</h1>
        <p class="page-subtitle">Buat periode rekonsiliasi baru</p>
    </div>
</div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('provinsi.periode.store') }}">
        @csrf
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Bulan <span class="required">*</span></label>
                    <select name="bulan" class="form-control {{ $errors->has('bulan') ? 'is-invalid' : '' }}">
                        @foreach(\App\Models\Periode::$namaBulan as $num => $nama)
                            <option value="{{ $num }}" {{ old('bulan', now()->month) == $num ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                    @error('bulan')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun <span class="required">*</span></label>
                    <input type="number" name="tahun" class="form-control" value="{{ old('tahun', now()->year) }}" min="2020" max="2030">
                    @error('tahun')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Tanggal Buka</label>
                    <input type="date" name="tanggal_buka" class="form-control" value="{{ old('tanggal_buka') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Tutup (Deadline)</label>
                    <input type="date" name="tanggal_tutup" class="form-control" value="{{ old('tanggal_tutup') }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan untuk periode ini...">{{ old('keterangan') }}</textarea>
            </div>
        </div>
        <div class="card-footer flex justify-between">
            <a href="{{ route('provinsi.periode.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Periode</button>
        </div>
    </form>
</div>
@endsection
