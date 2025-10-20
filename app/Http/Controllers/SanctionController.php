<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sanction;
use Illuminate\Http\Request;

class SanctionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $result = Sanction::orderBy('min_poin')->paginate($perPage);

        return response()->json([
            'data' => $result->items(),
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage(),
            'total' => $result->total(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'min_poin' => 'required|integer|min:0',
            'max_poin' => 'required|integer|gte:min_poin',
            'sanksi' => 'required|string|max:255',
        ]);

        $sanction = Sanction::create($request->only(['min_poin', 'max_poin', 'sanksi']));

        return response()->json(['message' => 'Sanksi berhasil ditambahkan', 'data' => $sanction], 201);
    }

    public function show($id)
    {
        $sanction = Sanction::find($id);
        if (!$sanction) {
            return response()->json(['message' => 'Sanksi tidak ditemukan'], 404);
        }

        return response()->json($sanction);
    }

    public function update(Request $request, $id)
    {
        $sanction = Sanction::find($id);
        if (!$sanction) {
            return response()->json(['message' => 'Sanksi tidak ditemukan'], 404);
        }

        $request->validate([
            'min_poin' => 'required|integer|min:0',
            'max_poin' => 'required|integer|gte:min_poin',
            'sanksi' => 'required|string|max:255',
        ]);

        $sanction->update($request->only(['min_poin', 'max_poin', 'sanksi']));

        return response()->json(['message' => 'Sanksi berhasil diperbarui', 'data' => $sanction]);
    }

    public function destroy($id)
    {
        $sanction = Sanction::find($id);
        if (!$sanction) {
            return response()->json(['message' => 'Sanksi tidak ditemukan'], 404);
        }

        $sanction->delete();
        return response()->json(['message' => 'Sanksi berhasil dihapus']);
    }
}
