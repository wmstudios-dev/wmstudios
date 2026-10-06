<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Illuminate\Http\Request;

class WorkController extends Controller
{
    public function index(Request $request)
    {
        $category = in_array($request->query('c'), Work::CATEGORIES, true) ? $request->query('c') : null;

        $works = Work::active()
            ->when($category, fn ($q) => $q->inCategory($category))
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        $categories = collect(Work::usedCategories());

        return view('works.index', compact('works', 'category', 'categories'));
    }

    public function show(Work $work)
    {
        abort_unless($work->is_active, 404);

        $work->load('photos');

        $related = Work::active()
            ->where('category', $work->category)
            ->whereKeyNot($work->id)
            ->ordered()
            ->take(3)
            ->get();

        return view('works.show', compact('work', 'related'));
    }
}
