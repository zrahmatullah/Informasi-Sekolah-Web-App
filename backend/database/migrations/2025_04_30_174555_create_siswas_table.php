<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswasTable extends Migration
{
    public function up()
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->nullable()->constrained()->onDelete('set null'); // Relasi ke kelas
            $table->string('nis');
            $table->string('nisn');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('agama');
            $table->integer('anak_ke');
            $table->string('alamat');
            $table->string('rt_rw');
            $table->string('dusun');
            $table->string('kelurahan');
            $table->string('kecamatan');
            $table->string('kode_pos');
            $table->string('nama_ayah');
            $table->string('pekerjaan_ayah');
            $table->string('pendidikan_ayah');
            $table->string('nama_ibu');
            $table->string('pekerjaan_ibu');
            $table->string('pendidikan_ibu');
            $table->string('nomor_telepon');
            $table->string('email')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');  // Relasi ke users
            $table->foreignId('guru_id')->nullable()->constrained()->onDelete('set null');  // Relasi ke guru
            $table->timestamps();
        });
    }



    public function down()
    {
        Schema::dropIfExists('siswas');
    }
}
