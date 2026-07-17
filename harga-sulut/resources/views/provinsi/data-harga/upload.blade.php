@extends('layouts.app')
@section('title', 'Upload Data Harga')
@section('breadcrumb') Data Harga <span>/ Upload Excel</span> @endsection

@section('content')
<div class="page-header"><div class="page-header-left"><h1 class="page-title">Upload Data Harga via Excel</h1></div></div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('provinsi.data-harga.upload.proses') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Periode <span class="required">*</span></label>
                    <select name="periode_id" class="form-control" required>
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Wilayah <span class="required">*</span></label>
                    <select name="wilayah_id" class="form-control" required>
                        @foreach(\App\Models\Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get() as $w)
                            <option value="{{ $w->id }}">{{ $w->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Tipe Indeks <span class="required">*</span></label>
                <select name="tipe_indeks" class="form-control" required>
                    <option value="IHK">IHK (Indeks Harga Konsumen)</option>
                    <option value="IHPB">IHPB (Indeks Harga Perdagangan Besar)</option>
                    <option value="IPP">IPP (Indeks Harga Produsen)</option>
                    <option value="IPH">IPH (Indeks Harga Petani)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">File Excel (.xlsx) <span class="required">*</span></label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                @error('file')<div class="form-error">{{ $message }}</div>@enderror
                <div class="form-hint">Maksimal 10MB. Gunakan template yang sudah disediakan.</div>
            </div>
        </div>
        <div class="card-footer flex justify-between">
            <a href="{{ route('provinsi.data-harga.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Upload & Proses</button>
        </div>
    </form>
</div>
@endsection
