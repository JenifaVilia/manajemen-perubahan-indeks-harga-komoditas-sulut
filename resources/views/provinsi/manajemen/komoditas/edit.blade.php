@extends('layouts.app')
@section('title', 'Edit Komoditas')
@section('breadcrumb') Manajemen <span>/ Komoditas / Edit</span> @endsection

@section('content')
<div class="page-header"><div class="page-header-left"><h1 class="page-title">Edit Komoditas — {{ $komoditas->nama_komoditas }}</h1></div></div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('provinsi.komoditas.update', $komoditas) }}">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Kode Komoditas <span class="required">*</span></label>
                    <input type="text" name="kode_komoditas" class="form-control" value="{{ old('kode_komoditas', $komoditas->kode_komoditas) }}">
                    @error('kode_komoditas')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan <span class="required">*</span></label>
                    <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $komoditas->satuan) }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Komoditas <span class="required">*</span></label>
                <input type="text" name="nama_komoditas" class="form-control" value="{{ old('nama_komoditas', $komoditas->nama_komoditas) }}">
                @error('nama_komoditas')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Kelompok <span class="required">*</span></label>
                    <input type="text" name="kelompok" class="form-control" value="{{ old('kelompok', $komoditas->kelompok) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Sub Kelompok</label>
                    <input type="text" name="sub_kelompok" class="form-control" value="{{ old('sub_kelompok', $komoditas->subkelompok) }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <label class="checkbox-pill" style="width:fit-content;">
                    <input type="checkbox" name="is_active" value="1" {{ $komoditas->is_active ? 'checked' : '' }}>
                    Aktif
                </label>
            </div>
        </div>
        <div class="card-footer flex justify-between">
            <a href="{{ route('provinsi.komoditas.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
