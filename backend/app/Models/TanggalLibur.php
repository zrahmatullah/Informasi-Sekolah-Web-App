<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TanggalLibur extends Model
{
    protected $fillable = ['tanggal', 'judul', 'keterangan'];
}
