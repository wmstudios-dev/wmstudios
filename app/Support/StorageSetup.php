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

    public static function ensurePortfolioFiles(): void
    {
        $marker = storage_path('framework/portfolio-files.synced');

        if (is_file($marker)) {
            return;
        }

        $base = database_path('data/portfolio');

        if (! is_dir($base)) {
            return;
        }

        try {
            $disk = Storage::disk('public');

            $slugs = collect(glob($base . '/*', GLOB_ONLYDIR))->map(fn ($d) => basename($d))->reject(fn ($s) => $s === 'logos')->all();

            foreach (Work::whereIn('slug', $slugs)->pluck('slug') as $slug) {
                foreach (glob("{$base}/{$slug}/*.webp") ?: [] as $file) {
                    $target = "uploads/works/{$slug}/" . basename($file);

                    if (! $disk->exists($target)) {
                        $disk->put($target, file_get_contents($file));
                    }
                }
            }

            foreach (glob("{$base}/logos/*.png") ?: [] as $file) {
                $target = 'uploads/clients/' . basename($file);

                if (! $disk->exists($target) && Client::where('logo', $target)->exists()) {
                    $disk->put($target, file_get_contents($file));
                }
            }

            @file_put_contents($marker, '1');
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
