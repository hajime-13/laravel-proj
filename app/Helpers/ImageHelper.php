<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Return the correct image URL whether it's a Cloudinary URL or a local storage path.
     */
    public static function url(?string $path): ?string
    {
        if (!$path) return null;

        // Already a full URL (Cloudinary)
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // Local storage path
        return \Illuminate\Support\Facades\Storage::url($path);
    }
}
