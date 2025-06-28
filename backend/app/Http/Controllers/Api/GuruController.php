<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Gurus;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\KritikSaran;
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GuruController extends Controller
{
    public function index()
    {
        $guru = Gurus::with(['user', 'jenisKelamin'])->get();
        return response()->json($guru, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nrp_nip' => 'required|string|max:255|unique:gurus',
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'pendidikan' => 'required|string|max:255',
            'tahun_masuk' => 'required|date',
            'alamat' => 'required|string',
            'rt_rw' => 'required|string|max:10',
            'dusun' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:10',
            'nomor_telpon' => 'required|string|max:15',
            'email' => 'required|email|max:255|unique:gurus',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $guru = Gurus::create($request->only([
            'nrp_nip',
            'nama',
            'tempat_lahir',
            'tanggal_lahir',
            'pendidikan',
            'tahun_masuk',
            'alamat',
            'rt_rw',
            'dusun',
            'kelurahan',
            'kecamatan',
            'kode_pos',
            'nomor_telpon',
            'email',
            'user_id',
            'jenis_kelamin_id',
        ]));

        return response()->json($guru->load(['user', 'jenisKelamin']), 201);
    }

    public function show($id)
    {
        $guru = Gurus::with(['user', 'jenisKelamin'])->find($id);

        if (!$guru) {
            return response()->json(['message' => 'Guru tidak ditemukan'], 404);
        }

        return response()->json($guru, 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nrp_nip' => 'required|string|max:255|unique:gurus,nrp_nip,' . $id,
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'pendidikan' => 'required|string|max:255',
            'tahun_masuk' => 'required|date',
            'alamat' => 'required|string',
            'rt_rw' => 'required|string|max:10',
            'dusun' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:10',
            'nomor_telpon' => 'required|string|max:15',
            'email' => 'required|email|max:255|unique:gurus,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $guru = Gurus::find($id);

        if (!$guru) {
            return response()->json(['message' => 'Guru tidak ditemukan'], 404);
        }

        $guru->update($request->only([
            'nrp_nip',
            'nama',
            'tempat_lahir',
            'tanggal_lahir',
            'pendidikan',
            'tahun_masuk',
            'alamat',
            'rt_rw',
            'dusun',
            'kelurahan',
            'kecamatan',
            'kode_pos',
            'nomor_telpon',
            'email',
        ]));

        return response()->json($guru->load(['user', 'jenisKelamin']), 200);
    }

    public function destroy($id)
    {
        $guru = Gurus::find($id);

        if (!$guru) {
            return response()->json(['message' => 'Guru tidak ditemukan'], 404);
        }

        $guru->delete();
        return response()->json(['message' => 'Guru berhasil dihapus'], 200);
    }

    public function getSiswaByGuru()
    {
        $userId = Auth::id();
        Log::info("User login ID: " . $userId);

        $guru = Gurus::where('user_id', $userId)->first();

        if (!$guru) {
            return response()->json(['message' => 'Data guru tidak ditemukan'], 404);
        }

        $siswa = Siswa::whereHas('kelas', function ($query) use ($guru) {
            $query->where('guru_id', $guru->id);
        })->with('kelas')->get();

        return response()->json($siswa, 200);
    }

    public function getJadwalByGuru()
    {
        $userId = Auth::id();
        Log::info("User login ID: " . $userId);

        $guru = Gurus::where('user_id', $userId)->first();

        if (!$guru) {
            return response()->json(['message' => 'Data guru tidak ditemukan'], 404);
        }

        $kelasIds = $guru->kelas()->pluck('id');

        if ($kelasIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $jadwal = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
            ->whereIn('kelas_id', $kelasIds)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $jadwal
        ]);
    }
    public function getKelasGuru()
    {
        $user = Auth::user();

        $guru = Gurus::where('user_id', $user->id)->first();
        if (!$guru) {
            return response()->json(['message' => 'Guru tidak ditemukan'], 404);
        }

        $kelas = Kelas::where('guru_id', $guru->id)->get();

        return response()->json($kelas);
    }

    public function getWaliKelasSiswa()
    {
        $user = Auth::user();


        if (!$user->siswa) {
            return response()->json(['message' => 'Akun ini bukan siswa'], 403);
        }

        $siswa = $user->siswa;


        if (!$siswa->kelas || !$siswa->kelas->guru) {
            return response()->json(['message' => 'Wali kelas tidak ditemukan.'], 404);
        }


        $guru = $siswa->kelas->guru;

        return response()->json([
            'success' => true,
            'data' => $guru
        ], 200);
    }
}
