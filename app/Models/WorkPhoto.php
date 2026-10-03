<?php

namespace App\Models;

use App\Support\ImageUploader;
use Illuminate\Database\Eloquent\Model;

class WorkPhoto extends Model
{
    protected $guarded = ['id'];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function url(bool $thumb = false): ?string
    {
        return ImageUploader::url($this->path, $thumb);
    }
}
