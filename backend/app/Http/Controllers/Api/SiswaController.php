<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::with(['user', 'jenisKelamin'])->get();
        return response()->json($siswas);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap_siswa' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:gurus,id',
            'nis' => 'required|numeric',
            'nisn' => 'required|numeric',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string',
            'anak_ke' => 'required|integer',
            'alamat' => 'required|string',
            'rt_rw' => 'required|string',
            'dusun' => 'required|string',
            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'kode_pos' => 'required|string',
            'nama_ayah' => 'required|string',
            'pekerjaan_ayah' => 'required|string',
            'pendidikan_ayah' => 'required|string',
            'nama_ibu' => 'required|string',
            'pekerjaan_ibu' => 'required|string',
            'pendidikan_ibu' => 'required|string',
            'nomor_telepon' => 'required|string',
            'email' => 'required|email',
            'jenis_kelamin_id' => 'required|exists:jenis_kelamin,id'
        ]);

        $siswa = Siswa::create($validated);

        return response()->json($siswa, 201);
    }


    public function show($id)
    {
        if (is_null($id)) {
            return response()->json(['message' => 'ID siswa tidak valid'], 400);
        }

        $siswa = Siswa::with('jenisKelamin')->find($id);

        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        return response()->json($siswa);
    }
    public function getByLogin(Request $request)
    {
        $user = $request->user();

        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return response()->json([
                'message' => 'Siswa tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'data' => $siswa
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap_siswa' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'nis' => 'required|numeric|unique:siswas,nis,' . $id,
            'nisn' => 'required|numeric|unique:siswas,nisn,' . $id,
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string|max:50',
            'anak_ke' => 'required|integer',
            'alamat' => 'required|string',
            'rt_rw' => 'required|string',
            'dusun' => 'required|string',
            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'kode_pos' => 'required|string',
            'nama_ayah' => 'required|string',
            'pekerjaan_ayah' => 'required|string',
            'pendidikan_ayah' => 'required|string',
            'nama_ibu' => 'required|string',
            'pekerjaan_ibu' => 'required|string',
            'pendidikan_ibu' => 'required|string',
            'nomor_telepon' => 'required|string',
            'email' => 'required|email',
            'jenis_kelamin_id' => 'required|exists:jenis_kelamin,id',
        ]);

        $siswa = Siswa::find($id);

        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        $siswa->update($request->except('guru_id'));

        $kelas = Kelas::find($siswa->kelas_id);
        if ($kelas && $kelas->guru_id) {
            $siswa->guru_id = $kelas->guru_id;
            $siswa->save();
        }

        return response()->json(['message' => 'Data siswa berhasil diupdate', 'data' => $siswa]);
    }


    public function destroy($id)
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        $siswa->delete();

        return response()->json(['message' => 'Data siswa berhasil dihapus']);
    }

    public function createUserForSiswa($siswaId, Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        DB::beginTransaction();

        try {
            $siswa = Siswa::findOrFail($siswaId);

            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),


                'role_id' => 3,
            ]);

            $siswa->update(['user_id' => $user->id]);

            DB::commit();

            return response()->json([
                'message' => 'Akun siswa berhasil dibuat dan dihubungkan.',
                'siswa' => $siswa,
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal membuat akun siswa', 'error' => $e->getMessage()], 500);
        }
    }

    public function getSiswaByKelas($kelasId)
    {
        $siswa = Siswa::with('jenisKelamin')
            ->where('kelas_id', $kelasId)
            ->get();

        if ($siswa->isEmpty()) {
            return response()->json(['message' => 'Tidak ada siswa di kelas ini'], 404);
        }

        return response()->json($siswa);
    }

    public function getSiswaByNama($nama)
    {
        $siswas = Siswa::with('jenisKelamin')
            ->where('nama_lengkap_siswa', 'like', '%' . $nama . '%')
            ->get();

        if ($siswas->isEmpty()) {
            return response()->json(['message' => 'Tidak ada siswa dengan nama tersebut'], 404);
        }

        return response()->json($siswas);
    }

    public function getSiswaByJenisKelamin($jenisKelaminId)
    {
        $siswa = Siswa::with('jenisKelamin')
            ->where('jenis_kelamin_id', $jenisKelaminId)
            ->get();

        if ($siswa->isEmpty()) {
            return response()->json(['message' => 'Tidak Ada Jenis Kelamin'], 404);
        }

        return response()->json($siswa);
    }

    public function getByKelas($kelasId)
    {
        $siswa = Siswa::with('jenisKelamin')
            ->where('kelas_id', $kelasId)
            ->get();

        return response()->json($siswa);
    }
    public function kelasToSiswa(Request $request, $siswaId)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        $siswa = Siswa::find($siswaId);

        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        $siswa->kelas_id = $request->kelas_id;
        $siswa->save();

        return response()->json(['message' => 'Kelas berhasil di-mapping ke siswa', 'data' => $siswa]);
    }
}
