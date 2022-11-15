<?php

namespace App\Http\Service;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFileService
{
    public function uploadImageFromFile($file, $uploadDestination, $width = 500, $height = 500)
    {

        $fileName = $uploadDestination . Str::uuid()  . '.' . $file->getClientOriginalExtension();
        $image = Image::make($file)->encode($file->getClientOriginalExtension());
        Storage::put($fileName, $image);

        return [
            'storage' => $fileName,
            'path' => str_replace('public', 'storage', $fileName)
        ];
    }

    public function addWaterMark()
    {
    }

    public function deleteFile($deletePath)
    {
        if (is_array($deletePath)) {
            $deletePath = array_map(function ($v) {
                return str_replace('storage', 'public', $v);
            }, $deletePath);
        } else {
            $deletePath = str_replace('storage', 'public', $deletePath);
        }
        Storage::delete($deletePath);
    }
}
