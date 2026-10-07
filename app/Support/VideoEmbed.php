<?php

namespace App\Support;

/**
 * Turns a pasted video link into something the page can show.
 * type: youtube | vimeo | instagram | drive | file | link
 */
class VideoEmbed
{
    public static function parse(?string $url): ?array
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?(?:.*&)?v=|shorts/|embed/|live/))([A-Za-z0-9_-]{11})~', $url, $m)) {
            return [
                'type' => 'youtube',
                'embed' => "https://www.youtube-nocookie.com/embed/{$m[1]}?autoplay=1&rel=0",
                'thumb' => "https://img.youtube.com/vi/{$m[1]}/hqdefault.jpg",
                'vertical' => str_contains($url, '/shorts/'),
                'url' => $url,
            ];
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
            return ['type' => 'vimeo', 'embed' => "https://player.vimeo.com/video/{$m[1]}?autoplay=1", 'thumb' => null, 'vertical' => false, 'url' => $url];
        }

        if (preg_match('~instagram\.com/(reel|p|tv)/([A-Za-z0-9_-]+)~', $url, $m)) {
            return ['type' => 'instagram', 'embed' => "https://www.instagram.com/{$m[1]}/{$m[2]}/embed", 'thumb' => null, 'vertical' => true, 'url' => $url];
        }

        // Google Drive: /file/d/<id>/view, /open?id=<id> or /uc?id=<id>. The file must be shared as "anyone with the link".
        if (preg_match('~(?:drive|docs)\.google\.com/(?:file/d/|(?:open|uc)\?(?:[^#]*&)?id=)([A-Za-z0-9_-]{10,})~', $url, $m)) {
            return ['type' => 'drive', 'embed' => "https://drive.google.com/file/d/{$m[1]}/preview", 'thumb' => null, 'vertical' => false, 'url' => $url];
        }

        if (preg_match('~\.(mp4|webm|mov)(\?.*)?$~i', $url)) {
            return ['type' => 'file', 'embed' => $url, 'thumb' => null, 'vertical' => false, 'url' => $url];
        }

        return ['type' => 'link', 'embed' => $url, 'thumb' => null, 'vertical' => false, 'url' => $url];
    }
}
