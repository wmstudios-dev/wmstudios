<?php

namespace App\Support;

/**
 * Everything editable on the admin "Site settings" page. Each field is either:
 *   ['key' => ..., 'label' => ..., 'type' => text|textarea|image|url, 'bilingual' => true|false, 'hint' => ...]
 * Bilingual fields are stored as key_id and key_en.
 */
class SettingsSchema
{
    public static function groups(): array
    {
        return [
            'Brand' => [
                ['key' => 'site_name', 'label' => 'Site name', 'type' => 'text'],
                ['key' => 'logo', 'label' => 'Logo (transparent PNG/WebP works best)', 'type' => 'image'],
                ['key' => 'tagline', 'label' => 'Tagline', 'type' => 'text', 'bilingual' => true],
            ],
            'Home hero' => [
                ['key' => 'hero_title', 'label' => 'Headline', 'type' => 'text', 'bilingual' => true, 'hint' => 'Big text at the top of the home page.'],
                ['key' => 'hero_subtitle', 'label' => 'Sub headline', 'type' => 'textarea', 'bilingual' => true],
                ['key' => 'hero_photo_1', 'label' => 'Hero photo 1', 'type' => 'image'],
                ['key' => 'hero_photo_2', 'label' => 'Hero photo 2', 'type' => 'image'],
                ['key' => 'hero_photo_3', 'label' => 'Hero photo 3', 'type' => 'image'],
            ],
            'Contact' => [
                ['key' => 'whatsapp', 'label' => 'WhatsApp number', 'type' => 'text', 'hint' => 'Digits only with country code, e.g. 6281234567890.'],
                ['key' => 'email', 'label' => 'Public email', 'type' => 'text'],
                ['key' => 'notify_email', 'label' => 'Send new messages to', 'type' => 'text', 'hint' => 'Contact form messages are emailed here. Leave empty to use the public email.'],
                ['key' => 'address', 'label' => 'Address / location', 'type' => 'text', 'bilingual' => true],
            ],
            'Social media' => [
                ['key' => 'instagram', 'label' => 'Instagram URL', 'type' => 'url'],
                ['key' => 'tiktok', 'label' => 'TikTok URL', 'type' => 'url'],
                ['key' => 'youtube', 'label' => 'YouTube URL', 'type' => 'url'],
                ['key' => 'linkedin', 'label' => 'LinkedIn URL', 'type' => 'url'],
                ['key' => 'behance', 'label' => 'Behance URL', 'type' => 'url'],
                ['key' => 'facebook', 'label' => 'Facebook URL', 'type' => 'url'],
            ],
            'Footer' => [
                ['key' => 'footer_note', 'label' => 'Footer note', 'type' => 'textarea', 'bilingual' => true],
            ],
        ];
    }

    /** Flat list of the real storage keys (bilingual fields expand to _id/_en). */
    public static function keys(): array
    {
        $keys = [];

        foreach (self::groups() as $fields) {
            foreach ($fields as $f) {
                if ($f['bilingual'] ?? false) {
                    $keys[] = $f['key'] . '_id';
                    $keys[] = $f['key'] . '_en';
                } else {
                    $keys[] = $f['key'];
                }
            }
        }

        return $keys;
    }

    public static function field(string $storageKey): ?array
    {
        foreach (self::groups() as $fields) {
            foreach ($fields as $f) {
                $base = preg_replace('/_(id|en)$/', '', $storageKey);

                if ($f['key'] === $storageKey || (($f['bilingual'] ?? false) && $f['key'] === $base)) {
                    return $f;
                }
            }
        }

        return null;
    }

    /** Social networks shown as icons in the header/footer, in order. */
    public static function socials(): array
    {
        return ['instagram', 'tiktok', 'youtube', 'linkedin', 'behance', 'facebook'];
    }
}
