<?php

namespace App\Http\Controllers\Api;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('guru')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($kelas);
    }


    public function show($id)
    {
        $kelas = Kelas::with('guru')->find($id);

        if (!$kelas) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        return response()->json($kelas);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'kode_kelas' => 'required|string|max:255|unique:kelas,kode_kelas',
            'guru_id'    => 'nullable|exists:gurus,id',
        ]);

        $kelas = Kelas::create($validated);
        $kelas->load('guru');

        return response()->json($kelas->load('guru'), 201);
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'kode_kelas' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kelas', 'kode_kelas')->ignore($kelas->id),
            ],
            'guru_id' => 'nullable|exists:gurus,id',
        ]);


        $guruChanged = $validated['guru_id'] !== $kelas->guru_id;

        $kelas->nama_kelas = $validated['nama_kelas'];
        $kelas->kode_kelas = $validated['kode_kelas'];
        $kelas->guru_id = $validated['guru_id'] ?? null;
        $kelas->save();


        if ($guruChanged) {
            Siswa::where('kelas_id', $kelas->id)->update(['guru_id' => $validated['guru_id']]);
        }

        return response()->json($kelas->load('guru'));
    }

    public function destroy($id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        $kelas->delete();

        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}
