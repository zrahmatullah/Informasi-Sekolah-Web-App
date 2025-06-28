<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;

class SiswaMapelController extends Controller
{
    public function index($id)
    {
        $siswa = Siswa::with(['mataPelajaran.guru'])->find($id);

        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        $mapelData = $siswa->mataPelajaran->map(function ($mapel) {
            return [
                'mata_pelajaran' => $mapel->nama_mapel,
                'guru_mapel' => $mapel->guru ? $mapel->guru->nama_guru : null,
                'nilai' => $mapel->pivot->nilai,
            ];
        });

        return response()->json([
            'siswa' => $siswa->nama_lengkap_siswa,
            'mata_pelajaran' => $mapelData
        ]);
    }
}
