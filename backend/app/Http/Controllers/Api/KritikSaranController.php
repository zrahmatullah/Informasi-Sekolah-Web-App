<?php

namespace App\Http\Controllers\Api;

use App\Models\KritikSaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KritikSaranController extends Controller
{

    public function index()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return response()->json([
                'message' => 'Anda bukan siswa atau belum memiliki data siswa.',
                'data' => []
            ], 403);
        }

        $kritikSarans = KritikSaran::with(['siswa', 'guru'])
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Data kritik & saran berhasil diambil.',
            'data' => $kritikSarans
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'guru_id' => 'required|exists:gurus,id',
            'judul'   => 'nullable|string|max:255',
            'isi'     => 'required|string',
        ]);

        $user = auth()->user();

        if (!$user->siswa) {
            return response()->json(['message' => 'Akun ini bukan siswa'], 403);
        }

        $siswa = $user->siswa;


        if (!$siswa->guru_id) {
            return response()->json(['message' => 'Wali kelas belum ditentukan untuk siswa ini.'], 422);
        }


        if ($request->guru_id != $siswa->guru_id) {
            return response()->json(['message' => 'Guru tujuan harus wali kelas Anda.'], 403);
        }

        $kritik = KritikSaran::create([
            'siswa_id' => $siswa->id,
            'guru_id'  => $request->guru_id,
            'tanggal'  => $request->tanggal,
            'judul'    => $request->judul,
            'isi'      => $request->isi,
        ]);

        return response()->json([
            'message' => 'Kritik & saran berhasil dikirim.',
            'data' => $kritik
        ], 201);
    }





    public function getByGuru(Request $request)
    {
        $user = auth()->user();
        $guru = $user->guru;

        if (!$guru) {
            return response()->json(['message' => 'Akses hanya untuk guru.'], 403);
        }

        $kritikSarans = KritikSaran::with('siswa')
            ->where('guru_id', $guru->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Kritik & saran untuk guru ini.',
            'data' => $kritikSarans
        ]);
    }


    public function tanggapi(Request $request, $id)
    {
        $request->validate([
            'tanggapan' => 'required|string|max:1000',
        ]);

        $kritik = KritikSaran::findOrFail($id);

        $user = auth()->user();
        $guru = $user->guru;

        if (!$guru || $kritik->guru_id !== $guru->id) {
            return response()->json(['message' => 'Tidak diizinkan menanggapi.'], 403);
        }

        if ($kritik->tanggapan) {
            return response()->json(['message' => 'Kritik ini sudah ditanggapi.'], 400);
        }

        $kritik->tanggapan = $request->tanggapan;
        $kritik->save();

        return response()->json([
            'message' => 'Tanggapan berhasil disimpan.',
            'data' => $kritik
        ]);
    }
}
