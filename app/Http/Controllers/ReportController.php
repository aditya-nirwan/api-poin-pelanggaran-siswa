<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Violation;
use App\Models\SchoolYear;
use App\Models\Guidance;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

use Carbon\Carbon;

class ReportController extends Controller
{
    private function getRekapPelanggaranData(Request $request)
    {
        $query = Student::with('violations.category')
            ->select('students.*')
            ->when($request->kelas, function ($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });

        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        $students = $query->get();

        return $students->map(function ($student) use ($tanggalAwal, $tanggalAkhir) {
            $pelanggaran = $student->violations()
                ->with('category')
                ->when($tanggalAwal && $tanggalAkhir, function ($q) use ($tanggalAwal, $tanggalAkhir) {
                    $q->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
                })
                ->orderBy('tanggal', 'desc')
                ->get();

            return [
                'id' => $student->id,
                'nama' => $student->user->nama ?? '-',
                'kelas' => $student->kelas,
                'total_poin' => $pelanggaran->sum('poin'),
                'pelanggaran' => $pelanggaran->map(function ($v) {
                    return [
                        'tanggal' => $v->tanggal,
                        'jenis_pelanggaran' => $v->category->jenis_pelanggaran ?? '-',
                        'kategori' => $v->category->kategori ?? '-',
                        'poin' => $v->poin,
                        'catatan' => $v->catatan,
                    ];
                }),
            ];
        });
    }

    public function rekapPelanggaranSiswa(Request $request)
    {
        $data = $this->getRekapPelanggaranData($request);
        return response()->json(['data' => $data]);
    }

    public function rekapPelanggaranSiswaPdf(Request $request)
    {
        $kelas = $request->kelas;
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        $data = $this->getRekapPelanggaranData($request);

        $pdf = Pdf::loadView('reports.rekap_pelanggaran', [
            'data' => $data,
            'kelas' => $kelas,
            'tanggal_awal' => $tanggalAwal,
            'tanggal_akhir' => $tanggalAkhir
        ]);

        $filename = 'rekap_pelanggaran_' . Str::slug($kelas ?? 'semua') . '_' . now()->format('Ymd_His') . '.pdf';
        $path = storage_path("app/public/reports/$filename");
        $pdf->save($path);

        return response()->json([
            'success' => true,
            'file_url' => asset("storage/reports/$filename"),
        ]);
    }

