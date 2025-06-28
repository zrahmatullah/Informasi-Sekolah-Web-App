<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gurus extends Model
{
    use HasFactory;

    protected $table = 'gurus';

    protected $fillable = [
        'nrp_nip',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'pendidikan',
        'tahun_masuk',
        'alamat',
        'rt_rw',
        'dusun',
        'kelurahan',
        'kecamatan',
        'kode_pos',
        'nomor_telpon',
        'email',
        'user_id',
        'jenis_kelamin_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jenisKelamin()
    {
        return $this->belongsTo(JenisKelamin::class);
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'guru_id');
    }
}
