<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Violation;
use App\Models\Guidance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $students = Student::whereHas('user', function ($q) {
            $q->where('role', 'siswa');
        })
            ->with('user')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $students->getCollection()->transform(function ($student) {
            return [
                'id' => $student->id,
                'user_id' => $student->user_id,
                'nama' => $student->user->nama ?? '',
                'email' => $student->user->email ?? '',
                'foto' => $student->user->foto ?? null,
                'kelas' => $student->kelas,
                'no_hp' => $student->no_hp,
                'total_poin' => $student->total_poin,
            ];
        });

        return response()->json($students);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'kelas'    => 'required|string|max:50',
            'no_hp'    => 'nullable|string|max:20',
            'foto'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $fotoName = null;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $foto->storeAs('public/users', $foto->hashName());
            $fotoName = $foto->hashName();
        }

        $user = User::create([
            'nama'     => $validated['nama'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'siswa',
            'foto'     => $fotoName, // simpan nama file saja
        ]);

        // // Simpan foto jika ada
        // $fotoPath = null;
        // if ($request->hasFile('foto')) {
        //     $fotoPath = $request->file('foto')->store('public/users');
        // }

        // // Buat user baru
        // $user = User::create([
        //     'nama'     => $validated['nama'],
        //     'email'    => $validated['email'],
        //     'password' => Hash::make($validated['password']),
        //     'role'     => 'siswa',
        //     'foto'     => $fotoPath ? Storage::url($fotoPath) : null,
        // ]);

        // Buat student baru
        $student = Student::create([
            'user_id'    => $user->id,
            'kelas'      => $validated['kelas'],
            'no_hp'      => $validated['no_hp'] ?? null,
            'total_poin' => 0,
        ]);

        return response()->json([
            'message' => 'Siswa berhasil ditambahkan',
            'student' => [
                'id' => $student->id,
                'user_id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'foto' => $user->foto,
                'kelas' => $student->kelas,
                'no_hp' => $student->no_hp,
                'total_poin' => $student->total_poin,
            ],
        ], 201);
    }

    public function show($id)
    {
        $student = Student::with('user')->findOrFail($id);

        return response()->json([
            'id' => $student->id,
            'user_id' => $student->user_id,
            'nama' => $student->user->nama,
            'email' => $student->user->email,
            'foto' => $student->user->foto,
            'kelas' => $student->kelas,
            'no_hp' => $student->no_hp,
            'total_poin' => $student->total_poin,
        ]);
    }

    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);
        $user = $student->user;

        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'kelas'    => 'required|string|max:50',
            'no_hp'    => 'nullable|string|max:20',
            'foto'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                $oldPath = 'public/users/' . $user->foto;
                Storage::delete($oldPath);
            }

            $foto = $request->file('foto');
            $foto->storeAs('public/users', $foto->hashName());
            $user->foto = $foto->hashName();
        }

        // Update user
        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        // Update student
        $student->update([
            'kelas' => $validated['kelas'],
            'no_hp' => $validated['no_hp'] ?? null,
        ]);

        return response()->json(['message' => 'Siswa berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $student = Student::with('user')->findOrFail($id);
        $user = $student->user;

        $fotoNama = $user->getRawOriginal('foto');

        if ($fotoNama && Storage::exists('public/users/' . $fotoNama)) {
            Storage::delete('public/users/' . $fotoNama);
        }

        $student->delete();
        $user->delete();

        return response()->json(['message' => 'Siswa berhasil dihapus']);
    }

    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $kelas = $request->get('kelas', null);
        $perPage = $request->get('per_page', 10);

        $students = Student::with(['user' => function ($q) {
            $q->select('id', 'nama', 'email', 'foto');
        }])
            ->join('users', 'students.user_id', '=', 'users.id')
            ->where('users.role', 'siswa')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('users.nama', 'like', "%$query%")
                        ->orWhere('users.email', 'like', "%$query%");
                });
            })
            ->when($kelas && $kelas !== 'all', function ($q) use ($kelas) {
                $q->where('students.kelas', $kelas);
            })
            ->orderBy('users.nama', 'asc')
            ->select([
                'students.*',
                'users.nama',
                'users.email',
                'users.foto'
            ])
            ->paginate($perPage);

        return response()->json([
            'current_page' => $students->currentPage(),
            'last_page' => $students->lastPage(),
            'data' => $students->items(),
        ]);
    }

    public function searchAll(Request $request)
    {
        $query = $request->get('query', '');
        $kelas = $request->get('kelas');

        $students = Student::join('users', 'students.user_id', '=', 'users.id')
            ->where('users.role', 'siswa')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('users.nama', 'like', "%$query%")
                        ->orWhere('users.email', 'like', "%$query%");
                });
            })
            ->when($kelas, function ($q) use ($kelas) {
                $q->where('students.kelas', $kelas);
            })
            ->orderBy('users.nama', 'asc')
            ->select('students.*')
            ->with('user')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'nama' => $student->user->nama ?? '',
                    'email' => $student->user->email ?? '',
                    'foto' => $student->user->foto ?? null,
                    'kelas' => $student->kelas,
                    'no_hp' => $student->no_hp,
                    'total_poin' => $student->total_poin,
                ];
            });

        return response()->json(['data' => $students]);
    }

    public function profile($user_id)
    {
        try {
            $student = Student::with('user')
                ->where('user_id', $user_id)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ], 404);
            }

            $response = [
                'success' => true,
                'data' => [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'nama' => $student->user->nama,
                    'email' => $student->user->email,
                    'foto' => $student->user->foto ? asset('storage/' . $student->user->foto) : null,
                    'kelas' => $student->kelas,
                    'no_hp' => $student->no_hp,
                    'total_poin' => $student->total_poin,
                    'role' => $student->user->role,
                ]
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProfile($userId)
    {
        try {
            $user = User::with('student')
                ->where('id', $userId)
                ->where('role', 'siswa')
                ->firstOrFail();

            $response = [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'foto' => $user->foto ? asset('storage/' . $user->foto) : null,
                'kelas' => $user->student->kelas,
                'no_hp' => $user->student->no_hp,
                'total_poin' => $user->student->total_poin
            ];

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan'
            ], 404);
        }
    }

    public function getViolationHistory($userId)
    {
        try {
            $user = User::where('id', $userId)
                ->where('role', 'siswa')
                ->firstOrFail();

            $violations = Violation::with(['category', 'teacher'])
                ->where('student_id', $user->student->id)
                ->orderBy('tanggal', 'desc')
                ->get();

            $formattedViolations = $violations->map(function ($violation) {
                return [
                    'id' => $violation->id,
                    'tanggal' => $violation->tanggal,
                    'kategori' => $violation->category->kategori,
                    'sub_kategori' => $violation->category->sub,
                    'jenis_pelanggaran' => $violation->category->jenis_pelanggaran,
                    'poin' => $violation->poin,
                    'catatan' => $violation->catatan,
                    'guru' => $violation->teacher->nama
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedViolations,
                'total_poin' => $user->student->total_poin
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pelanggaran'
            ], 500);
        }
    }

    public function getGuidanceHistory($userId)
    {
        try {
            $user = User::where('id', $userId)
                ->where('role', 'siswa')
                ->firstOrFail();

            $guidances = Guidance::with(['sanction', 'teacher'])
                ->where('student_id', $user->student->id)
                ->orderBy('tanggal', 'desc')
                ->get();

            $formattedGuidances = $guidances->map(function ($guidance) {
                return [
                    'id' => $guidance->id,
                    'tanggal' => $guidance->tanggal,
                    'sanksi' => $guidance->sanction->sanksi,
                    'catatan' => $guidance->catatan,
                    'status' => $guidance->status,
                    'guru' => $guidance->teacher->nama
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedGuidances
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pembinaan'
            ], 500);
        }
    }

    public function updateProfile(Request $request, $userId)
    {
        $user = User::with('student')->findOrFail($userId);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'no_hp' => 'required|string|max:20',
            'kelas' => 'required|string|max:10',
            'password' => 'nullable|string|min:6',
        ]);

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $user->student->no_hp = $validated['no_hp'];
        $user->student->kelas = $validated['kelas'];
        $user->student->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $user->load('student')
        ]);
    }


    public function uploadPhoto(Request $request, $userId)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::findOrFail($userId);

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::delete($user->foto);
            }

            $path = $request->file('foto')->store('profile_photos');
            $user->foto = $path;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diupload',
            'foto_url' => asset('storage/' . $path)
        ]);
    }
}
