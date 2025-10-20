<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Violation;
use App\Models\Guidance;
use App\Models\SchoolYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summarys()
    {
        $today = \Carbon\Carbon::today();
        $monthStart = \Carbon\Carbon::now()->startOfMonth();
        $monthEnd = \Carbon\Carbon::now()->endOfMonth();

        return response()->json([
            'siswa' => \App\Models\Student::count(),
            'guru' => \App\Models\User::where('role', 'guru')->count(),
            'pelanggaran_hari_ini' => \App\Models\Violation::whereDate('tanggal', $today)->count(),
            'pelanggaran_bulan_ini' => \App\Models\Violation::whereBetween('tanggal', [$monthStart, $monthEnd])->count(),
            'total_pelanggaran' => \App\Models\Violation::count(),
            'total_pembinaan' => \App\Models\Guidance::count(),
            'pembinaan_proses' => \App\Models\Guidance::where('status', 0)->count(),
            'pembinaan_selesai' => \App\Models\Guidance::where('status', 1)->count(),
        ]);
    }

    public function summary()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        // Summary data
        $jumlahSiswa = Student::count();
        $jumlahGuru = User::where('role', 'guru')->count();
        $pelanggaranHariIni = Violation::whereDate('tanggal', $today)->count();
        $pelanggaranMingguIni = Violation::whereBetween('tanggal', [$weekStart, $weekEnd])->count();
        $pelanggaranBulanIni = Violation::whereBetween('tanggal', [$monthStart, $monthEnd])->count();
        $totalPelanggaran = Violation::count();
        $totalPembinaan = Guidance::count();
        $pembinaanProses = Guidance::where('status', 0)->count();
        $pembinaanSelesai = Guidance::where('status', 1)->count();

        return response()->json([
            'total_siswa' => $jumlahSiswa,
            'total_guru' => $jumlahGuru,
            'pelanggaran_hari_ini' => $pelanggaranHariIni,
            'pelanggaran_minggu_ini' => $pelanggaranMingguIni,
            'pelanggaran_bulan_ini' => $pelanggaranBulanIni,
            'total_pelanggaran' => $totalPelanggaran,
            'total_pembinaan' => $totalPembinaan,
            'pembinaan_proses' => $pembinaanProses,
            'pembinaan_selesai' => $pembinaanSelesai,
        ]);
    }

    public function topPelanggar()
    {
        $topSiswa = \App\Models\Violation::with('student.user')
            ->selectRaw('student_id, COUNT(*) as total_pelanggaran, SUM(poin) as total_poin')
            ->groupBy('student_id')
            ->orderByDesc('total_poin')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->student->id,
                    'nama' => $item->student->user->nama ?? '-',
                    'kelas' => $item->student->kelas ?? '-',
                    'total_pelanggaran' => $item->total_pelanggaran,
                    'total_poin' => $item->total_poin,
                ];
            });

        return response()->json(['data' => $topSiswa]);
    }

    public function topJenisPelanggaran()
    {
        $topJenis = \App\Models\Violation::with('category')
            ->selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'jenis_pelanggaran' => $item->category->jenis_pelanggaran ?? '-',
                    'kategori' => $item->category->kategori ?? '-',
                    'total' => $item->total,
                ];
            });

        return response()->json(['data' => $topJenis]);
    }

    public function chartPelanggaran()
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();

        if (!$schoolYear) {
            return response()->json(['data' => []]);
        }

        $start = Carbon::parse($schoolYear->semester_ganjil_start);
        $end = Carbon::parse($schoolYear->semester_genap_end);

        $periode = [];
        $current = $start->copy();
        while ($current <= $end) {
            $periode[$current->format('Y-m')] = 0;
            $current->addMonth();
        }

        $data = Violation::selectRaw('DATE_FORMAT(tanggal, "%Y-%m") as bulan, COUNT(*) as total')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        foreach ($data as $row) {
            if (isset($periode[$row->bulan])) {
                $periode[$row->bulan] = $row->total;
            }
        }

        $result = collect($periode)->map(function ($val, $key) {
            return [
                'bulan' => Carbon::parse($key . '-01')->locale('id')->translatedFormat('F Y'),
                'total' => $val,
            ];
        })->values();

        return response()->json(['data' => $result]);
    }

    public function chartPelanggaranWeekly()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $weeks = [];
        $start = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $weekIndex = 1;

        while ($start < $endOfMonth) {
            $end = $start->copy()->endOfWeek(Carbon::SUNDAY);
            $weeks[] = [
                'index' => $weekIndex++,
                'start' => $start->copy(),
                'end' => $end->copy(),
            ];
            $start->addWeek();
        }

        $violations = DB::table('violations')
            ->select('tanggal', DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->groupBy('tanggal')
            ->get();

        $weeklyData = [];

        foreach ($weeks as $week) {
            $count = $violations->filter(function ($v) use ($week) {
                $date = Carbon::parse($v->tanggal);
                return $date->between($week['start'], $week['end']);
            })->sum('total');

            $weeklyData[] = [
                'minggu' => $week['start']->translatedFormat('d M') . ' - ' . $week['end']->translatedFormat('d M'),
                'total' => $count,
            ];
        }

        return response()->json(['data' => $weeklyData]);
    }
    public function violationWeeklyChart()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $violations = Violation::select(
            DB::raw('DATE(tanggal) as tanggal'),
            DB::raw('COUNT(*) as jumlah')
        )
            ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $chartData = [];
        for ($date = $startOfWeek->copy(); $date <= $endOfWeek; $date->addDay()) {
            $tanggalStr = $date->format('Y-m-d');
            $chartData[] = [
                'tanggal' => $tanggalStr,
                'jumlah' => $violations[$tanggalStr]->jumlah ?? 0,
            ];
        }

        return response()->json($chartData);
    }
}
