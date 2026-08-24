<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    /**
     * Model that was bound to a route is not found
     * @param string $modelName
     * @return \Illuminate\Http\JsonResponse
     */
    public static function modelNotFound(string $modelName)
    {
        return response()->json([
            'message' => "$modelName not found!"
        ], 404);
    }
}
