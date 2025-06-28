<?php

namespace App\Http\Controllers\Api;

use App\Models\Gurus;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\NilaiSiswa;
use Illuminate\Http\Request;
use App\Models\TahunPelajaran;
use Barryvdh\DomPDF\Facade\Pdf;
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
