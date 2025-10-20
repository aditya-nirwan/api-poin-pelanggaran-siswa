<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('jenis_aktivitas')) {
            $query->where('jenis_aktivitas', $request->jenis_aktivitas);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('waktu', $request->tanggal);
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('waktu', [$request->start_date, $request->end_date]);
        }

        $query->orderBy('waktu', 'desc');

        $perPage = $request->get('per_page', 10);
        $result = $query->paginate($perPage);

        $data = $result->getCollection()->transform(function ($log) {
            return [
                'id' => $log->id,
                'user_id' => $log->user_id,
                'nama_user' => $log->user->nama ?? null,
                'jenis_aktivitas' => $log->jenis_aktivitas,
                'aktivitas' => $log->aktivitas,
                'waktu' => $log->waktu,
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage(),
            'total' => $result->total(),
            'next_page_url' => $result->nextPageUrl(),
        ]);
    }
}
