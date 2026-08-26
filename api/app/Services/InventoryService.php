<?php

namespace App\Services;

use App\Helpers\HelperFunctions;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function storeProduct(?UploadedFile $picture, array $validated): Product
    {
        $generatedFileName = null;
        if ($picture) {
            $generatedFileName = 'products/' . Str::uuid() . '.webp';

            $encodedImage = HelperFunctions::compressImage($picture);
            Storage::disk('public')->put($generatedFileName, (string) $encodedImage);

            $validated['picture_url'] = $generatedFileName;
            unset($validated['picture']);
        }
        try {
            // transaction means do all, if one of them fails, everyone fail too
            return DB::transaction(function () use ($validated) {

                $product = Product::create($validated);
                // category_ids is a required field
                $product->categories()->attach($validated['category_ids']);

                return $product;
            });
        } catch (\Throwable $e) {
            if ($generatedFileName) {
                Storage::disk('public')->delete($generatedFileName);
            }
            throw ValidationException::withMessages([
                'message' => "failed to store product!"
            ]);
        }
    }
}
