<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\NilaiSiswa;
use Illuminate\Support\Facades\DB;

class DashboardSiswaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return response()->json(['message' => 'Data siswa tidak ditemukan'], 404);
        }

        $totalMapel = MataPelajaran::count();


        $nilaiPerTahun = NilaiSiswa::where('nilai_siswa.siswa_id', $siswa->id)
            ->join('tahun_pelajaran', 'tahun_pelajaran.id', '=', 'nilai_siswa.tahun_pelajaran_id')
            ->select(
                'tahun_pelajaran.tahun_pelajaran as tahun',
                DB::raw('AVG(nilai_siswa.rata_rata) as rata_rata')
            )
            ->groupBy('tahun_pelajaran.tahun_pelajaran')
            ->orderBy('tahun_pelajaran.tahun_pelajaran')
            ->get();


        $labels = $nilaiPerTahun->pluck('tahun');
        $values = $nilaiPerTahun->pluck('rata_rata')->map(function ($val) {
            return $val ?? 0;
        });

        return response()->json([
            'total_mapel' => $totalMapel,
            'grafik_nilai' => [
                'labels' => $labels,
                'values' => $values,
            ],
        ]);
    }
}
