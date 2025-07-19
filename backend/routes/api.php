<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\EmailOtpController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NilaiSiswaController;
use App\Http\Controllers\Api\SiswaMapelController;
use App\Http\Controllers\Api\KritikSaranController;
use App\Http\Controllers\Api\RaportSiswaController;
use App\Http\Controllers\Api\SuratEdaranController;
use App\Http\Controllers\Api\JenisKelaminController;
use App\Http\Controllers\Api\TanggalLiburController;
use App\Http\Controllers\Api\DashboardGuruController;
use App\Http\Controllers\Api\MataPelajaranController;
use App\Http\Controllers\Api\ProfilSekolahController;
use App\Http\Controllers\Api\DashboardSiswaController;
use App\Http\Controllers\Api\TahunPelajaranController;
use App\Http\Controllers\Api\JadwalPelajaranController;
use App\Http\Controllers\Api\CetakKritikSaranController;
use App\Http\Controllers\Api\RaportSiswaLoginController;

Route::post('login', [AuthController::class, 'login']);
Route::middleware('jwt.auth')->get('user', [AuthController::class, 'user']);
Route::middleware('jwt.auth')->post('logout', [AuthController::class, 'logout']);

Route::middleware('jwt.auth')->prefix('guru')->group(function () {
    Route::get('data-siswa', [GuruController::class, 'getSiswaByGuru']);


    Route::get('jadwal', [GuruController::class, 'getJadwalByGuru']);
    Route::get('kelas-guru', [GuruController::class, 'getKelasGuru']);


    Route::get('/', [GuruController::class, 'index']);
    Route::post('/', [GuruController::class, 'store']);
    Route::get('wali-kelas', [GuruController::class, 'getWaliKelasSiswa']);

    Route::get('{id}', [GuruController::class, 'show']);
    Route::put('{id}', [GuruController::class, 'update']);
    Route::delete('{id}', [GuruController::class, 'destroy']);


    Route::get('kelas/{kelasId}', [GuruController::class, 'getGuruByKelas']);
    Route::post('{guruId}/kelas/{kelasId}', [GuruController::class, 'assignGuruToKelas']);


    Route::post('{id}/create-user', [GuruController::class, 'createUserForGuru']);
    Route::post('{id}/reset-password', [GuruController::class, 'resetPassword']);
});

Route::middleware('jwt.auth')->prefix('siswa')->group(function () {
    Route::get('/', [SiswaController::class, 'index']);
    Route::post('{siswaId}/assign-kelas', [SiswaController::class, 'kelasToSiswa']);
    Route::get('/me', [SiswaController::class, 'getByLogin']);
    Route::post('/', [SiswaController::class, 'store']);
    Route::get('{id}', [SiswaController::class, 'show']);
    Route::put('{id}', [SiswaController::class, 'update']);
    Route::delete('{id}', [SiswaController::class, 'destroy']);
    Route::post('{id}/create-user', [SiswaController::class, 'createUserForSiswa']);
    Route::get('kelas/{kelasId}', [SiswaController::class, 'getSiswaByKelas']);
    Route::get('jenis-kelamin/{jeniskelaminId}', [SiswaController::class, 'getSiswaByJenisKelamin']);
});

Route::middleware('jwt.auth')->prefix('kelas')->group(function () {
    Route::get('/', [KelasController::class, 'index']);
    Route::post('/', [KelasController::class, 'store']);
    Route::get('{id}', [KelasController::class, 'show']);
    Route::put('{id}', [KelasController::class, 'update']);
    Route::delete('{id}', [KelasController::class, 'destroy']);
});

Route::middleware('jwt.auth')->prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('{id}', [RoleController::class, 'show']);
    Route::put('{id}', [RoleController::class, 'update']);
    Route::delete('{id}', [RoleController::class, 'destroy']);
});

