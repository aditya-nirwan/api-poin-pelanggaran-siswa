<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Guidance;
use App\Models\Student;
use App\Models\Sanction;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

use App\Services\FonnteService;

class GuidanceController extends Controller
{
    protected $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    public function index(Request $request)
    {
        $query = Guidance::with(['student.user', 'teacher', 'sanction']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('kelas')) {
            $kelas = $request->kelas;
            $query->whereHas('student', function ($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        $query->orderBy('tanggal', 'desc');

        $guidances = $query->paginate($request->get('per_page', 10));

        return response()->json($guidances);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id'  => 'required|exists:students,id',
            'teacher_id'  => 'required|exists:users,id',
            'sanction_id' => 'required|exists:sanctions,id',
            'tanggal'     => 'required|date',
            'catatan'     => 'nullable|string',
            'status'      => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $guidance = Guidance::create($validated);

        $guidance->load('student.user', 'sanction');

        $siswa = Student::with('user')->find($validated['student_id']);

        Log::create([
            'user_id' => $validated['teacher_id'],
            'jenis_aktivitas' => 'pembinaan',
            'aktivitas' => 'Menambahkan pembinaan untuk siswa ' . ($siswa?->user?->nama ?? '(Tidak ditemukan)'),
            'waktu' => now(),
        ]);

        return response()->json([
            'message' => 'Data pembinaan berhasil disimpan',
            'data' => $guidance,
        ], 201);
    }

    public function show($id)
    {
        $guidance = Guidance::with(['student.user', 'teacher', 'sanction'])->findOrFail($id);
        return response()->json($guidance);
    }

    public function update(Request $request, $id)
    {
        $guidance = Guidance::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'student_id'  => 'required|exists:students,id',
            'teacher_id'  => 'required|exists:users,id',
            'sanction_id' => 'required|exists:sanctions,id',
            'tanggal'     => 'required|date',
            'catatan'     => 'nullable|string',
            'status'      => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $guidance->update($validator->validated());

        return response()->json(['message' => 'Data pembinaan berhasil diperbarui', 'data' => $guidance]);
    }

    public function destroy($id)
    {
        $guidance = Guidance::findOrFail($id);
        $guidance->delete();

        return response()->json(['message' => 'Data pembinaan berhasil dihapus']);
    }

    public function pending()
    {
        $students = Student::with('user')
            ->get()
            ->filter(function ($student) {
                $sanction = Sanction::where('min_poin', '<=', $student->total_poin)
                    ->where('max_poin', '>=', $student->total_poin)
                    ->first();

                if (!$sanction) {
                    return false;
                }

                $ongoingGuidance = Guidance::where('student_id', $student->id)
                    ->where('status', 0)
                    ->exists();


                return !$ongoingGuidance;
            })
            ->map(function ($student) {
                $sanction = Sanction::where('min_poin', '<=', $student->total_poin)
                    ->where('max_poin', '>=', $student->total_poin)
                    ->first();

                return [
                    'student' => $student,
                    'sanction' => $sanction,
                ];
            })
            ->values();

        return response()->json($students);
    }

    public function inProcess(Request $request)
    {
        $query = Guidance::with(['student.user', 'teacher', 'sanction'])
            ->where('status', 0)
            ->orderByDesc('tanggal');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', function ($q2) use ($search) {
                    $q2->where('nama', 'like', "%$search%");
                })->orWhereHas('student', function ($q2) use ($search) {
                    $q2->where('kelas', 'like', "%$search%");
                });
            });
        }

        return response()->json($query->paginate(10));
    }

    public function completed(Request $request)
    {
        $query = Guidance::with(['student.user', 'teacher', 'sanction'])
            ->where('status', 1)
            ->orderByDesc('tanggal');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', function ($q2) use ($search) {
                    $q2->where('nama', 'like', "%$search%");
                })->orWhereHas('student', function ($q2) use ($search) {
                    $q2->where('kelas', 'like', "%$search%");
                });
            });
        }

        return response()->json($query->paginate(10));
    }


    public function generateSp($id)
    {
        $guidance = Guidance::with(['student.user', 'sanction'])->findOrFail($id);

        if ($guidance->sanction_id < 3) {
            return response()->json([
                'message' => 'Surat peringatan tidak diperlukan untuk sanksi ini.'
            ], 400);
        }

        $spNumber = $guidance->sanction_id === 3 ? 1 : 2;
        $view = "pdf.surat_peringatan_{$spNumber}";

        $kepsek = User::where('role', 'kepsek')->first();

        $pdf = Pdf::loadView($view, [
            'guidance' => $guidance,
            'kepsek' => $kepsek,
        ]);

        $directory = 'sp';
        Storage::disk('public')->makeDirectory($directory);

        $filename = "{$guidance->id}.pdf";
        $pdfPath = "$directory/$filename";

        // Simpan file PDF
        Storage::disk('public')->put($pdfPath, $pdf->output());

        $url = asset("storage/$pdfPath");

        return response()->json([
            'success' => true,
            'message' => 'Surat peringatan berhasil dibuat.',
            'url' => $url,
            'path' => $pdfPath
        ]);
    }

    public function sendSpNotification($id)
    {
        $guidance = Guidance::with(['student.user', 'sanction'])->findOrFail($id);
        $fonnteService = app(FonnteService::class);

        // Format nomor HP orang tua
        $target = preg_replace('/^\+?0?/', '62', $guidance->student->no_hp);

        Carbon::setLocale('id');
        $tanggal = Carbon::parse($guidance->tanggal)->translatedFormat('d F Y');

        // Pesan untuk orang tua
        $message = "Yth. Orang Tua/Wali dari {$guidance->student->user->nama},\n\n"
            . "Bersama ini kami sampaikan bahwa putra/putri Anda telah menerima:\n"
            . "📌 *Jenis Sanksi*: {$guidance->sanction->sanksi}\n"
            . "📅 *Tanggal*: {$tanggal}\n";

        if ($guidance->sanction_id >= 3) {
            $pdfPath = "sp/{$guidance->id}.pdf";

            if (!Storage::disk('public')->exists($pdfPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen SP belum dibuat. Silakan generate terlebih dahulu.'
                ], 400);
            }

            $pdfUrl = asset("storage/$pdfPath");
            $message .= "📄 *Dokumen Resmi*:\n"
                . "Silakan download surat peringatan melalui link berikut:\n"
                . "🔗 {$pdfUrl}\n\n";
        }

        $message .= "Kami mengharapkan kerja sama Bapak/Ibu untuk:\n"
            . "1. Membahas masalah ini dengan putra/putri Anda\n"
            . "2. Memantau perkembangan perilaku\n"
            . "3. Berkoordinasi dengan wali kelas\n\n"
            . "Hormat kami,\n"
            . "Bimbingan Konseling\n"
            . "SMK Pembangunan Ampel Boyolali\n";

        try {
            $result = $fonnteService->sendMessage($target, $message);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi untuk orang tua berhasil dikirim',
                'pdf_url' => $pdfUrl ?? null,
                'whatsapp_response' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim notifikasi ke orang tua: ' . $e->getMessage(),
                'pdf_url' => $pdfUrl ?? null
            ], 500);
        }
    }
}
