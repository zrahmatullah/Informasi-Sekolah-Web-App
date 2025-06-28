<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisKelaminToGuru extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->foreignId('jenis_kelamin_id')->constrained('jenis_kelamin')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropForeign(['jenis_kelamin_id']);
            $table->dropColumn('jenis_kelamin_id');
        });
    }
}
