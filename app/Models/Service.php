<?php

namespace App\Models;

use App\Models\Concerns\Localizes;
use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Localizes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Portfolio categories that belong to each service (the work categories of Work::CATEGORIES). */
    public const WORK_CATEGORIES = [
        'social-media' => ['social'],
        'documentation' => ['documentation'],
        'design' => ['design'],
        'photo-video-production' => ['photo', 'video'],
        'web-design-development' => ['web'],
    ];

    /** Package groups (the group_id text of a package) that belong to each service. */
    public const PACKAGE_GROUPS = [
        'social-media' => ['Sosial Media & Pembuatan Konten'],
        'documentation' => ['Dokumentasi'],
        'design' => ['Desain'],
        'photo-video-production' => ['Editing Video'],
    ];

    public function relatedWorks(int $limit = 6)
    {
        $categories = self::WORK_CATEGORIES[$this->slug] ?? [];

        if (! $categories) {
            return collect();
        }

        return Work::active()
            ->where(function ($query) use ($categories) {
                foreach ($categories as $category) {
                    $query->orWhere(fn ($q) => $q->inCategory($category));
                }
            })
            ->ordered()->take($limit)->get();
    }

    public function relatedPackages()
    {
        $groups = self::PACKAGE_GROUPS[$this->slug] ?? [];

        return $groups ? Package::active()->whereIn('group_id', $groups)->ordered()->get() : collect();
    }

    public function coverUrl(bool $thumb = false): ?string
    {
        return ImageUploader::url($this->cover_photo, $thumb);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
