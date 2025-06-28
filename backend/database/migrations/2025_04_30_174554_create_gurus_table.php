<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGurusTable extends Migration
{
    public function up()
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->string('nrp_nip');
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('pendidikan');
            $table->date('tahun_masuk');
            $table->string('alamat');
            $table->string('rt_rw');
            $table->string('dusun');
            $table->string('kelurahan');
            $table->string('kecamatan');
            $table->string('kode_pos');
            $table->string('nomor_telpon');
            $table->string('email')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Relasi ke users
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gurus');
    }
}
