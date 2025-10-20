<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    public function getActive()
    {
        $active = SchoolYear::where('is_active', true)->first();

        if (!$active) {
            return response()->json(['message' => 'Tahun ajaran aktif tidak ditemukan'], 404);
        }

        return response()->json([
            'tahun' => $active->tahun_awal . '/' . $active->tahun_akhir,
            'ganjil_start' => $active->semester_ganjil_start,
            'ganjil_end' => $active->semester_ganjil_end,
            'genap_start' => $active->semester_genap_start,
            'genap_end' => $active->semester_genap_end,
        ]);
    }
}
