<?php

namespace App\Repositories;

use App\Models\AgricolaImage;

class AgricolaImageRepository
{
    public function createAgricolaImage($data)
    {
        return AgricolaImage::create($data);
    }
}
