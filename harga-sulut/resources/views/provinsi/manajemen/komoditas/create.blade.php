@extends('layouts.app')
@section('title', 'Tambah Komoditas')
@section('breadcrumb') Manajemen <span>/ Komoditas / Tambah</span> @endsection

@section('content')
<div class="page-header"><div class="page-header-left"><h1 class="page-title">Tambah Komoditas Baru</h1></div></div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('provinsi.komoditas.store') }}">
        @csrf
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Kode Komoditas <span class="required">*</span></label>
                    <input type="text" name="kode_komoditas" class="form-control {{ $errors->has('kode_komoditas') ? 'is-invalid' : '' }}" value="{{ old('kode_komoditas') }}" placeholder="Cth: 01.01.01">
                    @error('kode_komoditas')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan <span class="required">*</span></label>
                    <input type="text" name="satuan" class="form-control" value="{{ old('satuan') }}" placeholder="Cth: kg">
                    @error('satuan')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Komoditas <span class="required">*</span></label>
                <input type="text" name="nama_komoditas" class="form-control {{ $errors->has('nama_komoditas') ? 'is-invalid' : '' }}" value="{{ old('nama_komoditas') }}" placeholder="Cth: Beras Premium">
                @error('nama_komoditas')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Kelompok <span class="required">*</span></label>
                    <input type="text" name="kelompok" class="form-control" value="{{ old('kelompok') }}" placeholder="Cth: Bahan Makanan">
                    @error('kelompok')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Sub Kelompok</label>
                    <input type="text" name="sub_kelompok" class="form-control" value="{{ old('sub_kelompok') }}" placeholder="Opsional">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi komoditas...">{{ old('deskripsi') }}</textarea>
            </div>
        </div>
        <div class="card-footer flex justify-between">
            <a href="{{ route('provinsi.komoditas.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>
@endsection
