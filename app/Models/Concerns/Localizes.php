<?php

namespace App\Models\Concerns;

/**
 * Content is stored per language in columns like title_en / title_id.
 * $model->t('title') returns the current language, falling back to the other one when it is empty.
 */
trait Localizes
{
    public function t(string $field): ?string
    {
        $locale = app()->getLocale() === 'en' ? 'en' : 'id';
        $other = $locale === 'en' ? 'id' : 'en';

        $value = $this->getAttribute("{$field}_{$locale}");

        if ($value === null || trim((string) $value) === '') {
            $value = $this->getAttribute("{$field}_{$other}");
        }

        return $value === null || trim((string) $value) === '' ? null : $value;
    }

    /** t() split into non-empty lines (for bullet lists written one per line in the admin). */
    public function tLines(string $field): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $this->t($field)))));
    }
}
