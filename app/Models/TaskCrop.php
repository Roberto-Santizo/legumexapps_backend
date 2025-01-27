<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskCrop extends Model
{
    
    public function crop()
    {
        return $this->BelongsTo(Crop::class);
    }
}
