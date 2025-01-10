<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'BROCOLI', 'variety_id' => 1],
        ];

        Crop::insert($data);
    }
}
