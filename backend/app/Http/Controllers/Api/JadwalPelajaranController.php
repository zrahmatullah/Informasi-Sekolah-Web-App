<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gurus;
use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $data = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'guru'])->orderBy('id', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $jadwal = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'guru'])->findOrFail($id);
        return response()->json($jadwal);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'guru_id' => 'required|exists:gurus,id',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $jadwal = JadwalPelajaran::create([
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mata_pelajaran_id,
            'guru_id' => $request->guru_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return response()->json($jadwal, 201);
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'guru_id' => 'required|exists:gurus,id',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $jadwal->update($request->all());
        return response()->json($jadwal);
    }

    public function destroy($id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $jadwal->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
    public function getByGuru()
    {
        $user = auth()->user();

        $jadwal = JadwalPelajaran::with(['mataPelajaran', 'kelas'])
            ->where('guru_id', $user->guru->id)
            ->get();

        return response()->json($jadwal);
    }
    public function getJadwalBySiswa()
    {
        $user = auth()->user();

        if (!$user->siswa) {
            return response()->json(['message' => 'Data siswa tidak ditemukan'], 404);
        }

        $kelasId = $user->siswa->kelas_id;

        $jadwal = JadwalPelajaran::with(['mataPelajaran', 'guru', 'kelas'])
            ->where('kelas_id', $kelasId)
            ->orderBy('hari') // jika ada field hari
            ->orderBy('jam_mulai') // jika ada field jam mulai
            ->get();

        return response()->json($jadwal);
    }
}
