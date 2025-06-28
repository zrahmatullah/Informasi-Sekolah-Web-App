<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('id', 1)->first();
        $guruRole = Role::where('id', 2)->first();
        $siswaRole = Role::where('id', 3)->first();

        if (!$adminRole || !$guruRole || !$siswaRole) {
            echo "Role not found!";
            return;
        }

        User::create([
            'username' => 'adminsekolah',
            'name' => 'Admin Sekolah',
            'email' => 'adminsekolah@example.com',
            'password' => Hash::make('admin'),
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'username' => 'guru',
            'name' => 'Guru Sekolah',
            'email' => 'guru@example.com',
            'password' => Hash::make('guru'),
            'role_id' => $guruRole->id,
        ]);

        User::create([
            'username' => 'siswa',
            'name' => 'Siswa Sekolah',
            'email' => 'siswa@example.com',
            'password' => Hash::make('siswa'),
            'role_id' => $siswaRole->id,
        ]);
    }
}
