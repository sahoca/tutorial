<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('author')->latest()->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.form', [
            'post' => new Post(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;
        $data['featured_image'] = $this->handleImage($request, $data['featured_image'] ?? null);

        $post = Post::create($data);
        $post->categories()->sync($request->input('categories', []));

        return redirect()->route('admin.posts.index')->with('status', 'Yazı oluşturuldu.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.form', [
            'post' => $post,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validated($request, $post);
        $data['featured_image'] = $this->handleImage($request, $post->featured_image);

        $post->update($data);
        $post->categories()->sync($request->input('categories', []));

        return redirect()->route('admin.posts.index')->with('status', 'Yazı güncellendi.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('status', 'Yazı silindi.');
    }

    private function handleImage(Request $request, ?string $current): ?string
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('posts', 'public');
        }

        return $current;
    }

    private function validated(Request $request, ?Post $post = null): array
    {
        $unique = $post ? ',' . $post->id : '';

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug' . $unique],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        return $request->only([
            'title', 'slug', 'excerpt', 'content',
            'meta_title', 'meta_description', 'status',
        ]);
    }
}
