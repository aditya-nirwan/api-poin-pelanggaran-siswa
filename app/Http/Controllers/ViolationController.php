<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Violation;

use App\Models\Student;
use App\Models\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;


class ViolationController extends Controller
{

    public function index(Request $request)
    {
        $query = Violation::with(['student.user', 'teacher', 'category']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            });
        }

        if ($request->has('kelas') && $request->kelas) {
            $kelas = $request->kelas;
            $query->whereHas('student', function ($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        $query->orderBy('tanggal', 'desc');

        $violations = $query->paginate(10);

        return response()->json($violations);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'teacher_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:violation_categories,id',
            'tanggal' => 'required|date',
            'poin' => 'required|integer',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $violation = Violation::create($validated);

        $student = Student::find($request->student_id);
        $student->total_poin += $request->poin;
        $student->save();

        $siswa = Student::with('user')->find($validated['student_id']);

        Log::create([
            'user_id' => $validated['teacher_id'],
            'jenis_aktivitas' => 'pelanggaran',
            'aktivitas' => 'Menambahkan pelanggaran untuk siswa ' . ($siswa->user->nama ?? 'Tidak diketahui'),
            'waktu' => now(),
        ]);

        return response()->json([
            'message' => 'Pelanggaran berhasil ditambahkan',
            'data' => $violation
        ], 201);
    }


    public function show($id)
    {
        $violation = Violation::with(['student', 'teacher', 'category'])->find($id);

        if (!$violation) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($violation);
    }

    public function update(Request $request, $id)
    {
        $violation = Violation::find($id);
        if (!$violation) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'teacher_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:violation_categories,id',
            'tanggal' => 'required|date',
            'poin' => 'required|integer',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        if ($validated['student_id'] != $violation->student_id) {

            $oldStudent = Student::find($violation->student_id);
            if ($oldStudent) {
                $oldStudent->total_poin -= $violation->poin;
                $oldStudent->save();
            }

            $newStudent = Student::find($validated['student_id']);
            if ($newStudent) {
                $newStudent->total_poin += $validated['poin'];
                $newStudent->save();
            }
        } else {

            $student = Student::find($violation->student_id);
            if ($student) {

                $student->total_poin -= $violation->poin;
                $student->total_poin += $validated['poin'];
                $student->save();
            }
        }

        $violation->update($validated);

        $siswa = Student::with('user')->find($validated['student_id']);

        Log::create([
            'user_id' => $validated['teacher_id'],
            'jenis_aktivitas' => 'pelanggaran',
            'aktivitas' => 'Mengubah pelanggaran untuk siswa ' . ($siswa?->user?->nama ?? '(Tidak ditemukan)'),
            'waktu' => now(),
        ]);

        return response()->json([
            'message' => 'Pelanggaran berhasil diperbarui',
            'data' => $violation
        ]);
    }


    public function destroy($id)
    {
        $violation = Violation::with('student.user')->find($id);

        if (!$violation) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $namaSiswa = $violation->student?->user?->nama ?? '(Tidak ditemukan)';

        $student = Student::find($violation->student_id);
        $student->total_poin -= $violation->poin;
        $student->save();

        $violation->delete();

        Log::create([
            'user_id' => $violation->teacher_id,
            'jenis_aktivitas' => 'pelanggaran',
            'aktivitas' => 'Menghapus pelanggaran untuk siswa ' . $namaSiswa,
            'waktu' => now(),
        ]);

        return response()->json(['message' => 'Pelanggaran berhasil dihapus']);
    }
}
