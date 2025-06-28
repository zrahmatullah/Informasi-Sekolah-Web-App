<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeysOnSiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Hapus dulu constraint lama (harus tahu nama constraintnya atau pakai DB raw)
            $table->dropForeign(['kelas_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['guru_id']);

            // Jadikan kolom nullable
            $table->foreignId('kelas_id')->nullable()->change();
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('guru_id')->nullable()->change();

            // Tambahkan constraint baru
            $table->foreign('kelas_id')->references('id')->on('kelas')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('guru_id')->references('id')->on('gurus')->nullOnDelete();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