Route::middleware('jwt.auth')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('{id}', [UserController::class, 'show']);
    Route::delete('{id}', [UserController::class, 'destroy']);
    Route::put('{id}/password', [UserController::class, 'updatePassword']);
});

Route::middleware('jwt.auth')->prefix('jenis-kelamin')->group(function () {
    Route::get('/', [JenisKelaminController::class, 'index']);
    Route::post('/', [JenisKelaminController::class, 'store']);
    Route::get('{id}', [JenisKelaminController::class, 'show']);
    Route::put('{id}', [JenisKelaminController::class, 'update']);
    Route::delete('{id}', [JenisKelaminController::class, 'destroy']);
});

Route::middleware('jwt.auth')->prefix('email-otp')->group(function () {
    Route::post('send', [EmailOtpController::class, 'sendOtp']);
    Route::post('verify', [EmailOtpController::class, 'verifyOtp']);
});

Route::middleware('jwt.auth')->prefix('matapelajaran')->group(function () {
    Route::get('/', [MataPelajaranController::class, 'index']);
    Route::post('/', [MataPelajaranController::class, 'store']);
    Route::get('{id}', [MataPelajaranController::class, 'show']);
    Route::put('{id}', [MataPelajaranController::class, 'update']);
    Route::delete('{id}', [MataPelajaranController::class, 'destroy']);
});

Route::middleware('jwt.auth')->get('siswa/{id}/mapel', [SiswaMapelController::class, 'index']);

// Route::middleware('jwt.auth')->prefix('nilai')->group(function () {
//     Route::get('/', [NilaiSiswaController::class, 'index']);
//     Route::get('/siswa/{siswa_id}', [NilaiSiswaController::class, 'showBySiswa']);
//     Route::post('/', [NilaiSiswaController::class, 'store']);
//     Route::put('/nilai/{id}', [NilaiSiswaController::class, 'update']);
//     // Route::put('/', [NilaiSiswaController::class, 'update']);
//     Route::put('/{id}', [NilaiSiswaController::class, 'update']);
//     Route::post('/batch', [NilaiSiswaController::class, 'storeBatch']);
// });

Route::middleware('jwt.auth')->prefix('nilai')->group(function () {
    Route::get('/', [NilaiSiswaController::class, 'index']);
    Route::get('/siswa/{siswa_id}', [NilaiSiswaController::class, 'showBySiswa']);
    Route::post('/', [NilaiSiswaController::class, 'store']);
    Route::put('/{id}', [NilaiSiswaController::class, 'update']);
    Route::post('/batch', [NilaiSiswaController::class, 'storeBatch']);
});


Route::prefix('nilai')->group(function () {
    Route::get('/kelas/{kelasId}/matapelajaran/{mapelId}/tahun/{tahunId}', [NilaiSiswaController::class, 'getNilaiByKelasMapel']);
    Route::get('/siswa/{siswaId}/tahun/{tahunId}', [NilaiSiswaController::class, 'getNilaiBySiswaDanTahun']);
});
Route::middleware('auth:api')->get('/siswa/nilai/{tahunId}', [NilaiSiswaController::class, 'getNilaiSiswaLogin']);

Route::middleware('jwt.auth')->prefix('jadwal-pelajaran')->group(function () {

    Route::get('/siswa/by-user', [JadwalPelajaranController::class, 'getJadwalBySiswa']);
    Route::get('/kelas', [KelasController::class, 'index']);
    Route::get('/guru', [GuruController::class, 'index']);
    Route::get('/mapel', [MataPelajaranController::class, 'index']);


    Route::get('/', [JadwalPelajaranController::class, 'index']);
    Route::post('/', [JadwalPelajaranController::class, 'store']);
    Route::get('/{id}', [JadwalPelajaranController::class, 'show']);
    Route::put('/{id}', [JadwalPelajaranController::class, 'update']);
    Route::delete('/{id}', [JadwalPelajaranController::class, 'destroy']);
});

