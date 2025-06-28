<?php

namespace App\Http\Controllers\Api;

use Log;
use App\Models\Siswa;
use App\Models\SuratEdaran;
use Illuminate\Http\Request;
use App\Mail\KirimSuratEdaran;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SuratEdaranController extends Controller
{
    public function index()
    {
        return response()->json(SuratEdaran::latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'file' => 'required|mimes:pdf,doc,docx|max:2048',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $filePath = $request->file('file')->store('surat-edaran', 'public');

        $surat = SuratEdaran::create([
            'judul' => $request->judul,
            'file' => $filePath,
        ]);

        $siswaList = Siswa::where('kelas_id', $request->kelas_id)->get();

        foreach ($siswaList as $siswa) {
            if ($siswa->email) {
                Mail::to($siswa->email)->send(new KirimSuratEdaran($surat));
            }
        }

        return response()->json(['message' => 'Surat berhasil dikirim ke siswa.']);
    }

    public function download($id)
    {
        $surat = SuratEdaran::findOrFail($id);


        return response()->download(storage_path('app/public/' . $surat->file));
    }

    public function destroy($id)
    {
        $surat = SuratEdaran::findOrFail($id);

        if (Storage::disk('public')->exists($surat->file)) {
            Storage::disk('public')->delete($surat->file);
        }

        $surat->delete();

        return response()->json(['message' => 'Surat berhasil dihapus.']);
    }

    public function upload(Request $request)
    {
        Log::info($request->all());
        Log::info($request->file('file'));

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('surat-edaran', 'public');

            return response()->json([
                'message' => 'Upload berhasil',
                'path' => $path,
            ]);
        }

        return response()->json(['message' => 'Tidak ada file yang diupload'], 400);
    }

    public function kirim(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'file' => 'required|string|ends_with:.pdf,.doc,.docx',
            'siswa_id' => 'required|exists:siswas,id',
        ]);


        $surat = SuratEdaran::create([
            'judul' => $request->judul,
            'file' => $request->file,
        ]);


        $siswa = Siswa::find($request->siswa_id);


        if ($siswa && $siswa->email) {
            $email = trim($siswa->email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($email)->send(new KirimSuratEdaran($surat));
            }
        }

        return response()->json(['message' => 'Surat berhasil dikirim ke siswa yang dipilih.']);
    }
}
