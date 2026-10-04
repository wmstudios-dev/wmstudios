<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Thought;
use App\Models\Work;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'works' => Work::count(),
            'thoughts' => Thought::count(),
            'new_messages' => ContactMessage::where('status', 'new')->count(),
            'messages' => ContactMessage::count(),
        ];

        $recentMessages = ContactMessage::latest()->take(6)->get();
        $recentWorks = Work::latest()->take(5)->get();

        // Without PHP's GD extension uploaded photos are stored untouched (big and slow), so the dashboard says so.
        $imageOptimization = function_exists('imagewebp');

        return view('admin.dashboard', compact('stats', 'recentMessages', 'recentWorks', 'imageOptimization'));
    }
}
