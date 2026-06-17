<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nm_user' => 'SPV Kedisiplinan',
            'email' => 'spv.kedisiplinan@lab.test',
            'password' => Hash::make('password'),
            'role_user' => 'spv_kedisiplinan',
            'status' => 'aktif',
        ]);

        User::create([
            'nm_user' => 'SPV Jaringan',
            'email' => 'spv.jaringan@lab.test',
            'password' => Hash::make('password'),
            'role_user' => 'spv_jaringan',
            'status' => 'aktif',
        ]);

        for ($i = 1; $i <= 11; $i++) {
            User::create([
                'nm_user' => 'Admin Lab ' . $i,
                'email' => 'admin.lab' . $i . '@lab.test',
                'password' => Hash::make('password'),
                'role_user' => 'admin_lab',
                'status' => 'aktif',
            ]);

            User::create([
                'nm_user' => 'Asisten Lab ' . $i,
                'email' => 'asisten.lab' . $i . '@lab.test',
                'password' => Hash::make('password'),
                'role_user' => 'asisten_lab',
                'status' => 'aktif',
            ]);
        }
    }
}