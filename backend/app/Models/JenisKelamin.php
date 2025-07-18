<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKelamin extends Model
{
    use HasFactory;
    protected $table = 'jenis_kelamin';
    protected $fillable = ['jenis_kelamin'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    public function guru()
    {
        return $this->hasMany(Gurus::class);
    }
}
