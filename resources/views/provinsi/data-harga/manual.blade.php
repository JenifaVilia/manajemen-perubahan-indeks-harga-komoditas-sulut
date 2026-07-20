@extends('layouts.app')
@section('title', 'Input Manual Harga')
@section('breadcrumb') Data Harga <span>/ Input Manual</span> @endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-title">Input Manual Data Harga</h1>
        <p class="page-subtitle">Formulir isian data harga per komoditas secara manual</p>
    </div>
</div>

<div class="grid-12">
    <!-- Form Input Manual (Left) -->
    <div class="col-span-8">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Form Isian Data
                </div>
            </div>
            <form method="POST" action="{{ route('provinsi.data-harga.manual.simpan') }}" data-confirm="Apakah Anda yakin ingin menyimpan data harga ini?">
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
                                <option value="IHK">IHK (Indeks Harga Konsumen)</option>
                                <option value="IHPB">IHPB (Indeks Harga Perdagangan Besar)</option>
                                <option value="IPP">IPP (Indeks Harga Produsen)</option>
                                <option value="IPH">IPH (Indeks Harga Petani)</option>
                            </select>
                        </div>
                    </div>
                    <hr style="margin:1rem 0;border-color:var(--color-outline-variant)">
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-size:0.95rem;font-weight:600;">Harga Level (Rp) <span class="required">*</span></label>
                        <input type="number" name="harga_level" class="form-control" step="0.01" min="0" value="{{ old('harga_level') }}" required placeholder="Contoh: 14500.00" style="font-size:1.1rem;padding:0.6rem 0.75rem;">
                        <small style="color:var(--color-on-surface-variant);display:block;margin-top:0.35rem;">
                            💡 Sistem akan secara otomatis mengalkulasikan nilai **Perubahan Harga MtM (%)** berdasarkan perbandingan dengan harga level periode sebelumnya.
                        </small>
                    </div>

                    <details style="margin-top:1rem;padding:0.875rem;background:var(--color-surface-variant);border-radius:var(--radius-md);border:1px solid var(--color-outline-variant);">
                        <summary style="cursor:pointer;font-weight:600;color:var(--color-primary);user-select:none;">
                            ⚙️ Angka Resmi BPS & Andil (Opsional / Override Manual)
                        </summary>
                        <div style="margin-top:1rem;">
                            <div class="grid-3">
                                <div class="form-group">
                                    <label class="form-label">MtM (%)</label>
                                    <input type="number" name="inflasi_mtm" class="form-control" step="0.0001" value="{{ old('inflasi_mtm') }}" placeholder="Otomatis / Contoh: 1.25">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">YtD (%)</label>
                                    <input type="number" name="inflasi_ytd" class="form-control" step="0.0001" value="{{ old('inflasi_ytd') }}" placeholder="Otomatis / Contoh: 2.10">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">YoY (%)</label>
                                    <input type="number" name="inflasi_yoy" class="form-control" step="0.0001" value="{{ old('inflasi_yoy') }}" placeholder="Otomatis / Contoh: 5.45">
                                </div>
                            </div>
                            <div class="grid-3" style="margin-top:0.5rem;">
                                <div class="form-group">
                                    <label class="form-label">Andil MtM</label>
                                    <input type="number" name="andil_mtm" class="form-control" step="0.0001" value="{{ old('andil_mtm') }}" placeholder="Contoh: 0.1234">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Andil YtD</label>
                                    <input type="number" name="andil_ytd" class="form-control" step="0.0001" value="{{ old('andil_ytd') }}" placeholder="Contoh: 0.4567">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Andil YoY</label>
                                    <input type="number" name="andil_yoy" class="form-control" step="0.0001" value="{{ old('andil_yoy') }}" placeholder="Contoh: 0.8901">
                                </div>
                            </div>
                        </div>
                    </details>
                </div>
                <div class="card-footer flex justify-between">
                    <a href="{{ route('provinsi.data-harga.index') }}" class="btn btn-ghost">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Data Harga</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info & Bantuan (Right) -->
    <div class="col-span-4">
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Informasi Validasi
                </div>
            </div>
            <div class="card-body" style="font-size:0.8125rem;line-height:1.6;color:var(--color-on-surface-variant);">
                <p style="margin-bottom:0.75rem;color:var(--color-on-surface);"><strong>Validasi Kolom Wajib:</strong></p>
                <ul style="list-style:disc;margin-left:1.25rem;margin-bottom:1rem;display:flex;flex-direction:column;gap:0.375rem;">
                    <li><strong>Periode & Wilayah</strong> harus dipilih dari opsi terdaftar.</li>
                    <li><strong>Komoditas</strong> disesuaikan dengan kode master IHK BPS.</li>
                    <li><strong>Harga Level</strong> harus bernilai numerik desimal positif (lebih dari 0).</li>
                </ul>
                <p style="margin-bottom:0.5rem;color:var(--color-on-surface);"><strong>Catatan Inflasi & Andil:</strong></p>
                <p>Data inflasi dimasukkan langsung dalam persentase desimal. Misalnya, jika inflasi adalah 2.5%, masukkan angka <code>2.5</code> (jangan 0.025).</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                    Menu Cepat
                </div>
            </div>
            <div class="card-body flex flex-col gap-2" style="padding:1rem;">
                <a href="{{ route('provinsi.data-harga.upload') }}" class="btn btn-secondary" style="width:100%;font-size:0.75rem;padding:0.4rem;">
                    📤 Switch ke Upload Excel
                </a>
                <a href="{{ route('provinsi.data-harga.riwayat') }}" class="btn btn-ghost" style="width:100%;font-size:0.75rem;padding:0.4rem;text-align:center;">
                    📋 Lihat Riwayat Upload
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
