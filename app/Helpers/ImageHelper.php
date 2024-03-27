<?php

namespace App\Helpers;

class ImageHelper
{
    public static function optimizeAndResize($photo)
    {
        $imagePath = 'images/' . time() . '_' . $photo->getClientOriginalName();

        try {
            // Image optimaze Tinify.
            \Tinify\Tinify::setKey(env('TINIFY_API_KEY'));
            $source = \Tinify\fromBuffer(file_get_contents($photo->path()));

            $resized = $source->resize([
                'method' => 'cover',
                'width' => 70,
                'height' => 70,
            ]);

            $resized->toFile(public_path($imagePath));

            return $imagePath;
        } catch (\Exception $e) {
            // Handle exception if needed
            return null;
        }
    }
}
