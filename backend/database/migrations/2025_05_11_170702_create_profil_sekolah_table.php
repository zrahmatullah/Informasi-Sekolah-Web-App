<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilSekolahTable extends Migration
{
    public function up(): void
    {
        Schema::create('profil_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah');
            $table->text('alamat');
            $table->unsignedBigInteger('kepala_sekolah_id');
            $table->string('no_telp');
            $table->text('visi');
            $table->text('misi');
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->foreign('kepala_sekolah_id')->references('id')->on('gurus')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_sekolah');
    }
}