Route::middleware('jwt.auth')->prefix('tahun-pelajaran')->group(function () {
    Route::get('/', [TahunPelajaranController::class, 'index']);
    Route::post('/', [TahunPelajaranController::class, 'store']);
    Route::get('{id}', [TahunPelajaranController::class, 'show']);
    Route::put('{id}', [TahunPelajaranController::class, 'update']);
    Route::delete('{id}', [TahunPelajaranController::class, 'destroy']);
});

Route::middleware('jwt.auth')->prefix('profil-sekolah')->group(function () {
    Route::get('/', [ProfilSekolahController::class, 'index']);
    Route::post('/', [ProfilSekolahController::class, 'store']);
    Route::put('{id}', [ProfilSekolahController::class, 'update']);
});

Route::middleware('jwt.auth')->group(function () {
    Route::get('siswa/{id}/mapel', [SiswaMapelController::class, 'index']);
});

Route::middleware('jwt.auth')->prefix('tanggal-libur')->group(function () {
    Route::get('/', [TanggalLiburController::class, 'index']);
    Route::post('/', [TanggalLiburController::class, 'store']);
    Route::put('{id}', [TanggalLiburController::class, 'update']);
    Route::delete('{id}', [TanggalLiburController::class, 'destroy']);
});

Route::get('surat-edaran/download/{filename}', [SuratEdaranController::class, 'downloadByFilename'])->name('download.surat');
Route::middleware('jwt.auth')->prefix('surat-edaran')->group(function () {
    Route::post('upload', [SuratEdaranController::class, 'upload']);
    Route::get('kelas/{kelasId}', [SuratEdaranController::class, 'getSiswaByKelas']);
    Route::post('kirim-per-kelas', [SuratEdaranController::class, 'kirimPerKelas']);
    Route::post('kirim-per-siswa', [SuratEdaranController::class, 'kirimPerSiswa']);
    // Route::get('download/{filename}', [SuratEdaranController::class, 'downloadByFilename'])->name('download.surat');
    Route::post('kirim', [SuratEdaranController::class, 'kirim']);
});

Route::middleware('jwt.auth')->prefix('dashboard')->group(function () {
    Route::get('stats', [DashboardController::class, 'index']);
});

Route::middleware('jwt.auth')->prefix('dashboard-guru')->group(function () {
    Route::get('stats', [DashboardGuruController::class, 'index']);
});

Route::middleware('jwt.auth')->prefix('dashboard-siswa')->group(function () {
    Route::get('stats', [DashboardSiswaController::class, 'index']);
});

Route::middleware('jwt.auth')->prefix('kritik-saran')->group(function () {
    Route::get('/', [KritikSaranController::class, 'index']);
    Route::post('kirim', [KritikSaranController::class, 'store']);
    Route::get('guru', [KritikSaranController::class, 'getByGuru']);
    Route::post('{id}/tanggapan', [KritikSaranController::class, 'tanggapi']);
});

Route::get('/cetak-raport/{siswaId}/{tahunPelajaranId}', [RaportSiswaController::class, 'cetakRaport']);

Route::middleware('auth:api')->get('/cetak-raport/{tahunPelajaranId}', [RaportSiswaController::class, 'cetakRaportByTahun']);

Route::get('/cetak-raport-login/{tahunPelajaranId}', [RaportSiswaController::class, 'cetakRaportLogin']);

Route::post('/raport/cetak-semua', [RaportSiswaController::class, 'cetakRaportSemuaSiswaKelas']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/siswa/raport/cetak/{tahunId}', [RaportSiswaLoginController::class, 'cetak']);
    Route::get('/cetak/kritik-saran', [CetakKritikSaranController::class, 'cetakSemua']);
});


Route::middleware(['auth:api'])->group(function () {
    Route::get('/siswa/raport/cetak/{tahunId}', [RaportSiswaLoginController::class, 'cetak']);
});

