<?php

namespace Database\Seeders;

use App\Models\Lab;
use App\Models\PenugasanUserLab;
use App\Models\User;
use Illuminate\Database\Seeder;

class PenugasanSeeder extends Seeder
{
    public function run(): void
    {
        $spvJaringan = User::where('email', 'spv.jaringan@lab.test')->first();

        foreach (Lab::all() as $lab) {
            // Contoh: SPV Jaringan menjadi PIC semua lab untuk data awal.
            PenugasanUserLab::create([
                'status_aktif' => 'aktif',
                'semester' => 'ganjil',
                'tahun_ajaran' => '2025/2026',
                'id_user' => $spvJaringan->id_user,
                'id_lab' => $lab->id_lab,
            ]);

            $number = (int) str_replace('LAB', '', $lab->kd_lab);

            $admin = User::where('email', 'admin.lab' . $number . '@lab.test')->first();
            $asisten = User::where('email', 'asisten.lab' . $number . '@lab.test')->first();

            foreach ([$admin, $asisten] as $user) {
                PenugasanUserLab::create([
                    'status_aktif' => 'aktif',
                    'semester' => 'ganjil',
                    'tahun_ajaran' => '2025/2026',
                    'id_user' => $user->id_user,
                    'id_lab' => $lab->id_lab,
                ]);
            }
        }
    }
}