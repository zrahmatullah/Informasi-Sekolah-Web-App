<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KritikSaran extends Model
{
    protected $fillable = [
        'siswa_id',
        'guru_id',
        'isi',
        'tanggapan',
        'judul',
        'tanggal',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru()
    {
        return $this->belongsTo(Gurus::class);
    }
}
