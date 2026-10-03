<?php

namespace App\Models;

use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value site settings (see App\Support\SettingsSchema for the keys).
 * Read with Setting::get('whatsapp'), or Setting::t('hero_title') for the current language.
 */
class Setting extends Model
{
    protected $guarded = ['id'];

    private const CACHE_KEY = 'site-settings';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    public static function allValues(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());
        } catch (\Throwable $e) {
            // Before the first migration there is no table yet; the site just shows its defaults.
            return [];
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = self::allValues()[$key] ?? null;

        return $value === null || trim($value) === '' ? $default : $value;
    }

    /** Localized value: key_id / key_en, falling back to the other language, then to $default. */
    public static function t(string $key, ?string $default = null): ?string
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'id';
        $other = $locale === 'en' ? 'id' : 'en';

        return self::get("{$key}_{$locale}") ?? self::get("{$key}_{$other}") ?? $default;
    }

    public static function image(string $key, bool $thumb = false): ?string
    {
        return ImageUploader::url(self::get($key), $thumb);
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
