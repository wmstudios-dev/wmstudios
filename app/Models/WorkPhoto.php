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
        'documentation' => 'Documentation (event photos)',
        'other' => 'Other / general',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function url(bool $thumb = false): ?string
    {
        return ImageUploader::url($this->path, $thumb);
    }
}
