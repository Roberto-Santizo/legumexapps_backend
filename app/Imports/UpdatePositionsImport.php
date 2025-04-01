<?php

namespace App\Imports;

use App\Models\Line;
use App\Models\LinePosition;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UpdatePositionsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */

    public $line;

    public function __construct($id)
    {
        $this->line = Line::find($id);
    }

    public function collection(Collection $rows)
    {
        $this->line->positions()->delete();

        foreach ($rows as $row) {
            LinePosition::create([
                'line_id' => $this->line->id,
                'name' => $row['posicion']
            ]);
        }
    }
}
