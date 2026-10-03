<?php

namespace App\Http\Controllers;

use App\Models\Thought;

class ThoughtController extends Controller
{
    public function index()
    {
        $thoughts = Thought::published()->latestFirst()->paginate(9);

        return view('thoughts.index', compact('thoughts'));
    }

    public function show(Thought $thought)
    {
        abort_unless(Thought::published()->whereKey($thought->id)->exists(), 404);

        $more = Thought::published()->whereKeyNot($thought->id)->latestFirst()->take(3)->get();

        return view('thoughts.show', compact('thought', 'more'));
    }
}
