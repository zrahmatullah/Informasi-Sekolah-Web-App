<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswas';

    protected $fillable = [
        'kelas_id',
        'guru_id',
        'nama_lengkap_siswa',
        'nis',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'anak_ke',
        'alamat',
        'rt_rw',
        'dusun',
        'kelurahan',
        'kecamatan',
        'kode_pos',
        'nama_ayah',
        'pekerjaan_ayah',
        'pendidikan_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'pendidikan_ibu',
        'nomor_telepon',
        'email',
        'user_id',
        'jenis_kelamin_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function jenisKelamin()
    {
        return $this->belongsTo(JenisKelamin::class);
    }


    public function mataPelajaran()
    {
        return $this->belongsToMany(MataPelajaran::class, 'nilai_siswa')
            ->withPivot(['nilai_tugas', 'nilai_uts', 'nilai_ujian', 'rata_rata', 'grade']);
    }

    public function nilaiSiswa()
    {
        return $this->hasMany(NilaiSiswa::class, 'siswa_id');
    }
}
