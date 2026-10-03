<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores uploaded pictures on the public disk, shrunk and converted to WebP so the site stays fast
 * (a phone photo is often 4-8 MB). A small "_thumb" copy is made for grids.
 * When PHP has no GD (or the file is a GIF/SVG), the original is stored untouched.
 */
class ImageUploader
{
    public const MAX_WIDTH = 1920;
    public const THUMB_WIDTH = 640;
    public const QUALITY = 82;

    private static array $thumbCache = [];

    public static function store(UploadedFile $file, string $folder): string
    {
        $dir = 'uploads/' . trim($folder, '/');

        try {
            if ($path = self::process($file, $dir)) {
                return $path;
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $file->store($dir, 'public');
    }

    /** Removes an uploaded picture and its thumbnail. Seeded/shared files (outside uploads/) are left alone. */
    public static function delete(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'uploads/')) {
            return;
        }

        Storage::disk('public')->delete([$path, self::thumbPath($path)]);
    }

    public static function url(?string $path, bool $thumb = false): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        if ($thumb) {
            $thumbPath = self::thumbPath($path);
            self::$thumbCache[$thumbPath] ??= Storage::disk('public')->exists($thumbPath);

            if (self::$thumbCache[$thumbPath]) {
                $path = $thumbPath;
            }
        }

        return asset('storage/' . $path);
    }

    private static function thumbPath(string $path): string
    {
        return preg_replace('/\.(webp|jpe?g|png)$/i', '', $path) . '_thumb.webp';
    }

    private static function process(UploadedFile $file, string $dir): ?string
    {
        $mime = (string) $file->getMimeType();

        if (! function_exists('imagewebp') || ! in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return null;
        }

        // Decoding a big photo needs far more memory than its file size.
        @ini_set('memory_limit', '512M');

        $source = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            default => @imagecreatefromwebp($file->getRealPath()),
        };

        if (! $source) {
            return null;
        }

        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $source = self::fixOrientation($source, $file->getRealPath());
        }

        imagepalettetotruecolor($source);
        imagealphablending($source, true);
        imagesavealpha($source, true);

        $name = Str::random(32);
        $main = self::scaled($source, self::MAX_WIDTH);
        $thumb = self::scaled($source, self::THUMB_WIDTH);

        $disk = Storage::disk('public');
        $disk->makeDirectory($dir);

        $mainPath = "{$dir}/{$name}.webp";
        $thumbPath = "{$dir}/{$name}_thumb.webp";

        $ok = imagewebp($main, $disk->path($mainPath), self::QUALITY)
            && imagewebp($thumb, $disk->path($thumbPath), self::QUALITY);

        foreach ([$source, $main, $thumb] as $img) {
            imagedestroy($img);
        }

        return $ok ? $mainPath : null;
    }

    private static function scaled($image, int $maxWidth)
    {
        $width = imagesx($image);

        if ($width <= $maxWidth) {
            $copy = imagecreatetruecolor($width, imagesy($image));
            imagealphablending($copy, false);
            imagesavealpha($copy, true);
            imagecopy($copy, $image, 0, 0, 0, 0, $width, imagesy($image));

            return $copy;
        }

        $scaled = imagescale($image, $maxWidth, -1, IMG_BICUBIC);
        imagesavealpha($scaled, true);

        return $scaled;
    }

    private static function fixOrientation($image, string $filePath)
    {
        $exif = @exif_read_data($filePath);
        $rotation = match ($exif['Orientation'] ?? 1) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($rotation === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $rotation, 0);

        return $rotated ?: $image;
    }
}
