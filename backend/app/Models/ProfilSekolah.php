<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilSekolah extends Model
{
    use HasFactory;

    protected $table = 'profil_sekolah';

    protected $fillable = [
        'nama_sekolah',
        'alamat',
        'kepala_sekolah_id',
        'no_telp',
        'visi',
        'misi',
        'foto',
    ];

    public function kepalaSekolah()
    {
        return $this->belongsTo(Gurus::class, 'kepala_sekolah_id');
    }
}
