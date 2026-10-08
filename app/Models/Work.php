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

    public const CATEGORIES = ['photo', 'video', 'documentation', 'design', 'web', 'social'];

    /** Where a wide cover stays in view when it is cropped (cards, menu): name => [admin label, CSS object-position]. */
    public const COVER_FOCUS = [
        'left' => ['Left', '15% 50%'],
        'center-left' => ['Centre-left', '35% 50%'],
        'center' => ['Centre (default)', '50% 50%'],
        'center-right' => ['Centre-right', '65% 50%'],
        'right' => ['Right', '85% 50%'],
    ];

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

    /** CSS object-position for the cover when it is cropped to fit a card. */
    public function coverPosition(): string
    {
        return self::COVER_FOCUS[$this->cover_focus][1] ?? '50% 50%';
    }

    /** Is the main photo wider than tall? (A landscape one can run full width; a poster must be shown whole.) */
    public function coverIsLandscape(): bool
    {
        $file = $this->cover_photo ? \App\Support\StorageSetup::locate($this->cover_photo) : null;
        $size = $file ? @getimagesize($file) : false;

        // wider than tall, and big enough to run full width without turning blurry
        return $size ? $size[0] > $size[1] && $size[0] >= 900 : false;
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

    /**
     * The extra videos of the project: one per line as "link | title | vertical" (title and "vertical" are optional).
     * Links the player cannot embed are skipped.
     *
     * @return array<int, array{embed: string, type: string, thumb: ?string, vertical: bool, title: string}>
     */
    public function moreVideos(): array
    {
        $videos = [];

        foreach (preg_split('/\R/', (string) $this->extra_videos) as $line) {
            $parts = array_map('trim', explode('|', $line));
            $video = VideoEmbed::parse($parts[0] ?? '');

            if (! $video || $video['type'] === 'link') {
                continue;
            }

            $video['vertical'] = $video['vertical'] || (isset($parts[2]) && stripos($parts[2], 'vert') === 0);
            $video['title'] = ($parts[1] ?? '') !== '' ? $parts[1] : __('site.works.video_n', ['n' => count($videos) + 2]);
            $videos[] = $video;
        }

        return $videos;
    }

    /** Main category first, then the extra ones (only known categories, no duplicates). */
    public function allCategories(): array
    {
        $extra = array_filter(explode(',', (string) $this->extra_categories));

        return array_values(array_unique(array_filter(
            array_merge([$this->category], $extra),
            fn ($c) => in_array($c, self::CATEGORIES, true)
        )));
    }

    /** Labels of every category the work belongs to. */
    public function categoryLabels(): array
    {
        return array_map(fn ($c) => __('site.categories.' . $c), $this->allCategories());
    }

    /** Works of a category, counting the extra categories too. */
    public function scopeInCategory(Builder $query, string $category): Builder
    {
        return $query->where(fn ($q) => $q->where('category', $category)
            ->orWhere('extra_categories', $category)
            ->orWhere('extra_categories', 'like', $category . ',%')
            ->orWhere('extra_categories', 'like', '%,' . $category . ',%')
            ->orWhere('extra_categories', 'like', '%,' . $category));
    }

    /** Every category in use by visible works (main or extra), in the usual order. */
    public static function usedCategories(): array
    {
        return collect(self::active()->get(['category', 'extra_categories']))
            ->flatMap(fn ($work) => $work->allCategories())
            ->unique()
            ->sortBy(fn ($c) => array_search($c, self::CATEGORIES))
            ->values()
            ->all();
    }

    public function categoryLabel(): string
    {
        return __('site.categories.' . $this->category);
    }
}
