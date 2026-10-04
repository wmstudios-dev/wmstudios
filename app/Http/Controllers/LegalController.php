<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;

class LegalController extends Controller
{
    public function privacy()
    {
        return $this->page('privacy');
    }

    public function terms()
    {
        return $this->page('terms');
    }

    private function page(string $key)
    {
        return view('legal', [
            'page' => __("legal.{$key}"),
            'updated' => Carbon::parse(config('legal.updated'))->locale(app()->getLocale())->translatedFormat('j F Y'),
        ]);
    }
}
