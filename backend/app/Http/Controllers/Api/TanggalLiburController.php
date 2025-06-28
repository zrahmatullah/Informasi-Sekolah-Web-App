<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TanggalLibur;
use Illuminate\Http\Request;

class TanggalLiburController extends Controller
{
    public function index()
    {
        return response()->json(TanggalLibur::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $libur = TanggalLibur::create($request->all());
        return response()->json($libur, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $libur = TanggalLibur::findOrFail($id);
        $libur->update($request->all());
        return response()->json($libur, 200);
    }

    public function destroy($id)
    {
        $libur = TanggalLibur::findOrFail($id);
        $libur->delete();

        return response()->json(['message' => 'Tanggal libur berhasil dihapus'], 200);
    }
}
