<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Faq;
use App\Models\Package;
use App\Models\ProcessStep;
use App\Models\Service;
use App\Models\SpaceItem;
use App\Models\Testimonial;
use App\Models\Thought;
use App\Models\Work;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home()
    {
        return view('home', [
            'works' => Work::active()->where('is_featured', true)->ordered()->take(6)->get(),
            'services' => Service::active()->ordered()->get(),
            'steps' => ProcessStep::active()->ordered()->get(),
            'clients' => Client::active()->ordered()->get(),
            'faqs' => Faq::active()->ordered()->take(6)->get(),
            'testimonials' => Testimonial::active()->ordered()->get(),
            'thoughts' => Thought::published()->latestFirst()->take(3)->get(),
            'space' => SpaceItem::active()->orderBy('sort_order')->orderByDesc('id')->take(6)->get(),
        ]);
    }

    public function services()
    {
        return view('services', [
            'services' => Service::active()->ordered()->get(),
            'packages' => Package::active()->ordered()->get(),
            'faqs' => Faq::active()->ordered()->get(),
        ]);
    }

    public function process()
    {
        return view('process', [
            'steps' => ProcessStep::active()->ordered()->get(),
            'behindTheScenes' => SpaceItem::active()->where('tag', 'bts')->orderBy('sort_order')->orderByDesc('id')->take(8)->get(),
        ]);
    }

    public function space(Request $request)
    {
        $tag = in_array($request->query('tag'), SpaceItem::TAGS, true) ? $request->query('tag') : null;

        $items = SpaceItem::active()
            ->when($tag, fn ($q) => $q->where('tag', $tag))
            ->orderBy('sort_order')->orderByDesc('id')
            ->get();

        $tags = SpaceItem::active()->select('tag')->distinct()->pluck('tag')
            ->sortBy(fn ($t) => array_search($t, SpaceItem::TAGS))->values();

        return view('space', compact('items', 'tags', 'tag'));
    }
}
