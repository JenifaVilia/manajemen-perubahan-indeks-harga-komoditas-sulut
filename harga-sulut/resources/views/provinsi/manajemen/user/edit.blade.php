@extends('layouts.app')
@section('title', 'Edit User')
@section('breadcrumb') Manajemen <span>/ User / Edit</span> @endsection

@section('content')
<div class="page-header"><div class="page-header-left"><h1 class="page-title">Edit User — {{ $user->name }}</h1></div></div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('provinsi.users.update', $user) }}">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Email <span class="required">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Wilayah</label>
                <select name="wilayah_id" class="form-control">
                    <option value="">— Tidak Ada —</option>
                    @foreach($wilayahs as $w)
                        <option value="{{ $w->id }}" {{ old('wilayah_id', $user->wilayah_id) == $w->id ? 'selected' : '' }}>{{ $w->tipe === 'kota' ? 'Kota' : 'Kab.' }} {{ $w->nama_wilayah }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Role</label>
                <span class="badge {{ $user->hasRole('provinsi') ? 'badge-blue' : 'badge-yellow' }}">{{ $user->hasRole('provinsi') ? 'Provinsi' : 'Kabupaten/Kota' }}</span>
                <div class="form-hint">Role tidak dapat diubah setelah dibuat. Hapus dan buat ulang jika perlu.</div>
            </div>
        </div>
        <div class="card-footer flex justify-between">
            <a href="{{ route('provinsi.users.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
