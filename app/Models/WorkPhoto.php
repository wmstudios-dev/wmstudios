<?php

namespace App\Models;

use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Model;

class WorkPhoto extends Model
{
    protected $guarded = ['id'];

    /** What a picture is, in the order a work page lists the groups. Labels shown to visitors live in the site language files. */
    public const KINDS = [
        'feed' => 'Feed post',
        'story' => 'Story',
        'carousel' => 'Carousel',
        'reel' => 'Reels cover',
        'identity' => 'Brand identity',
        'web' => 'Website screenshot',
        'mobile' => 'Website screenshot (phone)',
        'documentation' => 'Documentation (event photos)',
        'other' => 'Other / general',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    /**
     * width and height attributes for the thumbnail, so a masonry layout can reserve each picture's space before it
     * has downloaded (without them, lazy pictures start at zero height and the page jumps or never fills).
     */
    public function sizeAttrs(): string
    {
        static $cache = [];

        if (isset($cache[$this->path])) {
            return $cache[$this->path];
        }

        $thumb = preg_replace('/\.(webp|jpe?g|png)$/i', '', $this->path) . '_thumb.webp';
        $file = \App\Support\StorageSetup::locate($thumb) ?? \App\Support\StorageSetup::locate($this->path);
        $size = $file ? @getimagesize($file) : false;

        return $cache[$this->path] = $size ? 'width="' . $size[0] . '" height="' . $size[1] . '"' : '';
    }

    public function url(bool $thumb = false): ?string
    {
        return ImageUploader::url($this->path, $thumb);
    }
}
