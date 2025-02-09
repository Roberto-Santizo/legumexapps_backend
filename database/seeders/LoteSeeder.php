<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lote;

class LoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['finca_id' => 1, 'name' => 'FAL004'],
            ['finca_id' => 1, 'name' => 'FAL005'],
            ['finca_id' => 1, 'name' => 'FAL006'],
            ['finca_id' => 1, 'name' => 'FAL009'],
            ['finca_id' => 1, 'name' => 'FAL010'],
            ['finca_id' => 2, 'name' => 'FLS-001'],
            ['finca_id' => 2, 'name' => 'FLS-002'],
            ['finca_id' => 2, 'name' => 'FLS-003'],
            ['finca_id' => 2, 'name' => 'FLS-004'],
            ['finca_id' => 2, 'name' => 'FLS-005'],
            ['finca_id' => 2, 'name' => 'FLS-006'],
            ['finca_id' => 2, 'name' => 'FLS-008'],
            ['finca_id' => 2, 'name' => 'FLS-009'],
            ['finca_id' => 2, 'name' => 'FLS-010'],
            ['finca_id' => 2, 'name' => 'FLS-011'],
            ['finca_id' => 2, 'name' => 'FLS-012'],
            ['finca_id' => 2, 'name' => 'FLS-013'],
            ['finca_id' => 2, 'name' => 'FLS-014'],
            ['finca_id' => 2, 'name' => 'FLS-015'],
            ['finca_id' => 2, 'name' => 'FLS-016'],
            ['finca_id' => 2, 'name' => 'FLS-017'],
            ['finca_id' => 2, 'name' => 'FLS-018'],
            ['finca_id' => 2, 'name' => 'FLS-019'],
            ['finca_id' => 2, 'name' => 'FLS-020'],
            ['finca_id' => 2, 'name' => 'FLS-021'],
            ['finca_id' => 2, 'name' => 'FLS-022'],
            ['finca_id' => 2, 'name' => 'FLS-023'],
            ['finca_id' => 2, 'name' => 'FLS-024'],
            ['finca_id' => 2, 'name' => 'FLS-025'],
            ['finca_id' => 2, 'name' => 'FLS-026'],
            ['finca_id' => 4, 'name' => 'FT-001'],
            ['finca_id' => 4, 'name' => 'FT-002'],
            ['finca_id' => 4, 'name' => 'FT-003'],
            ['finca_id' => 4, 'name' => 'FT-004'],
            ['finca_id' => 4, 'name' => 'FT-005'],
            ['finca_id' => 4, 'name' => 'FT-006'],
            ['finca_id' => 4, 'name' => 'FT-007'],
            ['finca_id' => 4, 'name' => 'FT-008'],
            ['finca_id' => 4, 'name' => 'FT-009'],
            ['finca_id' => 4, 'name' => 'FT-010'],
            ['finca_id' => 4, 'name' => 'FT-011'],
            ['finca_id' => 4, 'name' => 'FT-012'],
            ['finca_id' => 4, 'name' => 'FT-013'],
            ['finca_id' => 4, 'name' => 'FT-014'],
            ['finca_id' => 4, 'name' => 'FT-015'],
            ['finca_id' => 4, 'name' => 'FT-016'],
        ];

        Lote::insert($data);
    }
}
