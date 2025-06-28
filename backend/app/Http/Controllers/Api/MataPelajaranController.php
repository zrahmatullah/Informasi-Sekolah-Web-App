<?php

namespace App\Http\Controllers\Api;

use App\Models\MataPelajaran;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{

    public function index()
    {
        $mataPelajaran = MataPelajaran::with('gurus')->get();
        return response()->json($mataPelajaran);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'guru_id' => 'required|exists:gurus,id',
        ]);

        $mataPelajaran = MataPelajaran::create($validated);
        return response()->json($mataPelajaran, 201);
    }


    public function show($id)
    {
        $mataPelajaran = MataPelajaran::with('guru')->findOrFail($id);
        return response()->json($mataPelajaran);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'guru_id' => 'required|exists:gurus,id',
        ]);

        $mataPelajaran = MataPelajaran::findOrFail($id);
        $mataPelajaran->update($validated);

        return response()->json($mataPelajaran);
    }


    public function destroy($id)
    {
        $mataPelajaran = MataPelajaran::findOrFail($id);
        $mataPelajaran->delete();

        return response()->json(['message' => 'Mata Pelajaran deleted successfully']);
    }
}
