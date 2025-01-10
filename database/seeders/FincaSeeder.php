<?php

namespace Database\Seeders;

use App\Models\Finca;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FincaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'FINCA ALAMEDA', 'code' => 'FAL', 'terminal_id' => 7],
            ['name' => 'FINCA LINDA SOFIA 1', 'code' => 'FLS1', 'terminal_id' => 1008],
            ['name' => 'FINCA LINDA SOFIA 2', 'code' => 'FLS2', 'terminal_id' => 1009],
            ['name' => 'FINCA TEHUYA', 'code' => 'FT', 'terminal_id' => 1011],
            ['name' => 'FINCA VICTORIA', 'code' => 'FV', 'terminal_id' => 1010],
            ['name' => 'FINCA OVEJERO', 'code' => 'FOV', 'terminal_id' => 1012],
        ];

        Finca::insert($data);
    }
}
