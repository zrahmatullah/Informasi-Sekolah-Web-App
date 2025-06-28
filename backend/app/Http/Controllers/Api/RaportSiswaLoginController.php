<?php

namespace App\Http\Controllers\Api;

use App\Models\NilaiSiswa;
use Illuminate\Http\Request;
use App\Models\TahunPelajaran;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RaportSiswaLoginController extends Controller
{
    public function cetak($tahunId)
    {
        $user = Auth::user();

        if (!$user || !$user->siswa) {
            return response()->json([
                'message' => 'Siswa tidak ditemukan atau tidak memiliki akses.'
            ], 403);
        }

        $siswa = $user->siswa;
        $tahun = TahunPelajaran::findOrFail($tahunId);

        $nilai = NilaiSiswa::with('mataPelajaran')
            ->where('siswa_id', $siswa->id)
            ->where('tahun_pelajaran_id', $tahunId)
            ->get();

        if ($nilai->isEmpty()) {
            return response()->json([
                'message' => 'Nilai belum tersedia untuk tahun ini.'
            ], 404);
        }

        $data = [
            'siswa' => $siswa,
            'nilai' => $nilai,
            'tahun' => $tahun
        ];

        $pdf = Pdf::loadView('raport.cetak-raport', $data)->setPaper('A4', 'portrait');

        return response()->make($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="raport_' . $siswa->nama . '.pdf"',
        ]);
    }
}
