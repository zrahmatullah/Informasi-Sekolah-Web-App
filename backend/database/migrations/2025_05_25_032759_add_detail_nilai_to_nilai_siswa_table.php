<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailNilaiToNilaiSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nilai_siswa', function (Blueprint $table) {
            $table->integer('nilai_tugas')->nullable();
            $table->integer('nilai_uts')->nullable();
            $table->integer('nilai_ujian')->nullable();
            $table->float('rata_rata')->nullable();
            $table->string('grade')->nullable();
        });
    }

    public function down()
    {
        Schema::table('nilai_siswa', function (Blueprint $table) {
            $table->dropColumn(['nilai_tugas', 'nilai_uts', 'nilai_ujian', 'rata_rata', 'grade']);
        });
    }
}
