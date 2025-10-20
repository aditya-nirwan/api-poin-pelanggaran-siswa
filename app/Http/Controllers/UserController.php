<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $users = User::whereIn('role', ['admin', 'kepsek', 'guru'])
            ->orderBy('nama', 'asc')
            ->paginate($perPage);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,guru,kepsek',
            'foto'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $fotoName = null;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $foto->storeAs('public/users', $foto->hashName());
            $fotoName = $foto->hashName();
        }

        $user = User::create([
            'nama'     => $validatedData['nama'],
            'email'    => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role'     => $validatedData['role'],
            'foto'     => $fotoName,
        ]);

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'user' => $user
        ], 201);
    }

    public function show(User $user)
    {
        return $user;
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'role' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->nama  = $request->nama ?? $user->nama;
        $user->email = $request->email ?? $user->email;
        $user->role  = $request->role ?? $user->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            $fotoLama = $user->getRawOriginal('foto');
            if ($fotoLama && Storage::exists('public/users/' . $fotoLama)) {
                Storage::delete('public/users/' . $fotoLama);
            }

            $fotoBaru = $request->file('foto');
            $fotoBaru->storeAs('public/users', $fotoBaru->hashName());
            $user->foto = $fotoBaru->hashName();
        }

        $user->save();

        return response()->json(['message' => 'User berhasil diupdate']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $fotoNama = $user->getRawOriginal('foto');

        if ($fotoNama && Storage::exists('public/users/' . $fotoNama)) {
            Storage::delete('public/users/' . $fotoNama);
        }

        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus']);
    }

    public function search(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->role && $request->role !== 'all') {
            $query->where('role', $request->role);
        } else {
            $query->whereIn('role', ['admin', 'guru', 'kepsek']);
        }

        return response()->json(
            $query->orderBy('nama', 'asc')->paginate($request->get('per_page', 5))
        );
    }
}
