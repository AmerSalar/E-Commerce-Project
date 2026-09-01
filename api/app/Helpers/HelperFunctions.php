<?php

namespace App\Helpers;


use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\HttpFoundation\Cookie;

class HelperFunctions
{
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

    public static function makeCookie(
        string $cookieName,
        string $cookieValue,
        int $lifespan
    ): Cookie {
        return cookie(
            $cookieName,
            $cookieValue,
            $lifespan,
            '/',
            null,
            config('app.env') === "production",
            true,
            false,
            "Lax"
        );
    }
}
