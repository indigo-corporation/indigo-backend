<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class ImageService
{
    public static function processImage(mixed $source, string $path, string $fileName, int $width, int $height): void
    {
        if (!file_exists($path)) {
            mkdir($path, 0775, true);
        }

        Image::configure(['driver' => 'imagick']);

        Image::make($source)->encode('webp')->resize($width, $height)
            ->save($path . '/' . $fileName);

        ImageOptimizer::optimize($path . '/' . $fileName);
    }
}
