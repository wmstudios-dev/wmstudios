<?php

namespace App\Support;

use App\Models\Setting;

class WhatsApp
{
    /** Digits only, Indonesian local numbers (08...) turned into 628... */
    public static function normalize(?string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }

    /** The agency's own WhatsApp chat link (header button, contact page), with an optional prefilled text. */
    public static function chatUrl(?string $text = null): ?string
    {
        $number = self::normalize(Setting::get('whatsapp'));

        if (! $number) {
            return null;
        }

        return 'https://wa.me/' . $number . ($text ? '?text=' . rawurlencode($text) : '');
    }

    /** Link for the admin to answer a visitor on WhatsApp. */
    public static function replyUrl(?string $phone, string $name, string $locale = 'id'): ?string
    {
        $number = self::normalize($phone);

        if (! $number) {
            return null;
        }

        $site = Setting::get('site_name', config('app.name'));
        $text = $locale === 'en'
            ? "Hi {$name}, thanks for contacting {$site}! About your message: "
            : "Halo {$name}, terima kasih sudah menghubungi {$site}! Soal pesanmu: ";

        return 'https://wa.me/' . $number . '?text=' . rawurlencode($text);
    }
}
