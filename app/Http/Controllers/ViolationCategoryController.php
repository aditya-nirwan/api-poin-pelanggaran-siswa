<?php

namespace App\Http\Controllers;

use App\Models\ViolationCategory;
use Illuminate\Http\Request;

class ViolationCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ViolationCategory::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $query->where('jenis_pelanggaran', 'like', '%' . $request->search . '%');
        }

        $query->orderBy('poin', 'desc')->orderBy('kategori');

        $perPage = $request->get('per_page', 10);
        $result = $query->paginate($perPage);

        return response()->json([
            'data' => $result->items(),
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage(),
            'total' => $result->total(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string',
            'sub' => 'nullable|string',
            'jenis_pelanggaran' => 'required|string',
            'poin' => 'required|integer',
        ]);

        $category = ViolationCategory::create($validated);
        return response()->json($category, 201);
    }

    public function show(ViolationCategory $violationCategory)
    {
        return $violationCategory;
    }

    public function update(Request $request, ViolationCategory $violationCategory)
    {
        $validated = $request->validate([
            'kategori' => 'required|string',
            'sub' => 'nullable|string',
            'jenis_pelanggaran' => 'required|string',
            'poin' => 'required|integer',
        ]);

        $violationCategory->update($validated);
        return response()->json($violationCategory);
    }

    public function destroy(ViolationCategory $violationCategory)
    {
        $violationCategory->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function all(Request $request)
    {
        $query = ViolationCategory::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $query->where('jenis_pelanggaran', 'like', '%' . $request->search . '%');
        }

        $data = $query->get();

        return response()->json([
            'data' => $data
        ]);
    }
}
