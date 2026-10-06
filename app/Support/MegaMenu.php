<?php

namespace App\Support;

use App\Models\ProcessStep;
use App\Models\Service;
use App\Models\SpaceItem;
use App\Models\Thought;
use App\Models\Work;
use Illuminate\Support\Facades\Cache;

/**
 * Content for the panels that open under the header menu items (works, services, process, space, thoughts).
 * Cached for a minute so every page view does not run these queries; the panels may lag an admin edit by that long.
 */
class MegaMenu
{
    public static function data(): array
    {
        try {
            return Cache::remember('mega-menu-data', 60, fn () => self::build());
        } catch (\Throwable $e) {
            // No database yet (first deploy) or cache unavailable: the menu simply shows no extra content.
            return self::empty();
        }
    }

    private static function build(): array
    {
        $works = Work::active()->orderByDesc('is_featured')->ordered()->take(2)->get();

        return [
            'works' => $works,
            'workCount' => Work::active()->count(),
            'categories' => Work::active()->select('category')->distinct()->pluck('category')
                ->sortBy(fn ($c) => array_search($c, Work::CATEGORIES))->values()->all(),
            'services' => Service::active()->ordered()->get(),
            'steps' => ProcessStep::active()->ordered()->take(5)->get(),
            'spaceTags' => SpaceItem::active()->select('tag')->distinct()->pluck('tag')
                ->sortBy(fn ($t) => array_search($t, SpaceItem::TAGS))->values()->all(),
            'spacePhotos' => SpaceItem::active()->orderBy('sort_order')->orderByDesc('id')->take(3)->get(),
            'thoughts' => Thought::published()->latestFirst()->take(3)->get(),
        ];
    }

    private static function empty(): array
    {
        return [
            'works' => collect(), 'workCount' => 0, 'categories' => [],
            'services' => collect(), 'steps' => collect(),
            'spaceTags' => [], 'spacePhotos' => collect(), 'thoughts' => collect(),
        ];
    }
}
