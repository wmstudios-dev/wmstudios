<?php

namespace App\Models;

use App\Models\Concerns\Localizes;
use App\Support\ImageUploader;
use App\Support\VideoEmbed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use Localizes;

    protected $guarded = ['id'];

    public const CATEGORIES = ['photo', 'video', 'design', 'web', 'social'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'year' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function photos()
    {
        return $this->hasMany(WorkPhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('year')->orderByDesc('id');
    }

    public function coverUrl(bool $thumb = false): ?string
    {
        // A video work without its own cover borrows the YouTube thumbnail when there is one.
        return ImageUploader::url($this->cover_photo, $thumb) ?? $this->video()['thumb'] ?? null;
    }

    /** Is the main photo wider than tall? (A landscape one can run full width; a poster must be shown whole.) */
    public function coverIsLandscape(): bool
    {
        $file = $this->cover_photo ? \App\Support\StorageSetup::locate($this->cover_photo) : null;
        $size = $file ? @getimagesize($file) : false;

        return $size ? $size[0] > $size[1] : false;
    }

    public function beforeUrl(): ?string
    {
        return ImageUploader::url($this->before_photo);
    }

    public function afterUrl(): ?string
    {
        return ImageUploader::url($this->after_photo);
    }

    public function hasBeforeAfter(): bool
    {
        return $this->before_photo && $this->after_photo;
    }

    public function video(): ?array
    {
        return VideoEmbed::parse($this->video_url);
    }

    public function categoryLabel(): string
    {
        return __('site.categories.' . $this->category);
    }
}
