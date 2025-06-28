<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratEdaran extends Model
{
    protected $table = 'surat_edaran';
    protected $fillable = ['judul', 'file'];
}