    public function detailPelanggaranSiswa(Request $request)
    {
        $studentId = $request->student_id;
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        $student = Student::with(['user', 'violations.category'])
            ->findOrFail($studentId);

        $pelanggaran = $student->violations()
            ->with('category')
            ->when($tanggalAwal && $tanggalAkhir, function ($q) use ($tanggalAwal, $tanggalAkhir) {
                $q->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        $data = [
            'id' => $student->id,
            'nama' => $student->user->nama ?? '-',
            'kelas' => $student->kelas,
            'total_poin' => $pelanggaran->sum('poin'),
            'pelanggaran' => $pelanggaran->map(function ($v) {
                return [
                    'tanggal' => $v->tanggal,
                    'jenis_pelanggaran' => $v->category->jenis_pelanggaran ?? '-',
                    'kategori' => $v->category->kategori ?? '-',
                    'poin' => $v->poin,
                    'catatan' => $v->catatan,
                ];
            }),
        ];

        return response()->json([
            'data' => $data,
        ]);
    }
    public function detailPelanggaranSiswaPdf(Request $request)
    {
        $data = $this->detailPelanggaranSiswa($request)->getData()->data;

        $pdf = Pdf::loadView('reports.detail_pelanggaran', [
            'data' => $data,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
        ]);

        $filename = 'detail_pelanggaran_' . Str::slug($data->nama) . '_' . now()->format('Ymd_His') . '.pdf';
        $path = storage_path("app/public/reports/$filename");
        $pdf->save($path);

        return response()->json([
            'success' => true,
            'file_url' => asset("storage/reports/$filename"),
        ]);
    }


    public function pelanggaranByWaktu(Request $request)
    {
        $query = Violation::with([
            'student.user',
            'category',
            'teacher'
        ]);

        $filter = $request->waktu;
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;
        $now = Carbon::now();

        switch ($filter) {
            case 'today':
                $query->whereDate('tanggal', $now->toDateString());
                break;

            case 'this_week':
                $query->whereBetween('tanggal', [
                    $now->copy()->startOfWeek()->toDateString(),
                    $now->copy()->endOfWeek()->toDateString(),
                ]);
                break;

            case 'this_month':
                $query->whereYear('tanggal', $now->year)
                    ->whereMonth('tanggal', $now->month);
                break;

            case 'this_semester':
                $schoolYear = SchoolYear::where('is_active', true)->first();
                if ($schoolYear) {
                    $now = Carbon::now();
                    if (
                        $schoolYear->semester_ganjil_start && $schoolYear->semester_ganjil_end &&
                        $now->between($schoolYear->semester_ganjil_start, $schoolYear->semester_ganjil_end)
                    ) {
                        $query->whereBetween('tanggal', [
                            $schoolYear->semester_ganjil_start,
                            $schoolYear->semester_ganjil_end
                        ]);
                    } elseif (
                        $schoolYear->semester_genap_start && $schoolYear->semester_genap_end &&
                        $now->between($schoolYear->semester_genap_start, $schoolYear->semester_genap_end)
                    ) {
                        $query->whereBetween('tanggal', [
                            $schoolYear->semester_genap_start,
                            $schoolYear->semester_genap_end
                        ]);
                    }
                }
                break;

            case 'this_year':
                $schoolYear = SchoolYear::where('is_active', true)->first();
                if ($schoolYear) {
                    $start = $schoolYear->semester_ganjil_start;
                    $end = $schoolYear->semester_genap_end ?? $schoolYear->semester_ganjil_end;

                    $query->whereBetween('tanggal', [$start, $end]);
                }
                break;

            case 'custom':
            case 'rentang':
                if ($tanggalAwal && $tanggalAkhir) {
                    $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
                }
                break;
        }

        $violations = $query->orderBy('tanggal', 'desc')->get();

        $data = $violations->map(function ($v) {
            return [
                'id' => $v->id,
                'nama_siswa' => $v->student->user->nama ?? '-',
                'kelas' => $v->student->kelas ?? '-',
                'jenis_pelanggaran' => $v->category->jenis_pelanggaran ?? '-',
                'kategori' => $v->category->kategori ?? '-',
                'poin' => $v->poin,
                'tanggal' => $v->tanggal,
                'guru' => $v->teacher->nama ?? '-',
                'catatan' => $v->catatan,
            ];
        });

        return response()->json(['data' => $data]);
    }
    public function pelanggaranByWaktuPdf(Request $request)
    {
        $response = $this->pelanggaranByWaktu($request);
        $data = $response->getData()->data ?? [];

        $waktu = $request->waktu ?? 'custom';
        $filename = 'pelanggaran_by_waktu_' . Str::slug($waktu) . '_' . now()->format('Ymd_His') . '.pdf';

        $periodeLabel = $this->getPeriodeLabel($request->waktu, $request->tanggal_awal, $request->tanggal_akhir);

        $pdf = Pdf::loadView('reports.pelanggaran_by_waktu', [
            'data' => $data,
            'filter' => $waktu,
            'periode' => $periodeLabel,
            'tanggal' => $request->tanggal,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
        ]);

        $path = storage_path("app/public/reports/{$filename}");
        $pdf->save($path);

        return response()->json([
            'success' => true,
            'file_url' => asset("storage/reports/{$filename}")
        ]);
    }
    public function getPeriodeLabel($filter, $tanggalAwal = null, $tanggalAkhir = null)
    {
        $now = Carbon::now();
        $format = 'd M Y';

        switch ($filter) {
            case 'today':
                return $now->format($format);

            case 'this_week':
                return $now->startOfWeek()->format($format) . ' - ' . $now->endOfWeek()->format($format);

            case 'this_month':
                return $now->copy()->startOfMonth()->format($format) . ' - ' . $now->copy()->endOfMonth()->format($format);

            case 'this_semester':
                $schoolYear = SchoolYear::where('is_active', true)->first();
                if ($schoolYear) {
                    $month = $now->month;
                    if ($month >= 1 && $month <= 6 && $schoolYear->semester_genap_start) {
                        return Carbon::parse($schoolYear->semester_genap_start)->format($format) . ' - ' .
                            Carbon::parse($schoolYear->semester_genap_end)->format($format);
                    } elseif ($month >= 7 && $schoolYear->semester_ganjil_start) {
                        return Carbon::parse($schoolYear->semester_ganjil_start)->format($format) . ' - ' .
                            Carbon::parse($schoolYear->semester_ganjil_end)->format($format);
                    }
                }
                return '-';

            case 'this_year':
                $schoolYear = SchoolYear::where('is_active', true)->first();
                if ($schoolYear) {
                    return Carbon::parse($schoolYear->semester_ganjil_start)->format($format) . ' - ' .
                        Carbon::parse($schoolYear->semester_genap_end)->format($format);
                }
                return '-';

            case 'rentang':
                if (!empty($tanggalAwal) && !empty($tanggalAkhir)) {
                    try {
                        return Carbon::parse($tanggalAwal)->format($format) . ' - ' .
                            Carbon::parse($tanggalAkhir)->format($format);
                    } catch (\Exception $e) {
                        return '-';
                    }
                }
                return '-';

            case 'tanggal':
                if ($tanggalAwal) {
                    return Carbon::parse($tanggalAwal)->format($format);
                }
                return '-';

            default:
                return '-';
        }
    }

    public function laporanPembinaan(Request $request)
    {
        $status = $request->status; // 0 atau 1
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        $query = Guidance::with([
            'student.user',
            'teacher',
            'sanction'
        ]);

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
        }

        $guidances = $query->orderBy('tanggal', 'desc')->get();

        $data = $guidances->map(function ($g) {
            return [
                'id' => $g->id,
                'nama_siswa' => $g->student->user->nama ?? '-',
                'kelas' => $g->student->kelas ?? '-',
                'sanksi' => $g->sanction->sanksi ?? '-',
                'tanggal' => $g->tanggal,
                'guru' => $g->teacher->nama ?? '-',
                'catatan' => $g->catatan,
                'status' => $g->status == 1 ? 'Selesai' : 'Proses',
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function laporanPembinaanPdf(Request $request)
    {
        $response = $this->laporanPembinaan($request);
        $data = $response->getData()->data ?? [];

        $status = $request->status;
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        $periode = '-';
        if ($tanggalAwal && $tanggalAkhir) {
            $periode = Carbon::parse($tanggalAwal)->translatedFormat('d M Y') . ' - ' .
                Carbon::parse($tanggalAkhir)->translatedFormat('d M Y');
        }

        $statusLabel = match ($status) {
            0 => 'Proses',
            1 => 'Selesai',
            default => 'Semua',
        };

        $filename = 'laporan_pembinaan_' . now()->format('Ymd_His') . '.pdf';

        $pdf = Pdf::loadView('reports.pembinaan', [
            'data' => $data,
            'periode' => $periode,
            'status' => $statusLabel,
        ]);

        $path = storage_path("app/public/reports/{$filename}");
        $pdf->save($path);

        return response()->json([
            'success' => true,
            'file_url' => asset("storage/reports/{$filename}")
        ]);
    }
}
