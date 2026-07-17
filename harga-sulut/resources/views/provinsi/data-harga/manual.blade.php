@extends('layouts.app')
@section('title', 'Input Manual Harga')
@section('breadcrumb') Data Harga <span>/ Input Manual</span> @endsection

@section('content')
<div class="page-header"><div class="page-header-left"><h1 class="page-title">Input Manual Data Harga</h1></div></div>

<div class="card" style="max-width:720px;">
    <form method="POST" action="{{ route('provinsi.data-harga.manual.simpan') }}">
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
                        @foreach($wilayahs as $w)
                            <option value="{{ $w->id }}">{{ $w->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Komoditas <span class="required">*</span></label>
                    <select name="komoditas_id" class="form-control" required>
                        @foreach($komoditas as $k)
                            <option value="{{ $k->id }}">{{ $k->kode_komoditas }} — {{ $k->nama_komoditas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Indeks <span class="required">*</span></label>
                    <select name="tipe_indeks" class="form-control" required>
                        <option value="IHK">IHK</option>
                        <option value="IHPB">IHPB</option>
                        <option value="IPP">IPP</option>
                        <option value="IPH">IPH</option>
                    </select>
                </div>
            </div>
            <hr style="margin:1rem 0;border-color:var(--color-outline-variant)">
            <div class="form-group">
                <label class="form-label">Harga Level (Rp) <span class="required">*</span></label>
                <input type="number" name="harga_level" class="form-control" step="0.01" min="0" value="{{ old('harga_level') }}" required>
            </div>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">MtM (%)</label>
                    <input type="number" name="inflasi_mtm" class="form-control" step="0.0001" value="{{ old('inflasi_mtm') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">YtD (%)</label>
                    <input type="number" name="inflasi_ytd" class="form-control" step="0.0001" value="{{ old('inflasi_ytd') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">YoY (%)</label>
                    <input type="number" name="inflasi_yoy" class="form-control" step="0.0001" value="{{ old('inflasi_yoy') }}">
                </div>
            </div>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Andil MtM</label>
                    <input type="number" name="andil_mtm" class="form-control" step="0.0001" value="{{ old('andil_mtm') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Andil YtD</label>
                    <input type="number" name="andil_ytd" class="form-control" step="0.0001" value="{{ old('andil_ytd') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Andil YoY</label>
                    <input type="number" name="andil_yoy" class="form-control" step="0.0001" value="{{ old('andil_yoy') }}">
                </div>
            </div>
        </div>
        <div class="card-footer flex justify-between">
            <a href="{{ route('provinsi.data-harga.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Data Harga</button>
        </div>
    </form>
</div>
@endsection
