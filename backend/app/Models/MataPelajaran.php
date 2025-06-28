<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $fillable = ['nama_mapel', 'guru_id'];

    public function gurus()
    {
        return $this->belongsTo(Gurus::class, 'guru_id');
    }

    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'nilai_siswa')->withPivot('nilai');
    }
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }
}
