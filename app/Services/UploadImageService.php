<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;


class UploadImageService
{
    public function uploadImage(string $base64Image, string $path)
    {
        list(, $base64Image) = explode(',', $base64Image);
        $base64Image = base64_decode($base64Image);
        $uniqueId = uniqid();
        $filename = "{$path}/{$uniqueId}.png";
        Storage::disk('s3')->put($filename, $base64Image);

        return $filename;
    }
}
