<?php

namespace App\Http\Controllers\Provinsi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ManajemenUserController extends Controller
{
    public function index()
    {
        $users = User::with(['wilayah', 'roles'])
            ->orderBy('name')
            ->paginate(20);

        return view('provinsi.manajemen.user.index', compact('users'));
    }

    public function create()
    {
        $wilayahs = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();
        return view('provinsi.manajemen.user.create', compact('wilayahs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'password'    => ['required', 'string', Password::min(8)],
            'wilayah_id'  => ['nullable', 'exists:wilayahs,id'],
            'role'        => ['required', 'in:provinsi,kabupaten_kota'],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah terdaftar.',
        ]);

        $user = User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'wilayah_id' => $data['wilayah_id'] ?? null,
            'is_active'  => true,
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('provinsi.users.index')
            ->with('success', "User '{$user->name}' berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        $wilayahs = Wilayah::kabupatenKota()->aktif()->orderBy('kode_wilayah')->get();
        return view('provinsi.manajemen.user.edit', compact('user', 'wilayahs'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', "unique:users,email,{$user->id}"],
            'wilayah_id' => ['nullable', 'exists:wilayahs,id'],
        ]);

        $user->update($data);

        return redirect()->route('provinsi.users.index')
            ->with('success', "User '{$user->name}' berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 422, 'Tidak dapat menghapus akun sendiri.');

        $nama = $user->name;
        $user->delete();

        return redirect()->route('provinsi.users.index')
            ->with('success', "User '{$nama}' berhasil dihapus.");
    }

    public function resetPassword(User $user)
    {
        $newPassword = Str::random(10);
        $user->update(['password' => Hash::make($newPassword)]);

        return back()->with('success', "Password user '{$user->name}' berhasil direset. Password baru: {$newPassword}");
    }

    public function toggleAktif(User $user)
    {
        abort_if($user->id === auth()->id(), 422, 'Tidak dapat menonaktifkan akun sendiri.');

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "User '{$user->name}' berhasil {$status}.");
    }
}
