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

        // $kritikSaranPerKelas = DB::table('kritik_sarans')
        //     ->join('siswas', 'kritik_sarans.siswa_id', '=', 'siswas.id')
        //     ->join('kelas', 'siswas.kelas_id', '=', 'kelas.id')
        //     ->select('kelas.nama_kelas', DB::raw('COUNT(*) as total'))
        //     ->groupBy('kelas.nama_kelas')
        //     ->orderBy('kelas.nama_kelas')
        //     ->pluck('total', 'kelas.nama_kelas');

                // $siswaPerKelas = Siswa::join('kelas', 'siswas.kelas_id', '=', 'kelas.id')
        //     ->selectRaw('kelas.nama_kelas as kelas, COUNT(siswas.id) as total')
        //     ->groupBy('kelas.nama_kelas')
        //     ->pluck('total', 'kelas')
        //     ->toArray();

        $kritikSaranPerKelas = DB::table('kelas')
            ->leftJoin('siswas', 'siswas.kelas_id', '=', 'kelas.id')
            ->leftJoin('kritik_sarans', 'kritik_sarans.siswa_id', '=', 'siswas.id')
            ->select('kelas.nama_kelas', DB::raw('COALESCE(COUNT(kritik_sarans.id), 0) as total'))
            ->groupBy('kelas.id','kelas.nama_kelas')
            ->orderBy('kelas.id','asc')
            ->get();

        $siswaPerKelas = DB::table('kelas')
            ->leftJoin('siswas', 'siswas.kelas_id', '=', 'kelas.id')
            ->select('kelas.nama_kelas', DB::raw('COUNT(siswas.id) as total'))
            ->groupBy('kelas.id', 'kelas.nama_kelas')
            ->orderBy('kelas.id', 'asc')
            ->pluck('total', 'kelas.nama_kelas')
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
            'kritikSaranPerKelas' => $kritikSaranPerKelas,
            'siswaPerKelas' => $siswaPerKelas,
            'pengumuman' => $pengumuman,
        ]);
    }
}
