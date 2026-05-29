<?php

namespace Database\Seeders;

use App\Models\Lab;
use Illuminate\Database\Seeder;

class LabSeeder extends Seeder
{
    public function run(): void
    {
        $labs = [];
        for ($i = 1; $i <= 11; $i++) {
            $labs[] = [
                'kd_lab' => 'LAB' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'nm_lab' => 'Laboratorium ' . $i,
                'status_lab' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Lab::insert($labs);
    }
}