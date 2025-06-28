<?php

namespace App\Http\Controllers\Api;

use App\Models\Kelas;
use App\Models\KritikSaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CetakKritikSaranController extends Controller
{

    public function cetakSemua(Request $request)
    {
        $judul = 'Laporan Semua Kritik & Saran';

        $tanggalAwal = $request->query('tanggal_awal');
        $tanggalAkhir = $request->query('tanggal_akhir');
        $username = $request->query('username', 'Tidak diketahui');

        $guru = Auth::user()->guru;

        if (!$guru) {
            return response()->json(['message' => 'Anda bukan guru atau belum terhubung ke data guru.'], 403);
        }

        $kelasIds = Kelas::where('guru_id', $guru->id)->pluck('id');

        $query = KritikSaran::with(['siswa.kelas', 'guru'])
            ->whereHas('siswa', function ($q) use ($kelasIds) {
                $q->whereIn('kelas_id', $kelasIds);
            });

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            $judul = "Laporan Kritik & Saran dari {$tanggalAwal} sampai {$tanggalAkhir}";
        }

        $kritikSarans = $query->orderBy('tanggal', 'desc')->get();

        $pdf = Pdf::loadView('kritikdansaran.cetak-kritik-saran', [
            'judul' => $judul,
            'kritikSarans' => $kritikSarans,
            'username' => $username,
        ]);

        return $pdf->stream('laporan-kritik-saran.pdf');
    }


    // Cetak khusus kritik & saran milik guru yang login
    // public function cetakByGuruLogin()
    // {
    //     $user = Auth::user();

    //     if (!$user || !$user->guru) {
    //         return abort(403, 'Akses hanya untuk guru.');
    //     }

    //     $kritikSarans = KritikSaran::with('siswa')
    //         ->where('guru_id', $user->guru->id)
    //         ->latest()
    //         ->get();

    //     return view('kritikdansaran.cetak-kritik-saran', [
    //         'judul' => 'Laporan Kritik & Saran Saya',
    //         'kritikSarans' => $kritikSarans
    //     ]);
    // }
}
