@extends('layouts.app')
@section('title', 'Tambah User')
@section('breadcrumb') Manajemen <span>/ User / Tambah</span> @endsection

@section('content')
<div class="page-header"><div class="page-header-left"><h1 class="page-title">Tambah User Baru</h1></div></div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('provinsi.users.store') }}">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Email <span class="required">*</span></label>
                <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" required>
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Password <span class="required">*</span></label>
                <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" required minlength="8">
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
                <div class="form-hint">Minimal 8 karakter</div>
            </div>
            <div class="form-group">
                <label class="form-label">Role <span class="required">*</span></label>
                <select name="role" class="form-control" id="role-select" onchange="toggleWilayah()">
                    <option value="kabupaten_kota" {{ old('role') === 'kabupaten_kota' ? 'selected' : '' }}>Kabupaten/Kota</option>
                    <option value="provinsi" {{ old('role') === 'provinsi' ? 'selected' : '' }}>Provinsi</option>
                </select>
            </div>
            <div class="form-group" id="wilayah-group">
                <label class="form-label">Wilayah <span class="required">*</span></label>
                <select name="wilayah_id" class="form-control">
                    <option value="">— Pilih Wilayah —</option>
                    @foreach($wilayahs as $w)
                        <option value="{{ $w->id }}" {{ old('wilayah_id') == $w->id ? 'selected' : '' }}>{{ $w->tipe === 'kota' ? 'Kota' : 'Kab.' }} {{ $w->nama_wilayah }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-footer flex justify-between">
            <a href="{{ route('provinsi.users.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

<script>
function toggleWilayah() {
    const role = document.getElementById('role-select').value;
    document.getElementById('wilayah-group').style.display = role === 'provinsi' ? 'none' : 'block';
}
document.addEventListener('DOMContentLoaded', toggleWilayah);
</script>
@endsection
