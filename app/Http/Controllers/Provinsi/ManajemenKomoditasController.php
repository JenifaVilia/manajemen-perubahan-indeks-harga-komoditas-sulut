<?php

namespace App\Http\Controllers\Provinsi;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use Illuminate\Http\Request;

class ManajemenKomoditasController extends Controller
{
    public function index()
    {
        $komoditas = Komoditas::withCount('dataHargas')
            ->orderBy('kode_komoditas')
            ->paginate(25);

        return view('provinsi.manajemen.komoditas.index', compact('komoditas'));
    }

    public function create()
    {
        return view('provinsi.manajemen.komoditas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_komoditas'  => ['required', 'string', 'max:20', 'unique:komoditas,kode_komoditas'],
            'nama_komoditas'  => ['required', 'string', 'max:255'],
            'satuan'          => ['required', 'string', 'max:50'],
            'kelompok'        => ['required', 'string', 'max:100'],
            'sub_kelompok'    => ['nullable', 'string', 'max:100'],
            'deskripsi'       => ['nullable', 'string', 'max:500'],
        ], [
            'kode_komoditas.required' => 'Kode komoditas wajib diisi.',
            'kode_komoditas.unique'   => 'Kode komoditas sudah ada.',
            'nama_komoditas.required' => 'Nama komoditas wajib diisi.',
            'satuan.required'         => 'Satuan wajib diisi.',
            'kelompok.required'       => 'Kelompok wajib diisi.',
        ]);

        $data['is_active'] = true;
        Komoditas::create($data);

        return redirect()->route('provinsi.komoditas.index')
            ->with('success', "Komoditas '{$data['nama_komoditas']}' berhasil ditambahkan.");
    }

    public function edit(Komoditas $komodita)
    {
        return view('provinsi.manajemen.komoditas.edit', ['komoditas' => $komodita]);
    }

    public function update(Request $request, Komoditas $komodita)
    {
        $data = $request->validate([
            'kode_komoditas'  => ['required', 'string', 'max:20', "unique:komoditas,kode_komoditas,{$komodita->id}"],
            'nama_komoditas'  => ['required', 'string', 'max:255'],
            'satuan'          => ['required', 'string', 'max:50'],
            'kelompok'        => ['required', 'string', 'max:100'],
            'sub_kelompok'    => ['nullable', 'string', 'max:100'],
            'deskripsi'       => ['nullable', 'string', 'max:500'],
            'is_active'       => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $komodita->update($data);

        return redirect()->route('provinsi.komoditas.index')
            ->with('success', "Komoditas '{$komodita->nama_komoditas}' berhasil diperbarui.");
    }

    public function destroy(Komoditas $komodita)
    {
        if ($komodita->dataHargas()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus komoditas yang sudah memiliki data harga.');
        }

        $nama = $komodita->nama_komoditas;
        $komodita->delete();

        return redirect()->route('provinsi.komoditas.index')
            ->with('success', "Komoditas '{$nama}' berhasil dihapus.");
    }
}
