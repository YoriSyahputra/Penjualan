<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        // Check if categories already exist
        if (DB::table('categories')->count() === 0) {
            DB::table('categories')->insert([
                ['id' => 1, 'name' => 'Pakaian & Aksesoris', 'slug' => 'pakaian-dan-aksesoris'],
                ['id' => 2, 'name' => 'Rumah Tangga', 'slug' => 'rumah-tangga'],
                ['id' => 3, 'name' => 'Dekorasi', 'slug' => 'dekorasi'],
                ['id' => 4, 'name' => 'Kamar Mandi', 'slug' => 'kamar-mandi'],
                ['id' => 5, 'name' => 'Kebutuhan Rumah', 'slug' => 'kebutuhan-rumah'],
                ['id' => 6, 'name' => 'Tempat Penyimpanan', 'slug' => 'tempat-penyimpanan'],
                ['id' => 7, 'name' => 'Elektronik', 'slug' => 'elektronik'],
                ['id' => 8, 'name' => 'Action Figure', 'slug' => 'action-figure'],
                ['id' => 9, 'name' => 'Alat Olahraga', 'slug' => 'alat-olahraga']
            ]);
        }
    }
}