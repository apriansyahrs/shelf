<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('job_titles')->insert([
            // Frontline/Staf
            ['title' => 'Kasir', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Pramuniaga/Sales Associate', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Customer Service', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Petugas Gudang', 'created_at' => now(), 'updated_at' => now()],

            // Manajemen Operasional
            ['title' => 'Supervisor/Koordinator', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Store Manager', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Area Manager', 'created_at' => now(), 'updated_at' => now()],

            // Manajemen Pendukung
            ['title' => 'Visual Merchandiser', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Inventory Controller', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Pemasar/Marketing', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'HRD/SDM', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Akuntan/Finance', 'created_at' => now(), 'updated_at' => now()],

            // Manajemen Senior
            ['title' => 'General Manager', 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Direktur', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
