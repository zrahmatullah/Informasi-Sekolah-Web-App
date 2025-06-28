<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSiswa extends Model
{
    use HasFactory;

    protected $table = 'nilai_siswa';

    protected $fillable = [
        'siswa_id',
        'mata_pelajaran_id',
        'nilai_tugas',
        'nilai_uts',
        'nilai_ujian',
        'rata_rata',
        'grade',
        'tahun_pelajaran_id',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }
    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }
}
