<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateKelasIdForeignOnSiswas extends Migration
{
    public function up()
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Hapus foreign key lama (pastikan nama constraint benar, misal: siswas_kelas_id_foreign)
            $table->dropForeign(['kelas_id']);

            // Tambah foreign key baru dengan SET NULL
            $table->foreign('kelas_id')->references('id')->on('kelas')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
        });
    }
}
