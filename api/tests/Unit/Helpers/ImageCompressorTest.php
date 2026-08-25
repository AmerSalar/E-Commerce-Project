<?php

namespace Tests\Unit;

use App\Helpers\HelperFunctions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageCompressorTest extends TestCase
{
    public function test_image_compressing_works_as_intended(): void
    {
        $pictureFile = UploadedFile::fake()
            ->image('product.jpg', 2000, 2000)
            ->size(2048);

        $compressedPicture = HelperFunctions::compressImage($pictureFile);

        // fake picture is uploaded file, but compressed picture is encoded in binary
        // so we need to get byte size to compare them
        $bytes = strlen((string) $compressedPicture);
        // check if size reduced
        $this->assertLessThan($pictureFile->getSize(), $bytes);
        // check if extension is webp
        $this->assertEquals('image/webp', $compressedPicture->mimetype());


        $pictureInformation = getimagesizefromstring((string) $compressedPicture);
        $width = $pictureInformation[0];
        // check if dimensions reduced
        $this->assertLessThanOrEqual(512, $width);
    }
}
