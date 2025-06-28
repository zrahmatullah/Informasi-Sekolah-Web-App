<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKritikSaransTable extends Migration
{
    public function up()
    {
        Schema::create('kritik_sarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('guru_id');

            $table->text('isi');
            $table->text('tanggapan')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('gurus')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kritik_sarans');
    }
}
