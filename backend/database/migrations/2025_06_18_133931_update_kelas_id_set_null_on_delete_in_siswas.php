<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateKelasIdSetNullOnDeleteInSiswas extends Migration
{
    public function up()
    {
        // 1. Drop foreign key constraint lama
        DB::statement('ALTER TABLE siswas DROP CONSTRAINT siswas_kelas_id_foreign');

        // 2. Jadikan kolom nullable
        DB::statement('ALTER TABLE siswas ALTER COLUMN kelas_id DROP NOT NULL');

        // 3. Tambahkan kembali foreign key dengan ON DELETE SET NULL
        DB::statement('ALTER TABLE siswas ADD CONSTRAINT siswas_kelas_id_foreign FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE SET NULL');
    }

    public function down()
    {
        // Balik ke kondisi awal jika diperlukan
        DB::statement('ALTER TABLE siswas DROP CONSTRAINT siswas_kelas_id_foreign');
        DB::statement('ALTER TABLE siswas ALTER COLUMN kelas_id SET NOT NULL');
        DB::statement('ALTER TABLE siswas ADD CONSTRAINT siswas_kelas_id_foreign FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE');
    }
}
