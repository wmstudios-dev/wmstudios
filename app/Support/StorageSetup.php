<?php

namespace App\Support;

use App\Models\Client;
use App\Models\Work;
use Illuminate\Support\Facades\Storage;

/**
 * Makes uploaded and seeded pictures reachable on hosts where nobody runs `php artisan storage:link`
 * and where the build/pre-deploy container is not the one that serves the site (Railway).
 *
 * - ensureLink(): creates public/storage -> storage/app/public when it is missing.
 * - ensurePortfolioFiles(): the studio's own portfolio pictures live in the repo (database/data/portfolio) and are
 *   copied to the public disk once per container start, only for works/clients that still exist in the database.
 */
class StorageSetup
{
    public static function ensureLink(): void
    {
        $link = public_path('storage');

        if (file_exists($link) || is_link($link)) {
            return;
        }

        try {
            symlink(storage_path('app/public'), $link);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Absolute path of a picture requested as /storage/<path>: the file on the public disk, or else the studio's
     * own portfolio picture shipped in the repo (so those never depend on the uploads folder being writable).
     */
    public static function locate(string $path): ?string
    {
        if (! str_starts_with($path, 'uploads/') || str_contains($path, '..')) {
            return null;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            return $disk->path($path);
        }

        $base = database_path('data/portfolio');

        if (preg_match('#^uploads/works/([a-z0-9-]+)/((?:\d\d|cover[a-z0-9-]*?)(?:_thumb)?\.webp)$#', $path, $m)) {
            $file = "{$base}/{$m[1]}/{$m[2]}";
        } elseif (preg_match('#^uploads/clients/([a-z0-9-]+\.png)$#', $path, $m)) {
            $file = "{$base}/logos/{$m[1]}";
        } else {
            return null;
        }

        return is_file($file) ? $file : null;
    }

    /** Can the web process create files in the uploads folder? (False on a host where the volume is root-only.) */
    public static function uploadsWritable(): bool
    {
        $dir = storage_path('app/public/uploads');
        @mkdir($dir, 0775, true);
        $probe = $dir . '/.write-test';
        $ok = @file_put_contents($probe, '1') !== false;
        @unlink($probe);

        return $ok;
    }

    public static function ensurePortfolioFiles(): void
    {
        $marker = storage_path('framework/portfolio-files.synced');

        if (is_file($marker)) {
            // '1' = done for this container; anything else = a failed attempt, retried at most every ten minutes.
            if (@file_get_contents($marker) === '1' || filemtime($marker) > time() - 600) {
                return;
            }
        }

        $base = database_path('data/portfolio');

        if (! is_dir($base)) {
            return;
        }

        try {
            $disk = Storage::disk('public');
            $allCopied = true;

            $slugs = collect(glob($base . '/*', GLOB_ONLYDIR))->map(fn ($d) => basename($d))->reject(fn ($s) => $s === 'logos')->all();

            foreach (Work::whereIn('slug', $slugs)->pluck('slug') as $slug) {
                foreach (glob("{$base}/{$slug}/*.webp") ?: [] as $file) {
                    $target = "uploads/works/{$slug}/" . basename($file);

                    if (! $disk->exists($target)) {
                        $allCopied = $disk->put($target, file_get_contents($file)) && $allCopied;
                    }
                }
            }

            foreach (glob("{$base}/logos/*.png") ?: [] as $file) {
                $target = 'uploads/clients/' . basename($file);

                if (! $disk->exists($target) && Client::where('logo', $target)->exists()) {
                    $allCopied = $disk->put($target, file_get_contents($file)) && $allCopied;
                }
            }

            // Not copied (read-only folder)? Then try again next time; locate() keeps the pictures visible meanwhile.
            @file_put_contents($marker, $allCopied ? '1' : '0');
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
