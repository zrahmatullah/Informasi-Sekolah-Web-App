<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Siswa;
use App\Models\KritikSaran;
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardGuruController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $guruId = $user->guru->id ?? null;

        $jumlahSiswa = Siswa::where('guru_id', $guruId)->count();
        $totalKritik = KritikSaran::where('guru_id', $guruId)->count();

        $kritikSaran = KritikSaran::selectRaw("
        TO_CHAR(created_at, 'Mon') as bulan,
        EXTRACT(MONTH FROM created_at) as bulan_angka,
        COUNT(*) as total
    ")
            ->whereYear('created_at', now()->year)
            ->where('guru_id', $guruId)
            ->groupBy('bulan', 'bulan_angka')
            ->orderBy('bulan_angka')
            ->get();

        $labels = $kritikSaran->pluck('bulan')->toArray();
        $values = $kritikSaran->pluck('total')->toArray();

        $pengumuman = DB::table('tanggal_liburs')
            ->select('judul', 'keterangan')
            ->latest('tanggal')
            ->first();

        $hariIni = Carbon::now()->translatedFormat('l');

        $jadwalHariIni = JadwalPelajaran::with(['mapel', 'kelas'])
            ->where('hari', $hariIni)
            ->where('guru_id', $guruId)
            ->get();

        return response()->json([
            'jumlah_siswa' => $jumlahSiswa,
            'total_kritik' => $totalKritik,
            'kritik_perbulan' => [
                'labels' => $labels,
                'values' => $values,
            ],
            'pengumuman' => $pengumuman ? "{$pengumuman->judul} - {$pengumuman->keterangan}" : '-',
            'jadwal_hari_ini' => $jadwalHariIni,
        ]);
    }
}
