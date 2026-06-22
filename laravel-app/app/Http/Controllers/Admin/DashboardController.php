<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pages' => Page::count(),
            'posts' => Post::count(),
            'media' => Media::count(),
            'messages' => ContactMessage::whereNull('read_at')->count(),
        ];

        $recentMessages = ContactMessage::latest()->take(5)->get();
        $recentPosts = Post::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentMessages', 'recentPosts'));
    }
}
