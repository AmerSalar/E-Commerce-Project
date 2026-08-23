<?php

namespace App\Helpers;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class HelperFunctions
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public static function compressImage(File|UploadedFile $picture)
    {
        $imgManager = new ImageManager(new Driver());
        $encodedImage = $imgManager->read($picture)
            ->scale(width: 512)
            ->toWebp(quality: 80);
        return $encodedImage;
    }
}
