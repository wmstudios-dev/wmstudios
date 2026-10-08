<?php

namespace App\Models;

use App\Models\Concerns\Localizes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use Localizes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function logoUrl(): ?string
    {
        return \App\Support\ImageUploader::url($this->logo, true);
    }

    public function chatImageUrl(): ?string
    {
        return \App\Support\ImageUploader::url($this->chat_image);
    }

    public function photoUrl(): ?string
    {
        return \App\Support\ImageUploader::url($this->photo, true);
    }
}
