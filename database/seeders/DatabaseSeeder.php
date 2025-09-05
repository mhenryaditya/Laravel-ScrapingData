<?php

namespace Database\Seeders;

use App\Models\UIGMRMetriks;
use App\Models\UIGMRPeringkat;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        UIGMRMetriks::factory()->count(6)->sequence(
            [
                'nama_metriks_lengkap' => 'Setting & Infractructure',
                'nama_metriks_singkat' => 'SI',
            ],
            [
                'nama_metriks_lengkap' => 'Energy & Climate Change',
                'nama_metriks_singkat' => 'EC',
            ],
            [
                'nama_metriks_lengkap' => 'Waste',
                'nama_metriks_singkat' => 'WS',
            ],
            [
                'nama_metriks_lengkap' => 'Water',
                'nama_metriks_singkat' => 'WR',
            ],
            [
                'nama_metriks_lengkap' => 'Transportation',
                'nama_metriks_singkat' => 'TR',
            ],
            [
                'nama_metriks_lengkap' => 'Education & Research',
                'nama_metriks_singkat' => 'ED',
            ],
        )->create();
    }
}
