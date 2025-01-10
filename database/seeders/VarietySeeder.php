<?php

namespace Database\Seeders;

use App\Models\Finca;
use App\Models\Variety;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VarietySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'CLAREMONT'],
        ];

        Variety::insert($data);
    }
}
