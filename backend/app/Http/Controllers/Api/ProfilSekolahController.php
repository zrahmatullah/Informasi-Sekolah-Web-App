<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfilSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilSekolahController extends Controller
{
    public function show()
    {
        $profil = ProfilSekolah::with('kepalaSekolah')->first();
        return response()->json($profil);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sekolah' => 'required|string',
            'alamat' => 'required|string',
            'kepala_sekolah_id' => 'required|exists:gurus,id',
            'no_telp' => 'required|string',
            'visi' => 'required|string',
            'misi' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto_sekolah', 'public');
        }

        $profil = ProfilSekolah::create($validated);
        return response()->json($profil, 201);
    }

    public function update(Request $request, $id)
    {
        $profil = ProfilSekolah::findOrFail($id);

        $validated = $request->validate([
            'nama_sekolah' => 'sometimes|required|string',
            'alamat' => 'sometimes|required|string',
            'kepala_sekolah_id' => 'sometimes|required|exists:gurus,id',
            'no_telp' => 'sometimes|required|string',
            'visi' => 'sometimes|required|string',
            'misi' => 'sometimes|required|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($profil->foto) {
                Storage::disk('public')->delete($profil->foto);
            }
            $validated['foto'] = $request->file('foto')->store('foto_sekolah', 'public');
        }

        $profil->update($validated);
        return response()->json($profil);
    }
}
