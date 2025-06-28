<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelas',
        'kode_kelas',
        'guru_id',
    ];
    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(Gurus::class, 'guru_id');
    }
}
