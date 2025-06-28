<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TahunPelajaran;
use App\Http\Controllers\Controller;

class TahunPelajaranController extends Controller
{
    public function index()
    {
        return response()->json(TahunPelajaran::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_pelajaran' => 'required|string',
        ]);

        $tahunpelajaran = TahunPelajaran::create($validated);

        return response()->json($tahunpelajaran, 201);
    }

    public function show($id)
    {
        $tahunpelajaran = TahunPelajaran::findOrFail($id);
        return response()->json($tahunpelajaran);
    }

    public function update(Request $request, $id)
    {
        $tahunpelajaran = TahunPelajaran::findOrFail($id);

        $validated = $request->validate([
            'tahun_pelajaran' => 'required|string',
        ]);

        $tahunpelajaran->update($validated);

        return response()->json($tahunpelajaran);
    }

    public function destroy($id)
    {
        $tahunpelajaran = TahunPelajaran::findOrFail($id);
        $tahunpelajaran->delete();

        return response()->json(null, 204);
    }
}
