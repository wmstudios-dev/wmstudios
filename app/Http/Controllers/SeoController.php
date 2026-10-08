<?php

namespace App\Http\Controllers;

use App\Models\Thought;
use App\Models\Work;

class SeoController extends Controller
{
    public function sitemap()
    {
        $urls = collect(['home', 'about', 'works.index', 'services', 'process', 'reviews', 'space', 'thoughts.index', 'contact', 'privacy', 'terms'])
            ->map(fn ($name) => route($name))
            ->merge(\App\Models\Service::active()->get()->map(fn ($s) => route('services.show', $s)))
            ->merge(Work::active()->get()->map(fn ($w) => route('works.show', $w)))
            ->merge(Thought::published()->get()->map(fn ($t) => route('thoughts.show', $t)));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n"
            . $urls->map(fn ($u) => '  <url><loc>' . e($u) . '</loc></url>')->implode("\n")
            . "\n</urlset>\n";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $body = "User-agent: *\nDisallow: /admin\nSitemap: " . route('sitemap') . "\n";

        return response($body, 200, ['Content-Type' => 'text/plain']);
    }
}
