<?php

namespace App\Http\Controllers\Api;

use App\Models\Gurus;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\NilaiSiswa;
use Illuminate\Http\Request;
use App\Models\MataPelajaran;
use App\Models\TahunPelajaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RaportSiswaController extends Controller
{
    public function cetakRaport($siswaId, $tahunPelajaranId)
    {
        $siswa = Siswa::findOrFail($siswaId);
        $tahun = TahunPelajaran::findOrFail($tahunPelajaranId);

        $nilai = NilaiSiswa::with('mataPelajaran')
            ->where('siswa_id', $siswaId)
            ->where('tahun_pelajaran_id', $tahunPelajaranId)
            ->get();

        $pdf = Pdf::loadView('raport.cetak-raport', compact('siswa', 'tahun', 'nilai'));
        return $pdf->stream("raport-{$siswa->nama_lengkap_siswa}.pdf");
    }

    public function cetakRaportByTahun(Request $request, $tahunPelajaranId)
    {

        $kelasId = $request->query('kelas_id');

        if (!$kelasId) {
            return response()->json(['message' => 'Parameter kelas_id wajib diisi.'], 400);
        }


        $tahun = TahunPelajaran::find($tahunPelajaranId);
        if (!$tahun) {
            return response()->json(['message' => 'Tahun pelajaran tidak ditemukan.'], 404);
        }


        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'User.'], 401);
        }


        $guru = Gurus::where('user_id', $user->id)->first();
        if (!$guru) {
            return response()->json(['message' => 'Data guru tidak ditemukan'], 404);
        }


        $kelas = Kelas::where('id', $kelasId)
            ->where('guru_id', $guru->id)
            ->first();

        if (!$kelas) {
            return response()->json(['message' => 'Kelas tidak ditemukan atau bukan milik guru ini'], 403);
        }


        $mapels = MataPelajaran::all();


        $siswas = Siswa::where('kelas_id', $kelas->id)
            ->orderBy('nama_lengkap_siswa')
            ->get();


        $data = [];
        foreach ($siswas as $siswa) {
            $nilaiPerMapel = [];
            $jumlah = 0;
            $jumlahMapelAdaNilai = 0;

            foreach ($mapels as $mapel) {
                $nilai = DB::table('nilai_siswa')
                    ->where('siswa_id', $siswa->id)
                    ->where('mata_pelajaran_id', $mapel->id)
                    ->where('tahun_pelajaran_id', $tahunPelajaranId)
                    ->value('rata_rata');

                if (is_numeric($nilai)) {
                    $nilai = ceil($nilai);
                    $jumlah += $nilai;
                    $jumlahMapelAdaNilai++;
                    $nilaiPerMapel[$mapel->id] = $nilai;
                } else {
                    $nilaiPerMapel[$mapel->id] = '-';
                }
            }

            $rata2 = $jumlahMapelAdaNilai > 0 ? round($jumlah / $jumlahMapelAdaNilai, 2) : 0;

            $data[] = [
                'nama_lengkap_siswa' => $siswa->nama_lengkap_siswa,
                'nilai' => $nilaiPerMapel,
                'jumlah' => $jumlah,
                'rata_rata' => $rata2,
                'rank' => 0,
            ];
        }

        usort($data, fn($a, $b) => $b['rata_rata'] <=> $a['rata_rata']);
        foreach ($data as $i => &$d) {
            $d['rank'] = $i + 1;
        }

        $wali_kelas = $guru->nama;

        $pdf = Pdf::loadView('raport.cetak-raport-semua-group', [
            'data' => $data,
            'mapels' => $mapels,
            'tahun' => $tahun,
            'kelas' => $kelas,
            'wali_kelas' => $wali_kelas,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("raport-{$kelas->nama_kelas}-{$tahun->tahun_pelajaran}.pdf");
    }



    public function cetakRaportLogin($tahunPelajaranId)
    {
        $user = Auth::user();

        if (!$user || !$user->siswa) {
            return abort(404, 'Siswa tidak ditemukan untuk user ini');
        }

        $siswa = $user->siswa;
        $tahun = TahunPelajaran::findOrFail($tahunPelajaranId);

        $nilai = NilaiSiswa::with(relations: 'mataPelajaran')
            ->where('siswa_id', $siswa->id)
            ->where('tahun_pelajaran_id', $tahunPelajaranId)
            ->get();

        $pdf = Pdf::loadView('raport.cetak-raport', compact('siswa', 'tahun', 'nilai'));
        return $pdf->stream("raport-{$siswa->nama_lengkap_siswa}.pdf");
    }

    public function cetakRaportUniversal(Request $request, $tahunPelajaranId, $siswaId = null)
    {
        if ($siswaId) {
            $siswa = Siswa::findOrFail($siswaId);
        } else {
            $user = Auth::user();
            if (!$user || !$user->siswa) {
                return abort(404, 'Siswa tidak ditemukan untuk user ini');
            }
            $siswa = $user->siswa;
        }

        $tahun = TahunPelajaran::findOrFail($tahunPelajaranId);

        $nilai = NilaiSiswa::with('mataPelajaran')
            ->where('siswa_id', $siswa->id)
            ->where('tahun_pelajaran_id', $tahunPelajaranId)
            ->get();

        $pdf = Pdf::loadView('raport.cetak-raport', compact('siswa', 'tahun', 'nilai'));
        return $pdf->stream("raport-{$siswa->nama_lengkap_siswa}.pdf");
    }
    public function cetakRaportSemuaSiswaKelas(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
        ]);

        $kelasId = $request->kelas_id;
        $tahunPelajaranId = $request->tahun_pelajaran_id;

        $kelas = Kelas::findOrFail($kelasId);
        $tahun = TahunPelajaran::findOrFail($tahunPelajaranId);

        $siswas = Siswa::where('kelas_id', $kelasId)->get();

        $dataRaport = [];

        foreach ($siswas as $siswa) {
            $nilai = NilaiSiswa::with('mataPelajaran')
                ->where('siswa_id', $siswa->id)
                ->where('tahun_pelajaran_id', $tahunPelajaranId)
                ->get();

            $dataRaport[] = [
                'siswa' => $siswa,
                'nilai' => $nilai,
                'kosong' => $nilai->isEmpty(),
            ];
        }

        $pdf = Pdf::loadView('raport.cetak-raport-semua', [
            'dataRaport' => $dataRaport,
            'tahun' => $tahun,
            'kelas' => $kelas,
        ]);

        return $pdf->stream("raport-semua-siswa.pdf");
    }
}
