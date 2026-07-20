@extends('layouts.app')
@section('title', 'Upload Data Harga')
@section('breadcrumb') Data Harga <span>/ Upload Excel</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Upload Data Harga via Excel</h1>
        <p class="page-subtitle">Unggah berkas excel rekap data harga komoditas wilayah</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success mb-4">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error mb-4">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>{{ session('error') }}</div>
    </div>
@endif

@if(session('import_errors'))
    <div class="alert alert-warning mb-4">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <strong>Beberapa baris dilewati karena terdapat kesalahan:</strong>
            <ul style="list-style:disc;margin-left:1.5rem;margin-top:0.25rem;">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="grid-12">
    <!-- Form Upload (Left) -->
    <div class="col-span-8">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Formulir Unggah Berkas
                </div>
            </div>
            <form method="POST" action="{{ route('provinsi.data-harga.upload.proses') }}" enctype="multipart/form-data" data-confirm="Apakah Anda yakin ingin mengunggah dan memproses file Excel ini?">
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
                        <div class="form-hint">Maksimal 10MB. Pastikan header tabel pada file Excel sesuai dengan ketentuan template.</div>
                    </div>
                </div>
                <div class="card-footer flex justify-between">
                    <a href="{{ route('provinsi.data-harga.index') }}" class="btn btn-ghost">Batal</a>
                    <button type="submit" class="btn btn-primary">Upload & Proses</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Panduan & Unduh Template (Right) -->
    <div class="col-span-4">
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Unduh Template Excel
                </div>
            </div>
            <div class="card-body">
                <p style="font-size:0.8125rem;line-height:1.5;color:var(--color-on-surface);margin-bottom:1rem;">
                    Gunakan template resmi untuk menghindari kegagalan impor. Template ini sudah disiapkan dengan kode komoditas aktif yang terdaftar di sistem.
                </p>
                <a href="{{ route('provinsi.data-harga.template') }}" class="btn btn-secondary" style="width:100%;">
                    Download Template (.xlsx)
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 01-2 2h0a2 2 0 01-2-2v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Petunjuk Pengisian
                </div>
            </div>
            <div class="card-body" style="font-size:0.75rem;line-height:1.6;color:var(--color-on-surface-variant);">
                <ol style="list-style:decimal;margin-left:1rem;display:flex;flex-direction:column;gap:0.5rem;">
                    <li>Jangan mengubah format header kolom template.</li>
                    <li>Kolom <strong>Kode Komoditas</strong> dan <strong>Harga Level</strong> wajib diisi dan valid.</li>
                    <li>Harga Level harus berupa angka positif.</li>
                    <li>Kolom andil dan inflasi (MtM, YtD, YoY) opsional dan dapat diisi dengan angka desimal (dalam persen, misal 1.2 untuk 1.2%).</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
