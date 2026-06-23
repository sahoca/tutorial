<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home()
    {
        $posts = Post::published()->latest('published_at')->take(6)->get();
        $page = Page::published()->where('slug', 'anasayfa')->first();

        return view('site.home', compact('posts', 'page'));
    }

    public function blog()
    {
        $posts = Post::published()->latest('published_at')->paginate(9);

        return view('site.blog', compact('posts'));
    }

    public function post(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $related = Post::published()->where('id', '!=', $post->id)->latest('published_at')->take(3)->get();

        return view('site.post', compact('post', 'related'));
    }

    public function page(string $slug)
    {
        $page = Page::published()->where('slug', $slug)->firstOrFail();

        return view('site.page', compact('page'));
    }

    public function assignments(Request $request)
    {
        $query = Assignment::published()->latest('assignment_date');

        if ($authority = $request->query('kurum')) {
            $query->where('authority', $authority);
        }

        $assignments = $query->paginate(15)->withQueryString();
        $authorities = Assignment::AUTHORITIES;

        return view('site.assignments', compact('assignments', 'authorities'));
    }

    public function assignment(string $slug)
    {
        $assignment = Assignment::published()->where('slug', $slug)->firstOrFail();

        return view('site.assignment', compact('assignment'));
    }

    public function contact()
    {
        return view('site.contact');
    }

    public function contactSubmit(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        ContactMessage::create($data);

        return back()->with('status', 'Mesajınız iletildi. En kısa sürede dönüş yapılacaktır.');
    }
}
