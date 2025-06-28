<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\KritikSaran;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGuru = User::where('role_id', 2)->count();
        $totalSiswa = Siswa::count();

        $totalWaliKelas = Kelas::whereNotNull('guru_id')
            ->distinct()
            ->count('guru_id');

        $totalUser = User::count();

        $totalKritikSaran = KritikSaran::count();

        $kritikSaranPerBulan = KritikSaran::selectRaw("TO_CHAR(created_at, 'Mon') as bulan, EXTRACT(MONTH FROM created_at) as bulan_angka, COUNT(*) as total")
            ->groupBy('bulan', 'bulan_angka')
            ->orderBy('bulan_angka')
            ->pluck('total', 'bulan');;

        $siswaPerKelas = Siswa::join('kelas', 'siswas.kelas_id', '=', 'kelas.id')
            ->selectRaw('kelas.nama_kelas as kelas, COUNT(siswas.id) as total')
            ->groupBy('kelas.nama_kelas')
            ->pluck('total', 'kelas')
            ->toArray();

        $pengumuman = DB::table('tanggal_liburs')
            ->select('judul', 'keterangan')
            ->latest('tanggal')
            ->limit(1)
            ->get();

        return response()->json([
            'totalUser' => $totalUser,
            'totalGuru' => $totalGuru,
            'totalSiswa' => $totalSiswa,
            'totalWaliKelas' => $totalWaliKelas,
            'totalKritikSaran' => $totalKritikSaran,
            'kritikSaranPerBulan' => $kritikSaranPerBulan,
            'siswaPerKelas' => $siswaPerKelas,
            'pengumuman' => $pengumuman,
        ]);
    }
}
